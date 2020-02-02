<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

?>
<style>
.icon-48-interamind_logo {
	background: transparent url(components/com_vmeeplus/images/plus_logo_48x48.png) repeat scroll 0 0;
}
</style>
	<div style="border:margin:10px; padding:10px;">
		<h1><image src="components/com_vmeeplus/images/32x32/help.png" alt="Help" ALIGN=ABSMIDDLE /> Send debug file and system info to Interamind support.</h1>
		<h2><?php echo JText::_( 'SUPPORT_HOW_TO' ); ?></h2>
		<ul>
			<li><?php echo sprintf(JText::_( 'SUPPORT_MSG_1' ), '<a style="text-decoration:underline" rel="{handler: \'iframe\', size: {x: 570, y: 500}}" href="index.php?option=com_config&view=component&component=com_vmeeplus&path=tmpl=component" class="modal">Parameters</a>'); ?>
			<li><?php echo JText::_( 'SUPPORT_MSG_2' ); ?>
			<li><?php echo JText::_( 'SUPPORT_MSG_3' ); ?>
		</ul>
		<br>
		<form method="post" name="adminForm" id="adminForm" style="font-size:12px;color:#666666;">
		<table cellspacing="5" cellpadding="5" border="0">
			<tr>
				<td style="font-weight: bold;">Email Subject:</td>
				<td><input type="text" name="subject" style="width:400px; height: 20px; padding: 3px;" /></td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Problem description:</td>
				<td><textarea name="description" style="width:400px; height:80px; padding: 3px;"></textarea></td>
			</tr>
			<tr>
				<td style="font-weight: bold;"><input type="radio" name="open_ticket" value="1" /> Open a new support ticket </td>
				<td></td>
			</tr>
			<tr>
				<td style="font-weight: bold;"><input type="radio" name="open_ticket" checked="checked" value="0" /> I already opened a ticket</td>
				<td><input type="text" name="ticket_id" style="height: 20px; padding: 3px; width: 270px; margin-right: 5px"> (* Ticket ID if available)</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Send me a copy of the email:</td>
				<td><input type="text" name="cc_address" style="width:400px; height: 20px; padding: 3px;"></td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Temporary Admin user:</td>
				<td><input type="text" name="admin_user" style="width:160px; height: 20px; padding: 3px; margin-right: 10px;"> Password: <input type="text" name="admin_password" style="width:160px; height: 20px; padding: 3px;"></td>
			</tr>
			<tr>
				<td></td>
				<td><input style="float:right; font-weight:bold;" onClick="Joomla.submitbutton('sendDebugFiles');" value="Send" type="button" /></td>
			</tr>
			<tr>
				<!-- <td colspan="2"><span style="color:red;">* Note </span>- <?php echo JText::_( 'SUPPORT_MSG_4' ); ?> <a target="_blank" href="index.php?option=com_admin&task=sysinfo"><?php echo JText::_( 'CLICK_HERE' ); ?></a></td>-->
				
			</tr>
		</table>
		
		<input type="hidden" name="option" value="com_vmeeplus" /> 
		<input type="hidden" name="task" value="" /> 
		<?php echo JHtml::_( 'form.token' ); ?>
		
		</form>
	</div>
	<!--
	<iframe scrolling="auto" name="vmee_help" width="100%" src="http://www.interamind.com/vmee_help_2_0" height="800px" style="overflow:auto; border:1px solid lightgrey;"></iframe>
	-->