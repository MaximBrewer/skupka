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

class OPCCaptcha {
  public static function getCaptcha(&$ref, $isReg=false)
 {
    $html = ''; 
	$session = JFactory::getSession(); 
    jimport( 'joomla.plugin.helper');
    $cd = JPluginHelper::isEnabled('system', 'cdcaptcha'); 
	if ($cd)
	{
    //cdcaptcha compatiblity: 
	$rand = $session->get('cdcaptcha_com_users');  
				if (empty($rand))
				 {
				    // random length from 10 to 30
		$length = mt_rand(10, 30);
		
		$alphanum = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		
        $var_random = '';
        mt_srand(10000000 * (double)microtime());
        for ($i = 0; $i < (int)$length; $i++)
            $var_random .= $alphanum[mt_rand(0, 61)];
        unset($alphanum);
        
        $rand = $var_random;
				 }
				
				$session->set('cdcaptcha_mod_login', $session->get('cdcaptcha_mod_login', $rand)); 
				$session->set('cdcaptcha_com_users', $session->get('cdcaptcha_com_users', $rand)); 
				$session->set('cdcaptcha_com_virtuemart', $session->get('cdcaptcha_com_virtuemart', $rand)); 
				$session->set('cdcaptcha_com_onepage', $session->get('cdcaptcha_com_onepage', $rand)); 
	
	      $html .= '<input type="hidden" name="cdcaptcha_mod_login" value="'.$session->get('cdcaptcha_mod_login').'"/>'; 
		  $html .= '<input type="hidden" name="cdcaptcha_com_users" value="'.$session->get('cdcaptcha_com_users').'"/>'; 
		  $html .= '<input type="hidden" name="cdcaptcha_com_virtuemart" value="'.$session->get('cdcaptcha_com_virtuemart').'"/>'; 
		  $html .= '<input type="hidden" name="cdcaptcha_com_onepage" value="'.$session->get('cdcaptcha_com_onepage').'"/>'; 
	}			
				
	
 
    /*include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); */
	
	
    $logged = false; 
	
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	
	$default = false; 
	
	if (empty($isReg)) {
	 $enable_captcha_logged = OPCconfig::get('enable_captcha_logged', $default); 
     $enable_captcha_unlogged = OPCconfig::get('enable_captcha_unlogged', $default); 
	}
	else
	{
		$enable_captcha_reg = OPCconfig::get('enable_captcha_reg', $default); 
		$vm = VmConfig::get ('reg_captcha', false); 
		if (!empty($vm)) $enable_captcha_reg = true; 
		
	}
	
	
	
	if(JFactory::getUser()->guest) {
		//do user logged out stuff
	}
	else { 
		$logged = true; 
		//do user logged in stuff
	}  
	
   if ((((!empty($enable_captcha_logged)) && ($logged)) || ((!empty($enable_captcha_unlogged)) && (!$logged))) || (!empty($enable_captcha_reg)))
   {
   
   //onCaptcha_DisplayHtmlBlock( &$Ok, &$captchaHtmlBlock, $submit_ids='' ) {
   //onCaptcha_Confirm( $secretword, &$Ok ) {
   JPluginHelper::importPlugin('system');
   JPluginHelper::importPlugin('captcha');
   $dispatcher = JDispatcher::getInstance();
   $ok = false; 
   
   if (class_exists('JCaptcha') && (method_exists('JCaptcha', 'getInstance')))
   {
   $captcha = JCaptcha::getInstance('recaptcha');
   if (method_exists($captcha, 'display'))
   {
   $html_captcha = $captcha->display(null, 'dynamic_recaptcha_1', 'required');
   }
   }
   
   if (empty($html_captcha)) {
   
   $ids = ''; 
   $returnValues = $dispatcher->trigger('onCaptcha_DisplayHtmlBlock', array(&$ok, &$html, $ids));   
  
  
  
   
   if (empty($html))
   {
	$id = 'captcha1'; 
    $returnValues = $dispatcher->trigger('onInit', array($id));  
	$returnValues1 = $dispatcher->trigger('onDisplay', array('recaptcha', $id, 'class'));  
	$returnValues2 = $dispatcher->trigger('_init_joo_recaptcha', array());  
	if (class_exists('JooReCaptcha'))
	{
	 $joo = array(); 
	$joo[0] = ''; 
	$joo[1] = ''; 
	$joo[3] = ''; 
	JooReCaptcha::process();
	$returnValues3 = $dispatcher->trigger('_addFormCallback', array($joo));  
	}
	
	
	foreach ($returnValues as $html2)
	{
	    if ($html2 !== true)
	   $html .= $html2; 	
	}
	foreach ($returnValues1 as $html2)
	{
	   if ($html2 !== true)
	   $html .= $html2; 	
	}
	foreach ($returnValues2 as $html2)
	{
	   if ($html2 !== true)
	   $html .= $html2; 	
	}
	if (!empty($returnValues3))
	foreach ($returnValues3 as $html2)
	{
	   if ($html2 !== true)
	   $html .= $html2; 	
	}
	
	
	
	
	
   }
   }
   else
   {
	   $html = $html_captcha; 
   }
   }
   return $html; 

   
 }


}