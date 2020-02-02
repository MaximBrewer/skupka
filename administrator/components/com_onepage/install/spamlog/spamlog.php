<?php

/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* OPC ADS plugin is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 


// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSystemSpamlog extends JPlugin
{
	
	
   public static function readLog($ip) {
      
	   if (!empty($ip)) {
		     $file = $ip.'.php'; 
			 $file = JFile::makeSafe($file); 
			 $mylogfile = plgSystemSpamlog::$logdir.DIRECTORY_SEPARATOR.$file; 
	   }
	    $maxlines = 10000;
	    $timelimit = 29; 
	    $time = time(); 
	    
		$datas = array(); 	   
	   	$fl = fopen($mylogfile, "r") or die('Not found');
		for($x_pos = 0, $ln = 0, $output = ''; fseek($fl, $x_pos, SEEK_END) !== -1; $x_pos--) 
		{
			$char = fgetc($fl);
		   
			$output = $char . $output; 
			if (stripos($output, plgSystemSpamlog::$startcode)===0) {
				//echo $output; die('ook'); 
				$output = str_replace(plgSystemSpamlog::$startcode, '', $output); 
				$datas[] = json_decode($output, true); 
				$output = ''; 
				
				
				
			}
			
			
			
	 
     
    
	 $now = time(); 
     if (($now - $time) > $timelimit) 
	 {
	 fclose($fl);
	 die('Timeout'); 
	 }
	  
	 if ($ln >= $maxlines) 
	 {
	 
	 fclose($fl);
	 break;
	 }
		}
		 fclose($fl);
	   
	 
		if (empty($datas)) {
			$datas = json_decode($output); 
		}
		
		return $datas; 
   }
	
   private static $data; 
   private static $logfile; 
   private static $logdir; 
   private static $startcode; 
   function __construct(&$subject, $config)
	{
 	  
	  plgSystemSpamlog::$startcode = urldecode('%3C%3Fphp%20die()%3B%20exit()%3B%20fatalerrortrigger()%3B%20%3F%3E'); 
	 
		
	  parent::__construct($subject, $config);
	   $config = JFactory::getConfig(); 
	   $logdir = $config->get('log_path'); 
	   jimport( 'joomla.filesystem.folder' );
	   jimport('joomla.filesystem.file');
		
	   $mydir = $logdir.DIRECTORY_SEPARATOR.'spamlog'; 
	   plgSystemSpamlog::$logdir = $mydir; 
	   if (!file_exists($mydir)) {
		   JFolder::create($mydir); 
		   
		   
	   }
	   if (!file_exists($mydir.DIRECTORY_SEPARATOR.'.htaccess')) {
	    $data = 'BROKEN HTACCESS !'."\n".'Require all denied'; 
		JFile::write($mydir.DIRECTORY_SEPARATOR.'.htaccess', $data); 
	   }
	   
	  
	   
	   
	    $app = JFactory::getApplication(); 
		if ($app->getName() != 'site') {
			return;
		}
	   
	   $arr = array(); 
	   if (isset($_GET))
	   $arr['_GET'] = $_GET; 
	   else $arr['_GET'] = array();  
	   
	   if (isset($_POST))
	   $arr['_POST'] = $_POST; 
	   else $arr['_POST'] = array(); 
	   
	   if (isset($_REQUEST))
	   $arr['_REQUEST'] = $_REQUEST; 
	   else $arr['_REQUEST'] = array(); 
	   
	   if (isset($_COOKIE))
	   $arr['_COOKIE'] = $_COOKIE; 
	   else $arr['_COOKIE'] = array(); 
	   
	   if (isset($_SERVER))
	   $arr['_SERVER'] = $_SERVER; 
	   else $arr['_SERVER'] = array(); 
	   
	   
	   $file = $_SERVER['REMOTE_ADDR'].'.php'; 
	  
	   $file = JFile::makeSafe($file); 
	   
	  
	   $mylogfile = $mydir.DIRECTORY_SEPARATOR.$file; 
	   plgSystemSpamlog::$logfile = $mylogfile; 
	   $this->filterData($arr); 
	   plgSystemSpamlog::$data = $arr; 
	   
	   
	}
	function formatBytes($size, $precision = 2)
{
    $base = log($size, 1024);
    $suffixes = array('', 'K', 'M', 'G', 'T');   

    return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
	}	

	public function onAfterRoute() {
		$app = JFactory::getApplication(); 
		if ($app->getName() != 'site') {
			$size = $this->getSize(); 
			$size = $this->formatBytes($size); 
			JFactory::getApplication()->enqueueMessage('Spamlog forensic plugin is enabled, data collection in process ! SpamLog directory size: '.$size); 
		}
	}
	private function getSize() {
		$size = 0; 
		if (file_exists( plgSystemSpamlog::$logdir)) {
		$iterator = new DirectoryIterator(plgSystemSpamlog::$logdir);
	foreach ($iterator as $fileinfo) {
    if ($fileinfo->isFile()) {
        $size += $fileinfo->getSize();
    }
	}
		}
		return $size; 
	}
	private function filterData(&$data) {
		$w = $this->params->get('filter_words', 'password,password2,opc_password,cc_number,cc_num,cc_number_,cc_cvv_'); 
		$we = explode(',', $w); 
		
		foreach ($we as $kkx=>$filterword) {
			$filterword = trim($filterword); 
			foreach ($data as $k=>&$v) {
				foreach ($v as $k2=>&$v2) {
					if ($k2 == $filterword) {
						unset($data[$k][$k2]); 
						continue; 
					}
					if (stripos($k2, $filterword)!==false) {
						unset($data[$k][$k2]); 
						continue; 
					}
					
				}
			}
		}
	}
	
	public function _spamlog_write($code=0, $errstr='', $file='', $line='') {
		static $zz; 
		if (!empty($zz)) return; 
		$zz = true; 
		
	$session = JFactory::getSession(); 
	//(); 
	
	//if (class_exists('JSession')) $session->close(); 
	  
	  if (!empty(plgSystemSpamlog::$logfile)) {
		  if (!empty(plgSystemSpamlog::$data)) {
		     
			 
			 $start = plgSystemSpamlog::$startcode; 
			 $json = json_encode(plgSystemSpamlog::$data, JSON_PRETTY_PRINT); 
			 file_put_contents(plgSystemSpamlog::$logfile, $start.$json, FILE_APPEND); 
		  }
	  }
	   
	    
	}
	
	function checkPerm() {
	   $user = JFactory::getUser(); 
	   
      $isroot = $user->authorise('core.admin');	
	  
	  if (!$isroot) 
	  {
		
		return false; 
	  }
	  else {
		  return true; 
	  }
	  
   }
	
	public function onAfterInitialise()
	{
		
		
		
		
		$option = filter_input(INPUT_GET, 'option'); 
		if ($option === 'com_ajax') {
			$plugin = filter_input(INPUT_GET, 'plugin'); 
			if ($plugin === 'spamlog') {
				$this->onAjaxSpamlog(); 
				JFactory::getApplication()->close(); 
			}
			
		}
		
		
		
		
		
		
		
		
		
	
		
	}	
	//index.php?option=com_ajax&plugin=spamlog&format=raw
	public function onAjaxSpamlog() {
		jimport('joomla.filesystem.file');
		$ip = JRequest::getVar('ip', ''); 
		JFile::makeSafe($ip); 
		$user = JFactory::getUser(); 
		$url = 'index.php?option=com_ajax&plugin=spamlog&format=raw&ip='.urlencode($ip); 
		if (empty($user->id)) {
			$app = JFactory::getApplication();
			$app->redirect(JRoute::_('index.php?option=com_users&view=login&return='.urlencode(base64_encode($url)))); 
			$app->close(); 
			
		}
		if ($this->checkPerm()) {
			$ip = JRequest::getVar('ip'); 
			$data = plgSystemSpamlog::readLog($ip); 
			
			include(__DIR__.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'details.php'); 
			
			return; 
		}
		
		
		
		$app = JFactory::getApplication();
		$app->redirect(JRoute::_('index.php?option=com_users&view=login&return='.urlencode(base64_encode($url))), 'spamlog: Access denied, log in as an Administrator'); 
		$app->close(); 
		
		
		$app->redirect(JRoute::_('index.php?option=com_users&view=login&return='.urlencode(base64_encode($url)))); 
		$app->close(); 
		
	}
	
	
	private function sendEmail($emailCust, $name, $first_name, $last_name ) {
		
		$config = JFactory::getConfig();	
	  if (method_exists($config, 'getValue'))
	  $sender = array( $config->getValue( 'config.mailfrom' ), $config->getValue( 'config.fromname' ) );
	  else
	  $sender = array( $config->get( 'mailfrom' ), $config->get( 'fromname' ) );
	  
	  
	   
	      $email = $sender[0]; 
	   
	  
	  if (!empty($email))
	  {
	    $mailer = JFactory::getMailer();
		$mailer->addRecipient( $email );
		
		
		
		$subject = 'Spamlog data available for new registration'; 
		$mailer->setSubject(  html_entity_decode( $subject) );
		$mailer->isHTML( false );
		
		$body = "Spamlog plugin detected a new registration: \n\n"; 

	 $pageURL = 'http';
     if ((isset($_SERVER['HTTPS'])) && ($_SERVER["HTTPS"] == "on")) {$pageURL .= "s";}
     $pageURL .= "://";
     if ($_SERVER["SERVER_PORT"] != "80") {
      $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
     } else {
      $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
     }		
			$body .= $pageURL."\n\n"; 
			$body .= 'Additional data: '."\n"; 
			$dataMsg = 'Name: '.$name." (".$first_name.' '.$last_name.') '."\n"; 
			$dataMsg .= 'Email: '.$emailCust."\n\n"; 
			$dataMsg .= 'Visit details about this user at '."\n";
			$dataMsg .= Juri::root().'index.php?option=com_ajax&plugin=spamlog&format=raw&ip='.urlencode($_SERVER['REMOTE_ADDR'])."\n\n";
			$body .= $dataMsg; 
			$body .= "\n\nTo disable these emails proceed to your Extensions -> Plug-in manager -> disable spamlog system plugin \n";
			
		
		
		
		
		$mailer->setBody( $body );
		$mailer->setSender( $sender );
		$res = $mailer->Send();
	}
	}
	function onUserAfterSave($user, $isNew, $result, $error)
	{
	  return $this->onAfterStoreUser($user, $isNew, $result, $error); 
	}
	

	
	function onAfterStoreUser($user, $isnew, $success, $msg){

		if(is_object($user)) $user = get_object_vars($user);

		if($success===false OR empty($user['email'])) return true;
		
				$last_name = $first_name = $name = $email = ''; 
				   $email = $user['email']; 
				   $name = $user['name']; 
				   $name = trim($name); 
				   
				   if (stripos($name, ' ')!==false)
				    {
					  $a = explode(' ', $name); 
					  $first_name = $a[0]; 
					  $last_name = $a[count($a)-1]; 
					}
					else
					{
					  $first_name = $name; 
					  $last_name = ''; 
					}
				   if (empty($first_name))
				   {
				      $first_name = JRequest::getVar('first_name', ''); 
				   }
				   if (empty($last_name))
				   {
				     $last_name = JRequest::getVar('last_name', ''); 
				   }
				   
				 
					if (!empty($user['email']))
					
						$email =  $user['email']; ; 
					
				$this->sendEmail($email, $name, $first_name, $last_name); 
		
		
	}
	
}

function spamlog_write($code=0, $errstr='', $file='', $line='') {
	plgSystemSpamlog::_spamlog_write(); 
}

if (function_exists('register_shutdown_function'))
register_shutdown_function( "spamlog_write" );

if (function_exists('set_error_handler'))
set_error_handler('spamlog_write'); 

if (function_exists('set_exception_handler'))
set_exception_handler('spamlog_write');