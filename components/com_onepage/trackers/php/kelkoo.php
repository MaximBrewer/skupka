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


$idformat = $this->idformat; 
 

 
 $order_total = (float)$this->order['details']['BT']->order_total;
 
 $country = $this->order['details']['BT']->country_2_code; 
 
$order_total_txt = number_format($order_total, 2, '.', ''); 


$c = count($this->order['items']); 
$z = 0; 
?>
<script type="text/javascript">
       var _adftrack = {
            pm: 343548,
            divider: encodeURIComponent('|'),
            comid: [<?php echo $this->params->COMID; ?>],
            pagename: encodeURIComponent('<?php echo $this->params->COMID; ?>|sale'),
            order : {
                  sales: '<?php echo $order_total_txt; ?>',
                  orderid: '<?php echo $idformat; ?>',
                  sv1: '<?php echo $country; ?>',
				  itms: [<?php foreach ($this->order['items'] as $order_item) { 
				  
				  $z++; 
				  $product_id = $order_item->pid; 
				
				  
				  
				  ?>				  
                  {
                       productname: '<?php echo $this->escapeSingle($order_item->order_item_name); ?>',
                       productid: '<?php echo $product_id; ?>',
                       svn1: '<?php echo $order_item->product_quantity; ?>',
                       svn2: '<?php echo number_format($order_item->product_final_price, 2, '.', ''); ?>'
                  }<?php if ($z !== $c) echo ','; ?>
				  <?php } ?>
				  ]
           }
       };
       (function () { var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = 'https://s.kelkoo.com/s.js'; var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(s, x); })();
	   
	  if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: kelkoo order tracking trigerred'); 
	  }
	   
</script>

<?php
