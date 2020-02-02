<?php
/**
 * Element: Group Level
 * Displays a select box of backend group levels
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

require_once JPATH_PLUGINS . '/system/nnframework/helpers/field.php';

class JFormFieldNN_GroupLevel extends NNFormField
{
	public $type = 'GroupLevel';

	protected function getInput()
	{
		$this->params = $this->element->attributes();

		$size = (int) $this->get('size');
		$multiple = $this->get('multiple');
		$show_all = $this->get('show_all');

		$options = $this->getUserGroups();

		if ($show_all)
		{
			$option = new stdClass;
			$option->value = -1;
			$option->text = '- ' . JText::_('JALL') . ' -';
			$option->disable = '';
			array_unshift($options, $option);
		}

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/html.php';

		return NNHtml::selectlist($options, $this->name, $this->value, $this->id, $size, $multiple);
	}

	protected function getUserGroups()
	{
		// Get the user groups from the database.
		$query = $this->db->getQuery(true)
			->select('a.id as value, a.title as text, a.parent_id AS parent')
			->from('#__usergroups AS a')
			->select('COUNT(DISTINCT b.id) AS level')
			->join('LEFT', '#__usergroups AS b ON a.lft > b.lft AND a.rgt < b.rgt')
			->group('a.id')
			->order('a.lft ASC');
		$this->db->setQuery($query);

		return $this->db->loadObjectList();
	}
}
