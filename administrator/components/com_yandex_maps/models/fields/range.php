<?php
defined('JPATH_BASE') or die;
class JFormFieldRange extends JFormField{
	protected function getInput(){
		return '<input type="range" value="'.$this->value.'" name="'.$this->name.'" min="'.$this->attr('min', 0).'" max="'.$this->attr('max', 100).'">';
	}
	public function attr($attr_name, $default = null){
		if (!empty($this->element[$attr_name])) {
			return $this->element[$attr_name];
		} else {
			return $default;
		}
	}
}
