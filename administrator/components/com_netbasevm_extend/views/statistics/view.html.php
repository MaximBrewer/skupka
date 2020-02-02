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

defined('_JEXEC') or ('Restrict Access');
// Load the view framework
require_once (JPATH_ADMINISTRATOR.DS."components".DS."com_virtuemart".DS."version.php");
$vmver = new vmVersion();
$matches = vmVersion::$RELEASE;
if(version_compare($matches, '3.0.0', 'ge')){
	if(!class_exists('VmView'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmviewadmin.php');
}
else{
	if(!class_exists('VmView'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmview.php');
}
Jimport('joomla.application.component.view');

require_once(JPATH_ADMINISTRATOR.DS."components".DS. "com_netbasevm_extend".DS."helpers/statistics".DS."config.php");

class NetBaseVm_ExtendViewStatistics extends VmViewAdmin
{

    function display ($tpl = null)
    {
    	$this->addHelperPath(array(JPATH_VM_ADMINISTRATOR.DS.'helpers'));
		// Load the helper(s)
		$this->loadHelper('html');
    	
        JToolBarHelper::title('Netbase VM Extend: ' . JText::_('COM_NETBASEVM_EXTEND_STATISTICS_INVOICES'), 'Statistics');

        $model = $this->getModel();

        $displayClasses = $model->getDisplayClasses(false);
        $this->displayClasses=$displayClasses;
        parent::display($tpl);
    }

}
?>