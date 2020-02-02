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

class TableVm2OrderPayment extends JTable
{

    function checkTableExists()
    {
    	//check if table for payment plugin exists
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
    	$this->_tbl = '#__virtuemart_payment_plg_'.$plugin;
		$this->_db		= &$db;
    	
		if ($this->checkTableExists())
        	parent::__construct($this->_tbl, 'id', $db);
    }
    
    //store info about current payment
    function save($data)
    {
    	//delete previous records from all plugin tables (except current one). becuase can be only one record in one table.
    	$this->_db->setQuery('SHOW TABLES LIKE '.$this->_db->Quote(str_replace('#__',$this->_db->getPrefix(),'#__virtuemart_payment_plg_%')));
    	$pluginTables = $this->_db->loadColumn();
    	foreach ($pluginTables as $pluginTable)	{ //is with real db prefix
    		
    		$pluginTable = preg_replace('#^'.preg_quote($this->_db->getPrefix()).'#i','#__',$pluginTable);
    		
    		if ($pluginTable==$this->_tbl) //not delete record from current table (can be edited or new)
    			continue;
    			
    		$this->_db->setQuery('DELETE FROM '.$pluginTable.' WHERE virtuemart_order_id='.(int)$data['order_id']);
    		if (!$this->_db->execute())
    			JError::raiseWarning(0,'Could not delete former payment from '.$pluginTable);
    	}
    	
    	//check if table for payment plugin exists
		if (!$this->checkTableExists())
			return false;
			
       	$this->_db->setQuery('SELECT `id` FROM `'.$this->_tbl .'` WHERE `virtuemart_order_id` = ' . (int) $data['order_id']);
       	$formerID = $this->_db->loadResult();

		$paymentInfo = invoiceGetter::getPaymentMethod($data['payment_method_id']);
		$currencies = invoiceGetter::getCurrencies();

        $now = gmdate('Y-m-d H:i:s');
        $currentUser = JFactory::getUser();
       	
    	$this->id = $formerID ? $formerID : null;
    	$this->virtuemart_order_id = $data['order_id'];
    	$this->order_number = $data['order_number'];
    	$this->virtuemart_paymentmethod_id = $data['payment_method_id'];
    	$this->payment_name = '<span class="vmpayment_name">'.@$paymentInfo->payment_name.'</span><br />'.@$paymentInfo->payment_info; //? VM...
    	
    	if (isset($this->payment_order_total)) //test, because some payment table not have all cols
    		$this->payment_order_total = $data['order_total'];
    		
		if (isset($this->payment_order_total)){  //test, because some payment table not have all cols
    		$this->payment_currency = $data['order_currency'];
			foreach ($currencies as $currency)
				if ($data['order_currency']==$currency->id)
					$this->payment_currency = $currency->currency_code;
		}
		
		if (isset($this->cost_per_transaction)) 
    		$this->cost_per_transaction = @$paymentInfo->cost_per_transaction;
    		
    	if (isset($this->cost_percent_total)) 
    		$this->cost_percent_total = @$paymentInfo->cost_percent_total;
    		
    	if (isset($this->tax_id)) 
    		$this->tax_id = @$paymentInfo->tax_id;
    		
    	if (isset($this->modified_on)) 
    		$this->modified_on = $now;
    	if (isset($this->modified_by)) 
	    	$this->modified_by = $currentUser->id;
	    if (!$this->id){
	    	if (isset($this->created_on)) 
	        	$this->created_on = $now;
	        if (isset($this->created_by)) 
	        	$this->created_by = $currentUser->id;
	    }
	    
    	return parent::store();
    }
}

?>