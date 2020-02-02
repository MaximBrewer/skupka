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
defined('_JEXEC') or die('Restrict access');
if(!defined('DS')){
   define('DS',DIRECTORY_SEPARATOR);
}
//error here
require_once(JPATH_ADMINISTRATOR.DS."components/com_netbasevm_extend/helpers/statistics/config.php");
// Component Helper
jimport('joomla.application.component.controller');

class NetBaseVm_ExtendController extends JControllerLegacy 
{	
	public function display($cachable = false, $urlparams = false)
	{
		parent::display();
	}
	
	function setSession()
	{
		$fromDate =  $_GET['fd'];
		$toDate =  $_GET['td'];
		$presetDate =  $_GET['pd'];
	
		$session = JFactory::getSession();
		if($session->getState() != "active")
			$session->restart();
		$session->set('sess_from_date', $fromDate);
		$session->set('sess_end_date', $toDate);
		$session->set('sess_preset', $presetDate);
	
		//We exit here since this is a AJAX call and we don't want the view to be echo to the client
		exit;
	}
}
?>