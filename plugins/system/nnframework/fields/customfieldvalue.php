<?php
/**
 * Element: Custom Field Value
 * Displays a custom key field (use in combination with customfieldkey)
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

class JFormFieldNN_CustomFieldValue extends NNFormField
{
	public $type = 'CustomFieldValue';

	protected function getLabel()
	{
		return '';
	}

	protected function getInput()
	{
		$this->params = $this->element->attributes();

		$label = $this->get('label') ? $this->get('label') : '';
		$size = $this->get('size') ? 'style="width:' . $this->get('size') . 'px"' : '';
		$class = 'class="' . ($this->get('class') ? $this->get('class') : 'text_area') . '"';
		$this->value = htmlspecialchars(html_entity_decode($this->value, ENT_QUOTES), ENT_QUOTES);

		return
			'</div></div></div>'
			. '<input type="text" name="' . $this->name . '" id="' . $this->id . '" value="' . $this->value
			. '" placeholder="' . JText::_($label) . '" title="' . JText::_($label) . '" ' . $class . ' ' . $size . ' />';
	}
}
