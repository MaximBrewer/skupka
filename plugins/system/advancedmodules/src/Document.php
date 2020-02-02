<?php
/**
 * @package         Advanced Module Manager
 * @version         7.5.1PRO
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2018 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace RegularLabs\Plugin\System\AdvancedModules;

defined('_JEXEC') or die;

use JFactory;
use JHtml;
use JRoute;
use JText;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Language as RL_Language;
use RegularLabs\Library\RegEx as RL_RegEx;

class Document
{
	public static function loadFrontEditScript()
	{
		if ( ! JFactory::getUser()->authorise('core.edit', 'com_menus')
			|| ! JFactory::getApplication()->get('frontediting', 1) == 2
		)
		{
			return;
		}

		JHtml::_('jquery.framework');

		RL_Document::script('advancedmodules/frontediting.min.js', '7.5.1.p');
	}

	/*
	 * Replace links to com_modules with com_advancedmodules
	 */
	public static function replaceLinks()
	{
		if (RL_Document::isClient('administrator') && JFactory::getApplication()->input->get('option') == 'com_modules')
		{
			self::replaceLinksInCoreModuleManager();

			return;
		}

		$body = JFactory::getApplication()->getBody();

		if (RL_Document::isClient('site'))
		{
			self::replaceLinksInFrontend($body);
		}

		self::replaceLinksModules($body);

		JFactory::getApplication()->setBody($body);
	}

	private static function replaceLinksModules(&$string)
	{
		if (strpos($string, 'com_modules') === false)
		{
			return;
		}

		$string = RL_RegEx::replace(
			'((["\'])[^\s"\'%]*\?option=com_)(modules[^a-z-_ "\']*?\2)',
			'\1advanced\3',
			$string
		);

		$string = str_replace(
			[
				'?option=com_advancedmodules&force=1',
				'?option=com_advancedmodules&amp;force=1',
			],
			'?option=com_modules',
			$string
		);
	}

	private static function replaceLinksInFrontend(&$string)
	{
		if (strpos($string, 'jmodediturl=') === false)
		{
			return;
		}

		$params = Params::get();

		$url = 'index.php?option=com_advancedmodules&view=edit&task=edit';

		if (JFactory::getUser()->authorise('core.manage', 'com_modules') && $params->use_admin_from_frontend)
		{
			$url = 'administrator/index.php?option=com_advancedmodules&task=module.edit';
		}

		$frontend_urls = [
			'index.php?option=com_config&controller=config.display.modules',
			'administrator/index.php?option=com_modules&view=module&layout=edit',
		];

		array_walk($frontend_urls, function (&$value) {
			$value = RL_RegEx::quote($value);
		});

		$string = RL_RegEx::replace(
			'(jmodediturl="[^"]*)(' . implode('|', $frontend_urls) . ')',
			'\1' . $url,
			$string
		);
	}

	private static function replaceLinksInCoreModuleManager()
	{
		RL_Language::load('com_advancedmodules');

		$body = JFactory::getApplication()->getBody();

		$url = 'index.php?option=com_advancedmodules';

		if (JFactory::getApplication()->input->get('view') == 'module')
		{
			$url .= '&task=module.edit&id=' . (int) JFactory::getApplication()->input->get('id');
		}

		$link = '<a style="float:right;" href="' . JRoute::_($url) . '">' . JText::_('AMM_SWITCH_TO_ADVANCED_MODULE_MANAGER') . '</a><div style="clear:both;"></div>';
		$body = RL_RegEx::replace('(</div>\s*</form>\s*(<\!--.*?-->\s*)*</div>)', $link . '\1', $body);

		JFactory::getApplication()->setBody($body);
	}
}
