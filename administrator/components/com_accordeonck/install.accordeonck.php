<?php
/**
 * @name		Accordeon Menu CK params
 * @package		com_accordeonck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */
 
defined('_JEXEC') or die('Restricted access');
/*
	preflight which is executed before install and update
	install
	update
	uninstall
	postflight which is executed after install and update
	*/

class com_accordeonckInstallerScript {

	function install($parent) {
		
	}

	function update($parent) {
		
	}

	function uninstall($parent) {

	}

	function preflight($type, $parent) {
		return true;
	}

	// run on install and update
	function postflight($type, $parent) {
		// install modules and plugins
		jimport('joomla.installer.installer');
		$db = JFactory::getDbo();
		$status = array();
		$src_ext = dirname(__FILE__).'/extensions';
		$installer = new JInstaller;

		// extensions to install
		// system plugin
		$result = $installer->install($src_ext.'/accordeonckparams');
		$status[] = array('name'=>'System - Accordeon CK Params','type'=>'plugin', 'result'=>$result);
		// system plugin must be enabled for user group limits and private areas
		$db->setQuery("UPDATE #__extensions SET enabled = '1' WHERE `element` = 'accordeonckparams' AND `type` = 'plugin'");
		$db->query();

		foreach ($status as $statu) {
			if ($statu['result'] == true) {
				$alert = 'success';
				$icon = 'icon-ok';
				$text = 'Successful';
			} else {
				$alert = 'warning';
				$icon = 'icon-cancel';
				$text = 'Failed';
			}
			echo '<div class="alert alert-' . $alert . '"><i class="icon ' . $icon . '"></i>Installation and activation of the <b>' . $statu['type'] . ' ' . $statu['name'] . '</b> : ' . $text . '</div>';
		}

		return true;
	}
}
