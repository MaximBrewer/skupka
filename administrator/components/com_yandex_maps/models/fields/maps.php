<?php
defined('JPATH_BASE') or die;
JFormHelper::loadFieldClass('list');
include_once JPATH_ADMINISTRATOR . "/components/com_yandex_maps/helpers/CModel.php";

JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_yandex_maps/models');

class JFormFieldMaps extends JFormFieldList{
	protected $type = 'maps';
	protected function getOptions(){
		$options = [];

		$maps = JModelLegacy::getInstance('Maps', 'Yandex_MapsModel')->models();
		$options[] = JHtml::_('select.option', '', 'Выберите карту');
		foreach($maps as $c) {
			$options[] = JHtml::_('select.option', $c->id, $c->title);
		}

		return $options;
	}
}
