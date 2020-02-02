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
$Id: vmshoppergroups.php 3 2017-09-20 14:30:08Z Abhshek Das $
----------------------------------------------------------------------------------------------------------*/
defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

if (!class_exists('VmConfig'))
{
	$config = JPath::clean(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php');
	if(file_exists($config)) require($config);
}

class JFormFieldVmshoppergroups extends JFormFieldList
{
	protected $type = 'Vmshoppergroups';
	
	protected static $shopper_groups = null;

	protected function getOptions()
	{
		if(!class_exists('VmConfig'))
		{
			JFactory::getApplication()->enqueueMessage('VirtueMart 3 Component not found in your site.', 'error');
			return array();
		}
		
		VmConfig::loadConfig();
		VmConfig::loadJLang('com_virtuemart', true);
		VmConfig::loadJLang('com_virtuemart_shoppers', true);
		
		if(self::$shopper_groups === null)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			
			$query->select('`virtuemart_shoppergroup_id` AS value, `shopper_group_name` AS text, `default`')
			      ->from('`#__virtuemart_shoppergroups`')
			      ->where('`published` = 1');
			
			$db->setQuery($query);
			self::$shopper_groups = $db->loadObjectList();
		}
		
		$ignore_defaults = !empty($this->element['ignore_defaults']) && $this->element['ignore_defaults'] == 'true' ? true : false;
		$options = array();
		
		if(!empty(self::$shopper_groups))
		{
			foreach(self::$shopper_groups as $group)
			{
				if($ignore_defaults && $group->default > 0)
				{
					continue;
				}
				
				$options[] = JHtml::_('select.option', (int) $group->value, JText::_($group->text));
			}
		}
		
		return array_merge(parent::getOptions(), $options);
	}
}