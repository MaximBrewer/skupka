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

class VdataViewSchedules extends VDView
{
    function display($tpl = null)
    {
		$mainframe = JFactory::getApplication();
		$context			= 'com_vdata.schedules.list.';
		$layout = JFactory::getApplication()->input->getCmd('layout', '');
		
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
        
		$this->profiles = $this->get('profiles');
		
		if($layout == 'form'){
			$this->item = $this->get('Item');
			$isNew		= ($this->item->id < 1);
			$this->plugins = $this->get('Plugins');
			$this->viewLevels = $this->get('ViewLevels');
			
			$this->qry = JFactory::getApplication()->input->get->getArray();
			$this->qry = array_key_exists('qry', $this->qry)? $this->qry['qry'] : '';
			
			
			
			if($isNew){
				JToolBarHelper::title( JText::_( 'SCHEDULE' ).' <small><small>'.JText::_('NEW').'</small></small>', 'feed' );
				// JToolBarHelper::apply('',JText::_('CONTINUE'));
				$session = JFactory::getSession();
				$cols = $session->get('columns', '');
				$iotype = JRequest::getInt('iotype', 0);
				if($iotype==1){
					$st_params = $session->get('exportitem', null);
				}
				elseif($iotype==0){
					$st_params = $session->get('importitem', null);
				}
				if($cols && !empty($st_params)){
					JToolBarHelper::apply('save_st',JText::_('SAVE_ST'));
				}
				else{
					JToolBarHelper::apply('',JText::_('CONTINUE'));
				}
			}
			else	{
				JToolBarHelper::title( $this->item->title.' <small><small>'.JText::_('EDIT').'</small></small>', 'feed' );
				// importready/exportready task based on import/export type schedule
				if($this->item->iotype==0)
					JToolBarHelper::apply('importready', JText::_('CONTINUE'));
				else
					JToolBarHelper::apply('exportready', JText::_('CONTINUE'));
			}
			JToolBarHelper::cancel();
		}
		else{
			$user = JFactory::getUser();
			$canAdd = $user->authorise('core.create', 'com_vdata');
			$canEdit = $user->authorise('core.edit', 'com_vdata');
			$canEditOwn = $user->authorise('core.edit.own', 'com_vdata');
			$canEditState = $user->authorise('core.edit.state', 'com_vdata');
			$canDelete = $user->authorise('core.delete', 'com_vdata');
			JToolBarHelper::title( JText::_( 'SCHEDULES' ), 'feed' );
			if($canDelete){
				JToolBarHelper::deleteList(JText::_('DELETE_CONFIRM'));
			}
			if($canEdit || $canEditOwn ){
				JToolBarHelper::editList();
			}
			if($canAdd){
				JToolBarHelper::addNew();
			}
        	if($canEditState){
				JToolBarHelper::publishList('publish', 'JTOOLBAR_ENABLE');
				JToolBarHelper::unpublishList('unpublish','JTOOLBAR_DISABLE');
			}
			if($canAdd){
				JToolbarHelper::save2copy('saveAsCopy');
				// JToolBarHelper::custom('saveAsCopy', 'copy', '', JText::_('SAVE_AS_COPY'),false);
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
