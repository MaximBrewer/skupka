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

<div id="couponcode_field" style="float: right; width: 100%; ">
<?php
// If you have a coupon code, please enter it here:
?>  
	
	<div class="coupon_wrapper">
	<div class="coupon_in">
    <input type="text" name="coupon_code" autocomplete="off" id="coupon_code" size="20"  class="coupon_input" alt="<?php echo $this->coupon_text ?>" value="<?php echo $this->coupon_text; ?>" onblur="if(this.value=='') this.value='<?php echo $this->coupon_text; ?>';" onfocus="if(this.value=='<?php echo $this->coupon_text; ?>') this.value='';" />
	</div>
    <span class="detailsbutton_opc">
	<input class="buttonopc" id="submit_coupon_button" type="submit" value="<?php echo JText::_('COM_VIRTUEMART_SAVE'); ?>" onclick="return Onepage.setCouponAjax(this);"  />
    
    </span>

	</div>
	
		
</div>

	

