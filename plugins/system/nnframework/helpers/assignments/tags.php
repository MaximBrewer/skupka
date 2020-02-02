<?php
/**
 * NoNumber Framework Helper File: Assignments: Tags
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

class NNFrameworkAssignmentsTags extends NNFrameworkAssignment
{
	function passTags()
	{
		$is_content = in_array($this->request->option, array('com_content', 'com_flexicontent'));

		if (!$is_content)
		{
			return $this->pass(false);
		}

		$is_item = in_array($this->request->view, array('', 'article', 'item'));
		$is_category = in_array($this->request->view, array('category'));

		if ($is_item)
		{
			$prefix = 'com_content.article';
		}
		else if ($is_category)
		{
			$prefix = 'com_content.category';
		}
		else
		{
			return $this->pass(false);
		}

		// Load the tags.
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('t.id'))
			->from('#__tags AS t')
			->join(
				'INNER', '#__contentitem_tag_map AS m'
				. ' ON m.tag_id = t.id'
				. ' AND m.type_alias = ' . $this->db->quote($prefix)
				. ' AND m.content_item_id IN ( ' . $this->request->id . ')'
			);
		$this->db->setQuery($query);
		$tags = $this->db->loadColumn();

		if (empty($tags))
		{
			return $this->pass(false);
		}

		foreach ($tags as $tag)
		{
			if (!$this->passTag($tag))
			{
				continue;
			}

			return $this->pass(true);
		}

		return $this->pass(false);
	}

	private function passTag($tag)
	{
		$pass = in_array($tag, $this->selection);

		if ($pass)
		{
			// If passed, return false if assigned to only children
			// Else return true
			return ($this->params->inc_children != 2);
		}

		if (!$this->params->inc_children)
		{
			return false;
		}

		// Return true if a parent id is present in the selection
		return array_intersect(
			$this->getTagsParentIds($tag),
			$this->selection
		);
	}

	private function getTagsParentIds($id = 0)
	{
		$parentids = $this->getParentIds($id, 'tags');
		// Remove the root tag
		$parentids = array_diff($parentids, array('1'));

		return $parentids;
	}
}
