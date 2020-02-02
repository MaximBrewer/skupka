<?php
defined ('_JEXEC') or die();
jimport('joomla.form.formfield');
class JFormFieldRanges extends JFormField {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'ranges';

	function getInput () {
		
		$name = $this->name; 
		$value = $this->value; 
		
		$control_name = 'params'; 
		$cid = JRequest::getVar('cid'); 
		if (empty($cid)) return 'Method not found, click save first !'; 
		
		$obj = $this->getParams($cid); 
		
		$row = $this->addScripts(); 
		
		
		$html = '<table id="range_table"><tr><th>From total (incl)</th><th>To (not incl, 0 = <span style="font-size: 2em; vertical-align:middle;">&infin;</span>)</th><th>Shipping Costs</th></tr>'; 
	   
	    $n = 0; 
	    if (!empty($obj->ranges))
		{
			
			foreach ($obj->ranges as $data)
			{
				if ((empty($data['from'])) && (empty($data['to'])) && (empty($data['price']))) continue; 
			  $row_i = $row; 
		      $row_i = str_replace('{n}', $n, $row_i); 		
			  $row_i = str_replace('{val_from_'.$n.'}', $data['from'], $row_i); 		
			  $row_i = str_replace('{val_to_'.$n.'}', $data['to'], $row_i); 		
			  $row_i = str_replace('{val_price_'.$n.'}', $data['price'], $row_i); 		
			  $row_i = str_replace('add_more_class_'.$n, 'add_more_class_'.$n.' more_class_hidden', $row_i); 
			  $html .= $row_i; 
			  $n++; 
			}
		}
		
		{
			  $row_i = $row; 
		      $row_i = str_replace('{n}', $n, $row_i); 		
			  $row_i = str_replace('{val_from_'.$n.'}', '', $row_i); 		
			  $row_i = str_replace('{val_to_'.$n.'}', '', $row_i); 		
			  $row_i = str_replace('{val_price_'.$n.'}','', $row_i); 	
			  
			  $html .= $row_i; 
		}
	  
		/*
		$html .= '<tr>'; 
		$html .= '<td><input class="inputbox" type="text" name="'.$name.'[1][from]"  value="" /></td>'; 
		$html .= '<td><input class="inputbox" type="text" name="'.$name.'[1][to]"  value="" /></td>'; 
		$html .= '<td><input class="inputbox" type="text" name="'.$name.'[1][price]"  value="" /></td>'; 
		$html .= '</tr>'; 
		*/
		$html .= '</table>'; 
		
		return $html; 
	}
	
	private function addScripts()
	{
		$name = $this->name; 
		$app = JFactory::getApplication();
        if( !$app->isAdmin() ) return; 
		$root = JURI::root(); 
		$root = str_replace('/administrator/', '/', $root); 
		if (substr($root, -1) !== '/') $root .= '/'; 
		$js = $root.'plugins/vmshipment/rupostel_range_shipping/assets/range_shipping.js';
		JHtml::script($js); 
		 
		 
		$row = '<tr class="tablerow tablerow_{n}">'; 
		$row .= '<td><input class="inputbox" type="text" name="'.$name.'[{n}][from]"  value="{val_from_{n}}" /></td>'; 
		$row .= '<td><input class="inputbox" type="text" name="'.$name.'[{n}][to]"  value="{val_to_{n}}" /></td>'; 
		$row .= '<td><input class="inputbox" type="text" name="'.$name.'[{n}][price]"  value="{val_price_{n}}" /></td>'; 
		$row .= '<td><a href="#" class="add_more add_more_class_{n}" id="add_more_{n}" onclick="return addMore(this);">Add More</a></td>'; 
		$row .= '</tr>'; 
		
		$doc = JFactory::getDocument(); 
		
		$jsrow = str_replace('{val_from_{n}}', '', $row); 
		$jsrow = str_replace('{val_to_{n}}', '', $jsrow); 
		$jsrow = str_replace('{val_price_{n}}', '', $jsrow); 
		
		$css = ' a.more_class_hidden { display: none; } '; 
		$doc->addStyleDeclaration($css); 
		$js = '
/*<![CDATA[*/
var rowIns = \''.$jsrow.'\'; 
/*]]>*/
'; 
		$doc->addScriptDeclaration($js); 
		return $row; 
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