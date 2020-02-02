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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Direct Access');

jimport('joomla.application.component.controller');

class VMInvoiceControllerFields extends JControllerLegacy
{

    function __construct ($config = array())   
    {
        parent::__construct($config);        
        $this->registerTask('add', 'edit');
    }

    function display ($cachable = false, $urlparams = false)
    {        
        JRequest::setVar('view', 'fields');
        parent::display($cachable, $urlparams);
    }

    function save ()
    {        
        $model = $this->getModel('fields');
        
        if ($model->store($post)) {            
            $msg = JText::_('COM_VMINVOICE_MSG_FIELDS_SAVED');        
        } else {            
            $msg = JText::_('COM_VMINVOICE_MSG_FIELDS_SAVE_ERROR');        
        }
        
        $link = 'index.php?option=com_vminvoice&controller=fields';        
        $this->setRedirect($link, $msg);    
    }

    function cancel ()   
    {        
        $link = 'index.php?option=com_vminvoice';
        $this->setRedirect($link);    
    }

}
