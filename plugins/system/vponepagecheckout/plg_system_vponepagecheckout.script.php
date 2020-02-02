<?php
/**
 *---------------------------------------------------------------------------------------
 * @package      VP One Page Checkout - Joomla! System Plugin
 * @subpackage   For VirtueMart 3+
 *---------------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2012-2017 VirtuePlanet Services LLP. All rights reserved.
 * @license      GNU General Public License version 2 or later; see LICENSE.txt
 * @authors      Abhishek Das
 * @email        info@virtueplanet.com
 * @link         https://www.virtueplanet.com
 *---------------------------------------------------------------------------------------
 * $Revision: 3 $
 * $LastChangedDate: 2017-09-20 20:00:08 +0530 (Wed, 20 Sep 2017) $
 * $Id: plg_system_vponepagecheckout.script.php 3 2017-09-20 14:30:08Z Abhshek Das $
 * --------------------------------------------------------------------------------------
*/

defined('_JEXEC') or die();

/**
 * VP One Page Checkout Plugin Script
 * 
 * @since   5.3
 */
class PlgSystemVponepagecheckoutInstallerScript
{
	/**
	 * Called on installation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function install(JAdapterInstance $adapter)
	{
		return true;
	}

	/**
	 * Called on update
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function update(JAdapterInstance $adapter)
	{
		// Update params from older installation
		$this->updatePluginParams();
		
		return true;
	}

	/**
	 * Called before any type of action
	 *
	 * @param     string              $route      Which action is happening (install|uninstall|discover_install)
	 * @param     jadapterinstance    $adapter    The object responsible for running this script
	 *
	 * @return    boolean                         True on success
	 */
	public function preflight($route, JAdapterInstance $adapter)
	{
		return true;
	}


	/**
	 * Called after any type of action
	 *
	 * @param     string              $route      Which action is happening (install|uninstall|discover_install)
	 * @param     jadapterinstance    $adapter    The object responsible for running this script
	 *
	 * @return    boolean                         True on success
	 */
	public function postflight($route, JAdapterInstance $adapter)
	{
		return true;
	}
	
	protected function updatePluginParams()
	{
		jimport('joomla.registry.registry');
		
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		// Get plugin params
		$query->select($db->quoteName('extension_id'))
		      ->select($db->quoteName('params'))
		      ->from($db->quoteName('#__extensions'))
		      ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
		      ->where($db->quoteName('element') . ' = ' . $db->quote('vponepagecheckout'))
		      ->where($db->quoteName('folder') . ' = ' . $db->quote('system'));
		
		$db->setQuery($query);
		$plugin = $db->loadObject();
		
		if(empty($plugin) || empty($plugin->extension_id) || empty($plugin->params))
		{
			return true;
		}
		
		$params = new JRegistry;
		$params->loadString($plugin->params);
		$updated = false;
		
		if($eu_vat_paying_groups = $params->get('eu_vat_paying_groups'))
		{
			$params->set('vat_exempted_groups', $eu_vat_paying_groups);
			
			$updated = true;
		}
		
		if($eu_vat_nonpaying_groups = $params->get('eu_vat_nonpaying_groups'))
		{
			$params->set('vat_paying_groups', $eu_vat_nonpaying_groups);
			
			$updated = true;
		}
		
		if(!$params->get('download_key'))
		{
			$params->set('download_key', '***');
			
			$updated = true;
		}
		
		if($updated)
		{
			$plugin->params = $params->toString();
			
			$result = $db->updateObject('#__extensions', $plugin, 'extension_id');
			
			return $result;
		}
		
		return true;
	}
}