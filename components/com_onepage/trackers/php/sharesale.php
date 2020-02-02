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


$order_total = 0; 
$totalcalc = (int)$this->params->totalcalc; 
if ($totalcalc === 2) {
foreach ($this->order['items'] as $key=>$order_item) {
  $order_total += ((float)$order_item->product_discountedPriceWithoutTax * (float)$order_item->product_quantity); 
  
}
}
else {
	$order_total = $this->order['details']['BT']->order_total;

}
$order_total_txt = number_format($order_total, 2, '.', '');


$idformat = $this->idformat; 

?>

<!-- begin of the Share-a-Sale Affiliate tracking code -->
<img src="https://shareasale.com/sale.cfm?amount=<?php echo $order_total_txt; ?>&tracking=<?php echo $idformat; ?>&transtype=sale&merchantID=<?php echo $this->params->site_id; ?>" width="1" height="1" /> 
<!--- end of the affiliate code -->

<script>
if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: sharesale.com order event'); 
	  }
</script>	  
	
