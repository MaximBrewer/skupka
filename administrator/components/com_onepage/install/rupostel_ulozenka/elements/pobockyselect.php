<?php
defined ('_JEXEC') or die();

jimport( 'joomla.html.parameter.element.list' );
class JElementPobockyselect extends JElementList {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'pobockyselect';

	function fetchElement ($name, $value, &$node, $control_name) {
		//$name, $value, &$node, $control_name
		$options = array(); 
		//$name = $this->name; 
		//$value = $this->value; 
		//$control_name = 'params'; 
		
		$db = JFactory::getDBO(); 
		$cid = JRequest::getVar('cid'); 
		$q = 'select shipment_params from #__virtuemart_shipmentmethods where shipment_element = \'rupostel_ulozenka\' '; 
		if (!empty($cid))
		 {
		   $cid = (int)$cid[0]; 
		   
		   $q .= ' and virtuemart_shipmentmethod_id = '.$cid; 
		 }
		$db->setQuery($q); 
		
		$params = $db->loadResult(); 
		
		$obj = new stdClass(); 
		
		$e = $db->getErrorMsg(); 
		if (!empty($e)){
		 JFactory::getApplication()->enqueueMessage($e); 
		  return; 
		}
		$err = true; 
		if (empty($params)) $err = true; 
		else
		{
		$a = explode('|', $params); 
		
		foreach ($a as $p)
		 {
		    $a2 = explode('=', $p); 
			if (!empty($a2) && (count($a2)==2))
			 {
			   $keyX = $a2[0]; 
			   $obj->$keyX = json_decode($a2[1]); 
			 }
		 }
		 
		 
		 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmshipment'.DIRECTORY_SEPARATOR.'rupostel_ulozenka'.DIRECTORY_SEPARATOR.'helper.php'); 
		
		
		$xml = UlozenkaHelper::getPobocky($obj, false); 
		
		
		
		$html = ''; 
		if (!empty($xml->error)) return $xml->error; 
		
		if (!empty($xml))
		 {
		    
				
			
				
		   $k=1;
		   foreach ($xml->pobocky as $p)
		     {
								
								$option_name = $p->nazev.' '.$p->ulice.' '.$p->obec.' '.$p->psc; 
								$option_value = $p->id; 
								
								$options[] = JHtml::_('select.option', $option_value, $option_name);
								
								
								
								
			 }
		 }
		}
		 return JHTML::_('select.genericlist', $options, $control_name . '[' . $name . '][]', $class, 'value', 'text', $value, $control_name . $name);
		//return $options; 
	}

}