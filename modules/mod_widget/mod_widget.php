<?php 
/*------------------------------------------------------------------------
# mod_widget - vData Widget
# ------------------------------------------------------------------------
# author    Team WDM Tech
# copyright Copyright (C) 2013 wwww.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech..com
# Technical Support:  Forum - http://www.wdmtech..com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}

require_once (dirname(__FILE__).DS.'helper.php');
	$uniqueid = $module->id;
	$user = JFactory::getUser();
	
	$widget = $params->get('widget'); 
	$session = JFactory::getSession();
	$uniqueid = $module->id;
	$session->set('widget_post'.$uniqueid, JRequest::get('post'));
	$session->set('widget_get'.$uniqueid, JRequest::get('get'));
	$session->set('module_id', $uniqueid);
	
	$widget = $params->get('widget');
	$widget_ordering = $params->get('widget_ordering');
	$widget_ordering_by = $params->get('widget_ordering_by');
	$lang = JFactory::getLanguage();
	$lang->load('com_vdata', JPATH_SITE);
	$document = JFactory::getDocument();
       //$show_for		= $params->get('show_for');
	   /* if(!empty($show_for))
	   {
		   $user  = JFactory::getUser();
			if($user->id==0){
				$msg = JText::_('VDATA_LOGIN_ALERT');
				$mainframe->redirect(JRoute::_('index.php?option=com_users&view=login'), $msg);
			}   
	    } */
	   
	$document->addStyleSheet(JUri::root().'/media/com_vdata/css/sitestyles.css');
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

    $widgets = modWidgetHelper::getWidgets($params);
	$profiles = modWidgetHelper::getProfiles();// $this->items
	$configuration = modWidgetHelper::getConfiguration();// $this->items
	
	$plugins = modWidgetHelper::getConfiguration();
	$lang = JFactory::getLanguage();

	$extension = 'com_vdata';

	$base_dir = JPATH_SITE;

	$language_tag = 'en-GB';

	$reload = true;

	$lang->load($extension, $base_dir.'/administrator/components/com_vdata');
// print_r($item);
require(JModuleHelper::getLayoutPath('mod_widget'));
