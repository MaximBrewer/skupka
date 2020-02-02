<?php
defined('JPATH_BASE') or die;
jimport('joomla.form.helper');

JHtml::stylesheet(JURI::root() . 'media/com_yandex_maps/js/chosenImage/chosenImage.css',[], true);
JHtml::script(JURI::root() . 'media/com_yandex_maps/js/chosenImage/chosenImage.js');
JFactory::getApplication()->getDocument()->addScriptDeclaration('jQuery(function() {
	jQuery("[data-icons-select]").chosenImage({
		disable_search_threshold: 10,
		disable_search: true
	});
})');

class JFormFieldListIcons extends JFormField {
	protected function getInput(){
		$options = [];

		$icons = include JPATH_BASE.'/components/com_yandex_maps/helpers/images.php';

		foreach ($icons as $icon => $image) {
			if ((preg_match('#cluster#i', $icon) and !$this->getAttribute('icon')) or (!preg_match('#cluster#i', $icon) and $this->getAttribute('icon'))) {
				$options[] = "<option " .
						" value='{$icon}' " .
						($this->value === $icon ? " selected=true " : "") .
						" data-img-src=\"" . JURI::root().'media/com_yandex_maps/images/placemark/'.$image . "\"" .
					">{$icon}</option>";
			}
		}

		return "<select data-icons-select name='{$this->name}' id='{$this->id}'>" .
			implode('', $options) .
		'</select>';
	}

	public function getAttribute($attr_name, $default = null){
		if (!empty($this->element[$attr_name])) {
			return $this->element[$attr_name];
		}

		return $default;
	}
}
