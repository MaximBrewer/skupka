<?php
defined("_JEXEC") or die("Access deny");
JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

include JPATH_COMPONENT_ADMINISTRATOR.'/helpers/main_menu.php';

$listOrder	= $this->escape($this->state->get('list.ordering','id'));
$listDirn	= strtoupper($this->escape($this->state->get('list.direction','desc')));

$saveOrder    = 	$listOrder == 'a.ordering';
if ($saveOrder){
	$saveOrderingUrl = 'index.php?option=com_yandex_maps&task=maps.saveOrderAjax';
	JHtml::_('sortablelist.sortable', 'mapsList', 'adminForm', $listDirn, $saveOrderingUrl);
}

$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>
<form method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
	<div class="row-fluid">
		<div class="span12">
		<?php
			// Search tools bar
			echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>
		</div>
	</div>
	<?php if (!count($this->items)) {?>
		<div class="alert alert-no-items"><?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></div>
	<?php } else { ?>
	<table id="mapsList" class="table table-striped table-hover">
	  <thead>
		<tr>
			<th width="1%" class="nowrap center hidden-phone">
				<?php //echo JHTML::_( 'grid.sort', 'Сортировка', 'ordering', $listDirn, $listOrder); ?>
				<?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
			</th>
			<th width="1%" class="center">
				<?php echo JHtml::_('grid.checkall'); ?>
			</th>
			<th>Управление</th>
			<th><?php echo JHTML::_( 'searchtools.sort', 'Название', 'a.title', $listDirn, $listOrder); ?></th>
			<th><?php echo JHTML::_( 'searchtools.sort', 'Объектов', 'objects_count', $listDirn, $listOrder); ?></th>
			<th><?php echo JHTML::_( 'searchtools.sort', 'Автор', 'a.author', $listDirn, $listOrder); ?></th>
			<th><?php echo JHTML::_( 'searchtools.sort', 'Дата', 'a.modified_time', $listDirn, $listOrder); ?></th>
			<th><?php echo JHTML::_( 'searchtools.sort', 'ID', 'a.id', $listDirn, $listOrder); ?></th>
		</tr>
	  </thead>
	  <tbody>
		<?php foreach($this->items as $i=>$item){?>
			<tr sortable-group-id="1">
				<td class="order nowrap center hidden-phone">
					<?php 
						if (!$saveOrder){
							$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
						}
					?>
					<span class="sortable-handler <?php echo $iconClass;?>">
						<i class="icon-menu"></i>
					</span>
					<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
				</td>
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<a class="btn btn-micro" title="Редактировать карту без загрузки объектов" href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i?>','maps.edit')"><i class="icon-edit"></i></a>
					<a class="btn btn-micro <?php echo $item->active ? 'active' : ''?>" href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i?>','maps.<?php echo $item->active?'unpublish':'publish'?>')"><i class="icon-<?php echo $item->active?'publish':'unpublish'?>"></i></a>
					<a class="btn btn-micro" title="<?php echo "Просмотр карты на сайте"?>" target="_blank" href="<?php echo JRoute::_(JURI::root().'index.php?option=com_yandex_maps&task=map&id=' . (int) @$item->id)?>">
						<img style="height:14px;" src="<?php echo JURI::root().'/media/com_yandex_maps/images/view.png'?>">
					</a>
					<div class="btn-group">
						<a class="btn btn-micro" title="<?php echo "Скопировать карту"?>" href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i?>','maps.copy')">
							<img style="height:14px;" src="<?php echo JURI::root().'/media/com_yandex_maps/images/copy.png'?>">
						</a>
						<button class="btn btn-micro dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
						<ul class="dropdown-menu">
							<li><a href="javascript:void(0);" onclick="jQuery('#mode').val(3); return listItemTask('cb<?php echo $i?>','maps.copy')">Скопировать карту со всеми связями</a></li>
							<li><a href="javascript:void(0);" onclick="jQuery('#mode').val(1); return listItemTask('cb<?php echo $i?>','maps.copy')">Скопировать карту с категориями</a></li>
							<li><a href="javascript:void(0);" onclick="jQuery('#mode').val(2); return listItemTask('cb<?php echo $i?>','maps.copy')">Скопировать карту с категориями и объектами</a></li>
							<li class="divider"></li>
						</ul>
					</div>
					
				</td>
				<td><a href="<?php echo JRoute::_('index.php?option=com_yandex_maps&task=maps.edit&id=' . (int) @$item->id)?>"><?php echo $item->title?></a></td>
				<td><a href="<?php echo JRoute::_('index.php?option=com_yandex_maps&task=objects&filter[mapid]=' . (int) @$item->id)?>"><?php echo $item->objects_count?></a></td>
				<td><?php echo $item->author?></td>
				<td><?php echo JHTML::_('date', $item->modified_time)?></td>
				<td><?php echo $item->id?></td>
			</tr>
		<?php }?>
	  </tbody>
	  <tfoot>
		<tr>
			<td colspan="8"><?php echo $this->pagination->getListFooter(); ?></td>
		</tr>
	  </tfoot>
	</table>
	<?php } ?>
	<input type="hidden" name="option" value="com_yandex_maps">
	<input type="hidden" name="task" value="maps">
	<input type="hidden" id="mode" name="mode" value="0">
	<input type="hidden" name="boxchecked" value="0" />
	<!--<input type="hidden" name="filter_order" value="<?php echo $this->escape($listOrder); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($listDirn); ?>" />-->
	<?php echo JHtml::_('form.token'); ?>
</form>