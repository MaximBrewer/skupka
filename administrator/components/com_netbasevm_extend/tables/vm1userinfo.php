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

class TableVm1UserInfo extends JTable
{
    var $user_info_id = null;
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
    var $cdate = null;
    var $mdate = null;
    var $perms = null;
    var $bank_account_nr = null;
    var $bank_name = null;
    var $bank_sort_code = null;
    var $bank_iban = null;
    var $bank_account_holder = null;
    var $bank_account_type = null;

    function __construct(&$db)
    {
        parent::__construct('#__vm_user_info', 'user_info_id', $db);
    }
    
    function bind(&$data,$type=false)
    {
    	if (!$type)
    		return parent::bind($data);
    	
        $addressTypes = array('ST' => 'S_' , 'BT' => 'B_');

        if (empty($data[$addressTypes[$type].'user_info_id'])) //new user info, generate user info id and address name
        { 
        	$data[$addressTypes[$type].'user_info_id'] = md5( uniqid( 'VirtueMartIsCool' ));
        	
        	$this->_db->setQuery( 'SELECT * FROM #__vm_user_info WHERE user_id = '.(int)$data['user_id'].' AND address_type_name=\'-default-\' AND address_type='.$this->_db->Quote($type));
        	$this->_db->execute(); //only one -default- for user and addres type
        	$data[$addressTypes[$type].'address_type_name'] = $this->_db->getNumRows()>0 ? JText::_('COM_VMINVOICE_NEW_ADDRESS') : '-default-';
        }
        
        //BIND INFO COLUMNS
        //get all columns
        $this->_db->setQuery('SHOW COLUMNS FROM `#__vm_user_info`');
        $vars = $this->_db->loadColumn(0);    
          
		//load VM framework (for handling user fields)
		global $mosConfig_absolute_path;
		InvoiceHelper::importVMFile('classes/ps_userfield.php'); 
		
		//bind right B_/S_ variables to table
        foreach ($vars as $column){ 
        	$fieldName = $addressTypes[$type].$column;

        	if (!isset($data[$fieldName]))
        		$data[$fieldName]  = null;
        	
			//get all non-system user fields for shipping or/and fields that are defined in VMInvoice config. (same as in order model)
			$extras='';
			$params = InvoiceHelper::getParams(); 
			
			foreach (range(1,4) as $i)
				if ($extra_field = $params->get('extra_field'.$i))
					$extras .= " OR fieldid=".$this->_db->Quote($extra_field);
	
            //if it is user field, get his type
            $this->_db->setQuery('SELECT `type` FROM `#__vm_userfield` WHERE `name`='.$this->_db->Quote($column).' AND ((`type`!=\'delimiter\' AND `shipping`=1 AND `sys`=0 AND `published`=1)'.$extras.')');
        		
            if ($userfieldType = $this->_db->loadResult()){ //use VM function to sanitise it's values (e.g. checkboxes)
            	
            	if (is_null($data[$fieldName]) && in_array($userfieldType,array('checkbox','multicheckbox')))
            		$data[$fieldName] = ''; //default value e.g. for checkboxes
            		
            	if (!is_null($data[$fieldName]))
               		$data[$fieldName] = ps_userfield::prepareFieldDataSave($userfieldType,$fieldName,$data[$fieldName]);
            }
               	
            if (isset($data[$fieldName]))
            	$this->$column = $data[$fieldName];
        }
        
    	if (!$this->address_type_name)
    		$this->address_type_name =  null; //important
    	$this->address_type = $type;
    	$this->user_id = $data['user_id'];
    }
    
    /**
     * Override default store method to work with no-autoincrement primary key
     * @see JTable::store()
     */
    function store($updateNulls=false)
    {
    	$k = $this->_tbl_key;
    	
    	$this->_db->setQuery( 'SELECT * FROM '.$this->_tbl.' WHERE '.$k.' = '.$this->_db->Quote($this->$k));
		$this->_db->execute();
		
		if( $this->_db->getNumRows()>0)
		{
			$ret = $this->_db->updateObject( $this->_tbl, $this, $this->_tbl_key, $updateNulls );
		}
		else
		{
			$ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
		}
		if( !$ret )
		{
			$this->setError(get_class( $this ).'::store failed - '.$this->_db->getErrorMsg());
			return false;
		}
		else
		{
			return true;
		}
    }
    
	/**
	 * Override default delete method to work with string primary key
	 */
	function delete( $oid=null )
	{
		//if (!$this->canDelete( $msg ))
		//{
		//	return $msg;
		//}

		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k =  $oid ;
		}

		$query = 'DELETE FROM '.$this->_db->quoteName( $this->_tbl ).
				' WHERE '.$this->_tbl_key.' = '. $this->_db->Quote($this->$k);
		$this->_db->setQuery( $query );

		if ($this->_db->query())
		{
			return true;
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
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