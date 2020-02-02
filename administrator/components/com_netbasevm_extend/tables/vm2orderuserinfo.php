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
 
class TableVm2OrderUserInfo extends JTable
{
    var $virtuemart_order_userinfo_id = null;
    var $virtuemart_order_id = null;
    var $virtuemart_user_id = null;
    var $address_type = null;
    var $address_type_name = null;
    /* others are added dynamically */
    var $created_on = null;
    var $created_by = null;
    var $modified_on = null;
    var $modified_by = null;
    var $customer_note = null;

    function __construct(&$db)
    {
     
        parent::__construct('#__virtuemart_order_userinfos', 'virtuemart_order_userinfo_id', $db);
    }

    function store($updateNulls = false)
    {
        $data = $requestpost= JRequest::get('post',4);


        if($data['cid']==NULL)
        {
            $data['cid']=$data['tmp_order_id'];
            $data['order_id']=$data['tmp_order_id'];
            
        }
    	$currentUser = JFactory::getUser();
    	
        $addressTypes = array('S_' => 'ST' , 'B_' => 'BT');
        
    	$rename = array(  //if we are using other "inner" name than db name
    		'virtuemart_state_id'=>'state', 
    		'virtuemart_country_id'=>'country'
    	);
    	
        //get all user info columns
        $this->_db->setQuery('SHOW COLUMNS FROM `#__virtuemart_order_userinfos`');
        $vars = $this->_db->loadColumn(0);      
		//load VM framework (for checking user fields)
		NbordersHelper::importVMFile('models/userfields.php');
		
        foreach ($addressTypes as $requestPrefix => $addressTypeCode) {

        	$this->reset();

            foreach ($vars as $column){

            	$varname = isset($rename[$column]) ? $rename[$column] : $column;
            	
            	if (!isset($data[$requestPrefix.$varname]))
        			$data[$requestPrefix.$varname] = null;

				//if it is user field, get his type (must be shipping or some of extra fields)
            	$extras='';
				$params = NbordersHelper::getParams(); 
				foreach (range(1,4) as $i)
					if ($extra_field = $params->get('extra_field'.$i))
						$extras .= " OR virtuemart_userfield_id=".$this->_db->Quote($extra_field);

               	$this->_db->setQuery('SELECT `type` FROM `#__virtuemart_userfields` WHERE `name`='.$this->_db->Quote($column).' AND (shipment=1'.$extras.') AND type != \'delimiter\' AND type != \'captcha\'');
                if ($userfieldType = $this->_db->loadResult()) //use VM function to sanitise values (e.g. for checkboxes)
                {
                	if (is_null($data[$requestPrefix.$varname]) AND in_array($userfieldType,array('checkbox','multicheckbox')))
                		$data[$requestPrefix.$varname] = ''; //if not checked checbox = empty default value. Other NO, because they are not presented in form.
                		
                	if (!is_null($data[$requestPrefix.$varname])){
                		
              			$params = array();

              			NbordersHelper::importVMFile('version.php');
              			
              			if (class_exists('vmVersion') AND version_compare(vmVersion::$RELEASE, '2.0.16') >= 0) //since 2.0.16 function syntax changed
              			{
              				$field = (object)array(
              					'type' => $userfieldType, 
              					'name' => $requestPrefix.$varname, 
              					'params' => $params);

              				$data[$requestPrefix.$varname] = VirtueMartModelUserfields::prepareFieldDataSave($field, $data);
              			}
              			else
                			$data[$requestPrefix.$varname] = VirtueMartModelUserfields::prepareFieldDataSave($userfieldType,$requestPrefix.$varname,$data[$requestPrefix.$varname],$data,$params);
                	}
                }
                
                $this->$column = $data[$requestPrefix . $varname];
            }

            $this->_db->setQuery('SELECT `virtuemart_order_userinfo_id` FROM `#__virtuemart_order_userinfos` WHERE `virtuemart_order_id` = ' . (int) $data['order_id'] . ' AND `address_type` = ' . $this->_db->Quote($addressTypeCode));
            $formerID = $this->_db->loadResult();
            
            if (!$this->address_type)
            	$this->address_type = null;
            
            $this->virtuemart_order_userinfo_id = $formerID ? $formerID : null;
            $this->virtuemart_order_id = $data['order_id'];
            $this->virtuemart_user_id = $data['user_id'];
            $this->customer_note = $data['customer_note'];
            $this->address_type = $addressTypeCode;
            $this->modified_on = gmdate('Y-m-d H:i:s');
            $this->modified_by = $currentUser->id;
            $this->created_on = !$formerID ? gmdate('Y-m-d H:i:s') : null;
            $this->created_by = !$formerID ? $currentUser->id : null;

        	if ($addressTypeCode=='ST' && !empty($data['billing_is_shipping'])){ //delete row (if exists) instead of saving
        		if ($this->virtuemart_order_userinfo_id)
        			$this->delete();
        	}
        	else
            	parent::store();
        }
        
     	return true;
    }
}

?>
