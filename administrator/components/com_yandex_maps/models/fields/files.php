<?php
defined('JPATH_BASE') or die;
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
class JFormFieldFiles extends JFormFieldList{
	protected function getOptions(){
		$options = array();
		$path =  JPATH_ROOT.$this->getAttribute('path',DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_yandex_maps'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'map'.DIRECTORY_SEPARATOR.'tmpl');
		$dir = opendir($path);
		$options[] = JHtml::_('select.option', '', 'По умолчанию');
		while ($file = readdir($dir)) {
			if (preg_match('#^(.*)\.php$#', $file, $filename)) {
				$options[] = JHtml::_('select.option', $filename[1], $filename[1]);
			}
		}
		closedir($dir);
		return $options;
	}
	public function getAttribute($attr_name, $default = null){
		if (!empty($this->element[$attr_name])) {
			return $this->element[$attr_name];
		} else {
			return $default;
		}
	}
}
