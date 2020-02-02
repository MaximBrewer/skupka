<?php
/**
 * @copyright	Copyright (c) 2017 content. All rights reserved.
 * @license		GNU General Public License version 2 or later;
 */

defined('_JEXEC') or die;
jimport('joomla.plugin.plugin');

require_once JPATH_SITE . '/components/com_users/helpers/route.php';

class plgYandexMapsSourceUser extends JPlugin {
	public $id = 2; // идентификатор источника

	public function onGetName(&$names) {
		$names[$this->id] = 'Пользователи';
	}

	public function onGetObjectsByBound($map, $bound = [], $offset = 0, $limit = 500, $exclude = [], $search = false, $forse_id = 0) {
		// если источник наш и в карте есть хотя бы одна категория
		if ($map and $map->settings->get('source')!=$this->id or !count($map->categories)) {
			return [];
		}

		$db = JFactory::getDBO();
		// Define null and now dates
	
		$db->setQuery('select u.* , p.profile_value as address
			from 
				#__users as u
			inner join 
				#__user_profiles as p on p.user_id = u.id
			where 
				p.profile_key="profile.address"
			group by
				u.id
			order by
				u.name asc
		');

		$data = $db->loadObjectList();

		$objects = [];
		foreach($data as $item) {
			$address = json_decode(json_decode($item->address, false));
			if (!$address->lat) {
				continue;
			}

			$object = new stdClass();
			$object->id = 'user'.$item->id;
			$object->link = JRoute::_('index.php?option=com_users&view=profile&user_id=' . $item->id, false);
			$object->target = $this->params->get('how_open_link','_self');
			$object->title = $item->name;
			$object->description = $item->name;
			$object->map_id = $map->id;

			$options = [
				'preset'=>$this->params->get('object_preset', 'islands#greenStretchyIcon'), 
				'iconColor'=>$this->params->get('object_iconColor','green'),
			];

			if ($this->params->get('object_iconImageHref')) {
				$options += [
					'iconLayout'=>$this->params->get('object_show_title_with_image', 0) ? 'default#imageWithContent' : 'default#image',
					'iconImageHref'=>$this->params->get('object_iconImageHref'),
					'iconImageSize'=>array_map('intval', explode(',', $this->params->get('object_iconImageSize', '37,42'))),
					'iconImageOffset'=>array_map('intval', explode(',', $this->params->get('object_iconImageOffset', '-19,-21'))),
				];

				$options['preset'] = 'islands#greenStretchyIcon';
			}

			$options['visible'] = $this->params->get('object_visible', 1) ? true : false;
			$object->options = json_encode((object)$options);
			$object->properties = json_encode((object)['metaType' => 'Point', 'iconContent' => $object->title]);
			$object->lat = $address->lat;
			$object->lan = $address->lan;
			$object->coordinates = json_encode([$object->lat, $object->lan]);
			$category = $map->categories[0];
			$object->category_id = $category->id;
			$object->category_slug = $category->slug;
			$object->category_title = $category->title;
			$object->category_link = JRoute::_('index.php?option=com_yandex_maps&task=category&id='.$category->slug);
			$object->zoom = $address->zoom;
			$object->type = 'placemark';

			$objects[] = $object;
		};

		return $objects;
	}
}