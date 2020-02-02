<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		 $config = new JModelConfig(); 
		 $config->loadVmConfig(); 
		 VmConfig::loadJLang('com_virtuemart');
		 VmConfig::loadJLang('plg_vmpsplugin', false);

$msgs = array(); 
OnepageTemplateHelper::$do_no_store_output = true; 
require_once(__DIR__.DIRECTORY_SEPARATOR.'dropship'.DIRECTORY_SEPARATOR.'helper.php'); 
$dir = $this->prepareDirectory($tid); 
/*basic config*/
dropshipHelper::$tidd = $tidd; 
dropshipHelper::$dir = $dir; 


$orders = array(); 
$orderModel = VmModel::getModel('Orders'); 
$skus = array(); 

$allowconfirm = (bool)$tidd['config']->allowconfirm; 

$isaction = JRequest::getInt('doaction', -1); 
if ($isaction === 1) $allowconfirm = false; 

if ($isaction === 2) {
	dropshipHelper::assignManufacturers(); 

}


$groups = array(); 
$errors = array(); 

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
		JFactory::getApplication()->enqueueMessage('Skipping order - Order ID '.$order_id.' was alredy imported: '.$arr[0]); 
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
	    $order['items'][$k]->quantity = $i->product_quantity; 
	    $product_id = $i->virtuemart_product_id; 
		$i->order = $order; 
		$mfdata = dropshipHelper::getMF($product_id); 
		if (empty($mfdata)) {
			$errors[$product_id] = $i; 
		}
		else {
			$mf_id = (int)$mfdata['virtuemart_manufacturer_id']; 
			$virtuemart_order_item_id = (int)$i->virtuemart_order_item_id;
			
			
			$groups[$mf_id][$virtuemart_order_item_id] = $i; 
			
			
			
		}
     }
	 
	 
	 
	 
  $orders[$order_id] = $order; 
}
	
}
$mfs = dropshipHelper::getMFS(); 

if (!empty($errors)) {
	echo dropshipHelper::render('errors', array('errors'=>$errors, 'orders'=>$orders, 'mfs'=>$mfs, 'localid'=>$localid), $this); 
}
else
if (!empty($orders)) {


if ($isaction === 3) {
	dropshipHelper::changeOrderStatuses($orders); 
}



if ($isaction !== 3)  {
if (!empty($allowconfirm)) {
	
	
	
  echo dropshipHelper::render('confirm', array('groups'=>$groups, 'mfs'=>$mfs, 'localid'=>$localid), $this); 
  OnepageTemplateHelper::$print_output = true; 
}
else {
	
	//dropshipHelper::generateAndSendXML($orders, $skus, $this, $msgs); 
	$fout = $fhash = array(); 
	dropshipHelper::createXLS($groups, $mfs, $this, $fout, $fhash); 
	echo dropshipHelper::render('links', array('fout'=>$fout, 'fhash'=>$fhash, 'tidd'=>$tidd, 'ehelper'=>$this, 'mfs'=>$mfs), $this); 
	
	$msg = dropShipHelper::sendEmails($mfs, $fhash, $this); 
	?><p><?php echo $msg; ?></p><?php
	if (!empty($tidd['config']->changeorderstatussecondstep)) {
		$_orderStatusList = dropshipHelper::getOrderStatuses(); 
			
			
	
	    $calculatedStatuses = dropshipHelper::calculateStatuses($orders, $_orderStatusList); 
		echo dropshipHelper::render('statuses', array('orders'=>$orders, 'groups'=>$groups, 'mfs'=>$mfs, 'localid'=>$localid, '_orderStatusList'=>$_orderStatusList, 'calculatedStatuses'=>$calculatedStatuses), $this); 
	}
	}
	
	
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
echo dropshipHelper::render('footer'); 
OnepageTemplateHelper::$print_output = true; 
//JFactory::getApplication()->enqueueMessage($res); 
