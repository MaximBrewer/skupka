<?php
/*
*
* @copyright Copyright (C) 2007 - 2010 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

// to drop any tables run:
// DROP TABLE `jos_onepage_exported`, `jos_onepage_export_templates`, `jos_onepage_export_templates_settings`;

defined( '_JEXEC' ) or die( 'Restricted access' );

if (function_exists('set_time_limit'))
set_time_limit(1000);




$dir = dirname(__FILE__); 
$a1 = explode(DS, $dir); 
$cname = $a1[count($a1)-2]; 
define('JPATH_COMPONENT_ADMINISTRATOR_ONEPAGE', JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.$cname);

if (!class_exists('Numbers_Words'))
 {
  require(JPATH_COMPONENT_ADMINISTRATOR_ONEPAGE.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'Words'.DIRECTORY_SEPARATOR.'Words.php');
 }


class OnepageTemplateHelper
{
 public static $do_no_close_app;  
 public static $do_no_store_output;  
 public static $print_output;  
 function __construct($local_id=0) 
 {
	 $this->local_id = $local_id;
 }
 
 private $local_id;
 private $templates = array();
 // this construtor will assign a localid which will be used if local_id in parameters is empty
 // localid is orderid for example, but can be also a hash of order list filter
 /*
 function __construct1($local_id) 
 {
   $this->local_id = $local_id;
   
   
 }
 */
 
 
 // returns array of the template settings, if not found, returns null
 /*
 zakladne hodnoty keyname su:
 tid_special (checkbox 0/1/neexistuje) – vytvori specialny input pre napr. Cislo faktury
 tid_ai – (checkbox 0/1/neexistuje) automaticke cislovanie tid_special (autoincrement)
 tid_num - (checkbox 0/1/neexistuje)  pre kazdu ciselnu hodnotu vytvori spatne polia a prida nuly
                 (napr. Cislo uctu v seku, ale bude to pre vsetky)
 tid_nummax – pocet cislic pre pridavanie nul (napr. 5, cize pre 123 by to bolo array ('3','2','1','0','0')
 tid_itemmax – max pocet poloziek objednavky na fakture (ak je v sablone napr _99)
 tid_back (checkbox 0/1/neexistuje) – vsetky cisla a AJ texty zkonvertuje do formatu priklad:
              pre string hello to bude array ('o', 'l', 'l', 'e', 'h')
 tid_forward (checkbox 0/1/neexistuje) vsetky cisla a aj texty budu zkonvertovane do arrayu:
               pre string hello to bude array ('h','e','l', 'l','o') a
               pre cislo 123 to bude ak je nummax=5 array('0', '0', '1', '2','3')
 tid_xml = XML pre dodatocne info
 NESKOR TU PRIBUDNE ESTE IKONKA:
 tid_icon = filepath
 */
 function getTemplate($tid)
 {
  
  if (!empty($this->templates))
  {
   foreach ($this->templates as $t)
   {
    if ($t['tid'] == $tid) return $t;
   }
  }
  $this->templates = $this->getExportTemplates('ALL');
  
  if (!empty($this->templates))
  {
   foreach ($this->templates as $t)
   {
    if ($t['tid'] == $tid) 
    {
     
     return $t;
    }
   }
  }
  
 return null; // if the template id was not found  
 }

 function createXLS($data, $fileout) {
	 
@ini_set("memory_limit","512M");
if (!file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'PHPExcel'.DIRECTORY_SEPARATOR.'Classes'.DIRECTORY_SEPARATOR.'PHPExcel.php')) { JFactory::getApplication()->enqueueMessage('Cannot find PHPExcel in '.JPATH_ROOT.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'PHPExcel');
return; 
}

require_once ( JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'export_helper.php');
require_once ( JPATH_ROOT.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'PHPExcel'.DIRECTORY_SEPARATOR.'Classes'.DIRECTORY_SEPARATOR.'PHPExcel.php');
require_once ( JPATH_ROOT.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'PHPExcel'.DIRECTORY_SEPARATOR.'Classes'.DIRECTORY_SEPARATOR.'PHPExcel'.DIRECTORY_SEPARATOR.'IOFactory.php');

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set properties
$objPHPExcel->getProperties()->setCreator("RuposTel Systems")
							 ->setLastModifiedBy("RuposTel Systems")
							 ->setTitle("OPC Order Management")
							 ->setSubject("OPC Order Management")
							 ->setDescription("Order Management for VirtueMart")
							 ->setKeywords("orders, virtuemart, eshop")
							 ->setCategory("Orders");
	 
	 
	 $sheet_n = 0; 
	 foreach ($data as $sheet_name=>$sheet_data) {

$first = reset($sheet_data); 	 
$keys = array_keys($first);
$row = 1; 



if ($sheet_n > 0)  {
	$objPHPExcel->createSheet($sheet_n);
	$objPHPExcel->setActiveSheetIndex($sheet_n);
}

foreach ($first as $key=>$val)
{
 $objPHPExcel->setActiveSheetIndex($sheet_n)
             ->setCellValueByColumnAndRow($col, $row, $key);
 $col++;
  
}
$row++;
$col=0;
foreach ($sheet_data as $k => $value)
{
 foreach ($value as $k2 => $colval)
 {
	 $test = trim($colval); 
	 if (substr($test, 0,1)==='=') $colval = "'".$colval; 
 	$objPHPExcel->setActiveSheetIndex($sheet_n)
            ->setCellValueByColumnAndRow($col, $row, $colval);
    $col++;
            
 }
 
 $row++;
 $col=0;
}
		$objPHPExcel->getActiveSheet()->setTitle($sheet_name);
		$sheet_n++; 
	 } 
	 
	 $objPHPExcel->setActiveSheetIndex(0);
	 
	 $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	 //screen: $objWriter->save('php://output'); 
	 if (file_exists($fileout)) {
		 JFile::delete($fileout); 
	 }
	 $objWriter->save($fileout); 
	 
	 
 }
 
 
 function getFileHash($tid)
 {
  $t = $this->getTemplate($tid);
  if (file_exists(JPATH_COMPONENT_ADMINISTRATOR_ONEPAGE.DIRECTORY_SEPARATOR.'export'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$t['file']))
  $mdate = filemtime(JPATH_COMPONENT_ADMINISTRATOR_ONEPAGE.DIRECTORY_SEPARATOR.'export'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$t['file']);
  $secret = 'onepage checkout is cool';
  $z = $secret.$mdate;
  
  	if (method_exists('JApplication', 'getHash'))
	$hashn = JApplication::getHash('opc export'); 
	else $hashn = JUtility::getHash('opc export'); 

	$z = $z.$hashn; 
	
  $z = hash('sha512', $z.$hashn); 
  $z = strtolower($z); 
  
  return $z; 
 }
 
 function getTxtTemplate($tid)
 {
  $tid = $this->getTemplate($tid);
  $tfile = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'export'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$tid['file'];
  if (file_exists($tfile))
  {
   return file_get_contents($tfile);
  }
  return false;
  
 }
 
 function processPhpTemplate($tid, $localid, &$data)
 {
   $tidd = $this->getTemplate($tid);
   $tfile = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'export'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$tidd['file'];
   
   if (file_exists($tfile))
   {
    
	OnepageTemplateHelper::$print_output = false; 
	
	ob_start(); 
	
	include($tfile); 
	$output = ob_get_clean(); 
	
	if (!empty(OnepageTemplateHelper::$print_output)) echo $output; 
	
	if (empty($output)) return false; 
	return $output; 
   }
   return false; 
 }
 
 function processTxtTemplate($tid, $localid, &$data)
 {
   $this->setStatus($tid, $localid, 'PROCESSING');
   $tidd = $this->getTemplate($tid);
   $rf = strrev($tidd['file']); 
   if (strpos($rf, 'php.')===0) $tx = $this->processPhpTemplate($tid, $localid, $data); 
   else
   $tx = $this->getTxtTemplate($tid);
   
   
   
   if (!empty(OnepageTemplateHelper::$do_no_store_output)) return; 
   
   if ($tx === false) 
   {
   $this->setStatus($tid, $localid, 'ERROR');
   return false;
   }
   foreach($data as $key=>$v)
   {
     $tx = str_replace('{'.$key.'}', $v, $tx);
   }
   
   $this->prepareDirectory($tid);
   
   $this->setStatus($tid, $localid, 'CREATED'); 
   
   if (!empty($data['special_value_ai_0']))  
   $file = $this->getFileName2Save($tid, $localid, $data['special_value_ai_0']);
   else
   $file = $this->getFileName2Save($tid, $localid);
   jimport('joomla.filesystem.file');
   
   if (strpos($rf, 'php.')===0)
   $file = str_replace('.php', '.xml', $file); 
   
   if (JFile::write($file, $tx)!==false)
   {
   $this->setStatus($tid, $localid, 'CREATED', urlencode($file));
   echo 'OK: File saved<br />'.$file; 
   return true; 
   $mainframe = JFactory::getApplication(); 
   $mainframe->close(); 
   
   }
   else 
   {
    $this->setStatus($tid, $localid, 'ERROR');
    echo 'Error: Cannot write to '.$file;
   }
   return true;
 }
 
 
 function processTxtTemplateMulti($tid, $order_ids, $localid_key, &$data)
 {
	 
	 
   foreach ($order_ids as $localid) {
     $this->setStatus($tid, $localid, 'PROCESSING');
   }
   
   
   
   //$localid_key
   
   $tidd = $this->getTemplate($tid);
   $rf = strrev($tidd['file']); 
   if (strpos($rf, 'php.')===0) $tx = $this->processPhpTemplate($tid, $order_ids, $data); 
   else
   {
	   die('format not supported, change type of the template'); 
   }
   
   if (!empty(OnepageTemplateHelper::$do_no_store_output)) return; 
   
   foreach ($order_ids as $localid) {
     
   if ($tx === false) 
   {
   $this->setStatus($tid, $localid, 'ERROR');
   return false;
   }
   
   }
   
   $this->prepareDirectory($tid);
   
  
   
   if (!empty($data['special_value_ai_0']))  
   $file = $this->getFileName2Save($tid, $localid_key, $data['special_value_ai_0']);
   else
   $file = $this->getFileName2Save($tid, $localid_key);
   jimport('joomla.filesystem.file');
   
   if (strpos($rf, 'php.')===0)
   $file = str_replace('.php', '.xml', $file); 
   
   if (JFile::write($file, $tx)!==false)
   {
   
   
   foreach ($order_ids as $localid) {
     $this->setStatus($tid, $localid, 'CREATED'); 
   }
   
   echo 'OK: File saved<br />'.$file; 
   return true; 
   $mainframe = JFactory::getApplication(); 
   $mainframe->close(); 
   
   }
   else 
   {
	    foreach ($order_ids as $localid) {
			$this->setStatus($tid, $localid, 'ERROR'); 
		}
	   
    
    echo 'Error: Cannot write to '.$file;
   }
   return true;
 }
 
 
 // $str = 1
// $num = 4
// output: 0001
function addZeros($str, $num)
{
 $start = strlen($str);
 for ($i=$start; $i<=$num; $i++)
 {
  $str = '0'.$str;
 }
 return $str;
}
 function getFileName2Save($tid, $localid, $ai=null)
 {
    jimport('joomla.filesystem.file');
	if (empty($localid))
    $localid = JRequest::getVar('localid');
    
    $tt = $this->getTemplate($tid);
    $eitem = $this->getExportItem($tid, $localid);
	
    $num = $eitem['ai'];
	
	
	
	if (!empty($num)) $num = $localid;
	else $num = $num;
	
	if (!empty($ai)) $num = $ai; 
	
    if (is_numeric($num)) $num = $this->addZeros($num, 4);
  	$tn=JFile::makesafe($tt['tid_name']);
  	// 'ORDER_DATA','ORDER_DATA_TXT','ORDERS','ORDERS_TXT' 
  	if ($tt['tid_type'] == 'ORDER_DATA' || ($tt['tid_type'] == 'ORDERS')) $ext = '.pdf';
  	else 
  	{
  	  $arr = explode('.', $tt['file']);
  	  $ext = '.'.end($arr);
  	}
  	$dir = $this->prepareDirectory($tid);
	$path = $dir.DIRECTORY_SEPARATOR.$num.'_'.$tn.$ext;
	return $path; 
 }
 
 function prepareDirectory($tid)
 {
 jimport('joomla.filesystem.file');
  $tname = $tid;
  $tname = JFile::makesafe($tname);
  
  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
  $defualt = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'export'.DIRECTORY_SEPARATOR;
  $ex = OPCconfig::getValue('xml_export', 'export_dir', 0, $defualt, false); 
  
  if (empty($ex)) $ex = $default; 
  
  if (stripos($ex, -1) !== DIRECTORY_SEPARATOR) $ex .= DIRECTORY_SEPARATOR; 
  
  if (!file_exists($ex))
  {
	  $data = ' '; 
	  JFolder::create($ex);
	  JFile::write($ex.'index.html', $data); 
	  
  }
  
  $exf = $ex.$tname;

  if (file_exists($exf)) return $exf;
  else
  {
     JFolder::create($exf);
     JFile::copy($ex.'.htaccess', $exf.DIRECTORY_SEPARATOR.'.htaccess');
     return $exf;
  }
 }
 
 function createTables()
 {
   $ret =  $this->parseSQLFile(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'order_export.sql');
   
 
 }
 
 
 function getXMLs() {
	 jimport('joomla.filesystem.folder');
	 $path = JPATH_COMPONENT_ADMINISTRATOR_ONEPAGE.DIRECTORY_SEPARATOR.'export'.DIRECTORY_SEPARATOR.'templates';
	 $files = JFolder::files($path, '.xml', false, true);
	 $reta = array(); 
	 foreach ($files as $fn) {
	 
	 $xml = @simplexml_load_file($fn); 
   if ($xml !== false) {
   $f = $xml->xpath('//extension/files/filename');
   if (is_array($f)) {
	 $f = (string)reset($f); 
	 if (!empty($f)) {
        $reta[] = $fn;
	 }
    }
   }
	 }
   return $reta; 
 }
 
 function getExportTemplates($type = 'ORDER_DATA', $onlyenabled=false)
{

 // june 2012, should be added: 
 // ALTER TABLE  `jos_onepage_exported` ADD  `aiprefix` VARCHAR( 100 ) NOT NULL
 // ALTER TABLE  `jos_onepage_exported` ADD  `aiwidth` INT NOT NULL
 // ALTER TABLE  `jos_onepage_exported` CHANGE  `ai`  `ai` BIGINT( 20 ) NOT NULL

 //if (!empty($this->templates)) return $this->templates;
 $path = JPATH_COMPONENT_ADMINISTRATOR_ONEPAGE.DIRECTORY_SEPARATOR.'export'.DIRECTORY_SEPARATOR.'templates';
 $maxs = 0;
 //$files = scandir($path);
 jimport('joomla.filesystem.folder');
 //$files = JFolder::files($path, '.xml', false, true);
 
 $files = $this->getXMLs(); 
 
 $xmlto_tid = array(); 
 
 $reta = array();
 foreach ($files as $fn)
 {
  // since okt 2012 we allow php files upload as well 
  // if ($f != '.' && $f != '..' && (strpos($f, '.php')===false) && (strpos($f, '.ht')===false) && (!is_dir($path.DIRECTORY_SEPARATOR.$f)))
  
  
   $xml = @simplexml_load_file($fn); 
   if ($xml !== false) {
   $f = $xml->xpath('//extension/files/filename');
   if (is_array($f)) {
	 $f = (string)reset($f); 
     $reta[] = $f;
	 
	 
	 $xmlto_tid[$f] = $fn; 
    }
   }
 }
 
 
 
 
// SQL FOR CREATING THE TABLE
if (empty($reta)) return $reta;
$dbj = JFactory::getDBO();
$retd = array();
if (!empty($reta))
 if (!$this->tableExists('onepage_export_templates'))
 {
  
  $this->createTables();
 }

 
 
foreach ($reta as $f)
{
 $res = '';
 
 $qf = 'select * from #__onepage_export_templates where file="'.$dbj->escape(urlencode($f)).'"';
 $dbj->setQuery($qf);
 $res = $dbj->loadAssoc();

 $msg = $dbj->getErrorMsg();
 
  $bwa = array();
  if (empty($res))
  {
   $q = 'insert into #__onepage_export_templates (`tid`, `file`, `name`, `type`) values (NULL, "'.$dbj->escape(urlencode($f)).'", "", "ORDER_DATA") ';
   $dbj->setQuery($q);
   $dbj->query();
   $msg = $dbj->getErrorMsg();
   if (!empty($msg)) { echo $msg; return; }
   $qf = 'select * from #__onepage_export_templates where file="'.$dbj->escape(urlencode($f)).'"';
   $dbj->setQuery($qf);
   $res = $dbj->loadAssoc();
   if (empty($res)) $res = array();
  }
  
  $bwa = $res;
 
 $tpath = JPATH_COMPONENT_ADMINISTRATOR_ONEPAGE.DIRECTORY_SEPARATOR.'export'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR;
 
 if (!file_exists($tpath.urldecode($bwa['file'])))
  {
	  $next = true; 
  }
 else
 {
 
 if (!empty($res))
 foreach ($bwa as $k=>$v)
 {
  // keys: tid file name type
  if ($k == 'file') $v = $cfile = urldecode($v);
  
 
  $bwa[$k] = $v;
 }
  // select settings
  $q = 'select * from #__onepage_export_templates_settings where tid = "'.(int)$bwa['tid'].'" ';
  $dbj->setQuery($q);
  $ss = $dbj->loadAssocList();
  // for each row and column of tid
 
 
 if (empty($ss))
 {
	 $default_config = $tpath.urldecode($bwa['file']).'.ini'; 
	 
	 if (file_exists($default_config))
	 {
		 $data = parse_ini_file($default_config, false); 

		 foreach ($data as $kk=>$vv)
		 {
			 $bwa[$kk] = $vv; 
		 }

	 }
 }
 
 
 if (!empty($ss))
 {
  foreach ($ss as $v)
   foreach ($v as $k=>$v2)
  {
   if ($k == 'keyname') 
   {
    $key = $v2;
    $value = $v['value'];
    $bwa[$key] = $value; 
   }
   
  }
  

 }
  $pa = pathinfo($xmlto_tid[$f]); 
  $xmlf = $pa['basename']; 
  $xmlfc = $pa['filename']; 
  $bwa['tid_xml'] = $xmlf; 
  $bwa['tid_configkey'] = JFile::makeSafe($xmlfc); 
  $tid = (int)$bwa['tid']; 
  
  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
  $default = new stdClass();   
  
  $config = OPCconfig::getValue('order_export_config', $xmlfc, $tid, $default);
  $bwa['config'] = $config; 
  
  if ($onlyenabled) {
	  
	  if (empty($bwa['tid_enabled'])) {
		  continue; 
	  }
  }
  
  $retd[] = $bwa;
 }
 
 
}



 // get max specials:
 foreach ($retd as $i=>$y)
 {
  if ($type == 'ALL')
   { if (!empty($retd[$i]['tid_special']) && (!empty($retd[$i]['tid_specials']))) 
    {
    if ($retd[$i]['tid_specials']>$maxs)
    $maxs = $retd[$i]['tid_specials'];
    }
   }
  else
   {
     if (!empty($retd[$i]['tid_special']) && (!empty($retd[$i]['tid_specials']) && ($retd[$i]['tid_type']==$type))) 
      {
       if ($retd[$i]['tid_specials']>$maxs)
       $maxs = $retd[$i]['tid_specials'];
      }
   }
 }
 foreach ($retd as $i=>$y)
 {
  $retd[$i]['max_specials'] = $maxs;
  if (empty($retd[$i]['tid_name'])) $retd[$i]['tid_name'] = $retd[$i]['file'];
  
  if (isset($retd[$i]['tid_autocreatestatus']))
  {
	  $retd[$i]['tid_autocreatestatus'] = @json_decode($retd[$i]['tid_autocreatestatus']); 
  }
 
 
 }
 if ($type != 'ALL')
 {
 foreach ($retd as $i=>$t)
 {
  if (empty($retd[$i]['tid_name'])) $retd[$i]['tid_name'] = $retd[$i]['file'];
  
  if ((isset($retd[$i]['tid_enabled'])) && $retd[$i]['tid_enabled'] != '1')
  {
   unset($retd[$i]);
  }
  else
  if ((!isset($retd[$i]['tid_type'])) || $retd[$i]['tid_type'] != $type)
  {
   unset($retd[$i]);
  }
 }
 }
 
 
 
 $this->templates = $retd;
 
 return $retd;
}


	function sendData($XPost, $tid=0, $order_id=0, $f=array())
	{
		
		
	if (!function_exists('curl_init'))
	 {
	    echo 'ERROR: Curl not installed ! Please contact your hosting provider. </br>';
	    return; 
	 }
	 
	 
		 // tu pojde kod na odoslanie na pdf.rupostel.com
	 // ale je nutne brat do uvahy ze vacsina zakaznkov CURL nebude mat aktivne
	 //echo '-------- sending XML ----------';
	
	 $url = 'https://pdf.rupostel.com/convert.php';
	 
	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
	 $default = 'https://pdf.rupostel.com/convert.php'; 
	 $url = OPCconfig::getValue('xml_export', 'api_url', 0, $default, false); 
	 
	 if (!is_array($XPost)) {
	  $z = strlen($XPost); 
	  error_log('OPC Order Export: Curl to URL: '.$url.' data length '.$z.' bytes');
	 }
	 
	 

	 $username = OPCconfig::getValue('xml_export', 'api_username', 0, '', false); 
	 $password = OPCconfig::getValue('xml_export', 'api_password', 0, '', false);
	 
	 $ret = OPCloader::fetchUrl($url, $XPost, $username, $password, false, $f); 
	 
	 if ($ret === false) {
	    echo "\n<br />Error loading ".$url; 
		if (!empty($tid) && (!empty($order_id)))
		$this->setStatus($tid, $order_id, 'ERROR');
		return; 
	 }
	 
	 echo $ret; 
	 
	 
	 
	

	}
	
	function getSpecialsArray($tid, $localid, $ind=0, &$data)
	{
	 $ind = '_'.$ind;
	 $arr = $this->getSpecials($tid, $localid);
	 foreach ($arr as $key=>$val)
	 {
	  $data[$key.$ind] = $val;
	 }
	 return $data;
	}
	
	// trims string of illegal XML characters
	function prepareString($title)
	{
	
	 if ((is_object($title) || (is_array($title)) || (is_null($title))))
	   {
		   return ''; 
	   }
	
		$replaceArray = array(array(), array()); // this is a replace array for illegal SGML characters;
		for ($i=0; $i<32; $i++)                  // produces a correct XML output
		{
			$replaceArray[0][] = chr($i);
			$replaceArray[1][] = "";
		}
		
			$title = str_replace($replaceArray[0], $replaceArray[1], $title); // get rid of illegal SGML chars
		
		return  $title; // prints out "Autobus zamiast Hetmana"
	}
	
	/* ONLY FOR ORDER_DATA AT THE MOMENT
    * Creates an XML from getOrderData() for export
    * Will attach full template with tid
    * This function was rewritten not tu run under DOMDocument php extension for portability
    */
    function getXml($tid, $localid=null, $data=null)
    {
        $component_name = JRequest::getVar('option', 'com_onepage');
        // no hacking here:
        $component_name = urlencode(urlencode($component_name));

     if (!isset($localid) && (!empty($this->local_id))) $localid = $this->local_id;
     if (empty($localid)) 
     {
      $order_id = JRequest::getVar('order_id');
      $this->local_id = $order_id;
     }
	 
     $t = $this->getTemplate($tid);
	 if (empty($data))     
     $data = $this->getOrderDataEx($tid, $localid);
     
	 $xml = '<?xml version="1.0" encoding="utf-8"?>'."\n".'<XML>'."\n".'<ORDER_DATA>'."\n";
	 foreach ($data as $k=>$v)
     {
      //if (!empty($data[$k]) || ($data[$k]==0))
      {
      
       //$xml .= '<![CDATA['.htmlspecialchars($data[$k]).']]>';
	   // we need to get rid of some ASCII codes here
	   if (is_array($data[$k]) && (empty($data[$k]))) $data[$k] = ''; 
	   
	   if (is_array($data[$k]))
	   {
	    $arr = $data[$k]; 
		
		foreach ($arr as $ki2=>$vi2)
		 {
			 $mkey = $k.'_'.$ki2; 
			$xml .= '<'.$mkey.'>';
		    $data[$k.'_'.$ki2] = $vi2; 
			$xml .= htmlspecialchars($this->prepareString($data[$k.'_'.$ki2]));
		    $xml .= '</'.$mkey.'>'."\n";	
		 }
		 unset($data[$k]); 
		 continue; 
	   }
	   
	  
	   
	   $xml .= '<'.$k.'>';
       $xml .= htmlspecialchars($this->prepareString($data[$k]));
       $xml .= '</'.$k.'>'."\n";
      }
     }
     
	 $xml .= '</ORDER_DATA>'."\n";
	 $xml .= '<TEMPLATE_SETTINGS>'."\n";
//	 $xml .= '<TID>'.$tid.'</TID>';
//	 $xml .= '<LOCALID>'.$localid.'</LOCALID>';
	 $hash = $this->getFileHash($tid);
	 $xml .= '<SECRET>'.$hash.'</SECRET>'."\n";
	 $xml .= '<URL><![CDATA['.$this->getUrl().'index.php?option=com_onepage&view=orderexport&format=opchtml&nosef=1]]></URL>'."\n";
	 $xml .= '<FILE>'.$t['file'].'</FILE>'."\n";
	 $xml .= '</TEMPLATE_SETTINGS>'."\n";
	 $stream = $this->getEncodedTemplate($tid);
	 //$xml .= '<TEMPLATE_FILE><![CDATA['.$stream.']]></TEMPLATE_FILE>';
	  
	  $turl = $this->getTemplateLink($tid); 
	  

	 
	 $xml .= '<TEMPLATE_URL>'.htmlspecialchars($turl).'</TEMPLATE_URL>'."\n";
	 // all post data will be returned on the upper URL
	 $xml .= '<POST_DATA>'."\n";
	 $xml .= '<localid>'.htmlspecialchars($localid).'</localid>'."\n";
	 $xml .= '<tid>'.htmlspecialchars($tid).'</tid>'."\n";
	 $xml .= '<hash>'.htmlspecialchars($this->getFileHash($tid)).'</hash>'."\n";
	 $xml .= '<view>orderexport</view>'."\n";
	 $xml .= '<task>putfile</task>'."\n";
	 $xml .= '<format>opchtml</format>'."\n";
	 $xml .= '<option>com_onepage</option>'."\n";
	 $xml .= '<nosef>com_onepage</nosef>'."\n";
	 $xml .= '</POST_DATA>'."\n";
	 $xml .= '</XML>';
	 // debug: 
	 //file_put_contents(JPATH_SITE.DIRECTORY_SEPARATOR.'opctest.xml', $xml); 
	 return $xml;
    }
    
   /* ONLY FOR ORDER_DATA AT THE MOMENT
    * Creates an XML from getOrderData() for export
    * Will attach full template with tid
    *
    */
    function getXml2($tid, $localid=null)
    {
     
     if (!isset($localid) && (!empty($this->local_id))) $localid = $this->local_id;
     
     if (empty($localid)) 
     {
      $order_id = JRequest::getVar('order_id');
      $this->local_id = $order_id;
     }
     $data = $this->getOrderDataEx($tid, $localid);
     $doc = new DOMDocument();
     $doc->formatOutput = true;
     
     $main = $doc->createElement('XML');
     $r = $doc->createElement('ORDER_DATA');
     $main->appendChild($r);
     
     foreach ($data as $k=>$v)
     {
      if (!empty($data[$k]) || ($data[$k]==0))
      {
       $el = $doc->createElement($k);
       $el->appendChild( $doc->createTextNode($data[$k]) );
       $r->appendChild($el);
      }
     }
     
     
     $stream = $this->getEncodedTemplate($tid);
     if (!empty($stream))
   {
     $file = $doc->createElement('TEMPLATE_FILE');
  
     $file->appendChild ( $doc->createCDATASection( $stream ) );
     
     $main->appendChild( $file );
   } 
    $doc->appendChild($main);
     return $doc->saveXML();
     
    }
    function getInvisibleRow()
    {
     //return '<tr id="invisible_row" style="display: none;"><td></td><td></td><td></td></tr>';
     // all the new exports will be listed inside this html:
     return '<div id="invisible_row" style="display: none;"></div>';
    }
    
    function listSingleExport($eid, $tid, $localid, $noheader=false)
    {
       $html = $this->getExportHtml($noheader);
       // html variables are:
       // {eid}
	   // {export_link}{template_id}{local_id}
       // {template_id} 
       // {local_id}
       // {template_name}
       // {local_id_oo}
	   $eid = (int)$eid; 
	   $tid = (int)$tid; 
	   
       $tid = $tid;
       $eid = $eid;
       $tt = $this->getTemplate($tid);
	   if (empty($tt))
	   {
		   $tt = array(); 
		   $tt['tid_name'] = 'Removed Template'; 
		   $tt['removed'] = true; 
	   }
	   
	   $item = $this->getExportItem($tid, $localid);
	   

	   
       $link = $this->getPdfLink($item);
	   

	   
       $status = $this->getStatus($tid, $localid);
       $oo = str_replace('_', ', ', $localid);
       
	   $tname = $tt['tid_name'];
	   
	   
       if (empty($link)) $link = '#';
       $html = str_replace('{export_link}{template_id}{local_id}', $link, $html);
       $html = str_replace('{template_id}', $tid, $html);
       $html = str_replace('{local_id}', $localid, $html);
       $html = str_replace('{template_name}', $tname, $html);
       $html = str_replace('{local_id_oo}', $oo, $html);
       $html = str_replace('{eid}', $eid, $html);
       $tmpl = str_replace('_', '', $localid);
       if (!is_numeric($tmpl)) return "";
       $style_none = ' style="display: none;" ';
       $style_created = ' style="display: none;" ';
       $style_processing = ' style="display: none;" ';
       $style_error = ' style="display: none;" ';
       $style_recreate = ''; 
	   
	   if (!empty($tt['removed']))
	   {
		   $style_recreate = ' style="display: none;" '; 
	   }
	   
       if ($status == 'NONE') $style_none = '';
       if ($status == 'CREATED') $style_created = '';
       if ($status == 'PROCESSING' || ($status=='AUTOPROCESSING')) $style_processing = '';
       if ($status == 'ERROR') $style_error = '';
       
	   $html = str_replace('{style_recreate}', $style_recreate, $html);
       $html = str_replace('{style_none}', $style_none, $html);
       $html = str_replace('{style_created}', $style_created, $html);
       $html = str_replace('{style_processing}', $style_processing, $html);
       $html = str_replace('{style_error}', $style_error, $html);
       
       return $html;

    }
    
    function listExports()
    {
     if ($this->tableExists('onepage_exported'))
     {
     
     $q = 'select * from #__onepage_exported where 1 order by id desc ';
     $dbj = JFactory::getDBO();
     $dbj->setQuery($q);
     $res = $dbj->loadAssocList();
     $head = false;
     //echo '<table class="adminlist" style="width: 100%;" id="order_export_table">';
     echo '<div style="width: 100%;">';
     if (count($res)>0)
     {
     //echo '<tr><th>Id</th><th>Status</th><th>Template Name</th><th>Orders</th></tr>';
     // invisible first row:
     echo $this->getInvisibleRow();
     foreach ($res as $k)
     {
		echo $this->listSingleExport($k['id'], $k['tid'], $k['localid']);
     }
     }
     //echo '</table>'; 
     echo '</div>';
	 }
    }
    
	function getVdata($local_id) {
		
		$ret = array(); 
		  $data = $this->getOrderData($local_id);
	if (isset($data['contact_email_0_0']))
    $vemail = $data['contact_email_0_0'];
    else 
	if (isset($data['vendor_email_0']))
	$vemail = $data['vendor_email_0']; 
	else 
	if (isset($data['vendor_email']))
			$vemail = $data['vendor_email']; 
		else
		{
			
			$config = JFactory::getConfig();	
			if (method_exists($config, 'getValue'))
				$vemail = $config->getValue( 'config.mailfrom' ); 
			else
				$vemail = $config->get( 'mailfrom' ); 
			/*
			$sender = array( $config->getValue( 'config.mailfrom' ), $config->getValue( 'config.fromname' ) );
	  else
	  $sender = array( $config->get( 'mailfrom' ), $config->get( 'fromname' ) );
	  */
		}
		
	$ret['vemail'] = $vemail; 
	
	if (isset($data['bt_user_email_0']))
    $cemail = $data['bt_user_email_0'];
	else
	if (isset($data['bt_email_0']))
	$cemail = $data['bt_email_0'];
	else
	if (isset($data['bt_email']))
	$cemail = $data['bt_email'];
    
	
	$ret['cemail'] = $cemail; 
	
	if (isset($data['vendor_name_0_0']))
    $vname = $data['vendor_name_0_0'];
	else
	if (isset($data['vendor_company_0']))
	$vname = $data['vendor_company_0']; 
	else
	if (isset($data['vendor_company']))
	$vname = $data['vendor_company']; 
    else
	{
		$config = JFactory::getConfig();	
			if (method_exists($config, 'getValue'))
				$vname = $config->getValue( 'config.fromname' ); 
			else
				$vname = $config->get( 'fromname' ); 
	}
		$ret['vname'] = $vname; 
		return $ret; 
	}
	
    function sendMail($tid, $local_id, $silent=false, $recipients=array(), $vdata=array(), $custom_subject='', $custom_cc=array())
    {
	jimport('joomla.filesystem.file');
	
	
	if (empty($vdata)) {
			$vdata = $this->getVdata($local_id); 
	}
	
		$vemail = $vdata['vemail']; 
		
		$vname = $vdata['vname']; 
	
	
  
	
	//JFile::write(JPATH_SITE.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'log.txt', $data);
   
    $config = JFactory::getConfig();
	
	$sender = array( 
     $vemail,
     $vname, 
    );
    
 	$mailer = JFactory::getMailer();
	$mailer->setSender($sender);
	
	if (empty($recipients)) {
		$cemail = $vdata['cemail']; 
		$recipients = $recipient = array( $cemail, $vemail );
		 if (empty($vemail) || (empty($cemail))) return false;
	}
	else {
		$recipient = $recipients; 
	}
	
	
	// http://docs.joomla.org/How_to_send_email_from_components
	$mailer->addRecipient($recipient);
	
	if (!empty($custom_cc)) {
	  $mailer->addCc($custom_cc); 
	}
	
	$tt = $this->getTemplate($tid);
	if (!empty($tt['tid_emailbody']))
	$body = $tt['tid_emailbody'];
	else
	$body   = "A new file was sent to you by shop owner.";
	
	if (empty($custom_subject)) {
	if (!empty($tt['tid_emailsubject']))
	$subject = $tt['tid_emailsubject'];
	else 
	$subject = 'New file';
	}
	else {
		$subject = $custom_subject; 
	}


	$mailer->setSubject($subject);
	$mailer->setBody($body);
	// Optional file attached
	$item = $this->getExportItem($tid, $local_id);
	//echo 'File: '.urldecode($item['path']);
	if (file_exists(urldecode($item['path'])))
	{
	$mailer->addAttachment(urldecode($item['path']));
	$send = $mailer->Send();
	if (!$silent)
	if ( $send !== true ) {
      echo 'Error sending email: ' . $send->message. '<br />';
	} else {
      echo 'Mail sent to '.implode(',',$recipients). '<br />';
	  
      return true;
	}
	}
	else
	{
	 if (!$silent)
	 echo 'Exported file not found! <br />';
	}
	return false;
    }
    
    function listExports2()
    {
     if ($this->tableExists('onepage_exported'))
     {
         $component_name = JRequest::getVar('option', 'com_onepage');
        // no hacking here:
        $component_name = urlencode(urlencode($component_name));

     
     $q = 'select * from #__onepage_exported where 1 order by id desc ';
     $dbj = JFactory::getDBO();
     $dbj->setQuery($q);
     $res = $dbj->loadAssocList();
     $head = false;
     echo '<table class="adminlist" style="width: 100%;">';
     if (count($res)>0)
     {
     echo '<tr><th>Id</th><th>Status</th><th>Template Name</th><th>Orders</th></tr>';
     foreach ($res as $k)
     {
      echo '<tr>';
     // foreach ($k as $val)
     {
      $tid = $k['tid'];
      $tt = $this->getTemplate($tid);
      $t = $tt;
      


      $oo = str_replace('_', ', ', $k['localid']);
      $link = $this->getPdfLink($k);
	  $order_id = $k['localid'];
	   
	   $status = $this->getStatus($t['tid'], $order_id);
	   if ($status == 'AUTOPROCESSING') $status = 'PROCESSING';
	 $status_txt = $this->getStatusTxt($t['tid'], $order_id);
	 $specials = $this->getSpecials($t['tid'], $order_id);

      echo '<td>'.$k['id'].'</td>';
      echo '<td>';
      // status
      	 ?>
      
	 <div id="tid_<?php echo $t['tid'].'_'.$k['localid']; ?>_div">
	 <?php
	  $lin = '<a href="'.$this->getExportItemLink($t['tid'], $order_id).'" id="tid_'.$t['tid'].'_'.$k['localid'].'" onclick="'."javascript:return op_runCmd('sendXmlMulti', this, '".$k['localid']."');".'" >';
	  //$plin = '<a href="#" id="tid_'.$t['tid'].'" >';
	  // status: NONE
	  ?><div id="tiddiv_<?php echo $t['tid'].'_'.$k['localid']; ?>_NONE" <?php if ($status != 'NONE') echo ' style="display: none;" '; ?>><?php
 	  echo $lin.'<img src="'.$this->getUrl().'components/com_onepage/assets/img/new.png" alt="'.$status_txt.'" title="'.$status_txt.'" /></a>';
	  ?></div><?php
	  $item = $this->getExportItem($t['tid'], $order_id);
	  $link = $this->getPdfLink($item);
	  if (empty($link)) $link = '#';
	  $created_html = '<a href="'.$link.'" id="tidpdf_'.$t['tid'].'_'.$k['localid'].'" target="_blank"'." ><img id='status_img' src='".$this->getUrl()."components/com_onepage/assets/img/pdf_button.png' alt='".$status_txt."' title='".$status_txt."' />".'</a>';
	  $processing_html2 = '<a href="#" id="tid_'.$t['tid'].'_'.$k['localid'].'_2" onclick="javascript:return op_runCmd('."'sendXmlMulti'".', this, '."'".$k['localid']."'".');"'." ><img id='status_img2' src='".$this->getUrl()."/administrator/components/".$component_name."/components/com_onepage/assets/img/process.png' alt='RECREATE' title='RECREATE' /></a>";
	  $created_html = $created_html.$processing_html2;
   	  ?><div id="tiddiv_<?php echo $t['tid'].'_'.$k['localid']; ?>_CREATED" <?php if ($status != 'CREATED') echo ' style="display: none;" '; ?>><?php
	  echo $created_html;
	  ?></div><?php
	  $processing_html = $lin."<img src='".$this->getUrl()."components/com_onepage/assets/img/mootree_loader.gif' alt='".$status_txt."' title='".$status_txt."' /></a>";
	  ?><div id="tiddiv_<?php echo $t['tid'].'_'.$k['localid']; ?>_PROCESSING" <?php if ($status != 'PROCESSING') echo ' style="display: none;" '; ?>><?php
	  echo $processing_html;
	  ?></div><?php
      $error_html = $lin."<img src='".$this->getUrl()."components/com_onepage/assets/img/remove.png' alt='".$status_txt."' title='".$status_txt."' /></a>";
	  ?><div id="tiddiv_<?php echo $t['tid'].'_'.$k['localid']; ?>_ERROR" <?php if ($status != 'ERROR') echo ' style="display: none;" '; ?>><?php
	  echo $error_html;
	  ?></div><?php
	  echo '</td>';
      echo '<td>'.$tt['tid_name'].'</td>';
      echo '<td> Orders: '.$oo.'</td>';
      /*
      if (!empty($link))
	  echo '<a href="'.$link.'">'.$tid.': '.$tt['tid_name'].' Orders: '.$oo.'</a>';
	  else
	  echo 'Orders: '.$oo;
      echo '</td>';
      */
     }
      echo '</tr>'; // end of row
     }
     
     }
     echo '</table>';
     } 
    }
    function getExportHtml($noheader = false)
    {
     $component_name = JRequest::getVar('option', 'com_onepage');
        // no hacking here:
        $component_name = urlencode(urlencode($component_name));
    
     
      $html = '<div id="eid_{eid}">';
      if ($noheader == true) $html = '';
      $html .= '
      <div style="float: left; width: 2%;">{eid}</div>
      <div style="float: left; width: 100px;">
	  <div id="tid_{template_id}_{local_id}_div">
	  <div id="tiddiv_{template_id}_{local_id}_NONE" {style_none}>
 	  	<a href="{export_link}{template_id}{local_id}" id="tid_{template_id}_{local_id}" onclick="javascript:return op_runCmd(\'sendXmlMulti\', this, \'{local_id}\');">
 	  	<img src="'.$this->getUrl().'components/com_onepage/assets/img/new.png" alt="CREATE" title="CREATE" />
 	  	</a>
	  </div>
	  <div id="tiddiv_{template_id}_{local_id}_CREATED" {style_created}>
	  	<a href="{export_link}{template_id}{local_id}" id="tidpdf_{template_id}_{local_id}" target="_blank">
	  	<img src="'.$this->getUrl().'components/com_onepage/assets/img/pdf_button.png" alt="PROCESSING" title="PROCESSING" />
	  	</a>
	  	<a href="#" id="tid_{template_id}_{local_id}_2" {style_recreate} onclick="javascript:return op_runCmd(\'sendXmlMulti\', this, \'{local_id}\');">
	  	<img src="'.$this->getUrl().'components/com_onepage/assets/img/process.png" alt="RECREATE" title="RECREATE" />
	  	</a>
	  </div>
	  <div id="tiddiv_{template_id}_{local_id}_PROCESSING" {style_processing}>
	  	<a href="#" id="tid_{template_id}_{local_id}_3" onclick="javascript:return op_runCmd(\'sendXmlMulti\', this, \'{local_id}\');">
	  	<img src="'.$this->getUrl().'components/com_onepage/assets/img/mootree_loader.gif" alt="RECREATE" title="RECREATE"/>
	  	</a>
	  </div>
	  <div id="tiddiv_{template_id}_{local_id}_ERROR" {style_error}>
	  	<a href="#" id="tid_{template_id}{local_id}_3" onclick="javascript:return op_runCmd(\'sendXmlMulti\', this, \'{local_id}\');">
      	<img src="'.$this->getUrl().'components/com_onepage/assets/img/remove.png" alt="TRY AGAIN" title="TRY AGAIN" />
      	</a>
	  </div>
	  </div>
	  </div>
      <div style="width: 300px; float: left;">{template_name}&nbsp;</div>
      <div style="float: left;">Orders: {local_id_oo}</div>';
      if ($noheader != true)
      {
      $html .= '</div>
      <br style="clear: both;"/>'; // end of row
      }
	 
	 return $html;
    
    }
    
    
    function getExportHtmlTable($noheader = false)
    {
    
     $component_name = JRequest::getVar('option', 'com_onepage');
        // no hacking here:
        $component_name = urlencode(urlencode($component_name));
        
     
      $html = '<tr id="eid_{eid}">';
      if ($noheader === true) $html = '';
      $html .= '
      <td>{eid}</td>
      <td>
	  <div id="tid_{template_id}_{local_id}_div">
	  <div id="tiddiv_{template_id}_{local_id}_NONE" {style_none}>
 	  	<a href="{export_link}{template_id}{local_id}" id="tid_{template_id}_{local_id}" onclick="javascript:return op_runCmd(\'sendXmlMulti\', this, \'{local_id}\');">
 	  	<img src="'.$this->getUrl().'components/com_onepage/assets/img/new.png" alt="CREATE" title="CREATE" />
 	  	</a>
	  </div>
	  <div id="tiddiv_{template_id}_{local_id}_CREATED" {style_created}>
	  	<a href="{export_link}{template_id}{local_id}" id="tidpdf_{template_id}_{local_id}" target="_blank">
	  	<img src="'.$this->getUrl().'components/com_onepage/assets/img/pdf_button.png" alt="PROCESSING" title="PROCESSING" />
	  	</a>
	  	<a href="#" id="tid_{template_id}_{local_id}_2" onclick="javascript:return op_runCmd(\'sendXmlMulti\', this, \'{local_id}\');">
	  	<img src="'.$this->getUrl().'components/com_onepage/assets/img/process.png" alt="RECREATE" title="RECREATE" />
	  	</a>
	  </div>
	  <div id="tiddiv_{template_id}_{local_id}_PROCESSING" {style_processing}>
	  	<a href="#" id="tid_{template_id}_{local_id}_3" onclick="javascript:return op_runCmd(\'sendXmlMulti\', this, \'{local_id}\');">
	  	<img src="'.$this->getUrl().'components/com_onepage/assets/img/mootree_loader.gif" alt="RECREATE" title="RECREATE"/>
	  	</a>
	  </div>
	  <div id="tiddiv_{template_id}_{local_id}_ERROR" {style_error}>
	  	<a href="#" id="tid_{template_id}{local_id}_3" onclick="javascript:return op_runCmd(\'sendXmlMulti\', this, \'{local_id}\');">
      	<img src="'.$this->getUrl().'components/com_onepage/assets/img/remove.png" alt="TRY AGAIN" title="TRY AGAIN" />
      	</a>
	  </div>
	  </div>
	  </td>
      <td>{template_name}</td>
      <td> Orders: {local_id_oo}</td>';
      if ($noheader!==true) $html .= '
      </tr>'; // end of row
	 
	 return $html;
    
    }
   	function getTemplateLink($tid)
	{
	    $component_name = 'com_onepage'; 
        // no hacking here:
        $component_name = urlencode(urlencode($component_name));
        
	  $hash = $this->getFileHash($tid);
	  
	  $root = $this->getUrl(); 
	  

	  $url = $root.'index.php?option='.$component_name.'&view=orderexport&task=getfile&tid='.$tid.'&hash='.$hash.'&nosef=1&tmpl=component&format=opchtml';
	  return $url;
	}
    function getUrl()
	{
	  static $myurl; 
	  if (!empty($myurl)) return $myurl; 
	   		  if (!class_exists('VmConfig'))
		  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		 
		  VmConfig::loadConfig(); 
	    
		$useSSL = VmConfig::get('useSSL', 0);
	   
	   $root = Juri::root(); 
	   $root = str_replace('/administrator', '', $root); 
	   
	   if (substr($root, -1) !== '/') $root .= '/'; 
	   
	   
	   if ($useSSL)
	   $root = str_replace('http:', 'https:', $root); 
	   

	   
	   $myurl = $root; 

	   return $root; 
	}
	function updateFileName($tid, $fname)
	{
		
		
		
	 $tid = (int)$tid; 
	 $dbj = JFactory::getDBO();
	 $tid = $dbj->escape($tid);
	 
	 $q = "select tid from #__onepage_export_templates where file = '".$dbj->escape(urlencode($fname))."' limit 0,1"; 
	 $dbj->setQuery($q); 
	 $tid2 = $dbj->loadResult(); 
	 
	 if (!empty($tid2))
	 if ($tid2 != $tid)
	  {
	      if (file_exists(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'export'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$fname))
		  {
		    return 'Another theme is using this filename !'; 
		  }
		  else
		  {
		    $q = "delete from #__onepage_export_templates where file = '".$dbj->escape(urlencode($fname))."' limit 1"; 
			$dbj->setQuery($q); 
			$dbj->query(); 
			
		  }
	  }
	 $q = "update #__onepage_export_templates set file = '".$dbj->escape(urlencode($fname))."' where tid='".(int)$tid."'";
	 $dbj->setQuery($q);
	 $dbj->query();
	 return $dbj->getErrorMsg();
	}
	
	// $r input array from loadAssoc of table onepage_exported
	function getPdfLink($r)
	{
	 $component_name = JRequest::getVar('option', 'com_onepage');
        // no hacking here:
        $component_name = urlencode(urlencode($component_name));
        
	  $path = urldecode($r['path']);
	  $link = '';
      if (file_exists($path))
	  {
	   $link = 'index.php?option='.$component_name.'&view=order_details&task=ajax&orderid='.$r['localid'].'&ajax=yes&format=raw&tmpl=component&cmd=showfile&fid='.$r['id'];
	  }
	  return $link;
	}
	
	function getFileHeader($file)
	{
	 $path_parts = pathinfo($file); 
     $ext = strtolower($path_parts["extension"]); 
     
     switch ($ext) {
      case "pdf": $ctype="application/pdf"; break;
      case "ods": $ctype="application/vnd.oasis.opendocument.spreadsheet"; break;
      case "exe": $ctype="application/octet-stream"; break;
      case "zip": $ctype="application/zip"; break;
      case "doc": $ctype="application/msword"; break;
      case "xls": $ctype="application/vnd.ms-excel"; break;
      case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
      case "gif": $ctype="image/gif"; break;
      case "png": $ctype="image/png"; break;
      case "jpeg":
      case "jpg": $ctype="image/jpg"; break;
      case "xml": $ctype="application/xml"; break;
      default: $ctype="application/force-download";
    }
    return $ctype; 
	}
	
    function showFile($order_id, $fid)
	{
	 
	 @ob_get_clean();@ob_get_clean();@ob_get_clean();@ob_get_clean();@ob_get_clean();
	 // autorization should be here!
	 $user = JFactory::getUser();
	 // autorization is done by MVC
	 
	 $data = $this->getExportItemFile($fid);
	 if (!empty($data))
	 {
	  $pdf = urldecode($data['path']);
	  if (file_exists($pdf))
	  {
	  $ctype = $this->getFileHeader($pdf);
	  $pi = pathinfo($pdf);
	  $filename = $pi['basename'];
	  $fsize = filesize($pdf);
	  header("Pragma: public"); // required
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Cache-Control: private",false); // required for certain browsers
      header("Content-Type: $ctype");
      header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
      header("Content-Transfer-Encoding: binary");
      header("Content-Length: ".$fsize); 
	  readfile($pdf);
	  JFactory::getApplication()->close(); 
	  die();
	  }
	 }
	 die('Cannot find the requested file!');
	}

	
	
    function getEncodedTemplate($tid)
    {
     $t = $this->getTemplate($tid);
     $file = JPATH_COMPONENT_ADMINISTRATOR_ONEPAGE.DIRECTORY_SEPARATOR.'export'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$t['file'];
     //$handle = fopen($file, "rb");
     //$base64stream = base64_encode(fread($handle, filesize($file)));
     return base64_encode(file_get_contents($file));
    }
	// mal by vracat iba URL adresu aj s hash pre zobraznie daneho suboru, v ziadnom pripade nie html <a href...     
    function getFileHref($tid, $localid=null)
    {
     $component_name = JRequest::getVar('option', 'com_onepage');
        // no hacking here:
        $component_name = urlencode(urlencode($component_name));
        
   	 $hash = getFileHash($tid); 
     if (defined(SECUREURL))
     {
       if (substr(SECUREURL, strlen(SECUREURL)-1, 1)!='/') 
       $url = SECUREURL.'/';
       else $url = SECUREURL;
     }
     $kk = URL;
     if (empty($url) && (!empty($kk)))  
     {
       if (substr(URL, strlen(URL)-1, 1)!='/') 
       $url = URL.'/';
       else $url = URL;
     }
     if (empty($url)) $url = JURI::base();
	 $href = $this->getTemplateLink(urlencode(trim($tid))); 
	 
     
	 
     return $href;

    }
    
    // returns array of specials, first is AI
    function getSpecials($tid, $localid)
    {
      $t = $this->getTemplate($tid);
      if (empty($t['tid_ai']))
      {
      if (empty($t['tid_special'])) return array();
      if (empty($t['tid_specials'])) return array();
	  }
	  else
	  {
	  }
      $dbj = JFactory::getDBO();
      $res = $this->getExportItem($tid, $localid);
      $specials = array();
      if (!empty($res))
      {
       $specials[0] = $res['ai'];
       $other = $res['specials'];
       $arr = explode('||', $other);
       if (!empty($arr))
       foreach ($arr as $v)
       {
        $specials[] = urldecode($v);
       }
      }
      else
      {
       $specials[0] = $this->getNextAi($tid, $localid);
      } 
      
      for ($i=0; $i<$t['tid_specials']; $i++)
      {
       if (empty($specials[$i])) $specials[$i] = '';
      }
      return $specials;
    }
    // sets array of specials, first is AI
    function setSpecials($tid, $localid, $arr, $status='NONE')
    {
      if (!$this->tableExists('onepage_exported'))
      {
        $this->getExportTemplates();
      }
      if ($this->tableExists('onepage_exported'))
      {
		  
		$r = $this->getExportItem($tid, $localid); 
      
      if (empty($r))
      {
       $this->setStatus($tid, $localid, $status);
      }
		  
      if (empty($arr)) return;
       $dbj = JFactory::getDBO();
      $ups = '';
      $specials = ", `specials` = '";
      $str = '';
      foreach ($arr as $k=>$v)
      {
       if ($k==0) $ups .= " `ai` = '".$dbj->escape($v)."' ";
       if ($k>0)
        {
          $str .= urlencode($v).'||';
        }
      }
      $ups = $ups.$specials.$dbj->escape($str)."'";
      $q = "update #__onepage_exported set ".$ups." where localid = '".$dbj->escape($localid)."' and tid = '".$dbj->escape($tid)."' ";
      $dbj->setQuery($q);
      $dbj->query();
      $msg = $dbj->getErrorMsg();
      if (!empty($msg)) { echo $msg; return; }
      }
	  
	  
	  
      
    }
    
	function setCustomSpecial($tid, $localid, $special, $status='NONE', $ai=null, $path='', $cdate=null) {
		
		if (empty($tid)) return; 
		if (empty($localid)) return; 
		if (empty($cdate)) $cdate = time(); 
		
		$q = 'select * from #__onepage_exported where tid = '.(int)$tid.' and localid = '.(int)$localid.' limit 0,1'; //.' and status = \''.$db->escape($status).'\''; 
		$db = JFactory::getDBO(); 
		$db->setQuery($q); 
		$res = $db->loadAssoc(); 
		
		
		
		$specials = $special; 
		
		$cols = array(); 
			//$cols['id'] = 'id'; $vals['id'] = 'NULL'; 
			$cols['localid'] = 'localid'; $vals['localid'] = (int)$localid; 
			$cols['tid'] = 'tid'; $vals['tid'] = (int)$tid; 
			$cols['status'] = 'status'; $vals['status'] = '\''.$db->escape($status).'\''; 	
			$cols['ai'] = 'ai'; $vals['ai'] = '\''.$db->escape(urlencode($ai)).'\''; 
			$cols['specials'] = 'specials'; $vals['specials'] = '\''.$db->escape(urlencode($special)).'\''; 
		    
		if (empty($res)) {
			$cols['cdate'] = 'cdate'; $vals['cdate'] = (int)$cdate; 
			$q = 'insert into #__onepage_exported '; 
			
			$q .= ' ('.implode(',', $cols).') values '; 
			$q .= ' ('.implode(',', $vals).')'; 
			$db->setQuery($q); 
			$db->query(); 
			//
		}
		else {
			
			
			$id = (int)$res['id']; 
			$q = 'update #__onepage_exported '; 
			$up = array(); 
			foreach ($vals as $col_name => $val) {
				$up[] = ' `'.$col_name.'` = '.$val; 
			}
			$q .= 'set '.implode(',', $up); 
			$q .= ' where id = '.(int)$id; 
			
			$db->setQuery($q); 
			$db->query(); 
			
			
		}
		
		
	
	}
	
    function setStatus($tid, $localid, $status, $path="")
    {
     if ($this->tableExists('onepage_exported'))
     {
     if (empty($localid)) return;
     
     $db = JFactory::getDBO();
     
     
     $q = 'select * from `#__onepage_exported` where `tid`='.(int)$tid.' and `localid` = "'.$db->escape($localid).'" limit 0,1';
     $db->setQuery($q);
     $r = $db->loadAssoc();
	 if (empty($r['ai'])) {
	 $ai = $this->getNextAI($tid, $localid);
	 if (empty($ai)) $ai = $localid;
     if (empty($r))
     {
      $q = "insert into #__onepage_exported (`id`, `tid`, `localid`, `status`, `ai`, `specials`, `path`, `cdate`) values (NULL, '".(int)$tid."', '".$db->escape($localid)."', '".$db->escape($status)."', '".$db->escape($ai)."', '', '".$db->escape($path)."', '".time()."') ";
      $db->setQuery($q);
      $db->query();
      echo $db->getErrorMsg();
     }
	 }
     else
     {
      if (!empty($r['id']))
      {
      $q = "update #__onepage_exported set `status` = '".$db->escape($status)."', path = '".$db->escape($path)."', `cdate`='".time()."' where `id` = '".(int)$r['id']."' ";
      $db->setQuery($q);
      $db->query();
      echo $db->getErrorMsg();
      } else { echo 'empty id in set state !!! ('.$localid.' '.$tid.')'; }
     }
     echo $db->getErrorMsg();
     }
    }
   function getStatus($tid, $localid=null)
    {
     if ($this->tableExists('onepage_exported'))
     {
     if (!isset($localid) && (!empty($this->local_id))) $localid = $this->local_id;
     $dbj = JFactory::getDBO();
     
     $q = 'select status from #__onepage_exported where localid = "'.$dbj->escape($localid).'" and tid = "'.$dbj->escape($tid).'" limit 0,1';
     $dbj->setQuery($q);
     $r = $dbj->loadResult();
     if (empty($r)) return 'NONE';
     return $r;
     }
     return 'NONE';
    }

   function getStatusTxt($tid, $localid=null)
    {
	 $r = $this->getStatus($tid, $localid);
	 
     if (empty($r)) return 'CREATE';
     if ($r == 'CREATED') return 'VIEW';
     if ($r == 'PROCESSING' || ($r == 'AUTOPROCESSING')) return 'RECREATE';
     if ($r == 'AUTOPROCESSING') return 'RECREATE';
     if ($r == 'NONE') return 'CREATE';
     return $r;
    }
   

   function getExportItemLink($tid, $localid)
   {
    $component_name = JRequest::getVar('option', 'com_onepage');
        // no hacking here:
        $component_name = urlencode(urlencode($component_name));
        
	$d2 = $this->getExportItem($tid, $localid);
	if (!empty($d2) && (!empty($d2['path']))) return 'index.php?option='.$component_name.'&amp;view=order_details&amp;format=raw&amp;tmpl=component&amp;task=ajax&amp;orderid='.$localid.'&amp;ajax=yes&amp;cmd=showfile&amp;fid='.$d2['id'];
	return '#';
   }    
   
   function getExportItem($tid, $localid)
   {
    if ($this->tableExists('onepage_exported'))
    {
     $dbj = JFactory::getDBO();
     $q = 'select * from #__onepage_exported where localid = "'.$dbj->escape($localid).'" and tid = "'.$dbj->escape($tid).'" limit 0,1 ';
     $dbj->setQuery($q);
     return $dbj->loadAssoc();
    }
    return array();
   }
   
   function getExportItemFile($fid)
   {
    if ($this->tableExists('onepage_exported'))
    {
     $dbj = JFactory::getDBO();
     $q = 'select * from #__onepage_exported where id = "'.$dbj->escape($fid).'" limit 0,1 ';
     $dbj->setQuery($q);
     return $dbj->loadAssoc();
    }
    return array();
   }

// reference to data array from getOrderData
// adds more formatting stuff to XML:
//  tid_special (checkbox 0/1/neexistuje) – vytvori specialny input pre napr. Cislo faktury
/* tid_ai – (checkbox 0/1/neexistuje) automaticke cislovanie tid_special (autoincrement)
* tid_num - (checkbox 0/1/neexistuje)  pre kazdu ciselnu hodnotu vytvori spatne polia a prida nuly
*                 (napr. Cislo uctu v seku, ale bude to pre vsetky)
* tid_nummax – pocet cislic pre pridavanie nul (napr. 5, cize pre 123 by to bolo array ('3','2','1','0','0')
* tid_itemmax – max pocet poloziek objednavky na fakture (ak je v sablone napr _99)
* tid_back (checkbox 0/1/neexistuje) – vsetky cisla a AJ texty zkonvertuje do formatu priklad:
*              pre string hello to bude array ('o', 'l', 'l', 'e', 'h')
* tid_forward 
*/
function getOrderDataEx($tid, $localid, $special_value = null, $ind=0)
{
 $mosConfig_offset = 0; 
 $data = $this->getOrderData($localid, $ind);
 $special_value = $this->getSpecials($tid, $localid); 
 $t = $this->getTemplate($tid);
 if (!is_array($t)) {
	 
 }
 if (isset($data['virtuemart_order_id_0']))
 $data['order_id_0'] = $data['virtuemart_order_id_0']; 
 // add new vars
 if (empty($localid))
 {
  $data['special_value_'.$ind.'_'.'0'] = 'special_value1';
 }
 else
 if (!empty($t['tid_special']))
 {
  if (!empty($special_value))
  {
   foreach ($special_value as $k=>$v)
   {
    $data['special_value_'.$ind.'_'.$k] = $v;
   }
  }
  else
  {
   if (empty($data['special_value_0_0']))
   $data['special_value_0_0'] = '';
  }
 }
 
 // outside foreach
  if (empty($localid))
 {
  $data['special_value_ai_'.$ind] = 'special_value1';
 }
 else
 if (!empty($t['tid_ai']) || (!empty($t['tid_foreign'])))
 {
   
   $data['special_value_ai_'.$ind] = $this->getNextAI($tid, $localid);
   
   
   // c is number of orders exported for this template = auto increment value
 }
 
 if (!empty($data))
 {
    if (empty($data['order_tax']) && (!empty($data['order_tax_0'])))
	{
		$data['order_tax'] = $data['order_tax_0']; 
	}		
	if (empty($data['cdate_'.$ind]))
	{
		
		
   $dbj = JFactory::getDBO();
   $q = 'select `cdate` from `#__onepage_exported` where `tid` = "'.$tid.'" and `localid` = "'.$localid.'" ';
   $dbj->setQuery($q);
   $res = $dbj->loadResult();
		if (!empty($res))
		{
			$data['cdate_'.$ind] = (int)$res; 
		}
		else
			if (!empty($data['special_value_'.$ind]))
			{
				$data['cdate_'.$ind] = time(); 
			}
		else
		if ((!empty($data['created_on_'.$ind])) && ($data['created_on_'.$ind] != '0000-00-00 00:00:00'))
		{
			$timeX = @strtotime($data['created_on_'.$ind]); 
			if (!empty($timeX))
			$data['cdate_'.$ind]  = $timeX; 
		}
		
		
		if (empty($data['cdate_'.$ind])) $data['cdate_'.$ind] = time(); 
		
	}
	
	
	
 foreach ($data as $key2=>$r2)
 {
  if (strpos($key2, 'ship_method_id')!==false)
  {
   $a = explode('|', urldecode($r2));
  	foreach ($a as $kk=>$s)
  	{
  	 $data[$key2.'_'.$kk] = $s;
  	}
  }
  
  if ((strpos($key2, 'address')!==false) || (strpos($key2, 'city')))
  {
    $this->parseStreet($r2, $key2, $data);
  }
  
  $dbj = JFactory::getDBO();
  // search for virtuemart variables country name and state name
  /*
  if (strpos($key2, 'order_tax_details')!==false)
  {
	
    $details = @unserialize($r2);

	$di = 0; 
	if (!empty($details) && (is_array($details)))
	 {
	   foreach ($details as $tdid => $vd)
	   {
	     $data[$key2.'_parsedrate_'.$di] = $tdid;
		 $data[$key2.'_parsedtax_'.$di] = $vd;
		 $di++;
	   }
	 }
  }
  else
	  */
  if (strpos($key2, 'country')!==false)
  {
  
   if (is_numeric($r2))
   {
     $col = 'virtuemart_country_id'; 
	 
   
    
    if (!empty($col))
    {
     $q = 'select * from #__virtuemart_countries where '.$col.' = "'.$dbj->escape($r2).'" ';
     $dbj->setQuery($q);
     $res = $dbj->loadAssoc();
    }
    else $res = '';
    if (!empty($res))
    {
    $data[$key2.'_named'] = $res['country_name'];
    $country_id = $res['virtuemart_country_id'];
	
	$res['country_id'] = $country_id; 
	
    $tk = str_replace('country', 'state', $key2);
    if (isset($data[$tk]) && (!empty($country_id)))
    {
     $state = $data[$tk];
	 if (is_numeric($state))
	 {
	  $col = 'virtuemart_state_id'; 
	 }
	 else
     if (strlen($state) == 2) $col = 'state_2_code';
     else
     if (strlen($state) == 3) $col = 'state_3_code';
     else $col = '';
     if (!empty($col))
     {
     $q = 'select state_name from #__virtuemart_states where '.$col.' = "'.$dbj->escape($state).'" ';
     $dbj->setQuery($q);
     $res = $dbj->loadResult();
     if (!empty($res))
     $data[$tk.'_named'] = $res;
     else
     $data[$tk.'_named'] = '';
     }
     else
     $data[$tk.'_named'] = '';
    }
    }
    else $data[$key2.'_named'] = '';
   }
  }
  
  
  if (strpos($key2, 'cdate')!==false)
  {
   
   $date = new OPCDate($data[$key2]);

   
   $data[$key2.'_named'] = $date->toRFC822();
   $data[$key2.'_vmdate'] = $date->toRFC822(); //vmFormatDate( $data[$key2] + $mosConfig_offset );
   $data[$key2.'_iso'] = date("Y-m-d",$data[$key2] + $mosConfig_offset);
  }
  
  
 }
 }
 $now = new OPCDate(time()); 
 $data['export_created_date_unix'] = time();
 $data['export_created_date_vm'] = $now->toRFC822(true); //vmFormatDate(time()+$mosConfig_offset, $VM_LANG->_('DATE_FORMAT_LC'));
 $time = time(); 
 $date = new OPCDate($time);
 
 
 
 $data['export_created_date_joomla'] = $date->toRFC822();
 $newdata = $data;
 
 if (empty($localid)) $t['tid_itemmax'] = 9;
 if (empty($localid)) $t['tid_nummax'] = 9;
 // change char settings
 if (!empty($data))
 foreach ($data as $key=>$r)
 {
	if (is_array($r))
	{
		unset($data[$key]); 
		continue; 
	}
	if (stripos($key, 'varsToPushParam'))
	{
		unset($data[$key]); 
		continue; 
	}
	 
 if (isset($t['tid_num']))
 if (($t['tid_num'] == 1) && (!empty($t['tid_nummax'])))
 {
   if (is_numeric($r))
   {
      $r2 = (int)$r;
      if ($r2 == $r)
      {
        $nr = $this->mb_strrev($r2, 'UTF-8');
        $new = $this->addZeroes($nr, $t['tid_nummax']);
        $this->getArray($key, $new, $newdata, $t['tid_nummax']);
      }
   }
 }
 if (!empty($t['tid_back']) || (empty($localid)))
 {
  $rev = $this->mb_strrev($r, 'UTF-8');
  $kk = $key.'_back';
  $this->getArray($kk, $rev, $newdata, $t['tid_nummax']);
 }
 if ((!empty($t['tid_forward'])) || (empty($localid)))
 {
  $kk = $key.'_forward';
  $this->getArray($kk, $r, $newdata,  $t['tid_nummax']);
 }
 if (empty($localid)) $t['tid_itemmax'] = 99;
 
 if ((!empty($t['tid_itemmax']) && is_numeric(trim($t['tid_itemmax']))))
 {
  
  $max = trim($t['tid_itemmax']);
  if (strpos($key, '_0')===(strlen($key)-2))
  {
   $rawkey = str_replace('_0', '', $key);
   for ($i=1; $i<=$max; $i++)
   {
    if (!isset($data[$rawkey.'_'.$i]))
    {
     // will insert empty values for numbered items
     $newdata[$rawkey.'_'.$i] = '';
    }
   }
  }
 }

 
 }
 /*
 foreach ($newdata as $k=>$v)
 {
	 if (stripos($k, 'date')!==false)
	 {
		 $date = $newdata[$k]; 
		 
		 
	 }
 }
 */
foreach ($newdata as $key=>$r)
 {
	if (is_array($r))
	{
		unset($newdata[$key]); 
		continue; 
	}
	if (stripos($key, 'varsToPushParam'))
	{
		unset($newdata[$key]); 
		continue; 
	}
 }
 return $newdata;
}

function processTemplate($tid, $order_id, $specials=array(), $status='PROCESSING')
{
	
	
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
 	 $tt = $this->getTemplate($tid);
 	 $this->setStatus($tid, $order_id, $status);
 	 echo $tt['tid_type'];
	 
	
	 
 	 if ($tt['tid_type'] == 'ORDER_DATA')
 	 {
	 
	 $this->setSpecials($tid, $order_id, $specials, $status);

	 
	 $xml = $this->getXml($tid, $order_id);
	 
	 $hash = $this->getFileHash($tid);
	 
	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	 $debug = OPCconfig::getValue('xml_export', 'xml_debug', 0, false, false); 	 
	 
	 jimport( 'joomla.filesystem.file' );
	 if (!empty($debug))
	 {
		//JFile::write(JPATH_ROOT.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'test.xml', $xml); 
	    
	 }
	 
	 $ex = OPCconfig::getValue('xml_export', 'export_dir', 0, $defualt, false); 
	 
	 
	 $datadir = $this->prepareDirectory('data'); 
	 $hash = hash('sha512', $xml); 
	 $sf = $datadir.DIRECTORY_SEPARATOR.$order_id.'_'.$tid.'_'.$hash.'.xml'; 
	 JFile::write($sf, $xml); 
	 
	 
	 
	// $XPost = 'localid='.$order_id.'&hash='.$hash.'&tid='.$tid.'&xml='.urlencode((string)$xml);
	 if (strnatcmp(phpversion(),'5.5.0') >= 0) {
	  
	  //$XPost = 'extra=1&xml='.urlencode((string)$xml); 
	  $XPost = array('extra_info'=>'none');
	  $f = array('filepath'=>$sf, 'mime'=>'text/xml'); 
	  $this->sendData($XPost, $tid, $order_id, $f);
	  
	 }
	 else {
		
		 $XPost = 'extra=1&xml='.urlencode((string)$xml); 
		 $this->sendData($XPost, $tid, $order_id);
	 }
	 
	
	 
	
	 }
	 else 
	 {
	  if ($tt['tid_type'] == 'ORDER_DATA_TXT')
	  {
	    $this->setSpecials($tid, $order_id, $specials, $status);
	    $data = $this->getOrderDataEx($tid, $order_id);
	    $this->processTxtTemplate($tid, $order_id, $data);
	  }
	  
	 }
	 
	  

}

	function checkFile()
	{
	 // ALTER TABLE `rupostel_test`.`jos_onepage_exported` ADD INDEX ( `cdate` ) 
	 // $sql = "ALTER TABLE `rupostel_test`.`jos_onepage_exported` ADD UNIQUE (`status`, `localid`, `cdate`)";
	 $localid = JRequest::getVar('localid');
	 $db = JFactory::getDBO(); 
	 $t = time() - 60*60*24;
	 $dbj = JFactory::getDBO();
	 
	 //echo 'Localid: '.$localid.'<br />';
	 $q = "select * from `#__onepage_exported` where (`localid` = '".$db->escape($localid)."') ";
	 if (empty($localid))
	 $q = "select * from `#__onepage_exported` where (`cdate` > '".(int)$t."') ";
	 // `status` = 'CREATED' and 
	 $dbj->setQuery($q);
	 $res = $dbj->loadAssocList();
	 echo $dbj->getErrorMsg();
	 // echo $q;

	 
	 
	 foreach($res as $k=>$r)
	 {
	  $link = $this->getPdfLink($r);
	  //echo $link;
	  //if (!empty($link))
	  { 
	  $status = $r['status'];
	  $trow = $this->listSingleExport($r['id'], $r['tid'], $r['localid'], true);
	  //$trow = '<!--//<![CDATA['.$trow.'//]]-->';
	  echo '<span style="display: none;">DATAS::'.$r['tid'].'::'.$link.'::'.$status.'::'.$r['localid'].'::'.$r['id'].'::'.$trow.'::DATAE</span>';
	  //echo 'DATAS::'.$r['tid'].'::'.$link.'::DATAE';
	  }
	  
	 }
	 
	 return;
	}


function formatDateKey($key, $value)
{
 
}
// return last autoincrement value of template, if localid is found it will return its pre-set value
// if you get error 500 from apache, we could have got a LOOP here !!!!
function getNextAI($tid, $localid=null, $key_ind=0)
{
   if (!isset($localid) && (!empty($this->local_id))) $localid = $this->local_id;
   
   // this will returne a shared AI value, if you create a loop, you may get err500
   $tt = $this->getTemplate($tid);
   
   if (empty($tt['tid_ai']) && (empty($tt['tid_foreign']))) return ""; 
   
   if (!empty($tt['tid_shared']) && is_numeric($tt['tid_shared']) && ($tt['tid_shared']!=$tid))
    return $this->getNextAI($tt['tid_shared'], $localid);
   if (!empty($tt['tid_foreign']) && (is_numeric($tt['tid_foreigntemplate'])) && $tt['tid_foreigntemplate'] != $tid)
    return $this->getNextAI($tt['tid_foreigntemplate'], $localid); 
   
   
   
   
   $dbj = JFactory::getDBO();
   $q = 'select `ai` from `#__onepage_exported` where `tid` = "'.$tid.'" and `localid` = "'.$localid.'" ';
   $dbj->setQuery($q);
   $res = $dbj->loadResult();
   
   
   if (!empty($res)) return $res;
   
   
   
   $tt = $this->getTemplate($tid);
   if (empty($tt['tid_ai']) && (empty($tt['tid_special']))) return $localid;

   
   $agenda_id = (int)$tt['tid_ai']; 
   $order_id = $localid; 
   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'numbering.php'); 
   
   $numbering = OPCNumbering::requestNew($agenda_id, $tid, $order_id);
   $status='PROCESSING';
   $specials = array(0 => $numbering); 
   
   return $numbering; 
   /*
   $q = 'select convert(ai, unsigned) as i from #__onepage_exported where tid = "'.$tid.'" and localid <> "'.$localid.'" order by ai desc limit 0,1';
   $dbj->setQuery($q);
   $c = $dbj->loadResult();


   if (empty($res))
    {
      $q = 'select ai from #__onepage_exported where tid = "'.$tid.'" and localid = "'.$localid.'" ';
      $dbj->setQuery($q);
      $res = $dbj->loadResult();
	}
   
   
   
   if (empty($c)) return 1;
   $num = "";
   $start = -1;
   $end = -1;
   $le = strlen($c);

   if (!is_numeric($c))
   {
    $a = str_split($c);
    for ($i=strlen($c)-1; $i>=0; $i--)
    {
      if (is_numeric($a[$i]))
      {
       $num = $a[$i].$num;
       if ($end == -1) $end = $i;
      }
      else
      {
       if (!empty($num)) 
       {
        $start = $i+1; 
        break;
       }
       
      }
    }
   }
   else
   {
    if (strpos($c, '0')===0)
    {
     $c2 = (int)$c;
     $nuls = str_replace($c2, '', $c);
     $newc = $c+1;
     if (strlen($newc)>strlen($c))
     {
      if (strlen($nuls)>1) $nuls=substr($nuls, 0, strlen($nuls)-1);
      else $nuls = '';
     }
     $c++;
     $c = $nuls.$c;
     return $c;
    }
    $c++;
    return $c;
   }
   if ($num !== "")
   {
    if ($start > 0)
    $c2 = substr($c, 0, $start).$num;
    else $c2 = $num;
    if ($end != $le-1) $c2 .= substr($c, $end+1);
    return $c2;
   }
   return $c;
   */
}

// this function is missing in php !!!
// will revers encoded string (obrati ho odzadu) hello = olleh
function mb_strrev($text, $encoding = null)
{
    $funcParams = array($text);
    if ($encoding !== null)
        $funcParams[] = $encoding;
	if (function_exists('mb_strlen'))
	{
		
		
		$length = mb_strlen($text, $encoding); 
		
	}
    

    $output = '';
    $funcParams = array($text, $length, 1);
    if ($encoding !== null)
        $funcParams[] = $encoding;
    while ($funcParams[1]--) {
         $output .= call_user_func_array('mb_substr', $funcParams);
    }
    return $output;
}



// function get array from a string or number where 
// array[key] = 'value' will be: array[key_0] = 'v' array[key_1]
// maybe we should enforce encoding here
function getArray($key, $string, &$data, $dig)
{ 
 $ret = array();
 if (!empty($dig) and ($dig>strlen($string))) $max = $dig;
 else $max = strlen($string);
 $l = strlen($string);
  
 for ($i=0; $i<$max; $i++)
 {
  if ($i>=$l)
  $data[$key.'_'.$i] = ' ';
  else
  $data[$key.'_'.$i] = mb_substr($string, $i, 1, 'UTF-8');
 }
 // returns data in reference
 //return $ret;
}

// function to add zeroes in front of a number
function addZeroes($number, $dig)
{
 if ($dig<=strlen($number)) return $number;
 for ($i=strlen($number); $i<=$dig; $i++)
 {
  $number .= '0';
 }
 return $number;
}
// returns array[0] = "Street name", array[1] = "123/23"
// this is for Central European countries
function parseStreet($string, $key, &$data)
{
 //$arr = explode($string);
 //echo $string.' :';

 $arr = mb_split('/[\s,\\\.\-\/]+/', $string);

 $street = '';
 $num = '';
 $pos = 0;
 if (count($arr)>1)
 {
  for ($i = 0; $i<count($arr); $i++)
  {
   // 217.65.5.162
   $part = $arr[$i];
   $pos += mb_strlen($arr[$i]);
   if ($i != count($arr)-1)
   {
   $delim = mb_substr($string, $pos, 1);
   $pos++;
   }
   else $delim = '';
   if (is_numeric($part))
   {
    $num .= $part.$delim;
   }
   else 
   {
    $street .= $part.$delim;
   }
  }
  $data[$key.'_parsedstreet'] = $street;
  $data[$key.'_parsedstreetnum'] = $num;

 }
 else
 {
   $data[$key.'_parsedstreet'] = $string;
   $data[$key.'_parsedstreetnum'] = '';
 }
 return $data;
}

// only for ORDER_DATA and ORDER_DATA_TXT type
function getOrderData($localid, $ind=0)
{
	
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'opctracking.php'); 
	$order_arr = array(); 
	$order_obj = new stdClass(); 
	OPCtrackingHelper::getOrderVars($localid, $order_arr, $order_obj); 
	$order_data = array(); 
	
	$ind = '_'.$ind;
	foreach ($order_arr as $k=>$d)
     {
      $order_data[$k.$ind] = $d;
     }
	$total = $order_arr['bt_order_total']; 
	$order_data['order_total_floor'.$ind] = floor($total);
    $order_data['order_total_floortxt'.$ind] = $this->number2text(floor($total));
    $cents = round(($total - floor($total))*100);
    $order_data['order_total_cents'.$ind] = $cents;
	return $order_data; 
	
	$this->number2text(555);
 	 
     
     
     $dbj = JFactory::getDBO();
     $order_id = $dbj->escape($localid);
     //ernest get data
     $order_data = array();
     $fieldsOnly = false;
     if (!empty($localid))
     $q = "SELECT * FROM #__virtuemart_orders WHERE virtuemart_order_id='".(int)$order_id."' LIMIT 0,1";
     else
     {
      $fieldsOnly = true; 
      $q = "SELECT * FROM #__virtuemart_orders WHERE 1 LIMIT 0,1";
     }
     $dbj->setQuery($q);
     // basic order data:
     $arr1 = $dbj->loadAssoc();
	 
	 
	 
	 if (!empty($arr1))
	 {
	 $arr1['order_id'] = (int)$arr1['virtuemart_order_id']; 
	 $arr1['user_id'] = (int)$arr1['virtuemart_user_id']; 
     foreach ($arr1 as $k=>$d)
     {
      $order_data[$k.$ind] = $d;
     }
	 }
	 
	 
     
     $total = $order_data['order_total'.$ind];
     
     $order_data['order_total_floor'.$ind] = floor($total);
     $order_data['order_total_floortxt'.$ind] = $this->number2text(floor($total));
     $cents = round(($total - floor($total))*100);
     $order_data['order_total_cents'.$ind] = $cents;
     $msg = $dbj->getErrorMsg(); if (!empty($msg)) {  die('opc export error'); }
     $order_id = (int)$arr1['order_id'];
     $user_id = (int)$arr1['user_id'];
     $qt = "SELECT * from #__virtuemart_order_userinfos WHERE virtuemart_user_id='".(int)$user_id."' AND virtuemart_order_id='".(int)$order_id."' AND address_type = 'BT' LIMIT 0,1"; 
     $dbj->setQuery($qt);
     // basic user data from order_info
     $bta = $dbj->loadAssoc();
	 $msg = $dbj->getErrorMsg(); if (!empty($msg)) { die('opc export error'); }
	 if (!empty($bta)) 
	 {
	 foreach ($bta as $key=>$value)
	  {
	   $order_data['bt_'.$key.$ind] = $value;
	   
	  }
	 }
	 
     $qt = "SELECT * from #__virtuemart_order_userinfos WHERE virtuemart_user_id='".(int)$user_id."' AND virtuemart_order_id='".(int)$order_id."' AND address_type = 'ST' LIMIT 0,1"; 
     $dbj->setQuery($qt);
	 $sta =  $dbj->loadAssoc();
	 $msg = $dbj->getErrorMsg(); if (!empty($msg)) { die('opc export error'); }
	 if (!empty($sta)) 
	 {
	  //$arr1['ship_to_address'] = $sta;
	  foreach ($sta as $key=>$value)
	  {
	   //if (!$fieldsOnly)
	   $order_data['st_'.$key.$ind] = $value;
	   //else $order_data['st_'.$key.$ind] = $order_data['bt_'.$key.$ind];
	  }
	 }
	 else
	 {
	 if (!empty($bta))
	 {
	 foreach ($bta as $key=>$value)
	  {
	  //if (!$fieldsOnly)
	   $order_data['st_'.$key.$ind] = $value;
	  // else $order_data['st_'.$key.$ind] = 'EMPTY';
	  }
	 }
	 }
	 // ziskami polozky objednavky	 
	 $qt  = "SELECT * FROM `#__virtuemart_order_items` WHERE virtuemart_order_id='".(int)$order_id."' ";
	 $dbj->setQuery($qt);
	 $prods = $dbj->loadAssocList();
	 $msg = $dbj->getErrorMsg(); if (!empty($msg)) { die('opc export error'); }
	 if (!empty($prods))
	 {
	 foreach ($prods as $ind2 => $prod)
	 {
	  foreach ($prod as $key=>$value)
	  {
	    // polozka bude vyzarat napr takto: ar1['order_item_name_0_0'] = 'nazov produktu'
	    if ($key != 'order_id')
	    $order_data[$key.$ind.'_'.$ind2] = $value;
	  }
	 }
	 }
	 
	 // ok, lets get payment information 
	 $q = "select * from `#__virtuemart_order_histories` where virtuemart_order_id = '".(int)$order_id."' order by virtuemart_order_history_id desc "; 
	 $dbj->setQuery($q);
	 $r = $dbj->loadAssocList();
	 
	 
	 foreach ($r as $ind2 => $historyitem)
	 foreach ($historyitem as $key=>$value)
	 {
	   // payment date here is in variable (last change):
	   // date_added_0_0 
	   $order_data[$key.$ind.'_'.$ind2] = $value;
	 }
	 $payment_id = $arr1['virtuemart_paymentmethod_id']; 
	 
	 if (!empty($payment) || ($payment==='0'))
	 {
	  $order_data['payment_method_id_'.$ind2] = $payment;
	  $q = "select * from #__virtuemart_paymentmethods where virtuemart_paymentmethod_id = '".(int)$payment."' "; 
	  $dbj->setQuery($q);
	  $r = $dbj->loadAssoc();
	  if (!empty($r))
	  foreach ($r as $key=>$data)
	  {
	   $order_data[$key.$ind] = $data;
	  }
	 }
	 
     return $order_data;
}

function number2text($num)
{
 if (class_exists('Numbers_Words'))
 {
  //$lang = JLanguage::load();
  $lang = JFactory::getLanguage();
  $locale = $lang->getLocale();

  $numt = new Numbers_Words();
  $str = @$numt->toWords((int)$num, 'sk');
  
 }
 if (empty($str)) return "";
 return $str;
}


function getColumns($table)
{
 $dbj = JFactory::getDBO();
 $prefix = $dbj->getPrefix();
 $table = str_replace('#__', '', $table); 
 $table = str_replace($prefix, '', $table); 
 $dbj = JFactory::getDBO();
 $q = "SHOW COLUMNS FROM `".$prefix.$table."`; ";
 $dbj->setQuery($q); 
 $ret = $dbj->loadAssocList();
 $fields = array();
 foreach ($ret as $key)
 {
  $fields[] = $key['Field'];
 }
 return $fields;
}

function columnExists($table, $column)
{
 if ($this->tableExists($table))
 {
   $tf = $this->getColumns($table);
   if (in_array($column, $tf)) return true;
 }
 return false;
}
function tableExists($table)
{

 $dbj = JFactory::getDBO();
 $prefix = $dbj->getPrefix();
 $table = str_replace('#__', '', $table); 
 $table = str_replace($prefix, '', $table); 
 
  $q = "SHOW TABLES LIKE '".$dbj->getPrefix().$table."'";
	   $dbj->setQuery($q);
	   $r = $dbj->loadResult();
	   if (!empty($r)) return true;
 return false;
}


/**
	 * Joomla modified function from installer.php file of /libraries/joomla/installer.php
	 *
	 * Method to extract the name of a discreet installation sql file from the installation manifest file.
	 *
	 * @access	public
	 * @param	string  $file 	 The SQL file
	 * @param	string	$version	The database connector to use
	 * @return	mixed	Number of queries processed or False on error
	 * @since	1.5
	 */
	function parseSQLFile($file)
	{
		// Initialize variables
		if (!file_exists($file)) die('File does not exists '.$file); 
		
		$queries = array();
		$dbj = & JFactory::getDBO();
		$class = get_class($dbj); 
		$class = strtolower($class); 
		if (stripos($class, 'mysql')!==false) $dbjDriver = 'mysql'; 
		$dbjCharset = ($dbj->hasUTF()) ? 'utf8' : '';

		

		// Get the array of file nodes to process

		// Get the name of the sql file to process
		$sqlfile = '';
			// we will set a default charset of file to utf8 and mysql driver
			$fCharset = 'utf8'; //(strtolower($file->attributes('charset')) == 'utf8') ? 'utf8' : '';
			$fDriver  = 'mysql'; // strtolower($file->attributes('driver'));


			
			{
			
			
			
				$sqlfile = $file;
				// Check that sql files exists before reading. Otherwise raise error for rollback

				$buffer = file_get_contents($file);

				// Graceful exit and rollback if read not successful
				if ( $buffer === false ) {
					return false;
				}

				// Create an array of queries from the sql file
				jimport('joomla.installer.helper');
				$queries = JInstallerHelper::splitSql($buffer);

				if (count($queries) == 0) {
					// No queries to process
					return 0;
				}

				// Process each query in the $queries array (split out of sql file).
				foreach ($queries as $query)
				{
					$query = trim($query);
					if ($query != '' && $query{0} != '#') {
						$dbj->setQuery($query);
						if (!$dbj->query()) {
							JError::raiseWarning(1, 'JInstaller::install: '.JText::_('SQL Error')." ".$dbj->stderr(true));
							return false;
						}
					}
				}
			}
		

		return (int) count($queries);
	}


}



class OPCDate {
 var $_time; 
 public function __construct($time=null)
 {
	 if (empty($time)) $time = time(); 
	 $this->_time = $time; 
	 
	 
 }
 function toRFC822()
 {
	 return date("Y-m-d H:i:s", $this->_time);   
 }
 function toMysql()
 {
	 return date("Y-m-d H:i:s", $this->_time);   
 }
 
 
}