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
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
defined('_JEXEC') or die();

jimport('joomla.application.component.modellist');

class VdataControllerVdata extends VdataController
{
    
   function __construct()
	{
		parent::__construct();
		$this->_dbs = JFactory::getDbo(); 
	   JFactory::getApplication()->input->set( 'view', 'vdata' );
	   $this->data_base = JFactory::getDbo();

	}
	
	function display($cachable = false, $urlparams = false){
		
		$user = JFactory::getUser();
		$canViewDashboard = $user->authorise('core.access.dashboard', 'com_vdata');
		
		if(!$canViewDashboard){
			$msg = JText::_( 'ALERT_AUTHORIZATION_ERROR' );
			$this->setRedirect( 'index.php', $msg , 'error');
		}
			
		parent::display();
	}
	
	function update_profile_ordering()
	{
		$this->model = $this->getModel('vdata');
		
		$this->model->updateordering();

	}
	function delete_widget()
	{
	   $model = $this->getModel('vdata');
		
		if($model->delete()) {
		$this->update_dashboard();	
			
		} 
		
	}
	function update_selected_keyword()
	{
			$keyword = JFactory::getApplication()->input->get('keyword', '','RAW');
			$type = JFactory::getApplication()->input->get('type', '');
			$url = 'http://www.joomlawings.com/index.php';
			$obj = new stdClass();
			$obj->result = 'error';
			$ch = curl_init(); 

			$postdata = array("keyword"=>$keyword, "type"=>$type, "option"=>"com_vdata", "task"=>"update_keyword", "token"=>JSession::getFormToken());
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$result = json_decode(curl_exec($ch));
			$obj->result = $result->result;
			jexit(json_encode($obj));
		
	}
	function update_dashboard_new(){
		$this->model = $this->getModel('vdata');
		$this->profiles = $this->model->getProfiles();
		$this->configuration = $this->model->getConfiguration();
		$this->update_item = JFactory::getApplication()->input->get('status_action','');
		$status_index = JFactory::getApplication()->input->get('status_index','');
		$status_index = $status_index==0?count($this->profiles)+1:($status_index+1);
		$this->items = $this->model->getProfilesname();
		$this->profile = $this->model->getProfile($this->update_item);
		$this->plugins = $this->model->getPlugins();
		$row_siz = $this->configuration->row_limit;
	    $one_column_size = 100/$this->configuration->column_limit;
		require_once JPATH_COMPONENT.'/classes/drawchart.php';
		$prev_current_width ='';
	    $future_width ='';
	    $live_data_query = array('Server Response Monitoring','Server CPU Monitoring','Server Monitoring','Thread Status','Queries Status');
		$data = new stdClass();
		$data->result = 'error';
		$html ='';
		$script =''; 
		$li =''; 
		 $k =1;
		$profile = json_decode($this->profile->detail);
		$current_width ='';
		$column_widget_width_value = '';
		$row_widget_height_value = '';
		$row_widget_height_value_chart = '';
		if(isset($profile->box_column) && $profile->box_column)
		{
		 $column_widget_width_value = (($profile->box_column*$one_column_size)-2).'%';	
		}
		
	    if(isset($profile->box_row) && $profile->box_row)
		{
		$row_widget_height_value =	(($profile->box_row*$row_siz)-20).'px';
		$row_widget_height_value_chart = (($profile->box_row*$row_siz)-20);
		}
		$sub_current_width =0;$box_class_name='';
		if($column_widget_width_value!='' && $row_widget_height_value!='') 
		  $style = ' style="width:'.$column_widget_width_value.';height:'.$row_widget_height_value.';"';
	      $style_update = $column_widget_width_value.':'.$row_widget_height_value;
		if(isset($profile->box_layout) && $profile->box_layout=="onebox"){$sub_current_width=1;$box_class_name=' onebox';}
		elseif(isset($profile->box_layout) && $profile->box_layout=="twobox"){$sub_current_width=2;$box_class_name=' twobox';}
		elseif(isset($profile->box_layout) && $profile->box_layout=="threebox"){$sub_current_width=3;$box_class_name=' threebox';}
		elseif(isset($profile->box_layout) && $profile->box_layout=="fourbox"){$sub_current_width=4;$box_class_name=' fourbox';}
		elseif(isset($profile->box_layout) && $profile->box_layout=="fivebox"){$sub_current_width=5;$box_class_name=' fivebox';}
		elseif(isset($profile->box_layout) && $profile->box_layout=="sixbox"){$sub_current_width=5;$box_class_name=' sixbox';}
			if(empty($prev_current_width))
			{
			$prev_current_width = $current_width;	
			}
			$current_width ='';
	  if(isset($profile->style_layout) && $profile->style_layout=='single_formate')
	  { 
	    $li .= '<li class="common_profile_main single_formate num'.($status_index);
				if($this->profile->datatype_option=='profile')
				{
				$li .= ' profile_widget';
				} 
		 $li .= $box_class_name.'" data-ordering-profile="'.$this->profile->id.':'.$this->profile->ordering.'"'.$style.'><div class="panel_header">'; 
		$html .= '<div class="panel_header">';
		 if(isset($this->profile->name)&& $this->profile->name!='')
		  $html .='<span class="profile_name">'.$this->profile->name.'</span>';
	
		$html .='<span class="delete-widget" onclick="delete_widget(this);" data-widget-id="'.$this->profile->id.'"><i class="icon-delete"></i></span>
		<span class="edit-widget" onclick="edit_widget(this);" data-widget-id="'.$this->profile->id.'"><i class="icon-edit"></i></span></div><div class="profile_mid_data">';
	 
		
		  $single_data = chart_draw::widgetvalue($profile);
		  $test_v = $profile->style_layout_editor;
		  
		  
		 if($single_data->result=='success'){
			 $object_array = get_object_vars($single_data->data);
		     $object_array = count($object_array)>0?array_keys($object_array):array();
			 $regex		= '/{(.*?)}/i';
		     preg_match_all($regex, $profile->style_layout_editor, $matches, PREG_SET_ORDER);
			foreach ($matches as $match)
			{
			foreach ($single_data->data as $key=>$value)
			{
			     
				 if(isset($profile->style_layout_editor) && $profile->style_layout_editor!=''&&$value!=null&&$match[1]==$key)
				 {	
				$test_v = preg_replace("|$match[0]|", '<span class="innser_single_trigger" data-profile-ids="profile_'.$this->profile->id.'" id="inner_single_'.trim(preg_replace('/\s*\([^)]*\)/', '', $key)).'">'.$value.'</span>', $test_v, 1);
	            
				
				}
				elseif(isset($profile->style_layout_editor) && $profile->style_layout_editor!=''&&$value==null){
				$test_v = preg_replace("|$match[0]|", '<span class="innser_single_trigger" data-profile-ids="profile_'.$this->profile->id.'" id="inner_single_'.trim(preg_replace('/\s*\([^)]*\)/', '', $key)).'">'.$value.'</span>', $test_v, 1);	
				}
            }
          }			
           $html .= $test_v;
		    }
			else{
			$html .= $single_data->error;	
			}
		
		  $html .='</div>';
		  //$html .='</li>';
		  
	  }
	  elseif(isset($profile->style_layout) && $profile->style_layout=='listing_formate')
	  { 
		    $k =1;
			if($profile->existing_database_table=='vData Profiles')
			{
		 
		 $li .= '<li class="common_profile_main listing_profiles num'.($status_index);
		 if($this->profile->datatype_option=='profile'){
			$li .= ' profile_widget';}
			$li .= $box_class_name.'" data-ordering-profile="'.$this->profile->id.':'.$this->profile->ordering.'"'.$style.'>';
	      $html .= '<div class="panel_header"><span class="profile_name">';
	     if(isset($this->profile->name)&& $this->profile->name!='')
		$html .= ''.$this->profile->name.'';
	
	$html .= '</span><span class="delete-widget" onclick="delete_widget(this);" data-widget-id="'.$this->profile->id.'"><i class="icon-delete"></i></span>
	<span class="edit-widget" onclick="edit_widget(this);" data-widget-id="'.$this->profile->id.'"><i class="icon-edit"></i></span></div>
	<div class="profile_mid_data common_class_listing_format">';
	
	$html .= '<ul class="profile_mid_data_inner">'; 
		  
		  $c=0;if(isset($profile->profile_creation_button)&&$profile->profile_creation_button==1) {
			 $c++; 
			
		  $html .= '<li class="common_profile num1 profile-section-listing" data-ordering-profile="'.$this->profile->id.':'.$this->profile->ordering.'" style="'.$sub_current_width.'"><a href="index.php?option=com_vdata&view=profiles&layout=wizard"><img src="'.Juri::root().'media/com_vdata/images/add-new-profile.png" alt="'.JText::_('CONFIG').'" /><span class="dashboard_profile_name">'.JText::_('COM_VDATA_CREAT_PROFILE').'</span></a></li>';
		 
		 }
		 
			for ($i=0, $n=count( $this->items ); $i < $n; $i++)
			{
			
			$row = $this->items[$i];
				if(in_array(JText::_("COM_VDATA_SELECT_ALL_PROFILES"),$profile->profiles) || in_array($row->id,$profile->profiles)){
				
				
				
			   $html .= '<li class="common_profile profile_mid_data_inner num'.(++$c).' profile-section-listing" data-ordering-profile="'.$row->id.':'.$row->ordering.'" style="'.$sub_current_width.'">';
					
					 if($row->iotype) {
					$html .= '<a href="index.php?option=com_vdata&view=profiles&task=export&profileid='.$row->id.'"><img src="'. Juri::root().'media/com_vdata/images/export_profile.png" alt="'.JText::_('CONFIG').'" /><span class="dashboard_profile_name">'.JText::_($row->title).'</span></a>';
					}else { 
					$html .= '<a href="index.php?option=com_vdata&view=import&profileid='.$row->id.'"><img src="'.Juri::root().'media/com_vdata/images/import_profile.png" alt="'.JText::_('CONFIG').'" /><span class="dashboard_profile_name">'.JText::_($row->title).'</span></a>';
					
					 } 
					
				   
				$html .= '</li>';
				
				$k = 1 - $k;
				}
			}
         $html .= '</ul>';			
		}
		elseif($profile->existing_database_table=='vData Plugins'){
			$li .= '<li class="common_profile_main listing_profiles num'.($status_index);
			if($this->profile->datatype_option=='profile'){
				$li .= ' profile_widget';}
				$li .= $box_class_name.'" data-ordering-profile="'.$this->profile->id.':'.$this->profile->ordering.'"'.$style.'>';
	$html .= '<div class="panel_header"><span class="profile_name">';
	 if(isset($this->profile->name)&& $this->profile->name!='')
		$html .= ''.$this->profile->name.'';
	
	$html .= '</span><span class="delete-widget" onclick="delete_widget(this);" data-widget-id="'.$this->profile->id.'"><i class="icon-delete"></i></span>
	<span class="edit-widget" onclick="edit_widget(this);" data-widget-id="'.$this->profile->id.'"><i class="icon-edit"></i></span></div>
	<div class="profile_mid_data common_class_listing_format">';
	
	$html .= '<ul class="profile_mid_data_inner">';
		  
		  
				for ($i=0, $n=count( $this->plugins ); $i < $n; $i++)
				{

					$row = $this->plugins[$i];
					if(in_array(JText::_("COM_VDATA_SELECT_ALL_PLUGINS"),$profile->plugins) || in_array($row->extension_id,$profile->plugins)){

					//$lang->load($this->items[$i]->extension . '.sys', JPATH_SITE.'/plugins/vdata/'.$this->items[$i]->element, null, false, false);

				

					$html .= '<li class="common_profile_plugin profile_mid_data_inner num'.($i+1).'" data-ordering-profile="'.$row->extension_id.':'.$row->name.'" style="'.$sub_current_width.'">';


					$html .= '<a href="index.php?option=com_vdata&view=profiles&extension_id='.$row->extension_id.'&task=edit&cid[]=0"><img src="'.Juri::root().'media/com_vdata/images/export_profile.png" alt="'.JText::_('CONFIG').'" /><span class="dashboard_profile_name">'. JText::_($row->name).'</span></a>';


					$html .= '</li>';
					
					$k = 1 - $k;
					}
				}
		 $html .= '</ul>'; 	
		}
	  else {
			
			$li .= '<li class="common_profile_main listing_profiles listing_formate num'.($status_index);
		   if($this->profile->datatype_option=='profile'){
			  $li  .= ' profile_widget';}
			   $li .= $box_class_name.'" data-ordering-profile="'.$this->profile->id.':'.$this->profile->ordering.'"'.$style.'><div class="panel_header">'; 
			$html .= '<div class="panel_header"><span class="profile_name">';
			if(isset($this->profile->name)&& $this->profile->name!='')
			$html .= ''.$this->profile->name.'';
			$html .= '</span><span class="delete-widget" onclick="delete_widget(this);" data-widget-id="'.$this->profile->id.'"><i class="icon-delete"></i></span>
			<span class="edit-widget" onclick="edit_widget(this);" data-widget-id="'.$this->profile->id.'"><i class="icon-edit"></i></span></div><div class="profile_mid_data common_class_listing_format">';
		  
			  $single_data = chart_draw::widgetvalue($profile);
				if(isset($single_data->result)&&$single_data->result=='error')
				{ 
				$html .= $single_data->error;
				}
				else
		       { 
		       $single_data = $single_data->data;
			  $object_array = isset($single_data[0])?get_object_vars($single_data[0]):array();
			  $object_array = count($object_array)>0?array_keys($object_array):array();
			   $html .= '<div class="listing_layout_others" style="height:'.($row_widget_height_value_chart-40).'px"><table class="adminlist table table-hover listing_info listing_info_'.$this->profile->id.' profile_mid_data_inner" width="100%"><thead><tr>';
				for ($s = 0;$s<count($object_array);$s++)
				{
					
					 $html .= '<th>'.$object_array[$s].'</th>';
					
				
				}			
			   $html .= '</thead>';
			   $g = 0;
				for ($l=0; $l < count( $single_data ); $l++)
				{
					$listing = $single_data[$l];
					$html .= '<tr>';
					foreach($listing as $listings){
					$html .= '<td>'.$listings.'</td>';	
					}
				$html .= '</tr>';	
				}
			  $html .= '</table></div>';
			  }
			 $html .= '</div>';
			 //$html .= '</li>';
	  }	
	  }
	  else
	  { 
			
	
	 $li .= '<li class="common_profile_main num'.($status_index);
	if($this->profile->datatype_option=='profile')
	 {
		 $li .= ' profile_widget';
	 }
	 $li .= $box_class_name.'" data-ordering-profile="'.$this->profile->id.':'.$this->profile->ordering.'"'.$style.'>';
	$html .= '<div class="panel_header"><span class="profile_name">';
	 if(isset($this->profile->name)&& $this->profile->name!='')
		$html .= ''.$this->profile->name.'';
	
	$html .='</span><span class="delete-widget" onclick="delete_widget(this);" data-widget-id="'.$this->profile->id.'"><i class="icon-delete"></i></span>
	<span class="edit-widget" onclick="edit_widget(this);" data-widget-id="'.$this->profile->id.'"><i class="icon-edit"></i></span></div><div class="profile_mid_data"><ul>';
	
	if($this->profile->datatype_option=='profile'){
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)
    {
        
		$row = $this->items[$i];
		if((isset($profile->show_all_profile) && $profile->show_all_profile==1) || in_array($row->id,$profile->existing_database_table)){
		
       $html .='<li class="common_profile num'.($i+1).' profile-section-listing" data-ordering-profile="'.$row->id.':'.$row->ordering.'" style="'. $sub_current_width.'">';
	        
          if($row->iotype) : 
            $html .='<a href="index.php?option=com_vdata&view=profiles&task=export&profileid='.$row->id.'"><img src="'. Juri::root().'media/com_vdata/images/export_profile.png" alt="'.JText::_('CONFIG').'" /><span class="dashboard_profile_name">'.JText::_($row->title).'</span></a>';
            else : 
			$html .= '<a href="index.php?option=com_vdata&view=import&profileid='.$row->id.'"><img src="'.Juri::root().'media/com_vdata/images/import_profile.png" alt="'.JText::_('CONFIG').'" /><span class="dashboard_profile_name">'.JText::_($row->title).'</span></a>';
			endif; 
            
           
        $html .= '</li>';
       
        $k = 1 - $k;
       }
      }
	}
   elseif($this->profile->datatype_option=='custom_plugin')
   {
    for ($i=0, $n=count( $this->plugins ); $i < $n; $i++)
    {
        
		$row = $this->plugins[$i];
		if((isset($profile->show_all_plugin) && $profile->show_all_plugin==1) || in_array($row->extension_id,$profile->existing_database_table)){
		
		//$lang->load($this->items[$i]->extension . '.sys', JPATH_SITE.'/plugins/vdata/'.$this->items[$i]->element, null, false, false);
		
       $html .= '<li class="common_profile_plugin num'.($i+1).'" data-ordering-profile="'.$row->extension_id.':'.$row->name.'" style="'.$sub_current_width.'">';
	        
            
            $html .= '<a href="index.php?option=com_vdata&view=profiles&extension_id='.$row->extension_id.'&task=edit&cid[]=0"><img src="media/com_vdata/images/export_profile.png" alt="'.JText::_('CONFIG').'" /><span class="dashboard_profile_name">'. JText::_($row->name).'</span></a>';
            
           
        $html .= '</li>';
       
        $k = 1 - $k;
       }
      }
	}	
   elseif($this->profile->datatype_option=='predefined')
   { 
	 $style= $row_widget_height_value_chart!=''?' style="height:'.($row_widget_height_value_chart-68).'px"':'';
	  if(isset($this->profile->chart_type) && $this->profile->chart_type!=''&& !in_array($profile->existing_database_table,$live_data_query))
	  {
	      
			$script_data = chart_draw::draw_view_chart($this->profile);
			 if($script_data->result=='success'){ 
			  $html .=' <li class="chart_profile num'.($status_index).'" data-ordering-profile="'.$this->profile->id.':'.$this->profile->ordering.'" data-widget-label="'.strtolower(str_replace(" ","_",$profile->existing_database_table)).$this->profile->id.'">';
			$html .=' <div id="widget_'.$this->profile->id.'" class="widget_chart" data-profile-id="drawchart'.$this->profile->id.'" '.$style.'></div></li>';
			
			 $script .= '<script type="text/javascript"> ';
			 $script .= $script_data->scripts;
			 $script .= '</script>';
			 
			 
			  }
			  elseif($script_data->result=='error'){
		     $script .= $script_data->error; 
	           } 
			  
          }
		elseif(in_array($profile->existing_database_table,$live_data_query)){
		 $script_data = chart_draw::draw_live_chart($this->profile);
		  if($profile->existing_database_table=='Server Monitoring'){
		  
   $html .= '<div id="tabs'.$this->profile->id.'" class="widget_chart" data-profile-id="drawchart'.$this->profile->id.'">';
     $html .= '<ul>';
       $html .= '<li data-chart-id="'.$this->profile->id.'" data-show-chart="no"><a href="#tabs-1">Processes</a></li>';
       $html .= '<li data-chart-id="'.$this->profile->id.'" data-show-chart="yes" data-chart-for="ram"><a href="#tabs-2">RAM Status</a></li>';
       $html .= '<li data-chart-id="'.$this->profile->id.'" data-show-chart="yes" data-chart-for="cpu"><a href="#tabs-3">CPU Status</a></li>';
     $html .= '</ul>';
    $html .= '<div id="tabs-1">';
    $html .= '<table class="adminlist table table-striped table-hover" width="100%"><tr><th>Process Name</th><th>Process Type</th><th>Time Taken</th></tr></table></div><div id="tabs-2"  style="width: 100%; height: 80%;" data-chart-id="'.$this->profile->id.'" data-show-chart="yes"><div id="widget_sever_2'.$this->profile->id.'"  style="width: 100%; height: 80%;"></div></div><div id="tabs-3" data-chart-id="'.$this->profile->id.'" data-show-chart="yes"><div id="widget_sever_3'.$this->profile->id.'"  style="width: 100%; height: 80%;"></div></div></div>'; 
		
		             $script .= '<script type="text/javascript"> ';
					 $script .=  $script_data;
					  $script .= '</script>';
		  }else
		        {
			  
					 if($script_data!='no_chart')
					{ 
				    
					  $html .= '<li class="chart_profile num'.($status_index).'"data-widget-label="'.strtolower(str_replace(" ","_",$profile->existing_database_table)).$this->profile->id.'" data-ordering-profile="'.$this->profile->id.':'.$this->profile->ordering.'">';
					  $html .= '<div id="widget_'.$this->profile->id.'" class="widget_chart" data-profile-id="drawchart'.$this->profile->id.'"  style="width: 100%; height: 80%;"></div></li>';
					
					 $script .=  '<script type="text/javascript"> ';
					 $script .=  $script_data;
					 $script .=  '</script>';
					}
			 
			  }		
		} 
	  else { 
		 $html .= ' <li class="chart_profile num'.($status_index).'" data-widget-label="'.strtolower(str_replace(" ","_",$profile->existing_database_table)).$this->profile->id.'" data-ordering-profile="'.$this->profile->id.':'.$this->profile->ordering.'" >'.$this->log_information($this->profile,$response_data).'</li>';
	     }	  
      
   }
   elseif($this->profile->datatype_option=='writequery'){
	 $style= $row_widget_height_value_chart!=''?' style="height:'.($row_widget_height_value_chart-68).'px"':'';  
	$script_data = chart_draw::draw_view_chart($this->profile);
     if($script_data->result=='success')
	 { 
	 $html .= ' <li class="chart_profile num'.($status_index).'" data-widget-label="'.strtolower(str_replace(" ","_",$profile->existing_database_table)).$this->profile->id.'" data-ordering-profile="'.$this->profile->id.':'.$this->profile->ordering.'">';
	 $html .= ' <div id="widget_'.$this->profile->id.'" class="widget_chart" data-profile-id="drawchart'.$this->profile->id.'" '.$style.'></div></li>';
	
	$script .= '<script type="text/javascript"> ';
	$script .=  $script_data->scripts;
	$script .=  '</script>';  
	 } 
     elseif($script_data->result=='error'){
		$script .= $script_data->error; 
	 }		 
   }
   
	$html .= '</ul></div>'; 
	}
        $data->html = $html;
		$data->script = $script;
		$data->li = $li;
		$data->style = $style_update;
		$data->result = 'success';
		jexit(json_encode($data));	
	}
	function update_dashboard(){
	    $this->model = $this->getModel('vdata');
		$this->items = $this->model->getItems();
		$this->profiles = $this->model->getProfiles();
		$this->plugins = $this->model->getPlugins();
		$version = new JVersion;
		$joomla = $version->getShortVersion();
		$jversion = substr($joomla,0,3);
		
		$data = new stdClass();
		$data->result = 'error';
		ob_start();
		
		$this->sidebar ='';
			$this->addsidemenu('vdata');
			if($jversion>=3.0)
			{
			$this->sidebar = JHtmlSidebar::render(); 
			}
		require(JPATH_ADMINISTRATOR.'/components/com_vdata/views/vdata/tmpl/default.php');
		require_once JPATH_COMPONENT.'/classes/drawchart.php';
		$document =  JFactory::getDocument();
		$document->addScript("https://www.google.com/jsapi");
		$html = mb_convert_encoding(ob_get_contents(), 'UTF-8');
		
		ob_end_clean();
		
		
		
		$data->html = $html;
		$data->result = 'success';
		jexit(json_encode($data));	
		
	}
	function addsidemenu($addmenu)
	{
		JHtmlSidebar::addEntry(
			JText::_('DASHBOARD'),
			'index.php?option=com_vdata&view=vdata',
			$addmenu == 'vdata');
		
		JHtmlSidebar::addEntry(
			JText::_('CONFIG'),
			'index.php?option=com_vdata&view=config',
			$addmenu == 'config');
		JHtmlSidebar::addEntry(
			JText::_('PROFILES'),
			'index.php?option=com_vdata&view=profiles',
			$addmenu == 'profiles');
		JHtmlSidebar::addEntry(
			JText::_('IMPORT'),
			'index.php?option=com_vdata&view=import',
			$addmenu == 'import');
		JHtmlSidebar::addEntry(
			JText::_('EXPORT'),
			'index.php?option=com_vdata&view=export',
			$addmenu == 'export');
		JHtmlSidebar::addEntry(
			JText::_('CRON_FEED'),
			'index.php?option=com_vdata&view=schedules',
			$addmenu == 'schedules');	
			
		JHtmlSidebar::addEntry(
			JText::_('HEXDATA_PHP_MY_ADMIN'),
			'index.php?option=com_vdata&view=quick',
			$addmenu == 'quick');
		JHtmlSidebar::addEntry(
			JText::_('COM_VDATA_LOGS'),
			'index.php?option=com_vdata&view=logs',
			$addmenu == 'logs');
	}
	
	function live_chart_data_test()
	{ 
		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache');
		
		 require_once JPATH_COMPONENT.'/classes/oprating_lib.php';
		 $output = new operating();
		 $output->loading();
		 $output = new WebpageXML(false, null);
	     $this->model = $this->getModel('vdata');
		 $this->profiles = $this->model->getProfiles();
		 $this->items = $this->model->getItems();
		 $this->plugins = $this->model->getPlugins();
		 
		 require_once JPATH_COMPONENT.'/classes/drawchart.php';
		 $datas = array();
		 
		
		 $data2 = $this->_dbs->setQuery('show processlist')->loadObjectList();
		 array_push($datas,$data2);
		 
		 $data1 = $this->_dbs->setQuery('show status where Variable_name in ("Com_select","Com_delete","Com_insert","Com_drop_table","Com_show_create_table","Innodb_rows_inserted","Queries","Slow_queries","Threads_connected","Threads_running")')->loadObjectList();
		 
		 array_push($datas,$data1);
		if (defined('PSI_JSON_ISSUE') && (PSI_JSON_ISSUE)) {
            $json = json_encode(simplexml_load_string(str_replace(">", ">\n", $output->getXMLString()))); 
        } else {
            $json = json_encode(simplexml_load_string($output->getXMLString()));
        }
			$json = str_replace('@attributes','attributes',$json);
			$json = json_decode($json);
			$cpu = new stdClass();
			$cpu->cpu_info = isset($json->Hardware->CPU->CpuCore[0]->attributes->Model)?$json->Hardware->CPU->CpuCore[0]->attributes->Model.' Cpu Speed'.$json->Hardware->CPU->CpuCore[0]->attributes->CpuSpeed:isset($json->Hardware->CPU->CpuCore->attributes->Model)?$json->Hardware->CPU->CpuCore->attributes->Model:'';
			$cpu->cpu_load = isset($json->Vitals->attributes->CPULoad)?$json->Vitals->attributes->CPULoad:'';
			$cpu->memory_status = isset($json->Memory->attributes->Percent)?$json->Memory->attributes->Percent:'';
			array_push($datas,$cpu); 
		
			$host =  $_SERVER['SERVER_NAME'];
			$port = 80;
			$timeout = 10;

			$start_time = microtime(TRUE);
			$status = @fsockopen($host, $port, $errno, $errstr, 10); 

			$end_time = microtime(TRUE);
			$time_taken = $end_time - $start_time;
			$time_taken = round($time_taken,5)*1000;
			array_push($datas,$time_taken);
			// array_push($datas,$status);
			$profile_outer = array();
			$profile_listing = array();
			$other_profile_listing = array();
		    for($j=0;$j<count($this->profiles);$j++){
			 $profile = json_decode($this->profiles[$j]->detail);
			 if(isset($profile->style_layout) && $profile->style_layout=='single_formate')
			 {
				  $single_data = chart_draw::widgetvalue($profile);
					if(isset($single_data->result)&&$single_data->result=='error'){

					array_push($profile_listing, $single_data->error);
					}
					else
					{
						$single_data = $single_data->data;

						$object_array = get_object_vars($single_data);
						$object_array = count($object_array)>0?array_keys($object_array):array();

						foreach ($single_data as $key=>$value)
						{

						if($value==null)	
						array_push($profile_outer, 0);
						else
						array_push($profile_outer, $value);

						}
					}
			 }
			 elseif(isset($profile->style_layout) && $profile->style_layout=='listing_formate')
			 {    $profile_listing_detail = array(); 
		           if($profile->existing_database_table=='vData Profiles'){
						
						for ($i=0, $n=count( $this->items ); $i < $n; $i++)
							{

								$row = $this->items[$i];
								$listing = '';
								if(in_array(JText::_("COM_VDATA_SELECT_ALL_PROFILES"),$profile->profiles) || in_array($row->id,$profile->profiles))
								{
									 $listing .= '<li class="common_profile profile_mid_data_inner num'.($i+1).' profile-section-listing" data-ordering-profile="'.$row->id.':'.$row->ordering.'" style="'.$sub_current_width.'">';
									
									 if($row->iotype)
									 {
									$listing .= '<a href="index.php?option=com_vdata&view=profiles&task=export&profileid='.$row->id.'"><img src="'.Juri::root().'media/com_vdata/images/export_profile.png" alt="'.JText::_('CONFIG').'" /><span class="dashboard_profile_name">'.JText::_($row->title).'</span></a>';
									 }
									 else
									 {
									$listing .= '<a href="index.php?option=com_vdata&view=import&profileid='.$row->id.'"><img src="'.Juri::root().'media/com_vdata/images/import_profile.png" alt="'.JText::_('CONFIG').'" /><span class="dashboard_profile_name">'.JText::_($row->title).'</span></a>';

									 }
									$listing .= '</li>';

								}
								 array_push($profile_listing_detail, $listing);
							}	
				   }
				   elseif($profile->existing_database_table=='vData Plugins'){
					for ($i=0, $n=count( $this->plugins ); $i < $n; $i++)
							{
                               
								$row = $this->plugins[$i];
								$listing = '';
								if(in_array(JText::_("COM_VDATA_SELECT_ALL_PLUGINS"),$profile->plugins) || in_array($row->extension_id,$profile->plugins))
								{

								$listing .= '<li class="common_profile_plugin profile_mid_data_inner num'.($i+1).'" data-ordering-profile="'. $row->extension_id.':'.$row->name.'" style="'.$sub_current_width.'"><a href="index.php?option=com_vdata&view=profiles&extension_id='.$row->extension_id.'&task=edit&cid[]=0"><img src="'.Juri::root().'media/com_vdata/images/export_profile.png" alt="'.JText::_('CONFIG').'" /><span class="dashboard_profile_name">'.JText::_($row->name).'</span></a></li>';
								
							  }
							  array_push($profile_listing_detail, $listing);
						     }    
				   }
 				   else
				   {
 					 
					 $single_data = chart_draw::widgetvalue($profile);
					if(isset($single_data->result)&&$single_data->result=='error'){
					
					  array_push($profile_listing, $single_data->error);
					}
					else
					{
					$single_data = $single_data->data;
					$lsting = '<div class="listing_layout_others" style="height:'.($row_widget_height_value_chart-60).'px"><table class="adminlist table table-hover listing_info listing_info_'.$this->profiles[$j]->id.' profile_mid_data_inner" width="100%"><thead><tr>';
					$object_array = isset($single_data[0])?get_object_vars($single_data[0]):array();
		            $object_array = count($object_array)>0?array_keys($object_array):array();
					for ($s = 0;$s<count($object_array);$s++)
					{

					$lsting .='<th>'.$object_array[$s].'</th>';


					}
                      array_push($profile_listing_detail, $lsting);					
					//array_push($profile_listing, $lsting);
					for ($l=0; $l < count( $single_data ); $l++)
					{
						$listing = $single_data[$l];
						$lsting .= '<tr>';
								foreach($listing as $listings){
								$lsting .= '<td>'.$listings.'</td>';	
								}
						$lsting .= '</tr>';	
				   } 
				   array_push($profile_listing_detail, $lsting);	
				  
                   }				   
			  }
			  array_push($profile_listing, $profile_listing_detail);
			} 
			
		 }
		 array_push($datas,$profile_outer);
		 array_push($datas,$profile_listing);
			$datas = json_encode($datas);
			echo "data:".$datas."\n\n";
			echo "retry: 8000" . PHP_EOL;
			flush();
		
		jexit();
	}
	function live_chart_data()
	{  
		
		 $data = new stdClass();
		 $data->result = 'error';
		 $config = JFactory::getConfig();
		 $int="eth0";
         require_once JPATH_COMPONENT.'/classes/oprating_lib.php';
		 $output = new operating();
		 $output->loading();
      
         //echo $rx[] = @file_get_contents("/sys/class/net/$int/statistics/rx_bytes");
         $host =  $_SERVER['SERVER_NAME'];
		 $port = 80;
		 $timeout = 16;
		 
		$start_time = microtime(TRUE);
		$status = @fsockopen($host, $port, $errno, $errstr, $timeout); 
		

		$end_time = microtime(TRUE);
		$time_taken = $end_time - $start_time;
		$time_taken = round($time_taken,5);
        /* $class_name = 'WebpageXML';
		 if (file_exists(JPATH_COMPONENT.'/operating/includes/output/class.'.$class_name.'.inc.php')) {
            include_once JPATH_COMPONENT.'/operating/includes/output/class.'.$class_name.'.inc.php';
           
        } */
       $output = new WebpageXML(false, null);
	   if (defined('PSI_JSON_ISSUE') && (PSI_JSON_ISSUE)) {
            $json = json_encode(simplexml_load_string(str_replace(">", ">\n", $output->getXMLString()))); // solving json_encode issue
        } else {
            $json = json_encode(simplexml_load_string($output->getXMLString()));
        }
        // check for jsonp with callback name restriction
		
       
		
		$html = $time_taken;
		$data->html = $html*1000;
		$data->json = str_replace('@attributes','attributes',$json);
		$data->result = 'success';
		jexit(json_encode($data));
	}
	
	function newbandwidth()
	{
		 $int="eth0";
  
   
    $rx[] = @file_get_contents("/sys/class/net/$int/statistics/rx_bytes");
    $tx[] = @file_get_contents("/sys/class/net/$int/statistics/tx_bytes");
    sleep(1);
    $rx[] = @file_get_contents("/sys/class/net/$int/statistics/rx_bytes");
    $tx[] = @file_get_contents("/sys/class/net/$int/statistics/tx_bytes");
    
    $tbps = $tx[1] - $tx[0];
    $rbps = $rx[1] - $rx[0];
    
    $round_rx=round($rbps/1024, 2);
    $round_tx=round($tbps/1024, 2);
    
	$session = JFactory::getSession();
    $time=date("U")."000";

	$session_rx[] = "[$time, $round_tx]";
	$session->set('rx', $session_rx);
	
	$session_tx[] = "[$time, $round_tx]";
	$session->set("tx", $session_tx);
	
    $data['label'] = $int;
    $data['data'] = $session->get('rx');
	
    # to make sure that the graph shows only the
    # last minute (saves some bandwitch to)
    if (count($session->get('rx'))>60)
    {
        $x = min(array_keys($session->get('rx')));
		$rx_unset_index = $session->get('rx');
		unset($rx_unset_index[$x]);
		$session->set('rx', $rx_unset_index);
    }
    
    echo '
    {"label":"'.$int.'","data":['.implode($session->get('rx'), ",").']}
    ';
	jexit();
	}
	
	function check_login_status(){
		   $obj = new stdClass();
		    $state = JFactory::getUser()->id>0 ? true:false;
		    $obj->result = 'success';
			$obj->state = $state;
		
		jexit(json_encode($obj));
	}
	
}

?>