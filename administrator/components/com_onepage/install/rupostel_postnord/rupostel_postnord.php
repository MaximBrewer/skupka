<?php

defined('_JEXEC') or die('Restricted access');

	if (!class_exists('vmPSPlugin'))
    require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR. 'vmpsplugin.php');

class plgVmShipmentRupostel_postnord extends vmPSPlugin
{
    // instance of class
    public static $_this = false;
    
    function __construct(&$subject, $config)
    {
		
        parent::__construct($subject, $config);
        
		
		
        $this->_loggable   = true;
		$this->_tablepkey = 'id';
		$this->_tableId = 'id';
		
        $this->tableFields = array_keys($this->getTableSQLFields());
        $varsToPush        = $this->getVarsToPush();
        $this->setConfigParameterable($this->_configTableFieldName, $varsToPush);        
        

if (!class_exists ('calculationHelper')) {
  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'calculationh.php');
}
if (!class_exists ('CurrencyDisplay')) {
  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'currencydisplay.php');
}
if (!class_exists ('VirtueMartModelVendor')) {
  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'vendor.php');
}	
		

        
    }    

    /**
     * Create the table for this plugin if it does not yet exist.
     * @author Valérie Isaksen
     */
    public function getVmPluginCreateTableSQL()
    {
        return $this->createTableSQL('rupostel_postnord');
    }
    
    function getTableSQLFields()
    {
        $SQLfields = array(
            'id' => 'int(11) UNSIGNED NOT NULL AUTO_INCREMENT',
            'virtuemart_order_id' => 'int(11) UNSIGNED',
            'virtuemart_shipmentmethod_id' => 'mediumint(1) UNSIGNED',
            'order_number' => 'char(32)',
			'service_id' => 'varchar(10)',
            'postnord_packet_id' => 'INT(11)',
            'postnord_packet_price' => 'DECIMAL(15,2)',
            'branch_id' => 'INT(11)',
            'branch_currency' => 'char(5)',
            'branch_name_street' => 'varchar(500)',
			'order_weight' => 'DECIMAL(10,4)',
			'label'=>'LONGTEXT',
			'registeredtime'=>'varchar(255)',
            'email' => 'varchar(255)', 
            'phone' => 'varchar(255)', 
            'first_name' => 'varchar(255)',
            'last_name' => 'varchar(255)',
            'address' => 'varchar(255)',
            'city' => 'varchar(255)',
            'zip_code' => 'varchar(255)',            
            'virtuemart_country_id' => 'varchar(255)',            
            'adult_content' => 'SMALLINT(1)', 
            'is_cod' => 'SMALLINT(1)',            
            'exported' => 'SMALLINT(1)',
            'printed_label' => 'SMALLINT(1)',            
            'shipment_name' => 'varchar(5000)',            
            'shipment_cost' => 'DECIMAL(10,2)',
            'shipment_package_fee' => 'DECIMAL(10,2)',
            'tax_id' => 'smallint(1)'
        );
        return $SQLfields;
    }
    
    /**
     * This method is fired when showing the order details in the frontend.
     * It displays the shipment-specific data.
     *
     * @param integer $order_number The order Number
     * @return mixed Null for shipments that aren't active, text (HTML) otherwise
     * @author Valérie Isaksen
     * @author Max Milbers
     */
    public function plgVmOnShowOrderFEShipment($virtuemart_order_id, $virtuemart_shipmentmethod_id, &$shipment_name)
    {
        $this->onShowOrderFE($virtuemart_order_id, $virtuemart_shipmentmethod_id, $shipment_name);
    }
    
    /**
     * This event is fired after the order has been stored; it gets the shipment method-
     * specific data.
     *
     * @param int $order_id The order_id being processed
     * @param object $cart  the cart
     * @param array $priceData Price information for this order
     * @return mixed Null when this method was not selected, otherwise true
     * @author Valerie Isaksen
     */
    
    function plgVmConfirmedOrder(VirtueMartCart $cart, $order)
    {
        if (!($method = $this->getVmPluginMethod($order['details']['BT']->virtuemart_shipmentmethod_id))) {
			
			
            return null; // Another method was selected, do nothing
        }
        if (!$this->selectedThisElement($method->shipment_element)) {
            
			return false;
        }
		
		
		
        if (!$this->OnSelectCheck($cart)) {
			
            return false;
        }
		
		$session = JFactory::getSession(); 
		$branch_id = JRequest::getVar('postnord_pobocky', $session->get('postnord_pobocka', '')); 
		$w = $this->getOrderWeight ($cart, $method->weight_unit);
		$cost = $this->getCosts ($cart, $method, ""); 
		
		$values = $this->getValues2Store($order, $branch_id, $w, 0, $cost, $method); 
		
		
        $ret = $this->storePSPluginInternalData($values);
		
		
		
        return true;
    }
	
	
	private function getData($order_id)
	{
		$q = 'select * from `'.$this->_tablename.'` where virtuemart_order_id = '.(int)$order_id; 
		$db = JFactory::getDBO(); 
		$db->setQuery($q); 
		$values = $db->loadAssoc(); 
		if (!empty($values))
		return $values; 
	
	    return array(); 

	}
	public function getValues2Store($order, $branch_id=0, $order_weight=0, $service_id=0, $cost=0, $method)
	{
		
		$values = $this->getData($order['details']['BT']->virtuemart_order_id); 
		if (empty($values)) 
		{
		$values = array(); 
		$values['id'] = null; 
		}
		else
		{
			$values['id'] = $values['id']; 
		}
			
        $values['virtuemart_order_id']          = $order['details']['BT']->virtuemart_order_id;
        $values['virtuemart_shipmentmethod_id'] = $order['details']['BT']->virtuemart_shipmentmethod_id;
        $values['order_number']                 = $order['details']['BT']->order_number;
		
		if (empty($order_weight))
		{
		  $items = array(); 
		  foreach($order['items'] as $k=>$v)
		  {
			  $ids[$v->virtuemart_product_id] = $v->virtuemart_product_id; 
			  
		  }
		  
		  if (!empty($ids))
		  {
		  
			  $db = JFactory::getDBO(); 
			  $q = 'select virtuemart_product_id,product_weight, product_weight_uom from #__virtuemart_products where virtuemart_product_id in ('.implode(',', $ids).')'; 
			  $db->setQuery($q); 
			  $res = $db->loadAssocList(); 
			  
			  foreach ($res as $row)
			  {
				$ids[$row['virtuemart_product_id']] = (float)ShopFunctions::convertWeightUnit ($row['product_weight'], $row['product_weight_uom'], $method->weight_unit);
			  }
		  }
		  $w = 0; 
		  foreach($order['items'] as $k=>$v)
		  {
			  
			  $v->product_quantity  = (float)$v->product_quantity ; 
			  $w += ($v->product_quantity  * ($ids[$v->virtuemart_product_id])); 
		  }
		  
		  $values['order_weight'] = $w; 
		}
		else
		$values['order_weight'] = $order_weight; 
		
		
		
		$values['registeredtime'] = ''; 
		
        $values['postnord_packet_id']         = 0;
        $values['postnord_packet_price']      = $order['details']['BT']->order_total;
		$session = JFactory::getSession(); 
        $values['branch_id']                    = $branch_id; 
		
		
		if (!empty($branch_id))
		{
			
			$lang_l = JFactory::getLanguage()->getTag(); 
			if (!empty($order['details']['BT']->order_language))
		{
			$lang_l = $order['details']['BT']->order_language; 
		}
		    if (!empty($order['details']['ST']))
			$country_iso2 = $order['details']['ST']->virtuemart_country_id; 
		    else
			$country_iso2 = $order['details']['BT']->virtuemart_country_id; 
		
		    if (!empty($order['details']['ST']))
			$zip = $order['details']['ST']->zip; 
		    else
			$zip = $order['details']['BT']->zip; 
		
		
		$this->getCountryAndLocale($country_iso2, $lang_l);
		
		require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php'); 
		
		
		$pobocka = postnordHelper::getDataPobocky($method, $values['branch_id'], $method->cache, $country_iso2, $lang_l, $zip); 
		
		
		if ((empty($pobocka->nazev) && (empty($pobocka->ulice)) && (empty($pobocka->obec)))) {
		$pobocka = postnordHelper::getDataPobocky($method, $values['branch_id'], false, $country_iso2, $lang_l, $zip); 
		}
		
		
		$ptext = $pobocka->nazev.', '.$pobocka->ulice.', '.$pobocka->obec.', '.$pobocka->psc; 
		if (!empty($pobocka->provoz))
		$ptext .= '<br /> '.$pobocka->provoz.' '; 
		}
		else
		{
			$ptext = ''; 
			if ((!empty($order['details']['ST'])) && (!empty($order['details']['ST']->virtuemart_country_id)))
		  {
			  $address = $order['details']['ST']; 
		  }
		  else
		  {
			  $address = $order['details']['BT']; 
		  }
		  
		  $country_id = $address->virtuemart_country_id; 
		  
		  
		}
		
        $values['branch_name_street']           = $ptext;
        //$values['email']                        = $cart->BT['email'];
        //$values['phone']                        = $cart->BT['phone_1'];
        $values['adult_content']                = 0;
		
		  $cod = -1;
		  $payment_id = $order['details']['BT']->virtuemart_paymentmethod_id; 
		  if (!empty($method->cod_payments))
		  if (in_array($payment_id, $method->cod_payments))
		  {
			  $cod = 1; 
		  }
		
        $values['is_cod']                       = $cod; //depends on actual settings of COD payments until its set manually in administration
        $values['exported']                = 0;        
        $values['shipment_name']                = $method->shipment_name.': '.$ptext;             
        $values['shipment_cost']                = $cost;
        $values['tax_id']                       = $method->tax_id;
		$values['service_id'] = 0; 
		return $values;  
	}
	
	public function getCountryAndLocale(&$country, &$tag)
	{
		$db = JFactory::getDBO(); 
		if (empty($country)) 
		{
			$country = 'SE'; 
		}
		else
		{
		
		
		 $q = 'select `country_2_code` from `#__virtuemart_countries` where `virtuemart_country_id` = '.(int)$country.' limit 0,1'; 
		  $db = JFactory::getDBO(); 
		  $db->setQuery($q); 
		  $country_iso2 = $db->loadResult(); 
		  
		  if (!empty($country_iso2)) $country = strtoupper($country_iso2); 
		  else $country = 'SE'; 
		}
		$avai = array('sv', 'no', 'da', 'fi', 'en'); 
		foreach ($avai as $l)
		{
			if (stripos($tag, $l)!==false)
			{
				$tag = $l; 
				break;
				return;				
			}
		}
		$tag = 'en'; 
		
		  
		
		
	}
	
	
	
	
    
    
  /**
   * calculateSalesPrice
   * overrides default function to remove currency conversion
   * @author Zasilkovna
   */

  function calculateSalesPrice ($cart, $method, $cart_prices) {    
      $value = $this->getCosts ($cart, $method, $cart_prices);

      $tax_id = @$method->tax_id;

	  $vendor_id = $cart->vendorId; 
	  if (empty($vendor_id))
      $vendor_id = 1;
	  
      $vendor_currency = VirtueMartModelVendor::getVendorCurrency ($vendor_id);

      $db = JFactory::getDBO ();
      $calculator = calculationHelper::getInstance ();
      $currency = CurrencyDisplay::getInstance ();    

      $taxrules = array();
	  
      if (!empty($tax_id)) {
        $q = 'SELECT * FROM #__virtuemart_calcs WHERE `virtuemart_calc_id`="' . $tax_id . '" ';
        $db->setQuery ($q);
        $taxrules = $db->loadAssocList ();
      }

      if (count ($taxrules) > 0) {
        $salesPrice = $calculator->roundInternal ($calculator->executeCalculation ($taxrules, $value));
      } else {
        $salesPrice = $value;
      }      
      return $salesPrice;       
  }

    
  
    /**    
     * @return delivery cost for the shipping method instance
     * @author Zasilkovna
     */
    
    function getCosts(VirtueMartCart $cart, $method, $cart_prices, $pobocka=0)
	{         
		if ((!empty($method->free_shipment)) && $cart_prices['salesPrice'] >= $method->free_shipment) {
			return 0.0;
		} else {

			$value = (float)$method->shipment_cost + (float)$method->package_fee;
			
			return $value; 
		}

	
    }
    
	private function getParcelCount($method, $weight)
	{
		$w = (float)$method->weight_stop; 
		 $times = $weight / $w; 
		  $c = ceil($times); 
		  return (int)$c; 
	}
	/**
	 * @param \VirtueMartCart $cart
	 * @param int             $method
	 * @param array           $cart_prices
	 * @return bool
	 */
	protected function checkConditionsWC ($cart, $method, $cart_prices) {

		$result = array();

		if($cart->STsameAsBT == 0){
			$type = ($cart->ST == 0 ) ? 'BT' : 'ST';
		} else {
			$type = 'BT';
		}
		if (method_exists($cart, 'getST'))
		{
		$address = $cart -> getST();
		}
		else
		{
			$address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
		}
		if(!is_array($address)) $address = array();
		if(isset($cart_prices['salesPrice'])){
			$hashSalesPrice = $cart_prices['salesPrice'];
		} else {
			$hashSalesPrice = '';
		}


		if(empty($address['virtuemart_country_id'])) $address['virtuemart_country_id'] = 0;
		if(empty($address['zip'])) $address['zip'] = 0;

		$hash = $method->virtuemart_shipmentmethod_id.$type.$address['virtuemart_country_id'].'_'.$address['zip'].'_'.$hashSalesPrice;

		if(isset($result[$hash])){
			return $result[$hash];
		}
		$this->convert ($method);
		$orderWeight = $this->getOrderWeight ($cart, $method->weight_unit);

		$countries = array();
		if (!empty($method->countries)) {
			if (!is_array ($method->countries)) {
				$countries[0] = $method->countries;
			} else {
				$countries = $method->countries;
			}
		}


		$weight_cond = $this->testRange($orderWeight,$method,'weight_start','weight_stop','weight');
		$nbproducts_cond = $this->_nbproductsCond ($cart, $method);

		if(isset($cart_prices['salesPrice'])){
			$orderamount_cond = $this->testRange($cart_prices['salesPrice'],$method,'orderamount_start','orderamount_stop','order amount');
		} else {
			return false; 
			$orderamount_cond = FALSE;
		}

		$userFieldsModel =VmModel::getModel('Userfields');
		if ($userFieldsModel->fieldPublished('zip', $type)){
			if (!isset($address['zip'])) {
				$address['zip'] = '';
			}
			$zip_cond = $this->testRange($address['zip'],$method,'zip_start','zip_stop','zip');
			
			if (!$zip_cond)
			{
				
				return false; 
			}
			
		} else {
			$zip_cond = true;
		}

		if ($userFieldsModel->fieldPublished('virtuemart_country_id', $type)){

			if (!isset($address['virtuemart_country_id'])) {
				$address['virtuemart_country_id'] = 0;
			}

			if (in_array ($address['virtuemart_country_id'], $countries) || count ($countries) == 0) {

				//vmdebug('checkConditions '.$method->shipment_name.' fit ',$weight_cond,(int)$zip_cond,$nbproducts_cond,$orderamount_cond);
				vmdebug('shipmentmethod '.$method->shipment_name.' = TRUE for variable virtuemart_country_id = '.$address['virtuemart_country_id'].', Reason: Countries in rule '.implode($countries,', ').' or none set');
				$country_cond = true;
			}
			else{
				vmdebug('shipmentmethod '.$method->shipment_name.' = FALSE for variable virtuemart_country_id = '.$address['virtuemart_country_id'].', Reason: Country '.implode($countries,', ').' does not fit');
				$country_cond = false;
				
			}
		} else {
			vmdebug('shipmentmethod '.$method->shipment_name.' = TRUE for variable virtuemart_country_id, Reason: no boundary conditions set');
			$country_cond = true;
		}

		$cat_cond = true;
		if (!empty($method->categories) || (!empty($method->blocking_categories)))
		if($method->categories or $method->blocking_categories){
			if($method->categories)$cat_cond = false;
			//vmdebug('hmm, my value',$method);
			//if at least one product is  in a certain category, display this shipment
			if(!is_array($method->categories)) $method->categories = array($method->categories);
			if(!is_array($method->blocking_categories)) $method->blocking_categories = array($method->blocking_categories);
			//Gather used cats
			if (!empty($cart->products))
			foreach($cart->products as $product){
				if(array_intersect($product->categories,$method->categories)){
					$cat_cond = true;
					//break;
				}
				if(array_intersect($product->categories,$method->blocking_categories)){
					return false; 
					$cat_cond = false;
					break;
				}
			}
			//if all products in a certain category, display the shipment
			//if a product has a certain category, DO NOT display the shipment
		}

		$allconditions = (int) $weight_cond + (int)$zip_cond + (int)$nbproducts_cond + (int)$orderamount_cond + (int)$country_cond + (int)$cat_cond;

		return true; 

	}
	function convert (&$method) {

		//$method->weight_start = (float) $method->weight_start;
		//$method->weight_stop = (float) $method->weight_stop;
		$method->orderamount_start =  (float)str_replace(',','.',$method->orderamount_start);
		$method->orderamount_stop =   (float)str_replace(',','.',$method->orderamount_stop);
		$method->zip_start = (int)$method->zip_start;
		$method->zip_stop = (int)$method->zip_stop;
		$method->nbproducts_start = (int)$method->nbproducts_start;
		$method->nbproducts_stop = (int)$method->nbproducts_stop;
		$method->free_shipment = (float)str_replace(',','.',$method->free_shipment);
	}
	
	private function _nbproductsCond ($cart, $method) {

		if (empty($method->nbproducts_start) and empty($method->nbproducts_stop)) {
			//vmdebug('_nbproductsCond',$method);
			return true;
		}

		$nbproducts = 0;
		foreach ($cart->products as $product) {
			$nbproducts += $product->quantity;
		}

		if ($nbproducts) {

			$nbproducts_cond = $this->testRange($nbproducts,$method,'nbproducts_start','nbproducts_stop','products quantity');

		} else {
			$nbproducts_cond = false;
		}

		return $nbproducts_cond;
	}
	
	private function testRange($value, $method, $floor, $ceiling,$name){

		$cond = true;
		if(!empty($method->$floor) and !empty($method->$ceiling)){
			$cond = (($value >= $method->$floor AND $value <= $method->$ceiling));
			if(!$cond){
				$result = 'FALSE';
				$reason = 'is NOT within Range of the condition from '.$method->$floor.' to '.$method->$ceiling;
			} else {
				$result = 'TRUE';
				$reason = 'is within Range of the condition from '.$method->$floor.' to '.$method->$ceiling;
			}
		} else if(!empty($method->$floor)){
			$cond = ($value >= $method->$floor);
			if(!$cond){
				$result = 'FALSE';
				$reason = 'is not at least '.$method->$floor;
			} else {
				$result = 'TRUE';
				$reason = 'is over min limit '.$method->$floor;
			}
		} else if(!empty($method->$ceiling)){
			$cond = ($value <= $method->$ceiling);
			if(!$cond){
				$result = 'FALSE';
				$reason = 'is over '.$method->$ceiling;
			} else {
				$result = 'TRUE';
				$reason = 'is lower than the set '.$method->$ceiling;
			}
		} else {
			$result = 'TRUE';
			$reason = 'no boundary conditions set';
		}

		vmdebug('shipmentmethod '.$method->shipment_name.' = '.$result.' for variable '.$name.' = '.$value.' Reason: '.$reason);
		return $cond;
	}
    /** TODO
    * Here can add check if user has filled in valid phone number or mail so he is reachable by zasilkovna
    */
    protected function checkConditions($cart, $method, $cart_prices)
    {
		
		if (!$this->checkConditionsWC($cart, $method, $cart_prices))
		{
			
			return false; 
		}
		
	   	$address = (($cart->ST == 0) ? $cart->BT : $cart->ST);

	   
	  $wstart = (float)$method->weight_start;
	  
      $ws = (float)$method->weight_stop;
      $orderWeight = $this->getOrderWeight ($cart, $method->weight_unit);
	  
	  if (!empty($wstart))
	  if ($orderWeight < $wstart) {
		  if (class_exists('OPCloader'))
		   {
			   OPCloader::opcDebug('OPC: Outside weight range', 'postnord');    
		   }

		  return false; 
	  }
	  
	  // do not allow oversize packages
	  if (!empty($ws))
      if (empty($method->strategy))
	  if ($orderWeight > $ws) {
		  if (class_exists('OPCloader'))
		   {
			   OPCloader::opcDebug('OPC: Outside weight range strategy', 'postnord');    
		   }
		  
		  return false; 
	  }
	  
	 
	  
	  $total = 0; 
	  
	  if ((!isset($cart->pricesUnformatted)) && (isset($cart->cartPrices)))
	  {
		  $cart->pricesUnformatted =& $cart->cartPrices; 
		  $total = (float)$cart->pricesUnformatted['billTotal']; 
	  }
	  
	  if (empty($total))
	  if (isset($cart->cartPrices))
	  {
	  
	 
		  if (!empty($cart->cartPrices['salesPrice']))
		  {
			  $total = $cart->cartPrices['salesPrice']; 
		  }
	  
	      if ((empty($total)) && (!empty($cart->cartPrices['billSub'])))
		  {
			  $total = floatval($cart->cartPrices['billSub']); 
			  if (!empty($cart->cartPrices['billTaxAmount']))
			  {
				  $total += floatval($cart->cartPrices['billTaxAmount']); 
			  }
		  }
	  }
	  
	  
	  
	  if (!empty($total)) {
	  if (!empty($method->orderamount_start))
	   {
	     $st = (float)$method->orderamount_start; 
		 if (($total < $st)) {
			 
			if (class_exists('OPCloader'))
		   {
			   OPCloader::opcDebug('OPC: Order amount start not met', 'postnord');    
		   }

			 
			 return false; 
		 }
	   }

	   if (!empty($method->orderamount_stop))
	   {
	     $st = (float)$method->orderamount_stop; 
		 if (($total < $st)) {
			 
		  if (class_exists('OPCloader'))
		   {
			   OPCloader::opcDebug('OPC: Max Order amount met', 'postnord');    
		   }

			 
			 return false; 
		 }
	   }
	  }

	   
      return true;
    }      
    
    /*
     * We must reimplement this triggers for joomla 1.7
     */
    
    /**
     * Create the table for this plugin if it does not yet exist.
     * This functions checks if the called plugin is active one.
     * When yes it is calling the standard method to create the tables
     * @author Valérie Isaksen
     *
     */
    function plgVmOnStoreInstallShipmentPluginTable($jplugin_id)
    {
        return $this->onStoreInstallPluginTable($jplugin_id);
    }
    
    /**
     * This event is fired after the shipment method has been selected. It can be used to store
     * additional payment info in the cart.
     *
     * @author Max Milbers
     * @author Valérie isaksen
     *
     * @param VirtueMartCart $cart: the actual cart
     * @return null if the payment was not selected, true if the data is valid, error message if the data is not vlaid
     *
     */
    // public function plgVmOnSelectCheck($psType, VirtueMartCart $cart) {
    // return $this->OnSelectCheck($psType, $cart);
    // }
    public function plgVmOnSelectCheckShipment(VirtueMartCart &$cart)
    {        
		if (!$this->_selectedThisByMethodId($cart->virtuemart_shipmentmethod_id)) {
			return NULL; // Another method was selected, do nothing
		}

		if (!$this->getVmPluginMethod($cart->virtuemart_shipmentmethod_id)) {
			return NULL; // Another method was selected, do nothing
		}


		
		$saved = JRequest::getVar('postnord_saved'); 
		
		
		if (!empty($saved)) JRequest::setVar('postnord_pobocka', $saved); 


		
	    $pobocka = JRequest::getVar('postnord_pobocky', ''); 
		$pobocka = (int)$pobocka; 
		/*
		if (empty($pobocka))
		{
			$id2 = JRequest::getVar('opc_shipment_price_id'); 
			important; price_id is not unique per pobocka
			if (!empty($id2))
			{
				$pobockaA = $this->parseId($id2); 
				if (!empty($pobockaA['pobocka_id']))
				{
				 //JRequest::getVar('postnord_pobocky', $pobockaA['pobocka_id']); 
				}
			}
		}
		*/
		
        if ($this->OnSelectCheck($cart)) {

			if (empty($pobocka)) 
			  {
			  
			    return false; 
			    
			  }
			$session = JFactory::getSession(); 
			$session->set('postnord_pobocka', (int)$pobocka); 
        }else{
			
        }
        
        $ret = $this->OnSelectCheck($cart);
		
		return $ret; 
    }
	
    /**
     * plgVmDisplayListFE
     * This event is fired to display the pluginmethods in the cart (edit shipment/payment) for exampel
     *
     * @param object $cart Cart object
     * @param integer $selected ID of the method selected
     * @return boolean True on succes, false on failures, null when this plugin was not selected.
     * On errors, JError::raiseWarning (or JError::raiseError) must be used to set a message.
     *
     * @author Valerie Isaksen
     * @author Max Milbers
     */
    public function plgVmDisplayListFEShipment(VirtueMartCart $cart, $selected = 0, &$htmlIn)
    {
	
        $js_html = '';
        if ($this->getPluginMethods($cart->vendorId) === 0) {
                return FALSE;            
        }        

        require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php'); 
		
	    $address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
      
		
		
		$html        = array();
        $method_name = $this->_psType . '_name';
        $document = JFactory::getDocument(); 
		
		
		$zip = $address['zip']; 
		
		$country_iso2 = $address['virtuemart_country_id']; 
		$lang_l = JFactory::getLanguage()->getTag(); 
		$this->getCountryAndLocale($country_iso2, $lang_l); 
		
		$address_hash = $country_iso2.'_'.$lang_l.'_'.$zip; 
		
        foreach ($this->methods as $key => $method) { 
		
		
			//if (!$this->checkConditions ($cart, $method, $cart->pricesUnformatted)) continue; 
		
			
			$mymethod = $method; 
			$cache_d = JPATH_CACHE.DIRECTORY_SEPARATOR.'postnord'.DIRECTORY_SEPARATOR.'postnord_html_'.'__'.$method->virtuemart_shipmentmethod_id.'_'.$address_hash.'.html'; 
			$cache = $method->cache; 
			
			if ((file_exists($cache_d)) && (!empty($cache)))
			 {
			   if (!isset($html[$key])) $html[$key] = ''; 
			   $html[$key] .= file_get_contents($cache_d); 
			   
			   
			   
			   $pobocka = JRequest::getVar('postnord_pobocky', ''); 
			   
			   
			   if (!empty($pobocka)) 
			   {
				   $sind = $pobocka; 
			   
			   $html[$key] = str_replace('value="'.$sind.'"', 'value="'.$sind.'" selected="selected" ', $html[$key]); 
			   }
			   
		
			   
			 }
			 else
			 {
			
		
			
		    $xml = postnordHelper::getPobocky($method, $cache, $country_iso2, $lang_l, $zip); 	
			
            $html[$key] = '';
			
			$sind = ''; 
			
			
			
			if (isset($xml->pobocky)) {
			if (count($xml->pobocky)) {
				
				
				$c = count($xml->pobocky); 
				if ($c === 1)
				{
					$first = reset($xml->pobocky); 
					if (isset($first->id))
					{
						$sind = $first->id; 
					}
				}
				

				$html[$key] .= '';
				$k=1;
				$first=true;
				$pobocky_options = array();
				$js_adresa="\n\nvar adresa=new Array();";
				$js_oteviraci_doba="\n\nvar oteviraci_doba=new Array();";
				$js_cena="\n\nvar cena=new Array();";
				$js_values="\n\nvar values=new Array();";
//				$js_mapa="\n\nvar mapa=new Array();";
				$first_opt = 0; 
				
				$ind = 0; 
				$pobocky_html = ''; 
				foreach ($xml->pobocky as $p) {
				    
					
					
					
					$enabled = true;
					
					if ($enabled) {
						if ((string)$p->aktiv) {
							
							
							
							$session = JFactory::getSession(); 
							$first_opt = $session->get('postnord_pobocka', $first_opt); 
							$mk = 0; 
							
							
					

	
	  $session = JFactory::getSession();   
	  $pobocka = JRequest::getVar('postnord_pobocky', $session->get('postnord_pobocka', '')); 

		
	    
		if (empty($sind))
		if (!empty($pobocka)) $sind = $pobocka; 
							
							if (empty($first_opt)) $first_opt = $p->id; 
							if ($first_opt == $p->id) $sind = $p->id; 
							$ind++; 
							
							
							
							
							
							
							//$pobocky_options[] = JHTML::_('select.option',  $p->id, $p->nazev );
							$opobocka = new stdClass(); 
							$opobocka->id = $p->id; 
							$opobocka->nazev = $p->nazev; 
							$pobocky_options[] = $opobocka; 
							//$pobocky_options[] = JHTML::_('select.option',  $p->id, $p->nazev );
							$js_adresa.="\nadresa[".$p->id."]='<b>".$p->nazev.'</b><br />'.$p->ulice.'<br />'.$p->obec.'<br />'.$p->psc."';";
							$p->provoz = str_replace("\n\r", '', $p->provoz); 
							$p->provoz = str_replace("\n", '', $p->provoz); 
							$js_oteviraci_doba.="\noteviraci_doba[".$p->id."]='".$p->provoz."';";
							
							
							
							$pobocky_html .= $this->renderByLayout('pobocka', array(
									'pobocka' => $p, 
									'sind' => $sind, 
							)); 
							
							//$js_mapa.="\nmapa[".$p->id."]='".$p->mapa."';";
						}
					}
				}
				
				
				if ($first) {
							$cena = ''; 
								$output1 = $this->renderByLayout('pobocky', array(
								  'virtuemart_shipmentmethod_id'=>$method->virtuemart_shipmentmethod_id, 
								  'first_opt'=>$first_opt,
								  'method'=>$method,
								)); 
								$detail_url = JURI::root().'plugins/vmshipment/postnord/detail_pobocky.php?id='.(string)$p->id;
								
								$first = false;

							}
				
				foreach ($pobocky_options as $k => &$ppp)
				 {
					 
					 
					 $enabled = true; 
					 if (!isset($ppp->price_key))
					 $pobocky_options[$k]->price_key = 'branch_for_'.$method->virtuemart_shipmentmethod_id; 
					 
					 
					 
					
				 }
				
				 $pobocky = $this->renderByLayout('pobocky_select', array(
				    'pobocky_options' => $pobocky_options,
					'virtuemart_shipmentmethod_id' => $method->virtuemart_shipmentmethod_id,
					'xml' => $xml,
					'sind' => $sind,
					'method'=>$method,
				 )); 
				 
				 
				

				$html[$key] .= str_replace('{combobox}', $pobocky, $output1);
				$html[$key] .= $pobocky_html; 
				$html[$key] .= "\n";
			
				JFile::write($cache_d, $html[$key]);
			
			}  // pobocky not empty
			
			} // isset pobocky
			
		} // end of ... if not cache ... 
			
			
           
        } // end of foreach 
	
		if (isset($mymethod))
		if (!defined('postnord_javascript'))
				{
				$document->addScriptDeclaration(
				"\n".'//<![CDATA['."\n" 
				."\n\nvar detail_url='".JURI::root()."plugins/vmshipment/postnord/detail_pobocky.php?id=';\n"	
				."\n\n
				function changepostnord(id, update) {
				    if (typeof jQuery != 'undefined')
					 jQuery('.zasielka_div1').not('#postnord_branch_' + id).hide();
					document.getElementById('postnord_pobocka').value=id;
					
					var d = document.getElementById('postnord_saved'); 
					if (d != null)
					d.value = id; 
					if (update)
					{
					document.getElementById('postnord_branch_'+id).style.display='block';
					if (typeof jQuery != 'undefined')
					{
					  jQuery('#shipment_id_".$method->virtuemart_shipmentmethod_id."').click(); 
					}
					else
					document.getElementById('shipment_id_".$method->virtuemart_shipmentmethod_id."').onclick();
					}
					if (typeof Onepage != 'undefined')
					Onepage.changeTextOnePage3();
					
				};\n".'//]]>'."\n"); 
				define('postnord_javascript', true); 
				}
		
		

        if (empty($html)) {
            return FALSE;
        }
        
		
		
        $htmlIn[] = $html;
        return TRUE;
    }
	
 
	
  public function getMultiShipmentIds(&$ret, $cart)
  {
	  if (!($method = $this->getVmPluginMethod($cart->virtuemart_shipmentmethod_id))) {
            return null; // Another method was selected, do nothing
        }
		
		if (!$this->selectedThisElement($method->shipment_element)) {
            return false;
        }
		
	   $calcs = array(); 
	   
		static $mr; 		
		if (empty($mr)) {
		  $mr = array(); 
		}
		else
		{
			$ret = $mr; 
			return true; 
		}
		 
		 
		 //foreach ($this->methods as $method)
		 {
		 $calcs['shipment_id_'.$method->virtuemart_shipmentmethod_id] = 'shipment_id_'.$method->virtuemart_shipmentmethod_id; 
		 $xml = postnordHelper::getPobocky($method); 	
			
           
			
			
			
			if (isset($xml->pobocky)) {
			if (count($xml->pobocky)) {
		foreach ($xml->pobocky as $p)
		{
			
			
			$enabled = true; 
			if (!$enabled) continue; 
			
			$pobocka = $p->id; 
			$key = $this->getPobockaPriceKey($method, $p); 
			$calcs[$key] = $key; 
			
			//$mr['shipment_id_'.$method->virtuemart_shipmentmethod_id.'_'.$p->id] = 'shipment_id_'.$method->virtuemart_shipmentmethod_id.'_'.$p->id; 
		}
			}
		
		
		//
			}
			}
			
			$ret = $mr = $calcs; 
			//$ret = $mr; 
  }
  
  
  private static $ids; 
  private function &_selectedThisByMethodId($id)
  {
	  $id = (int)$id; 
	  if (empty(self::$ids)) self::$ids = array(); 
	  
	  
	  if (isset(self::$ids[$id])) 
	  return self::$ids[$id]; 
	  
	  $val = $this->selectedThisByMethodId ($id); 
	  self::$ids[$id] =& $val; 
	  return $val; 
	  
	  
	  
  }
  
  public function setOPCbeforeSelect($cart, $shipmentid, $shipping_method, $id, &$html)
   {

	  if (!($this->_selectedThisByMethodId ($id))) {
       return NULL;
      }
	  $session = JFactory::getSession();   
	  $ret = JRequest::getVar('postnord_pobocky', $session->get('postnord_pobocka', '')); 
	  $ret = (int)$ret; 
	  if (!empty($ret))
	  {
		  $session->set('postnord_pobocka', $ret);
		  
	  }
	  if (!empty($ret)) return $ret; 
	  
	 
	  
	  
	  $a = explode('_', $shipmentid); 
	  
	  
	  //from: <option ismulti="true" multi_id="shipment_id_'.$method->virtuemart_shipmentmethod_id.'_'.$ppp->id.'" 
	  if (!empty($a[3]))
	   {
	     $pobocka = $a[3]; 
		
		 JRequest::setVar('postnord_pobocka', $pobocka); 
		 
		 
	   }
	   if (empty($pobocka)) $pobocka = ''; 
	   if (!defined('postnord_saved'))
	   {
	     $html .= '<input type="hidden" name="postnord_saved" value="'.$pobocka.'" id="postnord_saved" />'; 
		 define('postnord_saved', true); 
	   }
	  
   }
  /**
   * This method is fired when showing the order details in the backend.
   * It displays the shipment-specific data.
   * NOTE, this plugin should NOT be used to display form fields, since it's called outside
   * a form! Use plgVmOnUpdateOrderBE() instead!
   *
   * @param integer $virtuemart_order_id The order ID
   * @param integer $virtuemart_shipmentmethod_id The order shipment method ID
   * @param object  $_shipInfo Object with the properties 'shipment' and 'name'
   * @return mixed Null for shipments that aren't active, text (HTML) otherwise
   * @author Valerie Isaksen
   */
  public function plgVmOnShowOrderBEShipment ($virtuemart_order_id, $virtuemart_shipmentmethod_id) {

    if (!($this->_selectedThisByMethodId ($virtuemart_shipmentmethod_id))) {
      return NULL;
    }
    $html = $this->getOrderShipmentHtml ($virtuemart_order_id);
    return $html;
  }

  /**
   * @param $virtuemart_order_id
   * @return string
   * @author zasilkovna
   */
  function getOrderShipmentHtml ($virtuemart_order_id) {

    $db = JFactory::getDBO ();
    $q = 'SELECT * FROM `' . $this->_tablename . '` '
      . 'WHERE `virtuemart_order_id` = ' . $virtuemart_order_id;
    $db->setQuery ($q);
    if (!($shipinfo = $db->loadObject ())) {
      vmWarn (500, $q . " " . $db->getErrorMsg ());
      return '';
    }

    if (!class_exists ('CurrencyDisplay')) {
      require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'currencydisplay.php');
    }

    $currency = CurrencyDisplay::getInstance ();
    $tax = ShopFunctions::getTaxByID ($shipinfo->tax_id);
    $taxDisplay = is_array ($tax) ? $tax['calc_value'] . ' ' . $tax['calc_value_mathop'] : $shipinfo->tax_id;
    $taxDisplay = ($taxDisplay == -1) ? JText::_ ('COM_VIRTUEMART_PRODUCT_TAX_NONE') : $taxDisplay;

    $html = '<table class="adminlist">' . "\n";
    $html .= $this->getHtmlHeaderBE ();
    $html .= $this->getHtmlRowBE ('WEIGHT_COUNTRIES_SHIPPING_NAME', $shipinfo->shipment_name);   
    //$html .= $this->getHtmlRowBE ('BRANCH', $shipinfo->branch_name_street);   
    //$html .= $this->getHtmlRowBE ('CURRENCY', $shipinfo->branch_currency);       

    $html .= '</table>' . "\n";

    return $html;
  }    
    private function getSelectedPobocka($order_id=0)
	 {
		
	   $session = JFactory::getSession(); 
	   //$ret = JRequest::getVar('postnord_pobocka', $session->get('postnord_pobocka', '')); 
	   $ret = JRequest::getVar('postnord_pobocky', $session->get('postnord_pobocka', '')); 
	   
	   if (!empty($ret)) return $ret; 
	   
	   if (!empty($order_id))
		{
			$db = JFactory::getDBO(); 
		    $q = 'select * from '.$this->_tablename.' where virtuemart_order_id = '.(int)$order_id.' limit 0,1'; 
			$db->setQuery($q); 
			$res = $db->loadAssoc(); 
			if (!empty($res))
			return $res['branch_id']; 
		}
		
	   return 0; 
	 }

	 
    public function plgVmonSelectedCalculatePriceShipment(VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name)
    {        
		
		
		if (!($method = $this->_selectedThisByMethodId ($cart->virtuemart_shipmentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}

		if (!($method = $this->getVmPluginMethod ($cart->virtuemart_shipmentmethod_id))) {
			return NULL;
		}
		
		
		$cart_prices_name = '';
		$cart_prices['cost'] = $cart_prices[$this->_psType . 'Value'] = $this->getCosts($cart, $method, $cart_prices); 

		if (!$this->checkConditions ($cart, $method, $cart_prices)) {
			
			return FALSE;
		}

		$cart_prices_name = $this->renderPluginName ($method);
		$this->setCartPrices ($cart, $cart_prices, $method);

		return TRUE;
        //return $this->onSelectedCalculatePrice($cart, $cart_prices, $cart_prices_name);
    }
    
	
	function plgVmOnCheckoutCheckDataShipment($cart)
	{
	   if (!($method = $this->getVmPluginMethod($cart->virtuemart_shipmentmethod_id))) {
            return null; // Another method was selected, do nothing
        }
		
		if (!$this->selectedThisElement($method->shipment_element)) {
            return null;
        }

		  return true; 
		$address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
		$address = (object)$address; 
		 if (!empty($address->phone_1))
		  $customer_phone = $address->phone_1; 
		  else
		  if (!empty($address->phone_2))
		  $customer_phone = $address->phone_2; 
		  else
		  if (!empty($address->phone))
		  $customer_phone = $address->phone; 
		  else
		  if (!empty($address->mobile))
		  $customer_phone = $address->mobile; 
		
		  if (!isset($customer_phone)) $customer_phone = '';
		  
		  $customer_phone = preg_replace("/[^0-9]/", "", $customer_phone);
		  
		  if (empty($customer_phone))
		  {
			  JFactory::getApplication()->enqueueMessage(JText::_('COM_VIRTUEMART_USER_FORM_MISSING_REQUIRED_JS').': '.JText::_('COM_VIRTUEMART_SHOPPER_FORM_PHONE')); 
			  return false; 
		  }
		  
		  return true; 
	}
	
	
    /**
     * plgVmOnCheckAutomaticSelected
     * Checks how many plugins are available. If only one, the user will not have the choice. Enter edit_xxx page
     * The plugin must check first if it is the correct type
     * @author Valerie Isaksen
     * @param VirtueMartCart cart: the cart object
     * @return null if no plugin was found, 0 if more then one plugin was found,  virtuemart_xxx_id if only one plugin is found
     *
     */
    function plgVmOnCheckAutomaticSelectedShipment(VirtueMartCart $cart, array $cart_prices = array())
    {
        return $this->onCheckAutomaticSelected($cart, $cart_prices);
    }
    
    /**
     * This event is fired during the checkout process. It can be used to validate the
     * method data as entered by the user.
     *
     * @return boolean True when the data was valid, false otherwise. If the plugin is not activated, it should return null.
     * @author Max Milbers
     
     public function plgVmOnCheckoutCheckData($psType, VirtueMartCart $cart) {
     return null;
     }
     */
    
    /**
     * This method is fired when showing when priting an Order
     * It displays the the payment method-specific data.
     *
     * @param integer $_virtuemart_order_id The order ID
     * @param integer $method_id  method used for this order
     * @return mixed Null when for payment methods that were not selected, text (HTML) otherwise
     * @author Valerie Isaksen
     */
    function plgVmonShowOrderPrint($order_number, $method_id)
    {
        return $this->onShowOrderPrint($order_number, $method_id);
    }
    
    /**
     * Save updated order data to the method specific table
     *
     * @param array $_formData Form data
     * @return mixed, True on success, false on failures (the rest of the save-process will be
     * skipped!), or null when this method is not actived.
     * @author Oscar van Eijk
     
     public function plgVmOnUpdateOrder($psType, $_formData) {
     return null;
     }
     */
    /**
     * Save updated orderline data to the method specific table
     *
     * @param array $_formData Form data
     * @return mixed, True on success, false on failures (the rest of the save-process will be
     * skipped!), or null when this method is not actived.
     * @author Oscar van Eijk
     
     public function plgVmOnUpdateOrderLine($psType, $_formData) {
     return null;
     }
     */
    /**
     * plgVmOnEditOrderLineBE
     * This method is fired when editing the order line details in the backend.
     * It can be used to add line specific package codes
     *
     * @param integer $_orderId The order ID
     * @param integer $_lineId
     * @return mixed Null for method that aren't active, text (HTML) otherwise
     * @author Oscar van Eijk
     
     public function plgVmOnEditOrderLineBE($psType, $_orderId, $_lineId) {
     return null;
     }
     */
    /**
     * This method is fired when showing the order details in the frontend, for every orderline.
     * It can be used to display line specific package codes, e.g. with a link to external tracking and
     * tracing systems
     *
     * @param integer $_orderId The order ID
     * @param integer $_lineId
     * @return mixed Null for method that aren't active, text (HTML) otherwise
     * @author Oscar van Eijk
     
     public function plgVmOnShowOrderLineFE($psType, $_orderId, $_lineId) {
     return null;
     }
     */
    
    /**
     * plgVmOnResponseReceived
     * This event is fired when the  method returns to the shop after the transaction
     *
     *  the method itself should send in the URL the parameters needed
     * NOTE for Plugin developers:
     *  If the plugin is NOT actually executed (not the selected payment method), this method must return NULL
     *
     * @param int $virtuemart_order_id : should return the virtuemart_order_id
     * @param text $html: the html to display
     * @return mixed Null when this method was not selected, otherwise the true or false
     *
     * @author Valerie Isaksen
     *
     
     function plgVmOnResponseReceived($psType, &$virtuemart_order_id, &$html) {
     return null;
     }
     */
    function plgVmDeclarePluginParamsShipment($name, $id, &$data)
    {
		
        return $this->declarePluginParams('shipment', $name, $id, $data);
    }
    
	function plgVmDeclarePluginParamsShipmentVM3 (&$data) {
		return $this->declarePluginParams ('shipment', $data);
	}

	
    function plgVmSetOnTablePluginParamsShipment($name, $id, &$table)
    {
        return $this->setOnTablePluginParams($name, $id, $table);
    }
public function plgVmSetOnTablePluginShipment(&$data, &$table)
	{
		$cache_d = JPATH_SITE.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'postnord'; 
		
		if (file_exists($cache_d))
		{
			jimport( 'joomla.filesystem.folder' );
			JFolder::delete($cache_d); 
		}
		
	
		
	}

    
}

// No closing tag
