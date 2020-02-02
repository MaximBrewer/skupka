<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

JHtml::_('stylesheet','administrator/components/com_vmeeplus/views/com_vmeeplus.css');
JHtml::_('stylesheet','https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/smoothness/jquery-ui.css');
JHtml::_('stylesheet','administrator/components/com_vmeeplus/js/ui.dropdownchecklist.themeroller.css');
//JHtml::_('script', 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js');
//JHtml::_('script', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js');
//JHtml::_('script', 'ui.dropdownchecklist-1.4-min.js', 'administrator/components/com_vmeeplus/js/');
$jqurydropdownJS = "jQuery.noConflict();\n";
$jqurydropdownJS .= "jQuery(document).ready(function(){\n";
$jqurydropdownJS .= "\t jQuery(function(){jQuery( \"#accordion\" ).accordion({ autoHeight: false, collapsible: true, active: false });});\n";
$jqurydropdownJS .= "});\n";
$document = JFactory::getDocument();
$document->addScriptDeclaration($jqurydropdownJS);

$editor = JFactory::getEditor();
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

	if ( form.emailSubject.value == "" ) {
		alert("<?php echo JText::_( 'Template must have a subject', true ); ?>");
	} 
	else if ( form.templateName.value == "" ) {
		alert("<?php echo JText::_( 'Template must have a name', true ); ?>");
	}
	else {
		<?php echo $editor->save( 'emailBody' ) ; ?>
		submitform(pressbutton);
	}
}
function sendTest(){
	Joomla.submitbutton('sendTest');
}
-->
</script>

<style>
.icon-48-interamind_logo {
	background: transparent
		url(components/com_vmeeplus/images/plus_logo_48x48.png) repeat
		scroll 0 0;
}

td .iparam {
	border: none;
	background: none;
	min-width: 200px;
	width: 100%;
	cursor: pointer;
}

.iparam_header {
	width: 200px;
	background-color: #F6F6F6;
	border-bottom: 1px solid #E9E9E9;
	border-right: 1px solid #E9E9E9;
	color: #666666;
	font-weight: bold;
	text-align: right;
}

.iparam_td {
	min-width: 220px;
	background-color: #F6F6F6;
	border-bottom: 1px solid #E9E9E9;
	border-right: 1px solid #E9E9E9;
	color: #333333;
	font-weight: normal;
	text-align: left;
}

.paramlist{
	width: 100%
}
</style>
<form name="adminForm" id="adminForm" method="post">
	<h4 style="display: inline; margin-right: 5px;">
		<?php echo JText::_( 'TEMPLATE_NAME' ); ?>:
	</h4>
	<input class="text_area" type="text" name="templateName"
		id="templateName" value="<?php echo $this->templateName ?>" size="75"
		maxlength="100" title="" style="margin-right: 10px;"/>
	
	<h4 style="display: inline; margin-right: 5px;"><?php echo JText::_('EMAIL_TYPE'); ?>:</h4>
	<input type="text" id="rule_trigger" name="rule_trigger" value="<?php echo $this->triggerList[$this->trigger_id]['display_name']; ?>" disabled="disabled" style="background-color: white; width: 150px;" /><!-- <select id="rule_trigger" name="rule_trigger">
		<?php 
		foreach ($this->triggerList as $trigger) {
			$selected = $trigger['id'] == $this->trigger_id ? 'selected="selected"' : '';
			echo "<option value=\"".$trigger['id']. "\" " . $selected . " >".$trigger['display_name']."</option>";
		}
		?>
	</select> -->
	<div class="">
		<table class="adminform">
			<tr>
				<td valign="top">
					<fieldset class="vmeeproFs" style="float: left;">
						<legend>
							<?php echo JText::_( 'EMAIL DETAILS' ); ?>
						</legend>

						<table class="admintable">

							<tr>
								<td class="templatetd" style="width: 100px;"><label for="regConfEmailSubject"> <?php echo JText::_( 'Email subject' ); ?>:
								</label>
								</td>
								<td colspan="2" class="templatetd"><input class="text_area" type="text"
									name="emailSubject" id="emailSubject"
									value="<?php echo $this->subject ?>"maxlength="1000"
									title="" style="width: 100%;"/>
								</td>
							</tr>

							<tr>
								<td class="templatetd" colspan="3" style="text-align: left;"><label
									for="title" width="100"> <?php echo JText::_( 'Email body' ); ?>:
								</label>
								</td>
							</tr>

							<tr>
								<td valign="top" colspan="3" class="templatetd"><?php
								// parameters : areaname, content, width, height, cols, rows, show xtd buttons
								echo $editor->display( 'emailBody',  htmlspecialchars($this->body, ENT_QUOTES), '600', '700', '60', '20' ) ;
								?>
								</td>
							</tr>
						</table>
					</fieldset>
				</td>
				<td width="50%" valign="top">
					<div id="accordion">
						<?php   
						foreach ($this->available_tags as $availableTags){
							echo '<h3 style="padding-left: 30px;"><a href="#">' . $availableTags['title'] . '</a></h3>' . "\n";
							echo "<div>\n";
							echo "<div style=\"\">\n";
							echo $availableTags['description'];
							echo "</div>";
							echo "<div style=\"margin-top: 10px;\">\n";
							if(isset($availableTags['example']) && !empty($availableTags['example'])){
								echo "<h3>Example:</h3>";
								echo $availableTags['example'];
							}
							echo "</div>\n";
							echo "</div>\n";
						}
						?>
					</div>
					<div style="float: left; padding-left: 10px;">
						<a target="_blank"
							href="http://feeds.feedburner.com/~r/interamind/news/~6/1"><img
							src="http://feeds.feedburner.com/interamind/news.1.gif"
							alt="InteraMind News" style="border: 0pt none;" /> </a>
					</div>
				</td>
			</tr>
		</table>
	</div>

	<input type="hidden" name="task" value="" /> <input type="hidden"
		name="option" value="com_vmeeplus" /> <input type="hidden" name="view"
		value="template"> <input type="hidden" name="template_id"
		value="<?php echo $this->id ?>"> <input type="hidden" name="name"
		value="<?php echo $this->templateName ?>"> <input type="hidden"
		name="controller" value="template">
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
