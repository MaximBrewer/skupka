<?php defined('_JEXEC') or die; ob_start();?>
<?php
$uid = uniqid();
?>
<div id="xdm_wg_sidebar<?php echo $uid?>" class="xdm_wg_sidebar xdm_wg_position<?=$map->settings->get('show_widget_nearest_object', 0)?>">
	<div class="xdm_wg_sidebar_view">
		<div class="xdm_wg_sidebar_content">
			<div class="xdm_wg_header">
				<div class="xdm_wg_title"><?=$map->settings->get('wdm_widget_name', 'Объекты а карте')?></div>
			</div>
			<div class="xdm_wg_wrapper">
				<div class="xdm_wg_wrapper_scroller">
				<?=include 'ajax/widget.php'; ?>
				</div>
			</div>
			<div class="xdm_loading_indicator <?=$map->settings->get('wdm_widget_loading_height_overflow', 1) ? '' : 'fix'?>">
				<div class="xdm_loading_loading_text"><?=$map->settings->get('wdm_widget_loading_title', 'Загрузка...')?></div>
				<div class="xdm_loading_loading_small"  data-block="spinner-view">
					<div class="xdm_loading_loading_small_circle"></div>
				</div>
			</div>
			<?php if ($map->settings->get('show_wdm_close_button', 1)) { ?>
			<div title="Закрыть" class="xdm_wg_close"></div>
			<?php } ?>
			<?php if ($map->settings->get('show_wdm_collapse_button', 1)) { ?>
			<div title="Развернуть" class="xdm_wg_expand"></div>
			<div title="Свернуть" class="xdm_wg_collapse"></div>
			<?php } ?>
		</div>
	</div>
</div>
<script>
if (window.map<?=$map->id?> === undefined) {
	map<?=$map->id?> = new XDsoftMap(<?=JHtml::_('map.getOptions', $map, $params)?>);
	var map  = {id : parseInt(<?php echo $map->id?>, 10)};
	map<?=$map->id?>.setMap(map);
}
(function($){
	var $xdm_wg_sidebar = $('#xdm_wg_sidebar<?php echo $uid?>'),
		$xdm_wg_wrapper = $xdm_wg_sidebar.find('.xdm_wg_wrapper'),
		$xdm_wg_wrapper_scroller = $xdm_wg_wrapper.find('.xdm_wg_wrapper_scroller'),
		xhr,
		center,
		bounds,
		page = 1,
		count_on_page = <?php echo $map->settings->get('wdm_widget_count_on_page', 10)?>;

	$(window)
		.on('resize.xdsoft updatesize.xdsoft', function () {
			var h = parseInt($xdm_wg_sidebar[0].offsetHeight, 10);
			$xdm_wg_wrapper.css('max-height', (h - 40) + 'px');
		});

	$xdm_wg_wrapper
		.on('scroll', function () {
			if (Math.abs($(this).scrollTop() - $xdm_wg_wrapper_scroller.height() + $xdm_wg_wrapper.height()) < <?php echo $map->settings->get('wdm_widget_px_by_bottom_start_ajax', 300)?>) {
				openPage(page);
			}
		});

	$(function () {
		$(window).trigger('updatesize.xdsoft');
	});

	$xdm_wg_sidebar.find('.xdm_wg_collapse').on('click', function () {
		$xdm_wg_sidebar.addClass('_collapsed');
	});

	$xdm_wg_sidebar.find('.xdm_wg_expand').on('click', function () {
		$xdm_wg_sidebar.removeClass('_collapsed');
	});

	$xdm_wg_sidebar.find('.xdm_wg_close').on('click', function () {
		$xdm_wg_sidebar.addClass('_hidden');
	});
	
	function openPage(_page) {
		if (xhr) {
			xhr.abort();
		}

		$xdm_wg_sidebar.addClass('_show_loading');

		xhr = $.post("<?php echo JURI::root()?>index.php?option=com_yandex_maps&task=widget&name=widget", {bound: bounds, center: center, map_id: <?php echo $map->id?>, limit: count_on_page, offset : count_on_page * (_page - 1)}, function (resp) {
			if (page === 1) {
				$xdm_wg_wrapper_scroller.html(resp);
			} else {
				$xdm_wg_wrapper_scroller.append(resp);
			}

			$xdm_wg_sidebar.removeClass('_show_loading');
			page++;
		});
	}

	map<?php echo $map->id?>.events.on('boundschange', function (_center, _bounds) {
		center = _center;
		bounds = _bounds;
		page = 1;
		openPage(page);
	});
}(jQuery))
</script>
<?php if ($map->settings->get('wdm_widget_width')) { 
	$width = $map->settings->get('wdm_widget_width');
	$znak = (is_numeric($width) || preg_match('#[0-9]+px$#', $width)) ? 'px' : '%';
	$width = (float)$width;
?>
<style>
#xdm_wg_sidebar<?php echo $uid?> .xdm_wg_sidebar_view{
    width: <?php echo $width.$znak?>;
    margin-left: -<?php echo $width.$znak?>;
}
#xdm_wg_sidebar<?php echo $uid?>.xdm_wg_position3 .xdm_wg_sidebar_view{width:<?php echo $width.$znak?>;margin-left: -<?php echo round($width/2).$znak?>;}
#xdm_wg_sidebar<?php echo $uid?>.xdm_wg_position4 .xdm_wg_sidebar_view{width:<?php echo $width.$znak?>;margin-left: -<?php echo round($width/2).$znak?>;}
</style>
<?php } ?>
<?php 
return ob_get_clean();