<?php
defined('JPATH_PLATFORM') or die;

class JFormFieldMapObjects extends JFormField {
	protected $type = 'MapObjects';

	protected function getLabel() {
		return JText::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_FIELD_MAPOBJECTS_LABEL');
	}

    function getInput() {
        ob_start();
        include dirname(__FILE__) . DIRECTORY_SEPARATOR .'tmpl' . DIRECTORY_SEPARATOR . 'mapobjects.php';
		return ob_get_clean();
	}
}
