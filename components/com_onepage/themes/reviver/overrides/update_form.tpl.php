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
?><form action="<?php echo $action_url ?>" method="post"  class="inline cart_form">
    <div class="update_form_wrap" style="position: relative;">
	<button type="submit" class="vmicon vm2-add_quantity_cart" style="float: left; clear:none; position: absolute; top: 10px; left: 0; " name="update" title="<?php echo OPCLang::_('COM_VIRTUEMART_CART_UPDATE'); ?>" value=" <?php echo  JText::_ ('COM_VIRTUEMART_CART_UPDATE') ?>">&nbsp;</button>

				<input type="hidden" name="option" value="com_virtuemart" />
				<input type="text" style="float: left; clear:none; position: absolute; top: 0; left: 20px; width: 30px;" title="<?php echo OPCLang::_('COM_VIRTUEMART_CART_UPDATE'); ?>" class="inputbox_update quantity-input" size="3" name="quantity" value="<?php echo $product->quantity; ?>" />
				<input type="hidden" name="view" value="cart" />
				<input type="hidden" name="task" value="update" />
				<input type="hidden" name="cart_virtuemart_product_id" value="<?php echo $product->cart_item_id; ?>" />
			
				<a style="float: left; clear:none; position: absolute; top: 10px; left: 85px;" class="vmicon vm2-remove_from_cart" title="<?php echo OPCLang::_('COM_VIRTUEMART_CART_DELETE'); 
?>" href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=cart&task=delete&cart_virtuemart_product_id='.$product->cart_item_id, true, $useSSL  ); 
?>">&nbsp;</a>
</div>
			  </form>