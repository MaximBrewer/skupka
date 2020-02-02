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
class JFormFieldParamvalues extends JFormField {

    var $type = 'paramvalues';

    function getInput(){
		$name = $this->name;
		$value = $this->value;
		
	    $db = JFactory::getDBO();
		$virtuemart_custom_id = JRequest::getVar('virtuemart_custom_id',0);
		if(is_array($virtuemart_custom_id)){
			$virtuemart_custom_id = reset($virtuemart_custom_id);
		}
		$q = 'SELECT * FROM `#__virtuemart_product_custom_plg_param_values` WHERE `virtuemart_custom_id` = '.$virtuemart_custom_id.' ORDER BY ordering';
        $db->setQuery($q);
		$values = $db->loadObjectList();
		$html  = '<div style="width:500px;"><table border="1" style="border-collapse:collapse;"><tr><td width="35px">ID</td><td width="175px">Value</td><td>Ordering</td><td>Publish<td>-</td></tr></table></div>';
		$html .= '<ul id="dv_box" style="width:500px;">';
		if(!empty($values)){
			foreach($values as $k => &$v){
				$checked = $v->published == 1 ? ' checked="checked"' : '';
				$html .= '<li>
							<input name="vd['.$k.'][id]" type="text" size="1" value="'.$v->id.'" readonly="readonly" disable="disable" />
							<input name="vd['.$k.'][value]" type="text" size="32" value="'.$v->value.'" />
							<input class="ordering" name="vd['.$k.'][ordering]" type="text" size="1" value="'.$v->ordering.'" />
							<input name="vd['.$k.'][published]" type="hidden" value="0" />
							<input name="vd['.$k.'][published]" type="checkbox" value="1"'.$checked.' />
							<input name="vd['.$k.'][status]" type="text" size="1" value="'.$v->status.'" />
							<a class="dv_sort" href="#"></a><a class="dv_delete" href="#"></a>
						</li>';
			}unset($v);
		}
		$html .= '</ul>';
		$html .= '<a id="new_dv" href="#">'.JText::_('PLG_VMCUSTOM_PARAM_ADD_VALUE').'</a>';
		$html .= '<script type="text/javascript">
					(function($){
						function dv_reorder(){
							var ordering = 0;
							$("#dv_box li").each(function(){
								ordering++;
								$("input.ordering",$(this)).val(ordering);
							})
						}
						
						var k = 0;
						$("a#new_dv").click(function(){
							k++;
							$("#dv_box").append(\'<li><input name="vd_new[\'+k+\'][id]" type="text" size="1" value="" readonly="readonly" disable="disable" /><input name="vd_new[\'+k+\'][value]" type="text" size="32" value="" /><input class="ordering" name="vd_new[\'+k+\'][ordering]" type="text" size="1" value="" /><input name="vd_new[\'+k+\'][published]" type="hidden" value="0" /><input name="vd_new[\'+k+\'][published]" type="checkbox" checked="checked" value="1" /><input name="vd_new[\'+k+\'][status]" type="text" size="1" value="" /><a class="dv_sort" href="#"></a><a class="dv_delete" href="#"></a></li>\');
							return false;
						});
						
						$("#dv_box").sortable({
							placeholder: "ui-state-highlight",
							handle: "a.dv_sort",
							items: "li",
							opacity: 0.5,
							stop: dv_reorder
						});
						
						$("#dv_box").delegate("a.dv_delete","click", function(){
							$(this).parent().remove();
							return false;
						});
						
					})(jQuery)
				</script>';
		
        // $html = '<select class="inputbox" name="' . $name . '">';
        // $html .= '<option value="0"></option>';
		// foreach($values as &$v){
			// $selected = $value == $v->virtuemart_custom_id ? ' selected="selected"' : '';
			// $html .= '<option value="'.$v->virtuemart_custom_id.'"'.$selected.'>'.$v->custom_title.'</option>';
		// }
        // $html .="</select>";
		// $parent = $this->get('_parent');
		// $parent = $parent->getParamByName('data');
		// $parent->ordering = isset($parent->ordering) ? $parent->ordering : 0;
		// $html .= '<input type="hidden" value="'.$parent->ordering.'" name="ordering" />';
        return $html;
	}

}