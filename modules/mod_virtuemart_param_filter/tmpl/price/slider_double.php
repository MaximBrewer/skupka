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

$html .= '<div class="price">';
$selected_values = array(JRequest::getVar('pl',''),JRequest::getVar('pr',''));
$selected_values = array_diff($selected_values,array(''));
$reset = !empty($selected_values) ? '<a class="reset" href="#">[x]</a>' : '';
if(!empty($mcf_price_heading))
	$html .= '<div class="heading">'.$mcf_price_heading.$reset.'</div>';
$slider = true;
$html .= '<div class="values sliderbox slider-double-handle" data-id="p">';
$html .= '<input type="text" class="slider-range-gt" name="pl" rev="'.floor($price_limits_visible[0]->min).'" rel="'.floor($price_limits[0]->min).'" placeholder="'.floor($price_limits[0]->min).'" value="'.$price_left.'" size="4" />';
$html .= '<input type="text" class="slider-range-lt" name="pr" rev="'.ceil($price_limits_visible[0]->max + 1).'" rel="'.ceil($price_limits[0]->max + 1).'" placeholder="'.ceil($price_limits[0]->max + 1).'" value="'.$price_right.'" size="4" />';
$html .= '<div style="clear:both;"></div>';
$html .= '<div class="slider-line"></div>';
$html .= '</div>';
$html .= '</div>';