<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      sh404SEF
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      4.7.3.3292
 * @date        2016-03-15
 *
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
{
	die('Direct Access to this location is not allowed.');
}

// sometimes users disable our plugin
if (!defined('SH404SEF_AUTOLOADER_LOADED'))
{
	echo 'sh404SEF system plugin has been disabled or has failed initializing. Please enable it again to use sh404SEF, with Joomla! <a href="index.php?option=com_plugins">plugin manager</a>';
	return;
}

// Access check.
if (!Sh404sefHelperAcl::userCan('core.manage'))
{
	return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
}

// find about specific controller requested
$app = JFactory::getApplication();
$cName = $app->input->getCmd('c');

// per view access check
$authViewName = empty($cName) ? $app->input->getCmd('view') : $cName;
if (!empty($authViewName))
{
	$action = 'sh404sef.view.' . strtolower($authViewName);
	if (!Sh404sefHelperAcl::userCan($action))
	{
		return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
	}
}

// load edition-specific language file
JFactory::getLanguage()->load('com_sh404sef.' . Sh404sefConfigurationEdition::$id, JPATH_ADMINISTRATOR);

// load base class file (functions, not autoloaded
if (!defined('SH404SEF_BASE_CLASS_LOADED'))
{
	$baseClassFile = JPATH_ADMINISTRATOR . '/components/com_sh404sef/sh404sef.class.php';
	if (is_readable($baseClassFile))
	{
		require_once($baseClassFile);
	}
	else
	{
		JError::RaiseError(500, JText::_('COM_SH404SEF_NOREAD') . "( $baseClassFile )<br />" . JText::_('COM_SH404SEF_CHK_PERMS'));
	}
}

// languagefilter system plugin warning (for Joomla! 2)
if (version_compare(JVERSION, '3', '<'))
{
	$shouldWarn = Sh404sefHelperLanguage::getLanguageFilterWarning();
	if (!empty($shouldWarn))
	{
		$app->enqueueMessage(JText::_('COM_SH404SEF_LANGUAGEFILTER_PLUGIN_WARNING'), 'warning');
	}
}
else
{
	if ($app->input->get('tmpl') != 'component')
	{
		Sh404sefHelperMsg::updateSystemMessages();
	}
}

// Ensure the behavior is loaded
JHtml::_('behavior.framework');
if (version_compare(JVERSION, '3.0', 'ge'))
{
	JHtml::_('bootstrap.framework');
}

// get controller from factory
$controller = Sh404sefFactory::getController($cName);
Sh404sefHelperHtml::addSubmenu($app->input);
// read and execute task
$controller->execute($app->input->getCmd('task'));
$controller->redirect();
