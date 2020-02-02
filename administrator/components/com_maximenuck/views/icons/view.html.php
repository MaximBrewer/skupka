<?php
/**
 * @name		Maximenu CK params
 * @package		com_maximenuck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */
 
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class MaximenuckViewIcons extends JViewLegacy {

	/**
	 * About view display method
	 * @return void
	 * */
	function display($tpl = null) {
		$input = JFactory::getApplication()->input;

		$user = JFactory::getUser();
		$authorised = ($user->authorise('core.create', 'com_maximenuck') || (count($user->getAuthorisedCategories('com_maximenuck', 'core.create'))));

		if ($authorised !== true)
		{
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		parent::display($tpl);

		exit();
	}
}
