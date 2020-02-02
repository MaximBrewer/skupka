<?php
defined("_JEXEC") or die("Access deny");
class Yandex_MapsViewImport extends JViewLegacy{
	function display($tpl=null) {
		$tpl = JRequest::getCmd('layout', null);
		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::root().'media/com_yandex_maps/css/yandex_maps.css');
		JToolBarHelper::preferences('com_yandex_maps');
		JToolBarHelper::custom('import', 'box-remove', 'Импортировать','Импортировать');
		JToolBarHelper::Title('Импорт');
		JToolBarHelper::cancel('com_yandex_maps');
		parent::display($tpl);
	}
}