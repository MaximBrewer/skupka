<?php

/**
 * @package Unite Nivo Slider Module for Joomla 1.7-2.5
 * @version 1.0.0
 * @author UniteJoomla.com
 * @copyright (C) 2012- Unite Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined('_JEXEC') or die;

	//include nivo files
	$pathIncludes = JPATH_ADMINISTRATOR."/components/com_nivosliderpro/includes.php";
	require_once $pathIncludes;
	
	//set active menu link
	$urlBase = JURI::base();
	
	$sliderID = $params->get("sliderid");
	
	$document = JFactory::getDocument();
	$include_jquery = $params->get("include_jquery","true");
	
	if($include_jquery == "true"){
		
		$jsPrefix = "http";
		if(JURI::getInstance()->isSSL() == true)
			$jsPrefix = "https";
		
		$document->addScript("{$jsPrefix}://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js");
	}
		
	$loadType = $params->get("js_load_type","head");
	$isJSInBody = ($loadType == "body")?true:false;
	$noConflictMode = ($params->get("no_conflict_mode") == "true")?true:false;
	
	HelperNivoPro::outputSlider($sliderID,$isJSInBody,$noConflictMode);
	
	
	