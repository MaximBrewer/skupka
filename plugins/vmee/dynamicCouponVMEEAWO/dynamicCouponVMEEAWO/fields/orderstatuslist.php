<?php
/**
 * @copyright (C) 2014 Interamind LTD, http://www.interamind.com
**/

defined('_JEXEC') or die( 'Restricted access' );

class JFormFieldOrderStatusList extends JFormField {
	
	public $type = 'OrderStatusList';

	protected function getInput() {
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
			$attr .= ' disabled="disabled"';
		}

		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$attr .= $this->element['multiple'] ? ' multiple="multiple"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
		
		
		
		$db		= JFactory::getDBO();
//		$query = "SELECT order_status_code, order_status_name FROM #__vm_order_status";
		
		
		$query = 'SELECT `order_status_code` AS value, `order_status_name` AS text'
                . ' FROM `#__virtuemart_orderstates` '
                . ' WHERE `virtuemart_vendor_id` = 1'
                . ' ORDER BY `ordering` ASC '
        ;
		
		$db->setQuery( $query );
		$statuses = $db->loadObjectList();

//		array_unshift( $statuses, JHtml::_('select.options',  '', '- '. JText::_( 'ALL STATUSES' ) .' -', 'order_status_code', 'order_status_name' ) );

		$html[] = JHtml::_('select.genericlist', $statuses, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		
		return implode($html);
		
	}
	
	

}
?>
