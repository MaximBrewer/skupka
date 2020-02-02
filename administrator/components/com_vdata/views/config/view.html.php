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
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die(); 

class VdataViewConfig extends VDView
{
    
    function display($tpl = null)
    {
		$user  = JFactory::getUser();
		
		$document =  JFactory::getDocument();
		$this->item = $this->get('Item');
		JToolBarHelper::title( JText::_( 'Configuration' ), 'equalizer' );
		JToolBarHelper::apply('save', JText::_('Save'));
		JToolBarHelper::cancel('close');
		JToolBarHelper::help('help', true);
		
		if($user->authorise('core.admin', 'com_vdata'))
			JToolBarHelper::preferences('com_vdata', '', '', 'ACL');
		$version = new JVersion;
			$joomla = $version->getShortVersion();
			$jversion = substr($joomla,0,3);
			$this->sidebar ='';
			if($jversion>=3.0)
			{
			$this->sidebar = JHtmlSidebar::render();
			}
		parent::display($tpl);
		        
    }
  
  
}
