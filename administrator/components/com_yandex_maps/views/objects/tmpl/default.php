<?php
defined("_JEXEC") or die("Access deny");
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

include JPATH_COMPONENT_ADMINISTRATOR.'/helpers/main_menu.php';

$listOrder	= $this->escape($this->state->get('list.ordering','id'));
$listDirn	= $this->escape($this->state->get('list.direction','desc'));

$saveOrder    = $listOrder == 'a.ordering';
if ($saveOrder){
	$saveOrderingUrl = 'index.php?option=com_yandex_maps&task=objects.saveOrderAjax';
	JHtml::_('sortablelist.sortable', 'itemsList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
?>
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
	<?php } else {?>
	<table id="itemsList" class="table table-striped table-hover">
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
			<th><?php echo JHTML::_( 'searchtools.sort', 'Тип', 'a.type', $listDirn, $listOrder); ?></th>
			<th>Карта</th>
			<th>Категория</th>
			<!--<th><?php echo JHTML::_( 'searchtools.sort', 'Карта', 'm.title', $listDirn, $listOrder); ?></th>
			<th><?php echo JHTML::_( 'searchtools.sort', 'Категория', 'c.title', $listDirn, $listOrder); ?></th>-->
			<th><?php echo JHTML::_( 'searchtools.sort', 'Автор', 'author', $listDirn, $listOrder); ?></th>
			<th style="width:130px;text-align:center;"><?php echo JHTML::_( 'searchtools.sort', 'Обновлено', 'a.modified_time', $listDirn, $listOrder); ?></th>
			<th><?php echo JHTML::_( 'searchtools.sort', 'ID', 'a.id', $listDirn, $listOrder); ?></th>
		</tr>
	  </thead>
	  <tbody>
		<?php 
		$factory = JModelLegacy::getInstance('Objects', 'Yandex_MapsModel');
		$factory->fast = true;
		foreach($this->items as $i=>$item){
			$_item = $factory->model($item->id);
			?>
			<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo @$_item->category->id; ?>">
				<td class="order nowrap center hidden-phone">
					<?php 
						$iconClass = '';
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
					<a class="btn btn-micro <?php echo $item->active ? 'active' : ''?>" href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i?>','objects.<?php echo $item->active?'unpublish':'publish'?>')"><i class="icon-<?php echo $item->active?'publish':'unpublish'?>"></i></a>
					<a class="btn btn-micro" title="<?php echo "Просмотр объекта на сайте"?>" target="_blank" href="<?php echo JRoute::_(JURI::root().'index.php?option=com_yandex_maps&task=object&id=' . (int) @$item->id)?>">
						<img style="height:14px;" src="<?php echo JURI::root().'/media/com_yandex_maps/images/view.png'?>">
					</a>
					<a class="btn btn-micro" title="<?php echo "Скопировать карту"?>" target="_blank" href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i?>','objects.copy')"><img src="<?php echo JURI::root().'/media/com_yandex_maps/images/copy.png'?>"></a>
				</td>
				<td><a href="<?php echo JRoute::_('index.php?option=com_yandex_maps&task=objects.edit&id=' . (int) @$item->id)?>"><?php echo $item->title?></a></td>
				<td><a href="<?php echo JRoute::_('index.php?option=com_yandex_maps&task=objects.edit&id=' . (int) @$item->id)?>"><img class="img-circle" src="<?php echo JURI::root()?>/media/com_yandex_maps/images/<?php echo $item->type?>.png"></a></td>
				<td><a href="<?php echo JRoute::_('index.php?option=com_yandex_maps&task=maps.edit&id=' . (int) @$_item->map->id)?>"><?php echo $_item->map->title?></a></td>
				<td><a href="<?php echo JRoute::_('index.php?option=com_yandex_maps&task=categories.edit&id=' . (int) @$_item->category->id)?>"><?php echo $_item->category->title?></a></td>
				<td><?php echo $item->author?></td>
				<td style="text-align:center;line-height:0.8em;"><small><?php echo JHTML::_('date', $item->modified_time , 'd.m.Y<\b\r>H:i')?></small></td>
				<td><?php echo $item->id?></td>
			</tr>
		<?php }?>
	  </tbody>
	  <tfoot>
		<tr>
			<td colspan="10"><?php echo $this->pagination->getListFooter(); ?></td>
		</tr>
	  </tfoot>
	</table>
	<?php }?>
	<input type="hidden" name="option" value="com_yandex_maps">
	<input type="hidden" name="task" value="objects">
	<input type="hidden" name="boxchecked" value="0" />
	<!--<input type="hidden" name="filter_order" value="<?php echo $this->escape($listOrder); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($listDirn); ?>" />-->
	<?php echo JHtml::_('form.token'); ?>
</form>