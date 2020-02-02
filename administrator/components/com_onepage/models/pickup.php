<?php
/**
 * @version		$Id: cache.php 21518 2011-06-10 21:38:12Z chdemko $
 * @package		Joomla.Administrator
 * @subpackage	com_cache
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Cache Model
 *
 * @package		Joomla.Administrator
 * @subpackage	com_cache
 * @since		1.6
 */
class JModelPickup extends OPCModel
{

  function store()
  {
  
     require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	 if (!OPCmini::tableExists('virtuemart_shipment_plg_pickup_or_free_conf'))
	 {
	   $db = JFactory::getDBO(); 
	   $q = '
	
CREATE TABLE IF NOT EXISTS `#__virtuemart_shipment_plg_pickup_or_free_conf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `route` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;'; 
	$db->setQuery($q); 
	$db->query(); 
	$e = $db->getErrorMsg(); if (!empty($e)) return $e; 
	 }
    require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
/*	
if (!OPCJ3)
	{
	 require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'opcparameters.php'); 
	}
	else
	{

	}
*/
	   require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'jformrender.php'); 
	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		 $config = new JModelConfig(); 
		 $config->loadVmConfig(); 
		 $files = $config->getPhpTrackingThemes();
		 $statuses = $config->getOrderStatuses();
		 $data = JRequest::get('post');
		  jimport('joomla.filesystem.file');
     
	
	   
	   
	 return;
  }
 
  
  
  function getRoutes()
    { 
	
	 

	 $ret = array(); 
	 $file= JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmshipment'.DIRECTORY_SEPARATOR.'pickup_or_free'.DIRECTORY_SEPARATOR.'pickup_or_free.xml'; 
	 
	 $db = JFactory::getDBO(); 
	 $q = "select shipment_params from #__virtuemart_shipmentmethods where shipment_element = 'pickup_or_free' and published = 1 limit 0,1"; 
	 $db->setQuery($q); 
	 $json = $db->loadResult(); 
	 
	 
	  $params = new stdClass(); 
	  $thisparams = explode('|', $json);
	 
	  foreach ($thisparams as $item) {
                                                $item = explode('=', $item);
                                                $key = $item[0];
                                                unset($item[0]);
                                                $item = implode('=', $item);
                                                if (!empty($item) ) 
												{
												
												$params->$key =  json_decode($item);
												}
	 
	
		}
		$a = explode(';', $params->routes); 					  					  
		return $a; 

		
	}
}
