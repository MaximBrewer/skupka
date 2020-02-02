<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.form.formfield');
class JFormFieldFulltext extends JFormField
{
  protected $type = 'fulltext';
  function getInput()
	{
	$db = JFactory::getDBO(); 
	$enabled = false; 
	$status = $this->checkIndex('#__virtuemart_product_customfields', 'customfield_value');
	
	if (!empty($status))
	{
		$enabled = '  '; 
		
	}
	
	if ($status === 0)
	{
		$enabled = ' disabled="disabled" readonly="readonly" '; 
	}
	
    $html = '<select name="'.$this->name.'" '.$enabled.' >'; 
	$html .= '<option value="">Disabled</option>'; 
	$html .= '<option value="1"'; 
	if (!empty($status))  $html .= ' selected="selected" '; 
	$html .= '>Enabled</option>'; 
	
	$html .= '</select>'; 
	
	$isEnabled = $this->value;
	if (!empty($isEnabled))
	{
		if (!$this->checkIndex('#__virtuemart_product_customfields', 'customfield_value'))
		{
		  $ok = $this->addIndex('#__virtuemart_product_customfields', 'customfield_value');
		}
		
		if (!class_exists('VmConfig'))
		 require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
		 VmConfig::loadConfig(); 
		 
		 $langs = VmConfig::get('active_languages', array('en-gb')); 
		 if (!empty($langs))
		 foreach ($langs as $l)
		 {
			 $l = strtolower($l); 
			 $l = str_replace('-', '_', $l); 
			 if (!$this->checkIndex('#__virtuemart_products_'.$l, 'product_desc'))
			 {
				$this->addIndex('#__virtuemart_products_'.$l, 'product_desc');
			 }
			if (!$this->checkIndex('#__virtuemart_products_'.$l, 'product_s_desc'))
			{
				$this->addIndex('#__virtuemart_products_'.$l, 'product_s_desc');
			}
		 }
		 
		 if (!empty($ok))
		 $enabled = ' checked="checked" '; 
		
	}
	else
	{
		if ($keyname = $this->checkIndex('#__virtuemart_product_customfields', 'customfield_value'))
		{
		  $this->dropIndex('#__virtuemart_product_customfields', $keyname);
		}
		
		if (!class_exists('VmConfig'))
		 require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
		 VmConfig::loadConfig(); 
		 
		 $langs = VmConfig::get('active_languages', array('en-gb')); 
		 if (!empty($langs))
		 foreach ($langs as $l)
		 {
			 $l = strtolower($l); 
			 $l = str_replace('-', '_', $l); 
			 if ($keyname = $this->checkIndex('#__virtuemart_products_'.$l, 'product_desc'))
			 {
				$this->dropIndex('#__virtuemart_products_'.$l, $keyname);
			 }
			if ($keyname = $this->checkIndex('#__virtuemart_products_'.$l, 'product_s_desc'))
			{
				$this->dropIndex('#__virtuemart_products_'.$l, $keyname);
			}
		 }
	}
	
	return $html; 
	
	}
	private function checkIndex($table, $col)
	{
		$db = JFactory::getDBO(); 
		$q = 'show KEYS from `'.$table.'`'; 
	try
	{
	$db->setQuery($q); 
	$res = $db->loadAssocList(); 
	foreach ($res as $row)
	{
		
		if ($row['Column_name'] !== $col) continue; 
		$it = strtoupper($row['Index_type']); 
		if ($it === 'FULLTEXT')
		{
		
			return $row['Key_name']; 
		}
	}
	return false; 
	
	}
	catch (Exception $e)
	{
		
		return 0; 
	}
	return false; 
	}
	private function addIndex($table, $col)
	{
		 $q = "ALTER TABLE `".$table."` ADD FULLTEXT `".$col."_ft` (`".$col."`)";
		 $db = JFactory::getDBO(); 
		 try { 
		 $db->setQuery($q); 
		 $db->query(); 
		 
		 $e = $db->getErrorMsg(); 
		 }
		 catch (Exception $e) {
		  return false; 
		 }
		 if (!empty($e)) { return false; }
		 
		 return true; 
	}
	
	private function dropIndex($table, $keyname)
	{
		$q = 'ALTER TABLE `'.$table.'` DROP INDEX '.$keyname; 
		 $db = JFactory::getDBO(); 
		 try {
		  $db->setQuery($q); 
		  $db->query(); 
		 }
		 catch (Exception $e) {  
		  
		  return; 
		 }
		
	}
	
}

