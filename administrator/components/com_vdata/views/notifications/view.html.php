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

class VdataViewNotifications extends VDView
{
    
    function display($tpl = null)
    {
		
		$mainframe = JFactory::getApplication();
		$context			= 'com_vdata.notifications.list.';
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

			$this->item = $this->get('Item');
			$isNew	= ($this->item->id < 1);
			$this->tables = $this->get('Tables');
			
			$model = $this->getModel();
			if(isset($this->item->params->custom->table) && !empty($this->item->params->custom->table)){
				$this->tableColumns = $model->getTableFields($this->item->params->custom->table);
			}
			else{
				$this->tableColumns = array();
			}
			
			$this->notificationColumns = '';
			if( (isset($this->item->params->custom->table) && !empty($this->item->params->custom->table)) || (isset($this->item->params->query) && !empty($this->item->params->query)) ){
				
				$tableColumns = isset($this->item->params->custom->columns)?$this->item->params->custom->columns:array();
				$selected = isset($this->item->notification_tmpl->recipient->column->value)?$this->item->notification_tmpl->recipient->column->value:'';
				$result = $model->getNotificationColumn($this->item->params->custom->table, $tableColumns, $this->item->params->query, $selected);
				if($result->result=='success'){
					$this->notificationColumns = $result->html;
				}
				
			}
			$this->usergroups = $this->get('UserGroups');
			
			if($isNew){
				JToolBarHelper::title( JText::_( 'NOTIFICATION' ).' <small><small>'.JText::_('NEW').'</small></small>', 'user' );
			}
			else{
				JToolBarHelper::title( JText::_( 'NOTIFICATION' ).' <small><small>'.JText::_('EDIT').'</small></small>', 'user' );
			}
			
			JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolBarHelper::cancel();
			if(!$isNew){
				JToolbarHelper::save2copy('save2copy');
			}
		}
		else{
			JToolBarHelper::title( JText::_( 'NOTIFICATIONS' ), 'users' );
			if($canDelete){
				JToolBarHelper::deleteList(JText::_('DELETE_CONFIRM'));
			}
			if($canEdit || $canEditOwn){
				JToolBarHelper::editList();
			}
			if($canAdd){
				JToolBarHelper::addNew();
			}

			$this->items = $this->get('Items');			
			$this->pagination = $this->get('Pagination');
			
			if($canEditState){
				JToolBarHelper::publishList('publish', 'JTOOLBAR_ENABLE');
				JToolBarHelper::unpublishList('unpublish','JTOOLBAR_DISABLE');
			}
			
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
