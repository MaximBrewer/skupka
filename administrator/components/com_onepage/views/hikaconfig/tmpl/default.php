<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL 
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/


$version = ''; 
if (!defined('OPCVERSION'))
{
if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'version.php'))
{
  include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'version.php'); 
}
}

include(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."onepage.cfg.php");


  include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'api.php'); 

if ($api_key === 'default')  
if (empty($do_not_show_opcregistration) || (empty($rupostel_email)))
if (file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'default_register.php'))
 {
   echo $this->loadTemplate('register'); 
 }

echo '<div id="vmMainPageOPC"><div id="opc_config_wrapper">'; 
 
jimport ('joomla.html.html.bootstrap');

JHTML::_('behavior.tooltip');

JHtml::_('behavior.keepalive');

jimport( 'joomla.html.html.behavior' );
JHtml::_('behavior.modal', 'a.opcmodal'); 

//JHtml::_('formbehavior.chosen', 'select');

if (OPCVERSION != '{OPCVERSION}')
$version = ' ('.OPCVERSION.')'; 

	
	ob_start();
	JToolBarHelper::Title(JText::_('COM_ONEPAGE_CONFIGURATION_TITLE').$version , 'generic.png');
//	JToolBarHelper::install();
	JToolBarHelper::apply();
/*	JToolBarHelper::apply(); */
	//JToolBarHelper::cancel();
$document = JFactory::getDocument();
				$selectText = JText::_('COM_ONEPAGE_TAXES_DONOT_DELETE_GIFTS_STATUSES');
				$vm2string = "editImage: 'edit image',select_all_text: '".JText::_('Select All')."',select_some_options_text: '".JText::_($selectText)."'" ;
				


	//if (!OPCJ3)
	{
	  JHTMLOPC::stylesheet('bootstrap.min.css', 'components/com_onepage/themes/extra/bootstrap/', array());
	  JHTMLOPC::stylesheet('config.css', 'components/com_onepage/assets/css/', array());
	}
				
	if (!class_exists('VmConfig'))
	    require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
	    VmConfig::loadConfig(); 
		$app = JFactory::getApplication(); 
		if (method_exists('vmJsApi', 'js'))
		{
		
		$jq = $app->get('jquery', false); 
		$jq_ui = $app->get('jquery-ui', false); 
		if (empty($jq) && (!OPCJ3))
		{
		
		//DEPRECATED IN VM3: 
		//vmJsApi::js('jquery','//ajax.googleapis.com/ajax/libs/jquery/1.6.4','',TRUE);
		//vmJsApi::js ('jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16', '', TRUE);
		$root = Juri::root(); 
		if (substr($root, -1) !== '/') {
		 $root .= '/'; 
		}
		$root = str_replace('/administrator/', '/', $root); 
		$opc_jquery = $root.'components/com_onepage/themes/extra/jquery-ui/jquery-1.11.2.min.js'; 
		JHTMLOPC::script($opc_jquery); 
		//$document->addScript('//code.jquery.com/jquery-latest.min.js'); 
		if (empty($jq_ui))
		{
		JHTMLOPC::script('jquery-ui.min.js', 'components/com_onepage/themes/extra/jquery-ui/', false);
		JHTMLOPC::stylesheet('jquery-ui.min.css', 'components/com_onepage/themes/extra/jquery-ui/', false);
		} 
		$document->addScript('//code.jquery.com/jquery-migrate-1.2.1.min.js'); 
		$app->set('jquery', true); 
		$app->set('jquery-migrate', true); 
		
		
		}
		if (OPCJ3)
		 {
		 
		   JHtml::_('jquery.framework');
		   JHtml::_('jquery.ui');
		   JHtml::_('formbehavior.chosen', 'select.vm-chzn-select');
		   
		      $root = Juri::root(); 
		if (substr($root, -1)!=='/') $root .= '/'; 
		   
		   JHtml::script($root.'administrator/components/com_onepage/install/mod_coolrunner/media/jquery.cookie.js'); 
		 }
		 else
		 {
		vmJsApi::js('chosen.jquery.min');
		vmJsApi::css('chosen');
		 }
		$document->addScriptDeclaration ( '
//<![CDATA[
		var vm2string ={'.$vm2string.'} ;
		 jQuery( function($) {
			$(".vm-chzn-select").chosen({enable_select_all: true,select_all_text : vm2string.select_all_text,select_some_options_text:vm2string.select_some_options_text});
		});
//]]>
				');
		
		
		}
		else
		{
		vmJsApi::jQuery(); 
		}
		
$base = JURI::base(); 
$jbase = str_replace('/administrator', '', $base); 	
if (substr($jbase, -1) !== '/') $jbase .= '/'; 

if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'jquery.noConflict.js'))
$document->addScript($jbase.'components/com_virtuemart/assets/js/jquery.noConflict.js');
else
if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'jquery.noconflict.js'))
$document->addScript($jbase.'components/com_virtuemart/assets/js/jquery.noconflict.js');
		
		
	
	//$docj = JFactory::getDocument();
	$url = JURI::base(true); 
	if (substr($url, strlen($url))!= '/') $url .= '/'; 
	$javascript =  "\n".' var op_ajaxurl = "'.$url.'"; '."\n";
	/*
    $javascript .= 'if(window.addEventListener){ // Mozilla, Netscape, Firefox' . "\n";
    $javascript .= '    window.addEventListener("load", function(){ op_runAjax(); }, false);' . "\n";
    $javascript .= '} else { // IE' . "\n";
    $javascript .= '    window.attachEvent("onload", function(){ op_runAjax(); });' . "\n";
    $javascript .= '}';
    */
	//$document = JFactory::getDocument();
	$document->addScriptDeclaration( $javascript );	
	
	$c = VmConfig::get('coupons_enable', true); 
	VmConfig::set('coupons_enable', 10); 
	$test = VmConfig::get('coupons_enable'); 
	VmConfig::set('coupons_enable', $c); 
	if ($test != 10)
	 {
	   $is_admin =false; 
	 }
	 else $is_admin = true; 
	
      $session = JFactory::getSession();
      
        jimport('joomla.html.pane');
        jimport('joomla.utilities.utility');
	
	JHTMLOPC::script('opcbe.js', 'administrator/components/com_onepage/assets/js/', false);
    
		  if (!class_exists('VmConfig'))
		  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		  VmConfig::loadConfig(true); 

	



$document = JFactory::getDocument();
//$document->addScript('/administrator/includes/js/joomla.javascript.js');
	$is_admin = true; 
    
   	$document = JFactory::getDocument();
	$style = '
	
	div.current {
	 float: left;
	 
	 width: 98%;
	}
	div {
	 text-indent: 0;
	}
	dl {
	 margin-left: 0 !important;
	 padding: 0 !important;
	}
	dd {
	 margin-left: 0 !important;
	 padding: 0 !important;
	 width: 100%;
	 
	}
	dd div {
	 margin-left: 0 !important;
	 padding-left: 0 !important;
	 text-indent: 0 !important;
	 
	 
	}
	div.current dd {
	 display: block;
	 padding-left:1px;
     padding-right:1px;
     margin-left:1px;
     margin-right:1px;
     text-indent:1px;
     float: left;
	}
	input[type="button"]:hover, input[type="button"]:active {
	  background-color: #ddd; 
	}
	
	';
	if (!OPCJ3)
   $document->addStyleDeclaration($style);

//include_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'export_helper.php');

// set default variables:
if (!isset($disable_onepage)) $disable_onepage = false;
if (!isset($must_have_valid_vat)) $must_have_valid_vat = true;
if (!isset($unlog_all_shoppers)) $unlog_all_shoppers = false;
if (!isset($allow_duplicit)) $allow_duplicit = true;
if (!isset($tpl_logged)) $tpl_logged = '';
if (!isset($tpl_unlogged)) $tpl_unlogged = '';
if (!isset($css_logged)) $css_logged = '';
if (!isset($css_unlogged)) $css_unlogged = '';
if (!isset($show_full_tos)) $show_full_tos = false;
if (!isset($payment_default)) $payment_default = 'default';
if (!empty($this->default_country))
if (!isset($default_shipping_country)) $default_shipping_country = $this->default_country;

$userConfig = JComponentHelper::getParams('com_users');
$regA = $userConfig->get('allowUserRegistration');
$regB = $userConfig->get('useractivation');

if (!empty($_SESSION['onepage_err'])) $msg = unserialize($_SESSION['onepage_err']).'<br />';
else $msg = ''; 

if (isset($_SESSION['onepage_err']))
{
    $msg = unserialize($_SESSION['onepage_err']);
	if (!empty($msg))
	{
	    echo '<div style="width = 100%; border: 2px solid red;">';
	    echo $msg;
	    unset($_SESSION['onepage_err']);
	    echo '</div>';
	}
}	
	if (isset($payments_to_hide))
	{
	 $payments_to_hide = str_replace(' ', '',  $payments_to_hide);
	 $pth = explode(',', $payments_to_hide);
	}
	if (!isset($pth)) $pth = array();

?>
	
	<form action="<?php echo JURI::base(); ?>index.php?option=com_onepage&amp;controller=config" method="post" name="adminForm" id="adminForm">
	
	<input type="hidden" name="is_hika" value="1" />
	
	<?php 
	$x = VmConfig::get('active_languages', array('en-GB')); 
    ?>
	<input type="hidden" name="<?php echo $session->getName(); ?>" value="<?php echo $session->getId(); ?>" />
	<input type="hidden" name="ignhash" value="" id="ignhash" />
	<input type="hidden" name="<?php if (method_exists('JUtility', 'getToken'))
	echo JUtility::getToken();
	else echo JSession::getFormToken(); ?>" value="1" />

	
	
<input type="hidden" name="do_not_show_opcregistration" value="<?php if (!empty($do_not_show_opcregistration)) echo '1'; else echo '0'; ?>" id="do_not_show_opcregistration" />
<?php	
	$selected = $opclang = JRequest::getVar('opclang', ''); 
	if (!in_array($opclang, $x)) $selected = $opclang = ''; 
	$flag = ''; 
	if (count($x)>1)
	{
	$a1 = explode('-', $opclang); 
	if (isset($a1[0]))
	{
	 $cl = strtolower($a1[0]); 
	 if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'mod_languages'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$cl.'.gif'))
	  {
	    $root = Juri::root().'/'; 
		$root = str_replace('/administrator/', '', $root); 
		
	    $flag = '<br style="clear:both;"><img src="'.$root.'/media/mod_languages/images/'.$cl.'.gif" alt="'.$opclang.'"/>'; 
		
	  }
	}
	?>
    <div class="langtab" style="clear: both; " >
	<label for="opclang"><?php echo JText::_('JFIELD_LANGUAGE_LABEL'); ?></label>
	<select name="opclang" id="opclang" onchange="submitbutton('changelang');">
	 <?php 
	  
	  
	  echo '<option '; 
	  if (empty($selected)) echo 'selected="selected" '; 
	  echo ' value="">'.JText::_('JALL_LANGUAGE').'</option>'; 
	  foreach ($x as $l)
	   {
	     echo '<option '; 
		 if ($selected == $l) echo ' selected="selected" '; 
		 echo ' value="'.$l.'">'.$l.'</option>'; 
	   }
	   
	 ?>
	</select>
	
	
	</div>
	
	<?php
	}
	?>
	<input type="hidden" name="opc_lang_orig" value="<?php echo $opclang; ?>" />
	<?php
		$app = JFactory::getApplication(); 
		
		if ((isset($app->input)) ) {
		
		$inputCookie  = $app->input->cookie;
		$value        = $inputCookie->get('opc_tab', 'panel01id');
		
		
		
		}
		else
		{
			$value = 'panel01id'; 
		}
		
        $pane = OPCPane::getInstance('tabs', array('active'=>$value, 'startOffset'=>0));
        echo $pane->startPane('pane');
        
		echo $pane->startPanel(JText::_('COM_ONEPAGE_VERSION_PANEL'), 'panel01id');
		?>
		<div id="opc_new_version" style="display: none; width: 100%; background-color: green; color: white; font-weight: bold; padding:5px;"><?php echo JText::_('COM_ONEPAGE_UPDATE_AVAILABLE'); ?></div>
		<fieldset class="adminform">
		<legend><?php echo JText::_('COM_ONEPAGE_VERSION_INFO'); ?></legend>
        <table class="admintable table table-striped" style="width: 100%;">
		<tr>
	    <td class="key">
	     <label for="installed_version"><?php echo JText::_('COM_ONEPAGE_INSTALLED_VERSION'); ?></label> 
	    </td>
	    <td  >
		<?php echo OPCVERSION; 
		$document = JFactory::getDocument();
		$document->addScriptDeclaration(' var opc_current_version = "'.OPCVERSION.'"; ');
		if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'api.php'))
		{
		  $api_key = $api_stamp = 0; 
		  include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'api.php'); 
		}
		if (empty($disable_check))
		$document->addScript('//cdn.rupostel.com/rupostel.js?opcversion='.OPCVERSION.'&amp;api_key='.$api_key.'&amp;api_stamp='.$api_stamp); 
		//JHtml::script('//cdn.rupostel.com/rupostel.js');

		?>
		</td>
		</tr>
		
		<tr>
	    <td class="key">
	     <label for="latest_version"><?php echo JText::_('COM_ONEPAGE_AVAILABLE_VERSION'); ?></label> 
	    </td>
	    <td  ><div id="opc_latest_version_wrapper"><div id="opc_latest_version">&nbsp;<?php if (empty($disable_check)) { ?><img alt="Loading..." src="../media/system/images/mootree_loader.gif" /><?php } if (!empty($disable_check)) echo JText::_('COM_ONEPAGE_VERSION_CHECK_DISABLED'); ?> </div></div>
		</td>
		</tr>
		
		<tr>
	    <td class="key">
	     <label for="change_log"><?php echo JText::_('COM_ONEPAGE_CHANGELOG'); ?></label> 
	    </td>
	    <td  >
		<div id="opc_iframe_here">&nbsp;<?php if (!empty($disable_check)) echo JText::_('COM_ONEPAGE_VERSION_CHECK_DISABLED'); ?></div>
		  
		</td>
		</tr>
		
		<tr>
	    <td class="key">
	     <label for="rupostel_email"><?php echo JText::_('COM_ONEPAGE_RUPOSTEL_EMAIL'); ?></label> 
	    </td>
	    <td  >
		<?php echo JText::_('COM_ONEPAGE_RUPOSTEL_EMAIL_DESC'); ?><br />
		  <input type="text" style="width: 300px;" id="rupostel_email" name="rupostel_email" value="<?php if (!empty($rupostel_email)) echo $rupostel_email; ?>" />
		  
		  
		  <input type="hidden" value="<?php echo $this->registration->opc_registration_name; ?>" name="opc_registration_name" id="opc_registration_name_config" />
		  
		  <input type="hidden" value="<?php echo $this->registration->opc_registration_company; ?>" name="opc_registration_company" id="opc_registration_company_config" />
		  
		  <input type="hidden" value="<?php echo $this->registration->opc_registration_hash; ?>" name="opc_registration_hash" id="opc_registration_hash" />
		  
		  <input type="hidden" value="<?php echo $this->registration->opc_registration_username; ?>" name="opc_registration_username" id="opc_registration_username_config" />
		  
		
		  
		  
		  
		  
		</td>
		
		</tr>
		
		<tr>
	    <td class="key">
	     <label for="disable_check2"><?php echo JText::_('COM_ONEPAGE_DISABLE_VERSION_CHECK'); ?></label> 
	    </td>
	    <td  >
		
		  <input type="checkbox" id="disable_check"  style="float: left; text-align: left;" name="disable_check" <?php if (!empty($disable_check)) echo ' checked="checked" '; ?> value="1" />
		  <label for="disable_check"><?php echo JText::_('COM_ONEPAGE_DISABLE_VERSION_CHECK_DESC'); ?></label>
		</td>
		
		</tr>
		
		
		</table>
		</fieldset>
		<?php
		echo $pane->endPanel(); 
        echo $pane->startPanel(JText::_('COM_ONEPAGE_GENERAL_PANEL'), 'panel1');
?>
<fieldset class="adminform">
        <legend><?php echo JText::_('COM_ONEPAGE_GENERAL'); ?></legend>
        <table class="admintable table table-striped" style="width: 100%;">
	<tr>
	    <td class="key">
	     <label for="disable_op"><?php echo JText::_('COM_ONEPAGE_GENERAL_DISABLEOPC_LABEL'); ?></label> 
	    </td>
	    <td  >
	    <input id="disable_op" type="checkbox" name="disable_op" value="disable" <?php if ($this->disable_onepage === true) echo 'checked="checked"'; ?>/> 

		<input type="hidden" name="option" value="com_onepage" />
		<input type="hidden" name="view" value="hikaconfig" />
		<input type="hidden" name="task" id="task" value="save" />
		<input type="hidden" name="task2" id="task2" value="" />
		<input type="hidden" name="delete_ht" id="delete_ht" value="0" />
		<input type="hidden" name="backview" id="backview" value="panel1" />


	    </td><td><?php echo JText::_('COM_ONEPAGE_GENERAL_DISABLEOPC_DESC'); ?></td>
	</tr>

	
	
	<tr>
	    <td class="key">
	     <label for="agreed_notchecked" ><?php echo JText::_('COM_ONEPAGE_GENERAL_AGREEMENTCHECKBOX_LABEL'); ?></label>
	    </td>
	    <td  >
	     <input type="checkbox" name="agreed_notchecked" id="agreed_notchecked" value="agreed_notchecked" <?php if (isset($agreed_notchecked)) if ($agreed_notchecked==true) echo 'checked="checked"';?> />
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_GENERAL_AGREEMENTCHECKBOX_DESC'); ?>  
	    </td>
	</tr>

	<tr>
	    <td class="key">
	     <label for="opc_link_type" ><?php echo JText::_('COM_ONEPAGE_GENERAL_OPCLINKTYPE_LABEL'); ?></label>
	    </td>
	    <td>
	     <select  name="opc_link_type" id="opc_link_type">
		   <option <?php if (empty($opc_link_type)) echo ' selected="selected" '; ?> value="0"><?php echo JText::_('COM_ONEPAGE_GENERAL_OPCLINKTYPE_SELECT_NOTENABLED'); ?></option>
		   <option <?php if (!empty($opc_link_type) && ($opc_link_type == '1')) echo ' selected="selected" '; ?> value="1"><?php echo JText::_('COM_ONEPAGE_GENERAL_OPCLINKTYPE_SELECT_DELCARTSETLINK'); ?></option>
		   <option <?php if (!empty($opc_link_type) && ($opc_link_type == '2')) echo ' selected="selected" '; ?> value="2"><?php echo JText::_('COM_ONEPAGE_GENERAL_OPCLINKTYPE_SELECT_NOTINCREMENTQUANT'); ?></option>
		   <option <?php if (!empty($opc_link_type) && ($opc_link_type == '3')) echo ' selected="selected" '; ?> value="3"><?php echo JText::_('COM_ONEPAGE_GENERAL_OPCLINKTYPE_SELECT_INCREMENTQUANT'); ?></option>
		   
		 </select>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_GENERAL_OPCLINKTYPE_DESC1'); ?><a href="https://www.rupostel.com/one-page-checkout-component/features/add-to-cart-as-a-link"><?php echo JText::_('COM_ONEPAGE_GENERAL_OPCLINKTYPE_DESC2'); ?></a>. 
	    </td>
	</tr>

	<tr>
	    <td class="key">
	     <label for="opc_link_type" ><?php echo JText::_('COM_ONEPAGE_GENERAL_OPCLINKAUTO_COUPON_LABEL'); ?></label>
	    </td>
	    <td>
		<input type="text" value="<?php if (!empty($opc_auto_coupon)) echo $opc_auto_coupon; ?>" name="opc_auto_coupon" placeholder="<?php echo addslashes(JText::_('COM_ONEPAGE_GENERAL_OPCLINKAUTO_COUPON_PLACEHOLDER')); ?>" />
		</td>
		<td>
	     <?php echo JText::_('COM_ONEPAGE_GENERAL_OPCLINKAUTO_COUPON_DESC'); ?>
		 
	    </td>

	</tr>
<tr>
	    <td class="key">
	     <label for="adc_op_articleid"><?php echo JText::_('COM_ONEPAGE_DISPLAY_ARTICLE_ID_LABEL').JText::_('COM_ONEPAGE_DISPLAY_ARTICLE_ID_LABEL_FOR_ADDTOCARTASLINK'); echo $flag; ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_DISPLAY_ARTICLE_ID_LABEL'); ?>
	    </td>
	    <td>
		
	     <?php echo $this->articleselector3; ?>
		 <input type="button" class="btn btn-small btn-success" onclick="javascript: return clearArticle('adc_op_articleid');" value="<?php echo JText::_('COM_ONEPAGE_DISPLAY_ARTICLE_ID_VALUE'); ?>" />
	    </td>
	    <td>
	     <?php echo JText::_('COM_ONEPAGE_DISPLAY_ARTICLE_ID_DESC'); ?>  
	    </td>
		</tr>
	
	<?php if ($is_admin)
	{
	?>
	<tr>
	    <td class="key">
	     <label for="use_ssl" ><?php echo JText::_('COM_ONEPAGE_GENERAL_USESSL_LABEL') ?></label>
	    </td>
	    <td  >
	     <input type="checkbox" name="use_ssl" id="use_ssl" value="use_ssl" <?php 
		 $useSSL = VmConfig::get('useSSL', 0);
		 if (!empty($useSSL))  echo 'checked="checked"'; ?> />
	    </td>
	    <td> 
	    </td>
	</tr>
	
	<?php 
	}
   ?>
		
<?php 
// disabled in v 210+
/*
if (false)
{
?>
	<tr>
	    <td class="key">
	     <label for="g_analytics" ><?php echo JText::_('COM_ONEPAGE_GENERAL_GANALYTICS_ECOMMERS_LABEL'); ?></label>
	    </td>
	    <td>
		<select name="g_analytics" id="g_analytics">
		<option <?php if (($g_analytics==true) || (!isset($g_analytics))) echo 'selected="selected"'; ?> value="1"><?php echo JText::_('COM_ONEPAGE_GENERAL_GANALYTICS_ECOMMERS_SELECT_YES'); ?></option>
		<option <?php if ($g_analytics===false) echo 'selected="selected"'; ?>value="0"><?php echo JText::_('COM_ONEPAGE_GENERAL_GANALYTICS_ECOMMERS_SELECT_NO'); ?></option>
		</select> 
		
	    </td>
		<td>
		<?php if (false) { ?>
		<label for="google_id"><?php echo JText::_('COM_ONEPAGE_GENERAL_GANALYTICS_ID_LABEL'); ?></label>
		<input type="text" id="google_id" name="google_id" value="<?php if (!empty($google_id)) echo $google_id; ?>" />
		<br style="clear: both;"/>
		<?php 
		}
		?>
		
		<?php echo JText::_('COM_ONEPAGE_GENERAL_GANALYTICS_ECOMMERS_DESC'); ?> 
	    </td>
	</tr>
<?php 

}
*/
?>	
	<tr>
	    <td class="key">
	     <label for="opc_memory" ><?php echo JText::_('COM_ONEPAGE_OPC_MEMORY_LABEL'); ?></label>
	    </td>
	    <td>
		<select name="opc_memory" id="opc_memory">
		<option <?php if (empty($opc_memory)) echo ' selected="selected" '; ?> value="0"><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
		<option <?php if (!isset($opc_memory) || ($opc_memory=='128M')) echo 'selected="selected"'; ?> value="128M">128M</option>
		<option <?php if (!empty($opc_memory) && ($opc_memory=='256M')) echo 'selected="selected"'; ?> value="256M">256M</option>
		<option <?php if (!empty($opc_memory) && ($opc_memory=='64M')) echo 'selected="selected"'; ?> value="64M">64M (<?php echo JText::_('COM_ONEPAGE_OPC_NOT_RECOMMENDED'); ?>)</option>
		</select> 
		
	    </td>
		<td>
		
		<?php 

		$x = ini_get("memory_limit"); 
		echo JText::_('COM_ONEPAGE_MEMORY_DESC').$x; 

			if (function_exists('ini_set'))
			$x1 = ini_set('memory_limit', '256M'); 
			$x2 = ini_get('memory_limit'); 
			
			if (($x2 == '128M') || ($x2 == -1))
			{
			echo JText::_('COM_ONEPAGE_ERROR_SETTING_MEMORY_LIMIT'); 
			}
			else
			if (($x2 != '256M') || ($x1 === false)) echo ' <b style="color:red;">'.JText::_('COM_ONEPAGE_ERROR_SETTING_MEMORY_LIMIT').'</b>'; 
		
		
		?> 
	    </td>
	</tr>
	
	<tr>
	    <td class="key">
	     <label for="opc_plugin_order" ><?php echo JText::_('COM_ONEPAGE_PLUGIN_ORDER_LABEL'); ?></label>
	    </td>
	    <td>
				<input type="text" name="opc_plugin_order" id="opc_plugin_order" value="<?php 
				
				if (!isset($opc_plugin_order)) echo '-9999'; else echo $opc_plugin_order; 
				
				?>"  />
	    </td>
		<td>
		
		<?php 
		
		echo JText::_('COM_ONEPAGE_PLUGIN_ORDER_DESC'); 
		
		
		
		?> 
	    </td>
	</tr>
	
	<tr>
	    <td class="key">
	     <label for="opc_disable_for_mobiles" ><?php echo JText::_('COM_ONEPAGE_DISABLE_FOR_MOBILES_LABEL'); ?></label>
	    </td>
	    <td>
				<input type="checkbox" name="opc_disable_for_mobiles" id="opc_disable_for_mobiles" value="1" <?php if (!empty($opc_disable_for_mobiles)) echo ' checked="checked" '; ?> />
	    </td>
		<td>
		
		<?php 
		
		echo JText::_('COM_ONEPAGE_DISABLE_FOR_MOBILES_DESCRIPTION'); 
		
		
		
		?> 
	    </td>
	</tr>

	
		<tr>
	    <td class="key">
	     <label for="opc_debug" ><?php echo JText::_('COM_ONEPAGE_DEBUG_LABEL'); ?></label>
	    </td>
	    <td>
				<input type="checkbox" name="opc_debug" id="opc_debug" value="1" <?php if (!empty($opc_debug)) echo ' checked="checked" '; ?> />
	    </td>
		<td>
		
		<?php 
		
		echo JText::_('COM_ONEPAGE_DEBUG_DESC'); 
		
		
		
		?><br /> <label for="opc_debug_theme"><input type="checkbox" name="opc_debug_theme" id="opc_debug_theme" value="1" <?php if (!empty($opc_debug_theme)) echo ' checked="checked" '; ?> /><?php echo JText::_('COM_ONEPAGE_DEBUG_THEME_DESC'); ?></label>
		<br />
		
		<label for="opc_debug_plugins"><input type="checkbox" name="opc_debug_plugins" id="opc_debug_plugins" value="1" <?php if (!empty($opc_debug_plugins)) echo ' checked="checked" '; ?> /><?php echo JText::_('COM_ONEPAGE_DEBUG_PLUGINS_DESC'); ?></label>
		
		
	    </td>
	</tr>
<?php
//COM_ONEPAGE_DEBUG_LABEL2="Log Blank Screens"
//COM_ONEPAGE_DEBUG_LABEL2_DESC="Log Blank Screens (php fatal errors) to /log/fatal_errors.php"
//COM_ONEPAGE_DEBUG_LABEL3="Send blank screen log to this email address:"
//COM_ONEPAGE_DEBUG_LABEL4="View blank screen (fatal error) log"
?>
		<tr style="">
	    <td class="key">
	     <label for="opc_debug2" ><?php echo JText::_('COM_ONEPAGE_DEBUG_LABEL2'); ?></label>
	    </td>
	    <td>
				<input type="checkbox" name="opc_debug2" id="opc_debug2" value="1" <?php if (!empty($opc_debug2)) echo ' checked="checked" '; ?> />
	    </td>
		<td>
		<p>
		<?php 
		echo JText::_('COM_ONEPAGE_DEBUG_LABEL2_DESC'); 
		?> 
		</p>
		<p><label for="send_e"><?php echo JText::_('COM_ONEPAGE_DEBUG_LABEL3'); ?></label>
		<input id="send_e" type="text" value="<?php if (!empty($blank_screens_email)) echo $blank_screens_email; ?>" name="blank_screens_email" />
		<input style="display: none;" class="btn btn-small btn-success" type="button" onclick="javascript: submitbutton('send_blanks');" value="Send..." />
		</p>
		<p style="display: none; clear: both;"><a href="#"><?php echo JText::_('COM_ONEPAGE_DEBUG_LABEL4'); ?></a>
		</p>
	    </td>
		
	</tr>

	
	<tr>
	    <td class="key">
	     <label for="opc_async" ><?php echo JText::_('COM_ONEPAGE_OPC_ASYNC_LABEL'); ?></label>
	    </td>
	    <td>
				<input type="checkbox" name="opc_async" id="opc_async" value="1" <?php if (!empty($opc_async)) echo ' checked="checked" '; ?> />
	    </td>
		<td>
		
		<?php 
		
		echo JText::_('COM_ONEPAGE_OPC_ASYNC_DESC'); 
		
		
		
		?> 
	    </td>
	</tr>

	<tr>
	    <td class="key">
	     <label for="opc_php_js2" ><?php echo JText::_('COM_ONEPAGE_LOAD_OPC_CONFIG'); ?></label>
	    </td>
	    <td>
				<input type="hidden" name="opc_php_js" id="opc_php_js" value="0" /><input type="checkbox" <?php if (!empty($opc_php_js2)) echo ' checked="checked" '; if (!isset($opc_php_js2)) echo ' checked="checked" '; ?> value="1" name="opc_php_js2" id="opc_php_js2" />
				
	    </td>
		<td>
		
		<?php 
		
		echo JText::_('COM_ONEPAGE_LOAD_OPC_CONFIG_DESC'); 
		
		
		
		?> 
	    </td>
	</tr>
   <?php 
   $opc_load_jquery = OPCConfig::getValue('opc_load_jquery', '', 0, false, false); 
   ?>
	
		<tr>
	    <td class="key">
	     <label for="opc_load_jquery" ><?php echo JText::_('COM_ONEPAGE_LOAD_JQUERY'); ?></label>
	    </td>
	    <td>
				<input type="checkbox" <?php if (!empty($opc_load_jquery)) echo ' checked="checked" '; ?> value="1" name="opc_load_jquery" id="opc_load_jquery" />
				
	    </td>
		<td>
		
		<?php 
		
		echo JText::_('COM_ONEPAGE_LOAD_JQUERY_DESC'); 
		
		
		
		?> 
	    </td>
	</tr>



	

	
	


        </table>
    </fieldset>    
	
	<script type="text/javascript">
//<![CDATA[
//http://www.codeproject.com/Tips/585663/Communication-with-Cross-Domain-IFrame-A-Cross-Bro
// Here "addEventListener" is for standards-compliant web browsers and "attachEvent" is for IE Browsers.
var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
var eventer = window[eventMethod];


var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";

eventer(messageEvent, function (e) {

	if ((e.origin == 'https://cdn.rupostel.com') || (e.origin == 'http://cdn.rupostel.com') || (e.origin == '//cdn.rupostel.com')) {
		setVersion(e.data); 
		
	}
}, false);    



	var op_next = 0;
	<?php 
	if (false) 
	{
	?>
	var html1 = '<tr><td class="key"><label for="hidep_';
	var html2 = '" >Payment configuration: </label></td><td colspan="3" > For this shipping method <select style="max-width: 100px;"  id="hidepsid_';
	var html21 = '" name="hidepsid_';
	var html3 = '"><option value="del" selected="selected">NOT CONFIGURED/DELETE</option><?php
		  if (!empty($this->sids))
		  foreach ($this->sids as $k => &$sid)
		  {
		  ?><option value="<?php echo addslashes($k); ?>"><?php echo $sid ?></option><?php
		  }
		  ?></select> 	disable these payment payments methods (use CTRL)		<select style="max-width: 100px;" multiple="multiple" size="5" id="hidep_';
	var html31 = '" name="hidep_';	
	var html4 = '[]">	<?php
		if (!empty($this->pms))
		foreach($this->pms as $p)
		{
		 ?> <option value=<?php echo '"'.addslashes($p['payment_method_id']).'" '; ?>><?php echo addslashes($p['payment_method_name']);?></option><?php
		}
		?></select>and make default this one	<select style="max-width: 100px;" id="hidepdef_';
	var html41 = '"  name="hidepdef_';	
	var html5 = '">	<?php
	    if (!empty($this->pms))
		foreach($this->pms as $p)
		{
		 ?> <option value=<?php echo '"'.$p['payment_method_id'].'" ';  ?>><?php echo addslashes($p['payment_method_name']);?></option><?php
		}
		?></select><a href="#" onclick="javascript: return(addnew());"> Click here to ADD MORE ... </a>	    </td>	</tr>';
    <?php } ?>


		




	
//]]>
	</script>

	
<?php    

        echo $pane->endPanel();
    echo $pane->startPanel(JText::_('COM_ONEPAGE_SHIPPING_PANEL'), 'panel77');
?>
		<fieldset class="adminform">
        <legend><?php echo JText::_('COM_ONEPAGE_SHIPPING'); ?></legend>
        <table class="admintable table table-striped" id="comeshere" style="width: 100%;">
	<tr>
	    <td class="key">
	     <label for="op_disable_shipping" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_DISABLE_LABEL'); ?> </label><?php OPCVideoHelp::show('COM_ONEPAGE_SHIPPING_DISABLE_LABEL'); ?>
		 
	    </td>
	    <td  >
		 <?php $sa = VmConfig::get('automatic_shipment', 0); 
		 
		 ?>
	     <input type="checkbox" name="op_disable_shipping" id="op_disable_shipping" <?php 
		 //if (VmConfig::get('automatic_shipment', 0)==1) echo ' disabled="disabled" '; 
		 //else 
		 if (!empty($op_disable_shipping))echo 'checked="checked"'; ?> value="op_disable_shipping"  />
	    </td>
	    <td>
	     <?php if (VmConfig::get('automatic_shipment', 1)) echo JText::_('COM_ONEPAGE_SHIPPING_DISABLE_DESC').' '.JText::_('COM_ONEPAGE_WILL_ALTER_VIRTUEMART_CONFIGURATION'); ?>
	    </td>
	</tr>
		<tr>
	    <td class="key">
	     <label for="op_disable_shipto" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_DISABLE_SHIPTO_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_SHIPPING_DISABLE_SHIPTO_LABEL'); ?>
	    </td>
	    <td  >
	     <input type="checkbox" name="op_disable_shipto" id="op_disable_shipto" value="op_disable_shipto" <?php if (!empty($op_disable_shipto))echo 'checked="checked"';?> />
	    </td>
	    <td>
	     <?php echo JText::_('COM_ONEPAGE_SHIPPING_DISABLE_SHIPTO_DESC'); ?>
	    </td>
	</tr>
<tr>
	    <td class="key" >
	     <label for="only_one_shipping_address" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_ONE_SHIPPING_ADDRESS_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_REGISTRATION_ONE_SHIPPING_ADDRESS_LABEL'); ?>
	    </td>
	    <td>
	    <input type="checkbox" id="only_one_shipping_address" name="only_one_shipping_address" value="only_one_shipping_address" <?php if (!empty($only_one_shipping_address)) echo 'checked="checked"'; ?> /> 
	    </td>
	    <td>
	    <?php echo JText::_('COM_ONEPAGE_REGISTRATION_ONE_SHIPPING_ADDRESS_DESC') ?>
		<br /><div>
		<table class="adminList">
		<tr  class="row0">
		<td>
		<input type="checkbox" id="only_one_shipping_address_hidden" name="only_one_shipping_address_hidden" value="only_one_shipping_address_hidden" <?php if (!empty($only_one_shipping_address_hidden)) echo 'checked="checked"'; ?> /> 		
		</td>
		<td>
		<?php echo JText::_('COM_ONEPAGE_ONLY_ONE_ST_HIDDEN'); ?>
		</td>
		</tr>
		</table>
		</div>
	    </td>
	</tr>
	
	
		<tr>
	    <td class="key">
	     <label for="op_shipto_opened_default" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_DEFAULT_OPENED'); ?></label>
	    </td>
	    <td  >
	     <input type="checkbox" name="op_shipto_opened_default" id="op_shipto_opened_default" value="op_shipto_opened_default" <?php if (!empty($op_shipto_opened_default))echo 'checked="checked"';?> />
	    </td>
	    <td>
	     <?php echo JText::_('COM_ONEPAGE_SHIPPING_DEFAULT_OPENED_DESC'); ?>
	    </td>
	</tr>
	
	
		<tr>
	    <td class="key">
	     <label for="op_dontloadajax" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_DONTLOADAJAX_LABEL'); ?></label>
	    </td>
	    <td  >
	     <input type="checkbox" name="op_dontloadajax" id="op_dontloadajax" value="op_dontloadajax" <?php if (isset($op_dontloadajax)) if ($op_dontloadajax==true) echo 'checked="checked"';?> />
	    </td>
	    <td>
		<?php echo JText::_('COM_ONEPAGE_SHIPPING_DONTLOADAJAX_DESC'); ?>	
	    </td>
	</tr>
	
		<tr>
	    <td class="key">
	     <label for="op_dontrefresh_shipping" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_DISABLE_REFRESH'); ?></label>
	    </td>
	    <td  >
	     <input type="checkbox" name="op_dontrefresh_shipping" id="op_dontrefresh_shipping" value="op_dontrefresh_shipping" <?php if (isset($op_dontrefresh_shipping)) if ($op_dontrefresh_shipping==true) echo 'checked="checked"';?> />
	    </td>
	    <td>
		<?php echo JText::_('COM_ONEPAGE_SHIPPING_DISABLE_REFRESH_DESC'); ?>	
	    </td>
	</tr>
	
	
		<tr>
	    <td class="key">
	     <label for="op_loader" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_LOADER_LABEL'); ?></label>
	    </td>
	    <td>
	     <input type="checkbox" name="op_loader" id="op_loader" value="op_loader" <?php if (!empty($op_loader)) echo 'checked="checked"';?> />
	    </td>
	    <td>
	       <?php echo JText::_('COM_ONEPAGE_SHIPPING_LOADER_DESC'); ?>
	    </td>
	</tr>

		
	<tr>
	    <td class="key">
	     <label for="op_zero_weight_override" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_ZERO_WEIGHT_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_SHIPPING_ZERO_WEIGHT_LABEL'); ?>
	    </td>
	    <td>
	     <input type="checkbox" name="op_zero_weight_override" id="op_zero_weight_override" value="op_free_shipping" <?php if (isset($op_zero_weight_override)) if ($op_zero_weight_override==true) echo 'checked="checked"';?> />
	    </td>
	    <td>
	     <?php echo JText::_('COM_ONEPAGE_SHIPPING_ZERO_WEIGHT_DESC'); ?><br />
		 <table>
		 <tr>
		 <td>
		 <input type="checkbox" name="disable_ship_to_on_zero_weight" value="1" <?php if (!empty($disable_ship_to_on_zero_weight)) echo ' checked="checked" '; ?> id="disable_ship_to_on_zero_weight" />
		 </td>
		 <td>
		   <?php echo JText::_('COM_ONEPAGE_SHIPPING_ZERO_WEIGHT_DISABLESHIP_DESC'); ?>
		  </td>
		 </tr>
		 </table>
	    </td>
	</tr>
		

	<tr>
	    <td class="key">
	     <label for="op_delay_ship" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_DELAY_SHIP_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_SHIPPING_DELAY_SHIP_LABEL'); ?>
	    </td>
	    <td>
	     <input type="checkbox" name="op_delay_ship" id="op_delay_ship" value="op_delay_ship" <?php if (!empty($op_delay_ship)) echo 'checked="checked"';?> />
	    </td>
	    <td>
	      <?php echo JText::_('COM_ONEPAGE_SHIPPING_DELAY_SHIP_DESC'); ?> 
	    </td>
	</tr>
<?php if (false) { ?>
	<tr>
	    <td class="key">
	     <label for="op_last_field" ><?php echo JText::_('COM_ONEPAGE_SOON_AVAILABLE'); ?><br /><?php echo JText::_('COM_ONEPAGE_SHIPPING_LAST_FIELD_LABEL'); ?></label>
	    </td>
	    <td>
	     <input type="checkbox" name="op_last_field" id="op_last_field" value="op_last_field" <?php if (!empty($op_last_field)) echo 'checked="checked"';?> />
	    </td>
	    <td>
	      <?php echo JText::_('COM_ONEPAGE_SHIPPING_LAST_FIELD_DESC'); ?>
	    </td>
	</tr>
<?php } ?>
	<tr>
	    <td class="key">
	     <label for="op_customer_shipping" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_CUSTOM_INIT_SHIPPING_LABEL'); ?></label>
	    </td>
	    <td>
	     <input type="checkbox" name="op_customer_shipping" id="op_customer_shipping" value="op_customer_shipping" <?php if (!empty($op_customer_shipping)) echo 'checked="checked"';?> />
	    </td>
	    <td>
	      <?php echo JText::_('COM_ONEPAGE_SHIPPING_CUSTOM_INIT_SHIPPING_DESC'); ?>
	    </td>
	</tr>
	
	<tr>
	    <td class="key">
	     <label for="shipping_inside_basket" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_INSIDE_BASKET_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_SHIPPING_INSIDE_BASKET_LABEL'); ?>
	    </td>
	    <td>
	     <input  class="shipping_inside_basket" type="checkbox" value="1" name="shipping_inside_basket" id="shipping_inside_basket" <?php if (!empty($shipping_inside_basket)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_SHIPPING_INSIDE_BASKET_DESC') ?>
	    </td>
	</tr>
	
		<tr>
	    <td class="key">
	     <label for="shipping_inside" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_INSIDE_AS_SELECTBOX_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_SHIPPING_INSIDE_AS_SELECTBOX_LABEL'); ?>
	    </td>
	    <td>
	     <input class="shipping_inside" type="checkbox" value="1" name="shipping_inside" id="shipping_inside" <?php if (!empty($shipping_inside)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td ><?php echo JText::_('COM_ONEPAGE_SHIPPING_INSIDE_AS_SELECTBOX_NOTICE'); 
		// Removed in 2.0.196
		if (false)
		{
		?><br /><input class="shipping_inside_choose" type="checkbox" value="1" name="shipping_inside_choose" id="shipping_inside_choose" <?php if (!empty($shipping_inside_choose)) echo 'checked="checked"'; ?>/> <?php echo JText::_('COM_ONEPAGE_SHIPPING_INSIDE_AS_SELECTBOX_DESC'); 
		}
		?>
		
	    </td>
	</tr>

	
	<tr>
	    <td class="key">
	     <label for="shipping_template" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_TEMPLATE_LABEL'); ?> </label>
	    </td>
	    <td>
	     <input class="shipping_template" type="checkbox" value="1" name="shipping_template" id="shipping_template" <?php if (!empty($shipping_template)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td ><?php echo JText::_('COM_ONEPAGE_SHIPPING_TEMPLATE_DESC'); ?>  
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label  ><?php echo JText::_('COM_ONEPAGE_SHIPPING_DISABLE_PAYMENT_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_SHIPPING_DISABLE_PAYMENT_LABEL'); ?>
	    </td>
	    <td colspan="2">
	     <label for="disable_payment_per_shipping"><?php echo JText::_('COM_ONEPAGE_SHIPPING_DISABLE_PAYMENT_ENABLE_LABEL'); ?>
		 <input value="ok" class="disable_payment_per_shipping" type="checkbox"  name="disable_payment_per_shipping" id="disable_payment_per_shipping" <?php if (!empty($disable_payment_per_shipping)) echo 'checked="checked"'; ?>/> 
		 </label>
		 <br />
		 <?php
		 $iq = 0; 
		 if (empty($dpps_default)) $dpps_default[0] = ''; 
		 if (empty($dpps_search)) $dpps_search[0] = ''; 
		 if (empty($dpps_disable)) $dpps_disable[0] = ''; 
		 
		 $a = $dpps_search; 
		 foreach ($a as $iq=>$v)
		 {
		 $html = '
		 <div id="dpps_section_'.$iq.'">
		 <label for="dpps_search_'.$iq.'">'.JText::_('COM_ONEPAGE_SHIPPING_DISABLE_PAYMENT_SEARCH_LABEL').'</label><input id="dpps_search_'.$iq.'" name="dpps_search['.$iq.']" type="text" value="';
		 if (!empty($dpps_search[$iq])) $html .= urldecode($dpps_search[$iq]); 
		 $html .= '" />
		 <br />
		 <label for="dpps_disable_'.$iq.'">'.JText::_('COM_ONEPAGE_SHIPPING_DISABLE_PAYMENT_DISABLE_LABEL').'</label>
		 <select id="dpps_disable_'.$iq.'" name="dpps_disable['.$iq.']">
		<option value="" ';
		if (empty($dpps_disable[$iq])) $html .= ' selected="selected" '; 
		$html.= '		>'.JText::_('COM_ONEPAGE_NOT_CONFIGURED').'</option> '; 
		foreach($this->pms as $p)
		{
		$html .= '<option value="'.$p['payment_method_id'].'" '; 
		if ((!empty($dpps_disable[$iq])) && $p['payment_method_id']==$dpps_disable[$iq]) $html .= ' selected="selected" '; 
		$html .= '>'.$p['payment_method_name'].'</option>'; 
		 
		}
		$html .= '
		
		</select>
		 <br />
		 
		 <label for="dpps_default_'.$iq.'">'.JText::_('COM_ONEPAGE_SHIPPING_DISABLE_PAYMENT_DISABLE_DEFAULT').'</label>
		  <select id="dpps_default_'.$iq.'" name="dpps_default['.$iq.']">
		<option value="" '; 
		if (empty($dpps_default[$iq])) $html .= ' selected="selected" '; 
		$html .= '>'.JText::_('COM_ONEPAGE_NOT_CONFIGURED').'</option>'; 
		foreach($this->pms as $p)
		{
		 $html .= '<option value='; 
		 $html .= '"'.$p['payment_method_id'].'" '; 
		 if ((!empty($dpps_default[$iq])) && $p['payment_method_id']==$dpps_default[$iq]) $html .= ' selected="selected" '; 
		 $html .= '>'; 
		 $html .= $p['payment_method_name'].'</option>'; 
		 
		}
		$html .='
		</select>
		 <br />
		<div id="dpps_addhere_'.$iq.'">&nbsp;</div>		 
		 </div>'; 
		 echo $html; 
		 //if ($iq == (count($dpps_search)-1)) echo ''; 
		 }
		 ?>
		
		 <script type="text/javascript">
//<![CDATA[			 
		  var opc_last_dpps = <?php 
		  //echo count($dpps_search)-1; 
		  echo $iq
		  ?>;

//]]>		   
		 </script>
		 <br />
		 <div style="clear: both">
		 <a href="#" onclick="javascript: return add_dpps()" ><?php echo JText::_('COM_ONEPAGE_ADD_MORE'); ?></a>
		 </div>
	    </td>
	    
	</tr>
	<tr>
	    <td class="key">
	     <label for="opc_default_shipping"><?php echo JText::_('COM_ONEPAGE_DEFAULT_SHIPPING'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_DEFAULT_SHIPPING'); ?>
	    </td>
	    <td colspan="2">
		 <select name="opc_default_shipping" id="opc_default_shipping">
		  <option <?php if (empty($opc_default_shipping) && (empty($op_default_shipping_zero))) echo ' selected="selected" '; ?> value="0"><?php echo JText::_('COM_ONEPAGE_SELECT_NOT_ZERO'); ?></option>
		  <option <?php 
		  
		  if (empty($opc_default_shipping) && (!empty($op_default_shipping_zero))) echo ' selected="selected" '; 
		  else
		  if (!empty($opc_default_shipping)) if ($opc_default_shipping === 1) echo ' selected="selected" '; 
		  
		  ?> value="1"><?php echo JText::_('COM_ONEPAGE_SHIPPING_ZERO_PRICE_LABEL'); ?></option>
		  <option <?php if (!empty($opc_default_shipping)) if ($opc_default_shipping == 2) echo ' selected="selected" '; ?> value="2"><?php echo JText::_('COM_ONEPAGE_SELECT_THE_MOST_EXPENSIVE'); ?></option>
		  <option <?php 
		  
		  if (!empty($opc_default_shipping)) 
		  {
		  if ($opc_default_shipping == 3) echo ' selected="selected" '; 
		  }
		  else 
		  if (!empty($shipping_inside_choose))
		  echo ' selected="selected" '; 
		  
		  ?> value="3"><?php echo JText::_('COM_ONEPAGE_SELECT_NONE'); ?></option>
		 </select>
	    <?php echo JText::_('COM_ONEPAGE_DEFAULT_SHIPPING_DESC'); ?> 
	    </td>
	    
	</tr>
<?php
// stAn removed in 2.0.196
if (false) { 
?>
	<tr>
	    <td class="key">
	     <label for="op_default_shipping_zero"><?php echo JText::_('COM_ONEPAGE_SHIPPING_ZERO_PRICE_LABEL'); ?></label>
	    </td>
	    <td>
	     <input type="checkbox" value="1" name="op_default_shipping_zero" id="op_default_shipping_zero" <?php if (!empty($op_default_shipping_zero)) echo ' checked="checked" '; ?>/>
	    </td>
	    <td>
	    <?php echo JText::_('COM_ONEPAGE_SHIPPING_ZERO_PRICE_DESC'); ?> 
		</td>
	</tr>
<?php } 
?>
		<tr >
	    <td class="key">
	     <label for="op_default_shipping_search"><?php echo JText::_('COM_ONEPAGE_DEFAULT_SHIPPING_SEARCH_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_DEFAULT_SHIPPING_SEARCH_LABEL'); ?>
	    </td>
		
		
	    <td id="op_default_shipping_tr" colspan="2">
		<?php echo JText::_('COM_ONEPAGE_DEFAULT_SHIPPING_SEARCH_DESC'); ?> 
		<br />
		<a href="#" onclick="return addMore(ssearch, 'op_default_shipping_tr');"><?php echo JText::_('COM_ONEPAGE_ADD_MORE'); ?></a><br />
		<?php 
		$html = '<input placeholder="'.addslashes(JText::_('COM_ONEPAGE_DEFAULT_SHIPPING_SEARCH_PLACEHOLDER')).'" type="text" name="op_default_shipping_search[{key}]" id="op_default_shipping_search_{key}" size="40" value="{val}" />'; 
		
		$c = 0; 
		
		if (empty($op_default_shipping_search))
		{
		?>
	     <input placeholder="<?php echo addslashes(JText::_('COM_ONEPAGE_DEFAULT_SHIPPING_SEARCH_PLACEHOLDER')); ?>" type="text" name="op_default_shipping_search[0]" size="40" id="op_default_shipping_search_0" value="" />
		 <?php 
		 $c = 1; 
		}
		else
		{
		  foreach ($op_default_shipping_search as $key=>$val)
		  {
		     $html2 = str_replace('{key}', $key, $html); 
			 $html2 = str_replace('{val}', $val, $html2); 
			 echo $html2; 
			 $c++; 
		  }
		}
		 
		$document = JFactory::getDocument(); 
$document->addScriptDeclaration ( '
//<![CDATA[		 
		   var ssearch = \''.str_replace("'", "\'", $html).'\'; 
		   keycount = '.(int)$c.';
		  
//]]>		
');   
?>
		 
	    </td>
		
	   
	</tr>
	
	
	<tr>
	    <td class="key">
	     <label for="use_free_text"><?php echo JText::_('COM_ONEPAGE_USE_FREE_TEXT_LABEL'); ?></label>
	    </td>
	    <td>
	     <input type="checkbox" value="1" name="use_free_text" id="use_free_text" <?php if (!empty($use_free_text)) echo ' checked="checked" '; ?>/>
	    </td>
	    <td>
	    <?php echo JText::_('COM_ONEPAGE_USE_FREE_TEXT_DESC'); ?> 
		</td>
	</tr>

	
	<tr>
	    <td class="key">
	     <label for="disable_shipto_per_shipping"><?php echo JText::_('COM_ONEPAGE_DISABLE_SHIPTO_PER_ID'); ?></label>
	    </td>
	    <td>
	     <input type="text" placeholder="<?php echo htmlentities(JText::_('COM_ONEPAGE_DISABLE_SHIPTO_PER_ID_PLAC')); ?>" name="disable_shipto_per_shipping" id="disable_shipto_per_shipping" value="<?php if (!empty($disable_shipto_per_shipping)) echo htmlentities($disable_shipto_per_shipping); ?>" />
	    </td>
	    <td>
	    <?php echo JText::_('COM_ONEPAGE_DISABLE_SHIPTO_PER_ID_DESC'); ?> 
		</td>
	</tr>
	
	
        </table>
        </fieldset>
		
		<fieldset class="adminform" style="display: none;">
		<legend><?php echo JText::_('COM_ONEPAGE_SHIPPING_ESTIMATOR_CONFIG'); ?></legend>
		
		 <table class="admintable table table-striped" style="width: 100%;">
		<tr>
	    <td class="key">
	     <label for="opc_enable_shipipng_estimator" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_ESTIMATOR_CONFIG'); ?></label>
	    </td>
	    <td>
		 <select name="opc_enable_shipipng_estimator">
		  <option <?php if (empty($opc_enable_shipipng_estimator)) echo ' selected="selected" '; ?> value=""><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
		  <option <?php if (!empty($opc_enable_shipipng_estimator) && ($opc_enable_shipipng_estimator === 1)) echo ' selected="selected" '; ?> value="1"><?php echo JText::_('COM_ONEPAGE_SHIPPING_ESTIMATOR_ANONYMOUS'); ?></option>
		  <option <?php if (!empty($opc_enable_shipipng_estimator) && ($opc_enable_shipipng_estimator === 2)) echo ' selected="selected" '; ?> value="2"><?php echo JText::_('COM_ONEPAGE_SHIPPING_ESTIMATOR_CUSTOMERS'); ?></option>
		 
		 </select>
	    
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_SHIPPING_ESTIMATOR_CONFIG_DESC'); ?> 
	    </td>
		</tr>
		
		<tr>
	    <td class="key">
	     <label for="opc_estimator_step" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_ESTIMATOR_ANONYMOUS_STEP'); ?></label>
	    </td>
	    <td>
	     <input class="opc_estimator_step" type="checkbox" value="1" name="opc_estimator_step" id="opc_estimator_step" <?php if (!empty($opc_estimator_step)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_SHIPPING_ESTIMATOR_ANONYMOUS_STEP_DESC'); ?> 
	    </td>
		</tr>
		
		<tr>
	  
		
		  <td class="key">
	     <label for="opc_estimator_position" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_ESTIMATOR_DISPLAY_THEMEPOSITION'); ?></label>
	    </td>
		
		<td colspan="2">
		<select name="opc_estimator_position">
		  <?php foreach ($this->positions as $name=>$v)
		  {
			  ?><option <?php if (!empty($opc_estimator_position) && ($opc_estimator_position==$name)) echo ' selected="selected" '; 
			  else
			   if (!isset($opc_estimator_position) && ($name=='checkoutAdvertise')) echo ' selected="selected" '; 
			  
			  ?> value="<?php echo htmlentities($name); ?>"><?php echo htmlentities($name); ?></option>
			  <?php
		  }
		  ?>
		</select>
		</td>
		</tr>
		
		
		
		<tr>
		
		
		  <td class="key">
	     <label for="estimator_fields" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_ESTIMATOR_FIELDS'); ?></label>
	    </td>
		
		
	
		
		<td>
		
		<select name="estimator_fields[]" data-placeholder="<?php echo JText::_('COM_ONEPAGE_SHIPPING_ESTIMATOR_FIELDS'); ?>" multiple="multiple" style="min-width: 150px; " multiple="multiple" class="vm-chzn-select" id="estimator_fields">
		 <?php
		    
		   foreach ($this->ulist as $key=>$row)
		    {
			 if (!$row->published) continue; 
			 ?>
			 <option <?php if (!empty($estimator_fields)) if (in_array($row->name, $estimator_fields)) echo ' selected="selected" '; ?> value="<?php echo addslashes($row->name); ?>"><?php echo htmlentities(strip_tags(JText::_($row->title).'('.$row->name.')')); ?></option>
			 <?php
			}
			
		 ?>
		</select>
		
		</td>
		<td><?php echo JText::_('COM_ONEPAGE_SHIPPING_ESTIMATOR_FIELDS_DESC'); ?></td>
		</tr>
		
		
		</table>
		</fieldset>
		
		
		<fieldset class="adminform">
		<legend><?php echo JText::_('COM_ONEPAGE_SHIPPING_DEFAULT_ADDRESS_LEGEND'); ?></legend>
		<p><?php echo JText::_('COM_ONEPAGE_SHIPPING_DEFAULT_ADDRESS_DESC'); ?></p>
		<table class="admintable table table-striped" style="width: 100%;">
		
		   <?php 
   
   if (!empty($this->countries))
   {
   ?>
	<tr>
	    <td class="key">
	     <label for="default_country" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_ADDRESS_DEFAULT_COUNTRY_LABEL1'); ?><br/><?php echo JText::_('COM_ONEPAGE_SHIPPING_ADDRESS_DEFAULT_COUNTRY_LABEL2'); ?></label>
	    </td>
	    <td>
		<select name="default_country" id="default_country">
		<option value="default"><?php echo JText::_('COM_ONEPAGE_SHIPPING_ADDRESS_DEFAULT_COUNTRY_SELECT'); ?></option>
		<?php
		$sel = false;
		foreach($this->countries as $p)
		{
		 
		 ?>
		 <option value=<?php echo '"'.$p['virtuemart_country_id'].'"';
		 if ($p['virtuemart_country_id']==$default_shipping_country) { echo ' selected="selected" '; $sel = true;}
		 if (empty($default_shipping_country) || ($default_shipping_country == 'default'))
		 if ($p['virtuemart_country_id']==$this->default_country) echo ' selected="selected" ';
		 ?>><?php echo $p['country_name']; ?></option>
		 <?php
		}
		
		?>
		</select>
		</td>
		<td> <?php echo JText::_('COM_ONEPAGE_SHIPPING_ADDRESS_DEFAULT_COUNTRY_DESC'); ?>
	    </td>
	    
	</tr>
	
	
	
	<?php 
	}
	?>
	
		<tr>
	 <td class="key"><?php echo JText::_('COM_ONEPAGE_SHIPPING_ADDRESS_ADVANCED_COUNTRY_LABEL'); ?>
	 </td>
	 <td colspan="2">
	   <?php echo JText::_('COM_ONEPAGE_SHIPPING_ADDRESS_ADVANCED_COUNTRY_DESC'); ?><br />
	   <?php
	   	 $larr = array();
	     $num = 0;
	   
	   if (!empty($this->codes))
	   {
	   foreach ($this->codes as $uu)
	   {
	   ?>
	   <div style="width: 100%; clear: both;">
	   <select name="op_lang_code_<?php echo $num; ?>">
	    <option <?php if (empty($default_country_array[$uu['code']])) echo ' selected="selected" '; ?> value=""><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
	    <option  <?php if (!empty($default_country_array[$uu['code']])) echo ' selected="selected" '; ?> value="<?php echo $uu['code']; ?>"><?php echo $uu['code'] ?></option>
	   </select>
	   <select name="op_selc_<?php echo $num; ?>">
	    <?php 
		
		foreach ($this->countries as $p)  { 
		$ua = explode('-', $uu['code']); 
		$uc = $ua[1]; 
		$uc = strtoupper($uc); 
		
		?>
		 <option value=<?php echo '"'.$p['virtuemart_country_id'].'"';
		  if ((!empty($default_country_array[$uu['code']])) &&
		   ($default_country_array[$uu['code']]==$p['virtuemart_country_id'])) echo ' selected="selected" '; 
		  else
		  if ((empty($default_country_array[$uu['code']])))
		   {
		     if ($uc == $p['country_2_code']) echo ' selected="selected" '; 
		   }
		   ?>><?php echo $p['country_name']; ?></option>
	    <?php } ?>
	   </select>

	 
	   <br />
	   <?php 
	   $num++;
	   $larr[] = $uu;
	   echo '</div>'; 
	   }
	   }
	   else
	   {
	    echo JText::_('COM_ONEPAGE_JOS_LANG');
	   } ?>
	 </td>
	</tr>
		<tr>
	    <td class="key">
	     <label for="op_use_geolocator"><?php echo JText::_('COM_ONEPAGE_SHIPPING_ADDRESS_USE_GEOLOCATOR_LABEL'); ?></label>
	    </td>
	    <td>
	     <input type="checkbox" value="1" name="op_use_geolocator" id="op_use_geolocator" <?php if (!empty($op_use_geolocator)) echo ' checked="checked" '; ?>/>
	    </td>
	    <td>
	     <?php echo JText::_('COM_ONEPAGE_SHIPPING_ADDRESS_USE_GEOLOCATOR_DESC'); ?>
		</td>
	</tr>
	
<tr>
	    <td class="key">
	     <label for="op_default_zip"><br /><?php echo JText::_('COM_ONEPAGE_SHIPPING_ADDRESS_ZIP_CODE_LABEL'); ?></label>
	    </td>
	    <td>
	     <input type="text" name="op_default_zip" id="op_default_zip" value="<?php if (!empty($op_default_zip)) echo urldecode($op_default_zip); else 
		 if ($op_default_zip === 0) echo '0';
		 else
		 echo '99999'; ?>"/>
	    </td>
	    <td>
	    <b><?php echo JText::_('COM_ONEPAGE_SHIPPING_ADDRESS_ZIP_CODE_DESC_BOLD'); ?></b> <?php echo JText::_('COM_ONEPAGE_SHIPPING_ADDRESS_ZIP_CODE_DESC'); ?><br />
		</td>
	</tr>
	

		
		
		</table>
		</fieldset>
		
		
			<fieldset class="adminform">
		<legend><?php echo JText::_('COM_ONEPAGE_SHIPPING_DELIVERY_DATE_FIELDSET'); ?></legend>
		<p><?php echo JText::_('COM_ONEPAGE_SHIPPING_DELIVERY_DATE_FIELDSET_DESC'); ?></p>
		<table class="admintable table table-striped" style="width: 100%;">
	
	<tr>
	    <td class="key">
	     <label for="delivery_enabled" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_DELIVERY_DATE_ENABLE'); ?></label>
	    </td>
	    <td>
		  <?php
		     $default = new stdClass(); 
			 $default->enabled = false; 
			 $default->required = false; 
			 $default->firstday = 1; 
			 $default->offset = 0; 
			 $default->format = 'd MM yy';
			 $default->storeformat = 'yy-mm-dd'; 
			 $default->hollidays = ''; 
			 $config = OPCconfig::getValue('opc_delivery_date', '', 0, $default, false, false); 
		  ?>
		  <input type="checkbox" name="delivery_data[enabled]" id="delivery_enabled" value="1" <?php if (!empty($config->enabled)) echo ' checked="checked" '; ?> />
        </td>		  
    </tr>
	
	<tr>
	    <td class="key">
	     <label for="delivery_required" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_DELIVERY_DATE_REQUIRED'); ?></label>
	    </td>
	    <td>
		  <?php
		    
		  ?>
		  <input type="checkbox" name="delivery_data[required]" id="delivery_required" value="1" <?php if (!empty($config->required)) echo ' checked="checked" '; ?> />
        </td>		  
    </tr>
	
	<tr>
	    <td class="key">
	     <label for="delivery_selector" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_DELIVERY_DATE_SELECTOR'); ?></label>
	    </td>
	    <td>
		  <?php
		    
		  ?>
		  <select name="delivery_selector" id="delivery_selector" class="vm-chzn-select" >
		<option value=""><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
		<?php 
		  unset($row); 
		  reset($this->ulist); 
		  $acc = array('text', 'datepicker', 'date'); 
		  
		  foreach ($this->ulist as $key=>$row)
		  {
			  if (!$row->published) continue; 
			  
			  if (!in_array($row->type, $acc)) continue; 
			 
			  
			  $title = JText::_($row->title); 
			  ?><option <?php 
			  if (!empty($delivery_selector)) if ($delivery_selector === $row->name)  echo ' selected="selected" ';
			 
			  ?>value="<?php echo $row->name; ?>"><?php echo htmlentities(strip_tags($title)); ?></option>
			  
			  <?php
		  }
		?>
		
		</select>
        </td>		  
    </tr>
	
	
	<tr>
	    <td class="key">
	     <label for="delivery_offset" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_DELIVERY_DATE_OFFSET_DESC'); ?></label>
	    </td>
	    <td>
		  <?php
		    
		  ?>
		  <input type="number" name="delivery_data[offset]" id="delivery_offset" min="0" step="1" value="<?php if (isset($config->offset)) echo $config->offset; ?>" />
        </td>		  
    </tr>
	
	<tr>
	    <td class="key">
	     <label for="delivery_offsetmax" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_DELIVERY_DATE_OFFSET_MAX_DAYS'); ?></label>
	    </td>
	    <td>
		  <?php
		    
		  ?>
		  <input type="number" name="delivery_data[offsetmax]" id="delivery_offsetmax" min="0" step="1" value="<?php if (isset($config->offsetmax)) echo $config->offsetmax; else echo '365'; ?>" />
        </td>		  
    </tr>
	
		<tr>
	    <td class="key">
	     <label for="delivery_firstday" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_DELIVERY_DATE_FIRST_DAY'); ?></label>
	    </td>
	    <td>
		  <?php
		    
		  ?>
		  <input type="number" name="delivery_data[firstday]" id="delivery_firstday" min="0" max="1" step="1" value="<?php if (isset($config->firstday)) echo $config->firstday; else echo '365'; ?>" />
        </td>		  
    </tr>
	
	<tr>
	    <td class="key">
	     <label for="delivery_disabled" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_DELIVERY_DATE_DISABLED_DAYS'); ?></label>
	    </td>
	    <td>
		  <?php
		$days = array(
	1 => JText::_('MONDAY'), 
	2 => JText::_('TUESDAY'), 
	3 => JText::_('WEDNESDAY'), 
	4 => JText::_('THURSDAY'), 
	5 => JText::_('FRIDAY'), 
	6 => JText::_('SATURDAY'), 
	0 => JText::_('SUNDAY'), 
	);
		  foreach ($days as $i=>$day)
			{
			   ?><label for="delivery_days_<?php echo $i; ?>"><input id="delivery_days_<?php echo $i; ?>" type="checkbox" value="1" name="delivery_data[days][<?php echo $i; ?>]" <?php $key = 'day_'.$i; if (!empty($config->$key)) echo ' checked="checked" '; ?> /><?php echo $day; ?></label><br />

			   <?php
			} 
		  ?>
		  
        </td>		  
    </tr>
	
	
	<tr>
	    <td class="key">
	     <label for="delivery_format" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_DELIVERY_DATE_FORMAT_DISPLAY'); ?></label>
	    </td>
	    <td>
		   <input type="text" name="delivery_data[format]" id="delivery_format" value="<?php if (isset($config->format)) echo $config->format; else echo 'd MM yy'; ?>" />
        </td>		  
    </tr>
	
	<tr>
	    <td class="key">
	     <label for="delivery_storeformat" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_DELIVERY_DATE_FORMAT_STORE'); ?></label>
	    </td>
	    <td>
		   <input type="text" name="delivery_data[storeformat]" id="delivery_storeformat" value="<?php if (isset($config->storeformat)) echo $config->storeformat; else echo 'yy-mm-dd'; ?>" />
        </td>		  
    </tr>
	
	<tr>
	    <td class="key">
	     <label for="delivery_hollidays" ><?php echo JText::_('COM_ONEPAGE_SHIPPING_DELIVERY_DATE_HOLLIDAYS'); ?></label>
	    </td>
	    <td>
		   <textarea  rows="3" cols="20" name="delivery_data[hollidays]" id="delivery_hollidays" ><?php if (isset($config->hollidays)) echo $config->hollidays; ?></textarea>
        </td>		  
    </tr>
	
	</table>
		</fieldset>
		
		
		
<?php
    echo $pane->endPanel(); 
    echo $pane->startPanel(JText::_('COM_ONEPAGE_PAYMENT_PANEL'), 'panel799');
    ?>
    <fieldset class="adminform">
    <legend><?php echo JText::_('COM_ONEPAGE_PAYMENT'); ?></legend>
     <table class="admintable table table-striped" style="width: 100%;">
   <?php 
   
   if (!empty($this->pms))
   {
   ?>
	<tr>
	    <td class="key">
	     <label for="default_payment" ><?php echo JText::_('COM_ONEPAGE_PAYMENT_DEFAULT_OPTION_LABEL'); ?></label>
	    </td>
	    <td colspan="2" >
		<select id="default_payment" name="default_payment">
		<option value="0"><?php echo JText::_('COM_ONEPAGE_PAYMENT_DEFAULT_OPTION'); ?></option>
		<option <?php if ($payment_default == 'none') echo ' selected="selected" '; ?> value="none"><?php echo ' -- '.JText::_('COM_ONEPAGE_SELECT_NONE').' -- '; ?></option>
		
		<?php
		
		foreach($this->pms as $p)
		{
		 ?>
		 <option value=<?php echo '"'.$p['payment_method_id'].'" '; if ($p['payment_method_id']==$payment_default) echo 'selected="selected" '; ?>><?php echo $p['payment_method_name'];?></option>
		 <?php
		}
		
		?>
		</select>
	    </td>
	</tr>

   <?php 
   }
   

   
    if (!empty($this->pms))
   {
   ?>
	<tr>
	    <td class="key">
	     <label for="default_payment_zero_total" ><?php echo JText::_('COM_ONEPAGE_PAYMENT_DEFAULT_ZERO_OPTION_LABEL'); ?></label>
	    </td>
	    <td  >
		<select id="default_payment_zero_total" name="default_payment_zero_total">
		<option value="0" <?php if (empty($default_payment_zero_total)) echo ' selected="selected" '; ?> ><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
		
		
		<?php
		
		foreach($this->pms as $p)
		{
		 ?>
		 <option value=<?php echo '"'.$p['payment_method_id'].'" '; if ((!empty($default_payment_zero_total)) && ($p['payment_method_id']== $default_payment_zero_total)) echo 'selected="selected" '; ?>><?php echo $p['payment_method_name'];?></option>
		 <?php
		}
		
		?>
		</select>
	    </td>
		<td><?php echo JText::_('COM_ONEPAGE_PAYMENT_DEFAULT_ZERO_OPTION_DESC'); ?>
		</td>
	</tr>

   <?php 
   }
   ?>
   
   <tr>
	    <td class="key">
	     <label for="force_zero_paymentmethod" ><?php echo JText::_('COM_ONEPAGE_PAYMENT_FORCEPAYMENT'); ?></label>
	    </td>
	    <td>
	     <input name="force_zero_paymentmethod" type="checkbox" value="1"  id="force_zero_paymentmethod" <?php if (!empty($force_zero_paymentmethod)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_PAYMENT_FORCEPAYMENT_DESC'); ?>
	    </td>
	</tr>
   
   
   
	<tr>
	    <td class="key">
	     <label for="hide_payment_if_one" ><?php echo JText::_('COM_ONEPAGE_PAYMENT_HIDE_PAYMENT_IF_ONE_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_PAYMENT_HIDE_PAYMENT_IF_ONE_LABEL'); ?>
	    </td>
	    <td>
	     <input name="hide_payment_if_one" type="checkbox" value="1"  id="hide_payment_if_one" <?php if (!empty($hide_payment_if_one)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_PAYMENT_HIDE_PAYMENT_IF_ONE_DESC'); ?>
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="hide_advertise" ><?php echo JText::_('COM_ONEPAGE_PAYMENT_HIDE_ADVERTISEMENT_LABEL'); ?></label>
	    </td>
	    <td>
	     <input name="hide_advertise" type="checkbox" value="1" id="hide_advertise" <?php if (!empty($hide_advertise)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_PAYMENT_HIDE_ADVERTISEMENT_DESC'); ?>
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="payment_inside_basket" ><?php echo JText::_('COM_ONEPAGE_PAYMENT_INSIDE_BASKET_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_PAYMENT_INSIDE_BASKET_LABEL'); ?>
	    </td>
	    <td>
	     <input  class="payment_inside_basket" type="checkbox" value="1" name="payment_inside_basket" id="payment_inside_basket" <?php if (!empty($payment_inside_basket)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_PAYMENT_INSIDE_BASKET_DESC2'); ?> 
	    </td>
	</tr>

	

	<tr>
	    <td class="key">
	     <label for="payment_inside" ><?php echo JText::_('COM_ONEPAGE_PAYMENT_INSIDE_LABEL'); ?></label>
		 <?php OPCVideoHelp::show('COM_ONEPAGE_PAYMENT_INSIDE_BASKET_LABEL'); ?>
	    </td>
	    <td>
	     <input  class="payment_inside" type="checkbox" value="1" name="payment_inside" id="payment_inside" <?php if (!empty($payment_inside)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_PAYMENT_INSIDE_DESC2'); ?> 
	    </td>
	</tr>
		<tr>
	    <td class="key">
	     <label for="klarna_se_get_address" ><?php echo JText::_('COM_ONEPAGE_PAYMENT_KLARNA_GET_ADDRESS_LABEL'); ?></label>
	    </td>
	    <td>
	     <input  class="klarna_se_get_address" type="checkbox" value="1" name="klarna_se_get_address" id="klarna_se_get_address" <?php if (!empty($klarna_se_get_address)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_PAYMENT_KLARNA_GET_ADDRESS_DESC'); ?>
	    </td>
	</tr>

		<tr>
	    <td class="key">
	     <label for="opc_payment_refresh" ><?php echo JText::_('COM_ONEPAGE_PAYMENT_DISABLE_PAYMENT_REFRESH'); ?></label>
	    </td>
	    <td>
	     <input  class="opc_payment_refresh" type="checkbox" value="1" name="opc_payment_refresh" id="opc_payment_refresh" <?php if (!empty($opc_payment_refresh)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_PAYMENT_DISABLE_PAYMENT_REFRESH_DESC'); ?>
	    </td>
	</tr>
	
	<tr>
	    <td class="key">
	     <label for="opc_recalc_js" ><?php echo JText::_('COM_ONEPAGE_RECALC_PAYMENT'); ?></label>
	    </td>
	    <td>
	     <input  class="opc_recalc_js" type="checkbox" value="1" name="opc_recalc_js" id="opc_recalc_js" <?php if (!empty($opc_recalc_js)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_RECALC_PAYMENT_DESC'); ?>
	    </td>
	</tr>
	
<?php
	   if (!empty($this->pms))
   {
   ?>
	<tr>
	    <td class="key">
	     <label for="utm_payment" ><?php echo JText::_('COM_ONEPAGE_UTM_PAYMENT_LABEL'); ?></label>
	    </td>
	    <td colspan="1" >
		<select id="utm_payment" name="utm_payment[]" class="vm-chzn-select" multiple="multiple" data-placeholder="<?php echo JText::_('COM_VIRTUEMART_PAYMENTMETHOD_S'); ?>"  style="min-width: 150px; ">
		
		<?php
		$default = array(); 
		$utm_p = OPCConfig::getValue('opc_config', 'utm_payments', 0, $default, false, false);
		foreach($this->pms as $p)
		{
		 ?>
		 <option value=<?php echo '"'.$p['payment_method_id'].'" '; if (in_array($p['payment_method_id'], $utm_p)) echo 'selected="selected" '; ?>><?php echo $p['payment_method_name'];?></option>
		 <?php
		}
		
		?>
		</select>
		
	    </td>
		<td>
		<?php echo JText::_('COM_ONEPAGE_UTM_PAYMENT_DESC'); ?>
		</td>
	</tr>

   <?php 
   }
   ?>
   
   
   	
<?php
	   if (!empty($this->pms))
   {
   ?>
	<tr>
	    <td class="key">
	     <label for="opc_payment_isunder" ><?php echo JText::_('COM_ONEPAGE_PAYMENTUNDER'); ?></label>
	    </td>
	    <td colspan="1" >
		<select id="opc_payment_isunder" name="opc_payment_isunder[]" class="vm-chzn-select" multiple="multiple" data-placeholder="<?php echo JText::_('COM_VIRTUEMART_PAYMENTMETHOD_S'); ?>"  style="min-width: 150px; ">
		
		<?php
		$default = array(); 
		$opc_payment_isunder = OPCConfig::getValue('opc_config', 'opc_payment_isunder', 0, $default, false, false);
		foreach($this->pms as $p)
		{
		 ?>
		 <option value=<?php echo '"'.$p['payment_method_id'].'" '; if (in_array($p['payment_method_id'], $opc_payment_isunder)) echo 'selected="selected" '; ?>><?php echo $p['payment_method_name'];?></option>
		 <?php
		}
		
		?>
		</select>
		
	    </td>
		<td>
		<?php echo JText::_('COM_ONEPAGE_PAYMENTUNDER_DESC'); ?>
		</td>
	</tr>

   <?php 
   }
   ?>

	
     </table>
    </fieldset>
    <?php 
    if (false) {
    ?>
    <fieldset class="adminform">
    <legend><?php echo JText::_('COM_ONEPAGE_PAYMENT_ADVANCED'); ?></legend>
        <?php
        jimport( 'joomla.filesystem.folder' );
        $editor = JFactory::getEditor();
        $mce = true; 
         $ofolder = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'payment'.DIRECTORY_SEPARATOR.'onepage';
         if (!file_exists($ofolder))
         {
          if (!JFolder::create($ofolder))
           echo '<span style="color: red;">Cannot create directory for this feature: '.$ofolder.'</span>';
         }
		?>
        <table class="admintable table table-striped" style="width: 100%;">
        <tr>
	    <td class="key">
	     <label for="payment_advanced"><?php echo JText::_('COM_ONEPAGE_PAYMENT_ADVANCED_PAYMENT_LABEL'); ?></label>
	    </td>
	    <td >
	     <input type="checkbox"  name="payment_advanced" id="payment_advanced" value="payment_advanced" <?php if (!empty($payment_advanced)) echo 'checked="checked"' ?> />
	    </td>
	    <td>
	     [ADVANCED_PAYMENT] Enable <b>overriding of ps_checkout::list_payment_methods, ps_payment::list_payment_radio</b> and appropriate payment template files. All the features below need this feature to be enabled. If your payment method modifies VirtueMart's list_payment_method function, these features might not work correctly for you. Please let us know and we might create a custom OnePage extension to support your payment module.  
	    </td>
		</tr>
		<tr>
		<td colspan="3">
		<a href="index.php?option=com_onepage&amp;view=payment">Edit HTML per payment with content editor (SAVE CONFIGURATION FIRST!)</a>
		</td>
		</tr>
		<tr>
		<td colspan="3">
		
		and if joomfish available configure per language settings:
		<?php
		 if (!empty($this->codes))
	   {
	   ?>
	   <select name="payment_per_lang">
	   <?php
	   	   foreach ($this->codes as $uu)
	    {
		?>
	     <option value="<?php echo $uu['code']; ?>"><?php echo $uu['code']; ?></option>
		<?php 
		}
		?>
	   </select><input type="button" class="btn btn-small btn-success" value="Edit..." onclick="javascript: submitbutton('perlangedit');" /><br />
	   If content editor cannot save your payment information please edit the files directly in /administrator/components/com_virtuemart/classes/payment/onepage/{lang2code}/{payment_id}.part.html On some systems there are problems with relative image paths when using SEO and JCE.
	   <?php 
	   $num++;
	   
	   
	   }
	   else
	   {
	    echo 'jos_languages not found. JoomFISH not installed.';
	   }
	   ?>
		</td>

		</tr>
        <?php
		if (!empty($this->pms))
        foreach($this->pms as $p)
        {
        ?>
        <tr>
        <td class="key">
        Set text for<br />
         <?php echo $p['payment_method_name'];
         ?>
        </td>
        <td colspan="2">
        <?php
         $id = $p['payment_method_id'];
         if (file_exists($ofolder.DIRECTORY_SEPARATOR.$id.'.part.html')) 
         $html = file_get_contents($ofolder.DIRECTORY_SEPARATOR.$id.'.part.html');
         else $html = ''; 
         
         $id = $p['payment_method_id']; 
         echo 'You can use {payment_discount} to insert payment fee or discount at a specific location. If not used, it will be automatically appended at the end.<br />';
		 if (!$mce)
		 echo $editor->display('payment_content_'.$id, $html, '550', '400', '60', '20', true);
		 else echo '<textarea id="payment_content_'.$id.'" style="width: 550px; height: 400px;" cols="60" rows="20">'.$html.'</textarea>';
		 echo '<input type="hidden" name="payment_contentid_'.$id.'"/>';
        ?>
        </td>
        </tr>
        <?php
        }
        ?>
        </table>
        
    </fieldset>
    
    <?php
    }
    echo $pane->endPanel(); 
	if (false)
	{
	echo $pane->startPanel('Coupons', 'panel7');
			?>
			 <fieldset class="adminform">
        <legend>Coupon Products configuration</legend>
        <table class="admintable table table-striped" style="width: 100%;">
		<tr>
		 <h2>Experimental !</h2>This feature is built for K2 + Virtuemart coupon selling features. <br />
		 You need to set up available date for coupon products and optionally end date in attribute of the product. 
		</tr>
        <tr>
	    <td class="key">
	     <label for="fix_encoding">Coupon Products </label>
	    </td>
	    <td >
	     <input type="text" name="coupon_products" style="width: 200px;" id="coupon_products" value="<?php if (!empty($coupon_products)) echo $coupon_products; ?>" />
	    </td>
		<td>
		 Please enter product IDs separated by comma for which coupon code should be automatically generated on purchase and activated on order status change to confirmed. 
		</td>
		</tr>
        <tr>
		<tr>
	    <td class="key">
	     <label for="all_products">All products</label>
	    </td>
	    <td>
	     <input type="checkbox" name="all_products" style="width: 200px;" id="all_products" value="<?php if (!empty($coupon_products)) echo $coupon_products; ?>" />
	    </td>
		<td>
		 Please enter product IDs separated by comma for which coupon code should be automatically generated on purchase and activated on order status change to confirmed. 
		</td>
		</tr>
        <tr>
		</tr>
		</table>
		</fieldset>
		<?php
			echo $pane->endPanel(); 
			}
            echo $pane->startPanel(JText::_('COM_ONEPAGE_DISPLAY_PANEL'), 'panelz7');
?>
		<fieldset class="adminform">
        <legend><?php echo JText::_('COM_ONEPAGE_DISPLAY'); ?></legend>
        <table class="admintable table table-striped" style="width: 100%;">
	
		

        	
	   <tr> 
	    <td class="key">
	     <label for="selected_template"><?php echo JText::_('COM_ONEPAGE_DISPLAY_SELECTED_TEMPLATE_LABEL'); ?></label>
	    </td>
		
	    <td colspan="1" >
		<?php echo JText::_('COM_ONEPAGE_DESKTOP_THEME'); ?><br />
	     <select style="float: left; max-width: 200px; "  name="selected_template" id="selected_template">
	     <?php
		 
	     if (!empty($this->templates)) 
	     foreach($this->templates as $t)
	     {
		  if ($t == 'extra') continue; 
	      ?>
	      <option value="<?php echo $t; ?>" <?php if ((empty($selected_template) && ($t=='default')) || ($selected_template == $t)) echo ' selected="selected" '; ?>><?php echo $t; ?></option>
	      <?php
	     }
	     ?>
	     </select>
		 <br style="clear: both;" />
		<?php 
			
		echo JText::_('COM_ONEPAGE_MOBILE_THEME'); ?><br />
		<select style="float: left; max-width: 200px;"  name="mobile_template" id="mobile_template">
	     <option value=""><?php echo JText::_('COM_ONEPAGE_THE_SAME_AS_DESKTOP'); ?></option>
		 <?php
		 
	     if (!empty($this->templates)) 
	     foreach($this->templates as $t)
	     {
		  if ($t == 'extra') continue; 
	      ?>
	      <option value="<?php echo $t; ?>" <?php 
		  if (!empty($mobile_template)) 
		  if (($mobile_template == $t)) 
		  echo ' selected="selected" '; ?>><?php echo $t; ?></option>
	      <?php
	     }
	     ?>
	     </select>
		 </td>
		<td colspan="1">
		
		 <input style="float: left;" type="checkbox" name="load_min_bootstrap" value="1" <?php if (!empty($load_min_bootstrap)) echo ' checked="checked" '; ?> id="load_min_bootstrap" /><label style="float: left; clear: right; margin: 0;" for="load_min_bootstrap"><?php echo JText::_('COM_ONEPAGE_DISPLAY_LOAD_MIN_BOOTSTRAP'); ?></label>
		 <?php if (false) { ?>
		 <input style="float: left;"type="checkbox" name="opc_rtl" value="1" <?php if (!empty($opc_rtl)) echo ' checked="checked" '; ?> id="opc_rtl" /><label style="float: left; clear: right; margin: 0;" for="opc_rtl"><?php echo JText::_('COM_ONEPAGE_DISPLAY_OPC_RTL'); ?></label>
		 <?php } ?>
		  <br style="clear: both;"/><a href="index.php?option=com_onepage&amp;view=edittheme"><?php echo JText::_('COM_ONEPAGE_OPC_THEME_EDITOR'); ?>...</a>
		 <br />
	     <input class="text_area" type="hidden" name="override_css_by_class" id="override_css_by_class" value=""/>
	     <input class="text_area" type="hidden" name="override_css_by_id" id="override_css_by_id" value="<?php if (!empty($op_ids)) echo $op_ids ?>"/>
		 <input type="hidden" name="php_logged" value="onepage.logged.tpl.php" />
		 <input type="hidden" name="css_logged" value="onepage.css" />
 		 <input type="hidden" name="php_unlogged" value="onepage.unlogged.tpl.php" />
		 <input type="hidden" name="css_unlogged" value="onepage.css" />

	    </td>
		</tr>
		
		<?php 
		if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$selected_template.DIRECTORY_SEPARATOR.'config.xml'))
		{
		?>
		<tr>
	    <td class="key">
		<label><?php echo JText::_('COM_ONEPAGE_THEME_CONFIG'); ?></label>
		</td>
		<td colspan="2">
				 <a id="theme_config" href="index.php?option=com_onepage&amp;view=themeconfig"><?php echo JText::_('COM_ONEPAGE_THEMESPECIFIC_CONFIG').' '.$selected_template.'...'; ?></a>
		</td>
		</tr>
		<?php 
		}
		?>
		
		
		<tr>
	    <td class="key"><label><?php echo JText::_('COM_ONEPAGE_RENAME_THEME'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_RENAME_THEME'); ?></td>
		<td>
				 <input type="button" class="btn btn-small btn-success" name="rename_theme" value="<?php echo JText::_('COM_ONEPAGE_RENAME_TO_CUSTOM');  ?>" id="rename_theme" onclick="javascript: submitbutton('rename_theme');"/>
		</td>
		<td>
				<label for="rename_theme"><?php echo JText::_('COM_ONEPAGE_RENAME_THEME_DESC'); ?> 
				</label>
		</td>
		</tr>
        <tr style="display: none;">
	    <td class="key">
	     <label for="op_numrelated">[SOON AVAILABLE]<br /><?php echo JText::_('COM_ONEPAGE_DISPLAY_NUM_RELATED_LABEL'); ?></label>
	    </td>
	    <td>
	     <input type="text" name="op_numrelated" id="op_numrelated" value="<?php if (empty($op_numrelated) || (!is_numeric($op_numrelated))) echo '0'; else echo $op_numrelated; ?>" />
	    </td>
	    <td>
	     <?php echo JText::_('COM_ONEPAGE_DISPLAY_NUM_RELATED_DESC'); ?>
	    </td>
		</tr>
		
        <tr>
	    <td class="key">
	     <label for="op_customitemid"><?php echo JText::_('COM_ONEPAGE_DISPLAY_CUSTOM_ITEM_ID_LABEL'); echo $flag; ?></label>
	    </td>
	    <td>
	     <input type="text" id="op_customitemid" value="<?php 
		 
		 if (!isset($newitemid)) $newitemid = ''; 
		 $newitemid = OPCconfig::getValue('opc_config', 'newitemid', 0, $newitemid, $opclang); 
		 
		 if (!empty($newitemid)) echo $newitemid; ?>" name="newitemid" />
	     
	    </td>
	    <td>
	     <?php echo JText::_('COM_ONEPAGE_DISPLAY_CUSTOM_ITEM_ID_DESC'); ?>
	    </td>
		</tr>

		        <tr>
	    <td class="key">
	     <label for="op_customitemidty"><?php echo JText::_('COM_ONEPAGE_DISPLAY_CUSTOM_ITEM_ID_LABEL_TY'); echo $flag; ?></label>
	    </td>
	    <td>
	     <input type="text" id="op_customitemidty" value="<?php 
		 
		  if (!isset($op_customitemidty)) $op_customitemidty = ''; 
	  $op_customitemidty = OPCconfig::getValue('opc_config', 'op_customitemidty', 0, $op_customitemidty, $opclang); 
		 
		 if (!empty($op_customitemidty)) echo $op_customitemidty; ?>" name="op_customitemidty" />
	     
	    </td>
	    <td>
	     <?php echo JText::_('COM_ONEPAGE_DISPLAY_CUSTOM_ITEM_ID_DESC_TY'); ?>
	    </td>
		</tr>

		
		
        <tr>
	    <td class="key">
	     <label for="op_articleid"><?php echo JText::_('COM_ONEPAGE_DISPLAY_ARTICLE_ID_LABEL'); echo $flag; ?></label>
	    </td>
	    <td>
		 <?php if (false) { ?>
	     <input type="text" id="op_articleid" value="<?php if (!empty($op_articleid)) echo $op_articleid; ?>" name="op_articleid" /> <?php } ?>
	     <?php echo $this->articleselector2; ?>
		 <input type="button" class="btn btn-small btn-success" onclick="return clearArticle('op_articleid');" value="<?php echo JText::_('COM_ONEPAGE_DISPLAY_ARTICLE_ID_VALUE'); ?>" />
	    </td>
	    <td>
	     <?php echo JText::_('COM_ONEPAGE_DISPLAY_ARTICLE_ID_DESC'); ?>  
	    </td>
		</tr>
		
	<tr>
	
	    <td class="key">
	     <label for="show_full_tos" ><?php echo JText::_('COM_ONEPAGE_DISPLAY_SHOW_FULL_TOS_LABEL'); ?></label>
	    </td>
	    <td  colspan="2">
		
		<?php
		$d = VmConfig::get('agree_to_tos_onorder', '1'); 
		$vmtos = (int)VmConfig::get('agree_to_tos_onorder', '1'); 


		?>
		
		 <input <?php if (!$is_admin) echo ' disabled="disabled" '; ?> type="checkbox" value="1" name="full_tos_logged"  <?php if (!empty($full_tos_logged)) echo ' checked="checked" '; ?> /> <?php echo JText::_('COM_ONEPAGE_DISPLAY_SHOW_FULL_TOS_LOGGED') ?><br style="clear: both;"/>
		 <input <?php if (!$is_admin) echo ' disabled="disabled" '; ?> type="checkbox" value="1" name="full_tos_unlogged" <?php  if (!empty($full_tos_unlogged)) echo ' checked="checked" '; ?> /> <?php echo JText::_('COM_ONEPAGE_DISPLAY_SHOW_FULL_TOS_UNLOGGED'); ?>
		 <br style="clear: both;" />
		 <?php OPCVideoHelp::show('COM_ONEPAGE_DISPLAY_SHOW_FULL_TOS_UNLOGGED'); ?>
		 <br style="clear: both;" />
		 <input <?php if (!$is_admin) echo ' disabled="disabled" '; ?> type="checkbox" value="1" name="tos_logged" <?php if ($vmtos) echo ' checked="checked"  '; else if (!empty($tos_logged)) echo ' checked="checked" '; if (!isset($tos_unlogged)) echo ' checked="checked" '; ?>/> <?php echo JText::_('COM_ONEPAGE_DISPLAY_SHOW_TOS_LOGGED').' '; echo JText::_('COM_ONEPAGE_WILL_ALTER_VIRTUEMART_CONFIGURATION');  ?><br style="clear: both;" />
		 <input <?php if (!$is_admin) echo ' disabled="disabled" '; ?> type="checkbox" value="1" name="tos_unlogged" <?php if ($vmtos) echo ' checked="checked"  '; else if (!empty($tos_unlogged)) echo ' checked="checked" '; if (!isset($tos_unlogged)) echo ' checked="checked" '; ?> /> <?php echo JText::_('COM_ONEPAGE_DISPLAY_SHOW_TOS_UNLOGGED').' '; echo JText::_('COM_ONEPAGE_WILL_ALTER_VIRTUEMART_CONFIGURATION');  ?><br style="clear: both;" />
	    
		 <?php OPCVideoHelp::show('COM_ONEPAGE_DISPLAY_SHOW_TOS_UNLOGGED'); ?>
		 <br style="clear: both;" />
		 <input type="checkbox" value="1" name="tos_scrollable" <?php if (!empty($tos_scrollable)) echo ' checked="checked" '; ?> /><?php echo JText::_('COM_ONEPAGE_DISPLAY_SHOW_TOS_SCROLLABLE'); ?><br style="clear: both;" />
		</td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="tos_config" ><?php echo JText::_('COM_ONEPAGE_DISPLAY_SHOW_TOS_CONFIG'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_DISPLAY_SHOW_FULL_TOS_UNLOGGED'); ?>
	    </td>
	    <td>
		<?php echo $this->articleselector; ?>
	     <?php
		 if (false) { ?><input class="text_area" type="text" name="tos_config" id="tos_config" size="10" value="<?php if (!empty($tos_config)) echo $tos_config; ?>"/>
		 <?php } ?>
		 </td>
		 <td>
		 <?php echo JText::_('COM_ONEPAGE_DISPLAY_SHOW_TOS_CONFIG_DESC'); ?>
		 </td>
	</tr>
    <tr>
	  <td></td>
	  <td>
		 <?php echo JText::_('COM_ONEPAGE_DISPLAY_TOS_ITEM_ID_DESC'); echo $flag; ?>
	  </td>
	  <td>
	  <input type="text" name="tos_itemid" value="<?php 
	  
	  if (!isset($tos_itemid)) $tos_itemid = ''; 
	  $tos_itemid = OPCconfig::getValue('opc_config', 'tos_itemid', 0, $tos_itemid, $opclang); 
	  
	  if (!empty($tos_itemid)) echo $tos_itemid; ?>"/>  
		 
	  </td>
	</tr>
	<tr>
	    <td></td>
	   
		<td>
		<?php echo JText::_('COM_ONEPAGE_DISPLAY_TOS_RESET'); ?>
	    </td>
		 <td >
		<input type="button" class="btn btn-small btn-success" onclick="javascript: return clearArticle('tos_config');" value="<?php echo JText::_('COM_ONEPAGE_DISPLAY_TOS_RESET_VALUE'); ?>" />
		</td>
	</tr>

	<tr>
	    <td class="key">
	     <label for="op_no_basket" ><?php echo JText::_('COM_ONEPAGE_DISPLAY_NO_BASKET_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_DISPLAY_NO_BASKET_LABEL'); ?>
	    </td>
	    <td>
	     <input class="op_no_basket" type="checkbox" value="1" name="op_no_basket" id="op_no_basket" <?php if (!empty($op_no_basket)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_DISPLAY_NO_BASKET_DESC'); ?>
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="no_login_in_template" ><?php echo JText::_('COM_ONEPAGE_DISPLAY_NO_LOGIN_TEMPLATE_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="no_login_in_template" type="checkbox" value="1" name="no_login_in_template" id="no_login_in_template" <?php if (!empty($no_login_in_template)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_DISPLAY_NO_LOGIN_TEMPLATE_DESC'); ?>
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="no_continue_link" ><?php echo JText::_('COM_ONEPAGE_DISPLAY_NO_CONTINUE_LINK_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="no_continue_link" type="checkbox" value="1" name="no_continue_link" id="no_continue_link" <?php if (!empty($no_continue_link)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_DISPLAY_NO_CONTINUE_LINK_DESC'); ?>
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="no_extra_product_info" ><?php echo JText::_('COM_ONEPAGE_DISPLAY_NO_EXTRA_PRODUCT_INFO_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="no_extra_product_info" type="checkbox" value="1" name="no_extra_product_info" id="no_extra_product_info" <?php if (!empty($no_extra_product_info)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo strip_tags(JText::_('COM_ONEPAGE_DISPLAY_NO_EXTRA_PRODUCT_INFO_DESC')); ?> 
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="no_alerts" ><?php echo JText::_('COM_ONEPAGE_DISPLAY_NO_ALERTS_LABEL') ?></label>
	    </td>
	    <td>
	     <input class="no_alerts" type="checkbox" value="1" name="no_alerts" id="no_alerts" <?php if (!empty($no_alerts)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_DISPLAY_NO_ALERTS_DESC'); ?>
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="no_coupon_ajax" ><?php echo JText::_('COM_ONEPAGE_DISPLAY_NO_COUPON_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="no_coupon_ajax" type="checkbox" value="1" name="no_coupon_ajax" id="no_coupon_ajax" <?php if (!empty($no_coupon_ajax)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_DISPLAY_NO_COUPON_DESC'); ?>
	    </td>
	</tr>
	
	
	<tr>
	    <td class="key">
	     <label for="ajaxify_cart" ><?php echo JText::_('COM_ONEPAGE_DISPLAY_AJAXIFY_CART_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="ajaxify_cart" type="checkbox" value="1" name="ajaxify_cart" id="ajaxify_cart" <?php if (!empty($ajaxify_cart)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_DISPLAY_AJAXIFY_CART_DESC'); ?> 
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="use_original_basket" ><?php echo JText::_('COM_ONEPAGE_USE_ORIGINAL_BASKET_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="use_original_basket" type="checkbox" value="1" name="use_original_basket" id="use_original_basket" <?php if (!empty($use_original_basket)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_USE_ORIGINAL_BASKET_DESC'); ?> 
	    </td>
	</tr>

		<tr>
	    <td class="key">
	     <label for="opc_editable_attributes" ><?php echo JText::_('COM_ONEPAGE_EDITABLE_ATTRIBUTES_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="opc_editable_attributes" type="checkbox" value="1" name="opc_editable_attributes" id="opc_editable_attributes" <?php if (!empty($opc_editable_attributes)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_EDITABLE_ATTRIBUTES_DESC'); ?> 
	    </td>
	</tr>
	
	<tr>
	    <td class="key">
	     <label for="opc_show_sdesc" ><?php echo JText::_('COM_ONEPAGE_SHOW_SHORTDESC_IN_BASKET'); ?></label>
	    </td>
	    <td>
	     <input class="opc_show_sdesc" type="checkbox" value="1" name="opc_show_sdesc" id="opc_show_sdesc" <?php if (!empty($opc_show_sdesc)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_SHOW_SHORTDESC_IN_BASKET_DESC'); ?> 
	    </td>
	</tr>

		<tr>
	    <td class="key">
	     <label for="opc_show_weight" ><?php echo JText::_('COM_ONEPAGE_SHOW_WEIGHT_BASKET_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="opc_show_weight" type="checkbox" value="1" name="opc_show_weight" id="opc_show_weight" <?php if (!empty($opc_show_weight)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_SHOW_WEIGHT_BASKET_DESC'); ?> 
	    </td>
	</tr>
	
		<tr>
	    <td class="key">
	     <label for="opc_confirm_dialog" ><?php echo JText::_('COM_ONEPAGE_DISPLAY_CONFIRM_POPUP'); ?></label>
	    </td>
	    <td>
	     <input class="opc_confirm_dialog" type="checkbox" value="1" name="opc_confirm_dialog" id="opc_confirm_dialog" <?php if (!empty($opc_confirm_dialog)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_DISPLAY_CONFIRM_POPUP_DESC'); ?> 
	    </td>
	</tr>

	

		<tr>
	    <td class="key">
	     <label for="opc_only_parent_links" ><?php echo JText::_('COM_ONEPAGE_DISPLAY_ALWAYS_LINK_PARENT_PRODUCTS_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="opc_only_parent_links" type="checkbox" value="1" name="opc_only_parent_links" id="opc_only_parent_links" <?php if (!empty($opc_only_parent_links)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_DISPLAY_ALWAYS_LINK_PARENT_PRODUCTS_DESC'); ?> 
	    </td>
	</tr>
	
	
	<tr>
	    <td class="key">
	     <label for="opc_no_cart_p_links" ><?php echo JText::_('COM_ONEPAGE_DISPLAY_NO_LINKS_IN_CART'); ?></label>
	    </td>
	    <td>
	     <input class="opc_no_cart_p_links" type="checkbox" value="1" name="opc_no_cart_p_links" id="opc_no_cart_p_links" <?php if (!empty($opc_no_cart_p_links)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_DISPLAY_NO_LINKS_IN_CART_DESC'); ?> 
	    </td>
	</tr>
	

	<tr>
	    <td class="key">
	     <label for="opc_url_addtocart" ><?php echo JText::_('COM_ONEPAGE_DISPLAY_LINKSTOADDTOCART'); ?></label>
	    </td>
	    <td>
	     <input class="opc_url_addtocart" type="checkbox" value="1" name="opc_url_addtocart" id="opc_url_addtocart" <?php if (!empty($opc_url_addtocart)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_DISPLAY_LINKSTOADDTOCART_DESC'); ?> 
	    </td>
	</tr>
	
	
	<tr>
	    <td class="key">
	     <label for="opc_no_joomla_notices" ><?php echo JText::_('COM_ONEPAGE_DISPLAY_NOJOOMLANOTICES'); ?></label>
	    </td>
	    <td>
	     <input class="opc_no_joomla_notices" type="checkbox" value="1" name="opc_no_joomla_notices" id="opc_no_joomla_notices" <?php if (!empty($opc_no_joomla_notices)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_DISPLAY_NOJOOMLANOTICES_DESC'); ?> 
	    </td>
	</tr>

	
        
        </table>
        </fieldset>
		
		
		
		<fieldset class="adminform">
		<legend><?php echo JText::_('COM_ONEPAGE_STOCK_DISPLAY'); ?></legend>
		
		 <table class="admintable table table-striped" style="width: 100%;">
		 <tr>
	    <td class="key">
	     <label for="op_colorfy_products" ><?php echo JText::_('COM_ONEPAGE_STOCK_COLORFY_PRODUCTS'); ?></label>
		 
	    </td>
	    <td>
	     <input class="op_colorfy_products" value="1" type="checkbox"  name="op_colorfy_products" id="op_colorfy_products" <?php if (!empty($op_colorfy_products)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_STOCK_COLORFY_PRODUCTS_DESC'); ?>
	    </td>
		</tr>
		
		<tr>
	    <td class="key">
	     <label for="op_ignore_ordered_products" ><?php echo JText::_('COM_ONEPAGE_STOCK_COLORFY_PRODUCTS_INGORE_ORDERED'); ?></label>
		 
	    </td>
	    <td>
	     <input class="op_ignore_ordered_products" value="1" type="checkbox"  name="op_ignore_ordered_products" id="op_ignore_ordered_products" <?php if (!empty($op_ignore_ordered_products)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_STOCK_COLORFY_PRODUCTS_INGORE_ORDERED_DESC'); ?>
	    </td>
		</tr>
		
		<tr>
	    <td class="key">
	     <label for="op_color_codes" ><?php echo JText::_('COM_ONEPAGE_STOCK_COLORFY_PRODUCTS_COLOR_CODES'); ?></label>
		 
	    </td>
	    <td colspan="2">
		   <table class="admintable table table-striped" style="width: 100%;">
		   
		   <tr><th><?php echo JText::_('COM_ONEPAGE_SHIPPING_DISABLE_PAYMENT_ENABLE_LABEL'); ?></th>
		   <th><?php echo JText::_('COM_ONEPAGE_STOCK_COLORFY_PRODUCTS_COLOR_CODES'); ?>
		   
		   </th>
		   
		   <th style="min-width: 30%;"><?php echo JText::_('COM_ONEPAGE_LABEL'); ?><br />(<?php echo JText::_('COM_ONEPAGE_LEAVE_EMPTY_TO_DISABLE'); ?>)
		   
		   </th>
		   
		   </tr>
		   
		   <tr>
		   <td><input type="checkbox" value="1" name="op_color_codes_enabled[0]" <?php if ((!empty($op_color_codes_enabled)) && (!empty($op_color_codes_enabled[0]))) echo ' checked="checked" '; ?> />
		   </td>
		   <td>
		<?php
		  if (!isset($op_color_codes))
		  {
			  $op_color_codes = array(); 
			  $op_color_codes[0] = '#008000'; 
			  $op_color_codes[1] = '#ff0000'; 
			  $op_color_codes[2] = '#0000ff'; 
		  }
		  
		  if (!isset($op_color_texts))
		  {
			  $op_color_texts[0] = 'COM_VIRTUEMART_PRODUCT_IN_STOCK'; 
			  $op_color_texts[1] = 'COM_VIRTUEMART_CART_PRODUCT_OUT_OF_STOCK'; 
			  $op_color_texts[2] = 'COM_VIRTUEMART_SEARCH_ORDER_LOW_STOCK_NOTIFICATION'; 
			  $op_color_texts[3] = 'COM_ONEPAGE_QUANTITY_REQUESTED_LARGER_THAN_AVAILABLE_STOCK'; 
			  
		  }
		  
		?>
		
		 <label for="op_color_codes_0"><?php echo JText::_('COM_ONEPAGE_STOCK_COLORFY_PRODUCTS_COLOR_CODES_INSTOCK'); ?>
	     <input class="op_color_codes" type="color"  name="op_color_codes[0]" id="op_color_codes_0" <?php if (!empty($op_color_codes) & (!empty($op_color_codes[0]))) echo ' style="color: '.$op_color_codes[0].'" value="'.$op_color_codes[0].'"'; 
		 else echo ' value="" '; 
		 ?>/>
		 </label>
		 
		 </td>
		  <td><input type="text" name="op_color_texts[0]" value="<?php if (!empty($op_color_texts[0])) echo $op_color_texts[0]; ?>" /><br /><div style="float: left; clear: both;"><?php echo JText::_('COM_ONEPAGE_DEFAULT'); ?>: COM_VIRTUEMART_PRODUCT_IN_STOCK</div>
		  </td>
		 
		 </tr>
		 
		  <tr>
		   <td><input type="checkbox" value="1" name="op_color_codes_enabled[1]" <?php if ((!empty($op_color_codes_enabled)) && (!empty($op_color_codes_enabled[1]))) echo ' checked="checked" '; ?> />
		   </td>
		   <td>
		 
		 
		  <label for="op_color_codes_1"><?php echo JText::_('COM_ONEPAGE_STOCK_COLORFY_PRODUCTS_COLOR_CODES_NOTINSTOCK'); ?>
	     <input class="op_color_codes" type="color"  name="op_color_codes[1]" id="op_color_codes_1" <?php if (!empty($op_color_codes) & (!empty($op_color_codes[1]))) echo ' style="color: '.$op_color_codes[1].'" value="'.$op_color_codes[1].'"'; 
		else echo ' value="" '; 
		 ?>/>
		 </label>
		 
		  </td>
		  <td><input type="text" name="op_color_texts[1]" value="<?php if (!empty($op_color_texts[1])) echo $op_color_texts[1]; ?>" /><br /><div style="float: left; clear: both;"><?php echo JText::_('COM_ONEPAGE_DEFAULT'); ?>: COM_VIRTUEMART_CART_PRODUCT_OUT_OF_STOCK</div>
		  </td>
		  
		  
		 </tr>
		 
		  <tr>
		   <td><input type="checkbox" value="1" name="op_color_codes_enabled[2]" <?php if ((!empty($op_color_codes_enabled)) && (!empty($op_color_codes_enabled[2]))) echo ' checked="checked" '; ?> />
		   </td>
		   
		 
		   
		   <td>
		 
		 
		  <label for="op_color_codes_2"><?php echo JText::_('COM_ONEPAGE_STOCK_COLORFY_PRODUCTS_COLOR_CODES_QUESTIONABLE'); ?>
	     <input class="op_color_codes" type="color"  name="op_color_codes[2]" id="op_color_codes_2" <?php if (!empty($op_color_codes) & (!empty($op_color_codes[2]))) echo ' style="color: '.$op_color_codes[2].'" value="'.$op_color_codes[2].'"'; 
		 else echo ' value="" '; 
		 ?>/>
		 </label>
		 
		 
		  </td>
		  <td><input type="text" name="op_color_texts[2]" value="<?php if (!empty($op_color_texts[2])) echo $op_color_texts[2]; ?>" /><br /><div style="float: left; clear: both;"><?php echo JText::_('COM_ONEPAGE_DEFAULT'); ?>: COM_VIRTUEMART_SEARCH_ORDER_LOW_STOCK_NOTIFICATION</div>
		  </td>
		  
		  
		 </tr>
		 
		  <tr>
		   <td><input type="checkbox" value="1" name="op_color_codes_enabled[3]" <?php if ((!empty($op_color_codes_enabled)) && (!empty($op_color_codes_enabled[3]))) echo ' checked="checked" '; ?> />
		   </td>
		   <td>
		 
		  <label for="op_color_codes_3"><?php echo JText::_('COM_ONEPAGE_STOCK_COLORFY_PRODUCTS_COLOR_CODES_QUANTITYLARGERSTOCK'); ?>
	     <input class="op_color_codes" type="color"  name="op_color_codes[3]" id="op_color_codes_3" <?php if (!empty($op_color_codes) & (!empty($op_color_codes[3]))) echo ' style="color: '.$op_color_codes[3].'" value="'.$op_color_codes[3].'"'; 
		 else echo ' value="" '; 
		 ?>/>
		 </label>
		 
		  </td>
		  
		    <td><input type="text" name="op_color_texts[3]" value="<?php if (!empty($op_color_texts[3])) echo $op_color_texts[3]; ?>" /><br /><div style="float: left; clear: both;"><?php echo JText::_('COM_ONEPAGE_DEFAULT'); ?>: COM_ONEPAGE_QUANTITY_REQUESTED_LARGER_THAN_AVAILABLE_STOCK</div>
		  </td>
		  
		 </tr>
		 
		 
		  </table>
		 
		 
	    </td>
	    
		</tr>
		
		
		</table>
		
		
		
		</fieldset>
		
        <?php 
         echo $pane->endPanel();
		 




                    echo $pane->startPanel(JText::_('COM_ONEPAGE_REGISTRATION_PANEL'), 'panela8');
					
					?>
					<fieldset class="adminform">
		 <legend><?php echo JText::_('COM_ONEPAGE_REGISTRATION'); ?></legend>
		 <table class="admintable table table-striped" id="comeshere2x" style="width: 100%;">
		 <tr>
	    <td class="key">
	     <label for="op_redirect_joomla_to_vm" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_REDIRECT_JOOMLA_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_REGISTRATION_REDIRECT_JOOMLA_LABEL'); ?>
	    </td>
	    <td>
	     <input class="op_redirect_joomla_to_vm" value="1" type="checkbox"  name="op_redirect_joomla_to_vm" id="op_redirect_joomla_to_vm" <?php if (!empty($op_redirect_joomla_to_vm)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_REGISTRATION_REDIRECT_JOOMLA_DESC'); ?>
	    </td>
		</tr>
		<tr>
	    <td class="key">
	     <label for="opc_override_registration" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_OVERRIDE_VMREGISTRATION_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="opc_override_registration" value="1" type="checkbox" name="opc_override_registration" id="opc_override_registration" <?php

		  $db = JFactory::getDBO(); 
		  $q = "select * from #__extensions where element = 'opcregistration' and type='plugin' and folder='system' limit 0,1"; 
		  $db->setQuery($q); 
		  $isInstalled = $db->loadAssoc(); 
		  
		  if (empty($isInstalled) || (empty($isInstalled['enabled'])))
		  {
		  $opc_override_registration = false; 
		  }
		  else
		  $opc_override_registration = true; 
		  
		 if (!empty($opc_override_registration)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_REGISTRATION_OVERRIDE_VMREGISTRATION_DESC'); ?>
	    </td>
		</tr>
		
		
		<tr>
	    <td class="key">
	     <label for="opc_override_registration_logged" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_OVERRIDE_VMREGISTRATION_LABEL2'); ?></label>
	    </td>
	    <td>
	     <input class="opc_override_registration_logged" value="1" type="checkbox" name="opc_override_registration_logged" id="opc_override_registration_logged" <?php

		  $db = JFactory::getDBO(); 
		  $q = "select * from #__extensions where element = 'opcregistration' and type='plugin' and folder='system' limit 0,1"; 
		  $db->setQuery($q); 
		  $isInstalled = $db->loadAssoc(); 
		  
		  if (empty($isInstalled) || (empty($isInstalled['enabled'])))
		  {
		    $opc_override_registration_logged = false; 
		  }
		 
		  
		 if (!empty($opc_override_registration_logged)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_REGISTRATION_OVERRIDE_VMREGISTRATION_DESC2'); ?>
	    </td>
		</tr>

		
		
				 <tr>
	    <td class="key">
	     <label for="op_never_log_in" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_NEVER_LOGIN_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="op_never_log_in" value="1" type="checkbox" name="op_never_log_in" id="op_never_log_in" <?php if (!empty($op_never_log_in)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_REGISTRATION_NEVER_LOGIN_DESC'); ?> 
	    </td>
		</tr>
		 	<tr>
	    <td class="key">
	     <label for="op_usernameisemail" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_USERNAME_IS_EMAIL_LABEL'); ?></label><?php OPCVideoHelp::show('COM_ONEPAGE_REGISTRATION_USERNAME_IS_EMAIL_LABEL'); ?>
	    </td>
	    <td  >
	     <input type="checkbox" name="op_usernameisemail" id="op_usernameisemail" value="op_usernameisemail" <?php if (isset($op_usernameisemail)) if ($op_usernameisemail==true) echo 'checked="checked"';?> />
	    </td>
	    <td>
	     <?php echo JText::_('COM_ONEPAGE_REGISTRATION_USERNAME_IS_EMAIL_DESC'); ?>
	    </td>
		</tr>
		
	    <tr>
		 <td class="key">
		 <label for="opc_check_username" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_USERNAME_CHECK_LABEL'); ?></label>
		</td>
		<td>
		  <input type="checkbox" name="opc_check_username" value="1" <?php if (!empty($opc_check_username)) echo ' checked="checked" '; ?> />
		</td>
		<td>
		<table>
		<tr>
		 <td colspan="2">
		 <?php echo JText::_('COM_ONEPAGE_REGISTRATION_USERNAME_CHECK_DESC'); ?>
		 </td>
		</tr>
		<tr>
		<td>
		 <input type="checkbox" name="opc_no_duplicit_username" value="1" <?php if (!empty($opc_no_duplicit_username)) echo ' checked="checked" '; ?> />
		</td>
		<td>
		 <?php echo JText::_('COM_ONEPAGE_REGISTRATION_USERNAME_NO_DUPLICIT'); ?>
		</td>
		</tr>
		</table>
		</td>
		</tr>

	    <tr>
		 <td class="key">
		 <label for="opc_check_email" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_EMAIL_CHECK_LABEL') ?></label>
		</td>
		<td>
		  <input type="checkbox" name="opc_check_email" value="1" <?php if (!empty($opc_check_email)) echo ' checked="checked" '; ?> />
		</td>
		<td>
		
		
				<table>
		<tr>
		 <td colspan="2">
		 <?php echo JText::_('COM_ONEPAGE_REGISTRATION_EMAIL_CHECK_DESC'); ?> 
		 </td>
		</tr>
		<tr>
		<td>
		 <input type="checkbox" name="opc_no_duplicit_email" value="1" <?php if (!empty($opc_no_duplicit_email)) echo ' checked="checked" '; ?> />
		</td>
		<td>
		 <?php echo JText::_('COM_ONEPAGE_REGISTRATION_EMAIL_CHECK_NO_DUPLICIT'); ?>
		</td>
		</tr>
		</table>
		
		
		 
		</td>
		</tr>

		
		
	    <tr>
		 <td class="key">
		 <label for="opc_email_in_bt" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_EMAIL_IN_BT_LABEL'); ?></label>
		  
		  
		</td>
		<td>
		  <input type="checkbox" name="opc_email_in_bt" value="1" <?php if (!empty($opc_email_in_bt)) echo ' checked="checked" '; ?> />
		</td>
		<td>
		 <?php echo JText::_('COM_ONEPAGE_REGISTRATION_EMAIL_IN_BT_DESC'); ?>
		</td>
		</tr>
	    <tr>
		 <td class="key">
		 <label for="double_email" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_EMAIL_DOUBLE_LABEL'); ?></label>
		  
		</td>
		<td>
		  <input type="checkbox" name="double_email" value="1" <?php if (!empty($double_email)) echo ' checked="checked" '; ?>/> 
		</td>
		<td>
		<?php echo JText::_('COM_ONEPAGE_REGISTRATION_EMAIL_DOUBLE_DESC'); ?>
		</td>
		</tr>
			<tr>
	    <td class="key">
	     <label for="unlog_all_shoppers"><?php echo JText::_('COM_ONEPAGE_REGISTRATION_UNLOG_ALL_SHOPPERS_LABEL'); ?></label>
	    </td>
	    <td>
	    <input type="checkbox" value="1" id="unlog_all_shoppers" name="unlog_all_shoppers" <?php if ($unlog_all_shoppers==true) echo 'checked="checked"'; ?> /> 
	    </td>
		<td>
		<?php echo strip_tags(JText::_('COM_ONEPAGE_REGISTRATION_UNLOG_ALL_SHOPPERS_DESC')); ?>
		</td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="op_no_display_name" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_NO_DISPLAY_NAME_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="op_no_display_name" type="checkbox" value="1" name="op_no_display_name" id="op_no_display_name" <?php if (!empty($op_no_display_name)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_REGISTRATION_NO_DISPLAY_NAME_DESC'); ?>
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="op_create_account_unchecked" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_CREATE_ACCOUNT_UNCHECKED_LABEL'); ?></label>
	    </td>
	    <td>
	     <input class="op_create_account_unchecked" type="checkbox" value="1" name="op_create_account_unchecked" id="op_create_account_unchecked" <?php if (!empty($op_create_account_unchecked)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td><?php echo JText::_('COM_ONEPAGE_REGISTRATION_CREATE_ACCOUNT_UNCHECKED_DESC'); ?>
	    </td>
	</tr>        
	<tr>
	    <td class="key" >
	     <label for="allow_duplicit" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_ALLOW_DUPLICIT_LABEL'); ?></label>
	    </td>
	    <td>
	    <input type="checkbox" id="allow_duplicit" name="allow_duplicit" value="allow_duplicit" <?php if ($allow_duplicit==true) echo 'checked="checked"'; ?> /> 
	    </td>
	    <td>
	    <?php echo JText::_('COM_ONEPAGE_REGISTRATION_ALLOW_DUPLICIT_DESC'); ?>
	    </td>
	</tr>
	
	<tr>
	<td class="key">
	  	    
	     <label><?php echo JText::_('COM_ONEPAGE_REGISTRATION_ENABLE_CAPTCHA_LABEL'); ?></label>
	    

	</td>
	<td colspan="2">
	  <table style="border: none;">
	   <tr style="border: none;">
	    <td><input type="checkbox" id="enable_captcha_unlogged" name="enable_captcha_unlogged" value="enable_captcha_unlogged" <?php if (!empty($enable_captcha_unlogged)) echo ' checked="checked" '; ?> /> 
		</td>
	    <td>
		<label for="enable_captcha_unlogged" >
			<?php echo JText::_('COM_ONEPAGE_REGISTRATION_ENABLE_CAPTCHA_UNLOGGED'); ?></label>
			
			
		 </td>
		</tr>
		<tr style="border: none;">
		  <td>
		  <input type="checkbox" id="enable_captcha_logged" name="enable_captcha_logged" value="enable_captcha_logged" <?php if (!empty($enable_captcha_logged)) echo ' checked="checked" '; ?> /> 
		  </td>
		  <td>
		    <label for="enable_captcha_logged"><?php echo JText::_('COM_ONEPAGE_REGISTRATION_ENABLE_CAPTCHA_LOGGED'); ?>
			</label>
		   </td>
		   
		 </tr>
		 
		 <?php
		 $vmc = VmConfig::get ('reg_captcha', false); ; 
		 
		 ?>
		 <tr style="border: none;">
		  <td>
		  <input type="checkbox" id="enable_captcha_reg" name="enable_captcha_reg" value="enable_captcha_reg" <?php if ((!empty($enable_captcha_reg) || (!empty($vmc)))) echo ' checked="checked" '; ?> /> 
		  </td>
		  <td>
		    <label for="enable_captcha_reg" class="shared_width_vm"><?php echo JText::_('COM_ONEPAGE_ENABLE_CAPTCHA_IN_OPC_REGISTRATION_OVERRIDE'); ?>
			</label>
		   </td>
		   
		 </tr>
		 
		</table>
	    </td>
	   
	</tr>
	<tr>
	<td class="key">
	  	    
	     <label><?php echo JText::_('COM_ONEPAGE_USER_ACTIVATION_LABEL'); ?></label>
	    

	</td>
	<td colspan="2">
	  
		
			<input type="checkbox" id="opc_do_not_alter_registration" name="opc_do_not_alter_registration" value="opc_do_not_alter_registration" <?php if (!empty($opc_do_not_alter_registration)) echo ' checked="checked" '; ?> /> 
		 <?php echo JText::_('COM_ONEPAGE_USER_ACTIVATION_DESCRIPTION'); ?>
	    </td>
	   
	</tr>

	
	
	<tr>
	<td class="key">
	  	    
	     <label><?php echo JText::_('COM_ONEPAGE_USER_NOACTIVATION_LABEL'); ?></label>
	    

	</td>
	<td colspan="2">
	  
		
			<input type="checkbox" id="opc_no_activation" name="opc_no_activation" value="opc_no_activation" <?php if (!empty($opc_no_activation)) echo ' checked="checked" '; ?> /> 
		 <?php echo JText::_('COM_ONEPAGE_USER_NOACTIVATION_DESC'); ?>
	    </td>
	   
	</tr>

	<tr>
	
	
	<td class="key">
	  	    
	     <label><?php echo JText::_('COM_ONEPAGE_ACYMAILING_CHECKBOX_LABEL'); ?></label>
	    

	</td>
	<td colspan="2">
	  
		
			<input type="checkbox" id="opc_acymailing_checkbox" name="opc_acymailing_checkbox" value="opc_acymailing_checkbox" <?php if (!empty($opc_acymailing_checkbox)) echo ' checked="checked" '; ?> /> <input type="text" value="<?php if (isset($opc_acy_id)) echo $opc_acy_id; else echo "2"; ?>" name="opc_acy_id" />
		 <?php echo JText::_('COM_ONEPAGE_ACYMAILING_CHECKBOX_DESC'); 
		 ?><br /><label for="default_acy_checked"><?php echo JText::_('COM_ONEPAGE_DEFAULT_STATUS'); ?>
		 <select name="default_acy_checked" id="default_acy_checked">
		 <option value=""><?php echo JText::_('COM_ONEPAGE_DEFAULT_STATUS_NOTCHECKED'); ?></option>
		 <option <?php if (!empty($default_acy_checked)) echo ' selected="selected" '; ?> value="1"><?php echo JText::_('COM_ONEPAGE_DEFAULT_STATUS_CHECKED'); ?></option>
		 </select>
		 </label>
	    </td>
	   
	</tr>

	<tr>
	
	
	<td class="key">
	  	    
	     <label><?php echo JText::_('COM_ONEPAGE_DISABLE_EMAIL_LABEL'); ?></label>
	    

	</td>
	<td colspan="2">
	  
		
			<input type="checkbox" id="opc_disable_customer_email"  name="opc_disable_customer_email" value="opc_disable_customer_email" <?php if (!empty($opc_disable_customer_email)) echo ' checked="checked" '; ?> /> <input placeholder="<?php echo htmlentities(JText::_('COM_ONEPAGE_DISABLE_EMAIL_DEFAULTEMAILADDRESS')); ?>" type="text" value="<?php if (isset($opc_disable_customer_email_address)) echo $opc_disable_customer_email_address; else echo ""; ?>" name="opc_disable_customer_email_address" />
		 <?php echo JText::_('COM_ONEPAGE_DISABLE_EMAIL_LABEL_DESC'); 
		 ?><br /><label for="opc_disable_customer_email"><?php echo JText::_('COM_ONEPAGE_DISABLE_EMAIL_ALTERNATIVE_EMAIL'); ?>
		
		 </label>
	    </td>
	   
	</tr>
	
	
		<tr>
	
	
	<td class="key">
	  	    
	     <label><?php echo JText::_('COM_ONEPAGE_ITALIAN_CHECKBOX_LABEL'); ?></label>
	    

	</td>
	<td colspan="2">
	  
		
			<input type="checkbox" id="opc_italian_checkbox" name="opc_italian_checkbox" value="opc_italian_checkbox" <?php if (!empty($opc_italian_checkbox)) echo ' checked="checked" '; ?> /> 
		 <?php echo JText::_('COM_ONEPAGE_ITALIAN_CHECKBOX_DESC'); ?><br /><label for="default_italian_checked"><?php echo JText::_('COM_ONEPAGE_DEFAULT_STATUS'); ?></label>
		 <select name="default_italian_checked" id="default_italian_checked">
		 <option value=""><?php echo JText::_('COM_ONEPAGE_DEFAULT_STATUS_NOTCHECKED'); ?></option>
		 <option <?php if (!empty($default_acy_checked)) echo ' selected="selected" '; ?> value="1"><?php echo JText::_('COM_ONEPAGE_DEFAULT_STATUS_CHECKED'); ?></option>
		 </select>
	    </td>
	   
	</tr>

	
	<tr>
	
	
	<td class="key">
	  	    
	     <label for="opc_conference_mode"><?php echo JText::_('COM_ONEPAGE_CONFERENCE_MODE'); ?></label>
	    

	</td>
	<td colspan="2">
	  
		
			<input type="checkbox" id="opc_conference_mode" name="opc_conference_mode" value="opc_conference_mode" <?php if (!empty($opc_conference_mode)) echo ' checked="checked" '; ?> /> 
		 <label for="opc_conference_mode"><?php echo JText::_('COM_ONEPAGE_CONFERENCE_MODE_DESC'); ?></label>
	    </td>
	   
	</tr>

	
	
	<tr>
	
	
	<td class="key">
	  	    
	     <label><?php echo JText::_('COM_ONEPAGE_REGISTRATION_FIELDS'); ?></label>
	    

	</td>
	<td colspan="2">
	  
		
			<select id="bt_fields_from" name="bt_fields_from">
			<?php
			if (OPCJ3)
			$f = array(0,1,2); 
			else
			$f = array(0,1); 
			foreach ($f as $opt)
			{
			
			 if (defined('VM_VERSION') && (VM_VERSION >= 3))
			 if (empty($opt)) continue; 
			
			  echo '<option value="'.$opt.'"'; 
			  if (isset($bt_fields_from))
			  if ($bt_fields_from == $opt) echo ' selected="selected" '; 
			  //default
			  if (!isset($bt_fields_from))
			  if ($opt == 0) echo ' selected="selected" '; 
			  echo '>'.JText::_('COM_ONEPAGE_REGISTRATION_FIELDS_OPT'.$opt).'</option>'; 
			}
			
			?>
			</select>
		 <?php echo JText::_('COM_ONEPAGE_REGISTRATION_FIELDS_DESC'); ?>
	    </td>
	   
	</tr>
	

	
		</table>
		</fieldset>
		
		
		<?php
 if (!defined('VM_REGISTRATION_TYPE'))
   {
    if (VmConfig::get('oncheckout_only_registered', 0))
	{
	  if (VmConfig::get('oncheckout_show_register', 0))
	  define('VM_REGISTRATION_TYPE', 'NORMAL_REGISTRATION'); 
	  else 
	  define('VM_REGISTRATION_TYPE', 'SILENT_REGISTRATION'); 
	}
	else
	{
	if (VmConfig::get('oncheckout_show_register', 0))
    define('VM_REGISTRATION_TYPE', 'OPTIONAL_REGISTRATION'); 
	else 
	define('VM_REGISTRATION_TYPE', 'NO_REGISTRATION'); 
	}
   }
   
   ?>
   
   
					<fieldset class="adminform">
					<legend><?php echo JText::_('COM_ONEPAGE_REGISTRATION_VIRTUEMART'); ?></legend>
					
		<table class="admintable table table-striped" id="comeshere2" style="width: 100%;">
		 <tr>
	    <td class="key"><label>
		<?php echo JText::_('COM_ONEPAGE_JOOMLA_ACTIVATION'); ?></label>
		</td>
		
		<td>
		<a class="opcmodal" href="index.php?option=com_config&amp;view=component&amp;component=com_users&amp;path=&amp;tmpl=component" rel="<?php echo htmlentities("{handler: 'iframe', size: {x: 875, y: 550}, onClose: function() {}}"); ?>">
<?php echo JText::_('COM_ONEPAGE_REGISTRATION_VIRTUEMART_ACTIVATION'); ?> 
</a>
		</td>
		
		</tr>
		
		<tr>
		<td class="key">
		  <label for="opc_registraton_type">
<?php echo JText::_('COM_ONEPAGE_SELECT_REGISTRATION_TYPE'); echo JText::_('COM_ONEPAGE_WILL_ALTER_VIRTUEMART_CONFIGURATION'); ?>
</label>
<?php OPCVideoHelp::show('COM_ONEPAGE_SELECT_REGISTRATION_TYPE'); ?>
		</td>
		<td>
		<select <?php if (!$is_admin) echo ' disabled="disabled" '; ?> name="opc_registraton_type" id="opc_registraton_type">
 <?php 
 echo '<option value="NO_REGISTRATION"';
  if (VM_REGISTRATION_TYPE=='NO_REGISTRATOIN') 
 echo ' selected="selected"'; 
 echo '>'; 
 echo JText::_("COM_ONEPAGE_NO_REGISTRATION").'</option>'; 
 
 echo '<option value="OPTIONAL_REGISTRATION"';
  if (VM_REGISTRATION_TYPE=='OPTIONAL_REGISTRATION') 
 echo ' selected="selected"'; 
echo '>'; 
 echo JText::_("COM_ONEPAGE_OPTIONAL_REGISTRATION").'</option>'; 
 
 echo '<option value="SILENT_REGISTRATION"';
  if (VM_REGISTRATION_TYPE=='SILENT_REGISTRATION') 
 echo ' selected="selected"'; 
echo '>'; 
 echo JText::_("COM_ONEPAGE_SILENT_REGISTRATION").'</option>'; 
 
 
 echo '<option value="NORMAL_REGISTRATION"';
  if (VM_REGISTRATION_TYPE=='NORMAL_REGISTRATION') 
 echo ' selected="selected"'; 
 echo '>'; 
 echo JText::_("COM_ONEPAGE_NORMAL_REGISTRATION").'</option>'; 
 ?>
</select>
		</td>
		</tr>
		
		<?php if (defined('VM_VERSION') && (VM_VERSION >= 3)) { ?>
		<tr>
		 <td class="key">
		 <label for="disable_vm_cart_reload">
		  <?php echo JText::_('COM_ONEPAGE_REGISTRATION_DISBLE_VM_CARTRELOAD'); ?>
		 </label>
		 </td>
		 <td>
		   <input type="checkbox" value="1" <?php if (!empty($disable_vm_cart_reload)) echo ' checked="checked" '; ?> name="disable_vm_cart_reload" id="disable_vm_cart_reload" /><?php echo JText::_('COM_ONEPAGE_REGISTRATION_DISBLE_VM_CARTRELOAD_DESC'); ?>
		 </td>
		</tr>
		<?php } ?>
		
		
		</table>
					
					
		<?php 
		if (false) { 
		echo html_entity_decode(JText::_('COM_ONEPAGE_REGISTRATION_VIRTUEMART_TAX_CONFIG')); ?><br />
					<?php
		}
					
		
					?>

<br />

<p>

<br style="clear: both;"/>

<?php  echo JHtml::_('form.token'); 
/*
?>
<br style="clear: both;"/>
<?php echo JText::_('COM_ONEPAGE_REGISTRATION_VIRTUEMART_REGISTRATION_OPTION'); ?>

<?php
*/
?></p><?php
 $usersConfig = JComponentHelper::getParams( 'com_users' );
 $reg = $usersConfig->get('allowUserRegistration'); 
 if (empty($reg) && (VM_REGISTRATION_TYPE != 'NO_REGISTRATION'))
 {
   echo '<p style="color: red;">'.JText::_('COM_ONEPAGE_REGISTRATION_VIRTUEMART_NO_REGISTRATION').'</p>'; 
 }
 /*
?>
<?php if (VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION') echo '<span style="color: green; font-weight: bold; font-size: 14px;">'; ?>
<?php echo JText::_('COM_ONEPAGE_REGISTRATION_VIRTUEMART_A_IS_B_NOT'); ?><br />
<?php if (VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION') echo '</span>'; ?>
<?php if (VM_REGISTRATION_TYPE == 'SILENT_REGISTRATION') echo '<span style="color: green; font-weight: bold; font-size: 14px;">'; ?>
<?php echo JText::_('COM_ONEPAGE_REGISTRATION_VIRTUEMART_A_NOT_B_IS'); ?><br />
<?php if (VM_REGISTRATION_TYPE == 'SILENT_REGISTRATION') echo '</span>'; ?>
<?php if (VM_REGISTRATION_TYPE == 'NO_REGISTRATION') echo '<span style="color: green; font-weight: bold;font-size: 14px;">'; ?>
<?php echo JText::_('COM_ONEPAGE_REGISTRATION_VIRTUEMART_A_NOT_B_NOT'); ?><br />
<?php if (VM_REGISTRATION_TYPE == 'NO_REGISTRATION') echo '</span>'; ?>
<?php if (VM_REGISTRATION_TYPE == 'NORMAL_REGISTRATION') echo '<span style="color: green; font-weight: bold;font-size: 14px;">'; ?>
<?php echo JText::_('COM_ONEPAGE_REGISTRATION_VIRTUEMART_A_IS_B_IS'); ?><br />
<?php if (VM_REGISTRATION_TYPE == 'NORMAL_REGISTRATION') echo '</span>'; ?>
<br />
<br />
<?php echo JText::_('COM_ONEPAGE_REGISTRATION_VIRTUEMART_NOTE'); 
*/
?>
</fieldset>
<?php
					echo $pane->endPanel();
					?>
   
		<?php
		echo $pane->startPanel(JText::_('COM_ONEPAGE_SHOPPERFIELDS_PANEL'), 'panel82'); ?>
		<fieldset class="adminformF" style="max-width: 100%; float: left; clear: both;">
		 <?php
		   if (empty($this->ulist)) echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_INFO'); 
		   else
		   {
		   ?>
		  <h3><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_BUSINESS_VIEW_HEAD'); ?></h3><p><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_BUSINESS_VIEW_DESC'); ?></p>
		  <h3><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_RENDER'); ?></h3><p><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_RENDER_DESC'); ?></p>
		  <div>
		  <h3><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_PERSONAL'); ?></h3>
		  <p><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_PERSONAL_DESC'); ?></p>
		  <select name="opc_cr_type">
		  <option value="save_all" <?php if (!empty($opc_cr_type) && ($opc_cr_type=="save_all")) echo ' selected="selected" '; ?>><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_PERSONAL_SAVE_ALL'); ?></option>
		  <option value="save_order" <?php if (!empty($opc_cr_type) && ($opc_cr_type=="save_order")) echo ' selected="selected" '; ?>><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_PERSONAL_SAVE_ORDER'); ?></option>
		  <option value="save_none" <?php if (!empty($opc_cr_type) && ($opc_cr_type=="save_none")) echo ' selected="selected" '; ?>><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_PERSONAL_SAVE_NONE'); ?></option>
		  </select>
		  </div>
		  <br style="clear: both;"/>
		  <div style="overflow-x: scroll; max-width: 100%;">
		  <table class="admintable table table-striped" style="width: 100%; ">
		  <tr>
		   <th><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_FIELDNAME'); ?>
		   </th>
		   <th class="hasTip" title="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_BUSINESS_VIEW_DESC')); ?>" tooltip="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_BUSINESS_VIEW_DESC')); ?>" alt="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_BUSINESS_VIEW_DESC')); ?>" ><?php $col1_label = htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_BUSINESS')); echo $col1_label; ?></th>
		   <th class="hasTip" tooltip="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_RENDER_DESC')); ?>" title="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_RENDER_DESC')); ?>" alt="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_RENDER_DESC')); ?>"><?php $col2_label = JText::_('COM_ONEPAGE_SHOPPERFIELDS_CUSTOM'); echo $col2_label; ?></th>
		   <th class="hasTip" alt="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_ORDER_ALT')); 
		    
			if (defined('VM_VERSION') && (VM_VERSION >= 3))
		   {
		     echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_ORDER_NOTE')); 
		   }
		   
		   ?>" tooltip="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_ORDER')); 
		   
		     if (defined('VM_VERSION') && (VM_VERSION >= 3))
		   {
		     echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_ORDER_NOTE')); 
		   }
		   
		   ?>" title="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_ORDER')); ?>" ><?php  $col3_label = JText::_('COM_ONEPAGE_SHOPPERFIELDS_ORDER'); 
		   echo $col3_label; 
		   
		   ?></th>
		   <th class="hasTip" tooltip="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_SHIPPING_ALT')); ?>" title="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_SHIPPING_ALT')); ?>" alt="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_SHIPPING_ALT')); ?>" ><?php $col4_label = JText::_('COM_ONEPAGE_SHOPPERFIELDS_SHIPPING'); echo $col4_label; ?></th>
		   
		    <th class="hasTip" tooltip="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_AJAXFIELD_ALT')); ?>" title="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_AJAXFIELD_ALT')); ?>" alt="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_AJAXFIELD_ALT')); ?>" ><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_AJAXFIELD'); ?></th>
			
			
			<?php 
			
			$col_al = JText::_('COM_ONEPAGE_SHOPPERFIELDS_AJAXFIELD');
			?>
			
			
			<th class="hasTip" tooltip="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_ADMIN_ADDSHOPPER_ALT')); ?>" title="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_ADMIN_ADDSHOPPER_ALT')); ?>" alt="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_ADMIN_ADDSHOPPER_ALT')); ?>" ><?php $col5_label = JText::_('COM_ONEPAGE_SHOPPERFIELDS_ADMIN_ADDSHOPPER'); echo $col5_label; ?></th>
			
			
			<th class="hasTip" tooltip="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_RENDER_HIDDEN')); ?>" title="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_RENDER_HIDDEN')); ?>" alt="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_RENDER_HIDDEN')); ?>" ><?php $col6_label = JText::_('COM_ONEPAGE_SHOPPERFIELDS_RENDER_HIDDEN'); echo $col6_label; ?></th>
			
			
				<th class="hasTip" tooltip="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_RENDER_THIRDADDRESS')); ?>" title="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_RENDER_THIRDADDRESS')); ?>" alt="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_RENDER_THIRDADDRESS')); ?>" ><?php $col7_label = JText::_('COM_ONEPAGE_SHOPPERFIELDS_RENDER_THIRDADDRESS'); echo $col7_label; ?></th>
				
				<th class="hasTip" tooltip="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_HTML5_FIELD_DESC')); ?>" title="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_HTML5_FIELD_DESC')); ?>" alt="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_HTML5_FIELD_DESC')); ?>" ><?php $col_html5_label = JText::_('COM_ONEPAGE_SHOPPERFIELDS_HTML5_FIELDS'); echo $col_html5_label; ?></th>
				
				<?php 
				
				if (!empty($this->acyfields)) {
					?><th class="hasTip" tooltip="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_ACYFIELDS_DESC')); ?>" title="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_ACYFIELDS_DESC')); ?>" alt="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_ACYFIELDS_DESC')); ?>" ><?php $col9_label = JText::_('COM_ONEPAGE_SHOPPERFIELDS_ACYFIELDS'); echo $col9_label; ?></th><?php
				}
				
				
				
				/*
				$cols = OPCconfig::getValues('vm_userfields'); 
				
				foreach ($cols as $row) {
				$data = @json_decode($row['value'], false); 
				if (!empty($data))
				if (!empty($data->path)) {
			    $title = 'COM_ONEPAGE_USERFIELDS_'.strtoupper($data->path); 
				$title = JText::_($title); 
				$title2 = str_replace('_', ' ', $title); 
				*/
				$ftypes = $this->ftypes; 
				
				if (!empty($ftypes)) {
					foreach ($ftypes as $t) {
				
				$title = $t->header; 
				$title2 = $t->header2; 
				?>
				<th class="hasTip" tooltip="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_ADVANCED_IN').' '.$title2); ?>" title="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_ADVANCED_IN').' '.$title); ?>" alt="<?php echo htmlentities(JText::_('COM_ONEPAGE_SHOPPERFIELDS_ADVANCED_IN').' '.$title); ?>" ><?php $col8_label = JText::_('COM_ONEPAGE_SHOPPERFIELDS_ADVANCED_IN').' '.$title2; echo $col8_label; ?></th>
				
				<?php } } ?>
			
		  </tr>
		  

		   <?php
		   
		   foreach ($this->ulist as $key=>$row)
		    {
			 if (!$row->published) continue; 
			 
			 // the next line will filter core fields
			 //if (in_array($row->name, $this->clist)) continue;
			  //if ($row->published)

			   ?>
			   		<tr>
	    <td class="key">
	     <label><?php 
		 $title = $row->title; 
		 $title2 = JText::_($row->title); 
		 
		 if ($title2 != $title) echo htmlentities(strip_tags($title2.' ('.JText::_($row->name).')')); 
		 else echo $row->name; 
		 if ($row->name == 'register_account')
		  {
		    echo '<br /><small>'.JText::_('COM_ONEPAGE_SHOPPERFIELDS_REGISTER_NOTE').'</small>';
		  }
		  if ($row->name == 'password2')
		  {
		    echo '<br /><small>'.JText::_('COM_ONEPAGE_SHOPPERFIELDS_PASSW2_NOTE').'</small><br /><input type="checkbox" name="password_clear_text" value="1" ';
			if (!empty($password_clear_text)) echo ' checked="checked" ';
			echo ' /> '.JText::_('COM_ONEPAGE_SHOPPERFIELDS_PASS_CLEAR'); 
		  }

		 ?></label>
	    </td>
	    <td><input type="checkbox" name="business_fields[]" value="<?php echo $row->name; ?>" <?php 
		switch ($row->name)
		{
		 
		  case 'password':
		  case 'password2':
		    echo ' disabled="disabled" '; 
			
			break; 
		  default: 
			 if (!empty($business_selector) && ($row->name === $business_selector)) echo ' disabled="disabled" '; 
			 else
		     if (!empty($business_fields))
		     if (in_array($row->name, $business_fields)) echo ' checked="checked" '; 
			break; 
		}
		
		$title = $row->title; 
		$title2 = JText::_($row->title); 
		 
		 if ($title2 != $title) $title = $title2.' ('.JText::_($row->name).')'; 
		 else $title = $row->name; 
		 
		 
		 $title = $col1_label.': '.$title; 
		 $title = htmlentities(strip_tags($title)); 
		?> class="hasTip" tooltip="<?php echo $title; ?>" alt="<?php echo $title; ?>" title="<?php echo $title; ?>" /> 
		</td>

	    <td><input type="checkbox" name="custom_rendering_fields[]" value="<?php echo $row->name; ?>" <?php
		// case 'password2':
		switch ($row->name)
		{
		  case 'email':
		  case 'email2':
		  case 'username':
		  case 'agreed':
		  case 'name': 
		  case 'password':
		  case 'register_account':
		    echo ' disabled="disabled" checked="checked" readonly="readonly"'; 
		
		
		}
		$title = $row->title; 
		$title2 = JText::_($row->title); 
		 
		 if ($title2 != $title) $title = $title2.' ('.JText::_($row->name).')'; 
		 else $title = $row->name; 
		 
		 
		 $title = $col2_label.': '.$title; 
		 $title = htmlentities(strip_tags($title)); 
		 
		if (!empty($custom_rendering_fields))
		if (in_array($row->name, $custom_rendering_fields)) echo ' checked="checked"'; 
	?>  class="hasTip" tooltip="<?php echo $title; ?>" alt="<?php echo $title; ?>" title="<?php echo $title; ?>" />
		
		
		
		</td>
		
		
		  <td><input type="checkbox" name="per_order_rendering[]" value="<?php echo $row->name; ?>" <?php
		// case 'password2':
		switch ($row->name)
		{
		  case 'email':
		  case 'email2':
		  case 'username':
		  case 'agreed':
		  case 'name': 
		  case 'password':
		  case 'register_account':
		    echo ' disabled="disabled"  readonly="readonly"'; 
		
		
		}
		
		$cart_fields = array(); 
		if (defined('VM_VERSION') && (VM_VERSION >= 3))
		{
		
	
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 
	    require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php');   
		
		$userFieldsModel = OPCmini::getModel ('userfields');
	    $userFields = $userFieldsModel->getUserFields(
				'cart',
				array(),
				array()
			);
		
		
		if (!empty($userFields))
		foreach ($userFields as $k=>$v)
		 {
		    $cart_fields[] = $v->name; 
		 }
		
		
		}
		
		
		
		if (in_array($row->name, $cart_fields))
		{
		echo ' checked="checked" '; 
		
		
		}
		else
		if (!empty($per_order_rendering))
		if (in_array($row->name, $per_order_rendering)) echo ' checked="checked"'; 
		
		
		$title = $row->title; 
		$title2 = JText::_($row->title); 
		 
		 if ($title2 != $title) $title = $title2.' ('.JText::_($row->name).')'; 
		 else $title = $row->name; 
		 
		 
		 $title = $col3_label.': '.$title; 
		 
		$title= htmlentities(strip_tags($title)); 
		
		?> class="hasTip" tooltip="<?php echo $title; ?>" alt="<?php echo $title; ?>" title="<?php echo $title; ?>" />  
		</td>
		
		
		
		
		
		 <td>&nbsp;
		</td>

		
		 <td><input type="checkbox" name="opc_ajax_fields[]" value="<?php echo $row->name; ?>" <?php
		// case 'password2':
		
		
		if (!empty($opc_ajax_fields))
		if (in_array($row->name, $opc_ajax_fields)) echo ' checked="checked" '; 
		
		if (!isset($opc_ajax_fields))
		 {
		    switch ($row->name)
		{
		  case 'address_1':
		  case 'address_2':
		  case 'zip':
		  case 'virtuemart_country_id':
		  case 'virtuemart_state_id': 
		    echo ' checked="checked" '; 
			
			break; 
		
		}
		 }
		$title = $row->title; 
		$title2 = JText::_($row->title); 
		 
		 if ($title2 != $title) $title = $title2.' ('.JText::_($row->name).')'; 
		 else $title = $row->name; 
		 
		 
		 $title = $col_al.': '.$title; 
		 
		  $title= htmlentities(strip_tags($title)); 
		
		
		?> class="hasTip" tooltip="<?php echo $title; ?>"  alt="<?php echo $title; ?>" title="<?php echo $title; ?>" />  
		</td>
		
		
		 <td><input type="checkbox" name="admin_shopper_fields[]" value="<?php echo $row->name; ?>" <?php
		
			
		if (!empty($admin_shopper_fields))
		if (in_array($row->name, $admin_shopper_fields)) echo ' checked="checked" '; 
		
		$title = $row->title; 
		$title2 = JText::_($row->title); 
		 
		 if ($title2 != $title) $title = $title2.' ('.JText::_($row->name).')'; 
		 else $title = $row->name; 
		 
		 
		 $title = $col5_label.': '.$title; 
		 $title= htmlentities(strip_tags($title)); 
		 
		
	?>  class="hasTip" tooltip="<?php echo $title; ?>" alt="<?php echo $title; ?>" title="<?php echo $title; ?>" />
		</td>
		
		
		<td><input type="checkbox" name="render_as_hidden[]" value="<?php echo $row->name; ?>" <?php
		
			
		if (!empty($render_as_hidden))
		if (in_array($row->name, $render_as_hidden)) echo ' checked="checked" '; 
		
		$title = $row->title; 
		$title2 = JText::_($row->title); 
		 
		 if ($title2 != $title) $title = $title2.' ('.JText::_($row->name).')'; 
		 else $title = $row->name; 
		 
		 
		 $title = $col6_label.': '.$title; 
		 $title= htmlentities(strip_tags($title)); 
		
		?>  alt="<?php echo $title; ?>" title="<?php echo $title; ?>" class="hasTip" />
		
		</td>
		
		
		<td><input type="checkbox" name="render_in_third_address[]" value="<?php echo $row->name; ?>" <?php
		
			
		if (!empty($render_in_third_address))
		if (in_array($row->name, $render_in_third_address)) echo ' checked="checked" '; 
		
		$title = $row->title; 
		$title2 = JText::_($row->title); 
		 
		 if ($title2 != $title) $title = $title2.' ('.JText::_($row->name).')'; 
		 else $title = $row->name; 
		 
		 
		 $title = $col7_label.': '.$title; 
		 $title= htmlentities(strip_tags($title)); 
		
		?>  class="hasTip" tooltip="<?php echo $title; ?>" alt="<?php echo $title; ?>" title="<?php echo $title; ?>" />
		</td>
		
		
		<td><input type="text" name="html5_fields[<?php echo $row->name; ?>]" value="<?php 
		
			
		if (!empty($html5_fields))
		if (!empty($html5_fields[$row->name])) echo htmlentities(urldecode($html5_fields[$row->name])); ?>" <?php
		
		$title = $row->title; 
		$title2 = JText::_($row->title); 
		 
		 if ($title2 != $title) $title = $title2.' ('.JText::_($row->name).')'; 
		 else $title = $row->name; 
		 
		 $extr = JText::_('COM_ONEPAGE_SHOPPERFIELDS_HTML5_FIELDS_TYPE'); 
		 $title = $col_html5_label.': '.$title.' '.$extr; 
		 $title= htmlentities(strip_tags($title)); 
		
		?> class="hasTip" placeholder="<?php echo htmlentities($extr); ?>" tooltip="<?php echo $title; ?>" alt="<?php echo $title; ?>" title="<?php echo $title; ?>" />
		
		<input type="text" name="html5_fields_extra[<?php echo $row->name; ?>]" value="<?php 
		
			
		if (!empty($html5_fields_extra))
		if (!empty($html5_fields_extra[$row->name])) echo htmlentities(urldecode($html5_fields_extra[$row->name])); ?>" <?php
		
		$title = $row->title; 
		$title2 = JText::_($row->title); 
		 
		 if ($title2 != $title) $title = $title2.' ('.JText::_($row->name).')'; 
		 else $title = $row->name; 
		 
		 $extr = JText::_('COM_ONEPAGE_SHOPPERFIELDS_HTML5_FIELDS_EXTRA'); 
		 $title = $col_html5_label.': '.$title.' '.$extr; 
		 $title= htmlentities(strip_tags($title)); 
		
		?>  class="hasTip" placeholder="<?php echo htmlentities($extr); ?>" tooltip="<?php echo $title; ?>" alt="<?php echo $title; ?>" title="<?php echo $title; ?>" />
		
		
		<input type="text" name="html5_placeholder[<?php echo $row->name; ?>]" value="<?php 
		
			
		if (!empty($html5_placeholder))
		if (!empty($html5_placeholder[$row->name])) echo htmlentities(urldecode($html5_placeholder[$row->name])); ?>" <?php
		
		$title = $row->title; 
		$title2 = JText::_($row->title); 
		 
		 if ($title2 != $title) $title = $title2.' ('.JText::_($row->name).')'; 
		 else $title = $row->name; 
		 
		 $extr = JText::_('COM_ONEPAGE_SHOPPERFIELDS_HTML5_PLACEHOLDER'); 
		 $title = $col_html5_label.': '.$title.' '.$extr; 
		 $title= htmlentities(strip_tags($title)); 
		
		?>  class="hasTip" placeholder="<?php echo htmlentities($extr); ?>" tooltip="<?php echo $title; ?>" alt="<?php echo $title; ?>" title="<?php echo $title; ?>" />
		
		
		
		<input type="text" name="html5_validation_error[<?php echo $row->name; ?>]" value="<?php 
		
			
		if (!empty($html5_validation_error))
		if (!empty($html5_validation_error[$row->name])) echo htmlentities(urldecode($html5_validation_error[$row->name])); ?>" <?php
		
		$title = $row->title; 
		$title2 = JText::_($row->title); 
		 
		 if ($title2 != $title) $title = $title2.' ('.JText::_($row->name).')'; 
		 else $title = $row->name; 
		 
		 $extr = JText::_('COM_ONEPAGE_SHOPPERFIELDS_HTML5_VALIDATION_ERROR_MSG'); 
		 $title = $col_html5_label.': '.$title.' '.$extr; 
		 $title= htmlentities(strip_tags($title)); 
		
		?>  class="hasTip" placeholder="<?php echo htmlentities($extr); ?>" tooltip="<?php echo $title; ?>" alt="<?php echo $title; ?>" title="<?php echo $title; ?>" />
		
		
		
		</td>
		
		
			<?php
		
		if (!empty($this->acyfields)) {
			?><td>
			
			<?php
			$title = $row->title; 
		$title2 = JText::_($row->title); 
		 
		 if ($title2 != $title) $title = $title2.' ('.JText::_($row->name).')'; 
		 else $title = $row->name; 
		 
		 
		 $title = $col9_label.': '.$title; 
		 $title= htmlentities(strip_tags($title)); 
			
			?>
			<select style="max-width:100px;" class="hasTip" tooltip="<?php echo $title; ?>" alt="<?php echo $title; ?>" title="<?php echo $title; ?>" name="acymailing_fields[<?php echo $row->name; ?>]">
			  <option value=""><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
			  <?php foreach ($this->acyfields as $k=>$f) {
					?><option <?php 
					$default = ''; 
					$con = OPCconfig::getValue('acymailing_fields', $row->name, 0, $default); 
					if ($con === $k) {
						echo ' selected="selected" '; 
					}
					
					?> value="<?php echo $k; ?>"><?php echo $k; ?></option>
					<?php
			  }
			  ?>
			</select>
			
		
			
		
		
		
		
		
		</td>
			
			<?php
		}
		?>
		
		
		
		<?php if (!empty($ftypes)) {
					foreach ($ftypes as $t) {
							?>
							<td>
							
							<input type="checkbox" name="field_<?php echo $t->config_subname; ?>[]" value="<?php echo $row->name; ?>" <?php
		$ro = false; 
		
		//if (!empty($shipping_obligatory_fields))
		//if (in_array($row->name, $shipping_obligatory_fields)) echo '" checked="checked'; 
		
		$title = htmlentities(strip_tags($t->header2.' ('.$row->name.')')); 		
		
		
		?> class="hasTip" tooltip="<?php echo $title; ?>" alt="<?php echo $title; ?>" title="<?php echo $title; ?>"  <?php 
		if (isset($t->fields)) {
			if (isset($t->fields->{$row->name})) echo ' checked="checked" '; 
		}
		
		?> />
							
		<script>
		 console.log('<?php echo $t->header; ?>'); 
		 
		 
		</script>		
							</td>
							<?php 
					}
		}
		?>
		
		</tr>
		<tr><td><div style="clear: both; text-indent: 20px;"><?php echo JText::_('COM_VIRTUEMART_FIELDMANAGER_REQUIRED'); ?></div>
		<td>
		<input type="checkbox" name="business_obligatory_fields[]" value="<?php echo $row->name; ?>" <?php
		$ro = false; 
		switch ($row->name)
		{
		  case 'email':
		  case 'email2':
		  case 'username':
		  case 'agreed':
		  case 'name': 
		  case 'password':
		  case 'password2':
		  case 'register_account':
		    echo ' disabled="disabled" readonly="readonly" '; 
			$ro = true; 
			break; 
		
		}
		if ($ro)
		    echo ' disabled="disabled" readonly="readonly"'; 
			
		
		if (!empty($business_fields)) if (!in_array($row->name, $business_fields)) echo ' disabled="disabled" ';
		if (!empty($business_obligatory_fields))
		if (in_array($row->name, $business_obligatory_fields)) echo ' checked="checked" '; 
		
		
		
		
		 $title = $row->title; 
		 $title2 = JText::_($row->title); 
		 
		 if ($title2 != $title) $title = $title2.' ('.JText::_($row->name).')'; 
		 else $title = $row->name; 
		 
		  $title= htmlentities(strip_tags($title)); 
		?> class="hasTip" tooltip="<?php echo $title; ?>" alt="<?php echo $title; ?>" title="<?php echo $title; ?>" />
		</td>
		<td colspan="2">&nbsp;</td>
		<td><input type="checkbox" name="shipping_obligatory_fields[]" value="<?php echo $row->name; ?>" <?php
		$ro = false; 
		switch ($row->name)
		{
		  case 'email':
		  case 'email2':
		  case 'username':
		  case 'agreed':
		  case 'name': 
		  case 'password':
		  case 'password2':
		  case 'register_account':
		    echo ' disabled="disabled" checked="checked" readonly="readonly" '; 
			$ro = true; 
			break; 
		
		}
		if (!$ro)
		if ($row->shipment != '1')
		    echo ' disabled="disabled" readonly="readonly" '; 
			
		if (!empty($shipping_obligatory_fields))
		if (in_array($row->name, $shipping_obligatory_fields)) echo ' checked="checked" '; 
		$title = htmlentities(strip_tags($col4_label.' ('.$row->name.')')); 
		
		?> class="hasTip" tooltip="<?php echo $title; ?>" alt="<?php echo $title; ?>" title="<?php echo $title; ?>"  /></td>
		
		<td colspan="5">&nbsp;</td>
		
		<?php if (!empty($this->acyfields)) { 
		?>
		<td colspan="1">&nbsp;</td>
		<?php
		} ?>
		
		<?php
		if (!empty($ftypes)) {
			    ?>
							<td colspan="<?php echo count($ftypes); ?>">&nbsp;</td>
					<?php } ?>
					
		</tr>
		
		
		
		<?php
			}
			?></table></div><?php		
		   }
		 ?>
		</fieldset>
		
		<?php 
		jimport( 'joomla.plugin.helper' );
		$tg = JPluginHelper::getPlugin('system', 'vmfield_toggler'); 
		if (!empty($tg)) {
		
		?>
			<fieldset class="adminform">
		 <legend><?php echo JText::_('PLG_SYSTEM_VMFIELD_TOGGLER'); ?></legend>
		  <table class="table table-striped admintable table table-striped">
		    
		<tr>
	    <td><label for="clear_data"><?php echo JText::_('PLG_SYSTEM_VMFIELD_TOGGLER_CLEAR_DATA'); ?></label></td>
		
		
		<td>
		 <input type="button" value="<?php echo JText::_('PLG_SYSTEM_VMFIELD_TOGGLER_CLEAR_DATA'); ?>" id="clear_data" name="clear_data" class="btn btn-small btn-success"  onclick="submitbutton('clearfieldpaths');" />
		</td>
		<td><label for="clear_data">
		<?php echo JText::_('PLG_SYSTEM_VMFIELD_TOGGLER_CLEAR_DATA_DESC'); ?>
		</label>
		</td>
		
		</tr>
		</table>
		</fieldset>
		<?php 
		}
		?>
		
		
		<fieldset class="adminform">
		 <legend><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_BUSINESS_FIELDS'); ?></legend>
		  <table class="table table-striped admintable table table-striped">
		    
		<tr>
	    <td><label for="do_not_display_business"><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_BUSINESS_LOGGED_IN'); ?></label></td>
		
		
		<td>
		<input type="checkbox" value="1" id="do_not_display_business" name="do_not_display_business" <?php if (!empty($do_not_display_business)) echo ' checked="checked" '; ?> />
		</td>
		<td><label for="do_not_display_business">
		<?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_BUSINESS_LOGGED_IN_DESC'); ?>
		</label>
		</td>
		
		</tr>
		</table>
		</fieldset>
		<fieldset class="adminform">
		 <legend><?php echo JText::_('COM_ONEPAGE_THIRD_ADDRESS'); ?></legend>
		  <table class="table table-striped admintable table table-striped">
		    
		<tr>
	    <td><label for="opc_switch_rd">
		<?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_THIRDADDRESS_SWITCH'); ?>
		</label>
		</td>
		<td>
		<input type="checkbox" value="1" id="opc_switch_rd" name="opc_switch_rd" <?php if (!empty($opc_switch_rd)) echo ' checked="checked" '; ?> />
	    </td>
		<td><label for="opc_switch_rd">
		<?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_THIRDADDRESS_SWITCH_DESC'); ?></label>
		</td>
		</tr>
		
		<tr>
	    <td><label for="opc_btrd_def">
		<?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_THIRDADDRESS_DEFAULT'); ?>
		</label>
		</td>
		<td>
		<input type="checkbox" value="1" id="opc_btrd_def" name="opc_btrd_def" <?php if (!empty($opc_btrd_def)) echo ' checked="checked" '; ?> />
	    </td>
		<td><label for="opc_btrd_def">
		<?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_THIRDADDRESS_DEFAULT_BT'); ?></label>
		</td>
		</tr>
		
		
		</table>
		</fieldset>
		
		
			<fieldset class="adminform">
		 <legend><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_ST'); ?></legend>
		  <table class="table table-striped admintable table table-striped">
		    
		<tr>
	    <td><label for="opc_copy_bt_st">
		<?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_ST'); ?>
		</label>
		</td>
		<td>
		<input type="checkbox" value="1" id="opc_copy_bt_st" name="opc_copy_bt_st" <?php if (!empty($opc_copy_bt_st)) echo ' checked="checked" '; ?> />
	    </td>
		<td><label for="opc_copy_bt_st">
		<?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_ST_DESC'); ?></label>
		</td>
		</tr>
		</table>
		</fieldset>

		
		<fieldset class="adminform">
		 <legend><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_DEPENDENCE'); ?></legend>
		  <table class="table table-striped admintable table table-striped">
		    
		<tr>
	    <td>
		<?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_DEPENDENCE_WHEN'); ?>
		</td>
		<td>

		<select name="business_selector" id="bussiness_selector">
		<option value=""><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
		<?php 
		  unset($row); reset($this->ulist); 
		  foreach ($this->ulist as $key=>$row)
		  {
			   $arr = array('select', 'checkbox', 'radio', 'select-one'); 
			  if (!in_array($row->type, $arr)) continue; 
			  $later = array('virtuemart_country_id', 'virtuemart_state_id','opc_vat_field', 'pluginistraxx_euvatchecker' ); 
			  if (in_array($row->name, $later)) continue; 
			  
			   $title = JText::_($row->title).' ('.$row->name.')'; 
			   $title= htmlentities(strip_tags($title)); 
			  ?><option <?php 
			  if (!empty($business_selector)) if ($business_selector === $row->name) echo ' selected="selected" '; 
			  ?>value="<?php echo $row->name; ?>"><?php echo $title; ?></option>
			  
			  <?php
		  }
		?>
		
		</select>
		</td>
		</tr>
		<tr>
		<td>
		<?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_DEPENDENCE_HIDE'); ?>
		</td>
		<td>
		<select name="business_fields2[]" id="business_fields2" multiple="multiple" class="vm-chzn-select" >
		<option value=""><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
		<?php 
		
		  unset($row); 
		  reset($this->ulist); 
		  foreach ($this->ulist as $key=>$row)
		  {
			 
			  
			  $title = JText::_($row->title).' ('.$row->name.')'; 
			  ?><option <?php 
			  if (!empty($business_fields2)) if (in_array($row->name, $business_fields2)) echo ' selected="selected" ';
			  else			  
				  if (!empty($is_business2))
				  {
					 // if (!empty($business_fields)) if (in_array($row->name, $business_fields)) echo ' selected="selected" ';
				  }
			  ?>value="<?php echo $row->name; ?>"><?php echo htmlentities(strip_tags($title)); ?></option>
			  
			  <?php
		  }
		?>
		
		</select>
		</td>
		
		
		
		
		</tr>
		<tr>
		<td>
		<input type="checkbox" value="1" name="is_business2" <?php if (!empty($is_business2)) echo ' checked="checked" '; ?> />
		</td>
		<td><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_DEPENDENCE_SG'); ?></td>
		
		</tr>
		<tr>
		<td>
		
		<?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_DEPENDENCE_VALUE'); ?>
		</td>
		<td>
		 <select name="business2_value">
		 <?php if (empty($business_selector)) { ?>
		 
		  <option value=""><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED_CLICK_SAVE'); ?></option>
		  
		 <?php }
else {
	$res = array(); 
	$db = JFactory::getDBO(); 
	//$q = 'select `v`.`fieldtitle` as field_title, `v`.`fieldvalue` as field_value from `#__virtuemart_userfield_values` as v, `#__virtuemart_userfields` as f where `f`.`virtuemart_userfield_id` = `v`.`virtuemart_userfield_id` and `f`.`name` = \''.$db->escape($business_selector).'\''; 
	
	$q = 'select  `v`.`fieldtitle` as fieldtitle, `v`.`fieldvalue`, f.name as name, f.type, f.value from `#__virtuemart_userfield_values` as v, `#__virtuemart_userfields` as f where `f`.`virtuemart_userfield_id` = `v`.`virtuemart_userfield_id` and `f`.`name` = \''.$db->escape($business_selector).'\''; 
	try
	{
	$db->setQuery($q); 
	$res = $db->loadAssocList(); 
	
	
	
	}
	catch(Exception $e)
	{
	
		$res = array(); 
		
	}
	
	
	foreach ($res as $k=>$row)
	{
		
		if ($row['type'] == 'checkbox') {
	  ?><option value="1"><?php echo JText::_('COM_ONEPAGE_DEFAULT_STATUS_CHECKED'); ?></option>
	  <option value=""><?php echo JText::_('COM_ONEPAGE_DEFAULT_STATUS_NOTCHECKED'); ?></option>
	  <?php
	  break; 
	  }
	
		
		if (empty($row['fieldvalue']) && (empty($row['fieldtitle']))) continue; 
		?><option <?php 
		if (!empty($business2_value))
		{
			if ($business2_value === $row['fieldvalue'])
			{
				echo ' selected="selected" '; 
			}
		}
		$name = $row['name']; 
		
		?> value="<?php echo htmlentities($row['fieldvalue']); ?>"><?php echo htmlentities(strip_tags(JText::_($row['fieldtitle'])).' - '.$name); ?> (<?php echo $row['fieldvalue']?>)</option>
		<?php
	}
	?>
	
<?php } ?>
		 
		 </select>
		</td>
		
		
		</tr>
		<tr>
		<td colspan="2">
		
		<?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_DEPENDENCE_DESC'); ?>
		</td>
		</tr>
		
	   
	   
		 </table>
		</fieldset>

		<fieldset class="adminform">
		 <legend><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_ONE_OR_THE_OTHER'); ?></legend>
		 <p><?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_ONE_OR_THE_OTHER_DESC'); ?></p>
		  <table class="table table-striped admintable table table-striped" id="opc_ooro">
		 <?php 
		 if (empty($one_or_the_other)) {
		   $one_or_the_other = array(); 
		   $one_or_the_other[] = ''; 
		   $one_or_the_other2 = array(); 
		   $one_or_the_other2[] = ''; 
		 }
		 
		 $kx = 0; 
		 foreach ($one_or_the_other as $k=>$val) { 
		 ob_start(); 
		 ?>  
		<tr id="row_num_{num}">
	    <td>
		<?php echo JText::_('COM_ONEPAGE_SHOPPERFIELDS_ONE_OR_THE_OTHER_PAIR'); ?>
		</td>
		<td>
		<select name="one_or_the_other[{num}]" id="one_or_the_other_<?php echo $val; ?>" class="vm-chzn-select" >
		<option value=""><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
		<?php reset($this->ulist);  
		$ign = array('name', 'tos', 'username', 'password', 'password2', 'email', 'email2', 'agreed', 'register_account'); 
		foreach ($this->ulist as $key=>$row)
		  {
			  $f1 = $row->name; 
			  
			  if (in_array($f1, $ign)) continue; 
			   if ($row->type == 'delimiter') continue;
			  if (!$row->published) continue; 
			   $title = JText::_($row->title); 
			   ?><option <?php 
			  if (!empty($val)) if ($val === $row->name)  echo ' selected="selected" ';
			 
			  ?>value="<?php echo $row->name; ?>"><?php echo htmlentities(strip_tags($title)); ?></option>
			  
			   <?php
			   
		  }
		
		?>
		
		</select> 
		</td>
		<td>
		<select name="one_or_the_other2[{num}]" id="one_or_the_other2_<?php echo $val; ?>" class="vm-chzn-select" >
		<option value=""><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
		<?php reset($this->ulist);  
		$val = $one_or_the_other2[$k]; 
		foreach ($this->ulist as $key=>$row)
		  {
			  $f1 = $row->name; 
			  if ($row->type == 'delimiter') continue; 
			  if (in_array($f1, $ign)) continue; 
			  
			  if (!$row->published) continue; 
			   $title = JText::_($row->title); 
			   ?><option <?php 
			  if (!empty($val)) if ($val === $row->name)  echo ' selected="selected" ';
			 
			  ?> value="<?php echo $row->name; ?>"><?php echo htmlentities(strip_tags($title)); ?></option>
			  
			   <?php
			   
		  }
		
		?>
		
		</select> 
		</td>
		
		  <td>
		   <a href="#" onclick="javascript: return op_new_line3(lineConf);" ><?php echo JText::_('COM_ONEPAGE_ADD_MORE'); ?></a>
		   <a style="margin-left: 50px;" href="#" onclick="javascript: return op_remove_line3(this);" ><?php echo JText::_('COM_ONEPAGE_REMOVE'); ?></a>
		   </td>

		
		</tr>
		 <?php 
		 $row = $rowjs = ob_get_clean(); 
		 
		 
		 
		 $row = str_replace('{num}', $kx, $row); 
		 echo $row; 
		 
		 
		 $kx++; 
		 } 
		 //$num2 = $kx++; 
		 $rowjs = str_replace("'", "\'", $rowjs); 
		 $rowjs = str_replace("\r\r\n", " ", $rowjs); 
		 $rowjs = str_replace("\r\n", " ", $rowjs); 
		 $rowjs = str_replace("\n", " ", $rowjs); 
		  $document->addScriptDeclaration(' 
//<![CDATA[		
var lineConf = {};   
lineConf.iter = '.$num.'; 
lineConf.line = \''.$rowjs.'\';
lineConf.num = \'num\'; 
lineConf.id = \'opc_ooro\'; 
//]]>
'); 
		 
		 
		 ?>
		</table>
		</fieldset>

		
		<fieldset class="adminform">
		 <legend><?php echo JText::_('COM_ONEPAGE_CHECKBOX_PRODUCTS2').' / '.JText::_('COM_ONEPAGE_CHECKBOX_PRODUCTS'); ?></legend>
		 <p><?php echo JText::_('COM_ONEPAGE_CHECKBOX_PRODUCTS_DESC2'); ?></p>
		 <p><?php echo JText::_('COM_ONEPAGE_CHECKBOX_PRODUCTS_DESC'); ?></p>
		  <table class="table table-striped admintable table table-striped">
		    
		<tr>
	    <td>
		<?php echo JText::_('COM_ONEPAGE_CHECKBOX_PRODUCTS_LIST'); ?><br /><?php echo JText::_('COM_ONEPAGE_CHECKBOX_PRODUCTS_LIST_EXAMPLE'); ?>
		</td>
		<td>
		<input type="text" name="checkbox_products" value="<?php if (!empty($checkbox_products)) echo implode(',', $checkbox_products); ?>" placeholder="<?php echo JText::_('COM_ONEPAGE_CHECKBOX_PRODUCTS_LIST'); ?>" />
		</td>
		</tr>
		
		<tr>
	    <td>
		<?php echo JText::_('COM_ONEPAGE_CHECKBOX_PRODUCTS_DISPLAY'); ?>
		</td>
		<td>
		<select name="checkbox_products_display">
		 <option <?php if ((!empty($checkbox_products_display)) && ($checkbox_products_display === true)) echo ' selected="selected" '; ?> value="1"><?php echo JText::_('COM_ONEPAGE_CHECKBOX_PRODUCTS_DISPLAY_TOTALLINE'); ?></option>
		 <option <?php if (empty($checkbox_products_display)) echo ' selected="selected" '; ?> value=""><?php echo JText::_('COM_ONEPAGE_CHECKBOX_PRODUCTS_DISPLAY_CART'); ?></option>
		 <option <?php if ((!empty($checkbox_products_display)) && $checkbox_products_display === 2) echo ' selected="selected" '; ?> value="2"><?php echo JText::_('COM_ONEPAGE_CHECKBOX_PRODUCTS_DISPLAY_CARTQ'); ?></option>
		</select>
		</td>
		</tr>
		
		
		<tr>
	    <td>
		<?php echo JText::_('COM_ONEPAGE_CHECKBOX_PRODUCTS_DISPLAY_THEMEPOSITION'); ?>
		</td>
		<td>
		<select name="checkbox_products_position">
		  <?php foreach ($this->positions as $name=>$v)
		  {
			  ?><option <?php if (!empty($checkbox_products_position) && ($checkbox_products_position==$name)) echo ' selected="selected" '; 
			  else
			   if (!isset($checkbox_products_position) && ($name=='checkoutAdvertise')) echo ' selected="selected" '; 
			  
			  ?> value="<?php echo $name; ?>"><?php echo $name; ?></option>
			  <?php
		  }
		  ?>
		</select>
		</td>
		</tr>
		
		
		<tr>
	    <td>
		<?php echo JText::_('COM_ONEPAGE_CHECKBOX_PRODUCTS_DISPLAY_DISPLAYTYPE'); ?>
		</td>
		<td>
		<select name="checkbox_products_displaytype">
		  
		 <option <?php if (empty($checkbox_products_displaytype)) echo ' selected="selected" '; ?> value=""><?php echo htmlentities(JText::_('COM_ONEPAGE_CHECKBOX_PRODUCTS_DISPLAY_DISPLAYTYPE1')); ?></option>
		 <option <?php if (!empty($checkbox_products_displaytype)) echo ' selected="selected" '; ?> value="1"><?php echo htmlentities(JText::_('COM_ONEPAGE_CHECKBOX_PRODUCTS_DISPLAY_DISPLAYTYPE2')); ?></option>
			
		  
		</select>
		</td>
		</tr>
		
<?php
 if (!isset($checkbox_products_first)) $checkbox_products_first = 'COM_VIRTUEMART_LIST_EMPTY_OPTION'; 
?>		
		
		<tr>
	    <td>
		<?php echo JText::_('COM_ONEPAGE_CHECKBOX_PRODUCTS_DISPLAY_FIRSTVALUE'); ?>
		</td>
		<td>
		  <input type="text" value="<?php if (!empty($checkbox_products_first)) echo htmlentities($checkbox_products_first); ?>" name="checkbox_products_first" id ="checkbox_products_first" /> 
		</td>
		</tr>
		
		
		<tr>
	    <td>
		<?php echo JText::_('COM_ONEPAGE_CHECKBOX_PRODUCTS_DISPLAY_MINORDER'); ?>
		</td>
		<td>
		  <input type="text" value="<?php if (!empty($checkbox_order_start)) echo htmlentities($checkbox_order_start); ?>" name="checkbox_order_start" id ="checkbox_order_start" /> 
		</td>
		</tr>
		
		
		</table>
		</fieldset>
		
		
		
		<?php echo $pane->endPanel();
		echo $pane->startPanel(JText::_('COM_ONEPAGE_SHOPPERGROUP_PANEL'), 'panelb8');
		?>
		<fieldset class="adminform">
		 <legend><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP'); ?></legend>
		  <table class="table table-striped">
		    <tr>
	    <td class="key" >
	     <label for="allow_sg_update" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_ALLOW_SG_UPDATE_LABEL'); ?></label>
	    </td>
	    <td>
	    <input type="checkbox" id="allow_sg_update" name="allow_sg_update" value="allow_sg_update" <?php if (!empty($allow_sg_update)) echo 'checked="checked"'; ?> /> 
	    </td>
	    <td>
	    <?php echo JText::_('COM_ONEPAGE_REGISTRATION_ALLOW_SG_UPDATE_DESC'); ?>
	    </td>
	</tr>
	
	 <tr>
	    <td class="key" >
	     <label for="allow_sg_update_logged" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_ALLOW_SG_UPDATELOGGED_LABEL'); ?></label>
	    </td>
	    <td>
	    <input type="checkbox" id="allow_sg_update_logged" name="allow_sg_update_logged" value="allow_sg_update_logged" <?php if (!empty($allow_sg_update_logged)) echo 'checked="checked"'; ?> /> 
	    </td>
	    <td>
	    <?php echo JText::_('COM_ONEPAGE_REGISTRATION_ALLOW_SG_UPDATELOGGED_DESC'); ?>
	    </td>
	</tr>
	
	
		  </table>
		 </fieldset>
		<fieldset class="adminform">
		 <legend><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP'); ?></legend>
		 <p><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_DESC'); ?></p>
		
		
		<fieldset class="adminform">
		<legend><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_NOTALTER'); ?></legend>
		<label style="float: left;clear: none; margin: 0; padding:0;" for="option_sgroup3">
		
		<input type="radio" id="option_sgroup3" name="option_sgroup" value="0" <?php if (empty($option_sgroup)) echo ' checked="checked" '; ?> />
		
		<?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_NOTALTER_DESC'); ?></label>
		
        </fieldset>
		
		
		<fieldset class="adminform"><legend><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_GLOBALLY'); ?></legend>	
		
		<label style="float: left;clear: none; margin: 0; padding:0;" for="option_sgroup1">
		<input type="radio" id="option_sgroup1" name="option_sgroup" value="1" <?php if (!empty($option_sgroup) && ($option_sgroup===1)) echo ' checked="checked" '; ?> />
		<?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_GLOBALLY_DESC'); ?></label>
		<br style="clear: both;" />
		<table class="table table-striped">
		 <tr>
		   <th><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_LANGGROUP'); ?></th>
		   <th><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_SHOPGROUP'); ?></th>
		 </tr>
		 
		
	   <?php
	   	 $larr = array();
	     $num = 0;
	   
	   if (!empty($this->codes))
	   {
	   foreach ($this->codes as $uu)
	   {
	   ?>
	    <tr>
		 
		 <td>
		     
			 
	   <div style="width: 100%; clear: both;">
	   <select name="op_lang_code2_<?php echo $num; ?>">
	    <option <?php if (empty($lang_shopper_group[$uu['code']])) echo ' selected="selected" '; ?> value=""><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
	    <option  <?php //if (!empty($default_country_array[$uu['code']])) echo ' selected="selected" '; 
		if (!empty($lang_shopper_group[$uu['code']]))
		{
		 echo ' selected="selected" '; 
		 }
		?> value="<?php echo $uu['code']; ?>"><?php echo $uu['code'] ?></option>
	   </select>
	   </div>
	    </td>
		<td>
 
	   <select name="op_group_<?php echo $num; ?>">
	      <option <?php 
		  if (empty($lang_shopper_group[$uu['code']])) echo ' selected="selected" '; 
		  ?> value=""><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
		  
		  
		  <?php foreach ($this->groups as $g)
		  {
		    echo '<option '; 
			if (!empty($lang_shopper_group[$uu['code']]))
			if ($g['virtuemart_shoppergroup_id'] == $lang_shopper_group[$uu['code']]) echo ' selected="selected" '; 
			echo 'value="'.$g['virtuemart_shoppergroup_id'].'">'.JText::_($g['shopper_group_name']).'</option>'; 
		  }
		 ?>

	   </select>
	   
	   
	   
	   <?php 
	   $num++;
	   $larr[] = $uu;
	   echo '</td></tr>'; 
	   }
	   }
	   else
	   {
	    echo JText::_('COM_ONEPAGE_JOS_LANG');
	   } ?>
		
		
		
		
		</table>
		</fieldset>
		<fieldset class="adminform">
		<legend><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_BYIP'); ?></legend>
		<label style="float: left;clear: none; margin: 0; padding:0;" for="option_sgroup2"><input type="radio" id="option_sgroup2" name="option_sgroup" value="2" <?php if (!empty($option_sgroup) && ($option_sgroup==2)) echo ' checked="checked" '; ?> /><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_BYIP_DESC'); ?></label>
		<br style="clear: both;" />
		<table id="ip_shopper_group" class="table table-striped">
		 <tr>
		   <th><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_BYIP_SEARCH'); ?></th>
		   <th><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_BYIP_COUNTRY'); ?></th>
		   <th colspan="2"><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_BYIP_SGROUP'); ?></th>
		   
		 </tr>
		 <?php
		  //echo $num; 
		 $num = 0; 
		 
		 if (empty($lang_shopper_group_ip)) 
		 $lang_shopper_group_ip[0] = ''; 
		 
		 foreach ($lang_shopper_group_ip as $key=>$uu)
		 {
		 
		 if (strpos($key, '-')>0) continue; 
		 
		 $code ='
		
		  <td><input type="text" name="search" onkeyup="javascript: return opc_search(this, \'op_selc2_{num}\')" placeholder="'.addslashes(JText::_('COM_ONEPAGE_SHOPPERGROUP_BYIP_SEARCH_PLACEHOLDER')). '" value="" />
		  </td>
		  
		  <td>
		  <select style="margin: 0;" id="op_selc2_{num}" name="op_selc2_{num}">
	      <option value="0">'.JText::_('COM_ONEPAGE_SHOPPERGROUP_BYIP_NOT').'</option>
		';
		
		foreach ($this->countries as $p)  { 
		
		$uc = $key; 
		$uc = strtoupper($uc); 
		
		$code .= '
		 <option value="'.$p['virtuemart_country_id'].'"';
		  if  ($key==$p['virtuemart_country_id']) $code .= ' selected="selected" '; 
		  
		   $code .= '>'.$p['country_name'].'</option>';
	    }
      $code .= '		
	   </select>
	       </td>
		   <td>
		    <select style="margin: 0;" name="op_group_ip_{num}">
	      <option ';
		  if (empty($lang_shopper_group_ip[$key])) $code .= ' selected="selected" '; 
$code .= ' value="">'.JText::_('COM_ONEPAGE_NOT_CONFIGURED').'</option> '; 
		   foreach ($this->groups as $g)
		  {
		    $code .= '<option '; 
			if (!empty($lang_shopper_group_ip[$key]))
			if ($g['virtuemart_shoppergroup_id'] == $lang_shopper_group_ip[$key]) $code .= ' selected="selected" '; 
			$code .= ' value="'.$g['virtuemart_shoppergroup_id'].'">'.JText::_($g['shopper_group_name']).'</option>'; 
		  }
		$code .= '

	   </select>
	   </td>
		   <td>
		   <a href="#" onclick="javascript: return op_new_line(opc_line, \'ip_shopper_group\' );" >'.JText::_('COM_ONEPAGE_ADD_MORE').'</a>
		   <a style="margin-left: 50px;" href="#" onclick="javascript: return op_remove_line(\'{num}\', \'ip_shopper_group\' );" >'.JText::_('COM_ONEPAGE_REMOVE').'</a>
		   </td>
		  '; 
		  $jscode = $code; 
		  
		  $code_sg = '<tr id="rowid_'.$num.'">'.str_replace('{num}', $num, $code).'</tr>'; 
		 $num++;
		  unset($code); 
		  echo $code_sg; 
		  }
		  //echo $code_sg; 
		  
		  $code_sg = $jscode; 
		  $code_sg = trim($code_sg); 
		  $code_sg = str_replace("\r\r\n", "", $code_sg); 
		  $code_sg = str_replace("\r\n", "", $code_sg); 
		  $code_sg = str_replace("\n", "", $code_sg); 
		  $document->addScriptDeclaration(' 
//<![CDATA[		  
var line_iter = '.$num.'; 
var opc_line = \''.str_replace("'", "\'", $code_sg).'\';
//]]>
'); 

		  
		  //unset($code); 
		  $last_num = $num; 
		  ?>
		  
		  </table>
		  </fieldset>
		   

		</fieldset>
		<fieldset class="adminform"><legend><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_PER_REGISTRATION'); ?></legend>
		<table class="table table-striped">
		
	<tr>
	    <td class="key" >
	     <label for="business_shopper_group" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_BUSINESS_SHOPPER_GROUP_LABEL'); ?></label>
	    </td>
	    <td>
		<select name="business_shopper_group" >
		<option value=""><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
	    <?php foreach ($this->groups as $g)
		  {
		    echo '<option '; 
			if (!empty($business_shopper_group))
			if ($g['virtuemart_shoppergroup_id'] == $business_shopper_group) echo ' selected="selected" '; 
			echo 'value="'.$g['virtuemart_shoppergroup_id'].'">'.JText::_($g['shopper_group_name']).'</option>'; 
		  }
		 ?>
		 </select>
	    </td>
	    <td>
			<?php echo html_entity_decode(JText::_('COM_ONEPAGE_REGISTRATION_BUSINESS_SHOPPER_GROUP_DESC')); ?>
	    </td>
	</tr>
	<tr>
	    <td class="key" >
	     <label for="visitor_shopper_group" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_VISITOR_SHOPPER_GROUP_LABEL'); ?></label>
	    </td>
	    <td>
		<select name="visitor_shopper_group" >
		<option value=""><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
	    <?php foreach ($this->groups as $g)
		  {
		    echo '<option '; 
			if (!empty($visitor_shopper_group))
			if ($g['virtuemart_shoppergroup_id'] == $visitor_shopper_group) echo ' selected="selected" '; 
			echo 'value="'.$g['virtuemart_shoppergroup_id'].'">'.JText::_($g['shopper_group_name']).'</option>'; 
		  }
		 ?>
		 </select>
	    </td>
	    <td>
			<?php echo JText::_('COM_ONEPAGE_REGISTRATION_VISITOR_SHOPPER_GROUP_DESC'); ?>
	    </td>
	</tr>
	
	</table>
		</fieldset>
	<fieldset class="adminform"><legend><?php echo JText::_('COM_ONEPAGE_EUVAT_SECTION'); ?></legend>
		<table class="table table-striped">
	<tr>
	    <td class="key" >
	     <label for="euvat_shopper_group" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_EUVAT_SHOPPER_GROUP_LABEL'); ?></label>
	    </td>
	    <td>
		<select name="euvat_shopper_group" >
		<option value=""><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
	    <?php foreach ($this->groups as $g)
		  {
		    echo '<option '; 
			if (!empty($euvat_shopper_group))
			if ($g['virtuemart_shoppergroup_id'] == $euvat_shopper_group) echo ' selected="selected" '; 
			echo 'value="'.$g['virtuemart_shoppergroup_id'].'">'.JText::_($g['shopper_group_name']).'</option>'; 
		  }
		 ?>
		 </select>
	    </td>
	    <td>
			<?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_PER_EUVAT_DESC'); ?> 
	    </td>
	</tr>
	<tr>
	    <td class="key" >
	     <label for="home_vat_countries" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_EUVAT_HOME_COUNTRY_LABEL'); ?></label>
	    </td>
	    <td>
		 <input type="text" placeholder="<?php echo JText::_('COM_ONEPAGE_REGISTRATION_EUVAT_HOME_COUNTRY_PLACEHOLDER'); ?>" id="home_vat_countries" name="home_vat_countries" value="<?php if (!empty($home_vat_countries)) echo $home_vat_countries; ?>" />
	    </td>
	    <td>
			<?php echo JText::_('COM_ONEPAGE_REGISTRATION_EUVAT_HOME_COUNTRY_DESC'); ?> 
	    </td>
	</tr>
	
	<tr>
	    <td class="key" >
	     <label for="home_vat_num" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_EUVAT_HOME_COUNTRY_VATNUM'); ?></label>
	    </td>
	    <td>
		 <input type="text" placeholder="<?php echo JText::_('COM_ONEPAGE_REGISTRATION_EUVAT_HOME_VATNUM_FIELD'); ?>" id="home_vat_num" name="home_vat_num" value="<?php if (!empty($home_vat_num)) echo $home_vat_num; ?>" />
	    </td>
	    <td>
			<?php echo JText::_('COM_ONEPAGE_REGISTRATION_EUVAT_HOME_VATNUM_FIELD'); ?> 
	    </td>
	</tr>
	
	
	
	<tr>
	    <td class="key" >
	     <label for="opc_euvat" ><?php echo JText::_('COM_ONEPAGE_EUVAT_LABEL'); ?></label>
	    </td>
	    <td>
		 <input type="checkbox" name="opc_euvat" value="1" <?php if (!empty($opc_euvat)) echo ' checked="checked" '; ?> /><br />
		 
	    </td>
	    <td>
			<?php echo JText::_('COM_ONEPAGE_EUVAT_DESC'); ?> 
	    </td>
	</tr>
	
	
	<tr>
	    <td class="key" >
	     <label for="opc_euvat" ><?php echo JText::_('COM_ONEPAGE_FIELD_LABEL'); ?></label>
	    </td>
	    <td>
		 
		 <input type="text" name="opc_vat_field" value="<?php if (!empty($opc_vat_field)) echo $opc_vat_field; else echo 'opc_vat'; ?>" placeholder="<?php echo JText::_('COM_ONEPAGE_FIELD_LABEL'); ?>"/>
	    </td>
	    <td>
			<?php echo JText::_('COM_ONEPAGE_EUVAT_FIELD'); ?> 
	    </td>
	</tr>
	
	<?php /*
	<tr style="display: none; >
	    <td class="key" >
	     <label for="always_zero_tax" ><?php echo JText::_('COM_ONEPAGE_REGISTRATION_EUVAT_SET_ZERO_TAX'); ?></label>
	    </td>
	    <td>
		 <input type="checkbox" name="always_zero_tax" value="1" <?php if (!empty($always_zero_tax)) echo ' checked="checked" '; ?> />
	    </td>
	    <td>
			<?php echo JText::_('COM_ONEPAGE_REGISTRATION_EUVAT_SET_ZERO_TAX_DESC'); ?> 
	    </td>
	</tr>
	*/
	?>
	
	
	
	<tr >
	    <td class="key" >
	     <label for="opc_euvat_button" ><?php echo JText::_('COM_ONEPAGE_EUVAT_USEBUTTON'); ?></label>
	    </td>
	    <td>
		 <input type="checkbox" name="opc_euvat_button" value="1" <?php if (!empty($opc_euvat_button)) echo ' checked="checked" '; ?> />
	    </td>
	    <td>
			<?php echo JText::_('COM_ONEPAGE_EUVAT_USEBUTTON'); ?> 
	    </td>
	</tr>
	<tr >
	    <td class="key" >
	     <label for="opc_euvat_contrymatch" ><?php echo JText::_('COM_ONEPAGE_EUVAT_COUNTRYMATCH'); ?></label>
	    </td>
	    <td>
		 <input type="checkbox" name="opc_euvat_contrymatch" value="1" <?php if ((!empty($opc_euvat_contrymatch))) echo ' checked="checked" '; ?> />
	    </td>
	    <td>
			<?php echo JText::_('COM_ONEPAGE_EUVAT_COUNTRYMATCH'); ?> 
	    </td>
	</tr>
	
		<tr >
	    <td class="key" >
	     <label for="opc_euvat_allow_invalid" ><?php echo JText::_('COM_ONEPAGE_EUVAT_ALLOWINVALID'); ?></label>
	    </td>
	    <td>
		 <input type="checkbox" name="opc_euvat_allow_invalid" value="1" <?php if ((!empty($opc_euvat_allow_invalid))) echo ' checked="checked" '; ?> />
	    </td>
	    <td>
			<?php echo JText::_('COM_ONEPAGE_EUVAT_ALLOWINVALID_DESC'); ?> 
	    </td>
	</tr>
	
	
	
		</table>
		</fieldset>
		
		<?php
		echo $pane->endPanel(); 
							
                    echo $pane->startPanel(JText::_('COM_ONEPAGE_TAXES_PANEL'), 'panel8c6');
?>
		<fieldset class="adminform">
		<?php echo html_entity_decode(JText::_('COM_ONEPAGE_TAXES_DESC')); ?>
		 <table class="admintable table table-striped" id="comeshere4" style="width: 100%;">
	    <tr>
	    <td class="key">
	     <label for="american"><?php echo JText::_('COM_ONEPAGE_TAXES_AMERICA'); ?></label> 
	    </td>
	    <td>
			<input type="checkbox" value="1" name="opc_usmode" <?php if (!empty($opc_usmode)) echo ' checked="checked" '; ?> /> <?php echo JText::_('COM_ONEPAGE_TAXES_AMERICA_DESC'); ?>
	    </td>
		</tr>

		 <tr>
	    <td class="key">
	     <label for="product_price_display"><?php echo JText::_('COM_ONEPAGE_TAXES_ADWORDS'); ?></label> 
	    </td>
	    <td>
	    <select name="product_price_display">
		
		
		<option <?php if (!empty($product_price_display) && ($product_price_display == 'discountedPriceWithoutTax')) echo ' selected="selected" '; ?>value="discountedPriceWithoutTax"><?php echo JText::_('COM_ONEPAGE_TAXES_ADWORDS_AFTERDISCOUNT'); ?></option>
		 <option <?php if (!empty($product_price_display) && ($product_price_display == 'basePriceWithTax')) echo ' selected="selected" '; ?>value="basePriceWithTax"><?php echo JText::_('COM_ONEPAGE_TAXES_ADWORDS_WITHTAX'); ?></option>
		 <option <?php if (!empty($product_price_display) && ($product_price_display == 'basePrice')) echo ' selected="selected" '; ?>value="basePrice"><?php echo JText::_('COM_ONEPAGE_TAXES_ADWORDS_BASEPRICE'); ?></option>
		 <option <?php if (!empty($product_price_display) && ($product_price_display == 'priceWithoutTax')) echo ' selected="selected" '; ?>value="priceWithoutTax"><?php echo JText::_('COM_ONEPAGE_TAXES_ADWORDS_NOTAX'); ?></option>
		 <option <?php if (empty($product_price_display) || (!empty($product_price_display) && ($product_price_display == 'salesPrice'))) echo ' selected="selected" '; ?>value="salesPrice"><?php echo JText::_('COM_ONEPAGE_TAXES_ADWORDS_PRICE'); ?></option>
		</select>
	    </td>
		</tr>
	    <tr>
	    <td class="key">
	     <label for="id_subtotal_display"><?php echo JText::_('COM_ONEPAGE_TAXES_SUBTOTAL'); ?></label> 
	    </td>
	    <td>
	    <select name="subtotal_price_display" id="id_subtotal_display">
		 <option <?php if (!empty($subtotal_price_display) && ($subtotal_price_display == 'basePriceWithTax')) echo ' selected="selected" '; ?>value="basePriceWithTax"><?php echo JText::_('COM_ONEPAGE_TAXES_ADWORDS_WITHTAX'); ?></option>
		 <option <?php if (!empty($subtotal_price_display) && ($subtotal_price_display == 'diffTotals')) echo ' selected="selected" '; ?>value="diffTotals"><?php echo JText::_('COM_ONEPAGE_TAXES_DIFFERENCE_TOTAL'); ?></option>
		 <option <?php if (!empty($subtotal_price_display) && ($subtotal_price_display == 'basePrice')) echo ' selected="selected" '; ?>value="basePrice"><?php echo JText::_('COM_ONEPAGE_TAXES_ADWORDS_BASEPRICE'); ?></option>
		 <option <?php if (!empty($subtotal_price_display) && ($subtotal_price_display == 'billSub')) echo ' selected="selected" '; ?>value="billSub"><?php echo JText::_('COM_ONEPAGE_TAXES_BILLSUB'); ?></option>
		 <option <?php if (!empty($subtotal_price_display) && ($subtotal_price_display == 'priceWithoutTax')) echo ' selected="selected" '; ?>value="priceWithoutTax"><?php echo JText::_('COM_ONEPAGE_TAXES_ADWORDS_NOTAX'); ?></option>
		 <option <?php if (empty($subtotal_price_display) || (!empty($subtotal_price_display) && ($subtotal_price_display == 'salesPrice'))) echo ' selected="selected" '; ?>value="salesPrice"><?php echo JText::_('COM_ONEPAGE_TAXES_ADWORDS_PRICE'); ?></option>
		 
		  <option <?php if (empty($subtotal_price_display) || (!empty($subtotal_price_display) && ($subtotal_price_display == 'product_subtotal'))) echo ' selected="selected" '; ?>value="product_subtotal"><?php echo JText::_('COM_ONEPAGE_TAXES_OPC_CALCULATED_SUBTOTAL'); ?></option>
		 
		</select>
	    </td>
		</tr>
	    <tr>
	    <td class="key">
	     <label for="id_coupon_display"><?php echo JText::_('COM_ONEPAGE_TAXES_COUPON'); ?></label> 
	    </td>
	    <td>
	    <select name="coupon_price_display" id="id_coupon_display">
		 <option <?php if ((!empty($coupon_price_display) && ($coupon_price_display == 'billDiscountAmount'))) echo ' selected="selected" '; ?>value="billDiscountAmount"><?php echo JText::_('COM_ONEPAGE_TAXES_COUPON_BILLDISCOUNT'); ?></option>
		 <option <?php if (!empty($coupon_price_display) && ($coupon_price_display == 'discountAmount')) echo ' selected="selected" '; ?>value="discountAmount"><?php echo JText::_('COM_ONEPAGE_TAXES_COUPON_DISCOUNT'); ?></option>
		 <option <?php if (!empty($coupon_price_display) && ($coupon_price_display == 'couponValue')) echo ' selected="selected" '; ?>value="couponValue"><?php echo JText::_('COM_ONEPAGE_TAXES_COUPON_COUPONVALUE'); ?></option>
		 <option <?php if (!empty($coupon_price_display) && ($coupon_price_display == 'salesWithoutTax')) echo ' selected="selected" '; ?>value="salesWithoutTax"><?php echo JText::_('COM_ONEPAGE_TAXES_COUPON_NOTAX'); ?></option>
		 <option <?php if  (empty($coupon_price_display) || (!empty($coupon_price_display) && ($coupon_price_display == 'salesPriceCoupon'))) echo ' selected="selected" '; ?>value="salesPriceCoupon"><?php echo JText::_('COM_ONEPAGE_TAXES_COUPON_PRICE'); ?></option>
		</select>
	    </td>
		</tr>
		<?php 
		//if (file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_awocoupon')) 
		{
		?>
		
		<tr>
	    <td class="key">
	     <label for="id_coupon_display"><?php echo JText::_('COM_ONEPAGE_TAXES_COUPON_AWO'); ?></label> 
	    </td>
	    <td>
		<?php echo JText::_('COM_ONEPAGE_TAXES_COUPON_AWO_DESC'); ?><br />
	    <select name="coupon_tax_display" id="id_coupon_display">
		 <option <?php if (empty($coupon_tax_display)) echo ' selected="selected" '; ?>value="0"><?php echo JText::_('COM_ONEPAGE_TAXES_COUPON_AWO0'); ?></option>
		 <option <?php if (!empty($coupon_tax_display) && ($coupon_tax_display === 1)) echo ' selected="selected" '; ?>value="1"><?php echo JText::_('COM_ONEPAGE_TAXES_COUPON_AWO1'); ?></option>
		 <option <?php if (!empty($coupon_tax_display) && ($coupon_tax_display === 2)) echo ' selected="selected" '; ?>value="2"><?php echo JText::_('COM_ONEPAGE_TAXES_COUPON_AWO2'); ?></option>
		 <option <?php if (!empty($coupon_tax_display) && ($coupon_tax_display === 3)) echo ' selected="selected" '; ?>value="3"><?php echo JText::_('COM_ONEPAGE_TAXES_COUPON_AWO3'); ?></option>
		 <option <?php if  (!empty($coupon_tax_display) && (!empty($coupon_tax_display) && ($coupon_tax_display === 4))) echo ' selected="selected" '; ?>value="4"><?php echo JText::_('COM_ONEPAGE_TAXES_COUPON_AWO4'); ?></option>
		</select>
		
		 <select name="coupon_tax_display_id" id="id_coupon_display_id">
		 <option value=""><?php echo JText::_('COM_ONEPAGE_SELECT_NONE'); ?></option>
		  <?php 
			   if (!empty($this->calcs))
				foreach ($this->calcs as $id => $calc_name) {
				 ?>
				 <option <?php if  (!empty($coupon_tax_display_id) && (!empty($coupon_tax_display_id) && ($coupon_tax_display_id === $id))) echo ' selected="selected" '; ?>value="<?php echo $id; ?>"><?php echo $calc_name; ?></option>
				 <?php
				}
		  ?>
		 </select>
		
	    </td>
		</tr>
		
		<?php
		
		
		}
		?>
		
			<tr>
		<td class="key">
	     <label for="payment_discount_before2"><?php echo JText::_('COM_ONEPAGE_TAXES_PAYMENT'); ?></label> 
	    </td>
	    <td>
		 <input type="checkbox" value="1" id="payment_discount_before" name="payment_discount_before" <?php if (!empty($payment_discount_before)) echo ' checked="checked" '; ?> value="1" /> <label for="payment_discount_before"><?php echo JText::_('COM_ONEPAGE_TAXES_PAYMENT_DESC'); ?></label> 
		 
		 <select name="other_discount_display" id="other_coupon_display">
		 <option <?php if (empty($other_discount_display) ||((!empty($other_discount_display) && ($other_discount_display == 'billDiscountAmount')))) echo ' selected="selected" '; ?>value="billDiscountAmount"><?php echo JText::_('COM_ONEPAGE_TAXES_COUPON_BILLDISCOUNT'); ?></option>
		 <option <?php if (!empty($other_discount_display) && ($other_discount_display == 'discountAmount')) echo ' selected="selected" '; ?>value="discountAmount"><?php echo JText::_('COM_ONEPAGE_TAXES_COUPON_DISCOUNT'); ?></option>
		 <option <?php if (!empty($other_discount_display) && ($other_discount_display == 'minus')) echo ' selected="selected" '; ?>value="minus"><?php echo JText::_('COM_ONEPAGE_OTHER_DISCOUNT_MINUS'); ?></option>
		
		 <option <?php if (!empty($other_discount_display) && ($other_discount_display == 'sum')) echo ' selected="selected" '; ?>value="sum"><?php echo JText::_('COM_ONEPAGE_OTHER_DISCOUNT_SUM'); ?></option>
		</select>
		 
		 
		 
		</td>
		</tr>
		<tr>
			    <td class="key">
	     <label for="zero_total_status"><?php echo JText::_('COM_ONEPAGE_TAXES_ZEROTOTAL'); ?></label> 
	    </td>
	    <td>
	    <select name="zero_total_status" id="zero_total_status">
		  <?php 
		  foreach ($this->statuses as $k=>$s)
		   {
		      echo '<option '; 
		   if (empty($zero_total_status) && ($s['order_status_code'] == 'C')) echo ' selected="selected" '; 
		   else if ((!empty($zero_total_status)) && ($zero_total_status == $s['order_status_code'])) echo ' selected="selected" '; 
			  
			  echo ' value="'.$s['order_status_code'].'">'.JText::_($s['order_status_name']).'</option>'; 
		   }
		  ?>
		</select>
	    </td>
		</tr>
		
		
		<tr>
		<?php 
		if (!isset($show_single_tax)) $show_single_tax = true; 
		?>
		<td class="key">
	     <label for="show_single_tax"><?php echo JText::_('COM_ONEPAGE_TAXES_SINGLETAX'); ?></label> 
	    </td>
	    <td>
		 <input type="checkbox" id="show_single_tax" name="show_single_tax" <?php if (!empty($show_single_tax)) echo ' checked="checked" '; ?> value="1" /> 
		 <?php echo JText::_('COM_ONEPAGE_TAXES_SINGLETAX_DESC'); ?>
		</td>
		</tr>


		<tr>
		<?php 
		
		?>
		<td class="key">
	     <label for="opc_dynamic_lines"><?php echo JText::_('COM_ONEPAGE_TAXES_DYNAMIC_LINES'); ?></label> 
	    </td>
	    <td>
		 <input type="checkbox" id="opc_dynamic_lines" name="opc_dynamic_lines" <?php if (!empty($opc_dynamic_lines)) echo ' checked="checked" '; ?> value="1" /> 
		 <?php echo JText::_('COM_ONEPAGE_TAXES_DYNAMIC_LINES_DESC'); ?><br />
		 <?php echo JText::_('COM_ONEPAGE_TAXES_DYNAMIC_LINES_DESC2'); ?>
		 
		</td>
		</tr>

		
		<tr>
		<?php 
		
		?>
		<td class="key">
	     <label for="opc_tax_name_display"><?php echo JText::_('COM_ONEPAGE_TAXES_DISPLAYTAXNAME').'<br />'.JText::_('COM_ONEPAGE_NEW'); ?></label> 
	    </td>
	    <td>
		 <input type="checkbox" id="opc_tax_name_display" name="opc_tax_name_display" <?php if (!empty($opc_tax_name_display)) echo ' checked="checked" '; ?> value="1" /> 
		 <?php echo JText::_('COM_ONEPAGE_TAXES_DISPLAYTAXNAME'); ?>
		</td>
		</tr>
		<?php 
		// removed in 3.0.320 until needed: 
		if (false) {
		?>			
<tr>
		<?php 
		
		?>
		<td class="key">
	     <label for="awo_fix"><?php echo JText::_('COM_ONEPAGE_AWOFIX_BILLTAX'); ?></label> 
	    </td>
	    <td>
		 <input type="checkbox" id="awo_fix" name="awo_fix" <?php if (!empty($awo_fix)) echo ' checked="checked" '; ?> value="1" /> 
		 <?php echo JText::_('COM_ONEPAGE_AWOFIX_BILLTAX_DESC'); ?><br />
	
		 
		</td>
		</tr>
		<?php } 
		 else echo '<input type="hidden" id="awo_fix" name="awo_fix" value="0" /> '; 
		?>	
	  </table>
		</fieldset> 
		<?php 
		if (!empty($this->currencies) && (count($this->currencies) > 1)) {  ?> 
		
		<fieldset class="adminform">
		  <legend><?php echo JText::_('COM_ONEPAGE_CURRENCY_SETTINGS'); ?></legend>
		  <?php
		  
		  
			
			
		  ?>
		  
		  <table class="admintable adminlist table table-striped">
		  <tr>
		    <td class="key"><?php echo JText::_('COM_ONEPAGE_ENABLE_GEO_CURRENCY_PLUGIN'); ?>
			</td>
			<td><select name="currency_plg" id="currency_plg">
			   <option value=""
			   <?php $enabled = JPluginHelper::isEnabled('system', 'opc_currency'); 
			   
			   if (empty($enabled)) echo ' selected="selected" '; 
			   
			   ?> ><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
			    <?php 
				if (file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_geolocator'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'helper.php')) 
				{


			?>
			   <option value="1" <?php if (( (!empty($enabled))) && ((!empty($currency_switch) && $currency_switch === 1))) echo ' selected="selected" '; ?> ><?php echo JText::_('COM_ONEPAGE_CURRENCY_PER_GEOIP'); ?></option>
				<?php } ?>
			   <option value="2" <?php if (( !empty($enabled)) && ((!empty($currency_switch) && $currency_switch === 2))) echo ' selected="selected" '; ?> ><?php echo JText::_('COM_ONEPAGE_CURRENCY_PER_JOOMLA_LANG'); ?></option>
			   
			</td>
			  <td colspan="2">
			   <label for="currency_plg"><span style="color: <?php 
			   if (!$enabled) echo 'red'; 
			   else echo 'green'; ?>;"><?php
			   echo JText::_('COM_ONEPAGE_ENABLE_GEO_CURRENCY_PLUGIN_DESC'); ?></span></label>
			  </td>
		  </tr>
		   <tr>
		    <td class="key"><?php echo JText::_('COM_ONEPAGE_ENABLE_GEO_CURRENCY_PLUGIN_DISABLE_CHANGE'); ?>
			</td>
			<td>
			   <?php 
			   $default = false; 
			   $enabled = OPCconfig::getValue('currency_config', 'can_change', 0, $default);
			    
			   
			   ?>
			   <input type="checkbox" name="currency_plg_can_change" value="1" <?php if (!empty($enabled)) echo ' checked="checked" '; ?> id="currency_plg" />
			</td>
			  <td colspan="2">
			   <label for="currency_plg_can_change"><span> 
			   <?php
			   echo JText::_('COM_ONEPAGE_ENABLE_GEO_CURRENCY_PLUGIN_CAN_CHANGE_DESC'); ?></span></label>
			  </td>
		  </tr>
		  </table>
		  </fieldset>
		  
		  <fieldset><legend><?php echo JText::_('COM_ONEPAGE_CURRENCY_PER_GEOIP'); ?></legend>
		  
		  <?php
		   if (!file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_geolocator'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'helper.php'))
		    {
			  echo '<span style="color: red;">'.JText::_('COM_ONEPAGE_GEO_COUNTRY_CURRENCY_GEOLOCATOR_NOT_FOUND').'</span>You can install it from OPC Extension tab.<br />';
			}
			else
			{
				?>
		  
		  <table class="admintable adminlist table table-striped">
		  <?php foreach ($this->currencies as $c)
		  {
		  ?>
		  
		  <tr>
		    <td class="key">
			 <?php echo JText::_('COM_ONEPAGE_ASSOCIATE_A_COUNTRY_TO_A_CURRENCY'); ?>
			</td>
		    <td>
			<?php
			     echo $c->currency_name.' ('.$c->currency_code_3.')'; 
			   
			   ?>
			 
			</td>
			
			<td>
			
			<select style="min-width: 150px; " data-placeholder="<?php echo JText::_('COM_VIRTUEMART_COUNTRY_S'); ?>" multiple="multiple" name="country_currency[<?php echo $c->virtuemart_currency_id; ?>][]" class="vm-chzn-select"  id="country_currency_<?php echo $c->virtuemart_currency_id; ?>">
			<?php 
			$default = 0; 
			if (!empty($this->countries))
			 {
			    foreach($this->countries as $p)
				 {
				 
				 $c_int = (int)OPCconfig::getValue('currency_config', $p['country_2_code'], 0, $default); 
				 
				 
				 
				 $p['virtuemart_country_id'] = (int)$p['virtuemart_country_id']; 
				 
				 
				 
				 ?>
				    <option value=<?php echo '"'.$p['virtuemart_country_id'].'"';
		 if ($c_int == $c->virtuemart_currency_id) 
		 {
			
		   echo ' selected="selected" '; 
		 }
		 ?>><?php echo $p['country_name']; ?></option>
				 
				 <?php
				 }
			 }
			?>
			</select>
			</td>
		  </tr>
		 <?php 
		 }
		 ?>
		 </table>
		 
		 <?php 
		 } 
		 ?>
		</fieldset>
		
		<fieldset><legend><?php echo JText::_('COM_ONEPAGE_CURRENCY_PER_JOOMLA_LANG'); ?></legend>
		  
		  <table class="admintable adminlist table table-striped">
		  
		  <?php 
		  if (!empty($this->codes))
	   {
	   foreach ($this->codes as $uu)
	   {
		   ?><tr><td><?php echo $uu['code']; ?></td><td><select name="currency_per_lang[<?php echo $uu['code']; ?>]">
		   <option value="" ><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
		   <?php 
		   foreach ($this->currencies as $c) {
		    echo '<option value="'.$c->virtuemart_currency_id.'"'; 
			if ((!empty($currency_per_lang[$uu['code']])) && ($currency_per_lang[$uu['code']] == $c->virtuemart_currency_id))
				echo ' selected="selected" '; 
			echo ' >'.$c->currency_name.' ('.$c->currency_code_3.')'.'</option>'; 
		   }
		   ?></select></td></tr>
		   
		   <?php
	   }
	   }
	   ?>
		   </table>
		   
		</fieldset>
		
		<fieldset><legend><?php echo JText::_('COM_ONEPAGE_MULTICURRENCY_CONFIG_LABEL'); ?></legend>
		  
		  <table class="admintable adminlist table table-striped">
		  
		 
		<tr>
		
		<td class="key"><label for="override_payment_currency"><?php echo JText::_('COM_ONEPAGE_PAYMENT_CURRENCY_TO_USER_CURRENCY'); ?></label>
			</td>
		
		<td>
		   <?php 
			   $default = false; 
			   $enabled = OPCconfig::getValue('currency_config', 'can_change', 0, $default);
			    
			   
			   ?>
			   <input type="checkbox" name="override_payment_currency" value="1" <?php if (!empty($override_payment_currency)) echo ' checked="checked" '; ?> id="override_payment_currency" />
		</td>
		 <td >
			   <label for="override_payment_currency"><span> 
			   <?php
			   echo JText::_('COM_ONEPAGE_PAYMENT_CURRENCY_TO_USER_CURRENCY_LABEL'); ?></span></label>
			  </td>
		
		
		</tr>
		   
		  
		   </table>
		   
		</fieldset>
		
		
		<?php
		}
         echo $pane->endPanel();
        echo $pane->startPanel(JText::_('COM_ONEPAGE_AFTER_CHECKOUT_PANEL'), 'panel1d9');
		?>
		<fieldset class="adminform" style="overflow: visible;">
		
		<legend><?php echo JText::_('COM_ONEPAGE_THIRDPART_SUPPORT'); ?></legend>
          <table class="admintable table table-striped" id="comeshere5" style="width: 100%;">
		<tr>
		<?php 
		if (!isset($do_not_allow_gift_deletion)) $do_not_allow_gift_deletion = false; 
		?>
		<td class="key">
	     <label for="do_not_allow_gift_deletion"><?php echo JText::_('COM_ONEPAGE_TAXES_DONOT_DELETE_GIFTS'); ?></label> 
	    </td>
	    <td>
		
		 <input style="float:left;" type="checkbox" id="do_not_allow_gift_deletion" name="do_not_allow_gift_deletion" <?php if (!empty($do_not_allow_gift_deletion)) echo ' checked="checked" '; ?> value="1" /> 
		 </td>
		 <td>
		 <label for="do_not_allow_gift_deletion" style="float:left;">
		 <?php echo JText::_('COM_ONEPAGE_TAXES_DONOT_DELETE_GIFTS_DESC'); ?>
		 </label>
		 
		 <div style="">
		 <select name="gift_order_statuses[]" multiple="multiple" style="width: 150px;" class="vm-chzn-select" >
		 <?php 
		  		  foreach ($this->statuses as $k=>$s)
		   {
		      echo '<option '; 
			  
			  if (!empty($gift_order_statuses))
		      if (in_array($s['order_status_code'], $gift_order_statuses)) echo ' selected="selected" '; 
		   
			  
			  echo ' value="'.$s['order_status_code'].'">'.JText::_($s['order_status_name']).'</option>'; 
		   }

		  
		  ?>
		 </select>
		 </div>
		 
		</td>
		</tr>
		<tr>
		<td class="key"><label for="theme_fix1"><?php echo JText::_('COM_ONEPAGE_THEME_FIX1_LABEL'); ?></label></td>
		<td colspan="2"><input type="checkbox" id="theme_fix1" name="theme_fix1" <?php if (!empty($theme_fix1)) echo ' checked="checked" '; ?> value="1" /><label for="theme_fix1"> <?php echo JText::_('COM_ONEPAGE_THEME_FIX1_DESC'); ?></label></td>
		</tr>

		<tr>
		<td class="key"><label for="email_fix1"><?php echo JText::_('COM_ONEPAGE_EMAIL_FIX1_LABEL'); ?></label></td>
		<td colspan="2"><input type="checkbox" id="email_fix1" name="email_fix1" <?php if (!empty($email_fix1)) echo ' checked="checked" '; ?> value="1" /><label for="email_fix1"> <?php echo JText::_('COM_ONEPAGE_EMAIL_FIX1_DESC'); ?></label></td>
		</tr>

		<tr>
		<td class="key"><label for="email_fix2"><?php echo JText::_('COM_ONEPAGE_EMAIL_FIX2_LABEL'); ?></label></td>
		<td colspan="2"><input type="checkbox" id="email_fix2" name="email_fix2" <?php if (!empty($email_fix2)) echo ' checked="checked" '; ?> value="1" /><label for="email_fix2"> <?php echo JText::_('COM_ONEPAGE_EMAIL_FIX2_DESC'); ?></label></td>
		</tr>

		
		<tr>
		<td class="key"><label for="email_fix3"><?php echo JText::_('COM_ONEPAGE_EMAIL_FIX3_LABEL'); ?></label></td>
		<td colspan="2"><select multiple="multiple" style="max-width: 100px;" id="email_fix3" name="email_fix3[]" class="vm-chzn-select"  >
		<?php
		
		$default = array(); 
		$email_fix3 = OPCConfig::getValue('opc_config', 'email_fix3', 0, $default, false, false);
		
		foreach($this->pms as $p)
		{
		 ?>
		 <option value=<?php echo '"'.$p['payment_method_id'].'" '; if (in_array($p['payment_method_id'], $email_fix3)) echo 'selected="selected" '; ?>><?php echo $p['payment_method_name'];?></option>
		 <?php
		}
		
		?>
		
		</select>
		<label for="email_fix3"> <?php echo JText::_('COM_ONEPAGE_EMAIL_FIX3_LABEL_DESC'); ?></label></td>
		</tr>
		
		<?php if (!empty($vendor_emails)) $vendor_emails =  html_entity_decode($vendor_emails); ?>
		<tr>
		<td class="key"><label for="vendor_emails"><?php echo JText::_('COM_ONEPAGE_VENDOR_EMAILS'); ?></label></td>
		<td colspan="2"><input style="width: 95%; " type="text" placeholder="<?php echo htmlentities(JText::_('COM_ONEPAGE_VENDOR_EMAILS')); ?>" id="vendor_emails" name="vendor_emails" value="<?php if (!empty($vendor_emails)) echo htmlentities($vendor_emails); ?>" /><label for="vendor_emails"> <?php echo JText::_('COM_ONEPAGE_VENDOR_EMAILS_DESC'); ?></label></td>
		</tr>
		
		
		<tr>
		<td class="key"><label for="order_reuse_fix"><?php echo JText::_('COM_ONEPAGE_ALLOW_ORDER_REUSE'); ?></label></td>
		<td colspan="2"><input type="checkbox" id="order_reuse_fix" name="order_reuse_fix" <?php if (!empty($order_reuse_fix)) echo ' checked="checked" '; ?> value="1" />
		<select name="reuse_order_statuses[]" id="reuse_order_statuses" multiple="multiple" class="vm-chzn-select chzn-done" style="width: 150px; ">
		  <?php 
		  foreach ($this->statuses as $k=>$s)
		   {
		      echo '<option '; 
		   if (($s['order_status_code'] == 'P')) echo ' selected="selected" '; 
		   else if ((!empty($reuse_order_statuses)) && (in_array($s['order_status_code'], $reuse_order_statuses))) echo ' selected="selected" '; 
			  
			  echo ' value="'.$s['order_status_code'].'">'.JText::_($s['order_status_name']).'</option>'; 
		   }
		  ?>
		</select>

		<label for="order_reuse_fix"> <?php echo JText::_('COM_ONEPAGE_ALLOW_ORDER_REUSE_DESC'); ?></label></td>
		</tr>

		
		
		</table>
		</fieldset>
		<fieldset class="adminform">
		
		
		<legend><?php echo JText::_('COM_ONEPAGE_AFTER_CHECKOUT_STOCK_HANDLING'); ?></legend>
          <table class="admintable table table-striped" id="comeshere6" style="width: 100%;">
		  	
	   <tr>
	    <td class="key">
	     <label for="opc_stock_handling"><?php echo JText::_('COM_ONEPAGE_AFTER_CHECKOUT_STOCK_HANDLING'); 
		 
		 
		 
		 ?></label> 
	    </td>
		  
		<td>
		<select name="opc_stock_handling" id="opc_stock_handling">
		 <option <?php if (empty($opc_stock_handling)) echo ' selected="selected" '; ?> value="0"><?php echo JText::_('COM_ONEPAGE_AFTER_CHECKOUT_STOCK_HANDLING_FOLLOWVM'); ?></option>
		 <?php 
		 if (empty($op_ignore_ordered_products)) { ?>
		 <option <?php if (!empty($opc_stock_handling) && ($opc_stock_handling === 1)) echo ' selected="selected" '; ?> value="1"><?php echo JText::_('COM_ONEPAGE_AFTER_CHECKOUT_STOCK_HANDLING_BLOCK_WHENSUM'); ?></option>
		 <?php } ?>
		 <option <?php if (!empty($opc_stock_handling) && ($opc_stock_handling === 2)) echo ' selected="selected" '; ?>value="2"><?php echo JText::_('COM_ONEPAGE_AFTER_CHECKOUT_STOCK_HANDLING_BLOCK_ALWAYS'); ?></option>
		 
		 
		 <option <?php if (!empty($opc_stock_handling) && ($opc_stock_handling === 3)) echo ' selected="selected" '; ?>value="3"><?php echo JText::_('COM_ONEPAGE_AFTER_CHECKOUT_STOCK_HANDLING_ALLOW_ALWAYS'); ?></option>
		</select>
		
		
		</td>
		<td>
		 <?php echo JText::_('COM_ONEPAGE_AFTER_CHECKOUT_STOCK_HANDLING_DESC'); ?>
		</td>
		</tr>
		  
		   <tr>
	    <td class="key">
	     <label for="opc_stock_zero_weight"><?php echo JText::_('COM_ONEPAGE_AFTER_CHECKOUT_STOCK_HANDLING_SERVICES'); ?></label> 
	    </td>
		  
		<td>
		<input type="checkbox" name="opc_stock_zero_weight" id="opc_stock_zero_weight" <?php if (!empty($opc_stock_zero_weight)) echo ' checked="checked" '; ?> value="1" />
		</td>
		<td>
		 <?php echo JText::_('COM_ONEPAGE_AFTER_CHECKOUT_STOCK_HANDLING_SERVICES'); ?>
		</td>
		</tr>
		  
		  
		  </table>
		  </fieldset>
		<fieldset class="adminform">
		
        <legend><?php echo JText::_('COM_ONEPAGE_AFTER_CHECKOUT'); ?></legend>
          <table class="admintable table table-striped" id="comeshere6" style="width: 100%;">
	   
		
	   <tr>
	    <td class="key">
	     <label for="tr_ext_id"><?php echo JText::_('COM_ONEPAGE_AFTER_CHECKOUT_SENDEMAIL'); ?></label> 
	    </td>
		  
		<td>
		<input type="checkbox" name="send_pending_mail" id="send_pending_mail" <?php if (!empty($send_pending_mail)) echo ' checked="checked" '; ?> value="1" />
		</td>
		<td>
		 <?php echo JText::_('COM_ONEPAGE_AFTER_CHECKOUT_SENDEMAIL_DESC'); ?>
		</td>
		</tr>
		</table>
        </fieldset>
		<fieldset class="adminform">
        
		<legend><?php echo JText::_('COM_ONEPAGE_AFTER_CHECKOUT_THANKYOU'); ?></legend>
          <table class="admintable table table-striped" id="comeshere7" style="width: 100%;">
	    <tr>
		<td class="key"><label for="product_id_ty"><?php echo JText::_('COM_ONEPAGE_PRODUCT_ID_TY_PAGE'); ?></label></td>
		<td colspan="2"><input type="checkbox" id="product_id_ty" name="product_id_ty" <?php if (!empty($product_id_ty)) echo ' checked="checked" '; ?> value="1" />
		<label for="product_id_ty"> <?php echo JText::_('COM_ONEPAGE_PRODUCT_ID_TY_PAGE_DESC'); ?></label></td>
		</tr>
		 <tr>
		 
		 <td class="key">
	   <label for="adwords_enabled_0"><span style="color: <?php if (!empty($this->isEnabled)) echo 'green'; else echo 'red'; ?>;"><?php echo JText::_('COM_ONEPAGE_TRACKING_ADWORDS_ENABLE'); ?></span></label> 
	    </td>
		 
	    <td >
		 <input id="adwords_enabled_0" type="checkbox" value="1" name="adwords_enabled_0" <?php if (!empty($this->isEnabled)) echo 'checked="checked" '; ?>/>
	     
	    </td>
	    
		<td>
		</td>
		</tr>
		
		
		
		<tr>
	    <td class="key">
	     <label for="append_details"><?php echo JText::_('COM_ONEPAGE_AFTER_CHECKOUT_THANKYOU_APPEND'); ?></label> 
	    </td>
		  
		<td>
		<input type="checkbox" name="append_details" id="append_details" <?php if (!empty($append_details)) echo ' checked="checked" '; ?> value="1" />
		</td>
		<td>
		  <?php echo JText::_('COM_ONEPAGE_AFTER_CHECKOUT_THANKYOU_APPEND_DESC'); ?>
		</td>
		</tr>
		</table>
		</fieldset>
		<fieldset class="adminform">
		<legend><?php echo JText::_('COM_ONEPAGE_AFTER_CHECKOUT_THANKYOU_ARTICLE'); ?></legend>
		<p><?php echo JText::_('COM_ONEPAGE_TY_DESC'); ?></p>
		<table id="table_thankyou_config" class="table table-striped">
		<tr>
		   <th><?php echo JText::_('COM_ONEPAGE_TAXES_DONOT_DELETE_GIFTS_STATUSES'); ?></th>
		   <th><?php echo JText::_('COM_VIRTUEMART_PAYMENTMETHOD'); ?></th>
		   <th ><?php echo JText::_('COM_CONTENT_SELECT_AN_ARTICLE'); ?></th>
		   <th ><?php echo JText::_('COM_ONEPAGE_SHOPPERGROUP_LANGGROUP'); ?></th>
		    <th ><?php echo JText::_('COM_ONEPAGE_MODE'); ?></th>
			 <th ><?php echo JText::_('COM_ONEPAGE_ADD_MORE'); ?> / <?php echo JText::_('COM_ONEPAGE_REMOVE'); ?></th>
			  
		 </tr>

		
		<?php
		//stAn thank you page articla start
		
		  //echo $num; 
		 $num = 0; 
		 
		 if (empty($this->lang_thank_youpage)) 
		 $this->lang_thank_youpage[0] = ''; 
		 
		
		 
		 
		 foreach ($this->lang_thank_youpage as $key=>$uu2)
		 {
		  
		 if (strpos($key, '-')>0) continue; 
		 $uu = (array)$uu2; 
		 if (empty($uu['order_status'])) $uu['order_status'] = ''; 
		 $code ='
		
		  
		  
		  <td>
		  
		  <select style="margin: 0;" id="op_ostatus_{num}" name="op_ostatus_{num}">
		  <option value="">'.JText::_('COM_ONEPAGE_NOT_CONFIGURED').'</option>
	      <option '; 
		  if ($uu['order_status'] == '-0') $code .= ' selected="selected" '; 
		  
		 $code .= ' value="-0">- '.JText::_('COM_ONEPAGE_ANY').' -</option>
		';
		 
		  foreach ($this->statuses as $k=>$s)
		   {
		      $code .= '<option '; 
		  // if (empty($uu['order_status']) && ($s['order_status_code'] == 'C')) 
		  // $code .= ' selected="selected" '; 
		  // else 
		    
		  if ((!empty($uu['order_status'])) && ($uu['order_status'] == $s['order_status_code'])) 
		   $code .= ' selected="selected" '; 
			  
			  $code .= ' value="'.$s['order_status_code'].'">'.JText::_($s['order_status_name']).'</option>'; 
		   }
      $code .= '		
	   </select>
	       </td>
		   <td>
		    <select style="margin: 0;" name="op_opayment_{num}">
			<option ';
			
	      
		  if (empty($uu['payment_id'])) 
		  {
		  $code .= ' selected="selected" '; 
		  $uu['payment_id'] = 0; 
		  }
		  $code .= 'value="">'.JText::_('COM_ONEPAGE_NOT_CONFIGURED').'</option> <option ';
		
		  if ($uu['payment_id']=='-0') 
		  {
		  $code .= ' selected="selected" '; 
		  
		  }
		$code .= ' value="-0">- '.JText::_('COM_ONEPAGE_ANY').' -</option> '; 
		foreach($this->pms as $p)
		{
		 $code .= '
		 <option value="'.$p['payment_method_id'].'" '; 
		 if ($p['payment_method_id']==$uu['payment_id']) 
		 $code .=		 'selected="selected" '; 
		 $code .= '>'; 
		 $code .= $p['payment_method_name'].'</option>'; 
		 
		}
		$code .= '

	   </select>
	   </td>
	   <td>'; 
	   if (empty($uu['article_id'])) $uu['article_id'] = 0; 
	   $artc = $this->model->getArticleSelector('op_oarticle_{num}', $uu['article_id']); 
	   $code .= $artc; 
	  
		  
		 $code .=' </td><td>
		    <select style="margin: 0;" name="op_olang_{num}">
	      <option ';
		  if (empty($uu['language'])) 
		  {
		  $code .= ' selected="selected" '; 
		  $uu['language'] = null; 
		  }
$code .= ' value="-0">- '.JText::_('COM_ONEPAGE_ANY').' -</option> '; 
		foreach($this->codes as $p)
		{
		 $code .= '
		 <option value="'.$p['lang_code'].'" '; 
		 if ($p['lang_code']==$uu['language'])
		 {		 	
		 
		 $code .=		 'selected="selected" '; 
		 }
		 $code .= '>'; 
		 $code .= $p['lang_code'].'</option>'; 
		 
		}
		$code .= '

	   </select>
	   </td>';
	   
	    $code .=' <td>
		    <select style="margin: 0;" name="op_omode_{num}">';
		  if (empty($uu['mode'])) 
		  {

		  $uu['mode'] = 0; 
		  }

		$modes = array(0,1,2); 
		foreach($modes as $p)
		{
		 $code .= '
		 <option value="'.$p.'" '; 
		 if ($p==$uu['mode']) 
		 $code .=		 'selected="selected" '; 
		 $code .= '>'; 
		 $code .= JText::_('COM_ONEPAGE_TY_MODE_'.$p).'</option>'; 
		 
		}
		$code .= '

	   </select>
	   </td>
	   ';
	   
		  
		   $code .='
		   <td>
		   <a href="#" onclick="javascript: return op_new_line2(opc_line_ty, \'table_thankyou_config\' );" >'.JText::_('COM_ONEPAGE_ADD_MORE').'</a><br />
		   <a style="" href="#" onclick="javascript: return op_remove_line2(\'{num}\', \'ip_shopper_group\' );" >'.JText::_('COM_ONEPAGE_REMOVE').'</a>
		   </td>
		  '; 
		  $jscode = $code; 
		  
		  $code_sg = '<tr id="rowid2_'.$num.'">'.str_replace('{num}', $num, $code).'</tr>'; 
		 $num++;
		  unset($code); 
		  echo $code_sg; 
		  }
		  
		  $code_sg = $jscode; 
		  $code_sg = trim($code_sg); 
		  $code_sg = str_replace("\r\r\n", "", $code_sg); 
		  $code_sg = str_replace("\r\n", "", $code_sg); 
		  $code_sg = str_replace("\n", "{br}", $code_sg); 
		  $code_sg = str_replace("<", '&lt;', $code_sg); 
		  $code_sg = str_replace(">", '&lg;', $code_sg); 
		  $document->addScriptDeclaration(' 
//<![CDATA[		  
var line_iter2 = '.$num.'; 
var opc_line_ty = \''.str_replace("'", "\'", $code_sg).'\';
//]]>
'); 

		  
		  //unset($code); 
		  $last_num = $num; 
		  
		  
		  
		  ?>
		  </table>
        </fieldset>
		
		<fieldset>
		<legend><?php echo JText::_('COM_ONEPAGE_AFTER_CHECKOUT_THANKYOU_TRACKING'); ?>
		</legend>
		<p><?php echo JText::_('COM_ONEPAGE_AFTER_CHECKOUT_THANKYOU_TRACKING_DESC'); ?></p>
		<table id="ttable_thankyou_config" class="table table-striped">
		<?php
		  
		
		// stAn thank page article end
		
		//tracking per payment
		
		 $num = 0; 
		 
		 
		 if (!empty($this->trackingfiles))
		 foreach ($this->statuses as $k=>$s)
		 foreach ($this->trackingfiles as $k2=>$s2)
		 {
		  //if ($s2 === 'custom_code') continue; 
		  
		  $enabled = $this->modelT->isPluginEnabled($s2, $this->configT); 
		  if (!$enabled) continue; 
		 
		 $config = new stdClass(); 
		 $prevConfig = OPCconfig::getValue('tracking_config', $s2, 0, $config); 	
		 if (empty($prevConfig->enabled)) continue; 
		 
		 $uu = array(); 
		 
		 if (!isset($this->configT[$s['order_status_code']]->$s2)) continue; 
		 
		 
		 $uu['order_status'] = $s['order_status_code']; 
		 $uu['payment_id'] = ''; 
		 
		 if (isset($prevConfig->advanced)) {
		   $prevConfig->advanced = (array)$prevConfig->advanced; 
		   if (isset($prevConfig->advanced[$s['order_status_code']])) {
		     $prevConfig->advanced[$s['order_status_code']] = (object)$prevConfig->advanced[$s['order_status_code']]; 
			 if (!empty($prevConfig->advanced[$s['order_status_code']]->payment_id)) {
			    $uu['payment_id'] = $prevConfig->advanced[$s['order_status_code']]->payment_id; 
				
			 }
			 if (!empty($prevConfig->advanced[$s['order_status_code']]->language)) {
			    $uu['language'] = $prevConfig->advanced[$s['order_status_code']]->language; 
				
			 }
			 
		   }
		 }
		 
		 
		 $code = '
		
		  
		  
		  <td>
		  
		  <select style="margin: 0;" id="top_ostatus_{num}" name="top_ostatus_{num}">
		   '; 
	      
		 
		  foreach ($this->statuses as $kX=>$sX)
		   {
		      $code .= '<option '; 
		   if (empty($uu['order_status']) && ($sX['order_status_code'] == 'C')) 
		   $code .= ' selected="selected" '; 
		   else 
		  if ((!empty($uu['order_status'])) && ($uu['order_status'] == $sX['order_status_code'])) 
		   $code .= ' selected="selected" '; 
			  
			  $code .= ' value="'.$sX['order_status_code'].'">'.JText::_($sX['order_status_name']).'</option>'; 
		   }
      $code .= '		
	   </select>
	       </td>
		   <td>
		    <select style="margin: 0;" name="top_opayment_{num}">
			<option ';
		
		  if (($uu['payment_id']=='-0') || (empty($uu['payment_id'])))
		  {
		  $code .= ' selected="selected" '; 
		  
		  }
		$code .= ' value="">- '.JText::_('COM_ONEPAGE_ANY').' -</option> '; 
		foreach($this->pms as $p)
		{
		 $code .= '
		 <option value="'.$p['payment_method_id'].'" '; 
		 if ($p['payment_method_id']==$uu['payment_id']) 
		 $code .=		 'selected="selected" '; 
		 $code .= '>'; 
		 $code .= $p['payment_method_name'].'</option>'; 
		 
		}
		$code .= '

	   </select>
	   </td>
	   <td>'; 
	   /*
	   if (empty($uu['article_id'])) $uu['article_id'] = 0; 
	   $artc = $this->model->getArticleSelector('op_oarticle_{num}', $uu['article_id']); 
	   $code .= $artc; 
	  */
	  
	    $code .= '<select name="ttracking_{num}"  >'; 
		foreach ($this->trackingfiles as $k3=>$s3) 
		{
		   
		   $enabled = $this->modelT->isPluginEnabled($s3, $this->configT); 
		   if (!$enabled) continue; 
		   if ($s2 === $s3) $sel = ' selected="selected" '; 
		   else $sel = ''; 
		   
		   $code .= '<option '.$sel.'value="'.$s3.'">'.$s3.'</option>'; 
		}
		$code .= '</select>'; 
		  
		 $code .=' </td><td>
		    <select style="margin: 0;" name="top_olang_{num}">
	      <option ';
		  if (empty($uu['language'])) 
		  {
		  $code .= ' selected="selected" '; 
		  $uu['language'] = null; 
		  }
$code .= ' value="">- '.JText::_('COM_ONEPAGE_ANY').' -</option> '; 
		foreach($this->codes as $p)
		{
		 $code .= '
		 <option value="'.$p['lang_code'].'" '; 
		 if ($p['lang_code']==$uu['language'])
		 {		 	
		 
		 $code .=		 'selected="selected" '; 
		 }
		 $code .= '>'; 
		 $code .= $p['lang_code'].'</option>'; 
		 
		}
		$code .= '

	   </select>
	   </td>';
	   
	    $code .=' <td>
		    <select style="margin: 0;" name="top_omode_{num}">';
		  if (empty($uu['mode'])) 
		  {

		  $uu['mode'] = 0; 
		  }

		$modes = array(0,1,2); 
		foreach($modes as $p)
		{
		 $code .= '
		 <option value="'.$p.'" '; 
		 if ($p==$uu['mode']) 
		 $code .=		 'selected="selected" '; 
		 $code .= '>'; 
		 $code .= JText::_('COM_ONEPAGE_TY_MODE_'.$p).'</option>'; 
		 
		}
		$code .= '

	   </select>
	   </td>
	   ';
	   
		  
		   $code .='
		   <td>
		   <a class="button btn btn-primary" href="#" onclick="javascript: return op_new_line2(topc_line_ty, \'ttable_thankyou_config\' );" >'.JText::_('JLIB_HTML_BATCH_COPY').'...</a><br />
		   <a class="button btn btn-danger" style="" href="#" onclick="javascript: return op_remove_line2(\'{num}\', \'ip_shopper_group\' );" >'.JText::_('COM_ONEPAGE_REMOVE').'</a>
		   </td>
		  '; 
		  $jscode = $code; 
		  
		  $code_sg = '<tr id="rowid2_'.$num.'">'.str_replace('{num}', $num, $code).'</tr>'; 
		 $num++;
		  unset($code); 
		  echo $code_sg; 
		  
		  
		  }
		  
		  $code_sg = $jscode; 
		  $code_sg = trim($code_sg); 
		  $code_sg = str_replace("\r\r\n", "", $code_sg); 
		  $code_sg = str_replace("\r\n", "", $code_sg); 
		  $code_sg = str_replace("\n", "{br}", $code_sg); 
		  $code_sg = str_replace("<", '&lt;', $code_sg); 
		  $code_sg = str_replace(">", '&lg;', $code_sg); 
		  $document->addScriptDeclaration(' 
//<![CDATA[		  
var line_iterT2 = '.$num.'; 
var topc_line_ty = \''.str_replace("'", "\'", $code_sg).'\';
//]]>
'); 

		  
		//end tracking per payment
		
		
		?>
		
		
		
		</table>
        </fieldset>
	<script>
	 function changeNumbering(el)
	 {
		 if (el.options[el.selectedIndex].value == 'new')
			 window.location = 'index.php?option=com_onepage&view=numbering'; 
	 }
	</script>
		
		<fieldset><table class="admintable table table-striped" style="width: 100%;">
		<legend><?php echo JText::_('COM_ONEPAGE_NUMBERING'); ?></legend>
		<tr >
	    <td class="key">
	     <label for="order_numbering"><?php echo JText::_('COM_ONEPAGE_NUMBERING_ORDER');


		 ?></label> 
	    </td>
		
		<td>
		  <select name="order_numbering" id="order_numbering" onchange="javascript: return changeNumbering(this);">
			<option value=""><?php echo JText::_('COM_ONEPAGE_SHIPPING_ADDRESS_DEFAULT_COUNTRY_SELECT'); ?></option>
			<?php 
			  require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php');   
			  
			  //if (OPCmini::tableExists('onepage_agendas'))
			  {
				  require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'numbering.php');
				  $JModelNumbering = new JModelNumbering; 
				  $agendas = $JModelNumbering->getAgendas(); 
				  if (!empty($agendas))
				  foreach ($agendas as $k=>$row)
				  {
					  ?><option <?php 
					  if ((!empty($order_numbering)) && ($order_numbering == $row['id'])) echo ' selected="selected" '; 
					  ?>value="<?php echo $row['id']; ?>" ><?php echo $row['name']; ?></option>
					  <?php
				  }
			  }
			  
			  
			  
			?>
			<option value="-1" <?php if ($order_numbering === -1) echo ' selected="selected" '; ?> ><?php echo JText::_('COM_ONEPAGE_NUMBERING_ORDERID'); ?></option>
			<option value="new"><?php echo JText::_('COM_ONEPAGE_UTILS_NEW').'...'; ?></option>
		  </select>
		 </td>
		 </tr>
		 
		 
		 <tr >
	    <td class="key">
	     <label for="invoice_numbering"><?php echo JText::_('COM_ONEPAGE_NUMBERING_INVOICE');


		 ?></label> 
	    </td>
		<td>
		  <select name="invoice_numbering" id="invoice_numbering" onchange="javascript: return changeNumbering(this);">
			<option value=""><?php echo JText::_('COM_ONEPAGE_SHIPPING_ADDRESS_DEFAULT_COUNTRY_SELECT'); ?></option>
			<?php 
			  require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php');   
			  
			  if (OPCmini::tableExists('onepage_agendas'))
			  {
				  require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'numbering.php');
				  $JModelNumbering = new JModelNumbering; 
				  $agendas = $JModelNumbering->getAgendas(); 
				  foreach ($agendas as $k=>$row)
				  {
					  ?><option <?php 
					  if ((!empty($invoice_numbering)) && ($invoice_numbering == $row['id'])) echo ' selected="selected" '; 
					  ?>value="<?php echo $row['id']; ?>" ><?php echo $row['name']; ?></option>
					  <?php
				  }
			  }
			?>
			<option value="-1"><?php echo JText::_('COM_ONEPAGE_NUMBERING_ORDERID'); ?></option>
			<option value="new"><?php echo JText::_('COM_ONEPAGE_UTILS_NEW').'...'; ?></option>
		  </select>
		 </td>
		 </tr>
		 
		
		</table></fieldset>
		
		
        <?php 
         echo $pane->endPanel();
		
        echo $pane->startPanel(JText::_('COM_ONEPAGE_LANGUAGE_PANEL'), 'panel9e4'); ?>
        
        <fieldset class="adminform" style="width: 100%;">
        <legend><?php echo JText::_('COM_ONEPAGE_LANGUAGE'); ?></legend>
		<div id="opc_language_editor">
		  <table class="admintable table table-striped" id="comeshere8" style="width: 100%;">
	    
		<tr>
	    <td class="key">
	     <label for="tr_type"><?php echo JText::_('COM_ONEPAGE_LANGUAGE_TYPE'); ?></label> 
	    </td>
		<td>
		  <select name="tr_type" id="tr_ext_id" onchange="javascript: return ext_chageList(this);">
			<option value="site"><?php echo JText::_('COM_ONEPAGE_LANGUAGE_TYPE_SITE'); ?></option>
			<option value="administrator"><?php echo JText::_('COM_ONEPAGE_LANGUAGE_TYPE_ADMIN'); ?></option>
		  </select>
		 </td>
		 </tr>
		<tr>
	    <td class="key">
	     <label for="tr_ext_id"><?php echo JText::_('COM_ONEPAGE_LANGUAGE_EXT'); ?></label> 
	    </td>
		<td>
		  <select name="tr_ext_site" id="tr_ext_site">
		  <?php
		    foreach($this->exts as $key=>$xt)
			 {
			   echo '<option value="'.$key.'"';
			   if (strpos($key, 'com_onepage.ini')!==false) echo ' selected="selected" '; 
			   echo '>'.$key.'</option>'; 
			 }
		  ?>
		  </select>
		  
		   <select name="tr_ext_administrator" id="tr_ext_administrator" style="display: none;">
		  <?php
		    foreach($this->adminxts as $key=>$xt)
			 {
			   echo '<option value="'.$key.'"';
			   if (strpos($key, 'com_onepage.ini')!==false) echo ' selected="selected" '; 
			   echo '>'.$key.'</option>'; 
			 }
		  ?>
		  </select>
		  
		 </td>
		 </tr>
		 <tr>
		 <td class="key">
	     <label for="tr_fromlang_id"><?php echo JText::_('COM_ONEPAGE_LANGUAGE_FROMLANG'); ?></label> 
	     </td>
		 <td>
		  <select name="tr_fromlang" id="tr_fromlang_id">
		  <?php
		    foreach($this->extlangs as $key=>$xt)
			 {
			   echo '<option value="'.$key.'"';
			   if (strpos($key, 'en-GB')!==false) echo ' selected="selected" '; 
			   echo '>'.$key.'</option>'; 
			 }
		  ?>
		  </select>
		 </td>
		 </tr>
		 
		 <tr>
		 <td class="key">
	     <label for="tr_tolang_id"><?php echo JText::_('COM_ONEPAGE_LANGUAGE_TOLANG'); ?></label> 
	     </td>
		 <td>
		  
		  <select name="tr_tolang" id="tr_tolang_id">
		  <?php
		  
			$config = JFactory::getConfig();
			if (method_exists($config, 'getValue'))
			$flang = $config->getValue('config.language');
			else 
			$flang = $config->get('language');
			
		    foreach($this->extlangs as $key=>$xt)
			 {
			   echo '<option value="'.$key.'"';
			   if (stripos($key, $flang )!==false) echo ' selected="selected" '; 
			   echo '>'.$key.'</option>'; 
			 }
		  ?>
		  </select>
		</td>
		</tr>
		<tr>
		 <td>
		   <input type="button" class="btn btn-small btn-success" value="<?php echo JText::_('COM_ONEPAGE_LANGUAGE_BTN_VALUE'); ?>" onclick="javascript: submitbutton('langedit');" />
		 </td>
		</tr>
		<?php if (!empty($this->langerr))
		{
		 ?>
		  <tr>
		  <td>
		  <?php echo JText::_('COM_ONEPAGE_LANGUAGE_CREATE_COPY'); ?><br />
		  <?php
		  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		  OPCmini::setVMLANG(); 
		
		  foreach ($this->langerr as $mi)
		   {
		     $orig = str_replace(VMLANG, 'en_gb', $mi); 
		     echo 'Copy table <b>'.$orig.'</b> to <b>'.$mi.'</b><br />'; 
		   }
		  ?>
		  </td>
		  </tr>
		  <tr>
		 <td>
		   <input type="button" class="btn btn-small btn-success" value="YES, make the copy of the tables above" onclick="javascript: submitbutton('langcopy');" />
		 </td>
		</tr>
		 <?php
		}
		?>
		</table>
		  
		  
		  
		</div>
    
	
	</fieldset>
        <?php 
        echo $pane->endPanel();
		
   
		
	echo $pane->startPanel(JText::_('COM_ONEPAGE_OPC_EXTENSIONS_PANEL'), 'panel6g9');
?>
<fieldset><legend><?php echo JText::_('COM_ONEPAGE_OPC_EXTENSIONS'); ?></legend>
	<?php 
	/*
	if (empty($this->exthtml)) echo JText::_('COM_ONEPAGE_OPC_EXTENSIONS_NOEXT'); 
	else
	echo $this->exthtml; 
	*/
	?><input type="hidden" name="ext_id" value="" rel="" id="ext_id" /><table class="admintable table table-striped" id="extension_list">
	<?php
	if (!empty($this->opcexts))
	 {
	   $li = 0; 
	   foreach ($this->opcexts as $ext)
	    {
		
		  echo '<tr>'; 
		  echo '<td>'.$ext['name'].'</td>';  
		  echo '<td>'.$ext['description'].'</td>'; 
		  if (!empty($ext['data']))
		  {
		  if (!empty($ext['link']))
		  		  echo '<td><div style="color: green;" href="'.$ext['link'].'">'.JText::_('COM_ONEPAGE_INSTALLED').'<input type="button" class="btn btn-small btn-success"  onclick="javascript: installExt(\''.$li.'\');" value="'.JText::_('COM_ONEPAGE_UPDATE_OPCEXTENSION').'" /></div></td>'; 
		  else
echo '<td style="color: green;">'.JText::_('COM_ONEPAGE_INSTALLED').'<input type="button" class="btn btn-small btn-success"  onclick="javascript: installExt(\''.$li.'\');" value="'.JText::_('COM_ONEPAGE_UPDATE_OPCEXTENSION').'" /></td>'; 
		  //
		  }
		  else
		   {
		   echo '<td>'; 
		   echo '<input type="button" class="btn btn-small btn-success"  onclick="javascript: installExt(\''.$li.'\');" value="'.JText::_('COM_ONEPAGE_INSTALL_OPCEXTENSION').'" />';
		   echo '&nbsp;</td>'; 		   
		   }
		  echo '</tr>'; 
		
		
		 $li++; 
		}
	 }
	 
	?></table>
</fieldset>

<?php
	echo $pane->endPanel();
	echo $pane->startPanel(JText::_('COM_ONEPAGE_NOTES_PANEL'), 'panel6f8');
	?>
	<fieldset><legend><?php echo JText::_('COM_ONEPAGE_NOTES'); ?></legend>
	<h3><?php echo JText::_('COM_ONEPAGE_NOTES_COMMON'); ?></h3> 
	<p><?php echo JText::_('COM_ONEPAGE_NOTES_COMMON_DESC'); ?></p>
	<h3><?php echo JText::_('COM_ONEPAGE_NOTES_SPEED'); ?></h3>
	<p><?php echo JText::_('COM_ONEPAGE_NOTES_SPEED_DESC'); ?></p>
	<ul>
	 <li><?php echo JText::_('COM_ONEPAGE_NOTES_SPEED_OPT1'); ?></li>
	 <li><?php echo JText::_('COM_ONEPAGE_NOTES_SPEED_OPT2'); ?></li>
	 <li><?php echo JText::_('COM_ONEPAGE_NOTES_SPEED_OPT3'); ?></li>
	 
	</ul>
	
	</fieldset>
	<?php
	echo $pane->endPanel(); 
	
	// feature removed: 
	if (false)
	{
	
	echo $pane->startPanel(JText::_('COM_ONEPAGE_OPC_CACHING_PANEL'), 'panel6i0');
	?>
		<fieldset><legend><?php echo JText::_('COM_ONEPAGE_OPC_CACHING'); ?></legend>
	<h3><?php echo JText::_('COM_ONEPAGE_OPC_CACHING_HEAD'); ?></h3> 
	<p><?php echo JText::_('COM_ONEPAGE_OPC_CACHING_DESC'); ?></p>
	
	<br />
	<table class="admintable table table-striped" style="width: 100%;">
	<?php 
	$file = JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmshipment'.DIRECTORY_SEPARATOR.'alatak_usps'.DIRECTORY_SEPARATOR.'alatak_usps.php'; 
    if (file_exists($file))
	{
    $x = file_get_contents($file); 
	if (stripos($x, 'self::$uspsCache')===false)
	if (file_exists($file))
	{
		?>
		<tr>
	    <td class="key">
	     <label for="usps_cache"><?php echo JText::_('COM_ONEPAGE_OPC_CACHING_USPS'); ?></label> 
	    </td>
	    <td>

	<input type="button" class="btn btn-small btn-success"  id="usps_cache" name="usps_cache"  onclick="javascript: submitbutton('<?php
		$file = JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmshipment'.DIRECTORY_SEPARATOR.'alatak_usps'.DIRECTORY_SEPARATOR.'alatak_usps.php'; 

		$file2 = str_replace('.php', '_opc_backup.php', $file); 			
		
		if (file_exists($file2))
		{
			echo 'removepatchusps'; 
			$uspspatch = true; 
		}
		else
			echo 'patchusps';
	
	?>');" value="<?php if (empty($uspspatch)) echo JText::_('COM_ONEPAGE_OPC_CACHING_USPS_PATCH'); else echo JText::_('COM_ONEPAGE_OPC_CACHING_USPS_REMOVE'); ?>" /></td><td><?php echo JText::_('COM_ONEPAGE_OPC_CACHING_USPS_DESC'); ?></td>
		
		</tr>
		<?php 
		}
		}
		?>

	<tr>
	    <td class="key">
	     <label for="opc_calc_cache"><?php echo JText::_('COM_ONEPAGE_OPC_CACHING_CALC'); ?></label> 
	    </td>
	    <td>

	<input type="checkbox" value="1" id="opc_request_cache" name="opc_request_cache" <?php if (!empty($opc_request_cache)) echo ' checked="checked" '; ?> /></td><td><?php echo JText::_('COM_ONEPAGE_OPC_CACHING_CALC_DESC'); ?></td>
		
		</tr>

	  <tr>
	    <td class="key">
	     <label for="opc_calc_cache"><?php echo JText::_('COM_ONEPAGE_OPC_CACHING_PERMAMENT'); ?></label> 
	    </td>
	    <td>

	<input type="checkbox" value="1" id="opc_calc_cache" name="opc_calc_cache" <?php if (!empty($opc_calc_cache)) echo ' checked="checked" '; ?> /></td><td><?php echo JText::_('COM_ONEPAGE_OPC_CACHING_PERMAMENT_DESC'); ?></td>
		
		</tr>
	</table> 
	</fieldset>
	<?php 
    echo $pane->endPanel(); 
	}
	
	echo $pane->endPane();
		?>
  </form>

<?php
echo ob_get_clean();
function checkFile($file, $file2=null)
{
 $pi = pathinfo($file);
 if (!empty($pi['extension']))
  $name = str_replace('.'.$pi['extension'], '', $pi['basename']);
 else $name = $pi['basename']; 

 $orig = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'vm_files'.DIRECTORY_SEPARATOR.$pi['basename'];
 if (!empty($file2)) $orig = $file2;
 
 if (!file_exists($orig)) return 'Cannot Check';
 if (!file_exists($orig) && (file_exists($file))) return 'OK';
 if (file_exists($file))
 {
  
  $d1 = filemtime($file);
  $d2 = filemtime($orig);
  if ($d2>$d1)
  { 
  $d1 = hash_file('md5',$file);
  $d2 = hash_file('md5',$orig);
  if ($d1 != $d2 )
  {
   if (strpos($file, 'templates')!==false)
   return 'Template will not be overwritten'.retI($name, 'template');
   else
   return 'Upgrade'.retI($name, 'install');
  }
  else return 'OK'.retI($name, 'ok');
  
  }
  
  else
  return 'OK'.retI($name, 'ok');; 
 }
 else return 'File not found'.retI($name, 'install');;
}

function retI($name, $task)
{
 return '<input type="hidden" name="'.$name.'" value="'.$task.'" />';
}

// functions to parse variables
function parseP($hidep)
{
 $hidep = str_replace(' ', '', $hidep);
 $arr = explode (',', $hidep);
 return $arr;
}
// returns true if an payment id is there
function isThere($id, $hidep)
{


 
 $hidep = ','.$hidep.',';
 if (strpos($hidep, ','.$id.',') !== false) return true;
 if (strpos($hidep, ','.$id.'/') !== false) return true;
 return false;
}
// for an payment id get a default payment id 
function getDefP($id, $hidep)
{
 $hidep = ','.$hidep.',';
 if (strpos($hidep, '/'.$id.',') !== false) return true;
 return false;
 
}
$_SESSION['endmem'] = memory_get_usage(true); 
$mem =  $_SESSION['endmem'] - $_SESSION['startmem'];
//echo 'Cm: '.$mem.' All:'.$_SESSION['endmem'];
$document = JFactory::getDocument();
 
// Add Javascript
$js = '
//<![CDATA[
		if ((typeof window != \'undefined\') && (typeof window.addEvent != \'undefined\'))
			   {
			   window.addEvent(\'domready\', function() {
			      ';
				  if (!OPCJ3)
				  $js .= '
			      initRows(); 
				  op_checkHt();
				  '; 
				  
$js .= '				  
				   '; 
if (empty($disable_check))
$js .= '
				if (typeof getOPCExts != \'undefined\')
				  getOPCExts(); 
'; 
$js .= '				  
			    });
			   }
			   else
			   {
			     if(window.addEventListener){ // Mozilla, Netscape, Firefox
			window.addEventListener("load", function(){ 
			initRows();  
			op_checkHt(); 
			}, false);
			 } else { // IE
			window.attachEvent("onload", function(){ 
			op_checkHt(); 
			initRows();  
			});
			 }
			   }
			 
    
//]]>
';
$document->addScriptDeclaration($js); 

echo '</div></div>'; 


