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
				<a class="updatebtn" title="<?php echo OPCLang::_('COM_VIRTUEMART_CART_UPDATE'); 
?>" href="#" rel="<?php echo $product->cart_item_id.'|'.md5($product->cart_item_id); ;
?>">&nbsp;</a>
				<input type="text" title="<?php echo OPCLang::_('COM_VIRTUEMART_CART_UPDATE'); ?>" class="inputbox opcquantity" size="3" name="quantity" id="quantity_for_<?php echo md5($product->cart_item_id); ?>" value="<?php echo $product->quantity; ?>" />
				
				
				
				
				
				
				
				
			 