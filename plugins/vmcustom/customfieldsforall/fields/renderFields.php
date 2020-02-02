<?php
/**
 * @version		$Id: customvalues.php 2013-07-01 19:12 sakis Terz $
 * @package		customfieldsforall
 * @copyright	Copyright (C)2013 breakdesigns.net . All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');
if(!class_exists('Customfield'))require(JPATH_PLUGINS.DIRECTORY_SEPARATOR.'vmcustom'.DIRECTORY_SEPARATOR.'customfieldsforall'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'customfield.php');

class RenderFields{

	function fetchDatatype($fieldname='data_type', $virtuemart_custom_id, $value='string')
	{

		$options_array=array(
		'string'=>JText::_('PLG_CUSTOMSFORALL_STRING'),
		'color_hex'=>JText::_('PLG_CUSTOMSFORALL_COLOR_HEX'),
		'int'=>JText::_('PLG_CUSTOMSFORALL_INT'),
		'float'=>JText::_('PLG_CUSTOMSFORALL_FLOAT')
		);

		if($virtuemart_custom_id){
			$html=$options_array[$value];
			$html.='<input type="hidden" name="'.$fieldname.'" value="'.$value.'"/>';
		}
		else{
			$options_new_array=array();
			foreach ($options_array as $key=>$val){
				$myOpt=new stdClass();
				$myOpt->text=$val;
				$myOpt->value=$key;
				$options_new_array[]=$myOpt;
			}
			$html=JHtml::_('select.genericlist',$options_new_array,$fieldname,'class="inputbox required"','value', 'text',$value);
		}

		return $html;
	}

	function fetchDisplaytypes($fieldname, $virtuemart_custom_id,$default){
		$displaytypes=array(
		'button'=>JText::_('PLG_CUSTOMSFORALL_BTN'),
		'button_multi'=>JText::_('PLG_CUSTOMSFORALL_BTN_MULTI'),
		'color'=>JText::_('PLG_CUSTOMSFORALL_COLOR_BTN'),
		'color_multi'=>JText::_('PLG_CUSTOMSFORALL_COLOR_BTN_MULTI'),
		'checkbox'=>JText::_('PLG_CUSTOMSFORALL_CHECKBOXES'),
		'radio'=>JText::_('PLG_CUSTOMSFORALL_RADIO_BTN'),
		'select'=>JText::_('PLG_CUSTOMSFORALL_SELECT_LIST'),
		);

		//assoc array containing the valid display types for each datatype
		$datatypes=array(
		'string'=>array('display_types'=>array('button','button_multi','color','color_multi','checkbox','radio','select')),
		'color_hex'=>array('display_types'=>array('button','button_multi','color','color_multi','checkbox','radio','select')),
		'int'=>array('display_types'=>array('button','button_multi','checkbox','radio','select')),
		'float'=>array('display_types'=>array('button','button_multi','checkbox','radio','select')),
		);

		if(!empty($virtuemart_custom_id)){
			$customfield=Customfield::getInstance($virtuemart_custom_id);
			$custom_params=$customfield->getCustomfieldParams($virtuemart_custom_id);
			$datatype=$custom_params['data_type'];
		}

		$options=array();
			
		foreach ($displaytypes as $key=>$value){
			$option=array(
			'value'=>$key,
			'text'=>$value,			
			);

			if(isset($datatype) && !empty($datatypes[$datatype])){
				if(!in_array($key, $datatypes[$datatype]['display_types'])){
					$option['attr']=array('disabled'=>'true');
					if($default==$key)$default='button';
				}
			}
			$options[]=$option;
		}

		$properties = array(
	    'id' => 'displaytypes', // HTML id for select field
	    'list.attr' => array('class'=>'inputbox required',),
	    'option.value'=>'value', // key name for value in data array
	    'option.text'=>'text', // key name for text in data array
	    'option.attr'=>'attr', // key name for attr in data array
	    'list.select'=>$default, // value of the SELECTED field
		);

		$html=$result = JHtmlSelect::genericlist($options,$fieldname,$properties);
		//JHtml::_('select.genericlist',$options,$fieldname,'class="inputbox required"','value', 'text',$default);
		return $html;
	}


	/**
	 * Function to fetch the custom value inputs
	 *
	 * @param string $fieldname - the name with which all the passed inputs will start
	 * @param int $virtuemart_custom_id
	 * @param int $row - used mainly within the product form, where each custom has its own row
	 */
	function fetchCustomvalues($fieldname,$virtuemart_custom_id,$value='',$row=0,$custom_params){
		$jinput=JFactory::getApplication()->input;
		$view=$jinput->get('view','','STRING');
		$is_jscolor_loaded=$jinput->get('scripts_loaded',false,'BOOLEAN');
		$existing_values=array();
		$customfield=Customfield::getInstance($virtuemart_custom_id);		
		$single_entry=$custom_params['single_entry'];

		$data_type=!empty($custom_params['data_type'])?$custom_params['data_type']:'string';
		$is_custom_view=false;
		$is_stored=false;
		$class='input';
		$html='';
		$display_id=$row.'_'.$virtuemart_custom_id;


		if(!empty($virtuemart_custom_id) && $view=='custom'){
			$is_custom_view=true;
			$existing_values=$customfield->getCustomValues();
		}


		//load the js color script
		if(!empty($custom_params) && $data_type=='color_hex'){
			$class.=' color {required:false}';

			//if the color script does not exist we should load the script in the returned html
			if(!$is_jscolor_loaded){
				$color_script_path=JPATH_PLUGINS.DIRECTORY_SEPARATOR.'vmcustom'.DIRECTORY_SEPARATOR.'customfieldsforall'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'jscolor'.DIRECTORY_SEPARATOR.'jscolor.js';
				ob_start();
				readfile($color_script_path);
				$html.='<script type="text/javascript">'.ob_get_contents().'</script>';
				ob_end_clean();
				$jinput->set('scripts_loaded',true);
			}
		}
		//print_r((boolean)$custom_params['is_price_variant']);



		$ordering=0;
		$counter=0;
		$width_total=72;

		$html.='
		<ul style="clear:both; list-style:none;" id="cf_values_wrapper'.$display_id.'" class="sortable">';

		if(!empty($existing_values)){
			foreach ($existing_values as $obj){
				$color_pallette_counter=0;
				$pk=$obj->customsforall_value_id;
				$html.='
				<li id="customsforall_value_li_'.$counter.'" style="min-width:300px;">
					<div class="removable" style="padding:5px 10px; background:#ededed; margin-bottom:1em;">';

				if(!empty($custom_params) && $data_type=='color_hex'){
					$custom_values=explode('|', $obj->customsforall_value_name);
					$no_values=count($custom_values);
					$width=$width_total/$no_values;
					$html.='<div class="cf4all-color-value-wrapper" id="color-value-wrapper-'.$counter.'" style="width:300px;">';
					foreach($custom_values as $key=>$cf){
						$html.='<input style="width:'.$width.'%;" type="text" name="cf_val['.$counter.'][customsforall_value_name]['.$key.']" value="'.$cf.'" class="'.$class.'"/>';
					}
					$html.='</div>';//cf4all-color-value-wrapper
					$html.='
							<div class="cf4all-number-values-wrapper jgrid" id="number-values-wrapper-'.$counter.'">
								<input type="text" name="cf4all-number-values_'.$counter.'" id="cf4all-number-values_'.$counter.'" value="'.$no_values.'" size="2" disabled/>
								<span style="display:inline-block; cursor:pointer; min-width:13px;" class="cf4all-incdec uparrow"><i class="icon-arrow-up">&nbsp&nbsp&nbsp</i></span>
								<span style="display:inline-block; cursor:pointer; min-width:13px;" class="cf4all-incdec downarrow"><i class="icon-arrow-down">&nbsp&nbsp&nbsp</i></span>
							</div>';


					$html.='<input style="width:72%;" type="text" name="cf_val['.$counter.'][customsforall_value_label]" placeholder="'.JText::_('PLG_CUSTOMSFORALL_NEW_LABEL').'" value="'.$obj->customsforall_value_label.'" class="cf_value_label"/>';
				}else{
					$html.='<input style="width:75%;" type="text" name="cf_val['.$counter.'][customsforall_value_name]" value="'.$obj->customsforall_value_name.'" class="'.$class.'"/>';
				}
				$html.='<input type="hidden" name="cf_val['.$counter.'][customsforall_value_id]" value="'.$obj->customsforall_value_id.'"/>
						<span class="vmicon vmicon-16-move"></span>
						<a href="#" class="vmicon vmicon-16-remove customsforall_delete_btn" data-row_id="'.$counter.'" alt="'.JText::_('PLG_CUSTOMSFORALL_DELETE').'"></a>					
					</div>
				</li>';
				$ordering=$obj->ordering;
				$counter++;
			}
		}

		$html.='</ul>
		<button class="btn" id="cf_newvalue_btn'.$display_id.'" type="button">'.JText::_('PLG_CUSTOMSFORALL_NEW_VALUE').'</button>';


		$script='
		<script type="text/javascript">';
		$script.='
		jQuery(function($){
			$(".cfield-chosen-select").chosen({width:"200px",display_selected_options:false});
			is_added_'.$display_id.'=false;
			$("#cf_values_wrapper'.$display_id.'").delegate("a.customsforall_delete_btn","click",function(){
				$(this).parents("li").remove();
				is_added_'.$display_id.'=false;
				return false;
			});
			
			var counter='.$counter.';			
			$("#cf_newvalue_btn'.$display_id.'").click(function(){';

		if(!$is_custom_view && (boolean)$single_entry)$script.='if(!is_added_'.$display_id.'){';
		if($data_type!='color_hex'){
			$script.='var elem_appended=\'<li id="customsforall_value_li_\'+counter+\'" style="min-width:300px;"><div class="removable" style="padding:5px 10px; background:#ededed; margin-bottom:1em;"><input style="width:75%;" type="text" name="'.$fieldname.'[\'+counter+\'][customsforall_value_name]" value="" placeholder="'.JText::_('PLG_CUSTOMSFORALL_NEW_VALUE').'" class="'.$class.'" id="cf_value_input'.$display_id.'\'+counter+\'"/><input type="hidden" name="'.$fieldname.'[\'+counter+\'][customsforall_value_id]" value="0"/><span class="vmicon vmicon-16-move"></span><a href="#" class="vmicon vmicon-16-remove customsforall_delete_btn" alt="'.JText::_('PLG_CUSTOMSFORALL_DELETE').'"></a></div></li>\';';
		}
		else{
			//color hex
			//$script.='var elem_appended=\'<li id="customsforall_value_li_\'+counter+\'" style="min-width:300px;"><div class="removable" style="padding:5px 10px; background:#ededed"><input style="width:'.$width_total.'%;" type="text" name="'.$fieldname.'[\'+counter+\'][customsforall_value_name][0]" value="" placeholder="'.JText::_('PLG_CUSTOMSFORALL_NEW_VALUE').'"  class="'.$class.'" id="cf_value_input'.$row.'\'+counter+\'"/><input style="width:36%;" type="text" name="'.$fieldname.'[\'+counter+\'][customsforall_value_label]" value="" placeholder="'.JText::_('PLG_CUSTOMSFORALL_NEW_LABEL').'" class="cf_value_label" id="cf_label_input'.$row.'\'+counter+\'"/><input type="hidden" name="'.$fieldname.'[\'+counter+\'][customsforall_value_id]" value="0"/><span class="vmicon vmicon-16-move"></span><a href="#" class="vmicon vmicon-16-remove customsforall_delete_btn" alt="'.JText::_('PLG_CUSTOMSFORALL_DELETE').'"></a></div></li>\';';
			$script.='var elem_appended=\'<li id="customsforall_value_li_\'+counter+\'" style="min-width:300px;"><div class="removable" style="padding:5px 10px; background:#ededed"><div class="cf4all-color-value-wrapper" id="color-value-wrapper-\'+counter+\'" style="width:300px;"><input style="width:'.$width_total.'%;" type="text" name="'.$fieldname.'[\'+counter+\'][customsforall_value_name][]" value="" class="'.$class.'" id="cf_value_input'.$display_id.'\'+counter+\'"/></div><div class="cf4all-number-values-wrapper jgrid" id="number-values-wrapper-\'+counter+\'"><input type="text" name="cf4all-number-values_\'+counter+\'" id="cf4all-number-values_\'+counter+\'" value="1" size="2" disabled/><span style="display:inline-block; cursor:pointer; min-width:13px;" class="cf4all-incdec uparrow"><i class="icon-arrow-up">&nbsp&nbsp&nbsp</i></span><span style="display:inline-block; cursor:pointer; width:13px;" class="cf4all-incdec downarrow"><i class="icon-arrow-down">&nbsp&nbsp&nbsp</i></span></div><input style="width:72%;" type="text" name="'.$fieldname.'[\'+counter+\'][customsforall_value_label]" value="" placeholder="'.JText::_('PLG_CUSTOMSFORALL_NEW_LABEL').'" class="cf_value_label" id="cf_label_input'.$display_id.'\'+counter+\'"/><input type="hidden" name="'.$fieldname.'[\'+counter+\'][customsforall_value_id]" value="0"/><span class="vmicon vmicon-16-move"></span><a href="#" class="vmicon vmicon-16-remove customsforall_delete_btn" alt="'.JText::_('PLG_CUSTOMSFORALL_DELETE').'"></a></div></li>\';';
		}

		$script.='$("#cf_values_wrapper'.$display_id.'").append(elem_appended);
				is_added_'.$display_id.'=true;
				mypicker="myPicker"+counter;';
		if($data_type=='color_hex')$script.='var  mypicker= new jscolor.color(document.getElementById(\'cf_value_input'.$display_id.'\'+counter), {});';
		$script.='counter++;';
		if(!$is_custom_view && $single_entry)$script.='}';
		$script.='return false;
			});
			
			
			jQuery("#cf_values_wrapper'.$display_id.'").sortable({handle: ".vmicon-16-move"});';

		if($data_type=='color_hex'){
			$script.='
			//create the incr-decr effect
			$("#cf_values_wrapper'.$display_id.'").delegate("span.cf4all-incdec", "click",function(){
				var $button = $(this);
				var counter=($button.parent().attr("id").match(/\d+$/));
				var oldValue = parseInt($button.parent().find("input").val());
				
				if($button.hasClass("uparrow")){
					var newValue=oldValue+1;
					var elem_appended=\'<input style="width:'.$width_total.'%;" type="text" name="'.$fieldname.'[\'+counter+\'][customsforall_value_name][]" value="" class="'.$class.'" id="cf_value_input'.$display_id.'\'+counter+\'_\'+newValue+\'"/>\';					
					jQuery("#color-value-wrapper-"+counter).append(elem_appended);
					var  mypicker= new jscolor.color(document.getElementById(\'cf_value_input'.$display_id.'\'+counter+\'_\'+newValue), {});			
				}else if(oldValue>1){
					var newValue=oldValue-1;
					jQuery("#color-value-wrapper-"+counter+" .color:last-child").remove();
						
				}else{newValue=1}
				$button.parent().find("input").val(newValue);
				return true;
			});';
		}

		//end of jQuery
		$script.='	});';
		$script.='
		</script>';

		$html.=$script;
		return $html;
	}
}
