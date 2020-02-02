<?php
defined('_JEXEC') or die('Restricted access');

class OPChikajs {
	public static function getJavascript() {
		JHTMLOPC::script('sync.js', 'components/com_onepage/assets/js/', false);
		JHTMLOPC::script('hikaonepage.js', 'components/com_onepage/assets/js/', false);
		$fields = OPCHikaAddress::getAddressFieldsArray(); 
		
		$js = ''; 
		foreach ($fields as $area => $value) {
			$js .= ' var '.$area.' = '.json_encode($value).'; '."\n"; 
		}
		
		    $extJs = ''; 
			$full_url = Juri::root(); 
			if (substr($full_url, -1) != '/') $full_url .= '/'; 
	
   
			$extJs .= ' var op_loader_img = "'.$full_url.'components/com_onepage/themes/extra/img/loader1.gif";';  
			$extJs .= ' var op_loader = true; var opc_async = false; 
			
			var opc_disable_customer_email = false;  
			var opc_debug = true;  
			var op_customer_shipping = false;  
			var op_payment_inside = false;  
			var op_logged_in = false;  
			var op_last_payment_extra = null;  ';
			
			$user = JFactory::getUser(); 
	if ($user->id > 0)
	$extJs .= '	var op_logged_in_joomla = true;  '; 
	else 
	$extJs .= '	var op_logged_in_joomla = false;  '; 
			
			
			$extJs .= '
			var op_shipping_div = null;  
			var op_lastq = "";  
			var op_lastcountry = null; 
			var op_lastcountryst = null;  
			var op_isrunning = false;   '; 
			$extJs .= ' var COM_ONEPAGE_CLICK_HERE_TO_REFRESH_SHIPPING = "'.OPCloader::slash(OPCLang::_('COM_ONEPAGE_CLICK_HERE_TO_REFRESH_SHIPPING')).'"; ';
			$extJs .= ' var COM_VIRTUEMART_LIST_EMPTY_OPTION = "'.OPCloader::slash(OPCLang::_('COM_VIRTUEMART_LIST_EMPTY_OPTION')).'"; ';
			$extJs .= ' var COM_ONEPAGE_PLEASE_WAIT_LOADING = "'.OPCloader::slash(OPCLang::_('COM_ONEPAGE_PLEASE_WAIT_LOADING')).'"; ';
			$extJs .= ' var NO_PAYMENT_ERROR = "'.OPCloader::slash(JText::_('COM_VIRTUEMART_CART_SELECT_PAYMENT')).'"; ';
			$extJs .= ' var COM_ONEPAGE_MISSING_ONE_OR_THE_OTHER = "'.OPCloader::slash(JText::_('COM_ONEPAGE_MISSING_ONE_OR_THE_OTHER')).'"; ';
			$extJs .= ' var JERROR_AN_ERROR_HAS_OCCURRED = "'.OPCloader::slash(OPCLang::_('JERROR_AN_ERROR_HAS_OCCURRED')).'"; ';
			$extJs .= ' var COM_ONEPAGE_PLEASE_WAIT = "'.OPCloader::slash(OPCLang::_('COM_ONEPAGE_PLEASE_WAIT')).'"; ';
			$extJs .= ' var COM_ONEPAGE_CHOOSE_DESIRED_DELIVERY_DATE_ERROR = "'.OPCloader::slash(OPCLang::_('COM_ONEPAGE_CHOOSE_DESIRED_DELIVERY_DATE_ERROR')).'"; ';
			$extJs .= " var USERNAME_ERROR = '".OPCmini::slash(OPCLang::sprintf('COM_VIRTUEMART_STRING_ERROR_NOT_UNIQUE_NAME', OPCLang::_('COM_VIRTUEMART_USERNAME'))) ."';"; 
			$extJs .= " var EMAIL_ERROR = '".OPCmini::slash(OPCLang::sprintf('COM_ONEPAGE_EMAIL_ALREADY_EXISTS', OPCLang::_('COM_VIRTUEMART_USER_FORM_EMAIL'))) ."';"; 
			$extJs .= ' var OP_GENERAL_ERROR = '."'".OPCmini::slash(OPCLang::_('COM_VIRTUEMART_USER_FORM_MISSING_REQUIRED'))."';";
	        $extJs .= ' var OP_EMAIL_ERROR = '."'".OPCmini::slash(OPCLang::_('COM_VIRTUEMART_ENTER_A_VALID_EMAIL_ADDRESS'))."';";
			$err = OPCHikaMini::getPwdError(); 
			$extJs .= ' var OP_PWDERROR = '."'".OPCmini::slash($err)."';\n";
			$extJs .= " var OP_TEXTINCLSHIP = '".OPCmini::slash(OPCLang::_('COM_VIRTUEMART_CART_TOTAL'))."'; ";
			$selectl = OPCLang::_('COM_VIRTUEMART_LIST_EMPTY_OPTION');
			$extJs .= " var OP_LANG_SELECT = '(".$selectl.")'; ";
			$extJs .= ' var OP_SHIPPING_TXT = "'.OPCmini::slash(OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING_PRICE_LBL'), false).'"; '."\n"; 
			$extJs .= ' var OP_SHIPPING_TAX_TXT = "'.OPCmini::slash(OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING_TAX'), false).'"; '."\n"; 
			$extJs .= ' var OP_SUBTOTAL_TXT = "'.OPCmini::slash(OPCLang::_('COM_VIRTUEMART_CART_SUBTOTAL'), false).'"; ';
			$extJs .= ' var OP_TAX_TXT = "'.OPCmini::slash(OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX'), false).'"; ';
			$ship_country_change_msg = OPCLang::_('COM_ONEPAGE_SHIP_COUNTRY_CHANGED'); 
			$extJs .= ' var SHIPCHANGECOUNTRY = "'.OPCmini::slash($ship_country_change_msg, false).'"; '."\n";
			$extJs .= ' var OPC_FREE_TEXT = "'.OPCmini::slash(OPCLang::_('COM_ONEPAGE_FREE', false)).'"; '."\n";
			
			$extJs .= ' var OP_COUPON_DISCOUNT_TXT = "'.OPCmini::slash(OPCLang::_('COM_VIRTUEMART_COUPON_DISCOUNT'), false).'"; '."\n";
			$extJs .= ' var OP_OTHER_DISCOUNT_TXT = "'.OPCmini::slash(OPCLang::_('COM_ONEPAGE_OTHER_DISCOUNT'), false).'"; '."\n";
			$default_info_message = OPCLang::_('COM_ONEPAGE_PAYMENT_EXTRA_DEFAULT_INFO'); 
			$extJs .= ' var PAYMENT_DEFAULT_MSG = "'.str_replace('"', '\"', $default_info_message).'"; '."\n";
			$extJs .= ' var PAYMENT_BUTTON_DEF = "'.str_replace('"', '\"', OPCLang::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU')).'"; '."\n";
			
			
			$url = OPChikamini::getURL(true); 
			$extJs .= ' var op_relative_url = "'.$url.'"; '; 
			$extJs .= ' var opc_theme = "clean_simple2";  
			var op_usernameisemail = false;  
			var op_loader = false;  
			var op_onlydownloadable = "";  
			var op_last_field = false;  
			var op_refresh_html = "";  
			var no_alerts = false;  
			var opc_no_duplicit_username = true;  
			var opc_no_duplicit_email = true;  
			var last_username_check = true;  
			var last_email_check = true;  
			var op_delay = false;  
			var op_last1 = \'\';  
			var op_last2 = \'\';  '; 
			
			
			$actionurl = $url.'index.php'; 
			
			$extJs .= " var op_com_user = 'com_users'; "; 
			$extJs .= " var op_com_user_task = 'user.login'; "; 
			$extJs .= " var op_com_user_action = '".$actionurl."?option=com_users&task=user.login&controller=user'; "; 
			$extJs .= " var op_com_user_action_logout = '".$actionurl."?option=com_users&task=user.logout&controller=user'; "; 
			$extJs .= " var op_com_user_task_logout = 'user.logout'; "; 
			$extJs .= 'var op_firstrun = true;  
			var shippingOpenStatus = false; 
			var shipping_always_open = false;  
			var op_autosubmit = false;  
			var op_saved_shipping = null; 
			var op_saved_payment = null; 
			var op_saved_shipping_vmid = \'\'; 
			var op_vendor_style = \'1|£|2|.|,|3|8|8|{symbol}{number}|{symbol}{sign}{number}\';  '; 
			$currency_id = hikashop_getCurrency();
			$extJs .= '	var op_currency_id = '.(int)$currency_id.';  
				op_override_basket = true;  
				op_basket_override = true;  
				var opc_action_url = \'\';  
				var op_lang = \'en\';  
				var op_securl = \''.$url.'index.php?option=com_onepage\';  
				var pay_btn = new Array();  
				var pay_msg = new Array();  
				pay_msg[\'default\'] = \'\';  '; 
				$extJs .= " pay_btn['default'] = '".OPCmini::slash(OPCLang::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU'))."'; ";
				$extJs .= ' var op_timeout = 0;  
				var op_maxtimeout = 4;  
				var op_semafor = false;  
				var op_sum_tax = false;  
				var op_min_pov_reached = false;  
				var opc_logic = "checkout";  
				var payment_discount_before = false;  
				var op_payment_disabling_disabled = true;  
				var op_show_prices_including_tax = 1;  
				var never_show_total =  false; 
 var op_no_jscheck =  true;  var op_no_taxes_show =  false; 
 var op_no_taxes =  false; 
 var op_dont_show_taxes = 0; 
 var op_coupon_amount = 0; 
  var op_ordertotal = 0.0;  
  var op_currency = \'\';  
  var op_weight = 0.00;  
  var op_zone_qty = 0.00;  
  var op_grand_subtotal = 0.00;  
  var op_noshipping = false; 
  var op_autosubmit = false; 
  var shipChangeCountry = "Please choose the right carrier for selected country"; 
 var opc_free_text = "(free)"; 
 var use_free_text = false; 
 var noshiptocmsg = "We are sorry, but we do not ship to the chosen country. Please select a different country or contact us by phone."; 
 var default_ship = null; 
 var opc_vat_field = \'opc_vat\'; 
 var agreedmsg = "Please agree to the Terms of Service"; 
 var op_continue_link = ""; 
 var opc_cancel = "Cancel"; 
 var op_dontloadajax = false;  var op_user_name_checked = false;  var op_email_checked = false;  
 var op_vendor_name = ""; 
 var op_order_total = 0; 
 var op_total_total = 0; 
 var op_ship_total = 0; 
 var op_tax_total = 0; 
var op_fix_payment_vat = false;  var op_run_google = new Boolean(false);  var op_always_show_tax = false; 
 var op_always_show_all = false; 
 var op_add_tax = false;  var op_add_tax_to_shipping = false; 
 var op_add_tax_to_shipping_problem = false; 
 var op_no_decimals = false; 
 var op_curr_after = false; 
 var op_basket_subtotal_items_tax_only = 0.00;  var op_show_only_total = false; 
 var op_show_andrea_view = false; 
 var op_detected_tax_rate = "0";  var op_custom_tax_rate = "0.00"; 
 var op_coupon_discount_txt = "Coupon Discount"; 
 var op_other_discount_txt = "Other discount"; 
 var op_shipping_inside_basket = false;  var op_payment_inside_basket = false;  var op_disabled_payments = ""; 
var op_payment_discount = 0; 
 var op_ship_cost = 0; 
 var pdisc = []; 
var op_payment_fee_txt = "Payment Fee"; 
var op_payment_discount_txt = "Discount"; 
 var op_paypal_id = "x";  var op_paypal_direct = false;  
			';
		
		$js .= $extJs; 
			$document = JFactory::getDocument();
			$document->addScriptDeclaration($js); 
		$js_last = '<script>window.checkout = Hikaonepage;</script>'; 
		return $js_last;
		
	}
	public static function getCSS() {
		$selected = OPChikarenderer::getSelectedTemplate(); 
		JHTMLOPC::stylesheet('onepage.css', 'components/com_onepage/themes/'.$selected.'/', false);
	}
	
	
	
}