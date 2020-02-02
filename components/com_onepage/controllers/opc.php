<?php 
/**
* Controller for the OPC ajax and checkout
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
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 
require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'language.php'); 
require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 


jimport('joomla.application.component.controller');
class VirtueMartControllerOpc extends OPCController {
	/**
	* Construct the cart
	*
	* @access public
	* @author Max Milbers
	*/
	public static $ok_arr; 
	private static $last_vat_address; 
	public static $isjson; 
	public function __construct() {
		parent::__construct();
		{
			if (!defined('JPATH_OPC')) define('JPATH_OPC', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'); 
			if (!class_exists('VirtueMartCart'))
			require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'cart.php');
			if (!class_exists('calculationHelper'))
			require(JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers' .DIRECTORY_SEPARATOR. 'calculationh.php');
			
			self::$ok_arr = array(); 
			
		}
	}
	
	
	
	public function returnTerminate($msg='', $layout='', $url='', $type='error', $terminate=true)
	{
		
		
		
		if (!isset(VirtueMartControllerOpc::$isjson)) VirtueMartControllerOpc::$isjson = false; 
		if (!empty(VirtueMartControllerOpc::$isjson)) {
			
			if (is_array($msg)) {
				return $this->printJson($msg); 
			}
			else {
				$arr = array(); 
				$arr['error'] = $msg; 
				return $this->printJson($msg); 
			}
			
		}
		
		if (empty($url))
		$url = 'index.php?option=com_virtuemart&view=cart&error_redirect=1'; 
		$lang = OPCloader::getLangCode(); 
		if (!empty($lang))
		$url .= '&lang='.$lang; 
		if (!empty($layout)) $url .= '&layout='.$layout; 
		$app = JFactory::getApplication(); 
		
		if (strpos($url, 'view=cart')!==false) {
		 $newitemid = OPCconfig::getValue('opc_config', 'newitemid', 0, 0, true); 
		 if (!empty($newitemid)) $url .= '&Itemid='.$newitemid; 
		}
		
		$u = JRoute::_($url, FALSE, VmConfig::get('useSSL', false) ); 
		
		
		
		if (!empty($msg))
		OPCloader::storeError($msg); 
		
		if (!empty($msg))
		JFactory::getApplication()->enqueueMessage($msg,  $type); 
		$app->redirect($u, false, VmConfig::get('useSSL', false)); 
		$app->close(); 
		return false; 
	}
	
	private function getVendorDataStored(&$data)
	{
		// Store multiple selectlist entries as a ; separated string
		if (key_exists('vendor_accepted_currencies', $data) && is_array($data['vendor_accepted_currencies'])) {
			$data['vendor_accepted_currencies'] = implode(',', $data['vendor_accepted_currencies']);
		}

		$data['vendor_store_name'] = JRequest::getVar('vendor_store_name','','post','STRING',JREQUEST_ALLOWHTML);
		$data['vendor_store_desc'] = JRequest::getVar('vendor_store_desc','','post','STRING',JREQUEST_ALLOWHTML);
		$data['vendor_terms_of_service'] = JRequest::getVar('vendor_terms_of_service','','post','STRING',JREQUEST_ALLOWHTML);
		$data['vendor_letter_css'] = JRequest::getVar('vendor_letter_css','','post','STRING',JREQUEST_ALLOWHTML);
		$data['vendor_letter_header_html'] = JRequest::getVar('vendor_letter_header_html','','post','STRING',JREQUEST_ALLOWHTML);
		$data['vendor_letter_footer_html'] = JRequest::getVar('vendor_letter_footer_html','','post','STRING',JREQUEST_ALLOWHTML);
	}
	
	
	public function clearcart()
	{
		if ((!defined('VM_VERSION')) || (VM_VERSION < 3)) 
		{
			$session = JFactory::getSession();
			$session->set('vmcart', 0, 'vm');
			return; 
		}
		if(!class_exists('VirtueMartCart')) require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cart.php');
		$cart = VirtueMartCart::getCart();
		
		foreach ($cart->cartProductsData as $prod_id=>$val) {
			if(!class_exists('vmCustomPlugin')) require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmcustomplugin.php');
			JPluginHelper::importPlugin('vmcustom');
			$dispatcher = JDispatcher::getInstance();
			$addToCartReturnValues = $dispatcher->trigger('plgVmOnRemoveFromCart',array($cart,$prod_id));
		}
		
		$session = JFactory::getSession(); 
		$session->set('opc_last_coupon', ''); 
		$cart->couponCode = ''; 
		$opc_debug = OPCconfig::get('opc_debug', false);  if (!empty($opc_debug)) { JFactory::getApplication()->enqueueMessage('OPC Debug: Coupon removed at '.__FILE__.':'.__LINE__); }
		
		$st = $cart->ST; 
		$bt = $cart->BT;  
		$cart->products = array(); 
		$cart->cartData = array();  
		$cart->cartPrices = array(); 
		$cart->virtuemart_cart_id = 0; 
		$cart->cartProductsData = array(); 
		
		$data = $cart->getCartDataToStore(); 
		$data  = json_encode($data); 
		
		$session = JFactory::getSession();
		$session->set('vmcart', $data, 'vm');
		$cart = VirtueMartCart::getCart(true, array(), $data );
		
		// we also must remove it from the DB, otherwise it's reloaded automatically per logged in user
		if (method_exists($cart, 'deleteCart'))
		$cart->deleteCart(); 
		
		return $this->returnTerminate(); 
		
		//http://vm2.rupostel.com/purity/index.php?option=com_onepage&view=opc&task=clearcart
		
	}
	public function printJson($return=array(), $terminate=true) {
		
		$x = array(); 
		$x[] = @ob_get_clean(); $x[] = @ob_get_clean(); $x[] = @ob_get_clean(); $x[] = @ob_get_clean(); 
		
		if (isset($return['html'])) $return['html'].implode(' ',$x); 
		
		$retjson = json_encode($return); 
		
		if (function_exists('json_last_error_msg'))
		$e = json_last_error_msg(); 
		if (function_exists('json_last_error'))
		$e2 = json_last_error(); 
		
		if ((!empty($e2)))
		{
			
			if (!empty($e2))
			{
				switch ($e2) {
				case JSON_ERROR_DEPTH:
					$e2 = 'Json  - Maximum stack depth exceeded';
					break;
				case JSON_ERROR_STATE_MISMATCH:
					$e2 = 'Json  - Underflow or the modes mismatch';
					break;
				case JSON_ERROR_CTRL_CHAR:
					$e2 = 'Json  - Unexpected control character found';
					break;
				case JSON_ERROR_SYNTAX:
					$e2 = 'Json  - Syntax error, malformed JSON';
					break;
				case JSON_ERROR_UTF8:
					$e2 = 'Json  - Malformed UTF-8 characters, possibly incorrectly encoded';
					break;
				default:
					$e2 = 'Json Unknown error';
					break;
				}
			}	
			
			
			if (empty($retjson))
			{
				
			}
			else
			{
				if (empty($e)) $e = ''; 
				
				$r2 = json_decode($retjson, true); 
				
				if (empty($r2['msgs'])) $r2['msgs'] = array(); 
				$r2['msgs'][] = $e2.' '.$e; 
				if (empty($r2['shipping'])) $r2['shipping'] = $e2.' '.$e; ; 
				
				
				
				
				$retjson = json_encode($r2);
			}
			
			
		}
		
		if (empty($retjson))
		{
			foreach ($return as $k=>$v)
			{
				$return[$k] = utf8_encode($v); 
			}
			$retjson = json_encode($return); 
			
		}
		
		
		
		
		
		
		

		echo $retjson; 
		if (!empty($terminate)) {
		 $app = JFactory::getApplication(); 
		 $app->close(); 
		}
		return false; 
	}
	public static function checkOPCVat(&$vatid='', $virtuemart_country_id=0, &$ret=true, $company='')
	{
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vat.php'); 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		
		
		
		
		self::$last_vat_address = array(); 
		
		//COM_ONEPAGE_VAT_CHECKER_DOWN="EU validation service is currently not available for your country. Please try again later."
		//COM_ONEPAGE_VAT_CHECKER_INVALID="Invalid VAT number"
		//COM_ONEPAGE_VAT_CHECKER_INVALID_COUNTRY="The VAT ID you've entered doesn't match your country."
		$error0 = 'COM_ONEPAGE_VAT_CHECKER_INVALID'; 
		$error2 = 'COM_ONEPAGE_VAT_CHECKER_INVALID_COUNTRY'; 
		$error1 = 'COM_ONEPAGE_VAT_CHECKER_DOWN'; 
		
		
		$opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat'); 
		
		if (empty($vatid))
		$vatid = JRequest::getVar($opc_vat_key); 
		
		
		
		$virtuemart_country_id = (int)$virtuemart_country_id; 
		
		if (empty($vatid)) OPCvat::returnVatResp('', false, '', '', '', $vatid); 
		
		$_orig_input = $vatid; 
		$vat_id = strtoupper($vatid); 
		
		
		
		$c = substr($vatid, 0, 2); 
		$c = strtoupper($c); 
		
		
		if ($c === 'GR') $c = 'EL'; 
		if ($c === 'UK') $c = 'GB'; 
		
		include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php');
		
		
		if (empty($virtuemart_country_id))
		{
			
			$stopen = JRequest::getVar('shiptoopen', 0); 
			if ($stopen === 'false') $stopen = 0; 
			if (empty($stopen)) 
			{
				$sa = JRequest::getVar('sa', ''); 
				if ($sa == 'adresaina') $stopen = 1; 
			}
			
			
			if (!empty($stopen))
			{
				$virtuemart_country_id = JRequest::getVar('shipto_virtuemart_country_id', 0); 
			}
			else
			{
				$virtuemart_country_id = JRequest::getVar('virtuemart_country_id', 0); 
			}
		}
		
		
		
		
		// our shipping country_2_code
		$country_2_code = $virtuemart_country_2_code = OPCvat::getCountry($virtuemart_country_id); 
		$opc_euvat_contrymatch = OPCconfig::get('opc_euvat_contrymatch', false); 		   
		
		
		
		
		
		$home_vat_countries = OPCconfig::get('home_vat_countries', ''); 
		
		$home = explode(',', $home_vat_countries); 
		$list = array(); 
		if (is_array($home))
		{
			foreach ($home as $k=>$v)
			{
				$list[] = strtoupper(trim($v)); 
			}
			
		}
		
		
		
		if ((!in_array($country_2_code, OPCVat::$european_states)))
		{
			// return no error when the country is not in EU
			$ret = VAT_STATUS_COUNTRY_NOT_IN_EU; 
			return '';
		}
		
		//if the VAT ID already includes the country: 
		if (!empty($c) && (in_array($c, OPCVat::$european_states)))
		{
			$vat_id = substr($vat_id, 2); 
		}
		if (!empty($c) && (!in_array($c, OPCVat::$european_states))) {
			$addprefix = true; 
		}
		
		
		if (empty($c) || (!in_array($c, OPCVat::$european_states)))
		{
			$c = $country_2_code; 
		}
		
		if ((!in_array($c, OPCVat::$european_states)) && ((empty($opc_euvat_contrymatch)))) 
		{
			
			if (in_array($country_2_code, $list))
			{
				$ret = VAT_STATUS_COUNTRY_HOME; 
				return '';
			}

			
			// return no error when the country is not in EU
			$ret = VAT_STATUS_COUNTRY_NOT_IN_EU; 
			return '';
		}
		
		
		//if vat id prefix is inside EU
		if ((in_array($c, OPCVat::$european_states)) )
		{
			
			// but does not match the country provided, give an error, or a silent error:
			if (!empty($opc_euvat_contrymatch))
			{
				if (($c != $country_2_code) && ($c !== 'EU'))
				{
					
					$ret = VAT_STATUS_COUNTRY_ERROR; 
					
					return OPCvat::returnVatResp($error2, false, $_orig_input, $country_2_code, '',$_orig_input); 
				}
			}
			else
			{
				
				
				if (($c != $country_2_code) && ($c !== 'EU'))
				{
					
					$ret = VAT_STATUS_COUNTRY_ERROR; 
					//silently fail with country match error
					return OPCvat::returnVatResp('', false, $_orig_input, $country_2_code, '',$_orig_input); 
				}
			}
			
		}
		
		
		
		
		
		$country_2_code = $c; 
		
		if ($country_2_code === 'GR') $country_2_code = 'EL'; 
		if ($country_2_code === 'UK') $country_2_code = 'GB'; 
		
		
		
		OPCloader::opcDebug(__LINE__.':'.$country_2_code, 'eu_vat'); 
		OPCloader::opcDebug(__LINE__.':'.$vat_id, 'eu_vat'); 
		
		
		
		if (!in_array($country_2_code, OPCVat::$european_states))
		{
			// return no error when the country is not in EU
			$ret = VAT_STATUS_COUNTRY_NOT_IN_EU2; 
			return '';
		}
		
		if (empty($company))
		{
			$company = ''; 
		}
		$e = ''; 
		
		if (!empty($home_vat_num))
		$requester = $home_vat_num; 
		else $requester = ''; 
		if (empty($company)) {
			$company = JRequest::getVar('company', ''); 
		}
		$ise = OPCVat::detectVatCache($virtuemart_country_id, $_orig_input, false);
		
		
		$imk = $country_2_code.$vat_id;
		
		$again = false; 
		if (empty(OPCvat::$requestIdentifier[$imk])) $again = true; 
		
		if (($ise === false) || ($ise === VAT_STATUS_SOAP_ERROR) || ($again))
		{
			
			$result = OPCVat::isVIESValidVAT($country_2_code, $vat_id, $company, $e, $requester); 
			
			
			OPCloader::opcDebug(__LINE__.':'.var_export($result, true).' vat_id:'.$vat_id.' c2c:'.$country_2_code.' company:'.$company.' requester:'.$requester, 'eu_vat'); 
			
		}
		else
		{
			$result = $ise; 
			OPCloader::opcDebug(__LINE__.' from cache:'.$result.' vat_id:'.$country_2_code.$vat_id.'  company:'.$company.' requester:'.$requester, 'eu_vat'); 
		}
		
		$mk = $imk; 
		
		
		if (($country_2_code === 'EU') && ($result === VAT_STATUS_SOAP_ERROR))
		{
			
			if (in_array($country_2_code, $list))
			{
				$ret = VAT_STATUS_COUNTRY_HOME; 
				return '';
			}
			
			// consider all EU numbers that return soap error as valid... 
			$ret = VAT_STATUS_VALID; 
			$vatid3 = strtoupper($vatid); 
			$vatid3 = preg_replace("/[^a-zA-Z0-9]/", "", $vatid3);
			$mk2 = preg_replace("/[^a-zA-Z0-9]/", "", $mk);
			
			$a = array($vatid3, $mk2, $_orig_input, strtoupper($mk), substr($vatid3, 0, 2)); 
			
			
			
			return ''; 
		}
		
		// we've got a cache response
		//if ($result !== true)
		/*
			{
			if (!empty($vat_id))
			{
			$mk = $vat_id; 
			
			$vat_id = strtoupper($vat_id); 
			$vat_id = preg_replace("/[^a-zA-Z0-9]/", "", $vat_id);
			$starts = substr($vat_id, 0, 2); 
			
			if ($result === VAT_STATUS_VALID)
			if (!in_array($starts, OPCVat::$european_states))
			{
			$mk = $country_2_code.$vat_id;
			JRequest::setVar('opc_vat', $mk); 
			}
			
			OPCloader::opcDebug(__LINE__.' mk:'.$mk, 'eu_vat'); 
			}
			else
			{
				$mk = ''; 
			}
			
			}
			*/
		
		
		if ($result !== VAT_STATUS_SOAP_ERROR) {
			$vatid3 = strtoupper($vatid); 
			$vatid3 = preg_replace("/[^a-zA-Z0-9]/", "", $vatid3);
			$zmk = preg_replace("/[^a-zA-Z0-9]/", "", $mk);
			
			$a = array($vatid3, $zmk, $_orig_input, strtoupper($zmk), substr($vatid3, 0, 2), $imk); 
			
			$opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat'); 
			OPCvat::setVatCache($opc_vat_key, $virtuemart_country_id, $a, $result); 
		}
		
		if ($result === VAT_STATUS_NOT_VALID) 
		{
			

			OPCloader::opcDebug(__LINE__.':VAT_STATUS_NOT_VALID:'.$msg.' _orig_input:'.$_orig_input.' c2c:'.$country_2_code, 'eu_vat'); 
			
			if (in_array($country_2_code, $list))
			{
				
				$ret = VAT_STATUS_COUNTRY_HOME; 
				return OPCvat::returnVatResp('', VAT_STATUS_COUNTRY_HOME, $_orig_input, $country_2_code, false, $mk); 
			}
			
			

			
			$ret = VAT_STATUS_NOT_VALID;
			return OPCvat::returnVatResp($error0, VAT_STATUS_NOT_VALID, $_orig_input, $country_2_code, false, $mk); 
		}
		
		if ($result === VAT_STATUS_SOAP_ERROR) 
		{
			
			if (in_array($country_2_code, $list))
			{
				$ret = VAT_STATUS_COUNTRY_HOME; 
				return OPCvat::returnVatResp('', VAT_STATUS_COUNTRY_HOME, $_orig_input, $country_2_code, false, $mk); 
			}

			
			if (stripos($e, 'INVALID_INPUT')!==false) 
			{
				
				$ret = VAT_STATUS_NOT_VALID; 
				return OPCvat::returnVatResp($error0, VAT_STATUS_NOT_VALID, $_orig_input, $country_2_code, $e, $mk); 
			}

			$ret = VAT_STATUS_SOAP_ERROR; 
			return OPCvat::returnVatResp($error1, VAT_STATUS_SOAP_ERROR, $_orig_input, $country_2_code, $e, $mk); 
			
		}
		
		
		
		
		
		
		
		$rt = $result; 
		if (!empty(OPCvat::$requestIdentifier[$mk]))
		{
			if (!empty(OPCvat::$requestIdentifier[$mk]['requestIdentifier']))
			$suffix = ' (ID:'.OPCvat::$requestIdentifier[$mk]['requestIdentifier'].')'; 
			
			
			foreach (OPCvat::$requestIdentifier[$mk] as $a => $k)
			{
				self::$last_vat_address[$a] = $k;
			}
			
			$rt = OPCvat::$requestIdentifier[$mk]; 
			
		}
		else 
		{
			$suffix = ''; 
		}
		
		
		//$country_2_code, $vat_id
		if ($_orig_input !== $imk) {
			$vatid = $imk; 
		}
		
		$ret = VAT_STATUS_VALID; 
		return OPCvat::returnVatResp('COM_ONEPAGE_VALIDATE_VAT_VALID', VAT_STATUS_VALID, $_orig_input, $country_2_code, $rt, $mk); 
		
		//return OPCLang::_('COM_ONEPAGE_VALIDATE_VAT_VALID').$suffix; 
	}
	
	private function checkVM2Captcha($retUrl, $isReg=false)
	{
		$id = JFactory::getUser()->get('id'); 
		$guest = JFactory::getUser()->guest; 
		
		if ($guest || empty($id)) $logged = false; 
		else $logged = true; 
		$recaptchar = false; 
		if(($guest || empty($id)) and VmConfig::get ('reg_captcha', false))
		{
			
			$recaptcha = JRequest::getVar ('recaptcha_response_field');
			JPluginHelper::importPlugin('captcha');
			$dispatcher = JDispatcher::getInstance();
			$res = $dispatcher->trigger('onCheckAnswer',$recaptcha);
			
			
			
			foreach ($res as $result)
			{
				$recaptchar = true; 		
				if($result === false){
					
					$data = JRequest::get('post');
					$data['address_type'] = JRequest::getVar('addrtype','BT');
					if(!class_exists('VirtueMartCart')) require(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cart.php');
					$cart = VirtueMartCart::getCart();
					//$cart->saveAddressInCart($data, $data['address_type']);
					
					if ($data['address_type'] === 'BT') $prefix = ''; 
					else $prefix = 'shipto_'; 
					
					if (defined('VM_VERSION') && (VM_VERSION >= 3))
					$cart->saveAddressInCart($data, $data['address_type'], true, $prefix);
					else
					$cart->saveAddressInCart($data, $data['address_type']);
					
					
					$mainframe = JFactory::getApplication();
					
					if (function_exists('vmText'))
					$errmsg = vmText::_('PLG_RECAPTCHA_ERROR_INCORRECT_CAPTCHA_SOL');
					else
					$errmsg = JText::_('PLG_RECAPTCHA_ERROR_INCORRECT_CAPTCHA_SOL');
					
					return $this->returnTerminate($errmsg, '', $retUrl); 
					
					
					
				} 
			}
		} 
		if (empty($recaptchar))
		{
			
			//include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
			
			$default = false; 
			
			if (empty($isReg)) {
				$enable_captcha_logged = OPCconfig::get('enable_captcha_logged', $default); 
				$enable_captcha_unlogged = OPCconfig::get('enable_captcha_unlogged', $default); 
			}
			else
			{
				$enable_captcha_reg = OPCconfig::get('enable_captcha_reg', $default); 
			}
			
			
			
			if (((!empty($enable_captcha_logged)) && ($logged)) || ((!empty($enable_captcha_unlogged)) && (!$logged)))
			{
				
				
				$this->checkOPCCaptcha($retUrl); 
			}
		}
		
	}
	
	
	
	function opcthird($be=false) {
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'third_address.php'); 
		OPCthirdAddress::opcthird($be, $this); 
		
		
		
		
	}
	
	function opcregister()
	{
		
		$user_id_o = JFactory::getUser()->get('id'); 
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php');
		OPCloader::setRegType(true); 
		
		
		  $lang = OPCloader::getLangCode(); 
		  if (!empty($lang)) $lang = '&lang='.$lang; 
		
		  $Itemid = JRequest::getInt('Itemid', 0, 'post'); 
		  if (!empty($Itemid)) $Itemid = '&Itemid='.$Itemid; 

		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'language.php'); 
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
		
		$selected_template = OPCrenderer::getSelectedTemplate();  
		
		$this->storePost(); 	
		
		
		OPCLang::loadLang(); 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'user.php'); 
		$admin_add_shopper_type = JRequest::getVar('admin_add_shopper_type', false); 
		if (!empty($admin_add_shopper_type))
		{ 
			return OPCUser::adminRegister(); 
			
		}

		
		$this->checkVM2Captcha('index.php?option=com_virtuemart&view=user', true); 
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
		
		$msg = ''; 
		
		if (!class_exists('VmConfig'))
		require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
		VmConfig::loadConfig(); 
		
		$data = JRequest::get('post');
		$userinfo_id = (int)JRequest::getInt('virtuemart_userinfo_id', 0); 
		$data['address_type'] = JRequest::getWord('addrtype',JRequest::getWord('address_type', 'BT'));
		if ($data['address_type'] == 'BT')
		{
			$prefix = ''; 
			$data['shipto_virtuemart_userinfo_id'] = null; 
		}
		else 
		{
			$prefix = 'shipto_'; 
			$data['shipto_virtuemart_userinfo_id'] = (int)$userinfo_id; 
		}
		
		$data['quite'] = false; 
		
		$adminmode= false; 

		// logged in users
		if (!empty($userinfo_id))
		{
			
			$q = 'select * from `#__virtuemart_userinfos` where virtuemart_userinfo_id = '.(int)$userinfo_id.' limit 0,1'; 
			$db = JFactory::getDBO(); 
			$db->setQuery($q); 
			$res = $db->loadAssoc();
			
			
			// if user is already registered:
			$user = JFactory::getUser(); 
			$uidc = (int)$user->get('id'); 
			$data['virtuemart_user_id'] = $uidc; 
			if (!empty($res))
			{
				$address_type = $res['address_type']; 
				$uid = (int)$res['virtuemart_user_id']; 
				
				if (!empty($uid))
				{
					// 1st security, user ids must match
					if ($uid != $uidc) 
					{
						
						
						require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
						if (!OPCmini::isSuperVendor())
						{
							$msg = 'OPC: Access Denied'; 
							$url = 'index.php?option=com_virtuemart&view=user&layout=edit'.$Itemid.$lang.'&error_redirect='.__LINE__; 
							return $this->returnTerminate($msg, '', $url); 		
						}
						else
						{
							$adminmode= true; 
						}
						
						
					}
					JRequest::setVar('virtuemart_user_id', $uid); 
					$data['virtuemart_user_id'] = $uid; 
				}
			}
			
			
			if (!$adminmode)
			{
				//if (empty($data['user_id']))
				{
					$data['user_id'] = $uidc; 
					$data['virtuemart_user_id'] = $uidc; 
					$data[$prefix.'user_id'] = $uidc; 
					$data[$prefix.'virtuemart_user_id'] = $uidc; 
				}
				
				$username = $user->get('username'); 
				$email = $user->get('email'); 
				if (empty($data['username']) && (!empty($username)))
				{
					$data['username'] = $username; 
					$data[$prefix.'username'] = $username; 
					
				}
				else
				if (empty($data['username']) && (!empty($email)))
				{
					$data['username'] = $email; 
					$data[$prefix.'username'] = $email; 
				}
				
				if (empty($data[$prefix.'email']))
				{
					$data[$prefix.'email'] = $email; 
				}
				$doUpdate = true; 
				
			}
			
			
			
			// address name override: 
			$lE = OPCUserFields::fieldExistsPublished('last_name', 'BT'); 
			$lE2 = OPCUserFields::fieldExistsPublished('first_name', 'BT'); 
			// handle first and last name if name field is available: 
			// parse name into first+last name
			// lastname field must be published
			if (!isset($data[$prefix.'first_name']) && (!isset($data[$prefix.'last_name'])) && (!empty($data[$prefix.'name'])))
			if (((in_array('last_name', $custom_rendering_fields))) && ($lE) && ($lE2))
			{
				$a = explode($data[$prefix.'name']); 
				$c = count($a);  
				if ($c>1)
				{
					$data[$prefix.'first_name'] = $a[0]; 
					$cz = $c - 1; 
					$data[$prefix.'last_name'] = $a[$cz]; 
					
				}
			}
			
			
			
			if (empty($data[$prefix.'name']))
			{
				/*
				if (!empty($res))
				$data[$prefix.'name'] = $res['address_type_name']; 
				else $data[$prefix.'name'] = ''; 
				*/
				$data[$prefix.'name'] = ''; 
				
				if (!empty($data[$prefix.'first_name']))
				$data[$prefix.'name'] .= $data[$prefix.'first_name'];
				if (!empty($data[$prefix.'last_name']))
				$data[$prefix.'name'] .= $data[$prefix.'last_name']; 
			}
			
			
			
			// end of logged in user
			
			
			
			OPCloader::setRegType(); 
			//NO_REGISTRATION, NORMAL_REGISTRATION, SILENT_REGISTRATION, OPTIONAL_REGISTRATION
			if (VM_REGISTRATION_TYPE == 'NO_REGISTRATION')
			$register = false; 
			else 
			if (VM_REGISTRATION_TYPE == 'NORMAL_REGISTRATION')
			$register = true; 
			else
			if (VM_REGISTRATION_TYPE == 'SILENT_REGISTRATION')
			$register = true; 
			else 
			if (VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION')
			{
				$register = JRequest::getVar('register_account', false); 
			}
			
			
			$mainframe = JFactory::getApplication();
			
			$msg = '';
			$userModel = OPCmini::getModel('user');
			

			
			if (($adminmode) && ($user->guest!=1 || $register)) {
				self::getVendorDataStored($data); 
			}
			// update address of already registered user
			/*
		if (!empty($doUpdate))
		{
		$this->userStoreAddress($userModel, $data); 
		return $this->returnTerminate(''); 
		}
		*/ 
			
		}
		
		
		$cart = VirtuemartCart::getCart(); 
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shoppergroups.php'); 
		OPCShopperGroups::setShopperGroupsController($cart); 
		
		if (method_exists($cart, 'prepareCartData'))
		{
			$stored_coupon = $cart->couponCode; 
			ob_start(); 
			$cart->prepareCartData(false); 
			$zz = ob_get_clean(); 
			$cart->couponCode = $stored_coupon; 
		}
		
		$this->prepareFields(); 
		$this->setCartAddress($cart); 
		$this->setExtAddress($cart, false);
		// k2 mod with recaptcha enabled
		$session = JFactory::getSession(); 
		$orig = $session->get('socialConnectData'); 
		$session->set('socialConnectData', true); 
		// end p1 k2 mod with recaptcha enabled
		
		
		
		/*
			$reg = JRequest::getVar('register_account'); 
			if (empty($reg)) $reg = false; 
			else $reg = true; 
			*/
		
		$reg = true; 
		
		if (!isset($data['address_type'])) $data['address_type'] = 'BT'; 
		
		if ($data['address_type'] == 'ST')
		{
			if (!isset($data['ship_to_info_id'])) $data['ship_to_info_id'] = 'new'; 
			// opc hack: 
			$data['sa'] = 'adresaina'; 
			
			$suid = JRequest::getVar('shipto_virtuemart_userinfo_id', JRequest::getVar('virtuemart_userinfo_id')); 
			if (empty($suid)) 
			$data['opc_st_changed_new'] = true; 
			JRequest::setVar('opc_st_changed_new', true); 
			JRequest::setVar('sa', 'adresaina'); 
		}
		
		$user_id = JFactory::getUser()->get('id'); 
		// disable duplict should be true for registraton: 
		$ret = $this->saveData($cart,$reg, true, $data); 
		
		
		if ((!empty($data['name'])) && (!empty($data['email']))) {
			$this->acySub($data['name'], $data['email'], $cart); 
		}
		
		if ((empty($user_id)) || (!empty($allow_sg_update)))
		$this->storeShopperGroup($data, true); 
		
		$userModel = OPCmini::getModel('user');	   
		if (method_exists($userModel, 'getCurrentUser'))
		{
			$user = $userModel->getCurrentUser();
			self::$shopper_groups = $user->shopper_groups; 
		}
		
		// k2 mod with recaptcha enabled
		if (empty($orig))
		$session->clear('socialConnectData'); 
		else
		$session->set('socialConnectData', $orig); 
		// end p2 k2 mod with recaptcha enabled

	  
		
		if (isset(self::$ok_arr))
		if (is_array(self::$ok_arr))
		{
			foreach (self::$ok_arr as $e)
			{
				
				
				if (empty($e)) 
				{
					$url = 'index.php?option=com_virtuemart&view=user&layout=edit'.$Itemid.$lang.'&error_redirect='.__LINE__.'&nosef=1'; 

					return $this->returnTerminate($msg, '', $url ); 			
				}
			}
			if (!empty($user_id_o)) $msgtype = '&msgtype=3'; 
			else $msgtype = '&msgtype=0'; 
			
			return $this->returnTerminate($msg, '', 'index.php?option=com_onepage&view=registration_complete&nosef=1'.$msgtype.$lang.$Itemid); 
			
			
			
		}
		
		$url = 'index.php?option=com_virtuemart&view=user&layout=edit'.$Itemid.$lang.'&error_redirect='.__LINE__.'&nosef=1'; 
		return $this->returnTerminate($msg, '', $url); 			
		
		
		
		
	}
	
	
	
	
	function cart()
	{
		$view = new VirtueMartViewCartopc(); 
		$view->display(); 
		
	}
	
	function updateAttributesVM2(&$cart)
	{
		jimport ('joomla.utilities.arrayhelper');
		$virtuemart_product_idArray = JRequest::getVar ('virtuemart_product_id', array()); //is sanitized then
		
		
		if(is_array($virtuemart_product_idArray)){
			JArrayHelper::toInteger ($virtuemart_product_idArray);
			$virtuemart_product_id = $virtuemart_product_idArray[0];
		} else {
			$virtuemart_product_id = $virtuemart_product_idArray;
		}

		$customPrices = array();
		$customVariants = JRequest::getVar ('customPrice', array()); //is sanitized then
		

		//MarkerVarMods
		foreach ($customVariants as $customVariant) {
			//foreach ($customVariant as $selected => $priceVariant) {
			//In this case it is NOT $selected => $variant, because we get it that way from the form
			foreach ($customVariant as $priceVariant => $selected) {
				//Important! sanitize array to int
				$selected = (int)$selected;
				$customPrices[$selected] = $priceVariant;
			}
		}

		$quantity = JRequest::getVar ('quantity',1); //is sanitized then
		
		if (is_array($quantity)) $quantity = reset($quantity); 
		$quantity = (int)$quantity; 
		
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
		$product_model = OPCmini::getModel ('product');

		//VmConfig::$echoDebug = TRUE;
		
		$qs = $quantity; 
		$product = $product_model->getProduct ($virtuemart_product_id, TRUE, TRUE, TRUE,$quantity);
		
		$quantity = $qs; 
		
		$prices = $product_model->getPrice ($product, $customPrices, $quantity);
		
		$priceFormated = array();
		if (!class_exists ('CurrencyDisplay')) {
			require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'currencydisplay.php');
		}
		$currency = CurrencyDisplay::getInstance ();
		foreach ($prices as $name => $product_price) {
			if (is_numeric($product_price))
			$prices[$name] = $product_price * $quantity; 

			if ($name != 'costPrice') {
				$priceFormated[$name] = $currency->createPriceDiv ($name, '', $prices, TRUE, true);
			}
		}
		
		$s = ''; 
		
		
		
		$prod_id = JRequest::getVar('cart_virtuemart_product_id', 0); 
		$cart->removeProductCart($prod_id); 
		$keys = array(); 
		if (!empty($cart->products))
		foreach ($cart->products as $key=>$val)
		{
			$keys[] = $key; 
		}
		$cart->add($virtuemart_product_idArray, $s); 
		/*
		$last = end($cart->products); 
		$new_key = key($cart->products); 
		*/
		$new_key = key( array_slice( $cart->products, -1, 1, TRUE ) );
		$new_quantity = $quantity; 
		
		if (in_array($new_key, $keys))
		{
			// a product merge happened here
			$new_quantity = $cart->products[$new_key]->quantity; 
		}
		
		
		
		$arr = array(); 
		include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
		if (empty($product_price_display)) $product_price_display = 'salesPrice'; 
		$test_product_price_display = array($product_price_display, 'salesPrice', 'basePrice', 'priceWithoutTax', 'basePriceWithTax', 'priceBeforeTax', 'costPrice'); 
		foreach ($test_product_price_display as $product_price_display)
		{
			
			$test = $currency->createPriceDiv($product_price_display,'', '10',false,false, 1);
			if (empty($test)) continue; 
			else 
			break;
		}
		//$product['product_price'] = $currentPrice[$product_price_display];
		$arr['price'] = $priceFormated[$product_price_display]; 
		$arr['new_key'] = $new_key; 
		$arr['new_quantity'] = $new_quantity; 
		return $arr; 
	}
	
	
	
	function updateAttributesVM3(&$cart)
	{
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		OPCmini::storeReqState(array('new_quantity', 'new_virtuemart_product_id', 'quantity', 'virtuemart_product_id', 'cart_virtuemart_product_id', 'customProductData')); 
		$virtuemart_product_id = JRequest::getVar ('virtuemart_product_id', 0); //is sanitized then
		$quantity = JRequest::getVar ('quantity',1); //is sanitized then
		if (is_array($quantity)) $quantity = reset($quantity); 
		$quantity = (int)$quantity; 
		if (is_array($virtuemart_product_id)) $virtuemart_product_id = reset($virtuemart_product_id); 
		$virtuemart_product_id = (int)$virtuemart_product_id; 
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
		$product_model = OPCmini::getModel ('product');
		$s = ''; 
		$prod_id = $cart_virtuemart_product_id  = JRequest::getVar('cart_virtuemart_product_id', 0);
		$q_a = array($cart_virtuemart_product_id=>$quantity); 
		$p_a = array($cart_virtuemart_product_id=>$virtuemart_product_id); 
		JRequest::setVar('quantity', $q_a); 
		JRequest::setVar('virtuemart_product_id', $p_a); 
		if (isset($cart->_productAdded))
		{
			$cart->_productAdded = true; 
		}
		$cart->add($p_a, $s); 
		$_POST['delete_'.$cart_virtuemart_product_id] = true; 
		unset($cart->cartProductsData[$cart_virtuemart_product_id]);
		ob_start(); 
		$stored_coupon = $cart->couponCode; 
		$cart->prepareCartData(); 
		$cart->couponCode = $stored_coupon; 
		$zz = ob_get_clean(); 
		unset($cart->products[$cart_virtuemart_product_id]); 
		$cart->setCartIntoSession(true);
		
		return OPCmini::loadReqState(array('new_quantity', 'new_virtuemart_product_id', 'quantity', 'virtuemart_product_id', 'cart_virtuemart_product_id', 'customProductData')); 
		
		
		
	}
	
	function updateattributes(&$cart)
	{
		/*
	@header('Content-Type: text/html; charset=utf-8');
	@header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	@header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	*/
		return $this->updateAttributesVM2($cart); 
		return $this->printJson($arr); 
		
		
	}
	function tracker()
	{

		include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'tracker'.DIRECTORY_SEPARATOR.'tracker.php'); 
		$mainframe = JFactory::getApplication();
		$mainframe->close(); 
	}
	
	function getEscaped(&$dbc, $string)
	{
		if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
		return $dbc->escape($string); 
		else return $dbc->escape($string);  
	}
	
	function getEmail($id=null) {
		return OPCLoggedShopper::getEmail($id); 
	}
	
	function getBTaddress($user_info_id)
	{
		if(!class_exists('VirtuemartModelUser')) require(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'user.php');
		$vmusero = new VirtueMartModelUser();
		$user = JFactory::getUser();
		$vmusero->setUserId($user->id);
		$vmuser = $user->getUser(); 
		$vmuser->userInfo[$user_info_id]; 
		
		
	}
	
	// klarna and other 3rd party compatibility edits
	function setExtAddress(&$cart, $ajax=false, $force_only_st=false)
	{
		
		$name = 'cpsolrates';
		$jsess = JFactory::getSession();
		$has_shipping_rate = $jsess->get($name, -1, 'vm');
		if ($has_shipping_rate!=-1)
		if(strlen($has_shipping_rate)){
			$jsess->clear($name, 'vm');
			$jsess->set('updateshipping', 1, 'vm');
		}
		
		
		$session = JFactory::getSession(); 
		
		// parkbeach fedex: 
		// this will reset address once per ajax 
		if (isset($_SESSION['cart']['fedex_ship_dest_zip']))
		{
			unset($_SESSION['cart']['fedex_ship_dest_zip']); 
			
		}
		
		
		$kIndex = 'klarna_';
		
		$klarna_method = JRequest::getVar('klarna_opc_method', ''); 
		if (!empty($klarna_method))
		JRequest::setVar('klarna_paymentmethod', $klarna_method); 

		$klarna['klarna_paymentmethod'] = JRequest::getVar ($kIndex . 'paymentmethod');
		
		if ($klarna['klarna_paymentmethod'] == 'klarna_invoice') {
			$klarna_option = 'invoice';
		} elseif ($klarna['klarna_paymentmethod'] == 'klarna_partPayment') {
			$klarna_option = 'part';
		} elseif ($klarna['klarna_paymentmethod'] == 'klarna_speccamp') {
			$klarna_option = 'spec';
		} else {
			return NULL;

		}
		$prefix=$klarna_option . '_' . $kIndex ;

		$x = 		JRequest::getVar('klarna_paymentPlan', ''); 
		$y = JRequest::getVar($prefix.'paymentPlan', $x);
		
		if (empty($y))
		JRequest::setVar($prefix.'paymentPlan', $x);
		
		$sn = JRequest::getVar($prefix.'socialNumber'); 
		
		
		
		
		if (empty($sn))
		{
			if (!isset($cart->BT['socialNumber']))
			{
				$sn = JRequest::getVar('socialNumber', ''); 
				if (!empty($sn))
				{
					JRequest::setVar($prefix.'socialNumber', $sn); 
					JRequest::setVar($prefix.'pnum', $sn); 
				}
			}
			else 
			{
				JRequest::setVar($prefix.'socialNumber', $cart->BT['socialNumber']); 
				JRequest::setVar($prefix.'pnum', $cart->BT['socialNumber']); 
			}
		}
		else
		{
			JRequest::setVar($prefix.'pnum', $sn);  
		}
		
		if (!isset($cart->BT['first_name']))
		JRequest::setVar($prefix.'firstName', JRequest::getVar('first_name', '')); 
		else
		JRequest::setVar($prefix.'firstName', $cart->BT['first_name']); 
		
		if (!isset($cart->BT['last_name']))
		JRequest::setVar($prefix.'lastName', JRequest::getVar('last_name', '')); 
		else
		JRequest::setVar($prefix.'lastName', $cart->BT['last_name']); 
		
		
		$bd = JRequest::getVar($prefix.'birth_day'); 
		
		if (empty($bd))
		{
			JRequest::setVar($prefix.'birth_day', JRequest::getVar('klarna_birth_day', '')); 
			JRequest::setVar($prefix.'birth_month', JRequest::getVar('klarna_birth_month', '')); 
			JRequest::setVar($prefix.'birth_year', JRequest::getVar('klarna_birth_year', '')); 
		}
		
		if (empty($cart->BT['fax'])) $cart->BT['fax'] = ''; 
		if (empty($cart->BT['phone_2'])) $cart->BT['phone_2'] = ''; 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
		$noshipto = OPCloader::getShiptoEnabled($cart); 
		
		if (empty($noshipto)) {
			if (empty($cart->ST['fax'])) $cart->BT['fax'] = ''; 
			if (empty($cart->ST['phone_2'])) $cart->BT['phone_2'] = ''; 
		}
		
		JRequest::setVar($prefix.'fax', JRequest::getVar('fax', '')); 
		JRequest::setVar($prefix.'phone_2', JRequest::getVar('phone_2', '')); 
		
		if (!isset($cart->BT['phone_1']))
		JRequest::setVar($prefix.'phone', JRequest::getVar('phone_1', '')); 
		else
		JRequest::setVar($prefix.'phone', $cart->BT['phone_1']); 
		//klarna_birth_day
		//klarna_birth_month
		//klarna_birth_year
		if (isset($cart->BT['birthday']))
		{
			$bday = $cart->BT['birthday'];
			$arr = explode('-', $bday); 
			if (count($arr)==3)
			{
				JRequest::setVar($prefix.'birth_day', $arr[2]); 
				JRequest::setVar($prefix.'birth_month', $arr[1]); 
				JRequest::setVar($prefix.'birth_year', $arr[0]); 
			}
		}

		if (!isset($cart->BT['address_1']))
		JRequest::setVar($prefix.'street', JRequest::getVar('address_1', '')); 
		else
		JRequest::setVar($prefix.'street', $cart->BT['address_1']); 
		
		if (!isset($cart->BT['address_2']))
		JRequest::setVar($prefix.'homenumber', JRequest::getVar('address_2', '')); 
		else
		JRequest::setVar($prefix.'homenumber', $cart->BT['address_2']); 
		
		//klarna_city
		if (!isset($cart->BT['city']))
		JRequest::setVar($prefix.'city', JRequest::getVar('city', '')); 
		else
		JRequest::setVar($prefix.'city', $cart->BT['city']); 
		
		if (!isset($cart->BT['zip']))
		JRequest::setVar($prefix.'zipcode', JRequest::getVar('zip', '')); 
		else
		JRequest::setVar($prefix.'zipcode', $cart->BT['zip']); 
		
		$country_id = JRequest::getVar('virtuemart_country_id', ''); 
		if (isset($cart->BT['virtuemart_country_id'])) $country_id = $cart->BT['virtuemart_country_id']; 
		$klarna_country = JRequest::getVar('klarna_country_2_code', null); 
		if (isset($klarna_country) && (empty($klarna_country)))
		if (is_numeric($country_id))
		{
			$q = 'select country_2_code from #__virtuemart_countries where virtuemart_country_id = '.$country_id.' limit 0,1';    
			$db=JFactory::getDBO(); 
			$db->setQuery($q); 
			$country_2_code = $db->loadResult(); 
			JRequest::setVar($prefix.'country_2_code', strtoupper($country_2_code)); 
		}
		
		$emailPost = JRequest::getVar('email', ''); 
		if (!empty($emailPost)) 
		{
			$cart->BT['email'] = $emailPost; 
			if (is_array($cart->ST))
			$cart->ST['email'] = $emailPost; 
		}
		
		if (!isset($cart->BT['email']))
		JRequest::setVar($prefix.'emailAddress', JRequest::getVar('email'));
		else
		JRequest::setVar($prefix.'emailAddress', $cart->BT['email']);
		$em = JRequest::getVar($prefix.'emailAddress', ''); 
		if (empty($em))
		{
			$em = $this->getEmail(); 
			JRequest::setVar($prefix.'emailAddress', $em); 
		}
		if (!empty($cart->BT['title']))
		{
			switch ($cart->BT['title']) {
			case OPCLang::_ ('COM_VIRTUEMART_SHOPPER_TITLE_MR'):
				JRequest::setVar('part_klarna_gender', 1); 
				JRequest::setVar('spec_klarna_gender', 1); 
				JRequest::setVar('invoice_klarna_gender', 1); 
				JRequest::setVar($prefix.'gender', 1); 
				break;
			case 'Mr':
				JRequest::setVar('part_klarna_gender', 1); 
				JRequest::setVar('spec_klarna_gender', 1); 
				JRequest::setVar('invoice_klarna_gender', 1); 
				JRequest::setVar($prefix.'gender', 1); 
				break;
			case 'Ms':
			case 'Mrs':
			case OPCLang::_ ('COM_VIRTUEMART_SHOPPER_TITLE_MISS'):
			case OPCLang::_ ('COM_VIRTUEMART_SHOPPER_TITLE_MRS'):
				JRequest::setVar('klarna_gender', 0);
				JRequest::setVar('spec_klarna_gender', 0); 
				JRequest::setVar('invoice_klarna_gender', 0); 
				JRequest::setVar($prefix.'gender', 0);
				break;
			default:
				JRequest::setVar('klarna_gender', NULL );
				JRequest::setVar($prefix.'gender', NULL);
				break;
			}
		}
		
		
		if (isset($cart->BT['house_no']))
		JRequest::setVar($prefix.'homenumber', $cart->BT['house_no']); 
		//if (isset($cart->BT['klarna_house_extension
		$company = JRequest::getVar('company', @$cart->BT['company']); 
		if (empty($company))
		JRequest::setVar('klarna_invoice_type', 'private'); 
		else 
		{
			JRequest::setVar('klarna_invoice_type', 'company'); 
			JRequest::setVar('klarna_company', $company); 
			JRequest::setVar('klarna_invoice_type', 'company'); 
			JRequest::setVar('klarna_company_name', $company); 
		}
		//JRequest::setVar('klarna_country_2_code', JRequest::getVar('virtuemart_country_id', '')); 
		
		//klarna_birth_day, klarna_birth_month, klarna_birth_year
		/*
	
	*/
		//if (empty($cart->BT['birthday'])) $cart->BT['birthday'] = ''; 

		
		
		OPCloader::prepareBT($cart);
		


	}
	
	private function checkSec(&$userinfo_id) {
		return OPCLoggedShopper::checkSec($userinfo_id); 
	}
	
	private function getRequires() {
		
		if (!class_exists('VmConfig')) {
		  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		  VmConfig::loadConfig(); 
		}
		
		if(!class_exists('VirtuemartModelUserfields')) require(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'userfields.php');
		if(!class_exists('VirtueMartModelUser')) require(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'user.php');
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'unloggedshopper.php'); 
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shoppergroups.php'); 
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'language.php'); 
		
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
		 
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vat.php'); 
		if (!defined('JPATH_OPC')) define('JPATH_OPC', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'); 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'ajaxhelper.php'); 
		 
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loggedshopper.php'); 


	}
	
	private function setJoomlaGlobals(&$cart, &$post) {
		// POST has priority over cart
		$emailPost = JRequest::getVar('email', ''); 
		if (!empty($emailPost)) 
		{
			$cart->BT['email'] = $emailPost; 
			if (is_array($cart->ST))
			$cart->ST['email'] = $emailPost; 
		}
		
		if (!empty($cart->BT['email']))
		$post['email'] = $post['shipto_email'] = $cart->BT['email']; 
		
		$userNamePost = JRequest::getVar('username', ''); 
		if (!empty($userNamePost))
		$cart->BT['username'] = $userNamePost; 
		
		if (is_array($cart->ST))
		$cart->ST['username'] = $userNamePost; 
		
		if (!empty($cart->BT['username']))
		$post['username'] = $post['shipto_username'] = $cart->BT['username']; 
	}
	
	private function backwardCompatiblity() {
		$state_test = JRequest::getVar('virtuemart_state_id', '');
		if ($state_test == 'none')
		{
			JRequest::setVar('virtuemart_state_id', ''); 
			$_POST['virtuemart_state_id'] = $_GET['virtuemart_state_id'] = ''; 
		}

		$state_test = JRequest::getVar('shipto_virtuemart_state_id', '');
		if ($state_test == 'none')
		{
			JRequest::setVar('shipto_virtuemart_state_id', ''); 
			$_POST['shipto_virtuemart_state_id'] = $_GET['shipto_virtuemart_state_id'] = ''; 
		}

		
	}
	
	function setAddress(&$cart, $ajax=false, $force_only_st=false, $no_post=false)
	{	
		$this->getRequires(); 
		//3rd party edits: 
		$this->setExtAddress($cart, $ajax, $force_only_st); 
		$this->backwardCompatiblity(); 
		$user = JFactory::getUser(); 
		$user_id = $user->id; 	   
		$post = JRequest::get('post'); 
		$this->setJoomlaGlobals($cart, $post); 
		$savedS = $cart->virtuemart_shipmentmethod_id;
		$savedP =  $cart->virtuemart_paymentmethod_id; 
		// this sets shipping method !!!!
		if (method_exists($cart, 'setPreferred'))
		$cart->setPreferred(false); 

		if (($no_post) && (!empty($user_id))) return; 
		OPCUserFields::populateCart($cart, 'BT', true);
		$cart->virtuemart_shipmentmethod_id = $savedS; 
		$cart->virtuemart_paymentmethod_id = $savedP; 
		$corefields = array( 'name','username', 'email', 'password', 'password2' , 'agreed','language', 'tos');

		$userFields = $cart->BTaddress;
		OPCUserFields::populateCart($cart, 'ST', true);
		$userFieldsst = $cart->STaddress;
		$db = JFactory::getDBO();
		
		// we will populate the data for logged in users
		
		
		
		if ((!empty($post['ship_to_info_id'])) || (!empty($post['ship_to_info_id_bt'])))
		{
			OPCLoggedShopper::setAddress($cart, $ajax, $force_only_st, $no_post, $post, $userFields, $userFieldsst);
			return; 
		}
		
		OPCUnloggedShopper::setAddress($cart, $ajax, $force_only_st, $no_post, $post, $userFields, $userFieldsst);

	}
	
	//returns true if ST is used, false if we are using BT
	public static function stOpen() {
		
		$stopen = JRequest::getVar('shiptoopen', 0); 
		
		if ($stopen === 'false') $stopen = 0; 
		
		if (empty($stopen)) 
		{
			$sa = JRequest::getVar('sa', ''); 
			if ($sa == 'adresaina') $stopen = 1; 
		}
		
		if ($stopen) return true; 
		
		return false; 
	}
	function setAddress2(&$cart)
	{
		$address = array(); 
		$address['virtuemart_country_id'] = JRequest::getInt('virtuemart_country_id', 0); 
		$address['zip'] = JRequest::getVar('zip', ''); 
		$address['virtuemart_state_id'] = JRequest::getInt('virtuemart_state_id', ''); 
		$address['address_1'] = JRequest::getVar('address_1', ''); 
		$address['address_2'] = JRequest::getVar('address_2', ''); 
		/*
		foreach ($address as $kA=>$vA ){
					if (!is_array($cart->ST)) $cart->ST = array();
					if (isset($address['shipto_'.$ka])) $vA = $address['shipto_'.$ka]; 
					$cart->ST[$kA] = $vA;
					}
		*/
		self::addressToSt($cart, $address); 
		// not used $ship_to_info_id = JRequest::getVar('ship_to_info_id'); 
	}	
	
	
	function checkOPCCaptcha($retUrl='')
	{
		// before we do anything, let's check captcha: 

		include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
		JPluginHelper::importPlugin('system');
		JPluginHelper::importPlugin('captcha');
		$dispatcher = JDispatcher::getInstance();
		$code = ''; 
		$returnValues = $dispatcher->trigger('onCheckAnswer', array($code));  
		foreach ($returnValues as $val)
		{
			if ($val === false) 
			{
				
				$mainframe = JFactory::getApplication();
				if (empty($retUrl))
				{
					$msg = 'Captcha: '.OPCLang::_('COM_VIRTUEMART_CART_CHECKOUT_DATA_NOT_VALID'); 
					return $this->returnTerminate($msg); 
				}
				else
				{
					$msg = 'Captcha: '.OPCLang::_('COM_VIRTUEMART_CART_CHECKOUT_DATA_NOT_VALID'); 
					return $this->returnTerminate($msg, '', $retUrl.'&captcha=1'); 
					
				}
			
			}
		}
		


	}
	
	private function prepareFields()
	{
		include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
		$guest_email = JRequest::getVar('guest_email', ''); 
		$emailn = JRequest::getVar('email', ''); 
		$rg = JRequest::getVar('register_account', 0);
		
		if ((!empty($guest_email)) && (empty($rg)))
		{
			JRequest::setVar('email', $guest_email); 
			JRequest::setVar('register_account', JRequest::getVar('register_account', 0)); 
		}
		
		if (!empty($guest_email) && (empty($emailn)))
		{
			JRequest::setVar('email', $guest_email); 
		}
		
		// security: 
			$admin = false; 
	   		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	        if ((OPCmini::isSuperVendor()) || ((JFactory::getUser()->authorise('core.admin', 'com_virtuemart') || JFactory::getUser()->authorise('core.admin', 'com_virtuemart')))) { 
			  $admin = true; 
			}
			$my = JFactory::getUser();
			$iAmSuperAdmin = $my->authorise('core.admin');
			if (empty($iAmSuperAdmin)) { $admin = false; }
		
		if (empty($admin))
		JRequest::setVar('virtuemart_shoppergroup_id', null, 'post');
		
		$email = JRequest::getVar('email', ''); 
		$stemail = JRequest::getVar('shipto_email', ''); 
		if (empty($stemail)) JRequest::setVar('shipto_email', $email); 
		
		// password modification in OPC
		$pwd = JRequest::getVar('opc_password', '', 'post', 'string', JREQUEST_ALLOWRAW); 
		//if (!empty($pwd)) 
		// stAn 2.0.140: we always set password to opc_password, so it doesn't mix with the login password
		{
			// raw
			$_POST['password'] = $pwd; 
			JRequest::setVar('password', $pwd); 
			
			
		}
		if (in_array('password2', $custom_rendering_fields))
		{
			
			$p1 = JRequest::getVar('password'); 
			if (!empty($p1))
			{
				$_POST['opc_password2'] = $p1; 
				JRequest::setVar('opc_password2', $p1); 
				JRequest::setVar('password2', $p1); 
				
			}
			
		}
		
		// key captcha support: 
		$first_name = JRequest::getVar('opc_first_name');
		if (!empty($first_name)) 
		{
			$_POST['first_name'] = $first_name; 
			$_GET['first_name'] = $first_name; 
			$_REQUEST['first_name'] = $first_name; 
			JRequest::setVar('first_name', $first_name); 
			$app = JFactory::getApplication(); 
			if (isset($app->input)) {
				$app->input->set('first_name', $first_name); 
			}
		}
		
		$opc_password2 = JRequest::getVar('opc_password2', ''); 
		$password2 = JRequest::getVar('password2', ''); 
		if (empty($password2) && (!empty($opc_password2)))
		{
			$_POST['password2'] = $opc_password2; 
			$_GET['password2'] = $opc_password2; 
			$_REQUEST['password2'] = $opc_password2; 
			JRequest::setVar('password2', $opc_password2); 
			
		}
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php');  
		// if we used just first name then create a full name by string separation: 
		$fname = JRequest::getVar('first_name', ''); 
		$lname = JRequest::getVar('last_name', null); 
		$lE = OPCUserFields::fieldExistsPublished('last_name', 'BT'); 
		
		if ($lE) 
		if (!empty($fname) && (is_null($lname)))
		{
			$a = explode(' ', $fname); 
			if (count($a)>1)
			{
				if ($lE) {
					JRequest::setVar('first_name', $a[0]);
				}
				unset($a[0]); 
				$lname = implode(' ', $a); 
				JRequest::setVar('last_name', $lname); 
			}
			else 
			{
				// no last name
				if (!isset($_POST['last_name']))
				JRequest::setVar('last_name', '   '); 
			}
		}
		
		$lE = OPCUserFields::fieldExistsPublished('last_name', 'ST'); 
		
		$fname = JRequest::getVar('shipto_first_name', ''); 
		$lname = JRequest::getVar('shipto_last_name', ''); 
		if (!empty($fname) && (empty($lname)))
		{
			$a = explode(' ', $fname); 
			if (count($a)>1)
			{
				if ($lE) {
					JRequest::setVar('shipto_first_name', $a[0]);
				}
				unset($a[0]); 
				$lname = implode(' ', $a); 
				JRequest::setVar('shipto_last_name', $lname); 
			}
			else 
			{
				// no last name
				JRequest::setVar('shipto_last_name', '   '); 
			}
		}
		
		// we need to find in what type we are
		$ship_to_id = JRequest::getVar('shipto_logged', false); 
		$bt_id = JRequest::getVar('ship_to_info_id_bt', false); 
		
		$test = JRequest::getVar('ship_to_info_id', false); 
		if (empty($test))
		if (!empty($bt_id)) 
		{
			$_POST['ship_to_info_id'] = $bt_id; 
			JRequest::setVar('ship_to_info_id', $bt_id); 
		}
		
		
	}
	
	
	private function setCartAddress(&$cart)
	{
		
		
		include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'cart_override.php'); 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
		
		$OPCcheckout = new OPCcheckout($cart); 
		$loader = new OPCloader(); 
		
		$obj = new stdClass; 
		$obj->cart = $cart; 
		if (empty($cart->BT)) return; 
		$tos_required = $loader->getTosRequired($obj); 
		
		if (!empty($op_no_display_name))
		{
			$name = ''; 
			
			$first_name = JRequest::getVar('first_name', ''); 
			$last_name = JRequest::getVar('last_name', ''); 
			
			if (!empty($first_name)) $nameX = $first_name; 
			if (!empty($last_name)) { 
				if (!empty($nameX)) $nameX .= ' '; 
				$nameX .= $last_name; 
			}
			if (empty($nameX)) {
				if (!empty($cart->BT['first_name'])) $nameX = $cart->BT['first_name'];
				if (!empty($cart->BT['last_name'])) {
					if (!empty($nameX)) $nameX .= ' '; 
					$nameX = $cart->BT['first_name'];
				}
			}
			if (empty($cart->BT['name'])) $cart->BT['name'] = $nameX; 
			JRequest::setVar('name', $nameX);
			
			$first_name = JRequest::getVar('shipto_first_name', ''); 
			$last_name = JRequest::getVar('shipto_last_name', ''); 
			
			if (!empty($first_name)) $nameX = $first_name; 
			if (!empty($last_name)) { 
				if (!empty($nameX)) $nameX .= ' '; 
				$nameX .= $last_name; 
			}
			if (empty($nameX)) {
				if (!empty($cart->ST)) {
					if (!empty($cart->ST['first_name'])) $nameX = $cart->ST['first_name'];
					if (!empty($cart->ST['last_name'])) {
						if (!empty($nameX)) $nameX .= ' '; 
						$nameX = $cart->ST['first_name'];
					}
					
				
				if (empty($cart->ST['name']))
				$cart->ST['name'] = $nameX; 
			
			    }
			}
			JRequest::setVar('shipto_name', $nameX);
		}
		
		if ($tos_required)
		{
			if (!empty($post['tosAccepted']))
			{
				$cart->tosAccepted = 1; 
				$cart->BT['agreed'] = 1; 
				$cart->BT['tos'] = 1; 
				if (!empty($cart->ST)) 
				{
					$cart->ST['agreed'] = 1; 
					$cart->ST['tos'] = 1; 
				}
				JRequest::setVar('agreed', 1); 
				JRequest::setVar('shipto_agreed', 1); 
				JRequest::setVar('shipto_tos', 1); 
				
				JRequest::setVar('tos', 1); 
				
			}
			else
			{
				
			}
		}
		else
		{
			JRequest::setVar('tos', 1); 
			JRequest::setVar('agreed', 1); 
			JRequest::setVar('shipto_agreed', 1); 
			JRequest::setVar('shipto_tos', 1); 
			JRequest::setVar('tosAccepted', 1); 
		}
		
		// we need to find in what type we are
		$ship_to_id = JRequest::getVar('shipto_logged', false); 
		$bt_id = JRequest::getVar('ship_to_info_id_bt', false); 
		
		$test = JRequest::getVar('ship_to_info_id', false); 
		
		
		
		if (!empty($ship_to_id) && (!empty($bt_id)))
		{
			// let's set BT id as the BT address
			
			$sa = JRequest::getVar('sa', false); 
			
			if ($sa == 'adresaina')
			{
				$stopen = true; 
				JRequest::setVar('shiptoopen', true); 
			}
			else 
			{
				$stopen = false; 
				JRequest::setVar('shiptoopen', false); 
			}
			
			//$this->setAddress($cart, false, true);
			$this->setAddress($cart, false);
			
			if ($stopen)
			{
				$cart->selected_shipto = (int)$ship_to_id; 
				$cart->STsameAsBT = 0; 	
				JRequest::setVar('shipto', $ship_to_id); 
			}
			else 
			{
				$cart->ST = 0; 
				
				
				
				$cart->selected_shipto = $bt_id; 
				$cart->STsameAsBT = 1; 
			}
		}
		else
		{
			
			
			$this->setAddress($cart, false); 
		}
		
		
	}
	// note, that the full function for this is included in loader.php
	// this is used purely to load the last order_id !
	private function loadStoredOrderid(&$cart)
	{
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		$opc_conference_mode = OPCconfig::get('opc_conference_mode', false); 
		if (!empty($opc_conference_mode)) {
		   $cart->customer_number = null; 
		   $cart->virtuemart_order_id = null; 
		   return; 
		}
		
		$session = JFactory::getSession(); 
		$data = $session->get('opc_fields', '', 'opc'); 
		if (empty($data)) return array(); 
		$fields = @json_decode($data, true); 
		
		$reuse_order_statuses = OPCconfig::get('reuse_order_statuses', array('P')); 
		
		if (isset($fields['virtuemart_order_id'])) {
			$oi1 = (int)$fields['virtuemart_order_id']; 
		}
		else 
		{
			$oi1 = null; 
		}
		if (isset($cart->virtuemart_order_id)) {
			$oi2 = $cart->virtuemart_order_id; 
		}
		else
		{
			$oi2 = null; 
		}
		
		
		
		$db = JFactory::getDBO();  
		$txtA = array(); 
		foreach ($reuse_order_statuses as $txt)
		{
			$txtA[] = "'".$db->escape($txt)."'"; 
		}
		
		if (empty($txtA)) return; 
		
		if (!empty($reuse_order_statuses))  {
			if ((!empty($oi1)) || (!empty($oi2))) {
				
				
				if (!empty($oi1)) {
					
					
					$q = 'select `order_status`, `order_number` from #__virtuemart_orders where `virtuemart_order_id` = '.(int)$oi1.' and `order_status` IN ('.implode(',', $txtA).') limit 1'; 
					$db->setQuery($q); 
					$r = $db->loadAssoc(); 
					if (!empty($r)) {
						
						$order_status = $r['order_status']; 
						$order_number = $r['order_number']; 
						
						if ($order_status !== 'P') {
							// we need to set the order status to pending so VM can reuse it...
							$this->updateOrderStauts($oi1, 'P'); 
						}
						$cart->BT['order_number'] = $order_number; 
						$cart->virtuemart_order_id = $oi1; 
						$cart->reuseOrder = true; 
						return; 
					}
				}
				else
				{
					if (!empty($oi2)) {
						$db = JFactory::getDBO();  
						$q = 'select `order_status`, `order_number` from #__virtuemart_orders where `virtuemart_order_id` = '.(int)$oi1.' and `order_status` IN ('.implode(',', $txtA).') limit 1'; 
						$db->setQuery($q); 
						$r = $db->loadAssoc(); 
						if (!empty($r)) {
							
							$order_status = $r['order_status']; 
							$order_number = $r['order_number']; 
							
							
							if ($r !== 'P') {
								// we need to set the order status to pending so VM can reuse it...
								$this->updateOrderStauts($oi2, 'P'); 
								
							}
							$cart->BT['order_number'] = $order_number; 
							$cart->virtuemart_order_id = $oi2; 
							$cart->reuseOrder = true; 
							return; 
						}
					}
				}
				
			}
			
			if (isset($fields['customer_number'])) 
			{ 
				$oi1 = (int)$fields['customer_number']; 
			}
			else {
				$oi1 = null; 
			}
			
			if (isset($cart->customer_number)) {
				$oi2 = $cart->customer_number; 
			}
			else {
				$oi2 = null; 
			}
			
			if ((!empty($oi1)) || (!empty($oi2))) {
				$db = JFactory::getDBO();  
				$txtA = array(); 
				foreach ($reuse_order_statuses as $txt)
				{
					$txtA[] = "'".$db->escape($txt)."'"; 
				}
				
				if (!empty($oi1)) {
					
					
					$q = 'select `order_status`, `order_number`, `virtuemart_order_id` from #__virtuemart_orders where `customer_number` = '.(int)$oi1.' and `order_status` IN ('.implode(',', $txtA).') '; 
					
					//VM code: 
					$jnow = JFactory::getDate();
					$jnow->sub(new DateInterval('PT1H'));
					$minushour = $jnow->toSQL();
					$q .= ' AND `created_on` > "'.$minushour.'" order by `created_on` desc limit 1';
					//VM code end */
					
					$db->setQuery($q); 
					$rR = $db->loadAssoc(); 
					if (!empty($rR)) {
						$orderId = $rR['virtuemart_order_id']; 
						$order_status = $rR['order_status']; 
						$order_number = $rR['order_number']; 
						if (!empty($order_status)) {
							if ($order_status !== 'P') {
								// we need to set the order status to pending so VM can reuse it...
								$this->updateOrderStauts($oi1, 'P'); 
								
							}
							$cart->virtuemart_order_id = $orderId; 
							$cart->reuseOrder = true; 
							$cart->BT['order_number'] = $order_number; 
							return; 
							
						}
					}
				}
				
				
				if (!empty($oi2)) {
					$q = 'select `order_status`, `order_number`, `virtuemart_order_id` from #__virtuemart_orders where `customer_number` = '.(int)$oi1.' and `order_status` IN ('.implode(',', $txtA).') '; 
					
					//VM code: 
					$jnow = JFactory::getDate();
					$jnow->sub(new DateInterval('PT1H'));
					$minushour = $jnow->toSQL();
					$q .= ' AND `created_on` > "'.$minushour.'" order by `created_on` desc limit 1';
					//VM code end */
					
					$db->setQuery($q); 
					$rR = $db->loadAssoc(); 
					if (!empty($rR)) {
						$orderId = $rR['virtuemart_order_id']; 
						$order_status = $rR['order_status']; 
						$order_number = $rR['order_number']; 
						if (!empty($order_status)) {
							if ($order_status !== 'P') {
								// we need to set the order status to pending so VM can reuse it...
								$this->updateOrderStauts($oi2, 'P'); 
								
							}
							$cart->virtuemart_order_id = $oi2; 
							$cart->reuseOrder = true; 
							$cart->BT['order_number'] = $order_number; 
							return; 
						}
						
					}
					
					
					
				}
				
				
			}
			
			
		}
		$cart->reuseOrder = false; 
	}
	
	
	
	private function updateOrderStauts($order_id, $order_status, $notified=0) {
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
		$modelOrder = OPCmini::getModel('orders');
		//$order_id = $order['details']['BT']->virtuemart_order_id; 
		VirtueMartControllerOpc::emptyCache(); 
		$lastOrder = $modelOrder->getOrder($order_id); 
		$lastOrder['customer_notified'] = $notified;
		if (!isset($lastOrder['comments'])) $lastOrder['comments'] = ''; 
		if (isset($lastOrder['details']['BT']))
		{
			//if (!isset($lastOrder['order_status'])) 
			$lastOrder['order_status'] = $order_status;
			$order_id = $lastOrder['details']['BT']->virtuemart_order_id; 
		}
		if (!empty($order_id))
		$modelOrder->updateStatusForOneOrder($order_id, $lastOrder, false);
	}
	
	public static function emptyCache() {
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
		$orderModel = OPCmini::getModel('Orders'); 
		if (method_exists($orderModel, 'emptyCache')) {
			$orderModel->emptyCache(); 
		}
		$order_tables = array('orders', 'userinfos', 'order_items', 'order_userinfos', 'order_calc_rules', 'order_histories', 'order_item_histories', 'invoices', 'order_items'); 
		
		foreach ($order_tables as $tb) {
			$tb = $orderModel->getTable($tb);
			if (method_exists($tb, 'emptyCache')) {
				$tb->emptyCache(); 
			}
		}

		
	}
	
	
	private function storePost($virtuemart_order_id=0, $customer_number='')
	{
		$this->prepareFields(); 
		
		$session = JFactory::getSession(); 
		$data = $session->get('opc_fields', '', 'opc'); 
		if (!empty($data)) {
			$fields = @json_decode($data, true); 
		}
		else
		{
			$fields = array(); 
		}
		
		$db = JFactory::getDBO(); 
		$q = 'select `name` from #__virtuemart_userfields where `published` = 1'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		
		$saved_fields = array(); 
		$saved_fields['ST'] = array(); 
		$saved_fields['BT'] = array(); 
		
		$user_id = JFactory::getUser()->get('id'); 
		$saved_fields['user_id'] = $user_id; 
		
		$post = JRequest::get('post'); 
		
		foreach ($res as $k=>$row)
		{
			$name = $row['name']; 
			
			if ($name === 'password') continue; 
			if ($name === 'password2') continue; 
			if ($name === 'ssn') continue; 
			
			if (isset($post[$name])) $saved_fields['BT'][$name] = $post[$name]; 
			if (isset($post['shipto_'.$name])) 
			$saved_fields['ST'][$name] = $post['shipto_'.$name]; 
			
		}
		
		if (!empty($post['register_account'])) 
		$saved_fields['register_account'] = true; 
		else
		$saved_fields['register_account'] = false; 
		
		$stopen = JRequest::getVar('shiptoopen', 0); 
		
		if ($stopen === 'false') $stopen = 0; 
		
		if (empty($stopen)) 
		{
			$sa = JRequest::getVar('sa', ''); 
			if ($sa == 'adresaina') $stopen = 1; 
		}
		
		if ($stopen) 
		$saved_fields['stopen'] = true; 
		else
		$saved_fields['stopen'] = false; 
		
		$date_picker_text = JRequest::getVar('opc_date_picker', ''); 
		$date_picker_store = JRequest::getVar('opc_date_picker_store', ''); 
		if (!empty($date_picker_store))
		{
			$saved_fields['date_picker_store'] = $date_picker_store; 
			
			$saved_fields['date_picker_text'] = $date_picker_text; 
		}
		
		$agreed = JRequest::getVar('tosAccepted', JRequest::getVar('agreed', false)); 
		if (!empty($agreed))
		{
			$saved_fields['agreed'] = true; 
		}
		//italianagreed
		$italianagreed = JRequest::getVar('italianagreed', false); 
		if (!empty($italianagreed))
		{
			$saved_fields['italianagreed'] = true; 
		}
		else
		{
			$saved_fields['italianagreed'] = false; 
		}
		
		
		$opc_is_business = JRequest::getVar('opc_is_business', false); 
		
		if (!empty($opc_is_business))
		{
			$saved_fields['opc_is_business'] = true; 
		}
		else
		{
			$saved_fields['opc_is_business'] = false; 
		}
		
		
		$acysub = JRequest::getVar('acysub', false); 
		if (!empty($acysub))
		{
			$saved_fields['acysub'] = true; 
		}
		
		
		$p_id = JRequest::getVar('virtuemart_paymentmethod_id', 0); 
		if (!empty($p_id))
		{
			$saved_fields['p_id'] = $p_id; 
		}
		
		
		$s_id = JRequest::getVar('virtuemart_shipmentmethod_id', 0); 
		if (!empty($s_id))
		{
			$saved_fields['s_id'] = $s_id; 
		}

		$saved_shipping_id = JRequest::getVar('saved_shipping_id', ''); 
		if (!empty($saved_shipping_id))
		{
			$saved_fields['saved_shipping_id'] = $saved_shipping_id; 
		}
		
		$third_address_opened = JRequest::getVar('third_address_opened', 0); 
		$saved_fields['third_address_opened'] = $third_address_opened; 
		
		$render_in_third_address = OPCconfig::get('render_in_third_address', array()); 
		$arr = array(); 
		foreach ($render_in_third_address as $k=>$v)
		{
			$val = JRequest::getVar('third_'.$v, ''); 
			$arr[$v] = $val; 
			
		}
		$saved_fields['RD'] = $arr; 
		
		if (!empty($virtuemart_order_id)) {
			$saved_fields['virtuemart_order_id'] = (int)$virtuemart_order_id; 
		}
		else
		{
			if (!empty($fields['virtuemart_order_id'])) {
				$saved_fields['virtuemart_order_id'] = (int)$fields['virtuemart_order_id']; 
			}
		}
		if (!empty($customer_number)) {
			$saved_fields['customer_number'] = (int)$customer_number; 
		}
		else
		{
			if (!empty($fields['customer_number'])) {
				$saved_fields['customer_number'] = (int)$fields['customer_number']; 
			}
		}
		
		if (!empty($post['customer_note'])) {
			$saved_fields['ST']['customer_note'] = $saved_fields['customer_note'] = $saved_fields['BT']['customer_note'] = $post['customer_note']; 
		}
		else
		if (!empty($post['customer_comment'])) {
			$saved_fields['ST']['customer_note'] = $saved_fields['customer_note'] = $saved_fields['BT']['customer_note'] = $post['customer_comment']; 
		}
		
		$txt = json_encode($saved_fields); 
		$session = JFactory::getSession(); 
		$session->set('opc_fields', $txt, 'opc'); 
		
		
		
	}
	
	function setHeaders($isjson=false) {
		if (empty($isjson)) {
			@header('Content-Type: text/html; charset=utf-8');
			@header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			@header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		}
		else {
			
		}
		
	}
	
	function checkout()
	{
		
		
		JFactory::getApplication()->set('is_rupostel_opc', true);
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		$isjson = false; 
		VirtueMartControllerOpc::$isjson = false; 
		$default = array(); 
		$opc_payment_isunder = OPCConfig::getValue('opc_config', 'opc_payment_isunder', 0, $default, false, false);
		$pid = JRequest::getInt('virtuemart_paymentmethod_id', 0); 
		$format = JRequest::getVar('format', 'html'); 
		if ($format === 'opchtml')
		if (!empty($opc_payment_isunder))
		if (in_array($pid, $opc_payment_isunder)) {
			
			header('Access-Control-Allow-Origin: *');
		    header('Access-Control-Allow-Methods: GET, POST');
			
			$isjson = true; 
			VirtueMartControllerOpc::$isjson = true; 
		}
		//if we are in json, we need to controll the output:
		
		if (!defined('K_TCPDF_THROW_EXCEPTION_ERROR'))
		define('K_TCPDF_THROW_EXCEPTION_ERROR', true); 
		
		// some 3rd party opc themes to not implement sa checkbox: 
		$ship_to_id = JRequest::getVar('ship_to_info_id', ''); 
		$bt_id = JRequest::getVar('bt_virtuemart_userinfo_id', ''); 
		if ((!empty($ship_to_id)) && ($bt_id != $ship_to_id))
		{
			JRequest::setVar('sa', 'adresaina'); 	
		}
		
		$this->getRequires(); 
		$this->storePost(); 
		
		 
		
		/*
	$savedGET = $_GET; 
	$savedPOST = $_POST; 
	$savedREQUEST = $_REQUEST; 
	*/
		// before we do anything, let's check captcha: 

		
		
		$selected_template = OPCrenderer::getSelectedTemplate(); 
		
		include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 

		
		OPCLang::loadLang(); 
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		$cart = OPCmini::getCart(); 

		
		$opc_disable_customer_email = OPCconfig::get('opc_disable_customer_email', false); 
		 if (!empty($opc_disable_customer_email)) {
			 OPCUserFields::handleDisabledEmail($cart); 
		 }
		
		
		// this code is to make sure that the gift products are updated if they are too close to the submit button 
		$checkbox_products = OPCconfig::get('checkbox_products', array()); 
		if (!empty($checkbox_products))
		{
			$selected = JRequest::getVar('checkbox_products', array()); 
			if (!empty($selected)) {
			require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'addtocartaslink.php'); 
			
			
			$q_o = array(); 
			$new_id = array(); 
			if (is_array($selected)) {
			  foreach ($selected as $pid) {
				  if (in_array($pid, $checkbox_products)) {
					  $new_id[$pid] = $pid; 
					  $q_o[$pid] = 1; 
				  }
				  else {
					  $new_id[$pid] = $pid; 
					  $q_o[$pid] = 0; 
				  }
			  }
			}
			else {
				if (is_numeric($selected)) {
				$selected = (int)$selected; 
				if (!empty($selected)) {
				       $new_id[$selected] = $selected; 
					  $q_o[$selected] = 1; 
				}
				}
			}
			//remove any unselected checkbox products: 
			foreach ($checkbox_products as $pid) {
				if (!isset($new_id[$pid])) {
					$new_id[$pid] = $pid; 
					$q_o[$pid] = 0; 
				}
			}
			
			OPCAddToCartAsLink::checkCheckboxProducts($cart, $q_o, $new_id); 
			
		}
		}
						
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		$cart = OPCmini::getCart(); 
		
		
		//mobile handling: 
		if (!defined('OPC_DETECTED_DEVICE'))
		{
			if (class_exists('OPCplugin'))
			{
				OPCplugin::detectMobile(); 
			}
		}


		if (!isset($opc_memory)) $opc_memory = '128M';
		if (!empty($opc_memory)) 
		{
			ini_set('memory_limit',$opc_memory);
		}

		ini_set('error_reporting', 0);
		// disable error reporting for ajax:
		error_reporting(0); 
		
		$logged = false; 
		if(JFactory::getUser()->guest) {
			
		}
		else { 
			$logged = true; 
			
		}  

		include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
		if (((!empty($enable_captcha_logged)) && ($logged)) || ((!empty($enable_captcha_unlogged)) && (!$logged)))
		{
			$this->checkOPCCaptcha(); 
			

		}
		
		
		
		
		
		
		if (!class_exists('VmConfig'))	  
		require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		VmConfig::loadConfig(); 

		// since vm2.0.21a we need to load the language files here
		
		OPCLang::loadLang(); 



		
		$this->prepareFields(); 
		


		
		// fedex multibox fix: 
		
		
		
		// register user first: 
		$reg = JRequest::getVar('register_account'); 
		if (empty($reg)) $reg = false; 
		else $reg = true; 
		
		
		
		// ENABLE ONLY BUSINESS REGISTRATION WHEN REGISTER_ACCOUNT IS SET AS BUSINESS FIELD
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
		
		$selected_theme = OPCmini::getSelectedTemplate(); 
		$is_custom_validated = true; 
		if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$selected_theme.DIRECTORY_SEPARATOR.'after_checkout.php')) {
			include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$selected_theme.DIRECTORY_SEPARATOR.'after_checkout.php'); 
		}
		
		
		$is_business = JRequest::getVar('opc_is_business', 0); 
		
		
		// we need to alter shopper group for business when set to: 
		$is_business = JRequest::getVar('opc_is_business', 0); 
		if (!empty($business_fields))
		if (!$is_business)
		{
			// manage required business fields when not business selected: 
			foreach ($business_fields as $fn)
			{
				/*
				$x1 = JRequest::getVar($fn); 
				if (empty($x1))
				JRequest::setVar($fn, '_'); 

				$x1 = JRequest::getVar('shipto_'.$fn); 
				if (empty($x1))
				JRequest::setVar('shipto_'.$fn, '_'); 
			*/
				
			}
		}
		
		if ($reg)
		if (!empty($business_fields))
		if (in_array('register_account', $business_fields))
		{
			
			if (empty($is_business))
			{
				$reg = false;
			}
		}
		
		
		
		
		
		
		
		//if (!class_exists('VirtueMartControllerUser'))
		//require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'user.php'); 
		//$userC = new VirtueMartControllerUser(); 

		
		

		

		
		
		
		$mainframe = Jfactory::getApplication();
		$virtuemart_currency_id = $mainframe->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',JRequest::getInt('virtuemart_currency_id') );
		$cart->paymentCurrency = $virtuemart_currency_id; 
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shoppergroups.php'); 
		OPCShopperGroups::setShopperGroupsController($cart); 
		
		if (method_exists($cart, 'prepareCartData'))
		{
			ob_start(); 
			$stored_coupon = $cart->couponCode; 
			$cart->prepareCartData(false); 
			$cart->couponCode = $stored_coupon; 
			$zz = ob_get_clean(); 
		}
		
		
		if (!isset($cart->vendorId))
		{
			$cart->vendorId = 1; 
		}
		
		$payment_id = $cart->virtuemart_paymentmethod_id = JRequest::getInt('virtuemart_paymentmethod_id', ''); 
		

		
		$cart->virtuemart_shipmentmethod_id = JRequest::getInt('virtuemart_shipmentmethod_id', ''); 
		$coupon = JRequest::getVar('opc_coupon_code_returned', ''); 
		
		if (!empty($coupon) && (empty($cart->couponCode))) $cart->couponCode = $coupon; 
		
		$this->runExt($cart); 
		
		
		
		
		if (method_exists($cart, 'prepareCartProducts')) {
			ob_start(); 
			$cart->prepareCartProducts(); 
			ob_get_clean(); 
		}
		
		
		
		$this->setCartAddress($cart); 
		
		
		
		
		$this->setExtAddress($cart, false);
		


		
		$order_language = JFactory::getLanguage()->getTag(); 
		JRequest::setVar('order_language', $order_language); 
		$cart->order_language = $order_language; 
		

		
		
		// k2 mod with recaptcha enabled
		$session = JFactory::getSession(); 
		$orig = $session->get('socialConnectData'); 
		$session->set('socialConnectData', true); 
		// end p1 k2 mod with recaptcha enabled
		
		
		
		
		
		//$this->setShopperGroups($cart); 	  
		
		$data = JRequest::get('post');
		
		$data['quite'] = true; 
		
		
		if (method_exists($cart, 'saveCartFieldsInCart'))
		$cart->saveCartFieldsInCart(); 
		


		
		//suppress messages from thsi function 
		$this->saveData($cart,$reg, false, $data); 
		$opc_copy_bt_st = OPCconfig::get('opc_copy_bt_st', false); 
		if ($opc_copy_bt_st) {
			require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loggedshopper.php'); 
			OPCLoggedShopper::copyBTintoSTUserInfos($cart); 
		}

		
		if ((!empty($data['name'])) && (!empty($data['email']))) {
			$this->acySub($data['name'], $data['email'], $cart); 
		}
		

		
		if (!empty($allow_sg_update))
		$this->storeShopperGroup($data, true); 
		

		
		$userModel = OPCmini::getModel('user');	   
		if (method_exists($userModel, 'getCurrentUser'))
		{
			$user = $userModel->getCurrentUser();
			self::$shopper_groups = $user->shopper_groups; 
		}

		// k2 mod with recaptcha enabled
		if (empty($orig))
		$session->clear('socialConnectData'); 
		else
		$session->set('socialConnectData', $orig); 
		// end p2 k2 mod with recaptcha enabled
		
		
		
		/*
	if (!class_exists('VirtueMartControllerCartOpc'))
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'cartcontroller.php'); 
	$cartcontroller = new VirtueMartControllerCartOpc(); 
	*/
		
		$OPCcheckout = new OPCcheckout($cart); 
		
		$po = JRequest::getVar('socialNumber', ''); 
		
		$OPCcheckout->setshipment($cart); 
		// fix fedex multibox
		$saved_bt = $cart->BT; 
		

		
		if (isset($_SESSION['load_fedex_prices_from_session']))
		unset($_SESSION['load_fedex_prices_from_session']); 
		
		
		
		
		$OPCcheckout->setpayment($cart); 


		
		if ($cart->BT != $saved_bt)
		{
			if (empty($cart->ST) || ($cart->STsameAsBT))
			{
				$cart->STsameAsBT = 0; 
				$cart->ST = $saved_bt; 
				$text = JText::_('COM_ONEPAGE_USER_ENTERED_ADDRESS'); ; 
				if ($text == 'COM_ONEPAGE_USER_ENTERED_ADDRESS')
				$text = 'User Entered Address (Ship To)'; 
				$cart->ST['address_type_name'] = $text; 
				
				$cart->BT['address_type_name'] = JText::_('COM_ONEPAGE_ADDRESS_HAD_CHANGED_NAME'); 
				//$cart->BT = $saved_bt; 
			}
			else
			if ($cart->ST != $saved_bt)
			{
				$address_changed = JText::_('COM_ONEPAGE_ADDRESS_HAD_CHANGED'); 
				$mainframe = JFactory::getApplication();
				$msg = OPCLang::_('COM_ONEPAGE_ADDRESS_HAD_CHANGED'); 
				OPCloader::storeError($msg); 
				JFactory::getApplication()->enqueueMessage($msg, 'error'); 
				return $this->returnTerminate($msg); 
				

			}
			else
			{
				// $cart->BT = $saved_bt; 
			}
		}
		
		
		// security: 
		JRequest::setVar('html', ''); 
		
		
		

		$post = JRequest::get('post'); 
		
		
		if (empty($cart->BT)) 
		{
			
		}

		
		
		// fix the customer comment
		// $cart->customer_comment = JRequest::getVar('customer_comment', $cart->customer_comment);
		$cc = JRequest::getVar('customer_comment', ''); 
		$cc2 = JRequest::getVar('customer_note', '');
		
		if (empty($cart->customer_comment)) $cart->customer_comment = $cc2.$cc;
		else $cart->customer_comment = $cc2.$cc;
		
		
		JRequest::setVar('customer_comment', $cart->customer_comment); 	 
		JRequest::setVar('customer_note', $cart->customer_comment); 	 

		if ((defined('VM_VERSION')) && (VM_VERSION >= 3))
		{
			$cart->BT['customer_note'] = $cart->customer_comment; 
		}
		
		$opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat'); 
		$opc_vat = $opc_vat2 = JRequest::getVar($opc_vat_key, ''); 
		if (!empty($opc_vat))
		{
			$opc_euvat_contrymatch = OPCconfig::get('opc_euvat_contrymatch', false); 
			
			if (defined('VM_VERSION') && (VM_VERSION >= 3))
			{
				$address = $cart->getST(); 
				$country = $address['virtuemart_country_id']; 
				$countryST = $country; 
			}
			else
			{
				if (!empty($cart->ST) && (empty($cart->STsameAsBT)))
				{
					$country = $cart->ST['virtuemart_country_id']; 
					$countryST = $country; 
				}
				else
				{
					$country = $countryST = $cart->BT['virtuemart_country_id']; 
				}
			}
			
			$countryBT = $cart->BT['virtuemart_country_id']; 
			
			
			
			OPCloader::opcDebug(__LINE__.':'.$country, 'eu_vat'); 
			
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vat.php'); 
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
			
			
			
			$resp = VAT_STATUS_VALID; 
			$checkvat = self::checkOPCVat($opc_vat, $country, $resp); 
			
			
			
			
			$redoA = array(VAT_STATUS_COUNTRY_ERROR); 
			if ((empty($opc_euvat_contrymatch) && (in_array($resp, $redoA))) && ($countryBT !== $countryST))
			{
				$checkvat = self::checkOPCVat($opc_vat, $countryBT, $resp); 
			}
			
			if ($countryBT !== $countryST) { 
				
				$countryBT2 = OPCvat::getCountry($countryBT); 
				$country2 = OPCvat::getCountry($countryST); 
			}
			else
			{
				$countryBT2 = OPCvat::getCountry($countryBT); 
			}
			
			$ignore = false; 
			// if BT is outside EU we can ignore invalid VAT number: 
			if (($resp === VAT_STATUS_NOT_VALID) && ((!in_array($countryBT2, OPCVat::$european_states))))
			{
				$ignore = true; 
			}
			//opc_euvat_contrymatch
			$opc_euvat_allow_invalid = OPCconfig::get('opc_euvat_allow_invalid', false); 
			if (!empty($opc_euvat_allow_invalid)) $ignore = true;
			
			$cart->BT['opc_vat_info'] = $checkvat; 
			
			$refuse = array(VAT_STATUS_COUNTRY_ERROR); 
			
			
			if ((!$ignore) && (($resp === VAT_STATUS_NOT_VALID) || (!empty($opc_euvat_contrymatch) && (in_array($resp, $refuse)))))
			{
				if (!empty($msg)) {
				 $msg = $checkvat; 
				 return $this->returnTerminate($msg); 
				}
				

			}
			if ($resp === VAT_STATUS_VALID)
			{
				require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shoppergroups.php'); 
				OPCShopperGroups::getSetInitShopperGroup(); 
				
				
			}
			

		}
		
		$OPCcheckout = new OPCcheckout($cart); 
		$loader = new OPCloader(); 
		OPCcheckout::$current_cart =& $cart; 
		
		
		if (empty($is_custom_validated)) {
		  return $this->returnTerminate(); 
		}
		$OPCcheckout->checkoutData(false); 

		
		
		if ($cart->_dataValidated)
		{
			
			
			
			$cart->_confirmDone = true;
			$order = null; 
			
			$order_reuse = OPCconfig::get('order_reuse_fix', false); 
			if (!empty($order_reuse)) {
				$this->loadStoredOrderid($cart); 
			}
			
			$output =  $OPCcheckout->confirmedOrder(); //$cart, $this, $order);
			
			
			$order =& OPCcheckout::$new_order; 
			
			if (!empty($order)) {
				$orderID = $order['details']['BT']->virtuemart_order_id; 
				$customer_number = $order['details']['BT']->customer_number; 
				$this->storePost($orderID, $customer_number); 
				
				
				
			}
			
			
			/*
			jimport( 'joomla.plugin.helper' );
			$plugin = JPluginHelper::getPlugin('vmpayment', 'opctracking');
			if (!empty($plugin))
			{
			$opctracking = true; 
			}
			else 
			{
			$opctracking = false; 
			}
			*/
			
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
			
			$utm_p_load = false; 
			$default = array(); 
			$utm_p = OPCConfig::getValue('opc_config', 'utm_payments', 0, $default, false, false);
			if (!empty($payment_id))
			{
				if (in_array($payment_id, $utm_p))
				$utm_p_load = true; 
			}
			
			
			if ($utm_p_load)
			{
				if (stripos($output, 'utm_nooverride')===false)
				{
					$output = str_replace('&view=pluginresponse', '&utm_nooverride=1&view=pluginresponse', $output); 
					
					$output = str_replace('&amp;view=pluginresponse', '&amp;utm_nooverride=1&amp;view=pluginresponse', $output); 
				}
			}
			
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'removemsgs.php'); 
			
			OPCremoveMsgs::removeMsgs($cart); 

			
			
			
			$semail = JRequest::getVar('EpostProsjekt', ''); 
			
			if (!empty($semail))
			$OPCcheckout->secondMail($order, $semail); 
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
			$email_fix2 = OPCconfig::get('email_fix2', false); 
			
			
			if (!empty($email_fix2))
			{
				
				require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
				$orderModel = OPCmini::getModel('orders');
				$order_id = $order['details']['BT']->virtuemart_order_id; 
				
				//$lastOrder =& $order; 
				// we need to reload the order since the plugins had touched it since... 
				
				VirtueMartControllerOpc::emptyCache(); 
				$lastOrder = $orderModel->getOrder($order_id); 
				$lastOrder['customer_notified'] = 1;
				if (!isset($lastOrder['comments'])) $lastOrder['comments'] = ''; 
				if (isset($lastOrder['details']['BT']))
				{
					if (!isset($lastOrder['order_status'])) $lastOrder['order_status'] = $lastOrder['details']['BT']->order_status; 
					$order_id = $lastOrder['details']['BT']->virtuemart_order_id; 
				}
				try {
					if (!empty($order_id))
					$orderModel->updateStatusForOneOrder($order_id, $lastOrder, true);
				}
				catch (Exception $e) {
					$msg = (string)$e->getMessage(); 
					JFactory::getApplication()->enqueueMessage($msg, 'error'); 
				}
				
				
			}
			
			
			if (!isset($opc_memory)) $opc_memory = '128M'; 
			if (!empty($opc_memory))
			{
				ini_set('memory_limit',$opc_memory);
			}
			$session = JFactory::getSession();
			if (!empty($order))
			if ( $order['details']['BT']->order_total <= 0) {

				$dispatcher = JDispatcher::getInstance();
				$returnValues = $dispatcher->trigger('registerOrderAttempt', array(&$order));
				
				$cart->emptyCart();
			}
		}
		else 
		{
			
			$msg = 'Captcha: '.OPCLang::_('COM_VIRTUEMART_CART_CHECKOUT_DATA_NOT_VALID'); 
			return $this->returnTerminate($msg); 
		}
		
		

		
		// some extensions somehow reset the language, and we need to set it to the proper one: 
		$lang     = JFactory::getLanguage();
		$tag = $lang->getTag(); 
		$filename = 'com_virtuemart';
		$lang->load($filename, JPATH_ADMINISTRATOR, $tag, true);
		$lang->load($filename, JPATH_SITE, $tag, true);

		//$post = JRequest::get('post');
		$mainframe = JFactory::getApplication();		  
		$pathway = $mainframe->getPathway();
		$document = JFactory::getDocument();
		
		//  $html = JRequest::getVar('html', OPCLang::_('COM_VIRTUEMART_ORDER_PROCESSED'), 'post', 'STRING', JREQUEST_ALLOWRAW);
		
		
		
		
		$document->setTitle(OPCLang::_('COM_VIRTUEMART_CART_THANKYOU'));
		$cart->setCartIntoSession(); 
		// now the plugins should have already loaded the redirect html
		// we can safely 
		$virtuemart_order_id = $cart->virtuemart_order_id; 
		
		
		
		JRequest::setVar('view', 'cart'); 
		$_REQUEST['view'] = 'cart'; 
		$_POST['view'] = 'cart'; 
		$_GET['view'] = 'cart'; 

		
		
		if (!class_exists('VirtueMartControllerCart'))
		require_once(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'cart.php'); 

		$VirtueMartControllerCart = new VirtueMartControllerCart(); 
		
		//$view = $VirtueMartControllerCart->getView('cart', 'html');
		
		
		
		
		$display_title = JRequest::getVar('display_title',true);
		if (!empty($cart->pricesCurrency))
		$currencyDisplay = CurrencyDisplay::getInstance($cart->pricesCurrency);
		else
		$currencyDisplay = CurrencyDisplay::getInstance();
		//$view->assignRef('currency', $currencyDisplay); 
		$vars = array ( 
		'currencyDisplay' => $currencyDisplay,
		'cart' => $cart, 	
		'display_title' => $display_title, 
		'currency' => 	$currencyDisplay,
		'html' => $output, 
		
		
		);
		JRequest::setVar('display_loginform', false); 
		$viewHTML = $this->getVMView('cart', $vars, 'cart', 'order_done', 'html');
		
		
		
		// $view->currencyDisplay = $currencyDisplay; 
		
		
		//$view->cart = $cart; 
		
		
		
		// $view->display_title = $display_title;
		
		
		
		
		
		
		
		//$view->currencyDisplay = $currencyDisplay; 
		//$view->assignRef('currencyDisplay', $currencyDisplay); 
		
		
		
		
		//$view->assignRef('html', $output); 
		
		
		// commented for 2.0.22a
		//JRequest::setVar('html', $output);  
		//$view->html = $output; 
		
		

		// Display it all
		try {
			
			/*
	ob_start(); 
	$view->display();
	
	
	
	$html1 = ob_get_clean(); 
	*/
			$html1 = $viewHTML; 
			
			
			
			if (method_exists('shopFunctionsF', 'getLoginForm'))
			{
				$login = shopFunctionsF::getLoginForm(); 
				$html1 = str_replace($login, '', $html1); 
			}
			
		} catch (Exception $e)
		{
			
		}
		

		
		$items = $pathway->getPathwayNames(); 
		$skipp = false; 
		foreach ($items as $i)
		{
			if ($i == OPCLang::_('COM_VIRTUEMART_CART_THANKYOU'))
			$skipp = true; 
		}
		if (!$skipp)
		$pathway->addItem(OPCLang::_('COM_VIRTUEMART_CART_THANKYOU'));
		
		
		
		
		
		if (empty($html1)) $html1 = $output; 
		JRequest::setVar('view', 'opccart'); 
		JRequest::setVar('contoller', 'opccart'); 
		$html2 = ''; 
		if (!empty($append_details))
		{
			// ok, lets try to alter it with the order details 
			JRequest::setVar('order_pass',$order['details']['BT']->order_pass);
			JRequest::setVar('order_number',$order['details']['BT']->order_number);
			JRequest::setVar('virtuemart_order_id',$order['details']['BT']->virtuemart_order_id);
			
			
			//JRequest::setVar('tmpl', 'component'); 
			
			$html2 = $this->getVMView('orders', array(), 'orders', 'details', 'html'); 
			
			
		}
		
		$allhtml = $html1.$html2; 
		$this->runExtAfter($allhtml); 
		
		$allhtml = $html1.$html2; 
		$this->runExtAfter($allhtml); 
		ob_start(); 
		?><script> 
		if (typeof sessMin == 'undefined') var sessMin = 15; 
		if (typeof typeof jQuery.fancybox == 'undefined') var sessMin = 15;
		if ((typeof jQuery != 'undefined') && (typeof jQuery.fn.chosen == 'undefined')) jQuery.fn.chosen = function() {;}; 
		if ((typeof jQuery != 'undefined') && (typeof jQuery.fn.facebox == 'undefined')) jQuery.fn.facebox = function() {;}; 
		if ((typeof jQuery != 'undefined') && (typeof jQuery.fn.fancybox == 'undefined')) jQuery.fn.fancybox = function() {;}; 
		
		</script><?php
		$allhtml .= ob_get_clean(); 
		
		if (!empty(VirtueMartControllerOpc::$isjson)) {
			if (!isset($return)) $return = array(); 
			$return['html'] = $allhtml; 
		}
		else {
		  echo $allhtml; 
		}
		JRequest::setVar('html', ''); 
		JRequest::setVar('html', $allhtml); 
		
		
		JRequest::setVar('view', 'cart'); 
		$_REQUEST['view'] = 'cart'; 
		$_POST['view'] = 'cart'; 
		$_GET['view'] = 'cart'; 
		if (!empty($theme_fix1))
		{
			
			JRequest::setVar('layout', 'order_done'); 
		}
		
		
		/*
		$_GET = $savedGET; 
		$_POST = $savedPOST; 
		$_REQUEST = $savedREQUEST; 
	*/
		
		if (!isset($opc_memory)) $opc_memory = '128M'; 
		if (!empty($opc_memory))
		{
			ini_set('memory_limit',$opc_memory);
		}
		
		if (!empty(VirtueMartControllerOpc::$isjson)) {
			return $this->printJson($return); 
		}
		/*not needed: 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'removemsgs.php'); 
		OPCremoveMsgs::makeUnique();
		OPCremoveMsgs::removeMsgs($cart, true); 
		$x = JFactory::getApplication()->getMessageQueue(); 
		*/
		
		//empty coupon code on sucessfull orders
		
		if ((empty($cart->products)) && (empty($cart->cartProductsData))) {
		  $cart->couponCode = ''; 
		  $session = JFactory::getSession(); 
		  $session->set('opc_last_coupon', ''); 
		  $cart->setCartIntoSession();
		}
		
		return; 
		
		
	}
	
	
	
	
	// original code from shopfunctionsF::renderMail
	private static function getVMView($viewName, $vars=array(),$controllerName = NULL, $layout='default', $format='html')
	{
		$originallayout = JRequest::getVar( 'layout' );
		if(!class_exists('VirtueMartControllerVirtuemart')) require(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'virtuemart.php');
		// 		$format = (VmConfig::get('order_html_email',1)) ? 'html' : 'raw';
		
		// calling this resets the layout
		$controller = new VirtueMartControllerVirtuemart();
		JRequest::setVar( 'layout', $layout );
		

		//Todo, do we need that? refering to http://forum.virtuemart.net/index.php?topic=96318.msg317277#msg317277
		
		if (method_exists($controller, 'addViewPath')) { 
			$controller->addViewPath(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'views');
		}
		else
		if (method_exists($controller, 'addIncludePath')) {
			$controller->addIncludePath(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'views');
		}
		
		
		$view = $controller->getView($viewName, $format);
		
		
		$view->assignRef('layout', $layout); 
		$view->assignRef('format', $format); 
		
		$view->setLayout($layout); 
		
		if (!$controllerName) $controllerName = $viewName;
		$controllerClassName = 'VirtueMartController'.ucfirst ($controllerName) ;
		if (!class_exists($controllerClassName)) require(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$controllerName.'.php');

		//Todo, do we need that? refering to http://forum.virtuemart.net/index.php?topic=96318.msg317277#msg317277
		if (method_exists($view, 'addTemplatePath')) {
			$view->addTemplatePath(JPATH_VM_SITE.'/views/'.$viewName.'/tmpl'); 
		} else { 
			if (method_exists($view, 'addIncludePath')) 
			{
				$view->addIncludePath( JPATH_VM_SITE.'/views/'.$viewName.'/tmpl' );
			}
		}
		
		$app = JFactory::getApplication(); 
		$template = $app->getTemplate(); 
		
		$tp = JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'cart'.DIRECTORY_SEPARATOR.'order_done.php'; 
		if (method_exists($view, 'addTemplatePath')) {
			$view->addTemplatePath( JPATH_VM_SITE.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'cart'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR );
		}
		else
		{
			if (method_exists($view, 'addIncludePath')) {
				$view->addIncludePath( JPATH_VM_SITE.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'cart'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR );
			}
		}
		
		
		if (file_exists($tp))
		{
			if (method_exists($view, 'addTemplatePath')) { 
				$view->addTemplatePath(JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'cart'.DIRECTORY_SEPARATOR); 
			}
			else
			{
				if (method_exists($view, 'addIncludePath')) {
					$view->addIncludePath( JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'cart'.DIRECTORY_SEPARATOR );
				}
			}
		}

		$vmtemplate = VmConfig::get('vmtemplate','default');
		if($vmtemplate=='default'){
			if(JVM_VERSION >= 2){
				$q = 'SELECT `template` FROM `#__template_styles` WHERE `client_id`="0" AND `home`="1"';
			} else {
				$q = 'SELECT `template` FROM `#__templates_menu` WHERE `client_id`="0" AND `menuid`="0"';
			}
			$db = JFactory::getDbo();
			$db->setQuery($q);
			$template = $db->loadResult();
		} else {
			$template = $vmtemplate;
		}

		if($template){
			if (method_exists($view, 'addTemplatePath')) {
				$view->addTemplatePath(JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.$viewName);
			}
			else
			{
				if (method_exists($view, 'addIncludePath')) {
					$view->addIncludePath(JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.$viewName);
				}
			}
		} else {
			if(isset($db)){
				$err = $db->getErrorMsg() ;
			} else {
				$err = 'The selected vmtemplate is not existing';
			}
			
		}

		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		$path = OPCmini::getVMTemplate('cart', 'order_done'); 
		if (!empty($path))
		if (method_exists($view, 'addLayoutPath')) {
			
			
			
			
			$view->addLayoutPath('cart', $path); 
			if (method_exists($view, 'addIncludePath')) {
				$view->addIncludePath($path); 
			}
		}
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
		
		
		if (method_exists($view, 'addTemplatePath'))
		$view->addTemplatePath( JPATH_VM_SITE.'/views/cart/tmpl' );
		else
		if (method_exists($view, 'addIncludePath')) 
		{
			$view->addIncludePath( JPATH_VM_SITE.'/views/cart/tmpl' );
		}
		
		foreach ($vars as $key => $val) {
			$view->$key = $val;
		}
		ob_start(); 
		$html = $view->display();
		$html2 = ob_get_clean(); 
		
		JRequest::setVar( 'layout', $originallayout );
		return $html.$html2; 
		
		
		
	}
	
	// support for non-standard extensions
	// will be changed in the future over OPC extension tab and API
	function runExt(&$cart)
	{
		require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'third_party'.DIRECTORY_SEPARATOR.'third_party_set_shipping.php'); 
		
	}
	/**
	*
	*  ORIGINAL CODE FROM: \components\com_virtuemart\helpers\cart.php
	*
	* Validate the coupon code. If ok,. set it in the cart
	* @param string $coupon_code Coupon code as entered by the user
	* @author Oscar van Eijk
	* TODO Change the coupon total/used in DB ?
	* @access public
	* @return string On error the message text, otherwise an empty string
	*/
	function setCoupon(&$cart)
	{
		$session = JFactory::getSession(); 
		$session->set('coupon', '', 'awocoupon');
		JFactory::getApplication()->set('is_rupostel_opc', true);
		if (!class_exists('CouponHelper')) {
			require(JPATH_VM_SITE .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'coupon.php');
		}
		$nc = JRequest::getVar('new_coupon', ''); 
		if (!empty($nc)) {
			

			$session->set('opc_last_coupon', $nc); 
		}
		
		$coupon_code = JRequest::getVar('new_coupon', $cart->couponCode); 
		if (!empty($coupon_code))

		JRequest::setVar('coupon_code', $coupon_code); 
		// stAn, getCartPrices calls coupon process !
		
		if (isset($cart->cartPrices))
		{
			$prices = $cart->cartPrices; 
		}
		else
		{
			$prices = $cart->getCartPrices();
		}
		
		if (!empty($coupon_code))
		$msg = CouponHelper::ValidateCouponCode($coupon_code, $prices['salesPrice']);
		
		if (!empty($msg)) {

			$cart->couponCode = '';
			$opc_debug = OPCconfig::get('opc_debug', false);  if (!empty($opc_debug)) { JFactory::getApplication()->enqueueMessage('OPC Debug: Coupon removed at '.__FILE__.':'.__LINE__); }
			$cart->setCartIntoSession();
			JFactory::getApplication()->enqueueMessage($msg, 'error'); 
			
			return $msg;
		}
		
		
		
		
		//if (!empty($coupon_code))
		if (!empty($coupon_code))
		$cart->couponCode = $coupon_code;
		
		$cart->setCartIntoSession();
		// THIS IS NOT TRUE AS THE COUPON HAS NOT YET BEEN PROCESSED: return 'Virtuemarts cart says: '.OPCLang::_('COM_VIRTUEMART_CART_COUPON_VALID');
		/*
		JPluginHelper::importPlugin('vmcoupon');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('plgVmCouponHandler', array($_code,&$this->_cartData, &$this->_cartPrices));
		if(!empty($returnValues)){
			foreach ($returnValues as $returnValue) {
				if ($returnValue !== null  ) {
					return $returnValue;
				}
			}
		}
		
		if (method_exists($calc, 'setCartPrices')) $vm2015 = true; 
		else $vm2015 = false; 
		if ($vm2015)
		$calc->setCartPrices(array()); 
		*/
		// this will be loaded by OPC further
		// $calc->getCheckoutPrices(  $ref->cart, false);
		
		
	}
	
	
	function setSecret()
	{
		
	}
	
	private static $shopper_groups; 
	
	private function softVatCheck($vatid)
	{
		$eu = array('BE', 'BG', 'CZ', 'DK', 'DE', 'EE', 'IE', 'EL', 'ES', 'FR', 'HR', 'IT', 'CY', 'LV', 'LT', 'LU', 'HU', 'MT', 'NL', 'AT', 'PL', 'PT', 'RO', 'SI', 'SK', 'FI', 'SE', 'UK', 'GR', 'GB'); 
		$vat_country = substr($vatid, 0, 2); 
		$vat_country = strtoupper($vat_country); 
		$vat_number = substr($vatid, 2); 
		$vat_number = strtoupper($vat_number); 
		if (!in_array($vat_country, $eu)) return 'PREFIXERROR'; 
		
		if ($vat_country == 'GR') $vat_country = 'EL'; 
		if ($vat_country == 'UK') $vat_country = 'GB'; 
		
		$vat_number = str_replace(' ', '', $vat_number); 
		$vat_number = str_replace('-', '', $vat_number); 
		$vat_number = str_replace('/', '', $vat_number); 
		if (!class_exists('SwOfficialNumberValidator'))
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'softvat.php'); 
		
		$SwOfficialNumberValidator = new SwOfficialNumberValidator(); 
		
		//http://ec.europa.eu/taxation_customs/vies/faq.html#item_2
		switch ($vat_country)
		{
		case 'AT':
			if (!strlen($vat_number) != 9) return 'Invalid format, expected 9 characters'; 
			if (substr($vat_number, 0, 1)!= 'U')  return 'Invalid format, expected U after country code'; 
			break; 
		case 'BE': 
			if (!strlen($vat_number) != 10) return 'Invalid format, expected 10 digits'; 
			if (!is_numeric($vat_number)) return 'Invalid format, expecting a number after country code'; 
			$check = $SwOfficialNumberValidator->checkBeVat($vat_number); 
			if (empty($check)) return 'Vat number did not pass checksum validation'; 
			break; 
		case 'BG': 
			if (!((!strlen($vat_number) == 10) || ((!strlen($vat_number) == 9)))) return 'Invalid format, expected 9 or 10 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'CY': 
			if (!strlen($vat_number) != 9) return 'Invalid format, expected 9 digits'; 
			break; 
		case 'CZ': 
			if (!((strlen($vat_number)>=8) && ((strlen($vat_number)<=10)))) return 'Invalid format, expected 8 to 10 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'DE': 
			if (!strlen($vat_number) != 9) return 'Invalid format, expected 9 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'DK':
			if (!strlen($vat_number) != 8) return 'Invalid format, expected 8 digits'; 
			break; 
		case 'EE': 
			if (!strlen($vat_number) != 9) return 'Invalid format, expected 9 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'EL': 
			if (!strlen($vat_number) != 9) return 'Invalid format, expected 9 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'ES': 
			if (!strlen($vat_number) != 9) return 'Invalid format, expected 9 digits'; 
			$first = substr($vat_number, 0, 1); 
			$last = substr($vat_number, -1); 
			if (ctype_digit ($first) && (ctype_digit ($last))) return 'Invalid format, first and last character after language code cannot be numeric'; 
			break; 
		case 'FI': 
			if (!strlen($vat_number) != 8) return 'Invalid format, expected 9 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'FR': 
			$first = substr($vat_number, 0,1); 
			$s = substr($vat_number, 1, 1); 
			if (ctype_digit ($first) || (ctyle_digit($s))) return 'Invalid format, first and second characters cannot be a number';
			if (!strlen($vat_number) != 11) return 'Invalid format, expected 11 characters'; 
			$res = $SwOfficialNumberValidator->checkFrVat($vat_number); 
			if (empty($res)) return 'Vat number did not pass checksum validation'; 
			break; 
		case 'GB': 
			$p = array(9, 12, 5); 
			$l = strlen($vat_number); 
			if (!in_array($l, $p))  return 'Invalid format, expecting either 5, 9, or 12 characters after the country code';
			if (($l == 9) || ($l==12))
			{
				if (!ctype_digit($vat_number))  return 'Invalid format, expecting a number after country code';  
			}
			break; 
		case 'HR':
			if (!strlen($vat_number) != 11) return 'Invalid format, expected 11 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'HU': 
			if (!strlen($vat_number) != 8) return 'Invalid format, expected 8 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'IE': 
			if (!((strlen($vat_number)>=8) && ((strlen($vat_number)<=9)))) return 'Invalid format, expected 8 or 9 characters'; 
			break; 
		case 'IT': 
			if (!strlen($vat_number) != 11) return 'Invalid format, expected 11 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'LT': 
			$p = array(9, 12); 
			$l = strlen($vat_number); 
			if (!in_array($l, $p))  return 'Invalid format, expecting either 9 or 12 digits after the country code';
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'LU':
			if (!strlen($vat_number) != 8) return 'Invalid format, expected 8 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'LV':
			if (!strlen($vat_number) != 11) return 'Invalid format, expected 11 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'MT': 
			if (!strlen($vat_number) != 8) return 'Invalid format, expected 11 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'NL': 
			if (!strlen($vat_number) != 12) return 'Invalid format, expected 11 digits'; 
			if (substr($vat_number, 9, 1) != 'B') return 'Invalid format, expected B at 10th position'; 
			break; 
		case 'PL': 
			if (!strlen($vat_number) != 10) return 'Invalid format, expected 10 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'PT': 
			if (!strlen($vat_number) != 9) return 'Invalid format, expected 9 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'RO': 
			if (!((strlen($vat_number)>=2) && ((strlen($vat_number)<=10)))) return 'Invalid format, expected 2 to 10 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'SE': 
			if (!strlen($vat_number) != 12) return 'Invalid format, expected 12 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'SI': 
			if (!strlen($vat_number) != 8) return 'Invalid format, expected 8 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
		case 'SK': 
			if (!strlen($vat_number) != 10) return 'Invalid format, expected 10 digits'; 
			if (!ctype_digit ($vat_number)) return 'Invalid format, expecting a number after country code'; 
			break; 
			
			
			
		}
		
	}
	
	private function checkbitVat()
	{
		include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'bit_vm_check_vatid'.DIRECTORY_SEPARATOR.'checkvat.php'); 
		return $return; 
	}
	public static function prepareClearMsgs() {
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'removemsgs.php'); 
		return OPCremoveMsgs::prepareClearMsgs(); 
		
	}
	public static function clearMsgs($stored=array())
	{
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'removemsgs.php'); 
		return OPCremoveMsgs::clearMsgs($stored); 

		
	
	}
	
	
	
	function opc()
	{

		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'removemsgs.php');
		OPCremoveMsgs::loadAndClearMsgsInSession(); 
		

	
		$cart_virtuemart_product_id = JRequest::getVar('cart_virtuemart_product_id', null);
		$this->getRequires(); 
		
		
		VmConfig::loadConfig(); 
		
		
		

		// stAn, it's very important that we set the address at first: 
		$cart = VirtueMartCart::getCart(false);
		
		$store_coupon = $cart->couponCode; 
		
		if (empty($store_coupon))
		OPCremoveMsgs::removeMsg(JText::_('COM_VIRTUEMART_COUPON_CODE_INVALID')); 
		
		if (!empty($store_coupon)) {
			  if (is_array($cart->_triesValidateCoupon)) {
				  $cart->_triesValidateCoupon[$store_coupon] = $store_coupon; 
			  }
			}
		
		// start of new OPC set address (do it as the very first action...
		
		$stopen = JRequest::getVar('shiptoopen', false); 
		if ($stopen === 'false') $stopen = false; 
		if ($stopen === 'true') $stopen = true; 
		if ($stopen === '1') $stopen = true; 
		
		$user_id = JFactory::getUser()->get('id'); 
		
		
		
		
		
		
		if (($stopen === true))
		{
			$stopen = true; 
			$sa = JRequest::setVar('sa', 'adresaina'); 
			
		}
		else
		$stopen = false; 
		
		
		
		//$this->setAddress($cart, true, $stopen); 
		OPCloader::opcDebug($cart->BT, 'address0'); 
		OPCloader::opcDebug($cart->ST, 'address0'); 
		
		$this->setAddress($cart); 
		
		
		
		OPCloader::opcDebug($cart->BT, 'address1'); 
		OPCloader::opcDebug($cart->ST, 'address1'); 
		
		$BT = $cart->BT; 
		
		
		$this->storePost(); 
		if (!$stopen) {
			$cart->ST = 0;  
		}
		
		
		
		
		
		
		
		
		OPCUserFields::checkCart($cart); 
		
		OPCloader::opcDebug($cart->BT, 'address2');
		OPCloader::opcDebug($cart->ST, 'address2'); 
		
		$this->setExtAddress($cart, false, $stopen);
		
		$zip = JRequest::getVar('shipto_zip'); 
		
		

		OPCShopperGroups::setShopperGroupsController(); 
		
		// IMPORTANT: this function calls shipping if Vm's automatic enabled
		if (method_exists($cart, 'prepareCartData'))
		{
			ob_start(); 
			$stored_coupon = $cart->couponCode; 
			$cart->prepareCartData(false); 
			$cart->couponCode = $stored_coupon; 
			$zz = ob_get_clean(); 
		}

		
		OPCloader::opcDebug($cart->BT, 'address3');
		OPCloader::opcDebug($cart->ST, 'address3'); 
		
		
		// US and Canada fix, show no tax for no state selected
		if (!isset($cart->BT['virtuemart_state_id'])) $cart->BT['virtuemart_state_id'] = ''; 
		if (!empty($cart->ST))
		{
			// if the VM uses BT address instead of ST address in calculation, uncomment the following line: 
			// $cart->BT = $cart->ST;   // this only applies to the display of the checkout, not actual saving of the data
			if (!isset($cart->ST['virtuemart_state_id'])) $cart->ST['virtuemart_state_id'] = ''; 
		}
		
		/* end of new opc set address is here */
		
		OPCremoveMsgs::clearMsgs(); 
		
		
		
		$selected_template = OPCrenderer::getSelectedTemplate(); 
		
		//mobile handling: 
		if (!defined('OPC_DETECTED_DEVICE'))
		{
			if (class_exists('OPCplugin'))
			{
				OPCplugin::detectMobile(); 
			}
		}

		

		
		OPCLang::loadLang(); 
		
		
		
		
		//$this->setShopperGroups(); 
		include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php');
		
		
		$userModel = OPCmini::getModel('user');
		
		
		$cmd = JRequest::getVar('cmd', ''); 
		if ($cmd === 'checkbitvat')
		$checkvat = $this->checkBitVat(); 

		
		$opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat'); 
		$opc_vat = $opc_vat2 = JRequest::getVar($opc_vat_key, ''); 
		if (!empty($opc_vat))
		OPCloader::opcDebug(__LINE__.': vat entered: '.$opc_vat, 'eu_vat'); 
		if (($cmd === 'checkvatopc') || (!empty($opc_vat)))
		{
			$opc_euvat_contrymatch = OPCconfig::get('opc_euvat_contrymatch', false); 
			$country = $countryBT = JRequest::getVar('virtuemart_country_id', null); 
			
			$countryST = JRequest::getVar('shipto_virtuemart_country_id', null); 
			if (!empty($countryST)) {
				$country = $countryST; 
			}
			
			
			OPCloader::opcDebug(__LINE__.':'.$country, 'eu_vat'); 
			
			if (!empty($country))
			{
				$resp = VAT_STATUS_VALID;
				$checkvat = self::checkOPCVat($opc_vat, $country, $resp); 
				
				
				$redoA = array(VAT_STATUS_COUNTRY_ERROR); 
				if ((empty($opc_euvat_contrymatch) && (in_array($resp, $redoA))) && ($countryBT !== $countryST))
				{
					$checkvat = self::checkOPCVat($opc_vat, $countryBT, $resp); 
				}
				
				
			}
			else
			{	
				$checkvat = self::checkOPCVat($opc_vat); 
			}
			
			OPCloader::opcDebug($checkvat, 'eu_vat'); 

			
			
		}
		
		
		
		OPCShopperGroups::setShopperGroupsController(); 
		
		if (method_exists($userModel, 'getCurrentUser'))
		{
			
			$user = $userModel->getCurrentUser();
			
			self::$shopper_groups = $user->shopper_groups; 
			
			if (!empty($user->virtuemart_shipmentmethod_id))
			{
				$user->virtuemart_shipmentmethod_id = 0; 
				$user->virtuemart_paymentmethod_id = 0; 
			}
		}
		
		$session = JFactory::getSession();
		
		$b = $session->set('eurobnk', null, 'vm'); 
		
		
		if (empty($euvat_shopper_group))		 
		{
			jimport( 'joomla.html.parameter' );
			if (class_exists('plgSystemBit_vm_change_shoppergroup'))
			{
				$session = JFactory::getSession();
				$sg = $session->get('vm_shoppergroups_add', array(), 'vm'); 
				
				$dispatcher = JDispatcher::getInstance();
				JPluginHelper::importPlugin('system', 'plgSystemBit_vm_change_shoppergroup', true, $dispatcher); // very important
				$document = JFactory::getDocument();
				JRequest::setVar('format_override', 'html'); 
				$_REQUEST['view'] = 'cart'; 
				$_REQUEST['option'] = 'com_virtuemart'; 
				$doctype = $document->getType();
				
				$dispatcher->trigger('onAfterRender'); 
				JRequest::setVar('format_override', 'raw'); 
				$sg = $session->get('vm_shoppergroups_add', array(),  'vm'); 
				
				
			}
		}
		JResponse::setBody('');
		
		// security: 
		JRequest::setVar('virtuemart_shoppergroup_id', null, 'post');
		

		// since vm2.0.21a we need to load the language files here
		if (method_exists('VmConfig', 'loadJLang'))
		{
			$lang = JFactory::getLanguage();
			$extension = 'com_virtuemart';
			$lang->load($extension); //  when AJAX it needs to be loaded manually here >> in case you are outside virtuemart !!!

			VmConfig::loadJLang('com_virtuemart_orders', true); 
			VmConfig::loadJLang('com_virtuemart_shoppers', true); 

		}	  
		
		/// load shipping here
		$vars = JRequest::get('post'); 
		
		// custom tag test
		$cmd = JRequest::getVar('cmd', 'get_shipping'); 
		

		
		
		$doc = JFactory::getDocument();
		$type = get_class($doc); 
		if ($type == 'JDocumentRAW')
		{
			//C:\Documents and Settings\Rupos\Local Settings\Temp\scp02371\srv\www\clients\client1\web90\web\vm2\components\com_onepage\overrides\
			//require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'opchtml.php'); 
			//JFactory::$instance = new JDocumentOpchtml(); 
			//JFactory::$document = new JDocumentOpchtml(); 
			
		}
		/*
	$doc->addCustomTag = create_function('$string', 'return;');  
	$doc->addCustomTag( '<!-- This is a comment. -->' );
	*/
		$c = JRequest::getVar('virtuemart_currency_id', 0); 

		JRequest::setVar('virtuemart_currency_id', (int)JRequest::getVar('virtuemart_currency_id'));

		/* to test the currency: */
		$mainframe = Jfactory::getApplication();
		$virtuemart_currency_id = $mainframe->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',JRequest::getInt('virtuemart_currency_id') );




		// end custom tag test
		$view = $this->getView('cart', 'html');
		
		$cmd = JRequest::getCmd('cmd', 'get_shipping'); 
		
		$return = array(); 
		$return['cmd'] = $cmd; 
		
		if (!empty($checkvat))
		$return['checkvat'] = $checkvat; 

		if ($opc_vat2 != $opc_vat)
		{
			$opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat'); 
			$cart->BT[$opc_vat_key] = $opc_vat; 
			$return['new_vat'] = $opc_vat; 
		}

		if (isset(self::$last_vat_address))
		$return['klarna'] = self::$last_vat_address; 
		
		
		
		
		
		$db = JFactory::getDBO(); 
		// we will check it on each request 
		// to support google autocomplete 
		// if ($cmd == 'checkusername')
		{
			$username = JRequest::getVar('username', ''); 
			$user = JFactory::getUser(); 
			$un = $user->get('username'); 
			if ($un == $username)
			{
				// do not complain if entering the same username of already registered
				$return['username_exists'] = false; 
			}
			else
			if (!empty($username))
			{
				$q = "select username from #__users where username = '".$db->escape($username)."' limit 0,1"; 
				$db->setQuery($q); 
				$r = $db->loadResult(); 
				if (!empty($r))
				{
					$return['username_exists'] = true; 
				}
				else
				$return['username_exists'] = false; 
			}
		}
		
		$email = JRequest::getVar('email', ''); 
		
		$return['email_exists'] = false; 
		if (($cmd === 'checkemail') 
				|| ((!empty($email)) 
					&& ((!empty($opc_no_duplicit_email))) 
					|| (!empty($opc_no_duplicit_username) && ((!empty($op_usernameisemail))))))
		{
			
			
			
			$return['email_to_check'] = $email; 
			
			$return['email'] = $email;
			$user = JFactory::getUser(); 
			$ue = $user->get('email'); 
			$user_id = $user->get('id'); 
			
			
			if (!empty($user_id) && ($email === $ue))
			{
				// do not complain if user is logged in and enters the same email address
				$return['email_exists'] = false; 
				$return['user_equals_login'] = true; 
			}
			else
			if (!empty($email))
			{
				$q = "select email from #__users where username = '".$db->escape($email)."' or email = '".$db->escape($email)."' limit 0,1"; 
				$db->setQuery($q); 
				$r = $db->loadResult(); 
				
				
				$return['q'] = $q; 
				if (!empty($r))
				{
					$return['email_exists'] = true; 
				}
				
				
			}
			
			$return['email_was_checked'] = true; 
		}
		
		
		if ($cmd === 'get_klarna_address')
		{
			
			if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'klarna'.DIRECTORY_SEPARATOR.'klarna'.DIRECTORY_SEPARATOR.'api'.DIRECTORY_SEPARATOR.'klarna.php'))
			{
				
				$klarnaaddress = $this->getKlarnaAddress(); 
				
				if (!empty($klarnaaddress))
				{
					
					$ret = array('cmd'=>'getKlarna', 
					'shipping'=>'opc_do_not_update', 
					'klarna'=>$klarnaaddress,
					'totals_html'=>'', 
					'payment'=>''
					
					); 
					return $this->printJson($ret); 
					
				}
			}
		}
		
		
		
		
		
		
		$OPCloader = new OPCloader; 
		
		if (empty($opc_debug))	
		OPCloader::$debug_disabled = true; 
		else 
		OPCloader::$debug_disabled = false; 

		
		
		
		
		
		$opcsavedST = $cart->ST; 
		$opcsavedBT = $cart->BT; 
		$stopen = JRequest::getVar('shiptoopen', 0); 
		
		
		
		
		if ($stopen === 'false') $stopen = 0; 
		
		if (empty($stopen)) 
		{
			$sa = JRequest::getVar('sa', ''); 
			if ($sa == 'adresaina') $stopen = 1; 
		}
		
		if ($stopen)
		{
			$restoreST = false;
			$restoreBT = true;
		}
		else
		{
			$cart->ST = 0; 
			$restoreST = true;
			$restoreBT = false;
		}
		
		
		//$virtuemart_currency_id;  = $mainframe->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',JRequest::getInt('virtuemart_currency_id') );
		$cart->paymentCurrency = $virtuemart_currency_id; 
		if (defined('VM_VERSION') && (VM_VERSION >= 3))
		{
			
			if (method_exists($cart, 'prepareCartProducts')) {
				ob_start(); 
				$cart->prepareCartProducts(); 
				$zz = ob_get_clean(); 
			}
			
			
			
		}
		
		
		
		
		
		if ($cmd === 'getST')
		{
			
			
			$sthtml = OPCLoggedShopper::getSTHtml($cart); 
			
			$cartViewref = OPCrenderer::getInstance(); 
			$cartViewref->cart =& $cart; 
			$mkey = 'op_shipto'; 
			$sta = array($mkey => $sthtml); 
			OPCrenderer::addModules($cartViewref, $sta); 
			$return['sthtml'] = $sta[$mkey]; 
			
			

		}
		
		
		
		if (!isset($cart->vendorId))
		{
			$cart->vendorId = 1; 
		}
		
		if ($cmd == 'updateattributes')
		{
			$arr = $this->updateattributes($cart); 
			if (!empty($arr))
			foreach ($arr as $key=>$val)
			$return[$key] = $val; 
		}

		
		
		
		if (!empty($checkbox_products))
		{
			require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'addtocartaslink.php'); 
			OPCAddToCartAsLink::checkCheckboxProducts($cart); 
		}

		
		
		if ($cmd == 'update_product')
		{
			
			
			if (empty($cart->couponCode)) {
				/*
				$session = JFactory::getSession(); 
				$nc = $session->get('opc_last_coupon', ''); 
				if (!empty($nc)) {
					//jimport( 'joomla.application.component.helper' );
					//$c = JComponentHelper::getComponent('com_awocoupons'); 
					$c = OPCmini::extExists('com_awocoupon'); 
					if (!empty($c)) {
					$cart->couponCode = $nc; 
					}
				}
				*/
				$c = OPCloader::getCouponCode($cart); 
				if (!empty($c))
				$cart->couponCode = $c;
				
			}
			
			
			$cart_virtuemart_product_id = JRequest::getVar('cart_virtuemart_product_id', null);
			
			
			OPCloader::opcDebug(__LINE__.':'.var_export($cart_virtuemart_product_id, true), 'quantity_update'); 
			
			$quantity = JRequest::getVar('quantity', array()); 
			
			if (!is_array($quantity)) {
				$quantity_num = (float)$quantity; 
			}
			else
			{
				if (count($quantity) === 1) {
				if (empty($cart_virtuemart_product_id)) {
					$keys = array_keys($quantity); 
					if (!empty($keys))
					$cart_virtuemart_product_id = reset($keys);
				}
				$quantity_num = (float)reset($quantity); 
				}
			}
			
			
			$updated = false; 	
			// test error message: 
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'cart_override.php'); 
			$OPCcheckout = new OPCcheckout($cart); 
			if (count($quantity)>1) {
				foreach ($quantity as $ci=>$qi) {
					if (isset($cart->products[$ci])) {
						$product = $cart->products[$ci]; 
						OPCcheckout::$current_cart =& $cart; 
						$adjustQ = true; 
						$OPCcheckout->checkForQuantities($product, $qi, $e, $adjustQ); 
					}
				}
			}
			else
			if (isset($cart->products[$cart_virtuemart_product_id])) {
				$product = $cart->products[$cart_virtuemart_product_id]; 
				
				$e = ''; 
				
				
				
				if (!empty($quantity_num)) {
					OPCcheckout::$current_cart =& $cart; 
					$adjustQ = true; 
					$OPCcheckout->checkForQuantities($product, $quantity_num, $e, $adjustQ); 
					//$quantity = $quantity_num; 
				}
				
				if (!empty($e)) {
					JFactory::getApplication()->enqueueMessage($e, 'error'); 
				}
				
			}

			
			
			if (defined('VM_VERSION') && (VM_VERSION >= 3))
			{
			
				if (!is_array($quantity))
				{
					
					$arr = array($cart_virtuemart_product_id => (int)$quantity); 
					JRequest::setVar('quantity', $arr); 
					
					$x = JRequest::getVar('quantity'); 
					
				}
			}
			else
			{
				
				$is_attr = JRequest::getVar('update_attribute_s', false); 
				if (!empty($is_attr))
				{
					$this->updateattributesVM2($cart); 
				}
				else
				{
					////if (empty($quantity) || (empty($quantity[$cart_virtuemart_product_id])))
					if (!is_null($cart_virtuemart_product_id))
					if (empty($quantity_num))
					{
						
						
						$cart->removeProductCart($cart_virtuemart_product_id);
						$cart_virtuemart_product_id = 0; 
						$updated = true; 				  
						
					}
				}
			}
			
				
			OPCloader::opcDebug(__LINE__.':'.var_export($cart_virtuemart_product_id, true), 'quantity_update'); 
			
			if (!is_null($cart_virtuemart_product_id))
			{
				
				
				
				
				if (defined('VM_VERSION') && (VM_VERSION >= 3))
				{
					$is_attr = JRequest::getVar('update_attribute_s', false); 
					
					
					if (!empty($is_attr))
					{
						$this->updateattributesVM3($cart); 
					}
					else
					{
						
						if (empty($quantity_num))
						{
							$_POST['delete_'.$cart_virtuemart_product_id] = true; 
							
							
						}
						
						
						
						$cart->updateProductCart(); 
						if (!empty($quantity_num))
						{
							$cart->cartProductsData[$cart_virtuemart_product_id]['quantity'] = (int)$quantity_num; 
						}
						else
						{
							
							if(!class_exists('vmCustomPlugin')) require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmcustomplugin.php');
							JPluginHelper::importPlugin('vmcustom');
							$dispatcher = JDispatcher::getInstance();
							$addToCartReturnValues = $dispatcher->trigger('plgVmOnRemoveFromCart',array($cart,$cart_virtuemart_product_id));

							
							
							unset($cart->cartProductsData[$cart_virtuemart_product_id]); 
							unset($cart->products[$cart_virtuemart_product_id]);
							
							if (empty($cart->cartProductsData)) {
								$session = JFactory::getSession(); 
								$session->set('opc_last_coupon', ''); 
								$cart->couponCode = ''; 
								
								$opc_debug = OPCconfig::get('opc_debug', false);  if (!empty($opc_debug)) { JFactory::getApplication()->enqueueMessage('OPC Debug: Coupon removed at '.__FILE__.':'.__LINE__); }
								
							}
							
							
						}
						
						if (isset($cart->_productAdded))
						{
							$cart->_productAdded = true; 
						}
						ob_start(); 
						$stored_coupon = $cart->couponCode; 
						$cart->prepareCartData(); 
						$cart->couponCode = $stored_coupon; 
						$zz = ob_get_clean(); 
					}
					
					
					
					
					
				}
				else
				{
					$cart->updateProductCart($cart_virtuemart_product_id, $quantity_num); 
				}
				
				$to_debug = array('line'=>__LINE__,'cart_virtuemart_product_id'=>$cart_virtuemart_product_id, 'quantity'=>$quantity, 'quantity_num'=>$quantity_num); 
				OPCloader::opcDebug($to_debug, 'quantity_update'); 
			}
			else
			{
				
				OPCloader::opcDebug(__LINE__.':'.var_export($quantity, true), 'quantity_update'); 
				
				if (defined('VM_VERSION') && (VM_VERSION >= 3))
				{
					if (is_array($quantity))
					{
						
						
						
						$removed = array(); 
						//updating on VM3 works ok, but when having zero quantity it's not properly removed
						foreach ($quantity as $kkp=>$qx) {
							if (empty($qx)) {
								$_POST['delete_'.$kkp] = 1; 
								$removed[$kk] = $kkp;
							}
						}
						
						
						$cart->updateProductCart(); 
						
						if (!empty($removed)) {
						
							if(!class_exists('vmCustomPlugin')) require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmcustomplugin.php');
							JPluginHelper::importPlugin('vmcustom');
							$dispatcher = JDispatcher::getInstance();
						foreach ($removed as $ind) {
		
							$addToCartReturnValues = $dispatcher->trigger('plgVmOnRemoveFromCart',array($cart,$ind));

							
							
							unset($cart->cartProductsData[$ind]); 
							unset($cart->products[$ind]);
							$cart->_productAdded = true; 
						}
						
						}
						
						
						
						
						
						
					}
				}
			}
			
			
		}
		
			
		
		if ($cmd == 'delete_product')
		{
			
			$cart->updateProductCart(); 
			
			
			
		}
		
	
		
		
		if ($cmd == 'removecoupon')
		{
			JRequest::setVar('coupon_code', ''); 
			JRequest::setVar('new_coupon', ''); 
			
			$_REQUEST['coupon_code'] = $_POST['coupon_code'] = $_GET['coupon_code'] = ''; 
			$this->setCoupon($cart); 
			$cart->couponCode = ''; 
			JRequest::setVar('opc_coupon_code_returned', ''); 
			JRequest::setVar('coupon_code', ''); 
			JRequest::setVar('new_coupon_code', ''); 
			
			$opc_debug = OPCconfig::get('opc_debug', false);  if (!empty($opc_debug)) { JFactory::getApplication()->enqueueMessage('OPC Debug: Coupon removed at '.__FILE__.':'.__LINE__); }
			
			$session = JFactory::getSession(); 
			$session->set('coupon', '', 'awocoupon');

			//$this->setCoupon($cart); 
			$session = JFactory::getSession(); 
			/*
		$data = $session->get('opc_fields', '', 'opc'); 
		if (!empty($data)) {
			$fields = @json_decode($data, true); 
		}
		else
		{
			$fields = array(); 
		}
		
		$txt = json_encode($fields); 
		$session = JFactory::getSession(); 
		$session->set('opc_fields', $txt, 'opc'); 
			*/
			
			$deletecouponmsg = true; 
			$cart->setCartIntoSession();
		}
		
		$cart->couponCode = trim($cart->couponCode); 
		
		
		$cp = 0; 
		
		
		$todebug = array('line'=>__LINE__,'cart-products'=>$cart->products, 'cart-cartProductsData'=>$cart->cartProductsData); 
		OPCloader::opcDebug($todebug, 'products_in_cart'); 
		/*
		if (defined('VM_VERSION') && (VM_VERSION >= 3))
		{
			unset($cart->products); 
			$cart->products = array(); 
			
			if (isset($cart->_productAdded))
			{
				$cart->_productAdded = true; 
				
			}
			if (isset($cart->_calculated)) {
				$cart->_calculated = false;
			}
			
			
			
			
			
			$ptest = JRequest::getVar('virtuemart_product_id', null); 
			if (!is_null($ptest)) {
				unset($_GET['virtuemart_product_id']); 
				unset($_POST['virtuemart_product_id']); 
				unset($_REQUEST['virtuemart_product_id']); 
				JRequest::setVar('virtuemart_product_id', null); 
			}
			
			
			ob_start(); 
			$stored_coupon = $cart->couponCode; 
			$cart->prepareCartData(); 
			$cart->couponCode = $stored_coupon; 
			$zz = ob_get_clean(); 
		}
		*/
		$cart = OPCmini::getCart(); 
		
		$todebug = array('line'=>__LINE__,'cart-products'=>$cart->products, 'cart-cartProductsData'=>$cart->cartProductsData); 
		OPCloader::opcDebug($todebug, 'products_in_cart'); 
		
		
		if (method_exists($cart, 'prepareCartProducts')) {
			ob_start(); 
			$cart->prepareCartProducts(); 
			$zz = ob_get_clean(); 
		}


		$cart->setCartIntoSession();
		
		
		
		
		
		/* opc set address was originately here... */
		
		
		
		$this->setHeaders(VirtueMartControllerOpc::$isjson); 
		
		
		// run vm main controlle due to compatibilty
		JPluginHelper::importPlugin('vmextended');
		JPluginHelper::importPlugin('vmuserfield');
		$dispatcher = JDispatcher::getInstance();
		$_controller = 'cart'; 
		$trigger = 'onVmSiteController'; 
		$trigger = 'plgVmOnMainController'; 
		$dispatcher->trigger($trigger, array($_controller));
		
		
		// this function will reload the taxes on products per country
		
		// this calls api methods as well, let's disable it for now: 
		$cart->virtuemart_shipmentmethod_id = 0; 
		if (method_exists($cart, 'prepareCartViewData')) {
			ob_start(); 
			$cart->prepareCartViewData();
			$zz = ob_get_clean(); 
		}
		
		
		
		if (!empty($virtuemart_currency_id))
		$cart->paymentCurrency = $virtuemart_currency_id; 
		
		if ($cmd == 'process_coupon')
		{
			/*
		$coupon = JRequest::getVar('coupon_code', ''); 
		
		if (!empty($coupon)) {
		$session = JFactory::getSession(); 
		$session->set('opc_last_coupon', $coupon); 
		}
		
		
		$cart->couponCode = $coupon;
		*/

			$this->setCoupon($cart); 
			
			// set coupon 
			
		}
		
		

		
		$view->cart = $cart; 
		$view->assignRef('cart', $cart); 

		


		
		
		//if (($cmd != 'runpay') && ($cmd != 'refreshPayment'))
		
		// this influences the recalculation of the basket:
		
		
		$cmds = array('process_coupon', 'refresh-totals', 'refresh_totals', 'removecoupon', 'delete_product', 'update_product', 'checkvatopc', 'delete_product', 'update_product', 'updateattributes', 'get_shipping');
		
		
		//shipping_inside_basket
		if (((empty($cmd)) || (in_array($cmd, $cmds)) || (stripos($cmd, 'shipping')!==false))  || (!empty($shipping_inside_basket)))
		{
			
			
			
			$shipping = $OPCloader->getShipping($view, $cart, true); 
			
			


			
			$cartViewref = OPCrenderer::getInstance(); 
			$cartViewref->cart =& $cart; 
			$mkey = 'shipping_method_html'; 
			$sta = array($mkey => $shipping); 
			OPCrenderer::addModules($cartViewref, $sta); 
			$shipping = $sta[$mkey]; 
			
			if ($cmd === 'runpay')
			{
				$shipping .= '<div style="display: none;">opc_do_not_update</div>'; 
			}
			$saved_totals = OPCloader::$totals_html; 
			
			
			
		}
		else 
		{
			$shipping = 'opc_do_not_update'; 
			OPCloader::$totals_html = ''; 
		}
		
		
		
		
		$return['shipping'] = $shipping; 
		
		
		
		
		
		if (empty(OPCloader::$inform_html)) OPCloader::$inform_html = array(); 
		$return['inform_html'] = implode('', OPCloader::$inform_html); 
		
		
		if (!empty($cart->couponCode))
		{
			$db = JFactory::getDBO(); 
			$q = "select * from #__virtuemart_coupons where coupon_code = '".$db->escape($cart->couponCode)."' limit 0,1"; 
			$db->setQuery($q); 
			$res = $db->loadAssoc(); 
			
			
			if (!empty($res))
			if ($res['percent_or_total'] == 'percent')
			$cp = $res['coupon_value']; 
			
			if (empty($cp))
			if (OPCloader::tableExists('awocoupon'))
			{
				$db = JFactory::getDBO(); 
				$q = "select * from #__awocoupon where coupon_code = '".$db->escape($cart->couponCode)."' and coupon_value_type = 'percent' limit 0,1"; 
				$db->setQuery($q); 
				$res = $db->loadAssoc(); 
				
				if (!empty($res))
				if (!empty($res['coupon_value']))
				$cp = $res['coupon_value']; 
				
			}
		}
		if (!empty($cp))
		{
			$cp = (float)$cp; 
			if (round($cp) == $cp)
			{
				$cp = (int)$cp.' %'; 
			}
			else
			{
				$cp = number_format($cp, 2, '.', ' ').' %'; 
			}
		}
		
		if (!empty($cp))
		$return['couponpercent'] = $cp; 
		
		
		// get payment html
		
		$num = 0; 
		
		
		
		if ($cmd == 'runpay')
		{
			$view->cart->virtuemart_shipmentmethod_id = JRequest::getVar('shipping_rate_id', ''); 
			
		}
		$html_xx = ''; 
		$isexpress = OPCloader::isExpress($cart, $html_xx); 
		$ph2_a = $OPCloader->getPayment($view, $num, false, $isexpress); 
		$ph2 = $ph2_a['html'];
		
		
		$cartViewref = OPCrenderer::getInstance(); 
		$cartViewref->cart =& $cart; 
		$mkey = 'op_payment'; 
		$sta = array($mkey => $ph2); 
		OPCrenderer::addModules($cartViewref, $sta); 
		$ph2 = $sta[$mkey]; 
		
		
		$return['payment_extra'] = $ph2_a['extra']; 
		/*
	if (!empty($ph_a['extra']))
	{
		foreach ($ph_a['extra'] as $key=>$val)
		{
			$return['payment_extra'].$val; 
		}
	}
	*/
		
		$zero_p = OPCConfig::get('default_payment_zero_total', 0);   
		$zero_p = (int)$zero_p; 
		
		$force_zero_paymentmethod = OPCconfig::get('force_zero_paymentmethod', false); 
		if (empty($zero_p) && (!empty($force_zero_paymentmethod))) {
			$zero_p = JRequest::getInt('virtuemart_paymentmethod_id', 0); 
		}
		
		if ($cmd == 'runpay')
		$cart->virtuemart_shipmentmethod_id = null;
		if ((!empty(OPCloader::$totalIsZero)) && (empty($force_zero_paymentmethod)))
		{
			$hide_payment_if_one = true; 
			$num = 1; 
			$ph2 = '<input type="hidden" value="'.$zero_p.'" name="virtuemart_paymentmethod_id" id="virtuemart_paymentmethod_id_0" />'; 
			
		}
		else {
			$payment_inside = OPCconfig::get('payment_inside', false); 
			if (empty($payment_inside)) {
			$ph2 .= '<div style="display: none;"><input type="radio" value="'.$zero_p.'" name="virtuemart_paymentmethod_id" id="virtuemart_paymentmethod_id_0" /></div>'; 
			}
		}
		
		
		if ((!empty($hide_payment_if_one) && ($num === 1)) || ($isexpress))
		{
			$ph = '<div class="payment_inner_html" rel="force_hide_payments">'.$ph2;
		}
		else $ph = '<div class="payment_inner_html" rel="force_show_payments">'.$ph2;
		$ph .= '</div>'; 
		
		//always add empty payment:
		
		
		
		$return['payment'] = $ph;
		
		
		
		
		
		
		if (isset($saved_totals))
		$return['totals_html'] = $saved_totals; 
		else
		$return['totals_html'] = OPCloader::$totals_html; 
		
		$return['min_pov'] = OPCloader::checkPurchaseValue($cart);  
		
		if (!empty($return['totals_html']))
		{
			$session = JFactory::getSession();
			/*
	$r = $session->get('opcuniq'); 
	if (empty($r))
	{
	$rand = uniqid('', true); 
	$session->set('opcuniq', $rand);
	$session->set($rand, '0');
	}
	*/
			$rand = uniqid('', true); 
			$return['totals_html'] .= '<input type="hidden" name="opcuniq" value="'.$rand.'" />';
		}
		
		$t = $return['shipping'].' '.$return['payment']; 
		$t = str_replace('//<![CDATA[', '', $t); 
		$t = str_replace('//]]> ', '', $t); 
		$t = str_replace('<![CDATA[', '', $t); 
		$t = str_replace(']]> ', '', $t); 
		
		$t = str_replace('#paymentForm', '#adminForm', $t); 
		//$t = str_replace('jQuery(document).ready(', ' jQuery( ', $t); 
		$js = array(); 
		if (strpos($t, '<script')!==false)
		{
			$xa = basketHelper::strposall($t, '<script'); 
			foreach ($xa as $st)
			{
				// end of <script tag
				$x1 = strpos($t, '>', $st+1); 
				// end of </scrip tag
				$x2 = strpos($t, '</scrip', $st+1); 
				$js1 = substr($t, $x1+1, $x2-$x1-1); 
				$js[] = $js1; 
				
			}
		}
		
		$return['shipping'] .= JHtml::_('form.token'); 
		$return['payment'] .= JHtml::_('form.token'); 
		
		if (isset(VmPlugin::$ccount))
		if (!empty($opc_debug))
		$js[] = "\n".'op_log("Positive cache match: '.VmPlugin::$ccount.'");'; ;
		
		if (!empty($opc_debug))
		if (defined('OPCMEMSTART'))
		{
			$mem = memory_get_usage(true); 
			$memd = $mem - OPCMEMSTART; 
			$memd = (float)($memd/1024);
			$memd = number_format ($memd, 0, '.', ' '); 
			
			if (!defined('debugmem')) 
			{
				$debugmem = $mem-OPCMEMSTART; 
				$debugmem = (float)($debugmem/1024);
			}
			else
			$debugmem = (float)(debugmem/1024);
			$debugmem = number_format ($debugmem, 0, '.', ' '); 
			
			$mem = (float)($mem/1024);
			$mem = number_format ($mem, 0, '.', ' '); 
			
			$js[] = "\n".'op_log("Memory usage: '.$memd.'kb of '.$mem.'kb, debug mem: '.$debugmem.'kb ");'; ;
		}
		$return['javascript'] = $js; 
		
		$return['opcplugins'] = OPCloader::getPluginData($cart); 
		
		
		

		
		OPCloader::opcDebug($cart->BT, 'address4');
		OPCloader::opcDebug($cart->ST, 'address4'); 
		
		
		if (!empty($opc_debug))
		if (!empty(OPCloader::$debugMsg))
		{
			$return['debug_msgs'] = OPCloader::$debugMsg;
			
			
			
		}
		$session = JFactory::getSession(); 
		/*
		$session_array = $session->get('coupon', array(), 'awocoupon');
		if (!empty($session_array)) {
			$session_array = @unserialize($session_array); 
		}
		*/
		if ($cmd === 'removecoupon') {
			$return['couponcode'] = ''; 
		}
		else

		/*
		if ((!empty($session_array)) && (isset($session_array['coupon_code'])))
		{
			$return['couponcode'] = '<div class="awocoupons">'.$session_array['coupon_code'].'</div>'; 
		}
		else
			*/
		if (!empty($cart->couponCode))
		{
			$return['couponcode'] = $cart->couponCode; 
		}
		else {
			$return['couponcode'] = ''; 
		}

		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shoppergroups.php'); 
		OPCShopperGroups::setShopperGroupsController($cart); 
		$upd = array('update_product', 'delete_product', 'process_coupon', 'removecoupon', 'updateattribute', 'refreshall', 'updateattributes', 'checkvatopc', 'checkvat', 'vat_info'); 
		
		$first_run = JRequest::getVar('first_run', false); 
		
		
		if ((in_array($cmd, $upd) || (stripos($cmd, 'shipping')!==false) || (!empty($ajaxify_cart))) && (!$first_run))
		{
			if ($shipping=='opc_do_not_update') $shipping = ''; 
			
			if (!empty($ph2_a['html']))
			$payment_html = $op_payment = '<div id="payment_html">'.$ph2_a['html'].'</div>'; 
			else $payment_html = $op_payment = '<div id="payment_html">&nbsp;</div>'; 
			
			$html = $this->getCartHtml($cart, $OPCloader, $shipping, $payment_html); 
			
			if ($cmd != 'runpay')
			$return['basket'] = $html;
			
			
			
			
			
			
		}
		
		if (empty($cart->cartProductsData) && (empty($cart->products))) {
			$return['cart_empty'] = true; 
		}
		else {
			$return['cart_empty'] = false; 
		}
		
		
		if (!empty($cart->couponCode)) {
			if (empty($store_coupon))
		    OPCremoveMsgs::removeMsg(JText::_('COM_VIRTUEMART_COUPON_CODE_INVALID'));
		
		}
		
		/*start message filter*/
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'removemsgs.php'); 
		OPCremoveMsgs::makeUnique();
		OPCremoveMsgs::removeMsgs($cart); 
		
		 
		$x = JFactory::getApplication()->getMessageQueue(); 
		
		
		
		$arr = array(); 
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
		
		

		
		$remove = array(); 
		foreach ($x as $key=>$val)
		{
			
			
			foreach ($euvat_text as $kx => $eutext)
			{
				
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
				if (stripos($val['message'], JText::_('COM_VIRTUEMART_COUPON_CODE_INVALID'))!==false)
				{
					$cart->couponCode = ''; 
					$opc_debug = OPCconfig::get('opc_debug', false);  if (!empty($opc_debug)) { JFactory::getApplication()->enqueueMessage('OPC Debug: Coupon removed at '.__FILE__.':'.__LINE__); }
					$cart->setCartIntoSession();

				}
			}
			
		}
		
		
		
		if (!empty($euvatinfo)) $return['checkvat'] = $euvatinfo; 
		foreach ($x as $key=>$val)
		{
			if (!in_array($key, $remove))
			$arr[] = $val['message']; 		
		}
		
		
		
		$return['msgs'] = $arr; 
		/* end message filter */
		
		//if last one was user action, let's clear shown errors: 
		$user_actions = array('update_product', 'get_klarna_address', 'updateattributes', 'delete_product', 'removecoupon', 'process_coupon'); 
		
		$return['clear_msgs'] = false; 
		
		if (empty($return['msgs']) && (in_array($cmd, $user_actions))) {
			$return['clear_msgs'] = true; 
		}
		
		
		
		$cart->virtuemart_shipmentmethod_id = 0; 
		$cart->virtuemart_paymentmethod_id = 0; 
		$cart->setCartIntoSession();
		
		$this->printJson($return, false); 
		
		
		
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('updateAbaData', array());
		
		if ($restoreBT) $cart->BT = $opcsavedBT; 
		if ($restoreST) $cart->ST = $opcsavedST; 
		$cart->setCartIntoSession(); 
		
		$mainframe = JFactory::getApplication();
		// do not allow further processing
		
		
		$mainframe->close(); 
		

	}
	
	
	
	
	
	public function &getCartHtml(&$cart, &$OPCloader, $shipping_method_html='', $op_payment='' )
	{
		if (!defined('JPATH_OPC'))
		define('JPATH_OPC', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'); 

		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
		if (!class_exists('VirtueMartViewCart'))
		require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'virtuemart.cart.view.html.php'); 
		
		$VM_LANG = new op_languageHelper(); 
		$GLOBALS['VM_LANG'] = $VM_LANG;
		
		//$ref = new VirtueMartViewCart(); 
		//$ref->cart =& $cart; 

		require_once (JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
		$ref = OPCrenderer::getInstance(); 
		$ref->cart =& $cart; 
		
		$op_coupon = $op_coupon = $OPCloader->getCoupon($ref);
		//$html = $OPCloader->getBasket($ref, false, $op_coupon);
		OPCrenderer::registerVar('shipping_method_html', $shipping_method_html); 
		OPCrenderer::registerVar('op_payment', $op_payment); 
		$html = $OPCloader->getBasket($ref, false, $op_coupon, $shipping_method_html, $op_payment); 
		

		
		
		/*
			// disabled because the ajax sends only the inner html and the modules were already added from outside wrappers
		$cartViewref = OPCrenderer::getInstance(); 
	$cartViewref->cart =& $cart; 
	$mkey = 'op_basket'; 
	$sta = array($mkey => $html); 
	OPCrenderer::addModules($cartViewref, $sta); 
	$html = $sta[$mkey]; 
		*/
		return $html;  		
	}		
	private function dumpAddress($msg, $cart)
	{
		echo $msg."<br />\n"; 
		echo $cart->BT['virtuemart_country_id'];  echo "<br />\n";  echo 'city: ';  echo $cart->BT['city'];  echo "<br />\n"; echo 'virtuemart_state_id: ';  $cart->BT['virtuemart_state_id']; echo "<br />\n";   echo 'zip: '; $cart->BT['zip']; echo "<br />\n"; 
		if (!empty($cart->ST))
		echo 'st country: '; $cart->ST['virtuemart_country_id']; echo 'st city: ';   echo $cart->ST['city'];  echo 'st state: '; $cart->ST['virtuemart_state_id'];  echo 'st zip: '; $cart->BT['zip'];
		
		
	}
	private function runExtAfter(&$allhtml)
	{

		$allhtml = str_replace('Restricted access!', '', $allhtml); 
		$allhtml = str_replace('Order not found!', '', $allhtml); 
		$allhtml = str_replace('It may have been deleted.', '', $allhtml); 

		
		if (stripos($allhtml, 'document.vm_payment_form.submit()')!==false)
		{
			$allhtml = str_replace('href="javascript:document.vm_payment_form.submit();"', 'href="#" onclick="return opcFormSubmit();"', $allhtml); 
			$allhtml .= '
<script type="text/javascript">
function opcFormSubmit()
{
if (typeof document.vm_payment_form.submit != "undefined") 
{
document.vm_payment_form.submit(); 
}
else 
{
	if (typeof document.vm_payment_form[0] != "undefined")
	{
	document.vm_payment_form[0].submit(); 
	}
	
}
return false; 
}
</script>'; 
			
		}
	}
	
	
	/**
	* Save the user info. The saveData function dont use the userModel store function for anonymous shoppers, because it would register them.
	* We make this function private, so we can do the tests in the tasks.
	*
	* @author Max Milbers
	* @author Valérie Isaksen
	*
	* @param boolean Defaults to false, the param is for the userModel->store function, which needs it to determin how to handle the data.
	* @return String it gives back the messages.
	*
	*  stAn -> this function can ONLY be called upon opcregister or checkout
	*  
	*  THIS FUNCTION STORES CURRENT CART'S ADDRESS INTO THE DATABASE
	*
	*/
	private function saveData(&$cart=false,$register=false, $disable_duplicit=false, &$data) {

		
								
		
		
		include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
		
		$mainframe = JFactory::getApplication();
		$currentUser = JFactory::getUser();
		$user_id = (int)$currentUser->get('id'); 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 
		
		
		$msg = '';
		
		
		//store email: 
		if (isset($data['email']))
		$data['email'] = str_replace(array('\'','"',',','%','*','/','\\','?','^','`','{','}','|','~'),array(''),$data['email']);
		
		
		if (empty($user_id))
		{
			JFactory::getUser()->set('email', $data['email']); 
		}
		
		
		
		if (empty($data['shipto_address_type_name'])) $data['shipto_address_type_name'] = OPCLang::_(OPCLang::_('COM_ONEPAGE_FE_ST'));
		if (empty($data['address_type_name'])) $data['address_type_name'] = OPCLang::_(OPCLang::_('COM_ONEPAGE_FE_BT'));

		if (empty($data['address_type'])) $data['address_type'] = 'BT'; 
		$at = JRequest::getWord('addrtype');
		if (!empty($at))
		$data['address_type'] = $at; 

		
		$r = JRequest::getVar('register_account', ''); 
		if (!empty($r) || (VmConfig::get('oncheckout_only_registered', 0)))
		$register = true; 
		
		
		
		
		
		
		//if ($data['address_type'] == 'ST') $register = false; 

		$this->addModelPath( JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'models' );
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
		$userModel = OPCmini::getModel('user');
		
		
		
		//UPDATE OR REGISTER ADDRESS
		if((!empty($user_id)) || $register){
			$data['user_is_vendor'] = 0; 

			
			
			
			//It should always be stored, stAn: it will, but not here
			if((empty($user_id)) || (empty($data['ship_to_info_id']))){

				
				
				if (!empty($data['email']))
				if (empty($data['shipto_email'])) $data['shipto_email'] = $data['email']; 
				
				
				
				// check for duplicit registration feature
				if (($allow_duplicit) && (empty($disable_duplicit)))
				{
					
					// set the username if appropriate
					if (empty($data['username']))
					{
						if (!empty($currentUser->id))
						{
							$data['username'] = $username = $currentUser->username; 
							JRequest::setVar('username', $username); 
							$data['email'] = $email = $this->getEmail(); 
							JRequest::setVar('email', $email); 
							
						}
						else
						{
							$username = $data['email']; 
							$email = $data['email']; 
						}
						
						
					}
					else 
					{
						$username = $data['username'];
						if (!empty($data['email'])) $email = $data['email']; 
						else 
						{
							// support for 3rd party exts
							if (strpos($username, '@')!==false)
							$email = $username; 
						}
					}
					$db = JFactory::getDBO(); 
					
					
					
					$q = "select * from #__users where email LIKE '".$db->escape($email)."' limit 0,1"; //or username = '".$db->escape($username)."' ";

					$db->setQuery($q); 
					$res = $db->loadAssoc(); 
					$is_dup = false; 
					
					
					if (!empty($res))
					{
						
						$opc_debug = OPCconfig::get('opc_debug', false);  if (!empty($opc_debug)) {
							unset($res['password']); 
							$msg = 'OPC Debug: This email already exists - ST and BT addresses will not be stored in #__virtuemart_userinfos because user is not logged in. Found user: '.implode(',', $res).' '.__FILE__.':'.__LINE__;
							JFactory::getApplication()->enqueueMessage($msg); 
							
							OPCloader::opcDebug($msg, 'address'); 
						}
						
						
						//ok, the customer already used the same email address
						$is_dup = true; 
						$duid = (int)$res['id']; 
						$GLOBALS['is_dup'] = $duid; 
						
						$GLOBALS['opc_new_user'] = $duid; 
						
						
						//store BT state: 
						
						
						if (empty($data['address_type']))
						$data['address_type'] = 'BT';
					
						$data['virtuemart_user_id'] = $duid; 
						$data['shipto_virtuemart_user_id'] = $duid; 
						
						$this->saveToCart($data, $cart);
						
						
						
						
						// we will not save the user into the jos_virtuermart_userinfos
						if (!empty($user_id))
						{
							// ok, we have a joomla registration + logged in users
							// but the user might not be registered with virtuemart
							if ($user_id === $duid)
							{
								
								// yes we are talking about the same user
								// let's associate his data in the cart with his data in VM tables
								$q = "select * from #__virtuemart_userinfos where virtuemart_user_id = ".(int)$duid." "; 
								
								$db->setQuery($q); 
								$res = $db->loadAssocList(); 
								if (empty($res))
								{
									
									// ok, he has no BT address assigned
									
								}
								else
								{
									// he is already logged in and all we have to do is to store his data in the order details, not the userinfos
									if (empty($cart->selected_shipto)) {
										$q = "select * from #__virtuemart_userinfos where virtuemart_user_id = ".(int)$duid." and address_type = 'ST' "; 
										$db->setQuery($q); 
										$test_address = $db->loadAssoc(); 
										if ($test_address['zip'] == $data['shipto_zip']) {
											$cart->selected_shipto = (int)$test_address['virtuemart_userinfo_id']; 
											
											JRequest::setVar('shipto', (int)$test_address['virtuemart_userinfo_id']); 
											
										}
									}
									

									return true;
								}
							}
						}
						else
						{
							if (empty($cart->selected_shipto)) {
								$q = "select * from #__virtuemart_userinfos where virtuemart_user_id = ".(int)$duid." and address_type = 'ST' "; 
								$db->setQuery($q); 
								$test_address = $db->loadAssoc(); 
								if ($test_address['zip'] === $data['shipto_zip']) {
									$cart->selected_shipto = (int)$test_address['virtuemart_userinfo_id']; 
									
									JRequest::setVar('shipto', (int)$test_address['virtuemart_userinfo_id']); 
								}
							}
							return true; 
						}
						// ok, we've got a duplict registration here
						if (empty($currentUser->id))
						if (!empty($data['password']) && (!empty($data['username'])))
						{
							
							
							// if we showed the password fields, let try to log him in 
							
							// we can try to log him in if he entered password
							$credentials = array('username'  => $username,
							'password' => $data['password']);
							
							// added by stAn, so we don't ge an error
							$ret = false; 
							if (empty($op_never_log_in))
							{
								
								$options = array('silent' => true );
								$mainframe = JFactory::getApplication(); 
								ob_start();
								
								$ret = $mainframe->login( $credentials, $options );

								
								
							}
							// refresh user data: 
							// refresh user data: 
							// refresh user data: 
							/*
				$session = JFactory::getSession(); 
				$user = JFactory::getUser(); 
				$id = (int)$user->id; 
				$user = new JUser($id); 
				$session->clear('user');
				$user = new JUser($id); 	
				*/
							// end of refresh			
							if (empty($op_never_log_in)) {
								$xxy = ob_get_clean();
							}
							unset($xxy); 
							if ($ret === false)
							{
								// the login was not sucessfull
								
							}
							else
							{
								
								// login was sucessfull
								$dontproceed = true; 
							}

						}
						// did he check: shipping address is different?
						/*
				if (method_exists($cart, 'prepareAddressDataInCart'))
				$cart->prepareAddressDataInCart('BT', 1);
				
				if (method_exists($cart, 'prepareAddressFieldsInCart'))
				$cart->prepareAddressFieldsInCart();
				*/
						require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php');  
						OPCUserFields::populateCart($cart, 'BT', true);
						
						
						if(!class_exists('VirtuemartModelUserfields')) require(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'userfields.php');
						
						$corefields = array( 'name','username', 'email', 'password', 'password2' , 'agreed','language', 'tos');
						

						$fieldtype = 'BTaddress';
						$userFields = $cart->$fieldtype;
						/*
				if (method_exists($cart, 'prepareAddressDataInCart'))
				$cart->prepareAddressDataInCart('ST', 1);
				if (method_exists($cart, 'prepareAddressFieldsInCart'))
				$cart->prepareAddressFieldsInCart();
				*/
						
						OPCUserFields::populateCart($cart, 'ST', true);
						
						
						$fieldtype = 'STaddress';
						$userFieldsst = $cart->STaddress;
						
						if ((!empty($data['sa'])) && ($data['sa'] == 'adresaina'))
						{
							// yes, his data are in the shipto_ fields
							$address = array(); 
							foreach ($data as $ksa=>$vsa)
							{
								if (strpos($ksa, 'shipto_')===0)
								$address[$ksa] = $vsa; 
							}
						}
						else
						{
							// load the proper BT address
							$q = "select * from #__virtuemart_userinfos where virtuemart_user_id = '".$duid."' and address_type = 'BT' limit 0,1"; 
							$db->setQuery($q); 
							$bta = $db->loadAssoc(); 
							if (!empty($bta))
							{
								$address = array(); 
								// no, his data are in the BT address and therefore we need to copy them and set a proper BT address
								foreach ($userFieldsst['fields'] as $key=>$uf)   
								{
									$uf['name'] = str_replace('shipto_', '', $uf['name']); 
									// POST['variable'] is prefered form userinfos.variable in db
									if (empty($bta[$uf['name']])) $bta[$uf['name']] = ''; 
									{
										if (!isset($data[$uf['name']])) $data[$uf['name']] = ''; 
										if (empty($data['address_type_name'])) $data['address_type_name'] = OPCLang::_(OPCLang::_('COM_ONEPAGE_FE_BT'));
										if (empty($data['shipto_address_type_name'])) $data['shipto_address_type_name'] = OPCLang::_(OPCLang::_('COM_ONEPAGE_FE_ST'));
										if (empty($data['name'])) $data['name'] = $bta[$uf['name']];
										JRequest::setVar('shipto_'.$uf['name'], $data[$uf['name']], 'post'); 
										// this will set the new BT address in the cart later on and in the order details as well
										if (!empty($bta[$uf['name']]))
										JRequest::setVar($uf['name'], $bta[$uf['name']], 'post'); 
										$address['shipto_'.$uf['name']] = $data[$uf['name']]; 
										
										if (($key === 'virtuemart_state_id') || ($uf['name'] == 'virtuemart_state_id'))
										{
											$state = $address['shipto_'.$uf['name']]; 
											
											if (!empty($data['shipto_virtuemart_country_id']))
											$country = $data['shipto_virtuemart_country_id']; 
											else
											if (!empty($data['virtuemart_country_id']))
											$country = $data['virtuemart_country_id']; 
											
											$ok1 = OPCUserFields::checkCountryState($country, $state); 
											if (!$ok1)
											{
												$address['shipto_virtuemart_state_id'] = 0; 
											}
											
											
											
											
										}
										
									}
									
								}
							}
						}
						
						
						
						// ok, we've got the ST addres here, let's check if there is anything similar
						$q = "select * from #__virtuemart_userinfos where virtuemart_user_id = '".$duid."'"; 
						$db->setQuery($q); 
						$res = $db->loadAssocList(); 
						$ign = array('virtuemart_userinfo_id', 'virtuemart_user_id', 'address_type', 'address_type_name', 'name', 'agreed', 'tos', '', 'created_on', 'created_by', 'modified_on', 'modified_by', 'locked_on', 'locked_by');  
						if (function_exists('mb_strtolower'))
						$cf = 'mb_strtolower'; 
						else $cf = 'strtolower'; 
						$e = $db->getErrorMsg(); 
						
						if (!empty($res))
						{
							// user is already registered, but we need to fill some of the system fields
							foreach ($res as $k=>$ad)
							{
								$match = false; 
								foreach ($ad as $nn=>$val)
								{
									if (!in_array($nn, $ign))
									{
										
										
										if (!isset($address['shipto_'.$nn])) $address['shipto_'.$nn] = ''; 
										if ($cf($val) != $cf($address['shipto_'.$nn])) { $match = false; break; }
										else { $match = true; 
											$lastuid = $ad['virtuemart_userinfo_id']; 
											$lasttype = $ad['address_type']; 
										}
									}
								}
								if (!empty($match))
								{
									// we've got a ST address already registered
									if ($lasttype == 'BT')
									{
										// let's set STsameAsBT
										JRequest::setVar('sa', null); 
										
										// we don't have to do anything as the same data will be saved
										
										
									}
									else
									{
										
										JRequest::setVar('shipto_virtuemart_userinfo_id', $lastuid);
										$new_shipto_virtuemart_userinfo_id = $lastuid;
										
									}
									break; 
								}
								
								
							}
							
							// the user is registered and logged in, but he wants to checkout with a new address. he might still be in the guest mode
							
							if (empty($match) || (!empty($new_shipto_virtuemart_userinfo_id)))
							{
								
								// we need to store it as a new ST address
								$address['address_type'] = 'ST'; 
								$address['virtuemart_user_id'] = $duid; 
								$address['shipto_virtuemart_user_id'] = $duid; 
								if (empty($new_shipto_virtuemart_userinfo_id))
								{
									$address['shipto_virtuemart_userinfo_id'] = 0; 
									$address['shipto_virtuemart_userinfo_id'] = $this->OPCstoreAddress($cart, $address, $duid); 
									
									
									// let's set ST address here
								}
								else {
									
									$address['shipto_virtuemart_userinfo_id'] = $new_shipto_virtuemart_userinfo_id;
									
									$cart->selected_shipto = (int)$new_shipto_virtuemart_userinfo_id; 
									
									JRequest::setVar('shipto', (int)$new_shipto_virtuemart_userinfo_id); 
									
								}

								
								if (!isset($address['agreed']))
								{
									$address['agreed'] = JRequest::getBool('agreed', 1); 
								}
								
								if (!isset($address['tos']))
								{
									$address['tos'] = JRequest::getBool('tos', 1); 
								}

								
								// empty radios fix start
								//Notice: Undefined index:  name in /srv/www/clients/client1/web90/web/svn/2072/virtuemart/components/com_virtuemart/helpers/cart.php on line 1030
								//Notice: Undefined index:  agreed in /srv/www/clients/client1/web90/web/svn/2072/virtuemart/components/com_virtuemart/helpers/cart.php on line 1030
								//Notice: Undefined index:  myradio in /srv/www/clients/client1/web90/web/svn/2072/virtuemart/components/com_virtuemart/helpers/cart.php on line 1030
								//Notice: Undefined index:  testcheckbox in /srv/www/clients/client1/web90/web/svn/2072/virtuemart/components/com_virtuemart/helpers/cart.php on line 1030
								
								
								require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
								$userFieldsModel = OPCmini::getModel('userfields');
								$prefix = '';

								$prepareUserFieldsBT = $userFieldsModel->getUserFieldsFor('cart','BT');
								$prepareUserFieldsBT = $userFieldsModel->getUserFieldsFor('cart','ST');
								
								if (!empty($prepareUserFieldsBT))
								foreach ($prepareUserFieldsBT as $fldb) {
									$name = $fldb->name;
									
									
									if (!isset($btdata[$name]))
									{
										$btdata[$name] = '';
									}

								}
								if (!empty($prepareUserFieldsST))
								foreach ($prepareUserFieldsST as $flda)
								{
									$name = $flda->name;
									// we need to add empty values for checkboxes and radios
									if (!isset($address['shipto_'.$name]))
									{
										$address['shipto_'.$name] = '';
									}
								}
								// empty radios fix end
								

								
								
								if (defined('VM_VERSION') && (VM_VERSION >= 3))
								$cart->saveAddressInCart($address, 'ST', true, 'shipto_');
								else
								$cart->saveAddressInCart($address, 'ST', true);
								
								//$cart->saveAddressInCart($address, 'ST');
								
								
								$btdata = JRequest::get('post'); 
								$btdata['virtuemart_user_id'] = $duid;
								$btdata['address_type'] = 'BT'; 
								
								if (!isset($btdata['agreed']))
								{
									$btdata['agreed'] = JRequest::getBool('agreed', 1); 
								}
								
								
								if (!isset($btdata['tos']))
								{
									$btdata['tos'] = JRequest::getBool('tos', 1); 
								}
								
								$cart->saveAddressInCart($btdata, 'BT');
								
								return;
							}

							
						}
						
						

						
						
					}
					
					
				}
				
				
				
				
				
				if (empty($dontproceed))
				{
					
					if (empty($currentUser->id))
					{
						
						if (empty($data['username']))
						{
							$data['username'] = $data['email']; 
						}
						if (empty($data['password']) && (!VmConfig::get('oncheckout_show_register', 0)))
						{
							
							$data['password'] = $data['password2'] = uniqid(); 			
						}
					}
					
					if (!empty($data['first_name']))
					$data['name'] = $data['first_name'].' '.$data['last_name']; 
					else
					if (!empty($data['last_name']))
					$data['name'] = $data['last_name']; 
					else $data['name'] = '   '; 


					if (!empty($data['shipto_first_name']) && (!empty($data['shipto_last_name'])))
					$data['shipto_name'] = $data['shipto_first_name'].' '.$data['shipto_last_name']; 
					else
					if (!empty($data['shipto_last_name']))
					$data['shipto_name'] = $data['shipto_last_name']; 
					else $data['shipto_name'] = '   '; 
					
					
					if (empty($_POST['name']))
					{
						$_POST['name'] = $data['name']; 
					}
					// Bind the post data to the JUser object and the VM tables, then saves it, also sends the registration email
					if (empty($unlog_all_shoppers))
					if (empty($currentUser->id))
					$data['guest'] = 0; 
					
					
					
					$usersConfig = JComponentHelper::getParams( 'com_users' );
					
					// OPC can still register, but will unlog the shopper immidiately when no login is enabled
					if ($usersConfig->get('allowUserRegistration') != '0')
					{

						$ret = $this->userStore($data, $userModel, $cart); 
						$err = ''; 
						if (!empty($ret['user']))
						{
							if (method_exists($ret['user'], 'getError'))
							{
								$err = $ret['user']->getError();   
								if (!empty($err))
								{
									$ret['error'] = $err; 
									JFactory::getApplication()->enqueueMessage($err, 'error'); 
								}
							}
						}
						
						
						
						
						
						
					}
					else
					{
						
						$ret['success'] = true; 
						$user = JFactory::getUser();
						$unlog_all_shoppers = true; 
					}
					$data['address_type'] = 'ST'; 
					
					
					
					
					// this gives error on shipping address save
					// this section is used purely for unlogged customers
					if ((!empty($data['sa'])) && ($data['sa'] == 'adresaina'))
					{
						
						$ru = $this->userStoreAddress($userModel, $data, $cart); 
						if (empty($ru)) self::$ok_arr['3919'] = false; 
						else self::$ok_arr['3919'] = true; 
					}
					
					
					
					$user = $ret['user']; 
					$ok = $ret['success']; 
					
					$user = JFactory::getUser(); 
					// we will not send this again
					if (empty($unlog_all_shoppers))
					if($user->id==0){
						$msg = (is_array($ret)) ? $ret['message'] : $ret;
						$usersConfig = JComponentHelper::getParams( 'com_users' );
						$useractivation = $usersConfig->get( 'useractivation' );
						
						
						
						if (empty($op_never_log_in))
						if (is_array($ret) && $ret['success'] && ((empty($useractivation)) || (!empty($opc_no_activation)))) 
						{
							
							// Username and password must be passed in an array
							$credentials = array('username' => $ret['user']->username,
							'password' => $ret['user']->password_clear
							);
							$options = array('silent' => true );
							
							$return = $mainframe->login($credentials, $options);
							
							
							
							
						}
					}
				}
				
			}
			else
			{
				
				// the user is logged in and we want to update his address
				$data['address_type'] = 'ST'; 
				// this gives error on shipping address save
				
				$new_shipto = JRequest::getVar('opc_st_changed_new', false); 
				
				$ship_to_info_id = JRequest::getVar('ship_to_info_id', 0);
				$changed = JRequest::getVar('opc_st_changed_'.$ship_to_info_id, false); 
				
				if (((!empty($data['sa'])) && ($data['sa'] == 'adresaina')) || ($new_shipto))
				{
					
					
					
					$data['address_type'] = 'ST'; 
					//$data['shipto_virtuemart_userinfo_id'] = null; 
					if ((empty($data['email'])) && (!empty($currentUser->email)))
					{
						$data['email'] = $currentUser->email;
						$data['shipto_email'] = $currentUser->email;
					}
					if (($data['shipto_address_type_name'] === OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING_LBL')) || ($data['shipto_address_type_name'] === OPCLang::_(OPCLang::_('COM_ONEPAGE_FE_ST'))))
					{
						$type_name = array(); 
						
						if (!empty($data['shipto_first_name']))
						$type_name[] = $data['shipto_first_name']; 
						
						if (!empty($data['shipto_middle_name']))
						$type_name[] = $data['shipto_middle_name']; 
						
						if (!empty($data['shipto_last_name']))
						$type_name[] = $data['shipto_last_name']; 
						
						if (!empty($data['shipto_address_1']))
						$type_name[] = $data['shipto_address_1']; 
						
						if (!empty($data['shipto_city']))
						$type_name[] = $data['shipto_city']; 
						
						if (!empty($type_name))
						{
							$data['shipto_address_type_name'] = ''; 
							$tn = implode(', ', $type_name); 
							$data['shipto_address_type_name'] .= $tn; 
						}
						else
						{
							//$data['shipto_address_type_name'] = ''; 
						}
						
						
					}
					
					if (empty($data['name']))
					{
						$data['name'] = ''; 
						if (!empty($data['first_name'])) {
						 $data['name'] .= $data['first_name'];
						}
					
						if (!empty($data['middle_name'])) {
							$data['name'] .= $data['middle_name'];
						}
					
						if (!empty($data['last_name'])) {
						$data['name'] .= $data['last_name']; 
						}
					}
					if (empty($data['user_id']))
					{
						$data['user_id'] = $currentUser->id; 
						$data['virtuemart_user_id'] = $currentUser->id; 
					}
					if (empty($data['username']) && (!empty($currentUser->username)))
					$data['username'] = $currentUser->username; 
					
					// to create a new one: 
					if ($new_shipto) {
						$data['shipto_virtuemart_userinfo_id'] = 0; 
					}
					
					if ($changed) {
						$ru = $this->OPCstoreAddress($cart, $data);
					}
					
					if (empty($ru)) self::$ok_arr['4000'] = false; 
					else self::$ok_arr['4000'] = true; 
					
					
				}
				
				$bt = JRequest::getVar('ship_to_info_id_bt', ''); 
				
				if (!empty($bt))
				{
					$changed = JRequest::getVar('opc_st_changed_'.$bt, ''); 
					
					
					
					//320: always update BT !: if (!empty($changed))
					if (!empty($user_id))
					{
						$data['address_type'] = 'BT'; 
						$data['shipto_virtuemart_userinfo_id'] = null;
						$data['virtuemart_userinfo_id'] = (int)$bt; 
						if ((empty($data['email'])) && (!empty($currentUser->email))) {
						  $data['email'] = $currentUser->email;
						}
						
						if (empty($data['name']))
						{
							if (!empty($currentUser->name)) {
								$data['name'] = $currentUser->name;
							}
							else {
							$data['name'] = ''; 
							if (!empty($data['first_name'])) {
							 $data['name'] .= $data['first_name'];
							}
							
							if (!empty($data['middle_name'])) {
							 $data['name'] .= $data['middle_name'];
							}
						
							if (!empty($data['last_name'])) {
							 $data['name'] .= $data['last_name']; 
							}
							}
						}
						if (empty($data['user_id']))
						{
							$data['user_id'] = $currentUser->id; 
							$data['virtuemart_user_id'] = $currentUser->id; 
						}
						if (empty($data['username']) && (!empty($currentUser->username)))
						$data['username'] = $currentUser->username; 
						
						$ru = $this->userStoreAddress($userModel, $data, $cart); 
						if (empty($ru)) self::$ok_arr['4034'] = false; 
						else self::$ok_arr['4034'] = true; 
						
						
						
						//$userModel->storeAddress($data);
						
						
					}
				}
				
				
			}

		}
		
		
		$data['address_type'] = 'BT'; 
		
		$this->saveToCart($data, $cart);
		
		
		


		if (isset($ok))
		self::$ok_arr['4060'] = $ok; 
		
		
		return $msg;
	}
	private function login(&$data)
	{
		include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 	
		
	}
	
	private function getModifiedData(&$data, $data_orig=null)
	{
		include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 	
		if (!empty($data_orig))
		{		
			$data = $data_orig; 
			return;
		}
		if (empty($data['name']) && (!empty($data['fist_name'])) && (!empty($data['last_name'])))
		{
			$data['name'] = $data['fist_name'].' '.$data['last_name'];
		}
		if ($data['address_type'] == 'BT')
		{
			$orig = @$data['shipto_address_type_name']; 
			unset($data['shipto_address_type_name']); 
		}
		
		
		// we have registration fields only
		if (empty($bt_fields_from))
		if ($data['address_type'] == 'BT')
		{
			$onlyf = array(); 
			
			
			$q = 'select * from #__virtuemart_userfields where `published` = 1 and `required` = 1 and `registration` = 0'; 
			$db = JFactory::getDBO(); 
			$db->setQuery($q); 
			$onlyf2 = $db->loadAssocList(); 
			
			
			
			foreach ($onlyf2 as $k=>$v)
			{
				$key = $v['name']; 
				//if (empty($data[$key])) $data[$key] = '_'; 
			}
			
		}
		
		if (empty($data_orig))
		if (!empty($custom_rendering_fields))
		{
			if ($opc_cr_type != 'save_all')
			{
				foreach ($custom_rendering_fields as $fname)
				{
					if ($fname == 'name') continue; 
					if (isset($data[$fname])) $data[$fname] = ''; 
					if (isset($data['shipto_'.$fname])) $data[$fname] = ''; 
					
				}
			}
			else return;
		}
		else return; 
		
	}
	
	public function userStoreAddress(&$userModel, &$data, &$cart)
	{
		
		$data_orig = $data; 
		$this->getModifiedData($data); 
		
		
		
		if (!isset($data['virtuemart_userinfo_id']))
		if (!empty($data['bt_virtuemart_userinfo_id']))
		{
			$data['virtuemart_userinfo_id'] = (int)$data['bt_virtuemart_userinfo_id']; 
			
		}
		
		if (!isset($data['address_type']))
		{
			
		}

		
		
		if (empty($data['virtuemart_user_id']))
		{
			if (!empty($GLOBALS['opc_new_user']))
			{
				$data['virtuemart_user_id'] = $GLOBALS['opc_new_user']; 
				$data['shipto_virtuemart_user_id'] = $GLOBALS['opc_new_user']; 
			}
			else
			{
				if (!empty($GLOBALS['is_dup']))
				{
					$data['virtuemart_user_id'] = $GLOBALS['is_dup']; 
					$data['shipto_virtuemart_user_id'] = $GLOBALS['is_dup']; 
				} 
			}
		}
		
		
		if ($data['address_type'] == 'ST')
		$ret =	$this->OPCstoreAddress($cart, $data, $data['virtuemart_user_id']);
		else
		$ret =	$userModel->storeAddress($data);
		
		$user_id = JFactory::getUser()->get('id'); 
		if (!empty($data['virtuemart_userinfo_id'])) {
		$ui = (int)$data['virtuemart_userinfo_id'];
		if (!empty($ui))
		if ((!empty($user_id)) && ($data['address_type'] == 'BT')) {
			$db = JFactory::getDBO(); 
			$q = 'select `virtuemart_userinfo_id` from `#__virtuemart_userinfos`  where `virtuemart_user_id` = '.(int)$user_id. ' and `address_type` = "BT" and `virtuemart_userinfo_id` = '.(int)$ui; 
			$db->setQuery($q); 
			$isOk = $db->loadResult(); 
			
			if (!empty($isOk)) {
				// check for multiple BT addresses and change them to ST: 
				$q = 'select `virtuemart_userinfo_id` from `#__virtuemart_userinfos`  where `virtuemart_user_id` = '.(int)$user_id. ' and `address_type` = "BT" and `virtuemart_userinfo_id` <> '.(int)$ui; 
				$db->setQuery($q); 
				$res = $db->loadAssocList(); 
				
				
				if (!empty($res)) {
					$q = 'update `#__virtuemart_userinfos`  set `address_type` = "ST" where `virtuemart_user_id` = '.(int)$user_id. ' and `address_type` = "BT" and `virtuemart_userinfo_id` <> '.(int)$ui; 				
					$db->setQuery($q); 
					$db->query(); 
					
				}
				
			}
		}
		}
		
		// will set internal data to current user: 
		
		$this->getModifiedData($data, $data_orig); 
		return $ret; 
	}		
	private function userStore(&$data, &$userModel, &$cart)
	{

		$usersConfig = JComponentHelper::getParams( 'com_users' );
		// OPC can still register, but will unlog the shopper immidiately when no login is enabled
	    $canrun = $usersConfig->get('allowUserRegistration'); 
		$canrun = (int)$canrun; 
		$user_id = JFactory::getUser()->get('id'); 
		if (empty($user_id)) {
		 if ($canrun === 0) return; 
		}
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'user.php'); 
		
		include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php');
		
		$data_orig = $data; 
		$this->getModifiedData($data); 
		
		
		if (isset($data['shipto_address_type_name']))
		{
			$stored = $data['shipto_address_type_name']; 
			unset($data['shipto_address_type_name']); 
		}
		
		
		// before we store the user, let's check opc config for duplicit email or username: 
		$user_id = JFactory::getUser()->get('id'); 
		if (empty($user_id))
		{
			if (!empty($opc_no_duplicit_email))
			{
				$db = JFactory::getDBO(); 
				$email = $data['email']; 
				if (!empty($op_usernameisemail))
				$q = "select * from `#__users` where `username` = '".$db->escape($email)."' or `email` = '".$db->escape($email)."' limit 1"; 
				else
				$q = "select * from `#__users` where `email` = '".$db->escape($email)."' limit 1"; 
				$db->setQuery($q); 
				$result = $db->loadResult(); 
				if (!empty($result))
				{
					$u = OPCLang::_('COM_VIRTUEMART_USER_FORM_EMAIL'); 
					$msg = OPCLang::sprintf('COM_ONEPAGE_EMAIL_ALREADY_EXISTS', $u);
					return $this->returnTerminate($msg); 
				}
			}
			
			
			
			if (!empty($opc_no_duplicit_username))
			{
				$db = JFactory::getDBO(); 
				$email = $data['username']; 
				
				if (empty($data['username'])) 
				$data['username'] = $data['email']; 
				
				$q = "select * from `#__users` where `username` = '".$db->escape($email)."' limit 1"; 
				$db->setQuery($q); 
				$result = $db->loadResult(); 
				if (!empty($result))
				{
					if (!empty($op_usernameisemail))
					$u = OPCLang::_('COM_VIRTUEMART_USER_FORM_EMAIL'); 
					else
					$u = OPCLang::_('COM_VIRTUEMART_REGISTER_UNAME'); 
					
					$msg = OPCLang::sprintf('COM_ONEPAGE_EMAIL_ALREADY_EXISTS', $u);
					return $this->returnTerminate($msg); 
				}
			}
		}
		
		if (!class_exists('VirtueMartCart'))
		require(JPATH_VM_SITE .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'cart.php');
		
		if (empty($cart))
		$cart = VirtuemartCart::getCart(); 
		
		OPCShopperGroups::getSetShopperGroup(); 
		
		$ret = OPCUser::storeVM25($data, false, $userModel, $opc_no_activation, $this,$cart); 	
		
		if (isset($stored))
		$data['shipto_address_type_name'] = $stored; 
		
		
		
		$this->storeShopperGroup($data, true, $data['virtuemart_user_id']); 
		$this->getModifiedData($data, $data_orig); 
		return $ret; 
	}	

	private function storeShopperGroup(&$data, $update=false, $user_id=null)
	{
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
		$shoppergroupmodel = OPCmini::getModel('ShopperGroup');
		$userModel = OPCmini::getModel('user');
		
		$default = $shoppergroupmodel->getDefault(0); 
		if (!empty($default))
		$default_id = $default->virtuemart_shoppergroup_id; 
		else
		$default_id = 1; 
		
		$default2 = $shoppergroupmodel->getDefault(1); 
		if (!empty($default2))
		$default2_id = $default2->virtuemart_shoppergroup_id; 
		else
		$default2_id = 2; 
		
		
		$user = JFactory::getUser();
		
		if (empty($user_id))
		$user_id = $user->get('id'); 
		
		$db = JFactory::getDBO(); 
		
		
		if (empty($user_id)) 
		{
			
			return;
		}
		
		
		
		
		if (empty($data['virtuemart_shoppergroup_id']) ||  ($data['virtuemart_shoppergroup_id']==$default)){
			
			return; 
		}
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
		$usermodel = OPCmini::getModel ('user');
		//$user = $usermodel->getUser ();
		//$user->shopper_groups = (array)$user->shopper_groups;
		
		// Bind the form fields to the table

		//$data['virtuemart_shoppergroup_id'] = (int)$data['virtuemart_shoppergroup_id']; 
		//if(!empty($data['virtuemart_shoppergroup_id']))
		if (is_array($data['virtuemart_shoppergroup_id']))
		{
			foreach ($data['virtuemart_shoppergroup_id'] as $k=>$v)
			{
				$data['virtuemart_shoppergroup_id'][$k] = (int)$v; 
			}
		}
		else
		{
			$data['virtuemart_shoppergroup_id'] = array((int)$data['virtuemart_shoppergroup_id']); 
		}
		
		
		if (empty(self::$shopper_groups)) self::$shopper_groups = array(); 
		if (method_exists($userModel, 'getCurrentUser'))
		{
			$user = $userModel->getCurrentUser();
			self::$shopper_groups = $user->shopper_groups; 
		}
		
		if (empty(self::$shopper_groups)) self::$shopper_groups = array(); 
		if (!in_array($data['virtuemart_shoppergroup_id'], self::$shopper_groups))
		{
			foreach ($data['virtuemart_shoppergroup_id'] as $k=>$v)
			{
				self::$shopper_groups[] = $v; 
			}
		}
		
		
		if (!empty($update)) {
			$q = 'select * from #__virtuemart_vmuser_shoppergroups where virtuemart_user_id = '.(int)$user_id.' limit 0,1'; 
			$db->setQuery($q); 
			$res = $db->loadAssoc(); 
			
			// the shopper group was already set
			if (!empty($res)) 
			{
				return;
			}
		}
		
		foreach (self::$shopper_groups as $key=>$group)
		{
			if (empty($group)) continue; 
			// anonymous
			if ($group == $default_id) continue; 
			// default
			if ($group == $default2_id) continue; 
			if (empty($group)) continue; 
			//$user = $userModel->getUser(); 
			$group = (int)$group; 
			
			
			
			
			$q = 'select * from #__virtuemart_vmuser_shoppergroups where virtuemart_user_id = '.(int)$user_id.' and virtuemart_shoppergroup_id = '.$group.' limit 0,1'; 
			$db->setQuery($q); 
			$res = $db->loadAssoc(); 
			
			// the shopper group was already set
			if (!empty($res)) 
			{
				continue; 
			}
			
			
			
			$q = "insert into `#__virtuemart_vmuser_shoppergroups` (id, virtuemart_user_id, virtuemart_shoppergroup_id) values (NULL, ".(int)$user_id.", ".(int)$group.")"; 
			$db->setQuery($q); 
			$db->query(); 
			$e = $db->getError(); 
			
			
			
			/*
				$shoppergroupData = array('virtuemart_user_id'=>$user_id,'virtuemart_shoppergroup_id'=>$group);
				$user_shoppergroups_table = $userModel->getTable('vmuser_shoppergroups');
				$shoppergroupData = $user_shoppergroups_table -> bindChecknStore($shoppergroupData);
				$errors = $user_shoppergroups_table->getErrors();
				
				
				foreach($errors as $error){
					$this->setError($error);
					vmError('Set shoppergroup '.$error);
					
					$noError = false;
				}
				*/
		}
		
		
	}
	
	// this is an overrided function to support duplict emails
	// the orginal function was in: user.php storeAddress($data)
	function OPCstoreAddress(&$cart, $data, $user_id=0)
	{


		
		$x = JRequest::getVar('ship_to_info_id', ''); 
		if (!empty($x) && (is_numeric($x)))
		{
			$data['shipto_virtuemart_userinfo_id'] = (int)$x; 
		}
		if (!empty($data['shipto_virtuemart_userinfo_id']))
		$data['shipto_virtuemart_userinfo_id'] = (int)$data['shipto_virtuemart_userinfo_id']; 
		else
		$data['shipto_virtuemart_userinfo_id'] = 0; 
		
		//$user =JFactory::getUser();
		$this->addModelPath( JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'models' );
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
		$userModel = OPCmini::getModel('user');
		$user = JFactory::getUser();
		
		if (empty($user_id)) {
		  $user_id = $user->get('id'); 
		}
		
		$userinfo   = $userModel->getTable('userinfos');
		
		
		if(empty($userinfo->virtuemart_user_id)){
				
				if (!empty($user_id))
				{
					// this is the case with activation - when the user does not get logged in, but we already know his user_id
					$data['virtuemart_user_id'] = $user_id;
				}
				else
				{
					require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
					if (!OPCmini::isSuperVendor())
					{
						$data['virtuemart_user_id'] = $user->id;
					}
					else
					{
						if(isset($data['virtuemart_user_id'])){
							$data['virtuemart_user_id'] = (int)$data['virtuemart_user_id'];
						} else {
							//Disadvantage is that admins should not change the ST address in the FE (what should never happen anyway.)
							$data['virtuemart_user_id'] = $user->id;
						}
					}
					
				}
				
				if (empty($data['virtuemart_user_id'])) return; 
				
				
			}
			else
			{
				
				
				
				if($userinfo->virtuemart_user_id!=$user->id) 
				{
					
					return;
				}
			}
		
		if($data['address_type'] == 'BT'){
			$userfielddata = VirtueMartModelUser::_prepareUserFields($data, 'BT');
			try { 
			if (!$userinfo->bindChecknStore($userfielddata)) {
				vmError('storeAddress '.$userinfo->getError());
			}
			}
			catch (Exception $e) {
				 $insert = $data; 
	    $jnow = JFactory::getDate();
		if (method_exists($jnow, 'toMySQL'))
		$now = $jnow->toMySQL();
		else $now = $jnow->toSQL(); 
	   
	   
	   
	   
	   //generic insert data: 
	   $table = '#__virtuemart_userinfos';
	   $ui = OPCmini::getColumns($table); 
	   if (empty($insert['virtuemart_userinfo_id'])) {
		   $insert['virtuemart_userinfo_id'] = 'NULL'; 
	   }
	   $insert['created_on'] = $now; 
	   $insert['modified_on'] = $now;
	   $insert['modified_by'] = (int)$data['virtuemart_user_id'];
	   $insert['created_by'] = (int)$data['virtuemart_user_id'];
	   $insert['virtuemart_user_id'] = (int)$data['virtuemart_user_id'];
			
			OPCmini::insertArray($table, $insert, $ui); 
				
			}
		}
		// Check for fields with the the 'shipto_' prefix; that means a (new) shipto address.
		$shiptonew = JRequest::getVar('opc_st_changed_new', false); 
		$shiptologged = JRequest::getVar('shipto_logged'); 
		// shipto_logged
		
		
		
		
		
		// special case when using sinel ST address: 
		$skipc = false; 
		$shipto_logged = JRequest::getVar('shipto_logged', null); 
		$shipto_logged = (int)$shipto_logged; 
		if (!empty($shipto_logged))
		{
			$q = 'select address_type from #__virtuemart_userinfos where virtuemart_userinfo_id = '.(int)$data['shipto_virtuemart_userinfo_id'].' limit 0,1'; 
			$db = JFactory::getDBO(); 
			$db->setQuery($q); 
			$res2 = $db->loadResult(); 
			if ((!empty($res2)) && ($res2 != 'bt'))
			{
				$data['shipto_virtuemart_userinfo_id'] = $shipto_logged; 
				$skipc = true; 
			}
		}
		else
		if (!$skipc)
		{
			
			$q = 'select address_type from #__virtuemart_userinfos where virtuemart_userinfo_id = '.(int)$data['shipto_virtuemart_userinfo_id'].' limit 0,1'; 
			$db = JFactory::getDBO(); 
			$db->setQuery($q); 
			$res = $db->loadResult(); 
			if (empty($res))
			{
				// non existent update to ST 
				unset($data['shipto_virtuemart_userinfo_id']); 
				$data['shipto_virtuemart_userinfo_id'] = 0; 
				
			}
			if (strtolower($res) == 'bt')
			{
				// trying to update ST with improper ID
				$data['shipto_virtuemart_userinfo_id'] = 0; 
				
			}
		}
		
		if(isset($data['shipto_virtuemart_userinfo_id'])){
			$dataST = array();
			$_pattern = '/^shipto_/';

			foreach ($data as $_k => $_v) {
				if (preg_match($_pattern, $_k)) {
					$_new = preg_replace($_pattern, '', $_k);
					$dataST[$_new] = $_v;
				}
			}

			$userinfo   = $userModel->getTable('userinfos');
			if(isset($dataST['virtuemart_userinfo_id']) and $dataST['virtuemart_userinfo_id']!=0)
			{
				
				require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
				if (!OPCmini::isSuperVendor())
				{
					$userinfo->load($dataST['virtuemart_userinfo_id']);
				}
				
				
				
			}
			
			
			
			
			
			
			
			if(empty($userinfo->virtuemart_user_id)){
				
				if (!empty($user_id))
				{
					// this is the case with activation - when the user does not get logged in, but we already know his user_id
					$dataST['virtuemart_user_id'] = $user_id;
				}
				else
				{
					require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
					if (!OPCmini::isSuperVendor())
					{
						$dataST['virtuemart_user_id'] = $user->id;
					}
					else
					{
						if(isset($data['virtuemart_user_id'])){
							$dataST['virtuemart_user_id'] = (int)$data['virtuemart_user_id'];
						} else {
							//Disadvantage is that admins should not change the ST address in the FE (what should never happen anyway.)
							$dataST['virtuemart_user_id'] = $user->id;
						}
					}
					
				}
				
				if (empty($dataST['virtuemart_user_id'])) return; 
				
				
			}
			else
			{
				
				
				
				if($userinfo->virtuemart_user_id!=$user->id) 
				{
					
					return;
				}
			}

			$dataST['address_type'] = 'ST';
			
			
			$userfielddata = VirtueMartModelUser::_prepareUserFields($dataST, 'ST');
			if (!empty($dataST['address_type_name']))
			$userinfo->address_type_name = $dataST['address_type_name'];
		try { 
			if (!$userinfo->bindChecknStore($userfielddata)) {
				vmError($userinfo->getError());
			}
		}
		catch (Exception $e) {
			//JFactory::getApplication()->enqueueMessage('Error 6458: Storing Ship To address failed.'); 
			
			 $insert = $dataST; 
	    $jnow = JFactory::getDate();
		if (method_exists($jnow, 'toMySQL'))
		$now = $jnow->toMySQL();
		else $now = $jnow->toSQL(); 
	   
	   
	   
	   
	   //generic insert data: 
	   $table = '#__virtuemart_userinfos';
	   $ui = OPCmini::getColumns($table); 
	   if (empty($insert['virtuemart_userinfo_id'])) {
		   $insert['virtuemart_userinfo_id'] = 'NULL'; 
	   }
	   $insert['created_on'] = $now; 
	   $insert['modified_on'] = $now;
	   $insert['modified_by'] = (int)$dataST['virtuemart_user_id'];
	   $insert['created_by'] = (int)$dataST['virtuemart_user_id'];
	   $insert['virtuemart_user_id'] = (int)$dataST['virtuemart_user_id'];
			
			OPCmini::insertArray($table, $insert, $ui); 
			
			
		}
			
			
			if (!empty($userinfo->virtuemart_userinfo_id))
			{
				if (empty($cart)) 
				{
					
					
				}
				$shipto = JRequest::setVar('shipto', (int)$userinfo->virtuemart_userinfo_id); 
				$cart->selected_shipto = $userinfo->virtuemart_userinfo_id; 
				JRequest::getVar('shipto', (int)$userinfo->virtuemart_userinfo_id); 
				$cart->STsameAsBT = 0; 
			}
			
			
		}

		
		return $userinfo->virtuemart_userinfo_id;
		
	}
	function sendRegistrationMail($user)
	{
		
		// Compile the notification mail values.
		$data = $user->getProperties();
		$config	= JFactory::getConfig();
		if (method_exists($config, 'get'))
		{
			$data['fromname']	= $config->get('fromname');
			$data['mailfrom']	= $config->get('mailfrom');
			$data['sitename']	= $config->get('sitename');
			
		}
		else
		{
			$data['fromname']	= $config->getValue('config.fromname');
			$data['mailfrom']	= $config->getValue('config.mailfrom');
			$data['sitename']	= $config->getValue('config.sitename');
		}
		$data['siteurl']	= JUri::base();
		$usersConfig = JComponentHelper::getParams( 'com_users' );
		$useractivation = $usersConfig->get( 'useractivation' );
		// Handle account activation/confirmation emails.
		if ($useractivation == 2)
		{
			// Set the link to confirm the user email.
			$uri = JURI::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base.JRoute::_('index.php?option=com_users&task=registration.activate&token='.$data['activation'], false, VmConfig::get('useSSL', false));

			$emailSubject	= OPCLang::sprintf(
			'COM_USERS_EMAIL_ACCOUNT_DETAILS',
			$data['name'],
			$data['sitename']
			);

			$emailBody = OPCLang::sprintf(
			'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY',
			$data['name'],
			$data['sitename'],
			$data['siteurl'].'index.php?option=com_users&task=registration.activate&token='.$data['activation'],
			$data['siteurl'],
			$data['username'],
			$data['password_clear']
			);
		}
		elseif ($useractivation == 1)
		{
			// Set the link to activate the user account.
			$uri = JURI::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base.JRoute::_('index.php?option=com_users&task=registration.activate&token='.$data['activation'], false, VmConfig::get('useSSL', false));

			$emailSubject	= OPCLang::sprintf(
			'COM_USERS_EMAIL_ACCOUNT_DETAILS',
			$data['name'],
			$data['sitename']
			);

			$emailBody = OPCLang::sprintf(
			'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
			$data['name'],
			$data['sitename'],
			$data['siteurl'].'index.php?option=com_users&task=registration.activate&token='.$data['activation'],
			$data['siteurl'],
			$data['username'],
			$data['password_clear']
			);
		} else {

			$emailSubject	= OPCLang::sprintf(
			'COM_USERS_EMAIL_ACCOUNT_DETAILS',
			$data['name'],
			$data['sitename']
			);

			$emailBody = OPCLang::sprintf(
			'COM_USERS_EMAIL_REGISTERED_BODY',
			$data['name'],
			$data['sitename'],
			$data['siteurl']
			);
		}

		// Send the registration email.
		$return = JUtility::sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);

	}
	
	private function acySub($name, $email, $cart)
	{
		
		//acymailing customization: 
		if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'regacymailing')) 
		{
			if (empty($cart)) $cart = VirtuemartCart::getCart(); 
			
			require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php');  
			OPCUserFields::transformAcyFields($cart); 
			
		}
		
		if (class_exists('plgSystemRegacymailing'))
		{

			jimport( 'joomla.plugin.helper' );
			//$plugin = JPluginHelper::getPlugin('system', 'regacymailing');

			JPluginHelper::importPlugin('system', 'regacymailing');


			$ju = JFactory::getUser(); 
			//$plugin->oldUser = $ju; 
			$user = new stdClass(); 
			$user->id = $ju->id; 

			if (empty($ju->id)) {
				$isnew = true; 
			}
			else
			{
				$isnew = false; 
			}

			$user->blocked = false; 
			$user->block = 0; 
			$user->email = $email; 
			$user->name = $name;

			JDispatcher::getInstance()->trigger('onAfterStoreUser', array($user, $isnew, true, '')); 
		}
	}
	
	function saveToCart($data, $cart=null){

		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
		$userFieldsModel = OPCmini::getModel('userfields');
		

		$prepareUserFields = $userFieldsModel->getUserFieldsFor('cart',$data['address_type']);
		
		if (!empty($prepareUserFields))
		foreach ($prepareUserFields as $fld) {
			$name = $fld->name;
			
			if ($data['address_type'] == 'BT')
			if (isset($cart) && (!empty($cart->BT[$name])))
			{
				$data[$name] = $cart->BT[$name];
			}

			if ($data['address_type'] == 'ST')
			if (isset($cart) && (!empty($cart->ST[$name])))
			{
				$data[$name] = $cart->ST[$name];
			}
			
			// we need to add empty values for checkboxes and radios
			if ($data['address_type'] == 'ST')
			if (!isset($data['shipto_'.$name]))
			{
				$data['shipto_'.$name] = '';
			}
			
			
			
			if (isset($cart) && (empty($cart->BT[$name])))
			if ($data['address_type'] == 'BT')
			if (!isset($data[$name]))
			{
				$data[$name] = '';
			}
			
			
			
			
			
			

		}
		
		
		
		
		if(!class_exists('VirtueMartCart')) require(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cart.php');
		
		if (!isset($cart))
		$cart = VirtueMartCart::getCart();
		
		if (method_exists($cart, 'prepareCartData'))
		{
			ob_start(); 
			$stored_coupon = $cart->couponCode; 
			$cart->prepareCartData(false); 
			$cart->couponCode = $stored_coupon; 
			$zz = ob_get_clean(); 
		}
		
		$type= $data['address_type']; 
		$oldcart = clone($cart); 
		
		
		if ($type === 'BT') $prefix = ''; 
		else $prefix = 'shipto_'; 
		
		
		if (defined('VM_VERSION') && (VM_VERSION >= 3))
		$cart->saveAddressInCart($data, $type, true, $prefix);
		else
		$cart->saveAddressInCart($data, $type);

		
		
		
		//$cart->saveAddressInCart($data, $type );
		
		
		
		
		
		
		
		if ((isset($oldcart->$type)) && (is_array($oldcart->$type)))
		foreach ($oldcart->$type as $key=>$val)
		{
			if (empty($cart->{$type}[$key]))
			$cart->{$type}[$key] = $val; 
			
			
		}
		
		
		
		
		
		$sa = JRequest::getVar('sa', ''); 
		if ($sa == 'adresaina')
		{
			/*
		
		stAn -> this works as well - will update ST for VM3
		
		$ndata = $data; 
		
		
		foreach ($data as $key=>$val)
		{
			
			if (substr($key, 0, 7)==='shipto_')
			{
				$nk = substr($key, 7); 
				
			
				$ndata[$nk] = $val; 
				
				
			}
		}
		
		*/
			
			
			

			
			if (defined('VM_VERSION') && (VM_VERSION >= 3))
			$cart->saveAddressInCart($data, 'ST', true, 'shipto_');
			else
			$cart->saveAddressInCart($data, 'ST');
			
			
			
			
			if (isset($oldcart->ST))
			foreach ($oldcart->ST as $key=>$val)
			{
				if (empty($cart->ST[$key]))
				$cart->ST[$key] = $val; 
			}

		}
		else $cart->STsameAsBT = 1; 
		
		// make sure we use proper state: 
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php');  
		OPCUserFields::populateCart($cart, 'BT', false);
		
		
		$cart->setCartIntoSession();
		
		
		
	}
	


	function getKlarnaAddress()
	{
		
		if (JVM_VERSION >= 2) {
			
			require_once (JPATH_ROOT .DIRECTORY_SEPARATOR. 'plugins' .DIRECTORY_SEPARATOR. 'vmpayment' .DIRECTORY_SEPARATOR. 'klarna' .DIRECTORY_SEPARATOR. 'klarna.php'); 
			require_once (JPATH_ROOT .DIRECTORY_SEPARATOR. 'plugins' .DIRECTORY_SEPARATOR. 'vmpayment' .DIRECTORY_SEPARATOR. 'klarna' .DIRECTORY_SEPARATOR. 'klarna'.DIRECTORY_SEPARATOR.'api'.DIRECTORY_SEPARATOR.'klarnaaddr.php'); 
		} else {
			
			require_once (JPATH_ROOT .DIRECTORY_SEPARATOR. 'plugins' .DIRECTORY_SEPARATOR. 'vmpayment' .DIRECTORY_SEPARATOR. 'klarna.php'); 
			require_once (JPATH_ROOT .DIRECTORY_SEPARATOR. 'plugins' .DIRECTORY_SEPARATOR. 'vmpayment' .DIRECTORY_SEPARATOR. 'klarna' .DIRECTORY_SEPARATOR.'api'.DIRECTORY_SEPARATOR.'klarnaaddr.php'); 
		}

		$klarna = new Klarna(); 
		
		$q = "select * from #__virtuemart_paymentmethods where payment_element = 'klarna' and published = '1' limit 0,1"; 
		$db = JFactory::getDBO(); 
		$db->setQuery($q); 
		$res = $db->loadAssoc(); 
		if (empty($res)) return null; 
		$id = $res['virtuemart_paymentmethod_id']; 
		jimport( 'joomla.html.parameter' );
		
		$params = explode('|', $res['payment_params']);
		$obj = new stdclass(); 
		foreach($params as $item){

			$item = explode('=',$item);
			$key = $item[0];
			unset($item[0]);
			$item = implode('=',$item);
			if(!empty($item))
			{
				$obj->$key = @json_decode($item);

			}
		}
		
		$cData = KlarnaHandler::countryData ($obj, 'SWE');
		$language = KlarnaLanguage::fromCode('SE');
		$currency = KlarnaCurrency::fromCode($cData['currency_code']);
		$klarna->config ($cData['eid'], $cData['secret'], $cData['country_code'], $language, $currency, $cData['mode']);
		/*
	$country = JRequest::getVar('virtuemart_country_id', ''); 
	
	if (!empty($country) && (is_numeric($country)))
	{
	$q = 'select * from #__virtuemart_countries where virtuemart_country_id = '.$country.' limit 0,1'; 
	$db->setQuery($q); 
	$r = $db->loadAssoc(); 
	$e = $db->getErrorMsg(); 
	
	if (empty($r)) $c = 'se';
	else
	$c = strtolower($r['country_2_code']); 
	
	}
	else 
	*/
		$c = 'se'; 
		
		$klarna->setCountry($c);  
		
		
		$klarna->setLanguage($language);
		$klarna->setCurrency($currency);
		
		
		//try 
		{  
			//Attempt to get the address(es) associated with the SSN/PNO.  
			$pn = JRequest::getVar('socialNumber', ''); 
			
			$addrs = $klarna->getAddresses($pn);
			
			
			if (empty($addrs)) return null; 
			
			$a = array(); 	
			foreach ($addrs as $key => $addr)   
			{
				
				$a = $addr->toArray(); 
				foreach ($a as $k=>$v)
				$a[$k] = utf8_encode($v);
				return $a; 
				
				//if (empty($ar)) return null; 
				if ($addr->isCompany)
				$a['company_name'] = $addr->getCompanyName();
				else $a['company_name'] = ''; 
				
				$a['first_name'] = $addr->getFirstName();
				
				$a['last_name'] = $addr->getLastName();
				$a['address_1'] = $addr->getStreet();
				$a['email'] = $addr->getEmail(); 
				$a['phone_1'] = $addr->getTelno(); 
				$a['phone_2'] = $addr->getCellno(); 
				$a['address_2'] = $addr->getHouseExt(); 
				$a['zip'] = $addr->getZipCode(); 
				$a['city'] = $addr->getCity();
				
				
				
				return $a; 
			}
			
			return null;
			/* If there exists several addresses you would want to output a list in 
	which the customer could choose the address which suits him/her. 
	*/  

			// Print them if available:  
			foreach ($addrs as $key => $addr) {  
				echo "<table>\n";  

				// This only works if the right getAddresses type is used.  
				if ($addr->isCompany) {  
					echo "\t<tr><td>Company</td><td> {$addr->getCompanyName()} </td></tr>\n";  
				} else {  
					echo "\t<tr><td>First name</td><td>{$addr->getFirstName()}</td></tr>\n";  
					echo "\t<tr><td>Last name</td><td>{$addr->getLastName()}</td></tr>\n";  
				}  

				echo "\t<tr><td>Street</td><td>{$addr->getStreet()}</td></tr>\n";  
				echo "\t<tr><td>Zip code</td><td>{$addr->getZipCode()}</td></tr>\n";  
				echo "\t<tr><td>City</td><td>{$addr->getCity()}</td></tr>\n";  
				echo "\t<tr><td>Country</td><td>{$addr->getCountryCode()}</td></tr>\n";  
				echo "</table>\n";  
			}  
		} 
		//catch(Exception $e) 
		{  
			//Something went wrong  
			
			return null;
			
			
		}  
		return null;

	}
	
	public static function addressToSt(&$cart, $address) {
		if (!empty($address))
		foreach ($address as $ka => $kv)
		{
			if ($kv === 'false') $address[$ka] = false;
			if ($kv === '0') $address[$ka] = 0;
		}
		
		if (!isset($address['address_2'])) $address['address_2'] = ''; 
		if (!isset($address['shipto_address_2'])) $address['address_2'] = ''; 
		
		foreach ($address as $kA=>$vA ){
			if (!is_array($cart->ST)) $cart->ST = array();
			if (isset($address['shipto_'.$kA])) $vA = $address['shipto_'.$kA]; 
			if (substr($kA, 0, strlen('shipto_')) === 'shipto_') 
			$kA = substr($kA, strlen('shipto_')); 
			
			$cart->ST[$kA] = $vA;
			
			
			
		}
	}

	public static function addressToBt(&$cart, $address) {
		if (!empty($address))
		foreach ($address as $ka => $kv)
		{
			if ($kv === 'false') $address[$ka] = false;
			if ($kv === '0') $address[$ka] = 0;
		}
		
		
		foreach ($address as $kA=>$vA ){
			if (!is_array($cart->ST)) $cart->ST = array();
			if (substr($kA, 0, strlen('shipto_')) === 'shipto_') continue; 
			$cart->BT[$kA] = $vA;
		}	
	}




} 