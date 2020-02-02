<?php
/**
 * @copyright	Copyright (c) 2017 content. All rights reserved.
 * @license		GNU General Public License version 2 or later;
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
require_once JPATH_SITE . '/components/com_content/helpers/route.php';
include_once JPATH_ADMINISTRATOR."/components/com_yandex_maps/helpers/CModel.php";
JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_yandex_maps/models');
class plgYandexMapsSourceContent extends JPlugin {
	public $id = 'content'; // идентификатор источника
	public $main_params; // настройки Яндекс Мапс компонента
	public $content_params; // настройки компонента Материалы

	function __construct(&$subject, $config = array()) {
		parent::__construct($subject, $config);
		$this->main_params = JComponentHelper::getParams('com_yandex_maps');
		$this->content_params = JComponentHelper::getParams('com_content');
	}
	public function onThisPageIsMine($map) {
		if (!$map or $map->settings->get('source')!=$this->id) {
			return false;
		}
		$app = JFactory::getApplication();
		$option = $app->input->get('option');
		$view = $app->input->get('view');
		if($option!=='com_content' or $view!='article' or $app->isAdmin()) return false;
		if ($app->input->get('id')) {
			return $this->id.$app->input->get('id');
		}
	}
	public function onGetName(&$names) {
		$names[$this->id] = 'Материалы Joomla';
	}
	public function onGetCategories($map) {
		$categories = array();
		if (!$map or $map->settings->get('source')!=$this->id or !$this->params->get('use_content_categories')) {
			return $categories;
		}
		$db = JFactory::getDBO();
		$filter = array('1');
		if ($this->params->get('exlude_these_categories') and count($this->params->get('exlude_these_categories'))) {
			$filter[] = '(a.id not in ('.implode(',', $this->params->get('exlude_these_categories')).'))';
		}
		if ($this->params->get('include_only_these_categories') and count($this->params->get('include_only_these_categories'))) {
			$filter[] = '(a.id in ('.implode(',', $this->params->get('include_only_these_categories')).'))';
		}
		$db->setQuery(
			'select 
				a.*, 
				CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug
			from 
				#__categories  as a
			where
				id in (select catid from #__content group by catid) and
				a.published in (1) and ('.implode(' and ', $filter).')'
		);
		$data = $db->loadObjectList();
		foreach ($data as $category) {
			$_params = new JRegistry();
			$_params->loadString($category->params);// настройки категории
			$category->params = clone($this->content_params); // настройки компонента
			$category->params->merge($_params); // мержим сперва категорию
			$category->lat = $map->lat;
			$category->lan = $map->lan;
			$category->zoom = $map->zoom;
			$category->link = JRoute::_(ContentHelperRoute::getCategoryRoute($category->id));
			$category->id = $this->id.$category->id;
			$categories[] = $category;
		}
		return $categories;
	}
	public function onGetObjectsByBound($map, $bound = array(), $offset = 0, $limit = 500, $exclude = array(), $search = false, $forse_id = 0) {
		static $ymobjects = array();
		// если источник наш и в карте есть хотя бы одна категория
		if (!$map or $map->settings->get('source')!=$this->id or (!count($map->categories) and !$this->params->get('use_content_categories'))) {
			return array();
		}
		$db = JFactory::getDBO();
		
		$filter = array('1');
		if ($search) {
			$filter[]='(
				a.title like "%'.$db->escape( $search, true ).'%" or
				c.title like "%'.$db->escape( $search, true ).'%" 
			)';
		}
		
		// Define null and now dates
		$nullDate	= $db->quote($db->getNullDate());
		$nowDate	= $db->quote(JFactory::getDate()->toSql());
		$filter[]=' ((a.publish_up = '.$nullDate.' OR a.publish_up <= '.$nowDate.') and (a.publish_down = '.$nullDate.' OR a.publish_down >= '.$nowDate.'))';

		if ($this->params->get('exlude_these_categories') and count($this->params->get('exlude_these_categories'))) {
			$filter[] = '(c.id not in ('.implode(',', $this->params->get('exlude_these_categories')).'))';
		}
		if ($this->params->get('include_only_these_categories') and count($this->params->get('include_only_these_categories'))) {
			$filter[] = '(c.id in ('.implode(',', $this->params->get('include_only_these_categories')).'))';
		}

		$db->setQuery(
			'select a.*,
				c.title as category_title,
				c.params as category_params,
				CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,
				CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as category_slug
				from 
					#__content  as a
				left join
					#__categories  as c on a.catid=c.id
				where 
					state in (1) and ('.implode(' and ', $filter).')');

		$data = $db->loadObjectList();

		$objects = array();
		foreach($data as $item) {
			$metadata = json_decode($item->metadata);
			if (empty($metadata->address)) {
				continue;
			}
			$metadata->address = json_decode($metadata->address);
			if (!$metadata->address->lat) {
				continue;
			}
			
			$_params = new JRegistry();
			$_params->loadString($item->category_params);// настройки категории
			
			$category_params = clone($this->content_params); // настройки компонента
			$category_params->merge($_params); // мержим сперва категорию
			
			$object = new stdClass();
			$object->id = $this->id.$item->id;
			$object->link = jRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->category_slug));
			$object->target = $this->params->get('how_open_link','_self');
			$object->title = $item->title;
			$object->alias = $item->alias;
			$object->description = $item->introtext;
			$object->map_id = $map->id;
			
			if (empty($metadata->yandex_maps_object)) {
				// настройки внешнего вида объекта
				$options = array(
					'preset'=>$this->params->get('object_preset', 'islands#greenStretchyIcon'), 
					'iconColor'=>$this->params->get('object_iconColor','green'),
				);
				if ($this->params->get('object_iconImageHref', $this->main_params->get('object_iconImageHref')) or ($this->params->get('object_use_category_image', 1) && $category_params->get('image'))) {
					$icon = !$this->params->get('object_use_category_image', 1) ? '' : $category_params->get('image', ''); // изображение из настроек категории
					if (!$icon) {
						$icon = $this->params->get('object_iconImageHref', $this->main_params->get('object_iconImageHref'));
					}
					if ($icon) {
						if (!preg_match('#^http:#', $icon)) {
							$icon = substr(JURI::root(), 0, -1).str_replace(JURI::root(true), '', $icon);
						}
						$options+=array(
							'iconLayout'=>$this->params->get('object_show_title_with_image', 0) ? 'default#imageWithContent' : 'default#image',
							'iconImageHref'=>$icon,
							'iconImageSize'=>array_map('intval', explode(',', $this->params->get('object_iconImageSize', '37,42'))),
							'iconImageOffset'=>array_map('intval', explode(',', $this->params->get('object_iconImageOffset', '-19,-21'))),
						);
						$options['preset'] = 'islands#greenStretchyIcon';
					}
				}
				
				$options['visible'] = $this->params->get('object_visible', 1) ? true : false;
				$object->options = json_encode((object)$options);
				$object->properties = json_encode((object)array('metaType'=>'Point', 'iconContent'=>$item->title));
				$object->lat = $metadata->address->lat;
				$object->lan = $metadata->address->lan;
				$object->coordinates = json_encode(array($object->lat, $object->lan));
				$object->zoom = (isset($metadata->address->zoom) and (int)$metadata->address->zoom) ? (int)$metadata->address->zoom : 10;
				$object->type = 'placemark';
				if (!empty($metadata->yandex_maps_icon)) {
					$options = (array)json_decode($object->options);
					$options+=array(
						'iconLayout'=>$this->params->get('object_show_title_with_image', 0) ? 'default#imageWithContent' : 'default#image',
						'iconImageHref'=>$metadata->yandex_maps_icon,
						'iconImageSize'=>array_map('intval', explode(',', $this->params->get('object_iconImageSize', '37,42'))),
						'iconImageOffset'=>array_map('intval', explode(',', $this->params->get('object_iconImageOffset', '-19,-21'))),
					);
					$options['preset'] = 'islands#greenStretchyIcon';
					$object->options = json_encode((object)$options);
				}
			} else {
				if (!isset($ymobjects[$metadata->yandex_maps_object])) {
					$ymobjects[$metadata->yandex_maps_object] = JModelLegacy::getInstance('Objects', 'Yandex_MapsModel')->model($metadata->yandex_maps_object);
				}
				
				$object->options = $ymobjects[$metadata->yandex_maps_object]->options;
				$object->properties = $ymobjects[$metadata->yandex_maps_object]->properties;
				$object->lat = $ymobjects[$metadata->yandex_maps_object]->lat;
				$object->lan = $ymobjects[$metadata->yandex_maps_object]->lan;
				$object->coordinates = json_encode(array($ymobjects[$metadata->yandex_maps_object]->lat, $ymobjects[$metadata->yandex_maps_object]->lan));
				$object->zoom = $ymobjects[$metadata->yandex_maps_object]->zoom ?: 10;
				$object->type = $ymobjects[$metadata->yandex_maps_object]->type;
			}
			// категория
			if (empty($metadata->yandex_maps_object) or empty($ymobjects[$metadata->yandex_maps_object]) or !$this->params->get('use_category_by_choosen_ymobject', 0)) {
				if (!$this->params->get('use_content_categories', 1)) {
					$category = $map->categories[0];
					$object->category_id = $category->id;
					$object->category_slug = $category->slug;
					$object->category_title = $category->title;
					$object->category_link = JRoute::_('index.php?option=com_yandex_maps&task=category&id='.$object->category_slug);
				} else {
					$object->category_id = $this->id.$item->catid;
					$object->category_slug = $this->id.$item->category_slug;
					$object->category_title = $item->category_title;
					$object->category_link = JRoute::_(ContentHelperRoute::getCategoryRoute($item->catid));;
				}
			}

			$objects[] = $object;
		};
		return $objects;
	}
}