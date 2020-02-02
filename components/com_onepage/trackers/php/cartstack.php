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


$total = 0; 
foreach ($this->order['items'] as $key=>$order_item) {
  $total += ((float)$order_item->product_discountedPriceWithoutTax * (float)$order_item->product_quantity); 
  
}
?>
	
	
<script type="text/javascript">
      var _cartstack = _cartstack || [];
          _cartstack.push(['setSiteID', '<?php echo $this->params->site_id; ?>']); /* required */
          _cartstack.push(['setAPI', 'confirmation']); /* required */
		  
	if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: cartstack order event'); 
	  }
		  
</script>
<script src="https://api.cartstack.com/js/cartstack.js" type="text/javascript"></script>
