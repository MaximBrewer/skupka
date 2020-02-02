<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

/**
 * Skyline AdvPoll Factory Class.
 * @package		Joomla.Administrator
 * @subpakage	Skyline.AdvPoll
 */
class SL_AdvPollFactory {
	/**
	 * Get credits footer string.
	 * @return	string
	 */
	public static function getFooter() {
		return '<p class="sl_copyright"><span class="sl_title">Skyline Advanced Poll - Version ' . self::getVersion() . '</span> Copyright &copy; 2012 by <strong>Skyline Software - <a href="http://extstore.com" target="_blank">http://extstore.com</a></strong></p>';
	}

	/**
	 * Get current version of component.
	 */
	public static function getVersion() {
		$table		= JTable::getInstance('Extension');
		$table->load(array('name' => 'com_sl_advpoll'));
		$registry	= new JRegistry($table->manifest_cache);

		return $registry->get('version');
	}

	/**
	 * Get model of component
	 */
	public static function getModel($type, $prefix = 'SL_AdvPollModel', $config = array()) {
		return JModel::getInstance($type, $prefix, $config);
	}
}