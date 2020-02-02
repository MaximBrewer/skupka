<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

JHtml::_('stylesheet','https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/smoothness/jquery-ui.css');
JHtml::_('stylesheet','administrator/components/com_vmeeplus/js/ui.dropdownchecklist.themeroller.css');
JHtml::_('stylesheet','administrator/components/com_vmeeplus/views/com_vmeeplus.css');
// JHtml::_('script', 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js');
// JHtml::_('script', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js');
// JHtml::_('script', 'administrator/components/com_vmeeplus/js/ui.dropdownchecklist-1.4-min.js');

$jqurydropdownJS = "jQuery.noConflict();\n";
$jqurydropdownJS .= "jQuery(document).ready(function(){\n";
$jqurydropdownJS .= "\t jQuery(\"#rule_trigger\").dropdownchecklist({icon: {}, closeRadioOnClick: true,width: 265,maxDropHeight: 150 }); \n";
$jqurydropdownJS .= "});\n";
$document = JFactory::getDocument();
$document->addScriptDeclaration($jqurydropdownJS);

?>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js" type="text/javascript"></script>
<script src="components/com_vmeeplus/js/ui.dropdownchecklist-1.5.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript">
<!--
Joomla.submitbutton=function(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

if ( form.templateName.value == "" ) {
		alert("<?php echo JText::_( 'Template must have a name', true ); ?>");
	else {
		submitform(pressbutton);
	}
}
function sendTest(){
	Joomla.submitbutton('sendTest');
}
-->
</script>
<div>
<form name="adminForm" id="adminForm" method="post" >

<fieldset class="vmeeproFs" id="newTemplate" style="">
	<legend><?php echo JText::_("NEW_TEMPLATE")?></legend>
	<table>
		<tr>
			<td>Name: </td>
			<td><input class="inputbox" type="text" name="templateName" id="templateName" size="40" maxlength="100" /></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'RULE_TRIGGER' ); ?>: </td>
			<td>
				<select id="rule_trigger" name="rule_trigger">
					<?php 
					foreach ($this->triggerList as $trigger) {
						echo "<option value=\"".$trigger['id']."\" >".$trigger['display_name']."</option>";
					}
					?>
				</select>
			</td>
		</tr>
	</table>
</fieldset>
<input type="hidden" name="task" value="" />
<input type="hidden" name="option" value="com_vmeeplus" />
<input type="hidden" name="view" value="template" >
<input type="hidden" name="template_id" value="<?php echo $this->id ?>" >
<input type="hidden" name="name" value="<?php echo $this->templateName ?>" >
<input type="hidden" name="emailSubject" value="<?php echo $this->subject ?>" >
<input type="hidden" name="emailBody" value="<?php echo $this->body ?>" >
<input type="hidden" name="controller" value="template" >
<?php echo JHtml::_( 'form.token' ); ?>
</form>
</div>