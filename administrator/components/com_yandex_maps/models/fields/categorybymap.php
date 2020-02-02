<?php
defined('JPATH_BASE') or die;
JFormHelper::loadFieldClass('list');
include_once JPATH_ADMINISTRATOR."/components/com_yandex_maps/helpers/CModel.php";
JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_yandex_maps/models');
class JFormFieldCategoryByMap extends JFormFieldList{
	protected $type = 'categorybymap';
	protected function getOptions(){
		$options = array();
		$maps = JModelLegacy::getInstance('Maps', 'Yandex_MapsModel')->models();
		
		$options[] = JHtml::_('select.option', '', 'Выберите категорию');
		foreach($maps as $map) {
			$options[] = JHTML::_('select.optgroup', $map->title);
			$categories = $map->categories;
			foreach ($categories as $c) {
				$options[] = JHtml::_('select.option', $c->id, $c->title);
			}
			$options[] = JHTML::_('select.optgroup', $map->title);
		}
		return $options;
	}
}
