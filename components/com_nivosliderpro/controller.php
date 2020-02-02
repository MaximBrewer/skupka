<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;


class NivoSliderProController extends JControllerNivoBaseRev
{
	
	/**
	 * show some image
	 */
	public function showimage(){
		
		UniteFunctionJoomla::showImageFromRequest();
		exit();
	}
	
	/**
	 * 
	 * get css of some slider
	 */
	public function getcss(){
		$sliderID = UniteFunctions::getGetVar("slider_id");
		$sliderJSID = UniteFunctions::getGetVar("slider_js_id");
		
		$output = new NivoSliderProOutput();
		$output->outputCss($sliderID, $sliderJSID);
		exit();
	}
	
	
	/**
	 * 
	 * default display function
	 */
	public function display($cachable = false, $urlparams = false){
		echo "nothing here";
		exit();
	}
	
}