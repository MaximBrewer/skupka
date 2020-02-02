<?php
/**
 * @name		Maximenu CK params
 * @package		com_maximenuck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */
// no direct access
defined('_JEXEC') or die;

// check the joomla! version
if (version_compare(JVERSION, '3.0.0') > 0) {
	$jversion = '3';
} else {
	$jversion = '2';
}

// load the tooltip on the correct css class for joomla 2.5
if ($jversion === '2') {
	JHtml::_('behavior.tooltip', '.hasTooltip');
}

JHtml::_('behavior.multiselect');
// Import CSS
$document = JFactory::getDocument();

$user = JFactory::getUser();
$userId = $user->get('id');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
?>
<script type="text/javascript">
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_maximenuck'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="clearfix"> </div>
	<?php if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) { ?>
		<div class="alert alert-error">
			<h4 class="alert-heading">IMPORTANT</h4>
			<p>You must disable the 'magic_quotes_gpc' on your server else you will have some problems into the component</p>
		</div>
	<?php } ?>
	<div id="filter-bar" class="btn-toolbar">
		<div class="filter-search btn-group pull-left">
			<label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo JText::_('COM_MODULES_MODULES_FILTER_SEARCH_DESC'); ?>" />
		</div>
		<div class="btn-group pull-left hidden-phone">
			<button type="submit" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i><?php echo ($jversion === '2' ? JText::_('JSEARCH_FILTER_SUBMIT') : ''); ?></button>
			<button type="button" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value = '';
					this.form.submit();"><i class="icon-remove"></i><?php echo ($jversion === '2' ? JText::_('JSEARCH_FILTER_CLEAR') : ''); ?></button>
		</div>
			<?php if ($jversion === '3') { ?>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
			<?php echo $this->pagination->getLimitBox(); ?>
			</div>
			<?php } ?>
	</div>
	<div class="clearfix"> </div>
    <table class="table table-striped" id="templateckList">
        <thead>
            <tr>
                <th width="1%">
                    <input type="checkbox" name="checkall-toggle" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" value="" onclick="Joomla.checkAll(this)" />
                </th>

                <th class='left'>
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                </th>
				<th width="1%" class="nowrap center">
					<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
				</th>
				<th width="15%" class="nowrap hidden-phone">
				<?php echo JHtml::_('grid.sort', 'COM_MODULES_HEADING_POSITION', 'position', $listDirn, $listOrder); ?>
				</th>
				<?php if (isset($this->items[0]->state)) { ?>
				<?php } ?>
				<?php if (isset($this->items[0]->id)) {
					?>
					<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
				<?php } ?>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="10">
					<?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
			<?php
			foreach ($this->items as $i => $item) :
				$canCreate = $user->authorise('core.create', 'com_maximenuck');
				$canEdit = $user->authorise('core.edit', 'com_maximenuck');
				$canCheckin = $user->authorise('core.manage', 'com_maximenuck');
				$canChange = $user->authorise('core.edit.state', 'com_maximenuck');
				$link = 'index.php?option=com_maximenuck&view=styles&id=' . $item->id;
				?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="center">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>

					<td>
						<a href="javascript:void(0)" onclick="CKBox.open({handler:'iframe', fullscreen: true, url:'<?php echo JUri::root(true) . '/administrator/' . $link ?>&layout=modal'})"><?php echo $item->title; ?></a>
					</td>
					<td class="center">
						<div class="btn-group">
							<?php echo JHtml::_('modules.state', $item->published, $i, false, 'cb'); ?>
						</div>
					</td>
					<td class="small">
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

					<?php if (isset($this->items[0]->id)) {
						?>
						<td class="center">
						<?php echo (int) $item->id; ?>
						</td>
					<?php } ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
    </div>
</form>