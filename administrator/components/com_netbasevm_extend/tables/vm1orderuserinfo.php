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

class TableVm1OrderUserInfo extends JTable
{
    var $order_info_id = null;
    var $order_id = null;
    var $user_id = null;
    var $address_type = null;
    var $address_type_name = null;
    var $company = null;
    var $title = null;
    var $last_name = null;
    var $first_name = null;
    var $middle_name = null;
    var $phone_1 = null;
    var $phone_2 = null;
    var $fax = null;
    var $address_1 = null;
    var $address_2 = null;
    var $city = null;
    var $state = null;
    var $country = null;
    var $zip = null;
    var $user_email = null;
    var $extra_field_1 = null;
    var $extra_field_2 = null;
    var $extra_field_3 = null;
    var $extra_field_4 = null;
    var $extra_field_5 = null;
    var $bank_account_nr = null;
    var $bank_name = null;
    var $bank_sort_code = null;
    var $bank_iban = null;
    var $bank_account_holder = null;
    var $bank_account_type = null;

    function __construct(&$db)
    {
        parent::__construct('#__vm_order_user_info', 'order_info_id', $db);
    }

    function store(&$data)
    {
        $addressTypes = array('S_' => 'ST' , 'B_' => 'BT');
        
    	$rename = array(  //if we are using other "inner" name than db name
    		'user_email'=>'email'
    	);
    	
        //get all user info columns
        $this->_db->setQuery('SHOW COLUMNS FROM `#__vm_order_user_info`');
        $vars = $this->_db->loadColumn(0);      
        
		//load VM framework (for handling user fields)
		global $mosConfig_absolute_path;
		InvoiceHelper::importVMFile('classes/ps_userfield.php');

        foreach ($addressTypes as $requestPrefix => $addressTypeCode) {

            foreach ($vars as $column){
            	
            	$varname = isset($rename[$column]) ? $rename[$column] : $column;
            	
            	if (!isset($data[$requestPrefix.$varname]))
        			$data[$requestPrefix.$varname] = null;

				//get all non-system user fields for shipping or/and fields that are defined in VMInvoice config. (same as in order model)
				$extras='';
				$params = InvoiceHelper::getParams(); 
				foreach (range(1,4) as $i)
					if ($extra_field = $params->get('extra_field'.$i))
						$extras .= " OR fieldid=".$this->_db->Quote($extra_field);
							
                //if it is user field, get his type
                $this->_db->setQuery('SELECT `type` FROM `#__vm_userfield` WHERE `name`='.$this->_db->Quote($column).' AND ((`type`!=\'delimiter\' AND `shipping`=1 AND `sys`=0 AND `published`=1)'.$extras.')');
               	if ($userfieldType = $this->_db->loadResult()){ //use VM function to sanitise values (e.g. for checkboxes)
               		
               		$origVal = $data[$requestPrefix.$varname];
               		
               		if (is_null($data[$requestPrefix.$varname]) && in_array($userfieldType,array('checkbox','multicheckbox')))
						$data[$requestPrefix.$varname] = ''; //if not checked checbox = empty default value. Other NO, because they are not presented in form.
						
					if (!is_null($data[$requestPrefix.$varname]))
               			$data[$requestPrefix.$varname] = ps_userfield::prepareFieldDataSave($userfieldType,$requestPrefix.$varname,$origVal);
               	}
               	
                $this->$column = $data[$requestPrefix . $varname];
            }
            
            $this->_db->setQuery('SELECT `order_info_id` FROM `#__vm_order_user_info` WHERE `order_id` = ' . (int) $data['order_id'] . ' AND `address_type` = ' . $this->_db->Quote($addressTypeCode));
            $this->order_info_id = (int) $this->_db->loadResult();
            if (!$this->order_info_id)
            	$this->order_info_id = null;
            $this->order_id = $data['order_id'];
            $this->user_id = $data['user_id'];
            $this->address_type = $addressTypeCode;
            
         	if ($addressTypeCode=='ST' && $data['billing_is_shipping']){ //delete row (if exists) instead of saving
         		if ($this->order_info_id)
        			$this->delete();
        	}
        	else
            	parent::store();
        }
        
        return true;
    }
}

?>
