<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();
 //echo $this->loadTemplate('params'); exit;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
?>

<script type="text/javascript" xmlns="http://www.w3.org/1999/html">
	Joomla.submitbutton	= function(task) {
		if (task == 'poll.cancel' || document.formvalidator.isValid(document.id('poll-form'))) {
			Joomla.submitform(task, document.getElementById('poll-form'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_sl_advpoll&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="poll-form" class="form-validate">
	<div class="row-fluid">
		<fieldset class="adminform">
			<div class="span10 form-horizontal">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#details" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_SL_ADVPOLL_POLL_NEW') : JText::sprintf('COM_SL_ADVPOLL_POLL_EDIT', $this->item->id); ?></a></li>
					<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('JGLOBAL_FIELDSET_PUBLISHING');?></a></li>
					<?php
					$fieldSets = $this->form->getFieldsets('params');
					foreach ($fieldSets as $name => $fieldSet) :
					?>
					<li><a href="#params-<?php echo $name;?>" data-toggle="tab"><?php echo JText::_($fieldSet->label);?></a></li>
					<?php endforeach; ?>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="details">
						<div class="row-fluid">
							<div class="span6">
								<div class="control-group">
									<?php echo $this->form->getLabel('title'); ?>
									<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
								</div>
								<div class="control-group">
									<?php echo $this->form->getLabel('catid'); ?>
									<div class="controls"><?php echo $this->form->getInput('catid'); ?></div>
								</div>
								<div class="control-group">
									<?php echo $this->form->getLabel('ordering'); ?>
									<div class="controls"><?php echo $this->form->getInput('ordering'); ?></div>
								</div>
								<div class="control-group">
									<?php echo $this->form->getLabel('schedule'); ?>
									<div class="controls"><?php echo $this->form->getInput('schedule'); ?></div>
								</div>
								<div class="control-group">
									<?php echo $this->form->getLabel('publish_up'); ?>
									<div class="controls"><?php echo $this->form->getInput('publish_up'); ?></div>
								</div>
								<div class="control-group">
									<?php echo $this->form->getLabel('publish_down'); ?>
									<div class="controls"><?php echo $this->form->getInput('publish_down'); ?></div>
								</div>
							</div>
							<div class="span6">

							</div>
						</div>
						<div id="" class="control-group">
							<label class="hasTip control-label" title="<?php echo JText::_('COM_SL_ADVPOLL_POLL_FIELD_ANSWERS_LABEL'); ?>"><?php echo JText::_('COM_SL_ADVPOLL_POLL_FIELD_ANSWERS_LABEL'); ?></label>
							<div class="controls">
								<?php echo $this->loadTemplate('answers'); ?>
							</div>
						</div>
					</div>

					<div class="tab-pane" id="publishing">
						<div class="control-group">
							<?php echo $this->form->getLabel('alias'); ?>
							<div class="controls"><?php echo $this->form->getInput('alias'); ?></div>
						</div>
						<div class="control-group">
							<?php echo $this->form->getLabel('id'); ?>
							<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
						</div>
						<div class="control-group">
							<?php echo $this->form->getLabel('created_by'); ?>
							<div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
						</div>
						<div class="control-group">
							<?php echo $this->form->getLabel('created_by_alias'); ?>
							<div class="controls"><?php echo $this->form->getInput('created_by_alias'); ?></div>
						</div>
						<div class="control-group">
							<?php echo $this->form->getLabel('created'); ?>
							<div class="controls"><?php echo $this->form->getInput('created'); ?></div>
						</div>
						<?php if ($this->item->modified_by) : ?>
						<div class="control-group">
							<?php echo $this->form->getLabel('modified_by'); ?>
							<div class="controls"><?php echo $this->form->getInput('modified_by'); ?></div>
						</div>
						<div class="control-group">
							<?php echo $this->form->getLabel('modified'); ?>
							<div class="controls"><?php echo $this->form->getInput('modified'); ?></div>
						</div>
						<?php endif; ?>
					</div>

					<?php echo $this->loadTemplate('params'); ?>

					<input type="hidden" name="task" value="" />
					<?php echo JHtml::_('form.token'); ?>
				</div>
			</div>

			<div class="span2">
				<h4><?php echo JText::_('JDETAILS'); ?></h4>
				<hr />
				<fieldset class="form-vertical">
					<div class="control-group">
						<div class="controls"><?php echo $this->form->getValue('title'); ?></div>
					</div>
					<div class="control-group">
						<?php echo $this->form->getLabel('state'); ?>
						<div class="controls"><?php echo $this->form->getInput('state'); ?></div>
					</div>
					<div class="control-group">
						<?php echo $this->form->getLabel('access'); ?>
						<div class="controls"><?php echo $this->form->getInput('access'); ?></div>
					</div>
					<div class="control-group">
						<?php echo $this->form->getLabel('language'); ?>
						<div class="controls"><?php echo $this->form->getInput('language'); ?></div>
					</div>
				</fieldset>
			</div>
		</fieldset>
	</div>
</form>

<?php
echo SL_AdvPollFactory::getFooter();