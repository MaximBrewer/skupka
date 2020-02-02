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

class VdataViewImport extends VDView
{    
    function display($tpl = null)
    {
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		
		$layout = JFactory::getApplication()->input->getCmd('layout', '');

		if($layout=="import")	{

			$this->profile = $this->get('Profile');
            $st = JFactory::getApplication()->input->getCmd('st',0);
			
            JPluginHelper::importPlugin('vdata', $this->profile->plugin);
			$dispatcher = JDispatcher::getInstance();
			
			try {
				ob_start();
				$dispatcher->trigger('onImportProfile');
				$this->contents = ob_get_contents();
				ob_end_clean();
			
			}catch(Exception $e){
				JFactory::getApplication()->enqueueMessage($e->getMessage());
				if($st)
					$mainframe->redirect('index.php?option=com_vdata&view=schedules&task=edit&cid[]='.JFactory::getApplication()->input->getInt('id', 0));
				else
					$mainframe->redirect('index.php?option=com_vdata&view=import');
			}

			
			if($st){
				$session = JFactory::getSession();
				$importitem = $session->get('importitem', null);
				JToolBarHelper::title( $importitem->title.' '.JText::_( 'SCHEDULE' ), 'schedule' );
				JToolBarHelper::apply('save_st', JText::_('SAVE_ST'));
				$isNew = (JFactory::getApplication()->input->getInt('id', 0) < 1);
				if(!$isNew){
					JToolBarHelper::save2copy('save_st2copy');
				}
				JToolBarHelper::cancel('close_st');
			}
			else{
				$user = JFactory::getUser();
				$canImport = $user->authorise('core.import', 'com_vdata');
				JToolBarHelper::title( $this->profile->title.' '.JText::_( 'IMPORT' ), 'import' );
				if($canImport){
					//JToolBarHelper::apply('import_now', JText::_('IMPORT_NOW'));
					JToolBarHelper::apply('import_start', JText::_('IMPORT_NOW'));
				}
				
				JToolBarHelper::cancel('close');
				
				$files = $mainframe->input->files->get('file', array());
				if( (empty($files[0]['name']) && empty($files[0]['type']) && empty($files[0]['size']) && ($files[0]['error'] != 0) ) ){
					JToolBarHelper::custom('create_st', 'link', '', JText::_('CREATE_SCHEDULE'),false);
				}
			}
		}
		
		else	{
			
			JToolBarHelper::title( JText::_( 'IMPORT_DATA' ), 'import' );
			JToolBarHelper::apply('importready', JText::_('CONTINUE'));
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
