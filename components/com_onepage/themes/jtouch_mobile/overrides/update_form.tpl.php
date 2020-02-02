<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
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
*/
?>
 <div class="stepper">
  <div data-role="fieldcontain">
  <div data-role="stepper" data-theme="d" class="stepper" rel="<?php echo $product->cart_item_id; ?>" data-direction="horizontal">
	<input id="quantity_for_<?php echo md5($product->cart_item_id); ?>" value="<?php echo $product->quantity; ?>" type="text" onchange="Onepage.qChange(this);" name="quantity" rel="<?php echo $product->cart_item_id; ?>" id="stepper1" class="quantity" min="0" max="999999" size="2" data-role="none" />
  </div>
  </div>
  </div>
  
	