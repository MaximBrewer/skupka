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

class TableVm1OrderItem extends JTable
{
    var $order_item_id = null;
    var $order_id = null;
    var $user_info_id = null;
    var $vendor_id = null;
    var $product_id = null;
    var $order_item_sku = null;
    var $order_item_name = null;
    var $product_quantity = null;
    var $product_item_price = null;
    var $product_final_price = null;
    var $order_item_currency = null;
    var $order_status = null;
    var $cdate = null;
    var $mdate = null;
    var $product_attribute = null;

    function __construct(&$db)
    {
        parent::__construct('#__vm_order_item', 'order_item_id', $db);
    }

    function store(&$data)
    {
    	$rename = array(  //if we are using other "inner" name than db name
    		'product_final_price'=>'product_price_with_tax'
    	);
    	
        $now = gmdate('Y-m-d H:i:s');
        $vars = get_object_vars($this);
        
        //get user info id for this order
        $db = JFactory::getDBO();
        $db->setQuery('SELECT user_info_id FROM #__vm_orders WHERE order_id = '.(int)$data['order_id']);
        $userInfoId = $db->loadResult();
        
        foreach ($data['order_item_id'] as $i => $orderItemId) { //items to update/add
            foreach ($vars as $param => $value)
                if ($param[0] != '_'){
                	$name = (isset($rename[$param]) ? $rename[$param] : $param);
                    $this->$param = @reset(array_slice($data[$name], $i, 1));}
            $this->cdate = $this->order_item_id != 0 ? null : $now;
            $this->order_id = $data['order_id'];
        	$this->user_info_id = $userInfoId;
        	$this->mdate = $now;
        	
        	if (empty($this->order_status))
        		$this->order_status = !empty($data['status']) ? $data['status'] : 'P'; //status not selected: use order status, if not set, "pending"
        	
        	$this->updateVMStock($this->product_quantity, $this->product_id, $this->order_item_id); //update VM stock
        	
        	//store items
        	parent::store();
            $stored[] = $this->order_item_id;
            
        }
        if (isset($stored)) { //items to delete

        	//update VM stock
        	$this->_db->setQuery('SELECT product_id, order_item_id FROM `#__vm_order_item` WHERE `order_id` = ' .(int) $data['order_id'] . ' AND `order_item_id` NOT IN (' . implode(',', $stored) . ')');
        	$itemsDelete = $this->_db->loadObjectList();
        	if (!empty($itemsDelete))
        		foreach ($itemsDelete as $itemDelete)
        			$this->updateVMStock(0, $itemDelete->product_id, $itemDelete->order_item_id); //write removed pieces back to VM stock
        	
        	//delete items
            $this->_db->setQuery('DELETE FROM `#__vm_order_item` WHERE `order_id` = ' . (int)$data['order_id'] . ' AND `order_item_id` NOT IN (' . implode(',', $stored) . ')');
            $this->_db->execute();
        }

        return true;
    }
    
    /**
     * Update VM product stock and sales. Needs to be called before ordered items DB change! (to get right number of current pieces)
     * 
     * @param int	 new quantity
     * @param int	 product id
     * @param int	 current edited order_item id (or null if new) - to get previous quantity
     */
    function updateVMStock($quantity, $product_id, $order_item_id = null)
    {
    	//if it is "own product"
    	if (empty($product_id))
    		return true;
    	
    	//get number of currently ordered pieces (existing item)
    	if (!empty($order_item_id)){
    		$this->_db->setQuery('SELECT product_quantity FROM `#__vm_order_item` WHERE `order_item_id` = '.(int)$order_item_id);
        	$count = $this->_db->loadResult();}
        
        //TODO: based on item state (cancelled, shipped)
        	
        //else row not exists (new item)
        if (empty($count))
        	$count = 0;
        
        if ($quantity!=$count)
        {
        	//update stock numbers
        	$diff = $quantity - $count;
        	$this->_db->setQuery('UPDATE #__vm_product SET'.
        	' product_in_stock = product_in_stock - '.$diff.','.
        	' product_sales = product_sales + '.$diff.
        	' WHERE product_id = '.(int)$product_id);
        	return $this->_db->query();
        }
    }
}

?>