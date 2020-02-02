<?php
/**
 * @package Unite Nivo Slider Pro for Joomla 1.7-2.5
 * @version 1.0.0
 * @author UniteJoomla.com
 * @copyright (C) 2012- Unite Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


require_once JPATH_ADMINISTRATOR."/components/com_nivosliderpro/includes.php";

//error_reporting(E_ALL); // debug

// Include dependancies
jimport('joomla.application.component.controller');

if (UniteFunctions::isJoomla3())
    $controller = JControllerLegacy::getInstance(GlobalsNivoPro::EXTENSION_NAME);
else
    $controller = JController::getInstance(GlobalsNivoPro::EXTENSION_NAME);

// Perform the Request task
$task = JRequest::getVar('task');

$controller->execute($task);
$controller->redirect();

?>