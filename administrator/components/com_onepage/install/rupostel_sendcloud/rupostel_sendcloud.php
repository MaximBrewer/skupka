<?php

defined('_JEXEC') or die('Restricted access');

	if (!class_exists('vmPSPlugin'))
    require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR. 'vmpsplugin.php');

class plgVmShipmentRupostel_sendcloud extends vmPSPlugin
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
		
		require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php'); 		
        

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
        return $this->createTableSQL('rupostel_sendcloud');
    }
    
    function getTableSQLFields()
    {
        $SQLfields = array(
            'id' => 'int(11) UNSIGNED NOT NULL AUTO_INCREMENT',
            'virtuemart_order_id' => 'int(11) UNSIGNED',
            'virtuemart_shipmentmethod_id' => 'mediumint(1) UNSIGNED',
            'order_number' => 'char(32)',
			'service_id' => 'varchar(10)',
            'sendcloud_packet_id' => 'INT(11)',
            'sendcloud_packet_price' => 'DECIMAL(15,2)',
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
    
    private function addOrder($order_id)
	{
		$method = SendcloudHelper::getFirstMethod(); 
		if (empty($method)) return; 
		
		$order_id = (int)$order_id; 
		$orderModel = VmModel::getModel('orders'); 
		$order = $orderModel->getOrder($order_id); 
		$branch_id = $w = 0; 
		$values = $this->getValues2Store($order, $branch_id, 0, 0, 0, $method); 
		 $ret = $this->storePSPluginInternalData($values);
	}
    
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
		$branch_id = $id = $method->sendcloudshipping; 
		$shipping = SendcloudHelper::getShippingMethods($method, $id); 
		
		$w = $this->getOrderWeight ($cart, $method->weight_unit);
		$cost = $this->getCosts ($cart, $method, "");                
		$values = $this->getValues2Store($order, $branch_id, $w, 0, $cost, $method); 
		
		
        $ret = $this->storePSPluginInternalData($values);
		
		if (!empty($method->status_register))
		if (in_array($order['details']['BT']->order_status, $method->status_register))
		{
			if (!empty($method->reqshipping))
			{
				$values['requestShipment'] = true; 
			}
			$this->_register($method, $values); 
		}
		
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
		
		
		if (empty($values['registeredtime']))
		$values['registeredtime'] = ''; 
		if (empty($values['sendcloud_packet_id']))
        $values['sendcloud_packet_id']         = 0;
	
        $values['sendcloud_packet_price']      = $order['details']['BT']->order_total;
		$session = JFactory::getSession(); 
        
		$values['branch_id']                    = $branch_id; 
		
		require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php'); 
		
		if (!empty($branch_id))
		{
		
		$pobocka = SendcloudHelper::getShippingMethods($method, $values['branch_id']); 
		
		
		
		 $ptext = $pobocka['name'].'('.$pobocka['price'].')'; 
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
		  
		  
		  $q = 'select country_3_code from #__virtuemart_countries where virtuemart_country_id = '.(int)$country_id.' limit 0,1'; 
		  $db = JFactory::getDBO(); 
		  $db->setQuery($q); 
		  $country_iso3 = $db->loadResult(); 
		  $values['branch_currency']              = 'EUR'; 
		  
		  if (empty($country_iso3)) $country_iso3 = 'CZE'; 
		  if ($country_iso3 == 'CZE')
		  {
			  $values['branch_currency']              = 'CZK'; 
			  
		  }
		  
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
		
		if (!isset($method->shipment_name))
		{
			$method->shipment_name = 'SendCloud'; 
		}
		
        $values['is_cod']                       = $cod; //depends on actual settings of COD payments until its set manually in administration
        $values['exported']                = 0;        
        $values['shipment_name']                = $method->shipment_name.': '.$ptext;             
        $values['shipment_cost']                = $cost;
        $values['tax_id']                       = $method->tax_id;
		$values['service_id'] = 0; 
		return $values;  
	}
	
	private function getMethod($order_id)
	{
		$orderModel = VmModel::getModel('orders'); 
		$order = $orderModel->getOrder($order_id); 
		
		$method_id2 = $order['details']['BT']->virtuemart_shipmentmethod_id; 
		$method_id2 = $order['details']['BT']->virtuemart_shipmentmethod_id; 
		
		$method = SendcloudHelper::getFirstMethod($method_id2); 
		
		if (empty($method)) {
		 $method = SendcloudHelper::getFirstMethod(); 
		 if (empty($method)) return; 
		 $this->addOrder($order_id); 
		}
		
		return $method; 
	}
	
	public function plgVmOnUpdateOrderBEShipment($order_id)
	{
		
	    require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php'); 		
		
		$msg_u = ''; 
		$orderModel = VmModel::getModel('orders'); 
		$order = $orderModel->getOrder($order_id); 
		
		
		$method_id2 = $order['details']['BT']->virtuemart_shipmentmethod_id; 
		
		$country_id = $order['details']['BT']->virtuemart_country_id; 
		
		$method = $this->getMethod($order_id); 
		
		
		
		if (empty($method)) return; 
		
		
		
		$csl = SendcloudHelper::getShippingMethods($method); 
		
		
		
		if (empty($csl)) return;
		
		foreach ($csl as $cs)
		{
		 if (isset($cs['countries'][$country_id])) $ok = true; 
		}
		
		
		if (empty($ok)) return; 
		
		$x = JRequest::getVar('process_sendcloud'); 
		if (!empty($x))
		{
			
		   $values = array(); 
		   $values['virtuemart_order_id'] = $order_id; 
		   
		   
		   
		   $service_id = (int)JRequest::getVar('sendcloud_service'); 
		   
		   if (!empty($service_id))
		   {
		     $branch_id = $service_id; 
		   
			

		    $values = $this->getValues2Store($order, $branch_id, 0, $service_id, 0, $method); 
			
			
			$ret = $this->storePSPluginInternalData($values);
			
			
			$price = 0; 
			
			
			
			
		    $ret = $this->_register($method, $values, $service_id, $branch_id, $price); 
		    if ($ret === true)
			{
				JFactory::getApplication()->enqueueMessage('Package got registered at Sendcloud.nl and the Labels were created.'); 
			}
		   }
		}
		
		 if ((!empty($order['details']['ST'])) && (!empty($order['details']['ST']->virtuemart_country_id)))
		  {
			  $address = $order['details']['ST']; 
		  }
		  else
		  {
			  $address = $order['details']['BT']; 
		  }
		  
		  $country_id = $address->virtuemart_country_id; 
		   $q = 'select country_3_code from #__virtuemart_countries where virtuemart_country_id = '.(int)$country_id.' limit 0,1'; 
		  $db = JFactory::getDBO(); 
		  $db->setQuery($q); 
		  $country_iso3 = $db->loadResult(); 
		  
		  
		$address_state = $country_iso3; 
		$order_total = $order['details']['BT']->order_total; 
		 $currency_id = $address->order_currency;
		 
		 $q = 'select currency_code_3 from #__virtuemart_currencies where virtuemart_currency_id = '.(int)$currency_id.' limit 0,1'; 
		  $db->setQuery($q); 
		  $currency_iso = $db->loadResult(); 
		 
		  $currencyDisplay = CurrencyDisplay::getInstance($currency_id);
		
		
		
		$res = $this->getData($order_id); 
		
		$safe_path = VmConfig::get('safe_path'); 
		$ret = ''; 		
		
		
		
		if (!empty($res['sendcloud_packet_id']))
		{
			$parcel_id = $res['sendcloud_packet_id']; 
			$ret .= 'Internal Parcel ID: '.$parcel_id.'<br />'; 
			//$pdf = sendCloudHelper::getLabels($method, $parcel_id); 
			
			 $parcelData = SendcloudHelper::getParcel($method, $res['sendcloud_packet_id']); 
			
			if (!empty($parcelData['label']))
			{
				
			$pdf = $parcelData['label']; 
			}
			else
			{
				
				$pdf = sendCloudHelper::getLabels($method, $parcel_id, true);
				
				$parcelData = SendcloudHelper::getParcel($method, $res['sendcloud_packet_id']); 
			}
			/*
			 if (stripos('No Label', $parcelData['status']['message']) !== false)
			 {
				 SendcloudHelper::getLabels($method, $res['sendcloud_packet_id'], true); 
			 }
			 */
			
			if (!empty($parcelData['status']['message']))
			{
		    if (stripos('No label', $parcelData['status']['message']) === false)
			if (!empty($pdf))
			{
				
			if (isset($pdf['label'])) {
			foreach ($pdf['label'] as $k=>$v)
			{
			foreach ($v['normal_printer'] as $k=>$link)
			{
					$ret .= '<a class="normal_printer_pdf" href="'.$this->adjustLink($method, $link).'">PDF_'.$k.'<i class="icon-large icon-print"></i></a><br />'; 
			}
			if (!empty($pdf['label_printer']))
			{
					$ret .= '<a class="label_printer_pdf" href="'.$this->adjustLink($method, $pdf['label_printer']).'">PDF <i class="icon-large icon-print"></i></a><br />'; 
			}
			}
			
			
			}
			else
			if (isset($pdf['normal_printer']))
			{
				foreach ($pdf['normal_printer'] as $k=>$link)
			{
					$ret .= '<a class="normal_printer_pdf" href="'.$this->adjustLink($method, $link).'">PDF_'.$k.'<i class="icon-large icon-print"></i></a><br />'; 
			}
			if (!empty($pdf['label_printer']))
			{
					$ret .= '<a class="label_printer_pdf" href="'.$this->adjustLink($method, $pdf['label_printer']).'">PDF <i class="icon-large icon-print"></i></a><br />'; 
			}
			}
			}
			}
			
			
			 
			 if (!empty($parcelData['tracking_number']))
			 {
			 $msg_u  .= 'Tracking Number: '.$parcelData['tracking_number'].'<br />'; 
			 
			 $has_tracking_num = true; 
			 
			 }
			 if (!empty($parcelData['status']['message']))
			 {
			  //$no_checkbox = true; 
			  $has_tracking_num = true; 
			 $msg_u  .= 'Status: '.$parcelData['status']['message'].'<br />'; 
			 }
			
		}
		
		
		{
		 require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php'); 
		 $services = SendcloudHelper::getShippingMethods($method); 
		 
		 $data = $this->getData($order_id); 
		 $sbranch_id = 0; 
		
		 
		 if (!empty($data['branch_id']))
		 {
			 $sbranch_id = (int)$data['branch_id']; 
		 }
		 
		
		 
		 $select = '<select name="sendcloud_service" '; 
		 if (!empty($has_tracking_num)) $select .= ' readonly="readonly" disabled="disabled" '; 
		 $select .= ' >'; 
		 $service_branches = array(); 
		 
		 $data = $this->getData($order_id); 
		 foreach ($services as $k=>$s)
		 {
			
			 $selected_service = false; 
		
			 
						
			  $select .= '<option value="'.$s['id'].'" '; 
			 if (($s['id'] == $sbranch_id))
			 {
				 $select .= ' selected="selected" '; 
			 }
			 $select .= ' >'.htmlentities($s['name']).'</option>'; 
			 
		 }
		 $select .= '</select>'; 
		 
		 
		 
		 
		 
		}
		 
		
		 
		 $ret .= $select; 
		 $script = "
<script>

 
 function submitSendcloud(el)
 {
	 var container = document.getElementById('sendcloud_form'); 
	 var input = document.createElement('input');
	 input.type = 'hidden';
	 input.value='1'; 
	 input.name='process_sendcloud'; 
	
     container.appendChild(input); // put it into the DOM
	
	 document.adminFormSendCloud.submit(); 
 
	 return false; 
 }
 
</script>		 
		 
		 "; 
		 $ret .= $script; 
		 if (!empty($msg_u))
		 {
			 $msg_u = '<div style="width: 100%; clear: both; background-color: green; color: white;">'.$msg_u.'</div>'; 
		 }
		 else $msg_u = ''; 
		 
		 $dobierka = '<label for="request_shipping"><input type="checkbox" id="request_shipping" name="request_shipping" value="1" />Request Shipping (tracking ID)</label>'; 
		 
		 if (!empty($no_checkbox))
		 {
			 $dobierka = '<input type="hidden" id="request_shipping" name="request_shipping" value="1" >'; 
		 }
		 
		 $submit = '<input type="button" value="Register Parcel" onclick="return submitSendcloud(this);" />'; 
		 
		 if ((!empty($has_tracking_num)) || (!empty($no_checkbox))) {
		  //$dobierka = $submit = ''; 
		 }
		 
		$html = '<form action="index.php" method="post" id="adminFormSendCloud" name="adminFormSendCloud">
		<fieldset style="float: left; clear:both;"><legend>SendCloud</legend>'.$msg_u.$ret.'<div id="sendcloud_form" >'.$dobierka.$submit.'</div></fieldset>
		
		<input type="hidden" name="task" value="edit" />
		<input type="hidden" name="option" value="com_virtuemart" />
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="virtuemart_order_id" value="'.(int)$order_id.'" />
		
		
		'.JHtml::_('form.token').'
		
		
		</form>'; 
		
		
		return $html; 
	}
	private function _register($method, $values, $service_id=0, $branch_id=null, $cash_on_delivery=0)
	{
		
		
		
		$order_id = $values['virtuemart_order_id']; 
		if (empty($order_id)) return; 
		
		$data2 = $this->getData($order_id); 
		foreach ($data2 as $k=>$v)
		{
			if (empty($values[$k])) $values[$k] = $data2[$k]; 
		}
		
		require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php'); 
		
		
			
		
		
		$orderModel = VmModel::getModel('orders'); 
		  $order = $orderModel->getOrder($order_id); 
		
		
		  if ((!empty($order['details']['ST'])) && (!empty($order['details']['ST']->virtuemart_country_id)))
		  {
			  $address = $order['details']['ST']; 
		  }
		  else
		  {
			  $address = $order['details']['BT']; 
		  }
		  
		  
		  
		  $country_id = $address->virtuemart_country_id; 
		  
		  
		  $q = 'select `country_2_code` from `#__virtuemart_countries` where `virtuemart_country_id` = '.(int)$country_id.' limit 0,1'; 
		  $db = JFactory::getDBO(); 
		  $db->setQuery($q); 
		  $country_iso2 = $db->loadResult(); 
		  
		  $country_iso2 = strtoupper($country_iso2); 
		  
		  $address_state = $country_iso2; 
		  
		  $q = 'select * from `'.$this->_tablename.'` where virtuemart_order_id = '.(int)$order_id.' limit 0,1'; 
		
		  $db->setQuery($q); 
		  $orderShippingData = $db->loadAssoc(); 
		  
		   
		  if (empty($service_id))
		  {
		  if (empty($orderShippingData)) 
		  {
		    $service_id = 8; 	  
		  }
		  else
		  {
			  $service_id = (int)$orderShippingData['branch_id']; 
		  }
		  }
		  
		  
		  
		  
		  
		 
		  
		  
		
		  $order_number = $address->order_number; 
		  
		  if (isset($address->first_name))
		  $customer_name = $address->first_name; 

		  if (isset($address->middle_name))
		  $customer_name .= ' '.$address->middle_name; 

	  
		  if (isset($address->last_name))
		  $customer_name .= ' '.$address->last_name; 
		  
		  $company_name = ''; 
		  
		  if (isset($address->company))
		  $company_name = $address->company; 
			else
		  if (isset($order['details']['BT']->company))
		  $company_name = $order['details']['BT']->company;
		  
		  $address_street = ''; 
		  if (isset($address->address_1))
		  $address_street = $address->address_1; 
	      if (isset($address->address_2))
		  $address_street .= ' '.$address->address_2; 
		  
		  $address_zip = $address->zip; 
		  
		  $address_town = $address->city; 
		  
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
		
		  $customer_email = $order['details']['BT']->email; 
		  
		  $payment_id = $address->virtuemart_paymentmethod_id; 
		  
		  if (empty($cash_on_delivery))
		  $cash_on_delivery = 0; 
		  
		  
		  $weight = $orderShippingData['order_weight']; 
		  $parcel_count = $this->getParcelCount( $method, $weight); 
		  
		  $currency_id = $address->order_currency;
		  $currencyDisplay = CurrencyDisplay::getInstance($currency_id);
		  
		  $q = 'select currency_code_3 from #__virtuemart_currencies where virtuemart_currency_id = '.(int)$currency_id.' limit 0,1'; 
		  $db->setQuery($q); 
		  $currency_iso = $db->loadResult(); 
		  
		  
		  
		  
		  if (empty($cash_on_delivery))
		  if (!empty($method->cod_payments))
		  if (in_array($payment_id, $method->cod_payments))
		  {
			  $cash_on_delivery = number_format($address->order_total, 2, '.', ''); 
		  }
		  if (!empty($values['requestShipment']))
		  {
			  $req_tracking = true; 
		  }
		  else
		  {
		   $req_tracking = JRequest::getVar('request_shipping', false); 
		   if (!empty($req_tracking)) $req_tracking = true; 
		  }
		  $currency = $currency_iso; 
		  $password = ''; 
		  
		  $transport_service_id = 1; 
		 
		  
		  if (!empty($service_id)) $transport_service_id = $service_id; 
		  
		  $values['service_id'] = $transport_service_id; 
		  
		 
		  $myData = array(
				'name'=> $customer_name,
				'company_name' => $company_name,
				'address' => $address_street,
				'city' => $address_town,
				'postal_code' => $address_zip,
				'telephone' => $customer_phone,
				'requestShipment' => $req_tracking, 
				'email' => $customer_email,
				'country' => $country_iso2,
				'shipment' => array(
					'id' => $service_id
				),
				'order_number' => $order_number
			
			);
		
		
		
		
				
				
				if (!isset($myData['data']))
				{
					$myData['data'] = array(); 
				}
				if (!isset($myData['shipment']['options']))
				{
					$myData['shipment']['options'] = array(); 
				}
				
				if (!is_numeric($myData['order_number']))
				{
					 
    
				}
				
				
				
				
					 
    $num = preg_replace("/[^0-9]/", "", $myData['telephone']);
	if ($num !== false) $myData['telephone'] = $num; 
	
	
	$myData['telephone'] = (int)$myData['telephone']; 
	$myData['requestShipment'] = (bool)$myData['requestShipment']; 
				

				
			
		  
		  require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php'); 
		  
		 $method_id = $order['details']['BT']->virtuemart_shipmentmethod_id; 
		 
		 static $oncePerRequest; 
		 if (!empty($oncePerRequest)) return; 
		
		 if (!empty($values['sendcloud_packet_id']))
		 {
			 $values['sendcloud_packet_id'] = $parcel_id = (int)$values['sendcloud_packet_id']; 
			 //$parcelData = SendcloudHelper::getParcel($method, $values['sendcloud_packet_id']); 
			 
			$oncePerRequest = true; 
			 
			 $parcelData = SendcloudHelper::changeParcel($method, $parcel_id, $myData); 

			
		 }
		 else
		 {
			$oncePerRequest = true; 
		  $data = SendcloudHelper::registerParcel($method, $myData); 
		  
		  
		  
		  
		  if ((!empty($data)) && (isset($data['id'])))
		  {
			
		  
		 
		  $values['sendcloud_packet_id'] = $data['id']; 
		  $ret = $this->storePSPluginInternalData($values);
		  
		  
		  }
		 }
		  
		  
		 
		 
	}
	public function adjustLink($method, $link) {
	  if (stripos('@', $link)!==false) return $link; 
	  
	  $link = str_replace('://', '://'.$method->api_key.':'.$method->api_secret.'@', $link); 
	  return $link; 
	  
	}
	public function plgVmOnUpdateOrderShipment(&$data,$old_order_status)
	{
		
		
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
    
    
    function getCosts(VirtueMartCart $cart, $method, $cart_prices, $pobocka=0)
	{                   
        //get actual display currency. in $cart->pricesCurrency its not updated immediately.. but if we dont use change currency,   getUserStateFromRequest doesnt return anything.. so then use cart pricesCurrency
        $currency = CurrencyDisplay::getInstance ();            
		$app = JFactory::getApplication(); 
        $pricesCurrencyId = $app->getUserStateFromRequest( 'virtuemart_currency_id', 'virtuemart_currency_id',$currency->_vendorCurrency );   
		
        if(empty($pricesCurrencyId)){//this is pretty weird
          $pricesCurrencyId=$cart->pricesCurrency;
        }        
        
		require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php'); 
		
		if (empty($method->single_price)) {
		$id = $method->sendcloudshipping; 
		$shipping = SendcloudHelper::getShippingMethods($method, $id); 
		
		$address = $this->getAddress($cart); 
		
		$value = 0; 
		if (!empty($address['virtuemart_country_id']))
		{
		$country = $address['virtuemart_country_id']; 
		if (isset($shipping['countries'][$country]))
		{
			$value = $shipping['countries'][$country]['price']; 
			
		}
		}
		if ((empty($value)) && (isset($shipping['price'])))
		{
			$value = $shipping['price']; 
		}
		
		$price = $value; 
		}
		else
		{
			$p = floatval($method->single_price); 
			$price = $p; 
		}
		
        
		
		
		if (empty($cart->pricesUnformatted))
		{
			$cart->pricesUnformatted = $cart->cartPrices; 
		}
		if (isset($cart_prices['salesPrice'])) {
		 $total = $cart_prices['salesPrice']; 
		}
        else
		{
		$total = $cart->pricesUnformatted['billTotal']; 
		if (!empty($cart->pricesUnformatted['salesPriceShipment']))
		$total -= (float)$cart->pricesUnformatted['salesPriceShipment']; 
		
		if (!empty($cart->pricesUnformatted['salesPricePayment']))
		$total -= (float)$cart->pricesUnformatted['salesPricePayment']; 
		}
		if (!empty($method->free_start_sendcloud))
		{
		  $method->free_start_sendcloud = (float)$method->free_start_sendcloud; 
		  if (!empty($method->free_start_sendcloud))
		  if ($total >= $method->free_start_sendcloud)
		    {
			
			  return 0;    
			}
		}
		
	
		
		if (!empty($method->cod_payments))
		if (!empty($cart->virtuemart_paymentmethod_id))
		{
		   
		   if (in_array($cart->virtuemart_paymentmethod_id, $method->cod_payments))
		   if (!empty($method->code_price))
		   {
			   $cod = floatval($method->code_price); 
		     $price += $cod; 
		   }
		   
		} 
		
		
		$orderWeight = $this->getOrderWeight ($cart, $method->weight_unit);
		if (!empty($method->weight_stop))
		if ($orderWeight > $method->weight_stop)
		{
		   $w = (float)$method->weight_stop; 
		   $times = $orderWeight / $w; 
		   $c = ceil($times); 
		   $price = $price * $c; 
		}
		$app = JFactory::getApplication(); 
		
		return $price;  
		
    }
    
	private function getParcelCount($method, $weight)
	{
		$w = (float)$method->weight_stop; 
		 $times = $weight / $w; 
		  $c = ceil($times); 
		  return (int)$c; 
	}
	
	private function getAddress($cart)
	{
		if (method_exists($cart, 'getST'))
		{
		if($cart->STsameAsBT == 0){
			$type = ($cart->ST == 0 ) ? 'BT' : 'ST';
		} else {
			$type = 'BT';
		}

		$address = $cart->getST();
		}
		else
		{
	     $address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
        }
		return $address; 
	}
    
    protected function checkConditions($cart, $method, $cart_prices)
    {
	  $address = $this->getAddress($cart); 
	    
		
	  $wstart = (float)$method->weight_start;
	  
      $ws = (float)$method->weight_stop;
      $orderWeight = $this->getOrderWeight ($cart, $method->weight_unit);
	  
	  if (!empty($wstart))
	  if ($orderWeight < $wstart) return false; 
	  
	  // do not allow oversize packages
	  if (!empty($ws))
      if (empty($method->strategy))
	  if ($orderWeight > $ws) return false; 
	  
	  if (!empty($ws))
      if (!empty($method->strategy))
	  {
	  

		if(count($cart->products)>0) {
			foreach ($cart->products as $product) {

				$weight = ShopFunctions::convertWeightUnit ($product->product_weight, $product->product_weight_uom, $method->weight_unit) ;
				// single product weight is larger then x: 
				if ($weight > $ws) return false; 
			}
		}

	  
	  }
	  
	  if (isset($cart_prices['salesPrice'])) {
		 $total = $cart_prices['salesPrice']; 
		}
        else
		{
	  $total = (int)$cart->pricesUnformatted['billTotal']; 
		}
		
	  
	  if (!empty($method->orderamount_start))
	   {
	     $st = (float)$method->orderamount_start; 
		 if (($total < $st)) return false; 
	   }

	   if (!empty($method->orderamount_stop))
	   {
	     $st = (float)$method->orderamount_stop; 
		 if (($total < $st)) return false; 
	   }
	  
	  $id = (int)$method->sendcloudshipping; 
	  
	  if (empty($id)) return false; 
	  
	  
	  
	  
	  $shipping = SendcloudHelper::getShippingMethods($method, $id); 
	  
	   if (isset($address['virtuemart_country_id']))
	   {
	  		$countries = array();
		if (!empty($method->countries)) {
			if (!is_array ($method->countries)) {
				$countries[0] = $method->countries;
			} else {
				$countries = $method->countries;
			}
			
			if (!in_array ($address['virtuemart_country_id'], $countries)) {
			 return false; 
			}
			
		}
	   }

	
	  
	  if (isset($address['virtuemart_country_id']))
	  if (!isset($shipping['countries'][$address['virtuemart_country_id']]))
	  {
		  
		  return false; 
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
		$method = $this->getVmPluginMethod($cart->virtuemart_shipmentmethod_id); 
		
		if (empty($method)) {
			return NULL; // Another method was selected, do nothing
		}

		if (empty($cart->pricesUnformatted))
		{
			$cart->pricesUnformatted = $cart->cartPrices; 
		}
		
		if (!$this->checkConditions($cart, $method, $cart->pricesUnformatted)) return false; 
		
		
       
    }
	
    /**
     * plgVmDisplayListFE
     */
    public function plgVmDisplayListFEShipment(VirtueMartCart $cart, $selected = 0, &$htmlIn)
    {
	
        $js_html = '';
        if ($this->getPluginMethods($cart->vendorId) === 0) {
                return FALSE;            
        }        

        require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php'); 
		
		
		
		$address = $this->getAddress($cart); 
		
		$html        = array();
        $method_name = $this->_psType . '_name';
        $document = JFactory::getDocument(); 
		
		$helper_file = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php'); 
		require_once($helper_file); 
		
        foreach ($this->methods as $key => $method) { 
		
		if (!$this->checkConditions($cart, $method, $cart->pricesUnformatted)) continue; 
		
		$id = $method->sendcloudshipping; 
		$shipping = SendcloudHelper::getShippingMethods($method, $id); 
		
		// the price must not be overwritten directly in the cart
				$prices = $cart->cartPrices;
				$methodSalesPrice = $this->setCartPrices ($cart, $prices ,$method);

				$method->$method_name = $this->renderPluginName ($method);
				$html [] = $this->getPluginHtml ($method, $selected, $methodSalesPrice);
		
				
		}
		
			
			
		

        if (empty($html)) {
            return FALSE;
        }
        
		
		
        $htmlIn[] = $html;
        return TRUE;
    }
	
  public function getServices($method)
  {
	  require_once(__DIR__.DIRECTORY_SEPARATOR.'SendCloudApi.php'); 
	  $api_key = $method->api_key; 
	  $api_secret = $method->api_secret; 
	  
	  $debug = $method->is_live; 
	  if (!empty($debug)) $mode = 'live'; 
	  else $mode = 'live'; 
	  try
	  {
	  $api = new SendcloudApi($mode, $api_key,$api_secret);
	  $api->setMethod('get'); 
	  $shipping_methods = $api->shipping_methods->get();
	  }
	  catch (Exception $e)
	  {
		  $msg = $e->code.': '.$e->message; 
		  JFactory::getApplication()->enqueueMessage($msg, 'error'); 
	  }
	  return array(); 

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
    //$html .= $this->getHtmlRowBE ('Shipping Method', $shipinfo->branch_name_street);   
    //$html .= $this->getHtmlRowBE ('CURRENCY', $shipinfo->branch_currency);       

    $html .= '</table>' . "\n";

    return $html;
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
		$cart_prices['cost'] = $this->getCosts($cart, $method, $cart_prices); 

		if (!$this->checkConditions ($cart, $method, $cart_prices)) {
			return FALSE;
		}

		$cart_prices_name = $this->renderPluginName ($method);
		$this->setCartPrices ($cart, $cart_prices, $method);

		return TRUE;
        
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
    
    
    function plgVmonShowOrderPrint($order_number, $method_id)
    {
        return $this->onShowOrderPrint($order_number, $method_id);
    }
    
    
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
