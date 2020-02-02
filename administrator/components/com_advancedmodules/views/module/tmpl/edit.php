<?php
/**
 * @package         Advanced Module Manager
 * @version         7.5.1PRO
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2018 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use RegularLabs\Library\Document as RL_Document;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('jquery.framework');
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.combobox');
JHtml::_('formbehavior.chosen', '#jform_position', null, ['disable_search_threshold' => 0]);
JHtml::_('formbehavior.chosen', '.multipleCategories', null, ['placeholder_text_multiple' => JText::_('JOPTION_SELECT_CATEGORY')]);
JHtml::_('formbehavior.chosen', '.multipleTags', null, ['placeholder_text_multiple' => JText::_('JOPTION_SELECT_TAG')]);
JHtml::_('formbehavior.chosen', 'select');

$hasContent          = empty($this->item->module) || isset($this->item->xml->customContent);
$hasContentFieldName = 'content';

// For a later improvement
if ($hasContent)
{
	$hasContentFieldName = 'content';
}

// Get Params Fieldsets
$this->fieldsets     = $this->form->getFieldsets('params');
$this->hidden_fields = '';

$script = "
Joomla.submitbutton = function(task)
{
	if (task == 'module.cancel' || document.formvalidator.isValid(document.getElementById('module-form'))) {";
if ($hasContent)
{
	$script .= $this->form->getField($hasContentFieldName)->save();
}
$script .= "
			Joomla.submitform(task, document.getElementById('module-form'));

				jQuery('#permissions-sliders select').attr('disabled', 'disabled');

				if (self != top)
				{
					window.top.setTimeout('window.parent.SqueezeBox.close();', 1000);
				}
			}
	};";
if (JFactory::getUser()->authorise('core.admin'))
{
	$script .= "
	jQuery(document).ready(function()
	{
		// add alert on remove assignment buttons
		jQuery('button.rl_remove_assignment').click(function()
		{
			if(confirm('" . str_replace('<br>', '\n', JText::_('AMM_DISABLE_ASSIGNMENT', true)) . "')) {
				jQuery('div#toolbar-options button').click();
			}
		});
	});";
}

RL_Document::scriptDeclaration($script);
RL_Document::script('regularlabs/script.min.js');
RL_Document::script('regularlabs/toggler.min.js');
?>

<form action="<?php echo JRoute::_('index.php?option=com_advancedmodules&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm"
      id="module-form" class="form-validate">

	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', ['active' => 'general']); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_MODULES_MODULE')); ?>

		<div class="row-fluid">
			<div class="span9">
				<?php if ($this->item->xml) : ?>
					<?php if ($this->item->xml->description) : ?>
						<h3>
							<?php
							if ($this->item->xml)
							{
								echo ($text = (string) $this->item->xml->name) ? JText::_($text) : $this->item->module;
							}
							else
							{
								echo JText::_('COM_MODULES_ERR_XML');
							}
							?>
						</h3>
						<div class="info-labels">
							<span class="label hasTooltip" title="<?php echo JHtml::tooltipText('COM_MODULES_FIELD_CLIENT_ID_LABEL'); ?>">
								<?php echo $this->item->client_id == 0 ? JText::_('JSITE') : JText::_('JADMINISTRATOR'); ?>
							</span>
						</div>
						<div>
							<?php
							$short_description = JText::_($this->item->xml->description);
							$this->fieldset    = 'description';
							$long_description  = JLayoutHelper::render('joomla.edit.fieldset', $this);
							if ( ! $long_description)
							{
								$truncated = JHtmlString::truncate($short_description, 550, true, false);
								if (strlen($truncated) > 500)
								{
									$long_description  = $short_description;
									$short_description = JHtmlString::truncate($truncated, 250);
									if ($short_description == $long_description)
									{
										$long_description = '';
									}
								}
							}
							?>
							<p><?php echo $short_description; ?></p>
							<?php if ($long_description) : ?>
								<p class="readmore">
									<a href="#" onclick="jQuery('.nav-tabs a[href=#description]').tab('show');">
										<?php echo JText::_('JGLOBAL_SHOW_FULL_DESCRIPTION'); ?>
									</a>
								</p>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				<?php else : ?>
					<div class="alert alert-error"><?php echo JText::_('COM_MODULES_ERR_XML'); ?></div>
				<?php endif; ?>
				<?php
				if ($hasContent)
				{
					echo $this->form->getInput($hasContentFieldName);
				}
				$this->fieldset = 'basic';
				$html           = JLayoutHelper::render('joomla.edit.fieldset', $this);
				echo $html ? '<hr>' . $html : '';
				?>
			</div>
			<div class="span3">
				<fieldset class="form-vertical">
					<?php echo $this->form->renderField('showtitle'); ?>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('position'); ?>
						</div>
						<div class="controls">
							<?php echo $this->loadTemplate('positions'); ?>
						</div>
					</div>
				</fieldset>
				<?php
				// Set main fields.
				$this->fields = [
					'published',
					'access',
					'ordering',
					'note',
				];
				?>
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
				<fieldset class="form-vertical">
					<?php if ($this->config->show_color) : ?>
						<div class="control-group">
							<div class="control-label">
								<label id="advancedparams_color-lbl" for="advancedparams_color" class="hasTooltip" title=""
								       data-original-title="<strong><?php echo JText::_('AMM_COLOR', true); ?></strong><br><?php echo JText::_('AMM_COLOR_DESC', true); ?>">
									<?php echo JText::_('AMM_COLOR'); ?>
								</label>
							</div>
							<div class="controls">
								<?php
								include_once(JPATH_LIBRARIES . '/joomla/form/fields/color.php');
								$colorfield = new JFormFieldColor;

								$color = (isset($this->item->advancedparams['color']) && $this->item->advancedparams['color'])
									? str_replace('##', '#', $this->item->advancedparams['color'])
									: 'none';

								$element        = new SimpleXMLElement(
									'<field
											name="advancedparams[color]"
											id="advancedparams_color"
											type="color"
											control="simple"
											default=""
											colors="' . (isset($this->config->main_colors) ? $this->config->main_colors : '') . '"
											split="4"
											/>'
								);
								$element->value = $color;

								$colorfield->setup($element, $color);

								echo $colorfield->__get('input');
								?>
							</div>
						</div>
					<?php endif; ?>
					<?php if ($this->item->client_id == 0) : ?>
						<?php echo $this->render($this->assignments, 'pre_post_html'); ?>
					<?php endif; ?>
					<?php if ($this->item->client_id == 0 && $this->config->show_hideempty) : ?>
						<?php echo $this->render($this->assignments, 'hideempty'); ?>
					<?php endif; ?>
					<?php if ($this->config->show_extra) : ?>
						<?php for ($i = 1; $i <= 5; $i++) : ?>
							<?php if (isset($this->config->{'extra' . $i}) && $this->config->{'extra' . $i} != '') : ?>
								<?php
								$label   = explode('|', $this->config->{'extra' . $i}, 2);
								$tooltip = isset($label[1]) ? JText::_($label[1]) : '';
								$label   = JText::_($label[0]);
								?>
								<div class="control-group">
									<div class="control-label">
										<label id="advancedparams_extra<?php echo $i; ?>-lbl" for="advancedparams_extra<?php echo $i; ?>"
											<?php echo $tooltip ? 'class="tooltip" title="<strong>' . $label . '</strong><br>' . $tooltip . '"' : ''; ?>>
											<?php echo $label; ?>
										</label>
									</div>
									<div class="controls">
										<?php echo $this->assignments->getInput('extra' . $i); ?>
									</div>
								</div>
							<?php endif; ?>
						<?php endfor; ?>
					<?php endif; ?>
				</fieldset>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php if (isset($long_description) && $long_description != '') : ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'description', JText::_('JGLOBAL_FIELDSET_DESCRIPTION')); ?>
			<?php echo $long_description; ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; ?>

		<?php if ($this->item->client_id == 0) : ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'assignment', JText::_('AMM_ASSIGNMENTS')); ?>
			<?php echo $this->loadTemplate('assignment'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; ?>

		<?php if ($this->config->show_notes) : ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'notes', JText::_('AMM_NOTES')); ?>
			<div class="form-vertical">
				<?php echo $this->render($this->assignments, 'notes'); ?>
			</div>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; ?>

		<?php if ($this->canDo->get('core.admin')) : ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_('COM_MODULES_FIELDSET_RULES')); ?>
			<?php echo $this->form->getInput('rules'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; ?>

		<?php
		$this->fieldsets        = [];
		$this->ignore_fieldsets = ['basic', 'description'];
		echo JLayoutHelper::render('joomla.edit.params', $this);
		?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		<?php echo $this->hidden_fields; ?>

		<input type="hidden" name="task" value="">
		<?php echo JHtml::_('form.token'); ?>
		<?php echo $this->form->getInput('module'); ?>
		<?php echo $this->form->getInput('client_id'); ?>

		<?php if ($this->config->show_switch) : ?>
			<div style="text-align:right">
				<a href="<?php echo JRoute::_('index.php?option=com_modules&force=1&task=module.edit&id=' . (int) $this->item->id); ?>"><?php echo JText::_('AMM_SWITCH_TO_CORE'); ?></a>
			</div>
		<?php endif; ?>
	</div>
</form>
