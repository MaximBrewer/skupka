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
class BeslistXml {
 function clear()
  {
  }
  function startHeaders()
  {
     $xml = '<?xml version="1.0" encoding="utf-8"?>'."\n"; 
	 $xml .= '<export>'."\n"; 
	 return $xml; 
	 
  }
  function addItem($product, $vm1)
  {
          extract($vm1); 
  
     			// zaciatok shopitem
				
				
			$data = '<product>'."\n";
			if (empty($product->product_sku)) return; 
			if (empty($this->config->xml_export_unique))
			$data .= '<UniekeCode>'.$product_id.'</UniekeCode>'."\n";
			else
			$data .= '<UniekeCode>'.$product->product_sku.'</UniekeCode>'."\n";
			
			// no category, we cannot use the product: 
			if (count($longest_cats)>0)
			{
			$deepestcat = $longest_cats[count($longest_cats)-1]; 
			}
			else 
			return; 
			
			$deepestcat = $product->paired_category_name; 
			if (stripos($deepestcat, ' > ')!==false)
			 {
			   $a = explode(' > ', $deepestcat); 
			   $deepestcat = $a[count($a)-1]; 
			 }
			
			
			$data .= '<Categorie><![CDATA['.$deepestcat.']]></Categorie>'; 
			$data .= '<Omschrijving><![CDATA['.$fulldesc.']]></Omschrijving>'."\n";
			
			if (empty($product->prices['salesPrice'])) return;
			
			if (!empty($this->config->use_be))
			{
			 if ($product->prices['salesPrice'] <= $this->config->shippingfree_nl)
			 {
			   $data .= '<Porto_nl>0</Porto_nl>'; 
			 }
			 else
			 {
			   $data .= '<Porto_nl>'.$this->config->shipping_nl.'</Porto_nl>'."\n"; 
			 }
			
			 if ($product->prices['salesPrice'] <= $this->config->shippingfree_be)
			 {
			   $data .= '<Porto_be>0</Porto_be>."\n"'; 
			 }
			 else
			 {
			   $data .= '<Porto_be>'.$this->config->shipping_be.'</Porto_be>'."\n"; 
			 }

			 
			}
			else
			{
			  if ($product->prices['salesPrice'] <= $this->config->shippingfree_nl)
			 {
			   $data .= '<Porto>0</Porto>'."\n"; 
			 }
			 else
			 {
			   $data .= '<Porto>'.$this->config->shipping_nl.'</Porto>'."\n"; 
			 }
			}
			if ($product->prices['basePriceWithTax'] > $product->prices['salesPrice'])
			 {
			   
			   $data .= '<van-Prijs>'.$product->prices['basePriceWithTax'].'</van-Prijs>'."\n"; 
			 }
			
			
			
			if (!empty($this->config->use_be))
			{
			 $data .= '<Levertijd_be><![CDATA['.$this->config->avaitext_be.']]></Levertijd_be>'."\n";
			 $data .= '<Levertijd_nl><![CDATA['.$avaitext.']]></Levertijd_nl>'."\n";
			}
			else
			{
			  $data .= '<Levertijd><![CDATA['.$avaitext.']]></Levertijd>'."\n";
			}
			
			$data .= '<Producttitel><![CDATA['.$product_name.']]></Producttitel>'."\n";
			
			if (empty($cena_s_dph)) return; 
			
			$data .= '<Prijs>'.$cena_s_dph.'</Prijs>'."\n";
			
			if (!empty($thumb_url))
			$data .= '<URL-Productafbeelding><![CDATA['.$thumb_url.']]></URL-Productafbeelding>'."\n";
			else
			$data .= '<URL-Productafbeelding />'."\n";
			
			
			$data .= '<URL><![CDATA['.$link .']]></URL>'."\n";
			
			if (empty($manufacturer))
			$data .= '<Merk />'."\n";
			else
			$data .= '<Merk><![CDATA['.$manufacturer.']]></Merk>'."\n";
			
			//$data .= '<SKU><![CDATA['.$sku.']]></SKU>'."\n";
			//$data .= '<ITEM_ID><![CDATA['.$sku.']]></ITEM_ID>'."\n";
			
			//$data .= '<PRODUCT><![CDATA['.$product_name.']]></PRODUCT>'."\n";
			//$data .= '<SUBTITLE><![CDATA['.$product_name.']]></SUBTITLE>'."\n";
			
			//$data .= '<FULLIMAGE><![CDATA['.$fullimg.']]></FULLIMAGE>'."\n";
			//$data .= '<DESCRIPTION><![CDATA['.$desc.']]></DESCRIPTION>'."\n";
			
			//$data .= '<URL><![CDATA['.$link .']]></URL>'."\n";
			/*
			if (false)
			if (!empty($images))
			{
			$data .= '<IMAGES>'."\n";
			foreach ($images as $im)
			{
			
			$data .= "\t".'<IMAGE>'."\n"; 
			$data .= "\t"."\t".'<PATH><![CDATA['.'http://www.municak.sk'.$im['file_name'].']]></PATH>'."\n";
			$data .= "\t"."\t".'<TITLE><![CDATA['.$im['file_title'].']]></TITLE>'."\n";
			$data .= "\t".'</IMAGE>'."\n"; 
			}
			$data .= '</IMAGES>'."\n";
			}
			else $data .= '<IMAGES />'."\n"; 
			
			
			$data .= '<NONSEOLINK><![CDATA['.$node_link.']]></NONSEOLINK>'."\n";
			
			if (empty($cena_s_dph)) return ''; 
			$cena_s_dph = number_format ( $cena_s_dph, 2, ',', ''); 
			
			
			$data .= '<PRICE><![CDATA['.$cena_txt.']]></PRICE>'."\n";
			$tax_rate = number_format ( $tax_rate, 2, ',', ''); 
			*/
			
			//$data .= '<VAT>'.$tax_rate.'</VAT>'."\n"; 
			//$data .= '<ITEM_TYPE>NEW</ITEM_TYPE>'."\n";
			/*
			if (!empty($longest_cats))
			{
			//<CATEGORYTEXT>Obuv | Pánska | Bežecká | Nike</CATEGORYTEXT>
			 $lcats = implode(' | ', $longest_cats); 
			}
			else return; 
			
			
			
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
			
			/*
			
    			$data .= '<CATEGORYTEXT><![CDATA['.$lcats.']]></CATEGORYTEXT>'."\n";
				//$data .= '<EAN />'."\n";
    			//$data .= '<CATEGORY_PATH><![CDATA['.$kat2.']]></CATEGORY_PATH>'."\n";
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
				else
    			$data .= '<MANUFACTURER><![CDATA['.$manufacturer.']]></MANUFACTURER>'."\n";
				if (empty($manufacturer_id))
				$data .= '<MANUFACTURER_ID />'."\n";
				else
    			$data .= '<MANUFACTURER_ID>'.$manufacturer_id.'</MANUFACTURER_ID>'."\n";
    			
				$data .= '<AVAILABILITY_DAYS><![CDATA['.$avaidays.']]></AVAILABILITY_DAYS>'."\n";
    			$data .= '<AVAILABILITY_OBR>'.$avai_obr.'</AVAILABILITY_OBR>'."\n";
    			//$data .= '<EAN><![CDATA['.$ean.']]></EAN>'."\n";
    			$data .= '<ATTRIBUTES><![CDATA['.$atr.']]></ATTRIBUTES>'."\n";
    			$data .= '<DELIVERY_DATE><![CDATA['.$avaidays.']]></DELIVERY_DATE>'."\n";
				if ($published)
    			$data .= '<PUBLISHED>Y</PUBLISHED>'."\n";
				else
				$data .= '<PUBLISHED>N</PUBLISHED>'."\n";
				
				if (!empty($product->customfields))
				{
				   foreach ($product->customfields as $cf)
				    {
					  
					}
				}
				
				*/
				
		//	echo '<output>'.var_export($id, true).'</output>'."\n";
 			$data .= '</product>'."\n";
			return $data; 

  }
  function endHeaders()
  {
	 $xml = '</export>'."\n"; 
	 return $xml; 
  
  }
  function compress()
  {
  }
  
  function getPairingName()
  {
     return (string)$this->xml->category_pairing_name;
  }
  
  function getPairingUrl()
  {
	  
    $ret = (string)$this->xml->category_pairing_url; 
	return $ret; 
  }
  
  
  function recurseXml(&$el, &$ref)
   {
      foreach($el->children() as $child)
	   {
	      $id = (int)$child->attributes()->id; //."<br />"; 
		  $txt = (string)$child->attributes()->name; //."<br />"; 
		  //$new = new xmlCategory($id, $txt); 
		  $new =& $ref::getInstance($id, $txt); 
		  $ref->addItem($new); 
		  //if ($child->hasChildren())
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
	   
	   

foreach($r->categories->children() as $child) {
 {
  $id = (int)$child->attributes()->id; //."<br />"; 
  
  $txt = (string)$child->attributes()->name; //."<br />"; 
  //$new = new xmlCategory($id, $txt); 
  $new =& $return::getInstance($id, $txt); 
  $return->addItem($new); 
  
  //if ($child->hasChildren())
  $this->recurseXml($child, $new); 
 }
}
  return $return; 

	   
  }
  
}
 