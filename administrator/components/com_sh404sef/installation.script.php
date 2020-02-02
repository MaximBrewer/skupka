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

/**
 * Installation/Uninstallation script
 *
 */
class Com_Sh404sefInstallerScript
{
	const MIN_JOOMLA_VERSION = '3.4.2';
	const MAX_JOOMLA_VERSION = '4';

	const MIN_SHLIB_VERSION = '0.3.0';
	const MAX_SHLIB_VERSION = '';

	private $_siteId               = '';
	private $_preserveConfigFolder = '';
	private $_errorPageCatId       = 0;
	private $_shlibVersion         = '';
	private $_skipInstall          = array();

	public function preflight($route, $installer)
	{
		if ($route == 'install' || $route == 'update')
		{
			// check Joomla! version
			if (version_compare(JVERSION, self::MIN_JOOMLA_VERSION, '<') || version_compare(JVERSION, self::MAX_JOOMLA_VERSION, 'ge'))
			{
				JFactory::getApplication()
				        ->enqueueMessage(
					        sprintf(
						        'sh404SEF requires Joomla! version between %s and %s (you are using %s). Aborting installation',
						        self::MIN_JOOMLA_VERSION, self::MAX_JOOMLA_VERSION, JVERSION
					        ), 'error'
				        );

				return false;
			}

			// make sure resource manager is available, we'll need it during plugins installs
			if (!class_exists('ShlSystem_Resourcemanager'))
			{
				require_once $installer->getParent()->getPath('source') . '/admin/plugins/system/shlib/shl_packages/system/resourcemanager.php';
			}

			// check authorization to install for shared resources
			$newVersionFile = $installer->getParent()->getPath('source') . '/admin/plugins/system/shlib/shlib.xml';
			$this->_shlibVersion = ShlSystem_Resourcemanager::getXmlFileVersion($newVersionFile);
			$installCheckResult = ShlSystem_Resourcemanager::canInstall('shlib', $this->_shlibVersion, $allowDowngrade = false, self::MIN_SHLIB_VERSION, self::MAX_SHLIB_VERSION);

			if ($installCheckResult->canInstall == 'no')
			{
				JFactory::getApplication()
				        ->enqueueMessage(
					        'Cannot install sh404SEF: not allowed to install shLib version ' . $this->_shlibVersion . ': ' . $installCheckResult->reason,
					        'error'
				        );
			}
			if ($installCheckResult->canInstall == 'skip')
			{
				$this->_shlibVersion = '';
				$this->_skipInstall[] = 'shlib';
				JFactory::getApplication()
				        ->enqueueMessage('shLib: skipping install of shLib version ' . $this->_shlibVersion . ': ' . $installCheckResult->reason);
			}

			$canInstall = $installCheckResult->canInstall != 'no';

			return $canInstall;
		}
	}

	/**
	 * Insert in the db the previously retrieved parameters for a plugin
	 * including publication information. Also move files as required
	 *
	 * @param string $basePath , the base path to get original files from
	 */

	public function postflight($type, $parent)
	{
		if (function_exists('apc_clear_cache'))
		{
			apc_clear_cache();
		}
		if (function_exists('opcache_reset'))
		{
			opcache_reset();
		}

		$this->_doInstallUpdate($parent);

		// installed a shared resource? register it with version
		if (!in_array('shlib', $this->_skipInstall))
		{
			ShlSystem_Resourcemanager::registerResource('shlib', $this->_shlibVersion);
		}

		// register that we are now using shLib
		ShlSystem_Resourcemanager::register(array('resource' => 'shlib', 'context' => 'com_sh404sef', 'min_version' => self::MIN_SHLIB_VERSION, 'max_version' => self::MAX_SHLIB_VERSION));

		// make sure the update site is appropriate
		$this->_processUpdateSite($type, $parent);

		// clear caches, in case cache handling or remote config has been modified
		$caches = array('sh404sef_updates', 'sh404sef_rconfig', 'sh404sef_analytics_auth', 'sh404sef_analytics');
		foreach ($caches as $cacheName)
		{
			$cache = JFactory::getCache($cacheName);
			$cache->clean();
		}
	}

	public function install($parent)
	{
	}

	public function uninstall($parent)
	{
		// save configuration
		try
		{
			$this->_definePaths();

			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('params')
			      ->from('#__extensions')
			      ->where($db->qn('type') . '=' . $db->q('component'))
			      ->where($db->qn('element') . '=' . $db->q('com_sh404sef'));
			$db->setQuery($query);
			$sh404sef = $db->loadObject();

			$saved = $this->_writeExtensionConfig('com_sh404sef', array('sh404sefConfig' => $sh404sef->params));
			if (!$saved)
			{
				JFactory::getApplication()->enqueueMessage('Error saving sh404SEF configuration to disk, configuration may be lost upon reinstallation');
			}
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage('Error saving sh404SEF configuration to disk, configuration may be lost upon reinstallation');
		}

		$this->_doUninstall($parent);
	}

	public function update($parent)
	{
	}

	private function _definePaths()
	{
		$this->_siteId = rtrim(str_replace('/administrator', '', JURI::base()), '/');
		$this->_siteId = str_replace('/', '_', str_replace('http://', '', $this->_siteId));
		$this->_preserveConfigFolder = JPATH_ROOT . '/media/sh404sef/';
	}

	// Implementation of install/uninstall scripts

	/**
	 * Writes an extension parameter to a disk file, located
	 * in the /media directory
	 *
	 * @param string $extName the extension name
	 * @param array $shConfig associative array of parameters of the extension, to be written to disk
	 * @param array $pub , optional, only if module, an array of the menu item id where the module is published
	 *
	 * @return boolean, true if no error
	 */
	private function _writeExtensionConfig($extName, $params)
	{
		if (empty($params))
		{
			return;
		}

		// calculate target file name
		$extPath = $this->_preserveConfigFolder . 'sh404_upgrade_conf';

		// if it does not exists, lets create it first
		if (!JFolder::exists($extPath))
		{
			JFolder::create($extPath);
		}

		// make sure we have an index.html file in that folder
		$target = JPath::clean($extPath . '/' . 'index.html');
		if (!JFile::exists($target))
		{
			// copy one Joomla's index.html file to the backup directory
			$source = JPath::clean(JPATH_ROOT . '/plugins/index.html');
			$success = JFile::copy($source, $target);
		}

		// now build full path file name to save config
		$extFile = $extPath . '/' . $extName . '_' . $this->_siteId . '.php';

		// remove previous if any
		if (JFile::exists($extFile))
		{
			JFile::delete($extFile);
		}

		// prepare data for writing
		$data = '<?php // Extension params save file for sh404sef
    //
    if (!defined(\'_JEXEC\')) die(\'Direct Access to this location is not allowed.\');';
		$data .= "\n";

		if (!empty($params))
		{
			foreach ($params as $key => $value)
			{
				$data .= '$' . $key . ' = ' . var_export($value, true) . ';';
				$data .= "\n";
			}
		}

		// write to disk
		$success = JFile::write($extFile, $data);

		return $success !== false;
	}

	/**
	 * Performs pre-uninstall backup of configuration
	 *
	 * @param object $parent
	 */
	private function _doUninstall($parent)
	{
		$this->_definePaths();
		$this->_includeLibs();

		// Before uninstalling modules, save their settings, if told to do so
		$sef_config_class = JPATH_ADMINISTRATOR . '/components/com_sh404sef/sh404sef.class.php';

		// Make sure class was loaded.
		if (!class_exists('shSEFConfig'))
		{
			if (is_readable($sef_config_class))
			{
				require_once($sef_config_class);
			}
			else
			{
				JError::RaiseError(500, JText::_('COM_SH404SEF_NOREAD') . "( $sef_config_class )<br />" . JText::_('COM_SH404SEF_CHK_PERMS'));
			}
		}
		$sefConfig = new shSEFConfig();
		if (!$sefConfig->shKeepStandardURLOnUpgrade && !$sefConfig->shKeepCustomURLOnUpgrade)
		{
			$this->_deletetable('sh404sef_urls');
			$this->_deletetable('sh404sef_urls_src');
			$this->_deletetable('sh404sef_hits_404s');
			$this->_deletetable('sh404sef_aliases');
			$this->_deletetable('sh404sef_hits_aliases');
			$this->_deletetable('sh404sef_pageids');
			$this->_deletetable('sh404sef_hits_shurls');
		}
		elseif (!$sefConfig->shKeepStandardURLOnUpgrade)
		{
			$this->_deleteAllSEFUrl('Standard');
			$this->_deletetable('sh404sef_urls_src');
			$this->_deletetable('sh404sef_hits_404s');
		}
		elseif (!$sefConfig->shKeepCustomURLOnUpgrade)
		{
			$this->_deleteAllSEFUrl('Custom');
			$this->_deletetable('sh404sef_aliases');
			$this->_deletetable('sh404sef_hits_aliases');
			$this->_deletetable('sh404sef_pageids');
			$this->_deletetable('sh404sef_hits_shurls');
		}

		if (!$sefConfig->shKeepMetaDataOnUpgrade)
		{
			$this->_deletetable('sh404sef_metas');
		}

		// delete key store, content (error pages) has been saved before
		$this->_deletetable('sh404sef_keystore');

		// remove system plugins
		$this->_saveDeletePluginParams('sh404sef', 'system');
		$this->_saveDeletePluginParams('shmobile', 'system');

		// remove installer plugin
		$this->_saveDeletePluginParams('sh404sef', 'installer');

		// unregister from shLib, then possibly uninstall it
		if (JFile::exists(JPATH_ROOT . '/plugins/system/shlib/shl_packages/system/resourcemanager.php'))
		{
			require_once JPATH_ROOT . '/plugins/system/shlib/shl_packages/system/resourcemanager.php';
			ShlSystem_Resourcemanager::unregister('shlib', 'com_sh404sef');
			if (ShlSystem_Resourcemanager::canUninstall('shlib'))
			{
				$this->_saveDeletePluginParams('shlib', 'system');
			}
		}

		// remove core plugins
		$this->_saveDeletePluginGroup('sh404sefcore');
		$this->_saveDeletePluginGroup('sh404sefextplugins');

		// delete analytics cached data, to force update
		// in case this part of sh404sef has changed
		$cache = JFactory::getCache('sh404sef_analytics');
		$cache->clean();

		// preserve configuration or not ?
		if (!$sefConfig->shKeepConfigOnUpgrade)
		{
			// main config file
			$fileName = $this->_preserveConfigFolder . 'sh404_upgrade_conf_' . $this->_siteId . '.php';
			if (JFile::exists($fileName))
			{
				JFile::delete($fileName);
			}

			// user custom config file
			$fileName = $this->_preserveConfigFolder . 'sh404_upgrade_conf_' . $this->_siteId . '.custom.php';
			if (JFile::exists($fileName))
			{
				JFile::delete($fileName);
			}

			// related extensions (plugins) config files folder
			if (JFolder::exists($this->_preserveConfigFolder . 'sh404_upgrade_conf'))
			{
				JFolder::delete($this->_preserveConfigFolder . 'sh404_upgrade_conf');
			}

			// log files folder
			if (JFolder::exists($this->_preserveConfigFolder . 'sh404_upgrade_conf_logs'))
			{
				JFolder::delete($this->_preserveConfigFolder . 'sh404_upgrade_conf_logs');
			}

			// security log files folder
			if (JFolder::exists($this->_preserveConfigFolder . 'sh404_upgrade_conf_security'))
			{
				JFolder::delete($this->_preserveConfigFolder . 'sh404_upgrade_conf_security');
			}
		}
		else
		{ // if we keep config

			if (JFolder::exists(JPATH_ROOT . '/logs/sh404sef'))
			{
				JFolder::copy(JPATH_ROOT . '/logs/sh404sef', $this->_preserveConfigFolder . 'sh404_upgrade_conf_logs', $path = '', $force = true);
			}

			if (JFolder::exists(JPATH_ADMINISTRATOR . '/components/com_sh404sef/security'))
			{
				JFolder::copy(
					JPATH_ADMINISTRATOR . '/components/com_sh404sef/security',
					$this->_preserveConfigFolder . 'sh404_upgrade_conf_security', $path = '', $force = true
				);
			}
		}

		// delete folder in /media
		// display results
		echo '<h3>sh404SEF has been succesfully uninstalled. </h3>';
		echo '<br />';
		if ($sefConfig->shKeepStandardURLOnUpgrade)
		{
			echo '- automatically generated SEF url have not been deleted (table #__sh404sef_urls)<br />';
		}
		else
		{
			echo '- automatically generated SEF url have been deleted<br />';
		}
		echo '<br />';
		if ($sefConfig->shKeepCustomURLOnUpgrade)
		{
			echo '- custom SEF url, aliases and pageIds have not been deleted (tables #__sh404sef_urls, #__sh404sef_aliases and #__sh404sef_pageids)<br />';
		}
		else
		{
			echo '- custom SEF url, aliases and pageIds have been deleted<br />';
		}
		echo '<br />';
		if ($sefConfig->shKeepMetaDataOnUpgrade)
		{
			echo '- Custom Title and META data have not been deleted (table #__sh404sef_metas)<br />';
		}
		else
		{
			echo '- Custom Title and META data have been deleted<br />';
		}
		echo '<br />';
	}

	private function _includeLibs()
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.path');
		jimport('joomla.html.parameter');
		jimport('joomla.filter.filterinput');
		jimport('joomla.utilities.string');

		// we use require instead of require_once so that the NEW, just installed, version of the file is
		// reloaded. If any new define has been added in the new version, they will thus become available
		// This requires that defines in defines.php are protected again redifining the same constant
		require JPATH_ROOT . '/administrator/components/com_sh404sef/defines.php';
		// if these files have been included already, we will use the old version of the file
		require_once JPATH_ROOT . '/administrator/components/com_sh404sef/exceptions/default.php';
		require_once JPATH_ROOT . '/administrator/components/com_sh404sef/helpers/language.php';
	}

	private function _deletetable($tableName)
	{
		try
		{
			$db = JFactory::getDbo();
			$query = 'DROP TABLE IF EXISTS ' . $db->qn('#__' . $tableName);
			$db->setQuery($query)->query();
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueuemessage($e->getMessage(), 'error');
		}
	}

	private function _deleteAllSEFUrl($kind)
	{
		try
		{
			$db = JFactory::getDbo();
			if ($kind == 'Custom')
			{
				$where = $db->qn('dateadd') . ' > ' . $db->q('0000-00-00') . ' and ' . $db->qn('newurl') . ' != ' . $db->q('');
			}
			else
			{
				$where = $db->qn('dateadd') . ' = ' . $db->q('0000-00-00');
			}

			$db->setQuery('DELETE FROM ' . $db->qn('#__sh404sef_urls') . ' ' . $where);
			$db->query();
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueuemessage($e->getMessage(), 'error');
		}
	}

	/**
	 * Save parameters, then delete a module
	 * Would not work on additional copies made by user
	 *
	 * @param string $moduleName , the module name, matching 'module' column in modules table
	 * @param string $client (ie : site or administrator
	 *
	 * @return bool
	 */
	private function _saveDeleteModuleParams($moduleName, $client)
	{
		// read plugin param from db
		try
		{
			$db = JFactory::getDbo();

			//$result = ShlDbHelper::selectAssoc('#__extensions', array('*'),
			//	array('type' => 'module', 'element' => $moduleName, 'client_id' => $client));
			$query = $db->getQuery(true);
			$query->select('*')
			      ->from('#__extensions')
			      ->where($db->qn('type') . '=' . $db->q('module'))
			      ->where($db->qn('element') . '=' . $db->q($moduleName))
			      ->where($db->qn('client_id') . '=' . $db->q($client));
			$db->setQuery($query);
			$result = $db->loadAssoc();

			if (empty($result))
			{
				// invalid module name?
				return false;
			}
			// remove module db id
			unset($result['extension_id']);

			// write everything on disk
			$this->_writeExtensionConfig($moduleName . '_extension', array('shConfig' => $result));

			// now remove plugin details from db
			//ShlDbHelper::delete('#__extensions', array('type' => 'module', 'element' => $moduleName, 'client_id' => $client));
			$query = $db->getQuery(true);
			$query->delete('#__extensions')
			      ->where($db->qn('type') . '=' . $db->q('module'))
			      ->where($db->qn('element') . '=' . $db->q($moduleName))
			      ->where($db->qn('client_id') . '=' . $db->q($client));
			$db->setQuery($query)->query();

			// do the same for the module instance, in #__module table
			//$result = ShlDbHelper::selectAssoc('#__modules', array('*'), array('module' => $moduleName, 'client_id' => $client));
			$query = $db->getQuery(true);
			$query->select('*')
			      ->from('#__modules')
			      ->where($db->qn('module') . '=' . $db->q($moduleName))
			      ->where($db->qn('client_id') . '=' . $db->q($client));
			$db->setQuery($query);
			$result = $db->loadAssoc();

			if (empty($result))
			{
				// invalid module name?
				return false;
			}
			// save and remove module db id
			$moduleId = $result['id'];
			unset($result['id']);

			// write everything on disk
			$this->_writeExtensionConfig($moduleName . '_modules', array('shConfig' => $result));

			// now remove plugin details from db
			//ShlDbHelper::delete('#__modules', array('module' => $moduleName, 'client_id' => $client));
			$query = $db->getQuery(true);
			$query->delete('#__modules')
			      ->where($db->qn('module') . '=' . $db->q($moduleName))
			      ->where($db->qn('client_id') . '=' . $db->q($client));
			$db->setQuery($query)->query();

			// remove module/menu affectation
			//$result = ShlDbHelper::selectAssoc('#__modules_menu', array('*'), array('moduleid' => $moduleId));
			$query = $db->getQuery(true);
			$query->select('*')
			      ->from('#__modules_menu')
			      ->where($db->qn('moduleid') . '=' . $db->q($moduleId));
			$db->setQuery($query);
			$result = $db->loadAssoc();

			// remove module db id
			unset($result['moduleid']);

			// write everything on disk
			$this->_writeExtensionConfig($moduleName . '_modules_menu', array('shConfig' => $result));

			// now remove plugin details from db
			//ShlDbHelper::delete('#__modules_menu', array('moduleid' => $moduleId));
			$query = $db->getQuery(true);
			$query->delete('#__modules_menu')
			      ->where($db->qn('moduleid') . '=' . $db->q($moduleId));
			$db->setQuery($query)->query();
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueuemessage($e->getMessage(), 'error');
		}

		// delete the module files
		$path = JPATH_ROOT . '/' . ($client ? 'administrator' . '/' : '') . 'modules/' . $moduleName;
		if (JFolder::exists($path))
		{
			JFolder::delete($path);
		}
	}

	/**
	 * Save parameters, then delete a plugin
	 *
	 * @param string $pluginName , the plugin name, mathcing 'element' column in plugins table
	 * @param string $folder , the plugin folder (ie : 'content', 'search', 'system',...
	 */
	private function _saveDeletePluginParams($pluginName, $folder, $folders = null)
	{
		try
		{
			$db = JFactory::getDbo();

			//$result = ShlDbHelper::selectAssoc('#__extensions', array('*'), array('type' => 'plugin', 'element' => $pluginName, 'folder' => $folder));
			$query = $db->getQuery(true);
			$query->select('*')
			      ->from('#__extensions')
			      ->where($db->qn('type') . '=' . $db->q('plugin'))
			      ->where($db->qn('element') . '=' . $db->q($pluginName))
			      ->where($db->qn('folder') . '=' . $db->q($folder));
			$db->setQuery($query);
			$result = $db->loadAssoc();

			if (empty($result))
			{
				// invalid plugin name?
				return false;
			}

			// remove plugin db id
			$pluginId = $result['extension_id'];
			unset($result['extension_id']);

			// write everything on disk
			$this->_writeExtensionConfig($pluginName, array('shConfig' => $result));

			// now uninstall
			$installer = new JInstaller;
			$result = $installer->uninstall('plugin', $pluginId);
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueuemessage($e->getMessage(), 'error');
		}
	}

	/**
	 * Save params, then delete plugin, for all plugins
	 * in a given group
	 *
	 * @param $group the group to be deleted
	 *
	 * @return none
	 */
	private function _saveDeletePluginGroup($group)
	{
		$unsafe = array('authentication', 'content', 'editors', 'editors-xtd', 'search', 'system', 'xmlrpc');
		if (in_array($group, $unsafe))
		{
			// safety net : we don't want to delete the whole system or content folder
			return false;
		}

		// read plugin param from db
		try
		{
			$db = JFactory::getDbo();
			// $pluginList = ShlDbHelper::selectAssocList('#__extensions', array('*'), array('type' => 'plugin', 'folder' => $group));

			$query = $db->getQuery(true);
			$query->select('*')
			      ->from('#__extensions')
			      ->where($db->qn('type') . '=' . $db->q('plugin'))
			      ->where($db->qn('folder') . '=' . $db->q($group));
			$db->setQuery($query);
			$pluginList = $db->loadAssocList();

			if (empty($pluginList))
			{
				return true;
			}

			// for each plugin
			foreach ($pluginList as $plugin)
			{
				// remove plugin db id
				unset($plugin['id']);

				// write everything on disk
				$this->_writeExtensionConfig($plugin['folder'] . '.' . $plugin['element'], array('shConfig' => $plugin));

				// now remove plugin details from db
				//ShlDbHelper::delete('#__extensions', array('type' => 'plugin', 'element' => $plugin['element'], 'folder' => $plugin['folder']));
				$query = $db->getQuery(true);
				$query->delete('#__extensions')
				      ->where($db->qn('type') . '=' . $db->q('plugin'))
				      ->where($db->qn('element') . '=' . $db->q($plugin['element']))
				      ->where($db->qn('folder') . '=' . $db->q($plugin['folder']));
				$db->setQuery($query)->query();
			}
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueuemessage($e->getMessage(), 'error');
		}

		// now delete the files for the whole group
		if (JFolder::exists(JPATH_ROOT . '/plugins/' . $group))
		{
			JFolder::delete(JPATH_ROOT . '/plugins/' . $group);
		}
	}

	private function _doInstallUpdate($parent)
	{
		$this->_definePaths();
		$this->_includeLibs();
		$db = JFactory::getDbo();

		// Copy existing config file from /media to current component. Used to recover configuration when upgrading
		// check if old file exists before deleting stub config file
		$oldConfigFile = $this->_preserveConfigFolder . 'sh404_upgrade_conf_' . $this->_siteId . '.php';
		if (JFile::exists($oldConfigFile))
		{
			// update old config files from VALID_MOS check to _JEXEC
			$config = JFile::read($oldConfigFile);
			if ($config && strpos($config, 'VALID_MOS') !== false)
			{
				$config = str_replace('VALID_MOS', '_JEXEC', $config);
				JFile::write($oldConfigFile, $config); // write it back
			}
			// now get back old config
			if (JFile::exists(JPATH_ADMINISTRATOR . '/components/com_sh404sef/config/config.sef.php'))
			{
				JFile::delete(JPATH_ADMINISTRATOR . '/components/com_sh404sef/config/config.sef.php');
			}
			JFile::copy($oldConfigFile, JPATH_ADMINISTRATOR . '/components/com_sh404sef/config/config.sef.php');
		}

		// restore black/white lists
		$folder = $this->_preserveConfigFolder . 'sh404_upgrade_conf_security';
		if (JFolder::exists($folder))
		{
			$fileList = JFolder::files($folder);
			if (!empty($fileList))
			{
				foreach ($fileList as $file)
				{
					if (JFile::exists(JPATH_ADMINISTRATOR . '/components/com_sh404sef/security/' . $file))
					{
						JFile::delete(JPATH_ADMINISTRATOR . '/components/com_sh404sef/security/' . $file);
					}
					JFile::copy(
						$this->_preserveConfigFolder . 'sh404_upgrade_conf_security/' . $file,
						JPATH_ADMINISTRATOR . '/components/com_sh404sef/security/' . $file
					);
				}
			}
		}
		// if upgrading rather than installing from scratch, or after an uninstall
		// we must not copy back saved configuration files and log files
		// as this would overwrite up to date current ones
		// note that above we restored main config file and
		// security data files becomes blank files come
		// with the extension, so they'll be deleted in any case
		// and we have to restore them
		$shouldRestore = $this->_shouldRestore();

		if ($shouldRestore)
		{
			// restore log files
			$folder = $this->_preserveConfigFolder . 'sh404_upgrade_conf_logs';
			if (JFolder::exists($folder))
			{
				JFolder::copy($folder, JPATH_ROOT . '/logs/sh404sef', $path = '', $force = true);
			}

			// restore customized default params
			$oldCustomConfigFile = $this->_preserveConfigFolder . 'sh404_upgrade_conf_' . $this->_siteId . '.custom.php';
			if (is_readable($oldCustomConfigFile) && filesize($oldCustomConfigFile) > 1000)
			{
				// update old config files from VALID_MOS check to _JEXEC
				$config = JFile::read($oldCustomConfigFile);
				if ($config && strpos($config, 'VALID_MOS') !== false)
				{
					$config = str_replace('VALID_MOS', '_JEXEC', $config);
					JFile::write($oldCustomConfigFile, $config); // write it back
				}
				if (JFile::exists(JPATH_ADMINISTRATOR . '/components/com_sh404sef/custom.sef.php'))
				{
					JFile::delete(JPATH_ADMINISTRATOR . '/components/com_sh404sef/custom.sef.php');
				}
				$result = JFile::copy($oldCustomConfigFile, JPATH_ADMINISTRATOR . '/components/com_sh404sef/custom.sef.php');
			}

			// read saved config
			$extFile = $this->_preserveConfigFolder . 'sh404_upgrade_conf' . '/com_sh404sef_' . $this->_siteId . '.php';

			// remove previous if any
			if (JFile::exists($extFile))
			{
				include_once $extFile;
				if (!empty($sh404sefConfig))
				{
					// write it back into sh404sef config field
					try
					{
						$query = 'update ' . $db->qn('#__extensions') . ' set ' . $db->qn('params') . ' = ' . $db->q($sh404sefConfig)
							. ' where ' . $db->qn('type') . ' = ' . $db->q('component') . ' and '
							. $db->qn('element') . ' = ' . $db->q('com_sh404sef');
						$db->setQuery($query)->query();
					}
					catch (Exception $e)
					{
						JFactory::getApplication()
						        ->enqueuemessage('Database error while restoring saved configuration. Config may be lost: ' . $e->getMessage());
					}
				}
			}
		}

		// install plugins
		if (defined('SHLIB_ROOT_PATH'))
		{
			// trick of the day: we must fetch an instance of the db using the db helper
			// before installing the newest version of shLib system plugin. This will
			// force a decorated db instance to be created and stored, using the shlib
			// db class version that matches that of the shlib db helper class
			// As there was interface changes betwen shLib 0.1.x and 0.2.x, this prevents
			// "method not existing" errors when installing a newer version over an old one
			// make sure resource manager is available, we'll need it during plugins installs
			$shlDb = ShlDbHelper::getInstance();
			$status = $this->_installPluginGroup('system');
		}
		else
		{
			$this->_installPluginGroup('system');
			// shLib is not installed yet, let's make it available to us
			if (!JFile::exists(JPATH_ROOT . '/plugins/system/shlib/shlib.php'))
			{
				JFactory::getApplication()
				        ->enqueuemessage('shLib was not installed properly, cannot continue. Please try uninstalling and installing again');

				return false;
			}
			require_once JPATH_ROOT . '/plugins/system/shlib/shlib.php';
			$config = array('type' => 'system', 'name' => 'shlib', 'params' => '');
			if (version_compare(JVERSION, '3', 'ge'))
			{
				$dispatcher = JEventDispatcher::getInstance();
			}
			else
			{
				$dispatcher = JDispatcher::getInstance();
			}
			$shLibPlugin = new plgSystemShlib($dispatcher, $config);
			$shLibPlugin->onAfterInitialise();
			$status = true;
		}

		if (!$status)
		{
			JFactory::getApplication()
			        ->enqueuemessage('There was an error installing one or more system plugins. Please try uninstalling and installing again');

			return;
		}

		$status = $this->_installPluginGroup('sh404sefcore');
		if (!$status)
		{
			JFactory::getApplication()
			        ->enqueuemessage('There was an error installing one or more sh404SEF core plugins. Please try uninstalling and installing again');

			return;
		}
		$status = $this->_installPluginGroup('installer');
		if (!$status)
		{
			JFactory::getApplication()
			        ->enqueuemessage('There was an error installing one or more sh404SEF installer plugins. Please try uninstalling and installing again');

			return;
		}
		$status = $this->_installPluginGroup('sh404sefextplugins');
		if (!$status)
		{
			JFactory::getApplication()
			        ->enqueuemessage('There was an error installing one or more sh404SEF extension plugins. Please try uninstalling and installing again');

			return;
		}

		// remove cpicon module, not needed anymore that we use Joomla updater
		// remove admin quick icon module
		$this->_saveDeleteModuleParams('mod_sh404sef_cpicon', $client = 1);

		// apply various DB updates
		$this->_createDBStructure();
		$this->_updateDBStructure();

		// now we insert the 404 error page into the database
		// from version 1.5.5, the default content of 404 page has been largely modified
		// to make use of the similar urls plugin (and potentially others)
		// so we want to make sure people will have the new version of the 404 error page
		$status = $this->_updateErrorPage();
		if (!$status)
		{
			JFactory::getApplication()
			        ->enqueuemessage('There was an error updating the 404 error page. You may have to set it up manually in sh404SEF configuration.');

			return;
		}

		$status = $this->_updateAnalyticsAccount();
		if (!$status)
		{
			JFactory::getApplication()
			        ->enqueuemessage('There was an error updating the Google Analytics Web property ID. You may have to set it up manually in sh404SEF configuration.');

			return;
		}

		$status = $this->_fixInvalidShurl();
		if (!$status)
		{
			JFactory::getApplication()
			        ->enqueuemessage('There was an error fixing some invalid shURLs in the database. Please check that no shURL is identical to one of your language codes ie /en, /fr, /de.');

			return;
		}

		// make sur permissions record are set straight
		$query = $db->getQuery(true);
		$query->select('rules');
		$query->from('#__assets');
		$query->where($db->qn('name') . '=' . $db->q('com_sh404sef'));
		$db->setQuery($query);
		$existingRules = $db->loadResult();
		if (empty($existingRules) || $existingRules == '{}' || $existingRules == '[]')
		{
			// write valid default value into assets record
			$defaultRule = '{"core.admin":[],"core.manage":[],"core.create":[],"core.delete":[],"core.edit":[],"core.edit.state":[],"core.edit.own":[]}';
			$query->update($db->qn('#__assets'))->set($db->qn('rules') . '=' . $db->q($defaultRule))
			      ->where($db->qn('name') . '=' . $db->q('com_sh404sef'));
			$db->setQuery($query)->query();
		}

		// 4.7.0+ now using Joomla url rewriting setting
		$status = $this->updateSettings();
		if (!$status)
		{
			JFactory::getApplication()
			        ->enqueuemessage('There was an error transferring or updating settings. You may have to update sh404SEF or Joomla configuration manually');

			return;
		}

		// message
		// decide on help file language
		$languageCode = Sh404sefHelperLanguage::getFamily();
		$basePath = JPATH_ROOT . '/administrator/components/com_sh404sef/language/%s.postinstall.php';
		// fall back to english if language readme does not exist
		jimport('joomla.filesystem.file');
		if (!JFile::exists(sprintf($basePath, $languageCode)))
		{
			$languageCode = 'en';
		}

		ob_start();
		include sprintf($basePath, $languageCode);
		$postInstallMessage = ob_get_clean();
		JFactory::getApplication()->enqueueMessage( $postInstallMessage);
	}

	private function updateSettings()
	{
		$app = JFactory::getApplication();
		$j3 = version_compare(JVERSION, '3.0', 'ge');

		// get current (installed) sh404SEF settings
		try
		{
			if (!class_exists('Sh404sefFactory'))
			{
				// not previously installed, do nothing;
				return true;
			}
			// get the pre-existing config, the one instantiated before
			// installation started
			$sh404SEFConfig = Sh404sefFactory::getConfig();
			if (version_compare($sh404SEFConfig->version, '4.7.0', 'ge') || !$sh404SEFConfig->Enabled)
			{
				// already done, or not running, do nothing
				return true;
			}

			// installed and running, check/update Joomla config accordingly
			$joomlaUrlRewriting = (bool) ($j3 ? $app->get('sef_rewrite') : $app->getCfg('sef_rewrite'));
			$sh404SEFUrlRewriting = (bool) $sh404SEFConfig->shRewriteMode;

			// opposite storage convention between Joomla and sh404SEF
			// so if settings are equal, then config is different
			if ($sh404SEFUrlRewriting == $joomlaUrlRewriting)
			{
				// different settings. sh404SEF is running and enabled, so sh404SEF setting
				// must replace the Joomla one
				$jConfig = new JConfig();
				$jConfig = JArrayHelper::fromObject($jConfig);
				$writeConfig = new JRegistry();
				$writeConfig->loadArray($jConfig);

				// replace Joomla config setting with ours
				$writeConfig->set('sef_rewrite', $sh404SEFUrlRewriting ? 0 : 1);

				// Set the configuration file path.
				$file = JPATH_CONFIGURATION . '/configuration.php';

				// Get the new FTP credentials.
				$ftp = JClientHelper::getCredentials('ftp', true);

				// Attempt to make the file writeable if using FTP.
				if (!$ftp['enabled'] && JPath::isOwner($file) && !JPath::setPermissions($file, '0644'))
				{
					$app->enqueueMessage('Error: Joomla! configuration.php file is not writable.', 'notice');
				}

				// Attempt to write the configuration file as a PHP class named JConfig.
				$configuration = $writeConfig->toString('PHP', array('class' => 'JConfig', 'closingtag' => false));

				if (!JFile::write($file, $configuration))
				{
					throw new RuntimeException('Error writing to Joomla! configuration.php file, maybe not writeable?');
				}
			}
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage());
			return false;
		}

		return true;
	}

	/**
	 * Decide whether backed up params should be restore (and
	 * plugins reinstalled).
	 * This should happen only when the extension is NOT already
	 * installed. Most of times, as we are using updagre install
	 * that should not happen and we jst overwrite
	 * but if user uninstalled the extension, we must restore
	 * data saved when he uninstalled
	 *
	 */
	private function _shouldRestore()
	{
		// IMPORTANT: the check is done once, and only once
		// as for later calls, the system plugin will have been installed
		// and thus the test will not be valid anymore
		static $restore = null;

		if (is_null($restore))
		{
			// search for base xml file to decide if already installed
			$restore = !JFile::exists(JPATH_ROOT . '/plugins/system/sh404sef/sh404sef.xml');
		}

		return $restore;
	}

	/**
	 * Install all sh404sef plugins available in a given
	 * group
	 *
	 * @param string $group name of group
	 *
	 * @return boolean, true if success
	 */
	private function _installPluginGroup($group)
	{
		$app = JFactory::getApplication();

		$sourcePath = JPATH_ADMINISTRATOR . '/components/com_sh404sef/plugins/' . $group;
		if (!JFolder::exists($sourcePath))
		{
			$app->enqueueMessage('Trying to install empty plugin group: ' . $group);

			return true;
		}

		// if each plugin resides in its own subDir, we must iterate over all sub dirs
		$folderList = JFolder::folders($sourcePath);
		if (empty($folderList))
		{
			$app->enqueueMessage('Trying to install empty plugin group, folder is empty: ' . $sourcePath);

			return true;
		}

		// process each plugin
		$errors = false;
		foreach ($folderList as $folder)
		{
			// install the plugin itself
			$status = $this->_installPlugin($group, $folder, $sourcePath);
			// set flag if an error happened, but keep installing
			// other plugins
			$errors = $errors || !$status;
			// also display status
			if (!$status)
			{
				$app->enqueueMessage('Error installing sh404sef plugin from ' . $folder);
			}
		}

		// return true if no error at all
		return $errors == false;
	}

	// V 1.2.4.t improved upgrading

	/**
	 * Insert in the db the previously retrieved parameters for a plugin
	 * including publication information. Also move files as required
	 *
	 * @param string $pluginFolder
	 * @param string $pluginElement
	 * @param string $basePath
	 */
	private function _installPlugin($pluginFolder, $pluginElement, $sourcePath)
	{
		if ($pluginFolder == 'system' && $pluginElement == 'shlib')
		{
			if (in_array('shlib', $this->_skipInstall))
			{
				return true;
			}
		}

		$status = true;
		$app = JFactory::getApplication();

		// in case of upgrade, don't touch settings by user
		try
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('extension_id')->from('#__extensions')->where($db->qn('type') . '=' . $db->q('plugin'))
			      ->where($db->qn('element') . '=' . $db->q($pluginElement))
			      ->where($db->qn('folder') . '=' . $db->q($pluginFolder));
			$db->setQuery($query);
			$pluginId = $db->loadResult();
		}
		catch (Exception $e)
		{
			$status = false;
			$app->enqueueMessage('Error reading pre-existing plugin record from db: ' . $pluginFolder . ' / ' . $pluginElement);

			return $status;
		}

		$overrides = empty($pluginId) ? array('ordering' => 10, 'enabled' => 1) : array();

		// make sure main library is loaded first
		if (empty($pluginId) && $pluginElement == 'shlib')
		{
			$overrides['ordering'] = -100;
		}

		// use J! installer to fully install the plugin
		$installer = new JInstaller;
		$result = $installer->install($sourcePath . '/' . $pluginElement);

		if ($result)
		{
			$shouldRestore = $this->_shouldRestore();

			if ($shouldRestore)
			{
				// read stored params from disk
				$saved = array();
				$this->_getExtensionSavedParams($pluginFolder . '.' . $pluginElement, $saved);
				unset($saved['extension_id']);
				$overrides = array_merge($overrides, $saved);
			}

			// overrides data in extension table, possibly overriding some columns from saved data
			if (!empty($overrides))
			{
				try
				{
					$query = $db->getQuery(true);
					$query->select('extension_id')->from('#__extensions')->where($db->qn('type') . '=' . $db->q('plugin'))
					      ->where($db->qn('element') . '=' . $db->q($pluginElement))
					      ->where($db->qn('folder') . '=' . $db->q($pluginFolder));
					$db->setQuery($query);
					$pluginId = $db->loadResult();
					$error = $db->getErrorNum();
					if (!empty($error))
					{
						throw new Exception($db->getErrorMsg());
					}

					if (!empty($pluginId))
					{
						jimport('joomla.database.table.extension');
						$extension = JTable::getInstance('Extension');
						$extension->load($pluginId);
						$extension->bind($overrides);
						$status = $extension->store();
						if (!$status)
						{
							$app->enqueueMessage('Error writing updated extension record: ' . $extension->getError() . ' for ' . $pluginFolder . ' / ' . $pluginElement);
						}
					}
					else
					{
						$app->enqueueMessage('Error updating plugin DB record: ' . $pluginFolder . ' / ' . $pluginElement);
					}
				}
				catch (Exception $e)
				{
					$status = false;
					$app->enqueueMessage('Error: ' . $e->getMessage());
				}
			}
		}
		else
		{
			$app->enqueueMessage('Error installing sh404sef plugin: ' . $pluginFolder . ' / ' . $pluginElement);
			$status = false;
		}

		return $status;
	}

	/**
	 * Retrieves stored params of a given extension (module or plugin)
	 * (as saved upon uninstall)
	 *
	 * @param string $extName the module name, including mod_ if a module
	 * @param array $shConfig an array holding the database columns of the extension
	 * @param array $shPub , an array holding the publication information of the module (only for modules)
	 *
	 * @return boolean, true if any stored parameters were found for this extension
	 */
	private function _getExtensionSavedParams($extName, &$shConfig, &$shPub = null, $useId = false)
	{
		static $fileList = array();

		// prepare default return value
		$status = false;

		// read all file names in /media/sh404_upgrade_conf dir, for easier processing
		$baseFolder = $this->_preserveConfigFolder . 'sh404_upgrade_conf';
		if (JFolder::exists($baseFolder) && (empty($fileList) || !isset($fileList[$extName])))
		{
			$baseName = $extName . ($useId ? '_[0-9]{1,10}' : '') . '_' . $this->_siteId . '.php';
			$fileList[$extName] = JFolder::files($baseFolder, $baseName);
		}

		// extract filename from list we've established previously
		$extFile = isset($fileList[$extName]) && $fileList[$extName] !== false ? array_shift($fileList[$extName]) : '';
		if (empty($fileList[$extName]))
		{
			// prevent infinite loop
			$fileList[$extName] = false;
		}

		if (!empty($extFile) && JFile::exists($baseFolder . '/' . $extFile))
		{
			$status = true; // operation was successful
			include($baseFolder . '/' . $extFile);
		}

		return $status;
	}

	/**
	 *
	 * utility functions
	 *
	 */

	private function _installModule($module, $source, $extensionConfig, $moduleConfig)
	{
		$app = JFactory::getApplication();

		$path = $source . '/admin/modules/' . $module;
		$installer = new JInstaller;
		$result = $installer->install($path);

		if ($result)
		{
			// if files moved to destination, setup module in Joomla database
			$shouldRestore = $this->_shouldRestore();

			if ($shouldRestore)
			{
				// read stored params from disk
				$this->_getExtensionSavedParams($module . '_extension', $extensionConfig);
			}

			// update elements in db, only if we need to restore past configuration
			try
			{
				$db = JFactory::getDbo();
				if (!empty($extensionConfig))
				{
					// load module details from extension table
					//$moduleDetails = ShlDbHelper::selectAssoc('#__extensions', array('*'), array('type' => 'module', 'element' => $module));
					$query = $db->getQuery(true);
					$query->select('*')
					      ->from('#__extensions')
					      ->where($db->qn('type') . '=' . $db->q('module'))
					      ->where($db->qn('element') . '=' . $db->q($module));
					$db->setQuery($query);
					$moduleDetails = $db->loadAssoc();

					// merge with saved details and write back to disk
					$details = array_merge($moduleDetails, $extensionConfig);

					//ShlDbHelper::update('#__extensions', $details, array('extension_id' => (int) $moduleDetails['extension_id']));
					$query = $db->getQuery(true);
					$query->update('#__extensions')
					      ->where($db->qn('extension_id') . '=' . (int) $moduleDetails['extension_id']);
					foreach ($details as $key => $value)
					{
						$query->set($db->qn($key) . ' = ' . $db->q($value));
					}
					$db->setQuery($query)->query();
				}
			}
			catch (Exception $e)
			{
				$app->enqueueMessage('Error: ' . $e->getMessage());
			}

			if ($shouldRestore)
			{
				// read stored params from disk
				$this->_getExtensionSavedParams($module . '_modules', $moduleConfig);
			}

			// update elements in db, if we need to restore past configuration
			try
			{
				//$instanceDetails = ShlDbHelper::selectAssoc('#__modules', array('*'), array('module' => $module));
				$query = $db->getQuery(true);
				$query->select('*')
				      ->from('#__modules')
				      ->where($db->qn('module') . '=' . $db->q($module));
				$db->setQuery($query);
				$instanceDetails = $db->loadAssoc();

				// merge with saved details and write back to disk
				$details = array_merge($instanceDetails, $moduleConfig);

				//ShlDbHelper::update('#__modules', $details, array('id' => (int) $instanceDetails['id']));
				$query = $db->getQuery(true);
				$query->update('#__modules')
				      ->where($db->qn('id') . '=' . (int) $instanceDetails['id']);
				foreach ($details as $key => $value)
				{
					$query->set($db->qn($key) . ' = ' . $db->q($value));
				}
				$db->setQuery($query)->query();
			}
			catch (Exception $e)
			{
				$app->enqueueMessage('Error: ' . $e->getMessage());
			}

			// and finally we make sure there is a menu item associated with the module
			$details = array('menuid' => 0);

			if ($shouldRestore)
			{

				// read stored params from disk
				$this->_getExtensionSavedParams($module . '_modules_menu', $details);
			}
			$details = array_merge($details, array('moduleid' => (int) $instanceDetails['id']));

			// insert or update elements in db, if we need to restore past configuration
			try
			{
				//ShlDbHelper::insertUpdate('#__modules_menu', $details, array('moduleid' => (int) $instanceDetails['id']));

				$query = $db->getQuery(true);
				$query->select("count(*)")
				      ->from('#__modules_menu')
				      ->where($db->qn('moduleid') . '=' . (int) $instanceDetails['id']);
				$db->setQuery($query);
				$found = $db->loadResult();
				$query->clear();
				if (empty($found))
				{
					// insert
					$query->insert('#__modules_menu');
				}
				else
				{
					// update
					$query->update('#__modules_menu')
					      ->where($db->qn('moduleid') . '=' . (int) $instanceDetails['id']);
				}
				foreach ($details as $key => $value)
				{
					$query->set($db->qn($key) . ' = ' . $db->q($value));
				}
				$db->setQuery($query)->query();
			}
			catch (Exception $e)
			{
				$app->enqueueMessage('Error: ' . $e->getMessage());
			}
		}
		else
		{
			$app->enqueueMessage('Error installing sh404sef module: ' . $module);
		}

		return $result;
	}

	/**
	 * As of 4.7.0, Analytics classic is removed, we only use Universal
	 * Transfer settings accordingly
	 */
	private function _updateAnalyticsAccount()
	{
		if (!class_exists('Sh404sefFactory'))
		{
			// sh404SEF not running, no copying over of
			// accounts. If user disabled sh404SEF system plugin
			// we can't really detect that, but then they can
			// manually copy over the analytics configuration
			return true;
		}

		$config = Sh404sefFactory::getConfig();
		try
		{
			$updated = false;
			$params = Sh404sefHelperGeneral::getComponentParams();
			// select Universal if classic (or bot) was selected
			if (!empty($config->analyticsEdition) && ($config->analyticsEdition == 'ga' || $config->analyticsEdition == 'ga_and_uga'))
			{
				$params->set('analyticsEdition', 'uga');
				$updated = true;
			}

			// if we had a Classic web propery ID, move to UGA, unless we
			// already have such ID
			if (!empty($config->analyticsId) && empty($config->analyticsUgaId))
			{
				$params->set('analyticsUgaId', $config->analyticsId);
				$updated = true;
			}

			// if an
			if ($updated)
			{
				Sh404sefHelperGeneral::saveComponentParams($params);
			}
		}
		catch (Exception $e)
		{
			$app = JFactory::getApplication();
			$app->enqueueMessage('Error: ' . $e->getMessage());

			return false;
		}

		return true;
	}

	/**
	 * In 4.7.1 and 4.7.2, it may happen that shURL collides with language codes
	 * on ML sites.
	 */
	private function _fixInvalidShurl()
	{
		if (!class_exists('Sh404sefFactory'))
		{
			// sh404SEF not running, do nothing
			// If user disabled sh404SEF system plugin
			// we can't really detect that, but then they can
			// manually copy over the analytics configuration
			return true;
		}

		try
		{
			// read all content languages, if any
			$languages = Sh404sefHelperLanguage::getInstalledLanguagesList();

			// adjust to require format
			$langCodes = array();
			if (!empty($languages))
			{
				foreach ($languages as $language)
				{
					$langCodes[] = $language->shortcode;
				}
			}

			// search for shURLs that match any of them
			if (!empty($langCodes))
			{
				ShlDbHelper::deleteIn('#__sh404sef_pageids', 'pageid', $langCodes);
			}
		}
		catch (Exception $e)
		{
			$app = JFactory::getApplication();
			$app->enqueueMessage('Error: ' . $e->getMessage());

			return false;
		}

		return true;
	}

	/**
	 * Transfer to our keystore customized pages created by user, if any.
	 * Prior to 4.7.0, error pages were stored in regular Joomla articles.
	 *
	 * @return unknown_type
	 */
	private function _updateErrorPage($pageTitle = '__404__')
	{
		if (!class_exists('Sh404sefFactory'))
		{
			// sh404SEF not running, no copying over of
			// error page. If user disabled sh404SEF system plugin
			// we can't really detect that, but then they can
			// manually copy over the error page(s)
			return true;
		}

		// do we already have a __404__ article?
		$config = Sh404sefFactory::getConfig();
		try
		{
			$db = JFactory::getDbo();
			$catid = $this->_getErrorPageCatId();
			$pages = array();
			if (!empty($this->_errorPageCatId))
			{
				// we have cat id, try to read article (only the published ones)
				// unpublished will be processed manually
				$query = $db->getQuery(true);
				$query->select('*')
				      ->from('#__content')
				      ->where($db->qn('catid') . '=' . $catid)
				      ->where($db->qn('title') . '=' . $db->q($pageTitle))
				      ->where($db->qn('state') . '= 1');
				$db->setQuery($query);
				$pages = $db->loadObjectList();
			}

			// if no pages had been created, go away
			// user may have renamed them for instance
			// manual operation needed or just do nothing
			if (empty($pages))
			{
				return true;
			}

			// there were some pages, copy them into keystore
			$defaultLanguageTag = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
			foreach ($pages as $page)
			{
				// "All" language page goes to default language record
				$currentLanguageTag = $page->language == '*' ? $defaultLanguageTag : $page->language;
				$storageKey = 'com_sh404sef.errors.404.' . $currentLanguageTag;

				// is there already such an item?
				$query = $db->getQuery(true);
				$query->select('*')
				      ->from('#__sh404sef_keystore')
				      ->where($db->qn('key') . '=' . $db->q($storageKey));
				$db->setQuery($query);
				$storedPage = $db->loadObject();
				if (!empty($storedPage))
				{
					return;
				}

				// if none, insert
				$query = $db->getQuery(true);
				$query->insert($db->qn('#__sh404sef_keystore'));
				$query->set($db->qn('value') . '=' . $db->q(serialize($page->introtext)))
				      ->set($db->qn('key') . '=' . $db->q($storageKey))
				      ->set($db->qn('user_id') . '=' . JFactory::getUser()->id)
				      ->set($db->qn('format') . '= 0')
				      ->set($db->qn('modified_at') . '=' . $db->q(JFactory::getDate()->format('Y-m-d H:i:s')));
				$db->setQuery($query)->query();

				// lastly, trash the article
				$query = $db->getQuery(true);
				$query->update($db->qn('#__content'))
				      ->where($db->qn('id') . '=' . $db->q($page->id))
				      ->set($db->qn('state') . '= -2');
				$db->setQuery($query)->query();

				// was there a specific Itemid set?
				if (!empty($config->shPageNotFoundItemid))
				{
					$params = Sh404sefHelperGeneral::getComponentParams();
					$fieldName = 'languages_' . $currentLanguageTag . '_notFoundItemid';
					$params->set($fieldName, $config->shPageNotFoundItemid);
					Sh404sefHelperGeneral::saveComponentParams($params);
				}
			}

			// and then trash the category
			$query = $db->getQuery(true);
			$query->update($db->qn('#__categories'))
			      ->where($db->qn('id') . '=' . $db->q($this->_errorPageCatId))
			      ->set($db->qn('published') . '= -2');
			$db->setQuery($query)->query();

			return true;
		}
		catch (Exception $e)
		{
			$app = JFactory::getApplication();
			$app->enqueueMessage('Error: ' . $e->getMessage());

			return false;
		}
	}

	private function _getErrorPageCatId()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id')
		      ->from('#__categories')
		      ->where($db->qn('parent_id') . '= 1')
		      ->where($db->qn('extension') . '=' . $db->q('com_content'))
		      ->where($db->qn('path') . '=' . $db->q('sh404sef-custom-content'))
		      ->where($db->qn('level') . '= 1');
		$db->setQuery($query);
		$this->_errorPageCatId = $db->loadResult();

		// we have a category, all good
		return $this->_errorPageCatId;
	}

	private function _createDBStructure()
	{
		// get a db instance
		$db = JFactory::getDBO();

		$sqlQueries = array(

			"CREATE TABLE IF NOT EXISTS `#__sh404sef_urls` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
        `cpt` INT(11) NOT NULL DEFAULT '0',
        `rank` INT(11) NOT NULL DEFAULT '0',
        `oldurl` VARCHAR(255) NOT NULL DEFAULT '',
        `newurl` VARCHAR(255) NOT NULL DEFAULT '',
        `option` VARCHAR(255) NOT NULL DEFAULT '',
        `referrer_type` TINYINT(3) NOT NULL DEFAULT '0' COMMENT 'Used for 404, 0 = not set, 1 = external, 2 = internal',
        `dateadd` DATE NOT NULL DEFAULT '0000-00-00',
        PRIMARY KEY (`id`),
        KEY `newurl` (`newurl`),
        KEY `rank` (`rank`),
        KEY `oldurl` (`oldurl`)
        ) DEFAULT CHARSET=utf8;",

			"CREATE TABLE IF NOT EXISTS `#__sh404sef_metas` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
        `newurl` VARCHAR(255) NOT NULL DEFAULT '',
        `metadesc` VARCHAR(255) DEFAULT '',
        `metakey` VARCHAR(255) DEFAULT  '',
        `metatitle` VARCHAR(255) DEFAULT '',
        `metalang` VARCHAR(30) DEFAULT '',
        `metarobots` VARCHAR(30) DEFAULT '',
        `canonical` VARCHAR(255) DEFAULT '',
        `og_enable` TINYINT(3) NOT NULL DEFAULT '2',
        `og_type` VARCHAR(30) DEFAULT '',
        `og_image` VARCHAR(255) DEFAULT '',
        `og_enable_description` TINYINT(3) NOT NULL DEFAULT '2',
        `og_enable_site_name` TINYINT(3) NOT NULL DEFAULT '2',
        `og_site_name` VARCHAR(255) DEFAULT '',
        `fb_admin_ids` VARCHAR(255) DEFAULT '',
        `og_enable_location` TINYINT(3) NOT NULL DEFAULT '2',
        `og_latitude` VARCHAR(30) DEFAULT '',
        `og_longitude` VARCHAR(30) DEFAULT '',
        `og_street_address` VARCHAR(255) DEFAULT '',
        `og_locality` VARCHAR(255) DEFAULT '',
        `og_postal_code` VARCHAR(30) DEFAULT '',
        `og_region` VARCHAR(255) DEFAULT '',
        `og_country_name` VARCHAR(255) DEFAULT '',
        `og_enable_contact` TINYINT(3) NOT NULL DEFAULT '2',
        `og_email` VARCHAR(255) DEFAULT '',
        `og_phone_number` VARCHAR(255) DEFAULT '',
        `og_fax_number` VARCHAR(255) DEFAULT '',
        `og_enable_fb_admin_ids` TINYINT(3) NOT NULL DEFAULT '2',
        PRIMARY KEY (`id`),
        KEY `newurl` (`newurl`)
        ) DEFAULT CHARSET=utf8;",

			"CREATE TABLE IF NOT EXISTS `#__sh404sef_aliases` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
        `newurl` VARCHAR(255) NOT NULL DEFAULT '',
        `alias` VARCHAR(255) NOT NULL DEFAULT '',
        `type` TINYINT(3) NOT NULL DEFAULT '0',
        `hits` INT(11) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`),
        KEY `newurl` (`newurl`),
        KEY `alias` (`alias`),
        KEY `type` (`type`)
        ) DEFAULT CHARSET=utf8;",

			"CREATE TABLE IF NOT EXISTS `#__sh404sef_pageids` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
        `newurl` VARCHAR(255) NOT NULL DEFAULT '',
        `pageid` VARCHAR(255) NOT NULL DEFAULT '',
        `type` TINYINT(3) NOT NULL DEFAULT '0',
        `hits` INT(11) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`),
        KEY `newurl` (`newurl`),
        KEY `alias` (`pageid`),
        KEY `type` (`type`)
        ) DEFAULT CHARSET=utf8;",

			"CREATE TABLE IF NOT EXISTS `#__sh404sef_hits_shurls` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
        `url` VARCHAR(333) NOT NULL DEFAULT '',
        `target` VARCHAR(333) NOT NULL DEFAULT '',
        `target_domain` VARCHAR(255) NOT NULL DEFAULT '',
        `referrer` VARCHAR(333) NOT NULL DEFAULT '',
        `referrer_domain` VARCHAR(255) NOT NULL DEFAULT '',
        `user_agent` VARCHAR(255) NOT NULL DEFAULT '',
        `ip_address` VARCHAR(50) NOT NULL DEFAULT '',
        `datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `type` TINYINT(3) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`),
        KEY `url` (`url`),
        KEY `referrer` (`referrer`),
        KEY `user_agent` (`user_agent`),
        KEY `ip_address` (`ip_address`),
        KEY `type` (`type`)
        ) DEFAULT CHARSET=utf8;",

			"CREATE TABLE IF NOT EXISTS `#__sh404sef_hits_aliases` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
        `url` VARCHAR(333) NOT NULL DEFAULT '',
        `target` VARCHAR(333) NOT NULL DEFAULT '',
        `target_domain` VARCHAR(255) NOT NULL DEFAULT '',
        `referrer` VARCHAR(333) NOT NULL DEFAULT '',
        `referrer_domain` VARCHAR(255) NOT NULL DEFAULT '',
        `user_agent` VARCHAR(255) NOT NULL DEFAULT '',
        `ip_address` VARCHAR(50) NOT NULL DEFAULT '',
        `datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `type` TINYINT(3) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`),
        KEY `url` (`url`),
        KEY `referrer` (`referrer`),
        KEY `user_agent` (`user_agent`),
        KEY `ip_address` (`ip_address`),
        KEY `type` (`type`)
        ) DEFAULT CHARSET=utf8;",

			"CREATE TABLE IF NOT EXISTS `#__sh404sef_hits_404s` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
        `url` VARCHAR(333) NOT NULL DEFAULT '',
        `target` VARCHAR(333) NOT NULL DEFAULT '',
        `target_domain` VARCHAR(255) NOT NULL DEFAULT '',
        `referrer` VARCHAR(333) NOT NULL DEFAULT '',
        `referrer_domain` VARCHAR(255) NOT NULL DEFAULT '',
        `user_agent` VARCHAR(255) NOT NULL DEFAULT '',
        `ip_address` VARCHAR(50) NOT NULL DEFAULT '',
        `datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `type` TINYINT(3) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`),
        KEY `url` (`url`),
        KEY `referrer` (`referrer`),
        KEY `user_agent` (`user_agent`),
        KEY `ip_address` (`ip_address`),
        KEY `type` (`type`)
        ) DEFAULT CHARSET=utf8;",

			"CREATE TABLE IF NOT EXISTS `#__sh404sef_urls_src` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
        `url` VARCHAR(333) NOT NULL DEFAULT '',
        `routed_url` VARCHAR(333) NOT NULL DEFAULT '',
        `source_url` VARCHAR(333) NOT NULL DEFAULT '',
        `source_routed_url` VARCHAR(333) NOT NULL DEFAULT '',
        `trace` VARCHAR(10000) NOT NULL DEFAULT '',
        `datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY (`id`),
        KEY `url` (`url`),
        KEY `routed_url` (`routed_url`),
        KEY `source_url` (`source_url`)
        ) DEFAULT CHARSET=utf8;",

			"CREATE TABLE IF NOT EXISTS `#__sh404sef_keystore` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
		`scope` VARCHAR(50) NOT NULL DEFAULT 'default',
        `key` VARCHAR(255) NOT NULL DEFAULT '',
        `value` MEDIUMTEXT NOT NULL,
        `user_id` INT NOT NULL DEFAULT 0,
        `format` TINYINT(3) NOT NULL DEFAULT 1,
        `modified_at` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY (`id`),
        KEY `main` (`scope`,`key`)
        ) ENGINE = InnoDB DEFAULT CHARSET=utf8;"
		);

		foreach ($sqlQueries as $query)
		{
			try
			{
				$db->setQuery($query);
				$db->query();
				$error = $db->getErrorNum();
				if (!empty($error))
				{
					throw new Exception($db->getErrorMsg());
				}
			}
			catch (Exception $e)
			{
				$app = JFactory::getApplication();
				$app
					->enqueueMessage(
						'Error while creating the database : ' . $e->getMessage()
						. '. Sh404SEF will probably not operate properly. Please uninstall it, then try again after checking your database server setup. Contact us in case this happens again.'
					);
			}
		}
	}

	/**
	 * Performs update to db stucture on existing setups
	 */
	private function _updateDBStructure()
	{
		// get a db instance
		$db = JFactory::getDBO();

		/* version 3.4: added OpenGraph data columns to meta data table
		 CREATE TABLE IF NOT EXISTS `#__sh404sef_metas` (
		     `id` int(11) NOT NULL auto_increment,
		     `newurl` varchar(255) NOT NULL default '',
		     `metadesc` varchar(255) default '',
		     `metakey` varchar(255) default  '',
		     `metatitle` varchar(255) default '',
		     `metalang` varchar(30) default '',
		     `metarobots` varchar(30) default '',

		     `og_enable` tinyint(3) NOT NULL DEFAULT '0',
		     `og_type` varchar(30) default '',
		     `og_image` varchar(255) default '',
		     `og_enable_description` tinyint(3) NOT NULL DEFAULT '0',
		     `og_enable_site_name` tinyint(3) NOT NULL DEFAULT '0',
		     `og_site_name` varchar(255) default '',
		     `fb_admin_ids` varchar(255) default '',
		     `og_enable_location` tinyint(3) NOT NULL DEFAULT '0',
		     `og_latitude` varchar(30) default '',
		     `og_longitude` varchar(30) default '',
		     `og_street_address` varchar(255) default '',
		     `og_locality` varchar(255) default '',
		     `og_postal_code` varchar(30) default '',
		     `og_region` varchar(255) default '',
		     `og_country_name` varchar(255) default '',
		     `og_enable_contact` tinyint(3) NOT NULL DEFAULT '0',
		     `og_email` varchar(255) default '',
		     `og_phone_number` varchar(255) default '',
		     `og_fax_number` varchar(255) default '',
		     `og_enable_fb_admin_ids` tinyint(3) NOT NULL DEFAULT '0',
		     `canonical` varchar(255) default '',
		     PRIMARY KEY (`id`),
		     KEY `newurl` (`newurl`)
		 ) DEFAULT CHARSET=utf8;*/

		// when upgrading from older version, these values could be missing at the time of install
		defined('SH404SEF_OPTION_VALUE_NO') or define('SH404SEF_OPTION_VALUE_NO', 0);
		defined('SH404SEF_OPTION_VALUE_YES') or define('SH404SEF_OPTION_VALUE_YES', 1);
		defined('SH404SEF_OPTION_VALUE_USE_DEFAULT') or define('SH404SEF_OPTION_VALUE_USE_DEFAULT', 2);

		// get list of columns
		// platform 12.1
		if (method_exists($db, 'getTableFields'))
		{
			$columns = $db->getTableFields('#__sh404sef_metas');
			$columns = empty($columns['#__sh404sef_metas']) ? array() : $columns['#__sh404sef_metas'];
		}
		else
		{
			$columns = $db->getTableColumns('#__sh404sef_metas');
		}

		// build required statements
		$subQueries = array();
		$subqueries['#__sh404sef_metas'] = array();
		if (empty($columns['canonical']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `canonical` varchar(255) default ''";
		}

		if (empty($columns['og_enable']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `og_enable` tinyint(3) NOT NULL DEFAULT '" . SH404SEF_OPTION_VALUE_USE_DEFAULT . "'";
		}
		if (empty($columns['og_type']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `og_type` varchar(30) default ''";
		}
		if (empty($columns['og_image']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `og_image` varchar(255) default ''";
		}
		if (empty($columns['og_enable_description']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `og_enable_description` tinyint(3) NOT NULL DEFAULT '" . SH404SEF_OPTION_VALUE_USE_DEFAULT . "'";
		}
		if (empty($columns['og_enable_site_name']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `og_enable_site_name` tinyint(3) NOT NULL DEFAULT '" . SH404SEF_OPTION_VALUE_USE_DEFAULT . "'";
		}
		if (empty($columns['og_site_name']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `og_site_name` varchar(255) default ''";
		}
		if (empty($columns['fb_admin_ids']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `fb_admin_ids` varchar(255) default ''";
		}
		if (empty($columns['og_enable_location']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `og_enable_location` tinyint(3) NOT NULL DEFAULT '" . SH404SEF_OPTION_VALUE_USE_DEFAULT . "'";
		}
		if (empty($columns['og_latitude']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `og_latitude` varchar(30) default ''";
		}
		if (empty($columns['og_longitude']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `og_longitude` varchar(30) default ''";
		}
		if (empty($columns['og_street_address']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `og_street_address` varchar(255) default ''";
		}
		if (empty($columns['og_locality']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `og_locality` varchar(255) default ''";
		}
		if (empty($columns['og_postal_code']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `og_postal_code` varchar(30) default ''";
		}
		if (empty($columns['og_region']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `og_region` varchar(255) default ''";
		}
		if (empty($columns['og_country_name']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `og_country_name` varchar(255) default ''";
		}
		if (empty($columns['og_enable_contact']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `og_enable_contact` tinyint(3) NOT NULL DEFAULT '" . SH404SEF_OPTION_VALUE_USE_DEFAULT . "'";
		}
		if (empty($columns['og_email']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `og_email` varchar(255) default ''";
		}
		if (empty($columns['og_phone_number']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `og_phone_number` varchar(255) default ''";
		}
		if (empty($columns['og_fax_number']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `og_fax_number` varchar(255) default ''";
		}
		if (empty($columns['og_enable_fb_admin_ids']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `og_enable_fb_admin_ids` tinyint(3) NOT NULL DEFAULT '" . SH404SEF_OPTION_VALUE_USE_DEFAULT . "'";
		}

		// twitter cards
		if (empty($columns['twittercards_enable']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `twittercards_enable` tinyint(3) NOT NULL DEFAULT '" . SH404SEF_OPTION_VALUE_USE_DEFAULT . "'";
		}
		if (empty($columns['twittercards_site_account']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `twittercards_site_account` varchar(100) default ''";
		}
		if (empty($columns['twittercards_creator_account']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `twittercards_creator_account` varchar(100) default ''";
		}

		// google authorship
		if (empty($columns['google_authorship_enable']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `google_authorship_enable` tinyint(3) NOT NULL DEFAULT '" . SH404SEF_OPTION_VALUE_USE_DEFAULT . "'";
		}
		if (empty($columns['google_authorship_author_profile']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `google_authorship_author_profile` varchar(255) default ''";
		}
		if (empty($columns['google_authorship_author_name']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `google_authorship_author_name` varchar(255) default ''";
		}

		// google publisher
		if (empty($columns['google_publisher_enable']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `google_publisher_enable` tinyint(3) NOT NULL DEFAULT '" . SH404SEF_OPTION_VALUE_USE_DEFAULT . "'";
		}
		if (empty($columns['google_publisher_url']))
		{
			$subQueries['#__sh404sef_metas'][] = "add `google_publisher_url` varchar(255) default ''";
		}

		// some very old install don't have the 'type' or 'hits' columns in aliases table
		$subqueries['#__sh404sef_aliases'] = array();
		if (method_exists($db, 'getTableFields'))
		{
			$columns = $db->getTableFields('#__sh404sef_aliases');
			$columns = empty($columns['#__sh404sef_aliases']) ? array() : $columns['#__sh404sef_aliases'];
		}
		else
		{
			$columns = $db->getTableColumns('#__sh404sef_aliases');
		}
		// aliases type
		if (empty($columns['type']))
		{
			$subQueries['#__sh404sef_aliases'][] = "add `type` tinyint(3) NOT NULL DEFAULT '" . SH404SEF_OPTION_VALUE_NO . "'";
			$subQueries['#__sh404sef_aliases'][] = "add index `type` (`type`)";
		}

		// hits type
		if (empty($columns['hits']))
		{
			$subQueries['#__sh404sef_aliases'][] = "add `hits` int(11) NOT NULL DEFAULT '" . SH404SEF_OPTION_VALUE_NO . "'";
		}

		// 4.7.0 new columns for URLs table
		$subqueries['#__sh404sef_urls'] = array();
		if (method_exists($db, 'getTableFields'))
		{
			$columns = $db->getTableFields('#__sh404sef_urls');
			$columns = empty($columns['#__sh404sef_urls']) ? array() : $columns['#__sh404sef_urls'];
		}
		else
		{
			$columns = $db->getTableColumns('#__sh404sef_urls');
		}

		if (empty($columns['option']))
		{
			$subQueries['#__sh404sef_urls'][] = "add `option` varchar(255) NOT NULL DEFAULT ''";
		}
		if (empty($columns['referrer_type']))
		{
			$subQueries['#__sh404sef_urls'][] = "add `referrer_type` tinyint(3) NOT NULL DEFAULT '" . SH404SEF_OPTION_VALUE_NO . "'";
		}

		// upgrading to 4.7.0, we reset the hit counter of regular URLs.
		// previous counts may/will be wrong because they are the result of 404s
		// (for which hits were counted) turned into valid SEF (for which, up to now)
		// hits were not counted.
		// Thus we set to 0 all `cpt` fields for non-404 URLS (ie: automatic and custom).
		// of course, we must do this only once. We detect this condition by looking for
		// the referrer_type column in the table, which was introduced at the same time
		// as the hit counting feature
		if (empty($columns['referrer_type']))
		{
			try
			{
				$query = 'update ' . $db->qn('#__sh404sef_urls') . ' set ' . $db->qn('cpt') . ' = 0'
					. ' where ' . $db->qn('newurl') . " <> ''";
				$db->setQuery($query);
				$db->query();
				$error = $db->getErrorNum();
				if (!empty($error))
				{
					throw new Exception($db->getErrorMsg());
				}
			}
			catch (Exception $e)
			{
				$app = JFactory::getApplication();
				$app
					->enqueueMessage(
						'Error while upgrading the database : ' . $e->getMessage()
						. '. Sh404SEF will probably not operate properly. Please uninstall it, then try again after checking your database server setup. Contact us in case this happens again.'
					);
			}
		}

		// apply changes
		if (!empty($subQueries))
		{
			try
			{
				foreach ($subQueries as $table => $queries)
				{
					// aggregate sub-queries
					$queries = implode(', ', $queries);

					// prepend query
					$query = 'ALTER TABLE ' . $db->qn($table) . ' ' . $queries;

					// run query
					$db->setQuery($query);
					$db->query();
					$error = $db->getErrorNum();
					if (!empty($error))
					{
						throw new Exception($db->getErrorMsg());
					}
				}
			}
			catch (Exception $e)
			{
				$app = JFactory::getApplication();
				$app
					->enqueueMessage(
						'Error while upgrading the database : ' . $e->getMessage()
						. '. Sh404SEF will probably not operate properly. Please uninstall it, then try again after checking your database server setup. Contact us in case this happens again.'
					);
			}
		}
	}

	/**
	 * Make sure the update site is correct when switching from
	 * one edition to another.
	 * Specifically, wipe out update site when going from an
	 * edition that does auto-update to one that does not
	 * (ie community to full or lite)
	 *
	 * @param unknown $type
	 * @param unknown $parent
	 *
	 * @return boolean
	 */
	private function _processUpdateSite($type, $parent)
	{
		// figure out the extension id
		try
		{
			//$extensionId = (int) ShlDbHelper::selectResult('#__extensions', array('extension_id'),
			//	array('type' => 'component', 'element' => 'com_sh404sef'));
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('extension_id')
			      ->from('#__extensions')
			      ->where($db->qn('type') . ' = ' . $db->q('component'))
			      ->where($db->qn('element') . ' = ' . $db->q('com_sh404sef'));
			$db->setQuery($query);
			$extensionId = $db->loadResult();
		}
		catch (Exception $e)
		{
			$extensionId = 0;
		}
		// no update site. We must make sure we wipe out
		// any existing update site. We use Joomla code for that
		if (!empty($extensionId))
		{
			JPluginHelper::importPlugin('extension');
			if (version_compare(JVERSION, '3', 'ge'))
			{
				$dispatcher = JEventDispatcher::getInstance();
			}
			else
			{
				$dispatcher = JDispatcher::getInstance();
			}
			// Fire the onExtensionAfterInstall
			$result = null;
			$dispatcher->trigger('onExtensionAfterUninstall', array('installer' => clone $parent, 'eid' => $extensionId, 'result' => $result));
		}

		return true;
	}

}
