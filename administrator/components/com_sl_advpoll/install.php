<?php
/**
 * @version		$Id$
 * @author		Pham Minh Tuan (admin@extstore.com)
 * @package		Joomla.Administrator
 * @subpakage	Skyline.Downloads
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

jimport('joomla.filesystem.folder');

/**
 * Skyline Advanced Poll Installer Script
 *
 * @package		Joomla.Install
 * @subpakage	Skyline.AdvPoll
 */
class Com_SL_AdvPollInstallerScript {

	/**
	 * Install.
	 *
	 * @param	$parent
	 */
	public function install($parent) {
		$manifest		= $parent->get("manifest");
		$parent			= $parent->getParent();
		$source			= $parent->getPath("source");
		$module_attr	= $manifest->modules->attributes();
		$module_path	= isset($module_attr['folder']) ? '/' .$module_attr['folder'] : '';
		$plugin_attr	= $manifest->plugins->attributes();
		$plugin_path	= isset($plugin_attr['folder']) ? '/' .$plugin_attr['folder'] : '';

		$installer	= new JInstaller();

		// Install modules.
		foreach ($manifest->modules->module as $module) {
			$attributes	= $module->attributes();
			$path		= $source.$module_path. '/' .$attributes['name'];
			$installer->install($path);
		}

		// Install plugins.
		$plugins	= array();
		$db			= JFactory::getDbo();

		foreach ($manifest->plugins->plugin as $plugin) {
			$attributes	= $plugin->attributes();
			$path		= $source.$plugin_path. '/' .$attributes['group']. '/' .$attributes['name'];
			$installer->install($path);
			$plugins[]	= $db->quote($attributes['name']);
		}

		// Public plugins.
		if (count($plugins)) {
			$query	= 'UPDATE #__extensions'
				. ' SET enabled = 1'
				. ' WHERE element IN (' . implode(', ', $plugins) . ') AND type = \'plugin\' AND enabled = 0'
			;
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Update.
	 *
	 * @param	$parent
	 */
	public function update($installer) {
		if ($this->getCurrentVersion() <= '2.5.1') {
			$this->updateDatabase();
		}
		
		//set default permission
        if ($this->getCurrentVersion() <= '2.6.1') {
           $this->updatePermission();
        }

		return $this->install($installer);
	}

	public function getCurrentVersion() {
		$table = JTable::getInstance('Extension');
		$table->load(array('name' => 'com_sl_advpoll'));
		$registry = new JRegistry($table->manifest_cache);

		$version = $registry->get('version');

		return $version;
	}

	public function updateDatabase() {
		$db = JFactory::getDbo();
		$query = "ALTER TABLE #__sl_advpoll_polls ADD schedule tinyint(1) NOT NULL DEFAULT 0 AFTER state ";
		$db->setQuery($query);
		$db->query($query);

		$query = "ALTER TABLE #__sl_advpoll_polls ADD publish_up datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER schedule ";
		$db->setQuery($query);
		$db->query($query);

		$query = "ALTER TABLE #__sl_advpoll_polls ADD publish_down datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER publish_up ";
		$db->setQuery($query);
		$db->query($query);

		$query = "ALTER TABLE #__sl_advpoll_answers ADD type_answer ENUM('default', 'other') NOT NULL DEFAULT 'default' AFTER title ";
		$db->setQuery($query);
		$db->query($query);
	}
	
	public function updatePermission() {
        $db = JFactory::getDbo();
        $db->getQuery(true);
        $query = 'UPDATE #__assets SET rules = ' . $db->quote('{"core.admin":[],"core.manage":[],"core.create":[],"core.delete":[],"core.edit":[],"core.edit.state":[],"core.edit.own":[],"frontend.vote":{"1":1}}')
            . ' WHERE name = ' . $db->quote('com_sl_advpoll') . ' AND title = ' . $db->quote('com_sl_advpoll');
		$db->setQuery($query);
		$db->query($query);
	}
	
}