<?php

/**
 * @copyright	Copyright (C) 2011 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.form');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

require_once JPATH_ROOT . '/modules/mod_accordeonck/helper.php';


class JFormFieldCkstyleswizard extends JFormField {

	protected $type = 'ckstyleswizard';

	protected function getInput() {
		return '';
	}

	protected function getLabel() {

		$input = new JInput();
		$imgpath = JUri::root(true) . '/modules/mod_accordeonck/elements/images/';
		$doc = JFactory::getDocument();

		// check if the params component is installed
		$com_params_text = '';
		$button = '';
		if ( file_exists(JPATH_ROOT . '/administrator/components/com_accordeonck/accordeonck.php') ) {
			$doc->addScript(JUri::root(true) . '/administrator/components/com_accordeonck/assets/ckbox.js');
			$doc->addStylesheet(JUri::root(true) . '/administrator/components/com_accordeonck/assets/ckbox.css');
			$doc->addStylesheet(JUri::root(true) . '/modules/mod_accordeonck/elements/ckaccordeonelements.css');
			$com_params_text = '<img src="' . $imgpath . 'accept.png" />' . JText::_('MOD_ACCORDEONCK_COMPONENT_PARAMS_INSTALLED');

			$button .= '<input name="' . $this->name . '_button" id="' . $this->name . '_button" class="ckpopupwizardmanager_button" style="background-image:url(' . $imgpath . 'pencil.png);width:100%;" type="button" value="' . JText::_('ACCORDEONCK_STYLES_WIZARD') . '" onclick="if(' . $input->get('id',0,'int').' != 0) {CKBox.open({handler:\'iframe\', fullscreen: true, url:\'' . JUri::root(true) . '/administrator/index.php?option=com_accordeonck&view=modules&view=styles&&layout=modal&id=' . $input->get('id',0,'int') .'\'});} else {alert(\'Please save the module first\');}"/>';
		} else {
			$com_params_text = '<img src="' . $imgpath . 'cross.png" />' . JText::_('MOD_ACCORDEONCK_COMPONENT_PARAMS_NOT_INSTALLED');
			$button = '';
		}

		$html = '';
		// css styles already loaded into the checking field
		$html .= '<style>.accordeonckchecking {background:#efefef;border: none;
border-radius: 3px;
color: #333;
font-weight: normal;
line-height: 24px;
padding: 5px;
margin: 3px 0;
text-align: left;
text-decoration: none;
}
.accordeonckchecking img {
margin: 5px;
}</style>';
		$html .= $com_params_text ? '<div class="accordeonckchecking">' . $com_params_text . '</div>' : '';
		$html .= '<div class="clr"></div>';
		$html .= $button;

		return $html;
	}
}
