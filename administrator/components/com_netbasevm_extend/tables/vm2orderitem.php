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

class TableVm2OrderItem extends JTable
{
    var $virtuemart_order_item_id = null;
    var $virtuemart_order_id = null;
    var $virtuemart_vendor_id = null;
    var $virtuemart_product_id = null;
    var $order_item_sku = null;
    var $order_item_name = null;
    var $product_quantity = null;
    var $product_item_price = null;
    var $product_tax = null;
    var $product_basePriceWithTax = null;
    var $product_final_price = null;
    var $product_subtotal_discount = null;
    var $product_subtotal_with_tax = null;
    var $order_item_currency = null;
    var $order_status = null;
    var $product_attribute = null;
    var $created_on = null;
    var $created_by = null;
    var $modified_on = null;
    var $modified_by = null;
    
    function __construct(&$db)
    {
        parent::__construct('#__virtuemart_order_items', 'virtuemart_order_item_id', $db);
    }

    function store($updateNulls = false)
    {
    	$rename = array(  //if we are using other "inner" name than db name
    		'virtuemart_order_item_id'=>'order_item_id',
    		'virtuemart_vendor_id'=>'vendor_id',
    		'virtuemart_product_id' => 'product_id', 
    		'product_subtotal_discount' => 'product_price_discount'
    	);
    	$data = $requestpost= JRequest::get('post',4);
        if($data['cid']==NULL)
        {
            $data['cid']=$data['tmp_order_id'];
            $data['order_id']=$data['tmp_order_id'];
            
        }
        //$data[]

        $now = gmdate('Y-m-d H:i:s');
        $currentUser = JFactory::getUser();
        
        $vars = get_object_vars($this);
        
      
        $newStatuses = array();
        $stored = array();
        
        foreach ($data['order_item_id'] as $i => $orderItemId) { //items to update/add
        	
            foreach ($vars as $param => $value)
                if ($param[0] != '_'){
                	$name = (isset($rename[$param]) ? $rename[$param] : $param);
                	if (isset($data[$name]))
                    	$this->$param = @reset(array_slice($data[$name], $i, 1));
             	}
             	
            $this->order_status = null; //NOT update status here. use always VM functions (to adjust stock)
            if (!$this->virtuemart_order_item_id)
                $this->order_status = 'N';	//if ADDING product, set as NEW.
            
            //if changed quantity, set status to "N" to neutralize stock, than (after save) set new status with new quantity. thats only way to use VM functions.
            if ($this->virtuemart_order_item_id){
            	$class  = get_class($this);
            	$oldTable = new $class($this->_db);
            	if (!($oldTable->load($this->virtuemart_order_item_id)))
            		JError::raiseWarning(0, 'Cannit load old row for ordered item '.$this->virtuemart_order_item_id.'. '.$oldTable->getError());
            	elseif ($oldTable->product_quantity!=$this->product_quantity) //changed quantity!
            		$this->changeItemStatus((int)$data['order_id'], $this->virtuemart_order_item_id, 'N'); //change status to "N" (reset stock) with old quantity
            	unset($oldTable);
            }
            
            $this->virtuemart_order_id = $data['order_id'];
	        $this->modified_on = $now;
	        $this->modified_by = $currentUser->id;
	        if (!$this->virtuemart_order_item_id){
	            $this->created_on = $now;
	            $this->created_by = $currentUser->id;
	        }
	        
	        //compute rest of prices (see recomputeOrder function comments to explain/guess of prices meanings)
	        $this->product_basePriceWithTax = $this->product_item_price + $this->product_tax;
	        $this->product_final_price = $this->product_basePriceWithTax - $this->product_subtotal_discount;
	        
	        //since VM 2.0.11, product_tax is stored per-item (in older versions it was for item*quantity)
			NbordersHelper::importVMFile('version.php');
			$taxPerItem = class_exists('vmVersion') ? (version_compare(vmVersion::$RELEASE, '2.0.11') >= 0) : true; 
	        $this->product_tax = $taxPerItem ? $this->product_tax : ($this->product_tax*$this->product_quantity); //VM2 since version 2.0.11: tax is stored for 1 item
	        
	        $this->product_subtotal_discount = $this->product_subtotal_discount * $this->product_quantity;
	        $this->product_subtotal_with_tax = $this->product_basePriceWithTax * $this->product_quantity + $this->product_subtotal_discount;
	        
	        $this->order_item_currency = null;
	        if (!trim($this->product_attribute))
	        	$this->product_attribute = null;
        	//store item
        	parent::store($updateNulls = false);
        	//die;
            $stored[] = $this->virtuemart_order_item_id;


            $newStatuses[$this->virtuemart_order_item_id] = !empty($data['order_status'][$i]) ? $data['order_status'][$i] : (!empty($data['status']) ? $data['status'] : 'P'); //status not selected: use order status, if not selected, use pending
        }
        
        //items to delete:

        //change item status to "Cancelled" (to adjust stock) and than delete products
        $this->_db->setQuery('SELECT virtuemart_product_id, virtuemart_order_item_id FROM `#__virtuemart_order_items` WHERE `virtuemart_order_id` = ' .(int) $data['order_id'] . ($stored ? ' AND `virtuemart_order_item_id` NOT IN (' . implode(',', $stored) . ')' : ''));
        if ($itemsDelete = $this->_db->loadObjectList())
        	foreach ($itemsDelete as $itemDelete)
        		$this->changeItemStatus((int)$data['order_id'], $itemDelete->virtuemart_order_item_id, 'X');
        		
        //delete items
        $this->_db->setQuery('DELETE FROM `#__virtuemart_order_items` WHERE `virtuemart_order_id` = ' . (int)$data['order_id'] . ($stored ? ' AND `virtuemart_order_item_id` NOT IN (' . implode(',', $stored) . ')' : ''));
        $this->_db->execute();

        //now update order status for edited/added items
        $this->_db->setQuery('SELECT virtuemart_order_item_id FROM `#__virtuemart_order_items` WHERE `virtuemart_order_id` = ' . (int)$data['order_id']);
        
        foreach ($this->_db->loadColumn() as $orderItemId)
        	if (isset($newStatuses[$orderItemId]))
        		$this->changeItemStatus((int)$data['order_id'], $orderItemId, $newStatuses[$orderItemId]);     

        return true;
    }
    
    /**
     * Use VM code for change item status. Or just update stock when quantity changes.
     */
    function changeItemStatus($orderId, $orderItemId, $newStatus)
    {
    	$formerView = JRequest::getVar('view');
		JRequest::setVar('view','orders'); //this is also for VM checkFilterDir function
							
    	NbordersHelper::importVMFile('helpers/vmmodel.php');
    	NbordersHelper::importVMFile('models/orders.php');
    	NbordersHelper::importVMFile('tables/order_items.php');
    	NbordersHelper::importVMFile('tables/orders.php');
    	$model = VmModel::getModel('orders');
    	
    	$input = array();
    	$input[$orderItemId] = array('order_status' => $newStatus);
    		
		foreach ($input as $key=>$value) {

			if (!isset($value['comments'])) $value['comments'] = '';

			$data = (object)$value;
			$data->virtuemart_order_id = $orderId;

			$model->updateSingleItem((int)$key, $data);
		}
		
		JRequest::setVar('view',$formerView);
    }
    
}

?>