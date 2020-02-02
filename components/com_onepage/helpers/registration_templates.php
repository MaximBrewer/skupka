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
		
		$session = JFactory::getSession(); 
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		$opc_conference_mode = OPCconfig::get('opc_conference_mode', false); 
		if (!empty($opc_conference_mode)) {
			$saved_fields = array(); 
		}
		else {
		$saved_f = $session->get('opc_fields', array(), 'opc'); 
		if (empty($saved_f)) $saved_fields = array(); 
		else
		$saved_fields = @json_decode($saved_f, true); 
		}
	
	
	$is_registration_template = true; 
	
 if (!empty($saved_fields['agreed']))
 {
	 $agree_checked = true; 
 }

	
	//if (empty($registration_html)) $no_login_in_template = true; 
 if (!empty($saved_fields['opc_is_business'])) $opc_is_business = true; 
 else $opc_is_business = false; 
	
 ob_start();  
  echo '<div id="vmMainPageOPC">'; 
 include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
 
 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
 $selected_template = OPCrenderer::getSelectedTemplate();  
 
  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
  $newitemid = OPCconfig::getValue('opc_config', 'newitemid', 0, 0, true); 
 
 if (OPCloader::checkOPCSecret())
 {
	 $selected_template .= '_preview'; 
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
 
 

 
 
 if (VM_REGISTRATION_TYPE == 'NO_REGISTRATION')
 {
 $no_login_in_template = true; 
 }
 
 
 
 
 if(!class_exists('shopFunctionsF')) require(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shopfunctionsf.php');
 
  $comUserOption= 'com_users'; 

 
 $lang = JFactory::getLanguage();
 $tag = $lang->getTag();
 
 
$cart = $this->cart;
$uid = JFactory::getUser()->get('id'); 
 
 $use_multi_step = false; 
 ob_start(); 
 echo '<div class="opc_unlogged_wrapper" id="opc_unlogged_wrapper" ><div id="onepage_main_div">'; 
 if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$selected_template.DIRECTORY_SEPARATOR.'onepage.registration.tpl.php'))
 {
 include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$selected_template.DIRECTORY_SEPARATOR.'onepage.registration.tpl.php'); 
 }
 else
 {
   ?><form action="<?php echo $action_url; ?>" method="post" name="adminForm" class="form-ivalidate" autocomplete="off">
   
   
<!-- user registration and fields -->

<div id="register_box" style="width: 100%; clear: both;" <?php
	
	if (empty($registration_html))  { echo 'style="display:none"';}
		else if (empty($has_guest_tab) || (VM_REGISTRATION_TYPE != 'OPTIONAL_REGISTRATION' || (!empty($no_login_in_template)))) echo ' style="width:50%; " '; ?>>
	<div id="register_head" class="bandBoxStyle"><?php echo OPCLang::_('COM_VIRTUEMART_REGISTER') ?></div>
	<div id="register_container">
	<span><?php echo OPCLang::_('COM_ONEPAGE_REGISTER_TEXT') ?></span>
	<?php	echo $registration_html; ?>
	<div class="formField" id="registerbtnfield" >
	</div>
	</div>
</div>


<div id="billTo_box" style="width: 100%; clear: both;">
	<div id="billTo_head" class = "bandBoxStyle"><?php echo OPCLang::_('COM_VIRTUEMART_USER_FORM_BILLTO_LBL'); ?></div>
	<div id="billTo_container"><?php echo $op_userfields; // they are fetched from ps_userfield::listUserFields ?>
	</div>
</div>
   <?php echo $italian_checkbox; ?>
   <?php echo $captcha; ?>
 <div style="float: left; clear: both;">
	<input id="confirmbtn_button" type="submit" class="submitbtn bandBoxRedStyle" autocomplete="off" <?php echo $op_onclick ?>   />
 </div>
 
 </form>
   <?php
 }
 echo '</div></div>'; 
 $registration_override_page = ob_get_clean(); 
 $position = array('registration_override_page' => $registration_override_page); 
 OPCrenderer::addModules($this, $position); 
 echo $position['registration_override_page']; 
 
 
 if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$selected_template.DIRECTORY_SEPARATOR.'include.php'))
 include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$selected_template.DIRECTORY_SEPARATOR.'include.php'); 
 echo '</div>';
 
 $output = ob_get_clean(); 
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
 
 // legacy support
 $output = str_replace('"showSA', '"Onepage.showSA', $output); 
 $output = str_replace('javascript: showSA', 'javascript: Onepage.showSA', $output); 
 $output = str_replace('javascript: return op_login', 'javascript: return Onepage.op_login', $output); 
 $output = str_replace('javascript: return op_login', 'javascript: return Onepage.op_login', $output); 
 $output = str_replace('return submitenter', 'return Onepage.submitenter', $output); 
 $output = str_replace('return op_openlink', 'return Onepage.op_openlink', $output); 
 $output = str_replace('return changeST', 'return Onepage.changeST', $output); 
 $output = str_replace('"showSA', '"Onepage.showSA', $output); 
 
 //return op_openlink
 $output = str_replace('return op_unhide(', 'return Onepage.op_unhide(', $output); 
 //$output = str_replace('"showFields(', '"Onepage.showFields(', $output); 
 $output = str_replace('onchange="showFields(', 'onclick="return Onepage.showFields(', $output); 
 //return submitenter
 //$output = str_replace('javascript: showSA', 'javascript: Onepage.showSA', $output); 
 //javascript: return op_login();
 
$useSSL = VmConfig::get('useSSL', 0);
 if (!empty($useSSL))
 {
	 $output = str_replace('src="http:', 'src="https:', $output); 
 } 
 echo $output; 

 
 
 if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'fancybox'.DIRECTORY_SEPARATOR.'jquery.fancybox-1.3.4.pack.js')) {
		JHTMLOPC::stylesheet('jquery.fancybox-1.3.4.css', 'components/com_virtuemart/assets/css/'); 
		JHTMLOPC::script( 'jquery.fancybox-1.3.4.pack.js','components/com_virtuemart/assets/js/fancybox/');
		
		$css = ' #fancybox-loading { display: none; } '; 
		JFactory::getDocument()->addStyleDeclaration($css); 
		
}
$ex = '<script> if (typeof sessMin == \'undefined\') var sessMin = 15; </script>'; 
echo $ex; 
	
	
  if (method_exists('VmJsApi', 'loadPopUpLib')) {
   VmJsApi::loadPopUpLib(); 
 }
 JHTMLOPC::script('fancybinder.js', 'components/com_onepage/assets/js/', false);