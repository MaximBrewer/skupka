<?php
defined('JPATH_BASE') or die;
JFormHelper::loadFieldClass('list');
include_once JPATH_ADMINISTRATOR."/components/com_yandex_maps/helpers/CModel.php";
JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_yandex_maps/models');
class JFormFieldCategoryId extends JFormFieldList{
	protected $type = 'categoryid';
	protected function getOptions(){
		$options = array();
		$categories = JModelLegacy::getInstance('Categories', 'Yandex_MapsModel')->models();
		$options[] = JHtml::_('select.option', '', 'Выберите категорию');
		foreach($categories as $c) {
			$options[] = JHtml::_('select.option', $c->id, $c->title);
		}
		return $options;
	}
}
