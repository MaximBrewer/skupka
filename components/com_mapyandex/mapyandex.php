<?php
/*
 * @package Joomla 3.3
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */
 
defined('_JEXEC') or die('Restricted Access');
// get the controller
if(!defined('DS')){
define('DS',DIRECTORY_SEPARATOR);
}

require_once(JPATH_COMPONENT.DS.'controller.php');
// Require specific controller if requested
if($controller = JRequest::getWord('controller')) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}
if (! class_exists('MapYandexLoader')) {

    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mapyandex'.DS.'libraries'.DS.'loader.php');
}


mapyandeximport('mapyandex.wideimage.WideImage');

// Create the controller
$classname	= 'mapyandexController'.$controller;
$controller	= new $classname( );

// Perform the Request task
$controller->execute( JRequest::getVar( 'task' ) );

// Redirect if set by the controller
$controller->redirect();
