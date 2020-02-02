<?php defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/ 


$editor = JFactory::getEditor();
?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	if ( form.emailSubject.value == "" ) {
		alert("<?php echo JText::_( 'Email must have a subject', true ); ?>");
	} else {
		<?php echo $editor->save( 'emailBody' ) ; ?>
		submitform(pressbutton);
	}
}
function sendTest(){
	submitbutton('sendTest');
}
-->
</script>

<style>
.icon-48-interamind_logo {background:transparent url(components/com_vmeeplus/images/plus_logo_48x48.png) repeat scroll 0 0;}
td .iparam{border:none;background:none;min-width:200px;}
.iparam_header{width:200px;background-color:#F6F6F6;border-bottom:1px solid #E9E9E9;border-right:1px solid #E9E9E9;color:#666666;font-weight:bold;text-align:right;}
.iparam_td{min-width:200px;background-color:#F6F6F6;border-bottom:1px solid #E9E9E9;border-right:1px solid #E9E9E9;color:#333333;font-weight:normal;text-align:left;}
</style>

<h2>Name: <?php echo $this->name ?></h2>
<form name="adminForm" id="adminForm">
<table id="templatesTable" class="templatesTable" style="border-collapse:collapse;">
<!--
<tr>
	<td><?php echo JText::_("Is default? ");?>: <?php echo $this->default ?></td>
</tr>
<tr>
	<td><?php echo JText::_("Template type");?>: <?php echo $this->type ?></td>
</tr>

-->

</table>


<div class="">
		<table class="adminform">
			<tr>
				<td valign="top">
					<fieldset class="adminform" style="float:left;">
						<legend><?php echo JText::_( 'EMAIL DETAILS' ); ?></legend>
		
						<table class="admintable">
						
							<tr>
								<td class="key">
									<label for="regConfEmailSubject" width="100">
										<?php echo JText::_( 'Email subject' ); ?>:
									</label>
								</td>
								<td colspan="2">
									<input class="text_area" type="text" name="emailSubject" id="emailSubject" value="<?php echo $this->subject ?>" size="75" maxlength="100" title="" />
								</td>
							</tr>
								
							<tr>
								<td class="key">
									<label for="title" width="100">
										<?php echo JText::_( 'Email body' ); ?>:
									</label>
								</td>
								<td colspan="2">
									&nbsp;
								</td>
							</tr>
							
							<tr>
								<td valign="top" colspan="3">
									<?php
									// parameters : areaname, content, width, height, cols, rows, show xtd buttons
									echo $editor->display( 'emailBody',  htmlspecialchars($this->body, ENT_QUOTES), '600', '700', '60', '20' ) ;
									?>
								</td>
							</tr>
							</table>
					</fieldset>
				</td>
				<td width="50%"  valign="top">
					<fieldset class="adminform" style="">
						<legend><?php echo JText::_( 'Send test email' ); ?></legend>
							
							<input type="button" name="sendTestRegConfButton" title="Send Test Email" value="Send Test Email" onclick="sendTest()"/>
							
					</fieldset>
					
					<fieldset class="adminform" style="">
						<legend> <?php echo JText::_( 'TEMPLATE RULES' ); ?></legend>
						<table class="admintable">
							
							<tr>
								<td class="iparam_header">
									<label for="mail_disable"><?php echo JText::_( 'DISABLE EMAIL' ); ?></label>
								</td>
								<td class="iparam_td">
								<?php $checked = $this->enabled == false ? 'CHECKED' : ''; ?>
									<input type="checkbox" name="mail_disable" value="1" <?php echo $checked ?> />
								</td>
							</tr>
							
							<tr>
								<td class="iparam_header">
									<label for="ccList"><?php echo JText::_( 'CC List' ); ?></label>
								</td>
								<td class="iparam_td">
								<?php $ccList = $this->CC; ?>
									<input style="width:200px" type="text" name="ccList" value="<?php echo $ccList ?>"  />
								</td>
							</tr>
							<tr>
								<td class="iparam_header">
									<label for="bccList"><?php echo JText::_( 'BCC List' ); ?></label>
								</td>
								<td class="iparam_td">
								<?php $bccList = $this->BCC; ?>
									<input style="width:200px" type="text" name="bccList" value="<?php echo $bccList ?>"  />
								</td>
							</tr>
							<tr>
								<td class="iparam_header">
									<label for="attachment"><?php echo JText::_( 'Attachment' ); ?></label>
								</td>
								<td class="iparam_td">
								<?php $attachment = $this->attachment; ?>
									<input style="width:200px" type="text" name="attachment" value="<?php echo $attachment ?>"  />
								</td>
							</tr>
							
						</table>
					</fieldset>
					
					<fieldset class="adminform" style="">
						<legend><?php echo JText::_( 'Template subject Variables' ); ?></legend>
						
						<?php include_once 'templateVariablesSubject.incl.php'; ?>
						
					</fieldset>
						
						<?php include_once 'templateVariables.incl.php'; ?>
					
					<div style="float:left;padding-left:10px;">
						<a target="_blank" href="http://feeds.feedburner.com/~r/interamind/news/~6/1"><img src="http://feeds.feedburner.com/interamind/news.1.gif" alt="InteraMind News" style="border: 0pt none ;"/></a>
					</div>
				</td>
			</tr>
		</table>
		</div>
		
<input type="hidden" name="task" value="" />
<input type="hidden" name="option" value="com_vmeeplus" />
<input type="hidden" name="view" value="template" >
<input type="hidden" name="template_id" value="<?php echo $this->default ?>" >
<input type="hidden" name="controller" value="template" >
</form>