<?php
/**
 * Element: Languages
 * Displays a select box of languages
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

class JFormFieldNN_Languages extends NNFormField
{
	public $type = 'Languages';

	protected function getInput()
	{
		$this->params = $this->element->attributes();

		$size = (int) $this->get('size');
		$multiple = $this->get('multiple');
		$client = $this->get('client', 'SITE');

		jimport('joomla.language.helper');
		$langs = JLanguageHelper::createLanguageList($this->value, constant('JPATH_' . strtoupper($client)), true);
		$options = array();

		foreach ($langs as $lang)
		{
			if (!$lang['value'])
			{
				continue;
			}

			$option = new stdClass;
			$option->value = $lang['value'];
			$option->text = $lang['text'] . ' [' . $lang['value'] . ']';
			$options[] = $option;
		}

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/html.php';

		return NNHtml::selectlistsimple($options, $this->name, $this->value, $this->id, $size, $multiple);
	}
}
