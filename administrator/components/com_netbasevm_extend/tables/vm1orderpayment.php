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

class TableVm1OrderPayment extends JTable
{
    var $order_id = null;
    var $payment_method_id = null;
    var $order_payment_code = null;
    var $order_payment_number = null;
    var $order_payment_expire = null;
    var $order_payment_name = null;
    var $order_payment_log = null;
    var $order_payment_trans_id = null;

    /**
     * Create object. Set database connector. Set table name and primary key.
     * Table #__vm_order_payment hasn't primary key. Is used order_id and before storing is controled.
     * 
     * @param JDatabaseMySQL $db
     */
    function __construct(&$db)
    {
        parent::__construct('#__vm_order_payment', 'order_id', $db);
    }

    /**
     * Store object. Control if order_id exist in database to recognise update/insert operation.
     * 
     * @return booelan true/false - succed/unsucced 
     */
    function store()
    {
        $this->_db->setQuery('SELECT COUNT(*) FROM `#__vm_order_payment` WHERE `order_id` = ' . (int) $this->order_id);
        if ($this->_db->loadResult() == 0)
            $ret = $this->_db->insertObject($this->getTableName(), $this, $this->getKeyName());
        $ret = $this->_db->updateObject($this->getTableName(), $this, $this->getKeyName());
        
    	if( !$ret ){
			$this->setError(get_class( $this ).'::store failed - '.$this->_db->getErrorMsg());
			return false;
		}
		else {
			return true;
		}
    }
}

?>