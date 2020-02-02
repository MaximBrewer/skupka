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

jimport('joomla.application.plugin');

class plgSystemNetbaseVm_Extend extends JPlugin
{

	/**
	* Loads Virtuemart Simple CSV language when visiting VM backend
	*
	*/
	public function onAfterInitialise()
	{
		$option = JRequest::getCmd('option');
		if ($option == 'com_virtuemart' || $option == 'com_netbasevm_extend') {
			$this->_loadLanguage();
			
			// Load menu images and CSS
			//$document = JFactory::getDocument();
			//$document->addStyleSheet(JURI::root(true).'/administrator/components/com_netbasevm_extend/assets/css/menu_images.css');
		}
		//$this->_loadLanguage();
	}

	private function _loadLanguage()
	{
		$jlang = JFactory::getLanguage();
		$jlang->load('plg_system_netbasevm_extend', JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('plg_system_netbasevm_extend', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
		$jlang->load('plg_system_netbasevm_extend', JPATH_ADMINISTRATOR, null, true);
	}
}
