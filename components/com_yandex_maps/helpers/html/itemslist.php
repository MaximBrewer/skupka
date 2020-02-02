<?php
$category_id = 0;
$parts = [];

if (!isset($map)) {
	return '';
}

$settings = $map->settings;

if (empty($settings)) {
	return '';
}

$opened = $settings->get('howmoveto_on_category_change', 2)!=4;

if (!isset($data) or !$data->objects or !is_array($data->objects)) {
	return '';
}


foreach ($data->objects as $object) {

	if ($settings->get('show_category', 1) && $category_id !== $object->category_id) {
		$category_id = $object->category_id;
		$parts[] = '<a class="category_item category_' . $category_id . ' ' . ($opened ? 'xdsoft_open' :  'xdsoft_close').'" href="'.$object->category_link.'" onclick="return map' . $map->id . '.goToCategory(\''.$object->category_id.'\')">' . $object->category_title . '</a>';
	}

	if ($settings->get('show_objects', 1)) {
		$parts[] = '<a class="xdsoft_category_id' . $category_id . ' ' . ($opened ? '' :  'xdsoft_hidden').'" ' . ($settings->get('howmoveto', 0) == 2 ? 'href="' . $object->link . '"' : '') . ' onclick="return map' . $map->id . '.goTo(\'' . $object->id . '\')">' . $object->title . '</a>';
	}
}

return implode('', $parts);