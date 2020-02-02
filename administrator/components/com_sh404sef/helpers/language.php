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
defined('_JEXEC') or die;

class Sh404sefHelperLanguage
{
	/**
	 * Figures out if a language code should be inserted
	 * into urls for default language
	 *
	 */
	public static function getInsertLangCodeInDefaultLanguage()
	{
		static $shouldInsert = null;

		if (is_null($shouldInsert))
		{
			// default Joomla value is true
			// sh404SEF default always been false
			$shouldInsert = false;

			// try load languagefilter plugin params
			$plugin = JPluginHelper::getPlugin('system', 'languagefilter');
			if (!empty($plugin))
			{
				$params = new JRegistry();
				$params->loadString($plugin->params);
				$shouldInsert = $params->get('remove_default_prefix', false);
				$shouldInsert = empty($shouldInsert);
			}
		}

		return $shouldInsert;
	}

	/**
	 * Used only on J3+
	 *
	 * @return bool
	 */
	public static function shouldShowLanguageFilterWarning()
	{
		// only if this is supposed to be a ML site
		// that would be if the not-used anymore enableMultiLingualSupport
		// was false
		$sefConfig = Sh404sefFactory::getConfig();
		if (!$sefConfig->enableMultiLingualSupport)
		{
			return false;
		}

		// or if there's only one language on the site
		$languages = JLanguageHelper::getLanguages('sef');
		if (count($languages) <= 1)
		{
			return false;
		}

		// and only if the plugin is not enabled ofc
		$plugin = JPluginHelper::getPlugin('system', 'languagefilter');
		if (!empty($plugin))
		{
			return false;
		}

		// insert message
		return true;
	}

	/**
	 * Used only by J2
	 *
	 * @return bool
	 */
	public static function getLanguageFilterWarning()
	{
		static $displayed = false;

		if (!$displayed)
		{
			$displayed = true;
			$app = JFactory::getApplication();

			// figure out whether we should display the warning
			// only on html page, and on display or info tasks
			$format = $app->input->getCmd('format', 'html');
			if ($format != 'html')
			{
				return false;
			}
			$task = $app->input->getCmd('task', 'display');
			if ($task != 'display' && $task != 'info')
			{
				return false;
			}

			return self::shouldShowLanguageFilterWarning();
		}

		return false;
	}

	/**
	 * Find a language family
	 *
	 * @param object $language a Joomla! language object
	 * @return string a 2 or 3 characters language family code
	 */
	public static function getFamily($language = null)
	{
		if (!is_object($language))
		{
			// get application db instance
			$language = JFactory::getLanguage();
		}

		$code = $language->getTag();
		$bits = explode('-', $code);
		return empty($bits[0]) ? 'en' : $bits[0];
	}

	/**
	 * Get language tag from a url language code
	 *
	 * @param string $langCode
	 * @return string
	 */
	public static function getLangTagFromUrlCode($langCode)
	{

		$languages = JLanguageHelper::getLanguages('sef');
		if (!empty($languages[$langCode]))
		{
			$urlLangTag = $languages[$langCode]->lang_code;
		}
		else
		{
			$urlLangTag = self::getDefaultLanguageTag();
		}

		return $urlLangTag;
	}

	/**
	 * Get url short language code from a full language tag
	 *
	 * @param string $langTag
	 * @param bool   $default if true, return code for default language if not found
	 * @return string
	 */
	public static function getUrlCodeFromTag($langTag, $default = true)
	{
		$languages = JLanguageHelper::getLanguages('lang_code');
		$urlLangCode = null;
		if (!empty($languages[$langTag]))
		{
			$urlLangCode = $languages[$langTag]->sef;
		}
		else
		{
			if ($default)
			{
				$urlLangTag = self::getDefaultLanguageTag();
				if (!empty($languages[$urlLangTag]))
				{
					$urlLangCode = $languages[$urlLangTag]->sef;
				}
				else
				{
					ShlSystem_Log::error('sh404sef', '%s::%d: %s', __METHOD__, __LINE__,
						' Language tag ' . $urlLangTag . ' not in installed language list');
					$urlLangCode = explode('-', $langTag);
					$urlLangCode = empty($urlLangCode) ? 'en' : $urlLangCode[0];
				}
			}
		}

		return $urlLangCode;
	}

	public static function validateSefLanguageCode($sefLangCode)
	{
		static $codes = null;

		if (!isset($codes[$sefLangCode]))
		{
			// single language sites, don't have "Content" language
			// setup usually
			if ($sefLangCode == self::getDefaultLanguageSef())
			{
				$codes[$sefLangCode] = true;
				return true;
			}

			// not default language, we now look up the
			// "Content" language setup for ML sites
			$availableLanguages = self::getInstalledLanguagesList();
			foreach ($availableLanguages as $language)
			{
				if ($sefLangCode == $language->sef)
				{
					$codes[$sefLangCode] = true;
					return true;
				}
			}
			$codes[$sefLangCode] = false;
		}

		return $codes[$sefLangCode];
	}

	/**
	 * Get the SEF URL language code for the default language
	 * based on Joomla configuration
	 *
	 * @return string
	 */
	public static function getDefaultLanguageSef()
	{
		return self::getUrlCodeFromTag(self::getDefaultLanguageTag());
	}

	/**
	 * Get all languages installed on the site, either
	 * on the front or backend, depending on the base path passed
	 *
	 * @param string $basePath
	 * @return array
	 */
	public static function getAllInstalledLanguage($basePath = JPATH_BASE)
	{
		static $_allLanguages = array();

		if (empty($_allLanguages[$basePath]))
		{
			$_allLanguages[$basePath] = JLanguage::getKnownLanguages($basePath);
		}
		return $_allLanguages[$basePath];
	}

	/**
	 * Build an array of active langauges:
	 * - on single language sites, that's the default installed language
	 * - on ML sites, its whatever is in the #__languages table
	 *
	 * @return array
	 */
	public static function getActiveLanguages()
	{
		static $_languages = null;

		if (is_null($_languages))
		{
			$isMultilingual = Sh404sefFactory::getPageInfo()->isMultilingual;
			if ($isMultilingual === false)
			{
				$_languages = array(self::getDefaultLanguageRecord());
			}
			else
			{
				if ($isMultilingual == 'joomla')
				{
					$_languages = JLanguageHelper::getLanguages();
				}
			}
		}

		return $_languages;
	}

	private static function getDefaultLanguageRecord()
	{
		static $record = null;
		if (is_null($record))
		{
			$defaultLanguage = self::getDefaultLanguageTag();
			$allLanguages = self::getAllInstalledLanguage(JPATH_ROOT);
			$record = new stdClass();
			$record->lang_code = $defaultLanguage;
			$record->sef = self::getUrlCodeFromTag($defaultLanguage);
			$record->shortcode = $record->sef;
			$record->name = empty($allLanguages[$record->lang_code]) ? 'Default' : $allLanguages[$record->lang_code];
		}

		return $record;
	}

	/**
	 * Get installed front end language list
	 *
	 * @access  private
	 * @return  array
	 */
	public static function getInstalledLanguagesList($site = true)
	{
		static $_languages = null;

		if (is_null($_languages))
		{
			$pageInfo = Sh404sefFactory::getPageInfo();
			if ($pageInfo->isMultilingual === false)
			{
				$_languages = array(self::getDefaultLanguageRecord());
			}
			else
			{
				$db = ShlDbHelper::getDb();

				// is there a languages table ?
				$_languages = array();
				$languagesTableName = $db->getPrefix() . 'languages';
				$tablesList = $db->getTableList();
				if (is_array($tablesList) && in_array($languagesTableName, $tablesList))
				{
					try
					{
						$query = 'SELECT * FROM #__languages';
						$db->setQuery($query);
						$_languages = $db->loadObjectList();
					}
					catch (Exception $e)
					{
						JError::raiseWarning('SOME_ERROR_CODE', "Error loading languages lists: " . $e->getMessage());
						ShlSystem_Log::error('sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
						return false;
					}
					// match fields name to what we need, those were changed in version 2.2 of JF
					foreach ($_languages as $key => $language)
					{
						if (empty($language->id))
						{
							$_languages[$key]->id = $language->lang_id;
						}
						if (empty($language->name))
						{
							$_languages[$key]->name = $language->title;
						}
						if (empty($language->code))
						{
							$_languages[$key]->code = $language->lang_code;
						}
						if (empty($language->shortcode))
						{
							$_languages[$key]->shortcode = $language->sef;
						}
						if (empty($language->active) && empty($language->published))
						{
							// drop this language, it is not published
							unset($_languages[$key]);
						}
					}
				}
			}
		}

		return $_languages;
	}

	/**
	 * Returns the full language tag for the site default language
	 *
	 * @return string
	 */
	public static function getDefaultLanguageTag()
	{
		if (JFactory::getApplication()->isAdmin())
		{
			return JComponentHelper::getParams('com_languages')->get('administrator', 'en-GB');
		}
		else
		{
			return JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
		}
	}

	/**
	 * Returns the full language tag for the site default language
	 *
	 * @return string
	 */
	public static function getLanguageFilterPluginParam($paramName, $default = null)
	{
		static $params = null;

		if (is_null($params))
		{
			$plugin = JPluginHelper::getPlugin('system', 'languagefilter');
			$params = new JRegistry();
			$params->loadString(empty($plugin) ? '' : $plugin->params);
		}

		return is_null($params) ? $default : $params->get($paramName, $default);
	}

	/**
	 * Sets a Jooml! language cookie, deleting existing one if any
	 *
	 * @param string $languageCode the new target language code (ie: en-GB)
	 * @param int    $cookieTime   cookie duration time, set in params or default to 0
	 */
	public static function setLanguageCookie($languageCode = null, $cookieTime = 0)
	{
		$languageCode = empty($languageCode) ? Sh404sefHelperLanguage::getDefaultLanguageTag() : $languageCode;
		// Create a cookie
		$conf = JFactory::getConfig();
		$cookieName = JApplication::getHash('language');
		$cookieDomain = $conf->get('config.cookie_domain', '');
		$cookiePath = $conf->get('config.cookie_path', '/');
		// set new cookie, both in browser and in internal application vars
		$app = JFactory::getApplication();
		$app->input->cookie->set($cookieName, $languageCode, $cookieTime, $cookiePath, $cookieDomain, false, true);
		JRequest::setVar($cookieName, $languageCode, 'cookie');
		// as Joomla languagefilter also sets this cookie disregarding the path,domain and duration parameters, we must set it twice
		if (version_compare(JVERSION, '3.3', '<'))
		{
			$app->input->cookie->set($cookieName, $languageCode);
		}
	}

}
