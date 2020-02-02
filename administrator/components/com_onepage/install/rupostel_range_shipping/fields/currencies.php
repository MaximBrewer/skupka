<?php
defined ('_JEXEC') or die();
jimport('joomla.form.formfield');
class JFormFieldCurrencies extends JFormField {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'currencies';

	function getInput () {
		
		$name = $this->name; 
		$value = $this->value; 
		
		$control_name = 'params'; 
		$cid = JRequest::getVar('cid'); 
		if (empty($cid)) return 'Method not found, click save first !'; 
		
		$obj = $this->getParams($cid); 
		
		$html = '<select name="'.$name.'">'; 
		$html .= '<option value="0">Do not filter per currency</option>'; 
		$db = JFactory::getDBO(); 
		$q = 'select * from #__virtuemart_currencies where published = 1'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		foreach ($res as $row)
		{
			$obj->currency_filter = (int)$obj->currency_filter; 
			$row['virtuemart_currency_id'] = (int)$row['virtuemart_currency_id']; 
			if ($obj->currency_filter === $row['virtuemart_currency_id']) $selected = ' selected="selected" '; 
			else $selected = ''; 
			$html .= '<option '.$selected.' value="'.$row['virtuemart_currency_id'].'">'.JText::_($row['currency_name']).'</option>'; 
		}
		$html .= '</select>'; 
		
		return $html; 
	}
	
	private function getParams($cid)
	{
		$db = JFactory::getDBO(); 
		$q = 'select `shipment_params` from `#__virtuemart_shipmentmethods` where `shipment_element` = \'rupostel_range_shipping\' '; 
		if (!empty($cid))
		 {
		   $cid = (int)$cid[0]; 
		   
		   $q .= ' and virtuemart_shipmentmethod_id = '.$cid; 
		 }
		$db->setQuery($q); 
		
		$res = $db->loadResult(); 
		
		
		$e = $db->getErrorMessage(); if (!empty($e)) die($e); 
		$obj = new stdClass(); 
		
		$err = true; 
		if (empty($res)) $err = true; 
		else
		{
		$a = explode('|', $res); 
		
		foreach ($a as $p)
		 {
		    $a2 = explode('=', $p); 
			if (!empty($a2) && (count($a2)==2))
			 {
			   $keyX = $a2[0]; 
			   $obj->$keyX = json_decode($a2[1], true); 
			 }
		 }
		}
		
		return $obj; 
		 
	}
	
	
}