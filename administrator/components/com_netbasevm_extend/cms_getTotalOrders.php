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
define( '_JEXEC', 1 );

define('JPATH_BASE', dirname(__FILE__)."/../../..");

define( 'DS', DIRECTORY_SEPARATOR );

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

define("ENDSTATUSES", "'C','S'");
define("VERSION_NUMBER","2.1.0");

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

//timeFrame parameter is sending form the dashboard
//timeFrame parameters is sending form the dashboard
$fromDate =  $_GET['fd'];
$toDate =  $_GET['td'];

if(!isset($fromDate) || $fromDate == "all" || !isset($toDate) || $toDate == "all")
	$tfWhere = "";
else{
	if(VMRELEASE == 'OLD'){
		$tfWhere = " AND FROM_UNIXTIME( ord.cdate, '%Y-%m-%d' ) BETWEEN STR_TO_DATE('" . $fromDate . "','%Y-%m-%d') AND STR_TO_DATE('" . $toDate . "','%Y-%m-%d')";
	}
	else{
		$tfWhere = " AND DATE( ord.created_on) BETWEEN STR_TO_DATE('" . $fromDate . "','%Y-%m-%d') AND STR_TO_DATE('" . $toDate . "','%Y-%m-%d')";
	}
}

// Read the data from MySql

$db = JFactory::getDBO();
if(VMRELEASE == 'OLD'){
	$ordersTable = "#__vm_orders";
}
else{
	$ordersTable = "#__virtuemart_orders";
}
$sqlLife = "SELECT order_currency as  'cs',IFNULL(FORMAT(SUM(ord.order_total),2), 0) as 'Total life', IFNULL(FORMAT(AVG(ord.order_total),2),0) as 'Avg life'
FROM " . $ordersTable . " ord
WHERE (ord.order_status IN (" . ENDSTATUSES . "))";

$db->setQuery( $sqlLife );
$rowLife = $db->loadAssoc();

$sc = getsymbol2($rowLife['cs']);

$sqlPeriod = "SELECT IFNULL(FORMAT(SUM(ord.order_total),2), 0) as 'Total period', IFNULL(FORMAT(AVG(ord.order_total),2),0) as 'Avg period'
FROM " . $ordersTable . " ord
WHERE (ord.order_status IN (" . ENDSTATUSES . "))" . $tfWhere;

$db->setQuery( $sqlPeriod );
$rowPeriod = $db->loadAssoc();

$response = $sc[0].' '.$rowLife['Total life'] . "|" . $rowLife['Avg life']  . "|" .$sc[0].' '. $rowPeriod['Total period'] . "|" . $rowPeriod['Avg period'] . "|" . JText::_('Average order per period') . "|" . JText::_('Total income per period') . "|" . JText::_('All-time average order') . "|" . JText::_('All-time total orders income').'|';
echo $response;

function getsymbol2 ($id){
	$db = JFactory::getDBO();
	$query =$db->getQuery(true);

	$query->select($db->quoteName(array('currency_symbol')));
	$query->from($db->quoteName('#__virtuemart_currencies'));
	$query->where($db->quoteName('virtuemart_currency_id') . ' = '. $db->quote($id));

	$db->setQuery ($query);
	$row = $db->loadAssoc();
	$cs = $row['currency_symbol'];

	return array($cs);
}
?>