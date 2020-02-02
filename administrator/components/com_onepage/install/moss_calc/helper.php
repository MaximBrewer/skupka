<?php

if (!defined('_JEXEC'))
die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');

/**
 * Calculation plugin for MOSS and EU VAT
 *
 * @version $Id: 2.0.0
 * @package moss_calc for Virtuemart 3+
 * @subpackage Plugins
 * @author RuposTel.com
 * @copyright Copyright (C) RuposTel.com
 * @license commercial
 *
 *
 */
 
 class mossTaxHelper {
   var $ref; 
   var $calc_id; 
   var $calc_value; 
   public function __construct(&$ref)
   {
	   $this->ref =& $ref; 
   }
	static $qcache; 
	static $cache; 
	public function getResult($q)
	{
	  if (!isset(self::$qcache)) self::$qcache = array(); 
	  $hash = md5($q); 
	  if (isset(self::$qcache[$hash])) return self::$qcache[$hash]; 
	  $db = JFactory::getDBO(); 
	  try
	  {
	   $db->setQuery($q); 
	   $res = $db->loadResult(); 
	  }
	  catch (Exception $e)
	  {
		  JFactory::getApplication()->enqueueMessage('MOSS calc: Tables not installed !'); 
	  }
	  
	  if (is_null($res)) return null; 
	  
	  if (empty($res)) $res = 0; 
	  $res = floatval($res); 
	  
	  self::$qcache[$hash] = $res; 
	  $e = $db->getErrorMsg(); 
	  if (!empty($e)) { echo $e; die(); }
	  return $res; 
	}
	public static function getCountryByID($id, $what) {
		static $c; 
		if (isset($c[$id.'_'.$what])) return $c[$id.'_'.$what]; 
		
		if (!class_exists('ShopFunctions'))
		   require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'shopfunctions.php');
	   
		$ret = shopFunctions::getCountryByID($id, 'country_2_code'); 
	    $c[$id.'_'.$what] = $ret; 
		return $ret; 
	}
	public function loadAssocList($q) { 
	 return $this->getAssocList($q);
	}
	public function getAssocList($q)
	{
	  if (!isset(self::$qcache)) self::$qcache = array(); 
	  $hash = md5($q); 
	  if (isset(self::$qcache[$hash])) return self::$qcache[$hash]; 
	  $db = JFactory::getDBO(); 
	  try
	  {
	   $db->setQuery($q); 
	   $res = $db->loadAssocList(); 
	  }
	  catch (Exception $e)
	  {
		  
		  JFactory::getApplication()->enqueueMessage('MOSS calc: Tables not installed !'); 
	  }
	  
	  if (is_null($res)) return array(); 
	  
	  if (empty($res)) $res = array(); 
	  $res = (array)$res; 
	  
	  self::$qcache[$hash] = $res; 
	  $e = $db->getErrorMsg(); 
	  if (!empty($e)) { echo $e; die(); }
	  return $res; 
	}
	public function checkOthers()
	{
		
		$q = 'select `name` from #__extensions where `enabled` = 1 and `folder` = "vmcalculation" and `type` = "plugin" and `element` != "moss_calc"'; 
		$res = $this->loadAssocList($q); 
		$r2 = array(); 
		if (!empty($res)) {
			foreach ($res as $row) {
			//$r2[] = JText::_(strtoupper($row['name'])).' ('.$row['name'].')'; 
			$r2[] = $row['name']; 
			}
		}
		
		
		return $r2; 
		 
	}
	public function getCountryId($country_2_code)
	{
		$db = JFactory::getDBO(); 
		$country_2_code = trim($country_2_code); 
		$country_2_code = strtoupper($country_2_code); 
		$q = "select `virtuemart_country_id` from `#__virtuemart_countries` where `country_2_code` = '".$db->escape($country_2_code)."' limit 0,1"; 
		$cid = $this->getResult($q);
		$cid = (int)$cid; 
		return $cid; 
	}
	
    public function getTaxRate($virtuemart_country_id, $id, $default)
	{
		$default = (float)$default; 
	   //if (!$this->params->get('moss_mode', true)) return $default; 
	   $isMoss = $this->ref->_isMossMode($id); 
	   if (empty($isMoss)) return $default; 
		
	  if (empty($virtuemart_country_id)) $virtuemart_country_id = 0; 
	  $virtuemart_country_id = (int)$virtuemart_country_id; 
		
		if (empty($virtuemart_country_id)) 
		{
			
			
			return $default; 
		}
		
	  $id = (int)$id; 
	  $q = 'select `tax_rate` from '.$this->ref->taxTable.'_config where `virtuemart_country_id` = '.$virtuemart_country_id.' and virtuemart_calc_id = '.$id.' limit 0,1'; 
		  
		  $tax_rate = $this->getResult($q); 
		  if (is_null($tax_rate)) {
			  
			  
			 
			  return null; 
			  
		  }
		  $tax_rate = (float)$tax_rate; 
		 
		 
		 
		  return $tax_rate;
	}
	
	public function storeConfig(&$data)
	{
		
		$db = JFactory::getDBO();
		$table = new TableCalcs($db);
		$table->setUniqueName('calc_name');
		$table->setObligatoryKeys('calc_kind');
		$table->setLoggable();
		$table->setParameterable($this->ref->_xParamsP, $this->ref->_varsToPushParamP);
		$res = $table->bindChecknStore($data);
		
		
	
		
		
		
	}
	
	public static function insertArray($table, $fields, $def=array())
 {
	 if (empty($def)) {
		 $def = self::getColumns($table); 
	 }
	 foreach ($fields as $k=>$v)
	 {
		 if (!isset($def[$k])) unset($fields[$k]); 
	 }
	 
	 if (empty($fields)) return; 
	 
	 $dbvv = JFactory::getDBO(); 
	 $q = 'insert into `'.$table.'` (';
	 $keys = ''; 
	 $vals = ''; 
	 $i = 0; 
	 $c = count($fields); 
	 foreach ($fields as $key=>$val)
	 {
	  $keys .= '`'.$key.'`'; 
	  $i++;
	  
	  if ($val === 'NULL')
	   $vals .= 'NULL'; 
	  else 
	   $vals .= "'".$dbvv->escape($val)."'"; 

	  if ($i < $c) { 
	   $keys .= ', ';
	   $vals .= ', ';
	   }
	  
	 }
	 $q .= $keys.') values ('.$vals.') ';
	 $q .= ' ON DUPLICATE KEY UPDATE '; 
	 $u = false; 
	 foreach ($fields as $key=>$val)
	 {
	  if ($u) $q .= ','; 
	  $q .= '`'.$key.'` = '; 
	  $q .= "'".$dbvv->escape($val)."'"; 
	  $u = true; 
	 }
	 
	 $dbvv->setQuery($q); 
	 $dbvv->query();
	 return $q; 
 }
	
	public function loadGoogle($url)
	{
		if (defined('GDONE')) return; 
		define('GDONE', 1); 
		
		
		
		$x1 = stripos($url, '/d/'); 
		$x1 = $x1 + 3; 
		$x2 = stripos($url, '/', $x1); 
		$key = substr($url, $x1, $x2-$x1); 

		$x1 = stripos($url, '&gid='); 
		$gid = 0; 
		if ($x1 !== false) {
		
		$x1 = $x1 + 4; 
		$x2 = stripos($url, '&', $x1); 

			
		if ($x2 === false)
		{
			$gid = substr($url, $x1); 
		}
		else
		{
		  $gid = substr($url, $x1, $x2-$x1); 
		}
		}
		
		
		
		if (empty($key)) return; 
		
		$url = 'https://docs.google.com/spreadsheet/pub?key='.$key.'&single=true'; 
		
		if (!empty($gid)) {
			$url .= '&gid='.$gid; 
		}
		$url .= '&output=csv'; 
		
		$data = self::fetchUrl($url); 
		$cc = 0; 
		if ($data !== false)
		{
			
			$s1 = array("\r\r\n", "\r\n"); 
			$s2 = array("\n", "\n"); 
			$data = str_replace($s1, $s2, $data); 
			$data = explode("\n", $data); 
			
			
			
			$country_rates = array(); 
			
			foreach ($data as $n=>$line)
			{
				
				$x = str_getcsv($line);
				
				if (isset($x[1]))
				{
					$x[1] = str_replace(',', '.', $x[1]); 
					if (!is_numeric($x[1])) continue; 
				}
				
				if (strlen($x[0])!=2) continue; 
				
				$country_rates[$x[0]] = $x[1]; 
				
				
				
			}
			
			
			
			
			
			$myid = (int)$this->calc_id; 
			
			
			
			if (empty($myid)) return; 
			$db = JFactory::getDBO(); 
			if (!empty($country_rates))
			{
				   $q = 'delete from `'.$this->ref->taxTable.'_config` where `virtuemart_calc_id` = '.$myid.' limit 99999'; 
				   $db->setQuery($q); 
				   $db->query(); 
				   $e = $db->getErrorMsg(); 
				   if (!empty($e)) { die($e); }
				  
			
			$db = JFactory::getDBO(); 
			
			$q = array(); 
			foreach ($country_rates as $k2 => $rate)
			{
				
				$k2 = strtoupper($k2); 
				
				if ($k2 === 'UK') $k2 = 'GB'; 
				if ($k2 === 'EL') $k2 = 'GR'; 
				
				$qz = "select `virtuemart_country_id` from `#__virtuemart_countries` where `country_2_code` = '".$db->escape($k2)."' limit 0,1"; 
			    $db->setQuery($qz); 
			    $virtuemart_country_id = $db->loadResult(); 
				
				if (empty($virtuemart_country_id)) {
					
					 $this->eMsg('', __LINE__.' VAT Calc: Country was not found in Virtuemart: '.$k2); 
					
					continue; 
				}
				
				$tax_rate = $this->percent2Float($rate); 
				
				if (empty($tax_rate)) {
					//continue; 
				}
				
				  $qs = "insert into `".$this->ref->taxTable.'_config` (`id`, `virtuemart_calc_id`, `virtuemart_country_id`, `tax_rate`) values '; 
				  $q[] = '(NULL, '.(int)$myid.', '.(int)$virtuemart_country_id.', "'.$tax_rate.'")';
			   
			}
			$q = $qs.implode(', ', $q); 
			
			try
			   {
			    $db->setQuery($q); 
			    $db->query(); 
			   }
			   catch (Exception $e)
			   {

			   }
			
			}
			
			
		}
		

		
		
	}
	public function percent2Float($p)
	{
		$p = str_replace('%', '', $p); 
		$p = trim($p); 
		$p = floatval($p); 
		//$p = $p / 100; 
		return $p; 
	}
	public static function fetchUrl($url, $XPost='', $headers=array())
	{
	
	 if (!function_exists('curl_init'))
	 {
	  return file_get_contents($url); 
	 
	 }
		
	 $ch = curl_init(); 
	 $cookie = tempnam (JPATH_SITE.DIRECTORY_SEPARATOR."tmp", "CURLCOOKIE");
//	 curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
	 curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie );
	 curl_setopt($ch, CURLOPT_URL,$url); // set url to post to
	 
	 if (!empty($headers))
	 {
		 curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
	 }
	 
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
	    
	    JFactory::getApplication()->enqueueMessage('ERROR -> ' . curl_errno($ch) . ': ' . curl_error($ch), 'CURL');
		@curl_close($ch);
		return false; 
    } else {
		$response = curl_getinfo( $ch );
		
        $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		JFactory::getApplication()->enqueueMessage($url.' -> '.$returnCode, 'CURL');
        switch($returnCode){
            case 404:
			    @curl_close($ch);
                return false; 
                break;
			case 403:
				@curl_close($ch);
				return false; 
			case 301:
				
				break;
			case 302:
			    break; 
            case 200:
			
        	break;
            default:
				 @curl_close($ch);
            	return false; 
                break;
        }
		
	if ($response['http_code'] == 301 || $response['http_code'] == 302)
    {
        @ini_set("user_agent", "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");
        $headersr = get_headers($response['url']);

        $location = "";
		 @curl_close($ch);
        foreach( $headersr as $value )
        {
			$content = ''; 
			
            if ( substr( strtolower($value), 0, 9 ) == "location:" )
                return self::fetchUrl( trim( substr( $value, 9, strlen($value) ) ), $XPost, $headers);
        }
    }
		
		
    }
	
	
	
    
    @curl_close($ch);
    
  
    return $result;   
    
    

	}
	
	
	 public static function getColumns($table) {
   if (!self::tableExists($table)) return array(); 
   
     $db = JFactory::getDBO();
   $prefix = $db->getPrefix();
   $table = str_replace('#__', '', $table); 
   $table = str_replace($prefix, '', $table); 
   $table = $db->getPrefix().$table; 
   
   
   
   // here we load a first row of a table to get columns
   $db = JFactory::getDBO(); 
   $q = 'SHOW COLUMNS FROM '.$table; 
   $db->setQuery($q); 
   $res = $db->loadAssocList(); 
  
   $new = array(); 
   if (!empty($res)) {
    foreach ($res as $k=>$v)
	{
		
		$new[$v['Field']] = $v['Field']; 
	}
	
	return $new; 
   }
   
   return array(); 
   
   
 }
 
 
 static function tableExists($table)
  {
   
   
   $db = JFactory::getDBO();
   $prefix = $db->getPrefix();
   $table = str_replace('#__', '', $table); 
   $table = str_replace($prefix, '', $table); 
   $table = $db->getPrefix().$table; 
   
   
   
   if (isset(mossTaxHelper::$cache[$table])) return mossTaxHelper::$cache[$table]; 
   

   // stAn, it's much faster to do a positive select then to do a show tables like...
   $q = "SHOW TABLES LIKE '".$table."'";
	   $db->setQuery($q);
	   $r = $db->loadResult();
	   
	   if (empty(mossTaxHelper::$cache)) mossTaxHelper::$cache = array(); 
	   
	   if (!empty($r)) 
	    {
			
			
		mossTaxHelper::$cache[$table] = true; 
		return true;
		}
		mossTaxHelper::$cache[$table] = false; 
   return false;
  }
	
	
	
 }