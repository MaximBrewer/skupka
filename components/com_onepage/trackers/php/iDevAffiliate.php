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
?><img src="<?php echo $this->params->url; ?>?profile=<?php echo $this->params->profile_id; ?>&idev_ordernum=<?php echo $this->order['details']['BT']->virtuemart_order_id; ?>&idev_saleamt=<?php echo number_format($total, 2, '.', ''); ?>" border="0" width="1" height="1" />


