<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2016 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
//JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.modal');
JHtml::_('behavior.colorpicker');
$version = new JVersion;
$joomla = $version->getShortVersion();
$jversion = substr($joomla,0,3);
$document = JFactory::getDocument();
$details_values = isset($this->widget->detail)?json_decode($this->widget->detail):''; 
$details_value = isset($details_values->style_layout_editor)?$details_values->style_layout_editor:'';
?>


<script type="text/javascript">
var next_action = true;var query_error=true; var select_value='';var error_message='';var predefined_select_value='';
 var select_value='<?php echo isset($details_values->existing_database_table)?$details_values->existing_database_table:'';?>';

var auto_select_query_value='<?php echo isset($details_values->existing_database_table)?$details_values->existing_database_table:'';?>';
 window.parent.status_action= '<?php echo $this->widget->id;?>';
jQuery(document).ready(function(){
	jQuery('#editor_section').find('iframe').css('height','300px');
		if(jQuery('select[name="chart_type"]').val()=='')
			return;
		jQuery('input[type=radio][name="datatype_option"]').each(function(){
		if(jQuery(this).is(":checked")){
			selectoptions(this);
		}
			
		});
		
	});
jQuery('body').on('click','.delete_widget_info',function(){
	jQuery(this).parent('span').hide(1000);
	
});	
	

window.total_profile_listing = function(pro_listing){
	if(jQuery(pro_listing).is(':checked'))
		jQuery('select[name="params[existing_database_table][]"]').removeClass('required');
	else
		jQuery('select[name="params[existing_database_table][]"]').addClass('required');
};	
window.predefined_custom_query = function(pre_query){
	if(jQuery('textarea[name="params[remote_query_value]"]').val()==''||next_action==true)
		jQuery('textarea[name="params[remote_query_value]"]').val(jQuery(pre_query).val());
	
		if(next_action==true)
		load_change_function(pre_query);
		if(next_action==false)
		next_action=true;
};
window.update_autoselect_field = function(){
	    if (valid_auto == false) {
								jQuery('input[name="params[existing_database_table]"]').val('');
								jQuery('textarea[name="params[remote_query_value]"]').val('');
								jQuery('input[name="params[descriptin_widget]"]').val('');
								
								jQuery(".description_widget").hide();
								jQuery("span.testing_record").html('');
								} 
};
	var dialog='',selected_column=-2,selected_column_index=-2,selected_annotation_title_column='',selected_annotation_text_column='';
        var AREA_CHART = '<?php echo JText::_('CHART_TYPE_AREA');?>';
		var COLUMN_CHART = '<?php echo JText::_('CHART_COLUMN_CHART');?>';
		var CANDLE_CHART = '<?php echo JText::_('CHART_COLUMN_CHART');?>';
		var LINE_CHART = '<?php echo JText::_('CHART_TYPE_LINE');?>';
		var STEPPED_CHART = '<?php echo JText::_('CHART_STEPPED_CHART');?>';
		var cfdata=''; 
		<?php 
		
		if($this->widget->id) { ?>
		cfdata = <?php echo $this->widget->detail;?>;
		<?php } else { ?>
			cfdata = [];
		<?php } ?>
	jQuery(function()	{
var table_column='';
var closest_li_event='';
var series_value_save='';
var table_column_type = '';
var form,
 series_title = jQuery( "#series_title" ),
 series_column = jQuery( "#series_col option:selected" ),
 allFields = jQuery( [] ).add( series_title ).add( series_column ),
tips = jQuery( ".validateTips" );
	function updateTips( t ) {
	tips
	.text( t )
	.addClass( "ui-state-highlight" );
	setTimeout(function() {
	tips.removeClass( "ui-state-highlight", 1500 );
	}, 500 );
	}

	function checkLength( o, n, min, max ) 
	{ 
		if ( o.val().length > max || o.val().length < min ) {
		o.addClass( "ui-state-error" );
		updateTips( "Length of " + n + " must be between " +
		min + " and " + max + "." );
		return false;
		} else {
		return true;
		}
	}
	function checkValue( o, n, min, max ) 
	{ 
	var series_columnse = jQuery( "#series_col option:selected" );
	
	 var inde = jQuery.inArray(series_columnse.val(),table_column);
	   if ( series_columnse.val().length > max || series_columnse.val().length < min) {
		jQuery( "select.series_col").addClass( "ui-state-error" );
		updateTips( "<?php echo JText::_('COM_VDATA_SERIES_COLUMN_NAME');?>" );
		return false;
		} else {
		return true;
		}
	}
	function checkRegexp( o, regexp, n ) 
	{
		if ( !( regexp.test( o.val() ) ) ) 
		{
		o.addClass( "ui-state-error" );
		updateTips( n );
		return false;
		} else {
		return true;
		}
	}
	function axis_val(){
		var s_v_f = new Array();
		if(selected_column==''){
		var s_val =  document.getElementById('series_column_name').value;
		s_v_f = s_val.split(',');}
		var respose = '';var selected =''
					respose += '<select class="series_col series_col_change" name="series_col" id="series_col"><option value=""><?php echo JText::_('COM_VDATA_SELECT_SERIES_COLUMN_NAME');?></option>';  
					
					for(var i=0;i<table_column.length;i++){
						   selected ='';
						   var z_column = table_column[i];
							if(jQuery.inArray(table_column[i],s_v_f)== -1){
							 if(z_column==selected_column){
								selected =' selected="selected"';}
								
							respose += '<option value="'+table_column[i]+'"'+selected;	
							respose += '>'+table_column[i]+'</option>'; 
							}
							
					
						}
					respose += '</select>';
					
		     jQuery(".select_c").html(respose);
		    jQuery('select.series_col_change').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
		}
	
	
	function addSeries() 
	{ 
			var valid = true;
			allFields.removeClass( "ui-state-error" );
			valid = valid && checkLength( series_title, "Series title", 1, 500 );// new changes
			valid = valid && checkValue( series_column, "Series Column", 1, 500 );// new changes
			valid = valid && checkRegexp( series_title, /^[a-z]([0-9a-z_\s])+$/i, "Series title may consist of a-z, 0-9, underscores, spaces and must begin with a letter." );
			
			if ( valid ) {
				if(document.getElementById('series_name').value == ''){ 
				
				document.getElementById('series_name').value = series_title.val();
				document.getElementById('series_column_name').value = document.getElementById('series_col').value;

				
				}
				else{
					if(selected_column!=-2 && selected_column_index!=-2)
					{ 
					var series_column_existing = '';
					var series_column_existing = document.getElementById('series_name').value.split(',');
					//alert(selected_column_index);alert(series_column_existing);
					series_column_existing[selected_column_index] = series_title.val();
					//alert(series_column_existing);
					document.getElementById('series_name').value =series_column_existing.join(',');
					
					var series_existing = '';
					var series_existing = jQuery('#series_column_name').val().split(',');
					series_existing[selected_column_index] = jQuery('#series_col').val();
					
					jQuery('#series_column_name').val(series_existing.join(','));
					
				  }
				  else{
					
					jQuery('#series_name').val(jQuery('#series_name').val() + ','+series_title.val());
					jQuery('#series_column_name').val(jQuery('#series_column_name').val() + ','+jQuery('#series_col').val());
					
					}
				}
			updateTips( series_title.val()+" <?php echo JText::_('COM_VDATA_SELECT_SERIES_HAS_BEEN_CREATED');?>");
			series_title.val('');
			document.getElementById('series_col').value = '';
			jQuery( ".validateTips" ).html('');
			display_datafield_existing_database();
			jQuery(".select_c").html('');
			selected_column='';
			dialog.dialog( "close" );
			
			return valid;
			}
	}
	
	dialog = jQuery( "#dialog-form" ).dialog({
	autoOpen: false,
	height: jQuery(window).innerHeight()-100,
	width: jQuery(window).width()/2,
	modal: true,
	open: function(event, ui)	{
			 
                jQuery('.ui-dialog').css('zIndex',99);  
				jQuery('.ui-widget-overlay').css('zIndex',98);
                jQuery(this).closest(".ui-dialog").find(".ui-dialog-buttonset").find("button").eq(0).addClass("btn"); 
				jQuery(this).closest(".ui-dialog").find(".ui-dialog-buttonset").find("button").eq(1).addClass("btn"); 
				jQuery(this).closest(".ui-dialog")
				.find(".ui-dialog-titlebar-close")
				.addClass('ui-state-default')
				.html('<span class="ui-button-icon-primary ui-icon ui-icon-closethick"></span><span class="ui-button-text"></span>');
			 jQuery('.hasTipAseries').each(function() {
							var title = jQuery(this).attr('title');
							if (title) {
							var parts = title.split('::', 2);
							jQuery(this).data('tip:title', parts[0]);
							jQuery(this).data('tip:text', parts[1]);
							}
							});
                            var JTooltips = new Tips(jQuery('.hasTipAseries').get(), {"maxTitleChars": 50,"fixed": false});
		       jQuery.ajax({
				  url: "index.php",
				  type: "POST",
				  dataType: "json",
				  data: {                        
      'option':'com_vdata'
	  , 'view':'widget'
	  , 'task':'series_column'
	  , 'table_name':jQuery('select[name="params[existing_database_table]"]').val()
	  , 'chart_type':jQuery('select[name="chart_type"]').val()
	  , 'data_options': jQuery('input[name="datatype_option"]:checked').val()
	  , 'id':<?php echo isset($this->widget->id)&& $this->widget->id>0?$this->widget->id:0; ?>},
				  beforeSend: function()	{
					jQuery(".vdata_overlay").show();
				  },
				  complete: function()	{
					jQuery(".vdata_overlay").hide();
				  },
				  success: function(res)	{
					if(res.result == "success"){
						table_column = res.html;
						table_column_type = res.column_type;
						total_fields_column= res.html_column;
					    axis_val();
					
				     }
					
				  },
				  error: function(jqXHR, textStatus, errorThrown){
					  alert(textStatus);				  
				  }
			});
					
				},
	buttons: {
	"<?php echo JText::_('COM_VDATA_WIDGET_CREATE_SERIES_BUTTON');?>": addSeries,
	Cancel: function() 
			{
			dialog.dialog( "close" );
			}
		  },
		  overlay: {
         opacity: 0.7,
         background: "black"
      },
		 overlay: {
         opacity: 0.7,
         background: "black"
      },
	close: function() 
		  {
		  
		  
		 jQuery('input[name="series_title"]').val('');
		 
		  allFields.removeClass( "ui-state-error" );
		  }
	});
		 jQuery('body').on('click', '.create_series', function(event){

			if(jQuery('select[name="params[existing_database_table]"]').val()=='')
			{ 
			alert("<?php echo JText::_("COM_VDATA_TABLE_SELECT_EXISTING");?>");
			return false;
			}													   
			 dialog.dialog( "open" );
		});	
		jQuery('body').on('click', 'span.select_style_for_series_edit', function(event){

		jQuery('#series_title').val(jQuery(this).attr('data-series-name'));
		selected_column = jQuery(this).attr('data-series-column');
		var chart_type = jQuery('#chart_type').val();
		
		selected_column_index =  jQuery(this).closest( "tr" ).index();
		dialog.dialog( "open" );					
	});
   jQuery('body').on('click', 'span.select_style_for_series_delete', function(event){
                         
						  
							closest_li_event = jQuery(this).closest( "tr" );
							var series_name_delet = jQuery(this).attr( "data-series-name" );
							var series_name_column_delet = jQuery(this).attr( "data-series-column" );
							var chart_type = jQuery('#chart_type').val();
							
								jQuery("#dialog-confirm").dialog({
									resizable: false,
									modal: true,
									title: "Are you sure?",
									height: 250,
									width: 400,
									open: function(event, ui){
									jQuery(this).closest(".ui-dialog").find(".ui-dialog-buttonset").find("button").eq(0).addClass("btn"); 
									jQuery(this).closest(".ui-dialog").find(".ui-dialog-buttonset").find("button").eq(1).addClass("btn"); 
									jQuery(this).closest(".ui-dialog")
									.find(".ui-dialog-titlebar-close")
									.addClass('ui-state-default')
									.html('<span class="ui-button-icon-primary ui-icon ui-icon-closethick"></span><span class="ui-button-text"></span>');
									},
									buttons: {
										"Yes": function () {
											jQuery(this).dialog('close');
											callback(true,series_name_delet,series_name_column_delet,closest_li_event);
										},
											"No": function () {
											jQuery(this).dialog('close');
											callback(false,series_name_delet,series_name_column_delet,closest_li_event);
										}
									}
								});
							
						});	
  });	
	 jQuery('body').on('click', 'span.select_style_for_series', function(event){
		 if(jQuery('select[name="params[existing_database_table]:selected"]').val()!='' && jQuery('#series_name').length>0 && jQuery('#series_name').val() == ''){alert("<?php echo JText::_('COM_VDATA_WIDGET_ADD_SERIES_FIRST');?>");return;}
    
/* 															  
var series_c_c = jQuery( "#s_c_selections" );
var series_c_s = jQuery( "#series_color option:selected" );
var allField =  jQuery( [] ).add( series_c_c ).add( series_c_s ); */
var tips = jQuery( ".validateTips" );
 var valid_auto = false;
		jQuery( "#dialog-series" ).dialog({
		autoOpen: true,
		height: 400,
		width: 550,
		modal: true,
		show: {
				effect: 'blind',
				duration: 1000
				},
		hide: {
				effect: "blind",
				duration: 1000
				},
		open: function(event, ui){
		
			jQuery(this).closest(".ui-dialog").find(".ui-dialog-buttonset").find("button").eq(0).addClass("btn"); 
			jQuery(this).closest(".ui-dialog").find(".ui-dialog-buttonset").find("button").eq(1).addClass("btn"); 
			jQuery(this).closest(".ui-dialog")
			.find(".ui-dialog-titlebar-close")
			.addClass('ui-state-default')
			.html('<span class="ui-button-icon-primary ui-icon ui-icon-closethick"></span><span class="ui-button-text"></span>');
			 var s_data_for_color =  new Array();
			var color_value_f_ser =  new Array();
		   if(jQuery('textarea[name="params[user_write_query_value]"]').length>0&& jQuery('textarea[name="params[user_write_query_value]"]').val()!='')
		      {
			  jQuery.ajax({
		                url: "index.php",
						type: "POST",
						dataType: "json",
						data: {'option':'com_vdata', 'view':'widget', 'task':'series_color_object','sql_query':jQuery('textarea[name="params[user_write_query_value]"]').val(), 'chart_type':jQuery('select[name="chart_type"]').val(), 'id':<?php echo isset($this->widget->id)&& $this->widget->id>0?$this->widget->id:0; ?>, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1},
						
						beforeSend: function()	{
							jQuery(".vdata_overlay").show();
							},
							complete: function()	{
							jQuery(".vdata_overlay").hide();
							},
							success: function(res)	{
							if(res.result == "success"){
							s_data_for_color = res.data.split(',');
							if(document.getElementById('series_column_color').value!='')
			                 color_value_f_ser = document.getElementById('series_column_color').value.split(',');
								var series_to_select ='<table>';
								series_to_select += '<tr class="row"><td width="200px" align="centre"><strong><?php echo JText::_('COM_VDATA_SELECT_SERIES_NAME');?></strong></td><td width="200px" align="centre"><strong><?php echo JText::_('COM_VDATA_SELECT_SERIES_NAME_COLORING');?></strong></td></tr>';
									for(var z=0;z<s_data_for_color.length;z++)
									{
									series_to_select += '<tr class="row"><td width="200px" align="centre"><label>'+s_data_for_color[z]+'</label></td>';
									series_to_select += '<td width="200px" align="centre"><input type="text" id="s_c_selections_'+z+'" name="options[series_color]" class="series_color_dialog" value="'+color_value_f_ser[z]+'" /></td></tr>';

									}
								series_to_select += '</table>';
								document.getElementById('select_series_to_color').innerHTML = 	series_to_select;



								if(document.getElementById('chart_type').value=='')
								return;					 

								jQuery('.series_color_dialog').each(function() {
									jQuery(this).minicolors({
									control: jQuery(this).attr('data-control') || 'hue',
									position: jQuery(this).attr('data-position') || 'right',
									theme: 'bootstrap'
									});
								});
							}
							else{
						     alert(res.error);
							}
							},
							error: function(jqXHR, textStatus, errorThrown)	{
							alert(textStatus);			  
							}	
			
			
		               });	  
			  }
			else
			 {
		    var widget_chart = jQuery('select[name="chart_type"]').val();
			 if(widget_chart=='Line Chart' || widget_chart == 'Area Chart'|| widget_chart == 'Stepped AreaChart'||widget_chart == 'Column Chart'|| widget_chart =='Pie Chart' || widget_chart =='Slice Pie Chart'  || widget_chart == 'Combo Chart' || widget_chart == 'Bar Chart'||  widget_chart =='Table'){
			if(document.getElementById('series_name').value!='')
			s_data_for_color = document.getElementById('series_name').value.split(',');
			if(document.getElementById('series_column_color').value!='')
			color_value_f_ser = document.getElementById('series_column_color').value.split(',');
			
			
			
			var series_to_select ='<table>';
			series_to_select += '<tr class="row"><td width="200px" align="centre"><strong><?php echo JText::_('COM_VDATA_SELECT_SERIES_NAME');?></strong></td><td width="200px" align="centre"><strong><?php echo JText::_('COM_VDATA_SELECT_SERIES_NAME_COLORING');?></strong></td></tr>';
			for(var z=0;z<s_data_for_color.length;z++){
			

			series_to_select += '<tr class="row"><td width="200px" align="centre"><label>'+s_data_for_color[z]+'</label></td>';
			series_to_select += '<td width="200px" align="centre"><input type="text" id="s_c_selections_'+z+'" name="options[series_color]" class="series_color_dialog" value="'+color_value_f_ser[z]+'" /></td></tr>';
		
			}
		series_to_select += '</table>';
		document.getElementById('select_series_to_color').innerHTML = 	series_to_select;
			 }
			
		
					if(document.getElementById('chart_type').value=='')
						return;					 
					
					jQuery('.series_color_dialog').each(function() {
						jQuery(this).minicolors({
							control: jQuery(this).attr('data-control') || 'hue',
							position: jQuery(this).attr('data-position') || 'right',
							theme: 'bootstrap'
						});
					});
			
		}
			
			
			
		},
		buttons: {
		"<?php echo JText::_('COM_VDATA_SELECT_SERIES_ADD_COLORING');?>": addColor,
		Cancel: function() 
		{
		jQuery( "#dialog-series" ).dialog( "close" );
		}
		},
		overlay: {
         opacity: 0.7,
         background: "black"
      },
		close: function() 
		{
		
		}
		});							
		function updateTip( t ) 
		{
		tips
		.text( t )
		.addClass( "ui-state-highlight" );
		setTimeout(function() {
		tips.removeClass( "ui-state-highlight", 1500 );
		}, 500 );
		}
		
		function addColor()
		{
			
			
			var s_v_e = c_v_e = new Array();
			 if(jQuery('textarea[name="params[user_write_query_value]"]').length>0&& jQuery('textarea[name="params[user_write_query_value]"]').val()!='')
		      {
				jQuery('.series_color_dialog').each(function(index,value){
				if(jQuery(this).val()==''){
				updateTip('Please Select color for series '+r);
				return false;	
				}
				c_v_e[index] = jQuery(this).val();
                				
				});  
			  }
			 else
			 {
			
			var s_v = document.getElementById('series_name').value;
				s_v_e = s_v.split(',');
			for(var r=0;r<s_v_e.length;r++){jQuery('#s_c_selections_'+r).removeClass( "ui-state-error" );}
			for(var r=0;r<s_v_e.length;r++){
			if(document.getElementById('s_c_selections_'+r).value ==''){
				jQuery('#s_c_selections_'+r).addClass("ui-state-error");
				updateTip('Please Select color for series '+r);
				return false;
			   }
			}
			for(var r=0;r<s_v_e.length;r++){
				c_v_e[r] = document.getElementById('s_c_selections_'+r).value ;
				}
			}	
			document.getElementById('series_column_color').value = c_v_e.join(',');
			updateTip('Color code Updated');
            jQuery( ".validateTips" ).html('');
			jQuery( "#dialog-series" ).dialog( "close" );
		return true;
		}			
									
 

});
	function load_change_function(e){
	var table_name_value = ''; 
		var user_write_query_value ='1';
	 if(jQuery('input[type=radio][name="datatype_option"]:checked').val()=='predefined'){
		
		 if(auto_select_query_value=='vData Profiles'){
			 table_name_value = auto_select_query_value; 
		 }
		 else if(auto_select_query_value=='vData Plugins'){
			 table_name_value = auto_select_query_value;
		 }
		 else
		 table_name_value = jQuery('textarea[name="params[remote_query_value]"]').val();
	 }
	
	 if(jQuery("table.main_formating_section").length>0)
		  jQuery("table.main_formating_section").empty();
		if(jQuery("table.chart_formating_section").length>0)
		  jQuery("table.chart_formating_section").empty();
	 if(jQuery('input[type=radio][name="datatype_option"]:checked').val()=='writequery'){
		
		
			if(jQuery('select[name="params[existing_database_table]"]').val()!='')
			{
				table_name_value = jQuery('select[name="params[existing_database_table]"]').val();
				jQuery('textarea#user_write_query_value').val('');
				jQuery('textarea#user_write_query_value').removeClass('required');

				jQuery('.selectoptions_class').removeClass('required');
				jQuery('select[name="params[existing_database_table]"]').addClass('required');									 
			}

			if(jQuery('select[name="params[existing_database_table]"]').val()=='')
			{
				table_name_value = jQuery('textarea#user_write_query_value').val();
				user_write_query_value = jQuery('textarea#user_write_query_value').val();
				jQuery('textarea#user_write_query_value').addClass('required');
				jQuery('select[name="params[existing_database_table]"]').removeClass('required');	 
			} 
                             
		 
	 }
	jQuery.ajax({
		                url: "index.php",
						type: "POST",
						dataType: "json",
						data: {'option':'com_vdata', 'view':'widget', 'task':'table_reference_options','table_name':table_name_value, 'widget_type':jQuery('input[type=radio][name="datatype_option"]:checked').val(), 'user_write_query_value':user_write_query_value, 'chart_type':jQuery('select[name="chart_type"]').val(), 'id':<?php echo isset($this->widget->id)&& $this->widget->id>0?$this->widget->id:0; ?>, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1},
						
						beforeSend: function()	{
							jQuery(".vdata_overlay").show();
							},
							complete: function()	{
							jQuery(".vdata_overlay").hide();
							},
							success: function(res)	{
							if(res.result == "success")
							{
							jQuery("table.extra_condition_table").empty();
							
							//jQuery("table.local_database_table tr:first").after(res.html);
							jQuery("table.extra_condition_table").html(res.html);
							jQuery('tr.chart_type_select').hide();
							jQuery('#editor_section').hide(); 
							jQuery('select.selectoptions_class').chosen({"disable_search_threshold":0,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","display_selected_options": true,"search_contains": true,"no_results_text":"No results match"});
							if(jQuery('input[type=radio][name="params[style_layout]"]:checked').val()=='charting_formate'){
							jQuery('tr.chart_type_select').show();
		                    jQuery('select[name="chart_type"]').addClass('required');
							if(jQuery('select[name="chart_type"]').length > 0 && jQuery('select[name="chart_type"]').val()!=''){
							selectoptions_for_chart();	
							}
							}
							else if(jQuery('input[type=radio][name="params[style_layout]"]:checked').val()=='listing_formate'){
								jQuery('#listing_section').show();
								if(jQuery('input[type=radio][name="datatype_option"]:checked').val()=='writequery' && jQuery('textarea[name="params[user_write_query_value]"]').val()=='')
								   {
							       selectoptions_for_chart();	
							      }
							}
							else if(jQuery('input[type=radio][name="params[style_layout]"]:checked').val()=='single_formate'){
								jQuery('#editor_section').show();
							if(jQuery('select[name="chart_type"]').length > 0 && jQuery('select[name="chart_type"]').val()!=''){
							selectoptions_for_chart();	
							} 
							}
							
							
							jQuery('select.load_change_class').chosen({"disable_search_threshold":0,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","display_selected_options": true,"search_contains": true,"no_results_text":"No results match"});
							jQuery('.hasTiprefence').each(function() {
							var title = jQuery(this).attr('title');
							if (title) {
							var parts = title.split('::', 2);
							jQuery(this).data('tip:title', parts[0]);
							jQuery(this).data('tip:text', parts[1]);
							}
							});
                            var JTooltips = new Tips(jQuery('.hasTiprefence').get(), {"maxTitleChars": 50,"fixed": false});
							jQuery( ".generalsize" ).spinner({  
							min: 0,
							numberFormat: "n"});
							
							if(query_error == false)
							jQuery( "#system-message-container" ).html('');
							query_error = true;;
							
							}
							else{
							jQuery( "#system-message-container" ).html('<div class="alert alert-error"><p>'+res.error+'</p></div>');
						    query_error = false;
							error_message = res.error;
							}
							},
							error: function(jqXHR, textStatus, errorThrown)	{
							alert(textStatus);				  
							}	
			
			
		});	
		
	}
	function selectoptions(e){
		jQuery('span.label-message').removeClass('click');
		jQuery(e).nextAll('span:first').addClass('click');
		jQuery('.main_series_editing_section').html('');
		if(jQuery("table.main_formating_section").length>0)
		  jQuery("table.main_formating_section").empty();
		if(jQuery("table.chart_formating_section").length>0)
		  jQuery("table.chart_formating_section").empty();
		
		if(jQuery('input[type=radio][name="datatype_option"]:checked').val()==''){alert('<?php echo JText::_('COM_VDATA_WIDGET_DATA_OPTION_FOR_QUERY');?>');return;}
		//if(jQuery('select[name="chart_type"]').val() == ''){alert('<?php echo JText::_('COM_VDATA_WIDGET_SELECT_CHART_TYPE');?>');return;}
		jQuery.ajax({
		                url: "index.php",
						type: "POST",
						dataType: "json",
						data: {'option':'com_vdata', 'view':'widget', 'task':'data_for_query','data_base_options':jQuery('input[type=radio][name="datatype_option"]:checked').val(), 'id':<?php echo isset($this->widget->id)&& $this->widget->id>0?$this->widget->id:0; ?>, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1},
						
						 beforeSend: function()	{
							jQuery(".vdata_overlay").show();
							},
							complete: function()	{
							jQuery(".vdata_overlay").hide();
							},
							success: function(res)	{
							if(res.result == "success"){
							jQuery('.data_options_for_query').html(res.html);
							jQuery('tr.chart_type_select').hide();
							jQuery('#editor_section').hide();
							jQuery(".style_charting_formate").prop("disabled", false);  
							if(jQuery('input[type=radio][name="params[style_layout]"]:checked').val()=='charting_formate'){
							jQuery('tr.chart_type_select').show();
		                    jQuery('select[name="chart_type"]').addClass('required');
							}
							else if(jQuery('input[type=radio][name="params[style_layout]"]:checked').val()=='single_formate'){
								jQuery('#editor_section').show();
							}
							jQuery('#show_all_profile').on('click', function(){
								if(jQuery(this).is(":checked")){
								 jQuery('option').prop('selected', false);
                                 jQuery('.selectoptions_class').trigger('liszt:updated');	
								}
   
                               });
							jQuery('textarea#user_write_query_value').on("change", function() {
								if(jQuery('textarea#user_write_query_value').val()!='')
								{
								 jQuery('option').prop('selected', false);
                                 jQuery('.selectoptions_class').trigger('liszt:updated');
                                 jQuery('.selectoptions_class').removeClass('required');
								 jQuery(this).addClass('required');
								 load_change_function(this);
								}
                                
								 if(jQuery(this).val()==''){
									jQuery('.selectoptions_class').addClass('required');
									jQuery(this).removeClass('required');	 
								 } 
                               }); 
							if(jQuery('input[type=radio][name="datatype_option"]:checked').val()=='predefined')
								{   
                             var cache = caches_label = caches_value = charting_value = listing_value = single_value = desc = {};
							 
							cache  = [{"value":"Server Response Monitoring","label":"Server Response Monitoring","desc":"<?php echo JText::_('COM_VDATA_TO_MONITOR_SERVER_RESPONSE_TIME');?>","id":"0"},{"value":"Server CPU Monitoring","label":"Server CPU Monitoring","desc":"<?php echo JText::_('COM_VDATA_TO_MONITOR_CPU_UTILIZATION');?>","id":"0"},{"value":"Server Monitoring","label":"Server Monitoring Monitoring","desc":"<?php echo JText::_('COM_VDATA_TO_MONITOR_CPU_AND_RAM_UTILIZATION');?>","id":"0"},{"value":"Thread Status","label":"Thread Status","desc":"<?php echo JText::_('COM_VDATA_TO_MONITOR_SERVER_STATUS_CONNECTION');?>","id":"0"}];
                            caches_label  = ["Server Response Monitoring" ,"Server CPU Monitoring","Server Monitoring","Thread Status"];
							caches_value  = ["Server Response Monitoring" ,"Server CPU Monitoring","Server Monitoring","Thread Status"];
                            desc  = ["To monitor server response time" ,"To monitor cpu utilization","To monitor cpu and ram utilization","Server connection status"];
 						    charting_value  = [1,1,1,1];
							listing_value  = [0,0,0,0];
							single_value  = [0,0,0,0];
							var maintain_session = 1;
							jQuery("input.autocomplete_field" ).autocomplete({
								
							  source: function( request, response ) {
								var term = request.term;
								
								if(term == ''){
								 response(cache);
                                 return;								 
								}
								 if ( !term in cache ) {
								 jQuery('input[name="params[existing_database_table]"]').val('');
								  return;
								} 
						else {
							 valid_auto = false;
							jQuery.ajax({
							url: "index.php",
							type: "POST",
							dataType: "json",
						    data: {'option':'com_vdata', 'view':'widget', 'task':'predefined','term':term, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1, 'session_setting':maintain_session},
							beforeSend: function()	{
							jQuery("span.testing_record").html('');
							},
						    complete: function()	{
							jQuery("input").removeClass('ui-autocomplete-loading');
							},
							success: function(res)	{ 
							if(res.result == "success"){
								maintain_session = 0;
						   if(res.html==''){
							   jQuery('span.testing_record').css('display','block');
							   jQuery('span.testing_record').html('<a class="testing_record" href="#">No Record Found</a>');
						 setTimeout(function() {
								jQuery('a.testing_record1').fadeOut('slow');
							}, 2000);
                            }
							cache[ term ] = res.html;
							response( res.html );
							caches_label[ term ] = res.labels;
							caches_value[ term ] = res.htmls;
							charting_value[ term ] = res.charting;
							listing_value[ term ] = res.listing;
							single_value[ term ] = res.single;
							desc[ term ] = res.desc;
							
							jQuery.merge(caches_label, res.labels);
							jQuery.merge(caches_value, res.htmls);
							
							jQuery.merge(charting_value, res.charting);
							jQuery.merge(listing_value, res.listing);
							jQuery.merge(single_value, res.single);
							jQuery.merge(desc, res.desc);
							
							}
						
							
							} }); }
							  },minLength: 0,
							   open: function(e) {
									  // valid_auto = false;
									   //auto_select_query_value=''
									 },
							   select: function(e,ui)
							        {
								        for(var i=0;i<caches_label.length;i++)
										{
										 
										   if(ui.item.value==caches_label[i])
										   {
										   jQuery('textarea[name="params[remote_query_value]"]').val(String(caches_value[i]));
										   jQuery('input[name="params[descriptin_widget]"]').val(desc[i]);
										   
										  
										   auto_select_query_value = ui.item.value;
										   select_value = 1;
                                           jQuery('.detail_widget_info_main').hide(1000);										   
										   if(charting_value[i]==1){
											jQuery(".style_charting_formate").prop("disabled", false);   
										   }else{
											   jQuery(".style_charting_formate").prop("disabled", true);
											   jQuery(".style_charting_formate").prop("checked", false);
										   }
										   if(listing_value[i]==1){
											 jQuery(".style_listing_formate").prop("disabled", false);    
										   }else{
											  jQuery(".style_listing_formate").prop("disabled", true); 
											  jQuery(".style_listing_formate").prop("checked", false);
										   }
										   if(single_value[i]==1){
											jQuery(".style_single_formate").prop("disabled", false);     
										   }else{
											  jQuery(".style_single_formate").prop("disabled", true);
                                              jQuery(".style_single_formate").prop("checked", false);											  
										   }
										  jQuery(".description_widget").css('display','block');
										 
										jQuery.ajax({
											url: "index.php",
											type: "POST",
											data: {'option':'com_vdata', 'view':'vdata', 'task':'update_selected_keyword', 'type':"widget", 'keyword':ui.item.id, "<?php echo JSession::getFormToken(); ?>":1}	
										});
										   load_change_function(this);
										   }
											   
									    }
									   
										valid_auto = true;next_action=false;
										
										
									 },
							     close: function(event, ui){
								      
										/* if (!valid_auto)
										{ 
											if (jQuery.inArray(jQuery('input[name="params[existing_database_table]"]').val(), caches_label)==-1 ){
												jQuery('input[name="params[existing_database_table]"]').val('');
											select_value = '';}
											
												
										} */
									
									if(jQuery('input[name="params[existing_database_table]"]').val()=='')
									   jQuery("table.extra_condition_table").empty();
									 }
							}).focus(function() {
								jQuery(this).autocomplete('search', jQuery(this).val());
								if (jQuery.inArray(jQuery('input[name="params[existing_database_table]"]').val(), caches_label)==-1 ){
								jQuery('input[name="params[existing_database_table]"]').val('');select_value = '';}
												}).data("ui-autocomplete")
	._renderItem = function(ul, item) {
        var listItem = $hd("<li class='hasTipAuto' title='"+item.desc+"'></li>")
            .data("item.autocomplete", item)
            .append("<a>" + item.label + "</a>")
            .appendTo(ul);
		var JTooltips = new Tips(jQuery('.hasTipAuto').get(), {"maxTitleChars": 50,"fixed": false});
        return listItem;
    };        
							
						}                   
							
							jQuery('select.selectoptions_class').chosen({"disable_search_threshold":0,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","display_selected_options": true,"search_contains": true,"no_results_text":"No results match"});
							jQuery('.hasTip').each(function() {
							var title = jQuery(this).attr('title');
							if (title) {
							var parts = title.split('::', 2);
							jQuery(this).data('tip:title', parts[0]);
							jQuery(this).data('tip:text', parts[1]);
							}
							});
							var JTooltips = new Tips(jQuery('.hasTip').get(), {"maxTitleChars": 50,"fixed": false});
							
								if(jQuery('input[type=radio][name="datatype_option"]:checked').val()=='predefined')
								{
									jQuery('.series_color').each(function() {
										   jQuery(this).minicolors({
											control: jQuery(this).attr('data-control') || 'hue',
											position: jQuery(this).attr('data-position') || 'right',
											theme: 'bootstrap'
											});
									   });
								}
								
								if(jQuery('input[type=radio][name="datatype_option"]:checked').val()=='predefined'){
								if(jQuery('input[name="params[existing_database_table]"]').val()!='')
								{
								load_change_function(jQuery('input[name="params[existing_database_table]"]'));	
								}	
								}else if(jQuery('input[type=radio][name="datatype_option"]:checked').val()=='writequery')
								{
								 if(jQuery('textarea#user_write_query_value').val()!='')
									{
									 jQuery('option').prop('selected', false);
									 jQuery('.selectoptions_class').trigger('liszt:updated');
									 jQuery('.selectoptions_class').removeClass('required');
									 jQuery('textarea#user_write_query_value').addClass('required');
									 load_change_function(jQuery('textarea#user_write_query_value'));
									}
                                
								 if(jQuery('textarea#user_write_query_value').val()==''){
									jQuery('.selectoptions_class').addClass('required');
									jQuery('textarea#user_write_query_value').removeClass('required');
                                    load_change_function(jQuery('select[name="params[existing_database_table]"]'));										
								 } 
								
								}
								
							}
							else
							alert(res.error);
							
							},
							error: function(jqXHR, textStatus, errorThrown)	{
							alert(textStatus);				  
							}	
			
			
		});
		
	}
function selectoptions_for_chart()
    {
	  var user_write_query_value ='1';
	  jQuery('.main_series_editing_section').html('');
	  jQuery('.data_options_for_formating_section').html('');
	  var query ='no';
     if(jQuery('input[type=radio][name="datatype_option"]:checked').val()=='predefined'){
		query = jQuery('textarea[name="params[remote_query_value]"]').val();
	 }	
		if(jQuery('input[type=radio][name="datatype_option"]:checked').val()=='')
		{
			alert('<?php echo JText::_('COM_VDATA_WIDGET_DATA_OPTION_FOR_QUERY');?>');
			return;
		}
		if(jQuery('input[type=radio][name="datatype_option"]:checked').val() == 'predefined')
		{
		  var table_name = jQuery('input[name="params[existing_database_table]"]').val();
		}
		else if(jQuery('input[type=radio][name="datatype_option"]:checked').val()=='writequery')
		{
		
		
				if(jQuery('select[name="params[existing_database_table]"]').val()!='')
				{
					var table_name = jQuery('select[name="params[existing_database_table]"]').val();
					jQuery('textarea#user_write_query_value').val('');
					jQuery('textarea#user_write_query_value').removeClass('required');

					jQuery('.selectoptions_class').removeClass('required');
					jQuery('select[name="params[existing_database_table]"]').addClass('required');									 
				}

				if(jQuery('select[name="params[existing_database_table]"]').val()=='')
				{
					var table_name = jQuery('textarea#user_write_query_value').val();
					user_write_query_value = jQuery('textarea#user_write_query_value').val();
					//user_write_query_value = user_write_query_value.replcae('#__',database_prefix);
					jQuery('textarea#user_write_query_value').addClass('required');
					jQuery('select[name="params[existing_database_table]"]').removeClass('required');	 
				} 
                             
		 
	       }
		if(jQuery('input[type=radio][name="params[style_layout]"]:checked').val()=='charting_formate')
		{
		jQuery('input[type=number][name="params[box_row]"]').attr('min',2);	
		} 
		jQuery.ajax({
		                url: "index.php",
						type: "POST",
						dataType: "json",
						data: {'option':'com_vdata', 'view':'widget', 'task':'formating_section','chart_type':jQuery('select[name="chart_type"]').val(),'table_name':table_name, 'style':jQuery('input[type=radio][name="params[style_layout]"]:checked').val(),'user_write_query_value':user_write_query_value,'query':query,'datatype_option':jQuery('input[type=radio][name="datatype_option"]:checked').val(), 'id':<?php echo isset($this->widget->id)&& $this->widget->id>0?$this->widget->id:0; ?>, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1},
						
						beforeSend: function()	{
							jQuery(".vdata_overlay").show();
							},
							complete: function()	{
							jQuery(".vdata_overlay").hide();
							},
							success: function(res)	{
							if(res.result == "success"){
							
							if(jQuery('input[type=radio][name="params[style_layout]"]:checked').val()=='charting_formate' && jQuery('select[name="chart_type"]').val()!=''){
							jQuery('.data_options_for_formating_section').html(res.formating);
							jQuery('.change_button').attr('disabled',false);
							}
							if(jQuery('input[type=radio][name="datatype_option"]:checked').val()=='writequery' && jQuery('textarea[name="params[user_write_query_value]"]').val()==''){
								if(jQuery('input[type=radio][name="params[style_layout]:checked"]').val()=='charting_formate' && jQuery('select[name="chart_type"]').val()!='')
								{
								display_datafield_existing_database();	
								}
								if(jQuery('input[type=radio][name="params[style_layout]"]:checked').val()=='listing_formate'){
								jQuery('#listing_section').html(res.formating);
								   if(jQuery('select[name="params[reference_listing_column]"]').length>0)
		                              jQuery('select[name="params[reference_listing_column]"]').addClass('required');
								}
							}
						
							jQuery('select.selectoptions_class').chosen({"disable_search_threshold":0,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","display_selected_options": true,"search_contains": true,"no_results_text":"No results match"});
							jQuery('.hasTipformat').each(function() {
							var title = jQuery(this).attr('title');
							if (title) {
							var parts = title.split('::', 2);
							jQuery(this).data('tip:title', parts[0]);
							jQuery(this).data('tip:text', parts[1]);
							}
							});
							var JTooltips = new Tips(jQuery('.hasTipformat').get(), {"maxTitleChars": 50,"fixed": false});	
								if(jQuery('input[type=radio][name="datatype_option"]:checked').val()=='predefined')
								{
									jQuery('.series_color').each(function() {
										   jQuery(this).minicolors({
											control: jQuery(this).attr('data-control') || 'hue',
											position: jQuery(this).attr('data-position') || 'right',
											theme: 'bootstrap'
											});
									   });
								}
								
							}
							else
							alert(res.error);
							
							},
							error: function(jqXHR, textStatus, errorThrown)	{
							alert(textStatus);				  
							}	
			
			
		});
	
}
 
 SqueezeBox.loadModal = function(modalUrl,handler,x,y) {
        this.presets.size.x = 1024;
        this.initialize();      
        var options = {handler: 'iframe', size: {x: 1000, y: 550}, onClose: function() {}};      
        this.setOptions(this.presets, options);
        this.assignOptions();
        this.setContent(handler,modalUrl);
    };
window.callback=function(e,series_name_delet,series_name_column_delet,closest_li_event)
    { 
	     if(e==false)
			 return;
		 var s_v_e, s_v_c = new Array();
		  var chart_type = jQuery('#chart_type').val();
						if(chart_type=='Area Chart' || chart_type=='Column Chart'  || chart_type=='Line Chart'  || chart_type=='Stepped AreaChart'  || chart_type=='Table'  || chart_type=='Combo Chart'|| chart_type=='Bar Chart' || chart_type=='Bubble Chart')
						{
						s_v_e = document.getElementById('series_name').value.split(',');
						s_v_e = jQuery.grep(s_v_e, function( n, i ) {return ( n!=series_name_delet)});
						document.getElementById('series_name').value = s_v_e.join( "," );
						
						s_v_c = document.getElementById('series_column_name').value.split(',');
						s_v_c = jQuery.grep(s_v_c, function( n, i ) {return (  n!=series_name_column_delet)});
						document.getElementById('series_column_name').value = s_v_c.join( "," );
						
						}
						closest_li_event.remove();
						if(s_v_e.length==0)
						jQuery('.main_series_editing_section').html('');
	}
		
window.extra_formating_style =function(layout_style){
	
jQuery('select[name="chart_type"]').removeClass('required');
	 if(jQuery('select[name="params[reference_listing_column]"]').length>0)
	  jQuery('select[name="params[reference_listing_column]"]').removeClass('required');
	 jQuery('tr.chart_type_select, #editor_section, #listing_section').hide();
	jQuery('#listing_section').html('');
	 jQuery("table.main_formating_section").empty();
	 jQuery("table.chart_formating_section").empty();
	if(jQuery(layout_style).val() == 'charting_formate'){
		jQuery('tr.chart_type_select').show();
		jQuery('select[name="chart_type"]').addClass('required');
		if(jQuery('select[name="chart_type"]').val()!='')
		{
			selectoptions_for_chart();
		}
		
	}
	else if(jQuery(layout_style).val() == 'single_formate'){
		jQuery('#editor_section').show();
	}
	else if(jQuery(layout_style).val() == 'listing_formate'){
		jQuery('#listing_section').show();
		selectoptions_for_chart();
		if(jQuery('select[name="params[reference_listing_column]"]').length>0)
		 jQuery('select[name="params[reference_listing_column]"]').addClass('required');
	}
	
	
};		
window.display_datafield_existing_database=function()
    { 
	   
	var val = jQuery('select[name="chart_type"]').val();
	var srs ='';var vs = '';var srs_column = '';
	var html = '<table class="existing_data_section">';
	
	var z_option ='';
		
		switch(val)
		{
			
			case 'Combo Charts':
			
			var series = document.getElementById('series_name').value;
			
			var series_column = document.getElementById('series_column_name').value;
			   if(series != "")
				{
				
				 srs = series.split(',');
				 
				 srs_column = series_column.split(',');
				
				html += '<table class="series_editing_section"><tr><legend><label id="jform_params_chartparams_stype-lbl" for="jform_params_chartparams_stype" aria-invalid="false"><strong><?php echo JText::_('COM_VDATA_EXISTING_SERIES_TYPE_DISPLAY');?></strong></label></legend></tr>';
				
				
					for(var i=0;i<srs.length;i++)
						{
						html += '<tr class="testing"><td width="180"><label id="chartparams_stype'+i+'-lbl" class="demo_class_name" for="chartparams_stype'+i+'" aria-invalid="false">'+srs[i].trim()+'</label></td>';
					
						html += '<td><select class="load_change_class" name="params[combo_stype]['+i+']" id="chartparams_stype'+i+'">';
						
						html += '<option value="area"';
						if(cfdata!=null &&cfdata.combo_stype && cfdata.chartparams[i] && cfdata.chartparams[i]=="area")
							html += ' selected="selected"';
						html += '>'+AREA_CHART+'</option>';					
						
						html += '<option value="bars"';
						if(cfdata!=null &&cfdata.combo_stype && cfdata.chartparams[i] && cfdata.chartparams[i]=="bars")
							html += ' selected="selected"';
						html += '>'+COLUMN_CHART+'</option>';
						html += '<option value="line"';
						if(cfdata!=null &&cfdata.chartparams && cfdata.chartparams[i] && cfdata.chartparams[i]=="line")
							html += ' selected="selected"';
						html += '>'+LINE_CHART+'</option>';
						
						html += '<option value="steppedArea"';
						if(cfdata!=null &&cfdata.chartparams && cfdata.chartparams[i] && cfdata.chartparams[i]=="steppedArea")
							html += ' selected="selected"';
						html += '>'+STEPPED_CHART+'</option>';	
						
						html += '</select><span id="'+i+'" class="select_style_for_series_edit btn btn-small btn-success" data-series-name="'+srs[i]+'"  data-series-column="'+srs_column[i]+'"><?php echo JText::_('COM_HEXCHART_WIDGET_SERIES_EDIT');?></span><span id="'+i+'" class="select_style_for_series_delete btn btn-small btn-success"  data-series-name="'+srs[i]+'"  data-series-column="'+srs_column[i]+'" data-original-title="Delete the series" title="<?php echo JText::_('COM_HEXCHART_WIDGET_SERIES_DELETE_DESC');?>"><?php echo JText::_('COM_HEXCHART_WIDGET_SERIES_DELETE');?></span></td></tr>';
					
					}
			
			    }
					break;
				case 'Area Chart':
				case 'Line Chart':
				case 'Column Charts':
			    case 'Bar Chart':
				case 'Stepped AreaChart':
				case 'Table':
					 var series = document.getElementById('series_name').value;
					
					 var series_column = document.getElementById('series_column_name').value;
					
					if(series != "")
						{
						
						 srs = series.split(',');
						
						 srs_column = series_column.split(',');
						
						html += '<legend><label id="jform_params_chartparams_stype-lbl" for="jform_params_chartparams_stype" aria-invalid="false"><strong><?php echo JText::_('COM_VDATA_EXISTING_SERIES_TYPE_DISPLAY');?></strong></label></legend>';
						for(var i=0;i<srs.length;i++)
							{
							html += '<tr class="testing">';
							  html += '<td><label>'+srs[i].trim()+'</label></td>';
							html += '<td><span id="'+i+'" class="select_style_for_series_edit btn btn-small btn-success" data-series-name="'+srs[i]+'"  data-series-column="'+srs_column[i]+'"><?php echo JText::_('COM_HEXCHART_WIDGET_SERIES_EDIT');?></span><span id="'+i+'" class="select_style_for_series_delete btn btn-small btn-success" data-series-name="'+srs[i]+'"  data-series-column="'+srs_column[i]+'" data-original-title="Delete the series" title="<?php echo JText::_('COM_HEXCHART_WIDGET_SERIES_DELETE_DESC');?>"><?php echo JText::_('COM_HEXCHART_WIDGET_SERIES_DELETE');?></span></td></tr>';
						
						}
					
					}
			    break;
			

		      }
			 html += '</table>';
			 document.getElementById('main_series_editing_section').innerHTML = html; 
		
	};	

var extra_condition_dialog='';
 function add_extra_condition(){
	 if(jQuery('select[name="params[existing_database_table]"]').val()==''){alert("<?php echo Jtext::_('COM_VDATA_WIDGET_PLEASE_SELECT_TABLE_OPTION');?>");return;}
	 var dia_width = jQuery('.container_fronthand').width();
	 var filter_setting = new Array();
	 var table_column_extra_condition='',table_column_type_extra_condition='', total_fields_column='';
	 extra_condition_dialog = jQuery( ".container_fronthand" ).dialog({
		autoOpen: true,
		height: 400,
		width:800,
		modal: true,
		draggable:true,
		show: {
				effect: 'blind',
				duration: 1000
				},
		hide: {
				effect: "blind",
				duration: 1000
				},
		open: function(event, ui){
			jQuery(this).closest(".ui-dialog").find(".ui-dialog-buttonset").find("button").eq(0).addClass("btn"); 
									jQuery(this).closest(".ui-dialog").find(".ui-dialog-buttonset").find("button").eq(1).addClass("btn"); 
									jQuery(this).closest(".ui-dialog")
									.find(".ui-dialog-titlebar-close")
									.addClass('ui-state-default')
									.html('<span class="ui-button-icon-primary ui-icon ui-icon-closethick"></span><span class="ui-button-text"></span>');
			if(jQuery('input[type=radio][name="datatype_option"]:checked').val() == 'predefined'){
				
				var table_name = jQuery('textarea[name="params[remote_query_value]"]').val();}else{var table_name = jQuery('select[name="params[existing_database_table]"]').val();}
			jQuery.ajax({
				  url: "index.php",
				  type: "POST",
				  dataType: "json",
				 data: {                        
      'option':'com_vdata'
	  , 'view':'widget'
	  , 'task':'extra_conditions'
	  , 'table_name':table_name
	  ,'datatype_option':jQuery('input[type=radio][name="datatype_option"]:checked').val()
	  , 'chart_type':jQuery('select[name="chart_type"]').val()
	  , 'id':<?php echo isset($this->widget->id)&& $this->widget->id>0?$this->widget->id:0; ?>, "<?php echo JSession::getFormToken(); ?>":1, 'abase':1},
				  beforeSend: function()	{
					jQuery(".vdata_overlay").show();
				  },
				  complete: function()	{
					jQuery(".vdata_overlay").hide();
				  },
				  success: function(res)	{
					
					if(res.result == "error"){
						 error_report = true;
						 error_message = res.error;
						alert(res.error);
						}
					else if(res.result == "success"){
						table_column_extra_condition = res.html;
						table_column_type_extra_condition = res.column_type;
						total_fields_column= res.html_column;
						//var series_extra_condition = jQuery('#series_column_name_for_existing_database').val().split(',');
			                   var filter_setting_label = new Array();
							   for(var column_condition_for_extra=0;column_condition_for_extra<table_column_extra_condition.length;column_condition_for_extra++){  
								// var column_index = table_column_extra_condition.indexOf( series_extra_condition[column_condition_for_extra]);
								
								if(table_column_type_extra_condition[column_condition_for_extra]=='int' ||table_column_type_extra_condition[column_condition_for_extra]=='tinyint' ){
								    
									filter_setting_label.push({id:table_column_extra_condition[column_condition_for_extra],label:table_column_extra_condition[column_condition_for_extra],input:"text",type:'integer',optgroup:'Integer'});
									
									}
							else if(table_column_type_extra_condition[column_condition_for_extra]=='varchar'){
								filter_setting_label.push({id:table_column_extra_condition[column_condition_for_extra],label:table_column_extra_condition[column_condition_for_extra],type:'string',optgroup:'String'});
									
									 }
									/* 
									else if(table_column_type_extra_condition[column_condition_for_extra]=='tinyint'){
										 filter_setting_label.push({id:table_column_extra_condition[column_condition_for_extra],label:table_column_extra_condition[column_condition_for_extra],input:"text",type:'integer',optgroup:'Integer'}); 
										 }*/
								
								 else if(table_column_type_extra_condition[column_condition_for_extra]=='date'||table_column_type_extra_condition[column_condition_for_extra]=='datetime'){
										filter_setting_label.push({id:table_column_extra_condition[column_condition_for_extra],label:table_column_extra_condition[column_condition_for_extra] ,type:"date",optgroup: "Date type",validation: {dateFormat:'yy-mm-dd HH:mm:ss'},plugin: 'datetimepicker', plugin_config: {dateformat: 'yy-mm-dd',timeFormat:'HH:mm:ss',
      todayBtn: 'linked',
      todayHighlight: true,
      autoclose: true
    }}); 
										
										}
								
								
								else if(table_column_type_extra_condition[column_condition_for_extra]=='mediumtext'){
									filter_setting_label.push({id:table_column_extra_condition[column_condition_for_extra],label:table_column_extra_condition[column_condition_for_extra],type:'string',optgroup:'Text'});
								}
								
								}
							
								jQuery('#builder').queryBuilder({
								sortable: true,
								
								onValidationError: function($target, err) {
								console.error(err, $target);
								},
								
								filters: filter_setting_label
								});
								jQuery('.reset').on('click', function() {
								jQuery('#builder').queryBuilder('reset');
								jQuery('#result').addClass('hide').find('pre').empty();
								});
								
								// get rules
								jQuery('.parse-json').on('click', function() {
								jQuery('#result').removeClass('hide')
								.find('pre').html(JSON.stringify(
								jQuery('#builder').queryBuilder('getRules'),
								undefined, 2
								));
								});
								
								jQuery('.parse-sql').on('click', function() {
								var res = jQuery('#builder').queryBuilder('getSQL', jQuery(this).data('stmt'), false);
								jQuery('#result').removeClass('hide')
								.find('pre').html(
								res.sql + (res.params ? '\n\n' + JSON.stringify(res.params, undefined, 2) : '')
								);
								});
								
								
					
				     }
					else
						alert(res.error);
					
				  },
				  error: function(jqXHR, textStatus, errorThrown){
					  alert(textStatus);				  
				  }
			});
		
	  },
		buttons: {
		"<?php echo JText::_('COM_VDATA_SELECT_SERIES_ADD_EXTRA_CONDITIONS');?>": add_extra,
		Cancel: function() 
		{
		extra_condition_dialog.dialog( "close" );
		}
		},
		overlay: {
         opacity: 0.7,
         background: "black"
      },
		close: function() 
		{
		
		}
		});				
	  }

 function add_extra(){
		
		 var res = jQuery('#builder').queryBuilder('getSQL', jQuery('.parse-sql-add-extra').data('stmt'), false);
         jQuery('#result').addClass('hide').find('pre').html('');
   
		 jQuery('#extra_condition').html(res.sql + (res.params ? '\n\n' + JSON.stringify(res.params, undefined, 2) : ''));
		// update_column_value();
		extra_condition_dialog.dialog( "close" );
		
}

function add_notification_condition(){
	var cols = jQuery(jQuery('select[name="params[ordering_reference_column_name]"]').clone().find(':selected').removeAttr("selected").end()).html();
	var options = '<div>';
		options += '<span><select name="params[notification][condition_col][]">'+cols+'</select></span>';
		options += '<span><select name="params[notification][condition][]">';
			options += '<option value="equal"><?php echo JText::_('equal');?></option>';
			options += '<option value="not_equal"><?php echo JText::_('not_equal');?></option>';
			options += '<option value="in"><?php echo JText::_('in');?></option>';
			options += '<option value="not_in"><?php echo JText::_('not_in');?></option>';
			options += '<option value="less"><?php echo JText::_('less');?></option>';
			options += '<option value="less_or_equal"><?php echo JText::_('less_or_equal');?></option>';
			options += '<option value="greater"><?php echo JText::_('greater');?></option>';
			options += '<option value="greater_or_equal"><?php echo JText::_('greater_or_equal');?></option>';
			options += '<option value="between"><?php echo JText::_('between');?></option>';
		options += '</select></span>';
		options += '<span><input type="text" class="condition_val" name="params[notification][condition_val][]" value="" /></span>';
		options += '<br /><span class="remove_condition btn btn-success"><?php echo JText::_('REMOVE');?></span>' 
	options += '</div>';
	
	jQuery('span.notify_conditions').parent().append(options);
	jQuery('select[name="params[notification]"]').chosen({"disable_search_threshold":0,"search_contains": true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});	
}

jQuery(document).on('click', 'span.remove_condition', function(){
	jQuery(this).parent().remove();
});

var autoOpt = {
	source: ["NOW()", "CURDATE()", "DATE_SUB(NOW(), INTERVAL 10 DAY)"],
	minLength: 0
};
jQuery(document).on('focus', '.condition_val', function(){
	 jQuery(this).autocomplete(autoOpt).focus(function(){
		jQuery(this).autocomplete("search",jQuery(this).val());
	});
});

sumbitIframe = function(tasks)
{
	Joomla.submitbutton(tasks);
	//window.parent.SqueezeBox.close();
}
function valforms(e){
	if(jQuery(e).attr('id')=='save'){
		if(query_error==false)
		{
		alert(error_message);
        valids = false;		
		}
		else
	    valids = document.formvalidator.isValid(document.id('adminForm'));
	
	}
    else if(jQuery(e).attr('id')=='cancel'){
	valids = false;
	if(window.parent.check_status != 'action_saved')
	window.parent.check_status = 'action_cancel';
   window.parent.SqueezeBox.close();	
	}	
}
function valform(){
if(valids==true){
window.parent.check_status = 'action_saved';
jQuery( "#system-message-container" ).html('');			
}  
   
return valids;		
				
}	 
</script>
<form action="index.php?option=com_vdata&view=widget" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" onsubmit = "return valform()">
<input type="hidden" name="task" value="apply" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="id" value="<?php echo $this->widget->id;?>" />

<div id="vdatapanel">
<div class="vdata_overlay" style="display:none;"> 
<img class="vdata-loading" src="<?php echo JURI::root();?>media/com_vdata/images/loading_second.gif" alt="">
</div>
<div class="vdata_add_widget_buttons">
<div class="cc-selector-2">
		   <span class="widget_apply_section">
			<input type="radio" name="datatype_option" id="check1"  value="predefined" <?php if($this->widget->datatype_option=='predefined'){echo 'checked="checked"';}?>  onchange="selectoptions(this);" />
			<label class="hasTip datatype-label drinkcard-cc visa" for="check1" title="<?php echo JText::_( 'COM_VDATA_WIDGET_CREATION_PREDEFINED_QUERY_DESC' ); ?>">
            </label>
			<span class="label-message"><?php echo JText::_( 'COM_VDATA_WIDGET_CREATION_PREDEFINED_QUERY' ); ?></span></span>
			</td>
			<td>
			<span class="widget_apply_section">
			<input type="radio" name="datatype_option" id="check2"  value="writequery" <?php if($this->widget->datatype_option=='writequery'){echo 'checked="checked"';}?> onchange="selectoptions(this);" />
			<label class="hasTip datatype-label drinkcard-cc mastercard" for="check2" title="<?php echo JText::_( 'COM_VDATA_WIDGET_CREATION_MANUALLY_QUERY_DESC' ); ?>"></label>
			<span class="label-message">
            <?php echo JText::_( 'COM_VDATA_WIDGET_CREATION_MANUALLY_QUERY' ); ?></span></span>
			</div>
<div class="vdat_button_section">
                <input type="submit" class="btn btn-small btn-success save" id="save" onclick="valforms(this);"  value="<?php echo JText::_('SAVE'); ?>"/>
				<input type="submit" class="btn btn-small cancel" id="cancel" onclick="valforms(this);" value="<?php echo JText::_('Cancel'); ?>"/>
</div>
		</div>
<div class="vdata_widget_inner">
 <div class="data_options_for_query left"></div>
 <div class="data_options_for_formating_section right"></div>
 <div id="main_series_editing_section" class="main_series_editing_section left"></div>
 <div id="dialog-series" title="<?php echo JText::_('COM_VDATA_WIDGET_ADD_COLOR_FOR_SERIES');?>" style="display:none">

<div id="select_series_to_color"></div></div>
 <div id="editor_section" class="right" style="width: 50%; display:none;">
 <?php
        $details_value = isset($this->widget->detail)?json_decode($this->widget->detail):''; 
	    $details_value = isset($details_value->style_layout_editor)?$details_value->style_layout_editor:'';
      $editor = JFactory::getEditor();
      echo $editor->display('params[style_layout_editor]',$details_value, '200px', '200px', '5', '5', true);
 ?>
 </div>
 <div id="listing_section" class="right" style="width: 50%; display:none;">
 <div class="clr"></div>

<div id="editcell">
<p class="validateTips" style="display:none;"><?php echo JText::_('COM_VDATA_ALL_FORM_FIELDS_ARE_REQUIRED');?></p>

</div>
<div id="dialog-form" title="Create new series" style="display:none"> 
<p class="validateTips"><?php echo JText::_('COM_VDATA_ALL_FORM_FIELDS_ARE_REQUIRED');?></p>


<label class="hasTipAseries" for="series_title" id="series_title-lbl" title="<?php echo JText::_('COM_VDATA_SERIES_TITLE_TO_SHOW_DESC');?>"><?php echo JText::_('COM_VDATA_SERIES_TITLE_TO_SHOW');?></label>
<input type="text" name="series_title" id="series_title" value="" class="text ui-widget-content ui-corner-all">
<label id="select_c-lbl" class="hasTipAseries" for="select_c" title="<?php echo JText::_('COM_VDATA_SELECT_COLUMN_FOR_SERIES_VALUE_DESC');?>"><?php echo JText::_('COM_VDATA_SELECT_COLUMN_FOR_SERIES_VALUE');?></label>
<span class="select_c" id="select_c"></span></div> 
<div id="dialog-confirm"></div>
<div class="container_fronthand" title="Put Extra Condition on Data" style="display:none">
  <div class="col-md-12 col-lg-10 col-lg-offset-1">
    <div id="builder"></div>
    
    <div class="btn-group">
      <button class="btn btn-warning reset"><?php echo JText::_('COM_VDATA_WIDGET_RESET');?></button>
   
    </div>
    
    <div class="btn-group">
    
      <button class="btn btn-primary parse-sql parse-sql-add-extra" data-stmt="false"><?php echo JText::_('COM_VDATA_WIDGET_SQL');?></button>
    </div>

    <div id="result" class="hide">
      <h3><?php echo JText::_('COM_VDATA_WIDGET_OUTPUT');?></h3>
      <pre></pre>
    </div>
  </div>
</div>
</div>
<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_vdata" />

</form>

</div>