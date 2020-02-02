<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

/**
 * Skyline  Advanced Poll Helper.
 *
 * @package		Joomla.Administrator
 * @subpackage	Skyline.AdvPoll
 */
class SL_AdvPollHelper {
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 */
	public static function addSubmenu($vName = 'dashboard') {
		JHtmlSidebar::addEntry(
			JText::_('COM_SL_ADVPOLL_SUBMENU_DASHBOARD'),
			'index.php?option=com_sl_advpoll&view=dashboard',
			$vName == 'dashboard'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_SL_ADVPOLL_SUBMENU_POLLS'),
			'index.php?option=com_sl_advpoll&view=polls',
			$vName == 'polls'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_SL_ADVPOLL_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&extension=com_sl_advpoll',
			$vName == 'categories'
		);

		if ($vName == 'categories') {
			JToolBarHelper::title(
				JText::sprintf('COM_CATEGORIES_CATEGORIES_TITLE', JText::_('COM_SL_ADVPOLL')),
				'sl_advpoll-categories'
			);
		}
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	int		The category ID.
	 * @return	JObject
	 */
	public static function getActions($categoryId = 0) {
		$user	= JFactory::getUser();
		$result	= new JObject();

		if (empty($categoryId)) {
			$assetName	= 'com_sl_advpoll';
			$level = 'component';
		} else {
			$assetName	= 'com_sl_advpoll.category.' . (int) $categoryId;
			$level = 'category';
		}

		$actions = JAccess::getActions('com_sl_advpoll', $level);

		foreach ($actions as $action) {
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}

		return $result;
	}

}