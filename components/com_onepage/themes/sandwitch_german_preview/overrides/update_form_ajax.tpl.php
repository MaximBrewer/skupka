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
<div class="qu_center">
  <div class="qu_left">
    <div class="updatebtn_plus ">
      <a class="updatebtn_plus" title="<?php echo OPCLang::_('COM_VIRTUEMART_CART_UPDATE'); 
?>" href="#" rel="<?php echo $product->cart_item_id.'|'.md5($product->cart_item_id); ;?>"  onclick="return Onepage.plusQuantity(this);"></a></div>
    <div class="updatebtn_minus">		
      <a class="updatebtn_minus" title="<?php echo OPCLang::_('COM_VIRTUEMART_CART_UPDATE'); 
?>" href="#" rel="<?php echo $product->cart_item_id.'|'.md5($product->cart_item_id); ;
?>" onclick="return Onepage.minusQuantity(this);"></a>
      </div>
  </div>
  
  <div class="opcquantity_wrapper">
    <input type="text" 
   rel="<?php echo $product->cart_item_id.'|'.md5($product->cart_item_id); ;?>" 
   title="<?php echo OPCLang::_('COM_VIRTUEMART_CART_UPDATE'); ?>" 
   class="inputbox opcquantity" size="4" name="quantity" id="quantity_for_<?php echo md5($product->cart_item_id); ?>"  onchange="Onepage.updateProduct(this, this.value);" value="<?php echo $product->quantity; ?>"  />
  </div>
  
  
  
  
  
  <div class="updatebtn_delete">		
    <a class="updatebtn_delete" title="<?php echo OPCLang::_('COM_VIRTUEMART_CART_UPDATE'); 
?>" href="#" rel="<?php echo $product->cart_item_id.'|'.md5($product->cart_item_id); ;
?>" onclick="return Onepage.deleteProduct(this);"><span class="updatebtn_delete">&#10006;</span></a>			
  </div>
</div>
				
				
				
				
			 