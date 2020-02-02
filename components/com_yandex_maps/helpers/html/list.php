<?php
$html = '';
if ($data->count) {
	$id = 'xdsoft_list'.$map->id.'_'.rand(1000,5000);
	$html = '<div id="'.$id.'" class="xdsoft_list_object_items">';
	if ($map->settings->get('show_search_input', 1)) {
		$html.= '	<div class="xdsoft_search_object_list">
				<input type="text" class="xdsoft_search_object_list_input" placeholder="Введите название объекта">
			</div>';
	}
	$html.= '	<div class="items_box">';
	$html.= include 'itemslist.php';
	$html.= '</div>';
	$html.= '</div>';
	$html.= "<script>
		if (window.map{$map->id} === undefined) {
			map{$map->id} = new XDsoftMap(".JHtml::_('map.getOptions', $map, $params).");
			var map  = {id : parseInt({$map->id})};
			map{$map->id}.setMap(map);
		}
		map{$map->id}.list('#{$id}',{$data->count});
	</script>";
}
return $html;