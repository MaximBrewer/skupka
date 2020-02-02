<?php defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/ 

JHtml::_('script', 'administrator/components/com_vmeeplus/js/vmeePro.js');
JHtml::_('stylesheet','https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/smoothness/jquery-ui.css');
JHtml::_('stylesheet','administrator/components/com_vmeeplus/js/ui.dropdownchecklist.themeroller.css');
JHtml::_('stylesheet','administrator/components/com_vmeeplus/views/com_vmeeplus.css');
JHtml::_('script', 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js');
JHtml::_('script', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js');
$jqurydropdownJS = "jQuery.noConflict();\n";
$jqurydropdownJS .= "jQuery(document).ready(function(){\n";
$jqurydropdownJS .= "\t jQuery(\".vmeeprobutton\").button(); \n";
$jqurydropdownJS .= "});\n";

$submitJs = "Joomla.submitbutton=function(pressbutton) {
var form = document.adminForm;
if ( pressbutton == 'import' && form.boxchecked.value == 0 ) {
alert('" . JText::_( 'Please select templates to import first', true ) . "');
}
else {
submitform(pressbutton);
}
}
";
$document = JFactory::getDocument();

$document->addScriptDeclaration($submitJs);
$document->addScriptDeclaration($jqurydropdownJS);

JHtml::_('stylesheet', 'administrator/components/com_vmeeplus/views/com_vmeeplus.css');  ?>

<h2><?php echo JText::_("Maintenance");?></h2>
<form name="adminForm" id="adminForm" method="post">
<hr/>
<?php if(isset($this->emails)){?>
<h3><?php echo JText::_("Import templates from Emails manager");?></h3>

<?php 
	echo '<h4>' . JText::_("Select the templates to import") . '</h4>';
	foreach ($this->emails as $id=>$name){
		echo '<input type="checkbox" onclick="isChecked(this.checked);" value="' . $id . '" name="template_id[]" $id="tid' . $id . '" >' . $name . '</input><br/>';
	}
?>
<br/>
<input class="vmeeprobutton" type="button" value="Import" onclick="Joomla.submitbutton('import')" />
<hr/>
<?php }?>


<input type="hidden" name="task" value="" />
<input type="hidden" name="option" value="com_vmeeplus" />
<input type="hidden" name="boxchecked" value="0" >
<input type="hidden" name="view" value="maintenance">
<input type="hidden" name="controller" value="maintenance">
<?php echo JHtml::_( 'form.token' ); ?>



</form>