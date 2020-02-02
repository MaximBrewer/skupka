<?php
/**
 * @package        Joomla.Administrator
 * @subpakage    Skyline.AdvPoll
 * @copyright    Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

if (JFactory::getApplication()->isSite()) {
	JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));
}

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');

$function = JRequest::getCmd('function', 'jSelectPoll');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$editor	= JRequest::getString('e_name');
?>
<form action="<?php echo JRoute::_('index.php?option=com_sl_advpoll&view=polls&layout=modal&tmpl=component&function=' . $function . '&' . JSession::getFormToken() . '=1');?>"
	  method="post" name="adminForm" id="adminForm">
	<fieldset class="filter clearfix">
		<div class="pull-left">
			<label for="filter_search" style="float: left">
				<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>
			</label>
			<input type="text" name="filter_search" id="filter_search"
				   value="<?php echo $this->escape($this->state->get('filter.search')); ?>" size="30"
				   title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" style="float: left"/>

<!--			<button type="submit">--><?php //echo JText::_('JSEARCH_FILTER_SUBMIT'); ?><!--</button>-->
<!--			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();">--><?php //echo JText::_('JSEARCH_FILTER_CLEAR'); ?><!--</button>-->
			<div class="btn-group pull-left">
				<button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button class="btn hasTooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
		</div>

		<div class="pull-right">

			<select name="filter_state" class="inputbox" onchange="this.form.submit()" style="width: 150px">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true);?>
			</select>

			<select name="filter_category_id" class="inputbox" onchange="this.form.submit()" style="width: 150px">
				<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_sl_advpoll'), 'value', 'text', $this->state->get('filter.category_id'));?>
			</select>
		</div>
	</fieldset>

	<table class="table table-striped">
		<thead>
		<tr>
			<th class="title">
				<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
			</th>
			<th width="15%" class="nowrap hidden-phone">
				<?php echo JHtml::_('grid.sort', 'JCATEGORY', 'a.catid', $listDirn, $listOrder); ?>
			</th>
			<th width="5%" class="nowrap hidden-phone">
				<?php echo JHtml::_('grid.sort', 'JDATE', 'a.created', $listDirn, $listOrder); ?>
			</th>
			<th width="1%" class="nowrap hidden-phone">
				<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
			</th>
		</tr>
		</thead>
		<tfoot>
		<tr>
			<td colspan="15">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) : ?>
		<tr class="row<?php echo $i % 2; ?>">
			<td class="nowrap">
				<a class="pointer"
				   onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->title)); ?>', '<?php echo $this->escape($item->catid); ?>', '<?php echo $editor; ?>');">
					<?php echo $this->escape($item->title); ?></a>
			</td>
			<td class="small">
				<?php echo $this->escape($item->category_title); ?>
			</td>
			<td class="center nowrap">
				<?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
			</td>
			<td class="center">
				<?php echo (int)$item->id; ?>
			</td>
		</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

