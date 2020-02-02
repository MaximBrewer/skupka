<?php
/* 
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

// load OPC loader
//require_once(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'helpers'. DIRECTORY_SEPARATOR .'loader.php'); 

defined('_JEXEC') or die('Restricted access');

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
require_once(JPATH_ROOT. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'helpers'. DIRECTORY_SEPARATOR .'cache.php'); 
require_once(JPATH_ROOT. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'helpers'. DIRECTORY_SEPARATOR .'ajaxhelper.php'); 
    
	
class OPCTransform {
 
	
public static function getUnclosedTag($tag, $html, $prop="")
 {
	 // max 10 tries
	 for ($xstart=0; $xstart<strlen($html); $xstart++)
	 {
	 $x1 = stripos($html, '<'.$tag, $xstart); 
	 $x2 = stripos($html, '>', $x1); 
	 if ($x1 !== false)
	 if ($x2 !== false)
	 {
		 if (!empty($prop))
		 {
		 $x3 = stripos($html, $prop, $x1+1); 
		 if ($x3 < $x2)
		 {
			 // found match
			 $html = substr($html, $x1, $x2-$x1+1); 
			 return $html; 
		 }
		 else 
		 {
		 $xstart = $x3; 
		 continue; 
		 }
		 }
		 else
		 {
			 $html = substr($html, $x1, $x2-$x1+1); 
			 return $html; 
		 }
		 
		 
		 return ''; 
	 }
    }
	 return ''; 
	 
 }
 public static function getInnerTag($tag, $html, $which=0)
 {
	 $posa = basketHelper::strposall($html, '<'.$tag); 
	 if (empty($posa)) return ""; 
	 if (!empty($posa[$which]))
	 $x1 = $posa[$which]; //stripos($html, '<'.$tag); 
     else
	 $x1 = stripos($html, '<'.$tag); 
	 $x2 = stripos($html, '</'.$tag, $x1); 
	 if ($x1 !== false)
	 if ($x2 !== false)
	 {
		 $x3 = stripos($html, '>', $x1+1); 
		 $html = substr($html, $x3+1, $x2-($x3+1)); 
		 
		 return $html; 
	 }
	 
	 
 }
 
 public static function getOverride($layout_name, $name, $psType, &$ref, &$method='', &$htmlIn=array(), $extra=array(), $viewData=array())
	{
		static $theme; 
		if (empty($theme))
		{
		
		
		require_once(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'helpers'. DIRECTORY_SEPARATOR .'renderer.php'); 
		$selected_template = OPCrenderer::getSelectedTemplate();  
		
		$theme = $selected_template; 
		}
		
		/*
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('plgGetOpcOverride', array($layout_name, $name, $psType, &$ref, &$method, &$htmlIn, $extra));   
		*/
		
		if (!empty($returnValues))
		foreach ($returnValues as $v) {
		  if ($v === true) return $htmlIn; 
		}
		
		
		if (file_exists(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'themes'. DIRECTORY_SEPARATOR .$theme. DIRECTORY_SEPARATOR .'overrides'. DIRECTORY_SEPARATOR .$psType. DIRECTORY_SEPARATOR .$name. DIRECTORY_SEPARATOR .$layout_name.'.php'))
		 {
		  
		   $name = JFile::makeSafe($name); 
		   $layout_name = JFile::makeSafe($layout_name); 
		   $layout = JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'themes'. DIRECTORY_SEPARATOR .$theme. DIRECTORY_SEPARATOR .'overrides'. DIRECTORY_SEPARATOR .$psType. DIRECTORY_SEPARATOR .$name. DIRECTORY_SEPARATOR .$layout_name.'.php';
		   $isset = true; 
		 }
		 else
		if (file_exists(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'overrides'. DIRECTORY_SEPARATOR .$psType. DIRECTORY_SEPARATOR .$name. DIRECTORY_SEPARATOR .$layout_name.'.php'))
		 {
		  		   $isset = true; 
		   $name = JFile::makeSafe($name); 
		   $layout_name = JFile::makeSafe($layout_name); 
		   $layout = JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'overrides'. DIRECTORY_SEPARATOR .$psType. DIRECTORY_SEPARATOR .$name. DIRECTORY_SEPARATOR .$layout_name.'.php';
		 }
		 
		
		 if (!empty($layout)) 
		 {
		  include($layout); 
		  return $htmlIn; 
		 }
		 return $htmlIn; 
		
		
	}
 
 
 public static function overridePaymentHtmlBefore(&$html, $cart, $vm_id=0, $name, $type)
 {
 
 
 
   jimport('joomla.filesystem.file');
   $name = JFile::makeSafe($name); 
   $type = JFile::makeSafe($type); 
   if (file_exists(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'overrides'. DIRECTORY_SEPARATOR .'payment'. DIRECTORY_SEPARATOR .$name. DIRECTORY_SEPARATOR .'before_render'.'.php'))
     {
	    include(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'overrides'. DIRECTORY_SEPARATOR .'payment'. DIRECTORY_SEPARATOR .$name. DIRECTORY_SEPARATOR .'before_render'.'.php'); 
	 }
   
 }
 
 public static function overridePaymentHtml(&$html, $cart, $vm_id=0, $name, $type)
 {
   jimport('joomla.filesystem.file');
   $name = JFile::makeSafe($name); 
   $type = JFile::makeSafe($type); 
   if (file_exists(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'overrides'. DIRECTORY_SEPARATOR .'payment'. DIRECTORY_SEPARATOR .$name. DIRECTORY_SEPARATOR .'after_render'.'.php'))
     {
	    include(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'overrides'. DIRECTORY_SEPARATOR .'payment'. DIRECTORY_SEPARATOR .$name. DIRECTORY_SEPARATOR .'after_render'.'.php'); 
	 }
   
 }
 
 public static function getIdInHtml($shipping_method, $type='shipment') {
	 $ida = OPCTransform::getFT($shipping_method, 'input', 'virtuemart_'.$type.'method_id', 'type', 'hidden', '>', 'value');
		
	if (empty($ida) || ((count($ida)==1) && (isset($ida[0])) && (empty($ida[0]))))
	{
	$ida = OPCTransform::getFT($shipping_method, 'input', 'virtuemart_'.$type.'method_id', 'type', 'radio', '>', 'value');
	}
		
	if ((is_array($ida) && (!empty($ida))))
	{
		$id = reset($ida); 
		$id = (int)$id; 
	}
	else
	{
		$id = 0; 
	}
	return $id; 
 }
 
 public static function overrideShippingHtml(&$html, $cart, $vm_id=0)
 {
    //include(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'helpers'. DIRECTORY_SEPARATOR .'third_party'. DIRECTORY_SEPARATOR .'third_party_shipping_html.php'); 
	if (empty($vm_id))
	{
	 $vm_id = OPCTransform::getFT($html, 'input', 'virtuemart_shipmentmethod_id', 'type', 'radio', '>', 'value');
	 $vm_id = reset($vm_id); 
	 //$vm_id = $vm_id[0]; 
	}
	OPCloader::getPluginMethods(); 
	jimport('joomla.filesystem.file');
	if (!isset(OPCloader::$methods['shipment'][$vm_id])) return $html;
	$name = OPCloader::$methods['shipment'][$vm_id]['shipment_element']; 
	$name = JFile::makeSafe($name);
	if (empty($name)) return ''; 
	
	
	static $theme; 
		if (empty($theme))
		{
		
		
		require_once(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'helpers'. DIRECTORY_SEPARATOR .'renderer.php'); 
		$selected_template = OPCrenderer::getSelectedTemplate();  
		
		$theme = $selected_template; 
		}
	
	$psType = 'shipment'; 
	$layout_name = 'html'; 
	
			if (file_exists(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'themes'. DIRECTORY_SEPARATOR .$theme. DIRECTORY_SEPARATOR .'overrides'. DIRECTORY_SEPARATOR .$psType. DIRECTORY_SEPARATOR .$name. DIRECTORY_SEPARATOR .$layout_name.'.php')) {
include(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'themes'. DIRECTORY_SEPARATOR .$theme. DIRECTORY_SEPARATOR .'overrides'. DIRECTORY_SEPARATOR .$psType. DIRECTORY_SEPARATOR .$name. DIRECTORY_SEPARATOR .$layout_name.'.php'); 
			}
			else
	if (file_exists(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'overrides'. DIRECTORY_SEPARATOR .'shipment'. DIRECTORY_SEPARATOR .$name. DIRECTORY_SEPARATOR .'html.php'))
	{
	include(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'overrides'. DIRECTORY_SEPARATOR .'shipment'. DIRECTORY_SEPARATOR .$name. DIRECTORY_SEPARATOR .'html.php'); 
	
	}
	else
    if (file_exists(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'helpers'. DIRECTORY_SEPARATOR .'third_party'. DIRECTORY_SEPARATOR .$name. DIRECTORY_SEPARATOR .'html.php'))
	{
	include(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'helpers'. DIRECTORY_SEPARATOR .'third_party'. DIRECTORY_SEPARATOR .$name. DIRECTORY_SEPARATOR .'html.php'); 
	
	}
	
	return $html; 
 }
 
 public static function shippingToSelect($htmla, &$num, &$cart)
 {
	 //$extrainside = ''; 
	 $options = '';
	 $extra = array(); 
	 // this will always be rendered inside the checkout form: 
	 
	 include(JPATH_ROOT. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'config'. DIRECTORY_SEPARATOR .'onepage.cfg.php'); 
		if (!class_exists('CurrencyDisplay'))	
		require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'currencydisplay.php');
		$currencyDisplay = CurrencyDisplay::getInstance($cart->pricesCurrency);	 		
		
			foreach ($htmla as $shipment_html) {
				
				//$id =  self::getFT($paymentplugin_payment, 'input', 'virtuemart_shipmentmethod_id', 'name', 'virtuemart_shipmentmethod_id', '>', 'value');				
				$id =  self::getFT($shipment_html, 'input', 'virtuemart_shipmentmethod_id', 'type', 'radio', '>', 'id');
				$value =  self::getFT($shipment_html, 'input', 'virtuemart_shipmentmethod_id', 'type', 'radio', '>', 'value');
				
				if ((empty($id) || (empty($value))))
				{
					
					continue; 
				}
				
				$value_b = reset($value); 
				
				
				foreach ($id as $k=>$multi)
				{
					
				if (empty($value[$k])) $value[$k] = $value_b; 	
					
				$newoptions = ''; 
				include(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'helpers'. DIRECTORY_SEPARATOR .'third_party'. DIRECTORY_SEPARATOR .'third_party_shipping_options.php'); 
				if (!empty($newoptions)) 
				{
				$options .= $newoptions; 
				
				continue; 
				}
				
				
				$html = self::getInnerTag('label', $shipment_html, $k); 
				$x = str_replace($html, '', $shipment_html); 
				$t1 = strip_tags($x); 
				$t2 = trim($t1); 
				$hasextra = false; 
				if (!empty($t2))
				{
				  $x2 = self::getUnclosedTag('input', $x, 'virtuemart_shipmentmethod_id'); 
				  // remove the payment input
				  $x = str_replace($x2, '', $x); 
				  
				  
				  $extra[$id[$k]] = '<div class="shipmennt_extra" style="display: none;" id="extra_shipment_'.$id[$k].'">'.$x.'</div>'; 					
				  $hasextra = true; 
				}
				else
				{
			    $ind  = reset($id); 
				$extra[$ind] = ''; 
			    }
				$html = strip_tags($html); 
				
				$options .= '<option value="'.$value[$k].'" id="'.$id[$k].'"'; 
				if ($hasextra)
					$options .= ' rel="'.$id[$k].'" '; 
				$html = trim($html); 
				$options .= '>'.$html.'</option>'; 
				$num++; 
				}
				
				
			
			}
	include(JPATH_SITE. DIRECTORY_SEPARATOR ."components". DIRECTORY_SEPARATOR ."com_onepage". DIRECTORY_SEPARATOR ."config". DIRECTORY_SEPARATOR ."onepage.cfg.php");
	if (!empty($shipping_inside_choose))
	{
	 $options = '<option value="choose_shipping" id="shipment_id_0">'.OPCLang::_('COM_VIRTUEMART_LIST_EMPTY_OPTION').'</option>'.$options; 
	}
	$select = '<select autocomplete="off" id="opcShippingSelect" class="opcShippingSelect" onchange="javascript:Onepage.changeTextOnePage3(op_textinclship, op_currency, op_ordertotal);" name="virtuemart_shipmentmethod_id">'.$options.'</select>';
	//$extra['-1'] = $select; 
	
	$html = $select; 
	foreach ($extra as $l)
		$html .= $l; 
	
	
	
	return $html; 
			
 

 }
 public static function paymentToSelect($htmla, $shipping, $dpps)
 {
	 include(JPATH_ROOT. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'config'. DIRECTORY_SEPARATOR .'onepage.cfg.php'); 
	 
	  $session = JFactory::getSession(); 
		$data = $session->get('opc_fields', '', 'opc'); 
		if (empty($data)) $data = array(); 
		else
		$data = @json_decode($data, true); 
		if (!empty($data['p_id']))
			$payment_default = $data['p_id']; 

	 
	 
	  $pid = JRequest::getVar('payment_method_id', $payment_default);
	
	if ($payment_default === 'none')  
	{
	$payment_default = 0; 
	$adds = true; 
	}
	

	  
	 $options = '';
	 
	 if (!empty($adds))
	 {
	   $options .= '<option not_a_valid_payment="not_a_valid_payment" value="0" id="payment_id_0">'.OPCLang::_('COM_VIRTUEMART_LIST_EMPTY_OPTION').'</option>'; 
	 }
	 
	 $extra = array(); 
	 
	 		foreach ($htmla as $paymentplugin_payments) {
		    if (is_array($paymentplugin_payments)) {
			foreach ($paymentplugin_payments as $paymentplugin_payment) {
				
				$id =  self::getFT($paymentplugin_payment, 'input', 'virtuemart_paymentmethod_id', 'name', 'virtuemart_paymentmethod_id', '>', 'value');	
				
				 OPCloader::opcDebug('checking shipping '.$shipping, 'payment transform'); 
				 OPCloader::opcDebug('dpps:', 'payment transform'); 
				 OPCloader::opcDebug($dpps, 'payment transform'); 
				 OPCloader::opcDebug('dpps_disable:', 'payment transform'); 
				 OPCloader::opcDebug($dpps_disable, 'payment transform'); 
					if (!empty($shipping))
				if (!empty($dpps))
				if (!empty($disable_payment_per_shipping))
				{
				
				  $idp = reset($id); 
				  foreach ($dpps_disable as $k=>$v)
				   {
				     if (!empty($dpps[$k]))
					 foreach ($dpps[$k] as $y=>$try)
					 {
					 
				     if ((int)$dpps[$k][$y] == (int)$shipping)
					 {
					 OPCloader::opcDebug('found shipping '.$dpps[$k][$y].' testing payment id'.$idp, 'payment transform'); 
				     if ($dpps_disable[$k] == $idp)
					 {
					 OPCloader::opcDebug('disabling payment id '.$idp.' for shipping id '.$shipping, 'payment transform'); 
					 $paymentplugin_payment = ''; 
					 continue 3; 
					 }
					 }
					 }
				   }
				}
				
				$html = self::getInnerTag('label', $paymentplugin_payment); 
				
				// remove description: 
				$xd = stripos($html, '<span class="vmpayment_description">'); 
				if ($xd!==false)
				{
				
				$s1 = stripos($html, '</span>', $xd); 
				$len = strlen('</span>');
				$html = substr($html, 0, $xd).substr($html, $s1+$len); 
				 
				}
				$x = str_replace($html, '', $paymentplugin_payment); 
				$t1 = strip_tags($x); 
				$t2 = trim($t1); 
				$hasextra = false; 
				if (!empty($t2))
				{
				  $x2 = self::getUnclosedTag('input', $x, 'virtuemart_paymentmethod_id'); 
				  // remove the payment input
				  $x = str_replace($x2, '', $x); 
				  
				  $ind = reset($id); 
				  $extra[$ind] = '<div class="payment_extra" style="display: none;" id="extra_payment_'.$ind.'">'.$x.'</div>'; 					
				  $hasextra = true; 
				}
				else
				{
				$ind = reset($id); 
				$extra[$ind] = ''; 
			    }
				$html = strip_tags($html); 
				$ind = reset($id); 
				$options .= '<option value="'.$ind.'"'; 
				
				if ($ind==$pid)
					$options .= ' selected="selected" '; 
				
				if ($hasextra)
					$options .= ' rel="'.$ind.'" '; 
				$options .= '>'.$html.'</option>'; 
				
				
				
			}
			}
			}
	$select = '<select autocomplete="off" id="opcPaymentSelect" class="opcPaymentSelect" onchange="javascript: Onepage.runPaySelect(this);" name="virtuemart_paymentmethod_id">'.$options.'</select>';
	//$extra['-1'] = $select; 
	$a2 = array(); 
	$a2['extra'] = ''; 
	$a2['select'] = $select;
    /*	
	foreach ($extra as $key=>$l)
		$a2['extra'] .= $l; 
	*/
	
	
	if (empty($extra)) $extra = array(); 
	$a2['extra'] = $extra; 
	
	return $a2; 
			
 }
 // html = <input type="radio" value="123" name="myname" id="myid" />
// tagname = input
// mustIncl = myname
// mystProp = type
// mustVal = 123
// getProp = id
public static $_cachesearch; 
public static function getFT($html, $tagname, $mustIncl='', $mustProp='', $mustVal='', $ending='>', $getProp) {

	return self::getFT2($html, $tagname, $mustIncl, $mustProp, $mustVal, $ending, $getProp); 
	
	$ret = array(); 
	/*
	if (function_exists('str_get_html'))
	{
		$html = str_get_html($html); 
		$s = $tagname.'['.$mustProp.'='.$mustVal.']';
		echo $s."<br />"; 
		$res = $html->find($s); 
		
		if (isset($res->$getProp))
		{
			$return = $res->$getProp; 
			if (!empty($return))
			return $return; 
		}
		
		
		
	}
	else
		*/
	{
		if (class_exists('DOMDocument'))
		{
			 
			 $start_tag = urldecode('%3C%3F'); 
			 $end_tag = urldecode('%3F%3E');
			 $new_html = $start_tag."xml version=\"1.0\" encoding=\"UTF-8\"".$end_tag."<xml><div>".$html."</div></xml>"; 
			 $new_html = '<html><head><meta http-equiv="content-type" content="text/html; charset=utf-8" /></head><body><div>'.$html."</div></body></html>"; 

			try
			{
			$doc=@DOMDocument::loadHTML($new_html);
			
			$xpath= new DomXpath($doc);
			}
			catch (Exception $e)
			{
				return self::getFT2($html, $tagname, $mustIncl, $mustProp, $mustVal, $ending, $getProp); 
			}
			
			////input[@type='radio' and @value='1']/@name ?
			
			
			//we are not using this function,because this does not work as intended: 
			$search = '//'.$tagname.'[@'.$mustProp.'=\''.$mustVal.'\' and ./'.$tagname.'[contains(., \''.$mustIncl.'\')]]'; 
			
			//stAn, this works: 
			
			//$search = '//'.$tagname.'[@'.$mustProp.'=\''.$mustVal.'\']'; 
			
			$q = $xpath->query($search);
			
		
		
			
			$zi = 0; 
			
			if (!empty($q))
			foreach($q as $node){
				
				if (method_exists($node, 'getAttribute'))
				{
				$gz = $node->getAttribute($getProp); 
				if (!empty($gz))
				{
					$gz = (string)$gz; 
					$ret[$gz] = $gz; 
					continue; 
				}
				}
				
			  $mini = (string)$doc->saveXML($node);
			  
			  $res = self::getFT2($mini, $tagname, $mustIncl, $mustProp, $mustVal, $ending, $getProp); 
			  if (!empty($res))
			  foreach ($res as $k=>$v)
			  {
				  $ret[$k] = $v; 
			  }
			  $zi++; 
			}
			
			
		}
		else
		{
			return self::getFT2($html, $tagname, $mustIncl, $mustProp, $mustVal, $ending, $getProp); 
		}
		
		
	}
	
	  if (empty($ret)) return false; 
	  
	  $zy = 0; $ret2 = array(); 
	    foreach ($ret as $k=>$v)
		  {
			  
			  $ret2[$zy] = html_entity_decode($v); 
			  $zy++; 
		  }
	  
	 return $ret2; 
}
public static function getFT2($html, $tagname, $mustIncl='', $mustProp='', $mustVal='', $ending='>', $getProp)
{
	
    $input = md5($html.' '.$tagname.' '.$mustIncl.' '.$mustProp.' '.$mustVal.' '.$ending.' '.$getProp); 
   
	if (empty(self::$_cachesearch)) self::$_cachesearch = array(); 
	if (isset(self::$_cachesearch[$input])) return self::$_cachesearch[$input]; 
  
  
  $posa = basketHelper::strposall($html, $mustIncl); 
  $rev = strrev($html); 
  $len = strlen($html); 
  $ret = array(); 

//if ($mustIncl == 'usps_id_1')
{
   // $x = htmlentities($html); 
  
  
}
  
  if (!empty($posa))
  foreach ($posa as $x1)
  {
   $x2 = stripos($rev, strrev('<'.$tagname), $len-$x1); 
   $x2 = $len - $x2 - strlen('<'.$tagname) + 1; 
   
   if ($x2 < $x1)
   {
     
     
	 
	 // here we can search for /> or just > depending on what we need... 
	 $x3 = stripos($html, $ending, $x2); 
	 if ($x3 === false) continue; 
	 
	 // our search tag starts at $x2 and ends at $x3
	 $temp = substr($html, $x2, $x3-$x2); 
	
	

	 if (!empty($mustProp))
	  {
	     
			 
	  	 $val = self::getValFT($temp, $mustProp); 
		 if ($val === false) continue; 
		 if (!empty($mustVal))
		 if ($val != $mustVal) continue; 

	  }
	  
	  $val = self::getValFT($temp, $getProp); 
	  if ($val !== false) 
	  {
	
	  $ret[md5($val)] = $val; 
	  continue;
	  }
	  
	  
   }
   else
   continue;
  }
  if (empty($ret)) {
	  self::$_cachesearch[$input] = false; 
	  return false; 
  }
  
  $ret2 = array(); 
  
  foreach ($ret as $v)
  {
	  $ret2[] = $v; 
  }
  
  self::$_cachesearch[$input] = $ret2; 
  return $ret2; 
  
}
public static function getFTArray($html, $tagname, $mustProp, $mustVal)
{
}
// search value of a prop in temp
public static function getValFT($temp, $mustProp)
{
     // example data-usps='{"service":"Parcel Post","rate":15.09}'
	 // or id="xyz"
	    if (substr($mustProp, strlen($mustProp)-1)=='*')
		{
		$sb = substr($mustProp, 0, -1); 
		
		$x51 = stripos($temp, $sb);
		if ($x51 === false) return false;
		$x5 = stripos($temp, '=', $x51); 
		}
		else
	    $x5 = stripos($temp, $mustProp.'=');
		
	    if ($x5===false) return false; 
		
		$single = false;
		
		 
		   $x4 = stripos($temp, '"', $x5);
		   $x42 = stripos($temp, "'", $x5);
		   
		   if (($x42 !== false) && ($x4 !== false))
		   if ($x42 < $x4)
		   {
		    // we will start with ' instead of "
			$x4 = $x42; 
			$single = true; 
		   }
		   
		   // search for start and end by '
		   if ($single) 
		    {
			//$x4 = stripos($temp, "'", $x5);
			if ($x4 !== false)
			{
			  //$single = true; 
			  if ($single)
			  $x6e = basketHelper::strposall($temp, "'", $x4+1);
			  else $x6e = basketHelper::strposall($temp, '"', $x4+1);
			  
			  if (!empty($x6e))
			  foreach ($x6e as $x6test)
			   {
			     if (substr($temp, $x6test-1, 1)!=urldecode('%5C'))
				 {
				 $x6 = $x6test; 
				 break; 
				 }
			   }
			  //$x6 = stripos($temp, "'", $x4+1);
			}
			}
		   
		   if ($x4 === false) return ""; 
		   
		   // search for end by " 
		   if (!$single)
		   if (!isset($x6))
		   {
		     $x6e = basketHelper::strposall($temp, '"', $x4+1);
			  foreach ($x6e as $x6test)
			   {
			     if (substr($temp, $x6test-1, 1)!=urldecode('%5C'))
				 {
				 $x6 = $x6test; 
				 break; 
				 }
			   }
		     //$x6 = stripos($temp, '"', $x4+1);
		   }
		   if (!isset($x6)) 
		   {
		     return "";
		     echo $mustProp.' in: '.$temp.' '.$x4; 
		   }
		   if ($x6 === false) return ""; 
		   
		   $val = substr($temp, $x4+1, $x6-$x4-1); 
		   
		   return $val; 
		   
		 
	  
	  return false; 
}

// inserts an object or array $ins after/before name $field in $arr
 public static function insertAfter(&$arr, $field, $ins, $newkey, $before=false)
  {
    $new = array(); 
	foreach ($arr as $key=>$val)
	 {
	   if ($key == $field)
	   {
	   if ($before) $new[$newkey] = $ins; 
	   else { 
	     $new[$key] = $val; 
		 $new[$newkey] = $ins; 
	   }
	   }
	   else
	   {
	    $new[$key] = $val; 
	   }
	   
	 }
	 $arr = $new;
	 
	 
  }



}