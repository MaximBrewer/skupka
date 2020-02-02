<?php
defined('_JEXEC') or die('Access Deny');
class Yandex_MapsControllerMaps extends CController{
	public $_home = 'maps';
	public $_model = 'maps';
	public $_view = 'maps';

	public function add() {
		$view = $this->getView('map', 'html');

		if (isset($_POST['save'])){
			$this->model->_attributes = JFactory::getApplication()->input->get('jform', array(), 'ARRAY');
			if ($this->model->save() && $this->_close) {
				$this->redir($this->_close);
			}
		}

		$view->assign('item', $this->model);
		JToolBarHelper::Title('Создание карты');
		JToolBarHelper::apply('maps.add');
		JToolBarHelper::save('maps.addandclose');
		JToolbarHelper::save2new('maps.addandnew');
		$view->display();
	}

	public function categories() {
		$id = JRequest::getInt('id', 0);
		$model = $this->model->model($id);
		$view = $this->getView('select', 'html');
		$view->assign('items', $model->categories);
		$view->display();
		JFactory::getApplication()->close();
	}
	
	public function edit() {
		$id = JRequest::getInt('id', 0);
		$view = $this->getView( 'map', 'html' );
		$view->assign('loadobjects', JFactory::getApplication()->input->get('loadobjects', 1, 'INT'));
		if (!$id) {
			list($id) = $this->input->get('cid', array(), 'ARRAY');
			$view->assign('loadobjects', false);
		}
		if (!$id) {
			JError::raiseError(404, "Карта не найдена");
		}
		$model = $this->model->model($id);
		if (!$model->id) {
			JError::raiseError(404, "Карта не найдена");
		}
		if (isset($_POST['save'])){
			$model->_attributes = JFactory::getApplication()->input->get('jform', array(), 'ARRAY');
			if ($model->save() && $this->_close) {
				$this->redir($this->_close);
			}
		}
		
		JToolBarHelper::Title('Редактирование карты');
		$view->assign('item', $model);
		JToolBarHelper::apply('maps.edit');
		JToolBarHelper::save('maps.editandclose');
		JToolbarHelper::save2new('maps.editandnew');
		$view->display();
	}
}