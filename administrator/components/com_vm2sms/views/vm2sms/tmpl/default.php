<?php
// no direct access
defined('_JEXEC') or die;


?>

<form action="<?php echo JRoute::_('index.php?option=com_vm2sms'); ?>" method="post" name="adminForm" id="adminForm">
<div style="width:80%;float:left;">
	<table class="adminlist" >
		<thead>
			<tr>
				<th></th>
				<th colspan="4" style="background:#EAE8E8"><?php echo JText::_('COM_VM2SMS_CLIENT')?></th>
				<th width="10"></th>
				<th colspan="4" style="background:#EAE8E8"><?php echo JText::_('COM_VM2SMS_MANAGER')?></th>
				<th width="10"></th>
				<tr>
			<tr>
				<th width="100">
					<?php echo JText::_('COM_VM2SMS_ORDER_STATUS')?>
				</th>
				<th width="100">
				<?php echo JText::_('COM_VM2SMS_SEND_SMS')?>				</th>
				<th class="title">
					<?php echo JText::_('COM_VM2SMS_TEXT_SMS')?>
				</th>
				<th class="title">
					<?php echo JText::_('COM_VM2SMS_INCLUDE_COMMENT')?>
				</th>
				<th class="title">
					<?php echo JText::_('COM_VM2SMS_INSTANT')?>
				</th>
				<th class="title" title="<?php echo JText::_('COM_VM2SMS_WORKTIME_DESC')?>">
					<?php echo JText::_('COM_VM2SMS_WORKTIME')?>
				</th>
				<th></th>
				<th width="100">
				<?php echo JText::_('COM_VM2SMS_SEND_SMS')?>				</th>

				<th class="title">
					<?php echo JText::_('COM_VM2SMS_TEXT_SMS')?>
				</th>
				<th class="title">
					<?php echo JText::_('COM_VM2SMS_INSTANT')?>
				</th>
				<th class="title" title="<?php echo JText::_('COM_VM2SMS_WORKTIME_DESC')?>">
					<?php echo JText::_('COM_VM2SMS_WORKTIME')?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
				<?php echo JText::_($item->order_status_name);?>
				</td>
				<td class="center">
				<input type="checkbox" name="send_sms[]" value="<?php echo $item->order_status_code?>" <?php if($item->send_sms)echo "checked='checked'";?>/>
				</td>
				<td class="center">
				<textarea style="width:100%;height:50px" name="text_sms[<?php echo $item->order_status_code?>]"><?php echo $item->text_sms?></textarea>
				</td>
				<td class="center">
				<input type="checkbox" name="include_comment[]" value="<?php echo $item->order_status_code?>" <?php if($item->include_comment)echo "checked='checked'";?>/>
				</td>
				<td class="center">
				<input type="radio" name="worktime[<?php echo $item->order_status_code?>]" value="0" <?php if (!$item->worktime) echo 'checked="checked"';?>/>
				</td>
				<td class="center">
				<input type="radio" name="worktime[<?php echo $item->order_status_code?>]" value="1" <?php if ($item->worktime) echo 'checked="checked"';?>/>
				</td>
				<td></td>
				<td class="center">
				<input type="checkbox" name="manager_send_sms[]" value="<?php echo $item->order_status_code?>" <?php if($item->manager_send_sms)echo "checked='checked'";?>/>
				</td>

				<td class="center">
				<textarea style="width:100%;height:50px" name="manager_text_sms[<?php echo $item->order_status_code?>]"><?php echo $item->manager_text_sms?></textarea>
				</td>
				<td class="center">
				<input type="radio" name="manager_worktime[<?php echo $item->order_status_code?>]" value="0" <?php if (!$item->manager_worktime) echo 'checked="checked"';?>/>
				</td>
				<td class="center">
				<input type="radio" name="manager_worktime[<?php echo $item->order_status_code?>]" value="1" <?php if ($item->manager_worktime) echo 'checked="checked"';?>/>
				</td>


			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
<div style="width:18%;margin-left:2%;float:left;">
<h3><?php echo JText::_('COM_VM2SMS_DESC_TITLE')?></h3>
<?php echo JText::_('COM_VM2SMS_DESC')?>
</div>

	<div>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
