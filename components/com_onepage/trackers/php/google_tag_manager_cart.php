<?php
/*
*
* @copyright Copyright (C) 2007 - 2013 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* stAn note: Always use default headers for your php files, so they cannot be executed outside joomla security 
*
*/

defined( '_JEXEC' ) or die( 'Restricted access' );


?>
<script>
/* <![CDATA[ */
//cart visit event
if (typeof dataLayer == 'undefined')
	dataLayer = []; 
<?php

$product = $this->product; 
$this->params->adwords_remarketing = true; 

include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'GA'.DIRECTORY_SEPARATOR.'datalayer_cart.php'); 
 
$this->isPureJavascript = true; 

?>
/* ]]> */
</script>

<script>
function callAfterPaymentSelectGTM()
{
	if (typeof Onepage == 'undefined') return; 
	if (typeof jQuery == 'undefined') return; 
	if (typeof ga == 'undefined') return; 
	
	var payment_id = Onepage.getPaymentId();
	
	if (payment_id == 'payment_id_0') return; 
	if (payment_id == 0) return; 
	
	var my_label = payment_id; 
	
	
	var label = jQuery("label[for='payment_id_"+payment_id+"']"); 
	if (label != null)
	if (typeof label.text != 'undefined')
	{
	  	var my_label2 = label.text(); 
		if (my_label2.length > 0) my_label = my_label2; 
		
		my_label = my_label.split("\r\r\n").join(' ').split("\r\n").join(" ").split("\n").join(" ").trim(); 
	}
	
	onCheckoutOptionGTM(3, my_label); 
	Onepage.op_log('OPC GTM Tracking: Payment selected '+my_label+', step 3'); 
}

function onCheckoutOptionGTM(step, checkoutOption) {
  if (typeof dataLayer != 'undefined')
  dataLayer.push({
    'event': 'checkoutOption',
    'ecommerce': {
      'checkout_option': {
        'actionField': {'step': step, 'option': checkoutOption}
      }
    }
  });
}

function callAfterShippingSelectGTM()
{
	
	 
	 
	if (typeof Onepage == 'undefined') return; 
	if (typeof jQuery == 'undefined') return; 
	if (typeof ga == 'undefined') return; 
	
	
	 var ship_id = Onepage.getInputIDShippingRate();
	 
	if (ship_id == 'choose_shipping') return; 
	if (ship_id == 'shipment_id_0') return; 
	
	var my_label = ship_id; 
	
	
	var label = jQuery("label[for='"+ship_id+"']"); 
	if (label != null)
	if (typeof label.text != 'undefined')
	{
	  	var my_label2 = label.text(); 
		if (my_label2.length > 0) my_label = my_label2; 
		
		my_label = my_label.split("\r\r\n").join(' ').split("\r\n").join(" ").split("\n").join(" ").trim(); 
	}
	
	
	onCheckoutOptionGTM(2, my_label); 
	Onepage.op_log('OPC GTM Tracking: Shipping selected '+my_label+', step 2'); 
	 
}

function onOpcErrorMessageGTM(msg, cat) {
  onCheckoutOptionGTM(5, cat+': '+msg); 
}

function callSubmitFunctGTM()
{
	onCheckoutOptionGTM(4, 'OPC Confirm Button Clicked'); 
	Onepage.op_log('OPC GTM Tracking: Confirm order button clicked, step 4'); 
}

if (typeof addOpcTriggerer != 'undefined')
{
  addOpcTriggerer('callAfterPaymentSelect', callAfterPaymentSelectGTM); 
  addOpcTriggerer('callAfterShippingSelect', callAfterShippingSelectGTM); 
   addOpcTriggerer('callSubmitFunct', callSubmitFunctGTM); 
   addOpcTriggerer('onOpcErrorMessage', onOpcErrorMessageGTM); 
  
}
</script>