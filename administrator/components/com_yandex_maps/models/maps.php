<?php
defined("_JEXEC") or die("Access deny");

class Yandex_MapsModelMaps extends CModel{
	private $address = false;
	public function setAddress($address) {
		$this->address = $address;
	}
	public function getAddress() {
		return $this->address;
	}

	public $table = '#__yandex_maps_maps';
	public $safe = [
	    'categories_count', 'objects_count', 'id','a.id', 'alias', 'title', 'a.title', 'lat', 'lan', 'zoom', 'maxZoom', 'minZoom', 'ordering', 'type', 'width', 'height','description', 'active', 'modified_time','a.modified_time','created_time', 'create_by', 'fastobjects'
    ];

	public $map_types = [
		'yandex#map'=>'Схема',
		'yandex#satellite'=>'Спутник',
		'yandex#hybrid'=>'Гибрид',
		'yandex#publicMap'=>'Народная',
		'yandex#publicMapHybrid'=>'Народная+Спутник',
	];

	private $_objects = null;
	private $_categories = null;

	public function getObjects() {
		if (!$this->_objects) {
			$objects = JModelLegacy::getInstance('Objects', 'Yandex_MapsModel');
			$this->_objects = $objects->models('*', false, 'map_id=' . (int)$this->id);
		}
		return $this->_objects;
	}

	public function getCategories() {
		if (!$this->_categories) {
			$categories = JModelLegacy::getInstance('Categories', 'Yandex_MapsModel');
			$this->_categories = $categories->modelsBySql('select
				a.*
			from 
				#__yandex_maps_categories as a
			where
				a.id in (select category_id from  #__yandex_maps_category_to_map as ctm where ctm.map_id = ' . ((int)$this->id) . ') and
				a.active = 1
			order by
				a.ordering asc, a.title asc
			');
		}

		return $this->_categories;
	}

	public function getCategoriesEx() {
		$categories = [];

		if ($this->categoriesCount) {
			foreach($this->categories as $category){
				$category->_data->link = JRoute::_('index.php?option=com_yandex_maps&task=category&id=' . $category->slug);
				$categories[$category->id] = $category->_data;
				$categories[$category->id]->slug = $category->slug;
			}
		}

		if ($this->settings && $this->settings->get('source', 0)) {
			$dispatcher = JEventDispatcher::getInstance();
			JPluginHelper::importPlugin('yandexmapssource');

			$data = $dispatcher->trigger('onGetCategories', [$this]);
			if (is_array($data)) {
				foreach($data as $s=>$datas) {
					foreach ($datas as &$item) {
						if (is_object($item)) {
							$categories[$item->id] = $item;
						}
					}
				}
				
			}
		}

		return $categories;
	}

	public function getOnlySelfObjectsByBound($bound = [], $offset = 0, $limit = 500, $exclude = [], $search = false, $forse_id = 0, $filters = []) {
		$db = JFactory::getDBO();
		$filter = ['(a.active=1)'];

		if (!$forse_id) {
			if ($bound && isset($bound[0]) && $bound[0]>0) {
				$filter[] = '(a.lat > '.((float)$bound[0][0]).' and
					a.lan > '.((float)$bound[0][1]).' and
					a.lat < '.((float)$bound[1][0]).' and
					a.lan < '.((float)$bound[1][1]).')';
			}
			if ($filter && $exclude && isset($exclude[0]) && $exclude[0]>0) {
				$filter[] = '!(a.lat > '.((float)$exclude[0][0]).' and
				a.lan > '.((float)$exclude[0][1]).' and
				a.lat < '.((float)$exclude[1][0]).' and
				a.lan < '.((float)$exclude[1][1]).')';
			}
			if ($search) {
				$filter[] = ' (
					a.title like "%'.$db->escape( $search, true ).'%" or
					category.title like "%'.$db->escape( $search, true ).'%" 
				)';
			}
		} else {
			$filter[] ='(a.id='.((int)$forse_id).')';
		}

		$db->setQuery($q = 'select a.*, o.*, category.title as category_title, category.id as category_id,
			CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,	
			CASE WHEN CHAR_LENGTH(category.alias) THEN CONCAT_WS(":", category.id, category.alias) ELSE category.id END as category_slug	
		from #__yandex_maps_objects as a
		left join #__yandex_maps_object_to_category as otc on otc.object_id=a.id
		left join #__yandex_maps_category_to_map as ctm on ctm.category_id=otc.category_id
		left join #__yandex_maps_categories as category on otc.category_id=category.id
		left join #__yandex_maps_organizations as o on a.id=o.organization_object_id
		where 
			category.active = 1 and
			ctm.map_id = ' . $this->id . ' and
			('.implode(' and ', $filter).')
		group by a.id
		order by 
			a.id asc
		limit '.((int)$offset).','.((int)$limit));

		$data = $db->loadObjectList();

		return [
			'objects'   =>  $data,
			'count'     =>  $this->getObjectsCount('('.implode(' and ', $filter).') and ')
		];
	}

	public function getObjectsByBound($bound = [], $offset = 0, $limit = 500, $exclude = [], $search = false, $forse_id = 0, $bypublish = false, $filters = [], $nearPoint = false) {
		$db = JFactory::getDBO();
		$filter = ['(a.active = 1)'];

		if (!$forse_id) {
			if ($bound && isset($bound[0]) && $bound[0]>0) {
				$filter[] = '(a.lat > '.((float)$bound[0][0]).' and
					a.lan > '.((float)$bound[0][1]).' and
					a.lat < '.((float)$bound[1][0]).' and
					a.lan < '.((float)$bound[1][1]).')';
			}
			if ($filter && $exclude && isset($exclude[0]) && $exclude[0]>0) {
				$filter[] = ' not (a.lat > '.((float)$exclude[0][0]).' and
				a.lan > '.((float)$exclude[0][1]).' and
				a.lat < '.((float)$exclude[1][0]).' and
				a.lan < '.((float)$exclude[1][1]).')';
			}

			if ($search) {
				$filter[] =' (
					a.title like "%' . $db->escape($search, true ) . '%"
				)';
			}
		} else {
			$filter[] = '(a.id='.((int)$forse_id).')';
		}

		if ($filters and is_array($filters) and count($filters)) {
			$filter[] =' (
				category.id in ('.implode(',', array_map(array($db, 'quote'), $filters)).')
			)';
		}

		// Define null and now dates
		$nullDate	= $db->quote($db->getNullDate());
		$nowDate	= $db->quote(JFactory::getDate()->toSql());


		$filter[] ='  ((a.publish_up = '.$nullDate.' OR a.publish_up <= '.$nowDate.') and (a.publish_down = '.$nullDate.' OR a.publish_down >= '.$nowDate.'))';

        $dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('yandexmapssource');
        
        $select = [
            'a.*',
            'o.*',
            'category.title as category_title',
            'category.id as category_id',
            'CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug',
            'CASE WHEN CHAR_LENGTH(category.alias) THEN CONCAT_WS(":", category.id, category.alias) ELSE category.id END as category_slug',
        ];

        $join = [
            'from #__yandex_maps_objects as a',
            'inner join #__yandex_maps_object_to_category as otc on otc.object_id=a.id',
            'left join #__yandex_maps_categories as category on otc.category_id=category.id',
            'left join #__yandex_maps_organizations as o on a.id=o.organization_object_id',
            'inner join #__yandex_maps_category_to_map as ctm on ctm.category_id=otc.category_id',
        ];

        $dispatcher->trigger('onGenerateFilterWhere', [$this, & $filter, & $join, & $select]);

		$query = implode($join, "\n").'
		where 
			category.active = 1 and
			ctm.map_id = '.$this->id.' and
			('.implode(' and ', $filter).')
		group by a.id
		order by 
			'.($nearPoint ? 'SQRT(POW(a.lat -'.((float)$nearPoint[0]).', 2) + POW(a.lan -'.((float)$nearPoint[1]).', 2)) asc,' : '').'
			'.($bypublish ? 'a.publish_up asc,' : '').'
			'.($this->settings->get('group_by_category', 1) ? 'category.ordering asc, category.title asc,' : '').'
			a.ordering asc, a.title asc
		limit '.((int)$offset).','.((int)$limit);

		$db->setQuery('select '.implode($select, ',').' '.$query);
		$data = $db->loadObjectList();

		$db->setQuery('select a.id '.$query);
		$ids = $db->loadColumn();
		$object_to_category = count($ids) ? $db->setQuery('select object_id,category_id from #__yandex_maps_object_to_category where object_id in ('.implode(',', $ids).')')->loadObjectList() : [];

		static $articles = [];
		$target = $this->settings->get('how_open_link', '_self');

		foreach ($data as &$item) {
			$params = json_decode($item->params)?:new stdClass();
			if (isset($params->article_id) and (int)$params->article_id) {
				if (!isset($articles[$params->article_id])) {
					$articles[$params->article_id] = $db->setQuery('select a.*, 
						CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,
						CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as category_slug
						from 
							#__content  as a
						left join
							#__categories  as c on a.catid=c.id
						where a.id=' . $params->article_id
					)->loadObject();
				}
				if (!isset($articles[$params->article_id]->id)) {
					$item->link = JRoute::_('index.php?option=com_yandex_maps&task=object&id='.$item->slug);
				} else {
					require_once JPATH_SITE . '/components/com_content/helpers/route.php';
					$item->link = jRoute::_(ContentHelperRoute::getArticleRoute($articles[$params->article_id]->slug, $articles[$params->article_id]->category_slug));;
				}
			} else {
				$item->link = JRoute::_('index.php?option=com_yandex_maps&task=object&id='.$item->slug);
			}

			$item->category_link = JRoute::_('index.php?option=com_yandex_maps&task=category&id='.$item->category_slug);
			$item->target = $target;

			// рендер организации
			jHtml::_('xdwork.organization', $item);
			// ренедер описания
			jHtml::_('xdwork.description', $item, $this);
		}

		$count = 0;
		if ($this->settings->get('source', 0)) {
			$data2 = $dispatcher->trigger('onGetObjectsByBound', [$this, $bound, $offset, $limit, $exclude, $search, $forse_id, $bypublish, $filters, $nearPoint]);
			if (is_array($data2)) {
				foreach($data2 as $s=>$datas) {
					$count+=count($datas);
					foreach ($datas as &$item) {
						if (is_object($item)) {
							$data[] = $item;
						}
					}
				}
				
			}
		}

		return [
			'objects'               => $data,
			'object_to_category'    => $object_to_category,
			'count'                 =>  $this->getObjectsCount('(' . implode(' and ', $filter) . ') and ', $bound, $exclude, $search, $forse_id) + $count
		];
	}
	
	public function getObjectsCount($filter = '', $bound = [], $exclude = [], $search = false, $forse_id = 0) {
		$db = JFactory::getDBO();
		$db->setQuery('select count(distinct a.id) as cnt 
			from #__yandex_maps_objects as a 
			inner join #__yandex_maps_object_to_category as otc on otc.object_id=a.id
			inner join #__yandex_maps_category_to_map as ctm on ctm.category_id=otc.category_id
			inner join #__yandex_maps_categories as category on otc.category_id=category.id
			where '.$filter.' ctm.map_id='.(int)$this->id.' ');

        $cnt = (int)$db->loadResult();

		if ($this->settings->get('source', 0)) {
			JPluginHelper::importPlugin('yandexmapssource');
			$dispatcher = JEventDispatcher::getInstance();

			$result = $dispatcher->trigger('getObjectsCount', [null, $filter, $bound, $exclude, $search, $forse_id]);
			if (is_array($result)) {
				foreach ($result as $s => $datas) {
					$cnt += (int)$datas;
				}
			}
		}

        return $cnt;
	}

	public function getCategoriesCount() {
		$db = JFactory::getDBO();
		$db->setQuery('
			select
				count(id)
			from
				#__yandex_maps_categories 
			where 
				id in (select
							category_id
						from
							#__yandex_maps_category_to_map
						where
							map_id=' . $this->id . '
				)
				and active = 1'
		);
		return $db->loadResult();
	}
	public function afterLoad() {}
	public function getBehaviors($element = 'behaviors') {
		if (isset($this->_data->$element) and $this->_data->$element and is_string($this->_data->$element)) {
			return json_decode($this->_data->$element);
		} else {
			return array('default');
		}
	}
	public function setBehaviors($array = false, $element = 'behaviors') {
		if ($array) {
			if (is_array($array)) {
				$result = [];
				foreach($array as $item=>$value) {
					if ((int)$value === 1) {
						$result[] = $item;
					}
				}
				$this->_data->$element = json_encode($result);
			} else {
				$this->_data->$element = $array;
			}
		} else {
			$this->_data->$element = '["default"]';
		}
	}
	public function getControls() {
		return $this->getBehaviors('controls');
	}
	public function setControls($array = false) {
		$this->setBehaviors($array, 'controls');
	}
	public function afterSave() {
		if ($this->fastobjects and is_array($this->fastobjects) and count($this->fastobjects)) {
			foreach($this->_data->fastobjects as &$object) {
				$object = json_decode($object);
				if ($object[0]->id) {
					$newobject = JModelLegacy::getInstance('Objects', 'Yandex_MapsModel')->model($object[0]->id);
					if (isset($object[0]->deleted)) {
						$newobject->delete();
						continue;
					}
				} else {
					$newobject = JModelLegacy::getInstance('Objects', 'Yandex_MapsModel');
				}
				$newobject->title = $object[0]->title ?: 'Undefined';
				$newobject->description = isset($object[3]->balloonContent) ? $object[3]->balloonContent : '';
				if ($this->settings->get('use_description_how_balloon_content', 2) and isset($object[3]->balloonContent)) {
					unset($object[3]->balloonContent);
				}
				$newobject->map_id = $this->id;
				$newobject->zoom = $object[0]->zoom;
				$newobject->lat = $object[0]->lat;
				$newobject->lan = $object[0]->lan;

				if (!$object[0]->id) {
					$newobject->setCategoryIds(array($object[0]->category_id ?: JModelLegacy::getInstance('Categories', 'Yandex_MapsModel')->findNearest(array($newobject->lat, $newobject->lan, $this->id))->id));
				} else {
					$categories = $newobject->getCategoryIds();
					if ($object[0]->category_id) {
						$newobject->setCategoryIds(array_merge($categories, array($object[0]->category_id)));
					}
				}

				$newobject->coordinates = json_encode($object[1]);
				$newobject->options = json_encode($object[2]);
				$newobject->properties = json_encode($object[3]);
				$newobject->type = $object[0]->type;
				if (!$newobject->save()) {
					unset($object);
				}
			};
		}
	}
	public function defaults() {
		if (!$this->lat) {
			$this->lat = 55.745501257;
			$this->lan = 37.680073435;
			$this->maxZoom = 23;
			$this->minZoom = 0;
			$this->zoom = 10;
			$this->type = 'yandex#map';
			$this->width = 'auto';
			$this->active = 1;
			$this->height = '450px';
			$this->id = 0;
		}
	}
	
	public function validate() {
		if (mb_strlen($this->title)<3) {
			$this->error['title'] = 'Слишком короткое название';
		}
		if ($this->lat and !preg_match('#^(-)?[0-9]+(\.[0-9]+)?$#', $this->lat)) {
			$this->error['lat'] = 'Не верный формат координаты Широта';
		}
		if ($this->lan and !preg_match('#^(-)?[0-9]+(\.[0-9]+)?$#', $this->lan)) {
			$this->error['lan'] = 'Не верный формат координат Долгота';
		}
		
		$this->minZoom = (int)$this->minZoom;
		$this->maxZoom = (int)$this->maxZoom;

		if ($this->minZoom < 0) {
			$this->minZoom = 0;
		}
		if ($this->minZoom > 23) {
			$this->minZoom = 23;
		}
		if ($this->maxZoom < $this->minZoom) {
			$this->maxZoom = $this->minZoom;
		}
		if ($this->maxZoom > 23) {
			$this->maxZoom = 23;
		}
		
		$this->zoom = (int)$this->zoom;
		
		if ($this->zoom < $this->minZoom) {
			$this->zoom = $this->minZoom;
		}
		if ($this->zoom > $this->maxZoom) {
			$this->zoom = $this->maxZoom;
		}
		
		$this->type = in_array($this->type, array_keys($this->map_types)) ? $this->type : 'yandex#map';
		
		$this->active = in_array((int)$this->active, array(0,1,-2)) ? (int)$this->active : 0;

		return !count($this->error);
	}
	public function copy($mode=0) {
		switch ($mode) {
			case 0:
				parent::copy();
			break;
			case 1:
				$categories = $this->categories;
				$newmap = parent::copy();
				foreach ($categories as $category) {
					$copy = $category->copy();
				}
			break;
			case 2:
				$categories = $this->categories;
				$newmap = parent::copy();
				foreach ($categories as $category) {
					$objects = $category->objects;
					$copy = $category->copy();
					foreach ($objects as $object) {
						$object_copy = $object->copy();
					}
				}
			break;
			case 3:
				$newmap = parent::copy();
				$categories = $this->categories;
				foreach ($categories as $category) {
					$category->mapids = array_merge($category->mapids, array($newmap->id));
					$category->save();
				}
			break;
		}
	}
	public function getListQuery(){
		$query = parent::getListQuery();
		$query->select('(select count(distinct otc.object_id) from  #__yandex_maps_object_to_category AS otc where otc.category_id in (select category_id from #__yandex_maps_category_to_map where map_id=a.id)) as objects_count');
		//$query->leftJoin('#__yandex_maps_category_to_map AS ctm ON ctm.map_id = a.id');
		//$query->where('o.active=1');
		$query->group('a.id');
		return $query;
	}
	public function beforeRealDelete() {
		set_time_limit(0);
		$model = JModelLegacy::getInstance('Categories', 'Yandex_MapsModel');
		$model->fast = true;
		$categories = $model->modelsBySQl('select
			id
		from
			#__yandex_maps_categories 
		where 
			id in (select
						category_id
					from
						#__yandex_maps_category_to_map
					where
						map_id='.$this->id.'
			) and
			id not in (select
						category_id
					from
						#__yandex_maps_category_to_map
					where
						map_id!='.$this->id.'
			)
		');
		if (is_array($categories) and count($categories)) {			
			foreach($categories as $category) {
				$category->beforeRealDelete();
				unset($category);
			}
		}
		$db = JFactory::getDBO();
		$pid = $this->primary_id;
		$db->setQuery(
			'delete from '.$model->table.' where 
			id in (select category_id  from #__yandex_maps_category_to_map where map_id='.((int)$this->$pid).')
			and id not in (select category_id  from #__yandex_maps_category_to_map where map_id!='.((int)$this->$pid).')'
		);
		$db->execute();
		$db->setQuery('delete from #__yandex_maps_category_to_map where map_id='.((int)$this->$pid));
		$db->execute();
	}
}