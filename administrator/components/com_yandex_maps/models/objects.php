<?php
defined("_JEXEC") or die("Access deny");
class Yandex_MapsModelObjects extends CModel{
	public $error = array();
	private $_categoryids = null;

	private $_condition;
	public $table = '#__yandex_maps_objects';
	public $safe = array(
		'categoryid', 'mapid', 'id','a.id', 'alias', 'description','link', 'title','a.title', 'm.title', 'c.title','type','a.type', 'ordering', 'a.ordering', 'active', 'modified_time', 'a.modified_time','created_time','coordinates', 'options', 'properties', 'lat','lan','zoom', 'create_by', 'author', 'publish_up', 'publish_down',
		// для организаций
		'organization_object_id', 'organization_name', 'organization_form', 'organization_lider_fio', 'organization_lider_position', 'organization_acting_basis', 'organization_acting_basis_date', 'organization_acting_basis_number', 'organization_type', 'organization_trademark', 'organization_contact_fio', 'organization_contact_position', 'organization_contact_phone', 'organization_address_legal', 'organization_address', 'organization_phone', 'organization_email', 'organization_website', 'organization_image', 'organization_shedule_24', 'organization_shedule_days', 'organization_start_in', 'organization_end_in', 'organization_service_delivery', 'organization_service_delivery_variants', 'organization_license_number', 'organization_license_date', 'organization_bank_inn', 'organization_bank_kpp', 'organization_bank_rs', 'organization_bank_name', 'organization_bank_ks', 'organization_bank_bik', 'organization_info', 'organization_self_schedule_text', 'organization_compile'
	);
	public function setCategoryIds($ids) {
		$this->_categoryids = (array)$ids;
	}
	public function getCategoryIds() {
		if (!$this->_categoryids) {
			$db = JFactory::getDBO();
			$db->setQuery('select category_id from #__yandex_maps_object_to_category where object_id='.(int)$this->id);
			$this->_categoryids = $db->loadColumn();
		}
		return $this->_categoryids;
	}
	public function defaults() {
		if (!$this->type) {
			$this->type = $this->settings->get('object_type', 'placemark');
		}
		if (!$this->lat) {
			$this->lat = 55.745501257;
			$this->lan = 37.680073435;
			$this->zoom = 10;
			$this->active = 1;
		}
	}

	public $types = array(
		'placemark'=>'Метка',
		'polyline'=>'Линия',
		'polygon'=>'Многоугольник',
		'circle'=>'Круг',
	);
	
	static public $_maps = null;
	public function getMap() {
		if (!self::$_maps[$this->id]) {
			self::$_maps[$this->id] = JModelLegacy::getInstance('Maps', 'Yandex_MapsModel')->modelBySQL('select * from #__yandex_maps_maps where id in (select map_id from #__yandex_maps_category_to_map where category_id in (select category_id from #__yandex_maps_object_to_category where object_id='.((int)$this->id).')) limit 1');
		}
		return self::$_maps[$this->id];
	}
	
	static public $_categories = null;
	public function getCategory() {
		if (!self::$_categories[$this->id]) {
			self::$_categories[$this->id] = JModelLegacy::getInstance('Categories', 'Yandex_MapsModel')->modelBySQL('select * from #__yandex_maps_categories where id in (select category_id from #__yandex_maps_object_to_category where object_id='.((int)$this->id).') limit 1');
		}
		return self::$_categories[$this->id];
	}

	public function beforeSave() {
		if ($this->organization_object_id) {
			$organization = JModelLegacy::getInstance( 'Organizations', 'Yandex_MapsModel');
			$this->organization_address = json_encode($this->organization_address);
			$this->organization_address_legal = json_encode($this->organization_address_legal);
			$this->organization_service_delivery = isset($_REQUEST['jform']['organization_service_delivery']) ? 1 : 0;
			$this->organization_shedule_days = is_array($this->organization_shedule_days) ? implode(',', $this->organization_shedule_days) : '';
			$this->organization_service_delivery_variants = is_array($this->organization_service_delivery_variants) ? implode(',', $this->organization_service_delivery_variants) : '';
			$organization->_attributes = $this->_data;
			return $organization->save();
		}
		return parent::beforeSave();
	}
	public function afterSave() {
		$categoryids = $this->getCategoryIds();
		$db = JFactory::getDBO();
		$db->setQuery('delete from #__yandex_maps_object_to_category where object_id='.(int)$this->id);
		$db->execute();
		$link = new stdClass();
		$link->object_id = (int)$this->id;
		foreach ($categoryids as $id){
			if ((int)$id) {
				$link->category_id = (int)$id;
				JFactory::getDbo()->insertObject('#__yandex_maps_object_to_category', $link);
			}
		}
	}
	public function validate() {
		if (mb_strlen($this->title)<=0) {
			$this->error['title'] = 'Слишком короткое название';
		}
		
		/*$this->map_id = (int)$this->map_id;
		$map = JModelLegacy::getInstance( 'Maps', 'Yandex_MapsModel');
		
		if (!$map->model($this->map_id)->id) {
			$this->error['map_id'] = 'Вы должны выбрать карту';
		}
		
		$this->category_id = (int)$this->category_id;
		$category = JModelLegacy::getInstance( 'Categories', 'Yandex_MapsModel');
		$category = $category->model($this->category_id);
		if (!$category->id or $category->map_id!=$this->map_id) {
			$this->error['category_id'] = 'Вы должны выбрать категорию';
		}*/
		
		
		$categoryids = array_map('intval', (array)$this->categoryids);

		if (!count($categoryids)) {
			$this->error['map_id'] = 'Должна быть выбрана хотя бы одна категория';
		}
		
		
		$this->active = in_array((int)$this->active, array(0,1,-2)) ? (int)$this->active : 0;
		
		return !count($this->error);
	}

	public function copy(){
		$copy = parent::copy();
		$copy->categoryids = $this->categoryids;
		$copy->save();
		return $copy;
	}
	public function getListQuery(){
		$query = parent::getListQuery();
		
		/*$query->select('m.title AS map_title');
		$query->leftJoin('#__yandex_maps_maps AS m ON m.id = a.map_id');
		
		*/
		
		/*$query->leftJoin('#__yandex_maps_object_to_category AS otc ON otc.object_id = a.id');
		$query->select('c.title AS category_title, c.id as category_id');
		$query->innerJoin('#__yandex_maps_categories  AS c ON c.id = otc.category_id');
		

		$query->leftJoin('#__yandex_maps_category_to_map AS ctm ON ctm.map_id = otc.category_id');
		$query->select('m.title AS map_title, m.id as map_id');
		$query->innerJoin('#__yandex_maps_maps AS m ON m.id in (select map_id from #__yandex_maps_category_to_map where category_id in (select category_id from #__yandex_maps_object_to_category where object_id=a.id))');
*/
		$query->select('o.*');
		$query->leftJoin('#__yandex_maps_organizations  AS o ON a.id = o.organization_object_id');
		
        $categoryid = $this->getState('filter.categoryid');
 
        if (is_numeric($categoryid) and (int)$categoryid>0){
            $query->where('a.id in (select object_id from #__yandex_maps_object_to_category where category_id='.((int) $categoryid).')');
        }
		
		$mapid = $this->getState('filter.mapid');
 
        if (is_numeric($mapid) and (int)$mapid>0){
            $query->where('a.id in (select object_id from #__yandex_maps_object_to_category where category_id in (select category_id from #__yandex_maps_category_to_map where map_id = '.((int) $mapid).'))');
        }
		//$query->group('a.`id`');
		return $query;
	}
	
	public function model($id, $where = '') {
		$db = JFactory::getDBO();
		$db->setQuery('select a.*, o.* 
			from '.$this->table.' as a 
			left join #__yandex_maps_organizations as o on o.organization_object_id=a.id
			where id='.((int)$id).' '.$where.' 
			limit 1');
		$item = new $this->classname;
		$item->_attributes = $db->loadObject();
		if ($item->organization_address) {
			$item->organization_address = json_decode($item->organization_address);
		}

		if ($item->organization_address_legal) {
			$item->organization_address_legal = json_decode($item->organization_address_legal);
		}
		$this->afterLoad();
		return $item;
	}
	public function models($select = 'a.*, o.*', $return = false, $filter = '', $orderby = 'a.title asc', $limit = 1000) {
		$db = JFactory::getDBO();
		$this->_condition = $filter ? ' where '.$filter : 'where 1';
		$this->_condition.= ' and (active=1 '.(JFactory::getUser()->id? ' or create_by='.JFactory::getUser()->id.'' : '').')';
		$db->setQuery($q = 'select '.$select.'
			from '.$this->table.' as a 
			left join #__yandex_maps_organizations as o on o.organization_object_id=a.id
			'.$this->_condition.'
			order by '.$orderby.
			($limit ? ' limit '.((int)$limit) : '')
		);
		$items = $db->loadobjectList();
		if (is_string($return)) {
			$result = array();
			foreach ($items as $item) {
				$result[] = $item->$return;
			}
			return $result;
		}
		if (is_array($return)) {
			$result = array();
			foreach ($items as $item) {
				$result[$item->{$return[0]}] = $item->{$return[1]};
			}
			return $result;
		}
		foreach ($items as $i=>$item) {
			$item = new $this->classname(array(), $this->fast);
			$item->_attributes = $items[$i];
			$items[$i] = $item;
		}
		return $items;
	}
	public function beforeRealDelete() {
		$db = JFactory::getDBO();
		$pid = $this->primary_id;
		$db->setQuery('delete from #__yandex_maps_organizations where organization_object_id ='.(int)($this->$pid));
		$db->execute();
	}
}