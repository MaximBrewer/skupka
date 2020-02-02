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

class OPCJavascript
{

public static function getJavascript(&$ref, &$OPCloader, $isexpress=false, $action_url='index.php', $option='com_virtuemart', $task='checkout')
 {
   
   self::loadJavascriptFiles($ref, $OPCloader); 
   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'ajaxhelper.php'); 
   
   $bhelper = new basketHelper; 

   //$extHelper = new opExtension();
   //$extHelper->runExt('before');

   include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
   
   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
   
  // $ccjs = "\n".' var op_general_error = "'.OPCmini::slash(JText->_('CONTACT_FORM_NC')).'"; '."\n";
  // $ccjs .= ' var op_cca = "~';
   // COM_VIRTUEMART_ORDER_PRINT_PAYMENT
   
    $logged = OPCloader::logged($ref->cart);

	$user = JFactory::getUser(); 
	
	if ($user->id > 0)
	$logged_in_joomla = true; 
	else 
	$logged_in_joomla = false; 

	// check if klarna enabled
	 // let's include klarna from loadScriptAndCss: 
$db = JFactory::getDBO(); 
$q = "select `published` from #__virtuemart_paymentmethods where payment_element = 'klarna' limit 0,1"; 
$db->setQuery($q); 
$enabled = $db->loadResult();


if (!empty($enabled))
{
		if (file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'klarna'.DIRECTORY_SEPARATOR.'klarna.php'))
			$path = 'plugins/vmpayment/klarna';
	    else 
				$path = 'plugins/vmpayment';
 		$assetsPath = $path . '/klarna/assets/';
		JHTMLOPC::stylesheet ('style.css', $assetsPath . 'css/', FALSE);
		JHTMLOPC::stylesheet ('klarna.css', $assetsPath . 'css/', FALSE);
		JHTMLOPC::script ('klarna_general.js', $assetsPath . 'js/', FALSE);
		if (version_compare(JVERSION,'3.4.0','ge')) {
		JHTML::script ('https://static.klarna.com/external/js/klarnaConsentNew.js');
		}
		else
		JHTMLOPC::script ('klarnaConsentNew.js', 'https://static.klarna.com/external/js/', FALSE);
	
		$document = JFactory::getDocument ();
		
		$document->addScriptDeclaration ('
		 klarna.ajaxPath = "' . JURI::root () . '/index.php?option=com_virtuemart&view=plugin&vmtype=vmpayment&name=klarna";
	');
}
 // end

	// end
	
	 
	 
   	$extJs = " var shipconf = []; var payconf = []; "."\n";
	ob_start(); ?>
	
	
	if (typeof sessMin == 'undefined') var sessMin = 15; 
		
		if ((typeof jQuery != 'undefined') && (typeof jQuery.fn.chosen == 'undefined')) jQuery.fn.chosen = function() {;}; 
		if ((typeof jQuery != 'undefined') && (typeof jQuery.fn.facebox == 'undefined')) jQuery.fn.facebox = function() {;}; 
		if ((typeof jQuery != 'undefined') && (typeof jQuery.fn.fancybox == 'undefined')) jQuery.fn.fancybox = function() {;}; 
		
	
	<?php
	$extJs .= ob_get_clean(); 
	
	$virtuemart_currency_id = OPCloader::getCurrency($ref->cart); 
	$extJs .= " var op_virtuemart_currency_id = '".$virtuemart_currency_id."'; "; 
	
	//testing: 
	
	if ((!empty($opc_payment_refresh)) || ($isexpress))
	$extJs .= " var opc_payment_refresh = true; "; 
	else
	$extJs .= " var opc_payment_refresh = false; "; 
	
	
	$zero_p = OPCConfig::get('default_payment_zero_total', 0);  
    $zero_p = (int)$zero_p; 			 
	
	
	if (!empty($default_payment_zero_total))
	$extJs .= " var default_payment_zero_total = ".$zero_p.";"; 
	else
	$extJs .= " var default_payment_zero_total = ".$zero_p.";"; 

	$force_zero_paymentmethod = OPCConfig::get('force_zero_paymentmethod', 0);  
	
	if (!empty($force_zero_paymentmethod))
	$extJs .= " var force_zero_paymentmethod = true;"; 
	else
	$extJs .= " var force_zero_paymentmethod = false;"; 
	
	//force_zero_paymentmethod
	
	if (!empty($opc_confirm_dialog))
	{
		$extJs .= ' var opc_confirm_dialog = true; '; 
		
	    OPCJavascript::loadJquery();  
		
		
	 
	 
	 
	 //JHTMLOPC::script('jqueryopc-1.11.2.min.js', 'components/com_onepage/themes/extra/jquery-ui/'); 
	 
	 //JHTMLOPC::script('jquery-ui.min.js', 'components/com_onepage/themes/extra/jquery-ui/'); 
	}
	else {
		$extJs .= ' var opc_confirm_dialog = false; '; 
	}
	
	$disable_shipto_per_shipping = OPCconfig::get('disable_shipto_per_shipping', ''); 
	
	if (!empty($disable_shipto_per_shipping)) {
		JHTMLOPC::script('disableshipto.js', 'components/com_onepage/assets/js/', false);
	}
	
	$extJs .= ' var disable_shipto_per_shipping = \''.$disable_shipto_per_shipping.'\'; '; 
	
	if (!empty($op_dontrefresh_shipping))
	{
	$extJs .= " var op_dontrefresh_shipping = true; "; 
	}
	else
	{
	$extJs .= " var op_dontrefresh_shipping = false; "; 
	}
	
	if (!empty($opc_dynamic_lines))
	$extJs .= " var opc_dynamic_lines = true; "; 
	else
	$extJs .= " var opc_dynamic_lines = false; "; 
	
	$extJs .= " var opc_default_option = '".$option."'; "; 
	$extJs .= " var opc_default_task = '".$task."'; "; 
	
	if (method_exists('VmConfig', 'loadJLang'))
	VmConfig::loadJLang('com_virtuemart',true);



   $extJs .= "
		
		var ccErrors = new Array ();
		ccErrors [0] =  '" . addslashes( JText::_('COM_VIRTUEMART_CREDIT_CARD_UNKNOWN_TYPE') ). "';
		ccErrors [1] =  '" . addslashes( JText::_("COM_VIRTUEMART_CREDIT_CARD_NO_NUMBER") ). "';
		ccErrors [2] =  '" . addslashes( JText::_('COM_VIRTUEMART_CREDIT_CARD_INVALID_FORMAT')) . "';
		ccErrors [3] =  '" . addslashes( JText::_('COM_VIRTUEMART_CREDIT_CARD_INVALID_NUMBER')) . "';
		ccErrors [4] =  '" . addslashes( JText::_('COM_VIRTUEMART_CREDIT_CARD_WRONG_DIGIT')) . "';
		ccErrors [5] =  '" . addslashes( JText::_('COM_VIRTUEMART_CREDIT_CARD_INVALID_EXPIRE_DATE')) . "';

		";
		
	     $front = 'components/com_onepage/assets/';
	     JHTMLOPC::script('vmcreditcard.js', $front.'js/'); 
	
	$hasdatepicker = false; 
	$default = new stdClass(); 
	 $default->enabled = false; 
     $config = OPCconfig::getValue('opc_delivery_date', '', 0, $default, true); 
	 if (!empty($config->enabled))
	  {
	     //load VM's scripts 
	     
		 
		 $front = 'components/com_virtuemart/assets/';
	     // we have date picker here
		 //vmJsApi::js ('jquery.ui.core',FALSE,'',TRUE);
		 /*
		 if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.$front.'js/jquery.ui.core.min.js'))
		 JHTMLOPC::script('jquery.ui.core.min.js', $front.'js/'); 
		 else
		 JHTMLOPC::script('jquery.ui.core.js', $front.'js/'); 
		 //vmJsApi::js ('jquery.ui.datepicker',FALSE,'',TRUE);
		 
		 if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.$front.'js/jquery.ui.datepicker.min.js'))
		 JHTMLOPC::script('jquery.ui.datepicker.min.js', $front.'js/'); 
		 else
		 JHTMLOPC::script('jquery.ui.datepicker.js', $front.'js/'); 
		 
		 //vmJsApi::css ('jquery.ui.all',$front.'css/ui' ) ;
		 JHTMLOPC::stylesheet('jquery.ui.all.css', $front.'css/ui/'); 
		 
		 */
		 
		 
		
		OPCJavascript::loadJquery();  
		 
		 
		 $lg = JFactory::getLanguage();
		  $lang = $lg->getTag();

		  $existingLang = array("af","ar","ar-DZ","az","bg","bs","ca","cs","da","de","el","en-AU","en-GB","en-NZ","eo","es","et","eu","fa","fi","fo","fr","fr-CH","gl","he","hr","hu","hy","id","is","it","ja","ko","kz","lt","lv","ml","ms","nl","no","pl","pt","pt-BR","rm","ro","ru","sk","sl","sq","sr","sr-SR","sv","ta","th","tj","tr","uk","vi","zh-CN","zh-HK","zh-TW");
		  if (!in_array ($lang, $existingLang)) {
			 $lang = substr ($lang, 0, 2);
		  }
		  elseif (!in_array ($lang, $existingLang)) {
			 $lang = "en-GB";
		  }
		  //vmJsApi::js ('jquery.ui.datepicker-'.$lang, $front.'js/i18n' ) ;
		  
		  JHTMLOPC::script('jquery.ui.datepicker-'.$lang.'.js', $front.'js/i18n/'); 
		  
		  
	      
		  
		  
		  
		  
		  $year = date('Y'); 
		  $year_n = $year + 1; 
		  
		  $jsDateFormat = $config->format; 
		  $jsDateFormat2 = $config->storeformat; 
		  
		  $yearRange = $year.':'.$year_n; 
		  $document = JFactory::getDocument();
		  $offset = (int)$config->offset; 
		  $hlds = array(); 
		  $extJs .= ' var opc_datepicker_hollidays = ['; 
		  if (!empty($config->hollidays))
		   {
		     $f = 0; 
		     $a = explode(',', $config->hollidays); 
			 foreach ($a as $dt)
			  {
			     $dt = trim($dt);
				 
				 $hlds[$dt] = $dt; 
				
				  {
				   if ($f > 0) $extJs .= ','; 
				   $extJs .= '"'.$dt.'"'; 
				  }
					 $f++; 		
				 
			  }
		   }
		  $extJs .= ']; '; 
	
		JHTMLOPC::script('opcdatepicker.js', 'components/com_onepage/assets/js/', false);
	
	
	//$offset = 10; 
	//echo ' offset: '.$offset."<br />"; 
	
	//let's check offset: 
	$max = $offset+31; 
	
	$orig_offset = $offset; 
	$additional_offset = 0; 
	
	$nf_test = $offset; 
	
	for ($i=1;  $i<=$max; $i++)
	 {
		 
		
		 
	     $startTest = time()+($i * 24 * 60 * 60); 
	     $dow = date('w', $startTest); 
		 $key = 'day_'.$dow; 
		 if (!empty($config->$key)) 
		 {
			// echo $dow.' disabled...'."<br />\n"; 
			 $additional_offset++; 
			 $offset++; 
			 continue; 
		 }
		 
		 $df = date('Y-n-j', $startTest); 
		 //echo $df."<br />\n"; 
		 if (in_array($df, $hlds)) 
		 {
			// echo $df.' disabled...'."<br />\n"; 
			 $additional_offset++; 
			 $offset++; 
			 continue; 
		 }
		 
		
		
		  
		  if ($i < ($orig_offset+$additional_offset))
		 {
			 //$offset++; 
			// echo 'next day '.$i.' offset '.$offset.' smaller than orig and additional'.$additional_offset."<br />"; 
			 continue; 
		 }
		 
		  $check = $i + $additional_offset + 1; 
		  
		 if ($check - $additional_offset >= $orig_offset)
		 {
			// echo ' stop offset: '.$offset.' additinoal offset: '.$additional_offset.' orig offset: '.$orig_offset."<br />\n"; 
			break;
		 }
		 
		 
		/*
		$nf_test--; 
		if ((empty($nf_test)))
		{
		
		// $offset = $i; 
		 break; 
		}
		*/
		
		$offset++; 
		//echo 'adding offset '."<br />"; 
	
		
		 
	 }
	
	
	 
	 
	 
	$maxDate = (int)$config->offsetmax; 
	if (empty($maxDate)) $maxDate = 365; 
	
	$extJs .= ' var opc_datepicker_disableddays = ['; 
	$days = array(
	1 => JText::_('MONDAY'), 
	2 => JText::_('TUESDAY'), 
	3 => JText::_('WEDNESDAY'), 
	4 => JText::_('THURSDAY'), 
	5 => JText::_('FRIDAY'), 
	6 => JText::_('SATURDAY'), 
	0 => JText::_('SUNDAY'), 
	);
	
	 $document = JFactory::getDocument();
 //$document->addScriptDeclaration($javascript); 
 $lang = JFactory::getLanguage(); 

   $rtl = 'false'; 
   
if (method_exists($lang, 'isRTL'))
if ($lang->isRTL())
{
  $rtl = 'true'; 
}

  if (!isset($config->firstday)) $config->firstday = 1; 
	
	  $f = 0; 
		  foreach ($days as $i=>$day)
		  {
		     $key = 'day_'.$i;
			 
			 if (!empty($config->$key))
			  {
			     if ($f != 0) $extJs .= ','; 
				 $f++; 
			     $extJs .= (int)$i; 
			  }
		  }
	$extJs .= ']; 

			jQuery(document).ready( function($) {
			  var opcdptest = document.getElementById(\'opc_date_picker\'); 
			  if (opcdptest != null)
			  {
			  var opcdp = jQuery(\'#opc_date_picker\'); 
			  if (typeof opcdp.datepicker != \'undefined\')
			  opcdp.datepicker({
			        minDate:'.$offset.',
					maxDate:'.$maxDate.',
					changeMonth: true,
					beforeShowDay: OPCDatePicker.noSunday,
					changeYear: true,
					yearRange: \''.$yearRange.'\',
					dateFormat:"'.$jsDateFormat.'",
					altField: \'#opc_date_picker_store\',
					altFormat: "'.$jsDateFormat2.'",
					firstDay: '.(int)$config->firstday.',
					isRTL: '.$rtl.'
				});
				
			 
			 }
			
		});
'; 
  if (!empty($config->required))
   {
 
      $extJs .= '
	    if (typeof addOpcTriggerer != \'undefined\')
		 addOpcTriggerer(\'callSubmitFunct\', \'OPCDatePicker.validate\'); 
	  ';
   }

		 $hasdatepicker = true; 
	  }
	
	$opc_recalc_js = OPCconfig::get('opc_recalc_js', false); 
	if (!empty($opc_recalc_js)) {
		$path = 'components/com_onepage/themes/'.$selected_template.'/'; 
		$p2 = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$selected_template.DIRECTORY_SEPARATOR.'payment_shipping_fees.js'; 
		if (file_exists($p2)) {
		  JHTMLOPC::script('payment_shipping_fees.js', $path, false);
	    }
		else
		{
			$path = 'components/com_onepage/themes/extra/default/';
			JHTMLOPC::script('payment_shipping_fees.js', $path, false);
		}
	}
	
	
	$one_or_the_other = OPCconfig::get('one_or_the_other', array()); 
	$one_or_the_other2 = OPCconfig::get('one_or_the_other2', array()); 
	
	if ((!empty($one_or_the_other)) && (!empty($one_or_the_other2))) {
	   $extJs .= " var one_or_the_other = new Array(); ";
	   $extJs .= " var one_or_the_other2 = new Array(); ";
	   
	   JHTMLOPC::script('opc_one_or_the_other.js', 'components/com_onepage/assets/js/', false);
	   
	   foreach ($one_or_the_other as $k=>$v) {
	    if (empty($v)) continue; 
		if (!empty($one_or_the_other2[$k])) {
			$v2 = $one_or_the_other2[$k];
		  $extJs .= "  one_or_the_other[".$k."] = '".OPCmini::slash($v)."'; ";
	      $extJs .= "  one_or_the_other2[".$k."] = '".OPCmini::slash($v2)."'; ";
		}
	   
	   }
	   
	}
	
	
	$default = array(); 
	$opc_payment_isunder = OPCConfig::getValue('opc_config', 'opc_payment_isunder', 0, $default, false, false);
	if ((!empty($opc_payment_isunder))) {
	   $extJs .= " var opc_payment_isunder = new Array(); ";
	   JHTMLOPC::script('opc_payment_isunder.js', 'components/com_onepage/assets/js/', false);
	   
	   foreach ($opc_payment_isunder as $k=>$v) {
	    $v = (int)$v;
		if (!empty($v)) {
		  $extJs .= "  opc_payment_isunder.push(".(int)$v."); ";
		}
	   }
	}
	
	
	
	if (!empty($opc_disable_customer_email)) {
		$extJs .= " var opc_disable_customer_email = true; "; 
	}
	else {
		$extJs .= " var opc_disable_customer_email = false; "; 
	}
	
	if (!empty($opc_debug))
	$extJs .= " var opc_debug = true; "; 
	else
	$extJs .= " var opc_debug = false; "; 
	
	if (!empty($op_customer_shipping))
	$extJs .= " var op_customer_shipping = true; "; 
	else
	$extJs .= " var op_customer_shipping = false; "; 
	
	if ($opc_async)
	$extJs .= " var opc_async = true; "; 
	else
	$extJs .= " var opc_async = false; "; 

	
	if ($payment_inside)
	$extJs .= " var op_payment_inside = true; "; 
	else
	$extJs .= " var op_payment_inside = false; "; 
	
	$extJs .= " var op_logged_in = '".$logged."'; "; 
	$extJs .= " var op_last_payment_extra = null; "; 
	$extJs .= " var op_logged_in_joomla = '".$logged_in_joomla."'; "; 
	$extJs .= ' var op_shipping_div = null; ';
	$extJs .= ' var op_lastq = ""; ';
	$extJs .= ' var op_lastcountry = null; var op_lastcountryst = null; ';
    $extJs .= ' var op_isrunning = false; '; 


	$extJs .= ' var COM_ONEPAGE_CLICK_HERE_TO_REFRESH_SHIPPING = "'.OPCloader::slash(OPCLang::_('COM_ONEPAGE_CLICK_HERE_TO_REFRESH_SHIPPING')).'"; ';
	
	$extJs .= ' var COM_VIRTUEMART_LIST_EMPTY_OPTION = "'.OPCloader::slash(OPCLang::_('COM_VIRTUEMART_LIST_EMPTY_OPTION')).'"; ';
	
	$extJs .= ' var COM_ONEPAGE_PLEASE_WAIT_LOADING = "'.OPCloader::slash(OPCLang::_('COM_ONEPAGE_PLEASE_WAIT_LOADING')).'"; ';
	$theme = JRequest::getVar('opc_theme', ''); 
	$theme = preg_replace("/[^a-zA-Z0-9_]/", "", $theme);
	
	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
     $selected_template = OPCrenderer::getSelectedTemplate();  
	
	if (empty($theme)) $theme = $selected_template; 
	$extJs .= ' var opc_theme = "'.OPCloader::slash($theme).'"; '; 
	$extJs .= ' var NO_PAYMENT_ERROR = "'.OPCloader::slash(JText::_('COM_VIRTUEMART_CART_SELECT_PAYMENT')).'"; ';
	$extJs .= ' var COM_ONEPAGE_MISSING_ONE_OR_THE_OTHER = "'.OPCloader::slash(JText::_('COM_ONEPAGE_MISSING_ONE_OR_THE_OTHER')).'"; ';
	
	$extJs .= ' var JERROR_AN_ERROR_HAS_OCCURRED = "'.OPCloader::slash(OPCLang::_('JERROR_AN_ERROR_HAS_OCCURRED')).'"; ';
	$extJs .= ' var COM_ONEPAGE_PLEASE_WAIT = "'.OPCloader::slash(OPCLang::_('COM_ONEPAGE_PLEASE_WAIT')).'"; ';
	//$extJs .= ' var USERNAMESYNTAXERROR = "'.JText::_('', true).'"; ';
	$extJs .= ' var COM_ONEPAGE_CHOOSE_DESIRED_DELIVERY_DATE_ERROR = "'.OPCloader::slash(OPCLang::_('COM_ONEPAGE_CHOOSE_DESIRED_DELIVERY_DATE_ERROR')).'"; ';
	if (!empty($op_usernameisemail))
	$extJs .= ' var op_usernameisemail = true; '; 
	else 
	$extJs .= ' var op_usernameisemail = false; '; 
	$url = OPCloader::getURL(true); 
	if (!empty($op_loader))
	{
	 
	  $extJs .= ' var op_loader = true; ';
	  
	  			
	}	
	else $extJs .= ' var op_loader = false; ';
	
	$extJs .= ' var op_relative_url = "'.$url.'"; '; 
	
	$full_url = Juri::root(); 
	if (substr($full_url, -1) != '/') $full_url .= '/'; 
	
   //$extJs .= ' var op_loader_img = "'.$full_url.'media/system/images/mootree_loader.gif";';  
   $extJs .= ' var op_loader_img = "'.$full_url.'components/com_onepage/themes/extra/img/loader1.gif";';  
	
	if (!empty($double_email))
     if (!defined('op_doublemail_js'))
      {
        JHTMLOPC::script('doublemail.js', 'components/com_onepage/assets/js/', false);
        define('op_doublemail_js', '1'); 
      }
	
	
	if (!empty($onlyd))
	$extJs .= ' var op_onlydownloadable = "1"; ';
	else $extJs .= ' var op_onlydownloadable = ""; ';

		
	if (!empty($op_last_field))
	$extJs .= ' var op_last_field = true; ';
	else $extJs .= ' var op_last_field = false; ';
	
	$extJs .= ' var op_refresh_html = ""; ';

	if (!empty($no_alerts))
	$extJs .= ' var no_alerts = true; ';
	else
	$extJs .= ' var no_alerts = false; ';
	
	require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	
	$extJs .= " var username_error = '".OPCmini::slash(OPCLang::sprintf('COM_VIRTUEMART_STRING_ERROR_NOT_UNIQUE_NAME', OPCLang::_('COM_VIRTUEMART_USERNAME'))) ."';"; 
		$extJs .= " var email_error = '".OPCmini::slash(OPCLang::sprintf('COM_ONEPAGE_EMAIL_ALREADY_EXISTS', OPCLang::_('COM_VIRTUEMART_USER_FORM_EMAIL'))) ."';"; 
	if (!empty($opc_no_duplicit_username))
	{
		$extJs .= ' var opc_no_duplicit_username = true; ';
	}
	else
	{
		$extJs .= ' var opc_no_duplicit_username = false; ';
	}

   if (!empty($opc_no_duplicit_email))
	{
		$extJs .= ' var opc_no_duplicit_email = true; ';
	}
	else
	{
		$extJs .= ' var opc_no_duplicit_email = false; ';
	}

	
	$extJs .= ' var last_username_check = true; ';
	$extJs .= ' var last_email_check = true; ';
	// stAn mod for OPC2
	/*
	if (!empty($op_delay_ship))
	$extJs .= " var op_delay = true; ";
	else $extJs .= " var op_delay = false; ";
	*/


	if (!empty($op_delay_ship))
	$extJs .= " var op_delay = false; ";
	else $extJs .= " var op_delay = false; ";

	
	if (empty($last_ship2_field)) $last_ship2_field = ''; 
	if (empty($last_ship_field)) $last_ship_field = ''; 
	
	$extJs .= " var op_last1 = '".OPCmini::slash($last_ship_field)."'; ";
	$extJs .= " var op_last2 = '".OPCmini::slash($last_ship2_field)."'; ";

	/*
	$url = JURI::root(true); 
	if (empty($url)) $url = '/'; 
	if (substr($url, -1)!=='/') $url .= '/'; 
	*/
	$actionurl = $url.'index.php'; 
 if(version_compare(JVERSION,'2.5.0','ge')) {
	$extJs .= " var op_com_user = 'com_users'; "; 
	$extJs .= " var op_com_user_task = 'user.login'; "; 
	
	$extJs .= " var op_com_user_action = '".$actionurl."?option=com_users&task=user.login&controller=user'; "; 
	$extJs .= " var op_com_user_action_logout = '".$actionurl."?option=com_users&task=user.logout&controller=user'; "; 
	$extJs .= " var op_com_user_task_logout = 'user.logout'; "; 
  
 }
 else
 if(version_compare(JVERSION,'1.7.0','ge')) {
	$extJs .= " var op_com_user = 'com_users'; "; 
	$extJs .= " var op_com_user_task = 'user.login'; "; 
	$extJs .= " var op_com_user_action = '".$actionurl."?option=com_users&task=user.login&controller=user'; "; 
	$extJs .= " var op_com_user_action_logout = '".$actionurl."?option=com_users&task=user.logout&controller=user'; "; 
	$extJs .= " var op_com_user_task_logout = 'user.logout'; "; 


 // Joomla! 1.7 code here
} elseif(version_compare(JVERSION,'1.6.0','ge')) {
// Joomla! 1.6 code here
} else {	
	$extJs .= " var op_com_user = 'com_user'; "; 
	$extJs .= " var op_com_user_task = 'login'; "; 
	$extJs .= " var op_com_user_action = '".$actionurl."?option=com_user&task=login'; "; 
	$extJs .= " var op_com_user_action_logout = '".$actionurl."?option=com_user&task=logout'; "; 
	$extJs .= " var op_com_user_task_logout = 'logout'; "; 

	}
	
	$op_autosubmit = false;
	//$extHelper->runExt('autosubmit', '', '', $op_autosubmit);
	
	
	$extJs .= " var op_userfields_named = new Array(); ";
	if (!empty(OPCloader::$fields_names))
	 {
	   foreach (OPCloader::$fields_names as $key=>$val)
	    {
		  $extJs .= ' op_userfields_named[\''.OPCmini::slash($key).'\'] = \''.OPCmini::slash($val).'\'; ';  
		}
	 }
	$extJs .= " "; 
	// let's create all fields here
	
	if (!class_exists('VirtueMartCart'))
		require(JPATH_VM_SITE .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'cart.php');
		if (!isset($ref->cart))
	    $ref->cart = $cart = VirtueMartCart::getCart();
	
	{
	$extJs .= " var op_userfields = new Array("; 
	
	// updated on VM2.0.26D:
	/*
	if (!isset($ref->cart->STaddress)) $ref->cart->STaddress = array(); 
	if (!isset($ref->cart->BTaddress)) $ref->cart->BTaddress = array(); 
	$ref->cart->prepareAddressDataInCart('BTaddress', 0);
	$ref->cart->prepareAddressDataInCart('STaddress', 0);
	
	//$ref->cart->prepareAddressDataInCart('BT', 0);
	//$ref->cart->prepareAddressDataInCart('ST', 0);
	*/
	//$userFieldsST = $ref->cart->STaddress;
	$userFieldsST = OPCloader::getUserFields('ST', $ref->cart); 
	//$userFieldsBT = $ref->cart->BTaddress;
	$userFieldsBT = OPCloader::getUserFields('BT', $ref->cart); 
	$fx = array(); 
	$added = array(); 
	
	if (defined('VM_VERSION') && (VM_VERSION >= 3))
	{
		$userFieldsCart = OPCloader::getUserFields('cartfields', $ref->cart); 
	}
	
	$ignore = array('delimiter', 'hidden'); 
	foreach ($userFieldsBT['fields'] as $k2=>$v2)
	 {
	   if (!empty($added[$v2['name']])) continue; 
	   if (in_array($v2['type'], $ignore)) continue;
	   $fx[] = '"'.OPCmini::slash($v2['name'], false).'"'; 
	   $added[$v2['name']] = 1; 
	 }
    foreach ($userFieldsST['fields'] as $k=>$v)
	 {
	   if (in_array($v['type'], $ignore)) continue;
	    if (!empty($added[$v['name']])) continue; 
	   $fx[] = '"'.OPCmini::slash($v['name'], false).'"'; 
	   
	   $added[$v['name']] = 1; 
	 }
	 
	 if (!empty($userFieldsCart))
	 {
		 foreach ($userFieldsCart['fields'] as $k=>$v)
	 {
	   if (in_array($v['type'], $ignore)) continue;
	   if (!empty($added[$v['name']])) continue;  
	   $fx[] = '"'.OPCmini::slash($v['name'], false).'"'; 
	   
	   $added[$v['name']] = 1; 
	 }
	 }
	
	$render_in_third_address = OPCconfig::get('render_in_third_address', array()); 
	if (!empty($render_in_third_address))
	 {
		 foreach ($render_in_third_address as $k=>$v)
	 {
	   
	   
	   $fx[] = '"'.OPCmini::slash('third_'.$v, false).'"'; 
	   
	  
	 }
	 }
	
	if ($hasdatepicker) {
		$fx[] = '"'.OPCmini::slash('opc_date_picker', false).'"'; 
	}
	
	
	$fx2 = implode(',', $fx); 
	$extJs .= $fx2.'); '; 
	}
	
	
	
	
	//else
	//$extJs .= " var op_userfields = new Array(); "; 
	
	
	// ajax fields start: 
	{
	$extJs .= " var opc_ajax_fields = new Array("; 
	
	$opc_ajax_fields = OPCconfig::get('opc_ajax_fields', array()); 
	$fx = array(); 
	foreach ($opc_ajax_fields as $f)
	{
		 $fx[] = '"'.OPCmini::slash($f, false).'"'; 
		 $fx[] = '"'.OPCmini::slash('shipto_'.$f, false).'"'; 
	}
	
	

	$fx2 = implode(',', $fx); 
	$extJs .= $fx2.'); '; 
	}
	//ajax fields end
	
	$extJs .= ' var op_firstrun = true; ';
	//$extHelper->runExt('addjavascript', '', '', $extJs);
	
	if (!empty($business_fields))
	  {
	    $business_fields3 = array(); 
	    foreach ($business_fields as $k=>$line)
		 {
		   $business_fields3[$k] = "'".$line."'"; 
		 }
		 $newa = implode(',', $business_fields3); 
	    $extJs .= ' var business_fields = ['.$newa.']; ';
		 
	  }
	  else $extJs .= ' var business_fields = new Array(); '; 
	  

	if (!empty($business_fields2))
	  {
	    $business_fields3 = array(); 
	    foreach ($business_fields2 as $k=>$line)
		 {
		   $business_fields3[$k] = "'".$line."'"; 
		 }
		
		 $newa = implode(',', $business_fields3); 
	     $extJs .= ' var business_fields2 = ['.$newa.']; ';
		 $extJs .= ' var business_selector = \''.addslashes($business_selector).'\'; ';
		 
		 if (isset($business2_value))
		 {
			 $extJs .= ' var business2_value = \''.addslashes($business2_value).'\'; ';
		 }
		 else $extJs .= ' var business2_value = \'\'; ';
		 
	  }
	  else $extJs .= ' var business_fields2 = new Array(); var business_selector = \'\';  '; 

	  if (!empty($is_business2))
	  {
		  $extJs .= ' var is_business2 = true; ';
	  }
	  else
		  $extJs .= ' var is_business2 = false; ';
	  
	  
	   if (!empty($custom_rendering_fields))
	  {
	    $custom_rendering_fields2 = array(); 
	    foreach ($custom_rendering_fields as $k=>$line)
		 {
		   $custom_rendering_fields2[$k] = "'".$line."'"; 
		 }
		 $newa = implode(',', $custom_rendering_fields2); 
	    $extJs .= ' var custom_rendering_fields = new Array('.$newa.'); ';
		 
	  }
	  else $extJs .= ' var custom_rendering_fields = new Array(); '; 
	
	//shipping_obligatory_fields
	   if (!empty($shipping_obligatory_fields))
	  {
	    $shipping_obligatory_fields2 = array(); 
	    foreach ($shipping_obligatory_fields as $k=>$line)
		 {
		   $shipping_obligatory_fields2[$k] = "'".$line."'"; 
		 }
		 $newa = implode(',', $shipping_obligatory_fields2); 
	    $extJs .= ' var shipping_obligatory_fields = new Array('.$newa.'); ';
		 
	  }
	  else $extJs .= ' var shipping_obligatory_fields = new Array(); '; 
	
	
	 if (!empty($business_obligatory_fields))
	  {
	    $business_obligatory_fields2 = array(); 
	    foreach ($business_obligatory_fields as $k=>$line)
		 {
		   $business_obligatory_fields2[$line] = "'".$line."'"; 
		 }
		  if (!empty($business_fields2)) {
		 foreach ($userFieldsBT['fields'] as $k2=>$v2)
		 {
			 
		 foreach ($business_fields2 as $f=>$line3) 
		 {
			$name =  $v2['name']; 
			$nameb = $line3; 
			if ($name !== $nameb) continue; 
			if (!empty($v2['required'])) {
			  $business_obligatory_fields2[$line3] = "'".$line3."'"; ; 
			}
		 }
		 }
		  }
		 
		 $newa = implode(',', $business_obligatory_fields2); 
	    $extJs .= ' var business_obligatory_fields = new Array('.$newa.'); ';
		 
	  }
	  else $extJs .= ' var business_obligatory_fields = new Array(); '; 
	
	
	if (!empty($only_one_shipping_address_hidden))
	{
	 $extJs .= 'var shippingOpenStatus = true; '; 
	 $extJs .= 'var shipping_always_open = true; '; 
	}
	else
	{
	 $extJs .= 'var shippingOpenStatus = false; '; 
	 $extJs .= 'var shipping_always_open = false; '; 
	}
	
	if (empty($op_autosubmit))
	$extJs .= " var op_autosubmit = false; ";
	else 
	{ 
	 $extJs .= " var op_autosubmit = true; ";
	
	}
	$db=JFactory::getDBO();
	$q = 'select * from #__virtuemart_vendors where virtuemart_vendor_id = 1 limit 0,1 '; 
	$db->setQuery($q); 
	$res = $db->loadAssoc(); 
	if (!empty($res)) extract($res); 
	
	//VmConfig::get('useSSL',0)
	
	$mainframe = Jfactory::getApplication();
$vendorId = (int)JRequest::getInt('vendorid', 1);

/* table vm_vendor */

if (!class_exists('VirtueMartCart'))
	 require(JPATH_VM_SITE .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'cart.php');
	 
if (!class_exists('CurrencyDisplay'))
require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'currencydisplay.php');

$virtuemart_currency_id = OPCloader::getCurrency($ref->cart); 
if (empty($ref->cart))
{
  $ref->cart = $cart = VirtueMartCart::getCart();
}

if (!empty($virtuemart_currency_id))
$c = CurrencyDisplay::getInstance($virtuemart_currency_id);
else
{	
	$c = CurrencyDisplay::getInstance($ref->cart->paymentCurrency);
	$virtuemart_currency_id = $ref->cart->paymentCurrency;
}
    if (!method_exists($c, 'getDecimalSymbol'))
	{ 
	$db = JFactory::getDBO(); 
	$q = 'select * from #__virtuemart_currencies where virtuemart_currency_id = '.(int)$virtuemart_currency_id.' limit 0,1'; 
	$db->setQuery($q); 
	
	$c2 = $db->loadObject(); 
	if (empty($c2))
	{ 
	   $c2 = new stdClass(); 
	   $c2->currency_symbol = '$'; 
	   $c2->currency_decimal_place = 2; 
	   $c2->currency_decimal_symbol = '.'; 
	   $c2->currency_thousands = ' '; 
	   $c2->currency_positive_style = '{number} {symbol}';
	   $c2->currency_negative_style = '{sign}{number} {symbol}'; 
	   
	   
	}
	
	
	// op_vendor_style = '1|&euro;|2|.|\'|3|0'; 
	$arr = array(); 
	$arr[0] = '1'; 
	$arr[1] = $c2->currency_symbol; 
	$arr[2] = $c2->currency_decimal_place; 
	$arr[3] = $c2->currency_decimal_symbol; 
	$arr[4] = $c2->currency_thousands; 
	// for now
	$arr[5] = '3';
	$arr[6] = '8';
	$arr[7] = '8';
	$arr[8] = $c2->currency_positive_style; 
	$arr[9] = $c2->currency_negative_style; 
	$vendor_currency_display_style = implode('|', $arr);
	
	
	}
	else
	{
	
	



	// op_vendor_style = '1|&euro;|2|.|\'|3|0'; 
	if (method_exists($c, 'getDecimalSymbol'))
	{
	$arr = array(); 
	$arr[0] = '1'; 
	$arr[1] = $c->getSymbol(); 
	$arr[2] = $c->getNbrDecimals(); 
	$arr[3] = $c->getDecimalSymbol(); 
	$arr[4] = $c->getThousandsSeperator(); 
	// for now
	$arr[5] = '3';
	$arr[6] = '8';
	$arr[7] = '8';
	$arr[8] = $c->getPositiveFormat(); 
	$arr[9] = $c->getNegativeFormat(); 
	$vendor_currency_display_style = implode('|', $arr);
	}
	
	
	}
	//$arr[2] = $c->
	$extJs .= " var op_saved_shipping = null; var op_saved_payment = null; var op_saved_shipping_vmid = '';";
	
	$cs = str_replace("'", '\\\'', $vendor_currency_display_style);
	$extJs .= " var op_vendor_style = '".$cs."'; ";
	$extJs .= " var op_currency_id = '".(int)$virtuemart_currency_id."'; "; 
	//if (!empty($override_basket) || (!empty($shipping_inside_basket)) || (!empty($payment_inside_basket)))
	{
	 $extJs .= ' op_override_basket = true; ';
	 $extJs .= ' op_basket_override = true; ';
	}
	/*
	else 
	{
	 $extJs .= ' op_override_basket = false; ';
	 $extJs .= ' op_basket_override = false; ';
	}
	*/
	
	//   if ($onlyindex) return JURI::root(true).'/index.php'; 
	
	
	
			if (empty($action_url))
			$action_url = JURI::root(true).'/index.php?option=com_virtuemart&amp;view=opc&amp;controller=opc&amp;task=checkout&amp;nosef=1';

			$action_url = html_entity_decode($action_url); 
			
			
			$lang = OPCloader::getLangCode(); 
			if (!empty($lang))
			{
				$action_url .= '&lang='.$lang; 
			}

			
			
			$extJs .= " var opc_action_url = '".$action_url."'; "; 
	
	
        // google adwrods tracking code here
        if (!empty($adwords_enabled[0]))
            {
             $extJs .= " var acode = '1'; ";
            }
            else
            {
              $extJs .= " var acode = '0'; ";
            }
	
	if (ctype_alnum($lang))
	$extJs .= " var op_lang = '".$lang."'; ";
	else
	$extJs .= " var op_lang = ''; ";
	
	$ur = JURI::root(true); 
	if (substr($ur, strlen($ur)-1)!= '/')
	 $ur .= '/';

	$mm_action_url = $ur;
	
	$isVm202 = false; 
	 if (!class_exists('VirtueMartModelShopperGroup'))
	 {
	 if (file_exists(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'shoppergroup.php'))
		    require( JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'shoppergroup.php' );
		else 
		$isVm202 = true; 
	 }
	 if (!method_exists('VirtueMartModelShopperGroup', 'appendShopperGroups')) $isVm202 = true; 

	
	if (!$isVm202)
	$extJs .= " var op_securl = '".$ur."index.php?option=com_onepage'; ";
	else
	$extJs .= " var op_securl = '".$ur."index.php?option=com_virtuemart'; ";
	
	$extJs .= " var pay_btn = new Array(); "; 
	$extJs .= " var pay_msg = new Array(); "; 
	$extJs .= " pay_msg['default'] = ''; ";
	
    $extJs .= " pay_btn['default'] = '".OPCmini::slash(OPCLang::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU'))."'; ";

        $extJs .= " var op_timeout = 0; ";
		if (!empty($adwords_timeout))
        $extJs .= " var op_maxtimeout = ".$adwords_timeout."; ";
		else $extJs .= " var op_maxtimeout = 3000; ";
        $extJs .= " var op_semafor = false; ";
	if (!empty($op_sum_tax))
	{
	    $extJs .= " var op_sum_tax = true; ";
	}
	else
	{
	  $extJs .= " var op_sum_tax = false; ";
	}
	if (defined("_MIN_POV_REACHED") && (constant("_MIN_POV_REACHED")=='1'))
	{
	
	 $extJs .= " var op_min_pov_reached = true; ";
	}
	else
	{
	
	 $extJs .= " var op_min_pov_reached = false; ";
	}
	
	$extJs .= ' var opc_logic = "'.$task.'"; '; 
	
	
	// this setting says if to show discountAmout together with the classic discount
	if (!empty($payment_discount_before))
	$extJs .= " var payment_discount_before = true; ";
	else
	$extJs .= " var payment_discount_before = false; ";
	
	if (empty($hidep) || (!empty($payment_inside)))
	{
	$extJs .= " var op_payment_disabling_disabled = true; ";
	}
	else
	{
	$extJs .= " var op_payment_disabling_disabled = false; ";
	}
	//$extJs .= " var op_show_prices_including_tax = '".$auth["show_price_including_tax"]."'; ";
	$extJs .= " var op_show_prices_including_tax = '1'; ";
	$extJs .= " var never_show_total = ";
	if ((isset($never_show_total) && ($never_show_total==true))) $extJs .= ' true; '."\n";
	else $extJs .= ' false; '."\n";
	$extJs .= " var op_no_jscheck = ";
	// modified for OPC2
	if (!empty($no_jscheck)) $extJs .= " true; "; else $extJs .= " true; ";
	$extJs .= " var op_no_taxes_show = ";
	if ((isset($no_taxes_show) && ($no_taxes_show==true))) $extJs .= ' true; '."\n";
	else $extJs .= ' false; '."\n";

	$extJs .= " var op_no_taxes = ";
	if ((isset($no_taxes) && ($no_taxes==true))) $extJs .= ' true; '."\n";
	else $extJs .= ' false; '."\n";
	
	$selectl = OPCLang::_('COM_VIRTUEMART_LIST_EMPTY_OPTION');
	$extJs .= " var op_lang_select = '(".$selectl.")'; ";
	//if ((ps_checkout::tax_based_on_vendor_address()) && ($auth['show_price_including_tax']) && ((!isset($always_show_tax) || ($always_show_tax !== true))))
	//$extJs .= " var op_dont_show_taxes = '1'; ";
	//else
	$extJs .= " var op_dont_show_taxes = '0'; "."\n";
	$extJs .= ' var op_coupon_amount = "0"; '."\n";
	
	$extJs .= ' var op_shipping_txt = "'.OPCmini::slash(OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING_PRICE_LBL'), false).'"; '."\n"; 
	$extJs .= ' var op_shipping_tax_txt = "'.OPCmini::slash(OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING_TAX'), false).'"; '."\n"; 
  $country_ship = array();

    
	if (false)
	if (isset($hidep))
	foreach ($hidep as &$h)
	{ 
	  $h .= ','.$payments_to_hide.',';
	  $h = str_replace(' ', '', $h);
	  $h = ','.$h.',';
	}
	
	// found shipping methods
        /*
	if (false)
	foreach ($sarr as $k=>$ship)
	{
	   if (isset($hidep[$ship->virtuemart_shipmentmethod_id]))
	   $extJs .= " payconf['".$k."']=\",".$hidep[$k].",\"; ";
	   else $extJs .= " payconf['".$k."']=\",\"; "; 
	  
	}
       */
	// old code for standard shipping
	
	if (!empty($rows))
	foreach ($rows as $r)
	{
	 $id = $r['shipping_rate_id'];
	 $cs = $r['shipping_rate_country'];
	 $car = $r['shipping_rate_carrier_id'];
	 $k = explode(';', $cs, 1000);
	 foreach($k as $kk)
	 {
	  if ($kk!='')
	  {
	  $krajiny[] = $kk;
	  if (!isset($country_ship[$id]))
	    $country_ship[$id] = array();
	  $country_ship[$id][$kk] = $kk;
	  }
	 }
	 $extJs .= "shipconf[".$id."]=\"".$cs.'"; ';
	 
	}
		// end of old code for standard shipping
		
        
        // country_ship description:
        // country_ship[ship_id][country] = country
        // country_ship will be used for default shipping method for selected default shipping country
        
        // global variables: ordertotal, currency symbol, text for order total
//        echo $incship;
        $incship = OPCLang::_('COM_ONEPAGE_ORDER_TOTAL_INCL_SHIPPING'); 	
        if (empty($incship)) $incship = OPCLang::_('COM_VIRTUEMART_ORDER_LIST_TOTAL'); 		
        $incship = OPCmini::slash($incship);

	if (!empty($order_total))
        $extJs .= " var op_ordertotal = ".$order_total."; ";
         else $extJs .= " var op_ordertotal = 0.0; ";
        $extJs .= " var op_textinclship = '".OPCmini::slash(OPCLang::_('COM_VIRTUEMART_CART_TOTAL'))."'; ";
        $extJs .= " var op_currency = '".OPCmini::slash($c->getSymbol())."'; ";
        if (!empty($weight_total))
        $extJs .= " var op_weight = ".$weight_total."; ";
        else $extJs .= " var op_weight = 0.00; ";
        if (!empty($vars['zone_qty']))
        $extJs .= " var op_zone_qty = ".$vars['zone_qty']."; ";
        else $extJs .= " var op_zone_qty = 0.00; ";
        if (!empty($grandSubtotal))
        $extJs .= " var op_grand_subtotal = ".$grandSubtotal."; ";
        else $extJs .= " var op_grand_subtotal = 0.00; ";
        $extJs .= ' var op_subtotal_txt = "'.OPCmini::slash(OPCLang::_('COM_VIRTUEMART_CART_SUBTOTAL'), false).'"; ';
        $extJs .= ' var op_tax_txt = "'.OPCmini::slash(OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX'), false).'"; ';
       
	     $op_disable_shipping = OPCloader::getShippingEnabled($ref->cart);
        if (!empty($op_disable_shipping))
        $nos = 'true'; 
		else 
		$nos = 'false';
		
        $extJs .= "var op_noshipping = ".$nos."; ";
		$extJs .= "var op_autosubmit = false; "; 
//        $extJs .= " var op_tok = '".$_SESSION['__default']['session.token']."'; ";
	// array of avaiable country codes
	if (!empty($krajiny))
	$krajiny = array_unique($krajiny);
   
	
	$rp_js = ''; 
	$extJs .= $rp_js."\n";
	$ship_country_change_msg = OPCLang::_('COM_ONEPAGE_SHIP_COUNTRY_CHANGED'); 
	$extJs .= ' var shipChangeCountry = "'.OPCmini::slash($ship_country_change_msg, false).'"; '."\n";
	$extJs .= ' var opc_free_text = "'.OPCmini::slash(OPCLang::_('COM_ONEPAGE_FREE', false)).'"; '."\n";
	
	if (!empty($use_free_text))
	$extJs .= " var use_free_text = true; "."\n";
	else
	$extJs .= " var use_free_text = false; "."\n";
	
	$ship_country_is_invalid_msg = OPCLang::_('COM_ONEPAGE_SHIP_COUNTRY_INVALID'); 
	$extJs .= ' var noshiptocmsg = "'.OPCmini::slash($ship_country_is_invalid_msg, false).'"; '."\n";
	$extJs .= " var default_ship = null; "."\n";
	
	$opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat'); 
	$extJs .= " var opc_vat_field = '".OPCmini::slash($opc_vat_key)."'; "."\n";
	
    $extJs .= ' var agreedmsg = "'.OPCmini::slash(OPCLang::_('COM_VIRTUEMART_USER_FORM_BILLTO_TOS_NO', false)).'"; '."\n";
	$extJs .= ' var op_continue_link = ""; '."\n";
	if ($must_have_valid_vat)
        $extJs .= "var op_vat_ok = 2; var vat_input_id = \"".$vat_input_id."\"; var vat_must_be_valid = true; "."\n";
		$default_info_message = OPCLang::_('COM_ONEPAGE_PAYMENT_EXTRA_DEFAULT_INFO'); 
        $extJs .= ' var payment_default_msg = "'.str_replace('"', '\"', $default_info_message).'"; '."\n";
        $extJs .= ' var payment_button_def = "'.str_replace('"', '\"', OPCLang::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU')).'"; '."\n";
		
		$extJs .= ' var opc_cancel = "'.str_replace('"', '\"', OPCLang::_('JCANCEL')).'"; '."\n";
	if (empty($op_dontloadajax))
	$extJs .= ' var op_dontloadajax = false; ';
	else
	$extJs .= ' var op_dontloadajax = true; ';
    
	$extJs .= ' var op_user_name_checked = false; ';
	$extJs .= ' var op_email_checked = false; ';
	// adds payment discount array
	//if (isset($pscript))
	//$extJs .= $pscript;
	if (isset($payments_to_hide))
	{
	 $payments_to_hide = str_replace(' ', '', $payments_to_hide);
	}
	else
	 $payments_to_hide = "";

	// adds script to change text on the button
	if (isset($rp))
	$extJs .= $rp;
	if (!((isset($vendor_name)) && ($vendor_name!='')))
	$vendor_name = 'E-shop';
	$extJs .= ' var op_vendor_name = "'.OPCmini::slash($vendor_name, false).'"; '."\n";

	/*
		if (!isset($_SESSION['__default']['session.token']))
	$_SESSION['__default']['session.token'] = md5(uniqid());
	$next_order_id = $bhelper->getNextOrderId(); 

	jimport( 'joomla.utilities.utility' );
	if (method_exists('JUtility', 'getToken'))
	$token = JUtility::getToken(); 
	else 
	$token = JSession::getFormToken(); 
	$token = md5($token); 
	$g_order_id = $next_order_id."_".$token;
	$extJs .= ' var g_order_id = "'.$g_order_id.'"; '."\n";
	*/
	
	$extJs .= ' var op_order_total = 0; '."\n";
	$extJs .= ' var op_total_total = 0; '."\n";
	$extJs .= ' var op_ship_total = 0; '."\n";
	$extJs .= ' var op_tax_total = 0; '."\n";
	if (empty($op_fix_ins))
	$extJs .= 'var op_fix_payment_vat = false; ';
	
	$extJs .= ' var op_run_google = new Boolean(';
	if (!empty($g_analytics))
	 $extJs .= 'true); ';
	else
	 $extJs .= 'false); ';
	if (!isset($pth_js)) 
	$pth_js = '';
    $extJs .= ' var op_always_show_tax = ';
    if (isset($always_show_tax) && ($always_show_tax===true))
      $extJs .= 'true; '."\n";
     else $extJs .= 'false; '."\n";
    
    $extJs .= ' var op_always_show_all = ';
    if (isset($always_show_all) && ($always_show_all===true))
      $extJs .= 'true; '."\n";
     else $extJs .= 'false; '."\n";
     
    $extJs .= ' var op_add_tax = ';
    if (isset($add_tax) && ($add_tax===true))
      $extJs .= 'true; ';
     else $extJs .= 'false; ';
    
    $extJs .= ' var op_add_tax_to_shipping = ';
    if (isset($add_tax_to_shipping) && ($add_tax_to_shipping===true))
      $extJs .= 'true; '."\n";
     else $extJs .= 'false; '."\n";

    $extJs .= ' var op_add_tax_to_shipping_problem = ';
    if (isset($add_tax_to_shipping_problem) && ($add_tax_to_shipping_problem===true))
      $extJs .= 'true; '."\n";
     else $extJs .= 'false; '."\n";


    $extJs .= ' var op_no_decimals = ';
    if (isset($no_decimals) && ($no_decimals===true))
      $extJs .= 'true; '."\n";
     else $extJs .= 'false; '."\n";

    $extJs .= ' var op_curr_after = ';
    if (isset($curr_after) && ($curr_after===true))
      $extJs .= 'true; '."\n";
     else $extJs .= 'false; '."\n";
	
	if (empty($op_basket_subtotal_taxonly)) $op_basket_subtotal_taxonly = '0.00';
	$extJs .= ' var op_basket_subtotal_items_tax_only = '.$op_basket_subtotal_taxonly.'; ';
/*
	can be send to js if needed: 
			$op_basket_subtotal += $price["product_price"] * $cart[$i]["quantity"];
		$op_basket_subtotal_withtax += ($price["product_price"] * $cart[$i]["quantity"])*($my_taxrate+1);
		$op_basket_subtotal_taxonly +=  ($price["product_price"] * $cart[$i]["quantity"])*($my_taxrate);
*/

	$extJs .= ' var op_show_only_total = ';
    if (isset($show_only_total) && ($show_only_total===true))
      $extJs .= 'true; '."\n";
     else $extJs .= 'false; '."\n";
     
    $extJs .= ' var op_show_andrea_view = ';
    if (isset($show_andrea_view) && ($show_andrea_view===true))
      $extJs .= 'true; '."\n";
     else $extJs .= 'false; '."\n";
      
	$extJs .= ' var op_detected_tax_rate = "0"; ';
    $extJs .= ' var op_custom_tax_rate = ';
    if (empty($custom_tax_rate)) $custom_tax_rate = '0.00';
    $custom_tax_rate = str_replace(',', '.', $custom_tax_rate);
    $custom_tax_rate = str_replace(' ', '', $custom_tax_rate);
    if (!empty($custom_tax_rate) && is_numeric($custom_tax_rate))
      $extJs .= '"'.$custom_tax_rate.'"; '."\n";
     else $extJs .= '""; '."\n";

    $extJs .= ' var op_coupon_discount_txt = "'.OPCmini::slash(OPCLang::_('COM_VIRTUEMART_COUPON_DISCOUNT'), false).'"; '."\n";

    $extJs .= ' var op_other_discount_txt = "'.OPCmini::slash(OPCLang::_('COM_ONEPAGE_OTHER_DISCOUNT'), false).'"; '."\n";

    
    if (!empty($shipping_inside_basket))
    {
     $extJs .= " var op_shipping_inside_basket = true; ";
    }
    else $extJs .= " var op_shipping_inside_basket = false; ";

    if (!empty($payment_inside_basket) && (empty($isexpress)))
    {
     $extJs .= " var op_payment_inside_basket = true; ";
    }
    else $extJs .= " var op_payment_inside_basket = false; ";
    
    
	$extJs .= " var op_disabled_payments = \"$pth_js\"; \n";
  
  	$extJs .= "var op_payment_discount = 0; \n var op_ship_cost = 0; \n var pdisc = []; "."\n";
    $extJs .= 'var op_payment_fee_txt = "'.str_replace('"', '\"', OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT')).'"; '."\n"; // fee
    $extJs .= 'var op_payment_discount_txt = "'.str_replace('"', '\"', OPCLang::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT')).'"; '."\n"; // discount
    //$rp_js = ' var pay_msg = []; var pay_btn = []; ';	
    
    // paypal:
    if (false && $paypalActive)
    $extJs .= ' var op_paypal_id = "'.ps_paypal_api::getPaymentMethodId().'"; ';
    else $extJs .= ' var op_paypal_id = "x"; ';
    if (false && $paypalActive && (defined('PAYPAL_API_DIRECT_PAYMENT_ON')) && ((boolean)PAYPAL_API_DIRECT_PAYMENT_ON))
    {
      $extJs .= ' var op_paypal_direct = true; ';
    }
    else
    {
      $extJs .= ' var op_paypal_direct = false; ';
    }
	
	$extJs .= ' var op_general_error = '."'".OPCmini::slash(OPCLang::_('COM_VIRTUEMART_USER_FORM_MISSING_REQUIRED'))."';";
	$extJs .= ' var op_email_error = '."'".OPCmini::slash(OPCLang::_('COM_VIRTUEMART_ENTER_A_VALID_EMAIL_ADDRESS'))."';";
    $err = OPCJavascript::getPwdError(); 
	
	$extJs .= ' var op_pwderror = '."'".OPCmini::slash($err)."';\n";
   
   if ($double_email)
   if (!OPCloader::logged($ref->cart))
     {
	  $extJs .= ' 
	      if (typeof addOpcTriggerer != \'undefined\')
		  addOpcTriggerer(\'callSubmitFunct\', \'Onepage.doubleEmailCheck\');
          '; 
   	 }
	 
   if (!empty($disable_payment_per_shipping))
   {
     $extJs .= ' 
	 if (typeof addOpcTriggerer != \'undefined\')
	 addOpcTriggerer("callAfterShippingSelect", "Onepage.refreshPayment()"); '; 
   }
   
   if (empty($no_coupon_ajax))
   $extJs .= '
    if (typeof jQuery != \'undefined\')
   jQuery(document).ready(function() {
     jQuery(\'#userForm\').bind(\'submit\',function(){
		 if (userForm.coupon_code != null)
		 if (userForm.coupon_code.value != null)
		 {
		 new_coupon = Onepage.op_escape(userForm.coupon_code.value); 
		 if (typeof Onepage != \'undefined\')
		 if (typeof Onepage.op_runSS != \'undefined\')
		 {
         Onepage.op_runSS(this, false, true, \'process_coupon&new_coupon=\'+new_coupon); 
		 return false; 
		 }
		 }
    });
    });';
	//callAfterShippingSelect.push('hideShipto()'); 
	
	 $inside = JRequest::getCmd('insideiframe', ''); 
			$js = ''; 
			if (!empty($inside))
			{
			$js = "\n".' 
			if (typeof jQuery != \'undefined\' && (jQuery != null))
			{
			 jQuery(document).ready(function() {

			 if (typeof Onepage.op_runSS == \'undefined\') return;
			 '; 
			 
			 if (!empty($inside)) $js .= "\n".' op_resizeIframe(); '."\n"; 
			 
			 $js .= ' 		 });
			}
			else
			 {
			   if ((typeof window != \'undefined\') && (typeof window.addEvent != \'undefined\'))
			   {
			   window.addEvent(\'domready\', function() {
			   ';
			   if (!empty($inside)) $js .= ' op_resizeIframe(); '; 
			$js .= '
			
			    });
			   } 
			  }'; 
			 }
			
			
			
			$document  = JFactory::getDocument();
			$raw_js =   "\n".$extJs."\n".$js."\n"; 
			$src = '<script>'."\n".'//<![CDATA['.$raw_js.'//]]> '."\n".'</script>'; 
			
			
			
$app = JFactory::getApplication(); 
$jtouch = $app->getUserStateFromRequest('jtpl', 'jtpl', -1, 'int');
if ($jtouch > 0)
$opc_php_js2 = true; 


			// stAn, updated on 2.0.218
			// stan, to support gk gavick mobile themes we had to omit the type
			if (empty($opc_php_js2)) 
			 {
			    $document->addCustomTag($src);
				return;
			 }
			
			$js_dir = JPATH_CACHE.DIRECTORY_SEPARATOR.'com_onepage'; 
			
			$lang = JFactory::getLanguage()->getTag(); 
			$js_file = 'opc_dynamic_'.$lang.'_'.md5($raw_js).'.js'; 
			
			$js_path = $js_dir.DIRECTORY_SEPARATOR.$js_file; 
			
			$add = true; 
			
			
			
			
			
			
			jimport( 'joomla.filesystem.folder' );
			jimport( 'joomla.filesystem.file' );
			if (!is_writable(JPATH_CACHE)) $add = true; 
			else
			{
			if (!file_exists($js_dir)) 
			{
			  if (JFolder::create($js_dir) === false) $add = true;
			}
			
			if (!file_exists($js_path))
			{
			
			   if (JFile::write($js_path, $raw_js) !== false)
			    {
				
				  JHTMLOPC::script($js_file, 'cache/com_onepage/'); 
				  return; 
			      
				}
				else
				{
				
				  $add = true; 
				 
				}
			}
			
			if (!empty($opc_php_js2))
			if (file_exists($js_path))
			{
			
			JHTMLOPC::script($js_file, 'cache/com_onepage/'); 
			
			return;
			}
			}
			if ($add)
			$document->addCustomTag($src);
			
			
			
			//echo $src; 
			//$document->addCustomTag('<script type="text/javascript">'."\n".'//<![CDATA[  '."\n".$extJs."\n".$js."\n".'//]]> '."\n".'</script>');

	
    return; 
 }
 
 
 
  public static function getPwdError()
 {
   $jlang = JFactory::getLanguage(); 
   	 if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) {

   $jlang->load('com_users', JPATH_SITE, 'en-GB', true); 
   $jlang->load('com_users', JPATH_SITE, $jlang->getDefault(), true); 
   $jlang->load('com_users', JPATH_SITE, null, true); 
   
   return OPCLang::_('COM_USERS_FIELD_RESET_PASSWORD1_MESSAGE'); 
   
   }
   else
   {
    $jlang->load('com_user', JPATH_SITE, 'en-GB', true); 
    $jlang->load('com_user', JPATH_SITE, $jlang->getDefault(), true); 
    $jlang->load('com_user', JPATH_SITE, null, true); 

    return OPCLang::_('PASSWORDS_DO_NOT_MATCH'); 
   }
 }
 
 	

public static  function loadJavascriptFiles(&$ref, &$OPCloader)
 {
   
 include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
 /*
 if (!empty($opc_php_js))
 {
   $dc = JFactory::getDocument(); 
   $url = OPCloader::getUrl(true);
      
   $url_onepage = JRoute::_('index.php?option=com_onepage&view=loadjs&task=loadjs&file=sync.js&nosef=1&format=raw'); 
   $dc->addScript($url_onepage, "text/javascript", false, false); 
   $url_onepage = JRoute::_('index.php?option=com_onepage&view=loadjs&task=loadjs&file=onepage.js&nosef=1&format=raw'); 
   $dc->addScript($url_onepage, "text/javascript", true, true); 
   
   
   return; 
 }
 */ 
		if (empty($opc_async))
			{
			JHTMLOPC::script('onepage.js', 'components/com_onepage/assets/js/', false);
			JHTMLOPC::script('sync.js', 'components/com_onepage/assets/js/', false);
			}
			else
			{
			  JHTMLOPC::script('sync.js', 'components/com_onepage/assets/js/', false);
			  $dc = JFactory::getDocument(); 
			  $url = OPCloader::getUrl(true); 
			  $dc->addScript($url.'components/com_onepage/assets/js/onepage.js', "text/javascript", true, true); 
			}
 }
 
 
 public static function loadJquery()
 {
      
		 
	  // stAn - this block will enable including newest jquery library, uncomment if needed 
	    $document = JFactory::getDocument(); 
	    $app = JFactory::getApplication(); 
		$jq = $app->get('jquery', false); 
		$jq_ui = $app->get('jquery-ui', false); 
		
		if (empty($jq))
		 {
		 //jquery-1.11.0.min.js
		 //$document->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js'); 
		  //$document->addScript('//code.jquery.com/jquery-1.11.0.min.js'); 
		  if(!version_compare(JVERSION,'3.4.0','ge')) {
		  //$document->addScript('//code.jquery.com/jquery-latest.min.js'); 
		  
		  
		  
		  JHTMLOPC::script('jquery-1.11.2.min.js', 'components/com_onepage/themes/extra/jquery-ui/', false);
		  
		  //$document->addScript('//code.jquery.com/jquery-2.1.0.min.js'); 
		  $document->addScript('//code.jquery.com/jquery-migrate-1.2.1.min.js'); 
		  $app->set('jquery', true); 
		  $app->set('jquery-migrate', true); 
		  }
		  else
		  {
			  JHtml::_('jquery.framework');
			  JHtml::_('jquery.ui');
		  }
	     }
		 
		 
		if (empty($jq_ui))
		{
			JHTMLOPC::script('jquery-ui.min.js', 'components/com_onepage/themes/extra/jquery-ui/');
		    JHTMLOPC::stylesheet('jquery-ui.min.css', 'components/com_onepage/themes/extra/jquery-ui/');
			JHTMLOPC::stylesheet('jquery-ui.theme.css', 'components/com_onepage/themes/extra/jquery-ui/'); 
			$app->set('jquery-ui', true); 
			
		}
		/*
		$ui = $app->get('jquery-ui', false); 
		if (empty($ui))
		{
		$document->addScript( '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16');
		$app->set('jquery-ui', true); 
		}
		*/
	   $document = JFactory::getDocument();			
	   $base = JURI::base(); 
	   $jbase = str_replace('/administrator', '', $base); 	
	   if (substr($jbase, -1) !== '/') $jbase .= '/'; 
	   
	   
	   if (substr($jbase, 0, 5) === 'http:') {
				$jbase = substr($jbase, 5); 
			}
			if (substr($jbase, 0, 6) === 'https:') {
				$jbase = substr($jbase, 6); 
			}

if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'jquery.noConflict.js'))
$document->addScript($jbase.'components/com_virtuemart/assets/js/jquery.noConflict.js');
else
if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'jquery.noconflict.js'))
$document->addScript($jbase.'components/com_virtuemart/assets/js/jquery.noconflict.js');
	
	
	if (class_exists('plgSystemOpc'))
	{
	  plgSystemOpc::$opc_jquery_loaded = true; 
	}
	 
	 
	
	 // END of the block
 }
 
 
}