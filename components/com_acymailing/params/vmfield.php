<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.10.7
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><?php
if(!include_once(rtrim(JPATH_ADMINISTRATOR,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_acymailing'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php')){
	echo 'This module can not work without the AcyMailing Component';
}

if(!ACYMAILING_J16){
	class JElementVmfield extends JElement
	{
		function fetchElement($name, $value, &$node, $control_name)
		{
			$fields = acymailing_getColumns('#__vm_user_info');

			$dropdown = array();
			foreach($fields as $oneField => $fieldType){
				$dropdown[] = acymailing_selectOption($oneField, $oneField);
			}
			return acymailing_select($dropdown, $control_name.'['.$name.']' , 'size="1"', 'value', 'text', $value);
		}
	}
}else{
	class JFormFieldVmfield extends JFormField
	{
		var $type = 'vmfield';

		function getInput() {
			$fields = acymailing_getColumns('#__virtuemart_userinfos');

			$dropdown = array();
			foreach($fields as $oneField => $fieldType){
				$dropdown[] = acymailing_selectOption($oneField, $oneField);
			}
			return acymailing_select($dropdown, $this->name , 'size="1"', 'value', 'text', $this->value);
		}
	}
}
