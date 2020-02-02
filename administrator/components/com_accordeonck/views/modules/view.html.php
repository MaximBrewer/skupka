<?php

/**
 * @name		Accordeon Menu CK params
 * @package		com_accordeonck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
jimport('joomla.filesystem.folder');

/**
 * View class for a list of Accordeonck.
 */
class AccordeonckViewModules extends JViewLegacy {

	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		require_once JPATH_COMPONENT . '/helpers/accordeonckhelper.php';

		// Load the left sidebar.
		AccordeonckHelper::addSubmenu(JRequest::getCmd('view', 'modules'));

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar() {
		require_once JPATH_COMPONENT . '/helpers/accordeonckhelper.php';
		require_once JPATH_COMPONENT . '/helpers/html/modules.php';

		$state = $this->get('State');
//		var_dump($state);die;
		$canDo = AccordeonckHelper::getActions($state->get('filter.category_id'));

		JToolBarHelper::title(JText::_('COM_ACCORDEONCK') . ' - ' . JText::_('CK_MODULES_LIST'), 'logo_menumanagerck_large.png');

		if ($canDo->get('core.admin')) {
			// JToolBarHelper::preferences('com_accordeonck');
		}
	}
}
