<?php
defined('_JEXEC') or die('Access Deny');

jimport('joomla.application.component.model');
include JPATH_COMPONENT_ADMINISTRATOR."/helpers/CModel.php";
JModelLegacy::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/models');
JHtml::addIncludePath(JPATH_ROOT . '/components/com_yandex_maps/helpers');

$doc = JFactory::getDocument();

jhtml::_('xdwork.frontjs');

JRequest::getCmd('task');
require_once "controller.php";
$controller = JControllerLegacy::getInstance("Yandex_Maps");

if (!property_exists($controller, 'input')) {
	$controller->input = JFactory::getApplication()->input; // Для J2.7 вроде
}

$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();