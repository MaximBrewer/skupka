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
<div class="update_delete_div" style="position: relative; min-width: 150px; float: left; width: 150px; min-height: 30px; display: block;">

<div style="position:absolute; top:0; min-width: 30px; display:inline-block; width: 30px; height:30px; ">

<a class="deletebtn" style="width: 20px; height: 20px;" title="<?php echo OPCLang::_('COM_VIRTUEMART_CART_DELETE'); 
?>" href="#" rel="<?php echo $product->cart_item_id;
?>">&nbsp;</a>

</div>
<div style="position:absolute; top:0; height:30px;  left:30px; min-width: 70px;  max-width: 70px; width:100px; display:inline-block;">
				
<input style="min-width: 70px; max-width: 70px;" type="text" title="<?php echo OPCLang::_('COM_VIRTUEMART_CART_UPDATE'); ?>" class="inputbox" size="3" name="quantity" id="quantity_for_<?php echo md5($product->cart_item_id); ?>" value="<?php echo $product->quantity; ?>" style="margin: 0;" />
				
				
</div>				
<div style="position:absolute; height:30px;  top:0;  left:130px; min-width: 30px; display:block;  width: 30px; ">				
				
				<a style="width: 20px; height: 20px;" class="updatebtn" title="<?php echo OPCLang::_('COM_VIRTUEMART_CART_UPDATE'); 
?>" href="#" rel="<?php echo $product->cart_item_id.'|'.md5($product->cart_item_id); ;
?>">&nbsp;</a>
				
</div>				
</div>