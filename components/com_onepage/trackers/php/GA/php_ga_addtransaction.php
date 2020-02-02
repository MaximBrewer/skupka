<?php
/**
 * @version		opctracking.php 
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
use UnitedPrototype\GoogleAnalytics; 
defined('_JEXEC') or die;
	 //php_ga_addtransaction.php
	 
	
	 
	 $config = new UnitedPrototype\GoogleAnalytics\Config(); 
	 //4 second timeout: 
	 $config->setRequestTimeout(20); 
	 $config->setSendOnShutdown(true); 
	 $config->setErrorSeverity(UnitedPrototype\GoogleAnalytics\Config::ERROR_SEVERITY_SILENCE); 
	 $tracker = new GoogleAnalytics\Tracker($this->params->google_analytics_id, $_SERVER['SERVER_NAME'], $config);	 
	 // Assemble Visitor information
	// (could also get unserialized from database)
	$visitor = new GoogleAnalytics\Visitor();
	$visitor->setIpAddress($_SERVER['REMOTE_ADDR']);
	$visitor->setUserAgent($_SERVER['HTTP_USER_AGENT']);
	$visitor->setScreenResolution('1024x768');
	
// Assemble Session information
// (could also get unserialized from PHP session)
     $session = new GoogleAnalytics\Session();
	 $page = new GoogleAnalytics\Page($this->params->page_url); 
	 $page->setTitle('Purchase'); 
	 $orderId = $idformat; 
	 
	  $transaction = new GoogleAnalytics\Transaction(); 
	 $transaction->setOrderId($orderId); 
	 $transaction->setAffiliation('store name'); 
	 $transaction->setTax(number_format($this->order['details']['BT']->order_tax, 2, '.', '')); 
	 $transaction->setTotal(number_format($order_total, 2, '.', '')); 
	 $transaction->setShipping(number_format($this->order['details']['BT']->order_shipment, 2, '.', '')); 
	 $transaction->setCity($this->order['details']['BT']->city); 
	 $transaction->setRegion($this->order['details']['BT']->state_name); 
	 $transaction->setCountry($this->order['details']['BT']->country_3_code); 
	
	 
	 
	foreach ($this->order['items'] as $key=>$order_item) 
	{ 
		
   if (empty($order_item->category_name)) $order_item->category_name = ''; 
   if (!empty($order_item->virtuemart_category_name)) $order_item->category_name = $order_item->virtuemart_category_name;  
   

	 $pid = $order_item->pid; 
	
	 
	  $item = new GoogleAnalytics\Item(); 
	  $item->setOrderId($orderId); 
	  $item->setSku($pid); 
	  $item->setName($order_item->order_item_name); 
	  $item->setQuantity(number_format($order_item->product_quantity , 0, '.', '')); 
	  $item->setPrice(number_format($order_item->product_final_price, 2, '.', '')); 
	  $transaction->addItem($item); 
	}
	 
	
	 
	 
	 
	 $tracker->trackTransaction($transaction, $session, $visitor); 