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

class GoogleHelper
{
	public static $cache; 
	static $config; 
	
	function __construct() {
		if ((!class_exists('xmlCategory')) && file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'xmlcategory.php')) {
		  require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'xmlcategory.php'); 
		}
		else
		{
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'vmaddtabs'.DIRECTORY_SEPARATOR.'xmlcategory.php'); 
		}

	}
	
	function getData($useCache=true)
	{
	  
	  
	  return $this->xmlexportData($useCache); 
	  
	}
	
	function xmlexportData($useCache=true)
	{
	

	  
	  jimport( 'joomla.filesystem.file' );
	  jimport( 'joomla.filesystem.folder' );
	  
	  
	  $entity = 'google_cats'; 
	  $cache_dir = JPATH_SITE.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR; 
	  $cache_file = $cache_dir.'xmlcache_'.$entity.'.php'; 
	  
	  if (!file_exists($cache_dir))
	   {
	     JFolder::create($cache_dir); 
	   }
	  
	  if ($useCache)
	  if (file_exists($cache_file))
	    {
		   include($cache_file); 
		   if (isset($return)) return $return; 
		}
	  
	 
	  
	  
	  
	  
	  
	  
	  $url = $this->getPairingUrl(); 
	 
	  
	  
	  
	  $name = $this->getPairingName(); 
	  
	   
	   
	
	 
	 
	 
	  $res = self::fetchUrl($url); 
	  
	  
	  
	  
	  if (empty($res)) return; 
	  
	   $converted = array(); 
	  
	  
	  $data = $this->processPairingData($res, $converted); 
	  
	 
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
	  $data = '<?php defined( \'_JEXEC\' ) or die( \'Restricted access\' );'."\n"; 
	  $data .= ' $return = '.var_export($converted, true);
	  $data .= '; '."\n"; 
	  JFile::write($cache_file, $data); 
	  
	  
	  
	  
	  return $converted; 
	 
	  
	}
	
	function processPairingData($xml, &$converted)
  {
  
  $lines = explode("\n", $xml); 
  
  
  $ret = array();  
  foreach ($lines as $k=>$line)
   {     
     //get rid of the rest characters
     $lines[$k] = str_replace("\r", '', $line); 
	 if (substr($line, 0, 1)==='#') continue; 
	 $hash = $k;  
	 $ret[$hash] = $line; 
   }
   
   
   
  
   $converted = $ret; 
   return; 


	   
  }
	
		public static function fetchUrl($url, $XPost='')
	{
	
	 if (!function_exists('curl_init'))
	 {
	  return file_get_contents($url); 
	 
	 }
		
	 $ch = curl_init(); 
	 
//	 curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
	 curl_setopt($ch, CURLOPT_URL,$url); // set url to post to
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
	 curl_setopt($ch, CURLOPT_TIMEOUT, 4000); // times out after 4s
     curl_setopt($ch, CURLOPT_POSTFIELDS, $XPost); // add POST fields
     if (!empty($XPost))
	 curl_setopt($ch, CURLOPT_POST, 1); 
	 else
	 curl_setopt($ch, CURLOPT_POST, 0); 
     curl_setopt($ch, CURLOPT_ENCODING , "gzip");
	 $result = curl_exec($ch);   
	
    
    
    if ( curl_errno($ch) ) {      
	    
	    
		@curl_close($ch);
		return false; 
    } else {
        $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
        switch($returnCode){
            case 404:
			    @curl_close($ch);
                return false; 
                break;
            case 200:
        	break;
            default:
				 @curl_close($ch);
            	return false; 
                break;
        }
    }
    
    @curl_close($ch);
    
  
    return $result;   
    
    

	}
  function getPairingName()
  {
    $lang = JFactory::getLanguage()->getTag(); 
	$lang = str_replace('-', '_', $lang); 
	return 'googlerss_'.$lang; 
  }
	
  function getPairingUrl()
  {
  
    $url = 'http://www.google.com/basepages/producttype/taxonomy.{lang}.txt'; 
	
	$lang = JFactory::getLanguage()->getTag(); 	
	
	
	return str_replace('{lang}', $lang, $url); 
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
	 public static function store($config_name, $config_sub, $config_ref=0, $data)
  {
    if (!self::tableExists('google_config')) {
	
	  self::createTable(); 
	}
     $db = JFactory::getDBO(); 
	
	if (isset(self::$config[$config_name]))
	unset(self::$config[$config_name]); 
	
	 
	
	 
	   $datains = $db->escape(json_encode($data)); 
	  	 $q = "insert into `#__google_config` (`id`, `config_name`, `config_subname`, `config_ref`, `value`) values (NULL, '".$db->escape($config_name)."', '".$db->escape($config_sub)."', '".(int)$config_ref."', '".$datains."')  ON DUPLICATE KEY UPDATE value = '".$datains."' ";  

	 
	
	 
     $db->setQuery($q); 
	 $db->query(); 

	
  }
	function storeData($data)
	{
	    jimport( 'joomla.filesystem.file' );
		
	   
		$entity = 'google_cats'; 
		
		
		$vmcat = $data['vmcat']; 
		if (empty($vmcat)) return '';
		
		$store = new stdClass(); 
		
		$store->id = $data['refcat']; 
		$store->txt = $data['reftxt']; 
		
		
		self::store('xmlexport_pairing', $entity, $vmcat, $store); 
		return ''; 
	}
	
	function renderOption($entity, $vmCat, $refCat, $txt)
	{
	  $data = array(); 
	  $data['entity'] = $entity; 
	  $data['vmcat'] = $vmCat; 
	  $data['refcat'] = $refCat;
      $data['reftxt'] = $txt; 	  
	  
	  $json = urlencode(json_encode($data)); 
	  
	  
	  
	  $default = new stdClass(); 
	  $res = self::getValue('xmlexport_pairing', $entity, $vmCat, $default); 
	  
	  
	  
	  $ret = '<option value="'.$refCat.'" '; 
	  
	  if (!empty($res))
	  if (isset($res->id))
	  if ($res->id == $refCat)
	  $ret .= ' selected="selected" '; 
	  
	  $ret .= ' data="'.$json.'">'.$txt.'</option>'; 
	  
	  return $ret; 
	  
	}
	 static function tableExists($table='google_config')
  {
   
   
   $db = JFactory::getDBO();
   $prefix = $db->getPrefix();
   $table = str_replace('#__', '', $table); 
   $table = str_replace($prefix, '', $table); 
   $table = $db->getPrefix().$table; 
   
   
   
   
   
 
 
   $q = "SHOW TABLES LIKE '".$table."'";
	   $db->setQuery($q);
	   $r = $db->loadResult();
	   
	   
	   
	   if (!empty($r)) 
	    {
		
		return true;
		}
		
      return false;
  }
  
  

  public static function createTable()
  {
	  $q = ' CREATE TABLE IF NOT EXISTS `#__google_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_name` varchar(100) NOT NULL,
  `config_subname` varchar(100) NOT NULL,
  `config_ref` int(11) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_name` (`config_name`,`config_subname`,`config_ref`),
  KEY `config_name_2` (`config_name`,`config_subname`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=85 ; 
'; 
	$db = JFactory::getDBO(); 
    $db->setQuery($q); 
	$db->query(); 
	$e = $db->getErrorMsg(); 
	
  }
  
	 public static function getValue($config_name, $config_sub, $config_ref, $default, $checklang=false)
  { 
   if (!is_string($config_sub))
    {
	  $config_sub = ''; 
	}
   if (!self::tableExists('google_config')) return $default; 
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
	    // fill the cache: 
		$db = JFactory::getDBO(); 
		$q = "select * from #__google_config where config_name = '".$db->escape($config_name)."'"; 
		$db->setQuery($q); 
		$results = $db->loadAssocList(); 

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
