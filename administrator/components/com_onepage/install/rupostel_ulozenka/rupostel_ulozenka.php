<?php

defined('_JEXEC') or die('Restricted access');

	if (!class_exists('vmPSPlugin'))
    require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR. 'vmpsplugin.php');

class plgVmShipmentRupostel_ulozenka extends vmPSPlugin
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
		
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php'); 
        
    }    

    /**
     * Create the table for this plugin if it does not yet exist.
     * @author Valérie Isaksen
     */
    public function getVmPluginCreateTableSQL()
    {
        return $this->createTableSQL('rupostel_ulozenka');
    }
    
    function getTableSQLFields()
    {
        $SQLfields = array(
            'id' => 'int(11) UNSIGNED NOT NULL AUTO_INCREMENT',
            'virtuemart_order_id' => 'int(11) UNSIGNED',
            'virtuemart_shipmentmethod_id' => 'mediumint(1) UNSIGNED',
            'order_number' => 'char(32)',
			'service_id' => 'varchar(10)',
            'ulozenka_packet_id' => 'INT(11)',
            'ulozenka_packet_price' => 'DECIMAL(15,2)',
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
		$branch_id = JRequest::getVar('ulozenka_pobocky', $session->get('ulozenka_pobocka', '')); 
		$w = $this->getOrderWeight ($cart, $method->weight_unit);
		$cost = $this->getCosts ($cart, $method, "");                
		$values = $this->getValues2Store($order, $branch_id, $w, 0, $cost, $method); 
		
		
        $ret = $this->storePSPluginInternalData($values);
		
		if (!empty($method->status_register))
		if (in_array($order['details']['BT']->order_status, $method->status_register))
		{
			$this->_register($method, $values); 
		}
		
        return true;
    }
	public function plgVmOnAjaxRupostel_ulozenka()
	{
		
	}
	public function onAjaxRupostel_ulozenka()
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
		
        $values['ulozenka_packet_id']         = 0;
        $values['ulozenka_packet_price']      = $order['details']['BT']->order_total;
		$session = JFactory::getSession(); 
        $values['branch_id']                    = $branch_id; 
		
		
		if (!empty($branch_id))
		{
		
		$pobocka = UlozenkaHelper::getDataPobocky($method, $values['branch_id']); 
		
		
		if (!empty($pobocka->sk))
		$values['branch_currency']              = 'EUR'; 
		else
        $values['branch_currency']              = 'CZK'; 
		
		$ptext = $pobocka->nazev.', '.$pobocka->ulice.', '.$pobocka->obec.', '.$pobocka->psc; 
		
		$ptext = $pobocka->ulice.', '.$pobocka->obec.', '.$pobocka->psc; 
		
		if (!empty($pobocka->provoz))
		$ptext .= ' ('.$pobocka->provoz.')'; 
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
		
        $values['is_cod']                       = $cod; //depends on actual settings of COD payments until its set manually in administration
        $values['exported']                = 0;        
        $values['shipment_name']                = $method->shipment_name.': '.$ptext;             
        $values['shipment_cost']                = $cost;
        $values['tax_id']                       = $method->tax_id;
		$values['service_id'] = 0; 
		return $values;  
	}
	
	public function plgVmOnUpdateOrderBEShipment($order_id)
	{
		
		
		$db = JFactory::getDBO(); 
		$q = 'select virtuemart_shipmentmethod_id from #__virtuemart_shipmentmethods where shipment_element = \'rupostel_ulozenka\' and published = 1 limit 0,1'; 
		$db->setQuery($q); 
		$my_id = $db->loadResult(); 
		$method = $this->getVmPluginMethod($my_id); 
		
		
		
		if (empty($method)) return;
	
		$orderModel = VmModel::getModel('orders'); 
		$order = $orderModel->getOrder($order_id); 
		
		$x = JRequest::getVar('process_ulozenka'); 
		if (!empty($x))
		{
			
		   $values = array(); 
		   $values['virtuemart_order_id'] = $order_id; 
		   
		   
		   
		   $service_id = (int)JRequest::getVar('ulozenka_service'); 
		   
		   if (!empty($service_id))
		   {
		   $branch_id = JRequest::getVar('branches_'.$service_id, 0); 
		   
			

		    $values = $this->getValues2Store($order, $branch_id, 0, $service_id, 0, $method); 
			
			
			$ret = $this->storePSPluginInternalData($values);
			
			$cod = JRequest::getVar('ulozenka_dobierka', false); 
			$price = 0; 
			if (!empty($cod))
			{
				$price = JRequest::getVar('ulozenka_dobierka_price', 0); 
				
			}


			$insurance = 0; 
			$chki = JRequest::getVar('insurance_checkbox', false); 
			if (!empty($chki))
			{
				$insurance = JRequest::getVar('insurance_price', 0); 
				
			}
			
			
			
		    $ret = $this->_register($method, $values, $service_id, $branch_id, $price, $insurance); 
		    if ($ret === true)
			{
				JFactory::getApplication()->enqueueMessage('Zásielka zaregistrovaná a štítok vytvorený.'); 
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
		  
		  if (empty($country_iso3)) $country_iso3 = 'CZE'; 
		$address_state = $country_iso3; 
		$order_total = $order['details']['BT']->order_total; 
		 $currency_id = $address->order_currency;
		 
		 $q = 'select currency_code_3 from #__virtuemart_currencies where virtuemart_currency_id = '.(int)$currency_id.' limit 0,1'; 
		  $db->setQuery($q); 
		  $currency_iso = $db->loadResult(); 
		 
		  $currencyDisplay = CurrencyDisplay::getInstance($currency_id);
		if ($address_state == 'CZE')
		  {
			  if ($currency_iso != 'CZK')
			  {
				  $db = JFactory::getDBO(); 
				  $q = 'select `virtuemart_currency_id` from #__virtuemart_currencies where currency_code_3 = \'CZK\' limit 0,1'; 
				  $db->setQuery($q); 
				  $czk_id = $db->loadResult(); 
				  $address->order_total = $order_subtotal = round($currencyDisplay->convertCurrencyTo( $czk_id, $address->order_total,false));
				  $currency_iso = 'CZK'; 
			  }
			  $address->order_total = $order_subtotal = round($address->order_total); 
		  }
		  else
		  if ($address_state == 'SVK')
		  {
			  if ($currency_iso != 'EUR')
			  {
				  $db = JFactory::getDBO(); 
				  $q = 'select `virtuemart_currency_id` from #__virtuemart_currencies where currency_code_3 = \'EUR\' limit 0,1'; 
				  $db->setQuery($q); 
				  $eur_id = $db->loadResult(); 
				  $address->order_total = $order_subtotal = $currencyDisplay->convertCurrencyTo( $eur_id, $address->order_total,false);
				  $currency_iso = 'EUR'; 
			  }
			  $address->order_total = $order_subtotal = number_format($address->order_total, 2, '.', ''); 
			  
		  }
		  else {
			  
			  return; 
		  }
		
		
		$db = JFactory::getDBO(); 
		$q = 'select * from `'.$this->_tablename.'` where virtuemart_order_id = '.(int)$order_id.' limit 0,1'; 
		$db->setQuery($q); 
		$res = $db->loadAssoc(); 
		
		$safe_path = VmConfig::get('safe_path'); 
		$ret = ''; 		
		
		if (!empty($res['label']))
		{
			$data = $res['label']; 
			$data_json = json_decode($data, true); 
			if (isset($data_json['_links']))
		    if (isset($data_json['_links']['self']))
			if (isset($data_json['_links']['self']['href']))
			{
			$infoUrl = $data_json['_links']['self']['href']; 
			require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'api.php'); 
			$request = new ulozenkaApi($method);
			$data = $request->getStatus($infoUrl); 
			
			if (isset($data['status']['name']))
			{
				$msg_u = 'Ulozenka.cz: '.$data['status']['id'].': '.$data['status']['name']; 
				JFactory::getApplication()->enqueueMessage($msg_u); 
			}
			
			if (isset($data['transport_service_id']))
			{
				$selected_service_id = (int)$data['transport_service_id']; 
				
				$selected_service = true; 
			}
			
			}
			
			
			if (!empty($data_json['data']))
			{
			foreach ($data_json['data'] as $k=>$v)
			if (!empty($v['labels']))
			{
				if (!empty($v['labels']))
				{
					$labels_path = dirname(__FILE__).DIRECTORY_SEPARATOR.'labels'; 
					if (!file_exists($labels_path))
					{
						jimport( 'joomla.filesystem.folder' );
						JFolder::create($labels_path); 
						$data = ' '; 
						JFile::write($labels_path.DIRECTORY_SEPARATOR.'index.html', $data); 
					}
					$ok = false; 
					if (!empty($labels_path))
					{
						if (is_writable($labels_path))
						{
							$ok = true; 
						}
					}
					if ($ok)
					{
						
						$data = base64_decode($v['labels']); 
						$unique = $order_id.'_'.md5($data).'.pdf'; 
						if (!file_exists($labels_path.DIRECTORY_SEPARATOR.$unique))
						if (!empty($data))
						{
					      jimport( 'joomla.filesystem.file' );
						  JFile::write($labels_path.DIRECTORY_SEPARATOR.$unique, $data); 
						}
					}
					$r = Juri::root(); 
					if (substr($r, -1) != '/') $r .= '/'; 
					//echo '<a href="index.php?option=com_ajax&plugin=rupostel_ulozenka&group=vmshipment&format=raw&order_id='.$order_id.'">PDF</a>'; 
					$ret .= '<a href="'.$r.'plugins/vmshipment/rupostel_ulozenka/labels/'.$unique.'">PDF </a><br />'; 
				}
			}
			}
		}
		
		{
		 require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php'); 
		 $services = UlozenkaHelper::getServices($method); 
		 
		 $data = $this->getData($order_id); 
		 $sbranch_id = 0; 
		 if (!isset($selected_service_id)) $selected_service_id = 0; 
		 
		 if (!empty($data['branch_id']))
		 {
			 $sbranch_id = (int)$data['branch_id']; 
		 }
		 
		
		 
		 $select = '<select name="ulozenka_service" onchange="return onServiceChange(this);" style="min-width: 150px;">'; 
		 $service_branches = array(); 
		 foreach ($services as $k=>$s)
		 {
			
			 $selected_service = false; 
			 if (!empty($s['use_destination_branches']))
			 {
				 $pobocky = UlozenkaHelper::getPobockyByServiceId($s['id'], $method); 
				 
				 
				 if (!empty($pobocky['destination']))
				 foreach ($pobocky['destination'] as $k2=>$v)
				 {
					 $v['id'] = (int)$v['id']; 
					 if ($v['id'] === $sbranch_id)
					 {
						 $selected_service = true; 
						 $selected_service_id = (int)$s['id']; 
						
						
						 
					 }
				  $service_branches[$s['id']][$v['id']] = $v['name']; 
				 }
			 }
			 
			  $select .= '<option value="'.$s['id'].'" '; 
			 if (($s['id'] == $selected_service_id) || ($selected_service))
			 {
				 $select .= ' selected="selected" '; 
			 }
			 $select .= ' >'.htmlentities($s['name']).'</option>'; 
			 
		 }
		 $select .= '</select>'; 
		 
		 
		 
		 
		 
		}
		
		$css = '
			.chzn-container, .chzn-container-multi, .chzn-container-active, .chzn-drop, .chzn-drop .chzn-search, .chzn-drop .chzn-search input, .search-field input
	 {
			min-width: 150px; 
	 }

		'; 
		
		$document = JFactory::getDocument();
		$document->addStyleDeclaration($css); 
		 if (empty($selected_service_id))	 {
			 $first = true; 
		 }
		 
		 if (!empty($service_branches)) 
		 foreach ($service_branches as $k=>$v)
		 {
			
		 $select_o = '<div class="branches" id="branch_div_'.$k.'" '; 
		 if ((empty($selected_service_id) && (empty($first))) || ((!empty($selected_service_id)) && ($selected_service_id != $k)))
		 {
			 
		     $select_o .= ' style="display: none;" '; 
			 
		 }
		 else
		 if ($k !== $selected_service_id)
		 {
			// $select_o .= ' style="display: none;" '; 
		 }
		 
		
		 $first = false; 
		 $select_o .= ' ><select name="branches_'.$k.'" id="branches_'.$k.'" style="min-width: 150px;" >'; 
		 foreach ($v as $branch_id=>$name)
		 {
			 $select_o .= '<option value="'.$branch_id.'" '; 
			 if ($sbranch_id == $branch_id) 
			 {
				 $select_o .= ' selected="selected" '; 
				 
			 }
			 $select_o .= ' >'.htmlentities($name).'</option>'; 
		 }
		 $select_o .= '</select></div>'; 
		 $select .= $select_o; 
		 }
		 
		
		 
		 $ret .= $select; 
		 $script = "
<script>
 function onServiceChange(el)
 {
	 jQuery('.branches').hide(); 
	 var id = el.options[el.selectedIndex].value; 
	 var dx = document.getElementById('branch_div_'+id); 
	 if (dx != null)
	 jQuery('#branch_div_'+id).show(); 
	 return true; 
 }
 
 function submitUlozenka(el)
 {
	 var container = document.getElementById('ulozenka_form'); 
	 var input = document.createElement('input');
	 input.type = 'hidden';
	 input.value='1'; 
	 input.name='process_ulozenka'; 
	
     container.appendChild(input); // put it into the DOM
	
	 document.adminFormUlozenka.submit(); 
 
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
		 
		 $dobierka = '<label for="ulozenka_dobierka"><input type="checkbox" id="ulozenka_dobierka" name="ulozenka_dobierka" value="1" checked="checked">Na dobierku: <input type="text" value="'.$order_subtotal.'" name="ulozenka_dobierka_price" />'.$currency_iso.'</label>'; 
		 
		 		 $insurance = '<br /><label for="insurance_checkbox"><input type="checkbox" id="insurance_checkbox" name="insurance_checkbox" value="1">Poistenie: <input type="text" value="'.$order_subtotal.'" name="insurance_price" />'.$currency_iso.'</label>'; 

		 
		 $submit = '<input type="button" value="Vytvoriť štítok" onclick="return submitUlozenka(this);" />'; 
		return '<div style="float: left; clear: both; width: 100%;"><form action="index.php" method="post" id="adminFormUlozenka" name="adminFormUlozenka">
		<fieldset><legend>Uloženka</legend>'.$msg_u.$ret.'<div id="ulozenka_form" >'.$dobierka.$insurance.$submit.'</div></fieldset>
		
		<input type="hidden" name="task" value="edit" />
		<input type="hidden" name="option" value="com_virtuemart" />
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="virtuemart_order_id" value="'.(int)$order_id.'" />
		
		
		'.JHtml::_('form.token').'
		
		
		</form></div>'; 
	}
	
	private function _register($method, $values, $service_id=0, $branch_id=null, $cash_on_delivery=0, $insurance=0)
	{
		
		$order_id = $values['virtuemart_order_id']; 
		if (empty($order_id)) return; 
		
		
		
		require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php'); 
		
		if (is_null($branch_id))
		$pobocka = $this->getSelectedPobocka($order_id); 
		else $pobocka = $branch_id; 
			
		
		
		if (!empty($pobocka))
		$pobockaObj = UlozenkaHelper::getDataPobocky($method, $pobocka); 
		
		$orderModel = VmModel::getModel('orders'); 
		  $order = $orderModel->getOrder($order_id); 
		
		require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'api.php'); 
		  $request = new ulozenkaApi($method);
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
		  
		  if (empty($country_iso3)) $country_iso3 = 'CZE'; 
		  
		  $address_state = $country_iso3; 
		  
		  $q = 'select * from `'.$this->_tablename.'` where virtuemart_order_id = '.(int)$order_id.' limit 0,1'; 
		
		  $db->setQuery($q); 
		  $orderShippingData = $db->loadAssoc(); 
		  
		 
		  
		  if (empty($orderShippingData)) return; 
		  
		  $register_branch_id = $method->home_pobocka; 
		  if (empty($pobocka)) $destination_branch_id = null; 
		  else
		  $destination_branch_id = (int)$pobocka; 
		  
		  if (is_array($register_branch_id)) {
			  $register_branch_id = reset($register_branch_id); 
			}
		
		  $order_number = $address->order_number; 
		  
		  
		  $db = JFactory::getDBO(); 
		$q = 'select `invoice_number` from #__virtuemart_invoices where virtuemart_order_id = '.(int)$order_id.' order by created_on desc';
		$db->setQuery($q);
		$i_num = $db->loadResult(); 
		
		if (!empty($i_num)) { 
		   $order_number = $i_num; 
		}
		  
		  
		  if (isset($address->first_name))
		  $customer_name = $address->first_name; 
		  
		  if (isset($address->last_name))
		  $customer_surname = $address->last_name; 
		  
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
		  
		  
		  if ($address_state == 'CZE')
		  {
			  if ($currency_iso != 'CZK')
			  {
				  $db = JFactory::getDBO(); 
				  $q = 'select `virtuemart_currency_id` from #__virtuemart_currencies where currency_code_3 = \'CZK\' limit 0,1'; 
				  $db->setQuery($q); 
				  $czk_id = $db->loadResult(); 
				  $address->order_total = $order_subtotal = $currencyDisplay->convertCurrencyTo( $czk_id, $address->order_total,false);
				  $currency_iso = 'CZK'; 
			  }
		  }
		  if ($address_state == 'SVK')
		  {
			  if ($currency_iso != 'EUR')
			  {
				  $db = JFactory::getDBO(); 
				  $q = 'select `virtuemart_currency_id` from #__virtuemart_currencies where currency_code_3 = \'EUR\' limit 0,1'; 
				  $db->setQuery($q); 
				  $eur_id = $db->loadResult(); 
				  $address->order_total = $order_subtotal = $currencyDisplay->convertCurrencyTo( $eur_id, $address->order_total,false);
				  $currency_iso = 'EUR'; 
			  }
		  }
		  
		  if (empty($cash_on_delivery))
		  if (!empty($method->cod_payments))
		  if (in_array($payment_id, $method->cod_payments))
		  {
			  $cash_on_delivery = number_format($address->order_total, 2, '.', ''); 
		  }
		  $currency = $currency_iso; 
		  $password = ''; 
		  
		  $transport_service_id = 1; 
		  if (!empty($destination_branch_id))
		  {
		  $pobockaDetails = UlozenkaHelper::getPobockaDetails($method, $destination_branch_id, true); 
		  
		  
		  
		  
		  if (isset($pobockaDetails[0]))
		  if (is_array($pobockaDetails[0]['destination_for']))
		  {
			  foreach ($pobockaDetails[0]['destination_for'] as $k=>$v)
			  {
				  if (!empty($v['transport_service_id']))
				  {
					  $transport_service_id = $v['transport_service_id']; 
					 // $transport_service_id = $v['transport_id']; 
					  break; 
				  }
			  }
		  }
		  }
		  
		  if (!empty($service_id)) $transport_service_id = $service_id; 
		  
		  $values['service_id'] = $transport_service_id; 
		  
		 
		  $orig_phon = 'Tc:'.$customer_phone.' Obj:'.$address->order_number. 'ID Obj:'.$address->virtuemart_order_id; 
		  $customer_phone = preg_replace("/[^0-9]/", "", $customer_phone);
		  if (substr($customer_phone, 0,2) === '00')
			  $customer_phone = '+'.substr($customer_phone, 2); 
		  else
		  if ((substr($customer_phone, 0,1) === '0') && ($address_state == 'SVK'))
		  {
			  $customer_phone = '+421'.substr($customer_phone, 1); 
		  }
		  else
			  if (substr($customer_phone, 0,3) === '420') $customer_phone = '+'.$customer_phone; 
			  else
			  if (substr($customer_phone, 0,3) === '421') $customer_phone = '+'.$customer_phone; 
			  else
			  if ($address_state === 'CZE') $customer_phone = '+420'.$customer_phone; 
		  
		  if (substr($customer_phone, 0, 1)!='+') $customer_phone = '+'.$customer_phone; 
		  
		  if (empty($destination_branch_id))
			  $destination_branch_id = null; 
		  else 
			  $destination_branch_id = (int)$destination_branch_id; 
		
		  $myData = array(
		    "transport_service_id"=> $transport_service_id,
			"destination_branch_id"=>$destination_branch_id,
			"register_branch_id"=>(int)$register_branch_id,
			"password"=>$password,
			"order_number"=>(string)$order_number,
			"customer_name"=>$customer_name,
			"customer_surname"=>$customer_surname,
			"company_name"=>$company_name,
			"address_street"=>$address_street,
			"address_town"=>$address_town,
			"address_zip"=>$address_zip,
			"address_state"=>$address_state,
			"customer_phone"=>$customer_phone,
			"customer_email"=>$customer_email,
			"cash_on_delivery"=>$cash_on_delivery,
			"currency"=>$currency,
			"insurance"=>$insurance,
			"note"=>$orig_phon,
			"allow_card_payment"=>0,
			"require_full_age"=>0,
			"partner_consignment_id"=>null,
			"parcel_count"=>$parcel_count,
			"weight"=>(float)$weight,
			"labels"=>array(
				"type"=>"pdf"
			)
		  ); 
		  
		  $myData['X-Shop'] = $method->shopid;
		  $myData['X-Key'] = $method->xkey; 
		 
		  $data = $request->registerBalik($myData); 
		  
		 
		  $values['label'] = $data; 
		  $ret = $this->storePSPluginInternalData($values);
		  
		  
		  $data_json = json_decode($data, true); 
		  if (!empty($data_json['errors']))
		  {
			  foreach ($data_json['errors'] as $e)
			  {
				  JFactory::getApplication()->enqueueMessage($e['description']); 
			  }
		  }
		 
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
    

  function calculateSalesPrice_removed ($cart, $method, $cart_prices) {    
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

    function parseId($id, $method)
	{
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmshipment'.DIRECTORY_SEPARATOR.'rupostel_ulozenka'.DIRECTORY_SEPARATOR.'helper.php'); 
		 $xml = UlozenkaHelper::getPobocky($method); 	
		 if (isset($xml->pobocky)) {
			if (count($xml->pobocky)) {
		foreach ($xml->pobocky as $p)
		{
			/*
			$enabled_const = 'enabled'.$p->id; 
			if ((isset($method->pobocky->$enabled_const)) && ($method->pobocky->$enabled_const)) $enabled = true; 
					else $enabled = false; 
			*/
			if (isset($p->aktiv)) {
				$p->aktiv = (string)$p->aktiv; 
				if (empty($p->aktiv)) continue; 
			}
			$enabled = true; 
			if (!$enabled) continue; 
			
			
			
			
			$pobocka = (int)$p->id; 
			$prices = array(); 
			
			$this->getPobockaPriceKey($method, $p, $prices); 
			
		
			$key = $p->price_key; 
			
			if (($id === $key) || ($prices['md5'] == $id) || ($id == $pobocka))
			{
			
				return $prices; 
				
			
			}
			
			
		}
		}
		 }
		$a = array('parcel_price'=>0, 'dobierka_price' => 0, 'pobocka_id'=>0, 'pobocka_obj' => new stdClass()); 
		return $a; 
		
	}
  
   
    
    function getCosts(VirtueMartCart $cart, $method, $cart_prices, $pobocka=0)
	{                   
        //get actual display currency. in $cart->pricesCurrency its not updated immediately.. but if we dont use change currency,   getUserStateFromRequest doesnt return anything.. so then use cart pricesCurrency
     
		$app = JFactory::getApplication(); 
		$virtuemart_currency_id = $app->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',JRequest::getInt('virtuemart_currency_id') );	 

	 		
if (!empty($virtuemart_currency_id))
$currency = CurrencyDisplay::getInstance($virtuemart_currency_id);
else
{	
	$currency = CurrencyDisplay::getInstance($cart->paymentCurrency);
	$virtuemart_currency_id = $cart->paymentCurrency;
}

		
	
		
        $session = JFactory::getSession(); 
		$pobocka = JRequest::getVar('ulozenka_pobocky', $session->get('ulozenka_pobocka', $pobocka)); 
		
		// when loaded via OPC ajax
		$id2 = JRequest::getVar('opc_shipment_price_id'); 
		
		$price = 0; 
		 if ((!empty($id2)))
		 {
		    $arr = $this->parseId($id2, $method); 
			$price = $arr['parcel_price'];  
		    $dobierka_price_x = $arr['dobierka_price']; 
			// pozor, nejedna sa o vybranu pobocku, ale o skupinu pobociek s rovnakou cenou !!!
			$pobocka = $arr['pobocka_id']; 
			$pobockaObj = $arr['pobocka_obj']; 
			$skupina = true; 
		 }
		 
		
		
		if (empty($price))
		{
		if (empty($pobocka))
		{
		   // first load
		  $arr = $this->getSelectedPobockaPrice($method); 
		
		
	 
		  
		  $price = $arr['parcel_price'];  
		  $dobierka_price_x = $arr['dobierka_price']; 
		  
		
		}
		else
		{
		
		if (empty($pobocka)) 
		{
			 if (!empty($method->debug)) {
				 JFactory::getApplication()->enqueueMessage('Nulova cena pre neznamu pobocku'); 
			  }
			return 0; 
		}
		
	    require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php'); 
		$pobockaObj = UlozenkaHelper::getDataPobocky($method, $pobocka); 
		
		$prices = array(); 
		$this->getPobockaPriceKey($method, $pobockaObj, $prices); 
		
		$price = $prices['price']; 
		$dobierka_price_x = $prices['dobierka_price']; 
		
		
		
		
        }
		}
		
		
		

		
        
		if (!class_exists('ShopFunctions'))
		require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'shopfunctions.php');
		
		if (!empty($method->currency)) $cid = (int)$method->currency; 
		else
		$cid = ShopFunctions::getCurrencyIDByName("CZK"); 
		
		
		
		if (empty($cart->pricesUnformatted))
		{
			$cart->pricesUnformatted = $cart->cartPrices; 
		}

        /*
          Set free shipping
        */
		if (!empty($cart_prices['salesPrice'])) {
			
		 $total = $cart_prices['salesPrice'];
		}
		else {
		
		$total = $cart->pricesUnformatted['billTotal']; 
		if (!empty($cart->pricesUnformatted['salesPriceShipment']))
		$total -= (float)$cart->pricesUnformatted['salesPriceShipment']; 
		
		if (!empty($cart->pricesUnformatted['salesPricePayment']))
		$total -= (float)$cart->pricesUnformatted['salesPricePayment']; 
	
		}
		
		
		
		if (!empty($pobockaObj->sk))
		{
			
			
		  $method->free_start_sk = (float)$method->free_start_sk; 
		  if (!empty($method->free_start_sk))
		  if ($total >= $method->free_start_sk)
		    {
			  if (!empty($method->debug)) {
				  JFactory::getApplication()->enqueueMessage('Nulova cena pre SK pobocky'); 
			  }
			  return 0;    
			}
		}
		else
		{
			
		  if (!empty($pobockaObj->partner))
		   {
			   
			   
		      if (!empty($method->free_start_partner))
		      {
			     $fs = (float)$method->free_start_partner; 
				 if ($total >= $fs) 
				 {
				 if (!empty($method->debug)) {
				 JFactory::getApplication()->enqueueMessage('Nulova cena pre partnersku pobocku'); 
			  }
				 return 0; 
				 }
			  }
		   }
		   else
		   {
		      if ((empty($pobockaObj->sk)) && (!empty($method->free_start_ulozenka)))
		      {
				  
				  
				 
			     $fs = (float)$method->free_start_ulozenka; 
				 if ($total >= $fs) 
				 {
				 if (!empty($method->debug)) {
				 if (!empty($skupina)) {
					 JFactory::getApplication()->enqueueMessage('Nulova cena pre CZ pobocku '); 
				 }
				 else {
				   JFactory::getApplication()->enqueueMessage('Nulova cena pre CZ pobocku '.$pobockaObj->nazev.' '.var_export($pobockaObj, true)); 
				 }
			  }
				 return 0; 
				 }
			  }
		   
		   }
		   
		}
		if (empty($pobockaObj)) {
			if (!empty($method->debug)) {
				 JFactory::getApplication()->enqueueMessage('Standardna cena pre neznamu pobocku: '.$price); 
			  }
			  return $price; 
		}
		if (!empty($method->cod_payments))
		if (!empty($cart->virtuemart_paymentmethod_id))
		{
		   /*
		   if (in_array($cart->virtuemart_paymentmethod_id, $method->cod_payments))
		   if (!empty($method->pobocky->$dobierka_price))
		   $price += $method->pobocky->$dobierka_price; 
	   dobierka_price_x
	       */
		   $dobierka_priplatok_limit = (float)$method->dobierka_priplatok_limit; 
		   if ($total <= $dobierka_priplatok_limit) {
		   if (in_array($cart->virtuemart_paymentmethod_id, $method->cod_payments))
		   if (!empty($dobierka_price_x))  {
		   $price += $dobierka_price_x; 
		   
		    if (!empty($method->debug)) {
				 JFactory::getApplication()->enqueueMessage('Priplatok za dobierku '.$dobierka_price_x); 
			  }
		   
		   }
		   
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
		
		if (!empty($method->debug)) {
				 JFactory::getApplication()->enqueueMessage('Cena znasobena poctom balikov:  '.$c); 
			  }
		
		}
		
		
		$app = JFactory::getApplication(); 
		/*
	    $czk_id = $cid; 
		$czk_price = $price; 
		
		$currencyDisplayCzk = CurrencyDisplay::getInstance($czk_id);
		$virtuemart_currency_id = $current_eur = $app->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',JRequest::getInt('virtuemart_currency_id') );	 
		
		$currencyDisplay_eur = CurrencyDisplay::getInstance($virtuemart_currency_id);
		
		$price = $currencyDisplay_eur->convertCurrencyTo($currencyDisplayCzk, $czk_price,false);
		
		if (!empty($price))
		{
		}
		*/
		
		
		return $price;  
		/*
		if ($cid != $pricesCurrencyId)
        $price = $currency->convertCurrencyTo ($pricesCurrencyId, $price);//convert hack to keep constant shipment prices. VM2 converts it back later in cart checkout
		
		
		
        return $price;    
		*/
    }
    
	private function getParcelCount($method, $weight)
	{
		$w = (float)$method->weight_stop; 
		 $times = $weight / $w; 
		  $c = ceil($times); 
		  return (int)$c; 
	}
	
    
    protected function checkConditions($cart, $method, $cart_prices)
    {
	   	$address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
	    $is_cz = $this->isCz($address['virtuemart_country_id']); 
		$is_sk = $this->isSk($address['virtuemart_country_id']); 

	   if ((!$is_sk) && (!$is_cz)) 
	   {
		   if (class_exists('OPCloader'))
		   {
			   OPCloader::opcDebug('OPC: Not a CZ or SK country', 'ulozenka');    
		   }
		   
		   return false; 
	   }
		
	  $wstart = (float)$method->weight_start;
	  
      $ws = (float)$method->weight_stop;
      $orderWeight = $this->getOrderWeight ($cart, $method->weight_unit);
	  
	  if (!empty($wstart))
	  if ($orderWeight < $wstart) {
		  if (class_exists('OPCloader'))
		   {
			   OPCloader::opcDebug('OPC: Outside weight range', 'ulozenka');    
		   }

		  return false; 
	  }
	  
	  // do not allow oversize packages
	  if (!empty($ws))
      if (empty($method->strategy))
	  if ($orderWeight > $ws) {
		  if (class_exists('OPCloader'))
		   {
			   OPCloader::opcDebug('OPC: Outside weight range strategy', 'ulozenka');    
		   }
		  
		  return false; 
	  }
	  
	  if (!empty($ws))
      if (!empty($method->strategy))
	  {
	  

		if(count($cart->products)>0) {
			foreach ($cart->products as $product) {

				$weight = ShopFunctions::convertWeightUnit ($product->product_weight, $product->product_weight_uom, $method->weight_unit) ;
				// single product weight is larger then x: 
				if ($weight > $ws) {
					
					 if (class_exists('OPCloader'))
		   {
			   OPCloader::opcDebug('OPC: A product outside max weight range', 'ulozenka');    
		   }
					
					return false; 
				}
			}
		}

	  
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
			   OPCloader::opcDebug('OPC: Order amount start not met', 'ulozenka');    
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
			   OPCloader::opcDebug('OPC: Max Order amount met', 'ulozenka');    
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
		
		/*
				$q = 'ALTER TABLE  `#__virtuemart_shipmentmethods` CHANGE  `shipment_params`  `shipment_params` BLOB NOT NULL DEFAULT  \'\''; 
		$db = JFactory::getDBO(); 
		$db->setQuery($q); 
		$db->query(); 
		JFactory::getApplication()->enqueueMessage("RuposTel Ulozenka: shipment_params v tabulke #__virtuemart_shipmentmethods bol upraveny na typ BLOB. V pripade aktualizacie Virtuemartu, navstivte tieto nastavenia Ulozenky"); 
		*/
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

		
		$saved = JRequest::getInt('ulozenka_saved'); 
		
		
		
		


		
	    $pobocka = JRequest::getVar('ulozenka_pobocky', ''); 
		$pobocka = (int)$pobocka; 
		
		

		if ((empty($pobocka)) && (!empty($saved))) $pobocka = $saved; 
		
		//legacy: 
		
		$session = JFactory::getSession(); 
		if (!empty($pobocka)) {
		 $session->set('ulozenka_pobocka', $pobocka); 
		}
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
				 //JRequest::getVar('ulozenka_pobocky', $pobockaA['pobocka_id']); 
				}
			}
		}
		*/
		
        if ($this->OnSelectCheck($cart)) {

			if (empty($pobocka)) 
			  {
			  
			    return true; 
			    
			  }
			$session = JFactory::getSession(); 
			$session->set('ulozenka_pobocka', (int)$pobocka); 
        }else{
			
        }
        
        $ret = $this->OnSelectCheck($cart);
		return true; 
		return $ret; 
    }
	public function isSk($country_id)
	{
	    if (empty($country_id)) return true; 
	    $countryModel = VmModel::getModel ('country');
		$sk_id = $countryModel->getCountryByCode('SVK');
		if (empty($sk_id) && (!empty($country_id))) return false; 
		if (empty($sk_id) && (empty($country_id))) return true; 
		if (empty($country_id) || ($country_id == $sk_id->virtuemart_country_id)) return true; 
		return false; 
	}
	
	public function isCz($country_id)
	{
	    if (empty($country_id)) return true; 
	    $countryModel = VmModel::getModel ('country');
		$sk_id = $countryModel->getCountryByCode('CZE'); 
		if (empty($sk_id) && (!empty($country_id))) return false; 
		if (empty($sk_id) && (empty($country_id))) return true; 
		if (empty($country_id) || ($country_id == $sk_id->virtuemart_country_id)) return true; 
		return false; 
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
		if (method_exists($cart, 'getST')) {
		 $address = $cart->getST(); 
		}
		else
		{
	     $address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
		}
      
		$is_cz = $this->isCz($address['virtuemart_country_id']); 
		$is_sk = $this->isSk($address['virtuemart_country_id']); 
		
		$html        = array();
        $method_name = $this->_psType . '_name';
        $document = JFactory::getDocument(); 
		
		
		
        foreach ($this->methods as $key => $method) { 
		
		
			if (!$this->checkConditions ($cart, $method, $cart->pricesUnformatted)) continue; 
		
			
			$mymethod = $method; 
			$cache_d = JPATH_CACHE.DIRECTORY_SEPARATOR.'ulozenka'.DIRECTORY_SEPARATOR.'ulozenka_html_'.$method->partners.'_'.$is_cz.'_'.$is_sk.'_'.$method->virtuemart_shipmentmethod_id.'.html'; 
			
			
			if ((file_exists($cache_d) && (!empty($method->cache))))
			 {
				 
			   if (!isset($html[$key])) $html[$key] = ''; 
			   $html[$key] .= file_get_contents($cache_d); 
			   
			   
			   $session = JFactory::getSession(); 
			   $pobocka = JRequest::getVar('ulozenka_pobocky', $session->get('ulozenka_pobocka', '')); 
			   //$pobocka = JRequest::getVar('ulozenka_pobocky', ''); 
			   
			   
			   if (!empty($pobocka)) 
			   {
				   $sind = $pobocka; 
			   
			   $html[$key] = str_replace('value="'.$sind.'"', 'value="'.$sind.'" selected="selected" ', $html[$key]); 
			   }
			   
		
			   
			 }
			 else
			 {
			
		    $xml = UlozenkaHelper::getPobocky($method); 	
			
			
            $html[$key] = '';
			
			$sind = ''; 
			
			
			if (isset($xml->pobocky)) {
			if (count($xml->pobocky)) {

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
				    
					if ($is_sk)
					if ($p->country != 'SVK') continue; 

					if ($is_cz)
					if ($p->country != 'CZE') continue; 

					if (empty($method->partners))
					if (!empty($p->parner))
					continue; 
				    
					
					$p->aktiv = (string)$p->aktiv; 
					if (empty($p->aktiv)) continue; 
					
					/*
					$enabled_const = 'ULOZENKA_'.strtoupper((string)$p->zkratka).'_ENABLED';
					if (defined($enabled_const)) {
						if (constant($enabled_const)==1) {
							$enabled = true;
						} else {
							$enabled = false;
						}
					} else {
						$enabled = true;
					}
					
					$enabled_const = 'enabled'.$p->id; 
					
					if ((isset($method->pobocky->$enabled_const)) && ($method->pobocky->$enabled_const)) $enabled = true; 
					else $enabled = false; 
					*/
					$enabled = true; 
					
					
					if ($enabled) {
						if ((string)$p->aktiv) {
							//$price_const = 'ULOZENKA_'.strtoupper((string)$p->zkratka).'_PRICE';

							
							
							$session = JFactory::getSession(); 
							$first_opt = $session->get('ulozenka_pobocka', $first_opt); 
							$mk = 0; 
							
							
					

	
	  $session = JFactory::getSession();   
	  $pobocka = JRequest::getVar('ulozenka_pobocky', $session->get('ulozenka_pobocka', '')); 

		
	    
		
		if (!empty($pobocka)) $sind = $pobocka; 
							
							if (empty($first_opt)) $first_opt = $p->id; 
							if ($first_opt == $p->id) $sind = $ind; 
							$ind++; 
							
							
							
							
							
							
							//$pobocky_options[] = JHTML::_('select.option',  $p->id, $p->nazev );
							$opobocka = new stdClass(); 
							$opobocka->id = $p->id; 
							$opobocka->nazev = $p->nazev; 
							$opobocka->partner = $p->partner; 
							$opobocka->obj = $p; 
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
					
						  $methodSalesPrice     = $this->calculateSalesPrice($cart, $method, $cart->pricesUnformatted);   
						  
						  $single_price = ''; 
						  
						  if ((!empty($method->single_price)) && ($method->single_price == $method->single_price_partner))
						  {
							  $single_price = $methodSalesPrice; 
						  }
						  
						  $plg_name = $this->renderPluginName($method); 
						
						$shtml = ''; 
						if ($selected == $method->virtuemart_shipmentmethod_id) {
							$shtml = ' checked="checked" '; 
						}
						
						
							$cena = ''; 
								$output1 = $this->renderByLayout('pobocky', array(
								  'virtuemart_shipmentmethod_id'=>$method->virtuemart_shipmentmethod_id, 
								  'single_price'=>$methodSalesPrice,
								  'first_opt'=>$first_opt,
								  'method'=>$method,
								  'plg_name' => $plg_name,
								  'selected' => $shtml
								)); 
								$detail_url = JURI::root().'plugins/vmshipment/ulozenka/detail_pobocky.php?id='.(string)$p->id;
								
								$first = false;

							}
				
				foreach ($pobocky_options as $k => &$ppp)
				 {
					 $p = $ppp; 
					 
			
					 $enabled = true; 
					 
					 if (empty($enabled)) {
						 unset($pobocky_options[$k]); 
						 continue; 
					 }
					 
					 //$ppp->price_key = 
					 $prices = array(); 
					 $price_key = $this->getPobockaPriceKey($method, $ppp->obj, $prices); 
					 $ppp->price_key = $price_key;
					 
					 
					 
					
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
				
				if ($cart->virtuemart_shipmentmethod_id == $method->virtuemart_shipmentmethod_id) {
					
				   $html[$key] = str_replace('ifsel="ifsel"', ' selected="selected" checked="checked" ', $html[$key]); 
				}
				
				
				$session = JFactory::getSession(); 
			    $pobocka = JRequest::getVar('ulozenka_pobocky', $session->get('ulozenka_pobocka', '')); 
			   if (!empty($pobocka)) 
			   {
				   $sind = $pobocka; 
			   
			      $html[$key] = str_replace('value="'.$sind.'"', 'value="'.$sind.'" selected="selected" ', $html[$key]); 
			   }
				
				
			}  // pobocky not empty
			
			} // isset pobocky
			
		} // end of ... if not cache ... 
			
			
           
        } // end of foreach 
	
		if (isset($mymethod))
		if (!defined('ulozenka_javascript'))
				{
	
				$this->getOpcJavascript($method); 
				define('ulozenka_javascript', true); 
				}
		
		

        if (empty($html)) {
            return FALSE;
        }
        
		
		
        $htmlIn[] = $html;
        return TRUE;
    }
 	
 public function getPobockaPriceKey($method, &$pobockaObj, &$prices)
 {
	 
	 $pobocka = $pobockaObj->id; 
	 
	
	 
	
			
				if ((!empty($method->single_price_partner)) && (!empty($pobockaObj->partner)))
				{
				 if (!empty($method->single_price_partner)) 
				 $price = $method->single_price_partner; 
				 else $price = $method->single_price; 
				 
				 $dprice = (float)$method->dobierka_priplatok;  
				 
				}
				elseif (!empty($method->single_price))
				{
				$price = $method->single_price; 
				$dprice = (float)$method->dobierka_priplatok; 
				}
				else 
				{
					 $price = $pobockaObj->prices->parcel; 
					 $dprice = $pobockaObj->prices->cash_on_delivery; 
					 $currency = $pobockaObj->prices->currency; 
	 
	 $vendor_currency3 = UlozenkaHelper::getVendorCurrency(); 
	 if ($currency != $vendor_currency3) {
		 
		 
		 $price = UlozenkaHelper::convertPrice($price, $currency, $vendor_currency3); 
		 $dprice = UlozenkaHelper::convertPrice($dprice, $currency, $vendor_currency3); 
		 
		 
	 }
				}
				
			    
				
			
			
			if (!isset($pobockaObj->sk)) $pobockaObj->sk = 0; 
			
			
			
			$md5 = md5($price.'_'.$dprice.'_'.$pobockaObj->partner.'_'.$pobockaObj->sk); 
			$key = 'price_id_'.$method->virtuemart_shipmentmethod_id.'_'.$md5; 
			$pobockaObj->price_key = $key; 
			
			$prices['price'] = (float)$price; 
			$prices['dprice'] = (float)$dprice; 
			$prices['parcel_price'] = (float)$price; 
			$prices['dobierka_price'] = (float)$dprice; 
			$prices['pobocka_id'] = (int)$pobockaObj->id; 
			$prices['pobocka_obj'] = $pobockaObj; 
			$prices['md5'] = $md5; 
			
			
			return $key; 
			
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
		 $xml = UlozenkaHelper::getPobocky($method); 	
			
           
			
			
			
			if (isset($xml->pobocky)) {
			if (count($xml->pobocky)) {
		foreach ($xml->pobocky as $p)
		{
			
			/*
			$enabled_const = 'enabled'.$p->id; 
			if ((isset($method->pobocky->$enabled_const)) && ($method->pobocky->$enabled_const)) $enabled = true; 
					else $enabled = false; 
			
			if (!$enabled) continue; 
			*/
			if (isset($p->aktiv)) {
			 $p->aktiv = (string)$p->aktiv; 
		     if (empty($p->aktiv)) continue; 
			}
			
			$pobocka = $p->id; 
			$prices = array(); 
			$key = $this->getPobockaPriceKey($method, $p, $prices); 
			$calcs[$key] = $key; 
			
			//$mr['shipment_id_'.$method->virtuemart_shipmentmethod_id.'_'.$p->id] = 'shipment_id_'.$method->virtuemart_shipmentmethod_id.'_'.$p->id; 
			
			
		
		}
			}
		
		
		//
			}
			}
			$calcs['price_id_'.$method->virtuemart_shipmentmethod_id.'_0'] = 'price_id_'.$method->virtuemart_shipmentmethod_id.'_0'; 
			$ret = $mr = $calcs; 
			
			
			
			
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
  
  public function getHiddenShippingHtmlInsideFormOPC(&$html) {
    if (!defined('ulozenka_saved'))
	   {
		   
		 $session = JFactory::getSession(); 
		$branch_id = JRequest::getVar('ulozenka_pobocky', $session->get('ulozenka_pobocka', '')); 
		   
	     $html .= '<input type="hidden" name="ulozenka_saved" value="'.(int)$branch_id.'" id="ulozenka_saved" />'; 
		 define('ulozenka_saved', true); 
	   }
  }
  
  public function setOPCbeforeSelect($cart, $shipmentid, $shipping_method, $id, &$html)
   {

	  if (!($this->_selectedThisByMethodId ($id))) {
       return NULL;
      }
	  $session = JFactory::getSession();   
	  $ret = JRequest::getVar('ulozenka_pobocky', $session->get('ulozenka_pobocka', '')); 
	  $ret = (int)$ret; 
	  if (!empty($ret))
	  {
		  $session->set('ulozenka_pobocka', $ret);
		  
	  }
	  
	
	  
	  if (!empty($ret)) return $ret; 
	  
	 
	  
	  
	  $a = explode('_', $shipmentid); 
	  
	  
	  //from: <option ismulti="true" multi_id="shipment_id_'.$method->virtuemart_shipmentmethod_id.'_'.$ppp->id.'" 
	  if (!empty($a[3]))
	   {
	     $pobocka = $a[3]; 
		
		 JRequest::setVar('ulozenka_pobocka', $pobocka); 
		 
		 
	   }
	   if (empty($pobocka)) $pobocka = ''; 
	   
	  
   }
  
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
    $html .= $this->getHtmlRowBE ('BRANCH', $shipinfo->branch_name_street);   
    $html .= $this->getHtmlRowBE ('CURRENCY', $shipinfo->branch_currency);       

    $html .= '</table>' . "\n";

    return $html;
  }    
    private function getSelectedPobockaObj($id)
	{
	  	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php'); 
		if (!empty($this->methods))
		{
			$method = reset($this->methods); 
			
		}
		else return new stdClass(); 
		$pobockaObj = UlozenkaHelper::getDataPobocky($method, $id); 
		return $pobockaObj; 
	}
    private function getSelectedPobocka($order_id=0)
	 {
		
	   $session = JFactory::getSession(); 
	   //$ret = JRequest::getVar('ulozenka_pobocka', $session->get('ulozenka_pobocka', '')); 
	   $ret = JRequest::getVar('ulozenka_pobocky', $session->get('ulozenka_pobocka', '')); 
	   
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
	 
	 public function plgGetOpcOverride($layout_name, $name, $psType, &$ref, &$method, &$htmlIn, $extra) {
	   if ($layout_name === 'opc_javascript') {
	      $this->getOpcJavascript($method); 
		  return true; 
	   }
	 
	 }
	 public function getOpcJavascript($method) {
	  
			$document = JFactory::getDocument(); 

$root = Juri::root(); 
				if (substr($root, -1)!=='/') $root .= '/'; 
			
	$script = " var ulozenka_error = '"; 
	if (!empty($method->chyba))
	{
	 $script .= addslashes(JText::_($method->chyba));
	}
	else 
	{
	 $script .= 'Vyberte pobočku Uloženky'; 
	}
	$script .= "'; "; 
	$script .= ' var ulozenkaVmId = '.$method->virtuemart_shipmentmethod_id.'; '; 
	$document->addScriptDeclaration($script);  
	
$style = '
select#ulozenka_pobocky.invalid, #vmMainPageOPC select#ulozenka_pobocky.invalid { 
 border: 1px solid red; 
}

'; 
$document->addStyleDeclaration( $style );
	
				
				
				
				
				
				 
				
				if (class_exists('JHTMLOPC')) {
				  JHTMLOPC::_('behavior.modal', 'a.ulozenkamodal'); 
				}
		
		Jhtml::script($root.'plugins/vmshipment/rupostel_ulozenka/media/ulozenka.js'); 
		JHtml::script($root.'plugins/vmshipment/rupostel_ulozenka/media/jquery.cookie.js'); 
		
		
	 }
	 
	 public function getSelectedPobockaPrice($method)
	 {
		 $id = JRequest::getVar('ulozenka_pobocka', 0); 
		 
		 $id2 = JRequest::getVar('opc_shipment_price_id'); 
		 if (empty($id) && (!empty($id2)))
		 {
			 $id = $id2; 
		 }
		 
		 return $this->parseId($id, $method); 
	 }
	 
    public function plgVmonSelectedCalculatePriceShipment(VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name)
    {        
		
		
		if (!($method = $this->_selectedThisByMethodId ($cart->virtuemart_shipmentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}

		if (!($method = $this->getVmPluginMethod ($cart->virtuemart_shipmentmethod_id))) {
			return NULL;
		}
		
		
		
		
		
		/*
		$pobocka = $this->getSelectedPobocka(); 
		
		
		$parcel_price = 'parcelprice'.$pobocka;
	    $dobierka_price = 'codprice'.$pobocka; 
		
		$price = $method->pobocky->$parcel_price;  
		*/
		$cart_prices_name = '';
		$cart_prices['cost'] = $cart_prices[$this->_psType . 'Value'] = $this->getCosts($cart, $method, $cart_prices); 
		
		//echo 'plgVmonSelectedCalculatePriceShipment.'; 
		//echo 'cena: '.$cart_prices['cost']; 
		
		if (!$this->checkConditions ($cart, $method, $cart_prices)) {
				
			return FALSE;
		}

		$cart_prices_name = $this->renderPluginName ($method);
		$this->setCartPrices ($cart, $cart_prices, $method);

		
		
		static $c; 
		$c++; 
		
		if ((!empty($test)) && ($c>2)) {
		// var_dump($test); 
		}
		
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
			  
			  
			  $msg = JText::_('COM_VIRTUEMART_USER_FORM_MISSING_REQUIRED_JS').': '.JText::_('COM_VIRTUEMART_SHOPPER_FORM_PHONE'); 
			  JFactory::getApplication()->enqueueMessage($msg,'error'); 
			  return false; 
		  }
		  
		  
		  //ulozenka_pobocky
		  $session = JFactory::getSession(); 
		  $branch_id = JRequest::getVar('ulozenka_pobocky', $session->get('ulozenka_pobocka', '')); 
		  //$branch_id = JRequest::getVar('ulozenka_pobocky', JRequest::getVar('ulozenka_pobocka', '')); 
		  if (empty($branch_id)) {
			  $msg = $method->chyba; 
			  JFactory::getApplication()->enqueueMessage($msg,'error'); 
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
	public function setPobocka($pobocka, $price, $parcelprice, $enabled, $type='ULOZENKA', $shipper='') {
		
	}
	public function plgVmSetOnTablePluginShipment_removed(&$data, &$table)
	{
		
		$cache_d = JPATH_SITE.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'ulozenka'; 
		
		if (file_exists($cache_d))
		{
			jimport( 'joomla.filesystem.folder' );
			JFolder::delete($cache_d); 
		}
		
		if (empty($data['enable_all'])) return; 
		$data['enable_all'] = (int)$data['enable_all']; 
		
		
	
		
		foreach ($data['params']['pobocky'] as $k=>$v)
		{
			
			$data['enable_all'] = (int)$data['enable_all']; 
			
			$enabled = $data['enable_all']; 
			
			
			
			
			
			if (stripos($k, 'parcelprice')!==false)
			{
				$nk = str_replace('parcelprice', 'enabled', $k); 
			}
			else continue; 
			
			
			if (stripos($nk, 'enabled')!==false)
			{
				
			  if ($data['enable_all']===1)
			  {
				  $data['params']['pobocky'][$nk] = 1; 
				  $data['pobocky'][$nk] = 1; 
				  
				 
			  }
			  else
			  {
				  $data['params']['pobocky'][$nk] = 0; 
				  $data['pobocky'][$nk] = 0; 
			  }
			}
				
			
		}
	

	
	}

    
}

// No closing tag
