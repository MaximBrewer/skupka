<?php
/**
 * @copyright	Copyright (C) 2011 Cédric KEIFLIN alias ced1870
 * http://www.template-creator.com
 * http://www.joomlack.fr
 * @license		GNU/GPL
 * */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.event.plugin');

class plgSystemMaximenuckparams extends JPlugin {

	function __construct(&$subject, $params) {
		parent::__construct($subject, $params);
	}

	/**
	 * @param       JForm   The form to be altered.
	 * @param       array   The associated data for the form.
	 * @return      boolean
	 * @since       1.6
	 */
	function onContentPrepareForm($form, $data) {
		if (
				$form->getName() != 'com_menus.item' 
				&& $form->getName() != 'com_menumanagerck.itemedition'
				)
			return;

		JForm::addFormPath(JPATH_SITE . '/plugins/system/maximenuckparams/params');
		JForm::addFieldPath(JPATH_SITE . '/modules/mod_maximenuck/elements');

		// get the language
		$lang = JFactory::getLanguage();
		$langtag = $lang->getTag(); // returns fr-FR or en-GB
		$this->loadLanguage();

		// menu item options
		if ($form->getName() == 'com_menus.item' || $form->getName() == 'com_menumanagerck.itemedition') {
			$form->loadFile('advanced_itemparams_maximenuck', false);
		}
	}
	
	function check_version() {
		return '4';
	}
}