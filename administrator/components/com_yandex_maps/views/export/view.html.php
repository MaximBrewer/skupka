<?php
defined("_JEXEC") or die("Access deny");
class Yandex_MapsViewExport extends JViewLegacy{
	function display($tpl=null) {
		$tpl = JRequest::getCmd('layout', null);
		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::root().'media/com_yandex_maps/css/yandex_maps.css');
		JToolBarHelper::preferences('com_yandex_maps');
		JToolBarHelper::custom('export', 'box-add', 'Экспортировать','Экспортировать');
		JToolBarHelper::cancel('com_yandex_maps');
		JToolBarHelper::Title('Экспорт');
		parent::display($tpl);
	}
}