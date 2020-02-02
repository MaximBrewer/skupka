<?php
/**
 * Legacy template loader for One Page Checkout 2 for VirtueMart 2
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

$document = JFactory::getDocument();

$is_registration_template = false; 

 ob_start();  
  echo '<div id="vmMainPageOPC">'; 
 include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 

 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
 $selected_template = OPCrenderer::getSelectedTemplate();  
 if (!(empty($selected_template) || ($selected_template === 'extra')))
 {
 
  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
  $newitemid = OPCconfig::getValue('opc_config', 'newitemid', 0, 0, true); 
 
 if (empty(OPCrenderer::$globalVars)) OPCrenderer::$globalVars = array(); 
	
   foreach ($tpla as $k=>$v)
   {
	   OPCrenderer::$globalVars[$k] = $v; 
   }
   if (!empty(OPCrenderer::$globalVars))
   {
	   foreach (OPCrenderer::$globalVars as $k=>$v)
	   {
		   $tpla[$k] = $v; 
	   }
	   
   }
 
 
 extract($tpla);
 
 
 $op_shipto_opened = OPCloader::getShipToOpened(); 
 
 
 $session = JFactory::getSession(); 
 $saved_f = $session->get('opc_fields', array(), 'opc'); 
 if (empty($saved_f)) $saved_fields = array(); 
 else
 $saved_fields = @json_decode($saved_f, true); 

//if (empty($registration_html)) $no_login_in_template = true; 
 if (!empty($saved_fields['opc_is_business'])) $opc_is_business = true; 
 else $opc_is_business = false; 

 if (!empty($saved_fields['agreed']))
 {
	 $agree_checked = true; 
 }
 
 
 
 $currentUser = JFactory::getUser();
 $uid = $currentUser->get('id');
 if (!empty($uid)) 
 { 
 
 $no_login_in_template = true; 
 }
 
 
 {
 JHTMLOPC::stylesheet('onepage.css', 'components/com_onepage/themes/'.$selected_template.'/', array());

 JHTMLOPC::stylesheet('vmpanels.css', 'components/com_virtuemart/assets/css/', array());
 }
 
 if (!empty($load_min_bootstrap))
 {
 JHTMLOPC::stylesheet('bootstrap.min.css', 'components/com_onepage/themes/extra/bootstrap/', array());
 }
 
 if (empty($this->cart) || (empty($this->cart->products)))
 {
   $continue_link = $tpla['continue_link']; 
   ob_start(); 
   include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$selected_template.DIRECTORY_SEPARATOR.'empty_cart.tpl.php'); 
   $empty_cart = ob_get_clean(); 
   $position = array('empty_cart' => $empty_cart); 
   OPCrenderer::addModules($this, $position); 
   echo $position['empty_cart']; 
 }
 else
 {
 
 if (VM_REGISTRATION_TYPE == 'NO_REGISTRATION')
 {
 $no_login_in_template = true; 
 }
 
 
 
 
 if(!class_exists('shopFunctionsF')) require(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shopfunctionsf.php');
 
 $comUserOption= 'com_users'; 

 $VM_LANG = new op_languageHelper(); 
 $GLOBALS['VM_LANG'] = $VM_LANG; 
 $lang = JFactory::getLanguage();
 $tag = $lang->getTag();
 $langcode = JRequest::getVar('lang', ''); 
 $no_jscheck = true;
 define("_MIN_POV_REACHED", '1');
 $no_jscheck = true;
 
 if (empty($langcode))
 {
 if (!empty($tag))
 {
 $arr = explode('-', $tag); 
 if (!empty($arr[0])) $langcode = $arr[0]; 
 }
 if (empty($langcode)) $langcode = 'en'; 
 }
 $GLOBALS['mosConfig_locale'] = $langcode; 

 // legacy vars to be deleted: 
 
 $op_disable_shipping = OPCloader::getShippingEnabled($this->cart); 
 
 
 if (empty($op_disable_shipping)) $op_disable_shipping = false;
 $no_shipping = $op_disable_shipping; 
 

 
$cart = $this->cart;

if (!empty($min_reached_text))
{
   JFactory::getApplication()->enqueueMessage($min_reached_text); 
}
 
 if ((!empty($min_reached_text)) && (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$selected_template.DIRECTORY_SEPARATOR.'onepage.min.tpl.php')))
 {
	 ob_start(); 
    echo '<div class="opc_minorder_wrapper" id="opc_minorder_wrapper" >'; 
    include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$selected_template.DIRECTORY_SEPARATOR.'onepage.min.tpl.php'); 
    echo '</div>'; 
	$min_not_reached = ob_get_clean(); 
	 $position = array('cart_min_not_reached' => $min_not_reached); 
     OPCrenderer::addModules($this, $position); 
     echo $position['cart_min_not_reached']; 
 }
 else
 if ((OPCloader::logged($cart)))
 {
 
 if (!empty($min_reached_text))
 {
  $html_in_between .= '<div style="clear: both;">'.$min_reached_text.'</div>'; 
 }
 
 
 // let's set the TOS config here
 ob_start(); 
 echo '<div class="opc_logged_wrapper" id="opc_logged_wrapper" >'; 
 include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$selected_template.DIRECTORY_SEPARATOR.'onepage.logged.tpl.php'); 
 echo '</div>'; 
 $logged = ob_get_clean(); 
 $position = array('cart_logged' => $logged); 
 OPCrenderer::addModules($this, $position); 
 echo $position['cart_logged']; 
 
 
 }
 else
 {
 
 if (!empty($min_reached_text))
{
  $html_in_between .= '<div style="clear: both;">'.$min_reached_text.'</div>'; 
}
 ob_start(); 
 echo '<div class="opc_unlogged_wrapper" id="opc_unlogged_wrapper" >'; 
 include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$selected_template.DIRECTORY_SEPARATOR.'onepage.unlogged.tpl.php'); 
 echo '</div>'; 
 $unlogged = ob_get_clean(); 
 $position = array('cart_unlogged' => $unlogged); 
 OPCrenderer::addModules($this, $position); 
 echo $position['cart_unlogged']; 
 
 
 }
 }
 if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$selected_template.DIRECTORY_SEPARATOR.'include.php'))
 include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$selected_template.DIRECTORY_SEPARATOR.'include.php'); 
 echo '</div>';
 
 $output = ob_get_clean(); 
 }
 //post process
 $output = str_replace('name="adminForm"', ' id="adminForm" name="adminForm" ', $output);

 //html5 spec for enter key: 
 $x1 = stripos($output, 'id="adminForm"'); 
 if ($x1 !== false)
  {
     $x2 = stripos($output, '>', $x1); 
	 $add = '<div style="display: none;"><input type="submit" onclick="return Onepage.formSubmit(event, this);" name="hidden_submit" value="hidden_submit" /></div>'; 
	 $output = substr($output, 0, $x2+1).$add.substr($output, $x2+1); 
  }
 
 if (!class_exists('OPCloadmodule'))
 {
   require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loadmodule.php'); 
 }
 OPCloadModule::onContentPrepare('text', $output); 
 /*
 jimport( 'joomla.plugin.helper' );
 $dispatcher = JDispatcher::getInstance();
 JPluginHelper::importPlugin('content', 'loadmodule', true, $dispatcher); // very important
 if (class_exists('plgContentLoadmodule'))
 {
  $params = $mainframe->getParams('loadmodule'); 
  $cl = new plgContentLoadmodule($params); 
  $data = new stdClass(); 
  $data->text = $output; 
  
  $results = $dispatcher->trigger('onPrepareContent', array( &$data, &$params, 0)); 
  $results = $dispatcher->trigger('onContentPrepare', array( 'text', &$data, &$params, 0)); 
  if (!empty($data->text)) $output = $data->text; 
 }
 */
 // legacy support
 $output = str_replace('"showSA', '"Onepage.showSA', $output); 
 $output = str_replace('javascript: showSA', 'javascript: Onepage.showSA', $output); 
 $output = str_replace('javascript: return op_login', 'javascript: return Onepage.op_login', $output); 
 $output = str_replace('return submitenter', 'return Onepage.submitenter', $output); 
 $output = str_replace('return op_openlink', 'return Onepage.op_openlink', $output); 
 $output = str_replace('return changeST', 'return Onepage.changeST', $output); 
 
 
 //return op_openlink
 $output = str_replace('return op_unhide(', 'return Onepage.op_unhide(', $output); 
 //$output = str_replace('"showFields(', '"Onepage.showFields(', $output); 
 $output = str_replace('onchange="showFields(', 'onclick="return Onepage.showFields(', $output); 
 //return submitenter
 //$output = str_replace('javascript: showSA', 'javascript: Onepage.showSA', $output); 
 //javascript: return op_login();
  /*
  if (defined('OPC_DETECTED_DEVICE') && (OPC_DETECTED_DEVICE != 'DESKTOP'))
   {
      $f = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$selected_template.DIRECTORY_SEPARATOR.'joomla_theme'.DIRECTORY_SEPARATOR.'index.php'; 
	  
      if (file_exists($f))
	   {
	      include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$selected_template.DIRECTORY_SEPARATOR.'joomla_theme'.DIRECTORY_SEPARATOR.'index.php'); 
		  JFactory::getApplication()->close(); die(); 
	   }
   }
   */
 $useSSL = VmConfig::get('useSSL', 0);
			if ($useSSL)
			 {
			    $output = str_replace('src="http:', 'src="https:', $output); 
			 }
 echo $output; 

 
 if (VmConfig::get('usefancy', 1)) {
 if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'fancybox'.DIRECTORY_SEPARATOR.'jquery.fancybox-1.3.4.pack.js')) {
		JHTMLOPC::stylesheet('jquery.fancybox-1.3.4.css', 'components/com_virtuemart/assets/css/'); 
		JHTMLOPC::script( 'jquery.fancybox-1.3.4.pack.js','components/com_virtuemart/assets/js/fancybox/');
		
		$css = ' #fancybox-loading { display: none; } '; 
		JFactory::getDocument()->addStyleDeclaration($css); 
}
 }
 else
 {
	 
 }
 
 $ex = '<script> if (typeof sessMin == \'undefined\') var sessMin = 15; </script>'; 
 echo $ex; 
 
 if (method_exists('VmJsApi', 'loadPopUpLib')) {
   VmJsApi::loadPopUpLib(); 
 }
 JHTMLOPC::script('fancybinder.js', 'components/com_onepage/assets/js/', false);
 
 
 ?><jdoc:include type="modules" name="opc_footer" style="none" /><?php
 
 
 
 if (method_exists('VmJsApi', 'writeJS')) {
	$extras .= VmJsApi::writeJS(); 
 }