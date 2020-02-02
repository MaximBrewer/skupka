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
		
		$layout = JFactory::getApplication()->input->getCmd('layout', '');
		
		if($layout=="export")	{
		
			$this->profile = $this->get('Profile');
             
			 $st = JFactory::getApplication()->input->getCmd('st',0);
			 $profileid = JFactory::getApplication()->input->getInt('profileid', 0);
			if( ($st==1) && ($profileid==0) ){
				JPluginHelper::importPlugin('vdata', 'custom');
			}
			else{
				JPluginHelper::importPlugin('vdata', $this->profile->plugin);
			}
            
			// JPluginHelper::importPlugin('vdata', $this->profile->plugin);
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
				if($st)
					$mainframe->redirect('index.php?option=com_vdata&view=schedules&task=edit&cid[]='.JFactory::getApplication()->input->getInt('id', 0));
				else
					$mainframe->redirect('index.php?option=com_vdata&view=export');
			}
           
			if($st){
				$session = JFactory::getSession();
				$exportitem = $session->get('exportitem', null);
				JToolBarHelper::title( $exportitem->title.' '.JText::_( 'SCHEDULE' ), 'schedule' );
				JToolBarHelper::apply('save_st', JText::_('SAVE_ST'));
				$isNew = (JFactory::getApplication()->input->getInt('id', 0) < 1);
				if(!$isNew){ 
					JToolBarHelper::save2copy('save_st2copy');
				}
				JToolBarHelper::cancel('close_st');
			}
			else{
				$user = JFactory::getUser();
				$canExport = $user->authorise('core.export', 'com_vdata');
				JToolBarHelper::title( $this->profile->title.' '.JText::_( 'EXPORT' ), 'out-2' );
				if($canExport){
					JToolBarHelper::apply('export_start', JText::_('EXPORT_START'));
				}
				JToolBarHelper::cancel('close');
				
				if( ($mainframe->input->get('server', 'down', 'string') != 'down') || ($mainframe->input->get('source', '', 'string')=='remote') ){
					JToolBarHelper::custom('create_st', 'link', '', JText::_('CREATE_SCHEDULE'), false);
				}
			}
			
		
		}
		
		else	{
			
			JToolBarHelper::title( JText::_( 'EXPORT_DATA' ), 'out-2' );
			JToolBarHelper::apply('exportready', JText::_('CONTINUE'));
			JToolBarHelper::cancel();
			$version = new JVersion;
			$joomla = $version->getShortVersion();
			$jversion = substr($joomla,0,3);
			$this->sidebar ='';
			if($jversion>=3.0)
			{
			$this->sidebar = JHtmlSidebar::render();
			}
			$this->profiles = $this->get('Profiles');
                        
            $this->item = $this->get('Item');
		
		}
							
		parent::display($tpl);
        
    }
  
  
}
