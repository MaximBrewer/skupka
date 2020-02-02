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



<div id="couponcode_field_ajax" style="">
  <div data-role="fieldcontain">
			 <label for="coupon_code"><?php echo $this->coupon_text; ?></label>
			 <?php
			 if (!empty($this->coupon_text)) {
			$name = str_replace('"', '\"', $this->coupon_text); 
			
			}
			 ?>
			 <input type="text" name="coupon_code" autocomplete="off" id="coupon_code" placeholder="<?php echo $name; ?>" class="inputbox" />
			 
  </div>
  <div data-role="fieldcontain">
   <input type="button" value="<?php echo OPCLang::_('COM_VIRTUEMART_SAVE'); ?>" class="coupon_button  ui-btn ui-shadow ui-corner-all"  onclick="return Onepage.setCouponAjax(this);" data-role="button" data-mini="true"  data-icon="plus"/>
  </div>
		
</div> 




