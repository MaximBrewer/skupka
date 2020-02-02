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
		$user  = JFactory::getUser();
		/* if($user->id==0){
			$msg = JText::_('VDATA_LOGIN_ALERT');
			$mainframe->redirect(JRoute::_('index.php?option=com_users&view=login'), $msg);
		} */
		if(!$user->authorise('core.access.import', 'com_vdata')){
			$msg = JText::_('VDATA_LOGIN_ALERT');
			$mainframe->redirect(JRoute::_('index.php?option=com_users&view=login'), $msg);
		}
		
		$layout = JFactory::getApplication()->input->getCmd('layout', '');
		
		if($layout=="import")	{

			$this->profile = $this->get('Profile');
            $this->st = JFactory::getApplication()->input->getCmd('st', 0);
			
            JPluginHelper::importPlugin('vdata', $this->profile->plugin);
			$dispatcher = JDispatcher::getInstance();
			
			try {
				ob_start();
				$dispatcher->trigger('onImportProfile');
				$this->contents = ob_get_contents();
				ob_end_clean();
			
			}catch(Exception $e){
				JFactory::getApplication()->enqueueMessage($e->getMessage());
				if($this->st)
					$mainframe->redirect('index.php?option=com_vdata&view=schedules&task=edit&cid[]='.JFactory::getApplication()->input->getInt('id', 0));
				else
					$mainframe->redirect('index.php?option=com_vdata&view=import');
			}

			
			if($this->st){
				$session = JFactory::getSession();
				$importitem = $session->get('importitem', null);
				/* JToolBarHelper::title( $importitem->title.' '.JText::_( 'SCHEDULE' ), 'schedule' );
				JToolBarHelper::apply('save_st', JText::_('SAVE_ST'));
				JToolBarHelper::cancel('close_st'); */
			}
			else{
				/* JToolBarHelper::title( $this->profile->title.' '.JText::_( 'IMPORT' ), 'import' );
				JToolBarHelper::apply('import_start', JText::_('IMPORT_NOW'));
				JToolBarHelper::cancel('close'); */
			}
		}
		
		else	{
			
			/* JToolBarHelper::title( JText::_( 'IMPORT_DATA' ), 'import' );
			JToolBarHelper::apply('importready', JText::_('CONTINUE'));
			JToolBarHelper::cancel(); */
			
			$this->profiles = $this->get('Profiles');
                        
            $this->item = $this->get('Item');
		
		}
							
		parent::display($tpl);
        
    }
  
  
}
