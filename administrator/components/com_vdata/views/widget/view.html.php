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

class VdataViewWidget extends JViewLegacy
{    
    function display($tpl = null)
    { 
		
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		$context			= 'com_vdata.widget.list.';
		$layout = JFactory::getApplication()->input->getCmd('layout', '');
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', '', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
		$sql_query = $mainframe->getUserStateFromRequest($context.'sql_query', 'sql_query', '', 'string');
		$table_name = $mainframe->getUserStateFromRequest($context.'table_name_old', 'table_name', '', 'string');
		    $document->addScript(JURI::root().'media/com_vdata/js/query-builder.js');
			$document->addScript(JURI::root().'media/com_vdata/js/moment.js');
			$document->addScript(JURI::root().'media/com_vdata/js/jquery-ui-timepicker-addon.js');
			$document->addScript(JURI::root().'media/com_vdata/js/query-builder-sql-support.js');
			$document->addStyleSheet(JURI::root().'media/com_vdata/css/query-builder.css');
            $document->addStyleSheet(JURI::root().'media/com_vdata/css/jquery-ui-timepicker-addon.min.css');
			$document->addScript(JURI::root().'media/com_vdata/js/mini_color.js');
			$document->addStyleSheet(JURI::root().'media/com_vdata/css/mini_color.css');
		if($layout=="update")	{
			
			  
			
			JToolBarHelper::title(JText::_( 'VDATA_ADD_NEW_WIDGET' ), 'quickview' );
			
			
			JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolBarHelper::cancel('close');
		
		}
		
		else	{
			
			JToolBarHelper::title( JText::_( 'VDATA_WIDGET' ), 'quickview' );
			$this->widget	= $this->get('Item');
			JToolBarHelper::apply();
			//$this->chart_type = $this->chart_type();
			
		}
							
		parent::display($tpl);
       
    }
	

  
  
}
