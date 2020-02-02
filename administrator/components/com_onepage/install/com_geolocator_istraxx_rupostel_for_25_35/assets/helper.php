<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* GeoLocator is free software released under GNU/GPL  This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* This php file was create by www.rupostel.com team
*/
class geoHelper 
{

  // returns 2 letter country code
  // query is statically cached per detected IP address
  public static function getCountry2Code($ip="") {
    // cache positive reponses: 
    static $cache; 
	
	geoHelper::getIp($ip); 


	

    if (!empty($ip))
    {

	 if (isset($cache[$ip])) return $cache[$ip]; 
	
    
     // ipnum = 16777216*w + 65536*x + 256*y + z
	 // IP Address = w.x.y.z
	 // stAn sometimes we can get: w.x.y.z,127.0.0.1 from HTTP_X_FORWARDED_FOR
     
	 $ipl = geoHelper::getIP2Long($ip); 
	 if (empty($ipl)) return false; 
	 
	 $db = JFactory::getDBO();
	  
	 if (function_exists('vmdebug'))
	 {
	  $ipl2 = ip2long($ip);
	  vmdebug('my ip',$ip,$ipl,$ipl2);
	 }
	 
    
     $q = 'select country_2_code from #__geodata where longstart <= '.$ipl.' and longend >= '.$ipl.' limit 0,1'; 
     //echo $q;
     $db->setQuery($q); 
	 try
	 {
       $result = $db->loadResult();
	   // get rid of any references when using mysql native driver on php5.6
	   if (!empty($result))
	   $res = (string)$result; 
	 }
	 catch(Exception $e)
	 {
	   //stAn - if table does not exists for any reason
	   return false; 
	 }
	 
		if (function_exists('vmdebug'))
		{
		 if (empty($res)) $res = ''; 
		 vmdebug('getCountry2Code ',$q,$res);
		}

		if (empty($res)) return false;
		
		
		$cache[$ip] = $res; 
        return $res; 
    }
    
   return false;    
  }
  
  public static function getIP(&$ip)
  {
     if (!empty($ip)) return $ip; 
	 
	if (empty($ip))
    {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
	{
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	
	
	if (stripos($ip, ',')!==false)
	 {
	    $a = explode(',', $ip); 
		$ip = trim($a[0]); 
	 }
	
	}
    else
	{
    if (empty($ip) && (!empty($_SERVER['REMOTE_ADDR']))) 
	{
     $ip = $_SERVER['REMOTE_ADDR'];
	}
	}
    }
	
	
	 
  }
  public static function getIP2Long($ip)
  {
     $arr = explode('.', $ip); 
	 if (count($arr)<4) return 0;
	 $ipl = (16777216*$arr[0])+(65536*$arr[1])+(256*$arr[2])+$arr[3]; 
	 return $ipl;
  }
  
  // returns EN name of the country, or a text message of a local address
  public static function getCountry($ip='')
  {
  
  
    geoHelper::getIp($ip); 
	
	
	
	$local = array( array('127.0.0.1', '127.255.255.255'), array('10.0.0.0', '10.255.255.255'), array('172.16.0.0', '172.31.255.255'), array('192.168.0.0', '192.168.255.255')); 
	
	
	
    if (!empty($ip))
    {


	  
	 $ipl = geoHelper::getIP2Long($ip); 
	 if (empty($ipl)) return false; 
	 
	 
	 foreach ($local as $lr)
	 {
	      $l1 = geoHelper::getIP2Long($lr[0]); 
		  $l2 = geoHelper::getIP2Long($lr[1]); 
		  
		  if (($ipl >= $l1) && ($ipl <= $l2))
		   {
		      return 'Local Network'; 
		   }
	 }
    
	 
     $db = JFactory::getDBO();
     $q = 'select country_name from #__geodata where longstart <= '.$ipl.' and longend >= '.$ipl.' limit 0,1'; 
     
     $db->setQuery($q); 
     $res = $db->loadResult(); 
     
     if (empty($res)) return false;

     return $res; 
    }
    
   return false;     
  }
  
  // returns false on failture and array with geodata if found
  public static function getGeoData($ip)
  {
    geoHelper::getIp($ip); 

    
    if (!empty($ip))
    {

     $db = JFactory::getDBO();
     $ipl = geoHelper::getIP2Long($ip); 
     
     $q = 'select * from #__geodata where longstart <= '.$ipl.' and longend >= '.$ipl.' limit 0,1'; 
     
     $db->setQuery($q); 
     $res = $db->loadAssoc(); 
     
     if (empty($res)) return false;
	 
	 $res = (array)$res; 
	 
     return $res; 
    }
    
   return false;    
  }
}