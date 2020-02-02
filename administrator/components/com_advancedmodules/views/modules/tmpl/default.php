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
use RegularLabs\Library\License as RL_License;
use RegularLabs\Library\Version as RL_Version;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$client    = $this->state->get('filter.client_id') ? 'administrator' : 'site';
$user      = JFactory::getUser();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$trashed   = $this->state->get('filter.state') == -2 ? true : false;
$canOrder  = $user->authorise('core.edit.state', 'com_modules');
$saveOrder = ($listOrder == 'ordering');

$langs = JLanguage::getKnownLanguages(constant('JPATH_' . strtoupper($client)));

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_advancedmodules&task=modules.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'moduleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$showcolors = ($client == 'site' && $this->config->show_color);
if ($showcolors)
{
	$script = "
		function setColor(id, el) {
			var f = document.getElementById('adminForm');
			f.setcolor.value = jQuery(el).val();
			listItemTask(id, 'modules.setcolor');
		}
	";
	RL_Document::scriptDeclaration($script);
}

RL_Document::style('regularlabs/style.min.css');
?>
<form action="<?php echo JRoute::_('index.php?option=com_advancedmodules'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if ( ! empty($this->sidebar)) : ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
	<?php endif; ?>
	<div id="j-main-container"<?php echo ( ! empty($this->sidebar)) ? ' class="span10"' : ''; ?>>
		<?php
		// Version check
		if ($this->config->show_update_notification)
		{
			echo RL_Version::getMessage('ADVANCED_MODULE_MANAGER');
		}
		?>
		<div class="clear"></div>
		<?php
		// Search tools bar and filters
		echo JLayoutHelper::render('joomla.searchtools.default', ['view' => $this]);
		?>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('COM_MODULES_MSG_MANAGE_NO_MODULES'); ?>
			</div>
		<?php else : ?>
			<?php $cols = 10; ?>
			<table class="table table-striped rl_tablelist" id="moduleList">
				<thead>
					<tr>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', '', 'ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
						</th>
						<th width="1%" class="nowrap center">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>
						<th width="1%" class="nowrap center" style="min-width:55px">
							<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
						</th>
						<?php if ($showcolors) : ?>
							<?php $cols++; ?>
							<th width="1%" class="nowrap center hidden-phone">
								<?php echo JHtml::_('searchtools.sort', '', 'color', $listDirn, $listOrder, null, 'asc', 'AMM_COLOR', 'icon-color'); ?>
							</th>
						<?php endif; ?>
						<th class="title">
							<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<?php if ($this->config->show_note == 3) : ?>
							<?php $cols++; ?>
							<th class="title">
								<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_DESCRIPTION', 'a.note', $listDirn, $listOrder); ?>
							</th>
						<?php endif; ?>
						<th width="15%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_MODULES_HEADING_POSITION', 'position', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_MODULES_HEADING_MODULE', 'name', $listDirn, $listOrder); ?>
						</th>
						<?php if ($client == 'site') : ?>
							<th width="10%" class="nowrap hidden-phone">
								<?php echo JHtml::_('grid.sort', 'RL_MENU_ITEMS', 'pages', $listDirn, $listOrder); ?>
							</th>
						<?php endif; ?>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="<?php echo $cols; ?>">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
					<?php foreach ($this->items as $i => $item) : ?>
						<?php
						$ordering   = ($listOrder == 'ordering');
						$canCreate  = $user->authorise('core.create', 'com_modules');
						$canEdit    = $user->authorise('core.edit', 'com_modules.module.' . $item->id);
						$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
						$canChange  = $user->authorise('core.edit.state', 'com_modules.module.' . $item->id) && $canCheckin;
						?>
						<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->position ? $item->position : 'none'; ?>">
							<td class="order nowrap center hidden-phone">
								<?php
								$iconClass = '';
								if ( ! $canChange)
								{
									$iconClass = ' inactive';
								}
								elseif ( ! $saveOrder)
								{
									$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
								}
								?>
								<span class="sortable-handler<?php echo $iconClass ?>">
									<span class="icon-menu"></span>
								</span>
								<?php if ($canChange && $saveOrder) : ?>
									<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>"
									       class="width-20 text-area-order">
								<?php endif; ?>
							</td>
							<td class="center">
								<?php if ($item->enabled > 0) : ?>
									<?php echo JHtml::_('grid.id', $i, $item->id); ?>
								<?php endif; ?>
							</td>
							<td class="center">
								<div class="btn-group">
									<?php // Check if extension is enabled ?>
									<?php if ($item->enabled > 0) : ?>
										<?php echo JHtml::_('jgrid.published', $item->published, $i, 'modules.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
										<?php
										// Create dropdown items
										JHtml::_('actionsdropdown.duplicate', 'cb' . $i, 'modules');

										$action = $trashed ? 'untrash' : 'trash';
										JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'modules');

										// Render dropdown list
										echo JHtml::_('actionsdropdown.render', $this->escape($item->title));
										?>
									<?php else : ?>
										<?php // Extension is not enabled, show a message that indicates this. ?>
										<button class="btn-micro hasTooltip" title="<?php echo JText::_('COM_MODULES_MSG_MANAGE_EXTENSION_DISABLED'); ?>">
											<i class="icon-ban-circle"></i></button>
									<?php endif; ?>
								</div>
							</td>
							<?php if ($showcolors) : ?>
								<td class="center inlist">
									<?php
									include_once(JPATH_LIBRARIES . '/joomla/form/fields/color.php');
									$colorfield = new JFormFieldColor;

									$color = (isset($item->params->color) && $item->params->color)
										? $color = str_replace('##', '#', $item->params->color)
										: 'none';

									$onchange = 'setColor(\'cb' . $i . '\', this)';

									// For J3.7+ the onchange value needs to actually contain the onchange attribute name... nuts!
									if (JVERSION >= 3.7)
									{
										$onchange = ' onchange=&quot;' . $onchange . '&quot;';
									}

									$element = new SimpleXMLElement(
										'<field
											name="color_' . $i . '"
											type="color"
											control="simple"
											default=""
											colors="' . (isset($this->config->main_colors) ? $this->config->main_colors : '') . '"
											split="4"
											onchange="' . $onchange . '"
											/>'
									);

									$element->value = $color;

									$colorfield->setup($element, $color);

									echo $colorfield->__get('input');
									?>
								</td>
							<?php endif; ?>
							<td class="has-context">
								<div class="pull-left">
									<?php if ($item->checked_out) : ?>
										<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'modules.', $canCheckin); ?>
									<?php endif; ?>
									<?php
									$title   = $this->escape($item->title);
									$tooltip = '<strong>' . JText::_('AMM_EDIT_MODULE') . '</strong><br>' . htmlspecialchars($title);
									if ( ! empty($item->note) && $this->config->show_note == 1)
									{
										$tooltip .= '<br><em>' . htmlspecialchars( $this->escape($item->note)) . '</em>';
									}
									$title = '<span rel="tooltip" title="' . $tooltip . '">' . $title . '</span>';
									?>
									<?php if ($canEdit) : ?>
										<a href="<?php echo JRoute::_('index.php?option=com_advancedmodules&task=module.edit&id=' . (int) $item->id); ?>">
											<?php echo $title; ?></a>
									<?php else : ?>
										<?php echo $title; ?>
									<?php endif; ?>
									<?php if ( ! empty($item->note) && $this->config->show_note == 2) : ?>
										<div class="small">
											<?php echo $this->escape($item->note); ?>
										</div>
									<?php endif; ?>
								</div>
							</td>
							<?php if ($this->config->show_note == 3) : ?>
								<td class="has-context">
									<?php echo $this->escape($item->note); ?>
								</td>
							<?php endif; ?>
							<td class="small hidden-phone">
								<?php if ($item->position) : ?>
									<span class="label label-info">
										<?php echo $item->position; ?>
									</span>
								<?php else : ?>
									<span class="label">
										<?php echo JText::_('JNONE'); ?>
									</span>
								<?php endif; ?>
							</td>
							<td class="small hidden-phone">
								<?php echo $item->name; ?>
							</td>
							<?php if ($client == 'site') : ?>
								<td class="small hidden-phone">
									<?php echo $item->menuid; ?>
								</td>
							<?php endif; ?>
							<td class="small hidden-phone">
								<?php echo $this->escape($item->access_level); ?>
							</td>
							<td class="small hidden-phone">
								<?php
								if (empty($item->language))
								{
									echo JText::_('JDEFAULT');
								}
								elseif ($item->language == '*')
								{
									$advanced_params = json_decode($item->advancedparams);

									if ( ! empty($advanced_params->assignto_languages) && $advanced_params->assignto_languages == 2)
									{
										$text = JText::_('COM_MODULES_ASSIGNED_VARIES_EXCEPT');
									}
									else if ( ! empty($advanced_params->assignto_languages) && ! empty($advanced_params->assignto_languages_selection))
									{
										$text = JText::_('COM_MODULES_ASSIGNED_VARIES_ONLY');
									}
									else
									{
										$text = JText::alt('JALL', 'language');
									}

									echo $text;
								}
								else
								{
									echo $item->language_title
										? JHtml::_('image', 'mod_languages/' . $item->language_image . '.gif', $item->language_title, ['title' => $item->language_title], true) . '&nbsp;' . $this->escape($item->language_title)
										: ((isset($langs[$item->language]) && ! empty($langs[$item->language]['name']))
											? $this->escape($langs[$item->language]['name'])
											: JText::_('JUNDEFINED')
										);
								}
								?>
							</td>
							<td class="hidden-phone">
								<?php echo (int) $item->id; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<?php // Load the batch processing form. ?>
		<?php if ($user->authorise('core.create', 'com_modules')
			&& $user->authorise('core.edit', 'com_modules')
			&& $user->authorise('core.edit.state', 'com_modules')
		) : ?>
			<?php echo JHtml::_(
				'bootstrap.renderModal',
				'collapseModal',
				[
					'title'  => JText::_('COM_MODULES_BATCH_OPTIONS'),
					'footer' => $this->loadTemplate('batch_footer'),
				],
				$this->loadTemplate('batch_body')
			); ?>
		<?php endif; ?>

		<input type="hidden" name="task" value="">
		<input type="hidden" name="boxchecked" value="0">
		<input type="hidden" name="setcolor" value="">
		<?php echo JHtml::_('form.token'); ?>

		<?php if ($this->config->show_switch) : ?>
			<div style="text-align:right">
				<a href="<?php echo JRoute::_('index.php?option=com_modules&force=1'); ?>"><?php echo JText::_('AMM_SWITCH_TO_CORE'); ?></a>
			</div>
		<?php endif; ?>
		<?php

		// Copyright
		echo RL_Version::getFooter('ADVANCED_MODULE_MANAGER', $this->config->show_copyright);
		?>
	</div>
</form>
