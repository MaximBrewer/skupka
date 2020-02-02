<?php
/**
 * @version		$Id: tracking.php 
 * @package		tracking model for opc
 * @subpackage	com_onepage
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class JModelTracking extends OPCModel
{
  function storeCustom($data) {
	  //return;
    $trackings = array(); 
	foreach($data as $row) {
	  if (empty($trackings[$row['tracking']])) $trackings[$row['tracking']] = array(); 
	  $trackings[$row['tracking']][] = $row; 
	  
	  
	}
	
	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		 $config = new JModelConfig(); 
		 $config->loadVmConfig(); 
		 $files = $config->getPhpTrackingThemes();
		 $statuses = $config->getOrderStatuses();
		 $data = JRequest::get('post');
		  jimport('joomla.filesystem.file');
		  
		  $configG = $this->getConfigG(); 
		  $changed2 = false; 
		  $advP = array(); 
		  //prefill advP with all disabled: 
		  foreach ($files as $file) {
		    foreach ($statuses as $statusR) {
			  $status = $statusR['order_status_code']; 
			  $advP[$file][$status] = false; 
			}
		  }
		  
		  
		  
     foreach ($files as $file) {
		 $changed = false; 
		 
		 
		$config = new stdClass(); 
	    $prevConfig = OPCconfig::getValue('tracking_config', $file, 0, $config); 	
		
		if ($prevConfig->enabled)
	    if (empty($trackings[$file])) {
		   //it was removed for all order statuses: 
		   $this->disablePerStatus($file, $configG);
		   $changed2 = true; 
		   continue; 
		}
		
	
		
		
		$advL = array(); 
		$advanced = array(); 
		
		if (isset($trackings[$file]))
		foreach ($trackings[$file] as $row) {
		     $status = $row['order_status']; 
			 if (empty($status)) continue; 
			 $pid = (int)$row['payment_id']; 
			 $language = $row['language']; 
			 if ((!empty($pid)) || (!empty($language))) {
			   $advanced[$status] = new stdClass(); 
			   $advanced[$status]->payment_id = (int)$pid; 
			   $advanced[$status]->language = $language; 
			 }
			 
			 if (empty($advP[$file])) $advP[$file] = array(); 
			 $advP[$file][$status] = $status; 
			 
		
		}
		if (empty($advanced) && (!empty($prevConfig->advanced))) {
		  unset($prevConfig->advanced); 
		  $changed = true; 
		}
		if (!empty($advanced))
		{
			$changed = true; 
			$prevConfig->advanced = $advanced; 
		}
		
		if ($changed) {
			
		  OPCconfig::store('tracking_config', $file, 0, $prevConfig); 
	   
			if (!empty($ref))
			{
				$nc = true; 
				$ref = 1; 
				OPCconfig::store('tracking_config', $file, $ref, $nc); 
			}
		}
		
		
		
	 }
	 //var_dump($advP); die(); 
	 //if ($changed2) 
	 {
	   $this->storeConfigG($configG, '', $advP); 
	 }
	
  }
  function store()
  {
    $enabled = JRequest::getVar('adwords_enabled_0', false); 
	 
	 $order = JRequest::getInt('tracking_order', 9999); 
	 
    $msg = $this->setEnabled($enabled, $order); 

    require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
      /*
	if (!OPCJ3)
	{
	 require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'opcparameters.php'); 
	}
	else
	{
	   require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'jformrender.php'); 
	}
*/
	
	
	$wasEnabled = array(); 
	
	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		 $config = new JModelConfig(); 
		 $config->loadVmConfig(); 
		 $files = $config->getPhpTrackingThemes();
		 $statuses = $config->getOrderStatuses();
		 $data = JRequest::get('post');
		  jimport('joomla.filesystem.file');
     foreach ($files as $file)
	 {
	  
	   
	  
	   $file = JFile::makeSafe($file);
	
	   $path = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.$file.'.xml'; 
	   $nd = new stdClass(); 
	   
	   //$params = new OPCparameters($nd, $file, $path, 'opctracking');
	   
	   
	   // the item is not enabled: 
	   if (empty($data[$file])) 
		{
		   $data[$file]['enabled'] = 0; 
		}
	   
	   $config = OPCconfig::buildObject($data[$file]); 
       
	   if (!is_object($config)) $config = new stdClass(); 
	   
	   $prevConfig = OPCconfig::getValue('tracking_config', $file, 0, $config); 	   
	   //$configG = OPCconfig::getValues('tracking'); 
	   foreach ($prevConfig as $kX=>$vX) {
	     if (!isset($config->$kX)) $config->$kX = $vX; 
	   }
	   

	   $key = 'enabled'; 
	   $is_enabled = (int)JRequest::getVar('plugin_'.$file, -1); 
	   
	   if (!empty($prevConfig) && (is_object($config)))
	   {
	   if (empty($prevConfig->enabled) && (!empty($is_enabled)))
	   {
	      $config->enabled_since = time();    
		  $wasEnabled[$file] = time(); 
	   }
	   else
	   {
	      if (!empty($prevConfig->enabled_since))
	      $wasEnabled[$file] = $prevConfig->enabled_since; 
		  else 
		  $wasEnabled[$file] = time(); 
		  
	      $config->enabled_since = $wasEnabled[$file];    
	   }
	   }
	   
	   foreach ($prevConfig as $kp=>$vp)
	   {
	      if (!isset($config->$kp)) $config->$kp = ''; 
	   }
	   
	  
	   if (empty($is_enabled))
	   {
	     $wasEnabled[$file] = false; 
	   }
	   
	   $config->$key = $is_enabled; 
	   
	   if (!empty($config->enabled))
	   {
	     $ref = 1; 
		
	   }
	   else
	   {
	    $ref = 0; 
	   }
	   
	   
	   OPCconfig::store('tracking_config', $file, 0, $config); 
	   
	   
	   
	   if (!empty($ref))
	   {
	    $nc = true; 
	    OPCconfig::store('tracking_config', $file, $ref, $nc); 
	   }
	   else
	   {
	     OPCconfig::clearConfig('tracking_config', $file, 1); 
	   }
	 }
	 
	 $aba = JRequest::getVar('aba_enabled', false); 
	 if (!empty($aba)) $aba = true; 
	 
	 OPCconfig::store('aba', '', 0, $aba); 
	 
	 $advanced_tracking2 = JRequest::getVar('advanced_tracking', false); 
	 if (!empty($advanced_tracking2)) $advanced_tracking2 = true; 
	 
	 $advanced_tracking = OPCconfig::getValue('advanced_tracking', '', 0, false); 
	 
	 OPCconfig::store('advanced_tracking', '', 0, $advanced_tracking2); 
	
	 
	  
	
	 { 
	 
	 
	 foreach ($statuses as $status)
	 {
	    $status2 = $status['order_status_code']; 
		
		$default = new stdClass(); 
		$config = OPCconfig::getValue('tracking', $status2, 0, $default); 
	    
		
		// config is an object of strings ONLY
		if (!empty($config))
		foreach ($config as $file=>$data2)
		{
		  if (is_object($data2))
		  unset($config->$file);
		  else
		  if (stripos($file, 'since')===0) 
		  {
		    $file2 = substr($file, 5); 
			
			if (!in_array($file2, $files)) unset($config->$file); 
			else
			 {
			    $config->{$file2.'_since'} = $data2; 
				unset($config->$file); 
			 }
		  }
		  else
		  if (stripos($file, '_since')!==false) 
		  {
		    $file2 = str_replace('_since', '', $file); 
			if (!in_array($file2, $files)) unset($config->$file); 
		  }
		  else
		  if (!in_array($file, $files)) unset($config->$file); 
		  
		}
		
		
		
	    foreach ($files as $file)
		{
		
		/*
		if (!empty($config->$file))
		$wasEnabled = true; 
		else $wasEnabled = false; 
		*/
		
		   $is_enabled = (int)JRequest::getVar('plugin_'.$file, -1); 
		   $t1 = JRequest::getVar('plugin_'.$file, -1); 
		   
		   if (!empty($advanced_tracking)) {
				$enabled = JRequest::getVar($file.'_'.$status2, 0); 
				$only_when = JRequest::getVar('opc_tracking_only_when_'.$status2, ''); 
	       }
		   else
		   {
			   $data = JRequest::getVar($file.'_order_status_code'); 
			   $only_when = ''; 
			   if (!empty($data)) 
			   {
				   if (in_array($status2, $data))
				   {
					   $enabled = 1; 
				   }
				   else
				   {
					   $enabled = 0; 
				   }
				   
				   
				   
			   }
			   else {
			      $enabled = 0; 
			   }
			   
		   }
		   
		   
		  $key = $file.'_since'; 
		  
		  $ct2 = new stdClass(); 
		  //$ct = OPCconfig::getValue('tracking', $status2, 0, $ct2); 
		  
		  
		  
		  // stAn - do not ovewrite since time when not altering status
		  
		  // enabled per status: 
		  if ($enabled)
		  {
		  if (empty($config->$file))
		  {
		     // if the file was not enabled per this status before
		     $config->$key = time(); 
		  }
		  else
		  {
		  
		  
		     $config->$key = $wasEnabled[$file]; 
		  }
		  }
		  else
		  {
		    unset($config->$key); 
			unset($config->$file);
			
			 $enabled = false; 
		  $key = $file.'_enabled'; 
		 
		   // clear database of unneeded data: 
		   unset($config->$key); 
		   $key = $file.'_since'; 
		   unset($config->$key); 
			
		  }
		  
	   // gneral enabled (plugin)
	   if (empty($is_enabled))
	   {
	     $enabled = false; 
		 $key = $file.'_enabled'; 
		 
		 // clear database of unneeded data: 
		 unset($config->$key); 
		 $key = $file.'_since'; 
		 unset($config->$key); 
	   }
	   if (!empty($is_enabled) && (!empty($enabled)))
	   {
	    //if (empty($config->$file)) $config->$file = $file; 
	    $key = $file.'_enabled'; 
	    $config->$key = true; 
		$config->$file = $enabled;  
	   }
	   
		  
		  if (empty($enabled))
		  unset($config->$file);
		  else
		  $config->$file = $enabled; 
		  
		  
		  
		  
		  
		  
		  
		}
		
		
		$tmp = (array)$config; 
		
		if (empty($tmp))
		{
		$checkempty = true; 
		}
		else $checkempty = false; 
		
		
		// removed in opc 303
		$config->code = ''; ///JRequest::getVar('adwords_code_'.$status2, '', 'post', 'string', JREQUEST_ALLOWRAW); 
		
		
		$config->only_when = $only_when; 
		
		$donotsave =false; 
		if ($checkempty)
		{
		   if (empty($config->code))
		    {
			   OPCconfig::clearConfig('tracking', $status2, 0); 
			   
			   $donotsave =true; 
			}
		}
		if (!$donotsave)
		{
		OPCconfig::store('tracking', $status2, 0, $config); 
		
		
		}
	 }
	 }
	 
	 
	 $negative_statuses = JRequest::getVar('negative_statuses', array());
	 if (is_array($negative_statuses))
	  {
		
        OPCconfig::store('tracking_negative', 'negative_statuses', 0, $negative_statuses);	    
	  }

	 
	 
	 
	 
	 
	   
	   
	 return $msg;
  }
  function getConfigG() {
   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	   $OPCconfig = new JModelConfig(); 
	   
     $statuses = $OPCconfig->getOrderStatuses();
  $configG = array(); 
		  foreach ($statuses as $statusR) {
			$status = $statusR['order_status_code']; 
			$default = new stdClass(); 
		    $config = OPCconfig::getValue('tracking', $status, 0, $default); 
			$configG[$status] = $config; 
		  }
    return $configG; 
  
  }
  function storeConfigG($configG, $status='', $advP=array()) {
	 
	  if (empty($status)) {
		if (!empty($advP)) {
			
		
			
			   foreach ($advP as $file=>$statusesP) {
			      foreach ($statusesP as $kk=>$st) {
				     if (isset($configG[$kk])) {
						  $keyEnabled = $file.'_enabled'; 
						  $keySince = $file.'_since'; 
					   //foreach ($configG[$kk] as $k2=>$v2) 
					   {
					       if ($st === false) {
						      /*
							  if (strpos($k2, $file)===0) {
								 // plugin is disabled per this status: 
							     unset($configG[$kk]->$k2); 
							  }
							  */
							  unset($configG[$kk]->$keyEnabled); // = false; 
							  unset($configG[$kk]->$keySince); 
							  unset($configG[$kk]->$file); 
							
						   }
						   
						   
							
						  }
						  // enabled: 
						  if ($st === $kk)
						  if (!isset($configG[$kk]->$keyEnabled)) {
						      $configG[$kk]->$keyEnabled = true; 
							  $configG[$kk]->$keySince = time(); 
							  $configG[$kk]->$file = true; 
						  }
					   }
					 
					 }
				  }
			   
			}  
		  
	     foreach ($configG as $status=>$config) {
		
		    OPCconfig::store('tracking', $status, 0, $config); 
		 }
	  }
	  else
	  {
		  OPCconfig::store('tracking', $status, 0, $configG); 
	  }
	  
	  
  }
  
  function disablePerStatus($tracking, &$configG, $status='') {
	  if (empty($status)) {
	   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	   $OPCconfig = new JModelConfig(); 
	   
     $statuses = $OPCconfig->getOrderStatuses();
	
	   foreach ($configG as $status=>$configS) {
		      foreach ($configS as $k=>$v) {
			     if (stripos($k, $tracking)===0) unset($configG[$status]->$k); 
			  }
		   }
		   
		   
	}
	else
	{
		if (isset($configG[$status]))
		foreach ($configG[$status] as $k=>$v) {
			     if (stripos($k, $tracking)===0) unset($configG[$status]->$k); 
			  }
	}
  }
  function isPluginEnabled($file, &$config)
  {
  
	
  
     $enabled = false; 
	 foreach ($config as $status=>$c)
					 {
					    if (!empty($c->$file)) 
						$enabled = true; 
					 }
     if ($enabled) return true; 
	
     $default = new stdClass(); 
	 $ic = OPCconfig::getValue('tracking_config', $file, 0, $default); 
	 if (empty($ic->enabled)) return false; 
	 else return true; 

     return false; 	 
     				
					
  }
  
  function setEnabled($enabled = null, $order = null)
  {
   if (is_null($enabled))
  $enabled = JRequest::getVar('adwords_enabled_0', false); 
  
   $order = JRequest::getInt('tracking_order', 9999); 
  
       require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	   
	   OPCmini::clearTableExistsCache(); 
	   $isM55 = OPCmini::isMysql('5.6.5'); 
	   
	   
	   if ($isM55) {
	 if (!OPCmini::tableExists('virtuemart_plg_opctracking'))
	 {
	   $db = JFactory::getDBO(); 
	   $q = '
	CREATE TABLE IF NOT EXISTS `#__virtuemart_plg_opctracking` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`virtuemart_order_id` int(11) NOT NULL,
	`hash` varchar(32) NOT NULL,
	`shown` text NOT NULL,
	`created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`created_by` int(11) NOT NULL,
	`modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`modified_by` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `hash_2` (`hash`,`virtuemart_order_id`),
	KEY `virtuemart_order_id` (`virtuemart_order_id`),
	KEY `hash` (`hash`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;'; 
	$db->setQuery($q); 
	$db->query(); 
	$e = $db->getErrorMsg(); if (!empty($e)) return $e; 
	 }
	   }
	   else
	   {
		   if (!OPCmini::tableExists('virtuemart_plg_opctracking'))
	 {
	   $db = JFactory::getDBO(); 
	   $q = '
	CREATE TABLE IF NOT EXISTS `#__virtuemart_plg_opctracking` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`virtuemart_order_id` int(11) NOT NULL,
	`hash` varchar(32) NOT NULL,
	`shown` text NOT NULL,
	`created` timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	`created_by` int(11) NOT NULL,
	`modified` datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	`modified_by` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `hash_2` (`hash`,`virtuemart_order_id`),
	KEY `virtuemart_order_id` (`virtuemart_order_id`),
	KEY `hash` (`hash`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;'; 
	$db->setQuery($q); 
	$db->query(); 
	$e = $db->getErrorMsg(); if (!empty($e)) return $e; 
	 } 
	   }
  
   //update from prior opc versions: 
	 $db = JFactory::getDBO(); 
     $q = "delete from `#__extensions` WHERE  element = 'opctracking' and folder = 'system' "; 
     $db->setQuery($q); 
	 $db->query(); 
  OPCmini::clearTableExistsCache(); 
     	$msg = ''; 
	 // stAn, always copy plugins: 
	 //if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking'))
	    
		
		   jimport('joomla.filesystem.folder');
		   jimport('joomla.filesystem.file');
		   /*
		   if (JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking')!==false)
		   {
		   
		   }
		   else $msg .= JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_DIRECTORY', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking')."<br />\n"; ; 
		
		if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking'))
		{
			if (JFile::copy(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opctracking'.DIRECTORY_SEPARATOR.'opctracking.php', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking'.DIRECTORY_SEPARATOR.'opctracking.php')===false)
		   {
			    $msg .= JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_FILE_IN', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking')."<br />\n"; 
		   }
		   JFile::copy(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opctracking'.DIRECTORY_SEPARATOR.'opctracking.xml', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking'.DIRECTORY_SEPARATOR.'opctracking.xml'); 
		   JFile::copy(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opctracking'.DIRECTORY_SEPARATOR.'index.html', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking'.DIRECTORY_SEPARATOR.'index.html'); 
		}
		
		 if (JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opctrackingsystem')!==false)
		   {
		   
		   
		   }
		   else $msg .= JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_DIRECTORY', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking')."<br />\n"; ; 
		
		if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opctrackingsystem'))
		{
			if (JFile::copy(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opctrackingsystem'.DIRECTORY_SEPARATOR.'opctrackingsystem.php', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opctrackingsystem'.DIRECTORY_SEPARATOR.'opctrackingsystem.php')===false)
		   {
			    $msg .= JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_FILE_IN', 'opctrackingsystem.php', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opctrackingsystem'.DIRECTORY_SEPARATOR.'opctrackingsystem.php')."<br />\n"; 
		   }
		   JFile::copy(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opctrackingsystem'.DIRECTORY_SEPARATOR.'opctrackingsystem.xml', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opctrackingsystem'.DIRECTORY_SEPARATOR.'opctrackingsystem.xml'); 
		   JFile::copy(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opctrackingsystem'.DIRECTORY_SEPARATOR.'index.html', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opctrackingsystem'.DIRECTORY_SEPARATOR.'index.html'); 
		   
		}
		
		}
		
	 
	 if (!empty($msg)) 
	 {
		 
		 if ((!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opctrackingsystem'.DIRECTORY_SEPARATOR.'opctrackingsystem.php')) || (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking'.DIRECTORY_SEPARATOR.'opctracking.php')))
		 {
			return $msg; 
		 }
	 }
	 */
	 require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
	 $config = new JModelConfig(); 
	 $config->copyPlugin('system', 'opctrackingsystem'); 
	 $config->copyPlugin('vmpayment', 'opctracking'); 
	 
	 if (!empty($enabled))
	   {
	   
	      $db = JFactory::getDBO(); 
		  $q = "select * from #__extensions where element = 'opctracking' and type='plugin' and folder='vmpayment' limit 0,1"; 
		  $db->setQuery($q); 
		  $isInstalled = $db->loadAssoc(); 
		  
		  if (empty($isInstalled))
		   {
		      $q = ' INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES ';
			  $q .= " (NULL, 'plg_vmpayment_opctracking', 'plugin', 'opctracking', 'vmpayment', 0, 1, 1, 0, '{\"legacy\":false,\"name\":\"plg_vmpayment_opctracking\",\"type\":\"plugin\",\"creationDate\":\"December 2013\",\"author\":\"RuposTel s.r.o.\",\"copyright\":\"RuposTel s.r.o.\",\"authorEmail\":\"admin@rupostel.com\",\"authorUrl\":\"www.rupostel.com\",\"version\":\"1.7.0\",\"description\":\"One Page Checkout Affiliate Tracking support for VirtueMart 2\",\"group\":\"\"}', '{}', '', '', 0, '0000-00-00 00:00:00', '".$order."', 0) "; 
		      $db->setQuery($q); 
		      $db->query(); 
			  $e = $db->getErrorMsg(); if (!empty($e)) return $e; 
		   }
		   else
		   {
		      //if (empty($isInstalled['enabled']))
			  {
			  
		    $order = JRequest::getVar('tracking_order', null); 
			if (!empty($order))
			{
		     $q = " UPDATE `#__extensions` SET  `enabled` =  '1', `ordering`='".$order."', `state` = 0 WHERE  element = 'opctracking' and folder = 'vmpayment' "; 
			 $db->setQuery($q); 
			 $db->query(); 
			 $e = $db->getErrorMsg(); if (!empty($e)) { return $e;  }
			}
			else
			{
		     $q = " UPDATE `#__extensions` SET  `enabled` =  '1', `state` = 0 WHERE  element = 'opctracking' and folder = 'vmpayment' "; 
			 
			 $db->setQuery($q); 
			 $db->query(); 
				
			}
			
		     

			  }
			  
			  
		   }
		   
		   
		    $db = JFactory::getDBO(); 
		  $q = "select * from #__extensions where element = 'opctrackingsystem' and type='plugin' and folder='system' limit 0,1"; 
		  $db->setQuery($q); 
		  $isInstalled = $db->loadAssoc(); 
		  
		  if (empty($isInstalled))
		   {
		      $q = ' INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES ';
			  $q .= " (NULL, 'plg_system_opctrackingsystem', 'plugin', 'opctrackingsystem', 'system', 0, 1, 1, 0, '{\"legacy\":false,\"name\":\"plg_system_opctrackingsystem\",\"type\":\"plugin\",\"creationDate\":\"December 2014\",\"author\":\"RuposTel s.r.o.\",\"copyright\":\"RuposTel s.r.o.\",\"authorEmail\":\"admin@rupostel.com\",\"authorUrl\":\"www.rupostel.com\",\"version\":\"1.7.0\",\"description\":\"One Page Checkout Affiliate Tracking System Plugin for VirtueMart 2\",\"group\":\"\"}', '{}', '', '', 0, '0000-00-00 00:00:00', '".$order."', 0) "; 
		      $db->setQuery($q); 
		      $db->query(); 
			  $e = $db->getErrorMsg(); if (!empty($e)) return $e; 
		   }
		   else
		   {
		      //if (empty($isInstalled['enabled']))
			  {
			  
		    $order = JRequest::getVar('tracking_order', null); 
			if (!empty($order))
			{
		     $q = " UPDATE `#__extensions` SET  `enabled` =  '1', `ordering`='".$order."', `state` = 0 WHERE  element = 'opctrackingsystem' and folder = 'system' "; 
			 $db->setQuery($q); 
			 $db->query(); 
			 $e = $db->getErrorMsg(); if (!empty($e)) { return $e;  }
			}
			else
			{
		     $q = " UPDATE `#__extensions` SET  `enabled` =  '1', `state` = 0 WHERE  element = 'opctrackingsystem' and folder = 'system' "; 
			 $db->setQuery($q); 
			 $db->query(); 
				
			}
			
		     

			  }
			  
			  
		   }
		   
		   
		   return $msg; 
		  
	   }
	   else
	   {
	      
			  
			$db = JFactory::getDBO(); 
		  $q = "select * from #__extensions where element = 'opctracking' and type='plugin' and folder='vmpayment' limit 0,1"; 
		  $db->setQuery($q); 
		  $isInstalled = $db->loadAssoc(); 
		  if (!empty($isInstalled))
		  {
		    $db = JFactory::getDBO(); 
		    $q = " UPDATE `#__extensions` SET  enabled =  '0', ordering='".$order."', state = 0 WHERE  element = 'opctracking' and folder = 'vmpayment' "; 
			$db->setQuery($q); 
			$db->query(); 
			$e = $db->getErrorMsg(); if (!empty($e)) return $e; 
		  }
		  
		    $db = JFactory::getDBO(); 
		    $q = " UPDATE `#__extensions` SET  enabled =  '0' WHERE  element = 'opctrackingsystem' and folder = 'system' "; 
			$db->setQuery($q); 
			$db->query(); 
			$e = $db->getErrorMsg(); if (!empty($e)) return $e; 
			
		 

			  
	   }
	   OPCmini::clearTableExistsCache(); 
	   return ''; 
  }
  
  function getAba()
  {
     $ret = OPCconfig::getValue('aba', '', 0, false); 
	 
	 return $ret; 
  }
  
  function isEnabled($order=false)
  {
  
  
    $db = JFactory::getDBO(); 
		  $q = "select * from #__extensions where element = 'opctracking' and type='plugin' and folder='vmpayment' limit 0,1"; 
		  $db->setQuery($q); 
		  $isInstalled = $db->loadAssoc(); 
		  
	
		  $q = "select * from #__extensions where element = 'opctrackingsystem' and type='plugin' and folder='system' limit 0,1"; 
		  $db->setQuery($q); 
		  $isInstalledSys = $db->loadAssoc(); 
		  
		  
	if (!$order)
	{
		  if (empty($isInstalled)) return false; 
		  if (!empty($isInstalled['enabled'])) 
		  {
		  if (empty($isInstalledSys) || (empty($isInstalledSys['enabled'])))
		    {
			
		
			   if ((empty($isInstalledSys)) || (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opctrackingsystem'.DIRECTORY_SEPARATOR.'opctrackingsystem.php')))
			   {
			  
			   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
			   $opcconfig = new JModelConfig(); 
			   $sysplg = 'plg_system_opctrackingsystem'; 
			   $opcconfig->getOPCExtensions($sysplg); 
			   
			   $msg = 'opctrackingsystem plugin: '.$opcconfig->installext($sysplg); 
			   JFactory::getApplication()->enqueueMessage($msg); 
			   
			    	
			   }
			   
			   $q = 'update #__extensions set `enabled` = 1, `state` = 0  where element = \'opctrackingsystem\' and type = \'plugin\' and folder = \'system\' limit 1'; 
			   $db->setQuery($q); 
			   $db->query(); 
			   
			}
			
		  
		  
		  
		  return true; 
		  }
		  
		    $q = 'update #__extensions set `enabled` = 0 where element = \'opctrackingsystem\' and type = \'plugin\' and folder = \'system\' limit 1'; 
			   $db->setQuery($q); 
			   $db->query(); 
		  
	return false; 
	}
	else
	{
	  if (empty($isInstalled)) return 9999; 
	  return $isInstalled['ordering']; 
	}
  }
  function getStatusConfig($statuses)
  {
     $arr = array(); 
     foreach ($statuses as $status)
	 {
	  
	   $status2 = $status['order_status_code']; 
	   $default = new stdClass(); 
	   
	   $arr[$status2] = OPCconfig::getValue('tracking', $status2, 0, $default);
	   
		if (empty($arr[$status2]))
		{
		  unset($arr[$status2]); 
		}
		else
		if (is_object($arr[$status2]))
		{
		  $tmp = (array)$arr[$status2]; 
		  if (empty($tmp)) unset($arr[$status2]);
		  
		}
		else
		{
		if (!isset($arr[$status2]->code)) $arr[$status2]->code = ''; 
		if (!isset($arr[$status2]->only_when)) $arr[$status2]->only_when = ''; 
		if (!isset($arr[$status2]->since)) $arr[$status2]->since = time(); 
	   
	    
		}
	   
	 }
	 return $arr; 
  }
  
  function showOrderVars()
  {
    $array = array(); 
	$object = new stdClass(); 
	
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'opctracking.php'); 
	OPCtrackingHelper::getOrderVars(0, $array, $object, true); 
    
  }
  function getOrderVars(&$named)
  {
    require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'opctracking.php'); 
	 $array = array(); 
	 $object = new stdClass(); 
	 $named = array(); 
	 OPCtrackingHelper::getOrderVars(0, $array, $object, false, $named); 
	 
	 
	 
	 return $array; 
  }
  
  function getJforms($files, $data_config='tracking_config', $xmlpath=null, $tid=0)
    { 
		if (!class_exists('VmConfig'))	  
		{
			require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		}
		VmConfig::loadConfig(); 

	 
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	  require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'jformrender.php');  
	  /*
	  if (!OPCJ3)
	  {
	    require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'opcparameters.php'); 
	  }
	  */
	 
	  	  
	 $ret = array(); 
	 foreach ($files as $file)
	 {
	   if ($data_config === 'tracking_config') {
	    $path = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.$file.'.xml'; 
	    if (!file_exists($path)) continue; 
	   }
	   else {
		   $pa = pathinfo($file); 
		   if ($pa['extension'] === 'xml') $file = JFile::makeSafe($pa['filename']); 
		   
		   $path = $xmlpath.DIRECTORY_SEPARATOR.$file.'xml'; 
		   if (!file_exists($path)) continue; 
	   }
	   
	   
	   $default = new stdClass();  
	   //$data->adwords_id = 1; 
	   $data = OPCconfig::getValue($data_config, $file, $tid, $default);
	   
	   
	   $title = $description = ''; 
	   
	   if (function_exists('simplexml_load_file'))
	   {
	   $fullxml = simplexml_load_file($path);
	   
	   $title = $fullxml->name; 
	   $description = $fullxml->description; 
	   
	   
	   }
	   
	   /*
	   if (!OPCJ3)
	   {
	    $params = new OPCparameters($data, $file, $path, 'opctracking'); 
	    $test = $params->vmRender($file); 
	   }
	   else
	   */
	   
	   {
	   
	   
	   
	   	   $xml = file_get_contents($path); 
		$xml = str_replace('extension', 'form', $xml); 
		$xml = str_replace('params', 'fieldset', $xml); 
		$xml = str_replace('<fieldset', '<fields name="'.$file.'"><fieldset name="test" label="'.$title.'" ', $xml); 
		$xml = str_replace('param', 'field', $xml); 
		$xml = str_replace('</fieldset>', '</fieldset></fields>', $xml); 
		//$fullxml = simplexml_load_string($xml);

		
		// removes BOM: 
		$bom = pack('H*','EFBBBF');
		$text = preg_replace("/^$bom/", '', $xml);
		if (!empty($text)) $xml = $text; 

		$t1 = simplexml_load_string($xml); 
		if ($t1 === false) continue; 
		
		
		
		
	    $test = JForm::getInstance($file, $xml, array(),true);
		
		$multilang_atribs = array(); 
		//if ($file === 'google_tag_manager') 
		{
			//echo $xml; 
			foreach ( $t1->xpath('/form/fields/fieldset/field[@multilang="multilang"]') as $block)
			{
				
				//$name = $block->getAttribute('name');
                if (isset($block['name']))
				{
					
				}					
				$z = $block['name']; 
				$z = (string)$z; 
				$multilang_atribs[$z]  = $z; 
				
			}

			
			
		}
		
		
		$multilang = array(); 
		//$test->bind($data); 
		//foreach ($data as $k=>$vl)
		foreach ($multilang_atribs as $k=>$vl)
		{
					//if ($file === 'google_tag_manager')
					{
							
							$atr = $test->getFieldAttribute($k, 'multilang', false, $file); 
							if ($atr !== false)
						    {
								$atr = (string)$atr; 
								if ($atr === 'multilang')
								{
									$multilang[] = $k; 
									/*
									$langs = VmConfig::get('active_languages');
									if (!empty($langs))
									foreach ($langs as $lcode)
									{
									
									$ml = str_replace('-', '_', $lcode);
									$ml = strtolower($ml); 
									
									// ok
									$el = $test->getField($k, $file);
									
									if ($el !== false)
									{
									$class = new ReflectionClass($el); 
									$property = $class->getProperty("element");
									$property->setAccessible(true);
									$val = $property->getValue($el);
									if (!empty($val))
									if (is_object($val))
									{
									$val2 = clone $val; 
									foreach ($val2->attributes() as $k=>$vx)
									{
										$k2 = (string)$k; 
										if (($k2 === 'name') || ($k2 === 'id'))
										{
											$name = (string)$vx; 
											$val2[$k2] = $vx.$ml; 
										}

									}

									$parent = $val->xpath('..'); 
									$parent->addChild($val2); 
									var_dump($parent); die(); 
									//$val->parentNode->addChild($val2); 
									//$dom = dom_import_simplexml($val);
								    //$dom->parentNode->appendChild($val2);
									
									//$test->setField($val2, null, false); 
									$e = $test->getErrors(); 
									if (!empty($e)) die($e); 
									}
									}
									}
									*/
									
								}
							}
						
						
					}
		  
		
		}
		
		$nm = array(); 
		
		/*
		if ($file === 'google_tag_manager') {
		var_dump($multilang); die(); }
		*/
		
		if (!empty($multilang))
		{
			
			
			$langs = VmConfig::get('active_languages');
			
	   if (class_exists('JLanguageHelper') && (method_exists('JLanguageHelper', 'getLanguages')))
		{
			$langs = array(); 
		$sefs 		= JLanguageHelper::getLanguages();
		foreach ($sefs as $k=>$v)
		{
		   $langs[] = $v->lang_code; 	
			
		}
		}
		
			
			
			
			if (count($langs)>1)
			if (!empty($langs))
			foreach ($langs as $lcode)
			{
									
					$ml = str_replace('-', '_', $lcode);
					$ml = strtolower($ml); 
					foreach ($multilang as $key)
					{
						$kkey = $key.'_'.$ml; 
						if (empty($nm[$file])) $nm[$file] = new stdClass(); 
						$nm[$file]->$kkey = ''; 
						
					}
					
					
			}
		}
		
		
		
		
	/*
		if ($file === 'google_tag_manager')
		{
			var_dump($nm); die(); 
			var_dump($test); die(); 
		}
		*/
		
		foreach ($data as $k=>$vl)
		{
			$test->setValue($k, $file, $vl); 
			if (isset($nm[$file]))
			if (isset($nm[$file]->$k)) $nm[$file]->$k = $vl; 
			
		}
		
		$fieldSets = $test->getFieldsets();
		
		
		
		
		$test = OPCparametersJForm::render($test); 
		
		
		
	   }
	   
	    if ($data_config === 'tracking_config') {
	   /* general section */
		$gpath = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'forms'.DIRECTORY_SEPARATOR.'general.xml'; 
	   
	   $xml = file_get_contents($gpath); 
	   $xml = str_replace('{general}', $file, $xml); 

	   
	   $general = JForm::getInstance($file.'_general', $xml, array(),true);
	   $general->removeField('run_only_for_affiliate', $file); 
	   
	   // cart tracking
	   if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.$file.'_cart.php'))
	    {
		  $general->removeField('run_at_cart_view_event', $file); 
		}

	   if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.$file.'_category.php'))
	    {
		  $general->removeField('run_at_category_view_event', $file); 
		}
		
	if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.$file.'_impr.php'))
	    {
		  $general->removeField('run_at_impr_view_event', $file); 
		}

	   if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.$file.'_search.php'))
	    {
		  $general->removeField('run_at_search_view_event', $file); 
		}

		
		// product view tracking
//		
if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.$file.'_product.php'))
	    {
		  $general->removeField('run_at_product_view_event', $file); 
		}
		
		
		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.$file.'_first.php') && (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.$file.'_last.php') ))
	    {
		  $general->removeField('run_always', $file); 
		}


		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.$file.'_category.php'))
	    {
		  $general->removeField('run_at_category_view_event', $file); 
		}
		
		
		
		// cart tracking: 
		 if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.$file.'_cartadd.php'))
	    {
		  $general->removeField('run_at_cartadd_view_event', $file); 
		}
	   
		/* general section end */
	   
	  
	   //$test->bind($data); 
		foreach ($data as $k=>$vl)
		{
		  if ($general->setValue($k, $file, $vl)===false)
		    {
			
			}
		}
		
		
		
		
		$fieldSets = $general->getFieldsets();
		
		
		$generalf = OPCparametersJForm::render($general); 
		
	   
	   $ret[$file]['general'] = $generalf; 
	   }
	   $ret[$file]['params'] = $test;
		if (empty($title))
	   $ret[$file]['title'] = $file.'.php'; 
	    else $ret[$file]['title'] = (string)$title; 
		
		if (isset($nm[$file]))
		$ret[$file]['nm'] = $nm[$file]; 
	   
	    $ret[$file]['description'] = (string)$description; 
		
		
	 }
	 return $ret; 
	}
}
