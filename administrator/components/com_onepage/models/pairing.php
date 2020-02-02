<?php
/**
 * 
 *
 * @package One Page Checkout for VirtueMart 2
 * @subpackage opc
 * @author stAn
 * @author RuposTel s.r.o.
 * @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * One Page checkout is free software released under GNU/GPL and uses some code from VirtueMart
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 */


// no direct access
defined('_JEXEC') or die;

class JModelPairing extends OPCModel
{
	function __construct() {
		parent::__construct();
		if (!class_exists('xmlCategory')) {
		  require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'xmlcategory.php'); 
		}
	}
	
	function getData($entity='', $type='',  $asset='')
	{
		
	   if (empty($type)) {
	      //expects: "index.php?option=com_onepage&view=pairing&asset=virtuemart_category_id&entity=filename&type=xmlexport
	      $type = JRequest::getVar('type', 'xmlexport');
	   }
	  if ($type=='xmlexport')
	  return $this->xmlexportData($entity, $asset); 
	  
	}
	
	function xmlexportData($entity='',  $asset='')
	{
	
	  if (empty($entity)) {
	  $entity = JRequest::getVar('entity', ''); 
	  }
	  if (empty($asset)) {
	   $ref_id = JRequest::getVar('asset', 'virtuemart_category_id'); 
	  }
	  else $ref_id = $asset; 
	
	  jimport( 'joomla.filesystem.file' );
	  jimport( 'joomla.filesystem.folder' );
	  
	  $entity = JFile::makeSafe($entity); 
	  
	  $cache_dir = JPATH_SITE.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR; 
	  $cache_file = $cache_dir.'xmlcache_'.$entity.'.php'; 
	  
	  if (!file_exists($cache_dir))
	   {
	     JFolder::create($cache_dir); 
	   }
	  
	  
	   
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'xmlexport.php'); 
	  
	  
	  
	  $xc = new VirtueMartControllerXmlexport(); 
	  $class = $xc->getClass($entity); 
	  if (empty($class)) return; 
	  
	  if ((isset($class->general)) && (empty($class->general->xml_disable_categorycache)))
	  if (file_exists($cache_file))
	    {
		   include($cache_file); 
		  if (isset($return)) return $return; 
		}
	  
	  
	
	  
	   
	  
	  if (method_exists($class, 'getPairingUrl'))
	  {
	    $url = $class->getPairingUrl(); 
	 
	  }
	  else
	   {
	      if (isset($class->xml->category_pairing_url))
		   {
		      $url = (string)$class->xml->category_pairing_url; 
		   }
		   else 
		   return; 
	   }
	  
	 
	  if (method_exists($class, 'getPairingName'))
	  $name = $class->getPairingName(); 
	  else
	   {
	     if (isset($class->xml->category_pairing_name))
		  $name = (string)$class->xml->category_pairing_name; 
		 else 
		  $name = $entity; 
	   }
	   
	   
	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
	 
	 
	 
	  $res = OPCloader::fetchUrl($url); 
	  
	  
	  if (empty($res)) return; 
	  
	   $converted = array(); 
	  
	  if (method_exists($class, 'processPairingData'))
	  $data = $class->processPairingData($res, $converted); 
	  else return; 
	 
	  if (empty($converted))
	  {
	  foreach ($data->children as $topcat)
	   {
	      $converted[$topcat->id] = $topcat->txt; 
		  
		  if (!empty($topcat->children))
		   {
		     $this->recurseCat($topcat->children, $converted[$topcat->id], $converted); 
		   }
		  
		  
	   }
	  }
	  
	  if ((isset($class->general)) && (empty($class->general->xml_disable_categorycache))) {
	  $data = '<?php defined( \'_JEXEC\' ) or die( \'Restricted access\' );'."\n"; 
	  $data .= ' $return = '.var_export($converted, true);
	  $data .= '; '."\n"; 
	  JFile::write($cache_file, $data); 
	  }
	  
	  return $converted; 
	 
	  
	}
	
	function getVmCats()
	{
	  
	   if(!class_exists('VirtueMartModelConfig'))require(JPATH_VM_ADMINISTRATOR .'models/config.php');

		if (!class_exists('VmHTML'))
			require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'html.php');

		if (!class_exists ('shopFunctionsF'))
			require(JPATH_VM_SITE .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'shopfunctionsf.php');
		
		if (!class_exists('VirtueMartModelCategory'))
		require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'category.php'); 
		
		JRequest::setVar('limit', 9999999); 
		JRequest::setVar('limitstart', 0); 
		
		
		$model = new VirtueMartModelCategory(); 
		$model->_limitstart = 0; 
		$model->_limit = 999999; 
		$model->_noLimit = true; 
		
		$categories = $model->getCategoryTree(0,0,false,'');
		
		
		$re = array(); 
		foreach ($categories as $cat)
		 {  
		   $re[$cat->virtuemart_category_id] = $cat; 
		 }
		 
		 $all = array();

		 
		 foreach ($categories as $cat)
		  {
		  
		 
		  
		     $all[$cat->virtuemart_category_id] =& $cat->category_name; 
			 $current =& $all[$cat->virtuemart_category_id]; 
			 if (!empty($cat->category_parent_id ))
			 if (isset($re[$cat->category_parent_id]))
			  {
			     $this->recurseVmCat($re[$cat->category_parent_id], $current, $all, $re); 
			  }
		  
		  }
		  
		return $all; 
		
	}
	
	function storeData($data)
	{
	    jimport( 'joomla.filesystem.file' );
		
	   	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		
		$entity = JFile::makeSafe($data['entity']); 
		if (empty($entity)) return JText::_('COM_ONEPAGE_ERROR_SAVING_CONFIGURATION'); 
		
		$vmcat = $data['vmcat']; 
		if (empty($vmcat)) return JText::_('COM_ONEPAGE_ERROR_SAVING_CONFIGURATION'); 
		
		$store = new stdClass(); 
		
		$store->id = $data['refcat']; 
		$store->txt = $data['reftxt']; 
		
		
		OPCconfig::store('xmlexport_pairing', $entity, $vmcat, $store); 
		return JText::_('COM_ONEPAGE_OK'); 
	}
	
	function renderOption($entity, $vmCat, $refCat, $txt)
	{
	  $data = array(); 
	  $data['entity'] = $entity; 
	  $data['vmcat'] = $vmCat; 
	  $data['refcat'] = $refCat;
      $data['reftxt'] = $txt; 	  
	  
	  $json = urlencode(json_encode($data)); 
	  
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	  
	  $default = new stdClass(); 
	  $res = OPCconfig::getValue('xmlexport_pairing', $entity, $vmCat, $default); 
	  
	  
	  
	  $ret = '<option value="'.$refCat.'" '; 
	  
	  if (!empty($res))
	  if (isset($res->id))
	  if ($res->id == $refCat)
	  $ret .= ' selected="selected" '; 
	  
	  $ret .= ' data="'.$json.'">'.$txt.'</option>'; 
	  
	  return $ret; 
	  
	}
	
	function recurseVmCat(&$parent, &$txt, &$all, &$allcats)
	 {
	    $txt = $parent->category_name.' > '.$txt; 
		$all[$parent->virtuemart_category_id] =& $parent->category_name; 
		$current =& $all[$parent->virtuemart_category_id]; 
		if (!empty($parent->virtuemart_parent_id))
		if (isset($allcats[$parent->virtuemart_parent_id]))
		 {
		   $this->recurseVmCat($allcats[$parent->virtuemart_parent_id], $current,  $all, $allcats); 
		 }
	 }
	
	function recurseCat(&$children, &$topcat, &$all)
	 {
	    foreach ($children as $child)
		 {
		    $all[$child->id] = $topcat.' > '.$child->txt; 
			if (!empty($child->children))
			 {
			   $this->recurseCat($child->children, $all[$child->id], $all); 
			 }
		 }
	 }

}
