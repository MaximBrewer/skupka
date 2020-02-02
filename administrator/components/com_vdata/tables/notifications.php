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


class TableNotifications extends JTable
{
    /**
     * Primary Key
     *
     * @var int
     */
    var $id = null;
 
    /**
     * @var string
     */
    
    var $title = null;
	var $params = null;
    var $notification_tmpl = null;
    var $url = null;
    
    
    function TableNotifications( &$db ) {
        parent::__construct('#__vd_notifications', 'id', $db);
    }
	
	function bind($array, $ignore = '')
	{
		
		return parent::bind($array, $ignore);
		
	}
	
	function check()
	{
		
		if(empty($this->title))	{
			$this->setError( JText::_('PLZ_ENTER_TITLE') );
			return false;
		}
		
		$query_params = json_decode(json_encode($this->params));
		
		if( property_exists($query_params, 'filters') ){
			if(property_exists($query_params->filters, 'value')){
				foreach($query_params->filters->value as $key=>$value){
					$temp= str_replace('\'', '"', $value);
					$this->params->filters->value[$key] = $temp;
				}
			}
		}
		
		$this->params = json_encode($this->params);
		
		$this->notification_tmpl = json_encode($this->notification_tmpl);
		
		
		if(empty($query_params->custom->table) && empty($query_params->query)){
			$this->setError( JText::_('PLZ_SELECT_TABLE_OR_WRITE_CUSTOM_QUERY') );
			return false;
		}
		
		if(!empty($query_params->custom->table)){

			$query = "SELECT ";
			if(isset($query_params->custom->columns) && count($query_params->custom->columns)>0){
				foreach($query_params->custom->columns as $sidx=>$scolumn){
					$query .= $this->_db->quoteName($scolumn);
					if($sidx<(count($query_params->custom->columns)-1)){
						$query .= ", ";
					}
				}
			}
			else{
				$query .= " * ";
			}
			$query .= " FROM ".$this->_db->quoteName($query_params->custom->table);
			
			if(!empty($query_params->filters) && property_exists($query_params->filters, 'column')){
				
				$query .= " WHERE ";
				$m = $n = -1;
				foreach($query_params->filters->column as $j=>$column){
					
					$query .= $this->_db->quoteName($column)." ";
					
					if($query_params->filters->cond[$j]=='between' || $query_params->filters->cond[$j]=='notbetween'){
						$n = ($n<0)?0:$n+1;
					}
					else{
						$m = ($m<0)?0:$m+1;
						$value = $this->getQueryFilteredValue($query_params->filters->value[$m]);
					}
					
					if($query_params->filters->cond[$j]=='in'){
						$query .= " IN ( ".$this->_db->quote($value)." )";
					}
					elseif($query_params->filters->cond[$j]=='notin'){
						$query .= " NOT IN ( ".$this->_db->quote($value)." )";
					}
					elseif($query_params->filters->cond[$j]=='between'){
						$values = explode(',', $value);
						$value1 = $this->getQueryFilteredValue($values[0]);
						$value2 = $this->getQueryFilteredValue($values[1]);
						// $value1 = $this->getQueryFilteredValue($query_params->filters->value1[$n]);
						// $value2 = $this->getQueryFilteredValue($query_params->filters->value2[$n]);
						$query .= " BETWEEN ".$this->_db->quote($value1)." AND ".$this->_db->quote($value2);
					}
					elseif($query_params->filters->cond[$j]=='notbetween'){
						$values = explode(',', $value);
						$value1 = $this->getQueryFilteredValue($values[0]);
						$value2 = $this->getQueryFilteredValue($values[1]);
						// $value1 = $this->getQueryFilteredValue($query_params->filters->value1[$n]);
						// $value2 = $this->getQueryFilteredValue($query_params->filters->value2[$n]);
						$query .= " NOT BETWEEN ".$this->_db->quote($value1)." AND ".$this->_db->quote($value2);
					}
					elseif($query_params->filters->cond[$j]=='like'){
						$query .= " LIKE ".$this->_db->quote($value);
					}
					elseif($query_params->filters->cond[$j]=='notlike'){
						$query .= " NOT LIKE ".$this->_db->quote($value);
					}
					elseif($query_params->filters->cond[$j]=='regexp'){
						$query .= " REGEXP ".$this->_db->quote($query_params->filters->value[$j]);
					}
					else{
						$query .= $query_params->filters->cond[$j]." ".$this->_db->quote($value);
					}

					//sql operator
					if($j < (count($query_params->filters->column)-1)){
						$query .= $query_params->custom->clause;
					}
				}
			}
			
			if(isset($query_params->filters->additional) && !empty($query_params->filters->additional)){
				if(!empty($query_params->filters) && property_exists($query_params->filters, 'column')){
					$query .= " AND ".$query_params->filters->additional;
				}
				else{
					$query .= " WHERE ".$query_params->filters->additional;
				}
			}
			
			// if(!empty($query_params->custom->groupby)){
				// $query .= " GROUP BY ".$this->_db->quoteName($query_params->custom->groupby);
			// }
			if(!empty($query_params->custom->orderby)){
				$query .= " ORDER BY ".$this->_db->quoteName($query_params->custom->orderby)." ".$query_params->custom->orderdir;
			}
			
			try{
				$this->_db->setQuery($query,0, 1);
				$this->_db->execute();
				// $data = $this->_db->loadAssocList();
			}
			catch(Exception $e){
				$this->setError( $e->getMessage() );
				return false;
			}
		}
		else{
			if (preg_match("/^select (.*)/i", trim($query_params->query)) > 0){
				try{
					$this->_db->setQuery($query_params->query);
					$this->_db->loadObjectList();
				}
				catch(RuntimeException $e){
					$this->setError($e->getMessage());
					return false;
				}
			}
			else{
				$this->setError( JText::_('ONLY_SELECT_QUERY_ALLOWED') );
				return false;
			}
		}
		
		if($this->_db->getErrorNum())	{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		
		
		return parent::check();
		
	}
	
	function getQueryFilteredValue($value){
		
		$val = $value;
		$hd_sql_pattern = '/^@vdSql:(.*?)$/';
		$hd_php_pattern = '/^@vdPhp:(.*?)$/';
		if(preg_match($hd_sql_pattern, $value, $sqlmatches)){
			$val = $sqlmatches[1];
		}
		elseif(preg_match($hd_php_pattern, $value, $phpmatches)){
			$func = $phpmatches[1];
			$val = (eval("return $func;"));
		}
		
		return $val;
	}
	
	function store($updateNulls = false)
	{
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$date = JFactory::getDate();
		if($this->id){
			// $this->modified =  $date->toSql();//$date->toMySQL();
			// $this->modified_by = $user->id;
		}
		else{
			$this->created = $date->toSql();//$date->toMySQL();
			$this->created_by = $user->id;
		}
		if(!parent::store($updateNulls)){
			return false;
		}

		return true;
	
	}
	
	function delete($oid=null)
	{
		
		$this->id = $oid;
		
		$profile = $this->getProfile();
		
		if(empty($profile))	{
			$this->setError(JText::_('PROFILE_NOT_FOUND'));
			return false;
		}
		
		if(!parent::delete($oid))	{
			return false;
		}
		
	}
	
	
}
