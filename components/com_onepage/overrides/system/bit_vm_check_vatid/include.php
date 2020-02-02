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
*	  BIT VAT COMPATIBILITY FILE
*/

		 JHTMLOPC::script('jquery.base64.js', $plugin_short_path.'js/', false);
		 JHTMLOPC::script('bitvatidchecker.js', $plugin_short_path.'js/', false);
	$db = JFactory::getDBO(); 	 
	$app = JFactory::getApplication('site');
	$plugin = JPluginHelper::getPlugin('system', 'bit_vm_check_vatid');
	if (!empty($plugin))
	{
	$pluginParams = new JRegistry();
	$pluginParams->loadString($plugin->params);
	
	$euvatid_field_name = $pluginParams->get('euvatid_field_name','EUVatID');

	$validation_method = $pluginParams->get('validation_method',2);

	$error_msg = base64_encode($pluginParams->get('error_msg_invalid_id','Invalid VAT number'));

	$error_msg_country_mismatch = base64_encode($pluginParams->get('error_msg_country_mismatch',"The VAT ID you've entered doesn't match your country."));

	$prefix_invalid = base64_encode($pluginParams->get('invalid_prefix',"[invalid: ]"));

	$current_url=JURI::current();
	
	$euvat = $pluginParams->get('euvatid_field_name', 'EUVATnumber');
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
			$opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat'); 
	$session = JFactory::getSession(); 
	$vatids = $session->get($opc_vat_key, array());
	
	if (!is_array($vatids))
	$vatids = @json_decode($vatids, true); 
	
	
	$vatids['field'] = (string)$euvat; 
	$s = json_encode($vatids); 
	
	$vatids = $session->set($opc_vat_key, $s);
	
	
			if (!defined('bit_included'))
			{
		define('bit_included', 1); 
//http://vm2onj25.rupostel.com/index.php?option=com_onepage&nosef=1&task=opc&view=opc&format=opchtml&tmpl=component&op_onlyd=&lang=
	$bitjs = '
//<![CDATA[
	bit_error_msg = "'.$error_msg.'";
	bit_error_msg_country_mismatch = "'.$error_msg_country_mismatch.'"; 
	bit_validation_method = "'.$validation_method.'"; 
	bit_euvatid_field_name = "'.$euvatid_field_name.'"; 
	bit_prefix_invalid = "'.$prefix_invalid.'";  
	
	bit_current_url = "'.OPCloader::getUrl().'index.php?option=com_onepage&task=opc&tmpl=component&cmd=checkbitvat&format=opchtml&nosef=1&view=opc'.'"; 
	
	function opc_bit_check_vatid(el)
	{
	   //Onepage.validateBitVat(el);
	   //return;
		if (el.name.toString().indexOf(\'shipto_\')<0)
		{
		if (typeof document.userForm != \'undefined\')
		{
			 document.userForm[bit_euvatid_field_name] = document.getElementById(bit_euvatid_field_name+\'_field\'); 
			 document.userForm[\'virtuemart_country_id\'] = document.getElementById(\'virtuemart_country_id\'); 
		}
		else
		{
			 document.userForm = new Array(); 
			 document.userForm[bit_euvatid_field_name] = document.getElementById(bit_euvatid_field_name+\'_field\'); 
			 document.userForm[\'virtuemart_country_id\'] = document.getElementById(\'virtuemart_country_id\'); 
		}
		
		var country = Onepage.op_getSelectedCountry(); 
		// remove country if it was already set: 
		var bit_current_url_a = bit_current_url.split(\'&virtuemart_country_id\'); 
		if (bit_current_url_a.length > 0)
		 {
		   bit_current_url = bit_current_url_a[0]; 
		 }
		bit_current_url += \'&virtuemart_country_id=\'+country; 
		
	   bit_check_vatid(el.value, bit_euvatid_field_name, \'bill\', country, bit_error_msg, bit_current_url, bit_validation_method, bit_error_msg_country_mismatch, bit_prefix_invalid); 
		}
		else
		{
if (el.name.toString().indexOf(\'shipto_\')<0)
		{
		if (typeof document.userForm != \'undefined\')
		{
			 document.userForm[\'shipto_\'+bit_euvatid_field_name] = document.getElementById(\'shipto_\'+bit_euvatid_field_name+\'_field\'); 
			 document.userForm[\'shipto_virtuemart_country_id\'] = document.getElementById(\'shipto_virtuemart_country_id\'); 
		}
		else
		{
			 document.userForm = new Array(); 
			 document.userForm[\'shipto_\'+bit_euvatid_field_name] = document.getElementById(\'shipto_\'+bit_euvatid_field_name+\'_field\'); 
			 document.userForm[\'shipto_virtuemart_country_id\'] = document.getElementById(\'shipto_virtuemart_country_id\'); 
		}
		
	   bit_check_vatid(el.value, bit_euvatid_field_name, \'ship\', Onepage.op_getSelectedCountry(), bit_error_msg, bit_current_url, bit_validation_method, bit_error_msg_country_mismatch, bit_prefix_invalid); 			
		}
	    }
	   //setTimeout(function () {
        	Onepage.op_runSS(null, false, true, \'refresh-totals\');
		//	}, 4000);
	   return true; 
	   
	}
	
//]]>
	'; 
	$document = JFactory::getDocument();
	
	if (method_exists($document, 'addScriptDeclaration'))
	$document->addScriptDeclaration($bitjs);
		}
	$html = str_replace('id="'.$euvat.'_field"', ' id="'.$euvat.'_field" onblur="javascript: opc_bit_check_vatid(this);"  ', $html); 
    #bit_check_vatid(shipto_"."$euvatid_field_name"."_field.value,'$euvatid_field_name','ship',shipto_virtuemart_country_id.value,'$error_msg','$current_url','$validation_method','$error_msg_country_mismatch', '$prefix_invalid');\""
	}