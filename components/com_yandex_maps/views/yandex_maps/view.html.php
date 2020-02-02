<?php
defined("_JEXEC") or die("Access deny");
class Yandex_MapsViewYandex_Maps extends JViewLegacy{
	function display($tpl=null) {
		$tpl = JRequest::getCmd('layout', null);
		$doc = JFactory::getDocument();
		//$doc->addStyleSheet(JURI::root().'media/com_yandex_maps/css/yandex_maps.css');
		parent::display($tpl);
	}
}