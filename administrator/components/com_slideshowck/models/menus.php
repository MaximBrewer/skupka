<?php
/**
 * @name		Slideshow CK
 * @package		com_slideshowck
 * @copyright	Copyright (C) 2019. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - https://www.template-creator.com - https://www.joomlack.fr
 */

defined('_JEXEC') or die;

use \Slideshowck\CKModel;
use \Slideshowck\CKFof;

class SlideshowckModelMenus extends CKModel {

	public function getChildrenItems($menutype, $parentId) {
		JModelLegacy::addIncludePath(JPATH_SITE . '/administrator/components/com_menus/models', 'MenusModel');
		// Get an instance of the generic menus model
		$items = JModelLegacy::getInstance('Items', 'MenusModel', array('ignore_request' => true));
		if (! $parentId) $items->setState('filter.level', '1');
		$items->setState('filter.menutype', $menutype);
		$items->setState('filter.parent_id', $parentId);

		return $items->getItems();
	}

	public function getMenus() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
					->select($db->qn(array('menutype', 'title')))
					->from($db->qn('#__menu_types'));
//					->where($db->qn('menutype') . ' = ' . $db->q($menuType));

		$menus = $db->setQuery($query)->loadObjectList();
		return $menus;
	}
}
