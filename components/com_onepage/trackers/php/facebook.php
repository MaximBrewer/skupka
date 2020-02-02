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


?>



<!-- Facebook Conversion Code for chkt-pxl -->

<script>
if (typeof fbq !== 'undefined') {
	fbq('track', 'Purchase', {value: '<?php echo $order_total_txt; ?>', currency:'<?php echo $currency; ?>'});
	
	 if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: Facebook tracking via fbq object'); 
	  }
	
}
else {
(function() {
  	
	
  var _fbq = window._fbq || (window._fbq = []);
  if (!_fbq.loaded) {
    var fbds = document.createElement('script');
    fbds.async = true;
    fbds.src = '//connect.facebook.net/en_US/fbds.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(fbds, s);
    _fbq.loaded = true;
  }
})();
window._fbq = window._fbq || [];
window._fbq.push(['track', '<?php echo $this->params->addId; ?>', {'value':'<?php echo $order_total_txt; ?>','currency':'<?php echo $currency; ?>'}]);
}
</script>
<noscript>


<img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev=<?php echo $this->params->addId; ?>&cd[value]=<?php echo $order_total_txt; ?>&cd[currency]=<?php echo $currency; ?>&noscript=1" />

</noscript>


<script>
	  if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: Facebook tracking initialized'); 
	  }
</script>