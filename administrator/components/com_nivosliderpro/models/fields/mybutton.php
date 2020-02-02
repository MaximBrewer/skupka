<?php

defined('JPATH_BASE') or die;

/**
 * Supports a modal article picker.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @since		1.6
 */
class JFormFieldMybutton extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Mybutton';

	
	/**
	 * 
	 * get label
	 */
	protected function getLabel(){
		
		return("");
	}
	
	
	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput(){
		// Initialize some field attributes.
		if(empty($class))
			$class = "button1";
		
		$id = $this->element['name'];
		$label = $this->element['label'];
		
		$desc = UniteFunctions::getVal($this->element, "description");
		$htmlAddon = "";
		if(!empty($desc))
			$htmlAddon = "class='hasTip' title='$desc'";
		
		$html = "<input type='button' id='$id' value='$label' $htmlAddon>";
		
		return($html);
	}
	
	
}
