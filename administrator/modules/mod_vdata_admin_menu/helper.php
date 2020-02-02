<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access


defined('_JEXEC') or die;
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

abstract class ModVdataMenuHelper {


	public static function getVdataComponent($authCheck = true)
	{
		$lang	= JFactory::getLanguage();
		$user		= JFactory::getUser();
        $version = new JVersion;
		$joomla = $version->getShortVersion();
		$jversion = substr($joomla,0,3);
		$db = JFactory::getDBO();

		$q = 'SELECT m.id, m.title, m.alias, m.link, m.parent_id, m.img, e.element FROM `#__menu` as m
				LEFT JOIN #__extensions AS e ON m.component_id = e.extension_id
		         WHERE m.client_id = 1 AND e.enabled = 1 AND m.id > 1 AND e.element = \'com_vdata\' AND m.menutype="main"
		         AND (m.parent_id=1 OR m.parent_id =
			                        (SELECT m.id FROM `#__menu` as m
									LEFT JOIN #__extensions AS e ON m.component_id = e.extension_id
			                        WHERE m.parent_id=1 AND m.client_id = 1 AND e.enabled = 1 AND m.id > 1 AND e.element = \'com_vdata\' AND m.menutype="main"))
		         ORDER BY m.lft';
		$db->setQuery($q);
        
		$vmComponentItems = $db->loadObjectList();
		$result = new stdClass();
		if ($vmComponentItems) {
		foreach ($vmComponentItems as &$vmComponentItem) {
				$vmComponentItem->link = htmlspecialchars(trim($vmComponentItem->link),ENT_COMPAT,'UTF-8',false);//JRequest::getSpecialChars(trim($vmComponentItem->link));
				if ($vmComponentItem->parent_id == 1) {
					if ($authCheck == false || ($authCheck && $user->authorise('core.manage', $vmComponentItem->element))) {
						$result = $vmComponentItem;
						if (!isset($result->submenu)) {
							$result->submenu = array();
						}

						if (empty($vmComponentItem->link)) {
							$vmComponentItem->link = 'index.php?option=' . $vmComponentItem->element;
						}

						$vmComponentItem->text = $lang->hasKey($vmComponentItem->title) ? JText::_($vmComponentItem->title) : $vmComponentItem->alias;
					}
				} else {
					// Sub-menu level.
					if (isset($result)) {
						// Add the submenu link if it is defined.
						if (isset($result->submenu) && !empty($vmComponentItem->link)) {
							$vmComponentItem->text = $lang->hasKey($vmComponentItem->title) ? JText::_($vmComponentItem->title) : $vmComponentItem->alias;

							$class = preg_replace('#\.[^.]*$#', '', basename($vmComponentItem->img));
							$class = preg_replace('#\.\.[^A-Za-z0-9\.\_\- ]#', '', $class);
							if($jversion<3){
								$vmComponentItem->class="icon-16-".$class;
							} else {
								$vmComponentItem->class='';
							}
							$result->submenu[] = & $vmComponentItem;
						}
					}
				}
			}
			$props = get_object_vars($result);
			if(!empty($props)){
				return $result;
		  }
		}
	   return false;
	}
	
}
