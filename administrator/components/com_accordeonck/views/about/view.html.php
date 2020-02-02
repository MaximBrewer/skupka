<?php

/**
 * @name		Accordeon Menu CK params
 * @package		com_accordeonck
 * @copyright	Copyright (C) 2016. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * About View
 */
class AccordeonckViewAbout extends JViewLegacy {

	/**
	 * About view display method
	 * @return void
	 * */
	function display($tpl = null) {
		JToolBarHelper::title(JText::_('COM_ACCORDEONCK') . ' - ' . JText::_('CK_ABOUT'), 'home_accordeonck');

		// get the current version of the component
		require_once JPATH_COMPONENT . '/helpers/accordeonckhelper.php';
		$this->component_version = AccordeonckHelper::get_current_version();
		
		// Load the left sidebar.
		AccordeonckHelper::addSubmenu(JRequest::getCmd('view', 'modules'));

		parent::display($tpl);
	}
}
