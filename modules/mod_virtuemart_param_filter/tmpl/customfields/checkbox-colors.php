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
$selected_values = JRequest::getVar('cv'.$type->virtuemart_custom_id, array());
$selected_values = array_diff($selected_values,array(''));
$reset = !empty($selected_values) ? '<a class="reset" href="#">[x]</a>' : '';
$html .= '<div class="heading">'.JText::_($custom_params['n']).$tip.$reset.'</div>';
if(!empty($customfield_value)){
	$html .= '<ul class="values values-named cv-'.$type->virtuemart_custom_id.$custom_class.'" data-id="'.$type->virtuemart_custom_id.'"'.$custom_pid.$custom_pval.'>';
	foreach($customfield_value as $v){
		$vid = $custom_params['ft'] == 'int' ? $v : $v->id;
		$value = $custom_params['ft'] == 'int' ? $v : JText::_($v->value);
		$counts = $custom_params['ft'] == 'int' ? $custom_int_count : $custom_text_count;
		$checked = !$param_search_ids_clear && isset($selected_values) && in_array($vid,$selected_values) ? ' checked="checked"' : '';
		$checked_css = !$param_search_ids_clear && isset($selected_values) && in_array($vid,$selected_values) ? ' checked' : '';
		/* ----- + Count calculate ----- */
		$count = calcCount($counts,$vid);
		$count_sum += $count;
		$count_txt = $count_show ? '<span class="count"> ['.$count.']</span>' : '';
		$disabled = $count == 0 ? $count_zero_show_txt : '';
		$disable_css = $count == 0 ? ' '.$count_zero_show : '';
		$slug_css = JFilterOutput::stringURLSafe($value);
		/* ----- - Count calculate ----- */
		if($count_zero_show != 'hidden' || $count > 0){
			$html .= '<li class="'.$checked_css.'"><label class="filter '.$slug_css.$checked_css.$disable_css.'" ><input type="checkbox" name="cv'.$type->virtuemart_custom_id.'[]" value="'.$vid.'"'.$checked.$disabled.' /><span class="f-title"><b>'.$value.$count_txt.'</b></span><span class="color"></span></label></li>';
		}
	}
	$html .= '</ul>';
}
$html .= '</div>';