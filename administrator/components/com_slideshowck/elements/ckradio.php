<?php

/**
 * @copyright	Copyright (C) 2011 Cedric KEIFLIN alias ced1870
 * https://www.joomlack.fr
 * @license		GNU/GPL
 * */
defined('JPATH_PLATFORM') or die;

require_once 'ckformfield.php';

class JFormFieldCkradio extends CKFormField {

	protected $type = 'ckradio';

	protected function getInput() {
		$html = array();

		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="radio ' . (string) $this->element['class'] . '"' : ' class="radio"';
		$icon = $this->element['icon'];

		// Start the radio field output.
		$html[] = $icon ? '<div style="display:inline-block;vertical-align:top;margin-top:5px;width:20px;"><img src="' . $this->mediaPath . $icon . '" style="margin-right:5px;" /></div>' : '<div style="display:inline-block;width:20px;"></div>';
		$html[] = '<fieldset id="' . $this->id . '-fieldset"' . $class . ' style="display:inline-block;">';
		$html[] = '<input type="hidden" isradio="1" id="' . $this->id . '" class="' . $this->element['class'] . '" value="' . $this->value . '" />';

		// Get the field options.
		$options = $this->getOptions();

		// Build the radio field output.
		foreach ($options as $i => $option) {

			if (stristr($option->text, "img:"))
				$option->text = '<img src="' . $this->mediaPath . str_replace("img:", "", $option->text) . '" style="margin:0; float:none;" />';

			// Initialize some option attributes.
			$checked = ((string) $option->value == (string) $this->value) ? ' checked="checked"' : '';
			$class = !empty($option->class) ? ' class="' . $option->class . '"' : '';
			$disabled = !empty($option->disable) ? ' disabled="disabled"' : '';

			// Initialize some JavaScript option attributes.
			$onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';
			$onclick = ' onclick="$(\'' . $this->id . '\').setProperty(\'value\',this.value);"';

			$html[] = '<input type="radio" id="' . $this->id . $i . '" name="' . $this->name . '"' . ' value="'
					. htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' . $checked . $class . $onclick . $disabled . '/>';

			$html[] = '<label for="' . $this->id . $i . '"' . $class . '>'
					. JText::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)) . '</label>';
		}

		// End the radio field output.
		$html[] = '</fieldset>';

		return implode($html);
	}

	

}
