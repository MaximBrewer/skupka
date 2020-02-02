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

jimport( 'joomla.application.component.model' );

class VdataModelQuick extends VDModel
{

	var $_list;
	var $_data = null;
	var $flag = null;
	var $_total = null;
	var $_pagination = null;
	function __construct()
	{
		parent::__construct();
		$mainframe = JFactory::getApplication(); 
		$option    = JFactory::getApplication()->input->getCmd('option'); 
		$context			= 'com_vdata.quick.list.';
		
        $this->filter_order     = $mainframe->getUserStateFromRequest( $option.'filter_order', 'filter_order', '', 'cmd' );
		
        $this->filter_order_Dir = $mainframe->getUserStateFromRequest( $option.'filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
        $this->table_name = $mainframe->getUserStateFromRequest($option.'table_name', 'table_name', '', 'string');
		$this->limit = $mainframe->getUserStateFromRequest($option.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$this->limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );
       
		$this->limitstart = ($this->limit != 0 ? (floor($this->limitstart / $this->limit) * $this->limit) : 0);
       
        $this->setState('limit', $this->limit);
        $this->setState('limitstart', $this->limitstart);
		$array = JFactory::getApplication()->input->get('cid',  0, '', 'array');
		
		$column_name = JFactory::getApplication()->input->get('column_name', '');
		
		$this->setId((int)$array[0],$column_name);
	

	}
	function getConnectExternalDatabase(){
		
		 $driver_object = json_decode($this->getVdatConfig());
		   if(isset($driver_object->local_db) && $driver_object->local_db==0){
			$option = array(); 
				try
				{
					$option['driver']   = $driver_object->driver;            
					$option['host']     = $driver_object->host;  
					$option['user']     = $driver_object->user;  
					$option['password'] = $driver_object->password; 
					$option['database'] = $driver_object->database; 
					$option['prefix']   = $driver_object->dbprefix;  
					$this->_db = JDatabaseDriver::getInstance( $option );
					$this->_db->connect();
					return true;
				
				}
				catch (RuntimeException $e)
				{                   
					$mainfram = JFactory::getApplication();
					$msg = JText::_($e->getMessage());
					$mainfram->redirect('index.php?option=com_vdata&view=config',$msg);
				
				}   
		   }
		
	}
	function setId($id,$column)
	{
		
		$this->_id		= $id;
		$this->_column	= $column;
		$this->_data	= null;
	}
	function getVdatConfig()
	{
		
        $query = 'select `dbconfig` from #__vd_config';
		$this->_db->setQuery($query);
		$config = $this->_db->loadObject();
		return $config->dbconfig;
	}
	function getColumninfo(){
		
		   $mainframe = JFactory::getApplication();
		  
		  $context    = JFactory::getApplication()->input->getCmd('option'); 


         		  
		 $this->table_name = str_replace('@', $this->_db->getPrefix(),JFactory::getApplication()->input->get('main_table_name', '')); 
		  //JFactory::getApplication()->input->get('table_name', ''); jexit();//
		 //str_replace('@', $this->_db->getPrefix(),JFactory::getApplication()->input->get('table_name', ''));
		 
		  $column_name = JFactory::getApplication()->input->get('column_name', ''); 
		  $response = array();
		  $columninfo = $columnvalue = array();//new stdClass();
		  
		  if($this->table_name!=''){
			$query = 'SHOW COLUMNS FROM '.$this->table_name;
		    $this->_db->setQuery( $query );
			$columninfo = $this->_db->loadObjectList();
			}
		
		 if($this->table_name!='' && $this->_id && $this->_column!=''){
			$query = ' SELECT * FROM '.$this->table_name.'  WHERE '.$this->_column.' = '.$this->_id;
			$this->_db->setQuery( $query );
			$columnvalue = $this->_db->loadObject();  
		  }
		 		
			
		   $response[0] = $columninfo;
		   $response[1] = $columnvalue;
		  
		  return $response;
		  
	}
	function optimization()
	{    $this->getConnectExternalDatabase();
		 $obj = new stdClass();
		 $obj->result = 'error';
		$table_for_optimization = JFactory::getApplication()->input->get('table_for','');
		$selected_table = str_replace('@', $this->_db->getPrefix(),JFactory::getApplication()->input->get('selected_table',''));
		if($table_for_optimization=='selected_table'){
		 $query = 'OPTIMIZE TABLE '.$selected_table;
		 $this->_db->setQuery($query)->query();
			
		}elseif($table_for_optimization=='all_tables'){
			$tables_optimization = $this->getTables();
			foreach($tables_optimization as $value){
			 $query = 'OPTIMIZE TABLE '.str_replace('@', $this->_db->getPrefix(),$value);
			  $this->_db->setQuery($query)->query();	
				
			}
			
		}
		 $obj->result = 'success';
		return $obj;
	}
	function repaired()
	{     $this->getConnectExternalDatabase();
		 $obj = new stdClass();
		 $obj->result = 'error';
		$table_for_optimization = JFactory::getApplication()->input->get('table_for','');
		$selected_table = str_replace('@', $this->_db->getPrefix(),JFactory::getApplication()->input->get('selected_table',''));
		if($table_for_optimization=='selected_table'){
		 $query = 'REPAIR TABLE '.$selected_table;
		 $this->_db->setQuery($query)->query();
			
		}elseif($table_for_optimization=='all_tables'){
			$tables_optimization = $this->getTables();
			foreach($tables_optimization as $value){
			 $query = 'OPTIMIZE TABLE '.str_replace('@', $this->_db->getPrefix(),$value);
			  $this->_db->setQuery($query)->query();	
				
			}
			
		}
		 $obj->result = 'success';
		return $obj;
	}
	function getTables()
	{       $this->getConnectExternalDatabase();//jexit('zaheerw');
			$mainframe = JFactory::getApplication();
			$context	= 'com_vdata.quick.list.';
			$table_name = str_replace('@', $this->_db->getPrefix(), $mainframe->getUserStateFromRequest($context.'table_name', 'table_name', '', 'string'));
			$sql_query = $mainframe->getUserStateFromRequest($context.'sql_query', 'sql_query', '', 'string');
			if(!empty($sql_query))
			{
			$table_name = "";	
			}
		  $query = 'show tables';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadColumn();
		
		
		$items = $this->getMapCategories('table_name', $table_name, 0, "onchange='document.adminForm.filter_order.value=\"\"; jQuery(\"#search\").val(\"\");'");
		
		return $items;
		
	}
	function _buildQuery()
	{
		  $mainframe = JFactory::getApplication();
		  $context			= 'com_vdata.quick.list.';
		
		  $this->table_name = str_replace('@', $this->_db->getPrefix(), $mainframe->getUserStateFromRequest($context.'table_name', 'table_name', '', 'string'));
		 
		  $this->sql_query = $mainframe->getUserStateFromRequest($context.'sql_query', 'sql_query', '', 'RAW'); 
		  
		 
         //$filter_order     = JFactory::getApplication()->input->get('filter_order', '');
		 $filter_order = $mainframe->getUserStateFromRequest($context.'filter_order', 'filter_order', '', 'word' );
         $filter_order_Dir = $mainframe->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
 
         $orderby = $filter_order!=''?' order by '.$filter_order.' '.$filter_order_Dir . ' ':'';
        
		 $query ='';
		 if(!empty($this->sql_query)){
			 
			
			   if(strpos(strtolower($this->sql_query), 'show')!==false){$orderby='';}
			   if(strpos(strtolower($this->sql_query), 'insert')!==false){$orderby='';}
			   if(strpos(strtolower($this->sql_query), 'update')!==false){$orderby='';}
			   if(strpos(strtolower($this->sql_query), 'delete')!==false){$orderby='';}
			   if(strpos(strtolower($this->sql_query), 'order by')!==false){$orderby='';}
			  if(strpos(strtolower($this->sql_query), 'limit')!==false){
				$query = substr(strtolower($this->sql_query), 0, strpos(strtolower($this->sql_query),"limit")).$orderby. substr(strtolower($this->sql_query), strpos(strtolower($this->sql_query),"limit"));
				   
				   }
			else  
			   $query = $this->sql_query.''.$orderby;
		 }
		
	     elseif(!empty($this->table_name)){
			 
			 
			 
			 $query = 'select * from '.$this->table_name.''.$orderby; 
		 }
		//echo $query; jexit();	
		
		return $query;
	}
	
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			preg_match_all("/(insert |update |alter |delete |drop |references |create temporary tables |lock tables |create view |create route |alterroute |event |trigger |create |drop |truncate )/", strtolower($query), $matches);
            
			if(count($matches[1])<1){ 
			if(!empty($query))
			$this->_total = $this->_getListCount($query);
		}
		}

        return $this->_total;
	}

	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			
			$query = $this->_buildQuery();
			preg_match_all("/(insert |update |alter |delete |drop |references |create temporary tables |lock tables |create view |create route |alterroute |event |trigger |create |drop |truncate )/", strtolower($query), $matches);
			if(count($matches[1])<1){ 
			if(!empty($query))
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
        }
		return $this->_pagination;
	}
	function getTable_data(){
		    $this->getConnectExternalDatabase();
		     $query = $this->_buildQuery();
			$response = $key = $column_details = array();
			$table_name_from_query='';
			preg_match_all("/(insert |update |alter |delete |drop |references |create temporary tables |lock tables |create view |create route |alterroute |event |trigger |create |drop |truncate )/", strtolower($query), $matches);
			
			if(count($matches[1])>=1){
				
				try{
				 
				$this->_db->setQuery($query);
				$this->_db->query();
				JFactory::getApplication()->enqueueMessage(JText::_('COM_VDATA_QUERY_EXECUTION_MESSAGE'));
				}
			catch (RuntimeException $e){
					$response[0]='Error';
					$response[1]=$e->getMessage();
					return $response;
				}
				
				
				
				}
			 
		else{
			
			
			if(!empty($query)){
			if (strpos(strtolower($query),'join') == false) {
			
         
			if(strpos(strtolower($query), 'from ')){
		    $table_name_from_query = strpos(strtolower($query), 'from ');
			
			$table_name_from_query = substr(($query), $table_name_from_query+5);
			
			$this->table_name = $table_name_from_query = strpos($table_name_from_query, ' ')!=''?substr(($table_name_from_query), 0, strpos($table_name_from_query, ' ')):$table_name_from_query; 
			$table_name_from_query = str_replace('@', $this->_db->getPrefix(), $table_name_from_query);
			$query_table ="SHOW INDEXES FROM ".$table_name_from_query." WHERE Key_name IN('PRIMARY', 'Unique')";
			try{
			$this->_db->setQuery($query_table);
			$key = $this->_db->loadObject();
			}
			catch (RuntimeException $e){
					$response[0]='Error';
					$response[1]=$e->getMessage();
					return $response;
			}
			}
			} 
            try{
			$this->_db->setQuery($query);
			$total = $this->_db->loadObjectList();
			 }
			catch (RuntimeException $e){
					$response[0]='Error';
					$response[1]=$e->getMessage();
					return $response;
			}
			if(strpos(strtolower($query), 'show')!==false){
				try{
				$this->_db->setQuery($query);
				$this->_data = $this->_db->loadObjectlist();
				$this->setState('limit', count($this->_data));
				}
			catch (RuntimeException $e){
					$response[0]='Error';
					$response[1]=$e->getMessage();
					return $response;
			}
			}
			else{
			try{
			 if(strpos(strtolower($query), 'limit')!==false){
			  $this->_data = $this->_getList($query);	 
			 }else{
			  $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit')); 
			 }	
			 
		   }
			catch (RuntimeException $e){
					$response[0]='Error';
					$response[1]=$e->getMessage();
					return $response;
			}
				
			}
			
			   if(strpos(strtolower($query), '#')==false)
			   {
				$this->table_name = ''. $this->table_name;  
			   }
		   
			   if($this->table_name!='')
			   {
					$query_table ='SHOW COLUMNS FROM '.$this->table_name;
					try
					{
					$this->_db->setQuery($query_table);
					$column_details = $this->_db->loadObjectList();
					}
					catch (RuntimeException $e){
							$response[0]='Error';
							$response[1]=$e->getMessage();
							return $response;
					}
				}
			
			}
			}
		
		$response[0] = $key;
		$response[1] = $this->_data;
		$response[2] = $table_name_from_query;
		$response[3] = $column_details; 
		$response[4] = $this->getTotal();
		
		return $response;
	}
	function getMapCategories($var, $default, $disabled, $event){
		 $this->getConnectExternalDatabase();
		$option = '';
		$query = 'show tables';
		$this->_db->setQuery($query);
		$tables  = $this->_db->loadColumn();
		$types[] = JHTML::_('select.option', '', JText::_('Select Table'));
		for($i=0;$i<count($tables);$i++){
			
			$table = str_replace($this->_db->getPrefix(), '#__', $tables[$i]);
			
			/* if(strpos(strtolower($table), '#')!==false)
			$table_options = str_replace($db->getPrefix(), '@', $tables[$i]);
		    else */
			$table_options = $tables[$i];
			//$table_options = str_replace($db->getPrefix(), '', $tables[$i]);
			
		$types[] = JHTML::_('select.option', $table_options, JText::_($table));
	    }
		if($disabled == 1)
			$option = 'disabled';
	
		
	
		$lists 	 = JHTML::_('select.genericlist', $types, $var, "class='inputbox' size='1' $option $event", 'value', 'text', $default);
		
		return $lists;
		
		}
	function getProfile()
	{
		
		$id = JFactory::getApplication()->input->getInt('profileid', 0);
		
		$query = 'select i.*, e.element as plugin from #__vd_profiles as i join #__extensions as e on i.pluginid=e.extension_id where i.id = '.$id;
		$this->_db->setQuery( $query );
		$item = $this->_db->loadObject();
		
		return $item;
		
	}
	
	function getProfiles()
	{
		
		$query = 'select * from #__vd_profiles order by title asc';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		
		return $items;
		
	}
	
	function get_csv_fields()
	{
		
		$dir = JPATH_ADMINISTRATOR.'/components/com_vdata/uploads/data.csv';
		
		jimport('joomla.filesystem.file');
		
		$file = JFactory::getApplication()->input->get("file", null, 'FILES', 'array');
	
		$file_name    = str_replace(' ', '', JFile::makeSafe($file['name']));		
		$file_tmp     = $file["tmp_name"];
	
		$ext = strrchr($file_name, '.');
		
		if(filesize($file_tmp) == 0 and is_file($dir))	{
			return true;
		}
	
		if(filesize($file_tmp) == 0)	{
			$this->setError(JText::_('PLZ_SELECT_FILE'));
			return false;
		}
		
		if($ext <> '.csv')	{
			$this->setError(JText::_('ONLY_CSV'));
			return false;
		}
		
		if(!move_uploaded_file($file_tmp, $dir))	{
			$this->setError(JText::_('FILE_NOT_UPLOADED'));
			return false;
		}
		
		return true;
		
	}
	function delete()
	{
		$this->getConnectExternalDatabase();
		$cids = array();
        $cids[0] = $delete_id = JFactory::getApplication()->input->get( 'delete_id','');
		if($delete_id=='')
		$cids = JFactory::getApplication()->input->get( 'cid', array(0), 'post', 'array' );
		$row = $this->getTable('Quick', 'Table');
		 
		if (count( $cids )) 
		{
			foreach($cids as $cid) {
				$did = explode(':',$cid);
				 if (!$row->delete( $did[0] )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}  
			}						
		}
		return true;
	}
  function store()
	{  
		    $this->getConnectExternalDatabase();
			$row = $this->getTable('Quick', 'Table');
		    $data = JFactory::getApplication()->input->post->getArray();
		    $query_table ='SHOW COLUMNS FROM '.str_replace('@', $this->_db->getPrefix(),$data['table_name']);
			$this->_db->setQuery($query_table);
			$column_details = $this->_db->loadObjectList();
			
			for($i=0;$i<count($column_details);$i++){
				$colum_obj = $column_details[$i];
				if($colum_obj->Type == 'blob'){$maxsize = 64;}
				elseif($colum_obj->Type == 'longblob'){}
				elseif($colum_obj->Type == 'mediumblob'){}
				elseif($colum_obj->Type == 'tinyblob'){}
				if($colum_obj->Type=='blob' || $colum_obj->Type == 'longblob' || $colum_obj->Type == 'mediumblob' || $colum_obj->Type == 'tinyblob'){
				$file = JFactory::getApplication()->input->get($colum_obj->Field, null, 'FILES', 'array');	
				if(!empty($file['name']))
				$data[$colum_obj->Field] = file_get_contents($file['tmp_name']);	
					
				}
				if(strtolower($colum_obj->Type) == 'mediumtext' || strtolower($colum_obj->Type) == 'tinytext' || strtolower($colum_obj->Type) == 'mediumtext' ||strtolower($colum_obj->Type) =='longtext'){
				$data[$colum_obj->Field] = JFactory::getApplication()->input->get($colum_obj->Field, '', 'RAW');
				}
				
				
			}
		$primarycolumn = JFactory::getApplication()->input->get('primarycolumn', array(), 'post', 'array');
        $column_name = $primarycolumn[0];
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
       
		
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		try{
		if (!$row->store()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		
		return $row->$column_name;
		}catch (RuntimeException $e){
			
			return $e->getMessage();
		}
	}

}
