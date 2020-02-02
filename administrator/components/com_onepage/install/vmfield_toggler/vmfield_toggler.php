<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;




class plgSystemVmfield_toggler extends JPlugin {
	function __construct(& $subject, $config)
	{
		JFactory::getLanguage()->load('plg_system_vmfield_toggler', __DIR__); 
		JFactory::getLanguage()->load('plg_system_vmfield_toggler', JPATH_SITE); 
		
		parent::__construct($subject, $config);
		

		//Set the language in the class
		
		
		
	}
	public function opcGetFieldPaths(&$ftypes) {
	
	require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 
	$ftypes = OPCUserFields::getUserfTypes(); 
		
	
	}
	
	
	public function opcStoreFieldConfig($data) {
	    if (php_sapi_name() === 'cli') {
			return; 
		}
		
		  $ftypes = array(); 
		  $this->opcGetFieldPaths($ftypes); 
		  if (!empty($ftypes)) {
		   foreach ($ftypes as $f)
		   {
			   $changed = false; 
			   if (!empty($f->hash)) {
			     if (!empty($data['field_'.$f->hash])) {
				   $f->fields = new stdClass(); 
				   foreach ($data['field_'.$f->hash] as $fn) {
				      $f->fields->{$fn} = $fn; 
					  $changed = true; 
				   }
				 }
			    if ($changed)
			    OPCconfig::store('vm_userfields', $f->hash, 0, $f, false); 
			   }
			   
		   }
		  
		  }
	}
	
	public function plgVmOnGetUserfields($type, &$userFields) {
		
		if (php_sapi_name() === 'cli') {
			return; 
		}
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 
		$config = OPCUserFields::storeFieldPaths(); 
		
		
		
		if ((!empty($config)) && (!empty($config->enabled))) {
		  if (!empty($config->fields)) {
		  foreach ($userFields as $k=>$f)
		  foreach ($config->fields as $fn)
		  {
			if (is_object($f)) {
			if ($f->name === $fn) {
			   unset($userFields[$k]); continue; 
			}				
			}
			else
				if (is_array($f)) {
				if ($f['name'] === $fn) {
					unset($userFields[$k]); continue; 
				}				
				
				}
		  }
		  }
		}
		
		
	}
	
	
}