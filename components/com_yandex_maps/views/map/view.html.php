<?php
defined("_JEXEC") or die("Access deny");
class Yandex_MapsViewMap extends JViewLegacy{
	function display($tpl=null) {
		$doc = JFactory::getDocument();
		jhtml::_('xdwork.autocomplete');
		jhtml::_('xdwork.kladru');// датапикер для формы ввода даты
		jhtml::_('xdwork.datetimepicker');// датапикер для формы ввода даты
		jhtml::_('xdwork.dialog');// всплывашки
		jhtml::_('xdwork.mask'); // маска для телефонов и дат
		JHtml::script(JURI::root().'media/com_yandex_maps/js/registration.js');
		$doc->setBase(JURI::base());
		$this->setLayout($this->map->settings->get('template', 'default'));
		$this->setMetaData();
		parent::display($tpl);
	}
	function setMetaData() {
		$doc = JFactory::getDocument();
		if ($this->map->title  and $this->map->settings->get('use_map_title', 1)) {
			$doc->setTitle($this->map->title);
		}
		if (!empty($this->map->metadata->metadesc)) {
			$doc->setDescription($this->map->metadata->metadesc);
		}
		if (!empty($this->map->metadata->metakey)) {
			$doc->setMetadata('keywords', $this->map->metadata->metakey);
		}
	}
}