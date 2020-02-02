<?php

/**
 * @package 	mod_bt_backgroundslideshow - BT Slideshow Pro Module
 * @version		2.1.1
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

require_once 'helpers/helper.php';
$photos = BTBgSlideShowHelper::getPhotos($params);

$dir = JPATH_ROOT . '/modules/mod_bt_backgroundslideshow/images/';
foreach ($photos as $key => $photo) {
  
	if(isset($photo->remote) && $photo->remote){
		if(!JFile::exists($dir . '/manager/' . $photo->file)){
			if(JFile::exists($dir . '/tmp/manager/' . $photo->file)){
				JFile::move($dir . '/tmp/manager/' . $photo->file, $dir . '/manager/' . $photo->file);
				continue;
			}else{
				 unset($photos[$key]);
			}
		}
		continue;
	}

	if (!JFile::exists($dir . '/original/' . $photo->file)) {
		if (JFile::exists($dir . '/tmp/original/' . $photo->file)) {
			JFile::move($dir . '/tmp/original/' . $photo->file, $dir . '/original/' . $photo->file);
			JFile::move($dir . '/tmp/manager/' . $photo->file, $dir . '/manager/' . $photo->file);
		}else{
			unset($photos[$key]);
		}
	}

}
$numberOfPhotos  = count($photos);
if (count($photos) == 0) {
	echo JText::_('MOD_BTBGSLIDESHOW_NOTICE_NO_IMAGES');
	return;
}
$photos = array_values($photos);

$moduleID = $module->id;
$moduleURI = JURI::base()."modules/mod_bt_backgroundslideshow/";
$originalPath = JPATH_BASE . '/modules/mod_bt_backgroundslideshow/images/original/';
$cropImage = $params->get('crop_image', 0);
$cropWidth = $params->get('crop_width', 1600);
$cropHeight = $params->get('crop_height', 1000);
$navType = $params->get('nav-type', 'nav-btn');
$navPosition = $params->get('nav-position', 'fixed');
$thumbNumber = $params->get('thumb_number', 3);
$shvideo = $params->get('shvideo',1);
$controltype = $params->get('controltype',0);
$keepcontrolvideo = $params->get('keepcontrolvideo',0);
$videoquanlity = $params->get('videoquanlity','highres');
$videoautoplay = $params->get('videoautoplay',1);
$videovolume = $params->get('videovolume',100);
$thumbWidth = $params->get('thumb_width', 80);
$thumbHeight = $params->get('thumb_height', 50);
$autoPlay = $params->get('autoplay', 1);
$stopAuto = $params->get('stopAuto', 1);
$wrapperElement = $params->get('wrapper_element', 'body');
$styleSheet = $params->get('stylesheet', '');
$slideshowSpeed = $params->get('slideshowSpeed', 8000);
$effecttype =$params->get('effecttype','fade');
$vAlign = $params->get('vAlign', 't');
$hAlign = $params->get('hAlign', 'l');
$remote = $params->get('remote_image', 0);
if($effecttype == 'slider'){
$slidedirection =$params->get('slidedirection','random');
}
else{
$slidedirection = '';
}
$effectTime = $params->get('effectTime', 2000);
$resizeImage = $params->get('resizeImage', 'auto');
$slideshowWidth = $params->get('slideshowWidth');
$slideshowHeight = $params->get('slideshowHeight');
$slideshowSize = $params->get('slideshowSize', 'window');

$navAlign = $params->get('nav-align', 'center');
$bgoPattern = $params->get('bgo-pattern', '');
$gridOverlay = $bgoPattern != '';
$bgoOpacity = $params->get('bgo-opacity', 1);
if($gridOverlay){
	$bgoPattern = $moduleURI.'tmpl/images/pattern/'.$bgoPattern;
	$styleSheet .= ' .bgd-over-image{background:url("'.$bgoPattern.'") left top repeat;opacity:'.$bgoOpacity.';}';
}



// end new
// Make thumbnail & slideshow images if haven't created or just change the size
require_once (JPATH_ROOT . '/modules/mod_bt_backgroundslideshow/helpers/images.php');
//create image again

if($cropImage){
    $cropPath = JPATH_BASE . '/modules/mod_bt_backgroundslideshow/images/slideshow/';
    $folder = $moduleURI.'images/slideshow/';
	
    foreach($photos as $photo){
		$originalFile = '';
        $file = $cropPath.$photo->file;
		if($remote){
			if(isset($photo->remote)){
				$originalFile = $photo->remote;
			}else{
				$originalFile = $originalPath.$photo->file;
			}
		}else{
			if(file_exists($originalPath.$photo->file)){
				$originalFile = $originalPath.$photo->file;
			}else if(isset($photo->remote)){
				$originalFile = $photo->remote;
			}
		}
        if(file_exists($file)  && $cropWidth != 0 && $cropHeight != 0){
            $imageSize = getimagesize($file);
            if($imageSize[0] != $cropWidth || $imageSize[1] != $cropHeight){
               BTImageHelper::resize($originalFile, $file, $cropWidth, $cropHeight,true,$params->get('jpeg_compression'));
            }
        }else {
            if( $cropWidth != 0 && $cropHeight != 0){
                BTImageHelper::resize($originalFile, $file, $cropWidth, $cropHeight,true,$params->get('jpeg_compression'));
            }else{
                copy($originalFile, $file);
            }
        }
    }
}else{
    $folder = $moduleURI.'images/original/';
}
if($navType=='nav-thumb'){
	$thumbailPath = JPATH_BASE . '/modules/mod_bt_backgroundslideshow/images/thumbnail/';
	$thumbnailLink = $moduleURI.'images/thumbnail/';
	
	foreach($photos as $photo){
		$originalFile = '';
		$file = $thumbailPath.$photo->file;
		if($remote){
			if(isset($photo->remote)){
				$originalFile = $photo->remote;
			}else{
				$originalFile = $originalPath.$photo->file;
			}
		}else{
			if(file_exists($originalPath.$photo->file)){
				$originalFile = $originalPath.$photo->file;
			}else if(isset($photo->remote)){
				$originalFile = $photo->remote;
			}
		}

		if(file_exists($file)){
			$imageSize = getimagesize($file);
			if($imageSize[0] != $thumbWidth || $imageSize[1] != $thumbHeight){
			   BTImageHelper::resize($originalFile, $file, $thumbWidth, $thumbHeight,true,$params->get('jpeg_compression'));
			}
		}else {
			BTImageHelper::resize($originalFile, $file, $thumbWidth, $thumbHeight,true,$params->get('jpeg_compression'));	
		}
	}
}
$caption = $params->get('caption', 0);
$showTitle = false;
$showDesc = false;
$showLink = false;
switch($caption){
	case '1': $showTitle = true; 	break;
	case '2': $showTitle = true;
			  $showLink  = true;	break;
	case '3': $showDesc  = true; 	break;
	case '4': $showTitle = true;
			  $showDesc  = true; 	break;
	case '5': $showTitle = true;
			  $showDesc  = true;
			  $showLink  = true; 	break;

}

BTBgSlideShowHelper::fetchHead( $params );
require JModuleHelper::getLayoutPath('mod_bt_backgroundslideshow', $params->get('layout', 'default'));
?>