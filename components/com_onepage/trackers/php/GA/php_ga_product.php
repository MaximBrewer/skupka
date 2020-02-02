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


 $config = array(); 
	 //4 second timeout: 
	 $config['requestTimeout'] = 4; 
	 $config['sendOnShutdown'] = true; 
	 $config['errorSeverity'] = UnitedPrototype\GoogleAnalytics\Config::ERROR_SEVERITY_SILENCE; 
	 $tracker = new GoogleAnalytics\Tracker($this->params->google_analytics_id, $_SERVER['SERVER_NAME']);	 
	 // Assemble Visitor information
	// (could also get unserialized from database)
	$visitor = new GoogleAnalytics\Visitor();
	$visitor->setIpAddress($_SERVER['REMOTE_ADDR']);
	$visitor->setUserAgent($_SERVER['HTTP_USER_AGENT']);
	$visitor->setScreenResolution('1024x768');
	
// Assemble Session information
// (could also get unserialized from PHP session)
     $session = new GoogleAnalytics\Session();
	 $page = new GoogleAnalytics\Page($_SERVER['SCRIPT_URL']); 
	 jimport( 'joomla.document.document' );
	 $doc = JFactory::getDocument(); 
	 if (method_exists($doc, 'getTitle'))
	 $title = $doc->getTitle(); 
     else $title = $product->product_name; 
	 
	 $page->setTitle($title); 
	 
	 
	 
	 
	foreach ($this->products as $key=>$product) 
	{ 
	 

	$pid = $product->pid; 
	 
	 
	
	 
	 $event = new GoogleAnalytics\Event(); 
	 $event->setCategory('Product'); 
	 $event->setAction($product->product_name); 
	 $event->setLabel($pid); 
	 $event->setValue(number_format($product->product_final_price, 2, '.', '')); 
	 $event->setNonInteraction('true'); 
	 
	 $tracker->trackEvent($event, $session, $visitor); 
	 
	 
	 $tracker->trackPageView($page, $session, $visitor); 
	}