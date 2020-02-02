<?php
/**
 * NoNumber Framework Helper File: Assignments: Form2Content
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

class NNFrameworkAssignmentsForm2Content extends NNFrameworkAssignment
{
	function passProjects()
	{
		if ($this->request->option != 'com_content' && $this->request->view == 'article')
		{
			return $this->pass(false);
		}

		$query = $this->db->getQuery(true)
			->select('c.projectid')
			->from('#__f2c_form AS c')
			->where('c.reference_id = ' . (int) $this->request->id);
		$this->db->setQuery($query);
		$type = $this->db->loadResult();

		$types = $this->makeArray($type, 1);

		return $this->passSimple($types);
	}
}
