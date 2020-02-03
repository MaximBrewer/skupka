<?php

/**
 * @copyright	Copyright (C) 2011 Cedric KEIFLIN alias ced1870
 * https://www.joomlack.fr
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once 'ckformfield.php';

class JFormFieldCkspacer extends CKFormField {

	protected $type = 'ckspacer';

	protected function getLabel() {
		return '';
	}

	protected function getInput() {
		$html = array();
		$class = $this->element['class'] ? (string) $this->element['class'] : '';

		$style = $this->element['style'] ? $this->element['style'] : '';

		if ($style == 'title') {
			$doc = JFactory::getDocument();
			$styles = '.ckinfo.cktitle {
				background:#666;
				color: #eee;
				text-transform: uppercase;
				}
	';
			$doc->addStyleDeclaration($styles);
		}
		
		if ((string) $this->element['hr'] == 'true') {
			$html[] = '<hr class="' . $class . '" />';
		} else {
			$label = '';
			// Get the label text from the XML element, defaulting to the element name.
			$text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
			$text = $this->translateLabel ? JText::_($text) : $text;

			// Test to see if the patch is installed
			$testpatch = $this->element['testpatch'] ? $this->testPatch($this->element['testpatch']) : null;
			$text = $testpatch ? $testpatch : $text;

			// set the icon
			$icon = $this->element['icon'] ? $this->element['icon'] : 'info';
			$html[] = '<div class="ckinfo' . ($style == 'title' ? ' cktitle' : '') . '">' . ($style == 'title' ? '' : '<i class="fas fa-' . $icon . '"></i>') . $text . '</div>';
		}
		
		return implode('', $html);
	}
}

