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

defined('_JEXEC') or die('Restrict Access');

jimport('joomla.application.component.model');
require_once("viewStatistics.php");
class NetBaseVm_ExtendModelStatistics extends JModelLegacy
{
    
    var $_data = null;
    var $_pagination = null;

    function __construct ()
    {
        //global $mainframe, $option;
        $mainframe = JFactory::getApplication();
        $option = JRequest::getString('option');
        parent::__construct();
        /*
        $array = JRequest::getVar('cid', 0, '', 'array');
        $this->setId((int) $array[0]);
        */
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = $mainframe->getUserStateFromRequest($option . 'limitstart', 'limitstart', 0, 'int');
        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
    }

    public function getClassList($force = false)
    {
    	$viewListRead = viewListRead::getInstance();
    	return $viewListRead->getClassList($force);
    }
    
    public function getDisplayClasses($force = false)
    {
    	$viewListRead = viewListRead::getInstance();
    	return $viewListRead->getDisplayClasses($force);
    }
    
}
?>