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

class TableVm2UserInfo extends JTable
{
    var $virtuemart_userinfo_id = null;
    var $virtuemart_user_id = null;
    var $address_type = null;
    var $address_type_name = null;
    var $name = null;
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
    var $virtuemart_state_id = null;
    var $virtuemart_country_id = null;
    var $zip = null;
    var $created_on = null;
    var $created_by = null;
    var $modified_on = null;
    var $modified_by = null;
    
    function __construct(&$db)
    {
        parent::__construct('#__virtuemart_userinfos', 'virtuemart_userinfo_id', $db);
    }
    
    function save(&$data,$type=false)
    {
    	if (!$type)
    		return parent::bind($data);
    	
    	$currentUser = JFactory::getUser();
    	
    	$rename = array(  //if we are using other "inner" name than db name
    		'virtuemart_state_id'=>'state', 
    		'virtuemart_country_id'=>'country',
    		'virtuemart_userinfo_id'=>'user_info_id'
    	);
    	
        $addressTypes = array('ST' => 'S_' , 'BT' => 'B_');

        if (empty($data[$addressTypes[$type].'user_info_id'])) { //new user info, generate address name
        
        	$data[$addressTypes[$type].'virtuemart_userinfo_id'] = null; //null primary key => new record
        	$this->_db->setQuery( 'SELECT * FROM #__virtuemart_userinfos WHERE virtuemart_user_id = '.(int)$data['user_id'].' AND address_type_name IS NULL  AND address_type='.$this->_db->Quote($type));
        	$this->_db->execute(); //only one -default- (NULL) address for user and address type combination
        	$data[$addressTypes[$type].'address_type_name'] = $this->_db->getNumRows()>0 ? JText::_('COM_VMINVOICE_NEW_ADDRESS') : null;
        }
        
        //BIND INFO COLUMNS
        //get all columns
        $this->_db->setQuery('SHOW COLUMNS FROM `#__virtuemart_userinfos`');
        $vars = $this->_db->loadColumn(0);    
          
		//load VM framework (for handling user fields)
		NbordersHelper::importVMFile('models/userfields.php');
		
		//bind right B_/S_ variables to table
        foreach ($vars as $column){

        	$varname = isset($rename[$column]) ? $rename[$column] : $column;
        	
        	$fieldName = $addressTypes[$type].$varname;

        	if (!isset($data[$fieldName]))
        		$data[$fieldName] = null;
        	
            //if it is custom user field, get his type
			$extras='';
			$params = NbordersHelper::getParams(); 
			foreach (range(1,4) as $i)
				if ($extra_field = $params->get('extra_field'.$i))
					$extras .= " OR virtuemart_userfield_id=".$this->_db->Quote($extra_field);
        	
            $this->_db->setQuery('SELECT `type` FROM `#__virtuemart_userfields` WHERE `name`='.$this->_db->Quote($column).' AND (shipment=1'.$extras.') AND type != \'delimiter\' AND type != \'captcha\'');
           
            if ($userfieldType = $this->_db->loadResult()) { //it is userfield
           
            	if (is_null($data[$fieldName]) && in_array($userfieldType,array('checkbox','multicheckbox')))
            		$data[$fieldName] = ''; //default value for checkboxes
            		
            	if (!is_null($data[$fieldName])){  //use VM function to sanitise it's values (e.g. checkboxes)
            		$params = array();

            		NbordersHelper::importVMFile('version.php');
            		 
            		if (class_exists('vmVersion') AND version_compare(vmVersion::$RELEASE, '2.0.16') >= 0) //since 2.0.16 function syntax changed
            		{
            			$field = (object)array(
            					'type' => $userfieldType,
            					'name' => $fieldName,
            					'params' => $params);
            		
            			$data[$fieldName] = VirtueMartModelUserfields::prepareFieldDataSave($field, $data);
            		}
            		else            		
               			$data[$fieldName] = VirtueMartModelUserfields::prepareFieldDataSave($userfieldType,$fieldName,$data[$fieldName],$data,$params);
            	
            	}
            }
            
           	$this->$column = $data[$fieldName];
        }
        
    	$this->address_type = $type;
    	if (!$this->address_type_name)
    		$this->address_type_name =  null; //important
    	$this->virtuemart_user_id = $data['user_id'];
        $this->created_on = !$this->virtuemart_userinfo_id ? gmdate('Y-m-d H:i:s') : null;
        $this->created_by = !$this->virtuemart_userinfo_id ? $currentUser->id : null;
        $this->modified_on = gmdate('Y-m-d H:i:s');
        $this->modified_by = $currentUser->id;
    }
    
	/**
	 * REALLY Resets the default properties. 
	 * @return	void
	 */
	function reset()
	{
		if(COM_VMINVOICE_ISJ16)
			return parent::reset();
			
		$k = $this->_tbl_key;
		foreach ($this->getProperties() as $name => $value)
		{
			if($name != $k)
			{
				$this->$name	= null;
			}
		}
	}
}

?>