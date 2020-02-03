<?php
defined("_JEXEC") or die("Access deny");
jimport('joomla.application.component.modellist');
JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_yandex_maps/models','Yandex_MapsModel');

jimport('joomla.version');
$version = new JVersion();
if (version_compare($version->RELEASE, '2.6')<0) {
    class JEventDispatcher extends JDispatcher {}
}

abstract class CModel extends JModelList{
	public $error = array();
	public $classname = 'CModel';
	public $_data = null;
	public $_params = null;
	public $_forms = null;
	public $table = null;
	public $primary_id = 'id';
	public $fast = false;
	public $withOutValidate = false;
	
	public $safe = false;
	public function getClassName(){
		$name = get_class($this);
		return strtolower(preg_replace('#^Yandex_MapsModel#i', '', $name));
	}
	public function getNativeData(){
		$keys = $this->getTableFileds();
		$data = new stdClass();
		foreach ($this->_data as $key=>$value) {
			if (in_array($key, $keys)) {
				$data->$key = $this->_data->$key;
			}
		}
		return $data;
	}
	public function getTableFileds(){
		static $fields = false;
		if (!$fields) {
			$db = JFactory::getDBO();
			$db->setQuery('SHOW COLUMNS FROM '.str_replace('#__', $db->getPrefix(), $this->table).'');
			$fields = $db->loadColumn();
		}
		return $fields;
	}
	abstract public function defaults();
	
	/*public function populateState($ordering = null, $direction = null) {
		parent::populateState('a.'.$this->primary_id, 'desc');
	}*/

	public function __construct($config = array() ,$fast = false) {
		$this->_data = new stdClass();
		if (!$fast) {
			$this->classname = get_class($this);
			if (!$this->safe) {
				$this->safe = $this->getTableFileds();
			}

			if (empty($config['filter_fields'])){
				$config['filter_fields'] = array_merge($this->safe, ['author', 'a.ordering', 'published', 'a.title', 'a.id', 'a.active', 'a.modified_time', 'a.create_by']);
			}

			parent::__construct($config);
			$this->defaults();
		}
	}
	
	public function __get($name) {
		if (!$this->safe) {
			$this->safe = $this->getTableFileds();
		}

		if (in_array($name, $this->safe) && property_exists($this->_data, $name)) {
			return $this->_data->$name;
		}

		if (method_exists($this,'get'.$name)) {
			$call = 'get'.$name;
			return $this->$call();
		}

		return null;
	}
	
	public function __set($name, $value) {
		if (!$this->safe) {
			$this->safe = $this->getTableFileds();
		}
		if ($name==='_attributes') {
			if (is_object($value) or is_array($value)) {
				foreach($value as $key=>$val) {
					$this->__set($key,$val);
				}
			}
		} else {
			if (in_array($name, $this->safe)) {
				$this->_data->$name = $value;
			} else if (method_exists($this,'set'.$name)) {
				$call = 'set'.$name;
				$this->$call($value);
			}

		}
	}

	abstract public function validate();

	public static function truncate($html, $maxLength = 0) {
		$baseLength = strlen($html);
		$ptString = JHtml::_('string.truncate', $html, $maxLength, $noSplit = true, $allowHtml = false);
		for ($maxLength; $maxLength < $baseLength;){
			$htmlString = JHtml::_('string.truncate', $html, $maxLength, $noSplit = true, $allowHtml = true);
			$htmlStringToPtString = JHtml::_('string.truncate', $htmlString, $maxLength, $noSplit = true, $allowHtml = false);
			if ($ptString == $htmlStringToPtString){
				return $htmlString;
			}
			$diffLength = strlen($ptString) - strlen($htmlStringToPtString);

			$maxLength += $diffLength;

			if ($baseLength <= $maxLength || $diffLength <= 0){
				return $htmlString;
			}
		}
		return $html;
	}

	public function getIntro() {
		$text = $this->description;
		if (preg_match('#^(.*)<hr[\s]*id=("|\')system-readmore("|\')[\s]*(/)?>#Uusi',$text, $intro)) {
			return $intro[1];
		} else {
			return $this->truncate($text, $this->settings->get('description_intro_length', 200));
		}
	}

	public function getFull() {
		$text = $this->description;
		return preg_replace('#<hr[\s]*id=("|\')system-readmore("|\')[\s]*(/)?>#usi','',$text);
	}

	public function save($forseIsert = false) {
		if (!$this->withOutValidate and !$this->validate()) {
			return false;
		}

		if ($this->beforeSave()===false) {
			return false;
		}

		$db = JFactory::getDBO();
		$pid = $this->primary_id;
		
		if ($this->$pid and !$forseIsert) {
			if (!$this->create_by) {
				$this->create_by = JFactory::getUser()->id;
			}
			$this->modified_time = time();
			$data = $this->getNativeData();
			$res = $db->updateObject($this->table, $data, $this->primary_id);
		} else {
			if (!$this->create_by) {
				$this->create_by = JFactory::getUser()->id;
			}
			$this->modified_time = time();
			$this->created_time = time();
			$data = $this->getNativeData();
			$res = $db->insertObject($this->table, $data);
			$this->$pid = $db->insertid();
		}
		
		$this->afterSave();
		return true;
	}

	public function beforeRealDelete() {}

	public function afterRealDelete() {}

	public function realDelete() {
		$this->beforeRealDelete();
		$db = JFactory::getDBO();
		$pid = $this->primary_id;
		$db->setQuery('delete from '.$this->table.' where '.$pid.' ='.(int)($this->$pid));
		$result = $db->execute();
		$this->afterRealDelete();
        return $result;
	}
	public function delete($trash = true) {
		if ($this->active==-2) {
			return $this->realDelete();
		} else {
			$this->active = -2;
			return $this->save();
		}
	}
	public function active($activate = true) {
		$this->active = (int)!!$activate;
		$this->save();
	}
	public function copy() {
		$copy = new $this->classname;
		$copy->_data = clone($this->_data);
		$copy->_data->id = null;
		$copy->withOutValidate = true;
		$copy->save();
		$copy->withOutValidate = false;
		return $copy;
	}
	public function each($ids = array(), $method, $option = null) {
		if (method_exists($this,$method) and is_array($ids) and count($ids)) {
			foreach ($ids as &$id) {
				$id = (int)$id;
				$this->model($id)->$method($option);
			}
			return true;
		} else {
			return false;
		}
	}
	public function afterLoad() {}
	public function afterSave() {}

	public function beforeSave() {
		jimport('joomla.filter.output');

		if (empty($this->alias)){
			$this->alias = $this->title;
		}

		$this->alias = JFilterOutput::stringURLSafe($this->alias);
		return true;
	}

	public function findNearest($pid) {
		if (is_numeric($pid)) {
			return $this->model($pid);
		} else if (is_array($pid) and $pid[0] and $pid[1]){
			$db = JFactory::getDBO();
			$where = array('active = 1');
			if (isset($pid[2])) {
				$where[] = '(a.id in (select category_id from #__yandex_maps_category_to_map where map_id='.(int)$pid[2].'))';
			}
			$db->setQuery(
				$q = 'select 
					a.* 
				from 
					'.$this->table.' as a 
				where '.(implode(' and ', $where)).'
					
				order by 
					SQRT(POW( lat - '.$db->quote($pid[0]).', 2 ) + POW(lan - '.$db->quote($pid[1]).', 2 ) )  asc 
				limit 1'
			);

			$data = $db->loadObject();
			$item = new $this->classname;
			if (isset($data->id)) {
				$item->_attributes = $data;
				$this->afterLoad();
			} else {
				$item->title  = 'Uncategories';
				$item->mapids = array((int)$pid[2]);
				$item->save();
			}
			return $item;
		}
		return $this;
	}
	public function model($id, $where = '') {
		$db = JFactory::getDBO();
		$db->setQuery('select a.* 
			from '.$this->table.' as a 
			where 
			'.$this->primary_id. (is_array($id) ? ' in ('.implode(',', array_map('intval', count($id) ? $id : array(0) )).')' : '='.((int)$id)).' 
			'.$where.' limit 1');
		$item = new $this->classname;
		$item->_attributes = $db->loadObject();
		$this->afterLoad();
		return $item;
	}
	public function modelBySQL($query) {
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$item = new $this->classname;
		$item->_attributes = $db->loadObject();
		$this->afterLoad();
		return $item;
	}
	private $_condition;
	public function count($filter = '') {
		$db = JFactory::getDBO();
		$this->_condition = $filter ? ' where '.$filter : 'where 1';
		$this->_condition.= ' and active = 1';
		$db->setQuery('select count(id)
			from '.$this->table.' as a '.$this->_condition
		);
		return $db->loadResult();
	}
	public function modelsBySql($query, $return = false) {
		$db = JFactory::getDBO();
		$db->setQuery($query);
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
	public function models($select = 'a.*', $return = false, $filter = '', $orderby = 'a.title asc') {
		$db = JFactory::getDBO();
		$this->_condition = $filter ? ' where '.$filter : 'where 1';
		$this->_condition.= ' and active=1';
		$db->setQuery($q = 'select '.$select.'
			from '.$this->table.' as a '.$this->_condition.'
			order by '.$orderby.''
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
	public function getListQuery(){
		$db = JFactory::getDBO();
		$db = $this->getDbo();
        $query = $db->getQuery(true);
		$query->select('a.*');
		$query->from($this->table.' as a');
		
		$query->select('u.username AS author');
		$query->leftJoin('#__users AS u ON u.id = a.create_by');
		
		// Filter: like / search
		$search = $this->getState('filter.search');

		if (!empty($search)){
			$like = $db->quote('%' . $search . '%');
			$query->where('(a.title LIKE ' . $like.' or a.alias LIKE ' . $like.')');
		}
		
		$active = $this->getState('filter.published');
 
        if (is_numeric($active)){
            $query->where('a.active = '.(int) $active);
        } else {
            $query->where('a.active != -2');
		}
		
		$create_by = $this->getState('filter.create_by');
		
		if (is_numeric($create_by) and (int)$create_by > 0){
            $query->where('a.create_by = '.(int) $create_by);
        }

		$orderCol  = $this->state->get('list.ordering', 'a.id');
		$orderDirn = $this->state->get('list.direction', 'desc');
		$query->order($db->escape($orderCol . ' ' . $orderDirn));
		//$query->setLimit($this->getState('limit'), $this->getState('limitstart'));
		return $query;
	}
	public function getSlug() {
		return $this->alias!='' ? $this->id.':'.$this->alias : $this->id;
	}
	public function saveOrder($pks = null, $order = null) {
		foreach($pks as $i=>$pk) {
			$table = $this->model((int) $pk);
			if ($table->ordering != $order[$i]){
				$table->ordering = $order[$i];
				$table->save();
			}
		};
		return true;
	}

	public function getSettings() {
		if (!$this->_params) {
			$this->_params = clone(JComponentHelper::getParams('com_yandex_maps'));
			$params = new JRegistry();
			$params->loadObject($this->params);
			$this->_params->merge($params);
		}
		return $this->_params;
	}

	public function getParams($element = 'params') {
		if (isset($this->_data->$element) and $this->_data->$element and is_string($this->_data->$element)) {
			return json_decode($this->_data->$element);
		} else {
			return new stdClass();
		}
	}
	public function setParams($array = false, $element = 'params') {
		if ($array) {
			if (is_array($array)) {
				$this->_data->$element = json_encode($array);
			} else {
				$this->_data->$element = $array;
			}
		} else {
			$this->_data->$element = '{}';
		}
	}
	public function setMetaData($array) {
		$this->setParams($array, 'metadata');
	}
	public function getMetaData() {
		return $this->getParams('metadata');
	}
	public function getForm($data = [], $loadData = true){
		if (!method_exists('JModelList', 'getForm')) {
			$form = $this->loadForm('com_yandex_maps.'.$this->getClassName(), $this->getClassName(), ['control' => 'jform', 'load_data' => $loadData]);
			if (empty($form)) {
				return false;
			}

			$form->id = $this->id;

			return $form;
		} else {
			return parent::getForm($data, $loadData);
		}
	}
	public function loadFormData(){
		if (!method_exists('JModelList', 'loadFormData')) {
			$data = clone($this->_data);
			$data->params = isset($data->params) ? json_decode($data->params) : new stdClass();
			$data->metadata = isset($data->metadata) ? json_decode($data->metadata) : new stdClass();
			
		} else {
			$data  = parent::loadFormData();
		}
		
		$data->params = clone($this->params);
		$data->metadata = clone($this->metadata);
		
		
		return $data;
	}
}