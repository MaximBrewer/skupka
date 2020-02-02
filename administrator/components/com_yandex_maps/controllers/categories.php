<?php
defined('_JEXEC') or die('Access Deny');
class Yandex_MapsControllerCategories extends CController{
	public $_home = 'categories';
	public $_model = 'categories';
	public $_view = 'categories';

	public function add() {
		$view = $this->getView( 'Category', 'html' );
		$model = $this->getModel('Categories');
		if (isset($_POST['save'])){
			$model->_attributes = JFactory::getApplication()->input->get('jform', array(), 'ARRAY');
			if ($model->save() && $this->_close) {
				$this->redir($this->_close);
			}
		}
		$view->assign('item', $model);
		$view->assign('maps', $this->getModel('Maps'));
		JToolBarHelper::Title('Создание категории');
		JToolBarHelper::apply('categories.add');
		JToolBarHelper::save('categories.addandclose');
		JToolbarHelper::save2new('categories.addandnew');
		$view->display();
	}

	public function edit() {
		$id = JRequest::getInt('id', 0);
		$model = $this->getModel('Categories')->model($id);
		if (!$model->id) {
			JError::raiseError(404, "Категория не найдена");
		}
		if (isset($_POST['save'])){
			$model->_attributes = JFactory::getApplication()->input->get('jform', array(), 'ARRAY');
			if ($model->save() && $this->_close) {
				$this->redir($this->_close);
			}
		}
		JToolBarHelper::Title('Редактирование категории');
		$view = $this->getView( 'category', 'html' );
		$view->assign('maps', $this->getModel('Maps'));
		$view->assign('item',$model);
		JToolBarHelper::apply('categories.edit');
		JToolBarHelper::save('categories.editandclose');
		JToolbarHelper::save2new('categories.editandnew');
		$view->display();
	}
}