<?php
/**
 * Element: MultiSelect
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

class JFormFieldNN_MultiSelect extends NNFormField
{
	public $type = 'MultiSelect';

	protected function getInput()
	{
		$this->params = $this->element->attributes();

		if (!is_array($this->value))
		{
			$this->value = explode(',', $this->value);
		}

		foreach ($this->element->children() as $item)
		{
			$item_value = (string) $item['value'];
			$item_name = JText::_(trim((string) $item));
			$item_disabled = (int) $item['disabled'];
			$options[] = JHtml::_('select.option', $item_value, $item_name, 'value', 'text', $item_disabled);
		}

		$size = (int) $this->get('size');

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/html.php';

		return NNHtml::selectlist($options, $this->name, $this->value, $this->id, $size, 1);
	}
}
