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

	require_once JPATH_SITE.'/components/com_vdata/operating/drawchart.php';
	$document =  JFactory::getDocument();
	$document->addScript("https://www.google.com/jsapi");
	$mem_usage = memory_get_usage(true); 
	$mem_usage = round($mem_usage/1024,2);

	$user = JFactory::getUser();


 ?>

<script type="text/javascript">
var infor_memory_response=<?php echo  $mem_usage; ?>;
var infor_memory_response_text = <?php echo  $mem_usage; ?>;
var infor_cpu_response=0;
var infor_response_time=0;
var infor_cpu_name = '';
var server_cpu_load = '';
var thread_connected = 0;
var thread_running = 0;
var source<?php echo $uniqueid;?>;
var check_status = '';
var status_action = '';
var status_index = 0;
function eventsourcing<?php echo $uniqueid?>()
{
 
     source<?php echo $uniqueid?> = new EventSource("<?php echo "index.php?option=com_vdata&view=vdata&task=live_chart_data_test&widget_ordering".$uniqueid."=".$widget_ordering."&widget_ordering_by".$uniqueid."=".$widget_ordering_by."&module_id=".$uniqueid."&widget".$uniqueid."=".$widget;?>");
	
     source<?php echo $uniqueid;?>.onmessage = function(event) {
	   
		    var res = jQuery.parseJSON(event.data); 
	  
		    var st = '';
			for(var i=0;i<res[1].length;i++)
			{ 
					 st += '<td>'+res[1][i].Value+'<\/td>';
					 if(i==8){
						thread_connected = parseInt(res[1][i].Value);}
						 if(i==9){
						 thread_running = parseInt(res[1][i].Value);}
			} 	
		     
		     infor_cpu_name = res[2].cpu_info; 
			 infor_cpu_response = Math.round(res[2].cpu_load);
			 var memory_status = formate_check(res[2].memory_status);//data.info_memory;
			 infor_memory_response = Math.round(memory_status[0],1);
			 infor_memory_response_text = "%";
			 infor_response_time = res[3];
			 
			   if(typeof res[5]!='undefined'){
			  var single_value_display = res[5];
			  jQuery('.innser_single_trigger'+<?php echo $uniqueid;?>).each(function(index, value) { 
			  var st_text =	'';	 //alert(index);  
			  st_text = single_value_display[index];
			  jQuery(this).html(single_value_display[index]);
			  
			  }); 
	         }
			  if(typeof res[6]!='undefined')
			  { 
		       var listing_info_display = res[6];
			  jQuery('table.listing_info').each(function(index, value) {  
			  jQuery('table#'+jQuery(this).attr('id')+' tr').slice(1).remove();
			  jQuery('table#'+jQuery(this).attr('id')+' tbody').prepend(listing_info_display[index]);
			  
			  });	}
    };
	
  }

function formate_check(bytes){
	var show='';var show_text='';
	if (bytes > Math.pow(1024, 5)) {
            show += Math.round(bytes / Math.pow(1024, 5), 2);
            show_text += "<?php echo JText::_('PiB');?>";
        }
        else {
            if (bytes > Math.pow(1024, 4)) {
                show += Math.round(bytes / Math.pow(1024, 4), 2);
                show_text += "<?php echo JText::_('TiB');?>";
            }
            else {
                if (bytes > Math.pow(1024, 3)) {
                    show += Math.round(bytes / Math.pow(1024, 3), 2);
                    show_text += "<?php echo JText::_('GiB');?>";
                }
                else {
                    if (bytes > Math.pow(1024, 2)) {
                        show += Math.round(bytes / Math.pow(1024, 2), 2);
                        show_text += "<?php echo JText::_('MiB');?>";
                    }
                    else {
                        if (bytes > Math.pow(1024, 1)) {
                            show += Math.round(bytes / Math.pow(1024, 1), 2);
                            show_text += "<?php echo JText::_('KiB');?>";
                        }
                        else {
                            show += bytes;
                            show_text += "<?php echo JText::_('B');?>";
                        }
                    }
                }
            }
        }
	var show_array = new Array();
	show_array[0] =show; 
	show_array[1] =show_text; 
	return show_array;
}
jQuery(document).ready(function(){ 
	   status_index = parseInt(jQuery('ul.connectedsortable > li').length)+1;
	   var timer = '';
	  eventsourcing<?php echo $uniqueid?>(); //  
	 
});


</script>
 
<form action="index.php?option=com_vdata" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

<div class="profile-section vdata_widget_view">
<ul id="sortable1" class="connectedsortable">
<?php

$k = 0;
	
	$lang = JFactory::getLanguage();
	$prev_current_width ='';
	$future_width ='';
	$live_data_query = array('Server Response Monitoring','Server CPU Monitoring','Server Monitoring','Thread Status','Queries Status');
	$row_siz = $configuration->row_limit;
	$one_column_size = 100/$configuration->column_limit;
	
	for($j=0;$j<count($widgets);$j++){
		$widget = json_decode($widgets[$j]->detail);
		
		$current_width ='';
		$sub_current_width =0;$box_class_name='';
		if(isset($widget->box_layout) && $widget->box_layout=="onebox"){$sub_current_width=1;$box_class_name=' onebox';}
		elseif(isset($widget->box_layout) && $widget->box_layout=="twobox"){$sub_current_width=2;$box_class_name=' twobox';}
		elseif(isset($widget->box_layout) && $widget->box_layout=="threebox"){$sub_current_width=3;$box_class_name=' threebox';}
		elseif(isset($widget->box_layout) && $widget->box_layout=="fourbox"){$sub_current_width=4;$box_class_name=' fourbox';}
		elseif(isset($widget->box_layout) && $widget->box_layout=="fivebox"){$sub_current_width=5;$box_class_name=' fivebox';}
		elseif(isset($widget->box_layout) && $widget->box_layout=="sixbox"){$sub_current_width=5;$box_class_name=' sixbox';}
			
			$column_widget_width_value = '';
		    $row_widget_height_value = '';
		    $row_widget_height_value_chart = '';
		if(isset($widget->box_column) && $widget->box_column)
		{
		 $column_widget_width_value = (100).'%';	
		}
		
	    if(isset($widget->box_row) && $widget->box_row)
		{
		$row_widget_height_value =	(($widget->box_row*$row_siz)-20).'px';
		$row_widget_height_value_chart = (($widget->box_row*$row_siz)-20);
		}
			
			if(empty($prev_current_width))
			{
			$prev_current_width = $current_width;	
			}
			$current_width ='';
	  if(isset($widget->style_layout) && $widget->style_layout=='single_formate')
	  { ?>
	  <li class="common_profile_main single_formate num<?php echo ($j+1);if($widgets[$j]->datatype_option=='profile'){echo ' profile_widget';} echo $box_class_name;?>" data-ordering-profile="<?php echo $widgets[$j]->id.':'.$widgets[$j]->ordering; ?>" style="<?php if($column_widget_width_value!='' && $row_widget_height_value!='') 
		  echo 'width:'.$column_widget_width_value.';height:'.$row_widget_height_value.';';?>">
		<div class="panel_header">
		
		</div>
		<div class="profile_mid_data">
	  <?php 
		
		  $single_data = vchart::widgetvalue($widget);
		if(isset($single_data->result)&&$single_data->result=='error'){
			  echo $single_data->error;
		  }
		  else
		  {
		  $single_data = isset($single_data->data)?$single_data->data:array();
		  $test_v = isset($widget->style_layout_editor)?$widget->style_layout_editor:'';
		    $regex		= '/{(.*?)}/i';
		    preg_match_all($regex, $widget->style_layout_editor, $matches, PREG_SET_ORDER);
			
			 foreach ($matches as $match)
			{
			foreach ($single_data as $key=>$value)
			{ 
			     
				 if(isset($widget->style_layout_editor) && $widget->style_layout_editor!=''&&$value!=null&&$match[1]==$key)
				 {	
				$test_v = preg_replace("|$match[0]|", '<span class="innser_single_trigger'.$uniqueid.'" data-profile-ids="profile_'.$profiles[$j]->id.'" id="inner_single_'.trim(preg_replace('/\s*\([^)]*\)/', '', $key)).'">'.$value.'</span>', $test_v, 1);
	 
				
				}
				elseif(isset($widget->style_layout_editor) && $widget->style_layout_editor!=''&&$value==null)
				 {	
				$test_v = preg_replace("|$match[0]|", '<span class="innser_single_trigger'.$uniqueid.'" data-profile-ids="profile_'.$profiles[$j]->id.'" id="inner_single_'.trim(preg_replace('/\s*\([^)]*\)/', '', $key)).'">'.$value.'</span>', $test_v, 1);
				}
            }
		  }			
           echo $test_v;
		  }
		 ?>
		 </div>
		  </li>
		  <?php
	  }
	  elseif(isset($widget->style_layout) && $widget->style_layout=='listing_formate')
	  {
	  if($widget->existing_database_table=='vData Profiles'){
		  ?>
		  <li class="common_profile_main num<?php echo ($j+1);if($widgets[$j]->datatype_option=='profile'){echo ' profile_widget';}echo $box_class_name; ?>" data-ordering-profile="<?php echo $widgets[$j]->id.':'.$widgets[$j]->ordering; ?>" style="<?php if($column_widget_width_value!='' && $row_widget_height_value!='') 
		  echo 'width:'.$column_widget_width_value.';height:'.$row_widget_height_value.';';?>">
	<div class="panel_header">
	<?php 
	echo '<span class="profile_name">';
	if(isset($widgets[$j]->name)&& $widgets[$j]->name!='')
		echo $widgets[$j]->name.'</span>';
	echo '</span>';
	?>
	
	</div>
	<div class="profile_mid_data common_class_listing_format">
	<?php 
	$widget_style = $row_widget_height_value_chart!=''?' style="height:'.($row_widget_height_value_chart-63).'px"':'';
	echo '<ul class="profile_mid_data_inner"'.$widget_style.'>';
	?>
	     <?php $c=0;if(isset($widget->profile_creation_button)&&$widget->profile_creation_button==1) {
			 $c++; 
			 ?>
		  <li class="common_profile num1 profile-section-listing">
		  <a href="index.php?option=com_vdata&view=profiles&task=add"><img src="<?php echo Juri::root();?>media/com_vdata/images/add-new-profile.png" alt="<?php echo JText::_('CONFIG'); ?>" /><span class="dashboard_profile_name"><?php	echo JText::_('COM_VDATA_CREAT_PROFILE');	?></span></a>
		  </li>
		  <?php
		 }
			for ($i=0, $n=count( $profiles ); $i < $n; $i++)
			{
			
			$row = $profiles[$i];
				if(in_array(JText::_("COM_VDATA_SELECT_ALL_PROFILES"),$widget->profiles) || in_array($row->id,$widget->profiles)){
				
				?>
				
			   <li class="common_profile num<?php echo (++$c); ?> profile-section-listing" data-ordering-profile="<?php echo $row->id.':'.$row->ordering; ?>" style="<?php echo $sub_current_width;?>">
					
					<?php if($row->iotype) : ?>
					<a href="index.php?option=com_vdata&view=profiles&task=export&profileid=<?php echo $row->id; ?>"><img src="<?php echo Juri::root();?>media/com_vdata/images/export_profile.png" alt="<?php echo JText::_('CONFIG'); ?>" /><span class="dashboard_profile_name"><?php	echo JText::_($row->title);	?></span></a>
					<?php else : ?>
					<a href="index.php?option=com_vdata&view=import&profileid=<?php echo $row->id; ?>"><img src="<?php echo Juri::root();?>media/com_vdata/images/import_profile.png" alt="<?php echo JText::_('CONFIG'); ?>" /><span class="dashboard_profile_name"><?php	echo JText::_($row->title);	?></span></a>
					
					<?php endif; ?>
					
				   
				</li>
				<?php
				$k = 1 - $k;
				}
			}
          echo '</ul></li>';			
		}
		elseif($widget->existing_database_table=='vData Plugins'){
			?>
		  <li class="common_profile_main num<?php echo ($j+1);
		  if($widgets[$j]->datatype_option=='profile')
		  {
			  echo " profile_widget";
		}echo $box_class_name; ?>" data-ordering-profile="<?php echo $widgets[$j]->id.':'.$widgets[$j]->ordering; ?>" style="<?php if($column_widget_width_value!='' && $row_widget_height_value!='') 
		  echo 'width:'.$column_widget_width_value.';height:'.$row_widget_height_value.';';?>">
	<div class="panel_header">
	<?php 
	echo '<span class="profile_name">';
	if(isset($widgets[$j]->name)&& $widgets[$j]->name!='')
		echo $widgets[$j]->name;
	echo '</span>';
	?>
	
	</div>
	<div class="profile_mid_data common_class_listing_format">
	
	<ul>
		  
		  <?php
				for ($i=0, $n=count( $plugins ); $i < $n; $i++)
				{

					$row = $plugins[$i];
					if(in_array(JText::_("COM_VDATA_SELECT_ALL_PLUGINS"),$widget->plugins) || in_array($row->extension_id,$widget->plugins)){

					//$lang->load($profiles[$i]->extension . '.sys', JPATH_SITE.'/plugins/vdata/'.$profiles[$i]->element, null, false, false);

					?>

					<li class="common_profile_plugin num<?php echo ($i+1) ?>" data-ordering-profile="<?php echo $row->extension_id.':'.$row->name; ?>" style="<?php echo $sub_current_width;?>">


					<a href="index.php?option=com_vdata&view=profiles&extension_id=<?php echo $row->extension_id;?>&task=edit&cid[]=0"><img src="media/com_vdata/images/export_profile.png" alt="<?php echo JText::_('CONFIG'); ?>" /><span class="dashboard_profile_name"><?php	echo JText::_($row->name);	?></span></a>


					</li>
					<?php
					$k = 1 - $k;
					}
				}
		 echo '</ul></li>';	
		}
	  else{
	  
	  ?>
	   <li class="common_profile_main listing_formate num<?php echo ($j+1);if($widgets[$j]->datatype_option=='profile'){echo ' profile_widget';}echo $box_class_name; ?>" data-ordering-profile="<?php echo $widgets[$j]->id.':'.$widgets[$j]->ordering; ?>" style="<?php if($column_widget_width_value!='' && $row_widget_height_value!='') 
		  echo 'width:'.$column_widget_width_value.';height:'.$row_widget_height_value.';';?>">
		<div class="panel_header">
		<?php 
		echo '<span class="profile_name">';
		if(isset($widgets[$j]->name)&& $widgets[$j]->name!='')
		 echo $widgets[$j]->name;
	     echo '</span>';
		 ?>
		
		</div>
		<div class="profile_mid_data common_class_listing_format">
	  <?php 
		  
		  $single_data = vchart::widgetvalue($widget);
		   if(isset($single_data->result)&&$single_data->result=='error'){ 
			  echo $single_data->error;
		  }
		  else
		  {
	      $single_data = $single_data->data;
		  $object_array = isset($single_data[0])?get_object_vars($single_data[0]):array();
		  $object_array = count($object_array)>0?array_keys($object_array):array();
		  echo '<div class="listing_layout_others" style="height:'.($row_widget_height_value_chart-60).'px;">';
		   echo '<table class="adminlist table table-hover listing_info listing_info_'.$widgets[$j]->id.'" width="100%"><thead><tr>';
			for ($s = 0;$s<count($object_array);$s++) 
			{
			    
				  echo '<th>'.$object_array[$s].'</th>';
				
			
            }			
           echo '</thead>';
		   $g = 0;
			for ($l=0; $l < count( $single_data ); $l++)
			{
				$listing = $single_data[$l];
				echo '<tr>';
				foreach($listing as $listings){
				echo '<td>'.$listings.'</td>';	
				}
			echo '</tr>';	
			}
		  echo '</table>';
		  echo '</div>';
		  }
		 ?>
		 </div>
		  </li>
		<?php 
        }		
	  }
	 else
	 { 
			
		?>
	<li class="common_profile_main num<?php echo ($j+1);if($widgets[$j]->datatype_option=='profile'){echo ' profile_widget';}echo $box_class_name; ?>" data-ordering-profile="<?php echo $widgets[$j]->id.':'.$widgets[$j]->ordering; ?>" style="<?php if($column_widget_width_value!='' && $row_widget_height_value!='') 
		  echo 'width:'.$column_widget_width_value.';height:'.$row_widget_height_value.';';?>">
	<div class="panel_header">
	<?php 
	echo '<span class="profile_name">';
	if(isset($widgets[$j]->name)&& $widgets[$j]->name!='')
		echo $widgets[$j]->name.'';
	echo '</span>';
	?> 
	
	</div>
	<div class="profile_mid_data">
	
	<ul>
	
	<?php
	if($widgets[$j]->datatype_option=='profile'){
    for ($i=0, $n=count( $profiles ); $i < $n; $i++)
    {
        
		$row = $profiles[$i];
		if((isset($widget->show_all_profile) && $widget->show_all_profile==1) || in_array($row->id,$widget->existing_database_table)){
		
        ?>
		
       <li class="common_profile num<?php echo ($i+1) ?> profile-section-listing" data-ordering-profile="<?php echo $row->id.':'.$row->ordering; ?>" style="<?php echo $sub_current_width;?>">
	        
            <?php if($row->iotype) : ?>
            <a href="index.php?option=com_vdata&view=profiles&task=export&profileid=<?php echo $row->id; ?>"><img src="<?php echo Juri::root();?>media/com_vdata/images/export_profile.png" alt="<?php echo JText::_('CONFIG'); ?>" /><span class="dashboard_profile_name"><?php	echo JText::_($row->title);	?></span></a>
            <?php else : ?>
			<a href="index.php?option=com_vdata&view=import&profileid=<?php echo $row->id; ?>"><img src="<?php echo Juri::root();?>media/com_vdata/images/import_profile.png" alt="<?php echo JText::_('CONFIG'); ?>" /><span class="dashboard_profile_name"><?php	echo JText::_($row->title);	?></span></a>
			
            <?php endif; ?>
            
           
        </li>
        <?php
        $k = 1 - $k;
       }
      }
	} 
   elseif($widgets[$j]->datatype_option=='custom_plugin')
   {
    for ($i=0, $n=count( $plugins ); $i < $n; $i++)
    {
        
		$row = $plugins[$i];
		if((isset($widget->show_all_plugin) && $widget->show_all_plugin==1) || in_array($row->extension_id,$widget->existing_database_table)){
		
		//$lang->load($profiles[$i]->extension . '.sys', JPATH_SITE.'/plugins/vdata/'.$profiles[$i]->element, null, false, false);
		
        ?>
		
       <li class="common_profile_plugin num<?php echo ($i+1) ?>" data-ordering-profile="<?php echo $row->extension_id.':'.$row->name; ?>" style="<?php echo $sub_current_width;?>">
	        
            
            <a href="index.php?option=com_vdata&view=profiles&extension_id=<?php echo $row->extension_id;?>&task=edit&cid[]=0"><img src="media/com_vdata/images/export_profile.png" alt="<?php echo JText::_('CONFIG'); ?>" /><span class="dashboard_profile_name"><?php	echo JText::_($row->name);	?></span></a>
            
           
        </li>
        <?php
        $k = 1 - $k;
       }
      }
	}	
   elseif($widgets[$j]->datatype_option=='predefined')
   {  
    
	 $style= $row_widget_height_value_chart!=''?' style="height:'.($row_widget_height_value_chart-68).'px"':'';
	  if(isset($widgets[$j]->chart_type) && $widgets[$j]->chart_type!=''&& !in_array($widget->existing_database_table,$live_data_query)){
	      
			$script_data = vchart::draw_view_chart($widgets[$j]);
			 if($script_data->result=='success'){ 
			  echo ' <li class="chart_profile num'.($j+1).'" data-ordering-profile="'.$widgets[$j]->id.':'.$widgets[$j]->ordering.'"'.$style.'>';
			 echo ' <div id="widget_'.$widgets[$j]->id.'" class="widget_chart" data-profile-id="drawchart'.$widgets[$j]->id.'" '.$style.'></div></li>';
			
			 echo '<script type="text/javascript"> ';
			 echo $script_data->scripts;
			 echo '</script>';
			 
			 
			  } 
			  elseif($script_data->result=='error'){
				 $script_data->error; 
			  }
          }
		elseif(in_array($widget->existing_database_table,$live_data_query)){
		 $script_data = vchart::draw_live_chart($widgets[$j]);
		  if($widget->existing_database_table=='Server Monitoring'){?>
		  
<div id="tabs<?php echo $widgets[$j]->id;?>">
  <ul>
    <li data-chart-id="<?php echo $widgets[$j]->id;?>" data-show-chart="no"><a href="#tabs-1">Processes</a></li>
    <li data-chart-id="<?php echo $widgets[$j]->id;?>" data-show-chart="yes" data-chart-for="ram"><a href="#tabs-2">RAM Status</a></li>
    <li data-chart-id="<?php echo $widgets[$j]->id;?>" data-show-chart="yes" data-chart-for="cpu"><a href="#tabs-3">CPU Status</a></li>
  </ul>
  <div id="tabs-1">
    <table class="adminlist table table-striped table-hover" width="100%"><tr><th>Process Name</th><th>Process Type</th><th>Time Taken </th></tr></table></div>
  <div id="tabs-2"  style="width: 100%; height: 100%;" data-chart-id="<?php echo $widgets[$j]->id;?>" data-show-chart="yes">
   <div id="widget_sever_2<?php echo $widgets[$j]->id;?>"  style="width: 100%; height: 100%;"></div>
  </div>
  <div id="tabs-3" data-chart-id="<?php echo $widgets[$j]->id;?>" data-show-chart="yes">
    <div id="widget_sever_3<?php echo $widgets[$j]->id;?>"  style="width: 100%; height: 100%;"></div>
  </div>
</div>  
		  <?php 
		  echo '<script type="text/javascript"> ';
					 echo $script_data;
					  echo '</script>';
		  }else
		        {
			  
					 if($script_data!='no_chart')
					{ 
					  echo ' <li class="chart_profile num'.($j+1).'" data-ordering-profile="'.$widgets[$j]->id.':'.$widgets[$j]->ordering.'"'.$style.'>';
					  echo ' <div id="widget_'.$widgets[$j]->id.'" class="widget_chart" data-profile-id="drawchart'.$widgets[$j]->id.'"  style="width: 100%; height: 100%;"></div></li>';
					
					  echo '<script type="text/javascript"> ';
					  echo $script_data;
					  echo '</script>';
					}
			 
			  }		
		}  
		
	  else { 
		  echo ' <li class="chart_profile num'.($j+1).'" data-ordering-profile="'.$widgets[$j]->id.':'.$widgets[$j]->ordering.'"'.$style.'>'.modWidgetHelper::log_information($widgets[$j],$response_data).'</li>';
	     }	  
      
   }
   elseif($widgets[$j]->datatype_option=='writequery'){
	 $style= $row_widget_height_value_chart!=''?' style="height:'.($row_widget_height_value_chart-68).'px"':'';
	$script_data = vchart::draw_view_chart($widgets[$j]);
     if($script_data->result=='success'){ 
	  echo ' <li class="chart_profile num'.($j+1).'" data-ordering-profile="'.$widgets[$j]->id.':'.$widgets[$j]->ordering.'" '.$style.'>';
	 echo ' <div id="widget_'.$widgets[$j]->id.'" class="widget_chart" data-profile-id="drawchart'.$widgets[$j]->id.'"  '.$style.'></div></li>';
	
	 echo '<script type="text/javascript"> ';
	 echo $script_data->scripts;
	  echo '</script>'; 
	 }	
	 elseif($script_data->result=='error'){
		 echo $script_data->error; 
	 }	
   }
   
   ?></ul></li><?php
     }
	
	}
 ?>
 
 </ul>
</div>
</div>

	<div class="clr" style="clear:both;"></div>
	<input type="hidden" name="option" value="com_vdata" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="view" value="" />
	</form>
	