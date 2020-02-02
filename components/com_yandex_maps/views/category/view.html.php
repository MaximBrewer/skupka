<?php
defined("_JEXEC") or die("Access deny");
class Yandex_MapsViewCategory extends JViewLegacy{
	function display($tpl=null) {
		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::root().'media/com_yandex_maps/css/frontend.css');
		parent::display($tpl);
	}
}