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
class ZelaniaXml {
 function clear()
  {
  }
  function startHeaders()
  {
     $xml = '<?xml version="1.0" encoding="utf-8"?>'."\n"; 
	 $xml .= '<SHOP>'."\n"; 
	 return $xml; 
	 
  }
  function addItem($product, $vm1)
  {
	  
          extract($vm1); 
  
     			// zaciatok shopitem
			$data = '<SHOPITEM>'."\n";
			$data .= '<ID>'.$product_id.'</ID>'."\n";
			$data .= '<SKU><![CDATA['.$sku.']]></SKU>'."\n";
			$data .= '<ITEM_ID><![CDATA['.$sku.']]></ITEM_ID>'."\n";
			$data .= '<PRODUCTNAME><![CDATA['.$product_name.']]></PRODUCTNAME>'."\n";
			$data .= '<PRODUCT><![CDATA['.$product_name.']]></PRODUCT>'."\n";
			$data .= '<SUBTITLE><![CDATA['.$product_name.']]></SUBTITLE>'."\n";
			$data .= '<IMGURL><![CDATA['.$thumb_url.']]></IMGURL>'."\n";
			$data .= '<FULLIMAGE><![CDATA['.$fullimg.']]></FULLIMAGE>'."\n";
			$data .= '<DESCRIPTION><![CDATA['.$desc.']]></DESCRIPTION>'."\n";
			
			
			$fulldesc = str_replace('[CDATA[', '---', $fulldesc); 
			$fulldesc = str_replace(']]>', '--->', $fulldesc); 
			
			$data .= '<FULLDESCRIPTION><![CDATA['.$fulldesc.']]></FULLDESCRIPTION>'."\n";
			$data .= '<URL><![CDATA['.$link .']]></URL>'."\n";
			
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
			
			$data .= '<PRICE_VAT>'.$cena_s_dph.'</PRICE_VAT>'."\n";
			$data .= '<PRICE><![CDATA['.$cena_txt.']]></PRICE>'."\n";
			$tax_rate = number_format ( $tax_rate, 2, ',', ''); 
			$data .= '<VAT>'.$tax_rate.'</VAT>'."\n"; 
			$data .= '<ITEM_TYPE>NEW</ITEM_TYPE>'."\n";
			
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
			
			if (!empty($product->virtuemart_manufacturer_id))
			{
				$manufacturer_id = $product->virtuemart_manufacturer_id; 
				if (is_array($product->virtuemart_manufacturer_id))
				{
					$manufacturer_id = reset($product->virtuemart_manufacturer_id); 
				}
				$manufacturer_id = (int)$manufacturer_id; 
			}
			
			
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
				{
    			$data .= '<MANUFACTURER_ID>'.$manufacturer_id.'</MANUFACTURER_ID>'."\n";
				}
    			$data .= '<AVAILABILITY>'.$avaitext.'</AVAILABILITY>'."\n";
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
  
  
}
 