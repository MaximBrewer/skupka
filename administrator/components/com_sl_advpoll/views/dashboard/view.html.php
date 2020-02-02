<?php
/**
 * @version		$Id$
 * @author		Pham Minh Tuan (admin@extstore.com) (admin@extstore.com)
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

/**
 * Dashboard view.
 *
 */
class SL_AdvPollViewDashboard extends JViewLegacy {
	/**
	 * Display the view.
	 */
	public function display($tpl = null) {
		$canDo	= SL_AdvPollHelper::getActions();
		SL_AdvPollHelper::addSubmenu('dashboard');
		$this->sidebar = JHtmlSidebar::render();
		//$this->addToolbar();

		JToolBarHelper::title(JText::_('COM_SL_ADVPOLL_DASHBOARD_MANAGER'), 'dashboard.png');

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_sl_advpoll');
		}

		//
		$module		= JModelLegacy::getInstance('Polls', 'SL_AdvPollModel', array('ignore_request' => 1));
		$module->setState('list.start', 0);
		$module->setState('list.limit', 5);
		$module->setState('list.direction', 'DESC');

		$module->setState('list.ordering', 'a.created');
		$this->latest_items		= $module->getItems();
		$module->setState('list.ordering', 'date_voted');
		$this->last_voted_items	= $module->getItems();
		$module->setState('list.ordering', 'total_votes');
		$this->top_voted_items	= $module->getItems();

		parent::display($tpl);
	}

	/**
	 * Display quick icon button.
	 * 
	 * @param	string	$link
	 * @param	string	$image
	 * @param	string	$text
	 */
	protected function _quickIcon($link, $image, $text, $modal = false) {
		$button	= array(
			'link'	=> JRoute::_($link),
			'image'	=> 'com_sl_advpoll/' . $image,
			'text'	=> JText::_($text),
		);

		$this->button	= $button;
		echo $this->loadTemplate('button');
	}
}