<?php 
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    WDM Team
# copyright Copyright (C) 2014 wwww.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech..com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.model' );

class VdataModelWidget extends VDModel
{
	
	var $_data;
	var $_list;
	var $_total;
	
	
	function __construct()
	{
		parent::__construct();
      
		$mainframe = JFactory::getApplication(); 
		$option    = JFactory::getApplication()->input->getCmd('option'); 
		$controller = JFactory::getApplication()->input->getWord('controller');
        $context			= 'com_vdata.widget.list.'; 
		$array = JFactory::getApplication()->input->get('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
			
		 
	}

	function setId($id)
	{
		
		$this->_id		= $id;
		$this->_data	= null;
	}

	
	
	function getItem()
	{
		// Lets load the data if it doesn't already exist
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM #__vd_widget'.
					'  WHERE id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = '';
			$this->_data->name = '';
			$this->_data->data = '';
			$this->_data->chart_type = '';
			$this->_data->datatype_option = 'predefined';
			$this->_data->detail = '';
			$this->_data->created_time = date('Y-m-d');
			$this->_data->userid = JFactory::getUser()->id;
			$this->_data->ordering = '';
			
			
			}

		return $this->_data;
	   
	
	}
	
	public function getTable($type = 'widget', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	function store()
	{
		$row = $this->getTable('widget', 'Table');
		//print_r($row); jexit();
        jimport('joomla.filesystem.file');
		$data = JFactory::getApplication()->input->post->getArray();
        $mainframe = JFactory::getApplication();
		$db		=  JFactory::getDBO();
        $data = JFactory::getApplication()->input->post->getArray();
		
		if(isset($data['chart_type'])){
		switch($data['chart_type']){
			case JText::_('Line Chart'):
			case JText::_('Combo Chart'):
			case JText::_('Area Chart'):
			case JText::_('Stepped AreaChart'):
			case JText::_('Pie Chart'):
			case JText::_('Slice Pie Chart'):
			case JText::_('Bar Chart'):
			case JText::_('Column Chart'):
			case JText::_('Geo Charts'):
			case JText::_('Maps'):
			case JText::_('Table'):
		
		 break;
		 
			}
			}
		 if(isset($upload_file)&& $upload_file!=''){
		  $chart_option['selected_marker_path']= $upload_file;}
		 
		 if(isset($normal_file)&& $normal_file!='')
		 $chart_option['normal_marker_path']= $normal_file;
	    $date =JFactory::getDate();
		$user = JFactory::getUser();
		$data['create_time'] = $date->toSQL();
		$data['userid'] = $user->id;
		$params = JFactory::getApplication()->input->get('params', array(), 'RAW');
		 $data['detail'] = json_encode($params);
		 	
		
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return 0;
		}

		
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return 0;
		}
		
		
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg() );
			return 0;
		}
      
	 return $row->id;
	}

	function getReferColumns()
	{
		
		$obj = new stdClass();
		$obj->result = 'error';
		
		$table = JFactory::getApplication()->input->get('table', '');
		$column = JFactory::getApplication()->input->get('column', '');
		$id = JFactory::getApplication()->input->getInt('id', 0);
		
		if(empty($column))	{
			$obj->error = JText::_('PLZ_SEL_COLUMN_FIRST');
			return $obj;
		}
		
		$query = 'show fields FROM '.$table;
		$this->_db->setQuery( $query );
		
		if($this->_db->getErrorNum())	{
			$obj->error = $this->_db->getErrorMsg();
			return $obj;
		}
		
		$columns = $this->_db->loadObjectList();
		
		$query = 'select title, params from #__vd_profile_field where '.$this->_db->quoteName('profileid').' = '.$id.' and '.$this->_db->quoteName('column').' = '.$this->_db->quote($column);
		$this->_db->setQuery( $query );
		$item = $this->_db->loadObject();
		
		
		$params = new stdClass();
		
		$obj->html = ' <span class="label">'.JText::_('REFERENCED_COLUMN').'</span> <select name="params['.$column.'][reftext]">';
		
		for($i=0;$i<count($columns);$i++)	{
			$obj->html .= '<option value="'.$columns[$i]->Field.'"';
			if(isset($params->reftext) and $columns[$i]->Field==$params->reftext)	{
				$obj->html .= ' selected="selected"';
			}
			$obj->html .= '>'.$columns[$i]->Field.'</option>';
		}
		
		$obj->html .= '</select>';
		
		$obj->result = 'success';
		
		return $obj;
		
	}
	
   function data_for_query()
      {
	    $obj = new stdClass();
			$obj->result = 'error';
			$id = JFactory::getApplication()->input->get('id', 0);
			$chart_type = JFactory::getApplication()->input->get('chart_type', '');
			$data_base_options = JFactory::getApplication()->input->get('data_base_options', '');
       		
			if($id>0){
			$chart_details = $this->getChart($id);
			$chart_details_option = json_decode($chart_details->detail);
			}
			else
			$chart_details = $items = array();
		
			try
			{
			  if($data_base_options=='predefined')
			  {
				$db     = JFactory::getDbo();
				$query  = $db->getQuery(true);
				$result = array();

				$items =  array();
				}
				elseif($data_base_options=='writequery'){
				$query = 'show tables';
				$items = $this->_db->setQuery($query)->loadObjectList();
				}
				
			}
			catch (RuntimeException $e)
			{  
			$obj->result = 'error';
			$obj->error = $e->getMessage();
			return $obj;
			
			}
			
			$obj->formating = '';
			$obj->html = '<table class="local_database_table">';
			$obj->htmls ='';
			
			if($data_base_options=='writequery')
			{
				$obj->html .=  '<tr><td width="200"><label id="ordering-lbl" class="hasTip" title="'.JText::_('COM_VDATA_WIDGET_NAME_DESC').'" for="ordering">'.JText::_('COM_VDATA_WIDGET_NAME').'</label></td><td><input class="chartfield" id="ordering" type="text" value="';
				 $obj->html .= isset($chart_details->name)? $chart_details->name:'';
				
				$obj->html .=  '" name="name" /></td></tr>';
				$obj->html .=  '<tr><td width="200"><label id="user_write_query_value-lbl" class="hasTip" title="'.JText::_('COM_VDATA_WIDGET_CUSTOM_WRITE_QUERY_DESC').'" for="user_write_query_value">'.JText::_('COM_VDATA_WIDGET_CUSTOM_QUERY_BY_USER').'</label></td><td><textarea rows="4" cols="30" id="user_write_query_value" name="params[user_write_query_value]">';
                $obj->html .= isset($chart_details_option->user_write_query_value) && $chart_details_option->user_write_query_value!=''? $chart_details_option->user_write_query_value:'';
				
			    $obj->html .=  '</textarea></td></tr><tr><td>&nbsp;</td><td>'.JText::_('COM_VDATA_OR').'</td></tr>';
				
				$obj->html .= '<tr><td><label id="existing_database_table-lbl" class="hasTip" title="'.JText::_("COM_VDATA_WIDGET_SELECT_TABLE_NAME_DESC").'" for="existing_database_table"  aria-invalid="false">'.JText::_("COM_VDATA_WIDGET_SELECT_TABLE_NAME").'</label></td>
				<td><select class="required svminput selectoptions_class" name="params[existing_database_table]" id="existing_database_table" onchange="load_change_function(this);" >';
				$obj->html .= '<option value="">Select Table</option>';
				$database_name = JFactory::getConfig();
				$database = 'Tables_in_'.$database_name->get('db');
					
					foreach($items as $key => $value)
					{
					$table = str_replace($this->_db->getPrefix(), '#__', $value->$database);
					$select ='';
						if(isset($chart_details_option->existing_database_table) && $chart_details_option->existing_database_table!='' && $chart_details_option->existing_database_table==$table)
						$select ='selected="selected"';	
					$obj->html .= '<option value="'.$table.'"'.$select.'>'.$table.'</option>';
					
					 }	
				$obj->html .= '</select></td></tr>';
			    
				  
				
				$obj->html .=  '<tr><td><label id="ordering-lbl" class="hasTip" title="'.JText::_('COM_VDATA_WIDGET_ORDERING_DESC').'" for="ordering">'.JText::_('COM_VDATA_WIDGET_ORDERING').'<font color="#FF0000">*</font></label></td><td><input class="chartfield required" id="ordering" type="text" value="';
				$obj->html .= isset($chart_details->ordering)? $chart_details->ordering:0;
			
				$obj->html .=  '" name="ordering" /></td></tr>'; 
				
               $obj->html .=  '<tr><td><label id="display_widget_layout-lbl" class="hasTip" title="'.JText::_('COM_VDATA_WIDGET_DISPLAY_LAYOUT_COLUMN_DESC').'" for="display_widget_layout">'.JText::_('COM_VDATA_WIDGET_DISPLAY_LAYOUT_COLUMN').'<font color="#FF0000"> *</font></label></td><td><span class="box_layout_style"><input class="chartfield required" required="required" id="display_widget_layout" type="number" min="1" max="'.(isset($configuartion->column_limit)&& $configuartion->column_limit>0?$configuartion->column_limit:10).'" value="';
			  
			   if(isset($chart_details_option->box_column) && $chart_details_option->box_column!='')
			    $obj->html .= $chart_details_option->box_column;
				
			    $obj->html .=  '" name="params[box_column]" /></span></td></tr>';
				
				 $obj->html .=  '<tr><td><label id="display_widget_layout-lbl" class="hasTip" title="'.JText::_('COM_VDATA_WIDGET_DISPLAY_LAYOUT_ROW_DESC').'" for="display_widget_layout">'.JText::_('COM_VDATA_WIDGET_DISPLAY_LAYOUT_ROW').'<font color="#FF0000"> *</font></label></td><td><span class="box_layout_style"><input class="chartfield required" required="required" id="display_widget_layout" type="number" min="1" value="';
			  
			   if(isset($chart_details_option->box_row) && $chart_details_option->box_row!='')
			    $obj->html .= $chart_details_option->box_row;
				
			    $obj->html .=  '" name="params[box_row]" /></span></td></tr>';
				
				
				$obj->html .=  '<tr><td><label id="style-lbl" class="hasTip" title="'.JText::_('COM_VDATA_WIDGET_LAYOUT_STYLE_DESC').'" for="style">'.JText::_('COM_VDATA_WIDGET_LAYOUT_STYLE').'<font color="#FF0000"> *</font></label></td><td><span class="display_layout_style"><input class="style_common chartfield required style_charting_formate" required="required" onclick="extra_formating_style(this);" id="style" type="radio" value="charting_formate"'; 
			 
			    $obj->html .= isset($chart_details_option->style_layout) && $chart_details_option->style_layout=="charting_formate" ? ' checked="checked"':'';
				
			    $obj->html .=  ' name="params[style_layout]" />'.JText::_('COM_VDATA_WIDGET_DISPLAY_LAYOUT_CHARTING').'</span><span class="box_layout_style"><input class="style_common chartfield required style_listing_formate" required="required" id="style" onclick="extra_formating_style(this);" type="radio" value="listing_formate"'; 
				$obj->html .= isset($chart_details_option->style_layout) && $chart_details_option->style_layout=="listing_formate" ? ' checked="checked"':'';
                $obj->html .=  ' name="params[style_layout]" />'.JText::_('COM_VDATA_WIDGET_DISPLAY_LAYOUT_LISTING').'</span><span class="box_layout_style"><input class="style_common chartfield required style_single_formate" required="required" onclick="extra_formating_style(this);" id="style" type="radio" value="single_formate"';	
              	$obj->html .= isset($chart_details_option->style_layout) && $chart_details_option->style_layout=="single_formate" ? ' checked="checked"':'';
                $obj->html .=  ' name="params[style_layout]" />'.JText::_('COM_VDATA_WIDGET_DISPLAY_LAYOUT_SINGLE').'</span></td></tr>';
			}
			
			
			if($data_base_options=='predefined')
			{
				
				 $obj->html .=  '<tr><td width="200"><label id="widget_name-lbl" class="hasTip" title="'.JText::_('COM_VDATA_WIDGET_NAME_DESC').'" for="widget_name">'.JText::_('COM_VDATA_WIDGET_NAME').'</label></td><td><input class="chartfield" id="widget_name" type="text" value="';
				 $obj->html .= isset($chart_details->name)? $chart_details->name:'';
				
				$obj->html .=  '" name="name" /></td></tr>'; 				
				
				$obj->html .=  '<tr><td width="200"></td>&nbsp;<td><input type="hidden" id="descriptin_widget" name="params[descriptin_widget]" value="';
			              $style = '';
						 
						  if(isset($chart_details_option->descriptin_widget) && $chart_details_option->descriptin_widget!=''){
						 $obj->html .= $chart_details_option->descriptin_widget;
                            $style =	' style="display:block"';					 
						  }else{
							$obj->html .= ''; 
							$style =	' style="display:none"'; 
						  }
				 
				
			    $obj->html .=  '" name="params[descriptin_widget]" ></td></tr>'; 
				$obj->html .=  '<tr><td><label id="existing_database_table-lbl" class="hasTip" title="'.JText::_('COM_VDATA_WIDGET_PREDEFINED_QUERY_SELECT_DESC').'" for="existing_database_table"  aria-invalid="false">'.JText::_("COM_VDATA_WIDGET_PREDEFINED_QUERY_SELECT").'</label></td><td><span class="testing_record"></span><input class="chartfield required autocomplete_field" onchange="update_autoselect_field();" id="existing_database_table" style="display:block;" type="text" value="';
			 
				 $obj->html .= isset($chart_details_option->existing_database_table) && $chart_details_option->existing_database_table!=''&& $chart_details->datatype_option=='predefined'? $chart_details_option->existing_database_table:'';
				
			    $obj->html .=  '" name="params[existing_database_table]" /></td></tr>';  
				$obj->html .=  '<tr><td width="200"></td>&nbsp;<td><textarea rows="4" cols="30" id="existing_database_table" name="params[remote_query_value]"  readonly="readonly">'; 
			 
				 $obj->html .= isset($chart_details_option->remote_query_value) && $chart_details_option->remote_query_value!=''? $chart_details_option->remote_query_value:'';
				
			    $obj->html .=  '</textarea></td></tr>';
				
                $obj->html .=  '<tr><td><label id="ordering-lbl" class="hasTip" title="'.JText::_('COM_VDATA_WIDGET_ORDERING_DESC').'" for="ordering">'.JText::_('COM_VDATA_WIDGET_ORDERING').'<font color="#FF0000">*</font></label></td><td><input class="chartfield required" id="ordering" type="text" value="';
			 
				  $obj->html .= isset($chart_details->ordering)? $chart_details->ordering:0;
				
			    $obj->html .=  '" name="ordering" /></td></tr>'; 		   
				$obj->html .=  '<tr><td><label id="display_widget_layout-lbl" class="hasTip" title="'.JText::_('COM_VDATA_WIDGET_DISPLAY_LAYOUT_COLUMN_DESC').'" for="display_widget_layout">'.JText::_('COM_VDATA_WIDGET_DISPLAY_LAYOUT_COLUMN').'<font color="#FF0000"> *</font></label></td><td><span class="box_layout_style"><input class="chartfield required" required="required" id="display_widget_layout" type="number" min="1" max="'.(isset($configuartion->column_limit)&& $configuartion->column_limit>0?$configuartion->column_limit:10).'" value="';
			  
			   if(isset($chart_details_option->box_column) && $chart_details_option->box_column!='')
			    $obj->html .= $chart_details_option->box_column;
				
			    $obj->html .=  '" name="params[box_column]" /></span></td></tr>';
				
				 $obj->html .=  '<tr><td><label id="display_widget_layout-lbl" class="hasTip" title="'.JText::_('COM_VDATA_WIDGET_DISPLAY_LAYOUT_ROW_DESC').'" for="display_widget_layout">'.JText::_('COM_VDATA_WIDGET_DISPLAY_LAYOUT_ROW').'<font color="#FF0000"> *</font></label></td><td><span class="box_layout_style"><input class="chartfield required" required="required" id="display_widget_layout" type="number" min="1" value="';
			  
			   if(isset($chart_details_option->box_row) && $chart_details_option->box_row!='')
			    $obj->html .= $chart_details_option->box_row;
				
			    $obj->html .=  '" name="params[box_row]" /></span></td></tr>';
				
				
				$obj->html .=  '<tr><td><label id="style-lbl" class="hasTip" title="'.JText::_('COM_VDATA_WIDGET_LAYOUT_STYLE_DESC').'" for="style">'.JText::_('COM_VDATA_WIDGET_LAYOUT_STYLE').'<font color="#FF0000"> *</font></label></td><td><span class="display_layout_style"><input class="style_common chartfield required style_charting_formate" required="required" onclick="extra_formating_style(this);" id="style" type="radio" value="charting_formate"'; 
			 
			    $obj->html .= isset($chart_details_option->style_layout) && $chart_details_option->style_layout=="charting_formate" ? ' checked="checked"':'';
				
			    $obj->html .=  ' name="params[style_layout]" />'.JText::_('COM_VDATA_WIDGET_DISPLAY_LAYOUT_CHARTING').'</span><span class="box_layout_style"><input class="style_common chartfield required style_listing_formate" required="required" id="style" onclick="extra_formating_style(this);" type="radio" value="listing_formate"'; 
				$obj->html .= isset($chart_details_option->style_layout) && $chart_details_option->style_layout=="listing_formate" ? ' checked="checked"':'';
                $obj->html .=  ' name="params[style_layout]" />'.JText::_('COM_VDATA_WIDGET_DISPLAY_LAYOUT_LISTING').'</span><span class="box_layout_style"><input class="style_common chartfield required style_single_formate" required="required" onclick="extra_formating_style(this);" id="style" type="radio" value="single_formate"';	
              	$obj->html .= isset($chart_details_option->style_layout) && $chart_details_option->style_layout=="single_formate" ? ' checked="checked"':'';
                $obj->html .=  ' name="params[style_layout]" />'.JText::_('COM_VDATA_WIDGET_DISPLAY_LAYOUT_SINGLE').'</span></td></tr>';			
				
			   }
			
				$obj->html .= '</table><table class="extra_condition_table"></table>'; 
				$obj->html .='';
				
		  
	            $obj->result = 'success';
		
		     return $obj;
		
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
	  function legends($legend_val,$label,$name)
	  {
		
		$legend_vals = array('none','in','right','bottom','top');
		$legend_label = array(JText::_('COM_VDATA_LEGEND_POSITION_HIDE'),JText::_('COM_VDATA_LEGEND_POSITION_INSIDE'),JText::_('COM_VDATA_LEGEND_POSITION_ATRIGHT'),JText::_('COM_VDATA_LEGEND_POSITION_ATBOTTOM'),JText::_('COM_VDATA_LEGEND_POSITION_ATTOP'));
		$option_field='';
		 $option_field .= '<td width="250"><label class="hasTipformat" Title="'.JText::_('COM_VCHART_LEGEND_POSITION_DESC').'">'.$label.'</label></td><td><select name="'.$name.'" id="legend_position" class="inputbox selectoptions_class" size="1">';
		 for($i=0;$i<count($legend_vals);$i++){
			 $selected = '';
			  if(isset($legend_val) && $legend_val!='' && $legend_val ==$legend_vals[$i])
			  $selected ='selected="selected"';
			 
			 $option_field .='<option value="'.$legend_vals[$i].'" '.$selected.' >'.$legend_label[$i].'</option>';
			 
			 }
		  $option_field .='</select></td>';
		  
		  return $option_field;
		  }
	static function chart_list($name, $current_value, &$items, $first = 0, $extra='',$text)
      {       
     if($text!='')
	$html = "\n".'<td><label id="'.$name.'-msg" class="hasTiprefence" title="'.JText::_("COM_VDATA_CHART_TYPE_TO_SHOW").'" for="'.$name.'">'.JText::_($text).'</label></td><td><select name="'.$name.'" id="'.$name.'" class="selectoptions_class" size="1" '.$extra.'>';
	if($text=='')
	$html = "\n".'<span class="label">'.JText::_($text).'</span><select name="'.$name.'" id="'.$name.'" class="selectoptions_class" size="1" '.$extra.'>';
	if ($items == '')
		return '';
	$html .= "\n".'<option value="">Select Chart Type:</option>';
	foreach ($items as $key => $value)
		{
		if (strncmp($key,"OPTGROUP_START",14) == 0)
			{
			$html .= "\n".'<optgroup label="'.$value.'">';
			continue;
			}
		if (strncmp($key,"OPTGROUP_END",12) == 0)
			{
			$html .= "\n".'</optgroup>';
			continue;
			}
		if ($key < $first)					// skip unwanted entries
			continue;
		$selected = '';
		if ($current_value == $key){
			$selected = ' selected="selected"'; }
		$html .= "\n".'<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		}
	
	 if($text!='')
	 $html .= '</select></td>';
	 if($text=='')
	 $html .= '</select>';
	
	  return $html;
   }  
     function chart_type()

     {   
	
	    $this->chart_types = array(
		
		JText::_('CHART_TYPE_LINE')=> JText::_('CHART_TYPE_LINE'),
		JText::_('CHART_TYPE_AREA') => JText::_('CHART_TYPE_AREA'),
		JText::_('Stepped AreaChart') => JText::_('Stepped Area Chart'),
		JText::_('COLUMN_CHART')=>JText::_('COLUMN_CHART'),
		JText::_('CHART_TYPE_BAR') => JText::_('CHART_TYPE_BAR'),
		JText::_('GEOCHART')=>JText::_('GEOCHART'),
		JText::_('CHART_TYPE_PL_TABLE')    => JText::_('CHART_TYPE_PL_TABLE'),
	    JText::_('CHART_TYPE_PIE_2D')      => JText::_('CHART_TYPE_PIE_2D'));
		return $this->chart_types;
	
     }
	 
	 function pieHole($piehole_val,$label,$name){ 
		 
			$option_field ='';$k=0.1;
			$option_field .= '<td><label class="hasTipformat" for="pie_chart_piehole" id="pie_chart_piehole-lbl" title="'.JText::_('COM_VDATA_PIECHART_HOLE_DESC').'">'.$label.'</label></td><td><select class="selectoptions_class" name="'.$name.'" id="pie_chart_piehole" class="inputbox" size="1">';
			$option_field .='<option value="">Select pieHole</option>';
				for($i=0.1;$i<1.0;$i=$i+$k){
				
				$selected = '';
				if(isset($piehole_val) && $piehole_val!='' && $piehole_val==round($i,1))
				$selected ='selected="selected"';
				
				$option_field .='<option value="'.round($i,1).'" '.$selected.' >'.round($i,1).'</option>';

				
				}
			$option_field .='</select></td>';
			
			return $option_field;
		  } 
	function table_reference_options()
	 {
	    $obj = new stdClass();
		 $obj->result = 'error';
         $id = JFactory::getApplication()->input->getInt('id', 0);
		
		 $table_name = JFactory::getApplication()->input->get('table_name', '', 'RAW');
		 $widget_type = JFactory::getApplication()->input->get('widget_type', '', 'RAW');
		 $user_write_query_value = JFactory::getApplication()->input->get('user_write_query_value', 1);
		 $style_layout = JFactory::getApplication()->input->get('style_layout', '', 'RAW');
         $config = JFactory::getConfig();
		if($table_name==''){
			$obj->html = '';
			$obj->result = 'success';
			return $obj;
			}
		if($id>0){
		$chart_details = $this->getChart($id);
		$chart_details_option = json_decode($chart_details->detail);}
		else
		$chart_details=array();	
	     $obj->html = '';
	    $live_data_query = array('Server Response Monitoring','Server CPU Monitoring','Server Monitoring','Thread Status','vData Profiles','vData Plugins');
		$query = $table_name;
		if($widget_type=='predefined' && !in_array($table_name, $live_data_query)){
		try
			{
			    preg_match_all('/{tablename\s(.*?)}/i', $table_name, $matches);
                preg_match_all('/{as\s(.*?)}/i', $table_name, $match);
				$matches_s = $matches[1];
				$text = $table_name;
				for($r=0;$r<count($matches_s);$r++){
				$text = preg_replace('{tablename '.$matches_s[$r].'}', $matches_s[$r], $text, 1);	
				}
				$query = str_replace('{','',str_replace('}','',$text));
				$query = str_replace('informationschema','information_schema',$query);
				$query = str_replace('databasename',$config->get( 'db' ),$query);
				
		        $test = $this->_db->setQuery( $query )->loadObjectList();
			
			}
			catch (RuntimeException $e)
			{  
			$obj->result = 'error';
			$obj->error = $e->getMessage();
			return $obj;
			
			}	
		}
		if($widget_type=='writequery' && $user_write_query_value!=1){
		try
			{
			$test = $this->_db->setQuery( $query )->loadObjectList();
			}
			catch (RuntimeException $e)
			{  
			$obj->result = 'error';
			$obj->error = $e->getMessage();
			return $obj;
			
			}	
			
		}
		if($widget_type=='writequery' && $user_write_query_value==1){
		try
			{
			$test = $this->_db->setQuery( 'show fields FROM '.$query )->loadObjectList();
			}
			catch (RuntimeException $e)
			{  
			$obj->result = 'error';
			$obj->error = $e->getMessage();
			return $obj;
			
			}	
			
		}
		if($table_name=='vData Profiles'){
		$query = 'select i.id, i.title, e.name as plugin, e.element, concat("plg_", e.folder, "_", e.element) as extension FROM #__vd_profiles as i left join #__extensions as e on (e.extension_id=i.pluginid and e.enabled=1)';
		$profiels = $this->_db->setQuery($query)->loadObjectList();	
		$obj->html .= '<tr class="extra_condition_field vdata_profiles"><td width="200"><label id="existing_database_table_multi-lbl" class="hasTiprefence" title="'.JText::_("COM_VDATA_WIDGET_SELECT_PROFIL_NAME").'" for="existing_database_table_multi"  aria-invalid="false">'.JText::_("COM_VDATA_WIDGET_SELECT_PROFIL_NAME").'</label><font color="#FF0000">*</font></td>
				<td><select class="required svminput load_change_class profile_existing_database_table" name="params[profiles][]" id="existing_database_table_multi" multiple="true">';
				$select ='';
						if(isset($chart_details_option->existing_database_table) && isset($chart_details_option->profiles) && count($chart_details_option->profiles)>0 && in_array(JText::_("COM_VDATA_SELECT_ALL_PROFILES"), $chart_details_option->profiles))
						$select =' selected="selected"'; 	
				$obj->html .= '<option value="'.JText::_("COM_VDATA_SELECT_ALL_PROFILES").'"'.$select.'>'.JText::_("COM_VDATA_SELECT_ALL_PROFILES").'</option>';
					
					foreach($profiels as $key => $value)	{
					
					$select ='';
						if(isset($chart_details_option->existing_database_table)&& isset($chart_details_option->profiles) && count($chart_details_option->profiles)>0 && in_array($value->id, $chart_details_option->profiles))
						$select =' selected="selected"';	
					$obj->html .= '<option value="'.$value->id.'"'.$select.'>'.$value->title.'</option>';
					
					 }	
					 
				$obj->html .= '</select></td></tr>';
			$obj->html .= '<tr class="extra_condition_field"><td width="200"><label class="hasTiprefence" for="vdata_profile_creation" id="vdata_profile_creation-lbl" title="'.JText::_("COM_VDATA_PROFILE_CREATION_BUTTON_DESC").'">'.JText::_("COM_VDATA_PROFILE_CREATION_BUTTON").'</label></td>       
			<td><input type="checkbox" name="params[profile_creation_button]" id="vdata_profile_creation" value="1"';
			if(isset($chart_details_option->profile_creation_button) && $chart_details_option->profile_creation_button!='')	{
				$obj->html .= ' checked="checked"';
			}
			$obj->html .= '/></td></tr>';	
			}
		if($table_name=='vData Plugins'){
		$query = 'select extension_id, name, element, folder from #__extensions where type = "plugin" and folder = "vdata" and enabled = 1';
		$profiels = $this->_db->setQuery($query)->loadObjectList();	
		$obj->html .= '<tr class="extra_condition_field vdata_profiles"><td width="200"><label id="existing_database_table_multi-lbl" class="hasTiprefence" title="'.JText::_("COM_VDATA_WIDGET_SELECT_PROFIL_NAME").'" for="existing_database_table_multi"  aria-invalid="false">'.JText::_("COM_VDATA_WIDGET_SELECT_PROFIL_NAME").'</label><font color="#FF0000">*</font></td>
				<td><select class="required svminput load_change_class" name="params[plugins][]" id="existing_database_table_multi" multiple="true">';
				
				$select ='';
						if(isset($chart_details_option->plugins) && isset($chart_details_option->plugins) && count($chart_details_option->plugins)>0 && in_array(JText::_("COM_VDATA_SELECT_ALL_PLUGINS"), $chart_details_option->plugins))
						$select =' selected="selected"';	
				$obj->html .= '<option value="'.JText::_("COM_VDATA_SELECT_ALL_PLUGINS").'"'.$select.'>'.JText::_("COM_VDATA_SELECT_ALL_PLUGINS").'</option>';
					foreach($profiels as $key => $value)	{
					
					$select ='';
						if(isset($chart_details_option->plugins) && isset($chart_details_option->plugins) && count($chart_details_option->plugins)>0 && in_array($value->extension_id, $chart_details_option->plugins))
						$select =' selected="selected"';	
					$obj->html .= '<option value="'.$value->extension_id.'"'.$select.'>'.$value->name.'</option>'; 
					
					 }	
					 
				$obj->html .= '</select></td></tr>';
				
				
			}	
		if($widget_type!='profile' && !in_array($table_name, $live_data_query))
		{ 
	      
		if (strpos(strtolower($query),'information_schema') == false)
			{
		 if($user_write_query_value==1){		
        if(strpos(strtolower($query),'where') == false ){
		$obj->html .= '<tr class="extra_condition_field"><td width="200"></td><td><label class="hasTiprefence btn btn-small btn-success" onclick="add_extra_condition();" title="'.JText::_("COM_VDATA_EXTRA_CONDITIONS_DESC").'"><strong>'.JText::_("COM_VDATA_EXTRA_CONDITIONS").'</strong></label><textarea name="params[extra_condition]" id="extra_condition" onchange="update_column_value();" rows="5" cols="5" readonly="readonly">';
			if(isset($chart_details_option->extra_condition))	{
				$obj->html .= $chart_details_option->extra_condition;
			}
			$obj->html .= '</textarea></td></tr>';
			}
			if(strpos(strtolower($query),'limit') == false ){
			$obj->html .= '<tr class="extra_condition_field"><td><label class="hasTiprefence required" title="'.JText::_("COM_VDATA_LIMIT_VALUE_DESC").'">'.JText::_("COM_VDATA_LIMIT_VALUE").'</label></td>       
			<td><input type="text" class="fontsize generalsize" name="params[limit_value]" id="limit_value" value="';
			if(isset($chart_details_option->limit_value))	{
				$obj->html .= $chart_details_option->limit_value;
			}
			$obj->html .= '"/></td></tr>';
			}
			// new changes
			if($widget_type=='writequery' && $user_write_query_value==1){
			$obj->html .= '<tr class="extra_condition_field"><td><label class="hasTiprefence" title="'.JText::_('COM_VDATA_ORDERING_REFERENCE_COLUMN_ORDERING_DESC').'">'.JText::_("COM_VDATA_ORDERING_REFERENCE_COLUMN_ORDERING").'</label></td>      
			<td><select class="load_change_class selectoptions_class" name="params[ordering_reference_column_name]" id="ordering_reference_column_name">';
			
			$obj->html .= '<option value="">'.JText::_("COM_VDATA_ORDERING_REFERENCE_COLUMN_SELECT").'</option>';
			for($i=0;$i<count($test);$i++)	{
			$select = '';	
			$obj->html .= '<option value="'.$test[$i]->Field.'"';
			if(isset($chart_details_option->ordering_reference_column_name) && $test[$i]->Field==$chart_details_option->ordering_reference_column_name)	{
				$obj->html .= ' selected="selected"';
			}
			$obj->html .= '>'.$test[$i]->Field.'</option>';
		     
			 } 
		
		     $obj->html .= '</select>';
			
			$obj->html .= '<select class="load_change_class small_select_class" name="params[ordering]" id="ordering_table_value">';
			
			if(isset($chart_details_option->ordering) && $chart_details_option->ordering!=''&& $chart_details_option->ordering =='asc')
				$select = ' selected="selected"';
			$obj->html .= '<option value="asc"'.$select.'>'.JText::_("asc").'</option>';
			$select = '';
			if(isset($chart_details_option->ordering)&& $chart_details_option->ordering!=''&& $chart_details_option->ordering == 'desc')
				$select = ' selected="selected"';
			$obj->html .= '<option value="desc"'.$select.'>'.JText::_("desc").'</option>
			</select></td></tr>';
			}
			}
		    }
			$c_type = $this->chart_type();
			if($table_name!='Show Logs on Timeline'){
			$obj->html .=  '<tr class="extra_condition_field chart_type_select" style="display:none">'.$this->chart_list('chart_type', isset($chart_details->chart_type)?$chart_details->chart_type:'', $c_type, '', 'onchange="selectoptions_for_chart();"',JText::_('COM_VCHART_MAIN_SELECT_CHART_TYPE')).'</tr>';
			}
		  
		}
		
		
		if(in_array($table_name, $live_data_query)){
		$obj->html .= '<tr class="extra_condition_field"><td width="200"><label class="hasTiprefence required" for="data_range_limit" title="'.JText::_("COM_VDATA_DATA_RANGE_LIMIT_DESC").'">'.JText::_("COM_VDATA_DATA_RANGE_LIMIT").'</label></td>       
			<td><input type="text" class="fontsize generalsize" name="params[data_range_limit]" id="data_range_limit" value="';
			if(isset($chart_details_option->data_range_limit))	{
				$obj->html .= $chart_details_option->data_range_limit;
			}
			$obj->html .= '"/></td></tr>';	
		$c_type = $this->chart_type();
		$obj->html .=  '<tr class="extra_condition_field chart_type_select" style="display:none">'.$this->chart_list('chart_type', isset($chart_details->chart_type)?$chart_details->chart_type:'', $c_type, '', 'onchange="selectoptions_for_chart();"',JText::_('COM_VCHART_MAIN_SELECT_CHART_TYPE')).'</tr>';	
		
		}
		
		if($widget_type=='writequery'){
			
			$obj->html .=  '<tr class="">';
			
			$obj->html .= '<td width="200"><label class="hasTiprefence" title="'.JText::_('COM_VDATA_WIDGET_NOTIFICATION_CONDITION_DESC').'">'.JText::_('COM_VDATA_WIDGET_NOTIFICATION_CONDITION').'</label></td>';
			
			$obj->html .= '<td><textarea name="params[notification][custom_condition]" cols="5" rows="5" placeholder="'.JText::_('write custom query notification condition').'">';
			if(isset($chart_details_option->notification->custom_condition)){
				$obj->html .= $chart_details_option->notification->custom_condition;
			}
			$obj->html .= '</textarea>';
				
			if($user_write_query_value!=1){
				/* $obj->html .= '<td><textarea name="params[notification][custom_condition]" cols="5" rows="5">';
				if(isset($chart_details_option->notification->custom_condition)){
					$obj->html .= $chart_details_option->notification->custom_condition;
				}
				$obj->html .= '</textarea></td>'; */
			}
			else{
				
				$obj->html .= '<br><span class="lbl_or">'.JText::_('OR').'</span><span class="notify_conditions btn btn-small btn-success hasTiprefence  hasTipformat" title="'.JText::_('COM_VDATA_WIDGET_NOTIFICATION_CONDITION_BTN_DESC').'" onclick="add_notification_condition();"><i class="icon-new"></i>&nbsp'.JText::_('COM_VDATA_WIDGET_NOTIFICATION_CONDITION_BTN').'</span>';
				
				$obj->html .= '<br><select name="params[notification][closure]">';
				$obj->html .= '<option value="and" ';
				$obj->html .= (isset($chart_details_option->notification->closure) && $chart_details_option->notification->closure=='and')?'selected="selected"':'';
				$obj->html .= '>'.JText::_('AND').'</option>';
				$obj->html .= '<option value="or" ';
				$obj->html .= (isset($chart_details_option->notification->closure) && $chart_details_option->notification->closure=='or')?'selected="selected"':'';
				$obj->html .= '>'.JText::_('OR').'</option>';
				$obj->html .= '</select>';
				
				//$chart_details_option
				if(isset($chart_details_option->notification->condition_col)){
				foreach($chart_details_option->notification->condition_col as $i=>$col){
					$obj->html .= '<div>';
					
					$obj->html .= '<span>';
					$obj->html .= '<select name="params[notification][condition_col][]">';
					$obj->html .= '<option value="">'.JText::_("COM_VDATA_ORDERING_REFERENCE_COLUMN_SELECT").'</option>';
					for($j=0;$j<count($test);$j++)	{
						$obj->html .= '<option value="'.$test[$j]->Field.'"';
						if(isset($col) && $test[$j]->Field==$col)	{
							$obj->html .= ' selected="selected"';
						}
						$obj->html .= '>'.$test[$j]->Field.'</option>';
					} 
					$obj->html .= '</select>';
					$obj->html .= '</span>';
					
					$obj->html .= '<span>';
					$obj->html .= '<select name="params[notification][condition][]">';
					$obj->html .= '<option value="equal" ';
					$obj->html .= (isset($chart_details_option->notification->condition[$i]) && ($chart_details_option->notification->condition[$i]=='equal'))?'selected="selected"':'';
					$obj->html .= '>'.JText::_('equal').'</option>';
					$obj->html .= '<option value="not_equal" ';
					$obj->html .= (isset($chart_details_option->notification->condition[$i]) && ($chart_details_option->notification->condition[$i]=='not_equal'))?'selected="selected"':'';
					$obj->html .= '>'.JText::_('not_equal').'</option>';
					$obj->html .= '<option value="in" ';
					$obj->html .= (isset($chart_details_option->notification->condition[$i]) && ($chart_details_option->notification->condition[$i]=='in'))?'selected="selected"':'';
					$obj->html .= '>'.JText::_('in').'</option>';
					$obj->html .= '<option value="not_in" ';
					$obj->html .= (isset($chart_details_option->notification->condition[$i]) && ($chart_details_option->notification->condition[$i]=='not_in'))?'selected="selected"':'';
					$obj->html .= '>'.JText::_('not_in').'</option>';
					$obj->html .= '<option value="less" ';
					$obj->html .= (isset($chart_details_option->notification->condition[$i]) && ($chart_details_option->notification->condition[$i]=='less'))?'selected="selected"':'';
					$obj->html .= '>'.JText::_('less').'</option>';
					$obj->html .= '<option value="less_or_equal" ';
					$obj->html .= (isset($chart_details_option->notification->condition[$i]) && ($chart_details_option->notification->condition[$i]=='less_or_equal'))?'selected="selected"':'';
					$obj->html .= '>'.JText::_('less_or_equal').'</option>';
					$obj->html .= '<option value="greater" ';
					$obj->html .= (isset($chart_details_option->notification->condition[$i]) && ($chart_details_option->notification->condition[$i]=='greater'))?'selected="selected"':'';
					$obj->html .= '>'.JText::_('greater').'</option>';
					$obj->html .= '<option value="greater_or_equal" ';
					$obj->html .= (isset($chart_details_option->notification->condition[$i]) && ($chart_details_option->notification->condition[$i]=='greater_or_equal'))?'selected="selected"':'';
					$obj->html .= '>'.JText::_('greater_or_equal').'</option>';
					$obj->html .= '<option value="between" ';
					$obj->html .= (isset($chart_details_option->notification->condition[$i]) && ($chart_details_option->notification->condition[$i]=='between'))?'selected="selected"':'';
					$obj->html .= '>'.JText::_('between').'</option>';
					$obj->html .= '</select>';
					$obj->html .= '</span>';
					
					$obj->html .= '<span>';
					$obj->html .= '<input type="text" class="condition_val" name="params[notification][condition_val][]" value="';
					$obj->html .= isset($chart_details_option->notification->condition_val[$i])?$chart_details_option->notification->condition_val[$i]:'';
					$obj->html .= '" />';
					$obj->html .= '</span>';
					
					$obj->html .= '<br /><span class="remove_condition btn btn-success">'.JText::_('REMOVE').'</span>';

					$obj->html .= '</div>';
				}
				}
				
			}
			$obj->html .= '</td>';
			$obj->html .= '</tr>';
			
			$obj->html .= '<tr class="">';
			$obj->html .= '<td width="200"><label class="hasTiprefence" title="'.JText::_('COM_VDATA_WIDGET_NOTIFICATION_EMAIL_TMPL_DESC').'">'.JText::_('COM_VDATA_WIDGET_NOTIFICATION_EMAIL_TMPL').'</label></td>';
			$obj->html .= '<td><textarea name="params[notification][email_tmpl]" cols="5" rows="5">';
			if(isset($chart_details_option->notification->email_tmpl)){
				$obj->html .= $chart_details_option->notification->email_tmpl;
			}
			$obj->html .= '</textarea></td>';
			$obj->html .= '</tr>';
			
			if($id>0){
				$obj->html .= '<tr>';
				$obj->html .= '<td width="200"><label class="hasTiprefence" title="'.JText::_('COM_VDATA_WIDGET_NOTIFICATION_CRON_URL_DESC').'">'.JText::_('COM_VDATA_WIDGET_NOTIFICATION_CRON_URL').'</label></td>';
				$obj->html .= '<td>'.JUri::root().'index.php?option=com_vdata&task=notification&widget='.$id.'</td>';
				$obj->html .= '</tr>';
			}
		}
		
		
		$obj->result = 'success';
		
		return $obj;
		
	}
	function formating_section(){
		
		 $obj = new stdClass();
		 $obj->result = 'error';
         $id = JFactory::getApplication()->input->get('id', 0);
		 $query = JFactory::getApplication()->input->get('query', '', 'RAW');
		 $table_name = JFactory::getApplication()->input->get('table_name', '', 'RAW');
		 $style = JFactory::getApplication()->input->get('style', '', 'RAW');
		 $user_write_query_value = JFactory::getApplication()->input->get('user_write_query_value', 1);
		 $chart_type = JFactory::getApplication()->input->get('chart_type', '', 'RAw');
         $datatype_option = JFactory::getApplication()->input->get('datatype_option', '', 'RAW');
		if($table_name==''){
			$obj->html = '';
			$obj->result = 'success';
			return $obj;
			}
		if($id>0){
		$chart_details = $this->getChart($id);
		$chart_details_option = json_decode($chart_details->detail);}
		else
		$chart_details=array();	
	   if($chart_type!='' && $style=='charting_formate')
	    $obj->formating = '<table class="chart_formating_section"><tr><th width="250">'.JText::_("COM_VDATA_WIDGET_CHART_FORMATING_SECTION").'</th></tr>';
	     $obj->html = '<table class="main_formating_section">';
		if($datatype_option=='writequery')
		{
		 try
			{
			if($user_write_query_value==1){
			$sql = 'show fields FROM '.$table_name;
		    $this->_db->setQuery( $sql );
			$columns = $this->_db->loadObjectList();
			}
			if($user_write_query_value!=1)
			{
			$columns = array();
            $config = JFactory::getConfig();	
			preg_match_all('/\s#__(.*?)\s/', $user_write_query_value.' ', $matchesmod, PREG_OFFSET_CAPTURE);
			
			
				foreach ($matchesmod[0] as $matchtable)
			    {
				
				  $sql = 'show fields FROM '.$config->get( 'db' ).'.'.trim($matchtable[0]);
		          $this->_db->setQuery( $sql );
			      $columns = $this->_db->loadObjectList();
				
				 
				} 
			}
			}
			catch (RuntimeException $e)
			{  
			$obj->result = 'error';
			$obj->error = $e->getMessage();
			return $obj;
			
			}
		
		if($style=='charting_formate'){
	   if($chart_type=='Line Chart' || $chart_type=='Area Chart' || $chart_type=='Stepped AreaChart' || $chart_type=='Column Chart' || $chart_type=='Bar Chart'){
		if($user_write_query_value==1)
		{	 
		   $obj->html .= '<tr><td width="50%"> <label id="horizontal_value_column-lbl" class="hasTipformat" for="horizontal_value_column" title="'.JText::_('COM_VDATA_COLUMN_FOR_HORIZONTAL_VALUE_DESC').'">'.JText::_('COM_VDATA_COLUMN_FOR_HORIZONTAL_VALUE').'</label></td><td> <select class="load_change_class required selectoptions_class" name="params[column_name]" id="horizontal_value_column">';
			
			for($i=0;$i<count($columns);$i++)	{
				
			$obj->html .= '<option value="'.$columns[$i]->Field.'"';
			if(isset($chart_details_option->column_name) and $columns[$i]->Field==$chart_details_option->column_name)	{
				$obj->html .= ' selected="selected"';
			}
			$obj->html .= '>'.$columns[$i]->Field.'</option>';
		     
			 }
		
		     $obj->html .= '</select></td></tr><br>';
		
		
		            $obj->html .= '<tr class="not_show_in_sql"><td width="36%"><input type="button" id="create_series" class="create_series btn btn-small btn-success btn_size change_button" value="'.JText::_('COM_VDATA_WIDGET_ADD_SERIES').'" disabled="true" /></td><td width="62%"></td></tr>';
					$obj->html .= '<tr class="not_show_in_sql"><td><label id="series_name-lbl" class="hasTipformat" title="'.JText::_('COM_VDATA_WIDGET_ADD_SERIES_DESC').'" for="series_name" aria-invalid="false">'.JText::_('COM_VDATA_WIDGET_ADD_SERIES').'<font color="#FF0000">*</font></label></td><td  width="62%"><input class="chartfield required make_field_readonly line" id="series_name" type="text" readonly="readonly" value="';
					if(isset($chart_details_option->series) && $chart_details_option->series!='')
		            $obj->html .= $chart_details_option->series;
					$obj->html .= '" name="params[series]"></td></tr>';
					$obj->html .= '<tr class="not_show_in_sql"><td><label id="series_column_name-lbl" class="hasTipformat" title="'.JText::_('COM_VDATA_EXISTING_SERIES_COLUMN_NAME').'" for="series_column_name">'.JText::_('COM_VDATA_EXISTING_SERIES_COLUMN_NAME').'<font color="#FF0000">*</font></label></td><td><input class="chartfield required" readonly="readonly" id="series_column_name" type="text" value="';
					if(isset($chart_details_option->series_column_name) && $chart_details_option->series_column_name!='')
		            $obj->html .= $chart_details_option->series_column_name;
					
					$obj->html .= '" name="params[series_column_name]"></td></tr>';
	               }	
					$obj->html .= '<tr><td width="200"><label id="series_column_color-lbl" for="series_column_color" class="hasTipformat" title="'. JText::_('COM_VDATA_WIDGET_EXISTING_SERIES_COLORS_DESC').'">'.JText::_('COM_VDATA_WIDGET_SERIES_COLORS').'</label></td><td><input class="chartfield required make_field_readonly" id="series_column_color" readonly="readonly" type="text" value="';
					if(isset($chart_details_option->series_column_color) && $chart_details_option->series_column_color!='')
		            $obj->html .= $chart_details_option->series_column_color;
					
					$obj->html .= '" name="params[series_column_color]"><span class="select_style_for_series btn btn-small btn-success btn_size change_button hasTipformat" title="'.JText::_('COM_VCHART_EXISTING_SERIES_COLORS_SELECT_DESC').'" disabled="true"><i class="icon-new"></i>&nbsp'.JText::_('COM_VCHART_EXISTING_SERIES_COLORS_SELECT').'</span></td></tr>';
					
						
					}
		       
				 elseif($chart_type == 'Pie Chart' || $chart_type == 'Slice Pie Chart'){
				  if($user_write_query_value==1)
		           {
				 $obj->html .= '<tr><td width="50%"><label id="horizontal_label_column-lbl" class="hasTipformat" title="'.JText::_('COM_VDATA_WIDGET_PIE_CHART_LABEL_DESC').'" for="horizontal_label_column">'.JText::_('COM_VDATA_WIDGET_PIE_CHART_LABEL').'</label></td><td><select class="load_change_class required selectoptions_class" name="params[label_column_name]" id="horizontal_label_column">';
		
						for($i=0;$i<count($columns);$i++)	{
							$obj->html .= '<option value="'.$columns[$i]->Field.'"';
							if(isset($chart_details_option->label_column_name) and $columns[$i]->Field==$chart_details_option->label_column_name)	{
								$obj->html .= ' selected="selected"';
							}
							$obj->html .= '>'.$columns[$i]->Field.'</option>';
						}
		
		          $obj->html .= '</select></td></tr><br>';	
				  
				   $obj->html .= '<tr><td> <label id="horizontal_value_column-lbl" class="hasTipformat" title="'.JText::_('COM_VDATA_WIDGET_PIE_CHART_VALUE_DESC').'" for="horizontal_value_column">'.JText::_('COM_VDATA_WIDGET_PIE_CHART_VALUE').'</label></td><td> <select class="load_change_class required selectoptions_class" name="params[value_column_name]" id="horizontal_value_column">';
		
				for($i=0;$i<count($columns);$i++)	{
					$obj->html .= '<option value="'.$columns[$i]->Field.'"';
					if(isset($chart_details_option->value_column_name) and $columns[$i]->Field==$chart_details_option->value_column_name)	{
						$obj->html .= ' selected="selected"';
					}
					$obj->html .= '>'.$columns[$i]->Field.'</option>';
				}
				
				$obj->html .= '</select></td></tr><br>';
				   }
				}
				elseif($chart_type == 'Table Chart'){
				$obj->html .= '<tr class="not_show_in_sql"><td width="50%"><input type="button" id="create_series" class="create_series btn btn-small btn-success btn_size change_button" value="'.JText::_('COM_VDATA_WIDGET_ADD_SERIES').'" disabled="true" /></td><td width="62%"></td></tr>';
					$obj->html .= '<tr class="not_show_in_sql"><td><label id="series_name-lbl" class="hasTipformat" title="'.JText::_('COM_VDATA_WIDGET_TABLE_ADD_SERIES').'" for="series_name" aria-invalid="false">'.JText::_('COM_VDATA_WIDGET_TABLE_ADD_SERIES').'<font color="#FF0000">*</font></label></td><td  width="62%"><input class="chartfield required make_field_readonly line" id="series_name" type="text" readonly="readonly" value="';
					if(isset($chart_details_option->series) && $chart_details_option->series!='')
		            $obj->html .= $chart_details_option->series;
					$obj->html .= '" name="params[series]"></td></tr>';
					
					$obj->html .= '<tr class="not_show_in_sql"><td><label id="series_column_name-lbl" class="hasTipformat" title="'.JText::_('COM_VDATA_EXISTING_SERIES_COLUMN_NAME').'" for="series_column_name">'.JText::_('COM_VDATA_EXISTING_SERIES_COLUMN_NAME').'<font color="#FF0000">*</font></label></td><td><input class="chartfield required" readonly="readonly" id="series_column_name" type="text" value="';
					if(isset($chart_details_option->series_column_name) && $chart_details_option->series_column_name!='')
		            $obj->html .= $chart_details_option->series_column_name;
					
					$obj->html .= '" name="params[series_column_name]"></td></tr>';	
					
				}
				elseif($chart_type == 'Geo Chart'){
					$obj->html .= '<tr><td> <label id="horizontal_label_column-lbl" for="horizontal_label_column">'.JText::_('COM_VDATA_WIDGET_COLUMN_FOR_ADDRESS').'</label></td><td> <select class="load_change_class required selectoptions_class" name="params[label_column_name]" id="horizontal_label_column">';
		
						for($i=0;$i<count($columns);$i++)	{
							$obj->html .= '<option value="'.$columns[$i]->Field.'"';
							if(isset($chart_details_option->label_column_name) and $columns[$i]->Field==$chart_details_option->label_column_name)	{
								$obj->html .= ' selected="selected"';
							}
							$obj->html .= '>'.$columns[$i]->Field.'</option>';
						}
		
		          $obj->html .= '</select></td></tr><br>';	
				  
				   $obj->html .= '<tr><td> <label id="horizontal_value_column-lbl" for="horizontal_value_column">'.JText::_('COM_VDATA_WIDGET_COLUMN_FOR_VALUE').'</label></td><td> <select class="load_change_class required selectoptions_class" name="params[value_column_name]" id="horizontal_value_column">';
		
				for($i=0;$i<count($columns);$i++)	{
					$obj->html .= '<option value="'.$columns[$i]->Field.'"';
					if(isset($chart_details_option->value_column_name) and $columns[$i]->Field==$chart_details_option->value_column_name)	{
						$obj->html .= ' selected="selected"';
					}
					$obj->html .= '>'.$columns[$i]->Field.'</option>';
				}
				
				$obj->html .= '</select></td></tr><br>'; 
				}
		      }
		     
			 }
				
				if($datatype_option=='writequery' && $style == 'listing_formate' && $user_write_query_value==1)
				{ 
				$obj->html .= '<tr><td> <label id="horizontal_value_column-lbl" class="hasTipformat" title="'.JText::_('COM_VDATA_WIDGET_COLUMN_FOR_LISTING_FORMAT_DESC').'" for="horizontal_value_column">'.JText::_('COM_VDATA_WIDGET_COLUMN_FOR_LISTING_FORMAT').'</label></td><td> <select class="load_change_class required selectoptions_class" name="params[listing_column_name][]" id="horizontal_value_column" multiple="true">';
		
				for($i=0;$i<count($columns);$i++)	{
					$obj->html .= '<option value="'.$columns[$i]->Field.'"';
					if(isset($chart_details_option->listing_column_name) &&in_array($columns[$i]->Field, $chart_details_option->listing_column_name))	{
						$obj->html .= ' selected="selected"';
					}
					$obj->html .= '>'.$columns[$i]->Field.'</option>';
				}
				
				$obj->html .= '</select></td></tr><br>';	
				}	
                if($chart_type == 'Line Chart' || $chart_type == 'Area Chart' ||  $chart_type == 'Stepped AreaChart' || $chart_type == 'Combo Chart' || $chart_type == 'Bar Chart' || $chart_type=='Column Chart' || $chart_type == 'Pie Chart' || $chart_type == 'Slice Pie Chart' || $chart_type == 'Geo Chart')
				    {
			    
				   $obj->formating .=  '<tr>'.$this->legends(isset($chart_details_option->legend)?$chart_details_option->legend:'',JText::_("COM_VDATA_SERIES_LEGEND"),'params[legend]').'</tr>';
                			
				    }
				
                 if($chart_type == 'Geo Chart'){
					  $region_label = array(JText::_('Africa'),JText::_('Europe'),JText::_('Americas'),JText::_('Asia'),JText::_('Oceania'));
					 $display_mode_text = array(JText::_('Region'),JText::_('Markers'),JText::_('Text'));
					 $display_mode_value = array('region','markers','text');
					 $region_value = array('002','150','019','142','009');
					 $obj->formating .= '<tr><td> <label id="horizontal_value_column-lbl" for="horizontal_value_column">'.JText::_('COM_VDATA_WIDGET_REGION').'</label></td><td> <select class="load_change_class required selectoptions_class" name="params[region_label]" id="horizontal_value_column">';
			        $obj->formating .= '<option value="">'.JText::_('World').'</option>';
					for($i=0;$i<count($region_label);$i++)	{
						$obj->formating .= '<option value="'.$region_value[$i].'"';
						if(isset($chart_details_option->region_label) and $region_value[$i]==$chart_details_option->region_label)	{
							$obj->formating .= ' selected="selected"';
						}
						$obj->formating .= '>'.$region_label[$i].'</option>';
					}
					
					$obj->formating .= '</select></td></tr><br>';
					
					$obj->formating .= '<tr><td> <label id="geochart_displaymode-lbl" class="hasTipformat" for="horizontal_value_column" title="'.JText::_('COM_VDATA_WIDGET_DISPLAY_MODE_DESC').'">'.JText::_('COM_VDATA_WIDGET_DISPLAY_MODE').'</label></td><td> <select class="load_change_class required selectoptions_class" name="params[displaymode]" id="geochart_displaymode">';
			        $obj->formating .= '<option value="">'.JText::_('COM_VDATA_GEOCHART_SELECT_DISPLAY_MODE_OPTION').'</option>';
					for($i=0;$i<count($display_mode_text);$i++)	{
						$obj->formating .= '<option value="'.$display_mode_value[$i].'"';
						if(isset($chart_details_option->displaymode) and $display_mode_value[$i]==$chart_details_option->displaymode)	{
							$obj->formating .= ' selected="selected"';
						}
						$obj->formating .= '>'.$display_mode_text[$i].'</option>';
					}
					
					$obj->formating .= '</select></td></tr><br>';
        		}		
				if($chart_type == 'Line Chart' || $chart_type == 'Area Chart' ||  $chart_type == 'Stepped AreaChart' || $chart_type == 'Combo Chart' || $chart_type == 'Bar Chart' || $chart_type == 'Column Chart')
				{
				

				$obj->formating .=  '<tr><td><label id="x_axis" class="hasTipformat" title="'.JText::_("COM_VDATA_SERIES_HORIZONTAL_LABEL_DESC").'>" for="x_axis" >'.JText::_("COM_VDATA_SERIES_HORIZONTAL_LABEL").'</label></td><td><input class="chartfield" id="x_axis" type="text" value="';
				
				if(isset($chart_details_option->x_axis) && $chart_details_option->x_axis!='')
		            $obj->formating .= $chart_details_option->x_axis;
				
				$obj->formating .=  '" name="params[x_axis]" ></td></tr>';
				$obj->formating .=  '<tr><td><label id="y_axis" class="hasTipformat" title="'.JText::_("COM_VDATA_SERIES_VERTICAL_LABEL_DESC").'" for="y_axis" >'.JText::_("COM_VDATA_SERIES_VERTICAL_LABEL").'</label></td><td><input class="chartfield" id="y_axis" type="text" value="';
				
				if(isset($chart_details_option->y_axis) && $chart_details_option->y_axis!='')
		            $obj->formating .= $chart_details_option->y_axis;
				$obj->formating .=  '" name="params[y_axis]" ></td></tr>';

					if($chart_type=='Line Chart')
					{
						$obj->formating .= '<tr><td><label id="chart_churve" class="hasTipformat" title="'.JText::_("COM_VDATA_SERIES_LINE_CHART_CURVE_DESC").'" for="chart_churve" aria-invalid="false">'.JText::_("COM_VDATA_SERIES_LINE_CHART_CURVE").'</label></td><td><input class="chartfield" id="y_axis_labels" type="checkbox" value="chart_churve"';
							if(isset($chart_details_option->chart_churve) && $chart_details_option->chart_churve=="chart_churve")
							{
							$obj->formating .= 'checked="checked"';
							}
						$obj->formating .= '" name="params[chart_churve]" ></td></tr>';
					}
					
					if($chart_type=='Area Chart' || $chart_type=='Stepped AreaChart')
					{
							$obj->formating .= '<tr><td><label id="jform_params_chartparams_tool_html" class="hasTipformat" title="'.JText::_('COM_VDATA_SERIES_AREA_STACK_DESC').'">'.JText::_('COM_VDATA_SERIES_AREA_STACK').'</label></td><td><input class="chartfield" id="tooltip_html" type="checkbox" value="isStacked"';

								if(isset($chart_details_option->isStacked) && $chart_details_option->isStacked="isStacked")
								$obj->formating .= 'checked="checked"';

							$obj->formating .= 'name="params[isStacked]"></td></tr>';

							$obj->formating .= '<tr><td><label id="chart_churve" class="hasTipformat" title="'.JText::_('COM_VDATA_SERIES_AREA_STACK_CONNECTED_DESC').'" aria-invalid="false">'.JText::_('COM_VDATA_SERIES_AREA_STACK_CONNECTED').'</label></td><td><input class="chartfield" id="y_axis_labels" type="checkbox" value="1"';

								if(isset($chart_details_option->connectSteps) && $chart_details_option->connectSteps==1){
								$obj->formating .= 'checked="checked"';
								}

							$obj->formating .= '" name="params[connectSteps]" ></td></tr>';

							$obj->formating .= '<tr><td><label id="chart_churve" class="hasTipformat" title="'.JText::_('COM_VDATA_SERIES_AREA_INTERACTIVITY_DESC').'" aria-invalid="false">'.JText::_('COM_VDATA_SERIES_AREA_INTERACTIVITY').'</label></td><td><input class="chartfield" id="y_axis_labels" type="checkbox" value="1"';
									if(isset($chart_details_option->enableInteractivity) && $chart_details_option->enableInteractivity==1){
									$obj->formating .= 'checked="checked"';
									}
							   $obj->formating .= ' name="params[enableInteractivity]" ></td></tr>';
					}
					
					if($chart_type == 'Column Chart' || $chart_type == 'Bar Chart')
					{
                     
					}
				}
		        elseif($chart_type == 'Pie Chart' || $chart_type == 'Slice Pie Chart'){
					$obj->formating .= '<tr><td><label id="pie_3d-lbl" class="hasTipformat" title="'.JText::_('COM_VDATA_PICHART_3D_DESC').'" for="pie_3d" aria-invalid="false">'.JText::_('COM_VDATA_PICHART_3D').'</label></td><td><input class="chartfield" id="pie_3d" type="checkbox" value="1"';
				if( isset($chart_details_option->pie_3d) && $chart_details_option->pie_3d==1)
				$obj->formating .= 'checked="checked"';
				$obj->formating .= 'name="params[pie_3d]" ></td></tr>';
				$obj->formating .=  '<tr>'.$this->pieHole(isset($chart_details_option->piehole)?$chart_details_option->piehole:'',JText::_('COM_VDATA_PICHART_PIEHOLE'),'params[piehole]').'</tr><tr>';	
				}
				
				elseif($chart_type == 'Table Chart'){
					$obj->formating .= '<tr><td><label id="table_page-lbl" class="hasTipformat" title="'.JText::_('COM_VDATA_TABLE_CHART_PAGE_DESC').'" for="table_page" aria-invalid="false">'.JText::_('COM_VDATA_TABLE_CHART_PAGE').'</label></td><td><input class="chartfield" id="table_page" type="checkbox" value="1"';
				if( isset($chart_details_option->table_page) && $chart_details_option->table_page==1)
				$obj->formating .= 'checked="checked"';
				$obj->formating .= 'name="params[table_page]" ></td></tr>';	
				 $total_page  = 100;$total_page_increment = 10; 
				 $obj->formating .= '<tr><td><label id="table_page_size-lbl" class="hasTipformat" title="'.JText::_('COM_VDATA_TABLE_CHART_PAGE_SIZE_DESC').'" for="table_page_size">'.JText::_('COM_VDATA_TABLE_CHART_PAGE_SIZE').'</label></td><td><select class="inputbox selectoptions_class" name="params[table_page_size]" id="table_page_size">';
				 for($p=1;$p<10;$p++){  
				$obj->formating .= '<option value="'.($p*10).'"'.(isset($chart_details_option->table_page_size) && $chart_details_option->table_page_size==$p?'select="selected"':'').'>'.($p*10).'</option>'; 
				 }
				
				$obj->formating .= '</select></td></tr>';
				
                $obj->formating .= '<tr><td><label id="table_page_button-lbl" class="hasTipformat" title="'.JText::_('COM_VDATA_TABLE_CHART_PAGE_BUTTON_DESC').'" for="table_page_button">'.JText::_('COM_VDATA_TABLE_CHART_PAGE_BUTTON').'</label></td><td><input type="checkbox" class="inputbox" name="params[table_page_button]" id="table_page_button" value="1"';
				if( isset($chart_details_option->table_page_button) && $chart_details_option->table_page_button==1)
				$obj->formating .= ' checked="checked"';
				$obj->formating .= ' name="params[table_page_button]" ></td></tr>';
				}
				elseif($chart_type == 'Map Chart'){}
				elseif($chart_type == 'Geo Chart'){}
				$obj->formating .= '</table>';
				
				
				if($datatype_option=='predefined' && $chart_type!='')
		        {
				 				
				
				if($chart_type != 'Table Chart'){
						
				$obj->html .= '<tr><td width="200"><label id="series_column_color-lbl" for="series_column_color" class="hasTipformat" title="'. JText::_('COM_VDATA_WIDGET_EXISTING_SERIES_COLORS_DESC').'">'.JText::_('COM_VDATA_WIDGET_SERIES_COLORS').'</label></td><td><input class="chartfield make_field_readonly series_color" id="series_column_color_for_existing_database" type="text" value="';
					if(isset($chart_details_option->series_column_color) && $chart_details_option->series_column_color!='')
		            $obj->html .= $chart_details_option->series_column_color;
				
					$obj->html .= '" name="params[series_column_color]"></td></tr>';
					}
					
			     }
		
				$obj->html .= '</table>';	
				$obj->html .= $obj->formating;	
				$obj->formating  = $obj->html;
					$obj->result = 'success';
		
		            return $obj;
					
	}
	public function series_column()
	{
		$table = JFactory::getApplication()->input->get('table_name', '', 'RAW');
		$id = JFactory::getApplication()->input->getInt('id', 0);
		$chart_type = JFactory::getApplication()->input->get('chart_type', '');
		$datatype_option = JFactory::getApplication()->input->get('datatype_option', ''); 
		$ht = $hts = $columns_details= array();
		$obj = new stdClass();
		$obj->result = 'error';
		 if($datatype_option=='predefined'){
			//$query = 'show fields FROM '.$this->_db->quotename($table);
			
			     $datas = array(); 
				 /* $datas['id'] = $table;
                 $data = array('id' => $table, 'table' => 'demo');
                 $postString = http_build_query($data, '', '&');
				$ch = curl_init(); 

				if (!$ch){die("Couldn't initialize a cURL handle");}
				$ret = curl_setopt($ch, CURLOPT_URL,"http://okhlites.com/demo/CustomQuizWebsite/new_stockdata.php");
                 
				curl_setopt($ch,CURLOPT_POST,1);

				curl_setopt ($ch, CURLOPT_POST, true);
				curl_setopt ($ch, CURLOPT_POSTFIELDS, $postString); 
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
				//$items = (curl_exec($ch));
				$params = json_decode(curl_exec($ch)); *///print_r($columns);jexit();
				preg_match_all('/{tablename\s(.*?)}/i', $table, $matches);
                preg_match_all('/{as\s(.*?)}/i', $table, $match);
				
				
				
				
				/*  */
		/* $matches = array_unique($matches[1])
		$match = array_unique($match[1]) */
		
		
		 /* $string = ' '.$params->value;
		
		 $ini = strpos($string,' from');
		 $where = strpos($string,'where');
		  $stlength = strlen($string);
        
		$ini += strlen(' from ');
		if($where === false)
		$len = strlen($string);
        else
		$len = strpos($string,'where',$ini) - $ini;
	
		$new_query = substr($string,$ini,$len);  */
					$tables_numbers = '';
					$matches_s =  $matches[1];
					$ht = $hts = $columns_details= array();
					for($j=0;$j<count($matches_s);$j++){
						
					 $query = 'show fields FROM '.trim(str_replace('{','',str_replace('}','',$matches_s[$j]))).' where Type like "int%" or  Type like "long%" or Type like "text%" or Type like "smallint%" or Type like "double%" or Type like "date%" or Type like "datetime%" or Type like "varchar%" or Type like "tinyint%"';
					$this->_db->setQuery( $query );
					$columns = $this->_db->loadObjectList();
					
					
					for($i=0;$i<count($columns);$i++)	{
						 $ht[$i] = isset($match[1][$j])?$match[1][$j].'.'.$columns[$i]->Field:'';
						 $type  = explode('(',$columns[$i]->Type);
						 $hts[$i] = 	$type[0];						   
						
					}
					$tables_numbers .= $tables_numbers==''?isset($match[1][$j])?$matches_s[$j].' '.$match[1][$j]:$matches_s[$j]:isset($match[1][$j])?','.$matches_s[$j].' '.$match[1][$j]:','.$matches_s[$j];
				}
		/* $columns_details[0] = $ht;
		$columns_details[1] = $hts;
		$columns_details[2] = $table_name;
		echo json_encode($columns_details);
				
			
				
				/* $ht = $columns[0];
		        $hts  = $columns[1];
				$tables_numbers  = $columns[2]; */ 
			
			}
		 else{
		 $tables_numbers  = $table;
		 $query = 'show fields FROM '.$this->_db->quotename($table).' where Type like "int%" or  Type like "long%" or Type like "smallint%" or Type like "double%" or Type like "date%" or Type like "datetime%" or Type like "varchar%" or Type like "tinyint%"';
		$this->_db->setQuery( $query );
		
		if($this->_db->getErrorNum())	{
			$obj->error = $this->_db->getErrorMsg();
			return $obj;
		}
		
		$columns = $this->_db->loadObjectList();
		
		
		
		for($i=0;$i<count($columns);$i++)	{
			 $ht[$i] = $columns[$i]->Field;
		     $type  = explode('(',$columns[$i]->Type);
			 $hts[$i] = 	$type[0];						   
			
		}
	   }
		$obj->html = $ht;
		$obj->column_type = $hts;
		$obj->tables_numbers = $tables_numbers;
		$obj->result = 'success';
		
		return $obj; 
		
		}
    function value_from_existing_database_sql_query(){ 
				$sql_query = JFactory::getApplication()->input->get('sql_query', '');
				$chart_type = JFactory::getApplication()->input->get('vchart_type', '');
				$database_type = JFactory::getApplication()->input->get('database_type', '');
				$driver = JFactory::getApplication()->input->get('driver', '');
				$host = JFactory::getApplication()->input->get('host', '');
				$user = JFactory::getApplication()->input->get('user', '');
				$password = JFactory::getApplication()->input->get('password', '');
				$database = JFactory::getApplication()->input->get('database', '');
				$prefix = JFactory::getApplication()->input->get('prefix', '');
				if($database_type == 'chart_data_from_other_database'){
				$option = array();
				try
				{
				$option['driver']   = $driver;            
				$option['host']     = $host;  
				$option['user']     = $user;  
				$option['password'] = $password; 
				$option['database'] = $database; 
				$option['prefix']   = $prefix;  
				
				$db = JDatabaseDriver::getInstance( $option );
				 $db->connect(); 
				}
				catch (RuntimeException $e)
					{                   
					$obj->error = $e->getMessage();
					return $obj;
					
					}
				}
				else
				$db = JFactory::getDbo();
				
				$obj = new stdClass();
				$obj->result = 'error';
				$series_val = $axis_val = array();
		
				if($sql_query != '')
				{
						try
						{
						
						$regex = '/{(.*?)}/i';
				        $regex_userid = '/{loggedinuserid}/i';
		   
		                preg_match_all($regex, $sql_query, $matches, PREG_SET_ORDER);
						if($matches)
						{
						$obj->axis = '';
			            $obj->extra_token = 1;
		                $obj->result = 'success';
						return $obj;
						}
						
						$query = $sql_query;
						$db->setQuery($query);
						
						$axis_v = $db->loadObjectList();
						}
						catch (RuntimeException $e)
						{                   
						$obj->error = $e->getMessage();
						return $obj;
						
						}
					$first=$second=$third=$fourth=$fiveth=array();
						for($i=0;$i<count($axis_v);$i++)
						{
						 $item = $axis_v[$i];
						 
						 if($chart_type=='Line Chart' || $chart_type=='Area Chart'|| $chart_type=='Column Charts'|| $chart_type=='Stepped AreaChart'|| $chart_type=='Combo Charts'|| $chart_type=='Bar Chart'|| $chart_type=='Scatter Chart'){$s=1;}
					elseif($chart_type=='Candlestick Charts'){$s=4;}
					elseif($chart_type=='Timeline Chart'){if(count(get_object_vars($item))==3)$s=2;elseif(count(get_object_vars($item))==4)$s=3;elseif(count(get_object_vars($item))==5)$s=4;}
					elseif($chart_type=='Bubble Chart'){if(count(get_object_vars($item))==3)$s=2;elseif(count(get_object_vars($item))==4)$s=3;elseif(count(get_object_vars($item))==5)$s=4;}
					elseif($chart_type=='Annotation chart'){}
					elseif($chart_type=='Donut Pie Chart' || $chart_type=='Pie Chart'|| $chart_type=='Slice Pie Chart'){}
						   $f=0;
							foreach ($item as $key => $value)
							{
							if($chart_type=='Line Chart' || $chart_type=='Area Chart'|| $chart_type=='Column Charts'|| $chart_type=='Stepped AreaChart'|| $chart_type=='Combo Charts'|| $chart_type=='Bar Chart'|| $chart_type=='Scatter Chart'){
							$axis_val[$i] = $value;
							break;}
							elseif($chart_type=='Candlestick Charts'){
								if($f>$s) break;
								
								if($f == 0){
								$first[$i] = $value;}
								elseif($f==1){
								$second[$i] = $value;}
								elseif($f==2){
								$third[$i] = $value;}
								elseif($f==3){
								$fourth[$i] = $value;}
								elseif($f==4){
								$fiveth[$i] = $value;}
								    }
							elseif($chart_type=='Annotation chart'){
								$axis_val[$i] = $value;
							break;
								    }
							elseif($chart_type=='Timeline Chart'){
								$axis_val[$i] = $value;
							     break;
								    }
							elseif($chart_type=='Bubble Chart'){
								if($f>4) break;
								
								if($f == 0){
								$first[$i] = $value;}
								elseif($f == 1){
								$second[$i] = $value;}
								elseif($f == 2){
								$third[$i] = $value;}
								elseif($f == 3){
								$fourth[$i] = $value;}
								elseif($f == 4){
								$fiveth[$i] = $value;}
								    }		
							elseif($chart_type=='Donut Pie Chart' || $chart_type=='Pie Chart'|| $chart_type=='Slice Pie Chart'){
								if($f>1) break;
								if($f == 0){
								$first[$i] = $value;}
								elseif($f == 1){
								$second[$i] = $value;}
								    }
								$f++;	
							}
							
						}
					if($chart_type=='Line Chart' || $chart_type=='Area Chart'|| $chart_type=='Column Charts'|| $chart_type=='Stepped AreaChart'|| $chart_type=='Combo Charts'|| $chart_type=='Bar Chart'|| $chart_type=='Scatter Chart'){
					$obj->axis = implode(',',$axis_val); }
					elseif($chart_type=='Timeline Chart'){ 
					$obj->axis = implode(',',$axis_val);
					}
					elseif($chart_type=='Candlestick Charts'){ 
					$obj->axis0 = implode(',',$first);
					$obj->axis1 = implode(',',$second);
					$obj->axis2 = implode(',',$third);
					$obj->axis3 = implode(',',$fourth);
					$obj->axis4 = implode(',',$fiveth);
					}
					elseif($chart_type=='Bubble Chart'){ 
					$obj->axis0 = implode(',',$first);
					$obj->axis1 = implode(',',$second);
					$obj->axis2 = implode(',',$third);
					$obj->axis3 = implode(',',$fourth);
					$obj->axis4 = implode(',',$fiveth);
					}
					elseif($chart_type=='Donut Pie Chart' || $chart_type=='Pie Chart'|| $chart_type=='Slice Pie Chart'){ 
					$obj->axis1 = implode(',',$first);
					$obj->axis2 = implode(',',$second);
					
					}
					elseif($chart_type=='Annotation chart'){ 
					$obj->axis = implode(',',$axis_val);
					
					}
		       }
		else{
			$obj->axis = '';
			}
		
		
		
		$obj->result = 'success';
		$obj->extra_token = 0;
		return $obj;
	  
	  
	  }
  function publish()
	{
	
		JSession::checkToken() or jexit( JText::_('Invalid Token') );
		
		$cid		= JFactory::getApplication()->input->get( 'cid', array(), 'post', 'array' );
		$task		= JFactory::getApplication()->input->getCmd( 'task' );
		$publish	= ($task == 'publish');
		
		$row =& $this->getTable('widget', 'VchartTable');
		
		if (!$row->publish($cid, $publish))	{
			$this->setError($row->getError());
			return false;
		}
		else
			return true;
	
	}
	function delete_backgroun_image(){
				$cid		= JFactory::getApplication()->input->getInt( 'chart_id', 0);
				$image_name		= JFactory::getApplication()->input->get( 'image_name', 'post', '' );
				$obj = new stdClass();
		        $obj->result = 'error';
				$Query = 'SELECT chart_title_option FROM #__vchart where id='.(int)$cid;
				$this->_db->setQuery($Query);
				$item = $this->_db->loadObject();
	  
				$chart_title_detail =  json_decode($item->chart_title_option);
				
				$img = JPATH_SITE.'/components/com_vchart/images/background/'.$chart_title_detail->bacground_image; 
				
				if(!empty($chart_title_detail->bacground_image) and is_file($img)){
				unlink($img);
				$chart_title_detail->bacground_image='';
				$chart_title_detail = json_encode($chart_title_detail);
				$Query = 'update  #__vchart set chart_title_option='.$this->_db->quote($chart_title_detail).' where id='.(int)$cid;
				$this->_db->setQuery($Query);
				$this->_db->query();
				$obj->html = $cid;
				$obj->result = 'success';
				}
	   
	   return $obj;
	   
	   }
   function getChart($id){
	   $Query = 'SELECT * FROM #__vd_widget where id='.(int)$id;
	   $this->_db->setQuery($Query);
	   $item = $this->_db->loadObject();
	   
	   return $item;
	   
	   }
}

