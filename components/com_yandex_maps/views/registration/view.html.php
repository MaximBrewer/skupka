<?php
defined("_JEXEC") or die("Access deny");
class Yandex_MapsViewRegistration extends JViewLegacy{
	function display($tpl=null) {
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$doc->setBase(JURI::base());
		$params = JComponentHelper::getParams('com_yandex_maps');
		if ($app->input->get('map_id', 0, 'INT')) {
			$model = JModelLegacy::getInstance('Maps', 'Yandex_MapsModel')->model($app->input->get('map_id', 0, 'INT'));
			if ($model->id) {
				$buf = new JRegistry();
				$buf->loadString($model->_data->params);
				$params->merge($buf);
			}
		}
		$this->params = $params;
		if (!jhtml::_('xdwork.isAjax') and $params->get('registration_organization_active',1)==2 and !JFactory::getUser()->id) {
			$message = "Вы должны быть зарегестрированы для этого действия";
			$url = 'index.php?option=com_users&view=login&return=' . base64_encode(jRoute::_('index.php?view=registration'));
			$app->redirect($url, $message);
			jexit();
		}
		
		jhtml::_('xdwork.registration', true);
		parent::display($tpl);
	}
}