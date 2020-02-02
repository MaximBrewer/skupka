<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

class OPCremoveMsgs {

 public static function removeMsg($msg) {
	   $x = JFactory::getApplication()->getMessageQueue(); 
	  
	  $msg = JFactory::getSession()->get('application.queue', array());; 
	  $msgq1 = JFactory::getApplication()->get('messageQueue', array()); 
	  $msgq2 = JFactory::getApplication()->get('_messageQueue', array()); 
	  
	  $x = array_merge($x, $msg); 
	  $x = array_merge($x, $msgq1); 
	  $x = array_merge($x, $msgq2); 
	   foreach ($x as $key=>$val)
	    {
		if (stripos($val['message'], $msg)!==false)
			  {
				  $remove[] = $key; 
			  }

		}
		
			$arr = array(); 
		if (!empty($remove)) 
		foreach ($x as $key=>$val)
		{
			if (!in_array($key, $remove))
			{
			  $arr[] = $val; 		
			}
		}
		
	  if (!empty($remove))
	  {
		  
		  self::setMsgQueue($arr); 
		  
		
	  }
		
   // end,... removes missing value for...



		
	  
 }
 //removes messages in OPC third party filter 
 public static function removeMsgs($cart, $removeCouponError=false)
  {
      $x = JFactory::getApplication()->getMessageQueue(); 
	  
	  $msg = JFactory::getSession()->get('application.queue', array());; 
	  $msgq1 = JFactory::getApplication()->get('messageQueue', array()); 
	  $msgq2 = JFactory::getApplication()->get('_messageQueue', array()); 
	  
	  $x = array_merge($x, $msg); 
	  $x = array_merge($x, $msgq1); 
	  $x = array_merge($x, $msgq2); 
	  
	  
	  
	  
	   $disablarray = array( 'Unrecognised mathop', JText::_('COM_VIRTUEMART_CART_PLEASE_ACCEPT_TOS')); 
	   
	   include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'third_party'.DIRECTORY_SEPARATOR.'third_party_disable_msgs.php'); 
	   
	      $euvat_text = array('VMUSERFIELD_ISTRAXX_EUVATCHECKER_INVALID', 'VMUSERFIELD_ISTRAXX_EUVATCHECKER_VALID', 'VMUSERFIELD_ISTRAXX_EUVATCHECKER_INVALID_COUNTRYCODE', 'VMUSERFIELD_ISTRAXX_EUVATCHECKER_INVALID_FORMAT_REASON', 'VMUSERFIELD_ISTRAXX_EUVATCHECKER_INVALID_FORMAT', 'VMUSERFIELD_ISTRAXX_EUVATCHECKER_SERVICE_UNAVAILABLE', 
		  'VMUSERFIELD_ISTRAXX_EUVATCHECKER_COMPANYNAME_REQUIRED'); 
	   
	   foreach ($euvat_text as $k=>$t)
	   {
	   $tt = JText::_($t); 
	   $euvat_text[$k] = substr($tt, 0, 20); 
	   
	   }
	   $euvatinfo = ''; 
	   
	   			 
		//missing fields: 
		$t1 = JText::_('COM_VIRTUEMART_MISSING_VALUE_FOR_FIELD'); 
		$a = explode('%s', $t1); 
		
		
	   
	   $remove = array(); 
	   foreach ($x as $key=>$val)
	    {
		
if (!is_array($val)) continue; 
if (!isset($val['message'])) continue; 

// removes missing value for...
$f1 = false; 
if (!empty($a[0]))
{
if (stripos($val['message'], $a[0]) !== false)
$f1 = true; 
}
else $f1 = true; 

if (!empty($a[1]))
{
if (stripos($val['message'], $a[1]) !== false)
$f2 = true; 
}
else $f2 = true; 

if ($f1 && $f2) 
{
$remove[] = $key; 

}
// end,... removes missing value for...



		
		  
		     foreach ($euvat_text as $kx => $eutext)
			 {
			 // echo 'comparing '.$eutext.' with '.$val['message']."<br />\n"; 
			 if (stripos($val['message'], $eutext)!==false)
			   {

			     $euvatinfo .= $val['message']; 
			     $remove[] = $key; 
				 break;
			   }
			 }
			 
		  
		  foreach ($disablarray as $msg)
		  {
		  
		     
			  if (stripos($val['message'], $msg)!==false)
			  {
				  $remove[] = $key; 
			  }
			  if ((stripos($val['message'], JText::_('COM_VIRTUEMART_COUPON_CODE_INVALID'))!==false) && (stripos($val['message'], 'removecoupons')===false))
			  {
				 
				  $cart->couponCode = ''; 
				  $opc_debug = OPCconfig::get('opc_debug', false);  if (!empty($opc_debug)) { JFactory::getApplication()->enqueueMessage('OPC Debug: Coupon removed at '.__FILE__.':'.__LINE__); }
				  $cart->setCartIntoSession();
				  if ($removeCouponError) {
					  $remove[] = $key; 
				  }
			  }
		  }
		  
		}
		
		$arr = array(); 
		if (!empty($remove)) 
		foreach ($x as $key=>$val)
		{
			if (!in_array($key, $remove))
			{
			  $arr[] = $val; 		
			}
		}
		
	    /*
		if (!empty($remove))
		if (OPCJ3)
		if (class_exists('ReflectionClass'))
		{
		   $a = JFactory::getApplication(); 
		   $reflectionClass = new ReflectionClass($a);
		   $property = $reflectionClass->getProperty('_messageQueue'); 
		   $property->setAccessible(true);
		 
		   $property->setValue($a, $arr);
		   $x = JFactory::getApplication()->getMessageQueue(); 
		   
		}
		*/
	   
	   
	  // this works under j25 only: 
	  if (!empty($remove))
	  {
		  
		  self::setMsgQueue($arr); 
		  
		  /*
	   JFactory::getApplication()->set('messageQueue', $arr); 
	   JFactory::getApplication()->set('_messageQueue', $arr); 
	   JFactory::getSession()->set('application.queue', $arr);   
	      */
	  }
	  
  }
  //filters current queue per array of msgs
  public static function filterMsgs($msgs) {
	   $x = JFactory::getApplication()->getMessageQueue(); 
	  
	  $msg = JFactory::getSession()->get('application.queue', array());; 
	  $msgq1 = JFactory::getApplication()->get('messageQueue', array()); 
	  $msgq2 = JFactory::getApplication()->get('_messageQueue', array()); 
	  
	  $x = array_merge($x, $msg); 
	  $x = array_merge($x, $msgq1); 
	  $x = array_merge($x, $msgq2); 
	  
	  foreach ($msgs as $search)
	  foreach ($x as $val) {
	  	
if (!is_array($val)) continue; 
if (!isset($val['message'])) continue; 

// removes missing value for...
$f1 = false; 
if (!empty($search))
{
if (stripos($val['message'], $search) !== false)
$f1 = true; 
}


if ($f1) 
{
$remove[] = $key; 

}
	  }
	  
	  if (!empty($remove))
	  {
		  
		 $arr = array(); 
		foreach ($x as $key=>$val)
		{
			if (!in_array($key, $remove))
			{
			  $arr[] = $val; 		
			}
		}
		  
		  
		  self::setMsgQueue($arr); 
		  
		
	  }
	  
	  
  }
  
  public static function arrayToUnique(&$msgs) {
	  //make unique: 
		$rx = array(); 
		foreach ($msgs as $k=>$v) {
			$msg = $v['message']; 
			
			if (empty($msg)) { unset($msgs[$k]); continue; }
			$rx[$msg] = $k; 
			
		}
		$ret = array(); 
		foreach ($rx as $msg=>$k) {
			  {
				
			      $ret[$k] = $msgs[$k]; 		
				
			}
		}
		$msgs = $ret; 
		return $msgs; 
  }
  
  //sets a new array of messages (no merging here)
  public static function setMsgQueue($msgs) {
	  
	   self::arrayToUnique($msgs); 
		
		
		
		
			
	    
		
		if ( version_compare( JVERSION, '3.0', '>' ) == 1)       
		if (class_exists('ReflectionClass'))
		{
		   $a = JFactory::getApplication(); 
		   $reflectionClass = new ReflectionClass($a);
		   $property = $reflectionClass->getProperty('_messageQueue'); 
		   $property->setAccessible(true);
		 
		   $property->setValue($a, $msgs);
		   $x = JFactory::getApplication()->getMessageQueue(); 
		   
		   
		   
		}
	   
	   
	  // this works under j25 only: 
	  
	  
	   JFactory::getApplication()->set('messageQueue', $msgs); 
	   JFactory::getApplication()->set('_messageQueue', $msgs); 
	   JFactory::getSession()->set('application.queue', $msgs);   
	  
	  
  }
  
  public static function makeUnique() {
	  $x = self::prepareClearMsgs(true); 
	  
	   if (!empty($x)) {
		   self::arrayToUnique($x); 
		   self::setMsgQueue($x); 
	   }
  }
  
  
  public static function setMsgsInSession($arr) {
	  
	  $res = array(); 
	  if (!empty($arr)) {
		  foreach ($arr as $k) {
			  if (is_array($k)) {
				  $res[] = $k; 
			  }
		  }
	  }
	  $session = JFactory::getSession(); 
	  $old = $session->get('opc_msgs', array()); 
	  $res2 = array_merge($old, $res); 
	  $session->set('opc_msgs', $res2); 
	  
	  
	  
  }
  
  public static function loadAndClearMsgsInSession() {
	  $session = JFactory::getSession(); 
	  $x = self::prepareClearMsgs(true); 
	  $old = $session->get('opc_msgs', array()); 
	  $x = array_merge($old, $x); 
	  if (!empty($x)) {
	   self::setMsgQueue($x); 
	  }
	  
	  
	  $session->set('opc_msgs', array()); 
  }
  
  public static function prepareClearMsgs($retsingle=false) {
	  
		$session = JFactory::getSession();
		$msgqx4 = $session->get('application.queue', null);
		
		$msgqx1 = JFactory::getApplication()->get('messageQueue', null); 
		$msgqx2 = JFactory::getApplication()->get('_messageQueue', null); 
		$msgqx3 = array(); 
		if (OPCJ3)
		if (class_exists('ReflectionClass'))
		{
			$a = JFactory::getApplication(); 
			$reflectionClass = new ReflectionClass($a);
			$property = $reflectionClass->getProperty('_messageQueue'); 
			$property->setAccessible(true);
			
			$msgqx3 = $property->getValue($a);
			$x = JFactory::getApplication()->getMessageQueue(); 
			
		}
		if (!empty($retsingle)) {
			$ret = array(); 
			if (!empty($msgqx4))
			$ret = array_merge($ret, $msgqx4); 
			if (!empty($msgqx2))
			$ret = array_merge($ret, $msgqx2); 
			if (!empty($msgqx1))
			$ret = array_merge($ret, $msgqx1); 
			if (!empty($msgqx3))
			$ret = array_merge($ret, $msgqx3); 
			return $ret; 
		}
		return array('msgqx4'=>$msgqx4, 'msgqx2'=>$msgqx2, 'msgqx1'=>$msgqx1, 'msgqx3'=>$msgqx3); 
		
  }
  
  public static function clearMsgs($stored=array())
	{

		
		if (!empty($stored)) {
			$msgqx3 = $stored['msgqx3']; 
			$msgqx1 = $stored['msgqx1']; 
			$msgqx2 = $stored['msgqx2']; 
			$msgqx4 = $stored['msgqx4']; 
			
		}
		else {
			$msgqx3 = $msgqx1 = $msgqx2 = array(); 
			$msgqx4 = null; 
		}
		$session = JFactory::getSession();
		$session->set('application.queue', $msgqx4);
		
		$x = JFactory::getApplication()->set('messageQueue', $msgqx1); 
		$x = JFactory::getApplication()->set('_messageQueue', $msgqx2); 

		if (OPCJ3)
		if (class_exists('ReflectionClass'))
		{
			$a = JFactory::getApplication(); 
			$reflectionClass = new ReflectionClass($a);
			$property = $reflectionClass->getProperty('_messageQueue'); 
			$property->setAccessible(true);
			
			$property->setValue($a, $msgqx3);
			$x = JFactory::getApplication()->getMessageQueue(); 
			
		}
	}
  
  
}
