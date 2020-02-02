<?php
/**
 * @name		Accordeon Menu CK params
 * @package		com_accordeonck
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
<form action="<?php echo JRoute::_('index.php?option=com_accordeonck&view=menus'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="clearfix"> </div>
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
                <th class='left'>
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                </th>
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
			foreach ($this->menus as $i => $item) :
				$link = 'index.php?option=com_accordeonck&view=items&menutype=' . $item->menutype;
				?>
				<tr class="row<?php echo $i % 2; ?>">
					<td>
						<a href="javascript:void(0)" onclick="CKBox.open({handler:'iframe', fullscreen: true, url:'<?php echo JUri::root(true) . '/administrator/' . $link ?>&layout=modal'})"><?php echo $item->title; ?></a>
					</td>
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