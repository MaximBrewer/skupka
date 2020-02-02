<?php
defined('_JEXEC') or die('Restricted access');
/**
* Param Filter: Virtuemart 2 search module
* Version: 3.0.6 (2015.11.23)
* Author: Dmitriy Usov
* Copyright: Copyright (C) 2012-2015 usovdm
* License GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* http://myext.eu
**/

if($show_filter){
	echo '<div id="paramfilter-'.$module_id.'" class="paramfilter ver160">';
	echo   '<script type="text/javascript">
				var mcf_uri = "'.$doc->baseurl.'"'. PHP_EOL . 'loader_img["paramfilter-'.$module_id.'"] = "'.$loader_img.'";';
	echo '</script>';
	echo !empty($mcf_prepend_text) ? '<div class="mcf_prepend_text">'.$mcf_prepend_text.'</div>' : '';
	if(!empty($Itemid)){
		if($Itemid == (int)$Itemid){
			$link = JRoute::_('index.php?Itemid='.$Itemid);
		}else{
			$link = $Itemid;
		}
	}else{
		$link = JRoute::_('index.php?option=com_virtuemart&view=category&search=true&virtuemart_category_id=0');
	}
	echo '<form class="mcf_form'.$mcf_mod_ajax_css.'" action="'.str_replace('modules/mod_virtuemart_param_filter/','',$link).'" method="'.$method.'">
			<input type="hidden" name="option" value="com_virtuemart" />
			<input type="hidden" name="search" value="true" />
			<input type="hidden" name="view" value="category" />
			<input type="hidden" name="mcf_id" value="'.$module_id.'" />
			<div class="ajax_url" style="display:none;">'.str_replace('modules/mod_virtuemart_param_filter/','',JUri::base()).'modules/mod_virtuemart_param_filter/ajax.php?search=true&amp;virtuemart_category_id=0'.'</div>';
	if($stock){
		echo '<input type="hidden" name="s" value="1" />'; // instock trigger
	}
	if(!empty($mcf_mod_ajax_div)){
		echo '<div style="display:none;" id="ajax_div">'.$mcf_mod_ajax_div.'</div>'; // instock trigger
	}
	if(isset($_SESSION['__vm']['vmlastvisitedcategoryid'])){
		if(in_array(JRequest::getVar('option'),array('com_virtuemart')) && !in_array(JRequest::getVar('view'),array('category','productdetails'))){
			$_SESSION['__vm']['vmlastvisitedcategoryid'] = 0;
		}else{
			echo '<div style="display:none;" id="mcf_vmlastvisitedcategoryid">'.$_SESSION['__vm']['vmlastvisitedcategoryid'].'</div>';
		}
	}
	echo '<table class="horizontbox"><tr>';
	echo empty($categories_html) ? '' : '<td>'.$categories_html.'</td>';
	echo empty($manufacturers_html) ? '' : '<td>'.$manufacturers_html.'</td>';
	echo empty($price_html) ? '' : '<td>'.$price_html.'</td>';
	foreach($types as $type){
		$tmp_params = $type->custom_params;
		$tmp_params = explode('|',$tmp_params);
		$custom_params = array();
		foreach($tmp_params as $v){
			preg_match("/^([^=]*)=(.*)|/i",$v, $res);
			$custom_params[@$res[1]] = json_decode(@$res[2]);
		}
		if(isset($custom_params['af']) && (int)$custom_params['af']){
			$assign_field = JRequest::getVar('cv'.(int)$custom_params['af'],array());
			$assign_field = array_diff($assign_field,array('',null));
			if(isset($assign_field) && count($assign_field) > 0){
				if(isset($custom_params['av']) && !empty($custom_params['av'])){
					$custom_params['av'] = explode(';',$custom_params['av']);
					$hide = true;
					foreach($custom_params['av'] as &$v){
						if(in_array($v,$assign_field)){
							$hide = false;
							continue;
						}
					}unset($v);
					if($hide){
						continue;
					}
				}
			}else{
				continue;
			}
		}
		$customfield_value = isset($custom_values[$type->virtuemart_custom_id]) ? $custom_values[$type->virtuemart_custom_id] : array();
		if(isset($custom_params['ft']) && $custom_params['ft'] == 'int'){
			$customfield_value = isset($pre_int_values[$type->virtuemart_custom_id]) ? $pre_int_values[$type->virtuemart_custom_id] : array();
			sort($customfield_value);
			$customfield_value_vis = isset($pre_int_values_visible[$type->virtuemart_custom_id]) ? $pre_int_values_visible[$type->virtuemart_custom_id] : array();
			sort($customfield_value_vis);
		}else{
		}
		echo '<input type="hidden" name="cpi[]" value="'.$type->virtuemart_custom_id.'" />';
		$html = '';
		if(isset($custom_params['z']) && $custom_params['z'] != 'default' && $count_zero_show['z'] != $count_zero_show){
			$count_zero_show = $custom_params['z'];
			$count_zero_show_txt = $count_zero_show == 'disable' ? ' disabled="disabled"' : '';
		}
		$customfields_layout_tmp = $customfields_layout == 'auto' ? $custom_params['t'] : $customfields_layout;
		$count_sum = 0;
		$mcf_customfields_select_heading = isset($custom_params['ld']) && !empty($custom_params['ld']) ? $custom_params['ld'] : $mcf_customfields_select_heading_global;
		require(JModuleHelper::getLayoutPath('mod_virtuemart_param_filter','customfields'.DS.$customfields_layout_tmp)); // Generate customfields html
		if($count_zero_show != 'hidden' || $count_sum > 0){
			echo '<td>';
			echo $html;
			echo '</td>';
		}
		$count_zero_show = $count_zero_show_tmp;
		$count_zero_show_txt = $count_zero_show_txt_tmp;
	}
	if(isset($type_req->virtuemart_custom_id)){
		echo '<td><input type="hidden" name="custom_parent_id" value="'.$type_req->virtuemart_custom_id.'" />';
	}else{
		echo '<span style="color:#f00;font-size:80%;">Please create at least one customfield of required type according to documentation</span>';
	}
	echo '<input type="hidden" name="limitstart" value="0" />';
	echo '<input type="hidden" name="limit" value="'.$limit.'" />';
	$all_count = $show_all_count && $param_search_ids ? ' ('.count($param_search_ids).')' : '';
	echo '<input class="mcf_button" style="padding:5px;" type="submit" value="'.JText::_('MOD_VMCUSTOM_PARAM_FILTER_SEARCH').$all_count.'" />';
	echo '<a class="fullreset" href="#">'.JText::_('MOD_VMCUSTOM_PARAM_FILTER_RESET').'</a>';
	echo '</form>';
	echo '</td>';
	echo '</tr>';
	echo '</table>';
	echo !empty($mcf_append_text) ? '<div class="mcf_append_text">'.$mcf_append_text.'</div>' : '';
	echo '</div>';
}