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

$chosen = true;
$html .= '<div class="filter_manufacturers">';
if(!empty($mcf_manufacturers_heading)){
	$reset = !empty($mids) ? '<a class="reset" href="#">[x]</a>' : '';
	$html .= '<div class="heading">'.$mcf_manufacturers_heading.'</div>';
}
if(count($manufacturers) > 0){
	$html .= '<div class="values" data-id="m">';
	$html .= '<select name="mids[]" class="chosen" style="width:100%;" data-placeholder="'.$mcf_manufacturers_select_heading.'">';
	$html .= '<option value=""></option>';
	foreach($manufacturers as $v){
		$selected = isset($mids) && in_array($v->virtuemart_manufacturer_id,$mids)? ' selected="selected"' : '';
		
		/* ----- + Count calculate ----- */
		$v->count = isset($manufacturers_count[$v->virtuemart_manufacturer_id]->count) ? $manufacturers_count[$v->virtuemart_manufacturer_id]->count : 0;
		$count_txt = $count_show ? '</span><span class="count"> ['.$v->count.']' : '';
		$disabled = $v->count == 0 ? $count_zero_show_txt : '';
		$disable_css = $v->count == 0 ? ' '.$count_zero_show : '';
		/* ----- - Count calculate ----- */
		if($count_zero_show != 'hidden' || $v->count > 0){
			$html .= '<option value="'.$v->virtuemart_manufacturer_id.'"'.$selected.$disabled.'>'.$v->mf_name.$count_txt.'</option>';
		}
	}
	$html .= '</select>';
	$html .= '</div>';
}
$html .= '</div>';