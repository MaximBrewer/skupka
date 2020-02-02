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

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

class OPCstock {
	
/* return definition: 
0: Set color code for a product in stock (Selected product's quantity is larger than stock available) Class name: opc_product_instock
1: Set color code for a product out of stock (Selected product's quantity is larger than available stock quatity) Class name: opc_product_outofstock
2: Set color code for a product that has a small quantity than stock alert quantity set up at the product details (Product stock quantity is in low stock alert range. Actual quantity of selected product is not considered here) Class name: opc_product_questionable
3: Selected product's quantity is larger than stock available (Only some of the selected product's quantity is available) Class name: opc_product_quantitylarger
false: do not alter... 
*/

 public static function getStatus(&$product)
 {
	 $avai = 0; 
	 
	 //include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
	 
	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	 
	 $op_ignore_ordered_products = OPCconfig::get('op_ignore_ordered_products', false); 
	 $op_color_codes_enabled = OPCconfig::get('op_color_codes_enabled', array()); 
	 $opc_stock_zero_weight = OPCconfig::get('opc_stock_zero_weight', false); 
	 
	 if (!empty($opc_stock_zero_weight))
     if (empty($product->product_weight)) return false; 
	 
	 
	 $db = JFactory::getDBO(); 
			$q = 'select product_in_stock, product_ordered, low_stock_notification from #__virtuemart_products where virtuemart_product_id = '.(int)$product->virtuemart_product_id.' limit 1'; 
			$db->setQuery($q); 
			$stock = $db->loadAssoc(); 
			$product_in_stock = (float)$stock['product_in_stock']; 
		    $product_ordered = (float)$stock['product_ordered']; 
			$low_stock_notification = (float)$stock['low_stock_notification']; 
			
			
	 
	 
	 if (!isset($product->quantity))
	 {
		 $product->quantity = 1; 
	 }
		 
	 $quantity = (float)$product->quantity; 
	 
	 
	 
	 if ($op_ignore_ordered_products)
	 {
		 $avai = $product_in_stock; 
	 }
	 else
		 $avai = $product_in_stock - $product_ordered; 
	 
	 
	 // product not in stock in general...
	 if ((!empty($op_color_codes_enabled)) && (!empty($op_color_codes_enabled[1])))
	 if ($avai <= 0) return 1; 
	 
	 
	 // product in stock 
	 if ((!empty($op_color_codes_enabled)) && (!empty($op_color_codes_enabled[0])))
	 if ($avai >= $quantity)
     {
		 return 0; 
	 }
	 
	 // some of the quantity is not in stock
	  if ((!empty($op_color_codes_enabled)) && (!empty($op_color_codes_enabled[3])))
	 if ($quantity > $avai)
	 {
		 return 3; 
	 }
	 
	 // low stock notification
	 if ((!empty($op_color_codes_enabled)) && (!empty($op_color_codes_enabled[2])))
	 if ($avai <= $low_stock_notification)
	 {
		 return 2; 
	 }
	 
	 
	
	 
	return false; 
	 
	 
 }
 public static function includeCSS()
 {
	 static $runOnce; 
	 if (!empty($runOnce)) return; 
	 
	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	 
	 $op_ignore_ordered_products = OPCconfig::get('op_ignore_ordered_products', false); 
	 $op_color_codes_enabled = OPCconfig::get('op_color_codes_enabled', array()); 
	 
	
	 
	 $op_color_codes = OPCconfig::get('op_color_codes', array()); 
	 
	  
	 $doc = JFactory::getDocument(); 
	 $class = get_class($doc); 
	 $class = strtoupper($class); 
	 // never run in an ajax context !
	 $arr = array('JDOCUMENTHTML', 'JOOMLA\CMS\DOCUMENT\HTMLDOCUMENT'); 
	 if (in_array($class, $arr)) 
	 {
		 $css = ''; 
	 }
	 else return; 
	 
	 if (!empty($op_color_codes_enabled))
	 foreach ($op_color_codes_enabled as $k=>$v2)
	 {
		 if (empty($op_color_codes[$k])) continue; 
		 
		 $v = $op_color_codes[$k]; 
		 
		 if ($k == 0)
		 $css .= ' #vmMainPageOPC .opc_product_instock, #vmMainPageOPC .opc_product_instock_product { color: '.$v.' !important; } '; 
	 else
		 if ($k == 2)
		 $css .= ' #vmMainPageOPC .opc_product_questionable, #vmMainPageOPC .opc_product_questionable_product  { color: '.$v.' !important; } '; 
	 else
		 if ($k == 3)
		 $css .= ' #vmMainPageOPC .opc_product_quantitylarger, #vmMainPageOPC .opc_product_quantitylarger_product { color: '.$v.' !important; } '; 
	 else
		 if ($k === 1)
		 $css .= ' #vmMainPageOPC .opc_product_outofstock, #vmMainPageOPC .opc_product_outofstock_product { color: '.$v.' !important; } '; 
	 }
	 
	 $doc->addStyleDeclaration($css); 
	 
	 $runOnce = true; 
 }
 
 public static function getLabel($status)
 {
	  
	  $op_color_texts = OPCconfig::get('op_color_texts', array()); 
	  
	  if (!empty($op_color_texts[$status])) return JText::_($op_color_texts[$status]); 
	  
	  return ''; 
 }
 
 public static function getClassName($status)
 {
	  
	  
	  if ($status === false) return ''; 
	  switch ($status)
	  {
		  case 0: 
		   return 'opc_product_instock'; 
		  case 2: 
		    return 'opc_product_questionable'; 
		  case 3: 
		     return 'opc_product_quantitylarger'; 
		  case 1: 
		   return 'opc_product_outofstock'; 
		  default: return ''; 
	  }
	  return ''; 
 }
 
 
}