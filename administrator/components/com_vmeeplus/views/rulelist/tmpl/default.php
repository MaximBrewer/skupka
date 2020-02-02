<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/ 

JHtml::_('stylesheet', 'administrator/components/com_vmeeplus/views/com_vmeeplus.css');  ?>

<h2><?php echo JText::_("RULE_LIST");?>:</h2>
<form name="adminForm" id="adminForm" method="post">

<?php if (!empty($this->ruleList)) {?>
<table id="" class="adminlist">
<thead>
<tr>
	<th width="20"><?php echo JText::_("#");?></th>
	<th width="20"><input type="checkbox" onclick="checkAll(<?php echo count($this->ruleList); ?>,'tid');" value="" name="toggle"></th>
	<th style=""><?php echo JHtml::_( 'grid.sort', JText::_("RULE_NAME"), 'name', $this->sortDirection, $this->sortColumn); ?></th>
	<th align="center" width="6%"><?php echo JHtml::_( 'grid.sort', JText::_("ENABLED"), 'enabled', $this->sortDirection, $this->sortColumn); ?></th>
	<th style=""><?php echo JHtml::_( 'grid.sort', JText::_("RULE_TRIGGER"), 'trigger_id', $this->sortDirection, $this->sortColumn); ?></th>
	<th width="6%"><?php echo JHtml::_( 'grid.sort', JText::_("RULE_ID"), 'id', $this->sortDirection, $this->sortColumn); ?></th>
</tr>
</thead>
<?php 
$i = 0; 
$k = 0;

foreach ($this->ruleList as $rule) { ?>
	<tr class="<?php echo "row". $k; ?>">
	<td> <?php echo $i;?> </td>
	<td><input type="checkbox" onclick="Joomla.isChecked(this.checked);" value="<?php echo $rule->getId();?>" name="rule_id[]" id="tid<?php echo $i;?>"></td>
	<td><a class="toolbar" onclick="javascript:document.adminForm.tid<?php echo $i;?>.checked=true;Joomla.submitbutton('edit')" href="#"><?php echo $rule->getName()?></a></td>
	<?php 
	if($rule->isEnabled() == 1 ){
		$enabled = "<a onclick=\"javascript:document.adminForm.tid".$i.".checked=true;Joomla.submitbutton('disable')\" href=\"#\">" . JHtml::_('image','admin/'.'tick.png', JText::_( 'ENABLED' ), array('border' => 0), true) ."</a>";
	}else{
		$enabled = "<a onclick=\"javascript:document.adminForm.tid".$i.".checked=true;Joomla.submitbutton('enable')\" href=\"#\">" . JHtml::_('image','admin/'.'publish_x.png', JText::_( 'DISABLED' ), array('border' => 0), true) ."</a>";
	}
	?>
	<td align="center" ><?php echo $enabled ?></td>
	<td align="center" ><?php echo $rule->getTriggerDisplayName(); ?></td>
	<td align="center" > <?php echo $rule->getId(); ?> </td>
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
<input type="hidden" name="boxchecked" value="0" >
<input type="hidden" name="view" value="ruleList">
<input type="hidden" name="controller" value="ruleList">
<input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortDirection; ?>" />
<?php echo JHtml::_( 'form.token' ); ?>
</form>


<hr/>