<?php

defined('_JEXEC') or die;

class HelperNivoPro{
	
	/**
	 * 
	 * output the slider by slider id
	 */
	public static function outputSlider($sliderID,$isJSInBody=false,$noConflictMode = false){
		$output = new NivoSliderProOutput();
		
		if($isJSInBody == true)
			$output->setJsInBody();
		
		if($noConflictMode == true)		
			$output->enableNoConflict();
			
		$output->outputSlider($sliderID);
	}	
	
	
	/**
	 * 
	 * get sliders array small (id,title,alias)
	 */
	public static function getArrSliders(){	
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id,title,alias');
		$query->from(GlobalsNivoPro::TABLE_SLIDERS);
		
		$db->setQuery($query);
		$rows = $db->loadAssocList();
		return($rows);
	}
	
	/**
	 * get slider
	 */
	public static function getSlider($sliderID){
		$sliderID = (int)$sliderID;
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from(GlobalsNivoPro::TABLE_SLIDERS);
		$query->where("id='$sliderID'");
		
		$db->setQuery($query);
		$rows = $db->loadAssocList();
		if(empty($rows))
			UniteFunctions::throwError("slider not found: $sliderID");
			
		$row = $rows[0]; 
		$params = UniteFunctionJoomla::decodeRegistryToArray($row, "params");
		$visual = UniteFunctionJoomla::decodeRegistryToArray($row, "visual");
		$params = array_merge($params,$visual);
		
		$paramsReg = new JRegistry();
		$paramsReg->loadArray($params);
		
		
		$row["params"] = $paramsReg;
		$row["visual"] = $visual;
		 
		return($row);
	}
	
	
	/**
	 * 
	 * get array of arrows list
	 */
	public static function getArrowsList(){
		$pathArrows = GlobalsNivoPro::$pathAssetsArrows;
		
		$arrDirs = UniteFunctions::getFolderDirs($pathArrows);
		
		$arrows = array();
		foreach($arrDirs as $dir){
			$set = self::getArrowSetFromDir($dir);
			$arrows[$dir] = $set;
		}
		
		return($arrows);
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 */
	public static function getBulletsList(){
		$pathBullets = GlobalsNivoPro::$pathAssetsBullets;
		$arrDirs = UniteFunctions::getFolderDirs($pathBullets);
		$bullets = array();
		foreach($arrDirs as $dir){
			$set = self::getBulletSetFromDir($dir);
			$bullets[$dir] = $set;
		}
		
		return($bullets);
	}
	
	
	/**
	 * 
	 * get bullets set
	 * @param $name
	 */
	public static function getBulletsSet($name){
		$pathSet = GlobalsNivoPro::$pathAssetsBullets.$name."/";
		
		if(is_dir($pathSet))
			$set = self::getBulletSetFromDir($name);			
		else{
			$arrSets = self::getBulletsList();
			$set = array_pop($arrSets);
		}
		return($set);
	}
	
	/**
	 * 
	 * pub bullets set html
	 * @param mixed $set - can be array or set name
	 */
	public static function getBulletsHtml($set,$num=5){
		
		if($num < 3)
			$num = 3;
		
		if(gettype($set) == "string")
			$set = HelperNivoPro::getBulletsSet($set);
		
		$options = $set["options"];

		$imgLeft = UniteFunctions::getVal($set, "url_bgleft");
		$imgRight = UniteFunctions::getVal($set, "url_bgright");
		$imgCenter = UniteFunctions::getVal($set, "url_bgrepeat");
		
		$idBackground = false;
		if(!empty($imgCenter)){
			$idBackground = true;
			
			//validate background fields
			UniteFunctions::validateArrayFieldExists($options,"bg_height,bg_left_width,bg_right_width,padding_top",
															  "getBulletsHtml, background field not found in options");
			UniteFunctions::validateNotEmpty($imgRight,"right image");
			UniteFunctions::validateNotEmpty($imgLeft,"left image");
		}
		
		$space_middle = UniteFunctions::getVal($options, "space_middle", 3);
		
		$html = "";
		
		//Width Background
		if($idBackground == true):
			$bgHeight = $options["bg_height"];
			$bgWidthLeft = $options["bg_left_width"];
			$bgWidthRight = $options["bg_right_width"];
			$paddingTop = $options["padding_top"];
			$styleLeft = "float:left;height:{$bgHeight}px;width:{$bgWidthLeft}px;background-image:url(\"{$imgLeft}\");background-repeat:no-repeat;";
			$styleRight = "float:left;height:{$bgHeight}px;width:{$bgWidthLeft}px;background-image:url(\"{$imgRight}\");background-repeat:no-repeat;";
			$styleCenter = "float:left;height:{$bgHeight}px;background-image:url(\"{$imgCenter}\");background-repeat:releat-x;";
						
			$html .= "<div class='bullets_left' style='$styleLeft' ></div>";
			$html .= "<div class='bullets_middle' style='$styleCenter'>";
			$html .= "<div class='bullets_inner' style='padding-top:".$paddingTop."px;'>";
			
			$html .= 	'<ul>';
				for($i=0;$i<$num;$i++){				
					$urlBullet = $set["url_normal"];
					if($i == 1)
						$urlBullet = $set["url_active"];
	
					$styleLI = "";
					if($i>0)
						$styleLI = "margin-left:".$space_middle."px";
					
					$html .= "<li style='$styleLI'><img src='$urlBullet'/></li>";
				} 
			$html .= '</ul>';
			
			$html .= '</div>';
			$html .= '</div>';
			$html .= "<div class='bullets_right' style='$styleRight'></div>";
						
		else:		//no background:
		
			$html .= 	'<ul>';
				for($i=0;$i<$num;$i++){				
					$urlBullet = $set["url_normal"];
					if($i == 1)
						$urlBullet = $set["url_active"];
	
					$styleLI = "";
					if($i>0)
						$styleLI = "margin-left:".$space_middle."px";
					
					$html .= "<li style='$styleLI'><img src='$urlBullet'/></li>";
				} 
			$html .= '</ul>';
			
		endif;
		
		$html .= '<div class="clear"></div>';
		
		return($html);
	}

	
	
	/**
	 * 
	 * get arrows set by name
	 */
	public static function getArrowSet($name){
		$pathSet = GlobalsNivoPro::$pathAssetsArrows.$name."/";
		
		if(is_dir($pathSet)){
			$set = self::getArrowSetFromDir($name);
		}			
		else{
			$arrSets = self::getArrowsList();
			$set = array_pop($arrSets);
		}
		return($set);
	}
	
	
	/**
	 * 
	 * get arrows set by dir
	 */
	private static function getArrowSetFromDir($dir){
		$pathArrows = GlobalsNivoPro::$pathAssetsArrows;
		
		$pathSet = $pathArrows.$dir."/";
		
		if(is_dir($pathSet) == false)
			UniteFunctions::throwError("The arrow directory: $dir not found!");
				
		$urlSet = GlobalsNivoPro::$urlAssetsArrows.$dir."/";
		$leftName = "left.png";
		$rightName = "right.png";
		$leftHoverName = "left_hover.png";
		$rightHoverName = "right_hover.png";
		$options = "options.ini";
		
		$pathLeft = $pathSet.$leftName;
		$pathRight = $pathSet.$rightName;
		
		if(!file_exists($pathLeft))
			UniteFunctions::throwError("Left arrow of set $dir not found: $pathLeft");
			
		if(!file_exists($pathRight))
			UniteFunctions::throwError("Right arrow of set $dir not found: $pathRight");
		
		//validate required paths:
		if(file_exists($pathSet.$options) == false)
			UniteFunctions::throwError("$options not found in arrows set: $dir");
		
		$set = array();
		$set["name"] = $dir;
		$set["url_left"] = $urlSet.$leftName;
		$set["url_right"] = $urlSet.$rightName;
		
		$set["url_left_hover"] = "";
		$set["url_right_hover"] = "";
		$set["has_hover"] = false;
		
		if(file_exists($pathSet.$leftHoverName)){
			$set["url_left_hover"] = $urlSet.$leftHoverName;
			$set["has_hover"] = true;
		}

		if(file_exists($pathSet.$rightHoverName))
			$set["url_right_hover"] = $urlSet.$rightHoverName;
			
		//get options
   		$content = file_get_contents($pathSet.$options);
   		$arrOptions = UniteFunctions::parseSettingsFile($content);
   		$set["options"] = $arrOptions;
		
		return($set);
	}
	
	/**
	 * 
	 * get bullets set from some dir
	 */
	private static function getBulletSetFromDir($dir){
		
		$pathSet = GlobalsNivoPro::$pathAssetsBullets.$dir."/";
		if(is_dir($pathSet) == false)
			UniteFunctions::throwError("The bullet directory: $dir not found!");
		
		$urlSet = GlobalsNivoPro::$urlAssetsBullets.$dir."/";
		
		//set paths
		$bulletNormal = "bullet_normal.png";
		$bulletActive = "bullet_active.png";
		$bgLeft = "bg_left.png";
		$bgRepeat = "bg_repeat.png";
		$bgRight = "bg_right.png";		
		$preview = "preview.png";
		$options = "options.ini";
		
		//validate required paths:
		if(file_exists($pathSet.$bulletNormal) == false)
			UniteFunctions::throwError("$bulletNormal not found in bullets set: $dir");
		
		if(file_exists($pathSet.$bulletActive) == false)
			UniteFunctions::throwError("$bulletActive not found in bullets set: $dir");

		//validate required paths:
		if(file_exists($pathSet.$preview) == false)
			UniteFunctions::throwError("$preview not found in bullets set: $dir");
			
		//set data array
		$set = array();
		$set["name"] = $dir;
		$set["url_normal"] = $urlSet.$bulletNormal;
		$set["url_active"] = $urlSet.$bulletActive;
		$set["url_preview"] = $urlSet.$preview;
		
		//set bg
	   $set["url_bgleft"] = "";
	   $set["url_bgright"] = "";
	   $set["url_bgrepeat"] = "";		   
	   $set["is_bg"] = false;
	
		if(file_exists($pathSet.$bgLeft) && 
		   file_exists($pathSet.$bgRight) &&
		   file_exists($pathSet.$bgRepeat)){
		   	
		   		$set["is_bg"] = true;
			   $set["url_bgleft"] = $urlSet.$bgLeft;
			   $set["url_bgright"] = $urlSet.$bgRight;
			   $set["url_bgrepeat"] = $urlSet.$bgRepeat;		   
	   }
	   
	   //set options
	   $set["options"] = array();	   
	   if(file_exists($pathSet.$options)){
	   		$content = file_get_contents($pathSet.$options);
	   		$arrOptions = UniteFunctions::parseSettingsFile($content);
	   		$set["options"] = $arrOptions;
	   }
	   
	   return($set);
	}
	
	
	/**
	 * 
	 * add script relative to the "assets" folder
	 */
	public static function addScript($scriptName){
		$document = JFactory::getDocument();
		$document->addScript(GlobalsNivoPro::$urlAssets.$scriptName);
	}
	
	/**
	 * 
	 * add script relative to the "assets" folder
	 */
	public static function addStylesheet($cssName){
		$document = JFactory::getDocument();
		$document->addStyleSheet(GlobalsNivoPro::$urlAssets.$cssName);
	}
	
	/**
	 * 
	 * get arr sliders by id
	 */
	public static function getArrSlidersAssoc(){
		$arrSliders = self::getArrSliders();
		
		$arrOutput = array();
		foreach($arrSliders as $slider)
			$arrOutput[$slider["id"]] = $slider;
		
		return($arrOutput);
	}
	
	/**
	 * 
	 * get first slider id
	 */
	public static function getFirstSliderID(){
		
		$arrSliders = self::getArrSliders();
		if(empty($arrSliders))
			return("");
		
		$firstSliderID = $arrSliders[0]["id"];
		
		return($firstSliderID);
	}
	
	/**
	 * 
	 * validate that some slider exists. else throw error
	 */
	private static function validateSliderExists($sliderID){
		$arrSliders = self::getArrSlidersAssoc();
		if(array_key_exists($sliderID, $arrSliders) == false)
			throw new Exception("Slider with id: $sliderID not exists.");
	}
	
	/**
	 * 
	 * get slides row
	 */
	private static function getSlidesRows($sliderID){
		self::validateSliderExists($sliderID);
		
		$db = JFactory::getDBO();		
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from(GlobalsNivoPro::TABLE_SLIDES);
		$query->where("sliderid='$sliderID' and published=1");
		$query->order("ordering asc");
		$db->setQuery($query);		
		$rows = $db->loadAssocList();
		return($rows);
	}
	
	
	/**
	 * 
	 * get slides array of some slider
	 * @param $sliderID
	 */
	public static function getArrSlides($sliderID){
		
		$rows = self::getSlidesRows($sliderID);
		
		$urlRoot = JURI::root();
		
		//process params:
		foreach($rows as $key=>$row){
			$jsonParams = $row["params"];
			$params = new JRegistry();
			$params->loadString($jsonParams, "json");
			
			//add image full path
			$image = $params->get("image");
			$params->set("url_image",$urlRoot.$image);
			$rows[$key]["params"] = $params;
		}
		
		return($rows);
	}
	
	/**
	 * 
	 * get number of slides of some slider
	 */
	public static function getNumSlides($sliderID){
		$rows = self::getSlidesRows($sliderID);
		$numSlides = count($rows);
		return($numSlides);
	}
	
	
	/**
	 * get slider view url
	 */
	public static function getViewUrl_Slider($sliderID,$layout = null){
		if(empty($layout))
			$layout = GlobalsNivoPro::LAYOUT_SLIDER_GENERAL;
			
		$args = "id=".(int)$sliderID;		
		$url = UniteFunctionJoomla::getViewUrl(GlobalsNivoPro::VIEW_SLIDER, $layout,$args);
		return($url);
	}

	
	/**
	 * 
	 * get "items" view url
	 */
	public static function getViewUrl_Items($sliderID){
		$args = "id=".(int)$sliderID;
		$url = UniteFunctionJoomla::getViewUrl(GlobalsNivoPro::VIEW_ITEMS, null,$args);
		return($url);
	}
	
	/**
	 * 
	 * include some view, give path from "views" folder
	 */
	public static function includeView($pathView){
		
		$filepathView = GlobalsNivoPro::$pathViews.$pathView;
		
		if(file_exists($filepathView) == false)
			UniteFunctions::throwError("View not found: $pathView");
		
		require $filepathView;
	}
	
	
	
	/**
	 * 
	 * add submenu
	 */
	public static function addSubmenu($currentView,$currentLayout){
		
		switch($currentView){
			default:
			case GlobalsNivoPro::VIEW_SLIDER:
				$sliderID = JRequest::getCmd("id");
				$isNew = empty($sliderID);
				
				//disable the menu on new slider
				if($isNew == true)
					return(false);
					
				$viewSliderSettings = UniteFunctionJoomla::getViewUrl(GlobalsNivoPro::VIEW_SLIDER,GlobalsNivoPro::LAYOUT_SLIDER_GENERAL,"id=".$sliderID);
				$viewSliderVisual = UniteFunctionJoomla::getViewUrl(GlobalsNivoPro::VIEW_SLIDER,GlobalsNivoPro::LAYOUT_SLIDER_VISUAL, "id=".$sliderID);
				$viewSlides = UniteFunctionJoomla::getViewUrl(GlobalsNivoPro::VIEW_ITEMS,"","id=".$sliderID);
				
				//slider settings:
				$selectedGeneral = ($currentView == GlobalsNivoPro::VIEW_SLIDER && $currentLayout == GlobalsNivoPro::LAYOUT_SLIDER_GENERAL);
				$selectedVisual = ($currentView == GlobalsNivoPro::VIEW_SLIDER && $currentLayout == GlobalsNivoPro::LAYOUT_SLIDER_VISUAL);
				$selectedSlides = ($currentView == GlobalsNivoPro::VIEW_ITEMS);
				
				JSubMenuHelper::addEntry(JText::_('COM_NIVOSLIDERPRO_GENERAL_SETTINGS'),$viewSliderSettings,$selectedGeneral);
				JSubMenuHelper::addEntry(JText::_('COM_NIVOSLIDERPRO_VISUAL_SETTINGS'),$viewSliderVisual,$selectedVisual);
				JSubMenuHelper::addEntry(JText::_('COM_NIVOSLIDERPRO_SLIDES'),$viewSlides,$selectedSlides);
				
			break;
			case GlobalsNivoPro::VIEW_SLIDERS:	//don't show menu at all.
			break;
		}
		
	}
	
	
}
?>