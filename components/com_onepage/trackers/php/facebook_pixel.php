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

$order_total = $this->order['details']['BT']->order_total;
$order_total_txt = number_format($order_total, 2, '.', ''); 


$idformat = $this->idformat; 
 
 
 $currency = $this->order['details']['BT']->currency_code_3; 
 $currency = strtoupper($currency); 

 	
$products_tags = array(); 

$total = 0; 
foreach ($this->products as $key=>$product) { 

$pid = $product->pid;  
$products_tags[] = "'".$this->escapeSingle($pid)."'"; 
$total += ($product->product_final_price * $product->product_quantity); 


}
 
 $pr = implode(',', $products_tags);  
 $pids = '['.$pr.']'; 
	   

?><script>
fbq('track', 'Purchase', {
content_ids: [<?php echo $pids; ?>],
content_type: 'product',
value: <?php echo $order_total_txt; ?>,
currency: '<?php echo $currency; ?>'
});

		if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null)) 
							{
								console.log('OPC Tracking: Facebook thank you page detected'); 
							}

</script>
