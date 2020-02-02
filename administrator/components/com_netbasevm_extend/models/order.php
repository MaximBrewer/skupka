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

class NetBaseVm_ExtendModelOrder extends JModelLegacy
{
    /**
     * Order ID
     * 
     * @var int
     */
    var $orderID = null;
    /**
     * Order address type
     * 
     * @var string BT/ST - billing/shipping
     */
    var $addressType = null;
    /**
     * Order customer ID
     * 
     * @var int
     */
    var $userId = null;

    /**
     * Complete order store.
     * 
     * @param array $data request data
     * @return int order ID
     */
    function save(&$data)
    {
        $tPrefix = COM_NETBASEVM_EXTEND_ORDERS_ISVM2 ? 'vm2' : 'vm1';
    	$orderIdCol = COM_NETBASEVM_EXTEND_ORDERS_ISVM2 ? 'virtuemart_order_id' : 'order_id';
    	$userInfoCol = COM_NETBASEVM_EXTEND_ORDERS_ISVM2 ? 'virtuemart_userinfo_id' : 'user_info_id';
    	$newUser = false;
        if (empty($data['vendor'])) //guess vendor id, if not set
			if (count($vendors = InvoiceGetter::getVendors())==1)
				$data['vendor']=$vendors[0]->id;
		
    	if (!$data['user_id']){  //if create new Joomla! user. if creation fails, store order anyway, but don't update/insert vm_user_info
    		//create Joomla! user
    		if (!($data['user_id'] = $this->newJUser($data)))
    			$data['user_id'] = null;
    		else
    			$newUser = true; //success
    	}
       
    	//check if user "registered" also in VM
    	if ($data['user_id']){
	    	$user = JFactory::getUser($data['user_id']);
			if ($this->newVMShopper($data, $user) && !$newUser){ //if ONLY shopper created, display message about it
				$mainframe = JFactory::getApplication();
				$mainframe->enqueueMessage(JText::_('COM_NETBASEVM_EXTEND_SHOPPER_CREATED'));
			}			
    	}
    	
    	//store order
        $vmorder = $this->getTable($tPrefix.'order');
        $vmorder->bind($data,'order_status');  //IMPORTANT: NOT change status here. instead use updateStatus fnc at bottom of this function     
        if (!$vmorder->$orderIdCol)
        	$vmorder->order_status = 'P'; //for new orders, add them as PENDING. if selected f.e. confirmed, plugins adding points can be called now (?)

        if (!$vmorder->store(false)) //store
        	JError::raiseWarning(0, 'Cannot store order. '.$vmorder->getError());
        
 		if (!$data['order_id'] = $vmorder->$orderIdCol){ //if is created new order, write back it's id to data property
 			JError::raiseWarning(0,'Order table not stored');
 			return false;
 		}
 		
 		 			
 		$data['order_number'] = $vmorder->order_number;
 		
 		//store calculation rules (VM2)
 		if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2){
 			$vmordercalcrules = $this->getTable($tPrefix.'ordercalcrules');
 			if (!$vmordercalcrules->store($data))
 				JError::raiseWarning(0, 'Cannot store calculation rules. '.$vmordercalcrules->getError());
 		}

        //delete coupon, if set so
        if (!empty($data['coupon_code']) AND !empty($data['coupon_delete'])) 
        	$this->deleteCoupon($data['coupon_code']);

        //store items
        JRequest::setVar('tmp_order_id', $data['order_id']);
        $vmorderitem = $this->getTable($tPrefix.'orderitem');  
        if (!$vmorderitem->store($data))
        	JError::raiseWarning(0, 'Cannot store ordered items. '.$vmorderitem->getError());
 
        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM1){ //store payment (VM1)
	        $vmorderpayment = $this->getTable($tPrefix.'orderpayment');
	        $vmorderpayment->bind($data);
	        if (!$vmorderpayment->store())
	       		JError::raiseWarning(0, 'Cannot store order payment. '.$vmorderpayment->getError());
        }
        
        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2 && $data['payment_method_id']) { //store payment info (VM2)

        	//get name of plugin table used for store payment info
        	$this->_db->setQuery('SELECT payment_element FROM #__virtuemart_paymentmethods WHERE virtuemart_paymentmethod_id = '.(int)$data['payment_method_id']);

        	if ($element = $this->_db->loadResult()){
        		
        		include_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_netbasevm_extend' . DS . 'tables'.DS.'vm2orderpayment.php';
	        	$vmorderpayment = new TableVm2OrderPayment($this->_db,$element); //cannot use $this->getTable because of constructor param
	        	if (!$vmorderpayment->store($data))
	        		JError::raiseWarning(0, 'Cannot store order payment. '.$vmorderpayment->getError());
        	}
        }
        
        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2 && $data['shipment_method_id']) { //store shipment info (VM2)

        	//get name of plugin table used for store payment info
        	$this->_db->setQuery('SELECT shipment_element FROM #__virtuemart_shipmentmethods WHERE virtuemart_shipmentmethod_id = '.(int)$data['shipment_method_id']);

        	if ($element = $this->_db->loadResult()){
        		
        		include_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_netbasevm_extend' . DS . 'tables'.DS.'vm2ordershipment.php';
	        	$vmordershipment = new TableVm2OrderShipment($this->_db,$element); //cannot use $this->getTable because of constructor param
	        	if (!$vmordershipment->store($data))
	        		JError::raiseWarning(0, 'Cannot store order shipment. '.$vmordershipment->getError());
        	}
        }

        //store default user info / get address type name
        if ($data['user_id']) {

        	$vmuserinfo = $this->getTable($tPrefix.'userinfo');
        	$addressTypes = array('S_' => 'ST' , 'B_' => 'BT');
        	
        	foreach ($addressTypes as $requestPrefix => $addressTypeCode) {

        		if ($addressTypeCode=='ST' && !empty($data['billing_is_shipping']))
        			continue; //not update default shipping user info or get address type name if billing = shipping
        		
        		//update user info if not checked at js prompt OR user dont have any info yet (OR new user)
 		        if ($data['update_userinfo']==1 || $newUser || !InvoiceGetter::getUserInfo(null, $data['user_id'], 'BT')){ 
			        $vmuserinfo->bind($data,$addressTypeCode);
			        if (!$vmuserinfo->store())
			        	JError::raiseWarning(0, 'Cannot store default user info. '.$vmuserinfo->getError());
		        }
		        else //else bind just primary key to load address_type_name
		        	$vmuserinfo->$userInfoCol = $data[$requestPrefix.'user_info_id']; 

		        if ($vmuserinfo->load()) //get used address_type_name (if user info is set), for adding that vmorderuserinfo field if changing shipping address
		        	$data[$requestPrefix.'address_type_name'] = isset($vmuserinfo->address_type_name) ? $vmuserinfo->address_type_name : null;
        	}      
        }

        //store user info for order (if b = s it is not performed in table fnc)
        $vmorderuserinfo = $this->getTable($tPrefix.'orderuserinfo');

        if (!$vmorderuserinfo->store($data))
        	JError::raiseWarning(0, 'Cannot store order user info. '.$vmorderuserinfo->getError());
        		
 	//NOT apply to all items. items table for VM2 will call vm functions and trigger plugins on its own.
        $this->updateState($vmorder->$orderIdCol,$data['status'],isset($data['notify']) ? $data['notify'] : 'N', '', false, false); 
        
        //create Invoice Number if we changed state, have own numbering and record not created yet


  		NbordersHelper::getInvoiceNo($data['order_id']);
          
        return $vmorder->$orderIdCol;
    }

    /**
     * Create new Joomla! user from post data
     * 
     * @param array $postData
     */
    function newJUser($postData)
    {			
    	//load language to get user errors translated
		$language = JFactory::getLanguage();
		$language->load('com_users', JPATH_ADMINISTRATOR); 
		$language->load('com_users', JPATH_SITE);
		$language->load('joomla', JPATH_ADMINISTRATOR);
		$language->load('joomla', JPATH_SITE);
		
		if (COM_NETBASEVM_EXTEND_ISJ16){ //J1.6 and more
			
			jimport('joomla.application.component.helper');
			$config = JComponentHelper::getParams('com_users');
			// Default to Registered.
			$defaultUserGroup = $config->get('new_usertype', 2);
			
			$user = new JUser();
			$user->name = $postData['B_first_name'].' '.$postData['B_last_name'];

			//get username
			$defusername = JFilterOutput::stringURLUnicodeSlug($postData['B_first_name'].''.$postData['B_last_name']);
			$user->username = $defusername;
			$i=1;
			while(1){ //johnsmith1,2,3...
				$this->_db->setQuery('SELECT count(*) FROM #__users WHERE username = '.$this->_db->Quote($user->username));
				if ($this->_db->loadResult()>0) $user->username = $defusername.($i++); else break;
			}
			
			$user->email = $postData['B_email'];
			$user->guest = false;
			$user->password = md5($postData['B_email']);
			$user->password_clean = $postData['B_email'];
			$user->groups = array($defaultUserGroup);
			
			$result = false;
			$userId = null;

			// Create the user table object
			$table			= $user->getTable();
			//$user->params	= '';
			$table->bind($user->getProperties());
				
			// Allow an exception to be thrown.
			try
			{
				// Check and store the object.
				if (!$table->check()) {
					$user->setError($table->getError());
				}
				else
				{
					$my = JFactory::getUser();
	
					// Get the old user
					$oldUser = new JUser();
	
					$iAmSuperAdmin	= $my->authorise('core.admin');
		
					// We are only worried about edits to this account if I am not a Super Admin.
					if ($iAmSuperAdmin != true) {
						// Check if the new user is being put into a Super Admin group.
						foreach ($user->groups as $key => $groupId)
						{
							if (JAccess::checkGroup($groupId, 'core.admin')) {
								throw new Exception(JText::_('JLIB_USER_ERROR_NOT_SUPERADMIN'));
							}
						}
					}
		
					// Fire the onUserBeforeSave event.
					JPluginHelper::importPlugin('user');
					$dispatcher = JDispatcher::getInstance();
					$result = $dispatcher->trigger('onUserBeforeSave', array($oldUser->getProperties(), false, $user->getProperties()));
					
					if (!in_array(false, $result, true)) {
						
						// Store the user data in the database
						if (!($result = $table->store())) {
							throw new Exception($table->getError());
						}
						
						$userId = $table->get('id');
		
						// Fire the onAftereStoreUser event
						$dispatcher->trigger('onUserAfterSave', array($user->getProperties(), false, $result, $user->getError()));
					}
				}
			}
			catch (Exception $e)
			{
				JError::raiseWarning(0,$e->getMessage());
				$result = false;
			}
		}
		else{ //J! 1.5
			$user = new JUser();
			$user->name = $postData['B_first_name'].' '.$postData['B_last_name'];
			
			//get username
			$defusername = strtolower(preg_replace('#[ ;:?!"\']#','',$postData['B_first_name'].''.$postData['B_last_name'])); //1.5 dont have stringURLUnicodeSlug
			$user->username = $defusername;
			$i=1;
			while(1){ //johnsmith1,2,3...
				$this->_db->setQuery('SELECT count(*) FROM #__users WHERE username = '.$this->_db->Quote($user->username));
				if ($this->_db->loadResult()>0) $user->username = $defusername.($i++); else break;
			}
			
			$user->email = $postData['B_email'];
			$user->usertype = 'Registered';
			$user->guest = 0;
			$user->password = md5($postData['B_email']);	
			$acl = JFactory::getACL();
			$user->gid = $acl->get_group_id( '', $user->usertype, 'ARO' );
			$user->registerDate = JFactory::getDate()->toSql();
			
			$result = $user->save();
			$userId = $user->id;
		}
		
    	if ($result && $userId){
			$mainframe = JFactory::getApplication();
			$mainframe->enqueueMessage(JText::sprintf('COM_NETBASEVM_EXTEND_USER_CREATED',$user->username, $postData['B_email']));
    		return $userId;}
		else {
			JError::raiseWarning(500,JText::_('COM_NETBASEVM_EXTEND_USER_NOT_CREATED').': '.JText::_($user->getError()),$user->getError());
			return false;
		}
    }
    
    /**
     * "Register" new shopper to VM, if not "registered" yet. Not billing/shipping info, that is saved with order.
     * 
     * @param array $postData	post array
     * @param object $user		user object
     */
    function newVMShopper($postData, $user)
	{
		$shopperCreated = null;
		
		if (!empty($postData['vendor']))
		{
			$db = JFactory::getDBO();

			//add user to default shopper group, if not having group yet
			if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
				$db->setQuery('SELECT count(*) FROM #__virtuemart_vmuser_shoppergroups WHERE virtuemart_user_id='.(int)$user->id);
			else
				$db->setQuery('SELECT count(*) FROM #__vm_shopper_vendor_xref WHERE user_id='.(int)$user->id).' AND vendor_id='.(int)$postData['vendor'];
			
			if ($db->loadResult()==0)
			{
				$groupId = null;
				if ($postData['shopper_group']){ //passed group id: check if is in vendor's groups
					$groups = invoiceGetter::getShopperGroups($postData['vendor']);
					if (isset($groups[$postData['shopper_group']]))
						$groupId = $postData['shopper_group'];
				}
				
				if (!$groupId) //not - search default group
					$groupId = invoiceGetter::getDefaultShopperGroup($postData['vendor']);
				
				if ($groupId){
					if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
						$db->setQuery('INSERT INTO #__virtuemart_vmuser_shoppergroups (virtuemart_user_id,virtuemart_shoppergroup_id) VALUES ('.(int)$user->id.','.(int)$groupId.')');
					else
						$db->setQuery('INSERT INTO #__vm_shopper_vendor_xref (user_id,vendor_id,shopper_group_id,customer_number) VALUES ('.(int)$user->id.','.(int)$postData['vendor'].','.(int)$groupId.',\'\')');
				
					if (!$db->execute()){
						JError::raiseWarning(0,'User was not assigned to default shopper group '.$groupId, $db->getErrorMsg());}
					else
						$shopperCreated = true;
				}
			}

			//vm2: create also new vmusers record, if not exists yet
			if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
			{
				$db->setQuery('SELECT count(*) FROM #__virtuemart_vmusers WHERE virtuemart_user_id='.(int)$user->id);
				if ($db->loadResult()==0)
				{
					$currentUser = JFactory::getUser();
					
					$data = array(
						'virtuemart_user_id' => (int)$user->id, 
						'virtuemart_vendor_id' => (int)$postData['vendor'], 
						'customer_number' => md5($user->username), 
						'perms' => 'shopper', 
						'created_on' => gmdate('Y-m-d H:i:s'), 
						'created_by' => (int)$currentUser->id, 
						'customer_number_bycore' => 1 //don't know what this is, but it is required by plgVmShopperIstraxx_snumbers
					);
					
					JPluginHelper::importPlugin('vmshopper');
					$dispatcher = JDispatcher::getInstance();
					$dispatcher->trigger('plgVmOnUserStore',array(&$data));
						
					$db->setQuery('INSERT INTO #__virtuemart_vmusers (virtuemart_user_id, virtuemart_vendor_id, customer_number, created_on, created_by)
					VALUES ('.(int)$data['virtuemart_user_id'].','.(int)$data['virtuemart_vendor_id'].','.$db->Quote($data['customer_number']).
						','.$db->Quote($data['created_on']).','.(int)$data['created_by'].')');
	
					if (!$db->execute()){
						JError::raiseWarning(0,'Not created new record in virtuemart_vmusers table ', $db->getErrorMsg());}	
					else{
						$shopperCreated = true;
						$dispatcher->trigger('plgVmAfterUserStore',array($data));
					}
				}
			}
		}

		return $shopperCreated;
	}
	
    function deleteCoupon($code)
    {
    	$db = JFactory::getDBO();
    	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
    		$db->setQuery('DELETE FROM `#__virtuemart_coupons` WHERE `coupon_code` = '.$db->Quote($code).' LIMIT 1');
    	else
        	$db->setQuery('DELETE FROM `#__vm_coupons` WHERE `coupon_code` = '.$db->Quote($code).' LIMIT 1');
        return $db->execute();
    }
    
    function getAjaxList($filter, $type)
    {
        return call_user_func(array($this , 'getAjax' . ucfirst($type) . 'List'), $filter);
    }

    function getAjaxNewproductList($filter)
    {
    	//TODO: vracet az po case, ne po zmacknuti!!! takhle moc requestu. taky omezit pocet vysledku.
    	return InvoiceGetter::getAjaxProductList($filter);
    }

    function getAjaxUserList($filter)
    {
    	if (trim($filter)=='')
    		$users = array();
    	else
    		$users = invoiceGetter::getAjaxUserList($filter);
        
        $newUser = new stdClass();
        $newUser->id = 'new;;';
        $newUser->name = JText::_('COM_NETBASEVM_EXTEND_NEW_CUSTOMER');
        
        array_push($users,$newUser);
        
        return $users;
    }
    
    /**
     * Get coupon info & button for write to sum
     * @param string	 $coupon
     */
    function getAjaxCoupon($coupon,$currency)
    {
    	if (trim($coupon)=='')
    		return '';
 
        if (!$info = InvoiceGetter::getCoupon($coupon))
        	echo '<span class="red">'.JText::_('COM_NETBASEVM_EXTEND_COUPON_NOT_FOUND').'</span>';
        else
        {
        	echo '<div class="green">';
        	if ($info->coupon_type=='gift')
        		echo JText::_('COM_NETBASEVM_EXTEND_GIFT_COUPON');
        	else
        		echo JText::_('COM_NETBASEVM_EXTEND_PERMANENT_COUPON');

        	if ($info->percent_or_total=='percent')
        		echo ' ('.($info->coupon_value*1).'% '.JText::_('COM_NETBASEVM_EXTEND_DISCOUNT').')';
        	else
        		echo ' ('.InvoiceCurrencyDisplay::getFullValue($info->coupon_value,$currency).' '.JText::_('COM_NETBASEVM_EXTEND_DISCOUNT').')';
        	echo '</div>';

			if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2){
        		$today =  NbordersHelper::gmStrtotime(gmdate('Y-m-d'));
        		$start = NbordersHelper::gmStrtotime($info->coupon_start_date);	
        		$expiry = NbordersHelper::gmStrtotime($info->coupon_expiry_date);
        		
        		if ($info->coupon_start_date && $info->coupon_start_date!='0000-00-00 00:00:00' && $start>$today)
        			echo '<div class="yellow"> '.JText::sprintf('COM_NETBASEVM_EXTEND_COUPON_FUTURE',date('j.n.Y',$start)).' </div>';
        			
        		if ($info->coupon_expiry_date && $info->coupon_expiry_date!='0000-00-00 00:00:00' && $expiry<$today)
        			echo '<div class="yellow"> '.JText::sprintf('COM_NETBASEVM_EXTEND_COUPON_EXPIRED',date('j.n.Y',$expiry)).' </div>';
        			
        		if ($info->coupon_value_valid)
        			echo '<div class="yellow">'.JText::sprintf('COM_NETBASEVM_EXTEND_COUPON_MIN_ORDER',InvoiceCurrencyDisplay::getFullValue($info->coupon_value_valid,$currency)).'</div>';
        	}
        	
        	if ($info->coupon_type=='gift')
        		echo ' <label class="hasTip" title="'.JText::_('COM_NETBASEVM_EXTEND_DELETE_COUPON_AFTER_SAVING').'::'.JText::_('COM_NETBASEVM_EXTEND_DELETE_COUPON_DESC').'"><input type="checkbox" name="coupon_delete" value="1" checked> '.JText::_('COM_NETBASEVM_EXTEND_DELETE_COUPON_AFTER_SAVING').'</label>';
        	
        	echo ' <input type="button" value="'.JText::_('COM_NETBASEVM_EXTEND_PASS').' &raquo;" onclick="passCouponDiscount(\''.$info->percent_or_total.'\',\''.($info->coupon_value*1).'\');"> ';
        }
    }
    /**
     * Get current items array
     * 
     * @param array 	$orderIds		array of posted order_item_id
     * @param int		$productId		new item id		(eigter one or other is posted)
     * @param string	$productName	new item name	(eigter one or other is posted)
     * @param int		$productPrice	new item price	
     * @param object	$order			current order object
     */
    function getProductsInfo($orderIds = null, $productId = null, $productName = null, $productPrice = null, &$order = null)
    {
        $db = JFactory::getDBO();

        if (empty($orderIds)) //initial call
        {
        	$items = InvoiceGetter::getOrderItems($this->orderID); //note: initial ordering
        }
        else //ajax
        {
        	$items=array();
        	
        	foreach ($orderIds as $orderId)
        	{
        		if ($orderId>0){
        			$item = InvoiceGetter::getOrderItems(null,$orderId); //note: initial ordering
		        	$items = array_merge($items,!empty($item) ? $item : $this->emptyProduct('',$order));
        		}
        		else
        			$items[]=$this->emptyProduct('',$order);
        	}
        }

        //get calculation rules
        /*
        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2){
        	foreach ($items as &$item)
        		$item->calcRules = ($this->orderID && $item->order_item_id) ? InvoiceGetter::getOrderCalcRules($this->orderID, $item->order_item_id) : array();
        }
*/
        //guess tax rate for ordered items (we can't get it from VM prod., because 1) can be own product 2) tax rate could changed)
        foreach ($items as &$item){
     		$item->tax_rate_guessed = NbordersHelper::guessTaxRate($item->product_price_with_tax, $item->product_item_price);
     		$item->tax_rate = null; //will be entered in recomputeOrder
    	}
    	
        if (!empty($productId)) //add new product from vm ($productId parameter)
        {
        	$product = InvoiceGetter::getProduct($productId);
        	
        	$newItem = $this->emptyProduct('',$order);
        	$newItem->product_id=$product->product_id;
        	$newItem->order_item_name=$product->product_name;
        	$newItem->product_quantity=1;
        	$newItem->order_item_currency=$product->product_currency;
        	$newItem->order_item_sku=$product->product_sku;
        	$newItem->tax_rate = null;
        	$newItem->vendor_id = $product->vendor_id;
        	
        	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
        	{
				NbordersHelper::importVMfile('helpers/calculationh.php'); //TODO: liší se to!třeba o 4 desetiny!
        		NbordersHelper::importVMFile('models/product.php');
        		$model = new VirtueMartModelProduct();
        		$calculator = calculationHelper::getInstance();
        		
        		$product = $model->getProduct($newItem->product_id, TRUE, FALSE, TRUE);
        		$prices = $calculator->getProductPrices($product ? $product : $newItem->product_id); //in VM 2.0.7 must be passed object

        		$newItem->product_item_price = $prices['basePriceVariant'];
        		$newItem->product_price_with_tax = $prices['basePriceWithTax'];
        		$newItem->product_tax =  $prices['taxAmount'];
        		$newItem->tax_rate_guessed = NbordersHelper::guessTaxRate($prices['basePriceWithTax'], $prices['basePrice']);
        		$newItem->product_price_discount = - $prices['discountAmount'];
        		/*
        		$newItem->calcRules = array();
        		foreach ($calculator->rules as $kind => $rules)
        			foreach ($rules as $rule)
        				$newItem->calcRules[] = (object)$rule;
        				*/
        	}
        	else
        	{
        		 //TODO: what about currency.
        		$newItem->product_item_price = !is_null($productPrice) ? $productPrice : $product->product_price; //price manually selected, if not, price from db 
        		$newItem->tax_rate_guessed = $product->tax_rate; //assigned tax rate for product
        		$newItem->product_tax = $product->tax_rate * $newItem->product_item_price;
			    $newItem->product_price_with_tax = $newItem->product_item_price + $newItem->product_tax;
			    
			    //apply product specific discount
			    if ($product->amount>0) {
			    	if (($product->start_date<=time() || empty($product->start_date)) && ($product->end_date>=time() || empty($product->end_date))) {
				    	if ($product->is_percent)
				    		$newItem->product_price_with_tax = ($newItem->product_price_with_tax * (100 - $product->amount)) / 100;
				    	else
				    		$newItem->product_price_with_tax = $newItem->product_price_with_tax - $product->amount;
				    		
				    	$newItem->product_item_price = $newItem->product_price_with_tax / (1 + $newItem->tax_rate_guessed);
				    	$newItem->product_tax = $newItem->product_price_with_tax - $newItem->product_item_price;
			    	}
			    }
        	}

			$items[] = $newItem;
        }

		//add new product, that is not in vm ($productName parameter)
        if (empty($productId) AND !empty($productName))
        	$items[]=$this->emptyProduct($productName,$order);

        // count overall weight 
        //(for purpose of passing it into shipping modules). note this works still with original quantity from db, not post
        //only for VM1, because VM1 shipping moduels needs it, VM2 is bit more complex (handled in TableVm2OrderShipment)
        if (!COM_NETBASEVM_EXTEND_ORDERS_ISVM2){
        	$this->overal_weight = 0;
	        foreach ($items as &$item)
	        	if (!empty($item->product_id)){ //it is VM product
	        		if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
			        	$db->setQuery('SELECT p.`product_weight` FROM `#__virtuemart_products` AS p WHERE p.`virtuemart_product_id` = '. (int) $item->product_id);
	        		else
			        	$db->setQuery('SELECT p.`product_weight` FROM `#__vm_product` AS p WHERE p.`product_id` = '. (int) $item->product_id);
			        if ($weight = $db->loadResult())
			        	$this->overal_weight += $item->product_quantity * $weight;
	        	}
        }
        
        return $items;
    }
    
    function emptyProduct($name='',$order=null)
    {
		$product = new stdClass();
		$product->order_item_id = 0;
		$product->product_id = 0;
		$product->order_item_name = $name;
		$product->product_attribute = '';
		$product->order_status = $order->order_status;
		$product->product_quantity = 1;
		$product->product_price_with_tax = 0;
		$product->product_item_price = 0;
		$product->product_tax = 0;
		$product->order_item_currency = isset($order->order_currency) ? $order->order_currency : null;
		$product->order_item_sku = '';
		$product->vendor_id = isset($order->vendor_id) ? $order->vendor_id : null;
		$product->tax_rate_guessed = 0;
		$product->tax_rate = null;
		$product->product_price_discount = null;
		
		return $product;
    }

    function setUserId($uid)
    {
        $this->userId = $uid;
    }

    function setOrderNo($oNo)
    {
        $this->orderID = $oNo;
    }

    /**
     * Load order information
     * 
     * @param bool $fromPost 		if override loaded values by these from post ( = ajax refresh)
     * @param bool $updateShipping	if override shipping cost + tax by these from ship_method_id (only VM2)
     * @param bool $updatePayment	if override payment cost + tax by these from payment_method_id (only VM2)
     */
    function getOrderInfo($fromPost=false,$updateShipping=false,$updatePayment=false)
    { 

        if (!$order = InvoiceGetter::getOrder($this->orderID)) { //new order
        	
        	$params = NbordersHelper::getParams(); 
        	
        	//get default vendor. if not set and there only one in VM, use him
			if (!$defaultVendor = $params->get('default_vendor')){
				$vendors = InvoiceGetter::getVendors();
				$defaultVendor = count($vendors)==1 ? $vendors[0]->id : 0;
			}
			
			//get default currency. if not set, get default vendor currency, if not set, guess VM most used currency
			if (!$defaultCurrnecy = $params->get('default_currency'))
				$defaultCurrnecy = ($curr = InvoiceGetter::getDefaultCurrency($defaultVendor)) ? $curr : null;

			//get default status
			if (!$defaultStatus = $params->get('default_status'))
				$defaultStatus = '';
			
            $order = new stdClass();
            $order->user_id = '';
            $order->order_id = '';
            $order->user_info_id = '';
            $order->cdate = '';
            $order->mdate = '';
            $order->order_status = $defaultStatus;
            $order->vendor_id = $defaultVendor;
            $order->ship_method_id = '||||';
            $order->order_currency = $defaultCurrnecy;
            $order->payment_method_id = null;
            $order->shipment_method_id = null; //VM2
            $order->order_shipping = 0;
            $order->order_shipping_tax = 0;
            $order->order_shipping_taxrate = 0;
            $order->order_payment = 0;
            $order->order_payment_tax = 0;
            $order->order_payment_taxrate = 0;
            $order->coupon_discount = 0;
            $order->coupon_code = '';
            $order->order_discount = 0;
            $order->order_total = '';
            $order->order_subtotal = '';
            $order->order_tax = '';
            $order->customer_note1 = ''; 
            $order->order_discountAmount = 0; //VM2
            $order->calcRules = array();//VM2
        }
        elseif (COM_NETBASEVM_EXTEND_ORDERS_ISVM2){ //get calculation rules applied on order from db
        	$order->calcRules = InvoiceGetter::getOrderCalcRules($this->orderID);



        	foreach ($order->calcRules as $key => $rule){
        		if (!empty($rule->virtuemart_order_item_id)) //VM 2.0.12, unset rules for products
        			unset($order->calcRules[$key]);
        		elseif ((float)$rule->calc_amount==0) //unset rules with no amount
        			unset($order->calcRules[$key]);
        	}
        }
    
        //TODO: move to more propriate place
        $order->order_shipping_taxrate = NbordersHelper::guessTaxRate($order->order_shipping+$order->order_shipping_tax, $order->order_shipping);
        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
        	$order->order_payment_taxrate = NbordersHelper::guessTaxRate($order->order_payment+$order->order_payment_tax, $order->order_payment);
        	
       // fill custom shipping vars from ship_method_id
        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM1)
        {
	        $customShipping = explode('|', $order->ship_method_id);
			$order->custom_shipping_class = $customShipping[0];
			$order->custom_shipping_carrier = $customShipping[1];
			$order->custom_shipping_ratename = $customShipping[2];
			$order->custom_shipping_costs = $customShipping[3];
			if (isset($order->order_shipping_taxrate)) {
			    $order->custom_shipping_taxrate = $order->order_shipping_taxrate;
			} else {
			    if ($order->order_shipping_tax != $customShipping[3] && $order->order_shipping_tax != 0 &&  $customShipping[3] != 0) {
	                $order->custom_shipping_taxrate = NbordersHelper::guessTaxRate($order->order_shipping + $order->order_shipping_tax, $order->order_shipping);
			    } 
			    else 
			    	$order->custom_shipping_taxrate = 0;
			}
			
			$order->custom_shipping_id = isset($customShipping[4]) ? $customShipping[4] : '';	
			$order->order_shipping_taxrate = $order->custom_shipping_taxrate;
        	//they are glued back from POST at vm1tableorder
        }
        if ($fromPost){
        
         	//override loaded status by post
        	$order->order_status = JRequest::getVar('status', null);
        
			//override coupon discount by post
        	$order->coupon_discount = JRequest::getVar('coupon_discount', null);
        	
        	//override shipping costs from post
        	$order->order_shipping = JRequest::getVar('order_shipping', null);
        	$order->order_shipping_tax = JRequest::getVar('order_shipping_tax', null);
        	$order->order_shipping_taxrate = JRequest::getVar('order_shipping_taxrate', null);
        	
        	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2){
        		
        		//override shipping method from post
        		$order->shipment_method_id = JRequest::getVar('shipment_method_id', null);
        		
        		//override payment costs from post
	        	$order->order_payment = JRequest::getVar('order_payment', null);
	        	$order->order_payment_tax = JRequest::getVar('order_payment_tax', null);
	        	$order->order_payment_taxrate = JRequest::getVar('order_payment_taxrate', null);
	        	
	        	//override calculation rules from post
	        	$order->calcRules = array();
	        	$calcRuleNames = JRequest::getVar('calc_rule_name',array(),'default','array');
	        	$calcKinds = JRequest::getVar('calc_kind',array(),'default','array');
	        	$calcAmounts = JRequest::getVar('calc_amount',array(),'default','array');
	        	
	        	foreach ($calcAmounts as $key => $val){
	        		$rule = new stdClass();
	        		$rule->virtuemart_order_calc_rule_id = $key;
	        		$rule->calc_rule_name = $calcRuleNames[$key];
	        		$rule->calc_kind = $calcKinds[$key];
	        		$rule->calc_amount = $val;
	        		$order->calcRules[] = $rule;
	        	}
        	}
        	
        	//compute current order's subtotal (for payment method discount)
        	$orderSubtotal = 0;
        	$nets = JRequest::getVar('product_item_price', array(),'default','array');
        	$quantities = JRequest::getVar('product_quantity', array(),'default','array');
        	foreach ($nets as $key => $val)
        		$orderSubtotal += $val * $quantities[$key];
        	
        	
        	$order->payment_method_id = JRequest::getInt('payment_method_id');
        }
        else
        	$orderSubtotal = null; 

        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2 && $updateShipping)
        	$this->overrideShipment($order);
        
        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM1 || $updatePayment)
        	$this->overridePayment($order, $orderSubtotal);
        	
	    if (COM_NETBASEVM_EXTEND_ORDERS_ISVM1){
        	//get discount minus payment discount. if is not passed by GET, compute it by substracting payment discount
			$order->order_discount = $fromPost ? JRequest::getVar('order_discount')*1 : - $order->order_discount - $order->order_payment;
	    }
        return $order;
    }

    /**
     * Write selected payment method fee's to order
     * 
     * @param unknown_type $order
     * @param unknown_type $orderSubtotal only for VM1
     */
    function overridePayment(&$order, $orderSubtotal=null)
    {
    	$order->order_payment = 0;
    	$order->order_payment_taxrate = 0;
    	$order->order_payment_tax = 0;
    	
    	if (!$method = InvoiceGetter::getPaymentMethod($order->payment_method_id))
    		return ;
    	
    	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2){
    		
	    	 $order->order_payment = $method->cost_per_transaction;
	    	 if ($method->cost_percent_total) //selected percent of total items amount
	    	 	$order->order_payment = $order->order_payment + (($method->cost_percent_total/100)*$order->order_salesPrice); //TODO: jakto ze tady je to order_salesPrice a tam order_subtotal ? 

	    	 if ($method->tax_id>0){ //apply specific tax set to payment, BUT NOT 100% RELIABLE (they could select apply default rules?)
	    	 	$db = JFactory::getDBO();
	    	 	$db->setQuery('SELECT * FROM #__virtuemart_calcs WHERE virtuemart_calc_id='.(int)$method->tax_id);
	    	 	if ($calcRule = $db->loadObject()){
	    	 		if ($calcRule->calc_value_mathop=='+%')
	    	 			$order->order_payment_taxrate=$calcRule->calc_value/100;
	    	 		elseif ($calcRule->calc_value_mathop=='-%')
	    	 			$order->order_payment_taxrate=-($calcRule->calc_value/100);	
	    	 				
	    	 		$order->order_payment_tax = $order->order_payment * $order->order_payment_taxrate;
	    	 		
	    	 		if ($calcRule->calc_value_mathop=='+')
	    	 			$order->order_payment_tax=$order->order_payment + $calcRule->calc_value;
	    	 		elseif ($calcRule->calc_value_mathop=='-')
	    	 			$order->order_payment_tax=$order->order_payment - $calcRule->calc_value;
	    	 	}
	    	 }	
	    	 
	    	 //TODO: check all other things. grr. (min, max order?)
    	}
	    else{ //vm1

	    	if (is_null($orderSubtotal))
	    		$orderSubtotal = $order->order_subtotal;
	    	
	    	if ($method->payment_method_discount_is_percent==1){ //(little inspired by ps_checkout get_payment_discount())
    			$order->order_payment = $orderSubtotal * $method->payment_method_discount / 100;
    			
	    		if ($method->payment_method_discount_max_amount*1 && abs($order->order_payment) > $method->payment_method_discount_max_amount*1)
	    			$order->order_payment = - $method->payment_method_discount_max_amount*1;
	    					
	    		if ($method->payment_method_discount_min_amount*1 && abs($order->order_payment) < $method->payment_method_discount_min_amount*1)
	    			$order->order_payment = - $method->payment_method_discount_min_amount*1;
    		}
    		else
    			$order->order_payment = $method->payment_method_discount*1;
    					
	    	$order->order_payment = - $order->order_payment;
	    }
    }
    
    /**
     * Write selected shipping method fee's to order
     * 
     * @param order object $order
     */
    function overrideShipment(&$order)
    {
    	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2 && $shippings = InvoiceGetter::getShippingsVM2())
    	{
    	    foreach ($shippings as $shipping)
        	{
        		if ($shipping->shipping_rate_id == $order->shipment_method_id){
        			
        			$order->order_shipping = $shipping->cost + $shipping->package_fee;
			    	$order->order_shipping_taxrate = 0;
			    	$order->order_shipping_tax = 0;
		        			
			    	 if ($shipping->tax_id>0){ //apply specific tax set to payment, BUT NOT 100% RELIABLE (they could select apply default rules?)
			    	 	$db = JFactory::getDBO();
			    	 	$db->setQuery('SELECT * FROM #__virtuemart_calcs WHERE virtuemart_calc_id='.(int)$shipping->tax_id);
			    	 	if ($calcRule = $db->loadObject()){
			    	 		if ($calcRule->calc_value_mathop=='+%')
			    	 			$order->order_shipping_taxrate=$calcRule->calc_value/100;
			    	 		elseif ($calcRule->calc_value_mathop=='-%')
			    	 			$order->order_shipping_taxrate=-($calcRule->calc_value/100);	
			    	 				
			    	 		$order->order_shipping_tax = $order->order_shipping * $order->order_shipping_taxrate;
			    	 		
			    	 		if ($calcRule->calc_value_mathop=='+')
			    	 			$order->order_shipping_tax=$order->order_shipping + $calcRule->calc_value;
			    	 		elseif ($calcRule->calc_value_mathop=='-')
			    	 			$order->order_shipping_tax=$order->order_shipping - $calcRule->calc_value;
			    	 	}
			    	}	
			    	//TODO: check also other conditions...?
        		}
        	}
    	}
    }
    
    function getUserInfo($addressType,$userInfoId=null, $userId = null)
    {
    	$userInfo = null;
    	if ($userInfoId)
    		if ($userInfo = InvoiceGetter::getUserInfo($userInfoId)) //user info found
    			$userInfo->billing_is_shipping = 0;
    	
    	if (!$userInfo){
    		$userInfo = $this->getEmptyUser();	
        	if ($addressType=='ST') //no ST
        		$userInfo->billing_is_shipping = 1;
        		
    		//if no id passed, maybe there is joomla! user which is not registered in VM yet. 
    		//in that case, fill new user with joomla! register info
    		if ($userId && ($user = JFactory::getUser($userId)))
    		{
    			$userInfo->user_id = $userId;
    			
    			//guess parts of name
    			$name = explode(' ',$user->name);
    			if (count($name)==2){
    				$userInfo->first_name = $name[0];
    				$userInfo->last_name = $name[1];
    			}
    		    elseif (count($name)==3){
    				$userInfo->first_name = $name[0];
    				$userInfo->middle_name = $name[1];
    				$userInfo->last_name = $name[2];
    			}
    			else { //more parts, no matter, just put all to names
    				$userInfo->first_name = array_shift($name);
    				$userInfo->last_name = count($name) ? implode(' ',$name) : '';
    			}

    			$userInfo->email = $user->email;
    			
    			//try to load info from user profile plugin
    			$db = JFactory::getDBO();
    			$db->setQuery('SHOW TABLES LIKE \'%_user_profiles\'');
    			$tables = $db->loadColumn();
    			if ($tables && count($tables)>0)
    			{
    				$db->setQuery('SELECT profile_key, profile_value FROM #__user_profiles WHERE user_id='.(int)$userId);
    				$profileInfo = $db->loadObjectList('profile_key');
    				if (isset($profileInfo['profile.address1'])) $userInfo->address_1 = trim($profileInfo['profile.address1']->profile_value,' "');
    				if (isset($profileInfo['profile.address2'])) $userInfo->address_2 = trim($profileInfo['profile.address2']->profile_value,' "');
    				if (isset($profileInfo['profile.city'])) $userInfo->city = trim($profileInfo['profile.city']->profile_value,' "');
    				
    				if (isset($profileInfo['profile.region'])){ //find state id by name
    					$userInfo->state = trim($profileInfo['profile.region']->profile_value,' "');	
    				    $searchState = strtolower(preg_replace('#\W#','',$profileInfo['profile.region']->profile_value));
    					foreach (invoiceGetter::getStatesDB() as $region){
    						if (strtolower(preg_replace('#\W#','',$region->state_name)) == $searchState ||
    						strtolower(preg_replace('#\W#','',$region->state_3_code)) == $searchState ||
    						strtolower(preg_replace('#\W#','',$region->state_2_code)) == $searchState ||
    						$region->id == $profileInfo['profile.region']->profile_value){
    							$userInfo->state = $region->id; break;}
    					}
    				}
    				if (isset($profileInfo['profile.country'])){ //find country id by name
    					$userInfo->country = trim($profileInfo['profile.country']->profile_value,' "');
    					$searchCountry = strtolower(preg_replace('#\W#','',$profileInfo['profile.country']->profile_value));
    					foreach (invoiceGetter::getCountriesDB() as $country){
    						if (strtolower(preg_replace('#\W#','',$country->country_name)) == $searchCountry ||
    						strtolower(preg_replace('#\W#','',$country->country_3_code)) == $searchCountry ||
    						strtolower(preg_replace('#\W#','',$country->country_2_code)) == $searchCountry ||
    						$country->id == $profileInfo['profile.country']->profile_value){
    							$userInfo->country = $country->id; break;}
    					}
    				}
    				
    				if (isset($profileInfo['profile.postal_code'])) $userInfo->zip = trim($profileInfo['profile.postal_code']->profile_value,' "');
    				if (isset($profileInfo['profile.phone'])) $userInfo->phone_1 = trim($profileInfo['profile.phone']->profile_value,' "');
    			}
    			
    		}
    	}

        return $userInfo;
    }

    function getOrderUserInfo($addressType)
    {
        if ($user = InvoiceGetter::getOrderUserInfo($this->orderID, $addressType))
        {
        	$db = JFactory::getDBO();
        	
        	//try to also get global user_info_id for given address (we need it for updating user standard address)
        	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
        		$query = 'SELECT virtuemart_userinfo_id FROM #__virtuemart_userinfos WHERE virtuemart_user_id= '.(int)$user->user_id.' AND address_type = '.$db->Quote($addressType);
       		else
       			$query = 'SELECT user_info_id FROM #__vm_user_info WHERE user_id= '.(int)$user->user_id.' AND address_type = '.$db->Quote($addressType);
       			
        	$db->setQuery($query);
       		$db->execute();

       		if ($db->getNumRows()>1){ //more addresses (f.e. ST), try to find it by type name
       			if (!$user->address_type_name) //default ST address
       				$append = ' AND ((address_type_name IS NULL) OR address_type_name=\'-default-\')'; //NULL==-default- in VM1
       			else
       				$append = ' AND address_type_name = '.$db->Quote($user->address_type_name);
       			
       			$db->setQuery($query.$append);
        		$db->execute();

        		if (COM_NETBASEVM_EXTEND_ORDERS_ISVM1 && $db->getNumRows()>1 AND !$user->address_type_name){ //else try just -default- (vm1)
        			$db->setQuery($query.' AND address_type_name=\'-default-\''); 
        			$db->execute();
        		}
       		}
        	$user->user_info_id = $db->getNumRows()==1 ? $db->loadResult() : '';
        	$user->billing_is_shipping = 0;
        }
        else {
        	$user = $this->getEmptyUser();
        	if ($addressType=='ST') //no shipping address is found
        		$user->billing_is_shipping = 1;
        }
        	
        	
        return $user;
    }

    function getEmptyUser()
    {
        $user = new stdClass();
        $user->user_id = '';
        $user->address_type = '';
        $user->address_type_name = null;
        $user->company = '';
        $user->title = '';
        $user->last_name = '';
        $user->first_name = '';
        $user->middle_name = '';
        $user->phone_1 = '';
        $user->phone_2 = '';
        $user->fax = '';
        $user->address_1 = '';
        $user->address_2 = '';
        $user->city = '';
        $user->state = '';
        $user->country = '';
        $user->zip = '';
        $user->email = '';
   		$user->user_info_id='';
   		
   		$user->billing_is_shipping = 0;
   		
        return $user;
    }

    /**
     * Gets "custom" user fields from Virtue Mart
     * TODO: get also only names (to use it in table sabe funsions to not sanitise chekcboxes not presented on form) (see tables)
     * @param	string	B_ or S_
     * @param	object	billingData or shippingData object
     * 
     * @return	array	user fields labels and inputs
     */
    function getUserFields($prefix,&$data)
    {
    	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
    	{
    		NbordersHelper::importVMFile('models/userfields.php');
    		NbordersHelper::importVMFile('tables/userfields.php');
    		NbordersHelper::importVMFile('tables/countries.php');
 		
    		//load VM language to inputs get translated
    		$language = JFactory::getLanguage();
    		$language->load('com_virtuemart',JPATH_SITE);
    		$language->load('com_virtuemart',JPATH_ADMINISTRATOR);
    			
    		$model = new VirtueMartModelUserfields();
    		
    		$skip = array('username', 'password', 'password2','agreed',
    		'first_name','last_name','middle_name','title','company','address_1','address_2',
    		'city','zip','virtuemart_country_id','virtuemart_state_id',
    		'email','phone_1','phone_2','fax','address_type_name','address_type');
    		

    		$selection = $model->getUserFields('shipment',array('delimiters'=>false,'captcha'=>false),$skip);
    		
    		//now add extra fields, if defined and not there yet (for example if that field is allowed only for registration form
    		//find, if there is extra fields 1 - 4
    		$extraFields = array();
    		$params = NbordersHelper::getParams(); 
    		foreach (range(1,4) as $i) //put extra fields we need to get additionaly to speical array
				if ($extra_field = $params->get('extra_field'.$i)){
					$extraFields[$extra_field] = false; //add to array
    				foreach ($selection as $field)
    					if ($field->virtuemart_userfield_id == $extra_field) //if this is already presented in selection, remove
    						unset($extraFields[$extra_field]);
    			}
    			
    			
    		if ($extraFields){ //there are fields to get
    			foreach (array('registration','account') as $area) //find fields for registration or account form
		    		foreach ($model->getUserFields($area,array('delimiters'=>false,'captcha'=>false),$skip) as $field)  //go and check if extra field is inside
		    			if (isset($extraFields[$field->virtuemart_userfield_id])) 
		    				$extraFields[$field->virtuemart_userfield_id] = $field; //yes, add it to result
    			
    			foreach ($extraFields as $field) //go through results
    				if ($field) //not false
						array_push($selection, $field); //add to result array
    		}
    		
    		$fields = $model->getUserFieldsFilled($selection,$data,$prefix);
    		
    		$return = array();

    		if (count($fields['fields'])) foreach ($fields['fields'] as $field)
    			if(!empty($field['formcode']))
    				$return[] = array('title' => $field['title'],'input' => $field['formcode'],'desc' => $field['title']);
    		
    		return $return;
    	}
    	else
    	{
			//load VM1 framework
			global $mosConfig_absolute_path;
			
			NbordersHelper::importVMFile('virtuemart_parser.php',false);
			NbordersHelper::importVMFile('classes/ps_userfield.php');
			
			//get all non-system user fields for shipping or/and fields that are defined in VMInvoice config
			$params = NbordersHelper::getParams(); 
			$db = JFactory::getDBO();
			$extras='';
			foreach (range(1,4) as $i)
				if ($extra_field = $params->get('extra_field'.$i))
					$extras .= " OR fieldid=".$db->Quote($extra_field);

			$db->setQuery('SELECT * FROM `#__vm_userfield` WHERE (`type`!=\'delimiter\' AND `shipping`=1 AND `sys`=0 AND `published`=1)'.$extras.' ORDER BY ordering');
			$userFields = $db->loadObjectList();
			
			$skipFields = array();
			$db = new ps_DB();
	
			foreach ($userFields as & $field){
				
				if (property_exists($data, $field->name)){ //if we have default value set
					$field->default=$data->{$field->name};  //set "fake" default value to VM
					$db->record[0]->{$prefix.$field->name} = $data->{$field->name}; //set "fake" database record to VM
				}
				$field->name = $prefix.$field->name; //append B_ or S_ prefix to field name
				$field->required = 0; //empty requied info
												
				if ($field->type=='delimiter') //unset delimiters
					unset($field);
			}
			
			//call VM and catch thrown html
			ob_start();
			ps_userfield::listUserFields( $userFields, $skipFields, $db, false );
			$userInfo = ob_get_clean(); 
			
			//parse title, help hint and inputs from VM's gibbrish
			$regExp = 'class="formLabel.*"\s*>\s*<\s*label.*>(.*)<\s*\/\s*label.*
			class="formField.*"\s*>(.*)\s*(?:<\s*span.*onmouseover="Tip\s*\(\s*\'(.*)\'.*<\s*\/\s*span\s*>)?\s*(?:<\s*br\s*\/?s*>)?\s*<\s*\/\s*div\s*>';
			$userInfo = preg_match_all('/'.$regExp.'/iUsx',$userInfo,$matches,PREG_SET_ORDER);
			
			//store them to nice array
			$return = array();
			if (!empty($userInfo)){
				
				foreach ($matches as $key => $match){
					
					$return[$key]['title'] = $match[1];
					$return[$key]['input'] = $match[2];
					if (isset($match[3]))
						$return[$key]['desc'] = $match[3];
				}
			}
    	}
		return $return;
    }
    
    /**
     * Recompute order and products.
     * 
     * @param array $products
     * @param object $order
     * @param boolean $recomputeOrder 	whether called by ajax recompute
     * @param TableVmOrder $order
     */
    function recomputeOrder(&$products, &$order, $recomputeOrder = false)
    {
        $orderTax = $orderSubtotal = $order_salesPrice = $order_discountAmount = 0;
        $count = count($products);
        foreach ($products as &$product) {
            /* @var $product TableVmOrderItem */
        	


        	if ($recomputeOrder)// ok, when called by ajax, recompute guessed tax rate based on form data (which is in $products by now)
        		$product->tax_rate_guessed = NbordersHelper::guessTaxRate($product->product_tax + $product->product_item_price, $product->product_item_price);
        		
        	//recompute tax rates. bot only if they changded since last time (TODO: ?)
        	            
        	if (is_null($product->tax_rate)) //initial call. else we get it from select box.
        		$product->tax_rate = $product->tax_rate_guessed;
        		
        	if ($recomputeOrder) {

        		if (!is_null($product->tax_rate)) //should always be at this point
		            if ($product->tax_rate>=0 && $product->tax_rate!=$product->tax_rate_guessed) //tax rate changed by user, else stay initial
		            	$product->product_tax = $product->tax_rate * $product->product_item_price;
		            	
        		$product->product_price_with_tax = $product->product_item_price + $product->product_tax;
        		$product->product_subtotal_with_tax = $product->product_price_with_tax + $product->product_price_discount;
        	}
        	
            if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2){
            	$product->product_price_discount = $product->product_price_discount*1;
            	$product->overall_discount = $product->product_price_discount * $product->product_quantity;
            	
            	$order_discountAmount += $product->overall_discount;
            }
            
            $product->overall_tax = $product->product_tax * $product->product_quantity;
           	$product->subtotal = $product->product_item_price * $product->product_quantity;
           	$product->total = $product->product_subtotal_with_tax * $product->product_quantity;

            $orderSubtotal += $product->subtotal;
            $orderTax += $product->overall_tax;
            $order_salesPrice += $product->total;
        }
        
        //same as order subtotal (we use VM2 name)
        $order->order_salesPrice = $order->order_subtotal + $order->order_tax + (COM_NETBASEVM_EXTEND_ORDERS_ISVM2 ? $order->order_discountAmount : 0);
        
        if ($recomputeOrder) {
        	
            $order->order_tax = $orderTax;
            $order->order_subtotal = $orderSubtotal;
            $order->order_salesPrice = $order_salesPrice;
            
            if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
            	$order->order_discountAmount = $order_discountAmount;
            
            if ($order->order_shipping_taxrate>=0) //if not "-other-"
           		$order->order_shipping_tax = $order->order_shipping * $order->order_shipping_taxrate;
           		
           	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2 && $order->order_payment_taxrate>=0) //if not "-other-"
            	$order->order_payment_tax = $order->order_payment * $order->order_payment_taxrate;
            
            $order->order_total = $order->order_salesPrice; //price of all items featuring tax and discounts
            $order->order_total += $order->order_shipping + $order->order_shipping_tax; //add shipping + tax
            $order->order_total += $order->order_payment + $order->order_payment_tax; //add payment + tax
            $order->order_total += $order->coupon_discount; //add coupon discount
            
            if (COM_NETBASEVM_EXTEND_ORDERS_ISVM1)
            	$order->order_total += $order->order_discount; //vm 1: add "order other discount" (in db it is stored togehter with payment discount, but here we have it separated)
            else
	            foreach ($order->calcRules as $rule)
	            	$order->order_total += $rule->calc_amount; //vm2: add order calculation rules
        }

        //no, stejne se to v poslednich verzich asi zase x krat zmenilo.
        //produkty:
        //VM2:
        //product_final_price - cena jedne polozky s dani a po sleve
        //product_subtotal_with_tax = cena vsech polozek po dani a slevach
        
        //objednavka: 
        //VM2:
        //order_salesPrice - celkova cena objednych veci s danemi a vsim (slevy)
        //order_subtotal - soucet pocatecnich cen produktu product_item_price 
        //order_tax = 1 - slevy - 2
        //order_billTaxAmount = soucet VSECH dani. tedy shipment tax, payment tax, order_tax + dalsi dane (z tabulky calc_rules ovsem jen TAXY ne price modifiery)
        //order_billDiscountAmount = měl by to byt soucet discountu za objednavku? ale chyba ve VM asi
        //order_discountAmount = soucet discountu za produkty
        
        //VM1: 
        //order_subtotal - soucet pocatecnich cen produktu product_item_price  (stejne)
        //order_tax = 1 - 2 vsech produktu (ovsem pokud jsou nejak slevy tak nevim co)
    }
    
    /**
     * Update order's state using VM's functions.
     * Only if state is different then present OR is passed force notify (YF)
     * VM code (should) update also mdate. 
     * 
     * @param	object	order id
     * @param	string	new status
     * @param	string	"Y" if notify user if state changed, "YF" if force notify even state stays the same (resend notify), "N" or other for no notify
     * @param 	string	$comments			applies only for VM2! (now)
     * @param	bool	$includeComment		applies only for VM2! (now)
     * @param 	bool	$updateAllLines		applies only for VM2!
     */
	public function updateState($orderId,$newStatus,$notify,$comments='',$includeComment=false, $updateAllLines= false)
    {
		$order = InvoiceGetter::getOrder($orderId);
		
    	//if was changed state or checked notify
		if ((isset($newStatus) AND $newStatus!=$order->order_status) OR ($notify=='YF'))
		{
	    	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
	    	{
		    		//copy of admin/controllers/orders.php updatestatus()
		    		NbordersHelper::importVMFile('controllers/orders.php');
		    		NbordersHelper::importVMFile('views/orders/view.html.php');
		    		NbordersHelper::importVMFile('helpers/shopfunctions.php');
		    		NbordersHelper::importVMFile('models/orders.php');
		    		NbordersHelper::importVMFile('tables/orders.php');
		    		NbordersHelper::importVMFile('tables/vendors.php');
		    		NbordersHelper::importVMFile('tables/vendor_medias.php');
		    		NbordersHelper::importVMFile('tables/order_items.php');
		    		NbordersHelper::importVMFile('tables/userfields.php');
		    		NbordersHelper::importVMFile('tables/countries.php');
		    		NbordersHelper::importVMFile('tables/orderstates.php');
		    		NbordersHelper::importVMFile('tables/medias.php');
		    		NbordersHelper::importVMFile('tables/vmusers.php');
		    		NbordersHelper::importVMFile('tables/userinfos.php');
		    		NbordersHelper::importVMFile('tables/order_histories.php');
	
					$controller = new VirtuemartControllerOrders();
					
		    		/* Load the view object */
					$view = $controller->getView('orders', 'html');
			
					/* Load the helper */
					$view->loadHelper('shopFunctions');
					$view->loadHelper('vendorHelper');
			
					/* Update the statuses */
					//$model = $controller->getModel('orders');
					
					
		    		$model = VmModel::getModel('orders');
		    		
					// single order is in POST but we need an array
					$orderPass = array();
					$orderPass['order_id']=(int)$order->order_id;
					$orderPass['virtuemart_order_id']=$order->order_id;
					$orderPass['current_order_status']=$order->order_status;
					$orderPass['order_status']=$newStatus;
					$orderPass['comments']=$comments;
					$orderPass['customer_notified']=($notify=='Y' || $notify=='YF') ? 1 : 0;
					$orderPass['customer_send_comment']= $includeComment ? 1 : 0;
					$orderPass['update_lines']= $updateAllLines ? 1 : 0;
	
					$formerView = JRequest::getVar('view');
					JRequest::setVar('view','orders'); //this is also for VM checkFilterDir function
					
					if (!($result = $model->updateStatusForOneOrder($order->order_id,$orderPass)))
						JError::raiseWarning(0,'Order state not changed by VirtueMart (mail probably not sent). ',print_r($orderPass,true));
					
					JRequest::setVar('view',$formerView);
					
					return $result;
				
	    	}
	    	else //VM1
	    	{
		    	//get VirtueMart framework
		        global $mosConfig_absolute_path;
		        
		        NbordersHelper::importVMFile('virtuemart_parser.php',false);
		        NbordersHelper::importVMFile('classes/ps_order.php');

				//build "fake" array
		    	$d=JArrayHelper::fromObject($order);
		    		
		    	$d['order_id']=$order->order_id;
		    	$d["current_order_status"]=$order->order_status;
		    	$d["order_status"]=$d['new_order_status']=$newStatus;
		    	$d['notify_customer']= (isset($notify) AND ($notify=='Y' OR $notify=='YF')) ? 'Y' : 'N';
		    	$d["order_comment"]='';
		    		    	
		    	//pass it to VM function
		    	$vmOrder = new vm_ps_order;	
		    	$return = $vmOrder->order_status_update($d);
		    	if (!$return)
		    		JError::raiseWarning(0,'Order state not changed by VirtueMart (mail probably not sent). ',print_r($d,true));
	
		    	return $return;
    		}
		}
		else
			return 'no change';
	}
	
}
?>