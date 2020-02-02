	<?php
	/*------------------------------------------------------------------------
	# vData Custom Plugin
	# ------------------------------------------------------------------------
	# author    Team WDMtech
	# copyright Copyright (C) 2015 www.wdmtech.com. All Rights Reserved.
	# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
	# Websites: http://www.wdmtech.com
	# Technical Support:  Forum - http://www.wdmtech.com/support-forum
	-----------------------------------------------------------------------*/
	// No direct access
	defined('_JEXEC') or die('Restricted access');

	require(JPATH_ADMINISTRATOR.'/components/com_vdata/classes/plgvdata.php');

	// Include the JLog class.
	jimport('joomla.log.log');
	jimport('joomla.filesystem.file');
	/**
	 * Joomla! vData Plugin
	 *
	 * @package		Joomla
	 * @subpackage	Plugin
	 */

	class  plgvdataCustom extends plgvdata
	{
		public function getProfile()
		{
			parent::getProfile();
			$this->profile->title = isset($this->profile->title) ? $this->profile->title : null;
			$this->profile->params->table = isset($this->profile->params->table)?$this->profile->params->table:null;
			$this->profile->params->filters = isset($this->profile->params->filters)?$this->profile->params->filters:array();
			$this->profile->params->fields = isset($this->profile->params->fields)?$this->profile->params->fields:array();
			$this->profile->params->joins = isset($this->profile->params->joins)?$this->profile->params->joins:array();
			
			// initialize the logger
			$logger = $this->initializeLogger($this->profile->id);
			//$this->overridePhpConfig();
		}
		
		function initializeLogger($profileid=0){
			
			$config = $this->getConfig();
			if(!$config->logging){
				return false;
			}
			
			$date = JFactory::getDate()->format('Y-m-d');
			$session = JFactory::getSession();
			$op_start = $session->get('op_start', $date);
			//'m-d-Y-His A e'//'m-d-Y_hia'
			$op_start = JFactory::getDate($op_start)->format('m-d-Y_hia');
			// $log_file = 'com_vdata_plg_custom'.$profileid.'.php';
			$log_file = 'com_vdata_plg_custom';
			if($profileid){
				$log_file .= $profileid;
			}
			$log_file .= '_'.$op_start.'.txt';
			
			$session->set('logfile', $log_file);
			JLog::addLogger(
				array(
				'text_file' => $log_file,//text_file_path
				'text_file_no_php' => 'false'
				),
				JLog::ALL,
				array('com_vdata')
			);
			return true;
		}
		
		function overridePhpConfig(){
			
			$config = $this->getConfig();
			$phpSettings = $config->php_settings;
			if(isset($phpSettings->max_execution) && !empty($phpSettings->max_execution) && is_numeric($phpSettings->max_execution)){
				ini_set('max_execution_time', $phpSettings->max_execution);
			}
			elseif(isset($phpSettings->max_memory) && !empty($phpSettings->max_memory) && is_numeric($phpSettings->max_memory)){
				ini_set('memory_limit', $phpSettings->max_memory);
			}
			elseif(isset($phpSettings->max_post) && !empty($phpSettings->max_post) && is_numeric($phpSettings->max_post)){
				ini_set('post_max_size', $phpSettings->max_post);
			}
			elseif(isset($phpSettings->max_upload) && !empty($phpSettings->max_upload) && is_numeric($phpSettings->max_upload)){
				ini_set('upload_max_filesize', $phpSettings->max_upload);
			}
			
		}
		
		//fetches the tables in database
		protected function getTables()
		{
			$db = $this->getDbc();
			// $query = $db->getQuery(true);
			$query = 'show tables';
			$db->setQuery( $query );
			$items = $db->loadColumn();
			return $items;
		}
		
		function onEditProfile()
		{
			$db = $this->getDbc();
			$dbprefix = $db->getPrefix();
			$lang = JFactory::getLanguage();
			$lang->load('plg_vdata_custom', JPATH_SITE.'/plugins/vdata/custom');
			
			//check whether it is remote profile
			$remote = JFactory::getApplication()->input->getInt('remote', 1);
			if($remote==1){
				$this->profile = new stdClass();
				$params = JFactory::getApplication()->input->get('params', '', 'ARRAY');			
				$this->profile->id = 0;
				$this->profile->params = json_decode(json_encode($params));
				$this->profile->iotype = JFactory::getApplication()->input->getInt('iotype', 0);
			}
			else{
				$this->getProfile();
			}
			
			$tables = $this->getTables();
			$columns=array();
			if(!empty($this->profile->params->table))
			{
				$query = 'show fields FROM '.$db->quoteName($this->profile->params->table);
				$db->setQuery( $query );
				if($db->getErrorNum())	{
					throw new Exception($db->getErrorMsg());
					return false;
				}
				$fields = $db->loadObjectList();
				foreach($fields as $field)
				{
					array_push($columns,$field->Field);
				}
				if($this->profile->iotype==0){
					$component_tables = '';
					if(isset($this->profile->params->component->value) && !empty($this->profile->params->component->value)){
						$selected_table = (isset($this->profile->params->component->table) && !empty($this->profile->params->component->table))?$this->profile->params->component->table:'';
						$component_tables .= $this->getComponentTables($this->profile->params->component->value, $selected_table);
					}
					else{
						$component_tables .= '<option value="">'.JText::_('SELECT_COMPONENT_TABLE').'</option>';
					}
				}
			}
			require(dirname(__FILE__).'/tmpl/edit_profile.php');
			jexit();
		}
		
		function load_fields()
		{
			JSession::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALID_TOKEN').'"}' );
			$obj = new stdClass();
			$db = JFactory::getDbo();
			$table = JFactory::getApplication()->input->get('table', '', 'RAW');
			$query = 'show fields FROM '.$table;
			$db->setQuery( $query );
			if($db->getErrorNum())	{
				throw new Exception($db->getErrorMsg());
				return false;
			}
			$columns = $db->loadObjectList();
			$obj->fields = array();
			for($i=0;$i<count($columns);$i++)	{
				$field = $columns[$i]->Field;            
				array_push($obj->fields, $field);
			}
			$query = 'SHOW KEYS FROM '.$db->quoteName($table).' WHERE Key_name = "PRIMARY"';
			$db->setQuery($query);
			$keys = $db->loadColumn(4);
			$obj->primary = $keys;
			
			$obj->result = 'success';
			jexit(json_encode($obj));
		}
		
		//get fields of a particular table
		function load_columns()
		{
			JSession::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALID_TOKEN').'"}' );
			$obj = new stdClass();
			$db = $this->getDbc();
			$dbprefix = $db->getPrefix();  
			$lang = JFactory::getLanguage();
			$lang->load('plg_vdata_custom', JPATH_SITE.'/plugins/vdata/custom');
			
			$remote = JFactory::getApplication()->input->getInt('remote', 1);
			if($remote==1){
				
				$this->profile = new stdClass();
				$params = JFactory::getApplication()->input->get('params', '', 'ARRAY');
				// $this->profile->id = 0;
				$this->profile->params = json_decode(json_encode($params));
			}
			else{
				$this->getProfile();
			}
			
			$table = JFactory::getApplication()->input->get('table', '', 'RAW');
			$base_table = $table;
			$this->profile->iotype = JFactory::getApplication()->input->getInt('iotype', 0);
		   $operation = JFactory::getApplication()->input->getInt('operation', 0);
			
			$obj->html = $fhtml = '';
			$obj->columns = array();
			$importFilter = ( ($this->profile->iotype==0) && ($operation==3) )?true:false;
			
			$tables = $this->getTables();
			
			$query = 'show fields FROM '.$db->quoteName($table);
			$db->setQuery( $query );
			
			if($db->getErrorNum())	{
				throw new Exception($db->getErrorMsg());
				return false;
			}
			$columns = $db->loadObjectList();
			
			$query = $db->getQuery(true);
			$query = 'SHOW KEYS FROM '.$db->quoteName($table).' WHERE Key_name = "PRIMARY"';
			$db->setQuery($query);
			// $key = $db->loadObjectList();
			$key = $db->loadAssocList('Column_name');
			if( !empty($key) ){//&& (count($key)==1) && !empty($key[0]->Column_name)
				// $primary = $key[0]->Column_name;
				$primary = array_keys($key);
			}
			else{
				$primary = null;
			}
			
			$obj->html .= '<tr class="columns"><td colspan="2">'.JText::_('COLUMNS').'</td></tr>';
			
			for($i=0;$i<count($columns);$i++)	{
				$item = null;                
				$column = $columns[$i]->Field;                
				array_push($obj->columns, $column);
				if(isset($this->profile->params->fields->$column))	{
					$field = $this->profile->params->fields->$column;
					$field->data=isset($field->data)?$field->data:'skip';
				}
				else{
					$field = new stdClass();
					$field->data = 'skip';
				}	
				
				$obj->html .= '<tr data-column="'.$column.'"';
				if($columns[$i]->Key=="PRI")	{
				$obj->html .= 'class="primaryKey"';
				
				if( isset($this->profile->params->operation) && ($this->profile->params->operation==1) && isset($this->profile->params->component->value) && !empty($this->profile->params->component->value) && $operation==1){
					$obj->html .='style="display:none;"';
					}
				}
				
				$obj->html .= '><td width="200"><label class="hasTip';
				if($columns[$i]->Key=="PRI")	{
					$obj->html .= ' primary';
				}
				$obj->html .= '">'.$column.'</label></td><td>';				
				$obj->html .= $this->loadFieldOptions($field, $column);
				$obj->html .= '</td></tr>';
			}
			if( ($this->profile->iotype==1) || ($importFilter) )   {
				$filters = property_exists($this->profile->params, 'filters') ? $this->profile->params->filters : array();
				if(!empty($filters)) {
					$filters->op = isset($filters->op)? $filters->op : null;
					$fhtml .= '<tr class="filters"><td colspan="2">'.JText::_('FILTERS');
					$fhtml .= ' <select name="params[filters][op]"><option value="and"';
					if($filters->op=="and")
						$fhtml .= ' selected="selected"';
					$fhtml .= '>AND</option>';
					$fhtml .= '<option value="or"';
					if($filters->op=="or")
						$fhtml .= ' selected="selected"';
					$fhtml .= '>OR</option>';
					$fhtml .= '<option value="xor"';
					if($filters->op=="xor")
						$fhtml .= ' selected="selected"';
					$fhtml .= '>XOR</option>';
				}
				else{
					$fhtml .= '<tr class="filters"><td colspan="2">'.JText::_('FILTERS');
					$fhtml .= ' <select name="params[filters][op]"><option value="and"';
					$fhtml .= '>AND</option>';
					$fhtml .= '<option value="or"';
					$fhtml .= '>OR</option>';
					$fhtml .= '<option value="xor"';
					$fhtml .= '>XOR</option>';
				}
				$fhtml .= '</select> <span class="add_filter btn btn-success"><i class="icon-new"></i> '.JText::_('ADD_FILTER').'</span></td></tr>'
						.'<tr><td colspan="2"><table class="filter_table adminform table table-striped col_block"><tbody>';
				
				if(isset($filters->column)) :
				
				//base reference tables
				$baseRefTables = array();
				$baseRefColumns = array();
				foreach($this->profile->params->fields as $filterCol=>$filterVal){
					if($filterVal->data=='reference'){
						if(array_key_exists($filterVal->table, $baseRefTables)){
							$baseRefTables[$filterVal->table] += 1;
						}
						else{
							$baseRefTables[$filterVal->table] = 1;
							
							$query = 'show fields FROM '.$db->quoteName($filterVal->table);
							$db->setQuery( $query );
							if($db->getErrorNum())	{
								throw new Exception($db->getErrorMsg());
								return false;
							}
							$baseRefColumns[$filterVal->table] = $db->loadObjectList();
						}
					}
				}
				
				$childTables = array();
				$childTblColumns = array();
				$childRefTbl = array();
				if(isset($this->profile->params->joins) && !empty($this->profile->params->joins->table2)){
					//child tables
					foreach($this->profile->params->joins->table2 as $filterInx=>$filterTbl){
						if(array_key_exists($filterTbl, $childTables)){
							$childTables[$filterTbl] += 1; 
						}
						else{
							$childTables[$filterTbl] = 1;
							
							$query = 'show fields FROM '.$db->quoteName($filterTbl);
							$db->setQuery( $query );
							if($db->getErrorNum())	{
								throw new Exception($db->getErrorMsg());
								return false;
							}
							$childTblColumns[$filterTbl] = $db->loadObjectList();
						}
					}
					//child reference tables
					foreach($this->profile->params->joins->columns as $pidx=>$tblCols){
						$childRefTbl[$this->profile->params->joins->table2[$pidx]]['table']=array();
						$childRefTbl[$this->profile->params->joins->table2[$pidx]]['columns']=array();
						foreach($tblCols as $filterCol=>$filterVal){
							if($filterVal->data=='reference'){
								
								if( array_key_exists($filterVal->table, $childRefTbl[$this->profile->params->joins->table2[$pidx]]['table']) ){
									$childRefTbl[$this->profile->params->joins->table2[$pidx]]['table'][$filterVal->table] += 1;
								}
								else{
									$childRefTbl[$this->profile->params->joins->table2[$pidx]]['table'][$filterVal->table] = 1;
									$query = 'show fields FROM '.$db->quoteName($filterVal->table);
									$db->setQuery( $query );
									if($db->getErrorNum())	{
										throw new Exception($db->getErrorMsg());
										return false;
									}
									$childRefTbl[$this->profile->params->joins->table2[$pidx]]['columns'][$filterVal->table] = $db->loadObjectList();
								}
							}
						}
					}
				}
				$m = $n = -1;
				for($j=0;$j<count($filters->column);$j++):
					$fhtml .= '<tr><td colspan="2">';
					$fhtml .= '<span class="filter_block"><select name="params[filters][column][]">';
					//base table columns filter group
					$fhtml .= '<optgroup label="'.$table.'">';
					foreach($obj->columns as $fcol){
						$fhtml .= '<option value="'.$fcol.'"';
						if($fcol==$filters->column[$j])  {
							$fhtml .= ' selected="selected"';
						}
						$fhtml .= '>'.$fcol.'</option>';
					}
					$fhtml .= '</optgroup>';
					//base table reference columns filter group
					if(!empty($baseRefTables)){
						foreach($baseRefTables as $refTbl=>$refCount){
							$fhtml .= '<optgroup label="ref:'.$refTbl.'" data-count="'.$refCount.'">';
							if(isset($baseRefColumns[$refTbl])){
								foreach($baseRefColumns[$refTbl] as $refCol){
									$fhtml .= '<option value="'.$refTbl.'.'.$refCol->Field.'"';
									if($filters->column[$j]==($refTbl.'.'.$refCol->Field)){
										$fhtml .= ' selected="selected"';
									}
									$fhtml .= '>'.$refCol->Field.'</option>';
								}
							}
							$fhtml .= '</optgroup>';
						}
					}
					//child table columns filter group
					if(!empty($childTables)){
						foreach($childTables as $childTbl=>$childCount){
							$fhtml .= '<optgroup label="child:'.$childTbl.'" data-count="'.$childCount.'">';
							if(isset($childTblColumns[$childTbl])){
								foreach($childTblColumns[$childTbl] as $refCol){
									$fhtml .= '<option value="'.$table.'.'.$childTbl.'.'.$refCol->Field.'"';
									if($filters->column[$j]==($table.'.'.$childTbl.'.'.$refCol->Field)){
										$fhtml .= ' selected="selected"';
									}
									$fhtml .= '>'.$refCol->Field.'</option>';
								}
							}
							$fhtml .= '</optgroup>';
						}
					}
					//child table reference columns filter group
					if(!empty($childRefTbl)){
						foreach($childRefTbl as $childTbl=>$childData){
							foreach($childData['table'] as $refTbl=>$childRefCount){
								$fhtml .= '<optgroup label="child:ref:'.$refTbl.'" data-count="'.$childRefCount.'">';
								foreach($childData['columns'][$refTbl] as $refCol){
									$fhtml .= '<option value="'.$table.'.'.$childTbl.'.'.$refTbl.'.'.$refCol->Field.'"';
									//
									if($filters->column[$j]==($table.'.'.$childTbl.'.'.$refTbl.'.'.$refCol->Field)){
										$fhtml .= ' selected="selected"';
									}
									$fhtml .= '>'.$refCol->Field.'</option>';
								}
								$fhtml .= '</optgroup>';
							}
						}
					}
					$fhtml .= '</select></span>';
					$fhtml .= $this->loadoperators($filters->cond[$j]);
					if($filters->cond[$j]=='between' || $filters->cond[$j]=='notbetween'){
						$n = ($n<0)?0:$n+1;
					}
					else{
						$m = ($m<0)?0:$m+1;
					}
					$fhtml .= $this->loadFilterValue($j, $filters, $m, $n);
					$fhtml .= ' <span class="remove_filter btn btn-danger"><i class="icon-delete"></i> '.JText::_('REMOVE').'</span></td></tr>';
				endfor;
				endif;
				$fhtml .= '</tbody></table></td></tr>';
			//#filter end
				
			//#groupby field start
				$this->profile->params->groupby = isset($this->profile->params->groupby) ? $this->profile->params->groupby:null;
				$groupByHtml = '';
				$groupByHtml .= '<tr class="groupby"><td width="200"><label class="hasTip">'.JText::_('GROUP_BY').'</label></td><td>';
				$groupByHtml .= '<select name="params[groupby]">';
					$groupByHtml .= '<option value="">'.JText::_('SELECT_COLUMN').'</option>';
					foreach($obj->columns as $fcol) {
						$groupByHtml .= '<option value="'.$fcol.'"';
						if($fcol==$this->profile->params->groupby)   {
							$groupByHtml .= ' selected="selected"';
						}
						$groupByHtml .= '>'.$fcol.'</option>';
					}
					$groupByHtml .= '</select>';
				$groupByHtml .= '</td></tr>';
				//groupby end
				
				//orderby start
				$this->profile->params->orderby = isset($this->profile->params->orderby)?$this->profile->params->orderby:null;
				$this->profile->params->orderdir = isset($this->profile->params->orderdir)?$this->profile->params->orderdir:null;
				$ordreByHtml = '';
				$ordreByHtml .= '<tr class="orderby"><td width="200"><label class="hasTip">'.JText::_('ORDER_BY').'</label></td><td>';
				
				$ordreByHtml .= '<select name="params[orderby]">';
					$ordreByHtml .= '<option value="">'.JText::_('SELECT_COLUMN').'</option>';
					foreach($obj->columns as $fcol) {
						$ordreByHtml .= '<option value="'.$fcol.'"';
						if($fcol==$this->profile->params->orderby)   {
							$ordreByHtml .= ' selected="selected"';
						}
						$ordreByHtml .= '>'.$fcol.'</option>';
					}
					$ordreByHtml .= '</select>';
					$ordreByHtml .= ' <select name="params[orderdir]">';
						$ordreByHtml .= '<option value="asc"';
						if($this->profile->params->orderdir=="asc")   {
							$ordreByHtml .= ' selected="selected"';
						}
						$ordreByHtml .= '>'.JText::_('ASC').'</option>';
						$ordreByHtml .= '<option value="desc"';
						if($this->profile->params->orderdir=="desc")   {
							$ordreByHtml .= ' selected="selected"';
						}
						$ordreByHtml .= '>'.JText::_('DESC').'</option>';
					$ordreByHtml .= '</select>';
				$ordreByHtml .= '</td></tr>';
				//merge filter options
				if(!$importFilter){
					$fhtml .= $groupByHtml.''.$ordreByHtml;
				}

			}
			
			$jhtml = '<tr class="joins"><td colspan="2">'.JText::_('JOIN_TABLES').' <span class="add_join btn btn-success"><i class="icon-new"></i> '.JText::_('ADD_TABLE').'</span></td></tr>'
					. '<tr><td colspan="2"><table class="join_tables adminform table table-striped col_block"><tbody>';
			
			$joins = isset($this->profile->params->joins)?$this->profile->params->joins:null;
			
			if(!empty($joins)) :
				
				$joins->table1 = isset($joins->table1)?$joins->table1:array();
			
				for($i=0;$i<count($joins->table1);$i++) :
					
					if($i==0){
						$rch_table = ($joins->table1[$i]==$base_table) ? $joins->table1[$i] : $base_table;
					}
					else{
						$rch_table = $joins->table1[$i];
					}
					
					$query = 'show fields FROM '.$db->quoteName($rch_table);//$joins->table1[$i]
					$db->setQuery( $query );

					if($db->getErrorNum())	{
							throw new Exception($db->getErrorMsg());
							return false;
					}

					$t1columns = $db->loadObjectList();
					
					$query = 'show fields FROM '.$db->quoteName($joins->table2[$i]);
					$db->setQuery( $query );

					if($db->getErrorNum())	{
							throw new Exception($db->getErrorMsg());
							return false;
					}

					$t2columns = $db->loadObjectList();
					
					$jhtml .= '<tr><td colspan="2">';
		
					if($i==0){
						$chtable = ($joins->table1[$i]==$base_table) ? $joins->table1[$i] : $base_table;
						$jhtml .= '<select name="params[joins][table1][]" class="lefttable">';
						$jhtml .= '<option value="'.$chtable.'">'.$chtable.'</option>';
						$jhtml .= '</select>';
					}
					else{
						$jhtml .= '<select name="params[joins][table1][]" class="lefttable">';
						$temp_table1 = array();
						array_push($temp_table1, $base_table);
						for($t=0;$t<$i;$t++){
							if(!in_array($joins->table2[$t], $temp_table1)){
								array_push($temp_table1, $joins->table2[$t]);
							}
						}
						foreach($temp_table1 as $rtbl){
							$jhtml .= '<option value="'.$rtbl.'"';
							if($rtbl == $joins->table1[$i]){
								$jhtml .= ' selected="selected"';
							}
							$jhtml .= '>'.$rtbl.'</option>';
						}
						$jhtml .= '</select>';
					}
					
					// $jhtml .= ' <span class="hd_label">'.JText::_('JOIN').'<input type="hidden" name="params[joins][join][]" value="join" /></span> ';
					$jhtml .= '<span class="hd_label"><select name="params[joins][join][]" class="join">';
					$jhtml .= '<option value="join"';
						if($joins->join[$i]=='join'){
							$jhtml .= ' selected="selected"';
						}
					$jhtml .= '>'.JText::_('JOIN').'</option>';
					
					$jhtml .= '<option value="left_join"';
						if($joins->join[$i]=='left_join'){
							$jhtml .= ' selected="selected"';
						}
					$jhtml .= '>'.JText::_('LEFT_JOIN').'</option>';
					
					$jhtml .= '<option value="right_join"';
						if($joins->join[$i]=='right_join'){
							$jhtml .= ' selected="selected"';
						}
					$jhtml .= '>'.JText::_('RIGHT_JOIN').'</option>';
					
					$jhtml .= '</select></span>';

					$jhtml .= '<select name="params[joins][table2][]" class="righttable" data-previous="'.($joins->table2[$i]).'">';
					
					for($j=0;$j<count($tables);$j++)	{

						$table = str_replace($dbprefix, '#__', $tables[$j]);

						$jhtml .= '<option value="'.$table.'"';
						if($table===$joins->table2[$i]) $jhtml .= 'selected="selected"';
						$jhtml .= '>'.$table.'</option>';

					}

					$jhtml .= '</select> <label><span class="hd_label">'.JText::_('ON').'</span></label>';

					$jhtml .= '<select name="params[joins][column1][]" class="leftcolumn"><option value="">'.JText::_('SELECT_COLUMN').'</option>';
					
					for($j=0;$j<count($t1columns);$j++) :
						
						$column = $t1columns[$j]->Field;
					
						$jhtml .= '<option value="'.$column.'"';
						if($column==$joins->column1[$i]) $jhtml .= ' selected="selected"';
						$jhtml .= '>'.$column.'</option>';
						
					endfor;
					
					$jhtml .= '</select>';

					$jhtml .= ' = <select name="params[joins][column2][]" class="righttcolumn"><option value="">'.JText::_('SELECT_COLUMN').'</option>';
					
					for($j=0;$j<count($t2columns);$j++) :
						
						$column = $t2columns[$j]->Field;
					
						$jhtml .= '<option value="'.$column.'"';
						if($column==$joins->column2[$i]) $jhtml .= ' selected="selected"';
						$jhtml .= '>'.$column.'</option>';
						
					endfor;
					
					$jhtml .= '</select>';
					
				if($this->profile->iotype==0){
					$jhtml .= '<div class="field_options">';
					$jhtml .= '<span class="table_block">';
						$jhtml .= '<select class="component_value" name="params[joins][component][value][]">';
							$jhtml .= '<option value="">'.JText::_('SELECT_COMPONENT').'</option>';
							$components = $this->getComponents();
							foreach($components as $component){
								
								$jhtml .= '<option value="'.$component->name.'"';
								
								if( isset($joins->component->value[$i]) && $component->name==$joins->component->value[$i]){
								$jhtml .= ' selected="selected"';	
									$component_name = JText::_($component->name);
								}
								$jhtml .= '>'.JText::_($component->name).'</option>';
							}
						$jhtml .= '</select>';
					$jhtml .= '</span>';
					$jhtml .= '<span class="hd_label">'.JText::_('Table File').'</span>';
					$jhtml .= '<span class="field">';
						$jhtml .= '<select name="params[joins][component][table][]" class="component_table">';
						if(isset($joins->component->value[$i])){
							$seleced_table = isset($joins->component->table[$i])? $joins->component->table[$i]: false;
							$jhtml .= $this->getComponentTables($joins->component->value[$i], $seleced_table);
						}
						$jhtml .= '</select>';
					$jhtml .= '</span>';
					$jhtml .= '</div>';
				}
					
					$jhtml .= '</td></tr><tr><td width="200">'.JText::_('COLUMNS_TO_IMPORT_EXPORT').'</td><td class="iocolumns">';
					
					$jhtml .= '<table class="child_tables join_tables'.$i.'" name="join_tables'.$i.'"><tbody>';
					if( property_exists($joins, 'columns') && !empty($joins->columns[$i]) ){
						foreach($joins->columns[$i] as $jkey=>$jfield){
							$jhtml .= '<tr data-column="'.$jkey.'">';
							$jhtml .= '<td><label class="hasTip">'.$jkey.'</label></td>';
							$jhtml .= '<td>';
							$jhtml .= $this->loadFieldOptions($jfield, $jkey, $i);
							$jhtml .= '</td>';
							$jhtml .= '</tr>';
						}
					}
					$jhtml .= '</tbody></table>';
					
					$jhtml .= '</td></tr>';
					
				endfor;
				
				$jhtml .= '</tbody></table></td></tr>'
						.'<tr class="remove_join"><td colspan="2"><span class="remove_join btn btn-success"><i class="icon-delete"></i> '.JText::_('REMOVE').'</span></td></tr>';
				
			endif;
			
			if($importFilter){
				$obj->html = $fhtml;
			}
			else{
				$obj->html = $fhtml.$obj->html.$jhtml;                
			}
			$obj->unqkey_html = '<option value="">'.JText::_('SELECT_FIELD').'</option>';
			$keyArray = array();
			if(isset($this->profile->params->unqkey)){
				$keyArray = is_array($this->profile->params->unqkey)?$this->profile->params->unqkey:array($this->profile->params->unqkey);
			}
			foreach($obj->columns as $col)
			{
				$obj->unqkey_html .= '<option value="'.$col.'" ';
				
				if( !empty($this->profile->params->table) && ($this->profile->params->table == $base_table) && property_exists($this->profile->params, 'unqkey') && in_array($col, $keyArray) ){
					$obj->unqkey_html .= 'selected="selected"';
				}
				elseif(!empty($primary) && in_array($col, $primary)&& !property_exists($this->profile->params, 'unqkey')){
					$obj->unqkey_html .= 'selected="selected"';
				}
				elseif(!empty($primary) && in_array($col, $primary)&& empty($keyArray) && ($this->profile->params->table != $base_table)){
					$obj->unqkey_html .= 'selected="selected"';
				}
				$obj->unqkey_html .= ' >'.$col.'</option>';
			}
			$obj->result = 'success';
			jexit(json_encode($obj));
		}
			
		function loadoperators($value)
		{
			
			$html = '<span class="op_block"><select name="params[filters][cond][]" class="oplist">';
			
			$html .= '<option value="="';
			if($value=='=')
				$html .= ' selected="selected"';
			$html .= '>=</option>';
			$html .= '<option value="<>"';
			if($value=='<>')
				$html .= ' selected="selected"';
			$html .= '>!=</option>';
			$html .= '<option value="<"';
			if($value=='<')
				$html .= ' selected="selected"';
			$html .= '>&lt;</option>';
			$html .= '<option value=">"';
			if($value=='>')
				$html .= ' selected="selected"';
			$html .= '>&gt;</option>';
			$html .= '<option value="in"';
			if($value=='in')
				$html .= ' selected="selected"';
			$html .= '>IN</option>';
			$html .= '<option value="notin"';
			if($value=='notin')
				$html .= ' selected="selected"';
			$html .= '>NOT IN</option>';
			$html .= '<option value="between"';
			if($value=='between')
				$html .= ' selected="selected"';
			$html .= '>BETWEEN</option>';
			$html .= '<option value="notbetween"';
			if($value=='notbetween')
				$html .= ' selected="selected"';
			$html .= '>NOT BETWEEN</option>';
			$html .= '<option value="like"';
			if($value=='like')
				$html .= ' selected="selected"';
			$html .= '>LIKE</option>';
			$html .= '<option value="notlike"';
			if($value=='notlike')
				$html .= ' selected="selected"';
			$html .= '>NOT LIKE</option>';
			$html .= '<option value="regexp"';
			if($value=='regexp')
				$html .= ' selected="selected"';
			$html .= '>REGEXP</option>';
			
			$html .= '</select></span>';
			
			return $html;
			
		}
		
		function loadFilterValue($j, $filters, $single, $double){
			
			$html = '<span class="value_block">';
			
			if($filters->cond[$j]=='between' || $filters->cond[$j]=='notbetween'){
				
				foreach($filters->value1 as $key=>$val){
					if($key==$double){
						$value1 = $val;
					}
				}
				$value1 = isset($value1)?$value1:'';
				foreach($filters->value2 as $key=>$val){
					if($key==$double){
						$value2 = $val;
					}
				}
				$value2 = isset($value2)?$value2:'';
				$html .= '<input id="paramsfiltersvalue'.$j.'" class="inputbox filterval" type="text" size="50" value="'.htmlspecialchars($value1).'" name="params[filters][value1][]">';
				$html .= '<input id="paramsfiltersvalue'.$j.'" class="inputbox filterval" type="text" size="50" value="'.htmlspecialchars($value2).'" name="params[filters][value2][]">';
			}
			else{
				foreach($filters->value as $key=>$val){
					if($key==$single){
						$value = $val;
					}
				}
				$value = isset($value)?$value:'';
				$html .= '<input id="paramsfiltersvalue'.$j.'" class="inputbox filterval" type="text" size="50" value="'.htmlspecialchars($value).'" name="params[filters][value][]">';
			}
			$html .= '</span>';
			return $html;
		}
		
		
		protected function loadFieldOptions($field, $column, $index=-1)
		{
			$db = JFactory::getDbo();
			$tables = $this->getTables();
			$dbprefix = $db->getPrefix();
			$html = '<select '.($index!=-1 ? 'name="params[joins][columns]['.$index.']['.$column.'][data]" data-vid="'.$index.'"':'name="params[fields]['.$column.'][data]" id="paramsfields'.$column.'data"').' class="field_type">
					<option value="skip">'.JText::_('SKIP').'</option>';
			if($this->profile->iotype==1){
				$html .= '<option value="include"';
				if($field->data==="include")   {
					$html .= ' selected="selected"';
				}
				$html .= '>'.JText::_('INCLUDE').'</option>';
			}
			else{
				$html .= '<option value="file"';
				if($field->data==="file")   {
					$html .= ' selected="selected"';
				}
				$html .= '>'.JText::_('DATA_FILE').'</option>';
			}
			$html .= '<option value="defined"';
				if($field->data=="defined")
					$html .= ' selected="selected"';
				$html .= '>'.JText::_('AS_DEFINED').'</option>';
					
			$html .= '<option value="reference"'; 
				if($field->data==="reference")
					$html .= ' selected="selected"';
				$html .= '>'.JText::_('REFERENCED_TO').'</option>';
			if($this->profile->iotype!=1 && $column == 'asset_id')
			  {
				$html .= '<option value="asset_reference"';
				if($field->data==="asset_reference")
					$html .= ' selected="selected"';
				$html .= '>'.JText::_('ASSET_REFERENCED_TO').'</option>';
			   }
			$html .= '</select>';
			
			//field options
			$html .= '<div class="field_options">';
			
			if( ($field->data == 'file') && ($this->profile->iotype==0))  {
				$field->format=isset($field->format)?$field->format:'';
				$html .= ' <span class="format_block"><select '.($index!=-1 ? 'name="params[joins][columns]['.$index.']['.$column.'][format]" data-vid="'.$index.'"':'name="params[fields]['.$column.'][format]"').' class="field_format"><option value="string">'.JText::_('STRING').'</option>';
				$html .= '<option value="date"';
				if($field->format=='date')
						$html .= ' selected="selected"';
				$html .= '>'.JText::_('DATE').'</option>';
				$html .= '<option value="image"';
				if($field->format=='image')
						$html .= ' selected="selected"';
				$html .= '>'.JText::_('IMAGE').'</option>';
				$html .= '<option value="number"';
				if($field->format=='number')
						$html .= ' selected="selected"';
				$html .= '>'.JText::_('NUMBER').'</option>';
				$html .= '<option value="urlsafe"';
				if($field->format=='urlsafe')
						$html .= ' selected="selected"';
				$html .= '>'.JText::_('URLSAFE').'</option>';
				$html .= '<option value="encrypt"';
				if($field->format=='encrypt')
					$html .= ' selected="selected"';
				$html .= '>'.JText::_('ENCRYPTION').'</option>';
				$html .= '<option value="email"';
					if($field->format=='email'){
						$html .= ' selected="selected"';
					}
				$html .= '>'.JText::_('VDATA_VALIDATE_EMAIL').'</option>';
					
				$html .= '</select>';
				
				$html .= '<span class="field_val">';
				if($field->format=='string'){
					$html .= '<select class="field_str" '.($index!=-1? 'name="params[joins][columns]['.$index.']['.$column.'][type]" data-vid="'.$index.'"':'name="params[fields]['.$column.'][type]"').'>';
					$html .= '<option value=""';
					if($field->type==''){
						$html .= ' selected="selected"';
					}
					$html .= '>'.JText::_('AS_IT_IS').'</option>';
					$html .= '<option value="striptags"';
					if($field->type=='striptags'){
						$html .= ' selected="selected"';
					}
					$html .= '>'.JText::_('STRIP_TAGS').'</option>';
					$html .= '<option value="chars"';
					if($field->type=='chars'){
						$html .= ' selected="selected"';
					}
					$html .= '>'.JText::_('CHAR_LIMIT').'</option>';
					
					$html .= '</select>';
					
					$html .= '<span class="char_limit">';
					if($field->type=='chars'){
						$html .= '<input type="text" name="'.($index!=-1? 'params[joins][columns]['.$index.']['.$column.'][val]': 'params[fields]['.$column.'][val]').'" value="'.$field->val.'" />';
					}
					$html .= '</span>';
					
				}
				elseif($field->format=='date'){
					$html .= '<input type="text" name="'.($index!=-1? 'params[joins][columns]['.$index.']['.$column.'][type]':'params[fields]['.$column.'][type]').'" value="'.JText::_('Y-m-d H:i:s').'" />';
				}elseif($field->format=='image'){
					$html .= '<input type="text" name="'.($index!=-1? 'params[joins][columns]['.$index.']['.$column.'][location]':'params[fields]['.$column.'][location]').'" value="'.$field->location.'" />';
				}
				elseif($field->format=='encrypt'){
					$html .= '<select '.($index!=-1? 'name="params[joins][columns]['.$index.']['.$column.'][type]" data-vid="'.$index.'"':'name="params[fields]['.$column.'][type]"').'>';
					$html .= '<option value="plain"';
					if($field->type=='plain'){
						$html .= ' selected="selected"';
					}
					$html .= '>'.JText::_('AS_IT_IS').'</option>';
					$html .= '<option value="bcrypt"';
					if($field->type=='bcrypt'){
						$html .= ' selected="selected"';
					}
					$html .= '>'.JText::_('BCRYPT').'</option>';
					$html .= '<option value="sha"';
					if($field->type=='sha'){
						$html .= ' selected="selected"';
					}
					$html .= '>'.JText::_('SHA').'</option>';
					$html .= '<option value="crypt"';
					if($field->type=='crypt'){
						$html .= ' selected="selected"';
					}
					$html .= '>'.JText::_('CRYPT').'</option>';
					$html .= '<option value="crypt-des"';
					if($field->type=='crypt-des'){
						$html .= ' selected="selected"';
					}
					$html .= '>'.JText::_('CRYPT-DES').'</option>';
					$html .= '<option value="crypt-md5"';
					if($field->type=='crypt-md5'){
						$html .= ' selected="selected"';
					}
					$html .= '>'.JText::_('CRYPT-MD5').'</option>';
					$html .= '<option value="crypt-blowfish"';
					if($field->type=='crypt-blowfish'){
						$html .= ' selected="selected"';
					}
					$html .= '>'.JText::_('CRYPT-BLOWFISH').'</option>';
					
					$html .= '<option value="ssha"';
					if($field->type=='ssha'){
						$html .= ' selected="selected"';
					}
					$html .= '>'.JText::_('SSHA').'</option>';
					$html .= '<option value="smd5"';
					if($field->type=='smd5'){
						$html .= ' selected="selected"';
					}
					$html .= '>'.JText::_('SMD5').'</option>';
					$html .= '<option value="aprmd5"';
					if($field->type=='aprmd5'){
						$html .= ' selected="selected"';
					}
					$html .= '>'.JText::_('APRMD5').'</option>';
					$html .= '<option value="sha256"';
					if($field->type=='sha256'){
						$html .= ' selected="selected"';
					}
					$html .= '>'.JText::_('SHA256').'</option>';
					$html .= '<option value="md5-hex"';
					if($field->type=='md5-hex'){
						$html .= ' selected="selected"';
					}
					$html .= '>'.JText::_('MD5-HEX').'</option>';
					$html .= '</select>';
				}
				$html .= '</span>';
				$html .= '</span>';
			}
			
			elseif($field->data == 'defined')  {
				$field->default=isset($field->default)?$field->default:'';
				$html .= ' <span class="default_block"><input '.($index!=-1? 'name="params[joins][columns]['.$index.']['.$column.'][default]" data-vid="'.$index.'"':'name="params[fields]['.$column.'][default]" id="paramsfields'.$column.'default"').' value="'.htmlspecialchars($field->default).'" /></span>';
			}
			elseif($field->data == 'reference')  {
				$field->table=isset($field->table)?$field->table:'';
				$field->reftext=isset($field->reftext)?$field->reftext:null;
				$html .= ' <span class="table_block"><select class="field_table" '.($index!=-1?'name="params[joins][columns]['.$index.']['.$column.'][table]" data-vid="'.$index.'"' : 'name="params[fields]['.$column.'][table]"').' data-previous="'.($field->table).'"><option value="">'.JText::_('SELECT_TABLE').'</option>';
				foreach($tables as $table)	{
					$table = str_replace($dbprefix, '#__', $table);
					$html .= '<option value="'.$table.'"';
					if($field->table==$table)
							$html .= ' selected="selected"';
					$html .= '>'.$table.'</option>';
				}
				$html .= '</select></span>';
				if(empty($field->table))    {
					$html .= ' <span class="column_block"></span> <span class="refcolumn_block"></span>';
				}
				else{
					$query = 'show fields FROM '.$db->quoteName($field->table);
					$db->setQuery( $query );
					$cols = $db->loadObjectList();
					if($db->getErrorNum())	{
						throw new Exception($db->getErrorMsg());
						return false;
					}
					$html .= ' <span class="hd_label">'.JText::_('ON').'</span>';
					$html .= ' <span class="leftcolumn_block">';
					$html .= ' <span class="field"><select name="'.($index!=-1?'params[joins][columns]['.$index.']['.$column.'][on]':'params[fields]['.$column.'][on]').'">';
					for($j=0;$j<count($cols);$j++)	{
						$html .= '<option value="'.$cols[$j]->Field.'"';
						if(isset($field->on) and $cols[$j]->Field==$field->on)	{
							$html .= ' selected="selected"';
						}
						$html .= '>'.$cols[$j]->Field.'</option>';
					}
					$html .= '</select></span></span>';
					$html .= ' <span class="hd_label">'.JText::_('SELECT_COLUMNS').'</span>';
					$html .= ' <span class="refcolumn_block">';
					$html .= ' <span class="field"><select name="'.($index!=-1?'params[joins][columns]['.$index.']['.$column.'][reftext][]':'params[fields]['.$column.'][reftext][]').'" multiple="multiple" size="5">';
					for($j=0;$j<count($cols);$j++)	{
						$html .= '<option value="'.$cols[$j]->Field.'"';
						if(isset($field->reftext) and in_array($cols[$j]->Field, $field->reftext))	{
							$html .= ' selected="selected"';
						}
						$html .= '>'.$cols[$j]->Field.'</option>';
					}
					$html .= '</select></span></span>';
				}
			}
			elseif($field->data == 'asset_reference')
			{
			$html .= ' <span class="table_block"><select class="field_table component" '.($index!=-1?'name="params[joins][columns]['.$index.']['.$column.'][table]" data-vid="'.$index.'"' : 'name="params[fields]['.$column.'][table]"').'><option value="">'.JText::_('SELECT_COMPONENT').'</option>';

					$field->table=isset($field->table)?$field->table:null;
					$field->reftext=isset($field->reftext)?$field->reftext:null;
					$db  = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query->select('extension_id, name');
					$query->from($db->quoteName('#__extensions'));
					$query->where($db->quoteName('type') . ' = ' . $db->quote('component'));
					$db->setQuery($query);
					$components = $db->loadObjectList(); 
					$component_name = '';
					foreach($components as $component)	{
							
							$html .= '<option value="'.$component->name.'"';
							if($component->name==$field->table){
							$html .= ' selected="selected"';	
							$component_name = JText::_($component->name);
							}
									
							$html .= '>'.JText::_($component->name).'</option>';
					}

				$html .= '</select></span>';
				
				if(empty($field->table))    {
					$html .= ' <span class="hd_label">'.JText::_('Table File').'</span>';
					$html .= ' <span class="leftcolumn_block">';
					$html .= ' <span class="field"><select name="'.($index!=-1?'params[joins][columns]['.$index.']['.$column.'][on]':'params[fields]['.$column.'][on]').'">';
					$html .= '</select></span></span>';
					
					// $html .= ' <span class="column_block"></span> <span class="refcolumn_block"></span>';
				}
				else    {
						 
						if (strpos(strtolower($component_name), "com_") ===FALSE)
						 $component_name = "com_".strtolower($component_name); 
						$tablefile = array();
						$table_files = glob(JPATH_ADMINISTRATOR.'/components/'.$component_name.'/tables/*.php');
				
						if( @count($table_files) > 0 ) {
						foreach( $table_files as $f ) {
						$tablefile[] = str_replace(JPATH_ADMINISTRATOR.'/components/'.$component_name.'/tables/', '', $f);
						}
						sort($tablefile);
						}
					
					
					 $html .= ' <span class="hd_label">'.JText::_('Table File').'</span>';
					
					$html .= ' <span class="leftcolumn_block">';

					$html .= ' <span class="field"><select name="'.($index!=-1?'params[joins][columns]['.$index.']['.$column.'][on]':'params[fields]['.$column.'][on]').'">';
					
						if($component_name=='com_content')
						{
							$html .= '<option value="content"';
							if(isset($params->on) and "content" == $params->on)
							{
							$html .= ' selected="selected"';
							}
							$html .= '>Content</option>';	 
						}
						elseif($component_name=='com_modules'){
							$html .= '<option value="module"';
							if(isset($params->on) and "module" == $params->on)
							{
							$html .= ' selected="selected"';
							}
							$html .= '>Module</option>';
						}
						else
						{

							foreach( $tablefile as $api )
							{
								$html .= '<option value="'.substr($api, 0, strpos($api, '.')).'"';
								if(isset($params->on) and substr($api, 0, strpos($api, '.')) == $params->on)
								{
								$html .= ' selected="selected"';
								}
								$html .= '>'.substr($api, 0, strpos($api, '.')).'</option>';

							}
						}
					
					$html .= '</select></span></span>';
					
					
				}	
			}
			$html .= '</div>';
			
			return $html;
			
		}
		
		function load_table_fields(){
			
			JSession::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALID_TOKEN').'"}' ); 
			$obj = new stdClass();
			$table = JFactory::getApplication()->input->get('table', '', 'RAW');
			$index  = JFactory::getApplication()->input->get('index', '', 'INT');
			$iotype = JFactory::getApplication()->input->get('iotype', '0', 'INT');
			if(empty($table)){
				$obj->result = 'error';
				$obj->error = JText::_('NO_TABLE_FOUND');
				jexit(json_encode($obj));
			}
			$res = $this->getTableFieldsHtml($table, $index, $iotype);
			$columns = $this->getTableColumns($table);
			if($res->result=='error'){
				$obj->result = 'error';
				$obj->error = $res->error;
				jexit(json_encode($obj));
			}
			$obj->result = 'success';
			$obj->columns = $columns;
			$obj->html = $res->html;
			jexit(json_encode($obj));
			
		}
		
		function getTableFieldsHtml($table, $index=-1, $iotype){
			
			$columns = $this->getTableColumns($table);
			$obj = new stdClass();
			$obj->result = 'success';
			
			$obj->html = '<table name="join_tables'.($index!=-1 ? $index : '').'" class="child_tables join_tables'.($index!=-1 ? $index : '').'" >';
			foreach($columns as $column){
				$obj->html .= '<tr data-column="'.$column.'"><td><label>'.$column.'</label></td>';
				$obj->html .= '<td>';
				$obj->html .= '<select name="params[joins][columns]'.($index!=-1 ? '['.$index.']': '').'['.$column.'][data]" class="field_type" '.($index!=-1 ? ' data-vid="'.$index.'"': '').'>';
				$obj->html .= '<option value="skip">'.JText::_('SKIP').'</option>';
				if($iotype==0){
					$obj->html .= '<option value="file">'.JText::_('DATA_FILE').'</option>';
				}
				else{
					$obj->html .= '<option value="include">'.JText::_('INCLUDE').'</option>';
				}
				
				
				$obj->html .= '<option value="defined">'.JText::_('AS_DEFINED').'</option>';
				$obj->html .= '<option value="reference">'.JText::_('REFERENCED_TO').'</option>';
				if($column=='asset_id'){
				$obj->html .= '<option value="asset_reference">'.JText::_('ASSET_REFERENCED_TO').'</option>';
				}
				$obj->html .= '</select><div class="field_options"></div>';
				$obj->html .= '</td></tr>';
			}
			$obj->html .= '</table>';
			
			return $obj;
		}
		
		function getTableColumns($table){
			$columns = array();
			$db = $this->getDbc();
			$query = 'show fields from '.$db->quoteName($table);
			try{
				$db->setQuery($query);
				$fields = $db->loadObjectList();
			}
			catch(Exception $e){
				return $columns;
			}
			foreach($fields as $key=>$field){
				$columns[] = $field->Field;
			}
			return $columns;
		}
		
		function getComponents() {
			
			$components = array();
			$db  = $this->getDbc();
			$query = $db->getQuery(true);
			$query->select('extension_id, name');
			$query->from($db->quoteName('#__extensions'));
			$query->where($db->quoteName('type') . ' = ' . $db->quote('component'));
			$db->setQuery($query);
			$components = $db->loadObjectList();
			return $components;
			
		}
		
		//get referenced columns
		protected function getReferColumns()
		{
			$obj = new stdClass();
			$obj->result = 'error';
			$remote_db = JFactory::getApplication()->input->getInt('remote_db', 0);
			$session = JFactory::getSession();
			if($remote_db && $session->has('remote_details')) {
				$option = $session->get('remote_details');
				$db = JDatabaseDriver::getInstance( $option );
				$db->connect();
			}
			else
				$db = JFactory::getDbo();
			
			$table = JFactory::getApplication()->input->get('table', '', 'RAW');
			$column = JFactory::getApplication()->input->get('column', '', 'RAW');
			$component = JFactory::getApplication()->input->get('component', '', 'RAW');
			$id = JFactory::getApplication()->input->getInt('profileid', 0);
			$index = JFactory::getApplication()->input->getInt('index', -1);
			
			if(empty($column))	{
				$obj->error = JText::_('PLZ_SEL_COLUMN_FIRST');
				return $obj;
			}
			if(!empty($component))
			{
				
				$db  = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select('name');
				$query->from($db->quoteName('#__extensions'));  
				$query->where($db->quoteName('type') . ' = ' . $db->quote('component'));
				$query->where($db->quoteName('name') . ' = ' . $db->quote($component));
				$db->setQuery($query);
				$components = JText::_($db->loadResult()); 
				
				if (strpos(strtolower($components), "com_") ===false){
				$components = "com_".strtolower($components); 	
				}
				
				$tablefile = array();
				
				$table_files = glob(JPATH_ADMINISTRATOR.'/components/'.$components.'/tables/*.php');
				
						if( @count($table_files) > 0 ) {
						foreach( $table_files as $f ) {
						$tablefile[] = str_replace(JPATH_ADMINISTRATOR.'/components/'.$components.'/tables/', '', $f);
						}
						sort($tablefile);
						}
				 $obj->ontext = '<select name="'.($index>-1?'params[joins][columns]['.$index.']['.$column.'][on]':'params[fields]['.$column.'][on]').'">';
				 if($components=='com_content')
				 {
					$obj->ontext .= '<option value="content"';
					if(isset($params->on) and "content" == $params->on)
					{
					$obj->ontext .= ' selected="selected"';
					}
					$obj->ontext .= '>Content</option>';	 
				 }
				 elseif($components=='com_modules'){
					$obj->ontext .= '<option value="module"';
					if(isset($params->on) and "module" == $params->on)
					{
					$obj->ontext .= ' selected="selected"';
					}
					$obj->ontext .= '>Module</option>';
				 }
				 else
				 {
					
						foreach( $tablefile as $api )
						{
							$obj->ontext .= '<option value="'.substr($api, 0, strpos($api, '.')).'"';
							if(isset($params->on) and substr($api, 0, strpos($api, '.')) == $params->on)
							{
								$obj->ontext .= ' selected="selected"';
							}
							$obj->ontext .= '>'.substr($api, 0, strpos($api, '.')).'</option>';
						
						}
				} 
				 $obj->ontext .= '</select>';
				
				$obj->result = 'success';
				return $obj;
			}
			if(empty($table)){
				$obj->reftext = '<select name="'.($index>-1?'params[joins][columns]['.$index.']['.$column.'][reftext][]':'params[fields]['.$column.'][reftext][]').'" multiple="multiple" size="5">';
				$obj->ontext = '<select name="'.($index>-1?'params[joins][columns]['.$index.']['.$column.'][on]':'params[fields]['.$column.'][on]').'">';
				$obj->reftext .= '</select>';
				$obj->ontext .= '</select>';
				$obj->columns = array();
				$obj->result = 'success';
				return $obj;
			}
			
			$item = null;
			
			if(empty($item))
				$params = new stdClass();
			else
				$params = (object)json_decode($item->params);
			
			$query = 'show fields FROM '.$db->quoteName($table);$obj->tbl = $index;
			$db->setQuery( $query );
			
			if($db->getErrorNum())	{
				$obj->error = $db->getErrorMsg();
				return $obj;
			}
			
			$cols = $db->loadObjectList();
			
			$obj->reftext = '<select name="'.($index>-1?'params[joins][columns]['.$index.']['.$column.'][reftext][]':'params[fields]['.$column.'][reftext][]').'" multiple="multiple" size="5">';
					$obj->ontext = '<select name="'.($index>-1?'params[joins][columns]['.$index.']['.$column.'][on]':'params[fields]['.$column.'][on]').'">';
			$obj->columns = array();
			for($i=0;$i<count($cols);$i++)	{
				$obj->reftext .= '<option value="'.$cols[$i]->Field.'"';
				if(isset($params->reftext) and $cols[$i]->Field==$params->reftext)	{
					$obj->reftext .= ' selected="selected"';
				}
				$obj->reftext .= '>'.$cols[$i]->Field.'</option>';
							
			 $obj->ontext .= '<option value="'.$cols[$i]->Field.'"';
				if(isset($params->on) and $cols[$i]->Field==$params->on)	{
					$obj->ontext .= ' selected="selected"';
				}
				$obj->ontext .= '>'.$cols[$i]->Field.'</option>';
				array_push($obj->columns, $cols[$i]->Field);
			}
			
			$obj->reftext .= '</select>';
					$obj->ontext .= '</select>';
			
			$obj->result = 'success';
			
			return $obj;
			
		}
		
		function load_comonent_tables(){
			
			JSession::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALID_TOKEN').'"}' );
			// $component = JFactory::getApplication()->input->getInt('component', 0);
			$component = JFactory::getApplication()->input->get('component', '', 'cmd');
			$obj = new stdClass();
			$obj->html = '';
			if(!empty($component))
			{
				$obj->html .= $this->getComponentTables($component);
			}
			$obj->result = 'success';
			jexit(json_encode($obj));
			
		}
		
		function getComponentTables($component, $table=''){

			$html = '<option value="">'.JText::_('SELECT_COMPONENT_TABLE').'</option>';
			if(!empty($component))
			{
				$db  = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select('name');
				$query->from($db->quoteName('#__extensions'));  
				$query->where($db->quoteName('type') . ' = ' . $db->quote('component'));
				$query->where($db->quoteName('name') . ' = ' . $db->quote($component));
				$db->setQuery($query);
				$components = JText::_($db->loadResult()); 
				
				if (strpos(strtolower($components), "com_") ===false){
					$components = "com_".strtolower($components); 	
				}
				
				$tablefile = array();
				$table_files = glob(JPATH_ADMINISTRATOR.'/components/'.$components.'/tables/*.php');
				if( @count($table_files) > 0 ) {
					foreach( $table_files as $f ) {
						$tablefile[] = str_replace(JPATH_ADMINISTRATOR.'/components/'.$components.'/tables/', '', $f);
					}
					sort($tablefile);
				}
				if($components=='com_content')
				{
					$html .= '<option value="content"';
					if(isset($table) and "content" == $table)
					{
						$html .= ' selected="selected"';
					}
					$html .= '>Content</option>';	 
				}
				elseif($components=='com_modules'){
					$html .= '<option value="module"';
					if(isset($table) and "module" == $table){
						$html .= ' selected="selected"';
					}
					$html .= '>Module</option>';
				}
				else{
					foreach( $tablefile as $api )
					{
					   $html .= '<option value="'.substr($api, 0, strpos($api, '.')).'"';
						if(isset($table) and substr($api, 0, strpos($api, '.')) == $table)
						{
							$html .= ' selected="selected"';
						}
					   $html .= '>'.substr($api, 0, strpos($api, '.')).'</option>';
					}
				}
			}
			return $html;
		}
		
		function load_refer_columns()
		{
			$obj = $this->getReferColumns();
			jexit(json_encode($obj));
		}
		
		function load_table_columns()
		{
			$obj = new stdClass();
			$obj->result = 'success';
			$obj->columns = array();
			
			$remote_db = JFactory::getApplication()->input->getInt('remote_db', 0);
			$session = JFactory::getSession();
			if($remote_db && $session->has('remote_details')) 
			{
				$option = $session->get('remote_details');
				$db = JDatabaseDriver::getInstance( $option );
				$db->connect();
			}
			else
				$db = $this->getDbc();
			$table = JFactory::getApplication()->input->get('table', '', 'RAW');
			$id = JFactory::getApplication()->input->getInt('profileid', 0);

			if(empty($table))
				return $obj;
				
			$query = 'show fields FROM '.$db->quoteName($table);
			$db->setQuery( $query );

			if($db->getErrorNum())	{
				$obj->error = $db->getErrorMsg();
				return $obj;
			}

			$cols = $db->loadObjectList();
			
			foreach($cols as $col):
				array_push($obj->columns, $col->Field);
			endforeach;
		
			jexit(json_encode($obj));
		}
		
		function load_column_options(){
			
			JSession::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALID_TOKEN').'"}' );
			$obj = new stdClass();
			$remote_db = JFactory::getApplication()->input->getInt('remote_db', 0);
			$session = JFactory::getSession();
			if($remote_db) {
				if($session->has('remote_details')){
					$option = $session->get('remote_details');
					$db = JDatabaseDriver::getInstance( $option );
					$db->connect();
				}
				else{
					$obj->result = 'error';
					$obj->error = JText::_('UNABLE_TO_CONNECT_DATABASE');
					jexit(json_encode($obj));
				}
			}
			else{
				$db = $this->getDbc();
			}
			
			$table = JFactory::getApplication()->input->get('table', '', 'RAW');
			
			if(empty($table)) {
				$obj->result = 'error';
				$obj->error = JText::_('SELECT_TABLE');
				jexit(json_encode($obj));
			}
			$obj->result = 'success';
			$obj->html = '';
			$response = $this->getColumnOptions($table,'', $db);
			$obj->html .= $response->options;
			
			$obj->columns = $response->columns;//
			
			jexit(json_encode($obj));
		}
		
		function getColumnOptions($table, $selected='', $db=null){
			
			$obj = new stdClass();
			$obj->options = '';
			// $obj->options = '<option value="reference">'.JText::_('REFERENCE_FIELD').'</option>';//
			if(empty($table) || empty($db)){
				return $obj->options;
			}
			$query = 'show fields FROM '.$db->quoteName($table);
			$db->setQuery( $query );
			if($db->getErrorNum())	{
				return $obj->options;
			}
			$cols = $db->loadObjectList();
			$columns = array();//
			foreach($cols as $col){
				$obj->options .= '<option value="'.$col->Field.'"';
				if(!empty($selected)){
					if($selected==$col->Field) $obj->options .= ' selected="selected"';
				}
				$obj->options .= '>'.$col->Field.'</option>';
				array_push($columns, $col->Field);//
			}
			$obj->columns = $columns;//
			return $obj;
		}
		
		
		function load_import_columns(){
			
			JSession::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALID_TOKEN').'"}' );
			$table = JFactory::getApplication()->input->get('table', '', 'RAW');
			$db = JFactory::getDbo();
			$this->getProfile();
			$obj = new stdClass();
			$obj->result = 'success';
			$obj->html = '';
			$query = 'show fields FROM '.$db->quoteName($this->profile->params->table);
			$db->setQuery( $query );

			if($db->getErrorNum())	{
				$obj->error = $db->getErrorMsg();
				return $obj;
			}
			$cols = $db->loadObjectList();
			$obj->html .= '<tr class="columns"><td colspan="2">'.JText::_('COLUMNS').'</td></tr>';
			foreach($cols as $col)
			{
				$obj->html .= '<tr data-column='.$col->Field.'>';
				$obj->html .= '<td width="200"><label class="hasTip" title="">'.$col->Field.'</label></td>';
				$obj->html .= '<td><select class="remote_column_join" name="fields['.$col->Field.']">
							<option value="">'.JText::_('Select Field').'</option>';
							
				$query = 'show fields FROM '.$db->quoteName($table);
				$session = JFactory::getSession();
				try
				{
					$option = $session->get('remote_details');
					$remote_db = JDatabaseDriver::getInstance( $option );
					$remote_db->connect();
				}
				catch (RuntimeException $e) 
				{
						$remote_db->error = $remote_db->getErrorMsg();
						return $obj;
				}
				$remote_db->setQuery( $query );

				if($remote_db->getErrorNum())	{
					$remote_db->error = $remote_db->getErrorMsg();
					return $obj;
				}
				$remote_cols = $remote_db->loadObjectList();
				if(count($remote_cols))
				{
					foreach($remote_cols as $remote_col){
						$obj->html .= '<option value="'.$remote_col->Field.'"';
						if($col->Field == $remote_col->Field){$obj->html .= "selected=selected";}
						$obj->html .= '>'.$remote_col->Field.'</option>';
					}
				}
				$obj->html .= '</select></td>';	
				$obj->html .= '</tr>';
			}
			jexit(json_encode($obj));
		} 
		 
		 
		function onSaveProfile($id)
		{
			
			return true;
			
		}
		
		function onDeleteProfile($oid)
		{
			
			return true;
			
		}
		
		function onImportProfile()
		{
			$db = JFactory::getDbo();
			$dbprefix = $db->getPrefix();
			$lang = JFactory::getLanguage();
			$lang->load('plg_vdata_custom', JPATH_SITE.'/plugins/vdata/custom');
			$source = JFactory::getApplication()->input->get('source', '', 'STRING');
			
			$this->getProfile();
			
			$importitem = (object)JFactory::getApplication()->input->post->getArray();
			
			$session = JFactory::getSession();
			$session->set('importitem', $importitem);
			
			//if it's not new schedule,load it's field
			if(!empty($importitem->id)){
				$query = "select * from #__vd_schedules where id=".$importitem->id;
				$db->setQuery($query);
				$schedule = $db->loadObject();
				$schedule_columns = json_decode($schedule->columns);
				$profile_id = $schedule->profileid;
			}
			else{
				$schedule_columns = null;
				$profile_id = null;
			}
			if(!empty($importitem->st) && empty($source))
			{ 
			   require(dirname(__FILE__).'/tmpl/import_feed.php');
			   return true;
			}
			if(empty($this->profile->id)) {
				throw new Exception(JText::_('PLZ_SELECT_PROFILE'));
				return false;
			}

			switch($source) {
				case 'CSV':
				case 'csv':
					$csvfields = $this->prepare_csv();
					$config = $this->getConfig();
					$csv_child = json_decode($config->csv_child);
					require(dirname(__FILE__).'/tmpl/import_csv.php');
				break;
				case 'XML':
				case 'xml':
					$xmlfields = $this->prepare_xml();
					require(dirname(__FILE__).'/tmpl/import_xml.php');
				break;
				case 'JSON':
				case 'json':
					$jsonfields = $this->prepare_json();
					 require(dirname(__FILE__).'/tmpl/import_json.php');
				break;

				case 'remote':
					$this->prepare_remote(0);
					require(dirname(__FILE__).'/tmpl/import_remote.php');
				break;

				default:
				throw new Exception(JText::_('PLZ_SELECT_DATA_SOURCE'));
				return false;
				break;
			}

		}
		
		/*
		 * Method to prepare csv document before import
		 *
		 */
		
		function prepare_csv()
		{
			jimport('joomla.filesystem.file');
			$input = JFactory::getApplication()->input;
			$files = $input->files->get('file');
			$path = JFactory::getApplication()->input->get("path", '', 'RAW');
			$server = JFactory::getApplication()->input->get("server", 'remote', 'STRING');
			
			if( count($files)>1 || (count($files)==1 && !empty($files[0]["tmp_name"])) ){
				$newPaths = array();
				foreach($files as $id=>$file){
					$file_name    = str_replace(' ', '', JFile::makeSafe($file['name']));		
					$file_tmp     = $file["tmp_name"];
					if( (filesize($file_tmp) == 0) and empty($path) )	{
						throw new Exception(JText::_('PLZ_SELECT_FILE'));
						return false;
					}
					if( !empty($file) && ($file['error'] == 0) && is_uploaded_file($file['tmp_name']) ){
						if(filesize($file_tmp)>0) {
							$ext = strrchr($file_name, '.');
							if($ext <> '.csv'){
								throw new Exception(JText::_('PLZ_UPLOAD_CSV_FILE'));
								return false;
							}
							$new_path = JPATH_ROOT.'/components/com_vdata/uploads/'.JFilterOutput::stringURLSafe($this->profile->title).'0'.$id.''.$ext;
							$newPaths[] = $new_path;
							if(!JFile::upload($file_tmp, $new_path)){
								throw new Exception(JText::_('UPLOAD_CSV_FAILED'));
								return false;
							}
							if(!is_file($new_path))    {
								throw new Exception(JText::_('COULD_NOT_ACCESS'));
								return false;
							}
							if(!is_readable($new_path)){
								throw new Exception(JText::_('NOT_READABLE_FILE'));
								return false;
							}
						}
					}
				}
				$session = JFactory::getSession();
				$session->set('file_path', $newPaths);
				$session->set('iofile', $newPaths);
				$fp = @fopen($newPaths[0], "r");
				return $csvfields = $this->getCsvHeader($fp);
			}
			else
			{
				if($server=='local'){
					$path = JPATH_ROOT.'/'.$path;
				}
				elseif($server=='absolute'){
					set_time_limit(0);
					//profile name as file name
					$newPath = JPATH_ROOT.'/'.'components/com_vdata/uploads/'.$this->profile->title.'.csv';
					if(!copy($path, $newPath)){
						$copy_error = error_get_last();
						throw new Exception($copy_error['message']);
						return false;
					}
					$path = $newPath;
				}
				else{
					set_time_limit(0);
					//copy remote file using ftp
					$inputFtp = JFactory::getApplication()->input->getVar('ftp', array(),'ARRAY');
					if( empty($inputFtp['ftp_host']) || empty($inputFtp['ftp_user']) || empty($inputFtp['ftp_pass']) || empty($inputFtp['ftp_file']) ){
						throw new Exception(JText::_('VDATA_EMPTY_FTP_CREDENTIALS'));
						return false;
					}
					$ext = strrchr($inputFtp['ftp_file'], '.');
					if($ext <> '.csv'){
						throw new Exception(JText::_('PLZ_UPLOAD_CSV_FILE'));
						return false;
					}
					$ftp = $this->ftpConnection($inputFtp);
					if($ftp->result=='error'){
						throw new Exception($ftp->error);
						return false;
					}
					$remotePath = rtrim($inputFtp['ftp_directory'], '/').'/'.$inputFtp['ftp_file'];
					$path = JPATH_ROOT.'/'.'components/com_vdata/uploads/'.$inputFtp['ftp_file'].'.csv';
					$ftpHelper = $ftp->ftpHelper;
					if(!$ftpHelper->download($path, $remotePath)){
						throw new Exception($ftpHelper->getMessage());
						return false;
					}
				}
				$session = JFactory::getSession();
				$session->set('file_path', array($path));
				try{
					$fp = @fopen($path, "r");
					if ( !$fp ) {
						$open_error = error_get_last();
						throw new Exception($open_error['message']);
						return false;
					}
				} catch(Exception $e){
					throw new Exception($e->getMessage());
					return false;
				}
				
				// $fp = @fopen($path, "r");
				return $csvfields = $this->getCsvHeader($fp);
			}
		}
		
		function check_blank($values)
		{
			$blank = 0;
			if(is_array($values)){
				foreach($values as $value){
					if(empty($value))
						$blank += 1;	
				}
				return (count($values) == $blank) ? TRUE :FALSE;
			}elseif(is_object($values)){
				foreach($values as $key => $value) {
					if(empty($value))
						$blank += 1;
				}
				return (count(get_object_vars ($values)) == $blank) ? TRUE :FALSE;
			}else{
				if(!empty($values))return TRUE;
			}
			return FALSE;
		}
		
		function getCsvHeader($fp)
		{
			$delimiter = $this->getDelimiter();
			$enclosure = $this->getEnclosure();
			$csv = array();
			$csvfields = fgetcsv($fp, 100000, $delimiter, $enclosure);
			$csv[] = $csvfields;
			
			//if child record are stored in column then return $csv
			$config = $this->getConfig();
			$csv_child = json_decode($config->csv_child);
			if($csv_child->csv_child==1)
				return $csv;
			
			$head_length = count($csvfields);
			$blank_count =0;//count consecutive blank lines
			$tracker = 1;
			//get the heading of child tables
			while($heading = fgetcsv($fp, 100000, $delimiter, $enclosure))
			{
				$blank = $this->check_blank($heading);
				if($blank)
				{
					$blank_count += 1;							
				}
				else
				{
					if( ($blank_count < $tracker) && ($blank_count != 0) )//$blank_count == 1
					{
						break;//break at the start of base table's second record
					}
					if($tracker == $blank_count)
					{
						$temp = array();
						foreach($heading as $val)
						{
							if(!empty($val))
							{
								$temp[] = $val;
							}
						}
						$csv[] = $temp;
						$tracker += 1;//expected blank lines to get child table's heading if exists
					}
					
					//
					if( $blank_count > $tracker ){
						$temp = array();
						for($ct = 1; $ct<=($blank_count-$tracker); $ct++)
							$csv[] = $temp;
						foreach($heading as $val){
							if(!empty($val))
								$temp[] = $val;
						}
						$csv[] = $temp;
						$tracker += 1;
					}
					
					$blank_count = 0;
				}
			}
			return $csv;
		}
		
		function load_csv_columns()
		{
			JSession::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALID_TOKEN').'"}' );
			$db = JFactory::getDbo();
			$this->getProfile();
			$obj = new stdClass();
			$obj->result = 'success';
			$obj->html = '';
			$query = 'show fields FROM '.$db->quoteName($this->profile->params->table);
			$db->setQuery( $query );

			if($db->getErrorNum())	{
				$obj->error = $db->getErrorMsg();
				return $obj;
			}
			$cols = $db->loadObjectList();
			
			$session = JFactory::getSession();
			$importitem = $session->get('importitem');
			
			//if it's not new schedule,load it's field
			if(!empty($importitem->id)){
				$query = "select * from #__vd_schedules where id=".$importitem->id;
				$db->setQuery($query);
				$schedule_task = $db->loadObject();
				$schedule_columns = json_decode($schedule_task->columns);
			}
			else{
				$schedule_columns = null;
			}
			
			$delimiter = $this->getDelimiter();
			$enclosure = $this->getEnclosure();
			
			$obj->html .= '<tr class="columns"><td colspan="2">'.JText::_('COLUMNS').'</td></tr>';
			
			$paths = $session->get('file_path');
			$path = $paths[0];
			$session->set('file_path', $paths);
			$fp = @fopen($path, "r");
			$heading = fgetcsv($fp, 100000, $delimiter, $enclosure);
			foreach($cols as $col)
			{
				$obj->html .= '<tr data-column='.$col->Field.'>';
				$obj->html .= '<td width="200"><label class="hasTip" title="">'.$col->Field.'</label></td>';
				$obj->html .= '<td><select class="column" name="csvfield['.$col->Field.']">
							<option value="">'.JText::_('Select Field').'</option>';
				foreach($heading as $i=>$data)
				{
					$obj->html .= '<option value="'.$i.'"';
					if(!empty($schedule_columns) && property_exists($schedule_columns, 'quick') && ($schedule_columns->quick==0) && property_exists($schedule_columns->fields, $col->Field) && ($schedule_columns->fields->{$col->Field}==$i)){
						if($schedule_columns->fields->{$col->Field}!=""){
							$obj->html .= 'selected="selected"';
						}
					}
					elseif(!empty($schedule_columns) && property_exists($schedule_columns, 'quick') && ($schedule_columns->quick==1) && (strtolower($col->Field) == strtolower($data))){
						$obj->html .= 'selected="selected"';
					}
					elseif(empty($schedule_columns) && (strtolower($col->Field) == strtolower($data))){
						$obj->html .= 'selected="selected"';
					}
					$obj->html .= '>'.$data.'</option>';
				}				
				$obj->html .= '</select></td>';	
				$obj->html .= '</tr>';
			}
			jexit(json_encode($obj));
		}
		
		/*
		 * Method to prepare xml document before import
		 *
		 */
		
		function prepare_xml()
		{
			jimport('joomla.filesystem.file');
			$input = JFactory::getApplication()->input;
			$file = $input->files->get('file');
			$file = !empty($file)? $file[0]: $file;
			
			$path = JFactory::getApplication()->input->get("path", '', 'RAW');
			$server = JFactory::getApplication()->input->get("server", 'remote', 'STRING');
			
			$file_name    = str_replace(' ', '', JFile::makeSafe($file['name']));		
			$file_tmp     = $file["tmp_name"];

			$session = JFactory::getSession();
			if(!empty($file) && ($file['error'] == 0) && is_uploaded_file($file['tmp_name']) ) {
				if(filesize($file_tmp) == 0 and empty($path))	{
					throw new Exception(JText::_('PLZ_SELECT_FILE'));
					return false;
				}
				if(filesize($file_tmp)>0) {
					$ext = strrchr($file_name, '.');
					if($ext <> '.xml') {
						throw new Exception(JText::_('PLZ_UPLOAD_XML_FILE'));
						return false;
					}
					$new_path = JPATH_ROOT.'/components/com_vdata/uploads/'.JFilterOutput::stringURLSafe($this->profile->title).''.$ext;
					$session->set('file_path', $new_path);
					$session->set('iofile', $new_path);
					if(!JFile::upload($file_tmp, $new_path)) {
						throw new Exception(JText::_('UPLOAD_XML_FAILED'));
						return false;
					}
					if(!is_file($new_path)) {
						throw new Exception(JText::_('COULD_NOT_ACCESS'));
						return false;
					}
					if(!is_readable($new_path)) {
						throw new Exception(JText::_('NOT_READABLE_FILE'));
						return false;
					}	
				}
			}
			else {
				if($server=='local') {
					$path = JPATH_ROOT.'/'.$path;
				}
				elseif($server=='absolute'){
					set_time_limit(0);
					$newPath = JPATH_ROOT.'/'.'components/com_vdata/uploads/'.$this->profile->title.'.xml';
					if(!copy($path, $newPath)){
						$copy_error = error_get_last();
						throw new Exception($copy_error['message']);
						return false;
					}
					$path = $newPath;
				}
				else{
					set_time_limit(0);
					//copy remote file using ftp
					$inputFtp = JFactory::getApplication()->input->getVar('ftp', array(),'ARRAY');
					if( empty($inputFtp['ftp_host']) || empty($inputFtp['ftp_user']) || empty($inputFtp['ftp_pass']) || empty($inputFtp['ftp_file']) ){
						throw new Exception(JText::_('VDATA_EMPTY_FTP_CREDENTIALS'));
						return false;
					}
					$ext = strrchr($inputFtp['ftp_file'], '.');
					if($ext <> '.xml') {
						throw new Exception(JText::_('PLZ_UPLOAD_XML_FILE'));
						return false;
					}
					$ftp = $this->ftpConnection($inputFtp);
					if($ftp->result=='error'){
						throw new Exception($ftp->error);
						return false;
					}
					$remotePath = rtrim($inputFtp['ftp_directory'], '/').'/'.$inputFtp['ftp_file'];
					$path = JPATH_ROOT.'/'.'components/com_vdata/uploads/'.$inputFtp['ftp_file'].'.xml';
					$ftpHelper = $ftp->ftpHelper;
					if(!$ftpHelper->download($path, $remotePath)){
						throw new Exception($ftpHelper->getMessage());
						return false;
					}
				}
				$session->set('file_path', $path);
				try{
					$fp = @fopen(trim($path), "r");
					if ( !$fp ) {
						$open_error = error_get_last();
						throw new Exception($open_error['message']);
						return false;
					}
				} catch(Exception $e){
					throw new Exception($e->getMessage());
					return false;
				}
			}
			try{
				$xmltags = $this->getRootTags($session->get('file_path'));
			}
			catch(Exception $e){
				throw new Exception($e->getMessage());
				return false;
			}
			
			$xmlfields = $xmltags;
			// $xmlfields['test'] = $xmltags;
			
			return $xmlfields;
		}
		
		function getRootTags($path, $root=''){
			
			$rootTags = array();
			if(class_exists('XMLReader')){
				$xml = new XMLReader();
			}
			else{
				throw new Exception(JText::_('XMLREADER_NOT_FOUND'));
				return false;
			}
			
			$xml->open($path);
			$start_capture = empty($root) ? true : false;
			$tmp_root = array();
			
			$nodePath = array();
			if(!empty($root)) {
				$rootPath = explode('||', $root);
				if(strrpos( $rootPath[count($rootPath)-1], '-' )!==false){
					$lastTag = explode('-', $rootPath[count($rootPath)-1]);
					$lastTagIndex = $lastTag[1];
					$lastTagName = $lastTag[0];
					$rootPath[count($rootPath)-1] = $lastTagName;
				}
				$nodePath = $rootPath;
				$root = isset($lastTagName)?$lastTagName:$root;
				
			}
			
			while($xml->read()){
				switch($xml->nodeType) {
					case XMLReader::ELEMENT: {

						if($start_capture){
							if(!empty($root)){
								
								array_push($nodePath, $xml->name);
								$hierarchy = implode('||', $nodePath);
							}
							else{
								array_push($tmp_root, $xml->name);
								$hierarchy = implode('||', $tmp_root);
							}
							
							
							$rootTags[$xml->name] = $hierarchy;

						}
						
						if( !empty($root) && ($xml->name==$root) ){
							$start_capture = true;
						}
						break;
					}

					case XMLReader::END_ELEMENT: {
						
						if(!empty($root)){
							$hierarchy = implode('||', $nodePath);
							$rootTags = array_diff($rootTags, array($xml->name=>$hierarchy));
						}
						else{
							$hierarchy = implode('||', $tmp_root);
							$rootTags = array_diff($rootTags, array($xml->name=>$hierarchy));
						}
						if($start_capture){
							break 2;
						}
					}
				}
			}
			
			$xml->close();
			return $rootTags;
			
		}
		
		function loadXmlTags($selected = false){
			
			JSession::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALID_TOKEN').'"}' );
			$session = JFactory::getSession();
			$path = $session->get('file_path', '');
			$root = JFactory::getApplication()->input->getVar('root', '', 'STRING');
			$isRoot = JFactory::getApplication()->input->getVar('isRoot', 0, 'STRING');
			$obj = new stdClass();
			// $obj->html = '<option value="">'.JText::_('SELECT_FIELD').'</option>';
			$obj->html = '';
			$obj->result = 'success';
			if(empty($path) || empty($root)){
				jexit(json_encode($obj));
			}
			$child_tags = $this->getXmlTags($path, $root, $selected,true, $isRoot);
			if(!empty($child_tags->childs)){
				$obj->html .= $child_tags->childs;
			}
			
			jexit(json_encode($obj));
			
		}
		
		
		function getXmlTags($path, $root='', $selected=false, $attribs=true, $isRoot=0){
			
			$obj = new stdClass();
			$rootAttr = '';
			$childs = '';
			
			$tmp_root = explode('||', $root);
			
			// $last_index = preg_replace('/\D+/', '', $tmp_root[count($tmp_root)-1]);
			
			$last_index=0;
			if(strrpos( $tmp_root[count($tmp_root)-1], '-' )!==false){
				$last_index = substr( $tmp_root[count($tmp_root)-1], strrpos( $tmp_root[count($tmp_root)-1], '-' )+1 );
	// $last_index = substr( $tmp_root[count($tmp_root)-1], -1, strrpos( $tmp_root[count($tmp_root)-1], '-' ) );
				$last_index = ($last_index!='' && is_numeric($last_index))?$last_index:0;
			}
			
			
			// $tmp_root = preg_replace('/\d+/', '', $tmp_root);
			foreach($tmp_root as $k=>$tmp){
				// $tmp_root[$k] = preg_replace('/\d+/', '', $tmp);//concatenated index
				$tmp_root[$k] = preg_replace('/-\d+/', '', $tmp);//hyphenate index
			}
			$target_root = $tmp_root[count($tmp_root)-1];
			$hierarchy = $tmp_root;
			
			$node = new XMLReader();
			$node->open($path);
			$start_capture = false;
			$hasAttr = false;
			$nodePath = array();
			$depth = 0;
			
			
			$last_root = (count($tmp_root)>1) ? $tmp_root[count($tmp_root)-2]:$target_root;
			$ctracker = false;
			
			while($node->read()){
				if($node->nodeType === XmlReader::ELEMENT){
					
					if($ctracker){
						
						if($last_index!=0){
							$last_index = ($last_index>0)?($last_index-1):0;
							continue;
						}
					}
					if($node->name==$last_root){
						$ctracker = true;
					}
					
					if(in_array($node->name, $tmp_root)){
						
						array_shift($tmp_root);
					}
					
					if($start_capture==true){
						
						$i=0;
						$val = implode('||',array_merge($nodePath, array($node->name))).'-'.$i;
						// $childs .= '<option value="'.implode(':',array_merge($nodePath, array($node->name))).'" data-attr="'.($node->hasAttributes? 'yes': 'no').'"';
						$childs .= '<option value="'.$val.'" data-attr="'.($node->hasAttributes? 'yes': 'no').'"';
						
						if($val==$selected){
							$childs .= ' selected="selected"';
						}
						$nameAttr = $node->getAttribute('name');
						$nodeNameAttr = !empty($nameAttr)? $nameAttr:'';
						
						$nodeName = $node->name;
						$elements_depth = $node->depth;
						$prev_depth = $node->depth;
						
						$sameNode = 0;
						
						while($node->read()){
							if($node->nodeType === XmlReader::ELEMENT){
								
								if($node->depth == $elements_depth){
									
									if($nodeName==$node->name && $sameNode>0){
										continue;
									}
									
									
									$newNodeName = empty($nodeNameAttr)?$nodeName:$nodeName.':'.$nodeNameAttr;
									
									if($prev_depth>$elements_depth){
										$childs .= ' data-child="yes">'.$newNodeName.'</option>';
									}
									else{
										$childs .= ' data-child="no">'.$newNodeName.'</option>';
									}
									
									$hasNameAttr = $node->getAttribute('name');
									
									if(!isset($hasNameAttr) && $nodeName==$node->name){
										$sameNode++;
										continue;
									}
									/* else{
										$sameNode = 0;
									} */
									
									$i++;
									
									$curPath = array_merge($nodePath, array($node->name));
									// $nodeValue = implode(':', $curPath);
									$nodeValue = implode('||', $curPath).'-'.$i;
									$childs .= '<option value="'.$nodeValue.'"'.' data-attr="'.($node->hasAttributes? 'yes': 'no').'"';
									if($nodeValue==$selected){
										$childs .= ' selected="selected"';
									}
									$nodeName = $node->name;
									
									$nameAttr = $node->getAttribute('name');
									$nodeNameAttr = !empty($nameAttr)? $nameAttr:'';
								}
								
								$prev_depth = $node->depth;
							}
							if($node->nodeType ===XMLReader::END_ELEMENT && $node->name==$target_root){
								
								$newNodeName = empty($nodeNameAttr)?$nodeName:$nodeName.':'.$nodeNameAttr;
								if($prev_depth>$elements_depth){
									$childs .= ' data-child="yes">'.$newNodeName.'</option>';
								}
								else{
									$childs .= ' data-child="no">'.$newNodeName.'</option>';
								}
								
								break;
							}
						}
						break;
					}
					
					if($node->name==$target_root && empty($tmp_root)){
						
						$start_capture = true;
						$parent = $node->name;
						
						//get root tag attributes
						if($node->hasAttributes){
							$hasAttr = true;
							while($node->moveToNextAttribute()) {
								$attrVal = implode('||',array_merge($nodePath, array($parent))).'.'.$node->name;
								
								$rootAttr .= '<option value="'.$attrVal.'"';
								if($attrVal==$selected){
									$rootAttr .= ' selected="selected"';
								}
								$rootAttr .= '>'.$node->name.'</option>';
								
							}
							$node->moveToElement();
						}
						if($node->isEmptyElement){
							break;
						}
						
					}
					
					if($node->depth==0){
						if(!in_array($node->name, $nodePath)){
							array_push($nodePath, $node->name);
						}
					}
					elseif($depth==$node->depth && in_array($node->name, $hierarchy)){
						if(!in_array($node->name, $nodePath)){
							array_push($nodePath, $node->name);
						}
					}
					elseif($depth!=$node->depth && in_array($node->name, $hierarchy) ){
						
						if(!in_array($node->name, $nodePath)){
							array_push($nodePath, $node->name);
						}
					}
					$depth = $node->depth;
				}
				
				if($node->nodeType ===XMLReader::END_ELEMENT && $node->name==$target_root && empty($tmp_root)){
					break;
				}
			}
			$node->close();
			if($isRoot){
				$tag_options = '<option value="custom_path">'.JText::_('VDATA_LOAD_NODE_FROM_PATH').'</option>';
			}
			else{
				$tag_options = '';
			}
			if(!empty($rootAttr) && $attribs){
				$tag_options .= '<optgroup label="Attribute">'.$rootAttr.'</optgroup>';
			}
			if(!empty($childs)){
				$tag_options .= '<optgroup label="Childs">'.$childs.'</optgroup>';
			}
			$obj->childs = $tag_options;
			return $obj;
		}
		
		/* 
		 * Method to get the child tag
		 * 
		 */
		
		function loadChildXmlTags(){
			
			JSession::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALID_TOKEN').'"}' );
			
			$session = JFactory::getSession();
			$path = $session->get('file_path', '');
			$obj = new stdClass();
			
			$root = JFactory::getApplication()->input->getVar('root', '', 'STRING');
			if(empty($root)){
				$obj->result = 'error';
				$obj->error = JText::_('SELECT_ROOT_TAG');
			}
			$obj = $this->getChildXmlTags($path, $root);
			jexit(json_encode($obj));
		}
		
		/* 
		 * Method to get the child tag of given root tag
		 * 
		 * @param string $path  		xml file path
		 * @param string $root  		root tag
		 * @param boolean $selected 	selected tag
		 * 
		 */
		
		function getChildXmlTags($path, $root, $selected=false){
			
			$obj = new stdClass();
			$tmp_root = explode('||', $root);
			$targetRoot = $tmp_root[count($tmp_root)-1];
			$targetRoot = explode('-', $targetRoot);
			$obj->result = 'success';
			$obj->roots = '';
			$obj->childs = '';
			
			$childRoots = $this->getRootTags($path, $root);
			
			
			if(!empty($childRoots)){
				
				foreach($childRoots as $key=>$child){
					$obj->roots .= '<option value="'.$child.'"';
					if($child==$selected){
						$obj->roots .= ' selected="selected"';
					}
					$obj->roots .= '>'.$key.'</option>';
				}
				
				return $obj;
			}
			
			$childTags = $this->getXmlTags($path, $root, $selected);
			if(!empty($childTags->childs)){
				$obj->childs .= $childTags->childs;
			}
			
			return $obj;
		}
		
		/* 
		 * Method to load quick profile options for xml source
		 * 
		 */
		 
		function load_xml_columns()
		{
			JSession::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALID_TOKEN').'"}' );
			$db = JFactory::getDbo();
			$this->getProfile();
			$obj = new stdClass();
			$obj->result = 'success';
			$obj->html = '';
			$query = 'show fields FROM '.$db->quoteName($this->profile->params->table);
			$db->setQuery( $query );

			if($db->getErrorNum())	{
				$obj->result = 'error';
				$obj->error = $db->getErrorMsg();
				jexit(json_encode($obj));
			}
			$cols = $db->loadObjectList();
			
			$session = JFactory::getSession();
			$importitem = $session->get('importitem');
			
			
			//if it's not new schedule,load it's field
			if(!empty($importitem->id)){
				$query = "select * from #__vd_schedules where id=".$importitem->id;
				$db->setQuery($query);
				$schedule_task = $db->loadObject();
				$schedule_columns = json_decode($schedule_task->columns);
			}
			else{
				$schedule_columns = null;
			}
			
			$obj->html .= '<tr class="columns"><td colspan="2">'.JText::_('COLUMNS').'</td></tr>';
			$session = JFactory::getSession();
			$path = $session->get('file_path');
			
			$root = JFactory::getApplication()->input->get('root', '', 'string');
			$isRoot = JFactory::getApplication()->input->get('isRoot', 0, 'INT');
			// $tags = $this->getXmlTags($path, $root);
			
			foreach($cols as $col)
			{
				$obj->html .= '<tr data-column='.$col->Field.'>';
				$obj->html .= '<td width="200"><label class="hasTip" title="">'.$col->Field.'</label></td>';
				$obj->html .= '<td><select class="xml_field get_childs load_attr" name="xmlfield['.$col->Field.'][]">';
				$obj->html .= '<option value="">'.JText::_('SELECT_FIELD').'</option>';
				$loadDirectPath = false;
				$xmlField = isset($schedule_columns->fields->{$col->Field})?$schedule_columns->fields->{$col->Field}:null;
				if($isRoot){
					$obj->html .= '<option value="custom_path"';
					if(!empty($xmlField) && (count($xmlField)==2) && ($xmlField[0]=='custom_path')){
						$loadDirectPath = true;
						$obj->html .= ' selected="selected"';
					}
					$obj->html .= '>'.JText::_('VDATA_LOAD_NODE_FROM_PATH').'</option>';
				}
				if( !empty($schedule_columns) && property_exists($schedule_columns, 'quick') && ($schedule_columns->quick==0) && !empty($xmlField) ){
					$options = $this->getXmlTags($path, $root, $xmlField);
					$obj->html .= $options->childs;
				}
				else{
					$options = $this->getXmlTags($path, $root, $col->Field);
					$obj->html .= $options->childs;
				}
				// $obj->html .= $tags->childs;
				$obj->html .= '</select>';
				if($loadDirectPath){
					$obj->html .= '<input type="text" name="xmlfield['.$col->Field.'][]" value="'.$xmlField[count($xmlField)-1].'"/>';
				}
				$obj->html .= '</td>';	
				$obj->html .= '</tr>';
			}
			
			jexit(json_encode($obj));
		}
		
		/*
		 * Method to prepare json document before import
		 *
		 */
		
		function prepare_json()
		{
			jimport('joomla.filesystem.file');
			$input = JFactory::getApplication()->input;
			$file = $input->files->get('file');
			$file = !empty($file)? $file[0]: $file;
			
			$path = JFactory::getApplication()->input->get("path", '', 'RAW');
			$server = JFactory::getApplication()->input->get("server", 'remote', 'STRING');

			$file_name    = str_replace(' ', '', JFile::makeSafe($file['name']));		
			$file_tmp     = $file["tmp_name"];
			
			if(!empty($file) && ($file['error'] == 0) && is_uploaded_file($file['tmp_name']) ){
				if(filesize($file_tmp)) {
					$ext = strrchr($file_name, '.');
					if($ext <> '.json')	{
						throw new Exception(JText::_('PLZ_UPLOAD_JSON_FILE'));
						return false;
					}
					$new_path = JPATH_ROOT.'/components/com_vdata/uploads/'.JFilterOutput::stringURLSafe($this->profile->title).''.$ext;
					$session = JFactory::getSession();
					$session->set('file_path', $new_path);
					$session->set('iofile', $new_path);
					
					if(!JFile::upload($file_tmp, $new_path)){
						throw new Exception(JText::_('UPLOAD_JSON_FAILED'));
						return false;
					}
					if(!is_file($new_path))    {
						throw new Exception(JText::_('COULD_NOT_ACCESS'));
						return false;
					}
					if(!is_readable($new_path)){
						throw new Exception(JText::_('NOT_READABLE_FILE'));
						return false;
					}
					
					try	{
						$jsonstring = @file_get_contents($new_path);
					}
					catch(Exception $e){
						throw new Exception($e->getMessage());
						return false;
					}
					// $jsonstring = file_get_contents($new_path);
				}
			}
			else{
				if($server=='local'){
					$path = JPATH_ROOT.'/'.$path;
				}
				elseif($server=='absolute'){
					set_time_limit(0);
					// copy file from remote server to local server, renaming file as profile name
					$newPath = JPATH_ROOT.'/'.'components/com_vdata/uploads/'.$this->profile->title.'.json';
					if(!copy($path, $newPath)){
						$copy_error = error_get_last();
						throw new Exception($copy_error['message']);
						return false;
					}
					$path = $newPath;
				}
				else{
					set_time_limit(0);
					//copy remote file using ftp
					$inputFtp = JFactory::getApplication()->input->getVar('ftp', array(),'ARRAY');
					if( empty($inputFtp['ftp_host']) || empty($inputFtp['ftp_user']) || empty($inputFtp['ftp_pass']) || empty($inputFtp['ftp_file']) ){
						throw new Exception(JText::_('VDATA_EMPTY_FTP_CREDENTIALS'));
						return false;
					}
					$ext = strrchr($inputFtp['ftp_file'], '.');
					if($ext <> '.json')	{
						throw new Exception(JText::_('PLZ_UPLOAD_JSON_FILE'));
						return false;
					}
					$ftp = $this->ftpConnection($inputFtp);
					if($ftp->result=='error'){
						throw new Exception($ftp->error);
						return false;
					}
					$remotePath = rtrim($inputFtp['ftp_directory'], '/').'/'.$inputFtp['ftp_file'];
					$path = JPATH_ROOT.'/'.'components/com_vdata/uploads/'.$inputFtp['ftp_file'].'.json';
					$ftpHelper = $ftp->ftpHelper;
					if(!$ftpHelper->download($path, $remotePath)){
						throw new Exception($ftpHelper->getMessage());
						return false;
					}
				}
				
				// store file path in session for ajax polling
				$session = JFactory::getSession();
				$session->set('file_path', $path);
			}
			return true;
		}
		
		function loadJsonFields() {
			
			JSession::checkToken() or jexit('{"result":"error", "error":"'.JText::_('INVALID_TOKEN').'"}');
			$session = JFactory::getSession();
			
			$root = JFactory::getApplication()->input->get('root', '', 'RAW');
			$file = $session->get('file_path', '');
			$result = $this->getJsonField($file, $root);
			
			jexit(json_encode($result));
		}
		
		function getJsonData($path){
			
			$obj = new stdClass();
			try{
				$fp = @fopen($path, "r");
				if ( !$fp ) {
					$open_error = error_get_last();
					$obj->result = false;
					$obj->error = $open_error['message'];
					return $obj;
				}
			} catch(Exception $e){
				$obj->result = false;
				$obj->error = $e->getMessage();
				return $obj;
			}
			
			try	{
				$jsonstring = @file_get_contents($path);
			}
			catch(Exception $e){
				$obj->result = false;
				$obj->error = $e->getMessage();
				return $obj;
			}
			
			utf8_encode($jsonstring);
			$jsonfields = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $jsonstring), true);
			
			if($jsonfields === false or json_last_error()!=JSON_ERROR_NONE)   {
				$obj->result = false;
				$obj->error = JText::_('INVALID_JSON');
				return $obj;
			}
			$obj->result = true;
			$obj->data = $jsonfields;
			
			return $obj;
		}
		
		/*
		 * 
		 */
		function getJsonField($file, $root='', $selected='') {
			
			$obj = new stdClass();
			$obj->html = '';
			$fieldPath = empty($root)?array():explode(':', $root);
			$data = $this->getJsonData($file);
			if($data->result==false){
				$obj->result = 'error';
				$obj->error = $data->error;
				return $obj;
			}
			$jsonfields = $data->data;
			foreach($jsonfields as $jsonKey=>$jsonField){
				$fields = $jsonField;
				break;
			}
			// $fields = $jsonfields[0];
			$level = 1;
			if(empty($fieldPath)){
				$obj->html .= $this->getJsonOptions($fields, $root, $selected);
			}
			else{
				$pathCount = count($fieldPath);
				foreach($fieldPath as $i=>$token){
					$newValue = $this->getJsonVal($fields, $token);
					if($newValue!=false){
						if(array_keys($newValue)===range(0,count($newValue)-1)){
							$fields = $newValue[0];
						}
						else{
							$fields = $newValue;
						}
					}
					else{
						break;
					}
					if($i==($pathCount-1)){
						$obj->html .= $this->getJsonOptions($fields, $root, $selected);
					}
				}
			}
			$obj->result = 'success';
			return $obj;
		}
		
		function getJsonVal($fields, $token){
			
			foreach($fields as $key=>$field){
				if($key==$token){
					return $field;
				}
			}
			return false;
		}
		
		function getJsonOptions($fields, $root, $selected=''){
			
			$html = '';
			foreach($fields as $key=>$field){
				
				$value = empty($root)?$key:$root.':'.$key;
				$html .= '<option value="'.$value.'"';
				if($value===$selected){
					$html .= ' selected="selected"';
				}
				if(is_array($field) || is_object($field)){
					$html .= ' data-child="yes"';
				}
				else{
					$html .= ' data-child="no"';
				}
				$html .= '>'.$key.'</option>';
			}
			return $html;
		}
		
		
		function load_json_columns(){
			
			JSession::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALID_TOKEN').'"}' );
			$db = JFactory::getDbo();
			$this->getProfile();
			$obj = new stdClass();
			$obj->result = 'success';
			$obj->html = '';
			$query = 'show fields FROM '.$db->quoteName($this->profile->params->table);
			$db->setQuery( $query );
			
			$root = JFactory::getApplication()->input->get('root', '', 'RAW');
			$isRoot = JFactory::getApplication()->input->get('isRoot', 0, 'INT');
			$isRootSelected = false;
			if(isset($root) && $root!=''){
				$selectedRoot = ($root=='load_field')?'':$root;
				$isRootSelected = true;
			}
			else{
				$selectedRoot = '';
			}
			
			if($db->getErrorNum())	{
				$obj->error = $db->getErrorMsg();
				return $obj;
			}
			$cols = $db->loadObjectList();
			
			$session = JFactory::getSession();
			$importitem = $session->get('importitem');
			
			//if it's not new schedule,load it's field
			if(!empty($importitem->id)){
				$query = "select * from ".$db->quoteName('#__vd_schedules')." where id=".$importitem->id;
				$db->setQuery($query);
				$schedule_task = $db->loadObject();
				$schedule_columns = json_decode($schedule_task->columns);
			}
			else{
				$schedule_columns = null;
			}
			
			
			$session = JFactory::getSession();
			$path = $session->get('file_path');
			
			$data = $this->getJsonData($path);
			if($data->result==false){
				$obj->result = 'error';
				$obj->error = $data->error;
				return $obj;
			}
			$jsonfields = $data->data;
			
			$obj->html .= '<tr class="columns"><td colspan="2">'.JText::_('COLUMNS').'</td></tr>';
			foreach($cols as $col)
			{
				$obj->html .= '<tr data-column='.$col->Field.'>';
				$obj->html .= '<td width="200"><label class="hasTip" title="">'.$col->Field.'</label></td>';
				$obj->html .= '<td><select class="jsonfield subfields" name="jsonfield['.$col->Field.'][]">';
				$obj->html .= '<option value="">'.JText::_('SELECT_FIELD').'</option>';
				$loadDirectPath = false;
				$jsonField = isset($schedule_columns->fields->{$col->Field})?$schedule_columns->fields->{$col->Field}:null;
				if($isRoot){
					$obj->html .= '<option value="custom_path"';
					if(!empty($jsonField) && (count($jsonField)==2) && ($jsonField[0]=='custom_path')){
						$loadDirectPath = true;
						$obj->html .= ' selected="selected"';
					}
					$obj->html .= '>'.JText::_('VDATA_LOAD_NODE_FROM_PATH').'</option>';
				}
				$fieldSelected = false;
				if( !empty($schedule_columns) && property_exists($schedule_columns, 'quick') && ($schedule_columns->quick==0) && property_exists($schedule_columns->fields, $col->Field) ){
					$fieldSelected = isset($schedule_columns->fields->{$col->Field})?$schedule_columns->fields->{$col->Field}:false;
					$selectedField = ($fieldSelected)?$fieldSelected[0]:false;
				}
				elseif( !empty($schedule_columns) && property_exists($schedule_columns, 'quick') && ($schedule_columns->quick==1) ){
					$selectedField = $col->Field;
				}
				else{
					$selectedField = $col->Field;
				}
				if($isRootSelected){
					$res = $this->getJsonField($path, $selectedRoot, $selectedField);
					$obj->html .= $res->html;
				}
				$obj->html .= '</select>';
				
				if($fieldSelected && count($fieldSelected)>1){
					$obj->html .= '<span class="child_field">';
					if($loadDirectPath){
						$obj->html .= '<input type="text" name="jsonfield['.$col->Field.'][]" value="'.$jsonField[count($jsonField)-1].'"/>';
					}
					else{
						for($i=1;$i<count($fieldSelected);$i++){
							$obj->html .= '<span class="child_field">';
							$obj->html .= '<select class="jsonfield subfields" name="jsonfield['.$col->Field.'][]">';
								$obj->html .= '<option value="">'.JText::_('SELECT_FIELD').'</option>';
								$res = $this->getJsonField($path, $fieldSelected[$i-1], $fieldSelected[$i]);
								$obj->html .= $res->html;
							$obj->html .= '</select>';
							$obj->html .= '</span>';
						}
					}
					$obj->html .= '</span>';
				}
				
				$obj->html .= '</td>';	
				$obj->html .= '</tr>';
			}
			jexit(json_encode($obj));
		}
		
		/*
		 * Method to prepare remote database before import
		 *
		 */
		function prepare_remote($iotype)
		{
			$app = JFactory::getApplication();
			$option = array();
			
			$db_loc = JFactory::getApplication()->input->get('db_loc','', 'STRING');
			if($db_loc=='localdb'){
				$config = JFactory::getConfig();
				$hd_config = $this->getConfig();
				$dbconfig = json_decode($hd_config->dbconfig);
				if($dbconfig->local_db==1){
					$option['driver'] = $config->get('dbtype');
					$option['host'] = $config->get('host');
					$option['user'] = $config->get('user');
					$option['password'] = $config->get('password');
					$option['database'] = $config->get('db');
					$option['prefix'] = $config->get('dbprefix');
				}
				else{
					if( property_exists($dbconfig, 'driver') && property_exists($dbconfig, 'host') && property_exists($dbconfig, 'user') && property_exists($dbconfig, 'password') && property_exists($dbconfig, 'database') && property_exists($dbconfig, 'dbprefix') ){
						$option['driver'] = $dbconfig->driver;
						$option['host'] = $dbconfig->host;
						$option['user'] = $dbconfig->user;
						$option['password'] = $dbconfig->password;
						$option['database'] = $dbconfig->database;
						if(!empty($dbconfig->dbprefix))
							$option['prefix'] = $dbconfig->dbprefix;
						
					}
				}
			}
			else{
				$option['driver'] = JFactory::getApplication()->input->get("driver", '', 'RAW');
				$option['host'] = JFactory::getApplication()->input->get("host", '', 'RAW');
				$option['user'] = JFactory::getApplication()->input->get("user", '', 'RAW');
				$option['password'] = JFactory::getApplication()->input->get("password", '', 'RAW');
				$option['database'] = JFactory::getApplication()->input->get("database", '', 'RAW');
				$option['prefix'] = JFactory::getApplication()->input->get("dbprefix", '', 'RAW');
			}
			
			$session = JFactory::getSession();
			$session->set('remote_details', $option);
			
			//store insert/update operation in session variable
			if($iotype==1){
				$opr = JFactory::getApplication()->input->getInt('operation', 1);
				$session->set('expo_op', $opr);
			}
			
			try {
				$this->remotedb = JDatabaseDriver::getInstance( $option );
				$this->remotedb->connect();
				
				$query = 'show tables';
				$this->remotedb->setQuery( $query );
				$this->remotetables = $this->remotedb->loadColumn();
				
			}
			catch (RuntimeException $e) {
				throw new Exception($e->getMessage());
				return false;
			}
			return true;
		}
		
		function batchImport()
		{
			$this->getProfile();
			$session = JFactory::getSession();
			$importitem = $session->get('importitem');
			$source = $importitem->source;
			$config = $this->getConfig();
			$csv_child = json_decode($config->csv_child);
			switch($source) 
			{
				case 'remote':
					if($this->profile->quick)
						$res = $this->batch_import_remote_quick();
					else
						$res = $this->batch_import_remote();
					if($res->result == 'success'){
						return $res;
					}
					else{
						$session->clear('remote_details');
						$session->clear('importitem');
						$session->clear('qty');
						return $res;
					}
				break;
				case 'csv':
					if($this->profile->quick)
						$res = $this->batch_import_csv_quick();
					else{
						if($csv_child->csv_child==2)
							$res = $this->batch_import_csv();
						else
							$res = $this->batch_import_csv1();
					}
					if($res->result == 'success'){
						return $res;
					}
					else{
						$session->clear('file_path');
						$session->clear('importitem');
						$session->clear('qty');
						//
						$session->clear('fl_counter');
						
						return $res;
					}
				break;
				case 'xml':
					if($this->profile->quick)
						$res = $this->batch_import_xml_quick();
					else
						$res = $this->batch_import_xml();
					if($res->result == 'success'){
						return $res;
					}
					else{
						$session->clear('file_path');
						$session->clear('importitem');
						$session->clear('qty');
						return $res;
					}
				break;
				case 'json':
					if($this->profile->quick)
						$res = $this->batch_import_json_quick();
					else
						$res = $this->batch_import_json();
					if($res->result == 'success'){
						return $res;
					}
					else{
						$session->clear('file_path');
						$session->clear('importitem');
						$session->clear('qty');
						return $res;
					}
				break;
				default:
					$res = new stdClass();
					$res->result = 'error';
					$res->error = JText::_('IMPORT_DATA_FAIL');
					return $res;
				break;
			}
		}
		
		function batch_import_remote_quick()
		{
			$obj = new stdClass();
			$session = JFactory::getSession();
			$config = $this->getConfig();
			$local_db = $this->getDbc();
			
			$quick = JFactory::getApplication()->input->getInt('quick',0);
			$quick_field = JFactory::getApplication()->input->get('fields', array(), 'ARRAY');
			$offset = JFactory::getApplication()->input->getInt('offset');
			
			$dids = $session->get('dids', array());
			
			$query_local = $local_db->getQuery(true);
			try{
				$option = $session->get('remote_details');
				$remote_db = JDatabaseDriver::getInstance( $option );
				$remote_db->connect();
				$query_remote = $remote_db->getQuery(true);
				if($offset==0)
					JLog::add(JText::_('CONNECTED_TO_REMOTE_DB'), JLog::INFO, 'com_vdata');
			}
			catch (RuntimeException $e) {
				$obj->result = 'error';
				$obj->error = $e.getMessage();
				JLog::add(JText::sprintf('REMOTE_DB_CONNECT_ERROR',$e.getMessage()), JLog::ERROR, 'com_vdata');
				return $obj;
			}
			
			$remote_table = JFactory::getApplication()->input->get('table', '', 'RAW');

			$query_local = 'SHOW KEYS FROM '.$local_db->quoteName($this->profile->params->table).' WHERE Key_name = "PRIMARY"';
			$local_db->setQuery( $query_local );
			// $key = $local_db->loadObjectList();
			$key = $local_db->loadAssocList('Column_name');
			if(property_exists($this->profile->params, 'unqkey') && !empty($this->profile->params->unqkey)){
				$primary = $this->profile->params->unqkey;
			}
			elseif( !empty($key) ){
				$primary = array_keys($key);
			}
			else
				$primary = null;
			
			$query = 'show fields FROM '.$local_db->quoteName($this->profile->params->table);
			$local_db->setQuery( $query );
			$cols = $local_db->loadObjectList();
			
			$size_query = $remote_db->getQuery(true);
			
			if($quick)
			{
				$query_remote = "select * from ".$remote_db->quoteName($remote_table);
				$remote_db->setQuery($query_remote, $offset, $config->limit);
				$results = $remote_db->loadObjectList();
				
				$size_query = "select count(*) from ".$remote_db->quoteName($remote_table);
				$remote_db->setQuery($size_query);
				$size = $remote_db->loadResult();
				
				$n = 0;
				if(count($results)==0)	{
					//log info
					JLog::add(JText::sprintf('IMPORT_PROFILE', $this->profile->title), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('IMPORT_TABLE', $this->profile->params->table), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('IMPORT_FORMAT', 'DATABASE'), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('IMPORT_RECORD_COUNT', $session->get('qty',0)), JLog::INFO, 'com_vdata');
					if( ($this->profile->params->operation==2) || ($this->profile->params->operation==3) ){
						$deleteQry = 'DELETE FROM '.$local_db->quoteName($this->profile->params->table). ' WHERE %s';
						$whereCon = array();
						foreach($dids as $did){
							if(is_array($did)){
								$where = array();
								foreach($did as $pkey=>$pval){
									$where[] = $local_db->quoteName($pkey).' <> '.$local_db->quote($pval);
								}
								$whereCon[] = ' ('.implode(' AND ', $where).')';
							}
							else{
								$whereCon[] = ' '.$local_db->quoteName($primary).' <> '.$local_db->quote($did);
							}
						}
						//apply delete filter for delete operation
						if($this->profile->params->operation==3){
							//apply filters
							if(isset($this->profile->params->filters->column)){
								$filterValues = $this->importDeleteFilter($local_db, $this->profile->params);
								if(!empty($filterValues)){
									$whereCon[] = $filterValues;
								}
							}
						}
						if(!empty($whereCon)){
							$local_db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
							$res = $local_db->execute();
						}
					}
					$session->clear('dids');
					
					//send notification if done from site part
					if(JFactory::getApplication()->isSite()){
						$notify = $this->sendNotificationEmail( $this->profile->iotype,0 , array('schedule_id'=>"",'schedule_title'=>"","schedule_qry"=>"",'profile_id'=>$this->profile->id,'profile_title'=>$this->profile->title,"count"=>$session->get('qty',0), "format"=>"db") );
					}
					
					$obj->result='error';
					$obj->size = $size;
					$obj->qty = $session->get('qty',0);
					$obj->error = JText::_('NO_RECORD_TO_IMPORT');
					JLog::add(JText::_('NO_RECORD_TO_IMPORT'), JLog::INFO, 'com_vdata');
					return $obj;
				}
				
				// JLog::add(JText::_('STARTING_IMPORT'), JLog::INFO, 'com_vdata');
				
				foreach($results as $result)
				{
					$insert = new stdClass();
					if($this->profile->params->operation==3){
						//apply primary key and custom filters
						$where = array();
						$base_in_id = array();
						if(isset($this->profile->params->unqkey)){
							foreach($this->profile->params->unqkey as  $key){
								if($quick_field[$key]!="" && property_exists($result, $quick_field[$key])){
									$where[] = "(".$local_db->quoteName($key).' = '.$local_db->quote($result->{$quick_field[$key]}).")";
									$base_in_id[$key] = $result->{$quick_field[$key]};
								}
							}
						}
						if(!empty($where)){
							$statement = 'SELECT count(*) FROM ' . $local_db->quoteName($this->profile->params->table) . ' WHERE %s';
							$query = $local_db->getQuery(true);
							$local_db->setQuery(sprintf($statement, implode(' AND ', $where)));
							$resultCount = $local_db->loadResult();
							if($resultCount>0 && !empty($base_in_id)){
								$dids[] = $base_in_id;
							}
						}
					}
					else{
						foreach($cols as $key=>$value){
							if(property_exists($result, $value->Field)){
								$insert->{$value->Field} = $result->{$value->Field};
							}
						}
					}
					$isNew = true;
					$oldData = array();
					$where = array();
					if(!empty($primary)){
						foreach($primary as $keyCol){
							if(isset($insert->{$keyCol})){
							$where[] = $local_db->quoteName($keyCol).'='.$local_db->quote($insert->{$keyCol});
							}
						}
						if(!empty($where)){
							$query = "select * from ".$local_db->quoteName($this->profile->params->table)." where ".implode(' AND ', $where);;
							$local_db->setQuery($query);
							$oldData = $local_db->loadAssoc();
							$isNew = empty($oldData) ? true : false;
						}
					}
					
					if($this->profile->params->operation==3){
						//delete data operation
					}
					elseif($this->profile->params->operation==1){
						if(!$isNew){
							// JLog::add(JText::sprintf('RECORD_EXISTS',$primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
							JLog::add(JText::sprintf('RECORD_ALREADY_EXISTS',implode(',',$where)), JLog::ERROR, 'com_vdata');
							continue;
						}
						
						//capture events before insert
						if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
							
							//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
							$response = $this->captureEventsOnRecord( $insert, 'before', $isNew , array('pk'=>$primary ,'old'=>$oldData));
							if(in_array(false, $response)){
								JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
								continue;
							}
								
						}

						if(!$local_db->insertObject($this->profile->params->table, $insert)){
							JLog::add(JText::sprintf('INSERT_FAIL',$local_db->stderr()), JLog::ERROR, 'com_vdata');
							$obj->result = 'error';
							$obj->error = $local_db->stderr();
							return $obj;
						}
						
						if($this->getAffected($local_db) > 0){
							$n++;
							//capture events after insert
							if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
								$response = $this->captureEventsOnRecord( $insert, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
							}
						}	
					}
					else{
						if(empty($primary)){ //|| empty($insert->{$primary})
							$obj->result = 'error';
							$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
							return $obj;
						}
						else{
							foreach($primary as $pkey){
								if(!isset($insert->{$pkey}) || empty($insert->{$pkey})){
									$obj->result = 'error';
									$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
									return $obj;
								}
							}
						}
						
						//capture events before update
						if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
							//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
							$response = $this->captureEventsOnRecord( $insert, 'before',$isNew , array('pk'=>$primary ,'old'=>$oldData));
							if(in_array(false, $response)){
								JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
								continue;
							}
						}
						if($isNew){
							// JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
							JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
							if(!$local_db->insertObject($this->profile->params->table, $insert)){
								JLog::add(JText::sprintf('INSERT_FAIL',$local_db->stderr()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $local_db->stderr();
								return $obj;
							}
						}
						else{
							if(!$local_db->updateObject($this->profile->params->table, $insert, $primary)){
								JLog::add(JText::sprintf('INSERT_FAIL',$local_db->stderr()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $local_db->stderr();
								return $obj;
							}
						}
						$base_in_id = array();
						foreach($primary as $pkey){
							if(isset($insert->{$pkey})){
								$base_in_id[$pkey] = $insert->{$pkey};
							}
						}
						$dids[] = $base_in_id;
						// $dids[] = $insert->{$primary};
						if($this->getAffected($local_db) > 0){
							$n++;
							//capture events after update
							if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
								$response = $this->captureEventsOnRecord( $insert, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
							}
						}
					}
					
				}
			}
			else{
				if(!count($quick_field)){
					$obj->result = "error";
					$obj->error = JText::_('SELECT_FIELDS_TO_IMPORT');
					return $obj;
				}
				foreach($quick_field as $a=>$qf){
					if($qf!=""){
						$field[$a]=$local_db->quoteName($qf);
					}
				}
				
				$query_remote = "select ".implode(',',$field)." from ".$remote_db->quoteName($remote_table);
				$remote_db->setQuery($query_remote, $offset, $config->limit);
				$results = $remote_db->loadObjectList();
				
				$size_query = "select count(*) from ".$remote_db->quoteName($remote_table);
				$remote_db->setQuery($size_query);
				$size = $remote_db->loadResult();
				
				$n = 0;
				
				if(count($results)==0){
					//log info
					JLog::add(JText::sprintf('IMPORT_PROFILE', $this->profile->title), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('IMPORT_TABLE', $this->profile->params->table), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('IMPORT_FORMAT', 'DATABASE'), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('IMPORT_RECORD_COUNT', $session->get('qty',0)+$n), JLog::INFO, 'com_vdata');
					if( ($this->profile->params->operation==2) || ($this->profile->params->operation==3) ){
						$deleteQry = 'DELETE FROM '.$local_db->quoteName($this->profile->params->table). ' WHERE %s';
						$whereCon = array();
						foreach($dids as $did){
							if(is_array($did)){
								$where = array();
								foreach($did as $pkey=>$pval){
									$where[] = $local_db->quoteName($pkey).' <> '.$local_db->quote($pval);
								}
								$whereCon[] = ' ('.implode(' AND ', $where).')';
							}
							else{
								$whereCon[] = ' '.$local_db->quoteName($primary).' <> '.$local_db->quote($did);
							}
						}
						//apply delete filter for delete operation
						if($this->profile->params->operation==3){
							//apply filters
							if(isset($this->profile->params->filters->column)){
								$filterValues = $this->importDeleteFilter($local_db, $this->profile->params);
								if(!empty($filterValues)){
									$whereCon[] = $filterValues;
								}
							}
						}
						if(!empty($whereCon)){
							$local_db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
							$res = $local_db->execute();
						}
					}
					$session->clear('dids');
					
					//send notification if done from site part
					if(JFactory::getApplication()->isSite()){
						$notify = $this->sendNotificationEmail( $this->profile->iotype,0 , array('schedule_id'=>"",'schedule_title'=>"","schedule_qry"=>"",'profile_id'=>$this->profile->id,'profile_title'=>$this->profile->title,"count"=>($session->get('qty',0)+$n), "format"=>"db") );
					}
					$obj->totalRecordInFile = $size;
					$obj->result='error';
					$obj->size = $size;
					$obj->qty = $session->get('qty',0) + $n;
					$obj->error = JText::_('NO_RECORD_TO_IMPORT');
					JLog::add(JText::_('NO_RECORD_TO_IMPORT'), JLog::INFO, 'com_vdata');
					return $obj;
				}
				
				// JLog::add(JText::_('STARTING_IMPORT'), JLog::INFO, 'com_vdata');
				foreach($results as $result)
				{
					$insert = new stdClass();
					if($this->profile->params->operation==3){
						//apply primary key and custom filters
						$where = array();
						$base_in_id = array();
						if(isset($this->profile->params->unqkey)){
							foreach($this->profile->params->unqkey as  $key){
								if($quick_field[$key]!="" && property_exists($result, $quick_field[$key])){
									$where[] = "(".$local_db->quoteName($key).' = '.$local_db->quote($result->{$quick_field[$key]}).")";
									$base_in_id[$key] = $result->{$quick_field[$key]};
								}
							}
						}
						if(!empty($where)){
							$statement = 'SELECT count(*) FROM ' . $local_db->quoteName($this->profile->params->table) . ' WHERE %s';
							$query = $local_db->getQuery(true);
							$local_db->setQuery(sprintf($statement, implode(' AND ', $where)));
							$resultCount = $local_db->loadResult();
							if($resultCount>0 && !empty($base_in_id)){
								$dids[] = $base_in_id;
							}
						}
					}
					else{
						foreach($result as $key=>$value){
							if($quick_field[$key]!=""){
								$insert->{$quick_field[$key]} = $value;
							}
							
						}
					}
					$isNew = true;
					$oldData = array();
					$where = array();
					if( !empty($primary) ){
						foreach($primary as $keyCol){
							if(isset($insert->{$keyCol})){
							$where[] = $local_db->quoteName($keyCol).'='.$local_db->quote($insert->{$keyCol});
							}
						}
						if(!empty($where)){
							$query = 'SELECT * FROM '.$local_db->quoteName($this->profile->params->table).' WHERE '.implode(' AND ', $where);
							$local_db->setQuery( $query );
							$oldData = $local_db->loadAssoc();
							$isNew = empty($oldData) ? true : false;
						}
					}
					if($this->profile->params->operation==3){
						//delete data operation
					}
					elseif($this->profile->params->operation==1){
						if(!$isNew){
							// JLog::add(JText::sprintf('RECORD_EXISTS',$primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
							JLog::add(JText::sprintf('RECORD_ALREADY_EXISTS',implode(',',$where)), JLog::ERROR, 'com_vdata');
							continue;
						}
						//capture events before insert
						if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
							//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
							$response = $this->captureEventsOnRecord( $insert, 'before',$isNew, array('pk'=>$primary ,'old'=>$oldData) );
							if(in_array(false, $response)){
								JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
								continue;
							}
						}
						
						if(!$local_db->insertObject($this->profile->params->table, $insert))	
						{
							JLog::add(JText::sprintf('INSERT_FAIL',$local_db->stderr()), JLog::ERROR, 'com_vdata');
							$obj->result = 'error';
							$obj->error = $local_db->stderr();
							return $obj;
						}
						if($this->getAffected($local_db) > 0){
							$n++;
							//capture events after insert
							if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
								$response = $this->captureEventsOnRecord( $insert, 'after',$isNew, array('pk'=>$primary ,'success'=>true) );
							}
							
						}
							
					}
					else{
						if(empty($primary)){
							$obj->result = 'error';
							$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
							return $obj;
						}
						else{
							foreach($primary as $pkey){
								if(!isset($insert->{$pkey}) || empty($insert->{$pkey})){
									$obj->result = 'error';
									$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
									return $obj;
								}
							}
						}
						
						//capture events before update
						if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
							//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
							$response = $this->captureEventsOnRecord( $insert, 'before',$isNew, array('pk'=>$primary ,'old'=>$oldData));
							if(in_array(false, $response)){
								JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
								continue;
							}
						}
						if($isNew){
							// JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
							JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
							if(!$local_db->insertObject($this->profile->params->table, $insert))	
							{
								JLog::add(JText::sprintf('INSERT_FAIL',$local_db->stderr()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $local_db->stderr();
								return $obj;
							}
						}
						else{
							if(!$local_db->updateObject($this->profile->params->table, $insert, $primary))
							{
								JLog::add(JText::sprintf('INSERT_FAIL',$local_db->stderr()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $local_db->stderr();
								return $obj;
							}
						}
						$base_in_id = array();
						foreach($primary as $pkey){
							if(isset($insert->{$pkey})){
								$base_in_id[$pkey] = $insert->{$pkey};
							}
						}
						$dids[] = $base_in_id;
						// $dids[] = $insert->{$primary};
						
						if($this->getAffected($local_db) > 0){
							$n++;
							//capture events after update
							if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
								$response = $this->captureEventsOnRecord( $insert, 'after', $isNew, array('pk'=>$primary ,'success'=>true) );
							}
							
						}	
					}
				}
			}
			
			$session->set('dids', $dids);
			
			$obj->totalRecordInFile = $size;
			$obj->result='success';
			$obj->size = $size;
			$obj->offset = $offset + $config->limit;
			$qty = $session->get('qty',0) + $n;
			$session->set('qty',$qty);
			return $obj;
		}
		
		function batch_import_remote(){
			
			$obj = new stdClass();
			$session = JFactory::getSession();
			$config = $this->getConfig();
			$local_db = $this->getDbc();
			$query_local = $local_db->getQuery(true);
			try{
				$option = $session->get('remote_details');
				$remote_db = JDatabaseDriver::getInstance( $option );
				$remote_db->connect();
				$query_remote = $remote_db->getQuery(true);
			}
			catch (RuntimeException $e) {
				$obj->result = 'error';
				$obj->error = $e.getMessage();
				return $obj;
			}
			
			$dids = $session->get('dids', array());
			$join_dids = $session->get('join_dids', array());
			
			$remote_table = JFactory::getApplication()->input->get('table', '', 'RAW');
			$remote_fields = JFactory::getApplication()->input->get('fields', array(), 'ARRAY');
			$images_import = JFactory::getApplication()->input->get('images', array(), 'RAW');
			// $remote_primary = JFactory::getApplication()->input->get('unique', '', 'STRING');
			
			$offset = JFactory::getApplication()->input->getInt('offset');
			
			$query_local = 'SHOW KEYS FROM '.$local_db->quoteName($this->profile->params->table).' WHERE Key_name = "PRIMARY"';
			$local_db->setQuery( $query_local );
			// $key = $local_db->loadObjectList();
			$key = $local_db->loadAssocList('Column_name');
			
			if(property_exists($this->profile->params, 'unqkey') && !empty($this->profile->params->unqkey)){
				$primary = $this->profile->params->unqkey;
			}
			elseif(!empty($key) ){//&& (count($key)==1)
				// $primary = $key[0]->Column_name;
				$primary = array_keys($key);
			}
			else{
				$primary = null;
			}
			$query_remote = "select * from ".$remote_db->QuoteName($remote_table);
			$remote_db->setQuery($query_remote, $offset, $config->limit);
			$remote_data_set = $remote_db->loadObjectList();
			
			$size_query = $remote_db->getQuery(true);
			$size_query = "select count(*) from ".$remote_db->quoteName($remote_table);
			$remote_db->setQuery($size_query);
			$size = $remote_db->loadResult();
			
			if(count($remote_data_set) == 0)	{
				//log info
				JLog::add(JText::sprintf('IMPORT_PROFILE', $this->profile->title), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('IMPORT_TABLE', $this->profile->params->table), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('IMPORT_FORMAT', 'DATABASE'), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('IMPORT_RECORD_COUNT', $session->get('qty',0)), JLog::INFO, 'com_vdata');
				if( ($this->profile->params->operation==2) || ($this->profile->params->operation==3) ){
					$deleteQry = 'DELETE FROM '.$local_db->quoteName($this->profile->params->table). ' WHERE %s';
					$whereCon = array();
					foreach($dids as $did){
						if(is_array($did)){
							$where = array();
							foreach($did as $pkey=>$pval){
								$where[] = $local_db->quoteName($pkey).' <> '.$local_db->quote($pval);
							}
							$whereCon[] = ' ('.implode(' AND ', $where).')';
						}
						else{
							$whereCon[] = ' '.$local_db->quoteName($primary).' <> '.$local_db->quote($did);
						}
					}
					//apply delete filter for delete operation
					if($this->profile->params->operation==3){
						//apply filters
						if(isset($this->profile->params->filters->column)){
							$filterValues = $this->importDeleteFilter($local_db, $this->profile->params);
							if(!empty($filterValues)){
								$whereCon[] = $filterValues;
							}
						}
					}
					if(!empty($whereCon)){
						$local_db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
						$res = $local_db->execute();
					}
				}
				$session->clear('dids');
				$session->clear('join_dids');
				
				//send notification if done from site part
				if(JFactory::getApplication()->isSite()){
					$notify = $this->sendNotificationEmail( $this->profile->iotype,0 , array('schedule_id'=>"",'schedule_title'=>"","schedule_qry"=>"",'profile_id'=>$this->profile->id,'profile_title'=>$this->profile->title,"count"=>($session->get('qty',0)), "format"=>"db") );
				}
				
				$obj->result='error';
				$obj->size = $size;
				$obj->qty = $session->get('qty',0);
				$obj->error = JText::_('NO_RECORD_TO_IMPORT');
				return $obj;
			}
			
			$n = 0;
			foreach($remote_data_set as $key=>$remote_data)
			{
				$isNew = true;
				$insert = new stdClass();
				$cached_data = new stdClass();
				$oldData = array();
				$stance = false;
				$intance_column = '';
				if($this->profile->params->operation==3){
					//apply primary key and custom filters
					$where = array();
					$base_in_id = array();
					if(isset($this->profile->params->unqkey)){
						foreach($this->profile->params->unqkey as  $key){
							if($remote_fields[$key]!="" && property_exists($remote_data, $remote_fields[$key])){
								$where[] = "(".$local_db->quoteName($key).' = '.$local_db->quote($remote_data->{$remote_fields[$key]}).")";
								$base_in_id[$key] = $remote_data->{$remote_fields[$key]};
							}
						}
					}
					if(!empty($where)){
						$statement = 'SELECT count(*) FROM ' . $local_db->quoteName($this->profile->params->table) . ' WHERE %s';
						$query = $local_db->getQuery(true);
						$local_db->setQuery(sprintf($statement, implode(' AND ', $where)));
						$resultCount = $local_db->loadResult();
						if($resultCount>0 && !empty($base_in_id)){
							$dids[] = $base_in_id;
						}
					}
				}
				else{
					foreach($this->profile->params->fields as $column=>$field){
						switch($field->data){
							case 'file' :
								if(empty($remote_fields[$column])){
									break;
								}
								//fetching reference data
								if(is_array($remote_fields[$column]))
								{
									if(!empty($remote_fields[$column]["table"]) && !empty($remote_fields[$column]["column"]) && !empty($remote_fields[$column]["column2"]) && !empty($remote_fields[$column]["column1"]) ){
										
										$query_remote = 'SELECT '.$remote_db->quoteName($remote_fields[$column]["column"]).' FROM '.$remote_db->quoteName($remote_fields[$column]["table"]).' WHERE '.$remote_db->quoteName($remote_fields[$column]["column2"]).' = '.$remote_db->quote($remote_data->{$remote_fields[$column]["column1"]});
										$remote_db->setQuery($query_remote);
										if( $ref_val = $remote_db->loadResult() ){
											$insert->{$column} = $ref_val;
											$cached_data->{$column} = $ref_val;
											break;
										}
										else{
											$obj->result = 'error';
											$obj->error = $remote_db->stderr();
											return $obj;
										}
									}
								}
								else {
									$insert->{$column} = $cached_data->{$column} = $this->getFilteredValue($field, $remote_data->{$remote_fields[$column]});
									if( isset($field->format) && ($field->format=='email') && ($insert->{$column}==false) ){
										JLog::add(JText::sprintf('VDATA_EMAIL_VALIDATION_FAILED', $column), JLog::ERROR, 'com_vdata');
										unset($insert->{$column}, $cached_data->{$column});
									}
									if($field->format=="encrypt"){
										$cached_data->{$column} = $remote_data->{$remote_fields[$column]};
									}
								}
								if($field->format == "image"){
									$filename = $insert->{$column};$destination = rtrim($field->location,'/').'/'. basename($filename);
											
									$image_source=$path_type='';
									if(isset($images_import['root'][$column])){
										$image_source = isset($images_import['root'][$column]['image_source'])?$images_import['root'][$column]['image_source']:'';
										$path_type = isset($images_import['root'][$column]['path_type'])?$images_import['root'][$column]['path_type']:'';											
									}	
									if(!$image = $this->uploadImage($filename,$destination,$image_source,$path_type,$err)){
													
										$insert->{$column} = $cached_data->{$column} = $destination;
										$obj->result = 'error';
										$obj->error = $err;
										return $obj;
									}//jexit();
								} 
							break;
							case 'defined' :
								$defined = $field->default;
								$hd_remote_pattern = '/@vdRemote:[\w]*[.?\w]*/';
								$hdRemote = preg_match_all($hd_remote_pattern, $defined, $local_matches);
								if( ($hdRemote!==FALSE) ){
									foreach($local_matches[0] as $mk=>$match){
										if(!empty($match)){
											$fn = explode(':', $match);
											if(!empty($fn[1])){
												$info = explode('.', $fn[1]);
												if(count($info)==1){
													if(property_exists($remote_data, $info[0])){
														$defined = preg_replace('/'.$match.'/', $remote_data->$info[0], $defined);
													}
												}
												elseif(count($info)==2){
														if(!empty($info[0]) && !empty($info[1]) && property_exists($this->profile->params->fields, $info[0]) && ($this->profile->params->fields->{$info[0]}->data=='reference') ) {
														if(property_exists($remote_data, $info[0])){
														$query = 'select '.$remote_db->quoteName($info[1]).' from '.$remote_db->quoteName($this->profile->params->fields->{$info[0]}->table).' where '.$remote_db->quoteName($this->profile->params->fields->{$info[0]}->on).'='.$remote_db->quote($remote_data->$info[0]);
														$remote_db->setQuery($query);
														$rdata = $remote_db->loadResult();
														$defined = preg_replace('/'.$match.'/', $rdata, $defined);
														}
													}
												}
											}
										}
									}
								}
								$defined = $this->getDefinedValue($field->default, $this->profile->params->fields, (array)$remote_data, $remote_fields);
								
								$insert->{$column} = $defined;
								$cached_data->{$column} = $defined;
							break;
							case 'reference' :
								//one to one relation
								if( empty($remote_fields[$column]['table']) || empty($remote_fields[$column]['on']) || empty($remote_fields[$column]['value']) || empty($remote_fields[$column]['refcol']) ){
									break;
								}
								$remote_ref_field = array();
								foreach($remote_fields[$column]['refcol'] as $key=>$rfield){
									if(!empty($rfield)){
										$remote_ref_field[] = $rfield;
									}
								}
								
								$query_remote = "select ".implode(',',$remote_ref_field)." from ".$remote_db->quoteName($remote_fields[$column]['table'])." "; 
								$query_remote .= "where ".$remote_db->quoteName($remote_fields[$column]['on'])."=".$remote_data->{$remote_fields[$column]['value']};
								$remote_db->setQuery($query_remote);
								$values = $remote_db->loadObject();
								
								if(!empty($values)){
									$query_local = "select ".$local_db->quoteName($field->on)." from ".$local_db->quoteName($field->table)." where 1=1";
									foreach($remote_ref_field as $ref_field){
										$query_local .= " and ".$local_db->quoteName($ref_field)." = ".$local_db->quote($values->{$ref_field});
									}
									$local_db->setQuery($query_local);
									$ref_value = $local_db->loadResult();
									if(!empty($ref_value)){
										$insert->{$column} = $ref_value;
										$cached_data->{$column} = $ref_value;
									}
									else{
										JLog::add(JText::sprintf('LOCAL_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
									}
								}
								else{
									JLog::add(JText::sprintf('REMOTE_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
								}
							break;
							case 'asset_reference':
								$stance = true;
								$insert->{$column} = 0;
								$intance_column = $field->on;
								$instance_component = $field->table;
								
								if($remote_fields[$column]['rules']!=""){
									
									if(is_array($remote_fields[$column]['rules']))
									{
										if(!empty($remote_fields[$column]['rules']["table"])&& !empty($remote_fields[$column]['rules']["column"]) && !empty($remote_fields[$column]['rules']["column2"]) && !empty($remote_fields[$column]['rules']["column1"]) ){
											
											$query_remote = 'SELECT '.$remote_db->quoteName($remote_fields[$column]['rules']["column"]).' FROM '.$remote_db->quoteName($remote_fields[$column]['rules']["table"]).' WHERE '.$remote_db->quoteName($remote_fields[$column]['rules']["column2"]).' = '.$remote_db->quote($remote_data->{$remote_fields[$column]['rules']["column1"]});
											$remote_db->setQuery($query_remote);
											if( $ref_val = $remote_db->loadResult() )
											{
												$insert->rules = json_decode($ref_val, true);
												break;
											}
											else
											{
												$obj->result = 'error';
												$obj->error = $remote_db->stderr();
												return $obj;
											}
											
										}
									}
									else{
										$insert->rules = json_decode($remote_data->{$remote_fields[$column]['rules']}, true);
									}
								}
							break;
						}
					}
				}
				//check if data already exists
				if( !empty($primary) )
				{	$where =  array();
					foreach($primary as $keyCol){
						if(isset($insert->{$keyCol})){
						$where[] = $local_db->quoteName($keyCol).'='.$local_db->quote($insert->{$keyCol});
						}
					}
					if(!empty($where)){
						$query_local = 'SELECT * FROM '.$local_db->quoteName($this->profile->params->table).' WHERE '.implode(' AND ', $where);
						$local_db->setQuery( $query_local );
						$oldData = $local_db->loadAssoc();
						$isNew = empty($oldData) ? true : false;
					}
				}
				$afterState = false;
				
				if($this->profile->params->operation==3){
					//delete data operation
				}
				elseif($this->profile->params->operation==1){
					if(!$isNew){
						// JLog::add(JText::sprintf('RECORD_EXISTS',$primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
						JLog::add(JText::sprintf('RECORD_ALREADY_EXISTS',implode(',',$where)), JLog::ERROR, 'com_vdata');
						continue;
					}
					//capture events before update
					if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
						//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
						$response = $this->captureEventsOnRecord( $cached_data, 'before',$isNew, array('pk'=>$primary ,'old'=>$oldData));
						if(in_array(false, $response)){
							JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
							continue;
						}
					}
					$base_join_val = null;
					if($stance){
						$component = JText::_($instance_component);
						$component_name = $component;
						if (strpos(strtolower($component), "com_") ===FALSE){
							$component_name = "com_".strtolower($component_name); 
						}
						if (strpos($component, "com_")===0){
							$component = str_replace("com_", '',strtolower($component)); 
						}
						JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
						
						$core_tables = array('category', 'content', 'menu', 'module', 'module');
						if(in_array($intance_column, $core_tables)){
							$row = JTable::getInstance($intance_column);
						}
						else{
							$row = JTable::getInstance($intance_column, $component.'Table');
						}
						
						if(property_exists($row, 'parent_id')){
							$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
							@$row->setLocation($parent_id, 'last-child');
						}
						
						if (!$row->bind((array)$insert)) {
							JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
							$obj->result = 'error';
							$obj->error = $row->getError();
							return $obj;
						}
						if (!$row->check()) {
							JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
							$obj->result = 'error';
							$obj->error = $row->getError();
							return $obj;
						}
						if (!$row->store()){
							JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
							$obj->result = 'error';
							$obj->error = $row->getError();
							return $obj;
						}
						if(!empty($primary)){
							$base_in_id = array();
							foreach($primary as $pkey){
								if(property_exists($row, $pkey)){
									$base_in_id[$pkey] = $row->{$pkey};
								}
							}
						}
						/* if(property_exists($row, $primary)){
							$base_in_id = $row->{$primary};
						} */
						$n++;
						$afterState = true;
						if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($row, $this->profile->params->joins->column1[0])){
							$base_join_val = $row->{$this->profile->params->joins->column1[0]};
						}
					}
					else{
						if(isset($this->profile->params->component->table) && !empty($this->profile->params->component->table)){
							$component = JText::_($this->profile->params->component->value);
							$component_name = $component;
							if (strpos(strtolower($component), "com_") ===FALSE){
								$component_name = "com_".strtolower($component_name); 
							}
							if (strpos(strtolower($component), "com_") ===0){
								$component = str_replace("com_", '',strtolower($component)); 
							}
							JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
							$core_tables = array('category', 'content', 'menu', 'module');
							if(in_array($this->profile->params->component->table, $core_tables)){
								$row = JTable::getInstance($this->profile->params->component->table);
							}
							else{
								$row = JTable::getInstance($this->profile->params->component->table, $component.'Table');
							}
							
							$row->load(null);
							if(property_exists($row, 'parent_id')){
								$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
								@$row->setLocation($parent_id, 'last-child');
							}
							
							if (!$row->bind((array)$insert)) {
								JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $row->getError();
								return $obj;
							}
							
							if (!$row->check()) {
								JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $row->getError();
								return $obj;
							}
							
							if (!$row->store()){
								JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $row->getError();
								return $obj;
							}
							$n++;
							$afterState = true;
							if(!empty($primary)){
								$base_in_id = array();
								foreach($primary as $pkey){
									if(property_exists($row, $pkey)){
										$base_in_id[$pkey] = $row->{$pkey};
									}
								}
							}
							/* if(!empty($primary) && property_exists($row, $primary)){
								$base_in_id = $row->{$primary};
							} */
							if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($row, $this->profile->params->joins->column1[0])){
								$base_join_val = $row->{$this->profile->params->joins->column1[0]};
							}
						}
						else{
							if($local_db->insertObject($this->profile->params->table, $insert)){
								if($this->getAffected($local_db)> 0){
									$n++;
									$afterState = true;
								}
								$base_in_id = array();
								foreach($primary as $pkey){
									if(isset($insert->{$pkey})){
										$base_in_id[$pkey] = $insert->{$pkey};
									}
								}
										
								if((!empty($primary) && count($primary)==1) || (isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && !property_exists($insert, $profile_params->joins->column1[0]))){
									$id =$local_db->insertid();
									if($primary[0]!='id'){
										$query = 'SHOW KEYS FROM '.$local_db->quoteName($this->profile->params->table).' WHERE Key_name = "PRIMARY"';
										$local_db->setQuery( $query );
										// $key = $local_db->loadObjectList();
										$key = $local_db->loadAssocList('Column_name');
										$pri = array_keys($key)[0];
										$cached_data->{$pri} = $insert->{$pri} = $id;
									}else{
										$cached_data->{$primary[0]} = $insert->{$primary[0]} = $local_db->insertid();
									}
								}
								if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($insert, $this->profile->params->joins->column1[0])){
									$base_join_val = $insert->{$this->profile->params->joins->column1[0]};
								}
								
							}
							else{
								JLog::add(JText::sprintf('INSERT_FAIL',$local_db->stderr()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $local_db->stderr();
								return $obj;
							}
						}
					}
					if($afterState){
						//capture events after update
						if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
							//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
							if(!empty($primary)){
								foreach($primary as $pkey){
									if(property_exists($cached_data, $pkey) && isset($base_in_id[$pkey])){
										$cached_data->{$pkey} = $base_in_id[$pkey];
									}
								}
							}
							/* if(!property_exists($cached_data, $primary)){
								$cached_data->$primary = $base_in_id;
							} */
							$response = $this->captureEventsOnRecord( $cached_data, 'after', $isNew, array('pk'=>$primary ,'success'=>true) );
						}
					}
				}
				else{
					if(empty($primary)){
						$obj->result = 'error';
						$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
						return $obj;
					}
					else{
						foreach($primary as $pkey){
							if(!isset($insert->{$pkey}) || empty($insert->{$pkey})){
								$obj->result = 'error';
								$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
								return $obj;
							}
						}
					}
					//capture events before update
					if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
						//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
						$response = $this->captureEventsOnRecord( $cached_data, 'before',$isNew , array('pk'=>$primary ,'old'=>$oldData));
						if(in_array(false, $response)){
							JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
							continue;
						}
					}
					
					$base_join_val = null;
					if($stance){
						$component = JText::_($instance_component);
						$component_name = $component;
						if (strpos(strtolower($component), "com_") ===FALSE){
							$component_name = "com_".strtolower($component_name); 
						}
						if (strpos(strtolower($component), "com_") ===0){
							$component = str_replace("com_", '',strtolower($component)); 
						}
						JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
						$core_tables = array('category', 'content', 'menu', 'module');
						if(in_array($intance_column, $core_tables)){
							$row = JTable::getInstance($intance_column);
						}
						else{
							$row = JTable::getInstance($intance_column, $component.'Table');
						}
						
						
						$loadFields = array();
						foreach($primary as $pkey){
							if(isset($insert->{$pkey}) && !empty($insert->{$pkey})){
								$loadFields[$pkey] = $insert->{$pkey};
							}
						}
						$row->load($loadFields);
						
						if(property_exists($row, 'parent_id')){
							if (isset($row->id) && isset($insert->parent_id) && $row->id==1 && $insert->parent_id==1)
								$insert->parent_id = 0; 
							$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
							if ($row->parent_id!=$parent_id)
								@$row->setLocation($parent_id, 'last-child');
						}
						// $row->load($insert->{$primary});
						if($isNew){
							// JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
							JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
						}
						
						if (!$row->bind((array)$insert)) {
							JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
							$obj->result = 'error';
							$obj->error = $row->getError();
							return $obj;
						}
						if (!$row->check()) {
							JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
							$obj->result = 'error';
							$obj->error = $row->getError();
							return $obj;
						}
						if (!$row->store()){
							JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
							$obj->result = 'error';
							$obj->error = $row->getError();
							return $obj;
						}
						$n++;
						$afterState = true;
						if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($row, $this->profile->params->joins->column1[0])){
							$base_join_val = $row->{$this->profile->params->joins->column1[0]};
						}
					}
					else{
						
						if($isNew){
							// JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
							JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
							if(isset($this->profile->params->component->table) && !empty($this->profile->params->component->table)){
								$component = JText::_($this->profile->params->component->value);
								$component_name = $component;
								if (strpos(strtolower($component), "com_") ===FALSE){
									$component_name = "com_".strtolower($component_name); 
								}
								if (strpos(strtolower($component), "com_") ===0){
									$component = str_replace("com_", '',strtolower($component)); 
								}
								JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
								$core_tables = array('category', 'content', 'menu', 'module');
								if(in_array($this->profile->params->component->table, $core_tables)){
									$row = JTable::getInstance($this->profile->params->component->table);
								}
								else{
									$row = JTable::getInstance($this->profile->params->component->table, $component.'Table');
								}
								
								$row->load(null);
								if(property_exists($row, 'parent_id')){
									$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
									@$row->setLocation($parent_id, 'last-child');
								}
								if (!$row->bind((array)$insert)) {
									JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $row->getError();
									return $obj;
								}
								if (!$row->check()) {
									JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $row->getError();
									return $obj;
								}
								if (!$row->store()){
									JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $row->getError();
									return $obj;
								}
								$n++;
								$afterState = true;
								/* if(!empty($primary) && property_exists($row, $primary)){
									$base_in_id = $row->{$primary};
								} */
								if(!empty($primary)){
									$base_in_id = array();
									foreach($primary as $pkey){
										if(property_exists($row, $pkey)){
											$base_in_id[$pkey] = $row->{$pkey};
										}
									}
								}
								if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($row, $this->profile->params->joins->column1[0])){
									$base_join_val = $row->{$this->profile->params->joins->column1[0]};
								}
							}
							else{
								if($local_db->insertObject($this->profile->params->table, $insert)){
									if($this->getAffected($local_db)> 0){
										$n++;
										$afterState = true;
									}
									$base_in_id = array();
									foreach($primary as $pkey){
										if(isset($insert->{$pkey})){
											$base_in_id[$pkey] = $insert->{$pkey};
										}
									}
											
									if((!empty($primary) && count($primary)==1) || (isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && !property_exists($insert, $profile_params->joins->column1[0]))){
										$id =$local_db->insertid();
										if($primary[0]!='id'){
											$query = 'SHOW KEYS FROM '.$local_db->quoteName($this->profile->params->table).' WHERE Key_name = "PRIMARY"';
											$local_db->setQuery( $query );
											// $key = $local_db->loadObjectList();
											$key = $local_db->loadAssocList('Column_name');
											$pri = array_keys($key)[0];
											$cached_data->{$pri} = $insert->{$pri} = $id;
										}else{
											$cached_data->{$primary[0]} = $insert->{$primary[0]} = $local_db->insertid();
										}
									}
									if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($insert, $this->profile->params->joins->column1[0])){
										$base_join_val = $insert->{$this->profile->params->joins->column1[0]};
									}
								}
								else{
									JLog::add(JText::sprintf('INSERT_FAIL',$local_db->stderr()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $local_db->stderr();
									return $obj;
								}
							}
						}
						else{
							if(isset($this->profile->params->component->table) && !empty($this->profile->params->component->table)){
								$component = JText::_($this->profile->params->component->value);
								$component_name = $component;
								if (strpos(strtolower($component), "com_") ===FALSE){
									$component_name = "com_".strtolower($component_name); 
								}
								if (strpos(strtolower($component), "com_") ===0){
									$component = str_replace("com_", '',strtolower($component)); 
								}
								JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
								$core_tables = array('category', 'content', 'menu', 'module');
								if(in_array($this->profile->params->component->table, $core_tables)){
									$row = JTable::getInstance($this->profile->params->component->table);
								}
								else{
									$row = JTable::getInstance($this->profile->params->component->table, $component.'Table');
								}
								$loadFields = array();
								foreach($primary as $pkey){
									if(isset($insert->{$pkey}) && !empty($insert->{$pkey})){
										$loadFields[$pkey] = $insert->{$pkey};
									}
								}
								$row->load($loadFields);
								// $row->load($insert->{$primary});
								if(property_exists($row, 'parent_id')){
									if (isset($row->id) && isset($insert->parent_id) && $row->id==1 && $insert->parent_id==1)
										$insert->parent_id = 0; 
									$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
									if ($row->parent_id!=$parent_id){
										@$row->setLocation($parent_id, 'last-child');
									}
								}
								if (!$row->bind((array)$insert)) {
									JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $row->getError();
									return $obj;
								}
								if (!$row->check()) {
									JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $row->getError();
									return $obj;
								}
								if (!$row->store()){
									JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $row->getError();
									return $obj;
								}
								$n++;
								$afterState = true;
								/* if(!empty($primary) && property_exists($row, $primary)){
									$base_in_id = $row->{$primary};
								} */
								if(!empty($primary)){
									$base_in_id = array();
									foreach($primary as $pkey){
										if(property_exists($row, $pkey)){
											$base_in_id[$pkey] = $row->{$pkey};
										}
									}
								}
								if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($row, $this->profile->params->joins->column1[0])){
									$base_join_val = $row->{$this->profile->params->joins->column1[0]};
								}
							}
							else{
								if($local_db->updateObject($this->profile->params->table, $insert, $primary)){
									if($this->getAffected($local_db)> 0){
										$n++;
										$afterState = true;
									}
									// $base_in_id = $insert->{$primary};
									$base_in_id = array();
									foreach($primary as $pkey){
										if(isset($insert->{$pkey})){
											$base_in_id[$pkey] = $insert->{$pkey};
										}
									}
									if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 ){
										if(property_exists($insert, $this->profile->params->joins->column1[0])){
											$base_join_val = $insert->{$this->profile->params->joins->column1[0]};
										}else{
											//check if record already exists
											$where = array();
											if( !empty($primary)  ){
												foreach($primary as $keyCol){
													if(isset($insert->{$keyCol})){
													$where[] = $local_db->quoteName($keyCol).'='.$local_db->quote($insert->{$keyCol});
													}
												}
												if(!empty($where)){
													$query = 'SELECT '.$this->profile->params->joins->column1[0].' FROM '.$local_db->quoteName($this->profile->params->table).' WHERE '.implode(' AND ', $where);
													$local_db->setQuery( $query );
													$base_join_val = $cached_data->{$this->profile->params->joins->column1[0]} = $insert->{$this->profile->params->joins->column1[0]} = $local_db->loadResult();
												}
											}
										}
									}
								}
								else{
									JLog::add(JText::sprintf('INSERT_FAIL',$local_db->stderr()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $local_db->stderr();
									return $obj;
								}
							}
						}
					}
					
					$dids[] = $base_in_id;
					
					if($afterState){
						//capture events after update
						if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
							//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
							/* if(!property_exists($cached_data, $primary)){
								$cached_data->$primary = $base_in_id;
							} */
							if(!empty($primary)){
								foreach($primary as $pkey){
									if(property_exists($cached_data, $pkey) && isset($base_in_id[$pkey])){
										$cached_data->{$pkey} = $base_in_id[$pkey];
									}
								}
							}
							$response = $this->captureEventsOnRecord( $cached_data, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
						}
					}
				}
				if($this->profile->params->operation!=3){
					$join_dids[] = $base_join_val;
					$flag = $this->insertRemoteJoinRecords($base_join_val, $this->profile->params->joins, $remote_data, $remote_fields, $remote_db, $this->profile->params->operation);
					if($flag->result=='error'){
						JLog::add(JText::sprintf('INSERT_FAIL',$flag->error), JLog::ERROR, 'com_vdata');
						$obj->result = 'error';
						$obj->error = $flag->error;
						return $obj;
					}
				}
			}
			
			if( ($profile_params->operation==2) || ($profile_params->operation==3) ){
				$deleteQry = 'DELETE FROM '.$local_db->quoteName($profile_params->table). ' WHERE %s';
				$whereCon = array();
				foreach($dids as $did){
					if(is_array($did)){
						$where = array();
						foreach($did as $pkey=>$pval){
							$where[] = $local_db->quoteName($pkey).' <> '.$local_db->quote($pval);
						}
						$whereCon[] = ' ('.implode(' AND ', $where).')';
					}
					else{
						$whereCon[] = ' '.$local_db->quoteName($primary).' <> '.$local_db->quote($did);
					}
				}
				//apply delete filter for delete operation
				if($profile_params->operation==3){
					//apply filters
					if(isset($profile_params->filters->column)){
						$filterValues = $this->importDeleteFilter($local_db, $this->profile->params);
						if(!empty($filterValues)){
							$whereCon[] = $filterValues;
						}
					}
				}
				if(!empty($whereCon)){
					$local_db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
					$res = $local_db->execute();
				}
				
				if(isset($this->profile->params->joins)){
					$whereCon = array();
					$table2 = $this->profile->params->joins->table2;
					for($i=0;$i<count($table2);$i++){
						$deleteQry = 'DELETE FROM '.$local_db->quoteName($table2[$i]). ' WHERE %s';
						foreach($join_dids as $jdid){
							 if(is_array($jdid)){
								$where = array();
								foreach($jdid as $pkey=>$pval){
									$where[] = $local_db->quoteName($pkey).' <> '.$local_db->quote($pval);
								}
								$whereCon[] = ' ('.implode(' AND ', $where).')';
							}
							else{ 
								$whereCon[] = $local_db->quoteName($this->profile->params->joins->column2[$i]).' <> '.$local_db->quote($jdid);
							}
						}
						if(!empty($whereCon)){
							$local_db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
							$res = $local_db->execute();
						}
					}
				}
			}
			$session->clear('dids');
			$session->clear('join_dids');
			
			$obj->result='success';
			$obj->size = $size;
			$obj->totalRecordInFile = $size;
			$obj->offset = $offset + $config->limit;
			$qty = $session->get('qty',0) + $n;
			$session->set('qty',$qty);
			return $obj;
		}
		
		function insertRemoteJoinRecords($base_in_id, $params, $remoteData, $fields, $remote_db, $operation=1){
			
			$local_db = $this->getDbc();
			$obj = new stdClass();
			$previous_data = array();
			
			if(!empty($params->table2)){
				$abc = 0;
				$done = array();
				for($out=0;$abc<count($params->table2);$out++){
					if($params->join[$out]=='left_join' || $params->join[$out]=='join'){
						if(!in_array($out, $done)){
							
							if(empty($fields[$params->table2[$out]]['table']) && empty($fields[$params->table2[$out]]['on']) && empty($fields[$params->table2[$out]]['value'])){
								continue;
							}
							$childData = array();
							if($params->table1[$out]==$params->table1[0]){

								$query_remote = 'SELECT '.$remote_db->quoteName($fields[$params->table2[$out]]['table']).'.* FROM '.$remote_db->quoteName($fields[$params->table2[$out]]['table']).' WHERE '.$remote_db->quoteName($fields[$params->table2[$out]]['table']).'.'.$remote_db->quoteName($fields[$params->table2[$out]]['on']).' = '.$remote_db->quote($remoteData->{$fields[$params->table2[$out]]['value']});
								$remote_db->setQuery($query_remote);
								$childData = $remote_db->loadAssocList();
								
							}
							else{
								
								$query_remote = 'SELECT '.$remote_db->quoteName($fields[$params->table2[$out]]['table']).'.* FROM '.$remote_db->quoteName($fields[$params->table2[$out]]['table']);
								
								$trace_index = $out;
								while($params->table1[$trace_index]!=$params->table1[0]){
									$target_array = array_slice($params->table2, 0, $out);
									$new_key = array_search($params->table1[$trace_index], $target_array);
								
									if($new_key!==FALSE){
										
										$query_remote .= ' JOIN '.$remote_db->quoteName($fields[$params->table1[$trace_index]]['table']).' ON '.$remote_db->quoteName($fields[$params->table2[$trace_index]]['table']).'.'.$remote_db->quoteName($fields[$params->table2[$trace_index]]['on']).' = '.$remote_db->quoteName($fields[$params->table1[$trace_index]]['table']).'.'.$remote_db->quoteName($fields[$params->table2[$trace_index]]['value']);
									
										if($params->table1[$new_key] == $params->table1[0]){
											$query_remote .= ' WHERE '.$remote_db->quoteName($fields[$params->table2[$new_key]]['table']).'.'.$remote_db->quoteName($fields[$params->table2[$new_key]]['on']).' = '.$remote_db->quote($remoteData->{$fields[$params->table2[$new_key]]['value']});
										}
										$trace_index = $new_key;
									}
									else{
										break;
									}
								}
								$remote_db->setQuery($query_remote);
								$childData = $remote_db->loadAssocList();
							}
							
							if(empty($childData)){
								$abc++;
								$done[] = $out;
								continue;
							}
							$flag = $this->addChildRowData($out,$base_in_id, $params, $childData, $fields, $previous_data, $remote_db, $operation);
							if($flag->result=='success'){
								$abc++;
								$done[] = $out;
								$previous_data = $flag->previous_data;
							}
							else{
								$obj->result = 'error';
								$obj->error = $flag->error;
								return $obj;
							}
						}
					}
					elseif($params->join[$out]=='right_join'){
						if($abc==(count($params->table2)-1)){
							
							if(empty($fields[$params->table2[$out]]['table']) && empty($fields[$params->table2[$out]]['on']) && empty($fields[$params->table2[$out]]['value'])){
								continue;
							}
							$childData = array();
							if($params->table1[$out]==$params->table1[0]){
								
								$query_remote = 'SELECT '.$remote_db->quoteName($fields[$params->table2[$out]]['table']).'.* FROM '.$remote_db->quoteName($fields[$params->table2[$out]]['table']).' WHERE '.$remote_db->quoteName($fields[$params->table2[$out]]['table']).'.'.$remote_db->quoteName($fields[$params->table2[$out]]['on']).' = '.$remote_db->quote($remoteData->{$fields[$params->table2[$out]]['value']});
								$remote_db->setQuery($query_remote);
								$childData = $remote_db->loadAssocList();
								
							}
							else{
								
								$query_remote = 'SELECT '.$remote_db->quoteName($fields[$params->table2[$out]]['table']).'.* FROM '.$remote_db->quoteName($fields[$params->table2[$out]]['table']);
								
								$trace_index = $out;
								while($params->table1[$trace_index]!=$params->table1[0]){
									$target_array = array_slice($params->table2, 0, $out);
									$new_key = array_search($params->table1[$trace_index], $target_array);
									
									if($new_key!==FALSE){
										
										$query_remote .= ' JOIN '.$remote_db->quoteName($fields[$params->table1[$trace_index]]['table']).' ON '.$remote_db->quoteName($fields[$params->table2[$trace_index]]['table']).'.'.$remote_db->quoteName($fields[$params->table2[$trace_index]]['on']).' = '.$remote_db->quoteName($fields[$params->table1[$trace_index]]['table']).'.'.$remote_db->quoteName($fields[$params->table2[$trace_index]]['value']);
										
										if($params->table1[$new_key] == $params->table1[0]){
											$query_remote .= ' WHERE '.$remote_db->quoteName($fields[$params->table2[$new_key]]['table']).'.'.$remote_db->quoteName($fields[$params->table2[$new_key]]['on']).' = '.$remote_db->quote($remoteData->{$fields[$params->table2[$new_key]]['value']});
										}
										$trace_index = $new_key;
									}
									else{
										break;
									}
								}
								$remote_db->setQuery($query_remote);
								$childData = $remote_db->loadAssocList();
							}
							if(empty($childData)){
								$abc++;
								$done[] = $out;
								continue;
							}
							$flag = $this->addChildRowData($out,$base_in_id, $params, $childData, $fields, $previous_data, $remote_db, $operation);
							if($flag->result=='success'){
								$abc++;
								$done[] = $out;
								$previous_data = $flag->previous_data;
							}
							else{
								$obj->result = 'error';
								$obj->error = $flag->error;
								return $obj;
							}
						}
						continue;
					}
					
					if($out==count($params->table2)-1){
						$out = -1;
					}
				}
			}
			$obj->result = 'success';
			return $obj;
		}
		
		function addChildRowData($i, $base_in_id, $params, $childData, $fields, $previous_data, $remote_db, $operation){
			
			$obj = new stdClass();
			$local_db = $this->getDbc();
			$new_data[$params->column1[$i].':'.$params->column2[$i]] = array();
			$table2 = $params->table2[$i];
			$child_count = count($childData);
			
			foreach($childData as $k=>$data){
				
				$insert = new stdClass();
				$stance = false;
				$isNew = false;
				if($i==0){
					$insert->{$params->column2[0]} = $base_in_id;
				}
				
				if(!empty($previous_data)){
					if($params->join[$i]=='left_join' || $params->join[$i]=='join'){
						$keys = array_keys($previous_data);
						if(isset($previous_data[$keys[0]][$k]) && property_exists($previous_data[$keys[0]][$k], $params->column1[$i])){
							$insert->{$params->column2[$i]} = $previous_data[$keys[0]][$k]->{$params->column1[$i]};
						}else{
							$insert->{$params->column2[$i]} = $base_in_id;
						}
					}
					elseif($params->join[$i]=='right_join'){
						$keys = array_keys($previous_data);
						$column_keys = explode(':', trim($keys[0]));
						$insert->$column_keys[0] = $previous_data[$keys[0]][$k]->$column_keys[1];
					}
				}
				
				foreach($params->columns[$i] as $column=>$options){
					
					switch($options->data){
						case 'file':
							if($fields[$table2]['columns'][$column]==""){
								break;
							}
							if(is_array($fields[$table2]['columns'][$column])){
								if(!empty($fields[$table2]['columns'][$column]["table"])){
									$query_remote = 'SELECT '.$remote_db->quoteName($fields[$table2]['columns'][$column]["column"]).' FROM '.$remote_db->quoteName($fields[$table2]['columns'][$column]["table"]).' WHERE '.$remote_db->quoteName($fields[$table2]['columns'][$column]["column2"]).' = '.$remote_db->quote($data[$fields[$table2]['columns'][$column]["column1"]]);
									$remote_db->setQuery($query_remote);
									if( $ref_val = $remote_db->loadResult() ){
										$insert->{$column} = $ref_val;
										break;
									}
									else{
										$obj->result = 'error';
										$obj->error = $remote_db->stderr();
										return $obj;
									}
								}
							}
							else {
								$insert->{$column} = $this->getFilteredValue($options, $data[$fields[$table2]['columns'][$column]]);
								if( isset($options->format) && ($options->format=='email') && ($insert->{$column}==false) ){
									JLog::add(JText::sprintf('VDATA_EMAIL_VALIDATION_FAILED', $column), JLog::ERROR, 'com_vdata');
									unset($insert->{$column});
								}
							}
						break;
						case 'defined':
							$defined = $options->default;

							$hd_remote_pattern = '/@vdRemote:[\w]*[.?\w]*/';

							$hdRemote = preg_match_all($hd_remote_pattern, $defined, $local_matches);
							if( ($hdRemote!==FALSE) ){
								foreach($local_matches[0] as $mk=>$match){
									if(!empty($match)){
										$fn = explode(':', $match);
										if(!empty($fn[1])){
											$info = explode('.', $fn[1]);
											if(count($info)==1){
												if(array_key_exists($info[0], $data)){
												$defined = preg_replace('/'.$match.'/', $data[$info[0]], $defined);
												}
											}
											elseif(count($info)==2){
													if(!empty($info[0]) && !empty($info[1]) && property_exists($params->fields, $info[0]) && ($params->fields->{$info[0]}->data=='reference') ) {
													if(array_key_exists($info[0], $data)){
														$query = 'select '.$remote_db->quoteName($info[1]).' from '.$remote_db->quoteName($params->fields->{$info[0]}->table).' where '.$remote_db->quoteName($params->fields->{$info[0]}->on).'='.$remote_db->quote($data[$info[0]]);
														$remote_db->setQuery($query);
														$rdata = $remote_db->loadResult();
														$defined = preg_replace('/'.$match.'/', $rdata, $defined);
													}
												}
											}
										}
									}
								}
							}
							$defined = $this->getDefinedValue($options->default, $params->columns[$i], $data, $fields[$table2]['columns']);
							$insert->{$column} = $defined;
						break;
						case 'reference':
							//refrence data
							$menuException = false;
							if( empty($fields[$table2]['columns'][$column]['table']) || empty($fields[$table2]['columns'][$column]['on']) || empty($fields[$table2]['columns'][$column]['value']) || empty($fields[$table2]['columns'][$column]['refcol']) ){
								break;
							}
							$remote_ref_field = array();
							foreach($fields[$table2]['columns'][$column]['refcol'] as $key=>$rfield){
								if(!empty($rfield)){
									$remote_ref_field[] = $rfield;
								}
							}
							
							$query_remote = "select ".implode(',',$remote_ref_field)." from ".$remote_db->quoteName($fields[$table2]['columns'][$column]['table'])." "; 
							$query_remote .= "where ".$remote_db->quoteName($fields[$table2]['columns'][$column]['on'])."=";
							if($table2=='#__modules_menu' && preg_match('/^(-).*$/', $data[$fields[$table2]['columns'][$column]['value']], $match)){
								$query_remote .= substr($data[$fields[$table2]['columns'][$column]['value']],1);
								$menuException = true;
							}else{
								$query_remote .= $data[$fields[$table2]['columns'][$column]['value']];
							}
							$remote_db->setQuery($query_remote);
							$values = $remote_db->loadObject();
							
							if(!empty($values)){
								$query_local = "select ".$local_db->quoteName($options->on)." from ".$local_db->quoteName($options->table)." where 1=1";
								foreach($remote_ref_field as $ref_field){
									$query_local .= " and ".$local_db->quoteName($ref_field)." = ".$local_db->quote($values->{$ref_field});
								}
								$local_db->setQuery($query_local);
								$ref_value = $local_db->loadResult();
								if(!empty($ref_value)){
									if($table2=='#__modules_menu' && $menuException){
										$insert->{$column} = -$ref_value;
									}
									else{
										$insert->{$column} = $ref_value;
									}
									// $insert->{$column} = $ref_value;
								}
								else{
									JLog::add(JText::sprintf('LOCAL_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
								}
							}
							else{
								JLog::add(JText::sprintf('REMOTE_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
							}
						break;
						case 'asset_reference':
							$stance = true;
							$insert->$column = 0;
							$intance_column = $options->on;
							$instance_component = $options->component;
							if($fields[$table2]['columns'][$column]['rules']!=""){
								if(is_array($fields[$table2]['columns'][$column]['rules'])){
									$query_remote = 'SELECT '.$remote_db->quoteName($fields[$table2]['columns'][$column]['rules']["column"]).' FROM '.$remote_db->quoteName($fields[$table2]['columns'][$column]['rules']["table"]).' WHERE '.$remote_db->quoteName($fields[$table2]['columns'][$column]['rules']["column2"]).' = '.$remote_db->quote($data[$fields[$table2]['columns'][$column]['rules']["column1"]]);
									
									$remote_db->setQuery($query_remote);
									if( $ref_val = $remote_db->loadResult() )
									{
										$insert->rules = json_decode($ref_val, true);
										break;
									}
									else
									{
										$obj->result = 'error';
										$obj->error = $remote_db->stderr();
										return $obj;
									}
								}
								else{
									$insert->rules = json_decode($data[$fields[$table2]['columns'][$column]['rules']], true);
								}
							}
						break;
					}
				}
				
				//check record exists or not
				$primary = $this->getPrimaryKey($table2, $local_db);
				if(!empty($primary) && isset($insert->{$primary})){
					$query = 'SELECT COUNT(*) FROM '.$local_db->quoteName($table2).' WHERE '.$local_db->quoteName($primary).'='.$local_db->quote($insert->{$primary});
					$local_db->setQuery($query);
					$result = $local_db->loadResult();
					$isNew = ($result>0)?false:true;
				}else{
					$query = 'SELECT COUNT(*) FROM '.$local_db->quoteName($table2).' WHERE %s';
					$whereCon = array();
					foreach($insert as $key=>$val){
						$whereCon[] = $local_db->quoteName($key).' = '.$local_db->quote($val);
					}
					$local_db->setQuery(sprintf($query, implode(' AND ', $whereCon)));
					$result = $local_db->loadResult();
					$isNew = ($result>0)?false:true;
				}
				
				if($stance){
					$component = JText::_($instance_component);
					$component_name = $component;
					if (strpos(strtolower($component), "com_") ===FALSE){
						$component_name = "com_".strtolower($component_name); 
					}
					if (strpos(strtolower($component), "com_") ===0){
						$component = str_replace("com_", '',strtolower($component)); 
					}
					JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
					$core_tables = array('category', 'content', 'menu', 'module');
					if(in_array($intance_column, $core_tables)){
						$row = JTable::getInstance($intance_column);
					}
					else{
						$row = JTable::getInstance($intance_column, $component.'Table');
					}
					//update case
					if( ($operation==0) && !$isNew ){
						$row->load($insert->{$primary});
					}
					
					if(property_exists($row, 'parent_id')){
						if (isset($row->id) && isset($insert->parent_id) && $row->id==1 && $insert->parent_id==1)
							$insert->parent_id = 0; 
						$parent_id = isset($insert->parent_id) && !empty($insert->parent_id)?$insert->parent_id:1;
						if ($row->parent_id!=$parent_id)
							@$row->setLocation($parent_id, 'last-child');
					}
					if (!$row->bind((array)$insert)) {
						JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
						$obj->result = 'error';
						$obj->error = $row->getError();
						return $obj;
					}
					if (!$row->check()) {
						JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
						$obj->result = 'error';
						$obj->error = $row->getError();
						return $obj;
					}
					if (!$row->store()){
						JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
						$obj->result = 'error';
						$obj->error = $row->getError();
						return $obj;
					}
					
				}
				else{
					
					$flag = new stdClass();
					
					if(isset($params->component->table[$i]) && !empty($params->component->table[$i])){
						$component = JText::_($params->component->value[$i]);
						$component_name = $component;
						if (strpos(strtolower($component), "com_") ===FALSE){
							$component_name = "com_".strtolower($component_name); 
						}
						if (strpos(strtolower($component), "com_") ===0){
							$component = str_replace("com_", '',strtolower($component)); 
						}
						JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
						$core_tables = array('category', 'content', 'menu', 'module');
						if(in_array($params->component->table[$i], $core_tables)){
							$row = JTable::getInstance($params->component->table[$i]);
						}
						else{
							$row = JTable::getInstance($params->component->table[$i], $component.'Table');
						}
						//update case
						if( ($operation==0) && !$isNew ){
							$row->load($insert->{$primary});
						}
						if(property_exists($row, 'parent_id')){
							if (isset($row->id) && isset($insert->parent_id) && $row->id==1 && $insert->parent_id==1)
								$insert->parent_id = 0; 
							$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
							
							if ($row->parent_id!=$parent_id)
								@$row->setLocation($parent_id, 'last-child');
						}
					
						if (!$row->bind((array)$insert)) {
							JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
							$obj->result = 'error';
							$obj->error = $row->getError();
							return $obj;
						}
						if (!$row->check()) {
							JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
							$obj->result = 'error';
							$obj->error = $row->getError();
							return $obj;
						}
						if (!$row->store()){
							JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
							$obj->result = 'error';
							$obj->error = $row->getError();
							return $obj;
						}
					}
					else{
						if(!$isNew){
							if(empty($primary)){
								$flag->result = 'success';
								$flag->row = $insert;
									
							}else{
								
								if($local_db->updateObject($table2, $insert, is_array($primary)?$primary[0]:$primary)){
									$flag->result = 'success';
									$flag->row = $insert;
								}
								else{
									$flag->result = 'error';
									$flag->error = $local_db->getErrorMsg();
								}
							  }
						}else{
							if($local_db->insertObject($table2,$insert)){
								$flag->result = 'success';
								if(!empty($primary)){
								$insert->$primary = $local_db->insertid();
								}
								$flag->row = $insert;
							}
							else{
								$flag->result = 'error';
								$flag->error = $local_db->getErrorMsg();
							}
						}
						if($flag->result=='success'){
							$row = $flag->row;
						}
						else{
							$obj->result="error";
							$obj->error = $flag->error;
							return $obj;
						}
					}
				}
				
				$primary = $this->getPrimaryKey($table2, $local_db);
				$row = $insert;
				if(!empty($primary)){
					$row->{$primary} = 2;
				}
				
				array_push($new_data[$params->column1[$i].':'.$params->column2[$i]], $row);
			
			}
			if($operation==2){
				try{
					$where = array();
					$delQuery = 'DELETE FROM '. $table2 .' WHERE '.$params->column2[0] .' = '. $base_in_id .' AND ';
					//delete from xxxx_k2_comments where itemID=1 and (user_id,username) NOT IN ((0,'test user1'),(135,'Super User'));
					$syncData = array_values($new_data)[0];
					foreach($syncData as $obj){
						$temp ='(';
						$temp .=implode(',',array_diff(array_values(get_object_vars($obj)), [$base_in_id])).')';
						$where[] = $temp;
					}
					$delQuery .= '('.implode(',',array_diff(array_keys(get_object_vars($obj)), [$params->column2[0]])).') NOT IN ('.implode(',',$where).')';
					$local_db->setQuery($delQuery);
					$local_db->execute();
				}
				catch (Exception $e){
					$obj->result = 'error';
					$obj->error = $e->getMessage();
					return $obj;
				}
				
			}
			$obj->result = 'success';
			$obj->previous_data = $new_data;
			return $obj;
		}
		
		function batch_import_csv_quick()
		{
			$obj = new stdClass();
			$db = $this->getDbc();
			// $db = JFactory::getDbo();
			$session = JFactory::getSession();
			// $path = $session->get('file_path');
			$fl_counter = $session->get('fl_counter', 0);
			$paths = $session->get('file_path');
			//total files size
			$size = 0;
			foreach($paths as $ct=>$path){
				$size = $size+filesize($path);
			}
			$totalFiles = count($paths);
			
			if(!isset($paths[$fl_counter])){
				$obj->result = 'error';
				$obj->error = JText::_('FILE_NOT_FOUND');
				return $obj;
			}
			$path = $paths[$fl_counter];
			
			
			
			if(!is_file($path))	{
				$obj->result = 'error';
				$obj->error = JText::_('PLZ_SELECT_FILE');
				return $obj;
			}
			$cur_size = filesize($path);
			if($cur_size == 0)	{
				$obj->result = 'error';
				$obj->error = JText::_('PLZ_UPLOAD_VALID_CSV_FILE');
				return $obj;
			}
			
			$quick = JFactory::getApplication()->input->getInt('quick',0);
			$quick_fields = JFactory::getApplication()->input->get('csvfield', array(), 'ARRAY');
			
			$offset = JFactory::getApplication()->input->getInt('offset');
			//get csv configuration
			$config = $this->getConfig();
			$delimiter = $this->getDelimiter();
			$enclosure = $this->getEnclosure();
			$headingStatus = $config->csv_header;
			
			$dids = $session->get('dids', array());
			
			$query = 'SHOW KEYS FROM '.$db->quoteName($this->profile->params->table).' WHERE Key_name = "PRIMARY"';
			$db->setQuery( $query );
			// $key = $db->loadObjectList();
			$key = $db->loadAssocList('Column_name');
			if(property_exists($this->profile->params, 'unqkey') && !empty($this->profile->params->unqkey)){
				$primary = $this->profile->params->unqkey;
			}
			elseif(!empty($key)){
				// $primary = $key[0]->Column_name;
				$primary = array_keys($key);
			}
			else{
				$primary = null;
			}

			$fp = @fopen($path, "r");
			fseek($fp,$offset);
			
			$query = 'show fields FROM '.$db->quoteName($this->profile->params->table);
			$db->setQuery( $query );
			$cols = $db->loadObjectList();
			$totalRecordInFile = $session->get('totalRecordInFile',0);
			if($offset == 0)
			{
				$fileSpl = new SplFileObject($path, 'r');
				$fileSpl->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
				$fileSpl->seek(PHP_INT_MAX);
				$totalRecordInFile = $fileSpl->key() + $totalRecordInFile;
				if($headingStatus){
					$header = fgetcsv($fp, 100000, $delimiter, $enclosure);
					$totalRecordInFile-=1;				
				}		
				$session->set('totalRecordInFile',$totalRecordInFile);	
				$fileSpl=null;
				//log info
				JLog::add(JText::sprintf('IMPORT_PROFILE', $this->profile->title), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('IMPORT_TABLE', $this->profile->params->table), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('IMPORT_FORMAT', 'CSV'), JLog::INFO, 'com_vdata');
				
				/* if(count($header) != count($cols)){
					$obj->result = 'error';
					$obj->error = JText::_('PLZ_UPLOAD_VALID_CSV_FILE');
					return $obj;
				} */
			}
			
			//test for quick import with custom fields
			if($quick)
			{
				$n = 0;
				for($lm=0; $lm < $config->limit; $lm++)
				{
					if(($data = fgetcsv($fp, 100000, $delimiter, $enclosure)) !== FALSE)
					{	
						if($this->check_blank($data)){
							$totalRecordInFile-=1;
							$session->set('totalRecordInFile',$totalRecordInFile);
							continue;
						}
						$insert = new stdClass();
						if($this->profile->params->operation==3){
							//apply primary key and custom filters
							$where = array();
							$base_in_id = array();
							if(isset($this->profile->params->unqkey)){
								foreach($this->profile->params->unqkey as  $key){
									if($quick_fields[$key]!=""){
										$where[] = "(".$db->quoteName($key).' = '.$db->quote($data[$quick_fields[$key]]).")";
										$base_in_id[$key] = $data[$quick_fields[$key]];
									}
								}
							}
							if(!empty($where)){
								$statement = 'SELECT count(*) FROM ' . $db->quoteName($this->profile->params->table) . ' WHERE %s';
								$query = $db->getQuery(true);
								$db->setQuery(sprintf($statement, implode(' AND ', $where)));
								$result = $db->loadResult();
								if($result>0 && !empty($base_in_id)){
									$dids[] = $base_in_id;
								}
							}
						}
						else{
							foreach($cols as $i=>$col){
								if(isset($data[$i])){
									$insert->{$col->Field} = $data[$i];
								}
							}
						}
						//if primary key exists check existing record
						$isNew = true;
						$oldData = array();
						$where = array();
						if(!empty($primary)){
							foreach($primary as $keyCol){
								if(isset($insert->{$keyCol})){
								$where[] = $db->quoteName($keyCol).'='.$db->quote($insert->{$keyCol});
								}
							}
							if(!empty($where)){
								$query = 'SELECT * FROM '.$db->quoteName($this->profile->params->table).' WHERE '.implode(' AND ', $where);
								$db->setQuery( $query );
								$oldData = $db->loadAssoc();
								$isNew = empty($oldData) ? true : false;
							}
						}
						
						if($this->profile->params->operation==3){
							//delete data operation
						}
						elseif($this->profile->params->operation==1)
						{
							if(!$isNew){
								// JLog::add(JText::sprintf('RECORD_EXISTS',$primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
								JLog::add(JText::sprintf('RECORD_ALREADY_EXISTS',implode(',',$where)), JLog::ERROR, 'com_vdata');
								continue;
							}
							//capture events before insert
							if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
								
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
								$response = $this->captureEventsOnRecord( $insert, 'before', $isNew , array('pk'=>$primary ,'old'=>$oldData));
								if(in_array(false, $response)){
									JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
									continue;
								}
									
							}
							if(!$db->insertObject($this->profile->params->table, $insert))
							{
								JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $db->stderr();
								return $obj;
							}

							/* //$this->insertRow($table_name, $column_name, (array)$insert);
							JFactory::getApplication()->input->set('table_name', $this->profile->params->table);
							JFactory::getApplication()->input->set('column_name', $primary);
							$row = JTable::getInstance('import', 'Table');
							if (!$row->bind((array)$insert)) {
								$obj->result = 'error';
								$obj->error = $db->stderr();
								return $obj;
							}
							if (!$row->check()) {
								$obj->result = 'error';
								$obj->error = $db->stderr();
								return $obj;
							}
							if (!$row->store()){
								$obj->result = 'error';
								$obj->error = $row->getError();
								return $obj;
							} */
							
							if($this->getAffected($db)> 0){
								$n++;
								//capture events after insert
								if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
									//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
									$response = $this->captureEventsOnRecord( $insert, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
								}
							}	
						}
						else
						{
							if(empty($primary)){
								$obj->result = 'error';
								$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
								return $obj;
							}
							else{
								foreach($primary as $pkey){
									if(!isset($insert->{$pkey}) || empty($insert->{$pkey})){
										$obj->result = 'error';
										$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
										return $obj;
									}
								}
							}
							
							//capture events before update
							if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
								$response = $this->captureEventsOnRecord( $insert, 'before',$isNew , array('pk'=>$primary ,'old'=>$oldData));
								if(in_array(false, $response)){
									JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
									continue;
								}
							}
							
							if($isNew){
								// JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
								JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
								if(!$db->insertObject($this->profile->params->table, $insert))
								{
									JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $db->stderr();
									return $obj;
								}
							}
							else{
								if(!$db->updateObject($this->profile->params->table, $insert, $primary)){
									JLog::add(JText::sprintf('UPDATE_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $db->stderr();
									return $obj;
								}
							}
							$base_in_id = array();
							foreach($primary as $pkey){
								if(isset($insert->{$pkey})){
									$base_in_id[$pkey] = $insert->{$pkey};
								}
							}
							$dids[] = $base_in_id;
							// $dids[] = $insert->{$primary};
							
							if($this->getAffected($db)> 0){
								$n++;
								//capture events after update
								if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
									//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
									$response = $this->captureEventsOnRecord( $insert, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
								}
							}
								
						}
					}
					else
					{
						if($fl_counter<($totalFiles-1)){
							
							$obj->result='success';
							$obj->size = $size;
							
							$progress = ftell($fp);
							if($fl_counter>0){
								for($ft=0;$ft<$fl_counter;$ft++){
									$progress += filesize($paths[$ft]);
								}
							}
							$obj->progress = $progress;
							
							$fl_counter = $fl_counter+1;
							$session->set('fl_counter', $fl_counter);
							
							$obj->offset = 0;
							$qty = $session->get('qty',0) + $n;
							$session->set('qty',$qty);
							$obj->totalRecordInFile = $totalRecordInFile;
							return $obj;
						}
						
						JLog::add(JText::sprintf('IMPORT_RECORD_COUNT', $session->get('qty',0)+$n), JLog::INFO, 'com_vdata');
						
						if( ($this->profile->params->operation==2) || ($this->profile->params->operation==3) ){
							$deleteQry = 'DELETE FROM '.$db->quoteName($this->profile->params->table). ' WHERE %s';
							$whereCon = array();
							foreach($dids as $did){
								if(is_array($did)){
									$where = array();
									foreach($did as $pkey=>$pval){
										$where[] = $db->quoteName($pkey).' <> '.$db->quote($pval);
									}
									$whereCon[] = ' ('.implode(' AND ', $where).')';
								}
								else{
									$whereCon[] = ' '.$db->quoteName($primary).' <> '.$db->quote($did);
								}
							}
							
							//apply delete filter for delete operation
							if($this->profile->params->operation==3){
								//apply filters
								if(isset($this->profile->params->filters->column)){
									$filterValues = $this->importDeleteFilter($db, $this->profile->params);
									if(!empty($filterValues)){
										$whereCon[] = $filterValues;
									}
								}
							}
							if(!empty($whereCon)){
								$db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
								$res = $db->execute();
							}
						}
						$session->clear('dids');
						//send notification if done from site part
						if(JFactory::getApplication()->isSite()){
							$notify = $this->sendNotificationEmail( $this->profile->iotype,0 , array('schedule_id'=>"",'schedule_title'=>"","schedule_qry"=>"",'profile_id'=>$this->profile->id,'profile_title'=>$this->profile->title,"count"=>($session->get('qty',0)+$n), "format"=>"csv") );
						}
						$obj->result = 'error';
						$obj->offset = ftell($fp);
						$obj->size = $size;
						$obj->totalRecordInFile = $totalRecordInFile;
						
						$progress = ftell($fp);
						if($fl_counter>0){
							for($ft=0;$ft<$fl_counter;$ft++){
								$progress += filesize($paths[$ft]);
							}
						}
						$obj->progress = $progress;
						
						$obj->error = JText::_('NO_RECORD_TO_IMPORT');
						$obj->qty = $session->get('qty',0) + $n;
						return $obj;
					}
						
				}
			}
			else
			{
				if(!count($quick_fields))
				{
					$obj->result = 'error';
					$obj->error = JText::_('SELECT_FIELDS_TO_IMPORT');
					return $obj;
				}
				
				$n = 0;
				//import batch
				for($lm=0; $lm < $config->limit; $lm++)
				{
					if(($data = fgetcsv($fp, 100000, $delimiter, $enclosure)) !== FALSE)
					{
						if($this->check_blank($data)){
							$totalRecordInFile-=1;
							$session->set('totalRecordInFile',$totalRecordInFile);
							continue;
						}
						$insert = new stdClass();
						if($this->profile->params->operation==3){
							//apply primary key and custom filters
							$where = array();
							$base_in_id = array();
							if(isset($this->profile->params->unqkey)){
								foreach($this->profile->params->unqkey as  $key){
									if($quick_fields[$key]!=""){
										$where[] = "(".$db->quoteName($key).' = '.$db->quote($data[$quick_fields[$key]]).")";
										$base_in_id[$key] = $data[$quick_fields[$key]];
									}
								}
							}
							if(!empty($where)){
								$statement = 'SELECT count(*) FROM ' . $db->quoteName($this->profile->params->table) . ' WHERE %s';
								$query = $db->getQuery(true);
								$db->setQuery(sprintf($statement, implode(' AND ', $where)));
								$result = $db->loadResult();
								if($result>0 && !empty($base_in_id)){
									$dids[] = $base_in_id;
								}
							}
						}
						else{
							foreach($quick_fields as $k=>$field){
								if( $field != "" ){
									$insert->{$k} = $data[$field];
								}
							}
						}
						$isNew = true;
						$oldData = array();
						$where = array();
						if(!empty($primary)){
							foreach($primary as $keyCol){
								if(isset($insert->{$keyCol})){
								$where[] = $db->quoteName($keyCol).'='.$db->quote($insert->{$keyCol});
								}
							}
							if(!empty($where)){
								$query = 'SELECT * FROM '.$db->quoteName($this->profile->params->table).' WHERE '.implode(' AND ', $where);
								$db->setQuery( $query );
								$oldData = $db->loadAssoc();
								$isNew = empty($oldData) ? true : false;
							}
						}
						
						if($this->profile->params->operation==3){
							//delete data operation
						}
						elseif($this->profile->params->operation==1){
							if(!$isNew){
								// JLog::add(JText::sprintf('RECORD_EXISTS',$primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
								JLog::add(JText::sprintf('RECORD_ALREADY_EXISTS',implode(',',$where)), JLog::ERROR, 'com_vdata');
								continue;
							}
							
							//capture events before insert
							if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
								$response = $this->captureEventsOnRecord( $insert, 'before',$isNew, array('pk'=>$primary ,'old'=>$oldData) );
								if(in_array(false, $response)){
									JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
									continue;
								}
							}
							
							if(!$db->insertObject($this->profile->params->table, $insert))
							{
								JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $db->stderr();
								return $obj;
							}
							if($this->getAffected($db)> 0){
								$n++;
								//capture events after insert
								if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
									//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
									$response = $this->captureEventsOnRecord( $insert, 'after',$isNew, array('pk'=>$primary ,'success'=>true) );
								}
							}
								
						}
						else{
							if(empty($primary)){
								$obj->result = 'error';
								$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
								return $obj;
							}
							else{
								foreach($primary as $pkey){
									if(!isset($insert->{$pkey}) || empty($insert->{$pkey})){
										$obj->result = 'error';
										$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
										return $obj;
									}
								}
							}
							
							//capture events before update
							if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
								$response = $this->captureEventsOnRecord( $insert, 'before',$isNew, array('pk'=>$primary ,'old'=>$oldData));
								if(in_array(false, $response)){
									JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
									continue;
								}
							}
							
							if($isNew){
								// JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
								JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
								if(!$db->insertObject($this->profile->params->table, $insert))
								{
									JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $db->stderr();
									return $obj;
								}
							}
							else{
								if(!$db->updateObject($this->profile->params->table, $insert, $primary))
								{
									JLog::add(JText::sprintf('UPDATE_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $db->stderr();
									return $obj;
								}
							}
							$base_in_id = array();
							foreach($primary as $pkey){
								if(isset($insert->{$pkey})){
									$base_in_id[$pkey] = $insert->{$pkey};
								}
							}
							$dids[] = $base_in_id;
							// $dids[] = $insert->{$primary};
							
							if($this->getAffected($db)> 0){
								$n++;
								//capture events after update
								if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
									//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
									$response = $this->captureEventsOnRecord( $insert, 'after', $isNew, array('pk'=>$primary ,'success'=>true) );
								}
							}
						}
					}
					else
					{
						if($fl_counter<($totalFiles-1)){
							$obj->result='success';
							$obj->size = $size;
							$progress = ftell($fp);
							if($fl_counter>0){
								for($ft=0;$ft<$fl_counter;$ft++){
									$progress += filesize($paths[$ft]);
								}
							}
							$obj->progress = $progress;
							$fl_counter = $fl_counter+1;
							$session->set('fl_counter', $fl_counter);
							
							$obj->offset = 0;
							$qty = $session->get('qty',0) + $n;
							$session->set('qty',$qty);
							$obj->totalRecordInFile = $totalRecordInFile;
							return $obj;
						}
						
						JLog::add(JText::sprintf('IMPORT_RECORD_COUNT', $session->get('qty',0)+$n), JLog::INFO, 'com_vdata');
						
						if( ($this->profile->params->operation==2) || ($this->profile->params->operation==3) ){
							$deleteQry = 'DELETE FROM '.$db->quoteName($this->profile->params->table). ' WHERE %s';
							$whereCon = array();
							foreach($dids as $did){
								if(is_array($did)){
									$where = array();
									foreach($did as $pkey=>$pval){
										$where[] = $db->quoteName($pkey).' <> '.$db->quote($pval);
									}
									$whereCon[] = ' ('.implode(' AND ', $where).')';
								}
								else{
									$whereCon[] = ' '.$db->quoteName($primary).' <> '.$db->quote($did);
								}
							}
							
							//apply delete filter for delete operation
							if($this->profile->params->operation==3){
								//apply filters
								if(isset($this->profile->params->filters->column)){
									$filterValues = $this->importDeleteFilter($db, $this->profile->params);
									if(!empty($filterValues)){
										$whereCon[] = $filterValues;
									}
								}
							}
							if(!empty($whereCon)){
								$db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
								$res = $db->execute();
							}
						}
						$session->clear('dids');
						$session->clear('totalRecordInFile');
						//send notification if done from site part
						if(JFactory::getApplication()->isSite()){
							$notify = $this->sendNotificationEmail( $this->profile->iotype,0 , array('schedule_id'=>"",'schedule_title'=>"","schedule_qry"=>"",'profile_id'=>$this->profile->id,'profile_title'=>$this->profile->title,"count"=>($session->get('qty',0)+$n), "format"=>"csv") );
						}
						$obj->result='error';
						$obj->offset = ftell($fp);
						$obj->size = $size;
						$obj->totalRecordInFile = $totalRecordInFile;
						$progress = ftell($fp);
						if($fl_counter>0){
							for($ft=0;$ft<$fl_counter;$ft++){
								$progress += filesize($paths[$ft]);
							}
						}
						$obj->progress = $progress;
						
						$obj->error = JText::_('NO_RECORD_TO_IMPORT');
						$obj->qty = $session->get('qty',0) + $n;
						return $obj;
					}
					
				}
			}
			
			$session->set('dids', $dids);
			$session->set('totalRecordInFile', $totalRecordInFile);
			
			$obj->result='success';
			$obj->size = $size;
			$progress = $child_pos;
			if($fl_counter>0){
				for($ft=0;$ft<$fl_counter;$ft++){
					$progress += filesize($paths[$ft]);
				}
			}
			$obj->progress = $progress;
			
			//file pointer position for next batch
			$obj->offset = ftell($fp);
			$qty = $session->get('qty',0) + $n;
			$session->set('qty',$qty);
			return $obj;
		}
		
		function batch_import_csv()
		{
			$obj = new stdClass();
			$db = $this->getDbc();
			$session = JFactory::getSession();
			$path = $session->get('file_path');
			if(is_array($path)){$path = $path[0];}
			if(!is_file($path))	{
				$obj->result = 'error';
				$obj->error = JText::_('PLZ_SELECT_FILE');
				return $obj;
			}
			$size = filesize($path);
			if($size == 0)	{
				$obj->result = 'error';
				$obj->error = JText::_('PLZ_UPLOAD_VALID_CSV_FILE');
				return $obj;
			}
			$csvfields = JFactory::getApplication()->input->get('csvfield', array(), 'ARRAY');

			if(count($csvfields) == 0)	{
				$obj->result = 'error';
				$obj->error = JText::_('PLZ_UPLOAD_VALID_CSV_FILE');
				return $obj;
			}
			
			$dids = $session->get('dids', array());
			
			//Check if the fields are selected
			$fields=array();
			foreach($csvfields as $k=>$v)	{
				if($v != "")	{
					$fields[$k] = $v;
				}
			}
			
			if(count($fields)==0)	{
				$obj->result = 'error';
				$obj->error = JText::_('PLZ_SELECT_CSV_FIELD');
				return $obj;
			}
			
			$offset = JFactory::getApplication()->input->getInt('offset');
			//get csv configuration
			$config = $this->getConfig();
			$delimiter = $this->getDelimiter();
			$enclosure = $this->getEnclosure();
			$headingStatus = $config->csv_header;
			
			$query = 'SHOW KEYS FROM '.$db->quoteName($this->profile->params->table).' WHERE Key_name = "PRIMARY"';
			$db->setQuery( $query );
			// $key = $db->loadObjectList();
			$key = $db->loadAssocList('Column_name');
			if(property_exists($this->profile->params, 'unqkey') && !empty($this->profile->params->unqkey))
				$primary = $this->profile->params->unqkey;
			elseif(!empty($key)){//&& (count($key)==1)
				// $primary = $key[0]->Column_name;
				$primary = array_keys($key);
			}
			else
				$primary = null;
			
			$fp = @fopen($path, "r");
			fseek($fp,$offset);
			$totalRecordInFile = $session->get('totalRecordInFile',0);
			$images_import = JFactory::getApplication()->input->get('images', array(), 'RAW');
			
			if($offset == 0){
				$fileSpl = new SplFileObject($path, 'r');
				$fileSpl->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
				$fileSpl->seek(PHP_INT_MAX);
				$totalRecordInFile = $fileSpl->key() + $totalRecordInFile;
				if($headingStatus){
					$header = fgetcsv($fp, 100000, $delimiter, $enclosure);
					$totalRecordInFile-=1;				
				}		
				$session->set('totalRecordInFile',$totalRecordInFile);	
				$fileSpl=null;
				//log info
				JLog::add(JText::sprintf('IMPORT_PROFILE', $this->profile->title), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('IMPORT_TABLE', $this->profile->params->table), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('IMPORT_FORMAT', 'CSV'), JLog::INFO, 'com_vdata');
			}
			
			$n = 0;
			//import batch
			for($lm=0; $lm < $config->limit; $lm++)
			{
				if(($data = fgetcsv($fp, 100000, $delimiter, $enclosure)) !== FALSE)
				{	if($this->check_blank($data)){
						$totalRecordInFile-=1;
						$session->set('totalRecordInFile',$totalRecordInFile);
						continue;
					}
					$isNew = true;
					//check if record already exists
					$insert = new stdClass();
					$cached_data = new stdClass();
					$oldData = array();
					if($this->profile->params->operation==3){
						//apply primary key and custom filters
						$where = array();
						$base_in_id = array();
						if(isset($this->profile->params->unqkey)){
							foreach($this->profile->params->unqkey as  $key){
								if($csvfields[$key]!=""){
									$where[] = "(".$db->quoteName($key).' = '.$db->quote($data[$csvfields[$key]]).")";
									$base_in_id[$key] = $data[$csvfields[$key]];
								}
							}
						}
						if(!empty($where)){
							$statement = 'SELECT count(*) FROM ' . $db->quoteName($this->profile->params->table) . ' WHERE %s';
							$query = $db->getQuery(true);
							$db->setQuery(sprintf($statement, implode(' AND ', $where)));
							$result = $db->loadResult();
							if($result>0 && !empty($base_in_id)){
								$dids[] = $base_in_id;
							}
						}
					}
					else{
						foreach($this->profile->params->fields as $column=>$field)	{
							switch($field->data) {
								case 'file':
									if($csvfields[$column]==""){
										break;
									}
									if($field->format == 'string'){
										$str = $data[$csvfields[$column]];
										if($field->type=='striptags'){
											$str = strip_tags($str);
										}
										elseif( ($field->type=='chars') && (JString::strlen($str)> $field->val) ){
											$str = JString::substr($str, 0, $field->val);
										}
										$insert->{$column} = $str;
										$cached_data->{$column} = $str;
									}
									elseif($field->format == "date")	{
										// $date = JFactory::getDate($data[$csvfields[$column]]);
										$date = new DateTime($data[$csvfields[$column]]);
										if(!empty($field->type)){
											// $date = $date->toFormat($field->type);
											$date = $date->format($field->type);
										}	
										$insert->{$column} = $date;
										$cached_data->{$column} = $date;
									}
									elseif($field->format == "number"){
										$insert->{$column} = (int)$data[$csvfields[$column]];
										$cached_data->{$column} = (int)$data[$csvfields[$column]];
									}
									elseif($field->format == "urlsafe"){
										$insert->{$column} = JFilterOutput::stringURLSafe($data[$csvfields[$column]]);
										$cached_data->{$column} = JFilterOutput::stringURLSafe($data[$csvfields[$column]]);
									}
									elseif($field->format == "encrypt"){
										$cached_data->{$column} = $data[$csvfields[$column]];
										if($field->type=='bcrypt'){
											$insert->{$column} = JUserHelper::hashPassword( $data[$csvfields[$column]]);
										}
										else{
											$insert->{$column} = JUserHelper::getCryptedPassword( $data[$csvfields[$column]],'',$field->type );
										}
									}
									elseif($field->format=='email'){
										if(function_exists('filter_var')){
											$str = @filter_var($str, FILTER_VALIDATE_EMAIL);
											if($str==false){
												JLog::add(JText::sprintf('VDATA_EMAIL_VALIDATION_FAILED', $column), JLog::ERROR, 'com_vdata');
												unset($insert->{$column});
											}
										}
									}
									if($field->format == "image"){
											$filename = $insert->{$column};$destination = rtrim($field->location,'/').'/'. basename($filename);
											
											$image_source=$path_type='';
											if(isset($images_import['root'][$column])){
												$image_source = isset($images_import['root'][$column]['image_source'])?$images_import['root'][$column]['image_source']:'';
												$path_type = isset($images_import['root'][$column]['path_type'])?$images_import['root'][$column]['path_type']:'';											
											}	
											if(!$image = $this->uploadImage($filename,$destination,$image_source,$path_type,$err)){
													
												$insert->{$column} = $cached_data->{$column} = $destination;
												$obj->result = 'error';
												$obj->error = $err;
												return $obj;
											}//jexit();
										} 
								break;
								
								case 'defined':
									$defined = $field->default;
									$hd_local_pattern = '/@vdLocal:[\w]*[.?\w]*/';
									$hd_php_pattern = '/@vdPhp:(.*?)$/';
									$hd_sql_pattern = '/@vdSql:(.*?)$/';
									// $hd_remote_pattern = '/@vdRemote:[\w]*[.?\w]*/';
									$hdlocal = preg_match_all($hd_local_pattern, $defined, $local_matches);
									if( ($hdlocal!==FALSE) ){
										foreach($local_matches[0] as $mk=>$match){
											if(!empty($match)){
												$fn = explode(':', $match);
												if( ( $fn[1]!="" ) ){
													$info = explode('.', $fn[1]);
													if(count($info)==1){
														if(array_key_exists($info[0], $data)){
															$defined = preg_replace('/'.$match.'/', $data[$info[0]], $defined);
														}
													}
													elseif(count($info)==2){
															if( !empty($info[0]) && !empty($info[1]) && property_exists($this->profile->params->fields, $info[0]) && ($this->profile->params->fields->{$info[0]}->data=='reference') && in_array($info[1], $this->profile->params->fields->{$info[0]}->reftext) ) {//&& in_array($info[1], $this->profile->params->fields->{$info[0]}->reftext)
															
															$defined = preg_replace('/'.$match.'/', $data[$csvfields[$info[0]][$info[1]]], $defined);
														}
													}
												}
											}
										}
									}
									// $hdphp = preg_match_all($hd_php_pattern, $defined, $php_matches);
									$hdphp = preg_match($hd_php_pattern, $defined, $php_matches);
									if( ($hdphp!==FALSE) && !empty($php_matches[0])){
										$temp = $php_matches[0];
										$func = explode(':',$temp, 2);
										if(!empty($func[1])){
											$response = @eval("return $func[1];");
											if (error_get_last()){
												//throw error and abort execution
												// echo 'custom error';
												//Or log error
												//(error_get_last());
											}
											else{
												$defined = $response;
											}
										}
									}
									// $hdsql = preg_match_all($hd_sql_pattern, $defined, $sql_matches);
									$hdsql = preg_match($hd_sql_pattern, $defined, $sql_matches);
									if( ($hdsql!==FALSE) && !empty($sql_matches[0])){
										$temp = $sql_matches[0];
										$func = explode(':',$temp, 2);
										if(!empty($func[1])){
											$query = 'select '.$func[1];
											try{
												$db->setQuery($query);
												$defined = $db->loadResult();
											}
											catch(Exception $e){
												$err = $e->getMessage();
												//throw error and abort execution
												// echo 'custom error';
												//Or log error
												//($e->getMessage());
											}
										}
									}
									$insert->{$column} = $defined;
									$cached_data->{$column} = $defined;
								break;
								
								case 'reference':
									//one to one relation							
									$query = 'SHOW KEYS FROM '.$db->quoteName($field->table).' WHERE Key_name = "PRIMARY"';
									$db->setQuery( $query );
									$key_ref = $db->loadObjectList();
									if(!empty($key_ref) && (count($key_ref)==1))
										$primary_ref = $key_ref[0]->Column_name;
									else
										$primary_ref = null;
									$insert_ref = new stdClass();
									foreach($field->reftext as $i=>$ref){
										if($csvfields[$column][$ref]!=""){
											$insert_ref->{$ref} = $data[$csvfields[$column][$ref]];
										}
									}
									$insert_ref_val = (array)$insert_ref;
									if(!empty($insert_ref_val)){
										$query = "select ".$db->quoteName($field->on)." from ".$db->quoteName($field->table)." where 1=1";
										foreach($insert_ref as $ref_key=>$ref_val){
											$query .= " and ".$db->quoteName($ref_key)." = ".$db->quote($ref_val);
										}
										$db->setQuery($query);
										$ref_value = $db->loadResult();
										if(!empty($ref_value)){
											$insert->{$column} = $ref_value;
											$cached_data->{$column} = $ref_value;
										}
										else{
											JLog::add(JText::sprintf('LOCAL_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
										}
									}
									else{
										JLog::add(JText::sprintf('REMOTE_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
									}
								break;
							}
						}
					}
					//check if record already exists
					$where = array();
					if( !empty($primary) ){//&& isset($insert->{$primary})
						foreach($primary as $keyCol){
							if(isset($insert->{$keyCol})){
							$where[] = $db->quoteName($keyCol).'='.$db->quote($insert->{$keyCol});
							}
						}
						if(!empty($where)){
							$query = 'SELECT * FROM '.$db->quoteName($this->profile->params->table).' WHERE '.implode(' AND ', $where);
							$db->setQuery( $query );
							$oldData = $db->loadAssoc();
							$isNew = empty($oldData) ? true : false;
						}
					}
					
					if($this->profile->params->operation==3){
						//delete data operation
					}
					elseif($this->profile->params->operation==1){
						//insert/update base table data
						if(!$isNew)
						{
							//if record exists log record and move pointer to next base record
							// JLog::add(JText::sprintf('RECORD_EXISTS',$primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
							JLog::add(JText::sprintf('RECORD_ALREADY_EXISTS',implode(',',$where)), JLog::ERROR, 'com_vdata');
							if(empty($this->profile->params->joins->table2)){
								//if no child record next record is base record
								$child_pos = ftell($fp);
							}
							else{
								//calculate next base record position as $child_pos and move pointer to that position
								$cpos = ftell($fp);
								if( ($cpos_blank = fgetcsv($fp, 100000, $delimiter, $enclosure))  !== FALSE ){
									if(!$this->check_blank($cpos_blank)){
										fseek($fp, $cpos);
										$child_pos = ftell($fp);//$cpos
										continue;
									}
									else{
										fseek($fp, $cpos);
										$blnk_count = 0;
										$fchild = 0;
										while(($nxt_bs=fgetcsv($fp, 100000, $delimiter, $enclosure)) !== FALSE){
											$fchild++;
											$blnk = $this->check_blank($nxt_bs);
											if($blnk){
												$blnk_count+=1;
											}
											elseif( !$blnk && ($blnk_count==1) && ($fchild!=2) ){
												fseek($fp, $flg_point);
												$child_pos = ftell($fp);
												break;
											}
											else{$blnk_count = 0;}
											$flg_point = ftell($fp);
										}
										//if it is last base record with child then there is no single blank
										if($nxt_bs===FALSE || $nxt_bs===NULL){
											$child_pos = ftell($fp);//$flg_point
										}
									}
									
								}
								else{
									//last base record with no child record
									fseek($fp, $cpos);
									// $child_pos = ftell($fp);
									continue;
								}
							}
							continue;
						}
						else
						{
							//capture events before insert
							if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
								$response = $this->captureEventsOnRecord( $insert, 'before',$isNew, array('pk'=>$primary ,'old'=>$oldData) );
								if(in_array(false, $response)){
									JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
									continue;
								}
							}
							if($db->insertObject($this->profile->params->table, $insert))	
							{
								
								//Method to get the auto-incremented column value from the last INSERT statement.
								if(count($primary)==1){
									$base_in_id = $db->insertid();
								}else{
									$base_in_id = array();
									foreach($primary as $pkey){
										if(isset($insert->{$pkey})){
											$base_in_id[$pkey] = $insert->{$pkey};
										}
									}
								}
							}
							else
							{
								JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $db->stderr();
								return $obj;
							}
							if($this->getAffected($db)> 0){
								$n++;
								//capture events after insert
								if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
									//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
									if(!empty($primary)){
										foreach($primary as $pkey){
											if(property_exists($cached_data, $pkey) && isset($base_in_id[$pkey])){
												$cached_data->{$pkey} = $base_in_id[$pkey];
											}
										}
									}
									/* if(!property_exists($cached_data, $primary)){
										$cached_data->$primary = $base_in_id;
									} */
									$response = $this->captureEventsOnRecord( $cached_data, 'after',$isNew, array('pk'=>$primary ,'success'=>true) );
								}
							}
							
							if(!empty($this->profile->params->joins->table2)){
								//if it is new record and base has no child record then continue with pointer to next base
								$next_base_pointer = $child_pos = ftell($fp);
								if( ($next_base_row = fgetcsv($fp, 100000, $delimiter, $enclosure)) !==FALSE){
									if(!$this->check_blank($next_base_row)){
										fseek($fp, $next_base_pointer);
										continue;
									}
									fseek($fp, $next_base_pointer);
								}
								//if it is last base record with no child data
								
							}

						}	
					}
					else
					{
						if(empty($primary)){//|| empty($insert->{$primary})
							$obj->result = 'error';
							$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
							return $obj;
						}
						else{
							foreach($primary as $pkey){
								if(!isset($insert->{$pkey}) || empty($insert->{$pkey})){
									$obj->result = 'error';
									$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
									return $obj;
								}
							}
						}
						
						if($isNew)
						{
							// JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
							JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
							//move pointer to next base record before continue
							if(empty($this->profile->params->joins->table2))
							{
								$child_pos = ftell($fp);
							}
							else
							{
								//calculate next base record position as $child_pos and move pointer to that position
								$cpos = ftell($fp);
								if( ($cpos_blank = fgetcsv($fp, 100000, $delimiter, $enclosure))  !== FALSE ){
									if(!$this->check_blank($cpos_blank)){
										fseek($fp, $cpos);
										$child_pos = ftell($fp);
										continue;
									}
									else{
										fseek($fp, $cpos);
										$blnk_count = 0;
										$fchild = 0;
										while(($nxt_bs=fgetcsv($fp, 100000, $delimiter, $enclosure)) !== FALSE){
											$fchild++;
											$blnk = $this->check_blank($nxt_bs);
											if($blnk){
												$blnk_count+=1;
											}
											elseif( !$blnk && ($blnk_count==1) && ($fchild!=2) ){
												fseek($fp, $flg_point);
												$child_pos = ftell($fp);//
												break;
											}
											else{
												$blnk_count = 0;
											}
											$flg_point = ftell($fp);
										}
										//if it is last base record with child then there is no single blank
										if($nxt_bs===FALSE || $nxt_bs===NULL){
											$child_pos = ftell($fp);//$flg_point
										}
										
									}
									
								}
								else{
									//last base record with no child record
									fseek($fp, $cpos);
									// $child_pos = ftell($fp);
									continue;
								}

							}
							continue;
						}
						else
						{
							//capture events before update
							if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
								$response = $this->captureEventsOnRecord( $insert, 'before',$isNew, array('pk'=>$primary ,'old'=>$oldData));
								if(in_array(false, $response)){
									JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
									continue;
								}
							}
							
							if($db->updateObject($this->profile->params->table, $insert, $primary))
							{
								// $base_in_id = $insert->{$primary};
								$base_in_id = array();
								foreach($primary as $pkey){
									if(isset($insert->{$pkey})){
										$base_in_id[$pkey] = $insert->{$pkey};
									}
								}
							}
							else
							{
								JLog::add(JText::sprintf('UPDATE_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $db->stderr();
								return $obj;
							}
							if($this->getAffected($db)> 0){
								$n++;
								//capture events after update
								if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
									//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
									if(!empty($primary)){
										foreach($primary as $pkey){
											if(property_exists($cached_data, $pkey) && isset($base_in_id[$pkey])){
												$cached_data->{$pkey} = $base_in_id[$pkey];
											}
										}
									}
									/* if(!property_exists($cached_data, $primary)){
										$cached_data->$primary = $base_in_id;
									} */
									$response = $this->captureEventsOnRecord( $cached_data, 'after', $isNew, array('pk'=>$primary ,'success'=>true) );
								}
							}
							
							if(!empty($this->profile->params->joins->table2)){
								//if it is new record and base has no child record then continue with pointer to next base
								$next_base_pointer = $child_pos = ftell($fp);
								if( ($next_base_row = fgetcsv($fp, 100000, $delimiter, $enclosure)) !==FALSE){
									if(!$this->check_blank($next_base_row)){
										fseek($fp, $next_base_pointer);
										continue;
									}
									fseek($fp, $next_base_pointer);
								}
								//if it is last base record with no child data
								
							}	
						}
						// $dids[] = $insert->{$primary};
						$dids[] = $base_in_id;
					}
					
					//chained join
					$join_field = new stdClass();
					if(!empty($this->profile->params->joins->table2))
					{
						$index = $child_count = count($this->profile->params->joins->table2);
						$base_pos = ftell($fp);
						
						//calculate $next_base_pos
						$nxt_blnk = fgetcsv($fp, 100000, $delimiter, $enclosure);
						if($this->check_blank($nxt_blnk)){
							fseek($fp, $base_pos);
							$blank_ct = 0;
							$fchild = 0;
							while(($next_base = fgetcsv($fp, 100000, $delimiter, $enclosure)) !==FALSE){
									$fchild++;
									$blank_test = $this->check_blank($next_base);
									if($blank_test){
										$blank_ct +=1;
									}
									elseif( !$blank_test && ($blank_ct == 1) && ($fchild!=2) ){
										$next_base_pos = $blnk_pointer;
										// fseek($fp, $base_pos);
										break;
									}
									else{
										$blank_ct = 0;
									}
									$blnk_pointer = ftell($fp);
							}
							//if there is no blank line after the last base record's child
							if($next_base ===FALSE){
								$next_base_pos = ftell($fp);
							}
						}
						fseek($fp, $base_pos);
						
						
						while($index > 0)
						{
							//import child data starting from last child
							$idx = --$index;
							
							//
							$blnk_ct = 0;
							$skip_child = FALSE;
							while( ($ct_blank = fgetcsv($fp, 100000, $delimiter, $enclosure)) !==FALSE ){
								if(ftell($fp) < $next_base_pos){
									$blank = $this->check_blank($ct_blank);
									if($blank){	
										$blnk_ct +=1;
									}
									else{
										if($blnk_ct == ($idx+1) ){
											$skip_child = FALSE;
											break;
										}
										$blnk_ct = 0;
									}
								}
								else{
									$skip_child = TRUE;
									break;
								}
							}
							if($ct_blank ===FALSE){
								$skip_child = TRUE;
							}
							$child_pos = $next_base_pos;
							// move to base record
							fseek($fp,$base_pos);
							
							// check whether to skip child or not
							/* if($skip_child){
								continue;
							} */
							
							$query = 'SHOW KEYS FROM '.$db->quoteName($this->profile->params->joins->table2[$idx]).' WHERE Key_name = "PRIMARY"';
							$db->setQuery( $query );
							$key_join = $db->loadObjectList();
							if( !empty($key_join) && (count($key_join)==1) ){
								$primary_join = $key_join[0]->Column_name;
							}
							else{
								$primary_join = null;
							}
							
							//if table exist as $join_field property then append join based column
							if(property_exists($join_field,$this->profile->params->joins->table2[$idx]))
							{
								if(!in_array($join_field->{$this->profile->params->joins->table2[$idx]}->field, $this->profile->params->joins->columns[$idx]) )
								{
									array_push($this->profile->params->joins->columns[$idx], $join_field->{$this->profile->params->joins->table2[$idx]}->field);
								}
								//add base table primary key field to first child table
								if(($idx == 0) && !in_array($this->profile->params->joins->column2[0], $this->profile->params->joins->columns[$idx]))
								{
									array_push($this->profile->params->joins->columns[$idx], $this->profile->params->joins->column2[0]);
								}
							}
							//set left table and field as $join_field property
							$join_field->{$this->profile->params->joins->table1[$idx]} = new stdClass();
							$join_field->{$this->profile->params->joins->table1[$idx]}->field = $this->profile->params->joins->column1[$idx];
							
							//insert skipped table
							if($skip_child){
								if(property_exists($join_field,$this->profile->params->joins->table2[$idx]) && property_exists($join_field->{$this->profile->params->joins->table2[$idx]}, 'val'))
								{
									foreach($join_field->{$this->profile->params->joins->table2[$idx]}->val as $ck=>$cval){
										$insert_join = new stdClass();
										foreach($this->profile->params->joins->columns[$idx] as $m=>$key){
											if($join_field->{$this->profile->params->joins->table2[$idx]}->field==$key)
												$insert_join->{$key} = $cval;
											if(($idx == 0) && ($this->profile->params->joins->column2[0]==$key))
												$insert_join->{$key} = $base_in_id;
										}
										if($db->insertObject($this->profile->params->joins->table2[$idx], $insert_join) )
										{
											//if dependent column exists in $insert_join then store it else store auto incremented id 
											if(property_exists($insert_join, $this->profile->params->joins->column2[$idx])){
												$in_id = $insert_join->{$this->profile->params->joins->column2[$idx]};
											}
											else{
												$in_id = $db->insertid();
											}
										}
										else{
											$obj->result = 'error';
											$obj->error = $db->stderr();
											return $obj;
										}
										
										//store depended column values in array
										// $join_field->{$this->profile->params->joins->table1[$idx]}->val[] = $in_id;
									}
								}
								continue;
							}
							
							$blank_count=0;
							//insert data, starting from last child table
							while(($chain_data=fgetcsv($fp, 100000, $delimiter, $enclosure)) !== FALSE)
							{
								$blank = $this->check_blank($chain_data);
								if($blank)
								{	
									$blank_count +=1;
								}
								else
								{
									if($blank_count == ($idx+1) )
									{	
										if($this->profile->params->operation != 1)
										{
											$query = "Delete ".$this->profile->params->joins->table2[$idx]." FROM ".$this->profile->params->joins->table2[$idx];
											for($x=$idx; $x>=0; $x--)
											{
												if(($x-1) < 0){$d_table = $this->profile->params->table;}else{$d_table = $this->profile->params->joins->table2[($x-1)];}
												$query .= " JOIN ".$d_table." ON ".$this->profile->params->joins->table2[$x].".".$this->profile->params->joins->column2[$x]." = ".$d_table.".".$this->profile->params->joins->column1[$x]."";
											}
											$query .= " WHERE ".$this->profile->params->table.".".$this->profile->params->joins->column1[0]."=".$base_in_id;
											$db->setQuery($query);
											if(!$db->execute())
											{
												$obj->result = 'error';
												$obj->error = $db->stderr();
												return $obj;
											}
										}
										$k = 0;
										while(($child_data = fgetcsv($fp, 100000, $delimiter, $enclosure)) !== FALSE)
										{
											//store child's records
											if(!$this->check_blank($child_data))
											{
												$temp = array();
												foreach($chain_data as $ky=>$data)
												{
													if(!empty($data))
													{
														$temp[]=$child_data[$ky];
													}
												}
												$child_data =  $temp;
												if(property_exists($join_field,$this->profile->params->joins->table2[$idx]))
												{
													//append values related to previously added dependent columns 
													array_push($child_data, $join_field->{$this->profile->params->joins->table2[$idx]}->val[$k]);
													if($idx == 0)
													{
														array_push($child_data, $base_in_id);
													}
												}
												
												/* if( ($idx == 0) && in_array($this->profile->params->joins->column2[0], $this->profile->params->joins->columns[$idx]) ){
													array_push($child_data, $base_in_id);
												} */
												
												$insert_join = new stdClass();
												foreach($this->profile->params->joins->columns[$idx] as $m=>$key)
												{
													//column selected by user,check dependent column exists in posted $csvfields
													if(array_key_exists($key,$csvfields[$this->profile->params->joins->table2[$idx]])){
														if($csvfields[$this->profile->params->joins->table2[$idx]][$key]!=""){
															$insert_join->{$key} = $child_data[$csvfields[$this->profile->params->joins->table2[$idx]][$key]];
														}
														
														
													}
													else{
														$insert_join->{$key} = $child_data[$m];
													}
													
												}
												if($db->insertObject($this->profile->params->joins->table2[$idx], $insert_join) )
												{
													//if dependent column exists in $insert_join then store it else store auto incremented id 
													if(property_exists($insert_join, $this->profile->params->joins->column2[$idx])){
														$in_id = $insert_join->{$this->profile->params->joins->column2[$idx]};
													}
													else{
														$in_id = $db->insertid();
													}
												}
												else{
													$obj->result = 'error';
													$obj->error = $db->stderr();
													return $obj;
												}
												
												//store depended column values in array
												$join_field->{$this->profile->params->joins->table1[$idx]}->val[] = $in_id;
												$k++;
											}
											else 
											{
												$k = 0;
												//change to max blank line i.e. from index count
												if($blank_count == $child_count)
												{
													//position of last child record
													$child_pos = ftell($fp);
												}
												//move to base record
												fseek($fp,$base_pos);
												break;
											}
										}
										//if it's last record 
										if($child_data == FALSE)
										{
											$child_pos = ftell($fp);
											//move to base record
											fseek($fp,$base_pos);
										}
										break;
									}
									$blank_count=0;
								}
							}
							
						}
						//move to last child record that is next base record
						fseek($fp, $child_pos);
					}
					else
						$child_pos = ftell($fp);//if no child record then it is next base record

				}
				else
				{
					
					JLog::add(JText::sprintf('IMPORT_RECORD_COUNT', $session->get('qty',0)+$n), JLog::INFO, 'com_vdata');
					
					if( ($this->profile->params->operation==2) || ($this->profile->params->operation==3) ){
						$deleteQry = 'DELETE FROM '.$db->quoteName($this->profile->params->table). ' WHERE %s';
						$whereCon = array();
						foreach($dids as $did){
							if(is_array($did)){
								$where = array();
								foreach($did as $pkey=>$pval){
									$where[] = $db->quoteName($pkey).' <> '.$db->quote($pval);
								}
								$whereCon[] = ' ('.implode(' AND ', $where).')';
							}
							else{
								$whereCon[] = ' '.$db->quoteName($primary).' <> '.$db->quote($did);
							}
						}
						//apply delete filter for delete operation
						if($this->profile->params->operation==3){
							//apply filters
							if(isset($this->profile->params->filters->column)){
								$filterValues = $this->importDeleteFilter($db, $this->profile->params);
								if(!empty($filterValues)){
									$whereCon[] = $filterValues;
								}
							}
						}
						if(!empty($whereCon)){
							$db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
							$res = $db->execute();
						}
						/* if(isset($this->profile->params->joins)){
							$whereCon = array();
							for($i=0; $i<count($this->profile->params->joins->table2); $i++){
								if($this->profile->params->joins->table1[$i]!=$this->profile->params->table)continue;
								$deleteQry = 'DELETE FROM  '.$db->quote($this->profile->params->joins->table2[$i]).' %s';
								for($join_dids as $jdid){
									if(is_array($jdid)){
										$where = array();
										foreach($jdid as $pkey=>$pval){
											$where[] = $db->quoteName($pkey).' <> '.$db->quote($pval);
										}
										$whereCon[] = ' ('.implode(' AND ', $where).')';
									}
									else{
										$whereCon[] = ' '.$db->quoteName($this->profile->params->joins->column2[$i]).' <> '.$db->quote($jdid);
									}
								}
								if(!empty($whereCon)){
									$db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
									$res = $db->execute();
								}
							}
							 
						}*/
									
					}
					$session->clear('dids');
					//$session->clear('join_dids');
					//send notification if done from site part
					if(JFactory::getApplication()->isSite()){
						$notify = $this->sendNotificationEmail( $this->profile->iotype,0 , array('schedule_id'=>"",'schedule_title'=>"","schedule_qry"=>"",'profile_id'=>$this->profile->id,'profile_title'=>$this->profile->title,"count"=>($session->get('qty',0)+$n), "format"=>"csv") );
					}
					
					$obj->result = 'error';
					$obj->offset = ftell($fp);
					$obj->size = $size;
					$obj->totalRecordInFile = $totalRecordInFile;
					$obj->error = JText::_('NO_RECORD_TO_IMPORT');
					$obj->qty = $session->get('qty',0) + $n;
					return $obj;
				}
			}
			
			$session->set('dids', $dids);
			//$session->set('join_dids', $join_dids);
			
			
			$obj->result='success';
			$obj->size = $size;
			//file pointer position for next batch
			$obj->offset = $child_pos;
			$obj->totalRecordInFile = $totalRecordInFile;
			$qty = $session->get('qty',0) + $n;
			$session->set('qty',$qty);
			return $obj;
			
		}
		
		function getFilteredValue($field , $value){
			
			if($field->format == 'string'){
				$str = $value;
				if($field->type=='striptags'){
					$str = strip_tags($str);
				}
				elseif( ($field->type=='chars') && (JString::strlen($str)> $field->val) ){
					$str = JString::substr($str, 0, $field->val);
				}
				return $str;
			}
			elseif($field->format == "date"){
				$date = new DateTime($value);
				if(!empty($field->type))
					$date = $date->format($field->type);
				return $date;
			}
			elseif($field->format == "number"){
				return (int)$value;
			}
			elseif($field->format == "urlsafe"){
				return JFilterOutput::stringURLSafe($value);
			}
			elseif($field->format == "encrypt"){
				if($field->type=='bcrypt'){
					return JUserHelper::hashPassword( $value);
				}
				else{
					return JUserHelper::getCryptedPassword( $value,'',$field->type );
				}
			}
			elseif($field->format=="email"){
				if(function_exists('filter_var')){
					$str = (filter_var($str, FILTER_VALIDATE_EMAIL)==FALSE)?false:$str;
				}
			}
			return $value;
		}
		
		function getDefinedValue($defined, $params, $data, $fields='') {
			
			$hd_local_pattern = '/@vdLocal:[\w]*[.?\w]*/';
			$hd_php_pattern = '/@vdPhp:(.*?)$/';
			$hd_sql_pattern = '/@vdSql:(.*?)$/';
			$hd_remote_pattern = '/@vdRemote:[\w]*[.?\w]*/';
			
			//filter - local columns
			$hdlocal = preg_match_all($hd_local_pattern, $defined, $local_matches);
			if( ($hdlocal!==FALSE) ){
				foreach($local_matches[0] as $mk=>$match){
					if(!empty($match)){
						$fn = explode(':', $match);
						if( ( $fn[1]!="" ) ){
							$info = explode('.', $fn[1]);
							if(count($info)==1){
								if(array_key_exists($info[0], $data)){
									$defined = preg_replace('/'.$match.'/', $data[$info[0]], $defined);
									
								}
							}
							elseif(count($info)==2){
									if( !empty($info[0]) && !empty($info[1]) && property_exists($params, $info[0]) && ($params->{$info[0]}->data=='reference') && in_array($info[1], $params->{$info[0]}->reftext) ) {
									$defined = preg_replace('/'.$match.'/', $data[$fields[$info[0]][$info[1]]], $defined);
								}
							}
						}
					}
				}
			}
			
			//filter - import source data
			$hdRemote = preg_match_all($hd_remote_pattern, $defined, $remote_matches);
			if($hdRemote!==FALSE){
				foreach($remote_matches[0] as $mk=>$match){
					if(!empty($match)){
						$fn = explode(':', $match);
						if( ( $fn[1]!="" ) ){
							if(array_key_exists($fn[1], $data) && isset($data[$fn[1]])){
								$defined = preg_replace('/'.$match.'/', $data[$fn[1]], $defined);
							}
						}
					}
				}
			}
			
			// $hdphp = preg_match_all($hd_php_pattern, $defined, $php_matches);
			$hdphp = preg_match($hd_php_pattern, $defined, $php_matches);
			if( ($hdphp!==FALSE) && !empty($php_matches[0])){
				$temp = $php_matches[0];
				$func = explode(':',$temp, 2);
				if(!empty($func[1])){
					$response = @eval("return ".$func[1].";");
					if (error_get_last()){
						//throw Or log error (error_get_last())
					}
					else{
						$defined = $response;
					}
				}
			}
			// $hdsql = preg_match_all($hd_sql_pattern, $defined, $sql_matches);
			$hdsql = preg_match($hd_sql_pattern, $defined, $sql_matches);
			if( ($hdsql!==FALSE) && !empty($sql_matches[0])){
				$temp = $sql_matches[0];
				$func = explode(':',$temp, 2);
				if(!empty($func[1])){
					$query = 'select '.$func[1];
					try{
						$db = $this->getDbc();
						$db->setQuery($query);
						$defined = $db->loadResult();
					}
					catch(Exception $e){
						$err = $e->getMessage();
						//throw Or log error $e->getMessage()
					}
				}
			}
			
			return $defined;
			
		}
		
		function getPrimaryKey($table, $db){
			
			$query = 'SHOW KEYS FROM '.$db->quoteName($table).' WHERE Key_name = "PRIMARY"';
			$db->setQuery( $query );
			$key_ref = $db->loadObjectList();
			if(!empty($key_ref) && (count($key_ref)==1) )
				return $key_ref[0]->Column_name;
			else
				return null;
		}
		
		function  importDeleteFilter($db, $params){
			$where = array();
			$m = $n = -1;
			foreach($params->filters->column as $j=>$column){
				if($params->filters->cond[$j]=='between' || $params->filters->cond[$j]=='notbetween'){
					$n = ($n<0)?0:$n+1;
				}
				else{
					$m = ($m<0)?0:$m+1;
					$value = $this->getQueryFilteredValue($params->filters->value[$m]);
				}
				
				if($params->filters->cond[$j]=='in'){
					$where[] = $db->quoteName($column)." IN ( ".$db->quote($value)." )";
				}
				elseif($params->filters->cond[$j]=='notin'){
					$where[] = $db->quoteName($column)." NOT IN ( ".$value." )";
				}
				elseif($params->filters->cond[$j]=='between'){
					$value1 = $this->getQueryFilteredValue($params->filters->value1[$n]);
					$value2 = $this->getQueryFilteredValue($params->filters->value2[$n]);
					$where[] = $db->quoteName($column)." BETWEEN ".$db->quote($value1)." AND ".$db->quote($value2);
				}
				elseif($params->filters->cond[$j]=='notbetween'){
					$value1 = $this->getQueryFilteredValue($params->filters->value1[$n]);
					$value2 = $this->getQueryFilteredValue($params->filters->value2[$n]);
					$where[] = $db->quoteName($column)." NOT BETWEEN ".$db->quote($value1)." AND ".$db->quote($value2);
				}
				elseif($params->filters->cond[$j]=='like'){
					$where[] = $db->quoteName($column)." LIKE ".$db->quote($value);
				}
				elseif($params->filters->cond[$j]=='notlike'){
					$where[] = $db->quoteName($column)." NOT LIKE ".$db->quote($value);
				}
				elseif($params->filters->cond[$j]=='regexp'){
					$where[] = $db->quoteName($column)." REGEXP ".$db->quote($params->filters->value[$j]);
				}
				else{
					$where[] = $db->quoteName($column)."".$params->filters->cond[$j]." ".$db->quote($value);
				}
			}
			if(!empty($where)){
				return "(".implode(strtoupper(" ".$params->filters->op." "), $where).")";
			}
			return $where;
		}
		
		function batch_import_csv1()
		{
			$obj = new stdClass();
			$db = $this->getDbc();
			$session = JFactory::getSession();
			
			$fl_counter = $session->get('fl_counter', 0);
			$paths = $session->get('file_path');
			//total files size
			$size = 0;
			foreach($paths as $ct=>$path){
				$size = $size+filesize($path);
			}
			$totalFiles = count($paths);
			if(!isset($paths[$fl_counter])){
				$obj->result = 'error';
				$obj->error = JText::_('FILE_NOT_FOUND');
				return $obj;
			}
			$path = $paths[$fl_counter];
			
			if(!is_file($path))	{
				$obj->result = 'error';
				$obj->error = JText::_('PLZ_SELECT_FILE');
				return $obj;
			}
			$cur_size = filesize($path);
			if( $cur_size == 0 )	{
				$obj->result = 'error';
				$obj->error = JText::_('PLZ_UPLOAD_VALID_CSV_FILE');
				return $obj;
			}
			$csvfields = JFactory::getApplication()->input->get('csvfield', array(), 'ARRAY');
			if(count($csvfields) == 0)	{
				$obj->result = 'error';
				$obj->error = JText::_('PLZ_UPLOAD_VALID_CSV_FILE');
				return $obj;
			}
			
			$dids = $session->get('dids', array());
			$join_dids = $session->get('join_dids',array());
			
			//Check if the fields are selected
			$fields=array();
			foreach($csvfields as $k=>$v)	{
				if($v != "")	{
					$fields[$k] = $v;
				}
			}
			
			if(count($fields)==0)	{
				$obj->result = 'error';
				$obj->error = JText::_('PLZ_SELECT_CSV_FIELD');
				return $obj;
			}
			
			$offset = JFactory::getApplication()->input->getInt('offset');
			//get csv configuration
			$config = $this->getConfig();
			$delimiter = $this->getDelimiter();
			$enclosure = $this->getEnclosure();
			$csv_child = json_decode($config->csv_child);
			$child_delimiter = $this->getChildDelimiter();
			$headingStatus = $config->csv_header;
			
			$query = 'SHOW KEYS FROM '.$db->quoteName($this->profile->params->table).' WHERE Key_name = "PRIMARY"';
			$db->setQuery( $query );
			// $key = $db->loadObjectList();
			$key = $db->loadAssocList('Column_name');
			if(property_exists($this->profile->params, 'unqkey') && !empty($this->profile->params->unqkey)){
				$primary = $this->profile->params->unqkey;
			}
			elseif(!empty($key)){//&& (count($key)==1)
				// $primary = $key[0]->Column_name;
				$primary = array_keys($key);
			}
			else{
				$primary = null;
			}	
			$fp = @fopen($path, "r");
			fseek($fp,$offset);
			$totalRecordInFile = $session->get('totalRecordInFile',0);
			$images_import = JFactory::getApplication()->input->get('images', array(), 'RAW');
			
			if($offset == 0){
				$fileSpl = new SplFileObject($path, 'r');
				$fileSpl->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
				$fileSpl->seek(PHP_INT_MAX);
				$totalRecordInFile = $fileSpl->key() + $totalRecordInFile;
				if($headingStatus){
					$header = fgetcsv($fp, 100000, $delimiter, $enclosure);
					$totalRecordInFile-=1;				
				}		
				$session->set('totalRecordInFile',$totalRecordInFile);	
				$fileSpl=null;
				//log info
				JLog::add(JText::sprintf('IMPORT_PROFILE', $this->profile->title), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('IMPORT_TABLE', $this->profile->params->table), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('IMPORT_FORMAT', 'CSV'), JLog::INFO, 'com_vdata');
			}
			
			$n = 0;
			for($lm=0; $lm < $config->limit; $lm++)
			{
				if(($data = fgetcsv($fp, 100000, $delimiter, $enclosure)) !== FALSE)
				{	if($this->check_blank($data)){
						$totalRecordInFile-=1;
						$session->set('totalRecordInFile',$totalRecordInFile);
						continue;
					}
					$isNew = true;
					$insert = new stdClass();
					$cached_data = new stdClass();
					$oldData = array();
					$stance = false;
					$intance_column ='';
					if($this->profile->params->operation==3){
						//apply primary key and custom filters
						$where = array();
						$base_in_id = array();
						if(isset($this->profile->params->unqkey)){
							foreach($this->profile->params->unqkey as  $key){
								if($csvfields[$key]!=""){
									$where[] = "(".$db->quoteName($key).' = '.$db->quote($data[$csvfields[$key]]).")";
									$base_in_id[$key] = $data[$csvfields[$key]];
								}
							}
						}
						if(!empty($where)){
							$statement = 'SELECT count(*) FROM ' . $db->quoteName($this->profile->params->table) . ' WHERE %s';
							$query = $db->getQuery(true);
							$db->setQuery(sprintf($statement, implode(' AND ', $where)));
							$result = $db->loadResult();
							if($result>0 && !empty($base_in_id)){
								$dids[] = $base_in_id;
							}
						}
					}
					else{
						foreach($this->profile->params->fields as $column=>$field)	{
							switch($field->data) {
								case 'file':
									if($csvfields[$column]==""){//|| !isset($csvfields[$column])
										break;
									}
									$insert->{$column} = $cached_data->{$column} = $this->getFilteredValue($field, $data[$csvfields[$column]]);
									if( isset($field->format) && ($field->format=='email') && ($insert->{$column}==false)){
										JLog::add(JText::sprintf('VDATA_EMAIL_VALIDATION_FAILED', $column), JLog::ERROR, 'com_vdata');
										unset($insert->{$column}, $cached_data->{$column});
									}
									if($field->format == "encrypt"){
										$cached_data->{$column} = $data[$csvfields[$column]];
									}
									if($field->format == "image"){
										if($this->profile->params->table =='#__k2_items'){
											//$insert->{$column} is the id of k2_item
											$insert->{$column} = md5("Image".$insert->{$column}).'.jpg';
										}
										$filename = $insert->{$column};$destination = rtrim($field->location,'/').'/'. basename($filename);	
										$image_source=$path_type='';
										if(isset($images_import['root'][$column])){
											$image_source = isset($images_import['root'][$column]['image_source'])?$images_import['root'][$column]['image_source']:'';
											$path_type = isset($images_import['root'][$column]['path_type'])?$images_import['root'][$column]['path_type']:'';											
										}	
										if(!$image = $this->uploadImage($filename,$destination,$image_source,$path_type,$err)){
											if($this->profile->params->table =='#__k2_items'){
												JLog::add($err, JLog::ERROR, 'com_vdata');
												$insert->{$column} = '0';break;
											}
											//If you want to insert the Image filename rather than its full address, then change $destination to $filename	
											$insert->{$column} = $cached_data->{$column} = $destination;
											$obj->result = 'error';
											$obj->error = $err;
											return $obj;
										}
										if($this->profile->params->table =='#__k2_items'){
											$insert->{$column} = '0';
										}
									} 
								break;
								case 'defined':
									$defined = $field->default;
									$defined = $this->getDefinedValue($field->default, $this->profile->params->fields, $data, $csvfields);
									$insert->$column = $defined;
									$cached_data->{$column} = $defined;
									
								break;
								case 'reference':
									$insert_ref = new stdClass();
									foreach($field->reftext as $i=>$ref){
										if($csvfields[$column][$ref]!="") {
											$insert_ref->{$ref} = $data[$csvfields[$column][$ref]];
										}
									}
									
									$insert_ref_val = (array)$insert_ref;
									if(!empty($insert_ref_val)){
										$ref_value = $this->getReferenceVal($field->table, $field->on, $insert_ref, $db);
										if(!empty($ref_value)){
											$insert->$column = $ref_value;
											$cached_data->{$column} = $ref_value;
										}
										else{
											JLog::add(JText::sprintf('LOCAL_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
										}
									}
									else{
										JLog::add(JText::sprintf('REMOTE_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
									}
								break;
								case 'asset_reference':
									$stance = true;
									$insert->$column = 0;
									$intance_column = $field->on;
									$instance_component = $field->table;
									if($csvfields[$column]['rules']!=""){
										$insert->rules = json_decode($data[$csvfields[$column]['rules']], true);
									}
								break;
							}
						}
					}
					//check if record already exists
					$where = array();
					if( !empty($primary)  ){
						foreach($primary as $keyCol){
							if(isset($insert->{$keyCol})){
							$where[] = $db->quoteName($keyCol).'='.$db->quote($insert->{$keyCol});
							}
						}
						if(!empty($where)){
							$query = 'SELECT * FROM '.$db->quoteName($this->profile->params->table).' WHERE '.implode(' AND ', $where);
							$db->setQuery( $query );
							$oldData = $db->loadAssoc();
							$isNew = empty($oldData) ? true : false;
						}
					}
		
					$afterState = false;
					if($this->profile->params->operation==3){
						//delete data operation
					}
					elseif($this->profile->params->operation==1){
						//insert base table record
						if(!$isNew){
							// JLog::add(JText::sprintf('RECORD_EXISTS',$primary, $insert->$primary), JLog::ERROR, 'com_vdata');
							JLog::add(JText::sprintf('RECORD_ALREADY_EXISTS',implode(',',$where)), JLog::ERROR, 'com_vdata');
							
							continue;
						}
						//capture events before insert
						if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
							//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
							$response = $this->captureEventsOnRecord( $insert, 'before', $isNew , array('pk'=>$primary ,'old'=>$oldData));
							if(in_array(false, $response)){
								JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
								continue;
							}
						}
						$base_join_val = null;
						if($stance){
							$component = JText::_($instance_component);
							$component_name = $component;
							if (strpos(strtolower($component), "com_") ===FALSE){
								$component_name = "com_".strtolower($component_name); 
							}
							if (strpos($component, "com_")===0){
								$component = str_replace("com_", '',strtolower($component)); 
							}
							JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
							
							$core_tables = array('category', 'content', 'menu', 'module');
							if(in_array($intance_column, $core_tables)){
								$row = JTable::getInstance($intance_column);
							}
							else{
								$row = JTable::getInstance($intance_column, $component.'Table');
							}
							
							if(property_exists($row, 'parent_id')){
								$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
								@$row->setLocation($parent_id, 'last-child');
							}
							
							if (!$row->bind((array)$insert)) {
								JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $row->getError();
								return $obj;
							}
							if (!$row->check()) {
								JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $row->getError();
								return $obj;
							}
							if (!$row->store()){
								JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $row->getError();
								return $obj;
							}
							if(!empty($primary)){
								$base_in_id = array();
								foreach($primary as $pkey){
									if(property_exists($row, $pkey)){
										$base_in_id[$pkey] = $row->{$pkey};
									}
								}
							}
							/* if(property_exists($row, $primary)){
								$base_in_id = $row->{$primary};
							} */
							
							$n++;
							$afterState = true;
							if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($row, $this->profile->params->joins->column1[0])){
								$base_join_val = $row->{$this->profile->params->joins->column1[0]};
							}
						}
						else{
							if(isset($this->profile->params->component->table) && !empty($this->profile->params->component->table)){
								$component = JText::_($this->profile->params->component->value);
								$component_name = $component;
								if (strpos(strtolower($component), "com_") ===FALSE){
									$component_name = "com_".strtolower($component_name); 
								}
								if (strpos(strtolower($component), "com_") ===0){
									$component = str_replace("com_", '',strtolower($component)); 
								}
								JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
								$core_tables = array('category', 'content', 'menu', 'module');
								if(in_array($this->profile->params->component->table, $core_tables)){
									$row = JTable::getInstance($this->profile->params->component->table);
								}
								else{
									$row = JTable::getInstance($this->profile->params->component->table, $component.'Table');
								}
								
								$row->load(null);
								if(property_exists($row, 'parent_id')){
									$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
									@$row->setLocation($parent_id, 'last-child');
								}
								
								if (!$row->bind((array)$insert)) {
									JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $row->getError();
									return $obj;
								}
								
								if (!$row->check()) {
									JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $row->getError();
									return $obj;
								}
								
								if (!$row->store()){
									JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $row->getError();
									return $obj;
								}
								$n++;
								$afterState = true;
								
								if(!empty($primary)){
									$base_in_id = array();
									foreach($primary as $pkey){
										if(property_exists($row, $pkey)){
											$base_in_id[$pkey] = $row->{$pkey};
										}
									}
								}
								/* if(!empty($primary) && property_exists($row, $primary)){
									$base_in_id = $row->{$primary};
								} */
								if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($row, $this->profile->params->joins->column1[0])){
									$base_join_val = $row->{$this->profile->params->joins->column1[0]};
								}
							}
							else{
								if($db->insertObject($this->profile->params->table, $insert)){
									if($this->getAffected($db)> 0){
										$n++;
										$afterState = true;
									}
									$base_in_id = array();
									foreach($primary as $pkey){
										if(isset($insert->{$pkey})){
											$base_in_id[$pkey] = $insert->{$pkey};
										}
									}
										
									if((!empty($primary) && count($primary)==1) || (isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && !property_exists($insert, $profile_params->joins->column1[0]))){
										$newId = $db->insertid();
										if($primary[0]!='id'){
											$query = 'SHOW KEYS FROM '.$db->quoteName($this->profile->params->table).' WHERE Key_name = "PRIMARY"';
											$db->setQuery( $query );
											// $key = $db->loadObjectList();
											$key = $db->loadAssocList('Column_name');
											$pri = array_keys($key)[0];
											$cached_data->{$pri} = $insert->{$pri} = $newId;
										}else{
											$cached_data->{$primary[0]} = $insert->{$primary[0]} = $db->insertid();
										}
									}
									if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($insert, $this->profile->params->joins->column1[0])){
										$base_join_val = $insert->{$this->profile->params->joins->column1[0]};
									}
									
								}
								else{
									JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $db->stderr();
									return $obj;
								}
							}
						}
						if($afterState){
							//capture events after insert
							if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
								if(!empty($primary)){
									foreach($primary as $pkey){
										if(property_exists($cached_data, $pkey) && isset($base_in_id[$pkey])){
											$cached_data->{$pkey} = $base_in_id[$pkey];
										}
									}
								}
								/* if(!property_exists($cached_data, $primary)){
									$cached_data->{$primary} = $base_in_id;
								} */
								$response = $this->captureEventsOnRecord( $cached_data, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
							}
						}
					}
					else{
						if(empty($primary) ){//|| empty($insert->{$primary})
							$obj->result = 'error';
							$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
							return $obj;
						}
						else{
							foreach($primary as $pkey){
								if(!isset($insert->{$pkey}) || empty($insert->{$pkey})){
									$obj->result = 'error';
									$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
									return $obj;
								}
							}
						}
						
						//capture events before update
						if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
							//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
							$response = $this->captureEventsOnRecord( $insert, 'before',$isNew , array('pk'=>$primary ,'old'=>$oldData));
							if(in_array(false, $response)){
								JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
								continue;
							}
						}
						
						$base_join_val = null;
						if($stance){
							$component = JText::_($instance_component);
							$component_name = $component;
							if (strpos(strtolower($component), "com_") ===FALSE){
								$component_name = "com_".strtolower($component_name); 
							}
							if (strpos(strtolower($component), "com_") ===0){
								$component = str_replace("com_", '',strtolower($component)); 
							}
							JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
							$core_tables = array('category', 'content', 'menu', 'module');
							if(in_array($intance_column, $core_tables)){
								$row = JTable::getInstance($intance_column);
							}
							else{
								$row = JTable::getInstance($intance_column, $component.'Table');
							}
							
							
							
							$loadFields = array();
							foreach($primary as $pkey){
								if(isset($insert->{$pkey}) && !empty($insert->{$pkey})){
									$loadFields[$pkey] = $insert->{$pkey};
								}
							}
							$row->load($loadFields);//
							// $row->load($insert->{$primary});
							if(property_exists($row, 'parent_id')){
								if (isset($row->id) && isset($insert->parent_id) && $row->id==1 && $insert->parent_id==1)
									$insert->parent_id = 0; 
								$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
								if ($row->parent_id!=$parent_id)
									@$row->setLocation($parent_id, 'last-child');
							}
							if($isNew){
								// JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
								JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
							}
							
							if (!$row->bind((array)$insert)) {
								JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $row->getError();
								return $obj;
							}
							if (!$row->check()) {
								JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $row->getError();
								return $obj;
							}
							if (!$row->store()){
								JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $row->getError();
								return $obj;
							}
							$n++;
							$afterState = true;
							if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($row, $this->profile->params->joins->column1[0])){
								$base_join_val = $row->{$this->profile->params->joins->column1[0]};
							}
						}
						else{
							if($isNew){
								// JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
								JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
								if(isset($this->profile->params->component->table) && !empty($this->profile->params->component->table)){
									$component = JText::_($this->profile->params->component->value);
									$component_name = $component;
									if (strpos(strtolower($component), "com_") ===FALSE){
										$component_name = "com_".strtolower($component_name); 
									}
									if (strpos(strtolower($component), "com_") ===0){
										$component = str_replace("com_", '',strtolower($component)); 
									}
									JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
									$core_tables = array('category', 'content', 'menu', 'module');
									if(in_array($this->profile->params->component->table, $core_tables)){
										$row = JTable::getInstance($this->profile->params->component->table);
									}
									else{
										$row = JTable::getInstance($this->profile->params->component->table, $component.'Table');
									}
									
									$row->load(null);
									if(property_exists($row, 'parent_id')){
										$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
										@$row->setLocation($parent_id, 'last-child');
									}
									if (!$row->bind((array)$insert)) {
										JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
										$obj->result = 'error';
										$obj->error = $row->getError();
										return $obj;
									}
									if (!$row->check()) {
										JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
										$obj->result = 'error';
										$obj->error = $row->getError();
										return $obj;
									}
									if (!$row->store()){
										JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
										$obj->result = 'error';
										$obj->error = $row->getError();
										return $obj;
									}
									$n++;
									$afterState = true;
									/* if(!empty($primary) && property_exists($row, $primary)){
										$base_in_id = $row->{$primary};
									} */
									if(!empty($primary)){
										$base_in_id = array();
										foreach($primary as $pkey){
											if(property_exists($row, $pkey)){
												$base_in_id[$pkey] = $row->{$pkey};
											}
										}
									}
									
									if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($row, $this->profile->params->joins->column1[0])){
										$base_join_val = $row->{$this->profile->params->joins->column1[0]};
									}
								}
								else{
									if($db->insertObject($this->profile->params->table, $insert)){
										if($this->getAffected($db)> 0){
											$n++;
											$afterState = true;
										}
										$base_in_id = array();
										foreach($primary as $pkey){
											if(isset($insert->{$pkey})){
												$base_in_id[$pkey] = $insert->{$pkey};
											}
										}
										
										
										if((!empty($primary) && count($primary)==1) || (isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && !property_exists($insert, $profile_params->joins->column1[0]))){
											$newId = $db->insertid();
											if($primary[0]!='id'){
												$query = 'SHOW KEYS FROM '.$db->quoteName($this->profile->params->table).' WHERE Key_name = "PRIMARY"';
												$db->setQuery( $query );
												// $key = $db->loadObjectList();
												$key = $db->loadAssocList('Column_name');
												$pri = array_keys($key)[0];
												$cached_data->{$pri} = $insert->{$pri} = $newId;
											}else{
												$cached_data->{$primary[0]} = $insert->{$primary[0]} = $db->insertid();
											}
										}
										if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($insert, $this->profile->params->joins->column1[0])){
											$base_join_val = $insert->{$this->profile->params->joins->column1[0]};
										}
										
									}
									else{
										JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
										$obj->result = 'error';
										$obj->error = $db->stderr();
										return $obj;
									}
								}
							}
							else{
								if(isset($this->profile->params->component->table) && !empty($this->profile->params->component->table)){
									$component = JText::_($this->profile->params->component->value);
									$component_name = $component;
									if (strpos(strtolower($component), "com_") ===FALSE){
										$component_name = "com_".strtolower($component_name); 
									}
									if (strpos(strtolower($component), "com_") ===0){
										$component = str_replace("com_", '',strtolower($component)); 
									}
									JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
									$core_tables = array('category', 'content', 'menu', 'module');
									if(in_array($this->profile->params->component->table, $core_tables)){
										$row = JTable::getInstance($this->profile->params->component->table);
									}
									else{
										$row = JTable::getInstance($this->profile->params->component->table, $component.'Table');
									}
									$loadFields = array();
									foreach($primary as $pkey){
										if(isset($insert->{$pkey}) && !empty($insert->{$pkey})){
											$loadFields[$pkey] = $insert->{$pkey};
										}
									}
									$row->load($loadFields);
									// $row->load($insert->{$primary});
									if(property_exists($row, 'parent_id')){
										if (isset($row->id) && isset($insert->parent_id) && $row->id==1 && $insert->parent_id==1)
											$insert->parent_id = 0; 
										$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
										if ($row->parent_id!=$parent_id){
											@$row->setLocation($parent_id, 'last-child');
										}
									}
									
									if (!$row->bind((array)$insert)) {
										JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
										$obj->result = 'error';
										$obj->error = $row->getError();
										return $obj;
									}
									if (!$row->check()) {
										JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
										$obj->result = 'error';
										$obj->error = $row->getError();
										return $obj;
									}
									if (!$row->store()){
										JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
										$obj->result = 'error';
										$obj->error = $row->getError();
										return $obj;
									}
									$n++;
									$afterState = true;
									/* if(!empty($primary) && property_exists($row, $primary)){
										$base_in_id = $row->{$primary};
									} */
									if(!empty($primary)){
										$base_in_id = array();
										foreach($primary as $pkey){
											if(property_exists($row, $pkey)){
												$base_in_id[$pkey] = $row->{$pkey};
											}
										}
									}
									if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($row, $this->profile->params->joins->column1[0])){
										$base_join_val = $row->{$this->profile->params->joins->column1[0]};
									}
								}
								else{
									if($db->updateObject($this->profile->params->table, $insert, $primary)){
										if($this->getAffected($db)> 0){
											$n++;
											$afterState = true;
										}
										// $base_in_id = $insert->{$primary};
										$base_in_id = array();
										foreach($primary as $pkey){
											if(isset($insert->{$pkey})){
												$base_in_id[$pkey] = $insert->{$pkey};
											}
										}
										if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0){
											if(property_exists($insert, $this->profile->params->joins->column1[0])){
												$base_join_val = $insert->{$this->profile->params->joins->column1[0]};
											}else{
												//check if record already exists
												$where = array();
												if( !empty($primary)  ){
													foreach($primary as $keyCol){
														if(isset($insert->{$keyCol})){
														$where[] = $db->quoteName($keyCol).'='.$db->quote($insert->{$keyCol});
														}
													}
													if(!empty($where)){
														$query = 'SELECT '.$this->profile->params->joins->column1[0].' FROM '.$db->quoteName($this->profile->params->table).' WHERE '.implode(' AND ', $where);
														$db->setQuery( $query );
														$base_join_val = $cached_data->{$this->profile->params->joins->column1[0]} = $insert->{$this->profile->params->joins->column1[0]} = $db->loadResult();
													}
												}
											}
										}
										
									}
									else{
										JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
										$obj->result = 'error';
										$obj->error = $db->stderr();
										return $obj;
									}
								}
							}
						}
						
						
						$dids[] = $base_in_id;
						
						if($afterState){
							//capture events after update
							if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
								/* if(!property_exists($cached_data, $primary)){
									$cached_data->{$primary} = $base_in_id;
								} */
								if(!empty($primary)){
									foreach($primary as $pkey){
										if(property_exists($cached_data, $pkey) && isset($base_in_id[$pkey])){
											$cached_data->{$pkey} = $base_in_id[$pkey];
										}
									}
								}
								$response = $this->captureEventsOnRecord( $cached_data, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
							}
						}
					}
					if($this->profile->params->operation!=3){
						//insert joined tables record
						$join_dids[] = $base_join_val;
						$flag = $this->insertCsvJoinRecords($base_join_val, $this->profile->params->joins, $data, $csvfields, $this->profile->params->operation);
						if($flag->result=='error'){
							JLog::add(JText::sprintf('INSERT_FAIL',$flag->error), JLog::ERROR, 'com_vdata');
							$obj->result = 'error';
							$obj->error = $flag->error;
							return $obj;
						}
					}
					
				}
				else{
					if($fl_counter<($totalFiles-1)){

						$obj->result='success';
						// $obj->size = filesize($paths[$fl_counter]);
						$obj->size = $size;
						
						$progress = ftell($fp);
						// $progress = filesize($fath);
						if($fl_counter>0){
							for($ft=0;$ft<$fl_counter;$ft++){
								$progress += filesize($paths[$ft]);
							}
						}
						$obj->progress = $progress;
						
						$fl_counter = $fl_counter+1;
						$session->set('fl_counter', $fl_counter);
						
						$obj->offset = 0;
						$qty = $session->get('qty',0) + $n;
						$session->set('qty',$qty);
						$obj->totalRecordInFile = $totalRecordInFile;
						return $obj;
					}
					
					JLog::add(JText::sprintf('IMPORT_RECORD_COUNT', $session->get('qty',0)+$n), JLog::INFO, 'com_vdata');
					if( ($this->profile->params->operation==2) || ($this->profile->params->operation==3) ){
						$deleteQry = 'DELETE FROM '.$db->quoteName($this->profile->params->table). ' WHERE %s';
						$whereCon = array();
						
						foreach($dids as $did){
							if(is_array($did)){
								$where = array();
								foreach($did as $pkey=>$pval){
									$where[] = $db->quoteName($pkey).' <> '.$db->quote($pval);
								}
								$whereCon[] = ' ('.implode(' AND ', $where).')';
							}
							else{
								if(is_array($primary))
									$whereCon[] = ' '.$db->quoteName($primary[0]).' <> '.$db->quote($did);
								else
									$whereCon[] = ' '.$db->quoteName($primary).' <> '.$db->quote($did);
								
							}
						}
						
						//apply delete filter for delete operation
						if($this->profile->params->operation==3){
							//apply delete filters
							if(isset($this->profile->params->filters->column)){
								$filterValues = $this->importDeleteFilter($db, $this->profile->params);
								if(!empty($filterValues)){
									$whereCon[] = $filterValues;
								}
							}
						}
						if(!empty($whereCon)){
							$db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
							$res = $db->execute();
						}
						if(isset($this->profile->params->joins)){
							$whereCon = array();
							$table2 = $this->profile->params->joins->table2;
							for($i=0;$i<count($table2);$i++){
								$deleteQry = 'DELETE FROM '.$db->quoteName($table2[$i]). ' WHERE %s';
								foreach($join_dids as $jdid){
									 if(is_array($jdid)){
										$where = array();
										foreach($jdid as $pkey=>$pval){
											$where[] = $db->quoteName($pkey).' <> '.$db->quote($pval);
										}
										$whereCon[] = ' ('.implode(' AND ', $where).')';
									}
									else{ 
										$whereCon[] = $db->quoteName($this->profile->params->joins->column2[$i]).' <> '.$db->quote($jdid);
									}
								}
								if(!empty($whereCon)){
									$db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
									$res = $db->execute();
								}
							}
						}
					}
					$session->clear('dids');
					$session->clear('join_dids');
					
					//remove copied file
					
					//send notification if done from site particular
					if(JFactory::getApplication()->isSite()){
						$notify = $this->sendNotificationEmail( $this->profile->iotype,0 , array('schedule_id'=>"",'schedule_title'=>"","schedule_qry"=>"",'profile_id'=>$this->profile->id,'profile_title'=>$this->profile->title,"count"=>($session->get('qty',0)+$n), "format"=>"csv") );
					}
					
					$obj->result = 'error';
					$obj->offset = ftell($fp);
					$obj->size = $size;
					
					$progress = ftell($fp);
					if($fl_counter>0){
						for($ft=0;$ft<$fl_counter;$ft++){
							$progress += filesize($paths[$ft]);
						}
					}
					$obj->progress = $progress;
					
					$obj->error = JText::_('NO_RECORD_TO_IMPORT');
					$obj->qty = $session->get('qty',0) + $n;
					$obj->totalRecordInFile = $totalRecordInFile;
					return $obj;
				}
			}
			
			$session->set('dids', $dids);
			$session->set('join_dids', $join_dids);
			$child_pos = ftell($fp);
			$obj->result='success';
			$obj->size = $size;
			
			$progress = $child_pos;
			if($fl_counter>0){
				for($ft=0;$ft<$fl_counter;$ft++){
					$progress += filesize($paths[$ft]);
				}
			}
			$obj->progress = $progress;
			
			//file pointer position for next batch
			$obj->offset = $child_pos;
			$qty = $session->get('qty',0) + $n;
			$session->set('qty',$qty);
			$obj->totalRecordInFile = $totalRecordInFile;
			return $obj;
			
		}
		
		
		function getReferenceVal($table, $column, $ref_col, $db){
			
			$query = $db->getQuery(true);
			$query = "select ".$db->quoteName($column)." from ".$db->quoteName($table)." where 1=1";
			$blank=0;
			foreach($ref_col as $ref_key=>$ref_val){
				if(empty($ref_val) || $ref_val==''){$blank++;continue;}
				$query .= " and ".$db->quoteName($ref_key)." = ".$db->quote($ref_val);
			}
			if(count($ref_col)!=$blank){
				$db->setQuery($query);
				$ref_value = $db->loadResult();
			}else{
				$ref_value=null;
			}
			return $ref_value;
		}
		
		function insertCsvJoinRecords($base_in_id, $params, $data, $fields, $operation=1){
			
			$obj = new stdClass();
			$previous_data = array();
			
			if(!empty($params->table2)){
				
				$abc = 0;
				$done = array();
				for($out=0;$abc<count($params->table2);$out++){
					
					if($params->join[$out]=='left_join' || $params->join[$out]=='join'){
						if(!in_array($out, $done)){
							
							// print_r($params->table2[$out]);
							
							$flag = $this->addChildRow($out,$base_in_id, $params, $data, $fields, $previous_data, $operation);
							if($flag->result=='success'){
								$abc++;
								$done[] = $out;
								$previous_data = $flag->previous_data;
							}
							else{
								$obj->result = 'error';
								$obj->error = $flag->error;
								return $obj;
							}
						}
					}
					elseif($params->join[$out]=='right_join'){
						if($abc==(count($params->table2)-1)){
							
							// print_r($params->table2[$out]);
							
							$flag = $this->addChildRow($out,$base_in_id, $params, $data, $fields, $previous_data, $operation);
							if($flag->result=='success'){
								$abc++;
								$done[] = $out;
								$previous_data = $flag->previous_data;
							}
							else{
								$obj->result = 'error';
								$obj->error = $flag->error;
								return $obj;
							}
						}
						continue;
					}
					
					if($out==count($params->table2)-1){
						$out = -1;
					}
				}
			}
			$obj->result = 'success';
			return $obj;
		}
		
		function addChildRow($i,$base_in_id, $params, $data, $fields, $previous_data, $operation){
			
			$obj = new stdClass();
			$db = $this->getDbc();
			$new_data[$params->column1[$i].':'.$params->column2[$i]] = array();
			$table2 = $params->table2[$i];
			$child_count = $this->getCsvChildCount($table2, $data, $params->columns[$i], $fields);
			$child_delimiter = $this->getChildDelimiter();
			
			for($k=0; $k<$child_count; $k++){
				
				$insert = new stdClass();
				$stance = false;
				$isNew = false;
				if($i==0){
					$insert->{$params->column2[0]} = $base_in_id;
				}
				/* if(!empty($previous_data)){
					$keys = array_keys($previous_data);
					$column_keys = explode(':', trim($keys[0]));
					$insert->$column_keys[0] = $previous_data[$keys[0]][$k]->$column_keys[1];
				} */
				
				if(!empty($previous_data)){
					if($params->join[$i]=='left_join' || $params->join[$i]=='join'){
						$keys = array_keys($previous_data);
						if(isset($previous_data[$keys[0]][$k]) && property_exists($previous_data[$keys[0]][$k], $params->column1[$i])){
							$insert->{$params->column2[$i]} = $previous_data[$keys[0]][$k]->{$params->column1[$i]};
						}else{
							$insert->{$params->column2[$i]} = $base_in_id;
						}
					}
					elseif($params->join[$i]=='right_join'){
						$keys = array_keys($previous_data);
						$column_keys = explode(':', trim($keys[0]));
						$insert->$column_keys[0] = $previous_data[$keys[0]][$k]->$column_keys[1];
					}
				}
				
				foreach($params->columns[$i] as $column=>$options){
					
					switch($options->data){
						case 'file':
							if($fields[$table2][$column]==""){
								break;
							}
							if($child_count>1){
								$tmp_val = explode($child_delimiter , $data[$fields[$table2][$column]]);
								$insert->{$column} =  $this->getFilteredValue($options, $tmp_val[$k]);
							}
							else{
								$insert->{$column} =  $this->getFilteredValue($options, $data[$fields[$table2][$column]]);
							}
							if( isset($options->format) && ($options->format=='email') && ($insert->{$column}==FALSE) ){
								JLog::add(JText::sprintf('VDATA_EMAIL_VALIDATION_FAILED', $column), JLog::ERROR, 'com_vdata');
								unset($insert->{$column});
							}
						break;
						case 'defined':
							$insert->{$column} = $this->getDefinedValue($options->default, $params->columns[$i], $data, $fields);
						break;
						case 'reference':
						
							$ref_cols = array();
							$menuException = false;
							foreach($options->reftext as $j=>$ref){
								if($fields[$table2][$column][$ref]!="") {
									if($child_count>1){
										$tmp_val = explode($child_delimiter , $data[$fields[$table2][$column][$ref]]);
										if(count($tmp_val)==$child_count){
											if($table2=='#__modules_menu' && preg_match('/^(\(-\)).*$/', $tmp_val[$k], $match)){
												$ref_cols[$ref] = substr($match[0],3);
												$menuException = true;
											}
											else{
												$ref_cols[$ref] = $tmp_val[$k];
											}
										}
										else{
											if($table2=='#__modules_menu' && preg_match('/^(\(-\)).*$/', $data[$fields[$table2][$column][$ref]], $match)){
												$ref_cols[$ref] = substr($match[0],3);
												$menuException = true;
											}
											else{
												$ref_cols[$ref] = $data[$fields[$table2][$column][$ref]];
											}
										}
									}
									else{
										if($table2=='#__modules_menu' && preg_match('/^(\(-\)).*$/', $data[$fields[$table2][$column][$ref]], $match)){
											$ref_cols[$ref] = substr($match[0],3);
											$menuException = true;
										}
										else{
											$ref_cols[$ref] = $data[$fields[$table2][$column][$ref]];
										}
									}
								}
							}
							
							if(!empty($ref_cols)){
								$ref_blank=0;
								$query = "select ".$db->quoteName($options->on)." from ".$db->quoteName($options->table)." where 1=1";
								foreach($ref_cols as $ref_key=>$ref_val){
									if(empty($ref_val) || $ref_val==''){$ref_blank++;continue;}
									$query .= " and ".$db->quoteName($ref_key)." = ".$db->quote($ref_val);
								}
								if(count($ref_cols)!=$ref_blank){	
									$db->setQuery($query);
									$ref_value = $db->loadResult();
								}else{
									$ref_value=null;
								}
								if(!empty($ref_value)){
									if($table2=='#__modules_menu' && $menuException){
										$insert->{$column} = -$ref_value;
									}
									else{
										$insert->{$column} = $ref_value;
									}
								}
								else{
									JLog::add(JText::sprintf('LOCAL_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
								}
							}
							else{
								JLog::add(JText::sprintf('REMOTE_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
							}
						break;
						case 'asset_reference':
							$stance = true;
							$insert->$column = 0;
							$intance_column = $options->on;
							$instance_component = $options->component;
							if($fields[$table2][$column]['rules']!=""){
								$insert->rules = json_decode($data[$fields[$table2][$column]['rules']], true);
							}
						break;
					}
				}
				
				$primary = $this->getPrimaryKey($table2, $db);
				if(!empty($primary) && isset($insert->{$primary})){
					$query = 'SELECT COUNT(*) FROM '.$db->quoteName($table2).' WHERE '.$db->quoteName($primary).'='.$db->quote($insert->{$primary});
					$db->setQuery($query);
					$result = $db->loadResult();
					$isNew = ($result>0)?false:true;
				}else{
					$query = 'SELECT COUNT(*) FROM '.$db->quoteName($table2).' WHERE %s';
					$whereCon = array();
					foreach($insert as $key=>$val){
						$whereCon[] = $db->quoteName($key).' = '.$db->quote($val);
					}
					$db->setQuery(sprintf($query, implode(' AND ', $whereCon)));
					$result = $db->loadResult();
					$isNew = ($result>0)?false:true;
				}
				
				if($stance){
					$component = JText::_($instance_component);
					$component_name = $component;
					if (strpos(strtolower($component), "com_") ===FALSE){
						$component_name = "com_".strtolower($component_name); 
					}
					if (strpos(strtolower($component), "com_") ===0){
						$component = str_replace("com_", '',strtolower($component)); 
					}
					JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
					$core_tables = array('category', 'content', 'menu', 'module');
					if(in_array($intance_column, $core_tables)){
						$row = JTable::getInstance($intance_column);
					}
					else{
						$row = JTable::getInstance($intance_column, $component.'Table');
					}
					
					//update case
					if( ($operation==0) && !$isNew ){
						$row->load($insert->{$primary});
					}
					if(property_exists($row, 'parent_id')){
						if (isset($row->id) && isset($insert->parent_id) && $row->id==1 && $insert->parent_id==1)
							$insert->parent_id = 0; 
						$parent_id = isset($insert->parent_id) && !empty($insert->parent_id)?$insert->parent_id:1;
						if ($row->parent_id!=$parent_id)
							@$row->setLocation($parent_id, 'last-child');
					}
					if (!$row->bind((array)$insert)) {
						JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
						$obj->result = 'error';
						$obj->error = $row->getError();
						return $obj;
					}
					if (!$row->check()) {
						JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
						$obj->result = 'error';
						$obj->error = $row->getError();
						return $obj;
					}
					if (!$row->store()){
						JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
						$obj->result = 'error';
						$obj->error = $row->getError();
						return $obj;
					}
					
				}
				else{
					$flag = new stdClass();
					if(isset($params->component->table[$i]) && !empty($params->component->table[$i])){
						$component = JText::_($params->component->value[$i]);
						$component_name = $component;
						if (strpos(strtolower($component), "com_") ===FALSE){
							$component_name = "com_".strtolower($component_name); 
						}
						if (strpos(strtolower($component), "com_") ===0){
							$component = str_replace("com_", '',strtolower($component)); 
						}
						JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
						$core_tables = array('category', 'content', 'menu', 'module');
						if(in_array($params->component->table[$i], $core_tables)){
							$row = JTable::getInstance($params->component->table[$i]);
						}
						else{
							$row = JTable::getInstance($params->component->table[$i], $component.'Table');
						}
						
						//update case
						if( ($operation==0) && !$isNew ){
							$row->load($insert->{$primary});
						}
						if(property_exists($row, 'parent_id')){
							if (isset($row->id) && isset($insert->parent_id) && $row->id==1 && $insert->parent_id==1)
								$insert->parent_id = 0; 
							$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
							if ($row->parent_id!=$parent_id)
								@$row->setLocation($parent_id, 'last-child');
						}
						if (!$row->bind((array)$insert)) {
							JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
							$obj->result = 'error';
							$obj->error = $row->getError();
							return $obj;
						}
						if (!$row->check()) {
							JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
							$obj->result = 'error';
							$obj->error = $row->getError();
							return $obj;
						}
						if (!$row->store()){
							JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
							$obj->result = 'error';
							$obj->error = $row->getError();
							return $obj;
						}
					}
					else{
						if($this->check_blank($insert)===true){
							$flag->result = 'success';
							$flag->row = $insert;
						}else{
							if(!$isNew){
								if(empty($primary)){
									$flag->result = 'success';
									$flag->row = $insert;
										
								}else{
									
									if($db->updateObject($table2, $insert, is_array($primary)?$primary[0]:$primary)){
										$flag->result = 'success';
										$flag->row = $insert;
									}
									else{
										$flag->result = 'error';
										$flag->error = $db->getErrorMsg();
									}
								  }
							}else{
								if($db->insertObject($table2,$insert)){
									$flag->result = 'success';
									if(!empty($primary)){
									$insert->$primary = $db->insertid();
									}
									$flag->row = $insert;
								}
								else{
									$flag->result = 'error';
									$flag->error = $db->getErrorMsg();
								}
							}
						} 
						if($flag->result=='success'){
							$row = $flag->row;
						}
						else{
							$obj->result="error";
							$obj->error = $flag->error;
							return $obj;
						}
					}
				}
				/* $primary = $this->getPrimaryKey($table2, $db);
				if(!empty($primary)){
					$insert->$primary = $k;
				}
				$row = $insert;
				print_r($row); */
				
				array_push($new_data[$params->column1[$i].':'.$params->column2[$i]], $row);
			
			}
			if($operation==2){
				try{
					$where = array();
					$delQuery = 'DELETE FROM '. $table2 .' WHERE '.$params->column2[0] .' = '. $base_in_id .' AND ';
					//delete from xxxx_k2_comments where itemID=1 and (user_id,username) NOT IN ((0,'test user1'),(135,'Super User'));
					$syncData = array_values($new_data)[0];
					foreach($syncData as $obj){
						$temp ='(';
						$temp .=implode(',',array_diff(array_values(get_object_vars($obj)), [$base_in_id])).')';
						$where[] = $temp;
					}
					$delQuery .= '('.implode(',',array_diff(array_keys(get_object_vars($obj)), [$params->column2[0]])).') NOT IN ('.implode(',',$where).')';
					$db->setQuery($delQuery);
					$db->execute();
				}
				catch (Exception $e){
					$obj->result = 'error';
					$obj->error = $e->getMessage();
					return $obj;
				}
				
			}
			$obj->result = 'success';
			$obj->previous_data = $new_data;
			return $obj;
		}
		
		function getCsvChildCount($table, $data, $child_columns, $fields){
			
			$child_count = 0;
			$child_delimiter = $this->getChildDelimiter();
			foreach($child_columns as $column=>$options){
				if($options->data=='file' && $fields[$table][$column]!=""){
					$childs = explode($child_delimiter, $data[$fields[$table][$column]]);
					$child_count = count($childs);
					break;
				}
				elseif($options->data=='reference'){
					foreach($options->reftext as $key=>$refcol){
						if($fields[$table][$column][$refcol]!=""){
							$childs = explode($child_delimiter, $data[$fields[$table][$column][$refcol]]);
							$child_count = count($childs);
							break 2;
						}
					}
				}
			}
			return $child_count;
		}
		
		
		function insertRow($table_name, $column_name, $insert){
			
			JFactory::getApplication()->input->set('table_name', $table_name);
			JFactory::getApplication()->input->set('column_name', $column_name);
			$row = JTable::getInstance('import', 'Table');
			$obj = new stdClass();
			if (!$row->bind((array)$insert)) {
				JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
				$obj->result = 'error';
				$obj->error = $row->getError();
				return $obj;
			}
			if (!$row->check()) {
				JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
				$obj->result = 'error';
				$obj->error = $row->getError();
				return $obj;
			}
			if (!$row->store()){
				JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
				$obj->result = 'error';
				$obj->error = $row->getError();
				return $obj;
			}
			$obj->result = 'success';
			$obj->row = $row;
			return $obj;
		}
		
		//streaming parser
		function batch_import_xml_quick()
		{
			$obj = new stdClass();
			$db = $this->getDbc();
			// $db = JFactory::getDbo();
			$session = JFactory::getSession();
			$filepath = $session->get('file_path');
			if(!is_file($filepath))	{
				$obj->result = 'error';
				$obj->error = JText::_('PLZ_SELECT_FILE');
				return $obj;
			}
			$size = filesize($filepath);
			if($size == 0)	{
				$obj->result = 'error';
				$obj->error = JText::_('PLZ_UPLOAD_VALID_CSV_FILE');
				return $obj;
			}
			$quick = JFactory::getApplication()->input->getInt('quick',0);
			$quick_fields = JFactory::getApplication()->input->get('xmlfield', array(), 'ARRAY');
			
			$config = $this->getConfig();
			$offset = JFactory::getApplication()->input->getInt('offset');
			
			$dids = $session->get('dids', array());
			
			$query = 'SHOW KEYS FROM '.$db->quoteName($this->profile->params->table).' WHERE Key_name = "PRIMARY"';
			$db->setQuery( $query );
			// $key = $db->loadObjectList();
			$key = $db->loadAssocList('Column_name');
			if(property_exists($this->profile->params, 'unqkey') && !empty($this->profile->params->unqkey)){
				$primary = $this->profile->params->unqkey;
			}
			elseif(!empty($key)){//&& (count($key)==1)
				// $primary = $key[0]->Column_name;
				$primary = array_keys($key);
			}
			else{
				$primary = null;
			}
			$query = 'show fields FROM '.$db->quoteName($this->profile->params->table);
			$db->setQuery( $query );
			$cols = $db->loadObjectList();
			
			//open file
			$fp = @fopen($filepath, 'r');
			//move file pointer
			fseek($fp,$offset);
			
			$rootTag = JFactory::getApplication()->input->get('base', '', 'ARRAY');
			if(empty($rootTag) || count($rootTag)<2){
				$obj->result = 'error';
				$obj->error = JText::_('PLZ_SELECT_ROOT_TAG');
				return $obj;
			}
			$rootTag = $rootTag[count($rootTag)-2];
			
			$hasAttr = $this->getNodeAttr($filepath, $rootTag);
			
			$baseTag = explode('||', $rootTag);
			$baseRoot = $baseTag[count($baseTag)-1];
			
			$tmpRootTag = explode('-', $baseRoot);
			if(count($tmpRootTag)>1){
				$baseRoot = $tmpRootTag[0];
			}
			
			$tmp_str = empty($hasAttr)?'>':' ';
			$baseRootStart = '<'.$baseRoot.$tmp_str;
			$baseRootEnd = '</'.$baseRoot.'>';
			$totalRecordInFile = $session->get('totalRecordInFile',0);

			
			if($quick){
				$n = 0;
				for($i=0; $i<$config->limit; $i++){
					$xml_str = $this->nodeStringFromXML($fp, $baseRootStart, $baseRootEnd);
					$offset = ftell($fp);
					if($xml_str){
						$xml_str = "<ROWSET>".$xml_str."</ROWSET>";
						$xml = @simplexml_load_string($xml_str);
						
						if(!$xml){
							// JLog::add(JText::sprintf('INVALID_XML_FORMAT',libxml_get_last_error()), JLog::ERROR, 'com_vdata');
							$obj->result = 'error';
							$error = libxml_get_last_error();
							$obj->error = JText::_('PARSE_ERROR').$error->message;
							return $obj;
						}
						$totalRecordInFile += count($xml->{$baseRoot});
						foreach($xml->{$baseRoot} as $j=>$row){
							$insert = new stdClass();
							if($this->profile->params->operation==3){
								//apply primary key and custom filters
								$where = array();
								$base_in_id = array();
								if(isset($this->profile->params->unqkey)){
									foreach($this->profile->params->unqkey as  $key){
										if($quick_fields[$key]!=""){
											$dataVal = $this->getXmlFieldData($row, $quick_fields[$key]);
											$where[] = "(".$db->quoteName($key).' = '.$db->quote($dataVal).")";
											$base_in_id[$key] = $dataVal;
										}
									}
								}
								if(!empty($where)){
									$statement = 'SELECT count(*) FROM ' . $db->quoteName($this->profile->params->table) . ' WHERE %s';
									$query = $db->getQuery(true);
									$db->setQuery(sprintf($statement, implode(' AND ', $where)));
									$result = $db->loadResult();
									if($result>0 && !empty($base_in_id)){
										$dids[] = $base_in_id;
									}
								}
							}
							else{
								foreach($cols as $k=>$col){
									if(isset($row->{$col->Field})){
										$insert->{$col->Field} = (string)$row->{$col->Field};
									}
								}
							}
							//if primary key exists check existing record
							$isNew = true;
							$oldData = array();
							$where = array();
							if(!empty($primary)){//&& !empty($insert->{$primary})							
								foreach($primary as $keyCol){
									if(isset($insert->{$keyCol})){
									$where[] = $db->quoteName($keyCol).'='.$db->quote($insert->{$keyCol});
									}
								}
								if(!empty($where)){
									$query = 'SELECT * FROM '.$db->quoteName($this->profile->params->table).' WHERE '.implode(' AND ', $where);
									$db->setQuery( $query );
									$oldData = $db->loadAssoc();
									$isNew = empty($oldData) ? true : false;
								}
							}
							
							if($this->profile->params->operation==3){
								//delete data operation
							}
							elseif($this->profile->params->operation==1){
								if(!$isNew){
									// JLog::add(JText::sprintf('RECORD_EXISTS',$primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
									JLog::add(JText::sprintf('RECORD_ALREADY_EXISTS',implode(',',$where)), JLog::ERROR, 'com_vdata');
									continue;
								}
								//capture events before insert
								if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
									//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
									$response = $this->captureEventsOnRecord( $insert, 'before', $isNew , array('pk'=>$primary ,'old'=>$oldData));
									if(in_array(false, $response)){
										JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
										continue;
									}
								}
								
								if(!$db->insertObject($this->profile->params->table, $insert)){
									JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $db->stderr();
									return $obj;
								}
								if($this->getAffected($db)> 0){
									$n++;
									//capture events after insert
									if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
										//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
										$response = $this->captureEventsOnRecord( $insert, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
									}
								}
									
							}
							else{
								if(empty($primary)){//|| empty($insert->{$primary})
									$obj->result = 'error';
									$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
									return $obj;
								}
								else{
									foreach($primary as $pkey){
										if(!isset($insert->{$pkey}) || empty($insert->{$pkey})){
											$obj->result = 'error';
											$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
											return $obj;
										}
									}
								}
								
								//capture events before update
								if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
									//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
									$response = $this->captureEventsOnRecord( $insert, 'before',$isNew , array('pk'=>$primary ,'old'=>$oldData));
									if(in_array(false, $response)){
										JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
										continue;
									}
								}
								if($isNew){
									// JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
									JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
									if(!$db->insertObject($this->profile->params->table, $insert)){
										JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
										$obj->result = 'error';
										$obj->error = $db->stderr();
										return $obj;
									}
								}
								else{
									if(!$db->updateObject($this->profile->params->table, $insert, $primary)){
										JLog::add(JText::sprintf('UPDATE_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
										$obj->result = 'error';
										$obj->error = $db->stderr();
										return $obj;
									}
								}
								$base_in_id = array();
								foreach($primary as $pkey){
									if(isset($insert->{$pkey})){
										$base_in_id[$pkey] = $insert->{$pkey};
									}
								}
								$dids[] = $base_in_id;
								// $dids[] = $insert->{$primary};
								
								if($this->getAffected($db)> 0){
									$n++;
									//capture events after update
									if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
										//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
										$response = $this->captureEventsOnRecord( $insert, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
									}
								}
									
							}
						}
					}
					else{
						//log info
						JLog::add(JText::sprintf('IMPORT_PROFILE', $this->profile->title), JLog::INFO, 'com_vdata');
						JLog::add(JText::sprintf('IMPORT_TABLE', $this->profile->params->table), JLog::INFO, 'com_vdata');
						JLog::add(JText::sprintf('IMPORT_FORMAT', 'XML'), JLog::INFO, 'com_vdata');
						JLog::add(JText::sprintf('IMPORT_RECORD_COUNT', $session->get('qty',0)+$n), JLog::INFO, 'com_vdata');
						if( ($this->profile->params->operation==2) || ($this->profile->params->operation==3) ){
							$deleteQry = 'DELETE FROM '.$db->quoteName($this->profile->params->table). ' WHERE %s';
							$whereCon = array();
							foreach($dids as $did){
								if(is_array($did)){
									$where = array();
									foreach($did as $pkey=>$pval){
										$where[] = $db->quoteName($pkey).' <> '.$db->quote($pval);
									}
									$whereCon[] = ' ('.implode(' AND ', $where).')';
								}
								else{
									$whereCon[] = ' '.$db->quoteName($primary).' <> '.$db->quote($did);
								}
							}
							//apply delete filter for delete operation
							if($this->profile->params->operation==3){
								//apply filters
								if(isset($this->profile->params->filters->column)){
									$filterValues = $this->importDeleteFilter($db, $this->profile->params);
									if(!empty($filterValues)){
										$whereCon[] = $filterValues;
									}
								}
							}
							if(!empty($whereCon)){
								$db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
								$res = $db->execute();
							}
						}
						$session->clear('dids');
						$obj->totalRecordInFile = $totalRecordInFile;
						$session->clear('totalRecordInFile');
						//send notification if done from site part
						if(JFactory::getApplication()->isSite()){
							$notify = $this->sendNotificationEmail( $this->profile->iotype,0 , array('schedule_id'=>"",'schedule_title'=>"","schedule_qry"=>"",'profile_id'=>$this->profile->id,'profile_title'=>$this->profile->title,"count"=>($session->get('qty',0)+$n), "format"=>"xml") );
						}
						$obj->result = 'error';
						$obj->offset = ftell($fp);
						$obj->size = $size;
						$obj->error = JText::_('NO_RECORD_TO_IMPORT');
						$obj->qty = $session->get('qty',0) + $n;
						return $obj;
					}
					
					
					
				}
			}
			else{
				if(!count($quick_fields)){
					$obj->result = 'error';
					$obj->error = JText::_('SELECT_FIELDS_TO_IMPORT');
					return $obj;
				}
				$n = 0;
				for($i=0; $i<$config->limit; $i++){
					$xml_str = $this->nodeStringFromXML($fp, $baseRootStart, $baseRootEnd);
					$offset = ftell($fp);
					if($xml_str){
						$xml_str = "<ROWSET>".$xml_str."</ROWSET>";
						$xml = @simplexml_load_string($xml_str);
						if(!$xml){
							$obj->result = 'error';
							$error = libxml_get_last_error();
							$obj->error = JText::_('PARSE_ERROR').$error->message;
							return $obj;
						}
						$totalRecordInFile += count($xml->{$baseRoot});
						foreach($xml->{$baseRoot} as $j=>$row){
							$insert = new stdClass();
							if($this->profile->params->operation==3){
								//apply primary key and custom filters
								$where = array();
								$base_in_id = array();
								if(isset($this->profile->params->unqkey)){
									foreach($this->profile->params->unqkey as  $key){
										if($quick_fields[$key]!=""){
											$dataVal = $this->getXmlFieldData($row, $quick_fields[$key]);
											$where[] = "(".$db->quoteName($key).' = '.$db->quote($dataVal).")";
											$base_in_id[$key] = $dataVal;
										}
									}
								}
								if(!empty($where)){
									$statement = 'SELECT count(*) FROM ' . $db->quoteName($this->profile->params->table) . ' WHERE %s';
									$query = $db->getQuery(true);
									$db->setQuery(sprintf($statement, implode(' AND ', $where)));
									$result = $db->loadResult();
									if($result>0 && !empty($base_in_id)){
										$dids[] = $base_in_id;
									}
								}
							}
							else{
								foreach($quick_fields as $k=>$field){
									if($field!=""){
										$insert->{$k} = $this->getXmlFieldData($row, $field);
									}
								}
							}
							//if primary key exists check existing record
							$isNew = true;
							$oldData = array();
							$where = array();
							if(!empty($primary) && property_exists($insert, $primary) && !empty($insert->{$primary})){
								foreach($primary as $keyCol){
									if(isset($insert->{$keyCol})){
									$where[] = $db->quoteName($keyCol).'='.$db->quote($insert->{$keyCol});
									}
								}
								if(!empty($where)){
									$query = 'SELECT * FROM '.$db->quoteName($this->profile->params->table).' WHERE '.implode(' AND ', $where);
									$db->setQuery( $query );
									$oldData = $db->loadAssoc();
									$isNew = empty($oldData) ? true : false;
								}
							}
							
							if($this->profile->params->operation==3){
								//delete data operation
							}
							elseif($this->profile->params->operation==1){
								if(!$isNew){
									// JLog::add(JText::sprintf('RECORD_EXISTS',$primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
									JLog::add(JText::sprintf('RECORD_ALREADY_EXISTS',implode(',',$where)), JLog::ERROR, 'com_vdata');
									continue;
								}
								//capture events before insert
								if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
									//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
									$response = $this->captureEventsOnRecord( $insert, 'before',$isNew, array('pk'=>$primary ,'old'=>$oldData) );
									if(in_array(false, $response)){
										JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
										continue;
									}
								}
								
								if(!$db->insertObject($this->profile->params->table, $insert)){
									JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $db->stderr();
									return $obj;
								}
								if($this->getAffected($db)> 0){
									$n++;
									//capture events after insert
									if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
										//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
										$response = $this->captureEventsOnRecord( $insert, 'after',$isNew, array('pk'=>$primary ,'success'=>true) );
									}
								}
									
							}
							else{
								if(empty($primary)){//|| empty($insert->{$primary})
									$obj->result = 'error';
									$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
									return $obj;
								}
								else{
									foreach($primary as $pkey){
										if(!isset($insert->{$pkey}) || empty($insert->{$pkey})){
											$obj->result = 'error';
											$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
											return $obj;
										}
									}
								}
								//capture events before update
								if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
									//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
									$response = $this->captureEventsOnRecord( $insert, 'before',$isNew, array('pk'=>$primary ,'old'=>$oldData));
									if(in_array(false, $response)){
										JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
										continue;
									}
								}
								if($isNew){
									// JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
									JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
									if(!$db->insertObject($this->profile->params->table, $insert)){
										JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
										$obj->result = 'error';
										$obj->error = $db->stderr();
										return $obj;
									}
								}
								else{
									if(!$db->updateObject($this->profile->params->table, $insert, $primary)){
										JLog::add(JText::sprintf('UPDATE_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
										$obj->result = 'error';
										$obj->error = $db->stderr();
										return $obj;
									}
								}
								$base_in_id = array();
								foreach($primary as $pkey){
									if(isset($insert->{$pkey})){
										$base_in_id[$pkey] = $insert->{$pkey};
									}
								}
								$dids[] = $base_in_id;
								// $dids[] = $insert->{$primary};
								
								if($this->getAffected($db)> 0){
									$n++;
									//capture events after update
									if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
										//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
										$response = $this->captureEventsOnRecord( $insert, 'after', $isNew, array('pk'=>$primary ,'success'=>true) );
									}
								}
									
							}
						}
					}
					else{
						//log info
						JLog::add(JText::sprintf('IMPORT_PROFILE', $this->profile->title), JLog::INFO, 'com_vdata');
						JLog::add(JText::sprintf('IMPORT_TABLE', $this->profile->params->table), JLog::INFO, 'com_vdata');
						JLog::add(JText::sprintf('IMPORT_FORMAT', 'XML'), JLog::INFO, 'com_vdata');
						JLog::add(JText::sprintf('IMPORT_RECORD_COUNT', $session->get('qty',0)+$n), JLog::INFO, 'com_vdata');
						
						if( ($this->profile->params->operation==2) || ($this->profile->params->operation==3) ){
							$deleteQry = 'DELETE FROM '.$db->quoteName($this->profile->params->table). ' WHERE %s';
							$whereCon = array();
							foreach($dids as $did){
								if(is_array($did)){
									$where = array();
									foreach($did as $pkey=>$pval){
										$where[] = $db->quoteName($pkey).' <> '.$db->quote($pval);
									}
									$whereCon[] = ' ('.implode(' AND ', $where).')';
								}
								else{
									$whereCon[] = ' '.$db->quoteName($primary).' <> '.$db->quote($did);
								}
							}
							//apply delete filter for delete operation
							if($this->profile->params->operation==3){
								//apply filters
								if(isset($this->profile->params->filters->column)){
									$filterValues = $this->importDeleteFilter($db, $this->profile->params);
									if(!empty($filterValues)){
										$whereCon[] = $filterValues;
									}
								}
							}
							if(!empty($whereCon)){
								$db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
								$res = $db->execute();
							}
						}
						
						$session->clear('dids');
						$obj->totalRecordInFile = $totalRecordInFile;
						$session->clear('totalRecordInFile');
						//send notification if done from site part
						if(JFactory::getApplication()->isSite()){
							$notify = $this->sendNotificationEmail( $this->profile->iotype,0 , array('schedule_id'=>"",'schedule_title'=>"","schedule_qry"=>"",'profile_id'=>$this->profile->id,'profile_title'=>$this->profile->title,"count"=>($session->get('qty',0)+$n), "format"=>"xml") );
						}
						$obj->result = 'error';
						$obj->offset = ftell($fp);
						$obj->size = $size;
						$obj->error = JText::_('NO_RECORD_TO_IMPORT');
						$obj->qty = $session->get('qty',0) + $n;
						return $obj;
					}
				}
			}
			fclose($fp);
			
			$session->set('dids', $dids);
			$session->set('totalRecordInFile', $totalRecordInFile);
			
			$obj->result = 'success';
			$obj->offset = $offset;
			$obj->size = $size;
			$qty = $session->get('qty',0) + $n;
			$session->set('qty',$qty);
			return $obj;
		}
		
		function nodeStringFromXML($handle, $startNode, $endNode) {
			$cursorPos =ftell($handle);
			// Find start position
			$startPos = $this->getPos($handle, $startNode, $cursorPos);
			// We reached the end of the file or an error
			if($startPos === false) { 
				return false;
			}
			// Find where the node ends
			$endPos = $this->getPos($handle, $endNode, $startPos) + mb_strlen($endNode);
			if($endPos === false){
				return false;
			}
			// Jump back to the start position
			fseek($handle, $startPos);
			// Read the data
			$data = fread($handle, ($endPos-$startPos));
			if($data)
				return $data;
			else
				return false;
		}
		function getPos($handle, $string, $startFrom=0, $chunk=1024) {
			// Set the file cursor on the startFrom position
			fseek($handle, $startFrom, SEEK_SET);
			// Read data
			$data = fread($handle, $chunk);
			
			//preventing parse error
			if(strpos(substr($data,strlen($data)-15),'<')!==false && !empty($string)){
				if(strpos(substr($data,strlen($data)-15),substr($string,0,3))!==false || (strpos(substr($data,strlen($data)-3),'<')!==false)){
				$chunk=$this->getChunk($startFrom,$chunk,$handle,$string);
				fseek($handle, $startFrom, SEEK_SET);
				$data = fread($handle, $chunk);
				}
			}
			
			
			// Try to find the search $string in this chunk 
			$stringPos = mb_strpos($data, $string);
			// We found the string, return the position
			if($stringPos !== false ) {
				return $stringPos+$startFrom;   
			}
			// We reached the end of the file
			if(feof($handle)) {
				return false;
			}
			// Recurse to read more data until we find the search $string it or run out of disk
			return $this->getPos($handle, $string, $chunk+$startFrom);
		}
		
		function getChunk($startFrom,$chunk,$handle,$string){
			$max_chunk=3082;
			// Set the file cursor on the startFrom position
			fseek($handle, $startFrom, SEEK_SET);
			$data = fread($handle, $chunk);
			if(feof($handle)) {
						return $chunk;
			}
			if(strpos(substr($data,strlen($data)-15),'<')!==false){
				if((strpos(substr($data,strlen($data)-15),substr($string,0,3))!==false  && strpos(substr($data,strlen($data)-15),$string)===false) || (strpos(substr($data,strlen($data)-4),'<')!==false)){
					
					$random = rand(100, 300);	
					if($chunk<401)return $max_chunk;
					else $chunk -= $random;
					return $this->getChunk($startFrom,$chunk,$handle,$string);
				}else{
					return $chunk;
				}
			}else{
				return $chunk;
			}
			return $chunk;
		}
		
		function getXmlFieldData($xml, $fields){
			
			$data = '';
			$tmpPath = array();
			$separator = "||";
			foreach($fields as $t=>$field){
				if(empty($field)){
					break;
				}
				
				if($field=='custom_path' && (count($fields)==2)){
					if(empty($fields[count($fields)-1])){
						return $data;
					}
					$data = $this->loadDirectXmlPath($xml, $fields[count($fields)-1]);
					return $data;
				}
				
				$field = $tmp = explode($separator, $field);
				$field = $field[count($field)-1];
				
				if(!empty($tmpPath)){
					for($j=0;$j<count($tmp)-1;$j++){
						if(!in_array($tmp[$j], $tmpPath)){
							$targetPath = array_slice($tmp, 0, $j);
							if(!empty($targetPath)){
								$xml = $this->loadSkippedTag($xml, $tmp[$j]);
							}
						}
					}
				}
				
				if(strrpos($field, '-')!== false){
					
					$fieldIndex = explode('-', $field);
					
					$lastVal = $fieldIndex[0];
					$idx = 0;
					$uniqueFields=array();
					foreach($xml as $k=>$child){
						if( ($idx==$fieldIndex[1]) && ($k==$fieldIndex[0]) ){
							$xml = $child;
							$data = ($child->count())?$child:(string)$child;
							break;
						}
						if(in_array($k,$uniqueFields))	continue;
						$idx++;
						$uniqueFields[] = $k;
						$idx++;
					}
				}
				elseif(strrpos($field, '.')!== false){
					$attrIndex = explode('.', $field);
					
					$lastVal = $attrIndex[0];
					
					foreach($xml->attributes() as $attr=>$value){
						if($attr==$attrIndex[1]){
							$data = (string)$value;
							break;
						}
					}
				}
				/* elseif(!empty($field)){
					
				} */
				
					
				$tmp[count($tmp)-1] = $lastVal;
				$tmpPath = $tmp;
			}
			
			return $data;
			
		}
		
		function loadDirectXmlPath($xml, $path){
			$data = '';
			$path = explode('/', $path);
			/* foreach($path as $node){
				$xml = $this->loadSkippedTag($xml, $node);
			} 
			return $xml;*/
			$targetNode = $path[0];
			$hasAttr = false;
			$attrName = '';
			$newPath = $path;
			if(strrpos($targetNode, '.')!==false){
				$newPath = explode('.', $targetNode);
				if(count($newPath)==2){
					$hasAttr = true;
					$targetNode = $newPath[0];
					$attrName = $newPath[count($newPath)-1];
				}
			}
			$hasIdx = false;
			$idx = 0;
			if(strrpos($targetNode, '-')!==false){
				$newPath = explode('-', $newPath[0]);
				if(count($newPath)==2){
					$targetNode = $newPath[0];
					$hasIdx = true;
					$idx = $newPath[count($newPath)-1];
				}
			}
				
			foreach($xml as $node=>$value){
				
				if($node==$targetNode){
					if($hasIdx && $idx>0){
						$idx--;
						continue;
					}
					if(count($path)==1){
						if($hasAttr){
							foreach($value->attributes() as $attr=>$value){
								if($attr==$attrName){
									return(string)$value;
								}
							}
						}
						return ($value->count())?$value:(string)$value;
					}
					$flag = array_shift($path);
					$data = $this->loadDirectXmlPath($value, implode('/', $path));
				}
			}
			return $data;
		}
		
		function loadSkippedTag($xml, $targetNode){
			
			foreach($xml as $key=>$value){
				
				if($key==$targetNode){
					return ($value->count())?$value:(string)$value;
				}
				
			}
			return $xml;
		}
		
		function getNodeAttr($path, $node){
			
			$attrs = array();
			$xml = new XMLReader();
			if($xml->open($path)==false){
				return false;
			}
			$node = explode('||', $node);
			
			$targetNode = $node[count($node)-1];
			while($xml->read()){
				if($xml->nodeType === XmlReader::ELEMENT){
					if(in_array($xml->name, $node)){
						array_shift($node);
					}
					if(empty($node) && ($xml->name==$targetNode)){
						if($xml->hasAttributes){
							while($xml->moveToNextAttribute()){
								array_push($attrs, $xml->name);
							}
						}
						break;
					}
				}
			}
			return $attrs;
			
		}
		
		function batch_import_xml()
		{
			$obj = new stdClass();
			$db = $this->getDbc();
			
			$session = JFactory::getSession();
			$filepath = $session->get('file_path');
			
			if(!is_file($filepath))	{
				$obj->result = 'error';
				$obj->error = JText::_('PLZ_SELECT_FILE');
				return $obj;
			}
			$size = filesize($filepath);
			if($size == 0)	{
				$obj->result = 'error';
				$obj->error = JText::_('PLZ_UPLOAD_VALID_XML_FILE');
				return $obj;
			}
			
			$xmlfields = JFactory::getApplication()->input->get('xmlfield', array(), 'ARRAY');
			$childTags = JFactory::getApplication()->input->get('root', array(), 'ARRAY');
			if(count($xmlfields) == 0)	{
				$obj->result = 'error';
				$obj->error = JText::_('PLZ_UPLOAD_VALID_XML_FILE');
				return $obj;
			}
			
			$config = $this->getConfig();
			$offset = JFactory::getApplication()->input->getInt('offset');
			
			$dids = $session->get('dids', array());
			$join_dids = $session->get('join_dids', array());
			
			$query = 'SHOW KEYS FROM '.$db->quoteName($this->profile->params->table).' WHERE Key_name = "PRIMARY"';
			$db->setQuery( $query );
			// $key = $db->loadObjectList();
			$key = $db->loadAssocList('Column_name');
			if(property_exists($this->profile->params, 'unqkey') && !empty($this->profile->params->unqkey)){
				$primary = $this->profile->params->unqkey;
			}
			elseif(!empty($key)){//&& (count($key)==1)
				// $primary = $key[0]->Column_name;
				$primary = array_keys($key);
			}
			else{
				$primary = null;
			}
			
			//open file
			$fp = @fopen($filepath, 'r');
			//move file pointer
			fseek($fp,$offset);
			$n = 0;
			
			$rootTag = JFactory::getApplication()->input->get('base', '', 'ARRAY');
			$images_import = JFactory::getApplication()->input->get('images', array(), 'RAW');
			
			if(empty($rootTag) || count($rootTag)<2){
				$obj->result = 'error';
				$obj->error = JText::_('PLZ_SELECT_ROOT_TAG');
				return $obj;
			}
			$rootTag = $rootTag[count($rootTag)-2];
			
			$hasAttr = $this->getNodeAttr($filepath, $rootTag);
			
			$baseTag = explode('||', $rootTag);
			$baseRoot = $baseTag[count($baseTag)-1];
			
			$tmpRootTag = explode('-', $baseRoot);
			if(count($tmpRootTag)>1){
				$baseRoot = $tmpRootTag[0];
			}
			
			$tmp_str = empty($hasAttr)?'>':' ';
			$baseRootStart = '<'.$baseRoot.$tmp_str;
			$baseRootEnd = '</'.$baseRoot.'>';
			
			$totalRecordInFile = $session->get('totalRecordInFile',0);
			for($i=0; $i<$config->limit; $i++)
			{
				$xml_str = $this->nodeStringFromXML($fp, $baseRootStart, $baseRootEnd);
				
				$offset = ftell($fp);
				if($xml_str){
					$xml_str = "<ROWSET>".$xml_str."</ROWSET>";
					$xml = @simplexml_load_string($xml_str);
					if(!$xml){
						$obj->result = 'error';
						$error = libxml_get_last_error();
						$obj->error = JText::_('PARSE_ERROR').' - '.$error->message;
						return $obj;
					}
					$totalRecordInFile += count($xml->{$baseRoot});
					foreach($xml->{$baseRoot} as $j=>$row){
						//var_dump($row);continue;
						$isNew = true;
						$insert = new stdClass();
						$stance = false;
						$cached_data =  new stdClass();
						$oldData = array();
						if($this->profile->params->operation==3){
							//apply primary key and custom filters
							$where = array();
							$base_in_id = array();
							if(isset($this->profile->params->unqkey)){
								foreach($this->profile->params->unqkey as  $key){
									if($xmlfields[$key]!=""){
										$xmlData = $this->getXmlFieldData($row, $xmlfields[$key]);
										$where[] = "(".$db->quoteName($key).' = '.$db->quote($xmlData).")";
										$base_in_id[$key] = $xmlData;
									}
								}
							}
							if(!empty($where)){
								$statement = 'SELECT count(*) FROM ' . $db->quoteName($this->profile->params->table) . ' WHERE %s';
								$query = $db->getQuery(true);
								$db->setQuery(sprintf($statement, implode(' AND ', $where)));
								$result = $db->loadResult();
								if($result>0 && !empty($base_in_id)){
									$dids[] = $base_in_id;
								}
							}
						}
						else{
							foreach($this->profile->params->fields as $column=>$field)	{
								// print_r($xmlfields[$column]);
								switch($field->data){
									case 'file' :
										if($xmlfields[$column]==""){
											break;
										}
										$xmlData = $this->getXmlFieldData($row, $xmlfields[$column]);
										$insert->{$column} = $cached_data->{$column} = $this->getFilteredValue($field, $xmlData);
										if( isset($field->format) && ($field->format=='email') && ($insert->{$column}==false)){
											JLog::add(JText::sprintf('VDATA_EMAIL_VALIDATION_FAILED', $column), JLog::ERROR, 'com_vdata');
											unset($insert->{$column}, $cached_data->{$column});
										}
										if($field->format == "encrypt"){
											$cached_data->{$column} = $xmlData;
										}
										if($field->format == "image"){
											$filename = $insert->{$column};$destination = rtrim($field->location,'/').'/'. basename($filename);
											
											$image_source=$path_type='';
											if(isset($images_import['root'][$column])){
												$image_source = isset($images_import['root'][$column]['image_source'])?$images_import['root'][$column]['image_source']:'';
												$path_type = isset($images_import['root'][$column]['path_type'])?$images_import['root'][$column]['path_type']:'';											
											}	
											if(!$image = $this->uploadImage($filename,$destination,$image_source,$path_type,$err)){
													
												$insert->{$column} = $cached_data->{$column} = $destination;
												$obj->result = 'error';
												$obj->error = $err;
												return $obj;
											}//jexit();
										} 
										
										
									break;
									case 'defined' :
										$defined = $field->default;
										$defined = $this->getDefinedXmlValue($field->default, $this->profile->params->fields, $row, $xmlfields);
										$insert->{$column} = $cached_data->{$column} = $defined;
									break;
									case 'reference' :						
										$insert_ref = new stdClass();
										foreach($field->reftext as $k=>$ref){
											if($xmlfields[$column][$ref]!=""){
												$xmlData = $this->getXmlFieldData($row, $xmlfields[$column][$ref]);
												$insert_ref->{$ref} = $xmlData;
											}
										}
										
										$insert_ref_val = (array)$insert_ref;
										if(!empty($insert_ref_val)){
											$ref_value = $this->getReferenceVal($field->table, $field->on, $insert_ref, $db);
											if(!empty($ref_value)){
												$insert->{$column} = $cached_data->{$column} = $ref_value;
											}
											else{
												JLog::add(JText::sprintf('LOCAL_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
											}
										}
										else{
											JLog::add(JText::sprintf('REMOTE_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
										}
									break;
									case 'asset_reference':
										$stance = true;
										$insert->{$column} = 0;
										$intance_column = $field->on;
										$instance_component = $field->table;
										if(isset($xmlfields[$column]['rules']) && $xmlfields[$column]['rules']!=""){
											$xmlData = $this->getXmlFieldData($row, $xmlfields[$column]['rules']);
											$insert->rules = @json_decode($xmlData, true);
										}
									break;
								}
							}
						}
						//check if id already exists
						$where = array();
						if(!empty($primary)){//&& isset($insert->{$primary})
							foreach($primary as $keyCol){
								if(isset($insert->{$keyCol})){
								$where[] = $db->quoteName($keyCol).'='.$db->quote($insert->{$keyCol});
								}
							}
							if(!empty($where)){
								$query = 'SELECT * FROM '.$db->quoteName($this->profile->params->table).' WHERE '.implode(' AND ', $where);
								$db->setQuery( $query );
								$oldData = $db->loadAssoc();
								$isNew = empty($oldData) ? true : false;
							}
						}
						
						$afterState = false;
						if($this->profile->params->operation==3){
							//delete data operation
						}
						elseif($this->profile->params->operation==1){
							if(!$isNew){
								// JLog::add(JText::sprintf('RECORD_EXISTS',$primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
								JLog::add(JText::sprintf('RECORD_ALREADY_EXISTS',implode(',',$where)), JLog::ERROR, 'com_vdata');
								continue;
							}
							//capture events before insert
							if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
								$response = $this->captureEventsOnRecord( $insert, 'before',$isNew, array('pk'=>$primary ,'old'=>$oldData) );
								if(in_array(false, $response)){
									JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
									continue;
								}
							}
							$base_join_val = null;
							if($stance){
								$component = JText::_($instance_component);
								$component_name = $component;
								if (strpos(strtolower($component), "com_") ===FALSE){
									$component_name = "com_".strtolower($component_name); 
								}
								if (strpos(strtolower($component), "com_") ===0){
									$component = str_replace("com_", '',strtolower($component)); 
								}
								JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
								$core_tables = array('category', 'content', 'menu', 'module');
								if(in_array($intance_column, $core_tables)){
									$tbl_instance = JTable::getInstance($intance_column);
								}
								else{
									$tbl_instance = JTable::getInstance($intance_column, $component.'Table');
								}
								if(property_exists($tbl_instance, 'parent_id')){
									$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
									@$tbl_instance->setLocation($parent_id, 'last-child');
								}

								if (!$tbl_instance->bind((array)$insert)) {
									JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $tbl_instance->getError();
									return $obj;
								}
								if (!$tbl_instance->check()) {
									JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $tbl_instance->getError();
									return $obj;
								}
								if (!$tbl_instance->store()){
									JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $tbl_instance->getError();
									return $obj;
								}
								if(!empty($primary)){
									$base_in_id = array();
									foreach($primary as $pkey){
										if(property_exists($tbl_instance, $pkey)){
											$base_in_id[$pkey] = $tbl_instance->{$pkey};
										}
									}
								}
								/* if(property_exists($tbl_instance, $primary)){
									$base_in_id = $tbl_instance->{$primary};
								} */
								$n++;
								$afterState = true;
								if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($tbl_instance, $this->profile->params->joins->column1[0])){
									$base_join_val = $tbl_instance->{$this->profile->params->joins->column1[0]};
								}
							}
							else{
								if(isset($this->profile->params->component->table) && !empty($this->profile->params->component->table)){
									$component = JText::_($this->profile->params->component->value);
									$component_name = $component;
									if (strpos(strtolower($component), "com_") ===FALSE){
										$component_name = "com_".strtolower($component_name); 
									}
									if (strpos(strtolower($component), "com_") ===0){
										$component = str_replace("com_", '',strtolower($component)); 
									}
									
									JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
									$core_tables = array('category', 'content', 'menu', 'module');
									if(in_array($this->profile->params->component->table, $core_tables)){
										$tbl_instance = JTable::getInstance($this->profile->params->component->table);
									}
									else{
										$tbl_instance = JTable::getInstance($this->profile->params->component->table, $component.'Table');
										
									}
									
									// $tbl_instance->load(null);
									if(property_exists($tbl_instance, 'parent_id')){
										$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
										@$tbl_instance->setLocation($parent_id, 'last-child');
									}
									
									if (!$tbl_instance->bind((array)$insert)) {
										JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
										$obj->result = 'error';
										$obj->error = $tbl_instance->getError();
										return $obj;
									}
									
									if (!$tbl_instance->check()) {
										JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
										$obj->result = 'error';
										$obj->error = $tbl_instance->getError();
										return $obj;
									}
									
									if (!$tbl_instance->store()){
										JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
										$obj->result = 'error';
										$obj->error = $tbl_instance->getError();
										return $obj;
									}
									$n++;
									$afterState = true;
									if(!empty($primary)){
										$base_in_id = array();
										foreach($primary as $pkey){
											if(property_exists($tbl_instance, $pkey)){
												$base_in_id[$pkey] = $tbl_instance->{$pkey};
											}
										}
									}
									/* if(!empty($primary) && property_exists($tbl_instance, $primary)){
										$base_in_id = $tbl_instance->{$primary};
									} */
									if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($tbl_instance, $this->profile->params->joins->column1[0])){
										$base_join_val = $tbl_instance->{$this->profile->params->joins->column1[0]};
									}
								}
								else{
									if($db->insertObject($this->profile->params->table, $insert)){
										if($this->getAffected($db)> 0){
											$n++;
											$afterState = true;
										}
										$base_in_id = array();
										foreach($primary as $pkey){
											if(isset($insert->{$pkey})){
												$base_in_id[$pkey] = $insert->{$pkey};
											}
										}
										
										if((!empty($primary) && count($primary)==1) || (isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && !property_exists($insert, $profile_params->joins->column1[0]))){
											$newId = $db->insertid();
											if($primary[0]!='id'){
												$query = 'SHOW KEYS FROM '.$db->quoteName($this->profile->params->table).' WHERE Key_name = "PRIMARY"';
												$db->setQuery( $query );
												// $key = $db->loadObjectList();
												$key = $db->loadAssocList('Column_name');
												$pri = array_keys($key)[0];
												$cached_data->{$pri} = $insert->{$pri} = $newId;
											}else{
												$cached_data->{$primary[0]} = $insert->{$primary[0]} = $db->insertid();
											}
										}
										if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($insert, $this->profile->params->joins->column1[0])){
											$base_join_val = $insert->{$this->profile->params->joins->column1[0]};
										}
										
									}
									else{
										JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
										$obj->result = 'error';
										$obj->error = $db->stderr();
										return $obj;
									}
								}
							}
							
							if($afterState){
								//capture events after insert
								if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
									//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
									if(!empty($primary)){
										foreach($primary as $pkey){
											if(property_exists($cached_data, $pkey) && isset($base_in_id[$pkey])){
												$cached_data->{$pkey} = $base_in_id[$pkey];
											}
										}
									}
									/* if(!property_exists($cached_data, $primary)){
										$cached_data->{$primary} = $base_in_id;
									} */
									$response = $this->captureEventsOnRecord( $cached_data, 'after',$isNew, array('pk'=>$primary ,'success'=>true) );
								}
							}
						}
						else{
							if(empty($primary)){// || empty($insert->{$primary})
								$obj->result = 'error';
								$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
								return $obj;
							}
							else{
								foreach($primary as $pkey){
									if(!isset($insert->{$pkey}) || empty($insert->{$pkey})){
										$obj->result = 'error';
										$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
										return $obj;
									}
								}
							}
							
							//capture events before update
							if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
								$response = $this->captureEventsOnRecord( $insert, 'before',$isNew, array('pk'=>$primary ,'old'=>$oldData));
								if(in_array(false, $response)){
									JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
									continue;
								}
							}
							$base_join_val = null;
							if($stance){
								$component = JText::_($instance_component);
								$component_name = $component;
								if (strpos(strtolower($component), "com_") ===FALSE){
									$component_name = "com_".strtolower($component_name); 
								}
								if (strpos(strtolower($component), "com_") ===0){
									$component = str_replace("com_", '',strtolower($component)); 
								}
								JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
								$core_tables = array('category', 'content', 'menu', 'module');
								if(in_array($intance_column, $core_tables)){
									$tbl_instance = JTable::getInstance($intance_column);
								}
								else{
									$tbl_instance = JTable::getInstance($intance_column, $component.'Table');
								}
								if(property_exists($tbl_instance, 'parent_id')){
									$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
									@$tbl_instance->setLocation($parent_id, 'last-child');
								}
								
								// $tbl_instance->load($insert->{$primary});
								if($isNew){
									// JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
									JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
								}
								if (!$tbl_instance->bind((array)$insert)) {
									JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $tbl_instance->getError();
									return $obj;
								}
								if (!$tbl_instance->check()) {
									JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $tbl_instance->getError();
									return $obj;
								}
								if (!$tbl_instance->store()){
									JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $tbl_instance->getError();
									return $obj;
								}
								$n++;
								$afterState = true;
								if(!empty($primary)){
									$base_in_id = array();
									foreach($primary as $pkey){
										if(property_exists($tbl_instance, $pkey)){
											$base_in_id[$pkey] = $tbl_instance->{$pkey};
										}
									}
								}
								/* if(property_exists($tbl_instance, $primary)){
									$base_in_id = $tbl_instance->{$primary};
								} */
								if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($tbl_instance, $this->profile->params->joins->column1[0])){
									$base_join_val = $tbl_instance->{$this->profile->params->joins->column1[0]};
								}
							}
							else{
								if($isNew){
									// JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
									JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
									if(isset($this->profile->params->component->table) && !empty($this->profile->params->component->table)){
										$component = JText::_($this->profile->params->component->value);
										$component_name = $component;
										if (strpos(strtolower($component), "com_") ===FALSE){
											$component_name = "com_".strtolower($component_name); 
										}
										if (strpos(strtolower($component), "com_") ===0){
											$component = str_replace("com_", '',strtolower($component)); 
										}
										JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
										$core_tables = array('category', 'content', 'menu', 'module');
										if(in_array($this->profile->params->component->table, $core_tables)){
											$tbl_instance = JTable::getInstance($this->profile->params->component->table);
										}
										else{
											$tbl_instance = JTable::getInstance($this->profile->params->component->table, $component.'Table');
										}
										// $tbl_instance->load(null);
										if(property_exists($tbl_instance, 'parent_id')){
											$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
											@$tbl_instance->setLocation($parent_id, 'last-child');
										}
										if (!$tbl_instance->bind((array)$insert)) {
											JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
											$obj->result = 'error';
											$obj->error = $tbl_instance->getError();
											return $obj;
										}
										if (!$tbl_instance->check()) {
											JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
											$obj->result = 'error';
											$obj->error = $tbl_instance->getError();
											return $obj;
										}
										if (!$tbl_instance->store()){
											JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
											$obj->result = 'error';
											$obj->error = $tbl_instance->getError();
											return $obj;
										}
										$n++;
										$afterState = true;
										if(!empty($primary)){
											$base_in_id = array();
											foreach($primary as $pkey){
												if(property_exists($tbl_instance, $pkey)){
													$base_in_id[$pkey] = $tbl_instance->{$pkey};
												}
											}
										}
										/* if(!empty($primary) && property_exists($tbl_instance, $primary)){
											$base_in_id = $tbl_instance->{$primary};
										} */
										if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($tbl_instance, $this->profile->params->joins->column1[0])){
											$base_join_val = $tbl_instance->{$this->profile->params->joins->column1[0]};
										}
									}
									else{
										if($db->insertObject($this->profile->params->table, $insert)){
											if($this->getAffected($db)> 0){
												$n++;
												$afterState = true;
											}
											$base_in_id = array();
											foreach($primary as $pkey){
												if(isset($insert->{$pkey})){
													$base_in_id[$pkey] = $insert->{$pkey};
												}
											}
											
											if((!empty($primary) && count($primary)==1) || (isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && !property_exists($insert, $profile_params->joins->column1[0]))){
												$newId = $db->insertid();
												if($primary[0]!='id'){
													$query = 'SHOW KEYS FROM '.$db->quoteName($this->profile->params->table).' WHERE Key_name = "PRIMARY"';
													$db->setQuery( $query );
													// $key = $db->loadObjectList();
													$key = $db->loadAssocList('Column_name');
													$pri = array_keys($key)[0];
													$cached_data->{$pri} = $insert->{$pri} = $newId;
												}else{
													$cached_data->{$primary[0]} = $insert->{$primary[0]} = $db->insertid();
												}
											}
											if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($insert, $this->profile->params->joins->column1[0])){
												$base_join_val = $insert->{$this->profile->params->joins->column1[0]};
											}
											
										}
										else{
											JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
											$obj->result = 'error';
											$obj->error = $db->stderr();
											return $obj;
										}
									}
								}
								else{
									if(isset($this->profile->params->component->table) && !empty($this->profile->params->component->table)){
										$component = JText::_($this->profile->params->component->value);
										$component_name = $component;
										if (strpos(strtolower($component), "com_") ===FALSE){
											$component_name = "com_".strtolower($component_name); 
										}
										if (strpos(strtolower($component), "com_") ===0){
											$component = str_replace("com_", '',strtolower($component)); 
										}
										JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
										$core_tables = array('category', 'content', 'menu', 'module');
										if(in_array($this->profile->params->component->table, $core_tables)){
											$tbl_instance = JTable::getInstance($this->profile->params->component->table);
										}
										else{
											$tbl_instance = JTable::getInstance($this->profile->params->component->table, $component.'Table');
										}
										$loadFields = array();
										foreach($primary as $pkey){
											if(isset($insert->{$pkey}) && !empty($insert->{$pkey})){
												$loadFields[$pkey] = $insert->{$pkey};
											}
										}
										$tbl_instance->load($loadFields);
										// $tbl_instance->load($insert->{$primary});
										if(property_exists($tbl_instance, 'parent_id')){
											$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
											if (!$tbl_instance->isLeaf())
												@$tbl_instance->setLocation($parent_id, 'last-child');
										}
										if (!$tbl_instance->bind((array)$insert)) {
											JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
											$obj->result = 'error';
											$obj->error = $tbl_instance->getError();
											return $obj;
										}
										if (!$tbl_instance->check()) {
											JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
											$obj->result = 'error';
											$obj->error = $tbl_instance->getError();
											return $obj;
										}
										if (!$tbl_instance->store()){
											JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
											$obj->result = 'error';
											$obj->error = $tbl_instance->getError();
											return $obj;
										}
										$n++;
										$afterState = true;
										/* if(!empty($primary) && property_exists($tbl_instance, $primary)){
											$base_in_id = $tbl_instance->{$primary};
										} */
										if(!empty($primary)){
											$base_in_id = array();
											foreach($primary as $pkey){
												if(property_exists($tbl_instance, $pkey)){
													$base_in_id[$pkey] = $tbl_instance->{$pkey};
												}
											}
										}
										if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($tbl_instance, $this->profile->params->joins->column1[0])){
											$base_join_val = $tbl_instance->{$this->profile->params->joins->column1[0]};
										}
									}
									else{
										if($db->updateObject($this->profile->params->table, $insert, $primary)){
											if($this->getAffected($db)> 0){
												$n++;
												$afterState = true;
											}
											// $base_in_id = $insert->{$primary};
											$base_in_id = array();
											foreach($primary as $pkey){
												if(isset($insert->{$pkey})){
													$base_in_id[$pkey] = $insert->{$pkey};
												}
											}
											if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 ){
												if(property_exists($insert, $this->profile->params->joins->column1[0])){
													$base_join_val = $insert->{$this->profile->params->joins->column1[0]};
												}else{
													//check if record already exists
													$where = array();
													if( !empty($primary)  ){
														foreach($primary as $keyCol){
															if(isset($insert->{$keyCol})){
															$where[] = $db->quoteName($keyCol).'='.$db->quote($insert->{$keyCol});
															}
														}
														if(!empty($where)){
															$query = 'SELECT '.$this->profile->params->joins->column1[0].' FROM '.$db->quoteName($this->profile->params->table).' WHERE '.implode(' AND ', $where);
															$db->setQuery( $query );
															$base_join_val = $cached_data->{$this->profile->params->joins->column1[0]} = $insert->{$this->profile->params->joins->column1[0]} = $db->loadResult();
														}
													}
												}
											}
										}
										else{
											JLog::add(JText::sprintf('UPDATE_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
											$obj->result = 'error';
											$obj->error = $db->stderr();
											return $obj;
										}
										
									}
								}
							}
							
							$dids[] = $base_in_id;
							
							if($afterState){
								//capture events after update
								if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
									//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
									/* if(!property_exists($cached_data, $primary)){
										$cached_data->$primary = $base_in_id;
									} */
									if(!empty($primary)){
										foreach($primary as $pkey){
											if(property_exists($cached_data, $pkey) && isset($base_in_id[$pkey])){
												$cached_data->{$pkey} = $base_in_id[$pkey];
											}
										}
									}
									$response = $this->captureEventsOnRecord( $cached_data, 'after', $isNew, array('pk'=>$primary ,'success'=>true) );
								}
							}
						}
						
						//insert joined tables record
						if($this->profile->params->operation!=3){
							$join_dids[] = $base_join_val;
							$flag = $this->insertXmlJoinRecords($base_join_val, $this->profile->params->joins, $row, $xmlfields, $rootTag, $childTags, $this->profile->params->operation);
							if($flag->result=='error'){
								$obj->result = 'error';
								$obj->error = $flag->error;
								return $obj;
							}
						}
						
					}
				}
				else{
					//log info
					JLog::add(JText::sprintf('IMPORT_PROFILE', $this->profile->title), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('IMPORT_TABLE', $this->profile->params->table), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('IMPORT_FORMAT', 'XML'), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('IMPORT_RECORD_COUNT', $session->get('qty',0)+$n), JLog::INFO, 'com_vdata');
					if( ($this->profile->params->operation==2 || $this->profile->params->operation==3) ){
						$deleteQry = 'DELETE FROM '.$db->quoteName($this->profile->params->table). ' WHERE %s';
						$whereCon = array();
						foreach($dids as $did){
							if(is_array($did)){
								$where = array();
								foreach($did as $pkey=>$pval){
									$where[] = $db->quoteName($pkey).' <> '.$db->quote($pval);
								}
								$whereCon[] = ' ('.implode(' AND ', $where).')';
							}
							else{
								$whereCon[] = ' '.$db->quoteName($primary).' <> '.$db->quote($did);
							}
						}
						//apply delete filter for delete operation
						if($this->profile->params->operation==3){
							//apply filters
							if(isset($this->profile->params->filters->column)){
								$filterValues = $this->importDeleteFilter($db, $this->profile->params);
								if(!empty($filterValues)){
									$whereCon[] = $filterValues;
								}
							}
						}
						if(!empty($whereCon)){
							$db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
							$res = $db->execute();
						}
						if(isset($this->profile->params->joins)){
							$whereCon = array();
							$table2 = $this->profile->params->joins->table2;
							for($i=0;$i<count($table2);$i++){
								$deleteQry = 'DELETE FROM '.$db->quoteName($table2[$i]). ' WHERE %s';
								foreach($join_dids as $jdid){
									 if(is_array($jdid)){
										$where = array();
										foreach($jdid as $pkey=>$pval){
											$where[] = $db->quoteName($pkey).' <> '.$db->quote($pval);
										}
										$whereCon[] = ' ('.implode(' AND ', $where).')';
									}
									else{ 
										$whereCon[] = $db->quoteName($this->profile->params->joins->column2[$i]).' <> '.$db->quote($jdid);
									}
								}
								if(!empty($whereCon)){
									$db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
									$res = $db->execute();
								}
							}
						}
					}
					$session->clear('dids');
					$session->clear('join_dids');
					$obj->totalRecordInFile = $totalRecordInFile;
					$session->clear('totalRecordInFile');
					//send notification if done from site part
					if(JFactory::getApplication()->isSite()){
						$notify = $this->sendNotificationEmail( $this->profile->iotype,0 , array('schedule_id'=>"",'schedule_title'=>"","schedule_qry"=>"",'profile_id'=>$this->profile->id,'profile_title'=>$this->profile->title,"count"=>($session->get('qty',0)+$n), "format"=>"xml") );
					}
					
					$obj->result = 'error';
					$obj->offset = ftell($fp);
					$obj->size = $size;
					$obj->error = JText::_('NO_RECORD_TO_IMPORT');
					$obj->qty = $session->get('qty',0) + $n;
					return $obj;
				}
			}
			$session->set('totalRecordInFile',$totalRecordInFile);
			fclose($fp);
			$session->set('dids', $dids);
			$session->set('join_dids', $join_dids);
			$obj->result = 'success';
			$obj->offset = $offset;
			$obj->size = $size;
			$qty = $session->get('qty',0) + $n;
			$session->set('qty',$qty);
			return $obj;
		}
		
		function getDefinedXmlValue($default, $params, $row, $fields){
			
			$defined = $default;
			$hd_local_pattern = '/@vdLocal:[\w]*[.?\w]*/';
			$hd_php_pattern = '/@vdPhp:(.*?)$/';
			$hd_sql_pattern = '/@vdSql:(.*?)$/';
			$hd_remote_pattern = '/@vdRemote:[\w]*[.?\w]*/';
			
			//filter based on profile columns
			$hdlocal = preg_match_all($hd_local_pattern, $defined, $local_matches);
			if( ($hdlocal!==FALSE) ){
				foreach($local_matches[0] as $mk=>$match){
					if(!empty($match)){
						$fn = explode(':', $match);
						if( ( $fn[1]!="" ) ){
							$info = explode('.', $fn[1]);
							
							if(count($info)==1){
								if(isset($fields[$info[0]])){
									$xmlData = $this->getXmlFieldData($row, $fields[$info[0]]);
									$defined = preg_replace('/'.$match.'/', $xmlData, $defined);
								}
							}
							elseif(count($info)==2){
									if( !empty($info[0]) && !empty($info[1]) && property_exists($params, $info[0]) && ($params->{$info[0]}->data=='reference') && in_array($info[1], $params->{$info[0]}->reftext) ) {
										if(isset($fields[$info[0]][$info[1]])){
											$xmlData = $this->getXmlFieldData($row, $fields[$info[0]][$info[1]]);
											$defined = preg_replace('/'.$match.'/', $xmlData, $defined);
										}
								}
							}
						}
					}
				}
			}
			
			//filter using data source xml file
			$hdremote = preg_match_all($hd_remote_pattern, $defined, $local_matches);
			if( $hdremote!==FALSE ){
				foreach($local_matches[0] as $mk=>$match){
					if(!empty($match)){
						$fn = explode(':', $match);
						$targetNode = $fn[1];
						if( ( $targetNode!="" ) ){
							
							if(strrpos($targetNode, '.')!== false){
								$nodeAndProp = explode('.', $targetNode);
								$node = $nodeAndProp[0];
								$prop = $nodeAndProp[1];
								if(!empty($prop)){
									if($row->name==$node && $row->hasAttributes){
										while($row->moveToNextAttribute()){//$row->attributes() as $attr=>$value
											if($row->name==$prop){
												$xmlData = $row->getAttribute($row->name);
												$defined = preg_replace('/'.$match.'/', $xmlData, $defined);
												break;
											}
										}
									}
									else{
										foreach($row as $key=>$value){//$row->attributes() as $attr=>$value
											if($key==$node){
												while($row->moveToNextAttribute()){
													if($row->name==$prop){
														$xmlData = $row->getAttribute($row->name);
														$defined = preg_replace('/'.$match.'/', $xmlData, $defined);
														break;
													}
												}
											}
										}
									}
								}
							}
							else{
								foreach($row as $key=>$value){
									if($key==$targetNode){
										$xmlData = ($value->count())?$value:(string)$value;
										$defined = preg_replace('/'.$match.'/', $xmlData, $defined);
										break;
									}	
								}
								
							}
							
						}
					}
				}
			}
			
			// $hdphp = preg_match_all($hd_php_pattern, $defined, $php_matches);
			$hdphp = preg_match($hd_php_pattern, $defined, $php_matches);
			if( ($hdphp!==FALSE) && !empty($php_matches[0])){
				$temp = $php_matches[0];
				$func = explode(':',$temp, 2);
				if(!empty($func[1])){
					$response = @eval("return ".$func[1].";");
					if (error_get_last()){
						//throw Or log error (error_get_last())
					}
					else{
						$defined = $response;
					}
				}
			}
			// $hdsql = preg_match_all($hd_sql_pattern, $defined, $sql_matches);
			$hdsql = preg_match($hd_sql_pattern, $defined, $sql_matches);
			if( ($hdsql!==FALSE) && !empty($sql_matches[0])){
				$temp = $sql_matches[0];
				$func = explode(':',$temp, 2);
				if(!empty($func[1])){
					$query = 'select '.$func[1];
					try{
						$db = $this->getDbc();
						$db->setQuery($query);
						$defined = $db->loadResult();
					}
					catch(Exception $e){
						$err = $e->getMessage();
						//throw Or log error $e->getMessage()
					}
				}
			}
			
			return $defined;
		}
		
		function insertXmlJoinRecords($base_in_id, $params, $data, $fields, $rootTag, $childTags, $operation=1){
			
			$obj = new stdClass();
			$previous_data = array();
			if(!empty($params->table2)){
				
				$abc = 0;
				$done = array();
				for($out=0;$abc<count($params->table2);$out++){
					
					if($params->join[$out]=='left_join' || $params->join[$out]=='join'){
						if(!in_array($out, $done)){
							
							// print_r($params->table2[$out].': ');
							// $abc++;
							$flag = $this->addXmlChildRow($out, $base_in_id, $params, $data, $fields, $previous_data, $rootTag, $childTags, $operation);
							
							/* if($abc==3){
								jexit('stop');
							} */
							
							if($flag->result=='success'){
								$abc++;
								$done[] = $out;
								$previous_data = $flag->previous_data;
							}
							else{
								$obj->result = 'error';
								$obj->error = $flag->error;
								return $obj;
							}
						}
					}
					elseif($params->join[$out]=='right_join'){
						
						if($abc==(count($params->table2)-1)){
							
							// print_r($params->table2[$out].': ');
							// $abc++;
							$flag = $this->addXmlChildRow($out, $base_in_id, $params, $data, $fields, $previous_data, $rootTag, $childTags, $operation);
							if($flag->result=='success'){
								$abc++;
								$done[] = $out;
								$previous_data = $flag->previous_data;
							}
							else{
								$obj->result = 'error';
								$obj->error = $flag->error;
								return $obj;
							}

						}
						continue;
					}
					if($out==count($params->table2)-1)
						$out = -1;
					
				}
			}
			$obj->result = 'success';
			return $obj;
			
		}
		
		function addXmlChildRow($i,$base_in_id, $params, $data, $fields, $previous_data, $rootTag, $childTags, $operation){
			
			$db = $this->getDbc();
			$new_data[$params->column1[$i].':'.$params->column2[$i]] = array();
			$table2 = $params->table2[$i];
			$obj = new stdClass();
			
			//default one to one mapping
			$childCount = 1;
			//print_r($previous_data);
			
			if($childTags[$table2][0]!=$rootTag){
				if(count($childTags[$table2])>1 && $childTags[$table2][1]!=""){
					$targetTag = $childTags[$table2][1];
					$direct = false;
				}
				else{
					$targetTag = $childTags[$table2][0];
					$direct = true;
				}
				$childCount = $this->getXmlChildCount($targetTag, $data, $direct);
				// echo " one to many - ".$childCount." :";
				
			}
			
			//loop to enter multiple reocrds
			for($c=0;$c<$childCount;$c++){
			
				$insert = new stdClass();
				if(isset($params->component->table[$i]) && !empty($params->component->table[$i])){
					$stance = true;
					$instance_table = $params->component->table[$i];
				}
				else{
					$stance = false;
				}
				$isNew = false;
				
				if($i==0){
					$insert->{$params->column2[0]} = $base_in_id;
				}
				/* if(!empty($previous_data)){
					$keys = array_keys($previous_data);
					$column_keys = explode(':', trim($keys[0]));
					if(array_key_exists($c, $previous_data[$keys[0]])){
						$insert->$column_keys[0] = $previous_data[$keys[0]][$c]->$column_keys[1];
					}
				} */
				
				/* if(!empty($previous_data)){
					if($params->join[$i]=='left_join' || $params->join[$i]=='join'){
						$keys = array_keys($previous_data);
						if(array_key_exists($c, $previous_data[$keys[0]])){
							if(property_exists($previous_data[$keys[0]][$c], $params->column1[$i])){
								$insert->{$params->column2[$i]} = $previous_data[$keys[0]][$c]->{$params->column1[$i]};
							}
						}
					} */
					// Modified by meraj 
					if(!empty($previous_data)){
					if($params->join[$i]=='left_join' || $params->join[$i]=='join'){
						$keys = array_keys($previous_data);
						if(array_key_exists($c, $previous_data[$keys[0]])){
							if(isset($previous_data[$keys[0]][$c]) && property_exists($previous_data[$keys[0]][$c], $params->column2[$i])){
								$insert->{$params->column2[$i]} = $previous_data[$keys[0]][$c]->{$params->column2[$i]};
							}else{
								$insert->{$params->column2[$i]} = $base_in_id;
							}
						}
					}
					elseif($params->join[$i]=='right_join'){
						$keys = array_keys($previous_data);
						$column_keys = explode(':', trim($keys[0]));
						if(array_key_exists($c, $previous_data[$keys[0]])){
							$insert->$column_keys[0] = $previous_data[$keys[0]][$c]->$column_keys[1];
						}
					}
				}
				
				foreach($params->columns[$i] as $column=>$options){
					switch($options->data){
						case 'file':
							if($fields[$table2][$column][0]==""){
								break;
							}
							$tmp_childTags = $childTags[$table2];
							$tmp_fieldsColumn = $fields[$table2][$column];
							// if multiple records,handle indexing
							if($tmp_childTags[0]!=$rootTag){
								
								for($j=(count($tmp_childTags)-1);$j>=0;$j--){
									if($j==1 && strrpos($tmp_childTags[$j], '-')===false){
										//multiple record index
										$tmp_childTags[$j] = $tmp_childTags[$j].'-'.$c;
									}
									if(count($tmp_childTags)==1){
										$tmp_field = explode('||', $tmp_fieldsColumn[0]);
										if(strrpos($tmp_field[count($tmp_field)-1], '-') !== false){
											$lastTag = $tmp_field[count($tmp_field)-1];
											$tmpTag = explode('-', $lastTag);
											//multiple record index
											$tmpTag[1] = $c;
											$tmpTag = implode('-', $tmpTag);
											$tmp_field[count($tmp_field)-1] = $tmpTag;
										}
										$tmp_fieldsColumn[0] = implode('||', $tmp_field);
									}
									array_unshift($tmp_fieldsColumn, $tmp_childTags[$j]);
								}
								
								/* if($table2=='#__contentitem_tag_map'){
									print_r($tmp_fieldsColumn);
								} */
								/* if($table2=='#__tags'){
									print_r($tmp_fieldsColumn);
								} */
							}
							$xmlData = $this->getXmlFieldData($data, $tmp_fieldsColumn);
							$insert->{$column} = $this->getFilteredValue($options, $xmlData);
							if(isset($options->format) && ($options->format=='email') && ($insert->{$column}==FALSE) ){
								JLog::add(JText::sprintf('VDATA_EMAIL_VALIDATION_FAILED', $column), JLog::ERROR, 'com_vdata');
								unset($insert->{$column});
							}
						break;
						case 'defined':
							$defined = $options->default;
							$defined = $this->getDefinedXmlValue($options->default, $params->columns[$i], $data, $fields);
							$insert->{$column} = $defined;
						break;
						case 'reference':
							$insert_ref = new stdClass();
							$menuException = false;
							foreach($options->reftext as $k=>$ref){
								if($fields[$table2][$column][$ref][0]!=""){
									$tmp_childTags = $childTags[$table2];
									$tmp_fieldsColumn = $fields[$table2][$column][$ref];
									// if multiple records,handle indexing
									if($tmp_childTags[0]!=$rootTag) {
										for($j=(count($tmp_childTags)-1);$j>=0;$j--){
											if($j==1 && strrpos($tmp_childTags[$j], '-')===false){
												//multiple record index
												$tmp_childTags[$j] = $tmp_childTags[$j].'-'.$c;
											}
											if(count($tmp_childTags)==1){
												$tmp_field = explode('||', $tmp_fieldsColumn[0]);
												if(strrpos($tmp_field[count($tmp_field)-1], '-') !== false){
													$lastTag = $tmp_field[count($tmp_field)-1];
													$tmpTag = explode('-', $lastTag);
													//multiple record index
													$tmpTag[1] = $c;
													$tmpTag = implode('-', $tmpTag);
													$tmp_field[count($tmp_field)-1] = $tmpTag;
												}
												$tmp_fieldsColumn[0] = implode('||', $tmp_field);
											}	
											array_unshift($tmp_fieldsColumn, $tmp_childTags[$j]);
										}	
									}
									$xmlData = $this->getXmlFieldData($data, $tmp_fieldsColumn);
									//
									if($table2=='#__modules_menu' && preg_match('/^(\(-\)).*$/', $xmlData, $match)){
										$xmlData = substr($match[0],3);
										$menuException = true;
									}
									
									$insert_ref->{$ref} = $xmlData;
								}
							}
							
							$insert_ref_val = (array)$insert_ref;
							if(!empty($insert_ref_val)){
								$ref_value = $this->getReferenceVal($options->table, $options->on, $insert_ref, $db);
								if(!empty($ref_value)){
									//
									if($table2=='#__modules_menu' && $menuException){
										$insert->{$column} = -$ref_value;
									}
									else{
										$insert->{$column} = $ref_value;
									}
									// $insert->{$column} = $ref_value;
								}
								else{
									JLog::add(JText::sprintf('LOCAL_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
								}
							}
							else{
								JLog::add(JText::sprintf('REMOTE_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
							}
						break;
						case 'asset_reference':
							$stance = true;
							$insert->{$column} = 0;
							$intance_column = $options->on;
							if($fields[$table2][$column]['rules'][0]!=""){
								$tmp_childTags = $childTags[$table2];
								$tmp_fieldsColumn = $fields[$table2][$column]['rules'];
								// if multiple records,handle indexing
								if($tmp_childTags[0]!=$rootTag){
									for($j=(count($tmp_childTags)-1);$j>=0;$j--){
										if($j==1 && strrpos($tmp_childTags[$j], '-')===false){
											//multiple record index
											$tmp_childTags[$j] = $tmp_childTags[$j].'-'.$c;
										}
										if(count($tmp_childTags)==1){
											$tmp_field = explode('||', $tmp_fieldsColumn[0]);
											if(strrpos($tmp_field[count($tmp_field)-1], '-') !== false){
												$lastTag = $tmp_field[count($tmp_field)-1];
												$tmpTag = explode('-', $lastTag);
												//multiple record index
												$tmpTag[1] = $c;
												$tmpTag = implode('-', $tmpTag);
												$tmp_field[count($tmp_field)-1] = $tmpTag;
											}
											$tmp_fieldsColumn[0] = implode('||', $tmp_field);
										}
										array_unshift($tmp_fieldsColumn, $tmp_childTags[$j]);
									}
								}
								$xmlData = $this->getXmlFieldData($data, $tmp_fieldsColumn);
								$insert->rules = @json_decode($xmlData, true);
							}
						break;
					}
				}
				
				$primary = $this->getPrimaryKey($table2, $db);
				if(!empty($primary) && isset($insert->{$primary})){
					$query = 'SELECT COUNT(*) FROM '.$db->quoteName($table2).' WHERE '.$db->quoteName($primary).'='.$db->quote($insert->{$primary});
					$db->setQuery($query);
					$result = $db->loadResult();
					$isNew = ($result>0)?false:true;
				}else{
					$query = 'SELECT COUNT(*) FROM '.$db->quoteName($table2).' WHERE %s';
					$whereCon = array();
					foreach($insert as $key=>$val){
						$whereCon[] = $db->quoteName($key).' = '.$db->quote($val);
					}
					$db->setQuery(sprintf($query, implode(' AND ', $whereCon)));
					$result = $db->loadResult();
					$isNew = ($result>0)?false:true;
				}
				
				if($stance){
					$instance_table = isset($instance_table)?$instance_table:$intance_column;
					$tableInstance = JTable::getInstance($instance_table);
					
					//update case
					if( ($operation==0) && !$isNew ){
						$tableInstance->load($insert->{$primary});
					}
					if(property_exists($tableInstance, 'parent_id')){
						$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
						if (!$tableInstance->isLeaf())
							@$tableInstance->setLocation($parent_id, 'last-child');
					}
					if (!$tableInstance->bind((array)$insert)) {
						$obj->result = 'error';
						$obj->error = $tableInstance->getError();
						return $obj;
					}
					if (!$tableInstance->check()) {
						$obj->result = 'error';
						$obj->error = $tableInstance->getError();
						return $obj;
					}
					if (!$tableInstance->store()){
						$obj->result = 'error';
						$obj->error = $tableInstance->getError();
						return $obj;
					}
					
					if(property_exists($tableInstance, $primary)){
						$insert->{$primary} = $tableInstance->{$primary};
					}
					
				}
				else{
					
					if(isset($params->component->table[$i]) && !empty($params->component->table[$i])){
						$component = JText::_($params->component->value[$i]);
						$component_name = $component;
						if (strpos(strtolower($component), "com_") ===FALSE){
							$component_name = "com_".strtolower($component_name); 
						}
						if (strpos(strtolower($component), "com_") ===0){
							$component = str_replace("com_", '',strtolower($component)); 
						}
						JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
						$core_tables = array('category', 'content', 'menu', 'module');
						if(in_array($params->component->table[$i], $core_tables)){
							$tableInstance = JTable::getInstance($params->component->table[$i]);
						}
						else{
							$tableInstance = JTable::getInstance($params->component->table[$i], $component.'Table');
						}
						
						//update case
						if( ($operation==0) && !$isNew ){
							$tableInstance->load($insert->{$primary});
						}
						if(property_exists($tableInstance, 'parent_id')){
							$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
							if (!$tableInstance->isLeaf())
								@$tableInstance->setLocation($parent_id, 'last-child');
						}
						if (!$tableInstance->bind((array)$insert)) {
							$obj->result = 'error';
							$obj->error = $tableInstance->getError();
							return $obj;
						}
						if (!$tableInstance->check()) {
							$obj->result = 'error';
							$obj->error = $tableInstance->getError();
							return $obj;
						}
						if (!$tableInstance->store()){
							$obj->result = 'error';
							$obj->error = $tableInstance->getError();
							return $obj;
						}
						if(property_exists($tableInstance, $primary)){
							$insert->{$primary} = $tableInstance->{$primary};
						}
						
					}
					else{
						if(!$isNew){
							if(!empty($primary)){
								
								if($db->updateObject($table2, $insert, is_array($primary)?$primary[0]:$primary)){
									
								}
								else{
									JLog::add(JText::sprintf('UPDATE_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $db->stderr();
									return $obj;
								}
							}
						}else{
							if($db->insertObject($table2,$insert)){
								if(!empty($primary)){
									$insert->$primary = $db->insertid();
								}
							}
							else{
								JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $db->stderr();
								return $obj;
							}
						}
					}
					
				}
						/* if(!empty($primary)){
							$insert->$primary = 3;
						} */
				$new_data[$params->column1[$i].':'.$params->column2[$i]][] = $insert;
				
				// print_r($insert);
			}
			if($operation==2){
				try{
					$where = array();
					$delQuery = 'DELETE FROM '. $table2 .' WHERE '.$params->column2[0] .' = '. $base_in_id .' AND ';
					//delete from xxxx_k2_comments where itemID=1 and (user_id,username) NOT IN ((0,'test user1'),(135,'Super User'));
					$syncData = array_values($new_data)[0];
					foreach($syncData as $obj){
						$temp ='(';
						$temp .=implode(',',array_diff(array_values(get_object_vars($obj)), [$base_in_id])).')';
						$where[] = $temp;
					}
					$delQuery .= '('.implode(',',array_diff(array_keys(get_object_vars($obj)), [$params->column2[0]])).') NOT IN ('.implode(',',$where).')';
					$db->setQuery($delQuery);
					$db->execute();
				}
				catch (Exception $e){
					$obj->result = 'error';
					$obj->error = $e->getMessage();
					return $obj;
				}
				
			}
			//loop to enter multiple reocrds
			
			$obj->result = 'success';
			$obj->previous_data = $new_data;
			return $obj;
			
		}
		
		function getXmlChildCount($root, $data, $direct) {
			
			$count = 1;
			$rootPath = explode('||', $root);
			if($direct){
				$lastNode = $rootPath[count($rootPath)-1];
				if(strrpos($lastNode, '-')!==false){
					$tmpNode = explode('-', $lastNode);
					$lastNode = $tmpNode[0];
				}
				$rootPath[count($rootPath)-1] = $lastNode;
			}
			else{
				$lastNode = array_pop($rootPath);
			}
			
			$targetNode = $rootPath[count($rootPath)-1];
			foreach($rootPath as $t=>$node){
				
				if(property_exists($data, $targetNode)){
					$count = count($data->{$targetNode}->children());
					// $count = $data->{$targetNode}->count();
					break;
				}
				if(property_exists($data, $node)){
					
					$data = $data->{$node};
				}
			}
			
			return $count;
			
		}
		
		function batch_import_json_quick()
		{
			$obj = new stdClass();
			$db = JFactory::getDbo();
			$session = JFactory::getSession();
			$path = $session->get('file_path');
			if(!is_file($path)){
				$obj->result = 'error';
				$obj->error = JText::_('PLZ_SELECT_FILE');
				return $obj;
			}
			$file_size = filesize($path);
			if($file_size == 0){
				$obj->result = 'error';
				$obj->error = JText::_('PLZ_UPLOAD_VALID_CSV_FILE');
				return $obj;
			}
			$quick = JFactory::getApplication()->input->getInt('quick',0);
			$quick_fields = JFactory::getApplication()->input->get('jsonfield', array(), 'ARRAY');
			
			$offset = JFactory::getApplication()->input->getInt('offset');
			//get csv configuration
			$config = $this->getConfig();
			
			$dids = $session->get('dids', array());
			
			$query = 'SHOW KEYS FROM '.$db->quoteName($this->profile->params->table).' WHERE Key_name = "PRIMARY"';
			$db->setQuery( $query );
			// $key = $db->loadObjectList();
			$key = $db->loadAssocList('Column_name');
			if(property_exists($this->profile->params, 'unqkey') && !empty($this->profile->params->unqkey))
				$primary = $this->profile->params->unqkey;
			elseif(!empty($key)){//&& (count($key)==1)
				// $primary = $key[0]->Column_name;
				$primary = array_keys($key);
			}
			else
				$primary = null;
			
			$query = 'show fields FROM '.$db->quoteName($this->profile->params->table);
			$db->setQuery( $query );
			$cols = $db->loadObjectList();
			
			$data = $this->getJsonData($path);
			if(!$data->result){
				$obj->result = 'error';
				$obj->error = $data->error;
				return $obj;
			}
			$jsonfields = $data->data;
			$size = count($jsonfields);
			$totalRecordInFile = $session->get('totalRecordInFile',0);
			if($quick)
			{
				$n = 0;
				for($i=$offset; $i<($config->limit+$offset); $i++)
				{
					if(isset($jsonfields[$i]) && !empty($jsonfields[$i]))
					{	$totalRecordInFile++;
						$insert = new stdClass();
						if($this->profile->params->operation==3){
							//apply primary key and custom filters
							$where = array();
							$base_in_id = array();
							if(isset($this->profile->params->unqkey)){
								foreach($this->profile->params->unqkey as  $key){
									if(isset($quick_fields[$key]) && ($quick_fields[$key][0]!="")){
										$jsonVal = $this->getJsonFieldValue($jsonfields[$i], $quick_fields[$key][count($quick_fields[$key])-1]);
										$where[] = "(".$db->quoteName($key).' = '.$db->quote($jsonVal).")";
										$base_in_id[$key] = $jsonVal;
									}
								}
							}
							if(!empty($where)){
								$statement = 'SELECT count(*) FROM ' . $db->quoteName($this->profile->params->table) . ' WHERE %s';
								$query = $db->getQuery(true);
								$db->setQuery(sprintf($statement, implode(' AND ', $where)));
								$result = $db->loadResult();
								if($result>0 && !empty($base_in_id)){
									$dids[] = $base_in_id;
								}
							}
						}
						else{
							foreach($cols as $k=>$col){
								if(isset($jsonfields[$i][$col->Field])){
									$insert->{$col->Field} = $jsonfields[$i][$col->Field];
								}
							}
						}
						//if primary key exists check existing record
						$isNew = true;
						$oldData = array();
						$where = array();
						if( !empty($primary)){// && !empty($insert->{$primary})
							foreach($primary as $keyCol){
								if(isset($insert->{$keyCol})){
								$where[] = $db->quoteName($keyCol).'='.$db->quote($insert->{$keyCol});
								}
							}
							if(!empty($where)){
								$query = 'SELECT * FROM '.$db->quoteName($this->profile->params->table).' WHERE '.implode(' AND ', $where);
								$db->setQuery( $query );
								$oldData = $db->loadAssoc();
								$isNew = empty($oldData) ? true : false;
							}
						}
						
						if($this->profile->params->operation==3){
							//delete data operation
						}
						elseif($this->profile->params->operation==1){
							if(!$isNew){
								// JLog::add(JText::sprintf('RECORD_EXISTS',$primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
								JLog::add(JText::sprintf('RECORD_ALREADY_EXISTS',implode(',',$where)), JLog::ERROR, 'com_vdata');
								continue;
							}
							//capture events before insert
							if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
								$response = $this->captureEventsOnRecord( $insert, 'before', $isNew , array('pk'=>$primary ,'old'=>$oldData));
								if(in_array(false, $response)){
									JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
									continue;
								}
							}
							if(!$db->insertObject($this->profile->params->table, $insert)){
								JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $db->stderr();
								return $obj;
							}
							if($this->getAffected($db)> 0){
								$n++;
								//capture events after insert
								if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
									//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
									$response = $this->captureEventsOnRecord( $insert, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
								}
							}
						}
						else{
							if(empty($primary)){// || empty($insert->{$primary})
								$obj->result = 'error';
								$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
								return $obj;
							}
							else{
								foreach($primary as $pkey){
									if(!isset($insert->{$pkey}) || empty($insert->{$pkey})){
										$obj->result = 'error';
										$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
										return $obj;
									}
								}
							}
							
							//capture events before update
							if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
								$response = $this->captureEventsOnRecord( $insert, 'before',$isNew , array('pk'=>$primary ,'old'=>$oldData));
								if(in_array(false, $response)){
									JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
									continue;
								}
							}
							if($isNew){
								// JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
								JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
								if(!$db->insertObject($this->profile->params->table, $insert)){
									JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $db->stderr();
									return $obj;
								}
							}
							else{
								if(!$db->updateObject($this->profile->params->table, $insert, $primary)){
									JLog::add(JText::sprintf('UPDATE_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $db->stderr();
									return $obj;
								}
							}
							
							$base_in_id = array();
							foreach($primary as $pkey){
								if(isset($insert->{$pkey})){
									$base_in_id[$pkey] = $insert->{$pkey};
								}
							}
							$dids[] = $base_in_id;
							// $dids[] = $insert->{$primary};
							
							if($this->getAffected($db)> 0){
								$n++;
								//capture events after update
								if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
									//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
									$response = $this->captureEventsOnRecord( $insert, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
								}
							}
						}
					}
					else
					{
						//log info
						JLog::add(JText::sprintf('IMPORT_PROFILE', $this->profile->title), JLog::INFO, 'com_vdata');
						JLog::add(JText::sprintf('IMPORT_TABLE', $this->profile->params->table), JLog::INFO, 'com_vdata');
						JLog::add(JText::sprintf('IMPORT_FORMAT', 'JSON'), JLog::INFO, 'com_vdata');
						JLog::add(JText::sprintf('IMPORT_RECORD_COUNT', $session->get('qty',0)+$n), JLog::INFO, 'com_vdata');
						
						if( ($this->profile->params->operation==2 || $this->profile->params->operation==3) ){
							$deleteQry = 'DELETE FROM '.$db->quoteName($this->profile->params->table). ' WHERE %s';
							$whereCon = array();
							foreach($dids as $did){
								if(is_array($did)){
									$where = array();
									foreach($did as $pkey=>$pval){
										$where[] = $db->quoteName($pkey).' <> '.$db->quote($pval);
									}
									$whereCon[] = ' ('.implode(' AND ', $where).')';
								}
								else{
									$whereCon[] = ' '.$db->quoteName($primary).' <> '.$db->quote($did);
								}
							}
							//apply delete filter for delete operation
							if($this->profile->params->operation==3){
								//apply filters
								if(isset($this->profile->params->filters->column)){
									$filterValues = $this->importDeleteFilter($db, $this->profile->params);
									if(!empty($filterValues)){
										$whereCon[] = $filterValues;
									}
								}
							}
							if(!empty($whereCon)){
								$db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
								$res = $db->execute();
							}
						}
						$session->clear('dids');
						//send notification if done from site part
						if(JFactory::getApplication()->isSite()){
							$notify = $this->sendNotificationEmail( $this->profile->iotype,0 , array('schedule_id'=>"",'schedule_title'=>"","schedule_qry"=>"",'profile_id'=>$this->profile->id,'profile_title'=>$this->profile->title,"count"=>($session->get('qty',0)+$n), "format"=>"json") );
						}
						$obj->result='error';
						$obj->offset = $offset + $config->limit;
						$obj->size = $size;
						$obj->error = JText::_('NO_RECORD_TO_IMPORT');
						$obj->qty = $session->get('qty',0) + $n;
						return $obj;
					}
				}
			}
			else
			{
				if(!count($quick_fields)){
					$obj->result = 'error';
					$obj->error = JText::_('SELECT_FIELDS_TO_IMPORT');
					return $obj;
				}
				$n = 0;
				for($i=$offset; $i<($config->limit+$offset); $i++)
				{
					if(isset($jsonfields[$i]) && !empty($jsonfields[$i]))
					{	$totalRecordInFile++;
						$insert = new stdClass();
						if($this->profile->params->operation==3){
							//apply primary key and custom filters
							$where = array();
							$base_in_id = array();
							if(isset($this->profile->params->unqkey)){
								foreach($this->profile->params->unqkey as  $key){
									if(isset($quick_fields[$key]) && ($quick_fields[$key][0]!="")){
										$jsonVal = $this->getJsonFieldValue($jsonfields[$i], $quick_fields[$key][count($quick_fields[$key])-1]);
										$where[] = "(".$db->quoteName($key).' = '.$db->quote($jsonVal).")";
										$base_in_id[$key] = $jsonVal;
									}
								}
							}
							if(!empty($where)){
								$statement = 'SELECT count(*) FROM ' . $db->quoteName($this->profile->params->table) . ' WHERE %s';
								$query = $db->getQuery(true);
								$db->setQuery(sprintf($statement, implode(' AND ', $where)));
								$result = $db->loadResult();
								if($result>0 && !empty($base_in_id)){
									$dids[] = $base_in_id;
								}
							}
						}
						else{
							foreach($cols as $k=>$col){
								if(isset($quick_fields[$col->Field]) && $quick_fields[$col->Field][0]!="" ){
									$insert->{$col->Field} = $this->getJsonFieldValue($jsonfields[$i], $quick_fields[$col->Field][count($quick_fields[$col->Field])-1]);
								}
							}
						}
						
						$isNew = true;
						$oldData = array();
						$where = array();
						if(!empty($primary)){// && property_exists($insert, $primary)
							foreach($primary as $keyCol){
								if(isset($insert->{$keyCol})){
								$where[] = $db->quoteName($keyCol).'='.$db->quote($insert->{$keyCol});
								}
							}
							if(!empty($where)){
								$query = 'SELECT * FROM '.$db->quoteName($this->profile->params->table).' WHERE '.implode(' AND ', $where);
								$db->setQuery( $query );
								$oldData = $db->loadAssoc();
								$isNew = empty($oldData) ? true : false;
							}
						}
						
						if($this->profile->params->operation==3){
							//delete data operation
						}
						elseif($this->profile->params->operation==1){
							if(!$isNew){
								// JLog::add(JText::sprintf('RECORD_EXISTS',$primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
								JLog::add(JText::sprintf('RECORD_ALREADY_EXISTS',implode(',',$where)), JLog::ERROR, 'com_vdata');
								continue;
							}
							//capture events before insert
							if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
								$response = $this->captureEventsOnRecord( $insert, 'before',$isNew, array('pk'=>$primary ,'old'=>$oldData) );
								if(in_array(false, $response)){
									JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
									continue;
								}
							}
							if(!$db->insertObject($this->profile->params->table, $insert)){
								JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $db->stderr();
								return $obj;
							}
							if($this->getAffected($db)> 0){
								$n++;
								//capture events after insert
								if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
									//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
									$response = $this->captureEventsOnRecord( $insert, 'after',$isNew, array('pk'=>$primary ,'success'=>true) );
								}
							}
								
						}
						else{
							if(empty($primary)){// || empty($insert->{$primary})
								$obj->result = 'error';
								$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
								return $obj;
							}
							else{
								foreach($primary as $pkey){
									if(!isset($insert->{$pkey}) || empty($insert->{$pkey})){
										$obj->result = 'error';
										$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
										return $obj;
									}
								}
							}
							//capture events before update
							if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
								$response = $this->captureEventsOnRecord( $insert, 'before',$isNew, array('pk'=>$primary ,'old'=>$oldData));
								if(in_array(false, $response)){
									JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
									continue;
								}
							}
							if($isNew){
								// JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
								JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
								if(!$db->insertObject($this->profile->params->table, $insert)){
									JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $db->stderr();
									return $obj;
								}
							}
							else{
								if(!$db->updateObject($this->profile->params->table, $insert, $primary)){
									JLog::add(JText::sprintf('UPDATE_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $db->stderr();
									return $obj;
								}
							}
							$base_in_id = array();
							foreach($primary as $pkey){
								if(isset($insert->{$pkey})){
									$base_in_id[$pkey] = $insert->{$pkey};
								}
							}
							$dids[] = $base_in_id;
							// $dids[] = $insert->{$primary};
							
							if($this->getAffected($db)> 0){
								$n++;
								//capture events after update
								if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
									//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
									$response = $this->captureEventsOnRecord( $insert, 'after', $isNew, array('pk'=>$primary ,'success'=>true) );
								}
							}
						}
					}
					else{
						//log info
						JLog::add(JText::sprintf('IMPORT_PROFILE', $this->profile->title), JLog::INFO, 'com_vdata');
						JLog::add(JText::sprintf('IMPORT_TABLE', $this->profile->params->table), JLog::INFO, 'com_vdata');
						JLog::add(JText::sprintf('IMPORT_FORMAT', 'JSON'), JLog::INFO, 'com_vdata');
						JLog::add(JText::sprintf('IMPORT_RECORD_COUNT', $session->get('qty',0)+$n), JLog::INFO, 'com_vdata');
						
						if( ($this->profile->params->operation==2) || ($this->profile->params->operation==3) ){
							$deleteQry = 'DELETE FROM '.$db->quoteName($this->profile->params->table). ' WHERE %s';
							$whereCon = array();
							foreach($dids as $did){
								if(is_array($did)){
									$where = array();
									foreach($did as $pkey=>$pval){
										$where[] = $db->quoteName($pkey).' <> '.$db->quote($pval);
									}
									$whereCon[] = ' ('.implode(' AND ', $where).')';
								}
								else{
									$whereCon[] = ' '.$db->quoteName($primary).' <> '.$db->quote($did);
								}
							}
							//apply delete filter for delete operation
							if($this->profile->params->operation==3){
								//apply filters
								if(isset($this->profile->params->filters->column)){
									$filterValues = $this->importDeleteFilter($db, $this->profile->params);
									if(!empty($filterValues)){
										$whereCon[] = $filterValues;
									}
								}
							}
							if(!empty($whereCon)){
								$db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
								$res = $db->execute();
							}
						}
						$session->clear('dids');
						$obj->totalRecordInFile = $totalRecordInFile;
						$session->clear('totalRecordInFile');
						//send notification if done from site part
						if(JFactory::getApplication()->isSite()){
							$notify = $this->sendNotificationEmail( $this->profile->iotype,0 , array('schedule_id'=>"",'schedule_title'=>"","schedule_qry"=>"",'profile_id'=>$this->profile->id,'profile_title'=>$this->profile->title,"count"=>($session->get('qty',0)+$n), "format"=>"json") );
						}
						$obj->result='error';
						$obj->offset = $offset + $config->limit;
						$obj->size = $size;
						$obj->error = JText::_('NO_RECORD_TO_IMPORT');
						$obj->qty = $session->get('qty',0) + $n;
						return $obj;
					}
				}
			}
			
			$session->set('dids', $dids);
			$session->set('totalRecordInFile', $totalRecordInFile);
			
			$obj->result='success';
			$obj->size = $size;
			$obj->offset = $offset + $config->limit;
			$qty = $session->get('qty',0) + $n;
			$session->set('qty',$qty);
			return $obj;
		}
		
		function getJsonFieldValue($json, $field){
			
			$value = '';
			
			$fieldPath = explode(':', $field);
			$pathDepth = count($fieldPath);
			if( ($pathDepth==1) && (strrpos($field, '/')!== false) ){
				$fieldPath = explode('/', $field);
				$pathDepth = count($fieldPath);
			}
			
			
			foreach($fieldPath as $index=>$token){
				if($token==""){
					return $value;
				}
				
				$value = $this->getJsonVal($json, $token);
				
				if($index==($pathDepth-1)){
					return $value;
				}
				
				if($value!=false){
					if(array_keys($value)===range(0,count($value)-1)){
						$json = $value[0];
					}
					else{
						$json = $value;
					}
				}
				else{
					break;
				}
			}
			return $value;
		}
		
		/*
		 *
		 */
		function batch_import_json()
		{
			$obj = new stdClass();
			$db = $this->getDbc();
			$session = JFactory::getSession();
			$path = $session->get('file_path');
			
			if(!is_file($path))	{
				$obj->result = 'error';
				$obj->error = JText::_('PLZ_SELECT_FILE');
				return $obj;
			}
			$file_size = filesize($path);
			if($file_size == 0)	{
				$obj->result = 'error';
				$obj->error = JText::_('PLZ_UPLOAD_VALID_JSON_FILE');
				return $obj;
			}
			
			$jsonfields = JFactory::getApplication()->input->get('jsonfield', array(), 'ARRAY');
			$base = JFactory::getApplication()->input->get('base', array(), 'RAW');
			$root = JFactory::getApplication()->input->get('root', array(), 'RAW');
			$images_import = JFactory::getApplication()->input->get('images', array(), 'RAW');
				
			if(count($jsonfields) == 0)	{
				$obj->result = 'error';
				$obj->error = JText::_('PLZ_UPLOAD_VALID_JSON_FILE');
				return $obj;
			}

			$offset = JFactory::getApplication()->input->getInt('offset');
			$config = $this->getConfig();
			
			$dids = $session->get('dids', array());
			$join_dids = $session->get('join_dids', array());
			
			$query = 'SHOW KEYS FROM '.$db->quoteName($this->profile->params->table).' WHERE Key_name = "PRIMARY"';
			$db->setQuery( $query );
			// $key = $db->loadObjectList();
			$key = $db->loadAssocList('Column_name');
			if(property_exists($this->profile->params, 'unqkey') && !empty($this->profile->params->unqkey))
				$primary = $this->profile->params->unqkey;
			elseif(!empty($key)){//&& (count($key)==1)
				// $primary = $key[0]->Column_name;
				$primary = array_keys($key);
			}
			else
				$primary = null;
			
			$data = $this->getJsonData($path);
			if(!$data->result){
				$obj->result = 'error';
				$obj->error = $data->error;
				return $obj;
			}
			$json = $data->data;
			$size = count($json);
			$totalRecordInFile = $session->get('totalRecordInFile',0);
			$n = 0;
			for($i=$offset; $i<($config->limit+$offset); $i++)
			{
				if(isset($json[$i]) && !empty($json[$i]))
				{	$totalRecordInFile++;
					$isNew = true;
					$insert = new stdClass();
					$stance = false;
					$cached_data = new stdClass();
					$oldData = array();
					if($this->profile->params->operation==3){
						//apply primary key and custom filters
						$where = array();
						$base_in_id = array();
						if(isset($this->profile->params->unqkey)){
							foreach($this->profile->params->unqkey as  $key){
								if(!empty($jsonfields[$key]) && ($jsonfields[$key][0]!="")){
									$fieldVal = $this->getJsonFieldValue($json[$i], $jsonfields[$key][count($jsonfields[$key])-1]);
									$where[] = "(".$db->quoteName($key).' = '.$db->quote($fieldVal).")";
									$base_in_id[$key] = $fieldVal;
								}
							}
						}
						if(!empty($where)){
							$statement = 'SELECT count(*) FROM ' . $db->quoteName($this->profile->params->table) . ' WHERE %s';
							$query = $db->getQuery(true);
							$db->setQuery(sprintf($statement, implode(' AND ', $where)));
							$result = $db->loadResult();
							if($result>0 && !empty($base_in_id)){
								$dids[] = $base_in_id;
							}
						}
					}
					else{
						foreach($this->profile->params->fields as $column=>$field){
							switch($field->data) :
								case 'file' : 
									if(empty($jsonfields[$column]) || $jsonfields[$column][0]==""){
										break;
									}
									$fieldVal = $this->getJsonFieldValue($json[$i], $jsonfields[$column][count($jsonfields[$column])-1]);
									$insert->{$column} = $cached_data->{$column} = $this->getFilteredValue($field, $fieldVal);
									if( isset($field->format) && ($field->format=='email') && ($insert->{$column}==false)){
										JLog::add(JText::sprintf('VDATA_EMAIL_VALIDATION_FAILED', $column), JLog::ERROR, 'com_vdata');
										unset($insert->{$column}, $cached_data->{$column});
									}
									if($field->format == "encrypt"){
										$cached_data->{$column} = $fieldVal;
									}
									if($field->format == "image"){
										$filename = $insert->{$column};$destination = rtrim($field->location,'/').'/'. basename($filename);
										$image_source=$path_type='';
										if(isset($images_import['root'][$column])){
											$image_source = isset($images_import['root'][$column]['image_source'])?$images_import['root'][$column]['image_source']:'';
											$path_type = isset($images_import['root'][$column]['path_type'])?$images_import['root'][$column]['path_type']:'';											
										}	
										if(!$image = $this->uploadImage($filename,$destination,$image_source,$path_type,$err)){
													
											$insert->{$column} = $cached_data->{$column} = $destination;
											$obj->result = 'error';
											$obj->error = $err;
											return $obj;
										}//jexit();
									} 
								break;
								case 'defined' :
									$defined = $field->default;
									$defined = $this->getDefinedJsonValue($field->default, $this->profile->params->fields, $json[$i], $jsonfields);
									$insert->{$column} = $cached_data->{$column} = $defined;
								break;
								case 'reference' :
									$insert_ref = new stdClass();
									foreach($field->reftext as $k=>$ref){
										if( array_key_exists($ref, $jsonfields[$column]) && ($jsonfields[$column][$ref]!="") ){
											$insert_ref->{$ref} = $this->getJsonFieldValue($json[$i], $jsonfields[$column][$ref][count($jsonfields[$column][$ref])-1]);
										}
									}
									$insert_ref_val = (array)$insert_ref;
									if(!empty($insert_ref_val)){
										$ref_value = $this->getReferenceVal($field->table, $field->on, $insert_ref, $db);
										if(!empty($ref_value)){
											$insert->{$column} = $ref_value;
											$cached_data->{$column} = $ref_value;
										}
										else{
											JLog::add(JText::sprintf('LOCAL_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
										}
									}
									else{
										JLog::add(JText::sprintf('REMOTE_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
									}
								break;
								case 'asset_reference':
									$stance = true;
									$insert->$column = 0;
									$intance_column = $field->on;
									$instance_component = $field->table;
									if($jsonfields[$column]['rules']!=""){
										$fieldVal = $this->getJsonFieldValue($json[$i], $jsonfields[$column]['rules'][count($jsonfields[$column]['rules'])-1]);
										$insert->rules = json_decode($fieldVal, true);
									}
								break;
							endswitch;
						}
					}
					//check if id already exists
					$where = array();
					if(!empty($primary)) //&& isset($insert->{$primary})
					{
						foreach($primary as $keyCol){
							if(isset($insert->{$keyCol})){
							$where[] = $db->quoteName($keyCol).'='.$db->quote($insert->{$keyCol});
							}
						}
						if(!empty($where)){
							$query = 'SELECT * FROM '.$db->quoteName($this->profile->params->table).' WHERE '.implode(' AND ', $where);
							$db->setQuery( $query );
							$oldData = $db->loadAssoc();
							$isNew = empty($oldData) ? true : false;
						}
					}
					
					// print_r($insert);
					// jexit('insert');
					
					$afterState = false;
					if($this->profile->params->operation==3){
						//delete data operation
					}
					elseif($this->profile->params->operation==1){
						if(!$isNew){
							// JLog::add(JText::sprintf('RECORD_EXISTS',$primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
							JLog::add(JText::sprintf('RECORD_ALREADY_EXISTS',implode(',',$where)), JLog::ERROR, 'com_vdata');
							continue;
						}
						//capture events before insert
						if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
							//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
							$response = $this->captureEventsOnRecord( $insert, 'before',$isNew, array('pk'=>$primary ,'old'=>$oldData) );
							if(in_array(false, $response)){
								JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
								continue;
							}
						}
						$base_join_val = null;
						if($stance){
							$component = JText::_($instance_component);
							$component_name = $component;
							if (strpos(strtolower($component), "com_") ===FALSE){
								$component_name = "com_".strtolower($component_name); 
							}
							if (strpos(strtolower($component), "com_") ===0){
								$component = str_replace("com_", '',strtolower($component)); 
							}
							JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
							$core_tables = array('category', 'content', 'menu', 'module');
							if(in_array($intance_column, $core_tables)){
								$row = JTable::getInstance($intance_column);
							}
							else{
								$row = JTable::getInstance($intance_column, $component.'Table');
							}
							if(property_exists($row, 'parent_id')){
								$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
								@$row->setLocation($parent_id, 'last-child');
							}
							
							if (!$row->bind((array)$insert)) {
								JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $row->getError();
								return $obj;
							}
							if (!$row->check()) {
								JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $row->getError();
								return $obj;
							}
							if (!$row->store()){
								JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $row->getError();
								return $obj;
							}
							if(!empty($primary)){
								$base_in_id = array();
								foreach($primary as $pkey){
									if(property_exists($row, $pkey)){
										$base_in_id[$pkey] = $row->{$pkey};
									}
								}
							}
							/* if(property_exists($row, $primary)){
								$base_in_id = $row->{$primary};
							} */
							$n++;
							$afterState = true;
							if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($row, $this->profile->params->joins->column1[0])){
								$base_join_val = $row->{$this->profile->params->joins->column1[0]};
							}
						}
						else{
							if(isset($this->profile->params->component->table) && !empty($this->profile->params->component->table)){
								$component = JText::_($this->profile->params->component->value);
								$component_name = $component;
								if (strpos(strtolower($component), "com_") ===FALSE){
									$component_name = "com_".strtolower($component_name); 
								}
								if (strpos(strtolower($component), "com_") ===0){
									$component = str_replace("com_", '',strtolower($component)); 
								}
								JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
								$core_tables = array('category', 'content', 'menu', 'module');
								if(in_array($this->profile->params->component->table, $core_tables)){
									$row = JTable::getInstance($this->profile->params->component->table);
								}
								else{
									$row = JTable::getInstance($this->profile->params->component->table, $component.'Table');
								}
								
								// $row->load(null);
								if(property_exists($row, 'parent_id')){
									$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
									@$row->setLocation($parent_id, 'last-child');
								}
								if (!$row->bind((array)$insert)) {
									JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $row->getError();
									return $obj;
								}
								if (!$row->check()) {
									JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $row->getError();
									return $obj;
								}
								if (!$row->store()){
									JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $row->getError();
									return $obj;
								}
								$n++;
								$afterState = true;
								if(!empty($primary)){
									$base_in_id = array();
									foreach($primary as $pkey){
										if(property_exists($row, $pkey)){
											$base_in_id[$pkey] = $row->{$pkey};
										}
									}
								}
								/* if(!empty($primary) && property_exists($row, $primary)){
									$base_in_id = $row->{$primary};
								} */
								if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($row, $this->profile->params->joins->column1[0])){
									$base_join_val = $row->{$this->profile->params->joins->column1[0]};
								}
							}
							else{
								if($db->insertObject($this->profile->params->table, $insert)){	
									if($this->getAffected($db)> 0){
										$n++;
										$afterState = true;
									}
									$base_in_id = array();
									foreach($primary as $pkey){
										if(isset($insert->{$pkey})){
											$base_in_id[$pkey] = $insert->{$pkey};
										}
									}
										
									if((!empty($primary) && count($primary)==1) || (isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && !property_exists($insert, $profile_params->joins->column1[0]))){
										$newId =$db->insertid();
										if($primary[0]!='id'){
											$query = 'SHOW KEYS FROM '.$db->quoteName($this->profile->params->table).' WHERE Key_name = "PRIMARY"';
											$db->setQuery( $query );
											// $key = $db->loadObjectList();
											$key = $db->loadAssocList('Column_name');
											$pri = array_keys($key)[0];
											$cached_data->{$pri} = $insert->{$pri} = $newId;
										}else{
											$cached_data->{$primary[0]} = $insert->{$primary[0]} = $db->insertid();
										}
									}
									if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($insert, $this->profile->params->joins->column1[0])){
										$base_join_val = $insert->{$this->profile->params->joins->column1[0]};
									}
									
								}
								else{
									JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $db->stderr();
									return $obj;
								}
							}
						}
						if($afterState){
							//capture events after insert
							if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
								if(!empty($primary)){
									foreach($primary as $pkey){
										if(property_exists($cached_data, $pkey) && isset($base_in_id[$pkey])){
											$cached_data->{$pkey} = $base_in_id[$pkey];
										}
									}
								}
								/* if(!property_exists($cached_data, $primary)){
									$cached_data->$primary = $base_in_id;
								} */
								$response = $this->captureEventsOnRecord( $cached_data, 'after',$isNew, array('pk'=>$primary ,'success'=>true) );
							}
						}
					}
					else{
						if(empty($primary)){//|| empty($insert->{$primary})
							$obj->result = 'error';
							$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
							return $obj;
						}
						else{
							foreach($primary as $pkey){
								if(!isset($insert->{$pkey}) || empty($insert->{$pkey})){
									$obj->result = 'error';
									$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
									return $obj;
								}
							}
						}
						
						//capture events before update
						if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ){
							//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $this->profile->params->events->before)), JLog::INFO, 'com_vdata');
							$response = $this->captureEventsOnRecord( $insert, 'before',$isNew, array('pk'=>$primary ,'old'=>$oldData));
							if(in_array(false, $response)){
								JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
								continue;
							}
						}
						$base_join_val = null;
						if($stance){
							$component = JText::_($instance_component);
							$component_name = $component;
							if (strpos(strtolower($component), "com_") ===FALSE){
								$component_name = "com_".strtolower($component_name); 
							}
							if (strpos(strtolower($component), "com_") ===0){
								$component = str_replace("com_", '',strtolower($component)); 
							}
							JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
							$core_tables = array('category', 'content', 'menu', 'module');
							if(in_array($intance_column, $core_tables)){
								$row = JTable::getInstance($intance_column);
							}
							else{
								$row = JTable::getInstance($intance_column, $component.'Table');
							}
							
							$loadFields = array();
							foreach($primary as $pkey){
								if(isset($insert->{$pkey}) && !empty($insert->{$pkey})){
									$loadFields[$pkey] = $insert->{$pkey};
								}
							}
							$row->load($loadFields);
							// $row->load($insert->{$primary});
							if(property_exists($row, 'parent_id')){
								if (isset($row->id) && isset($insert->parent_id) && $row->id==1 && $insert->parent_id==1)
									$insert->parent_id = 0; 
								$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
								if ($row->parent_id!=$parent_id)
									@$row->setLocation($parent_id, 'last-child');
							}
							if($isNew){
								// JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
								JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
							}
							if (!$row->bind((array)$insert)) {
								JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $row->getError();
								return $obj;
							}
							if (!$row->check()) {
								JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $row->getError();
								return $obj;
							}
							if (!$row->store()){
								JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $row->getError();
								return $obj;
							}
							$n++;
							$afterState = true;
							if(property_exists($row, $primary)){
								$base_in_id = $row->{$primary};
							}
							if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($row, $this->profile->params->joins->column1[0])){
								$base_join_val = $row->{$this->profile->params->joins->column1[0]};
							}
						}
						else{
							if($isNew){
								// JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
								JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
								if(isset($this->profile->params->component->table) && !empty($this->profile->params->component->table)){
									$component = JText::_($this->profile->params->component->value);
									$component_name = $component;
									if (strpos(strtolower($component), "com_") ===FALSE){
										$component_name = "com_".strtolower($component_name); 
									}
									if (strpos(strtolower($component), "com_") ===0){
										$component = str_replace("com_", '',strtolower($component)); 
									}
									JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
									$core_tables = array('category', 'content', 'menu', 'module');
									if(in_array($this->profile->params->component->table, $core_tables)){
										$row = JTable::getInstance($this->profile->params->component->table);
									}
									else{
										$row = JTable::getInstance($this->profile->params->component->table, $component.'Table');
									}
									// $row->load(null);
									if(property_exists($row, 'parent_id')){
										$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
										@$row->setLocation($parent_id, 'last-child');
									}
									if (!$row->bind((array)$insert)) {
										JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
										$obj->result = 'error';
										$obj->error = $row->getError();
										return $obj;
									}
									if (!$row->check()) {
										JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
										$obj->result = 'error';
										$obj->error = $row->getError();
										return $obj;
									}
									if (!$row->store()){
										JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
										$obj->result = 'error';
										$obj->error = $row->getError();
										return $obj;
									}
									$n++;
									$afterState = true;
									/* if(!empty($primary) && property_exists($row, $primary)){
										$base_in_id = $row->{$primary};
									} */
									if(!empty($primary)){
										$base_in_id = array();
										foreach($primary as $pkey){
											if(property_exists($row, $pkey)){
												$base_in_id[$pkey] = $row->{$pkey};
											}
										}
									}
									if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($row, $this->profile->params->joins->column1[0])){
										$base_join_val = $row->{$this->profile->params->joins->column1[0]};
									}
								}
								else{
									if($db->insertObject($this->profile->params->table, $insert)){
										if($this->getAffected($db)> 0){
											$n++;
											$afterState = true;
										}
										$base_in_id = array();
										foreach($primary as $pkey){
											if(isset($insert->{$pkey})){
												$base_in_id[$pkey] = $insert->{$pkey};
											}
										}
										
										if((!empty($primary) && count($primary)==1) || (isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && !property_exists($insert, $profile_params->joins->column1[0]))){
											$newId = $db->insertid();
											if($primary[0]!='id'){
												$query = 'SHOW KEYS FROM '.$db->quoteName($this->profile->params->table).' WHERE Key_name = "PRIMARY"';
												$db->setQuery( $query );
												// $key = $db->loadObjectList();
												$key = $db->loadAssocList('Column_name');
												$pri = array_keys($key)[0];
												$cached_data->{$pri} = $insert->{$pri} = $newId;
											}else{
												$cached_data->{$primary[0]} = $insert->{$primary[0]} = $db->insertid();
											}
										}
										if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($insert, $this->profile->params->joins->column1[0])){
											$base_join_val = $insert->{$this->profile->params->joins->column1[0]};
										}
									}
									else{
										JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
										$obj->result = 'error';
										$obj->error = $db->stderr();
										return $obj;
									}
								}
							}
							else{
								if(isset($this->profile->params->component->table) && !empty($this->profile->params->component->table)){
									$component = JText::_($this->profile->params->component->value);
									$component_name = $component;
									if (strpos(strtolower($component), "com_") ===FALSE){
										$component_name = "com_".strtolower($component_name); 
									}
									if (strpos(strtolower($component), "com_") ===0){
										$component = str_replace("com_", '',strtolower($component)); 
									}
									JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
									$core_tables = array('category', 'content', 'menu', 'module');
									if(in_array($this->profile->params->component->table, $core_tables)){
										$row = JTable::getInstance($this->profile->params->component->table);
									}
									else{
										$row = JTable::getInstance($this->profile->params->component->table, $component.'Table');
									}
									$loadFields = array();
									foreach($primary as $pkey){
										if(isset($insert->{$pkey}) && !empty($insert->{$pkey})){
											$loadFields[$pkey] = $insert->{$pkey};
										}
									}
									$row->load($loadFields);
									// $row->load($insert->{$primary});
									if(property_exists($row, 'parent_id')){
										if (isset($row->id) && isset($insert->parent_id) && $row->id==1 && $insert->parent_id==1)
											$insert->parent_id = 0; 
										$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
										if ($row->parent_id!=$parent_id)
											@$row->setLocation($parent_id, 'last-child');
									}
									if (!$row->bind((array)$insert)) {
										JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
										$obj->result = 'error';
										$obj->error = $row->getError();
										return $obj;
									}
									if (!$row->check()) {
										JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
										$obj->result = 'error';
										$obj->error = $row->getError();
										return $obj;
									}
									if (!$row->store()){
										JLog::add(JText::sprintf('INSERT_FAIL',$row->getError()), JLog::ERROR, 'com_vdata');
										$obj->result = 'error';
										$obj->error = $row->getError();
										return $obj;
									}
									$n++;
									$afterState = true;
									/* if(!empty($primary) && property_exists($row, $primary)){
										$base_in_id = $row->{$primary};
									} */
									if(!empty($primary)){
										$base_in_id = array();
										foreach($primary as $pkey){
											if(property_exists($row, $pkey)){
												$base_in_id[$pkey] = $row->{$pkey};
											}
										}
									}
									if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 && property_exists($row, $this->profile->params->joins->column1[0])){
										$base_join_val = $row->{$this->profile->params->joins->column1[0]};
									}
								}
								else{
									if($db->updateObject($this->profile->params->table, $insert, $primary)){
										if($this->getAffected($db)> 0){
											$n++;
											$afterState = true;
										}
										// $base_in_id = $insert->{$primary};
										$base_in_id = array();
										foreach($primary as $pkey){
											if(isset($insert->{$pkey})){
												$base_in_id[$pkey] = $insert->{$pkey};
											}
										}
										if(isset($this->profile->params->joins->column1) && count($this->profile->params->joins->column1)>0 ){
											if(property_exists($insert, $this->profile->params->joins->column1[0])){
												$base_join_val = $insert->{$this->profile->params->joins->column1[0]};
											}else{
												//check if record already exists
												$where = array();
												if( !empty($primary)  ){
													foreach($primary as $keyCol){
														if(isset($insert->{$keyCol})){
														$where[] = $db->quoteName($keyCol).'='.$db->quote($insert->{$keyCol});
														}
													}
													if(!empty($where)){
														$query = 'SELECT '.$this->profile->params->joins->column1[0].' FROM '.$db->quoteName($this->profile->params->table).' WHERE '.implode(' AND ', $where);
														$db->setQuery( $query );
														$base_join_val = $cached_data->{$this->profile->params->joins->column1[0]} = $insert->{$this->profile->params->joins->column1[0]} = $db->loadResult();
													}
												}
											}
										}
									}
									else{
										JLog::add(JText::sprintf('UPDATE_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
										$obj->result = 'error';
										$obj->error = $db->stderr();
										return $obj;
									}
								}
							}
						}
						$dids[] = $base_in_id;
						
						if($afterState){
							//capture events after update
							if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
								if(!property_exists($cached_data, $primary)){
									$cached_data->$primary = $base_in_id;
								}
								$response = $this->captureEventsOnRecord( $cached_data, 'after', $isNew, array('pk'=>$primary ,'success'=>true) );
							}
						}
					}
					
					if($this->profile->params->operation!=3){
						$join_dids[] =$base_join_val; 
						$flag = $this->insertJsonJoinRecords($base_join_val, $this->profile->params->joins, $json[$i], $jsonfields, $base, $root, $this->profile->params->operation);
						if($flag->result=='error'){
							$obj->result = 'error';
							$obj->error = $flag->error;
							return $obj;
						}
					}
				}
				else
				{
					//log info
					JLog::add(JText::sprintf('IMPORT_PROFILE', $this->profile->title), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('IMPORT_TABLE', $this->profile->params->table), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('IMPORT_FORMAT', 'JSON'), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('IMPORT_RECORD_COUNT', $session->get('qty',0)+$n), JLog::INFO, 'com_vdata');
					
					if( ($this->profile->params->operation==2) || ($this->profile->params->operation==3) ){
						$deleteQry = 'DELETE FROM '.$db->quoteName($this->profile->params->table). ' WHERE %s';
						$whereCon = array();
						foreach($dids as $did){
							if(is_array($did)){
								$where = array();
								foreach($did as $pkey=>$pval){
									$where[] = $db->quoteName($pkey).' <> '.$db->quote($pval);
								}
								$whereCon[] = ' ('.implode(' AND ', $where).')';
							}
							else{
								$whereCon[] = ' '.$db->quoteName($primary).' <> '.$db->quote($did);
							}
						}
						//apply delete filter for delete operation
						if($this->profile->params->operation==3){
							//apply filters
							if(isset($this->profile->params->filters->column)){
								$filterValues = $this->importDeleteFilter($db, $this->profile->params);
								if(!empty($filterValues)){
									$whereCon[] = $filterValues;
								}
							}
						}
						if(!empty($whereCon)){
							$db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
							$res = $db->execute();
						}
						
						if(isset($this->profile->params->joins)){
							$whereCon = array();
							$table2 = $this->profile->params->joins->table2;
							for($i=0;$i<count($table2);$i++){
								$deleteQry = 'DELETE FROM '.$db->quoteName($table2[$i]). ' WHERE %s';
								foreach($join_dids as $jdid){
									 if(is_array($jdid)){
										$where = array();
										foreach($jdid as $pkey=>$pval){
											$where[] = $db->quoteName($pkey).' <> '.$db->quote($pval);
										}
										$whereCon[] = ' ('.implode(' AND ', $where).')';
									}
									else{ 
										$whereCon[] = $db->quoteName($this->profile->params->joins->column2[$i]).' <> '.$db->quote($jdid);
									}
								}
								if(!empty($whereCon)){
									$db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
									$res = $db->execute();
								}
							}
						}
					}
					$obj->totalRecordInFile = $totalRecordInFile;
					$session->clear('totalRecordInFile');
					$session->clear('dids');
					$session->clear('join_dids');
					//send notification if done from site part
					if(JFactory::getApplication()->isSite()){
						$notify = $this->sendNotificationEmail( $this->profile->iotype,0 , array('schedule_id'=>"",'schedule_title'=>"","schedule_qry"=>"",'profile_id'=>$this->profile->id,'profile_title'=>$this->profile->title,"count"=>($session->get('qty',0)+$n), "format"=>"json") );
					}
					$obj->result='error';
					$obj->offset = $offset + $config->limit;
					$obj->size = $size;
					$obj->error = JText::_('NO_RECORD_TO_IMPORT');
					$obj->qty = $session->get('qty',0) + $n;
					return $obj;
				}
			}
			
			$session->set('dids', $dids);
			$session->set('join_dids', $join_dids);
			$session->set('totalRecordInFile',$totalRecordInFile);
			
			$obj->result='success';
			$obj->size = $size;
			$obj->offset = $offset + $config->limit;
			$qty = $session->get('qty', 0) + $n;
			$session->set('qty',$qty);
			return $obj;
		}
		
		function insertJsonJoinRecords($base_in_id, $params, $data, $fields, $base, $child, $operation=1){
			
			$obj = new stdClass();
			$previous_data = array();
			if(!empty($params->table2)){
				
				$abc = 0;
				$done = array();
				for($out=0;$abc<count($params->table2);$out++){
					
					if($params->join[$out]=='left_join' || $params->join[$out]=='join'){
						if(!in_array($out, $done)){
							
							// print_r($params->table2[$out].': ');
							/* $abc++;
							$done[] = $out; */
							$flag = $this->addJsonChildRow($out, $base_in_id, $params, $data, $fields, $previous_data, $base, $child, $operation);
							if($flag->result=='success'){
								$abc++;
								$done[] = $out;
								$previous_data = $flag->previous_data;
							}
							else{
								$obj->result = 'error';
								$obj->error = $flag->error;
								return $obj;
							}
						}
					}
					elseif($params->join[$out]=='right_join'){
						
						if($abc==(count($params->table2)-1)){
							
							// print_r($params->table2[$out].': ');
							/* $abc++;
							$done[] = $out; */
							$flag = $this->addJsonChildRow($out, $base_in_id, $params, $data, $fields, $previous_data, $base, $child, $operation);
							if($flag->result=='success'){
								$abc++;
								$done[] = $out;
								$previous_data = $flag->previous_data;
							}
							else{
								$obj->result = 'error';
								$obj->error = $flag->error;
								return $obj;
							}
						}
						continue;
					}
					if($out==count($params->table2)-1)
						$out = -1;
					
				}
			}
			$obj->result = 'success';
			return $obj;
			
		}
		
		
		function addJsonChildRow($i, $base_in_id, $params, $data, $fields, $previous_data, $base, $child, $operation){
			
			$db = $this->getDbc();
			$new_data[$params->column1[$i].':'.$params->column2[$i]] = array();
			$table2 = $params->table2[$i];
			$obj = new stdClass();
			
			//default
			$childCount = 0;
			$targetProp = false;
			if(isset($child[$table2])){
				if($child[$table2][count($child[$table2])-1]==''){
					$targetProp = false;
				}
				elseif(count($child[$table2])==1 && $child[$table2][count($child[$table2])-1]=='load_field'){
					foreach($base as $key=>$val){
						if($key=='load_field'){
							break;
						}
						$targetProp = $val;
					}
				}
				else{
					foreach($child[$table2] as $key=>$val){
						if($val=='load_field'){
							break;
						}
						$targetProp = $val;
					}
				}
			}
			
			if(!$targetProp){
				$obj->result = 'error';
				$obj->error = JText::_('SELECT_CHILD_TABLE_PROPERTIES');
				return $obj;
			}
			
			$json = $this->getJsonFieldValue($data, $targetProp);
			$childCount = is_array($json)?count($json):0;
			
			
			for($c=0;$c<$childCount;$c++){
				
				$insert = new stdClass();
				$cached_data =  new stdClass();
				if(isset($params->component->table[$i]) && !empty($params->component->table[$i])){
					$stance = true;
					$instance_table = $params->component->table[$i];
				}
				else{
					$stance = false;
				}
				
				if($i==0){
					$insert->{$params->column2[0]} = $base_in_id;
				}
				$isNew = false;
				
				if(!empty($previous_data)){
					if($params->join[$i]=='left_join' || $params->join[$i]=='join'){
						$keys = array_keys($previous_data);
						if(array_key_exists($c, $previous_data[$keys[0]])){
							if(isset($previous_data[$keys[0]][$k]) && property_exists($previous_data[$keys[0]][$c], $params->column1[$i])){
								$insert->{$params->column2[$i]} = $previous_data[$keys[0]][$c]->{$params->column1[$i]};
							}else{
								$insert->{$params->column2[$i]} = $base_in_id;
							}
						}
						
					}
					elseif($params->join[$i]=='right_join'){
						$keys = array_keys($previous_data);
						$column_keys = explode(':', trim($keys[0]));
						if(array_key_exists($c, $previous_data[$keys[0]])){
							$insert->$column_keys[0] = $previous_data[$keys[0]][$c]->$column_keys[1];
						}
					}
				}
				
				foreach($params->columns[$i] as $column=>$options){
					switch($options->data){
						case 'file':
							if(empty($fields[$table2][$column]) || $fields[$table2][$column][0]==""){
								break;
							}
							$selectedField = explode(':', $fields[$table2][$column][count($fields[$table2][$column])-1]);
							$targetField = $selectedField[count($selectedField)-1];
							 
							$fieldVal = $this->getJsonVal($json[$c], $targetField);
							$insert->{$column} = $cached_data->{$column} = $this->getFilteredValue($options, $fieldVal);
							if( isset($options->format) && ($options->format=='email') && ($insert->{$column}) ){
								JLog::add(JText::sprintf('VDATA_EMAIL_VALIDATION_FAILED', $column), JLog::ERROR, 'com_vdata');
								unset($insert->{$column});
							}
							if($options->format == "encrypt"){
								$cached_data->{$column} = $fieldVal;
							}
						break;
						case 'defined':
							$defined = $options->default;
							$defined = $this->getDefinedJsonValue($options->default, $params->columns[$i], $json[$c], $fields[$table2], 'c');
							$insert->{$column} = $cached_data->{$column} = $defined;
						break;
						case 'reference':
							$insert_ref = new stdClass();
							$menuException = false;
							foreach($options->reftext as $k=>$ref){
								if( array_key_exists($ref, $fields[$table2][$column]) && ($fields[$table2][$column][$ref]!="") ){
									$selectedField = explode(':', $fields[$table2][$column][$ref][count($fields[$table2][$column][$ref])-1]);
									$targetField = $selectedField[count($selectedField)-1];
									$insert_ref->{$ref} = $this->getJsonVal($json[$c], $targetField);
									if($table2=='#__modules_menu' && preg_match('/^(\(-\)).*$/', $insert_ref->{$ref}, $match)){
										$insert_ref->{$ref} = substr($match[0],3);
										$menuException = true;
									}
								}
							}
							$insert_ref_val = (array)$insert_ref;
							if(!empty($insert_ref_val)){
								$ref_value = $this->getReferenceVal($options->table, $options->on, $insert_ref, $db);
								if(!empty($ref_value)){
									if($table2=='#__modules_menu' && $menuException){
										$insert->{$column} = -$ref_value;
										$cached_data->{$column} = $ref_value;
									}
									else{
										$insert->{$column} = $ref_value;
										$cached_data->{$column} = $ref_value;
									}
									// $insert->{$column} = $ref_value;
									
								}
								else{
									JLog::add(JText::sprintf('LOCAL_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
								}
							}
							else{
								JLog::add(JText::sprintf('REMOTE_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
							}
						break;
						case 'asset_reference':
							$stance = true;
							$insert->$column = 0;
							$intance_column = $options->on;
							$instance_component = $options->table;
							if($fields[$table2][$column]['rules']!=""){
								$selectedField = explode(':', $fields[$table2][$column]['rules'][count($fields[$table2][$column]['rules'])-1]);
								$targetField = $selectedField[count($selectedField)-1];
								$fieldVal = $this->getJsonVal($json[$c], $targetField);
								$insert->rules = json_decode($fieldVal, true);
							}
						break;
					}
				}
				
				$primary = $this->getPrimaryKey($table2, $db);
				if(!empty($primary) && isset($insert->{$primary})){
					$query = 'SELECT COUNT(*) FROM '.$db->quoteName($table2).' WHERE '.$db->quoteName($primary).'='.$db->quote($insert->{$primary});
					$db->setQuery($query);
					$result = $db->loadResult();
					$isNew = ($result>0)?false:true;
				}else{
					$query = 'SELECT COUNT(*) FROM '.$db->quoteName($table2).' WHERE %s';
					$whereCon = array();
					foreach($insert as $key=>$val){
						$whereCon[] = $db->quoteName($key).' = '.$db->quote($val);
					}
					$db->setQuery(sprintf($query, implode(' AND ', $whereCon)));
					$result = $db->loadResult();
					$isNew = ($result>0)?false:true;
				}
				
				if($stance){
					$instance_table = isset($instance_table)?$instance_table:$intance_column;
					$tableInstance = JTable::getInstance($instance_table);
					
					//update case
					if( ($operation==0) && !$isNew ){
						$tableInstance->load($insert->{$primary});
					}
					if(property_exists($tableInstance, 'parent_id')){
						$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
						if (!$tableInstance->isLeaf())
							@$tableInstance->setLocation($parent_id, 'last-child');
					}
					if (!$tableInstance->bind((array)$insert)) {
						$obj->result = 'error';
						$obj->error = $tableInstance->getError();
						return $obj;
					}
					if (!$tableInstance->check()) {
						$obj->result = 'error';
						$obj->error = $tableInstance->getError();
						return $obj;
					}
					if (!$tableInstance->store()){
						$obj->result = 'error';
						$obj->error = $tableInstance->getError();
						return $obj;
					}
					if(property_exists($tableInstance, $primary)){
						$insert->{$primary} = $tableInstance->{$primary};
					}
				}
				else{
					if(isset($params->component->table[$i]) && !empty($params->component->table[$i])){
						$component = JText::_($params->component->value[$i]);
						$component_name = $component;
						if (strpos(strtolower($component), "com_") ===FALSE){
							$component_name = "com_".strtolower($component_name); 
						}
						if (strpos(strtolower($component), "com_") ===0){
							$component = str_replace("com_", '',strtolower($component)); 
						}
						JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
						$core_tables = array('category', 'content', 'menu', 'module');
						if(in_array($params->component->table[$i], $core_tables)){
							$tableInstance = JTable::getInstance($params->component->table[$i]);
						}
						else{
							$tableInstance = JTable::getInstance($params->component->table[$i], $component.'Table');
						}
						
						//update case
						if( ($operation==0) && !$isNew ){
							$tableInstance->load($insert->{$primary});
						}
						if(property_exists($tableInstance, 'parent_id')){
							$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
							if (!$tableInstance->isLeaf())
								@$tableInstance->setLocation($parent_id, 'last-child');
						}
						if (!$tableInstance->bind((array)$insert)) {
							$obj->result = 'error';
							$obj->error = $tableInstance->getError();
							return $obj;
						}
						if (!$tableInstance->check()) {
							$obj->result = 'error';
							$obj->error = $tableInstance->getError();
							return $obj;
						}
						if (!$tableInstance->store()){
							$obj->result = 'error';
							$obj->error = $tableInstance->getError();
							return $obj;
						}
						if(property_exists($tableInstance, $primary)){
							$insert->{$primary} = $tableInstance->{$primary};
						}
					}
					else{
						if(!$isNew){
							if(!empty($primary)){
								
								if($db->updateObject($table2, $insert, is_array($primary)?$primary[0]:$primary)){
									
								}
								else{
									JLog::add(JText::sprintf('UPDATE_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
									$obj->result = 'error';
									$obj->error = $db->stderr();
									return $obj;
								}
							}
						}else{
							if($db->insertObject($table2,$insert)){
								if(!empty($primary)){
									$insert->$primary = $db->insertid();
								}
							}
							else{
								JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
								$obj->result = 'error';
								$obj->error = $db->stderr();
								return $obj;
							}
						}
					}
				}
				
					/* if(!empty($primary)){
						$insert->$primary = 3;
					}
					print_r($insert); */
					
				$new_data[$params->column1[$i].':'.$params->column2[$i]][] = $insert;
			}
			if($operation==2){
				try{
					$where = array();
					$delQuery = 'DELETE FROM '. $table2 .' WHERE '.$params->column2[0] .' = '. $base_in_id .' AND ';
					//delete from xxxx_k2_comments where itemID=1 and (user_id,username) NOT IN ((0,'test user1'),(135,'Super User'));
					$syncData = array_values($new_data)[0];
					foreach($syncData as $obj){
						$temp ='(';
						$temp .=implode(',',array_diff(array_values(get_object_vars($obj)), [$base_in_id])).')';
						$where[] = $temp;
					}
					$delQuery .= '('.implode(',',array_diff(array_keys(get_object_vars($obj)), [$params->column2[0]])).') NOT IN ('.implode(',',$where).')';
					$db->setQuery($delQuery);
					$db->execute();
				}
				catch (Exception $e){
					$obj->result = 'error';
					$obj->error = $e->getMessage();
					return $obj;
				}
				
			}
			
			$obj->result = 'success';
			$obj->previous_data = $new_data;
			return $obj;
		}
		
		
		function getDefinedJsonValue($default, $params, $data, $fields, $type='p'){
			
			$defined = $default;
			$hd_local_pattern = '/@vdLocal:[\w]*[.?\w]*/';
			$hd_php_pattern = '/@vdPhp:(.*?)$/';
			$hd_sql_pattern = '/@vdSql:(.*?)$/';
			$hd_remote_pattern = '/@vdRemote:[\w]*[.?\w]*/';
			
			//filter based on profile columns
			$hdlocal = preg_match_all($hd_local_pattern, $defined, $local_matches);
			if( ($hdlocal!==FALSE) ){
				foreach($local_matches[0] as $mk=>$match){
					if(!empty($match)){
						$fn = explode(':', $match);
						if( ( $fn[1]!="" ) ){
							$info = explode('.', $fn[1]);
							
							if(count($info)==1){
								if(isset($fields[$info[0]])){
									if($type='c'){
										$rootField = explode(':', $fields[$info[0]][count($fields[$info[0]])-1]);
										$targetField = $rootField[count($rootField)-1];
										$fieldVal = $this->getJsonVal($data, $targetField);
									}
									else{
										$fieldVal = $this->getJsonFieldValue($data, $fields[$info[0]][count($fields[$info[0]])-1]);
									}
									$defined = preg_replace('/'.$match.'/', $fieldVal, $defined);
								}
							}
							elseif(count($info)==2){
								if( !empty($info[0]) && !empty($info[1]) && property_exists($params, $info[0]) && ($params->{$info[0]}->data=='reference') && in_array($info[1], $params->{$info[0]}->reftext) ) {
									if(isset($fields[$info[0]][$info[1]])){
										if($type='c'){
											$rootField = explode(':', $fields[$info[0]][$info[1]][count($fields[$info[0]][$info[1]])-1]);
											$targetField = $rootField[count($rootField)-1];
											$fieldVal = $this->getJsonVal($data, $targetField);
										}
										else{
											$fieldVal = $this->getJsonFieldValue($data, $fields[$info[0]][$info[1]][count($fields[$info[0]][$info[1]])-1]);
										}
										$defined = preg_replace('/'.$match.'/', $fieldVal, $defined);
									}
								}
							}
						}
					}
				}
			}
			
			//filter using data source json file
			$hdremote = preg_match_all($hd_remote_pattern, $defined, $local_matches);
			if( $hdremote!==FALSE ){
				foreach($local_matches[0] as $mk=>$match){
					if(!empty($match)){
						$fn = explode(':', $match);
						$targetProp = $fn[1];
						if( ( $targetProp!="" ) ){
							
							foreach($data as $key=>$value){
								if($key==$targetProp){
									$fieldVal = $value;
									$defined = preg_replace('/'.$match.'/', $fieldVal, $defined);
									break;
								}
							}
						}
					}
				}
			}
			
			// $hdphp = preg_match_all($hd_php_pattern, $defined, $php_matches);
			$hdphp = preg_match($hd_php_pattern, $defined, $php_matches);
			if( ($hdphp!==FALSE) && !empty($php_matches[0])){
				$temp = $php_matches[0];
				$func = explode(':',$temp, 2);
				if(!empty($func[1])){
					$response = @eval("return ".$func[1].";");
					if (error_get_last()){
						//throw Or log error (error_get_last())
					}
					else{
						$defined = $response;
					}
				}
			}
			// $hdsql = preg_match_all($hd_sql_pattern, $defined, $sql_matches);
			$hdsql = preg_match($hd_sql_pattern, $defined, $sql_matches);
			if( ($hdsql!==FALSE) && !empty($sql_matches[0])){
				$temp = $sql_matches[0];
				$func = explode(':',$temp, 2);
				if(!empty($func[1])){
					$query = 'select '.$func[1];
					try{
						$db = $this->getDbc();
						$db->setQuery($query);
						$defined = $db->loadResult();
					}
					catch(Exception $e){
						$err = $e->getMessage();
						//throw Or log error $e->getMessage()
					}
				}
			}
			
			return $defined;
			
		}
		
		function captureEventsOnRecord( $row, $time, $isNew, $options )
		{
			$return = array();
			if( $time=='before' ){
				if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'before') ) {
					
					foreach($this->profile->params->events->before as $event) {
						$info = explode(':', $event);
						$return[] = $this->triggerEvent($info[0], $info[1], $row, $isNew, $options);
					}
				}
			}
			if( $time=='after' ){
				if( property_exists($this->profile->params, 'events') && property_exists($this->profile->params->events, 'after') ) {
					
					foreach($this->profile->params->events->after as $event) {
						$info = explode(':', $event);
						$return[] = $this->triggerEvent($info[0], $info[1], $row, $isNew, $options);
					}
				}
			}
			return $return;
		}
		
		function triggerEvent($group, $element, $data, $isNew, $options){
			
			JPluginHelper::importPlugin($group);
			$dispatcher = JEventDispatcher::getInstance();
			switch($group){
				case 'user':
					switch($element){
						case 'onUserBeforeSave':
							$oldUser = array_key_exists('old', $options) ? $options['old'] : null;
							
							// JLog::add(JText::sprintf('TRIGGER_EVENT', $element), JLog::INFO, 'com_vdata');
							try{
								$result = $dispatcher->trigger( $element, array($oldUser, $isNew, $data) );
							}
							catch(Exception $e){
								JLog::add($e->getMessage(), JLog::ERROR, 'com_vdata');
								return false;
							}
							return true;
						break;
						case 'onUserAfterSave':
							$success = array_key_exists('success', $options) ? $options['success'] : false;
							$msg = JText::_('EVENT_TRIGGER_ERROR');
							if(!property_exists($data, 'params') || !property_exists($data, 'name') || !property_exists($data, 'username') || !property_exists($data, 'password') || !property_exists($data, 'email') ){
								JLog::add(JText::sprintf('TRIGGER_EVENT_INVALID_DATA', $group, $element), JLog::ERROR, 'com_vdata');
								break;
							}
							// JLog::add(JText::sprintf('TRIGGER_EVENT', $element), JLog::INFO, 'com_vdata');
							try{
								$data->password_clear = $data->password;
								$result = $dispatcher->trigger( $element, array((array)$data, $isNew, $success, $msg) );
							}
							catch(Exception $e){
								JLog::add($e->getMessage(), JLog::ERROR, 'com_vdata');
								return false;
							}
							return true;
						break;
					}
				break;
				case 'content':
					if (version_compare(JVERSION, '3.0', 'lt')) 
						JTable::addIncludePath(JPATH_PLATFORM . 'joomla/database/table');
					$article = JTable::getInstance('content');
					foreach($data as $key=>$value)
						$article->$key = $value;
					
					switch($element){
						case 'onContentBeforeSave':
							JLog::add(JText::sprintf('TRIGGER_EVENT', $element), JLog::INFO, 'com_vdata');
							try{
								$result = $dispatcher->trigger( $element, array('com_content.article' ,$article , $isNew) );
							}
							catch(Exception $e){
								JLog::add($e->getMessage(), JLog::ERROR, 'com_vdata');
								return false;
							}
							return true;
						break;
						case 'onContentAfterSave':
							JLog::add(JText::sprintf('TRIGGER_EVENT', $element), JLog::INFO, 'com_vdata');
							try{
								$result = $dispatcher->trigger( $element, array('com_content.article' ,$article , $isNew) );
							}
							catch(Exception $e){
								JLog::add($e->getMessage(), JLog::ERROR, 'com_vdata');
								return false;
							}
							return true;
						break;
						case 'onContentChangeState':
							$pks=array();
							if( array_key_exists('pk', $options) && !empty($options['pk']) ){
								foreach($options['pk'] as $pkey){
									if(isset($article->{$pkey})){
										$pks[] = $article->{$pkey};
									}
								}
								// $pks[] = $article->{$options['pk']};
							}
							if(property_exists($article, 'state')){
								JLog::add(JText::sprintf('TRIGGER_EVENT', $element), JLog::INFO, 'com_vdata');
								try{
									$result = $dispatcher->trigger( $element, array('com_content.article' ,$pks , $article->state) );
								}
								catch(Exception $e){
									JLog::add($e->getMessage(), JLog::ERROR, 'com_vdata');
									return false;
								}
							}
							else{
								JLog::add(JText::sprintf('TRIGGER_EVENT_INVALID_DATA', $group, $element), JLog::ERROR, 'com_vdata');
							}
							return true;
						break;
						/* case 'onCategoryChangeState':
						
						break; */
					}
				break;
				case 'finder':
					if (version_compare(JVERSION, '3.0', 'lt')) 
						JTable::addIncludePath(JPATH_PLATFORM . 'joomla/database/table');
					$article = JTable::getInstance('content');
					foreach($data as $key=>$value)
						$article->$key = $value;
						
					switch($element){
						case 'onFinderBeforeSave':
							JLog::add(JText::sprintf('TRIGGER_EVENT', $element), JLog::INFO, 'com_vdata');
							try{
								$result = $dispatcher->trigger( $element, array('com_content.article' ,$article , $isNew) );
							}
							catch(Exception $e){
								JLog::add($e->getMessage(), JLog::ERROR, 'com_vdata');
								return false;
							}
							return true;
						break;
						case 'onFinderAfterSave':
							JLog::add(JText::sprintf('TRIGGER_EVENT', $element), JLog::INFO, 'com_vdata');
							try{
								$result = $dispatcher->trigger( $element, array('com_content.article' ,$article , $isNew) );
							}
							catch(Exception $e){
								JLog::add($e->getMessage(), JLog::ERROR, 'com_vdata');
								return false;
							}
							return true;
						break;
						case 'onFinderChangeState':
							$pks=array();
							if( array_key_exists('pk', $options) && !empty($options['pk']) ){
								foreach($options['pk'] as $pkey){
									if(isset($article->{$pkey})){
										$pks[] = $article->{$pkey};
									}
								}
								// $pks[] = $article->{$options['pk']};
							}
							if(property_exists($article, 'state')){
								JLog::add(JText::sprintf('TRIGGER_EVENT', $element), JLog::INFO, 'com_vdata');
								try{
									$result = $dispatcher->trigger( $element, array('com_content.article' ,$pks , $article->state) );
								}
								catch(Exception $e){
									JLog::add($e->getMessage(), JLog::ERROR, 'com_vdata');
									return false;
								}
							}
							return true;
						break;
						/* case 'onFinderCategoryChangeState':
							
						break; */
					}
				break;
			}
			return true;
		}
		
		
		protected function getAffected($db)
		{
			return $db->getAffectedRows();
		}
		
		function load_expo_columns()
		{
			JSession::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALID_TOKEN').'"}' );
			$db = $this->getDbc();
			$this->getProfile();
			$obj = new stdClass();
			$obj->result = 'success';
			$obj->html = '';
			$query = 'show fields FROM '.$db->quoteName($this->profile->params->table);
			$db->setQuery( $query );

			if($db->getErrorNum())	{
				$obj->error = $db->getErrorMsg();
				return $obj;
			}
			$cols = $db->loadObjectList();
			
			$session = JFactory::getSession();
			$exportitem = $session->get('exportitem');
			//if it's not new schedule,load it's field
			if(!empty($exportitem->id)){
				$query = "select * from #__vd_schedules where id=".$exportitem->id;
				$db->setQuery($query);
				$schedule_task = $db->loadObject();
				$schedule_columns = json_decode($schedule_task->columns);
			}
			else{
				$schedule_columns = null;
			}
			
			$obj->html .= '<tr class="columns"><td colspan="2">'.JText::_('COLUMNS').'</td></tr>';
			
			foreach($cols as $col){
				$obj->html .= '<tr data-column='.$col->Field.'>';
				$obj->html .= '<td width="200"><label class="hasTip" title="">'.$col->Field.'</label></td>';
				$obj->html .= '<td><input type="text" name="field['.$col->Field.']" value="';
				if( !empty($schedule_columns) && property_exists($schedule_columns, 'quick') &&($schedule_columns->quick==0) && !empty($schedule_columns->fields->{$col->Field}) ){
					$obj->html .= $schedule_columns->fields->{$col->Field};
				}
				else{
					$obj->html .= $col->Field;
				}
				
				$obj->html .= '" /></td>';	
				$obj->html .= '</tr>';
			}
			jexit(json_encode($obj));
		}
		
		function load_remote_table_columns()
		{
			JSession::checkToken() or jexit('{"result":"error", "error":"'.JText::_('INVALID_TOKEN').'"}');
			$db = JFactory::getDbo();
			$this->getProfile();
			$obj = new stdClass();
			$obj->result = 'success';
			$obj->html = '';
			$query = 'show fields FROM '.$db->quoteName($this->profile->params->table);
			$db->setQuery( $query );

			if($db->getErrorNum())	{
				$obj->result = 'error';
				$obj->error = $db->getErrorMsg();
				return $obj;
			}
			$cols = $db->loadObjectList();
			
			
			$session = JFactory::getSession();
			try{
				$option = $session->get('remote_details');
				$remote_db = JDatabaseDriver::getInstance( $option );
				$remote_db->connect();
			}
			catch (RuntimeException $e){
				$obj->result = 'error';
				$remote_db->error = $remote_db->getErrorMsg();
				return $obj;
			}

			$exportitem = $session->get('exportitem');
			//if it's not new schedule,load it's field
			if(!empty($exportitem->id)){
				$query = "select * from #__vd_schedules where id=".$exportitem->id;
				$db->setQuery($query);
				$schedule = $db->loadObject();
				$schedule_columns = json_decode($schedule->columns);
				$columns = json_decode(json_encode($schedule_columns), true);
			}
			else{
				$schedule_columns = null;
				$columns = null;
			}
			
			$rtable = JFactory::getApplication()->input->get('table', '', 'RAW');

			$obj->html .= '<tr class="columns"><td colspan="2">'.JText::_('COLUMNS').'</td></tr>';
			foreach($cols as $col)
			{
				$obj->html .= '<tr data-column='.$col->Field.'>';
				$obj->html .= '<td width="200"><label class="hasTip" title="">'.$col->Field.'</label></td>';
				$obj->html .= '<td><select class="expo_columns" name="fields['.$col->Field.']">
							<option value="">'.JText::_('Select Field').'</option>';
				
				if(!empty($columns) && ($columns['quick']==0)){
					$selected = isset($columns['fields'][$col->Field])?$columns['fields'][$col->Field]:'';
				}
				else{
					$selected = $col->Field;
				}
				
				$response =  $this->getColumnOptions($rtable, $selected, $remote_db);
				$obj->html .= $response->options;
				$obj->html .= '</select></td>';	
				$obj->html .= '</tr>';
			}
			jexit(json_encode($obj));
		}
		
		function onExportProfile()
		{
			$db = JFactory::getDbo();
			$dbprefix = $db->getPrefix();
			$lang = JFactory::getLanguage();
			$lang->load('plg_vdata_custom', JPATH_SITE.'/plugins/vdata/custom');
			$source = JFactory::getApplication()->input->get('source', '', 'STRING');
			$this->getProfile();
			
			$exportitem = (object)JFactory::getApplication()->input->post->getArray();
			
			$exportitem->qry = JFactory::getApplication()->input->get('qry', '', 'RAW');
			
			//if its schedule task, verify export query if provided by user
			$st = JFactory::getApplication()->input->getInt('st', 0);
			$qry = JFactory::getApplication()->input->get('qry', '', 'RAW');
			$profileid = JFactory::getApplication()->input->getInt('profileid', 0);
			if($st){
				if(empty($qry) && ($profileid ==0)){
					throw new Exception(JText::_('PLZ_SELECT_PROFILE_OR_QUERY'));
					return false;
				}
				if(!empty($qry)){
					try{
						$config_db = $this->getDbc();
						
						$reg = "/^(\s*?)select\s*?.*?\s*?from([\s]|[^;]|(['\"].*;.*['\"]))*;?$/i";
						preg_match($reg,trim($qry),$match);
						if(empty($match)){
							throw new Exception(JText::_('PLZ_ENTER_SELECT_QRY').' - '.$config_db->quote($qry));
							return false;
						}
						
						$config_db->setQuery($qry);
						$config_db->loadObjectList();
					}
					catch(RuntimeException $e){
						throw new Exception(JText::_('PLZ_ENTER_VALID_QUERY').' - '.$e->getMessage());
						return false;
					}
				}
			}
			
			$session = JFactory::getSession();
			$session->set('exportitem', $exportitem);
			
			//if it's not new schedule,load it's field
			if(!empty($exportitem->id)){
				$query = "select * from #__vd_schedules where id=".$exportitem->id;
				$db->setQuery($query);
				$schedule = $db->loadObject();
				$schedule_columns = json_decode($schedule->columns);
			}
			else{
				$schedule = null;
				$schedule_columns = null;
			}
			
			if( $st && !empty($qry)  ){//&& ($profileid==0)
				$config_db = $this->getDbc();
				
				$config_db->setQuery($qry);
				$sfields = $config_db->loadAssoc();
				if(empty($sfields)){
					throw new Exception(JText::_('NO_RECORD_IN_TABLE'));
					return false;
				}
			}
			if($st){
				if($profileid !=0){
					if($this->profile->quick==1){
						$query = 'show fields FROM '.$db->quoteName($this->profile->params->table);
						$db->setQuery($query);
						$feedfileds = $db->loadObjectList();
					}
				}
				
			}
			
			if(($st==0) && empty($this->profile->id)) {
				throw new Exception(JText::_('PLZ_SELECT_PROFILE'));
				return false;
			}
			
			switch($source) {
				case 'CSV' :
				case 'csv':
					//if(!$st)
						$flag = $this->validateFile();
						require(dirname(__FILE__).'/tmpl/export.php');
					break;
				case 'XML' :
				case 'xml':
					//if(!$st)
						$flag = $this->validateFile();
						require(dirname(__FILE__).'/tmpl/export.php');
					break;
				case 'JSON' :
				case 'json':
					//if(!$st)
						$flag = $this->validateFile();
						require(dirname(__FILE__).'/tmpl/export.php');
					break;
				case 'remote':
					$this->prepare_remote(1);
					require(dirname(__FILE__).'/tmpl/export_remote.php');
					break;
				case 'SITEMAP':
					require(dirname(__FILE__).'/tmpl/feeds_sitemap.php');
					break;
				case 'RSS2':
					require(dirname(__FILE__).'/tmpl/feeds_rss2.php');
					break;
				case 'RSS1':
					require(dirname(__FILE__).'/tmpl/feeds_rss1.php');
					break;
				case 'ATOM':
					require(dirname(__FILE__).'/tmpl/feeds_atom.php');
					break;
				default:
					throw new Exception(JText::_('PLZ_SELECT_DATA_SOURCE'));
					return false;
					break;
			}
			
		}
		
		function validateFile(){
			
			$source = JFactory::getApplication()->input->get('source', '', 'STRING');
			$path = $temp_path = JFactory::getApplication()->input->get("path", '', 'RAW');
			$server = JFactory::getApplication()->input->get("server", 'down', 'STRING');
			
			if(empty($source)){
				throw new Exception(JText::_('PLZ_SELECT_FORMAT'));
				return false;
			}
			//if download file selected ,no validation required
			if($server=='down'){
				return true;
			}
			$type = JFactory::getApplication()->input->get('type', '', 'STRING');
			$session = JFactory::getSession();
			$exportitem = $session->get('exportitem');
			if($type==2){
				return true;
			}
		
			//validate ftp connection
			if($server=='write_remote' || $server=='remote'){
				$inputFtp = JFactory::getApplication()->input->getVar('ftp', array(),'ARRAY');
				if(empty($inputFtp['ftp_host']) || empty($inputFtp['ftp_user']) || empty($inputFtp['ftp_pass'])){
					throw new Exception(JText::_('VDATA_EMPTY_FTP_CREDENTIALS'));
					return false;
				}
				if(!empty($inputFtp['ftp_file'])){
					$ext = strrchr($inputFtp['ftp_file'], '.');
					if($ext<>'.'.$source){
						throw new Exception(JText::sprintf('PLZ_SELECT_RIGHT_SOURCE_FILE', $source));
						return false;
					}
				}
				$ftp = $this->ftpConnection($inputFtp);
				if($ftp->result=='error'){
					throw new Exception($ftp->error);
					return false;
				}
				return true;
			}
			
			//validate file write on local server
			$path = JPATH_ROOT.'/'.$path;
			if(($server=='write_local' || $server=='local') && empty($path)){
				throw new Exception(JText::_('PLZ_SELECT_FILE'));
				return false;
			}
			
			$ext = strrchr($path, '.');
			switch($source){
				case 'CSV':
				case 'csv':
					if($ext<>'.csv'){
						throw new Exception(JText::_('CSV_FILE_NOT_FOUND'));
						return false;
					}
				break;
				case 'JSON':
				case 'json':
					if($ext<>'.json'){
						throw new Exception(JText::_('JSON_FILE_NOT_FOUND'));
						return false;
					}
				break;
				case 'XML':
				case 'xml':
					if($ext<>'.xml'){
						throw new Exception(JText::_('XML_FILE_NOT_FOUND'));
						return false;
					}
				break;
			}
			
			if($server=='write_local' || $server=='local'){
				$base = basename($path);
				$dir = dirname($path);
				if(!empty($base)){
					$file_ext = explode('.', $base);
					if( (count($file_ext)>1) ){
						if(empty($file_ext[0])){
							//if file extension exists and no file name given
							$this->getProfile();
							$file_ext[0] = JFilterOutput::stringURLSafe($this->profile->title);
							$path = $dir.DIRECTORY_SEPARATOR.implode('.', $file_ext);
							$dir_ses = dirname($temp_path);
							$session = JFactory::getSession();
							$exportitem = $session->get('exportitem');
							$exportitem->path = $dir_ses.DIRECTORY_SEPARATOR.implode('.', $file_ext);
							$session->set('exportitem', $exportitem);
						}
						if( !empty($file_ext[count($file_ext)-1]) && strrchr($path, '.') ){
							$dir = dirname($path);
							if (!file_exists($dir)) {
								mkdir($dir, 0777, true);
							}
						}
					}
				}
				
			}
			
			try {
				$fp = @fopen($path, "a");
				if ( !$fp ) {
					$open_error = error_get_last();
					throw new Exception($open_error['message']);
					return false;
				}
			} catch(Exception $e){
				throw new Exception($e->getMessage());
				return false;
			}
			
			return true;
		}
		
		function batchExport()
		{
			$this->getProfile();
			$session = JFactory::getSession();
			$exportitem = $session->get('exportitem');
			$source = $exportitem->source;
			$config = $this->getConfig();
			$csv_child = json_decode($config->csv_child);
			switch($source) {
				case 'csv' :
					if($this->profile->quick)
						$res = $this->batch_export_csv_quick();
					else{
						if($csv_child->csv_child==2)
							$res = $this->batch_export_csv();
						else
							$res = $this->batch_export_csv1();
					}
					if($res->result == 'success'){
						return $res;
					}
					else{
						$session->clear('exportitem');
						$session->clear('qty');
						return $res;
					}
				break;
				case 'xml' :
					if($this->profile->quick)
						$res = $this->batch_export_xml_quick();
					else
						$res = $this->batch_export_xml();
					if($res->result == 'success'){
						return $res;
					}
					else{
						$session->clear('exportitem');
						$session->clear('qty');
						return $res;
					}
				break;
				case 'json' :
					if($this->profile->quick)
						$res = $this->batch_export_json_quick();
					else
						$res = $this->batch_export_json();
					if($res->result == 'success'){
						return $res;
					}
					else{
						$session->clear('exportitem');
						$session->clear('qty');
						return $res;
					}
				break;
				case 'remote':
					if($this->profile->quick)
						$res = $this->batch_export_remote_quick();
					else
						$res = $this->batch_export_remote();
					$res->dlink = false;
					if($res->result=='success'){
						return $res;
					}
					else{
						$session->clear('exportitem');
						$session->clear('qty');
						$session->clear('expo_op');
						return $res;
					}
				break;
				default :
				$res = new stdClass();
				$res->result = 'error';
				$res->error = JText::_('EXPORT_DATA_FAIL');
				return $res;
				break;
			}
		}
		
		function batch_export_csv_quick()
		{
			$obj = new stdClass();
			$db = $this->getDbc();
			$session = JFactory::getSession();
			$quick = JFactory::getApplication()->input->getInt('quick', 0);
			$quick_fields = JFactory::getApplication()->input->get('field', array(), 'ARRAY');
			$exportitem = $session->get('exportitem');
			$server = $exportitem->server;
			
			$offset = JFactory::getApplication()->input->getInt('offset');
			$config = $this->getConfig();
			
			if($exportitem->server=='down')
			{
				if($offset == 0){
					$filename = JFilterOutput::stringURLSafe($this->profile->title).'.csv';
					$exportitem->write = $filename;
					$session->set('exportitem',$exportitem);
					$mode = 'w';
				}
				else{
					$filename = $exportitem->write;
					$mode = 'a';
				}
				$filepath = JPATH_ROOT.'/components/com_vdata/uploads/'.$filename;
				$output = @fopen($filepath, $mode);
				$session->set('down', $filename);
			}
			elseif($exportitem->server=='write_remote'){
				$filename = JFilterOutput::stringURLSafe($this->profile->title).'.csv';
				$filepath = JPATH_ROOT.'/components/com_vdata/uploads/'.$filename;
				if($offset == 0){
					$output = @fopen($filepath, $exportitem->mode);
				}
				else{
					$output = @fopen($filepath, 'a');
				}
			}
			else
			{	
				$filepath = $exportitem->path;
				
				if($exportitem->server=='write_local')
				{
					$filepath = JPATH_ROOT.'/'.$exportitem->path;
				}
				
				//validate file extension
				$ext = strrchr($filepath, '.');
				if($ext <> '.csv')	
				{
					$obj->result = 'error';
					$obj->error = JText::_('PLZ_SELECT_CSV_FILE');
					return $obj;
				}
				//file_exists,
				if($exportitem->mode=='a' && !is_file($filepath))    
				{
					$obj->result = "error";
					$obj->error = JText::_('COULD_NOT_ACCESS');
					return $obj;
				}

				if($exportitem->mode=='a' && !is_writable($filepath))
				{
					$obj->result = "error";
					$obj->error = JText::_('NOT_WRITABLE_FILE');
					return $obj;
				}
				
				if($offset == 0){
					$output = @fopen($filepath, $exportitem->mode);
				}
				else{
					$output = @fopen($filepath, 'a');
				}
			}

			if($output == FALSE){
				$obj->result = "error";
				$open_error = error_get_last();
				$obj->error = $open_error['message'];//JText::_('FAIL_TO_OPEN_STREAM')
				return $obj;
			}
			
			$query = "select * from ".$db->quoteName($this->profile->params->table);
			$db->setQuery($query, $offset, $config->limit);
			$data = $db->loadAssocList();
			
			$size_query = "select count(*) from ".$db->quoteName($this->profile->params->table);
			$db->setQuery($size_query);
			$size = $db->loadResult();
			
			$n = 0;
			if(count($data)== 0){
				$qty = $session->get('qty',0);
				if($exportitem->server == 'down' && $qty){
					$obj->dname = $filename;
					$obj->dlink = true;
				}
				else{
					$obj->dlink = false;
				}
				//email export file
				if( ($qty>0) && isset($exportitem->sendfile["status"]) && ($exportitem->sendfile["status"]==1) && isset($exportitem->sendfile["emails"]) && !empty($exportitem->sendfile["emails"]) ){
					$emails = explode(',', $exportitem->sendfile["emails"]);
					$cc = explode(',', $exportitem->sendfile["cc"]);
					$bcc = explode(',', $exportitem->sendfile["bcc"]);
					$subject = $exportitem->sendfile["subject"];
					$body = $exportitem->sendfile["tmpl"];
					$attachment = $filepath;
					$sendfile = $this->sendFile($emails, $cc, $bcc, $subject, $body, $attachment);
				}
				//write file to remote server using ftp
				if( ($qty>0) && ($exportitem->server=='write_remote')){
					if(!$this->uploadToFtp($exportitem->ftp, $filepath)){
						$obj->result = 'error';
						$obj->error = JText::_('VDATA_FTP_FILE_WRITE_ERROR');
						return $obj;
					}
					if(!JFile::delete($filepath)){
						JLog::add(JText::sprintf('VDATA_UNABLE_TO_DELETE_LOCAL_COPY', $filepath), JLog::INFO, 'com_vdata');
					}
				}
				
				//send notification if done from site part
				if(JFactory::getApplication()->isSite()){
					$notify = $this->sendNotificationEmail( $this->profile->iotype,0 , array('schedule_id'=>"",'schedule_title'=>"","schedule_qry"=>"",'profile_id'=>$this->profile->id,'profile_title'=>$this->profile->title,"count"=>$qty, "format"=>"csv") );
				}
				
				JLog::add(JText::sprintf('EXPORT_RECORD_COUNT', $qty), JLog::INFO, 'com_vdata');
				$obj->size = $size;
				$obj->result = 'error';
				$obj->qty = $qty;
				$obj->error = JText::_('NO_RECORD_TO_EXPORT');
				return $obj;
			}
			
			$delimiter = $this->getDelimiter();
			$enclosure = $this->getEnclosure();
			$headingStatus = $config->csv_header;
			
			foreach($data as $h=>$row)
			{
				if( ($offset==0) && ($h==0) && (($exportitem->server=='down') || ($exportitem->mode=='w')) )
				{
					JLog::add(JText::sprintf('EXPORT_PROFILE', $this->profile->title), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_TABLE', $this->profile->params->table), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_FORMAT', 'CSV'), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_DELIMITER', $delimiter), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_ENCLOSURE', $enclosure), JLog::INFO, 'com_vdata');
					if($quick){
						//set heading
						if($headingStatus){
							$fields = array_keys($row);
							fputcsv($output, $fields, $delimiter, $enclosure);
						}
						JLog::add(JText::sprintf('EXPORT_COLUMNS', implode(',', $fields)), JLog::INFO, 'com_vdata');
					}
					else{
						//set custom heading
						if($headingStatus){
							foreach($quick_fields as $field){
								if(empty($field)){
									$obj->result = 'error';
									$obj->error = JText::_('ENTER_HEADING');
									return $obj;
								}
							}
							fputcsv($output, $quick_fields, $delimiter, $enclosure);
						}
						JLog::add(JText::sprintf('EXPORT_COLUMNS', implode(',', $quick_fields)), JLog::INFO, 'com_vdata');
					}
				}
				if(fputcsv($output, $row, $delimiter, $enclosure)) 
					$n++;
			}
			
			$obj->result='success';
			$obj->size = $size;
			$obj->offset = $offset + $config->limit;
			$qty = $session->get('qty',0) + $n;
			$session->set('qty',$qty);
			return $obj;
		}
		
		function getDelimiter()
		{
			$config = $this->getConfig();
			$delimiter = json_decode($config->delimiter);
			switch($delimiter->value){
				case 'comma':
					return ",";
				break;
				case 'tab':
					return "\t";
				break;
				case 'semicolon':
					return ";";
				break;
				case 'space':
					return " ";
				break;
				case 'pipe':
					return "|";
				break;
				case 'other':
					return $delimiter->other;
				break;
				default:
				return ",";
			}
		}
		
		function getEnclosure()
		{
			$config = $this->getConfig();
			$enclosure = json_decode($config->enclosure);
			switch($enclosure->value){
				case 'dquote':
					return '"';
				break;
				case 'squote':
					return "'";
				break;
				case 'other':
					return $enclosure->other;
				break;
				default:
				return '"';
			}
		}
		
		function getChildDelimiter(){
			$config = $this->getConfig();
			$child_delimiter = json_decode($config->csv_child);
			switch($child_delimiter->child_sep){
				case 'comma':
					return ",";
				break;
				case 'tab':
					return "\t";
				break;
				case 'semicolon':
					return ",";
				break;
				case 'space':
					return " ";
				break;
				case 'pipe':
					return "|";
				break;
				case 'other':
					return $child_delimiter->child_del;
				break;
				default:
				return "|";
			}
		}
		
		function batch_export_csv()
		{
			$obj = new stdClass();
			$db = $this->getDbc();
			$session = JFactory::getSession();
			$expo_fields = JFactory::getApplication()->input->get('field', array(), 'ARRAY');
			$exportitem = $session->get('exportitem');
			
			$offset = JFactory::getApplication()->input->getInt('offset');
			$config = $this->getConfig();
			$delimiter = $this->getDelimiter();
			$enclosure = $this->getEnclosure();
			$headingStatus = $config->csv_header;
			
			if($exportitem->server=='down')
			{
				$filename = JFilterOutput::stringURLSafe($this->profile->title).'.csv';
				
				if($offset == 0){
					$filename = JFilterOutput::stringURLSafe($this->profile->title).'.csv';
					$exportitem->write = $filename;
					$session->set('exportitem',$exportitem);
					$mode = 'w';
				}
				else{
					$filename = $exportitem->write;
					$mode = 'a';
				}
				$filepath = JPATH_ROOT.'/administrator/components/com_vdata/uploads/'.$filename;
				$filepath = JPATH_ROOT.'/components/com_vdata/uploads/'.$filename;
				$output = @fopen($filepath, $mode);
				$session->set('down', $filename);
			}
			elseif($exportitem->server=='write_remote'){
				
				$filename = JFilterOutput::stringURLSafe($this->profile->title).'.csv';
				$filepath = JPATH_ROOT.'/components/com_vdata/uploads/'.$filename;
				if($offset == 0){
					$output = @fopen($filepath, $exportitem->mode);
				}
				else{
					$output = @fopen($filepath, 'a');
				}
			}
			else
			{
				$filepath = $exportitem->path;
				if($exportitem->server=='write_local')
				{
					$filepath = JPATH_ROOT.'/'.$exportitem->path;
				}
				
				//validate file extension
				$ext = strrchr($filepath, '.');
				if($ext <> '.csv')	
				{
					$obj->result = 'error';
					$obj->error = JText::_('PLZ_SELECT_CSV_FILE');
					return $obj;
				}
				if($exportitem->mode=='a' && !is_file($filepath))    
				{
					$obj->result = "error";
					$obj->error = JText::_('COULD_NOT_ACCESS');
					return $obj;
				}

				if($exportitem->mode=='a' && !is_writable($filepath))
				{
					$obj->result = "error";
					$obj->error = JText::_('NOT_WRITABLE_FILE');
					return $obj;
				}
				if($offset == 0)
					$output = @fopen($filepath, $exportitem->mode);
				else
					$output = @fopen($filepath, 'a');
			}

			if($output == FALSE){
				$obj->result = "error";
				$open_error = error_get_last();
				$obj->error = $open_error['message'];//JText::_('FAIL_TO_OPEN_STREAM')
				return $obj;
			}
			//retrieve data
			$result = $this->getExportData($db, $this->profile->params, $offset, $config->limit, 'ar');
			$data = $result->data;
			$size  = $result->size;
			
			$n = 0;
			if(count($data)== 0)	{
				$qty = $session->get('qty', 0);
				if($exportitem->server == 'down' && $qty){
					$obj->dname = $filename;
					$obj->dlink = true;
				}
				else{
					$obj->dlink = false;
				}
				//email export file
				if( ($qty>0) && isset($exportitem->sendfile["status"]) && ($exportitem->sendfile["status"]==1) && isset($exportitem->sendfile["emails"]) && !empty($exportitem->sendfile["emails"]) ){
					$emails = explode(',', $exportitem->sendfile["emails"]);
					$cc = explode(',', $exportitem->sendfile["cc"]);
					$bcc = explode(',', $exportitem->sendfile["bcc"]);
					$subject = $exportitem->sendfile["subject"];
					$body = $exportitem->sendfile["tmpl"];
					$attachment = $filepath;
					$sendfile = $this->sendFile($emails, $cc, $bcc, $subject, $body, $attachment);
				}
				//write file to remote server using ftp
				if( ($qty>0) && ($exportitem->server=='write_remote')){
					if(!$this->uploadToFtp($exportitem->ftp, $filepath)){
						$obj->result = 'error';
						$obj->error = JText::_('VDATA_FTP_FILE_WRITE_ERROR');
						return $obj;
					}
					if(!JFile::delete($filepath)){
						JLog::add(JText::sprintf('VDATA_UNABLE_TO_DELETE_LOCAL_COPY', $filepath), JLog::INFO, 'com_vdata');
					}
				}
				//send notification if done from site part
				if(JFactory::getApplication()->isSite()){
					$notify = $this->sendNotificationEmail( $this->profile->iotype,0 , array('schedule_id'=>"",'schedule_title'=>"","schedule_qry"=>"",'profile_id'=>$this->profile->id,'profile_title'=>$this->profile->title,"count"=>$qty, "format"=>"csv") );
				}
				JLog::add(JText::sprintf('EXPORT_RECORD_COUNT', $qty), JLog::INFO, 'com_vdata');
				$obj->size = $size;
				$obj->result = 'error';
				$obj->qty = $qty;
				$obj->error = JText::_('NO_RECORD_TO_EXPORT');
				return $obj;
			}
			//validate heading fields
			foreach($expo_fields as $head){
				if(empty($head)){
					$obj->result = 'error';
					$obj->error = JText::_('ENTER_HEADING');
					return $obj;
				}
			}
			
			foreach($data as $h=>$row)
			{
				$write = array();
				$heading = array();
				foreach($this->profile->params->fields as $column=>$field){
					switch($field->data){
						case 'include' : 
							$heading[] = $expo_fields[$column];
							$write[] = $row[$column];
						break;
						case 'defined' :
							$heading[] = $expo_fields[$column];
							$defined = $field->default;
							$defined = $this->getDefinedValue($field->default, $this->profile->params->fields, $row, $expo_fields);
							$write[] = $defined;
						break;
						case 'reference' :
							$query = "SELECT ".$db->quoteName($field->table.''.$h).".".implode(','.$db->quoteName($field->table.''.$h).".", $field->reftext)." FROM ".$db->quoteName($field->table)." AS ".$db->quoteName($field->table.''.$h)." JOIN ".$db->quoteName($this->profile->params->table)." ON ".$db->quoteName($field->table.''.$h).".".$db->quoteName($field->on)." = ".$db->quoteName($this->profile->params->table).".".$column." WHERE ".$db->quoteName($this->profile->params->table).".".$column." = ".$db->quote($row[$column]);
							$db->setQuery($query);
							$ref_data = $db->loadAssoc();
							$heading = array_merge($heading, array_values($expo_fields[$column]));
							if($ref_data){
								$write = array_merge($write, array_values($ref_data));
							}
							else{
								$write = array_merge($write, array_fill(0, count($expo_fields[$column]), ''));
							}
						break;
					}
				}
			
				if(($offset==0) && ($h==0) && ($exportitem->server=='down' || ($exportitem->mode=='w'))){
					//set heading
					if($headingStatus){
						fputcsv($output, $heading, $delimiter, $enclosure);
					}
					JLog::add(JText::sprintf('EXPORT_PROFILE', $this->profile->title), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_TABLE', $this->profile->params->table), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_FORMAT', 'CSV'), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_DELIMITER', $delimiter), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_ENCLOSURE', $enclosure), JLog::INFO, 'com_vdata');
				}
				if(fputcsv($output, $write, $delimiter, $enclosure))
					$n++;
				
				//write child tables data
				if(!empty($this->profile->params->joins->table2)){
					$child_count = count($this->profile->params->joins->table2);
					
					$exData = array();
					$exData[$this->profile->params->table][] = $row;
					
					foreach($this->profile->params->joins->table2 as $c=>$child){
						
						//#
						$blank = array_fill(0, count($heading), NUll);
						if(array_key_exists($child, $expo_fields)){

							$response_data = $this->getExportChildData($db, $c, $this->profile->params, $exData,'ar');
							if($response_data->result=='error'){
								$obj->result = "error";
								$obj->error = $response_data->error;
								return $obj;
							}
							$child_data = $response_data->data;
							
							if(!empty($child_data)){
								foreach($child_data as $cd){
									$exData[$child][] = $cd;
								}
							}
							
							if(!empty($child_data)){
								//write blank line
								for($ch=0;$ch<=$c; $ch++){
									fputcsv($output, $blank, $delimiter, $enclosure);
								}
							}
							
							//write data
							foreach($child_data as $cd=>$cdata){
								$child_heading = array();
								$child_row = array();
								foreach($this->profile->params->joins->columns[$c] as $column=>$field){
									
									switch($field->data){
										case 'include' :
											$child_heading[] = $expo_fields[$child][$column];
											$child_row[] = $cdata[$column];
										break;
										case 'defined' :
											$child_heading[] = $expo_fields[$child][$column];
											$defined = $field->default;
											$defined = $this->getDefinedValue($field->default, $this->profile->params->joins->columns[$c], $cdata, $expo_fields[$child]);
											$child_row[] = $defined;
										break;
										case 'reference' :
											$child_heading = array_merge($child_heading, array_values($expo_fields[$child][$column]));
												
												$query = "SELECT ".$db->quoteName($field->table.''.$c).".".implode(','.$db->quoteName($field->table.''.$c).".", $field->reftext);
												$query .= " FROM ".$db->quoteName($field->table)." AS ".$db->quoteName($field->table.''.$c);
												$query .= " JOIN ".$db->quoteName($child);
												$query .= " ON ".$db->quoteName($field->table.''.$c).".".$db->quoteName($field->on)." = ";
												$query .= ($child=='#__modules_menu')?'ABS('.$db->quoteName($child).".".$column.')':($db->quoteName($child).".".$column);
												$query .= " WHERE ".$db->quoteName($child).".".$column." = ".$db->quote($cdata[$column]);
												
												$db->setQuery($query);
												$ref_data = $db->loadAssoc();
												
												if($ref_data){
													$ref_array_values = array_values($ref_data);
													if($child=='#__modules_menu' && $cdata[$column]<0){
														$resp = array_walk($ref_array_values, function(&$item, $key){if(!is_numeric($item)){
																$item = "(-)".$item;
															}
														});
													}
													$child_row = array_merge($child_row, $ref_array_values);
													
													
												}
												else{
													$child_row = array_merge($child_row, array_fill(0, count($expo_fields[$child][$column]), ''));
													
												}
										break;
									}
									
								}
								if($cd==0){
									fputcsv($output, $child_heading, $delimiter, $enclosure);
								}
								fputcsv($output, $child_row, $delimiter, $enclosure);
							}
							
							//write blank line if it's last child record to indicate next base record
							if($child_count == ($c+1)){
								fputcsv($output, $blank, $delimiter, $enclosure);
							}
							
						}
						//#
					}
				}
				
			}
			$obj->result='success';
			$obj->size = $size;
			$obj->offset = $offset + $config->limit;
			$qty = $session->get('qty',0) + $n;
			$session->set('qty',$qty);
			return $obj;
		}
		
		function getExportData($db, $params, $offset, $limit, $type='ob'){
			
			$obj = new stdClass();
			$data = array();
			$size = 0;
			//export data retrieval query
			$query = "SELECT ".$db->quoteName($params->table).".* FROM ".$db->quoteName($params->table);
			//apply filters
			if(!empty($params->filters) && property_exists($params->filters, 'column')){
				
				//filter tracker
				$applyFilter = array();
				$filterTables = array();
				
				foreach($params->filters->column as $j=>$column){
					$filterField = explode('.', $column);
					$applyFilter[$j] = false;
					
					//base table reference column filter
					if(count($filterField)==2){
						if(isset($params->fields) && !empty($params->fields)){
							foreach($params->fields as $iofield=>$ioval){
								if( ($ioval->data=='reference') && ($ioval->table==$filterField[0]) && !in_array($ioval->table, $filterTables) ){
									$query .= " JOIN ".$db->quoteName($filterField[0]).' ON '.$db->quoteName($params->table).".".$db->quoteName($iofield)."=".$db->quoteName($ioval->table).".".$db->quoteName($ioval->on);
									$applyFilter[$j] = true;
									array_push($filterTables, $ioval->table);
									break;
								}
							}
						}
					}
					//child table column filter
					elseif(count($filterField)==3){
						
						if( isset($params->joins) && isset($params->joins->table2) ){
							
							foreach($params->joins->table2 as $ioidx=>$iotable){
								if( ($iotable==$filterField[1]) ){
									if( ($params->joins->table1[$ioidx]==$params->table) && !in_array($iotable, $filterTables) ){
										//
										$query .= ' JOIN '.$db->quoteName($filterField[1])." ON ".$db->quoteName($params->table).".".$db->quoteName($params->joins->column1[$ioidx])."=".$db->quoteName($filterField[1]).".".$db->quoteName($params->joins->column2[$ioidx]);
										$applyFilter[$j] = true;
										array_push($filterTables, $iotable);
									}
									else{
										$tempIdx = $ioidx;
										$left = $params->joins->table1[$ioidx];
										$joinPath = array($ioidx);
										
										while($tempIdx>=0){
											$tempIdx--;
											if($left==$params->joins->table2[$tempIdx]){
												array_unshift($joinPath, $tempIdx);
												if($params->joins["table1"][$tempIdx]==$params->table){
													break;
												}
											}
										}
										
										foreach($joinPath as $joinIdx){
											if(!in_array($params->joins->table2[$joinIdx], $filterTables)){
												$query .= ' JOIN '.$db->quoteName($params->joins->table2[$joinIdx]).' ON '.$db->quoteName($params->joins->table1[$joinIdx]).'.'.$db->quoteName($params->joins->column1[$joinIdx]).'='.$db->quoteName($params->joins->table2[$joinIdx]).'.'.$db->quoteName($params->joins->column2[$joinIdx]);
												array_push($filterTables, $params->joins->table2[$joinIdx]);
												$applyFilter[$j] = true;
											}
										}
									}
									break;
								}
							}
						}
					}
					//child table reference column filter
					elseif(count($filterField)==4){
						if( isset($params->joins) && isset($params->joins->table2) ){
							if(!in_array($filterField[1], $filterTables)){
								foreach($params->joins->table2 as $ioidx=>$iotable){
									if($iotable==$filterField[1]){
										if($params->joins->table1[$ioidx]==$params->table){
											$query .= ' JOIN '.$db->quoteName($filterField[1])." ON ".$db->quoteName($params->table).".".$db->quoteName($params->joins->column1[$ioidx])."=".$db->quoteName($filterField[1]).".".$db->quoteName($params->joins->column2[$ioidx]);
											$applyFilter[$j] = true;
											array_push($filterTables, $iotable);
										}
										else{
											$tempIdx = $ioidx;
											$left = $params->joins->table1[$ioidx];
											$joinPath = array($ioidx);
											while($tempIdx>=0){
												$tempIdx--;
												if($left==$params->joins->table2[$tempIdx]){
													array_unshift($joinPath, $tempIdx);
													if($params->joins->table1[$tempIdx]==$params->table){
														break;
													}
												}
											}
											foreach($joinPath as $joinIdx){
												if(!in_array($params->joins->table2[$joinIdx], $filterTables)){
													$query .= ' JOIN '.$db->quoteName($params->joins->table2[$joinIdx]).' ON '.$db->quoteName($params->joins->table1[$joinIdx]).'.'.$db->quoteName($params->joins->column1[$joinIdx]).'='.$db->quoteName($params->joins->table2[$joinIdx]).'.'.$db->quoteName($params->joins->column2[$joinIdx]);
													array_push($filterTables, $params->joins->table2[$joinIdx]);
													$applyFilter[$j] = true;
												}
											}
										}
									}
								}
							}
							if(!in_array($filterField[2], $filterTables)){
								$tableIndex = array_search($filterField[1], $params->joins->table2);
								foreach($params->joins->columns[$tableIndex] as $ccol=>$cval){
									if($cval->data=='reference' && $cval->table==$filterField[2]){
										$query .= ' JOIN '.$db->quoteName($filterField[2]).' ON '.$db->quoteName($filterField[1]).'.'.$db->quoteName($ccol).'='.$db->quoteName($filterField[2]).'.'.$db->quoteName($cval->on);
										
										array_push($filterTables, $filterField[2]);
										$applyFilter[$j] = true;
										break;
									}
								}
							}
							if( in_array($filterField[1], $filterTables) && in_array($filterField[2], $filterTables) ){
								$applyFilter[$j] = true;
							}
						}
					}
				}
				
				$query .= " WHERE ";
				$m = $n = -1;
				foreach($params->filters->column as $j=>$column){
					$filterField = explode('.', $column);
					$filterFieldPath = count($filterField);
					//base table reference filter
					if( ($filterFieldPath==2) && ($applyFilter[$j]) ){
						$query .= $db->quoteName($filterField[0]).'.'.$db->quoteName($filterField[$filterFieldPath-1])." ";
					}
					//child table filter
					elseif( ($filterFieldPath==3) && ($applyFilter[$j]) ){
						$query .= $db->quoteName($filterField[1]).'.'.$db->quoteName($filterField[$filterFieldPath-1])." ";
					}
					//child table reference filter
					elseif( ($filterFieldPath==4) && ($applyFilter[$j]) ){
						$query .= $db->quoteName($filterField[2]).'.'.$db->quoteName($filterField[$filterFieldPath-1])." ";
					}
					//base table filters
					else{
						$query .= $db->quoteName($params->table).'.'.$db->quoteName($column)." ";
					}
					
					if($params->filters->cond[$j]=='between' || $params->filters->cond[$j]=='notbetween'){
						$n = ($n<0)?0:$n+1;
					}
					else{
						$m = ($m<0)?0:$m+1;
						$value = $this->getQueryFilteredValue($params->filters->value[$m]);
					}
					
					if($params->filters->cond[$j]=='in'){
						$query .= " IN ( ".$db->quote($value)." )";
					}
					elseif($params->filters->cond[$j]=='notin'){
						// $query .= " NOT IN ( ".$db->quote($value)." )";
						$query .= " NOT IN ( ".$value." )";
					}
					elseif($params->filters->cond[$j]=='between'){
						$value1 = $this->getQueryFilteredValue($params->filters->value1[$n]);
						$value2 = $this->getQueryFilteredValue($params->filters->value2[$n]);
						$query .= " BETWEEN ".$db->quote($value1)." AND ".$db->quote($value2);
					}
					elseif($params->filters->cond[$j]=='notbetween'){
						$value1 = $this->getQueryFilteredValue($params->filters->value1[$n]);
						$value2 = $this->getQueryFilteredValue($params->filters->value2[$n]);
						$query .= " NOT BETWEEN ".$db->quote($value1)." AND ".$db->quote($value2);
					}
					elseif($params->filters->cond[$j]=='like'){
						$query .= " LIKE ".$db->quote($value);
					}
					elseif($params->filters->cond[$j]=='notlike'){
						$query .= " NOT LIKE ".$db->quote($value);
					}
					elseif($params->filters->cond[$j]=='regexp'){
						$query .= " REGEXP ".$db->quote($params->filters->value[$j]);
					}
					else{
						$query .= $params->filters->cond[$j]." ".$db->quote($value);
					}
					
					if($j < (count($params->filters->column)-1)){
						$query .= " ".$params->filters->op." ";
					}
				}
			}
			//group by
			if(!empty($params->groupby)){
				$query .= " GROUP BY ".$db->quoteName($params->table).".".$db->quoteName($params->groupby);
			}
			//order by
			if(!empty($params->orderby)){
				$query .= " ORDER BY ".$db->quoteName($params->table).".".$db->quoteName($params->orderby)." ".$params->orderdir;
			}
			//limit
			$db->setQuery($query, $offset, $limit);
			
			if($type=='ar'){
				$data = $db->loadAssocList();
			}
			else{
				$data = $db->loadObjectList();
			}
			$size = count($data);
			$obj->data = $data;
			
			//count total records
			$size_query = "select count(*) from ".$db->quoteName($params->table);
			$db->setQuery($size_query);
			$size = $db->loadResult();
			$obj->size = $size;
			
			return $obj;
			
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
		
		function batch_export_csv1()
		{
			$obj = new stdClass();
			$db = $this->getDbc();
			$session = JFactory::getSession();
			$expo_fields = JFactory::getApplication()->input->get('field', array(), 'ARRAY');
			$exportitem = $session->get('exportitem');
			
			$offset = JFactory::getApplication()->input->getInt('offset');
			$config = $this->getConfig();
			$delimiter = $this->getDelimiter();
			$enclosure = $this->getEnclosure();
			$child_delimiter = $this->getChildDelimiter();
			$headingStatus = $config->csv_header;
			
			if($exportitem->server=='down')
			{
				$filename = JFilterOutput::stringURLSafe($this->profile->title).'.csv';
				if($offset == 0){
					$filename = JFilterOutput::stringURLSafe($this->profile->title).'.csv';
					$exportitem->write = $filename;
					$session->set('exportitem',$exportitem);
					$mode = 'w';
				}
				else{
					$filename = $exportitem->write;
					$mode = 'a';
				}
				$filepath = JPATH_ROOT.'/components/com_vdata/uploads/'.$filename;
				$output = @fopen($filepath, $mode);
				$session->set('down', $filename);
			}
			elseif($exportitem->server=='write_remote'){
				
				$filename = JFilterOutput::stringURLSafe($this->profile->title).'.csv';
				$filepath = JPATH_ROOT.'/components/com_vdata/uploads/'.$filename;
				if($offset == 0){
					$output = @fopen($filepath, $exportitem->mode);
				}
				else{
					$output = @fopen($filepath, 'a');
				}
			}
			else
			{
				$filepath = $exportitem->path;
				if($exportitem->server=='write_local')
				{
					$filepath = JPATH_ROOT.DS.$exportitem->path;
				}
				
				//validate file extension
				$ext = strrchr($filepath, '.');
				if($ext <> '.csv')	
				{
					$obj->result = 'error';
					$obj->error = JText::_('PLZ_SELECT_CSV_FILE');
					return $obj;
				}
				if($exportitem->mode=='a' && !is_file($filepath))    
				{
					$obj->result = "error";
					$obj->error = JText::_('COULD_NOT_ACCESS');
					return $obj;
				}

				if($exportitem->mode=='a' && !is_writable($filepath))
				{
					$obj->result = "error";
					$obj->error = JText::_('NOT_WRITABLE_FILE');
					return $obj;
				}
				if($offset == 0){
					$output = @fopen($filepath, $exportitem->mode);
				}
				else{
					$output = @fopen($filepath, 'a');
				}
			}
			if($output == FALSE){
				$obj->result = "error";
				$open_error = error_get_last();
				$obj->error = $open_error['message'];
				return $obj;
			}
			
			//retrieve data
			$result = $this->getExportData($db, $this->profile->params, $offset, $config->limit, 'ar');
			$data = $result->data;
			$size  = $result->size;
			
			$n = 0;
			if(count($data)== 0){
				$qty = $session->get('qty', 0);
				if($exportitem->server == 'down' && $qty){
					$obj->dname = $filename;
					$obj->dlink = true;
				}
				else{
					$obj->dlink = false;
				}
				JLog::add(JText::sprintf('EXPORT_RECORD_COUNT', $qty), JLog::INFO, 'com_vdata');
				
				//email export file
				if( ($qty>0) && isset($exportitem->sendfile["status"]) && ($exportitem->sendfile["status"]==1) && isset($exportitem->sendfile["emails"]) && !empty($exportitem->sendfile["emails"]) ){
					$emails = explode(',', $exportitem->sendfile["emails"]);
					$cc = explode(',', $exportitem->sendfile["cc"]);
					$bcc = explode(',', $exportitem->sendfile["bcc"]);
					$subject = $exportitem->sendfile["subject"];
					$body = $exportitem->sendfile["tmpl"];
					$attachment = $filepath;
					$sendfile = $this->sendFile($emails, $cc, $bcc, $subject, $body, $attachment);
				}
				//write file to remote server using ftp
				if( ($qty>0) && ($exportitem->server=='write_remote')){
					if(!$this->uploadToFtp($exportitem->ftp, $filepath)){
						$obj->result = 'error';
						$obj->error = JText::_('VDATA_FTP_FILE_WRITE_ERROR');
						return $obj;
					}
					if(!JFile::delete($filepath)){
						JLog::add(JText::sprintf('VDATA_UNABLE_TO_DELETE_LOCAL_COPY', $filepath), JLog::INFO, 'com_vdata');
					}
				}
				//send notification if done from site part
				if(JFactory::getApplication()->isSite()){
					$notify = $this->sendNotificationEmail( $this->profile->iotype,0 , array('schedule_id'=>"",'schedule_title'=>"","schedule_qry"=>"",'profile_id'=>$this->profile->id,'profile_title'=>$this->profile->title,"count"=>$qty, "format"=>"csv") );
				}
				
				$obj->size = $size;
				$obj->result = 'error';
				$obj->qty = $qty;
				$obj->error = JText::_('NO_RECORD_TO_EXPORT');
				return $obj;
			}
			
			//validate heading fields
			foreach($expo_fields as $head){
				if(empty($head)){
					$obj->result = 'error';
					$obj->error = JText::_('ENTER_HEADING');
					return $obj;
				}
			}
			
			foreach($data as $h=>$row){
				
				$write = array();
				$heading = array();
				foreach($this->profile->params->fields as $column=>$field){
					switch($field->data){
						case 'include' : 
							$heading[] = $expo_fields[$column];
							$write[] = $row[$column];
						break;
						case 'defined' :
							$heading[] = $expo_fields[$column];
							$defined = $field->default;
							$defined = $this->getDefinedValue($field->default, $this->profile->params->fields, $row, $expo_fields);
							$write[] = $defined;
						break;
						case 'reference' :
							$query = "SELECT ".$db->quoteName($field->table.''.$h).".".implode(','.$db->quoteName($field->table.''.$h).".", $field->reftext)." FROM ".$db->quoteName($field->table)." AS ".$db->quoteName($field->table.''.$h)." JOIN ".$db->quoteName($this->profile->params->table)." ON ".$db->quoteName($field->table.''.$h).".".$db->quoteName($field->on)." = ".$db->quoteName($this->profile->params->table).".".$column." WHERE ".$db->quoteName($this->profile->params->table).".".$column." = ".$db->quote($row[$column]);
							$db->setQuery($query);
							$ref_data = $db->loadAssoc();
							$heading = array_merge($heading, array_values($expo_fields[$column]));
							if($ref_data){
								$write = array_merge($write, array_values($ref_data));
							}
							else{
								$write = array_merge($write, array_fill(0, count($expo_fields[$column]), ''));
							}
						break;
					}
				}
				
				if(!empty($this->profile->params->joins->table2)){
					
					$exData = array();
					$exData[$this->profile->params->table][] = $row;
					
					foreach($this->profile->params->joins->table2 as $c=>$child){
						
						if(array_key_exists($child, $expo_fields)){
							
							$response_data = $this->getExportChildData($db, $c, $this->profile->params, $exData,'ar');
							if($response_data->result=='error'){
								$obj->result = "error";
								$obj->error = $response_data->error;
								return $obj;
							}
							$child_data = $response_data->data;
							
							if(!empty($child_data)){
								foreach($child_data as $cd){
									$exData[$child][] = $cd;
								}
							}
							
							foreach($this->profile->params->joins->columns[$c] as $column=>$field){
								$val = array();
								switch($field->data){
									case 'include' :
										$heading[] = $expo_fields[$child][$column];
										foreach($child_data as $cd=>$cdata){
											$val[] = $cdata[$column];
										}
										$value = implode($child_delimiter, $val);
										$write[] = $value;
									break;
									case 'defined' :
										$heading[] = $expo_fields[$child][$column];
										foreach($child_data as $cd=>$cdata){
											$defined = $field->default;
											$defined = $this->getDefinedValue($field->default, $this->profile->params->joins->columns[$c], $cdata, $expo_fields[$child]);
											$val[] = $defined;
										}
										$value = implode($child_delimiter, $val);
										$write[] = $value;
									break;
									case 'reference' :
										$heading = array_merge($heading, array_values($expo_fields[$child][$column]));
										foreach($child_data as $cd=>$cdata){
											$query = "SELECT ".$db->quoteName($field->table.''.$c).".".implode(','.$db->quoteName($field->table.''.$c).".", $field->reftext);
											$query .= " FROM ".$db->quoteName($field->table)." AS ".$db->quoteName($field->table.''.$c);
											$query .= " JOIN ".$db->quoteName($child);
											$query .= " ON ".$db->quoteName($field->table.''.$c).".".$db->quoteName($field->on)." = ";
											$query .= ($child=='#__modules_menu')?'ABS('.$db->quoteName($child).".".$column.')':($db->quoteName($child).".".$column);
											$query .= " WHERE ".$db->quoteName($child).".".$column." = ".$db->quote($cdata[$column]);
											
											$db->setQuery($query);
											$ref_data = $db->loadAssoc();
											
											if($ref_data){
												/* $ref_array_values = array_values($ref_data);
												if($child=='#__modules_menu' && $cdata[$column]<0){
													$resp = array_walk($ref_array_values, function(&$item, $key){
														if(!is_numeric($item)){
															$item = "(-)".$item;
														}
													});
												} */
												// print_r($ref_data);jexit('test');
												foreach($field->reftext as $sep_col){
													$val[$sep_col][] = ($child=='#__modules_menu' && $cdata[$column]<0)?"(-)".$ref_data[$sep_col]:$ref_data[$sep_col];
												}
												// $val = array_merge($val, $ref_array_values);
												
											}
											else{
												foreach($field->reftext as $sep_col){
													$val[$sep_col][] = '';
												}
												// $val = array_merge($val, array_fill(0, count($expo_fields[$child][$column]), ''));
											}
											
										}
										
										if(empty($child_data) && count($expo_fields[$child][$column])>1){
											$write = array_merge($write, array_fill(0, count($expo_fields[$child][$column]), ''));
										}
										else{
											foreach($val as $sep_val){
												$value = implode($child_delimiter, $sep_val);
												$write[] = $value;
											}
											// $value = implode($child_delimiter, $val);
											// $write[] = $value;
										}
									break;
								}

							}
							
						}
					}
				}
				
				if(($offset==0) && ($h==0) && ($exportitem->server=='down' || ($exportitem->mode=='w'))){
					//set heading
					if($headingStatus){
						fputcsv($output, $heading, $delimiter, $enclosure);
					}
					// log info
					JLog::add(JText::sprintf('EXPORT_PROFILE', $this->profile->title), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_TABLE', $this->profile->params->table), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_FORMAT', 'CSV'), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_DELIMITER', $delimiter), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_ENCLOSURE', $enclosure), JLog::INFO, 'com_vdata');
				}
				//write data
				if(fputcsv($output, $write, $delimiter, $enclosure))
					$n++;
			}
			
			$obj->result='success';
			$obj->size = $size;
			$obj->offset = $offset + $config->limit;
			$qty = $session->get('qty',0) + $n;
			$session->set('qty',$qty);
			return $obj;
		}
		
		function getExportChildData($db, $c, $params, $exData, $type='ob'){
			
			$obj = new stdClass();
			
			$data = array();
			if(empty($exData)){
				$obj->result = 'success';
				$obj->data = $data;
				return $obj;
			}
			
			if($params->joins->join[$c]=='left_join'){
				$join = "LEFT JOIN";
			}
			elseif($params->joins->join[$c]=='right_join'){
				$join = "RIGHT JOIN";
			}
			else{
				$join = "JOIN";
			}
			
			$rows = array();
			if(array_key_exists($params->joins->table1[$c], $exData)){
				$rows = $exData[$params->joins->table1[$c]];
			}
			
			if(empty($rows)){
				$obj->result = 'success';
				$obj->data = $data;
				return $obj;
			}
			
			$query = "SELECT ".$params->joins->table2[$c].".* FROM ".$params->joins->table1[$c];
			$query .= " ".$join." ".$params->joins->table2[$c]." ON ".$params->joins->table1[$c].".".$params->joins->column1[$c]." = ".$params->joins->table2[$c].".".$params->joins->column2[$c]."";
			
			foreach($rows as $i=>$row){
				if($params->joins->table1[$c]==$params->table){
					if(is_object($row)){
						if(property_exists($row, $params->joins->column1[$c])){
							$filterVal = $row->{$params->joins->column1[$c]};
						}
					}
					else{
						if(array_key_exists($params->joins->column1[$c], $row)){
							$filterVal = $row[$params->joins->column1[$c]];
						}
					}
					if(isset($filterVal) && !empty($filterVal)){
						if($i==0){
							$query .= " WHERE ".$params->joins->table1[$c].".".$params->joins->column1[$c]."=".$db->quote($filterVal);
						}
						else{
							$query .= " OR ".$params->joins->table1[$c].".".$params->joins->column1[$c]."=".$db->quote($filterVal);
						}
					}
				}
				else{
					if(is_object($row)){
						if(isset($params->joins->column2[$c-1]) && property_exists($row, $params->joins->column2[$c-1])){
							$filterVal = $row->{$params->joins->column2[$c-1]};
						}
					}
					else{
						if(isset($params->joins->column2[$c-1]) && array_key_exists($params->joins->column2[$c-1], $row)){
							$filterVal = $row[$params->joins->column2[$c-1]];
						}
					}
					if(isset($filterVal) && !empty($filterVal)){
						if($i==0){
							$query .= " WHERE ".$params->joins->table1[$c].".".$params->joins->column2[$c-1]."=".$db->quote($filterVal);
						}
						else{
							$query .= " OR ".$params->joins->table1[$c].".".$params->joins->column2[$c-1]."=".$db->quote($filterVal);
						}
					}
					
				}

			}
			try{
				$db->setQuery($query);
				if($type=='ar'){
					$data = $db->loadAssocList();
				}
				else{
					$data = $db->loadObjectList();
				}
			}
			catch(Exception $e){
				$obj->result = 'error';
				$obj->error = $e->getMessage();
				return $obj;
			}
			
			$obj->result = 'success';
			$obj->data = $data;
			return $obj;
			
		}
		
		function batch_export_xml_quick()
		{
			$obj = new stdClass();
			$db = $this->getDbc();
			$session = JFactory::getSession();
			$quick = JFactory::getApplication()->input->getInt('quick', 0);
			$quick_fields = JFactory::getApplication()->input->get('field', array(), 'ARRAY');
			$exportitem = $session->get('exportitem');
			$server = $exportitem->server;
			
			$offset = JFactory::getApplication()->input->getInt('offset');
			$config = $this->getConfig();
			$xmlConfig = json_decode($config->xml_parent);
			$rootNode = !empty($xmlConfig->root)?ltrim(rtrim($xmlConfig->root, '>'), '<'):'ROWSET';
			$rootData = !empty($xmlConfig->data)?ltrim(rtrim($xmlConfig->data, '>'), '<'):'ROW';
			
			$rootNodePath = explode('/', $rootNode);
			$reverseRootNodePath = array_reverse($rootNodePath);
			$replaceTags = '';
			foreach($reverseRootNodePath as $nodeName){
				$replaceTags .= "</".$nodeName.">";
			}
			
			if($exportitem->server=='down')
			{
				if($offset==0){
					$filename = JFilterOutput::stringURLSafe($this->profile->title).'.xml';
					$exportitem->write = $filename;
					$session->set('exportitem',$exportitem);
				}
				else{
					$filename = $exportitem->write;
				}
				$filepath = JPATH_ROOT.'/components/com_vdata/uploads/'.$filename;
				$session->set('down', $filename);
			}
			elseif($exportitem->server=='write_remote'){
				$filename = JFilterOutput::stringURLSafe($this->profile->title).'.xml';
				$filepath = JPATH_ROOT.'/components/com_vdata/uploads/'.$filename;
			}
			else
			{
				$filepath = $exportitem->path;
				if($exportitem->server=='write_local')
				{
					$filepath = JPATH_ROOT.'/'.$exportitem->path;
				}
				//validate file extension
				$ext = strrchr($filepath, '.');
				if($ext <> '.xml')	
				{
					$obj->result = 'error';
					$obj->error = JText::_('PLZ_SELECT_XML_FILE');
					return $obj;
				}
				if($exportitem->mode=='a' && !is_file($filepath))    
				{
					$obj->result = "error";
					$obj->error = JText::_('COULD_NOT_ACCESS');
					return $obj;
				}

				if($exportitem->mode=='a' && !is_writable($filepath))
				{
					$obj->result = "error";
					$obj->error = JText::_('NOT_WRITABLE_FILE');
					return $obj;
				}
			}
			$query = "select * from ".$db->quoteName($this->profile->params->table);
			$db->setQuery($query, $offset, $config->limit);
			$dataset = $db->loadObjectList();
			
			$size_query = "select count(*) from ".$db->quoteName($this->profile->params->table);
			$db->setQuery($size_query);
			$size = $db->loadResult();
			
			$n=0;
			if(count($dataset)==0)	{
				$qty = $session->get('qty',0);
				if($exportitem->server == 'down' && $qty){
					$obj->dname = $filename;
					$obj->dlink = true;
				}
				else{
					$obj->dlink = false;
				}
				//email export file
				if( ($qty>0) && isset($exportitem->sendfile["status"]) && ($exportitem->sendfile["status"]==1) && isset($exportitem->sendfile["emails"]) && !empty($exportitem->sendfile["emails"]) ){
					$emails = explode(',', $exportitem->sendfile["emails"]);
					$cc = explode(',', $exportitem->sendfile["cc"]);
					$bcc = explode(',', $exportitem->sendfile["bcc"]);
					$subject = $exportitem->sendfile["subject"];
					$body = $exportitem->sendfile["tmpl"];
					$attachment = $filepath;
					$sendfile = $this->sendFile($emails, $cc, $bcc, $subject, $body, $attachment);
				}
				//write file to remote server using ftp
				if( ($qty>0) && ($exportitem->server=='write_remote')){
					if(!$this->uploadToFtp($exportitem->ftp, $filepath)){
						$obj->result = 'error';
						$obj->error = JText::_('VDATA_FTP_FILE_WRITE_ERROR');
						return $obj;
					}
					if(!JFile::delete($filepath)){
						JLog::add(JText::sprintf('VDATA_UNABLE_TO_DELETE_LOCAL_COPY', $filepath), JLog::INFO, 'com_vdata');
					}
				}
				//send notification if done from site part
				if(JFactory::getApplication()->isSite()){
					$notify = $this->sendNotificationEmail( $this->profile->iotype,0 , array('schedule_id'=>"",'schedule_title'=>"","schedule_qry"=>"",'profile_id'=>$this->profile->id,'profile_title'=>$this->profile->title,"count"=>$qty, "format"=>"xml") );
				}
				
				JLog::add(JText::sprintf('EXPORT_PROFILE', $this->profile->title), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('EXPORT_TABLE', $this->profile->params->table), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('EXPORT_FORMAT', 'XML'), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('EXPORT_RECORD_COUNT', $qty), JLog::INFO, 'com_vdata');
				$obj->result = 'error';
				$obj->size = $size;
				$obj->qty = $qty;
				$obj->error = JText::_('NO_RECORD_TO_EXPORT');
				return $obj;
			}
			
			$xmlWriter = new XMLWriter();
			$xmlWriter->openMemory();
			if($exportitem->server=='down'){
				if($offset==0){
					$xmlWriter->setIndent(true);
					$xmlWriter->startDocument('1.0','UTF-8');
					// $xmlWriter->startElement($rootNode);
					foreach($rootNodePath as $pathNode){
						$xmlWriter->startElement($pathNode);
					}
					
				}
				else{
					$xmlWriter->setIndent(true);
				}
			}
			else
			{
				if( ($exportitem->mode=='w') && ($offset==0) ){
					$xmlWriter->setIndent(true);
					$xmlWriter->startDocument('1.0','UTF-8');
					// $xmlWriter->startElement($rootNode);
					foreach($rootNodePath as $pathNode){
						$xmlWriter->startElement($pathNode);
					}
					
				}
				else{
					$xmlWriter->setIndent(true);
				}
			}
			
			foreach($dataset as $i=>$data)
			{
				if(!$quick){
					$row = new stdClass();
					foreach($quick_fields as $key=>$quick_field){
						$row->{$quick_field} = $data->{$key};
					}
				}
				else{
					$row = $data;
				}
				
				$xmlWriter->startElement($rootData);
				foreach($row as $col=>$val){
					//start node path elements
					$nodeName = $col;
					$parentElements = array();
					$nodePath = explode('/', $nodeName);
					if(count($nodePath)>1){
						foreach($nodePath as $node){
							if(!empty($node)){
								array_push($parentElements, $node);
							}
						}
						if(count($parentElements)>1){
							for($elm=0;$elm<(count($parentElements)-1);$elm++){
								$xmlWriter->startElement($parentElements[$elm]);
							}
							$nodeName = $parentElements[count($parentElements)-1];
						}
					}
					
					//write node data
					if($xmlConfig->node=='other'){
						$xmlWriter->startElement($xmlConfig->name);
							$xmlWriter->writeAttribute($xmlConfig->attribute, $nodeName);
						$xmlWriter->text($val);
						$xmlWriter->endElement();
					}
					else{
						$xmlWriter->writeElement($nodeName, $val);
					}
					
					//close node path elements
					for($elm=1;$elm<count($parentElements);$elm++){
						$xmlWriter->endElement();
					}
				}
				$xmlWriter->endElement();

				if($exportitem->server=='down'){
					if($offset==0){
						if($i==0){
							$fp = @fopen($filepath, 'w');
							$flag = fwrite($fp, $xmlWriter->flush(true));
						}
						else{
							$fp = @fopen($filepath, 'r+');
							fseek($fp, 0,SEEK_END);
							$flag = fwrite($fp, $xmlWriter->flush(true));
						}
					}
					else{
						$fp = @fopen($filepath, 'r+');
						if($i==0){
							fseek($fp,(filesize($filepath)-strlen($replaceTags.PHP_EOL)));
							$flag = fwrite($fp, $xmlWriter->flush(true));
						}
						else{
							fseek($fp, 0,SEEK_END);
							$flag = fwrite($fp, $xmlWriter->flush(true));
						}
					}
				}
				else{
					if($exportitem->mode=='w' && $offset==0){
						if($i==0){
							$fp = @fopen($filepath, $exportitem->mode);
							$flag = fwrite($fp, $xmlWriter->flush(true));
						}
						else{
							$fp = @fopen($filepath, 'r+');
							fseek($fp, 0,SEEK_END);
							$flag = fwrite($fp, $xmlWriter->flush(true));
						}
						
					}
					else{
						$fp = @fopen($filepath, 'r+');
						if($i==0){
							fseek($fp,(filesize($filepath)-strlen($replaceTags.PHP_EOL)));
							$flag = fwrite($fp, $xmlWriter->flush(true));
						}
						else{
							fseek($fp, 0,SEEK_END);
							$flag = fwrite($fp, $xmlWriter->flush(true));
						}
					}
				}
				if($flag !== FALSE)
					$n++;
			}
			$close = file_put_contents($filepath, $replaceTags.PHP_EOL, FILE_APPEND);
			$xmlWriter->endDocument();
			
			$obj->result = 'success';
			$obj->size = $size;
			$obj->offset = $offset + $config->limit;
			$qty = $session->get('qty',0) + $n;
			$session->set('qty',$qty);
			return $obj;	
		}
		
		function batch_export_xml()
		{
			$obj = new stdClass();
			$db = $this->getDbc();
			$dbPrefix = $db->getPrefix();
			$session = JFactory::getSession();
			$expo_fields = JFactory::getApplication()->input->get('field', array(), 'ARRAY');
			$exportitem = $session->get('exportitem');
			$server = $exportitem->server;
			
			$offset = JFactory::getApplication()->input->getInt('offset');
			$config = $this->getConfig();
			$xmlConfig = json_decode($config->xml_parent);
			$rootNode = !empty($xmlConfig->root)?ltrim(rtrim($xmlConfig->root, '>'), '<'):'ROWSET';
			$rootData = !empty($xmlConfig->data)?ltrim(rtrim($xmlConfig->data, '>'), '<'):'ROW';
			$rootDataChild = !empty($xmlConfig->child)?ltrim(rtrim($xmlConfig->child, '>'), '<'):'CHILD';
			
			$rootNodePath = explode('/', $rootNode);
			$reverseRootNodePath = array_reverse($rootNodePath);
			$replaceTags = '';
			foreach($reverseRootNodePath as $nodeName){
				$replaceTags .= "</".$nodeName.">";
			}
			
			if($exportitem->server=='down')
			{
				if($offset==0){
					$filename = JFilterOutput::stringURLSafe($this->profile->title).'.xml';
					$exportitem->write = $filename;
					$session->set('exportitem',$exportitem);
				}
				else{
					$filename = $exportitem->write;
				}
				$filepath = JPATH_ROOT.'/components/com_vdata/uploads/'.$filename;
				$session->set('down', $filename);
			}
			elseif($exportitem->server=='write_remote'){
				$filename = JFilterOutput::stringURLSafe($this->profile->title).'.xml';
				$filepath = JPATH_ROOT.'/components/com_vdata/uploads/'.$filename;
			}
			else
			{	
				$filepath = $exportitem->path;
				if($exportitem->server=='write_local')
				{
					$filepath = JPATH_ROOT.'/'.$exportitem->path;
				}
				//validate file extension
				$ext = strrchr($filepath, '.');
				if($ext <> '.xml')	
				{
					$obj->result = 'error';
					$obj->error = JText::_('PLZ_SELECT_XML_FILE');
					return $obj;
				}
				if($exportitem->mode=='a' && !is_file($filepath))    
				{
					$obj->result = "error";
					$obj->error = JText::_('COULD_NOT_ACCESS');
					return $obj;
				}

				if($exportitem->mode=='a' && !is_writable($filepath))
				{
					$obj->result = "error";
					$obj->error = JText::_('NOT_WRITABLE_FILE');
					return $obj;
				}
			}
			
			//retrieve data
			$result = $this->getExportData($db, $this->profile->params, $offset, $config->limit, 'ob');
			$data = $result->data;
			$size  = $result->size;
			
			$n=0;
			if(count($data)==0)	{
				$qty = $session->get('qty',0);
				if($exportitem->server == 'down' && $qty){
					$obj->dname = $filename;
					$obj->dlink = true;
				}
				else{
					$obj->dlink = false;
				}
				//email export file
				if( ($qty>0) && isset($exportitem->sendfile["status"]) && ($exportitem->sendfile["status"]==1) && isset($exportitem->sendfile["emails"]) && !empty($exportitem->sendfile["emails"]) ){
					$emails = explode(',', $exportitem->sendfile["emails"]);
					$cc = explode(',', $exportitem->sendfile["cc"]);
					$bcc = explode(',', $exportitem->sendfile["bcc"]);
					$subject = $exportitem->sendfile["subject"];
					$body = $exportitem->sendfile["tmpl"];
					$attachment = $filepath;
					$sendfile = $this->sendFile($emails, $cc, $bcc, $subject, $body, $attachment);
				}
				//write file to remote server using ftp
				if( ($qty>0) && ($exportitem->server=='write_remote')){
					if(!$this->uploadToFtp($exportitem->ftp, $filepath)){
						$obj->result = 'error';
						$obj->error = JText::_('VDATA_FTP_FILE_WRITE_ERROR');
						return $obj;
					}
					if(!JFile::delete($filepath)){
						JLog::add(JText::sprintf('VDATA_UNABLE_TO_DELETE_LOCAL_COPY', $filepath), JLog::INFO, 'com_vdata');
					}
				}
				//send notification if done from site part
				if(JFactory::getApplication()->isSite()){
					$notify = $this->sendNotificationEmail( $this->profile->iotype,0 , array('schedule_id'=>"",'schedule_title'=>"","schedule_qry"=>"",'profile_id'=>$this->profile->id,'profile_title'=>$this->profile->title,"count"=>$qty, "format"=>"xml") );
				}
				
				JLog::add(JText::sprintf('EXPORT_PROFILE', $this->profile->title), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('EXPORT_TABLE', $this->profile->params->table), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('EXPORT_FORMAT', 'XML'), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('EXPORT_RECORD_COUNT', $qty), JLog::INFO, 'com_vdata');
				$obj->result = 'error';
				$obj->size = $size;
				$obj->qty = $qty;
				$obj->error = JText::_('NO_RECORD_TO_EXPORT');
				return $obj;
			}
			
			$xmlWriter = new XMLWriter();
			$xmlWriter->openMemory();
			if($exportitem->server=='down'){
				if($offset==0){
					$xmlWriter->setIndent(true);
					$xmlWriter->startDocument('1.0','UTF-8');
					// $xmlWriter->startElement($rootNode);
					foreach($rootNodePath as $pathNode){
						$xmlWriter->startElement($pathNode);
					}
				}
				else{
					$xmlWriter->setIndent(true);
				}
			}
			else{
				if($exportitem->mode=='w' && $offset==0){
					$xmlWriter->setIndent(true);
					$xmlWriter->startDocument('1.0','UTF-8');
					// $xmlWriter->startElement($rootNode);
					foreach($rootNodePath as $pathNode){
						$xmlWriter->startElement($pathNode);
					}
				}
				else{
					$xmlWriter->setIndent(true);
				}
			}
			
			//write data
			foreach($data as $h=>$row)
			{
				$xmlWriter->startElement($rootData);
				foreach($this->profile->params->fields as $column=>$field){
					switch($field->data){
						case 'include' :
							//start node path elements
							$nodeName = $expo_fields[$column];
							$parentElements = array();
							$nodePath = explode('/', $nodeName);
							if(count($nodePath)>1){
								foreach($nodePath as $node){
									if(!empty($node)){
										array_push($parentElements, $node);
									}
								}
								if(count($parentElements)>1){
									for($elm=0;$elm<(count($parentElements)-1);$elm++){
										$xmlWriter->startElement($parentElements[$elm]);
									}
									$nodeName = $parentElements[count($parentElements)-1];
								}
							}
							
							//write node data
							if($xmlConfig->node=='other'){
								$xmlWriter->startElement($xmlConfig->name);
									$xmlWriter->writeAttribute($xmlConfig->attribute, $nodeName);
								$xmlWriter->text($row->{$column});
								$xmlWriter->endElement();
							}
							else{
								$xmlWriter->writeElement($nodeName, $row->{$column});
							}
							
							//close node path elements
							for($elm=1;$elm<count($parentElements);$elm++){
								$xmlWriter->endElement();
							}
							
						break;
						case 'defined' :
							$defined = $field->default;
							$defined = $this->getDefinedValue($field->default, $this->profile->params->fields, (array)$row, $expo_fields);
							if($xmlConfig->node=='other'){
								$xmlWriter->startElement($xmlConfig->name);
									$xmlWriter->writeAttribute($xmlConfig->attribute, $expo_fields[$column]);
								$xmlWriter->text($defined);
								$xmlWriter->endElement();
							}
							else{
								$xmlWriter->writeElement($expo_fields[$column], $defined);
							}
						break;
						case 'reference' :
							$query = "SELECT ".$db->quoteName($field->table.''.$h).".".implode(','.$db->quoteName($field->table.''.$h).".", $field->reftext)." FROM ".$db->quoteName($field->table)."  AS ".$db->quoteName($field->table.''.$h)." JOIN ".$db->quoteName($this->profile->params->table)." ON ".$db->quoteName($field->table.''.$h).".".$db->quoteName($field->on)." = ".$db->quoteName($this->profile->params->table).".".$column." WHERE ".$db->quoteName($this->profile->params->table).".".$column." = ".$db->quote($row->{$column});
							$db->setQuery($query);
							$ref_data = $db->loadObject();
							if($ref_data){
								foreach($ref_data as $rkey=>$rval){
									//start node path elements
									$nodeName = $expo_fields[$column][$rkey];
									$parentElements = array();
									$nodePath = explode('/', $nodeName);
									if(count($nodePath)>1){
										foreach($nodePath as $node){
											if(!empty($node)){
												array_push($parentElements, $node);
											}
										}
										if(count($parentElements)>1){
											for($elm=0;$elm<(count($parentElements)-1);$elm++){
												$xmlWriter->startElement($parentElements[$elm]);
											}
											$nodeName = $parentElements[count($parentElements)-1];
										}
									}
									//write node data
									if($xmlConfig->node=='other'){
										$xmlWriter->startElement($xmlConfig->name);
											$xmlWriter->writeAttribute($xmlConfig->attribute, str_replace('#__', $dbPrefix, $nodeName));
										$xmlWriter->text($rval);
										$xmlWriter->endElement();
									}
									else{
										$xmlWriter->writeElement(str_replace('#__', $dbPrefix, $nodeName), $rval);
									}
									//close node path elements
									for($elm=1;$elm<count($parentElements);$elm++){
										$xmlWriter->endElement();
									}
								}
							}
							
						break;
					}
				}
				
				//write child table record
				if(!empty($this->profile->params->joins->table2)){
					
					$exData = array();
					$exData[$this->profile->params->table][] = $row;
					
					foreach($this->profile->params->joins->table2 as $c=>$child){
						$child_root_tag = !empty($expo_fields[$child]["root_tag"])?$expo_fields[$child]["root_tag"]:str_replace('#__', $dbPrefix, $child);
						$xmlWriter->startElement( $child_root_tag );
						if(array_key_exists($child, $expo_fields)){
							
							$response_data = $this->getExportChildData($db, $c, $this->profile->params, $exData,'ob');
							if($response_data->result=='error'){
								$obj->result = "error";
								$obj->error = $response_data->error;
								return $obj;
							}
							$child_data = $response_data->data;
							
							if(!empty($child_data)){
								foreach($child_data as $cd){
									$exData[$child][] = $cd;
								}
							}

							foreach($child_data as $cd=>$cdata){
								$xmlWriter->startElement($rootDataChild);
								foreach($this->profile->params->joins->columns[$c] as $column=>$field){
									switch($field->data){
										case 'include' :
											//start node path elements
											$nodeName = $expo_fields[$child][$column];
											$parentElements = array();
											$nodePath = explode('/', $nodeName);
											if(count($nodePath)>1){
												foreach($nodePath as $node){
													if(!empty($node)){
														array_push($parentElements, $node);
													}
												}
												if(count($parentElements)>1){
													for($elm=0;$elm<(count($parentElements)-1);$elm++){
														$xmlWriter->startElement($parentElements[$elm]);
													}
													$nodeName = $parentElements[count($parentElements)-1];
												}
											}
											//write node data
											if($xmlConfig->node=='other'){
												$xmlWriter->startElement($xmlConfig->name);
													$xmlWriter->writeAttribute($xmlConfig->attribute, $nodeName);
												$xmlWriter->text($cdata->{$column});
												$xmlWriter->endElement();
											}
											else{
												$xmlWriter->writeElement($nodeName, $cdata->{$column});
											}
											//close node path elements
											for($elm=1;$elm<count($parentElements);$elm++){
												$xmlWriter->endElement();
											}
										break;
										case 'defined' :
											$defined = $field->default;
											$defined = $this->getDefinedValue($field->default, $this->profile->params->joins->columns[$c], $cdata, $expo_fields[$child]);
											if($xmlConfig->node=='other'){
												$xmlWriter->startElement($xmlConfig->name);
													$xmlWriter->writeAttribute($xmlConfig->attribute, $expo_fields[$child][$column]);
												$xmlWriter->text($defined);
												$xmlWriter->endElement();
											}
											else{
												$xmlWriter->writeElement($expo_fields[$child][$column], $defined);
											}
										break;
										case 'reference' :
											$query = "SELECT ".$db->quoteName($field->table.''.$c).".".implode(','.$db->quoteName($field->table.''.$c).".", $field->reftext);
											$query .= " FROM ".$db->quoteName($field->table)." AS ".$db->quoteName($field->table.''.$c);
											$query .= " JOIN ".$db->quoteName($child);
											$query .= " ON ".$db->quoteName($field->table.''.$c).".".$db->quoteName($field->on)." = ";
											$query .= ($child=='#__modules_menu')?'ABS('.$db->quoteName($child).".".$column.')':($db->quoteName($child).".".$column);
											$query .= " WHERE ".$db->quoteName($child).".".$column." = ".$db->quote($cdata->{$column});
											
											$db->setQuery($query);
											$ref_data = $db->loadAssoc();
											
											
											if($ref_data){
												foreach($ref_data as $rkey=>$rval){
													$valTest = ($child=='#__modules_menu' && $cdata->{$column}<0)?"(-)".$rval:$rval;
													//start node path elements
													$nodeName = $expo_fields[$child][$column][$rkey];
													$parentElements = array();
													$nodePath = explode('/', $nodeName);
													if(count($nodePath)>1){
														foreach($nodePath as $node){
															if(!empty($node)){
																array_push($parentElements, $node);
															}
														}
														if(count($parentElements)>1){
															for($elm=0;$elm<(count($parentElements)-1);$elm++){
																$xmlWriter->startElement($parentElements[$elm]);
															}
															$nodeName = $parentElements[count($parentElements)-1];
														}
													}
													//write node data
													if($xmlConfig->node=='other'){
														$xmlWriter->startElement($xmlConfig->name);
															$xmlWriter->writeAttribute($xmlConfig->attribute, $nodeName);
														$xmlWriter->text($valTest);
														$xmlWriter->endElement();
													}
													else{
														$xmlWriter->writeElement($nodeName, $valTest);
													}
													//close node path elements
													for($elm=1;$elm<count($parentElements);$elm++){
														$xmlWriter->endElement();
													}
												}
											}
											else{
												foreach($field->reftext as $rkey=>$rval){
													//start node path elements
													$nodeName = $expo_fields[$child][$column][$rval];
													$parentElements = array();
													$nodePath = explode('/', $nodeName);
													if(count($nodePath)>1){
														foreach($nodePath as $node){
															if(!empty($node)){
																array_push($parentElements, $node);
															}
														}
														if(count($parentElements)>1){
															for($elm=0;$elm<(count($parentElements)-1);$elm++){
																$xmlWriter->startElement($parentElements[$elm]);
															}
															$nodeName = $parentElements[count($parentElements)-1];
														}
													}
													//write node data
													if($xmlConfig->node=='other'){
														$xmlWriter->startElement($xmlConfig->name);
															$xmlWriter->writeAttribute($xmlConfig->attribute, $nodeName);
														$xmlWriter->text('');
														$xmlWriter->endElement();
													}
													else{
														$xmlWriter->writeElement($nodeName, '');
													}
													//close node path elements
													for($elm=1;$elm<count($parentElements);$elm++){
														$xmlWriter->endElement();
													}
												}
											}
										break;
									}
								}
								$xmlWriter->endElement();
							}
						}
						$xmlWriter->endElement();
					}
				}
				$xmlWriter->endElement();
		
				if($exportitem->server=='down'){
					if($offset==0){
						if($h==0){
							$fp = @fopen($filepath, 'w');
							$flag = fwrite($fp, $xmlWriter->flush(true));
						}
						else{
							$fp = @fopen($filepath, 'r+');
							fseek($fp, 0,SEEK_END);
							$flag = fwrite($fp, $xmlWriter->flush(true));
						}
					}	
					else{
						$fp = @fopen($filepath, 'r+');
						if($h==0){
							fseek($fp,(filesize($filepath)-strlen($replaceTags.PHP_EOL)));
							$flag = fwrite($fp, $xmlWriter->flush(true));
						}
						else{
							fseek($fp, 0,SEEK_END);
							$flag = fwrite($fp, $xmlWriter->flush(true));
						}
					}		
				}
				else{
					if( ($exportitem->mode=='w') && ($offset==0)){
						if($h==0){
							$fp = @fopen($filepath, $exportitem->mode);
							$flag = fwrite($fp, $xmlWriter->flush(true));
						}
						else{
							$fp = @fopen($filepath, 'r+');
							fseek($fp, 0,SEEK_END);
							$flag = fwrite($fp, $xmlWriter->flush(true));
						}
						
					}
					else{
						$fp = @fopen($filepath, 'r+');
						if($h==0){
							fseek($fp,(filesize($filepath)-strlen($replaceTags.PHP_EOL)));
							$flag = fwrite($fp, $xmlWriter->flush(true));
						}
						else{
							fseek($fp, 0,SEEK_END);
							$flag = fwrite($fp, $xmlWriter->flush(true));
						}
					}
				}
				if($flag !== FALSE)
					$n++;	
				
			}
			$close = file_put_contents($filepath, $replaceTags.PHP_EOL, FILE_APPEND);
			$xmlWriter->endDocument();
			
			$obj->result = 'success';
			$obj->size = $size;
			$obj->offset = $offset + $config->limit;
			$qty = $session->get('qty',0) + $n;
			$session->set('qty',$qty);
			return $obj;
		}
		
		
		function batch_export_remote_quick()
		{
			$obj = new stdClass();
			$local_db = $this->getDbc();
			$query_local = $local_db->getQuery(true);
			$session = JFactory::getSession();
			try
			{
				$option = $session->get('remote_details');
				$remote_db = JDatabaseDriver::getInstance( $option );
				$remote_db->connect();
				$query_remote = $remote_db->getQuery(true);
			}
			catch (RuntimeException $e) 
			{
				$obj->result = 'error';
				$obj->error = $e.getMessage();
				return $obj;
			}
			
			$dids = $session->get('dids', array());
			
			$quick = JFactory::getApplication()->input->getInt('quick', 0);
			$quick_fields = JFactory::getApplication()->input->get('fields', array(), 'ARRAY');
			$remote_table = JFactory::getApplication()->input->get('table', '', 'RAW');
			$unkey = JFactory::getApplication()->input->get('unkey','', 'STRING');
			
			$operation = $session->get('expo_op', 1);
			
			$offset = JFactory::getApplication()->input->getInt('offset');
			$config = $this->getConfig();
			
			$query_remote = 'SHOW KEYS FROM '.$remote_db->quoteName($remote_table).' WHERE Key_name = "PRIMARY"';
			$remote_db->setQuery($query_remote);
			$key = $remote_db->loadObjectList();
			if(!empty($unkey))
				$primary = $unkey;
			elseif( !empty($key) && (count($key)==1) )
				$primary = $key[0]->Column_name;
			else
				$primary = null;
			
			$size_query = $local_db->getQuery(true);
			
			if($quick)
			{
				$query_local = "select * from ".$local_db->quoteName($this->profile->params->table);
				$local_db->setQuery($query_local, $offset, $config->limit);
				$results = $local_db->loadObjectList();
				
				$size_query = "select count(*) from ".$local_db->quoteName($this->profile->params->table);
				$local_db->setQuery($size_query);
				$size = $local_db->loadResult();
				
				
				$n = 0;
				if(count($results)==0)	{
					//log info
					JLog::add(JText::sprintf('EXPORT_PROFILE', $this->profile->title), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_TABLE', $this->profile->params->table), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_FORMAT', 'DATABASE'), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_RECORD_COUNT', $session->get('qty',0)), JLog::INFO, 'com_vdata');
					
					if( ($operation==2) && !empty($dids) && !empty($primary) ){
						$query_remote = 'delete from '.$remote_db->quoteName($this->profile->params->table). ' where 1=1';
						foreach($dids as $did){
							$query_remote .= ' and '.$remote_db->quoteName($primary).' <> '.$remote_db->quote($did);
						}
						$remote_db->setQuery($query_remote);
						$res = $remote_db->execute();
					}
					$session->clear('dids');
					//send notification if done from site part
					if(JFactory::getApplication()->isSite()){
						$notify = $this->sendNotificationEmail( $this->profile->iotype,0 , array('schedule_id'=>"",'schedule_title'=>"","schedule_qry"=>"",'profile_id'=>$this->profile->id,'profile_title'=>$this->profile->title,"count"=>$qty, "format"=>"db") );
					}
				
					$obj->result='error';
					$obj->size = $size;
					$obj->qty = $session->get('qty',0);
					$obj->error = JText::_('NO_RECORD_TO_EXPORT');
					return $obj;
				}
				
				foreach($results as $result)
				{
					$insert = new stdClass();
					foreach($result as $key=>$value)
						$insert->{$key} = $value;
					$isNew = true;
					if(!empty($primary) && !empty($insert->{$primary}))
					{
						$query = "select count(*) from ".$remote_db->quoteName($remote_table)." where ".$remote_db->quoteName($primary)." = ".$remote_db->quote($insert->{$primary});
						$remote_db->setQuery($query);
						$count = $remote_db->loadResult();
						$isNew = $count > 0 ? false : true;	
					}
					if($operation==1)	
					{
						if(!$isNew){
							JLog::add(JText::sprintf('RECORD_EXISTS',$primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
							continue;
						}
						if(!$remote_db->insertObject($remote_table, $insert)){
							$obj->result = 'error';
							$obj->error = $remote_db->stderr();
							return $obj;
						}
						if($this->getAffected($remote_db)> 0)
							$n++;
					}
					else	
					{
						if(empty($primary)  || empty($insert->{$primary})){
							$obj->result = 'error';
							$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
							return $obj;
						}
						
						$dids[] = $insert->{$primary};
						
						if($isNew){
							JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
							if(!$remote_db->insertObject($remote_table, $insert)){
								$obj->result = 'error';
								$obj->error = $remote_db->stderr();
								return $obj;
							}
							if($this->getAffected($remote_db)> 0)
								$n++;
						}
						else{
							if(!$remote_db->updateObject($remote_table, $insert, $primary)){
								$obj->result = 'error';
								$obj->error = $remote_db->stderr();
								return $obj;
							}
							if($this->getAffected($remote_db)> 0)
								$n++;
						}
					}
				}
			}
			else
			{
				if(!count($quick_fields)){
					$obj->result = "error";
					$obj->error = JText::_('SELECT_FIELDS_TO_EXPORT');
					return $obj;
				}
				$query_local = "select * from ".$local_db->quoteName($this->profile->params->table);
				$local_db->setQuery($query_local, $offset, $config->limit);
				$results = $local_db->loadObjectList();
				
				$size_query = "select count(*) from ".$local_db->quoteName($this->profile->params->table);
				$local_db->setQuery($size_query);
				$size = $local_db->loadResult();
				
				if(count($results)==0){
					//log info
					JLog::add(JText::sprintf('EXPORT_PROFILE', $this->profile->title), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_TABLE', $this->profile->params->table), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_FORMAT', 'DATABASE'), JLog::INFO, 'com_vdata');
					$eop = ($operation) ? JText::_('INSERT') : JText::_('UPDATE');
					JLog::add(JText::sprintf('EXPORT_OPERATION', $eop), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_RECORD_COUNT', $session->get('qty',0)), JLog::INFO, 'com_vdata');
					
					if( ($operation==2) && !empty($dids) && !empty($primary) ){
						$query_remote = 'delete from '.$remote_db->quoteName($this->profile->params->table). ' where 1=1';
						foreach($dids as $did){
							$query_remote .= ' and '.$remote_db->quoteName($primary).' <> '.$remote_db->quote($did);
						}
						$remote_db->setQuery($query_remote);
						$res = $remote_db->execute();
					}
					$session->clear('dids');

					$obj->result='error';
					$obj->size = $size;
					$obj->qty = $session->get('qty',0);
					$obj->error = JText::_('NO_RECORD_TO_EXPORT');
					return $obj;
				}
				
				$n = 0;
				foreach($results as $result)
				{
					$insert = new stdClass();
					
					foreach($result as $key=>$value){
						if(!empty($quick_fields[$key])){
							$insert->{$key} = $result->{$quick_fields[$key]};
						}
					}
					
					$isNew = true;
					if(!empty($primary) && !empty($insert->{$primary}))
					{
						$query = "select count(*) from ".$remote_db->quoteName($remote_table)." where ".$remote_db->quoteName($primary)." = ".$remote_db->quote($insert->{$primary});
						$remote_db->setQuery($query);
						$count = $remote_db->loadResult();
						$isNew = $count > 0 ? false : true;	
					}
					if($operation==1)	
					{
						if(!$isNew){
							JLog::add(JText::sprintf('RECORD_EXISTS',$primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
							continue;
						}
						if(!$remote_db->insertObject($remote_table, $insert))	
						{
							$obj->result = 'error';
							$obj->error = $remote_db->stderr();
							return $obj;
						}
						if($this->getAffected($remote_db)> 0)
							$n++;
					}
					else	
					{
						if(empty($primary) || empty($insert->{$primary})){
							$obj->result = 'error';
							$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
							return $obj;
						}
						
						$dids[] = $insert->{$primary};
						
						if($isNew){
							JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
							if(!$remote_db->insertObject($remote_table, $insert))	
							{
								$obj->result = 'error';
								$obj->error = $remote_db->stderr();
								return $obj;
							}
							if($this->getAffected($remote_db)> 0)
								$n++;
						}
						else{
							if(!$remote_db->updateObject($remote_table, $insert, $primary)){
								$obj->result = 'error';
								$obj->error = $remote_db->stderr();
								return $obj;
							}
							if($this->getAffected($remote_db)> 0)
								$n++;
						}
					}
				}
			}
			
			$session->set('dids', $dids);
			
			$obj->result = 'success';
			$obj->size = $size;
			$obj->offset = $offset + $config->limit;
			$qty = $session->get('qty',0) + $n;
			$session->set('qty',$qty);
			return $obj;
			
		}
		
		function batch_export_remote()
		{
			$obj = new stdClass();
			$session = JFactory::getSession();
			$local_db = $this->getDbc();
			$query_local = $local_db->getQuery(true);
			try
			{
				$option = $session->get('remote_details');
				$remote_db = JDatabaseDriver::getInstance( $option );
				$remote_db->connect();
				$query_remote = $remote_db->getQuery(true);
			}
			catch (RuntimeException $e) 
			{
				$obj->result = 'error';
				$obj->error = $e.getMessage();
				return $obj;
			}
			
			$remote_fields = JFactory::getApplication()->input->get('fields', array(), 'ARRAY');
			$remote_table = JFactory::getApplication()->input->get('table', '', 'RAW');
			$join_table = JFactory::getApplication()->input->get('join_table', '', 'RAW');
			$unkey = JFactory::getApplication()->input->get('unkey', '', 'STRING');
			
			$operation = $session->get('expo_op', 1);
			
			$dids = $session->get('dids', array());
			
			$offset = JFactory::getApplication()->input->getInt('offset');
			$config = $this->getConfig();
			
			$query_remote = 'SHOW KEYS FROM '.$remote_db->quoteName($remote_table).' WHERE Key_name = "PRIMARY"';
			$remote_db->setQuery( $query_remote );
			$key = $remote_db->loadObjectList();
			if(!empty($unkey)){
				$primary = $unkey;
			}
			elseif( !empty($key) && (count($key)==1) )
			{
				$primary = $key[0]->Column_name;
			}
			else
			{
				$primary = null;
			}
			
			$result = $this->getExportData($local_db, $this->profile->params, $offset, $config->limit, 'ob');
			$local_data_set = $result->data;
			$size  = $result->size;
			
			if(count($local_data_set) == 0)	{
				//log info
				JLog::add(JText::sprintf('EXPORT_PROFILE', $this->profile->title), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('EXPORT_TABLE', $this->profile->params->table), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('EXPORT_FORMAT', 'DATABASE'), JLog::INFO, 'com_vdata');
				$eop = ($operation) ? JText::_('INSERT') : JText::_('UPDATE');
				JLog::add(JText::sprintf('EXPORT_OPERATION', $eop), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('EXPORT_RECORD_COUNT', $session->get('qty',0)), JLog::INFO, 'com_vdata');
				
				if( ($operation==2) && !empty($dids) && !empty($primary) ){
					$query_remote = 'delete from '.$remote_db->quoteName($this->profile->params->table). ' where 1=1';
					foreach($dids as $did){
						$query_remote .= ' and '.$remote_db->quoteName($primary).' <> '.$remote_db->quote($did);
					}
					$remote_db->setQuery($query_remote);
					$res = $remote_db->execute();
				}
				$session->clear('dids');
				
				//send notification if done from site part
				if(JFactory::getApplication()->isSite()){
					$notify = $this->sendNotificationEmail( $this->profile->iotype,0 , array('schedule_id'=>"",'schedule_title'=>"","schedule_qry"=>"",'profile_id'=>$this->profile->id,'profile_title'=>$this->profile->title,"count"=>$qty, "format"=>"db") );
				}
				
				$obj->result='error';
				$obj->size = $size;
				$obj->qty = $session->get('qty',0);
				$obj->error = JText::_('NO_RECORD_TO_EXPORT');
				return $obj;
			}
			
			$n = 0;
			foreach($local_data_set as $key=>$local_data)
			{
				$isNew = true;
				$insert = new stdClass();
				foreach($this->profile->params->fields as $column=>$field)
				{
					//check if id already exists
					if(!empty($primary) and $column==$primary and $field->data <> "skip")	
					{
						$query_remote = 'SELECT count(*) FROM '.$remote_db->quoteName($remote_table).' WHERE '.$remote_db->quoteName($primary).' = '.$remote_db->quote($local_data->{$column});
						$remote_db->setQuery($query_remote);
						$count = $remote_db->loadResult();
						$isNew = $count > 0 ? false : true;
					}
					switch($field->data)
					{
						case 'include' : 
							if(!empty($remote_fields[$column])){
								$insert->{$column} = $local_data->{$remote_fields[$column]};
							}
							
						break;
						case 'defined' :
							$defined = $field->default;
							$defined = $this->getDefinedValue($field->default, $this->profile->params->fields, (array)$local_data, $remote_fields);
							$insert->{$column} = $defined;
						break;
						case 'reference' :
							//one to one relation
							if( empty($remote_fields[$column]['table']) || empty($remote_fields[$column]['refcol']) ){
								break;
							}
							$remote_ref_field = array();
							foreach($remote_fields[$column]['refcol'] as $key=>$rfield){
								if(!empty($rfield)){
									$remote_ref_field[] = $rfield;
								}
							}
							
							$query_local = "SELECT ".implode(',',$remote_ref_field)." FROM ".$local_db->quoteName($field->table)." "; 
							$query_local .= "where ".$local_db->quoteName($field->on)."=".$local_data->{$column};
							$local_db->setQuery($query_local);
							$values = $local_db->loadObject();
						
							if(!empty($values)){
								$query_remote = "select ".$remote_db->quoteName($field->on)." from ".$remote_db->quoteName($field->table)." where 1=1";
								foreach($remote_ref_field as $ref_field){
									$query_remote .= " and ".$remote_db->quoteName($ref_field)." = ".$remote_db->quote($values->{$ref_field});
								}
								$remote_db->setQuery($query_remote);
								$ref_value = $remote_db->loadResult();
								if(!empty($ref_value)){
									$insert->{$column} = $ref_value;
								}
								else{
									JLog::add(JText::sprintf('LOCAL_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
								}
							}
							else{
								JLog::add(JText::sprintf('REMOTE_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
							}
						break;
					}
					
				}
				
				
				if($operation==1)	
				{
					if(!$isNew){
						JLog::add(JText::sprintf('RECORD_EXISTS',$primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
						continue;
					}
					
					if($remote_db->insertObject($remote_table, $insert))	
						$base_in_id = $remote_db->insertid();
					else{
						$obj->result = 'error';
						$obj->error = $remote_db->stderr();
						return $obj;
					}
					if($this->getAffected($remote_db) > 0)
						$n++;
				}
				else
				{
					if(empty($primary) || empty($insert->{$primary})){
						$obj->result = 'error';
						$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
						return $obj;
					}
					
					$dids[] = $insert->{$primary};
					
					if($isNew){
						JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
						//insert if record do not exist
						if($remote_db->insertObject($remote_table, $insert))	
							$base_in_id = $remote_db->insertid();
						else{
							$obj->result = 'error';
							$obj->error = $remote_db->stderr();
							return $obj;
						}
						if($this->getAffected($remote_db) > 0){
							$n++;
						}
					}
					else{
						if($remote_db->updateObject($remote_table, $insert, $primary)){
							$base_in_id = $insert->{$primary};
						}
						else{
							$obj->result = 'error';
							$obj->error = $remote_db->stderr();
							return $obj;
						}
						if($this->getAffected($remote_db) > 0){
							$n++;
						}
					}
				}

				//export child's record
				if(!empty($this->profile->params->joins->table2))
				{
					$exData = array();
					$exData[$this->profile->params->table][] = $local_data;
					
					foreach($this->profile->params->joins->table2 as $c=>$child){
						if(array_key_exists($child, $remote_fields)){
							$child_primary = $this->getPrimaryKey($child, $remote_db);
							
							$response_data = $this->getExportChildData($local_db, $c, $this->profile->params, $exData,'ob');
							if($response_data->result=='error'){
								$obj->result = "error";
								$obj->error = $response_data->error;
								return $obj;
							}
							$child_data = $response_data->data;
							
							if(!empty($child_data)){
								foreach($child_data as $cd){
									$exData[$child][] = $cd;
								}
							}
							$child_array = array();
							foreach($child_data as $cd=>$cdata){
								
								$child_obj = new stdClass();
								foreach($this->profile->params->joins->columns[$c] as $column=>$field){
									switch($field->data){
										case 'include' :
										if($remote_fields[$child][$column]!=""){
											$child_obj->{$remote_fields[$child][$column]} = $cdata->{$column};
										}
										break;
										case 'defined' :
											$defined = $field->default;
											$defined = $this->getDefinedValue($field->default, $this->profile->params->joins->columns[$c], $cdata, $remote_fields[$child]);
											$child_obj->{$remote_fields[$child][$column]} = $defined;
										break;
										case 'reference' :
											if( empty($remote_fields[$child][$column]['table']) || empty($remote_fields[$child][$column]['refcol']) ){
												break;
											}
											$remote_ref_field = array();
											foreach($remote_fields[$child][$column]['refcol'] as $key=>$rfield){
												if(!empty($rfield)){
													$remote_ref_field[] = $rfield;
												}
											}
											$query_local = "SELECT ".implode(',',$remote_ref_field)." FROM ".$local_db->quoteName($field->table)." "; 
											$query_local .= "where ".$local_db->quoteName($field->on)."=".$cdata->{$column};
											$local_db->setQuery($query_local);
											$values = $local_db->loadObject();
											if(!empty($values)){
												$query_remote = "SELECT ".$remote_db->quoteName($field->on)." from ".$remote_db->quoteName($field->table)." where 1=1";
												foreach($remote_ref_field as $ref_field){
													$query_remote .= " and ".$remote_db->quoteName($ref_field)." = ".$remote_db->quote($values->{$ref_field});
												}
												$remote_db->setQuery($query_remote);
												$ref_value = $remote_db->loadResult();
												if(!empty($ref_value)){
													$child_obj->{$column} = $ref_value;
												}
												else{
													JLog::add(JText::sprintf('LOCAL_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
												}
											}
											else{
												JLog::add(JText::sprintf('REMOTE_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
											}
										break;
									}
								}
								
								//insert record
								if($operation==1)
								{
									if(!$isNew){
										JLog::add(JText::sprintf('RECORD_EXISTS',$child_primary, $child_obj->{$child_primary}), JLog::ERROR, 'com_vdata');
										continue;
									}
									
									if($remote_db->insertObject($child, $child_obj))
										$base_in_id = $remote_db->insertid();
									else{
										$obj->result = 'error';
										$obj->error = $remote_db->stderr();
										return $obj;
									}
								}
								else
								{
									if(empty($child_primary) || empty($child_obj->{$child_primary})){
										$obj->result = 'error';
										$obj->error = JText::_('PRIMARY_KEY_NOT_FOUND');
										return $obj;
									}
									if($isNew){
										JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $child_primary, $child_obj->{$child_primary}), JLog::ERROR, 'com_vdata');
										//insert if record do not exist
										if($remote_db->insertObject($child, $child_obj))
											$base_in_id = $remote_db->insertid();
										else{
											$obj->result = 'error';
											$obj->error = $remote_db->stderr();
											return $obj;
										}
									}
									else{
										if($remote_db->updateObject($child, $child_obj, $child_primary)){
											$base_in_id = $child_obj->{$child_primary};
										}
										else{
											$obj->result = 'error';
											$obj->error = $remote_db->stderr();
											return $obj;
										}
									}
								}
								
							}
							
						}
					}
					
				}
				
			}
			
			$session->set('dids', $dids);
			
			$obj->result='success';
			$obj->size = $size;
			$obj->offset = $offset + $config->limit;
			$qty = $session->get('qty',0) + $n;
			$session->set('qty',$qty);
			return $obj;
		}
		
		function batch_export_json_quick()
		{
			$obj = new stdClass();
			$db = $this->getDbc();
			$session = JFactory::getSession();
			$quick = JFactory::getApplication()->input->getInt('quick', 0);
			$quick_fields = JFactory::getApplication()->input->get('field', array(), 'ARRAY');
			$exportitem = $session->get('exportitem');
			$server = $exportitem->server;
			
			$offset = JFactory::getApplication()->input->getInt('offset');
			$config = $this->getConfig();
			if($exportitem->server=='down')
			{
				if($offset==0){
					$filename = JFilterOutput::stringURLSafe($this->profile->title).'.json';
					$exportitem->write = $filename;
					$session->set('exportitem',$exportitem);
				}
				else{
					$filename = $exportitem->write;
				}
				$filepath = JPATH_ROOT.'/components/com_vdata/uploads/'.$filename;
				$session->set('down', $filename);
			}
			elseif($exportitem->server=='write_remote'){
				$filename = JFilterOutput::stringURLSafe($this->profile->title).'.json';
				$filepath = JPATH_ROOT.'/components/com_vdata/uploads/'.$filename;
			}
			else
			{	
				$filepath = $exportitem->path;
				if($exportitem->server=='write_local')
				{
					$filepath = JPATH_ROOT.'/'.$exportitem->path;
				}
				//validate file extension
				$ext = strrchr($filepath, '.');
				if($ext <> '.json')	
				{
					$obj->result = 'error';
					$obj->error = JText::_('PLZ_SELECT_JSON_FILE');
					return $obj;
				}
				if($exportitem->mode=='a' && !is_file($filepath))    
				{
					$obj->result = "error";
					$obj->error = JText::_('COULD_NOT_ACCESS');
					return $obj;
				}

				if($exportitem->mode=='a' && !is_writable($filepath))
				{
					$obj->result = "error";
					$obj->error = JText::_('NOT_WRITABLE_FILE');
					return $obj;
				}
			}
			$query = "select * from ".$db->quoteName($this->profile->params->table);
			$db->setQuery($query, $offset, $config->limit);
			$dataset = $db->loadObjectList();
			
			$size_query = "select count(*) from ".$db->quoteName($this->profile->params->table);
			$db->setQuery($size_query);
			$size = $db->loadResult();
			
			$n=0;
			if(count($dataset)==0){
				$qty = $session->get('qty',0);
				if($exportitem->server == 'down' && $qty){
					$obj->dname = $filename;
					$obj->dlink = true;
				}
				else{
					$obj->dlink = false;
				}
				//email export file
				if( ($qty>0) && isset($exportitem->sendfile["status"]) && ($exportitem->sendfile["status"]==1) && isset($exportitem->sendfile["emails"]) && !empty($exportitem->sendfile["emails"]) ){
					$emails = explode(',', $exportitem->sendfile["emails"]);
					$cc = explode(',', $exportitem->sendfile["cc"]);
					$bcc = explode(',', $exportitem->sendfile["bcc"]);
					$subject = $exportitem->sendfile["subject"];
					$body = $exportitem->sendfile["tmpl"];
					$attachment = $filepath;
					$sendfile = $this->sendFile($emails, $cc, $bcc, $subject, $body, $attachment);
				}
				//write file to remote server using ftp
				if( ($qty>0) && ($exportitem->server=='write_remote')){
					if(!$this->uploadToFtp($exportitem->ftp, $filepath)){
						$obj->result = 'error';
						$obj->error = JText::_('VDATA_FTP_FILE_WRITE_ERROR');
						return $obj;
					}
					if(!JFile::delete($filepath)){
						JLog::add(JText::sprintf('VDATA_UNABLE_TO_DELETE_LOCAL_COPY', $filepath), JLog::INFO, 'com_vdata');
					}
				}
				//send notification if done from site part
				if(JFactory::getApplication()->isSite()){
					$notify = $this->sendNotificationEmail( $this->profile->iotype,0 , array('schedule_id'=>"",'schedule_title'=>"","schedule_qry"=>"",'profile_id'=>$this->profile->id,'profile_title'=>$this->profile->title,"count"=>$qty, "format"=>"json") );
				}
				
				//log info
				JLog::add(JText::sprintf('EXPORT_PROFILE', $this->profile->title), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('EXPORT_TABLE', $this->profile->params->table), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('EXPORT_FORMAT', 'JSON'), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('EXPORT_RECORD_COUNT', $qty), JLog::INFO, 'com_vdata');
				
				$obj->result = 'error';
				$obj->size = $size;
				$obj->qty = $qty;
				$obj->error = JText::_('NO_RECORD_TO_EXPORT');
				return $obj;
			}
			if($exportitem->server=='down'){
				if($offset==0)	{
					$fp = @fopen($filepath, 'w');
					$flag = fwrite($fp, "[]");
				}
			}
			else{
				if($offset==0 && $exportitem->mode!='a'){
					$fp = @fopen($filepath, 'w');
					$flag = fwrite($fp, "[]");
				}
			}

			foreach($dataset as $i=>$data){
				if(!$quick){
					$row = new stdClass();
					foreach($quick_fields as $key=>$quick_field){
						$nodeValue = $data->{$key};
						$nodeName = $quick_field;
						$parentElements = array();
						$nodePath = explode('/', $nodeName);
						if(count($nodePath)>1){
							foreach($nodePath as $node){
								if(!empty($node)){
									array_push($parentElements, $node);
								}
							}
							if(count($parentElements)>1){
								$preObj = new stdClass();
								for($elm=(count($parentElements)-1);$elm>0;$elm--){
									$dummy = new stdClass();
									if($elm==(count($parentElements)-1)){
										$dummy->{$parentElements[$elm]} = $data->{$key};
									}
									else{
										$dummy->{$parentElements[$elm]} = $preObj;
									}
									$preObj = $dummy;
								}
								$nodeName = $parentElements[0];
								$nodeValue = $preObj;
							}
						}
						$row->{$nodeName} = $nodeValue;
					}
				}
				else{
					$row = $data;
				}
				
				if($exportitem->server=='down'){
					$fp = @fopen($filepath, 'r+');
					fseek($fp,-1,SEEK_END);
					if($offset==0){
						if(count($dataset)==($i+1)){
							$flag = fwrite($fp, json_encode($row, JSON_UNESCAPED_UNICODE));
						}
						else{
							$flag = fwrite($fp, json_encode($row, JSON_UNESCAPED_UNICODE).',');
						}
					}
					else{
						$flag = fwrite($fp, ','.json_encode($row, JSON_UNESCAPED_UNICODE));
					}
					fseek($fp,0,SEEK_END);
					$flag = fwrite($fp, "]");
				}
				else{
					if($exportitem->mode=='a'){
						$fp = @fopen($filepath, 'r+');
						fseek($fp,-1,SEEK_END);
						$flag = fwrite($fp, ','.json_encode($row, JSON_UNESCAPED_UNICODE));
						fseek($fp,0,SEEK_END);
						$flag = fwrite($fp, "]");
					}
					else{
						$fp = @fopen($filepath, 'r+');
						fseek($fp,-1,SEEK_END);
						if($offset==0)	{
							if(count($dataset)==($i+1)){
								$flag = fwrite($fp, json_encode($row, JSON_UNESCAPED_UNICODE));
							}
							else{
								$flag = fwrite($fp, json_encode($row, JSON_UNESCAPED_UNICODE).',');
							}
						}
						else{
							$flag = fwrite($fp, ','.json_encode($row, JSON_UNESCAPED_UNICODE));
						}
						fseek($fp,0,SEEK_END);
						$flag = fwrite($fp, "]");
					}
				}
				if($flag !== FALSE)
					$n++;
			}
			fclose($fp);
			
			$obj->result='success';
			$obj->size = $size;
			$obj->offset = $offset + $config->limit;
			$qty = $session->get('qty',0) + $n;
			$session->set('qty',$qty);
			return $obj;
		}
		
		function batch_export_json()
		{
			$obj = new stdClass();
			$db = $this->getDbc();
			$session = JFactory::getSession();
			$expo_fields = JFactory::getApplication()->input->get('field', array(), 'ARRAY');
			$exportitem = $session->get('exportitem');
			
			$offset = JFactory::getApplication()->input->getInt('offset');
			$config = $this->getConfig();
			if($exportitem->server=='down'){
				if($offset==0){
					$filename = JFilterOutput::stringURLSafe($this->profile->title).'.json';
					$exportitem->write = $filename;
					$session->set('exportitem',$exportitem);
				}
				else{
					$filename = $exportitem->write;
				}
				$filepath = JPATH_ROOT.'/components/com_vdata/uploads/'.$filename;
				$session->set('down', $filename);
			}
			elseif($exportitem->server=='write_remote'){
				$filename = JFilterOutput::stringURLSafe($this->profile->title).'.json';
				$filepath = JPATH_ROOT.'/components/com_vdata/uploads/'.$filename;
			}
			else{	
				$filepath = $exportitem->path;
				if($exportitem->server=='write_local'){
					$filepath = JPATH_ROOT.'/'.$exportitem->path;
				}
				//validate file extension
				$ext = strrchr($filepath, '.');
				if($ext <> '.json')	{
					$obj->result = 'error';
					$obj->error = JText::_('PLZ_SELECT_JSON_FILE');
					return $obj;
				}
				if($exportitem->mode=='a' && !is_file($filepath)){
					$obj->result = "error";
					$obj->error = JText::_('COULD_NOT_ACCESS');
					return $obj;
				}
				if($exportitem->mode=='a' && !is_writable($filepath)){
					$obj->result = "error";
					$obj->error = JText::_('NOT_WRITABLE_FILE');
					return $obj;
				}
			}
			
			if($exportitem->server=='down'){
				if($offset==0){
					$fp = @fopen($filepath, 'w');
					$flag = fwrite($fp, "[]");
				}
			}
			else{
				if($offset==0 && $exportitem->mode!='a'){
					$fp = @fopen($filepath, 'w');
					$flag = fwrite($fp, "[]");
				}
			}
			
			//retrieve data
			$result = $this->getExportData($db, $this->profile->params, $offset, $config->limit, 'ob');
			$dataset = $result->data;
			$size  = $result->size;
			
			$n = 0;
			if(count($dataset)==0){
				$qty = $session->get('qty', 0);
				if($exportitem->server == 'down' && $qty){
					$obj->dname = $filename;
					$obj->dlink = true;
				}
				else{
					$obj->dlink = false;
				}
				//email export file
				if( ($qty>0) && isset($exportitem->sendfile["status"]) && ($exportitem->sendfile["status"]==1) && isset($exportitem->sendfile["emails"]) && !empty($exportitem->sendfile["emails"]) ){
					$emails = explode(',', $exportitem->sendfile["emails"]);
					$cc = explode(',', $exportitem->sendfile["cc"]);
					$bcc = explode(',', $exportitem->sendfile["bcc"]);
					$subject = $exportitem->sendfile["subject"];
					$body = $exportitem->sendfile["tmpl"];
					$attachment = $filepath;
					$sendfile = $this->sendFile($emails, $cc, $bcc, $subject, $body, $attachment);
				}
				//write file to remote server using ftp
				if( ($qty>0) && ($exportitem->server=='write_remote')){
					if(!$this->uploadToFtp($exportitem->ftp, $filepath)){
						$obj->result = 'error';
						$obj->error = JText::_('VDATA_FTP_FILE_WRITE_ERROR');
						return $obj;
					}
					if(!JFile::delete($filepath)){
						JLog::add(JText::sprintf('VDATA_UNABLE_TO_DELETE_LOCAL_COPY', $filepath), JLog::INFO, 'com_vdata');
					}
				}
				//send notification if done from site part
				if(JFactory::getApplication()->isSite()){
					$notify = $this->sendNotificationEmail( $this->profile->iotype,0 , array('schedule_id'=>"",'schedule_title'=>"","schedule_qry"=>"",'profile_id'=>$this->profile->id,'profile_title'=>$this->profile->title,"count"=>$qty, "format"=>"json") );
				}
				//log info
				JLog::add(JText::sprintf('EXPORT_PROFILE', $this->profile->title), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('EXPORT_TABLE', $this->profile->params->table), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('EXPORT_FORMAT', 'JSON'), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('EXPORT_RECORD_COUNT', $qty), JLog::INFO, 'com_vdata');
				
				$obj->result = 'error';
				$obj->size = $size;
				$obj->qty = $qty;
				$obj->error = JText::_('NO_RECORD_TO_EXPORT');
				return $obj;
			}
			
			foreach($dataset as $i=>$data)
			{
				$row = new stdClass();
				foreach($this->profile->params->fields as $column=>$field){
					switch($field->data){
						case 'include' :
							$nodeValue = $data->{$column};
							$nodeName = $expo_fields[$column];
							$parentElements = array();
							$nodePath = explode('/', $nodeName);
							if(count($nodePath)>1){
								foreach($nodePath as $node){
									if(!empty($node)){
										array_push($parentElements, $node);
									}
								}
								if(count($parentElements)>1){
									$preObj = new stdClass();
									for($elm=(count($parentElements)-1);$elm>0;$elm--){
										$dummy = new stdClass();
										if($elm==(count($parentElements)-1)){
											$dummy->{$parentElements[$elm]} = $data->{$column};
										}
										else{
											$dummy->{$parentElements[$elm]} = $preObj;
										}
										$preObj = $dummy;
									}
									$nodeName = $parentElements[0];
									$nodeValue = $preObj;
								}
							}
							$row->{$nodeName} = $nodeValue;
							// $row->{$expo_fields[$column]} = $data->{$column};
						break;
						case 'defined' :
							$defined = $field->default;
							$defined = $this->getDefinedValue($field->default, $this->profile->params->fields, (array)$data, $expo_fields);
							$row->{$expo_fields[$column]} = $defined;
						break;
						case 'reference' :
							$query = "SELECT ".$db->quoteName($field->table.''.$i).".".implode(','.$db->quoteName($field->table.''.$i).".", $field->reftext)." FROM ".$db->quoteName($field->table)." AS ".$db->quoteName($field->table.''.$i)." JOIN ".$db->quoteName($this->profile->params->table)." ON ".$db->quoteName($field->table.''.$i).".".$db->quoteName($field->on)." = ".$db->quoteName($this->profile->params->table).".".$column." WHERE ".$db->quoteName($this->profile->params->table).".".$column." = ".$db->quote($data->{$column});
							$db->setQuery($query);
							$ref_data = $db->loadObject();
							if($ref_data){
								foreach($ref_data as $j=>$val){
									$nodeValue = $val;
									$nodeName = $expo_fields[$column][$j];
									$parentElements = array();
									$nodePath = explode('/', $nodeName);
									if(count($nodePath)>1){
										foreach($nodePath as $node){
											if(!empty($node)){
												array_push($parentElements, $node);
											}
										}print_r($parentElements);
										if(count($parentElements)>1){
											$preObj = new stdClass();
											for($elm=(count($parentElements)-1);$elm>0;$elm--){
												$dummy = new stdClass();
												if($elm==(count($parentElements)-1)){
													$dummy->{$parentElements[$elm]} = $val;
												}
												else{
													$dummy->{$parentElements[$elm]} = $preObj;
												}
												$preObj = $dummy;
											}
											$nodeName = $parentElements[0];
											$nodeValue = $preObj;
										}
									}
									$row->{$nodeName} = $nodeValue;				
									// $row->{str_replace('#__', '', $expo_fields[$column][$j])} = $val;
								}
							}
						break;
						
					}
				}
				
				if(!empty($this->profile->params->joins->table2)){
					
					$exData = array();
					$exData[$this->profile->params->table][] = $data;
					
					foreach($this->profile->params->joins->table2 as $c=>$child){
						if(array_key_exists($child, $expo_fields)){
							
							$response_data = $this->getExportChildData($db, $c, $this->profile->params, $exData,'ob');
							if($response_data->result=='error'){
								$obj->result = "error";
								$obj->error = $response_data->error;
								return $obj;
							}
							$child_data = $response_data->data;
							
							if(!empty($child_data)){
								foreach($child_data as $cd){
									$exData[$child][] = $cd;
								}
							}
							$child_array = array();
							foreach($child_data as $cd=>$cdata){
								$child_obj = new stdClass();
								foreach($this->profile->params->joins->columns[$c] as $column=>$field){
									switch($field->data){
										case 'include' :
											$nodeValue = $cdata->{$column};
											$nodeName = $expo_fields[$child][$column];
											$parentElements = array();
											$nodePath = explode('/', $nodeName);
											if(count($nodePath)>1){
												foreach($nodePath as $node){
													if(!empty($node)){
														array_push($parentElements, $node);
													}
												}
												if(count($parentElements)>1){
													$preObj = new stdClass();
													for($elm=(count($parentElements)-1);$elm>0;$elm--){
														$dummy = new stdClass();
														if($elm==(count($parentElements)-1)){
															$dummy->{$parentElements[$elm]} = $cdata->{$column};
														}
														else{
															$dummy->{$parentElements[$elm]} = $preObj;
														}
														$preObj = $dummy;
													}
													$nodeName = $parentElements[0];
													$nodeValue = $preObj;
												}
											}
											$child_obj->{$nodeName} = $nodeValue;
											// $child_obj->{$expo_fields[$child][$column]} = $cdata->{$column};
										break;
										case 'defined' :
											$defined = $field->default;
											$defined = $this->getDefinedValue($field->default, $this->profile->params->joins->columns[$c], $cdata, $expo_fields[$child]);
											$child_obj->{$expo_fields[$child][$column]} = $defined;
										break;
										case 'reference' :
											
											$query = "SELECT ".$db->quoteName($field->table.''.$c).".".implode(','.$db->quoteName($field->table.''.$c).".", $field->reftext);
											$query .= " FROM ".$db->quoteName($field->table)." AS ".$db->quoteName($field->table.''.$c);
											$query .= " JOIN ".$db->quoteName($child);
											$query .= " ON ".$db->quoteName($field->table.''.$c).".".$db->quoteName($field->on)." = ";
											$query .= ($child=='#__modules_menu')?'ABS('.$db->quoteName($child).".".$column.')':($db->quoteName($child).".".$column);
											$query .= " WHERE ".$db->quoteName($child).".".$column." = ".$db->quote($cdata->{$column});
											
											$db->setQuery($query);
											$ref_data = $db->loadAssoc();
											
											if($ref_data){
												foreach($ref_data as $rkey=>$rval){
													$nodeValue = $rval;
													$nodeName = $expo_fields[$child][$column][$rkey];
													$parentElements = array();
													$nodePath = explode('/', $nodeName);
													if(count($nodePath)>1){
														foreach($nodePath as $node){
															if(!empty($node)){
																array_push($parentElements, $node);
															}
														}
														if(count($parentElements)>1){
															$preObj = new stdClass();
															for($elm=(count($parentElements)-1);$elm>0;$elm--){
																$dummy = new stdClass();
																if($elm==(count($parentElements)-1)){
																	$dummy->{$parentElements[$elm]} = $rval;
																}
																else{
																	$dummy->{$parentElements[$elm]} = $preObj;
																}
																$preObj = $dummy;
															}
															$nodeName = $parentElements[0];
															$nodeValue = $preObj;
														}
													}
													$child_obj->{$nodeName} = $nodeValue;
													// $child_obj->{$expo_fields[$child][$column][$rkey]} = ($child=='#__modules_menu' && $cdata->{$column}<0)?"(-)".$rval:$rval;
												}
											}
										break;
									}
								}
								array_push($child_array, $child_obj);
								
							}
							$row->{str_replace('#__', '', $child)} = $child_array;
							
						}
					}
				}
				
				if($exportitem->server=='down'){
					$fp = @fopen($filepath, 'r+');
					fseek($fp,-1,SEEK_END);
					if($offset==0){
						if(count($dataset)==($i+1)){
							$flag = fwrite($fp, json_encode($row, JSON_UNESCAPED_UNICODE));
						}
						else{
							$flag = fwrite($fp, json_encode($row, JSON_UNESCAPED_UNICODE).',');
						}
					}
					else{
						$flag = fwrite($fp, ','.json_encode($row, JSON_UNESCAPED_UNICODE));
					}
					fseek($fp,0,SEEK_END);
					$flag = fwrite($fp, "]");
				}
				else{
					if($exportitem->mode=='a'){
						$fp = @fopen($filepath, 'r+');
						fseek($fp,-1,SEEK_END);
						$flag = fwrite($fp, ','.json_encode($row, JSON_UNESCAPED_UNICODE));
						fseek($fp,0,SEEK_END);
						$flag = fwrite($fp, "]");
					}
					else{
						$fp = @fopen($filepath, 'r+');
						fseek($fp,-1,SEEK_END);
						if($offset==0)	{
							if(count($dataset)==($i+1)){
								$flag = fwrite($fp, json_encode($row, JSON_UNESCAPED_UNICODE));
							}
							else{
								$flag = fwrite($fp, json_encode($row, JSON_UNESCAPED_UNICODE).',');
							}
						}
						else{
							$flag = fwrite($fp, ','.json_encode($row, JSON_UNESCAPED_UNICODE));
						}
						fseek($fp,0,SEEK_END);
						$flag = fwrite($fp, "]");
					}
				}
				if($flag !== FALSE)
					$n++;
				
			}
			
			fclose($fp);
			
			$obj->result='success';
			$obj->size = $size;
			$obj->offset = $offset + $config->limit;
			$qty = $session->get('qty',0) + $n;
			$session->set('qty',$qty);
			return $obj;
		}
		
		//method to get complete configuration
		function getConfig()
		{
			$db = JFactory::getDbo();
			$query = "select * from #__vd_config where id =1";
			$db->setQuery($query);
			return $db->loadObject();
		}
		
		function getDbc()
		{
			$config = JFactory::getConfig();
			$hd_config = $this->getConfig();
			$dbconfig = json_decode($hd_config->dbconfig);
			if($dbconfig->local_db==1){
				$dbc = JFactory::getDbo();
			}
			else{
				$option = array();
				if( property_exists($dbconfig, 'driver') && property_exists($dbconfig, 'host') && property_exists($dbconfig, 'user') && property_exists($dbconfig, 'password') && property_exists($dbconfig, 'database') && property_exists($dbconfig, 'dbprefix') ){
					$option['driver'] = $dbconfig->driver;
					$option['host'] = $dbconfig->host;
					$option['user'] = $dbconfig->user;
					$option['password'] = $dbconfig->password;
					$option['database'] = $dbconfig->database;
					if(!empty($dbconfig->dbprefix))
						$option['prefix'] = $dbconfig->dbprefix;
					
				}
				try{
					$dbc = JDatabaseDriver::getInstance($option);
					$dbc->connect();
				}
				catch(Exception $e){
					throw new Exception($e->getMessage());
					return false;
				}
			}
			
			return $dbc;
		}
		
		function setFeed(){
			
			$input 		= JFactory::getApplication()->input;
			$schedule_uid = $input->get('type', '', 'RAW');
			$items = $input->get('items', array(), 'RAW');
			
			$db = JFactory::getDbo();
			$query = 'select * from #__vd_schedules where state=1 and uid='.$db->quote($schedule_uid);
			$db->setQuery($query);
			$schedule = $db->loadObject();
			
			if(empty($items)){
				jexit('{"result":"error", "error":"'.JText::_('NO_RECORD_TO_IMPORT').'"}');
			}
			if(empty($schedule->profileid)){
				jexit('{"result":"error", "error":"'.JText::_('INVALID_PROFILE_ID').'"}');
			}
			
			$log = new stdClass();
			$log->iotype = 0;
			$log->profileid = $schedule->profileid;
			$log->side = 'feed';
			$log->user = JFactory::getUser()->id;
			$op_start = JFactory::getDate('now')->format('m-d-Y_hia');
			$log_file = 'com_vdata_plg_custom'.'_'.$op_start.'.txt';
			
			$config = $this->getConfig();
			if($config->logging){
				$logger = $this->initializeLogger();
			}
			
			$log->logfile = $log_file;
			$log->op_start =  JFactory::getDate(microtime(true))->toSql(true);
			
			
			$query = 'select * from #__vd_profiles where id='.$schedule->profileid;
			$db->setQuery($query);
			$profile = $db->loadObject();
			$profile_params = json_decode($profile->params);
			
			$query = 'SHOW KEYS FROM '.$db->quoteName($profile_params->table).' WHERE Key_name = "PRIMARY"';
			$db->setQuery( $query );
			$key = $db->loadObjectList();
			if(property_exists($profile_params, 'unqkey') && !empty($profile_params->unqkey))
				$primary = $profile_params->unqkey;
			elseif(!empty($key) && (count($key)==1) )
				$primary = $key[0]->Column_name;
			else
				$primary = null;
			
			// print_r($items);
			$schedule_columns = json_decode($schedule->columns, true);
			$schedule_fields = $schedule_columns["fields"];
			// print_r($schedule_fields);
			// print_r($profile_params);
			
			$n = 0;
			
			$insert = new stdClass();
			$cached_data = new stdClass();
			$instance = false;
			$isNew = true;
			$after = false;
			foreach($profile_params->fields as $column=>$field){
				/* if(empty($schedule_fields[$column])){
					continue;
				} */
				if( !empty($primary) and ($column==$primary) and ($field->data <> "skip") and isset($items[$schedule_fields[$column]]) )	{		
					$query = 'select * from '.$db->quoteName($profile_params->table).' where '.$db->quoteName($primary).' = '.$db->quote($items[$schedule_fields[$column]]);
					$db->setQuery( $query );
					$oldData = $db->loadAssoc();
					$isNew = empty($oldData) ? true : false;
				}
				switch($field->data){
					case "file":
						if(isset($items[$schedule_fields[$column]])){
							$insert->{$column} = $cached_data->{$column} = $this->getFilteredValue($field, $items[$schedule_fields[$column]]);
							if($field->format == "encrypt"){
								$cached_data->{$column} = $items[$schedule_fields[$column]];
							}
						}
					break;
					case "defined":
						$defined = $field->default;
						$defined = $this->getDefinedValue($field->default, $profile_params->fields, $items, $schedule_fields);
						$insert->{$column} = $cached_data->{$column} = $defined;
					break;
					case "reference":
						$insert_ref = new stdClass();
						foreach($field->reftext as $i=>$ref){
							if(!empty($schedule_fields[$column][$ref]) && isset($items[$schedule_fields[$column][$ref]])) {
								$insert_ref->{$ref} = $items[$schedule_fields[$column][$ref]];
							}
						}
				
						$insert_ref_val = (array)$insert_ref;
						if(!empty($insert_ref_val)){
							$ref_value = $this->getReferenceVal($field->table, $field->on, $insert_ref, $db);
							if(!empty($ref_value)){
								$insert->{$column} = $ref_value;
								$cached_data->{$column} = $ref_value;
							}
							else{
								JLog::add(JText::sprintf('LOCAL_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
							}
						}
						else{
							JLog::add(JText::sprintf('REMOTE_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
						}
					break;
					case 'asset_reference':
						$instance = true;
						$insert->$column = 0;
						$intance_column = $field->on;
						$instance_component = $field->table;
						if(isset($schedule_fields[$column]['rules']) && $schedule_fields[$column]['rules']!="" && isset($items[$schedule_fields[$column]['rules']])){
							$insert->rules = json_decode($items[$schedule_fields[$column]['rules']], true);
						}
					break;
				}
			}
			
			// print_r($insert);jexit('stop');
			
			if($profile_params->operation==1){
				if(!$isNew){
					JLog::add(JText::sprintf('RECORD_EXISTS',$primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
					// continue;
					$log->op_end = JFactory::getDate('now')->toSql(true);
					$log->status = 'abort';
					$log->message = JText::sprintf('RECORD_EXISTS',$primary, $insert->{$primary});
					$flag = $this->setLog($log);
					jexit('{"result":"error","error":"'.JText::sprintf('RECORD_EXISTS',$primary, $insert->{$primary}).'"}');
				}
				//capture events before insert
				if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'before') ){
					//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $profile_params->events->before)), JLog::INFO, 'com_vdata');
					$this->profile = new stdClass();
					$this->profile->params = $profile_params;
					$response = $this->captureEventsOnRecord( $insert, 'before', $isNew , array('pk'=>$primary ,'old'=>$oldData));
					if(in_array(false, $response)){
						JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
						// continue;
						$log->op_end = JFactory::getDate('now')->toSql(true);
						$log->status = 'abort';
						$log->message = JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD');
						$flag = $this->setLog($log);
						jexit('{"result":"error","error":"'.JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD').'"}');
					}
				}
				
				if(!$instance && isset($profile_params->component->table) && !empty($profile_params->component->table)){
					$instance = true;
					$instance_component = $profile_params->component->value;
					$intance_column = $profile_params->component->table;
				}
				if($instance){
					$component = JText::_($instance_component);
					$component_name = $component;
					if (strpos(strtolower($component), "com_") ===FALSE){
						$component_name = "com_".strtolower($component_name); 
					}
					if (strpos(strtolower($component), "com_") ===0){
						$component = str_replace("com_", '',strtolower($component)); 
					}
					JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
					$core_tables = array('category', 'content', 'menu', 'module');
					if(in_array($intance_column, $core_tables)){
						$tbl_instance = JTable::getInstance($intance_column);
					}
					else{
						$tbl_instance = JTable::getInstance($intance_column, $component.'Table');
					}
					
					if (!$tbl_instance->bind((array)$insert)) {
						JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
						$log->op_end = JFactory::getDate('now')->toSql(true);
						$log->status = 'abort';
						$log->message = $tbl_instance->getError();
						$flag = $this->setLog($log);
						jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
					}
					if (!$tbl_instance->check()) {
						JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
						$log->op_end = JFactory::getDate('now')->toSql(true);
						$log->status = 'abort';
						$log->message = $tbl_instance->getError();
						$flag = $this->setLog($log);
						jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
					}
					if (!$tbl_instance->store()){
						JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
						$log->op_end = JFactory::getDate('now')->toSql(true);
						$log->status = 'abort';
						$log->message = $tbl_instance->getError();
						$flag = $this->setLog($log);
						jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
					}
					$n++;
					$after = true;
					if(!empty($primary) && property_exists($tbl_instance, $primary)){
						$base_in_id = $tbl_instance->{$primary};
					}
				}
				else{
					if($db->insertObject($profile_params->table, $insert)){
						$base_in_id = $db->insertid();
						if($db->getAffectedRows()>0){
							$after = true;
							$n++;
						}
					}
					else{
						JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
						$log->op_end = JFactory::getDate('now')->toSql(true);
						$log->status = 'abort';
						$log->message = $db->stderr();
						$flag = $this->setLog($log);
						jexit('{"result":"error","error":"'.$db->stderr().'"}');
					}
				}
				//capture events after insert
				if($after){
					if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'after') ){
						//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
						if(!property_exists($cached_data, $primary)){
							$cached_data->$primary = $base_in_id;
						}
						$response = $this->captureEventsOnRecord( $cached_data, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
					}
				}
				
			}
			else{
				if(empty($primary) || empty($insert->{$primary})){
					$log->op_end = JFactory::getDate('now')->toSql(true);
					$log->status = 'abort';
					$log->message = JText::_('PRIMARY_KEY_NOT_FOUND');
					$flag = $this->setLog($log);
					jexit('{"result":"error","error":"'.JText::_('PRIMARY_KEY_NOT_FOUND').'"}');
				}
				//capture events before insert
				if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'before') ){
					//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $profile_params->events->before)), JLog::INFO, 'com_vdata');
					$this->profile = new stdClass();
					$this->profile->params = $profile_params;
					$response = $this->captureEventsOnRecord( $insert, 'before', $isNew , array('pk'=>$primary ,'old'=>$oldData));
					if(in_array(false, $response)){
						JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
						// continue;
						$log->op_end = JFactory::getDate('now')->toSql(true);
						$log->status = 'abort';
						$log->message = JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD');
						$flag = $this->setLog($log);
						jexit('{"result":"error","error":"'.JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD').'"}');
						
					}
				}
				if($instance){
					$component = JText::_($instance_component);
					$component_name = $component;
					if (strpos(strtolower($component), "com_") ===FALSE){
						$component_name = "com_".strtolower($component_name); 
					}
					if (strpos(strtolower($component), "com_") ===0){
						$component = str_replace("com_", '',strtolower($component)); 
					}
					JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
					$core_tables = array('category', 'content', 'menu', 'module');
					if(in_array($intance_column, $core_tables)){
						$tbl_instance = JTable::getInstance($intance_column);
					}
					else{
						$tbl_instance = JTable::getInstance($intance_column, $component.'Table');
					}
					
					if(!$isNew){
						$tbl_instance->load($insert->{$primary});
					}
					
					if (!$tbl_instance->bind((array)$insert)) {
						JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
						$log->op_end = JFactory::getDate('now')->toSql(true);
						$log->status = 'abort';
						$log->message = $tbl_instance->getError();
						$flag = $this->setLog($log);
						jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
					}
					if (!$tbl_instance->check()) {
						JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
						$log->op_end = JFactory::getDate('now')->toSql(true);
						$log->status = 'abort';
						$log->message = $tbl_instance->getError();
						$flag = $this->setLog($log);
						jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
					}
					if (!$tbl_instance->store()){
						JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
						$log->op_end = JFactory::getDate('now')->toSql(true);
						$log->status = 'abort';
						$log->message = $tbl_instance->getError();
						$flag = $this->setLog($log);
						jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
					}
					$n++;
					$after = true;
					if(!empty($primary) && property_exists($tbl_instance, $primary)){
						$base_in_id = $tbl_instance->{$primary};
					}
				}
				else{
					if($isNew){
						JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
						if($db->insertObject($profile_params->table, $insert)){
							$base_in_id = $db->insertid();
							if($db->getAffectedRows()>0){
								$after = true;
								$n++;
							}
						}
						else{
							JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
							$log->op_end = JFactory::getDate('now')->toSql(true);
							$log->status = 'abort';
							$log->message = $db->stderr();
							$flag = $this->setLog($log);
							jexit('{"result":"error","error":"'.$db->stderr().'"}');
						}
					}
					else{
						if($db->updateObject($this->profile->params->table, $insert, $primary)){
							$base_in_id = $insert->{$primary};
							if($this->getAffected($db)> 0) {$n++;}
							$after = true;
						}
						else{
							JLog::add(JText::sprintf('UPDATE_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
							$log->op_end = JFactory::getDate('now')->toSql(true);
							$log->status = 'abort';
							$log->message = $db->stderr();
							$flag = $this->setLog($log);
							jexit('{"result":"error","error":"'.$db->stderr().'"}');
						}
					}
				}
				//capture events after insert
				if($after){
					if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'after') ){
						//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $this->profile->params->events->after)), JLog::INFO, 'com_vdata');
						if(!property_exists($cached_data, $primary)){
							$cached_data->$primary = $base_in_id;
						}
						$response = $this->captureEventsOnRecord( $cached_data, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
					}
				}
			}
			// $base_in_id = 1;
			$join_field = new stdClass();
			$previous_data = array();
			if(!empty($profile_params->joins->table2)){
				$index = $child_count = count($profile_params->joins->table2);
				while($index > 0){
					//import child data starting from last child
					$idx = --$index;
					
					
					
					$query = 'SHOW KEYS FROM '.$db->quoteName($profile_params->joins->table2[$idx]).' WHERE Key_name = "PRIMARY"';
					$db->setQuery( $query );
					$key_join = $db->loadObjectList();
					if( !empty($key_join) && (count($key_join)==1) ){
						$primary_join = $key_join[0]->Column_name;
					}
					else{
						$primary_join = null;
					}
					
					
					$insert_join = new stdClass();
					if($idx==0){
						$insert_join->{$profile_params->joins->column2[0]} = $base_in_id;
					}
					
					if(!empty($previous_data)){
						$keys = array_keys($previous_data);
						$key = $keys[count($keys)-1];
						if(!empty($key)){
							$tmp_keys = explode(':', $key);
							if(property_exists($previous_data[$key], $tmp_keys[1])){
								$insert_join->{$tmp_keys[0]} = $previous_data[$key]->{$tmp_keys[1]};
							}
						}
					}
					$previous_data = array();
					
					foreach($profile_params->joins->columns[$idx] as $col=>$key){
						if(isset($schedule_fields[$profile_params->joins->table2[$idx]][$key]) && isset($items[$schedule_fields[$profile_params->joins->table2[$idx]][$key]])){
							$insert_join->{$key} =  $items[$schedule_fields[$profile_params->joins->table2[$idx]][$key]];
						}
					}
					
					// print_r($insert_join);
					$is_new = true;
					if(!empty($primary_join) && property_exists($insert_join, $primary_join)){
						$query = 'select count(*) from '.$db->quoteName($profile_params->joins->table2[$idx]).' where '.$db->quoteName($primary_join).'='.$db->quote($insert_join->{$primary_join});
						$db->setQuery($query);
						$result = $db->loadResult();
						$is_new = ($result)>0?false:true;
					}
					
					
					if(isset($profile_params->joins->component->value[$idx]) && isset($profile_params->joins->component->table[$idx]) && !empty($profile_params->joins->component->table[$idx])){
						
						$component = JText::_($profile_params->joins->component->value[$idx]);
						$intance_column = $profile_params->joins->component->table[$idx];
						$component_name = $component;
						if (strpos(strtolower($component), "com_") ===FALSE){
							$component_name = "com_".strtolower($component_name); 
						}
						if (strpos(strtolower($component), "com_") ===0){
							$component = str_replace("com_", '',strtolower($component)); 
						}
						JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
						$core_tables = array('category', 'content', 'menu', 'module');
						if(in_array($intance_column, $core_tables)){
							$tbl_instance = JTable::getInstance($intance_column);
						}
						else{
							$tbl_instance = JTable::getInstance($intance_column, $component.'Table');
						}
						
						if($profile_params->operation!=1){
							if(!$is_new){
								$tbl_instance->load($insert_join->{$primary_join});
							}
						}
						if (!$tbl_instance->bind((array)$insert_join)) {
							JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
							$log->op_end = JFactory::getDate('now')->toSql(true);
							$log->status = 'abort';
							$log->message = $tbl_instance->getError();
							$flag = $this->setLog($log);
							jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
						}
						if (!$tbl_instance->check()) {
							JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
							$log->op_end = JFactory::getDate('now')->toSql(true);
							$log->status = 'abort';
							$log->message = $tbl_instance->getError();
							$flag = $this->setLog($log);
							jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
						}
						if (!$tbl_instance->store()){
							JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
							$log->op_end = JFactory::getDate('now')->toSql(true);
							$log->status = 'abort';
							$log->message = $tbl_instance->getError();
							$flag = $this->setLog($log);
							jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
						}
						
						if(!empty($primary_join)){
							// $insert_join->{$primary_join} = 3;
							$insert_join->{$primary_join} = $tbl_instance->{$primary_join};
						}
					}
					else{
						
						/* if(!empty($primary_join)){
							$insert_join->{$primary_join} = 3;
						} */
						
						if($profile_params->operation==1){
							if($db->insertObject($profile_params->joins->table2[$idx], $insert_join)){
								$in_id = $db->insertid();
								if(!empty($primary_join)){
									$insert_join->{$primary_join} = $in_id;
								}
							}
							else{
								JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $db->stderr();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$db->stderr().'"}');
							}
						}
						else{
							if($is_new){
								if($db->insertObject($profile_params->joins->table2[$idx], $insert_join)){
									$in_id = $db->insertid();
									if(!empty($primary_join)){
										$insert_join->{$primary_join} = $in_id;
									}
								}
								else{
									JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $db->stderr();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$db->stderr().'"}');
								}
							}
							else{
								if($db->updateObject($this->profile->params->table, $insert_join, $primary_join)){
									$in_id = $insert_join->{$primary_join};
								}
								else{
									JLog::add(JText::sprintf('UPDATE_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $db->stderr();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$db->stderr().'"}');
								}
							}
						}
					}
					
					$previous_data[$profile_params->joins->column1[$idx].':'.$profile_params->joins->column2[$idx]] = $insert_join;
				}
			}
			// jexit('stop');
			JLog::add(JText::sprintf('IMPORT_RECORD_COUNT', $n), JLog::INFO, 'com_vdata');
			$log->op_end =  JFactory::getDate(microtime(true))->toSql(true);
			$log->status = 'success';
			$log->message = JText::sprintf('LOG_IMPORT_SUCCESS_MSG', $n);
			$log->iocount = $n;
			$flag = $this->setLog($log);
			jexit('{"result":"success","message":"'.JText::sprintf('LOG_IMPORT_SUCCESS_MSG', $n).'"}');
			
		}
		
		function getFeeds()
		{
			$input 		= JFactory::getApplication()->input;
			$ids 		= $input->get('id', '', 'RAW');
			$columns 	= $input->get('value', '', 'RAW');
			$limit 		= $input->getInt('limit');
			$limitstart = $input->getInt('limitstart');
			$schedule_uid = $input->get('type', '', 'RAW');
			
			$config = $this->getConfig();
			$csv_child = json_decode($config->csv_child);
			
			$db = JFactory::getDbo();
			$query = 'select * from #__vd_schedules where state=1 and uid='.$db->quote($schedule_uid);
			$db->setQuery($query);
			$schedule = $db->loadObject();
			$params = json_decode($schedule->params);
			switch($params->source){
				case 'RSS2':
					$this->getRss2($schedule);
				break;
				case 'RSS1':
					$this->getRss1($schedule);
				break;
				case 'ATOM':
					$this-> getAtom($schedule);
				break;
				case 'SITEMAP':
					$this->getSitemap($schedule);
				break;
				case 'csv':
				case 'CSV':
					if($csv_child->csv_child==2)
						$this->autoExportCsv($schedule);
					else
						$this->autoExportCsv1($schedule);
				break;
				case 'xml':
				case 'XML':
					$this->autoExportXml($schedule);
				break;
				case 'json':
				case 'JSON':
					$this->autoExportJson($schedule);
				break;
				default:
					throw new Exception(JText::_('INVALID_SOURCE'));
			}
			
		}
		
		function getRss2($schedule) {
			$params = json_decode($schedule->params);
			
			$log = new stdClass();
			$log->iotype = 2;
			$log->profileid = $schedule->profileid;
			$log->side = 'site';
			$log->user = JFactory::getUser()->id;
			if($schedule->profileid){
				$db = $this->getDbc();
				$query = 'select * from #__vd_profiles where id='.$schedule->profileid;
				$db->setQuery($query);
				$profile = $db->loadObject();
				$profile_params = json_decode($profile->params);
				$log->table = $profile_params->table;
			}
			$log->message = JText::_('LOG_FEED');
			$log->op_start =  JFactory::getDate(microtime(true))->toSql(true);
			
			
			$xmlWriter = new XMLWriter();
			$xmlWriter->openMemory();
			$xmlWriter->setIndent(true);
			//start document
			$xmlWriter->startDocument('1.0','UTF-8');
			//start root element
			$xmlWriter->startElement('rss');
			$xmlWriter->writeAttribute('version', '2.0');
				//start channel tag
				$xmlWriter->startElement('channel');
					$xmlWriter->writeElement('title', $params->rss2_title);
					$xmlWriter->writeElement('link', $params->rss2_link);
					$xmlWriter->writeElement('description', $params->rss2_desc);
					$columns = json_decode($schedule->columns);
					$rows = $this->getFeedFields($schedule->profileid, $schedule->qry, $columns->fields);
					foreach($rows as $row){
						$xmlWriter->startElement('item');
							$xmlWriter->writeElement('title', $row->{$columns->fields->title});
							
							$feed_link = $row->{$columns->fields->link};
							$xmlWriter->writeElement('link', $feed_link);
							$xmlWriter->writeElement('description', $row->{$columns->fields->desc});
							$xmlWriter->writeElement('author', $row->{$columns->fields->author});
							$xmlWriter->writeElement('pubDate', $row->{$columns->fields->pubdate});
						$xmlWriter->endElement();
					}
				//end channel tag	
				$xmlWriter->endElement();
			//end root element	
			$xmlWriter->endElement();
			//end document
			$xmlWriter->endDocument();
			
			// log hits in #__vd_logs table
			$log->op_end =  JFactory::getDate(microtime(true))->toSql(true);
			$log->status = "success";
			$flag = $this->setLog($log);
			
			header('Content-Type: application/rss+xml');//application/xml
			jexit($xmlWriter->outputMemory());
		}
		
		function getRss1($schedule){
			$params = json_decode($schedule->params);
			$columns = json_decode($schedule->columns);
			$rows = $this->getFeedFields($schedule->profileid, $schedule->qry, $columns->fields);
			
			$log = new stdClass();
			$log->iotype = 2;
			$log->profileid = $schedule->profileid;
			$log->side = 'site';
			$log->user = JFactory::getUser()->id;
			if($schedule->profileid){
				$db = $this->getDbc();
				$query = 'select * from #__vd_profiles where id='.$schedule->profileid;
				$db->setQuery($query);
				$profile = $db->loadObject();
				$profile_params = json_decode($profile->params);
				$log->table = $profile_params->table;
			}
			$log->message = JText::_('LOG_FEED');
			$log->op_start =  JFactory::getDate(microtime(true))->toSql(true);
			
			$xmlWriter = new XMLWriter();
			$xmlWriter->openMemory();
			$xmlWriter->setIndent(true);
			//start document
			$xmlWriter->startDocument('1.0','UTF-8');
				//start root element
				$xmlWriter->startElement('rdf:RDF');
				$xmlWriter->writeAttribute('xmlns:rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
				$xmlWriter->writeAttribute('xmlns', 'http://purl.org/rss/1.0/');
					//write channel tag
					$xmlWriter->startElement('channel');
					$xmlWriter->writeAttribute('rdf:about', 'http://www.xml.com/xml/news.rss');
						$xmlWriter->writeElement('title', $params->rss1_title);
						$xmlWriter->writeElement('link', $params->rss1_link);
						$xmlWriter->writeElement('description', $params->rss1_desc);
						//image tag
							
						//write items tag
						$xmlWriter->startElement('items');
							$xmlWriter->startElement('rdf:Seq');
							foreach($rows as $row){
								$xmlWriter->startElement('rdf:li');
								$xmlWriter->writeAttribute('rdf:resource', $row->{$columns->fields->link});
								$xmlWriter->endElement();
							}
							$xmlWriter->endElement();
						$xmlWriter->endElement();
					
					//end channel tag
					$xmlWriter->endElement();
					
					//write articles
					foreach($rows as $row){
						$xmlWriter->startElement('item');
						$xmlWriter->writeAttribute('rdf:about', $row->{$columns->fields->link});
							$xmlWriter->writeElement('title', $row->{$columns->fields->title});
							
							$feed_link = $row->{$columns->fields->link};
							$xmlWriter->writeElement('link', $feed_link);
							$xmlWriter->writeElement('description', $row->{$columns->fields->desc});
						$xmlWriter->endElement();
					}
					
				//end root element
				$xmlWriter->endElement();
			//end document
			$xmlWriter->endDocument();
			
			// log hits in #__vd_logs table
			$log->op_end =  JFactory::getDate(microtime(true))->toSql(true);
			$log->status = "success";
			$flag = $this->setLog($log);
			
			header('Content-Type: application/xml');//application/xml,application/rdf+xml
			jexit($xmlWriter->outputMemory());
		}
		
		function getAtom($schedule){
			$params = json_decode($schedule->params);
			
			$log = new stdClass();
			$log->iotype = 2;
			$log->profileid = $schedule->profileid;
			$log->side = 'site';
			$log->user = JFactory::getUser()->id;
			if($schedule->profileid){
				$db = $this->getDbc();
				$query = 'select * from #__vd_profiles where id='.$schedule->profileid;
				$db->setQuery($query);
				$profile = $db->loadObject();
				$profile_params = json_decode($profile->params);
				$log->table = $profile_params->table;
			}
			$log->message = JText::_('LOG_FEED');
			$log->op_start =  JFactory::getDate(microtime(true))->toSql(true);
			
			$xmlWriter = new XMLWriter();
			$xmlWriter->openMemory();
			$xmlWriter->setIndent(true);
			//start document
			$xmlWriter->startDocument('1.0','UTF-8');
				//start root element
				$xmlWriter->startElement('feed');
				$xmlWriter->writeAttribute('xmlns', 'http://www.w3.org/2005/Atom');
				$xmlWriter->writeElement('title', $params->atom_title);
				$xmlWriter->writeElement('updated', JFactory::getDate());
				$xmlWriter->writeElement('category', $params->atom_category);	

				$xmlWriter->startElement('author');
					$xmlWriter->writeElement('name', $params->atom_author_name);
					$xmlWriter->writeElement('email', $params->atom_author_email);
				$xmlWriter->endElement();
				
				$columns = json_decode($schedule->columns);
				$rows = $this->getFeedFields($schedule->profileid, $schedule->qry, $columns->fields);
				foreach($rows as $row){
					//start entry tag
					$xmlWriter->startElement('entry');
						$xmlWriter->writeElement('title', $row->{$columns->fields->title});
						$xmlWriter->startElement('link');
							$feed_link = $row->{$columns->fields->link};
							$xmlWriter->writeAttribute('href', $feed_link);
						$xmlWriter->endElement();
						$xmlWriter->writeElement('summary', $row->{$columns->fields->summary});
						$xmlWriter->writeElement('updated', $row->{$columns->fields->updated});
						$xmlWriter->writeElement('id', $row->{$columns->fields->id});
					//end entry tag
					$xmlWriter->endElement();
				}
				//end root element
				$xmlWriter->endElement();
			//end document
			$xmlWriter->endDocument();
			
			// log hits in #__vd_logs table
			$log->op_end =  JFactory::getDate(microtime(true))->toSql(true);
			$log->status = "success";
			$flag = $this->setLog($log);
			
			header('Content-Type: application/xml');//application/xml,application/atom+xml
			jexit($xmlWriter->outputMemory());
		}
		
		function getSitemap($schedule){
			
			$params = json_decode($schedule->params);
			
			$log = new stdClass();
			$log->iotype = 2;
			$log->profileid = $schedule->profileid;
			$log->side = 'site';
			$log->user = JFactory::getUser()->id;
			if($schedule->profileid){
				$db = $this->getDbc();
				$query = 'select * from #__vd_profiles where id='.$schedule->profileid;
				$db->setQuery($query);
				$profile = $db->loadObject();
				$profile_params = json_decode($profile->params);
				$log->table = $profile_params->table;
			}
			$log->message = JText::_('LOG_FEED');
			$log->op_start =  JFactory::getDate(microtime(true))->toSql(true);
			
			$xmlWriter = new XMLWriter();
			$xmlWriter->openMemory();
			//start document
			$xmlWriter->startDocument('1.0', 'UTF-8');
			$xmlWriter->setIndent(true);
			//start root element
			$xmlWriter->startElement("urlset");
				$xmlWriter->writeAttribute("xmlns", "http://www.sitemaps.org/schemas/sitemap/0.9");
				$xmlWriter->writeAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
				$xmlWriter->writeAttribute("xsi:schemaLocation", "http://www.sitemaps.org/schemas/sitemap/0.9  http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd");
				
				$columns = json_decode($schedule->columns);
				$rows = $this->getFeedFields($schedule->profileid, $schedule->qry, $columns->fields);
				foreach($rows as $row){
					//start parent tag
					$xmlWriter->startElement("url");
						$xmlWriter->writeElement("loc", $row->{$columns->fields->loc});
						$xmlWriter->writeElement("lastmod", date("Y-m-d", strtotime($row->{$columns->fields->lastmod})));
						$xmlWriter->writeElement("changefreq", $columns->fields->changefreq);
						$xmlWriter->writeElement("priority", $columns->fields->priority);
					//end parent tag
					$xmlWriter->endElement();
				}
			//end root element
			$xmlWriter->endElement();
			//end document
			$xmlWriter->endDocument();
			
			// log hits in #__vd_logs table
			$log->op_end =  JFactory::getDate(microtime(true))->toSql(true);
			$log->status = "success";
			$flag = $this->setLog($log);
			
			header('Content-Type: text/xml');
			jexit( $xmlWriter->outputMemory() );//flush
		}
		
		function getFeedFields($profileid, $qry, $columns)
		{
			$db = JFactory::getDbo();
			if($profileid!=0){
				$query = 'select * from #__vd_profiles where id='.$profileid;
				$db->setQuery($query);
				$profile = $db->loadObject();
				$profile_params = json_decode($profile->params);
				if($profile->quick==1){
					$query = 'select * from '.$profile_params->table;
					$db->setQuery($query);
					$rows = $db->loadObjectList();
				}
				else{
					$query = "SELECT * FROM ".$db->quoteName($profile_params->table);
					if( !empty($profile_params->filters) && property_exists($profile_params->filters, 'column') ){
						$query .= " WHERE ";
						$m = $n = -1;
						foreach($profile_params->filters->column as $j=>$column){
							
							$query .= $db->quoteName($column)." ";
							if($profile_params->filters->cond[$j]=='between' || $profile_params->filters->cond[$j]=='notbetween'){
								$n = ($n<0)?0:$n+1;
							}
							else{
								$m = ($m<0)?0:$m+1;
								$value = $this->getQueryFilteredValue($profile_params->filters->value[$m]);
							}
							
							if($profile_params->filters->cond[$j]=='in'){
								$query .= " IN ( ".$db->quote($value)." )";
							}
							elseif($profile_params->filters->cond[$j]=='notin'){
								// $query .= " NOT IN ( ".$db->quote($value)." )";
								$query .= " NOT IN ( ".$value." )";
							}
							elseif($profile_params->filters->cond[$j]=='between'){
								$value1 = $this->getQueryFilteredValue($profile_params->filters->value1[$n]);
								$value2 = $this->getQueryFilteredValue($profile_params->filters->value2[$n]);
								$query .= " BETWEEN ".$db->quote($value1)." AND ".$db->quote($value2);
							}
							elseif($profile_params->filters->cond[$j]=='notbetween'){
								$value1 = $this->getQueryFilteredValue($profile_params->filters->value1[$n]);
								$value2 = $this->getQueryFilteredValue($profile_params->filters->value2[$n]);
								$query .= " NOT BETWEEN ".$db->quote($value1)." AND ".$db->quote($value2);
							}
							elseif($profile_params->filters->cond[$j]=='like'){
								$query .= " LIKE ".$db->quote($value);
							}
							elseif($profile_params->filters->cond[$j]=='notlike'){
								$query .= " NOT LIKE ".$db->quote($value);
							}
							elseif($profile_params->filters->cond[$j]=='regexp'){
								$query .= " REGEXP ".$db->quote($profile_params->filters->value[$j]);
							}
							else{
								$query .= $profile_params->filters->cond[$j]." ".$db->quote($value);
							}
							
							if($j < (count($profile_params->filters->column)-1)){
								$query .= " ".$profile_params->filters->op." ";
							}
						}
					}
					if(!empty($profile_params->groupby)){
						$query .= " GROUP BY ".$db->quoteName($profile_params->groupby);
					}
					if(!empty($profile_params->orderby)){
						$query .= " ORDER BY ".$db->quoteName($profile_params->orderby)." ".$profile_params->orderdir;
					}
					$db->setQuery($query);
					$rows = $db->loadObjectList();
					//if field is already defined in profile use defined value
					
					foreach($profile_params->fields as $defcol=>$field){
						if( ($field->data=='defined') ){
							foreach($columns as $key=>$column) {//loop throught schedule columns
								if($column==$defcol){ //if defined columns exists in schedule columns
									foreach($rows as $row){
										$defined = $field->default;
										
										$hd_local_pattern = '/@vdLocal:[\w]*[.?\w]*/';
										$hd_php_pattern = '/@vdPhp:(.*?)$/';
										$hd_sql_pattern = '/@vdSql:(.*?)$/';
										// $hd_remote_pattern = '/@vdRemote:[\w]*[.?\w]*/';
										
										$hdlocal = preg_match_all($hd_local_pattern, $defined, $local_matches);
										if( ($hdlocal!==FALSE) ){
											foreach($local_matches[0] as $mk=>$match){
												if(!empty($match)){
													$fn = explode(':', $match);
													if(!empty($fn[1])){
														$info = explode('.', $fn[1]);
														if(count($info)==1){
															if(property_exists($row, $info[0])){
																$defined = preg_replace('/'.$match.'/', $row->$info[0], $defined);
															}
														}
														elseif(count($info)==2){
																if(!empty($info[0]) && !empty($info[1]) && property_exists($profile_params->fields, $info[0]) && ($profile_params->fields->{$info[0]}->data=='reference') ) {//&& in_array($info[1], $profile_params->fields->{$info[0]}->reftext)
																if(property_exists($row, $info[0])){
																$query = 'select '.$db->quoteName($info[1]).' from '.$db->quoteName($profile_params->fields->{$info[0]}->table).' where '.$db->quoteName($profile_params->fields->{$info[0]}->on).'='.$db->quote($row->$info[0]);
																$db->setQuery($query);
																$rdata = $db->loadResult();
																$defined = preg_replace('/'.$match.'/', $rdata, $defined);
																}
															}
														}
													}
												}
											}
										}
										// $hdphp = preg_match_all($hd_php_pattern, $defined, $php_matches);
										$hdphp = preg_match($hd_php_pattern, $defined, $php_matches);
										if( ($hdphp!==FALSE) && !empty($php_matches[0])){
											$temp = $php_matches[0];
											$func = explode(':',$temp, 2);
											if(!empty($func[1])){
												$response = @eval("return $func[1];");
												if (error_get_last()){
													//throw error and abort execution
													// echo 'custom error';
													//Or log error
													//(error_get_last());
												}
												else{
													$defined = $response;
												}
											}
										}
										// $hdsql = preg_match_all($hd_sql_pattern, $defined, $sql_matches);
										$hdsql = preg_match($hd_sql_pattern, $defined, $sql_matches);
										if( ($hdsql!==FALSE) && !empty($sql_matches[0])){
											$temp = $sql_matches[0];
											$func = explode(':',$temp, 2);
											if(!empty($func[1])){
												$query = 'select '.$func[1];
												try{
													$db->setQuery($query);
													$defined = $db->loadResult();
												}
												catch(Exception $e){
													$err = $e->getMessage();
													//throw error and abort execution
													// echo 'custom error';
													//Or log error
													//($e->getMessage());
												}
												
											}
										}
										$row->{$column} = $defined;
									}
								}
							}
						}
					}
					
					foreach($columns as $key=>$column) {
						$ar = explode(':', $column);
						if(count($ar)==2) {
							foreach($rows as $row) {
								$query = 'select '.$db->quoteName($ar[1]).' from '.$db->quoteName($profile_params->fields->{$ar[0]}->table).' where '.$db->quoteName($profile_params->fields->{$ar[0]}->on).'='.$db->quote($row->{$ar[0]});
								$db->setQuery($query);
								$data = $db->loadResult();
								$row->{$column} = $data;
							}
						}
					}
					
				}
			}
			else{
				$db->setQuery($qry);
				$rows = $db->loadObjectList();
			}
			
			return $rows;
			
		}
		
		
		function setImport()
		{
			$input 		= JFactory::getApplication()->input;
			$schedule_uid = $input->get('type', '', 'RAW');
			
			$config = $this->getConfig();
			$csv_child = json_decode($config->csv_child);
			
			$db = JFactory::getDbo();
			$query = 'select * from #__vd_schedules where state=1 and uid='.$db->quote($schedule_uid);
			$db->setQuery($query);
			$schedule = $db->loadObject();
			$params = json_decode($schedule->params);
			
			switch($params->source){
				case 'csv':
					if($csv_child->csv_child==2)
						$flag = $this->autoImportCsv($schedule);
					else
						$flag = $this->autoImportCsv1($schedule);
				break;
				case 'xml':
					$flag = $this->autoImportXml($schedule);
				break;
				case 'json':
					$flag = $this->autoImportJson($schedule);
				break;
				case 'remote':
					$flag = $this->autoImportRemote($schedule);
				break;
				default:
			}
		}
		
		function autoImportCsv($schedule){
			$log = new stdClass();
			$log->iotype = 0;
			$log->profileid = $schedule->profileid;
			$log->cronid = $schedule->id;
			$log->side = 'cron';
			$log->user = JFactory::getUser()->id;
			$op_start = JFactory::getDate('now')->format('m-d-Y_hia');
			$log_file = 'com_vdata_plg_custom'.'_'.$op_start.'.txt';
			
			$config = $this->getConfig();
			if($config->logging){
				$logger = $this->initializeLogger();
			}
			
			$log->logfile = $log_file;
			$log->op_start =  JFactory::getDate(microtime(true))->toSql(true);
			
			$params = json_decode($schedule->params);
			$schedule_cols = json_decode($schedule->columns);
			
			$db = $this->getDbc();
			//fetch profile details
			$query = 'select * from #__vd_profiles where id='.$schedule->profileid;
			$db->setQuery($query);
			$profile = $db->loadObject();
			$profile_params = json_decode($profile->params);
			
			$delimiter = $this->getDelimiter();
			$enclosure = $this->getEnclosure();
			
			$query = 'SHOW KEYS FROM '.$db->quoteName($profile_params->table).' WHERE Key_name = "PRIMARY"';
			$db->setQuery( $query );
			$key = $db->loadAssocList('Column_name');
			if(property_exists($profile_params, 'unqkey') && !empty($profile_params->unqkey))
				$primary = $profile_params->unqkey;
			elseif(!empty($key))
				$primary = array_keys($key);
			else
				$primary = null;
			
			$path = $params->path;
			if($params->server=='local'){
				$path = JPATH_ROOT.'/'.$path;
			}
			elseif($params->server=='absolute'){
				$newPath = JPATH_ROOT.'/'.'components/com_vdata/uploads/'.$profile->title.'.csv';
				if(!copy($path, $newPath)){
					$copy_error = error_get_last();
					jexit('{"result":"error","error":"'.$copy_error['message'].'"}');
				}
				$path = $newPath;
			}
			else{
				//copy remote file using ftp
				$inputFtp = (array)$params->ftp;
				if( empty($inputFtp['ftp_host']) || empty($inputFtp['ftp_user']) || empty($inputFtp['ftp_pass']) || empty($inputFtp['ftp_file']) ){
					jexit('{"result":"error","error":"'.JText::_('VDATA_EMPTY_FTP_CREDENTIALS').'"}');
				}
				$ext = strrchr($inputFtp['ftp_file'], '.');
				if($ext <> '.csv'){
					jexit('{"result":"error","error":"'.JText::_('PLZ_UPLOAD_CSV_FILE').'"}');
				}
				$ftp = $this->ftpConnection($inputFtp);
				if($ftp->result=='error'){
					jexit('{"result":"error","error":"'.$ftp->error.'"}');
				}
				$remotePath = rtrim($inputFtp['ftp_directory'], '/').'/'.$inputFtp['ftp_file'];
				$path = JPATH_ROOT.'/'.'components/com_vdata/uploads/'.$inputFtp['ftp_file'].'.csv';
				$ftpHelper = $ftp->ftpHelper;
				if(!$ftpHelper->download($path, $remotePath)){
					jexit('{"result":"error","error":"'.$ftpHelper->getMessage().'"}');
				}
			}
			
			try{
				$fp = @fopen($path, "r");
				if ( !$fp ) {
					$open_error = error_get_last();
					jexit('{"result":"error","error":"'.$open_error['message'].'"}');
				}
			} catch(Exception $e){
				jexit('{"result":"error","error":"'.$e->getMessage().'"}');
			}
			
			$log->iofile = $path;
			$log->table = $profile_params->table;
			
			//log info
			JLog::add(JText::sprintf('IMPORT_PROFILE', $profile->title), JLog::INFO, 'com_vdata');
			JLog::add(JText::sprintf('IMPORT_TABLE', $profile_params->table), JLog::INFO, 'com_vdata');
			JLog::add(JText::sprintf('IMPORT_OPERATION', $profile_params->operation), JLog::INFO, 'com_vdata');
			JLog::add(JText::sprintf('IMPORT_FORMAT', 'CSV'), JLog::INFO, 'com_vdata');
			
			$dids = array();
			
			$query = 'show fields FROM '.$db->quoteName($profile_params->table);
			$db->setQuery( $query );
			$cols = $db->loadObjectList();
			$n = 0;
			if( property_exists($schedule_cols, 'quick') ) {
				$header = fgetcsv($fp, 100000, $delimiter, $enclosure);
				while(($data = fgetcsv($fp, 100000, $delimiter, $enclosure))!==FALSE){
					$insert = new stdClass();
					$isNew = true;
					$oldData = array();
					if($profile_params->operation==3){
						//apply primary key and custom filters
						$quick_fields = $schedule_cols->fields;
						$where = array();
						$base_in_id = array();
						if(isset($profile_params->unqkey)){
							foreach($profile_params->unqkey as  $key){
								if($quick_fields[$key]!=""){
									$where[] = "(".$db->quoteName($key).' = '.$db->quote($data[$quick_fields[$key]]).")";
									$base_in_id[$key] = $data[$quick_fields[$key]];
								}
							}
						}
						if(!empty($where)){
							$statement = 'SELECT count(*) FROM ' . $db->quoteName($profile_params->table) . ' WHERE %s';
							$query = $db->getQuery(true);
							$db->setQuery(sprintf($statement, implode(' AND ', $where)));
							$result = $db->loadResult();
							if($result>0 && !empty($base_in_id)){
								$dids[] = $base_in_id;
							}
						}
					}
					else{
						if($schedule_cols->quick==1){
							foreach($cols as $i=>$col){
								if(array_key_exists($i, $data)){
									$insert->{$col->Field} = $data[$i];
								}
							}
						}
						else{
							$quick_fields = $schedule_cols->fields;
							foreach($quick_fields as $k=>$field){
								if( ($field!="") && array_key_exists($field, $data) ){
									$insert->{$k}=$data[$field];
								}
							}	
						}
					}
					if(!empty($primary)){
						foreach($primary as $keyCol){
							if(isset($insert->{$keyCol})){
							$where[] = $db->quoteName($keyCol).'='.$db->quote($insert->{$keyCol});
							}
						}
						if(!empty($where)){
							$query = 'SELECT * FROM '.$db->quoteName($profile_params->table).' WHERE '.implode(' AND ', $where);
							$db->setQuery( $query );
							$oldData = $db->loadAssoc();
							$isNew = empty($oldData) ? true : false;
						}
					}
					if($profile_params->operation==3){
						//delete data operation
					}
					elseif($profile_params->operation==1){
						if(!$isNew){
							JLog::add(JText::sprintf('RECORD_ALREADY_EXISTS',implode(',',$where)), JLog::ERROR, 'com_vdata');
							continue;
						}
						//capture events before insert
						if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'before') ){
							
							//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $profile_params->events->before)), JLog::INFO, 'com_vdata');
							$this->profile = new stdClass();
							$this->profile->params = $profile_params;
							$response = $this->captureEventsOnRecord( $insert, 'before', $isNew , array('pk'=>$primary ,'old'=>$oldData));
							if(in_array(false, $response)){
								JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
								continue;
							}
								
						}
						if(!$db->insertObject($profile_params->table, $insert)){
							JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
							//log error
							$log->op_end = JFactory::getDate('now')->toSql(true);
							$log->status = 'abort';
							$log->message = $db->stderr();
							$flag = $this->setLog($log);
							jexit('{"result":"error","error":"'.$db->stderr().'"}');
						}
						if($this->getAffected($db) > 0){
							$n++;
							//capture events after insert
							if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'after') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $profile_params->events->after)), JLog::INFO, 'com_vdata');
								$this->profile = new stdClass();
								$this->profile->params = $profile_params;
								$response = $this->captureEventsOnRecord( $insert, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
							}
						}
					}
					else{
						if(empty($primary)){
							$log->op_end = JFactory::getDate('now')->toSql(true);
							$log->status = 'abort';
							$log->message = JText::_('PRIMARY_KEY_NOT_FOUND');
							$flag = $this->setLog($log);
							jexit('{"result":"error","error":"'.JText::_('PRIMARY_KEY_NOT_FOUND').'"}');
						}
						else{
							foreach($primary as $pkey){
								if(!isset($insert->{$pkey}) || empty($insert->{$pkey})){
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = JText::_('PRIMARY_KEY_NOT_FOUND');
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.JText::_('PRIMARY_KEY_NOT_FOUND').'"}');
								}
							}
						}
						
						//capture events before update
						if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'before') ){
							//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $profile_params->events->before)), JLog::INFO, 'com_vdata');
							$this->profile = new stdClass();
							$this->profile->params = $profile_params;
							$response = $this->captureEventsOnRecord( $insert, 'before',$isNew , array('pk'=>$primary ,'old'=>$oldData));
							if(in_array(false, $response)){
								JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
								continue;
							}
						}
						if($isNew){
							JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
							if(!$db->insertObject($profile_params->table, $insert)){
								JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
								//log error
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $db->stderr();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$db->stderr().'"}');
							}
						}
						else{
							if(!$db->updateObject($profile_params->table, $insert, $primary)){
								JLog::add(JText::sprintf('UPDATE_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $db->stderr();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$db->stderr().'"}');
							}
						}
						$base_in_id = array();
						foreach($primary as $pkey){
							if(isset($insert->{$pkey})){
								$base_in_id[$pkey] = $insert->{$pkey};
							}
						}
						$dids[] = $base_in_id;
						
						if($this->getAffected($db)> 0){
							$n++;
							//capture events after update
							if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'after') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $profile_params->events->after)), JLog::INFO, 'com_vdata');
								$this->profile = new stdClass();
								$this->profile->params = $profile_params;
								$response = $this->captureEventsOnRecord( $insert, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
							}
						}
					}
					
				}
			}
			else{
				$images_import = array();
				if(property_exists($schedule_cols,'images'))
					$images_import = json_decode(json_encode($schedule_cols->images), true);
				$csvfields = $schedule_cols->fields;
				$heading = $data = fgetcsv($fp, 100000, $delimiter, $enclosure);
				while(($data = fgetcsv($fp, 100000, $delimiter, $enclosure))!==FALSE){
					$insert = new stdClass();
					$cached_data = new stdClass();
					$isNew = true;
					$oldData = array();
					if($profile_params->operation==3){
						//apply primary key and custom filters
						$where = array();
						$base_in_id = array();
						if(isset($profile_params->unqkey)){
							foreach($profile_params->unqkey as  $key){
								if($csvfields[$key]!=""){
									$where[] = "(".$db->quoteName($key).' = '.$db->quote($data[$csvfields[$key]]).")";
									$base_in_id[$key] = $data[$csvfields[$key]];
								}
							}
						}
						if(!empty($where)){
							$statement = 'SELECT count(*) FROM ' . $db->quoteName($profile_params->table) . ' WHERE %s';
							$query = $db->getQuery(true);
							$db->setQuery(sprintf($statement, implode(' AND ', $where)));
							$result = $db->loadResult();
							if($result>0 && !empty($base_in_id)){
								$dids[] = $base_in_id;
							}
						}
					}
					else{
						foreach($profile_params->fields as $column=>$field){
							switch($field->data) {
								case 'file':
									if($csvfields->{$column}==""){
										break;
									}
									if($field->format == 'string'){
										$str = $data[$csvfields->{$column}];
										if($field->type=='striptags'){
											$str = strip_tags($str);
										}
										elseif( ($field->type=='chars') && (JString::strlen($str)> $field->val) ){
											$str = JString::substr($str, 0, $field->val);
										}
										$insert->{$column} = $str;
										$cached_data->{$column} = $str;
									}
									elseif($field->format == "date"){
										// $date = JFactory::getDate($data[$csvfields->{$column}]);
										$date = new DateTime($data[$csvfields->{$column}]);
										if(!empty($field->type)){
											// $date = $date->toFormat($field->type);
											$date = $date->format($field->type);
										}	
										$insert->{$column} = $date;
										$cached_data->{$column} = $date;
									}
									elseif($field->format == "number"){
										$insert->{$column} = (int)$data[$csvfields->{$column}];
										$cached_data->{$column} = (int)$data[$csvfields->{$column}];
									}
									elseif($field->format == "urlsafe"){
										$insert->{$column} = JFilterOutput::stringURLSafe($data[$csvfields->{$column}]);
										$cached_data->{$column} = JFilterOutput::stringURLSafe($data[$csvfields->{$column}]);
									}
									elseif($field->format == "encrypt"){
										$cached_data->{$column} = $data[$csvfields->{$column}];
										if($field->type=='bcrypt'){
											$insert->{$column} = JUserHelper::hashPassword( $data[$csvfields->{$column}] );
										}
										else{
											$insert->{$column} = JUserHelper::getCryptedPassword( $data[$csvfields->{$column}],'',$field->type );
										}
									}
									elseif($field->format=='email'){
										if(function_exists('filter_var')){
											$str = filter_var($str, FILTER_VALIDATE_EMAIL);
											if($str==FALSE){
												JLog::add(JText::sprintf('VDATA_EMAIL_VALIDATION_FAILED', $column), JLog::ERROR, 'com_vdata');
												unset($insert->{$column});
											}
										}
									}
									if($field->format == "image"){
										$filename = $insert->{$column};$destination = rtrim($field->location,'/').'/'. basename($filename);
											
										$image_source=$path_type='';
										if(isset($images_import['root'][$column])){
											$image_source = isset($images_import['root'][$column]['image_source'])?$images_import['root'][$column]['image_source']:'';
											$path_type = isset($images_import['root'][$column]['path_type'])?$images_import['root'][$column]['path_type']:'';											
										}	
										if(!$image = $this->uploadImage($filename,$destination,$image_source,$path_type,$err)){
													
											$insert->{$column} = $cached_data->{$column} = $destination;
											jexit('{"result":"error","error":"'.$err.'"}');
										}
									} 
								break;
								case 'defined':
									$defined = $field->default;
									
									$hd_local_pattern = '/@vdLocal:[\w]*[.?\w]*/';
									$hd_php_pattern = '/@vdPhp:(.*?)$/';
									$hd_sql_pattern = '/@vdSql:(.*?)$/';
									// $hd_remote_pattern = '/@vdRemote:[\w]*[.?\w]*/';
									
									$hdlocal = preg_match_all($hd_local_pattern, $defined, $local_matches);
									if( ($hdlocal!==FALSE) ){
										foreach($local_matches[0] as $mk=>$match){
											if(!empty($match)){
												$fn = explode(':', $match);
												if( ( $fn[1]!="" ) ){
													$info = explode('.', $fn[1]);
													if(count($info)==1){
														if(array_key_exists($info[0], $data)){
															$defined = preg_replace('/'.$match.'/', $data[$info[0]], $defined);
														}
													}
													elseif(count($info)==2){
															if( !empty($info[0]) && !empty($info[1]) && property_exists($profile_params->fields, $info[0]) && ($profile_params->fields->{$info[0]}->data=='reference') && in_array($info[1], $profile_params->fields->{$info[0]}->reftext) ) {//&& in_array($info[1], $profile_params->fields->{$info[0]}->reftext)
															
															$defined = preg_replace('/'.$match.'/', $data[$csvfields->{$info[0]}->{$info[1]}], $defined);
														}
													}
												}
											}
										}
									}
									// $hdphp = preg_match_all($hd_php_pattern, $defined, $php_matches);
									$hdphp = preg_match($hd_php_pattern, $defined, $php_matches);
									if( ($hdphp!==FALSE) && !empty($php_matches[0])){
										$temp = $php_matches[0];
										$func = explode(':',$temp, 2);
										if(!empty($func[1])){
											$response = @eval("return $func[1];");
											if (error_get_last()){
												//throw error and abort execution
												// echo 'custom error';
												//Or log error
												//(error_get_last());
											}
											else{
												$defined = $response;
											}
										}
									}
									// $hdsql = preg_match_all($hd_sql_pattern, $defined, $sql_matches);
									$hdsql = preg_match($hd_sql_pattern, $defined, $sql_matches);
									if( ($hdsql!==FALSE) && !empty($sql_matches[0])){
										$temp = $sql_matches[0];
										$func = explode(':',$temp, 2);
										if(!empty($func[1])){
											$query = 'select '.$func[1];
											try{
												$db->setQuery($query);
												$defined = $db->loadResult();
											}
											catch(Exception $e){
												$err = $e->getMessage();
												//throw error and abort execution
												// echo 'custom error';
												//Or log error
												//($e->getMessage());
											}
										}
									}
									
									$insert->{$column} = $defined;
									$cached_data->{$column} = $defined;
								break;
								case 'reference':
									//one to one relation							
									$query = 'SHOW KEYS FROM '.$db->quoteName($field->table).' WHERE Key_name = "PRIMARY"';
									$db->setQuery( $query );
									$key_ref = $db->loadObjectList();
									if(!empty($key_ref) && (count($key_ref)==1) )
										$primary_ref = $key_ref[0]->Column_name;
									else
										$primary_ref = null;
									$insert_ref = new stdClass();
									foreach($field->reftext as $i=>$ref){
										if($csvfields->{$column}->{$ref}!=""){
											$insert_ref->{$ref} = $data[$csvfields->{$column}->{$ref}];
										}
									}
									
									$insert_ref_val = (array)$insert_ref;
									if(!empty($insert_ref_val)){
										$query = "select ".$db->quoteName($field->on)." from ".$db->quoteName($field->table)." where 1=1";
										foreach($insert_ref as $ref_key=>$ref_val){
											$query .= " and ".$db->quoteName($ref_key)." = ".$db->quote($ref_val);
										}
										$db->setQuery($query);
										$ref_value = $db->loadResult();
										if(!empty($ref_value)){
											$insert->{$column} = $ref_value;
											$cached_data->{$column} = $ref_value;
										}
										else{
											JLog::add(JText::sprintf('LOCAL_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
										}
									}
									else{
										JLog::add(JText::sprintf('REMOTE_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
									}
								break;
							
							}				
						}
					}
					//check if record already exists
					$where = array();
					if( !empty($primary)  ){
						foreach($primary as $keyCol){
							if(isset($insert->{$keyCol})){
							$where[] = $db->quoteName($keyCol).'='.$db->quote($insert->{$keyCol});
							}
						}
						if(!empty($where)){
							$query = 'SELECT * FROM '.$db->quoteName($profile_params->table).' WHERE '.implode(' AND ', $where);
							$db->setQuery( $query );
							$oldData = $db->loadAssoc();
							$isNew = empty($oldData) ? true : false;
						}
					}
					
					if($profile_params->operation==3){
						//delete data operation
					}
					elseif($profile_params->operation==1){
						//base table record
						if(!$isNew){
							//if record exists log record and move pointer to next base record
							JLog::add(JText::sprintf('RECORD_ALREADY_EXISTS',implode(',',$where)), JLog::ERROR, 'com_vdata');
							if(empty($profile_params->joins->table2)){
								//if no child record next record is base record
								$child_pos = ftell($fp);
							}
							else{
								//calculate next base record position as $child_pos and move pointer to that position
								$cpos = ftell($fp);
								if( ($cpos_blank = fgetcsv($fp, 100000, $delimiter, $enclosure))  !== FALSE ){
									if(!$this->check_blank($cpos_blank)){
										fseek($fp, $cpos);
										$child_pos = ftell($fp);
										continue;
									}
									else{
										fseek($fp, $cpos);
										$fchild = 0;
										$blnk_count = 0;
										while(($nxt_bs=fgetcsv($fp, 100000, $delimiter, $enclosure)) !== FALSE){
											$fchild++;
											$blnk = $this->check_blank($nxt_bs);
											if($blnk){
												$blnk_count+=1;
											}
											elseif( !$blnk && ($blnk_count==1) && ($fchild != 2) ){
												fseek($fp, $flg_point);
												$child_pos = ftell($fp);
												break;
											}
											else{$blnk_count = 0;}
											$flg_point = ftell($fp);
										}
										//if it is last base record with child then there is no single blank
										if($nxt_bs===FALSE || $nxt_bs===NULL){
											$child_pos = ftell($fp);
										}
									}
								}
								else{
									//last base record with no child record
									fseek($fp, $cpos);
									continue;
								}
							}
							continue;
						}
						else{
							
							//capture events before insert
							if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'before') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $profile_params->events->before)), JLog::INFO, 'com_vdata');
								$this->profile = new stdClass();
								$this->profile->params = $profile_params;
								$response = $this->captureEventsOnRecord( $insert, 'before', $isNew , array('pk'=>$primary ,'old'=>$oldData));
								if(in_array(false, $response)){
									JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
									continue;
								}
							}
							if($db->insertObject($profile_params->table, $insert)){
								//Method to get the auto-incremented column value from the last INSERT statement.
								if(count($primary)==1){
									$base_in_id = $db->insertid();
								}
								else{
									$base_in_id = array();
									foreach($primary as $pkey){
										if(isset($insert->{$pkey})){
											$base_in_id[$pkey] = $insert->{$pkey};
										}
									}
								}
								
								if(!empty($primary)){
									// $insert->{$primary} = $db->insertid();
									if(count($primary)==1){
										$insert->{$primary[0]} = $db->insertid();
									}
								}
							}
							else{
								JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $db->stderr();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$db->stderr().'"}');
							}
							if($this->getAffected($db)> 0){
								$n++;
								//capture events after insert
								if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'after') ){
									if(!empty($primary)){
										foreach($primary as $pkey){
											if(property_exists($cached_data, $pkey) && isset($base_in_id[$pkey])){
												$cached_data->{$pkey} = $base_in_id[$pkey];
											}
										}
									}
									$this->profile = new stdClass();
									$this->profile->params = $profile_params;
									$response = $this->captureEventsOnRecord( $cached_data, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
								}
							}
							if(!empty($profile_params->joins->table2)){
								//if it's new record and base has no child record then continue with pointer to next base
								$next_base_pointer = $child_pos = ftell($fp);
								if( ($next_base_row = fgetcsv($fp, 100000, $delimiter, $enclosure)) !==FALSE){
									if(!$this->check_blank($next_base_row)){
										fseek($fp, $next_base_pointer);
										continue;
									}
									fseek($fp, $next_base_pointer);
								}
								//if it is last base record with no child data
								
							}

						}	
					}
					else{
						if(empty($primary)){
							$log->op_end = JFactory::getDate('now')->toSql(true);
							$log->status = 'abort';
							$log->message = JText::_('PRIMARY_KEY_NOT_FOUND');
							$flag = $this->setLog($log);
							jexit('{"result":"error","error":"'.JText::_('PRIMARY_KEY_NOT_FOUND').'"}');
						}
						else{
							foreach($primary as $pkey){
								if(!isset($insert->{$pkey}) || empty($insert->{$pkey})){
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = JText::_('PRIMARY_KEY_NOT_FOUND');
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.JText::_('PRIMARY_KEY_NOT_FOUND').'"}');
								}
							}
						}
						$base_in_id = array();
						foreach($primary as $pkey){
							if(isset($insert->{$pkey})){
								$base_in_id[$pkey] = $insert->{$pkey};
							}
						}
						$dids[] = $base_in_id;
						
						if($isNew){
							JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
							//move pointer to next base record before continue
							if(empty($profile_params->joins->table2))
								$child_pos = ftell($fp);
							else{
								//calculate next base record position as $child_pos and move pointer to that position
								$cpos = ftell($fp);
								if( ($cpos_blank = fgetcsv($fp, 100000, $delimiter, $enclosure))  !== FALSE ){
									if(!$this->check_blank($cpos_blank)){
										fseek($fp, $cpos);
										$child_pos = ftell($fp);
										continue;
									}
									else{
										fseek($fp, $cpos);
										$fchild = 0;
										$blnk_count = 0;
										while(($nxt_bs=fgetcsv($fp, 100000, $delimiter, $enclosure)) !== FALSE){
											$fchild++;
											$blnk = $this->check_blank($nxt_bs);
											if($blnk){
												$blnk_count+=1;
											}
											elseif( !$blnk && ($blnk_count==1) && ($fchild != 2) ){
												fseek($fp, $flg_point);
												$child_pos = ftell($fp);
												break;
											}
											else{
												$blnk_count = 0;
											}
											$flg_point = ftell($fp);
										}
										//if it is last base record with child then there is no single blank
										if($nxt_bs===FALSE || $nxt_bs===NULL){
											$child_pos = ftell($fp);
										}
									}
								}
								else{
									//last base record with no child record
									fseek($fp, $cpos);
									continue;
								}
							}
							continue;
						}
						else
						{
							//capture events before update
							if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'before') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $profile_params->events->before)), JLog::INFO, 'com_vdata');
								$this->profile = new stdClass();
								$this->profile->params = $profile_params;
								$response = $this->captureEventsOnRecord( $insert, 'before',$isNew , array('pk'=>$primary ,'old'=>$oldData));
								if(in_array(false, $response)){
									JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
									continue;
								}
							}
							if($db->updateObject($profile_params->table, $insert, $primary)){
								$base_in_id = array();
								foreach($primary as $pkey){
									if(isset($insert->{$pkey})){
										$base_in_id[$pkey] = $insert->{$pkey};
									}
								}
							}
							else{
								JLog::add(JText::sprintf('UPDATE_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $db->stderr();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$db->stderr().'"}');
							}
							if($this->getAffected($db)> 0){
								$n++;
								//capture events after update
								if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'after') ){	
									if(!empty($primary)){
										foreach($primary as $pkey){
											if(property_exists($cached_data, $pkey) && isset($base_in_id[$pkey])){
												$cached_data->{$pkey} = $base_in_id[$pkey];
											}
										}
									}
									$this->profile = new stdClass();
									$this->profile->params = $profile_params;
									$response = $this->captureEventsOnRecord( $cached_data, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
								}
							}
							if(!empty($profile_params->joins->table2)){
								//if it's new record and base has no child record then continue with pointer to next base
								$next_base_pointer = $child_pos = ftell($fp);
								if( ($next_base_row = fgetcsv($fp, 100000, $delimiter, $enclosure)) !==FALSE){
									if(!$this->check_blank($next_base_row)){
										fseek($fp, $next_base_pointer);
										continue;
									}
									fseek($fp, $next_base_pointer);
								}
								//if it is last base record with no child data
								
							}
						}
					}
					//
					
					//child table record
					$join_field = new stdClass();
					if(!empty($profile_params->joins->table2))
					{
						$index = $child_count = count($profile_params->joins->table2);
						$base_pos = ftell($fp);
						//calculate $next_base_pos
						$nxt_blnk = fgetcsv($fp, 100000, $delimiter, $enclosure);
						if($this->check_blank($nxt_blnk)){
							fseek($fp, $base_pos);
							$fchild = 0;
							$blank_ct = 0;
							while(($next_base = fgetcsv($fp, 100000, $delimiter, $enclosure)) !==FALSE){
									$fchild++;
									$blank_test = $this->check_blank($next_base);
									if($blank_test){
										$blank_ct +=1;
									}
									elseif( !$blank_test && ($blank_ct == 1) && ($fchild != 2) ){
										$next_base_pos = $blnk_pointer;
										// fseek($fp, $base_pos);
										break;
									}
									else{
										$blank_ct = 0;
									}
									$blnk_pointer = ftell($fp);
							}
							//if there is no blank line after the last base record's child
							if($next_base ===FALSE)
								$next_base_pos = ftell($fp);
						}
						fseek($fp, $base_pos);
						
						while($index > 0)
						{
							//import child data starting from last child
							$idx = --$index;
							//
							$blnk_ct = 0;
							$skip_child = FALSE;
							while( ($ct_blank = fgetcsv($fp, 100000, $delimiter, $enclosure)) !==FALSE ){
								if(ftell($fp) < $next_base_pos){
									$blank = $this->check_blank($ct_blank);
									if($blank){	
										$blnk_ct +=1;
									}
									else{
										if($blnk_ct == ($idx+1) ){
											$skip_child = FALSE;
											break;
										}
										$blnk_ct = 0;
									}
								}
								else{
									$skip_child = TRUE;
									break;
								}
							}
							if($ct_blank ===FALSE){
								$skip_child = TRUE;
							}
							$child_pos = $next_base_pos;
							// move to base record
							fseek($fp,$base_pos);
							// check whether to skip child or not
							/* if($skip_child){
								continue;
							} */
							
							$query = 'SHOW KEYS FROM '.$db->quoteName($profile_params->joins->table2[$idx]).' WHERE Key_name = "PRIMARY"';
							$db->setQuery( $query );
							$key_join = $db->loadObjectList();
							if( !empty($key_join) && (count($key_join)==1) ){
								$primary_join = $key_join[0]->Column_name;
							}
							else{
								$primary_join = null;
							}
							
							//if table exist as $join_field property then append join based column
							if(property_exists($join_field,$profile_params->joins->table2[$idx]))
							{
								if(!in_array($join_field->{$profile_params->joins->table2[$idx]}->field, $profile_params->joins->columns[$idx]) )
								{
									array_push($profile_params->joins->columns[$idx], $join_field->{$profile_params->joins->table2[$idx]}->field);
								}
								//add base table primary key field to first child table
								if($idx == 0 && !in_array($profile_params->joins->column2[0], $profile_params->joins->columns[$idx]))
								{
										array_push($profile_params->joins->columns[$idx], $profile_params->joins->column2[0]);
								}
							}
							//set left table and field as $join_field property
							$join_field->{$profile_params->joins->table1[$idx]} = new stdClass();
							$join_field->{$profile_params->joins->table1[$idx]}->field = $profile_params->joins->column1[$idx];
							
							//insert skipped table
							if($skip_child){
								if( property_exists($join_field,$profile_params->joins->table2[$idx]) && property_exists($join_field->{$profile_params->joins->table2[$idx]}, 'val') )
								{
									foreach($join_field->{$profile_params->joins->table2[$idx]}->val as $ck=>$cval){
										$insert_join = new stdClass();
										foreach($profile_params->joins->columns[$idx] as $m=>$key){
											if($join_field->{$profile_params->joins->table2[$idx]}->field==$key)
												$insert_join->$key = $cval;
											if(($idx == 0) && ($profile_params->joins->column2[0]==$key))
												$insert_join->$key = $base_in_id;
										}
										if($db->insertObject($profile_params->joins->table2[$idx], $insert_join) )
										{
											//if dependent column exists in $insert_join then store it else store auto incremented id 
											if(property_exists($insert_join, $profile_params->joins->column2[$idx])){
												$in_id = $insert_join->{$profile_params->joins->column2[$idx]};
											}
											else{
												$in_id = $db->insertid();
											}
										}
										else{
											$log->op_end = JFactory::getDate('now')->toSql(true);
											$log->status = 'abort';
											$log->message = $db->stderr();
											$flag = $this->setLog($log);
											jexit('{"result":"error","error":"'.$db->stderr().'"}');
										}
										//store depended column values in array
										// $join_field->{$profile_params->joins->table1[$idx]}->val[] = $in_id;
									}
								}
								continue;
							}
							
							$blank_count=0;
							//insert data, starting from last child table
							while(($chain_data=fgetcsv($fp, 100000, $delimiter, $enclosure)) !== FALSE)
							{
								$blank = $this->check_blank($chain_data);
								if($blank)
								{
									$blank_count +=1;
								}
								else
								{
									if($blank_count == ($idx+1) )
									{	
										if($profile_params->operation != 1)
										{
											$query = "Delete ".$profile_params->joins->table2[$idx]." FROM ".$profile_params->joins->table2[$idx];
											for($x=$idx; $x>=0; $x--)
											{
												if(($x-1) < 0){$d_table = $profile_params->table;}else{$d_table = $profile_params->joins->table2[($x-1)];}
												$query .= " JOIN ".$d_table." ON ".$profile_params->joins->table2[$x].".".$profile_params->joins->column2[$x]." = ".$d_table.".".$profile_params->joins->column1[$x]."";
											}
											$query .= " WHERE ".$profile_params->table.".".$profile_params->joins->column1[0]."=".$base_in_id;
											$db->setQuery($query);
											if(!$db->execute())
											{
												$log->op_end = JFactory::getDate('now')->toSql(true);
												$log->status = 'abort';
												$log->message = $db->stderr();
												$flag = $this->setLog($log);
												jexit('{"result":"error","error":"'.$db->stderr().'"}');
											}
										}
										$k = 0;
										while(($child_data = fgetcsv($fp, 100000, $delimiter, $enclosure)) !== FALSE)
										{
											//store child's records
											if(!$this->check_blank($child_data))
											{
												$temp = array();
												foreach($chain_data as $ky=>$data)
												{
													if(!empty($data))
													{
														$temp[]=$child_data[$ky];
													}
												}
												$child_data =  $temp;
												if(property_exists($join_field,$profile_params->joins->table2[$idx]))
												{
													//append values related to previously added dependent columns 
													array_push($child_data, $join_field->{$profile_params->joins->table2[$idx]}->val[$k]);
													/* if($idx == 0)
													{
														array_push($child_data, $base_in_id);
													} */
												}
												if( ($idx == 0) && in_array($profile_params->joins->column2[0], $profile_params->joins->columns[$idx]) ) {
													array_push($child_data, $base_in_id);
												}
												
												$insert_join = new stdClass();
												foreach($profile_params->joins->columns[$idx] as $m=>$key)
												{
													//column selected by user,check dependent column exists in posted $csvfields
													if(array_key_exists($key,(array)$csvfields->{$profile_params->joins->table2[$idx]})){
														$insert_join->{$key} = $child_data[$csvfields->{$profile_params->joins->table2[$idx]}->{$key}];
													}
													else{
														$insert_join->{$key} = $child_data[$m];
													}
													
												}
												if($db->insertObject($profile_params->joins->table2[$idx], $insert_join) )
												{
													//if dependent column exists in $insert_join then store it else store auto incremented id 
													if(property_exists($insert_join, $profile_params->joins->column2[$idx])){
														$in_id = $insert_join->{$profile_params->joins->column2[$idx]};
													}
													else{
														$in_id = $db->insertid();
													}
												}
												else{
													$log->op_end = JFactory::getDate('now')->toSql(true);
													$log->status = 'abort';
													$log->message = $db->stderr();
													$flag = $this->setLog($log);
													jexit('{"result":"error","error":"'.$db->stderr().'"}');
												}
												//store depended column values in array
												$join_field->{$profile_params->joins->table1[$idx]}->val[] = $in_id;
												$k++;
											}
											else 
											{
												$k = 0;
												//change to max blank line i.e. from index count
												if($blank_count == $child_count)
												{
													//position of last child record
													$child_pos = ftell($fp);
												}
												//move to base record
												fseek($fp,$base_pos);
												break;
											}
										}
										//if it's last record 
										if($child_data == FALSE)
										{
											$child_pos = ftell($fp);
											//move to base record
											fseek($fp,$base_pos);
										}
										break;
									}
									$blank_count=0;
								}
							}
							
						}
						//move to last child record that is next base record
						fseek($fp, $child_pos);
					}
					else
						$child_pos = ftell($fp);//if no child record then it is next base record
				}
			}
			
			JLog::add(JText::sprintf('IMPORT_RECORD_COUNT', $n), JLog::INFO, 'com_vdata');
			if( ($profile_params->operation==2) || ($profile_params->operation==3) ){
				$deleteQry = 'DELETE FROM '.$db->quoteName($profile_params->table). ' WHERE %s';
				$whereCon = array();
				foreach($dids as $did){
					if(is_array($did)){
						$where = array();
						foreach($did as $pkey=>$pval){
							$where[] = $db->quoteName($pkey).' <> '.$db->quote($pval);
						}
						$whereCon[] = ' ('.implode(' AND ', $where).')';
					}
					else{
						$whereCon[] = ' '.$db->quoteName($primary).' <> '.$db->quote($did);
					}
				}
				//apply delete filter for delete operation
				if($profile_params->operation==3){
					//apply filters
					if(isset($profile_params->filters->column)){
						$filterValues = $this->importDeleteFilter($db, $profile_params);
						if(!empty($filterValues)){
							$whereCon[] = $filterValues;
						}
					}
				}
				if(!empty($whereCon)){
					$db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
					$res = $db->execute();
				}
			}
			//remove copy of remote data file
			$isRemoteFile = ($params->server=='local')? false:true;
			if($isRemoteFile){
				$ul = @unlink($path);
				if($ul===false){
					JLog::add(JText::_('unable to remove local copy of remote data file'), JLog::INFO, 'com_vdata');
				}
			}
			
			//send notification email
			$notifyProfile = ($schedule->profileid!=0)?$profile->title:"";
			$notifyQry = ($schedule->profileid==0)?$schedule->qry:"";
			$notify = $this->sendNotificationEmail( $schedule->iotype,1 , array('schedule_id'=>$schedule->id,'schedule_title'=>$schedule->title,"schedule_qry"=>$notifyQry,'profile_id'=>$schedule->profileid,'profile_title'=>$notifyProfile,"count"=>$n, "format"=>"csv") );
			
			$log->op_end =  JFactory::getDate(microtime(true))->toSql(true);
			$log->status = 'success';
			$log->message = JText::sprintf('LOG_IMPORT_SUCCESS_MSG', $n);
			$log->iocount = $n;
			$flag = $this->setLog($log);
			jexit('{"result":"success","message":"'.JText::sprintf('LOG_IMPORT_SUCCESS_MSG', $n).'"}');
		}
		
		function autoImportCsv1($schedule){
			$log = new stdClass();
			$log->iotype = 0;
			$log->profileid = $schedule->profileid;
			$log->cronid = $schedule->id;
			$log->side = 'cron';
			
			$log->user = JFactory::getUser()->id;
			$op_start = JFactory::getDate('now')->format('m-d-Y_hia');
			$log_file = 'com_vdata_plg_custom'.'_'.$op_start.'.txt';
			
			$config = $this->getConfig();
			if($config->logging){
				$logger = $this->initializeLogger();
			}
			
			$log->logfile = $log_file;
			$log->op_start =  JFactory::getDate(microtime(true))->toSql(true);
			
			$params = json_decode($schedule->params);
			$schedule_cols = json_decode($schedule->columns);
			
			$db = $this->getDbc();
			//fetch profile details
			$query = 'select * from #__vd_profiles where id='.$schedule->profileid;
			$db->setQuery($query);
			$profile = $db->loadObject();
			$profile_params = json_decode($profile->params);
			
			$delimiter = $this->getDelimiter();
			$enclosure = $this->getEnclosure();
			$config = $this->getConfig();
			$csv_child = json_decode($config->csv_child);
			$child_delimiter = $this->getChildDelimiter();
			
			$dids = array();
			$join_dids = array();
			$query = 'SHOW KEYS FROM '.$db->quoteName($profile_params->table).' WHERE Key_name = "PRIMARY"';
			$db->setQuery( $query );
			$key = $db->loadAssocList('Column_name');
			if(property_exists($profile_params, 'unqkey') && !empty($profile_params->unqkey))
				$primary = $profile_params->unqkey;
			elseif(!empty($key))
				$primary = array_keys($key);
			else
				$primary = null;
			
			$path = $params->path;
			if($params->server=='local'){
				$path = JPATH_ROOT.'/'.$path;
			}
			elseif($params->server=='absolute'){
				$newPath = JPATH_ROOT.'/'.'components/com_vdata/uploads/'.$profile->title.'.csv';
				if(!copy($path, $newPath)){
					$copy_error = error_get_last();
					jexit('{"result":"error","error":"'.$copy_error['message'].'"}');
				}
				
				$path = $newPath;
			}
			else{
				//copy remote file using ftp
				$inputFtp = (array)$params->ftp;
				if( empty($inputFtp['ftp_host']) || empty($inputFtp['ftp_user']) || empty($inputFtp['ftp_pass']) || empty($inputFtp['ftp_file']) ){
					jexit('{"result":"error","error":"'.JText::_('VDATA_EMPTY_FTP_CREDENTIALS').'"}');
				}
				$ext = strrchr($inputFtp['ftp_file'], '.');
				if($ext <> '.csv'){
					jexit('{"result":"error","error":"'.JText::_('PLZ_UPLOAD_CSV_FILE').'"}');
				}
				$ftp = $this->ftpConnection($inputFtp);
				if($ftp->result=='error'){
					jexit('{"result":"error","error":"'.$ftp->error.'"}');
				}
				$remotePath = rtrim($inputFtp['ftp_directory'], '/').'/'.$inputFtp['ftp_file'];
				$path = JPATH_ROOT.'/'.'components/com_vdata/uploads/'.$inputFtp['ftp_file'].'.csv';
				$ftpHelper = $ftp->ftpHelper;
				if(!$ftpHelper->download($path, $remotePath)){
					jexit('{"result":"error","error":"'.$ftpHelper->getMessage().'"}');
				}
			}

			try{
				$fp = @fopen($path, "r");
				if ( !$fp ) {
					$open_error = error_get_last();
					jexit('{"result":"error","error":"'.$open_error['message'].'"}');
				}
			} catch(Exception $e){
				jexit('{"result":"error","error":"'.$e->getMessage().'"}');
			}
			
			$log->iofile = $path;
			$log->table = $profile_params->table;
			
			//log info
			JLog::add(JText::sprintf('IMPORT_PROFILE', $profile->title), JLog::INFO, 'com_vdata');
			JLog::add(JText::sprintf('IMPORT_TABLE', $profile_params->table), JLog::INFO, 'com_vdata');
			JLog::add(JText::sprintf('IMPORT_OPERATION', $profile_params->operation), JLog::INFO, 'com_vdata');
			JLog::add(JText::sprintf('IMPORT_FORMAT', 'CSV'), JLog::INFO, 'com_vdata');
			
			$query = 'show fields FROM '.$db->quoteName($profile_params->table);
			$db->setQuery( $query );
			$cols = $db->loadObjectList();
			$n = 0;
			if( property_exists($schedule_cols, 'quick') ) {
				$header = fgetcsv($fp, 100000, $delimiter, $enclosure);
				while(($data = fgetcsv($fp, 100000, $delimiter, $enclosure))!==FALSE){
					if($this->check_blank($data)){
						continue;
					}
					$insert = new stdClass();
					$isNew = true;
					$oldData = array();
					if($profile_params->operation==3){
						//apply primary key and custom filters
						$quick_fields = $schedule_cols->fields;
						$where = array();
						$base_in_id = array();
						if(isset($profile_params->unqkey)){
							foreach($profile_params->unqkey as  $key){
								if($quick_fields[$key]!=""){
									$where[] = "(".$db->quoteName($key).' = '.$db->quote($data[$quick_fields[$key]]).")";
									$base_in_id[$key] = $data[$quick_fields[$key]];
								}
							}
						}
						if(!empty($where)){
							$statement = 'SELECT count(*) FROM ' . $db->quoteName($profile_params->table) . ' WHERE %s';
							$query = $db->getQuery(true);
							$db->setQuery(sprintf($statement, implode(' AND ', $where)));
							$result = $db->loadResult();
							if($result>0 && !empty($base_in_id)){
								$dids[] = $base_in_id;
							}
						}
					}
					else{
						if($schedule_cols->quick==1){
							foreach($cols as $i=>$col){
								if(array_key_exists($i, $data)){
									$insert->{$col->Field} = $data[$i];
								}
							}
						}
						else{
							$quick_fields = $schedule_cols->fields;
							foreach($quick_fields as $k=>$field){
								if( ($field!="") && array_key_exists($field, $data) ){
									$insert->{$k}=$data[$field];
								}
							}
						}
					}
					//check if record already exists
					$where = array();
					if( !empty($primary)  ){
						foreach($primary as $keyCol){
							if(isset($insert->{$keyCol})){
							$where[] = $db->quoteName($keyCol).'='.$db->quote($insert->{$keyCol});
							}
						}
						if(!empty($where)){
							$query = 'SELECT * FROM '.$db->quoteName($profile_params->table).' WHERE '.implode(' AND ', $where);
							$db->setQuery( $query );
							$oldData = $db->loadAssoc();
							$isNew = empty($oldData) ? true : false;
						}
					}
					if($profile_params->operation==3){
						//delete data operation
					}
					elseif($profile_params->operation==1){
						if(!$isNew){
							JLog::add(JText::sprintf('RECORD_ALREADY_EXISTS',implode(',',$where)), JLog::ERROR, 'com_vdata');
							continue;
						}
						//capture events before insert
						if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'before') ){
							//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $profile_params->events->before)), JLog::INFO, 'com_vdata');
							$this->profile = new stdClass();
							$this->profile->params = $profile_params;
							$response = $this->captureEventsOnRecord( $insert, 'before', $isNew , array('pk'=>$primary ,'old'=>$oldData));
							if(in_array(false, $response)){
								JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
								continue;
							}
						}
						if(!$db->insertObject($profile_params->table, $insert)){
							JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
							//log error
							$log->op_end = JFactory::getDate('now')->toSql(true);
							$log->status = 'abort';
							$log->message = $db->stderr();
							$flag = $this->setLog($log);
							jexit('{"result":"error","error":"'.$db->stderr().'"}');
						}
						if($this->getAffected($db)> 0){
							$n++;
							//capture events after insert
							if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'after') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $profile_params->events->after)), JLog::INFO, 'com_vdata');
								$this->profile = new stdClass();
								$this->profile->params = $profile_params;
								$response = $this->captureEventsOnRecord( $insert, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
							}
						}
					}
					else{
						if(empty($primary)){
							$log->op_end = JFactory::getDate('now')->toSql(true);
							$log->status = 'abort';
							$log->message = JText::_('PRIMARY_KEY_NOT_FOUND');
							$flag = $this->setLog($log);
							jexit('{"result":"error","error":"'.JText::_('PRIMARY_KEY_NOT_FOUND').'"}');
						}
						else{
							foreach($primary as $pkey){
								if(!isset($insert->{$pkey}) || empty($insert->{$pkey})){
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = JText::_('PRIMARY_KEY_NOT_FOUND');
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.JText::_('PRIMARY_KEY_NOT_FOUND').'"}');
								}
							}
						}
						
						//capture events before update
						if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'before') ){
							//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $profile_params->events->before)), JLog::INFO, 'com_vdata');
							$this->profile = new stdClass();
							$this->profile->params = $profile_params;
							$response = $this->captureEventsOnRecord( $insert, 'before',$isNew , array('pk'=>$primary ,'old'=>$oldData));
							if(in_array(false, $response)){
								JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
								continue;
							}
						}
						if($isNew){
							JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
							if(!$db->insertObject($profile_params->table, $insert)){
								JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
								//log error
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $db->stderr();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$db->stderr().'"}');
							}
						}
						else{
							if(!$db->updateObject($profile_params->table, $insert, $primary)){
								JLog::add(JText::sprintf('UPDATE_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $db->stderr();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$db->stderr().'"}');
							}
						}
						$base_in_id = array();
						foreach($primary as $pkey){
							if(isset($insert->{$pkey})){
								$base_in_id[$pkey] = $insert->{$pkey};
							}
						}
						$dids[] = $base_in_id;
						if($this->getAffected($db)> 0){
							$n++;
							//capture events after update
							if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'after') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $profile_params->events->after)), JLog::INFO, 'com_vdata');
								$this->profile = new stdClass();
								$this->profile->params = $profile_params;
								$response = $this->captureEventsOnRecord( $insert, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
							}
						}
					}
					
				}
			}
			else{
				// $csvfields = $schedule_cols->fields;
				$csvfields = json_decode(json_encode($schedule_cols->fields),true);
				$images_import = array();
				if(property_exists($schedule_cols,'images'))
					$images_import = json_decode(json_encode($schedule_cols->images), true);
				$heading = $data = fgetcsv($fp, 100000, $delimiter, $enclosure);
				while(($data = fgetcsv($fp, 100000, $delimiter, $enclosure)) !== FALSE){
					if($this->check_blank($data)){
						continue;
					}
					$isNew = true;
					$insert = new stdClass();
					$cached_data = new stdClass();
					$oldData = array();
					$stance = false;
					$intance_column ='';
					if($profile_params->operation==3){
						//apply primary key and custom filters
						$where = array();
						$base_in_id = array();
						if(isset($profile_params->unqkey)){
							foreach($profile_params->unqkey as  $key){
								if($csvfields[$key]!=""){
									$where[] = "(".$db->quoteName($key).' = '.$db->quote($data[$csvfields[$key]]).")";
									$base_in_id[$key] = $data[$csvfields[$key]];
								}
							}
						}
						if(!empty($where)){
							$statement = 'SELECT count(*) FROM ' . $db->quoteName($profile_params->table) . ' WHERE %s';
							$query = $db->getQuery(true);
							$db->setQuery(sprintf($statement, implode(' AND ', $where)));
							$result = $db->loadResult();
							if($result>0 && !empty($base_in_id)){
								$dids[] = $base_in_id;
							}
						}
					}
					else{
						foreach($profile_params->fields as $column=>$field)	{
							switch($field->data) {
								case 'file':
									if($csvfields[$column]==""){
										break;
									}
									$insert->{$column} = $cached_data->{$column} = $this->getFilteredValue($field, $data[$csvfields[$column]]);
									if( isset($field->format) && ($field->format=='email') && ($insert->{$column}==false) ){
										JLog::add(JText::sprintf('VDATA_EMAIL_VALIDATION_FAILED', $column), JLog::ERROR, 'com_vdata');
										unset($insert->{$column}, $cached_data->{$column});
									}
									if($field->format == "encrypt"){
										$cached_data->{$column} = $data[$csvfields[$column]];
									}
									if($field->format == "image"){
										$filename = $insert->{$column};$destination = rtrim($field->location,'/').'/'. basename($filename);
											
										$image_source=$path_type='';
										if(isset($images_import['root'][$column])){
											$image_source = isset($images_import['root'][$column]['image_source'])?$images_import['root'][$column]['image_source']:'';
											$path_type = isset($images_import['root'][$column]['path_type'])?$images_import['root'][$column]['path_type']:'';											
										}	
										if(!$image = $this->uploadImage($filename,$destination,$image_source,$path_type,$err)){
													
											$insert->{$column} = $cached_data->{$column} = $destination;
											jexit('{"result":"error","error":"'.$err.'"}');
										}
									} 
								break;
								case 'defined':
									$defined = $field->default;
									
									$defined = $this->getDefinedValue($field->default, $profile_params->fields, $data, $csvfields);
									$insert->$column = $cached_data->{$column} =$defined;
								break;
								case 'reference':
									$insert_ref = new stdClass();
									foreach($field->reftext as $i=>$ref){
										if($csvfields[$column][$ref]!=""){
											$insert_ref->{$ref} = $data[$csvfields[$column][$ref]];
										}
									}
									
									$insert_ref_val = (array)$insert_ref;
									if(!empty($insert_ref_val)){
										$ref_value = $this->getReferenceVal($field->table, $field->on, $insert_ref, $db);
										if(!empty($ref_value)){
											$insert->{$column} = $ref_value;
											$cached_data->{$column} = $ref_value;
										}
										else{
											JLog::add(JText::sprintf('LOCAL_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
										}
									}
									else{
										JLog::add(JText::sprintf('REMOTE_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
									}
								break;
								case 'asset_reference':
									$stance = true;
									$insert->$column = 0;
									$intance_column = $field->on;
									$instance_component = $field->table;
									if($csvfields[$column]['rules']!=""){
										$insert->rules = json_decode($data[$csvfields[$column]['rules']], true);
									}
								break;
							}
						}
					}
					//check if record already exists
					$where = array();
					if( !empty($primary)  ){
						foreach($primary as $keyCol){
							if(isset($insert->{$keyCol})){
							$where[] = $db->quoteName($keyCol).'='.$db->quote($insert->{$keyCol});
							}
						}
						if(!empty($where)){
							$query = 'SELECT * FROM '.$db->quoteName($profile_params->table).' WHERE '.implode(' AND ', $where);
							$db->setQuery( $query );
							$oldData = $db->loadAssoc();
							$isNew = empty($oldData) ? true : false;
						}
					}
					
					//insert base table record
					$afterState = false;
					if($profile_params->operation==3){
						//delete data operation
					}
					elseif($profile_params->operation==1){
						if(!$isNew){
							JLog::add(JText::sprintf('RECORD_ALREADY_EXISTS',implode(',',$where)), JLog::ERROR, 'com_vdata');
							continue;
						}
						//capture events before insert
						if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'before') ){
							//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $profile_params->events->before)), JLog::INFO, 'com_vdata');
							$this->profile = new stdClass();
							$this->profile->params = $profile_params;
							$response = $this->captureEventsOnRecord( $insert, 'before', $isNew , array('pk'=>$primary ,'old'=>$oldData));
							if(in_array(false, $response)){
								JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
								continue;
							}
						}
						
						$base_join_val = null;
						if($stance){
							$component = JText::_($instance_component);
							$component_name = $component;
							if (strpos(strtolower($component), "com_")===FALSE){
								$component_name = "com_".strtolower($component_name); 
							}
							if (strpos($component, "com_")===0){
								$component = str_replace("com_", '',strtolower($component)); 
							}
							JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
							
							$core_tables = array('category', 'content', 'menu', 'module');
							if(in_array($intance_column, $core_tables)){
								$row = JTable::getInstance($intance_column);
							}
							else{
								$row = JTable::getInstance($intance_column, $component.'Table');
							}
							
							if(property_exists($row, 'parent_id')){
								$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
								@$row->setLocation($parent_id, 'last-child');
							}
							if (!$row->bind((array)$insert)) {
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $row->getError();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$row->getError().'"}');
							}
							if (!$row->check()) {
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $row->getError();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$row->getError().'"}');
							}
							if (!$row->store()){
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $row->getError();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$row->getError().'"}');
							}
							if(!empty($primary)){
								$base_in_id = array();
								foreach($primary as $pkey){
									if(property_exists($row, $pkey)){
										$base_in_id[$pkey] = $row->{$pkey};
									}
								}
							}
							$n++;
							$afterState = true;
							if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($row, $profile_params->joins->column1[0])){
								$base_join_val = $row->{$profile_params->joins->column1[0]};
							}
						}
						else{
							if(isset($profile_params->component->table) && !empty($profile_params->component->table)){
								$component = JText::_($profile_params->component->value);
								$component_name = $component;
								if (strpos(strtolower($component), "com_") ===FALSE){
									$component_name = "com_".strtolower($component_name); 
								}
								if (strpos(strtolower($component), "com_") ===0){
									$component = str_replace("com_", '',strtolower($component)); 
								}
								JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
								$core_tables = array('category', 'content', 'menu', 'module');
								if(in_array($profile_params->component->table, $core_tables)){
									$row = JTable::getInstance($profile_params->component->table);
								}
								else{
									$row = JTable::getInstance($profile_params->component->table, $component.'Table');
								}
								
								$row->load(null);
								if(property_exists($row, 'parent_id')){
									$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
									@$row->setLocation($parent_id, 'last-child');
								}
								if (!$row->bind((array)$insert)) {
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $row->getError();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$row->getError().'"}');
								}
								if (!$row->check()) {
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $row->getError();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$row->getError().'"}');
								}
								if (!$row->store()){
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $row->getError();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$row->getError().'"}');
								}
								$n++;
								$afterState = true;
								if(!empty($primary)){
									$base_in_id = array();
									foreach($primary as $pkey){
										if(property_exists($row, $pkey)){
											$base_in_id[$pkey] = $row->{$pkey};
										}
									}
								}
							}
							else{
								if($db->insertObject($profile_params->table, $insert)){
									if($this->getAffected($db)> 0){
										$n++;
										$afterState = true;
									}
									$base_in_id = array();
									foreach($primary as $pkey){
										if(isset($insert->{$pkey})){
											$base_in_id[$pkey] = $insert->{$pkey};
										}
									}
									
									
									if((!empty($primary) && count($primary)==1) || (isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && !property_exists($insert, $profile_params->joins->column1[0]))){
										$newId = $db->insertid();
										if($primary[0]!='id'){
											$query = 'SHOW KEYS FROM '.$db->quoteName($profile_params->table).' WHERE Key_name = "PRIMARY"';
											$db->setQuery( $query );
											// $key = $db->loadObjectList();
											$key = $db->loadAssocList('Column_name');
											$pri = array_keys($key)[0];
											$cached_data->{$pri} = $insert->{$pri} = $newId;
										}else{
											$cached_data->{$primary[0]} = $insert->{$primary[0]} = $db->insertid();
										}
									}
									if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($insert, $profile_params->joins->column1[0])){
										$base_join_val = $insert->{$profile_params->joins->column1[0]};
									}
									
								}
								else{
									JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $db->stderr();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$db->stderr().'"}');
								}
							}
						}
						if($afterState){
							//capture events after insert
							if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'after') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $profile_params->events->after)), JLog::INFO, 'com_vdata');
								if(!empty($primary)){
									foreach($primary as $pkey){
										if(property_exists($cached_data, $pkey) && isset($base_in_id[$pkey])){
											$cached_data->{$pkey} = $base_in_id[$pkey];
										}
									}
								}
								$this->profile = new stdClass();
								$this->profile->params = $profile_params;
								$response = $this->captureEventsOnRecord( $cached_data, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
							}
						}
					}
					else{
						if(empty($primary) ){
							JLog::add(JText::_('PRIMARY_KEY_NOT_FOUND'), JLog::ERROR, 'com_vdata');
							$log->op_end = JFactory::getDate('now')->toSql(true);
							$log->status = 'abort';
							$log->message = $db->stderr();
							$flag = $this->setLog($log);
							jexit('{"result":"error","error":"'.$db->stderr().'"}');
						}
						else{
							foreach($primary as $pkey){
								if(!isset($insert->{$pkey}) || empty($insert->{$pkey})){
									JLog::add(JText::_('PRIMARY_KEY_NOT_FOUND'), JLog::ERROR, 'com_vdata');
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $db->stderr();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$db->stderr().'"}');
								}
							}
						}
						
						//capture events before update
						if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'before') ){
							//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $profile_params->events->before)), JLog::INFO, 'com_vdata');
							$this->profile = new stdClass();
							$this->profile->params = $profile_params;
							$response = $this->captureEventsOnRecord( $insert, 'before',$isNew , array('pk'=>$primary ,'old'=>$oldData));
							if(in_array(false, $response)){
								JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
								continue;
							}
						}
						$base_join_val = null;
						if($stance){
							$component = JText::_($instance_component);
							$component_name = $component;
							if (strpos(strtolower($component), "com_") ===FALSE){
								$component_name = "com_".strtolower($component_name); 
							}
							if (strpos(strtolower($component), "com_") ===0){
								$component = str_replace("com_", '',strtolower($component)); 
							}
							JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
							$core_tables = array('category', 'content', 'menu', 'module');
							if(in_array($intance_column, $core_tables)){
								$row = JTable::getInstance($intance_column);
							}
							else{
								$row = JTable::getInstance($intance_column, $component.'Table');
							}
							
							
							$loadFields = array();
							foreach($primary as $pkey){
								if(isset($insert->{$pkey}) && !empty($insert->{$pkey})){
									$loadFields[$pkey] = $insert->{$pkey};
								}
							}
							$row->load($loadFields);
							if(property_exists($row, 'parent_id')){
								if (isset($row->id) && isset($insert->parent_id) && $row->id==1 && $insert->parent_id==1)
									$insert->parent_id = 0; 
								$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
								if ($row->parent_id!=$parent_id)
									@$row->setLocation($parent_id, 'last-child');
							}
							if($isNew){
								JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
							}
							if (!$row->bind((array)$insert)) {
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $row->getError();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$row->getError().'"}');
							}
							if (!$row->check()) {
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $row->getError();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$row->getError().'"}');
							}
							if (!$row->store()){
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $row->getError();;
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$row->getError().'"}');
							}
							$n++;
							$afterState = true;
							if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($row, $profile_params->joins->column1[0])){
								$base_join_val = $row->{$profile_params->joins->column1[0]};
							}
						}
						else{
							if(isset($profile_params->component->table) && !empty($profile_params->component->table)){
								$component = JText::_($profile_params->component->value);
								$component_name = $component;
								if (strpos(strtolower($component), "com_") ===FALSE){
									$component_name = "com_".strtolower($component_name); 
								}
								if (strpos(strtolower($component), "com_") ===0){
									$component = str_replace("com_", '',strtolower($component)); 
								}
								JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
								
								$core_tables = array('category', 'content', 'menu', 'module');
								if(in_array($profile_params->component->table, $core_tables)){
									$row = JTable::getInstance($profile_params->component->table);
								}
								else{
									$row = JTable::getInstance($profile_params->component->table, $component.'Table');
								}
								
								if($isNew){
									JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
								}
								else{
									$loadFields = array();
									foreach($primary as $pkey){
										if(isset($insert->{$pkey}) && !empty($insert->{$pkey})){
											$loadFields[$pkey] = $insert->{$pkey};
										}
									}
									$row->load($loadFields);
								}
								if(property_exists($row, 'parent_id')){
									if (isset($row->id) && isset($insert->parent_id) && $row->id==1 && $insert->parent_id==1)
										$insert->parent_id = 0; 
									$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
									if ($row->parent_id!=$parent_id)
										@$row->setLocation($parent_id, 'last-child');
								}
								if (!$row->bind((array)$insert)) {
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $row->getError();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$row->getError().'"}');
								}
								if (!$row->check()) {
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $row->getError();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$row->getError().'"}');
								}
								if (!$row->store()){
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $row->getError();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$row->getError().'"}');
								}
								$n++;
								$afterState = true;
								if(!empty($primary)){
									$base_in_id = array();
									foreach($primary as $pkey){
										if(property_exists($row, $pkey)){
											$base_in_id[$pkey] = $row->{$pkey};
										}
									}
								}
								if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($row, $profile_params->joins->column1[0])){
									$base_join_val = $row->{$profile_params->joins->column1[0]};
								}
							}
							else{
								if($isNew){
									JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
									if($db->insertObject($profile_params->table, $insert)){
										if($this->getAffected($db)> 0){
											$n++;
											$afterState = true;
										}
										$base_in_id = array();
										foreach($primary as $pkey){
											if(isset($insert->{$pkey})){
												$base_in_id[$pkey] = $insert->{$pkey};
											}
										}
										
										
										if((!empty($primary) && count($primary)==1) || (isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && !property_exists($insert, $profile_params->joins->column1[0]))){
											$newId = $db->insertid();
											if($primary[0]!='id'){
												$query = 'SHOW KEYS FROM '.$db->quoteName($profile_params->table).' WHERE Key_name = "PRIMARY"';
												$db->setQuery( $query );
												// $key = $db->loadObjectList();
												$key = $db->loadAssocList('Column_name');
												$pri = array_keys($key)[0];
												$cached_data->{$pri} = $insert->{$pri} = $newId;
											}else{
												$cached_data->{$primary[0]} = $insert->{$primary[0]} = $db->insertid();
											}
										}
										if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($insert, $profile_params->joins->column1[0])){
											$base_join_val = $insert->{$profile_params->joins->column1[0]};
										}
										
									}
									else{
										JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
										$log->op_end = JFactory::getDate('now')->toSql(true);
										$log->status = 'abort';
										$log->message = $db->stderr();
										$flag = $this->setLog($log);
										jexit('{"result":"error","error":"'.$db->stderr().'"}');
									}
								}
								else{
									if($db->updateObject($profile_params->table, $insert, $primary)){
										if($this->getAffected($db)> 0){
											$n++;
											$afterState = true;
										}
										// $base_in_id = $insert->{$primary};
										$base_in_id = array();
										foreach($primary as $pkey){
											if(isset($insert->{$pkey})){
												$base_in_id[$pkey] = $insert->{$pkey};
											}
										}
										if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0){
											if(property_exists($insert, $profile_params->joins->column1[0])){
												$base_join_val = $insert->{$profile_params->joins->column1[0]};
											}else{
												//check if record already exists
												$where = array();
												if( !empty($primary)  ){
													foreach($primary as $keyCol){
														if(isset($insert->{$keyCol})){
														$where[] = $db->quoteName($keyCol).'='.$db->quote($insert->{$keyCol});
														}
													}
													if(!empty($where)){
														$query = 'SELECT '.$profile_params->joins->column1[0].' FROM '.$db->quoteName($profile_params->table).' WHERE '.implode(' AND ', $where);
														$db->setQuery( $query );
														$base_join_val = $cached_data->{$profile_params->joins->column1[0]} = $insert->{$profile_params->joins->column1[0]} = $db->loadResult();
													}
												}
											}
										}
										
									}
									else{
										JLog::add(JText::sprintf('UPDATE_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
										$log->op_end = JFactory::getDate('now')->toSql(true);
										$log->status = 'abort';
										$log->message = $db->stderr();
										$flag = $this->setLog($log);
										jexit('{"result":"error","error":"'.$db->stderr().'"}');
									}
								}
							}
						}
						$dids[] = $base_in_id;
						
						if($afterState){
							//capture events after update
							if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'after') ){
								if(!empty($primary)){
									foreach($primary as $pkey){
										if(property_exists($cached_data, $pkey) && isset($base_in_id[$pkey])){
											$cached_data->{$pkey} = $base_in_id[$pkey];
										}
									}
								}
								$this->profile = new stdClass();
								$this->profile->params = $profile_params;
								$response = $this->captureEventsOnRecord( $cached_data, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
							}
						}
					}
					if($profile_params->operation!=3 && isset($profile_params->joins)){
						//insert joined tables record
						$join_dids[]= $base_join_val; 
						$flag = $this->insertCsvJoinRecords($base_join_val, $profile_params->joins, $data, $csvfields, $profile_params->operation);
						if($flag->result=='error'){
							$obj->result = 'error';
							$obj->error = $flag->error;
							return $obj;
						}
					}
				}
			}
			
			JLog::add(JText::sprintf('IMPORT_RECORD_COUNT', $n), JLog::INFO, 'com_vdata');
			if( ($profile_params->operation==2) || ($profile_params->operation==3) ){
				$deleteQry = 'DELETE FROM '.$db->quoteName($profile_params->table). ' WHERE %s';
				$whereCon = array();
				foreach($dids as $did){
					if(is_array($did)){
						$where = array();
						foreach($did as $pkey=>$pval){
							$where[] = $db->quoteName($pkey).' <> '.$db->quote($pval);
						}
						$whereCon[] = ' ('.implode(' AND ', $where).')';
					}
					else{
						$whereCon[] = ' '.$db->quoteName($primary).' <> '.$db->quote($did);
					}
				}
				
				//apply delete filter for delete operation
				if($profile_params->operation==3){
					//apply delete filters
					if(isset($profile_params->filters->column)){
						$filterValues = $this->importDeleteFilter($db, $profile_params);
						if(!empty($filterValues)){
							$whereCon[] = $filterValues;
						}
					}
				}
				if(!empty($whereCon)){
					$db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
					$res = $db->execute();
				}
				if(isset($profile_params->joins)){
					$whereCon = array();
					$table2 = $profile_params->joins->table2;
					for($i=0;$i<count($table2);$i++){
						$deleteQry = 'DELETE FROM '.$db->quoteName($table2[$i]). ' WHERE %s';
						foreach($join_dids as $jdid){
							 if(is_array($jdid)){
								$where = array();
								foreach($jdid as $pkey=>$pval){
									$where[] = $db->quoteName($pkey).' <> '.$db->quote($pval);
								}
								$whereCon[] = ' ('.implode(' AND ', $where).')';
							}
							else{ 
								$whereCon[] = $db->quoteName($profile_params->joins->column2[$i]).' <> '.$db->quote($jdid);
							}
						}
						if(!empty($whereCon)){
							$db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
							$res = $db->execute();
						}
					}
				}
			}
			//remove copy of remote data file
			$isRemoteFile = ($params->server=='local')? false:true;
			if($isRemoteFile){
				$ul = @unlink($path);
				if($ul===false){
					JLog::add(JText::_('unable to remove local copy of remote data file'), JLog::INFO, 'com_vdata');
				}
			}
			//send notification email
			$notifyProfile = ($schedule->profileid!=0)?$profile->title:"";
			$notifyQry = ($schedule->profileid==0)?$schedule->qry:"";
			$notify = $this->sendNotificationEmail( $schedule->iotype,1 , array('schedule_id'=>$schedule->id,'schedule_title'=>$schedule->title,"schedule_qry"=>$notifyQry,'profile_id'=>$schedule->profileid,'profile_title'=>$notifyProfile,"count"=>$n, "format"=>"csv") );
			
			$log->op_end =  JFactory::getDate(microtime(true))->toSql(true);
			$log->status = 'success';
			$log->message = JText::sprintf('LOG_IMPORT_SUCCESS_MSG', $n);
			$log->iocount = $n;
			$flag = $this->setLog($log);
			jexit('{"result":"success","message":"'.JText::sprintf('LOG_IMPORT_SUCCESS_MSG', $n).'"}');
		}
		
		function autoImportXml($schedule){
			$log = new stdClass();
			$log->iotype = 0;
			$log->profileid = $schedule->profileid;
			$log->cronid = $schedule->id;
			$log->side = 'cron';
			$log->user = JFactory::getUser()->id;
			$op_start = JFactory::getDate('now')->format('m-d-Y_hia');
			$log_file = 'com_vdata_plg_custom'.'_'.$op_start.'.txt';
			
			$config = $this->getConfig();
			if($config->logging){
				$logger = $this->initializeLogger();
			}
			
			$log->logfile = $log_file;
			$log->op_start =  JFactory::getDate(microtime(true))->toSql(true);
			
			$params = json_decode($schedule->params);
			$schedule_cols = json_decode($schedule->columns);
			
			$db = $this->getDbc();
			//fetch profile details
			$query = 'select * from #__vd_profiles where id='.$schedule->profileid;
			$db->setQuery($query);
			$profile = $db->loadObject();
			$profile_params = json_decode($profile->params);
			
			$query = 'SHOW KEYS FROM '.$db->quoteName($profile_params->table).' WHERE Key_name = "PRIMARY"';
			$db->setQuery( $query );
			$key = $db->loadAssocList('Column_name');
			if(property_exists($profile_params, 'unqkey') && !empty($profile_params->unqkey)){
				$primary = $profile_params->unqkey;
			}
			elseif(!empty($key)){
				$primary = array_keys($key);
			}
			else{
				$primary = null;
			}
			
			$xmlfields = json_decode(json_encode($schedule_cols->fields), true);
			$childTags = json_decode(json_encode($schedule_cols->child_tags), true);
			
			if(count($xmlfields) == 0)	{
				jexit('{"result":"error","error":"'.JText::_('PLZ_UPLOAD_VALID_XML_FILE').'"}');
			}
			
			$path = $params->path;
			if($params->server=='local'){
				$path = JPATH_ROOT.'/'.$path;
			}
			elseif($params->server=='absolute'){
				$newPath = JPATH_ROOT.'/'.'components/com_vdata/uploads/'.$profile->title.'.xml';
				if(!copy($path, $newPath)){
					$copy_error = error_get_last();
					jexit('{"result":"error","error":"'.$copy_error['message'].'"}');
				}
				$path = $newPath;
			}
			else{
				//copy remote file using ftp
				$inputFtp = (array)$params->ftp;
				if( empty($inputFtp['ftp_host']) || empty($inputFtp['ftp_user']) || empty($inputFtp['ftp_pass']) || empty($inputFtp['ftp_file']) ){
					jexit('{"result":"error","error":"'.JText::_('VDATA_EMPTY_FTP_CREDENTIALS').'"}');
				}
				$ext = strrchr($inputFtp['ftp_file'], '.');
				if($ext <> '.xml'){
					jexit('{"result":"error","error":"'.JText::_('PLZ_UPLOAD_CSV_FILE').'"}');
				}
				$ftp = $this->ftpConnection($inputFtp);
				if($ftp->result=='error'){
					jexit('{"result":"error","error":"'.$ftp->error.'"}');
				}
				$remotePath = rtrim($inputFtp['ftp_directory'], '/').'/'.$inputFtp['ftp_file'];
				$path = JPATH_ROOT.'/'.'components/com_vdata/uploads/'.$inputFtp['ftp_file'].'.xml';
				$ftpHelper = $ftp->ftpHelper;
				if(!$ftpHelper->download($path, $remotePath)){
					jexit('{"result":"error","error":"'.$ftpHelper->getMessage().'"}');
				}
			}
			
			try{
				$fp = @fopen($path, "r");
				if ( !$fp ) {
					$open_error = error_get_last();
					jexit('{"result":"error","error":"'.$open_error['message'].'"}');
				}
			} catch(Exception $e){
				jexit('{"result":"error","error":"'.$e->getMessage().'"}');
			}
			
			$log->iofile = $path;
			$log->table = $profile_params->table;
			
			//log info
			JLog::add(JText::sprintf('IMPORT_PROFILE', $profile->title), JLog::INFO, 'com_vdata');
			JLog::add(JText::sprintf('IMPORT_TABLE', $profile_params->table), JLog::INFO, 'com_vdata');
			JLog::add(JText::sprintf('IMPORT_OPERATION', $profile_params->operation), JLog::INFO, 'com_vdata');
			JLog::add(JText::sprintf('IMPORT_FORMAT', 'XML'), JLog::INFO, 'com_vdata');
			
			$dids = array();
			$join_dids = array();

			$query = 'show fields FROM '.$db->quoteName($profile_params->table);
			$db->setQuery( $query );
			$cols = $db->loadObjectList();
			$n = 0;
			
			$rootTag = $schedule_cols->base;
			if(empty($rootTag) || count($rootTag)<2){
				jexit('{"result":"error","error":"'.JText::_('PLZ_SELECT_ROOT_TAG').'"}');
			}
			$rootTag = $rootTag[count($rootTag)-2];
			
			$hasAttr = $this->getNodeAttr($path, $rootTag);
			$baseTag = explode('||', $rootTag);
			$baseRoot = $baseTag[count($baseTag)-1];
			$tmpRootTag = explode('-', $baseRoot);
			if(count($tmpRootTag)>1){
				$baseRoot = $tmpRootTag[0];
			}
			$tmp_str = empty($hasAttr)?'>':' ';
			$baseRootStart = '<'.$baseRoot.$tmp_str;
			$baseRootEnd = '</'.$baseRoot.'>';
				
			if( property_exists($schedule_cols, 'quick') ) {
			
				while(($xml_str = $this->nodeStringFromXML($fp, $baseRootStart, $baseRootEnd))!== false){
					$xml_str = "<ROWSET>".$xml_str."</ROWSET>";
					$xml = @simplexml_load_string($xml_str);
					if(!$xml){
						$error = libxml_get_last_error();
						JLog::add(JText::_('PARSE_ERROR').$error->message, JLog::ERROR, 'com_vdata');
						$log->op_end = JFactory::getDate('now')->toSql(true);
						$log->status = 'abort';
						$log->message = JText::_('PARSE_ERROR').$error->message;
						$flag = $this->setLog($log);
						jexit('{"result":"error","error":"'.JText::_('PARSE_ERROR').$error->message.'"}');
					}
					foreach($xml->{$baseRoot} as $j=>$row){
						$insert = new stdClass();
						if($schedule_cols->operation==3){
							//apply primary key and custom filters
							$where = array();
							$base_in_id = array();
							$quick_fields = $schedule_cols->fields;
							if(isset($schedule_cols->unqkey)){
								foreach($schedule_cols->unqkey as  $key){
									if($quick_fields[$key]!=""){
										$dataVal = $this->getXmlFieldData($row, $quick_fields[$key]);
										$where[] = "(".$db->quoteName($key).' = '.$db->quote($dataVal).")";
										$base_in_id[$key] = $dataVal;
									}
								}
							}
							if(!empty($where)){
								$statement = 'SELECT count(*) FROM ' . $db->quoteName($schedule_cols->table) . ' WHERE %s';
								$query = $db->getQuery(true);
								$db->setQuery(sprintf($statement, implode(' AND ', $where)));
								$result = $db->loadResult();
								if($result>0 && !empty($base_in_id)){
									$dids[] = $base_in_id;
								}
							}
						}
						else{
							if($schedule_cols->quick==1){
								foreach($cols as $k=>$col){
									if(property_exists($row, $col->Field)){
										$insert->{$col->Field} = (string)$row->{$col->Field};
									}
									
								}
							}
							else{
								$quick_fields = $schedule_cols->fields;
								foreach($quick_fields as $k=>$field){
									if(!empty($field)){
										$insert->{$k} = (string)$row->{$field};
									}
								}
							}
						}
						//if primary key exists check existing record
						$isNew = true;
						$oldData = array();
						$where = array();
						if(!empty($primary)){//&& !empty($insert->{$primary})							
							foreach($primary as $keyCol){
								if(isset($insert->{$keyCol})){
								$where[] = $db->quoteName($keyCol).'='.$db->quote($insert->{$keyCol});
								}
							}
							if(!empty($where)){
								$query = 'SELECT * FROM '.$db->quoteName($profile_params->table).' WHERE '.implode(' AND ', $where);
								$db->setQuery( $query );
								$oldData = $db->loadAssoc();
								$isNew = empty($oldData) ? true : false;
							}
						}
						
						if($profile_params->operation==3){
							//delete data operation
						}
						elseif($profile_params->operation==1){
							if(!$isNew){
								JLog::add(JText::sprintf('RECORD_ALREADY_EXISTS',implode(',',$where)), JLog::ERROR, 'com_vdata');
								continue;
							}
							//capture events before insert
							if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'before') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $profile_params->events->before)), JLog::INFO, 'com_vdata');
								$this->profile = new stdClass();
								$this->profile->params = $profile_params;
								$response = $this->captureEventsOnRecord( $insert, 'before', $isNew , array('pk'=>$primary ,'old'=>$oldData));
								if(in_array(false, $response)){
									JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
									continue;
								}
							}
							if(!$db->insertObject($profile_params->table, $insert)){
								JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $db->stderr();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$db->stderr().'"}');
							}
							if($this->getAffected($db)> 0){
								$n++;
								//capture events after insert
								if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'after') ){
									//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $profile_params->events->after)), JLog::INFO, 'com_vdata');
									$this->profile = new stdClass();
									$this->profile->params = $profile_params;
									$response = $this->captureEventsOnRecord( $insert, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
								}
							}
						}
						else{
							if(empty($primary)){
								JLog::add(JText::_('PRIMARY_KEY_NOT_FOUND'), JLog::ERROR, 'com_vdata');
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = JText::_('PRIMARY_KEY_NOT_FOUND');
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.JText::_('PRIMARY_KEY_NOT_FOUND').'"}');
							}
							else{
								foreach($primary as $pkey){
									if(!isset($insert->{$pkey}) || empty($insert->{$pkey})){
										JLog::add(JText::_('PRIMARY_KEY_NOT_FOUND'), JLog::ERROR, 'com_vdata');
										$log->op_end = JFactory::getDate('now')->toSql(true);
										$log->status = 'abort';
										$log->message = JText::_('PRIMARY_KEY_NOT_FOUND');
										$flag = $this->setLog($log);
										jexit('{"result":"error","error":"'.JText::_('PRIMARY_KEY_NOT_FOUND').'"}');
									}
								}
							}
							
							//capture events before update
							if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'before') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $profile_params->events->before)), JLog::INFO, 'com_vdata');
								$this->profile = new stdClass();
								$this->profile->params = $profile_params;
								$response = $this->captureEventsOnRecord( $insert, 'before',$isNew , array('pk'=>$primary ,'old'=>$oldData));
								if(in_array(false, $response)){
									JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
									continue;
								}
							}
							if($isNew){
								JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
								if(!$db->insertObject($profile_params->table, $insert)){
									JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $db->stderr();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$db->stderr().'"}');
								}
							}
							else{
								if(!$db->updateObject($profile_params->table, $insert, $primary)){
									JLog::add(JText::sprintf('UPDATE_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $db->stderr();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$db->stderr().'"}');
								}
							}
							$base_in_id = array();
							foreach($primary as $pkey){
								if(isset($insert->{$pkey})){
									$base_in_id[$pkey] = $insert->{$pkey};
								}
							}
							$dids[] = $base_in_id;
							
							if($this->getAffected($db)> 0){
								$n++;
								//capture events after update
								if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'after') ){
									//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $profile_params->events->after)), JLog::INFO, 'com_vdata');
									$this->profile = new stdClass();
									$this->profile->params = $profile_params;
									$response = $this->captureEventsOnRecord( $insert, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
								}
							}
								
						}
					}
				}
				
			}
			else{
				$images_import = array();
				if(property_exists($schedule_cols,'images'))
					$images_import = json_decode(json_encode($schedule_cols->images), true);
				while(($xml_str = $this->nodeStringFromXML($fp, $baseRootStart, $baseRootEnd))!== false){
					$xml_str = "<ROWSET>".$xml_str."</ROWSET>";
					$xml = @simplexml_load_string($xml_str);
					if(!$xml){
						$error = libxml_get_last_error();
						JLog::add(JText::_('PARSE_ERROR').$error->message, JLog::ERROR, 'com_vdata');
						$log->op_end = JFactory::getDate('now')->toSql(true);
						$log->status = 'abort';
						$log->message = JText::_('PARSE_ERROR').$error->message;
						$flag = $this->setLog($log);
						jexit('{"result":"error","error":"'.JText::_('PARSE_ERROR').$error->message.'"}');
					}
					
					foreach($xml->{$baseRoot} as $j=>$row){
						$isNew = true;
						$insert = new stdClass();
						$stance = false;
						$cached_data =  new stdClass();
						$oldData = array();
						if($profile_params->operation==3){
							//apply primary key and custom filters
							$where = array();
							$base_in_id = array();
							if(isset($profile_params->unqkey)){
								foreach($profile_params->unqkey as  $key){
									if($xmlfields[$key]!=""){
										$xmlData = $this->getXmlFieldData($row, $xmlfields[$key]);
										$where[] = "(".$db->quoteName($key).' = '.$db->quote($xmlData).")";
										$base_in_id[$key] = $xmlData;
									}
								}
							}
							if(!empty($where)){
								$statement = 'SELECT count(*) FROM ' . $db->quoteName($profile_params->table) . ' WHERE %s';
								$query = $db->getQuery(true);
								$db->setQuery(sprintf($statement, implode(' AND ', $where)));
								$result = $db->loadResult();
								if($result>0 && !empty($base_in_id)){
									$dids[] = $base_in_id;
								}
							}
						}
						else{
							foreach($profile_params->fields as $column=>$field)	{
								switch($field->data) {
									case 'file' :
										if(empty($xmlfields[$column])){
											break;
										}
										$xmlData = $this->getXmlFieldData($row, $xmlfields[$column]);
										$insert->{$column} = $cached_data->{$column} = $this->getFilteredValue($field, $xmlData);
										if( isset($field->format) && ($field->format=='email') && ($insert->{$column}==false) ){
											JLog::add(JText::sprintf('VDATA_EMAIL_VALIDATION_FAILED', $column), JLog::ERROR, 'com_vdata');
											unset($insert->{$column}, $cached_data->{$column});
										}
										if($field->format == "encrypt"){
											$cached_data->{$column} = $xmlData;
										}
										if($field->format == "image"){
											$filename = $insert->{$column};$destination = rtrim($field->location,'/').'/'. basename($filename);
												
											$image_source=$path_type='';
											if(isset($images_import['root'][$column])){
												$image_source = isset($images_import['root'][$column]['image_source'])?$images_import['root'][$column]['image_source']:'';
												$path_type = isset($images_import['root'][$column]['path_type'])?$images_import['root'][$column]['path_type']:'';											
											}	
											if(!$image = $this->uploadImage($filename,$destination,$image_source,$path_type,$err)){
													
												$insert->{$column} = $cached_data->{$column} = $destination;
												jexit('{"result":"error","error":"'.$err.'"}');
											}
										} 
									break;
									case 'defined' :
										$defined = $field->default;
										
										$defined = $this->getDefinedXmlValue($field->default, $profile_params->fields, $row, $xmlfields);
										$insert->{$column} = $cached_data->{$column} = $defined;
									break;
									case 'reference' :
										//one to one relation							
										$insert_ref = new stdClass();
										foreach($field->reftext as $k=>$ref){
											if(!empty($xmlfields[$column][$ref])){
												$insert_ref->{$ref} = $this->getXmlFieldData($row, $xmlfields[$column][$ref]);
											}
										}
										
										$insert_ref_val = (array)$insert_ref;
										if(!empty($insert_ref_val)){
											$ref_value = $this->getReferenceVal($field->table, $field->on, $insert_ref, $db);
											if(!empty($ref_value)){
												$insert->{$column} = $cached_data->{$column} = $ref_value;
											}
											else{
												JLog::add(JText::sprintf('LOCAL_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
											}
										}
										else{
											JLog::add(JText::sprintf('REMOTE_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
										}
										
									break;
									case 'asset_reference':
										$stance = true;
										$insert->{$column} = 0;
										$intance_column = $field->on;
										$instance_component = $field->table;
										if($xmlfields[$column]['rules']!=""){
											$xmlData = $this->getXmlFieldData($row, $xmlfields[$column]['rules']);
											$insert->rules = @json_decode($xmlData, true);
										}
									break;
								}
							}
						}
						//check if id already exists
						$where = array();
						if(!empty($primary)){//&& isset($insert->{$primary})
							foreach($primary as $keyCol){
								if(isset($insert->{$keyCol})){
								$where[] = $db->quoteName($keyCol).'='.$db->quote($insert->{$keyCol});
								}
							}
							if(!empty($where)){
								$query = 'SELECT * FROM '.$db->quoteName($profile_params->table).' WHERE '.implode(' AND ', $where);
								$db->setQuery( $query );
								$oldData = $db->loadAssoc();
								$isNew = empty($oldData) ? true : false;
							}
						}
						
						$afterState = false;
						if($profile_params->operation==3){
							//delete data operation
						}
						elseif($profile_params->operation==1){
							if(!$isNew){
								JLog::add(JText::sprintf('RECORD_ALREADY_EXISTS',implode(',',$where)), JLog::ERROR, 'com_vdata');
								continue;
							}
							//capture events before insert
							if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'before') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $profile_params->events->before)), JLog::INFO, 'com_vdata');
								$this->profile = new stdClass();
								$this->profile->params = $profile_params;
								$response = $this->captureEventsOnRecord( $insert, 'before', $isNew , array('pk'=>$primary ,'old'=>$oldData));
								if(in_array(false, $response)){
									JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
									continue;
								}	
							}
							
							$base_join_val = null;
							if($stance){
								$component = JText::_($instance_component);
								$component_name = $component;
								if (strpos(strtolower($component), "com_") ===FALSE){
									$component_name = "com_".strtolower($component_name); 
								}
								if (strpos(strtolower($component), "com_") ===0){
									$component = str_replace("com_", '',strtolower($component)); 
								}
								JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
								$core_tables = array('category', 'content', 'menu', 'module');
								if(in_array($intance_column, $core_tables)){
									$tbl_instance = JTable::getInstance($intance_column);
								}
								else{
									$tbl_instance = JTable::getInstance($intance_column, $component.'Table');
								}
								
								if(property_exists($tbl_instance, 'parent_id')){
									$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
									@$tbl_instance->setLocation($parent_id, 'last-child');
								}
								if (!$tbl_instance->bind((array)$insert)) {
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $db->stderr();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$db->stderr().'"}');
								}
								if (!$tbl_instance->check()) {
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $db->stderr();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$db->stderr().'"}');
								}
								if (!$tbl_instance->store()){
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $db->stderr();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$db->stderr().'"}');
								}
								if(!empty($primary)){
									$base_in_id = array();
									foreach($primary as $pkey){
										if(property_exists($tbl_instance, $pkey)){
											$base_in_id[$pkey] = $tbl_instance->{$pkey};
										}
									}
								}
								$n++;
								$afterState = true;
								if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($tbl_instance, $profile_params->joins->column1[0])){
									$base_join_val = $tbl_instance->{$profile_params->joins->column1[0]};
								}
							}
							else{
								if(isset($profile_params->component->table) && !empty($profile_params->component->table)){
									$component = JText::_($profile_params->component->value);
									$component_name = $component;
									if (strpos(strtolower($component), "com_") ===FALSE){
										$component_name = "com_".strtolower($component_name); 
									}
									if (strpos(strtolower($component), "com_") ===0){
										$component = str_replace("com_", '',strtolower($component)); 
									}
									JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
									$core_tables = array('category', 'content', 'menu', 'module');
									if(in_array($profile_params->component->table, $core_tables)){
										$tbl_instance = JTable::getInstance($profile_params->component->table);
									}
									else{
										$tbl_instance = JTable::getInstance($profile_params->component->table, $component.'Table');
									}
									if(property_exists($tbl_instance, 'parent_id')){
										$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
										@$tbl_instance->setLocation($parent_id, 'last-child');
									}
									
									if (!$tbl_instance->bind((array)$insert)) {
										$log->op_end = JFactory::getDate('now')->toSql(true);
										$log->status = 'abort';
										$log->message = $db->stderr();
										$flag = $this->setLog($log);
										jexit('{"result":"error","error":"'.$db->stderr().'"}');
									}
									
									if (!$tbl_instance->check()) {
										$log->op_end = JFactory::getDate('now')->toSql(true);
										$log->status = 'abort';
										$log->message = $db->stderr();
										$flag = $this->setLog($log);
										jexit('{"result":"error","error":"'.$db->stderr().'"}');
									}
									
									if (!$tbl_instance->store()){
										$log->op_end = JFactory::getDate('now')->toSql(true);
										$log->status = 'abort';
										$log->message = $db->stderr();
										$flag = $this->setLog($log);
										jexit('{"result":"error","error":"'.$db->stderr().'"}');
									}
									$n++;
									$afterState = true;
									if(!empty($primary)){
										$base_in_id = array();
										foreach($primary as $pkey){
											if(property_exists($tbl_instance, $pkey)){
												$base_in_id[$pkey] = $tbl_instance->{$pkey};
											}
										}
									}
									if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($tbl_instance, $profile_params->joins->column1[0])){
										$base_join_val = $tbl_instance->{$profile_params->joins->column1[0]};
									}
								}
								else{
									if($db->insertObject($profile_params->table, $insert)){
										if($this->getAffected($db)> 0){
											$n++;
											$afterState = true;
										}
										$base_in_id = array();
										foreach($primary as $pkey){
											if(isset($insert->{$pkey})){
												$base_in_id[$pkey] = $insert->{$pkey};
											}
										}
										
										if((!empty($primary) && count($primary)==1) || (isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && !property_exists($insert, $profile_params->joins->column1[0]))){
											$newId =$db->insertid();
											if($primary[0]!='id'){
												$query = 'SHOW KEYS FROM '.$db->quoteName($profile_params->table).' WHERE Key_name = "PRIMARY"';
												$db->setQuery( $query );
												// $key = $db->loadObjectList();
												$key = $db->loadAssocList('Column_name');
												$pri = array_keys($key)[0];
												$cached_data->{$pri} = $insert->{$pri} = $newId;
											}else{
												$cached_data->{$primary[0]} = $insert->{$primary[0]} = $db->insertid();
											}
										}
										if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($insert, $profile_params->joins->column1[0])){
											$base_join_val = $insert->{$profile_params->joins->column1[0]};
										}
										
									}
									else{
										JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
										$log->op_end = JFactory::getDate('now')->toSql(true);
										$log->status = 'abort';
										$log->message = $db->stderr();
										$flag = $this->setLog($log);
										jexit('{"result":"error","error":"'.$db->stderr().'"}');
									}
								}
							}
							
							if($afterState){
								//capture events after insert
								if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'after') ){
									//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $profile_params->events->after)), JLog::INFO, 'com_vdata');
									if(!empty($primary)){
										foreach($primary as $pkey){
											if(property_exists($cached_data, $pkey) && isset($base_in_id[$pkey])){
												$cached_data->{$pkey} = $base_in_id[$pkey];
											}
										}
									}
									$this->profile = new stdClass();
									$this->profile->params = $profile_params;
									$response = $this->captureEventsOnRecord( $cached_data, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
								}
							}
								
						}
						else{
							if(empty($primary)){
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = JText::_('PRIMARY_KEY_NOT_FOUND');
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.JText::_('PRIMARY_KEY_NOT_FOUND').'"}');
							}
							else{
								foreach($primary as $pkey){
									if(!isset($insert->{$pkey}) || empty($insert->{$pkey})){
										$log->op_end = JFactory::getDate('now')->toSql(true);
										$log->status = 'abort';
										$log->message = JText::_('PRIMARY_KEY_NOT_FOUND');
										$flag = $this->setLog($log);
										jexit('{"result":"error","error":"'.JText::_('PRIMARY_KEY_NOT_FOUND').'"}');
									}
								}
							}
							
							
							//capture events before update
							if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'before') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $profile_params->events->before)), JLog::INFO, 'com_vdata');
								$this->profile = new stdClass();
								$this->profile->params = $profile_params;
								$response = $this->captureEventsOnRecord( $insert, 'before',$isNew , array('pk'=>$primary ,'old'=>$oldData));
								if(in_array(false, $response)){
									JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
									continue;
								}
							}
							$base_join_val = null;
							if($stance){
								$component = JText::_($instance_component);
								$component_name = $component;
								if (strpos(strtolower($component), "com_") ===FALSE){
									$component_name = "com_".strtolower($component_name); 
								}
								if (strpos(strtolower($component), "com_") ===0){
									$component = str_replace("com_", '',strtolower($component)); 
								}
								JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
								$core_tables = array('category', 'content', 'menu', 'module');
								if(in_array($intance_column, $core_tables)){
									$tbl_instance = JTable::getInstance($intance_column);
								}
								else{
									$tbl_instance = JTable::getInstance($intance_column, $component.'Table');
								}
								
								
								$loadFields = array();
								foreach($primary as $pkey){
									if(isset($insert->{$pkey}) && !empty($insert->{$pkey})){
										$loadFields[$pkey] = $insert->{$pkey};
									}
								}
								$tbl_instance->load($loadFields);
								
								if(property_exists($tbl_instance, 'parent_id')){
									$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
									if (!$tbl_instance->isLeaf())
										@$tbl_instance->setLocation($parent_id, 'last-child');
								}
								if($isNew){
									JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
								}
								if (!$tbl_instance->bind((array)$insert)) {
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $db->stderr();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$db->stderr().'"}');
								}
								if (!$tbl_instance->check()) {
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $db->stderr();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$db->stderr().'"}');
								}
								if (!$tbl_instance->store()){
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $db->stderr();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$db->stderr().'"}');
								}
								$n++;
								$afterState = true;
								if(!empty($primary)){
									$base_in_id = array();
									foreach($primary as $pkey){
										if(property_exists($tbl_instance, $pkey)){
											$base_in_id[$pkey] = $tbl_instance->{$pkey};
										}
									}
								}
								if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($tbl_instance, $profile_params->joins->column1[0])){
									$base_join_val = $tbl_instance->{$profile_params->joins->column1[0]};
								}
							}
							else{
								if($isNew){
									JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
									if(isset($profile_params->component->table) && !empty($profile_params->component->table)){
										$component = JText::_($profile_params->component->value);
										$component_name = $component;
										if (strpos(strtolower($component), "com_") ===FALSE){
											$component_name = "com_".strtolower($component_name); 
										}
										if (strpos(strtolower($component), "com_") ===0){
											$component = str_replace("com_", '',strtolower($component)); 
										}
										JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
										$core_tables = array('category', 'content', 'menu', 'module');
										if(in_array($profile_params->component->table, $core_tables)){
											$tbl_instance = JTable::getInstance($profile_params->component->table);
										}
										else{
											$tbl_instance = JTable::getInstance($profile_params->component->table, $component.'Table');
										}
										
										$tbl_instance->load(null);
										if(property_exists($tbl_instance, 'parent_id')){
											$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
											@$tbl_instance->setLocation($parent_id, 'last-child');
										}
										if (!$tbl_instance->bind((array)$insert)) {
											$log->op_end = JFactory::getDate('now')->toSql(true);
											$log->status = 'abort';
											$log->message = $db->stderr();
											$flag = $this->setLog($log);
											jexit('{"result":"error","error":"'.$db->stderr().'"}');
										}
										if (!$tbl_instance->check()) {
											$log->op_end = JFactory::getDate('now')->toSql(true);
											$log->status = 'abort';
											$log->message = $db->stderr();
											$flag = $this->setLog($log);
											jexit('{"result":"error","error":"'.$db->stderr().'"}');
										}
										if (!$tbl_instance->store()){
											$log->op_end = JFactory::getDate('now')->toSql(true);
											$log->status = 'abort';
											$log->message = $db->stderr();
											$flag = $this->setLog($log);
											jexit('{"result":"error","error":"'.$db->stderr().'"}');
										}
										$n++;
										$afterState = true;
										if(!empty($primary)){
											$base_in_id = array();
											foreach($primary as $pkey){
												if(property_exists($tbl_instance, $pkey)){
													$base_in_id[$pkey] = $tbl_instance->{$pkey};
												}
											}
										}
										if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($tbl_instance, $profile_params->joins->column1[0])){
											$base_join_val = $tbl_instance->{$profile_params->joins->column1[0]};
										}
									}
									else{
										if($db->insertObject($profile_params->table, $insert)){
											if($this->getAffected($db)> 0){
												$n++;
												$afterState = true;
											}
											$base_in_id = array();
											foreach($primary as $pkey){
												if(isset($insert->{$pkey})){
													$base_in_id[$pkey] = $insert->{$pkey};
												}
											}
											
											if((!empty($primary) && count($primary)==1) || (isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && !property_exists($insert, $profile_params->joins->column1[0]))){
												$newId =$db->insertid();
												if($primary[0]!='id'){
													$query = 'SHOW KEYS FROM '.$db->quoteName($profile_params->table).' WHERE Key_name = "PRIMARY"';
													$db->setQuery( $query );
													// $key = $db->loadObjectList();
													$key = $db->loadAssocList('Column_name');
													$pri = array_keys($key)[0];
													$cached_data->{$pri} = $insert->{$pri} = $newId;
												}else{
													$cached_data->{$primary[0]} = $insert->{$primary[0]} = $db->insertid();
												}
											}
											if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($insert, $profile_params->joins->column1[0])){
												$base_join_val = $insert->{$profile_params->joins->column1[0]};
											}
										}
										else{
											JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
											$log->op_end = JFactory::getDate('now')->toSql(true);
											$log->status = 'abort';
											$log->message = $db->stderr();
											$flag = $this->setLog($log);
											jexit('{"result":"error","error":"'.$db->stderr().'"}');
										}
									}
								}
								else{
									if(isset($profile_params->component->table) && !empty($profile_params->component->table)){
										$component = JText::_($profile_params->component->value);
										$component_name = $component;
										if (strpos(strtolower($component), "com_") ===FALSE){
											$component_name = "com_".strtolower($component_name); 
										}
										if (strpos(strtolower($component), "com_") ===0){
											$component = str_replace("com_", '',strtolower($component)); 
										}
										JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
										$core_tables = array('category', 'content', 'menu', 'module');
										if(in_array($profile_params->component->table, $core_tables)){
											$tbl_instance = JTable::getInstance($profile_params->component->table);
										}
										else{
											$tbl_instance = JTable::getInstance($profile_params->component->table, $component.'Table');
										}
										
										$loadFields = array();
										foreach($primary as $pkey){
											if(isset($insert->{$pkey}) && !empty($insert->{$pkey})){
												$loadFields[$pkey] = $insert->{$pkey};
											}
										}
										$tbl_instance->load($loadFields);
										if(property_exists($tbl_instance, 'parent_id')){
											$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
											if (!$tbl_instance->isLeaf())
												@$tbl_instance->setLocation($parent_id, 'last-child');
										}
										if (!$tbl_instance->bind((array)$insert)) {
											$obj->result = 'error';
											$obj->error = $tbl_instance->getError();
											return $obj;
										}
										if (!$tbl_instance->check()) {
											$obj->result = 'error';
											$obj->error = $tbl_instance->getError();
											return $obj;
										}
										if (!$tbl_instance->store()){
											$obj->result = 'error';
											$obj->error = $tbl_instance->getError();
											return $obj;
										}
										$n++;
										$afterState = true;
										if(!empty($primary)){
											$base_in_id = array();
											foreach($primary as $pkey){
												if(property_exists($tbl_instance, $pkey)){
													$base_in_id[$pkey] = $tbl_instance->{$pkey};
												}
											}
										}
										if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($tbl_instance, $profile_params->joins->column1[0])){
											$base_join_val = $tbl_instance->{$profile_params->joins->column1[0]};
										}
									}
									else{
										if($db->updateObject($profile_params->table, $insert, $primary)){
											if($this->getAffected($db)> 0){
												$n++;
												$afterState = true;
											}
											// $base_in_id = $insert->{$primary};
											$base_in_id = array();
											foreach($primary as $pkey){
												if(isset($insert->{$pkey})){
													$base_in_id[$pkey] = $insert->{$pkey};
												}
											}
											if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 ){
												if(property_exists($insert, $profile_params->joins->column1[0])){
													$base_join_val = $insert->{$profile_params->joins->column1[0]};
												}else{
													//check if record already exists
													$where = array();
													if( !empty($primary)  ){
														foreach($primary as $keyCol){
															if(isset($insert->{$keyCol})){
															$where[] = $db->quoteName($keyCol).'='.$db->quote($insert->{$keyCol});
															}
														}
														if(!empty($where)){
															$query = 'SELECT '.$profile_params->joins->column1[0].' FROM '.$db->quoteName($profile_params->table).' WHERE '.implode(' AND ', $where);
															$db->setQuery( $query );
															$base_join_val = $cached_data->{$profile_params->joins->column1[0]} = $insert->{$profile_params->joins->column1[0]} = $db->loadResult();
														}
													}
												}
											}
										}
										else{
											JLog::add(JText::sprintf('UPDATE_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
											$log->op_end = JFactory::getDate('now')->toSql(true);
											$log->status = 'abort';
											$log->message = $db->stderr();
											$flag = $this->setLog($log);
											jexit('{"result":"error","error":"'.$db->stderr().'"}');
										}
									}
								}
							}
							$dids[] = $base_in_id;
							
							if($afterState){
								//capture events after update
								if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'after') ){
									if(!empty($primary)){
										foreach($primary as $pkey){
											if(property_exists($cached_data, $pkey) && isset($base_in_id[$pkey])){
												$cached_data->{$pkey} = $base_in_id[$pkey];
											}
										}
									}
									$this->profile = new stdClass();
									$this->profile->params = $profile_params;
									$response = $this->captureEventsOnRecord( $cached_data, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
								}
							}
						
						}
					
						if($profile_params->operation!=3 && isset($profile_params->joins)){
							$join_dids[] = $base_join_val; 
							$flag = $this->insertXmlJoinRecords($base_join_val, $profile_params->joins, $row, $xmlfields, $rootTag, $childTags, $profile_params->operation);
							if($flag->result=='error'){
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$obj->error = $flag->error;
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$db->stderr().'"}');
							}
						}
					}
				}
				
			}
			
			JLog::add(JText::sprintf('IMPORT_RECORD_COUNT', $n), JLog::INFO, 'com_vdata');
			if( ($profile_params->operation==2 || $profile_params->operation==3) ){
				$deleteQry = 'DELETE FROM '.$db->quoteName($profile_params->table). ' WHERE %s';
				$whereCon = array();
				foreach($dids as $did){
					if(is_array($did)){
						$where = array();
						foreach($did as $pkey=>$pval){
							$where[] = $db->quoteName($pkey).' <> '.$db->quote($pval);
						}
						$whereCon[] = ' ('.implode(' AND ', $where).')';
					}
					else{
						$whereCon[] = ' '.$db->quoteName($primary).' <> '.$db->quote($did);
					}
				}
				//apply delete filter for delete operation
				if($profile_params->operation==3){
					//apply filters
					if(isset($profile_params->filters->column)){
						$filterValues = $this->importDeleteFilter($db, $profile_params);
						if(!empty($filterValues)){
							$whereCon[] = $filterValues;
						}
					}
				}
				if(!empty($whereCon)){
					$db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
					$res = $db->execute();
				}
				
				if(isset($profile_params->joins)){
					$whereCon = array();
					$table2 = $profile_params->joins->table2;
					for($i=0;$i<count($table2);$i++){
						$deleteQry = 'DELETE FROM '.$db->quoteName($table2[$i]). ' WHERE %s';
						foreach($join_dids as $jdid){
							if(is_array($jdid)){
								$where = array();
								foreach($jdid as $pkey=>$pval){
									$where[] = $db->quoteName($pkey).' <> '.$db->quote($pval);
								}
								$whereCon[] = ' ('.implode(' AND ', $where).')';
							}
							else{ 
								$whereCon[] = $db->quoteName($profile_params->joins->column2[$i]).' <> '.$db->quote($jdid);
							}
						}
						if(!empty($whereCon)){
							$db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
							$res = $db->execute();
						}
					}
				}
			}
			//remove copy of remote data file
			$isRemoteFile = ($params->server=='local')? false:true;
			if($isRemoteFile){
				$ul = @unlink($path);
				if($ul===false){
					JLog::add(JText::_('unable to remove local copy of remote data file'), JLog::INFO, 'com_vdata');
				}
			}
			//send notification email
			$notifyProfile = ($schedule->profileid!=0)?$profile->title:"";
			$notifyQry = ($schedule->profileid==0)?$schedule->qry:"";
			$notify = $this->sendNotificationEmail( $schedule->iotype,1 , array('schedule_id'=>$schedule->id,'schedule_title'=>$schedule->title,"schedule_qry"=>$notifyQry,'profile_id'=>$schedule->profileid,'profile_title'=>$notifyProfile,"count"=>$n, "format"=>"xml") );
			
			$log->op_end =  JFactory::getDate(microtime(true))->toSql(true);
			$log->status = 'success';
			$log->message = JText::sprintf('LOG_IMPORT_SUCCESS_MSG', $n);
			$log->iocount = $n;
			$flag = $this->setLog($log);
			jexit('{"result":"success","message":"'.JText::sprintf('LOG_IMPORT_SUCCESS_MSG', $n).'"}');
		}
		
		function autoImportJson($schedule){

			$log = new stdClass();
			$log->iotype = 0;
			$log->profileid = $schedule->profileid;
			$log->cronid = $schedule->id;
			$log->side = 'cron';
			
			$log->user = JFactory::getUser()->id;
			$op_start = JFactory::getDate('now')->format('m-d-Y_hia');
			$log_file = 'com_vdata_plg_custom'.'_'.$op_start.'.txt';
			
			$config = $this->getConfig();
			if($config->logging){
				$logger = $this->initializeLogger();
			}
			
			$log->logfile = $log_file;
			$log->op_start =  JFactory::getDate(microtime(true))->toSql(true);
			
			$params = json_decode($schedule->params);
			$schedule_cols = json_decode($schedule->columns);
			
			$db = $this->getDbc();
			//fetch profile details
			$query = 'select * from #__vd_profiles where id='.$schedule->profileid;
			$db->setQuery($query);
			$profile = $db->loadObject();
			$profile_params = json_decode($profile->params);
			
			$query = 'SHOW KEYS FROM '.$db->quoteName($profile_params->table).' WHERE Key_name = "PRIMARY"';
			$db->setQuery( $query );
			$key = $db->loadAssocList('Column_name');
			if(property_exists($profile_params, 'unqkey') && !empty($profile_params->unqkey))
				$primary = $profile_params->unqkey;
			elseif(!empty($key))
				$primary = array_keys($key);
			else
				$primary = null;

			
			$path = $params->path;
			if($params->server=='local'){
				$path = JPATH_ROOT.'/'.$path;
			}
			elseif($params->server=='absolute'){
				$newPath = JPATH_ROOT.'/'.'components/com_vdata/uploads/'.$profile->title.'.json';
				if(!copy($path, $newPath)){
					$copy_error = error_get_last();
					jexit('{"result":"error","error":"'.$copy_error['message'].'"}');
				}
				$path = $newPath;
			}
			else{
				//copy remote file using ftp
				$inputFtp = (array)$params->ftp;
				if( empty($inputFtp['ftp_host']) || empty($inputFtp['ftp_user']) || empty($inputFtp['ftp_pass']) || empty($inputFtp['ftp_file']) ){
					jexit('{"result":"error","error":"'.JText::_('VDATA_EMPTY_FTP_CREDENTIALS').'"}');
				}
				$ext = strrchr($inputFtp['ftp_file'], '.');
				if($ext <> '.json'){
					jexit('{"result":"error","error":"'.JText::_('PLZ_UPLOAD_CSV_FILE').'"}');
				}
				$ftp = $this->ftpConnection($inputFtp);
				if($ftp->result=='error'){
					jexit('{"result":"error","error":"'.$ftp->error.'"}');
				}
				$remotePath = rtrim($inputFtp['ftp_directory'], '/').'/'.$inputFtp['ftp_file'];
				$path = JPATH_ROOT.'/'.'components/com_vdata/uploads/'.$inputFtp['ftp_file'].'.json';
				$ftpHelper = $ftp->ftpHelper;
				if(!$ftpHelper->download($path, $remotePath)){
					jexit('{"result":"error","error":"'.$ftpHelper->getMessage().'"}');
				}
			}
			$log->iofile = $path;
			$log->table = $profile_params->table;
			
			//log info
			JLog::add(JText::sprintf('IMPORT_PROFILE', $profile->title), JLog::INFO, 'com_vdata');
			JLog::add(JText::sprintf('IMPORT_TABLE', $profile_params->table), JLog::INFO, 'com_vdata');
			JLog::add(JText::sprintf('IMPORT_OPERATION', $profile_params->operation), JLog::INFO, 'com_vdata');
			JLog::add(JText::sprintf('IMPORT_FORMAT', 'JSON'), JLog::INFO, 'com_vdata');
			
			$dids = array();
			$join_dids = array();
			
			$query = 'show fields FROM '.$db->quoteName($profile_params->table);
			$db->setQuery( $query );
			$cols = $db->loadObjectList();
			
			$data = $this->getJsonData($path);
			if(!$data->result){
				jexit('{"result":"error","error":"'.$data->error.'"}');
			}
			$json_data = $data->data;
			
			if(empty($json_data)){
				JLog::add(JText::_('NO_RECORD_TO_IMPORT'), JLog::ERROR, 'com_vdata');
				$log->op_end = JFactory::getDate('now')->toSql(true);
				$log->status = 'abort';
				$log->message = JText::_('NO_RECORD_TO_IMPORT');
				$flag = $this->setLog($log);
				jexit('{"result":"success","message":"'.JText::_('NO_RECORD_TO_IMPORT').'"}');
			}
			$n = 0;
			if( property_exists($schedule_cols, 'quick') ){
				
				foreach($json_data as $i=>$row){
					$insert = new stdClass();
					if($profile_params->operation==3){
						//apply primary key and custom filters
						$where = array();
						$base_in_id = array();
						$quick_fields = json_decode(json_encode($schedule_cols->fields), true);
						if(isset($profile_params->unqkey)){
							foreach($profile_params->unqkey as  $key){
								if(isset($quick_fields[$key]) && ($quick_fields[$key][0]!="")){
									$jsonVal = $this->getJsonFieldValue($row, $quick_fields[$key][count($quick_fields[$key])-1]);
									$where[] = "(".$db->quoteName($key).' = '.$db->quote($jsonVal).")";
									$base_in_id[$key] = $jsonVal;
								}
							}
						}
						if(!empty($where)){
							$statement = 'SELECT count(*) FROM ' . $db->quoteName($profile_params->table) . ' WHERE %s';
							$query = $db->getQuery(true);
							$db->setQuery(sprintf($statement, implode(' AND ', $where)));
							$result = $db->loadResult();
							if($result>0 && !empty($base_in_id)){
								$dids[] = $base_in_id;
							}
						}
					}
					else{
						if($schedule_cols->quick==1){
							foreach($cols as $k=>$col){
								if( isset($row[$col->Field]) ){
									$insert->{$col->Field} = $row[$col->Field];
								}
							}
						}
						else{
							$quick_fields = json_decode(json_encode($schedule_cols->fields), true);
							foreach($cols as $k=>$col){
								if(isset($quick_fields[$col->Field]) && $quick_fields[$col->Field][0]!="" ){
									$insert->{$col->Field} = $this->getJsonFieldValue($row, $quick_fields[$col->Field][count($quick_fields[$col->Field])-1]);
								}
							}
						}
					}
					//if primary key exists check existing record
					$isNew = true;
					$oldData = array();
					$where = array();
					if( !empty($primary)){// && !empty($insert->{$primary})
						foreach($primary as $keyCol){
							if(isset($insert->{$keyCol})){
							$where[] = $db->quoteName($keyCol).'='.$db->quote($insert->{$keyCol});
							}
						}
						if(!empty($where)){
							$query = 'SELECT * FROM '.$db->quoteName($profile_params->table).' WHERE '.implode(' AND ', $where);
							$db->setQuery( $query );
							$oldData = $db->loadAssoc();
							$isNew = empty($oldData) ? true : false;
						}
					}
					
					if($profile_params->operation==3){
						//delete data operation
					}
					elseif($profile_params->operation==1){
						if(!$isNew){
							JLog::add(JText::sprintf('RECORD_ALREADY_EXISTS',implode(',',$where)), JLog::ERROR, 'com_vdata');
							continue;
						}
						//capture events before insert
						if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'before') ){
							//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $profile_params->events->before)), JLog::INFO, 'com_vdata');
							$this->profile = new stdClass();
							$this->profile->params = $profile_params;
							$response = $this->captureEventsOnRecord( $insert, 'before', $isNew , array('pk'=>$primary ,'old'=>$oldData));
							if(in_array(false, $response)){
								JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
								continue;
							}
						}
						if(!$db->insertObject($profile_params->table, $insert)){
							JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
							$log->op_end = JFactory::getDate('now')->toSql(true);
							$log->status = 'abort';
							$log->message = $db->stderr();
							$flag = $this->setLog($log);
							jexit('{"result":"error","error":"'.$db->stderr().'"}');
						}
						if($this->getAffected($db)> 0){
							$n++;
							//capture events after insert
							if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'after') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $profile_params->after)), JLog::INFO, 'com_vdata');
								$this->profile = new stdClass();
								$this->profile->params = $profile_params;
								$response = $this->captureEventsOnRecord( $insert, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
							}
						}
					}
					else{
						if(empty($primary)){
							JLog::add(JText::_('PRIMARY_KEY_NOT_FOUND'), JLog::ERROR, 'com_vdata');
							$log->op_end = JFactory::getDate('now')->toSql(true);
							$log->status = 'abort';
							$log->message = JText::_('PRIMARY_KEY_NOT_FOUND');
							$flag = $this->setLog($log);
							jexit('{"result":"error","error":"'.JText::_('PRIMARY_KEY_NOT_FOUND').'"}');
						}
						else{
							foreach($primary as $pkey){
								if(!isset($insert->{$pkey}) || empty($insert->{$pkey})){
									JLog::add(JText::_('PRIMARY_KEY_NOT_FOUND'), JLog::ERROR, 'com_vdata');
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = JText::_('PRIMARY_KEY_NOT_FOUND');
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.JText::_('PRIMARY_KEY_NOT_FOUND').'"}');
								}
							}
						}
						
						//capture events before update
						if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'before') ){
							//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $profile_params->events->before)), JLog::INFO, 'com_vdata');
							$this->profile = new stdClass();
							$this->profile->params = $profile_params;
							$response = $this->captureEventsOnRecord( $insert, 'before',$isNew , array('pk'=>$primary ,'old'=>$oldData));
							if(in_array(false, $response)){
								JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
								continue;
							}
						}
						
						if($isNew){
							JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
							if(!$db->insertObject($profile_params->table, $insert)){
								JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $db->stderr();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$db->stderr().'"}');
							}
						}
						else{
							if(!$db->updateObject($profile_params->table, $insert, $primary)){
								JLog::add(JText::sprintf('UPDATE_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $db->stderr();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$db->stderr().'"}');
							}
						}
						
						$base_in_id = array();
						foreach($primary as $pkey){
							if(isset($insert->{$pkey})){
								$base_in_id[$pkey] = $insert->{$pkey};
							}
						}
						$dids[] = $base_in_id;
						
						if($this->getAffected($db)> 0){
							$n++;
							//capture events after update
							if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'after') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $profile_params->events->after)), JLog::INFO, 'com_vdata');
								$this->profile = new stdClass();
								$this->profile->params = $profile_params;
								$response = $this->captureEventsOnRecord( $insert, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
							}
						}
					}
				}
			}
			else{
				$jsonfields = json_decode(json_encode($schedule_cols->fields), true);
				$base = json_decode(json_encode($schedule_cols->base), true);
				$root = json_decode(json_encode($schedule_cols->root), true);
				$images_import = array();
				if(property_exists($schedule_cols,'images'))
					$images_import = json_decode(json_encode($schedule_cols->images), true);
				
				foreach($json_data as $i=>$row){
					$isNew = true;
					$insert = new stdClass();
					$stance = false;
					$cached_data = new stdClass();
					$oldData = array();
					if($profile_params->operation==3){
						//apply primary key and custom filters
						$where = array();
						$base_in_id = array();
						if(isset($profile_params->unqkey)){
							foreach($profile_params->unqkey as  $key){
								if(!empty($jsonfields[$key]) && ($jsonfields[$key][0]!="")){
									$fieldVal = $this->getJsonFieldValue($row, $jsonfields[$key][count($jsonfields[$key])-1]);
									$where[] = "(".$db->quoteName($key).' = '.$db->quote($fieldVal).")";
									$base_in_id[$key] = $fieldVal;
								}
							}
						}
						if(!empty($where)){
							$statement = 'SELECT count(*) FROM ' . $db->quoteName($profile_params->table) . ' WHERE %s';
							$query = $db->getQuery(true);
							$db->setQuery(sprintf($statement, implode(' AND ', $where)));
							$result = $db->loadResult();
							if($result>0 && !empty($base_in_id)){
								$dids[] = $base_in_id;
							}
						}
					}
					else{
						foreach($profile_params->fields as $column=>$field)	{
							switch($field->data) :
								case 'file' :
									if(empty($jsonfields[$column]) || $jsonfields[$column][0]==""){
										break;
									}
									$fieldVal = $this->getJsonFieldValue($row, $jsonfields[$column][count($jsonfields[$column])-1]);
									$insert->{$column} = $cached_data->{$column} = $this->getFilteredValue($field, $fieldVal);
									if( isset($field->format) && ($field->format=='email') && ($insert->{$column}==false) ){
										JLog::add(JText::sprintf('VDATA_EMAIL_VALIDATION_FAILED', $column), JLog::ERROR, 'com_vdata');
										unset($insert->{$column}, $cached_data->{$column});
									}
									if($field->format == "encrypt"){
										$cached_data->{$column} = $fieldVal;
									}
									if($field->format == "image"){
										$filename = $insert->{$column};$destination = rtrim($field->location,'/').'/'. basename($filename);
										$image_source=$path_type='';
										if(isset($images_import['root'][$column])){
											$image_source = isset($images_import['root'][$column]['image_source'])?$images_import['root'][$column]['image_source']:'';
											$path_type = isset($images_import['root'][$column]['path_type'])?$images_import['root'][$column]['path_type']:'';											
										}	
										if(!$image = $this->uploadImage($filename,$destination,$image_source,$path_type,$err)){
													
											$insert->{$column} = $cached_data->{$column} = $destination;
											jexit('{"result":"error","error":"'.$err.'"}');
										}
									} 
								break;
								case 'defined' :
									$defined = $field->default;
									$defined = $this->getDefinedJsonValue($field->default, $profile_params->fields, $row, $jsonfields);
									$insert->{$column} = $cached_data->{$column} =  $defined;
								break;
								case 'reference' :
									//one to one relation							
									$insert_ref = new stdClass();
									foreach($field->reftext as $k=>$ref)
									{
										if( array_key_exists($ref, $jsonfields[$column]) && ($jsonfields[$column][$ref]!="") ){
											$insert_ref->{$ref} = $this->getJsonFieldValue($row, $jsonfields[$column][$ref][count($jsonfields[$column][$ref])-1]);
										}
									}
									
									$insert_ref_val = (array)$insert_ref;
									if(!empty($insert_ref_val)){
										$ref_value = $this->getReferenceVal($field->table, $field->on, $insert_ref, $db);
										if(!empty($ref_value)){
											$insert->{$column} = $ref_value;
											$cached_data->{$column} = $ref_value;
										}
										else{
											JLog::add(JText::sprintf('LOCAL_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
										}
									}
									else{
										JLog::add(JText::sprintf('REMOTE_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
									}
								break;
								case 'asset_reference':
									$stance = true;
									$insert->$column = 0;
									$intance_column = $field->on;
									$instance_component = $field->table;
									if($jsonfields[$column]['rules']!=""){
										$fieldVal = $this->getJsonFieldValue($row, $jsonfields[$column]['rules'][count($jsonfields[$column]['rules'])-1]);
										$insert->rules = json_decode($fieldVal, true);
									}
								break;
							endswitch;
						}
					}
					//check if id already exists
					$where = array();
					if(!empty($primary)){
						foreach($primary as $keyCol){
							if(isset($insert->{$keyCol})){
							$where[] = $db->quoteName($keyCol).'='.$db->quote($insert->{$keyCol});
							}
						}
						if(!empty($where)){
							$query = 'SELECT * FROM '.$db->quoteName($profile_params->table).' WHERE '.implode(' AND ', $where);
							$db->setQuery( $query );
							$oldData = $db->loadAssoc();
							$isNew = empty($oldData) ? true : false;
						}
					}
					$afterState = false;
					
					if($profile_params->operation==3){
						//delete data operation
					}
					elseif($profile_params->operation==1){
						if(!$isNew){
							JLog::add(JText::sprintf('RECORD_ALREADY_EXISTS',implode(',',$where)), JLog::ERROR, 'com_vdata');
							continue;
						}
						//capture events before insert
						if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'before') ){
							//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $profile_params->events->before)), JLog::INFO, 'com_vdata');
							$this->profile = new stdClass();
							$this->profile->params = $profile_params;
							$response = $this->captureEventsOnRecord( $insert, 'before',$isNew, array('pk'=>$primary ,'old'=>$oldData) );
							if(in_array(false, $response)){
								JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
								continue;
							}
						}
						
						$base_join_val = null;
						if($stance){
							$component = JText::_($instance_component);
							$component_name = $component;
							if (strpos(strtolower($component), "com_") ===FALSE){
								$component_name = "com_".strtolower($component_name); 
							}
							if (strpos(strtolower($component), "com_") ===0){
								$component = str_replace("com_", '',strtolower($component)); 
							}
							JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
							$core_tables = array('category', 'content', 'menu', 'module');
							if(in_array($intance_column, $core_tables)){
								$tbl_instance = JTable::getInstance($intance_column);
							}
							else{
								$tbl_instance = JTable::getInstance($intance_column, $component.'Table');
							}
							if(property_exists($tbl_instance, 'parent_id')){
								$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
								@$tbl_instance->setLocation($parent_id, 'last-child');
							}
							
							if (!$tbl_instance->bind((array)$insert)) {
								JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $tbl_instance->getError();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
							}
							if (!$tbl_instance->check()) {
								JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $tbl_instance->getError();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
							}
							if (!$tbl_instance->store()){
								JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $tbl_instance->getError();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
							}
							if(!empty($primary)){
								$base_in_id = array();
								foreach($primary as $pkey){
									if(property_exists($tbl_instance, $pkey)){
										$base_in_id[$pkey] = $tbl_instance->{$pkey};
									}
								}
							}
							$n++;
							$afterState = true;
							if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($tbl_instance, $profile_params->joins->column1[0])){
								$base_join_val = $tbl_instance->{$profile_params->joins->column1[0]};
							}
						}
						else{
							if(isset($profile_params->component->table) && !empty($profile_params->component->table)){
								$component = JText::_($profile_params->component->value);
								$component_name = $component;
								if (strpos(strtolower($component), "com_") ===FALSE){
									$component_name = "com_".strtolower($component_name); 
								}
								if (strpos(strtolower($component), "com_") ===0){
									$component = str_replace("com_", '',strtolower($component)); 
								}
								JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
								$core_tables = array('category', 'content', 'menu', 'module');
								if(in_array($profile_params->component->table, $core_tables)){
									$tbl_instance = JTable::getInstance($profile_params->component->table);
								}
								else{
									$tbl_instance = JTable::getInstance($profile_params->component->table, $component.'Table');
								}
								
								// $tbl_instance->load(null);
								if(property_exists($tbl_instance, 'parent_id')){
									$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
									@$tbl_instance->setLocation($parent_id, 'last-child');
								}
								if (!$tbl_instance->bind((array)$insert)) {
									JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $tbl_instance->getError();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
								}
								if (!$tbl_instance->check()) {
									JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $tbl_instance->getError();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
								}
								if (!$tbl_instance->store()){
									JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $tbl_instance->getError();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
								}
								$n++;
								$afterState = true;
								if(!empty($primary)){
									$base_in_id = array();
									foreach($primary as $pkey){
										if(property_exists($tbl_instance, $pkey)){
											$base_in_id[$pkey] = $tbl_instance->{$pkey};
										}
									}
								}
								if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($tbl_instance, $profile_params->joins->column1[0])){
									$base_join_val = $tbl_instance->{$profile_params->joins->column1[0]};
								}
							}
							else{
								if($db->insertObject($profile_params->table, $insert)){	
									if($this->getAffected($db)> 0){
										$n++;
										$afterState = true;
									}
									$base_in_id = array();
									foreach($primary as $pkey){
										if(isset($insert->{$pkey})){
											$base_in_id[$pkey] = $insert->{$pkey};
										}
									}
										
									if((!empty($primary) && count($primary)==1) || (isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && !property_exists($insert, $profile_params->joins->column1[0]))){
										$newId =$db->insertid();
										if($primary[0]!='id'){
											$query = 'SHOW KEYS FROM '.$db->quoteName($profile_params->table).' WHERE Key_name = "PRIMARY"';
											$db->setQuery( $query );
											// $key = $db->loadObjectList();
											$key = $db->loadAssocList('Column_name');
											$pri = array_keys($key)[0];
											$cached_data->{$pri} = $insert->{$pri} = $newId;
										}else{
											$cached_data->{$primary[0]} = $insert->{$primary[0]} = $db->insertid();
										}
									}
									if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($insert, $profile_params->joins->column1[0])){
										$base_join_val = $insert->{$profile_params->joins->column1[0]};
									}
									
								}
								else{
									JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $db->stderr();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$db->stderr().'"}');
								}
							}
						}
						if($afterState){
							//capture events after insert
							if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'after') ){
								if(!empty($primary)){
									foreach($primary as $pkey){
										if(property_exists($cached_data, $pkey) && isset($base_in_id[$pkey])){
											$cached_data->{$pkey} = $base_in_id[$pkey];
										}
									}
								}
								$this->profile = new stdClass();
								$this->profile->params = $profile_params;
								$response = $this->captureEventsOnRecord( $cached_data, 'after',$isNew, array('pk'=>$primary ,'success'=>true) );
							}
						}
					}
					else{
						if(empty($primary)){
							JLog::add(JText::_('PRIMARY_KEY_NOT_FOUND'), JLog::ERROR, 'com_vdata');
							$log->op_end = JFactory::getDate('now')->toSql(true);
							$log->status = 'abort';
							$log->message = JText::_('PRIMARY_KEY_NOT_FOUND');
							$flag = $this->setLog($log);
							jexit('{"result":"error","error":"'.JText::_('PRIMARY_KEY_NOT_FOUND').'"}');
						}
						else{
							foreach($primary as $pkey){
								if(!isset($insert->{$pkey}) || empty($insert->{$pkey})){
									JLog::add(JText::_('PRIMARY_KEY_NOT_FOUND'), JLog::ERROR, 'com_vdata');
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = JText::_('PRIMARY_KEY_NOT_FOUND');
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.JText::_('PRIMARY_KEY_NOT_FOUND').'"}');
								}
							}
						}
						
						//capture events before update
						if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'before') ){
							//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $profile_params->events->before)), JLog::INFO, 'com_vdata');
							$this->profile = new stdClass();
							$this->profile->params = $profile_params;
							$response = $this->captureEventsOnRecord( $insert, 'before',$isNew, array('pk'=>$primary ,'old'=>$oldData));
							if(in_array(false, $response)){
								JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
								continue;
							}
						}
						
						$base_join_val = null;
						if($stance){
							$component = JText::_($instance_component);
							$component_name = $component;
							if (strpos(strtolower($component), "com_") ===FALSE){
								$component_name = "com_".strtolower($component_name); 
							}
							if (strpos(strtolower($component), "com_") ===0){
								$component = str_replace("com_", '',strtolower($component)); 
							}
							JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
							$core_tables = array('category', 'content', 'menu', 'module');
							if(in_array($intance_column, $core_tables)){
								$tbl_instance = JTable::getInstance($intance_column);
							}
							else{
								$tbl_instance = JTable::getInstance($intance_column, $component.'Table');
							}
							
							$loadFields = array();
							foreach($primary as $pkey){
								if(isset($insert->{$pkey}) && !empty($insert->{$pkey})){
									$loadFields[$pkey] = $insert->{$pkey};
								}
							}
							$tbl_instance->load($loadFields);
							if(property_exists($tbl_instance, 'parent_id')){
								$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
								if (!$tbl_instance->isLeaf())
									@$tbl_instance->setLocation($parent_id, 'last-child');
							}
							if($isNew){
								JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
							}
							if (!$tbl_instance->bind((array)$insert)) {
								JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $tbl_instance->getError();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
							}
							if (!$tbl_instance->check()) {
								JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $tbl_instance->getError();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
							}
							if (!$tbl_instance->store()){
								JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $tbl_instance->getError();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
							}
							$n++;
							$afterState = true;
							if(!empty($primary)){
								$base_in_id = array();
								foreach($primary as $pkey){
									if(property_exists($tbl_instance, $pkey)){
										$base_in_id[$pkey] = $tbl_instance->{$pkey};
									}
								}
							}
							if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($tbl_instance, $profile_params->joins->column1[0])){
								$base_join_val = $tbl_instance->{$profile_params->joins->column1[0]};
							}
						}
						else{
							if($isNew){
								JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
								if(isset($profile_params->component->table) && !empty($profile_params->component->table)){
									$component = JText::_($profile_params->component->value);
									$component_name = $component;
									if (strpos(strtolower($component), "com_") ===FALSE){
										$component_name = "com_".strtolower($component_name); 
									}
									if (strpos(strtolower($component), "com_") ===0){
										$component = str_replace("com_", '',strtolower($component)); 
									}
									JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
									$core_tables = array('category', 'content', 'menu', 'module');
									if(in_array($profile_params->component->table, $core_tables)){
										$tbl_instance = JTable::getInstance($profile_params->component->table);
									}
									else{
										$tbl_instance = JTable::getInstance($profile_params->component->table, $component.'Table');
									}
									// $tbl_instance->load(null);
									if(property_exists($tbl_instance, 'parent_id')){
										$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
										@$tbl_instance->setLocation($parent_id, 'last-child');
									}
									if (!$tbl_instance->bind((array)$insert)) {
										JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
										$log->op_end = JFactory::getDate('now')->toSql(true);
										$log->status = 'abort';
										$log->message = $tbl_instance->getError();
										$flag = $this->setLog($log);
										jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
									}
									if (!$tbl_instance->check()) {
										JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
										$log->op_end = JFactory::getDate('now')->toSql(true);
										$log->status = 'abort';
										$log->message = $tbl_instance->getError();
										$flag = $this->setLog($log);
										jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
									}
									if (!$tbl_instance->store()){
										JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
										$log->op_end = JFactory::getDate('now')->toSql(true);
										$log->status = 'abort';
										$log->message = $tbl_instance->getError();
										$flag = $this->setLog($log);
										jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
									}
									$n++;
									$afterState = true;
									if(!empty($primary)){
										$base_in_id = array();
										foreach($primary as $pkey){
											if(property_exists($tbl_instance, $pkey)){
												$base_in_id[$pkey] = $tbl_instance->{$pkey};
											}
										}
									}
									if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($tbl_instance, $profile_params->joins->column1[0])){
										$base_join_val = $tbl_instance->{$profile_params->joins->column1[0]};
									}
								}
								else{
									if($db->insertObject($profile_params->table, $insert)){
										if($this->getAffected($db)> 0){
											$n++;
											$afterState = true;
										}
										$base_in_id = array();
										foreach($primary as $pkey){
											if(isset($insert->{$pkey})){
												$base_in_id[$pkey] = $insert->{$pkey};
											}
										}
											
										if((!empty($primary) && count($primary)==1) || (isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && !property_exists($insert, $profile_params->joins->column1[0]))){
											$newId =$db->insertid();
											if($primary[0]!='id'){
												$query = 'SHOW KEYS FROM '.$db->quoteName($profile_params->table).' WHERE Key_name = "PRIMARY"';
												$db->setQuery( $query );
												// $key = $db->loadObjectList();
												$key = $db->loadAssocList('Column_name');
												$pri = array_keys($key)[0];
												$cached_data->{$pri} = $insert->{$pri} = $newId;
											}else{
												$cached_data->{$primary[0]} = $insert->{$primary[0]} = $db->insertid();
											}
										}
										if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($insert, $profile_params->joins->column1[0])){
											$base_join_val = $insert->{$profile_params->joins->column1[0]};
										}
									}
									else{
										JLog::add(JText::sprintf('INSERT_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
										$log->op_end = JFactory::getDate('now')->toSql(true);
										$log->status = 'abort';
										$log->message = $db->stderr();
										$flag = $this->setLog($log);
										jexit('{"result":"error","error":"'.$db->stderr().'"}');
									}
								}
							}
							else{
								if(isset($profile_params->component->table) && !empty($profile_params->component->table)){
									$component = JText::_($profile_params->component->value);
									$component_name = $component;
									if (strpos(strtolower($component), "com_") ===FALSE){
										$component_name = "com_".strtolower($component_name); 
									}
									if (strpos(strtolower($component), "com_") ===0){
										$component = str_replace("com_", '',strtolower($component)); 
									}
									JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
									$core_tables = array('category', 'content', 'menu', 'module');
									if(in_array($profile_params->component->table, $core_tables)){
										$tbl_instance = JTable::getInstance($profile_params->component->table);
									}
									else{
										$tbl_instance = JTable::getInstance($profile_params->component->table, $component.'Table');
									}
									$loadFields = array();
									foreach($primary as $pkey){
										if(isset($insert->{$pkey}) && !empty($insert->{$pkey})){
											$loadFields[$pkey] = $insert->{$pkey};
										}
									}
									$tbl_instance->load($loadFields);
									if(property_exists($tbl_instance, 'parent_id')){
										$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
										if (!$tbl_instance->isLeaf())
											@$tbl_instance->setLocation($parent_id, 'last-child');
									}
									if (!$tbl_instance->bind((array)$insert)) {
										JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
										$log->op_end = JFactory::getDate('now')->toSql(true);
										$log->status = 'abort';
										$log->message = $tbl_instance->getError();
										$flag = $this->setLog($log);
										jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
									}
									if (!$tbl_instance->check()) {
										JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
										$log->op_end = JFactory::getDate('now')->toSql(true);
										$log->status = 'abort';
										$log->message = $tbl_instance->getError();
										$flag = $this->setLog($log);
										jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
									}
									if (!$tbl_instance->store()){
										JLog::add(JText::sprintf('INSERT_FAIL',$tbl_instance->getError()), JLog::ERROR, 'com_vdata');
										$log->op_end = JFactory::getDate('now')->toSql(true);
										$log->status = 'abort';
										$log->message = $tbl_instance->getError();
										$flag = $this->setLog($log);
										jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
									}
									$n++;
									$afterState = true;
									if(!empty($primary)){
										$base_in_id = array();
										foreach($primary as $pkey){
											if(property_exists($row, $pkey)){
												$base_in_id[$pkey] = $row->{$pkey};
											}
										}
									}
									if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($tbl_instance, $profile_params->joins->column1[0])){
										$base_join_val = $tbl_instance->{$profile_params->joins->column1[0]};
									}
								}
								else{
									if($db->updateObject($profile_params->table, $insert, $primary)){
										if($this->getAffected($db)> 0){
											$n++;
											$afterState = true;
										}
										// $base_in_id = $insert->{$primary};
										$base_in_id = array();
										foreach($primary as $pkey){
											if(isset($insert->{$pkey})){
												$base_in_id[$pkey] = $insert->{$pkey};
											}
										}
										if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 ){
											if(property_exists($insert, $profile_params->joins->column1[0])){
												$base_join_val = $insert->{$profile_params->joins->column1[0]};
											}else{
												//check if record already exists
												$where = array();
												if( !empty($primary)  ){
													foreach($primary as $keyCol){
														if(isset($insert->{$keyCol})){
														$where[] = $db->quoteName($keyCol).'='.$db->quote($insert->{$keyCol});
														}
													}
													if(!empty($where)){
														$query = 'SELECT '.$profile_params->joins->column1[0].' FROM '.$db->quoteName($profile_params->table).' WHERE '.implode(' AND ', $where);
														$db->setQuery( $query );
														$base_join_val = $cached_data->{$profile_params->joins->column1[0]} = $insert->{$profile_params->joins->column1[0]} = $db->loadResult();
													}
												}
											}
										}
									}
									else{
										JLog::add(JText::sprintf('UPDATE_FAIL',$db->stderr()), JLog::ERROR, 'com_vdata');
										$log->op_end = JFactory::getDate('now')->toSql(true);
										$log->status = 'abort';
										$log->message = $db->stderr();
										$flag = $this->setLog($log);
										jexit('{"result":"error","error":"'.$db->stderr().'"}');
									}
								}
							}
						}
						$dids[] = $base_in_id;
						
						if($afterState){
							//capture events after update
							if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'after') ){
								if(!empty($primary)){
									foreach($primary as $pkey){
										if(property_exists($cached_data, $pkey) && isset($base_in_id[$pkey])){
											$cached_data->{$pkey} = $base_in_id[$pkey];
										}
									}
								}
								$this->profile = new stdClass();
								$this->profile->params = $profile_params;
								$response = $this->captureEventsOnRecord( $cached_data, 'after', $isNew, array('pk'=>$primary ,'success'=>true) );
							}
						}			
					}
					if($profile_params->operation!=3 && isset($profile_params->joins)){
						$join_dids[] = $base_join_val;
						$flag = $this->insertJsonJoinRecords($base_join_val, $profile_params->joins, $row, $jsonfields, $base, $root, $profile_params->operation);
						if($flag->result=='error'){
							$obj->result = 'error';
							$obj->error = $flag->error;
							return $obj;
						}
					}
				}
			}
			JLog::add(JText::sprintf('IMPORT_RECORD_COUNT', $n), JLog::INFO, 'com_vdata');
			if( ($profile_params->operation==2) || ($profile_params->operation==3) ){
				$deleteQry = 'DELETE FROM '.$db->quoteName($profile_params->table). ' WHERE %s';
				$whereCon = array();
				foreach($dids as $did){
					if(is_array($did)){
						$where = array();
						foreach($did as $pkey=>$pval){
							$where[] = $db->quoteName($pkey).' <> '.$db->quote($pval);
						}
						$whereCon[] = ' ('.implode(' AND ', $where).')';
					}
					else{
						$whereCon[] = ' '.$db->quoteName($primary).' <> '.$db->quote($did);
					}
				}
				//apply delete filter for delete operation
				if($profile_params->operation==3){
					//apply filters
					if(isset($profile_params->filters->column)){
						$filterValues = $this->importDeleteFilter($db, $profile_params);
						if(!empty($filterValues)){
							$whereCon[] = $filterValues;
						}
					}
				}
				if(!empty($whereCon)){
					$db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
					$res = $db->execute();
				}
				
				if(isset($profile_params->joins)){
					$whereCon = array();
					$table2 = $profile_params->joins->table2;
					for($i=0;$i<count($table2);$i++){
						$deleteQry = 'DELETE FROM '.$db->quoteName($table2[$i]). ' WHERE %s';
						foreach($join_dids as $jdid){
							 if(is_array($jdid)){
								$where = array();
								foreach($jdid as $pkey=>$pval){
									$where[] = $db->quoteName($pkey).' <> '.$db->quote($pval);
								}
								$whereCon[] = ' ('.implode(' AND ', $where).')';
							}
							else{ 
								$whereCon[] = $db->quoteName($profile_params->joins->column2[$i]).' <> '.$db->quote($jdid);
							}
						}
						if(!empty($whereCon)){
							$db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
							$res = $db->execute();
						}
					}
				}
			}
			//remove copy of remote data file
			$isRemoteFile = ($params->server=='local')? false:true;
			if($isRemoteFile){
				$ul = @unlink($path);
				if($ul===false){
					JLog::add(JText::_('unable to remove local copy of remote data file'), JLog::INFO, 'com_vdata');
				}
			}
			//send notification email
			$notifyProfile = ($schedule->profileid!=0)?$profile->title:"";
			$notifyQry = ($schedule->profileid==0)?$schedule->qry:"";
			$notify = $this->sendNotificationEmail( $schedule->iotype,1 , array('schedule_id'=>$schedule->id,'schedule_title'=>$schedule->title,"schedule_qry"=>$notifyQry,'profile_id'=>$schedule->profileid,'profile_title'=>$notifyProfile,"count"=>$n, "format"=>"json") );
			
			$log->op_end =  JFactory::getDate(microtime(true))->toSql(true);
			$log->status = 'success';
			$log->message = JText::sprintf('LOG_IMPORT_SUCCESS_MSG', $n);
			$log->iocount = $n;
			$flag = $this->setLog($log);
			jexit('{"result":"success","message":"'.JText::sprintf('LOG_IMPORT_SUCCESS_MSG', $n).'"}');
		}
		
		function autoImportRemote($schedule){
			
			$log = new stdClass();
			$log->iotype = 0;
			$log->profileid = $schedule->profileid;
			$log->cronid = $schedule->id;
			$log->side = 'cron';
			$log->user = JFactory::getUser()->id;
			$op_start = JFactory::getDate('now')->format('m-d-Y_hia');
			$log_file = 'com_vdata_plg_custom'.'_'.$op_start.'.txt';
			
			$config = $this->getConfig();
			if($config->logging){
				$logger = $this->initializeLogger();
			}
			
			$log->logfile = $log_file;
			$log->op_start =  JFactory::getDate(microtime(true))->toSql(true);
			
			//joomla database object
			$db = JFactory::getDbo();
			//database object from com_vdata configuration
			$local_db = $this->getDbc();
			$query_local = $local_db->getQuery(true);
			
			$input 	= JFactory::getApplication()->input;
			
			$params = json_decode($schedule->params);
			$schedule_cols = json_decode($schedule->columns);
			
			//fetch profile details
			$query = 'select * from #__vd_profiles where id='.$schedule->profileid;
			$db->setQuery($query);
			$profile = $db->loadObject();
			$profile_params = json_decode($profile->params);
			
			try {
				if(property_exists($params, 'local_db') && ($params->local_db==1)){
					$remote_db = $this->getDbc();
				}
				else{
					$option = array();
					$option['driver'] = $params->driver;
					$option['host'] = $params->host;
					$option['user'] = $params->user;
					$option['password'] = $params->password;
					$option['database'] = $params->database;
					$option['prefix'] = $params->dbprefix;
					
					$remote_db = JDatabaseDriver::getInstance( $option );
					$remote_db->connect();
				}
				
				$query_remote = $remote_db->getQuery(true);
			}
			catch (RuntimeException $e) {
				//log error
				$log->op_end = JFactory::getDate('now')->toSql(true);
				$log->status = 'abort';
				$log->message = $e->getMessage();
				$flag = $this->setLog($log);
				jexit('{"result":"error","error":"'.$e->getMessage().'"}');
			}
			
			$remote_table = $schedule_cols->table;
			//log table name to log database table
			$log->table = $remote_table;
			
			//log info
			JLog::add(JText::sprintf('IMPORT_PROFILE', $profile->title), JLog::INFO, 'com_vdata');
			JLog::add(JText::sprintf('IMPORT_TABLE', $profile_params->table), JLog::INFO, 'com_vdata');
			JLog::add(JText::sprintf('IMPORT_OPERATION', $profile_params->operation), JLog::INFO, 'com_vdata');
			JLog::add(JText::sprintf('IMPORT_FORMAT', 'DATABASE'), JLog::INFO, 'com_vdata');
			
			$dids = array();
			$join_dids = array();
			
			$query_local = 'SHOW KEYS FROM '.$local_db->quoteName($profile_params->table).' WHERE Key_name = "PRIMARY"';
			$local_db->setQuery( $query_local );
			$key = $local_db->loadAssocList('Column_name');
			if(property_exists($profile_params, 'unqkey') && !empty($profile_params->unqkey)){
				$primary = $profile_params->unqkey;
			}
			elseif(!empty($key)){
				$primary = array_keys($key);
			}
			else{
				$primary = null;
			}
			
			
			$query_remote = "select * from ".$remote_db->quoteName($remote_table);
			$remote_db->setQuery($query_remote);
			$rows = $remote_db->loadObjectList();
			if(count($rows)==0)	{
				//log error
				$log->op_end = JFactory::getDate('now')->toSql(true);
				$log->status = 'abort';
				$log->message = JText::_('NO_RECORD_TO_IMPORT');
				$flag = $this->setLog($log);
				jexit('{"result":"success","message":"'.JText::_('NO_RECORD_TO_IMPORT').'"}');
			}
			$n = 0;
			// if it is quick import/export profile
			if( property_exists($schedule_cols, 'quick') ) {
				$query = 'show fields FROM '.$local_db->quoteName($profile_params->table);
				$local_db->setQuery( $query );
				$cols = $local_db->loadObjectList();
			
				$quick_field = $schedule_cols->fields;
				foreach($rows as $row) {
					$insert = new stdClass();
					if($profile_params->operation==3){
						//apply primary key and custom filters
						$where = array();
						$base_in_id = array();
						if(isset($profile_params->unqkey)){
							foreach($profile_params->unqkey as  $key){
								if($quick_field->{$key}!="" && property_exists($row, $quick_field->{$key})){
									$where[] = "(".$local_db->quoteName($key).' = '.$local_db->quote($row->{$quick_field->{$key}}).")";
									$base_in_id[$key] = $row->{$quick_field->{$key}};
								}
							}
						}
						if(!empty($where)){
							$statement = 'SELECT count(*) FROM ' . $local_db->quoteName($profile_params->table) . ' WHERE %s';
							$query = $local_db->getQuery(true);
							$local_db->setQuery(sprintf($statement, implode(' AND ', $where)));
							$resultCount = $local_db->loadResult();
							if($resultCount>0 && !empty($base_in_id)){
								$dids[] = $base_in_id;
							}
						}
					}
					else{
						if($schedule_cols->quick==0){
							foreach($cols as $key=>$value){
								if(!empty($quick_field->{$value->Field})){
									$insert->{$value->Field} = $row->{$quick_field->{$value->Field}};
								}
							}
						}
						else{
							foreach($cols as $key=>$value){
								if(property_exists($row, $value->Field)){
									$insert->{$value->Field} = $row->{$value->Field};
								}
							}
						}
					}
					$isNew = true;
					$oldData = array();
					$where = array();
					if(!empty($primary)){
						foreach($primary as $keyCol){
							if(isset($insert->{$keyCol})){
							$where[] = $local_db->quoteName($keyCol).'='.$local_db->quote($insert->{$keyCol});
							}
						}
						if(!empty($where)){
							$query = "select * from ".$local_db->quoteName($profile_params->table)." where ".implode(' AND ', $where);;
							$local_db->setQuery($query);
							$oldData = $local_db->loadAssoc();
							$isNew = empty($oldData) ? true : false;
						}
					}
					
					if($profile_params->operation==3){
						//delete data operation
					}
					elseif($profile_params->operation==1){
						if(!$isNew){
							JLog::add(JText::sprintf('RECORD_ALREADY_EXISTS',implode(',',$where)), JLog::ERROR, 'com_vdata');
							continue;
						}
						//capture events before insert
						if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'before') ){
							//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $profile_params->events->before)), JLog::INFO, 'com_vdata');
							$this->profile = new stdClass();
							$this->profile->params = $profile_params;
							$response = $this->captureEventsOnRecord( $insert, 'before', $isNew , array('pk'=>$primary ,'old'=>$oldData));
							if(in_array(false, $response)){
								JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
								continue;
							}	
						}
						
						if(!$local_db->insertObject($profile_params->table, $insert))	
						{
							// log error
							$log->op_end = JFactory::getDate('now')->toSql(true);
							$log->status = 'abort';
							$log->message = $local_db->stderr();
							$flag = $this->setLog($log);
							jexit('{"result":"error","error":"'.$local_db->stderr().'"}');
						}
						
						if($this->getAffected($local_db)> 0){
							$n++;
							//capture events after insert
							if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'after') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $profile_params->events->after)), JLog::INFO, 'com_vdata');
								$this->profile = new stdClass();
								$this->profile->params = $profile_params;
								$response = $this->captureEventsOnRecord( $insert, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
							}
						}
							
					}
					else{
						if(empty($primary)){
							$log->op_end = JFactory::getDate('now')->toSql(true);
							$log->status = 'abort';
							$log->message = JText::_('PRIMARY_KEY_NOT_FOUND');
							$flag = $this->setLog($log);
							jexit('{"result":"error","error":"'.JText::_('PRIMARY_KEY_NOT_FOUND').'"}');
						}
						else{
							foreach($primary as $pkey){
								if(!isset($insert->{$pkey}) || empty($insert->{$pkey})){
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = JText::_('PRIMARY_KEY_NOT_FOUND');
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.JText::_('PRIMARY_KEY_NOT_FOUND').'"}');
								}
							}
						}
						
						//capture events before update
						if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'before') ){
							//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $profile_params->events->before)), JLog::INFO, 'com_vdata');
							$this->profile = new stdClass();
							$this->profile->params = $profile_params;
							$response = $this->captureEventsOnRecord( $insert, 'before',$isNew , array('pk'=>$primary ,'old'=>$oldData));
							if(in_array(false, $response)){
								JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
								continue;
							}
						}
						if($isNew){
							JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
							if(!$local_db->insertObject($profile_params->table, $insert))	{
								// log error
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $local_db->stderr();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$local_db->stderr().'"}');
							}
						}
						else{
							if(!$local_db->updateObject($profile_params->table, $insert, $primary)){
								//log error
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $local_db->stderr();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$local_db->stderr().'"}');
							}
						}
						
						$base_in_id = array();
						foreach($primary as $pkey){
							if(isset($insert->{$pkey})){
								$base_in_id[$pkey] = $insert->{$pkey};
							}
						}
						$dids[] = $base_in_id;
						
						if($this->getAffected($local_db)> 0){
							$n++;
							//capture events after update
							if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'after') ){
								//JLog::add(JText::sprintf('TRIGGERING_EVENTS_AFTER_UPDATE', implode(',', $profile_params->events->after)), JLog::INFO, 'com_vdata');
								$this->profile = new stdClass();
								$this->profile->params = $profile_params;
								$response = $this->captureEventsOnRecord( $insert, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
							}
						}	
					}
				}
			}
			else{

				$remote_fields = json_decode(json_encode($schedule_cols->fields),true);
				$images_import = array();
				if(property_exists($schedule_cols,'images'))
					$images_import = json_decode(json_encode($schedule_cols->images), true);
				
				foreach($rows as $row){
					$isNew = true;
					$insert = new stdClass();
					$cached_data = new stdClass();
					$oldData = array();
					$stance = false;
					$intance_column = '';
					if($profile_params->operation==3){
						//apply primary key and custom filters
						$where = array();
						$base_in_id = array();
						if(isset($profile_params->unqkey)){
							foreach($profile_params->unqkey as  $key){
								if($remote_fields[$key]!="" && property_exists($row, $remote_fields[$key])){
									$where[] = "(".$local_db->quoteName($key).' = '.$local_db->quote($row->{$remote_fields[$key]}).")";
									$base_in_id[$key] = $row->{$remote_fields[$key]};
								}
							}
						}
						if(!empty($where)){
							$statement = 'SELECT count(*) FROM ' . $local_db->quoteName($profile_params->table) . ' WHERE %s';
							$query = $local_db->getQuery(true);
							$local_db->setQuery(sprintf($statement, implode(' AND ', $where)));
							$resultCount = $local_db->loadResult();
							if($resultCount>0 && !empty($base_in_id)){
								$dids[] = $base_in_id;
							}
						}
					}
					else{
						foreach($profile_params->fields as $column=>$field){
							switch($field->data){
								case 'file' :
									if( empty($remote_fields[$column]) ){
										break;
									}
									//fetching reference data
									if(is_array($remote_fields[$column])){
										if(!empty($remote_fields[$column]["table"]) && !empty($remote_fields[$column]["column"]) && !empty($remote_fields[$column]["column2"]) && !empty($remote_fields[$column]["column1"]) ){
											
											$query_remote = 'SELECT '.$remote_db->quoteName($remote_fields[$column]["column"]).' FROM '.$remote_db->quoteName($remote_fields[$column]["table"]).' WHERE '.$remote_db->quoteName($remote_fields[$column]["column2"]).' = '.$remote_db->quote($row->{$remote_fields[$column]["column1"]});
											$remote_db->setQuery($query_remote);
											
											if( $ref_val = $remote_db->loadResult() ){
												$insert->{$column} = $ref_val;
												$cached_data->{$column} = $ref_val;
												break;
											}
											else{
												//log error
												$log->op_end = JFactory::getDate('now')->toSql(true);
												$log->status = 'abort';
												$log->message = $remote_db->stderr();
												$flag = $this->setLog($log);
												jexit('{"result":"error","error":"'.$remote_db->stderr().'"}');
											}
										}
									}
									else{
										$insert->{$column} = $cached_data->{$column} = $this->getFilteredValue($field, $row->{$remote_fields[$column]});
										if( isset($field->format) && ($field->format=='email') && ($insert->{$column}==false) ){
											JLog::add(JText::sprintf('VDATA_EMAIL_VALIDATION_FAILED', $column), JLog::ERROR, 'com_vdata');
											unset($insert->{$column}, $cached_data->{$column});
										}
										if($field->format == "encrypt"){
											$cached_data->{$column} = $row->{$remote_fields[$column]};
										}
									}
									if($field->format == "image"){
										$filename = $insert->{$column};$destination = rtrim($field->location,'/').'/'. basename($filename);
											
										$image_source=$path_type='';
										if(isset($images_import['root'][$column])){
											$image_source = isset($images_import['root'][$column]['image_source'])?$images_import['root'][$column]['image_source']:'';
											$path_type = isset($images_import['root'][$column]['path_type'])?$images_import['root'][$column]['path_type']:'';											
										}	
										if(!$image = $this->uploadImage($filename,$destination,$image_source,$path_type,$err)){
													
											$insert->{$column} = $cached_data->{$column} = $destination;
											jexit('{"result":"error","error":"'.$err.'"}');
										}
									} 
								break;
								case 'defined' :
									$defined = $field->default;
									
									$hd_remote_pattern = '/@vdRemote:[\w]*[.?\w]*/';
									$hdRemote = preg_match_all($hd_remote_pattern, $defined, $local_matches);
									if( ($hdRemote!==FALSE) ){
										foreach($local_matches[0] as $mk=>$match){
											if(!empty($match)){
												$fn = explode(':', $match);
												if(!empty($fn[1])){
													$info = explode('.', $fn[1]);
													if(count($info)==1){
														if(property_exists($row, $info[0])){
															$defined = preg_replace('/'.$match.'/', $row->$info[0], $defined);
														}
													}
													elseif(count($info)==2){
															if(!empty($info[0]) && !empty($info[1]) && property_exists($profile_params->fields, $info[0]) && ($profile_params->fields->{$info[0]}->data=='reference') ) {
															if(property_exists($row, $info[0])){
															$query = 'select '.$remote_db->quoteName($info[1]).' from '.$remote_db->quoteName($profile_params->fields->{$info[0]}->table).' where '.$remote_db->quoteName($profile_params->fields->{$info[0]}->on).'='.$remote_db->quote($row->$info[0]);
															$remote_db->setQuery($query);
															$rdata = $remote_db->loadResult();
															$defined = preg_replace('/'.$match.'/', $rdata, $defined);
															}
														}
													}
												}
											}
										}
									}
									$defined = $this->getDefinedValue($field->default, $profile_params->fields, (array)$row, $remote_fields);
									
									$insert->{$column} = $defined;
									$cached_data->{$column} = $defined;
								break;
								case 'reference' :
									//one to one relation
									if( empty($remote_fields[$column]['table']) || empty($remote_fields[$column]['on']) || empty($remote_fields[$column]['value']) || empty($remote_fields[$column]['refcol']) ){
										break;
									}
									$remote_ref_field = array();
									foreach($remote_fields[$column]['refcol'] as $key=>$rfield){
										if(!empty($rfield)){
											$remote_ref_field[] = $rfield;
										}
									}
									
									$query_remote = "select ".implode(',',$remote_ref_field)." from ".$remote_db->quoteName($remote_fields[$column]['table'])." "; 
									$query_remote .= "where ".$remote_db->quoteName($remote_fields[$column]['on'])."=".$row->{$remote_fields[$column]['value']};
									$remote_db->setQuery($query_remote);
									$values = $remote_db->loadObject();
									
									if(!empty($values)){
										$query_local = "select ".$local_db->quoteName($field->on)." from ".$local_db->quoteName($field->table)." where 1=1";
										foreach($remote_ref_field as $ref_field){
											$query_local .= " and ".$local_db->quoteName($ref_field)." = ".$local_db->quote($values->{$ref_field});
										}
										$local_db->setQuery($query_local);
										$ref_value = $local_db->loadResult();
										if(!empty($ref_value)){
											$insert->{$column} = $ref_value;
											$cached_data->{$column} = $ref_value;
										}
										else{
											JLog::add(JText::sprintf('LOCAL_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
										}
									}
									else{
										JLog::add(JText::sprintf('REMOTE_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
									}
								break;
								case 'asset_reference':
									$stance = true;
									$insert->{$column} = 0;
									$intance_column = $field->on;
									$instance_component = $field->table;
									
									if($remote_fields[$column]['rules']!=""){
										
										if(is_array($remote_fields[$column]['rules']))
										{
											if(!empty($remote_fields[$column]['rules']["table"])&& !empty($remote_fields[$column]['rules']["column"]) && !empty($remote_fields[$column]['rules']["column2"]) && !empty($remote_fields[$column]['rules']["column1"]) ){
												
												$query_remote = 'SELECT '.$remote_db->quoteName($remote_fields[$column]['rules']["column"]).' FROM '.$remote_db->quoteName($remote_fields[$column]['rules']["table"]).' WHERE '.$remote_db->quoteName($remote_fields[$column]['rules']["column2"]).' = '.$remote_db->quote($row->{$remote_fields[$column]['rules']["column1"]});
												$remote_db->setQuery($query_remote);
												if( $ref_val = $remote_db->loadResult() )
												{
													$insert->rules = json_decode($ref_val, true);
													break;
												}
												else
												{
													$log->op_end = JFactory::getDate('now')->toSql(true);
													$log->status = 'abort';
													$log->message = $remote_db->stderr();
													$flag = $this->setLog($log);
													jexit('{"result":"error","error":"'.$remote_db->stderr().'"}');
												}
											}
										}
										else{
											$insert->rules = json_decode($row->{$remote_fields[$column]['rules']}, true);
										}
									}
								break;
							}
						}
					}
					//check if id already exists
					$where = array();
					if( !empty($primary) ){
						foreach($primary as $keyCol){
							if(isset($insert->{$keyCol})){
							$where[] = $local_db->quoteName($keyCol).'='.$local_db->quote($insert->{$keyCol});
							}
						}
						if(!empty($where)){
							$query_local = 'SELECT * FROM '.$local_db->quoteName($profile_params->table).' WHERE '.implode(' AND ', $where);
							$local_db->setQuery( $query_local );
							$oldData = $local_db->loadAssoc();
							$isNew = empty($oldData) ? true : false;
						}
					}
					$afterState = false;
					
					//insert base table record
					if($profile_params->operation==3){
						//delete data operation
					}
					elseif($profile_params->operation==1){
						if(!$isNew){
							JLog::add(JText::sprintf('RECORD_ALREADY_EXISTS',implode(',',$where)), JLog::ERROR, 'com_vdata');
							continue;
						}
						//capture events before insert
						if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'before') ){
							//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $profile_params->events->before)), JLog::INFO, 'com_vdata');
							$this->profile = new stdClass();
							$this->profile->params = $profile_params;
							$response = $this->captureEventsOnRecord( $insert, 'before', $isNew , array('pk'=>$primary ,'old'=>$oldData));
							if(in_array(false, $response)){
								JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
								continue;
							}
						}
						$base_join_val = null;
						if($stance){
							$component = JText::_($instance_component);
							$component_name = $component;
							if (strpos(strtolower($component), "com_") ===FALSE){
								$component_name = "com_".strtolower($component_name); 
							}
							if (strpos($component, "com_")===0){
								$component = str_replace("com_", '',strtolower($component)); 
							}
							JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
							
							$core_tables = array('category', 'content', 'menu', 'module');
							if(in_array($intance_column, $core_tables)){
								$tbl_instance = JTable::getInstance($intance_column);
							}
							else{
								$tbl_instance = JTable::getInstance($intance_column, $component.'Table');
							}
							
							if(property_exists($tbl_instance, 'parent_id')){
								$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
								@$tbl_instance->setLocation($parent_id, 'last-child');
							}
							
							if (!$tbl_instance->bind((array)$insert)) {
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $tbl_instance->getError();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
							}
							if (!$tbl_instance->check()) {
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $tbl_instance->getError();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
							}
							if (!$tbl_instance->store()){
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $tbl_instance->getError();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
							}
							if(!empty($primary)){
								$base_in_id = array();
								foreach($primary as $pkey){
									if(property_exists($tbl_instance, $pkey)){
										$base_in_id[$pkey] = $tbl_instance->{$pkey};
									}
								}
							}
							$n++;
							$afterState = true;
							if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($tbl_instance, $profile_params->joins->column1[0])){
								$base_join_val = $tbl_instance->{$profile_params->joins->column1[0]};
							}
						}
						else{
							if(isset($profile_params->component->table) && !empty($profile_params->component->table)){
								$component = JText::_($profile_params->component->value);
								$component_name = $component;
								if (strpos(strtolower($component), "com_") ===FALSE){
									$component_name = "com_".strtolower($component_name); 
								}
								if (strpos(strtolower($component), "com_") ===0){
									$component = str_replace("com_", '',strtolower($component)); 
								}
								JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
								$core_tables = array('category', 'content', 'menu', 'module');
								if(in_array($profile_params->component->table, $core_tables)){
									$tbl_instance = JTable::getInstance($profile_params->component->table);
								}
								else{
									$tbl_instance = JTable::getInstance($profile_params->component->table, $component.'Table');
								}
								
								$tbl_instance->load(null);
								if(property_exists($tbl_instance, 'parent_id')){
									$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
									@$tbl_instance->setLocation($parent_id, 'last-child');
								}
								
								if (!$tbl_instance->bind((array)$insert)) {
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $tbl_instance->getError();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
								}
								
								if (!$tbl_instance->check()) {
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $tbl_instance->getError();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
								}
								
								if (!$tbl_instance->store()){
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $tbl_instance->getError();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
								}
								$n++;
								$afterState = true;
								if(!empty($primary)){
									$base_in_id = array();
									foreach($primary as $pkey){
										if(property_exists($tbl_instance, $pkey)){
											$base_in_id[$pkey] = $tbl_instance->{$pkey};
										}
									}
								}
								if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($tbl_instance, $profile_params->joins->column1[0])){
									$base_join_val = $tbl_instance->{$profile_params->joins->column1[0]};
								}
							}
							else{
								if($local_db->insertObject($profile_params->table, $insert)){
									if($this->getAffected($local_db)> 0){
										$n++;
										$afterState = true;
									}
									$base_in_id = array();
									foreach($primary as $pkey){
										if(isset($insert->{$pkey})){
											$base_in_id[$pkey] = $insert->{$pkey};
										}
									}
											
									if((!empty($primary) && count($primary)==1) || (isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && !property_exists($insert, $profile_params->joins->column1[0]))){
										$id =$local_db->insertid();
										if($primary[0]!='id'){
											$query = 'SHOW KEYS FROM '.$local_db->quoteName($profile_params->table).' WHERE Key_name = "PRIMARY"';
											$local_db->setQuery( $query );
											// $key = $local_db->loadObjectList();
											$key = $local_db->loadAssocList('Column_name');
											$pri = array_keys($key)[0];
											$cached_data->{$pri} = $insert->{$pri} = $id;
										}else{
											$cached_data->{$primary[0]} = $insert->{$primary[0]} = $local_db->insertid();
										}
									}
									if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($insert, $profile_params->joins->column1[0])){
										$base_join_val = $insert->{$profile_params->joins->column1[0]};
									}
									
								}
								else{
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = $local_db->stderr();
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.$local_db->stderr().'"}');
								}
							}
						}
						if($afterState){
							//capture events after insert
							if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'after') ){
								if(!empty($primary)){
									foreach($primary as $pkey){
										if(property_exists($cached_data, $pkey) && isset($base_in_id[$pkey])){
											$cached_data->{$pkey} = $base_in_id[$pkey];
										}
									}
								}
								$this->profile = new stdClass();
								$this->profile->params = $profile_params;
								$response = $this->captureEventsOnRecord( $cached_data, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
							}
						}
					}
					else{
						if(empty($primary)){
							$log->op_end = JFactory::getDate('now')->toSql(true);
							$log->status = 'abort';
							$log->message = JText::_('PRIMARY_KEY_NOT_FOUND');
							$flag = $this->setLog($log);
							jexit('{"result":"error","error":"'.JText::_('PRIMARY_KEY_NOT_FOUND').'"}');
						}
						else{
							foreach($primary as $pkey){
								if(!isset($insert->{$pkey}) || empty($insert->{$pkey})){
									$log->op_end = JFactory::getDate('now')->toSql(true);
									$log->status = 'abort';
									$log->message = JText::_('PRIMARY_KEY_NOT_FOUND');
									$flag = $this->setLog($log);
									jexit('{"result":"error","error":"'.JText::_('PRIMARY_KEY_NOT_FOUND').'"}');
								}
							}
						}
						
						//capture events before update
						if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'before') ){
							//JLog::add(JText::sprintf('TRIGGERING_EVENTS_BEFORE_UPDATE', implode(',', $profile_params->events->before)), JLog::INFO, 'com_vdata');
							$this->profile = new stdClass();
							$this->profile->params = $profile_params;
							$response = $this->captureEventsOnRecord( $insert, 'before',$isNew , array('pk'=>$primary ,'old'=>$oldData));
							if(in_array(false, $response)){
								JLog::add(JText::_('PLUGIN_EVENT_ERROR_SKIP_RECORD'), JLog::ERROR, 'com_vdata');
								continue;
							}
						}
						$base_join_val = null;
						if($stance){
							$component = JText::_($instance_component);
							$component_name = $component;
							if (strpos(strtolower($component), "com_") ===FALSE){
								$component_name = "com_".strtolower($component_name); 
							}
							if (strpos(strtolower($component), "com_") ===0){
								$component = str_replace("com_", '',strtolower($component)); 
							}
							JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
							$core_tables = array('category', 'content', 'menu', 'module');
							if(in_array($intance_column, $core_tables)){
								$tbl_instance = JTable::getInstance($intance_column);
							}
							else{
								$tbl_instance = JTable::getInstance($intance_column, $component.'Table');
							}
							
							
							$loadFields = array();
							foreach($primary as $pkey){
								if(isset($insert->{$pkey}) && !empty($insert->{$pkey})){
									$loadFields[$pkey] = $insert->{$pkey};
								}
							}
							$tbl_instance->load($loadFields);
							if(property_exists($tbl_instance, 'parent_id')){
								$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
								if (!$tbl_instance->isLeaf())
									@$tbl_instance->setLocation($parent_id, 'last-child');
							}
							if($isNew){
								JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
							}
							
							if (!$tbl_instance->bind((array)$insert)) {
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $tbl_instance->getError();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
							}
							if (!$tbl_instance->check()) {
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $tbl_instance->getError();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
							}
							if (!$tbl_instance->store()){
								$log->op_end = JFactory::getDate('now')->toSql(true);
								$log->status = 'abort';
								$log->message = $tbl_instance->getError();
								$flag = $this->setLog($log);
								jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
							}
							$n++;
							$afterState = true;
							if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($tbl_instance, $profile_params->joins->column1[0])){
								$base_join_val = $tbl_instance->{$profile_params->joins->column1[0]};
							}
						}
						else{
							if($isNew){
								JLog::add(JText::sprintf('RECORD_NOT_EXISTS', implode(',',$where)), JLog::ERROR, 'com_vdata');
								if(isset($profile_params->component->table) && !empty($profile_params->component->table)){
									$component = JText::_($profile_params->component->value);
									$component_name = $component;
									if (strpos(strtolower($component), "com_") ===FALSE){
										$component_name = "com_".strtolower($component_name); 
									}
									if (strpos(strtolower($component), "com_") ===0){
										$component = str_replace("com_", '',strtolower($component)); 
									}
									JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
									$core_tables = array('category', 'content', 'menu', 'module');
									if(in_array($profile_params->component->table, $core_tables)){
										$tbl_instance = JTable::getInstance($profile_params->component->table);
									}
									else{
										$tbl_instance = JTable::getInstance($profile_params->component->table, $component.'Table');
									}
									if(property_exists($tbl_instance, 'parent_id')){
										$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
										@$tbl_instance->setLocation($parent_id, 'last-child');
									}
									if (!$tbl_instance->bind((array)$insert)) {
										$log->op_end = JFactory::getDate('now')->toSql(true);
										$log->status = 'abort';
										$log->message = $tbl_instance->getError();
										$flag = $this->setLog($log);
										jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
									}
									if (!$tbl_instance->check()) {
										$log->op_end = JFactory::getDate('now')->toSql(true);
										$log->status = 'abort';
										$log->message = $tbl_instance->getError();
										$flag = $this->setLog($log);
										jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
									}
									if (!$tbl_instance->store()){
										$log->op_end = JFactory::getDate('now')->toSql(true);
										$log->status = 'abort';
										$log->message = $tbl_instance->getError();
										$flag = $this->setLog($log);
										jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
									}
									$n++;
									$afterState = true;
									if(!empty($primary)){
										$base_in_id = array();
										foreach($primary as $pkey){
											if(property_exists($row, $pkey)){
												$base_in_id[$pkey] = $row->{$pkey};
											}
										}
									}
									if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($tbl_instance, $profile_params->joins->column1[0])){
										$base_join_val = $tbl_instance->{$profile_params->joins->column1[0]};
									}
								}
								else{
									if($local_db->insertObject($profile_params->table, $insert)){
										if($this->getAffected($local_db)> 0){
											$n++;
											$afterState = true;
										}
										$base_in_id = array();
										foreach($primary as $pkey){
											if(isset($insert->{$pkey})){
												$base_in_id[$pkey] = $insert->{$pkey};
											}
										}
												
										if((!empty($primary) && count($primary)==1) || (isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && !property_exists($insert, $profile_params->joins->column1[0]))){
											$id =$local_db->insertid();
											if($primary[0]!='id'){
												$query = 'SHOW KEYS FROM '.$local_db->quoteName($profile_params->table).' WHERE Key_name = "PRIMARY"';
												$local_db->setQuery( $query );
												// $key = $local_db->loadObjectList();
												$key = $local_db->loadAssocList('Column_name');
												$pri = array_keys($key)[0];
												$cached_data->{$pri} = $insert->{$pri} = $id;
											}else{
												$cached_data->{$primary[0]} = $insert->{$primary[0]} = $local_db->insertid();
											}
										}
										if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($insert, $profile_params->joins->column1[0])){
											$base_join_val = $insert->{$profile_params->joins->column1[0]};
										}
									
									}
									else{
										$log->op_end = JFactory::getDate('now')->toSql(true);
										$log->status = 'abort';
										$log->message = $local_db->stderr();
										$flag = $this->setLog($log);
										jexit('{"result":"error","error":"'.$local_db->stderr().'"}');
									}
								}
							}
							else{
								if(isset($profile_params->component->table) && !empty($profile_params->component->table)){
									$component = JText::_($profile_params->component->value);
									$component_name = $component;
									if (strpos(strtolower($component), "com_") ===FALSE){
										$component_name = "com_".strtolower($component_name); 
									}
									if (strpos(strtolower($component), "com_") ===0){
										$component = str_replace("com_", '',strtolower($component)); 
									}
									JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/'.$component_name.'/tables');
									$core_tables = array('category', 'content', 'menu', 'module');
									if(in_array($profile_params->component->table, $core_tables)){
										$tbl_instance = JTable::getInstance($profile_params->component->table);
									}
									else{
										$tbl_instance = JTable::getInstance($profile_params->component->table, $component.'Table');
									}
									$loadFields = array();
									foreach($primary as $pkey){
										if(isset($insert->{$pkey}) && !empty($insert->{$pkey})){
											$loadFields[$pkey] = $insert->{$pkey};
										}
									}
									$tbl_instance->load($loadFields);
									if(property_exists($tbl_instance, 'parent_id')){
										$parent_id = isset( $insert->parent_id)&& !empty( $insert->parent_id)?$insert->parent_id:1;
										if (!$tbl_instance->isLeaf())
											@$tbl_instance->setLocation($parent_id, 'last-child');
									}
									if (!$tbl_instance->bind((array)$insert)) {
										$log->op_end = JFactory::getDate('now')->toSql(true);
										$log->status = 'abort';
										$log->message = $tbl_instance->getError();
										$flag = $this->setLog($log);
										jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
									}
									if (!$tbl_instance->check()) {
										$log->op_end = JFactory::getDate('now')->toSql(true);
										$log->status = 'abort';
										$log->message = $tbl_instance->getError();
										$flag = $this->setLog($log);
										jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
									}
									if (!$tbl_instance->store()){
										$log->op_end = JFactory::getDate('now')->toSql(true);
										$log->status = 'abort';
										$log->message = $tbl_instance->getError();
										$flag = $this->setLog($log);
										jexit('{"result":"error","error":"'.$tbl_instance->getError().'"}');
									}
									$n++;
									$afterState = true;
									if(!empty($primary)){
										$base_in_id = array();
										foreach($primary as $pkey){
											if(property_exists($row, $pkey)){
												$base_in_id[$pkey] = $row->{$pkey};
											}
										}
									}
									if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 && property_exists($tbl_instance, $profile_params->joins->column1[0])){
										$base_join_val = $tbl_instance->{$profile_params->joins->column1[0]};
									}
								}
								else{
									if($local_db->updateObject($profile_params->table, $insert, $primary)){
										if($this->getAffected($local_db)> 0){
											$n++;
											$afterState = true;
										}
										// $base_in_id = $insert->{$primary};
										$base_in_id = array();
										foreach($primary as $pkey){
											if(isset($insert->{$pkey})){
												$base_in_id[$pkey] = $insert->{$pkey};
											}
										}
										if(isset($profile_params->joins->column1) && count($profile_params->joins->column1)>0 ){
											if(property_exists($insert, $profile_params->joins->column1[0])){
												$base_join_val = $insert->{$profile_params->joins->column1[0]};
											}else{
												//check if record already exists
												$where = array();
												if( !empty($primary)  ){
													foreach($primary as $keyCol){
														if(isset($insert->{$keyCol})){
														$where[] = $local_db->quoteName($keyCol).'='.$local_db->quote($insert->{$keyCol});
														}
													}
													if(!empty($where)){
														$query = 'SELECT '.$profile_params->joins->column1[0].' FROM '.$local_db->quoteName($profile_params->table).' WHERE '.implode(' AND ', $where);
														$local_db->setQuery( $query );
														$base_join_val = $cached_data->{$profile_params->joins->column1[0]} = $insert->{$profile_params->joins->column1[0]} = $local_db->loadResult();
													}
												}
											}
										}
									}
									else{
										$log->op_end = JFactory::getDate('now')->toSql(true);
										$log->status = 'abort';
										$log->message = $local_db->stderr();
										$flag = $this->setLog($log);
										jexit('{"result":"error","error":"'.$local_db->stderr().'"}');
									}
								}
							}
						}
						$dids[] = $base_in_id;
						
						if($afterState){
							//capture events after update
							if( property_exists($profile_params, 'events') && property_exists($profile_params->events, 'after') ){
								if(!empty($primary)){
									foreach($primary as $pkey){
										if(property_exists($cached_data, $pkey) && isset($base_in_id[$pkey])){
											$cached_data->{$pkey} = $base_in_id[$pkey];
										}
									}
								}
								$this->profile = new stdClass();
								$this->profile->params = $profile_params;
								$response = $this->captureEventsOnRecord( $cached_data, 'after',$isNew , array('pk'=>$primary ,'success'=>true));
							}
						}
					}
					if($profile_params->operation!=3 && isset($profile_params->joins)){
						$join_dids[] = $base_join_val;
						$flag = $this->insertRemoteJoinRecords($base_join_val, $profile_params->joins, $row, $remote_fields, $remote_db, $profile_params->operation);
						if($flag->result=='error'){
							$log->op_end = JFactory::getDate('now')->toSql(true);
							$log->status = 'abort';
							$log->message = $flag->error;
							$flag = $this->setLog($log);
							jexit('{"result":"error","error":"'.$flag->error.'"}');
						}
					}
				}
			}
			
			JLog::add(JText::sprintf('IMPORT_RECORD_COUNT', $n), JLog::INFO, 'com_vdata');
			if( ($profile_params->operation==2) || ($profile_params->operation==3) ){
				$deleteQry = 'DELETE FROM '.$local_db->quoteName($profile_params->table). ' WHERE %s';
				$whereCon = array();
				foreach($dids as $did){
					if(is_array($did)){
						$where = array();
						foreach($did as $pkey=>$pval){
							$where[] = $local_db->quoteName($pkey).' <> '.$local_db->quote($pval);
						}
						$whereCon[] = ' ('.implode(' AND ', $where).')';
					}
					else{
						$whereCon[] = ' '.$local_db->quoteName($primary).' <> '.$local_db->quote($did);
					}
				}
				//apply delete filter for delete operation
				if($profile_params->operation==3){
					//apply filters
					if(isset($profile_params->filters->column)){
						$filterValues = $this->importDeleteFilter($local_db, $profile_params);
						if(!empty($filterValues)){
							$whereCon[] = $filterValues;
						}
					}
				}
				if(!empty($whereCon)){
					$local_db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
					$res = $local_db->execute();
				}
				
				if(isset($profile_params->joins)){
					$whereCon = array();
					$table2 = $profile_params->joins->table2;
					for($i=0;$i<count($table2);$i++){
						$deleteQry = 'DELETE FROM '.$local_db->quoteName($table2[$i]). ' WHERE %s';
						foreach($join_dids as $jdid){
							 if(is_array($jdid)){
								$where = array();
								foreach($jdid as $pkey=>$pval){
									$where[] = $local_db->quoteName($pkey).' <> '.$local_db->quote($pval);
								}
								$whereCon[] = ' ('.implode(' AND ', $where).')';
							}
							else{ 
								$whereCon[] = $local_db->quoteName($profile_params->joins->column2[$i]).' <> '.$local_db->quote($jdid);
							}
						}
						if(!empty($whereCon)){
							$local_db->setQuery(sprintf($deleteQry, implode(' AND ', $whereCon)));
							$res = $local_db->execute();
						}
					}
				}
			}
			//send notification email
			$notifyProfile = ($schedule->profileid!=0)?$profile->title:"";
			$notifyQry = ($schedule->profileid==0)?$schedule->qry:"";
			$notify = $this->sendNotificationEmail( $schedule->iotype,1 , array('schedule_id'=>$schedule->id,'schedule_title'=>$schedule->title,"schedule_qry"=>$notifyQry,'profile_id'=>$schedule->profileid,'profile_title'=>$notifyProfile,"count"=>$n, "format"=>"db") );
			
			$log->op_end =  JFactory::getDate(microtime(true))->toSql(true);
			$log->status = 'success';
			$log->message = JText::sprintf('LOG_IMPORT_SUCCESS_MSG', $n);
			$log->iocount = $n;
			$flag = $this->setLog($log);
			jexit('{"result":"success","message":"'.JText::sprintf('LOG_IMPORT_SUCCESS_MSG', $n).'"}');
		}
		
		function setExport()
		{
			$input = JFactory::getApplication()->input;
			$schedule_uid = $input->get('type', '', 'RAW');
			
			$config = $this->getConfig();
			$csv_child = json_decode($config->csv_child);
			
			$db = JFactory::getDbo();
			$query = 'select * from #__vd_schedules where state=1 and uid='.$db->quote($schedule_uid);
			$db->setQuery($query);
			$schedule = $db->loadObject();
			$params = json_decode($schedule->params);
			
			switch($params->source){
				case 'csv':
					if($csv_child->csv_child==2)
						$flag = $this->autoExportCsv($schedule);
					else
						$flag = $this->autoExportCsv1($schedule);
				break;
				case 'xml':
					$flag = $this->autoExportXml($schedule);
				break;
				case 'json':
					$flag = $this->autoExportJson($schedule);
				break;
				case 'remote':
					$flag = $this->autoExportRemote($schedule);
				break;
				default:
			}
		}
		
		function autoExportCsv($schedule)
		{
			$log = new stdClass();
			$log->iotype = 1;
			$log->profileid = $schedule->profileid;
			$log->cronid = $schedule->id;
			$log->side = 'cron';
			$log->user = JFactory::getUser()->id;
			$op_start = JFactory::getDate('now')->format('m-d-Y_hia');
			$log_file = 'com_vdata_plg_custom'.'_'.$op_start.'.txt';
			
			$config = $this->getConfig();
			if($config->logging){
				$logger = $this->initializeLogger();
			}
			$headingStatus = $config->csv_header;
			
			$log->logfile = $log_file;
			$log->op_start =  JFactory::getDate(microtime(true))->toSql(true);
			
			//joomla database object
			$db = JFactory::getDbo();
			//database object from com_vdata configuration
			$dbc = $this->getDbc();
			$input 		= JFactory::getApplication()->input;
			
			// fetch value filter values
			$columns 		= $input->get('fields', '', 'RAW');
			
			$delimiter = $this->getDelimiter();
			$enclosure = $this->getEnclosure();
			
			$params = json_decode($schedule->params);
			$schedule_cols = json_decode($schedule->columns);
			$expo_fields = json_decode(json_encode($schedule_cols->fields), true);
			
			$path = $params->path;
			if($params->server == 'local'){
				$path = JPATH_ROOT.'/'.$params->path;
				$base = basename($path);
				$dir = dirname($path);
				if(!empty($base)){
					$file_ext = explode('.', $base);
					if( (count($file_ext)>1) ){
						if( !empty($file_ext[count($file_ext)-1]) && strrchr($path, '.') ){
							$dir = dirname($path);
							if (!file_exists($dir)) {
								mkdir($dir, 0777, true);
							}
						}
					}
				}
			}
			else{
				//
				$filename = (isset($params->ftp->ftp_file) && !empty($params->ftp->ftp_file))?$params->ftp->ftp_file:JFilterOutput::stringURLSafe($schedule->title).'.csv';
				$path = JPATH_ROOT.'/components/com_vdata/uploads/'.$filename;
			}
			
			if( ($schedule->type==1) ){
				try {
					$fp = @fopen($path, "a");
					if ( !$fp ) {
						$open_error = error_get_last();
						jexit('{"result":"error", "error":"'.$open_error['message'].'"}');
					}
				} catch(Exception $e){
					jexit('{"result":"error", "error":"'.$e->getMessage().'"}');
				}
			}
			$log->iofile = $path;
			
			//value filter to fetch requested column values separated by vertical bar
			$cols = array();
			if(!empty($columns)){
				$cols = explode('|', $columns);
			}
			$rows = $this->getExpoData($schedule, 'array');
			//export record counter
			$n=0;
			
			$heading = array();
			//if no profile selected i.e. manual query
			if($schedule->profileid==0){
				//fetch table name from export query
				$table_name_from_query = strpos(strtolower($schedule->qry), 'from ');
				$table_name_from_query = substr(strtolower($schedule->qry), $table_name_from_query+5);
				$table_name = strpos($table_name_from_query, ' ')!=''?substr(strtolower($table_name_from_query), 0, strpos($table_name_from_query, ' ')):$table_name_from_query;
				
				//retrieve primary key for id(primary key) filter values
				$query = 'SHOW KEYS FROM '.$dbc->quoteName($table_name).' WHERE Key_name = "PRIMARY"';
				$dbc->setQuery($query);
				$pkey = $dbc->loadObjectList();
				
				if($schedule->type!=2){
					//log info
					JLog::add(JText::sprintf('EXPORT_QUERY', $schedule->qry), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_TABLE', $table_name), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_FORMAT', 'CSV'), JLog::INFO, 'com_vdata');
				}
				//write heading
				if(!empty($rows)){
					foreach($rows[0] as $col=>$val){
						if(!empty($cols)){//if value filter applied,output selected columns
							if(array_key_exists($col, $expo_fields) && in_array($expo_fields[$col], $cols))
								array_push($heading, $expo_fields[$col]);
						}
						else
							array_push($heading, $expo_fields[$col]);
					}
					if($schedule->type==2 && $schedule->iotype==1)
						$output = @fopen("php://memory", $params->mode);
					else
						$output = @fopen($path, $params->mode);
					
				}
				//write data
				foreach($rows as $h=>$row){
					
					//heading only in case of write mode
					if( ($h==0) && ($params->mode=='w') ){
						if($headingStatus){
							fputcsv($output, $heading, $delimiter, $enclosure);
						}
					}

					//apply value(column) filter
					if(!empty($cols)){
						$vals = array();
						foreach($row as $col=>$val){
							if(array_key_exists($col, $expo_fields) && in_array($expo_fields[$col], $cols))
								array_push($vals, $val);
						}
						if(fputcsv($output, $vals, $delimiter, $enclosure))
							$n++;
					}
					else{
						if(fputcsv($output, $row, $delimiter, $enclosure))
							$n++;
					}
					//insert log in database and file
				}
			}
			else {
				//fetch profile details
				$query = 'select * from #__vd_profiles where id='.$schedule->profileid;
				$db->setQuery($query);
				$profile = $db->loadObject();
				$profile_params = json_decode($profile->params);
				
				$log->table = $profile_params->table;
				
				if($schedule->type!=2){
					//log info
					JLog::add(JText::sprintf('EXPORT_PROFILE', $profile->title), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_TABLE', $profile_params->table), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_FORMAT', 'CSV'), JLog::INFO, 'com_vdata');
				}
				
				
				//retrieve primary key for id(primary key) filter values
				$query = 'SHOW KEYS FROM '.$dbc->quoteName($profile_params->table).' WHERE Key_name = "PRIMARY"';
				$dbc->setQuery($query);
				$pkey = $dbc->loadObjectList();//Column_name

				if( property_exists($schedule_cols, 'quick') ) { // if it is quick import/export profile
					//write heading
					if(!empty($rows)){
						foreach($rows[0] as $col=>$val){
							
							if($schedule_cols->quick==1){
								if(!empty($cols)){//if value filter applied,output selected columns
									if(in_array($col, $cols))
										array_push($heading, $col);
								}
								else
									array_push($heading, $col);
							}
							else{
								if(!empty($cols)){//if value filter applied,output selected columns
									if(in_array($expo_fields[$col], $cols))
										array_push($heading, $expo_fields[$col]);
								}
								else
									array_push($heading, $expo_fields[$col]);
							}
							
						}
						if($schedule->type==2 && $schedule->iotype==1)
							$output = @fopen("php://memory", $params->mode);
						else
							$output = @fopen($path, $params->mode);
						
					}
					//write data
					foreach($rows as $h=>$row){
						
						//heading only in case of write mode
						if( ($h==0) && ($params->mode=='w') ){
							if($headingStatus){
								fputcsv($output, $heading, $delimiter, $enclosure);
							}
						}

						//apply value(column) filter
						if(!empty($cols)){
							$vals = array();
							foreach($row as $col=>$val){
								if($schedule_cols->quick==1){
									if(in_array($col, $cols))
										array_push($vals, $val);
								}
								else{
									if(in_array($expo_fields[$col], $cols))
										array_push($vals, $val);
								}
							}
							if(fputcsv($output, $vals, $delimiter, $enclosure))
								$n++;
						}
						else{
							if(fputcsv($output, $row, $delimiter, $enclosure))
								$n++;
						}
						//insert log in database and file
					}
				}
				else {
					if(!empty($rows)){
						if($schedule->type==2 && $schedule->iotype==1)
							$output = @fopen("php://memory", $params->mode);
						else
							$output = @fopen($path, $params->mode);
					}
					foreach($rows as $ct=>$row){
						$write = array();
						foreach($profile_params->fields as $column=>$field){
							//apply value filter
							if(!empty($cols)){
								if(!array_key_exists($column, $expo_fields) && !in_array($expo_fields[$column], $cols))
									continue;
							}
							switch($field->data){
								case 'include' : 
									$heading[] = $expo_fields[$column];
									$write[] = $row[$column];
								break;
								case 'defined' :
									$heading[] = $expo_fields[$column];
									$defined = $field->default;
									$defined = $this->getDefinedValue($field->default, $profile_params->fields, $row, $expo_fields);
									$write[] = $defined;
								break;
								case 'reference' :
									$query = "SELECT ".$dbc->quoteName($field->table.''.$ct).".".implode(','.$dbc->quoteName($field->table.''.$ct).".", $field->reftext)." FROM ".$dbc->quoteName($field->table)." AS ".$dbc->quoteName($field->table.''.$ct)." JOIN ".$dbc->quoteName($profile_params->table)." ON ".$dbc->quoteName($field->table.''.$ct).".".$dbc->quoteName($field->on)." = ".$dbc->quoteName($profile_params->table).".".$column." WHERE ".$dbc->quoteName($profile_params->table).".".$column." = ".$dbc->quote($row[$column]);
									$dbc->setQuery($query);
									$ref_data = $dbc->loadAssoc();
									$heading = array_merge($heading, array_values($expo_fields[$column]));
									if($ref_data)
										$write = array_merge($write, array_values($ref_data));
									else
										$write = array_merge($write, array_fill(0, count($expo_fields[$column]), ''));
								break;
							}
							
							
						}
						//write heading
						if( ($ct==0) && ($params->mode=='w') ){
							if($headingStatus){
								fputcsv($output, $heading, $delimiter, $enclosure);
							}
						}

						//write data
						if(fputcsv($output, $write, $delimiter, $enclosure))
							$n++;
						
						//if value filter applied skip child record
						if(!empty($cols)){
							continue;
						}

						//write child table data
						if(!empty($profile_params->joins->table2)){
							$child_count = count($profile_params->joins->table2);
							
							$exData = array();
							$exData[$profile_params->table][] = $row;
							
							foreach($profile_params->joins->table2 as $c=>$child){
								
								//#
								$blank = array_fill(0, count($heading), NUll);
								if(array_key_exists($child, $expo_fields)){

									$response_data = $this->getExportChildData($dbc, $c, $profile_params, $exData,'ar');
									if($response_data->result=='error'){
										jexit('{"result":"error", "error":"'.$response_data->error.'"}');
									}
									$child_data = $response_data->data;
									
									if(!empty($child_data)){
										foreach($child_data as $cd){
											$exData[$child][] = $cd;
										}
									}
									
									if(!empty($child_data)){
										//write blank line
										for($ch=0;$ch<=$c; $ch++){
											fputcsv($output, $blank, $delimiter, $enclosure);
										}
									}
									
									//write data
									foreach($child_data as $cd=>$cdata){
										$child_heading = array();
										$child_row = array();
										foreach($profile_params->joins->columns[$c] as $column=>$field){
											
											switch($field->data){
												case 'include' :
													$child_heading[] = $expo_fields[$child][$column];
													$child_row[] = $cdata[$column];
												break;
												case 'defined' :
													$child_heading[] = $expo_fields[$child][$column];
													$defined = $field->default;
													$defined = $this->getDefinedValue($field->default, $profile_params->joins->columns[$c], $cdata, $expo_fields[$child]);
													$child_row[] = $defined;
												break;
												case 'reference' :
													$child_heading = array_merge($child_heading, array_values($expo_fields[$child][$column]));
													foreach($child_data as $crd=>$cdata){
														$query = "SELECT ".$dbc->quoteName($field->table.''.$c).".".implode(','.$dbc->quoteName($field->table.''.$c).".", $field->reftext)." FROM ".$dbc->quoteName($field->table)." AS ".$dbc->quoteName($field->table.''.$c)." JOIN ".$dbc->quoteName($child)." ON ".$dbc->quoteName($field->table.''.$c).".".$dbc->quoteName($field->on)." = ".$dbc->quoteName($child).".".$column." WHERE ".$dbc->quoteName($child).".".$column." = ".$dbc->quote($cdata[$column]);
														$dbc->setQuery($query);
														$ref_data = $dbc->loadAssoc();
														
														if($ref_data){
															$child_row = array_merge($child_row, array_values($ref_data));
														}
														else{
															$child_row = array_merge($child_row, array_fill(0, count($expo_fields[$child][$column]), ''));
														}
													}
												break;
											}
											
										}
										if($cd==0){
											fputcsv($output, $child_heading, $delimiter, $enclosure);
										}
										fputcsv($output, $child_row, $delimiter, $enclosure);
									}
									
									//write blank line if it's last child record to indicate next base record
									if($child_count == ($c+1)){
										fputcsv($output, $blank, $delimiter, $enclosure);
									}
									
								}
								//#
							}
							
						}
						//insert log in database and file
					}
				}
			}
			if($schedule->type==2){
				rewind($output);
				// header('Content-Type: text/csv');
				while($line=fgets($output,65535)) {
				   echo $line;
				}
				jexit();
				// jexit(stream_get_contents($output));
			}
			
			//email export file
			if( ($n>0) && isset($params->sendfile->status) && ($params->sendfile->status==1) && isset($params->sendfile->emails) && !empty($params->sendfile->emails) ){
				$emails = explode(',', $params->sendfile->emails);
				$cc = explode(',', $params->sendfile->cc);
				$bcc = explode(',', $params->sendfile->bcc);
				$subject = $params->sendfile->subject;
				$body = $params->sendfile->tmpl;
				$attachment = $path;
				$sendfile = $this->sendFile($emails, $cc, $bcc, $subject, $body, $attachment);
			}
			
			//write file to remote server using ftp
			if( ($n>0) && ($params->server=='remote')){
				if(!$this->uploadToFtp((array)$params->ftp, $path)){
					jexit('{"result":"success", "message":"'.JText::_('VDATA_FTP_FILE_WRITE_ERROR').'"}');
				}
				jimport('joomla.filesystem.file');
				if(!JFile::delete($path)){
					JLog::add(JText::sprintf('VDATA_UNABLE_TO_DELETE_LOCAL_COPY', $path), JLog::INFO, 'com_vdata');
				}
			}
			
			JLog::add(JText::sprintf('EXPORT_RECORD_COUNT', $n), JLog::INFO, 'com_vdata');
			$log->op_end = JFactory::getDate(microtime(true))->toSql(true);
			$log->status = 'success';
			$log->message = JText::sprintf('LOG_EXPORT_SUCCESS_MSG', $n);
			$log->iocount = $n;
			$flag = $this->setLog($log);
			
			//send notification email
			$notifyProfile = ($schedule->profileid!=0)?$profile->title:"";
			$notifyQry = ($schedule->profileid==0)?$schedule->qry:"";
			$notify = $this->sendNotificationEmail( $schedule->iotype,1 , array('schedule_id'=>$schedule->id,'schedule_title'=>$schedule->title,"schedule_qry"=>$notifyQry,'profile_id'=>$schedule->profileid,'profile_title'=>$notifyProfile,"count"=>$n, "format"=>"csv") );
			
			jexit('{"result":"success", "message":"'.JText::sprintf('LOG_EXPORT_SUCCESS_MSG', $n).'"}');
		}
		
		function autoExportCsv1($schedule)
		{
			$log = new stdClass();
			$log->iotype = 1;
			$log->profileid = $schedule->profileid;
			$log->cronid = $schedule->id;
			$log->side = 'cron';
			$log->user = JFactory::getUser()->id;
			$op_start = JFactory::getDate('now')->format('m-d-Y_hia');
			$log_file = 'com_vdata_plg_custom'.'_'.$op_start.'.txt';
			
			$config = $this->getConfig();
			if($config->logging){
				$logger = $this->initializeLogger();
			}
			$headingStatus = $config->csv_header;
			
			$log->logfile = $log_file;
			$log->op_start =  JFactory::getDate(microtime(true))->toSql(true);
			
			//joomla database object
			$db = JFactory::getDbo();
			//database object from com_vdata configuration
			$dbc = $this->getDbc();
			$input 		= JFactory::getApplication()->input;
			
			// fetch value filter values
			$columns 		= $input->get('fields', '', 'RAW');
			
			$delimiter = $this->getDelimiter();
			$enclosure = $this->getEnclosure();
			
			$params = json_decode($schedule->params);
			$schedule_cols = json_decode($schedule->columns);
			$expo_fields = json_decode(json_encode($schedule_cols->fields), true);
			
			$path = $params->path;		
			if($params->server == 'local'){
				$path = JPATH_ROOT.'/'.$params->path;
				$base = basename($path);
				$dir = dirname($path);
				if(!empty($base)){
					$file_ext = explode('.', $base);
					if( (count($file_ext)>1) ){
						if( !empty($file_ext[count($file_ext)-1]) && strrchr($path, '.') ){
							$dir = dirname($path);
							if (!file_exists($dir)) {
								mkdir($dir, 0777, true);
							}
						}
					}
				}
			}
			else{
				//write remote file->upload after write complete->delete
				$filename = (isset($params->ftp->ftp_file) && !empty($params->ftp->ftp_file))?$params->ftp->ftp_file:JFilterOutput::stringURLSafe($schedule->title).'.csv';
				$path = JPATH_ROOT.'/components/com_vdata/uploads/'.$filename;
			}
			
			if( ($schedule->type==1) ){
				try {
					$fp = @fopen($path, "a");
					if ( !$fp ) {
						$open_error = error_get_last();
						jexit('{"result":"error", "error":"'.$open_error['message'].'"}');
					}
				} catch(Exception $e){
					jexit('{"result":"error", "error":"'.$e->getMessage().'"}');
				}
			}
			
			$log->iofile = $path;
			
			//value filter to fetch requested column values separated by vertical bar
			$cols = array();
			if(!empty($columns)){
				$cols = explode('|', $columns);
			}
			
			$rows = $this->getExpoData($schedule, 'array');
			//export record counter
			$n=0;
			
			$heading = array();
			//if no profile selected i.e. manual query
			if($schedule->profileid==0){
				//fetch table name from export query
				$table_name_from_query = strpos(strtolower($schedule->qry), 'from ');
				$table_name_from_query = substr(strtolower($schedule->qry), $table_name_from_query+5);
				$table_name = strpos($table_name_from_query, ' ')!=''?substr(strtolower($table_name_from_query), 0, strpos($table_name_from_query, ' ')):$table_name_from_query;
				
				//retrieve primary key for id(primary key) filter values
				$query = 'SHOW KEYS FROM '.$dbc->quoteName($table_name).' WHERE Key_name = "PRIMARY"';
				$dbc->setQuery($query);
				$pkey = $dbc->loadObjectList();//Column_name
				
				if($schedule->type!=2){
					//log info
					JLog::add(JText::sprintf('EXPORT_QUERY', $schedule->qry), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_TABLE', $table_name), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_FORMAT', 'CSV'), JLog::INFO, 'com_vdata');

				}
				//write heading
				if(!empty($rows)){
					foreach($rows[0] as $col=>$val){
						if( !empty($cols) ){
							//if value filter applied,output selected columns
							if(array_key_exists($col, $expo_fields) && in_array($expo_fields[$col], $cols))
								array_push($heading, $expo_fields[$col]);
						}
						else
							array_push($heading, $expo_fields[$col]);
					}
					if($schedule->type==2 && $schedule->iotype==1)
						$output = @fopen("php://memory", $params->mode);
					else
						$output = @fopen($path, $params->mode);
				}
				//write data
				foreach($rows as $h=>$row){
					
					//heading only in case of write mode
					if( ($h==0) && ($params->mode=='w') ){
						if($headingStatus){
							fputcsv($output, $heading, $delimiter, $enclosure);
						}
					}

					//apply value(column) filter
					if(!empty($cols)){
						$vals = array();
						foreach($row as $col=>$val){
							if(array_key_exists($col, $expo_fields) && in_array($expo_fields[$col], $cols))
								array_push($vals, $val);
						}
						if(fputcsv($output, $vals, $delimiter, $enclosure))
							$n++;
					}
					else{
						if(fputcsv($output, $row, $delimiter, $enclosure))
							$n++;
					}
					//insert log in database and file
				}
			}
			else{
				//fetch profile details
				$query = 'select * from #__vd_profiles where id='.$schedule->profileid;
				$db->setQuery($query);
				$profile = $db->loadObject();
				$profile_params = json_decode($profile->params);
				$child_delimiter = $this->getChildDelimiter();
				$log->table = $profile_params->table;
				
				if($schedule->type!=2){
					//log info
					JLog::add(JText::sprintf('EXPORT_PROFILE', $profile->title), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_TABLE', $profile_params->table), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_FORMAT', 'CSV'), JLog::INFO, 'com_vdata');
				}
				
				//retrieve primary key for id(primary key) filter values
				$query = 'SHOW KEYS FROM '.$dbc->quoteName($profile_params->table).' WHERE Key_name = "PRIMARY"';
				$dbc->setQuery($query);
				$pkey = $dbc->loadObjectList();//Column_name

				if( property_exists($schedule_cols, 'quick') ) { // if it is quick import/export profile
					//write heading
					if(!empty($rows)){
						foreach($rows[0] as $col=>$val){
							
							if($schedule_cols->quick==1){
								if(!empty($cols)){//if value filter applied,output selected columns
									if(in_array($col, $cols))
										array_push($heading, $col);
								}
								else
									array_push($heading, $col);
							}
							else{
								if(!empty($cols)){//if value filter applied,output selected columns
									if(in_array($expo_fields[$col], $cols))
										array_push($heading, $expo_fields[$col]);
								}
								else
									array_push($heading, $expo_fields[$col]);
							}
						}
						if($schedule->type==2 && $schedule->iotype==1)
							$output = @fopen("php://memory", $params->mode);
						else
							$output = @fopen($path, $params->mode);
					}
					//write data
					foreach($rows as $h=>$row){
						//heading only in case of write mode
						if( ($h==0) && ($params->mode=='w') ){
							if($headingStatus){
								fputcsv($output, $heading, $delimiter, $enclosure);
							}
						}

						//apply value(column) filter
						if(!empty($cols)){
							$vals = array();
							foreach($row as $col=>$val){
								if($schedule_cols->quick==1){
									if(in_array($col, $cols))
										array_push($vals, $val);
								}
								else{
									if(in_array($expo_fields[$col], $cols))
										array_push($vals, $val);
								}
							}
							if(fputcsv($output, $vals, $delimiter, $enclosure))
								$n++;
						}
						else{
							if(fputcsv($output, $row, $delimiter, $enclosure))
								$n++;
						}
						//insert log in database and file
					}
				}
				else{
					if(!empty($rows)){
						if($schedule->type==2 && $schedule->iotype==1)
							$output = @fopen("php://memory", $params->mode);
						else
							$output = @fopen($path, $params->mode);
					}
					
					foreach($rows as $h=>$row){
				
						$write = array();
						$heading = array();
						foreach($profile_params->fields as $column=>$field){
							//apply value filter
							if(!empty($cols)){
								if(!array_key_exists($column, $expo_fields) && !in_array($expo_fields[$column], $cols))
									continue;
							}
							switch($field->data){
								case 'include' : 
									$heading[] = $expo_fields[$column];
									$write[] = $row[$column];
								break;
								case 'defined' :
									$heading[] = $expo_fields[$column];
									$defined = $field->default;
									$defined = $this->getDefinedValue($field->default, $profile_params->fields, $row, $expo_fields);
									$write[] = $defined;
								break;
								case 'reference' :
									$query = "SELECT ".$db->quoteName($field->table.''.$h).".".implode(','.$db->quoteName($field->table.''.$h).".", $field->reftext)." FROM ".$db->quoteName($field->table)." AS ".$db->quoteName($field->table.''.$h)." JOIN ".$db->quoteName($profile_params->table)." ON ".$db->quoteName($field->table.''.$h).".".$db->quoteName($field->on)." = ".$db->quoteName($profile_params->table).".".$column." WHERE ".$db->quoteName($profile_params->table).".".$column." = ".$db->quote($row[$column]);
									$db->setQuery($query);
									$ref_data = $db->loadAssoc();
									$heading = array_merge($heading, array_values((array)$expo_fields[$column]));
									if($ref_data)
										$write = array_merge($write, array_values($ref_data));
									else
										$write = array_merge($write, array_fill(0, count((array)$expo_fields[$column]), ''));
								break;
								
							}
						}

						if(!empty($profile_params->joins->table2) && empty($cols)){
							//if value filter applied skip child record
							
							$exData = array();
							$exData[$profile_params->table][] = $row;
							
							foreach($profile_params->joins->table2 as $c=>$child){
								
								if(array_key_exists($child, $expo_fields)){
									
									$response_data = $this->getExportChildData($db, $c, $profile_params, $exData,'ar');
									if($response_data->result=='error'){
										jexit('{"result":"error", "error":"'.$response_data->error.'"}');
									}
									$child_data = $response_data->data;
									
									if(!empty($child_data)){
										foreach($child_data as $cd){
											$exData[$child][] = $cd;
										}
									}
									
									foreach($profile_params->joins->columns[$c] as $column=>$field){
										$val = array();
										switch($field->data){
											case 'include' :
												$heading[] = $expo_fields[$child][$column];
												foreach($child_data as $cd=>$cdata){
													$val[] = $cdata[$column];
												}
												$value = implode($child_delimiter, $val);
												$write[] = $value;
											break;
											case 'defined' :
												$heading[] = $expo_fields[$child][$column];
												foreach($child_data as $cd=>$cdata){
													$defined = $field->default;
													$defined = $this->getDefinedValue($field->default, $profile_params->joins->columns[$c], $cdata, $expo_fields[$child]);
													$val[] = $defined;
												}
												$value = implode($child_delimiter, $val);
												$write[] = $value;
											break;
											case 'reference' :
												$heading = array_merge($heading, array_values($expo_fields[$child][$column]));
												foreach($child_data as $cd=>$cdata){
													$query = "SELECT ".$db->quoteName($field->table.''.$c).".".implode(','.$db->quoteName($field->table.''.$c).".", $field->reftext)." FROM ".$db->quoteName($field->table)." AS ".$db->quoteName($field->table.''.$c)." JOIN ".$db->quoteName($child)." ON ".$db->quoteName($field->table.''.$c).".".$db->quoteName($field->on)." = ".$db->quoteName($child).".".$column." WHERE ".$db->quoteName($child).".".$column." = ".$db->quote($cdata[$column]);
													$db->setQuery($query);
													$ref_data = $db->loadAssoc();
													
													if($ref_data){
														$val = array_merge($val, array_values($ref_data));
													}
													else{
														$val = array_merge($val, array_fill(0, count($expo_fields[$child][$column]), ''));
													}
												}
												if(empty($child_data) && count($expo_fields[$child][$column])>1){
													$write = array_merge($write, array_fill(0, count($expo_fields[$child][$column]), ''));
												}
												else{
													$value = implode($child_delimiter, $val);
													$write[] = $value;
												}
											break;
										}

									}
									
								}
							}
						}
						
						if( ($h==0) && ($params->mode=='w') ){
							//set heading
							if($headingStatus){
								fputcsv($output, $heading, $delimiter, $enclosure);
							}
							if($schedule->type!=2){
								// log info
								JLog::add(JText::sprintf('EXPORT_PROFILE', $profile->title), JLog::INFO, 'com_vdata');
								JLog::add(JText::sprintf('EXPORT_TABLE', $profile_params->table), JLog::INFO, 'com_vdata');
								JLog::add(JText::sprintf('EXPORT_FORMAT', 'CSV'), JLog::INFO, 'com_vdata');
								JLog::add(JText::sprintf('EXPORT_DELIMITER', $delimiter), JLog::INFO, 'com_vdata');
								JLog::add(JText::sprintf('EXPORT_ENCLOSURE', $enclosure), JLog::INFO, 'com_vdata');
							}
						}
						//write data
						if(fputcsv($output, $write, $delimiter, $enclosure))
							$n++;
					}	
				}
			}
			
			if($schedule->type==2){
				rewind($output);
				// header('Content-Type: text/csv');
				while($line=fgets($output,65535)) {
				   echo $line;
				}
				jexit();
				// jexit(stream_get_contents($output));
			}
			
			//email export file
			if( ($n>0) && isset($params->sendfile->status) && ($params->sendfile->status==1) && isset($params->sendfile->emails) && !empty($params->sendfile->emails) ){
				$emails = explode(',', $params->sendfile->emails);
				$cc = explode(',', $params->sendfile->cc);
				$bcc = explode(',', $params->sendfile->bcc);
				$subject = $params->sendfile->subject;
				$body = $params->sendfile->tmpl;
				$attachment = $path;
				$sendfile = $this->sendFile($emails, $cc, $bcc, $subject, $body, $attachment);
			}
			
			//write file to remote server using ftp
			if( ($n>0) && ($params->server=='remote')){
				if(!$this->uploadToFtp((array)$params->ftp, $path)){
					jexit('{"result":"success", "message":"'.JText::_('VDATA_FTP_FILE_WRITE_ERROR').'"}');
				}
				jimport('joomla.filesystem.file');
				if(!JFile::delete($path)){
					JLog::add(JText::sprintf('VDATA_UNABLE_TO_DELETE_LOCAL_COPY', $path), JLog::INFO, 'com_vdata');
				}
			}
			
			JLog::add(JText::sprintf('EXPORT_RECORD_COUNT', $n), JLog::INFO, 'com_vdata');
			$log->op_end = JFactory::getDate(microtime(true))->toSql(true);
			$log->status = 'success';
			$log->message = JText::sprintf('LOG_EXPORT_SUCCESS_MSG', $n);
			$log->iocount = $n;
			$flag = $this->setLog($log);
			//send notification email
			$notifyProfile = ($schedule->profileid!=0)?$profile->title:"";
			$notifyQry = ($schedule->profileid==0)?$schedule->qry:"";
			$notify = $this->sendNotificationEmail( $schedule->iotype,1 , array('schedule_id'=>$schedule->id,'schedule_title'=>$schedule->title,"schedule_qry"=>$notifyQry,'profile_id'=>$schedule->profileid,'profile_title'=>$notifyProfile,"count"=>$n, "format"=>"csv") );
			
			jexit('{"result":"success", "message":"'.JText::sprintf('LOG_EXPORT_SUCCESS_MSG', $n).'"}');
		}
		
		function autoExportXml($schedule)
		{
			$log = new stdClass();
			$log->iotype = 1;
			$log->profileid = $schedule->profileid;
			$log->cronid = $schedule->id;
			$log->side = 'cron';
			$log->user = JFactory::getUser()->id;
			$op_start = JFactory::getDate('now')->format('m-d-Y_hia');
			$log_file = 'com_vdata_plg_custom'.'_'.$op_start.'.txt';
			
			$config = $this->getConfig();
			if($config->logging){
				$logger = $this->initializeLogger();
			}
			
			$log->logfile = $log_file;
			$log->op_start =  JFactory::getDate(microtime(true))->toSql(true);
			
			//joomla database object
			$db = JFactory::getDbo();
			//database object from com_vdata configuration
			$dbc = $this->getDbc();
			$dbPrefix = $dbc->getPrefix();
			$input 		= JFactory::getApplication()->input;
			// fetch value filter values
			$columns 	= $input->get('fields', '', 'RAW');
			
			$params = json_decode($schedule->params);
			$schedule_cols = json_decode($schedule->columns);
			$expo_fields = json_decode(json_encode($schedule_cols->fields), true);
			
			$xmlConfig = json_decode($config->xml_parent);
			$rootNode = !empty($xmlConfig->root)?ltrim(rtrim($xmlConfig->root, '>'), '<'):'ROWSET';
			$rootData = !empty($xmlConfig->data)?ltrim(rtrim($xmlConfig->data, '>'), '<'):'ROW';
			$rootDataChild = !empty($xmlConfig->child)?ltrim(rtrim($xmlConfig->child, '>'), '<'):'CHILD';
			
			$rootNodePath = explode('/', $rootNode);
			$reverseRootNodePath = array_reverse($rootNodePath);
			$replaceTags = '';
			foreach($reverseRootNodePath as $nodeName){
				$replaceTags .= "</".$nodeName.">";
			}
			
			$path = $params->path;
			if($params->server == 'local'){
				$path = JPATH_ROOT.'/'.$params->path;
				$base = basename($path);
				$dir = dirname($path);
				if(!empty($base)){
					$file_ext = explode('.', $base);
					if( (count($file_ext)>1) ){
						
						if( !empty($file_ext[count($file_ext)-1]) && strrchr($path, '.') ){
							$dir = dirname($path);
							if (!file_exists($dir)) {
								mkdir($dir, 0777, true);
							}
						}
					}
				}
			}
			else{
				//write remote file->upload after write complete->delete
				$filename = (isset($params->ftp->ftp_file) && !empty($params->ftp->ftp_file))?$params->ftp->ftp_file:JFilterOutput::stringURLSafe($schedule->title).'.xml';
				$path = JPATH_ROOT.'/components/com_vdata/uploads/'.$filename;
			}
			
			if( ($schedule->type==1) ){
				try {
					$fp = @fopen($path, "a");
					if ( !$fp ) {
						$open_error = error_get_last();
						jexit('{"result":"error", "error":"'.$open_error['message'].'"}');
					}
				} catch(Exception $e){
					jexit('{"result":"error", "error":"'.$e->getMessage().'"}');
				}
			}
			$log->iofile = $path;
			
			//value filter to fetch requested column values separated by vertical bar
			$cols = array();
			if(!empty($columns)){
				$cols = explode('|', $columns);
			}
			if($schedule->type==2 && $schedule->iotype==1){
				$fp = @fopen("php://memory", 'r+');
			}
			$rows = $this->getExpoData($schedule, 'object');
			
			//export record counter
			$n=0;
			$xmlWriter = new XMLWriter();
			if($schedule->profileid==0){
				//fetch table name from export query
				$table_name_from_query = strpos(strtolower($schedule->qry), 'from ');
				$table_name_from_query = substr(strtolower($schedule->qry), $table_name_from_query+5);
				$table_name = strpos($table_name_from_query, ' ')!=''?substr(strtolower($table_name_from_query), 0, strpos($table_name_from_query, ' ')):$table_name_from_query;
				
				//retrieve primary key for id(primary key) filter values
				$query = 'SHOW KEYS FROM '.$dbc->quoteName($table_name).' WHERE Key_name = "PRIMARY"';
				$dbc->setQuery($query);
				$pkey = $dbc->loadObjectList();
				
				if($schedule->type!=2){
					//log info
					JLog::add(JText::sprintf('EXPORT_QUERY', $schedule->qry), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_TABLE', $table_name), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_FORMAT', 'XML'), JLog::INFO, 'com_vdata');
				}
				
				$xmlWriter->openMemory();
				if($params->mode=='w'){
					$xmlWriter->setIndent(true);
					$xmlWriter->startDocument('1.0','UTF-8');
					// $xmlWriter->startElement('ROWSET');
					foreach($rootNodePath as $pathNode){
						$xmlWriter->startElement($pathNode);
					}
				}
				else{
					$xmlWriter->setIndent(true);
				}
				
				foreach($rows as $i=>$row){

					// $xmlWriter->startElement('ROW');
					$xmlWriter->startElement($rootData);
					foreach($row as $field=>$val){
						//apply value filter
						if(!empty($cols)){
							if(array_key_exists($field, $expo_fields) && in_array($expo_fields[$field] , $cols)){
								//start node path elements
								$nodeName = $expo_fields[$field];
								$parentElements = array();
								$nodePath = explode('/', $nodeName);
								if(count($nodePath)>1){
									foreach($nodePath as $node){
										if(!empty($node)){
											array_push($parentElements, $node);
										}
									}
									if(count($parentElements)>1){
										for($elm=0;$elm<(count($parentElements)-1);$elm++){
											$xmlWriter->startElement($parentElements[$elm]);
										}
										$nodeName = $parentElements[count($parentElements)-1];
									}
								}
								
								//write node data
								if($xmlConfig->node=='other'){
									$xmlWriter->startElement($xmlConfig->name);
										$xmlWriter->writeAttribute($xmlConfig->attribute, $nodeName);
									$xmlWriter->text($val);
									$xmlWriter->endElement();
								}
								else{
									$xmlWriter->writeElement($nodeName, $val);
								}
								
								//close node path elements
								for($elm=1;$elm<count($parentElements);$elm++){
									$xmlWriter->endElement();
								}
							}
						}
						else{
							//start node path elements
							$nodeName = $expo_fields[$field];
							$parentElements = array();
							$nodePath = explode('/', $nodeName);
							if(count($nodePath)>1){
								foreach($nodePath as $node){
									if(!empty($node)){
										array_push($parentElements, $node);
									}
								}
								if(count($parentElements)>1){
									for($elm=0;$elm<(count($parentElements)-1);$elm++){
										$xmlWriter->startElement($parentElements[$elm]);
									}
									$nodeName = $parentElements[count($parentElements)-1];
								}
							}
							
							//write node data
							if($xmlConfig->node=='other'){
								$xmlWriter->startElement($xmlConfig->name);
									$xmlWriter->writeAttribute($xmlConfig->attribute, $nodeName);
								$xmlWriter->text($val);
								$xmlWriter->endElement();
							}
							else{
								$xmlWriter->writeElement($nodeName, $val);
							}
							
							//close node path elements
							for($elm=1;$elm<count($parentElements);$elm++){
								$xmlWriter->endElement();
							}
						}
					}
					$xmlWriter->endElement();
					if($schedule->type==2){
						$flag = fwrite($fp, $xmlWriter->flush(true));
					}
					else{
						if($params->mode=='w'){
							if($i==0){
								$fp = @fopen($path, $params->mode);
								$flag = fwrite($fp, $xmlWriter->flush(true));
							}
							else{
								$fp = @fopen($path, 'r+');
								fseek($fp, 0,SEEK_END);
								$flag = fwrite($fp, $xmlWriter->flush(true));
							}
						}
						else{
							$fp = @fopen($path, 'r+');
							if($i==0){
								fseek($fp,(filesize($path)-strlen($replaceTags.PHP_EOL)));
								$flag = fwrite($fp, $xmlWriter->flush(true));
							}
							else{
								fseek($fp, 0,SEEK_END);
								$flag = fwrite($fp, $xmlWriter->flush(true));
							}
						}
					}
					if($flag !== FALSE)
					$n++;
				}
			}
			else{
				//fetch profile details
				$query = 'select * from #__vd_profiles where id='.$schedule->profileid;
				$db->setQuery($query);
				$profile = $db->loadObject();
				$profile_params = json_decode($profile->params);
				$log->table = $profile_params->table;
				if($schedule->type!=2){
					//log info
					JLog::add(JText::sprintf('EXPORT_PROFILE', $profile->title), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_TABLE', $profile_params->table), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_FORMAT', 'XML'), JLog::INFO, 'com_vdata');
				}
				//retrieve primary key for id(primary key) filter values
				$query = 'SHOW KEYS FROM '.$dbc->quoteName($profile_params->table).' WHERE Key_name = "PRIMARY"';
				$dbc->setQuery($query);
				$pkey = $dbc->loadObjectList();//Column_name

				if( property_exists($schedule_cols, 'quick') ){
					$xmlWriter->openMemory();
					if($params->mode=='w'){
						$xmlWriter->setIndent(true);
						$xmlWriter->startDocument('1.0','UTF-8');
						// $xmlWriter->startElement('ROWSET');
						foreach($rootNodePath as $pathNode){
							$xmlWriter->startElement($pathNode);
						}
					}
					else{
						$xmlWriter->setIndent(true);
					}
					foreach($rows as $i=>$row){

						// $xmlWriter->startElement('ROW');
						$xmlWriter->startElement($rootData);
						foreach($row as $field=>$val){
							//apply value filter
							if(!empty($cols)){
								if($schedule_cols->quick==1){
									if(in_array($field , $cols)){
										
										if($xmlConfig->node=='other'){
											$xmlWriter->startElement($xmlConfig->name);
												$xmlWriter->writeAttribute($xmlConfig->attribute, $field);
											$xmlWriter->text($val);
											$xmlWriter->endElement();
										}
										else{
											$xmlWriter->writeElement($field, $val);
										}
										
									}
								}
								else{
									if(in_array($expo_fields[$field] , $cols)){
										//start node path elements
										$nodeName = $expo_fields[$field];
										$parentElements = array();
										$nodePath = explode('/', $nodeName);
										if(count($nodePath)>1){
											foreach($nodePath as $node){
												if(!empty($node)){
													array_push($parentElements, $node);
												}
											}
											if(count($parentElements)>1){
												for($elm=0;$elm<(count($parentElements)-1);$elm++){
													$xmlWriter->startElement($parentElements[$elm]);
												}
												$nodeName = $parentElements[count($parentElements)-1];
											}
										}
										
										//write node data
										if($xmlConfig->node=='other'){
											$xmlWriter->startElement($xmlConfig->name);
												$xmlWriter->writeAttribute($xmlConfig->attribute, $nodeName);
											$xmlWriter->text($val);
											$xmlWriter->endElement();
										}
										else{
										$xmlWriter->writeElement($nodeName, $val);
										}
										//close node path elements
										for($elm=1;$elm<count($parentElements);$elm++){
											$xmlWriter->endElement();
										}
									}
								}
							}
							else{
								if($schedule_cols->quick==1){
									
									if($xmlConfig->node=='other'){
										$xmlWriter->startElement($xmlConfig->name);
											$xmlWriter->writeAttribute($xmlConfig->attribute, $field);
										$xmlWriter->text($val);
										$xmlWriter->endElement();
									}
									else{
									$xmlWriter->writeElement($field, $val);
									}
									
								}
								else{
									//start node path elements
									$nodeName = $expo_fields[$field];
									$parentElements = array();
									$nodePath = explode('/', $nodeName);
									if(count($nodePath)>1){
										foreach($nodePath as $node){
											if(!empty($node)){
												array_push($parentElements, $node);
											}
										}
										if(count($parentElements)>1){
											for($elm=0;$elm<(count($parentElements)-1);$elm++){
												$xmlWriter->startElement($parentElements[$elm]);
											}
											$nodeName = $parentElements[count($parentElements)-1];
										}
									}
									//write node data
									if($xmlConfig->node=='other'){
										$xmlWriter->startElement($xmlConfig->name);
											$xmlWriter->writeAttribute($xmlConfig->attribute, $nodeName);
										$xmlWriter->text($val);
										$xmlWriter->endElement();
									}
									else{
									$xmlWriter->writeElement($nodeName, $val);
									}
									//close node path elements
									for($elm=1;$elm<count($parentElements);$elm++){
										$xmlWriter->endElement();
									}
								}
							}
						}
						$xmlWriter->endElement();
						if($schedule->type==2){
							$flag = fwrite($fp, $xmlWriter->flush(true));
						}
						else{
							if($params->mode=='w'){
								if($i==0){
									$fp = @fopen($path, $params->mode);
									$flag = fwrite($fp, $xmlWriter->flush(true));
								}
								else{
									$fp = @fopen($path, 'r+');
									fseek($fp, 0,SEEK_END);
									$flag = fwrite($fp, $xmlWriter->flush(true));
								}
							}
							else{
								$fp = @fopen($path, 'r+');
								if($i==0){
									fseek($fp,(filesize($path)-strlen($replaceTags.PHP_EOL)));
									$flag = fwrite($fp, $xmlWriter->flush(true));
								}
								else{
									fseek($fp, 0,SEEK_END);
									$flag = fwrite($fp, $xmlWriter->flush(true));
								}
							}
						}
						if($flag !== FALSE)
						$n++;
					}
				}
				else{
					//write data
					$xmlWriter->openMemory();
					if($params->mode=='w'){
						$xmlWriter->setIndent(true);
						$xmlWriter->startDocument('1.0','UTF-8');
						// $xmlWriter->startElement('ROWSET');
						foreach($rootNodePath as $pathNode){
							$xmlWriter->startElement($pathNode);
						}
					}
					else{
						$xmlWriter->setIndent(true);
					}
					foreach($rows as $i=>$row)
					{
						// $xmlWriter->startElement('ROW');
						$xmlWriter->startElement($rootData);
						foreach($profile_params->fields as $column=>$field){
							//apply value filter
							if(!empty($cols)){
								if(!array_key_exists($column, $expo_fields) || !in_array($expo_fields[$column], $cols))
									continue;
							}
							switch($field->data){
								case 'include' :
									//start node path elements
									$nodeName = $expo_fields[$column];
									$parentElements = array();
									$nodePath = explode('/', $nodeName);
									if(count($nodePath)>1){
										foreach($nodePath as $node){
											if(!empty($node)){
												array_push($parentElements, $node);
											}
										}
										if(count($parentElements)>1){
											for($elm=0;$elm<(count($parentElements)-1);$elm++){
												$xmlWriter->startElement($parentElements[$elm]);
											}
											$nodeName = $parentElements[count($parentElements)-1];
										}
									}
									//write node data
									if($xmlConfig->node=='other'){
										$xmlWriter->startElement($xmlConfig->name);
											$xmlWriter->writeAttribute($xmlConfig->attribute, $nodeName);
										$xmlWriter->text($row->{$column});
										$xmlWriter->endElement();
									}
									else{
									$xmlWriter->writeElement($nodeName, $row->{$column});
									}
									//close node path elements
									for($elm=1;$elm<count($parentElements);$elm++){
										$xmlWriter->endElement();
									}
								break;
								case 'defined' :
									$defined = $field->default;
									$defined = $this->getDefinedValue($field->default, $profile_params->fields, (array)$row, $expo_fields);
									if($xmlConfig->node=='other'){
										$xmlWriter->startElement($xmlConfig->name);
											$xmlWriter->writeAttribute($xmlConfig->attribute, $expo_fields[$column]);
										$xmlWriter->text($defined);
										$xmlWriter->endElement();
									}
									else{
									$xmlWriter->writeElement($expo_fields[$column], $defined);
									}
								break;
								case 'reference' :
									$query = "SELECT ".$db->quoteName($field->table.''.$i).".".implode(','.$db->quoteName($field->table.''.$i).".", $field->reftext)." FROM ".$db->quoteName($field->table)." AS ".$db->quoteName($field->table.''.$i)." JOIN ".$db->quoteName($profile_params->table)." ON ".$db->quoteName($field->table.''.$i).".".$db->quoteName($field->on)." = ".$db->quoteName($profile_params->table).".".$column." WHERE ".$db->quoteName($profile_params->table).".".$column." = ".$db->quote($row->{$column});
									$db->setQuery($query);
									$ref_data = $db->loadObject();
									if($ref_data){
										foreach($ref_data as $rkey=>$rval){
											//start node path elements
											$nodeName = $expo_fields[$column][$rkey];
											$parentElements = array();
											$nodePath = explode('/', $nodeName);
											if(count($nodePath)>1){
												foreach($nodePath as $node){
													if(!empty($node)){
														array_push($parentElements, $node);
													}
												}
												if(count($parentElements)>1){
													for($elm=0;$elm<(count($parentElements)-1);$elm++){
														$xmlWriter->startElement($parentElements[$elm]);
													}
													$nodeName = $parentElements[count($parentElements)-1];
												}
											}
											//write node data
											if($xmlConfig->node=='other'){
												$xmlWriter->startElement($xmlConfig->name);
													$xmlWriter->writeAttribute($xmlConfig->attribute, str_replace('#__', $dbPrefix, $nodeName));
												$xmlWriter->text($rval);
												$xmlWriter->endElement();
											}
											else{
											$xmlWriter->writeElement(str_replace('#__', $dbPrefix, $nodeName), $rval);
											}
											//close node path elements
											for($elm=1;$elm<count($parentElements);$elm++){
												$xmlWriter->endElement();
											}
										}
									}
								break;
							}
						}
						
						//write child table record
						if(!empty($profile_params->joins->table2) && empty($cols) ){
							//if value filter applied skip child record
							
							$exData = array();
							$exData[$profile_params->table][] = $row;
							
							foreach($profile_params->joins->table2 as $c=>$child){
								// $xmlWriter->startElement(str_replace('#__', '', $child));
								$child_root_tag = !empty($expo_fields[$child]["root_tag"])?$expo_fields[$child]["root_tag"]:str_replace('#__', $dbPrefix, $child);
								$xmlWriter->startElement( $child_root_tag );
								if(array_key_exists($child, $expo_fields)){
									
									$response_data = $this->getExportChildData($db, $c, $profile_params, $exData,'ob');
									if($response_data->result=='error'){
										jexit('{"result":"error", "error":"'.$response_data->error.'"}');
									}
									$child_data = $response_data->data;
									
									if(!empty($child_data)){
										foreach($child_data as $cd){
											$exData[$child][] = $cd;
										}
									}

									foreach($child_data as $cd=>$cdata){
										// $xmlWriter->startElement('CHILD');
										$xmlWriter->startElement($rootDataChild);
										
										foreach($profile_params->joins->columns[$c] as $column=>$field){
											switch($field->data){
												case 'include' :
													//start node path elements
													$nodeName = $expo_fields[$child][$column];
													$parentElements = array();
													$nodePath = explode('/', $nodeName);
													if(count($nodePath)>1){
														foreach($nodePath as $node){
															if(!empty($node)){
																array_push($parentElements, $node);
															}
														}
														if(count($parentElements)>1){
															for($elm=0;$elm<(count($parentElements)-1);$elm++){
																$xmlWriter->startElement($parentElements[$elm]);
															}
															$nodeName = $parentElements[count($parentElements)-1];
														}
													}
													//write node data
													if($xmlConfig->node=='other'){
														$xmlWriter->startElement($xmlConfig->name);
															$xmlWriter->writeAttribute($xmlConfig->attribute, $nodeName);
														$xmlWriter->text($cdata->{$column});
														$xmlWriter->endElement();
													}
													else{
													$xmlWriter->writeElement($nodeName, $cdata->{$column});
													}
													//close node path elements
													for($elm=1;$elm<count($parentElements);$elm++){
														$xmlWriter->endElement();
													}
												break;
												case 'defined' :
													$defined = $field->default;
													$defined = $this->getDefinedValue($field->default, $profile_params->joins->columns[$c], $cdata, $expo_fields[$child]);
													if($xmlConfig->node=='other'){
														$xmlWriter->startElement($xmlConfig->name);
															$xmlWriter->writeAttribute($xmlConfig->attribute, $expo_fields[$child][$column]);
														$xmlWriter->text($defined);
														$xmlWriter->endElement();
													}
													else{
													$xmlWriter->writeElement($expo_fields[$child][$column], $defined);
													}
												break;
												case 'reference' :
													$query = "SELECT ".$db->quoteName($field->table.''.$c).".".implode(','.$db->quoteName($field->table.''.$c).".", $field->reftext)." FROM ".$db->quoteName($field->table)." AS ".$db->quoteName($field->table.''.$c)." JOIN ".$db->quoteName($child)." ON ".$db->quoteName($field->table.''.$c).".".$db->quoteName($field->on)." = ".$db->quoteName($child).".".$column." WHERE ".$db->quoteName($child).".".$column." = ".$db->quote($cdata->{$column});
													$db->setQuery($query);
													$ref_data = $db->loadAssoc();
													
													if($ref_data){
														foreach($ref_data as $rkey=>$rval){
															$valTest = ($child=='#__modules_menu' && $cdata->{$column}<0)?"(-)".$rval:$rval;
															//start node path elements
															$nodeName = $expo_fields[$child][$column][$rkey];
															$parentElements = array();
															$nodePath = explode('/', $nodeName);
															if(count($nodePath)>1){
																foreach($nodePath as $node){
																	if(!empty($node)){
																		array_push($parentElements, $node);
																	}
																}
																if(count($parentElements)>1){
																	for($elm=0;$elm<(count($parentElements)-1);$elm++){
																		$xmlWriter->startElement($parentElements[$elm]);
																	}
																	$nodeName = $parentElements[count($parentElements)-1];
																}
															}
															//write node data
															if($xmlConfig->node=='other'){
																$xmlWriter->startElement($xmlConfig->name);
																	$xmlWriter->writeAttribute($xmlConfig->attribute, $nodeName);
																$xmlWriter->text($valTest);
																$xmlWriter->endElement();
															}
															else{
															$xmlWriter->writeElement($nodeName, $rval);
															}
															//close node path elements
															for($elm=1;$elm<count($parentElements);$elm++){
																$xmlWriter->endElement();
															}
														}
													}
													else{
														foreach($field->reftext as $rkey=>$rval){
															if($xmlConfig->node=='other'){
																$xmlWriter->startElement($xmlConfig->name);
																	$xmlWriter->writeAttribute($xmlConfig->attribute, $expo_fields[$child][$column][$rval]);
																$xmlWriter->text('');
																$xmlWriter->endElement();
															}
															else{
															$xmlWriter->writeElement($expo_fields[$child][$column][$rval], '');
															}
														}
													}
												break;
											}
										}
										$xmlWriter->endElement();
									}
								}
								$xmlWriter->endElement();
							}
						}
						
						$xmlWriter->endElement();
						
						if($schedule->type==2){
							$flag = fwrite($fp, $xmlWriter->flush(true));
						}
						else{
							if($params->mode=='w'){
								if($i==0){
									$fp = @fopen($path, $params->mode);
									$flag = fwrite($fp, $xmlWriter->flush(true));
								}
								else{
									$fp = @fopen($path, 'r+');
									fseek($fp, 0,SEEK_END);
									$flag = fwrite($fp, $xmlWriter->flush(true));
								}
							}
							else{
								$fp = @fopen($path, 'r+');
								if($i==0){
									fseek($fp,(filesize($path)-strlen($replaceTags.PHP_EOL)));
									$flag = fwrite($fp, $xmlWriter->flush(true));
								}
								else{
									fseek($fp, 0,SEEK_END);
									$flag = fwrite($fp, $xmlWriter->flush(true));
								}
							}
						}
						if($flag !== FALSE)
							$n++;
					}
				}
			}
			
			if($schedule->type==2 && $schedule->iotype==1){
				file_put_contents("php://memory", $replaceTags.PHP_EOL, FILE_APPEND);
				fwrite($fp, $replaceTags);
				rewind($fp);
				header('Content-Type: text/xml');
				while($line=fgets($fp,65535)) {
				   echo $line;
				}
				jexit();
			}
			
			$close = file_put_contents($path, $replaceTags.PHP_EOL, FILE_APPEND);
			$xmlWriter->endDocument();
			fclose($fp);
			
			//email export file
			if( ($n>0) && isset($params->sendfile->status) && ($params->sendfile->status==1) && isset($params->sendfile->emails) && !empty($params->sendfile->emails) ){
				$emails = explode(',', $params->sendfile->emails);
				$cc = explode(',', $params->sendfile->cc);
				$bcc = explode(',', $params->sendfile->bcc);
				$subject = $params->sendfile->subject;
				$body = $params->sendfile->tmpl;
				$attachment = $path;
				$sendfile = $this->sendFile($emails, $cc, $bcc, $subject, $body, $attachment);
			}
			//write file to remote server using ftp
			if( ($n>0) && ($params->server=='remote')){
				if(!$this->uploadToFtp((array)$params->ftp, $path)){
					jexit('{"result":"success", "message":"'.JText::_('VDATA_FTP_FILE_WRITE_ERROR').'"}');
				}
				jimport('joomla.filesystem.file');
				if(!JFile::delete($path)){
					JLog::add(JText::sprintf('VDATA_UNABLE_TO_DELETE_LOCAL_COPY', $path), JLog::INFO, 'com_vdata');
				}
			}
			
			JLog::add(JText::sprintf('EXPORT_RECORD_COUNT', $n), JLog::INFO, 'com_vdata');
			$log->op_end =  JFactory::getDate(microtime(true))->toSql(true);
			$log->status = 'success';
			$log->message = JText::sprintf('LOG_EXPORT_SUCCESS_MSG', $n);
			$log->iocount = $n;
			$flag = $this->setLog($log);
			//send notification email
			$notifyProfile = ($schedule->profileid!=0)?$profile->title:"";
			$notifyQry = ($schedule->profileid==0)?$schedule->qry:"";
			$notify = $this->sendNotificationEmail( $schedule->iotype,1 , array('schedule_id'=>$schedule->id,'schedule_title'=>$schedule->title,"schedule_qry"=>$notifyQry,'profile_id'=>$schedule->profileid,'profile_title'=>$notifyProfile,"count"=>$n, "format"=>"xml") );
			
			jexit('{"result":"success", "message":"'.JText::sprintf('LOG_EXPORT_SUCCESS_MSG', $n).'"}');
		}
		
		function autoExportJson($schedule)
		{
			$log = new stdClass();
			$log->iotype = 1;
			$log->profileid = $schedule->profileid;
			$log->cronid = $schedule->id;
			$log->side = 'cron';
			$log->user = JFactory::getUser()->id;
			$op_start = JFactory::getDate('now')->format('m-d-Y_hia');
			$log_file = 'com_vdata_plg_custom'.'_'.$op_start.'.txt';
			
			$config = $this->getConfig();
			if($config->logging){
				$logger = $this->initializeLogger();
			}
			
			$log->logfile = $log_file;
			$log->op_start =  JFactory::getDate(microtime(true))->toSql(true);
			
			//joomla database object
			$db = JFactory::getDbo();
			//database object from com_vdata configuration
			$dbc = $this->getDbc();
			$input 		= JFactory::getApplication()->input;
			// fetch value filter values
			$columns 		= $input->get('fields', '', 'RAW');
			
			$params = json_decode($schedule->params);
			$schedule_cols = json_decode($schedule->columns);
			$expo_fields = json_decode(json_encode($schedule_cols->fields), true);
			
			$path = $params->path;		
			if($params->server == 'local'){
				$path = JPATH_ROOT.'/'.$params->path;
				$base = basename($path);
				$dir = dirname($path);
				if(!empty($base)){
					$file_ext = explode('.', $base);
					if( (count($file_ext)>1) ){
						if( !empty($file_ext[count($file_ext)-1]) && strrchr($path, '.') ){
							$dir = dirname($path);
							if (!file_exists($dir)) {
								mkdir($dir, 0777, true);
							}
						}
					}
				}
			}
			else{
				//write remote file->upload after write complete->delete
				$filename = (isset($params->ftp->ftp_file) && !empty($params->ftp->ftp_file))?$params->ftp->ftp_file:JFilterOutput::stringURLSafe($schedule->title).'.json';
				$path = JPATH_ROOT.'/components/com_vdata/uploads/'.$filename;
			}
			
			if( ($schedule->type==1) ){
				try {
					$fp = @fopen($path, "a");
					if ( !$fp ) {
						$open_error = error_get_last();
						jexit('{"result":"error", "error":"'.$open_error['message'].'"}');
					}
				} catch(Exception $e){
					jexit('{"result":"error", "error":"'.$e->getMessage().'"}');
				}
			}
			$log->iofile = $path;
			
			//value filter to fetch requested column values separated by vertical bar
			$cols = array();
			if(!empty($columns)){
				$cols = explode('|', $columns);
			}
			
			$rows = $this->getExpoData($schedule, 'object');
			
			//export record counter
			$n=0;

			if(($rows!=0) && ($params->mode=='w')) {
				if($schedule->type==2 && $schedule->iotype==1)
					$fp = @fopen("php://memory", 'r+');
				else
					$fp = @fopen($path, 'w');
				$flag = fwrite($fp, "[]");
			}
				
			if($schedule->profileid==0) {
				//fetch table name from export query
				$table_name_from_query = strpos(strtolower($schedule->qry), 'from ');
				$table_name_from_query = substr(strtolower($schedule->qry), $table_name_from_query+5);
				$table_name = strpos($table_name_from_query, ' ')!=''?substr(strtolower($table_name_from_query), 0, strpos($table_name_from_query, ' ')):$table_name_from_query;
				
				//retrieve primary key for id(primary key) filter values
				$query = 'SHOW KEYS FROM '.$dbc->quoteName($table_name).' WHERE Key_name = "PRIMARY"';
				$dbc->setQuery($query);
				$pkey = $dbc->loadObjectList();//Column_name
				
				if($schedule->type!=2){
					//log info
					JLog::add(JText::sprintf('EXPORT_QUERY', $schedule->qry), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_TABLE', $table_name), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_FORMAT', 'JSON'), JLog::INFO, 'com_vdata');
				}
				
				foreach($rows as $i=>$row) {

					$data = new stdClass();
					if(!empty($cols)){
						//apply value filter
						foreach($row as $field=>$val){
							
							if(array_key_exists($field, $expo_fields) && in_array($expo_fields[$field] , $cols)){
								$nodeValue = $row->{$field};
								$nodeName = $expo_fields[$field];
								$parentElements = array();
								$nodePath = explode('/', $nodeName);
								if(count($nodePath)>1){
									foreach($nodePath as $node){
										if(!empty($node)){
											array_push($parentElements, $node);
										}
									}
									if(count($parentElements)>1){
										$preObj = new stdClass();
										for($elm=(count($parentElements)-1);$elm>0;$elm--){
											$dummy = new stdClass();
											if($elm==(count($parentElements)-1)){
												$dummy->{$parentElements[$elm]} = $row->{$field};
											}
											else{
												$dummy->{$parentElements[$elm]} = $preObj;
											}
											$preObj = $dummy;
										}
										$nodeName = $parentElements[0];
										$nodeValue = $preObj;
									}
								}
								$data->{$nodeName} = $nodeValue;
								// $data->{$expo_fields[$field]} = $val;
								
							}
						}
					}
					else{
						// $data = $row;
						foreach($row as $field=>$val){
							$nodeValue = $row->{$field};
							$nodeName = $expo_fields[$field];
							$parentElements = array();
							$nodePath = explode('/', $nodeName);
							if(count($nodePath)>1){
								foreach($nodePath as $node){
									if(!empty($node)){
										array_push($parentElements, $node);
									}
								}
								if(count($parentElements)>1){
									$preObj = new stdClass();
									for($elm=(count($parentElements)-1);$elm>0;$elm--){
										$dummy = new stdClass();
										if($elm==(count($parentElements)-1)){
											$dummy->{$parentElements[$elm]} = $row->{$field};
										}
										else{
											$dummy->{$parentElements[$elm]} = $preObj;
										}
										$preObj = $dummy;
									}
									$nodeName = $parentElements[0];
									$nodeValue = $preObj;
								}
							}
							$data->{$nodeName} = $nodeValue;
						}
					}
					
					if($schedule->type==2){
						fseek($fp,-1,SEEK_END);
						if( (count($rows)==1) || ($i==0) || ($n==0) )
							$flag = fwrite($fp, json_encode($data, JSON_UNESCAPED_UNICODE));
						else
							$flag = fwrite($fp, ','.json_encode($data, JSON_UNESCAPED_UNICODE));
						fseek($fp,0,SEEK_END);
						fwrite($fp, "]");
					}
					else{
						if($params->mode=='w') {
							$fp = @fopen($path, 'r+');
							fseek($fp,-1,SEEK_END);
							if( (count($rows)==1) || ($i==0) || ($n==0) )
								$flag = fwrite($fp, json_encode($data, JSON_UNESCAPED_UNICODE));
							else
								$flag = fwrite($fp, ','.json_encode($data, JSON_UNESCAPED_UNICODE));
							fseek($fp,0,SEEK_END);
							fwrite($fp, "]");
						}
						else {
							$fp = @fopen($path, 'r+');
							fseek($fp,-1,SEEK_END);
							$flag = fwrite($fp, ','.json_encode($data, JSON_UNESCAPED_UNICODE));
							fseek($fp,0,SEEK_END);
							fwrite($fp, "]");
						}
					}
					if($flag !== FALSE)
						$n++;
				}
			}
			else {
				//fetch profile details
				$query = 'select * from #__vd_profiles where id='.$schedule->profileid;
				$db->setQuery($query);
				$profile = $db->loadObject();
				$profile_params = json_decode($profile->params);
				
				$log->table = $profile_params->table;
				
				//retrieve primary key for id(primary key) filter values
				$query = 'SHOW KEYS FROM '.$dbc->quoteName($profile_params->table).' WHERE Key_name = "PRIMARY"';
				$dbc->setQuery($query);
				$pkey = $dbc->loadObjectList();//Column_name

				if($schedule->type!=2){
					//log info
					JLog::add(JText::sprintf('EXPORT_PROFILE', $profile->title), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_TABLE', $profile_params->table), JLog::INFO, 'com_vdata');
					JLog::add(JText::sprintf('EXPORT_FORMAT', 'JSON'), JLog::INFO, 'com_vdata');
				}
				
				
				if( property_exists($schedule_cols, 'quick') ){
					foreach($rows as $i=>$row){

						$data = new stdClass();
						//apply value(column) filter
						if(!empty($cols)){
							foreach($row as $field=>$val){
								if($schedule_cols->quick==1){
									if(in_array($field , $cols))
										$data->{$field} = $val;
								}
								else{
									if(in_array($expo_fields[$field] , $cols)){
										$nodeValue = $row->{$field};
										$nodeName = $expo_fields[$field];
										$parentElements = array();
										$nodePath = explode('/', $nodeName);
										if(count($nodePath)>1){
											foreach($nodePath as $node){
												if(!empty($node)){
													array_push($parentElements, $node);
												}
											}
											if(count($parentElements)>1){
												$preObj = new stdClass();
												for($elm=(count($parentElements)-1);$elm>0;$elm--){
													$dummy = new stdClass();
													if($elm==(count($parentElements)-1)){
														$dummy->{$parentElements[$elm]} = $row->{$field};
													}
													else{
														$dummy->{$parentElements[$elm]} = $preObj;
													}
													$preObj = $dummy;
												}
												$nodeName = $parentElements[0];
												$nodeValue = $preObj;
											}
										}
										$data->{$nodeName} = $nodeValue;
										// $data->{$expo_fields[$field]} = $val;
									}
								}
							}
						}
						else{
							if($schedule_cols->quick==1)
								$data = $row;
							else{
								foreach($row as $field=>$val){
									$nodeValue = $row->{$field};
									$nodeName = $expo_fields[$field];
									$parentElements = array();
									$nodePath = explode('/', $nodeName);
									if(count($nodePath)>1){
										foreach($nodePath as $node){
											if(!empty($node)){
												array_push($parentElements, $node);
											}
										}
										if(count($parentElements)>1){
											$preObj = new stdClass();
											for($elm=(count($parentElements)-1);$elm>0;$elm--){
												$dummy = new stdClass();
												if($elm==(count($parentElements)-1)){
													$dummy->{$parentElements[$elm]} = $row->{$field};
												}
												else{
													$dummy->{$parentElements[$elm]} = $preObj;
												}
												$preObj = $dummy;
											}
											$nodeName = $parentElements[0];
											$nodeValue = $preObj;
										}
									}
									$data->{$nodeName} = $nodeValue;
									// $data->{$expo_fields[$field]} = $val;
								}
							}
						}
						if($schedule->type==2){
							fseek($fp,-1,SEEK_END);
							if( (count($rows)==1) || ($i==0) || ($n==0) )
								$flag = fwrite($fp, json_encode($data, JSON_UNESCAPED_UNICODE));
							else
								$flag = fwrite($fp, ','.json_encode($data, JSON_UNESCAPED_UNICODE));
							fseek($fp,0,SEEK_END);
							fwrite($fp, "]");
						}
						else{
							if($params->mode=='w') {
								$fp = @fopen($path, 'r+');
								fseek($fp,-1,SEEK_END);
								if( (count($rows)==1) || ($i==0) || ($n==0) )
									$flag = fwrite($fp, json_encode($data, JSON_UNESCAPED_UNICODE));
								else
									$flag = fwrite($fp, ','.json_encode($data, JSON_UNESCAPED_UNICODE));
								fseek($fp,0,SEEK_END);
								fwrite($fp, "]");
							}
							else {
								$fp = @fopen($path, 'r+');
								fseek($fp,-1,SEEK_END);
								$flag = fwrite($fp, ','.json_encode($data, JSON_UNESCAPED_UNICODE));
								fseek($fp,0,SEEK_END);
								fwrite($fp, "]");
							}
						}
						if($flag !== FALSE)
							$n++;	
					}
					
				}
				else{
					foreach($rows as $i=>$row){

						$data = new stdClass();
						foreach($profile_params->fields as $column=>$field){
							//apply value filter
							if(!empty($cols)){
								if(!array_key_exists($column, $expo_fields) || !in_array($expo_fields[$column], $cols))
									continue;
							}
							switch($field->data){
								case 'include' :
									$nodeValue = $row->{$column};
									$nodeName = $expo_fields[$column];
									$parentElements = array();
									$nodePath = explode('/', $nodeName);
									if(count($nodePath)>1){
										foreach($nodePath as $node){
											if(!empty($node)){
												array_push($parentElements, $node);
											}
										}
										if(count($parentElements)>1){
											$preObj = new stdClass();
											for($elm=(count($parentElements)-1);$elm>0;$elm--){
												$dummy = new stdClass();
												if($elm==(count($parentElements)-1)){
													$dummy->{$parentElements[$elm]} = $row->{$column};
												}
												else{
													$dummy->{$parentElements[$elm]} = $preObj;
												}
												$preObj = $dummy;
											}
											$nodeName = $parentElements[0];
											$nodeValue = $preObj;
										}
									}
									$data->{$nodeName} = $nodeValue;
									// $data->{$expo_fields[$column]} = $row->{$column};
								break;
								case 'defined' :
									$defined = $field->default;
									$defined = $this->getDefinedValue($field->default, $profile_params->fields, (array)$row, $expo_fields);
									$data->{$expo_fields[$column]} = $defined;
								break;
								case 'reference' :
									$query = "SELECT ".$db->quoteName($field->table.''.$i).".".implode(','.$db->quoteName($field->table.''.$i).".", $field->reftext)." FROM ".$db->quoteName($field->table)." AS ".$db->quoteName($field->table.''.$i)." JOIN ".$db->quoteName($profile_params->table)." ON ".$db->quoteName($field->table.''.$i).".".$db->quoteName($field->on)." = ".$db->quoteName($profile_params->table).".".$column." WHERE ".$db->quoteName($profile_params->table).".".$column." = ".$db->quote($row->$column);
									$db->setQuery($query);
									$ref_data = $db->loadObject();
									if($ref_data){
										foreach($ref_data as $j=>$val){
											$nodeValue = $val;
											$nodeName = $expo_fields[$column][$j];
											$parentElements = array();
											$nodePath = explode('/', $nodeName);
											if(count($nodePath)>1){
												foreach($nodePath as $node){
													if(!empty($node)){
														array_push($parentElements, $node);
													}
												}
												if(count($parentElements)>1){
													$preObj = new stdClass();
													for($elm=(count($parentElements)-1);$elm>0;$elm--){
														$dummy = new stdClass();
														if($elm==(count($parentElements)-1)){
															$dummy->{$parentElements[$elm]} = $val;
														}
														else{
															$dummy->{$parentElements[$elm]} = $preObj;
														}
														$preObj = $dummy;
													}
													$nodeName = $parentElements[0];
													$nodeValue = $preObj;
												}
											}
											$data->{$nodeName} = $nodeValue;
											// $data->{str_replace('#__', '', $expo_fields[$column][$j])} = $val;
										}
									}
								break;
							}
						}
						//write child table record
						if(!empty($profile_params->joins->table2) && empty($cols) ){
							$exData = array();
							$exData[$profile_params->table][] = $data;
							
							foreach($profile_params->joins->table2 as $c=>$child){
								if(array_key_exists($child, $expo_fields)){
									
									$response_data = $this->getExportChildData($db, $c, $profile_params, $exData,'ob');
									if($response_data->result=='error'){
										jexit('{"result":"error", "error":"'.$response_data->error.'"}');
									}
									$child_data = $response_data->data;
									
									if(!empty($child_data)){
										foreach($child_data as $cd){
											$exData[$child][] = $cd;
										}
									}
									$child_array = array();
									foreach($child_data as $cd=>$cdata){
										$child_obj = new stdClass();
										foreach($profile_params->joins->columns[$c] as $column=>$field){
											switch($field->data){
												case 'include' :
													$nodeValue = $cdata->{$column};
													$nodeName = $expo_fields[$child][$column];
													$parentElements = array();
													$nodePath = explode('/', $nodeName);
													if(count($nodePath)>1){
														foreach($nodePath as $node){
															if(!empty($node)){
																array_push($parentElements, $node);
															}
														}
														if(count($parentElements)>1){
															$preObj = new stdClass();
															for($elm=(count($parentElements)-1);$elm>0;$elm--){
																$dummy = new stdClass();
																if($elm==(count($parentElements)-1)){
																	$dummy->{$parentElements[$elm]} = $cdata->{$column};
																}
																else{
																	$dummy->{$parentElements[$elm]} = $preObj;
																}
																$preObj = $dummy;
															}
															$nodeName = $parentElements[0];
															$nodeValue = $preObj;
														}
													}
													$child_obj->{$nodeName} = $nodeValue;
													// $child_obj->{$expo_fields[$child][$column]} = $cdata->{$column};
												break;
												case 'defined' :
													$defined = $field->default;
													$defined = $this->getDefinedValue($field->default, $profile_params->joins->columns[$c], $cdata, $expo_fields[$child]);
													$child_obj->{$expo_fields[$child][$column]} = $defined;
												break;
												case 'reference' :
													$query = "SELECT ".$db->quoteName($field->table.''.$c).".".implode(','.$db->quoteName($field->table.''.$c).".", $field->reftext)." FROM ".$db->quoteName($field->table)." AS ".$db->quoteName($field->table.''.$c)." JOIN ".$db->quoteName($child)." ON ".$db->quoteName($field->table.''.$c).".".$db->quoteName($field->on)." = ".$db->quoteName($child).".".$column." WHERE ".$db->quoteName($child).".".$column." = ".$db->quote($cdata->{$column});
													$db->setQuery($query);
													$ref_data = $db->loadAssoc();
													if($ref_data){
														foreach($ref_data as $rkey=>$rval){
															$nodeValue = $rval;
															$nodeName = $expo_fields[$child][$column][$rkey];
															$parentElements = array();
															$nodePath = explode('/', $nodeName);
															if(count($nodePath)>1){
																foreach($nodePath as $node){
																	if(!empty($node)){
																		array_push($parentElements, $node);
																	}
																}
																if(count($parentElements)>1){
																	$preObj = new stdClass();
																	for($elm=(count($parentElements)-1);$elm>0;$elm--){
																		$dummy = new stdClass();
																		if($elm==(count($parentElements)-1)){
																			$dummy->{$parentElements[$elm]} = $rval;
																		}
																		else{
																			$dummy->{$parentElements[$elm]} = $preObj;
																		}
																		$preObj = $dummy;
																	}
																	$nodeName = $parentElements[0];
																	$nodeValue = $preObj;
																}
															}
															$child_obj->{$nodeName} = $nodeValue;
															// $child_obj->{$expo_fields[$child][$column][$rkey]} = $rval;
														}
													}
												break;
											}
										}
										array_push($child_array, $child_obj);
										
									}
									$data->{str_replace('#__', '', $child)} = $child_array;
								}
							}
						}
						
						if($schedule->type==2){
							fseek($fp,-1,SEEK_END);
							if( (count($rows)==1) || ($i==0) || ($n==0) )
								$flag = fwrite($fp, json_encode($data, JSON_UNESCAPED_UNICODE));
							else
								$flag = fwrite($fp, ','.json_encode($data, JSON_UNESCAPED_UNICODE));
							fseek($fp,0,SEEK_END);
							fwrite($fp, "]");
						}
						else{
							if($params->mode=='w') {
								$fp = @fopen($path, 'r+');
								fseek($fp,-1,SEEK_END);
								if( (count($rows)==1) || ($i==0) || ($n==0) )
									$flag = fwrite($fp, json_encode($data, JSON_UNESCAPED_UNICODE));
								else
									$flag = fwrite($fp, ','.json_encode($data, JSON_UNESCAPED_UNICODE));
								fseek($fp,0,SEEK_END);
								fwrite($fp, "]");
							}
							else {
								$fp = @fopen($path, 'r+');
								fseek($fp,-1,SEEK_END);
								$flag = fwrite($fp, ','.json_encode($data, JSON_UNESCAPED_UNICODE));
								fseek($fp,0,SEEK_END);
								fwrite($fp, "]");
							}
						}
						if($flag !== FALSE)
							$n++;
					}
				}
			}
			
			if($schedule->type==2){
				rewind($fp);
				while($line=fgets($fp,65535)) {
				   echo $line;
				}
				jexit();
			}
			fclose($fp);
			
			//email export file
			if( ($n>0) && isset($params->sendfile->status) && ($params->sendfile->status==1) && isset($params->sendfile->emails) && !empty($params->sendfile->emails) ){
				$emails = explode(',', $params->sendfile->emails);
				$cc = explode(',', $params->sendfile->cc);
				$bcc = explode(',', $params->sendfile->bcc);
				$subject = $params->sendfile->subject;
				$body = $params->sendfile->tmpl;
				$attachment = $path;
				$sendfile = $this->sendFile($emails, $cc, $bcc, $subject, $body, $attachment);
			}
			//write file to remote server using ftp
			if( ($n>0) && ($params->server=='remote') ){
				if(!$this->uploadToFtp((array)$params->ftp, $path)){
					jexit('{"result":"success", "message":"'.JText::_('VDATA_FTP_FILE_WRITE_ERROR').'"}');
				}
				jimport('joomla.filesystem.file');
				if(!JFile::delete($path)){
					JLog::add(JText::sprintf('VDATA_UNABLE_TO_DELETE_LOCAL_COPY', $path), JLog::INFO, 'com_vdata');
				}
			}
			
			JLog::add(JText::sprintf('EXPORT_RECORD_COUNT', $n), JLog::INFO, 'com_vdata');
			$log->op_end =  JFactory::getDate(microtime(true))->toSql(true);
			$log->status = 'success';
			$log->message = JText::sprintf('LOG_EXPORT_SUCCESS_MSG', $n);
			$log->iocount = $n;
			$flag = $this->setLog($log);
			//send notification email
			$notifyProfile = ($schedule->profileid!=0)?$profile->title:"";
			$notifyQry = ($schedule->profileid==0)?$schedule->qry:"";
			$notify = $this->sendNotificationEmail( $schedule->iotype,1 , array('schedule_id'=>$schedule->id,'schedule_title'=>$schedule->title,"schedule_qry"=>$notifyQry,'profile_id'=>$schedule->profileid,'profile_title'=>$notifyProfile,"count"=>$n, "format"=>"json") );
			
			jexit('{"result":"success", "message":"'.JText::sprintf('LOG_EXPORT_SUCCESS_MSG', $n).'"}');
		}
		
		function autoExportRemote($schedule)
		{
			$log = new stdClass();
			$log->iotype = 1;
			$log->profileid = $schedule->profileid;
			$log->cronid = $schedule->id;
			$log->side = 'cron';
			$log->user = JFactory::getUser()->id;
			$op_start = JFactory::getDate('now')->format('m-d-Y_hia');
			$log_file = 'com_vdata_plg_custom'.'_'.$op_start.'.txt';
			
			$config = $this->getConfig();
			if($config->logging){
				$logger = $this->initializeLogger();
			}
			
			$log->op_start =  JFactory::getDate(microtime(true))->toSql(true);
			$log->logfile = $log_file;
			
			//joomla database object
			$db = JFactory::getDbo();
			//database object from com_vdata configuration
			$local_db = $this->getDbc();
			$query_local = $local_db->getQuery(true);
			
			$input 	= JFactory::getApplication()->input;
			// fetch value filter values
			$columns = $input->get('fields', '', 'RAW');
			
			$params = json_decode($schedule->params);
			$schedule_cols = json_decode($schedule->columns);
			
			try {
				$option = array();
				if(property_exists($params, 'local_db') && ($params->local_db==1)){
					$remote_db = $this->getDbc();
				}
				else{
					$option['driver'] = $params->driver;
					$option['host'] = $params->host;
					$option['user'] = $params->user;
					$option['password'] = $params->password;
					$option['database'] = $params->database;
					$option['prefix'] = $params->dbprefix;
					
					$remote_db = JDatabaseDriver::getInstance( $option );
					$remote_db->connect();
				}
				$query_remote = $remote_db->getQuery(true);
			}
			catch (RuntimeException $e) {
				// log error
				$log->op_end = JFactory::getDate('now')->toSql(true);;
				$log->status = 'abort';
				$log->message = $e->getMessage();
				$flag = $this->setLog($log);
				jexit('{"result":"error", "error":"'.$e->getMessage().'"}');
			}
			
			$dids = array();
			
			//value filter to fetch requested column values separated by vertical bar
			$cols = array();
			if(!empty($columns)){
				$cols = explode('|', $columns);
			}
			
			$rows = $this->getExpoData($schedule, 'object');
			//export record counter
			$n=0;
			if($schedule->profileid==0) {
				//fetch table name from export query
				$table_name_from_query = strpos(strtolower($schedule->qry), 'from ');
				$table_name_from_query = substr(strtolower($schedule->qry), $table_name_from_query+5);
				$table_name = strpos($table_name_from_query, ' ')!=''?substr(strtolower($table_name_from_query), 0, strpos($table_name_from_query, ' ')):$table_name_from_query;
				
				//retrieve primary key for id(primary key) filter values
				$query_local = 'SHOW KEYS FROM '.$local_db->quoteName($table_name).' WHERE Key_name = "PRIMARY"';
				$local_db->setQuery($query_local);
				$pkey = $local_db->loadObjectList();
				if( count($pkey)==1 ){
					$pkey = $pkey[0];
				}
				else{
					$pkey = null;
				}
				
				$remote_table = $schedule_cols->table;
				$unkey = $schedule_cols->unkey;
				// $remote_fields = $schedule_cols->fields;
				$remote_fields = json_decode(json_encode($schedule_cols->fields), true);
				
				$query_remote = 'SHOW KEYS FROM '.$remote_db->quoteName($remote_table).' WHERE Key_name = "PRIMARY"';
				$remote_db->setQuery($query_remote);
				$key = $remote_db->loadObjectList();
				
				if(!empty($unkey))
					$primary = $unkey;
				elseif( !empty($key) && (count($key)==1) )
					$primary = $key[0]->Column_name;
				else
					$primary = null;

				//log info
				JLog::add(JText::sprintf('EXPORT_QUERY', $schedule->qry), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('EXPORT_TABLE', $table_name), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('EXPORT_FORMAT', 'DATABASE'), JLog::INFO, 'com_vdata');
				
				foreach($rows as $i=>$row){

					$insert = new stdClass();
					foreach($row as $field=>$val){
						if(!empty($cols)){
							if(in_array($schedule_cols->fields->{$field} , $cols)){
								$insert->{$schedule_cols->fields->$field} = $val;
							}
						}
						else
							$insert->{$remote_fields[$key]} = $value;
					}
					$isNew = true;
					if(!empty($primary) && !empty($insert->{$primary})){
						$query_remote = "select count(*) from ".$remote_db->quoteName($remote_table)." where ".$remote_db->quoteName($primary)." = ".$remote_db->quote($insert->{$primary});
						$remote_db->setQuery($query_remote);
						$count = $remote_db->loadResult();
						$isNew = $count > 0 ? false : true;	
					}
					
					if($params->operation==1) {
						if(!$isNew){
							JLog::add(JText::sprintf('RECORD_EXISTS',$primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
							continue;
						}
						if(!$remote_db->insertObject($remote_table, $insert)) {
							//log error and stop further execution
							$log->op_end = JFactory::getDate('now')->toSql(true);;
							$log->status = 'abort';
							$log->message = $remote_db->stderr();
							$flag = $this->setLog($log);
							jexit('{"result":"error", "error":"'.$remote_db->stderr().'"}');
						}
						if($this->getAffected($remote_db)> 0)
							$n++;
					}
					else {
						if(empty($primary)  || empty($insert->{$primary})) {
							$log->op_end = JFactory::getDate('now')->toSql(true);;
							$log->status = 'abort';
							$log->message = JText::_('PRIMARY_KEY_NOT_FOUND');
							$flag = $this->setLog($log);
							jexit('{"result":"error", "error":"'.JText::_('PRIMARY_KEY_NOT_FOUND').'"}');
						}
						
						$dids[] = $insert->{$primary};
						
						if($isNew) {
							JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
							if(!$remote_db->insertObject($remote_table, $insert)) {
								//log error and stop further execution
								$log->op_end = JFactory::getDate('now')->toSql(true);;
								$log->status = 'abort';
								$log->message = $remote_db->stderr();
								$flag = $this->setLog($log);
								jexit('{"result":"error", "error":"'.$remote_db->stderr().'"}');
							}
							if($this->getAffected($remote_db)> 0)
								$n++;
						}
						else{
							if(!$remote_db->updateObject($remote_table, $insert, $primary)) {
								//log error and stop further execution
								$log->op_end = JFactory::getDate('now')->toSql(true);;
								$log->status = 'abort';
								$log->message = $remote_db->stderr();
								$flag = $this->setLog($log);
								jexit('{"result":"error", "error":"'.$remote_db->stderr().'"}');
							}
							if($this->getAffected($remote_db)> 0)
								$n++;
						}
					}
				}
			}
			else{
				//fetch profile details
				$query = 'select * from #__vd_profiles where id='.$schedule->profileid;
				$db->setQuery($query);
				$profile = $db->loadObject();
				$profile_params = json_decode($profile->params);
				
				$log->table = $profile_params->table;
				
				//retrieve primary key for id(primary key) filter values
				$query_local = 'SHOW KEYS FROM '.$local_db->quoteName($profile_params->table).' WHERE Key_name = "PRIMARY"';
				$local_db->setQuery($query_local);
				$pkey = $local_db->loadObjectList();//Column_name
				if( count($pkey)==1 ){
					$pkey = $pkey[0];
				}
				else{
					$pkey = null;
				}
				
				$remote_table = $schedule_cols->table;
				$unkey = $schedule_cols->unkey;
				// $remote_fields = $schedule_cols->fields;
				$remote_fields = json_decode(json_encode($schedule_cols->fields), true);
				
				$query_remote = 'SHOW KEYS FROM '.$remote_db->quoteName($remote_table).' WHERE Key_name = "PRIMARY"';
				$remote_db->setQuery($query_remote);
				$key = $remote_db->loadObjectList();
				
				if(!empty($unkey))
					$primary = $unkey;
				elseif(!empty($key) && (count($key)==1) )
					$primary = $key[0]->Column_name;
				else
					$primary = null;

				//log info
				JLog::add(JText::sprintf('EXPORT_PROFILE', $profile->title), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('EXPORT_TABLE', $profile_params->table), JLog::INFO, 'com_vdata');
				JLog::add(JText::sprintf('EXPORT_FORMAT', 'DATABASE'), JLog::INFO, 'com_vdata');
				
				if( property_exists($schedule_cols, 'quick') ){
					foreach($rows as $i=>$row){

						$insert = new stdClass();
						//apply value(column) filter
						if(!empty($cols)){
							foreach($row as $field=>$val){
								if($schedule_cols->quick==1){
									if(in_array($field , $cols)){
										$insert->{$field} = $val;
									}
								}
								else{
									if(in_array($remote_fields[$field] , $cols)){
										$insert->{$remote_fields[$field]} = $val;
									}
								}
							}
						}
						else{
							if($schedule_cols->quick==1){
								$insert = $row;
							}
							else{
								foreach($row as $field=>$val){
									$insert->{$remote_fields[$field]} = $val;
								}
							}
						}
						$isNew=true;
						if(!empty($primary) && !empty($insert->{$primary})){
							$query_remote = "select count(*) from ".$remote_db->quoteName($remote_table)." where ".$remote_db->quoteName($primary)." = ".$remote_db->quote($insert->{$primary});
							$remote_db->setQuery($query_remote);
							$count = $remote_db->loadResult();
							$isNew = $count > 0 ? false : true;	
						}
						if($params->operation==1) {
							if(!$isNew){
								JLog::add(JText::sprintf('RECORD_EXISTS',$primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
								continue;
							}
							if(!$remote_db->insertObject($remote_table, $insert)) {
								//log error and stop further execution
								$log->op_end = JFactory::getDate('now')->toSql(true);;
								$log->status = 'abort';
								$log->message = $remote_db->stderr();
								$flag = $this->setLog($log);
								jexit('{"result":"error", "error":"'.$remote_db->stderr().'"}');
							}
							if($this->getAffected($remote_db)> 0)
								$n++;
						}
						else{
							if(empty($primary)  || empty($insert->{$primary})) {
								$log->op_end = JFactory::getDate('now')->toSql(true);;
								$log->status = 'abort';
								$log->message = JText::_('PRIMARY_KEY_NOT_FOUND');
								$flag = $this->setLog($log);
								jexit('{"result":"error", "error":"'.JText::_('PRIMARY_KEY_NOT_FOUND').'"}');
							}
							
							$dids[] = $insert->{$primary};
							
							if($isNew) {
								JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
								if(!$remote_db->insertObject($remote_table, $insert)) {
									//log error and stop further execution
									$log->op_end = JFactory::getDate('now')->toSql(true);;
									$log->status = 'abort';
									$log->message = $remote_db->stderr();
									$flag = $this->setLog($log);
									jexit('{"result":"error", "error":"'.$remote_db->stderr().'"}');
								}
								if($this->getAffected($remote_db)> 0)
									$n++;
							}
							else{
								if(!$remote_db->updateObject($remote_table, $insert, $primary)) {
									//log error and stop further execution
									$log->op_end = JFactory::getDate('now')->toSql(true);;
									$log->status = 'abort';
									$log->message = $remote_db->stderr();
									$flag = $this->setLog($log);
									jexit('{"result":"error", "error":"'.$remote_db->stderr().'"}');
								}
								if($this->getAffected($remote_db)> 0)
									$n++;
							}
						}
					}
				}
				else{
					$join_table = $schedule_cols->join_table;
					foreach($rows as $h=>$row) {

						$isNew = true;
						$insert = new stdClass();
						foreach($profile_params->fields as $column=>$field){
							//apply value filter
							if(!empty($cols)){
								if(!array_key_exists($column, $remote_fields) || !in_array($remote_fields[$column], $cols))
									continue;
							}
							//check if id already exists
							if(!empty($primary) and $column==$primary and $field->data <> "skip")	{		
								$query_remote = 'SELECT count(*) FROM '.$remote_db->quoteName($remote_table).' WHERE '.$remote_db->quoteName($primary).' = '.$remote_db->quote($row->{$column});
								$remote_db->setQuery($query_remote);
								$count = $remote_db->loadResult();
								$isNew = $count > 0 ? false : true;		
							}
							
							switch($field->data){
								case 'include' :
									if(!empty($remote_fields[$column])){
										$insert->{$column} = $row->{$remote_fields[$column]};
									}
								break;
								case 'defined' :
									$defined = $field->default;
									$defined = $this->getDefinedValue($field->default, $profile_params->fields, (array)$row, $remote_fields);
									$insert->{$column} = $defined;
								break;
								case 'reference' :
									//one to one relation
									
									$array_ref_cols = $remote_fields[$column]['refcol'];
									if( empty($remote_fields[$column]['table']) || empty($array_ref_cols) ){
										break;
									}
									$remote_ref_field = array();
									foreach($remote_fields[$column]['refcol'] as $key=>$rfield){
										if(!empty($rfield)){
											$remote_ref_field[] = $rfield;
										}
									}
									
									$query_local = "SELECT ".implode(',',$remote_ref_field)." FROM ".$local_db->quoteName($field->table)." "; 
									$query_local .= "where ".$local_db->quoteName($field->on)."=".$row->{$column};
									$local_db->setQuery($query_local);
									$values = $local_db->loadObject();
									
									if(!empty($values)){
										$query_remote = "select ".$remote_db->quoteName($field->on)." from ".$remote_db->quoteName($field->table)." where 1=1";
										foreach($remote_ref_field as $ref_field){
											$query_remote .= " and ".$remote_db->quoteName($ref_field)." = ".$remote_db->quote($values->{$ref_field});
										}
										$remote_db->setQuery($query_remote);
										$ref_value = $remote_db->loadResult();
										if(!empty($ref_value)){
											$insert->{$column} = $ref_value;
										}
										else{
											JLog::add(JText::sprintf('LOCAL_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
										}
									}
									else{
										JLog::add(JText::sprintf('REMOTE_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
									}
								break;
							}
						}
						
						//insert base table records
						if($params->operation==1)	{
							if(!$isNew){
								JLog::add(JText::sprintf('RECORD_EXISTS',$primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
								continue;
							}
							if($remote_db->insertObject($remote_table, $insert)) {
								$base_in_id = $remote_db->insertid();
							}
							else {
								//log error and stop further execution
								$log->op_end = JFactory::getDate('now')->toSql(true);;
								$log->status = 'abort';
								$log->message = $remote_db->stderr();
								$flag = $this->setLog($log);
								jexit('{"result":"error", "error":"'.$remote_db->stderr().'"}');
							}
							if($this->getAffected($remote_db)> 0)
								$n++;
						}
						else{
							if(empty($primary) || empty($insert->{$primary})) {
								$log->op_end = JFactory::getDate('now')->toSql(true);;
								$log->status = 'abort';
								$log->message = JText::_('PRIMARY_KEY_NOT_FOUND');
								$flag = $this->setLog($log);
								jexit('{"result":"error", "error":"'.JText::_('PRIMARY_KEY_NOT_FOUND').'"}');
							}
							
							$dids[] = $insert->{$primary};
							
							if($isNew) {
								JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $primary, $insert->{$primary}), JLog::ERROR, 'com_vdata');
								if($remote_db->insertObject($remote_table, $insert)) {
									$base_in_id = $remote_db->insertid();
								}
								else {
									//log error and stop further execution
									$log->op_end = JFactory::getDate('now')->toSql(true);;
									$log->status = 'abort';
									$log->message = $remote_db->stderr();
									$flag = $this->setLog($log);
									jexit('{"result":"error", "error":"'.$remote_db->stderr().'"}');
								}
								if($this->getAffected($remote_db)> 0)
									$n++;
							}
							else{
								if($remote_db->updateObject($remote_table, $insert, $primary)) {
									$base_in_id = $insert->{$primary};
								}
								else {
									//log error and stop further execution
									$log->op_end = JFactory::getDate('now')->toSql(true);;
									$log->status = 'abort';
									$log->message = $remote_db->stderr();
									$flag = $this->setLog($log);
									jexit('{"result":"error", "error":"'.$remote_db->stderr().'"}');
								}
								if($this->getAffected($remote_db)> 0)
									$n++;
							}
						}
						
						//if value filter applied skip child record
						if(!empty($cols)){
							continue;
						}
						
						// export child tables records
						if(!empty($profile_params->joins->table2))
						{
							$exData = array();
							$exData[$profile_params->table][] = $row;
							
							foreach($profile_params->joins->table2 as $c=>$child){
								if(array_key_exists($child, $remote_fields)){
									$child_primary = $this->getPrimaryKey($child, $remote_db);
									
									$response_data = $this->getExportChildData($local_db, $c, $profile_params, $exData,'ob');
									if($response_data->result=='error'){
										$log->op_end = JFactory::getDate('now')->toSql(true);;
										$log->status = 'abort';
										$log->message = $response_data->error;
										$flag = $this->setLog($log);
										jexit('{"result":"error", "error":"'.$response_data->error.'"}');
									}
									$child_data = $response_data->data;
									
									if(!empty($child_data)){
										foreach($child_data as $cd){
											$exData[$child][] = $cd;
										}
									}
									$child_array = array();
									foreach($child_data as $cd=>$cdata){
										
										$child_obj = new stdClass();
										foreach($profile_params->joins->columns[$c] as $column=>$field){
											switch($field->data){
												case 'include' :
												if($remote_fields[$child][$column]!=""){
													$child_obj->{$remote_fields[$child][$column]} = $cdata->{$column};
												}
												break;
												case 'defined' :
													$defined = $field->default;
													$defined = $this->getDefinedValue($field->default, $profile_params->joins->columns[$c], $cdata, $remote_fields[$child]);
													$child_obj->{$remote_fields[$child][$column]} = $defined;
												break;
												case 'reference' :
													if( empty($remote_fields[$child][$column]['table']) || empty($remote_fields[$child][$column]['refcol']) ){
														break;
													}
													$remote_ref_field = array();
													foreach($remote_fields[$child][$column]['refcol'] as $key=>$rfield){
														if(!empty($rfield)){
															$remote_ref_field[] = $rfield;
														}
													}
													$query_local = "SELECT ".implode(',',$remote_ref_field)." FROM ".$local_db->quoteName($field->table)." "; 
													$query_local .= "where ".$local_db->quoteName($field->on)."=".$cdata->{$column};
													$local_db->setQuery($query_local);
													$values = $local_db->loadObject();
													if(!empty($values)){
														$query_remote = "SELECT ".$remote_db->quoteName($field->on)." from ".$remote_db->quoteName($field->table)." where 1=1";
														foreach($remote_ref_field as $ref_field){
															$query_remote .= " and ".$remote_db->quoteName($ref_field)." = ".$remote_db->quote($values->{$ref_field});
														}
														$remote_db->setQuery($query_remote);
														$ref_value = $remote_db->loadResult();
														if(!empty($ref_value)){
															$child_obj->{$column} = $ref_value;
														}
														else{
															JLog::add(JText::sprintf('LOCAL_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
														}
													}
													else{
														JLog::add(JText::sprintf('REMOTE_RECORD_NOT_FOUND', $column), JLog::ERROR, 'com_vdata');
													}
												break;
											}
										}
										
										//insert record
										if($params->operation==1)
										{
											if(!$isNew){
												JLog::add(JText::sprintf('RECORD_EXISTS',$child_primary, $child_obj->{$child_primary}), JLog::ERROR, 'com_vdata');
												continue;
											}
											
											if($remote_db->insertObject($child, $child_obj))
												$base_in_id = $remote_db->insertid();
											else{
												$log->op_end = JFactory::getDate('now')->toSql(true);;
												$log->status = 'abort';
												$log->message = $remote_db->stderr();
												$flag = $this->setLog($log);
												jexit('{"result":"error", "error":"'.$remote_db->stderr().'"}');
											}
										}
										else
										{
											if(empty($child_primary) || empty($child_obj->{$child_primary})){
												$log->op_end = JFactory::getDate('now')->toSql(true);;
												$log->status = 'abort';
												$log->message = JText::_('PRIMARY_KEY_NOT_FOUND');
												$flag = $this->setLog($log);
												jexit('{"result":"error", "error":"'.JText::_('PRIMARY_KEY_NOT_FOUND').'"}');
											}
											if($isNew){
												JLog::add(JText::sprintf('RECORD_NOT_EXISTS', $child_primary, $child_obj->{$child_primary}), JLog::ERROR, 'com_vdata');
												//insert if record do not exist
												if($remote_db->insertObject($child, $child_obj))
													$base_in_id = $remote_db->insertid();
												else{
													$log->op_end = JFactory::getDate('now')->toSql(true);;
													$log->status = 'abort';
													$log->message = $remote_db->stderr();
													$flag = $this->setLog($log);
													jexit('{"result":"error", "error":"'.$remote_db->stderr().'"}');
												}
											}
											else{
												if($remote_db->updateObject($child, $child_obj, $child_primary)){
													$base_in_id = $child_obj->{$child_primary};
												}
												else{
													$log->op_end = JFactory::getDate('now')->toSql(true);;
													$log->status = 'abort';
													$log->message = $remote_db->stderr();
													$flag = $this->setLog($log);
													jexit('{"result":"error", "error":"'.$remote_db->stderr().'"}');
												}
											}
										}
										
									}
									
								}
							}
							
							
						}
					}
				}
			}
			JLog::add(JText::sprintf('EXPORT_RECORD_COUNT', $n), JLog::INFO, 'com_vdata');
			if( ($params->operation==2) && !empty($dids) && !empty($primary) ){
				$query_remote = 'delete from '.$remote_db->quoteName($profile_params->table). ' where 1=1';
				foreach($dids as $did){
					$query_remote .= ' and '.$remote_db->quoteName($primary).' <> '.$remote_db->quote($did);
				}
				$remote_db->setQuery($query_remote);
				$res = $remote_db->execute();
			}
			$log->op_end =  JFactory::getDate(microtime(true))->toSql(true);
			$log->status = 'success';
			$log->message = JText::sprintf('LOG_IMPORT_SUCCESS_MSG', $n);
			$log->iocount = $n;
			$flag = $this->setLog($log);
			//send notification email
			$notifyProfile = ($schedule->profileid!=0)?$profile->title:"";
			$notifyQry = ($schedule->profileid==0)?$schedule->qry:"";
			$notify = $this->sendNotificationEmail( $schedule->iotype,1 , array('schedule_id'=>$schedule->id,'schedule_title'=>$schedule->title,"schedule_qry"=>$notifyQry,'profile_id'=>$schedule->profileid,'profile_title'=>$notifyProfile,"count"=>$n, "format"=>"db") );
			
			jexit('{"result":"success", "message":"'.JText::sprintf('LOG_IMPORT_SUCCESS_MSG', $n).'"}');
		}
		
		function setLog($log)
		{
			$config = $this->getConfig();
			if(!$config->logging){
				return false;
			}
			$db = JFactory::getDbo();
			return $db->insertObject('#__vd_logs', $log);
		}
		
		function sendNotificationEmail($iotype, $isCron, $params=array()){
		
			$vdataConfig = $this->getConfig();
			$notificationConfig = json_decode($vdataConfig->notification);
			if(!$notificationConfig->status){
				return false;
			}
			
			$mailer = JFactory::getMailer();
			
			//set sender
			$config = JFactory::getConfig();
			$sender = array(
				$config->get( 'mailfrom' ),
				$config->get( 'fromname' ) 
			);
			$mailer->setSender($sender);

			//set subject
			$subject = ($iotype)?JText::_('VDATA_EXPORT_NOTIFICATION_MAIL_SUBJECT'):JText::_('VDATA_IMPORT_NOTIFICATION_MAIL_SUBJECT');
			$mailer->setSubject($subject);
			
			//set body
			if($isCron){
				$body = ($iotype)?JText::sprintf('VDATA_EXPORT_NOTIFICATION_CRON_MAIL_DESC', $params["count"], $params["format"], $params["schedule_id"], $params["schedule_title"]):JText::sprintf('VDATA_IMPORT_NOTIFICATION_CRON_MAIL_DESC', $params["count"], $params["format"], $params["schedule_id"], $params["schedule_title"]);
			}
			else{
				$body = ($iotype)?JText::sprintf('VDATA_EXPORT_NOTIFICATION_MANUALLY_MAIL_DESC', $params["count"], $params["format"], $params["profile_id"], $params["profile_title"]):JText::sprintf('VDATA_IMPORT_NOTIFICATION_MANUALLY_MAIL_DESC', $params["count"], $params["format"], $params["profile_id"], $params["profile_title"]);
			}
			
			$mailer->setBody($body);
			$mailer->isHTML();
			$mailer->Encoding = 'base64';
			
			//set recipients
			$recipient = explode(',',$notificationConfig->recipients);
			$mailer->addRecipient($recipient);
			
			$send = $mailer->Send();
			if ( $send !== true ) {
				JLog::add($send->__toString(), JLog::ERROR, 'com_vdata');
			}
			else {
				JLog::add(JText::sprintf('VDATA_IMPORT_EXPORT_NOTIFICATION_MAIL_SUCCESS', $notificationConfig->recipients), JLog::INFO, 'com_vdata');
			}
			return true;
		}
		
		function getExpoData($schedule, $op='')
		{
			$db = JFactory::getDbo();
			$dbc = $this->getDbc();
			$input 		= JFactory::getApplication()->input;
			$limit 		= $input->getInt('limit', 0);
			$limitstart = $input->getInt('limitstart', 0);
			
			//fetch id filter falue
			$column 		= $input->getVar('column', '', 'RAW');
			// fetch value filter values
			$values 		= $input->getVar('value', '', 'RAW');
			$cols = array();
			if(!empty($values)){
				$cols = explode('|', $values);
			}
			$keys = array();
			if(!empty($column)) {
				$keys = explode('|', $column);
			}
			
			if($schedule->profileid==0){
				$dbc->setQuery($schedule->qry, $limitstart, $limit);
			}
			else{
				$query = 'select * from #__vd_profiles where id='.$schedule->profileid;
				$db->setQuery($query);
				$profile = $db->loadObject();
				$profile_params = json_decode($profile->params);
				if($profile->quick==1){
					$query = 'select * from '.$dbc->quoteName($profile_params->table);
					$dbc->setQuery($query, $limitstart, $limit);
				}
				else{
					
					$query = "show fields from ".$dbc->quoteName($profile_params->table);
					$dbc->setQuery($query);
					$total_table_columns = $dbc->loadObjectList();
					$total_table_column = array();
					foreach($total_table_columns as $key=>$value){
						array_push($total_table_column, $value->Field);
					} 
					
					$query = "SELECT ".$dbc->quoteName($profile_params->table).".* FROM ".$dbc->quoteName($profile_params->table);
					if( !empty($profile_params->filters) && property_exists($profile_params->filters, 'column') ){
						//filter tracker
						$applyFilter = array();
						$filterTables = array();
						
						foreach($profile_params->filters->column as $j=>$column){
							$filterField = explode('.', $column);
							$applyFilter[$j] = false;
							
							//base table reference column filter
							if(count($filterField)==2){
								if(isset($profile_params->fields) && !empty($profile_params->fields)){
									foreach($profile_params->fields as $iofield=>$ioval){
										if( ($ioval->data=='reference') && ($ioval->table==$filterField[0]) && !in_array($ioval->table, $filterTables) ){
											$query .= " JOIN ".$db->quoteName($filterField[0]).' ON '.$db->quoteName($profile_params->table).".".$db->quoteName($iofield)."=".$db->quoteName($ioval->table).".".$db->quoteName($ioval->on);
											$applyFilter[$j] = true;
											array_push($filterTables, $ioval->table);
											break;
										}
									}
								}
							}
							//child table column filter
							elseif(count($filterField)==3){
								
								if( isset($profile_params->joins) && isset($profile_params->joins->table2) ){
									foreach($profile_params->joins->table2 as $ioidx=>$iotable){
										if( ($iotable==$filterField[1]) ){
											if( ($profile_params->joins->table1[$ioidx]==$profile_params->table) && !in_array($iotable, $filterTables) ){
												//
												$query .= ' JOIN '.$db->quoteName($filterField[1])." ON ".$db->quoteName($profile_params->table).".".$db->quoteName($profile_params->joins->column1[$ioidx])."=".$db->quoteName($filterField[1]).".".$db->quoteName($profile_params->joins->column2[$ioidx]);
												$applyFilter[$j] = true;
												array_push($filterTables, $iotable);
											}
											else{
												$tempIdx = $ioidx;
												$left = $profile_params->joins->table1[$ioidx];
												$joinPath = array($ioidx);
												
												while($tempIdx>=0){
													$tempIdx--;
													if($left==$profile_params->joins->table2[$tempIdx]){
														array_unshift($joinPath, $tempIdx);
														if($profile_params->joins->table1[$tempIdx]==$profile_params->table){
															break;
														}
													}
												}
												
												foreach($joinPath as $joinIdx){
													if(!in_array($profile_params->joins->table2[$joinIdx], $filterTables)){
														$query .= ' JOIN '.$db->quoteName($profile_params->joins->table2[$joinIdx]).' ON '.$db->quoteName($profile_params->joins->table1[$joinIdx]).'.'.$db->quoteName($profile_params->joins->column1[$joinIdx]).'='.$db->quoteName($profile_params->joins->table2[$joinIdx]).'.'.$db->quoteName($profile_params->joins->column2[$joinIdx]);
														array_push($filterTables, $profile_params->joins->table2[$joinIdx]);
														$applyFilter[$j] = true;
													}
												}
											}
											break;
										}
									}
								}
								
							}
							//child table reference column filter
							elseif(count($filterField)==4){
								if( isset($profile_params->joins) && isset($profile_params->joins->table2) ){
									if(!in_array($filterField[1], $filterTables)){
										foreach($profile_params->joins->table2 as $ioidx=>$iotable){
											if($iotable==$filterField[1]){
												if($profile_params->joins->table1[$ioidx]==$profile_params->table){
													$query .= ' JOIN '.$db->quoteName($filterField[1])." ON ".$db->quoteName($profile_params->table).".".$db->quoteName($profile_params->joins->column1[$ioidx])."=".$db->quoteName($filterField[1]).".".$db->quoteName($profile_params->joins->column2[$ioidx]);
													$applyFilter[$j] = true;
													array_push($filterTables, $iotable);
												}
												else{
													$tempIdx = $ioidx;
													$left = $profile_params->joins->table1[$ioidx];
													$joinPath = array($ioidx);
													while($tempIdx>=0){
														$tempIdx--;
														if($left==$profile_params->joins->table2[$tempIdx]){
															array_unshift($joinPath, $tempIdx);
															if($profile_params->joins->table1[$tempIdx]==$profile_params->table){
																break;
															}
														}
													}
													foreach($joinPath as $joinIdx){
														if(!in_array($profile_params->joins->table2[$joinIdx], $filterTables)){
															$query .= ' JOIN '.$db->quoteName($profile_params->joins->table2[$joinIdx]).' ON '.$db->quoteName($profile_params->joins->table1[$joinIdx]).'.'.$db->quoteName($profile_params->joins->column1[$joinIdx]).'='.$db->quoteName($profile_params->joins->table2[$joinIdx]).'.'.$db->quoteName($profile_params->joins->column2[$joinIdx]);
															array_push($filterTables, $profile_params->joins->table2[$joinIdx]);
															$applyFilter[$j] = true;
														}
													}
												}
											}
											
										}
									}
									if(!in_array($filterField[2], $filterTables)){
										$tableIndex = array_search($filterField[1], $profile_params->joins->table2);
										foreach($profile_params->joins->columns[$tableIndex] as $ccol=>$cval){
											if($cval->data=='reference' && $cval->table==$filterField[2]){
												$query .= ' JOIN '.$db->quoteName($filterField[2]).' ON '.$db->quoteName($filterField[1]).'.'.$db->quoteName($ccol).'='.$db->quoteName($filterField[2]).'.'.$db->quoteName($cval->on);
												
												array_push($filterTables, $filterField[2]);
												$applyFilter[$j] = true;
												break;
											}
										}
										
									}
									if( in_array($filterField[1], $filterTables) && in_array($filterField[2], $filterTables) ){
										$applyFilter[$j] = true;
									}
									
								}
							}
						}
						
						$query .= " WHERE ";
						$m = $n = -1;
						foreach($profile_params->filters->column as $j=>$column){

							$filterField = explode('.', $column);
							$filterFieldPath = count($filterField);
							//base table reference filter
							if( ($filterFieldPath==2) && ($applyFilter[$j]) ){
								$query .= $db->quoteName($filterField[0]).'.'.$db->quoteName($filterField[$filterFieldPath-1])." ";
							}
							//child table filter
							elseif( ($filterFieldPath==3) && ($applyFilter[$j]) ){
								$query .= $db->quoteName($filterField[1]).'.'.$db->quoteName($filterField[$filterFieldPath-1])." ";
							}
							//child table reference filter
							elseif( ($filterFieldPath==4) && ($applyFilter[$j]) ){
								$query .= $db->quoteName($filterField[2]).'.'.$db->quoteName($filterField[$filterFieldPath-1])." ";
							}
							//base table filters
							else{
								$query .= $db->quoteName($profile_params->table).'.'.$db->quoteName($column)." ";
							}
							
							if($profile_params->filters->cond[$j]=='between' || $profile_params->filters->cond[$j]=='notbetween'){
								$n = ($n<0)?0:$n+1;
							}
							else{
								$m = ($m<0)?0:$m+1;
								$value = $this->getQueryFilteredValue($profile_params->filters->value[$m]);
							}
						
							if($profile_params->filters->cond[$j]=='in'){
								$query .= " IN ( ".$db->quote($value)." )";
							}
							elseif($profile_params->filters->cond[$j]=='notin'){
								// $query .= " NOT IN ( ".$db->quote($value)." )";
								$query .= " NOT IN ( ".$value." )";
							}
							elseif($profile_params->filters->cond[$j]=='between'){
								$value1 = $this->getQueryFilteredValue($profile_params->filters->value1[$n]);
								$value2 = $this->getQueryFilteredValue($profile_params->filters->value2[$n]);
								$query .= " BETWEEN ".$db->quote($value1)." AND ".$db->quote($value2);
							}
							elseif($profile_params->filters->cond[$j]=='notbetween'){
								$value1 = $this->getQueryFilteredValue($profile_params->filters->value1[$n]);
								$value2 = $this->getQueryFilteredValue($profile_params->filters->value2[$n]);
								$query .= " NOT BETWEEN ".$db->quote($value1)." AND ".$db->quote($value2);
							}
							elseif($profile_params->filters->cond[$j]=='like'){
								$query .= " LIKE ".$db->quote($value);
							}
							elseif($profile_params->filters->cond[$j]=='notlike'){
								$query .= " NOT LIKE ".$db->quote($value);
							}
							elseif($profile_params->filters->cond[$j]=='regexp'){
								$query .= " REGEXP ".$db->quote($profile_params->filters->value[$j]);
							}
							else{
								$query .= $profile_params->filters->cond[$j]." ".$db->quote($value);
							}
							
							if($j < (count($profile_params->filters->column)-1)){
								$query .= " ".$profile_params->filters->op." ";
							}
						}
					}
					
					//dynamic filter
					if(count($keys)>0 && count($cols)>0 &&  !empty($profile_params->filters) && property_exists($profile_params->filters, 'column') ){
						if(count($keys)==1 && count($cols)>1){
								for($id_replace_column=0;$id_replace_column<count($cols);$id_replace_column++){
							if(in_array($keys[0], $total_table_column)){
									
								$query .= " OR ".$db->quoteName($keys[0])." = ".$db->quote(addslashes($cols[$id_replace_column]));
							}
						}
						}
						else{
						for($id_replace_column=0;$id_replace_column<count($keys)&&$id_replace_column<count($cols);$id_replace_column++){
							if(in_array($keys[$id_replace_column], $total_table_column)){
									
								$query .= " AND ".$db->quoteName($keys[$id_replace_column])." = ".$db->quote(addslashes($cols[$id_replace_column]));
							}
						}
						}
					}
					elseif(count($keys)>0 && count($cols)>0){
						 $addwhere = 1;
						if(count($keys)==1 && count($cols)>1){
							
								for($id_replace_column=0;$id_replace_column<count($cols);$id_replace_column++){
							if(in_array($keys[0], $total_table_column)){
								$query .= $addwhere == 1? " where ":" OR ";	$addwhere=2;	
								$query .= $db->quoteName($keys[0])." = ".$db->quote(addslashes($cols[$id_replace_column]));
							}
						}
						}
						else{
						for($id_replace_column=0;$id_replace_column<count($keys)&&$id_replace_column<count($cols);$id_replace_column++){
							
							if(in_array(trim($keys[$id_replace_column]), $total_table_column)){
								$query .= $addwhere == 1? " where ":" AND ";	$addwhere=2;
								$query .= $db->quoteName($keys[$id_replace_column])." = ".$db->quote(($cols[$id_replace_column])); 
							}	
						
						}				 
						}
					}
					
					if(!empty($profile_params->groupby)){
						$query .= " GROUP BY ".$profile_params->table.".".$dbc->quoteName($profile_params->groupby);
					}
					if(!empty($profile_params->orderby)){
						$query .= " ORDER BY ".$profile_params->table.".".$dbc->quoteName($profile_params->orderby)." ".$profile_params->orderdir;
					}
					
					$dbc->setQuery($query, $limitstart, $limit);
					
				}
			}
			if($op=='array')
				$rows = $dbc->loadAssocList();
			else
				$rows = $dbc->loadObjectList();
			
			return $rows;
		}
		
		function sendFile($emails=array(), $cc=array(), $bcc=array(), $subject='', $body='', $attachment=''){
			if(empty($emails)){
				return false;
			}
			$mailer = JFactory::getMailer();
			
			//set sender
			$config = JFactory::getConfig();
			$sender = array(
				$config->get( 'mailfrom' ),
				$config->get( 'fromname' ) 
			);
			$mailer->setSender($sender);

			//set subject
			$mailer->setSubject($subject);
			$mailer->setBody($body);
			$mailer->isHTML();
			$mailer->Encoding = 'base64';
			//file attachment
			if(!empty($attachment)){
				$mailer->addAttachment($attachment);
			}
			//set recipients
			$mailer->addRecipient($emails);
			
			$send = $mailer->Send();
			
			if($send !== true){
				JLog::add(JText::sprintf('VDATA_EMAIL_EXPORT_FILE_FAIL', $send->__toString()), JLog::INFO, 'com_vdata');
				return false;
			}
			
			JLog::add(JText::sprintf('VDATA_EMAIL_EXPORT_FILE_SUCCESS', implode(',',$emails)), JLog::INFO, 'com_vdata');
			return true;
		}
		
		function ftpConnection($inputFtp) {
			// require_once JPATH_ADMINISTRATOR . '/components/com_vdata/classes/ftp.class.php';
			JLoader::register('VDataFtpHelper', JPATH_ADMINISTRATOR . '/components/com_vdata/classes/ftp.class.php');
			
			$obj = new stdClass();
			try{
				$ftp = new VDataFtpHelper();
			}
			catch(Exception $e){
				$obj->result = 'error';
				$obj->error = $e->getMessage();
				return $obj;
			}
			
			if( !$ftp->connect($inputFtp['ftp_host'], $inputFtp['ftp_port'], $inputFtp['ftp_user'], $inputFtp['ftp_pass']) ){
				$obj->result = 'error';
				$obj->error = $ftp->getMessage();
				return $obj;
			}
			$listDir = empty($inputFtp['ftp_directory'])?".":$inputFtp['ftp_directory'];
			$list = $ftp->getList($listDir); 
			if(!is_array($list)){
				$obj->result = 'error';
				$obj->error = JText::_('VDATA_FTP_CONNECTION_ERROR');
				return $obj;
			}
			$obj->result = 'success';
			$obj->ftpHelper = $ftp;
			return $obj;
		}
		
		function uploadToFtp($ftpInput=array(), $filepath){
			
			$ftp = $this->ftpConnection($ftpInput);
			if($ftp->result=='error'){
				return false;
			}
			$ftpHelper = $ftp->ftpHelper;
			// $serverPath = $ftpInput['ftp_directory'].''.$ftpInput['ftp_file'];
			$serverPath = rtrim($ftpInput['ftp_directory'], '/').'/'.$ftpInput['ftp_file'];
			
			if(!$ftpHelper->upload($serverPath, $filepath)){
				return false;
			}
			return true;
		}
		
		function uploadImage($filename,$destination='',$source='',$path_type='',&$err){
			//echo 'filename : '.$filename .' destination: '.$destination,' source: '.$source.' path: '.$path_type;jexit();
			$err = '';
			$destination = JPATH_ROOT . '/'.ltrim($destination,'/'); 
			$ext = strtolower(strrchr($filename, '.'));
			$done=false;

			$allowed_image_ext = array('jpeg','png','jpg');
			if(!in_array(substr($ext,1),$allowed_image_ext)){
				$err = JText::_('ALLOWED_IMAGE_EXTENSION')." " . implode(", ", $allowed_image_ext);
				return false;
			}
			
			if($path_type=="directory"){
				$source = rtrim(rtrim($source,'/'),'\\').'/'.$filename;
				if($source[0]=='/' || $source[0]=='\\')$source = substr($source, 1);
				
				$absolute_path = JPATH_ROOT .'/'. ltrim($source,'/');
				if (!file_exists(dirname($absolute_path))) {
					$err = JText::_('IMAGE_NOT_EXIST') .' '.$absolute_path ;
					return false;
				}
				if(!JFile::copy($absolute_path, $destination)){
					$err = JText::_('UNABLE_TO_UPLOAD_IMAGE_FROM') .' PATH '.$absolute_path ;
					return false;
				}else{
					$done=true;
				}		
			}elseif($path_type=="ftp"){
				$ftpInput = array();
				$temp = explode('|',$source);
				foreach($temp as $arr){
					$temp2='';
					$temp2 = explode(':',$arr);
					if($temp2[0]=='ftp_host'){
						$ftpInput['ftp_host']=$temp2[1];
					}elseif($temp2[0]=='ftp_port'){
						$ftpInput['ftp_port']=$temp2[1];
					}elseif($temp2[0]=='ftp_user'){
						$ftpInput['ftp_user']=$temp2[1];
					}elseif($temp2[0]=='ftp_pass'){
						$ftpInput['ftp_pass']=$temp2[1];
					}elseif($temp2[0]=='ftp_directory'){
						$ftpInput['ftp_directory']=$temp2[1];
					}
				}
				$ftpInput['ftp_file']=$filename;
				if( empty($ftpInput['ftp_host']) || empty($ftpInput['ftp_user']) || empty($ftpInput['ftp_pass']) || empty($ftpInput['ftp_file']) ){
						$err = JText::_('VDATA_EMPTY_FTP_CREDENTIALS');
						return false;
					}
					if(isset($this->ftpConnect)){
						$ftpHelper = $this->ftpConnect;
					}else{	
						$ftp = $this->ftpConnection($ftpInput);
						if($ftp->result=='error'){
							$err = $ftp->error;
							return false;
						}
						$this->ftpConnect = $ftpHelper = $ftp->ftpHelper;
					}
					$remotePath = rtrim($ftpInput['ftp_directory'], '/').'/'.$filename;
					if(!$ftpHelper->download($destination, $remotePath,$allowed_image_ext,FTP_BINARY)){
						$err = $ftpHelper->getMessage();
						return false;
					}else{
						$done=true;
					}
				
			}else{
				if($source!=null)
					$source = rtrim($source,'/').'/'.$filename;
				else
					$source = $filename;
				$image = @file_get_contents($source);
				if($image!==false)
					$upload = file_put_contents($destination, $image);
				if(empty($upload)){
					$err = JText::_('UNABLE_TO_UPLOAD_IMAGE_FROM').' URL '.$source;
					$err .='  Error:'.error_get_last()['message'];
					return false;
				}else{
					$done = true;
				}				
			}
			if(!$done){
				$err = JText::_('UNABLE_TO_UPLOAD_IMAGE_FROM') .' URL '.$source ;
				return false;
			}
			
			return true;
			
		} 
		
	}