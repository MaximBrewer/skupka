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
defined('_JEXEC') or die('Restricted access');

class VdataViewExport extends VDView
{    
    function display($tpl = null)
    {
		
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		$user  = JFactory::getUser();
		
		/* if($user->id==0){
			$msg = JText::_('VDATA_LOGIN_ALERT');
			$mainframe->redirect(JRoute::_('index.php?option=com_users&view=login'), $msg);
		} */
		if(!$user->authorise('core.access.export', 'com_vdata')){
			$msg = JText::_('VDATA_LOGIN_ALERT');
			$mainframe->redirect(JRoute::_('index.php?option=com_users&view=login'), $msg);
		}
		
		$layout = JFactory::getApplication()->input->getCmd('layout', '');
		
		if($layout=="export")	{
						
			$this->profile = $this->get('Profile');
             
			$this->st = JFactory::getApplication()->input->getCmd('st',0);
			$profileid = JFactory::getApplication()->input->getInt('profileid', 0);
			if( ($this->st==1) && ($profileid==0) ){
				JPluginHelper::importPlugin('vdata', 'custom');
			} 
			else{
				JPluginHelper::importPlugin('vdata', $this->profile->plugin);
			}
			$dispatcher = JDispatcher::getInstance();

			try{
				ob_start();
				$dispatcher->trigger('onExportProfile');
				$this->contents = ob_get_contents();
				ob_end_clean();
			
			}
			catch(Exception $e){
				JFactory::getApplication()->enqueueMessage($e->getMessage());
				// $mainframe->redirect('index.php?option=com_vdata&view=export');
				if($this->st)
					$mainframe->redirect('index.php?option=com_vdata&view=schedules&task=edit&cid[]='.JFactory::getApplication()->input->getInt('id', 0));
				else
					$mainframe->redirect('index.php?option=com_vdata&view=export');
			}
           
			if($this->st){
				$session = JFactory::getSession();
				$exportitem = $session->get('exportitem', null);
				/* JToolBarHelper::title( $exportitem->title.' '.JText::_( 'SCHEDULE' ), 'schedule' );
				JToolBarHelper::apply('save_st', JText::_('SAVE_ST'));
				JToolBarHelper::cancel('close_st'); */
			}
			else{
				/* JToolBarHelper::title( $this->profile->title.' '.JText::_( 'EXPORT' ), 'export' );
				JToolBarHelper::apply('export_start', JText::_('EXPORT_START'));
				JToolBarHelper::cancel('close'); */
			}
			
		
		}
		
		else	{
			
			/* JToolBarHelper::title( JText::_( 'EXPORT_DATA' ), 'export' );
			JToolBarHelper::apply('exportready', JText::_('CONTINUE'));
			JToolBarHelper::cancel(); */
			
			$this->profiles = $this->get('Profiles');
                        
            $this->item = $this->get('Item');
		
		}
							
		parent::display($tpl);
        
    }
  
  
}
