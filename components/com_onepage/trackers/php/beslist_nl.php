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
$oc =  (int)(((float)$this->order['details']['BT']->order_shipment )*100); 
$os = (int)($order_total * 100); 

$order_total_txt = number_format($order_total, 2, '.', ''); 

$order_costs = (float)$this->order['details']['BT']->order_shipment + (float)$this->order['details']['BT']->order_payment; 
$order_costs_txt = number_format($order_costs, 2, '.', ''); 

$pl = ''; 
foreach ($this->order['items'] as $key=>$order_item) { 
$price = (int)($order_item->product_final_price * 100); 
if (!empty($pl)) $pl .= ';'; 
$pl .= $order_item->order_item_sku.':'.$order_item->product_quantity.':'.$price; 
}
$pl = $this->escapeSingle($pl); 


// \x70\x32\x2E\x62\x65\x73\x6C\x69\x73\x74\x2E\x6E\x6C  translates to: p2.beslist.nl
// \x77\x77\x77\x2E\x62\x65\x73\x6C\x69\x73\x74\x2E\x6E\x6C traslates to: www.beslist.nl

// pl: items
// os: order total
// ti: order_id
// oc: order shipping
// test is always 0
// ident is the hostname per configuration

if (empty($this->params->method) || (empty($this->params->shopid)))
{
  ?>
<script>  
var beslistQueue = [];
beslistQueue.push(['setShopId', '<?php echo $this->escapeSingle($this->params->shopid); ?>']);
beslistQueue.push(['cps', 'setTestmode', <?php if (empty($this->params->testmode)) echo 'false'; else echo 'true'; ?>]);
beslistQueue.push(['cps', 'setTransactionId', '<?php echo $this->order['details']['BT']->virtuemart_order_id; ?>']);
beslistQueue.push(['cps', 'setOrdersum', <?php echo $order_total_txt; ?>]);
beslistQueue.push(['cps', 'setOrderCosts', <?php echo $order_costs_txt; ?>]);


beslistQueue.push(['cps', 'setOrderProducts', [
<?php
$count = count($this->order['items']); 
$c = 0; 
foreach ($this->order['items'] as $key=>$order_item) {
$c++; 
?>

['<?php echo $this->escapeSingle($order_item->order_item_sku); ?>', <?php echo $order_item->product_quantity; ?>, <?php echo number_format($order_item->product_final_price, 2, '.', ''); ?>]<?php if ($c < $count) echo ','; 
echo "\n"; 
?>
<?php } ?>
]]);


beslistQueue.push(['cps', 'trackSale']);
(function () {
var ba = document.createElement('script');
ba.async = true;
ba.src = '//pt1.beslist.nl/pt.js';
var s = document.getElementsByTagName('script')[0];
s.parentNode.insertBefore(ba, s);
})();
  
</script>  
  <?php
}
else
{

?>

<script>
var _v = _v || [];
_v.push(
['ti', '<?php echo $this->order['details']['BT']->virtuemart_order_id; ?>'],
['os', '<?php echo $os; ?>'],
['pl', '<?php echo $pl; ?>'],
['oc', '<?php echo $oc; ?>'],
['ident', '<?php echo $this->params->hostname; ?>'],
['test', '<?php if (empty($this->params->testing)) echo '0'; else echo '1'; ?>']
);
var _a = "/pot/?v=2.1&p=" + encodeURIComponent(_v) + "&_=" + (Math.random() + "" * 10000000000000), 
_p = ('https:' == document.location.protocol ? 'https://' : 'http://'), 
_i = new Image;
_i.onerror = function(e) { _i.src = _p+"\x70\x32\x2E\x62\x65\x73\x6C\x69\x73\x74\x2E\x6E\x6C"+_a; _i = false; };
_i.src = _p+"\x77\x77\x77\x2E\x62\x65\x73\x6C\x69\x73\x74\x2E\x6E\x6C"+_a;
</script>

<?php
}