<?php
defined('_JEXEC') or die;
JLoader::register('MapHelper', JPATH_ADMINISTRATOR . '/components/com_yandex_maps/helpers/html/map.php');
require_once JPATH_ADMINISTRATOR . "/components/com_yandex_maps/helpers/html/configHelper.php";
JHtml::addIncludePath(JPATH_ROOT.'/components/com_yandex_maps/helpers');

abstract class JHtmlMap{
	static public function validateJSON($json, $default = '{}'){
		$data = json_decode($json);

		if ($data) {
			return $json;
		}

		return $default;
	}
	static public function validateGeometry($geometry){
		$coords = $geometry;
		if (is_string($geometry)) {
			$coords = json_decode($geometry);
		}
		if ($coords and is_array($coords)) {
			if (count($coords)) {
				foreach ($coords as $r=>$t) {
					if (is_array($coords[$r])) {
						if (!self::validateGeometry($coords[$r])) {
							return false;
						}
					} else {
						if (!is_numeric($coords[$r]) || $coords[$r] === null) {
							return false;
						}
					}
				}
				return true;
			}
		}
		return false;
	}

	static public function getOptions($map, $params) {
		configHelper::addParams($map->settings, 0);
		configHelper::addParams($params, 1);

		return "{
			address:". ($map->getAddress() ? "'".addslashes($map->getAddress())."'" : 'false').",
			cluster:{
				".(call_user_func(function() {
					$icons = (array)configHelper::get('cluster_icons', array());
					$numbers = array();
					$one = $set = array();
					if(count($icons)) {
						foreach ($icons as $count=>$icon) {
							if ($icon->icon) {
								if (count($one)) {
									$numbers[] = (int)$icon->count;
								}
								$one = array();
								$one['href'] = $icon->icon;
								if ($icon->size) {
									$one['size'] = array_map('intval', explode(',', $icon->size));
								}
								if ($icon->offset) {
									$one['offset'] = array_map('intval', explode(',', $icon->offset));
								}

								$set[] = $one;
							}
						}
						if (count($set)>1) {
							return 'clusterIcons:'.json_encode($set).','."\n".'clusterNumbers:'.json_encode($numbers).',';
						};
					};
				}))."
				gridSize: ".(call_user_func(function() {
					$size = configHelper::get('gridSize', 64);
					if (256 % $size === 0) {
						return $size;
					} else {
						return 64;
					}
				})).",
				groupByCoordinates: ".configHelper::get('groupByCoordinates', 0)." ? true : false,
				cluster_text_color: '".configHelper::get('cluster_text_color', '#000000')."',
				hasBalloon: ".configHelper::get('hasBalloon', 1)." ? true : false,
				hasHint: ".configHelper::get('hasHint', 1)." ? true : false,
				maxZoom: ".configHelper::get('maxZoom', 0)." || 100000000,
				minClusterSize: ".configHelper::get('minClusterSize', 2).",
				preset: '".configHelper::get('preset', 'islands#blueClusterIcons')."',
				showInAlphabeticalOrder: ".configHelper::get('showInAlphabeticalOrder', 0)." ? true : false,
				viewportMargin: ".configHelper::get('viewportMargin', 128).",
				clusterDisableClickZoom: ".configHelper::get('clusterDisableClickZoom', 0)." ? true : false,
				zoomMargin: ".configHelper::get('zoomMargin', 0).",
				clusterHideIconOnBalloonOpen: ".configHelper::get('clusterHideIconOnBalloonOpen', 1)." ? true : false,
				clusterOpenBalloonOnClick: ".configHelper::get('clusterOpenBalloonOnClick', 1)." ? true : false,
				clusterBalloonContentLayout: '".configHelper::get('clusterBalloonContentLayout', 'cluster#balloonTwoColumns')."'
			},
            defaultView: {
                properties: {
                    iconContent: '" . configHelper::get('object_iconContent', 'Точка') . "'
                },
                options: {
                    preset: '" . configHelper::get('object_preset', 'islands#blueClusterIcons') . "'
                }
            },
			
			api_key: '" . configHelper::get('api_key', '') . "',
			show_description_in_custom_balloon: ".configHelper::get('show_description_in_custom_balloon', 0).",
			use_search_only_in_self_objects: ".configHelper::get('use_search_only_in_self_objects', 0)." ? true : false,
			registration_organization_use_addres_position: ".configHelper::get('registration_organization_use_addres_position', 0)." ? true : false,
			show_only_visible_objects_now: ".configHelper::get('show_only_visible_objects_now', 1)." ? true : false,
			show_search_input: ".configHelper::get('show_search_input', 1)." ? true : false,
			open_balloon_after_goto: ".configHelper::get('open_balloon_after_goto', 0).",
			save_map_position_in_hash: ".configHelper::get('save_map_position_in_hash', 0).",
			show_more_in_balloon: ".(configHelper::get('show_more_in_balloon', 1)).",
			use_cluster: ".configHelper::get('use_cluster', 1).",
			use_title_how_icon_content: ".configHelper::get('use_title_how_icon_content', 2).",
			use_title_how_hint_content: ".configHelper::get('use_title_how_hint_content', 0).",
			use_description_how_balloon_content: ".configHelper::get('use_description_how_balloon_content', 2).",
			center_to_user_place: '".configHelper::get('center_to_user_place', 0)."',
			add_osm_layer: ".configHelper::get('add_osm_layer', 0).",
			show_no_results: ".configHelper::get('show_no_results', 1).",
			show_pagination: ".configHelper::get('show_pagination', 1).",
			count_object_in_partition: ".configHelper::get('count_object_in_partition', 500).",
			counonpage: ".configHelper::get('count_on_page', 10).",
			howmoveto: ".configHelper::get('howmoveto', 0).",
			url_root: '".JURI::root()."',
			url_sheme: '".JURI::getInstance()->getScheme()."',
			balloon_width: ".configHelper::get('balloon_width', 'false').",
			balloon_height: ".configHelper::get('balloon_height', 'false').",
			how_open_balloon: '".configHelper::get('how_open_balloon', 'click')."',
			howmoveto_on_category_change: ".configHelper::get('howmoveto_on_category_change', 2)."
		}";
	}

	static public function show($map, $object = false, $params =null) {
		configHelper::addParams($map->settings, 0);
		configHelper::addParams($params, 1);

		$doc = JFactory::getDocument();

		jhtml::_('xdwork.yapi');
		
		if (configHelper::get('include_jquery', 1)) {
			$doc->addScript(JURI::root().'media/com_yandex_maps/js/jquery.min.js');
		}

		$objects = '{}';
		$categories = '{}';
		
		$lat = $map->lat;
		$lan = $map->lan;
		$zoom = $map->zoom;
		$maxZoom = ($map->maxZoom>=0 and $map->maxZoom<=23) ? $map->maxZoom : 23;
		$minZoom = ($map->minZoom>=0 and $map->minZoom<=$maxZoom)? $map->minZoom : 0;

		$mapobjects = ['objects'=> [], 'object_to_category'=> [], 'count'=> 0];
        $cache = JFactory::getCache('com_yandex_maps');

		if ($object === false) {
            if ($cache) {
                $categories = $cache->call([$map, 'getCategoriesEx']);
            } else {
                $categories = $map->getCategoriesEx();
            }

            $categories = json_encode($categories);
            if ($cache) {
                $objects_count = $cache->call([$map, 'getObjectsCount']);
            } else {
                $objects_count = $map->getObjectsCount();
            }

			if ($objects_count < configHelper::get('max_count_object', 100)) {
				$_objects = array();

                if ($cache) {
                    $mapobjects = $cache->call([$map, 'getObjectsByBound']);
                } else {
                    $mapobjects = $map->getObjectsByBound();
                }

				foreach ($mapobjects['objects'] as $object) {					
					if(is_object($object) and $object->coordinates and self::validateGeometry($object->coordinates) ){
						$_objects[$object->id] = $object;
					}
				}

				$objects = json_encode((object)$_objects);
			}
		} else {
			$lat = $object->lat;
			$lan = $object->lan;
			$zoom = $object->zoom;

			if($object->coordinates and self::validateGeometry($object->coordinates) ){
				$objects = '{' . $object->id . ':' . json_encode($object->_data) . '};';
			}
		}

		$object_to_category = json_encode($mapobjects['object_to_category']);

		// настройка нужна только для модуля и для карты источник у которых берется из плагина
		$goToCurrent = '';
        $dispatcher = JEventDispatcher::getInstance();

        if (configHelper::get('move_to_current_object', 0) and $map->settings->get('source', 0)) {
			JPluginHelper::importPlugin('yandexmapssource');
			$result = $dispatcher->trigger('onThisPageIsMine', array($map));
			if (is_array($result)) {
				foreach($result as $id) {
					if ($id) {
						$goToCurrent = 'map'.$map->id.'.goTo("'.$id.'");';
					}
				}
				
			}
		}

		foreach($map->controls as $control) {
			if ($control=='addUserPoint') {
				jhtml::_('xdwork.registration'); // если есть кнопка добавить объект то подключаем нужные скрипты
			}
		}

		$active_categories = $map->settings->get('active_categories_in_filter', array(0));
		if (!$active_categories or !is_array($active_categories) or !count($active_categories)) {
			$active_categories = array(0);
		}

		foreach ($active_categories as $i => $val) {
			if ($val == 0 || $val == -1) {
				$active_categories = array($val);
				break;
			}
		}

		$html="
		<div class=\"xdsoft"."_map ".(configHelper::get('show_gif_loader', 1) ? 'xdsoft_show_gif_loader' : '')."\" id=\"yandex"."_map{$map->id}\" style=\"width:{$map->width};height:{$map->height};\"></div>
		".(configHelper::get('show_copyright_link', 1) ? "<div style=\"di"."splay:blo"."ck !"."i"."mport"."ant;co"."lor:#c"."cc !"."i"."mpor"."tant;text-al"."ign:ri"."ght;\">"."<"."a style=\"di"."splay:blo"."ck !"."i"."mport"."ant;color:#ccc !"."i"."mpor"."tant;text-al"."ign:right;\" hr"."ef=\"ht"."tp:/"."/"."x"."d"."an.ru/k"."om"."po"."ne"."nt-yan"."de"."ks-k"."a"."r"."ty-dlya-jo"."oml"."a.ht"."ml\">"."К"."о"."мпо"."не"."нт Янд"."е"."кс К"."ар"."ты на xd"."a"."n.r"."u"."<"."/"."a"."></div>" : '')."
		<script>
			(function(){
				var map = {id : parseInt({$map->id}, 10)},
					objects = [],
					category,
					controls = " . json_encode($map->controls) . ",
					categories = {},					
					object;

				if (window.map{$map->id} === undefined) {
					map{$map->id} = new XDsoftMap(" . self::getOptions($map, $params) . ");
				}

				map{$map->id}.setMap(map);

				ymaps.ready(function () {
					if (map{$map->id}.getMap() instanceof ymaps.Map) {
						return;
					}

					categories = {$categories};
					map{$map->id}.setCategories(categories);
					map{$map->id}.setObjectToCategory({$object_to_category});
					map{$map->id}.addCustomControls();

					map = new ymaps.Map(\"yandex_map{$map->id}\", {
						center: [parseFloat({$lat}) || 53, parseFloat({$lan}) || 34],
						zoom: parseInt({$zoom}, 10) || 10,
						type: \"{$map->type}\",
						behaviors:".json_encode($map->behaviors).",
						controls:controls
					}, {
						".(!configHelper::get('show_link_open_in_yandex_maps', 1) ? 'suppressMapOpenBlock: true,' : '')."
						maxZoom: parseInt({$maxZoom}, 10),
						minZoom: parseInt({$minZoom}, 10)
					});

					map.lat = parseFloat({$lat}) || 53;
					map.lan = parseFloat({$lan}) || 34;
					map.zoom = parseInt({$zoom}, 10)  || 10;
					map.id = parseInt({$map->id}, 10);

					objects = {$objects};

					map{$map->id}.setMap(map);

					map{$map->id}.init(".($object!==false ? 'false' : 'true').");
					if (".($object!==false ? 'false' : 'true').") {
						map{$map->id}.setFilter([".implode(',', $active_categories)."]);
					}

					map{$map->id}.addObjectsToMap(objects);

					{$goToCurrent}
				});
			}());
		</script>";

        $dispatcher = JEventDispatcher::getInstance();
        $dispatcher->trigger('onOpenMap', [&$html, $map]);

		return $html;
	}
}