<?php

use JchOptimize\JS_Optimize;
use JchOptimize\CSS_Optimize;
use JchOptimize\HTML_Optimize;

/**
 * JCH Optimize - Aggregate and minify external resources for optmized downloads
 * 
 * @author Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright Copyright (c) 2010 Samuel Marshall
 * @license GNU/GPLv3, See LICENSE file
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */
defined('_JCH_EXEC') or die('Restricted access');

class JchOptimizeHelperBase
{

        /**
         * 
         */
        public static function cookieLessDomain($params, $path, $orig_path, $domains_only=false)
        {
                return $domains_only ? array() : $orig_path;
        }

	public static function addHttp2Push($url, $type)
	{
		return $url;
	}
}

/**
 * Some helper functions
 * 
 */
class JchOptimizeHelper extends JchOptimizeHelperBase
{
	public static $preloads = array();

        /**
         * Checks if file (can be external) exists
         * 
         * @param type $sPath
         * @return boolean
         */
        public static function fileExists($sPath)
        {
                if ((strpos($sPath, 'http') === 0))
                {
                        $sFileHeaders = @get_headers($sPath);

                        return ($sFileHeaders !== FALSE && strpos($sFileHeaders[0], '404') === FALSE);
                }
                else
                {
                        return file_exists($sPath);
                }
        }

        /**
         * 
         * @return boolean
         */
        public static function isMsieLT10()
        {
                $browser = JchOptimizeBrowser::getInstance();

                return ($browser->getBrowser() == 'IE' && $browser->getVersion() < 10);
        }

        /**
         * 
         * @param type $string
         * @return type
         */
        public static function cleanReplacement($string)
        {
                return strtr($string, array('\\' => '\\\\', '$' => '\$'));
        }
        
        /**
         * Get local path of file from the url if internal
         * If external or php file, the url is returned
         *
         * @param string  $sUrl  Url of file
         * @return string       File path
         */
        public static function getFilePath($sUrl)
        {
                $sUriPath = JchPlatformUri::base(TRUE);

                $oUri = clone JchPlatformUri::getInstance();
                $oUrl = clone JchPlatformUri::getInstance(html_entity_decode($sUrl));

                //Use absolute file path if file is internal and a static file
                if (JchOptimizeUrl::isInternal($sUrl) && !JchOptimizeUrl::requiresHttpProtocol($sUrl))
                {
                        return JchPlatformPaths::absolutePath(preg_replace('#^' . preg_quote($sUriPath, '#') . '#', '', $oUrl->getPath()));
                }
                else
                {
                        $scheme = $oUrl->getScheme();

                        if (empty($scheme))
                        {
                                $oUrl->setScheme($oUri->getScheme());
                        }

                        $host = $oUrl->getHost();

                        if (empty($host))
                        {
                                $oUrl->setHost($oUri->getHost());
                        }

                        $path = $oUrl->getPath();

                        if (!empty($path))
                        {
                                if (substr($path, 0, 1) != '/')
                                {
                                        $oUrl->setPath($sUriPath . '/' . $path);
                                }
                        }

                        $sUrl = $oUrl->toString();

                        $query = $oUrl->getQuery();

                        if (!empty($query))
                        {
                                parse_str($query, $args);

                                $sUrl = str_replace($query, http_build_query($args, '', '&'), $sUrl);
                        }

                        return $sUrl;
                }
        }

        /**
         * 
         * @param type $sUrl
         * @return type
         */
        public static function parseUrl($sUrl)
        {
                preg_match('#^(?:([a-z][a-z0-9+.-]*+):(?=//))?(?://(?:(?:([^:@/]*+)(?::([^@/]*+))?@)?([^:/]*+)?(?::([^/]*+))?)?(?=/))?'
                        . '((?:/|^)[^?\#\n]*+)(?:\?([^\#\n]*+))?(?:\#(.*+))?$#i', $sUrl, $m);

                $parts = array();

                $parts['scheme']   = !empty($m[1]) ? $m[1] : null;
                $parts['user']     = !empty($m[2]) ? $m[2] : null;
                $parts['pass']     = !empty($m[3]) ? $m[3] : null;
                $parts['host']     = !empty($m[4]) ? $m[4] : null;
                $parts['port']     = !empty($m[5]) ? $m[5] : null;
                $parts['path']     = !empty($m[6]) ? $m[6] : '';
                $parts['query']    = !empty($m[7]) ? $m[7] : null;
                $parts['fragment'] = !empty($m[8]) ? $m[8] : null;

                return $parts;
        }

        /**
         * Gets the name of the current Editor
         * 
         * @staticvar string $sEditor
         * @return string
         */
        public static function getEditorName()
        {
                static $sEditor;

                if (!isset($sEditor))
                {
                        $sEditor = JchPlatformUtility::getEditorName();
                }

                return $sEditor;
        }


        /**
         * 
         * @staticvar string $sContents
         * @return boolean
         */
        public static function checkModRewriteEnabled($params)
        {
                JCH_DEBUG ? JchPlatformProfiler::start('CheckModRewriteEnabled') : null;

                $oFileRetriever = JchOptimizeFileRetriever::getInstance();

                if (!$oFileRetriever->isHttpAdapterAvailable())
                {
                        $params->set('htaccess', 0);
                }
                else
                {
                        $oUri  = JchPlatformUri::getInstance();
                        $sUrl  = $oUri->toString(array('scheme', 'user', 'pass', 'host', 'port')) . JchPlatformPaths::assetPath(TRUE);
                        $sUrl2 = JchPlatformPaths::rewriteBase() . 'test_mod_rewrite';

                        try
                        {
                                $sContents = $oFileRetriever->getFileContents($sUrl . $sUrl2);

                                if ($sContents == 'TRUE')
                                {
                                        $params->set('htaccess', 1);
                                }
                                else
                                {
                                        $sContents2 = $oFileRetriever->getFileContents($sUrl . '3' . $sUrl2);

                                        if ($sContents2 == 'TRUE')
                                        {
                                                $params->set('htaccess', 3);
                                        }
                                        else
                                        {
                                                $params->set('htaccess', 0);
                                        }
                                }
                        }
                        catch (Exception $e)
                        {
                                $params->set('htaccess', 0);
                        }
                }


                JchPlatformPlugin::saveSettings($params);

                JCH_DEBUG ? JchPlatformProfiler::stop('CheckModRewriteEnabled', TRUE) : null;
        }

        /**
         * 
         * @param type $aArray
         * @param type $sString
         * @return boolean
         */
        public static function findExcludes($aArray, $sString, $sType = '')
        {
                foreach ($aArray as $sValue)
                {
                        if ($sType == 'js')
                        {
                                $sString = JS_Optimize::optimize($sString);
                        }
                        elseif ($sType == 'css')
                        {
                                $sString = CSS_Optimize::optimize($sString);
                        }

                        if ($sValue && strpos(htmlspecialchars_decode($sString), $sValue) !== FALSE)
                        {
                                return TRUE;
                        }
                }

                return FALSE;
        }

        /**
         * 
         * @return type
         */
        public static function getBaseFolder()
        {
                return JchPlatformUri::base(true) . '/';
        }

        /**
         * 
         * @param string $search
         * @param string $replace
         * @param string $subject
         * @return type
         */
        public static function strReplace($search, $replace, $subject)
        {
                return str_replace(self::cleanPath($search), $replace, self::cleanPath($subject));
        }

        /**
         * 
         * @param type $str
         * @return type
         */
        public static function cleanPath($str)
        {
                return str_replace(array('\\\\', '\\'), '/', $str);
        }

        /**
         * Determines if document is of html5 doctype
         * 
         * @return boolean	True if doctype is html5
         */
        public static function isHtml5($sHtml)
        {
                return (bool) preg_match('#^<!DOCTYPE html>#i', trim($sHtml));
        }

        /**
         * Determine if document is of XHTML doctype
         * 
         * @return boolean
         */
        public static function isXhtml($sHtml)
        {
                return (bool) preg_match('#^\s*+(?:<!DOCTYPE(?=[^>]+XHTML)|<\?xml.*?\?>)#i', trim($sHtml));
        }

        /**
         * If parameter is set will minify HTML before sending to browser; 
         * Inline CSS and JS will also be minified if respective parameters are set
         * 
         * @return string                       Optimized HTML
         * @throws Exception
         */
        public static function minifyHtml($sHtml, $oParams)
        {
                JCH_DEBUG ? JchPlatformProfiler::start('MinifyHtml') : null;


                if ($oParams->get('html_minify', 0))
                {
                        $aOptions = array();

                        if ($oParams->get('css_minify', 0))
                        {
                                $aOptions['cssMinifier'] = array('JchOptimize\CSS_Optimize', 'optimize');
                        }

                        if ($oParams->get('js_minify', 0))
                        {
                                $aOptions['jsMinifier'] = array('JchOptimize\JS_Optimize', 'optimize');
                        }

			$aOptions['jsonMinifier'] = array('JchOptimize\JSON_Optimize', 'optimize');
                        $aOptions['minifyLevel'] = $oParams->get('html_minify_level', 2);
                        $aOptions['isXhtml']     = self::isXhtml($sHtml);
                        $aOptions['isHtml5']     = self::isHtml5($sHtml);

                        $sHtmlMin = HTML_Optimize::optimize($sHtml, $aOptions);

                        if ($sHtmlMin == '')
                        {
                                JchOptimizeLogger::log('Error while minifying HTML', $oParams);

                                $sHtmlMin = $sHtml;
                        }

                        $sHtml = $sHtmlMin;

                        JCH_DEBUG ? JchPlatformProfiler::stop('MinifyHtml', TRUE) : null;
                }

                return $sHtml;
        }

        /**
         * Splits a string into an array using any regular delimiter or whitespace
         *
         * @param string  $sString   Delimited string of components
         * @return array            An array of the components
         */
        public static function getArray($sString)
        {
                if (is_array($sString))
                {
                        $aArray = $sString;
                }
                else
                {
                        $aArray = explode(',', trim($sString));
                }

                $aArray = array_map(function($sValue)
                {
                        return trim($sValue);
                }, $aArray);

                return array_filter($aArray);
        }

        /**
         * 
         * @param type $url
         * @param array $params
         */
        public static function postAsync($url, $params, array $posts)
        {
                foreach ($posts as $key => &$val)
                {
                        if (is_array($val))
                        {
                                $val = implode(',', $val);
                        }

                        $post_params[] = $key . '=' . urlencode($val);
                }

                $post_string = implode('&', $post_params);

                $parts = JchOptimizeHelper::parseUrl($url);

                if (isset($parts['scheme']) && ($parts['scheme'] == 'https'))
                {
                        $protocol     = 'ssl://';
                        $default_port = 443;
                }
                else
                {
                        $protocol     = '';
                        $default_port = 80;
                }

                $fp = @fsockopen($protocol . $parts['host'], isset($parts['port']) ? $parts['port'] : $default_port, $errno, $errstr, 1);

                if (!$fp)
                {
                        JchOptimizeLogger::log($errno . ': ' . $errstr, $params);
			JchOptimizeLogger::debug($errno . ': ' . $errstr, 'JCH_post-error');
                }
                else
                {
                        $out = "POST " . $parts['path'] . '?' . $parts['query'] . " HTTP/1.1\r\n";
                        $out.= "Host: " . $parts['host'] . "\r\n";
                        $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
                        $out.= "Content-Length: " . strlen($post_string) . "\r\n";
                        $out.= "Connection: Close\r\n\r\n";

                        if (isset($post_string))
                        {
                                $out.= $post_string;
                        }

                        fwrite($fp, $out);
                        fclose($fp);
			JchOptimizeLogger::debug($out, 'JCH_post');
                }
        }

        /**
         * 
         * @param type $sHtml
         */
        public static function validateHtml($sHtml)
        {
                return preg_match('#^(?>(?><?[^<]*+)*?<html(?><?[^<]*+)*?<head(?><?[^<]*+)*?</head\s*+>)(?><?[^<]*+)*?'
                        . '<body.*</body\s*+>(?><?[^<]*+)*?</html\s*+>#is', $sHtml);
        }

        /**
         * 
         * @param type $image
         * @return type
         */
        public static function prepareImageUrl($image)
        {
                return array('path' => JchPlatformUtility::encrypt($image));
        }

        /**
         * 
         * @param JchPlatformSettings $params
         */
        public static function clearHiddenValues(JchPlatformSettings $params)
        {
                $params->set('hidden_containsgf', '');
                JchPlatformPlugin::saveSettings($params);
        }
	
##<procode>##
        /**
         * 
         */

        public static function cookieLessDomain($params, $path, $orig_path, $domains_only=false, $reset=false)
        {
		//If feature disabled just return the path if present
                if (!$params->get('pro_cookielessdomain_enable', '0') && !$domains_only)
                {
                        return parent::cookieLessDomain($params, $path, $orig_path, $domains_only);
                }

		//Cache processed files to ensure the same file isn't placed on a different domain
		//if it occurs on the page twice
                static $aDomain    = array();
                static $aFilePaths = array();

		//reset $aFilePaths for unit testing
		if ($reset)
		{
			foreach ($aFilePaths as $key => $value)
			{
				unset($aFilePaths[$key]);
			}
			
			foreach ($aDomain as $key => $value)
			{
				unset($aDomain[$key]);
			}

			return false;
		}

                if (empty($aDomain))
                {
			switch ($params->get('pro_cdn_scheme', '0'))
			{
				case '1':
					$scheme = 'http:';
					break;
				case '2':
					$scheme = 'https:';
					break;
				case '0':
				default:
					$scheme = '';
					break;
			}
			
			$aDefaultFiles = self::getStaticFiles();

			if (trim($params->get('pro_cookielessdomain', '')) != '')
			{
				$domain1 = $params->get('pro_cookielessdomain');
				$staticfiles1 = implode('|', array_merge($params->get('pro_staticfiles', $aDefaultFiles), $params->get('pro_customcdnextensions', array()))); 

				$aDomain[$scheme . self::prepareDomain($domain1)] = $staticfiles1;
			}

			if (trim($params->get('pro_cookielessdomain_2', '')) != '')
			{
				$domain2 = $params->get('pro_cookielessdomain_2');
				$staticfiles2 = implode('|', $params->get('pro_staticfiles_2', $aDefaultFiles)); 

                                $aDomain[$scheme . self::prepareDomain($domain2)] = $staticfiles2;
                        }

                        if (trim($params->get('pro_cookielessdomain_3', '')) != '')
                        {
				$domain3 = $params->get('pro_cookielessdomain_3');
				$staticfiles3 = implode('|', $params->get('pro_staticfiles_3', $aDefaultFiles)); 

                                $aDomain[$scheme . self::prepareDomain($domain3)] = $staticfiles3;
                        }
                }

		//Sprite Generator needs this to remove CDN domains from images to create sprite
		if ($domains_only)
		{
			return $aDomain;
		}

		//if no domain is configured abort
                if (empty($aDomain))
                {
                        return parent::cookieLessDomain($params, $path, $orig_path);
                }

		//If we haven't matched a cdn domain to this file yet then find one.
		if (!isset($aFilePaths[$path]))
		{
			$aFilePaths[$path] = self::selectDomain($aDomain, $path);
		}

		if ($aFilePaths[$path] === false)
		{
			return $orig_path;
		}

		return $aFilePaths[$path];
        }

        /**
         * 
         * @param type $domain
         * @return type
         */
        private static function prepareDomain($domain)
        {

                return '//' . preg_replace('#^(?:https?:)?//|/$#i', '', trim($domain));
        }

        /**
         * 
         * @staticvar int $iIndex
         * @param type $aDomain
         * @return type
	 */
        private static function selectDomain(&$aDomain, $sPath)
        {
		//If no domain is matched to a configured file type then we'll just return the file
		$sCdnUrl = false;

		for ($i=0; count($aDomain) > $i; $i++) 
		{
			$sStaticFiles = current($aDomain);
			$sDomain = key($aDomain);
			next($aDomain);

			if(current($aDomain) === false)
			{
				reset($aDomain);
			}

			if (preg_match('#\.(?>' . $sStaticFiles . ')#i', $sPath))
			{
				//Prepend the cdn domain to the file path if a match is found.
				$sCdnUrl = $sDomain . $sPath;

				break;
			}
		}

                return $sCdnUrl;
        }

	/**
	 * Returns array of default static files to load from CDN
	 *
	 *
	 * @return array $aStaticFiles Array of file type extensions
	 */
	public static function getStaticFiles()
	{
		$aStaticFiles = array('css', 'js', 'jpe?g', 'gif', 'png', 'ico', 'bmp', 'pdf', 'webp', 'svg');

		return $aStaticFiles;
	}

	/**
	 * Returns an array of file types that will be loaded by CDN
	 *
	 * @return array $aCdnFileTypes Array of file type extensions
	 */
	public static function getCdnFileTypes($params)
	{
		$aCdnFileTypes = null;

		if (is_null($aCdnFileTypes))
		{
			$aCdnFileTypes = array();
			$aDomains = JchOptimizeHelper::cookieLessDomain($params, '', '', true);

			if (!empty($aDomains))
			{
				foreach($aDomains as $cdn_file_types)
				{
					$aCdnFileTypes = array_merge($aCdnFileTypes, explode('|', $cdn_file_types));
				}

				$aCdnFileTypes = array_unique($aCdnFileTypes);
			}
		}

		return $aCdnFileTypes;
	}

	public static function addHttp2Push($url, $type, $deferred=false)
	{
		if ($url == '' || strpos($url, 'data:image') === 0)
		{
			return false;
		}

		static $bAlreadyCached = null;

		$params = JchPlatformPlugin::getPluginParams();

		//If http2 is not enabled or file is deferred when exclude deferred is set, return
		if (!$params->get('pro_http2_push_enable', '0') 
		|| ($params->get('pro_http2_exclude_deferred', '1') && $deferred))
		{
			return parent::addHttp2Push($url, $type);
		}

		if ($params->get('pro_cookielessdomain_enable', '0'))
		{
			static $sCdnFileTypesRegex = '';

			if (empty($sCdnFileTypesRegex))
			{
				$sCdnFileTypesRegex = implode('|', self::getCdnFileTypes($params));
			}

			//If this file type will be loaded by CDN don't push
			if ($sCdnFileTypesRegex != '' && preg_match('#\.(?>' . $sCdnFileTypesRegex . ')#i', $url))
			{
				return parent::addHttp2Push($url, $type);
			}
		}

		if ($type == 'js')
		{
			$type = 'script';
		}

		if ($type == 'css')
		{
			$type = 'style';
		}

		if (!in_array($type, $params->get('pro_http2_file_types', array('style', 'script', 'font', 'image'))))
		{
			return parent::addHttp2Push($url, $type);
		}

		//Only add internal files to preload
		if (JchOptimizeUrl::isInternal($url))
		{
			self::addToPreload($url, $type);

			return true;
		}
		else
		{ 
			return false;
		}
	}

	private static function addToPreload($url, $type)
	{
		$preload = "<{$url}>; rel=preload; as={$type}";

		if($type == 'font')
		{
			$preload .= '; crossorigin';

			if (preg_match("#\.\K\w++(?=$|[\#?])#", $url, $m) == '1')
			{

				switch ($m[0])
				{
				case 'woff2':
					$preload .= '; type="font/woff2"';
					break;
				case 'woff':
					$preload .= '; type="font/woff"';
					break;
				case 'eot':
					$preload .= '; type="application/vnd.ms-fontobject"';
					break;
				case 'otf':
					$preload .= '; type="font/otf"';
					break;
				case 'ttf':
					$preload .= '; type="font/ttf"';
					break;
				case 'svg':
					$preload .= '; type="image/svg+xml"';
					break;
				default:
					break;
				}
			}

		}


		if (!in_array($preload, self::$preloads))
		{
			self::$preloads[] = $preload;
		}
	}

		
##</procode>##
}
