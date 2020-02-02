<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/
$document = JFactory::getDocument();
		
JHtml::_('script', 'administrator/components/com_vmeeplus/js/dojo-modules.js');
JHtml::_('script', 'https://ajax.googleapis.com/ajax/libs/dojo/1.4/dojo/dojo.xd.js');
JHtml::_('stylesheet','https://ajax.googleapis.com/ajax/libs/dojo/1.4.0/dijit/themes/tundra/tundra.css');
JHtml::_('stylesheet','https://ajax.googleapis.com/ajax/libs/dojo/1.4.0/dojox/grid/resources/Grid.css');
JHtml::_('stylesheet','https://ajax.googleapis.com/ajax/libs/dojo/1.4.0/dojox/grid/resources/tundraGrid.css');
JHtml::_('script', 'administrator/components/com_vmeeplus/js/vmeePro.js');
JHtml::_('stylesheet','https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/smoothness/jquery-ui.css');
JHtml::_('stylesheet','administrator/components/com_vmeeplus/js/ui.dropdownchecklist.themeroller.css');
JHtml::_('stylesheet','administrator/components/com_vmeeplus/views/com_vmeeplus.css');
//JHtml::_('script', 'https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js');
//JHtml::_('script', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js');
//JHtml::_('script', 'administrator/components/com_vmeeplus/js/ui.dropdownchecklist-1.4-min.js');

$jqurydropdownJS = "jQuery.noConflict();\n";
$jqurydropdownJS .= "jQuery(document).ready(function(){\n";
$jqurydropdownJS .= "\t jQuery(\"#templateID\").dropdownchecklist({icon: {}, closeRadioOnClick: true,width: 265,maxDropHeight: 150 }); \n";
$jqurydropdownJS .= "\t jQuery(\"#valuesselect\").dropdownchecklist({ maxDropHeight: 150, width: 250 }); \n";
$jqurydropdownJS .= "\t jQuery(\"#newConditionOperator\").dropdownchecklist({ maxDropHeight: 150 }); \n";
$jqurydropdownJS .= "\t //jQuery(\"#status\").dropdownchecklist({icon: {}, width: 150,maxDropHeight: 150 }); \n";
$jqurydropdownJS .= "\t jQuery(\"#submenu li a\").button(); \n";
$jqurydropdownJS .= "});\n";


$document->addScriptDeclaration($jqurydropdownJS);
$submitJs = "Joomla.submitbutton=function(pressbutton) {
var form = document.adminForm;
if (pressbutton == 'cancel') {
submitform( pressbutton );
return;
}

if ( form.name.value == '' ) {
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
$vmeeproJS = "var vmeeProURL = '" . $_SERVER['PHP_SELF'] . "?option=com_vmeeplus';";
$document->addScriptDeclaration($vmeeproJS);
JHtml::_('behavior.tooltip');
?>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js" type="text/javascript"></script>
<script src="components/com_vmeeplus/js/ui.dropdownchecklist-1.5.js" type="text/javascript"></script>
<style>
.icon-48-interamind_logo {background:transparent url(components/com_vmeeplus/images/plus_logo_48x48.png) repeat scroll 0 0;}
td .iparam{border:none;background:none;min-width:200px;}
.iparam_header{width:200px;background-color:#F6F6F6;border-bottom:1px solid #E9E9E9;border-right:1px solid #E9E9E9;color:#666666;font-weight:bold;text-align:right;}
.iparam_td{min-width:200px;background-color:#F6F6F6;border-bottom:1px solid #E9E9E9;border-right:1px solid #E9E9E9;color:#333333;font-weight:normal;text-align:left;}

.vmeeprobutton{
padding: 5px !important;
margin-top: -8px !important;
}
</style>

<?php 
$rule = $this->rule;
?>
<form name="adminForm" id="adminForm" method="post" >
<fieldset class="vmeeproFs" id="newCondition" style="width:800px">
	<legend><?php echo JText::_("RULE_BASIC_PARAMS")?></legend>
	<label for="name"><?php echo JText::_( 'RULE_NAME' ); ?></label> <input class="text_area" type="text" name="name" id="name" value="<?php echo $rule->getName(); ?>" size="40" maxlength="100" title="" />
</fieldset>
<table class="adminform" style="margin-left: 3px; width: 810px;">
	<tr>
		<td><label for="trigger"> <?php echo JText::_( 'RULE_TRIGGER' ); ?> </label></td>
		<td>
			<i><?php echo $rule->getTriggerDisplayName() ?></i>
<!--			<input class="inputbox" type="text" name="trigger" id="trigger" value="<?php //echo $rule->getTrigger(); ?>" size="40" maxlength="100" title="" />-->
		</td>
		
		<td><?php echo JText::_( 'RULE_IS_ENABLED' ); ?></td>
		<td>
			<input id="isEnabled0" type="radio" value="0" name="isEnabled" <?php if($rule->isEnabled()==0) echo "checked=\"checked\""; ?>>
			<label for="isEnabled0"><?php echo JText::_( 'VMNO' ); ?></label>
			<input id="isEnabled1" type="radio" value="1" name="isEnabled" <?php if($rule->isEnabled()==1) echo "checked=\"checked\""; ?>>
			<label for="isEnabled1"><?php echo JText::_( 'VMYES' ); ?></label>
		</td>
	</tr>
	<tr>
		<td><label for="templateID"><?php echo JText::_( 'RULE_TEMPLATE_MAPPING' ); ?></label></td>
		<td>
		<select id="templateID" name="templateID">
		<?php 
		$selectedTemplateId = $rule->getTemplateId();
		$bMatch = false;
		foreach ($this->templateList as $template) {
			$selected = $selectedTemplateId == $template['id'] ? ' SELECTED=SELECTED ' : '';
			if(!empty($selected)){
				$bMatch = true;
			}
			echo "<option value=\"".$template['id']."\" ".$selected.">".$template['name']."</option>";
		}
		if(!empty($selectedTemplateId) && !$bMatch){
			//template might have been deleted, write warning
			JError::raiseWarning('', JText::_("Rule's Template could not be found (deleted?). Please select template and save rule"));
		}
		?>
		</select>
		<br><a id="editTemplateLink" target="_blank" href="index.php?template_id[]=<?php echo $selectedTemplateId ?>&task=edit&option=com_vmeeplus&view=templateList&controller=templateList"><?php echo JText::_( 'EDIT_TEMPLATE_LINK' ); ?></a>
		</td>
		
		<td><?php echo JText::_( 'RULE_IS_EMAIL_TO_ADMINS' ); ?></td>
		<td>
			<input class="hasTip" title="<?php echo JText::_('Add Joomla administrators to Bcc'); ?>" id="isEmailToAdmins0" type="radio" value="0" name="isEmailToAdmins" <?php if($rule->isEmailToAdmins()==0) echo "checked=\"checked\""; ?>>
			<label for="isEmailToAdmins0"><?php echo JText::_( 'VMNO' ); ?></label>
			<input class="hasTip" title="<?php echo JText::_('Add Joomla administrators to Bcc'); ?>" id="isEmailToAdmins1" type="radio" value="1" name="isEmailToAdmins" <?php if($rule->isEmailToAdmins()==1) echo "checked=\"checked\""; ?>>
			<label for="isEmailToAdmins1"><?php echo JText::_( 'VMYES' ); ?></label>
		</td>
	</tr>
	<tr>
		<td><label for="to"><?php echo JText::_( 'RULE_TO' ); ?></label></td>
		<td><input class="inputbox hasTip" title="<?php echo JText::_('LIST_OF_EMAIL_ADDRESSES_SEPERATED_BY_SEMICOLON'); ?>" type="text" name="to" id="to" value="<?php echo $rule->getTo(); ?>" size="40"  title="" /></td>
		
		<td></td>
		<td>
			
		</td>
	<?php if($rule->allowEmailFromCustomer()){ ?>
	<tr>
		<td style="border: 0px;"></td>
		<td style="border: 0px;"></td>
		<td><?php echo JText::_( 'RULE_CUSTOMER_FROM' ); ?></td>
		<td>
			<input id="isUseCustomerFrom0" type="radio" value="0" name="isUseCustomerFrom" onclick="jQuery('#from').removeAttr('disabled'); jQuery('#fromName').removeAttr('disabled');" <?php if($rule->isUseCustomerFrom()==0) echo "checked=\"checked\""; ?>>
			<label for="isUseCustomerFrom0"><?php echo JText::_( 'VMNO' ); ?></label>
			<input id="isUseCustomerFrom1" type="radio" value="1" name="isUseCustomerFrom" onclick="jQuery('#from').attr('disabled','disabled'); jQuery('#fromName').attr('disabled','disabled');" <?php if($rule->isUseCustomerFrom()==1) echo "checked=\"checked\""; ?>>
			<label for="isUseCustomerFrom1"><?php echo JText::_( 'VMYES' ); ?></label>
		</td>
	</tr>
	<?php }?>
	<tr>
		<td><label for="cc"><?php echo JText::_( 'RULE_CC' ); ?></label></td>
		<td><input class="inputbox hasTip" title="<?php echo JText::_('LIST_OF_EMAIL_ADDRESSES_SEPERATED_BY_SEMICOLON'); ?>" type="text" name="cc" id="cc" value="<?php echo $rule->getCc(); ?>" size="40"  title="" /></td>
		
		<td><label for="from"><?php echo JText::_( 'RULE_FROM' ); ?></label></td>
		<td><input class="inputbox" type="text" name="from" id="from" value="<?php echo $rule->getFrom(); ?>" size="40" maxlength="100" title="" <?php if($rule->isUseCustomerFrom() == 1) echo 'disabled="disabled"';  ?> /></td>
	</tr>
	<tr>
		<td><label for="bcc"><?php echo JText::_( 'RULE_BCC' ); ?></label></td>
		<td><input class="inputbox hasTip" title="<?php echo JText::_('LIST_OF_EMAIL_ADDRESSES_SEPERATED_BY_SEMICOLON'); ?>" type="text" name="bcc" id="bcc" value="<?php echo $rule->getBcc(); ?>" size="40"  title="" /></td>
		
		<td><label for="fromName"><?php echo JText::_( 'RULE_FROM_NAME' ); ?></label></td>
		<td><input class="inputbox" type="text" name="fromName" id="fromName" value="<?php echo $rule->getFromName(); ?>" size="40" maxlength="100" title="" <?php if($rule->isUseCustomerFrom() == 1) echo 'disabled="disabled"';  ?> /></td>
	</tr>
	<tr>
		<td><label for="bcc"><?php echo JText::_( 'ATTACHMENTS' ); ?></label></td>
		<td colspan="3"><input style="width: 96%;" class="inputbox hasTip" title="<?php echo JText::_('LIST_ATTACHMENTS_SEPERATED_BY_SEMICOLON'); ?>" type="text" name="attachments" id="attachments" value="<?php echo $rule->getAttachments(); ?>" size="1024"  title="" /></td>
	</tr>
</table>
	<?php 
		$rulePreconditions = $rule->getRulePreconditions();
		if(!empty($rulePreconditions)){ ?>
		<fieldset class="vmeeproFs" id="precondition" style="width:800px">
			<legend><?php echo JText::_("RULE_PRECONDITION")?></legend>
	<?php 
			foreach ($rulePreconditions as $precondition){
			echo '<div class="precondition">' . $precondition . '</div>' . "\n";
			} ?>
		</fieldset>
		<?php } ?>

<input type="hidden" name="task" value="" />
<input type="hidden" name="option" value="com_vmeeplus" />
<input type="hidden" name="view" value="rule" >
<input type="hidden" name="rule_id" value="<?php echo $rule->getId(); ?>" >
<input type="hidden" name="controller" value="rule" >
<?php echo JHtml::_( 'form.token' ); ?>
</form>

<fieldset class="vmeeproFs" id="newCondition" style="width:800px">
	<legend><?php echo JText::_("ADD_NEW_CONDITION")?></legend>
	<form id="newConditionForm" name="newConditionForm">
		<input type="hidden" id="rule_id" name="rule_id" value="<?php echo $rule->getId(); ?>" >
		<table>
			<tr>
				<td><select id="newConditionType" name="newConditionType" style="width:200px;"></select></td>
				<td><select id="newConditionOperator" name="newConditionOperator" style="width:200px;"></select></td>
				<td><span id="condvalueplaceholder"></span><!-- <input type="text" id="newConditionValue" name="newConditionValue" style="width:200px;"/> --></td>
				<td><input class="vmeeprobutton" type="button" id="createNew" value="<?php echo JText::_("ADD")?>"  onclick="addNewCondition()"/></td>
				<td><img id="loading" src="components/com_vmeeplus/images/ajax-loader1.gif" style="display: none;"/></td>
			</tr>
		</table>
	</form>
	<br/>
	<div id="conditionsGrid" style="width: 100%; height: 150px; border: 1px solid lightgrey"></div>
	<input style="float: right; margin-top:3px" id="deleteBtn" type="button" value="<?php echo JText::_("DELETE_CONDITION")?>" disabled="disabled" onclick="deleteSelected()"/>
</fieldset>
