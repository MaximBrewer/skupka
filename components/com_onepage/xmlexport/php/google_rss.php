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
class Google_rssXml {
 function clear()
  {
  }
  function startHeaders()
  {
	  
	 if (empty($this->params->title)) $this->params->title = 'OPC XML Google Product Feed'; 
	  
     $xml = '<?xml version="1.0" encoding="utf-8"?>'."\n"; 
	 $xml .= '<rss version="2.0" '."\n";  
	 $xml .= 'xmlns:g="http://base.google.com/ns/1.0">'; 
	 $xml .= '<channel>'; 
	 $xml .= '<title><![CDATA['.$this->params->title.']]></title>'; 
	 $xml .= '<link><![CDATA['.$this->config->xml_live_site.']]></link>'; 
	 $xml .= '<description><![CDATA['.$this->params->description.']]></description>'; 
	 
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
  
  
  function addItem($product, $vm1)
  {
	  
	  
	 $tax_info = $this->getTaxInfo($product);
	  
	  
          extract($vm1); 
		  
		  if (empty($product_name) || (empty($product->paired_category_name)))
		  {
			  return ''; 
		  }
$this->params->pidformat = (int)$this->params->pidformat; 


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

		  $default = ''; 
				
  
     			// zaciatok shopitem
			$data = '<item>'."\n";
			
			$data .= '<g:id>'.htmlspecialchars($pid).'</g:id>'."\n";
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
			
			$data .= '<g:item_group_id><![CDATA['.$parent_id .']]></g:item_group_id>'."\n";
				
			}
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
						$data .= '<g:size>'.$value.'</g:size>';
						break;
						
						case 'color':
						$data .= '<g:color>'.$value.'</g:color>';
						break;
						
						case 'pattern':
						$data .= '<g:pattern>'.$value.'</g:pattern>';
						break;
						
						case 'material':
						$data .= '<g:material>'.$value.'</g:material>';
						break;
						
						case 'gender':
						$data .= '<g:gender>'.$value.'</g:gender>';
						break;
						
						case 'age_group':
						$data .= '<g:age_group>'.$value.'</g:age_group>';
						break;
						
						case 'size_type':
						$data .= '<g:size_type>'.$value.'</g:size_type>';
						break;
						default: 
						break; 
						
						
					}
					}
					
				}
			}
			
			
			
			
			
			}	
			
			$data .= '<g:condition>new</g:condition>'."\n"; 
			
			
			if (!empty($tax_info))
			foreach ($tax_info as $tax)
			{
				//$data .= var_export($tax['country_2_code'], true); 
				//$data .= var_export($tax, true); 
				$data .= '<g:tax>
    <g:country>'.$tax['country_2_code'].'</g:country>'; 
	if (!empty($tax['state_2_code'])) { 
    $data .= '<g:region>'.$tax['state_2_code'].'</g:region>'; 
	}
	$data .= '<g:rate>'.number_format((float)$tax['calc_value'], 4, '.', '').'</g:rate>
	</g:tax>'; 
			}
			if (!empty($manufacturer))
			{
				$data .= '<g:brand><![CDATA['.$manufacturer.']]></g:brand>'; 
			}
			$u = false; 
			if (!empty($product->product_gtin))
			{
				$product->product_gtin = str_replace(' ', '', $product->product_gtin); 
				$data .= '<g:gtin><![CDATA['.$product->product_gtin.']]></g:gtin>'; 
				$u = true; 
			}
			
			if (!empty($product->product_mpn))
			{
				
				$data .= '<g:mpn><![CDATA['.$product->product_mpn.']]></g:mpn>'; 
				
				$u = true; 
			}
			if (empty($u))
			{
				$data .= '<g:gtin>FALSE</g:gtin>'; 
			}
			
			
			$data .= '<g:google_product_category><![CDATA['.$product->paired_category_name.']]></g:google_product_category>'; 
			
			$data .= '<title><![CDATA['.$product_name.']]></title>'."\n";
			
			if (!empty($longest_cats))
			$data .= '<g:product_type><![CDATA['.implode(' > ', $longest_cats).']]></g:product_type>'; 
		
			$data .= '<g:image_link><![CDATA['.$thumb_url.']]></g:image_link>'."\n";
			
			if (!empty($product->product_in_stock))
				{
					$pi = (int)$product->product_in_stock; 
					if ($pi > 0)
					{
						$avaidays = 0; 
					}
				}
				
				
			if ((empty($avaidays)))
			{
				$data .= '<g:availability>in stock</g:availability>'."\n";
			}
			else
			$data .= '<g:availability>out of stock</g:availability>'."\n";
			
			//$data .= '<FULLIMAGE><![CDATA['.$fullimg.']]></FULLIMAGE>'."\n";
			
			if ((!empty($fulldesc)) && (empty($desc)))
			{
				$desc = $fulldesc; 
				$desc = str_replace('<![CDATA[', "\n", $desc); 
				$desc = str_replace(']]>', "\n", $desc); 
				$desc = strip_tags($desc); 
			}
			
			
			$data .= '<description><![CDATA['.$desc.']]></description>'."\n";
			//$data .= '<FULLDESCRIPTION><![CDATA['.$fulldesc.']]></FULLDESCRIPTION>'."\n";
			$data .= '<link><![CDATA['.$link .']]></link>'."\n";
			
			$live_site = $this->config->xml_live_site; 
			
			// comes from extract($vm1)
			if (empty($cena_s_dph)) return; 
			
			
			
			$data .= '<g:price>'.$cena_s_dph.'</g:price>'."\n";
			/*
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
			
			if (empty($cena_s_dph)) return ''; 
			$cena_s_dph = number_format ( $cena_s_dph, 2, ',', ''); 
			*/
			
			
			/*
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
				
				if (!$published) return; 
				
				
				*/


		
		
 			$data .= '</item>'."\n";
			
			
			return $data; 

  }
  function endHeaders()
  {
     $xml = '</channel>'."\n"; 
	 $xml .= '</rss>'."\n"; 
	 return $xml; 
  
  }
  function compress()
  {
  }
  
  
 
  
   function getPairingUrl()
  {
  
    $url = (string)$this->config->catlink; 
	
	$lang = JFactory::getLanguage()->getTag(); 
	$lang2 = $this->config->language; 
	if (!empty($lang2)) $lang = $lang2; 
	
	
	
	return str_replace('{lang}', $lang, $url); 
  }
  
  function getPairingName()
  {
    $lang = JFactory::getLanguage()->getTag(); 
	$lang = str_replace('-', '_', $lang); 
	return 'googlerss_'.$lang; 
  }
  
  // either return an object or converted categories
  function processPairingData($xml, &$converted)
  {
  
  $lines = explode("\n", $xml); 
  
  
  $ret = array();  
  $useIds = false; 
  foreach ($lines as $k=>$line)
   {     
   
   
     if (substr($line, 0, 1) === '#') {
		 continue; 
	 }
	 if (empty($line)) continue; 
	  
     //get rid of the rest characters
     $lines[$k] = str_replace("\r", '', $line); 
	 if (substr($line, 0, 1)==='#') continue; 
	 $hash = $k;  
	 
	 $a = explode(' - ', $line); 
	 
	 
	 if ($useIds) {
	   $hash = (int)trim($a[0]); 
	   $line = $a[1]; 
	   	   
	   
	 }
	 else {
	 //test IDs: 
	 
	 if ((!empty($a[0])) && (is_numeric($a[0]))) {
	   $hash = (int)trim($a[0]); 
	   $useIds = true; 
	   $line = $a[1]; 
	 }
	 }
	 
	 
	 
	 $ret[$hash] = $line; 
   }
   
  
   
  
   $converted = $ret; 
   return; 


	   
  }
  
  
  
}
 