<?php
/**
 * @package         NoNumber Framework
 * @version         20.1.23725
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2020 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text as JText;

require_once dirname(__DIR__) . '/helpers/field.php';

class JFormFieldNN_Framework_Needed extends NNFormField
{
	public $type = 'Framework_Needed';

	protected function getLabel()
	{
		return '';
	}

	protected function getInput()
	{
		require_once dirname(__DIR__) . '/helpers/framework_needed.php';

		$still_installed = NNFrameworkNeeded::getOldInstalledExtensions();

		if ( ! empty($still_installed))
		{
			// An extension (version) is installed that still needs the NoNumber framework
			return
				'</div><div class="alert alert-warning">'
				. JText::_('NN_FRAMEWORK_STILL_USED_MESSAGE')
				. '<br><br>'
				. JText::sprintf(
					'NN_FRAMEWORK_STILL_INSTALLED',
					'<ul><li>' . implode('</li><li>', $still_installed) . '</li></ul>'
				);
		}

		// No extensions found that still needs the NoNumber framework
		return
			'</div><div class="alert alert-warning">'
			. JText::sprintf(
				'NN_FRAMEWORK_NOT_USED_MESSAGE',
				'<a class="btn btn-danger" href="index.php?option=com_installer&view=manage&filter_search=NoNumber Framework">',
				'</a>'
			);
	}
}
