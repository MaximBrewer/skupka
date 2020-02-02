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
				
				
				
				
				
				<select rel="<?php echo $product->cart_item_id.'|'.md5($product->cart_item_id); ;
?>" name="quantity" class="quantity_select opcquantity"  id="quantity_for_<?php echo md5($product->cart_item_id); ?>" onchange="return Onepage.updateProduct(this);">
				<?php
				//you can print your own options, or use the pre-generated: for ($i=$step; $i<=$max; $i=$i+$step ) 
				echo $options; 
				
				?>
				</select>
				
				
				
				
				
				
				
				
			 