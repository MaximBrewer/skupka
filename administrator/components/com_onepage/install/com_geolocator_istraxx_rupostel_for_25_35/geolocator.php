<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* GeoLocator is free software released under GNU/GPL  This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* This php file was created by www.rupostel.com team
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
//get base controller

require_once (JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controllerBase.php');
//get query variables

if (file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'))
{
if (!class_exists( 'VmConfig' )) require(JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
VmConfig::loadConfig();
}

$memory_limit = (int) substr(ini_get('memory_limit'),0,-1);
// empty memory limit means no memory limit
if (!empty($memory_limit))
if($memory_limit<128){
	@ini_set( 'memory_limit', '128M' );
}

$max_execution_time = ini_get('max_execution_time');
if($max_execution_time<120){
	@ini_set( 'max_execution_time', '180' );
}

$task = JRequest::getCmd('task', 'display');

$controller=JRequest::getcmd('view', 'default');

$controllerPath    = JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$controller.'.php';
 
if (file_exists($controllerPath)) {
        require_once($controllerPath);
} else {
        JError::raiseError(500, 'Invalid Controller');
}
 
$controllerClass = 'JController'.ucfirst($controller);
if (class_exists($controllerClass)) {
    $controller = new $controllerClass();
} else {
    JError::raiseError(500, 'Invalid Controller Class');
}

$controller->execute($task);

if (class_exists('VmConfig'))
{
if($task != 'display')
{
	vmRam('End');
	vmRamPeak('Peak');
	$controller->redirect();
}
vmRam('End');
vmRamPeak('Peak');
}


