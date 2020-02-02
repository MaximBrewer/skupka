<?php
defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldCategories extends JFormField {

	protected $type = 'categories';

	protected function getInput() {

		$html = '';
		$options = array();
		
		$options[] = JHtml::_('select.option', 0, 'Все');
		$options[] = JHtml::_('select.option', -1, 'Ни одна');
		
		if (isset($this->form->id) and (int)$this->form->id) {
			$map = JModelLegacy::getInstance('Maps', 'Yandex_MapsModel')->model((int)$this->form->id);
			if ($map and $map->id) {
				foreach ($map->getCategoriesEx() as $result) {
					$options[] = JHtml::_('select.option', $result->id, $result->title);
				}
			}
		}
		$input_options = '';
		if ($this->multiple) {
			$input_options.= ' multiple="multiple" ';
		}
		
		$value = $this->value;

		if (is_string($value)) {
			$value = array($value);
		} else if (is_object($value)) {
			$value = get_object_vars($value);
		}

		if ($value and is_array($value)) {	
			foreach ($value as $i=>$val) {
				if ($val==0 || $val==-1) {
					$value = array($val);
					break;
				}
			}
		}

		$html = JHtml::_('select.genericlist', $options, $this->name, $input_options, 'value', 'text', $value, $this->id);

		return $html;
	}

	public function getAttribute($attr_name, $default = null) {
		if (!empty($this->element[$attr_name])) {
			return $this->element[$attr_name];
		} else {
			return $default;
		}
	}
}
