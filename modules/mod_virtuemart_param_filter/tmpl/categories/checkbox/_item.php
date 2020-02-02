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

function recursiveListcheckbox($categories,$active_categories=array(),$parent_category_id,$depth,$tmpl){
	$html = '';
	foreach($categories as $v){
		if($v->category_parent_id == $parent_category_id){
			$v->depth = $depth + 1;
			$html .= '<li>';
			$checked = (isset($active_categories) && in_array($v->virtuemart_category_id,$active_categories)) ? ' checked="checked"' : '';
			if($v->depth > 1){
				$html .= str_repeat('&nbsp;&nbsp;',$v->depth - 1).'<sup>L</sup>&nbsp;';
			}
			$html .= '<label><input type="checkbox" name="cids[]" value="'.$v->virtuemart_category_id.'"'.$checked.' /><span>'.$v->category_name.'</span></label>';
			$child = recursiveListcheckbox($categories,$active_categories,$v->category_child_id,$v->depth,$tmpl);
			if(!empty($child)){
				$html .= '&nbsp;<a class="next_depth" href="#">&darr;&uarr;</a>';
				$html .= '<ul style="display:none;">'.$child.'</ul>';
			}
			$html .= '</li>';
		}
	}
	return $html;
}