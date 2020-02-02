<?php 
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
*
* @version $Id:couponField.tpl.php 431 2006-10-17 21:55:46 +0200 (Di, 17 Okt 2006) soeren_nb $
* @package VirtueMart
* @subpackage themes
* @copyright Copyright (C) 2008 soeren - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
* @author Erich Vinson
* http://virtuemart.net
*/



?>


<div class="vmsectiontableentry1">
<div id="couponcode_field_input" >

<div id="couponcode_field_ajax" style="">
<?php
// If you have a coupon code, please enter it here:
?>  
<div>
<div class="coupon_input_wrapper" style="">
    <input type="text" name="coupon_code" autocomplete="off" id="coupon_code" size="20" class="coupon_input_ajax" alt="<?php echo $coupon_text ?>" value="<?php echo $coupon_text; ?>" onblur="if(this.value=='') this.value='<?php echo $coupon_text; ?>';" onfocus="if(this.value=='<?php echo $coupon_text; ?>') this.value='';" />
</div>
<div style="float: left; clear:right;">
	<input class="buttonopc buttonopc_ajax" id="submit_coupon_button" type="button" value="<?php echo OPCLang::_('COM_VIRTUEMART_SAVE'); ?>" />
</div>
</div>
</div>	
		
</div> 
<div id="couponcode_field_txt_discount" > 
 <span <?php if (empty($coupon_display)) echo ' style="display: none;" '; ?> id="tt_order_discount_after_div_basket">&nbsp;
  <span id="tt_order_discount_after_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_COUPON_DISCOUNT') ?>
  </span>
 </span>
</div> 
	
<div id="couponcode_field_discount">
	<span>
	<span id="tt_order_discount_after_basket"><?php echo $coupon_display ?>
	</span>
	</span>
</div>


</div>
