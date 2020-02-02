<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/
// no direct access
defined('_JEXEC') or die('Restricted access');


if(version_compare(JVERSION,'3.0.0','ge')) 
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibilityj3.php'); 
else
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibilityj2.php'); 


$_SESSION['startmem'] = memory_get_usage(true);
//get active language
$lang = JFactory::getLanguage();
$active_lang = trim($lang->get('backwardlang'));

/*
//get  language file
if( file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'languages'.DIRECTORY_SEPARATOR.$active_lang.'.php')) {
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'languages'.DIRECTORY_SEPARATOR.$active_lang.'.php');
} 
else {
	require(dirname(__FILE__).DIRECTORY_SEPARATOR.'languages'.DIRECTORY_SEPARATOR.'english.php');
}
*/


$lang = JFactory::getLanguage();
$extension = 'com_onepage';
$lang->load($extension, JPATH_ADMINISTRATOR, 'en-GB');
$tag = $lang->getTag();
if (file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$tag.DIRECTORY_SEPARATOR.$tag.'.com_onepage.ini'))
$lang->load('com_onepage', JPATH_ADMINISTRATOR, $tag, true, true);

//get base controller
require_once (JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controllerBase.php');
//get query variables
 
$cmd = JRequest::getCmd('task', 'display');
$task = JRequest::getCmd('task', 'display');
$controller = JRequest::getCmd('view', 'config'); 
$controllerPath    = JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$controller.'.php';
 
if (file_exists($controllerPath)) {
        require_once($controllerPath);
} else {
        JError::raiseError(500, 'Invalid Controller');
}
 
$controllerClass = strtolower($controller).'Controller'.ucfirst($controller);
if (class_exists($controllerClass)) {
    $controller = new $controllerClass();
	
} else {
    JError::raiseError(500, 'Invalid Controller Class');
	echo 'Invalid controller class'; 
	$app = JFactory::getApplication(); 
	$app->close(); 
}



if (empty($task)) $task = 'display'; 
$controller->execute($task);

if($task != 'display')
{
	$controller->redirect();
}
