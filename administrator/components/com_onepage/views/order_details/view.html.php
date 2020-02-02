<?php
/*
*
* @copyright Copyright (C) 2007 - 2010 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

	defined( '_JEXEC' ) or die( 'Restricted access' );
	jimport('joomla.application.component.view');
	
	if (!class_exists('OnepageTemplateHelper'))
	require ( JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'export_helper.php');

	

	
	//if(!class_exists('VmView'))require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vmview.php');
	
	class JViewOrder_details extends OPCView
	{
		function display($tpl = null)
		{

		
			//VM2 CODE HERE
			//Load helpers
		  if (!class_exists('VmConfig'))
		  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		  VmConfig::loadConfig(); 
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'javascript.php');
		
		OPCJavascript::loadJquery();  		
		
		if (!class_exists('CurrencyDisplay'))
			require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'currencydisplay.php');

		if (!class_exists('VmHTML'))
			require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'html.php');

		if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DIRECTORY_SEPARATOR.'vmpsplugin.php');
		$orderStatusModel=VmModel::getModel('orderstatus');
		$orderStates = $orderStatusModel->getOrderStatusList();

		//$this->SetViewTitle( 'ORDER');

		$orderModel = VmModel::getModel('orders');

		$curTask = JRequest::getWord('task', 'edit');
		$lang = JFactory::getLanguage();
		$extension = 'com_onepage';
		$base_dir = JPATH_SITE;
		$language_tag = 'en-GB';
		$reload = true;
		$lang->load($extension, $base_dir); //, $language_tag, $reload);
		
		
		{
			VmConfig::loadJLang('com_virtuemart');
			VmConfig::loadJLang('com_virtuemart_shoppers',TRUE);
			VmConfig::loadJLang('com_virtuemart_orders', true);

			// Load addl models
			$userFieldsModel = VmModel::getModel('userfields');
			$productModel = VmModel::getModel('product');

			// Get the data
			$virtuemart_order_id = JRequest::getInt('virtuemart_order_id', JRequest::getVar('order_id'));
			$order = $orderModel->getOrder($virtuemart_order_id);

			$_orderID = $order['details']['BT']->virtuemart_order_id;
			$orderbt = $order['details']['BT'];
			$orderst = (array_key_exists('ST', $order['details'])) ? $order['details']['ST'] : $orderbt;
			$orderbt ->invoiceNumber = $orderModel->getInvoiceNumber($orderbt->virtuemart_order_id);

			$currency = CurrencyDisplay::getInstance('',$order['details']['BT']->virtuemart_vendor_id);
			
			$this->currency = $currency;

			$_userFields = $userFieldsModel->getUserFields(
					 'account'
					, array('captcha' => true, 'delimiters' => true) // Ignore these types
					, array('delimiter_userinfo','user_is_vendor' ,'username','password', 'password2', 'agreed', 'address_type') // Skips
			);

			$userfields = $userFieldsModel->getUserFieldsFilled(
					 $_userFields
					,$orderbt
					,'BT_'
			);

			$_userFields = $userFieldsModel->getUserFields(
					 'shipment'
					, array() // Default switches
					, array('delimiter_userinfo', 'username', 'email', 'password', 'password2', 'agreed', 'address_type') // Skips
			);

			$shipmentfields = $userFieldsModel->getUserFieldsFilled(
					 $_userFields
					,$orderst
					,'ST_'
			);

			// Create an array to allow orderlinestatuses to be translated
			// We'll probably want to put this somewhere in ShopFunctions...
			$_orderStatusList = array();
			foreach ($orderStates as $orderState) {
				//$_orderStatusList[$orderState->virtuemart_orderstate_id] = $orderState->order_status_name;
				//When I use update, I have to use this?
				$_orderStatusList[$orderState->order_status_code] = JText::_($orderState->order_status_name);
			}

			$_itemStatusUpdateFields = array();
			$_itemAttributesUpdateFields = array();
			foreach($order['items'] as $_item) {
				$_itemStatusUpdateFields[$_item->virtuemart_order_item_id] = JHTML::_('select.genericlist', $orderStates, "item_id[".$_item->virtuemart_order_item_id."][order_status]", 'class="selectItemStatusCode"', 'order_status_code', 'order_status_name', $_item->order_status, 'order_item_status'.$_item->virtuemart_order_item_id,true);

			}

			if(!isset($_orderStatusList[$orderbt->order_status])){
				if(empty($orderbt->order_status)){
					$orderbt->order_status = 'unknown';
				}
				$_orderStatusList[$orderbt->order_status] = JText::_('COM_VIRTUEMART_UNKNOWN_ORDER_STATUS');
			}

			/* Assign the data */
			$this->assignRef('orderdetails', $order);
			$this->assignRef('orderID', $_orderID);
			$this->assignRef('userfields', $userfields);
			$this->assignRef('shipmentfields', $shipmentfields);
			$this->assignRef('orderstatuslist', $_orderStatusList);
			$this->assignRef('itemstatusupdatefields', $_itemStatusUpdateFields);
			$this->assignRef('itemattributesupdatefields', $_itemAttributesUpdateFields);
			$this->assignRef('orderbt', $orderbt);
			$this->assignRef('orderst', $orderst);
			$this->assignRef('virtuemart_shipmentmethod_id', $orderbt->virtuemart_shipmentmethod_id);

			/* Data for the Edit Status form popup */
			$_currentOrderStat = $order['details']['BT']->order_status;
			// used to update all item status in one time
			$_orderStatusSelect = JHTML::_('select.genericlist', $orderStates, 'order_status', '', 'order_status_code', 'order_status_name', $_currentOrderStat, 'order_items_status',true);
			$this->assignRef('orderStatSelect', $_orderStatusSelect);
			$this->assignRef('currentOrderStat', $_currentOrderStat);

			/* Toolbar */
			//JToolBarHelper::custom( 'prevItem', 'back','','COM_VIRTUEMART_ITEM_PREVIOUS',false);
			//JToolBarHelper::custom( 'nextItem', 'forward','','COM_VIRTUEMART_ITEM_NEXT',false);
			//JToolBarHelper::divider();
			//JToolBarHelper::custom( 'cancel', 'back','back','back',false,false);
		}
		
		{
			if(!class_exists('calculationHelper')) require(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'calculationh.php');

			$this->assignRef('orderstatuses', $orderStates);

			$model = VmModel::getModel('orders');
			$orderId = JRequest::getString('orderId', '');
			$orderLineItem = JRequest::getVar('orderLineId', '');
			$this->assignRef('virtuemart_order_id', $orderId);
			$this->assignRef('virtuemart_order_item_id', $orderLineItem);

			$orderItem = $model->getOrderLineDetails($orderId, $orderLineItem);
			$this->assignRef('orderitem', $orderItem);
		}
		

		

			//END OF VM2CODE
			
			$order_id = JRequest::getInt('order_id');
			if (empty($order_id)) die('Empty order id'); 
			
			//$limit = JRequest::getVar('limit', $mainframe->getCfg('list_limit'));
			//limitstart = JRequest::getVar('limitstart', 0);
			$model = $this->getModel('order_details');
			$this->order = $model->getOrderVM2($order_id); 
			$this->shippingmethods = $model->getShippingMethods($this->order); 
			$this->payments = $model->getPaymentMethods($this->order); 
			
			$this->next_order = $model->getNext($virtuemart_order_id); 
			$this->prev_order = $model->getPrev($virtuemart_order_id); 
			
			JPluginHelper::importPlugin('vmshipment');
			JPluginHelper::importPlugin('vmshopper');
			JPluginHelper::importPlugin('vmpayment');

			$ehelper = new OnepageTemplateHelper();
			$templates = $ehelper->getExportTemplates('ALL');

			$templates = $model->getTemplates();
			$order_data = $model->getOrderData();
		    
		    //$ehelper = new OnepageTemplateHelper($order_id);
			
			$this->assignRef('ehelper', $ehelper);
			$this->assignRef('templates', $templates);
			$this->assignRef('order_data', $order_data);
			jimport('joomla.html.pagination');
			parent::display($tpl); 
		}
		
	 public function getUrl()
	 {
	    $url = Juri::root(); 
		if (substr($url, -1) != '/') $url .= '/'; 
		return $url; 
	 }
	}
