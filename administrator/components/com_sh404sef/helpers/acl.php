<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date        2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
	die('Direct Access to this location is not allowed.');

class Sh404sefHelperAcl
{
	public static $actionNames = array(
		'core.manage',
		'sh404sef.view.configuration',
		'sh404sef.view.urls',
		'sh404sef.view.aliases',
		'sh404sef.view.pageids',
		'sh404sef.view.metas',
		'sh404sef.view.analytics'
	);

	private static $_actions = array();


	/**
	 * Creates and returns an array with all permissions for a given user
	 *
	 * @param null| int | Object $user the user, either its id or a user object. If null, current user is used
	 * @return array an array of booleans indexed on action names, true is user can perform the action
	 */
	public static function getActions($user = null)
	{
		if (is_null($user))
		{
			$user = JFactory::getUser();
			$userId = $user->id;
		}
		else if (is_int($user))
		{
			$userId = $user;
			$user = JFactory::getUser($userId);
		}
		else if (is_object($user) && isset($user->id))
		{
			$userId = $user->id;
		}
		else
		{
			return array();
		}

		// if user not seen before, load its action data
		if (is_null(self::$_actions) || empty(self::$_actions[$userId]))
		{
			foreach (self::$actionNames as $actionName)
			{
				self::$_actions[$userId][$actionName] = (bool) $user->authorise($actionName, 'com_sh404sef');
			}
		}

		return self::$_actions[$userId];
	}

	/**
	 * Compute whether a user can perform a given action based on set ACL
	 *
	 * @param string $actionName
	 * @param @param null| int | Object $user the user, either its id or a user object. If null, current user is used
	 * @return bool
	 */
	public static function userCan($actionName, $user = null)
	{
		$auth = self::getActions($user);
		if (!isset($auth[$actionName]))
		{
			return true;
		}

		return $auth[$actionName] === true;
	}
}
