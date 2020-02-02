<?php
/**
 * Controller for the OPC ajax and checkout
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
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

jimport('joomla.application.component.controller');

class VirtueMartControllerOrderexport extends OPCController {
  public function process()
  {
  
     $taska = array('getfile', 'putfile'); 
	 $task = JRequest::getVar('task', ''); 
	 if (!in_array($task, $taska))
	  {
	     $app = JFactory::getApplication()->close(); 
	  }
	  
	  if ($task == 'getfile')
	  $this->getfile(); 
	  if ($task == 'putfile')
	  $this->putfile(); 
  }
  private function getfile()
  {
     require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'export_helper.php');
$tid = JRequest::getVar('tid');

$ehelper = new OnepageTemplateHelper();
$hash2 = $ehelper->getFileHash($tid);
$tt = $ehelper->getTemplate($tid);

$hash = JRequest::getVar('hash');
$filepath = JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'export'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR;

if ((strtolower($hash) == $hash2) && is_numeric($tid))
{
  $filename = $tt['file'];
  $file = $filepath.$tt['file'];
  if (!file_exists($file)) die('File does not exists! '.$file);
  $d = @ob_get_clean();$d = @ob_get_clean();$d = @ob_get_clean();$d = @ob_get_clean();$d = @ob_get_clean();
  unset($d);
  $fsize = filesize($file);
  if(@ini_get('zlib.output_compression'))
    @ini_set('zlib.output_compression', 'Off'); 
  
  $ctype = $ehelper->getFileHeader($file);
  
    header("Pragma: public"); // required
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false); // required for certain browsers
    header("Content-Type: $ctype");
    header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$fsize); 
  
    @ob_clean();
    @flush();
    readfile( $file ); 
  //echo 'filename';
   $app = JFactory::getApplication()->close(); 
  die();
}

unset($hash2);
unset($res);

  }
  private function putfile()
  {
	  
     require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'export_helper.php');
	 
	 $debug = OPCconfig::getValue('xml_export', 'xml_debug', 0, false, false); 	 
	 
$ehelper = new OnepageTemplateHelper();
// no direct access

// POST: tid, localid, file, hash, 
$tid = JRequest::getVar('tid');

$localid = JRequest::getVar('localid');
//$ehelper->setStatus($tid, $localid, 'RECEIVING');
$hash = JRequest::getVar('hash');



$hash2 = $ehelper->getFileHash($tid);
$eitem = $ehelper->getExportItem($tid, $localid);

$tt = $ehelper->getTemplate($tid);
jimport('joomla.filesystem.file');

$tname = $tid;

$tname = JFile::makesafe($tname);
$ex = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'export'.DIRECTORY_SEPARATOR;
$exf = $ex.$tname;

if (empty($_FILES))
{
  JFactory::getApplication()->close(); 
}

if ((strtolower($hash) == $hash2) && is_numeric($tid))
{
  {
    
    $ehelper->prepareDirectory($tid);
   
	// here we should have autoincrement value instead of order id=local id
	$fileName = $_FILES['file_contents']['name'];
	$fileTemp = $_FILES['file_contents']['tmp_name'];
	
	if (!is_uploaded_file($_FILES['file_contents']['tmp_name'])) return; 
	
	$num = $eitem['ai'];
	if (!isset($num)) $num = $localid;
	else $num = $num;
	
	if (is_numeric($num)) $num = $ehelper->addZeros($num, 4);
	
	$tn=JFile::makesafe($tt['tid_name']);
	$path = $exf.DIRECTORY_SEPARATOR.$num.'_'.$tn.'.pdf';
	
	$path = $ehelper->getFileName2Save($tid, $localid);
	
	
	if (file_exists($path))
	{
	 $xt = rand(); 
	 
	 $pa = pathinfo($path); 
	 $ext = '.bck'; 
	 if (!empty($pa['extension']))
	 {
		 $ext = '.'.$pa['extension']; 
	 }
	 JFile::move($path, $path.'_history_'.$xt.$ext); 
	 //JFile::delete($path);
	}
	if(!JFile::upload($fileTemp, $path)) 
	{
	 $ehelper->setStatus($tid, $localid, 'ERROR');
	 echo 'Error saving file!';
	 $fd = var_export($fileTemp, true); 
	 
	 if (!empty($debug)) {
	   JFile::write($exf.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'order_invoice_log.txt', $fd );
	 }
	}
	else
	{
	 //echo 'Saving data: '.$tid.' '.$localid.' '.$path;
	 
	 
	 // here we can send it to a customer
	 $tt = $ehelper->getTemplate($tid); 
	 if ($ehelper->getStatus($tid, $localid) == 'AUTOPROCESSING')
	 if (!empty($tt['tid_autocreate']) && (!empty($tt['tid_email'])))
	 {
	   $ehelper->setStatus($tid, $localid, 'CREATED', urlencode($path));
	   ob_start(); 
	   $ehelper->sendMail($tid, $localid, false);
	   $x = ob_get_clean(); 
	   echo $x; 
	   $xd = 'sending mail'.$x;  
	   if (!empty($debug)) {
	   JFile::write($exf.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'order_invoice_log.txt', $xd);
	   }
	   //$ehelper->syntaxError();
	 }
	 $ehelper->setStatus($tid, $localid, 'CREATED', urlencode($path));
	 echo 'File Saved OK!';
	}

    //file_put_contents($exf.DIRECTORY_SEPARATOR.$localid.'_'.$tname.'.pdf', $file);
    
  }
  /*
  else
  {
   echo 'ERROR: Nothing to save!';
  }
  */
  JFactory::getApplication()->close(); 
}
else 
{
	$xd = 'secret not equal'; 
	if (!empty($debug)) {
		JFile::write($exf.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'order_invoice_log.txt', $xd);
	}
 $ehelper->setStatus($tid, $localid, 'ERROR');
 echo 'Secret not equal !';
 
 JFactory::getApplication()->close(); 
}
  }
}
