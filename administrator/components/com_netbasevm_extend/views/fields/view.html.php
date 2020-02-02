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

defined('_JEXEC') or die('Restrict Access');

jimport('joomla.application.component.view');

class VMInvoiceViewFields extends VmViewAdmin
{

    function display ($tpl = null)
    {        
    	InvoiceHelper::setSubmenu(4);
    	
        JToolBarHelper::title('VM Invoice: ' . JText::_('COM_VMINVOICE_FIELDS'), 'fields');        
        //JToolBarHelper::back(JText::_('COM_VMINVOICE_BACK'));
        JToolBarHelper::cancel('cancel', JText::_('COM_VMINVOICE_CLOSE'));
        JToolBarHelper::save('save', JText::_('COM_VMINVOICE_SAVE'));
        
        $fields =  $this->get('Data');
        $this->fields=$fields;
        
        parent::display($tpl);
    }

}
?>