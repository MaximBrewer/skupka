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

?>
if (typeof pageTracker != 'undefined')
{
pageTracker._addTrans(
      "<?php echo $this->escapeDouble($idformat); ?>",
      "<?php echo $this->escapeDouble($this->vendor['company']); ?>",
      "<?php echo number_format($order_total, 2, '.', ''); ?>",
      "<?php echo number_format($this->order['details']['BT']->order_tax, 2, '.', ''); ?>",
      "<?php echo number_format($this->order['details']['BT']->order_shipment, 2, '.', ''); ?>",
      "<?php echo $this->escapeDouble($this->order['details']['BT']->city); ?>",
      "<?php echo $this->escapeDouble($this->order['details']['BT']->state_name); ?>",
      "<?php echo $this->escapeDouble($this->order['details']['BT']->country_3_code); ?>"
    );
	
	
	  if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: _gat pageTracker addtrans'); 
	  }
}	  