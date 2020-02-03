<?php
defined('_JEXEC') or die('Access Deny');
jimport('joomla.application.component.controlleradmin');
abstract class CController extends JControllerAdmin{
	public $_home = '{home}';
	public $_model = '{model}';
	public $_view = '{view}';
	
	public $_close = false;

	protected function isOrgMode() {
		$form = JFactory::getApplication()->input->get('jform', array(), 'ARRAY');

		if ($form['organization_data']) {
			return true;
		}

		return JFactory::getApplication()->input->get('mode') == 'organization';
	}

	abstract public function add();

	public function addorg() {
		JFactory::getApplication()->input->set('mode', 'organization');
		$this->add();
	}

	abstract public function edit();
	
	public function getModel($name = '', $prefix = 'Yandex_MapsModel', $config = Array()){
		return parent::getModel($name ? $name : $this->_model, $prefix, array('ignore_request' => true));
	}

	public function addandnew() {
		$this->_close = 2;
		if ($this->isOrgMode()) {
			$this->addorg();
		} else {
			$this->add();
		}
	}

	public function editandnew() {
		$this->_close = 2;
		$this->edit();
	}

	public function addandclose() {
		$this->_close = 1;

		if ($this->isOrgMode()) {
			$this->addorg();
		} else {
			$this->add();
		}
	}

	public function editandclose() {
		$this->_close = 1;
		$this->edit();
	}

	public function __construct() {
		parent::__construct();
		$this->model 	= $this->getModel($this->_model);
		$this->view 	= $this->getView($this->_view, 'html');
	}

	public function publish() {
		$data = array('publish' => 1, 'unpublish' => 0);
		$task = $this->getTask();
		$value = JArrayHelper::getValue($data, $task, 0, 'int');
		$this->model->each($_POST['cid'], 'active', $value);
		$this->setRedirect(JRoute::_('index.php?option=com_yandex_maps&task='.$this->_home, false));
	}
	public function copy() {
		$this->model->each($_POST['cid'], 'copy', JRequest::getInt('mode', 0));
		$this->setRedirect(JRoute::_('index.php?option=com_yandex_maps&task='.$this->_home, false));
	}
	public function unpublish() {
		$this->model->each($_POST['cid'], 'active', false);
		$this->setRedirect(JRoute::_('index.php?option=com_yandex_maps&task='.$this->_home, false));
	}
	public function gototrash() {
		$app = JFactory::getApplication();
		$app->setUserState("com_yandex_maps.".$this->_home.".filter.published", $app->getUserState("com_yandex_maps.".$this->_home.".filter.published", 0) == -2 ? null : -2);
		$this->setRedirect(JRoute::_('index.php?option=com_yandex_maps&task='.$this->_home, false));
	}
	public function redir($mode) {
		switch ($mode) {
			case 1 : $this->setRedirect(JRoute::_('index.php?option=com_yandex_maps&task='.$this->_home, false));break;
			case 2 : $this->setRedirect(JRoute::_('index.php?option=com_yandex_maps&task='.$this->_home.'.add', false));break;
		}
	}
	public function delete() {
		$this->model->each($_POST['cid'], 'delete', 1);
		$this->setRedirect(JRoute::_('index.php?option=com_yandex_maps&task='.$this->_home, false));
	}
}