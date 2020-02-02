<?php
defined('JPATH_BASE') or die;
JFormHelper::loadFieldClass('list');
include_once JPATH_ADMINISTRATOR."/components/com_yandex_maps/helpers/CModel.php";
JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_yandex_maps/models');
class JFormFieldObjects extends JFormFieldList{
	protected $type = 'maps';
	public static function ajaxfield($selector='#jform_request_id', $allowCustom = true)
	{
		// Get the component parameters
		$params = JComponentHelper::getParams("com_tags");
		$minTermLength = (int) $params->get("min_term_length", 3);

		// Tags field ajax
		$chosenAjaxSettings = new JRegistry(
			array(
				'selector'      => $selector,
				'type'          => 'GET',
				'url'           => JUri::base() . 'index.php?option=com_yandex_maps&task=objects.searchAjax',
				'dataType'      => 'json',
				'jsonTermKey'   => 'like',
				'minTermLength' => $minTermLength
			)
		);
		JHtml::_('formbehavior.ajaxchosen', $chosenAjaxSettings);
	}
	protected function getInput(){
		$id    = isset($this->element['id']) ? $this->element['id'] : null;
		$cssId = '#' . $this->getId($id, $this->element['name']);
		self::ajaxfield($cssId);
		$input = parent::getInput();
		return $input;
	}
	protected function getOptions(){
		$options = array();
		$items = JModelLegacy::getInstance('Objects', 'Yandex_MapsModel')->models();
		$options[] = JHtml::_('select.option', 0, 'Выберите объект');
		foreach($items as $c) {
			$options[] = JHtml::_('select.option', $c->id, htmlspecialchars($c->title));
		}
		return $options;
	}
}
