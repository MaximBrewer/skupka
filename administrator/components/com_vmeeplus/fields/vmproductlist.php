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

class JFormFieldVmPRoductList extends JFormField
{
	/**
	 * Element name
	 *
	 * @access       protected
	 * @var          string
	 */
	public $type = 'vmproductlist';

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
		
		$key = 'virtuemart_product_id';
		$val = 'name';
		
		$result = $this->getProducts();
		$html[] = JHtml::_('select.genericlist', $result, $this->name, trim($attr), $key, $val, $this->value, $this->id);
		//emp_logger::log('helper::get products html: ', emp_logger::LEVEL_DEBUG, $html);
		
		return implode($html);
	}
	
	protected function getProducts($lang = null){
		if(is_null($lang)){
			require_once JPATH_ADMINISTRATOR.'/components/com_vmeeplus/classes/helper.php';
			emp_helper::loadVirtueMartFiles();
			$lang = VMLANG;
		}

		$query = "SELECT p.virtuemart_product_id , CONCAT(l.product_name,' (',p.product_sku, ')') as name FROM #__virtuemart_products_".$lang." as l JOIN #__virtuemart_products as p using (virtuemart_product_id)";
		$query .= ' ORDER BY l.virtuemart_product_id';
		//emp_logger::log('helper::get products query: ', emp_logger::LEVEL_DEBUG, $query);
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$result = $db->loadObjectList();
		if(empty($result) && $lang != 'en_gb'){
			return $this->getProducts('en_gb');
		}
		return $result;
	}
}