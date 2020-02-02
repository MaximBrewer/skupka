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

class VdataViewQuick extends JViewLegacy
{    
    function display($tpl = null)
    {
		
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		
		$context			= 'com_vdata.quick.list.';
		$layout = JFactory::getApplication()->input->getCmd('layout', '');
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', '', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
		$sql_query = $mainframe->getUserStateFromRequest($context.'sql_query', 'sql_query', '', 'RAW');
		$table_name = $mainframe->getUserStateFromRequest($context.'table_name', 'table_name', '', 'string');
		
		if($layout=="update")	{
			
			$document->addScript(JURI::root(true).'/media/com_vdata/js/jquery.ui.datepicker.js');
			$document->addScript(JURI::root(true).'/media/com_vdata/js/jquery.datetimepicker.js');
			$document->addStyleSheet(JURI::root(true).'/media/com_vdata/css/jquery.ui.theme.css');
			$document->addStyleSheet(JURI::root(true).'/media/com_vdata/css/jquery.ui.datepicker.css');
			$document->addStyleSheet(JURI::root(true).'/media/com_vdata/css/jquery.datetimepicker.css'); 
			$database_connectivity = $this->get('ConnectExternalDatabase');
			
			
           $data = $this->get('Columninfo');
           $this->rowinfo = $data[0];	
           $this->rowvalue = $data[1];		   
			
			JToolBarHelper::title(JText::_( 'VDATA_PHP_MY_ADMIN_UPDATE' ), 'cube' );
			
			
			JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolBarHelper::cancel('close');
		
		}
		elseif($layout=="export"){
			
			JToolBarHelper::title(JText::_( 'VDATA_PHP_MY_ADMIN_EXPORT' ), 'out-2' );
			
		}
		else	{
		$document->addScript(JURI::root().'media/com_vdata/js/conform.js');	
			JToolBarHelper::title( JText::_( 'VDATA_PHP_MY_ADMIN' ), 'cube' );
			
			JToolBarHelper::addNew('add', JText::_( 'Insert' ), false );
			JToolBarHelper::deleteList(JText::_('DTITEM'));
			JToolBarHelper::custom( 'export', 'out-2', 'out-2', JText::_( 'Export' ), false );
			JToolBarHelper::custom( 'optimise', 'file', 'file', JText::_( 'Optimise' ), false );
			JToolBarHelper::custom( 'repair', 'ok', 'ok', JText::_( 'Repair' ), false );
			$version = new JVersion;
			$joomla = $version->getShortVersion();
			$jversion = substr($joomla,0,3);
			$this->sidebar ='';
			if($jversion>=3.0)
			{
			$this->sidebar = JHtmlSidebar::render();
			}
			$this->test_error= '';
			
			$response = $this->get('Table_data');
			
			
			if($response[0]=='Error'){ 
			$this->test_error = $response[0];
			$this->error_message = $response[1];
		    }
			else{
			
			$this->pagination = $this->get('Pagination');
			$this->edit_functionality = $response[0];
			$this->table_data = $response[1];
			$this->column_details = $response[3];
			$this->total_records = $response[4];
			$this->lists['main_table_name']     = $response[2];	
			
			}
		
			$this->tables = $this->get('Tables');
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;
		    $this->lists['sql_query']     = $sql_query;
			$this->lists['table_name']     = $table_name;
		}
							
		parent::display($tpl);
        
    }
  
  
}
