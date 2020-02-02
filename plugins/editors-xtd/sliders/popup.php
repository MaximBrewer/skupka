<?php
/**
 * @package         Sliders
 * @version         6.0.8
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2016 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

if (JFactory::getUser()->get('guest'))
{
	JError::raiseError(403, JText::_("ALERTNOTAUTH"));
}

require_once JPATH_LIBRARIES . '/regularlabs/helpers/parameters.php';
$parameters = RLParameters::getInstance();
$params     = $parameters->getPluginParams('sliders');

if (JFactory::getApplication()->isSite() && !$params->enable_frontend)
{
	JError::raiseError(403, JText::_("ALERTNOTAUTH"));
}

$class = new PlgButtonSlidersPopup($params);
$class->render();

class PlgButtonSlidersPopup
{
	var $params = null;

	function __construct(&$params)
	{
		$this->params = $params;
	}

	function render()
	{
		require_once JPATH_LIBRARIES . '/regularlabs/helpers/functions.php';

		jimport('joomla.filesystem.file');

		// Load plugin language
		RLFunctions::loadLanguage('plg_system_regularlabs');
		RLFunctions::loadLanguage('plg_editors-xtd_sliders');
		RLFunctions::loadLanguage('plg_system_sliders');

		RLFunctions::script('regularlabs/script.min.js');
		RLFunctions::stylesheet('regularlabs/popup.min.css');
		RLFunctions::stylesheet('regularlabs/style.min.css');

		// Tag character start and end
		list($tag_start, $tag_end) = explode('.', $this->params->tag_characters);

		$script = "
			var sliders_tag_open = '" . preg_replace('#[^a-z0-9-_]#s', '', $this->params->tag_open) . "';
			var sliders_tag_close = '" . preg_replace('#[^a-z0-9-_]#s', '', $this->params->tag_close) . "';
			var sliders_tag_delimiter = '" . (($this->params->tag_delimiter == '=') ? '=' : ' ') . "';
			var sliders_tag_characters = ['" . $tag_start . "', '" . $tag_end . "'];
			var sliders_editorname = '" . JFactory::getApplication()->input->getString('name', 'text') . "';
			var sliders_content_placeholder = '" . JText::_('SLD_TEXT', true) . "';
			var sliders_error_empty_title = '" . JText::_('SLD_ERROR_EMPTY_TITLE', true) . "';
			var sliders_max_count = " . (int) $this->params->button_max_count . ";
		";
		JFactory::getDocument()->addScriptDeclaration($script);

		RLFunctions::script('sliders/popup.min.js', '6.0.8');
		RLFunctions::stylesheet('sliders/popup.min.css', '6.0.8');

		echo $this->getHTML();
	}

	function getHTML()
	{
		ob_start();
		include __DIR__ . '/popup.tmpl.php';
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
