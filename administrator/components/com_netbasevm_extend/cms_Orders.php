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
//Include the extended API
define( '_JEXEC', 1 );

define('JPATH_BASE', dirname(__FILE__)."/../../..");

define( 'DS', DIRECTORY_SEPARATOR );

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
//include_once("dashboard.cfg.php");

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
define("ENDSTATUSES", "'C','S','P','U','X','R'");

$jlang = JFactory::getLanguage();
$jlang->load('com_netbasevm_extend', JPATH_ADMINISTRATOR, 'en-GB', true);
$jlang->load('com_netbasevm_extend', JPATH_ADMINISTRATOR, null, true);

//calculate current date with TZ consideration
jimport('joomla.utilities.date');
$config = JFactory::getConfig();
$tzoffset = $config->get('offset');
$date = new JDate('now');
$date->setTimezone( $config->get('offset' ));
$tmpDate = strtotime($date->Format());

$currDate = $date->Format('%Y-%m-%d');
$lastWeek = date('Y-m-d', mktime(0,0,0,date('m',$tmpDate),date('d',$tmpDate)-7,date('Y',$tmpDate))); 
$lastMonth = date('Y-m-d', mktime(0,0,0,date('m',$tmpDate)-1,date('d',$tmpDate),date('Y',$tmpDate))); 
$lastYear = date('Y-m-d', mktime(0,0,0,date('m',$tmpDate),date('d',$tmpDate),date('Y',$tmpDate)-1)); 

// Read the data from MySql

$db = JFactory::getDBO();

//SQL for max daily order in last month and average daily order in last month

if(VMRELEASE == 'OLD'){
	$sql1 = "	SELECT IFNULL(FORMAT(MAX(ot),2),0) as max_daily_month, IFNULL(FORMAT(SUM(ot)/30,2),0) as avg_daily_month
				FROM 
				(
					SELECT SUM(ord.order_total) as ot 
					FROM #__vm_orders ord 
					WHERE ord.order_status IN (" . ENDSTATUSES . ") AND FROM_UNIXTIME( ord.cdate, '%Y-%m-%d' ) BETWEEN STR_TO_DATE('" . $lastMonth . "','%Y-%m-%d') AND STR_TO_DATE('" . $currDate . "','%Y-%m-%d')
					GROUP BY FROM_UNIXTIME( ord.cdate, '%Y-%m-%d' )
				) as ord1";
}
else{
	$sql1 = "	SELECT IFNULL(FORMAT(MAX(ot),2),0) as max_daily_month, IFNULL(FORMAT(SUM(ot)/30,2),0) as avg_daily_month
				FROM
				(
				SELECT SUM(ord.order_total) as ot
				FROM #__virtuemart_orders ord
				WHERE ord.order_status IN (" . ENDSTATUSES . ") AND DATE( ord.created_on) BETWEEN STR_TO_DATE('" . $lastMonth . "','%Y-%m-%d') AND STR_TO_DATE('" . $currDate . "','%Y-%m-%d')
				GROUP BY DATE( ord.created_on)
	) as ord1";
}
$db->setQuery( $sql1 );
$row1 = $db->loadAssoc();

//SQL for max daily order in last year and average daily order in last year
if(VMRELEASE == 'OLD'){
	$sql2 = "	SELECT IFNULL(FORMAT(MAX(ot),2),0) as max_daily_year, IFNULL(FORMAT(SUM(ot)/365,2),0) as avg_daily_year
				FROM 
				(
					SELECT SUM(ord.order_total) as ot 
					FROM #__vm_orders ord 
					WHERE ord.order_status IN (" . ENDSTATUSES . ") AND FROM_UNIXTIME( ord.cdate, '%Y-%m-%d' ) BETWEEN STR_TO_DATE('" . $lastYear . "','%Y-%m-%d') AND STR_TO_DATE('" . $currDate . "','%Y-%m-%d')
					GROUP BY FROM_UNIXTIME( ord.cdate, '%Y-%m-%d' )
				) as ord1";
}
else{
	$sql2 = "	SELECT IFNULL(FORMAT(MAX(ot),2),0) as max_daily_year, IFNULL(FORMAT(SUM(ot)/365,2),0) as avg_daily_year
				FROM
				(
				SELECT SUM(ord.order_total) as ot
				FROM #__virtuemart_orders ord
				WHERE ord.order_status IN (" . ENDSTATUSES . ") AND DATE( ord.created_on) BETWEEN STR_TO_DATE('" . $lastYear . "','%Y-%m-%d') AND STR_TO_DATE('" . $currDate . "','%Y-%m-%d')
				GROUP BY DATE( ord.created_on)
				) as ord1";
}
$db->setQuery( $sql2 );
$row2 = $db->loadAssoc();

//SQL for last week daily average orders
if(VMRELEASE == 'OLD'){
	$sql3 = "	SELECT IFNULL(FORMAT(SUM(ot)/7,2),0) as avg_daily_week
				FROM 
				(
					SELECT SUM(ord.order_total) as ot 
					FROM #__vm_orders ord 
					WHERE ord.order_status IN (" . ENDSTATUSES . ") AND FROM_UNIXTIME( ord.cdate, '%Y-%m-%d' ) BETWEEN STR_TO_DATE('" . $lastWeek . "','%Y-%m-%d') AND STR_TO_DATE('" . $currDate . "','%Y-%m-%d')
					GROUP BY FROM_UNIXTIME( ord.cdate, '%Y-%m-%d' )
				) as ord1";
}
else{
	$sql3 = "	SELECT IFNULL(FORMAT(SUM(ot)/7,2),0) as avg_daily_week
				FROM
				(
				SELECT SUM(ord.order_total) as ot
				FROM #__virtuemart_orders ord
				WHERE ord.order_status IN (" . ENDSTATUSES . ") AND DATE( ord.created_on) BETWEEN STR_TO_DATE('" . $lastWeek . "','%Y-%m-%d') AND STR_TO_DATE('" . $currDate . "','%Y-%m-%d')
				GROUP BY DATE( ord.created_on)
				) as ord1";
}

$db->setQuery( $sql3 );
$row3 = $db->loadAssoc();

$response = str_replace(",","",$row1['max_daily_month']) . "|" . str_replace(",","",$row1['avg_daily_month'])  . "|" . str_replace(",","",$row2['max_daily_year'])  . "|" . str_replace(",","",$row2['avg_daily_year'])  . "|" . str_replace(",","",$row3['avg_daily_week']);

echo $response;

?>