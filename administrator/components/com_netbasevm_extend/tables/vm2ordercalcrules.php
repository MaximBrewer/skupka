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

class TableVm2OrderCalcRules extends JTable
{
    var $virtuemart_order_calc_rule_id = null;
    var $virtuemart_order_id = null;
    var $virtuemart_vendor_id = null;
    var $calc_rule_name = null;
    var $calc_kind = null;
    var $calc_amount = null;
    var $created_on = null;
    var $created_by = null;
    var $modified_on = null;
    var $modified_by = null;
    
    function __construct(&$db)
    {
        parent::__construct('#__virtuemart_order_calc_rules', 'virtuemart_order_calc_rule_id', $db);
        
        foreach (invoiceGetter::getOrderCalcRulesFields() as $field) //create object vars dynamically, can differ
        	$this->$field = null;
    }

    function save(&$data)
    {
    	$rename = array();
    	$stored = array();
        $now = gmdate('Y-m-d H:i:s');
        $currentUser = JFactory::getUser();
        $vars = get_object_vars($this);
        
        if (isset($data['calc_rule_name'])) foreach ($data['calc_rule_name'] as $ruleId => $calc_rule_name) { //items to update/add
        	
        	$this->virtuemart_order_calc_rule_id = $ruleId ? $ruleId : null;

            $this->_db->setQuery('SELECT virtuemart_order_calc_rule_id FROM `#__virtuemart_order_calc_rules` WHERE `virtuemart_order_id` = ' . (int)$data['order_id'] . ' AND `virtuemart_order_calc_rule_id` = '.(int)$ruleId);
        	
        	if (!$this->_db->loadResult()) //this key does not exists yet for THIS order => null, to make new row (because it can be primary key of rule at other order)
        		$this->virtuemart_order_calc_rule_id = null;

        	//former row exists, but not in initial array, it is some hidden row (f.e. shipping, payment)
        	if ($this->virtuemart_order_calc_rule_id && !isset($data['init_calc_rules'][$this->virtuemart_order_calc_rule_id]))
        		$this->virtuemart_order_calc_rule_id = null;
        	
        	if ($this->virtuemart_order_calc_rule_id)
        		$this->load(); //load row, if exists
        	
            foreach ($vars as $param => $value) //bind post values
                if ($param[0] != '_'){
                	$name = (isset($rename[$param]) ? $rename[$param] : $param);
                	if (isset($data[$name]))
                		$this->$param = htmlspecialchars_decode($data[$name][$ruleId]);}
                	
                	
            if (property_exists($this, 'calc_currency'))
            	$this->calc_currency = (int)$data['order_currency']; //set order's currency

            $this->virtuemart_order_id = $data['order_id'];
            $this->virtuemart_vendor_id = $data['vendor'];
	        $this->modified_on = $now;
	        $this->modified_by = $currentUser->id;
	        if (!$this->created_on){ //new record
	            $this->created_on = gmdate('Y-m-d H:i:s');
	            $this->created_by = $currentUser->id;
	        }

        	//store items
        	parent::store();
            $stored[] = $this->virtuemart_order_calc_rule_id;
        }

        //fer items to delete
        $this->_db->setQuery('SELECT * FROM `#__virtuemart_order_calc_rules` WHERE 
        		`virtuemart_order_id` = ' . (int)$data['order_id'].(count($stored) ? ' AND 
        		`virtuemart_order_calc_rule_id` NOT IN (' . implode(',', $stored) . ')' : '')); //this order and not just saved
        
        $delete = $this->_db->loadObjectList();
        $deleteIds = array();
        
        //when using extended (VM >= 2.0.12), dont delete rules pertaining to products 
        //delete only rules with amount > 0 (VM stores these automatically, for example to store info about shipment or payment tax, keep them)
        foreach ($delete as $rule) 
        	if (empty($rule->virtuemart_order_item_id) && (float)($rule->calc_amount)>0)
        		$deleteIds[] = $rule->virtuemart_order_calc_rule_id;
        
        //run delete
        if ($deleteIds){
       		$this->_db->setQuery('DELETE FROM `#__virtuemart_order_calc_rules` WHERE `virtuemart_order_calc_rule_id` IN ('.implode(',', $deleteIds).')');
        	if (!($this->_db->execute()))
        		JError::raiseWarning(0,'Cannot delete calc rules: '.$this->_db->getErrorMsg());
        }
        return true;
    }
}

?>