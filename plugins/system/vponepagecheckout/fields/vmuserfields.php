<?php
/*--------------------------------------------------------------------------------------------------------
# VP One Page Checkout - Joomla! System Plugin for VirtueMart 3
----------------------------------------------------------------------------------------------------------
# Copyright:     Copyright (C) 2012-2017 VirtuePlanet Services LLP. All Rights Reserved.
# License:       GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
# Author:        Abhishek Das
# Email:         info@virtueplanet.com
# Websites:      https://www.virtueplanet.com
----------------------------------------------------------------------------------------------------------
$Revision: 3 $
$LastChangedDate: 2017-09-20 20:00:08 +0530 (Wed, 20 Sep 2017) $
$Id: vmuserfields.php 3 2017-09-20 14:30:08Z Abhshek Das $
----------------------------------------------------------------------------------------------------------*/
defined('JPATH_PLATFORM') or die;
JFormHelper::loadFieldClass('list');

if (!class_exists('VmConfig'))
{
	$config = JPath::clean(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php');
	if(file_exists($config)) require($config);
}

class JFormFieldVMUserfields extends JFormFieldList
{
	protected $type = 'VMUserfields';
	
	protected static $userfields = array();

	protected function getOptions()
	{
		if(!class_exists('VmConfig'))
		{
			JFactory::getApplication()->enqueueMessage('VirtueMart 3 Component not found in your site.', 'error');
			return array();
		}
		
		VmConfig::loadConfig();
		
		if (!class_exists ('VirtueMartModelUserfields')) 
		{
			require(VMPATH_ADMIN . '/models/userfields.php');
		}
		
		$types = !empty($this->element['field_types']) ? (string) $this->element['field_types'] : array('BT');
		
		if(!empty($types) && is_string($types))
		{
			if(strpos($types, ',') !== false)
			{
				$types = array_map('trim', explode(',', $types));
			}
			else
			{
				$types = array($types);
			}
		}
		
		$sf_types = !empty($this->element['sf_types']) ? (string) $this->element['sf_types'] : array();
		
		if(!empty($sf_types) && is_string($sf_types))
		{
			if(strpos($sf_types, ',') !== false)
			{
				$sf_types = array_map('trim', explode(',', $sf_types));
			}
			else
			{
				$sf_types = array($sf_types);
			}
		}
		
		$skips = !empty($this->element['skips']) ? (string) $this->element['skips'] : array();
		
		if(!empty($skips) && is_string($skips))
		{
			if(strpos($skips, ',') !== false)
			{
				$skips = array_map('trim', explode(',', $skips));
			}
			else
			{
				$skips = array($skips);
			}
		}
		
		$userFieldsModel = VmModel::getModel('Userfields');
		$fields = array();
		$options = array();
		$added = array();
		
		foreach($types as $type)
		{
			if(!isset(self::$userfields[$type]))
			{
				self::$userfields[$type] = $userFieldsModel->getUserFieldsFor('cart', $type);
			}
			
			$fields = array_merge($fields, self::$userfields[$type]);
		}

		foreach($fields as $field)
		{
			if(!in_array($field->name, $skips) && $field->type != 'delimiter' && !in_array($field->name, $added) && (empty($sf_types) || in_array($field->type, $sf_types)))
			{
				$options[] = JHtml::_('select.option', (string) $field->name, JText::_($field->title));
				$added[] = $field->name;
			}
		}
		
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}