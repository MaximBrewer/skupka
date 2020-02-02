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
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

class VMInvoiceViewInfo extends VmViewAdmin
{

	function display($tpl = null)
	{
		//$task = JRequest::getVar('task');
                $task=$input->get('task');
	
		switch ($task) {
		    case 'help':
		    default:
		        $title = 'VM Invoice '.JText::_('COM_VMINVOICE_SUPPORT');
		        $icon = 'help.png';
		}

		InvoiceHelper::setSubmenu(8);
		JToolBarHelper::title($title, $icon);		
		JToolBarHelper::back(JText::_('COM_VMINVOICE_BACK'), 'index.php?option=com_vminvoice');

		parent::display($tpl);
	}

}
