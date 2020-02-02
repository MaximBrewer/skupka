<?php
/**
 * NoNumber Framework Helper File: Assignments: Users
 *
 * @package         NoNumber Framework
 * @version         15.9.5525
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once JPATH_PLUGINS . '/system/nnframework/helpers/assignment.php';

class NNFrameworkAssignmentsUsers extends NNFrameworkAssignment
{
	function passUserGroupLevels()
	{
		$user = JFactory::getUser();

		if (!empty($user->groups))
		{
			$groups = array_values($user->groups);
		}
		else
		{
			$groups = $user->getAuthorisedGroups();
		}

		return $this->passSimple($groups);
	}

	function passUsers()
	{
		return $this->passSimple(JFactory::getUser()->get('id'));
	}
}
