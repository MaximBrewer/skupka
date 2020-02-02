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

jimport('joomla.application.component.controller');

class NetBaseVm_ExtendControllerOrder extends JControllerLegacy
{

    function __construct($config = array())
    {
        parent::__construct($config);
        $this->registerTask('userajax', 'display');
        $this->registerTask('orderajax', 'display');
    }

    function couponajax()
    {
    	header('Content-Type: text/xml; charset=UTF-8');
    	$model = $this->getModel('order');
    	echo $model->getAjaxCoupon(JRequest::getVar('coupon'),JRequest::getVar('currency'));
    	exit;
    }
    
    function statesajax()
    {
    	header('Content-Type: text/xml; charset=UTF-8');
    	
    	$states = InvoiceGetter::getStates(JRequest::getVar('country_id'));
    	foreach ($states as $state)
    	{
    		echo '<option value="'.$state->id.'">'.$state->name.'</option>'."\n";
    	}
    	
    	exit;
    }
     
    function whisper()
    {
        $type = JRequest::getString('type');
        $model = $this->getModel('order');      
        /* @var $model VMInvoiceModelOrder */
        die(JHTML::_('select.genericlist', $model->getAjaxList(JRequest::getString('str'), $type), 'naseptavac', 'multiple="multiple" onclick="getClickHandler($(\'' . $type . '\'));" onchange="getChangeHandler($(\'' . $type . '\'));"', 'id', 'name'));
    }

    function display($cachable = false, $urlparams = false)
    {
        JRequest::setVar('view', 'order');
        parent::display($cachable, $urlparams);
    }

    function apply()
    {
        $this->save(true);
    }

    function save($apply = false)
    {
        $model = $this->getModel('order');
        /* @var $model VMInvoiceModelOrder */
        ////can edit here turn off
        $requestpost= JRequest::get('post',4);
        $id = $model->save($requestpost); //4 = allow HTML
        if ($apply)
            $this->setRedirect('index.php?option=com_netbasevm_extend&controller=nborders&task=editOrder&cid=' . $id, JText::_('COM_NETBASEVM_EXTEND_ORDER_SAVED'));
        else
            $this->cancel('Order saved');
    }

    function cancel($msg = 'Order edit canceled')
    {
        $this->setRedirect('index.php?option=com_netbasevm_extend&controller=nborders', JText::_($msg));
    }
}

?>