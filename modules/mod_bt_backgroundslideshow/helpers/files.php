<?php

/**
 * @package 	helpers
 * @version		1.0
 * @created		Apr 2012
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */
// No direct access
defined('_JEXEC') or die;

class BTFilesHelper extends JObject {
	static function cleanFiles($items, $moduleId) {
		$files = array();
		if (is_array($items)) {
			foreach ($items as $item) {
				$files[$item->file] = true;
			}
		}

		$dir = JPATH_ROOT . '/modules/mod_bt_backgroundslideshow/images/' . $moduleId;
		self::_cleanFiles($files, $dir . '/manager');
		self::_cleanFiles($files, $dir . '/original');
		self::_cleanFiles($files, $dir . '/slideshow');
		self::_cleanFiles($files, $dir . '/thumbnail');
	}
	static function _cleanFiles($files, $dir) {
		if (!JFolder::exists($dir)) return;
		foreach (JFolder::files($dir) as $file) {
			if (!array_key_exists($file, $files)) {
				JFile::delete($dir . '/' . $file);
			}
		}
	}
}
?>