<?php
/**
 * Element: License
 * Displays the License state
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

class JFormFieldNN_License extends NNFormField
{
	public $type = 'License';

	protected function getLabel()
	{
		return '';
	}

	protected function getInput()
	{
		$this->params = $this->element->attributes();

		$extension = $this->get('extension');

		if (!strlen($extension))
		{
			return '';
		}

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/licenses.php';

		return '</div><div class="hide">' . NoNumberLicenses::render($extension, true);
	}
}
