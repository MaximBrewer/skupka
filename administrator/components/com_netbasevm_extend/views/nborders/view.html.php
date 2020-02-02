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

defined('_JEXEC') or ('Restrict Access');
// Load the view framework
if(version_compare(VM_RELEASE, '3.0.0', 'ge')){
	if(!class_exists('VmView'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmviewadmin.php');
}
else{
	if(!class_exists('VmView'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmview.php');
}

Jimport('joomla.application.component.view');
class NetBaseVm_ExtendViewNborders extends VmViewAdmin
{

    function display ($tpl = null)
    {

    	$this->addHelperPath(array(JPATH_VM_ADMINISTRATOR.DS.'helpers'));
		// Load the helper(s)
		$this->loadHelper('html');
    	
        JToolBarHelper::title('Netbase VM Extend: ' . JText::_('COM_NETBASEVM_EXTEND_ORDERS_MANAGEMENT'), 'invoices');
        JToolBarHelper::cancel('cancel', 'COM_NETBASEVM_EXTEND_CLOSE');
        JToolBarHelper::addNew('addNewNbOrders');

        $params = NbordersHelper::getParams();
        $this->delivery_note = $params->get('delivery_note');
        $this->order_numbering = $params->get('order_number');
        $this->prefix_editing = $params->get('allow_prefix_editing',0);
        $this->default_prefix = $params->get('number_prefix','');
        $this->pagination = $this->get('Pagination');
        $this->invoices = $this->get('Data');
        $this->statuses = InvoiceGetter::getOrderStates();   
        
        $db =  JFactory::getDBO();
        	
        //set additioanl invoice variables
		if(!empty($this->invoices))
		{
			foreach ($this->invoices as &$invoice)
			{
				
				if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2){
					$invoice->cdate = NbordersHelper::gmStrtotime($invoice->created_on);
					$invoice->mdate = NbordersHelper::gmStrtotime($invoice->modified_on);
				}
				
				
							
				//get invoice numbers.
				$invoice->invoiceNoFull = NbordersHelper::getInvoiceNo($invoice->order_id); //full value 
				
				//load nos and prefixes after getInvoiceNo, because could be created new invoice numbers
				$db->setQuery("SELECT `order_no`, `order_prefix`, `order_date` FROM `#__nborders_mailsended` WHERE `order_id` = ".(int)$invoice->order_id);
				$invoiceNos = $db->loadObject();
	
				$invoice->invoiceDate = !empty($invoiceNos) ? $invoiceNos->order_date : false;
				$invoice->invoiceNoPrefix = !empty($invoiceNos) ? (is_null($invoiceNos->order_prefix) ? $this->default_prefix : $invoiceNos->order_prefix) : false; //prefix
				$invoice->invoiceNoDb = !empty($invoiceNos) ? $invoiceNos->order_no : false; //value without prefix
				
				$invoice->generated = NbordersHelper::canUseActualPDF($invoice->order_id,false);
				
                                if ($this->delivery_note==1)
					$invoice->generatedDN = NbordersHelper::canUseActualPDF($invoice->order_id,true);
			}
		}

        $this->newInoviceNo = $this->get('NewInvoiceNo');
      
        JPluginHelper::importPlugin('vminvoice');
        $this->dispatcher = JDispatcher::getInstance();
       
        //$this->addToolbar();
        parent::display($tpl);
        
    }


}
?>