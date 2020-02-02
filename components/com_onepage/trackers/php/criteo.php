<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
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
$order_total = number_format($order_total, 2, '.', ''); 

?>


<!--  transaction tracker -->
<script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async="true"></script>
<script type="text/javascript">
window.criteo_q = window.criteo_q || [];
window.criteo_q.push(               
        { event: "setAccount", account: "<?php echo $this->params->account; ?>" },
        { event: "setCustomerId", id: "<?php echo $this->order['details']['BT']->virtuemart_user_id ?>" },
        { event: "setSiteType", type: "d" },
        { event: "trackTransaction", id: "<?php echo $this->order['details']['BT']->virtuemart_order_id ?>", new_customer: "",deduplication: "", item: [
<?php 
$last = count($this->order['items']); 
$i = 1; 
foreach ($this->order['items'] as $key=>$order_item) { ?>		
              { id: "<?php echo $order_item->virtuemart_product_id; ?>", price: <?php echo number_format($order_item->product_final_price, 2, '.', ''); ?>, quantity: <?php echo number_format($order_item->product_quantity , 0, '.', ''); ?> }<?php if ($i!=$last) echo ','; ?>

<?php 
$i++; 
} ?>
]
});
</script>

