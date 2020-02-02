<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/ 

JHtml::_('stylesheet', 'administrator/components/com_vmeeplus/views/com_vmeeplus.css');  ?>

<h2><?php echo JText::_("Email templates");?>:</h2>

<form name="adminForm" id="adminForm" method="post">
<?php if (!empty($this->templateList)) {?>
<table id="" class="adminlist">
<thead>
<tr>
	<th width="20"><?php echo JText::_("#");?></th>
	<th width="20"><input type="checkbox" onclick="checkAll(<?php echo count($this->templateList); ?>,'tid');" value="" name="toggle"></th>
	<th style=""><?php echo JHtml::_( 'grid.sort', JText::_("Template name"), 'name', $this->sortDirection, $this->sortColumn); ?></th>
	<th style=""><?php echo JHtml::_( 'grid.sort', JText::_("Subject"), 'subject', $this->sortDirection, $this->sortColumn); ?></th>
	<th style=""><?php echo JHtml::_( 'grid.sort', JText::_("EMAIL_TYPE"), 'trigger_id', $this->sortDirection, $this->sortColumn); ?></th>
	<th width="8%"><?php echo JHtml::_( 'grid.sort', JText::_("TEMPLATE_ID"), 'id', $this->sortDirection, $this->sortColumn); ?></th>
</tr>
</thead>
<?php 
$i = 0; 
$k = 0;

foreach ($this->templateList as $template) {?>
<tr class="<?php echo "row". $k; ?>">
<td> <?php echo $i;?> </td>
<td><input type="checkbox" onclick="Joomla.isChecked(this.checked);" value="<?php echo $template['id']?>" name="template_id[]" id="tid<?php echo $i;?>"></td>
<td><a class="toolbar" onclick="javascript:document.adminForm.tid<?php echo $i;?>.checked=true;Joomla.submitbutton('edit')" href="#"><?php echo $template['name']?></a></td>
<?php 
/*if($template['isDefault'] == 1 ){
	$default = "<img src=\"templates/khepri/images/menu/icon-16-default.png\" alt=\"".JText::_( 'Default' )."\" />";
}else{
	$default = "&nbsp;";
}*/
?>
<!--<td align="center" ><?php //echo $default ?></td>-->
<td><?php echo $template['subject']?></td>
<td><?php echo $this->triggerList[$template['trigger_id']]['display_name']?></td>
<td> <?php echo $template['id'];?> </td>
</tr>
<?php 
$i++; 
$k = 1 - $k;
}
?>
</table>
<?php }?>
<input type="hidden" name="task" value="" />
<input type="hidden" name="option" value="com_vmeeplus" />
<input type="hidden" name="boxchecked" value="0">
<input type="hidden" name="view" value="templateList">
<input type="hidden" name="controller" value="templateList">
<input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortDirection; ?>" />
<?php echo JHtml::_( 'form.token' ); ?>
</form>


<hr/>