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

class PlgSystemSlidersHelperHead
{
	var $helpers = array();
	var $params  = null;

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = PlgSystemSlidersHelpers::getInstance();
		$this->params  = $this->helpers->getParams();
	}

	public function addHeadStuff()
	{
		// do not load scripts/styles on feeds or print pages
		if (RLFunctions::isFeed() || JFactory::getApplication()->input->getInt('print', 0))
		{
			return;
		}

		require_once JPATH_LIBRARIES . '/regularlabs/helpers/functions.php';

		if ($this->params->load_bootstrap_framework)
		{
			JHtml::_('bootstrap.framework');
		}


		$script = '
			var rl_sliders_use_hash = ' . (int) $this->params->use_hash . ';
			var rl_sliders_reload_iframes = ' . (int) $this->params->reload_iframes . ';
			var rl_sliders_init_timeout = ' . (int) $this->params->init_timeout . ';
			';
		JFactory::getDocument()->addScriptDeclaration('/* START: Sliders scripts */ ' . preg_replace('#\n\s*#s', ' ', trim($script)) . ' /* END: Sliders scripts */');

		RLFunctions::script('sliders/script.min.js', ($this->params->media_versioning ? '6.0.8' : false));

		if ($this->params->load_stylesheet)
		{
			RLFunctions::stylesheet('sliders/style.min.css', ($this->params->media_versioning ? '6.0.8' : false));
		}

	}

	public function removeHeadStuff(&$html)
	{
		// Don't remove if sliders id is found
		if (strpos($html, 'id="set-rl_sliders') !== false)
		{
			return;
		}

		// remove style and script if no items are found
		$html = preg_replace('#\s*<' . 'link [^>]*href="[^"]*/(sliders/css|css/sliders)/[^"]*\.css[^"]*"[^>]*( /)?>#s', '', $html);
		$html = preg_replace('#\s*<' . 'script [^>]*src="[^"]*/(sliders/js|js/sliders)/[^"]*\.js[^"]*"[^>]*></script>#s', '', $html);
		$html = preg_replace('#((?:;\s*)?)(;?)/\* START: Sliders .*?/\* END: Sliders [a-z]* \*/\s*#s', '\1', $html);
	}
}
