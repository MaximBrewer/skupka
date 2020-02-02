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

class VdataViewVdata extends JViewLegacy
{
    
    function display($tpl = null)
    {
		
		$document =  JFactory::getDocument();
		$mainframe = JFactory::getApplication();
		$context			= 'com_vdata.vdata.list.';
		$layout = JFactory::getApplication()->input->getCmd('layout', '');
		
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
		$bar = JToolBar::getInstance('toolbar');
		
		JToolBarHelper::title( JText::_( 'vData - Dashboard' ), 'dashboard' );
		
		$version = new JVersion;
			$joomla = $version->getShortVersion();
			$jversion = substr($joomla,0,3);
			$this->sidebar ='';
			if($jversion>=3.0)
			{
			$this->sidebar = JHtmlSidebar::render();
			}
			$user  = JFactory::getUser();
			if($layout !='information')
			{
				JToolBarHelper::help('help', true);
				if($user->authorise('core.admin', 'com_vdata'))
				JToolBarHelper::preferences('com_vdata', '', '', 'ACL');
				if($user->authorise('core.widget', 'com_vdata'))
				JToolBarHelper::addNew('insert', JText::_( 'VDATA_ADD_WIDGET' ), false );
			}
			
		
		     $this->items = $this->get('Items');
			 $this->configuration = $this->get('Configuration');
			 $this->profiles = $this->get('Profiles');
			 $this->profiles_name = $this->get('Profilesname');
			 $this->plugins = $this->get('Plugins');
		     $this->lists['order_Dir'] = $filter_order_Dir;
			 $this->lists['order']     = $filter_order;		
		     parent::display($tpl);
		        
    }
  function log_information($q,$response_data){
	  $query = '';
	  $opton_value = json_decode($q->detail);
	  $db = JFactory::getDbo();
	  for($v=0;$v<count($response_data);$v++){
		  $response_datas = $response_data[$v];
		  if($opton_value->existing_database_table==$response_datas->label)
			  $query =$response_datas->value;
		  
	  }
	  if(!empty($query)){
		        preg_match_all('/{tablename\s(.*?)}/i', $query, $matches);
                preg_match_all('/{as\s(.*?)}/i', $query, $match);
				$matches_s = $matches[1];
				$text = $query;
				for($r=0;$r<count($matches_s);$r++){
				$text = preg_replace('{tablename '.$matches_s[$r].'}', $matches_s[$r], $text, 1);	
				}
				 $query = str_replace('{','',str_replace('}','',$text)); 
	   
	   $sql = $query;
	   $db->setQuery($sql);
	   $datas = $db->loadObjectList();
	   $html = '<table class="adminlist table table-hover" width="100%"><th>No</th><th>Table Name</th><th>Message</th>';
	   for($v=0;$v<count($datas)&&$v<6;$v++){
		   $data = $datas[$v];
		  $html .= '<tr><td>'.$data->id.'</td><td>'.$data->table.'</td><td>'.$data->message.'</td><tr>'; 
	   }
	   $html .= '</table>';
	   
	   return $html;
	   }
	  
  }
  
}
