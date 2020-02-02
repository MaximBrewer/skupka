<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2016 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');

//import the assets css, js
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root(true).'/media/com_vdata/css/sitestyles.css');
$document->addStyleSheet(JUri::root(true).'/media/com_vdata/css/jquery.ui.theme.css');
$document->addStyleSheet(JUri::root(true).'/media/com_vdata/css/jquery.ui.datepicker.css');
$document->addStyleSheet(JUri::root(true).'/media/com_vdata/css/jquery-ui.css');
if (version_compare ( JVERSION, '3.0', 'ge' ))
	$document->addScript(JUri::root(true).'/media/jui/js/jquery.min.js');
if (version_compare ( JVERSION, '3.0', 'lt' )) 
	 $document->addScript(JUri::root(true).'/media/com_vdata/js/jquery.js');
 
$document->addScript(JUri::root(true).'/media/com_vdata/js/noconflict.js');
$document->addScript(JUri::root(true).'/media/com_vdata/js/jquery-ui.js');
$document->addStyleSheet(JUri::root(true).'/media/com_vdata/css/chosen.css');
$document->addScript(JUri::root(true).'/media/com_vdata/js/chosen.jquery.min.js');
$js = '$hd(function() {$hd("#vdatapanel").prepend("<div class=\"loading\"><div class=\"loading-icon\"><div></div></div></div>"); });';
$document->addScriptDeclaration($js);
$controller = JFactory::getApplication()->input->getWord('view', 'vdata');
// Require the base controller
require_once( JPATH_SITE.'/components/com_vdata/controller.php' );
// Require specific controller if requested
if($controller) {
    $path = JPATH_SITE.'/components/com_vdata/controllers/'.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}
// Create the controller
$classname    = 'VdataController'.$controller;
$controller   = new $classname( );
// Perform the Request task
$controller->execute( JFactory::getApplication()->input->get( 'task' ) );
// Redirect if set by the controller
$controller->redirect();