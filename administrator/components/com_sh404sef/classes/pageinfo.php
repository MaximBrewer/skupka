<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date		2016-03-15
 */

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die;

/**
 * Maintain data and handle requests about current
 * page. Accessed through factory:
 *
 * $liveSite = Sh404sefFactory::getPageInfo()->getDefaultFrontLiveSite();
 *
 *
 * @author yannick
 *
 */
class Sh404sefClassPageinfo
{
	const LIVE_SITE_SECURE_IGNORE = 0;
	const LIVE_SITE_SECURE_YES = 1;
	const LIVE_SITE_SECURE_NO = -1;

	const LIVE_SITE_NOT_MOBILE = 0;
	const LIVE_SITE_MOBILE = 1;

	public $shURL = '';
	public $currentNonSefUrl = '';
	public $currentSefUrl = '';
	public $originalUri = '';
	public $baseUrl = '';
	public $currentLanguageTag = '';
	public $currentLanguageShortTag = '';
	public $allLangHomeLink = '';
	public $homeLink = '';
	public $homeLinks = array();
	public $homeItemid = 0;
	public $isMobileRequest = self::LIVE_SITE_NOT_MOBILE;
	public $httpStatus = null;
	public $isMultilingual = null;
	public $pageCanonicalUrl = '';
	public $joomlaSuffixSetting = 0;
	public $pageTitle = '';
	public $pageTitlePr = '';

	// pagination management
	public $paginationPrevLink = '';
	public $paginationNextLink = '';

	// store our router instance
	public $router = null;

	// this will be filled up upon startup by system plugin
	// code with the current detected base site url for the request
	// ie: it can be secure, unsecure, for one language or another
	protected $_defaultLiveSite = '';

	public function setDefaultLiveSite($url)
	{
		$this->_defaultLiveSite = $url;
	}

	public function getDefaultLiveSite()
	{
		return $this->_defaultLiveSite;
	}

	public function getDefaultFrontLiveSite()
	{
		return str_replace('/administrator', '', $this->_defaultLiveSite);
	}

	public function setCurrentLanguage($languageTag)
	{
		$this->currentLanguageTag = $languageTag;
		$this->currentLanguageShortTag = Sh404sefHelperLanguage::getUrlCodeFromTag($languageTag);
	}

	public function init()
	{
		static $_initialized = false;

		if (!$_initialized)
		{
			$this->joomlaSuffixSetting = JFactory::$config->get('sef_suffix');
			$this->originalUri = $this->getURI();
			$uri = JURI::getInstance();
			$this->currentSefUrl = $uri->toString();
			$site = $uri->toString(array('scheme', 'host', 'port'));
			$this->basePath = JString::rtrim(str_replace($site, '', $uri->base()), '/');
			$this->detectMultilingual();
			$this->setCurrentLanguage(Sh404sefHelperLanguage::getDefaultLanguageTag());
			$this->_defaultLiveSite = JString::rtrim($uri->base(), '/');
			$this->loadHomepages();
			$_initialized = true;
		}
	}

	public function detectMultilingual()
	{
		$app = JFactory::getApplication();
		if ($app->isSite())
		{
			$this->isMultilingual = $app->getLanguageFilter() ? 'joomla' : false;
		}
	}

	public function loadHomepages()
	{
		$app = JFactory::getApplication();
		if ($app->isAdmin())
		{
			return;
		}

		// store default links in each language
		jimport('joomla.language.helper');
		$defaultLanguage = Sh404sefHelperLanguage::getDefaultLanguageTag();
		$languages = Sh404sefHelperLanguage::getActiveLanguages();
		if ($this->isMultilingual === false || $this->isMultilingual == 'joomla')
		{
			$menu = JFactory::getApplication()->getMenu();
			foreach ($languages as $language)
			{
				$menuItem = $menu->getDefault($language->lang_code);
				if (!empty($menuItem))
				{
					$this->homeLinks[$language->lang_code] = $this->_prepareLink($menuItem);
					if ($language->lang_code == $defaultLanguage)
					{
						$this->homeLink = $this->homeLinks[$language->lang_code];
						$this->homeItemid = $menuItem->id;
					}
				}
			}

			// find about the "All" languages home link
			$menuItem = $menu->getDefault('*');
			if (!empty($menuItem))
			{
				$this->allLangHomeLink = $this->_prepareLink($menuItem);
			}
		}
		else
		{
			// trouble starts
			$db = ShlDbHelper::getDb();
			$query = $db->getQuery(true);
			$query->select('id,language,link');
			$query->from('#__menu');
			$query->where('home <> 0');
			try
			{
				$db->setQuery($query);
				$items = $db->loadObjectList('language');
			}
			catch (Exception $e)
			{
				ShlSystem_Log::error('sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
			}
			if (!empty($items))
			{
				if (count($items) == 1)
				{
					$tmp = array_values($items);
					$defaultItem = $tmp[0];
				}
				if (empty($defaultItem))
				{
					$defaultItem = empty($items[$defaultLanguage]) ? null : $items[$defaultLanguage];
				}
				if (empty($defaultItem))
				{
					$defaultItem = empty($items['*']) ? null : $items['*'];
				}
				foreach ($languages as $language)
				{
					if (!empty($items[$language->lang_code]))
					{
						$this->homeLinks[$language->lang_code] = $this->_prepareLink($items[$language->lang_code]);
					}
					else
					{
						// no menu item for home link
						// let's try to  build one
						$this->homeLinks[$language->lang_code] = $this
							->_prepareLink($defaultItem, Sh404sefHelperLanguage::getUrlCodeFromTag($language->lang_code));
					}

					if ($language->lang_code == $defaultLanguage)
					{
						$this->homeLink = $this->homeLinks[$language->lang_code];
						$this->homeItemid = $defaultItem->id;
						$this->allLangHomeLink = shCleanUpLang($this->homeLinks[$language->lang_code]);
					}

				}
			}
		}

		ShlSystem_Log::debug('sh404sef', 'HomeLinks = %s', print_r($this->homeLinks, true));
	}

	protected function _prepareLink($menuItem, $forceLanguage = null)
	{
		$link = Sh404sefHelperUrl::setUrlVar($menuItem->link, 'Itemid', $menuItem->id);
		$linkLang = Sh404sefHelperUrl::getUrlLang($link);
		if (empty($linkLang))
		{
			// if no language in link, use current, except if 'All', in which case use actual default
			if (empty($forceLanguage))
			{
				$itemLanguage = $menuItem->language == '*' ? Sh404sefHelperLanguage::getDefaultLanguageSef()
					: Sh404sefHelperLanguage::getUrlCodeFromTag($menuItem->language);
			}
			else
			{
				$itemLanguage = $forceLanguage;
			}
			$link = Sh404sefHelperUrl::setUrlVar($link, 'lang', $itemLanguage);
		}

		return $link;
	}

	protected function getURI()
	{
		// copied from Joomla, as JURI keeps the original URI
		// as a protected var, we can't access it
		// Determine if the request was over SSL (HTTPS).
		if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off'))
		{
			$https = 's://';
		}
		else
		{
			$https = '://';
		}

		// Since we are assigning the URI from the server variables, we first need
		// to determine if we are running on apache or IIS.  If PHP_SELF and REQUEST_URI
		// are present, we will assume we are running on apache.

		if (!empty($_SERVER['PHP_SELF']) && !empty($_SERVER['REQUEST_URI']))
		{
			// To build the entire URI we need to prepend the protocol, and the http host
			// to the URI string.
			$theURI = 'http' . $https . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}
		else
		{
			// Since we do not have REQUEST_URI to work with, we will assume we are
			// running on IIS and will therefore need to work some magic with the SCRIPT_NAME and
			// QUERY_STRING environment variables.

			// IIS uses the SCRIPT_NAME variable instead of a REQUEST_URI variable... thanks, MS
			$theURI = 'http' . $https . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];

			// If the query string exists append it to the URI string
			if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']))
			{
				$theURI .= '?' . $_SERVER['QUERY_STRING'];
			}
		}
		return $theURI;
	}
}
