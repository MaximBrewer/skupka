<?php
/*
*
* @copyright Copyright (C) 2007 - 2010 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

	defined( '_JEXEC' ) or die( 'Restricted access' );
	jimport( 'joomla.application.component.model' );
	jimport( 'joomla.filesystem.file' );
	
	require_once( JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'export_helper.php' );
	
	class JModelOrder_details extends OPCModel
  {
    function __construct()
		{
			parent::__construct();
		
		}

		
	function &getOrderVM2($order_id)
	{
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'opctracking.php'); 
	
	   $modelOrder = OPCmini::getModel('orders');
	   $order= $modelOrder->getOrder($order_id);
	   return $order; 
	}
	
	function getNext($order_id)
	{
	  $order_id = (int)$order_id; 
	  $db = JFactory::getDBO(); 
	  $q = 'select virtuemart_order_id from #__virtuemart_orders where virtuemart_order_id > '.$order_id.' order by virtuemart_order_id desc limit 0,1'; 
	  $db->setQuery($q); 
	  return $db->loadResult(); 
	  
	}
	
	function getPrev($order_id)
	{
	  $order_id = (int)$order_id; 
	  $db = JFactory::getDBO(); 
	  $q = 'select virtuemart_order_id from #__virtuemart_orders where virtuemart_order_id < '.$order_id.' order by virtuemart_order_id desc limit 0,1'; 
	  $db->setQuery($q); 
	  $e = $db->getErrorMsg(); if (!empty($e)) { echo $e; die(); }
	  return $db->loadResult(); 
	  
	}
	
	function getLangTag(&$order)
	{
	   $lang = JFactory::getLanguage(); 
	$lang = $lang->getDefault(); 
	
   if (!class_exists('VmConfig'))	  
	 {
	  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	  VmConfig::loadConfig(); 
	 }

	
	if (!empty($order['details']['BT']))
	if (!empty($order['details']['BT']->order_language))
	 {
	   $lang = $order['details']['BT']->order_language; 
	 }
	 else
	 {
	   $langs = VmConfig::get('active_languages', array($lang)); 
	   foreach ($langs as $lang2)
	    {
		  $lang = $lang2; 
		  break; 
		}
	 } 
	 
	$vmlang = strtolower($lang); 
	$vmlang = str_replace('-', '_', $vmlang); 


		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		OPCmini::setVMLANG(); 
    
	if (defined('VMLANG'))
	{
	$vmlang_c = VMLANG; 
    if (empty($vmlang) && (!empty($vmlang_c))) $vmlang = VMLANG; 
	}
	return $vmlang; 
	}
		
    function getShippingMethods(&$order)
	{
	
	 if (!class_exists('VmConfig'))	  
	 {
	  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	  VmConfig::loadConfig(); 
	 }
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	  
	  $sm = OPCmini::getModel('shipmentmethod'); 
	  $shipments = $sm->getShipments(); 
	  return $shipments; 
	  
	}
	
	 function getPaymentMethods(&$order)
	{
	
	 if (!class_exists('VmConfig'))	  
	 {
	  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	  VmConfig::loadConfig(); 
	 }
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	  
	  $sm = OPCmini::getModel('paymentmethod'); 
	  $shipments = $sm->getPayments(); 
	  return $shipments; 
	  
	}
	
	
    function save()
    {
     
     JRequest::setVar( 'view', '[ order_details ]' );
     JRequest::setVar( 'layout', 'default'  );  
     $order_id = JRequest::getVar('order_id',0);
     $d = JRequest::get('post');
     $ps_order = new ps_order;
     if ($ps_order->order_status_update($d)) $msg = 'Order status updated'; else 'Error updating order status';
      //echo 'som tu';die();
     $link = 'index.php?option=com_onepage&view=order_details&order_id='.$order_id;
     $this->setRedirect($link, $msg);
    }
    
 	// zisti stav objednavky a zformatuje ho podla toho
	function getStatus($tid)
	{
	  $order_id = JRequest::getVar('order_id');
     $ehelper = new OnepageTemplateHelper;
     $status = $ehelper->getStatus($order_id, $tid);
     // $status moze byt: NONE, PROCESSING, DONE, ERROR
     
     /// ... ernest tu ... 
     
     // vracat by malo html s formatovanym statusom
     return $status;
	  
	}
    
    
    // mal by vracat bud cely <a href=".... pre konkretnu vygenerovanu template
    // alebo ikonku pre vygenerovanie a pod
    function getHref($tid)
    {
      $order_id = JRequest::getVar('order_id');
      $ehelper = new OnepageTemplateHelper;
      return $ehelper->getFileHref($tid, $order_id);
      
    }  
  
	// vracia zoznam templatov aj s nastavenim v numerickom array    
    function getTemplates()
    {
     $ehelper = new OnepageTemplateHelper;
     return $ehelper->getExportTemplates();
    }
    
    // vracia xml pre request['order_id'] a zadanu tid
	// tato funkcia sa bude pouzivat najma pre AJAX
	// v teste ju mozeme pouzit priamo aj v order_details 
	function getXml($tid)
	{
     $order_id = JRequest::getVar('order_id');
     if (empty($order_id)) return "";
     $ehelper = new OnepageTemplateHelper;
     return $ehelper->getXml($tid, $order_id);
	}
    
    // vracia pole so vsetkymi beznymi hodnotami objednavky
    function getOrderData()
    {
     $order_id = JRequest::getVar('order_id');
     if (empty($order_id)) return "";
     $ehelper = new OnepageTemplateHelper;
     return $ehelper->getOrderData($order_id);
    }
    
}