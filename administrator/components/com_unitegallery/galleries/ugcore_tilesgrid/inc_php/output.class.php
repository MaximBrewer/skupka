<?php


defined('_JEXEC') or die('Restricted access');


	class UGTilesGridOutput extends UGMainOutput{
		

		/**
		 *
		 * construct the output object
		 */
		public function __construct(){
			
			$this->theme = UGMainOutput::THEME_TILESGRID;
			$this->isTilesType = true;
			
			parent::__construct();
		}		
		
		
		/**
		 * modify optoins
		 */
		protected function modifyOptions(){
			parent::modifyOptions();
			
			$enableNavigation = $this->getParam("custom_enable_navigation", self::FORCE_BOOLEAN);
			
			if($enableNavigation === false)
				$this->arrParams["grid_num_rows"] = 9999;
			
			//treat open at start
			$openAtStart = $this->getParam("theme_open_lightbox_at_start", self::FORCE_BOOLEAN);
			
			if($openAtStart == true){
				$openAt = $this->getParam("theme_auto_open", self::FORCE_NUMERIC);
				$this->arrParams["theme_auto_open"] = $this->getParam("theme_auto_open", self::FORCE_NUMERIC);
			}else
				unset($this->arrParams["theme_auto_open"]);
			
			//grid align
			$position = $this->getParam("position");
			if($position != "center")
				$this->arrParams["theme_grid_align"] = $position;
		}

		
		/**
		 * 
		 * put theme related scripts
		 */
		protected function putScripts($putSkins = true){
			
			parent::putScripts();
			
			if($this->putJsToBody == false)
				HelperGalleryUG::addScriptAbsoluteUrl($this->urlPlugin."themes/tilesgrid/ug-theme-tilesgrid.js", "unitegallery_tilesgrid_theme");
			
		}
		
		
		/**
		 * put javascript includes to the body before the gallery div
		 */
		protected function putJsIncludesToBody(){
			$output = parent::putJsIncludesToBody();
			
			$src = $this->urlPlugin."themes/tilesgrid/ug-theme-tilesgrid.js";
			
			$output .= "<script type='text/javascript' src='{$src}'></script>";
			return($output);
			
		}
		
		
		/**
		 * get theme options override
		 */
		protected function getArrJsOptions(){

			$arr = parent::getArrJsOptions();
			
			$arr[] = $this->buildJsParam("theme_gallery_padding", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("grid_padding", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("grid_num_rows", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			
			$arr[] = $this->buildJsParam("grid_space_between_mobile", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("grid_min_cols", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			
			$arr[] = $this->buildJsParam("theme_navigation_type");
			$arr[] = $this->buildJsParam("theme_arrows_margin_top", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("theme_space_between_arrows", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("theme_bullets_color");
			$arr[] = $this->buildJsParam("bullets_space_between", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("theme_bullets_margin_top", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			
			$arr[] = $this->buildJsParam("theme_auto_open", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("theme_grid_align");
			
			return($arr);
		}
		
		
	}

?>