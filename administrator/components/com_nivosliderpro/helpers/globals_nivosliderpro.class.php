<?php

	class GlobalsNivoPro{
		
		const EXTENSION_NAME = "nivosliderpro"; 
		const COMPONENT_NAME = "com_nivosliderpro";
		
		const TABLE_SLIDERS = "#__nivosliderpro_sliders";
		const TABLE_SLIDES = "#__nivosliderpro_slides";
		
		const VIEW_SLIDER = "slider";
		const VIEW_SLIDERS = "sliders";
		const VIEW_ITEMS = "items";
		
		const LAYOUT_SLIDER_GENERAL = "edit";
		const LAYOUT_SLIDER_VISUAL = "visual";

		public static $urlAssets;
		public static $urlMediaAssets;		
		public static $urlAssetsArrows;
		public static $urlAssetsBullets;
		public static $urlNivoPlugin;
		
		public static $pathAssets;
		public static $pathComponent;
		public static $pathMedia;
		public static $pathAssetsArrows;
		public static $pathAssetsBullets;
		public static $pathViews;
		
		
		/**
		 * 
		 * init globals
		 */
		public static function init(){
			//set global vars
			
			self::$urlAssets = JURI::root()."administrator/components/".self::COMPONENT_NAME."/assets/";
			self::$urlMediaAssets = JURI::root()."media/".self::COMPONENT_NAME."/assets/";
			
			self::$urlAssetsArrows = self::$urlMediaAssets."arrows/";
			self::$urlAssetsBullets = self::$urlMediaAssets."bullets/";
			
			self::$pathComponent = JPATH_ADMINISTRATOR."/components/".self::COMPONENT_NAME."/";
			self::$pathMedia = JPATH_SITE."/media/".self::COMPONENT_NAME."/";
			
			//dmp(self::$pathMedia);exit();
			
			self::$pathAssets = GlobalsNivoPro::$pathComponent."assets/";
			
			self::$pathAssetsArrows = GlobalsNivoPro::$pathMedia."assets/arrows/";
			self::$pathAssetsBullets = GlobalsNivoPro::$pathMedia."assets/bullets/";
			
			self::$pathViews = GlobalsNivoPro::$pathComponent."views/";
			
			self::$urlNivoPlugin = self::$urlMediaAssets."nivo-slider/";
		}
		
	}

?>