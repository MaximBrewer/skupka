/*------------------------------------------------------------------------
* Advanced Virtuemart Invoices
* author    CMSMart Team
* copyright Copyright (C) 2012 Cmsmart Team. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Technical Support:  Forum - http://cmsmart.net/forum
* version 2.0
-------------------------------------------------------------------------*/

var lastSelected=null;
var productID = null;
var url = null;

function showUserData(url){	
	var user_id = $('user_id').value;
	
	if (url=='') //get base url from form
		url = $('baseurl').value;
	
	if (user_id != null){
		url += 'index.php?option=com_netbasevm_extend&controller=order&task=userajax&uid='+encodeURIComponent(user_id)+'&tmpl=component';
		
		if (typeof Request != "undefined" && typeof Request.HTML != "undefined"){ //use request HTML which separates scripts and allows to evaluate them
		
			var req = new Request.HTML({ 
				url:url,
				evalScripts: false, 
				noCache: true, 
				onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript){
					updateUserInfo(responseHTML);
					eval(responseJavaScript);
				}
			}).send();
		}
		else
			AUtility.ajaxSend (url, updateUserInfo, null);
	}
}


function updateUserInfo(response,name){	
	document.getElementById('userInfo').innerHTML = response;
	
	//window.JTooltips.initialize();
	
	/*
	//eval all <scrips> on the page again - to load editors again (caused problems with editors not loaded and order cannot be saved)
	//EDIT: editors disabled completely in tmpl/userinfo.php
	var re = new RegExp('textarea', "g");
	if (response.match(re))
	{
		$each (document.getElements('script'),function (script){
			eval(script.innerHTML);
		});
	}
	*/
	
	//attach "onchange" events on input again
	changed_userinfo=false;
	addUserInfoCheck(); 
}

function copyBillingToDelivery()
{
	//copy inputs
	$each($('billing_address').getElements('input'),function (b_input){
		
		s_name = 'S_'+b_input.getProperty('name').substring(2);
		
		//checkbox or radio
		if (b_input.getProperty('type')=='checkbox' || b_input.getProperty('type')=='radio')
		{
			//find collection of opposite inputs by name
			if (s_name.substr(-2)=='[]') //by this[] mootools is confused - use only begining
				s_inputs =  $('shipping_address').getElements('input[name^='+s_name.substr(0,s_name.length-2)+']');
			else
				s_inputs =  $('shipping_address').getElements('input[name='+s_name+']');

			$each(s_inputs,function (s_input){ //find input with same value

				if (s_input.value==b_input.value){
					//check or uncheck him
					if (b_input.checked) s_input.checked=true; 
					else s_input.checked=false;
				}
			});
		}
		//text input
		else {
			s_input =  $('shipping_address').getElement('input[name='+s_name+']');
			if (s_input)
				s_input.value=b_input.value;
		}
	});
	
	//copy selects
	$each($('billing_address').getElements('select'),function (b_input){
		
		//find selected options
		var selected = new Array();
		$each(b_input.getElements('option'),function (b_option){ //find selected options
			if (b_option.selected==true)
				selected[b_option.value]=b_option.value;
		});
		
		s_name = 'S_'+b_input.getProperty('name').substring(2);

		if (s_name.substr(s_name.length-2,2)=='[]') //by this[] mootools is confused - use only begining.  btw IE bug with substr http://rapd.wordpress.com/2007/07/12/javascript-substr-vs-substring/
			s_input =  $('shipping_address').getElement('select[name^='+s_name.substr(0,s_name.length-2)+']'); 
		else
			s_input =  $('shipping_address').getElement('select[name='+s_name+']');
		
		if (s_input)
		{
			$each(s_input.getElements('option'),function (s_option){ //iterate through options
				if (typeof selected[s_option.value] == 'undefined' ) //and select/unselect them
					s_option.selected=false
				else
					s_option.selected=true;
			});
		}
	});
	
	//copy textareas
	$each($('billing_address').getElements('textarea'),function (b_input){
		
		s_name = 'S_'+b_input.getProperty('name').substring(2);
		s_input = $('shipping_address').getElement('textarea[name='+s_name+']');
		
		if (s_input)
			s_input.value = b_input.value;
		
	});
	
	//copy iframe (editor)
	$each($('billing_address').getElements('iframe'),function (b_input){
		
		s_name = 'S_'+b_input.getProperty('id').substring(2);
		s_input = $('shipping_address').getElement('iframe[id='+s_name+']');
		
		if (s_input)
			s_input.contentWindow.document.body.innerHTML = b_input.contentWindow.document.body.innerHTML;
		
	});
	
	changed_userinfo=true;
	
	$('billing_is_shipping').checked = false;
	enableAllShipping();
}

function disableAllShipping()
{
//	jQuery.each(jQuery('shipping_address').getElements('textarea,input,select'),function (el){
//		if (el.name != 'billing_is_shipping')
//			el.disabled = true;
//	});
document.id('shipping_address').getElements('textarea,input,select').each(function(el) {
    if (el.name != 'billing_is_shipping')
	el.disabled = true;
});
}

function enableAllShipping()
{
    document.id('shipping_address').getElements('textarea,input,select').each(function(el) {
    if (el.name != 'billing_is_shipping')
	el.disabled = false;
});
}

function showOrderData(url, newProduct, overrideShipping, overridePayment) 
{	
	if (url === undefined || !url) //get base url from form
		url = $('baseurl').value;	
	
	if (newProduct === undefined)
		newProduct = false;
	
	if (overrideShipping === undefined)
		overrideShipping = false;
	
	if (overridePayment === undefined)
		overridePayment = false;
	
	url += 'index.php?option=com_netbasevm_extend&controller=order&task=orderajax&tmpl=component';
	
	if (newProduct){
		if ($('newproduct_id').value != '' || $('newproduct').value.trim() != ''){
			url += appendQuery('pid','newproduct_id');
			url += appendQuery('pname','newproduct');
			url += appendQuery('pprice','newproduct_price');
		}
		else {
			alert(AddProduct);
			return ;
		}
	}
	
	url += appendQuery('cid','cid');
	url += appendQuery('status','status');
	url += appendQuery('payment_method_id','payment_method_id');
	url += appendQuery('shipment_method_id','shipment_method_id'); //only for vm2
	
	url += appendQuery('coupon_discount','coupon_discount');
	url += appendQuery('order_discount','order_discount'); //only for vm1
	
	url += appendQuery('order_payment','order_payment'); 
	url += appendQuery('order_payment_tax','order_payment_tax'); //only for vm2
	url += appendQuery('order_payment_taxrate','order_payment_taxrate'); //only for vm2
	
	url += appendQuery('order_shipping','order_shipping');
	url += appendQuery('order_shipping_tax','order_shipping_tax');
	url += appendQuery('order_shipping_taxrate','order_shipping_taxrate');

	if (overrideShipping)
		url += '&override_shipping=1';
		
	if (overridePayment) //only for vm2. vm1 is overriden every time
		url += '&override_payment=1';
	
	var params = '';
	params = appendProperty(params, 'orderInfo', 'input', 'product_quantity');
	params = appendProperty(params, 'orderInfo', 'textarea', 'product_attribute');
	params = appendProperty(params, 'orderInfo', 'select', 'order_status');
	params = appendProperty(params, 'orderInfo', 'input', 'product_item_price');
	params = appendProperty(params, 'orderInfo', 'input', 'product_tax');
	params = appendProperty(params, 'orderInfo', 'input', 'product_price_discount');
	params = appendProperty(params, 'orderInfo', 'input', 'product_id');
	params = appendProperty(params, 'orderInfo', 'input', 'order_item_id');
	params = appendProperty(params, 'orderInfo', 'input', 'order_item_name');
	params = appendProperty(params, 'orderInfo', 'input', 'order_item_sku');
	params = appendProperty(params, 'orderInfo', 'select', 'tax_rate');
	params = appendProperty(params, 'orderInfo', 'input', 'calc_rule_name'); //only for vm2
	params = appendProperty(params, 'orderInfo', 'select', 'calc_kind'); //only for vm2
	params = appendProperty(params, 'orderInfo', 'input', 'calc_amount'); //only for vm2

	AUtility.ajaxSendPost (url, params, updateOrderInfo, null);

	return;
}

function deleteProduct(a) {
	if (confirm(AreYouSure)){
		var td = $(a).getParent();
		var tr = $(td).getParent();
		var tbody = $(tr).getParent();
		tbody.removeChild(tr);
	}
	return;
}

function appendQuery(varName,inputId){
	if ($(inputId))
		return '&'+varName+'='+encodeURIComponent($(inputId).value);
	return '';
}

function appendProperty(url, parent, type, name) {
	var elements = $(parent).getElements(type + '[name^=' + name + ']');
	for (var i = 0; i < elements.length; i++)
		url += (url == '' ? '' : '&') + elements[i].name + '=' + encodeURIComponent(elements[i].value);
	return url;
}

function updateOrderInfo(response, name) 
{	
	$('orderInfo').innerHTML = response;		
}


function GetKeyCode(e)
{
	var event = new Event(e);
	return event.code;
} 

function generateWhisper(element,e,url,type)
{
	var unicode = GetKeyCode(e);
	var str = element.value;
	if (unicode != 38 && unicode != 40 && str != lastSelected) {
		if (unicode != 13) {	
			url += 'index.php?option=com_netbasevm_extend&controller=order&task=whisper&tmpl=component&str='+ encodeURIComponent(str)+'&type='+element.name;
			AUtility.ajaxSend (url, processRequest, element);
		} else     
			setWhisperVisibility(element,'none');
	}
}

function moveWhisper(element,e) {
	var unicode = GetKeyCode(e);
	var naseptavac = $('naseptavac');
	if (unicode == 40) {
		naseptavac.options.selectedIndex = ((naseptavac.options.selectedIndex >= 0) && (naseptavac.options.selectedIndex < (naseptavac.options.length-1)) ? (naseptavac.options.selectedIndex+1) : 0);
		getChangeHandler(element);
	} else if (unicode == 38) {
		naseptavac.options.selectedIndex = ((naseptavac.options.selectedIndex > 0) ? (naseptavac.options.selectedIndex-1) : (naseptavac.options.length-1));
		getChangeHandler(element);
	}
	else if (unicode == 13) { //enter
		getClickHandler(element);
		lastSelected = element.value;
		if (window.event)
			e.returnValue = false;
		else
			e.preventDefault();
		setWhisperVisibility(element,'none');
	}
} 

function processRequest(response, element) {
    var name = element.name + 'whisper';
    $(name).innerHTML = response;
    $('naseptavac').size = $('naseptavac').options.length;
    setWhisperVisibility(element,'block');
}

function getChangeHandler(element) {
	var select = $('naseptavac');
	var nazev = select.options[select.selectedIndex].innerHTML;	
	element.value = nazev.replace(/\&amp;/g,'&');
	document.getElementById(element.name+'_id').value = select.value;
}

function getClickHandler(element) {
	getChangeHandler(element);
	if (element.name=='user')
		showUserData('');
	if (element.name=='newproduct')
		showOrderData(null,true,false,false);
	
}

function generateParams(response,name){
	$('params').innerHTML = response;
}

function setWhisperVisibility(element,value){
	var name = element.name + 'whisper';
	$(name).style.display = value;
}

function processShippingChange(el)
{
	var shipping = $('ship_method_id').options[$('ship_method_id').selectedIndex].value.split('|');
	
	if (shipping.length > 3)
	{
		$('custom_shipping_class').value=shipping[0];
		$('custom_shipping_carrier').value=shipping[1];
		$('custom_shipping_ratename').value=shipping[2];
		$('custom_shipping_costs').value=shipping[3];
		$('custom_shipping_id').value=shipping[4];
		$('custom_shipping_taxrate').value=shipping[5];
	}
}

function applyShipping() //only for VM1 (i VM2 is applied in php based on selected shipping_method_id)
{
	$('order_shipping').value = $('custom_shipping_costs').value;
	$('order_shipping_taxrate').value = $('custom_shipping_taxrate').value;
	
	showOrderData(null,false,false,false);
}

function applyStatus()
{
	$$('select[name^=order_status[]]').set('value',$('status').value);
}

var last_coupon = "";

function getCouponInfo(coupon,currency,url)
{
	if (last_coupon != coupon)
	{
		last_coupon = coupon;
		$('coupon_info').innerHTML='...';
		
		if (url === undefined) //get base url from form
			url = $('baseurl').value;

		url += 'index.php?option=com_netbasevm_extend&controller=order&task=couponajax&coupon='+encodeURIComponent(coupon)+'&currency='+encodeURIComponent(currency);
		AUtility.ajaxSend (url, updateCouponInfo);
	}
}

function updateCouponInfo(text)
{
	document.getElementById('coupon_info').innerHTML=text;
}

function passCouponDiscount(type,discount)
{
	if (type=="percent")	{
		
		if ($('order_salesPrice')) /* VM2 */
			base = $('order_salesPrice').value*1;
		else /* VM1 */
			base = $('order_subtotal').value*1+$('order_tax').value*1;
		
		discount = Math.round(discount*base)/100;
	}	
		
	$('coupon_discount').value = (-(discount*1))*1;
}

function populateStates(countrySelectName,stateSelectName)
{
	country = $(document.body).getElement('select[name='+countrySelectName+']').value;
	
	stateSelect = $(document.body).getElement('select[name='+stateSelectName+']');
	
	if (country && stateSelect){
		stateSelect.innerHTML = '<option>...</option>';
		AUtility.ajaxSend('index.php?option=com_netbasevm_extend&controller=order&task=statesajax&country_id='+country, populateStatesCallback);
	}
}

function populateStatesCallback(text)
{
	stateSelect.innerHTML = text;
}