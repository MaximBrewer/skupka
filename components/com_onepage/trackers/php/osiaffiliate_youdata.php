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
$order_total = $this->order['details']['BT']->order_subtotal;
$order_total_txt = number_format($order_total, 2, '.', ''); 
$order_id = $this->idformat;

if (empty($this->params->my_domain))
{
	 $this->params->my_domain = 'www.youdata.com'; 
}
?>
<script>
  function hideIF2() {	
  document.getElementById('IF').style.display = '';
  }
 function getSaleInfo2() {
  <?php
  
  $name = $this->order['details']['BT']->first_name; 
  $last = $this->order['details']['BT']->last_name; 
  $phone_1 = $this->order['details']['BT']->phone_1; 
  $email = $this->order['details']['BT']->email; 
  $qe = '&setdata1='.urlencode($name).'&setdata2='.urlencode($last).'&setdata3='.urlencode($phone_1).'&Email='.urlencode($email); 
 ?>  
 var s = document.getElementById('st_code2'); 
 if (s != null)
 {
	 s.innerHTML='<iframe src="https://<?php echo $this->params->my_domain; ?>/salejs.php?amount=<?php echo $order_total_txt; ?>&transaction=<?php echo $order_id; ?><?php echo $qe; ?>" alt="" id="IF" width="50px" height="50px" border="0" frameborder="0" onload="hideIF2()">';
 }
 }
 window.onload = getSaleInfo2; 
</script>
<div id="st_code2"></div>  

<script>
if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: OsiAffiliate code was executed'); 
	  }
	  
</script>	  