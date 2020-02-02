<?php
$html = '<select class="xdsoft_select_category_input xdsoft_select_category_input' . $map->id . '" onchange="map' . $map->id . '.goToCategory(jQuery(this).val(), jQuery(this).find(\'options:selected\').attr(\'href\'))">';
$html.= '<option href="#" value="0">Выбор категории</option>';
foreach($map->categoriesex as $i => $category){
	$html.= '<option href="' . JRoute::_('index.php?option=com_yandex_maps&task=category&id=' . $category->slug) . '" value="' . $category->id . '">' . $category->title . '</option>';
}
$html.= '</select>';
return $html;
