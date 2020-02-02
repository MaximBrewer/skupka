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
defined('_JEXEC') or die;
class OPCconfig {
 static $config; 
 static $override; 
 public static function set($var, $val) {
	 if (empty(self::$override)) self::$override = array(); 
	 self::$override[$var] = $val; 
 }
 public static function get($var, $default=false)
  {
	 if ((!empty(self::$override)) && (isset(self::$override[$var]))) return self::$override[$var]; 
	  
    if (isset(OPCconfig::$config[$var])) return OPCconfig::$config[$var]; 
	if (defined('OPCCONFIGLOADED')) {
		if (isset(OPCconfig::$config[$var]))
		{
			$default = OPCconfig::$config[$var]; 
			return OPCconfig::$config[$var]; 
		}
		
		return $default; 
	}
    
	include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
	define('OPCCONFIGLOADED', 1); 
	
	 
		
		require_once (JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		
		
	
	$selected_template = OPCmini::getSelectedTemplate();  
	
	
	   $f = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$selected_template.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'onepage.cfg.php'; 
	   
	   if (file_exists($f))
	   {
		   include($f); 
	   }
	
	$arr = get_defined_vars(); 
	foreach ($arr as $key=>$val)
	 {
	   OPCconfig::$config[$key] = $val;   
	 }
	
	
	
	 if (isset($arr[$var])) 
	 {
		 $default = $arr[$var]; 
	     return $arr[$var]; 
	 }
	
	OPCconfig::$config[$var] = $default; 
	
	return $default; 
	// OPCconfig::get is only used for non-database settings
	// for database related stuff use OPCconfig::getValue
	// return self::getValue($var, '', 0, $default); 
	

  }
  
  
  public static function copy($config_name, $config_sub_from, $config_sub_to)
  {
	  $db = JFactory::getDBO(); 
	  $q = "select `config_ref`, `value` from `#__onepage_config` where `config_name` = '".$db->escape($config_name)."' "; 
      $db->setQuery($q); 
	  $res = $db->loadAssocList(); 	  
	  if (!empty($res))
	  foreach ($res as $row)
	  {
		  $config_name = $config_name; 
		  $config_sub = $config_sub_to; 
		  $config_ref = $row['config_ref']; 
		  $value = $row['value']; 
		  
		     $datains = $db->escape($value); 
	  	 $q = "insert into `#__onepage_config` (`id`, `config_name`, `config_subname`, `config_ref`, `value`) values (NULL, '".$db->escape($config_name)."', '".$db->escape($config_sub)."', '".(int)$config_ref."', '".$datains."')  ON DUPLICATE KEY UPDATE value = '".$datains."' ";  

	 
	
	 
     $db->setQuery($q); 
	 $db->query(); 

	  }
	  
  }
  
  public static function tableExists()
  {
    static $ret; if (isset($ret)) return $ret; 
	
	require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	$ret = OPCmini::tableExists('onepage_config'); 
	return $ret; 
  }
  public static function clearConfig($config_name, $config_sub='', $config_ref=0)
  {
     if (!self::tableExists()) return; 
	 
     $db = JFactory::getDBO(); 
	 $q = "delete from #__onepage_config where config_name LIKE '".$db->escape($config_name)."' "; 
	 if (!empty($config_sub))
	 $q .= " and config_subname LIKE '".$db->escape($config_sub)."' "; 
	 if (!empty($config_ref))
	 $q .= " and config_ref = ".(int)$config_ref; 
	 $db->setQuery($q); 
	 $db->query(); 
	 
	 
  }
  
  //original: 
  public static function getValueNoCache($config_name, $config_sub, $config_ref, $default, $checklang=false)
  { 
  if (!self::tableExists()) return $default; 


	if ($checklang)
	 {
	   $config_sub_orig = $config_sub; 
	   if (is_bool($checklang))
	   $config_sub .= JFactory::getLanguage()->getTag(); 
	   else 
	   $config_sub .= $checklang; 
	 }
	
    $db = JFactory::getDBO(); 
	if (!defined('OPCJ3') || (OPCJ3))
	{
	      $q = "select `value` from #__onepage_config where config_name = '".$db->escape($config_name)."' and config_subname = '".$db->escape($config_sub)."' and config_ref = ".(int)$config_ref." limit 0,1"; 

	}
	else
	{
    $q = "select `value` from #__onepage_config where config_name = '".$db->escape($config_name)."' and config_subname = '".$db->escape($config_sub)."' and config_ref = ".(int)$config_ref." limit 0,1"; 
	}
	
	$db->setQuery($q); 
	
	$res = $db->loadResult(); 
	
	
	
	if (is_null($res)) 
	{
	  // default language query: 
	  if ($checklang)
	  {
	  $q = "select value from #__onepage_config where config_name = '".$db->escape($config_name)."' and config_subname = '".$db->escape($config_sub_orig)."' and config_ref = ".(int)$config_ref." limit 0,1"; 
	  $db->setQuery($q); 
	  $res = $db->loadResult(); 
	  }
	  
	  if (!isset($default)) $default = new stdClass(); 
	  
	  if (is_null($res))
	  return $default; 
	}
	
	return self::translateValue($res, $default); 
	
	
	
	
  }
  
  // returns only objects, associative arrays are not supported here
  public static function translateValue($res, $default)
  {
	  
	  
	  if (is_null($res)) return $default; 
	  if ($res === 'true') return true; 
	  if ($res === 'false') return false; 
	  
	  
	  
	  $r = @json_decode($res); 
	  

	  
	if ((empty($r) || ($res=='[]')) && (!empty($default))) 
	{
		
		return $default; 
	}
	else
	if ((empty($r) || ($res=='[]'))) 
	{
		
			  
		
	 if (is_bool($default)) return false; 
	 if (is_array($default)) return array(); 
	 if (empty($default)) return $r; 
	 //$r = new stdClass(); 
	}
	
	
	
	if (empty($r)) {
		return $default; 
	}
	else 
	{
		return $r; 
	}
	
	
	
	return $r; 
	  
  } 
  public static function getArray($config_name, $config_sub='', $config_ref='', $default) 
  {
	  $db = JFactory::getDBO(); 
	  $q = "select * from `#__onepage_config` where `config_name` = '".$db->escape($config_name)."' "; 
	  if (!empty($config_sub))
		  $q .= " and `config_subname` =  '".$db->escape($config_sub)."' "; 
	  if (!empty($config_ref))
		  $q .= " and `config_ref` =  '".$db->escape($config_ref)."' "; 
	  
	  $db->setQuery($q); 
	  $res = $db->loadAssocList(); 
	  
	 
	  if (empty($res)) return $default; 
	  if (!is_array($res)) return $default; 
	  $ret = array();  
	  foreach ($res as $k => $row)
	  {
		  $row = (array)$row; // get rid of any mysql row reference if possible
		  $row['value'] = self::translateValue($row['value'], $default); 
		  $ret[$k] = $row; 
	  }
	 
	  return $ret; 
  }
  public static function getValues($config_name) {
	  
	  if (!empty(self::$config[$config_name])) {
	    {
		  return self::$config[$config_name]; 
		}
		/*
		elseif ((!empty($config_sub)) && (is_null($config_ref)))  {
			if (!empty(self::$config[$config_name][$config_sub]))
			{
			  return self::$config[$config_name][$config_sub]; 
			}
			
		}
		elseif ((!empty($config_sub)) && (!is_null($config_ref))) {
			if (!empty(self::$config[$config_name][$config_sub][$config_ref])) {
			  return self::$config[$config_name][$config_sub][$config_ref]; 
			}				
		}
		*/
	  }
         $db = JFactory::getDBO(); 
		$q = "select * from #__onepage_config where config_name = '".$db->escape($config_name)."'"; 
		$db->setQuery($q); 
		$results = $db->loadAssocList(); 
		if (empty($results)) $results = array(); 
		$results = (array)$results; 
		return $results; 
  }
  public static function getValue($config_name, $config_sub='', $config_ref=0, $default='', $checklang=false)
  { 
   if (!is_string($config_sub))
    {
	  $config_sub = ''; 
	}
   if (!self::tableExists()) return $default; 
    if ($checklang)
	 {
	   $config_sub_orig = $config_sub; 
	   if (is_bool($checklang))
	   $config_sub .= JFactory::getLanguage()->getTag(); 
	   else 
	   $config_sub .= $checklang; 
	 }
	 
	 $res = null; 
	 
	if (!isset(self::$config[$config_name]))
	 {
		$results = self::getValues($config_name, $config_sub, $config_ref); 
	    // fill the cache: 
		

		if (!empty($results))
		 {
		 
		    foreach ($results as $k=>$row)
			 {
			
			    //init array: 
			    if (!isset(self::$config[$config_name][$row['config_subname']])) self::$config[$config_name][$row['config_subname']] = array(); 
				
			    self::$config[$config_name][$row['config_subname']][$row['config_ref']] = $row['value']; 
			 }
		 }
		 
		 
	 }
	 
	   if (isset(self::$config[$config_name]))
	   if (isset(self::$config[$config_name][$config_sub]))
	   if (isset(self::$config[$config_name][$config_sub][$config_ref]))
	   $res = self::$config[$config_name][$config_sub][$config_ref]; 
	 
	 
  
	if (is_null($res)) 
	{
	  // default language query: 
	  if ($checklang)
	  {
	  
	  
	   if (isset(self::$config[$config_name]))
	   if (isset(self::$config[$config_name][$config_sub_orig]))
	   if (isset(self::$config[$config_name][$config_sub_orig][$config_ref]))
	   $res = self::$config[$config_name][$config_sub_orig][$config_ref]; 
	  
	  
	  }
	  
	  if (!isset($default)) $default = new stdClass(); 
	  
	  if (is_null($res))
	  return $default; 
	}
	
	
	if ($res === 'true') return true; 
	if ($res === 'false') return false; 
	
	$r = @json_decode($res); 
	
	
		

	
	if ((empty($r) || ($res=='[]')) && (!empty($default))) return $default; 
	else
	if ((empty($r) || ($res=='[]'))) 
	{
	 if (is_bool($default)) return false; 
	 if (is_array($default)) return array(); 
	 //$r = new stdClass(); 
	}
	
	if (empty($r)) return $default; 
	else return $r; 
	
	return $r; 
  }


  
  public static function store($config_name, $config_sub, $config_ref=0, $data)
  {
    if (!self::tableExists()) return false; 
     $db = JFactory::getDBO(); 
	
	if (isset(self::$config[$config_name]))
	unset(self::$config[$config_name]); 
	
	 
	
	 
	   $datains = $db->escape(json_encode($data)); 
	  	 $q = "insert into `#__onepage_config` (`id`, `config_name`, `config_subname`, `config_ref`, `value`) values (NULL, '".$db->escape($config_name)."', '".$db->escape($config_sub)."', '".(int)$config_ref."', '".$datains."')  ON DUPLICATE KEY UPDATE value = '".$datains."' ";  

	 
	
	 
     $db->setQuery($q); 
	 $db->query(); 

	
  }
  
  public static function buildObject($post, $key='')
  {
    $ret = new stdClass(); 
	if (!empty($key))
	{
     foreach ($post[$key] as $key2->$val)
	 {
	   $ret->$key2 = $val; 
	 }
	  
	}
	else
    foreach ($post as $key=>$val)
	{
	  $ret->$key = $val; 
	}
	return $ret; 
  }
}