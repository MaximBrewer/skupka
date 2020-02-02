<?php
defined('_JEXEC') or die('Access Deny');

if (!JFactory::getUser()->authorise('core.manage', 'com_yandex_maps')) {
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 401);
}

// for Joomla 2.5
include_once 'helpers/joomla25/includer.php';

include_once "helpers/CModel.php";

JHtml::addIncludePath(JPATH_ROOT.'/components/com_yandex_maps/helpers');

jimport('joomla.application.component.controller');
include "helpers/CController.php";
JToolBarHelper::preferences('com_yandex_maps');
$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::root().'media/com_yandex_maps/css/yandex_maps.css');
$doc->addScriptDeclaration('window.juri_root = "'.JURI::root().'"');

$controller = JControllerLegacy::getInstance("Yandex_Maps");
if (!property_exists($controller, 'input')) {
	$controller->input = JFactory::getApplication()->input;
}

$controller->execute(JFactory::getApplication()->input->getCmd('task','display'));
$controller->redirect();