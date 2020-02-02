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
*/
?>
<div id="agreed_div" class="opc_bottom_checkboxwrap">
	<div class="checkbox_5">
	<input type="checkbox" id="agreed_field" name="agreed" value="1" class="inputbox terms-of-service" <?php if (!empty($agree_checked)) echo ' checked="checked" '; ?>  required="required" autocomplete="off" />
	
</div>
					<div class="checkbox_95" >
				<label for="agreed_field" class="opc_bottom_labels"><span class="checkbox_label_opc"><?php echo OPCLang::_('COM_VIRTUEMART_I_AGREE_TO_TOS'); ?></span></label>
				<a target="_blank" href="<?php echo $tos_link; ?>" title="<?php  echo JText::_('COM_VIRTUEMART_I_AGREE_TO_TOS'); ?>" onclick="javascript: return op_openlink(this); ">
				 (<?php echo JText::_('COM_VIRTUEMART_CART_TOS'); ?>)
				</a>
	<strong>* </strong> 
	</div>
</div>