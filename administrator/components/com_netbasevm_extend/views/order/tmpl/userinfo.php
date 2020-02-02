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

defined('_JEXEC') or ('Restrict Access');

/* @var $this VMInvoiceViewOrder */

JFilterOutput::objectHTMLSafe($this->billingData);
JFilterOutput::objectHTMLSafe($this->shippingData);

//TODO: remove all scripts!!!
//http://forum.virtuemart.net/index.php?topic=59255.15
function sanitiseUserInput($input)
{
	$input = preg_replace('#<\s*script\b[^>]*>.*<\s*\/\s*script\s*>#isU','',$input); //remove script tags

	if (preg_match('/(<\s*input.*type\s*=\s*"\s*text\s*".*)>(?!.*<\s*input)$/iUsx',$input,$matchesInput)){

		if (preg_match('/class\s*=\s*".*"/iUsx',$matchesInput[1])) //has class defined, append fullWidth
			$modifInput = preg_replace('/class\s*=\s*"(.*)"/iUsx','class="$1 fullWidth"',$matchesInput[1]);
		else
			$modifInput =  $matchesInputs[1].' class="fullWidth"'; //add new class
			
		$input = preg_replace('#'.preg_quote($matchesInput[1]).'#is', $modifInput, $input);
	}

	//if it contains editor, keep only raw text area (editors makes JS bugs on reloading page)
	if (preg_match('/<!--\s*Start Editor\s*-->.*(<\s*textarea.*>.*<\s*\/\s*textarea\s*>)/isU',$input,$match))
		$input = preg_replace('/(<textarea.*)class\s*=\s*(?:"|\').*(?:"|\')(.*>)/isU','$1 $2',$match[1]);

	return $input;
}

?>

<?php if (! $this->userajax) { ?>
<div id="userInfo">
	<?php } ?>
	<fieldset class="adminform">
		<legend class="user_info_form"
			style="border: none !important; color: #315b8c;">
			<?php echo JText::_('COM_NETBASEVM_EXTEND_CUSTOMER_INFORMATION'); ?>
		</legend>

		<input type="hidden" name="user_id" id="user_id"
			value="<?php echo $this->billingData->user_id; ?>" autocomplete="off" />
		<input type="hidden" name="update_userinfo" id="update_userinfo"
			value="0" autocomplete="off" /> <input type="hidden"
			name="B_user_info_id" id="B_user_info_id"
			value="<?php if (isset($this->billingData->user_info_id)) echo $this->billingData->user_info_id; ?>"
			autocomplete="off" /> <input type="hidden" name="S_user_info_id"
			id="S_user_info_id"
			value="<?php  if (isset($this->shippingData->user_info_id)) echo $this->shippingData->user_info_id; ?>"
			autocomplete="off" />


		<table class="admintable registration" style="width: 100% !important;">
			<tr>
				<td width="28%" align="left"><span class="customerid "
					title="<?php echo JText::_('COM_NETBASEVM_EXTEND_CUSTOMER_ID_DESC'); ?>"><?php echo JText::_('COM_NETBASEVM_EXTEND_CUSTOMER_ID'); ?>
				</span>
				</td>
				<td align="left"><?php
				if (empty($this->billingData->user_id))
					echo '<span class="" title="'.$this->escape(JText::_('COM_NETBASEVM_EXTEND_NEW_CUSTOMER_ADDITIONAL')).'"><b>'.JText::_('COM_NETBASEVM_EXTEND_NEW_CUSTOMER_DESC').'</b></span>';
				else {

  		if (!empty($this->billingData->user_info_id))
  		{
  			if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
  				$url = 'index.php?option=com_virtuemart&view=user&task=edit&cid[]='.$this->billingData->user_id.'&virtuemart_user_id[]='.$this->billingData->user_id;
  			else
  				$url = 'index.php?option=com_virtuemart&page=admin.user_form&user_id='.$this->billingData->user_id;

  			echo '<b><a href="'.JRoute::_($url).'" target="_blank">'.$this->billingData->user_id.'</a></b>';
  			echo ': '.$this->billingData->first_name.' '.$this->billingData->last_name;
  		}
  		else
  		{
  			if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
  				$url = 'index.php?option=com_users&task=user.edit&id='.$this->billingData->user_id;
  			else
  				$url = 'index.php?option=com_users&view=user&task=edit&cid[]='.$this->billingData->user_id;

  			echo '<b><a href="'.JRoute::_($url).'" target="_blank">'.JText::_('COM_NETBASEVM_EXTEND_JOOMLA_USER').' '.$this->billingData->user_id.'</a></b>';
  			$user = JFactory::getUser($this->billingData->user_id);
  			echo ': '.$user->name;
  			echo ' - <b class="" title="'.$this->escape(JText::_('COM_NETBASEVM_EXTEND_NEW_SHOPPER_ADDITIONAL')).'">'.JText::_('COM_NETBASEVM_EXTEND_NEW_SHOPPER_DESC').'.</b>';
  		}
  	}


  	?>
				</td>
			</tr>
			<?php  if (empty($this->billingData->user_id) || empty($this->billingData->user_info_id)) { ?>
			<tr>
				<td align="left"><?php echo JText::_('COM_NETBASEVM_EXTEND_NEW_SHOPPER_GROUP') ?>
				</td>
				<td>
				<select id="shopper_group" class="chzn-done" name="shopper_group">
				<?php 
               // echo '<pre>'.print_r(invoiceGetter::getShopperGroups() ,1).'</pre>';
				foreach(invoiceGetter::getShopperGroups() as $value){
					?>
						<option value="<?php echo $value->id ?>"><?php echo JText::_($value->name) ?></option>
					<?php
				}
              
				//echo JHTML::_('select.genericlist',invoiceGetter::getShopperGroups(), 'shopper_group','', 'id', JText::_('name'), InvoiceGetter::getDefaultShopperGroup());
				?></select></td>
			</tr>
			<?php } ?>
			<tr>
				<td align="left"><span class="registration "
					title="<?php echo $this->escape(JText::_('COM_NETBASEVM_EXTEND_REGISTRATION_INFO_TITLE')); ?>::<?php echo $this->escape(JText::_('COM_NETBASEVM_EXTEND_REGISTRATION_INFO_DESC')); ?>"><?php echo JText::_('COM_NETBASEVM_EXTEND_EXISTING_NEW_CUSTOMER'); ?>
				</span>
				
				<td><input type="text" id="user" name="user" class="fullWidth"
					autocomplete="off"
					onkeyup="generateWhisper(this,event,'<?php echo JURI::base(); ?>');"
					onkeydown="moveWhisper(this,event);"
					onclick="generateWhisper(this,event,'<?php echo JURI::base(); ?>');" />
					<div class="clr"></div>
					<div id="userwhisper"></div></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><strong><?php echo $this->escape(JText::_('COM_NETBASEVM_EXTEND_REGISTRATION_INFO_TITLE')); ?>
				</strong><br /> <?php //echo $this->escape(JText::_('COM_NETBASEVM_EXTEND_REGISTRATION_INFO_DESC')); ?>
				</td>
			</tr>
		</table>

	</fieldset>


	<table class="table_user_info">
		<tr>
			<td class="table_user_info_h2_ship">
				<h2 class="billing_shipping">
					<?php echo JText::_('COM_NETBASEVM_EXTEND_SHIPPING_ADDRESS');?>
				</h2>
			</td>
			<td class="table_user_info_h2_bill">
				<h2 class="billing_shipping">
					<?php echo JText::_('COM_NETBASEVM_EXTEND_BILLING_ADDRESS');?>
				</h2>
			</td>
		</tr>
		<tr>
			
			<td class="table_user_info_ship_content" id="billing_address">
			<span class="muiten">a</span>
				<fieldset class="adminform" style="width: 530px;">
					<legend>
						<?php echo JText::_('COM_NETBASEVM_EXTEND_CUSTOMER_NAME') ?>
					</legend>
					<table class="admintable" cellspacing="0" cellpadding="1"
						border="0">
						<tbody>
							<tr>
								<td width="20%" align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_FIRST_NAME'); ?>
									<?php if (empty($this->billingData->user_id)) echo '&nbsp;<sup style="color:red;">*</sup>';?>
								</td>
								<td width="30%" align="left"><input id="B_first_name"
									type="text" class="fullWidth" name="B_first_name"
									value="<?php echo $this->billingData->first_name; ?>" />
								</td>
								<td width="20%" align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_LAST_NAME'); ?>
									<?php if (empty($this->billingData->user_id)) echo '&nbsp;<sup style="color:red;">*</sup>';?>
								</td>
								<td width="30%" align="left"><input id="B_last_name" type="text"
									class="fullWidth" name="B_last_name"
									value="<?php echo $this->billingData->last_name; ?>" />
								</td>
							</tr>
							<tr>
								<td align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_TITLE'); ?>
								</td>
								<td align="left"><input id="B_title" type="text"
									class="fullWidth" name="B_title"
									value="<?php echo $this->billingData->title; ?>" />
								</td>
								<td align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_MIDDLE_NAME'); ?>
								</td>
								<td align="left"><input id="B_middle_name" type="text"
									class="fullWidth" name="B_middle_name"
									value="<?php echo $this->billingData->middle_name; ?>" />
								</td>
							</tr>
							<tr>
								<td width="20%" align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_COMPANY_NAME'); ?>
								</td>
								<td width="80%" colspan="3" align="left"><input type="text"
									 class="fullWidth"
									name="B_company"
									value="<?php echo $this->billingData->company; ?>" />
								</td>
							</tr>
						</tbody>
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('COM_NETBASEVM_EXTEND_ADDRESS') ?>
					</legend>
					<table class="admintable" cellspacing="0" cellpadding="1"
						border="0">
						<tbody>
							<tr>
								<td width="20%" align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_ADDRESS_1'); ?>
								</td>
								<td width="30%" align="left"><input type="text"
									class="fullWidth" name="B_address_1"
									value="<?php echo $this->billingData->address_1; ?>" />
								</td>
								<td width="20%" align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_ADDRESS_2'); ?>
								</td>
								<td width="30%" align="left"><input type="text"
									class="fullWidth" name="B_address_2"
									value="<?php echo $this->billingData->address_2; ?>" />
								</td>
							</tr>
							<tr>
								<td align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_CITY'); ?>
								</td>
								<td align="left"><input type="text" class="fullWidth"
									name="B_city" value="<?php echo $this->billingData->city; ?>" />
								</td>
								<td align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_ZIP_POSTAL_CODE'); ?>
								</td>
								<td align="left"><input type="text" class="fullWidth"
									name="B_zip" value="<?php echo $this->billingData->zip; ?>" />
								</td>
							</tr>
							<tr>
								<td align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_COUNTRY'); ?>
								</td>
								<td align="left"><?php echo JHTML::_('select.genericlist',$this->countries, 'B_country', array('class' => 'fullWidth','onchange' => 'populateStates(\'B_country\',\'B_state\')','onkeyup' => 'populateStates(\'B_country\',\'B_state\')'), 'id', 'name', $this->billingData->country); ?>
								</td>
								<td align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_STATE_PROVINCE_REGION'); ?>
								</td>
								<td align="left"><?php echo JHTML::_('select.genericlist',$this->b_states, 'B_state', array('class' => 'fullWidth'), 'id', 'name', $this->billingData->state); ?>
								</td>
							</tr>
						</tbody>
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('COM_NETBASEVM_EXTEND_CONTACTS') ?>
					</legend>
					<table class="admintable" cellspacing="0" cellpadding="1"
						border="0">
						<tbody>
							<tr>
								<td width="20%" align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_MAIL'); ?>
									<?php if (empty($this->billingData->user_id)) { 
										echo '&nbsp;<sup style="color:red;">*</sup>';
									} ?>
								</td>
								<td width="30%" align="left"><input type="text"
									class="fullWidth" name="B_email"
									value="<?php echo $this->billingData->email; ?>" />
								</td>
								<td width="20%" align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_PHONE'); ?>
								</td>
								<td width="30%" align="left"><input type="text"
									class="fullWidth" name="B_phone_1"
									value="<?php echo $this->billingData->phone_1; ?>" />
								</td>
							</tr>
							<tr>
								<td align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_MOBILE_PHONE'); ?>
								</td>
								<td align="left"><input type="text" class="fullWidth"
									name="B_phone_2"
									value="<?php echo $this->billingData->phone_2; ?>" />
								</td>
								<td align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_FAX'); ?>
								</td>
								<td align="left"><input type="text" class="fullWidth"
									name="B_fax" value="<?php echo $this->billingData->fax; ?>" />
								</td>
							</tr>

							<?php 
							//display custom user fields
							if (isset($this->billingData->userFields)) {
          						  $countUF = 0;
          						  foreach ($this->billingData->userFields as $userField)
          						  {


          						  	$countUF++;
          						  	if ($countUF == 1) {
                            ?>

							<tr>
								<td colspan="4">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="4" align="left" class="title"><h3>
										<?php echo JText::_('COM_NETBASEVM_EXTEND_ADDITIONAL_FIELDS') ?>
									</h3>
								</td>
							</tr>

							<?php
              						}
              						//display label (with tip)
              						echo '<tr><td align="right">';
              						$titleTrans = InvoiceGetter::getVMTranslation($userField['title']);
              						if (isset($userField['desc']) && $userField['desc']!=$userField['title'])
              							echo '<span class="" title="'.$titleTrans.'::'.InvoiceGetter::getVMTranslation($userField['desc']).'">'.$titleTrans.'</span></td>';
              						else
              							echo $titleTrans.'</td>';

              						//display inputs
              						echo '<td align="left">'.sanitiseUserInput($userField['input']).'</td></tr>';
          						  }
          						}
          						?>
						</tbody>
					</table>
				</fieldset>
			</td>
			<td class="table_user_info_bill_content">
				<table class="admintable" cellspacing="0" cellpadding="1" border="0"
					id="shipping_address">
					<tbody>
						<tr>
							<td align="left"><label
								style="font-weight: bold; vertical-align: middle;"
								class=""
								title="<?php echo JText::_('COM_NETBASEVM_EXTEND_SAME_AS_BILLING'); ?>::<?php echo JText::_('COM_NETBASEVM_EXTEND_SAME_AS_BILLING_DESC'); ?>">
									<input style="margin: 0px; width: 20px !important;"
									type="checkbox" id="billing_is_shipping"
									name="billing_is_shipping" value="1"
									onclick="if (this.checked) disableAllShipping(); else enableAllShipping(); changed_userinfo=true;"
          						<?php if ($this->shippingData->billing_is_shipping) echo 'checked' ?> />
									<?php echo JText::_('COM_NETBASEVM_EXTEND_SAME_AS_BILLING'); ?>
							</label></td>
							<td align="left">&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td align="left">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="4">
								<fieldset class="adminform">
									<legend>
										<?php echo JText::_('COM_NETBASEVM_EXTEND_CUSTOMER_NAME') ?>
									</legend>
									<table class="admintable" cellspacing="0" cellpadding="1"
										border="0">
										<tbody>
											<tr>
												<td width="20%" align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_FIRST_NAME'); ?>
												</td>
												<td width="30%" align="left"><input type="text"
													class="fullWidth" name="S_first_name"
													value="<?php echo $this->shippingData->first_name; ?>" />
												</td>
												<td width="20%" align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_LAST_NAME'); ?>
												</td>
												<td width="30%" align="left"><input type="text"
													class="fullWidth" name="S_last_name"
													value="<?php echo $this->shippingData->last_name; ?>" />
												</td>
											</tr>
											<tr>
												<td align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_TITLE'); ?>
												</td>
												<td align="left"><input type="text" class="fullWidth"
													name="S_title"
													value="<?php echo $this->shippingData->title; ?>" />
												</td>
												<td align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_MIDDLE_NAME'); ?>
												</td>
												<td align="left"><input type="text" class="fullWidth"
													name="S_middle_name"
													value="<?php echo $this->shippingData->middle_name; ?>" />
												</td>
											</tr>
											<tr>
												<td width="20%" align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_COMPANY_NAME'); ?>
												</td>
												<td width="80%" colspan="3" align="left"><input type="text"
													class="fullWidth" name="S_company"
													value="<?php echo $this->shippingData->company; ?>" />
												</td>
											</tr>
										</tbody>
									</table>
								</fieldset>
								<fieldset class="adminform">
									<legend>
										<?php echo JText::_('COM_NETBASEVM_EXTEND_ADDRESS') ?>
									</legend>
									<table class="admintable" cellspacing="0" cellpadding="1"
										border="0">
										<tbody>
											<tr>
												<td width="20%" align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_ADDRESS_1'); ?>
												</td>
												<td width="30%" align="left"><input type="text"
													class="fullWidth" name="S_address_1"
													value="<?php echo $this->shippingData->address_1; ?>" />
												</td>
												<td width="20%" align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_ADDRESS_2'); ?>
												</td>
												<td width="30%" align="left"><input type="text"
													class="fullWidth" name="S_address_2"
													value="<?php echo $this->shippingData->address_2; ?>" />
												</td>
											</tr>
											<tr>
												<td align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_CITY'); ?>
												</td>
												<td align="left"><input type="text" class="fullWidth"
													name="S_city"
													value="<?php echo $this->shippingData->city; ?>" />
												</td>
												<td align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_ZIP_POSTAL_CODE'); ?>
												</td>
												<td align="left"><input type="text" class="fullWidth"
													name="S_zip"
													value="<?php echo $this->shippingData->zip; ?>" />
												</td>
											</tr>
											<tr>
												<td align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_COUNTRY'); ?>
												</td>
												<td align="left"><?php echo JHTML::_('select.genericlist',$this->countries, 'S_country', array('class' => 'fullWidth','onchange' => 'populateStates(\'S_country\',\'S_state\')','onkeyup' => 'populateStates(\'S_country\',\'S_state\')'), 'id', 'name', $this->shippingData->country); ?>
												</td>
												<td align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_STATE_PROVINCE_REGION'); ?>
												</td>
												<td align="left"><?php echo JHTML::_('select.genericlist',$this->s_states, 'S_state', array('class' => 'fullWidth'), 'id', 'name', $this->shippingData->state); ?>
												</td>
											</tr>
										</tbody>
									</table>
								</fieldset>
								<fieldset class="adminform">
									<legend>
										<?php echo JText::_('COM_NETBASEVM_EXTEND_CONTACTS') ?>
									</legend>
									<table class="admintable" cellspacing="0" cellpadding="1"
										border="0">
										<tbody>
											<tr>
												<td width="20%" align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_MAIL'); ?>
												</td>
												<td width="30%" align="left"><input type="text"
													class="fullWidth" name="S_email"
													value="<?php echo $this->shippingData->email; ?>" />
												</td>
												<td width="20%" align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_PHONE'); ?>
												</td>
												<td width="30%" align="left"><input type="text"
													class="fullWidth" name="S_phone_1"
													value="<?php echo $this->shippingData->phone_1; ?>" />
												</td>
											</tr>
											<tr>
												<td align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_MOBILE_PHONE'); ?>
												</td>
												<td align="left"><input type="text" class="fullWidth"
													name="S_phone_2"
													value="<?php echo $this->shippingData->phone_2; ?>" />
												</td>
												<td align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_FAX'); ?>
												</td>
												<td align="left"><input type="text" class="fullWidth"
													name="S_fax"
													value="<?php echo $this->shippingData->fax; ?>" />
												</td>
											</tr>

											<?php 
											//display custom user fields
											if (isset($this->shippingData->userFields)) {
          							$countUF = 0;
          							foreach ($this->shippingData->userFields as $userField)
          							{

          								$countUF++;
          								if ($countUF == 1) {
                            ?>

											<tr>
												<td colspan="4">&nbsp;</td>
											</tr>
											<tr>
												<td colspan="4" align="left" class="title"><h3>
														<?php echo JText::_('COM_NETBASEVM_EXTEND_ADDITIONAL_FIELDS') ?>
													</h3>
												</td>
											</tr>

											<?php
              						}
              						//display label (with tip)
              						echo '<tr><td align="right">';
              						$titleTrans = InvoiceGetter::getVMTranslation($userField['title']);
              						if (isset($userField['desc']) && $userField['desc']!=$userField['title'])
              							echo '<span class="" title="'.$titleTrans.'::'.InvoiceGetter::getVMTranslation($userField['desc']).'">'.$titleTrans.'</span></td>';
              						else
              							echo $titleTrans.'</td>';

              						//display inputs
              						echo '<td align="left">'.sanitiseUserInput($userField['input']).'</td></tr>';
          							}
          						}

          						?>
										</tbody>
									</table>
								</fieldset>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</table>
	<?php if ($this->shippingData->billing_is_shipping){ ?>
	<script type="text/javascript">disableAllShipping();</script>
	<?php }?>

	<?php if (! $this->userajax) { ?>
</div>
<?php } ?>