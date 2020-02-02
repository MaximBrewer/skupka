<?php

/**
 *
 * @package	VirtueMart
 * @subpackage   Models Fields
 * @author Valerie Isaksen
 * @edit		Dmitriy Usov
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: $
 */
defined('JPATH_BASE') or die;
if (!class_exists('VmConfig')){
    require(JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'config.php');
}
if(empty(VmConfig::$vmlang)){
	VmConfig::loadConfig();
}
if (!class_exists('ShopFunctions'))
    require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'shopfunctions.php');
if (!class_exists('TableCategories'))
    require(JPATH_VM_ADMINISTRATOR . DS . 'tables' . DS . 'categories.php');
jimport('joomla.form.formfield');

/**
 * Supports a modal product picker.
 *
 *
 */
class JFormFieldCategory extends JFormField
{ 
	protected $type = 'category';

	/**
	 * Method to get the field input markup.
	 *
         * @author      Valerie Cartan Isaksen
		 * @edit		Dmitriy Usov
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
  
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
        $html .="</select>";
        return $html;
    }
}