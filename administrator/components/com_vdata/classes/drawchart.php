<?php 
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Zaheer Abbas
# copyright Copyright (C) 2013 wwww.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech..com
# Technical Support:  Forum - http://www.wdmtech..com/support-forum chart_draw
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted Access');


class chart_draw
{

var $chart_script = '';			// the chart script to be returned
var $chart_data = null;			// the chart data definition structure
var $datasets = array();		// the dataset arrays
var $total_rows = 0;			// total number of rows from all queries
var $chart_title = '';
var $font_style='';


//-------------------------------------------------------------------------------
// Constructor
//

 public static  function draw_view_chart($chart_data)
       {
			$obj = new stdClass();
			$db = JFactory::getDbo();
		    $config = JFactory::getConfig();
			$uniqid 			= $chart_data->id;
			
			$chart_title		= $chart_data->chart_type;
			$width				= '400px';
			$height				= '400px';
			$chart_detail		= json_decode($chart_data->detail);
			if(empty($chart_detail->existing_database_table)&&(isset($chart_detail->user_write_query_value)&&$chart_detail->user_write_query_value=='') ){
				$obj->result = 'error';
				$obj->error = 'no_chart';;
				return $obj;
			}
				
			$chart_type = $chart_data->chart_type;
				
    $options = array();
	if($chart_type=='Line Chart')
	$chart_type='LineChart';
	if($chart_type=='Stepped AreaChart')
	$chart_type='SteppedAreaChart';
	if($chart_type=='Combo Charts')
	$chart_type='ComboChart';
   if($chart_type=='Area Chart')
	$chart_type='AreaChart';
	if($chart_type=='Bar Chart')
	$chart_type='BarChart';
	if($chart_type=='Column Chart')
	$chart_type='ColumnChart';
	if($chart_type=='Pie Chart')
	$chart_type='PieChart';
	if($chart_type=='Slice Pie Chart')
	$chart_type='PieChart';
	if($chart_type=='Geo Chart')
	$chart_type='GeoChart';
   if($chart_type=='Map Chart')
	$chart_type='Map';
if($chart_type=='Table Chart')
	$chart_type='Table';
	
	
					
				//if(isset($chart_title_option->backgroundColor))
			    //array_push($options, 'backgroundColor:{fill:"'.$chart_title_option->backgroundColor.'",stroke:"#666",strokeWidth:"10px"}'); 
				 $option =  $opt =  $opt1 = $grid = array();
					
					
					if(isset($chart_detail->x_axis) && $chart_detail->x_axis!='')
					array_push($options, 'hAxis: {title: "'.$chart_detail->x_axis.'", textPosition: "out"}');
				   if(isset($chart_detail->y_axis) && $chart_detail->y_axis!='')
					array_push($options, 'vAxis: {title: "'.$chart_detail->y_axis.'"}');
					if(isset($chart_detail->legend) && $chart_detail->legend!='')
					array_push($options, 'legend: {position:"'.$chart_detail->legend.'"}');
		           
				if(isset($chart_detail->pie_3d) && $chart_detail->pie_3d!='')
					array_push($options, 'is3D: true');
				if(isset($chart_detail->piehole) && $chart_detail->piehole!='')
					array_push($options, 'pieHole:'.$chart_detail->piehole);
				 array_push($options, 'width:"100%"');	
				 array_push($options, 'height:"100%"');
	                if(isset($chart_detail->legend) && $chart_detail->legend!='')
					array_push($options, 'chartArea:{top:30,height:"85%",width:"85%"}');
                    else
					array_push($options, 'chartArea:{top:25,height:"90%",width:"90%"}');	
						
			if(isset($chart_detail->series_column_color)&& $chart_detail->series_column_color!=''){
			$colors = explode(',',$chart_detail->series_column_color);

			if(count($colors)>0)
			array_push($options, 'colors: ["'.implode('","',$colors).'"]');
			}
		  

	switch($chart_type)	{
	
	case 'AreaChart':
	case 'LineChart':
	case 'ComboChart':
	case 'ColumnChart':
	case 'SteppedAreaChart':
	case 'BarChart':
	$package = 'corechart';
             $series_display = array();
			if(isset($chart_detail->chart_churve) && $chart_detail->chart_churve!='')
			array_push($options, ' curveType: "function"');
			
			array_push($options, ' lineWidth: 4');	
			
			if(isset($chart_detail->isStacked) && $chart_detail->isStacked!='')
			array_push($options, ' isStacked: true');
			if(isset($chart_detail->connectSteps) && $chart_detail->connectSteps!='')
			array_push($options, ' connectSteps: true');
			
			
			if(isset($chart_detail->orientation) && $chart_detail->orientation!='')
			array_push($options, ' orientation: "'.$chart_detail->orientation.'"');
			
			$items = array();
		   $data =array();
		   
		if($chart_data->datatype_option=='predefined')
		 {
			   $da = JFactory::getConfig(); 
			  
                $chart_detail->remote_query_value = preg_replace('{databasename}', '"'.$da->get('db').'"', $chart_detail->remote_query_value, 1); 
				preg_match_all('/{tablename\s(.*?)}/i', $chart_detail->remote_query_value, $matches);
                preg_match_all('/{as\s(.*?)}/i', $chart_detail->remote_query_value, $match);
				$matches_s = $matches[1];
				$text = $chart_detail->remote_query_value;
				for($r=0;$r<count($matches_s);$r++){
				$text = preg_replace('{tablename '.$matches_s[$r].'}', $matches_s[$r], $text, 1);	
				}
				$query = str_replace('{','',str_replace('}','',$text));
				
			if(isset($chart_detail->extra_condition) && !empty($chart_detail->extra_condition)){
				if(strpos(strtolower($query), 'where')!== false){
				 $substr = 'where ';
				 $attachment = $chart_detail->extra_condition.' AND ';
				 $query  = str_replace($substr, $substr.$attachment, $query);
                
				}
				elseif(strpos(strtolower($query), 'group by')!== false){
				 $substr = ' GROUP BY';
				 $attachment = ' where '.$chart_detail->extra_condition.' ';
				 $query = str_replace($substr, $attachment.$substr, $query);
				}
				else
				$query .= ' where '.$chart_detail->extra_condition;
			}
			if(isset($chart_detail->ordering_reference_column_name) && !empty($chart_detail->ordering_reference_column_name) && isset($chart_detail->ordering) && !empty($chart_detail->ordering))
				$query .= ' ORDER BY '.$chart_detail->ordering_reference_column_name.' '.$chart_detail->ordering;
			if(isset($chart_detail->limit_value) && !empty($chart_detail->limit_value))
				$query .= ' LIMIT '.$chart_detail->limit_value;
			    $query = str_replace('informationschema','information_schema',$query);
				$query = str_replace('{databasename}',$config->get( 'db' ),$query);
		        $db->setQuery($query);
				if($db->getErrorNum())	{
				$obj->result = 'error';
				$obj->error = $db->getErrorMsg();
				return $obj;
				}
				$chart_data = $db->loadObjectList(); 
			 
				$label_array = isset($chart_data[0])?get_object_vars($chart_data[0]):array();
				
				 $num =0;
				 $new_label_array = array();
				if(strpos($query, 'information_schema')!== false)
				{
				$item = '[{type:"string", label:"'.$chart_detail->existing_database_table.'"}';
				$num =1;
				for($i=0;$i<count($chart_data);$i++)
				  {
					$chart_datas = $chart_data[$i];
						
					$item .= ', "'.$chart_datas->Variable_name.'"';
					array_push($series_display,'{column: '.($num).',display: true}');
					$num++;		
									  
					}
						$item .= ']';
				array_push($items, $item);
				$item = '["'.$chart_detail->existing_database_table.'"';
				
				for($i=0;$i<count($chart_data);$i++)
				  {
					$chart_datas = $chart_data[$i];

					$item .= ', '.$chart_datas->Value;
				  
					}
						$item .= ']';
						array_push($items, $item);		
						 
								
				}
				else
				{
				$item = '[';
				 $num =0;
				 $new_label_array = array();
				foreach($label_array as $key=>$value){
					$new_label_array[$num] = $key;
					if($num == 0)
				$item .= '{type:"string", label:"'.$key.'"}';
                else
					{
					$item .= ', "'.$key.'"';
					array_push($series_display,'{column: '.$num.',display: true}');
				    }
                 $num++;			
				}
	              $item .= ']';
                 array_push($items, $item);				
				
				
				
				$num =0;
				
			for($i=0;$i<count($chart_data);$i++){
				$chart_datas = $chart_data[$i];
			    $new_label = $new_label_array[0];
				$item = '["'.$chart_datas->$new_label.'"';	
				
			  	for($j=1;$j<count($new_label_array);$j++){
					$new_label = $new_label_array[$j];
					$item .= ', '.$chart_datas->$new_label;	
				}
               $item .=']';
              array_push($items, $item);			 
			}
			}
			$data = implode(',', $items); 
		    
			
			
		}
		elseif($chart_data->datatype_option=='writequery')
		{     
				if(isset($chart_detail->user_write_query_value) && $chart_detail->user_write_query_value!='')
				{
				$query  = $chart_detail->user_write_query_value;
				}
			    else
				{
				$series_array = explode(',', $chart_detail->series_column_name);
				$series_name = explode(',', $chart_detail->series);
				for($s =0; $s<count($series_array);$s++){

				if($chart_detail->column_name==$series_array[$s]){
				$series_array[$s] = $series_array[$s].' as '.$series_name[$s]; 
				}
				} 
				$query = 'select '.$chart_detail->column_name.','.implode(', ',$series_array).' from '.$chart_detail->existing_database_table;
				if(!empty($chart_detail->extra_condition))
				$query .= ' where '.$chart_detail->extra_condition;
				if(isset($chart_detail->ordering_reference_column_name) && !empty($chart_detail->ordering_reference_column_name) && isset($chart_detail->ordering) && !empty($chart_detail->ordering))
				$query .= ' ORDER BY '.$chart_detail->ordering_reference_column_name.' '.$chart_detail->ordering;
				if(isset($chart_detail->limit_value) && !empty($chart_detail->limit_value))
				$query .= ' LIMIT '.$chart_detail->limit_value;
			     }
				
				$db->setQuery($query);
				if($db->getErrorNum())	{
				$obj->result = 'error';
				$obj->error = $db->getErrorMsg();
				return $obj;
				}
				$chart_data = $db->loadObjectList();
				$label_array = isset($chart_data[0])?get_object_vars($chart_data[0]):array();
				$num =0;$label_arrays = array();
				if(strpos($query, 'information_schema')!== false)
				{
				$item = '[{type:"string", label:"'.$chart_detail->existing_database_table.'"}';
				$num =1;
				for($i=0;$i<count($chart_data);$i++)
				  {
					$chart_datas = $chart_data[$i];
						
					$item .= ', "'.$chart_datas->Variable_name.'"';
					array_push($series_display,'{column: '.($num).',display: true}');
					$num++;		
									  
					}
						$item .= ']';
				array_push($items, $item);
				$item = '["'.$chart_detail->existing_database_table.'"';
				
				for($i=0;$i<count($chart_data);$i++)
				  {
					$chart_datas = $chart_data[$i];

					$item .= ', '.(float)$chart_datas->Value;
				  
					}
						$item .= ']';
						array_push($items, $item);		
						 
								
				}
                else{
					
				
				$item = '[';
				 
				foreach($label_array as $key=>$value){
					$label_arrays[$num] = $key;
					if($num == 0)
				$item .= '{type:"string", label:"'.$key.'"}';
                else
					{
					$item .= ', "'.$key.'"';
					array_push($series_display,'{column: '.$num.',display: true}');
				    }
			   $num++;
				}
	            $item .= ']';
				array_push($items, $item);
				$num =0;
					for($i=0;$i<count($chart_data);$i++){

                        $chart_datas = $chart_data[$i];

                        $labelname = $label_arrays[0];

                        $item = '["'.$chart_datas->$labelname.'"';    

                        

                        for($j=1;$j<count($label_arrays);$j++){

                            $labelname = $label_arrays[$j];

                            $item .= ', '.(float)$chart_datas->$labelname;    

                        }

                       $item .=']';

                      array_push($items, $item);            

                    }
				
		    }
			$data = implode(',', $items);
		  }
		
			 $scripts = ' if(typeof google !== "undefined") 
				google.load("visualization", "1.1", {"packages":["'. $package.'", "controls"], callback: drawchart' . $uniqid . '});	
				
				function drawchart'.$uniqid.'() {
				var data'.$uniqid.' = google.visualization.arrayToDataTable(['.$data.']);
				
				
				var chart'.$uniqid.' = new google.visualization.ChartWrapper({
				chartType: "'.$chart_type.'",
				containerId: "widget_' . $uniqid . '",
				dataTable: data'.$uniqid.',
				options : {'.implode(',', $options).'}
				})
				 var columns'.$uniqid.' = [0];
  
    var seriesMap'.$uniqid.' = ['.implode(',', $series_display).'];
    var columnsMap'.$uniqid.' = {};
    var series'.$uniqid.' = [];
    for (var i = 0; i < seriesMap'.$uniqid.'.length; i++) {
        var col'.$uniqid.' = seriesMap'.$uniqid.'[i].column;
        columnsMap'.$uniqid.'[col'.$uniqid.'] = i;
       
        series'.$uniqid.'[i] = {};
        if (seriesMap'.$uniqid.'[i].display) {
          
            columns'.$uniqid.'.push(col'.$uniqid.');
        }
        else {
           
                columns'.$uniqid.'.push({
                label: data'.$uniqid.'.getColumnLabel(col'.$uniqid.'),
                type: data'.$uniqid.'.getColumnType(col'.$uniqid.'),
                sourceColumn: col'.$uniqid.',
                calc: function () {
                    return null;
                }
            });
          
            if (typeof(series'.$uniqid.'[i].color) !== "undefined") {
                series'.$uniqid.'[i].backupColor = series'.$uniqid.'[i].color;
            }
            series'.$uniqid.'[i].color = "#CCCCCC";
        }
        
    }
    
    chart'.$uniqid.'.setOption("series", series'.$uniqid.');
				
				
				
				google.visualization.events.addListener(chart'.$uniqid.', "select", showHideSeries'.$uniqid.');
				 function showHideSeries'.$uniqid.' () {
                   var sel'.$uniqid.' = chart'.$uniqid.'.getChart().getSelection();
       
					if (sel'.$uniqid.'.length > 0) {
						
						if (sel'.$uniqid.'[0].row == null) {
							var col'.$uniqid.' = sel'.$uniqid.'[0].column;
							if (typeof(columns'.$uniqid.'[col'.$uniqid.']) == "number") {
								var src'.$uniqid.' = columns'.$uniqid.'[col'.$uniqid.'];
								
							   
								columns'.$uniqid.'[col'.$uniqid.'] = {
									label: data'.$uniqid.'.getColumnLabel(src'.$uniqid.'),
									type: data'.$uniqid.'.getColumnType(src'.$uniqid.'),
									sourceColumn: src'.$uniqid.',
									calc: function () {
										return null;
									}
								};
								
							   
								series'.$uniqid.'[columnsMap'.$uniqid.'[src'.$uniqid.']].color = "#CCCCCC";
							}
							else {
								var src'.$uniqid.' = columns'.$uniqid.'[col'.$uniqid.'].sourceColumn;
								
							   
								columns'.$uniqid.'[col'.$uniqid.'] = src'.$uniqid.';
								series'.$uniqid.'[columnsMap'.$uniqid.'[src'.$uniqid.']].color = null;
							}
							var view'.$uniqid.' = chart'.$uniqid.'.getView() || {};
							view'.$uniqid.'.columns = columns'.$uniqid.';
							chart'.$uniqid.'.setView(view'.$uniqid.');
							chart'.$uniqid.'.draw();
						}
					}
    }
				
				 var view'.$uniqid.' = {
        columns: columns'.$uniqid.'
    };
    chart'.$uniqid.'.draw();';
			
          $scripts .= '}';	
				
				
				
			/* 	$scripts = ' if(typeof google !== "undefined") 
				google.load("visualization", "1.1", {"packages":["'. $package.'", "controls"]});	
				google.setOnLoadCallback(drawchart' . $uniqid . ');
				function drawchart'.$uniqid.'() {
				var data'.$uniqid.' = google.visualization.arrayToDataTable(['.$data.']);
				var options'.$uniqid.' = {'.implode(',', $options).'};
				var chart'.$uniqid.' =  new google.visualization.'.$chart_type.'(document.getElementById("widget_' . $uniqid . '"));';
				
				
				
				$scripts .= ' chart'.$uniqid.'.draw(data'.$uniqid.', options'.$uniqid.');';
			
               $scripts .= '}'; */
			
				break;
				case 'GeoChart':
				case 'PieChart':
				
				if(isset($chart_detail->region_label) && $chart_detail->region_label!='')
				array_push($options, ' region: "'.$chart_detail->region_label.'"');
				if(isset($chart_detail->displaymode) && $chart_detail->displaymode!='')
				array_push($options, ' displayMode: "'.$chart_detail->displaymode.'"');
			
				$items = array();
				$package = 'corechart';
				if($chart_data->datatype_option=='predefined')
		        {
			    
                $da = JFactory::getConfig(); 
                $chart_detail->remote_query_value = preg_replace('{databasename}', '"'.$da['db'].'"', $chart_detail->remote_query_value, 1);
				 
				if(empty($chart_detail->remote_query_value))
					return ;
				preg_match_all('/{tablename\s(.*?)}/i', $chart_detail->remote_query_value, $matches);
                preg_match_all('/{as\s(.*?)}/i', $chart_detail->remote_query_value, $match);
				$matches_s = $matches[1];
				$text = $chart_detail->remote_query_value;
				for($r=0;$r<count($matches_s);$r++){
				$text = preg_replace('{tablename '.$matches_s[$r].'}', $matches_s[$r], $text, 1);	
				}
				$query = str_replace('{','',str_replace('}','',$text));
				
			if(!empty($chart_detail->extra_condition))
				$query .= ' where '.$chart_detail->extra_condition;
			if(isset($chart_detail->ordering_reference_column_name) && !empty($chart_detail->ordering_reference_column_name) && isset($chart_detail->ordering) && !empty($chart_detail->ordering))
				$query .= ' ORDER BY '.$chart_detail->ordering_reference_column_name.' '.$chart_detail->ordering;
			if(isset($chart_detail->limit_value) && !empty($chart_detail->limit_value))
				$query .= ' LIMIT '.$chart_detail->limit_value;
			    $query = str_replace('informationschema','information_schema',$query);
				$query = str_replace('{databasename}',$config->get( 'db' ),$query);
		        $db->setQuery($query);
				if($db->getErrorNum())	{
				$obj->result = 'error';
				$obj->error = $db->getErrorMsg();
				return $obj;
				}
				$chart_data = $db->loadObjectList();
				$label_array = get_object_vars($chart_data[0]);
				if(strpos($query, 'information_schema')!== false)
				{
				$item = '[{type:"string", label:"'.$chart_detail->existing_database_table.'"},"Value"]';
				array_push($items, $item);
				
				for($i=0;$i<count($chart_data);$i++)
				  {
						$chart_datas = $chart_data[$i];
					$item = '["'.$chart_datas->Variable_name.'"';	
					$item .= ', '.$chart_datas->Value.']';
					
				     array_push($items, $item);	
									  
					}
				
				}
				else
				{
				 $item = '[';
				 $num =0;
				 $new_label_array = array();
				foreach($label_array as $key=>$value){
					$new_label_array[$num] = $key;
					if($num == 0)
				$item .= '{type:"string", label:"'.$key.'"}';
                else
                $item .= ', "'.$key.'"';
                 $num++;			
				}
	            $item .= ']';
				array_push($items, $item);
				$num =0;
			for($i=0;$i<count($chart_data);$i++){
				$chart_datas = $chart_data[$i];
			    $newlabel = $new_label_array[0];
				$item = '["'.$chart_datas->$newlabel.'"';	
				
			  	for($j=1;$j<count($new_label_array);$j++){
					$newlabel = $new_label_array[$j];
					$item .= ', '.$chart_datas->$newlabel;	
				}
               $item .=']';
              array_push($items, $item);			 
			}
		}
			$data = implode(',', $items); 
		    
			
			
		}
		elseif($chart_data->datatype_option=='writequery')
		{
			  
			  if(isset($chart_detail->user_write_query_value) && $chart_detail->user_write_query_value!='')
				{
				$query  = $chart_detail->user_write_query_value; 
			 }
             else
			 {		 
			 
			 if($chart_detail->label_column_name!=$chart_detail->value_column_name)
			 $query = 'select '.$chart_detail->label_column_name.','.$chart_detail->value_column_name.' from '.$chart_detail->existing_database_table;
		    else
			 $query = 'select '.$chart_detail->label_column_name.','.$chart_detail->value_column_name.' from '.$chart_detail->existing_database_table;
		     }	
		 
			if(!empty($chart_detail->extra_condition))
				$query .= ' where '.$chart_detail->extra_condition;
			if(isset($chart_detail->ordering_reference_column_name) && !empty($chart_detail->ordering_reference_column_name) && isset($chart_detail->ordering) && !empty($chart_detail->ordering))
				$query .= ' ORDER BY '.$chart_detail->ordering_reference_column_name.' '.$chart_detail->ordering;
			if(isset($chart_detail->limit_value) && !empty($chart_detail->limit_value))
				$query .= ' LIMIT '.$chart_detail->limit_value;
			
				
				$db->setQuery($query);
				if($db->getErrorNum())	{
				$obj->result = 'error';
				$obj->error = $db->getErrorMsg();
				return $obj;
				}
				$chart_data = $db->loadObjectList();//print_r($data);
				$label_array = isset($chart_data[0])?get_object_vars($chart_data[0]):array();
				
					$num =0; 
					$item = '[';
					foreach($label_array as $key=>$value)
					{
						$label_arrays[$num] = $key;
						
						$item .= $num == 0?'{type:"string", label:"'.$key.'"}':', "'.$key.'"';
						
						$num++;
					}	
					$item .= ']';				
				
				array_push($items, $item);
				$num =0;
				
			for($i=0;$i<count($chart_data);$i++)
			   {
				$chart_datas = $chart_data[$i];
			    $item ='[';
				$num =0;
					foreach($chart_datas as $key=>$value)
					{
					  $item .= $num ==0?'"'.$value.'"':', '.$value;	
					 $num++;
					 }
				 $item .=']';
				  array_push($items, $item);			 
				}
				$data = implode(',', $items);
			  }				
				
				
				 $scripts = ' if(typeof google !== "undefined") 
				google.load("visualization", "1.1", {"packages":["'. $package.'", "controls"]});	
				google.setOnLoadCallback(drawchart' . $uniqid . ');
				function drawchart'.$uniqid.'() {
				var data'.$uniqid.' = google.visualization.arrayToDataTable(['.$data.']);
				var options'.$uniqid.' = {'.implode(',', $options).'};
				var chart'.$uniqid.' =  new google.visualization.'.$chart_type.'(document.getElementById("widget_' . $uniqid . '"));';
				
				
				
				$scripts .= ' chart'.$uniqid.'.draw(data'.$uniqid.', options'.$uniqid.');';
			
          $scripts .= '}';
				break;
				case 'Table':
				$package = 'table'; 
				$items = array(); 
		        array_push($options, 'showRowNumber: true');
				array_push($options, 'allowHtml: true');
				if(isset($chart_detail->table_page) && $chart_detail->table_page!='')
				array_push($options, ' page: "enable"');
			if(isset($chart_detail->table_page_size) && $chart_detail->table_page_size!='')
				array_push($options, ' pageSize: '.$chart_detail->table_page_size);
			if(isset($chart_detail->table_page_button) && $chart_detail->table_page_button!='')
				array_push($options, ' pagingSymbols: "{prev: \'prev\', next: \'next\'}"');
		    if($chart_data->datatype_option=='predefined')
		       {
			    $da = JFactory::getConfig(); 
                $chart_detail->remote_query_value = preg_replace('{databasename}', '"'.$da['db'].'"', $chart_detail->remote_query_value, 1);
				preg_match_all('/{tablename\s(.*?)}/i', $chart_detail->remote_query_value, $matches);
                preg_match_all('/{as\s(.*?)}/i', $chart_detail->remote_query_value, $match);
				$matches_s = $matches[1];
				$text = $chart_detail->remote_query_value;
				for($r=0;$r<count($matches_s);$r++){
				$text = preg_replace('{tablename '.$matches_s[$r].'}', $matches_s[$r], $text, 1);	
				}
				$query = str_replace('{','',str_replace('}','',$text));
				
			if(isset($chart_detail->extra_condition) && !empty($chart_detail->extra_condition)){
				if(strpos(strtolower($query), 'where')!== false){
				 $substr = 'where ';
				 $attachment = $chart_detail->extra_condition.' AND ';
				 $query  = str_replace($substr, $substr.$attachment, $query);
                
				}
				elseif(strpos(strtolower($query), 'group by')!== false){
				 $substr = ' GROUP BY';
				 $attachment = ' where '.$chart_detail->extra_condition.' ';
				 $query = str_replace($substr, $attachment.$substr, $query);
				}
				else
				$query .= ' where '.$chart_detail->extra_condition;
			}
			if(isset($chart_detail->ordering_reference_column_name) && !empty($chart_detail->ordering_reference_column_name) && isset($chart_detail->ordering) && !empty($chart_detail->ordering))
				$query .= ' ORDER BY '.$chart_detail->ordering_reference_column_name.' '.$chart_detail->ordering;
			if(isset($chart_detail->limit_value) && !empty($chart_detail->limit_value))
				$query .= ' LIMIT '.$chart_detail->limit_value;
			    $query = str_replace('informationschema','information_schema',$query);
				$query = str_replace('{databasename}',$config->get( 'db' ),$query);
		        $db->setQuery($query);
				if($db->getErrorNum())	{
				$obj->result = 'error';
				$obj->error = $db->getErrorMsg();
				return $obj;
				}
				$chart_data = $db->loadObjectList();
				$label_array = isset($chart_data[0])?get_object_vars($chart_data[0]):array();
				 
				 $num =0;
				 $new_label_array = array();
				 if(isset($chart_detail->existing_database_table) && $chart_detail->existing_database_table=='Show Logs on Timeline'){
				$item = '[{type:"string"';	
                $item .= ', label:"Table Name"},"Message","Date"]';	
				array_push($items, $item);
				
                $new_label_array = array('table','message','op_start');	
                for($i=0;$i<count($chart_data);$i++){
				$chart_datas = $chart_data[$i];
			    $link 		= JRoute::_( 'index.php?option=com_vdata&view=logs&task=edit&cid[]='. $chart_datas->id );
				$newlabel = $new_label_array[0];
				$item = '["<a href=\"'.$link.'\">'.$chart_datas->$newlabel.'</a>"';	
				
			  	for($j=1;$j<count($new_label_array);$j++){
					$newlabel = $new_label_array[$j];
					$item .= ', "'.$chart_datas->$newlabel.'"';		
				}
               $item .=']';
              array_push($items, $item);			 
			}				
				 }
				 else
				 {
				 $item = '[';
				foreach($label_array as $key=>$value){
					$new_label_array[$num] = $key;
					if($num == 0)
				$item .= '{type:"string", label:"'.$key.'"}';
                else
                $item .= ', {type:"string",label:"'.$key.'"}';
                 $num++;			
				}
	            $item .= ']';
				 
				array_push($items, $item); 
				$num =0;
			for($i=0;$i<count($chart_data);$i++){
				$chart_datas = $chart_data[$i];
			    $newlabel = $new_label_array[0];
				$item = '["'.$chart_datas->$newlabel.'"';	
				
			  	for($j=1;$j<count($new_label_array);$j++){
					 $newlabel = $new_label_array[$j];
					$item .= ', "'.$chart_datas->$newlabel.'"';	
				}
               $item .=']';
              array_push($items, $item);			 
			}
			}
			$data = implode(',', $items); 
		    
			
			
		}
		elseif($chart_data->datatype_option=='writequery')
		{
			  $series_array = explode(',', $chart_detail->series_column_name);
			  $series_name = explode(',', $chart_detail->series);
			 for($s =0; $s<count($series_array);$s++){
			   $series_array[$s] = '`'.$series_array[$s].'` as `'.$series_name[$s].'`'; 
			  
			  } 
            $query = 'select '.implode(', ',$series_array).' from '.$chart_detail->existing_database_table;
			if(!empty($chart_detail->extra_condition))
				$query .= ' where '.$chart_detail->extra_condition;
			if(isset($chart_detail->ordering_reference_column_name) && !empty($chart_detail->ordering_reference_column_name) && isset($chart_detail->ordering) && !empty($chart_detail->ordering))
				$query .= ' ORDER BY '.$chart_detail->ordering_reference_column_name.' '.$chart_detail->ordering;
			if(isset($chart_detail->limit_value) && !empty($chart_detail->limit_value))
				$query .= ' LIMIT '.$chart_detail->limit_value;
			
			  
			 
				$db->setQuery($query);
				if($db->getErrorNum())	{
				$obj->result = 'error';
				$obj->error = $db->getErrorMsg();
				return $obj;
				}
				$chart_data = $db->loadObjectList();
				$label_array = isset($chart_data[0])?get_object_vars($chart_data[0]):array();
				 $item = '[{type:"string"';
				 $num =0;$label_arrays = array();
				foreach($label_array as $key=>$value){
					$label_arrays[$num] = $key;
					if($num == 0)
				$item .= ', label:"'.$key.'"}';
                else
                $item .= ', "'.$key.'"';
			   $num++;
				}
	            $item .= ']';
				array_push($items, $item);
				$num =0;
			for($i=0;$i<count($chart_data);$i++){
				$chart_datas = $chart_data[$i];
			    $newlabel = $label_arrays[0];
				$item = '["'.$chart_datas->$newlabel.'"';	
				
			  	for($j=1;$j<count($label_arrays);$j++){
					$newlabel = $label_arrays[$j];
					$item .= ', "'.$chart_datas->$newlabel.'"';	
				}
               $item .=']';
              array_push($items, $item);			 
			}
			$data = implode(',', $items);
		  }
		        $scripts = ' if(typeof google !== "undefined") 
				google.load("visualization", "1.1", {"packages":["'. $package.'", "controls"]});	
				google.setOnLoadCallback(drawchart' . $uniqid . ');
				function drawchart'.$uniqid.'() {
				var data'.$uniqid.' = google.visualization.arrayToDataTable(['.$data.']);
				var options'.$uniqid.' = {'.implode(',', $options).'};
				var chart'.$uniqid.' =  new google.visualization.'.$chart_type.'(document.getElementById("widget_' . $uniqid . '"));';
				
				
				
				$scripts .= ' chart'.$uniqid.'.draw(data'.$uniqid.', options'.$uniqid.');';
			
          $scripts .= '}';
				break;
				case 'Maps':
				$package = 'map';
				
				$chart_type = 'Map';
				 $scripts = ' if(typeof google !== "undefined") 
				google.load("visualization", "1.1", {"packages":["'. $package.'", "controls"]});	
				google.setOnLoadCallback(drawchart' . $uniqid . ');
				function drawchart'.$uniqid.'() {
				var data'.$uniqid.' = google.visualization.arrayToDataTable(['.$data.']);
				var options'.$uniqid.' = {'.implode(',', $options).'};
				var chart'.$uniqid.' =  new google.visualization.'.$chart_type.'(document.getElementById("widget_' . $uniqid . '"));';
				
				
				
				$scripts .= ' chart'.$uniqid.'.draw(data'.$uniqid.', options'.$uniqid.');';
			
          $scripts .= '}';
				break;
	
}			    
          $obj->result ='success';  
          $obj->scripts =$scripts;  		  
          return $obj;
	
	}
	static function widgetvalue($single_data)
	{
	            $return_data = array(); 
			   $config = JFactory::getConfig();
                $database_name = $config->get( 'db' );			   
			    $db = JFactory::getDbo();
				$obj = new stdClass();
		        $obj->result = 'error';
				$obj->error = 'error';
			     if(isset($single_data->style_layout) && $single_data->style_layout=='single_formate'){
					if(isset($single_data->remote_query_value) && $single_data->remote_query_value!='')
					{
						
						preg_match_all('/{tablename\s(.*?)}/i', $single_data->remote_query_value, $matches);
						preg_match_all('/{as\s(.*?)}/i', $single_data->remote_query_value, $match);
						$matches_s = $matches[1];
						$text = $single_data->remote_query_value;
						for($r=0;$r<count($matches_s);$r++){
						$text = preg_replace('{tablename '.$matches_s[$r].'}', $matches_s[$r], $text, 1);	
						}
						$query = str_replace('{','',str_replace('}','',$text));
						$query = str_replace('informationschema','information_schema',$query);
						$query = str_replace('databasename',$database_name,$query);
						if(!empty($single_data->extra_condition))
						$query .= ' where '.$single_data->extra_condition;
						if(isset($single_data->ordering) && !empty($single_data->ordering))
						$query .= ' ORDER BY '.$single_data->column_name.' '.$single_data->ordering;
						if(isset($single_data->limit_value) && !empty($single_data->limit_value))
						$query .= ' LIMIT '.$single_data->limit_value;
					    try
				        {
						$obj->result = 'success';	
						$obj->data = $db->setQuery( $query )->loadObject();
						
						return $obj;
						}
						catch (RuntimeException $e)
						{  
							$obj->result = 'error';
							$obj->error = $e->getMessage();
							return $obj;
						
						}
					}
				}
				elseif(isset($single_data->style_layout) && $single_data->style_layout=='listing_formate'){
					
					if(isset($single_data->remote_query_value) && $single_data->remote_query_value!='')
					{
						preg_match_all('/{tablename\s(.*?)}/i', $single_data->remote_query_value, $matches);
						preg_match_all('/{as\s(.*?)}/i', $single_data->remote_query_value, $match);
						$matches_s = $matches[1];
						$text = $single_data->remote_query_value;
						for($r=0;$r<count($matches_s);$r++){
						$text = preg_replace('{tablename '.$matches_s[$r].'}', $matches_s[$r], $text, 1);	
						}
						$query = str_replace('{','',str_replace('}','',$text));
						$query = str_replace('informationschema','information_schema',$query);
						$query = str_replace('databasename',$config->get( 'db' ),$query);
						if(!empty($single_data->extra_condition))
						$query .= ' where '.$single_data->extra_condition;
						if(isset($single_data->ordering) && !empty($single_data->ordering))
						$query .= ' ORDER BY '.$single_data->column_name.' '.$single_data->ordering;
						if(isset($single_data->limit_value) && !empty($single_data->limit_value))
						$query .= ' LIMIT '.$single_data->limit_value;
						
                         try
				        {
						$obj->result = 'success';	
						$obj->data = $db->setQuery( $query )->loadObjectList();
						
						return $obj;
						}
						catch (RuntimeException $e)
						{  
							$obj->result = 'error';
							$obj->error = $e->getMessage();
							return $obj;
						
						}						
					}
                   elseif(isset($single_data->user_write_query_value) && $single_data->user_write_query_value!='')
					{
						preg_match_all('/{tablename\s(.*?)}/i', $single_data->user_write_query_value, $matches);
						preg_match_all('/{as\s(.*?)}/i', $single_data->user_write_query_value, $match);
						$matches_s = $matches[1];
						$text = $single_data->user_write_query_value;
						for($r=0;$r<count($matches_s);$r++){
						$text = preg_replace('{tablename '.$matches_s[$r].'}', $matches_s[$r], $text, 1);	
						}
						$query = str_replace('{','',str_replace('}','',$text));
						$query = str_replace('informationschema','information_schema',$query);
						$query = str_replace('databasename',$config->get( 'db' ),$query);
						if(!empty($single_data->extra_condition))
						$query .= ' where '.$single_data->extra_condition;
						if(isset($single_data->ordering) && !empty($single_data->ordering))
						$query .= ' ORDER BY '.$single_data->column_name.' '.$single_data->ordering;
						if(isset($single_data->limit_value) && !empty($single_data->limit_value))
						$query .= ' LIMIT '.$single_data->limit_value;
						
                         try
				        {
						$obj->result = 'success';	
						$obj->data = $db->setQuery( $query )->loadObjectList();
						
						return $obj;
						}
						catch (RuntimeException $e)
						{  
							$obj->result = 'error';
							$obj->error = $e->getMessage();
							return $obj;
						
						}						
					}
                    elseif(isset($single_data->existing_database_table) && $single_data->existing_database_table!='')
					{
						/* preg_match_all('/{tablename\s(.*?)}/i', $single_data->existing_database_table, $matches);
						preg_match_all('/{as\s(.*?)}/i', $single_data->existing_database_table, $match);
						$matches_s = $matches[1];
						$text = $single_data->existing_database_table;
						for($r=0;$r<count($matches_s);$r++){
						$text = preg_replace('{tablename '.$matches_s[$r].'}', $matches_s[$r], $text, 1);	
						} */
						/* $query = str_replace('{','',str_replace('}','',$text));
						$query = str_replace('informationschema','information_schema',$query);
						$query = str_replace('databasename',$config->get( 'db' ),$query); */
						$query = 'select `'.implode('`,`',$single_data->listing_column_name).'` from '.$single_data->existing_database_table;
						if(!empty($single_data->extra_condition))
						$query .= ' where '.$single_data->extra_condition;
						if(isset($single_data->ordering_reference_column_name) && !empty($single_data->ordering_reference_column_name))
						$query .= ' ORDER BY '.$single_data->ordering_reference_column_name; 
					
						$query .= isset($single_data->ordering)?' '.$single_data->ordering:' asc'; 
						if(isset($single_data->limit_value) && !empty($single_data->limit_value))
						$query .= ' LIMIT '.$single_data->limit_value;
						
						try
				        {
						$obj->result = 'success';	
						$obj->data =  $db->setQuery( $query )->loadObjectList();	
						
						return $obj;
						}
						catch (RuntimeException $e)
						{  
							$obj->result = 'error';
							$obj->error = $e->getMessage();
							return $obj;
						
						}	
					}						
				}
				
			
		 return $return_data;
	}
	
	static function draw_live_chart($chart_data)
	{
		require_once JPATH_ADMINISTRATOR.'/components/com_vdata/classes/oprating_lib.php';
		 $output = new operating();
		 $output->loading();
		
		$obj = new stdClass();
		
       $output = new WebpageXML(false, null);
	   if (defined('PSI_JSON_ISSUE') && (PSI_JSON_ISSUE)) {
            $json = json_encode(simplexml_load_string(str_replace(">", ">\n", $output->getXMLString()))); // solving json_encode issue
        } else {
            $json = json_encode(simplexml_load_string($output->getXMLString()));
        }
			
			$json = json_decode(str_replace('@attributes','attributes',$json)); 
			$new_free_memory = self::formate_check($json->Memory->attributes->Free);
			$new_free_memory_val = $new_free_memory[0];
			$new_free_memory_text = $new_free_memory[1];
			$new_used_memory = self::formate_check($json->Memory->attributes->Used);
			$new_used_memory_val = $new_used_memory[0];
			$new_used_memory_text = $new_used_memory[1];
			$new_total_memory = self::formate_check($json->Memory->attributes->Total);
			$new_total_memory_val = $new_total_memory[0];
			$new_total_memory_text = $new_total_memory[1];
			$new_percent_memory = $json->Memory->attributes->Percent;
			
			$new_cpu_load= $json->Vitals->attributes->CPULoad;
			$db = JFactory::getDbo();
		
			$uniqid 			= $chart_data->id;
			
			$chart_title		= $chart_data->chart_type;
			$width				= '400px';
			$height				= '400px';
			$chart_detail		= json_decode($chart_data->detail);
			if(empty($chart_detail->existing_database_table)){
				return 'no_chart';
			}
				
			$chart_type = $chart_data->chart_type;
				
    $options = array();
	if($chart_type=='Line Chart')
	$chart_type='LineChart';
	if($chart_type=='Stepped AreaChart')
	$chart_type='SteppedAreaChart';
	if($chart_type=='Combo Charts')
	$chart_type='ComboChart';
   if($chart_type=='Area Chart')
	$chart_type='AreaChart';
	if($chart_type=='Bar Chart')
	$chart_type='BarChart';
	if($chart_type=='Column Chart')
	$chart_type='ColumnChart';
	if($chart_type=='Pie Chart')
	$chart_type='PieChart';
	if($chart_type=='Slice Pie Chart')
	$chart_type='PieChart';
	if($chart_type=='Geo Chart')
	$chart_type='GeoChart';
   if($chart_type=='Map Chart')
	$chart_type='Map';
if($chart_type=='Table Chart')
	$chart_type='Table';
	
	
					
				//if(isset($chart_title_option->backgroundColor))
			    //array_push($options, 'backgroundColor:{fill:"'.$chart_title_option->backgroundColor.'",stroke:"#666",strokeWidth:"10px"}'); 
				 $option =  $opt =  $opt1 = $grid = array();
					
					array_push($options, ' lineWidth: 4');
				if(isset($chart_detail->x_axis) && $chart_detail->x_axis!='')
					array_push($options, 'hAxis: {title: "'.$chart_detail->x_axis.'", textPosition: "none" }');
				else{
					array_push($options, 'hAxis: {textPosition: "none"}');
				}
				   if(isset($chart_detail->y_axis) && $chart_detail->y_axis!='')
					array_push($options, 'vAxis: {title: "'.$chart_detail->y_axis.'"}');
					if(isset($chart_detail->legend) && $chart_detail->legend!='')
					array_push($options, 'legend: {position:"'.$chart_detail->legend.'"}');
		           
					array_push($options, 'chartArea:{top:10,height:"80%",width:"90%"}');
			if(isset($chart_detail->series_column_color)&& $chart_detail->series_column_color!=''){
			$colors = explode(',',$chart_detail->series_column_color);

			if(count($colors)>0)
			array_push($options, 'colors:["'.implode('","',$colors).'"]');
			}
		  

	        array_push($options, 'width: "100%"');
			array_push($options, 'height: "100%"');
			 if(isset($chart_detail->chart_churve) && $chart_detail->chart_churve!='')
					array_push($options, 'curveType: "function"');
			$package = 'corechart';
			$scripts = '';
			if($chart_detail->existing_database_table=='Server Monitoring'){
			    $data_range_limits = isset($chart_detail->data_range_limit)&&$chart_detail->data_range_limit!=''&&$chart_detail->data_range_limit!=0?$chart_detail->data_range_limit:20;
               
				
                  $mem_usage = memory_get_usage(true); 
		          $mem_usage = round($mem_usage/1024,2);
				  
			
					//array_push($options, 'hAxis: { textPosition: "none" }');
					
				
			 $scripts .= '
			  
				var chart_server'. $uniqid .'=0;
				var live_chart_data_server' . $uniqid . ' = new Array();
				var live_chart_cpu_load' . $uniqid . ' = new Array();
				var chart_cpu' . $uniqid . '=0;				 
				var options_server'.$uniqid.'=0;
				var option_cpu'.$uniqid.'="";			 
				var ram_info' . $uniqid . '=2;
				var cpu_info' . $uniqid . '=2;
				var server_memory_response = '.round($new_percent_memory,1).';
				var server_memory_response_text = "%";
				server_cpu_load = '.round($new_cpu_load).';
			 jQuery(document).ready(function() {
				jQuery("base").remove();
                jQuery( "#tabs' . $uniqid . '" ).tabs({
				activate: function (event, ui) { 
				jQuery( "#tabs' . $uniqid . '" ).tabs( "refresh" );
				var active = jQuery("#tabs' . $uniqid . '").tabs("option", "active");
				
				if(jQuery("#tabs' . $uniqid . ' ul>li").eq(active).attr("data-show-chart")=="yes"){
                        if(jQuery("#tabs' . $uniqid . ' ul>li").eq(active).attr("data-chart-for")=="ram"){ 
							chart_server'.$uniqid.'.draw(live_chart_data_server' . $uniqid . ', options'.$uniqid.');
						}
                         else if(jQuery("#tabs' . $uniqid . ' ul>li").eq(active).attr("data-chart-for")=="cpu"){
							 chart_cpu'.$uniqid.'.draw(live_chart_cpu_load' . $uniqid . ', option_cpu'.$uniqid.');
						 }						
					
				}	
				}	
				});
              });';
			  $scripts .= 'if(typeof google !== "undefined") 
				google.load("visualization", "1.1", {"packages":["'. $package.'", "controls"]});	
				google.setOnLoadCallback(drawchart' . $uniqid . ');
				
				function drawchart'.$uniqid.'()
				{
				live_chart_data_server' . $uniqid . ' = new google.visualization.DataTable();
				live_chart_data_server' . $uniqid . '.addColumn("number", "Server Ram Status");
				
				live_chart_data_server' . $uniqid . '.addColumn("number", "Server Ram Status");
				live_chart_data_server' . $uniqid . '.addColumn({type: "string", role: "tooltip"});
				
				live_chart_data_server' . $uniqid . '.addRow([1, parseInt(server_memory_response),"Used Memory : "+server_memory_response+""+server_memory_response_text]);
				
				live_chart_cpu_load' . $uniqid . ' = new google.visualization.DataTable();
				live_chart_cpu_load' . $uniqid . '.addColumn("number", "Server CPU Status");
				
				live_chart_cpu_load' . $uniqid . '.addColumn("number", "Server CPU Status");
				live_chart_cpu_load' . $uniqid . '.addColumn({type: "string", role: "tooltip"});
				
				live_chart_cpu_load' . $uniqid . '.addRow([1, server_cpu_load,"CPU Load : "+server_cpu_load+"%"]);
				
				drawChart'.$uniqid.'();
				}
				function drawChart'.$uniqid.'()
				{
				options'.$uniqid.' = {'.implode(',', $options).',
				title: "Memory Status of Server, Total Memory '.round($new_total_memory_val,1).$new_total_memory_text.'"
                };      
		   option_cpu'.$uniqid.' = {'.implode(',', $options).',
				title: "CPU Status of Server CPU Model '.(isset($json->Hardware->CPU->CpuCore[0]->attributes->Model)?$json->Hardware->CPU->CpuCore[0]->attributes->Model:isset($json->Hardware->CPU->CpuCore->attributes->Model)?$json->Hardware->CPU->CpuCore->attributes->Model:'').'"
        };
			chart_server'.$uniqid.' = new google.visualization.'.$chart_type.'(document.getElementById("widget_sever_2' . $uniqid . '"));
			chart_cpu'.$uniqid.' = new google.visualization.'.$chart_type.'(document.getElementById("widget_sever_3' . $uniqid . '"));
			google.visualization.events.addListener(chart_server'.$uniqid.', "ready", function(){ 
			jQuery( "#tabs" ).tabs( "refresh" );
			
			setTimeout(function(){
					
					ram_info' . $uniqid . ' = ram_info' . $uniqid . '+1;
					
					 if(live_chart_data_server' . $uniqid . '.getNumberOfRows()>'.$data_range_limits.'){
			          live_chart_data_server' . $uniqid . '.removeRow(0); 
		               }
					  live_chart_data_server' . $uniqid . '.addRow([parseInt(ram_info' . $uniqid . '), parseFloat(infor_memory_response),"Used Memory : "+infor_memory_response+" "+infor_memory_response_text]);
					  		   
					
					chart_server'.$uniqid.'.draw(live_chart_data_server' . $uniqid . ', options'.$uniqid.');
					
				}, 10000);
			});	   
			google.visualization.events.addListener(chart_cpu'.$uniqid.', "ready", function(){ 
			setTimeout(function(){
					
					
					cpu_info' . $uniqid . ' = cpu_info' . $uniqid . '+1;
					 
					  
					if(live_chart_cpu_load' . $uniqid . '.getNumberOfRows()>'.$data_range_limits.'){
			          live_chart_cpu_load' . $uniqid . '.removeRow(0); 
		               }
                   
                   live_chart_cpu_load' . $uniqid . '.addRow([parseInt(cpu_info' . $uniqid . '), infor_cpu_response,"CPU Load : "+infor_cpu_response+"%"]);				   
					
					chart_cpu'.$uniqid.'.draw(live_chart_cpu_load' . $uniqid . ', option_cpu'.$uniqid.'); 
				}, 10000);
			
			});
	         chart_server'.$uniqid.'.draw(live_chart_data_server' . $uniqid . ', options_server'.$uniqid.');
			 chart_cpu'.$uniqid.'.draw(live_chart_cpu_load' . $uniqid . ', option_cpu'.$uniqid.');
            			
	        }';
			}
			elseif($chart_detail->existing_database_table=='Server Response Monitoring'){
				$data_range_limit = isset($chart_detail->data_range_limit)&&$chart_detail->data_range_limit!=''&&$chart_detail->data_range_limit!=0?$chart_detail->data_range_limit:20;
				//array_push($options, 'hAxis: { textPosition: "none" }');
					
				
		    $scripts .= ' 
			
		    var live_chart_data_server_response' . $uniqid . ' = new Array();
            var res' . $uniqid . '=0;	
            var ress' . $uniqid . '=3;
            var chart' . $uniqid . '=0;	
            var options'.$uniqid.'=0;
            var timer'.$uniqid.'=0;
            var timer'.$uniqid.'=0;	

            var process_section'.$uniqid.'=0;
            var memory_section'.$uniqid.'=0;				
		
			
			if(typeof google !== "undefined") 
				google.load("visualization", "1.1", {"packages":["'. $package.'", "controls"]});	
				google.setOnLoadCallback(drawchart' . $uniqid . ');
				
				function drawchart'.$uniqid.'() 
				{
				live_chart_data_server_response'.$uniqid.' = new google.visualization.DataTable();
				live_chart_data_server_response'.$uniqid.'.addColumn("number", "Server Respone Time");
				live_chart_data_server_response'.$uniqid.'.addColumn("number", "Server Respone Time");
				live_chart_data_server_response'.$uniqid.'.addColumn({type: "string", role: "tooltip"});
				live_chart_data_server_response'.$uniqid.'.addRow([1,  0, "Server Respone Time 0ms"]);
				drawChartserverresponse'.$uniqid.'(); 
				}
				function drawChartserverresponse'.$uniqid.'()
				{
				options'.$uniqid.' = {'.implode(',', $options).',
				title: "'.$chart_detail->existing_database_table.'"
        };
			chart'.$uniqid.' = new google.visualization.'.$chart_type.'(document.getElementById("widget_'.$uniqid.'"));
				    google.visualization.events.addListener(chart'.$uniqid.', "ready", function(){ 
					
					setTimeout(function(){
					
					ress' . $uniqid . ' = ress' . $uniqid . '+1;
					 if(live_chart_data_server_response' . $uniqid . '.getNumberOfRows()>'.$data_range_limit.'){
			          live_chart_data_server_response' . $uniqid . '.removeRow(0); 
		               }
					  var tooltips_data'.$uniqid.' = "Server Respone Time "+parseFloat(infor_response_time)+" ms";
             live_chart_data_server_response' . $uniqid . '.addRow([parseInt(ress' . $uniqid . '),  parseFloat(infor_response_time),tooltips_data'.$uniqid.']); 
					   
					chart'.$uniqid.'.draw(live_chart_data_server_response'.$uniqid.', options'.$uniqid.');
				}, 10000);
			   });
     
	        chart'.$uniqid.'.draw(live_chart_data_server_response'.$uniqid.', options'.$uniqid.');
            			
	        }';
				
			  }
			  elseif($chart_detail->existing_database_table=='Server CPU Monitoring'){
				$data_range_limit = isset($chart_detail->data_range_limit)&&$chart_detail->data_range_limit!=''&&$chart_detail->data_range_limit!=0?$chart_detail->data_range_limit:20;
				//array_push($options, 'hAxis: { textPosition: "none" }');
				
				//array_push($options, 'curveType: "function"');
		    $scripts .= ' 
			
		    var live_chart_data_server_response' . $uniqid . ' = new Array();
            var res' . $uniqid . '=0;	
            var ress' . $uniqid . '=3;
            var chart' . $uniqid . '=0;	
            var options'.$uniqid.'=0;
            
            server_cpu_load = '.round($new_cpu_load).';
           			
		
			
			if(typeof google !== "undefined") 
				google.load("visualization", "1.1", {"packages":["'. $package.'", "controls"]});	
				google.setOnLoadCallback(drawchart' . $uniqid . ');
				
				function drawchart'.$uniqid.'() 
				{
				live_chart_data_server_response'.$uniqid.' = new google.visualization.DataTable();
				live_chart_data_server_response'.$uniqid.'.addColumn("number", "Server CPU Status");
				live_chart_data_server_response'.$uniqid.'.addColumn("number", "Server CPU Status");
				live_chart_data_server_response'.$uniqid.'.addColumn({type: "string", role: "tooltip"});
				live_chart_data_server_response'.$uniqid.'.addRow([1, server_cpu_load,"CPU Load : "+server_cpu_load+"%"]);
				drawChartserverresponse'.$uniqid.'(); 
				}
				function drawChartserverresponse'.$uniqid.'()
				{
				options'.$uniqid.' = {'.implode(',', $options).',
				title: "'.$chart_detail->existing_database_table.'"
        };
			chart'.$uniqid.' = new google.visualization.'.$chart_type.'(document.getElementById("widget_'.$uniqid.'"));
				    google.visualization.events.addListener(chart'.$uniqid.', "ready", function(){ 
					
					setTimeout(function(){
					
					ress' . $uniqid . ' = ress' . $uniqid . '+1;
					 if(live_chart_data_server_response' . $uniqid . '.getNumberOfRows()>'.$data_range_limit.'){
			          live_chart_data_server_response' . $uniqid . '.removeRow(0); 
		               }
					  var tooltips_data'.$uniqid.' = "CPU Load : "+parseFloat(infor_cpu_response)+"%";
             live_chart_data_server_response' . $uniqid . '.addRow([parseInt(ress' . $uniqid . '),  parseFloat(infor_cpu_response),tooltips_data'.$uniqid.']); 
					   
					chart'.$uniqid.'.draw(live_chart_data_server_response'.$uniqid.', options'.$uniqid.');
				}, 10000);
			   });
     
	        chart'.$uniqid.'.draw(live_chart_data_server_response'.$uniqid.', options'.$uniqid.');
            			
	        }';  
				  
			  }
			  elseif($chart_detail->existing_database_table=='Thread Status'){
				$data_range_limit = isset($chart_detail->data_range_limit)&&$chart_detail->data_range_limit!=''&&$chart_detail->data_range_limit!=0?$chart_detail->data_range_limit:20;
				//array_push($options, 'hAxis: { textPosition: "none" }');
				
		    $scripts .= ' 
			
		    var live_chart_data_server_response' . $uniqid . ' = new Array();
            var res' . $uniqid . '=0;	
            var ress' . $uniqid . '=3;
            var chart' . $uniqid . '=0;	
            var options'.$uniqid.'=0;
            
            server_cpu_load = '.round($new_cpu_load).';
           			
		
			
			if(typeof google !== "undefined") 
				google.load("visualization", "1.1", {"packages":["'. $package.'", "controls"]});	
				google.setOnLoadCallback(drawchart' . $uniqid . ');
				
				function drawchart'.$uniqid.'() 
				{
				live_chart_data_server_response'.$uniqid.' = new google.visualization.DataTable();
				live_chart_data_server_response'.$uniqid.'.addColumn({type: "number", label: "Date"});
				live_chart_data_server_response'.$uniqid.'.addColumn("number", "Thread Running");
				live_chart_data_server_response'.$uniqid.'.addColumn({type: "string", role: "tooltip"});
				live_chart_data_server_response'.$uniqid.'.addColumn("number", "Thread Connected");
				live_chart_data_server_response'.$uniqid.'.addColumn({type: "string", role: "tooltip"});
				live_chart_data_server_response'.$uniqid.'.addRow([1, thread_running,"Thread running on server : "+thread_running, thread_connected,"Thread connect on server :"+thread_connected]);
				drawChartserverresponse'.$uniqid.'(); 
				}
				function drawChartserverresponse'.$uniqid.'()
				{
				options'.$uniqid.' = {'.implode(',', $options).',
				title: "'.$chart_detail->existing_database_table.'"
        };
			chart'.$uniqid.' = new google.visualization.'.$chart_type.'(document.getElementById("widget_'.$uniqid.'"));
				    google.visualization.events.addListener(chart'.$uniqid.', "ready", function(){ 
					
					setTimeout(function(){
					
					ress' . $uniqid . ' = ress' . $uniqid . '+1;
					 if(live_chart_data_server_response' . $uniqid . '.getNumberOfRows()>'.$data_range_limit.'){
			          live_chart_data_server_response' . $uniqid . '.removeRow(0); 
		               }
					 
             live_chart_data_server_response' . $uniqid . '.addRow([ress' . $uniqid . ', thread_running,"Thread running on server : "+thread_running, thread_connected,"Thread connect on server :"+thread_connected]); 
					   
					chart'.$uniqid.'.draw(live_chart_data_server_response'.$uniqid.', options'.$uniqid.');
				}, 10000);
			   });
     
	        chart'.$uniqid.'.draw(live_chart_data_server_response'.$uniqid.', options'.$uniqid.');
            			
	        }';   
				  
			  }
			elseif($chart_detail->existing_database_table=='Queries Status'){
				$data_range_limit = isset($chart_detail->data_range_limit)&&$chart_detail->data_range_limit!=''&&$chart_detail->data_range_limit!=0?$chart_detail->data_range_limit:20;
				//array_push($options, 'hAxis: { textPosition: "none" }');
				
		    $scripts .= ' 
			
		    var live_chart_data_server_response' . $uniqid . ' = new Array();
            var res' . $uniqid . '=0;	
            var ress' . $uniqid . '=3;
            var chart' . $uniqid . '=0;	
            var options'.$uniqid.'=0;
            
            server_cpu_load = '.round($new_cpu_load).';
           			
		
			
			if(typeof google !== "undefined") 
				google.load("visualization", "1.1", {"packages":["'. $package.'", "controls"]});	
				google.setOnLoadCallback(drawchart' . $uniqid . ');
				
				function drawchart'.$uniqid.'() 
				{
				live_chart_data_server_response'.$uniqid.' = new google.visualization.DataTable();
				live_chart_data_server_response'.$uniqid.'.addColumn({type: "number", label: "Date"});
				live_chart_data_server_response'.$uniqid.'.addColumn("number", "Thread Running");
				live_chart_data_server_response'.$uniqid.'.addColumn({type: "string", role: "tooltip"});
				live_chart_data_server_response'.$uniqid.'.addColumn("number", "Thread Connected");
				live_chart_data_server_response'.$uniqid.'.addColumn({type: "string", role: "tooltip"});
				live_chart_data_server_response'.$uniqid.'.addRow([1, thread_running,"Thread running on server : "+thread_running, thread_connected,"Thread connect on server :"+thread_connected]);
				drawChartserverresponse'.$uniqid.'(); 
				}
				function drawChartserverresponse'.$uniqid.'()
				{
				options'.$uniqid.' = {'.implode(',', $options).',
				title: "'.$chart_detail->existing_database_table.'"
        };
			chart'.$uniqid.' = new google.visualization.'.$chart_type.'(document.getElementById("widget_'.$uniqid.'"));
				    google.visualization.events.addListener(chart'.$uniqid.', "ready", function(){ 
					
					setTimeout(function(){
					
					ress' . $uniqid . ' = ress' . $uniqid . '+1;
					 if(live_chart_data_server_response' . $uniqid . '.getNumberOfRows()>'.$data_range_limit.'){
			          live_chart_data_server_response' . $uniqid . '.removeRow(0); 
		               }
					 
             live_chart_data_server_response' . $uniqid . '.addRow([ress' . $uniqid . ', thread_running,"Thread running on server : "+thread_running, thread_connected,"Thread connect on server :"+thread_connected]); 
					   
					chart'.$uniqid.'.draw(live_chart_data_server_response'.$uniqid.', options'.$uniqid.');
				}, 10000);
			   });
     
	        chart'.$uniqid.'.draw(live_chart_data_server_response'.$uniqid.', options'.$uniqid.');
            			
	        }';   
				  
			  }  
          return $scripts;
	
		
	}
static function formate_check($bytes)
{
	$show = ''; $show_text='';
	if ($bytes > pow(1024, 5)) {
            $show .= round($bytes / pow(1024, 5), 2);
            $show_text .= JText::_('PiB');
        }
        else {
            if ($bytes > pow(1024, 4)) {
                $show .= round($bytes / pow(1024, 4), 2);
                $show_text .= JText::_('TiB');
            }
            else {
                if ($bytes > pow(1024, 3)) {
                    $show .= round($bytes / pow(1024, 3), 2);
                    $show_text .= JText::_('GiB');
                }
                else {
                    if ($bytes > pow(1024, 2)) {
                        $show .= round($bytes / pow(1024, 2), 2);
                        $show_text .= JText::_('MiB');
                    }
                    else {
                        if ($bytes > pow(1024, 1)) {
                            $show .= round($bytes / pow(1024, 1), 2);
                            $show_text .= JText::_('KiB');
                        }
                        else {
                            $show .= $bytes;
                            $show_text .= JText::_('B');
                        }
                    }
                }
            }
        }
	$show_array = array();
	$show_array[0] =$show;
	$show_array[1] =$show_text;
	return $show_array;
	
}

}

?>
