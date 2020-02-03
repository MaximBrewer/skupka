<?php
/**
 * @copyright	Copyright (C) 2019. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */
defined('_JEXEC') or die;

use \Slideshowck\CKFolder;

//$path = '/administrator/components/com_slideshowck/presets/';
$folder_path = SLIDESHOWCK_MEDIA_PATH . '/presets/';
$presets = CKFolder::files($folder_path, '.mmck');
natsort($presets);
$i = 1;
echo '<div class="clearfix" style="min-height:35px;margin: 0 5px;">';
foreach ($presets as $preset) {
	$presetName = JFile::stripExt($preset);
	$theme_title = "";
	if ( file_exists($folder_path .$presetName . '.png') ) {
		$theme = SLIDESHOWCK_MEDIA_URL . '/presets/' . $presetName . '.png';
	} else {
		$theme = SLIDESHOWCK_MEDIA_URL . '/images/unknown.png" width="110" height="110';
	}

	echo '<div class="themethumb" data-name="' . $presetName . '" onclick="ckLoadPreset(\'' . $presetName . '\')">'
		. '<div class="themethumbimg">'
		. '<img src="' . $theme . '" style="margin:0;padding:0;" title="' . $theme_title . '" class="hasTip" />'
		. '</div>'
		. '<div class="themename">' . $presetName . '</div>'
		. '</div>';
	$i++;
}
echo '</div>';