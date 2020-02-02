<?php

if (!defined('_JEXEC'))
die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');

/**
 * Calculation plugin for zip based tax rates
 *
 * @version $Id: 2.0.0
 * @package tax_per_zip for Virtuemart 3+
 * @subpackage Plugins - Zip Based US Tax
 * @author RuposTel.com
 * @copyright Copyright (C) RuposTel.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 *
 */
 
 class zipTaxHelper {
   var $ref; 
   var $calc_id; 
   public function __construct(&$ref)
   {
	   $this->ref =& $ref; 
   }
	static $qcache; 
	private function getResult($q)
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
		  JFactory::getApplication()->enqueueMessage('Tax per zip: Tables not installed !'); 
	  }
	  if (empty($res)) $res = 0; 
	  $res = floatval($res); 
	  
	  self::$qcache[$hash] = $res; 
	  $e = $db->getErrorMsg(); 
	  if (!empty($e)) { echo $e; die(); }
	  return $res; 
	}
	
    public function getTaxRate($zip, $id)
	{
	  $id = (int)$id; 
	  $q = 'select tax_rate from '.$this->ref->taxTable.'_config where zip_start <= '.$zip.' and zip_end >= '.$zip.' and virtuemart_calc_id = '.$id.' limit 0,1'; 
		  //echo $q; die(); 
		  $tax_rate = $this->getResult($q); 
		 
		  return $tax_rate;
	}
	
	public function storeConfig(&$data)
	{
		$db = JFactory::getDBO ();
		$table = new TableCalcs($db);
		$table->setUniqueName('calc_name');
		$table->setObligatoryKeys('calc_kind');
		$table->setLoggable();
		$table->setParameterable ($this->ref->_xParamsP, $this->ref->_varsToPushParamP);
		$table->bindChecknStore($data);
		
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
			$ZIP_COLS = array(); 
			$results = array(); 
			$system_cols = array(); 
			$repeated = array(); 
			$s1 = array("\r\r\n", "\r\n"); 
			$s2 = array("\n", "\n"); 
			$data = str_replace($s1, $s2, $data); 
			$data = explode("\n", $data); 
			
			
			
			foreach ($data as $n=>$line)
			{
				
				$x = str_getcsv($line);
				
				$line_zips = array(); 
				
				$general = new stdClass(); 
				
				foreach ($x as $ind => $v)
				{
					    // special case define columns: 
						if ($n === 0)
						{
					         if ($v === 'County') {
								 $COUNTY_COL = $ind; 
							 }
							  
							 if (stripos($v, 'State Tax')!==false) {
								 $STATE_COL = $ind; 
							 }
							 
							 if (stripos($v, 'County Total')!==false) {
								 $TOTAL_COL = $ind; 
							 }
							 
							 if (stripos($v, 'County Surtax')!==false)
							 {
								 $SURTAX_COL = $ind; 
							 }
							 
							 if (stripos($v, 'Zip')!==false)
							 {
								 $ZIP_COLS[$ind] = $ind; 
							 }
							 
						}
						else
						{
							
							if (!in_array($ind, $system_cols))
							if ((in_array($ind, $ZIP_COLS)) || ((is_numeric($v)) && (strlen($v)===5)))
							{
								if (!empty($v))
								$line_zips[] = $v; 
							}
							
							
							
						}


				
				}
				
				if ($n === 0) {
				 
							if (isset($COUNTY_COL)) $system_cols[$COUNTY_COL] = $COUNTY_COL; 
							if (isset($STATE_COL)) $system_cols[$STATE_COL]= $STATE_COL; ; 
							if (isset($TOTAL_COL)) $system_cols[$TOTAL_COL]= $TOTAL_COL; ; 
							if (isset($SURTAX_COL)) $system_cols[$SURTAX_COL]= $SURTAX_COL; ; 
				  
				  
				  
				  continue; 
				}
				
				//echo 'lz:'.count($line_zips)."<br />"; 
				$cc += count($line_zips); 
				
				
				
				foreach ($line_zips as $zip)
				{
				$general = new stdClass(); 
				if (isset($COUNTY_COL))
				$general->county = $x[$COUNTY_COL]; 
				if (isset($STATE_COL))
				$general->state_tax = $x[$STATE_COL]; 
			    if (isset($TOTAL_COL))
				$general->county_total = $x[$TOTAL_COL]; 
				if (isset($SURTAX_COL))
				$general->county_surtax = $x[$SURTAX_COL]; 
				$zip = (int)$zip; 
				
				if (empty($zip)) continue; 
				
				if (isset($results[$zip]))
				{
					$repeated[] = $zip;  
					continue; 
				}
				$general->zip = $zip; 
				$results[$zip] = $general; 
				}
				
				
				
				//echo $n."<br />"; 
				
				
			}
			
			
			if (!empty($repeated))
			{
				JFactory::getApplication()->enqueueMessage('Repeating Zip Codes: '.implode(',', $repeated).'. Please check if they got imported correctly'); 
			}
			
			
			$myid = (int)$this->calc_id; 
			if (empty($myid)) return; 
			$db = JFactory::getDBO(); 
			if (!empty($results))
			{
				   $q = 'delete from `'.$this->ref->taxTable.'_config` where `virtuemart_calc_id` = '.$myid.' limit 99999'; 
				   $db->setQuery($q); 
				   $db->query(); 
				   $e = $db->getErrorMsg(); 
				   if (!empty($e)) { die($e); }
				  
			
			$q = array(); 
			foreach ($results as $d)
			{
				
				$zip_start = $zip_end = $d->zip; 
				$tax_rate = $this->percent2Float($d->county_total); 
				
				if ($tax_rate < 1) $tax_rate = $tax_rate * 100; 
				
				if (empty($tax_rate)) {
					continue; 
				}
				
				  $qs = "insert into `".$this->ref->taxTable.'_config` (`id`, `virtuemart_calc_id`, `zip_start`, `zip_end`, `tax_rate`) values '; 
				  $q[] = '(NULL, '.(int)$myid.', '.(int)$zip_start.', '.(int)$zip_end.', "'.$tax_rate.'")';
			   
			}
			if ((empty($qs)) || (empty($q))) {
				JFactory::getApplication()->enqueueMessage('Tax rates were not found in the CSV file. Allowed first line column names are: County, State Tax, County Total, County Surtax, Zip. Only County Total and Zip are used in this plugin.'); 
			}
			else
			{
			$q = $qs.implode(', ', $q); 
			//echo $q; die(); //32694
			try
			   {
			    $db->setQuery($q); 
			    $db->query(); 
			   }
			   catch (Exception $e)
			   {
				   
				   JFactory::getApplication()->enqueueMessage('Error: '.$e); 
				 
			   }
			
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
		
		$q = 'select `name` from #__extensions where `enabled` = 1 and `folder` = "vmcalculation" and `type` = "plugin" and `element` != "tax_per_zip"'; 
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
	
	
 }