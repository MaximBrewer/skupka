<?php
/*
*
* @copyright Copyright (C) 2007 - 2013 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* stAn note: Always use default headers for your php files, so they cannot be executed outside joomla security 
*
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

// this feed is referenced by: https://support.google.com/merchants/answer/160589

// all classes should be named by <element>Xml per it's manifest with upper letter for the element name and the Xml
class Ceneo_plXml {
 function clear()
  {
  }
  function startHeaders()
  {
     $xml = '<?xml version="1.0" encoding="utf-8"?>'."\n"; 
	 $xml .= '<offers xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" version="1">'."\n";  
	

	 
	 return $xml; 
	 
  }
  function getProductTax(&$product, $tax_info)
  {
	  

	  if (empty($product->product_tax_id)) return $tax_info; 
	  $product->product_tax_id = (int)$product->product_tax_id; 
	  
	  
	  if ($product->product_tax_id === -1) return array(); 
	  
	  $ret = array(); 
	  
	  if (!empty($tax_info))
	  foreach ($tax_info as $t)
	  {
		  $t['virtuemart_calc_id'] = (int)$t['virtuemart_calc_id']; 
		  if ($t['virtuemart_calc_id'] === $product->product_tax_id)
		  {
			  $ret[] = $t; 
			  // return the ID found:
			  return $ret; 
		  }
	  }
	  
	  //return all taxes at once: 
	  return $tax_info; 
	  
  }
  function getTaxInfo(&$product)
  {
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	  static $ret; 
	  
	  if (isset($ret)) return $this->getProductTax($product, $ret); 
	  $ret = array(); 
	  
	  if(!class_exists('calculationHelper')) require(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'calculationh.php');
	// $VirtueMartModelCalc = OPCmini::getModel('calc'); 
	// $calcs = $VirtueMartModelCalc->getCalcs(true, true); 
	
	
  
  
  

		
		$calcModel = VmModel::getModel('calc');
		

		//Test if calculation affects the current entry point
		//shared rules counting for every vendor seems to be not necessary
		$q = 'SELECT * FROM #__virtuemart_calcs ';
		
		$jnow = JFactory::getDate();
		$now = $jnow->toSQL();
		$db = JFactory::getDBO(); 

		$q .= 'WHERE
                `calc_kind` NOT LIKE "merge"
				AND `calc_value_mathop` LIKE "'.$db->escape('+%').'"
                AND `published`="1"
                AND (`virtuemart_vendor_id`= 1 OR `shared`="1" )
				AND ( publish_up = "' . $db->escape($db->getNullDate()) . '" OR publish_up <= "' . $db->escape($now) . '" )
				AND ( publish_down = "' . $db->escape($db->getNullDate()) . '" OR publish_down >= "' . $db->escape($now) . '" )';
		//$q .= $shopperGrpJoin.$countryGrpJoin.$stateGrpJoin;

		$db->setQuery($q);
		$rules = $db->loadAssocList();

		foreach ($rules as $rule2) {

			$ruleD = $calcModel->getCalc($rule2['virtuemart_calc_id']);	
			$rule = array(); 
			foreach ($ruleD as $k=>$v)
			{
				$rule[$k] = $v; 
			}
			
			
			if (!empty($rule['calc_countries']))
			{
				
				
			foreach ($rule['calc_countries'] as $ci)
			{
			  $q = 'select * from #__virtuemart_countries where virtuemart_country_id = '.(int)$ci.' limit 0,1'; 
			  $db->setQuery($q); 
			  $res = $db->loadAssoc(); 
			  if (!empty($res))
			  {
			  foreach ($res as $k3=>$v3)
			  {
				  $rule[$k3] = $v3; 
			  }
			  if (!empty($rule['virtuemart_state_ids']))
			  {
				  foreach ($rule['virtuemart_state_ids'] as $si)
				  {
			  $q = 'select * from #__virtuemart_states where virtuemart_state_id = '.(int)$si.' limit 0,1'; 
			  $db->setQuery($q); 
			  $res2 = $db->loadAssoc(); 
			  if (!empty($res2))
			  {
			  foreach ($res2 as $k2=>$v2)
			  {
				  $rule[$k2] = $v2; 
			  }
			  }
			  $ret[] = $rule; 
				  }
				  continue; 
			  }
			  else
			  {
			   $ret[] = $rule; 
			  }
			  }
			}
			}
			
		}

		return $this->getProductTax($product, $ret); ;

		
		
	}
  
  private function getPid($product, $vm1)
  {
$pid = $product->virtuemart_product_id; 
if (strlen($product->product_sku)>40) {
 $product->product_sku = $product->virtuemart_product_id; 
}

switch ($this->params->pidformat)
{
  case 0: 
   $pid = $product->virtuemart_product_id; 
   break;
  case 1: 
    $pid = $product->product_sku; 
	if (empty($pid))
    $pid = $product->virtuemart_product_id; 
	break; 
  case 2: 
    $pid = $product->product_sku; 
	if (empty($pid))
    $pid = $product->virtuemart_product_id; 
    $lang = $this->params->language; 
	if (!empty($lang) && (stripos($lang, '-')!==false))
	 {
	   $a = explode('-', $lang); 
	   $pid = $pid.'-'.$a[1]; 
	 }
	 else
	 {
	   $tag = JFactory::getLanguage()->getTag(); 
	   $a = explode('-', $tag); 
	   if (!empty($a))
	   $pid = $pid.'-'.$a[1]; 
	 }
	 break; 
	 
}
return $pid; 
	  
  }
  function addItem($product, $vm1)
  {
	  extract($vm1); 
	  
	  
	  
	  $pid = $this->getPid($product, $vm1); 
	  if (empty($pid)) return; 
	  if (empty($vm1['cena_s_dph'])) return; 
	  if (empty($vm1['link'])) return; 
	  $avaidays = $vm1['avaidays']; 
	  if (($product->product_in_stock - $product->product_ordered) <= 0 ) $avaidays = 1; 	  
	  if (!empty($product->product_in_stock))
				{
					$pi = (int)$product->product_in_stock; 
					if ($pi > 0)
					{
						$avaidays = 1; 
					}
				}
	  $avaidays = (int)$avaidays; 
	  $weight = (float)$product->product_weight; 
	  if (empty($product->product_weight_uom)) $product->product_weight_uom = 'KGS'; 
	  if ($product->product_weight_uom != 'KGS') {
		 if (!class_exists('VmConfig')) {
		  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		  VmConfig::loadConfig(); 
		 }

		  if (!class_exists('ShopFunctions')) {
		    require( JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'shopfunctions.php' );
		  }
	  $weight = ShopFunctions::convertWeightUnit ($product->product_weight, $product->product_weight_uom, 'KGS');;
	  }
	  
	  
	  
	  $stock_management = (int)$this->config->stock_management; 
	  $stock = ''; 
	  switch ($stock_management) {
	    case 0: 
		 $n = (int)$product->product_in_stock; 
		 if ($n > 0)
		 $stock = ' stock="'.$n.'" '; 
		 else
		 $stock = ''; 
		 break; 
		
		case 2: 
		 $n = (int)$product->product_in_stock; 
		 if ($n > 0)
		 $stock = ' stock="1" '; 
	     else
		 $stock = ''; 
		 break;
	    case 3: 
		 $stock = ' stock="10" '; 
		 break;
		case 4: 
		 $stock = ''; 
		 break; 
		case 1: 
		 $n = (int)$product->product_in_stock; 
		 $n2 = $n = (int)$product->product_ordered; 
		 $n = $n - $n2; 
		 if ($n < 0) $n = 0; 
		 if ($n > 0)
		 $stock = ' stock="'.$n.'" '; 
	     else $stock = ''; 
		 break;
		
	  }
	  
	  $vm1['link'] = str_replace('&', '&amp;', $vm1['link']); 
	  $vm1['cena_s_dph'] = number_format($vm1['cena_s_dph'], 2, '.', ''); 
	  if (!empty($weight)) {
	  $weight = ' weight="'.number_format($weight, 2, '.', '').'"'; 
	  }
	  else
	  {
		  $weight = ''; 
	  }
	  $data = '<o id="'.$pid.'" url="'.$vm1['link'].'" price="'.$vm1['cena_s_dph'].'" avail="'.$avaidays.'" set="0" '.$weight.' basket="'.(int)$this->config->buy_in_ceneo.'" '.$stock.'>'."\n"; 

	  
				if (!empty($product->paired_category_name))
				{
					$cat_override = str_replace(' > ', '/', $product->paired_category_name); 
					$data .= '<cat><![CDATA['.$cat_override.']]></cat>'."\n";
				}
				else
				{ 
			     if (empty($vm1['longest_cats'])) return; 
				 $lcats = implode('/', $vm1['longest_cats']); 
    			 $data .= '<cat><![CDATA['.$lcats.']]></cat>'."\n";
				}

$data .= '<name><![CDATA['.$vm1['product_name'].']]></name>'."\n"; 
$data .= '<imgs>'."\n"; 

if (!empty($vm1['fullimg']))
$data .= '<main url="'.$vm1['fullimg'].'"/>'."\n"; ; 
if (!empty($vm1['thumb_url']))
$data .= '<i url="'.$vm1['thumb_url'].'"/>'."\n"; ; 
$data .= '</imgs>'."\n"; ; 

$desc = $vm1['desc']; 
if ((!empty($vm1['fulldesc'])) && (empty($vm1['desc'])))
			{
				$desc = $vm1['fulldesc']; 
				$desc = str_replace('<![CDATA[', "\n", $desc); 
				$desc = str_replace(']]>', "\n", $desc); 
				$desc = strip_tags($desc); 
				if (strlen($desc)>30000) {
				  if (function_exists('mb_substr'))
				  $desc = mb_substr($desc, 0, 20000); 
				  else
				  $desc = substr($desc, 0, 29900); 
				}
			}
$data .= '<desc><![CDATA['.$desc.']]></desc>'."\n"; 
$data .= '<attrs>'."\n"; 

$dataTemp = ''; 
if (!empty($product->virtuemart_customs))
			foreach ($product->virtuemart_customs as $k=>$v)
			{
				$id = (int)$v['virtuemart_custom_id']; 
				$value = $v['customfield_value']; 
				
				
				foreach ($this->config->customs_override as $k2=>$v2)
				{
					$ref_id = (int)$v2['config_ref']; 
					
					
					if ($ref_id !== $id) continue; 
					
					$ref_type = $v2['value']; 
					
					
					
					if (!empty($ref_type))
					{
					
					switch ($ref_type)
					{
						case 'size':
						$dataTemp .= '<g:size>'.$value.'</g:size>';
						break;
						
						case 'color':
						$dataTemp .= '<g:color>'.$value.'</g:color>';
						break;
						
						case 'pattern':
						$dataTemp .= '<g:pattern>'.$value.'</g:pattern>';
						break;
						
						case 'material':
						$dataTemp .= '<g:material>'.$value.'</g:material>';
						break;
						
						case 'gender':
						$dataTemp .= '<g:gender>'.$value.'</g:gender>';
						break;
						
						case 'age_group':
						$dataTemp .= '<g:age_group>'.$value.'</g:age_group>';
						break;
						
						case 'size_type':
						$dataTemp .= '<g:size_type>'.$value.'</g:size_type>';
						break;
						
						case 'manufacturer': 
						if (empty($value)) break; 
						$data .= '<a name="Producent"><![CDATA['.$value.']]></a>'."\n"; 
						$manset = true; 
						break; 
						
						case 'isbn': 
						if (empty($value)) break; 
						$data .= '<a name="ISBN"><![CDATA['.$value.']]></a>'."\n"; 
						break; 

						case 'bloz': 
						if (empty($value)) break; 
						if (strlen($value) == 7) $nameK = 'BLOZ_7'; 
						else $nameK = 'BLOZ_12'; 
						$data .= '<a name="'.$nameK.'"><![CDATA['.$value.']]></a>'."\n"; 
						break; 
						
						
						case 'ean': 
						if (empty($value)) break; 
						$data .= '<a name="EAN"><![CDATA['.$value.']]></a>'."\n"; 
						$eanset = true; 
						break; 
						
						case 'mpn': 
						if (empty($value)) break; 
						$data .= '<a name="Kod_producenta"><![CDATA['.$value.']]></a>'."\n"; 
						$mpnset = true; 
						break; 
						
						default: 
						break; 
						
						
					}
					}
					
				}
			}

if (empty($manset))
if (!empty($vm1['manufacturer']))
$data .= '<a name="Producent"><![CDATA['.$vm1['manufacturer'].']]></a>'."\n"; 

if (empty($product->product_mpn)) $product->product_mpn = $product->product_sku; 

if (empty($mpnset)) 
if (!empty($product->product_mpn))	
$data .= '<a name="Kod_producenta"><![CDATA['.$product->product_mpn.']]></a>'."\n"; 

if (empty($eanset))
if (isset($product->product_gtin) && (is_numeric($product->product_gtin)))
$data .= '<a name="EAN"><![CDATA['.$product->product_gtin.']]></a>'."\n"; 

$data .= '</attrs>'; 
$data .= '</o>'; 
	
			return $data; 

  }
  function endHeaders()
  {
     $xml = '</offers>'."\n"; 
	 
	 return $xml; 
  
  }
  function compress()
  {
  }
  
  
 
/*  
   function getPairingUrl()
  {
  
    $return = (string)$this->config->catlink; 
	
  }
  
  function getPairingName()
  {
    $lang = JFactory::getLanguage()->getTag(); 
	$lang = str_replace('-', '_', $lang); 
	return 'googlerss_'.$lang; 
  }
  */
  
  
  function recurseXml(&$el, &$ref)
   {
      foreach($el->children() as $child)
	   {
	      $name = $child->getName(); 
		  if ($name !== 'Category') continue; 
		  
	      $id = (int)$child->Id; //."<br />"; 
		  $txt = (string)$child->Name; //."<br />"; 
		  //$new =& new xmlCategory($id, $txt); 
		  //$new = new xmlCategory($id, $txt); 
		  $new =& $ref::getInstance($id, $txt); 
		  $ref->addItem($new); 
		  //if ($child->hasChildren())
		  if (isset($child->Subcategories))
		  $this->recurseXml($child->Subcategories, $new); 
	   }
   }
  // either return an object or converted categories
  function processPairingData($xml)
  {
  
  $s1 = memory_get_usage(true); 
  
       $return = new xmlCategory(); 
       $r = simplexml_load_string($xml); 
  $s2 =    memory_get_usage(true); 
	   if ($r === false) return false; 
	   
	   

foreach($r->children() as $child) {
 {
   $name = $child->getName(); 
   
   if ($name !== 'Category') continue; 
		  
  $id = (int)$child->Id; //."<br />"; 
  $txt = (string)$child->Name; //."<br />"; 
  //$new =& new xmlCategory($id, $txt); 
  //$new = new xmlCategory($id, $txt); 
  $new =& $return::getInstance($id, $txt); 
  $return->addItem($new); 
  
  //if ($child->hasChildren())
  if (isset($child->Subcategories))
  $this->recurseXml($child->Subcategories, $new); 
 }
}


  return $return; 

	   
  }
  
  
  
  
}
 