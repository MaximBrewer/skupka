<?php
/*------------------------------------
* -Netbase- Advanced Virtuemart Invoices for Virtuemart
* Author    CMSMart Team
* Copyright (C) 2012 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support:  Forum - http://bloorum.com/forums
-----------------------------------------------------*/

defined('_JEXEC') or die('Restrict Access');
jimport('joomla.filter.output');
$editor =  JFactory::getEditor();

?>
<form action="index.php" name="adminForm" id="adminForm" method="post" >
<div class="col100">
	<fieldset class="adminform">
	<legend><?php echo JText::_('COM_VMINVOICE_BANK_DETAILS'); ?></legend>	

	<table class="admintable">
		<tr>
			<td width="100" align="right" class="key">
				<label for="name">
					<?php echo JText::_('COM_VMINVOICE_BANK_NAME'); ?>:
				</label>
			</td>
			<td>
            <input class="text_area" type="text" name="bank_name" id="bank_name" size="40" maxlength="250"
                value="<?php echo $this->fields->bank_name;?>" />	
			</td>
            <td>
            <?php echo JText::_('COM_VMINVOICE_SHOW'); ?> <input type="radio" style="float:none"  name="show_bank_name" value="1" 
            <?php if($this->fields->show_bank_name == 1) {?>  checked="checked" <?php }?> />
            <?php echo JText::_('COM_VMINVOICE_HIDE'); ?> <input type="radio" style="float:none"  name="show_bank_name" value="0" 
            <?php if($this->fields->show_bank_name == 0) {?>  checked="checked" <?php }?> />
			</td>
		</tr>
        <tr>
			<td width="100" align="right" class="key">
				<label for="name">
					<?php echo JText::_('COM_VMINVOICE_ACCOUNT_NUMBER'); ?> :
				</label>
			</td>
			<td>
          <input class="text_area" type="text" name="account_nr" id="account_nr" size="40" maxlength="250"
                value="<?php echo $this->fields->account_nr;?>" />
			</td>
			<td>
            <?php echo JText::_('COM_VMINVOICE_SHOW'); ?> <input type="radio" style="float:none"  name="show_account_nr" value="1" 
            <?php if($this->fields->show_account_nr == 1) {?>  checked="checked" <?php }?> />
            <?php echo JText::_('COM_VMINVOICE_HIDE'); ?> <input type="radio" style="float:none"  name="show_account_nr" value="0" 
            <?php if($this->fields->show_account_nr == 0) {?>  checked="checked" <?php }?> />
			</td>
		</tr>    
         <tr>
			<td width="100" align="right" class="key">
				<label for="name">
					<?php echo JText::_('COM_VMINVOICE_BANK_CODE'); ?> :
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="bank_code_no" id="bank_code_no" size="40" maxlength="250"
                value="<?php echo $this->fields->bank_code_no;?>" />                
			</td>
            <td>
             <?php echo JText::_('COM_VMINVOICE_SHOW'); ?> <input type="radio" style="float:none"  name="show_bank_code_no" value="1" 
             <?php if($this->fields->show_bank_code_no == 1) {?>  checked="checked" <?php }?> />
             <?php echo JText::_('COM_VMINVOICE_HIDE'); ?> <input type="radio" style="float:none"  name="show_bank_code_no" value="0" 
             <?php if($this->fields->show_bank_code_no == 0) {?>  checked="checked" <?php }?> />
			</td>
		</tr>       
        <tr>
			<td width="100" align="right" class="key">
				<label for="name">
					<?php echo JText::_('COM_VMINVOICE_BIC_SWIFT'); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="bic_swift" id="bic_swift" size="40" maxlength="250"
                value="<?php echo $this->fields->bic_swift;?>" />                
			</td>
            <td>
            <?php echo JText::_('COM_VMINVOICE_SHOW'); ?> <input type="radio" style="float:none"  name="show_bic_swift" value="1" 
            <?php if($this->fields->show_bic_swift == 1) {?>  checked="checked" <?php }?> />
            <?php echo JText::_('COM_VMINVOICE_HIDE'); ?> <input type="radio" style="float:none"  name="show_bic_swift" value="0" 
            <?php if($this->fields->show_bic_swift == 0) {?>  checked="checked" <?php }?> />
			</td>
		</tr>       
        <tr>
			<td width="100" align="right" class="key">
				<label for="name">
					<?php echo JText::_('COM_VMINVOICE_IBAN'); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="iban" id="iban" size="40" maxlength="250"
                value="<?php echo $this->fields->iban;?>" />                
			</td>
            <td>
             <?php echo JText::_('COM_VMINVOICE_SHOW'); ?> <input type="radio" style="float:none"  name="show_iban" value="1" 
             <?php if($this->fields->show_iban == 1) {?>  checked="checked" <?php }?> />
             <?php echo JText::_('COM_VMINVOICE_HIDE'); ?> <input type="radio" style="float:none"  name="show_iban" value="0" 
             <?php if($this->fields->show_iban == 0) {?>  checked="checked" <?php }?> />
			</td>
		</tr>
	</table>

</fieldset>
</div>

<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_VMINVOICE_COMPANY_DETAILS'); ?></legend>	
        	<table class="admintable">
		<tr>
			<td width="100" align="right" class="key">
				<label for="name">
					<?php echo JText::_('COM_VMINVOICE_TAX_NUMBER'); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="tax_number" id="tax_number" size="40" maxlength="250" 
                value="<?php echo $this->fields->tax_number;?>" />
			</td>		
           <td>
            <?php echo JText::_('COM_VMINVOICE_SHOW'); ?> <input type="radio" style="float:none"  name="show_tax_number" value="1" 
            <?php if($this->fields->show_tax_number == 1) {?>  checked="checked" <?php }?> />
            <?php echo JText::_('COM_VMINVOICE_HIDE'); ?> <input type="radio" style="float:none"  name="show_tax_number" value="0" 
            <?php if($this->fields->show_tax_number == 0) {?>  checked="checked" <?php }?> />
			</td>
		</tr>
        <tr>
			<td width="100" align="right" class="key">
				<label for="name">
					<?php echo JText::_('COM_VMINVOICE_VAT_ID'); ?> :
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="vat_id" id="vat_id" size="40" maxlength="250" value="<?php echo $this->fields->vat_id;?>" />
			</td>            
            <td>
            <?php echo JText::_('COM_VMINVOICE_SHOW'); ?> <input type="radio" style="float:none"  name="show_vat_id" value="1" 
            <?php if($this->fields->show_vat_id == 1) {?>  checked="checked" <?php }?> />
            <?php echo JText::_('COM_VMINVOICE_HIDE'); ?> <input type="radio" style="float:none"  name="show_vat_id" value="0" 
            <?php if($this->fields->show_vat_id == 0) {?>  checked="checked" <?php }?> />
			</td>
		</tr>
        <tr>
			<td width="100" align="right" class="key">
				<label for="name">
					<?php echo JText::_('COM_VMINVOICE_REGISTRATION_COURT'); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="registration_court" id="registration_court" size="40" maxlength="250" 
                value="<?php echo $this->fields->registration_court;?>" />
			</td>          
           <td>
            <?php echo JText::_('COM_VMINVOICE_SHOW'); ?> <input type="radio" style="float:none"  name="show_registration_court" value="1" 
            <?php if($this->fields->show_registration_court == 1) {?>  checked="checked" <?php }?> />
            <?php echo JText::_('COM_VMINVOICE_HIDE'); ?> <input type="radio" style="float:none"  name="show_registration_court" value="0" 
            <?php if($this->fields->show_registration_court == 0) {?>  checked="checked" <?php }?> />
			</td>
		</tr>
        <tr>
			<td width="100" align="right" class="key">
				<label for="name">
					<?php echo JText::_('COM_VMINVOICE_PHONE'); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="phone" id="phone" size="40" maxlength="250" 
                value="<?php echo $this->fields->phone;;?>" />
			</td>
			<td>
            <?php echo JText::_('COM_VMINVOICE_SHOW'); ?> <input type="radio" style="float:none"  name="show_phone" value="1" 
            <?php if($this->fields->show_phone == 1) {?>  checked="checked" <?php }?> />
            <?php echo JText::_('COM_VMINVOICE_HIDE'); ?> <input type="radio" style="float:none"  name="show_phone" value="0" 
             <?php if($this->fields->show_phone == 0) {?>  checked="checked" <?php }?> />
			</td>
		</tr>
        <tr>
			<td width="100" align="right" class="key">
				<label for="name">
					<?php echo JText::_('COM_VMINVOICE_MAIL'); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="email" id="email" size="40" maxlength="250" 
                value="<?php echo $this->fields->email;?>" />
			</td>
            <td>
             <?php echo JText::_('COM_VMINVOICE_SHOW'); ?> <input type="radio" style="float:none"  name="show_email" value="1" 
             <?php if($this->fields->show_email == 1) {?>  checked="checked" <?php }?> />
             <?php echo JText::_('COM_VMINVOICE_HIDE'); ?> <input type="radio" style="float:none"  name="show_email" value="0" 
             <?php if($this->fields->show_email == 0) {?>  checked="checked" <?php }?> />
			</td>
		</tr>
         <tr>
			<td width="100" align="right" class="key">
				<label for="name">
					<?php echo JText::_('COM_VMINVOICE_WEB_URL'); ?>:
				</label>
			</td>
			<td>
            <input class="text_area" type="text" name="web_url" id="web_url" size="40" maxlength="250" 
                value="<?php echo $this->fields->web_url;?>" />					
			</td>
            <td>
             <?php echo JText::_('COM_VMINVOICE_SHOW'); ?> <input type="radio" style="float:none"  name="show_web_url" value="1" 
             <?php if($this->fields->show_web_url == 1) {?>  checked="checked" <?php }?> />
             <?php echo JText::_('COM_VMINVOICE_HIDE'); ?> <input type="radio" style="float:none"  name="show_web_url" value="0" 
             <?php if($this->fields->show_web_url == 0) {?>  checked="checked" <?php }?> />
			</td>
		</tr>
        </table>
        <table class="admintable">
         <tr>
			<td width="100" align="right" class="key">
				<label for="name">
					<?php echo JText::_('COM_VMINVOICE_PDF_START_NOTE'); ?>:
				</label>
			</td>
			<td>
                <?php echo $editor->display('note_start', $this->fields->note_start, '150%', '250', '40', '10' ) ;?>		
			</td>          
		</tr>
		<tr>
			<td width="100" align="right" class="key">
				<label for="name">
					<?php echo JText::_('COM_VMINVOICE_PDF_END_NOTE'); ?>:
				</label>
			</td>
			<td>
				<?php echo $editor->display('note_end', $this->fields->note_end, '150%', '250', '40', '10' ) ;?>
			</td>
		</tr>       
	</table>   

</fieldset>
</div>

<div class="clr"></div>

<input type="hidden" name="option" value="com_vminvoice" />
<input type="hidden" name="id" value="1" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="fields" />

</form>