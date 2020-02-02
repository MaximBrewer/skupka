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

class NetBaseVm_ExtendControllerNborders extends JControllerLegacy
{

    function __construct ($config = array())
    {
        parent::__construct($config);
        $this->registerTask('add', 'edit');
        $this->registerTask('addNewNbOrders','editOrder');
        
         NbordersHelper::createInvoiceNos(); //make sure all invoice numbers are created
    }

    function display($cachable = false, $urlparams = false)
    {
    	$redirect=false;
	
    	//update invoice number
    	if (!is_null(JRequest::getVar('update_inv_no',null,'post','array'))){
    		$this->getModel('nborders')->editInvoiceNo();
    		$redirect=true;}
		    			
    	//update invoice date
    	if (!is_null(JRequest::getVar('update_inv_date',null,'post','array'))){
    		$this->getModel('nborders')->editInvoiceDate();
    		$redirect=true;}
 		
    	//update orders states
    	if (!is_null(JRequest::getVar('update',null,'post','array'))){
    		$this->getModel('nborders')->updateStates($this->getModel('order'));
			$redirect=true;}
                        
		if ($redirect){
	    	$link = 'index.php?option=com_netbasevm_extend&controller=nborders';
	        //append form filters to url
    		foreach (JRequest::get() as $key => $var)
	    		if (preg_match('/^filter_/i',$key))
	    		{
		    		if (is_array($var)) //probably status
		    			foreach ($var as $val)
		    				$link.='&'.$key.'[]='.urlencode($val);
		    		elseif (!is_null($var) AND $var!='')
		    			$link.='&'.$key.'='.urlencode($var);
	    		}
                        
    		$this->setRedirect($link);
		}
		else{
       		JRequest::setVar('view', 'nborders');    
        	parent::display($cachable, $urlparams);}
                 
    }
    

    function editOrder(){  	
    	$cid = JRequest::getVar('cid',array(0));
    	JRequest::setVar('view', 'order');
        parent::display();
    	
    }
    
    function batch()
    {
    	$params = NbordersHelper::getParams();
    	
    	$message = null;
    	$msgType ='message'; //type of return message
    	$link = 'index.php?option=com_netbasevm_extend&controller=nborders';
    	
    	$action = JRequest::getVar('batch');
    	if (empty($action)){JError::raiseWarning(0,'Select batch action');$this->display();exit;}
    	
    	if (JRequest::getVar('batch_select')=='all_filtered'){
	    	//get all filtered orders
	    	$model = $this->getModel('nborders');
	    	$model->setState('limitstart',0);
	    	$model->setState('limit',0);
	    	$orders = $model->getData(); 
	    	
	    	$cids = array();
	    	foreach ($orders as $order)
	    		$cids[] = $order->order_id;
	    	JRequest::setVar('cid',$cids);
	    	
    	}
    	elseif (JRequest::getVar('batch_select')!='selected_list')
    		{JError::raiseWarning(0,'Select batch selection option');$this->display();exit;}
    	
    	$cids=(array)JRequest::getVar('cid');

    	if (count($cids)>0){
    		
    		asort($cids); //sort ids to have oldest orders first (especialy when creating new invoices)
    		
	    	switch ($action)
	    	{
	    		case 'download':
	    			if ($params->get('delivery_note')==1 && JRequest::getVar('batch_download_option','invoice')=='dn')
	    				$task = 'pdf_dn';
	    			else
	    				$task = 'pdf';
	    			$this->$task();
	    			break;
	    			
	    		case 'mail':
	    			$force = JRequest::getInt('batch_mail_force',0)==1 ? true : false;
	    			if ($params->get('delivery_note')==1 && $params->get('send_both',1)==0 && JRequest::getVar('batch_mail_option','invoice')=='dn')
	    				$this->mailSelected($force,true); 
	    			else
	    				$this->mailSelected($force);
	    			break;
	    			
	    		case 'create_invoice':
	    			$created=0;
	    			foreach ($cids as $cid)
	    				if (NbordersHelper::getInvoiceNo($cid)===false)
	    					if (NbordersHelper::getInvoiceNo($cid,true)>0) //force create invoice number
	    						$created++;
	    			$message = JText::sprintf('COM_NETBASEVM_EXTEND_INVOICES_CREATED',$created);
	    			break;
	    			
	    		case 'generate':
	    			$generated=0;
	    			$force = JRequest::getInt('batch_generate_force',0)==1 ? true : false; //generate also already generated invoices
	    			foreach ($cids as $cid){
	    				if (NbordersHelper::getInvoiceNo($cid)!==false){
	    					if ($force){
	    						if (NbordersHelper::generatePDF($cid,true,false,true)!==false)
	    							$generated++;
	    					}
	    					elseif (NbordersHelper::canUseActualPDF($cid)===false)
	    						if (NbordersHelper::generatePDF($cid,true)!==false)
	    							$generated++;
	    							
	    					if ($params->get('delivery_note')==1){
	    						if ($force){
	    							if (NbordersHelper::generatePDF($cid,true,true,true)!==false)
	    								$generated++;
	    						}
	    						elseif (NbordersHelper::canUseActualPDF($cid,true)===false)
	    							if (NbordersHelper::generatePDF($cid,true,true)!==false)
	    								$generated++;
	    					}
	    				}
	    			}
	    			
	    			$message = JText::sprintf('COM_NETBASEVM_EXTEND_PDFS_GENERATED',$generated);
	    			break;

	    		case 'change_status':
	    			if (!JRequest::getVar('batch_status',false))
	    				{JError::raiseWarning(0,'Select batch status');$this->display();exit;}
	    			$model = $this->getModel('order');
	    			$updated = 0;
	    			foreach ($cids as $cid)
	    				$updated = $model->updateState($cid, JRequest::getVar('batch_status'), JRequest::getVar('batch_notify_customer','N'),'',false,true)==true ? $updated+1 : $updated;
	    			$model = $this->getModel('nborders');	
	    			$states = InvoiceGetter::getOrderStates();
	    			$message = JText::sprintf('COM_NETBASEVM_EXTEND_ORDERS_STATES_CHANGED',$updated,$states[JRequest::getVar('batch_status')]->name);
	    			break;
	    			
	    		case 'delete':
	    			
	    	    	//delete files
    				foreach ($cids as $cid){
    					if (file_exists($invoiceFile = NbordersHelper::getInvoiceFile($cid)))
    						unlink($invoiceFile);
    					if (file_exists($dnFile = NbordersHelper::getDeliveryNoteFile($cid)))
    						unlink($dnFile);	
    				}
    				
	    			//delete from mailsended table
	    			$db = JFactory::getDBO();
    				$db->setQuery('DELETE FROM #__nborders_mailsended WHERE order_id IN ('.implode(',',$cids).')');
    				if ($db->execute())
    					$message = JText::sprintf('COM_NETBASEVM_EXTEND_INVOICES_DELETED',$db->getAffectedRows());

	    			if (!isset($message)){
	    				$message = JText::_('COM_NETBASEVM_EXTEND_ERROR_DURING_DELETION');
	    				$msgType='error';}
	    			break;
	    			
	    		default:
					$message = JText::_('COM_NETBASEVM_EXTEND_BATCH_PROCESS_NOT_DEFINED');
					$msgType='error';
	    			break;
	    	}
    	}
    	else{
    		$message=JText::_('COM_NETBASEVM_EXTEND_NO_ORDERS_TO_PROCESS');
    		$msgType='warning';
    	}

    	//append form filters to url
    	foreach (JRequest::get() as $key => $var)
    		if (preg_match('/^filter_/i',$key))
    		{
	    		if (is_array($var)) //probably status
	    			foreach ($var as $val)
	    				$link.='&'.$key.'[]='.urlencode($val);
	    		elseif (!is_null($var) AND $var!='')
	    			$link.='&'.$key.'='.urlencode($var);
    		}
        $this->setRedirect($link, $message, $msgType);
    } 
    
    function pdf ()
    {
        $cid = JRequest::getVar('cid', 0);
        NbordersHelper::generatePDF($cid, false);
    }

    function pdf_dn ()
    {
        $cid = JRequest::getVar('cid', 0);
        NbordersHelper::generatePDF($cid, false, true);
    }

    function pdfSelected ()
    {
        $orderIDs = JRequest::getVar('cid', 0);
        // sort IDs from oldest to newest
        sort($orderIDs);

        NbordersHelper::generatePDF($orderIDs);
    }

    /**
     * Send mails with invoices/dn to selected orders
     * @param bool	 $force		true = send mails always, even if is already sent; false  = send only mails that are not sent yet
     * @param bool	 $onlyDN	if yes, sent only delivery note, if no, send invoice (or both, if set in config to send both once)
     */
    function mailSelected($force=true,$onlyDN=false) 
    {   
       
        $db = JFactory::getDBO();
    	
        $mainframe = JFactory::getApplication();
        $orderIDs = JRequest::getVar('cid', 0);
        $sendBoth = NbordersHelper::getSendBoth();
        $msg = '';

        if ($onlyDN && NbordersHelper::getParams()->get('delivery_note')==0)
        	return false; //if delivery notes not allowed
        	
        // sort IDs from oldest to newest
        sort($orderIDs);
        $sent = 0;
       
        // send orders one by one
        foreach ($orderIDs as $orderID) {
        	
        	//determine what to send
        	if ($onlyDN){
        		$sendInvoice = false;
        		$sendDN = true;
        	}
        	else {
	        	$sendInvoice = true;
	        	$sendDN = $sendBoth;
        	}

	        if (!$force){
	        	$db->setQuery('SELECT * FROM #__nborders_mailsended WHERE order_id = '.(int)$orderID);

                        $sentDb = $db->loadObject();

	        	if (!empty($sentDb)){
	        		if ($sendInvoice && $sentDb->invoice_mailed==1)
	        			$sendInvoice = false;
	        		if ($sendDN && $sentDb->dn_mailed==1)
	        			$sendDN = false;	
	        	}
	        }

	        if (!$sendInvoice AND !$sendDN)
	        	continue;
	        
            // generate invoice
            if ($sendInvoice)
            	if (!NbordersHelper::generatePDF($orderID, true))
            		continue; //if not generated pdf (e.g. haven't order number), skip mail
            	
            // generate delivery note
            if ($sendDN)
            	if (!NbordersHelper::generatePDF($orderID, true, true))
            		continue;
           
            if (NbordersHelper::sendMail($orderID, $sendInvoice, $sendDN)===true)
            	$sent++;
            else
                $mainframe->enqueueMessage(JText::sprintf('COM_NETBASEVM_EXTEND_MSG_MAIL_ORDERID_SENT_ERROR', $orderID), 'error');
        }
        if ($sent==1)
        	$mainframe->enqueueMessage(JText::sprintf('COM_NETBASEVM_EXTEND_MSG_MAIL_ORDERID_SENT', $orderID), 'info');
        elseif ($sent>0)
        	$mainframe->enqueueMessage(JText::sprintf('COM_NETBASEVM_EXTEND_MSG_MAILS_SENT', $sent), 'info');
        
        $link = 'index.php?option=com_netbasevm_extend&controller=nborders';
        $this->setRedirect($link);
    }    
    
    function send_mail ()
    {
        $cid = JRequest::getInt('cid', 0);
        $sendBoth = NbordersHelper::getSendBoth();

        // generate and send invoice
        if (NbordersHelper::generatePDF($cid, true)!=false)
        {
            // generate and send delivery note
	        if ($sendBoth)
	        	if (NbordersHelper::generatePDF($cid, true, true)===false){
	        		JError::raiseWarning(0,JText::_('COM_NETBASEVM_EXTEND_DELIVERY_NOTE_NOT_GENERATED'));
	        		$sendBoth=false;}

	        $mail = NbordersHelper::sendMail($cid, true, $sendBoth);
	                
	        if ($mail===true) {
	            $msg = JText::_('COM_NETBASEVM_EXTEND_MSG_MAIL_SENT');
	            $type="message";
	        } else {
	            $msg = JText::_('COM_NETBASEVM_EXTEND_MSG_MAIL_SENT_ERROR');
	            $type="error";
	        }
        }
        else{
        	$msg = JText::_('COM_NETBASEVM_EXTEND_MSG_MAIL_SENT_ERROR')." ".JText::_('COM_NETBASEVM_EXTEND_INVOICE_NUMBER_NOT_GENERATED_YET');
        	$type="error";
        }
        
       
		$link = 'index.php?option=com_netbasevm_extend&controller=nborders';
        $this->setRedirect($link, $msg, $type);
    }

    function send_delivery_note ()
    {
        $cid = JRequest::getVar('cid', 0);
        // generate delivery note
        if (NbordersHelper::generatePDF($cid, true, true)!=false)
        {
	        $mail = NbordersHelper::sendMail($cid, false, true);
	        if ($mail===true) {
	            $msg = JText::_('COM_NETBASEVM_EXTEND_MSG_MAIL_SENT');
	            $type="message";
	        } else {
	            $msg = JText::_('COM_NETBASEVM_EXTEND_MSG_MAIL_SENT_ERROR');
	            $type="error";
	        }
	        
        }
        else{
        	$msg = JText::_('COM_NETBASEVM_EXTEND_MSG_MAIL_SENT_ERROR')." ".JText::_('COM_NETBASEVM_EXTEND_INVOICE_NUMBER_NOT_GENERATED_YET');
        	$type="error";
        }

        $link = 'index.php?option=com_netbasevm_extend&controller=nborders';
        $this->setRedirect($link, $msg, $type);
    }

    function cancel ()
    {
        $link = 'index.php?option=com_netbasevm_extend&controller=nborders';
        $this->setRedirect($link);
    }

}
?>