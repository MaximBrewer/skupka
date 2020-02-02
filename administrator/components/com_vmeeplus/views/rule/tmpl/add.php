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
$jqurydropdownJS .= "\t jQuery(\"#templateID\").dropdownchecklist({icon: {}, closeRadioOnClick: true,width: 265,maxDropHeight: 150 }); \n";
$jqurydropdownJS .= "\t jQuery(\"#rule_trigger\").dropdownchecklist({icon: {}, closeRadioOnClick: true,width: 265,maxDropHeight: 150 }); \n";
$jqurydropdownJS .= "});\n";
$document = JFactory::getDocument();
$document->addScriptDeclaration($jqurydropdownJS);

defined( '_JEXEC' ) or die( 'Restricted access' );
$submitJs = "Joomla.submitbutton = function(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

if ( form.rule_name.value == '' ) {
		alert('" . JText::_( 'Rule must have a name', true ) . "');
	}
	else {
		submitform(pressbutton);
	}
}
function sendTest(){
	Joomla.submitbutton('sendTest');
}";

$document->addScriptDeclaration($submitJs);
?>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js" type="text/javascript"></script>
<script src="components/com_vmeeplus/js/ui.dropdownchecklist-1.5.js" type="text/javascript"></script>
<div>
<form name="adminForm" id="adminForm" method="post" >

<fieldset class="vmeeproFs" id="newRule" style="">
	<legend><?php echo JText::_("NEW_RULE")?></legend>
	<table>
		<tr>
			<td>Name: </td>
			<td><input class="inputbox" type="text" name="rule_name" id="rule_name" size="40" maxlength="100" /></td>
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
		<tr>
			<td><?php echo JText::_( 'RULE_TEMPLATE_MAPPING' ); ?>: </td>
			<td>
				<select id="templateID" name="templateID">
					<?php 
					foreach ($this->templateList as $template) {
						echo "<option value=\"".$template['id']."\" >".$template['name']."</option>";
					}
					?>
				</select>
			</td>
		</tr>
	</table>
</fieldset>
<input type="hidden" name="task" value="" />
<input type="hidden" name="option" value="com_vmeeplus" />
<input type="hidden" name="view" value="rule" >
<input type="hidden" name="controller" value="rule" >
<?php echo JHtml::_( 'form.token' ); ?>
</form>
</div>