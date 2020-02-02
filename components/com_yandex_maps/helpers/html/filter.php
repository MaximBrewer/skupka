<?php 
defined('_JEXEC') or die; ob_start();
$dispatcher = JEventDispatcher::getInstance();
JPluginHelper::importPlugin('yandexmapssource');
JPluginHelper::importPlugin('system');
$filters = [];
$dispatcher->trigger('generateFilter', [&$filters, $map]);
$id = uniqid();
?>
<div style="min-height:<?=$map->settings->get('filter_height', 210);?>px;margin-top:-<?=round($map->settings->get('filter_height', 210)/2);?>px;<?=$map->settings->get('filter_extended_style', '');?>" class="xdsoft_filter <?=$map->settings->get('filter_style', 1) ? 'xdsoft_filter_vertical' : 'xdsoft_filter_horizontal'?> xdsoft_filter_<?=$map->settings->get('show_category_filter', 0);?>">
	<div class="xdsoft_filter_wrapper">
        <?php if ($map->settings->get('show_label_filter', 1) and $map->settings->get('label_category_filter')) {?>
        <h4><?=$map->settings->get('label_category_filter');?></h4>
        <?php } ?>
        <div class="xdsoft_filter_items" style="height:<?php echo $map->settings->get('filter_height', 210) - 50;?>px;">
            <?php foreach ($filters as $filter) { ?>
            <div class="xdsoft_filter_item">
                <?=$filter?>
            </div>
            <?php } ?>
        </div>
        <?php if (in_array($map->settings->get('show_category_filter', 0), [1, 2, 3, 4])) {?>
            <a href="#" id="toggleFilter<?php echo $id?>" class="hide_panel"><span>Скрыть панель</span></a>
        <?php } ?>
    </div>
</div>
<script>
(function($){
	$('.xdsoft_filter #toggleFilter<?=$id?>').click(function() {
		$(this).closest('.xdsoft_filter').toggleClass('filterhide');
		return false;
	});
}(window.XDjQuery || window.jQ || window.jQuery))
</script>
<?php
return ob_get_clean();