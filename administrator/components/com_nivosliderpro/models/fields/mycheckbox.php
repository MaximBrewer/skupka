<?php

defined('JPATH_BASE') or die;

/**
 * Supports a modal article picker.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @since		1.6
 */
class JFormFieldMycheckbox extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Mycheckbox';

	
	/**
	 * 
	 * get if checked or not
	 */
	public function isChecked(){
				
		if(!empty($this->value)){
			
			if($this->value == "true")
				return(true);
				
		}else{
			
			if($this->element['value'] == "true")
				return(true);
			}
		
		return(false);
	}
	
	
	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput(){
		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$disabled = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		
		$checked = $this->isChecked();
		
		$strChecked = ($checked == true) ? ' checked="checked"' : '';		
		
		$checkboxID = $this->id;
		$inputID = $this->id."-input";
		$spanID = $this->id."-span";
		$value = ($strChecked == true)?"true":"false";
		
		$html = "";
		$html .= '<span class="mycheckbox_span" id="'.$spanID.'">';
		$html .= '<input type="checkbox" class="mycheckbox_check" '.$strChecked.' id="'.$checkboxID.'">';
		$html .= '<input type="hidden" value="'.$value.'" class="mycheckbox_input" name="'.$this->name.'" '.$strChecked.' id="'.$inputID.'">';
		$html .= '</span>';
		
		return $html; 
	}
	
	
}
