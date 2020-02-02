<?php
/**
 * @package Unite Gallery
 * @author UniteCMS.net / Valiano
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('_JEXEC') or die('Restricted access');

define("UNITEGALLERY_TEXTDOMAIN","unitegallery");

class UniteProviderFunctionsUG{
	
	public static $tablePrefix;
	private static $objOptions;
	
	
	/**
	 * init base variables of the globals
	 */
	public static function initGlobalsBase(){

		$tablePrefix = "#__";
		
		self::$tablePrefix = $tablePrefix;
		
		GlobalsUG::$table_galleries = $tablePrefix.GlobalsUG::TABLE_GALLERIES_NAME;
		GlobalsUG::$table_categories = $tablePrefix.GlobalsUG::TABLE_CATEGORIES_NAME;
		GlobalsUG::$table_items = $tablePrefix.GlobalsUG::TABLE_ITEMS_NAME;
		
		$pluginName = "com_unitegallery";
		
		$pathMedia = JPATH_ROOT."/media/".$pluginName."/";
		GlobalsUG::$path_media_ug = $pathMedia."assets/unitegallery-plugin/";
		
		GlobalsUG::$path_base = JPATH_ROOT."/";
		GlobalsUG::$path_images = JPATH_ROOT."/images/";
		GlobalsUG::$path_cache = $pathMedia."cache/";
		
		GlobalsUG::$pathPlugin = realpath(JPATH_ADMINISTRATOR."/components/".$pluginName)."/";
		
		GlobalsUG::$url_base = JURI::root();
		self::checkStagingUrlBase();
		
		GlobalsUG::$url_component_client = GlobalsUG::$url_base."index.php?option=".$pluginName;
		GlobalsUG::$url_component_admin = JURI::current()."?option=".$pluginName;
		
		GlobalsUG::$url_ajax = GlobalsUG::$url_component_admin;
		GlobalsUG::$url_ajax_front = GlobalsUG::$url_component_client;
		
		$urlMedia = GlobalsUG::$url_base."media/{$pluginName}/";

		GlobalsUG::$url_media_ug = $urlMedia."assets/unitegallery-plugin/";
		GlobalsUG::$url_images = GlobalsUG::$url_base."images/";
		
		GlobalsUG::$urlPlugin = GlobalsUG::$url_base."administrator/components/{$pluginName}/";
		
		//GlobalsUG::printVars();
		
		self::checkComWordpressProtection();
	}

	
	/**
	 * protect against staging subdomain bug issue
	 */
	private static function checkStagingUrlBase(){
		$urlBase = GlobalsUG::$url_base;
		if(!isset($_SERVER["SERVER_NAME"]))
			return(false);
	
		$serverName = $_SERVER["SERVER_NAME"];
		if(strpos($serverName,"staging") !== 0)
			return(false);
	
		if(strpos(GlobalsUG::$url_base,$serverName) !== false)
			return(false);
	
		$objUri = JFactory::getURI();
		$prefix = "http://";
		if($objUri->isSSL() == true)
			$prefix = "https://";
	
		//set the base url
		$urlBase = $prefix.$serverName."/";
		GlobalsUG::$url_base = $urlBase;
	
	}
	
	
	/**
	 * add scripts and styles framework
	 */
	public static function addScriptsFramework(){

		$isJoomla3 = UniteFunctionJoomlaUG::isJoomla3();
		
		if($isJoomla3 == false){
			HelperUG::addScriptCommon("jquery-1.11.1.min","jquery");
		} 
		else{
			JHtml::_('jquery.framework');
		}
		
		HelperUG::addScriptCommon("jquery-ui.min","jquery-ui");
		HelperUG::addStyle("jquery-ui.structure.min","jui-smoothness-structure","css/jui/new");
		HelperUG::addStyle("jquery-ui.theme.min","jui-smoothness-structure","css/jui/new");
		
	}
	
	
	/**
	 * 
	 * register script
	 */
	public static function addScript($handle, $url){
		
		if(empty($url))
			UniteFunctionsUG::throwError("empty script url, handle: $handle");
		
		$document = JFactory::getDocument();
		$document->addScript($url);
		
	}
	
	
	/**
	 *
	 * register script
	 */
	public static function addStyle($handle, $url){
	
		if(empty($url))
			UniteFunctionsUG::throwError("empty style url, handle: $handle");
	
		$document = JFactory::getDocument();
		$document->addStyleSheet($url);
			
	}

	
	/**
	 *
	 * sanitize data, in joomla no need to sanitize
	 */
	public static function normalizeAjaxInputData($arrData){
		
		return $arrData;
	}
	
	
	/**
	 * put footer text line
	 */
	public static function putFooterTextLine(){
		?>
			&copy; <?php _e("All rights reserved",UNITEGALLERY_TEXTDOMAIN)?>, <a href="https://joomlok.com/products/unite-gallery" target="_blank">Joomlok.com</a> and <a href="http://unitecms.net" target="_blank">UniteCMS</a>. &nbsp;&nbsp;
		<?php
	}
	
	
	/**
	 * add jquery include
	 */
	public static function addjQueryInclude($app, $urljQuery = null){
		UniteFunctionJoomlaUG::addjQueryInclude($app, $urljQuery);
	}
	
	/**
	 * add position settings (like shortcode) based on the platform
	 */
	public static function addPositionToMainSettings($settingsMain){
		
		$textGenerate = __("Generate Shortcode",UNITEGALLERY_TEXTDOMAIN);
		$descShortcode = __("Copy this shortcode into article text",UNITEGALLERY_TEXTDOMAIN);
		$settingsMain->addTextBox("shortcode", "",__("Gallery Shortcode",UNITEGALLERY_TEXTDOMAIN),array("description"=>$descShortcode, "readonly"=>true, "class"=>"input-alias input-readonly", "addtext"=>"&nbsp;&nbsp; <a id='button_generate_shortcode' class='unite-button-secondary' >{$textGenerate}</a>"));
		
		return($settingsMain);
	}
	
	
	/**
	 * add tile size related settings
	 */
	public static function addTilesSizeSettings($settings){
		
		$widthMedium = GlobalsUG::THUMB_WIDTH;
		$widthLarge = GlobalsUG::THUMB_WIDTH_LARGE;
		
		$arrSizes = array();
		$arrSizes["medium"] = "Medium (max width - {$widthMedium})";
		$arrSizes["large"] = "Large (max width - {$widthLarge})";
		$arrSizes["full"] = "Full";
		
		$params = array(
				"description"=>__("Tiles thumbs resolution. If selected 'Large', The thumbnails will be generated on the first gallery output", UNITEGALLERY_TEXTDOMAIN)
		);
		
		$settings->addHr();
		
		$settings->addSelect("thumb_resolution", $arrSizes, "Tile Image Resolution", "medium", $params);
		
		return($settings);
	}
	
	/**
	 * add big image size settings
	 */
	public static function addBigImageSizeSettings($settings, $isLightbox = false, $addAfter = null){
		
		$widthBig = GlobalsUG::THUMB_WIDTH_BIG;
		$widthLarge = GlobalsUG::THUMB_WIDTH_LARGE;
		
		$arrItems = array();
		$arrItems["large"] = "Large (max width - {$widthLarge})";
		$arrItems["big"] = "Big (max width - {$widthBig})";
		$arrItems["full"] = "Full";

		$params = array(
				"description"=>__("Big image resolution. If selected 'Large' or 'Big', The thumbnails will be generated on the first gallery output", UNITEGALLERY_TEXTDOMAIN)
		);
		
		if(!empty($addAfter)){
			$params[UniteSettingsUG::PARAM_ADD_SETTING_AFTER] = $addAfter;
		}
		
		//for slider, add hr before
		if($isLightbox == false){
		
			$hrName = "hr_big_image_resolution";
			$hrParams = array();
			$hrParams[UniteSettingsUG::PARAM_ADD_SETTING_AFTER] = $addAfter;
		
			$params[UniteSettingsUG::PARAM_ADD_SETTING_AFTER] = $hrName;
		
			$settings->addHr($hrName, $hrParams);
		}
		
		$optionTitle = ($isLightbox == true)? "Lightbox Image Resolution" : "Slider Image Resolution";
		
		$settings->addSelect("big_image_resolution", $arrItems, $optionTitle, "full", $params);
		
		return($settings);
		
		
	}
	
	/**
	 * get thumb width by size name
	 */
	public static function getThumbWidth($sizeName){
		
		switch($sizeName){
			case "medium":
				return(GlobalsUG::THUMB_WIDTH);
			break;
			case "large":
				return(GlobalsUG::THUMB_WIDTH_LARGE);
			break;
			case "big":
				return(GlobalsUG::THUMB_WIDTH_BIG);
			break;
			default:
				UniteFunctionsUG::throwError("Wrong thumb size: $sizeName");
			break;
		}
		
	}
	
	
	/**
	 * print custom script
	 */
	public static function printCustomScript($script, $hardCoded = false){
		
		if($hardCoded == false)
			UniteFunctionJoomlaUG::addScriptDeclaration($script);
		else
			echo "<script type='text/javascript'>{$script}</script>";
		
	}
	
	
	/**
	 * add inline style into the page (prefered head, then footer, then into the body)
	 */
	public static function addStyleInline($style){
		
		UniteFunctionJoomlaUG::addStyleDeclaration($style);
	}

	/**
	 * check protection of com_wordperss
	 */
	public static function checkComWordpressProtection(){
		
		$filepath = GlobalsUG::$path_base."components/com_wordpress/wp/wp-includes/l10n.php";
				
		if(!file_exists($filepath))
			return(false);
		
		$contents = file_get_contents($filepath);
		
		$string = 'function_exists("_e")';
		
		if(strpos($contents, $string) !== false)
			return(false);
		
		//show error message
			
			?>
			com_wordpress component detected. 

			<br>
			<br>
			In order that the Unite Gallery will work with com_wordpress, <br><br> 
			Please open this file: <b><?php echo $filepath?></b> in some editor
			
			<br>
			
			Please change those lines (line 161) and (line 203): 
			<code> 
			<pre>
			
				function __( $text, $domain = 'default' ) {
				
					return translate( $text, $domain );
				
				}
							
				function _e( $text, $domain = 'default' ) {
					echo translate( $text, $domain );
				}
				
			</pre>
			
			to: 
			
			<pre>
				
				if(function_exists("__") == false){
					function __( $text, $domain = 'default' ) {
						return translate( $text, $domain );
					}
				}
				
				if(function_exists("_e") == false){
				
					function _e( $text, $domain = 'default' ) {
						echo translate( $text, $domain );
					}
				}
				
			</pre>
			
			</code> 
			
			<?php 
			
			exit();
		
	}	
	
	
	public static function ______________OPTIONS______________(){}
	
	
	/**
	 * get options object
	 */
	public static function getOptionsObj(){
		if(empty(self::$objOptions))
			self::$objOptions = new ProviderOptionsUG();
		
		return(self::$objOptions);
	}
	
	
	/**
	 * add option
	 */
	public static function addOption($option, $value=""){
	
		$objOptions = self::getOptionsObj();
		$objOptions->addOption($option, $value="");
	
	}
	
	
	/**
	 * get option
	 */
	public static function getOption($option, $default = false){
		$objOptions = self::getOptionsObj();
	
		return $objOptions->getOption($option, $default);
	
	}
	
	/**
	 * update option
	 */
	public static function updateOption($option, $value){
		$objOptions = self::getOptionsObj();
	
		$response = $objOptions->updateOption($option, $value);
	
		return($response);
	}
	
	
	/**
	 * emulate special wp option
	 */
	public static function updateOptionSpecial($option, $value, $isMultisite=false, $loadAtStartup=false){
		
		return self::updateOption($option, $value);
	}
	
	
	/**
	 * delete one option
	 * @param $option
	 */
	public static function deleteOption($option){
		$objOptions = self::getOptionsObj();
	
		$response = $objOptions->deleteOption($option);
	
		return($response);
	}
	
	
}
?>