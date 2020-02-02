<?php
defined('_JEXEC') or die('Access Deny');
class Yandex_MapsController extends JControllerLegacy{
	function maps() {
		$view = $this->getView('maps', 'html');
		$view->setModel($this->getModel('maps'), true);
		$view->display();
	}
	function categories() {
			$session = JFactory::getSession();
		$registry = $session->get('registry');
		if (!JModelLegacy::getInstance('Maps','Yandex_MapsModel')->count()) {
			JFactory::getApplication()->redirect('index.php?option=com_yandex_maps&task=maps');
		}
		$view = $this->getView('categories', 'html');
		$view->setModel($this->getModel('categories'), true);
		$view->display();
	}
	function load() {
		$objects = array();
		$forse_id = $this->input->get('forse_id', '0', 'INTEGER');
		$mapid = $this->input->get('map_id', '0', 'INTEGER');
		$limit = $this->input->get('limit', 500, 'INTEGER');
		($limit > 500) && ($limit = 500);
		$offset = $this->input->get('offset', 0, 'INTEGER');
		($offset < 0) && ($offset = 0);
		$bound = $this->input->get('bound', array(), 'ARRAY');
		$exclude = $this->input->get('exclude', array(), 'ARRAY');
		$map = $this->map = $this->getModel('Maps')->model((int)$mapid, 'and active=1');
		if (!$this->map->id) {
			echo json_encode($objects);
			JFactory::getApplication()->close();
		}
		$objects = $this->map->getOnlySelfObjectsByBound($bound, $offset, $limit, $exclude, false, $forse_id);
		echo json_encode($objects);
		JFactory::getApplication()->close();
	}
	function objects() {
		if (!JModelLegacy::getInstance('Categories','Yandex_MapsModel')->count()) {
			JFactory::getApplication()->redirect('index.php?option=com_yandex_maps&task=categories');
		}
		$view = $this->getView('objects', 'html');
		$view->setModel($this->getModel('objects'), true);
		$view->display();
	}
	function export() {
		if (isset($_POST['jform']) and isset($_POST['jform']['export'])) {
			include_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/exporter.php';
			yandex_maps_export::start($_POST['jform']['export']);
		}
		$view = $this->getView('export', 'html');
		$view->display();
	}
	function import() {
		if (isset($_POST['jform']) and isset($_POST['jform']['import'])) {
			include_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/importer.php';
			yandex_maps_import::start(@$_POST['jform']['import']);
		}
		$view = $this->getView('import', 'html');
		$view->display();
	}
	function display($tpl=null,$urlparams = Array()) {
		parent::display($tpl);
	}
}