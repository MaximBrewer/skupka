<?php
/*-------------------------------------------------------------------------
# com_improved_ajax_login - com_improved_ajax_login
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2013 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
$revision = '2.4.166';
$revision = '2.4.166';
?><?php
// No direct access
defined('_JEXEC') or die;

if (!class_exists('JHtmlSidebar')) {
  class JHtmlSidebar extends JSubMenuHelper {
    function render() {}
    function setAction() {}
    function addFilter() {}
  }
}

/**
 * Improved_ajax_login helper.
 */
class Improved_ajax_loginHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($vName = '')
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_IMPROVED_AJAX_LOGIN_TITLE_MODULES'),
			'index.php?option=com_improved_ajax_login&view=modules',
			$vName == 'modules'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_IMPROVED_AJAX_LOGIN_TITLE_FORMS'),
			'index.php?option=com_improved_ajax_login&view=forms',
			$vName == 'forms'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_IMPROVED_AJAX_LOGIN_TITLE_OAUTHS'),
			'index.php?option=com_improved_ajax_login&view=oauths',
			$vName == 'oauths'
		);
		JHtmlSidebar::addEntry(
			JText::_('Export Users'),
			'index.php?option=com_improved_ajax_login&view=export',
			$vName == 'export'
		);

	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_improved_ajax_login';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}
