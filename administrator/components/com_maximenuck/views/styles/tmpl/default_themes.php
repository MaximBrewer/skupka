<?php
/**
 * @name		Maximenu CK params
 * @package		com_maximenuck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */
defined('_JEXEC') or die;
$path = JPATH_ROOT . '/modules/mod_maximenuck/themes';
$folders = JFolder::folders($path);
natsort($folders);
$i = 1;
echo '<div class="clearfix" style="min-height:35px;margin: 0 5px;">';
foreach ($folders as $folder) {
	// don't take the custom folder
	if ($folder == 'custom') {
		continue;
	} 
	$theme_title = "";
	if ( file_exists($path . '/' . $folder. '/css/maximenuck.php') ) {
		if ( file_exists($path . '/' . $folder. '/' . $folder . '.png') ) {
			$theme = JUri::root(true) . '/modules/mod_maximenuck/themes/' . $folder . '/' . $folder . '.png';
		} else {
			$theme = Juri::root(true) . '/administrator/components/com_maximenuck/images/what.png" width="110" height="110';
			$theme_title = JText::_('CK_THEME_PREVIEW_NOT_FOUND');
		}
	} else {
		$theme = Juri::root(true) . '/administrator/components/com_maximenuck/images/warning.png" width="110" height="110';
		$theme_title = JText::_('CK_THEME_CSS_NOT_COMPATIBLE');
	}

	echo '<div class="themethumb" data-name="' . $folder . '" onclick="change_theme_stylesheet(\'' . $folder . '\')">'
		. '<img src="' . $theme . '" style="margin:0;padding:0;" title="' . $theme_title . '" class="hasTip" />'
		. '<div class="themename">' . $folder . '</div>'
		. '</div>';
	$i++;
}
echo '</div>';