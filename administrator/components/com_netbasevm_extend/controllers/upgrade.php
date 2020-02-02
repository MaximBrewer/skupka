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

defined('_JEXEC') or die('Direct Access');
jimport('joomla.application.component.controller');

class VMInvoiceControllerUpgrade extends JControllerLegacy
{

    function __construct ($config = array())
    {
        parent::__construct($config);
    }

    function display ($cachable = false, $urlparams = false)
    {
        JRequest::setVar('view', 'upgrade');
        parent::display($cachable, $urlparams);
    }

    function save () //? 
    {
        $model = $this->getModel('upgrade');
        if ($model->store($post)) {
            $msg = JText::_('COM_VMINVOICE_MSG_CONFIG_SAVED');
        } else {
            $msg = JText::_('COM_VMINVOICE_MSG_CONFIG_SAVE_ERROR');
        }
        
        $model->update_tmpl();
        $link = 'index.php?option=com_vminvoice&controller=upgrade';
        $this->setRedirect($link, $msg);
    }

    function cancel ()
    {        
        $link = 'index.php?option=com_vminvoice';        
        $this->setRedirect($link);   
    }
    
    function doUpgrade()
    {
        JRequest::checkToken() or jexit( 'Invalid Token' );
        
        $model =  $this->getModel('upgrade');
        $result = $model->upgrade();
        $model->setState('result', $result);
        
        $view =  $this->getView('upgrade', 'html');
        $view->setModel($model, true);
        $view->showMessage();
    }

}
