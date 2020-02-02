<?php
defined ('_JEXEC') or die();
jimport('joomla.form.formfield');
class JFormFieldShoppergroups extends JFormField {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'shoppergroups';
	protected $type = 'shoppergroups';
	function getInput () {
		/*
		if (!class_exists('VmConfig'))
		 require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
		 if (!defined('VMPATH_SITE'))
		 VmConfig::loadConfig(); 
		*/
		$name = $this->name; 
		$value = $this->value; 
		
		$control_name = 'params'; 
		
		
		JFactory::getLanguage()->load('com_virtuemart'); 
		JFactory::getLanguage()->load('com_virtuemart_shoppers', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'); 
		JFactory::getLanguage()->load('com_virtuemart_shoppers', JPATH_SITE); 
		//
		$html = '<select name="'.$name.'" multiple="multiple">'; 
		$html .= '<option value="0">Do not filter per currency</option>'; 
		$db = JFactory::getDBO(); 
		$q = 'select * from `#__virtuemart_shoppergroups` where 1'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		
		foreach ($res as $row)
		{
			
			$sgid = $row['virtuemart_shoppergroup_id'] = (int)$row['virtuemart_shoppergroup_id']; 
			if ((!empty($this->value)) && (in_array($sgid, $this->value))) $selected = ' selected="selected" '; 
			else $selected = ''; 
			$html .= '<option '.$selected.' value="'.$sgid.'">'.htmlentities(JText::_($row['shopper_group_name'])).'</option>'; 
		}
		$html .= '</select>'; 
		
		return $html; 
	}
	
	
	
	
}