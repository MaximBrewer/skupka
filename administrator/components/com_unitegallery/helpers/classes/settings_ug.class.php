<?php


defined('_JEXEC') or die('Restricted access');


class UniteGallerySettingsUG extends UniteSettingsAdvancedUG{
	
	
	/**
	 * add items category select
	 */
	public function addItemsCategorySelect($name = "category", $title = null, $isNewGallery = false){
		
		if($title == null)
			$title = __("Item Category", UNITEGALLERY_TEXTDOMAIN);
		
		$objCategories = new UniteGalleryCategories();
		
		$addType = "empty";
		if($isNewGallery == true)
			$addType = "new";
		
		$arrCats = $objCategories->getCatsShort($addType);
		
		//set selected category
		if($isNewGallery == true)
			$defaultCat = "new";
		else
			$defaultCat = UniteFunctionsUG::getFirstNotEmptyKey($arrCats);
			
		$this->addSelect($name, $arrCats, $title, $defaultCat);
	}
	
	
	/**
	 * get skins array
	 */
	private function getArrSkins($noInherit = false){

		$arrSkins = array();
		if($noInherit == false)
			$arrSkins[""] = __("[Global Skin]", UNITEGALLERY_TEXTDOMAIN);
		
		$arrSkins["default"] = "Default";
		$arrSkins["alexis"] = "Alexis";
		
		return($arrSkins);
	}
	
	/**
	 * add transitions array item to some select
	 */
	public function updateSelectToSkins($name, $default, $noInherit	 = false){
		
		$arrSkins = $this->getArrSkins($noInherit);
		$this->updateSettingItems($name, $arrSkins, $default);
	
	}	

	
	/**
	 * add hidden type option
	 * @param $name
	 */
	public function addHidden( $name ){
		$this->add($name, "", " ", self::TYPE_HIDDEN);
	}
	
	
	/**
	 * update text panel position setting, remove top/bottom from
	 */
	public function helper_updateTextPanelPosition(){
		
		$arrItems = array();
		$arrItems["inside_bottom"] = __("Inside Bottom", UNITEGALLERY_TEXTDOMAIN);
		$arrItems["inside_top"] = __("Inside Top", UNITEGALLERY_TEXTDOMAIN);
		$arrItems["middle"] = __("Middle", UNITEGALLERY_TEXTDOMAIN);
		
		$this->updateSettingItems("tile_textpanel_position", $arrItems);
		
	}
	
	
}

?>
