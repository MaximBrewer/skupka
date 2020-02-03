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

require_once JPATH_ROOT . '/modules/mod_maximenuck/helper.php';


class JFormFieldCkstyleswizard extends JFormField {

	protected $type = 'ckstyleswizard';

	protected function getInput() {
		return '';
	}

	protected function getLabel() {

		$input = new JInput();
		$imgpath = JUri::root(true) . '/modules/mod_maximenuck/elements/images/';
		$doc = JFactory::getDocument();
		$doc->addScript(JUri::root(true) . '/modules/mod_maximenuck/elements/ckbox/ckbox.js');
		$doc->addStylesheet(JUri::root(true) . '/modules/mod_maximenuck/elements/ckbox/ckbox.css');

		// check if the maximenu params component is installed
		$com_params_text = '';
		$button = '';
		if ( file_exists(JPATH_ROOT . '/administrator/components/com_maximenuck/maximenuck.php') ) {
			$com_params_text = '<img src="' . $imgpath . 'accept.png" />' . JText::_('MOD_MAXIMENUCK_COMPONENT_PARAMS_INSTALLED');
			//$button = '<input name="' . $this->name . '_button" id="' . $this->name . '_button" class="ckpopupwizardmanager_button" style="background-image:url(' . $imgpath . 'pencil.png);width:100%;" type="button" value="' . JText::_('MAXIMENUCK_STYLES_WIZARD') . '" onclick="SqueezeBox.fromElement(this, {handler:\'iframe\', size: {x: 800, y: 500}, url:\'' . JUri::root(true) . '/administrator/index.php?option=com_maximenuck&view=modules&view=styles&&layout=modal&id=' . $input->get('id',0,'int') .'\'})"/>';
			$button .= '<input name="' . $this->name . '_button" id="' . $this->name . '_button" class="ckpopupwizardmanager_button" style="background-image:url(' . $imgpath . 'pencil.png);width:100%;min-width: 300px;" type="button" value="' . JText::_('MAXIMENUCK_STYLES_WIZARD') . '" onclick="CKBox.open({handler:\'iframe\', fullscreen: true, url:\'' . JUri::root(true) . '/administrator/index.php?option=com_maximenuck&view=modules&view=styles&&layout=modal&id=' . $input->get('id',0,'int') .'\'})"/>';
		} else {
			$com_params_text = '<img src="' . $imgpath . 'cross.png" />' . JText::_('MOD_MAXIMENUCK_COMPONENT_PARAMS_NOT_INSTALLED');
			$button = '';
		}

		$html = '';
		// css styles already loaded into the ckmaximenuchecking field
		$html .= $com_params_text ? '<div class="maximenuckchecking">' . $com_params_text . '</div>' : '';
		$html .= '<div class="clr"></div>';
		$html .= $button;

		return $html;
	}
}
