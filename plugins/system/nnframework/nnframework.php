<?php
/**
 * @package         NoNumber Framework
 * @version         18.12.3953
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2018 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Plugin\CMSPlugin as JPlugin;

$app = JFactory::getApplication();

if ($app->isClient('administrator'))
{
	// load the NoNumber Framework language file
	require_once __DIR__ . '/helpers/functions.php';
	NNFrameworkFunctions::loadLanguage('plg_system_nnframework');
}

if (
	$app->isClient('administrator')
	&& JFactory::getUser()->id
	&& ! $app->input->get('tmpl')
	&& $app->input->get('task') != 'preview'
	&& ! (
		$app->input->get('option') == 'com_finder'
		&& $app->input->get('format') == 'json'
	)
)
{

	require_once __DIR__ . '/helpers/parameters.php';
	$parameters = NNParameters::getInstance();
	$params     = $parameters->getPluginParams('nnframework');

	if ($params->enable_not_used_message)
	{
		require_once __DIR__ . '/helpers/framework_needed.php';
		$still_installed = NNFrameworkNeeded::getOldInstalledExtensions();

		if (empty($still_installed))
		{
			// No extensions found that still needs the NoNumber framework
			JFactory::getApplication()->enqueueMessage(
				JText::sprintf(
					'NN_FRAMEWORK_NOT_USED_MESSAGE',
					'<a class="btn btn-danger" href="index.php?option=com_installer&view=manage&filter_search=NoNumber Framework">',
					'</a>'
				)
				. '<br><em>' . JText::sprintf(
					'NN_DISABLE_NOT_USED_MESSAGE',
					'<a href="index.php?option=com_plugins&filter_search=NoNumber Framework">',
					'</a>'
				) . '</em>',
				'warning'
			);
		}
	}
}

jimport('joomla.filesystem.file');

/**
 * Plugin that loads Framework
 */
class PlgSystemNNFramework extends JPlugin
{
	public function onAfterRoute()
	{
		if ( ! JFactory::getApplication()->input->getInt('nn_qp', 0))
		{
			return;
		}

		// Include the Helper
		require_once __DIR__ . '/helper.php';
		$helper = new PlgSystemNNFrameworkHelper;

		$helper->render();
	}
}

