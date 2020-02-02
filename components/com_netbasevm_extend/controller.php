<?php

if (!defined('_VALID_MOS') && !defined('_JEXEC'))
die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');

/*------------------------------------
* -Netbase- Advanced Virtuemart Invoices for Virtuemart
* Author    CMSMart Team
* Copyright (C) 2012 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support:  Forum - http://bloorum.com/forums
-----------------------------------------------------*/

// Load the controller framework
jimport('joomla.application.component.controller');

class NetBaseVM_ExtendController extends JControllerLegacy {
	
	/**
	 *
	 */
	public function request()
	{
		$view = JRequest::getCmd('view', '');
		
		/*
		$download_code = JRequest::getVar('download_code', '');
		
		if (!empty($download_code)) {
			if (!class_exists('DigiTollDownloadsModelDownload')) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_digitolldownloads'.DS.'models'.DS.'download.php');
			$model = new DigiTollDownloadsModelDownload();
			$model->downloadRequest($download_code);
		}
		*/
	}
}
