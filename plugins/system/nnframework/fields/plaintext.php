<?php
/**
 * Element: PlainText
 * Displays plain text as element
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

class JFormFieldNN_PlainText extends NNFormField
{
	public $type = 'PlainText';

	protected function getLabel()
	{
		JHtml::stylesheet('nnframework/style.min.css', false, true);

		$this->params = $this->element->attributes();

		$label = $this->prepareText($this->get('label'));
		$tooltip = $this->prepareText($this->get('description'));

		if (!$label && !$tooltip)
		{
			return '';
		}

		if (!$label)
		{
			return '<div>' . $tooltip . '</div>';
		}

		if (!$tooltip)
		{
			return '<div>' . $label . '</div>';
		}

		return '<label class="hasTooltip" title="<strong>' . $label . '</strong><br />' . htmlentities($tooltip) . '">'
		. $label . '</label>';
	}

	protected function getInput()
	{
		$this->params = $this->element->attributes();

		$text = $this->prepareText($this->value);

		if (!$text)
		{
			return '';
		}

		return '<fieldset class="nn_plaintext">' . $text . '</fieldset>';
	}
}
