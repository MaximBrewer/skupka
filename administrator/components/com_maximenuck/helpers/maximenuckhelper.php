<?php

/**
 * @name		Maximenu CK params
 * @package		com_maximenuck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Maximenuck helper.
 */
class MaximenuckHelper {

	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($vName = '') {
		// load jQuery for joomla 2.5
		if (version_compare(JVERSION, '3.0.0') < 0) {
			$doc = JFactory::getDocument();
			$doc->addScript(JUri::base(true) . '/components/com_maximenuck/assets/jquery.min.js');
			$doc->addScript(JUri::base(true) . '/components/com_maximenuck/assets/jquery-noconflict.js');
		}
		JSubMenuHelper::addEntry(
				JText::_('CK_MODULES'), 'index.php?option=com_maximenuck&view=modules', $vName == 'modules'
		);
		JSubMenuHelper::addEntry(
				JText::_('CK_MENUS'), 'index.php?option=com_maximenuck&view=menus', $vName == 'menus'
		);
		JSubMenuHelper::addEntry(
				JText::_('CK_ABOUT') . '<span class="maximenuckchecking" data-name="maximenuck" data-type="component" data-folder=""></span>', 'index.php?option=com_maximenuck&view=about', $vName == 'about'
		);
		JSubMenuHelper::addEntry(
				JText::_('CK_MIGRATION_TOOL'), 'index.php?option=com_maximenuck&view=migration', $vName == 'migration'
		);
		$js_checking = 'jQuery(document).ready(function (){
				jQuery(\'.maximenuckchecking\').each(function(i ,el){
				if (jQuery(el).attr(\'data-name\')) {
					jQuery.ajax({
						type: "POST",
						url: \'' . JUri::root(true) . '/administrator/index.php?option=com_maximenuck&task=check_update\',
						data: {
							name: jQuery(el).attr(\'data-name\'),
							type: jQuery(el).attr(\'data-type\'),
							folder: jQuery(el).attr(\'data-folder\')
						}
					}).done(function(response) {
						response = response.trim();
						if ( response.substring(0,7).toLowerCase() == \'error\' ) {
							alert(response);
							// show_ckmodal(response);
						} else {
							jQuery(el).append(response);
						}
					}).fail(function() {
						//alert(Joomla.JText._(\'CK_FAILED\', \'Failed\'));
					});
				}
				});
			});';
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js_checking);
	}
	
	/**
	 * Check if you have the latest version
	 * 
	 * @return boolean, true if outdated
	 */
	public static function is_outdated() {
		// return version_compare(self::get_latest_version(), self::get_current_version() ) > 0;
	}
	
	/**
	 * Get the release notes content
	 * 
	 * @return false or the file content
	 */
	public static function get_release_notes() {
		// $url = 'http://www.template-creator.com/tck_update.txt';
		// return @file_get_contents($url);
	}

	/**
	 * Check if a new version is available
	 * 
	 * @return false, or the latest version
	 */
	public static function get_latest_version() {
		// $release_notes = self::get_release_notes();
		// $latest_version = false;
		// if ($release_notes !== false) {
			// $test_version = preg_match('/\*(.*?)\n/', $release_notes, $results);
			// $latest_version = trim($results[1]);

		// }

		// return $latest_version;
	}
	
	/*
	 * Get a variable from the manifest file (actually, from the manifest cache).
	 * 
	 * @return the current version
	 */
	public static function get_current_version() {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "com_maximenuck"');
		$manifest = json_decode($db->loadResult(), true);

		return $manifest['version'];
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions() {
		$user = JFactory::getUser();
		$result = new JObject;

		$assetName = 'com_maximenuck';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}

}
