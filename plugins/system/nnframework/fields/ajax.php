<?php
/**
 * Element: Ajax
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

require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/field.php';

class JFormFieldNN_Ajax extends NNFormField
{
	public $type = 'Ajax';

	protected function getInput()
	{
		$this->params = $this->element->attributes();

		JHtml::_('jquery.framework');

		NNFrameworkFunctions::addScriptVersion(JUri::root(true) . '/media/nnframework/js/script.min.js');

		$loading = "jQuery(\"#" . $this->id . "\").find(\"span\").attr(\"class\", \"icon-refresh icon-spin\")";
		$success = "jQuery(\"#" . $this->id . "\").find(\"span\").attr(\"class\", \"icon-ok\")";
		$error = "jQuery(\"#" . $this->id . "\").find(\"span\").attr(\"class\", \"icon-warning\");"
			. "jQuery(\"#message_" . $this->id . "\").addClass(\"alert alert-danger alert-inline\").html(data);";
		$script = "
			function loadAjax" . $this->id . "() {
				" . $loading . ";
				jQuery(\"#message_" . $this->id . "\").attr(\"class\", \"\").html(\"\");
				nnScripts.loadajax(
					'" . addslashes($this->get('url')) . "',
					'if(data == \"\") { " . $success . " } else { " . $error . " }',
					'" . $error . "'
				);
			}
		";
		JFactory::getDocument()->addScriptDeclaration($script);

		return
			'<button id="' . $this->id . '" class="btn" title="' . JText::_($this->get('description')) . '" onclick="loadAjax' . $this->id . '();return false;">'
			. '<span class="' . $this->get('icon', '') . '"></span> '
			. JText::_($this->get('text', $this->get('label')))
			. '</button>'
			. '<div id="message_' . $this->id . '"></div>';
	}
}
