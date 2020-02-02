<?php
defined('JPATH_BASE') or die;
/**
* Param: Virtuemart 2 customfield plugin
* Version: 3.0.6 (2015.11.23)
* Author: Dmitriy Usov
* Copyright: Copyright (C) 2012-2015 usovdm
* License GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* http://myext.eu
**/
if (!class_exists('VmConfig')){
    require(JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'config.php');
}
if(empty(VmConfig::$vmlang)){
	VmConfig::loadConfig();
}
if (!class_exists('ShopFunctions'))
    require(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'shopfunctions.php');
if (!class_exists('TableCategories'))
    require(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'tables' . DIRECTORY_SEPARATOR . 'categories.php');
jimport('joomla.form.formfield');


class JFormFieldCategorylist extends JFormField
{ 
	protected $type = 'categorylist';
  
     function getInput() {
	 
        $key = ($this->element['key_field'] ? $this->element['key_field'] : 'value');
        $val = ($this->element['value_field'] ? $this->element['value_field'] : $this->name);
		$attr  = $this->element['multiple'] ? ' multiple="'.$this->element['multiple'].'"' : '';
		$attr .= $this->element['size'] ? ' size="'.$this->element['size'].'"' : '';
		
        // JPlugin::loadLanguage('com_virtuemart', JPATH_ADMINISTRATOR);
		if(empty($this->value)) $this->value = array();
		ShopFunctions::$categoryTree = 0;
        // $categorylist = ShopFunctions::categoryListTree($this->value);
		$categorylist = ShopFunctions::categoryListTreeLoop($this->value, 0, 0, array(), 0, 1,VmConfig::$vmlang);
        $class = '';
        $html = '<select class="inputbox" name="' . $this->name . '"'.$attr.'>';
		$null_selected = in_array(0,$this->value) ? ' selected="selected"' : '';
        $html .= '<option value="0"'.$null_selected.'>- None Selected -</option>';
        $html .= $categorylist;
        $html .='</select>';
		// <a href="#" class="select_toggle">show select</a>';
        return $html;
    }
}