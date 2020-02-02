<?php

defined('_JEXEC') or die('Restricted access');

	if (!class_exists('vmPSPlugin'))
    require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR. 'vmpsplugin.php');

class plgVmShipmentRupostel_coolrunner extends vmPSPlugin
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
        return $this->createTableSQL('rupostel_coolrunner');
    }
    
	function checkMethod($method_id)
	{
		$method_id = (int)$method_id; 
		$db = JFactory::getDBO(); 
	$q = 'select `params` from `#__modules` where `module` = "mod_coolrunner" and `published` = 1';
	$q = $db->setQuery($q); 
	$res = $db->loadResult(); 
	
	if (empty($res)) return false; 
	$params = new JRegistry($res); 
		$shipping_methods = $params->get('shipping_methods', ''); 
$db = JFactory::getDBO(); 
$q = 'select `virtuemart_shipmentmethod_id` from `#__virtuemart_shipmentmethods` where `shipment_element` = "rupostel_coolrunner" and `published` = 1'; 
$res = array(); 
try
{
$db->setQuery($q); 
$res = $db->loadAssocList(); 



if (empty($res)) $res = array(); 
}
catch (Exception $e)
{

	// do nothing... 
}


$ex = explode(',',$shipping_methods); 
if (is_array($ex))
{	
foreach ($ex as $s)
{
	$s = trim($s); 
	$a = array(); $a['virtuemart_shipmentmethod_id'] = (int)$s; 
	$res[] = $a; 
}
}

foreach ($res as $row)
{
	$id = (int)$row['virtuemart_shipmentmethod_id']; 
	if ($id === $method_id) return true; 
}

return false; 

	}
	
	
    function getTableSQLFields()
    {
        $SQLfields = array(
            'id' => 'int(11) UNSIGNED NOT NULL AUTO_INCREMENT',
            'virtuemart_order_id' => 'int(11) UNSIGNED',
            'virtuemart_shipmentmethod_id' => 'mediumint(1) UNSIGNED',
            'order_number' => 'char(32)',
			'service_id' => 'varchar(10)',
            'coolrunner_packet_id' => 'INT(11)',
            'coolrunner_packet_price' => 'DECIMAL(15,2)',
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
            'shipment_name' => 'text',            
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
		
        //$this->onShowOrderFE($virtuemart_order_id, $virtuemart_shipmentmethod_id, $shipment_name);
		if (empty($shipment_name)) $shipment_name = ''; 
		$shipment_name .= $this->renderData($virtuemart_order_id); 
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
    
	
	public function renderData($order_id)
	{
		
		if (empty($this->_tablename)) return; 
		
		$q = 'select * from `'.$this->_tablename.'` where virtuemart_order_id = '.(int)$order_id.' limit 0,1'; 
		$db = JFactory::getDBO(); 
		$db->setQuery($q); 
		$data = $db->loadAssoc(); 
		
		
		if (!empty($data))
		{
			$data['branch'] = json_decode($data['shipment_name'], true); 
			if (!empty($data['branch']))
			{
			  return $this->renderByLayout('order', array('data'=>$data)); 
			}
		}
		return ''; 
		
	}
	
    function plgVmConfirmedOrder(VirtueMartCart $cart, $order)
    {
		if (!$this->checkMethod($order['details']['BT']->virtuemart_shipmentmethod_id)) return; 
        if (!($method = $this->getVmPluginMethod($order['details']['BT']->virtuemart_shipmentmethod_id))) {
          //  return null; // Another method was selected, do nothing
        }
        if (!$this->selectedThisElement($method->shipment_element)) {
           // return false;
        }
		
		
		
        if (!$this->OnSelectCheck($cart)) {
            //return false;
        }
		
		$session = JFactory::getSession(); 
		$b = $branchJson = JRequest::getVar('coolrunner_pobocky', $session->get('coolrunner_pobocka', '')); 
		
		
		
		if (empty($branchJson)) return; 
		
		$session->set('coolrunner_pobocka', $branchJson );
		
		$branch_data = json_decode($branchJson, true); 
		if (empty($branch_data)) return; 
		
		
		$branch_data['json'] = $branchJson; 
		$values = $this->getValues2Store($order, $branch_data, 0, 0, 0, $this); 
		
		
		
		
		
        $ret = $this->storePSPluginInternalData($values);
		
		if (!empty($method->status_register))
		if (in_array($order['details']['BT']->order_status, $method->status_register))
		{
			$this->_register($method, $values); 
		}
		
        return true;
    }
	public function plgVmOnAjaxRupostel_coolrunner()
	{
		
	}
	public function onAjaxRupostel_coolrunner()
	{
		
		return;
		$db = JFactory::getDBO(); 
		$q = 'select * from `'.$this->_tablename.'` where virtuemart_order_id = '.(int)$order_id.' limit 0,1'; 
		$db->setQuery($q); 
		$res = $db->loadAssoc(); 
		
		if (!empty($res['label']))
		{
			$data = $res['label']; 
			$data_json = json_decode($data); 
		
			if (!empty($data_json['data']['labels']))
			{
				
			}
		}
		die(); 
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
	public function getValues2Store($order, $b, $order_weight=0, $service_id=0, $cost=0, $method)
	{
		if (is_array($b)) $b = (object)$b; 
		if (is_array($b->address)) $b->address = (object)$b->address; 
		
		$branch_id = $b->droppoint_id; 
		
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
				$ids[$row['virtuemart_product_id']] = (float)ShopFunctions::convertWeightUnit ($row['product_weight'], $row['product_weight_uom'], 'KGS');
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
		
        $values['coolrunner_packet_id']         = 0;
        $values['coolrunner_packet_price']      = $order['details']['BT']->order_total;
		$session = JFactory::getSession(); 
        $values['branch_id']                    = $branch_id; 
		
		
		if (!empty($branch_id))
		{
		
		 $ptext = $b->json; 
		}
		
		
        $values['branch_name_street']           = $b->address->street;
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
        $values['shipment_name']                = $b->json;              
        $values['shipment_cost']                = $cost;
        $values['tax_id']                       = 0;
		$values['service_id'] = 0; 
		return $values;  
	}
	
	public function plgVmOnUpdateOrderBEShipment($order_id)
	{
		
		return; 
	
	}
	
	private function _register($method, $values, $service_id=0, $branch_id=null, $cash_on_delivery=0)
	{
		return; 
	
		 
	}
	
	public function plgVmOnUpdateOrderShipment(&$data,$old_order_status)
	{
		
		return; 
		if (!is_object($data)) return;
		
		
		if (!($method = $this->getVmPluginMethod($data->virtuemart_shipmentmethod_id))) {
            return null; // Another method was selected, do nothing
        }
        if (!$this->selectedThisElement($method->shipment_element)) {
            return false;
        }
		
		$status = $data->order_status; 
		$register = $method->status_register; 
		if (!empty($register))
		if (in_array($status, $register))
		{
			$nd = (array)$data; 
			$this->_register($method, $nd); 
		}
		
		
		
		
	}
    
    
  
  
    /**    
     * @return delivery cost for the shipping method instance
     * @author Zasilkovna
     */
    
    function getCosts(VirtueMartCart $cart, $method, $cart_prices, $pobocka=0)
	{     
		return 0; 	
        
    }
    
	
    /** TODO
    * Here can add check if user has filled in valid phone number or mail so he is reachable by zasilkovna
    */
    protected function checkConditions($cart, $method, $cart_prices)
    {
		return true; 
	   

	   
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
	   
	    $session = JFactory::getSession(); 
		$b = $branchJson = JRequest::getVar('coolrunner_pobocky', $session->get('coolrunner_pobocka', '')); 
		
		
		
		if (empty($branchJson)) return; 
		
		$session->set('coolrunner_pobocka', $branchJson );
		
		
		return null; 
		
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
		return; 
		static $done; 
		if (!empty($done)) return; 
		
        $document = JFactory::getDocument(); 
        foreach ($this->methods as $key => $method) { 
		    
			$hm = '<input type="hidden" name="coolrunner_pobocky" id="coolrunner_pobocky" value="" />'; 
			$html[] = $hm; 
		
		}
		
		$done = true; 
		
		
        $htmlIn[] = $html;
        return TRUE;
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
	   return; 

	 
	  
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

	if (!$this->checkMethod($virtuemart_shipmentmethod_id)) return NULL; 
    if (!($this->_selectedThisByMethodId ($virtuemart_shipmentmethod_id))) {
    //  return NULL;
    }
    $html = $this->getOrderShipmentHtml ($virtuemart_order_id);
	if (!empty($html)) return $html; 
    return NULL;
  }

  /**
   * @param $virtuemart_order_id
   * @return string
   * @author zasilkovna
   */
  function getOrderShipmentHtml ($virtuemart_order_id) {

    return $this->renderData($virtuemart_order_id); 
	
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
    $html .= $this->getHtmlRowBE ('BRANCH', $shipinfo->branch_name_street);   
    $html .= $this->getHtmlRowBE ('CURRENCY', $shipinfo->branch_currency);       

    $html .= '</table>' . "\n";

    return $html;
  }    
    private function getSelectedPobocka($order_id=0)
	 {
		
	   $session = JFactory::getSession(); 
	   //$ret = JRequest::getVar('coolrunner_pobocka', $session->get('coolrunner_pobocka', '')); 
	   $ret = JRequest::getVar('coolrunner_pobocky', $session->get('coolrunner_pobocka', '')); 
	   
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
	 public function getSelectedPobockaPrice($method)
	 {
		 $id = JRequest::getVar('coolrunner_pobocka', 0); 
		 
		 $id2 = JRequest::getVar('opc_shipment_price_id'); 
		 if (empty($id) && (!empty($id2)))
		 {
			 $id = $id2; 
		 }
		 
		 return $this->parseId($id, $method); 
	 }
	 
    public function plgVmonSelectedCalculatePriceShipment(VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name)
    {        
		return NULL;
		
		
    }
    
	
	function plgVmOnCheckoutCheckDataShipment($cart)
	{
	   if (!($method = $this->getVmPluginMethod($cart->virtuemart_shipmentmethod_id))) {
            return null; // Another method was selected, do nothing
        }
		
		if (!$this->selectedThisElement($method->shipment_element)) {
            return null;
        }

		
	
		  return null; 
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
		
	}

    
}

// No closing tag
