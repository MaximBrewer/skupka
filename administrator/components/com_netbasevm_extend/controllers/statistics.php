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

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Articles list controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @since	1.6
 */
class NetBaseVm_ExtendControllerStatistics extends JControllerLegacy
{
	function __construct ($config = array())
    {
        parent::__construct($config);

    }

    function display($cachable = false, $urlparams = false)
    {
       	JRequest::setVar('view', 'statistics');
        parent::display($cachable, $urlparams);
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
