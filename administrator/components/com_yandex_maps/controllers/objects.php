<?php
defined('_JEXEC') or die('Access Deny');
class Yandex_MapsControllerObjects extends CController{
	public $_home = 'objects';
	public $_model = 'objects';
	public $_view = 'objects';

	public function add() {
		$org_mode = JFactory::getApplication()->input->get('mode') == 'organization';
		$view = $this->getView( 'Object', 'html' );
		$model = $this->getModel('Objects');

		if (isset($_POST['save'])){
			$model->_attributes = JFactory::getApplication()->input->get('jform', array(), 'ARRAY');

			if ($model->save() && $this->_close) {
				$this->redir($this->_close);
			}
		}

		$view->assign('item', $model);
		$view->assign('maps', $this->getModel('Maps'));

		JToolBarHelper::Title('Создание объекта');
		JToolBarHelper::apply($org_mode ? 'objects.addorg' : 'objects.add');
		JToolBarHelper::save('objects.addandclose');
		JToolbarHelper::save2new('objects.addandnew');
		$view->display();
	}

	public function searchAjax(){
		// Required objects
		$app = JFactory::getApplication();
		$objects = jFactory::getDBO()->setQuery('select id as value, title as text from #__yandex_maps_objects where title like '.jFactory::getDBO()->quote('%'.$app->input->get('like', null, 'STRING').'%'))->loadObjectList();
		echo json_encode($objects);

		$app->close();
	}

	public function edit() {
		$id = JRequest::getInt('id', 0);
		$model = $this->getModel('Objects')->model($id);
		if (!$model->id) {
			JError::raiseError(404, "Объект не найден");
		}
		if (isset($_POST['save'])){
			$model->_attributes = JFactory::getApplication()->input->get('jform', array(), 'ARRAY');
			if ($model->save() && $this->_close) {
				$this->redir($this->_close);
			}
		}
		JToolBarHelper::Title('Редактирование объекта');
		$view = $this->getView( 'Object', 'html' );
		$view->assign('maps', $this->getModel('Maps'));
		$view->assign('item',$model);
		JToolBarHelper::apply('objects.edit');
		JToolBarHelper::save('objects.editandclose');
		JToolbarHelper::save2new('objects.editandnew');
		$view->display();
	}
}