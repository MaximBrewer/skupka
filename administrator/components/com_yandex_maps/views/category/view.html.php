<?php
defined("_JEXEC") or die("Access deny");
class Yandex_MapsViewCategory extends JViewLegacy{
	function getForm() {
		return $this->form;
	}
	function display($tpl=null) {
		$this->form = $this->item->getForm();
		$tpl = JRequest::getCmd('layout', null);
		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::root().'media/com_yandex_maps/css/yandex_maps.css');
		$lang = in_array(JFactory::getLanguage()->getTag(),array('ru-RU','en-US','tr-TR','uk-UA'))?JFactory::getLanguage()->getTag():'en-US';
		JToolBarHelper::cancel('categories');
		$this->assign('lang', $lang); 
		parent::display($tpl);
	}
}