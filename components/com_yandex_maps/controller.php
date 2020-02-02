<?php
defined('_JEXEC') or die('Access Deny');
jimport('joomla.filesystem.file');
class Yandex_MapsController extends JControllerLegacy{
	function display($cachable = null, $urlparams = null) {
		$model = $this->getModel('Maps');
		$view = $this->getView( jFactory::getApplication()->input->get('view', 'yandex_maps'), 'html' );
		$view->assign('items', $model->models('*',false,'active=1'));
		$view->assign('params', JComponentHelper::getParams('com_yandex_maps'));
		$view->display($cachable, $urlparams);
	}

	function widget() {
		$app =  JFactory::getApplication();
		$mapid = $this->input->get('map_id', '0', 'INTEGER');
		$name = JFile::makeSafe($this->input->get('name', 'widget', 'STRING'));
		$map = $this->map = $this->getModel('Maps')->model((int)$mapid, 'and active=1');

		if (!$this->map->id) {
			JError::raiseError(404, "Map not found");
		}

		header('Content-Type: text/html; charset=utf-8');
		
		if (file_exists(JPATH_ROOT.'/templates/'.$app->getTemplate().'/html/com_yandex_maps/helpers/html/ajax/'.$name.'.php')) {
			echo include JPATH_ROOT.'/templates/'.$app->getTemplate().'/html/com_yandex_maps/helpers/html/ajax/'.$name.'.php';
		} else if (file_exists(JPATH_ROOT.'/components/com_yandex_maps/helpers/html/ajax/'.$name.'.php')) {
			echo include JPATH_ROOT.'/components/com_yandex_maps/helpers/html/ajax/'.$name.'.php';
		} else {
			JError::raiseError(404, "Widget not found");
		}

		JFactory::getApplication()->close();
	}

	function load() {
		$forse_id = $this->input->get('forse_id', '0', 'INTEGER');
		$mapid = $this->input->get('map_id', '0', 'INTEGER');
		$limit = $this->input->get('limit', 500, 'INTEGER');
		$search = $this->input->get('search', false, 'STRING');
		($limit > 500) && ($limit = 500);
		$offset = $this->input->get('offset', 0, 'INTEGER');
		($offset < 0) && ($offset = 0);
		$bound = $this->input->get('bound', array(), 'ARRAY');
		$exclude = $this->input->get('exclude', array(), 'ARRAY');
		$filters = $this->input->get('filters', array(), 'ARRAY');
		$map = $this->map = $this->getModel('Maps')->model((int)$mapid, 'and active=1');


		if (!$map->id) {
			JError::raiseError(404, "Карта не найдена");
		}

		$cache = JFactory::getCache('com_yandex_maps');

		if ($cache) {
			$objects = $cache->call([$this->map, 'getObjectsByBound'], $bound, $offset, $limit, $exclude, $search, $forse_id, false, $filters, false, serialize($_POST));
		} else {
			$objects = $this->map->getObjectsByBound($bound, $offset, $limit, $exclude, $search, $forse_id, false, $filters, false, serialize($_POST));
		}

		if ($this->input->get('generate', 0, 'BOOLEAN')) {
			$data = (object)$objects;
			$objects['html'] = include JPATH_COMPONENT . '/helpers/html/itemslist.php';
		}

		echo json_encode($objects);
		JFactory::getApplication()->close();
	}

	function map() {
		$view = $this->getView( 'Map', 'html' );
		$mapid =  $this->input->get('id',0);
		$map = $this->getModel('Maps')->model((int)$mapid, 'and active=1');
		if (!$map->id) {
			JError::raiseError(404, "Карта не найдена");
		}
		
		$pathway = JFactory::getApplication()->getPathway(); 

		$path = [new stdClass(), new stdClass()];
		$path[1]->link = null;
		$path[1]->name = $map->title;
		$path[0]->link = 'index.php?option=com_yandex_maps';
		$path[0]->name = 'Яндекс карты';
		$pathway->setPathway($path);
		
		$view->assign('map', $map);
		$view->assign('params', JComponentHelper::getParams('com_yandex_maps'));

		$view->display();
	}

	function category() {
		$view = $this->getView( 'Category', 'html' );
		$id =  $this->input->get('id',0);
		$category = $this->getModel('Categories')->model((int)$id, 'and active=1');
		if (!$category->id) {
			JError::raiseError(404, "Категория не найдена");
		}
		$view->assign('item', $category);
		$view->assign('params', JComponentHelper::getParams('com_yandex_maps'));
		$view->display();
	}

	function loadObject() {
		$id  =  (int)$this->input->get('id',0);
		$object = $this->getModel('Objects')->model($id, 'and active=1');
		if (!$object->id) {
			JError::raiseError(404, "Объект не найден");
		}
		$object->link = JRoute::_('index.php?option=com_yandex_maps&task=object&id='.$object->slug);
        JPluginHelper::importPlugin('yandexmapssource');
        jHtml::_('xdwork.description', $object, $object->map);
		echo (json_encode($object->_data));
		JFactory::getApplication()->close();
	}

	function object() {
		$view = $this->getView( 'object', 'html' );
		$id =  $this->input->get('id',0);
		$object = $this->getModel('Objects')->model((int)$id, JFactory::getUser()->get('isRoot') ? '' : 'and (active=1 '.(JFactory::getUser()->id? ' or create_by='.JFactory::getUser()->id.'' : '').')');

		if (!$object->id) {
			JError::raiseError(404, "Объект не найден");
		}
		$params = JComponentHelper::getParams('com_yandex_maps');
		$pathway = JFactory::getApplication()->getPathway(); 
		$path = [new stdClass(),new stdClass(),new stdClass(),new stdClass()];
		$path[3]->link = null;
		$path[3]->name = $object->title;
		$path[2]->link = 'index.php?option=com_yandex_maps&task=category&id='.$object->category->id;
		$path[2]->name = $object->category->title;
		$path[1]->link = 'index.php?option=com_yandex_maps&task=map&id='.$object->map->id;
		$path[1]->name = $object->map->title;
		$path[0]->link = 'index.php?option=com_yandex_maps';
		$path[0]->name = 'Яндекс карты';
		if (!$params->get('show_category_in_breadcrumbs', 1)) {
			unset($path[2]);
		}
		$pathway->setPathway($path);
		
		$view->assign('object', $object);
		$view->assign('params', $params);
		$view->display();
	}
}