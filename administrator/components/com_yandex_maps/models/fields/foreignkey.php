<?php
defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldForeignKey extends JFormField {

	protected $type = 'foreignkey';
	private $input_type;
	private $table;
	private $key_field;
	private $value_field;

	protected function getInput() {

		$this->input_type = $this->getAttribute('input_type');

		$this->table = $this->getAttribute('table');

		$this->key_field = (string) $this->getAttribute('key_field');


		$this->value_field = (string) $this->getAttribute('value_field');

		$html = '';

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select(
				array(
					$db->quoteName($this->key_field),
					$db->quoteName($this->value_field)
				)
			)
			->from($this->table)
			->where((string) $this->getAttribute('where') ?: '(active=1)');

		$db->setQuery($query);
		$results = $db->loadObjectList();

		$input_options = 'class="' . $this->getAttribute('class') . '"';

		$options = array();

		foreach ($results as $result) {
			$options[] = JHtml::_('select.option', $result->{$this->key_field}, $result->{$this->value_field});
		}
		switch ($this->input_type) {
			case 'list':
				return $options;
			default:

				$value = $this->value;

				if (is_string($value)) {
					$value = array($value);
				} else if (is_object($value)) {
					$value = get_object_vars($value);
				}

				if ($this->multiple) {
					$input_options .= 'multiple="multiple"';
				} else {
					array_unshift($options, JHtml::_('select.option', '', ''));
				}

				$html = JHtml::_('select.genericlist', $options, $this->name, $input_options, 'value', 'text', $value, $this->id);
				break;
		}

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
