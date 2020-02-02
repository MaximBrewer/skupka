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
if (!isset($action)) $action = 'purchase'; 
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
	  ga('<?php echo $tracker_name.'.'; ?>ec:setAction', '<?php echo $action; ?>'<?php 
	  
	  if (!empty($idformat) && ($this->isPurchaseEvent)) { ?>, { 
			'id': '<?php echo $this->escapeSingle($idformat); ?>',
			'affiliation': '<?php echo $this->escapeSingle($this->vendor['company']); ?>'<?php 
			
			if ((!empty($order_total) && (isset($this->order)) && (isset($this->order['details']['BT']->order_shipment)))) {
			  ?>,
			'revenue': <?php echo number_format($order_total, 2, '.', ''); ?>,
			'shipping': <?php echo number_format($this->order['details']['BT']->order_shipment, 2, '.', ''); ?>,
			'tax': <?php echo number_format($this->order['details']['BT']->order_tax, 2, '.', ''); ?>
			
			<?php } ?>
	  }<?php }
else
if (!empty($this->isCartEvent))	{
  echo ', {\'step\': 1} '; 
}
?>);

if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: EC action: <?php echo $action; ?>'); 
	  }
