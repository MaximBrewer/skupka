<?php

defined('_JEXEC') or die('Restricted access');


	if(!isset($isNewGallery))
		$isNewGallery = false;

	$settingsMain = new UniteGallerySettingsUG();
	$settingsMain->addTextBox("title", "",__("Gallery Title",UNITEGALLERY_TEXTDOMAIN),array("description"=>__("The title of the gallery. Example: Gallery1",UNITEGALLERY_TEXTDOMAIN),"required"=>"true"));
	$settingsMain->addTextBox("alias", "",__("Gallery Alias",UNITEGALLERY_TEXTDOMAIN),array("description"=>__("The alias that will be used for embedding the gallery. Example: gallery1",UNITEGALLERY_TEXTDOMAIN),"required"=>"true", "class"=>"input-alias"));
	
	if($isNewGallery == false){
		$settingsMain = UniteProviderFunctionsUG::addPositionToMainSettings($settingsMain);
		
		//add gallery theme settings
		//add gallery theme settings
		$textChangeTheme = __("Change",UNITEGALLERY_TEXTDOMAIN);
		$params = array("class"=>"small","readonly"=>"true",
				UniteGallerySettingsUG::PARAM_ADDTEXT=>"<a id='ug_button_change_theme' href='javascript:void(0)' class='unite-button-secondary'>$textChangeTheme</a>");
		
		$params[UniteSettingsUG::PARAM_NOSETVAL] = true;
		
		$galleryTypeTitle = GlobalsUGGallery::$gallery->getTypeTitle();
		
		$settingsMain->addTextbox("gallery_theme_info", $galleryTypeTitle, "Gallery Theme",$params);
	}
	
	$settingsMain->addHr();
	
	$settingsMain->addItemsCategorySelect("category", null, $isNewGallery);
	
	if($isNewGallery == false) {
		$settingsMain->addHr();
		$settingsMain->addRadioBoolean("enable_category_tabs", __("Enable Category Tabs", UNITEGALLERY_TEXTDOMAIN), false);
	}
	
	$settingsMain->addHr();
	
	$params = array("class"=>"input-number","unit"=>"px");
	
	$paramsWidth = $params;
	$paramsHeight = $params;
	$paramsWidth[UniteSettingsUG::PARAM_ADDFIELD] = "gallery_height";
	$paramsHeight[UniteSettingsUG::PARAM_NODRAW] = true;
	
	$settingsMain->addRadioBoolean("full_width", "Full Width", false);
	
	$settingsMain->addTextbox("gallery_width", "900", __("Width", UNITEGALLERY_TEXTDOMAIN),$paramsWidth);	
	$settingsMain->addTextbox("gallery_height", "400", __("Height", UNITEGALLERY_TEXTDOMAIN),$paramsHeight);
	
	$settingsMain->addControl("full_width", "gallery_width", "hide", "true");
	
	//in case of existing gallery
	if($isNewGallery == false){
		$settingsMain->addHr();
		
		$settingsMain->addTextbox("gallery_min_width", "150", __("Min. Width", UNITEGALLERY_TEXTDOMAIN),$params);
		$settingsMain->addTextbox("gallery_min_height", "100", __("Min. Height", UNITEGALLERY_TEXTDOMAIN),$params);
	}


?>