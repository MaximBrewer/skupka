<?php
/*------------------------------------
* -Netbase- Advanced Virtuemart Invoices for Virtuemart
* Author    CMSMart Team
* Copyright (C) 2012 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support:  Forum - http://bloorum.com/forums
-----------------------------------------------------*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restrict Access');
if(!defined('DS')){
   define('DS',DIRECTORY_SEPARATOR);
}
if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
VmConfig::loadConfig();

// Load the view framework
require_once (JPATH_ADMINISTRATOR.DS."components".DS."com_virtuemart".DS."version.php");
$vmver = new vmVersion();
$matches = vmVersion::$RELEASE;
define('VM_RELEASE', $matches);


if(VmConfig::get('enableEnglish', 1)){
    $jlang = JFactory::getLanguage();
    $jlang->load('com_virtuemart', JPATH_ADMINISTRATOR, 'en-GB', true);
    $jlang->load('com_virtuemart', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
    $jlang->load('com_virtuemart', JPATH_ADMINISTRATOR, null, true);
}
vmJsApi::jQuery();
vmJsApi::jSite();
// Load the CSS
$document = JFactory::getDocument();

$document->addStyleSheet(JURI::root() . '/administrator/components/com_netbasevm_extend/assets/css/general.css');



// Initialize helper, classes, languages and temp. folder
require_once (JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_netbasevm_extend' . DS . 'helpers' . DS . 'nbordershelper.php');


//load controller
if (($controller = JRequest::getWord('controller'))) {
    $controllerFile = JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . $controller . '.php';
    if (file_exists($controllerFile)) {
        require_once ($controllerFile);
    } else {
        require_once (JPATH_COMPONENT_ADMINISTRATOR . DS . 'controller.php');
        $controller = '';
    }
} else {
    require_once (JPATH_COMPONENT_ADMINISTRATOR . DS . 'controller.php');
}

$classname = 'NetBaseVm_ExtendController' . ucfirst($controller);

$task = JRequest::getWord('task');

$controller = new $classname();
$controller->execute($task);
$controller->redirect();
