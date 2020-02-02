<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

/**
 * View class for a list of Polls.
 * @package		Joomla.Administrator
 * @subpackage	Skyline.AdvPoll
 */
class SL_AdvPollViewPolls extends JViewLegacy {
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view.
	 */
	public function display($tpl = null) {
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		SL_AdvPollHelper::addSubmenu('polls');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar() {
		require_once(JPATH_COMPONENT . '/helpers/sl_advpoll.php');

		$state	= $this->get('State');
		$canDo	= SL_AdvPollHelper::getActions($state->get('filter.category_id'));
		$user	= JFactory::getUser();
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		JToolBarHelper::title(JText::_('COM_SL_ADVPOLL_POLLS_MANAGER'), 'polls.png');
		if (count($user->getAuthorisedCategories('com_sl_advpoll', 'core.create'))) {
			JToolBarHelper::addNew('poll.add');
		}

		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('poll.edit');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::publish('polls.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('polls.unpublish', 'JTOOLBAR_UNPUBLISH', true);

			JToolBarHelper::divider();
			JToolBarHelper::archiveList('polls.archive');
			JToolBarHelper::checkin('polls.checkin');
		}

		if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'polls.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		} else if ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('polls.trash');
		}

		// Add a batch button
		if ($canDo->get('core.edit')) {
			JHtml::_('bootstrap.modal', 'collapseModal');
			$title = JText::_('JTOOLBAR_BATCH');
			$dhtml = "<button data-toggle=\"modal\" data-target=\"#collapseModal\" class=\"btn btn-small\">
						<i class=\"icon-checkbox-partial\" title=\"$title\"></i>
						$title</button>";
			$bar->appendButton('Custom', $dhtml, 'batch');
		}

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_sl_advpoll');
		}
		JToolbarHelper::help('JHELP_COMPONENTS_SL_ADVPOLL_LINKS');

		JHtmlSidebar::setAction('index.php?option=com_sl_advpoll&view=polls');

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_state',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true)
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_CATEGORY'),
			'filter_category_id',
			JHtml::_('select.options', JHtml::_('category.options', 'com_sl_advpoll'), 'value', 'text', $this->state->get('filter.category_id'))
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_ACCESS'),
			'filter_access',
			JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
		);
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields(){
		return array(
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.state' => JText::_('JSTATUS'),
			'a.title' => JText::_('JGLOBAL_TITLE'),
			'a.access' => JText::_('JGRID_HEADING_ACCESS'),
			'a.language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}