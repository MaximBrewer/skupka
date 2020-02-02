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

class VMInvoiceControllerConfig extends JControllerLegacy
{

    function __construct ($config = array())
    {
        parent::__construct($config);
        $this->registerTask('add', 'edit');
    }

    function display ($cachable = false, $urlparams = false)
    {
        JRequest::setVar('view', 'config');
        parent::display($cachable, $urlparams);
    }

    function save ()
    {
        $model = $this->getModel('config');
        if ($model->store()){
            $msg = JText::_('COM_VMINVOICE_MSG_CONFIG_SAVED');
        	$link = 'index.php?option=com_vminvoice&controller=config&type='.JRequest::getVar('type','general');
       	 	$this->setRedirect($link, $msg);
        }
        else {
            $msg = JText::_('COM_VMINVOICE_MSG_CONFIG_SAVE_ERROR').': '.$model->getError();
       		JError::raiseWarning(0, $msg);
       		return $this->display();
        }
    }

    function cancel ()
    {        
        $link = 'index.php?option=com_vminvoice';        
        $this->setRedirect($link);   
    }
    
    function template_restore()
    {
 		$model = $this->getModel('config');
    	if ($model->restoreTemplate()){
    		$msg = JText::_('COM_VMINVOICE_INVOICE_TEMPLATE_WAS_RESTORED');
    		$type='message';
    	
    		$db = JFactory::getDBO();
    		$db->setQuery('UPDATE #__vminvoice_config SET last_appearance_change='.time().' WHERE id=1');
    		$db->execute();
    	}
    	else{
    		$msg = JText::_('COM_VMINVOICE_INVOICE_TEMPLATE_WAS_NOT_RESTORED').': '.$model->getError();
    		$type='error';}   
    		
        $this->setRedirect('index.php?option=com_vminvoice&controller=config&type='.JRequest::getVar('type','general'),$msg,$type);   
    }
    
    function template_dn_restore()
    {
 		$model = $this->getModel('config');
    	if ($model->restoreTemplate('dn_')){
    		$msg = JText::_('COM_VMINVOICE_DELIVERY_NOTE_TEMPLATE_WAS_RESTORED');
    		$type='message';
    		
    	    $db = JFactory::getDBO();
    		$db->setQuery('UPDATE #__vminvoice_config SET last_appearance_change='.time().' WHERE id=1');
    		$db->execute();
    	}
    	else{
    		$msg = JText::_('COM_VMINVOICE_DELIVERY_NOTE_TEMPLATE_WAS_NOT_RESTORED').': '.$model->getError();
    		$type='error';}   
    		
        $this->setRedirect('index.php?option=com_vminvoice&controller=config&type='.JRequest::getVar('type','general'),$msg,$type);   
    }
    
    //just dev function. maybe could be in program also.
    function makerestore($dn='')
    {
    	$model = $this->getModel('config');
    	if ($model->makeRestore('dn_') && $model->makeRestore()){
    		$msg = JText::_('COM_VMINVOICE_RESTORE_POINT_WAS_CREATED');
    		$type='message';}
    	else{
    		$msg = JText::_('COM_VMINVOICE_RESTORE_POINT_WAS_NOT_CREATED');
    		$type='error';}
    		
        $this->setRedirect('index.php?option=com_vminvoice&controller=config&type='.JRequest::getVar('type','general'),$msg,$type);   
        
    }
}
