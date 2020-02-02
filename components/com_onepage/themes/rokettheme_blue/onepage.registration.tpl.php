<?php
/**
 * Legacy template loader for One Page Checkout 2 for VirtueMart 2
 *
 * @package One Page Checkout for VirtueMart 2
 * @subpackage opc
 * @author stAn
 * @author RuposTel s.r.o.
 * @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * One Page checkout is free software released under GNU/GPL and uses some code from VirtueMart
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 */
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

?><h1><?php echo JText::_('COM_USERS_REGISTRATION'); ?></h1><div id="opc_unlogged_wrapper2 "><?php

?><form action="<?php echo $action_url; ?>" method="post" name="adminForm" class="form-ivalidate" autocomplete="off">
   
   
<!-- user registration and fields -->

<div id="register_box" style="width: 100%; clear: both;" <?php
	
	if (empty($registration_html))  { echo 'style="display:none"';}
		else if (empty($has_guest_tab) || (VM_REGISTRATION_TYPE != 'OPTIONAL_REGISTRATION' || (!empty($no_login_in_template)))) echo ' style="width:50%; " '; ?>>
	<div id="register_head" class="bandBoxStyle"><?php echo OPCLang::_('COM_VIRTUEMART_REGISTER') ?></div>
	<div id="register_container">
	<span><?php echo OPCLang::_('COM_ONEPAGE_REGISTER_TEXT') ?></span>
	<?php	echo $registration_html; ?>
	<div class="formField" id="registerbtnfield" >
	</div>
	</div>
</div>

<div id="billTo_box" style="width: 100%; clear: both;">
	<div id="billTo_head" class = "bandBoxStyle"><?php echo OPCLang::_('COM_VIRTUEMART_USER_FORM_BILLTO_LBL'); ?></div>
	<div id="billTo_container"><?php echo $op_userfields; // they are fetched from ps_userfield::listUserFields ?>
	</div>
</div>
<div id="comment_container"><div id="rbsubmit">
<div id="onepage_info_above_button">

<div  class="acy_wrapper " >
	
<div class="acy_checkbox_wrapper" >
<input value="1" type="checkbox" id="agreed_field" name="tosAccepted" <?php if (!empty($agree_checked)) echo ' checked="checked" '; ?> class="terms-of-service2"  required="required" autocomplete="off" />
</div>
<div class="acy_label_wrapper">
<label for="agreed_field" class="terms-of-service2">
					<?php echo OPCLang::_('COM_VIRTUEMART_I_AGREE_TO_TOS'); 
					if (!empty($tos_link))
					{
					JHTMLOPC::_('behavior.modal', 'a.opcmodal'); 
					
					?><a target="_blank" rel="{handler: 'iframe', size: {x: 500, y: 400}}" class="opcmodal" href="<?php echo $tos_link; ?>" onclick="javascript: return op_openlink(this); " >(<?php echo OPCLang::_('COM_VIRTUEMART_CART_TOS').' *'; ?>)</a><?php } ?></label>
</div>				
</div>				

   <?php echo $italian_checkbox; ?>
   <?php echo $captcha; ?>
   <?php $txt = JText::_('COM_ONEPAGE_CREATEACCOUNT'); ?>
 <div style="float: left; clear: both;">
	<input id="confirmbtn_button" type="submit" class="submitbtn bandBoxRedStyle" value="<?php echo str_replace('"', '\"', $txt); ?>" autocomplete="off" <?php echo $op_onclick ?>   />
 </div>
</div></div></div>
 
 </form>
 </div>