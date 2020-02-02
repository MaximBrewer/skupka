<?php
/**
 * Element: Load Language
 * Loads the English language file as fallback
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

class JFormFieldNN_LoadLanguage extends NNFormField
{
	public $type = 'LoadLanguage';

	protected function getLabel()
	{
		return '';
	}

	protected function getInput()
	{
		$this->params = $this->element->attributes();

		$extension = $this->get('extension');
		$admin = $this->get('admin', 1);

		self::loadLanguage($extension, $admin);

		return '';
	}

	function loadLanguage($extension, $admin = 1)
	{
		if (!$extension)
		{
			return;
		}

		NNFrameworkFunctions::loadLanguage($extension, $admin ? JPATH_ADMINISTRATOR : JPATH_SITE);
	}
}
