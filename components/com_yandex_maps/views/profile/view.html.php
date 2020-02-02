<?php
defined("_JEXEC") or die("Access deny");
jimport('joomla.application.component.modellist');
JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_yandex_maps/models');
class Yandex_MapsViewProfile extends JViewLegacy{
	function display($tpl=null) {
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$doc->setBase(JURI::base());
		$params = JComponentHelper::getParams('com_yandex_maps');
		
		if (!JFactory::getUser()->id) {
			$message = "Вы должны быть зарегестрированы для этого действия";
			$url = 'index.php?option=com_users&view=login&return=' . base64_encode(jRoute::_('index.php?view=profile'));
			$app->redirect($url, $message);
		}
		
		$this->object = JModelLegacy::getInstance('Objects', 'Yandex_MapsModel');
		$this->object->setState('filter.create_by', JFactory::getUser()->id);
		$this->myobjects = $this->object->getItems();
		$this->myobjectstotal = $this->object->getTotal();
		$this->pagination = $this->object->getPagination();
		parent::display($tpl);
	}
}