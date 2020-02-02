<?php
/**
 * @package    Favorites & Wishlist
 * @subpackage com_wishlist
 * @license  GNU/GPL v2
 * @Copyright (C) 2013 2KWeb Solutions. All rights reserved.
 * This program is distributed under the terms of the GNU General Public License
*/
if(!defined('DS')){
define('DS',DIRECTORY_SEPARATOR);
}

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Require the base controller
require_once( JPATH_COMPONENT.DS.'controller.php' );

$controllers = explode(',', 'favoriteslist,favoritesshlist,josuserslist,productlist,favoritesloglist');
if(!JRequest::getWord('controller')){
	JRequest::setVar( 'controller', $controllers[0] );
}
foreach($controllers as $controller){
	$link = JRoute::_("index.php?option=com_wishlist&controller={$controller}");
	$selected = ($controller == JRequest::getWord('controller'));
	JSubMenuHelper::addEntry(JText::_( 'MENU' . $controller ), "index.php?option=com_wishlist&controller={$controller}", ($controller == JRequest::getWord('controller')));
}
JRequest::setVar( 'view', JRequest::getWord('controller') );


// Require specific controller if requested; allways, in standard execution
if($controller = JRequest::getWord('controller')) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

// Create the controller
$classname	= 'FavoritesController'.$controller;
$controller	= new $classname( );

// Perform the Request task
$controller->execute( JRequest::getCmd( 'task' ) );

// Redirect if set by the controller
$controller->redirect();