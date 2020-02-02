<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

/*
*
* @copyright Copyright (C) 2007 - 2010 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* THIS FILE RENDERS THE SELECT THE CHECKBOX AND THE SINGLE SHIPPING ADDRESS
*
* TO FORMAT EDITING OF THE ADDRESS USE list_user_fields_shipping.tpl.php WHICH IS USED FOR UNLOGGED AS WELL
*
* This file is loaded from \components\com_onepage\helpers\loader.php function getUserInfoST
*
* This file is used by unlogged or by logged when single shipping address is enabled
*/
// MISSING LANGUAGE STRINGS
$CLICK_HERE_TO_ADD_SHIPPING_ADDRESS = OPCLang::_('COM_ONEPAGE_CLICK_HERE_TO_ADD_SHIPPING_ADDRESS'); //Verzendadres wijkt van contact adres af

?>

<div id="ship_to_wrapper">
		<?php if (empty($only_one_shipping_address_hidden)) { ?>
								<input type="radio" id="sachone2" name="sa" value="" <?php if (empty($op_shipto_opened)) echo ' checked="checked" '; ?> onkeypress="showSA(document.getElementById('sachone'), 'idsa');" onclick="javascript: showSA(document.getElementById('sachone'), 'idsa');"/>
								<label for="sachone2"><?php echo OPCLang::_('COM_VIRTUEMART_ACC_BILL_DEF'); ?></label>
								<br />
								<input type="radio" <?php if (!empty($op_shipto_opened)) echo ' checked="checked" '; ?> id="sachone" name="sa" value="adresaina" onkeypress="showSA(this, 'idsa');" onclick="javascript: showSA(this, 'idsa');"/>
								<label for="sachone">
								<?php echo OPCLang::_('COM_VIRTUEMART_USER_FORM_ADD_SHIPTO_LBL');  ?>
								</label>
								<?php } else { ?>
								<input type="hidden" id="sachone" name="sa" value="adresaina" onkeypress="showSA(sa, 'idsa');" onclick="javascript: showSA(sa, 'idsa');" autocomplete="off" />								
								<?php } ?>
								
								
	<div id="idsa" style="<?php if (empty($op_shipto_opened)) echo ' display: none; '; ?> ">
								
	
								  <?php echo $op_shipto; // will list shipping user fields from ps_userfield::listUserFields with modification of ids and javascripts ?>
								
								<div id="new_shipping_msg">&nbsp;</div>
	</div>
  
</div>