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
		$user  = JFactory::getUser();
		
		/* if($user->id==0){
			$msg = JText::_('VDATA_LOGIN_ALERT');
			$mainframe->redirect(JRoute::_('index.php?option=com_users&view=login'), $msg);
		} */
		if(!$user->authorise('core.access.cron', 'com_vdata')){
			$msg = JText::_('VDATA_LOGIN_ALERT');
			$mainframe->redirect(JRoute::_('index.php?option=com_users&view=login'), $msg);
		}
		
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
        
		$this->profiles = $this->get('profiles');
		
		if($layout == 'form'){
			$this->item = $this->get('Item');
			$this->isNew = ($this->item->id < 1);
			$this->plugins = $this->get('Plugins');
			$this->viewLevels = $this->get('ViewLevels');
		}
		else{
			$user = JFactory::getUser();
			$canAdd = $user->authorise('core.create', 'com_vdata');
			$canEdit = $user->authorise('core.edit', 'com_vdata');
			$canEditOwn = $user->authorise('core.edit.own','com_vdata');
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
