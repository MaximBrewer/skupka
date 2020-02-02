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

class InvoiceHTML extends InvoiceHTMLParent
{
    
    var $db;
    var $images;
    var $params;
    var $currency;
    var $colsCount;
    var $fields;
    var $taxSum;
    var $subtotal = 0;
    var $deliveryNote;
    var $order;
    var $payment;
    var $vendor;
    
    //TODO: nastaveni marginu pro vsechny strany (tcpf config )
    
    //TODO: altermative classes: (shell cmd). maybe add like downloadable option?
    //http://code.google.com/p/wkhtmltopdf/

    function replaceTags($match)
    {
    	$brBefore = $match[1]; //http://www.artio.net/cz/support-forums/vm-invoice/customer-support/not-displaying-blank-lines-in-custom-address-tags
    	$tag = $match[2];
    	$colonAfter = isset($match[3]) ? $match[3] : '';

    	$replacement = false; 
    	
		$opening='';
    	$tag = trim($tag,' {}');
    	$closing='';
    	
    	//what?
    	//"You can add HTML tags to {} content and that tags will be applied only if tag is not empty.
    	// For example {shipping_address_2<br>} - <br> will be applied only if shipping_address_2 is presented"
    	if (preg_match('#^\s*(<[^}]*>)?\s*(\w+)\s*(<[^}]*>)?\s*$#Us',$tag, $matches)){ //tag content can be wrapped to html tags (commonly <br>)
	    	$opening = $matches[1];
	    	$tag = strtolower(trim($matches[2]));
			$closing = isset($matches[3]) ? $matches[3] : '';
    	}
		
    	$dateStr = $this->params->get('date_pattern') ? $this->params->get('date_pattern') : 'd.m.Y';
    	
    	//TODO: add order_create_date, modify date

    	//trigger event. if some plugin returns string, it is used as replacement no other replacement is done
        $results = $this->dispatcher->trigger('onTagBeforeReplace', array(&$tag, &$this, $this->params));
    	foreach ($results as $result)
    		if (is_string($result))
    			$replacement = $result;
        
        if ($replacement===false)
        {
    	switch ($tag) {
    		
    		
    		
    		case 'contact':
    	        if ($this->params->get('show_contact', 1)) {
    	        	
    	        	$contact = array();
    	        	if (!empty($this->vendor->company_name)) $contact[] = $this->vendor->company_name;
    	        	if (!empty($this->vendor->address_1)) $contact[] = $this->vendor->address_1;
    	        	if (!empty($this->vendor->address_2)) $contact[] = $this->vendor->address_2;
    	        	if (!empty($this->vendor->zip) || !empty($this->vendor->city)) $contact[] = @$this->vendor->zip . ' ' . @$this->vendor->city;
    	        	if (!empty($this->vendor->state_name)) $contact[] = $this->vendor->state_name;
    	        	if (!empty($this->vendor->country_name)) $contact[] = $this->vendor->country_name;
		            $replacement = VMI_NL. implode (' | ', $contact);
		        }
        		break;
        		
    		case 'logo':
    			if ($this->params->get('show_logo', 1)) {
    				$replacement = $this->getVendorImage();
    			}
    			break;
    			
    		case 'shipping_date_cpt':
    			if ($this->params->get('show_shipping_date'))
    				$replacement = $this->_('COM_NETBASEVM_EXTEND_SHIPPING_DATE');
    			break; 
    			
    		case 'shipping_date':
    			if ($this->params->get('show_shipping_date')) {
		            if ($shippingDate = InvoiceGetter::getOrderShippingDate($this->order->order_id))
		            	$replacement = $this->formatGMDate($dateStr, $shippingDate);
	            	else
	            		$replacement = $this->_('COM_NETBASEVM_EXTEND_NO_SHIPPING_DATE');
    			}
            	break;
            	
            case 'shipping_cpt':	
            	$replacement = $this->_('COM_NETBASEVM_EXTEND_HANDLING_AND_SHIPPING');
            	break;
            	
            case 'shipping_name':
            	$replacement = $this->order->shipment_name;
            	break;
            	
            case 'shipping_desc':
            	$replacement = nl2br($this->order->shipment_desc);
            	break;

    		case 'payment_type_cpt':
    			if ($this->params->get('show_payment_type'))
    				$replacement = $this->_('COM_NETBASEVM_EXTEND_PAYMENT_TYPE');
    			break; 
    			
    		case 'payment_type':
    		case 'payment_type_desc':
    			if ($this->params->get('show_payment_type')){
    				$payments = InvoiceGetter::getPayments();
    				if (isset($payments[$this->order->payment_method_id])){
    					if ($tag=='payment_type')
    						$replacement = $payments[$this->order->payment_method_id]->name;
    					elseif ($tag=='payment_type_desc')
    						$replacement = $payments[$this->order->payment_method_id]->desc;
    				}
    			}	
            	break;
            	
    		case 'variable_symbol_cpt':
    			if ($this->params->get('show_variable_symbol'))
    				$replacement = $this->_('COM_NETBASEVM_EXTEND_VARIABLE_SYMBOL');
    			break;
            	
    		case 'variable_symbol':
    			if ($this->params->get('show_variable_symbol')){
    				$field = $this->params->get('variable_symbol');
    				if ($field=='order_no')
    					$replacement = $this->order->order_id;
    				elseif ($field=='invoice_no')
    					$replacement = $this->invoice_no;
    				elseif ($field=='order_number')
    					$replacement = $this->order->order_number;
    			}
    			break;
    			
    		case 'finnish_index_number_cpt':
    			if ($this->params->get('index_number_fi'))
    				$replacement = $this->_('COM_NETBASEVM_EXTEND_INDEX_NUMBER');
    			break;
    			
    		case 'finnish_index_number':
    			if ($this->params->get('index_number_fi')) {
				    // NOTE: Index number must be atleast 4 chars! (3 + checksum), so own invoice numbering which have enougt digits  must be used
				    // Get only numbers
				    $val = preg_replace('/[^0-9]/', '', $this->invoice_no);
				    $replacement = $this->countReferenceFI($val);
    			}
    			break;
    			
    		case 'customer_number_cpt':
    			if ($this->params->get('show_customer_number'))
    				$replacement = $this->_('COM_NETBASEVM_EXTEND_CUSTOMER_NUMBER');
    			break;
    			
    		case 'customer_number':
    			if ($this->params->get('show_customer_number')) {
		        	$replacement = ($custNo = InvoiceGetter::getCustomerNumber($this->order->user_id)) ? $custNo : '' ;
    			}
    			break;
    			
    		case 'shopper_group_cpt':
    			if ($this->params->get('show_shopper_group'))
    				$replacement = $this->_('COM_NETBASEVM_EXTEND_SHOPPER_GROUP');
    			break;
    			
    		case 'shopper_group':
    			if ($this->params->get('show_shopper_group')) {
    				$group = InvoiceGetter::getShopperGroup($this->order->user_id);
		        	$replacement = $group ? $group : ''; 
    			}
    			break;

    		case 'coupon_code_cpt':
    			if ($this->order->coupon_code)
    				$replacement = $this->_('COM_NETBASEVM_EXTEND_COUPON_CODE');
    			break;	
    			
    		case 'coupon_code':
    			if ($this->order->coupon_code)
    				$replacement = $this->order->coupon_code;
    			break;	
    			
    		case 'coupon_discount':
    			$replacement = InvoiceCurrencyDisplay::getFullValue($this->order->coupon_discount, $this->currency);
    			break;	
    		case 'coupon_discount-words':
    			$replacement = $this->toWords($this->order->coupon_discount);
    			break;
    			
    		case 'order_discount':
    			$replacement = InvoiceCurrencyDisplay::getFullValue($this->order->order_discount, $this->currency);
    			break;	
    		case 'order_discount-words':
    			$replacement = $this->toWords($this->order->order_discount);
    			break;
    				
    		case 'subtotal_net':
    			$replacement = InvoiceCurrencyDisplay::getFullValue($this->subtotal_net, $this->currency);
    			break;	
    		case 'subtotal_net-words':
    			$replacement = $this->toWords($this->subtotal_net);
    			break;
    				
    		case 'subtotal_tax':
    			$replacement = InvoiceCurrencyDisplay::getFullValue($this->subtotal_tax, $this->currency);
    			break;	
    		case 'subtotal_tax-words':
    			$replacement = $this->toWords($this->subtotal_tax);
    			break;
	
    		case 'subtotal_gross':
    			$replacement = InvoiceCurrencyDisplay::getFullValue($this->subtotal_gross, $this->currency);
    			break;	
    		case 'subtotal_gross-words':
    			$replacement = $this->toWords($this->subtotal_gross);
    			break;

    		case 'total':
    			$replacement = InvoiceCurrencyDisplay::getFullValue($this->order->order_total, $this->currency);
    			break;
    		case 'total-words':
    			$replacement = $this->toWords($this->order->order_total);
    			break;
    			
    		case 'billing_address':
    		case 'shipping_address':
    			
		        // load addresses        
		        $address = array();
		        $adressType = (! $this->deliveryNote) ? $this->params->get('invoice_address') : $this->params->get('dn_address');
		        $address['BT'] = $this->address['BT'];
		        $address['ST'] = $this->address['ST'];   
		        
		        //determine if shipping address is set
		        $STAddressSet=false;
		        $fields = array('first_name','last_name','company','address_1','address_2','city','zip','state','country_name'); //fields to check in comparing
		        //$ignore = array('order_info_id','order_id','user_id','address_type','bank_account_type','address_type_name'); //fields to ignore in comparing
		        
		        //we must determine, if shipping address is set
		        //that mean if it is not empty
		        //and if it is not if is not the same as billing.
		        
		        if (isset($address['ST']->order_id) && intval($address['ST']->order_id) > 0){
			        foreach ($address['ST'] as $key => $val)
			        	if (in_array($key,$fields) AND !empty($val) AND trim($val)!='' AND $val!=$address['BT']->$key){
			        		$STAddressSet=true;
			        		break;}}
		
		        // if ST address is empty and should be shown always both
			    if (($adressType == 'both' || $adressType == 'ST') && !$STAddressSet) {
		            $address['ST'] = $address['BT'];
		            $STAddressSet = true;
		        }
		        //if show delivery only if differs from billing
		        if ($adressType == 'bothi'){
		        	$adressType = 'BT';
		        	if ($STAddressSet)
				        foreach ($address['BT'] as $key => $val)
				        	if (in_array($key,$fields) AND $val!=$address['ST']->$key AND !empty($address['ST']->$key) AND trim($address['ST']->$key)!=''){
				        		$adressType = 'both';
				        		break;}
		        }
		        
		        if ($tag=='billing_address' && ($adressType == 'BT' || $adressType == 'both' || !$STAddressSet))
		        	$replacement = (array_key_exists('BT', $address) ? $this->generateAddress($address['BT'], 'BT') : '');
		        if ($tag=='shipping_address' && (($adressType == 'ST' || $adressType == 'both') && $STAddressSet))
		        	$replacement = (array_key_exists('ST', $address) ? $this->generateAddress($address['ST'], 'ST') : '');
    			break;
        		
    		case 'customer_note_cpt':
    			if (trim($this->order->customer_note)!='' AND (($this->deliveryNote AND $this->params->get('dn_customer_note')) OR (!$this->deliveryNote AND $this->params->get('in_customer_note'))))
    				$replacement = $this->_('COM_NETBASEVM_EXTEND_CUSTOMER_NOTE');
    			break;
    			
    		case 'customer_note':
    	        if (trim($this->order->customer_note)!='' AND (($this->deliveryNote AND $this->params->get('dn_customer_note')) OR (!$this->deliveryNote AND $this->params->get('in_customer_note'))))
		        	$replacement = nl2br(strip_tags($this->order->customer_note));
    			break;
    			        		
    		case 'order_status_cpt': 
    			$replacement = $this->_('COM_NETBASEVM_EXTEND_ORDER_STATUS');
    			break;
    			
    		case 'order_status': 
    	        $replacement = $this->_($this->order->order_status_name);
    			break;
		
    		case 'order_history':
    		
    			$db = JFactory::getDBO();
				if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
					$db->setQuery('SELECT modified_on AS time, order_status_code, comments FROM #__virtuemart_order_histories WHERE virtuemart_order_id='.(int)$this->order->order_id.' ORDER BY time DESC');
				else 
					$db->setQuery('SELECT date_added AS time, order_status_code, comments FROM #__vm_order_history WHERE order_id='.(int)$this->order->order_id.' ORDER BY time DESC');
				$history = $db->loadObjectList();
				
				if ($history){
					
					$states = invoiceGetter::getOrderStates();
					$replacement = '<table>';
					foreach ($history as $action){
						
						$add = strpos($dateStr, '%')===false ? ' G:i' : ' %H:%M'; //add time iformation
						
						$replacement .= '<tr>';
						$replacement .= '<td>'.$this->formatGMDate($dateStr.$add, NbordersHelper::gmStrtotime($action->time)).'</td>';
						$replacement .= '<td>'.$this->_($states[$action->order_status_code]->name).'</td>';
						$replacement .= '<td>'.nl2br($action->comments).'</td>';
						$replacement .= '</tr>';
					}
					
					$replacement .= '</table>';
				}

    			break;
    			
    		//TODO: order weight
    		//fnc convertWeigthUnit in VM
    			
    		
    			
    		    			
    		//common: shoudld work also for booking if no error
    		
    		case 'items':
    			$br = '\s*<\s*br\s*\/?\s*>\s*';
    			$colon = '\s*:\s*';
    			$items = $this->generateItems(); //get items table
    			$replacement = preg_replace_callback('#('.$br.')?('.TAG_REGEXP.')('.$colon.')?#is',array( &$this, 'replaceTags'),$items); //call self to replace _cpt tags inside table
    			break;
    			
    		case 'invoice_date_cpt':
    			if ($this->deliveryNote ? $this->params->get('dn_date_label') : $this->params->get('invoice_date_label'))
	    			$replacement = $this->deliveryNote ? $this->_('COM_NETBASEVM_EXTEND_DATE') : $this->_('COM_NETBASEVM_EXTEND_INVOICE_DATE');
    			break;
	    
    		case 'invoice_date':
        		if (empty($this->mailsended->invoice_date)){ //for some reason now row in db, but should be. use default date from config.
        			$dateType = $this->params->get('invoice_date');
        			$invoiceDate = ($dateType == 'cdate' || $dateType == 'mdate') ? $this->order->$dateType : time();
        		}
        		else
        			$invoiceDate = $this->mailsended->invoice_date;
        			
				$replacement = $this->formatGMDate($dateStr,$invoiceDate);
    			break;
    			
    		case 'taxable_payment_date_cpt':
    			if ($this->params->get('show_taxable_payment_date'))
    				$replacement = $this->_('COM_NETBASEVM_EXTEND_TAXABLE_PAYMENT_DATE');
    			break;
    			
    		case 'maturity_date_cpt':
    			if ($this->params->get('show_maturity_date'))
    				$replacement = $this->_('COM_NETBASEVM_EXTEND_MATURITY_DATE');
    			break;
    			
    		case 'taxable_payment_date':
    		case 'maturity_date':
    			
	           $val = $this->params->get('taxable_payment_date');
	           if (empty($val) OR $val=='invdate') { //same as above. TODO. optimize
        			if ($this->mailsended->invoice_date)
        				$taxable_payment_date = $this->mailsended->invoice_date;
        			else{ //for some reason not in row in db, but should be. use default date from config.
        				$dateType = $this->params->get('invoice_date');
        				$taxable_payment_date = ($dateType == 'cdate' || $dateType == 'mdate') ? $this->order->$dateType : time();
        			}
	            }
	            else
	            	$taxable_payment_date = $this->order->$val;

	            if ($tag=='taxable_payment_date' && $this->params->get('show_taxable_payment_date'))
	            	$replacement = $this->formatGMDate($dateStr,$taxable_payment_date);
	            elseif ($tag=='maturity_date' && $this->params->get('show_maturity_date'))
	            	$replacement = $this->formatGMDate($dateStr,$taxable_payment_date+$this->params->get('maturity') * 86400);
	            	
    			break;
    			
    		case 'extra_fields':
    	        if (($this->template_type=="body" && $this->params->get('fields_pos', 0) == 1) //template body
    	        	OR
    	        	($this->template_type=="footer" && 
    	        	($this->params->get('fields_pos', 0) == 0 //every footer 
    	        		OR 
    	        	($this->params->get('fields_pos', 0) == 3 && $this->lastPage)))) //only last footer
		            $replacement = $this->generateExtraFields();
        		break;
        		
    		case 'signature': 
    			if ($this->params->get('show_signature', 1))
	    			$replacement = $this->getSignature();
    			break;

    		case 'pagination': 
    			$this->onlyOnePage=isset($this->onlyOnePage) ? $this->onlyOnePage : false;
    	        $pagination = $this->params->get('show_pagination', 2);
				if ($pagination == 2 && !$this->onlyOnePage || $pagination == 1)
    	        	$replacement = $this->sprintf('COM_NETBASEVM_EXTEND_PAGE_S_OF_S', $this->currPageInGroup, $this->totalGroups);
    			break;
        		
    		default:
    			if (isset($this->replacementFields[$tag]))
    				$replacement = $this->replacementFields[$tag];
    	} //end: switch
        } //end: if ($replacement===false)

        if ($replacement===false)
        	$replacement = ''; //not supported tag?
        
        //trigger plugin to allow additional changes
        $neighbours = array(&$brBefore, &$opening, &$closing, &$colonAfter); //allow plugin also change enclosing neighbours
        $this->dispatcher->trigger('onTagAfterReplace', array(&$tag, &$replacement, &$neighbours, &$this, $this->params));

    	if (trim($replacement)!='') //proper replacement - involve also opening and closing tags, brs and colons..
    		return $brBefore.$opening.$replacement.$closing.$colonAfter;
    	else //empty replacement - output nothing
    		return '';
    }
    
    function initializeReplacements()
    {
    	$this->replacementFields = array();
    	$this->replacementFields['invoice_number'] = $this->invoice_no;
    	
    	
 		$this->replacementFields['order_id'] = $this->order->order_id;
 		$this->replacementFields['order_number'] = $this->order->order_number;

 		$allowedAddrFields = InvoiceGetter::getOrderAddress();
 		
 		foreach ($this->address['BT'] as $key => $val) //billing tags
 			if (in_array($key,$allowedAddrFields))
 				$this->replacementFields['billing_'.$key] = $val;
 				
 		foreach ($this->address['ST'] as $key => $val) //shipping tags
 			if (in_array($key,$allowedAddrFields))
 				$this->replacementFields['shipping_'.$key] = $val;

 		foreach (InvoiceGetter::getVendor() AS $field) //vendor tags
 			if (isset($this->vendor->$field))
 				$this->replacementFields['vendor_'.$field] = $this->vendor->$field;

 		foreach ($this->order as $key => $val) //general order tags
 			if (!isset($this->replacementFields['order_'.$key]))
 				$this->replacementFields['order_'.$key] = $val;
 		
 			    $this->replacementFields['start_note'] = $this->fields->note_start;
	    $this->replacementFields['end_note'] = $this->fields->note_end;
	    
 			    
	    //items header language strings
	     $this->replacementFields['qty_cpt'] = $this->_('COM_NETBASEVM_EXTEND_QTY');
	     $this->replacementFields['sku_cpt'] = $this->_('COM_NETBASEVM_EXTEND_SKU');
	     $this->replacementFields['name_cpt'] = $this->_('COM_NETBASEVM_EXTEND_PRODUCT_NAME');
	     $this->replacementFields['price_cpt'] = $this->_('COM_NETBASEVM_EXTEND_PRICE');
	     $this->replacementFields['base_total_cpt'] = $this->_('COM_NETBASEVM_EXTEND_BASE_TOTAL');
	     $this->replacementFields['tax_rate_cpt'] = $this->_('COM_NETBASEVM_EXTEND_TAX_RATE');
	     $this->replacementFields['tax_cpt'] = $this->_('COM_NETBASEVM_EXTEND_TAX');
	     $this->replacementFields['discount_cpt'] = $this->_('COM_NETBASEVM_EXTEND_DISCOUNT');
	     $this->replacementFields['subtotal_cpt'] = $this->_('COM_NETBASEVM_EXTEND_SUBTOTAL');
	     $this->replacementFields['order_number_cpt'] = $this->_('COM_NETBASEVM_EXTEND_ORDER_NUMBER');
	     
	     $this->replacementFields['invoice_cpt'] = $this->_('COM_NETBASEVM_EXTEND_INVOICE');
	     $this->replacementFields['dn_cpt'] = $this->_('COM_NETBASEVM_EXTEND_DELIVERY_NOTE');	   
	     
	     
	     $this->replacementFields['shipping_net'] =  InvoiceCurrencyDisplay::getFullValue($this->order->order_shipping, $this->currency);
	     $this->replacementFields['shipping_tax'] = InvoiceCurrencyDisplay::getFullValue($this->order->order_shipping_tax, $this->currency);
	     $this->replacementFields['shipping_tax_rate'] =  $this->shipTaxRate.'%';
	     $this->replacementFields['shipping_gross'] =  InvoiceCurrencyDisplay::getFullValue($this->order->order_shipping + $this->order->order_shipping_tax, $this->currency);
	     
		 $this->replacementFields['payment_net'] =  InvoiceCurrencyDisplay::getFullValue($this->order->order_payment, $this->currency);
		 $this->replacementFields['payment_tax'] = InvoiceCurrencyDisplay::getFullValue($this->order->order_payment_tax, $this->currency);
		 $this->replacementFields['payment_tax_rate'] =  $this->paymentTaxRate.'%';
		 $this->replacementFields['payment_gross'] =  InvoiceCurrencyDisplay::getFullValue($this->order->order_payment + $this->order->order_payment_tax, $this->currency);
		 

	     	    $this->replacementFields['items_count'] = $this->items_count;
	    $this->replacementFields['items_sum'] = $this->items_sum;
    }
    
    function InvoiceHTML($orderID, $deliveryNote, $language)
    {
		echo $orderID;die;
    	parent::InvoiceHTML($orderID, $language);
    	
    	$this->deliveryNote = $deliveryNote;
                
        
    	$this->order = InvoiceGetter::getOrder($orderID);
    	
    	//in VM1, compute order payment to make it look like VM2
    	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM1)
    	{
    		$this->paymentTaxRate = round($this->params->get('paymenet_taxrate'),2);
    		
    		if ($this->params->get('payment_amount_source')==1) //use just order discount
    		{
    			$gross = $this->order->order_discount;
    		}
    		else //get payment based on selected method
    		{
    			if ($method = InvoiceGetter::getPaymentMethod($this->order->payment_method_id))  //(little inspired by ps_checkout get_payment_discount())
    			{
    				if ($method->payment_method_discount_is_percent==1){
    					$gross = $this->order->order_subtotal * $method->payment_method_discount / 100;
	    				if ($method->payment_method_discount_max_amount*1 && abs($gross) > $method->payment_method_discount_max_amount*1)
	    					$gross = $method->payment_method_discount_max_amount*1;
	    					
	    				if ($method->payment_method_discount_min_amount*1 && abs($gross) < $method->payment_method_discount_min_amount*1)
	    					$gross = $method->payment_method_discount_min_amount*1;
    				}
    				else
    					$gross = $method->payment_method_discount*1;
    			}  
    			else
    				$gross = 0;  			
    		}
    		
    		$gross = -$gross;
    		
    		$this->order->order_payment = $gross / (($this->paymentTaxRate/100)+1); //compute net
    		$this->order->order_payment_tax = $gross - $this->order->order_payment; //compute tax
    		
    	}
    	else //vm2
    		$this->paymentTaxRate = $this->order->order_payment ? round(NbordersHelper::guessTaxRate($this->order->order_payment+$this->order->order_payment_tax,$this->order->order_payment) * 100,2)*1 : 0;
    	
        $this->invoice_no =  NbordersHelper::getInvoiceNo($this->order->order_id);
        $this->address = array();
        $this->address['BT'] = InvoiceGetter::getOrderAddress($this->order->order_id,'BT');
		$this->address['ST'] = InvoiceGetter::getOrderAddress($this->order->order_id,'ST');      

        //get currency
        $this->currency = $this->order->order_currency;
       	
        // load vendor info
        $this->vendor = InvoiceGetter::getVendor($this->order->vendor_id);

        //get items and calculate tax summary and subtotals
        $this->taxSum = array();

        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
        	$this->calcRules = InvoiceGetter::getOrderCalcRules($orderID);
        	
        
        
        $this->loadItems(); 
        $this->initializeReplacements();
    }

    function generateExtraFields ()
    {
        $code = '';
        $code .= VMI_NL . sprintf('<table style="background-color: %s; font-size: 80%%;" cellpadding="8"><tr>', 
            $this->params->get('fields_bg', '#efefef'));

        // vendor info
        $code .= VMI_NL . '  <td>';
        
        
        $code .= $this->vendor->company_name . '<br />';
        $code .= $this->vendor->address_1 . '<br />';
        if ($this->vendor->address_2)
            $code .= $this->vendor->address_2 . '<br />';
        
        $format = $this->params->get('address_format');
        if ($format=='usa')
        	$code .=  $this->vendor->city .(!empty($this->vendor->state_2_code) && !is_numeric($this->vendor->state_2_code) ? ', '.$this->vendor->state_2_code : @$this->vendor->state_name).' '.$this->vendor->zip. '<br />';
        elseif ($format=='uk') {
        	$code .= $this->vendor->city . '<br />';
        	if (!empty($this->vendor->state_name))
        		$code .= $this->vendor->state_name . '<br />';
        	$code .= $this->vendor->zip . '<br />';
        }
        else {
        	$code .= $this->vendor->zip . ' ' . $this->vendor->city . '<br />';
        	if (isset($this->vendor->state_name))
        		$code .= $this->vendor->state_name . '<br />';
        }
        
        $code .= $this->vendor->country_name . '<br />';
        $code .= $this->vendor->url;
        
        $code .= VMI_NL . '  </td>';

        // extra fields 1
        $code .= VMI_NL . '  <td>';
        //
        if ($this->fields->show_bank_name == 1) {
            $code .= $this->_('COM_NETBASEVM_EXTEND_BANK_NAME') . ':  ' . $this->fields->bank_name . '<br />';
        }
        if ($this->fields->show_account_nr == 1) {
            $code .= $this->_('COM_NETBASEVM_EXTEND_ACCOUNT_NUMBER') . ':  ' . $this->fields->account_nr . '<br />';
        }
        if ($this->fields->show_bank_code_no == 1) {
            $code .= $this->_('COM_NETBASEVM_EXTEND_BANK_CODE') . ':  ' . $this->fields->bank_code_no . '<br />';
        }
        if ($this->fields->show_bic_swift == 1) {
            $code .= $this->_('COM_NETBASEVM_EXTEND_BIC_SWIFT') . ':  ' . $this->fields->bic_swift . '<br />';
        }
        if ($this->fields->show_iban == 1) {
            $code .= $this->_('COM_NETBASEVM_EXTEND_IBAN') . ':  ' . $this->fields->iban . '';
        }
        $code .= VMI_NL . '  </td>';
        
        // extra fields 2
        $code .= VMI_NL . '  <td>';
        //
        if ($this->fields->show_tax_number == 1) {
            $code .= $this->_('COM_NETBASEVM_EXTEND_TAX_NUMBER') . ':  ' . $this->fields->tax_number . '<br />';
        }
        if ($this->fields->show_vat_id == 1) {
            $code .= $this->_('COM_NETBASEVM_EXTEND_VAT_ID') . ':  ' . $this->fields->vat_id . '<br />';
        }
        if ($this->fields->show_registration_court == 1) {
            $code .= $this->_('COM_NETBASEVM_EXTEND_REGISTRATION_COURT') . ':  ' . $this->fields->registration_court . '<br />';
        }
        if ($this->fields->show_phone == 1) {
            $code .= $this->_('COM_NETBASEVM_EXTEND_PHONE') . ':  ' . $this->fields->phone . '<br />';
        }
        if ($this->fields->show_email == 1) {
            $code .= $this->_('COM_NETBASEVM_EXTEND_MAIL') . ':  ' . $this->fields->email;
            ;
        }
        $code .= VMI_NL . '  </td>';
        
        $code .= VMI_NL . '</tr></table>';
        
        return $code;
    }
	
    function generateAddress($address=null, $type=null)
    {
    	$format = $this->params->get('address_format');
        $lines = array();
                
        // title
        if (!$address && !$type)
        	die('Address and type not set');
        
        if ($this->params->get('address_label')) {
            if ($type == 'ST')
                $text = $this->_('COM_NETBASEVM_EXTEND_SHIPPING_ADDRESS');
            if ($type == 'BT')
                $text = $this->_('COM_NETBASEVM_EXTEND_BILLING_ADDRESS');
            $lines[] = '<strong>' . $text . ':</strong>';
        }
		if ($format=='german'){ 
			if ($address->title)
				$lines[] = $address->title;
	        $lines[] =  $address->first_name . ' ' .  ($address->middle_name ? $address->middle_name.' ': '') . $address->last_name;
	        if ($address->company)
           		$lines[] = $address->company;
		}
		else {
	        if ($address->company)
	            $lines[] = $address->company;
	        $lines[] = ($address->title ? $address->title.' ': '') . $address->first_name . ' ' .  ($address->middle_name ? $address->middle_name.' ': '') . $address->last_name;
		}
		
        $lines[] = $address->address_1;
        if ($address->address_2)
            $lines[] = $address->address_2;

		if ($format=='usa')
			$lines[] =  $address->city.($address->state_2_code ? ', '.$address->state_2_code : '').' '.$address->zip;
		elseif ($format=='uk') {
			$lines[] = $address->city;
			if (!empty($address->state) && $address->state!='-')
				$lines[] = $address->state;
			$lines[] = $address->zip;
		}
		else {
	        $lines[] = $address->zip . '  ' . $address->city;
	        if ($address->state && $address->state!='-')
	            $lines[] = $address->state;
		}
	    if ($address->country_name)
	   		$lines[] = $address->country_name;

	    // extralines 1 - 4
	    foreach (range(1,4) as $i)
		     if ($this->params->get('extra_field'.$i)) {
	            $field = InvoiceGetter::getVMExtraField($this->params->get('extra_field'.$i));
	            $label = $this->params->get('show_extra_field_label') ? InvoiceGetter::getVMTranslation($field['title']) . ': ' : '';
	            $name = $field['name'];
	            if (isset($address->$name) && $address->$name)
	                $lines[] = $label . str_replace('|*|',', ',$address->$name);
	        }

        
        
        return VMI_NL . implode('<br />' . VMI_NL, array_map('stripslashes', $lines));
    }
	
    
    function getVendorImage ()
    {
    	$filename = InvoiceGetter::getVendorImage($this->order->vendor_id);
    	$path = JPATH_SITE.'/'.$filename;
    	$uri = $filename;
    	
        if ($filename && $uri && file_exists($path)) {
           
            $logoWidth = $this->params->get('logo_width','');
            
            if (!empty($logoWidth) && is_readable($path)) {
            	list ($width, $height, $type, $attr) = getimagesize($path);
                $height = round(($logoWidth/$width) * $height);
                $width = $logoWidth;
                return '<img src="' . $uri . '" style="width:'.($width*$this->scaleCmToPDF).'pt;height:'.($height*$this->scaleCmToPDF).'pt;" width="' . ($width*$this->scaleCmToPDF) . '" height="' . ($height*$this->scaleCmToPDF) . '" />';

                //return '<img src="' . $uri . '" width="' . ($width*$this->scaleCmToPDF) . '" height="' . ($height*$this->scaleCmToPDF) . '" />';
 
            }
            else
            	return '<img src="' . $uri . '"/>';
        }
        else
            return '';
    }
    
    
	/**
	 * Only for template help. Should stay same as replaceItemRow function below.
	 */
    static function getAvailableTags()
    {
    	$ret = array();
    	
    	$ret[0]['seq']='COM_NETBASEVM_EXTEND_SEQ_HELP';
    	$ret[0]['seq_dot']='COM_NETBASEVM_EXTEND_SEQ_DOT_HELP';
    	$ret[0]['qty']='';
    	$ret[0]['qty_unit']='';
    	$ret[0]['sku']='';
        $ret[0]['item_image']='';
        $ret[0]['item_image_url']='';
    	$ret[0]['name']='';
    	$ret[0]['attributes']='';
    	$ret[0]['price']='COM_NETBASEVM_EXTEND_PRICE_HELP';
    	$ret[0]['price_notax']='COM_NETBASEVM_EXTEND_PRICE_NOTAX_HELP';
    	$ret[0]['price_withtax']='COM_NETBASEVM_EXTEND_PRICE_WITHTAX_HELP';
    	$ret[0]['tax_rate']='COM_NETBASEVM_EXTEND_TAX_RATE_HELP';
    	$ret[0]['tax_price']='COM_NETBASEVM_EXTEND_TAX_PRICE_HELP';
    	$ret[0]['tax_price_item']='COM_NETBASEVM_EXTEND_TAX_PRICE_ITEM_HELP';
    	$ret[0]['discount']='COM_NETBASEVM_EXTEND_DISCOUNT_HELP';
    	$ret[0]['discount_item']='COM_NETBASEVM_EXTEND_DISCOUNT_ITEM_HELP';
    	$ret[0]['subtotal']='COM_NETBASEVM_EXTEND_SUBTOTAL_HELP';
    	$ret[0]['subtotal_item']='COM_NETBASEVM_EXTEND_SUBTOTAL_ITEM_HELP';
    	
    	$ret[0]['product_s_desc']='';
    	$ret[0]['product_desc']='';
    	$ret[0]['product_weight']='';
    	$ret[0]['product_weight_unit']='';
    	
    	$ret[1]['qty_cpt']=JText::_('COM_NETBASEVM_EXTEND_QTY'); //captions
    	$ret[1]['sku_cpt']=JText::_('COM_NETBASEVM_EXTEND_SKU');
    	$ret[1]['name_cpt']=JText::_('COM_NETBASEVM_EXTEND_PRODUCT_NAME');
    	$ret[1]['price_cpt']=JText::_('COM_NETBASEVM_EXTEND_PRICE');
    	$ret[1]['base_total_cpt']=JText::_('COM_NETBASEVM_EXTEND_BASE_TOTAL');
    	$ret[1]['tax_rate_cpt']=JText::_('COM_NETBASEVM_EXTEND_TAX_RATE');
    	$ret[1]['tax_cpt']=JText::_('COM_NETBASEVM_EXTEND_TAX');
    	$ret[1]['discount_cpt']=JText::_('COM_NETBASEVM_EXTEND_DISCOUNT');
    	$ret[1]['subtotal_cpt']=JText::_('COM_NETBASEVM_EXTEND_SUBTOTAL');
    	
    	
    	    	
    	return $ret;
    }
    
    function replaceItemRow($match){
    	
    	$item = $this->currentItem;    	
    	$replacement = false;
    	$tag = strtolower(trim($match[0],' {}'));
    	
    	//trigger event. if some plugin returns string, it is used as replacement and no other replacement is done
        $results = $this->dispatcher->trigger('onTagItemBeforeReplace', array(&$tag, &$item, &$this, $this->params));
    	foreach ($results as $result)
    		if (is_string($result))
    			$replacement = $result;
    	
    	if ($replacement===false)
	   	switch ($tag) {
	   		
	   		case 'seq':
	   			$replacement = $this->seqNo;
	   			break;
	   			
	   		case 'seq_dot':
	   			$replacement = $this->seqNo.'.';
	   			break;
	   				
	    	
	    	case 'qty':
	    		$replacement = $item->product_quantity  * 1;
	    		break;
	    		
	    	case 'qty_unit':
	    		$replacement = ($this->deliveryNote && $this->params->get('show_quantity_unit_dn') || !$this->deliveryNote && $this->params->get('show_quantity_unit')) ? ' '.$this->_('COM_NETBASEVM_EXTEND_PCS') : '';
	    		break;
	        
	    	case 'sku':
	    		if (($this->params->get('show_sku') && ! $this->deliveryNote) || ($this->params->get('show_sku_dn') && $this->deliveryNote))
                	$replacement = $item->order_item_sku;
                else
	    			$replacement = '-remove-';
	        	break;
	        
            case 'item_image':    
				if ($this->params->get('show_product_image_in_invoice') AND $item->item_image) {  
                    $width = (int)$this->params->get('invoice_image_width');
                    $height = (int)$this->params->get('invoice_image_height');    
                	$replacement = '<img src="'.$item->item_image . '" style="width:'.$width.'px;height:'.$height.'px;" width="' . $width. '" height="' .$height. '" />';
                }
                break;     

            case 'item_image_url':
            	$replacement = $item->item_image;
            	break;
                        
	    	case 'name':
	    		$replacement = $item->order_item_name;
	        	break;
	        	
	    	case 'attributes':
	    		if ($this->params->get('show_attributes')==1){
	    			
	    			$decoded = json_decode($item->product_attribute, true); 

	    			if ($decoded==null) //string attributes (VM1)
	    				$replacement =  preg_replace('#(\s*<\s*p\s[^>]*>\s*|\s*<\s*\/\s*p\s*>\s*|<\s*br\s*\/?\s*>\s*$|^\s*<\s*br\s*\/?\s*>)#i','',$item->product_attribute); //remove all <p>s and enclosing <br>s
	    			else{ //JSON atrributes (VM2)

	    				$attributes = array();
	    				foreach ($decoded as $key => $val){
	    					if (is_array($val)){
	    						foreach ($val as $key2 => $val2){

	    							if (is_array($val2)){
	    								foreach ($val2 as $key3 => $val3){

	    									//support for Custom Field Dropbox Plugin (or more) - replace title from custom field title
	    									if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2 AND !is_numeric($key3)){
	    										$db = JFactory::getDBO();
	    										$db->setQuery('SELECT custom_param FROM #__virtuemart_product_customfields WHERE custom_value = '.$db->Quote($key2).' AND virtuemart_customfield_id = '.(int)$key);
	    										
	    										if (($param = $db->loadResult()) AND ($params = json_decode($param, true)))
	    											foreach ($params as $key4 => $val4)
	    												if (substr($key4, -5)=='_name' AND is_string($val4))
	    													$key3 = $val4;
	    									}
	    									if (!empty($key3) && !empty($val3))
												$attributes[] = $this->_($key3, 'com_virtuemart').': '.$this->_($val3, 'com_virtuemart');
	    								}
	    							}
	    							elseif (!empty($key2) && !empty($val2))
										$attributes[] = $this->_($key2, 'com_virtuemart').': '.$this->_($val2, 'com_virtuemart');
	    						}
	    					}
	    					else{
		    					if (preg_match('#^\s*<\s*span.*>(.*)<\s*\\/\s*span\s*>\s*<\s*span.*>(.*)<\s*\\/\s*span\s*>\s*$#iU', (string)$val, $matches)) //two spans
		    					//if (preg_match('#^\s*<\s*span\s+class\s*=\s*\\"costumTitle\\"\s*>(.*)<\s*\\?\s*/\s*span\s*>\s*<\s*span\s+class\s*=\s*\\"costumValue\\"\s*>(.*)<\s*\\?\s*/\s*span\s*>\s*$#iU', $val, $matches))
		    						$attributes[] = $matches[1].': '.$matches[2];
		    					elseif (($val = trim((string)$val)) AND !preg_match('#^\d+$#',$val)) //http://www.artio.net/support-forums/vm-invoice/customer-support/custom-plugin-attribute-title ?
		    						$attributes[] = $this->_((string)$val, 'com_virtuemart'); //only when not empty and not integer
	    					}
	    				}
	    				$replacement = implode("<br>\n", $attributes);
	    			}
	    		}
	    		else 
	    			$replacement = '-remove-';
	        	break;
	        
	    	case 'price': //1 item price without tax
				$replacement = InvoiceCurrencyDisplay::getFullValue($item->product_item_price, $this->currency, $this->showCurrency);
	        	break;
	        
	    	case 'price_notax': //quantity * price without tax
	    		if ($this->params->get('show_price_notax'))
	    			$replacement =  InvoiceCurrencyDisplay::getFullValue($item->item_price_notax, $this->currency, $this->showCurrency);
	    		else 
	    			$replacement = '-remove-';
	        	break;
	        
	    	case 'price_withtax': //1 item with tax
				$replacement = InvoiceCurrencyDisplay::getFullValue($item->product_item_price+($item->item_tax_amount/$item->product_quantity), $this->currency, $this->showCurrency);
	        	break;
	        	
	    	case 'tax_rate':
	    		if ($this->params->get('show_tax_rate'))
	    			$replacement =  $item->tax_item.'%';
	    		else 
	    			$replacement = '-remove-';
	        	break;

	    	case 'tax_price':
	    		if ($this->params->get('show_tax_price'))
	    			$replacement =  InvoiceCurrencyDisplay::getFullValue($item->item_tax_amount, $this->currency, $this->showCurrency);
	    		else 
	    			$replacement = '-remove-';
	        	break;
	        	
	    	case 'tax_price_item':
	    			$replacement =  InvoiceCurrencyDisplay::getFullValue($item->item_tax_amount/$item->product_quantity, $this->currency, $this->showCurrency);
	        	break;
	        	
	    	case 'discount':
	    		if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2 && (($this->params->get('show_discount')==2 AND $this->isItemsDiscount) OR $this->params->get('show_discount')==1))
	    			$replacement =  InvoiceCurrencyDisplay::getFullValue($item->discount, $this->currency, $this->showCurrency);
	    		else
	    			$replacement = '-remove-';
	        	break;
	        	
	    	case 'discount_item':
	    		if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2 && $item->discount)
	    			$replacement =  InvoiceCurrencyDisplay::getFullValue($item->discount/$item->product_quantity, $this->currency, $this->showCurrency);
	        	break;
	        	
	    	case 'subtotal_item':
	    	case 'subtotal':
	    		if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2 AND !$this->params->get('item_subtotal_with_discount', 1)) //no discount!
	    			$subtotal = $item->subtotal - $item->discount;
	    		else
	    			$subtotal = $item->subtotal;
	    		if ($tag=='subtotal_item')
	    			$replacement =  InvoiceCurrencyDisplay::getFullValue($subtotal/$item->product_quantity, $this->currency, $this->showCurrency);
	    		else
	    			$replacement =  InvoiceCurrencyDisplay::getFullValue($subtotal, $this->currency);
	        	break;

	    	case 'product_s_desc':
	    		$replacement =  preg_replace('#(\s*<\s*p\s*[^>]*>\s*|\s*<\s*\/\s*p\s*>\s*|<\s*br\s*\/?\s*>\s*$|^\s*<\s*br\s*\/?\s*>)#i','',$item->product_s_desc); //remove all <p>s and enclosing <br>s
	        	break;
	    	case 'product_desc':
	    		$replacement =  preg_replace('#(\s*<\s*p\s*[^>]*>\s*|\s*<\s*\/\s*p\s*>\s*|<\s*br\s*\/?\s*>\s*$|^\s*<\s*br\s*\/?\s*>)#i','',$item->product_desc); //remove all <p>s and enclosing <br>s
	        	break;
	    	case 'product_weight':
	    		$replacement =  $item->product_weight*1;
	        	break;
	    	case 'product_weight_unit':
	    		$replacement =  $item->product_weight_uom;
	        	break;
	        	    	
	        
	        	
	        	   	}
    	
	   	$this->dispatcher->trigger('onTagItemAfterReplace', array(&$tag, &$replacement, &$item, &$this, $this->params));
	   	
    	return $replacement;
    }
        
    /**
     * Loads items and computes tax summary. Also loads shipping and its tax rates.
     */
    function loadItems()
    {
    	$this->items_count = 0;
    	$this->items_sum = 0;
    	
    	
    	$decimals = InvoiceCurrencyDisplay::getDecimals($this->currency);
    	$sumRounded = $this->params->get('tax_sums_rounded',1) && ($decimals!==false); //whether calculate tax summary with already rounded values (default yes)
    	
        // load items
        $this->items = InvoiceGetter::getOrderItems($this->order->order_id, null, $this->params->get('items_ordering'));
        $this->subtotal_net = 0;
        $this->subtotal_tax = 0;
        $this->subtotal_gross = 0;
        $this->isItemsDiscount = false;
		$this->subtotal_discount = 0;

        foreach ($this->items as &$orderItem) {
        	
        	$q = $orderItem->product_quantity;
        	
        	$this->items_count++;
        	$this->items_sum+= $q;
        	
        	// calculates for footer and replace fnc
			$guessedRate = NbordersHelper::guessTaxRate($orderItem->product_price_with_tax,$orderItem->product_item_price);
			$orderItem->tax_item = round($guessedRate * 100,2)*1;
			$orderItem->item_price_notax = $q * $orderItem->product_item_price;
			if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2){ //vm2
				$orderItem->item_tax_amount = $orderItem->product_tax*$q;
				$orderItem->item_price_tax = $orderItem->product_price_with_tax * $q;
				$orderItem->subtotal = $orderItem->product_subtotal_with_tax*$q; //should be also with discount
				$orderItem->discount = $orderItem->product_price_discount*$q;
				if ($orderItem->discount)
					$this->isItemsDiscount = true;
			}
			else{ //vm1
				if ($this->params->get('product_price_calculation','vm')=='tax'){ //compute from base x tax
					$orderItem->item_tax_amount = $orderItem->item_price_notax * $guessedRate;
					$orderItem->item_price_tax = $orderItem->item_price_notax + $orderItem->item_tax_amount;}
				else { //take from VM
					$orderItem->item_tax_amount = $q * ($orderItem->product_price_with_tax - $orderItem->product_item_price);
					$orderItem->item_price_tax = $q * $orderItem->product_price_with_tax;}
				$orderItem->subtotal = $orderItem->item_price_tax;
				$orderItem->discount = null;
			}
			if (! isset($this->taxSum[(string) $orderItem->tax_item])) {
			    $this->taxSum[(string) $orderItem->tax_item]['notax'] = 0;
			    $this->taxSum[(string) $orderItem->tax_item]['taxa'] = 0;
			    $this->taxSum[(string) $orderItem->tax_item]['total'] = 0;
			}
			
			$this->subtotal_discount+=$orderItem->discount;
			
			$this->taxSum[(string) $orderItem->tax_item]['notax'] += $sumRounded ? round($orderItem->item_price_notax, $decimals) : $orderItem->item_price_notax;
			$this->taxSum[(string) $orderItem->tax_item]['taxa'] +=  $sumRounded ? round($orderItem->item_tax_amount, $decimals) : $orderItem->item_tax_amount;
			
			
			$priceTax = $sumRounded ? round($orderItem->item_price_tax, $decimals) : $orderItem->item_price_tax;
			if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2 && $this->params->get('take_discount_into_summary',0)==1) //deduct discount from at summary GROSS
				$priceTax += $sumRounded ? round($orderItem->discount, $decimals) : $orderItem->discount;
				
			$this->taxSum[(string) $orderItem->tax_item]['total'] += $priceTax;
			
	        $this->subtotal_net += $orderItem->item_price_notax;
	        $this->subtotal_tax += $orderItem->item_tax_amount;
			$this->subtotal_gross += $orderItem->item_price_tax;
        }

        //compute shipping
		if ($this->order->order_shipping || $this->order->order_shipping_tax){
			
			// tax rate (guessed)
			$this->shipTaxRate=  round(NbordersHelper::guessTaxRate($this->order->order_shipping+$this->order->order_shipping_tax,$this->order->order_shipping) * 100,2)*1;
				
			//add shipping to tax summary
	        if (! isset($this->taxSum[(string) $this->shipTaxRate])) {
	            $this->taxSum[(string) $this->shipTaxRate]['notax'] = 0;
	            $this->taxSum[(string) $this->shipTaxRate]['taxa'] = 0;
	            $this->taxSum[(string) $this->shipTaxRate]['total'] = 0;
	        }
	        $this->taxSum[(string) $this->shipTaxRate]['notax'] += $sumRounded ? round($this->order->order_shipping, $decimals) : $this->order->order_shipping;
	        $this->taxSum[(string) $this->shipTaxRate]['taxa'] += $sumRounded ? round($this->order->order_shipping_tax, $decimals) : $this->order->order_shipping_tax;
	        $this->taxSum[(string) $this->shipTaxRate]['total'] += $sumRounded ? round($this->order->order_shipping + $this->order->order_shipping_tax, $decimals) : ($this->order->order_shipping + $this->order->order_shipping_tax);
	        
	        $this->subtotal_net += $this->order->order_shipping;
	        $this->subtotal_tax += $this->order->order_shipping_tax;
	        $this->subtotal_gross += $this->order->order_shipping + $this->order->order_shipping_tax;
		}
		
        //compute payment
		if (($this->order->order_payment!=0 || $this->order->order_payment_tax!=0) && 
			(COM_NETBASEVM_EXTEND_ORDERS_ISVM2 || (COM_NETBASEVM_EXTEND_ORDERS_ISVM1 && $this->params->get('show_payment_row')!=2))){ //in vm1 only if selected to show in separate rpw

			//add payment to tax summary
	        if (! isset($this->taxSum[(string) $this->paymentTaxRate])) {
	            $this->taxSum[(string) $this->paymentTaxRate]['notax'] = 0;
	            $this->taxSum[(string) $this->paymentTaxRate]['taxa'] = 0;
	            $this->taxSum[(string) $this->paymentTaxRate]['total'] = 0;
	        }
	        $this->taxSum[(string) $this->paymentTaxRate]['notax'] += $sumRounded ? round($this->order->order_payment, $decimals) : $this->order->order_payment;
	        $this->taxSum[(string) $this->paymentTaxRate]['taxa'] += $sumRounded ? round($this->order->order_payment_tax, $decimals) : $this->order->order_payment_tax;
	        $this->taxSum[(string) $this->paymentTaxRate]['total'] += $sumRounded ? round($this->order->order_payment + $this->order->order_payment_tax, $decimals) : ($this->order->order_payment + $this->order->order_payment_tax);
	        
	        $this->subtotal_net += $this->order->order_payment;
	        $this->subtotal_tax += $this->order->order_payment_tax;
	        $this->subtotal_gross += $this->order->order_payment + $this->order->order_payment_tax;
		}

        //unset empty tax summary (like free shipping)
        foreach ($this->taxSum as $taxRate => $taxSum)
			if (!$taxSum['notax'] && !$taxSum['taxa'] && !$taxSum['total']){
		       unset($this->taxSum[$taxRate]);
		       continue;}
		       
	    if (false) { //take from vm databse (but prices can be misfitting http://www.artio.net/cz/support-forums/vm-invoice/customer-support/some-wrong-shown-things-tax-title-country)

	        $this->subtotal_net = $this->order->order_subtotal;

	        $this->subtotal_tax = $this->order->order_tax;
	        	
	        //add shipping value to subtotal
	        $this->subtotal_net += $this->order->order_shipping;
		    $this->subtotal_tax += $this->order->order_shipping_tax;
		    if (!(COM_NETBASEVM_EXTEND_ORDERS_ISVM1 && $this->params->get('show_payment_row')==2)) { //add payment value to subtotals
		       $this->subtotal_net += $this->order->order_payment;
		       $this->subtotal_tax += $this->order->order_payment_tax;
		    }
	    }
	    
	    //http://www.artio.net/cz/support-forums/vm-invoice/customer-support/total-mismatch
	    //now this is tricky: when order total misfits calculated order (sub)total
	    //change it to original order (sub)total (total minus discounts), else prices on invoice wont fit
	    //if showing tax summary, it must have only one tax rate! (because else we canot know what subtotal is misfitting)
		//only in VM1, becaue VM2 stores prices with higher precision, so they shouldnt misfit
	    if (COM_NETBASEVM_EXTEND_ORDERS_ISVM1 && (count($this->taxSum)==1 || !$this->params->get('show_tax_summary'))){

	    	$computedSubTotalGrossRounded = InvoiceCurrencyDisplay::getFullValue($this->subtotal_gross, $this->currency, false);
	    	$orderSubtotalGross = $this->order->order_total - $this->order->coupon_discount;
	    	
	    	//now this is very sophisticated.
	    	if ($this->params->get('show_payment_row')==2) //add (decuduct) order disocunt only if not shpwing payment row above
	    		$orderSubtotalGross += $this->order->order_discount; //+!!!!!!!!!!!!!!
	    	
	    	$orderSubtotalGrossRounded = InvoiceCurrencyDisplay::getFullValue($orderSubtotalGross, $this->currency, false);

	    	if ($orderSubtotalGrossRounded!=$computedSubTotalGrossRounded){ //computed subtotal and order subtotal misfits
	    		$this->subtotal_gross = $orderSubtotalGross;
	    		if (count($this->taxSum)==1){
	    			reset($this->taxSum);
	    			$this->taxSum[key($this->taxSum)]['total'] = $sumRounded ? round($orderSubtotalGross, $decimals) : $orderSubtotalGross;
	    		}
	    	}
	    }
	    
	    if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2 && $this->params->get('take_discount_into_summary',0)==1) //deduct discount from 
	    	$this->subtotal_gross -= $this->order->order_discount;
	    
		
		    }
    
    function generateItems()
    {
    	$code = '';
    	
    	//get items template
	    $dn = $this->deliveryNote ? 'dn_' : '';
	    $db = JFactory::getDBO();
	    $db->setQuery('SELECT `template_'.strtolower($dn).'items` FROM `#__vminvoice_config`');
	    if (count($template = explode('_TEMPLATE_ITEMS_SEPARATOR_',$db->loadResult()))!=2) 
	    	die('Not defined items block template for '.(!$this->deliveryNote ? 'invoice':'delivery note'));

        if (!is_numeric(NbordersHelper::getNumCols($template[0])))
        	die('Not proper header template for items block ');
	
        foreach ($template as &$templatePart)	//remove table tags to get only row
        	$templatePart = preg_replace('#<\s*\/?\s*(table|tbody|thead)[^>]*>#is','',$templatePart);
        	
        //non-empty first cell - add 1% width cell on start with empty <span> inside. it is because TCPDF bug putting content on next page. (http://sourceforge.net/projects/tcpdf/forums/forum/435311/topic/5096409)
        $columnsItem = NbordersHelper::getColumns($template[1]);
        if (trim(strip_tags($columnsItem[0][2]))!='') {
        	$td = '<td width="1%"><span></span></td>';
        	$template[0] = NbordersHelper::addColumn($template[0],0, $td);
        	$template[1] = NbordersHelper::addColumn($template[1],0, $td);
        }
        	
        // generate item lines
        $itemLines = array();
        $i = 0;
        
                
        foreach ($this->items as $orderItem) {

        	$this->seqNo = ++$i;
			$this->currentItem = $orderItem; //to pass item obj to function

			$itemLine = preg_replace_callback("#\{[\w ]+\}#is",array( &$this, 'replaceItemRow'),$template[1]); //replace tags

			if (!isset($colsToDelete)){ //determine which columns will be removed
				$colsToDelete = array();
				$columns = NbordersHelper::getColumns($itemLine);

				foreach ($columns as $key => $column){

					$colspan = 1; 
					if (preg_match('#colspan\s*=\s*["\']?\s*(\d+)#i',$column[1],$tdColspan))
						$colspan = $tdColspan[1];
						
					if (preg_match('#^(\s*-remove-\s*)+$#is',strip_tags($column[2]))){ //if all content of column was removed..., remove column 
						for ($j=0;$j<$colspan;$j++) //if column has rowspan, mark also "non existing" columns
							$colsToDelete[]=$key+$j;
					}
				}
			}
			
			$itemLine = NbordersHelper::removeColumns($itemLine,$colsToDelete);
			$itemLine = str_replace('-remove-','',$itemLine); //remove "to-remove" marks
			$itemLines[] = $itemLine;
        }
        
	    $templateLine = NbordersHelper::removeColumns($template[1],$colsToDelete);
	    $colsNo = NbordersHelper::getNumCols($templateLine);
	        
        
        //VM1 pocitani = subtotal + tax  + shipment + shipment tax + payment + payment tax - coupon discount - order discount (jeste se jinak jmenujou)
        //VM2 pocitani = subtotal + tax  + shipment + shipment tax + payment + payment tax + coupon discount + order discount
        //VM2 order_subtotal = souscet cen bez dane a modifikatoru.
        
	    //determine last "subtotal" tag, sometimes there can be subtotal, sometimes subtotal_item
	    $subtotalTag = strpos($templateLine, '{subtotal}')!==false ? '{subtotal}' : (strpos($templateLine, '{subtotal_item}')!==false ? '{subtotal_item}' : '{subtotal}');
	    
        if (!$this->deliveryNote)
        {	
	        //generate line for shipping
        	if ($this->params->get('show_shipping_row')==0 || ($this->params->get('show_shipping_row')==1 && $this->order->order_shipping>0)) {
        	
		        $shippingLine = $template[1];
		        $shippingLine = NbordersHelper::removeColumns($shippingLine,$colsToDelete);
			    $shippingLine = str_replace('{name}',$this->_('COM_NETBASEVM_EXTEND_HANDLING_AND_SHIPPING'),$shippingLine);
				$shippingLine = str_replace('{attributes}',$this->params->get('show_shipping_carrier') ? trim($this->order->shipment_name.': '.$this->order->shipment_desc,' :') : '',$shippingLine);
				
				if ($this->params->get('show_shipping_prices')==1 OR (!$this->params->get('show_shipping_prices') AND $this->order->order_shipping>0)) //display prices
			    {
					$shippingLine = str_replace('{price}',InvoiceCurrencyDisplay::getFullValue($this->order->order_shipping, $this->currency, $this->showCurrency),$shippingLine);
					$shippingLine = str_replace('{price_notax}',InvoiceCurrencyDisplay::getFullValue($this->order->order_shipping, $this->currency, $this->showCurrency),$shippingLine);
					$shippingLine = str_replace('{tax_rate}',$this->shipTaxRate.'%',$shippingLine);
					$shippingLine = str_replace('{tax_price}',InvoiceCurrencyDisplay::getFullValue($this->order->order_shipping_tax, $this->currency, $this->showCurrency),$shippingLine);
					$shippingLine = str_replace('{tax_price_item}',InvoiceCurrencyDisplay::getFullValue($this->order->order_shipping_tax, $this->currency, $this->showCurrency),$shippingLine);
					$shippingLine = str_replace($subtotalTag,InvoiceCurrencyDisplay::getFullValue($this->order->order_shipping + $this->order->order_shipping_tax, $this->currency),$shippingLine);
		        }
		        
		        $shippingLine = preg_replace('#\{[\w ]+\}#Us','',$shippingLine); //remove rest of tags
				$itemLines[] = $shippingLine;
        	}
        	
			//generate line for payment fee/discount (in VM2 always if not empty, in VM1 based on config)
			if ($this->params->get('show_payment_row')==0 OR
				($this->params->get('show_payment_row')==1 AND ($this->order->order_payment + $this->order->order_payment_tax)!=0))
			{
				if (($this->order->order_payment + $this->order->order_payment_tax)>=0)
					$label = $this->_('COM_NETBASEVM_EXTEND_PAYMENT_FEE');
				else
					$label = $this->_('COM_NETBASEVM_EXTEND_PAYMENT_DISCOUNT');
				
		        $paymentLine = $template[1];
		        $paymentLine = NbordersHelper::removeColumns($paymentLine,$colsToDelete);
			    $paymentLine = str_replace('{name}',$label,$paymentLine);
				
			    if ($this->params->get('show_payment_row_type')) { //show payment type in line also
			        $payments = InvoiceGetter::getPayments();
    				if (isset($payments[$this->order->payment_method_id]))
    					$paymentLine = str_replace('{attributes}',trim($payments[$this->order->payment_method_id]->name.': '.$payments[$this->order->payment_method_id]->desc,' :'),$paymentLine);
    			}
    						
				if (COM_NETBASEVM_EXTEND_ORDERS_ISVM1 && empty($this->paymentTaxRate)) //if vm1 and not entered payment tax rate, dont display zero tax and base prices, only subtotal
					$paymentLine = str_replace($subtotalTag,InvoiceCurrencyDisplay::getFullValue($this->order->order_payment + $this->order->order_payment_tax, $this->currency),$paymentLine);
				else{
					$paymentLine = str_replace('{price}',InvoiceCurrencyDisplay::getFullValue($this->order->order_payment, $this->currency, $this->showCurrency),$paymentLine);
					$paymentLine = str_replace('{price_notax}',InvoiceCurrencyDisplay::getFullValue($this->order->order_payment, $this->currency, $this->showCurrency),$paymentLine);
					$paymentLine = str_replace('{tax_rate}',$this->paymentTaxRate.'%',$paymentLine);
					$paymentLine = str_replace('{tax_price}',InvoiceCurrencyDisplay::getFullValue($this->order->order_payment_tax, $this->currency, $this->showCurrency),$paymentLine);
					$paymentLine = str_replace('{tax_price_item}',InvoiceCurrencyDisplay::getFullValue($this->order->order_payment_tax, $this->currency, $this->showCurrency),$paymentLine);
					$paymentLine = str_replace($subtotalTag,InvoiceCurrencyDisplay::getFullValue($this->order->order_payment + $this->order->order_payment_tax, $this->currency),$paymentLine);
				}
				
		        $paymentLine = preg_replace('#\{[\w ]+\}#Us','',$paymentLine); //remove rest of tags
				$itemLines[] = $paymentLine;
			}

			//generate items footer
	    
	        //determine measures of footer block
	        $relevantCols = array('{price_notax}','{tax_rate}','{tax_price}',$subtotalTag); //columns we use in footer block. footer block must contain all of them + 1 to left for labels.
	        $columns = NbordersHelper::getColumns($templateLine);
	        $leftOffset = null; //left column no of items block
	        $rightOffset = $colsNo-1; //right column no of items block
	        $subtotalOffset = 0;
	        
	        foreach ($columns as $key => $column)
	        	foreach ($relevantCols as $relevantCol)
	        		if (strpos($column[2],$relevantCol)!==false){

	        			$leftOffset = is_null($leftOffset) ? $key : min($leftOffset,$key);
	        			$rightOffset = max($rightOffset,$key);
	        			if ($relevantCol==$subtotalTag)
	        				$subtotalOffset = $key;
	        		}

	        $rightOffset++;
	       
	        //TODO: poslední nemusí být subtotral?. to bohužel není možné. Subtotal je vždy s VAT. Vyžadovalo by další hodiny supportu to doprogramovat. 
	        //(poznámka: řádek se součty bude vždy poslední v tabulce a co si zobrazí jako poslední v řádku je jejich věc)
	        
	        //univeral code for footer line with or without hr
	       	$hrCode = VMI_NL.'<tr>';
	        if ($leftOffset-1>0)
	        	$hrCode.='<td colspan="'.($leftOffset-1).'"></td>';
	        $hrCode.='<td colspan="'.($rightOffset-$leftOffset+1).'"><hr></td>';
	        if ($colsNo-$rightOffset>0)
	        	$hrCode.='<td colspan="'.($colsNo-$rightOffset).'"></td>';
	        $hrCode .= '</tr>';
	        
	       	$emptyCode = VMI_NL.'<tr>';
	        if ($leftOffset-1>0)
	        	$emptyCode.='<td colspan="'.($leftOffset-1).'"></td>';
	        $emptyCode.='<td colspan="'.($rightOffset-$leftOffset+1).'"></td>';
	        if ($colsNo-$rightOffset>0)
	        	$emptyCode.='<td colspan="'.($colsNo-$rightOffset).'"></td>';
	        $emptyCode .= '</tr>';
	        
	        //just prepare vars
	        $showingTaxPrice = (strpos($template[1], '{tax_price}')!==false AND $this->params->get('show_tax_price'));
	        $showingTaxPriceItem = (strpos($template[1], '{tax_price_item}')!==false);
	        
	       	//pre-set content of footer lines
	        $footerLines = array();
	        
	        //add tax summary
	        if ($this->params->get('show_tax_summary')) {

	        	if ($this->params->get('show_tax_summary_label')){ //add tax summary label
			        $headerCode = VMI_NL.'<tr>';
			        if ($leftOffset-1>0)
			        	$headerCode.='<td colspan="'.($leftOffset-1).'"></td>';
			        $headerCode.='<td colspan="'.($rightOffset-$leftOffset+1).'">'.$this->_('COM_NETBASEVM_EXTEND_TAX_SUMMARY').'</td>';
			        if ($colsNo-$rightOffset>0)
			        	$headerCode.='<td colspan="'.($colsNo-$rightOffset).'"></td>';
			        $headerCode .= '</tr>';
			        
			        $footerLines['tax_summary'][] = $headerCode;
		        }
		        
		        $footerLines['tax_summary'][] = $hrCode; //add HR line before
		        
		        ksort($this->taxSum); 	// sort tax groups
		        foreach ($this->taxSum as $taxRate => $taxSum) {
		        	
		            $taxLine = $template[1];
		            $taxLine = NbordersHelper::removeColumns($taxLine,$colsToDelete);
		            $taxLine = str_replace('{price_notax}',InvoiceCurrencyDisplay::getFullValue($taxSum['notax'], $this->currency, $this->showCurrency),$taxLine);
		            $taxLine = str_replace('{tax_rate}',$taxRate.'%',$taxLine);

		            if ($showingTaxPrice AND $showingTaxPriceItem) //if showing both taxes, replace only "overall	
		            	$taxLine = str_replace('{tax_price}',InvoiceCurrencyDisplay::getFullValue($taxSum['taxa'], $this->currency, $this->showCurrency),$taxLine);
		            else //else replace one or other
		            	$taxLine = str_replace(array('{tax_price}', '{tax_price_item}'),InvoiceCurrencyDisplay::getFullValue($taxSum['taxa'], $this->currency, $this->showCurrency),$taxLine);
		            
		            $taxLine = str_replace($subtotalTag,InvoiceCurrencyDisplay::getFullValue($taxSum['total'], $this->currency),$taxLine);
		        	$taxLine = preg_replace('#\{[\w ]+\}#Us','',$taxLine); //remove rest of tags
					$footerLines['tax_summary'][]= $taxLine; //add tax line
		        }
		        
		   		$footerLines['tax_summary'][] = $hrCode; //add HR line after
	        }
	        
	        $couponDiscount = $this->order->coupon_discount;	//real coupon discount (gross)
	        $couponTaxRate = $this->params->get('coupon_vat')*1;	//coupon % VAT
	        $couponNoTax = $this->order->coupon_discount/(($couponTaxRate/100)+1);	//coupon discount net
	        $couponTaxAmount = $this->order->coupon_discount - $couponNoTax;	//coupon tax amount
		    
		    $totalNet = $this->subtotal_net;	//total net price, without discount
		    $totalTax = $this->subtotal_tax;	//total tax, without coupon
		    $totalDiscount = $this->order->order_total - $this->subtotal_net - $this->subtotal_tax - $couponDiscount; //total discount, without coupon

		    if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2 && $this->calcRules)
	        	foreach ($this->calcRules as $rule)
		    		$totalDiscount -= (float)(string)$rule->calc_amount;
		    
		    //determine if using only one tax rate
		    $onlyOneTaxRate = false;
		    $taxRates = array_keys($this->taxSum);
		    foreach ($taxRates as $key => $val) //unset 0%
		    	if (empty($val))
		    		unset($taxRates[$key]);
       		if (count($taxRates)==1)  { 
				$taxRate = reset($taxRates);
				if ($couponTaxRate == $taxRate OR !$couponDiscount OR !$couponTaxRate)
					$onlyOneTaxRate = $taxRate;
        	}
        	
		    //subtotal discount jsou všechny discounty BEZ COUPONU (ptz ty jsou pod nim)
	        if ($this->params->get('show_subtotal')){ //takze subtotal je jeste bez couponu, dal se coupon odecte
	        	
	        	//subtotals are shown without coupon
	        	//(overall coupon discount is only taken to {discount} field
	        	$subtotalDiscount = $totalDiscount + $couponDiscount;
	        	
	        	$subtotalLine = $template[1];
	        	$subtotalLine = NbordersHelper::removeColumns($subtotalLine,$colsToDelete);
	        	if ($leftOffset>0)
	        		$subtotalLine = NbordersHelper::replaceColumn($subtotalLine,$leftOffset-1, '<td align="left">'.$this->_('COM_NETBASEVM_EXTEND_SUBTOTAL').':</td>');
		        $subtotalLine = str_replace('{price_notax}',InvoiceCurrencyDisplay::getFullValue($totalNet, $this->currency, $this->showCurrency),$subtotalLine);
		        
		        if ($showingTaxPrice AND $showingTaxPriceItem) //if showing both taxes, replace only "overall
		        	$subtotalLine = str_replace('{tax_price}',InvoiceCurrencyDisplay::getFullValue($this->subtotal_tax, $this->currency, $this->showCurrency),$subtotalLine);
		        else //else replace one or other
		        	$subtotalLine = str_replace(array('{tax_price}', '{tax_price_item}'),InvoiceCurrencyDisplay::getFullValue($this->subtotal_tax, $this->currency, $this->showCurrency),$subtotalLine);

		        $subtotalLine = str_replace('{discount}',InvoiceCurrencyDisplay::getFullValue($subtotalDiscount, $this->currency, $this->showCurrency),$subtotalLine);
		        $subtotalLine = str_replace($subtotalTag,InvoiceCurrencyDisplay::getFullValue($this->subtotal_gross, $this->currency),$subtotalLine);
		        
		        $subtotalLine = preg_replace('#\{[\w ]+\}#Us','',$subtotalLine); //remove rest of tags
				$footerLines['subtotal'][] = $subtotalLine;
	        }

	        //now gerenate total lines 
	        
	        $labelColspan = $subtotalOffset - $leftOffset + 1; //space for total labels
	        
	        $couponLine = false;

        	if ($couponDiscount != 0 AND !$this->deliveryNote){

	        	$couponLine = $template[1];
	        	$couponLine = NbordersHelper::removeColumns($couponLine,$colsToDelete);
	        	
	        	if ($this->params->get('coupon_extended')>0){ //broke coupon discount price to net + tax + price

		        	if ($leftOffset>0)
		        		$couponLine = NbordersHelper::replaceColumn($couponLine,$leftOffset-1, '<td align="left">'.$this->_('COM_NETBASEVM_EXTEND_COUPON').':</td>'); //use shorter string version
		            $couponLine = str_replace('{price_notax}',InvoiceCurrencyDisplay::getFullValue($couponNoTax, $this->currency, $this->showCurrency),$couponLine);
		            $couponLine = str_replace('{tax_rate}',$couponTaxRate.'%',$couponLine);
		            
		            if ($showingTaxPrice AND $showingTaxPriceItem) //if showing both taxes, replace only "overall
		            	$couponLine = str_replace('{tax_price}',InvoiceCurrencyDisplay::getFullValue($couponTaxAmount, $this->currency, $this->showCurrency),$couponLine);
		          	else //else replace one or other
		            	$couponLine = str_replace(array('{tax_price}', '{tax_price_item}'),InvoiceCurrencyDisplay::getFullValue($couponTaxAmount, $this->currency, $this->showCurrency),$couponLine);
	        	}
	        	else{ //else just coupon discount
	        		
		        	if ($leftOffset>0 && $labelColspan>0){ //make column wider straight to subtotal
		        		$couponLine = NbordersHelper::removeColumns($couponLine,range($leftOffset-1, $subtotalOffset -1));
		        		$td = '<td align="left" colspan="'.$labelColspan.'">'.$this->_('COM_NETBASEVM_EXTEND_COUPON_DISCOUNT').':</td>';
		        		$couponLine = NbordersHelper::addColumn($couponLine,$leftOffset-1, $td);
		        	}
	        	}
	        	$couponLine = str_replace($subtotalTag,InvoiceCurrencyDisplay::getFullValue($couponDiscount, $this->currency),$couponLine);	        	
	        	$couponLine = preg_replace('#\{[\w ]+\}#Us','',$couponLine); //remove rest of tags
	        }
	        
	        if ($couponLine && $this->params->get('coupon_extended')) //add "extended" coupon line
				$footerLines['coupon_extended'][] = $couponLine;
     	        
	        //VM2: display lines with used calcaultion rules
	        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2 && $this->calcRules)
	        	foreach ($this->calcRules as $rule){
	        		
	        		if (!empty($rule->virtuemart_order_item_id)) //VM 2.0.12, skip rules for products
	        			continue;
	        		if ((string)$rule->calc_amount==0) //skip rules with no amount
	        			continue;
	        		
	        		$ruleCurrency = !empty($rule->calc_currency) ? $rule->calc_currency : $this->currency; //get currency (VM 2.0.12 stores it)
	        		//$ruleValue = 
	        		
		        	$ruleLine = $template[1];
		        	$ruleLine = NbordersHelper::removeColumns($ruleLine,$colsToDelete);
		        	if ($leftOffset>0 && $labelColspan>0){ //make column wider straight to subtotal
		        		$ruleLine = NbordersHelper::removeColumns($ruleLine,range($leftOffset-1, $subtotalOffset -1));
		        		$td = '<td align="left" colspan="'.$labelColspan.'">'.($rule->calc_rule_name ? $rule->calc_rule_name.':' : '').'</td>';
		        		$ruleLine = NbordersHelper::addColumn($ruleLine,$leftOffset-1, $td);
		        	}
		        	$ruleLine = str_replace($subtotalTag,InvoiceCurrencyDisplay::getFullValue($rule->calc_amount, $ruleCurrency),$ruleLine);
					$ruleLine = preg_replace('#\{[\w ]+\}#Us','',$ruleLine); //remove rest of tags
					$footerLines['calc_rules'][] = $ruleLine;
	        	}
	        
	   		$orderDiscount =  $this->order->order_discount;
	        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM1 && $this->params->get('show_payment_row')!=2) //in VM1 and we already deducted payment discount from order discount...
	        	$orderDiscount += $this->order->order_payment + $this->order->order_payment_tax;
	   		
	        if ((int)InvoiceCurrencyDisplay::getFullValue(-$orderDiscount, $this->currency,false) != 0
	        	AND !(COM_NETBASEVM_EXTEND_ORDERS_ISVM2 AND $this->params->get('take_discount_into_summary',0)==1)){
	        	$discountLine = $template[1];
	        	$discountLine = NbordersHelper::removeColumns($discountLine,$colsToDelete);
	        	if ($leftOffset>0 AND $labelColspan>0){  //make column wider straight to subtotal
	        		$discountLine = NbordersHelper::removeColumns($discountLine,range($leftOffset-1, $subtotalOffset -1));
	        		$td = '<td align="left" colspan="'.$labelColspan.'">'.$this->_($orderDiscount> 0 ? 'COM_NETBASEVM_EXTEND_DISCOUNT' : 'COM_NETBASEVM_EXTEND_FEE').':</td>';
	        		$discountLine = NbordersHelper::addColumn($discountLine,$leftOffset-1, $td);
	        	}
	        	$discountLine = str_replace($subtotalTag,InvoiceCurrencyDisplay::getFullValue(-$orderDiscount, $this->currency),$discountLine);
				$discountLine = preg_replace('#\{[\w ]+\}#Us','',$discountLine); //remove rest of tags
				$footerLines['discount'][] = $discountLine;
	        }

	        if ($couponLine AND !$this->params->get('coupon_extended')) //add "simple" coupon line
				$footerLines['coupon_simple'][] = $couponLine;
	        
	        //CUSTOMIZATION: (Photogem s.r.l.) - show totals with coupon discounts
	        /*
	        "Total order deduced the coupon net value without tax (euro 5,25)
			Total tax deduced the coupon tax (euro 1,25)
			Total order, net value + tax (euro 7,20)"
			*/

	        //show totals
	        
	        if ($this->params->get('show_total_net',0)>0) //add "Total net" line
	        {
	        	$totalNetLine = $template[1];
	        	$totalNetLine = NbordersHelper::removeColumns($totalNetLine,$colsToDelete);
	        	if ($leftOffset>0 && $labelColspan>0){  //make column wider straight to subtotal
	        		$totalNetLine = NbordersHelper::removeColumns($totalNetLine,range($leftOffset-1, $subtotalOffset -1));
	        		$td = '<td align="left" colspan="'.$labelColspan.'">'.$this->_('COM_NETBASEVM_EXTEND_TOTAL_NET').':</td>';
	        		$totalNetLine = NbordersHelper::addColumn($totalNetLine,$leftOffset-1, $td);
	        	}

	        	//show without (other, not coupon) discount (that is in "disocunts"), unless...
	        	$totalNetShow = $totalNet; //2: witout coupon discount
	        	if ($this->params->get('show_total_net',0)==1) //1: deduct coupon
	        		$totalNetShow += $couponNoTax;
	        	elseif ($this->params->get('show_total_net',0)==4) //4: deduct coupon and items discounts
	        		$totalNetShow += $couponNoTax + $totalDiscount;
	        	elseif ($this->params->get('show_total_net',0)==3) //...if set, deduct from net now (andrea.coppolecchia@gmail.com)
	        		$totalNetShow += $totalDiscount;

	        	$totalNetLine = str_replace($subtotalTag,InvoiceCurrencyDisplay::getFullValue($totalNetShow, $this->currency),$totalNetLine);
				$totalNetLine = preg_replace('#\{[\w ]+\}#Us','',$totalNetLine); //remove rest of tags
				$footerLines['total_net'][] = $totalNetLine;
	        }
	        
	        if ($this->params->get('show_total_tax',0)>0) //add "Total tax" line
	        {
	        	$totalTaxLine = $template[1];
	        	$totalTaxLine = NbordersHelper::removeColumns($totalTaxLine,$colsToDelete);
	        	if ($leftOffset>0 && $labelColspan>0){  //make column wider straight to subtotal
	        		$totalTaxLine = NbordersHelper::removeColumns($totalTaxLine,range($leftOffset-1, $subtotalOffset -1));
	        		$td = '<td align="left" colspan="'.$labelColspan.'">'.$this->_('COM_NETBASEVM_EXTEND_TOTAL_TAX').(($this->params->get('show_total_tax_percent') AND $onlyOneTaxRate!==false) ? ' ('.($onlyOneTaxRate*1).'%)' : '').':</td>';
	        		$totalTaxLine = NbordersHelper::addColumn($totalTaxLine,$leftOffset-1, $td);
	        	}
	        	
	        	$totalTaxShow = $totalTax; //2: witout coupon discount
	        	if ($this->params->get('show_total_tax',1)==1) //1: deduct coupon
	        		$totalTaxShow += $couponTaxAmount;
	        		        	
	        	//without coupon discount
	        	$totalTaxLine = str_replace($subtotalTag,InvoiceCurrencyDisplay::getFullValue($totalTaxShow, $this->currency),$totalTaxLine);
	        	$totalTaxLine = preg_replace('#\{[\w ]+\}#Us','',$totalTaxLine); //remove rest of tags
	        	$footerLines['total_tax'][] = $totalTaxLine;
	        }
	        
	        if (($totalDiscount) && $this->params->get('show_total_discount',0)>0) //add "Total discount" line
	        {
	        	$totalDiscountLine = $template[1];
	        	$totalDiscountLine = NbordersHelper::removeColumns($totalDiscountLine,$colsToDelete);
	        	if ($leftOffset>0 && $labelColspan>0){  //make column wider straight to subtotal
	        		$totalDiscountLine = NbordersHelper::removeColumns($totalDiscountLine,range($leftOffset-1, $subtotalOffset -1));
	        		$td = '<td align="left" colspan="'.$labelColspan.'">'.$this->_('COM_NETBASEVM_EXTEND_TOTAL_DISCOUNT').':</td>';
	        		$totalDiscountLine = NbordersHelper::addColumn($totalDiscountLine,$leftOffset-1, $td);
	        	}
	        	
	        	
	        	$totalDiscountShow = $totalDiscount; //2: witout coupon discount
	        	if ($this->params->get('show_total_discount',1)==1) //1: deduct coupon
	        		$totalDiscountShow += $couponDiscount;

	        	$totalDiscountLine = str_replace($subtotalTag,InvoiceCurrencyDisplay::getFullValue($totalDiscountShow, $this->currency),$totalDiscountLine);
	        	$totalDiscountLine = preg_replace('#\{[\w ]+\}#Us','',$totalDiscountLine); //remove rest of tags
	        	$footerLines['total_discount'][] = $totalDiscountLine;
	        }
	        
	        //show total line
	        $totalLine = $template[1];
	        if ($this->params->get('total_extended',0)>0){

	        	$totalNetShow = $totalNet; //2: not deduct coupon discount
	        	$totalTaxShow = $totalTax;
	        	$totalDiscountShow = $totalDiscount + $couponDiscount; //(so we must add it to discount field to prices fit)
	        	if ($this->params->get('total_extended',0)==1){ //1: deduct coupon discount
	        		$totalNetShow += $couponNoTax;
	        		$totalTaxShow += $couponTaxAmount;
	        		$totalDiscountShow -= $couponDiscount; //not add to disocunt fields to prices fit
	        	}
	        	
			    $totalLine = str_replace('{price_notax}',InvoiceCurrencyDisplay::getFullValue($totalNetShow, $this->currency, $this->showCurrency),$totalLine);
			    if ($onlyOneTaxRate)//if is used one tax for whole order, display it
			    	$totalLine = str_replace('{tax_rate}',$onlyOneTaxRate.'%',$totalLine);

			    if ($showingTaxPrice AND $showingTaxPriceItem) //if showing both taxes, replace only "overall
			   	 	$totalLine = str_replace('{tax_price}',InvoiceCurrencyDisplay::getFullValue($totalTaxShow, $this->currency, $this->showCurrency),$totalLine);
			    else //else replace one or other
			    	$totalLine = str_replace(array('{tax_price}', '{tax_price_item}'),InvoiceCurrencyDisplay::getFullValue($totalTaxShow, $this->currency, $this->showCurrency),$totalLine);

			    $totalLine = str_replace('{discount}',InvoiceCurrencyDisplay::getFullValue($totalDiscountShow, $this->currency, $this->showCurrency),$totalLine);
	        }

	        $totalLine = NbordersHelper::removeColumns($totalLine,$colsToDelete);
	        
        	if ($leftOffset>0 && $labelColspan>0){  //add total label
        		if ($this->params->get('total_extended')){
        			$td = '<td align="left"><b>'.$this->_('COM_NETBASEVM_EXTEND_TOTAL').':</b></td>';
        			$totalLine = NbordersHelper::removeColumns($totalLine,$leftOffset-1);
        			$totalLine = NbordersHelper::addColumn($totalLine,$leftOffset-1, $td);
        		}
        		else { //make column wider straight to subtotal
        			$td = '<td align="left" colspan="'.$labelColspan.'"><b>'.$this->_('COM_NETBASEVM_EXTEND_TOTAL').':</b></td>';
	        		$totalLine = NbordersHelper::removeColumns($totalLine,range($leftOffset-1, $subtotalOffset -1));
	        		$totalLine = NbordersHelper::addColumn($totalLine,$leftOffset-1, $td);
        		}
	        }

	        $totalLine = str_replace($subtotalTag,'<b>'.InvoiceCurrencyDisplay::getFullValue($this->order->order_total, $this->currency).'</b>',$totalLine);
	        $totalLine = preg_replace('#\{[\w ]+\}#Us','',$totalLine); //remove rest of tags
	        
	        $footerLines['total'][] = $totalLine; 
        }
        
        
        $headerLine = NbordersHelper::removeColumns($template[0],$colsToDelete); //language contstants are at template and are replaced by [] replace in method getHTML
		
        //now compute columns width from header columns
        $colWidths = array();
        $headColumns = NbordersHelper::getColumns($headerLine);
        foreach ($headColumns as $key => $headColumn)
        {
        	if (preg_match('#style\s*=\s*"[^"]*width\s*:\s*(\d+)\s*%#is',$headColumn[1],$width)) //width set by style
        		$colWidths[$key]=(int)$width[1]; 
        	if (preg_match('#width\s*=\s*"?\s*(\d+)\s*%#is',$headColumn[1],$width)) //width set by attribute
        		$colWidths[$key]=(int)$width[1]; 
        }

        if ($colsNoWidth = count($headColumns) - count($colWidths)) //some columns dont have width specified
        {
        	$oneColumn = (100 - array_sum($colWidths)) / $colsNoWidth; //split remaning % (if any) width between them
        	
        	if ($oneColumn<0)
        		$oneColumn = 0;
        		
	        foreach ($headColumns as $key => $headColumn) 
	        	if (!isset($colWidths[$key]))
	        		$colWidths[$key] = $oneColumn;	
        }

        if (($widthSum = array_sum($colWidths))!=100) //overall width is bigger or lower than 100%
        {
        	$ratio = 100 / $widthSum; //ratio to reduce/enlarge them to 100
        	foreach ($colWidths as &$width)
        		$width *= $ratio;
        }
        //TODO: get column widths array real (like without colspan). when is colspan, devide by it
		//TODO: when setting column widths, counts also with colspan (sum them)
        //set new widths
        $headerLine = NbordersHelper::setColumnWidths($headerLine,$colWidths);
		$itemLines = NbordersHelper::setColumnWidths($itemLines,$colWidths);
			
		//start generating table
        $code = VMI_NL.'<table style="table-layout:fixed;width:100%" >';
        if ($this->params->get('repeat_header')) //TCPDF will auto repeat then
        	$code.= VMI_NL.'<thead>';
		$code.= VMI_NL.$headerLine;
		$code.= VMI_NL.'<tr><td width="100%" colspan="'.$colsNo.'"><hr></td></tr>'; //TODO: tohle dÄ›lat v tamplatu. poÄŤĂ­tat s rowspanem. au. :D a proÄŤ je to tak velkĂ©?
        if ($this->params->get('repeat_header'))
        	$code.= VMI_NL.'</thead>';
        $code.= VMI_NL.'<tbody>';
        $code.= VMI_NL.implode(VMI_NL,$itemLines);
        $code.= VMI_NL.'</tbody>';
        
        
        //booing does not have footer?
        
        //TODO: samzřejmě postupně uvolnit, možnost přodat tyhle liny i do DN, všechno editovat přes ten ffoter editor
        //pluc ehckboy "display footer"
        //a pa u každého řádku ano, ne a paramery
        //takže parametry budou i pro DN ? jedna v2c je show nowhow a .. hm...
        //asi jo. ale u invoicu bude zašrtávátko "same also for DN" a to to zkopčí do DN (?)
        //prostě zvlášť.
        
        if (!$this->deliveryNote)
        {
	        $code.= VMI_NL.'<tfoot>';

	        //re-sort footer lines based on config
			$ordering = NbordersHelper::getItemsFooterOrdering($this->deliveryNote);
			$footerLinesOrdered = array();
			foreach ($ordering as $key => $type){
				if (isset($footerLines[$type]))
					$footerLinesOrdered[$type] = $footerLines[$type];
				elseif ($type=='empty') // add empty line
					$footerLinesOrdered[$type.'_'.$key] = $emptyCode; //must be unique
				elseif ($type=='hr') // add hr line
					$footerLinesOrdered[$type.'_'.$key] = $hrCode; //must be unique
			}
			
	        //make one-dimensional array from it
	        $footerLinesNew = array();
	        foreach ($footerLinesOrdered as $type => $lines) {
        		if (!is_array($lines))
        			$footerLinesNew[$type] = NbordersHelper::setColumnWidths($lines,$colWidths);
        		else
        			foreach ($lines as $key => $line)
	        			$footerLinesNew[$type.'_'.$key] = NbordersHelper::setColumnWidths($line,$colWidths);
	        }
	        
	        //delete duplicated rows (like hr-rows next to each other)
	        $lastLine = false;
	        foreach ($footerLinesNew as $key => $line){
	        	if ($lastLine==$line AND !preg_match('#^empty_#', $key)) //not apply for empty line (can be intentional)
	        		unset($footerLinesNew[$key]);
	        	$lastLine = $line;
	        }
	        
	        //trigger event
	        $this->dispatcher->trigger('onItemsFooterWrite', array(&$this, &$footerLinesNew, &$code, $this->params));
	        
	        $code.= implode(VMI_NL, $footerLinesNew);
	        
	        $code.= VMI_NL.'</tfoot>';
        }
        
        $code.= VMI_NL.'</table>';

        //insert special tag fro TCPDF to non-break table rows (but its not working)
        $code = preg_replace('#(<\s*tr.*)>#Uis','$1 nobr="true">',$code);
 
        //trigger event
	    $this->dispatcher->trigger('onItemsWrite', array(&$this, &$code, $this->params));
	        
        return $code;
    }

    function getSignature ()
    {
		
    }
}
?>
