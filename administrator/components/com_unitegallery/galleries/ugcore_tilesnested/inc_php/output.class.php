<?php


defined('_JEXEC') or die('Restricted access');


	class UGTilesNestedOutput extends UGMainOutput{
		

		/**
		 *
		 * construct the output object
		 */
		public function __construct(){
			
			$this->theme = UGMainOutput::THEME_TILES;
			$this->isTilesType = true;
			
			parent::__construct();
		}		
		
		
		/**
		 * 
		 * add justified function
		 */
		protected function modifyOptions(){
			
			parent::modifyOptions();
			$this->arrParams["tiles_type"] = "nested";
			
			//set tiles_align if position is not center
			$position = $this->getParam("position");
			if($position == "left" || $position == "right")
				$this->arrParams["tiles_align"] = $position;
			
			//treat open at start
			$openAtStart = $this->getParam("theme_open_lightbox_at_start", self::FORCE_BOOLEAN);
			
			if($openAtStart == true){
				$openAt = $this->getParam("theme_auto_open", self::FORCE_NUMERIC);
				$this->arrParams["theme_auto_open"] = $this->getParam("theme_auto_open", self::FORCE_NUMERIC);
			}else
				unset($this->arrParams["theme_auto_open"]);
			
		}
		
		
		
		/**
		 * 
		 * put theme related scripts
		 */
		protected function putScripts($putSkins = true){
			
			parent::putScripts();
			
			if($this->putJsToBody == false)
				HelperGalleryUG::addScriptAbsoluteUrl($this->urlPlugin."themes/tiles/ug-theme-tiles.js", "unitegallery_tiles_theme");
			
		}
		
		
		/**
		 * put javascript includes to the body before the gallery div
		 */
		protected function putJsIncludesToBody(){
			$output = parent::putJsIncludesToBody();
			
			$src = $this->urlPlugin."themes/tiles/ug-theme-tiles.js";
			
			$output .= "<script type='text/javascript' src='{$src}'></script>";
			return($output);

		}
		
		
		/**
		 * get theme options override
		 */
		protected function getArrJsOptions(){

			$arr = parent::getArrJsOptions();
			
			$arr[] = $this->buildJsParam("tiles_type");
			$arr[] = $this->buildJsParam("theme_gallery_padding", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			
			$arr[] = $this->buildJsParam("tiles_nested_optimal_tile_width", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("tiles_min_columns", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			
			$arr[] = $this->buildJsParam("tiles_space_between_cols", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("tiles_space_between_cols_mobile", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			
			$arr[] = $this->buildJsParam("theme_enable_preloader", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("theme_preloading_height", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("theme_preloader_vertpos", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			
			$arr[] = $this->buildJsParam("tiles_enable_transition", null, self::TYPE_BOOLEAN);
			
			$arr[] = $this->buildJsParam("tiles_align");	//param not in settings
			
			$arr[] = $this->buildJsParam("theme_auto_open", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			
			return($arr);
		}
		
		
	}

?>