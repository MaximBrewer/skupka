<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

/**
 * View to edit a Poll.
 */
class SL_AdvPollViewPoll extends JViewLegacy {

	protected $state;
	protected $item;
	protected $form;

	/**
	 * Display the view.
	 */
	public function display($tpl = null) {
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar() {
		JRequest::setVar('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= $this->item->id == 0;
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= SL_AdvPollHelper::getActions($this->state->get('filter.category_id'), $this->item->id);

		JToolBarHelper::title(JText::_('COM_SL_ADVPOLL_POLL_MANAGER'), 'poll.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || (count($user->getAuthorisedCategories('com_sl_advpoll', 'core.create'))))) {
			JToolBarHelper::apply('poll.apply');
			JToolBarHelper::save('poll.save');
		}

		if (!$checkedOut && (count($user->getAuthorisedCategories('com_sl_advpoll', 'core.create')))) {
			JToolBarHelper::save2new('poll.save2new');
		}

		// If an existing item, can save to a copy.
		if (!$isNew && (count($user->getAuthorisedCategories('com_sl_advpoll', 'core.create')))) {
			JToolBarHelper::save2copy('poll.save2copy');
		}

		if (empty($this->item->id)) {
			JToolBarHelper::cancel('poll.cancel');
		} else {
			JToolBarHelper::cancel('poll.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}