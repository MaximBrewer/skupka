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

defined('_JEXEC') or die();
jimport('joomla.application.component.model');

class VMInvoiceModelFields extends JModelLegacy //after edit toolbar,open new form in form.php in (view vminvoice)
{

    function __consturct ()   
    {        
        parent::__construct();
        //$array = JRequest::getVar('cid', 0, '', 'array');
        $this->setId(1);    
    }

    function setId ($id)    
    {        
        $this->_id = $id;       
        $this->_data = null;    
    }

    function &getData ()    
    {        
        if (empty($this->_data)) {
            $query = ' SELECT * FROM #__vminvoice_additional_field WHERE id = 1';            
            $this->_db->setQuery($query);            
            $this->_data = $this->_db->loadObject();        
        }
        
        if (! $this->_data) {            
            $this->_data = new stdClass();            
            $this->_data->id = 0;            
            $this->_data->bank_name = null;            
            $this->_data->account_nr = null;            
            $this->_data->bank_code_no = null;            
            $this->_data->iban = null;            
            $this->_data->vat_id = null;            
            $this->_data->registration_court = null;            
            $this->_data->phone = null;            
            $this->_data->email = null;            
            $this->_data->web_url = null;            
            $this->_data->note_start = null;            
            $this->_data->note_end = null;
            $this->_data->pdf_logo = null;        
        }
        
        return $this->_data;
    }

    function store ()    
    {
        $row =  $this->getTable();
        //$data = JRequest::get('post');

        $input=JFactory::getApplication()->input;
        $data=$input->post;
        /*
		if ($_FILES["pdf_logo"]["name"] )
		{			
			$dest=JPATH_SITE."/administrator/components/com_vminvoice/tcpdf/images/pdf_logo.jpg";
			$src=$_FILES['pdf_logo']['tmp_name'];
			move_uploaded_file($src,$dest);
		}
		*/
        
        if (! $row->bind($data)) {            
            $this->setError($this->_db->getErrorMsg());            
            return false;        
        }
       
          
        if (! $row->check()) {            
            $this->setError($this->_db->getErrorMsg());            
            return false;        
        }
        
        $row->note_start = JRequest::getVar('note_start', '', 'post', 'string', JREQUEST_ALLOWRAW);
        $row->note_end = JRequest::getVar('note_end', '', 'post', 'string', JREQUEST_ALLOWRAW);
        

	   //determine if there was any real change
	   $originalData = $this->getData();
	   foreach ($originalData as $key => $value)
	     	if (isset($row->$key) AND $originalData->$key!=$row->$key AND $key!="last_fields_change")
	       		 $row->last_fields_change=time();
	         
        
        if (! $row->store()) {            
            $this->setError($row->getErrorMsg());            
            return false;        
        }
        
        return true;    
    }

}
?>