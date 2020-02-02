<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

defined ("VMEE_PRO_CLASSPATH") or define ("VMEE_PRO_CLASSPATH", JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR);

require_once JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'autoloader.php';
JLoader::register('vmeePlusHelper', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'vmeeplus.php');

$version = emp_helper::getVersion();
define ("VMEE_PRO_TITLE", "Interamind Emails Manager Plus (v".$version.")");
if(emp_license::checkLicense() != emp_license::SUCCESS){
	JError::raiseWarning('', JText::_('LICENSE_NOT_VALID'));
}
$controller = JRequest::getWord( 'controller', 'templateList' );
$path = JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$controller.'.php';
require_once $path;
 
$classname    = 'vmeeProController'.$controller;
$controller   = new $classname( );
 
$controller->execute( JFactory::getApplication()->input->get( 'task' ,null,'RAW') );
 
$controller->redirect();
?>