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
$plr = JRequest::getVar('plr',null);
$reset = !empty($plr) ? '<a class="reset" href="#">[x]</a>' : '';
$prices_array = array(
	"0-10" => " < 10$",
	"10-50" => "10$ - 50$",
	"50-100" => "50$ - 100$",
	"100-500" => "100$ - 500$",
	"500-1000" => "500$ - 1000$",
	"1000" => " > 1000$"
);
		
$html .= '<div class="price">';
if(!empty($mcf_price_heading))
	$html .= '<div class="heading">'.$mcf_price_heading.'</div>';
$html .= '<div class="values" data-id="p"><select name="plr" class="chosen" style="width:100%;" data-placeholder="'.$mcf_price_select_heading.'"><option value=""></option>';
foreach($prices_array as $k => $v){
	$selected = $plr==$k ? ' selected="selected"' : '';
	$html .= '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
}
$html .= '</select></div></div>';