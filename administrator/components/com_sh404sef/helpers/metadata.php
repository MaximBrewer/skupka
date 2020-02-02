<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      sh404SEF
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      4.7.3.3292
 * @date        2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
defined('_JEXEC') or die();

class Sh404sefHelperMetadata
{
	public static function shouldInsertMeta($input = null, $categories = array())
	{
		$input = empty($input) ? JFactory::getApplication()->input : $input;
		$shouldInsertMeta = false;

		// get request details
		$component = $input->getCmd('option');
		$view = $input->getCmd('view');
		$printing = $input->getInt('print');

		// we are set to only display on canonical page for an item
		// this can only be true if context and current request matches
		if (empty($component) && empty($view))
		{
			return $shouldInsertMeta;
		}

		switch ($component)
		{
			case 'com_content':
				// only display if on an article page
				$shouldInsertMeta = ($view == 'article' || $view == 'featured') && empty($printing);
				// check category
				if ($shouldInsertMeta)
				{
					if (!empty($categories) && ($categories[0] != 'show_on_all'))
					{
						// find about article category
						$catid = $input->getInt('catid', 0);
						if (empty($catid))
						{
							$id = $input->getInt('id', 0);
							if ($id)
							{
								$article = JTable::getInstance('content');
								$article->load($id);
								$catid = $article->catid;
							}
						}
						if (!empty($catid))
						{
							$shouldInsertMeta = in_array($catid, $categories);
						}
					}
					else
					{
						$shouldInsertMeta = true;
					}
				}
				break;
			case 'com_k2':
				$shouldInsertMeta = $view == 'item';
				break;
			default:
				$shouldInsertMeta = true;
				break;
		}

		return $shouldInsertMeta;
	}

	public static function getCustomMetaDataFromDb($nonSef = '')
	{
		static $_tags = array();
		static $_model = null;

		$nonSef = empty($nonSef) ? Sh404sefHelperUrl::getCurrentNonSef() : $nonSef;
		if (!isset($_tags[$nonSef]))
		{
			$_model = is_null($_model) ? new Sh404sefModelMetas() : $_model;
			$isHome = Sh404sefHelperUrl::isNonSefHomepage($nonSef);
			$_tags[$nonSef] = $_model->getCustomMetaData($isHome ? sh404SEF_HOMEPAGE_CODE : $nonSef);
		}

		return $_tags[$nonSef];
	}

	/**
	 * Compute and returns meta data, including canonical,
	 * in their "raw" form
	 *
	 * @param string $nonSef
	 * @return mixed
	 * @see Sh404sefHelperMetadata::getFinalizedCustomMetaData
	 *
	 */
	public static function getCustomMetaData($nonSef = '')
	{
		static $_tags = array();

		$nonSef = empty($nonSef) ? Sh404sefHelperUrl::getCurrentNonSef() : $nonSef;
		if (!isset($_tags[$nonSef]))
		{
			$pageInfo = Sh404sefFactory::getPageInfo();

			// get raw custom meta data
			$_tags[$nonSef] = self::getCustomMetaDataFromDb($nonSef);

			// various fixes
			global $shCustomTitleTag, $shCustomDescriptionTag, $shCustomKeywordsTag, $shCustomRobotsTag, $shCustomLangTag, $shCanonicalTag;

			// J! 2.5 finder canonical handling/hack
			$highlight = Sh404sefHelperUrl::getUrlVar($nonSef, 'highlight', null);
			if (!empty($highlight) && empty($shCanonicalTag))
			{
				$searchCanoNonSef = str_replace('?highlight=' . $highlight, '', $nonSef);
				$searchCanoNonSef = str_replace('&highlight=' . $highlight, '', $searchCanoNonSef);
				$shCanonicalTag = JRoute::_($searchCanoNonSef);
				$pageInfo->pageCanonicalUrl = Sh404sefHelperUrl::routedToAbs($shCanonicalTag);
			}

			// splash page hack
			$splash = Sh404sefHelperUrl::getUrlVar($nonSef, 'sh404sef_splash', null);
			if (!empty($splash) && empty($shCanonicalTag))
			{
				$shCanonicalTag = Sh404sefFactory::getConfig()->shForcedHomePage;
				$pageInfo->pageCanonicalUrl = Sh404sefHelperUrl::routedToAbs($shCanonicalTag);
			}

			// @deprecated Some globals can be set by extensions. Will be really deprecated when a replacement is in place
			$_tags[$nonSef]->metatitle = empty($_tags[$nonSef]->metatitle) ? $shCustomTitleTag : $_tags[$nonSef]->metatitle;
			$_tags[$nonSef]->metadesc = empty($_tags[$nonSef]->metadesc) ? $shCustomDescriptionTag : $_tags[$nonSef]->metadesc;
			$_tags[$nonSef]->metakey = empty($_tags[$nonSef]->metakey) ? $shCustomKeywordsTag : $_tags[$nonSef]->metakey;
			$_tags[$nonSef]->metarobots = empty($_tags[$nonSef]->metarobots) ? $shCustomRobotsTag : $_tags[$nonSef]->metarobots;
			$_tags[$nonSef]->metalang = empty($_tags[$nonSef]->metalang) ? $shCustomLangTag : $_tags[$nonSef]->metalang;
			$_tags[$nonSef]->canonical = empty($_tags[$nonSef]->canonical) ? $shCanonicalTag : $_tags[$nonSef]->canonical;
		}

		return $_tags[$nonSef];
	}

	/**
	 * Compute and return metadata, in their final usable form, ready to insert in page
	 * ie: tags or quotes removed for instance
	 *
	 * @param object $rawMetaData
	 * @param boolean $isHome
	 * @return mixed|null|void
	 */
	public static function getFinalizedCustomMetaData($rawMetaData = null, $isHome = null)
	{
		$rawMetaData = empty($rawMetaData) ? self::getCustomMetaData() : $rawMetaData;
		if (empty($rawMetaData))
		{
			return;
		}

		$config = Sh404sefFactory::getConfig();
		$isHome = is_null($isHome) ? Sh404sefHelperUrl::isNonSefHomepage() : $isHome;
		$metaData = clone($rawMetaData);

		// page title
		if (!empty($rawMetaData->metatitle))
		{
			$prepend = $isHome ? '' : $config->prependToPageTitle;
			$append = $isHome ? '' : $config->appendToPageTitle;
			$metaData->pageTitle = self::cleanUpTitle($prepend . $rawMetaData->metatitle . $append);
		}

		// meta description
		if (!empty($rawMetaData->metadesc))
		{
			$t = htmlspecialchars(self::cleanUpDesc($rawMetaData->metadesc), ENT_COMPAT, 'UTF-8');
			$metaData->pageDescription = ShlSystem_Strings::pr('#\$([0-9]*)#u', '\\\$${1}', $t);
		}

		if (!empty($rawMetaData->metakey))
		{
			$t = htmlspecialchars(self::cleanUpDesc($rawMetaData->metakey), ENT_COMPAT, 'UTF-8');
			$metaData->pageKeywords = ShlSystem_Strings::pr('#\$([0-9]*)#u', '\\\$${1}', $t);
		}

		if (!empty($rawMetaData->metarobots))
		{
			$metaData->pageRobotsTag = $rawMetaData->metarobots;
		}

		if (!empty($rawMetaData->metalang))
		{
			$metaData->pageLangTag = $rawMetaData->metalang;
		}

		return $metaData;
	}

	/**
	 * Insert metadata into document
	 * Only "common" metadata is handled here.
	 * Canonical and other that may needs special processing
	 * are deferred to onAfterRender
	 *
	 * @param null $metaData
	 */
	public static function insertMetaData($metaData = null)
	{
		$metaData = empty($metaData) ? self::getFinalizedCustomMetaData() : $metaData;
		$pageInfo = Sh404sefFactory::getPageInfo();
		$config = Sh404sefFactory::getConfig();
		$document = JFactory::getDocument();

		// page title
		if (empty($metaData->pageTitle) && !Sh404sefHelperUrl::isNonSefHomepage())
		{
			// we don't have a custom page title, but we may have to append/prepend
			// a custom string
			$metaData->pageTitle = self::cleanUpTitle(
				$config->prependToPageTitle
				. $document->getTitle()
				. $config->appendToPageTitle
			);
		}
		if (!empty($metaData->pageTitle))
		{
			$pageInfo->pageTitle = $metaData->pageTitle;
			$pageInfo->pageTitlePr = self::protectPageTitle($pageInfo->pageTitle);
			$document->setTitle($pageInfo->pageTitle);
		}

		// meta description
		if (!empty($metaData->pageDescription))
		{
			$pageInfo->pageDescription = $metaData->pageDescription;
			$document->setDescription($pageInfo->pageDescription);
		}

		if (!empty($metaData->pageKeywords))
		{
			$pageInfo->pageKeywords = $metaData->pageKeywords;
			$document->setMetaData('keywords', $pageInfo->pageKeywords);
		}

		if (!empty($metaData->pageRobotsTag))
		{
			$pageInfo->pageRobotsTag = $metaData->pageRobotsTag;
			$document->setMetaData('robots', $pageInfo->pageRobotsTag);
		}

		if (!empty($metaData->pageLangTag))
		{
			$pageInfo->pageLangTag = $metaData->pageLangTag;
			$document->setMetaData('Content-Language', $pageInfo->pageLangTag, true);
		}

		// referrer policy
		if ($config->referrerPolicyMeta != 'none')
		{
			$document->setMetaData('referrer', $config->referrerPolicyMeta);
		}
	}

	public static function cleanUpTitle($title)
	{
		$title = JString::trim(html_entity_decode($title, ENT_COMPAT, 'UTF-8'));
		if (!empty($title) && substr($title, 0, 1) == '"' && substr($title, -1) == '"')
		{
			$title = JString::substr($title, 1);
			$title = JString::substr($title, 0, -1);
		}
		return $title;
	}

	public static function protectPageTitle($title)
	{
		// in case there are some $nn in the title
		$title = ShlSystem_Strings::pr('#\$([0-9]*)#u', '\\\$${1}', $title);
		return $title;
	}

	public static function cleanUpDesc($desc)
	{
		$desc = stripslashes(html_entity_decode(strip_tags($desc, '<br><br /><p></p>'), ENT_NOQUOTES, 'UTF-8'));
		$desc = str_replace('<br>', ' - ', $desc); // otherwise, one word<br >another becomes onewordanother
		$desc = str_replace('<br />', ' - ', $desc);
		$desc = str_replace('<p>', ' - ', $desc);
		$desc = str_replace('</p>', ' - ', $desc);
		while (strpos($desc, ' -  - ') !== false)
		{
			$desc = str_replace(' -  - ', ' - ', $desc);
		}
		$desc = str_replace("&#39;", '\'', $desc);
		$desc = str_replace("&#039;", '\'', $desc);
		$desc = str_replace('"', '\'', $desc);
		$desc = str_replace("\r\n", ' ', $desc);
		$desc = str_replace("\n\r", ' ', $desc);
		$desc = str_replace("\r", ' ', $desc);
		$desc = str_replace("\n", ' ', $desc);
		return JString::substr(JString::trim($desc), 0, 512);
	}

	public static function includeMetaPlugin()
	{
		$option = JFactory::getApplication()->input->getCmd('option');

		// get extension plugin
		$extPlugin = Sh404sefFactory::getExtensionPlugin($option);

		// which plugin file are we supposed to use?
		$extPluginPath = $extPlugin->getMetaPluginPath(Sh404sefFactory::getPageInfo()->currentNonSefUrl);

		if (!empty($extPluginPath))
		{
			include $extPluginPath;
		}
	}

	public static function getMenuItemTitle($Itemid = 0)
	{
		// get the current menu item, or possibly the one asked for
		$menus = JFactory::getApplication()->getMenu();
		$menuItem = empty($Itemid) ? $menus->getActive() : $menus->getItem($Itemid);

		// get value, if any set
		$title = is_object($menuItem) ? $menuItem->params->get('page_title') : '';

		// return whatever we found
		return $title;
	}
}
