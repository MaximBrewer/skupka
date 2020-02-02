<?php
/**
 * NoNumber Framework Helper File: Assignments: Languages
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

class NNFrameworkAssignmentsLanguages extends NNFrameworkAssignment
{
	function passLanguages()
	{
		return $this->passSimple(JFactory::getLanguage()->getTag(), true);
	}
}
