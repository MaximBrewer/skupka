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

jimport('joomla.application.component.view');


class VMInvoiceViewUpgrade extends VmViewAdmin
{  
    function display ($tpl = null)
    {
    	InvoiceHelper::setSubmenu(7);
    	
        $params = InvoiceHelper::getParams();
        
        $downloadId = $params->get('download_id');
        $this->downloadId=$downloadId;
        
        JToolBarHelper::title('VM Invoice: ' . JText::_('COM_VMINVOICE_UPGRADE'), 'update.png');
        
        JToolBarHelper::back('Back', 'index.php?option=com_vminvoice');
        
        $oldVer = $this->getVMIVersion();
        $this->oldVer=$oldVer;
        
        $newVer = $this->getNewestVersion();
        $this->newVer=$newVer;
        
        $regInfo = VMInvoiceModelUpgrade::getRegisteredInfo($downloadId);
        $this->regInfo=$regInfo;
        
        $isPaidVersion = $this->get('IsPaidVersion');
        $this->isPaidVersion=$isPaidVersion;
        
        JHTML::_('behavior.tooltip');
        JHTML::_('behavior.modal');
        
        parent::display($tpl);
    }

    function showMessage ()
    {
        JToolBarHelper::title('VM Invoice ' . JText::_('COM_VMINVOICE_UPGRADE'), 'update.png');
        
        $url = 'index.php?option=com_vminvoice&task=showupgrade';
        $redir = JRequest::getVar('redirto', null, 'post');
        if (! is_null($redir)) {
            $url = 'index.php?option=com_vminvoice&' . $redir;
        }
        JToolBarHelper::back('Back', $url);
        
        $this->assign('url', $url);
        
        $this->setLayout('message');
        parent::display();
    }

    function getVMIVersion ()
    {
        static $version;
        
        if (! isset($version)) {
            $xml = JFactory::getXML('Simple');
            
            $xmlFile = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_vminvoice' . DS . 'vminvoice.xml';
            
            if (JFile::exists($xmlFile)) {
                if ($xml->loadFile($xmlFile)) {
                    $root =  $xml->document;
                    $element =  $root->getElementByPath('version');
                    $version = $element ? $element->data() : '';
                }
            }
        }
        
        return $version;
    }

    function getNewestVersion ()
    {
        $db =  JFactory::getDBO();

        $configs = InvoiceHelper::getParams();
        
        $newVer = '';
        if ($configs->get('version_checker')) {
            $model2 =  JModelLegacy::getInstance('Upgrade', 'VMInvoiceModel');
            $newVer = $model2->getNewVMIVersion();
            $vmiinfo = $model2->getVMIInfo();
            
            if (((strnatcasecmp($newVer, $vmiinfo['version']) > 0) ||
             (strnatcasecmp($newVer, substr($vmiinfo['version'], 0, strpos($vmiinfo['version'], '-'))) == 0))) {
                $newVer = '<span style="font-weight: bold; color: red;">' . $newVer . '</span>';
            }
            
            $this->assign('newestVersion', $newVer);
        } else {
            $newVer = JText::_('COM_VMINVOICE_VERSION_CHECKER_DISABLED');
        }
        return $newVer;
    }

}
?>