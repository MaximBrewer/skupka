<?php
/**
 
 
 * @copyright (C) 2014 Interamind LTD, http://www.interamind.com
 
**/
defined('JPATH_BASE') or die();

class JFormFieldTitle extends JFormField {

	public $type = 'Title';

	protected function getInput() {
		$html = '';
		if ($this->element['default']) {
			$html .= '<div style="color:#0D507A;margin: 10px 0 5px 0; font-weight: bold; padding: 5px; background-color: #cacaca; float: left;">';
			$html .= JText::_($this->element['default']);
			$html .= '</div>';
		}
		
		return $html;
	}
}
