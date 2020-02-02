<?php

defined('JPATH_BASE') or die;

/**
 * Supports a modal article picker.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @since		1.6
 */
class JFormFieldSlider extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Slider';

	/**
	 * 
	 * include all the files needed
	 */
	protected function requireFramework(){
		$currentDir = dirname(__FILE__);
		$pathBase = $currentDir."/../../";
		$pathBase = realpath($pathBase)."/";
		
		$pathHelpers = $pathBase."helpers/";
				
		require_once($pathHelpers."helper_nivosliderpro.class.php");
		require_once($pathHelpers."globals_nivosliderpro.class.php");
		require_once($pathBase."/unitejoomla/includes.php");
		GlobalsNivoPro::init();
	}
	
	
	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		$this->requireFramework();
				
		$arrSliders = HelperNivoPro::getArrSliders();
		
		$html = "<select id='{$this->id}_id' name='{$this->name}'>";
		foreach($arrSliders as $slider){
			$title = $slider["title"];
			$id = $slider["id"];
			$selected = "";
			$selectedID = $this->value;
			if(empty($selectedID))
				$selectedID = JRequest::getVar("sliderid");
				
			if($id == $selectedID)
				$selected = 'selected="selected"';
			
			$html .= "<option value='$id' $selected>$title</option>";
		}		
		$html .= "</select>";
		
		return $html;
	}
	
	
}
