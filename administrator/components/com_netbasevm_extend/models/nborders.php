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

jimport('joomla.application.component.model');

class NetBaseVm_ExtendModelNborders extends JModelLegacy
{
    
    var $_data = null;
    var $_pagination = null;

    function __construct ()
    {
        //global $mainframe, $option;
        $mainframe = JFactory::getApplication();
        $option = JRequest::getString('option');
        parent::__construct();
        /*
        $array = JRequest::getVar('cid', 0, '', 'array');
        $this->setId((int) $array[0]);
        */
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = $mainframe->getUserStateFromRequest($option . 'limitstart', 'limitstart', 0, 'int');
        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
    }

    /*
    function setId ($id)
    {
        // Set id and wipe data
        $this->_id = $id;
        $this->_data = null;
    }
	*/
    function getData ()
    {
        $this->_data = $this->_getList($this->_buildQuery(), $this->getState('limitstart'), $this->getState('limit'));
        return $this->_data;
    }

    function getPagination ()
    {
        if (empty($this->_pagination)) {
            jimport('joomla.html.pagination');
            $total = $this->_getListCount($this->_buildQuery(true));
            $this->_pagination = new JPagination($total, $this->getState('limitstart'), $this->getState('limit'));
        }
        
        return $this->_pagination;
    }

    function _buildQuery ($countOnly = false)
    {
    	$params = NbordersHelper::getParams();
    	
    	//get search values
    	$start_date = strtotime(JRequest::getVar('filter_start_date')); //user input dates are converted to utc by strtotime. orders are in utc
    	$start_date2 = JRequest::getVar('filter_start_date');
    	$end_date = strtotime(JRequest::getVar('filter_end_date'));
    	$end_date2 = JRequest::getVar('filter_end_date');
    	
    	$order_status = (array)JRequest::getVar('filter_order_status');
    	$id = JRequest::getVar('filter_id');
    	$inv_prefix = JRequest::getVar('filter_inv_prefix');
    	$inv_no = JRequest::getVar('filter_inv_no');
    	$name = JRequest::getVar('filter_name'); 
    	$email = JRequest::getVar('filter_email'); 

    	//echo $start_date2.'-'.$end_date2;
    	
    	//build search conditions
    	$where=array();
    	
    	$joinMailsended = $countOnly ? false : true; //join VM invoice table?
    	$joinOrderUserInfo = $countOnly ? false : true; //join order user info table ?

    	if(COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
    	{
    		/*
    		if ($this->_id)
	            $where[] = 'o.virtuemart_order_id = '.(int)$id;
	        */
    		if ($start_date2 == $end_date2 && $start_date2){
    			$where[] = 'o.created_on > "'.$start_date2.'"';
    			$where[] = 'o.created_on <= \''.gmdate('Y-m-d H:i:s',(int)$end_date+86400).'\'';
    		}
    		else {
	    	if ($start_date2)
	    		//$where[] = 'o.created_on >= \''.gmdate('Y-m-d H:i:s',(int)$start_date).'\'';
	    		$where[] = 'o.created_on > "'.$start_date2.'"';
	    	if ($end_date2)
	    		$where[] = 'o.created_on <= \''.gmdate('Y-m-d H:i:s',(int)$end_date+86400).'\'';
	    		//$where[] = 'o.created_on <= "'.$end_date2.'"';
    		}
	    	
	    	
	    	if (isset($id) AND $id>=0)
	    		//$where[] ='o.virtuemart_order_id = '.(int)$id.' OR o.order_number='.$this->_db->Quote($id);
	    		$where[] ='o.virtuemart_order_id like "%'.$id.'%"';

	        if (isset($inv_no) AND !empty($inv_no)) {
	        	if ($params->get('order_number')=='own'){
		        	//$where[] = 'ms.order_no ='.$this->_db->Quote($inv_no); $joinMailsended = true;
	        		$where[] = 'ms.order_no like "%'.$inv_no.'%"';
	        	}
		        else
		        	$where[] = 'o.virtuemart_order_id  ='.(int)$inv_no.' OR o.order_number = '.$this->_db->Quote($inv_no);
	        }
	        if (isset($email) AND !empty($email)){
	        	$email = $this->_db->escape($email);
	            $where[] = "i.email LIKE '%$email%'";
	            $joinOrderUserInfo = true;
	        }

    	}
    	else
    	{
    		/*
	        if ($this->_id)
	            $where[] = 'o.order_id = '.(int)$id;
	        */
	    	if (isset($start_date) AND $start_date>0)
	    		$where[] = 'o.cdate > '.(int)$start_date;
	    	if (isset($end_date) AND $end_date>0)
	    		$where[] = 'o.cdate <= '.((int)$end_date);
	    	if (isset($id) AND $id>0)
	    		$where[] = 'o.order_id = '.(int)$id;
	        if (isset($inv_no) AND !empty($inv_no)) {
	        	if ($params->get('order_number')=='own'){
		        	$where[] = 'ms.order_no ='.$this->_db->Quote($inv_no); $joinMailsended = true;}
		        else
		        	$where[] = 'o.order_id  ='.$this->_db->Quote($inv_no).' OR o.order_number = '.$this->_db->Quote($inv_no);
	        }
	        if (isset($email) AND !empty($email)){
	        	$email = $this->_db->escape($email);
	            $where[] = "i.user_email LIKE '%$email%'";
	            $joinOrderUserInfo = true;
	        }
	    }
	    
    	if (!empty($order_status)) { //common for VM 1 and 2
	        $statusCond=array();
	      	foreach ($order_status as $status)
	        	$statusCond[]='o.`order_status` = '.$this->_db->Quote($status);
	        $where[] = '('.implode(' OR ',$statusCond).')';
	    }
	   	if (isset($inv_prefix) AND !empty($inv_prefix)){
	    	$where[] = 'ms.order_prefix LIKE \'%'.$this->_db->escape($inv_prefix).'%\''; 
	    	$joinMailsended = true;}
	    if (isset($name) AND !empty($name)){
	       //$name = $this->_db->escape($name);
	       $where[] = "(i.first_name LIKE '%$name%' OR i.last_name LIKE '%$name%')";

		   $joinOrderUserInfo = true;
	    }
	        
        $where = count($where)>0 ? " WHERE (" . implode(') AND (', $where).')' : '';
        if(COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
        	$query = "SELECT 
        	".($countOnly ? "o.virtuemart_order_id" : "o.*, o.virtuemart_order_id AS order_id, 
        	ms.order_id as 'order_mailed', ms.order_mailed, ms.dn_mailed, ms.order_date, 
             i.first_name, i.last_name, i.company, i.email, o.created_on AS created_on , o.modified_on AS modified_on ")." 
    		FROM `#__virtuemart_orders` AS o 
    		".($joinOrderUserInfo ? ' LEFT JOIN `#__virtuemart_order_userinfos` AS i ON (o.`virtuemart_order_id` = i.`virtuemart_order_id` AND i.`address_type` = \'BT\') ' : '')."
    		".($joinMailsended ? ' LEFT JOIN `#__nborders_mailsended` AS ms ON (o.`virtuemart_order_id` = ms.`order_id`) ' : '')."    		
    	    $where 
    	    ".(!$countOnly ? "ORDER BY o.`virtuemart_order_id` DESC" : "");
        else
        	$query = "SELECT 
        	".($countOnly ? "o.order_id" : " o.*, ms.order_id as 'order_mailed', ms.order_mailed, ms.dn_mailed, ms.order_date, 
             i.first_name, i.last_name, i.company, i.user_email AS email")." 
    		FROM `#__vm_orders` AS o 
    		".($joinOrderUserInfo ? ' LEFT JOIN `#__vm_order_user_info` AS i ON (o.`order_id` = i.`order_id` AND i.`address_type` = \'BT\') ' : '')."
    		".($joinMailsended ? ' LEFT JOIN `#__nborders_mailsended` AS ms ON (o.`order_id` = ms.`order_id`) ' : '')."	
    	    $where 
    	    ".(!$countOnly ? "ORDER BY o.`order_id` DESC" : "");

        //NOTE: removed group by, because it slows down query and probability that there will be more than 1  billing address is ... small.
        //GROUP BY o.virtuemart_order_id
        //GROUP BY o.order_id
        
		
    	
    	    
        return $query;
    }
    
    
    /**
     * Update order's states, using POST values.
     * 
     * @param mixed reference to 'order' model
     */
    public function updateStates(&$model)
    {
    	$mainframe = JFactory::getApplication();
    	
        //load variables
    	$this->getData();
    	$postStatuses = JRequest::getVar('status',array(),'post','array');
    	$postNotify = JRequest::getVar('notify',array(),'post','array');
    	$success = 0;
    	//$model = JControllerLegacy::getModel('order','VMInvoice');
    	foreach ($this->_data as $order)
    	{
    		$update = $model->updateState($order->order_id, $postStatuses[$order->order_id], isset($postNotify[$order->order_id]) ? $postNotify[$order->order_id] : 'N','',false,true); //for all products also
    		
    		if ($update===true)
    			$success++;
    		elseif ($update===false)
    			$mainframe->enqueueMessage(JText::_('COM_NETBASEVM_EXTEND_COULD_NOT_CHANGE_STATUS_OR_SEND_NOTIFY_E-MAIL_AT_ORDER_').$order->order_id, 'error');
    	}

    	if ($success==1)
    		$mainframe->enqueueMessage(JText::sprintf('Order status successfully changed'));
    	elseif ($success>1)
    		$mainframe->enqueueMessage(JText::sprintf('Order\'s statuses successfully changed',$success));
    	
    }
    
    /**
     * Add/Edit single invoice number from Invoices view
     */
	public function editInvoiceNo()
    {
    	$db = JFactory::getDBO();
    	$mainframe = JFactory::getApplication();
    	$params = NbordersHelper::getParams();
       
    	//check posted values
    	$postSubmits = JRequest::getVar('update_inv_no',null,'post','array');
    	$postInvoiceNo = JRequest::getVar('order_no',null,'post','array');
    	
    	@$orderNo = key($postSubmits)*1;
    	if (count($postSubmits)!=1 OR !isset($postInvoiceNo[$orderNo])){
    		$mainframe->enqueueMessage(JText::sprintf('Error in submit values.'));return false;}
    		
    	//get prefix
    	if ( $params->get('allow_prefix_editing',0)==1){ //from post
    		$postInvoicePrefix = JRequest::getVar('order_prefix',null,'post','array');
    		$invoicePrefix = $postInvoicePrefix[$orderNo];
    	}
    	else //we dont edit prefixes - load "old" prefix from db, if not yet or empty, use default
    	{
    		$db->setQuery("SELECT id,order_prefix FROM #__nborders_mailsended WHERE order_id=".(int)$orderNo);
    		$oldInv = $db->loadObject();
    		if (!empty($oldInv) AND !empty($oldInv->order_prefix))
    			$invoicePrefix = $oldInv->order_prefix;
    		else
    			$invoicePrefix = $params->get('number_prefix');
    	}
    	
        if (!InvoiceGetter::checkOrderExists($orderNo)){
    		$mainframe->enqueueMessage(JText::sprintf('Order not found.'));return false;}
        
    	$invoiceNo = $postInvoiceNo[$orderNo] * 1;

    	//check if invoice no already exists
    	if (!empty($invoiceNo)) {
	        $db->setQuery("SELECT id FROM #__nborders_mailsended WHERE NOT (order_id=".(int)$orderNo.") AND order_no=".(int)$invoiceNo." AND order_prefix=".$db->Quote($invoicePrefix));
	    	$db->execute();
	    	if ($db->getNumRows()>0){
	    		$mainframe->enqueueMessage(JText::_('COM_NETBASEVM_EXTEND_THIS_INVOICE_NUMBER_IS_ALREADY_IN_DB'), 'error');
	    		return false;}
    	}
    	 
    	//build query
    	$db->setQuery("SELECT id FROM #__nborders_mailsended WHERE order_id=".(int)$orderNo);
    	$db->execute();
    	$numrows = $db->getNumRows();
    	if ($numrows==1){ //update
    		 if (empty($invoiceNo))
    		 	return false; //no number posted, don't change
    		$mailSendedId = $db->loadResult();
    	 	$db->setQuery("UPDATE #__nborders_mailsended SET order_no=".$invoiceNo.",order_prefix=".$db->Quote($invoicePrefix)." WHERE id=".(int)$mailSendedId);
    	}
    	elseif($numrows==0){ //create
    	    if (empty($invoiceNo)) //no number posted, create new
    			$invoiceNo = $this->getNewInvoiceNo($invoicePrefix);
    	    $db->setQuery("INSERT INTO `#__nborders_mailsended` (`order_id`,`order_no`,`order_prefix`) VALUES ('$orderNo', '$invoiceNo',".$db->Quote($invoicePrefix).")");
    	}
    	else
    		return false;
    		
    	//execute
        if ($db->execute()){
    		$mainframe->enqueueMessage(JText::sprintf('Invoice number successfully changed')); return true;}
    	else {
    		$mainframe->enqueueMessage(JText::_('COM_NETBASEVM_EXTEND_INVOICE_NUMBER_UPDATE_ERROR'), 'error'); return false;}	
    }
    
    public function editInvoiceDate()
    {
    	$db = JFactory::getDBO();
    	$mainframe = JFactory::getApplication();
    	$params = NbordersHelper::getParams();
       	//print_r($_POST);die;
    	//check posted values
    	$postSubmits = JRequest::getVar('update_inv_date',null,'post','array');
    	$postInvoiceDates = JRequest::getVar('order_date',null,'post','array');
    	
    	@$orderNo = key($postSubmits)*1;
		
		//echo $postSubmits;die;
		
    	if (count($postSubmits)!=1 OR !isset($postInvoiceDates[$orderNo])){
		//if (count($postSubmits)!=1){
    		$mainframe->enqueueMessage(JText::sprintf('Error in submit values.'));return false;}
    		
    	if (!$newDate = NbordersHelper::gmStrtotime($postInvoiceDates[$orderNo])) {
    		$mainframe->enqueueMessage(JText::_('COM_NETBASEVM_EXTEND_NOT_ENTERED_PROPER_DATE'), 'error'); return false;}
    	
    	if (NbordersHelper::getInvoiceNo($orderNo)!=false) //for all cases, to create rpw/check if we have right to update row
    		$db->setQuery('UPDATE #__nborders_mailsended SET order_date='.(int)$newDate.',order_lastchanged='.time().' WHERE order_id='.(int)$orderNo.' LIMIT 1');

    	//execute
        if ($db->execute()){
    		$mainframe->enqueueMessage(JText::sprintf('Invoice date successfully changed')); return true;}
    	else {
    		$mainframe->enqueueMessage(JText::_('COM_NETBASEVM_EXTEND_INVOICE_DATE_CHANGE_ERROR'), 'error'); return false;}		
    }
    /**
     * Get new invoice number with given prefix
     * 
     * @param text $invoicePrefix. if not passed, default prefix from config is checked.
     */
    public function getNewInvoiceNo($invoicePrefix=false)
    {
    	if (!$invoicePrefix){
    		$params = NbordersHelper::getParams();
    		$invoicePrefix = $params->get('number_prefix');}
    	
    	$params = NbordersHelper::getParams();
		$startNo = $params->get('start_number');
        
         // find last number
         $db = JFactory::getDBO();
         $db->setQuery('SELECT MAX(`order_no`) FROM `#__nborders_mailsended` WHERE `order_prefix`='.$db->Quote($invoicePrefix));
         $no = $db->loadResult();
         // set next number
         return ($no < $startNo) ? $startNo : ++$no;
    }
    
    
}
?>