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

// check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restrict Access');

// Component Helper
jimport('joomla.application.component.view');
// Panes
jimport('joomla.html.pane');

class VMInvoiceViewVMInvoice extends VmViewAdmin
{

    function display ($tpl = null)
    {
    	InvoiceHelper::setSubmenu(0);
    	
        $params = InvoiceHelper::getParams();
                
        if ($params->get('version_checker')) {
            $model2 =  JModelLegacy::getInstance('Upgrade', 'VMInvoiceModel');
            $newVer = $model2->getNewVMIVersion();
            $vmiinfo = $model2->getVMIInfo();
            
            if (((strnatcasecmp($newVer, $vmiinfo['version']) > 0) ||
             (strnatcasecmp($newVer, substr($vmiinfo['version'], 0, strpos($vmiinfo['version'], '-'))) == 0))) {
                $newVer = '<span style="font-weight: bold; color: red;">' . $newVer .
                 '</span>&nbsp;&nbsp;<input type="button" onclick="showUpgrade();" value="' . JText::_('COM_VMINVOICE_GO_TO_UPGRADE_PAGE') . '" />';
            }
            
            $this->assign('newestVersion', $newVer);
        } else {
            $newestVersion = JText::_('COM_VMINVOICE_VERSION_CHECKER_DISABLED');
            $this->assign('newestVersion', $newestVersion);
        }
        
        JToolBarHelper::title('ARTIO VM Invoice', 'vminvoice');
        parent::display($tpl);
    }

}
?>
