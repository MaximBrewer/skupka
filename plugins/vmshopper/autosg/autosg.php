<?php
defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;

/**
 * @subpackage  vmshopper.autosg
 *
 * @copyright	Copyright (C) EasyJoomla.org. All rights reserved.
 * @license		GNU General Public License version 2 or later
 * @author      Jan Linhart for EasyJoomla.org
 */

jimport('joomla.log.log');

if (!class_exists('plgVmShopperAutosg')) require(JPATH_VM_PLUGINS . DS . 'vmshopperplugin.php');

class plgVmShopperAutosg extends vmShopperPlugin
{

	function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		$lang = & JFactory::getLanguage();
		$lang->load('plg_vmshopper_autosg', JPATH_ADMINISTRATOR);

		JLog::addLogger(array('text_file' => 'plg_autosg.php'), JLog::ALL, 'plg_autosg');
	}

	public function plgVmOnUserStore($data)
	{
		return $data;
	}

	public function plgVmAfterUserStore($data)
	{
		$userField = $this->params->get('user_field', 0);
		$shopperGroup2Set = $this->params->get('shopper_group', 0);
		$shopperGroup2Remove = $this->params->get('shopper_group_remove', 0);
		$savedShopperGroups = $this->getShopperGroups($data['virtuemart_user_id']);

		JLog::add('Data: '.$data, JLog::INFO, 'plg_autosg');

		if(!isset($data[$userField]))
		{
			JLog::add('User field: '.$userField.'does not exist.', JLog::ERROR, 'plg_autosg');
			return $data;
		}

		if($userField && $shopperGroup2Set && $data[$userField] && !in_array($shopperGroup2Set, $savedShopperGroups))
		{
			JLog::add('Filed '.$userField.' = '. $data[$userField], JLog::INFO, 'plg_autosg');
			$savedShopperGroups[] = $shopperGroup2Set;
			$this->saveShopperGroup($data['virtuemart_user_id'], $shopperGroup2Set);
		}

	  	if($shopperGroup2Remove && !$data[$userField] && in_array($shopperGroup2Set, $savedShopperGroups))
	  	{
			if($this->deleteShopperGroup($data['virtuemart_user_id'], $shopperGroup2Set))
			{
				JLog::add('Data: '.$data, JLog::ERROR, 'plg_autosg');
			}
		}

		return $data;
	}

	public function plgVmOnUpdateOrderBEShopper($_orderID)
	{
		return $_orderID;
	}

	public function getShopperGroups($virtuemart_user_id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('virtuemart_shoppergroup_id');
		$query->from('#__virtuemart_vmuser_shoppergroups');
		$query->where('virtuemart_user_id = '.(int)$virtuemart_user_id);
		$db->setQuery($query);

		$result = $db->loadResultArray();

		return $result;
	}

	public function saveShopperGroup($virtuemart_user_id, $shopperGroup)
	{

		$db = JFactory::getDbo();

		if ($virtuemart_user_id && $shopperGroup)
		{
			$groupObject = new stdClass();
			$groupObject->virtuemart_user_id = $virtuemart_user_id;
			$groupObject->virtuemart_shoppergroup_id = $shopperGroup;

			if ($db->insertObject( '#__virtuemart_vmuser_shoppergroups', $groupObject, 'id' ))
			{
				JLog::add('Insert group SUCCESS. User: '.$groupObject->virtuemart_user_id.' Group: '. $groupObject->virtuemart_shoppergroup_id, JLog::INFO, 'plg_autosg');
				return true;
			}
			else
			{
				JLog::add('Insert group ERROR. User: '.$groupObject->virtuemart_user_id.' Group: '. $groupObject->virtuemart_shoppergroup_id, JLog::ERROR, 'plg_autosg');
				return false;
			}

			if ($msg = $db->getErrorMsg())
			{
				JLog::add($msg, JLog::ERROR, 'plg_autosg');
				return false;
			}
		}
		else
		{
			JLog::add('Insert group ERROR. Some ID missing. User: '.$virtuemart_user_id.' Group: '. $shopperGroup, JLog::ERROR, 'plg_autosg');
			return false;
		}
	}

	public function deleteShopperGroup($virtuemart_user_id, $shopperGroup)
	{
		if($virtuemart_user_id && $shopperGroup)
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->from('#__virtuemart_vmuser_shoppergroups');
			$query->delete();
			$query->where('virtuemart_user_id ='.(int)$virtuemart_user_id);
			$query->where('virtuemart_shoppergroup_id ='.(int)$shopperGroup);
			$db->setQuery($query);
			$result = $db->loadObjectList();

			if ($result)
			{
				JLog::add('Delete group SUCCESS. User: '.$groupObject->virtuemart_user_id.' Group: '. $groupObject->virtuemart_shoppergroup_id, JLog::INFO, 'plg_autosg');
				return true;
			}
			else
			{
				JLog::add('Delete group ERROR. User: '.$groupObject->virtuemart_user_id.' Group: '. $groupObject->virtuemart_shoppergroup_id, JLog::ERROR, 'plg_autosg');
				return false;
			}

			if($msg = $db->getErrorMsg())
			{
				JLog::add($msg, JLog::ERROR, 'plg_autosg');
				return false;
			}
		}
		else
		{
			JLog::add('Delete group ERROR. Some ID missing. User: '.$virtuemart_user_id.' Group: '. $shopperGroup, JLog::ERROR, 'plg_autosg');
			return false;
		}
	}

}
