<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Renders a multiple item select element
 * using SQL result and explicitly specified params
 *
 */

class JFormFieldMultiSelectParam extends JFormField
{
	/**
	 * Element name
	 *
	 * @access       protected
	 * @var          string
	 */
	public $type = 'multiselectparam';

	function getInput()
	{
		
		$html = array();
		$attr = '';
		
		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : ' class="inputbox"';
		
		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
			$attr .= ' disabled="disabled"';
		}
		
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$attr .= $this->element['multiple'] ? ' multiple="multiple"' : '';
		
		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
		
		$key = ($this->element['key_field'] ? $this->element['key_field'] : 'id');
		$val = ($this->element['value_field'] ? $this->element['value_field'] : 'name');
		
		$db		= JFactory::getDBO();
		
		$query = $this->element['sql'];
		
		$db->setQuery( $query );
		$result = $db->loadObjectList();
		foreach ($result as &$item){
			if(!empty($item)){
				foreach ($item as &$prop){
					$prop = JText::_($prop);
				}
			}
		}
		$html[] = JHtml::_('select.genericlist', $result, $this->name, trim($attr), $key, $val, $this->value, $this->id);
		
		return implode($html);
	}
}