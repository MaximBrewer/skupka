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

//Include the extended API
define( '_JEXEC', 1 );

define('JPATH_BASE', dirname(__FILE__)."/../../..");

define( 'DS', DIRECTORY_SEPARATOR );

require_once ( '../../../../includes/defines.php' );
require_once ( '../../../../includes/framework.php' );

$mainframe = JFactory::getApplication('site');
$mainframe->initialise();


//timeFrame parameter is sending form the dashboard
$timeFrame = $_GET['timeFrame'];

jimport('joomla.utilities.date');
$config = JFactory::getConfig();
$tzoffset = $config->get('offset');
$date = new JDate('now');
$date->setTimezone( $config->get('offset' ));
$toDate = $date->Format('%Y-%m-%d');
$tmpDate = strtotime($date->Format());

switch($timeFrame)
{
	case "n":
		$fromDate = $toDate;
		break;
	case "d":
		$toDate = $fromDate = date('Y-m-d', mktime(0,0,0,date('m',$tmpDate),date('d',$tmpDate)-1,date('Y',$tmpDate))); 
		break;
	case "w":
		$fromDate = date('Y-m-d', mktime(0,0,0,date('m',$tmpDate),date('d',$tmpDate)-7,date('Y',$tmpDate))); 
		break;
	case "m":
		$fromDate = date('Y-m-d', mktime(0,0,0,date('m',$tmpDate)-1,date('d',$tmpDate),date('Y',$tmpDate))); 
		break;
	case "3m":
		$fromDate = date('Y-m-d', mktime(0,0,0,date('m',$tmpDate)-3,date('d',$tmpDate),date('Y',$tmpDate))); 
		break;
	case "6m":
		$fromDate = date('Y-m-d', mktime(0,0,0,date('m',$tmpDate)-6,date('d',$tmpDate),date('Y',$tmpDate))); 
		break;
	case "y":
		$fromDate = date('Y-m-d', mktime(0,0,0,date('m',$tmpDate),date('d',$tmpDate),date('Y',$tmpDate)-1)); 
		break;
	case "a":
		$fromDate = "all";
		$toDate = "all";
		break;
	default:
		$tfWhere = "";
}

echo $fromDate . "|" . $toDate;

?>