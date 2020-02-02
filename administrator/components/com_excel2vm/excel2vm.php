<?php defined( "_JEXEC" ) or die( "Restricted access" );
if(!defined("DS")){
    define("DS",DIRECTORY_SEPARATOR);
}
ini_set('short_open_tag','On');
if (!JFactory::getUser()->authorise("core.manage", "com_excel2vm"))
{
	return JError::raiseWarning(404, JText::_("JERROR_ALERTNOAUTHOR"));
}

$controller	= JControllerLegacy::getInstance("Excel2vm");
$controller->execute(JFactory::getApplication()->input->get("task"));
$controller->redirect();