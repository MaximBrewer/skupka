<?php

/**
 * @copyright	Copyright (C) 2013 Cédric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * @license		GNU/GPL
 * */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.event.plugin');

class plgSystemAccordeonckparams extends JPlugin {

	function plgSystemAccordeonckparams(&$subject, $params) {
		parent::__construct($subject, $params);
	}

	/**
	 * @param       JForm   The form to be altered.
	 * @param       array   The associated data for the form.
	 * @return      boolean
	 * @since       1.6
	 */
	function onContentPrepareForm($form, $data) {
		if ($form->getName() == 'com_menus.item') {
			JForm::addFormPath(JPATH_SITE . '/plugins/system/accordeonckparams/params');
			JForm::addFieldPath(JPATH_SITE . '/modules/mod_accordeonck/elements');

			// get the language
			$lang = JFactory::getLanguage();
			$langtag = $lang->getTag(); // returns fr-FR or en-GB
			$this->loadLanguage();

			// load the xml file
			$form->loadFile('params_accordeonckparams', false);
		}
	}
}
