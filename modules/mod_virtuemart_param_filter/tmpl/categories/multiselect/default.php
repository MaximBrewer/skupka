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

$multiple = true;

$html .= '<div class="filter_category">';
if($mcf_category_heading)
	$html .= '<div class="heading">'.$mcf_category_heading.'</div>';
$html .= '<div class="values" data-id="c"><select name="cids[]" multiple size="5" style="width:100%;">'.recursiveList($categories,$cids,$parent_category_id,0,'select').'</select></div>';
$html .= '</div>';