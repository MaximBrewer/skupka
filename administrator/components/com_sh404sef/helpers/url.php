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
if (!defined('_JEXEC'))
{
	die();
}

class Sh404sefHelperUrl
{
	const IS_UNKNOWN  = 0;
	const IS_EXTERNAL = 1;
	const IS_INTERNAL = 2;

	static $componentsRouters = array();

	static public $buildingNonSef = '';
	static public $buildingSef = '';

	public static function buildUrl($elements, $option = 'com_sh404sef')
	{
		$url = 'index.php?option=' . $option;

		if (is_array($elements) && !empty($elements))
		{
			foreach ($elements as $key => $value)
			{
				$url .= '&' . $key . '=' . $value;
			}
		}

		return $url;
	}

	public static function stripTrackingVarsFromNonSef($url)
	{
		$trackingVars = self::_getTrackingVars();
		return self::stripVarsFromNonSef($url, $trackingVars);
	}

	protected static function _getTrackingVars()
	{
		$trackingVars = Sh404sefFactory::getPConfig()->trackingVars;

		return $trackingVars;
	}

	public static function stripVarsFromNonSef($url, $vars = array())
	{
		if (!empty($vars))
		{
			foreach ($vars as $var)
			{
				$url = Sh404sefHelperUrl::clearUrlVar($url, $var);
			}
		}

		return $url;
	}

	public static function getCurrentNonSef()
	{
		// remove Google tracking vars, would prevent us to find the correct meta tags
		$nonSef = self::stripTrackingVarsFromNonSef(Sh404sefFactory::getPageInfo()->currentNonSefUrl);

		// Virtuemart hack
		// VM 2.x actually sets JRequest variables to communicate between view.html.php and layouts!
		// so the current non-sef url is modified (showall=1 is added) which prevent
		// all meta data associated with this non-sef to be properly retrieved
		$isVm = self::getUrlVar($nonSef, 'option', null) == 'com_virtuemart';
		$isProductDetails = $isVm && self::getUrlVar($nonSef, 'view', null) == 'productdetails';
		if ($isProductDetails)
		{
			$nonSef = str_replace('&showall=1', '', $nonSef);
		}

		// normalize, set variables in alpha order
		return self::sortUrl($nonSef);
	}

	public static function stripTrackingVarsFromArray($urlArray)
	{
		$trackingVars = self::_getTrackingVars();
		if (!empty($trackingVars))
		{
			foreach ($trackingVars as $var)
			{
				if (isset($urlArray[$var]))
				{
					unset($urlArray[$var]);
				}
			}
		}

		return $urlArray;
	}

	/** works only with non-sef urls, starting with index.php? */
	public static function clearUrlVar($string, $var)
	{
		$string = str_replace('index.php?', '&', $string);
		$cleaned = ShlSystem_Strings::pr('/(&|\?)' . preg_quote($var, '/') . '=[^&]*/iu', '', $string);
		$cleaned = ltrim($cleaned, '&');
		return empty($cleaned) || $cleaned == 'index.php' ? 'index.php' : 'index.php?' . $cleaned;
	}

	public static function stripTrackingVarsFromSef($url)
	{
		// do we have query vars?
		$parts = explode('?', $url);
		if (empty($parts[1]))
		{
			// no variable parts, return identical
			return $url;
		}

		$trackingVars = self::_getTrackingVars();
		$cleaned = self::stripVarsFromNonSef('?' . $parts[1], $trackingVars);

		// rebuild and return
		$cleaned = JString::ltrim($cleaned, '?&');
		$cleaned = $parts[0] . (empty($cleaned) ? '' : '?' . $cleaned);

		return $cleaned;
	}

	public static function extractTrackingVarsFromNonSef($url, &$existingVars, $keepThem = false)
	{
		$trackingVars = self::_getTrackingVars();
		foreach ($trackingVars as $var)
		{
			// collect existing value, if any
			$value = self::getUrlVar($url, $var, null);
			if (!is_null($value))
			{
				// store extracted value into passed array
				$existingVars[$var] = $value;
			}
			// still remove var from url
			if (!$keepThem)
			{
				$url = Sh404sefHelperUrl::clearUrlVar($url, $var);
			}
		}
		return $url;
	}

	public static function getUrlVar($string, $var, $default = '')
	{
		static $_parsed = array();

		if (empty($_parsed[$string]))
		{
			if (strpos($string, 'index.php?') === 0)
			{
				$string = substr($string, 10);
			}
			$string = str_replace('&amp;', '&', $string); // normalize
			$string = str_replace('&amp;', '&', $string); // normalize #2
			$vars = array();
			parse_str($string, $vars);
			$_parsed[$string] = $vars;
		}

		$value = isset($_parsed[$string]) && isset($_parsed[$string][$var]) ? $_parsed[$string][$var] : $default;

		return $value;
	}

	public static function setUrlVar($string, $var, $value, $canBeEmpty = false)
	{
		if (empty($string) || empty($var))
		{
			return $string;
		}
		if (!$canBeEmpty && empty($value))
		{
			return $string;
		}
		$string = str_replace('&amp;', '&', $string); // normalize
		$exp = '/(&|\?)' . preg_quote($var, '/') . '=[^&]*/iu';
		$result = preg_match($exp, $string);
		if ($result) // var already in URL
		{
			$result = preg_replace($exp, '$1' . $var . '=' . $value, $string);
		}
		else
		{ // var does not exist in URL
			$result = $string . (strpos($string, '?') !== false ? '&' : '?') . $var . '=' . $value;
			$result = Sh404sefHelperUrl::sortUrl($result);
		}
		return $result;
	}

	/**
	 * Sort query key/value pairs in alphabetical
	 * increasing order
	 *
	 * @param string $string the non-sef url, starting with index.php?
	 * @return string
	 */
	public static function sortURL($nonSef)
	{
		static $_sorted = array();

		if (empty($_sorted[$nonSef]))
		{
			// URL must be like : index.php?param2=xxx&option=com_ccccc&param1=zzz
			if ((substr($nonSef, 0, 10) !== 'index.php?'))
			{
				return $nonSef;
			}

			$_sorted[$nonSef] = '';

			// URL returned will be ! index.php?option=com_ccccc&param1=zzz&param2=xxx
			$st = str_replace('&amp;', '&', $nonSef);
			$st = str_replace('index.php', '', $st);
			$st = str_replace('?', '', $st);
			parse_str($st, $shTmpVars);
			$shVars = self::deepEncode($shTmpVars);
			if (count($shVars) > 0)
			{
				ksort($shVars); // sort URL array
				$shNewString = '';
				$_sorted[$nonSef] = 'index.php?';
				foreach ($shVars as $key => $value)
				{
					if (strtolower($key) != 'option')
					{
						// option is always first parameter
						if (is_array($value))
						{
							foreach ($value as $k => $v)
							{
								// fix for arrays, thanks doorknob
								$shNewString .= '&' . $key . '[' . $k . ']=' . str_replace(array('%2F', '%2f'), '/', $v);
							}
						}
						else
						{
							$shNewString .= '&' . $key . '=' . str_replace(array('%2F', '%2f'), '/', $value);
						}
					}
					else
					{
						$_sorted[$nonSef] .= $key . '=' . $value;
					}
				}
				$_sorted[$nonSef] .= $_sorted[$nonSef] == 'index.php?' ? JString::ltrim($shNewString, '&') : $shNewString;
			}
		}
		return $_sorted[$nonSef];
	}

	/**
	 * Url encoding with 2-levels arrays
	 *
	 * @param mixed $data
	 * @return mixed
	 */
	public static function deepEncode($data)
	{
		if (is_array($data))
		{
			foreach ($data as $key => $element)
			{
				$data[$key] = self::deepEncode($element);
			}
			return $data;
		}
		else
		{
			return urlencode($data);
		}
	}

	/**
	 * Get Language tag from url code found in a url
	 *
	 */
	public static function getUrlLang($string)
	{
		$matches = array();
		$string = str_replace('&amp;', '&', $string); // normalize
		$result = preg_match('/(&|\?)lang=[^&]*/i', $string, $matches);
		if (!empty($matches))
		{
			$result = JString::trim($matches[0], '&?');
			$result = str_replace('lang=', '', $result);
			return Sh404sefHelperLanguage::getLangTagFromUrlCode($result);
		}
		return '';
	}

	public static function buildUrlWithRouterphp(&$vars, $option)
	{
		$componentName = substr($option, 4);

		// search for proxy functions first
		// so as to be pre-3.3 compatible
		$functionName = ucfirst($componentName) . 'BuildRoute';
		$fileName = JPATH_ROOT . '/components/' . $option . '/router.php';
		if (!function_exists($functionName) && file_exists($fileName))
		{
			include_once $fileName;
		}

		if (function_exists($functionName))
		{
			$segments = $functionName($vars);
			return $segments;
		}

		// new API, J!3.3+
		if (version_compare(JVERSION, '3.3', 'ge'))
		{
			// no function, try class
			if (empty(self::$componentsRouters[$componentName]))
			{
				$className = $componentName . 'Router';
				if (!class_exists($className))
				{
					// Use the custom routing handler if it exists
					if (file_exists($fileName))
					{
						require_once $fileName;
					}
				}
				if (class_exists($className))
				{
					$reflection = new ReflectionClass($className);
					if (in_array('JComponentRouter', $reflection->getInterfaceNames()))
					{
						self::$componentsRouters[$componentName] = new $className();
					}
				}
			}
			if (!empty(self::$componentsRouters[$componentName]))
			{
				$segments = self::$componentsRouters[$componentName]->build($vars);
				return $segments;
			}
		}

		return array();
	}

	/**
	 * Encode route segments
	 *
	 * @param   array $segments An array of route segments
	 *
	 * @return  array  Array of encoded route segments
	 *
	 */
	public static function encodeSegments($segments)
	{
		$total = count($segments);
		for ($i = 0; $i < $total; $i++)
		{
			$segments[$i] = str_replace(':', '-', $segments[$i]);
		}

		return $segments;
	}

	/**
	 * Extract original url used to build a JUri object
	 * as it's often modified by Joomla, though needed later
	 *
	 * @param JUri $uri
	 * @return string original url stored in protected property
	 */
	public static function getOriginalUrlFromUri($uri)
	{
		if (version_compare(JVERSION, '3.4', 'ge'))
		{
			// things changed in 3.4, the uri property doesnt't have the full url
			$originalUrl = Sh404sefHelperGeneral::getProtectedProperty('Juri', 'uri', $uri);
			if ($originalUrl == 'index.php')
			{
				$query = $uri->getQuery();
				$originalUrl = empty($query) ? $originalUrl : $originalUrl . '?' . $query;
			}
		}
		else
		{
			$propertyName = version_compare(JVERSION, '3.0', 'ge') ? 'uri' : '_uri';
			$originalUrl = Sh404sefHelperGeneral::getProtectedProperty('Juri', $propertyName, $uri);
		}

		return $originalUrl;
	}

	/**
	 * Extract original path used to build a JUri object
	 * as it's often modified by Joomla, though needed later
	 *
	 * @param JUri $uri
	 * @parma string $base the base url for the uri, usually JURI:: base
	 * @return string original path built from stored in protected property
	 */
	public static function getOriginalPathFromUri($uri, $base)
	{
		$propertyName = version_compare(JVERSION, '3.0', 'ge') ? 'uri' : '_uri';
		$originalUrl = Sh404sefHelperGeneral::getProtectedProperty('Juri', $propertyName, $uri);
		$originalPath = substr_replace(urldecode($originalUrl), '', 0, strlen(JUri::base()));
		$pathBits = explode('?', $originalPath);
		$originalPath = $pathBits[0];

		return $originalPath;
	}

	public static function isNonSefHomepage($nonSef = '')
	{
		static $_isHome = array();

		$nonSef = empty($nonSef) ? self::getCurrentNonSef() : $nonSef;

		if (!isset($_isHome[$nonSef]))
		{
			$_isHome[$nonSef] = $nonSef == self::sortUrl(self::cleanUpAnchor(Sh404sefFactory::getPageInfo()->homeLink));
		}

		return $_isHome[$nonSef];
	}

	public static function isHomepage($uri = null)
	{
		$uri = empty($uri) ? JFactory::getURI() : $uri;

		$path = trim($uri->getPath(), '/');
		$base = trim($uri->base(true), '/');

		$realPath = str_replace($base, '', $path);
		if (!empty($realPath))
		{
			return false;
		}

		// do not consider home if we have /?lang=xx
		if (!Sh404sefFactory::getPageInfo()->isMultilingual)
		{
			return true;
		}

		$lang = $uri->getVar('lang');
		if (empty($lang))
		{
			return true;
		}

		if ($lang == Sh404sefHelperLanguage::getDefaultLanguageSef())
		{
			return true;
		}

		return false;
	}

	public static function routedToAbs($routedUrl)
	{
		static $basePath = null;
		static $baseFull = null;

		if (is_null($basePath))
		{
			$basePath = Juri::base(true);
			$baseFull = JUri::base(false);
		}

		$absoluteUrl = JString::trim($baseFull, '/') . str_replace($basePath, '', $routedUrl);

		return $absoluteUrl;
	}

	public static function cleanUpAnchor($string)
	{
		$bits = explode('#', $string);
		return $bits[0];
	}

	public static function storeUrlSourceData($nonsef, $sef)
	{
		self::$buildingNonSef = $nonsef;
		self::$buildingSef = $sef;
	}

	public static function storeUrlSource($nonsef = null, $sef = null, $currentNonSef = null, $currentSef = null, $trace = null)
	{
		if (Sh404sefFactory::getConfig()->logUrlsSource)
		{
			$data = array();
			$data['url'] = empty($nonsef) ? self::$buildingNonSef : $nonsef;
			$data['url'] = self::sortURL($data['url']);
			$data['routed_url'] = empty($sef) ? self::$buildingSef : $sef;
			$pageInfo = Sh404sefFactory::getPageInfo();
			$data['source_url'] = is_null($currentNonSef) ? $pageInfo->currentNonSefUrl : $currentNonSef;
			$data['source_url'] = self::sortURL($data['source_url']);

			// have a record for same sef/non-sef pair created on same page?
			$found = ShlDbHelper::count('#__sh404sef_urls_src', 'id', array(
				'url'        => $data['url'],
				'routed_url' => $data['routed_url'],
				'source_url' => $data['source_url']
			));

			if (empty($found))
			{
				$data['source_routed_url'] = is_null($currentSef) ? str_replace($pageInfo->getDefaultFrontLiveSite(), '', $pageInfo->currentSefUrl) : $currentSef;
				if (is_null($trace))
				{
					$e = new Exception;
					$trace = $e->getTraceAsString();
				}
				$data['trace'] = str_replace(JPATH_ROOT, '', $trace);
				$data['datetime'] = ShlSystem_Date::getSiteNow();

				ShlDbHelper::insert('#__sh404sef_urls_src', $data);
			}
		}
	}
}
