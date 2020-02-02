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
error_reporting(0);
//The response should be utf8 encoded
header('Content-Type: text/html; charset=utf-8');

//Include the extended API
include_once("gvServerAPIEx.php");
define( '_JEXEC', 1 );

define('JPATH_BASE', dirname(__FILE__)."/../../../..");
//define('JPATH_ADMINISTRATOR', JPATH_BASE."/administrator");

//define( 'DS', DIRECTORY_SEPARATOR );
define( 'DS', '/' );

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
include_once("../helpers/statistics/config.php");

$mainframe = JFactory::getApplication('site');
$mainframe->initialise();
require_once (JPATH_ADMINISTRATOR.DS."components".DS."com_virtuemart".DS."version.php");
$vmver = new vmVersion();
$matches = vmVersion::$RELEASE;
if(version_compare($matches, '2.0.0', 'ge')){
	define('VMRELEASE', 'NEW');
}
else{
	define('VMRELEASE', 'OLD');
}

$jlang = JFactory::getLanguage();
$jlang->load('com_netbasevm_extend', JPATH_ADMINISTRATOR, 'en-GB', true);
$jlang->load('com_netbasevm_extend', JPATH_ADMINISTRATOR, null, true);

//------------------------------------------

//-- Add here business logic, if needed
//-- For example users authentication and access control 

//------------------------------------------

// 2 parameters of the protocol are supported: tqx and responseHandler. 
// They should be pass to the gvStreamerEx object
$tqx = isset($_GET['tqx']) ? $_GET['tqx'] : NULL ;
$resHandler = isset($_GET['responseHandler']) ? $_GET['responseHandler'] : NULL;

//feed object name
$className = $_GET['fid'];
require_once( JPATH_ADMINISTRATOR.DS."components".DS."com_netbasevm_extend".DS.'tables/statistics'.DS.$className.'.php');
require_once(JPATH_ADMINISTRATOR.DS."components".DS. "com_netbasevm_extend".DS."helpers/statistics".DS."config1.php");

//indactes which view to create if this is a double view object.
$ForS = $_GET['fors'];

//get parameter if exist
if(isset($_GET['param1']))
	$dashParam1 = $_GET['param1'];

//timeFrame parameters is sending form the dashboard
$fromDate =  $_GET['fd'];
$toDate =  $_GET['td'];

$viewObj = new $className(FEEDDIR, $className);
if($viewObj->isTimeFromOrderItem())
	$timeTable = 'oi';
else
	$timeTable = 'ord';
$viewObj = null;
if(!isset($fromDate) || $fromDate == "all" || !isset($toDate) || $toDate == "all")
	$tfWhere = " ";
else{

    
	if(VMRELEASE == 'OLD'){
		$tfWhere = " AND FROM_UNIXTIME( " . $timeTable . ".cdate, '%Y-%m-%d' ) BETWEEN STR_TO_DATE('" . $fromDate . "','%Y-%m-%d') AND STR_TO_DATE('" . $toDate . "','%Y-%m-%d') ";
	}
	else{
		$tfWhere = " AND DATE( " . $timeTable . ".created_on) BETWEEN STR_TO_DATE('" . $fromDate . "','%Y-%m-%d') AND STR_TO_DATE('" . $toDate . "','%Y-%m-%d') ";
	}
}

//calculate the current date considering the time zone as defined in the Joomla configuration.
jimport('joomla.utilities.date');
$config = JFactory::getConfig();
$date = new JDate('now');
$date->setTimezone( $config->get('offset' ));
$curdate = "STR_TO_DATE('" . $date->Format('Y-m-d') . "','%Y-%m-%d')";



$viewObj = new $className(FEEDDIR, $className);

// Read the data from MySql
$db = JFactory::getDBO();

$sql = !isset($ForS) || $ForS == "" || $ForS == 'f' ? $viewObj->getSql1() : $viewObj->getSql2();

//exit;

$db->setQuery( $sql );
$result = $db->execute();

// Initialize the gvStreamerEx object
$gvJsonObj = new gvStreamerEx();
// If there will be an error during the inialization
// gvStreamerEx object will generate an error message
if($gvJsonObj->init($tqx, $resHandler) == true);
{
	//convert the entire query result into the compliant response
	if($config->get('dbtype') == "mysqli")
		$gvJsonObj->convertMysqliRes($result, "%01.2f", "m/d/Y", "G:i:s", "m/d/Y");
	else
		$gvJsonObj->convertMysqlRes($result, "%01.2f", "m/d/Y", "G:i:s", "m/d/Y");
}

echo $gvJsonObj;


?>