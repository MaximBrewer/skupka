<?php
/**
 * Element: IsInstalled
 * Displays a hidden field with a boolean value based on whether the given extension is installed
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

jimport('joomla.form.formfield');

require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/field.php';

class JFormFieldNN_IsInstalled extends NNFormField
{
	public $type = 'IsInstalled';

	protected function getLabel()
	{
		return '';
	}

	protected function getInput()
	{
		$this->params = $this->element->attributes();

		$is_installed = NNFrameworkFunctions::extensionInstalled($this->get('extension'), $this->get('extension_type'), $this->get('folder'));

		return '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '" value="' . (int) $is_installed . '" />';
	}
}
