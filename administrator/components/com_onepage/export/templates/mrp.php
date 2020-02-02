<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
$msgs = array(); 
OnepageTemplateHelper::$do_no_store_output = true; 
require_once(__DIR__.DIRECTORY_SEPARATOR.'mrp'.DIRECTORY_SEPARATOR.'helper.php'); 
$dir = $this->prepareDirectory($tid); 
/*basic config*/
mrpHelper::$tidd = $tidd; 
mrpHelper::$dir = $dir; 


$orders = array(); 
$orderModel = VmModel::getModel('Orders'); 
$skus = array(); 

$allowconfirm = (bool)$tidd['config']->allowconfirm; 
$isaction = JRequest::getInt('doaction', -1); 
if ($isaction === 1) $allowconfirm = false; 



//csv_pairing
if (!empty($tidd['config']->csvfile)) {
  $parovaci_subor = JPATH_SITE.DIRECTORY_SEPARATOR.$tidd['config']->csvfile; 
}
else {
 $parovaci_subor = JPATH_SITE.DIRECTORY_SEPARATOR.'import'.DIRECTORY_SEPARATOR.'product-sku-mrp.csv';
}
if (file_exists($parovaci_subor)) {
$par_data = array(); 
if (file_exists($parovaci_subor)) {
	$row = 0;
	
	$sep = ';'; 

if (!empty($tidd['config']->csvfilesep)) $sep = $tidd['config']->csvfilesep;
	
if (($handle = fopen($parovaci_subor, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        
		
        
        
		$row++;
		if ($row === 1) continue; 
		
		$vm_sku = trim($data[0]); 
		$vm_attr = trim($data[1]); 
		$mrp_sku = trim($data[2]); 
        if (!isset($par_data[$vm_sku])) $par_data[$vm_sku] = array(); 
		$par_data[$vm_sku][$vm_attr] = $mrp_sku; 
		
    }
    fclose($handle);
}
}
}
else {
	JFactory::getApplication()->enqueueMessage('Párovací súbor PLU neexistuje, budú použité virtuemart PLU pre párovanie'); 
}

$toskip = array(); 
if (!is_array($localid)) $localid = array($localid); 
asort($localid); 
if (is_array($localid)) {
	$order_ids = $localid; 
foreach ($localid as $order_id) {
	
	$arr = $this->getSpecials($tid, $order_id);
	//skip order only if isaction==1
	if ((empty($allowconfirm)) && ($isaction !== 1)) {
	if ((!empty($arr)) && (!empty($arr[1]))) {
		$this->setStatus($tid, $order_id, 'CREATED'); 
		JFactory::getApplication()->enqueueMessage('Preskakujem objednávku - Objednávka '.$order_id.' už bola importovaná ako '.$arr[0]); 
		continue;
	}
	}
	else {
		if ((!empty($arr)) && (!empty($arr[1]))) {
		$toskip[$order_id] = $arr[1]; 
	}
	}
    $order= $orderModel->getOrder($order_id);
	
	foreach ($order['items'] as $k=>$i) {
	if (!isset($i->product_sku)) {
		$i->product_sku = $i->order_item_sku; 
	    $order['items'][$k]->product_sku = $i->order_item_sku; 
	}
	
   if (isset($par_data[$i->product_sku])) {
	   $atr = $i->product_attribute; 
	   $js = json_decode($atr, true); 
	   $db = JFactory::getDBO(); 
	   foreach ($js as $custom_field_id => $val) {
		   
		    if (!defined('VM_VERSION') || (VM_VERSION < 3))
			 {
			  $custom_field_col = 'custom_value'; 
			 }
			 else
			 {
				 $custom_field_col = 'customfield_value'; 
			 }
		   
		   $q = 'select `'.$custom_field_col.'` from #__virtuemart_product_customfields where virtuemart_customfield_id = '.(int)$custom_field_id; 
		   $db->setQuery($q); 
		   $custom_val = $db->loadResult(); 
		   
		   if (!empty($custom_val)) {
			   $custom_val = trim($custom_val); 
			   if (isset($par_data[$i->product_sku][$custom_val])) {
					$order['items'][$k]->cisloKarty = $par_data[$i->product_sku][$custom_val]; 
					$skus[] = $order['items'][$k]->cisloKarty; 
					continue 2; 
			   }
		   }
		   else {
			   $val = trim($val); 
			   $e = explode('/span>', $val); 
			   if (isset($e[1])) {
				   $ee = $e[1].'/span>'; 
				   $ee = strip_tags($ee); 
				   $custom_val = trim($ee); 
				   
				   if (isset($par_data[$i->product_sku][$custom_val])) {
					$order['items'][$k]->cisloKarty = $par_data[$i->product_sku][$custom_val]; 
					$skus[] = $order['items'][$k]->cisloKarty; 
					continue 2; 
			   }
			   }
			   
		   }
	   }
	   if (empty($order['items'][$k]->cisloKarty)) {
		   $msgs[] = 'neviem najst atribut pre '.$i->product_sku; 
		   continue 2; 
		   
		   
	   }
	   
	  
   }
   else {
	if (empty($i->product_sku)) {
		
		JFactory::getApplication()->enqueueMessage('Empty product SKU, MRP not possible !'); 
		die('Empty product SKU, MRP not possible !'); 
	}
	if (stripos($i->product_sku, ',')===false)
	$order['items'][$k]->cisloKarty = $i->product_sku.',00'; 
	else
	$order['items'][$k]->cisloKarty = $i->product_sku; 
   }
   if (empty($order['items'][$k]->cisloKarty)) {
	   $msgs[] = 'Chyba, chýba SKU v MRP: '.$i->product_sku; 
	   continue 2;
   }
   $skus[] = $order['items'][$k]->cisloKarty; 
   
   
}
	
	$orders[$order_id] = $order; 
}
}

if (!empty($orders)) {
mrpHelper::checkSkus($skus); 

if (!empty($allowconfirm)) {
  echo mrpHelper::render('confirm', $orders, $skus, $toskip, $this); 
  OnepageTemplateHelper::$print_output = true; 
}
else {
	
	mrpHelper::generateAndSendXML($orders, $skus, $this, $msgs); 
	
}

}
else {
	$msgs[] = 'No order found !'; 
}
OnepageTemplateHelper::$do_no_close_app = true; 



if (!empty($msgs)) {
 
 $res = implode("<br />\n", $msgs); 
 echo $res; 
}
echo mrpHelper::render('footer'); 
OnepageTemplateHelper::$print_output = true; 
//JFactory::getApplication()->enqueueMessage($res); 
