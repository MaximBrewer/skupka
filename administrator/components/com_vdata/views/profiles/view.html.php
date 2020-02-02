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
		
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
        
		$this->config = $this->get('Config');
		
		$user = JFactory::getUser();
		$canAdd = $user->authorise('core.create', 'com_vdata');
		$canEdit = $user->authorise('core.edit', 'com_vdata');
		$canEditOwn = $user->authorise('core.edit.own', 'com_vdata');
		$canEditState = $user->authorise('core.edit.state', 'com_vdata');
		$canDelete = $user->authorise('core.delete', 'com_vdata');
		
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
				$isNew	= ($this->item->id < 1);
				$this->remote = 0;
			}
			
			$this->plugins = $this->get('Plugins');
			$this->topProfiles = $this->get('TopRemoteProfiles');
			
			if($isNew)
				JToolBarHelper::title( JText::_( 'PROFILE' ).' <small><small>'.JText::_('NEW').'</small></small>', 'user' );
			
			else	{
				JToolBarHelper::title( JText::_( 'PROFILE' ).' <small><small>'.JText::_('EDIT').'</small></small>', 'user' );
			}
			
			JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolBarHelper::cancel();
			if(!$isNew){
				JToolbarHelper::save2copy('save2copy');
				JToolBarHelper::custom('schedule','link','',JText::_('CRON_FEED'),false);
			}
		}
		elseif($layout == 'wizard'){
			
			JToolBarHelper::title( JText::_( 'PROFILE_WIZARD' ), 'cube' );
			
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
			
			$version = new JVersion;
			$joomla = $version->getShortVersion();
			$jversion = substr($joomla,0,3);
			$this->sidebar ='';
			if($jversion>=3.0){
				$this->sidebar = JHtmlSidebar::render();
			}
			JToolBarHelper::cancel();
		}
		else{
			JToolBarHelper::title( JText::_( 'PROFILES' ), 'users' );
			if($canAdd){
				JToolBarHelper::addNew();
			}
			if($canEdit || $canEditOwn){
				JToolBarHelper::editList();
			}
			if($canAdd){
				JToolbarHelper::save2copy('saveAsCopy', JText::_('COPY'));
				// JToolBarHelper::custom('saveAsCopy', 'copy', '', JText::_('COPY'),false);
			}
			
			//profile wizard
			JToolBarHelper::custom('profile_wizard', 'link', '', JText::_('PROFILE_WIZARD'),false);
			
			if($canDelete){
				JToolBarHelper::deleteList(JText::_('DELETE_CONFIRM'));
			}
			$this->items = $this->get('Items');
			$this->pagination = $this->get('Pagination');
			
			// Table ordering.
			$version = new JVersion;
			$joomla = $version->getShortVersion();
			$jversion = substr($joomla,0,3);
			$this->sidebar ='';
			if($jversion>=3.0)
			{
			$this->sidebar = JHtmlSidebar::render();
			}
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;
		}
		
		parent::display($tpl);
        
    }
  
  
}
