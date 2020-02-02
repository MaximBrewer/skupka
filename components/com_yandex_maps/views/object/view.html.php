<?php
defined("_JEXEC") or die("Access deny");
class Yandex_MapsViewObject extends JViewLegacy{
	function display($tpl=null) {
		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::root().'media/com_yandex_maps/css/frontend.css');
		jhtml::_('xdwork.dialog');
		$this->setMetaData();
		parent::display($tpl);
	}
	function setMetaData() {
		$doc = JFactory::getDocument();
		if ($this->object->title and $this->object->map->settings->get('use_object_title', 1)) {
			$doc->setTitle($this->object->title);
		}
		if (!empty($this->object->metadata->metadesc)) {
			$doc->setDescription($this->object->metadata->metadesc);
		}
		if (!empty($this->object->metadata->metakey)) {
			$doc->setMetadata('keywords', $this->object->metadata->metakey);
		}
	}
}