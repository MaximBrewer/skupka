<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Virtuemart Page Cache Plugin 
 *
 * @package		vmcache
 * @subpackage	System.vmcache
 */
$GLOBALS['vmcachestart'] = microtime(true); 

class plgSystemVmcache_last extends JPlugin
{

	var $_cache = null;
    var $_ref = null; 
	var $_stop = false; 
	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param	array	$config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		if (class_exists('plgSystemVmcache'))
		{
		  if (isset(plgSystemVmcache::$_cache))
		  {
		  $this->_cache =& plgSystemVmcache::$_cache; 
		  $this->_ref =& plgSystemVmcache::getInstance(); 
		  $this->_stop = false; 
		  	    
		   if ($this->_ref->setCaching())
		   {
		     $arr = $this->_ref->getDimensions(); 
		     $this->_ref->setDimensions($arr); 
		   }
		  }
		  else
		  $this->_stop = true; 

		}
		else 
		$this->_stop = true; 
		
		

		
	}
	
	function onExtensionAfterSave($tes2, $test)
	{
	  
	  if (empty($test)) return; 
	  if (!is_object($test)) return; 
	  
	 if (!(($test->element === 'vmcache_last') || ($test->element === 'vmcache')))
	  return; 
	  if ($test->folder !== 'system') return; 
	  
	 
	  
	  $isEnabled = $test->enabled; 
	  // update other plugin
	  $db = JFactory::getDBO(); 
	  
	  $q = 'select (min(ordering)-100) as min from #__extensions where 1'; 
	  
	  $db->setQuery($q); 
	  $min = (int)$db->loadResult(); 
	  
	  $q = 'select (max(ordering)+100) as min from #__extensions where 1'; 
	  $db->setQuery($q); 
	  $max = (int)$db->loadResult(); 
	  
	  // update other plugin
	 
	  $q = 'update #__extensions set enabled = '.(int)$test->enabled.', ordering = '.$min.' where element = "vmcache" and folder = "system" limit 1'; 
	  $db->setQuery($q); 
	  $db->query($q); 
	  $e = $db->getErrorMsg(); if (!empty($e)) { die($e); }

	  
	  $db = JFactory::getDBO(); 
	  $q = 'update #__extensions set enabled = '.(int)$test->enabled.', ordering = '.$max.' where element = "vmcache_last" and folder = "system" limit 1'; 
	  $db->setQuery($q); 
	  $db->query($q); 
	  $e = $db->getErrorMsg(); if (!empty($e)) { die($e); }
	 
	  
	  
	  $e = $db->getErrorMsg(); if (!empty($e)) { die($e); }
	 

	  
	  
	}
	
	
	function onAfterRoute()
	{
	    if ($this->_stop) return; 
		if (empty($this->_ref)) return; 
	    if (!$this->_ref->setCaching()) return; 
	}
	
	
	function onAfterRender()
	{
		
		
	    if (!JFactory::getApplication()->isSite()) return; 
		
		if (empty($this->_ref)) return; 
	    if ($this->_ref->https_override)
		 {
		     $buffer = JResponse::getBody();
			 $buffer = str_replace('src="http://', 'src="//', $buffer); 
			 JResponse::setBody($buffer);
			 
			 
		 }
		 
		 $a1 = function_exists('fastcgi_finish_request'); 
		 $a2 = function_exists('register_shutdown_function'); 
		 
		 // url caching first: 
		 if (defined('SHM_TYPE') && SHM_TYPE === false)
		  {
		     if (((!$this->_ref->params->get('useasync', false)) || (!$a1)) && (!$a2))
		{
		
			$this->_ref->_storeUrlCache(); 
			return; 
			
		
		}
		
		  }
		  
		  
		 // end url caching
		 
		 
		if ($this->_stop) return; 
		
		if (!$this->_ref->setCaching(true)) return; 
		
				
			
		
		$a1 = function_exists('fastcgi_finish_request'); 
		$a2 = function_exists('register_shutdown_function'); 
		
		if (((!$this->_ref->params->get('useasync', false)) || (!$a1)) && (!$a2))
		{
		
			$this->_ref->_storeCache(); 
			return; 
			
		
		}
		else
		{
		   register_shutdown_function(array($this->_ref, 'callAtShutDown')); 
		}
	}
}
