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

$html .= '<div class="filter_category">';
if($mcf_category_heading)
	$html .= '<div class="heading">'.$mcf_category_heading.'</div>';
$html .= '<div class="values" data-id="c"><select class="chosen" name="cids[]" style="width:200px;" data-placeholder="'.$mcf_category_select_heading.'"><option value=""></option>'.recursiveList($categories,$cids,$parent_category_id,0,'select-chosen').'</select></div>';
$html .= '</div>';