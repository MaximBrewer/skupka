<?php

defined('_JEXEC') or die('Restricted access');


	class UGMainOutput extends UniteOutputBaseUG{
		
		protected static $serial = 0;
		
		private $gallery;
		protected $urlPlugin;
		private $galleryHtmlID;
		private $galleryID;
		protected $theme;
		private $putNoConflictMode = false;
		protected $putJsToBody = false;
		protected $isTilesType = false;
		protected $arrJsParamsAssoc = array();
		protected $arrLoadMoreCache = null;
		
		
		const THEME_DEFAULT = "default";
		const THEME_COMPACT = "compact";
		const THEME_SLIDER = "slider";
		const THEME_GRID = "grid";
		const THEME_VIDEO = "video";
		const THEME_TILES = "tiles";
		const THEME_TILESGRID = "tilesgrid";
		const THEME_CAROUSEL = "carousel";
		
		
		/**
		 * 
		 * construct the output object
		 */
		public function __construct(){
						
			$this->init();
		}
		
		
		/**
		 * 
		 * init the gallery
		 */
		private function init(){
			
			$urlBase = GlobalsUGGallery::$urlBase;
			if(empty($urlBase))
				UniteFunctionsUG::throwError("The gallery globals object not inited!");
			 
			$this->urlPlugin = GlobalsUG::$url_media_ug;
			
		}
		
		
		/**
		 * get must fields that will be thrown from the settings anyway
		 */
		protected function getArrMustFields(){
			$arrMustKeys = array(
					"category",					
					"gallery_theme",
					"full_width",
					"gallery_width",
					"gallery_height",
					"position",
					"margin_top",
					"margin_bottom",
					"margin_left",
					"margin_right"
			);

			return($arrMustKeys);
		}
		
		
		/**
		 * 
		 * init gallery related variables
		 */
		protected function initGallery($galleryID){
			
			self::$serial++;
			
			$this->gallery = new UniteGalleryGallery();
			$this->gallery->initByID($galleryID);
			
			//get real gallery id: 
			$galleryID = $this->gallery->getID();
			
			$serial = self::$serial;
			$this->galleryID = $galleryID;
			$this->galleryHtmlID = "unitegallery_{$galleryID}_{$serial}";
			
			$origParams = $this->gallery->getParams();
						
			//set params for default settings get function
			$this->arrOriginalParams = $origParams;	
			
			$enableTabs = $this->getParam("enable_category_tabs", self::FORCE_BOOLEAN);
			
			$defaultValues = $this->getDefautSettingsValues();
			
			//get categoty tabs settings:
			if($enableTabs === true){
				$defaultValuesTabs = $this->getDefautSettingsValues_tabs();
				$defaultValues = array_merge($defaultValues, $defaultValuesTabs);
			}
			
			//add advanced settings (instead of merge with setting file):
			$defaultValues["ug_additional_scripts"] = "";
			$defaultValues["ug_additional_styles"] = "";
			
			$origParams = UniteFunctionsUG::filterArrFields($origParams, $defaultValues, true);
			
			$this->arrOriginalParams = array_merge($defaultValues, $origParams);
			
			$arrMustKeys = $this->getArrMustFields();
						
			$this->arrParams = UniteFunctionsUG::getDiffArrItems($this->arrOriginalParams, $defaultValues, $arrMustKeys);
			
			$enableLoadmore = $this->getParam("show_loadmore",self::FORCE_BOOLEAN);
			
			$addAjax = false;
			
			//add tabs related options
			if($enableTabs === true){
				$addAjax = true;
				$this->arrParams["gallery_enable_tabs"] = "true";
			}
			
			//add loadmore related options
			if($enableLoadmore == true){
				$arrLoadmore = $this->getLoadMoreParams();
				$addAjax = true;
				$this->arrParams["gallery_enable_loadmore"] = "true";
				$this->arrParams["loadmore_container"] = $arrLoadmore["wrapper_id"];
			}
			
			if($addAjax == true){
				$this->arrParams["gallery_urlajax"] = GlobalsUG::$url_ajax_front;
			}
			
			$this->modifyOptions();
		}
		
		
		
		/**
		 * modify options
		 */
		protected function modifyOptions(){
			
			if($this->isTilesType == true){
				
				//handle compact lightbox type options
				$lightboxType = $this->getParam("lightbox_type");
				
				if($lightboxType == "compact"){
					
					$this->renameOption("lightbox_compact_overlay_opacity", "lightbox_overlay_opacity", true);
					$this->renameOption("lightbox_compact_overlay_color", "lightbox_overlay_color", true);
					$this->renameOption("lightbox_compact_show_numbers", "lightbox_show_numbers", true);
					$this->renameOption("lightbox_compact_numbers_size", "lightbox_numbers_size", true);
					$this->renameOption("lightbox_compact_numbers_color", "lightbox_numbers_color", true);
					$this->renameOption("lightbox_compact_numbers_padding_top", "lightbox_numbers_padding_top", true);
					$this->renameOption("lightbox_compact_numbers_padding_right", "lightbox_numbers_padding_right", true);
					$this->renameOption("lightbox_compact_show_textpanel", "lightbox_show_textpanel", true);
					$this->renameOption("lightbox_compact_textpanel_source", "lightbox_textpanel_source", true);
					$this->renameOption("lightbox_compact_textpanel_title_color", "lightbox_textpanel_title_color", true);
					$this->renameOption("lightbox_compact_textpanel_title_font_size", "lightbox_textpanel_title_font_size", true);
					$this->renameOption("lightbox_compact_textpanel_title_bold", "lightbox_textpanel_title_bold", true);
					$this->renameOption("lightbox_compact_textpanel_padding_left", "lightbox_textpanel_padding_left", true);
					$this->renameOption("lightbox_compact_textpanel_padding_right", "lightbox_textpanel_padding_right", true);
					$this->renameOption("lightbox_compact_textpanel_padding_top", "lightbox_textpanel_padding_top", true);
					$this->renameOption("lightbox_compact_slider_image_border", "lightbox_slider_image_border", true);
					$this->renameOption("lightbox_compact_slider_image_border_width", "lightbox_slider_image_border_width", true);
					$this->renameOption("lightbox_compact_slider_image_border_color", "lightbox_slider_image_border_color", true);
					$this->renameOption("lightbox_compact_slider_image_border_radius", "lightbox_slider_image_border_radius", true);
					$this->renameOption("lightbox_compact_slider_image_shadow", "lightbox_slider_image_shadow", true);
					
					$this->deleteOption("lightbox_textpanel_title_text_align");
				}else{
					
					//delete all compact related options if exists
					$arrOptionsToDelete = array(
						"lightbox_compact_overlay_opacity",
						"lightbox_compact_overlay_color",
						"lightbox_compact_show_numbers",
						"lightbox_compact_numbers_size",
						"lightbox_compact_numbers_color",
						"lightbox_compact_numbers_padding_top",
						"lightbox_compact_numbers_padding_right",
						"lightbox_compact_show_textpanel",
						"lightbox_compact_textpanel_source",
						"lightbox_compact_textpanel_title_color",
						"lightbox_compact_textpanel_title_font_size",
						"lightbox_compact_textpanel_title_bold",
						"lightbox_compact_textpanel_padding_top",
						"lightbox_compact_textpanel_padding_left",
						"lightbox_compact_textpanel_padding_right",
						"lightbox_compact_slider_image_border",
						"lightbox_compact_slider_image_border_width",
						"lightbox_compact_slider_image_border_color",
						"lightbox_compact_slider_image_border_radius",
						"lightbox_compact_slider_image_shadow"
					);
					
					$this->deleteOptions($arrOptionsToDelete);
				}
				
				//handle text panel source
				$lightboxSource = $this->getParam("lightbox_textpanel_source");
				
				switch($lightboxSource){
					case "desc":
						$this->arrParams["lightbox_textpanel_enable_title"] = "false";
						$this->arrParams["lightbox_textpanel_enable_description"] = "true";
					break;
					case "title_desc":
						$this->arrParams["lightbox_textpanel_enable_description"] = "true";
					break;
				}
				
			}else{
				if($this->isParamExists("strippanel_background_transparent")){
					$isTrans = $this->getParam("strippanel_background_transparent", self::FORCE_BOOLEAN);
					if($isTrans == true)
						$this->arrParams["strippanel_background_color"] = "transparent";
				}
			}
			
			//modify thumb resolution (from old option).
			$resolution = $this->getParam("thumb_resolution");
			if(empty($resolution)){
				$resolution = $this->getParam("tile_image_resolution");
				if(!empty($resolution))
					$this->arrParams["thumb_resolution"] = $resolution;
			}
			
		}
		
		
		/**
		 * get array of skins that exists in the gallery
		 */
		protected function getArrActiveSkins($arrAddOptions = array()){
			
			$gallerySkin = $this->getParam("gallery_skin");
			
			if(empty($gallerySkin))
				$gallerySkin = "default";
			
			$arrSkins = array($gallerySkin=>true);
			
			$arrOptions = array(
					"strippanel_buttons_skin",
					"strippanel_handle_skin",
					"slider_bullets_skin",
					"slider_arrows_skin",
					"slider_play_button_skin",
					"slider_fullscreen_button_skin",
					"slider_zoompanel_skin"
			);
			
			$arrOptions = array_merge($arrOptions, $arrAddOptions);
			
			foreach($arrOptions as $option){
				$skin = $this->getParam($option);
				if(empty($skin))
					continue;
				
				$arrSkins[$skin] = true;
			}
			
			return($arrSkins);
		}
		
		
		/**
		 * 
		 * put gallery scripts
		 */
		protected function putScripts($putSkins = true){
			
			//put jquery
			$includeJQuery = $this->getParam("include_jquery", self::FORCE_BOOLEAN);
			if($includeJQuery == true){
				$urljQuery = GlobalsUG::$url_media_ug."js/jquery-11.0.min.js";
				UniteProviderFunctionsUG::addjQueryInclude("unitegallery", $urljQuery);
			}
			
			if($this->putJsToBody == false)
				HelperGalleryUG::addScriptAbsoluteUrl($this->urlPlugin."js/unitegallery.min.js", "unitegallery_main");
			
			HelperGalleryUG::addStyleAbsoluteUrl($this->urlPlugin."css/unite-gallery.css","unite-gallery-css");
			
			//include skins
			if($putSkins == true){
			
				$arrSkins = $this->getArrActiveSkins();
				
				foreach($arrSkins as $skin => $nothing){
					if(empty($skin) || $skin == "default")
						continue;
					
					HelperGalleryUG::addStyleAbsoluteUrl($this->urlPlugin."skins/{$skin}/{$skin}.css","ug-skin-{$skin}");
				}
			}
			
		}
		
		
		/**
		 * get default settings values
		 * get them only once
		 */
		protected function getDefautSettingsValues(){
			
			require HelperGalleryUG::getFilepathSettings("gallery_settings");
			
			return($valuesMerged);
		}
		
		/**
		 * get default settings of categories
		 */
		protected function getDefautSettingsValues_tabs(){
		
			require GlobalsUG::$pathHelpersSettings."categorytab_main.php";
			require GlobalsUG::$pathHelpersSettings."categorytab_params.php";
			
			// get merged settings with values
			$valuesMain = $settingsMain->getArrValues();
			$valuesParams = $settingsParams->getArrValues();
			$valuesMerged = array_merge($valuesMain, $valuesParams);
			
			return($valuesMerged);
		}
		
		
		/**
		 * get params array defenitions that shouls be put as is from the settings
		 */
		protected function getArrJsOptions(){
			
			$arr = array();
			$arr[] = $this->buildJsParam("gallery_theme");
			$arr[] = $this->buildJsParam("gallery_width", self::VALIDATE_SIZE, self::TYPE_SIZE);
			$arr[] = $this->buildJsParam("gallery_height", self::VALIDATE_SIZE, self::TYPE_SIZE);
			$arr[] = $this->buildJsParam("gallery_min_width", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("gallery_min_height", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("gallery_skin");
			$arr[] = $this->buildJsParam("gallery_images_preload_type");
			$arr[] = $this->buildJsParam("gallery_autoplay", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("gallery_play_interval", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("gallery_pause_on_mouseover", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("gallery_mousewheel_role");
			$arr[] = $this->buildJsParam("gallery_control_keyboard", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("gallery_preserve_ratio", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("gallery_shuffle", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("gallery_debug_errors", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("slider_background_color");
			$arr[] = $this->buildJsParam("slider_background_opacity", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			
			$arr[] = $this->buildJsParam("slider_scale_mode");
			$arr[] = $this->buildJsParam("slider_scale_mode_media");
			$arr[] = $this->buildJsParam("slider_scale_mode_fullscreen");
			
			$arr[] = $this->buildJsParam("slider_transition");
			$arr[] = $this->buildJsParam("slider_transition_speed", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("slider_transition_easing");
			$arr[] = $this->buildJsParam("slider_control_swipe", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("slider_control_zoom", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("slider_zoom_max_ratio", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("slider_enable_links", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("slider_links_newpage", null, self::TYPE_BOOLEAN);
			
			$arr[] = $this->buildJsParam("slider_video_enable_closebutton", null, self::TYPE_BOOLEAN);
			
			$arr[] = $this->buildJsParam("slider_controls_always_on", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("slider_controls_appear_ontap", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("slider_controls_appear_duration", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			
			$arr[] = $this->buildJsParam("slider_loader_type", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("slider_loader_color");
			
			$arr[] = $this->buildJsParam("slider_enable_bullets", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("slider_bullets_skin");
			$arr[] = $this->buildJsParam("slider_bullets_space_between", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("slider_bullets_align_hor");
			$arr[] = $this->buildJsParam("slider_bullets_align_vert");
			$arr[] = $this->buildJsParam("slider_bullets_offset_hor", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("slider_bullets_offset_vert", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);

			$arr[] = $this->buildJsParam("slider_enable_arrows", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("slider_arrows_skin");
			$arr[] = $this->buildJsParam("slider_arrow_left_align_hor");
			$arr[] = $this->buildJsParam("slider_arrow_left_align_vert");
			$arr[] = $this->buildJsParam("slider_arrow_left_offset_hor", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("slider_arrow_left_offset_vert", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("slider_arrow_right_align_hor");
			$arr[] = $this->buildJsParam("slider_arrow_right_align_vert");
			$arr[] = $this->buildJsParam("slider_arrow_right_offset_hor", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("slider_arrow_right_offset_vert", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);

			$arr[] = $this->buildJsParam("slider_enable_progress_indicator", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("slider_progress_indicator_type");
			$arr[] = $this->buildJsParam("slider_progress_indicator_align_hor");
			$arr[] = $this->buildJsParam("slider_progress_indicator_align_vert");
			$arr[] = $this->buildJsParam("slider_progress_indicator_offset_hor", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("slider_progress_indicator_offset_vert", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			
			$arr[] = $this->buildJsParam("slider_progressbar_color");
			$arr[] = $this->buildJsParam("slider_progressbar_opacity", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("slider_progressbar_line_width", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			
			$arr[] = $this->buildJsParam("slider_progresspie_color1");
			$arr[] = $this->buildJsParam("slider_progresspie_color2");
			$arr[] = $this->buildJsParam("slider_progresspie_stroke_width", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("slider_progresspie_width", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("slider_progresspie_height", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			
			$arr[] = $this->buildJsParam("slider_enable_play_button", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("slider_play_button_skin");
			$arr[] = $this->buildJsParam("slider_play_button_align_hor");
			$arr[] = $this->buildJsParam("slider_play_button_align_vert");
			$arr[] = $this->buildJsParam("slider_play_button_offset_hor", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("slider_play_button_offset_vert", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);

			$arr[] = $this->buildJsParam("slider_enable_fullscreen_button", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("slider_fullscreen_button_skin");
			$arr[] = $this->buildJsParam("slider_fullscreen_button_align_hor");
			$arr[] = $this->buildJsParam("slider_fullscreen_button_align_vert");
			$arr[] = $this->buildJsParam("slider_fullscreen_button_offset_hor", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("slider_fullscreen_button_offset_vert", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);

			$arr[] = $this->buildJsParam("slider_enable_zoom_panel", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("slider_zoompanel_skin");
			$arr[] = $this->buildJsParam("slider_zoompanel_align_hor");
			$arr[] = $this->buildJsParam("slider_zoompanel_align_vert");
			$arr[] = $this->buildJsParam("slider_zoompanel_offset_hor", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("slider_zoompanel_offset_vert", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			
			$arr[] = $this->buildJsParam("slider_enable_text_panel", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("slider_textpanel_always_on", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("slider_textpanel_align");
			$arr[] = $this->buildJsParam("slider_textpanel_margin", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("slider_textpanel_text_valign");
			$arr[] = $this->buildJsParam("slider_textpanel_padding_top", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("slider_textpanel_padding_bottom", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("slider_textpanel_height", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("slider_textpanel_padding_title_description", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("slider_textpanel_padding_right", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("slider_textpanel_padding_left", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("slider_textpanel_fade_duration", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("slider_textpanel_enable_title", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("slider_textpanel_enable_description", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("slider_textpanel_enable_bg", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("slider_textpanel_bg_color");
			$arr[] = $this->buildJsParam("slider_textpanel_bg_opacity", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			
			$arr[] = $this->buildJsParam("thumb_width", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("thumb_height", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("thumb_fixed_size", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("thumb_border_effect", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("thumb_border_width", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("thumb_border_color");
			$arr[] = $this->buildJsParam("thumb_over_border_width", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("thumb_over_border_color");
			$arr[] = $this->buildJsParam("thumb_selected_border_width", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("thumb_selected_border_color");
			$arr[] = $this->buildJsParam("thumb_round_corners_radius", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("thumb_color_overlay_effect", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("thumb_overlay_color");
			$arr[] = $this->buildJsParam("thumb_overlay_opacity", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("thumb_overlay_reverse", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("thumb_image_overlay_effect", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("thumb_image_overlay_type");
			$arr[] = $this->buildJsParam("thumb_transition_duration", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("thumb_transition_easing");
			$arr[] = $this->buildJsParam("thumb_show_loader", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("thumb_loader_type");
			
			$arr[] = $this->buildJsParam("strippanel_padding_top", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("strippanel_padding_bottom", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("strippanel_padding_left", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("strippanel_padding_right", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("strippanel_enable_buttons", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("strippanel_buttons_skin");
			$arr[] = $this->buildJsParam("strippanel_padding_buttons", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("strippanel_buttons_role");
			$arr[] = $this->buildJsParam("strippanel_enable_handle", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("strippanel_handle_align");
			$arr[] = $this->buildJsParam("strippanel_handle_offset", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("strippanel_handle_skin");
			$arr[] = $this->buildJsParam("strippanel_background_color");
			$arr[] = $this->buildJsParam("strip_thumbs_align");
			$arr[] = $this->buildJsParam("strip_space_between_thumbs", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("strip_thumb_touch_sensetivity", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("strip_scroll_to_thumb_duration", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("strip_scroll_to_thumb_easing");
			$arr[] = $this->buildJsParam("strip_control_avia", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("strip_control_touch", null, self::TYPE_BOOLEAN);
			
			$arr[] = $this->buildJsParam("gridpanel_vertical_scroll", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("gridpanel_grid_align");
			$arr[] = $this->buildJsParam("gridpanel_padding_border_top", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("gridpanel_padding_border_bottom", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("gridpanel_padding_border_left", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("gridpanel_padding_border_right", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("gridpanel_arrows_skin");
			$arr[] = $this->buildJsParam("gridpanel_arrows_align_vert");
			$arr[] = $this->buildJsParam("gridpanel_arrows_padding_vert", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("gridpanel_arrows_align_hor");
			$arr[] = $this->buildJsParam("gridpanel_arrows_padding_hor", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("gridpanel_space_between_arrows", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("gridpanel_arrows_always_on", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("gridpanel_enable_handle", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("gridpanel_handle_align");
			$arr[] = $this->buildJsParam("gridpanel_handle_offset", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("gridpanel_handle_skin");
			$arr[] = $this->buildJsParam("gridpanel_background_color");
			
			$arr[] = $this->buildJsParam("grid_panes_direction");
			$arr[] = $this->buildJsParam("grid_num_cols", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("grid_space_between_cols", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("grid_space_between_rows", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("grid_transition_duration", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
			$arr[] = $this->buildJsParam("grid_transition_easing");
			$arr[] = $this->buildJsParam("grid_carousel", null, self::TYPE_BOOLEAN);

			//category tabs related
			$arr[] = $this->buildJsParam("gallery_urlajax");
			$arr[] = $this->buildJsParam("gallery_enable_tabs", null, self::TYPE_BOOLEAN);
			$arr[] = $this->buildJsParam("tabs_type");
			$arr[] = $this->buildJsParam("tabs_container");
			$arr[] = $this->buildJsParam("gallery_initial_catid");
			
			//loadmore related
			$arrLoadmore = $this->getLoadMoreParams();
			if($arrLoadmore["enable"] == true){
				$arr[] = $this->buildJsParam("gallery_enable_loadmore", null, self::TYPE_BOOLEAN);
				$arr[] = $this->buildJsParam("loadmore_container");
			}
			
			//tiles type
			if($this->isTilesType == true){
				
				$arr[] = $this->buildJsParam("tile_width", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("tile_height", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("tile_enable_background", null, self::TYPE_BOOLEAN);
				$arr[] = $this->buildJsParam("tile_background_color");
				$arr[] = $this->buildJsParam("tile_enable_border", null, self::TYPE_BOOLEAN);
				$arr[] = $this->buildJsParam("tile_border_width", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("tile_border_color");
				$arr[] = $this->buildJsParam("tile_border_radius", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("tile_enable_outline", null, self::TYPE_BOOLEAN);
				$arr[] = $this->buildJsParam("tile_outline_color");
				$arr[] = $this->buildJsParam("tile_enable_shadow", null, self::TYPE_BOOLEAN);
				$arr[] = $this->buildJsParam("tile_shadow_h", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("tile_shadow_v", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("tile_shadow_blur", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("tile_shadow_spread", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("tile_shadow_color");
				$arr[] = $this->buildJsParam("tile_enable_action");
				$arr[] = $this->buildJsParam("tile_as_link", null, self::TYPE_BOOLEAN);
				$arr[] = $this->buildJsParam("tile_link_newpage", null, self::TYPE_BOOLEAN);
				$arr[] = $this->buildJsParam("tile_enable_overlay", null, self::TYPE_BOOLEAN);
				$arr[] = $this->buildJsParam("tile_overlay_opacity", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("tile_overlay_color");
				$arr[] = $this->buildJsParam("tile_enable_icons", null, self::TYPE_BOOLEAN);
				$arr[] = $this->buildJsParam("tile_show_link_icon", null, self::TYPE_BOOLEAN);
				$arr[] = $this->buildJsParam("tile_space_between_icons");
				$arr[] = $this->buildJsParam("tile_videoplay_icon_always_on");
				$arr[] = $this->buildJsParam("tile_enable_image_effect", null, self::TYPE_BOOLEAN);
				$arr[] = $this->buildJsParam("tile_image_effect_type");
				$arr[] = $this->buildJsParam("tile_image_effect_reverse", null, self::TYPE_BOOLEAN);
				$arr[] = $this->buildJsParam("tile_enable_textpanel", null, self::TYPE_BOOLEAN);
				$arr[] = $this->buildJsParam("tile_textpanel_source");
				$arr[] = $this->buildJsParam("tile_textpanel_position");
				
				$arr[] = $this->buildJsParam("tile_textpanel_always_on", null, self::TYPE_BOOLEAN);
				$arr[] = $this->buildJsParam("tile_textpanel_appear_type");
				$arr[] = $this->buildJsParam("tile_textpanel_offset", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("tile_textpanel_padding_top", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("tile_textpanel_padding_bottom", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("tile_textpanel_padding_left", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("tile_textpanel_padding_right", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("tile_textpanel_bg_color");
				$arr[] = $this->buildJsParam("tile_textpanel_bg_opacity", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("tile_textpanel_title_color");
				$arr[] = $this->buildJsParam("tile_textpanel_title_text_align");
				$arr[] = $this->buildJsParam("tile_textpanel_title_font_size", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("tile_textpanel_title_bold", null, self::TYPE_BOOLEAN);
				$arr[] = $this->buildJsParam("tile_textpanel_desc_bold", null, self::TYPE_BOOLEAN);

				$arr[] = $this->buildJsParam("lightbox_hide_arrows_onvideoplay", null, self::TYPE_BOOLEAN);
				$arr[] = $this->buildJsParam("lightbox_slider_control_swipe", null, self::TYPE_BOOLEAN);
				$arr[] = $this->buildJsParam("lightbox_slider_control_zoom", null, self::TYPE_BOOLEAN);
				$arr[] = $this->buildJsParam("lightbox_close_on_emptyspace", null, self::TYPE_BOOLEAN);
				
				$arr[] = $this->buildJsParam("lightbox_slider_zoom_max_ratio", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("lightbox_slider_transition");
				$arr[] = $this->buildJsParam("lightbox_overlay_opacity", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("lightbox_overlay_color");
				
				$arr[] = $this->buildJsParam("lightbox_top_panel_opacity", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("lightbox_show_numbers", null, self::TYPE_BOOLEAN);
				$arr[] = $this->buildJsParam("lightbox_numbers_size", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("lightbox_numbers_color");
				$arr[] = $this->buildJsParam("lightbox_show_textpanel", null, self::TYPE_BOOLEAN);
				
				$arr[] = $this->buildJsParam("lightbox_textpanel_width", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("lightbox_textpanel_enable_title", null, self::TYPE_BOOLEAN);
				$arr[] = $this->buildJsParam("lightbox_textpanel_enable_description", null, self::TYPE_BOOLEAN);
				
				$arr[] = $this->buildJsParam("lightbox_textpanel_title_color");
				$arr[] = $this->buildJsParam("lightbox_textpanel_title_text_align");
				$arr[] = $this->buildJsParam("lightbox_textpanel_title_font_size", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("lightbox_textpanel_title_bold", null, self::TYPE_BOOLEAN);
				
				$arr[] = $this->buildJsParam("lightbox_textpanel_desc_color");
				$arr[] = $this->buildJsParam("lightbox_textpanel_desc_text_align");
				$arr[] = $this->buildJsParam("lightbox_textpanel_desc_font_size", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("lightbox_textpanel_desc_bold", null, self::TYPE_BOOLEAN);
				
				//lightbox compact related styles
				$arr[] = $this->buildJsParam("lightbox_type");
				$arr[] = $this->buildJsParam("lightbox_arrows_position");
				$arr[] = $this->buildJsParam("lightbox_arrows_inside_alwayson", null, self::TYPE_BOOLEAN);
				$arr[] = $this->buildJsParam("lightbox_numbers_padding_top", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("lightbox_numbers_padding_right", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("lightbox_textpanel_padding_left", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("lightbox_textpanel_padding_right", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("lightbox_textpanel_padding_top", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("lightbox_slider_image_border", null, self::TYPE_BOOLEAN);
				$arr[] = $this->buildJsParam("lightbox_slider_image_border_width", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("lightbox_slider_image_border_color");
				$arr[] = $this->buildJsParam("lightbox_slider_image_border_radius", self::VALIDATE_NUMERIC, self::TYPE_NUMBER);
				$arr[] = $this->buildJsParam("lightbox_slider_image_shadow", null, self::TYPE_BOOLEAN);
				
			}	//tiles type end
			
			
			return($arr);
		}
		
		
		/**
		 * put error message instead of the gallery
		 */
		private function getErrorMessage(Exception $e, $prefix){
			
			$message = $e->getMessage();
			$trace = "";
			if(GlobalsUG::SHOW_TRACE == true)
				$trace = $e->getTraceAsString();
			
			$message = $prefix . ": ".$message;
			
			$output = HelperUG::$operations->getErrorMessageHtml($message, $trace);
			
			return($output);
		}
		
		
		/**
		 * put javascript includes to the body before the gallery div
		 */
		protected function putJsIncludesToBody(){
			$src = $this->urlPlugin."js/unitegallery.min.js";
			$html = "\n <script type='text/javascript' src='{$src}'></script>";
			
			return($html);
		}
		
		

		
		/**
		 * put gallery items
		 */
		protected function putItems($arrItems){
			
			$thumbSize = $this->getParam("thumb_resolution");
			$bigImageSize = $this->getParam("big_image_resolution");
			
			$thumbSizeMobile = $this->getParam("thumb_resolution_mobile");
			$bigImageSizeMobile = $this->getParam("big_image_resolution_mobile");
			
			$objItems = new UniteGalleryItems();
			
			$htmlItems =  $objItems->getItemsHtmlFront($arrItems, $thumbSize, $bigImageSize, $this->isTilesType, $thumbSizeMobile, $bigImageSizeMobile);
			
			return($htmlItems);
		}
		
		
		/**
		 * set gallery output options like put js to body etc.
		 */
		protected function setOutputOptions(){
			
			$jsToBody = $this->getParam("js_to_body", self::FORCE_BOOLEAN);
			$this->putJsToBody = $jsToBody;
						
		}
		
		/**
		 * put inline styles
		 */
		protected function putInlineStyle($style, $output){
			
			//put in the body or add to inline
			$putStylesInBody = $this->getParam("tab_put_styles_in_body", self::FORCE_BOOLEAN);
			if($putStylesInBody == true)
				$output .= "\n<style type='text/css'>{$style}</style>\n\n";
			else
				HelperUG::addStyleInline($style);
			
			return($output);
		}
		
		
		/**
		 * get category tabs html
		 */
		protected function getCategoryTabsHtml_tabs($galleryHtmlID, $objCategories){
			
			$categories = $this->getParam("categorytabs_ids");
			if(empty($categories))
				return("");
			
			$tabsID = $galleryHtmlID."_tabs";
			$this->arrParams["tabs_container"] = "#".$tabsID;
			
			$output = "";
			
			//make inner style
			$arrStyleWrapper = array();
			$arrStyleTab = array();
			$arrStyleTabHover = array();
			$arrStyleTabSelected = array();
			
			//make wrapper style
			$position = $this->getParam("tabs_position");
			if($position == "left" || $position == "right"){
				$arrStyleWrapper = $this->addParamToStyleArray($arrStyleWrapper, "tabs_position", "text-align");
				$arrStyleWrapper = $this->addParamToStyleArray($arrStyleWrapper, "tabs_offset", "padding-{$position}", "px", self::FORCE_NUMERIC);
			}
			
			$arrStyleWrapper = $this->addParamToStyleArray($arrStyleWrapper, "tabs_margin_top", "margin-top", "px", self::FORCE_NUMERIC);
			$arrStyleWrapper = $this->addParamToStyleArray($arrStyleWrapper, "tabs_margin_bottom", "margin-bottom", "px", self::FORCE_NUMERIC);
			
			//make tab style
			
			//space between tabs
			$arrStyleTab = $this->addParamToStyleArray($arrStyleTab, "tabs_space_between", "margin-left", "px", self::FORCE_NUMERIC);
			
			//tab padding
			$arrStyleTab = $this->addParamToStyleArray($arrStyleTab, "tab_padding_vert", "padding-top", "px", self::FORCE_NUMERIC);
			$arrStyleTab = $this->addParamToStyleArray($arrStyleTab, "tab_padding_vert", "padding-bottom", "px", self::FORCE_NUMERIC);
			$arrStyleTab = $this->addParamToStyleArray($arrStyleTab, "tab_padding_hor", "padding-left", "px", self::FORCE_NUMERIC);
			$arrStyleTab = $this->addParamToStyleArray($arrStyleTab, "tab_padding_hor", "padding-right", "px", self::FORCE_NUMERIC);
			
			//tab style
			$arrStyleTab = $this->addParamToStyleArray($arrStyleTab, "tab_background_color", "background-color");
			$arrStyleTab = $this->addParamToStyleArray($arrStyleTab, "tab_text_color", "color");
			$arrStyleTab = $this->addParamToStyleArray($arrStyleTab, "tab_text_size", "font-size","px",self::FORCE_NUMERIC);
			$arrStyleTab = $this->addParamToStyleArray($arrStyleTab, "tab_font_family", "font-family");
			$arrStyleTab = $this->addParamToStyleArray($arrStyleTab, "tab_font_weight", "font-weight");
			$arrStyleTab = $this->addParamToStyleArray($arrStyleTab, "tab_border_radius", "border-radius", "px", self::FORCE_NUMERIC);
			
			//tab border
			$enableBorder = $this->getParam("tab_enable_border", self::FORCE_BOOLEAN);
			if($enableBorder == true){
				$arrStyleTab = $this->addParamToStyleArray($arrStyleTab, "tab_border_width", "border-width", "px", self::FORCE_NUMERIC);
				$arrStyleTab = $this->addParamToStyleArray($arrStyleTab, "tab_border_color", "border-color");
				$arrStyleTab["border-style"] = "solid";
			}
			
			//tab mouseover
			$enableHoverBG = $this->getParam("tab_hover_background_change", self::FORCE_BOOLEAN);
			if($enableHoverBG == false)
				$arrStyleTabHover["background-color"] = $this->getParam("tab_background_color")." !important";
			else
				$arrStyleTabHover = $this->addParamToStyleArray($arrStyleTabHover, "tab_hover_background_color", "background-color"," !important");
			
			$enableHoverColor = $this->getParam("tab_hover_color_change", self::FORCE_BOOLEAN);
			if($enableHoverColor == true)
				$arrStyleTabHover = $this->addParamToStyleArray($arrStyleTabHover, "tab_hover_text_color", "color"," !important");
			
			$enableHoverColor = $this->getParam("tab_hover_bordercolor_change", self::FORCE_BOOLEAN);
			if($enableHoverColor == true)
				$arrStyleTabHover = $this->addParamToStyleArray($arrStyleTabHover, "tab_hover_border_color", "border-color"," !important");
			
			//tab selected:
			$arrStyleTabSelected = $this->addParamToStyleArray($arrStyleTabSelected, "tab_selected_background_color", "background-color"," !important");
			
			$enableSelectedColor = $this->getParam("tab_selected_color_change", self::FORCE_BOOLEAN);
			if($enableSelectedColor == true)
				$arrStyleTabSelected = $this->addParamToStyleArray($arrStyleTabSelected, "tab_selected_text_color", "color"," !important");
			
			
			$enableSelectedBorderColor = $this->getParam("tab_selected_bordercolor_change", self::FORCE_BOOLEAN);
			if($enableSelectedBorderColor == true)
				$arrStyleTabSelected = $this->addParamToStyleArray($arrStyleTabSelected, "tab_selected_border_color", "border-color"," !important");
			
			$addCSSTab = $this->getParam("tab_additional_css");
			$addCSSTabHover = $this->getParam("tab_hover_additional_css");
			$addCSSTabSelected = $this->getParam("tab_selected_additional_css");
			
			
			//make style strings
			$strStyleWrapper = UniteFunctionsUG::arrStyleToStrStyle($arrStyleWrapper, "#".$tabsID.".ug-tabs-wrapper");
			$strStyleTab = UniteFunctionsUG::arrStyleToStrStyle($arrStyleTab, "#".$tabsID.".ug-tabs-wrapper a.ug-tab", $addCSSTab);
			$strStyleTabHover = UniteFunctionsUG::arrStyleToStrStyle($arrStyleTabHover, "#".$tabsID.".ug-tabs-wrapper a.ug-tab:hover:not(.ug-tab-selected)", $addCSSTabHover);
			$strStyleTabSelected = UniteFunctionsUG::arrStyleToStrStyle($arrStyleTabSelected, "#".$tabsID.".ug-tabs-wrapper a.ug-tab.ug-tab-selected", $addCSSTabSelected);
			
			$style = $strStyleWrapper.$strStyleTab.$strStyleTabHover.$strStyleTabSelected;
			
			$output = "";
			
			$output = $this->putInlineStyle($style, $output);

			$arrCats = $objCategories->getListByIds($categories);
			
			$output .= "<div id=\"{$tabsID}\" class=\"ug-tabs-wrapper\">";
			
			$isFirstSelected = false;
			$selectedCat = $this->getParam("tabs_init_catid");
			if($selectedCat == "first")
				$isFirstSelected = true;
			
			foreach ( $arrCats as $category ) {
			
				$title = UniteFunctionsUG::getVal($category, "title");
				$id = UniteFunctionsUG::getVal($category, "id");
			
				$class = "";
				if($isFirstSelected == true){
					$isFirstSelected = false;
					$class = " ug-tab-selected";
				}else{
					if($id == $selectedCat)
						$class = " ug-tab-selected";
				}
			
				$output .= "<a class=\"ug-tab{$class}\" href=\"javascript:void(0)\" data-catid=\"{$id}\">{$title}</a>";
			}
			
			$output .= "</div>";
			
			return($output);
		}
		
		
		/**
		 * get category select html 
		 */
		protected function getCategoryTabsHtml_select($galleryHtmlID, $objCategories){
			
			$output = "";
			
			$categories = $this->getParam("categorytabs_ids");
			if(empty($categories))
				return("");

			$wrapperID = $galleryHtmlID."_tabs_wrapper";
			$selectID = $galleryHtmlID."_tabs_select";
			
			//set styling - to the wrapper
			
			//style position
			
			$arrStyleWrapper = array();
			$arrStyleSelect = array();
			
			$setPosition = $this->getParam("tab_selectbox_set_position",self::FORCE_BOOLEAN);
			
			$position = $this->getParam("tabs_selectbox_position");
			if($position == "left" || $position == "right"){
				$arrStyleWrapper = $this->addParamToStyleArray($arrStyleWrapper, "tabs_selectbox_position", "text-align");
				$arrStyleWrapper = $this->addParamToStyleArray($arrStyleWrapper, "tabs_selectbox_offset", "padding-{$position}", "px", self::FORCE_NUMERIC);
			}
			
			$arrStyleWrapper = $this->addParamToStyleArray($arrStyleWrapper, "tabs_selectbox_margin_top", "margin-top", "px", self::FORCE_NUMERIC);
			$arrStyleWrapper = $this->addParamToStyleArray($arrStyleWrapper, "tabs_selectbox_margin_bottom", "margin-bottom", "px", self::FORCE_NUMERIC);
			
			
			//style border
			$styleBorder = $this->getParam("tab_style_selectbox_border",self::FORCE_BOOLEAN);
			
			if($styleBorder == true){
				$borderColor = $this->getParam("tab_selectbox_border_color");
				if(!empty($borderColor))
					$arrStyleSelect["border"] = "1px solid {$borderColor}";
				
				//tab_selectbox_border_radius
				$arrStyleSelect = $this->addParamToStyleArray($arrStyleSelect, "tab_selectbox_border_radius", "border-radius", "px", self::FORCE_NUMERIC);
				
				$showOutline = $this->getParam("tab_selectbox_show_outline",self::FORCE_BOOLEAN);
				if($showOutline == false)
					$arrStyleSelect["outline"] = "none";
			}
			
			//style text
			
			$styleText = $this->getParam("tab_style_selectbox_text",self::FORCE_BOOLEAN);
			if($styleText == true){

				$textColor = $this->getParam("tab_selectbox_color");
				$arrStyleSelect = $this->addParamToStyleArrayForce($arrStyleSelect, "tab_selectbox_color", "color");
				$arrStyleSelect = $this->addParamToStyleArrayForce($arrStyleSelect, "tab_selectbox_font_size", "font-size","px");
				$arrStyleSelect = $this->addParamToStyleArrayForce($arrStyleSelect, "tab_selectbox_font_weight", "font-weight");
			}
			
			//change size
			
			$changeSize = $this->getParam("tab_style_selectbox_size",self::FORCE_BOOLEAN);
			if($changeSize == true){
				
				$arrStyleSelect = $this->addParamToStyleArrayForce($arrStyleSelect, "tab_selectbox_width", "width","px");
				$arrStyleSelect = $this->addParamToStyleArrayForce($arrStyleSelect, "tab_selectbox_height", "height","px");
				
			}
			
			//concat styles
			
			$strStyleWrapper = "";
			if(!empty($arrStyleWrapper))
				$strStyleWrapper = UniteFunctionsUG::arrStyleToStrStyle($arrStyleWrapper, "#".$wrapperID.".ug-tabs-wrapper");
			
			$addCSSSelect = $this->getParam("tab_selectbox_additional_css");
			$strStyleSelect = UniteFunctionsUG::arrStyleToStrStyle($arrStyleSelect, "#".$selectID.".ug-tabs-select", $addCSSSelect);
			
			$addCssOption = $this->getParam("tab_selectbox_option_additional_css");
			$addCssOption = trim($addCssOption);
			$strStyleOption = "";
			
			if(!empty($addCssOption)){
				$strStyleOption .= "#".$selectID.".ug-tabs-select option{ {$addCssOption} }";
			}
			
			$style = $strStyleWrapper.$strStyleSelect.$strStyleOption;
			
			//put html select
			$output = $this->putInlineStyle($style, $output);
			
			$this->arrParams["tabs_container"] = "#".$selectID;
			
			$arrCats = $objCategories->getListByIds($categories);

			$output .= "<div id=\"{$wrapperID}\" class=\"ug-tabs-wrapper\">";
			$output .= "<select id=\"{$selectID}\" class=\"ug-tabs-select\">";
			
			$isFirstSelected = false;
			$selectedCat = $this->getParam("tabs_init_catid");
			if($selectedCat == "first")
				$isFirstSelected = true;
			
			foreach ( $arrCats as $category ) {
			
				$title = UniteFunctionsUG::getVal($category, "title");
				$id = UniteFunctionsUG::getVal($category, "id");
			
				$selected = "";
				if($isFirstSelected == true){
					$isFirstSelected = false;
					$selected = " selected='selected'";
				}else{
					if($id == $selectedCat)
						$selected = " selected='selected'";
				}
			
				$output .= "<option{$selected} class=\"ug-option\" value=\"{$id}\">{$title}</option>";
			}
			
			$output .= "</select>";
			$output .= "</div>";
			
						
			return $output;
		}
		
		
		/**
		 * output categories
		 */
		protected function getCategoryTabsHtml($galleryHtmlID, $objCategories ){
			
			$tabsType = $this->getParam("tabs_type");
			
			switch($tabsType){
				default:
				case "tabs":
					$output = $this->getCategoryTabsHtml_tabs($galleryHtmlID, $objCategories);
				break;
				case "select":
					$output = $this->getCategoryTabsHtml_select($galleryHtmlID, $objCategories);
				break;
			}
		
			$output .= self::BR;
			
			return $output;
		}
		
		
		
		/**
		 * get additional scripts
		 */
		protected function getAdditionalScripts($serial){
			
			$addScripts = $this->getParam("ug_additional_scripts");
			$addScripts = trim($addScripts);
			if(empty($addScripts))
				return($addScripts);
			
			//add tabs prefix to each line
			$addScripts = UniteFunctionsUG::addPrefixToEachLine($addScripts, self::LINE_PREFIX4);
			
			//replace API tab
			$varAPI = "ugapi".$serial;
			$addScripts = str_replace("[api]", $varAPI, $addScripts);
			
			return($addScripts);
		}
		
		
		/**
		 * get add styles in themes - text panel 
		 */
		protected function getAdditionalStyles_themes(){
			
			$addStyles = "";
			
			$cssTitle = $this->getParam("slider_textpanel_css_title", self::TRIM);
			
			$cssTitle = UniteFunctionsUG::jsonToCss($cssTitle);
			if(!empty($cssTitle)){
				$cssTitle = "[galleryid] .ug-textpanel-title{ {$cssTitle} }";
				$addStyles .= "\n" . $cssTitle;
			}
			
			//---- text panel description
			$cssDesc = $this->getParam("slider_textpanel_css_description", self::TRIM);
			
			$cssDesc = UniteFunctionsUG::jsonToCss($cssDesc);
			if(!empty($cssDesc)){
				$cssDesc = "[galleryid] .ug-textpanel-description{ {$cssDesc} }";
				$addStyles .= "\n" . $cssDesc;
			}
			
			
			return($addStyles);
		}
		
		
		/**
		 * get additional style in tiles types - text panel
		 */
		protected function getAdditionalStyles_tiles(){
			
			$addStyles = "";
			
			$cssTitle = $this->getParam("tile_textpanel_additional_css", self::TRIM);
			$cssDesc = $this->getParam("tile_textpanel_desc_additional_css", self::TRIM);
			
			$css = "";
			if(!empty($cssTitle)){
				$css .= "[galleryid] .ug-thumb-wrapper .ug-textpanel-title{ {$cssTitle} }";
			}
			
			if(!empty($cssDesc)){
				$css .= "\n"."[galleryid] .ug-thumb-wrapper .ug-textpanel-description{ {$cssDesc} }";
			}

			
			//------- lightbox text panel description
			$enableLightboxDesc = $this->getParam("lightbox_textpanel_enable_description", self::FORCE_BOOLEAN);
			$cssLightboxDesc = $this->getParam("lightbox_textpanel_desc_addcss", self::TRIM);
			
			if($enableLightboxDesc == true && !empty($cssLightboxDesc)){
				$css .= "\n".".ug-lightbox .ug-textpanel .ug-textpanel-description{ {$cssLightboxDesc} }";
			}
			
			if(!empty($css))
				$addStyles .= "\n" . $css;
			
			
			return($addStyles);
		}
		
		
		/**
		 * get additional styles
		 */
		protected function getAdditionalStyles(){
			
			$addStyles = "";
			
			//add css from options (text panel)
			
			if($this->isTilesType == true)
				$addStyles .= $this->getAdditionalStyles_tiles();
			else 	
				$addStyles .= $this->getAdditionalStyles_themes();
			
			//additional styles from advaced view
			$advancedStyles = $this->getParam("ug_additional_styles",self::TRIM); 
			
			if(!empty($advancedStyles))
				$addStyles .= "\n".$advancedStyles;
			
			//add loadmore styles
			$arrLoadmore = $this->getLoadMoreParams();
			if($arrLoadmore["enable"] == true){
				$loadmoreStyles = $this->getLoadmoreStyles($arrLoadmore);
				if(!empty($loadmoreStyles))
					$addStyles .= "\n".$loadmoreStyles;
			}
			
			$addStyles = trim($addStyles);
			
			if(empty($addStyles))
				return($addStyles);
			
			$replaceID = "#".$this->galleryHtmlID;
			
			$addStyles = str_replace("[galleryid]", $replaceID, $addStyles);
			
			$addStyles = "\n/* unite gallery additional styles */ \n".$addStyles;
			
			return($addStyles);
		}
		
		
		private function a__________LOADMORE___________(){}
		
		
		/**
		 * get loadmore styles
		 */
		protected function getLoadmoreStyles($arrLoadmore){
			
			//get styles from params
			$arrStyleNormal = array();
			$arrStyleNormal = $this->addParamToStyleArray($arrStyleNormal, "loadmore_text_color", "color");
			$arrStyleNormal = $this->addParamToStyleArray($arrStyleNormal, "loadmore_border_color", "border-color");
			
			$arrStyleHover = array();
			$arrStyleHover = $this->addParamToStyleArray($arrStyleHover, "loadmore_hover_back_color", "background-color");
			
			//get add styles
			$addStylesNormal = UniteFunctionsUG::getVal($this->arrParams, "loadmore_button_styles");
			$addStylesHover = UniteFunctionsUG::getVal($this->arrParams, "loadmore_button_hover_styles");
			$addStylesLoader = UniteFunctionsUG::getVal($this->arrParams, "loadmore_loader_styles");
			
			//get the wrapper
			$wrapperID = $arrLoadmore["wrapper_id"];
			
			//make style strings
			$strStyleNormal = UniteFunctionsUG::arrStyleToStrStyle($arrStyleNormal, "#{$wrapperID} .ug-loadmore-button", $addStylesNormal);
			$strStylesHover = UniteFunctionsUG::arrStyleToStrStyle($arrStyleHover, "#{$wrapperID} .ug-loadmore-button:hover", $addStylesHover);
			$strStylesLoader = UniteFunctionsUG::arrStyleToStrStyle(array(), "#{$wrapperID} .ug-loadmore-loader", $addStylesLoader);
			
			//combine styles
			$strStyles = $strStyleNormal.$strStylesHover.$strStylesLoader;
			
			return($strStyles);
		}
		
		
		/**
		 * get load more html
		 */
		protected function getLoadMoreHtml($arr){
		
			$wrapperID = $arr["wrapper_id"];
			
			$buttonText = $arr["button_text"];
			$loaderText = $arr["loader_text"];
			
			
			$html = "";
			$html .= self::LINE_PREFIX1."<div id=\"{$wrapperID}\" class=\"ug-loadmore-wrapper\" style=\"display:none\">";
			$html .= self::LINE_PREFIX1."	<a href=\"javascript:void(0)\" class=\"ug-loadmore-button\">".$buttonText."</a>";
			$html .= self::LINE_PREFIX1."	<span class=\"ug-loadmore-loader\" style=\"display:none\">".$loaderText."</span>";
			$html .= self::LINE_PREFIX1."	<span class=\"ug-loadmore-error\" style=\"display:none\"></span>";
			$html .= self::LINE_PREFIX1."</div>";
		
			return($html);
		}
		
		
		/**
		 * get load more parameters
		 */
		protected function getLoadMoreParams(){
			
			if(!empty($this->arrLoadMoreCache))
				return($this->arrLoadMoreCache);
			
			$output = array();
		
			$enableLoadmore = $this->getParam("show_loadmore", self::FORCE_BOOLEAN);
			
			$output["enable"] = $enableLoadmore;
			
			if($enableLoadmore == false)
				return($output);
						
			//get extra params
			$output["wrapper_id"] = "ug_loadmore_wrapper_".self::$serial;
			$output["max_items"] = $this->getParam("loadmore_max_items", self::VALIDATE_NUMERIC);
			$output["min_items"] = $this->getParam("loadmore_min_items", self::VALIDATE_NUMERIC);
		
			$output["button_text"] = $this->getParam("loadmore_button_text", self::VALIDATE_EXISTS);
			$output["loader_text"] = $this->getParam("loadmore_loader_text", self::VALIDATE_EXISTS);
			
			$this->arrLoadMoreCache = $output;
			
			return($output);
		}
		
		
		/**
		 * disable load more if enabled
		 */
		private function disableLoadMore(){
			
			if($this->arrLoadMoreCache)
				$this->arrLoadMoreCache["enable"] = false;
			
		}
		
		
		private function a__________STOP_LOADMORE___________(){}
		
		
		/**
		 * add js loaded validation
		 */
		protected function addJsLoadedValidationScripts($output){
			
			
			$galleryHtmlID = $this->galleryHtmlID;
			
			if(GlobalsUG::$isScriptsInFooter == true)
				$output .= self::LINE_PREFIX1."<script type='text/javascript'>";			
			
			$output .= self::LINE_PREFIX2."window.onload = function(e) {";
			
			$output .= self::LINE_PREFIX3."if(typeof ugCheckForErrors == \"undefined\"){";
			
			$notIncludedMessage = "Unite Gallery Error - gallery js and css files not included";
			
			if(method_exists("UniteProviderFunctionsUG", "getJsNotIncludedErrorMessage"))
				$notIncludedMessage = UniteProviderFunctionsUG::getJsNotIncludedErrorMessage();
			
			$output .= self::LINE_PREFIX4."document.getElementById(\"{$galleryHtmlID}\").innerHTML = \"<span style='color:red'>{$notIncludedMessage}</span>\";}";
			$output .= self::LINE_PREFIX3."else{ ugCheckForErrors(\"{$galleryHtmlID}\", \"jquery\");}";
			$output .= self::LINE_PREFIX2."};";
			
			if(GlobalsUG::$isScriptsInFooter == true)
				$output .= self::LINE_PREFIX1."</script>";
			
			
			return($output);
		}

		
		/**
		 * put scripts
		 */
		protected function putGalleryScripts($output){
			
			$jsOptions = $this->buildJsParams();
			
			$addScripts = $this->getAdditionalScripts(self::$serial);
			$hasAddScripts = !empty($addScripts);
			
			$serial = self::$serial;
			$galleryHtmlID = $this->galleryHtmlID;
			
			$scriptOutput = "";
			
			if(GlobalsUG::$isScriptsInFooter == false)
				$output .= self::LINE_PREFIX1."<script type='text/javascript'>";
			
			//check js loaded only once
			if($serial == 1)
				$output = $this->addJsLoadedValidationScripts($output);
			
			if($this->putNoConflictMode == true)
				$scriptOutput .= self::LINE_PREFIX2."jQuery.noConflict();";
			
			$scriptOutput .= self::LINE_PREFIX2."var ugapi{$serial};";
			$scriptOutput .= self::LINE_PREFIX2."jQuery(document).ready(function(){";
			$scriptOutput .= self::LINE_PREFIX3."var objUGParams = {";
			$scriptOutput .= self::LINE_PREFIX4.$jsOptions;
			$scriptOutput .= self::LINE_PREFIX3."};";
			
			$scriptOutput .= self::LINE_PREFIX3."if(ugCheckForErrors('#{$galleryHtmlID}', 'cms'))";
			
			if($hasAddScripts)
				$scriptOutput .= "{";
			
			$scriptOutput .= self::LINE_PREFIX4."ugapi{$serial} = jQuery('#{$galleryHtmlID}').unitegallery(objUGParams);";
			
			//add custom scripts
			if($hasAddScripts){
				$scriptOutput .= self::BR;
				$scriptOutput .= self::LINE_PREFIX4."// custom scripts";
				$scriptOutput .= self::BR;
				$scriptOutput .= $addScripts;
				$scriptOutput .= self::LINE_PREFIX3."}";
			}
			
			$scriptOutput .= self::LINE_PREFIX2."});";
			
			if(GlobalsUG::$isScriptsInFooter == true)

				UniteProviderFunctionsUG::printCustomScript($scriptOutput);
			
			else{
				$output .= $scriptOutput;
				$output .= self::LINE_PREFIX1."</script>";
			}
			
			return($output);
		}
		
		
		
		/**
		 * get items for output
		 */
		protected function getItemsForOutput($categoryID){
			
			$items = new UniteGalleryItems();
			
			$arrLoadmore = $this->getLoadMoreParams();
			
			if($arrLoadmore["enable"] == true){
				
				$maxItems = $arrLoadmore["max_items"];
				$minItems = $arrLoadmore["min_items"];
				
				$arrItems = $items->getCatItemsLimit($categoryID, $maxItems, $minItems);
				
				//check and disable loadmore if needed
				$maxItems = (int)$maxItems;
				$totalItems = $items->getTotalCatItems($categoryID);
								
				if($totalItems <= $maxItems)
					$this->disableLoadMore();
				
			}else{
				$arrItems = $items->getCatItems($categoryID);
			}
			
			return($arrItems);
		}
		
		
		/**
		 * 
		 * put the gallery
		 */
		public function putGallery($galleryID, $arrOptions = array(), $initType = "id"){

			
			try{
				$objCategories = new UniteGalleryCategories();
				
				$this->initGallery($galleryID);
				
				$this->setOutputOptions();
				
				$this->putScripts();
				
				$enableCatTabs = $this->getParam('enable_category_tabs', self::FORCE_BOOLEAN);
				
				//custom items pass
				if(is_array($arrOptions) && array_key_exists("items", $arrOptions)){

					$arrItems = $arrOptions["items"];
					
					$enableCatTabs = false;
					
				}else{
					
					//set gallery category						
					$optCatID = UniteFunctionsUG::getVal($arrOptions, "categoryid");
					
					if(!empty($optCatID) && $objCategories->isCatExists($optCatID))
						$categoryID = $optCatID;
					else{
						if($enableCatTabs == true){
							$categoryID = $this->getParam("tabs_init_catid");
							
							if($categoryID == "first"){	//get first category from tabs
								$strCatIDs = $this->getParam("categorytabs_ids");
								$arrIDs = explode("," , $strCatIDs);
								if(!empty($arrIDs))
									$categoryID = $arrIDs[0];
							}
							
							if(empty($categoryID) || is_numeric($categoryID) == false)
							$categoryID = $this->getParam("category");
						}else
							$categoryID = $this->getParam("category");
					}
					
					if(empty($categoryID))
						UniteFunctionsUG::throwError(__("No items category selected", UNITEGALLERY_TEXTDOMAIN));
					
					//get items
					$arrItems = $this->getItemsForOutput($categoryID);
				}
				
				
				if(empty($arrItems))
					UniteFunctionsUG::throwError("No gallery items found", UNITEGALLERY_TEXTDOMAIN);
	
				//set wrapper style
				
				//size validation
				$this->getParam("gallery_width", self::FORCE_SIZE);
				if($this->isTilesType == false)
					$this->getParam("gallery_height", self::VALIDATE_NUMERIC);
				
				$fullWidth = $this->getParam("full_width", self::FORCE_BOOLEAN);
				
				if($fullWidth == true){
					$this->arrParams["gallery_width"] = "100%";
				}
				
				$wrapperStyle = $this->getPositionString();
				
				//set tabs html
				$htmlTabs = "";
				if($enableCatTabs == true){
					$htmlTabs = $this->getCategoryTabsHtml($this->galleryHtmlID, $objCategories);
					$this->arrParams["gallery_initial_catid"] = $categoryID;
				}
				
				//set loadmore html
				
				$arrLoadmore = $this->getLoadMoreParams();
				
				$enableLoadmore = $arrLoadmore["enable"];
				
				if($enableLoadmore == true)
					$htmlLoadMore = $this->getLoadMoreHtml($arrLoadmore);
				
				//get output related variables
				$addStyles = $this->getAdditionalStyles();
				
				$position = $this->getParam("position");
				
				$isRtlWrapper = ($position == "right");

				if($isRtlWrapper == true){
					$rtlWrapperStyle = $wrapperStyle;
					if(!empty($rtlWrapperStyle))
						$rtlWrapperStyle = " style='$rtlWrapperStyle'";
					
					$wrapperStyle = "";		//move the wrapper style to rtl wrapper
				}
				
				if(!empty($wrapperStyle))
					$wrapperStyle = " style='$wrapperStyle'";
				
				
				global $uniteGalleryVersion;
				$output = "
					\n
					<!-- START UNITE GALLERY {$uniteGalleryVersion} -->
					
				";
				
				if(!empty($addStyles))
					$output = $this->putInlineStyle($addStyles, $output);
				
				if($this->putJsToBody == true)
					$output .= $this->putJsIncludesToBody();
				
				if($enableCatTabs == true)
					$output .= $htmlTabs;

				//add rtl prefix to get the gallery to right if needed
				if($isRtlWrapper == true){
					$output .= self::LINE_PREFIX1."<div class='ug-rtl'{$rtlWrapperStyle}>";
				}
				
				$output .= self::LINE_PREFIX1."<div id='{$this->galleryHtmlID}' class='unite-gallery'{$wrapperStyle}>";
				$output .= self::LINE_PREFIX2.$this->putItems($arrItems);
				$output .= self::LINE_PREFIX1."</div>";
				
				if($isRtlWrapper == true)
					$output .= self::LINE_PREFIX1."</div>";
				
				//put loadmore html
				if($enableLoadmore == true){
					$output .= self::BR;
					$output .= $htmlLoadMore;
				}
				
				$output .= self::BR;
				
				$output = $this->putGalleryScripts($output);
				
				$output .= self::BR;
				
				$output .= self::LINE_PREFIX1."<!-- END UNITEGALLERY -->";
				
				$compressOutput = $this->getParam("compress_output", self::FORCE_BOOLEAN);
				
				if($compressOutput == true){
					$output = str_replace("\r", "", $output);
					$output = str_replace("\n", "", $output);
					$output = trim($output);
				}
				
				return $output;
				?>
				
			<?php 
			
		     }catch(Exception $e){
		     	$prefix = __("Unite Gallery Error",UNITEGALLERY_TEXTDOMAIN);
				$output = $this->getErrorMessage($e, $prefix);
				return($output);
		     }
		
		  }
}

?>