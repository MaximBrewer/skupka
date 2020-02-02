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


// all classes should be named by <element>Xml per it's manifest with upper letter for the element name and the Xml
class HeurekaXml {
 function clear()
  {
  }
  function startHeaders()
  {
     $xml = '<?xml version="1.0" encoding="utf-8"?>'."\n"; 
	 $xml .= '<SHOP>'."\n"; 
	 return $xml; 
	 
  }
  
  function convert(&$product, &$vm)
  {
	    if (empty($product->prices['product_currency'])) return; 
	  $currency_id = $product->prices['product_currency']; 
	  
	 
	  
	  static $ex; 
	  if (empty($ex)) $ex = array(); 
	  
	  if (!isset($ex[$currency_id]))
	  {
		  
		  $db = JFactory::getDBO(); 
		  $q = 'select `currency_code_3` from `#__virtuemart_currencies` where `virtuemart_currency_id` = '.(int)$currency_id.' limit 0,1'; 
		  $db->setQuery($q); 
		  $from_c = $db->loadResult(); 
		  
		  
		  if (empty($from_c)) $from_c = 'CZK'; 
		  $from_c = strtoupper($from_c); 
		  
		  
		  if ($from_c === 'EUR')
		  {
			  $ex[$currency_id] = 1; 
		  }
		  else
		  {
		  if (!class_exists('convertECB'))
		  {
			  require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'currency_converter'.DIRECTORY_SEPARATOR.'convertECB.php'); 
		  }  
			  $convertECB = new convertECB; 
			  $rate = $convertECB->convert(1000, $from_c, 'EUR'); 
			  if (!empty($rate))
			  $rate = $rate / 1000; 
			  else $rate = 1;
			  
			  $ex[$currency_id] = $rate; 
		  
		  }
		  
	  }
	  
	  $rate = $ex[$currency_id]; 
	  
	  $floatv = floatval($vm['cena_s_dph']); 
	   
	  $vm['cena_s_dph'] = $vm['cena_s_dph'] * $rate; 
	  
	  
  }
  
  function addItem($product, $vm1)
  {
	 
	$this->convert($product, $vm1); 
	
          extract($vm1); 
  
     			// zaciatok shopitem
			$data = '<SHOPITEM>'."\n";
			
			if (!empty($this->config->xml_export_unique))
			{
				$z = (int)$this->config->xml_export_unique; 
				if ($z === 1) {
				 if (!empty($sku))
				 {
				 $product_id = $sku; 
				 }
				}
			}
			
			$data .= '<ITEM_ID>'.$product_id.'</ITEM_ID>'."\n";
			//$data .= '<SKU><![CDATA['.$sku.']]></SKU>'."\n";
			//$data .= '<ITEM_ID><![CDATA['.$sku.']]></ITEM_ID>'."\n";
			$data .= '<PRODUCTNAME><![CDATA['.$product_name.']]></PRODUCTNAME>'."\n";
			$data .= '<PRODUCT><![CDATA['.$product_name.']]></PRODUCT>'."\n";
			$data .= '<SUBTITLE><![CDATA['.$product_name.']]></SUBTITLE>'."\n";
			$data .= '<IMGURL><![CDATA['.$thumb_url.']]></IMGURL>'."\n";
			$data .= '<FULLIMAGE><![CDATA['.$fullimg.']]></FULLIMAGE>'."\n";
			
			if ((!empty($fulldesc)) && (empty($desc)))
			{
				$desc = $fulldesc; 
				$desc = str_replace('<![CDATA[', "\n", $desc); 
				$desc = str_replace(']]>', "\n", $desc); 
				$desc = strip_tags($desc); 
			}
			$data .= '<DESCRIPTION><![CDATA['.$desc.']]></DESCRIPTION>'."\n";
			
			/*
			
			
			$fulldesc = str_replace('[CDATA[', '---', $fulldesc); 
			$fulldesc = str_replace(']]>', '--->', $fulldesc); 

			$data .= '<FULLDESCRIPTION><![CDATA['.$fulldesc.']]></FULLDESCRIPTION>'."\n";
			*/
			
			$this->config->child_type = (int)$this->config->child_type; 
			if ($this->config->child_type === 1)
			{
			//export childs in group_id only if parent and child is exported 
			if (!empty($product->parentProduct))
			{
				$parent_id = $product->parentProduct->virtuemart_product_id; 
			if (!empty($this->config->xml_export_unique))
			{
				$z = (int)$this->config->xml_export_unique; 
				if ($z === 1) {
				 if (!empty($product->parentProduct->product_sku))
				 {
				 $parent_id = $product->parentProduct->product_sku; 
				 }
				}
			}
			
			$data .= '<ITEMGROUP_ID><![CDATA['.$parent_id .']]></ITEMGROUP_ID>'."\n";
				
			}
			
			}				
			
			
			$data .= '<URL><![CDATA['.$link .']]></URL>'."\n";
			
			$live_site = $this->config->xml_live_site; 
			
			
			if (!empty($images))
			{
			$data .= '<IMAGES>'."\n";
			foreach ($images as $im)
			{
			
			$data .= "\t".'<IMAGE>'."\n"; 
			if (substr($im['file_name'], 0, 4) != 'http')
			$data .= "\t"."\t".'<PATH><![CDATA['.$live_site.$im['file_name'].']]></PATH>'."\n";
			else
			$data .= "\t"."\t".'<PATH><![CDATA['.$im['file_name'].']]></PATH>'."\n";
			
			$data .= "\t"."\t".'<TITLE><![CDATA['.$im['file_title'].']]></TITLE>'."\n";
			$data .= "\t".'</IMAGE>'."\n"; 
			}
			$data .= '</IMAGES>'."\n";
			}
			else $data .= '<IMAGES />'."\n"; 
			
			$data .= '<NONSEOLINK><![CDATA['.$node_link.']]></NONSEOLINK>'."\n";

			$t1 = (float)$cena_s_dph; 
			
			
			
			if ($t1 <= 0) {
				OPCXmlExport::log('Skipping product '.$product_name.'('.$product_id.') - empty or negative price'); 
				return ''; 
			}
			
			if (empty($cena_s_dph)) {
				OPCXmlExport::log('Skipping product '.$product_name.'('.$product_id.') - empty or negative price'); 
				return ''; 
			}
			$cena_s_dph = number_format ( $cena_s_dph, 2, ',', ''); 
			
			if ($cena_s_dph === '0,00') {
				OPCXmlExport::log('Skipping product '.$product_name.'('.$product_id.') - empty or negative price'); 
				return ''; 
			}
			 
			$data .= '<PRICE_VAT>'.$cena_s_dph.'</PRICE_VAT>'."\n";
			
			//if ($cena_txt === '0,00') return ''; 
			//$data .= '<PRICE><![CDATA['.$cena_txt.']]></PRICE>'."\n";
			$tax_rate = number_format ( $tax_rate, 2, ',', ''); 
			$data .= '<VAT>'.$tax_rate.'</VAT>'."\n"; 
			
			 $type = 'NEW'; 
	  if (!empty($this->config->bazaar))
	  {
		  if (stripos($a, ',')===false)
		  {
			  $bc = (int)$a; 
			  if (!empty($bc))
				  {
					  if (in_array($bc, $product->categories))
					  {
						  $type = 'bazar'; 
						 
					  }
				  }
		  }
		  else
		  {
		  $a = explode(',', $this->config->bazaar); 
		  if (is_array($a))
		  {
			  foreach ($a as $bc)
			  {
				  $bc = (int)$bc; 
				  if (!empty($bc))
				  {
					  if (in_array($bc, $product->categories))
					  {
						  $type = 'bazar'; 
						 
					  }
				  }
			  }
		  }
		  }
	  }
			
			$data .= '<ITEM_TYPE>'.$type.'</ITEM_TYPE>'."\n";
			/*
			if ($product2->is_override)
			{
				$data .= '<ISCATEGORIZED>1</ISCATEGORIZED>'; 
			}
			*/

			if (!empty($longest_cats))
			{
			//<CATEGORYTEXT>Obuv | Pánska | Bežecká | Nike</CATEGORYTEXT>
			 $lcats = implode(' | ', $longest_cats); 
			}
			else {
				OPCXmlExport::log('Skipping product '.$product_name.'('.$product_id.') - no categories'); 
				return ''; 
			}
			
			
			/*
			if (!empty($cats))
			{
			  $data .= '<ALLCATEGORIES>'."\n"; 
			  foreach ($cats as $c)
			   {
			     $data .= "\t".'<CATEGORYPATH><![CDATA['.implode(' | ', $c).']]></CATEGORYPATH>'."\n"; 
			   }
			  $data .= '</ALLCATEGORIES>'."\n"; 
			}
			*/
			
			
			    if (!empty($product->paired_category_name))
				{
					$cat_override = str_replace(' > ', ' | ', $product->paired_category_name); 
					$data .= '<CATEGORYTEXT><![CDATA['.$cat_override.']]></CATEGORYTEXT>'."\n";
				}
				else
				{ 
    			 $data .= '<CATEGORYTEXT><![CDATA['.$lcats.']]></CATEGORYTEXT>'."\n";
				}
				//$data .= '<EAN />'."\n";
    			//$data .= '<CATEGORY_PATH><![CDATA['.$kat2.']]></CATEGORY_PATH>'."\n";
				/*
				if (empty($manufacturer))
				{
				$isset = false; 
				if (!empty($attribs))
				{
				  foreach ($attribs as $k=>$v)
				   {
				     if ($v['virtuemart_custom_id']==90)
					 {
					 $data .= '<MANUFACTURER><![CDATA['.$v['custom_value'].']]></MANUFACTURER>'."\n";
					 $isset = true; 
					
					 break; 
					 
					 
					 }
				   }
				}
				if (!$isset)
				$data .= '<MANUFACTURER />'."\n";
				}
				*/
				if (!empty($manufacturer))
    			$data .= '<MANUFACTURER><![CDATA['.$manufacturer.']]></MANUFACTURER>'."\n";
				if (empty($manufacturer_id))
				$data .= '<MANUFACTURER_ID />'."\n";
				else
    			$data .= '<MANUFACTURER_ID>'.$manufacturer_id.'</MANUFACTURER_ID>'."\n";
    			$data .= '<AVAILABILITY>'.$avaitext.'</AVAILABILITY>'."\n";
				
				
				if (!empty($product->product_in_stock))
				{
					$pi = (int)$product->product_in_stock; 
					if ($pi > 0)
					{
						$avaidays = 0; 
					}
				}
				
				$data .= '<AVAILABILITY_DAYS><![CDATA['.$avaidays.']]></AVAILABILITY_DAYS>'."\n";
    			$data .= '<AVAILABILITY_OBR>'.$avai_obr.'</AVAILABILITY_OBR>'."\n";
    			//$data .= '<EAN><![CDATA['.$ean.']]></EAN>'."\n";
				$data .= '<EAN>'.htmlentities(trim($ean)).'</EAN>'."\n";
    			if (!empty($atr))
				$data .= '<ATTRIBUTES><![CDATA['.$atr.']]></ATTRIBUTES>'."\n";
    			$data .= '<DELIVERY_DATE><![CDATA['.$avaidays.']]></DELIVERY_DATE>'."\n";
				if ($published)
    			$data .= '<PUBLISHED>Y</PUBLISHED>'."\n";
				else
				$data .= '<PUBLISHED>N</PUBLISHED>'."\n";
				
				if (!$published) {
					OPCXmlExport::log('Skipping product '.$product_name.'('.$product_id.') - not published'); 
					return ''; 
				}
				
				
				if (!empty($product->customfields))
				{
				   foreach ($product->customfields as $cf)
				    {
					  
					}
				}
				
		//	echo '<output>'.var_export($id, true).'</output>'."\n";
 			$data .= '</SHOPITEM>'."\n";
			
			
			return $data; 

  }
  function endHeaders()
  {
	 $xml = '</SHOP>'."\n"; 
	 return $xml; 
  
  }
  function compress()
  {
  }
  
  
  function recurseXml(&$el, &$ref)
   {
      foreach($el->children() as $child)
	   {
	      $name = $child->getName(); 
		  if ($name !== 'CATEGORY') continue; 
	      $id = (int)$child->CATEGORY_ID; 
		  $txt = (string)$child->CATEGORY_NAME; 
		  $new =& $ref::getInstance($id, $txt); 
		  $ref->addItem($new); 
		  $this->recurseXml($child, $new); 
	   }
   }
   
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
   
   if ($name !== 'CATEGORY') continue; 
		  
  $id = (int)$child->CATEGORY_ID; 
  $txt = (string)$child->CATEGORY_NAME; 
  $new =& $return::getInstance($id, $txt); 
  $return->addItem($new); 
  $this->recurseXml($child, $new); 
 }
}


  return $return; 

	   
  }
  
  
  
}
 