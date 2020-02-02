<?php
defined("_JEXEC") or die("Access deny");
class Yandex_MapsViewSelect extends JViewLegacy{
	function display($tpl=null) {
		$this->items = $this->items;
		parent::display($tpl);
	}
}