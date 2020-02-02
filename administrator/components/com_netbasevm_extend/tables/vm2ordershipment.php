<?php
/*------------------------------------
* -Netbase- Advanced Virtuemart Invoices for Virtuemart
* Author    CMSMart Team
* Copyright (C) 2012 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support:  Forum - http://bloorum.com/forums
-----------------------------------------------------*/

class TableVm2OrderShipment extends JTable
{
    var $id = null;
    var $virtuemart_order_id = null;
    var $order_number = null;
    var $virtuemart_shipmentmethod_id = null;
    var $shipment_name = null;
    var $order_weight = null;
    var $shipment_weight_unit = null;
    var $shipment_cost = null;
    var $shipment_package_fee = null;
    var $tax_id = null;
    var $created_on = null;
    var $created_by = null;
    var $modified_on = null;
    var $modified_by = null;

    function checkTableExists()
    {
    	//check if table for shipment plugin exists
    	$this->_db->setQuery('SHOW TABLES LIKE '.$this->_db->Quote(str_replace('#__',$this->_db->getPrefix(),$this->_tbl)));
    	$this->_db->execute();
		return $this->_db->getNumRows() ? true : false;
    }
    
    /**
     * Create object. Set database connector. Set table name and primary key.
     * 
     * @param JDatabaseMySQL $db
     */
    function __construct(&$db, $plugin)
    {
    	// Set internal variables.
    	$this->_tbl = '#__virtuemart_shipment_plg_'.$plugin;
		$this->_db		= &$db;
    	
		if ($this->checkTableExists())
        	parent::__construct($this->_tbl, 'id', $db);
    }
    
    //store info about current shipment
    function save($updateNulls = false)
    {
        $data = $requestpost= JRequest::get('post',4);
        
    	//delete previous records from all plugin tables. becuase can be only one record in one table.
    	$this->_db->setQuery('SHOW TABLES LIKE '.$this->_db->Quote(str_replace('#__',$this->_db->getPrefix(),'#__virtuemart_shipment_plg_%')));
    	$pluginTables = $this->_db->loadColumn();
    	foreach ($pluginTables as $pluginTable)	{ //is with real db prefix
    		
    		$pluginTable = preg_replace('#^'.preg_quote($this->_db->getPrefix()).'#i','#__',$pluginTable);
    		
    		if ($pluginTable==$this->_tbl) //not delete record from current table (can be edited or new)
    			continue;
    			
    		$this->_db->setQuery('DELETE FROM '.$pluginTable.' WHERE virtuemart_order_id='.(int)$data['order_id']);
    		if (!$this->_db->execute())
    			JError::raiseWarning(0,'Could not delete former shipment record from '.$pluginTable);
    	}
    	
    	//check if table for shipment plugin exists
		if (!$this->checkTableExists())
			return false;
			
       	$this->_db->setQuery('SELECT `id` FROM `'.$this->_tbl .'` WHERE `virtuemart_order_id` = ' . (int) $data['order_id']);
       	$formerID = $this->_db->loadResult();

		$now = gmdate('Y-m-d H:i:s');
        $currentUser = JFactory::getUser();
       	
        //TODO: we cannot use plugin functions directly, because it needs whole cart object now. maybe we will have to simulate it.
    	$this->id = $formerID ? $formerID : null;
    	$this->virtuemart_order_id = $data['order_id'];
    	$this->order_number = $data['order_number'];
    	$this->virtuemart_shipmentmethod_id = $data['shipment_method_id'];

    	$weightUnit = false;
    	
    	//find proper shipment record, than store its info to this order-shipment table. 
    	foreach (invoiceGetter::getShippingsVM2() as $shipping) 
    		if ($shipping->shipping_rate_id == $this->virtuemart_shipmentmethod_id){
    			$this->shipment_name = '<span class="vmshipment_name">'.$shipping->name.'</span><span class="vmshipment_description">'.$shipping->desc.'</span>'; //? VM...
		    	$this->shipment_cost = isset($shipping->cost) ? $shipping->cost : null;
		    	$this->shipment_package_fee = isset($shipping->package_fee) ? $shipping->package_fee : null;
		    	$this->tax_id = isset($shipping->tax_id) ? $shipping->tax_id : null;
		    	$weightUnit = @$shipping->weight_unit;
    		}

    	//update weight and unit. lets hope it will work for other than default plugins also
    	$weight = $this->getOrderWeight($data, $weightUnit);	
    	$this->order_weight = $weight!==false ? $weight : null;
		$this->shipment_weight_unit = $weight!==false ? $weightUnit : null;
		
    	$this->modified_on = $now;
	    $this->modified_by = $currentUser->id;
	    if (!$this->id){
	        $this->created_on = $now;
	        $this->created_by = $currentUser->id;
	    }
	    
    	return parent::store();
    }
    
    //similar to plg_weight_countries function
    function getOrderWeight($data, $toUnit) {

    	if (!$toUnit)
    		return false;
    	
    	$weight = 0;
    	NbordersHelper::importVMFile('helpers/shopfunctions.php');
    	foreach ($data['order_item_id'] as $i => $orderItemId) {
    		if (($productId = $data['product_id'][$i])){
    			
    			if (!($product = invoiceGetter::getProduct($productId)) OR !isset($data['product_quantity'][$i])) //if error, dont compute! 
    				return false;
    			
    			$quantity = $data['product_quantity'][$i];
    			$weight += (ShopFunctions::convertWeigthUnit($product->product_weight, $product->product_weight_uom, $toUnit) * $quantity);
    		}
    	}

		return $weight;
	}
}

?>