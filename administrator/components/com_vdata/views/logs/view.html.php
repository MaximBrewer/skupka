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

class VdataViewLogs extends JViewLegacy
{    
    function display($tpl = null)
    {
		
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		$context			= 'com_vdata.logs.list.';
		$layout = JFactory::getApplication()->input->getCmd('layout', '');
		$this->sortColumn     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.id', 'cmd' );
		$this->sortDirection = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
		$search_log = $mainframe->getUserStateFromRequest($context.'search_log', 'search_log', '', 'string');
		   
		
		if($layout == 'form')	{
        	
			$this->log = $this->get('Item');
			$isNew		= ($this->log->id < 1);
			
			
			if($isNew)
				JToolBarHelper::title( JText::_( 'Logs' ).' <small><small>'.JText::_('NEW').'</small></small>', 'profiles' );
			
			else	{
				JToolBarHelper::title( JText::_( 'Logs' ).' <small><small>'.JText::_('Details').'</small></small>', 'list-2' );
			}
			
			// JToolBarHelper::addNew('cancel', JText::_( 'VDATA_BACK' ), false );
			JToolBarHelper::custom('cancel', 'arrow-left', '', JText::_( 'VDATA_BACK' ), false);
			
		}
		
		else	{
		    JToolBarHelper::custom('truncate', 'trash', '', JText::_( 'TRUNCATE_LOG' ), false);
			
			JToolBarHelper::title( JText::_( 'VDATA_LOGS' ), 'list-2' );
		    $version = new JVersion;
			$joomla = $version->getShortVersion();
			$jversion = substr($joomla,0,3);
			$this->sidebar ='';
			if($jversion>=3.0)
			{
				$this->sidebar = JHtmlSidebar::render(); 
			}
			$this->logs = $this->get('Items');
			$this->profiles = $this->get('Profiles');
			$this->pagination = $this->get('Pagination');
						
			//$this->chart_type = $this->chart_type();
			
		}
							
		parent::display($tpl);
       
    }
	

  
  
}
