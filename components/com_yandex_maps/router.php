<?php
function getXDSoftItemid ($task, $id = null){
    $items = JFactory::getApplication()->getMenu('site')->getItems('component', 'com_yandex_maps');
	foreach ($items as $item ) {
        if((isset($item->query['task']) and $item->query['task'] === $task and (!$id or $item->query['id'] === $id))
			or (isset($item->query['view']) and $item->query['view'] === $task and (!$id or $item->query['id'] === $id))){
            return $item;
        }
    }
	return null;
}
function Yandex_MapsParseRoute($segments) {
	$vars = array();
	$doc = JFactory::getDocument();
	$doc->setBase(JURI::base());
	$itemId = null;
	if( count($segments) ){
		switch($segments[0]){
			case 'registration':
				$vars['view'] = 'registration';
				$vars['id'] = (int)$segments[1];
			break;
			case 'map':
				$vars['task'] = 'map';
				$vars['id'] = (int)$segments[1];
			break;
			case 'category':
				$vars['task'] = 'category';
				$vars['id'] = (int)$segments[1];
			break;
			case 'object':
				$vars['task'] = 'object';
				$vars['id'] = (int)$segments[1];
			break;
			default:
				$vars['view'] = $vars['task'] = $segments[0];
			break;
		}
	}

    return $vars;
}
function Yandex_MapsBuildRoute( &$query  ){
	$segments = array();

	if (isset($query['task'])) {		
		switch ($query['task']) {
			case 'map':
				@list($id, $alias) = explode(':', $query['id']);
				$item = getXDSoftItemid('map', $id);
				if (!$item) {
					$item = getXDSoftItemid('yandex_maps');
					if ($item) {
						$query['Itemid'] = getXDSoftItemid('yandex_maps')->id;
					}
					$segments = array('map', $query['id']);
				} else {
					$query['Itemid'] = $item->id;
				}
				unset($query['id']);
				unset($query['task']);
			break;
			case 'category':
				@list($id, $alias) = explode(':', $query['id']);
				$item = getXDSoftItemid('category', $id);
				if (!$item) {
					$item = getXDSoftItemid('yandex_maps');
					if ($item) {
						$query['Itemid'] = getXDSoftItemid('yandex_maps')->id;
					}
					$segments = array('category', $query['id']);
				} else {
					$query['Itemid'] = $item->id;
				}
				unset($query['id']);
				unset($query['task']);
				unset($query['view']);
			break;
			case 'object':
				@list($id, $alias) = explode(':', $query['id']);
				$item = getXDSoftItemid('object', $id);
				if (!$item) {
					if (getXDSoftItemid('yandex_maps')) {
						$query['Itemid'] = getXDSoftItemid('yandex_maps')->id;
					}
					$segments = array('object', $query['id']);
				} else {
					$query['Itemid'] = $item->id;
				}
				unset($query['id']);
				unset($query['task']);
				unset($query['view']);
			break;
			default:
				//$item = getXDSoftItemid('yandex_maps');
				//print_r($query);
				//$segments = array($item->route ? $item->route : 'yandex_maps');
				//unset($query['Itemid']);
				//unset($query['option']);
				//unset($query['view']);
				//print_r($query);
			break;
		}
	} else {
		if (!empty($query['view'])) {
			$item = getXDSoftItemid($query['view'], 0);
			if (!$item) {
				$segments = array($query['view']);
			} else {
				$query['Itemid'] = $item->id;
			}
			unset($query['view']);
		}
	}
	
    return $segments;
}