<?php 
defined( '_JEXEC' ) or die( 'Restricted access' ); 
class dropshipHelper { 
  public static $tidd; 
  public static $dir; 
  public static function checkSkus($skus) {
 
  }
  
  public static function calculateStatuses(&$orders, $_orderStatusList)  {
	 
	  $suggested = array(); 
	  $virtuemart_custom_id = 0; 
	  $value = self::$tidd['config']->avai_to_status; 
	   if (is_array($value)) {
			if (isset($value['select']))
			{
				
				$virtuemart_custom_id = (int)$value['select'];
			}
			}
			else {
				if (isset($value->select))
				{
				
				$virtuemart_custom_id = (int)$value->select;
				}
			}
			
		if (empty($virtuemart_custom_id)) return; 	
	  
	  
	  
	  $db = JFactory::getDBO(); 
	  /*
	  $q = 'select custom_title from #__virtuemart_customs where virtuemart_custom_id = '.(int)$virtuemart_custom_id.' limit 1'; 
	  $db->setQuery($q); 
	  $custom_title = $db->loadResult(); 
	  if (empty($custom_title)) return; 
	  */
	  $custom_title = self::getAvaiTitle(); 
	  
	  foreach ($orders as $k1=>$order) {
		  
		  $max = 0; 
		  $selected = new stdClass(); 
		  
	  foreach ($order['items'] as $k2=>$item) {
		  $id = $item->virtuemart_order_item_id; 
		  $suggested[$id] = $item->order_status;
		  
		  $product_id = $item->virtuemart_product_id; 
		  
		  
		  
		  
		  
		  /*
		  $q = 'select customfield_value from #__virtuemart_product_customfields where virtuemart_product_id = '.(int)$product_id.' and virtuemart_custom_id = '.(int)$virtuemart_custom_id.' limit 0,1';
		  $db->setQuery($q); 
		  $custom_value = $db->loadResult(); 
		  */
		  $custom_value = self::getAvaiValue($product_id); 
		  if (empty( $custom_value)) $custom_value = ''; 
		  $orders[$k1]['items'][$k2]->calculated = new stdClass(); 
		  $orders[$k1]['items'][$k2]->calculated->name = $custom_title; 
		  $orders[$k1]['items'][$k2]->calculated->value = $custom_value; 
		  
		  
				$ind = md5($custom_value); 
				if (is_array($value)) {
				if (isset($value[$ind])) $val = $value[$ind]; 
				}
				else 
				if (is_object($value)) {
						if (isset($value->$ind)) $val = $value->$ind; 
					}
					
					
					
					foreach ($_orderStatusList as $key=>$o) {
						$key = (string)$key; 
					    $val = (string)$val; 
						
						if ($val === $key) {
					    
						$orders[$k1]['items'][$k2]->calculated->order_status = $key; 						
						$orders[$k1]['items'][$k2]->calculated->order_status_name = $o; 						
						$orders[$k1]['items'][$k2]->calculated->order_status_name_translated = JText::_($o); 						
						$suggested[$id] = $key; 
						}
					}
					
					
					$ind = $ind.'_days';
						if (is_array($value)) {
				if (isset($value[$ind])) $val = $value[$ind]; 
				}
				else 
				if (is_object($value)) {
						if (isset($value->$ind)) $val = $value->$ind; 
					}
				
				$orders[$k1]['items'][$k2]->calculated->days = $val; 			
				$vv = (float)$val; 
				if ($vv >= $max) {
					$max = $vv; 
					$selected = $orders[$k1]['items'][$k2]->calculated;
				}
				
			
		  
	  }
	  
	  foreach ($order['items'] as $kk=>$iitem) {
		  $orders[$k1]['items'][$kk]->calculated = $selected; 
		  $id = (int)$iitem->virtuemart_order_item_id; 
		  $key = $selected->order_status; 
		  $orders[$k1]['details']['BT']->calculated = $selected; 
		  $suggested[$id] = $key; 
	  }
	  
	  
	  }
	  return $suggested; 
  }
  public static function changeOrderStatuses($orders) {
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'opc.php'); 
	  $_orderStatusList = dropshipHelper::getOrderStatuses(); 
	  self::calculateStatuses($orders, $_orderStatusList); 
	  $orderModel = OPCmini::getModel('Orders'); 
	  foreach ($orders as $order) {
		    VirtueMartControllerOpc::emptyCache(); 
			
			ob_start(); 
			if (!empty($order['details']['BT']->calculated->order_status))
			if ($order['details']['BT']->order_status != $order['details']['BT']->calculated->order_status)
			{ 
				$neworder = array(); 
				$neworder['virtuemart_order_id'] = $order['details']['BT']->virtuemart_order_id;
				$neworder['order_status'] = $order['details']['BT']->calculated->order_status;
				
				$neworder['doVendor'] = true; 
				$neworder['url'] = 'url';
				$neworder['customer_notified'] = 1;
				$neworder['comments'] = '';
				$orderID = $order['details']['BT']->virtuemart_order_id; 
				
				$orderModel->updateStatusForOneOrder($orderID, $neworder, true);
                
				
			}
			$output = ob_get_clean(); 
			
			VirtueMartControllerOpc::emptyCache(); 
	  }
  }
  
  
  public static function getOrderStatuses() {
	  $orderStatusModel=VmModel::getModel('orderstatus');
			$orderStates = $orderStatusModel->getOrderStatusList();
			$_orderStatusList = array();
			foreach ($orderStates as $orderState) {
				//$_orderStatusList[$orderState->virtuemart_orderstate_id] = $orderState->order_status_name;
				//When I use update, I have to use this?
				$_orderStatusList[$orderState->order_status_code] = JText::_($orderState->order_status_name);
			}
			return $_orderStatusList;
  }
  public static function getAvaiTitle() {
$db = JFactory::getDBO(); 
	  $virtuemart_custom_id = 0; 
	  $value = self::$tidd['config']->avai_to_status; 
	   if (is_array($value)) {
			if (isset($value['select']))
			{
				
				$virtuemart_custom_id = (int)$value['select'];
			}
			}
			else {
				if (isset($value->select))
				{
				
				$virtuemart_custom_id = (int)$value->select;
				}
			}
			
		if (empty($virtuemart_custom_id)) return ''; 	 
	 $q = 'select custom_title from #__virtuemart_customs where virtuemart_custom_id = '.(int)$virtuemart_custom_id.' limit 1'; 
	  $db->setQuery($q); 
	  $custom_title = $db->loadResult(); 
	  if (empty($custom_title)) return ''; 
	  return $custom_title;
  }
  public static function getAvaiValue($product_id) {
	  $product_id = (int)$product_id; 
	  $db = JFactory::getDBO(); 
	  $virtuemart_custom_id = 0; 
	  $value = self::$tidd['config']->avai_to_status; 
	   if (is_array($value)) {
			if (isset($value['select']))
			{
				
				$virtuemart_custom_id = (int)$value['select'];
			}
			}
			else {
				if (isset($value->select))
				{
				
				$virtuemart_custom_id = (int)$value->select;
				}
			}
			
		if (empty($virtuemart_custom_id)) return ''; 	
	  
	   $q = 'select customfield_value from #__virtuemart_product_customfields where virtuemart_product_id = '.(int)$product_id.' and virtuemart_custom_id = '.(int)$virtuemart_custom_id.' limit 0,1';
		  $db->setQuery($q); 
		  $custom_value = $db->loadResult(); 
		  if (empty($custom_value)) $custom_value = ''; 
		  return $custom_value; 
  }
  
  public static function getMF($product_id) {
	  
	  
	  
	  static $cache; 
	  if (!empty($cache[$product_id])) return $cache[$product_id]; 
	  
	  
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		OPCmini::setVMLANG(); 
	  
	  $db = JFactory::getDBO(); 
	  $q = 'select * from #__virtuemart_manufacturers as m, `#__virtuemart_manufacturers_'.VMLANG.'` as l, #__virtuemart_product_manufacturers as p where p.virtuemart_product_id = '.(int)$product_id.' and p.virtuemart_manufacturer_id = m.virtuemart_manufacturer_id and m.virtuemart_manufacturer_id = l.virtuemart_manufacturer_id limit 0,1'; 
	  $db->setQuery($q); 
	  $mfdata = $db->loadAssoc(); 
	  if (empty($mfdata)) $mfdata = array(); 
	  $cache[$product_id] = (array)$mfdata; 
	  return $cache[$product_id];
	  
  }
  
   public static function assignManufacturers() {
	   $mfs = JRequest::getVar('manufacturer', array()); 
	   $db = JFactory::getDBO(); 
	   foreach ($mfs as $product_id => $manufacturer_id) {
		   $q = 'select virtuemart_manufacturer_id from #__virtuemart_product_manufacturers where virtuemart_product_id = '.(int)$product_id; 
		   $db->setQuery($q); 
		   $mfid = $db->loadResult();
		   if (!empty($mfid)) {
			   JFactory::getApplication()->enqueueMessage('Manufacturer was already assigned, skipping product_id '.$product_id); 
			   continue; 
		   }
		   $q = 'insert into #__virtuemart_product_manufacturers (id, virtuemart_product_id, virtuemart_manufacturer_id) values (NULL, '.(int)$product_id.', '.(int)$manufacturer_id.')';
		   $db->setQuery($q); 
		   $db->query(); 
		   
	   }
	   
   }
   
   public static function getAdminFields() {
	   $db = JFactory::getDBO(); 
	   $q = 'select custom_title, virtuemart_custom_id from #__virtuemart_customs where layout_pos = "XLS"'; 
	   $db->setQuery($q); 
	   $res = $db->loadAssocList(); 
	   
	   $datas = array(); 
	   foreach ($res as $row) {
		   $title = $row['custom_title']; 
		   $id = (int)$row['virtuemart_custom_id']; 
		   $datas[$id] = $title; 
	   }
	   return $datas; 
   }
   
   public static function getProductAdminCustomFields($product_id, $adminList=array(), &$row=array()) {
	   $inA = array();
	   $db = JFactory::getDBO(); 
	   foreach ($adminList as $id => $name) {
		   $inA[$id] = (int)$id; 
	   }
	   
	   if (empty($inA)) return; 
	   
	   $q = 'select virtuemart_custom_id, customfield_value from #__virtuemart_product_customfields where virtuemart_product_id = '.(int)$product_id.' and virtuemart_custom_id IN ('.implode(',', $inA).') limit '.count($inA); 
	   $db->setQuery($q); 
	   $res = $db->loadAssocList(); 
	   
	   $ret = array(); 
	   
	   
	    foreach ($res as $rowX) {
			$rowX['virtuemart_custom_id'] = (int)$rowX['virtuemart_custom_id']; 
			$name = $adminList[$rowX['virtuemart_custom_id']]; 
		
			$ret[$name] = $rowX['customfield_value']; 
			$row[$name] = $rowX['customfield_value']; 
		}
		
		foreach ($adminList as $id => $name) {
			if (!isset($ret[$name])) $ret[$name] = ''; 
			if (!isset($row[$name])) $row[$name] = ''; 
		}
		
		return $ret; 
		
	   
	   
	   
   }
   
   public static function createXLS($groups, &$mfs, $ehelper, &$fout, &$fhash) {
	   
	   require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'order_excell.php'); 
	   $JModelOrder_excell = new JModelOrder_excell(); 
	   
	   $now = time(); 
		
	   $lines = array(); 
	   $grouped = array(); 
	   $n = 0; 
	   
	   
	   $adminList = self::getAdminFields(); 
	   
	   foreach ($groups as $mf_id => $group) {
		   $mf_id = (int)$mf_id; 
		   $n++; 
		   $sheets = array();    
		   $fileout = ''; 
		   $lines = array(); 
		   $grouped = array(); 
		   foreach ($group as $item) {
		   $row = array(); 
		   $row['quantity'] = $item->product_quantity; 
		   
		   //$row['ean'] = $item->product_gtin; 
		   $row['mpn'] = $item->product_mpn; 
		   $item->product_attribute = JModelOrder_excell::adjustProductAttribute($item->product_attribute, true); 
		   $attribute = $item->product_attribute; 
		   $row['product_attribute'] = $attribute; 
		   //$row['sku'] = $item->product_sku; 
		   $row['name'] = $item->product_name; 
		   $row['product_sku'] = $item->order_item_sku; 
		   //$row['price'] = $item->product_priceWithoutTax; 
		   /*
		   $root = Juri::root(); 
		   if (substr($root, -1, 1) !== '/') $root .= '/'; 
		   $row['url']  = $root.'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.(int)$item->virtuemart_product_id; 
		   */
		   //$row['price']  = $item->product_url;
		   
		   $row['sup_name'] = $mfs[$mf_id]['mf_name']; 
		   $row['order_number'] = $item->order['details']['BT']->order_number; 
		   $row['virtuemart_order_item_id'] = $item->virtuemart_order_item_id; 
		   
		   self::getProductAdminCustomFields($item->virtuemart_product_id, $adminList, $row); 
		   
		   $timestamp = date("Y/m/d H:i:s", $now);
		   $custom_subject = $mfs[$mf_id]['mf_name'].' - '.$timestamp;
		   $mfs[$mf_id]['mf_subject'] = $custom_subject; 
		   
		   $fileout .= $item->virtuemart_order_item_id.'_'; 
		   $lines[$item->virtuemart_order_item_id] = $row; 
		   
		   $atr_str = $attribute; 
		   if (!is_string($atr_str)) {
			   $atr_str = json_encode($atr_str); 
		   }
		   
		   if (!isset($grouped[$item->virtuemart_product_id.'_'.$atr_str])) {
			 unset($row['order_number']); 
		     $grouped[$item->virtuemart_product_id.'_'.$atr_str] = $row; 
		   }
		   else {
			   $grouped[$item->virtuemart_product_id.'_'.$atr_str]['quantity'] += $row['quantity'];
		   }
		   
		   }
		   
		   
		   $JModelOrder_excell->jsonToCols($lines, 'product_attribute', 'virtuemart_order_item_id', ''); 
		   
		   foreach ($lines as $ind=>$data) {
			   foreach ($data as $col_name=>$val) {
			   if (stripos($col_name, 'product_attribute')===0) {
				unset($lines[$ind][$col_name]); 
			   }
			   if (stripos($col_name, 'atr_')===0) {
				unset($lines[$ind][$col_name]); 
			   }
			   if (stripos($col_name, 'virtuemart_order_item_id')===0) {
				unset($lines[$ind][$col_name]); 
			   }
			   
		     }
		   }
		   
		   $JModelOrder_excell->jsonToCols($grouped, 'product_attribute', 'virtuemart_order_item_id', ''); 
		   
		  
		   
		      foreach ($grouped as $ind=>$data) {
			   foreach ($data as $col_name=>$val) {
			   if (stripos($col_name, 'product_attribute')===0) {
				unset($grouped[$ind][$col_name]); 
			   }
			   if (stripos($col_name, 'atr_')===0) {
				unset($grouped[$ind][$col_name]); 
			   }
			   
			   if (stripos($col_name, 'virtuemart_order_item_id')===0) {
				unset($grouped[$ind][$col_name]); 
			   }
		     }
		   }
		   
		   
		   
		   $fnx = $mfs[$mf_id]['mf_subject'].'.xls';
		   $fnx = str_replace(' ', '_', $fnx); 
		   $fnx = str_replace('/', '_', $fnx); 
		   $fnx = str_replace('\\', '_', $fnx); 
		   $fnx = str_replace('"', '_', $fnx); 
		   $fnx = str_replace("'", '_', $fnx); 
		   $fnx = str_replace("_-_", '_', $fnx); 
		   $fnx = str_replace("__", '_', $fnx); 
		   $fnx = JFile::makeSafe($fnx); 
		   $fout[$mf_id] = $fnx; //md5($fileout).'.xls'; 
		   $fhash[$mf_id] = md5($fout[$mf_id]); 
		   
		   $fileout = self::$dir.DIRECTORY_SEPARATOR.$fout[$mf_id];
		   
		   $ehelper->createXLS(array('Items by SKU'=>$grouped, 'Internal use' => $lines ), $fileout); 
		   
		   $ehelper->setStatus(self::$tidd['tid'], $fhash[$mf_id], 'CREATED', urlencode($fileout));
		   
		   
		   
	   }
	   return $fout; 
   }
   
   public static function sendEmails(&$mfs, $fhash, $ehelper) {
	   $now = time(); 
	   $vdata = array(); 
	   
			$config = JFactory::getConfig();	
			if (method_exists($config, 'getValue'))
			$vname = $config->getValue( 'config.fromname' ); 
			else
		    $vname = $config->get( 'fromname' ); 
		
		$config = JFactory::getConfig();	
			if (method_exists($config, 'getValue'))
				$vemail = $config->getValue( 'config.mailfrom' ); 
			else
				$vemail = $config->get( 'mailfrom' ); 
		
		$vdata['vname'] = $vname; 
		$vdata['vemail'] = $vemail; 
	   
	   ob_start(); 
	   $n = 1; 
	   foreach ($fhash as $current_mf_id => $hash) {
	   
	   $emails = array(); 
	   $mf_sendmail = JRequest::getVar('mf_sendmail', array()); 
	   
	   
	   foreach ($mf_sendmail as $mf_id) {
		 if ($mf_id === 'joomla') {
			$emails[] = JFactory::getConfig()->get('mailfrom');
			continue; 
		}
		$mf_id = (int)$mf_id; 
		
		if ($mf_id !== $current_mf_id) continue; 
		
		if (isset($mfs[$mf_id])) {
			$emails[] = $mfs[$mf_id]['mf_email']; 
		}
	  }
	  
	  if (!empty($emails)) {
	    echo $n.': '; 
		$vdata = array(); 
		
		$config = JFactory::getConfig();	
			if (method_exists($config, 'getValue'))
				$vemail = $config->getValue( 'config.mailfrom' ); 
			else
				$vemail = $config->get( 'mailfrom' ); 
		
		$vdata['vemail'] = $vemail;
		
		if (method_exists($config, 'getValue'))
				$vname = $config->getValue( 'config.fromname' ); 
			else
				$vname = $config->get( 'fromname' ); 
		
		$vdata['vname'] = $vname;
		$timestamp = date("Y/m/d H:i:s", $now);
		
		//$custom_subject = $mfs[$current_mf_id]['mf_name'].' - '.$timestamp;
		$custom_subject = $mfs[$current_mf_id]['mf_subject'];
		
		if (!empty(self::$tidd['config']->sendcc)) {
		 $custom_cc = array(self::$tidd['config']->sendcc); 
		}
		else 
		$custom_cc = ''; 
	
		$ehelper->sendMail(self::$tidd['tid'], $hash, false, $emails, $vdata, $custom_subject, $custom_cc); 
		
	  }
	  else {
		  echo 'No emails associated with hash '.$hash.'<br />'; 
	  }
	  $n++; 
	   }
	   
	   $msg = ob_get_clean(); 
	   return $msg; 
	  
   }
   
   public static function getMFS() {
	   
	   
	   
	  static $cache; 
	  if (!empty($cache)) return $cache; 
	  
	  
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		OPCmini::setVMLANG(); 
	  
	  $db = JFactory::getDBO(); 
	  $q = 'select * from #__virtuemart_manufacturers as m, `#__virtuemart_manufacturers_'.VMLANG.'` as l where m.virtuemart_manufacturer_id = l.virtuemart_manufacturer_id'; 
	  $db->setQuery($q); 
	  $mfs = $db->loadAssocList(); 
	  
	  
	 
	  if (empty($mfs)) $mfs = array(); 
	  
	  $new = array(); 
	  foreach ($mfs as $mf) {
		  $id = (int)$mf['virtuemart_manufacturer_id']; 
		  $new[$id] = (array)$mf; 
	  }
	  $cache = $new; 
	  return $cache;
	  
  }
  
  
  
  public static function render($template, $data=array(), $ehelper=null) {
		if (!empty($data)) {
			extract($data); 
		}
	  $tidd = self::$tidd; 
	  $dir = self::$dir; 
	  $html = ''; 
	  if (file_exists(__DIR__.DIRECTORY_SEPARATOR.$template.'.tmpl.php')) {
	  ob_start(); 
	  include(__DIR__.DIRECTORY_SEPARATOR.$template.'.tmpl.php'); 
	  $html = ob_get_clean(); 
	  }
	  return $html; 
  }
  
  public static function generateAndSendXML($orders, $skus, $ehelper, &$msgs) {
	  $order_ids = array();
	  $tidd = self::$tidd; 
	  $dir = self::$dir; 
	  
	  foreach ($orders as $k=>$order) {
		  $order_ids[(int)$order['details']['BT']->virtuemart_order_id] = (int)$order['details']['BT']->virtuemart_order_id;
	  }
ob_start(); 
include(__DIR__.DIRECTORY_SEPARATOR.'order.xml.php'); 
$xml = ob_get_clean(); 



$fn = implode('_', $order_ids); 
$time = time(); 
$xf = $dir.DIRECTORY_SEPARATOR.$fn.'_'.$time.'.xml'; 
file_put_contents($xf, $xml); 



$tx = mrpHelper::send($xml, self::$tidd); 
$xf2 = $dir.DIRECTORY_SEPARATOR.$fn.'_reply_'.$time.'.xml';
file_put_contents($xf2, $tx); 

$xml = simplexml_load_string($tx); 
$err = $xml->xpath('//mrpEnvelope/body/mrpResponse/status/error/errorMessage');
if (!empty($err)) {
$msgs[] = (string)$err[0]; 

}

$data['special_value_ai_0'] = ''; 

$f = $xml->xpath('//mrpEnvelope/body/mrpResponse/data/datasets/objednavka/rows/row/fields');
$msg = $error; 
foreach ($f as $row) {
	$puvodniCislo = (string)$row->puvodniCislo; 
	$puvodniCislo = (int)$puvodniCislo; 
	$cislo = (string)$row->cislo; 

	foreach ($order_ids as $order_id) {
		$order_id = (int)$order_id; 
		if ($puvodniCislo !== $order_id) continue; 
		$msgs[] = 'Objednávka '.$order_id.' importovaná ako '.$cislo; 
		$ehelper->setCustomSpecial($tid, $order_id, $cislo, 'CREATED', $cislo); 
		
		$data['special_value_ai_0'] .= $cislo.'_'; 
	}
	
}


return $msgs; 
  }
  

  public static function getStock($mrp_plu) {
	    $time = time(); 
		
		
		
	    $mrp_plu = str_replace(',', '.', $mrp_plu); 
		
		ob_start(); 
		include(__DIR__.DIRECTORY_SEPARATOR.'stock.xml.php'); 
		$xmlsend = ob_get_clean(); 
		
		if (!empty($dir)) {
		
		 $xf = $dir.DIRECTORY_SEPARATOR.'send_'.$mrp_plu.'_'.$time.'.xml'; 
		 file_put_contents($xf, $xmlsend); 
		}
		
		$xml = self::doCurl($xmlsend); 
		$xml = '<?xml version="1.0" encoding="UTF-8" ?>'.$xml; 
		$simpleXml = simplexml_load_string($xml); 
		if ($simpleXml === false) {
			echo 'error: '; var_dump($xml); die(); 
		}
		$dom = dom_import_simplexml($simpleXml)->ownerDocument;
	    $dom->formatOutput = true;
		$fxml = $dom->saveXML();
		
		if (!empty($dir)) {
		
		 $xf = $dir.DIRECTORY_SEPARATOR.$mrp_plu.'_'.$time.'.xml'; 
		 file_put_contents($xf, $fxml); 
		}
		
		return $xml; 
	  
  }

  public static  function send($xml) {
	  
	  return self::doCurl($xml); 
	  


	  
  }
  
  public static  function doCurl($xml) {
	  
$mrpurl = self::$tidd['config']->url;
if (empty($mrpurl)) return 'no MRP URL configured'; 
$ch = curl_init();



curl_setopt($ch, CURLOPT_URL,$mrpurl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

$headers = [
    'X-Apple-Tz: 0',
    'X-Apple-Store-Front: 143444,12',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Encoding: gzip, deflate',
    'Accept-Language: en-US,en;q=0.5',
    'Cache-Control: no-cache',
    'Content-Type: application/xhtml+xml; charset=utf-8',
    'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0',
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,30);
curl_setopt($ch,CURLOPT_TIMEOUT, 60);
// in real life you should use something like:
// curl_setopt($ch, CURLOPT_POSTFIELDS, 
//          http_build_query(array('postvar1' => 'value1')));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
// receive server response ...

//echo $httpcode; 

$server_output = curl_exec ($ch);
$httpcode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpcode !== 200) {
	
	die('error sending data, returned http code: '.$httpcode.' to URL '.$mrpurl); 
}

curl_close ($ch);

return $server_output; 
  }
  
}