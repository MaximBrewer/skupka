<?php
/**
 * Element: Tags
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

class JFormFieldNN_Tags extends NNFormField
{
	public $type = 'Tags';

	protected function getInput()
	{
		$this->params = $this->element->attributes();

		$size = (int) $this->get('size');

		// assemble items to the array
		$options = array();
		if ($this->get('show_ignore'))
		{
			if (in_array('-1', $this->value))
			{
				$this->value = array('-1');
			}
			$options[] = JHtml::_('select.option', '-1', '- ' . JText::_('NN_IGNORE') . ' -', 'value', 'text', 0);
			$options[] = JHtml::_('select.option', '-', '&nbsp;', 'value', 'text', 1);
		}

		$options += $this->getTags();

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/html.php';

		return NNHtml::selectlist($options, $this->name, $this->value, $this->id, $size, 1);
	}

	protected function getTags()
	{
		// Get the user groups from the database.
		$query = $this->db->getQuery(true)
			->select('a.id as value, a.title as text, a.parent_id AS parent')
			->from('#__tags AS a')
			->select('COUNT(DISTINCT b.id) - 1 AS level')
			->join('LEFT', '#__tags AS b ON a.lft > b.lft AND a.rgt < b.rgt')
			->where('a.alias <> ' . $this->db->quote('root'))
			->group('a.id')
			->order('a.lft ASC');
		$this->db->setQuery($query);
		$options = $this->db->loadObjectList();

		return $options;
	}
}
