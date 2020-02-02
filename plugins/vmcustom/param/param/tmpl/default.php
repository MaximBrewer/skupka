<?php
defined('_JEXEC') or die('Restricted access');
/**
* Param: Virtuemart 2 customfield plugin
* Version: 3.0.6 (2015.11.23)
* Author: Dmitriy Usov
* Copyright: Copyright (C) 2012-2015 usovdm
* License GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* http://myext.eu
**/
if(!isset($viewData)) $viewData = $params; // VM 2.0.6 Fix
$html = '<div class="product-field-'.$viewData->virtuemart_custom_id.'">';
$html .= '<div class="mcf-fields-title">'.JText::_($viewData->custom_title).'</div>';
// $values = $viewData->ft == 'int' ? array($viewData->intvalue) : explode('|',substr($viewData->value,1,-1));
$values = $viewData->value;
if(count($values) > 0){
	$html .='<div class="product-fields-value">';
	$html .= '<ul>';
	foreach($values as &$v){
		if($viewData->ft == 'int'){
			$html .= '<li>'.$v->intval.'</li>';
		}else{
			$html .= '<li>'.JText::_($v->value).'</li>';
		}
	}unset($v);
	$html .= '</ul>';
	$html .='</div></div>';
	echo $html;
}