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
*  Description: This file is used only from Virtuemart 2.0.22, all of the prior versions of Virtuemart require core modification for this feature to work. 
*
*/

// load OPC loader
if (!class_exists('calculationHelper')) 
	require(JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'calculationh.php'); 

require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cache.php'); 


class calculationHelperOPC extends calculationHelper
{
	
	public static $_forhash; 
	

	static public function getInstanceOPC() {
		if (!is_object(calculationHelperOPC::$_instance)) {
			calculationHelperOPC::$_instance = new calculationHelperOPC();
		} else {


		if (get_class(calculationHelperOPC::$_instance) != 'calculationHelperOPC')
		{
		
			calculationHelperOPC::$_instance = new calculationHelperOPC();
		}
			//We store in UTC and use here of course also UTC
			//$jnow = JFactory::getDate();
			//calculationHelperOPC::$_instance->_now = $jnow->toMySQL();
		}
		
		return calculationHelperOPC::$_instance;
	}
	
	public function getCheckoutPricesOPC($cart, $checkAutomaticSelected=true) {
		
	  return $this->getCheckoutPrices($cart, $checkAutomaticSelected);
	}
	/**
	 * Calculates the effecting Shipment prices for the calculation
	 * @copyright (c) 2009 VirtueMart Team. All rights reserved.
	 * @author Max Milbers
	 * @author Valerie Isaksen
	 * @param 	$code 	The Id of the coupon
	 * @return 	$rules 	ids of the coupons
	 */
	function calculateShipmentPrice(  $cart, $ship_id, $checkAutomaticSelected=true) {
	

		
		$val = OPCcache::getValue(calculationHelperOPC::$_forhash); 
		
		
		
		if (!empty($val))
		{
			$this->_cartData['shipmentName'] = $val['shipmentName']; 
			
			OPCcache::arrayMerge($this->_cartPrices, $val); 
			
			return $this->_cartPrices; 
		}
		
		
		
		$this->_cartData['shipmentName'] = JText::_('COM_VIRTUEMART_CART_NO_SHIPMENT_SELECTED');
		$this->_cartPrices['shipmentValue'] = 0; //could be automatically set to a default set in the globalconfig
		$this->_cartPrices['shipmentTax'] = 0;
		$this->_cartPrices['salesPriceShipment'] = 0;
		$this->_cartPrices['shipment_calc_id'] = 0;
		// check if there is only one possible shipment method
		
		$automaticSelectedShipment =   $cart->CheckAutomaticSelectedShipment($this->_cartPrices, $checkAutomaticSelected);
		if ($automaticSelectedShipment) $ship_id=$cart->virtuemart_shipmentmethod_id;
		if (empty($ship_id)) 
		{
			$arr = OPCcache::getSearch('shipment', $this->_cartPrices); 
		    $arr['shipmentName'] = $this->_cartData['shipmentName']; 
		    $val = OPCcache::setValue(calculationHelperOPC::$_forhash, $arr); 
		    return $this->_cartPrices;
		}

		// Handling shipment plugins
		if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS .DIRECTORY_SEPARATOR. 'vmpsplugin.php');
		JPluginHelper::importPlugin('vmshipment');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('plgVmonSelectedCalculatePriceShipment',array(  $cart, &$this->_cartPrices, &$this->_cartData['shipmentName']  ));

		/*
		   * Plugin return true if shipment rate is still valid
		   * false if not any more
		   */
		$shipmentValid=0;
		foreach ($returnValues as $returnValue) {
			$shipmentValid += $returnValue;
		}
		if (!$shipmentValid) {
			$cart->virtuemart_shipmentmethod_id = 0;
			$cart->setCartIntoSession();
		}

		// store the cached response
		$arr = OPCcache::getSearch('shipment', $this->_cartPrices); 
		
		$arr['shipmentName'] = $this->_cartData['shipmentName']; 
		$val = OPCcache::setValue(calculationHelperOPC::$_forhash, $arr); 
		
		return $this->_cartPrices;
	}
	
	/* original constructor */
		/** Constructor,... sets the actual date and current currency
	 *
	 * @copyright Copyright (c) 2009 VirtueMart Team. All rights reserved.
	 * @author Max Milbers
	 * @author Geraint
	 */
	public function __construct() {
		$this->_db = JFactory::getDBO();
		$this->_app = JFactory::getApplication();

		//We store in UTC and use here of course also UTC
		$jnow = JFactory::getDate();
	        if (method_exists($jnow, 'toMySQL'))
		$this->_now = $jnow->toMySQL();
		else $this->_now = $jnow->toSQL();

		$this->_nullDate = $this->_db->getNullDate();

		//Attention, this is set to the mainvendor atm.
		//This means also that atm for multivendor, every vendor must use the shopcurrency as default
		//         $this->vendorCurrency = 1;
		$this->productVendorId = 1;

		if (!class_exists('CurrencyDisplay')
		)require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'currencydisplay.php');
		$this->_currencyDisplay = CurrencyDisplay::getInstance();
		$this->_debug = false;

		if(!empty($this->_currencyDisplay->_vendorCurrency)){
			$this->vendorCurrency = $this->_currencyDisplay->_vendorCurrency;
			$this->vendorCurrency_code_3 = $this->_currencyDisplay->_vendorCurrency_code_3;
			$this->vendorCurrency_numeric = $this->_currencyDisplay->_vendorCurrency_numeric;
		}
	/*	else if(VmConfig::get('multix','none')!='none'){
			$this->_db->setQuery('SELECT `vendor_currency` FROM #__virtuemart_vendors  WHERE `virtuemart_vendor_id`="1" ');
			$single = $this->_db->loadResult();
			$this->vendorCurrency = $single;
		}*/
		
		if (method_exists($this, 'setShopperGroupIds'))
		{
		 $a = get_class_methods ($this); 
		 if (in_array('setShopperGroupIds', $a))
		 {
		    $this->setShopperGroupIds();	 
		 }
		 	  
		 
		 
		}
		
		if (method_exists($this, 'setVendorId'))
		$this->setVendorId($this->productVendorId);

		$this->rules['Marge'] = array();
		$this->rules['Tax'] 	= array();
		$this->rules['VatTax'] 	= array();
		$this->rules['DBTax'] = array();
		$this->rules['DATax'] = array();

		//round only with internal digits
		$this->_roundindig = VmConfig::get('roundindig',FALSE);
	}
	
	
	

}