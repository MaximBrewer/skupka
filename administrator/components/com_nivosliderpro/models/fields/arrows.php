<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Supports a modal article picker.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @since		1.6
 */
class JFormFieldArrows extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'arrows';
	
	
	/**
	 * 
	 * get label function
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
	protected function getInput()
	{
		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal');

		// Build the script.
		$script = array();
		$script[] = '	function onArrowsSelect(data){';
		$script[] = '		UniteNivoPro.onArrowsSelect(data);';
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
		

		// Setup variables for display.
		$html	= array();
		
		$link = 'index.php?option=com_nivosliderpro&view=slider&layout=arrows&tmpl=component';
		
		$html[] = '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'.$this->value.'" />';
		$buttonID = $this->id."-btn";
		
		$arrowsText = "Change Arrows";

		$class = "modal panel_button";
		
		$desc = UniteFunctions::getVal($this->element, "description");
		$htmlAddon = "";
		if(!empty($desc)){
			$htmlAddon = ' title="'.$desc.'"';
			//$class .= " hasTip";	//making problems with rel
		}
		
		
		// The user select button.
		$html[] = '	<a id="'.$buttonID.'" class="'.$class.'" '.$htmlAddon.' href="'.$link.'&amp;'.JSession::getFormToken().'=1" rel="{handler: \'iframe\', size: {x: 900, y: 450}}">'.$arrowsText.'</a>';
		
		$html = implode("\n", $html);
		
		return $html;
	}
}
