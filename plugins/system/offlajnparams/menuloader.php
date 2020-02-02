<?php
/*-------------------------------------------------------------------------
# plg_offlajnparams - Offlajn Params
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2016 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class OfflajnMenuTypes {

	private static function getMenuHtml(&$menuItems) {
		$children = array();
		if ($menuItems) {
			foreach ($menuItems as &$item){
				$pt = $item->parent_id;
				$list = isset($children[$pt]) ? $children[$pt] : array();
				array_push($list, $item);
				$children[$pt] = $list;
			}
		}

		// initializing $parent as the root
		$parent = 0;
		$parent_stack = array();
		$loop = !empty( $children[0] );
		$html  = array();

		while ( $loop && ( ($option = each($children[$parent])) || $parent ) ) {
			if ( $option === false ) {
				$parent = array_pop( $parent_stack );
				// HTML for menu item containing childrens (close)
				$html[] = '</dl></dd>';

			} elseif ( !empty( $children[$option['value']->id] ) ) {
				// HTML for menu item containing childrens (open)
				$html[] = '<dt><i class="otl-toggle"></i><label><input type="checkbox" value="'. $option['value']->id .'">'. $option['value']->title .'</label></dt>';
				$html[] = '<dd><dl>';

				array_push( $parent_stack, $option['value']->parent_id );
				$parent = $option['value']->id;

			} else {
				// HTML for menu item with no children (aka "leaf")
				$html[] = '<dt><i class="otl-leaf"></i><label><input type="checkbox" value="'. $option['value']->id .'">'. $option['value']->title .'</label></dt>';
			}
		}

		return implode( '', $html );
	}

	public static function getJoomlaMenuItems() {
		// get Joomla menus
		$db = JFactory::getDBO();
		$db->setQuery("SELECT CONCAT('jm-', id) AS id, 'jroot' AS parent_id, title, menutype FROM `#__menu_types` ORDER BY menutype");
		$menuTypes = $db->loadObjectList('menutype');
		if ($db->getErrorNum()) return '';

		// get Joomla menu-items
		$db->setQuery("SELECT CONCAT('j-', id) AS id, CONCAT('j-', parent_id) AS parent_id, title, menutype
			FROM `#__menu` WHERE published = 1 ORDER BY menutype, lft, parent_id");
		$menuItems = $db->loadObjectList();
		if ($db->getErrorNum()) return '';

		// update parent id of level1 menuitems
		foreach ($menuItems as &$menuItem) {
			if ($menuItem->parent_id == 'j-1') {
				$menuItem->parent_id = $menuTypes[$menuItem->menutype]->id;
			}
		}

		// add menu types to menu items
		$rootMenuItem = array_shift($menuItems);
		$rootMenuItem->id = 'jroot';
		$rootMenuItem->title = 'Joomla Menus';
		$rootMenuItem->parent_id = 0;
		array_unshift($menuTypes, $rootMenuItem);
		$menuItems = array_merge($menuTypes, $menuItems);

		return self::getMenuHtml($menuItems);
	}

	public static function getJoomlaContentItems() {
		$db = JFactory::getDBO();
		$db->setQuery("SELECT CONCAT('jc-', id) AS id, title, CONCAT('jc-', parent_id) AS parent_id FROM `#__categories`
			WHERE published = 1 AND (extension = 'com_content' OR extension = 'system') ORDER BY lft");
		$menuItems = $db->loadObjectList();
		if ($db->getErrorNum()) return '';

		$menuItems[0] = new stdClass();
		$menuItems[0]->id = 'jc-1';
		$menuItems[0]->title = 'Joomla Contents';
		$menuItems[0]->parent_id = 0;

		return self::getMenuHtml($menuItems);
	}

	public static function getK2Items() {
		if (!file_exists(JPATH_ROOT.'/components/com_k2')) return '';

		$db = JFactory::getDBO();
		$db->setQuery("SELECT CONCAT('k2-', id) AS id, name AS title, CONCAT('k2-', parent) AS parent_id
			FROM `#__k2_categories` WHERE published = 1 ORDER BY parent, ordering");
		$menuItems = $db->loadObjectList();
		if ($db->getErrorNum()) return '';

		$rootMenuItem = new stdClass();
		$rootMenuItem->id = 'k2-0';
		$rootMenuItem->title = 'K2';
		$rootMenuItem->parent_id = 0;
		array_unshift($menuItems, $rootMenuItem);

		return self::getMenuHtml($menuItems);
	}

	public static function getZooItems() {
		if (!file_exists(JPATH_ROOT.'/components/com_zoo')) return '';

		$db = JFactory::getDBO();
		$db->setQuery("SELECT CONCAT('za-', id) AS id, 'zoo' AS parent_id, name AS title FROM `#__zoo_application` ORDER BY name");
		$apps = $db->loadObjectList('id');
		if ($db->getErrorNum()) return '';

		$db->setQuery("SELECT CONCAT('zc-', id) AS id, CONCAT('zc-', parent) AS parent_id, name AS title, CONCAT('za-', application_id) as app_id
			FROM `#__zoo_category` WHERE published = 1 ORDER BY parent, ordering");
		$menuItems = $db->loadObjectList();
		if ($db->getErrorNum()) return '';

		// update parent id of level1 menuitems
		foreach ($menuItems as &$menuItem) {
			if ($menuItem->parent_id == 'zc-0') {
				$menuItem->parent_id = $apps[$menuItem->app_id]->id;
			}
		}

		$rootMenuItem = new stdClass();
		$rootMenuItem->id = 'zoo';
		$rootMenuItem->title = 'Zoo';
		$rootMenuItem->parent_id = 0;
		array_unshift($apps, $rootMenuItem);
		$menuItems = array_merge($apps, $menuItems);

		return self::getMenuHtml($menuItems);
	}

	public static function getHikashopItems() {
		if (!file_exists(JPATH_ROOT.'/components/com_hikashop')) return '';

		$db = JFactory::getDBO();
		$db->setQuery("SELECT CONCAT('hs-', category_id) AS id, category_name AS title, CONCAT('hs-', category_parent_id) AS parent_id
			FROM `#__hikashop_category` WHERE category_published = 1 AND category_type = 'product' ORDER BY category_ordering");
		$menuItems = $db->loadObjectList();
		if ($db->getErrorNum()) return '';

		$rootMenuItem = new stdClass();
		$rootMenuItem->id = 'hs-1';
		$rootMenuItem->title = 'HikaShop';
		$rootMenuItem->parent_id = 0;
		array_unshift($menuItems, $rootMenuItem);

		return self::getMenuHtml($menuItems);
	}

	public static function getJoomshoppingItems() {
		if (!file_exists(JPATH_ROOT.'/components/com_jshopping')) return '';

		$lang = JFactory::getLanguage()->getTag();
		$db = JFactory::getDBO();
		$db->setQuery("SELECT CONCAT('js-', category_id) AS id, `name_$lang` AS title, CONCAT('js-', category_parent_id) AS parent_id
			FROM `#__jshopping_categories` WHERE category_publish = 1 ORDER BY ordering");
		$menuItems = $db->loadObjectList();
		if ($db->getErrorNum()) return '';

		$rootMenuItem = new stdClass();
		$rootMenuItem->id = 'js-0';
		$rootMenuItem->title = 'JoomShopping';
		$rootMenuItem->parent_id = 0;
		array_unshift($menuItems, $rootMenuItem);

		return self::getMenuHtml($menuItems);
	}

	public static function getMijoshopItems() {
		if (!file_exists(JPATH_ROOT.'/components/com_mijoshop/mijoshop/mijoshop.php')) return '';

		require_once(JPATH_ROOT.'/components/com_mijoshop/mijoshop/mijoshop.php');
		$lang = '';
		$config = MijoShop::get('opencart')->get('config');
		if (is_object($config)) {
			$lang = 'AND cd.language_id = '.$config->get('config_language_id');
		}

		$db = JFactory::getDBO();
		$db->setQuery("SELECT CONCAT('ms-', c.category_id) AS id, cd.name AS title, CONCAT('ms-', c.parent_id) AS parent_id
			FROM `#__mijoshop_category` AS c LEFT JOIN `#__mijoshop_category_description` AS cd ON c.category_id = cd.category_child_id
			WHERE c.status = 1 $lang ORDER BY c.sort_order");
		$menuItems = $db->loadObjectList();
		if ($db->getErrorNum()) return '';

		$rootMenuItem = new stdClass();
		$rootMenuItem->id = 'ms-0';
		$rootMenuItem->title = 'MijoShop';
		$rootMenuItem->parent_id = 0;
		array_unshift($menuItems, $rootMenuItem);

		return self::getMenuHtml($menuItems);
	}

	public static function getRedshopItems() {
		if (!file_exists(JPATH_ROOT.'/components/com_redshop')) return '';

		$db = JFactory::getDBO();
		$db->setQuery("SELECT CONCAT('rs-', c.category_id) AS id, c.category_name AS title, CONCAT('rs-', cx.category_parent_id) AS parent_id
			FROM `#__redshop_category` AS c LEFT JOIN `#__redshop_category_xref` AS cx ON c.category_id = cx.category_child_id
			WHERE c.published = 1 ORDER BY c.ordering");
		$menuItems = $db->loadObjectList();
		if ($db->getErrorNum()) return '';

		$rootMenuItem = new stdClass();
		$rootMenuItem->id = 'rs-0';
		$rootMenuItem->title = 'RedShop';
		$rootMenuItem->parent_id = 0;
		array_unshift($menuItems, $rootMenuItem);

		return self::getMenuHtml($menuItems);
	}

	public static function getVirtuemartItems() {
		if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/config.php')) return '';

		require_once(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/config.php');
		VmConfig::loadConfig();
		$lang = VMLANG;

		$db = JFactory::getDBO();
		$db->setQuery("SELECT CONCAT('vm-', cc.category_child_id) AS id, cl.category_name AS title, CONCAT('vm-', cc.category_parent_id) AS parent_id
			FROM `#__virtuemart_categories` AS c
			LEFT JOIN `#__virtuemart_categories_$lang` AS cl ON c.virtuemart_category_id = cl.virtuemart_category_id
			LEFT JOIN `#__virtuemart_category_categories` AS cc ON c.virtuemart_category_id = cc.category_child_id
			WHERE c.published = 1 ORDER BY c.ordering");
		$menuItems = $db->loadObjectList();
		if ($db->getErrorNum()) return '';

		$rootMenuItem = new stdClass();
		$rootMenuItem->id = 'vm-0';
		$rootMenuItem->title = 'VirtueMart';
		$rootMenuItem->parent_id = 0;
		array_unshift($menuItems, $rootMenuItem);

		return self::getMenuHtml($menuItems);
	}

}

foreach (get_class_methods('OfflajnMenuTypes') as $method) {
	echo call_user_func(array('OfflajnMenuTypes', $method));
}

exit;