<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date        2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
	die('Direct Access to this location is not allowed.');

class Sh404sefHelperGeneral
{
	const COM_SH404SEF_ALL_DUPLICATES = 0;
	const COM_SH404SEF_ONLY_DUPLICATES = 1;
	const COM_SH404SEF_NO_DUPLICATES = 2;

	const COM_SH404SEF_ALL_ALIASES = 0;
	const COM_SH404SEF_ONLY_ALIASES = 1;
	const COM_SH404SEF_NO_ALIASES = 2;

	const COM_SH404SEF_ALL_URL_TYPES = 0;
	const COM_SH404SEF_ONLY_CUSTOM = 1;
	const COM_SH404SEF_ONLY_AUTO = 2;

	const COM_sh404SEF_URLTYPE_404 = -2;
	const COM_sh404SEF_URLTYPE_NONE = -1;
	const COM_sh404SEF_URLTYPE_AUTO = 0;
	const COM_sh404SEF_URLTYPE_CUSTOM = 1;

	const COM_SH404SEF_URLTYPE_ALIAS = 0;
	const COM_SH404SEF_URLTYPE_PAGEID = 1;

	const COM_SH404SEF_ALL_TITLE = 0;
	const COM_SH404SEF_ONLY_TITLE = 1;
	const COM_SH404SEF_NO_TITLE = 2;

	const COM_SH404SEF_ALL_DESC = 0;
	const COM_SH404SEF_ONLY_DESC = 1;
	const COM_SH404SEF_NO_DESC = 2;

	const COM_SH404SEF_HIDE_DUPLICATES_AND_404 = 0;
	const COM_SH404SEF_HIDE_DUPLICATES = 1;
	const COM_SH404SEF_HIDE_404 = 2;
	const COM_SH404SEF_HIDE_NONE = 3;

	const ERROR_404_HANDLE = 0;
	const ERROR_404_OVERRIDE_JOOMLA = 1;
	const ERROR_404_USE_JOOMLA = 2;

	const COLLAPSE_NONE = 0;
	const COLLAPSE_BY_REFERRER = 1;
	const COLLAPSE_BY_IP = 2;
	const COLLAPSE_BY_USER_AGENT = 3;

	const SHOW_ALL_REFERRERS = 0;
	const HIDE_EMPTY_REFERRERS = 1;

	const SHOW_REQUESTED_OR_NOT = 0;
	const SHOW_REQUESTED = 1;
	const SHOW_NOT_REQUESTED = 2;

	/**
	 * Builds a string based on current Joomla version
	 * Format is 'j' followed by major version
	 */
	public static function getJoomlaVersionPrefix()
	{
		// version prefix
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			return 'j3';
		}
		else
		{
			return 'j2';
		}
	}

	/**
	 * Load components
	 *
	 * @access  public
	 * @param array exclude an array of component to exclude from result
	 * @return  array
	 */
	public static function getComponentsList($exclude = array())
	{
		static $components = null;

		if (is_null($components))
		{
			$db = ShlDbHelper::getDb();

			// exclude some and ourselves
			$exclude = array_merge(
				array('com_sh404sef', 'com_joomfish', 'com_falang', 'com_joomsef', 'com_acesef', 'com_admin', 'com_cache', 'com_categories',
					'com_checkin', 'com_cpanel', 'com_installer', 'com_languages', 'com_media', 'com_menus', 'com_messages', 'com_modules',
					'com_plugins', 'com_templates', 'com_config', 'com_redirect'), $exclude);

			$where = $db->quoteName('type') . ' = ? and ' . $db->quoteName('enabled') . ' = ? and ' . $db->quoteName('element') . ' <> ? ' . ' and '
				. $db->quoteName('element') . ' not in (' . ShlDbHelper::arrayToQuotedList($exclude) . ')';
			$whereData = array('component', 1, '');
			try
			{
				$components = ShlDbHelper::selectObjectList('#__extensions', array('*'), $where, $whereData, $orderBy = array('name'), $offset = 0,
					$lines = 0, $key = 'element');
			}
			catch (Exception $e)
			{
				JError::raiseWarning('SOME_ERROR_CODE', "Error loading Components: " . $e->getMessage());
				return false;
			}
		}

		return $components;
	}

	public static function getComponentParams($forceRead = false)
	{
		static $_params = null;

		if (is_null($_params) || $forceRead)
		{
			try
			{
				$oldParams = ShlDbHelper::selectResult('#__extensions', 'params', array('element' => 'com_sh404sef', 'type' => 'component'));
				$_params = new JRegistry();
				$_params->loadString($oldParams);
			}
			catch (Exception $e)
			{
				$_params = new JRegistry();
				ShlSystem_Log::error('sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
			}
		}

		return $_params;
	}

	public static function getExtensionParams($extension, $options, $forceRead = false)
	{
		static $_params = array();

		if (!isset($_params[$extension]) || $forceRead)
		{
			try
			{
				$oldParams = ShlDbHelper::selectResult('#__extensions', 'params', $options);
				$_params[$extension] = new JRegistry();
				$_params[$extension]->loadString($oldParams);
			}
			catch (Exception $e)
			{
				$_params[$extension] = new JRegistry();
				ShlSystem_Log::error('sh404sef', '%s::%d: %s', __METHOD__, __LINE__, $e->getMessage());
			}
		}

		return $_params[$extension];
	}

	public static function saveComponentParams($params)
	{
		try
		{
			ShlDbHelper::update('#__extensions', array('params' => (string) $params), array('element' => 'com_sh404sef', 'type' => 'component'));
			return true;
		}
		catch (Exception $e)
		{
			ShlSystem_Log::error('sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
			return false;
		}
	}

	public static function getComponentUrl()
	{
		return 'administrator/components/com_sh404sef';
	}

	/**
	 * Create toolbar title for current view
	 *
	 * This one can ucstomize the class for styling
	 * plus the output can be used to
	 * simply display the title as opposed to
	 * using $mainframe to set the component
	 * title, which is not OK when used inside a modal box
	 *
	 * @param string $title text title
	 * @param string $icon the name of an image, which is used to calculate aclass name
	 * @param string $class the name of a wrapping class
	 */
	public static function makeToolbarTitle($title, $icon = 'generic.png', $class = 'header')
	{
		//strip the extension
		$icon = preg_replace('#\.[^.]*$#u', '', $icon);

		$html = "<div class=\"$class icon-48-$icon\">\n";
		$html .= "$title\n";
		$html .= "</div>\n";

		return $html;
	}

	/**
	 * Prepare an xml file content holding
	 * a standard record for returning result
	 * of an ajax request
	 *
	 * @param JView $view the view handling the request
	 */
	public static function prepareAjaxResponse($view)
	{
		// create a root node
		$base = '<?xml version="1.0" encoding="UTF-8" ?><item id="shajax-response"></item>';
		$xml = new SimpleXMLElement($base);

		$status = '_';
		$message = '_';
		$messagecode = '_';
		$taskexecuted = '_';

		// set their respective values
		$vErrors = $view->getErrors();
		$view = new stdClass();

		if (empty($vErrors))
		{
			// retrieve messagecode and task
			if (empty($view->messagecode))
			{
				$view->messagecode = 'COM_SH404SEF_OPERATION_COMPLETED';
			}
			if (empty($view->taskexecuted))
			{
				$view->taskexecuted = $taskexecuted;
			}

			// either a success or a redirect
			if (empty($view->redirectTo))
			{
				// no error
				$status = 'success';
				$msg = empty($view->message) ? JText::_('COM_SH404SEF_OPERATION_COMPLETED') : $view->message;
				$message = '<ul>' . $msg . '</ul>';
				$messagecode = 200;
			}
			else
			{
				$status = 'redirect';
				$glue = strpos($view->redirectTo, '?') === false ? '?' : '&';
				$message = $view->redirectTo . $glue . 'sh404sefMsg=' . $view->messagecode;
			}
			$taskexecuted = $view->taskexecuted;
		}
		else
		{
			$status = 'failure';
			$messageTxt = '';
			foreach ($vErrors as $error)
			{
				$messageTxt .= '<li>' . $error . '</li>';
			}
			$message = '<ul>' . $messageTxt . '</ul>';
		}

		// add children : status, message, message code, task
		$xml->addChild('status', $status);
		$xml->addChild('message', $message);
		$xml->addChild('messagecode', $messagecode);
		$xml->addChild('taskexecuted', $taskexecuted);

		// output resulting text, no need for a layout file I think
		$output = $xml->asXml();

		return $output;
	}

	/**
	 * Calculate MD5 of a set of data
	 *
	 * @param array $dataSet the data, as an array of objects or arrays
	 * @param array $columns , hold the names of the object properties to be used in calculation
	 * @param boolean $asObject if true, dataSet is an array of objects, else an array of array
	 */
	public static function getDataMD5($dataSet, $columns, $asObject = true)
	{
		$md5 = null;
		$sum = '';

		if (!empty($dataSet) && !empty($columns))
		{
			foreach ($dataSet as $record)
			{
				foreach ($columns as $column)
				{
					$sum .= $asObject ? $record->$column : $record[$column];
				}
			}
			$md5 = md5($sum);
		}

		return $md5;
	}

	/**
	 * Returns either the full set or just one
	 * header line to be used in an export file
	 * Also needed when importing, to recognize
	 * import type
	 *
	 * @param string $type the data type being imported
	 */
	public static function getExportHeaders($type = null)
	{
		static $_headers = array('aliases' => '"Nbr","Alias","Sef url","Non sef url","Type","Hits"',
			'urls' => '"Nbr","Sef url","Non sef url","Hits","Rank","Date added","Page title","Page description","Page keywords","Page language","Robots tag","Canonical","Referrer type","Src id"',
			'metas' => '"Nbr","Sef url","Non sef url","Hits","Rank","Date added","Page title","Page description","Page keywords","Page language","Robots tag","Canonical"',
			'pageids' => '"Nbr","pageId","Sef url","Non sef url","Type","Hits"',
			'view404' => '"Nbr","Sef url","Non sef url","Hits","Rank","Date added","Page title","Page description","Page keywords","Page language","Robots tag","Referrer type"'
			// legacy files
		, 'sh404sefurls' => '"id","Count","Rank","SEF URL","non-SEF URL","Date added"',
			'sh404sefmetas' => '"id","newurl","metadesc","metakey","metatitle","metalang","metarobots"');

		if (is_null($type))
		{
			return $_headers;
		}

		if (isset($_headers[$type]))
		{
			return $_headers[$type];
		}

		return false;
	}

	public static function checkIPList($ip, $ipList)
	{
		if (empty($ip) || empty($ipList))
			return false;
		foreach ($ipList as $ipInList)
		{
			if (self::checkIpRange($ip, $ipInList))
			{
				return true;
			}
		}
		return false;
	}

	public static function checkIpRange($ip, $ipExp)
	{
		if (empty($ip) || empty($ipExp))
			return false;
		$exp = '/' . str_replace('\*', '[0-9]{1,3}', preg_quote($ipExp)) . '/'; // allow * wild card
		return preg_match($exp, $ip);
	}

	public static function getUserGroups($format = 'all')
	{
		static $_groups = null;

		if (is_null($_groups))
		{
			$groups_['all'] = array();
			$groups_['id'] = array();
			$groups_['title'] = array();

			// read groups from DB
			$rawGroups = array();
			try
			{
				$rawGroups = ShlDbHelper::selectObjectList('#__usergroups', array('id', 'title'));
			}
			catch (Exception $e)
			{
				ShlSystem_Log::error('sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
			}

			// store groups by format: id&name, id only, names only
			foreach ($rawGroups as $group)
			{
				$_groups['all'][$group->id] = $group->title;
				$_groups['id'][] = $group->id;
				$_groups['title'][] = $group->title;
			}
		}

		return $_groups[$format];
	}

	public static function isInGroupList($groups, $groupsList)
	{
		if (empty($groups) || empty($groupsList))
		{
			return false;
		}

		foreach ($groups as $groupId)
		{
			if (in_array($groupId, $groupsList))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns the sh404SEF SEF url for a give non-sef url,
	 * creating it on the fly if not already in the database
	 *
	 * @param string $nonSefUrl non-sef url, starting with index.php?...
	 * @param boolean $fullyQualified if true, return a fully qualified url, including protocol and host
	 * @param boolean $xhtml
	 * @param  $ssl
	 */
	public static function getSefFromNonSef($nonSefUrl, $fullyQualified = true, $xhtml = false, $ssl = null)
	{
		if (!defined('SH404SEF_IS_RUNNING'))
		{
			return false;
		}

		$pageInfo = Sh404sefFactory::getPageInfo();

		if (empty($nonSefUrl))
		{
			return $pageInfo->getDefaultFrontLiveSite();
		}

		$newUri = new JURI($nonSefUrl);
		$originalUri = clone $newUri;

		$route = shSefRelToAbs($nonSefUrl, $shLanguageParam = '', $newUri, $originalUri);
		$route = ltrim(str_replace($pageInfo->getDefaultFrontLiveSite(), '', $route), '/');
		$route = $route == '/' ? '' : $route;

		// find path
		$nonSefVars = $newUri->getQuery($asArray = true);
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
		$newUri->setPath($path);

		$liveSite = $pageInfo->getDefaultFrontLiveSite();
		if ($fullyQualified || (int) $ssl === 1)
		{
			// remove protocol, host, etc, only keep relative-to-site part
			if ((int) $ssl === 1 && substr($liveSite, 0, 7) == 'http://')
			{
				$liveSite = str_replace('http://', 'https://', $liveSite);
			}
			$sefUrl = $liveSite . '/' . $newUri->toString();
		}
		else
		{
			$base = str_replace(array('http://', 'https://'), '', $liveSite);
			$bits = explode('/', $base);
			array_shift($bits);
			$sefUrl = '/' . trim(implode('/', $bits), '/') . '/' . $newUri->toString(array('path', 'query', 'fragment'));
		}

		if ($xhtml)
		{
			$sefUrl = htmlspecialchars($sefUrl, ENT_COMPAT, 'UTF-8');
		}

		return $sefUrl;
	}

	/**
	 * Instantiates a new component configuration model
	 * from Joomla! own com_config
	 *
	 * @param string $component name of component for which the model should be initialized
	 * @param string $path path to a folder where config xml file can be found
	 * @return ConfigModelComponent
	 */
	public static function getComConfigComponentModel($component = 'com_sh404sef', $path = '')
	{
		if (!class_exists('ConfigModelComponent'))
		{
			if (file_exists(JPATH_ROOT . '/administrator/components/com_config/model/component.php'))
			{
				// post J! 3.2
				$files = array(JPATH_ROOT . '/components/com_config/model/cms.php', JPATH_ROOT . '/components/com_config/model/form.php',
					JPATH_ROOT . '/administrator/components/com_config/model/component.php');
			}
			else
			{
				// pre J! 3.2
				$files = array(JPATH_ROOT . '/administrator/components/com_config/models/component.php');
			}
			foreach ($files as $file)
			{
				require_once($file);
			}
		}
		$comConfigModel = new ConfigModelComponent(array('ignore_request' => true));
		$state = $comConfigModel->getState();
		$state->set('component.option', $component);
		if (!empty($path))
		{
			$state->set('component.path', $path);
		}
		return $comConfigModel;
	}

	/**
	 * Creates a link to the shLib plugin page
	 * @return string
	 */
	public static function getShLibPluginLink($xhtml = true)
	{
		try
		{
			$pluginId = ShlDbHelper::selectResult('#__extensions', array('extension_id'),
				array('type' => 'plugin', 'element' => 'shlib', 'folder' => 'system'));
		}
		catch (Exception $e)
		{
			ShlSystem_Log::error('sh404sef', __CLASS__ . '/' . __METHOD__ . '/' . __LINE__ . ': ' . $e->getMessage());
		}

		$link = '';
		$pluginId = (int) $pluginId;
		if (!empty($pluginId))
		{
			$link = 'index.php?option=com_plugins&task=plugin.edit&extension_id=' . $pluginId;
		}

		if ($xhtml)
		{
			$link = htmlspecialchars($link, ENT_COMPAT, 'UTF-8');
		}

		return $link;
	}

	/**
	 * Fetch a private or protected property from an object
	 *
	 * @param string $className
	 * @param string $propertyName
	 * @param object $instance
	 * @param boolean $static
	 *
	 * @return mixed property value, or null
	 */
	public static function getProtectedProperty($className, $propertyName, $instance, $static = false)
	{
		static $_classesCache = array();
		static $_propertiesCache = array();

		if (version_compare(phpversion(), '5.3', 'ge'))
		{
			try
			{
				if (empty($_propertiesCache[$className . $propertyName]))
				{
					if (empty($_classesCache[$className]))
					{
						$_classesCache[$className] = new ReflectionClass($className);
					}
					$_propertiesCache[$className . $propertyName] = $_classesCache[$className]->getProperty($propertyName);
					$_propertiesCache[$className . $propertyName]->setAccessible(true);
				}
				$propertyValue = $static ? $_propertiesCache[$className . $propertyName]->getStaticValue($instance)
					: $_propertiesCache[$className . $propertyName]->getValue($instance);

			}
			catch (Exception $e)
			{
				ShlSystem_Log::error('sh404sef', __CLASS__ . '/' . __METHOD__ . '/' . __LINE__ . ': ' . $e->getMessage());
				$propertyValue = null;
			}
		}
		else
		{
			if (!$static)
			{
				// poor man's reflection, pre 5.3.0
				$dump = print_r($instance, true);
				$propertytag = '[' . $propertyName . ':protected]';
				$bits = explode($propertytag, $dump);
				$bit = $bits[1];
				$bits = explode('[_', $bit);
				$bit = str_replace('=>', '', $bits[0]);
				$propertyValue = trim($bit);
			}
			else
			{
				$propertyValue = null;
			}
		}

		return $propertyValue;
	}
}
