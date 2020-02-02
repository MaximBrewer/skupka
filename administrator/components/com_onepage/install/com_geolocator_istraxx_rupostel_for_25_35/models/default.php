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

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.model' );
jimport( 'joomla.filesystem.file' );

	
class DefaultModelDefault extends JModelLegacy {

    function __construct() {
		parent::__construct();
	}

    
    function save() { }
	
	function getName()
	{
	  return 'default';
	}

	function clean($url)
	{
	   /*
	   jimport('joomla.filesystem.folder'); 
	   jimport('joomla.filesystem.file'); 
	   JFolder::delete(JPATH_ROOT.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'geodata'); 
	   $pa = pathinfo($url); 
	   if (!empty($pa['extension']))
	   $ext = $pa['extension']; 
	   else $ext = 'zip'; 
	 
	   $path = JPATH_ROOT.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'geodata.'.$ext;
	   //if (file_exists($path)) JFile::delete($path);
	   */
	   
	   
	   $this->addIndexes(); 
	}

	function insert($from, $to) {

	$db = JFactory::getDBO();
	$row = 0;
	$file = $this->getCsv(); 
	if (empty($file)) {
	   return false;
	}

	if (($handle = fopen($file, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 5000, ",")) !== FALSE) {
        $num = count($data);
        //echo "<p> $num fields in line $row: <br /></p>\n";
		
		if ($num == 6)
		{
		if (($row >= $from) && ($row <= $to))
		{
		  if (!$this->insert2db($data)) 
		   {
		    //die('sql error');
		    return false;
		   }
        }
		else
		if ($row > $to) 
		 {
		   //echo 'End: '.$row.' of max '.$to; 
		   //if ($to > 200) 
		   //die('ok here');
		   break 1;
		 }
		}
		else die('Incorrect format');
		
		$row++;
		//if ($row > 200) echo $row. ' ';
		//echo $row.' '.$from.' '.$to; 
		//if ($row > 200) die('here');

		}
    }
	else 
	{
	  //echo 'File error';die();
	  return false; 
	}
	
    fclose($handle);
	if ($data === false) 
	 {
	  // terminator code :)
	  //die('-3');
	  return -3; 
	 }
	   //die('ok');
	  return true; 
	}
	
	function insert2db($data){

		$db = JFactory::getDBO();
		// structure:
		/*
		$ipstart = $data[0];
		$ipend = $data[1];
		$iplongstart = $data[2];
		$iplongend = $data[3];
		$country_2_code = $data[4];
		$country_name = $data[5];
		*/
		$q = "insert delayed into #__geodata (`geo_id`, `ipstart`, `ipend`, `longstart`, `longend`, `country_2_code`, `country_name`) values (NULL, '".$db->escape($data[0])."', '".$db->escape($data[1])."', '".$db->escape($data[2])."', '".$db->escape($data[3])."', '".$db->escape($data[4])."', '".$db->escape($data[5])."') ";
		$db->setQuery($q);

		if (! $db->query()) {
			echo $q;
			return false;
		}
		return true;
	}
	 
	function getCsv() {
	  $files = scandir(JPATH_ROOT.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'geodata'); 
	  foreach ($files as $fi)
	   {
	     $pa = pathinfo($fi); 
		 if ((!empty($pa['extension'])) && ($pa['extension'] == 'csv')) return JPATH_ROOT.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'geodata'.DIRECTORY_SEPARATOR.$fi; 
	   }

	   return ""; 
	}
	
	function extract($url)
	{
	
	 $lf = JRequest::getBool('localfile', false); 
	 if ($lf)
	 {
	   $path = JPATH_ROOT.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'GeoIPCountryCSV.zip' ;
	 }
	 if (!file_exists($path))
	 {
	  $pa = pathinfo($url); 
	  if (!empty($pa['extension']))
	  $ext = $pa['extension']; 
	  else $ext = 'zip'; 
	 
	   $path = JPATH_ROOT.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'geodata.'.$ext;
	  }
	  
	  echo 'Extracting: '.$path.'<br />'; 
	   if (!file_exists($path)) 
	   {
	   echo 'File does not exists!<br />'; 
	   return false;
	   }
	   $dest = JPATH_ROOT.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'geodata'.DIRECTORY_SEPARATOR.'geodata.csv'; 
	   
	   jimport('joomla.filesystem.archive'); 
	   jimport('joomla.filesystem.jfolder'); 
	   $fold = JPATH_ROOT.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'geodata'; 
	   @JFolder::create($fold); 
	   $res = @JArchive::extract($path, $fold); 
	   
	   
	   
	   if ($res === true) 
	   {
	    $db = JFactory::getDBO();
	    if (!$this->tableExists('geodata'))
		 {
			 $this->createTable();
			 return false; 

		 }
		 else
		 {
		   $q = 'delete from #__geodata where 1 limit 99999999 ';
		   $db->setQuery($q); 
		   $db->query(); 
		   $q = 'ALTER TABLE #__geodata AUTO_INCREMENT = 1;'; 
		   $db->setQuery($q); 
		   $db->query(); 
		   
		   if (!empty($err)) 
		    {
			//echo $err;
			return false; 
			}
		 }
	    return true; 
	   }
	   
	   return false;
	  
	}

	
	function addIndexes()
	{
	   $q = 'ALTER TABLE `#__geodata` ADD UNIQUE KEY `iprange` (`longstart`,`longend`), ADD UNIQUE KEY `longstart` (`longstart`), ADD UNIQUE KEY `longend` (`longend`);'; 
	   $db = JFactory::getDBO(); 
	   $db->setQuery($q); 
	   try
	   {
	     $db->query(); 
	   }
	   catch (Exception $e)
	   {
	     // once we run this twice... 
	   }
	   
	}
	function createTable(){
		$q = 'CREATE TABLE IF NOT EXISTS `#__geodata` (
				`geo_id` bigint(20) NOT NULL auto_increment,
				`ipstart` varchar(39) NOT NULL,
				`ipend` varchar(39) NOT NULL,
				`longstart` bigint(20) NOT NULL,
				`longend` bigint(20) NOT NULL,
				`country_2_code` varchar(3) NOT NULL,
				`country_name` varchar(255) NOT NULL,
				PRIMARY KEY  (`geo_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			';
		$db = JFactory::getDBO();
		$db->setQuery($q);
		return $db->query();

	}

	function download($url)
	{
	  $pa = pathinfo($url); 
	  if (!empty($pa['extension']))
	  $ext = $pa['extension']; 
	  else $ext = 'zip'; 
	 
      $path = JPATH_ROOT.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'geodata.'.$ext; 
 
     $fp = fopen($path, 'w');
	 if ($fp === false) 
	  {
	    $inmem = true; 
		$fp = ''; 
	  }
     $ch = curl_init($url);
	 
	 if (empty($inmem))
     curl_setopt($ch, CURLOPT_FILE, $fp);
	 else curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	 
     $data = curl_exec($ch);
	 $err = curl_errno($ch); 
	 
     curl_close($ch);
	 if (!empty($err)) return false;
	 
	 if (empty($inmem))
     fclose($fp);
	 else 
	  {
	    jimport('joomla.filesystem.file'); 
		$res = @JFile::write($path, $fp); 
		if (empty($res)) return false;
	  }
	  //die('here');
	  return true; 
	}
    function tableExists($table)
{
 $db = JFactory::getDBO();
 $prefix = $db->getPrefix();
 $table = str_replace('#__', '', $table); 
 $table = str_replace($prefix, '', $table); 
 
  $q = "SHOW TABLES LIKE '".$db->getPrefix().$table."'";
	   $db->setQuery($q);
	   $r = $db->loadResult();
	   if (!empty($r)) return true;
 return false;
}

    
    
     
}