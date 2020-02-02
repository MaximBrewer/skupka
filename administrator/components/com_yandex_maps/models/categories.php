<?php
defined("_JEXEC") or die("Access deny");
class Yandex_MapsModelCategories extends CModel{
	public $error = array();
	private $map = null;
	private $_mapids = null;
	
	public $table = '#__yandex_maps_categories';
	public $safe = array('id', 'a.id', 'alias', 'title', 'a.title', 'description', 'mapid', 'category_id', 'm.title', 'objects_count', 'active', 'a.modified_time', 'modified_time','created_time', 'ordering','howtocenter','lat','lan','zoom', 'create_by');

	public function defaults() {
		if (!$this->lat) {
			$this->lat = 55.745501257;
			$this->lan = 37.680073435;
			$this->zoom = 10;
			$this->active = 1;
		}
	}
	private $_objects = null;

	public function getObjects() {
		if (!$this->_objects) {
			$objects = JModelLegacy::getInstance('Objects', 'Yandex_MapsModel');
			$this->_objects = $objects->modelsBySQL('select * from #__yandex_maps_objects where id in (select object_id from #__yandex_maps_object_to_category where category_id='.((int)$this->id).')');
		}
		return $this->_objects;
	}
	public function getObjectsCount() {
		$db = JFactory::getDBO();
		$db->setQuery('select count(id) from #__yandex_maps_objects where category_id='.$this->id.' and active = 1');
		return $db->loadResult();
	}
	
	public function setMapIds($ids) {
		$this->_mapids = (array)$ids;
	}
	public function getMapIds() {
		if (!$this->_mapids) {
			$db = JFactory::getDBO();
			$db->setQuery('select map_id from #__yandex_maps_category_to_map where category_id='.(int)$this->id);
			$this->_mapids = $db->loadColumn();
		}
		return $this->_mapids;
	}
	public function getMap() {
		if (!$this->map) {
			$this->map = JModelLegacy::getInstance('Maps', 'Yandex_MapsModel')->model($this->getMapIds());
		}
		return $this->map;
	}
	public function afterSave() {
		$mapids = (array)$this->_mapids;
		$db = JFactory::getDBO();
		$db->setQuery('delete from #__yandex_maps_category_to_map where category_id='.(int)$this->id);
		$db->execute();
		$link = new stdClass();
		$link->category_id = (int)$this->id;
		foreach ($mapids as $id){
			if ((int)$id) {
				$link->map_id = (int)$id;
				JFactory::getDbo()->insertObject('#__yandex_maps_category_to_map', $link);
			}
		}
	}
	public function validate() {
		if (mb_strlen($this->title)<3) {
			$this->error['title'] = 'Слишком короткое название';
		}
		
		$this->category_id = (int)$this->category_id;
		$mapids = array_map('intval', (array)$this->mapids);

		if (!count($mapids)) {
			$this->error['map_id'] = 'Должна быть выбрана хотя бы одна карта';
		}
		
		if ($this->lat and !preg_match('#^(-)?[0-9]+(\.[0-9]+)?$#', $this->lat)) {
			$this->error['lat'] = 'Не верный формат координаты Широта';
		}
		if ($this->lan and !preg_match('#^(-)?[0-9]+(\.[0-9]+)?$#', $this->lan)) {
			$this->error['lan'] = 'Не верный формат координат Долгота';
		}
		if ($this->zoom and is_numeric($this->zoom)) {
			$this->zoom = (int)$this->zoom;
		}
		
		$this->active = in_array((int)$this->active, array(0,1,-2)) ? (int)$this->active : 0;
		$this->howtocenter = in_array((int)$this->howtocenter, array(0,1,2)) ? (int)$this->howtocenter : 0;
		
		return !count($this->error);
	}

    /**
     * Применяет функцию $callback ко всем элементам дерева категорий.Если функция возвращает false, ветка пропускается
     *
     * @method tree
     * @param {function} $callback Функция с параметрами $category - катгория, $deep(int) - глубина
     * @param {int} [$root_id=0] Необязательный параметр, дает начало дерева
     */
	public function tree($callback, $root_id = 0) {
        $categories = $this->modelsBySql('select * from #__yandex_maps_categories where active=1 order by category_id asc, ordering asc');
        $tree = array();
        foreach ($categories as $category) {
            if (!isset($tree[$category->category_id])) {
                $tree[$category->category_id] = array();
            }
            $tree[$category->category_id][] = $category;
        }

        if ($callback) {
            $treeWalker  = function ($branch, $deep = 0) use ($tree, $callback, &$treeWalker){
                foreach ($branch as $category) {
                    if ($callback($category, $deep) === false) {
                        continue;
                    }
                    if (isset($tree[$category->id])) {
                        $treeWalker($tree[$category->id], $deep + 1);
                    }
                }
            };
            if (isset($tree[$root_id])) {                
                $treeWalker($tree[$root_id]);    
            }
        }
    }
	public function getListQuery(){
		$query = parent::getListQuery();
		$query->select('m.title AS map_title');
		$query->select('m.id as map_id');
		$query->innerJoin('#__yandex_maps_category_to_map AS ctm ON ctm.category_id = a.id');
		$query->innerJoin('#__yandex_maps_maps AS m ON ctm.map_id = m.id');
		
		$query->select('(select count(distinct otc.object_id) from  #__yandex_maps_object_to_category AS otc where otc.category_id=a.id)as objects_count');
		
		$mapid = $this->getState('filter.mapid');
 
        if (is_numeric($mapid) and (int)$mapid){
            $query->where('ctm.map_id = '.(int) $mapid);
        }
		
		$query->group('a.id');
		return $query;
	}
	public function copy() {
		$copy = parent::copy();
		$copy->mapids = $this->mapids;
		$copy->save();
		return $copy;
	}
	public function beforeRealDelete() {
		$model = JModelLegacy::getInstance('Objects', 'Yandex_MapsModel');
		$model->fast = true;
		$objects = $model->modelsBySQL($q = 'select id from '.$model->table.'
			where 
				id in (select object_id as o from #__yandex_maps_object_to_category where category_id='.((int)$this->id).')
				and id not in (select object_id from #__yandex_maps_object_to_category where category_id!='.((int)$this->id).')
		');
		
		if (is_array($objects) and count($objects)) {			
			foreach($objects as &$object) {
				$object->beforeRealDelete();
				unset($object);
			}
		}
		$db = JFactory::getDBO();
		$pid = $this->primary_id;
		$db->setQuery('delete from '.$model->table.' 
			where 
				id in (select object_id from #__yandex_maps_object_to_category where category_id='.((int)$this->id).')
				and id not in (select object_id from #__yandex_maps_object_to_category where category_id!='.((int)$this->id).')'
		);
		$db->execute();
		$db->setQuery('delete from #__yandex_maps_object_to_category where category_id='.((int)$this->id));
		$db->execute();
	}
}