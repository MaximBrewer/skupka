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

class TableVm1Order extends JTable
{
    var $order_id = null;
    var $user_id = null;
    var $vendor_id = null;
    var $order_number = null;
    var $user_info_id = null;
    var $order_total = null;
    var $order_subtotal = null;
    var $order_tax = null;
    var $order_tax_details = null;
    var $order_shipping = null;
    var $order_shipping_tax = null;
    var $coupon_discount = null;
    var $coupon_code = null;
    var $order_discount = null;
    var $order_currency = null;
    var $order_status = null;
    var $cdate = null;
    var $mdate = null;
    var $ship_method_id = null;
    var $customer_note = null;
    var $ip_address = null;

    function __construct(&$db)
    {
        parent::__construct('#__vm_orders', 'order_id', $db);
    }

    function bind(&$data)
    {    	
        parent::bind($data);
        $this->order_status = $data['status'];			
        $this->vendor_id = $data['vendor'];

        if (!empty($data['S_user_info_id'])) //shipping user_info_id has priority
        	$this->user_info_id = $data['S_user_info_id'];
        elseif (!empty($data['B_user_info_id']))
        	$this->user_info_id = $data['B_user_info_id'];

        if (! $this->order_id)
            $this->cdate = time();
        $this->mdate = time();
        
        //build shipping method string. because db field has limit 255 chars, we ust cut long ratename.
        $this->ship_method_id = substr($data['custom_shipping_class'].'|'.$data['custom_shipping_carrier'].'|'.$data['custom_shipping_ratename'],0,230).'|'.$data['custom_shipping_costs'].'|'.$data['custom_shipping_id'];
        
        //get discounts
        $this->coupon_discount = - ($this->coupon_discount);        
        $this->order_discount = - ($this->order_discount + $data['order_payment']);
        	
    	//new order: generate new order number string (from ps_checkout.php function get_order_number())
    	if (empty($this->order_id)){
			$order_number = (!empty($this->user_id) ?  $this->user_id.'_' : ''). md5(session_id().(string)time());
			$this->order_number = substr($order_number, 0, 32);	
    	}
    }
}

?>