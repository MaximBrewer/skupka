<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/*
*      One Page Checkout configuration file
*      Copyright RuposTel s.r.o. under GPL license
*      Version 2 of date 31.March 2012
*      Feel free to modify this file according to your needs
*
*
*     @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
*     @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*     One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
*     VirtueMart is free software. This version may have been modified pursuant
*     to the GNU General Public License, and as distributed it includes or
*     is derivative of works licensed under the GNU General Public License or
*     other free or open source software licenses.
* 
*/





		  if (!class_exists('VmConfig'))
		  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		  VmConfig::loadConfig(); 

 $opc_vat_field = 'opc_vat';  $one_or_the_other = array(); $one_or_the_other2 = array();  $op_color_texts = array();  $op_color_texts[0] = "COM_VIRTUEMART_PRODUCT_IN_STOCK";  $op_color_texts[1] = "COM_VIRTUEMART_CART_PRODUCT_OUT_OF_STOCK";  $op_color_texts[2] = "COM_VIRTUEMART_SEARCH_ORDER_LOW_STOCK_NOTIFICATION";  $op_color_texts[3] = "COM_ONEPAGE_QUANTITY_REQUESTED_LARGER_THAN_AVAILABLE_STOCK";  $op_color_codes = array();  $op_color_codes[0] = "#008000";  $op_color_codes[1] = "#ff0000";  $op_color_codes[2] = "#0000ff";  $op_color_codes[3] = "#000000";  $opc_cr_type = 'save_all';  $bt_fields_from = '1';  $op_default_shipping_search = array(); $use_original_basket = false; 
    $theme_fix1 = false; 
    $opc_override_registration = true; 
    $opc_no_activation = false; 
    $disable_check = false; 
    $opc_php_js2 = true;
    $op_shipto_opened_default = false; 
    $only_one_shipping_address_hidden = false; 
    $opc_only_parent_links = false; 
    $opc_dynamic_lines = false; 
    $opc_editable_attributes = false; 
    $op_customer_shipping = false; 
    $allow_sg_update = false; 
    $allow_sg_update_logged = false; 
    $do_not_allow_gift_deletion = false; 
     $gift_order_statuses = array(); 
 $reuse_order_statuses = array('P');
$opc_async = false; 
    $use_free_text = false; 
    $disable_onepage = false; 
    $opc_italian_checkbox = false; 
    $opc_acymailing_checkbox = false; 
     $opc_acy_id = (int)"2"; $opc_do_not_alter_registration = false; 
     $opc_memory = '128M';  $rupostel_email = ''; $opc_plugin_order = '-9999';
    $opc_disable_for_mobiles = false; 
    $opc_request_cache = false; 
    $opc_check_username = false;$opc_rtl = false;$opc_no_duplicit_username = false; 
    $klarna_se_get_address = false;$ajaxify_cart = false;$opc_check_email = false;$opc_no_duplicit_email = false; 
    $show_single_tax = true;
    $opc_calc_cache = false; 
    $visitor_shopper_group = 0; 
    $no_coupon_ajax = false; 
    $business_shopper_group = 0; 
    $zero_total_status = "C";
    $option_sgroup = false; 
    $op_never_log_in = false; 
    $allow_duplicit = true;
      $no_alerts = false; 
    $disable_ship_to_on_zero_weight = false; 
    $op_use_geolocator = false; 
    $append_details = false; 
    $op_redirect_joomla_to_vm = false; 
    $password_clear_text = false; 
     $dpps_search = array(); $dpps_disable = array(); $dpps_default=array(); 
 $disable_payment_per_shipping = false; $euvat_shopper_group = 0; 
    $payment_discount_before = false; 
    $only_one_shipping_address = false; 
    $no_extra_product_info = false; 
    $enable_captcha_reg = true;
    $send_pending_mail = false; 
    $enable_captcha_logged = false; 
    $hide_advertise = false; 
    $hide_payment_if_one = false; 
    
/* If user in Optional, normal, silent registration sets email which already exists and is registered 
* and you set this to true
* his order details will be saved but he will not be added to joomla registration and checkout can continue
* if registration type allows username and password which is already registered but his new password is not the same as in DB then checkout will return error
*/
$email_after = false;
      $opc_link_type = 0;
       $business_fields = array();  $custom_rendering_fields = array();  $per_order_rendering = array('customer_note','tos');  $opc_ajax_fields = array('address_1','address_2','virtuemart_country_id','virtuemart_state_id','zip');  $admin_shopper_fields = array();  $render_as_hidden = array();  $render_in_third_address = array();  $html5_fields = array();  $html5_fields[register_account] = 'test' $html5_fields[delimiter_sendregistration] = '' $html5_fields[email] = '' $html5_fields[username] = '' $html5_fields[name] = '' $html5_fields[password] = '' $html5_fields[password2] = '' $html5_fields[agreed] = '' $html5_fields[customer_note] = '' $html5_fields[tos] = '' $html5_fields[delimiter_userinfo] = '' $html5_fields[address_type_name] = '' $html5_fields[company] = '' $html5_fields[first_name] = '' $html5_fields[delimiter_billto] = '' $html5_fields[DelimiterTest1] = '' $html5_fields[address_1] = '' $html5_fields[address_2] = '' $html5_fields[city] = '' $html5_fields[virtuemart_country_id] = '' $html5_fields[virtuemart_state_id] = '' $html5_fields[zip] = '' $html5_fields[phone_1] = '' $html5_fields[DelimiterTest] = '' $html5_fields[opc_vat] = '' $html5_fields[opc_vat_info] = '' $html5_fields[picked_delivery_date] = '' $html5_fields[city-1] = '' $html5_fields[confezione_regalo] = '' $html5_fields[tempfield] = '' $html5_fields[Kundengruppe] = '' $html5_fields[rf] = '' $html5_fields[multicheckboxtest] = '' $html5_fields_extra = array();  $html5_fields_extra[register_account] = 'pattern%3D%22%5E%5Cd%7B4%7D-%5Cd%7B3%7D-%5Cd%7B4%7D%24%22';  $html5_fields_extra[delimiter_sendregistration] = '';  $html5_fields_extra[email] = '';  $html5_fields_extra[username] = '';  $html5_fields_extra[name] = '';  $html5_fields_extra[password] = '';  $html5_fields_extra[password2] = '';  $html5_fields_extra[agreed] = '';  $html5_fields_extra[customer_note] = '';  $html5_fields_extra[tos] = '';  $html5_fields_extra[delimiter_userinfo] = '';  $html5_fields_extra[address_type_name] = '';  $html5_fields_extra[company] = '';  $html5_fields_extra[first_name] = '';  $html5_fields_extra[delimiter_billto] = '';  $html5_fields_extra[DelimiterTest1] = '';  $html5_fields_extra[address_1] = '';  $html5_fields_extra[address_2] = '';  $html5_fields_extra[city] = '';  $html5_fields_extra[virtuemart_country_id] = '';  $html5_fields_extra[virtuemart_state_id] = '';  $html5_fields_extra[zip] = '';  $html5_fields_extra[phone_1] = '';  $html5_fields_extra[DelimiterTest] = '';  $html5_fields_extra[opc_vat] = '';  $html5_fields_extra[opc_vat_info] = '';  $html5_fields_extra[picked_delivery_date] = '';  $html5_fields_extra[city-1] = '';  $html5_fields_extra[confezione_regalo] = '';  $html5_fields_extra[tempfield] = '';  $html5_fields_extra[Kundengruppe] = '';  $html5_fields_extra[rf] = '';  $html5_fields_extra[multicheckboxtest] = '';  $shipping_obligatory_fields = array();  $business_obligatory_fields = array(); $op_disable_shipping = false;
      $op_disable_shipto = false;
      $op_no_display_name = false;
      $op_create_account_unchecked = false;
       $product_price_display = "salesPrice";
 $subtotal_price_display = "product_subtotal";
 $opc_usmode = false;  $full_tos_logged = false;  $tos_scrollable = false;  $full_tos_unlogged = false;  $tos_logged = true;  $tos_unlogged = true;  $opc_email_in_bt = false;  $double_email = false;  $coupon_price_display = "salesPriceCoupon";
 $other_discount_display = "billDiscountAmount";
$agreed_notchecked = true;
      
	   $op_default_shipping_zero = false;
	   $opc_default_shipping = 0;
       $shipping_inside_choose = false;
	  $never_count_tax_on_shipping = false;
      $save_shipping_with_tax = false;
      $op_no_basket = false;
      $shipping_template = true;
       $currency_per_lang = array(); $op_sum_tax = false;
      $op_last_field = false;
      $op_default_zip = "11111"; 
	$op_numrelated = "5"; 
      
// auto config by template
$cut_login = false;
      $op_delay_ship = false;
      $op_loader = true;
      $op_usernameisemail = false;
      $no_continue_link_bottom = false;
      $op_default_state = false;
      $list_userfields_override = false;
      $no_jscheck = true;
      $op_dontloadajax = false;
      $shipping_error_override = "ERROR";
      $op_zero_weight_override = false;
      $email_after = false;
      $override_basket = false;
      
	  
	  require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	 
	  $selected_template = OPCmini::getSelectedTemplate('icetheme_thestore', ''); 
	  

		
       $adwords_timeout = 4; $dont_show_inclship = false;
      $no_continue_link = true;
      $no_login_in_template = false;
      $shipping_inside = false;
      $payment_inside = false;
      $payment_saveccv = false;
      $payment_advanced = false;
      $fix_encoding = false;
      $fix_encoding_utf8 = false;
      $shipping_inside_basket = false;
      $payment_inside_basket = false;
      $email_only_pok = false;
      $no_taxes_show = false;
      $use_order_tax = false;
      $no_taxes = false;
      $never_show_total = false;
      $email_dontoverride = false;
      $show_only_total = false;
      $show_andrea_view = false;
      $always_show_tax = false;
$always_show_all = false;
$add_tax = false;
      $add_tax_to_shipping_problem = false;
      $add_tax_to_shipping = false;
      $custom_tax_rate = 0;
      $opc_auto_coupon = ""; 
      $no_decimals = false;$curr_after = false;$load_min_bootstrap = false;
/*
Set this to true to unlog (from Joomla) all shoppers after purchase
*/
$unlog_all_shoppers = false;
$vat_input_id = ""; $eu_vat_always_zero = ""; $vat_except = ""; $move_vat_shopper_group = "";  $zerotax_shopper_group = array();  
/* set this to true if you don't accept other than valid EU VAT id */
$must_have_valid_vat = false; 
/*
* Set this to true to unlog (from Joomla) all shoppers after purchase
*/
 $unlog_all_shoppers = false;
     
/* This will disable positive messages on Thank You page in system info box */


/* please check your source code of your country list in your checkout and get exact virtuemart code for your country
* all incompatible shipping methods will be hiddin until customer choses other country
* this will also be preselected in registration and shipping forms
* Your shipping method cannot have 0 index ! Otherwise it will not be set as default
*/     
 $default_shipping_country = "default";
      
/* since VM 1.1.5 there is paypal new api which can be clicked on image instead of using checkout process
* therefore we can hide it from payments
* These payments will be hidden all the time
* example:  $payments_to_hide = "4,3,5,2";
*/

/* default payment option id
* leave commented or 0 to let VM decide
*/
$payment_default = '0';
	
/* turns on google analytics tracking, set to false if you don't use it */

/* set this to false if you don't want to show full TOS
* if you set show_full_tos, set this variable to one of theses:
* use one of these values:
* 'shop.tos' to read tos from your VirtueMart configuration
* '25' if set to number it will search for article with this ID, extra lines will be removed automatically
* both will be shown without any formatting
*/
 $op_fix_payment_vat = false; 
 $op_free_shipping = false; 

/* change this variable to your real css path of '>> Proceed to Checkout'
* let's hide 'Proceed to checkout' by CSS
* if it doesn't work, change css path accordingly, i recommend Firefox Firebug to get the path
* but this works for most templates, but if you see 'Proceed to checkout' link, contact me at stan@rupostel.sk
* for rt_mynxx_j15 template use '.cart-checkout-bar {display: none; }'
*/

$payment_info = array();
$payment_button = array();
$default_country_array = array();

 /* URLs fetched after checkout encoded by base64_encode */
 $curl_url = array();

	
	
if (class_exists('OPCmini'))
{
jimport('joomla.filesystem.file');
$selected_template = JFile::makeSafe($selected_template); 
if (!empty($selected_template) && (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$selected_template.DIRECTORY_SEPARATOR."overrides".DIRECTORY_SEPARATOR."onepage.cfg.php")))
{
  
  include(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$selected_template.DIRECTORY_SEPARATOR."overrides".DIRECTORY_SEPARATOR."onepage.cfg.php");
 
}
}
	



