<?php
defined("_JEXEC") or die("Access deny");

JHtml::addIncludePath(JPATH_ROOT . '/administrator/components/com_yandex_maps/helpers/html');

if (empty($map) and $this->map) {
	$map = $this->map;
}
if (empty($params) and $this->params) {
	$params = $this->params;
}

?>
<div class="xdsoft_yandex_maps xdsoft_map<?=$map->id;?> <?=$map->settings->get('map_on_fullscreen',0) ? 'xdsoft_fullscreen' : '';?> map xdsoft_with_object_list<?php echo $map->settings->get('show_object_list',1);?> <?php echo ($map->settings->get('show_search_on_map',1) or ($map->settings->get('show_select_category',1) and count($map->categories))) ? 'xdsoft_map_with_navigate' : ''?>">
	<?php if ($map->settings->get('show_title_map', 1)) { ?>
	<h1><?=$map->title?></h1>
	<?php } ?>
	<?php if ($map->settings->get('show_map_description', 1)) { ?>
	<div class="map_description"><?php echo $map->full?></div>
	<?php } ?>
	<?php if ($map->settings->get('show_map',1)) { ?>
	<div class="map_view xdsoft_row">
        <?php if (in_array($map->settings->get('show_category_filter', 0), array(8))) {?>
			<?php
				echo include JHtml::_('xdwork.includePHP', 'helpers/html/filter.php');
			?>
		<?php } ?>
		<?php if ($map->settings->get('show_object_list',1)==1 or $map->settings->get('show_object_list',1)==4) { ?>
		<div class="xdsoft_col<?=($map->settings->get('show_object_list',1)==1 ? $map->settings->get('ratio_map_to_list', 4) : 12)?> xdsoft_object_list_box">
			<?php if (in_array($map->settings->get('show_category_filter', 0), array(6))) {?>
				<?php
					echo include JHtml::_('xdwork.includePHP', 'helpers/html/filter.php');
				?>
			<?php } ?>
			<?php
				$data = (object)$map->getObjectsbyBound(null, 0, $map->settings->get('max_count_object', 100));
				echo include JHtml::_('xdwork.includePHP', 'helpers/html/list.php');
			?>
			<?php if (in_array($map->settings->get('show_category_filter', 0), array(5))) {?>
				<?php
					echo include JHtml::_('xdwork.includePHP', 'helpers/html/filter.php');
				?>
			<?php } ?>
            <?php echo jhtml::_('xdwork.modules', 'yandex_maps_sidebar')?>
		</div>
		<?php } ?>
		<div class="xdsoft_col<?php echo 12 - ((int)in_array((int)$map->settings->get('show_object_list',1), array(1, 2)) ? $map->settings->get('ratio_map_to_list', 4) : 0)?> xdsoft_map_box" <?php echo $map->settings->get('map_style') ? 'style="'.$map->settings->get('map_style').'"' : '';?>>
			<?php if ($map->settings->get('show_search_on_map',1) or ($map->settings->get('show_select_category',1) and count($map->categories))) { ?>
            <div class="xdsoft_navigate_box xdsoft_row">
				<?php if ($map->settings->get('show_search_on_map',1)) {?>
				<div class="xdsoft_search_on_map xdsoft_col6">
					<?php
						echo include JHtml::_('xdwork.includePHP', 'helpers/html/search.php');
					?>
				</div>
				<?php } ?>
				<?php if ($map->settings->get('show_select_category',1) and count($map->categories)) {?>
				<div class="xdsoft_select_category_box xdsoft_col6">
					<?php
						echo include JHtml::_('xdwork.includePHP', 'helpers/html/categoriesselect.php');
					?>
				</div>
				<?php } ?>
			</div>
            <?php } ?>

            <?php if ($map->settings->get('show_description_in_custom_balloon', 0)) {
                // выгрузка описания объекта в отдельный виджет
                echo include JHtml::_('xdwork.includePHP', 'helpers/html/customballoon.php');
            } ?>
			<?php if ($map->settings->get('show_widget_nearest_object', 0)) {
                // виджет ближайшие объекты
				echo include JHtml::_('xdwork.includePHP', 'helpers/html/widget.php');
			} ?>

			<?php if (in_array($map->settings->get('show_category_filter', 0), array(1, 4,))) {?>
				<?php
					echo include JHtml::_('xdwork.includePHP', 'helpers/html/filter.php');
				?>
			<?php } ?>
			<?php echo JHtml::_('map.show',$map)?>
			
			<?php if (in_array($map->settings->get('show_category_filter', 0), array(2, 3,))) {?>
				<?php
					echo include JHtml::_('xdwork.includePHP', 'helpers/html/filter.php');
				?>
			<?php } ?>
			<?php if ($map->settings->get('show_btn_add_new_object',0)) { ?>
			<div class="xdsoft_navigate_box xdsoft_row">
				<?php
					echo include JHtml::_('xdwork.includePHP', 'helpers/html/addbtn.php');
				?>
			</div>
			<?php } ?>
		</div>
		<?php if ($map->settings->get('show_object_list',1)==2 or $map->settings->get('show_object_list',1)==3) { ?>
		<div class="xdsoft_col<?php echo ($map->settings->get('show_object_list',1)==2 ? $map->settings->get('ratio_map_to_list', 4) : 12)?> xdsoft_object_list_box">
			<?php if (in_array($map->settings->get('show_category_filter', 0), array(6))) {?>
				<?php
					echo include JHtml::_('xdwork.includePHP', 'helpers/html/filter.php');
				?>
			<?php } ?>
			<?php
				$data = (object)$map->getObjectsbyBound(null, 0, $map->settings->get('max_count_object', 100));
				echo include JHtml::_('xdwork.includePHP', 'helpers/html/list.php');
			?>
			<?php if (in_array($map->settings->get('show_category_filter', 0), array(5))) {?>
				<?php
					echo include JHtml::_('xdwork.includePHP', 'helpers/html/filter.php');
				?>
			<?php } ?>
		</div>
		<?php } ?>
		<?php if (in_array($map->settings->get('show_category_filter', 0), array(7))) { ?>
			<?php
				echo include JHtml::_('xdwork.includePHP', 'helpers/html/filter.php');
			?>
		<?php } ?>
	</div>
	<?php } ?>
</div>