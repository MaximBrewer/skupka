<?php
/**
 * @name		Slider CK
 * @package		com_slideshowck
 * @copyright	Copyright (C) 2016. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */

// no direct access
defined('_JEXEC') or die;

use \Slideshowck\CKFof;

// load the lightbox
SlideshowckHelper::loadCkbox();
\Slideshowck\CKFramework::load();
\Slideshowck\CKFramework::loadFaIconsInline();

// vars
$modal = $this->input->get('layout', '') == 'modal' ? true : false;
//JHtml::_('behavior.tooltip');
//JHtml::_('behavior.multiselect');

$user = JFactory::getUser();
$userId = $user->get('id');
// for ordering
$listOrder = $this->state->get('filter_order', 'a.id');
$listDirn = $this->state->get('filter_order_Dir', 'ASC');
$filter_search = $this->state->get('filter_search', '');
$limitstart = $this->state->get('limitstart', 0);
$limit = $this->state->get('limit', 20);

$isModal = $this->input->get('layout', '', 'string') == 'modal';
$function = $this->input->get('returnFunc', 'ckSelectStyle', 'string');
$appendUrl = $isModal ? '&layout=modal&tmpl=component' : '';
?>
<style>
	body.contentpane {
		padding-top: 125px;
	}
</style>
<form action="<?php echo JRoute::_('index.php?option=com_slideshowck&view=styles'.$appendUrl); ?>" method="post" name="adminForm" id="adminForm">
	<?php if ($isModal) { ?>
	<div id="ckheader">
		<div class="ckheaderlogo"><a href="https://www.joomlack.fr" target="_blank"><img title="JoomlaCK" src="https://media.joomlack.fr/images/logo_ck_white.png" width="35" height="35"></a></div>
		<div class="ckheadermenu">
			<div class="ckheadertitle">SLIDESHOW CK - <?php echo JText::_('CK_STYLES'); ?></div>
		</div>
	</div>
	<div id="cktoolbar-fixed">
		<?php echo $this->toolbar->render(); ?>
	</div>
	<?php } ?>
	<div id="filter-bar" class="btn-toolbar">
		<div class="filter-search ckbutton-group">
			<label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->state->get('filter_search'); ?>" class="hasTooltip" title="" />
			<button type="submit" class="ckbutton hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
			<button type="button" class="ckbutton hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="this.form.filter_search.value = '';
					this.form.submit();"><i class="icon-remove"></i></button>
		</div>
			<div class="ckbutton-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
			<?php echo $this->pagination->getLimitBox(); ?>
			</div>
	</div>
	<table class="table table-striped" id="itemsList">
		<thead>
			<tr>
				<?php if (CKFof::userCan('create') || CKFof::userCan('edit')) { ?>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" value="" onclick="Joomla.checkAll(this)" />
				</th>
				<?php } ?>
				<th class='left'>
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($this->items as $i => $item) :
				$link = 'index.php?option=com_slideshowck&view=style&layout=modal&tmpl=component&id=' . $item->id;
				$name = $item->name ? $item->name : 'style' . $item->id;
				?>
				<tr class="row<?php echo $i % 2; ?>">
					<?php if (CKFof::userCan('create') || CKFof::userCan('edit')) { ?>
					<td class="center">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<?php } ?>
					<td>
						<?php if ($modal) { ?>
						<a href="javascript:void(0)" onclick="window.parent.<?php echo $function ?>('<?php echo $item->id; ?>', '<?php echo $name; ?>')"><?php echo $name; ?></a>
						<?php /*<a href="<?php echo JUri::root(true) . '/administrator/' . $link ?>" class="ckbutton"><?php echo JText::_('CK_EDIT'); ?></a>*/ ?>
						<?php } else { ?>
						<a onclick="CKBox.open({handler:'iframe', fullscreen: true, url:'<?php echo JUri::root(true) . '/administrator/' . $link ?>'})" href="#"><?php echo $name; ?></a>
						<?php } ?>
					</td>
					<td class="center">
					<?php echo (int) $item->id; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php echo $this->pagination->getListFooter() ?>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php \Slideshowck\CKFof::renderToken() ?>
	</div>
</form>