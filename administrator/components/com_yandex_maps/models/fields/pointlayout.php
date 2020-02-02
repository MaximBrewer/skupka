<?php
defined('JPATH_BASE') or die;
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldPointLayout extends JFormFieldList{
	protected function getOptions(){
		$options = array();

		$options[] = JHtml::_('select.option', '', 'Без шаблона');
        $options[] = JHtml::_('select.option', 'circle_ballon', 'Белый круг с иконкой');
        $options[] = JHtml::_('select.option', 'circle_ballon_simple', 'Белый круг');

		return $options;
	}
	public function attr($attr_name, $default = null){
		if (!empty($this->element[$attr_name])) {
			return $this->element[$attr_name];
		} else {
			return $default;
		}
	}
}
