<?php

/**
 * @copyright	Copyright (C) 2011 Cedric KEIFLIN alias ced1870
 * https://www.joomlack.fr
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once 'ckformfield.php';

class JFormFieldCkdocumentation extends CKFormField {

	protected $type = 'ckdocumentation';

	protected function getLabel() {
		return '';
	}

	protected function getInput() {
		$html = array();

		$icon = $this->element['icon'] ? $this->element['icon'] : 'file-alt';
		$url = $this->element['url'] ? $this->element['url'] : 'https://www.joomlack.fr/en/documentation';
		$html[] = '<div class="ckinfo"><i class="fas fa-' . $icon . '"></i><a href="' . $url . '" target="_blank">' . JText::_('SLIDESHOWCK_DOCUMENTATION') . '</a></div>';

		return implode('', $html);
	}
}

