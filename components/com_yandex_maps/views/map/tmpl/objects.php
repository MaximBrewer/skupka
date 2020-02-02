<?php
defined("_JEXEC") or die("Access deny");
JHtml::addIncludePath(JPATH_ROOT.'/administrator/components/com_yandex_maps/helpers/html');
if (empty($map) and $this->map) {
	$map = $this->map;
}
if (empty($params) and $this->params) {
	$params = $this->params;
}
?>
<div class="xdsoft_col<?php echo $map->settings->get('ratio_map_to_list', 4)?> xdsoft_object_list_box">
<?php
	$data = (object)$map->getObjectsbyBound(null, 0, $map->settings->get('max_count_object', 100));
	echo include JHtml::_('xdwork.includePHP', 'helpers/html/list.php');
?>
</div>
