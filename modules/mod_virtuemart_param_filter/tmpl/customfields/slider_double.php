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

$custom_class = isset($custom_params['af']) && (int)$custom_params['af'] ? ' custom_child custom_child-'.(int)$custom_params['af'] : '';
$custom_pid = isset($custom_params['af']) && (int)$custom_params['af'] ? ' data-pid="'.(int)$custom_params['af'].'"' : '';
$custom_pval = isset($custom_params['av']) && is_array($custom_params['av']) && count($custom_params['av']) > 0 ? ' data-pval="'.implode(';',$custom_params['av']).'"' : '';

$html .= '<div class="custom_params custom_params-'.$type->virtuemart_custom_id.'">';
$tip = !empty($type->custom_tip) ? ' <span class="mcf_tip hasTip" title="'.$type->custom_tip.'">[?]</span>' : '';
$selected_values = JRequest::getVar('cv'.$type->virtuemart_custom_id,array());
$selected_values = array_diff($selected_values,array(''));
$reset = !empty($selected_values) ? '<a class="reset" href="#">[x]</a>' : '';
$html .= '<div class="heading">'.$reset.JText::_($custom_params['n']).$tip.'</div>';
if(!empty($customfield_value)){
	$slider = true;
	if(isset($custom_params['ft']) && $custom_params['ft'] == 'int'){ 
		$custom_params['vd'] = array('gt' => $customfield_value[0],'lt' => $customfield_value[count($customfield_value)-1]);
		$custom_params['vd_vis'] = array('gt' => @$customfield_value_vis[0],'lt' => @$customfield_value_vis[count($customfield_value_vis)-1]);
		// Handle int only
		$html .= '<div class="values sliderbox slider-double-handle cv-'.$type->virtuemart_custom_id.$custom_class.'" data-id="'.$type->virtuemart_custom_id.'"'.$custom_pid.$custom_pval.'>';
		$value = !$param_search_ids_clear && isset($selected_values['gt']) ? $selected_values['gt'] : '';
		$html .= '<input class="slider-range-gt" rel="'.$custom_params['vd']['gt'].'" rev="'.$custom_params['vd_vis']['gt'].'" type="text" name="cv'.$type->virtuemart_custom_id.'[gt]" value="'.$value.'" size="5" placeholder="'.$custom_params['vd']['gt'].'" />';
		$value = !$param_search_ids_clear && isset($selected_values['lt']) ? $selected_values['lt'] : '';
		$html .= '<input class="slider-range-lt" rel="'.$custom_params['vd']['lt'].'" rev="'.$custom_params['vd_vis']['lt'].'" type="text" name="cv'.$type->virtuemart_custom_id.'[lt]" value="'.$value.'" size="5" placeholder="'.$custom_params['vd']['lt'].'" />';
		$html .= '<div style="clear:both;"></div>';
		$html .= '<div class="slider-line"></div>';
		$html .= '</div>';
	}else{
		// Text
		$html .= '<div class="values sliderbox slider-double-list cv-'.$type->virtuemart_custom_id.$custom_class.'" data-id="'.$type->virtuemart_custom_id.'"'.$custom_pid.$custom_pval.'>';
		$html .= '<div class="slider-range-gt"></div>';
		foreach($customfield_value as $k=>$v){
			$vid = $v->id;
			$value = JText::_($v->value);
			$counts = $custom_text_count;
			$checked = !$param_search_ids_clear && isset($selected_values) && in_array($vid,$selected_values)? ' checked="checked"' : '';
			/* ----- + Count calculate ----- */
			$count = calcCount($counts,$vid);
			$count_sum += $count;
			$count_txt = $count_show ? '</span><span class="count"> ['.$count.']' : '';
			/* ----- - Count calculate ----- */
			$hidden_css = ($count > 0) ? 'class="slider_visible"' : '';
			$html .= '<label class="slider-value" style="display:none;"><input type="checkbox" name="cv'.$type->virtuemart_custom_id.'[]" value="'.$vid.'"'.$hidden_css.$checked.' /><span>'.$value.$count_txt.'</span></label>';
		}
		$html .= '<div class="slider-line"></div>';
		$html .= '<div class="slider-range-lt"></div>';
		$html .= '</div>';
	}
}
$html .= '</div>';