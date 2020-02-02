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

defined('_JEXEC') or ('Restrict Access');

// Load the view framework
require_once (JPATH_ADMINISTRATOR.DS."components".DS."com_virtuemart".DS."version.php");
$vmver = new vmVersion();
$matches = vmVersion::$RELEASE;
if(version_compare($matches, '3.0.0', 'ge')){
	if(!class_exists('VmView'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmviewadmin.php');
}
else{
	if(!class_exists('VmView'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmview.php');
}


Jimport('joomla.application.component.view');

class NetBaseVm_ExtendViewOrder extends VmViewAdmin
{
    /**
     * @var VMInvoiceModelOrder
     */
    var $model = null;
    var $orderStatus = null;
    var $orderData = null;
    var $productsInfo = null;
    var $vendors = null;
    var $shippings = null;
    var $currencies = null;
    var $payments = null;
    var $countries;
    var $userajax = null;
    var $orderajax = null;
    
    var $newproduct_id = '';
    var $newproduct_name;
    
    function display($tpl = null)
    {
		$this->addHelperPath(array(JPATH_VM_ADMINISTRATOR.DS.'helpers'));
		
		// Load the helper(s)
		$this->loadHelper('html');
		
    	//if using date picker, include jquery (?) do iut soon, because lsem VM will include UI before jquery 
    	if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2){
    		NbordersHelper::importVMFile('helpers/config.php',true, true);
    		//vmJsApi::jQuery();
    	}
    	
    	
        $this->model = $this->getModel();
        
        JRequest::setVar('hidemainmenu', 1);
        
        $this->userajax = JRequest::getString('task') == 'userajax';
        $this->orderajax = JRequest::getString('task') == 'orderajax';
        $this->taxajax = JRequest::getString('task') == 'taxajax';
        
        $this->model->setOrderNo(JRequest::getInt('cid'));
        
        $this->countries = InvoiceGetter::getCountries();

        if (! $this->orderajax) {
        	
        	$uid = explode(';',JRequest::getVar('uid')); //get selected ST/BT address code from ajax

        	if ($uid[0]=='new')
        		$uid[0]=null;
        		
            $this->model->setUserId($uid[0]);
            
            $this->billingData = $this->userajax ? $this->model->getUserInfo('BT', $uid[1], $uid[0]) : $this->model->getOrderUserInfo('BT');
            $this->billingData->userFields = $this->model->getUserFields('B_',$this->billingData);
            
            $this->shippingData = $this->userajax ? $this->model->getUserInfo('ST', $uid[2], $uid[0]) : $this->model->getOrderUserInfo('ST');
            $this->shippingData->userFields = $this->model->getUserFields('S_', $this->shippingData);
            
        	$this->b_states = InvoiceGetter::getStates($this->billingData->country);
        	$this->s_states = InvoiceGetter::getStates($this->shippingData->country);
            
            if ($this->userajax) {
                $this->setLayout('userinfo');
                parent::display($tpl);
                exit();
            }
        }

        //get order data
        $this->orderData = $this->model->getOrderInfo($this->orderajax,JRequest::getInt('override_shipping'),JRequest::getInt('override_payment'));


        //if adding product, get select for new product price (if more prices)
        $this->productPrices = array();
        $this->productPriceSelected = null;
        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM1) //currently, this feature works only for VM1
	        if (($pid = JRequest::getInt('pid')) && is_null(JRequest::getVar('pprice'))){ //passed new product id but no price
	        
	        	 $productPrices = InvoiceGetter::getProductPrices($pid);
	        	 
	        	 if ($productPrices && count($productPrices)==1){ //only one price - act like it was alerady selected
	        	 	$price = reset($productPrices);
	        	 	JRequest::setVar('pprice', $price->product_price);
	        	 }
	        	 elseif ($productPrices){ //more prices - dont add product, instead display select box
					$groups = InvoiceGetter::getShopperGroups();
					foreach ($productPrices as $price){
		        	 	$group = isset($groups[$price->shopper_group_id]) ? ' ('.$groups[$price->shopper_group_id]->name.')' : null;
		        	 	$this->productPrices[] = JHTML::_('select.option', $price->product_price,  InvoiceCurrencyDisplay::getFullValue($price->product_price, $price->product_currency).$group);
		        	}
		        	
		        	//reset new product from request (to not add product below)
		        	JRequest::setVar('pid', null); 
		        	JRequest::setVar('pname', null); 
		        	
		        	//for template
		        	$this->newproduct_id = $pid;
		        	if ($product = InvoiceGetter::getProduct($pid))
		        		$this->newproduct_name = $product->product_name;
		        	
		        	//get pre-selected price based on order user id
		        	if (!empty($this->orderData->user_id) && ($groupId = InvoiceGetter::getShopperGroup($this->orderData->user_id)))
		        		foreach ($productPrices as $price)
		        			if ($price->shopper_group_id == $groupId)
		        				$this->productPriceSelected = $price->product_price;
	        	 }
	        	 else { //no prices in VM, very, very strange
	        	 	echo "Notice: No prices for product $pid";
	        	 }
	        }
        
        
        $this->orderStatus = InvoiceGetter::getOrderStates();
        $this->vendors = InvoiceGetter::getVendors();
        $this->taxRates = InvoiceGetter::getTaxRates();
        $this->nbDecimal = InvoiceCurrencyDisplay::getDecimals($this->orderData->order_currency);
        $this->productsInfo = $this->model->getProductsInfo(JRequest::getVar('order_item_id', null, 'default', 'array'), JRequest::getInt('pid'), JRequest::getVar('pname'), JRequest::getVar('pprice'),$this->orderData);

        if ($this->orderajax) { //rewrite products parameters from db by request ones

            $count = count($this->productsInfo);
            $rcount = @count($_REQUEST['order_item_id']);

            for ($i = 0; $i < $count; $i ++) {

                $product = &$this->productsInfo[$i];

                if ($i < $rcount){
                    $params = get_object_vars($product);
                    foreach ($params as $param => $value)
                        if (isset($_REQUEST[$param][$i]))
                            $product->$param = stripslashes($_REQUEST[$param][$i]); //stripslashes for all cases
                }
            }
        }
        
        $this->model->recomputeOrder($this->productsInfo, $this->orderData, $this->orderajax);
        
        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2){
        	$this->shippings = InvoiceGetter::getShippingsVM2();
        	$this->payments = InvoiceGetter::getPayments();
        }
        
        if ($this->orderajax) {
            $this->setLayout('products');
            parent::display($tpl);
            exit();
        }
        
        $this->model->setUserId($this->orderData->user_id);
        
        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM1){
        	$this->shippings = InvoiceGetter::getShippingsVM1($this->orderData->user_info_id, $this->orderData->order_currency, $this->orderData->user_id, $this->model->overal_weight);
        	$this->payments = InvoiceGetter::getPayments();
    	}
        $this->currencies = InvoiceGetter::getCurrencies();
        

        parent::display($tpl);
    }
}

?>