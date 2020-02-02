<?php

defined('_JEXEC') or die('Restricted access');

$settings = new UniteGallerySettingsUG();

$isFullVersion = GlobalsUG::$isFullVersion;

if($isFullVersion){
	
	$settings->loadXMLFile(GlobalsUG::$pathFullVersion."helpers/settings/loadmore.xml");
	
}else{
	$settings->loadXMLFile(GlobalsUG::$pathHelpersSettings."loadmore.xml");
}
