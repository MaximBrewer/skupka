<?php

/**
 * @name		Maximenu CK params
 * @package		com_maximenuck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * About View
 */
class MaximenuckViewAbout extends JViewLegacy {

	/**
	 * About view display method
	 * @return void
	 * */
	function display($tpl = null) {
		JToolBarHelper::title(JText::_('COM_MAXIMENUCK') . ' - ' . JText::_('CK_ABOUT'), 'home_maximenuck');

		// get the current version of the component
		require_once JPATH_COMPONENT . '/helpers/maximenuckhelper.php';
		$this->component_version = MaximenuckHelper::get_current_version();
		
		// Load the left sidebar.
		MaximenuckHelper::addSubmenu(JRequest::getCmd('view', 'modules'));

		parent::display($tpl);
	}
}
