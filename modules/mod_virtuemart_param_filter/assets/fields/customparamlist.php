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
class JFormFieldCustomparamlist extends JFormField {

    var $type = 'customparamlist';

    function getInput(){
		$name = $this->name;
		$value = $this->value;
		
	    $db = JFactory::getDBO();
		$q = 'SELECT `virtuemart_custom_id`, `custom_title` FROM `#__virtuemart_customs` WHERE `custom_element` = "param"';
		$virtuemart_custom_id = JRequest::getVar('virtuemart_custom_id',0);
		$virtuemart_custom_id = is_array($virtuemart_custom_id) ? end($virtuemart_custom_id) : $virtuemart_custom_id;
		if($virtuemart_custom_id){
			$q .= ' AND `virtuemart_custom_id` != '.$virtuemart_custom_id;
		}
        $db->setQuery($q);
		$list = $db->loadObjectList();
        $class = '';
        $html = '<select class="inputbox" name="' . $name . '">';
        $html .= '<option value="0">нет</option>';
		foreach($list as &$v){
			$selected = $value == $v->virtuemart_custom_id ? ' selected="selected"' : '';
			$html .= '<option value="'.$v->virtuemart_custom_id.'"'.$selected.'>'.$v->custom_title.'</option>';
		}
        $html .="</select>";
		// echo '<pre>'.print_r($this,1).'</pre>';
		// $parent = $this->get('_parent');
		// $parent = $parent->getParamByName('data');
		// $parent->ordering = isset($parent->ordering) ? $parent->ordering : 0;
		// $html .= '<input type="hidden" value="'.$parent->ordering.'" name="ordering" />';
		// echo '<pre>'.print_r($html,1).'</pre>';
		// die();
        return $html;
	}

}