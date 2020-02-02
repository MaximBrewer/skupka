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
<div class="arrowup"><div class="opc_arrow_up">&nbsp;</div></div>
<div class="opc_heading" ><button name="stbutton" onkeypress="return showSA2(this, 'idsa');" class="<?php 
if (empty($op_shipto_opened)) echo 'button_checkbox_uned'; 
else echo 'button_checkbox_ed'; 
?>" onclick="javascript: return showSA2(this, 'idsa');" autocomplete="off" >
<div>&nbsp;</div>
<span class="opc_title"><?php echo $CLICK_HERE_TO_ADD_SHIPPING_ADDRESS ?></span></button>
<?php 
// echo OPCLang::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL'); 
// OR echo OPCLang::_('COM_VIRTUEMART_USER_FORM_ADD_SHIPTO_LBL');  
?></div>
  <div class="opc_inside" id="idsa" style="<?php if (empty($op_shipto_opened)) echo 'display: none;' ?>">
    <div>
	<div style="display: none;"><input type="checkbox" <?php if (!empty($op_shipto_opened)) echo 'checked="checked" '; ?>  name="sa" value="adresaina" id="sachone" autocomplete="off" />Address is different</div>
								
								
								<div>
								  <?php echo $op_shipto; // will list shipping user fields from ps_userfield::listUserFields with modification of ids and javascripts ?>
								</div>
								<div id="new_shipping_msg">&nbsp;</div>
	</div>
  </div>
</div>

