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

$id = $this->idformat;
$order_total = $this->order['details']['BT']->order_total;
$order_total = number_format($order_total, 2, '.', ''); 
$order_shipping = number_format($this->order['details']['BT']->order_shipment, 2, '.', '');
$order_tax = number_format($this->order['details']['BT']->order_tax, 2, '.', '');
?>
<!-- Send the Order data -->
<script>
  if (typeof sa != 'undefined')
  {
  sa('ecommerce', 'addOrder', JSON.stringify({
    order_id: '<?php echo $id; ?>',  // Order ID. Required.
    revenue:  '<?php echo $order_total; ?>',   // Grand Total. Includes Tax and Shipping.
    shipping: '<?php echo $order_shipping; ?>',    // Total Shipping Cost.
    tax:      '<?php echo $order_tax; ?>'    // Total Tax.
  }));
  
  if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: skroutz.gr order tracking sent'); 
	  }
  
  }
</script>
<script>
<?php 
foreach ($this->order['items'] as $key=>$order_item) { 
?>
 if (typeof sa != 'undefined')
	 if (typeof JSON != 'undefined')
		 if (typeof JSON.stringify != 'undefined')
		 {			 
 sa('ecommerce', 'addItem', JSON.stringify({
    order_id:   '<?php echo $id; ?>',  // Order ID. Required.
    product_id: '<?php echo $order_item->pid; ?>',  // Product ID. Required.
    price:      '<?php echo $order_item->product_final_price; ?>',    // Price per Unit. Required.
    quantity:   '<?php echo $order_item->product_quantity; ?>'        // Quantity of Items. Required.
  }
  ));
   
  if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: skroutz.gr order item sent'); 
	  }
		 }

<?php

}

?>
</script>
