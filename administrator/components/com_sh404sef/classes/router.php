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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
jimport('joomla.utilities.string');

/**
 * Class to create and parse routes for the site application
 */
class Sh404sefClassRouterInternal extends JRouterSite
{
	const ROUTER_MODE_SH404SEF = 999;

	// flag, to make sure we know when we are parsing the request
	// parsing can be called upon several times by Joomla
	// however some operations associated with parsing (mostly
	// automated redirects) should be done only once
	public static $requestParsed = false;

	public static $parsedWithJoomlaRouter = false;

	public $joomlaErrorHandler = null;

	protected $_shId             = 'sh404SEF for Joomla 1.6';
	protected $_originalBuildUri = null;

	protected $_guessedLanguageCode = '';

	/**
	 * Perform startup operations such as detecting environment
	 * and checking automated redirections
	 */
	public function startup(&$uri)
	{
		// check some SEO related redirects
		$this->_checkSeoRedirects($uri);

		// check www vs non-www and other domain related stuff
		// may redirect to another url
		$this->_checkDomain($uri);

		// load common language strings
		$language = JFactory::getLanguage();
		$language->load('com_sh404sef.sys', JPATH_ADMINISTRATOR);
		if (JFactory::getApplication()->isSite())
		{
			$language->load('com_sh404sef');
		}

		// hookup to Joomla error page handling
		$this->_setupNotFoundErrorHandling();
	}

	protected function _checkSeoRedirects($uri)
	{
		// facebook: it may happen that FB cause an URL that has been liked or otherise shared
		// to be linked to and thus indexed with an added query parameter. This will cause
		// duplicate content issue, so if it happens, we want to 301 redirect to the same URL
		// without that parameter
		$fb_xd_bust = isset($_GET['fb_xd_bust']);
		$fb_xd_fragment = isset($_GET['fb_xd_fragment']);
		if (!empty($fb_xd_bust) || !empty($fb_xd_fragment))
		{

			// sanity checks, like don't redirect if there is some POST data
			if (!$this->_canRedirectFrom($uri))
			{
				return;
			}

			// need to redirect, let's kill the faulty params
			$uri->delVar('fb_xd_bust');
			$uri->delVar('fb_xd_fragment');

			// finally redirect
			$target = $uri->toString();
			ShlSystem_Log::debug('sh404sef', 'Performing seo redirect to:' . $target);
			shRedirect($target);
		}
	}

	/**
	 * Check various conditions on a request to
	 * decide whether it is safe to allow a redirection
	 * to another page
	 * Does NOT check configuration settings, only look
	 * at the passed uri and method parameters
	 *
	 * @param        JURI    object $uri object describing the current request, from which we want to redirect
	 * @param string $method current request method
	 */
	protected function _canRedirectFrom($uri, $method = '', $url = '')
	{
		// use framework if no method passed
		if (empty($method))
		{
			$method = JRequest::getMethod();
		}

		// get the requested url
		$url = empty($url) ? $this->getFullUrl($uri) : $url;

		// start with hope
		$canRedirect = !self::$requestParsed;
		$canRedirect = $canRedirect && !empty($url);
		$canRedirect = $canRedirect && strpos($url, 'index2.php') === false;
		$canRedirect = $canRedirect && strpos($url, 'tmpl=component') === false;
		$canRedirect = $canRedirect && strpos($url, 'no_html=1') === false;
		$canRedirect = $canRedirect && strpos($url, 'sh404sef_splash=1') === false;
		$canRedirect = $canRedirect
			&& (empty($_SERVER['HTTP_X_REQUESTED_WITH'])
				|| (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
					&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest'));
		$canRedirect = $canRedirect && empty($_POST);
		$canRedirect = $canRedirect && $method != 'POST';

		return $canRedirect;
	}

	/**
	 * Put back the base path that was removed by Joomla router prior to sending
	 * uri to parserule
	 *
	 * @param JURI $uri
	 */
	protected function getFullUrl($uri)
	{
		$clone = clone $uri;
		$basePath = $clone->base(true);
		if (!empty($basePath))
		{
			$currentPath = $clone->getPath();
			$clone->setPath($basePath . '/' . $currentPath);
		}

		$fullUrl = $clone->toString();

		return $fullUrl;
	}

	/**
	 *
	 * Performs some checks, and possibly some redirects based
	 * on the current request domain
	 *
	 * Typically, ensure access through either www or non-www version of domain
	 *
	 * In the future, will also allow for language determination based on
	 * domain or sub domain
	 *
	 * @param JURI object $uri
	 */
	protected function _checkDomain($originalUri)
	{
		// break reference
		$uri = clone $originalUri;

		// get configuration
		$sefConfig = Sh404sefFactory::getConfig();
		$pageInfo = &Sh404sefFactory::getPageInfo();

		// get request infos:
		$rootUrl = $pageInfo->getDefaultFrontLiveSite();
		$host = $uri->getHost();
		$canRedirect = $this->_canRedirectFrom($uri) && !empty($host) && $host != 'localhost' && $host != 'web.local';
		$targetUrl = '';

		// first check if there are some settings for per language domain, and apply them
		// search for live site in our list of per language root url
		foreach ($sefConfig->liveSites as $language => $langRootUrl)
		{
			if (!empty($langRootUrl) && $langRootUrl == $rootUrl)
			{

				// TODO: switch to that language

				// stop
				break;
			}
		}

		// if we have not already
		if (empty($targetUrl) && $canRedirect && $sefConfig->shAutoRedirectWww != shSEFConfig::DONT_ENFORCE_WWW)
		{
			if (substr($host, 0, 4) != 'www.' && $sefConfig->shAutoRedirectWww == shSEFConfig::ENFORCE_WWW)
			{
				ShlSystem_Log::debug('sh404sef', 'Redirecting from non www to wwww');
				$uri->setHost('www.' . $host);
				$targetUrl = $uri->toString();
			}
			if (substr($host, 0, 4) == 'www.' && $sefConfig->shAutoRedirectWww == shSEFConfig::ENFORCE_NO_WWW)
			{
				ShlSystem_Log::debug('sh404sef', 'Redirecting from www to non wwww');
				$uri->setHost(str_replace('www.', '', $host));
				$targetUrl = $uri->toString();
			}
		}

		// Redirect if needed
		if (!empty($targetUrl))
		{
			shRedirect($targetUrl);
		}
	}

	/**
	 * Optionnally overrides Joomla 404 error page
	 *
	 * const ERROR_404_HANDLE = 0;
	 * const ERROR_404_OVERRIDE_JOOMLA = 1;
	 * const ERROR_404_USE_JOOMLA = 2;
	 */
	protected function _setupNotFoundErrorHandling()
	{
		// we store current handler, so as to pass-thru to it if needed
		$this->joomlaErrorHandler = JError::getErrorHandling(E_ERROR);

		// then override Joomla! handler
		JError::setErrorHandling(E_ERROR, 'callback', array($this, 'sh404sefErrorPage'));
		set_exception_handler(array($this, 'sh404sefExceptionHandler'));
	}

	/**
	 * Circumvent the language filter to allow checking for aliases and shurls
	 *
	 * Only executed after J 3.4 (needs "processing stages in router")
	 *
	 * @param $router
	 * @param $uri
	 */
	public function preprocessParseRule(&$router, &$uri)
	{
		$originalUrl = Sh404sefHelperUrl::getOriginalUrlFromUri($uri);
		$originalPath = trim(Sh404sefHelperUrl::getOriginalPathFromUri($uri, JUri::base()), '/');
		$originalUri = new JUri($originalUrl);
		$originalUri->setPath($originalPath);
		$this->_checkAliases($uri);
		$this->_checkShurls($uri);
	}

	/**
	 * Method attached to J! main router object
	 * and processed as a parseRule
	 *
	 * @param object J! router object reference
	 * @param object uri object provided by Joomla. Will represent request upon
	 *               first call of this method, but any uri upon subsequent calls
	 *
	 * @return array $vars list of query variables as decoded by us, if any
	 */
	public function parseRule(&$jRouter, &$uri)
	{
		static $_vars = array();

		// rebuild an URI object, bypassing any change made by the language filter plugin
		$originalUrl = Sh404sefHelperUrl::getOriginalUrlFromUri($uri);
		$originalPath = trim(Sh404sefHelperUrl::getOriginalPathFromUri($uri, JUri::base()), '/');

		// by pass routing when extensions incorrectly POST data
		// (usually with ajax) to a relative URL
		if ($this->_relativeUrlBypass($originalPath))
		{
			return array();
		}

		$originalUri = new JUri($originalUrl);
		$originalUri->setPath($originalPath);

		// check if url is accessed thru /index.php/ while site is
		// set up to use url rewriting
		$this->_checkAccessThruIndexphp($originalUri);

		// check if user has set any alias for this - possibly non-sef - url
		if (version_compare(JVERSION, '3.4', '<'))
		{
			// only do this on J < 3.4
			// if > 3.4, we do this check at the PROCESS_BEFORE stage of routing
			// Only way to have proper aliases and shURLs when language filter is on
			$this->_checkAliases($originalUri);
		}

		// check SEO redirects, such as index.php on home page
		$this->_checkHomepageRedirects($originalUri);

		// calculate signature of uri, avoiding multiple parsing of same uri
		$signature = md5($originalUrl);

		// don't parse twice, this would break J! 1.6 Router mode handling
		if (isset($_vars[$signature]))
		{
			if (!self::$parsedWithJoomlaRouter)
			{
				$uri->setPath('');
				$uri->setQuery($_vars[$signature]);
			}

			return $_vars[$signature];
		}

		// debug info
		ShlSystem_Log::debug('sh404sef', 'Starting to parse %s', print_r($uri, true));

		// fix the path before checking url. Joomla unfortunately remove
		// the trailing slash from the path, thus we're not working on the actual requested URL
		if (!self::$requestParsed)
		{
			$path = $uri->getPath();
			if (!empty($path))
			{
				if ($this->_hasTrailingSlash() & JString::substr($path, -1) != '/')
				{
					// if trailing slash on original request, but not on path
					// put it back
					$uri->setPath($path . '/');
				}
			}

			/*
			 * 1 - We don't know what the language filter may or may not have done
			 * with the path. It may already have returned a lang var to the router
			 * but we don't know that either
			 *
			 * Actually we do: if a language was detected, $app->input->get('language')
			 * will have the language code (ie en-GB)
			 * If that is set, the path has been truncated, ie the leading en/ code
			 * has been removed (and no query var set to compensate!)
			 *
			 * 2 - What we can do: compare the original url with the path
			 * in $uri. If we do have a language in the original but not
			 * in the incoming $uri, then language filter already has done lang
			 * detection and removed that from the path (including when there's no
			 * language code at all: that's default language)
			 * Then we can:
			 * - figure out what the language is and stick it back in $uri
			 * - parse through sh404sef
			 * - remove the leading lang code we put in $uri
			 * - unset any 'lang' we may have calculated ourselves so as
			 * not to override what the languge filter has done
			 * EDIT: to still be able to switch back to default language (when no lang code is used
			 * on default language, we must use trick (ie set a cookie)
			 *
			 */
			$appLang = JFactory::getApplication()->input->get('language', '');
			$sefLangCode = '';
			if (!empty($appLang))
			{
				$languages = JLanguageHelper::getLanguages('lang_code');
				$sefLangCode = empty($languages[$appLang]) ? '' : $languages[$appLang]->sef;
			}

			$currentPath = '';
			if (!empty($sefLangCode) && $originalPath != 'index.php'
				&& (
					$appLang != Sh404sefHelperLanguage::getDefaultLanguageTag()
					||
					(
						$appLang == Sh404sefHelperLanguage::getDefaultLanguageTag()
						&& Sh404sefHelperLanguage::getInsertLangCodeInDefaultLanguage()
						&& (!empty($originalPath))
					)
				)
			)
			{
				// stick back the path in the url
				$currentPath = $uri->getPath();
				$uri->setPath($sefLangCode . '/' . (empty($currentPath) ? '' : $currentPath));
				$this->_guessedLanguageCode = $sefLangCode;
			}

			// do the parsing
			$_vars[$signature] = $this->_parseSefRoute($uri);

			if (!empty($_vars[$signature]))
			{
				// merge our decoded vars with those that could be passed as query string
				$_vars[$signature] = array_merge($_vars[$signature], $uri->getQuery($asArray = true));
				if (!self::$parsedWithJoomlaRouter)
				{
					$uri->setQuery($_vars[$signature]);
				}
				else
				{
					// $uri is not set when Joomla! router is used to parse, so
					// build reference non-sef url otherwise
					$query = urldecode(http_build_query($_vars[$signature], '', '&'));
					$query = empty($query) ? '' : '?' . $query;
					$nonSefUrl = 'index.php' . $query . $uri->toString(
							array(
								'fragment'
							)
						);
				}

				// kill the path, so that J! router don't try to keep parsing
				// the sef url
				if (!self::$parsedWithJoomlaRouter)
				{
					$uri->setPath('');

					// when J! will try parse this as RAW route, for some reasons it
					// tries to get the Itemid from the request, so we have to fake having one
					if (!empty($_vars[$signature]['Itemid']))
					{
						JRequest::setVar('Itemid', (int) $_vars[$signature]['Itemid']);
					}

					// unset the Itemid var if it's empty: some extensions (or user-created custom SEF URLs)
					// have "&Itemid=&id=..., which currently caused the Itemid to be set to nothing
					// instead of Joomla default behavior
					if (empty($_vars[$signature]['Itemid']) && isset($_vars[$signature]['Itemid']))
					{
						unset($_vars[$signature]['Itemid']);
					}

					// big bad hack for Joomla 3.x
					$this->_mailtoHack($_vars[$signature]);
				}

				// now store the decoded current non-sef url
				$nonSefUrl = empty($nonSefUrl)
					? 'index.php' . $uri->toString(
						array(
							'query',
							'fragment'
						)
					) : $nonSefUrl;

				Sh404sefFactory::getPageInfo()->currentNonSefUrl = Sh404sefHelperUrl::stripTrackingVarsFromNonSef($nonSefUrl);
			}
			else if (!empty($appLang))
			{
				// revert the changes we made to $uri
				$uri->setPath($currentPath);
			}
		}

		// mark request as parsed, so that we don't try to do
		// redirects and such if the router parse method is
		// called several times
		self::$requestParsed = true;

		// a few per-extension hacks
		$this->_extensionsParseHacks($jRouter, $uri, $_vars[$signature]);

		// store page language information
		if (!empty($appLang))
		{
			Sh404sefFactory::getPageInfo()->setCurrentLanguage($appLang);
		}

		// Send that back to J! router to put everything together
		if (self::$parsedWithJoomlaRouter)
		{
			return array();
		}
		else
		{
			return empty($_vars[$signature]) ? array() : $_vars[$signature];
		}
	}

	/**
	 * Detect POSTing of data to index.php and accept
	 * it even if the URL is invalid (a 404).
	 *
	 * Prevents errors when extensions javascript send
	 * ajax requests to relative URLs.
	 *
	 * @param string $originalPath
	 * @return bool
	 */
	private function _relativeUrlBypass($originalPath)
	{
		$method = JFactory::getApplication()->input->getMethod();
		if ($method != 'POST')
		{
			return false;
		}
		$bits = explode('/', $originalPath);
		if (empty($bits))
		{
			return false;
		}
		$lastBit = array_pop($bits);
		if ($lastBit != 'index.php')
		{
			return false;
		}
		return true;
	}

	/**
	 * Detects if requests access an existing page using /index.php/
	 * while url rewriting is activated, and redirect if so
	 *
	 * @param JURI $uri
	 */
	protected function _checkAccessThruIndexphp($uri)
	{
		if (self::$requestParsed || !empty(Sh404sefFactory::getConfig()->shRewriteMode))
		{
			return;
		}

		// is the url being accessed with index.php?
		$pageInfo = Sh404sefFactory::getPageInfo();
		$originalUrl = Sh404sefHelperUrl::getOriginalUrlFromUri($uri);
		$originalPathAndQuery = substr_replace($originalUrl, '', 0, strlen(JUri::base()));
		if (strpos($originalPathAndQuery, 'index.php/') === 0)
		{
			if (!$this->_canRedirectFrom($uri))
			{
				return;
			}
			$targetSefUrl = str_replace('/index.php/', '/', $pageInfo->currentSefUrl);
			ShlSystem_Log::debug('sh404sef', 'Redirecting to same url without /index.php: ' . $targetSefUrl);
			shRedirect($targetSefUrl);
		}
	}

	protected function _checkAliases($uri)
	{
		if (self::$requestParsed)
		{
			return;
		}

		// our configuration object
		$config = Sh404sefFactory::getConfig();
		$pageInfo = Sh404sefFactory::getPageInfo();

		// separate path and full request
		$path = $uri->getPath();
		$requestPath = str_replace(
			JURI::root() . ltrim($config->shRewriteStrings[$config->shRewriteMode], '/'),
			'', $pageInfo->originalUri
		);
		$queryString = $uri->getQuery();

		if (empty($requestPath))
		{
			return;
		}

		// build sql query, we may check both path and full query
		$sql = 'SELECT ??,??,??,?? FROM ?? WHERE ?? = ?';
		$nameQuoted = array(
			'id',
			'alias',
			'newurl',
			'hits',
			'#__sh404sef_aliases',
			'alias'
		);
		$quoted = array(
			$requestPath
		);

		// path different from full url requested, means there is a query string
		if (!empty($path) && $path != $requestPath)
		{
			$sql .= ' or ?? = ?';
			$nameQuoted[] = 'alias';
			$quoted[] = $path;
			if (JString::substr($path, -1) != '/')
			{
				// getPath will trim trailing / so we must try also with it
				$sql .= ' or ?? = ?';
				$nameQuoted[] = 'alias';
				$quoted[] = $path . '/';
			}
		}

		// finally order by alias desc, just have to choose one ordering
		$sql .= ' order by ?? desc';
		$nameQuoted[] = 'alias';

		try
		{
			$aliasRecord = ShlDbHelper::quoteQuery($sql, $nameQuoted, $quoted)->loadObject();
			if ($aliasRecord)
			{
				$trimmedAlias = trim($aliasRecord->alias);
				if ($trimmedAlias == 'index.php' || $trimmedAlias == '/' || empty($trimmedAlias))
				{
					return;
				}
			}

			// append query string if some params were added to the alias
			if (!empty($aliasRecord) && !empty($aliasRecord->newurl) && !empty($queryString) && $aliasRecord->alias != $requestPath)
			{
				$aliasRecord->newurl .= JString::strpos($aliasRecord->newurl, '?') !== false ? '&' . $queryString
					: '?' . $queryString;
			}

			// do the redirect, after checking a few conditions
			if (!empty($aliasRecord))
			{
				$destUrl = $this->checkRedirect($aliasRecord->newurl, $pageInfo->originalUri);
				if (!empty($destUrl))
				{
					// update alias hits counter
					ShlDbHelper::update('#__sh404sef_aliases', array('hits' => $aliasRecord->hits + 1), array('id' => $aliasRecord->id));

					// record a detailed log of the alias, if set to
					if ($config->logAliasesHits)
					{
						$recorder = Sh404sefModelReqrecorder::getInstance(Sh404sefModelReqrecorder::REQUEST_ALIAS);
						$recorder->record($path, $destUrl);
					}
					// do redirect
					ShlSystem_Log::debug('sh404sef', 'Redirecting to ' . $destUrl . ' from alias ' . $path);
					shRedirect($destUrl);
				}
			}
		}
		catch (Sh404sefException $e)
		{
			// if error, just log
			ShlSystem_Log::error(
				'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__,
				' Database error reading aliases: ' . $e->getMessage()
			);
		}
	}

	/**
	 *
	 * Performs various seo redirect checks, in case where the request is
	 * for the home page. A home page request only means the request path is empty;
	 * such request may have query vars - ie site.com/index.php?option=com_content&id=12&view=article
	 * is still a home page request
	 *
	 * Include redirecting site.com/index.php to site.com and, in the future
	 * site.com/index.php?lang=xx to site.com/xx or the correct sef url for that language
	 *
	 * @param JURI object $uri
	 */
	protected function _checkHomepageRedirects($uri)
	{
		if (self::$requestParsed || !$this->_canRedirectFromNonSef($uri))
		{
			return;
		}

		// check if we already did all the redirections we can
		$pageInfo = Sh404sefFactory::getPageInfo();

		// basic data
		$sefConfig = Sh404sefFactory::getConfig();
		$path = $uri->getPath();
		$url = $this->getFullUrl($uri);
		$vars = $uri->getQuery(true);

		// 0 - check forced homepage, in case of index.html splash page (!!!)
		if (!empty($sefConfig->shForcedHomePage)
			&& ($sefConfig->shForcedHomePage == $pageInfo->originalUri
				|| $sefConfig->shForcedHomePage . '?sh404sef_splash=1' == $pageInfo->originalUri)
		)
		{
			return;
		}

		// 1 - check index.php on home page
		$indexString = str_replace($pageInfo->getDefaultFrontLiveSite(), '', $pageInfo->currentSefUrl);
		$indexString = explode('?', $indexString);
		$indexString = JString::substr($indexString[0], -9);
		// IIS sometimes adds index.php to uri, even if user did not request it.
		$IIS = !empty($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== false;

		if (sh404SEF_REDIRECT_IF_INDEX_PHP && !$IIS && (empty($path) || $path == 'index.php') && empty($vars)
			&& $indexString == 'index.php'
		)
		{
			// redirect to home page
			$targetUrl = $pageInfo->getDefaultFrontLiveSite();
			ShlSystem_Log::debug('sh404sef', 'Redirecting home page request with index.php to home page: ' . $targetUrl);
			shRedirect($targetUrl);
		}

		// 2 - Home page, some query vars, but we don't have an option var, ie dunno what component to go to
		// just drop the index.php
		if (empty($path) && !empty($vars) && empty($vars['option']) && empty($vars['lang'])
			&& $indexString == 'index.php'
		)
		{
			$query = $uri->getQuery();
			$targetUrl = $uri->base() . (empty($query) ? '' : '?' . $query);
			ShlSystem_Log::debug(
				'sh404sef',
				'Redirecting home page request with index.php and query vars to home page: ' . $targetUrl
			);
			shRedirect($targetUrl);
		}

		// 3 - request for home page with a language element
		if (empty($path) && !empty($vars) && empty($vars['option']) && !empty($vars['lang'])
			&& $indexString == 'index.php'
		)
		{
			$query = $uri->getQuery();
			$targetUrl = $uri->base() . (empty($query) ? '' : '?' . $query);
			ShlSystem_Log::debug(
				'sh404sef',
				'Redirecting home page request to non-default language home page: ' . $targetUrl
			);
			shRedirect($targetUrl);
		}

		// 4 - Still home page, ie empty path, but some query vars, lookup db to find it
		if (empty($path) && !empty($vars) && !empty($vars['option']))
		{
			// need an url model
			$urlModel = ShlMvcModel_Base::getInstance('Sefurls', 'Sh404sefModel');
			// rebuild the non-sef url requested
			$nonSefUrl = 'index.php' . $uri->toString(
					array(
						'query'
					)
				);
			$sefUrl = '';
			// try to get it from our url store
			$urlType = shGetSefURLFromCacheOrDB($nonSefUrl, $sefUrl);
			if ($urlType == sh404SEF_URLTYPE_AUTO || $urlType == sh404SEF_URLTYPE_CUSTOM)
			{
				// found a match in database
				$sefUrl = $uri->base() . ltrim($sefConfig->shRewriteStrings[$sefConfig->shRewriteMode], '/') . $sefUrl;
				ShlSystem_Log::debug('sh404sef', 'redirecting non-sef to existing SEF : ' . $sefUrl);
				shRedirect($sefUrl);
			}

			// 5 - no success yet, we'll try SEF-y the non-sef url
			if ($sefConfig->shRedirectNonSefToSef && !empty($nonSefUrl)
				&& (empty($pageInfo->isMultilingual)
					|| (($pageInfo->isMultilingual !== false)
						&& Sh404sefHelperLanguage::getDefaultLanguageTag()
						== $pageInfo->currentLanguageTag))
			)
			{
				$sefUrl = JRoute::_($nonSefUrl, $xhtml = false);
				$s = str_replace(JURI::base(true), '', $sefUrl);
				if (!empty($s) && $s != '/')
				{
					$sefUrl = $uri->toString(
							array(
								'scheme',
								'host',
								'port'
							)
						) . $sefUrl;
					if (!shIsHomepage($sefUrl) && strpos($sefUrl, 'option=com') === false && $sefUrl != $url)
					{
						ShlSystem_Log::debug(
							'sh404sef',
							'Homepage redirect to newly created SEF : ' . $sefUrl . ' from ' . $url
						);
						shRedirect($sefUrl);
					}
				}
			}
		}
	}

	/**
	 *
	 * Check a number of conditions, both global and
	 * relative to a provided source page uri
	 * to decide whether a redirect to another page
	 * can take place
	 * Will also check configuration settings
	 *
	 * @param object $uri
	 */
	protected function _canRedirectFromNonSef($uri, $method = '')
	{
		// if not parsing the initial request, no way we can redirect
		if (self::$requestParsed)
		{
			return false;
		}

		$pageInfo = Sh404sefFactory::getPageInfo();

		// what's the requested url?
		$url = $pageInfo->currentSefUrl;
		$request = str_replace($pageInfo->getDefaultFrontLiveSite(), '', $url);

		if ($request != '/index.php' && strpos($url, 'index.php?') === false)
		{
			return false;
		}

		if ($request != '/index.php' && strpos($url, 'sh404sef_splash=1') !== false)
		{
			return false;
		}

		// use framework if no method passed
		if (empty($method))
		{
			$method = JRequest::getMethod();
		}

		// get config
		$sefConfig = Sh404sefFactory::getConfig();

		// get/set data
		$vars = $uri->getQuery(true);
		$canRedirect = true;

		// first condition: component should not be set to "skip"
		if (!empty($vars['option']))
		{
			$shOption = str_replace('com_', '', $vars['option']);
			if (!empty($shOption) && in_array($shOption, $sefConfig->skip))
			{
				$canRedirect = false;
			}
		}

		// redirect only well formed component name
		$canRedirect = $canRedirect && (empty($vars['option']) || strpos($vars['option'], 'com_') === 0);

		// additional redirects checks on the full URL
		$canRedirect = $canRedirect && $sefConfig->shRedirectNonSefToSef && $this->_canRedirectFrom($uri, $method);

		return $canRedirect;
	}

	/**
	 *
	 * Checks if the path of a request has a trailing slash
	 *
	 * @param JURI object $uri
	 *
	 * @return boolean true if has trailing slash
	 */
	protected function _hasTrailingSlash()
	{
		$url = Sh404sefFactory::getPageInfo()->currentSefUrl;
		$rawPath = explode('?', $url);
		$rawPath = $rawPath[0]; // query string removed, if any
		$rawPath = explode('#', $rawPath);
		$rawPath = $rawPath[0]; // fragment removed, if any
		$trailingSlash = JString::substr($rawPath, -1) == '/';

		return $trailingSlash;
	}

	/**
	 * As of Joomla 3.2.x, mailto component incorrectly uses JRequest::getVar() to fetch
	 * the mailto link (it uses the 'method' param, which is ok on J 2.5, but bad on 3.x, as
	 * the router does not set the request after routing, but instead sets $app->input)
	 * This hack makes sure link is passed as a GET var
	 *
	 * @param array $vars
	 */
	private function _mailtoHack($vars)
	{
		if (!version_compare(JVERSION, '3.0', 'ge') || empty($vars['option']) || empty($vars['link']))
		{
			return;
		}
		if ($vars['option'] == 'com_mailto' && empty($vars['view']))
		{
			JRequest::setVar('link', $vars['link']);
		}
	}

	/**
	 * Some extensions do unusual things we might have to accomodate
	 *
	 * @param JRouter $jRouter
	 * @param JURI $uri
	 * @param array $vars
	 */
	private function _extensionsParseHacks(&$jRouter, &$uri, &$vars)
	{
		// Some extensions have not updated to J 3.0 API
		// or use hacks to delay updating
		$method = strtolower(JFactory::getApplication()->input->getMethod());
		if ('get' == $method && !empty($vars['option'])
			&& strpos(Sh404sefFactory::getConfig()->extensionToExtractGetVars, $vars['option']) !== false
		)
		{
			foreach ($vars as $key => $value)
			{
				JRequest::setVar($key, $value);
			}
		}

		// Kunena - sometimes - uses an internal $current variable
		// to build urls, notably pagination links. That variable
		// is set inside the parse function of its router.php KunenaParseRoute function
		if ('get' == $method && !empty($vars['option']) && $vars['option'] == 'com_kunena' && class_exists('KunenaRoute') && isset(KunenaRoute::$current))
		{
			foreach ($vars as $key => $value)
			{
				if ($key != 'start')
				{
					KunenaRoute::$current->setVar($key, $value);
				}
			}
		}

		// Community Builder: uses directly $_GET, which is not set
		// by the Joomla router
		if ('get' == $method && !empty($vars['option']) && $vars['option'] == 'com_comprofiler')
		{
			foreach ($vars as $key => $value)
			{
				if ($key == 'limitstart')
				{
					JRequest::setVar($key, $value);
				}
			}
		}

		// mijoshop still using J2 api. Also, always require the "route" var
		// extracted into $_GET, even on POST request
		if (!empty($vars['option']) && $vars['option'] == 'com_mijoshop')
		{
			if (!empty($vars['route']))
			{
				JRequest::setVar('route', $vars['route'], 'get');
			}
		}
	}

	/**
	 * Method attached to J! main router object
	 * and processed as a buildRule
	 *
	 * @param object J! router object reference
	 * @param object uri object provided by Joomla. We must set the path of this
	 *               object, and adjust its query vars list, removing those that are represented
	 *               in the path and thus don't need anymore to be specified as query vars
	 */
	public function buildRule(&$jRouter, &$uri)
	{
		// if superadmin, display non-sef URL, for testing/setting up purposes
		if (sh404SEF_NON_SEF_IF_SUPERADMIN)
		{
			$user = JFactory::getUser();
			if ($user->usertype == 'Super Administrator')
			{
				ShlSystem_Log::debug('sh404sef', 'Returning non-sef because superadmin said so.');

				return;
			}
		}

		// keep a copy of  Joomla original URI, which has article names in it (ie: 43:article-title)
		$this->_originalBuildUri = clone ($uri);

		// build the path
		$this->_buildSefRoute($uri);

		// a few per-extension hacks
		$this->_extensionsBuildHacks($jRouter, $uri);

		// record URL source information
		Sh404sefHelperUrl::storeUrlSource();
	}

	/**
	 * Some extensions do unusual things we might have to accomodate
	 *
	 * @param JRouter $jRouter
	 * @param JURI $uri
	 */
	private function _extensionsBuildHacks(&$jRouter, &$uri)
	{
		// From J! 3.4.3 language filter is broken, not reading cookie language when
		// set to "Not remove language code on default language".
		// This is best workaround to ensure users can actually switch language
		// to default language at the expense of duplicating the whole site
		// Adding a canonical to alleviate that.
		$targetJoomlaVersions = Sh404sefFactory::getPConfig()->jVersionForceHomeLangCode;
		if (version_compare(JVERSION, $targetJoomlaVersions['min'], 'ge')
			&& version_compare(JVERSION, $targetJoomlaVersions['max'], '<')
			&& Sh404sefFactory::getPageInfo()->isMultilingual
		)
		{
			$path = $uri->getPath();
			$query = $uri->getQuery();

			if (empty($path) && empty($query))
			{
				$sef = Sh404sefHelperLanguage::getDefaultLanguageSef();
				$path = 'index.php' . (empty($sef) ? '' : '/' . $sef . '/');
				$uri->setPath($path);
				Sh404sefHelperUrl::$buildingSef = $path;
			}
		}
	}

	/**
	 * Public wrapper around encode route segments
	 * from J! router method
	 *
	 * @param    array    An array of route segments
	 *
	 * @return  array
	 */
	public function encodeSegments($segments)
	{
		sh404sefHelperUrl::encodeSegments($segments);
	}

	protected function _buildSefRouteInternal(&$uri)
	{
		// J! 4.3+ hack for fixing language filter plugin modifying language query variable on the fly
		$this->_fixUriLanguageVar($uri);

		// kill Joomla suffix, so that it doesn't add or remove it in the parsing/building process
		JFactory::$config->set('sef_suffix', 0);

		$pageInfo = Sh404sefFactory::getPageInfo();
		$sefConfig = Sh404sefFactory::getConfig();
		$app = JFactory::getApplication();
		$menu = $app->getMenu();

		// restore uri as it will have been "damaged" by languagefilter plugin
		if ($app->getLanguageFilter())
		{
			$originalUrl = Sh404sefHelperUrl::getOriginalUrlFromUri($uri);
			$uri = new JUri($originalUrl);
			$this->_fixUriLanguageVar($uri);
			$option = $uri->getVar('option', '');
			if (empty($option))
			{
				$Itemid = $uri->getVar('Itemid', 0);
				if (!empty($Itemid))
				{
					$menuItem = $menu->getItem($Itemid);
					if (!empty($menuItem))
					{
						$tmpVars = array();
						parse_str(str_replace('index.php?', '', $menuItem->link), $tmpVars);
						if (!empty($tmpVars['option']))
						{
							$uri->setVar('option', $tmpVars['option']);
						}
					}
				}
			}
		}

		// keep a copy of almost-original uri
		$originalUri = clone ($uri);

		// put non-sef in a standard form, includes restoring full url
		// from menu item, when only an Itemid is specified
		shNormalizeNonSefUri($uri, $menu);

		// do our job!
		$query = $uri->getQuery(false);

		// shortcut for components that must be left non-sef or use Joomla! router
		$option = Sh404sefHelperUrl::getUrlVar($query, 'option', '');
		$option = str_replace('com_', '', $option);
		if (!empty($option))
		{
			$alwaysSkip = Sh404sefFactory::getPConfig()->alwaysNonSefComponents;
			$toSkip = array_merge($alwaysSkip, $sefConfig->skip);
			if (in_array($option, $toSkip))
			{
				$sefUrl = $uri->toString(
					array(
						'path',
						'query',
						'fragment'
					)
				);
				$uri->setPath($sefUrl);
				$uri->setQuery(array());

				return;
			}

			// use J! router
			if (in_array($option, $sefConfig->useJoomlaRouter))
			{
				// restore original suffix setting, so that Joomla adds suffix
				// where it's due
				JFactory::$config->set('sef_suffix', $pageInfo->joomlaSuffixSetting);
				$uri = $this->_originalBuildUri;

				return;
			}
		}

		// no shortcut, normal route
		Sh404sefHelperUrl::$buildingNonSef = 'index.php?' . $query;
		$route = shSefRelToAbs(Sh404sefHelperUrl::$buildingNonSef, null, $uri, $originalUri);
		$route = ltrim(str_replace($pageInfo->getDefaultFrontLiveSite(), '', $route), '/');
		$route = $route == '/' ? '' : $route;

		// check against forced home page, ie using a splash page
		$forcedHomePage = empty($sefConfig->shForcedHomePage) ? ''
			: str_replace($pageInfo->getDefaultFrontLiveSite(), '', $sefConfig->shForcedHomePage);
		if ($route == 'index.php' && !empty($forcedHomePage))
		{
			$route = $forcedHomePage . '?sh404sef_splash=1';
		}

		// find path
		$nonSefVars = $uri->getQuery($asArray = true);
		if (strpos($route, '?') !== false && !empty($nonSefVars))
		{
			$parts = explode('?', $route);
			// there are some query vars, just use the path
			$path = $parts[0];
		}
		else
		{
			$path = $route;
		}
		$uri->setPath($path);
		Sh404sefHelperUrl::$buildingSef = $path;
	}

	/**
	 * Process "lang" var in a JURI object to make sure it's a 2 letters lang code
	 * instead of the possibly 4 letters code that can be injected by the language filter plugin
	 *
	 * @param $uri
	 * @param $languageCode
	 */
	private function _fixUriLanguageVar(&$uri)
	{
		$languageCode = $uri->getVar('lang', '');
		if (!empty($languageCode))
		{
			$isValid = Sh404sefHelperLanguage::validateSefLanguageCode($languageCode);
			// if not valid, but looks like a full language code as can be added by language filter plugin
			// preprocess rules post J! 3.4, we can turn that back into a valid sef-code
			// just remove the extra characters
			// trying to invalidate completely is a problem for many sites
			// where default language does not exist as a "Content" language
			if (!$isValid && strpos($languageCode, '-') !== false)
			{
				$fixedLang = explode('-', $languageCode);
				$fixedLang = empty($fixedLang) ? '' : $fixedLang[0];
				if (!empty($fixedLang))
				{
					$uri->setVar('lang', $fixedLang);
				}
			}
			elseif (!$isValid)
			{
				// destroy invalid language
				$uri->delVar('lang');
			}
		}
	}

	/**
	 * Actual method performing parsing of a request as described
	 * by an JURI object
	 *
	 * Also performs additional duties, like checking on aliases,
	 * searching alternates syntax (add/remove trailing slash),
	 * auto-redirect from non-sef to sef, etc
	 * Possibly trigger a 404 if url can't be parsed to a valid
	 * non sef url
	 *
	 * @see JRouter::_parseSefRoute()
	 */
	protected function _parseSefRouteInternal(&$uri)
	{
		$app = JFactory::getApplication();

		// will hold query vars parsed
		$vars = array();

		// general config
		$sefConfig = Sh404sefFactory::getConfig();
		$pageInfo = Sh404sefFactory::getPageInfo();

		// get request path and try to decode it
		$path = $uri->getPath();
		$method = JFactory::getApplication()->input->getMethod();

		// home page or
		// /xx or /xx/ where xx is the default language code, on a multilingual site.
		$isHome = empty($path) ||
			(
				$app->getLanguageFilter() && JString::rtrim($path, '/') == Sh404sefHelperLanguage::getDefaultLanguageSef()
			);
		if ($isHome)
		{
			$path = '';
			// check more redirects: from non sef to sef
			$this->_checkNonSefToSefRedirects($uri);

			// now if no query vars, this is really home page
			$uriVars = $uri->getQuery(true);
			$uriVars = Sh404sefHelperUrl::stripTrackingVarsFromArray($uriVars);
			if ($method !== 'POST' && empty($uriVars))
			{
				// Create the query array.
				$homeLink = $pageInfo->homeLink;
				$homeLink = str_replace('index.php?', '', $homeLink);
				$homeLink = str_replace('&amp;', '&', $homeLink);

				parse_str($homeLink, $vars);

				// Get its Itemid
				$vars['Itemid'] = $pageInfo->homeItemid;
			}
			else
			{
				// if some query vars, just pass them thru
				$vars = $uriVars;
			}
		}

		// non home page
		if (!empty($path))
		{
			// lookup db for this sef
			$lookUp = $this->_lookupSef($uri, $vars, $path);

			// store the fetched data, if any
			$vars = $lookUp->vars;

			// and take additional actions based on the result of the lookup
			switch ($lookUp->urlType)
			{
				// existing matching url
				case sh404SEF_URLTYPE_AUTO:
				case sh404SEF_URLTYPE_CUSTOM:
					// record a hit on that URL
					if (!empty($lookUp->urlRecord))
					{
						$hits = $lookUp->urlRecord->cpt + 1;
						ShlDbHelper::update('#__sh404sef_urls', array('cpt' => $hits), array('id' => $lookUp->urlRecord->id, 'rank' => 0));
					}
					break;

				// 404 or some kind of redirect
				default:
					// maybe there are some manually created redirects
					// in Joomla Redirect component?
					$this->_checkJoomlaRedirects($uri);

					// check more redirects: from Joomla SEF to our SEF
					$this->_checkJoomlaSefToSefRedirects($uri);

					// try find similar urls to redirect to: with or without trailing slash
					$this->_checkTrailingSlash($uri);

					// there might be an alias we're supposed to redirect current request to
					$this->_checkAliases($uri);

					// check if this is a short url
					$this->_checkShurls($uri);

					// try using J! router
					// if at least one extension uses Joomla! router, we must first try to use that
					if (!empty(Sh404sefFactory::getConfig()->useJoomlaRouter))
					{
						// on ML sites, JOomla router doesn't expect the language code in the path,
						// we have to remove it
						if (!empty($this->_guessedLanguageCode) && JString::substr($path, 0, Jstring::strlen($this->_guessedLanguageCode) + 1) == $this->_guessedLanguageCode . '/')
						{
							$uri->setPath(JString::substr($path, Jstring::strlen($this->_guessedLanguageCode) + 1));
						}

						// use parent parser
						$vars = parent::_parseSefRoute($uri);

						// if we found something, raise a flag
						self::$parsedWithJoomlaRouter = true;
						// collect vars that may have been stored by J! such as Itemid
						$vars = array_merge(Sh404sefFactory::getPageInfo()->router->getVars(), $vars);
						$this->setVars(array());

						// and cut through the rest of the processing
						break;
					}

					// try to auto redirect to similar URL
					if ($sefConfig->autoRedirect404)
					{
						$this->_autoRedirect404($uri);
					}

					// if no alternative found, issue a 404
					$vars = $this->_do404();
					break;
			}
		}

		// Set the menu item as active, if we found any
		// this would normally be done by Joomla own _parseSefRoute() method
		// except it's not gonna be run
		if (!self::$requestParsed && ($pageInfo->isMultilingual === false || $pageInfo->isMultilingual == 'joomla'))
		{
			if (!empty($vars['Itemid']))
			{
				$app->getMenu()->setActive($vars['Itemid']);
			}
		}

		// do security checks after decoding url
		$secLayerFile = JPATH_ROOT . '/components/com_sh404sef/shSec.php';
		if ($sefConfig->shSecEnableSecurity)
		{
			include_once($secLayerFile);
			// do security checks
			shDoSecurityChecks($this->getFullUrl($uri), false); // check this newly created URL
		}

		return $vars;
	}

	/**
	 * Redirect a request that should be a 404 Not found
	 * to a similar one, taken from the URL database
	 *
	 * @param $uri
	 */
	protected function _autoRedirect404($uri)
	{
		if (function_exists('shFindSimilarUrls'))
		{
			$config = Sh404sefFactory::getConfig();
			$plugin = JPluginHelper::getPlugin('sh404sefcore', 'sh404sefsimilarurls');

			// get params from plugin
			$pluginParams = new JRegistry;
			$pluginParams->loadString($plugin->params);

			// search for similar URLs
			$path = $uri->getPath();
			$urlList = shFindSimilarUrls($path, $pluginParams);
			if (!empty($urlList))
			{
				$target = array_shift($urlList);
				if (!empty($target->oldurl) && $path != $target->oldurl && $this->_canRedirectFrom($uri, $method = '', $path))
				{
					// still store the 404
					$this->_store404Page();

					$config = Sh404sefFactory::getConfig();

					// load language strings for info message
					$message = '';
					if ($config->autoRedirect404WithMessage)
					{
						JFactory::getLanguage()->load('plg_sh404sefcore_sh404sefsimilarurls', JPATH_ADMINISTRATOR);
						$message = JText::sprintf('COM_SH404SEF_404_AUTO_REDIRECT', htmlspecialchars('/' . $path, ENT_COMPAT, 'UTF-8'));
						if (!empty($urlList))
						{
							$alternativesHtml = JText::_('COM_SH404SEF_404_AUTO_REDIRECT_ALTERNATIVES');
							if (strpos($alternativesHtml, '{sh404sefSimilarUrls}') !== false)
							{
								$message = $message . ' ' . str_replace('{sh404sefSimilarUrls}', shFormatSimilarUrls($urlList), $alternativesHtml);
							}
						}
					}

					// redirect to similar URL
					$targetUrl = JUri::base() . ltrim($config->shRewriteStrings[$config->shRewriteMode], '/')
						. JString::ltrim($target->oldurl, '/');
					ShlSystem_Log::debug('sh404sef', 'Redirecting 404 to similar URL : ' . $targetUrl . ' from ' . $path);
					if (!empty($message))
					{
						JFactory::getSession()->set(
							'wb_sh404sef_404_redirect_message',
							array(
								'content' => $message,
								'title' => Jtext::_('COM_SH404SEF_404_AUTO_REDIRECT_TITLE'),
								'rawUrlList' => $urlList,
								'color' => $config->error404MsgColor,
								'background_color' => $config->error404MsgBackgroundColor, // #7FBA00
								'opcacity' => $config->error404MsgOpacity
							)
						);
					}
					shRedirect($targetUrl);
				}
			}
		}
	}

	/**
	 *
	 * Redirects a non-sef request to its SEF equivalent
	 * if any can be found or built
	 *
	 * @param unknown_type $uri
	 */
	protected function _checkNonSefToSefRedirects($uri)
	{
		// don't redirect if this is simply J! trying to parse a URL
		// or if this is an ajax call or similar
		if (!$this->_canRedirectFromNonSef($uri))
		{
			return;
		}

		// check this is really a non-sef request
		$path = $uri->getPath();
		if (!empty($path))
		{
			// we only try to redirect fully non sef requests, too hard otherwise
			return;
		}

		// prevent homepage loops
		$query = $uri->getQuery();
		if (empty($query))
		{
			// empty path and empty query, this is just root url
			return;
		}

		// break linkage
		$newUri = clone $uri;

		// collect languages information
		$languages = JLanguageHelper::getLanguages('lang_code');

		// make sure we have a language code
		$langCode = $newUri->getVar('lang');
		if (empty($langCode))
		{
			$currentLang = JFactory::getLanguage()->getTag();
			$newUri
				->setVar(
					'lang',
					empty($languages[$currentLang]) || empty($languages[$currentLang]->sef) ? 'en'
						: $languages[$currentLang]->sef
				);
		}

		//redirect
		$this->_redirectNonSef($newUri);
	}

	protected function _redirectNonSef($uri)
	{
		if (!$this->_canRedirectFromNonSef($uri))
		{
			return;
		}

		// search cache and db for a sef url
		$nonSefUrl = Sh404sefHelperUrl::sortUrl('index.php?' . $uri->getQuery());
		$targetSefUrl = '';
		$urlType = ShlMvcModel_Base::getInstance('Sefurls', 'Sh404sefModel')
		                           ->getSefURLFromCacheOrDB($nonSefUrl, $targetSefUrl);

		$pageInfo = Sh404sefFactory::getPageInfo();

		// found a match : redirect
		if ($urlType == sh404SEF_URLTYPE_AUTO || $urlType == sh404SEF_URLTYPE_CUSTOM)
		{
			$tmpUri = new JURI($uri->base() . $targetSefUrl);
			$targetSefUrl = $tmpUri->toString();
			if ($targetSefUrl != $pageInfo->currentSefUrl)
			{
				ShlSystem_Log::debug('sh404sef', 'redirecting non-sef to existing SEF : ' . $targetSefUrl);
				shRedirect($targetSefUrl);
			}
		}

		// haven't found a SEF in the cache or DB, maybe we can just create it on the fly ?
		if (!empty($nonSefUrl) && (empty($pageInfo->isMultilingual) || $pageInfo->isMultilingual == 'joomla'))
		{
			// $currentLanguageTag is still deafult lang, as language has not been discovered yet
			$GLOBALS['mosConfig_defaultLang'] = Sh404sefFactory::getPageInfo()->currentLanguageTag;

			// create new sef url
			$targetSefUrl = JRoute::_($nonSefUrl, $xhtml = false);
			$s = str_replace(JURI::base(true), '', $targetSefUrl);
			if (!empty($s) && $s != '/' && $targetSefUrl != $pageInfo->currentSefUrl)
			{
				$targetSefUrl = $uri->toString(
						array(
							'scheme',
							'host',
							'port'
						)
					) . $targetSefUrl;
				$sourceUrl = $this->getFullUrl($uri);
				if (strpos($targetSefUrl, 'option=com') === false && $targetSefUrl != $sourceUrl)
				{
					ShlSystem_Log::debug(
						'sh404sef',
						'Redirecting non-sef to newly created SEF : ' . $targetSefUrl . ' from ' . $nonSefUrl
					);
					shRedirect($targetSefUrl);
				}
			}
		}
	}

	/**
	 *
	 * Lookup a SEF url in the cache and database, searching
	 * for a non-sef associated url
	 * Returns a record holding query vars and a status code
	 * If no non-sef is found, incoming query vars are returned untouched
	 * If a non-sef is found, a query var array is built, merged with incoming vars
	 * and returned instead of the incoming one
	 *
	 * @param        JURI    object full request details
	 * @param array $vars key/value pairs of query vars, usually empty
	 * @param string $sefUrl the sef url to search for
	 */
	protected function _lookupSef($uri, $vars, $sefUrl)
	{
		// object to hold result
		$result = new stdClass();

		// identify Sh404sefClassBaseextplugin::TYPE_SIMPLE URLs, ie simple encoding, DB bypass
		$isSimpleUrl = $this->_isSimpleEncodingSef($sefUrl);
		if ($isSimpleUrl)
		{
			// handle manual decoding
			$vars = $this->_parseSimpleUrls($sefUrl);
			$urlType = sh404SEF_URLTYPE_AUTO;
			$urlRecord = null;
		}

		if (!$isSimpleUrl)
		{
			// get a model and check if we've seen this request before
			$nonSefUrl = '';
			$urlRecord = ShlMvcModel_Base::getInstance('Sefurls', 'Sh404sefModel')
			                             ->getNonSefUrlRecordFromDatabase($sefUrl, $nonSefUrl);
			$urlType = $urlRecord['status'];

			switch ($urlType)
			{
				// existing matching url
				case sh404SEF_URLTYPE_AUTO:
				case sh404SEF_URLTYPE_CUSTOM:
					// our db lookup is case insensitive, which allows
					// doing a 301 to the correct url case, avoiding duplicate content
					$this->_checkRedirectToCorrectCase($uri, $sefUrl);

					// collect the query vars, using a JURI instance
					$newUri = new JURI($nonSefUrl);
					$vars = array_merge($vars, $newUri->getQuery(true));
					break;

				// 404 or some kind or error
				default:
					break;
			}
		}

		// store result
		$result->vars = $vars;
		$result->urlType = $urlType;
		$result->urlRecord = $urlRecord['url'];

		return $result;
	}

	/**
	 * Recognize if a given (usually incoming) url has been
	 * encoded as a simple-type encoding
	 * Such urls start with component/com_ or lang_code/component/com_
	 *
	 * @param string $url
	 */
	protected function _isSimpleEncodingSef($url)
	{
		$isSimpleEncodingSef = $this->_getSimpleSefLanguageSefCode($url) !== false;

		return $isSimpleEncodingSef;
	}

	/**
	 * Figure out whether an incoming url has been encoded as SEF
	 * using the 'simple' scheme, and at the same time
	 * identify if a language code was encoded
	 * Format is: xx/component/com_xxxx... or component/com_xxxx...
	 *
	 * Return true if simple scheme, a sef lang string if simple scheme and a language
	 * has been recognized or an empty string if default language and language filter
	 * plugin is set to remove lang code from URL on default langague or this is
	 * a monolingual site
	 *
	 * @param string $url
	 *
	 * @return string|boolean false if not simple-scheme SEF, empty string if default language or sef lang code otherwise
	 */
	protected function _getSimpleSefLanguageSefCode($url)
	{
		static $prefixes = null;

		$config = Sh404sefFactory::getConfig();
		$app = Jfactory::getApplication();

		if (is_null($prefixes))
		{
			$prefixes = array();
			// build up prefix list
			$languages = JLanguageHelper::getLanguages('sef');
			$default = Sh404sefHelperLanguage::getDefaultLanguageSef();
			foreach ($languages as $sefCode => $language)
			{
				if ($sefCode == $default)
				{
					$prefixes[$sefCode] = $app->getLanguageFilter()
					&& Sh404sefHelperLanguage::getInsertLangCodeInDefaultLanguage()
						? $sefCode . '/component/com_' : 'component/com_';
				}
				else
				{
					$prefixes[$sefCode] = $sefCode . '/component/com_';
				}
			}
		}

		foreach ($prefixes as $sefCode => $prefix)
		{
			if ($prefix == JString::substr($url, 0, Jstring::strlen($prefix)))
			{
				if ($app->getLanguageFilter() && !Sh404sefHelperLanguage::getInsertLangCodeInDefaultLanguage()
					&& $sefCode == Sh404sefHelperLanguage::getDefaultLanguageSef()
				)
				{
					return '';
				}
				else
				{
					// return no language code if not a multilingual site
					return $app->getLanguageFilter() ? $sefCode : '';
				}
			}
		}

		return false;
	}

	/**
	 *
	 * Parse sef urls built using the TYPE_SIMPLE method
	 * ie: simply combining each argument, comma separated
	 *
	 * @param string $nonSefUrl starting with /component/com_xxxxx/...
	 */
	protected function _parseSimpleUrls($sefUrl)
	{
		$vars = array();

		// create an array
		$urlArray = explode('/', $sefUrl);
		$urlArray = array_filter($urlArray);
		$urlArray = array_values($urlArray);

		$sefLangCode = $this->_getSimpleSefLanguageSefCode($sefUrl);
		if (!empty($sefLangCode))
		{
			// remove language information
			$vars['lang'] = $sefLangCode;
			array_shift($urlArray);
		}

		// remove 'component'
		array_shift($urlArray);

		// start by extracting option, which is always first
		$option = array_shift($urlArray);

		// sanitize
		$vars['option'] = JFilterInput::getInstance()->clean($option, 'cmd');

		// process remaining vars, if any
		if (!empty($urlArray))
		{
			foreach ($urlArray as $segment)
			{
				$tmp = explode(',', $segment, 2);
				if (count($tmp) == 2)
				{
					$vars[$tmp[0]] = $tmp[1];
				}
			}
		}

		return $vars;
	}

	protected function _checkRedirectToCorrectCase($uri, $path)
	{
		if (!$this->_canRedirectFrom($uri))
		{
			return;
		}

		// get config object
		$sefConfig = Sh404sefFactory::getConfig();

		if ($sefConfig->redirectToCorrectCaseUrl)
		{
			// if initial query exactly matches oldurl found in db, then case is correct
			// else we redirect to the url found in db, but we also need to append query string to it !
			$originalPath = $uri->getPath();

			// now the only difference between the two can be the case
			if ($originalPath != $path)
			{
				// can only be different from case, change case in uri, and add rewrite mode prefix, if any
				$uri->setPath($path);
				$targetUrl = $uri->base() . ltrim($sefConfig->shRewriteStrings[$sefConfig->shRewriteMode], '/')
					. $uri->toString(
						array(
							'path',
							'query',
							'fragment'
						)
					);
				// perform redirect
				ShlSystem_Log::debug(
					'sh404sef',
					'Redirecting to correct url case : from ' . $this->getFullUrl($uri) . ' to ' . $targetUrl
				);
				shRedirect($targetUrl);
			}
		}
	}

	/**
	 * Checks Joomla's redirections table for
	 * existing redirects
	 *
	 * @param JURI $uri
	 */
	protected function _checkJoomlaRedirects($uri)
	{
		if (self::$requestParsed)
		{
			return;
		}
		$fullUrl = Sh404sefFactory::getPageInfo()->currentSefUrl;
		$redirectUrl = ShlDbHelper::selectObject(
			'#__redirect_links', '*',
			array(
				'old_url' => $fullUrl,
				'published' => 1
			)
		);
		if (!empty($redirectUrl->new_url))
		{
			ShlSystem_Log::debug(
				'sh404sef',
				'Redirecting 404 to SEF from Joomla! #__redirect_links table : ' . $redirectUrl->new_url . ' from '
				. $fullUrl
			);
			shRedirect($redirectUrl->new_url);
		}
	}

	protected function _checkJoomlaSefToSefRedirects($uri)
	{
		if (self::$requestParsed)
		{
			return;
		}
	}

	protected function _checkTrailingSlash($uri)
	{
		if (!$this->_canRedirectFrom($uri))
		{
			return;
		}

		// get config object
		$sefConfig = Sh404sefFactory::getConfig();

		// get the url path and try add or remove a trailing slash
		$path = $uri->getPath();

		// if path ends with current suffix, stop here
		if (JString::substr($path, -JString::strlen($sefConfig->suffix)) == $sefConfig->suffix)
		{
			return;
		}

		// same with optional index file
		if (JString::substr($path, -JString::strlen($sefConfig->addFile)) == $sefConfig->addFile)
		{
			return;
		}

		// now add or remove trailing slash. Must check existence of trailing slash
		// on the $uri->_uri, as J! always remove it from $uri->_path
		$trailingSlash = $this->_hasTrailingSlash($uri);
		if ($trailingSlash)
		{
			$path = JString::rtrim($path, '/');
		}
		else
		{
			$path .= '/';
		}

		// and check db again
		$vars = array(); // dummy, we don't care about the actual vars retrieved
		$lookUp = $this->_lookupSef($uri, $vars, $path);

		// if url exists with slash added or removed, 301 to that valid url
		if ($lookUp->urlType == sh404SEF_URLTYPE_AUTO || $lookUp->urlType == sh404SEF_URLTYPE_CUSTOM)
		{
			$query = $uri->getQuery();
			$targetSefUrl = $uri->base() . $path . (empty($query) ? '' : '?' . $query);
			ShlSystem_Log::debug('sh404sef', 'Redirecting to same with trailing slash added: ' . $targetSefUrl);
			shRedirect($targetSefUrl);
		}
	}

	protected function _checkShurls($uri)
	{
		if (self::$requestParsed)
		{
			return;
		}

		// sanitize
		$path = $uri->getPath();
		if (empty($path))
		{
			// no path in request, no possible short url
			return;
		}

		// our configuration object
		$config = Sh404sefFactory::getConfig();

		// check short url based on request path
		if ($config->enablePageId)
		{
			try
			{
				$shurlRecord = ShlDbHelper::selectObject(
					'#__sh404sef_pageids', '*', array(
					'pageid' => $path
				)
				);

				// checks on shur target: if empty, prevent loop, plus stitch back query string, if any
				if (!empty($shurlRecord))
				{
					$queryString = $uri->getQuery();
					if (!empty($queryString))
					{
						$shurlRecord->newurl .= JString::strpos($shurlRecord->newurl, '?') !== false ? '&' . $queryString : '?' . $queryString;
					}

					$target = $this->checkRedirect($shurlRecord->newurl, $this->getFullUrl($uri));
					if (!empty($target))
					{
						// update alias hits counter
						ShlDbHelper::update('#__sh404sef_pageids', array('hits' => $shurlRecord->hits + 1), array('id' => $shurlRecord->id));

						// record a detailed log of the 404, if set to
						if ($config->logShurlsHits)
						{
							$recorder = Sh404sefModelReqrecorder::getInstance(Sh404sefModelReqrecorder::REQUEST_SHURL);
							$recorder->record($path, $target);
						}
						// do redirect
						ShlSystem_Log::debug('sh404sef', 'Redirecting to ' . $target . ' from shurl ' . $path);
						shRedirect($target);
					}
				}
			}
			catch (Sh404sefException $e)
			{
				// if error, just log
				ShlSystem_Log::error(
					'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__,
					' Database error reading shurl: ' . $e->getMessage()
				);
			}
		}
	}

	protected function checkRedirect($dest, $incomingUrl)
	{
		$config = Sh404sefFactory::getConfig();
		if (!empty($dest) && $dest != $incomingUrl)
		{
			// redirect to alias
			if ($dest == sh404SEF_HOMEPAGE_CODE)
			{
				if (!empty($config->shForcedHomePage))
				{
					$dest = shFinalizeURL($config->shForcedHomePage);
				}
				else
				{
					$dest = shFinalizeURL(Sh404sefFactory::getPageInfo()->getDefaultFrontLiveSite());
				}
			}
			else
			{
				$shUri = new JURI($dest);
				$shOriginalUri = clone ($shUri);
				$dest = shSefRelToAbs($dest, '', $shUri, $shOriginalUri) . $shUri->toString(array('query'));
			}

			if ($dest != $incomingUrl)
			{
				return $dest;
			}
		}

		return false;
	}

	/**
	 * Make Joomla display our 404 page
	 *
	 * @param $uri
	 * @return mixed
	 */
	protected function _do404($error = null)
	{
		if (self::$requestParsed)
		{
			return;
		}

		// Create an error object if none supplied
		if (empty($error))
		{
			$error = new Exception('Not found', 404);
		}

		// render document
		self::sh404sefErrorPage($error);

		// and stop there
		exit();
	}

	/**
	 * Proxy method for handling exception and passing them
	 * on to the regular method sh404sefErrorPage
	 * Avoids errors on PHP7, due to sh404sefErrorPage requires
	 * a reference (B/C with previous Joomla handlers)
	 *
	 * @param $exception
	 */
	public function sh404sefExceptionHandler($exception)
	{
		$this->sh404sefErrorPage($exception);
	}

	/**
	 * Overrride for Joomla! default error page handler
	 *
	 * @param JException $error
	 */
	public function sh404sefErrorPage(& $error)
	{
		// only handle 404 errors, defer to previous handler otherwise
		$code = $error->getCode();
		if (404 != $code && !empty($this->joomlaErrorHandler) && ($this->joomlaErrorHandler['mode'] != 'callback' || !is_callable($this->joomlaErrorHandler['options'])))
		{
			JError::customErrorPage($error);
			return;
		}

		if (404 != $code && !empty($this->joomlaErrorHandler) && $this->joomlaErrorHandler['mode'] == 'callback' && is_callable($this->joomlaErrorHandler['options']))
		{
			call_user_func_array($this->joomlaErrorHandler['options'], array(&$error));
			return;
		}

		$setting = Sh404sefFactory::getConfig()->notFoundErrorHandling;
		if (Sh404sefHelperGeneral::ERROR_404_HANDLE == $setting)
		{
			// if we should only handle URL parsing errors
			// adjust switch value according to current situation
			if (self::$requestParsed)
			{
				// Joomla error, use Joomla handling
				$setting = Sh404sefHelperGeneral::ERROR_404_USE_JOOMLA;
			}
			else
			{
				// URL parsing error, use sh404SEF handling
				$setting = Sh404sefHelperGeneral::ERROR_404_OVERRIDE_JOOMLA;
			}
		}

		// request is considered parsed now
		self::$requestParsed = true;

		switch ($setting)
		{
			case Sh404sefHelperGeneral::ERROR_404_USE_JOOMLA:
				$this->_store404Page();

				$configItemid = Sh404sefFactory::getConfig()->shPageNotFoundItemid;
				if (!empty($configItemid))
				{
					Sh404sefHelperError::setItemid($configItemid);
				}

				// we forward the error to the original Joomla handler
				if (!empty($this->joomlaErrorHandler) && $this->joomlaErrorHandler['mode'] == 'callback' && is_callable($this->joomlaErrorHandler['options']))
				{
					call_user_func_array($this->joomlaErrorHandler['options'], array(&$error));
				}
				else
				{
					// This is required to prevent a very unhelpful white-screen-of-death
					jexit('<strong>An error just happened: </strong>' . $error->getMessage() . '<br /><small>and there was another one displaying this message (invalid error handler).</small>');
				}
				break;
			case Sh404sefHelperGeneral::ERROR_404_OVERRIDE_JOOMLA:
				// we use our own handling
				$this->_store404Page();
				Sh404sefHelperError::render404ErrorDocument($error);
				exit();
				break;
			default:
				break;
		}
	}

	protected function _store404Page($uri = null)
	{
		$uri = empty($uri) ? JUri::getInstance() : $uri;

		// optionnally log the 404 details
		$reqPath = $uri->getPath();
		$basePath = JUri::base(true);
		if (!empty($basePath) && strpos($reqPath, $basePath) === 0)
		{
			$reqPath = substr($reqPath, JString::strlen($basePath) + 1);
		}
		$storer = Sh404sefModelNotfoundstore::getInstance();
		$storer->store($reqPath, Sh404sefFactory::getConfig());
	}

	/**
	 * Redirect user, on first visit, to its own language
	 * as detected in browser
	 * Happens only on home page, as otherwise we wouldn't know
	 * on which page to redirect him/her
	 *
	 * @param JURI $uri
	 */
	protected function _languageRedirect($uri, $currentLanguageCode)
	{
		if (!$this->_canRedirectFrom($uri))
		{
			return;
		}

		$pageInfo = Sh404sefFactory::getPageInfo();

		// decide whether this is homepage request
		$path = JString::trim(
			str_replace($pageInfo->getDefaultFrontLiveSite() . '/', '', $pageInfo->currentSefUrl),
			'/'
		);
		$possibleUrls = array();
		foreach ($pageInfo->homeLinks as $language => $link)
		{
			$possibleUrls[] = Sh404sefHelperLanguage::getUrlCodeFromTag($language);
		}
		if (!empty($path) && !in_array($path, $possibleUrls))
		{
			return;
		}

		$cookieTag = JRequest::getString(JApplication::getHash('language'), null, 'cookie');

		// no cookie - try autoredirect to user language
		if (empty($cookieTag))
		{
			if (Sh404sefHelperLanguage::getLanguageFilterPluginParam('detect_browser', 1))
			{
				$userLanguage = JLanguageHelper::detectLanguage();
				if (!empty($userLanguage) && $userLanguage != $currentLanguageCode)
				{
					ShlSystem_Log::debug(
						'sh404sef',
						'User lang: ' . $userLanguage . ' langcode: ' . $currentLanguageCode
					);
					// user has a specific language, let's redirect to the home page in that language
					$target = JRoute::_($pageInfo->homeLinks[$userLanguage]);
					shRedirect($target);
				}
			}
		}
	}
}

if (version_compare(JVERSION, '3.2', 'ge'))
{
	class Sh404sefClassRouter extends Sh404sefClassRouterInternal
	{
		protected function _parseSefRoute(&$uri)
		{
			return $this->_parseSefRouteInternal($uri);
		}

		protected function _buildSefRoute(&$uri)
		{
			$this->_buildSefRouteInternal($uri);
		}
	}
}
else if (version_compare(JVERSION, '3.0', 'ge'))
{
	class Sh404sefClassRouter extends Sh404sefClassRouterInternal
	{
		protected function _parseSefRoute($uri)
		{
			return $this->_parseSefRouteInternal($uri);
		}

		protected function _buildSefRoute($uri)
		{
			$this->_buildSefRouteInternal($uri);
		}
	}
}
else
{
	class Sh404sefClassRouter extends Sh404sefClassRouterInternal
	{
		protected function _parseSefRoute(&$uri)
		{
			return $this->_parseSefRouteInternal($uri);
		}

		protected function _buildSefRoute(&$uri)
		{
			$this->_buildSefRouteInternal($uri);
		}
	}
}
