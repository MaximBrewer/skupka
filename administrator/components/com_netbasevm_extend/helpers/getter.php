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

// check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restrict Access');

class InvoiceGetter
{


	static function checkOrderExists($orderId)
	{
		$db = JFactory::getDBO();
		
		if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
			$db->setQuery("SELECT virtuemart_order_id FROM #__virtuemart_orders WHERE virtuemart_order_id=".(int)$orderId);
		else
			$db->setQuery("SELECT order_id FROM #__vm_orders WHERE order_id=".(int)$orderId);
		
				return ($db->loadResult()>0);
	}
	
	static function getOrderNumberAndPass($orderId)
	{
		$db = JFactory::getDBO();
		if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
			$db->setQuery('SELECT order_number, order_pass FROM #__virtuemart_orders WHERE virtuemart_order_id='.(int)$orderId);
		else
			$db->setQuery('SELECT order_number, NULL as order_pass FROM #__vm_orders WHERE order_id='.(int)$orderId);
			
		return $db->loadAssoc();
	}
	
	static function getOrder($orderId=null)
	{
		static $orders;
		
		if (!isset($orders[$orderId])) //cache
		{
			
	        $db = JFactory::getDBO();
	        
	        if (empty($orderId))
	        	return array(); //fields list	
	        //should produce most same results (if not eixists, null)

	        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
	        {
	        	$langPayments = self::getVm2LanguageTable('#__virtuemart_paymentmethods');
	        	$langShipments = self::getVm2LanguageTable('#__virtuemart_shipmentmethods');
       	
		        $sql=('SELECT 
		        `orders`.*,
		        `orders`.virtuemart_order_id AS order_id, 
		        `orders`.virtuemart_vendor_id AS vendor_id, 
		        `orders`.virtuemart_user_id AS user_id, 
                        `userinfos`.customer_note AS customer_note, 
		        `orders`.order_shipment AS order_shipping, 
		        `orders`.order_shipment_tax AS order_shipping_tax, 
		        (-1 * `orders`.`order_discount`) AS `order_discount`, 
		        (-1 * `orders`.`coupon_discount`) AS `coupon_discount`, 
		        `orders`.created_on, 
		        `orders`.modified_on, 
		        `orders`.`virtuemart_paymentmethod_id` AS `payment_method_id`, 
		        `orders`.`virtuemart_shipmentmethod_id` AS `shipment_method_id`, 
		        `#__virtuemart_orderstates`.`order_status_name`, 
		        `vmplang`.`payment_name`, `vmplang`.`payment_desc`, 
		        `vmslang`.`shipment_name`, `vmslang`.`shipment_desc`
		        FROM `#__virtuemart_orders` AS `orders`
		        LEFT JOIN `#__virtuemart_orderstates` ON `orders`.`order_status`=`#__virtuemart_orderstates`.`order_status_code` 
                        LEFT JOIN `#__virtuemart_order_userinfos` `userinfos` ON `orders`.`virtuemart_order_id` =`userinfos`.`virtuemart_order_id`
		        LEFT JOIN `#__virtuemart_paymentmethods` `vmp` ON `orders`.`virtuemart_paymentmethod_id` = `vmp`.`virtuemart_paymentmethod_id`
		        LEFT JOIN `'.$langPayments.'` vmplang ON `orders`.`virtuemart_paymentmethod_id` = `vmplang`.`virtuemart_paymentmethod_id`
		        LEFT JOIN `#__virtuemart_shipmentmethods` `vms` ON `orders`.`virtuemart_shipmentmethod_id` = `vms`.`virtuemart_shipmentmethod_id`
		        LEFT JOIN `'.$langShipments.'` vmslang ON `orders`.`virtuemart_shipmentmethod_id` = `vmslang`.`virtuemart_shipmentmethod_id`
		        WHERE `orders`.`virtuemart_order_id` = '.(int)$orderId.' GROUP BY `orders`.`virtuemart_order_id`');
                        $db->setQuery($sql);
		        $orders[$orderId] = $db->loadObject();
                        
		        $orders[$orderId]->cdate = NbordersHelper::gmStrtotime($orders[$orderId]->created_on);
		        $orders[$orderId]->mdate = NbordersHelper::gmStrtotime($orders[$orderId]->modified_on);
		        
	        }
	        else
	        {
		        $query = '
		        SELECT `order`.`order_id`,`order`.`user_id`, `order`.`order_number`, 
		        `order`.`user_info_id`, `cdate`, `order`.`mdate`, `order_status`, 
		        `order`.`vendor_id`, `order`.`ship_method_id`, `order_currency`, 
		        `method`.`payment_method_id`, `order`.`order_shipping`, 
		        `order`.`order_shipping_tax`,
		        `order_discount`, 
		        (-1 * `order`.`coupon_discount`) AS `coupon_discount`, 
		        NULL AS `order_payment`, 
		        NULL AS `order_payment_tax`, 
		        `coupon_code` , `order_total`, `order_subtotal`, `order_tax`, `customer_note`, 
		        `payment_method_discount`, `payment_method_discount_is_percent`, 
		        `#__vm_order_status`.`order_status_name`
		        FROM `#__vm_orders` AS `order` 
		        LEFT JOIN `#__vm_order_status` ON `order`.`order_status` = `#__vm_order_status`.`order_status_code` 
		        LEFT JOIN `#__vm_order_payment` AS `payment` ON `order`.`order_id` = `payment`.`order_id` 
		        LEFT JOIN `#__vm_payment_method` AS `method` ON  `method`.`payment_method_id` = `payment`.`payment_method_id`
		        LEFT JOIN `#__vm_shipping_rate` AS `shipping` ON `shipping`.`shipping_rate_id` = `order`.`ship_method_id`
		        LEFT JOIN `#__vm_tax_rate` AS `tax` ON `tax`.`tax_rate_id` = `shipping`.`shipping_rate_vat_id` 
		        WHERE `order`.`order_id`='.(int)$orderId.' GROUP BY `order`.`order_id`';
		        
		        $db->setQuery($query);
		        $orders[$orderId] = $db->loadObject();
		        $shipInfo = explode('|', $orders[$orderId]->ship_method_id);
		        $orders[$orderId]->shipment_name = $shipInfo[1];
		        $orders[$orderId]->shipment_desc = $shipInfo[2];
	        }
	        
	        		}
		
		return $orders[$orderId];
	}
	
	
	static function getOrderVendor($orderId)
	{
		$db = JFactory::getDBO();
		if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
			$db->setQuery('SELECT virtuemart_vendor_id FROM #__virtuemart_orders WHERE virtuemart_order_id='.(int)$orderId);
		else
			$db->setQuery('SELECT vendor_id FROM #__vm_orders WHERE order_id='.(int)$orderId);
		return $db->loadResult();
	}
	
	
	static function getOrderItems($orderId=null,$orderItemsIds=null, $ordering = null)
	{
		if (!$orderId && !$orderItemsIds)
			return array();
		
		if (!$ordering)
			$ordering = 'id ASC';
			
		if (!preg_match('#^(\w+)(?: (asc|desc))?$#i',trim($ordering), $match)){
			JError::raiseWarning(0,'Invoice: getOrderItems: Bad ordering param');
			$match = array('id asc', 'id', 'asc');}
	
		$orderCol = trim($match[1]);
		$orderDir = (!empty($match[2]) && in_array(strtolower(trim($match[2])), array('asc','desc'))) ? $match[2] : 'asc';
			
		
		
		//translate order col name
		if ($orderCol=='name')	$orderCol='order_item_name';
		elseif ($orderCol=='sku')	$orderCol='order_item_sku';
	
		if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2){ //all prices convert for one item
	        	
			//translate order col name (VM2)
			if ($orderCol=='id')
				$orderCol='virtuemart_order_item_id';
			elseif ($orderCol=='price')
				$orderCol='product_subtotal_with_tax';
			
			//since VM 2.0.11, product_tax is stored per-item (in older versions it was for item*quantity)
			NbordersHelper::importVMFile('version.php');
			$taxPerItem = class_exists('vmVersion') ? (version_compare(vmVersion::$RELEASE, '2.0.11') >= 0) : true; 
			
			$sql = 'SELECT item.*,item.`virtuemart_order_item_id` AS `order_item_id`, 
				item.`virtuemart_product_id` AS `product_id`,
				item.`virtuemart_vendor_id` AS `vendor_id`, 
				item.product_item_price, 
				item.product_basePriceWithTax AS product_price_with_tax, 
				item.product_subtotal_discount/product_quantity AS product_price_discount, 
				'.($taxPerItem ? 'item.product_tax' : 'item.product_tax/product_quantity AS product_tax').', 
				item.product_subtotal_with_tax/product_quantity AS product_subtotal_with_tax, 
				lang.product_s_desc, lang.product_desc, product.product_weight, product.product_weight_uom,
                medias.file_url_thumb AS item_image
		        FROM `#__virtuemart_order_items` AS item
		        LEFT JOIN `#__virtuemart_products` AS product ON item.virtuemart_product_id = product.virtuemart_product_id
		        LEFT JOIN '.self::getVm2LanguageTable('#__virtuemart_products').' AS lang ON item.virtuemart_product_id = lang.virtuemart_product_id 
                LEFT JOIN `#__virtuemart_product_medias` AS pmedias ON (item.virtuemart_product_id = pmedias.virtuemart_product_id AND pmedias.ordering = 1)
                LEFT JOIN `#__virtuemart_medias` AS medias ON pmedias.virtuemart_media_id = medias.virtuemart_media_id
		        WHERE '.
				($orderId ? '`virtuemart_order_id` = ' . (int)$orderId : ' `virtuemart_order_item_id` IN ('.implode(',',(array)$orderItemsIds).')').
				' GROUP BY item.`virtuemart_order_item_id`'. /*group for all cases */
				' ORDER BY item.`'.$orderCol.'` '.$orderDir;
		}
		else {
			
			//translate order col name (VM1)
			if ($orderCol=='id')	$orderCol='order_item_id';
			elseif ($orderCol=='price')	$orderCol='product_final_price';
			
			$sql = 'SELECT item.*, 
			item.product_item_price, 
			item.product_final_price AS product_price_with_tax,
			NULL AS product_price_discount, 
			(item.product_final_price - item.product_item_price) AS product_tax, 
			item.product_final_price AS product_subtotal_with_tax, 
			product.product_s_desc, product.product_desc, product.product_weight, product.product_weight_uom, 
			product.product_full_image AS item_image
			FROM `#__vm_order_item` AS item 
			LEFT JOIN #__vm_product AS product ON item.product_id = product.product_id 
			WHERE '.($orderId ? 'item.`order_id` = ' 
			. (int)$orderId : ' item.`order_item_id` IN ('.implode(',',(array)$orderItemsIds).')').
			' ORDER BY item.`'.$orderCol.'` '.$orderDir;
		}
		
				$db = JFactory::getDBO();
		$db->setQuery($sql);
		if ($res = $db->loadObjectList())
		{
			if (!COM_NETBASEVM_EXTEND_ORDERS_ISVM2) //prepend image path relative to joomla root
				foreach ($res as &$row)
					if ($row->item_image)
						$row->item_image = /*JPATH_SITE.DS.*/'components'.'/'.'com_virtuemart'.'/'.'shop_image'.'/'.'product'.'/'.$row->item_image;
			
			//unset images that dont exists to not break pdf generate completly
			foreach ($res as &$row)
				if ($row->item_image AND !file_exists(JPATH_SITE.DS.ltrim(str_replace('/',DS,$row->item_image), DS)))
					$row->item_image = null;
		}
		
		return $res;
	}
	
		
	
	/**
	 * Only for VM2.
	 * WARNING: return can differ based on version of VM2
	 * @param int $orderId
	 */
	static function getOrderCalcRules($orderId,$orderItemId=null)
	{
		$db = JFactory::getDBO();
		$where = array();
		if ($orderId)
			$where[] = 'virtuemart_order_id='.(int)$orderId;
		if ($orderItemId)
			$where[] = 'virtuemart_order_item_id='.(int)$orderItemId;
		$sql=('SELECT * FROM #__virtuemart_order_calc_rules WHERE ('.implode(') AND (', $where).') ORDER BY virtuemart_order_calc_rule_id ASC');	
        
                return $db->loadObjectList();
	}
	
	/**
	 * Get fields from order calc rules to retermine if using extended table (probably from 2.0.12, but better check manually)
	 * 
	 * @param	$checkField string, optional	check existence of one field
	 * @return	array/bool
	 */
	static function getOrderCalcRulesFields($checkField = null)
	{
		static $fields; //cache
		
		if (!isset($fields))
		{
			$fields = array();
			$db = JFactory::getDBO();
			$db->setQuery('SHOW FIELDS FROM #__virtuemart_order_calc_rules');
			foreach ($db->loadObjectList() as $field)
				$fields[$field->Field] = $field->Field;
		}
		
		return $checkField ? isset($fields[$checkField]) : $fields;
	}
	
	
 	
	static function getProduct($productId)
	{
		//TODO: použít na počítání discountu a daní nějakou vm funkci?
		
		$db = JFactory::getDBO();
		if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2){
			$langProducts = self::getVm2LanguageTable('#__virtuemart_products');
        	$sql = 'SELECT 
        			`p`.`virtuemart_product_id` AS product_id, 
        			l.product_name AS product_name, 
        			`price`.`product_currency` AS `product_currency`,
            		IF (`price`.override=1,`product_override_price`,`price`.`product_price`) AS `product_price`, 
            		`p`.`product_sku` AS `product_sku`, 
            		`p`.`virtuemart_vendor_id` AS vendor_id, 
            		`p`.product_weight, `p`.product_weight_uom
		        	FROM `#__virtuemart_products` AS p
		        	LEFT JOIN '.$langProducts.' AS l ON p.virtuemart_product_id = l.virtuemart_product_id
            		LEFT JOIN `#__virtuemart_product_prices` AS `price` ON `p`.`virtuemart_product_id` = `price`.`virtuemart_product_id` 
            		WHERE `p`.`virtuemart_product_id`='.(int)$productId.' 
        			GROUP BY `p`.`virtuemart_product_id`';
		}
		else
        	$sql = 'SELECT  
        			`p`.`product_id`, 
        			`p`.`product_name` AS `product_name`, 
            		`price`.`product_price` AS `product_price`, 
            		`price`.`product_currency` AS `product_currency`,
            		`p`.`product_sku` AS `product_sku`, 
            		`p`.`vendor_id` AS vendor_id, 
            		`p`.product_weight, `p`.product_weight_uom, 
            		`tax_rate`, `discount`.*
            		FROM `#__vm_product` AS `p` 
            		LEFT JOIN `#__vm_product_price` AS `price` ON `p`.`product_id` = `price`.`product_id` 
            		LEFT JOIN `#__vm_tax_rate` AS `tax` ON `tax`.`tax_rate_id` = `p`.`product_tax_id` 
            		LEFT JOIN `#__vm_product_discount` AS `discount` ON `discount`.`discount_id` = `p`.`product_discount_id` 
            		WHERE `p`.`product_id`='.(int)$productId.' 
					GROUP BY `p`.`product_id`';
        	
        $db->setQuery($sql);
        return $db->loadObject();
	}
	
	static function getProductPrices($productId)
	{
		$db = JFactory::getDBO();
		
		if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2) //TODO: override ? 
		 	$db->setQuery('SELECT IF(override=1, product_override_price, product_price) AS product_price, product_currency, virtuemart_shoppergroup_id AS shopper_group_id FROM #__virtuemart_product_prices WHERE virtuemart_product_id = '.(int)$productId);
		else
			$db->setQuery('SELECT product_price, product_currency, shopper_group_id FROM #__vm_product_price WHERE product_id = '.(int)$productId);
		
		return $db->loadObjectList();
	}
	
	/**
	 * Limit 100
	 * @param unknown_type $filter
	 */
    static function getAjaxProductList($filter)
    {
        $db = JFactory::getDBO();
        
        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2){
        	$langProducts = self::getVm2LanguageTable('#__virtuemart_products');
        	$db->setQuery('SELECT p.`virtuemart_product_id` AS `id`, l.`product_name`, p.`product_sku`, 
        	p.product_in_stock, p.product_ordered
        	FROM `#__virtuemart_products` AS p
        	LEFT JOIN '.$langProducts.' AS l ON p.virtuemart_product_id=l.virtuemart_product_id
        	WHERE l.`product_name` LIKE ' . $db->Quote('%' . $filter . '%').' OR p.`product_sku` LIKE ' . $db->Quote('%' . $filter . '%'),0,50);
        }
        else
        	$db->setQuery('SELECT `product_id` AS `id`,`product_name`, `product_sku`, product_in_stock
        	FROM `#__vm_product` 
        	WHERE `product_name` LIKE ' . $db->Quote('%' . $filter . '%').' OR `product_sku` LIKE ' . $db->Quote('%' . $filter . '%'),0,50);

        if (count($res = $db->loadObjectList())) foreach ($res as &$product)
        {
        	$product->name = $product->product_sku.' - '.$product->product_name;
        	$product->name.=' ('.JText::_('COM_NETBASEVM_EXTEND_IN_STOCK').': '.$product->product_in_stock;
        	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
        		$product->name.=', '.JText::_('COM_NETBASEVM_EXTEND_ORDERED').': '.$product->product_ordered;
        	$product->name.=')';
        }
        	
        return $res;
    }
    
    static function getAjaxUserList($filterOrig)
    {
		$db = JFactory::getDBO();
		$filter = $db->Quote('%'.$db->escape(JString::strtolower(JString::trim($filterOrig)), true).'%');
		
	    //get user list with all shipping adresses   
		if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
		{
			$searchId = is_numeric($filterOrig) ? ' OR BT.virtuemart_user_id = '.(int)$filterOrig : '';
	        $db->setQuery('SELECT ST.`address_type_name`, ST.`virtuemart_userinfo_id` AS st_user_info_id,
	        BT.`virtuemart_user_id` AS user_id,  BT.`virtuemart_userinfo_id` AS bt_user_info_id,
	        BT.`last_name`, BT.`first_name`, BT.`title`, BT.`middle_name`, BT.`company`, BT.`city` 
	        FROM `#__virtuemart_userinfos` AS BT
	        LEFT JOIN `#__virtuemart_userinfos` AS ST ON (BT.virtuemart_user_id = ST.virtuemart_user_id AND ST.`address_type` = "ST")
	        LEFT JOIN `#__users` AS U ON BT.virtuemart_user_id=U.id
	        WHERE (BT.`address_type` = "BT" 
	        AND BT.`last_name` LIKE ' . $filter . ' 
	        '.$searchId.' 
	        OR BT.`first_name` LIKE ' . $filter . ' 
	        OR U.`email` LIKE ' . $filter . ' 
	        OR BT.`company` LIKE ' . $filter . ' 
	        OR BT.`city` LIKE ' . $filter . ' 
	        OR ST.`address_type_name` LIKE ' . $filter . ' ) 
	        ORDER BY BT.`last_name`', 0, 50);
		}
		else 
		{
			$searchId = is_numeric($filterOrig) ? ' OR BT.user_id = '.(int)$filterOrig : '';
	        $db->setQuery('SELECT ST.`address_type_name`, ST.`user_info_id` AS st_user_info_id,
	        BT.`user_id` AS user_id,  BT.`user_info_id` AS bt_user_info_id,
	        BT.`last_name`, BT.`first_name`, BT.`title`, BT.`middle_name`, BT.`company`, BT.`city` 
	        FROM `#__vm_user_info` AS BT
	        LEFT JOIN `#__vm_user_info` AS ST ON (BT.user_id = ST.user_id AND ST.`address_type` = "ST")
	        WHERE BT.`address_type` = "BT" 
	        AND (BT.`last_name` LIKE ' . $filter . ' 
	        '.$searchId.' 
	        OR BT.`first_name` LIKE ' . $filter . ' 
	        OR BT.`user_email` LIKE ' . $filter . ' 
	        OR BT.`company` LIKE ' . $filter . ' 
	        OR BT.`city` LIKE ' . $filter . ' 
	        OR ST.`address_type_name` LIKE ' . $filter . ' ) 
	        ORDER BY BT.`last_name`', 0, 50);
		}
		
	    $users = $db->loadObjectList();
	    
	    //TODO: each user have to have option to select only billing address
	    //scenario: user have some shipping address
	    
	    $usersOnlyBilling = array(); //element with only billing address (= billing same as shipping)
	    $usersWithShipping = array();
	    
        foreach ($users as $key => $user) {
        	
            $user->id = $user->user_id.';'.$user->bt_user_info_id.';';
            $user->name = $user->title . ' ' . $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name;
            if (($company = JString::trim($user->company)))
                $user->name .= ', ' . $company;
            if (($city = JString::trim($user->city)))
                $user->name .= ', ' . $city; 
            
            if (!isset($usersOnlyBilling[$user->user_id])) //store only "pure" billing infio record (for billing same as shipping)
            	$usersOnlyBilling[$user->user_id] = clone $user; //to break reference
            
            $user->id .= $user->st_user_info_id; //add shipping info id
            	
            if ($user->st_user_info_id && !empty($user->address_type_name) AND $user->address_type_name!='-default-') //append ST adress type name, if not default
            	$user->name .= ' (' .$user->address_type_name.')' ;
            	
            if ($user->st_user_info_id)
            	$usersWithShipping[$user->user_id] = true; //if this is shipping address also, store info about it
        }
        
        foreach ($users as $key => $user) { //add user only billing info before ones with shipping assigned
        	if (isset($usersWithShipping[$user->user_id])){
        		array_splice($users, $key, 0, array($usersOnlyBilling[$user->user_id]));
        		unset($usersWithShipping[$user->user_id]);
        	}
        }
        
        //now join users which dont have VM account
       	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
       	{
       		$searchId = is_numeric($filterOrig) ? ' OR U.id = '.(int)$filterOrig : '';
       		 $db->setQuery('SELECT U.id,U.name,U.username FROM #__users AS U
       		 LEFT JOIN `#__virtuemart_userinfos` AS ui ON ui.virtuemart_user_id=U.id
       		 WHERE ui.virtuemart_user_id IS NULL AND (
       		 LOWER(U.name) LIKE ' . $filter . ' OR 
       		 LOWER(U.username) LIKE ' . $filter . ' OR 
       		 LOWER(U.email) LIKE ' . $filter . ' OR 
       		 LOWER(U.name) LIKE ' . $filter . ' 
       		 '.$searchId.')');
       	}
       	else
       	{
       		 $db->setQuery('SELECT U.id,U.name,U.username FROM #__users AS U
       		 LEFT JOIN `#__vm_user_info` AS ui ON ui.user_id=U.id
       		 WHERE ui.user_id IS NULL AND (
       		 LOWER(U.name) LIKE ' . $filter . ' OR 
       		 LOWER(U.username) LIKE ' . $filter . ' OR 
       		 LOWER(U.email) LIKE ' . $filter . ' OR 
       		 LOWER(U.name) LIKE ' . $filter . ' 
       		 '.$searchId.')');   		
       	}

       	if ($users2 = $db->loadObjectList()) foreach($users2 as $user2)
       	{  	
	       	$newUser = new stdClass();
	        $newUser->id = $user2->id.';;';
	        $newUser->name = $user2->name.' ('.$user2->username.') - '.JText::_('COM_NETBASEVM_EXTEND_ONLY_JOOMLA_USER');
	        
       		array_push($users,$newUser);
       	}

        return $users;
    }
    
    
    /**
     * Get available order statuses.
     * 
     * @return array
     */
    static function getOrderStates()
    {    	
        $db = JFactory::getDBO();
        
        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
        	$db->setQuery('SELECT order_status_code AS \'id\', order_status_name AS \'name\' FROM `#__virtuemart_orderstates`');
        else
        	$db->setQuery('SELECT order_status_code AS \'id\', order_status_name AS \'name\' FROM `#__vm_order_status`');
        
                $orderStatus =  $db->loadObjectList('id');
        foreach ($orderStatus as $key => $status) //translate status
        	$orderStatus[$key]->name=JText::_($status->name);
        	
        return $orderStatus;	
    }
    
    
    static function getTaxRates()
    {
    	$db = JFactory::getDBO();
    	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2) //in VM2, taxes are not explicitely marked as taxes, just select taxes for product or bill with +%
    	 	$db->setQuery('SELECT `virtuemart_calc_id` AS id, (`calc_value`/100) AS value FROM `#__virtuemart_calcs` 
    	 	WHERE (calc_kind=\'Tax\' OR calc_kind=\'TaxBill\' OR calc_kind=\'VatTax\') AND calc_value_mathop=\'+%\' ORDER BY `value` ASC');
    	else
    		$db->setQuery('SELECT `tax_rate` AS id, `tax_rate` AS value FROM `#__vm_tax_rate` ORDER BY `value` ASC');
    		
    	$already = array(); //prevent duplicates
    	$taxRates = $db->loadObjectList('id');
    	$result = array();
    	if (count($taxRates)) foreach ($taxRates as $key => &$taxRate){
    		
    		$taxRate->value = (float)$taxRate->value;
    		$taxRate->name = ($taxRate->value*100).'%';

    		if (in_array($taxRate->value,$already))
    			unset($taxRates[$key]);
    		else
    			$already[] = $taxRate->value;
    	}
    	
	   	$zeroTaxRate = new StdClass(); //add 0% tax rate
	    $zeroTaxRate->id = 0;
	    $zeroTaxRate->value = 0;
	    $zeroTaxRate->name = '0%';
	    array_unshift($taxRates, $zeroTaxRate);
	    
    	return $taxRates;
    }
    
    static function getPaymentMethod($methodId)
    {
    	$db = JFactory::getDBO();
    	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2){
    		$langPayments = self::getVm2LanguageTable('#__virtuemart_paymentmethods');
    		$db->setQuery('SELECT *
		        FROM `#__virtuemart_paymentmethods` `vmp` 
		        LEFT JOIN `'.$langPayments.'` vmplang ON `vmp`.`virtuemart_paymentmethod_id` = `vmplang`.`virtuemart_paymentmethod_id`
		        WHERE `vmp`.`virtuemart_paymentmethod_id` = '.(int)$methodId);
    		
    		if ($method = $db->loadObject()){ //parse also params string
				$params = explode('|',$method->payment_params);
    			foreach ($params as $param){
    				$param = explode('=',$param,2);
    				if (count($param)>1)
    					$method->{$param[0]} = trim($param[1],'"');
    			}
    		}

    		return $method;
    	}
    	else{
    		$db->setQuery('SELECT * FROM `#__vm_payment_method` 
    		WHERE `payment_method_id` = '.(int)$methodId);
    		return $db->loadObject();
    	}
    }
	
    static function getVendors()
    {
        $db = JFactory::getDBO();
        
        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
        	$db->setQuery('SELECT virtuemart_vendor_id AS `id`, `vendor_name` AS `name` FROM `#__virtuemart_vendors` ORDER BY `name` ASC');
		else
        	$db->setQuery('SELECT `vendor_id` AS id, `vendor_name` AS name FROM `#__vm_vendor` ORDER BY `name` ASC');
        
        return $db->loadObjectList();
    }
    
    static function getCurrencies()
    {
        $db = JFactory::getDBO();
        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
        	$db->setQuery('SELECT `currency_name`, `currency_name` AS name, `virtuemart_currency_id`, `virtuemart_currency_id` AS id, `currency_symbol` AS `symbol`, currency_code_3 AS currency_code FROM `#__virtuemart_currencies` ORDER BY `currency_name` ASC');
        else
        	$db->setQuery('SELECT `currency_name`, `currency_name` AS name, `currency_code`, `currency_code` AS id FROM `#__vm_currency` ORDER BY `currency_name` ASC');
        
        return $db->loadObjectList();
    }
    
    static function getCountriesDB()
    {
        $db = JFactory::getDBO();
        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
        	$db->setQuery('SELECT `country_name`, `country_name` AS name, `country_3_code`, `country_2_code`, `virtuemart_country_id` AS id, `virtuemart_country_id` AS `country_id` FROM `#__virtuemart_countries` ORDER BY `country_name` ASC');
        else
        	$db->setQuery('SELECT `country_name`, `country_name` AS name, `country_3_code`, `country_2_code`, `country_3_code` AS id, `country_id` FROM `#__vm_country` ORDER BY `country_name` ASC');
    
    	return $db->loadObjectList();;
    }
    
    static function getCountries()
    {
        $countries = self::getCountriesDB();

         foreach ($countries as $i => $country) //translate
          	$countries[$i]->name = JText::sprintf('COM_NETBASEVM_EXTEND_COUNTRY_SHORT_INFO', $country->country_name, $country->country_3_code);
          									
        $newCountry = new stdClass();
        $newCountry->id = COM_NETBASEVM_EXTEND_ORDERS_ISVM2 ? 0 : '-';
        $newCountry->name =  JText::_('COM_NETBASEVM_EXTEND_SELECT');
        array_unshift($countries,$newCountry);

        return $countries;
    }
    
    static function getStatesDB($country = null)
    {
    	$db = JFactory::getDBO();
        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
        	$db->setQuery('SELECT `state_name`, `state_name` AS name, `state_2_code`, `state_3_code`, `virtuemart_state_id` AS id FROM `#__virtuemart_states` 
        	'.($country ? 'WHERE virtuemart_country_id = '.(int)$country : '').' ORDER BY `state_name` ASC');
        else{
        	
        	if ($country && !is_numeric($country)) {//country code
        		$db->setQuery('SELECT country_id FROM `#__vm_country` WHERE country_3_code LIKE '.$db->Quote($country));
        		$country = $db->loadResult();
        	}
        	
        	$db->setQuery('SELECT `state_name`, `state_name` AS name, `state_2_code`, `state_3_code`, state_2_code AS id FROM `#__vm_state` 
        	'.($country ? 'WHERE country_id = '.(int)$country : '').' ORDER BY `state_name` ASC');
        }
        return $db->loadObjectList();
    }
    
    /**
     * Get states list
     * 
     * @param unknown_type (optional) $country	country id (VM2) OR country_3_code (VM1)
     */
    static function getStates($country = null)
    {
        $states = self::getStatesDB($country);
        
        $newState = new stdClass();
        $newState->id = COM_NETBASEVM_EXTEND_ORDERS_ISVM2 ? 0 : '-';
        $newState->name =  JText::_('COM_NETBASEVM_EXTEND_SELECT');
        array_unshift($states,$newState);
        
        return $states;
    }
    
    static function getPayments()
    {
    	static $cache;
    	if (isset($cache))
    		return $cache;
    	
        $db = JFactory::getDBO();
        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
        {
            $langPayments = InvoiceGetter::getVm2LanguageTable('#__virtuemart_paymentmethods');
    		$db->setQuery('SELECT *, 
    			`#__virtuemart_paymentmethods`.virtuemart_paymentmethod_id AS `id`,
    			`'.$langPayments.'`.`payment_name` AS `name`, 
    			`'.$langPayments.'`.`payment_desc` AS `desc`
    			FROM `#__virtuemart_paymentmethods` LEFT JOIN `'.$langPayments.'` ON  
    			#__virtuemart_paymentmethods.virtuemart_paymentmethod_id = `'.$langPayments.'`.virtuemart_paymentmethod_id');
        }
        else
        	$db->setQuery('SELECT 
        	`payment_method_id` AS id, 
        	`payment_method_name` AS name, 
        	NULL AS `desc`,  
        	`payment_method_discount`, 
        	`payment_method_discount_is_percent` 
        	FROM `#__vm_payment_method` WHERE `payment_enabled`=\'Y\' ORDER BY `payment_method_name` ASC');
        $cache = $db->loadObjectList('id');
        return $cache;
    }
    
    static function getShippingsVM2()
    {
    	static $shippings;
    	if (empty($shippings))
    	{
    		$shippings = array();
	    	//TODO: we need to "fake" whole virtue matrt cart object to pass it to shipping plugins to get available shippings ?
	    	$langShippings = InvoiceGetter::getVm2LanguageTable('#__virtuemart_shipmentmethods');
	    	
	    	$db = JFactory::getDBO();
	    	$db->setQuery('SELECT s.virtuemart_shipmentmethod_id, 
	    	l.shipment_name, l.shipment_desc, s.shipment_params
	    	FROM #__virtuemart_shipmentmethods s
	    	LEFT JOIN '.$langShippings.' l ON  
	    	s.virtuemart_shipmentmethod_id = l.virtuemart_shipmentmethod_id');
	    	$dbshippings = $db->loadObjectList();
	    	$shippings = array();
	    	foreach ($dbshippings as $dbshipping)
	    	{
	    		$shipping = new stdClass();
	    		$shipping->shipping_rate_id = $dbshipping->virtuemart_shipmentmethod_id;
	    		$shipping->name = $dbshipping->shipment_name;
	    		$shipping->desc = $dbshipping->shipment_desc;
	    		foreach (explode('|', $dbshipping->shipment_params) as $param){
	    			$paramVal = explode('=',$param,2);
	    			if (count($paramVal)>1)
	    				$shipping->{$paramVal[0]} = trim($paramVal[1],'"');
	    		}
	    		$shippings[$shipping->shipping_rate_id] = $shipping;
	    	}
    	}
    	return $shippings;
    }
    
    /**
     * Get aviable shippings array. Standard shippings from db, other from shipping modules. 
     * But it is not very reliable, shipping modules are little moody (currency rates change, ...)
     * 
     * @param	string	used by shipping module; from view->orderData->user_info_id  (to get adress)
     * @param 	string	used by shipping module; currency to count proper prices
     * @param	int		used by shipping module; overal weight of order
     */
    static function getShippingsVM1($user_info_id, $currency, $userId, $weight = 0)
    {
    	static $shippings;
    	
    	if (!empty($shippings[$user_info_id.$currency.$weight])) //cache
    		return $shippings[$user_info_id.$currency.$weight];
    	
    	$decimals = InvoiceCurrencyDisplay::getDecimals();
    	
    	//get VirtueMart framework
        global $mosConfig_absolute_path;
        
        NbordersHelper::importVMFile('virtuemart_parser.php',false);
      	
    	//1. Get all values for standard_shipping from db    	
        $db = JFactory::getDBO();
        $db->setQuery(
        	'SELECT `shipping_rate_id`, `shipping_carrier_name`, `shipping_rate_name`, `currency_code`, `shipping_rate_value`, 
        		`shipping_rate_package_fee`, `shipping_rate_vat_id`, `tax_rate` 
        		FROM `#__vm_shipping_rate` AS `rate` 
        		LEFT JOIN `#__vm_shipping_carrier` AS `carrier` ON `carrier`.`shipping_carrier_id` = `rate`.`shipping_rate_carrier_id`
        		LEFT JOIN `#__vm_currency` AS `currency` ON `currency`.`currency_id` = `rate`.`shipping_rate_currency_id` 
        		LEFT JOIN `#__vm_tax_rate` AS `tax` ON `tax`.`tax_rate_id` = `rate`.`shipping_rate_vat_id` 
        		ORDER BY `shipping_carrier_name` ASC, `shipping_rate_name` ASC');
  
        $items = $db->loadObjectList();
        // standard shipping
		if(!empty($items))
		{
			foreach ($items as $item)
			{
				// convert to current currency
				$item->shipping_rate_value = $GLOBALS['CURRENCY']->convert($item->shipping_rate_value, $item->currency_code, $currency) ;
				$item->shipping_rate_package_fee = $GLOBALS['CURRENCY']->convert($item->shipping_rate_package_fee, $item->currency_code, $currency) ;
							
				$shippingRateString = implode('|', array(
					'standard_shipping',
					$item->shipping_carrier_name,
					$item->shipping_rate_name,
					number_format($item->shipping_rate_value + $item->shipping_rate_package_fee, $decimals, '.', ''),
					$item->shipping_rate_id
				));
	
				$shipping = new stdClass();
				$shipping->shipping_rate_id = $shippingRateString;
				$shipping->name = array('',
					$item->shipping_carrier_name,
					(strlen($item->shipping_rate_name) > 50 ? substr($item->shipping_rate_name, 0, 50)."..." : $item->shipping_rate_name),
					round($item->shipping_rate_value,$decimals).'+'.round($item->shipping_rate_package_fee, $decimals),
					$currency . ', VAT '. round($item->tax_rate * 100, 2).'%');
				$shipping->tax_rate = $item->tax_rate*1;
				$shippings[$user_info_id.$currency.$weight][] = $shipping;
			}
		}
        // 2. Get other shippings from custom shipping modules. 
        $GLOBALS['product_currency'] = $currency;
        global $weight_total;
		$weight_total = (is_numeric($weight)) ?  $weight : 0;  //store total weight for purpose of finding shippings
		ob_start();
		ps_checkout::list_shipping_methods($user_info_id, null); //get methods html. note first paraneter, that is user info id from vm_user_info table, from which is token his country and zip in modules. so doesen't matter which country you select in VM Invoice form!!!
		$shippingsHTML = ob_get_clean(); //catch thrown html shipping form from buffer

		if (preg_match_all('/value=["\'](.+)["\']/iU', $shippingsHTML, $matches)) //find radio inputs in html
		{
			foreach ($matches[1] as $shippingRateString)
			{
				$shippingRateString = urldecode($shippingRateString);
				$shippingValues = explode('|', $shippingRateString);

				if (count($shippingValues) > 3 AND trim($shippingValues[0]) != 'standard_shipping') { //not involve standard_shipping, that is handled above
				
					//substract shipping tax from price
					$tax = InvoiceGetter::getVM1ShippingTax($shippingValues[0], isset($shippingValues[4]) ? $shippingValues[4] : '', $userId);
					if ($tax > 0)
						$shippingValues[3] = round($shippingValues[3] - ($shippingValues[3] * $tax), $decimals);
						
					if (!isset($shippingValues[4]))
						$shippingValues[4]= 0; //shipping rate id
						
					$shipping = new stdClass();
					$shipping->shipping_rate_id = implode('|', $shippingValues);
					$shipping->name = array(
    					$shippingValues[0],
    					$shippingValues[1],
    					strlen($shippingValues[2]) > 50 ? substr($shippingValues[2], 0, 50) . "..." : $shippingValues[2],
    					round($shippingValues[3], $decimals),
    					$currency .', VAT '.round($tax * 100, 2).'%');
    				$shipping->tax_rate = $tax*1;
					$shippings[$user_info_id.$currency.$weight][] = $shipping;
				}
			}
		}
        
        return $shippings[$user_info_id.$currency.$weight];
    }
    
    /**
     * Gets shipping tax from VM's shipping module.
     * 
     * @param	string	name of shipping module
     * @param	int		shipping method id (=last, optional parameter in shipping method string)
     * @param	id		id of user who does the order (it will be computed by his country maybe)
     */
    function getVM1ShippingTax($shippingClass, $shippMethodId=null, $userId=null)
    {
    	NbordersHelper::importVMFile('classes/ps_ini.php');
    	if (!NbordersHelper::importVMFile('classes/shipping/'.$shippingClass.'.php'))
    		return false;

	    $shipping = new $shippingClass();
	    $_REQUEST["shipping_rate_id"]="||||".$shippMethodId; //ship method sting...
	    if (!is_null($userId)) $_SESSION['auth']['user_id']=$userId; //user id to determine address...
	    $shipTaxRate = !is_null($shippMethodId) ? $shipping->get_tax_rate($shippMethodId) : $shipping->get_tax_rate();
		return $shipTaxRate;
    }
    
	static function getVendor($vendorId=null)
    {
    	if (empty($vendorId)) //keys for template helper. should be same as returned values below
    	return array('company_name', 'title', 'first_name', 'middle_name', 'last_name', 'phone_1', 'phone_2', 'phone_vendor', 'fax', 'email',
    	 'address_1', 'address_2', 'city', 'state_name', 'state_2_code', 'state_3_code', 'country_name', 'country_2_code', 'country_3_code', 
    	 'zip', 'store_name', 'store_desc', 'url', 'currency_name', 'currency_3_code');
    	
    	$db = JFactory::getDBO();
    	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2){
    		$langVendors = InvoiceGetter::getVm2LanguageTable('#__virtuemart_vendors');
	        $db->setQuery(
	        'SELECT i.*,
	        v.vendor_name AS company_name, 
	        i.title AS title, 
	        i.first_name AS first_name , 
	        i.middle_name AS middle_name, 
	        i.last_name AS last_name, 
	        i.phone_1 AS phone_1, 
	        i.phone_2 AS phone_2, 
	        vlang.vendor_phone AS phone_vendor,
	        i.fax AS fax, 
	        u.email AS email, 
	        i.address_1 AS address_1, 
	        i.address_2 AS address_2, 
	        i.city AS city,
	        s.state_name,
	        s.state_2_code,
	        s.state_3_code,
	        c.country_name,
	        c.country_2_code,
	        c.country_3_code, 
	        i.zip AS zip, 
	        vlang.vendor_store_name AS store_name, 
	        vlang.vendor_store_desc AS store_desc, 
	        vlang.vendor_url AS url, 
	        IF (curr.currency_name IS NULL, CONCAT(\'Currency \',v.vendor_currency), curr.currency_name) AS currency_name, 
	        IF (curr.currency_code_3 IS NULL, CONCAT(\'Currency \',v.vendor_currency), curr.currency_code_3) AS currency_3_code
	        FROM `#__virtuemart_vendors` AS v
	        LEFT JOIN `#__virtuemart_vmusers` AS vmusers ON v.virtuemart_vendor_id = vmusers.virtuemart_vendor_id
	        LEFT JOIN `#__virtuemart_userinfos` AS i ON (vmusers.virtuemart_user_id=i.virtuemart_user_id AND i.address_type=\'BT\')
	        LEFT JOIN `#__virtuemart_countries` AS c ON (i.virtuemart_country_id = c.virtuemart_country_id)
	        LEFT JOIN `#__virtuemart_states` AS s ON (i.virtuemart_state_id = s.virtuemart_state_id)
	        LEFT JOIN '.$langVendors.' AS vlang ON vmusers.virtuemart_vendor_id = .vlang.virtuemart_vendor_id
	        LEFT JOIN `#__users` AS u ON vmusers.virtuemart_user_id = u.id
	        LEFT JOIN `#__virtuemart_currencies` AS curr ON v.vendor_currency = curr.virtuemart_currency_id
	        WHERE v.virtuemart_vendor_id='.(int)$vendorId.' GROUP BY v.virtuemart_vendor_id LIMIT 1');
	        /*
	        IF (s.state_name IS NULL, CONCAT(\'State \',i.virtuemart_state_id), s.state_name) AS state_name,
	        IF (s.state_2_code IS NULL, CONCAT(\'State \',i.virtuemart_state_id),s.state_2_code) AS state_2_code,
	        IF (s.state_3_code IS NULL, CONCAT(\'State \',i.virtuemart_state_id), s.state_3_code) AS state_3_code,
	        IF (c.country_name IS NULL, CONCAT(\'Country \',i.virtuemart_country_id), c.country_name) AS country_name,
	        IF (c.country_2_code IS NULL, CONCAT(\'Country \',i.virtuemart_country_id), c.country_2_code) AS country_2_code,
	        IF (c.country_3_code IS NULL, CONCAT(\'Country \',i.virtuemart_country_id), c.country_3_code) AS country_3_code, 
	         */
    	}
    	else
	        $db->setQuery(
	        "SELECT v.*, 
	        v.vendor_name AS company_name, 
	        v.contact_title AS title, 
	        v.contact_first_name AS first_name , 
	        v.contact_middle_name AS middle_name, 
	        v.contact_last_name AS last_name, 
	        v.contact_phone_1 AS phone_1, 
	        v.contact_phone_2 AS phone_2, 
	        v.vendor_phone AS phone_vendor, 
	        v.contact_fax AS fax, 
	        v.contact_email AS email, 
	        v.vendor_address_1 AS address_1, 
	        v.vendor_address_2 AS address_2, 
	        v.vendor_city AS city,
	        IF (s.state_name IS NULL, v.vendor_state, s.state_name) AS state_name,
	        IF (s.state_2_code IS NULL, v.vendor_state, s.state_2_code) AS state_2_code,
	        IF (s.state_3_code IS NULL, v.vendor_state, s.state_3_code) AS state_3_code,
	        IF (c.country_name IS NULL, v.vendor_country, c.country_name) AS country_name,
	        IF (c.country_2_code IS NULL, v.vendor_country, c.country_2_code) AS country_2_code,
	        IF (c.country_3_code IS NULL, v.vendor_country, c.country_3_code) AS country_3_code, 
	        v.vendor_zip AS zip,
	        v.vendor_store_name AS store_name,
	        v.vendor_store_desc AS store_desc,
	        v.vendor_url AS url, 
	        IF (curr.currency_name IS NULL, v.vendor_currency, curr.currency_name) AS currency_name, 
	        v.vendor_currency AS currency_3_code
	        	FROM `#__vm_vendor` AS v
	          	LEFT JOIN `#__vm_country` AS c ON (v.`vendor_country` = c.`country_3_code` OR v.`vendor_country` = c.`country_2_code`)
	          	LEFT JOIN `#__vm_state` AS s ON ((v.`vendor_state` = s.`state_3_code` OR v.`vendor_state` = s.`state_2_code`) AND s.country_id = c.country_id)
	          	LEFT JOIN `#__vm_currency` AS curr ON (v.`vendor_currency` = curr.`currency_code`)
	      		WHERE v.`vendor_id` = " . (int)$vendorId.' GROUP BY v.vendor_id LIMIT 1');

        return $db->loadObject();
    }
    
    static function getVendorMailAndName($vendorId)
    {
    	static $cache;
    	
    	if (!isset($cache[$vendorId])){
    		$db = JFactory::getDBO();
    		if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
    			$db->setQuery('
    			SELECT v.vendor_name AS name, i.email
    			FROM `#__virtuemart_vendors` AS v
	        	LEFT JOIN `#__virtuemart_vmusers` AS vmusers ON v.virtuemart_vendor_id = vmusers.virtuemart_vendor_id
	        	WHERE virtuemart_vendor_id = '.(int)$vendorId);
    		else
            	$db->setQuery("SELECT v.`contact_email` AS email, v.`vendor_name` AS name FROM `#__vm_vendor` AS v WHERE v.`vendor_id` = ".(int)$vendorId);
            $cache[$vendorId] = $db->loadObject();
    	}
    	return $cache[$vendorId];
    }
    
    /**
     * Get vendor title image relative to joomla root.
     * 
     * @param unknown_type $vendorId
     */
    static function getVendorImage($vendorId)
    {
    	$db = JFactory::getDBO();
    	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2){ //get first vendor image in vm2
    	
    		$db->setQuery('SELECT m.file_url 
    		FROM #__virtuemart_vendor_medias vm 
    		JOIN #__virtuemart_medias m ON vm.virtuemart_media_id = m.virtuemart_media_id
    		WHERE vm.virtuemart_vendor_id = '.(int)$vendorId.'
    		ORDER BY vm.ordering ASC LIMIT 1');
    		if ($filename = ltrim($db->loadResult(),' /\\'))
    			return $filename;
			else
				return false;
    	}
    	else //vm1
    	{
    		$vendor = self::getVendor($vendorId);
			if ($vendor && $vendor->vendor_full_image)
				return "components/com_virtuemart/shop_image/vendor/" . $vendor->vendor_full_image;
			else
				return false;
    	}
    }
    
    /**
     * Get order address. If called ST and not presented (= when BT = ST), returned is ST.
     * If called with no parameter, returned is list of available fields (for replacables tags help)
     */
    static function getOrderAddress($orderId=null,$type=null)
    {
    	$db = JFactory::getDBO();
    	
    	if (!$orderId){ //keys for template helper. should be same as returned values below
    		
    		static $return;
    		
    		if (empty($return))
    		{
	    		$return = array();
	    		$exclude = array('virtuemart_order_userinfo_id','virtuemart_order_id','virtuemart_user_id',
	    		'order_info_id','order_id','address_type',
	    		'created_on','created_by','modified_on','modified_by','locked_on','locked_by',
	    		'virtuemart_state_id', 'virtuemart_country_id'); //ugly fields
	    		
	    		$db->setQuery('SHOW COLUMNS FROM '.(COM_NETBASEVM_EXTEND_ORDERS_ISVM2 ? '#__virtuemart_order_userinfos' : '#__vm_order_user_info'));
	
	    		foreach ( $db->loadObjectList() as $column)
	    			if (!in_array($column->Field,$exclude))
	    				$return[] = $column->Field;
	
	    		$return[]='country_name';
	    		$return[]='state';
	    		$return[]='state_2_code';
	    		if (!in_array('user_id',$return))
	    			$return[] = 'user_id';
	    		$return[]='username';
	    		
	    		$return = array_unique($return);
    		}
    		return $return;
    	}

    	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
	        $db->setQuery(
	        "SELECT i.*, i.`virtuemart_order_id` AS order_id, i.virtuemart_user_id AS user_id, u.username, c.`country_name`, s.`state_name` AS `state`, s.`state_2_code`
	        	FROM `#__virtuemart_order_userinfos` AS i
	        	LEFT JOIN `#__virtuemart_states` AS s ON (i.`virtuemart_state_id` = s.`virtuemart_state_id`)
	          	LEFT JOIN `#__virtuemart_countries` AS c ON (i.`virtuemart_country_id` = c.`virtuemart_country_id`)
	          	LEFT JOIN `#__users` AS u ON i.virtuemart_user_id=u.id
	      		WHERE i.`virtuemart_order_id` = $orderId AND i.`address_type` = '$type'");
    	else
	        $db->setQuery(
	        "SELECT i.*, u.username, c.`country_name`, i.`state` AS `state_2_code`, IFNULL(s.`state_name`, i.`state`) AS `state`
	        	FROM `#__vm_orders` AS o 
	          	INNER JOIN `#__vm_order_user_info` AS i ON (o.`order_id` = i.`order_id` AND i.`address_type` = '$type')
	          	LEFT JOIN `#__vm_country` AS c ON (i.`country` = c.`country_3_code`)
	        	LEFT JOIN `#__vm_state` AS s ON ((i.`state` = s.`state_2_code` OR i.`state` = s.`state_id`) AND s.country_id = c.country_id)
	          	LEFT JOIN `#__users` AS u ON i.user_id=u.id
	      		WHERE o.`order_id` = " .(int)$orderId);
	        
	    $address  = $db->loadObject();
	    
	    if (!$address AND $type=='ST') //no shipping address stored = use billing
	    	$address = self::getOrderAddress($orderId,'BT');
	        
	   	if (is_object($address) && (!$address->address_type_name OR $address->address_type_name=='-default-'))
	    	$address->address_type_name = null; //form VM1
	        	
        return $address;
    }
    
    static function getVMExtraField($fieldID)
    {
    	$db = JFactory::getDBO();
    	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
    		$db->setQuery("SELECT `name`, `title` FROM `#__virtuemart_userfields` WHERE `virtuemart_userfield_id` = ".(int)$fieldID);
    	else
        	$db->setQuery("SELECT `name`, `title` FROM `#__vm_userfield` WHERE `fieldid` = ".(int)$fieldID);
        return $db->loadAssoc();
    }
    
    /**
     * Get (last) shipping date in timestamp
     * 
     * @param int $orderId
     */
    static function getOrderShippingDate($orderId)
    {
    	$db = JFactory::getDBO();
    	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
    		$db->setQuery("SELECT `created_on` FROM `#__virtuemart_order_histories` WHERE `virtuemart_order_id` = ".(int)$orderId.
	            	" AND `order_status_code`='S' ORDER BY `created_on` DESC LIMIT 1");
    	else
    		$db->setQuery("SELECT `date_added` FROM `#__vm_order_history` WHERE `order_id` = ".(int)$orderId.
	            	" AND `order_status_code`='S' ORDER BY `date_added` DESC LIMIT 1");
    	$res = 	$db->loadResult();
    	return $res ? NbordersHelper::gmStrtotime($res) : null;
    }
    
    static function getCustomerNumber($userId)
    {
    	$db = JFactory::getDBO();
    	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
    		$db->setQuery('SELECT `customer_number` FROM `#__virtuemart_vmusers` WHERE `virtuemart_user_id` = '.(int)$userId);
    	else
    		$db->setQuery('SELECT `customer_number` FROM `#__vm_shopper_vendor_xref` WHERE `user_id` = '.(int)$userId);
    		
		return $db->loadResult();	        
    }
    
    static function getShopperGroup($userId, $getId = false)
    {
    	$db = JFactory::getDBO();
    	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
    		$db->setQuery('SELECT `xref`.`virtuemart_shoppergroup_id` AS `grid`, `group`.`shopper_group_name` AS `grname`
		        	FROM `#__virtuemart_vmuser_shoppergroups` `xref` 
		        	LEFT JOIN `#__virtuemart_shoppergroups` `group` ON `xref`.`virtuemart_shoppergroup_id` = `group`.`virtuemart_shoppergroup_id`
		        	WHERE `xref`.`virtuemart_user_id` = '.(int)$userId);
    	else
    		$db->setQuery('SELECT `xref`.`shopper_group_id` AS `grid`, `group`.`shopper_group_name` AS `grname`
		        	FROM `#__vm_shopper_vendor_xref` `xref` 
		        	LEFT JOIN `#__vm_shopper_group` `group` ON `xref`.`shopper_group_id` = `group`.`shopper_group_id`
		        	WHERE `xref`.`user_id` = '.(int)$userId);
    			        	
    	$cust = $db->loadObject();
    		
    	return ($cust ? ($getId ? $cust->grid : ($cust->grname ? $cust->grname : $cust->grid)) : null);		        	
    }
    
    static function getShopperGroups($vendorId = null)
    {
    	$db = JFactory::getDBO();
    	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
    		$db->setQuery('SELECT virtuemart_shoppergroup_id AS id, shopper_group_name AS name FROM #__virtuemart_shoppergroups '.($vendorId ? 'WHERE virtuemart_vendor_id='.(int)$vendorId : ''));
    	else
    		$db->setQuery('SELECT shopper_group_id AS id, shopper_group_name AS name FROM #__vm_shopper_group '.($vendorId ? 'WHERE vendor_id='.(int)$vendorId : ''));
    	
		return $db->loadObjectList('id');
    }
    
    static function getDefaultShopperGroup($vendorId = null)
    {
    	$db = JFactory::getDBO();
    	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
    		$db->setQuery('SELECT virtuemart_shoppergroup_id FROM #__virtuemart_shoppergroups WHERE `default` = 1'.($vendorId ? ' AND virtuemart_vendor_id='.(int)$vendorId : ''));
    	else
    		$db->setQuery('SELECT shopper_group_id FROM #__vm_shopper_group WHERE `default` = 1'.($vendorId ? ' AND vendor_id='.(int)$vendorId : ''));
    	return $db->loadResult();
    }

    /**
     * Get default currency. Returned currency for vendor, if not set, currency used by most products.
     */
    static function getDefaultCurrency($vendorId=null)
    {
    	$db = JFactory::getDBO();	
    	if ($vendorId){
    		if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
    			$db->setQuery('SELECT vendor_currency FROM `#__virtuemart_vendors` WHERE virtuemart_vendor_id = '.(int)$vendorId);
    		else
    			$db->setQuery('SELECT vendor_currency FROM `#__vm_vendor` WHERE vendor_id = '.(int)$vendorId);
    			
    		return $db->loadResult();
    	}	
		if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
			$db->setQuery('SELECT product_currency,count(virtuemart_product_price_id) AS records FROM `#__virtuemart_product_prices` GROUP BY product_currency ORDER BY records DESC LIMIT 1');
		else
			$db->setQuery('SELECT product_currency,count(product_price_id) AS records FROM `#__vm_product_price` GROUP BY product_currency ORDER BY records DESC LIMIT 1');
    	return $db->loadResult();
    }
    
    /**
     * Get user info eigther by user info id OR by combination of user_id and address type
     * 
     * @param unknown_type $userInfoId	VM1: some long hash string, VM2: int
     * @param unknown_type $userId		Joomla user id
     * @param unknown_type $addressType	BT or ST
     */
    static function getUserInfo($userInfoId=null,$userId=null,$addressType=null)
    {
    	$db = JFactory::getDBO();	
    	
    	if (!$userInfoId && !$userId){ //keys for template helper. should be same as returned values below
    	
    		static $return;
    	
    		if (empty($return))
    		{
    			$return = array();
    			$exclude = array('virtuemart_order_userinfo_id','virtuemart_order_id','virtuemart_user_id',
    					'order_info_id','order_id','address_type',
    					'created_on','created_by','modified_on','modified_by','locked_on','locked_by',
    					'virtuemart_state_id', 'virtuemart_country_id'); //ugly fields
    			 
    			$db->setQuery('SHOW COLUMNS FROM '.(COM_NETBASEVM_EXTEND_ORDERS_ISVM2 ? '#__virtuemart_userinfos' : '#__vm_user_info'));
    	
    			foreach ( $db->loadObjectList() as $column)
    				if (!in_array($column->Field,$exclude))
    					$return[] = $column->Field;
    	
    			$return[]='user_id';
    			$return[]='country';
    			$return[]='state';
	 
    			$return = array_unique($return);
    		}
    		return $return;
    	}
    	
    	
        if ($userInfoId) {
			if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
				$where = 'UI.`virtuemart_userinfo_id` = ' . (int)$userInfoId;
			else
        		$where = '`user_info_id` = ' . $db->Quote($userInfoId);
        }
        elseif ($userId && $addressType){
        	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
        		$where = 'UI.`virtuemart_user_id` = ' . (int) $userId . ' AND UI.`address_type` = ' . $db->Quote($addressType);
        	else
        		$where = '`user_id` = ' . (int) $userId . ' AND `address_type` = ' . $db->Quote($addressType);
        }
        else
        	return false;
        
        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
        	$db->setQuery('SELECT UI.*,    
        		UI.virtuemart_userinfo_id AS user_info_id,
        		UI.virtuemart_user_id AS user_id, 
	        	UI.virtuemart_country_id AS country, 
	        	UI.virtuemart_state_id AS state, 
	        	U.email AS email
	        	FROM `#__virtuemart_userinfos` UI
	        	LEFT JOIN #__users U ON UI.virtuemart_user_id=U.id WHERE '.$where);
        else
        	$db->setQuery('SELECT *, user_email AS email FROM `#__vm_user_info` WHERE '.$where);
         	
        $info = $db->loadObject();

        if (is_object($info) && (!$info->address_type_name OR $info->address_type_name=='-default-'))
	    	$info->address_type_name = null;
	        	
        return $info;
    }
    
    
    
    static function getOrderUserId($orderId)
    {
    	$db = JFactory::getDBO();
    	
       	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
        	$db->setQuery("SELECT U.`id` FROM `#__virtuemart_orders` AS O JOIN `#__users` AS U ON O.virtuemart_user_id=U.id WHERE O.`virtuemart_order_id` = ".(int)$orderId);
        else
			$db->setQuery("SELECT U.`id` FROM `#__vm_orders` AS O JOIN `#__users` AS U ON O.user_id=U.id WHERE O.`order_id` = ".(int)$orderId);
    	
		
			
				return $db->loadResult();	
    }

    
    /**
     * Get user info of order. If address not presented, returned empty. (which is different from getOrderAddress behaivor)
     */
    static function getOrderUserInfo($orderId,$addressType)
    {
    	static $cache;
    	if (!isset($cache[$orderId.$addressType])){
	        $db = JFactory::getDBO();
	        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
	        	$db->setQuery('SELECT *,
	        	virtuemart_order_userinfo_id AS order_info_id, 
	        	virtuemart_order_id AS order_id, 
	        	virtuemart_user_id AS user_id, 
	        	virtuemart_country_id AS country, 
	        	virtuemart_state_id AS state
	        	FROM `#__virtuemart_order_userinfos` WHERE `virtuemart_order_id` = ' . (int) $orderId . ' AND `address_type` = ' . $db->Quote($addressType));
	        else
	        	$db->setQuery('SELECT *, 
	        	`user_email` AS `email`
	        	FROM `#__vm_order_user_info` WHERE `order_id` = ' . (int) $orderId . ' AND `address_type` = ' . $db->Quote($addressType));
	        
	        $info = $db->loadObject();

	        if (is_object($info) && (!$info->address_type_name OR $info->address_type_name=='-default-'))
	        	$info->address_type_name = null; //for VM1
	        		
	        $cache[$orderId.$addressType] = $info;
    	}

    	return $cache[$orderId.$addressType];
    }
    
    
    /**
     * Returns order numbers:
     * 1) which are unsent (or delivery note unsent if send both)
     * 2) are in defined status
     * 3) were modified on last 24 hours (only at VM)
     */
    static function getUnsentOrderIDs ()
    {
    	 $params = NbordersHelper::getParams();

         $dnCond = NbordersHelper::getSendBoth() ? " OR ms.`dn_mailed` = '0'" : "";
           
         $order_status = (array)$params->get('order_status');
         
                
        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2) //vm2 stores mdate and cdate in gmt also (only in datetime format)
        	$sql = "SELECT o.`virtuemart_order_id` FROM `#__virtuemart_orders` AS o
        		LEFT JOIN `#__nborders_mailsended ` AS ms ON (o.`virtuemart_order_id` = ms.`order_id`)
				WHERE (ms.`order_id` IS NULL OR ms.`order_mailed` = '0' $dnCond)
				AND o.`modified_on` > '".gmdate('Y-m-d H:i:s',time()-86400)."'";
        else //vm1 stores cdate and mdate in gmt
        	$sql = "SELECT o.`order_id` 
        		FROM `#__vm_orders` AS o
        		LEFT JOIN `#__nborders_mailsended ` AS ms ON (o.`order_id` = ms.`order_id`)
				WHERE (ms.`order_id` IS NULL OR ms.`order_mailed` = '0' $dnCond)
				AND o.`mdate` > UNIX_TIMESTAMP() - 86400";
        if (count($order_status))
        	$sql .= " AND o.`order_status` IN ('".implode("','",$order_status)."')";
        
        // make sure orders are sorted from oldest to newest by order ID
		if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
			$sql .= " ORDER BY o.`virtuemart_order_id` ASC";
		else
		    $sql .= " ORDER BY o.`order_id` ASC";
		    
        
        $db = JFactory::getDBO();
        $db->setQuery($sql);
        return $db->loadColumn();
    }
    
    
    /**
     * Get order IDS created after specified time
     * 
     * @param int $time
     */
    static function getOrdersFromTime($time)
    {
    	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
    		$sql = 'SELECT `virtuemart_order_id` FROM `#__virtuemart_orders` WHERE `created_on > \''.gmdate('Y-m-d H:i:s',$time)."'";
    	else
    		$sql = 'SELECT `order_id` FROM `#__vm_orders` WHERE `o`.`cdate`>'.$time;
        $db = JFactory::getDBO();
        $db->setQuery($sql);
        return $db->loadColumn();
    }
    
    static function getCoupon($code)
    {
		$db = JFactory::getDBO();
		if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
			$db->setQuery('SELECT * FROM `#__virtuemart_coupons` WHERE `coupon_code`='.$db->Quote($code));
		else
        	$db->setQuery('SELECT * FROM `#__vm_coupons` WHERE `coupon_code`='.$db->Quote($code));
        return $db->loadObject();
    }
    
   	
    
	/**
	 * Gets specific SQL string to config file.
	 * 
	 * @param string $field		config field name
	 * @param string $defalt	default sql query
	 */
    static function getConfigSQL($field,$defalt=null)
    {
    	switch ($field){
    	
    		case 'order_status':
    			
		    	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
		    		return 'SELECT order_status_code AS value, order_status_name AS title FROM `#__virtuemart_orderstates`';
		    	else
		    		return 'SELECT order_status_code AS value, order_status_name AS title FROM `#__vm_order_status`';
		    	
		    					break;
				
			
			case 'extra_field1':
			case 'extra_field2':
			case 'extra_field3':
			case 'extra_field4':
						
		    	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
		    		return 'SELECT \'\' AS fieldid,\'\' AS name UNION SELECT virtuemart_userfield_id AS fieldid, name FROM #__virtuemart_userfields';
		    	else
		    		return 'SELECT \'\' AS fieldid,\'\' AS name UNION SELECT fieldid, name FROM #__vm_userfield`';
	    		break;
	    	
			case 'default_vendor':
		    	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
		    		return 'SELECT `virtuemart_vendor_id` AS `vendor_id`, `vendor_name` FROM `#__virtuemart_vendors` ORDER BY `vendor_name` ASC';
		    	else
		    		return 'SELECT `vendor_id`, `vendor_name` FROM `#__vm_vendor` ORDER BY `vendor_name` ASC';
	    		break;
	    	
			case 'default_currency':
		    	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
		    		return 'SELECT `virtuemart_currency_id` AS id, CONCAT(`currency_name`,\', \',`currency_code_3`) AS `name` FROM `#__virtuemart_currencies` ORDER BY `virtuemart_currency_id` ASC';
		    	else
		    		return 'SELECT `currency_code` AS id, CONCAT(`currency_name`,\', \',`currency_code`) AS `name` FROM `#__vm_currency` ORDER BY `currency_id` ASC';
	    		break;
	    		
	    	case 'default_status':
	    		if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
		        	return 'SELECT order_status_code AS \'id\', order_status_name AS \'name\' FROM `#__virtuemart_orderstates`';
		        else
		        	return 'SELECT order_status_code AS \'id\', order_status_name AS \'name\' FROM `#__vm_order_status`';
	    		break;
	    	
	    		
			default:
				return $defalt;
    	}
    }

	
	/**
	 * Get VM language localised table name prior to current language.
	 * @param string $tableName
	 * @uses current JLanguage setting
	 * @return string	table name with language suffix, with db #__ prefix
	 */
	static function getVm2LanguageTable($tableName)
	{

		static $langTables;
 
		$db = JFactory::getDBO();
		$tableName = str_replace('#__',$db->getPrefix(),$tableName);
	
		if (!isset($langTables[$tableName])){
			$db = JFactory::getDBO();
			$db->setQuery('SHOW TABLES LIKE \''.$db->escape($tableName).'_%\'');
			$langTables[$tableName] = $db->loadColumn();
		}
         
     
		if (!count($langTables[$tableName]))
			return false;
		
		$lang = JFactory::getLanguage();
             
		$tag = strtolower(str_replace('-','_',$lang->get('tag')));
		$langTable = $tableName.'_'.$tag;
 

		if (!in_array($langTable,$langTables[$tableName])){ //no table for current language
			$langTable = $tableName.'_en_gb';
                 
//			if (!in_array($langTable,$langTables[$tableName])) //try en_gb                           
//				$langTable = reset($langTables[$tableName]);	 //else use first table
		}

		return preg_replace('#^'.preg_quote($db->getPrefix()).'#i','#__',$langTable);	
	}

    /**
     * Get VM language string
     * @param string $string
     * @param string $module optional for VM1
     */
    static function getVMTranslation($string,$module='common')
    {
    	$language =  JFactory::getLanguage();
    	$currTag = $language->get('tag');
    	
    	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
    	{
    		static $langLoaded;
    		
    		if (empty($langLoaded)){ //load english as base
    			
    			$language->load('com_virtuemart',JPATH_SITE,'en-GB');
    			$language->load('com_virtuemart',JPATH_ADMINISTRATOR,'en-GB');
    			$langLoaded = 'en-GB';
    		}
    		
    		if ($langLoaded!=$currTag){
    			
    			$language->load('com_virtuemart',JPATH_SITE,$currTag,true);
    			$language->load('com_virtuemart',JPATH_ADMINISTRATOR,$currTag,true);
    	    	$langLoaded = $currTag;
    		}

    		return JText::_($string);
    	}
    	else
    	{   		
			global $mosConfig_lang, $VM_LANG, $modulename;
			
			static $vm1Language;
			
			if (empty($vm1Language[$currTag]))
			{
				$backwardLang = strtolower( $language->getBackwardLang() );
				$loadLang = $backwardLang;
				$langPath = JPATH_ADMINISTRATOR. '/components/com_virtuemart/languages/'.$module.'/';
				
				//try alternative names, if not, english
				if (!file_exists( $langPath.$loadLang.'.php' ))
					$loadLang = $backwardLang.'iso';
				if (!file_exists( $langPath.$loadLang.'.php' ))
					$loadLang = $backwardLang.'1250';
				if (!file_exists( $langPath.$loadLang.'.php' ))
					$loadLang = 'english';

				$mosConfig_lang = $loadLang;
				$GLOBALS['mosConfig_lang'] = $mosConfig_lang;
					
				NbordersHelper::importVMFile('classes/language.class.php'); 
				$vm1Language[$currTag] = new vmLanguage();;
				$GLOBALS['VM_LANG'] = &$vm1Language[$currTag];				
				$vm1Language[$currTag]->_debug = false; //disable VM debug if translation not found
				
				//DONT! use vm_lang->load() function, because it use require_once - in more instances doesnt load language again
				//instead, use own load here
				if (file_exists($langPath.strtolower($loadLang).'.php')) 
					include( $langPath.strtolower($loadLang).'.php' );
			}
			
			$modulename = $module; //global variable used in _ function

			$vmTrans = $vm1Language[$currTag]->_($string); //get translation

			if (isset($vm1Language[$currTag]->modules[$module]['CHARSET']) and function_exists('iconv')) //convert to utf 8 based on encoding STATED in language file
				$vmTrans = iconv ($vm1Language[$currTag]->modules[$module]['CHARSET'] ,'utf-8' , $vmTrans );
			

			return ($vmTrans && $vmTrans!=$string) ? $vmTrans : JText::_($string);
    	}
			
    }
    
    
}

?>
