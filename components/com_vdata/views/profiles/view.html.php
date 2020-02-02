<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die(); 

class VdataViewProfiles extends VDView
{
    
    function display($tpl = null)
    {
		
		$mainframe = JFactory::getApplication();
		$context			= 'com_vdata.profiles.list.';
		$layout = JFactory::getApplication()->input->getCmd('layout', '');
		
		$user  = JFactory::getUser();
		/* if($user->id==0){
			$msg = JText::_('VDATA_LOGIN_ALERT');
			$mainframe->redirect(JRoute::_('index.php?option=com_users&view=login'), $msg);
		} */
		if(!$user->authorise('core.access.profiles', 'com_vdata')){
			$msg = JText::_('VDATA_LOGIN_ALERT');
			$mainframe->redirect(JRoute::_('index.php?option=com_users&view=login'), $msg);
		}
		
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
        
		$this->config = $this->get('Config');
		
		if($layout == 'form')	{
        	
			$origin = JFactory::getApplication()->input->getCmd('origin', 'local');
			if($origin=='remote'){
				$this->item = $this->get('WizardProfile');
				$this->item->id = 0;
				$this->item->quick = 0;
				$this->item->params  = json_decode($this->item->params);
				$isNew = true;
				$this->remote = 1;
			}
			else{
				$this->item = $this->get('Item');
				$isNew		= ($this->item->id < 1);
				$this->remote = 0;
			}

			$this->plugins = $this->get('Plugins');
			$this->topProfiles = $this->get('TopRemoteProfiles');
			
		}
		elseif($layout == 'wizard'){
			// $this->components = $this->get('Components');
			
			$wizardProfiles = $this->get('WizardProfiles');
			if($wizardProfiles){
				$this->components = $wizardProfiles->components?$wizardProfiles->components:array();
				$this->profiles = $wizardProfiles->profiles;
				$this->total = $wizardProfiles->total;
			}
			else{
				$this->components = array();
				$this->profiles = false;
				$this->total = 0;
			}
			// $this->profiles = $this->get('WizardProfiles');
			
			$config = JFactory::getConfig();
			$config_limit = $config->get('list_limit');
			$limit = JFactory::getApplication()->input->getVar('limit', $config_limit);
			$limitstart = JFactory::getApplication()->input->getVar('limitstart', 0);
			
			$this->conf_limit = $limit;
			$this->pagination = new JPagination($this->total, $limitstart, $limit);
		}
		else	{
			
			$user = JFactory::getUser();
			$canAdd = $user->authorise('core.create', 'com_vdata');
			$canEdit = $user->authorise('core.edit', 'com_vdata');
			$canEditState = $user->authorise('core.edit.state', 'com_vdata');
			$canDelete = $user->authorise('core.delete', 'com_vdata');
        	
			
			$this->items = $this->get('Items');
			
			$this->pagination = $this->get('Pagination');
	
			// Table ordering.
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;
			
		}
		
		parent::display($tpl);
        
    }
  
  
}
