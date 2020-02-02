<?php

/**
 * @copyright	Copyright (C) 2011 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Maximenu CK
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die;
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class modMaximenuckHelper {

	private static $_itemcss;

	private static $_modulecss;

	/**
	 * Get a list of the menu items.
	 *
	 * @param	JRegistry	$params	The module options.
	 *
	 * @return	array
	 */
	static function getItems(&$params) {
		$app = JFactory::getApplication();
		$menu = $app->getMenu();

		// If no active menu, use default
		$active = ($menu->getActive()) ? $menu->getActive() : $menu->getDefault();
		$base = self::getBase($params);

//		$user = JFactory::getUser();
//		$levels = $user->getAuthorisedViewLevels();
//		asort($levels);
//		$key = 'menu_items' . $params . implode(',', $levels) . '.' . $active->id;
//		$cache = JFactory::getCache('mod_maximenuck', '');
//		if (!($items = $cache->get($key)) || (int) $params->get('cache') == '0') {
			// Initialise variables.
			$list = array();
			$modules = array();
			$db = JFactory::getDbo();
			$document = JFactory::getDocument();

			// load the libraries
			jimport('joomla.application.module.helper');

			$path = $base->tree;
			$start = (int) $params->get('startLevel');
			$end = (int) $params->get('endLevel');
			$items = $menu->getItems('menutype', $params->get('menutype'));

			// if no items in the menu then exit
			if (!$items)
				return false;

			$lastitem = 0;
			// list all modules
			$modulesList = modmaximenuckHelper::CreateModulesList();

			foreach ($items as $i => $item) {
				$isdependant = $params->get('dependantitems', false) ? ($start > 1 && !in_array($item->tree[$start - 2], $path)) : false;
				if (($start && $start > $item->level) || ($end && $item->level > $end) || $isdependant
				) {
					unset($items[$i]);
					continue;
				}

				$item->deeper = false;
				$item->shallower = false;
				$item->level_diff = 0;

				if (isset($items[$lastitem])) {
					$items[$lastitem]->deeper = ($item->level > $items[$lastitem]->level);
					$items[$lastitem]->shallower = ($item->level < $items[$lastitem]->level);
					$items[$lastitem]->level_diff = ($items[$lastitem]->level - $item->level);
				}

				// Test if this is the last item
				$item->is_end = !isset($items[$i + 1]);

				$item->parent = (boolean) $menu->getItems('parent_id', (int) $item->id, true);
				$item->active = false;
				$item->current = false;
				$item->flink = $item->link;
				$item->classe = '';

				switch ($item->type) {
					case 'separator':
					case 'heading':
						$item->classe .= ' headingck';
						// No further action needed.
						continue;

					case 'url':
						if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false)) {
							// If this is an internal Joomla link, ensure the Itemid is set.
							$item->flink = $item->link . '&Itemid=' . $item->id;
						}
						$item->flink = JFilterOutput::ampReplace(htmlspecialchars($item->flink));
						break;

					case 'alias':
						// If this is an alias use the item id stored in the parameters to make the link.
						$item->flink = 'index.php?Itemid=' . $item->params->get('aliasoptions');
						break;

					default:
						// get the router according to the joomla version
						// no more used, see new method below
						// if (version_compare(JVERSION, '3.0.0') < 0) {
							// $router = JSite::getRouter();
						// } else {
							// $router = $app::getRouter();
						// }
						
						// Get the router.
						$appsite = JApplication::getInstance('site');
						$router = $appsite->getRouter();

						if ($router->getMode() == JROUTER_MODE_SEF)
						{
							$item->flink = 'index.php?Itemid=' . $item->id;

							if (isset($item->query['format']) && $app->getCfg('sef_suffix'))
							{
								$item->flink .= '&format=' . $item->query['format'];
							}
						}
						else
						{
							$item->flink .= '&Itemid=' . $item->id;
						}
						break;
				}

				if (strcasecmp(substr($item->flink, 0, 4), 'http') && (strpos($item->flink, 'index.php?') !== false)) {
					$item->flink = JRoute::_($item->flink, true, $item->params->get('secure'));
				} else {
					$item->flink = JRoute::_($item->flink);
				}

				$item->anchor_css = htmlspecialchars($item->params->get('menu-anchor_css', ''), ENT_COMPAT, 'UTF-8', false);
				$item->anchor_title = htmlspecialchars($item->params->get('menu-anchor_title', ''), ENT_COMPAT, 'UTF-8', false);
				$item->menu_image = $item->params->get('menu_image', '') ? htmlspecialchars($item->params->get('menu_image', ''), ENT_COMPAT, 'UTF-8', false) : '';



				//  ---------------- begin the maximenu work on items --------------------

				$item->ftitle = htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8', false);
				$item->ftitle = JFilterOutput::ampReplace($item->ftitle);
				$parentItem = modMaximenuckHelper::getParentItem($item->parent_id, $items);

				// ---- add some classes ----
				// add itemid class
				$item->classe .= ' item' . $item->id;
				// add current class
				if (isset($active) && $active->id == $item->id) {
					$item->classe .= ' current';
					$item->current = true;
				}
				// add active class
				if (is_array($path) &&
						( ($item->type == 'alias' && in_array($item->params->get('aliasoptions'), $path)) || in_array($item->id, $path))) {
					$item->classe .= ' active';
					$item->active = true;
				}
				// add the parent class
				if ($item->deeper) {
					$item->classe .= ' deeper';
				}

				if ($item->parent && ($end == 0 || (int)$item->level < (int)$end)) {
					if ($params->get('layout', 'default') != '_:flatlist')
						$item->classe .= ' parent';
				}

				// add last and first class
				$item->classe .= $item->is_end ? ' last' : '';
				$item->classe .= !isset($items[$i - 1]) ? ' first' : '';

				if (isset($items[$lastitem])) {
					$items[$lastitem]->classe .= $items[$lastitem]->shallower ? ' last' : '';
					$item->classe .= $items[$lastitem]->deeper ? ' first' : '';
					if (isset($items[$i + 1]) AND $item->level - $items[$i + 1]->level > 1 AND $parentItem) {
						$parentItem->classe .= ' last';
					}
				}

				// manage the class to show the item on desktop and mobile
				if ($item->params->get('maximenu_disablemobile') == '1') {
					$item->classe .= ' nomobileck';
				}
				
				if ($item->params->get('maximenu_disabledesktop') == '1') {
					$item->classe .= ' nodesktopck';
				}


				// ---- manage params ----
				// -- manage column --
				$item->colwidth = $item->params->get('maximenu_colwidth', '180');
				$item->createnewrow = $item->params->get('maximenu_createnewrow', 0) || stristr($item->ftitle, '[newrow]');
				// check if there is a width for the subcontainer
				preg_match('/\[subwidth=([0-9]+)\]/', $item->ftitle, $subwidth);
				$subwidth = isset($subwidth[1]) ? $subwidth[1] : '';
				if ($subwidth)
					$item->ftitle = preg_replace('/\[subwidth=[0-9]+\]/', '', $item->ftitle);
				$item->submenucontainerwidth = $item->params->get('maximenu_submenucontainerwidth', '') ? $item->params->get('maximenu_submenucontainerwidth', '') : $subwidth;

				if ($item->params->get('maximenu_createcolumn', 0)) {
					$item->colonne = true;
					// add the value to give the total parent container width
					if (isset($parentItem->submenuswidth)) {
						if (! stristr($item->colwidth, '%') ) $parentItem->submenuswidth = strval($parentItem->submenuswidth) + strval($item->colwidth);
					} else if (isset($parentItem) && $parentItem) {
						if (! stristr($item->colwidth, '%') ) $parentItem->submenuswidth = strval($item->colwidth);
					}
					// if specified by user with the plugin, then give the width to the parent container
					if (isset($items[$lastitem]) && $items[$lastitem]->deeper) {
						$items[$lastitem]->nextcolumnwidth = $item->colwidth;
					}
					$item->columnwidth = $item->colwidth;
				} elseif (preg_match('/\[col=([0-9]+)\]/', $item->ftitle, $resultat)) {
					$item->ftitle = str_replace('[newrow]', '', $item->ftitle);
					$item->ftitle = preg_replace('/\[col=[0-9]+\]/', '', $item->ftitle);
					$item->colonne = true;
					if (isset($parentItem->submenuswidth)) {
						if (! stristr($item->colwidth, '%') ) $parentItem->submenuswidth = strval($parentItem->submenuswidth) + strval($resultat[1]);
					} else if (isset($parentItem)) {
						if (! stristr($item->colwidth, '%') ) $parentItem->submenuswidth = strval($resultat[1]);
					}
					if (isset($items[$lastitem]) && $items[$lastitem]->deeper) {
						$items[$lastitem]->nextcolumnwidth = $resultat[1];
					}
					$item->columnwidth = $resultat[1];
				}
				if (isset($parentItem->submenucontainerwidth) AND $parentItem->submenucontainerwidth) {
					$parentItem->submenuswidth = $parentItem->submenucontainerwidth;
				}

				// -- manage module --
				$moduleid = $item->params->get('maximenu_module', '');
				$style = $item->params->get('maximenu_forcemoduletitle', 0) ? 'xhtml' : '';
				if ($item->params->get('maximenu_insertmodule', 0)) {
					if (!isset($modules[$moduleid])) {
						$modules[$moduleid] = modmaximenuckHelper::GenModuleById($moduleid, $params, $modulesList, $style, $item->level);
					}
					// for maximenu imbricated, use another css class
					$special_subclass = ($modulesList[$moduleid]->module == 'mod_maximenuck') ? '2' : '';
					$item->content = '<div class="maximenuck_mod' . $special_subclass . '">' . $modules[$moduleid] . '<div class="clr"></div></div>';
				} elseif (preg_match('/\[modid=([0-9]+)\]/', $item->ftitle, $resultat)) {
					// for maximenu imbricated, use another css class
					$special_subclass = ($modulesList[$resultat[1]]->module == 'mod_maximenuck') ? '2' : '';
					$item->ftitle = preg_replace('/\[modid=[0-9]+\]/', '', $item->ftitle);
					$item->content = '<div class="maximenuck_mod' . $special_subclass . '">' . modmaximenuckHelper::GenModuleById($resultat[1], $params, $modulesList, $style, $item->level) . '<div class="clr"></div></div>';
				}

				// -- manage rel attribute --
				$item->rel = '';
				if ($rel = $item->params->get('maximenu_relattr', '')) {
					$item->rel = ' rel="' . $rel . '"';
				} elseif (preg_match('/\[rel=([a-z]+)\]/i', $item->ftitle, $resultat)) {
					$item->ftitle = preg_replace('/\[rel=[a-z]+\]/i', '', $item->ftitle);
					$item->rel = ' rel="' . $resultat[1] . '"';
				}

				// -- manage link description --
				$item->description = $item->params->get('maximenu_desc', '');
				if ($item->description) {
					$item->desc = $item->description;
				} else {
					$resultat = explode("||", $item->ftitle);
					if (isset($resultat[1])) {
						$item->desc = $resultat[1];
					} else {
						$item->desc = '';
					}
					$item->ftitle = $resultat[0];
				}

				// add the anchor tag
				$item->flink .= $item->params->get('maximenu_anchor', '') ? '#' . $item->params->get('maximenu_anchor', '') : '';

				// add styles to the page for customization
				$menuID = $params->get('menuid', 'maximenuck');

				// get plugin parameters that are used directly in the layout
				$item->leftmargin = $item->params->get('maximenu_leftmargin', '');
				$item->topmargin = $item->params->get('maximenu_topmargin', '');
				$item->liclass = $item->params->get('maximenu_liclass', '');
				$item->colbgcolor = $item->params->get('maximenu_colbgcolor', '');
				$item->tagcoltitle = $item->params->get('maximenu_tagcoltitle', 'none');
				$item->submenucontainerheight = $item->params->get('maximenu_submenucontainerheight', '');

				// get mobile plugin parameters that are used directly in the layout
				$item->mobile_data = '';
				$item->mobile_data .= $item->params->get('maximenumobile_icon', '') ? ' data-mobileicon="' . $item->params->get('maximenumobile_icon', '') . '"' : '';
				$item->mobile_data .= $item->params->get('maximenumobile_textreplacement', '') ? ' data-mobiletext="' . $item->params->get('maximenumobile_textreplacement', '') . '"' : '';

				// set the item styles if the plugin is enabled
				if (JPluginHelper::isEnabled('system', 'maximenuckparams')) {
					if ($params->get('doCompile') || $params->get('loadcompiledcss', '0') == '0') {
						$itemcss = self::injectItemCss($item, $menuID, $params);
						if ($itemcss) {
							if ($params->get('loadcompiledcss', '0') == '0') {
								$document->addStyleDeclaration($itemcss);
							} else {
								self::$_itemcss .= $itemcss;
							}
						}
					}
				}

				$lastitem = $i;
			} // end of boucle for each items
			// give the correct deep infos for the last item
			if (isset($items[$lastitem])) {
				$items[$lastitem]->deeper = (($start ? $start : 1) > $items[$lastitem]->level);
				$items[$lastitem]->shallower = (($start ? $start : 1) < $items[$lastitem]->level);
				$items[$lastitem]->level_diff = ($items[$lastitem]->level - ($start ? $start : 1));
			}

//			$cache->store($items, $key);
//		}
		return $items;
	}

	/**
	 * Get a the parent item object
	 *
	 * @param Object $id The current item
	 * @param Array $items The list of all items
	 *
	 * @return object
	 */
	static function getParentItem($id, $items) {
		foreach ($items as $item) {
			if ($item->id == $id)
				return $item;
		}
		return false;
	}

	/**
	 * Render the module
	 *
	 * @param Int $moduleid The module ID to load
	 * @param JRegistry $params
	 * @param Array $modulesList The list of all module objects published
	 *
	 * @return string with HTML
	 */
	static function GenModuleById($moduleid, $params, $modulesList, $style, $level = '1') {
		$attribs['style'] = $style;
		// get the title of the module to load
		$modtitle = $modulesList[$moduleid]->title;
		$modname = $modulesList[$moduleid]->module;
		//$modname = preg_replace('/mod_/', '', $modname);
		// load the module
		if (JModuleHelper::isEnabled($modname)) {
			$module = JModuleHelper::getModule($modname, $modtitle);
			// set the module param to know the calling level
			$paramstmp = new JRegistry;
			$paramstmp->loadString($module->params);
			$paramstmp->set('calledfromlevel', $level);
			$module->params = $paramstmp->toString();

			return JModuleHelper::renderModule($module, $attribs);
		}
		return 'Module ID=' . $moduleid . ' not found !';
	}

	/**
	 * Create the list of all modules published as Object
	 *
	 * @return Array of Objects
	 */
	static function CreateModulesList() {
		$db = JFactory::getDBO();
		$query = "
			SELECT *
			FROM #__modules
			WHERE published=1
			ORDER BY id
			;";
		$db->setQuery($query);
		$modulesList = $db->loadObjectList('id');
		return $modulesList;
	}

	/**
	 * Create the css properties
	 * @param JRegistry $params
	 * @param string $prefix the xml field prefix
	 *
	 * @return Array
	 */
	static function createCss($menuID, $params, $prefix = 'menu', $important = false, $itemid = '', $use_svggradient = true) {
		$css = Array();
		$important = ($important == true ) ? ' !important' : '';
		$csspaddingtop = ($params->get($prefix . 'paddingtop') != '') ? 'padding-top: ' . self::testUnit($params->get($prefix . 'paddingtop', '0')) . $important . ';' : '';
		$csspaddingright = ($params->get($prefix . 'paddingright') != '') ? 'padding-right: ' . self::testUnit($params->get($prefix . 'paddingright', '0')) . $important . ';' : '';
		$csspaddingbottom = ($params->get($prefix . 'paddingbottom') != '') ? 'padding-bottom: ' . self::testUnit($params->get($prefix . 'paddingbottom', '0')) . $important . ';' : '';
		$csspaddingleft = ($params->get($prefix . 'paddingleft') != '') ? 'padding-left: ' . self::testUnit($params->get($prefix . 'paddingleft', '0')) . $important . ';' : '';
		$css['padding'] = $csspaddingtop . $csspaddingright . $csspaddingbottom . $csspaddingleft;
		$cssmargintop = ($params->get($prefix . 'margintop') != '') ? 'margin-top: ' . self::testUnit($params->get($prefix . 'margintop', '0')) . $important . ';' : '';
		$cssmarginright = ($params->get($prefix . 'marginright') != '') ? 'margin-right: ' . self::testUnit($params->get($prefix . 'marginright', '0')) . $important . ';' : '';
		$cssmarginbottom = ($params->get($prefix . 'marginbottom') != '') ? 'margin-bottom: ' . self::testUnit($params->get($prefix . 'marginbottom', '0')) . $important . ';' : '';
		$cssmarginleft = ($params->get($prefix . 'marginleft') != '') ? 'margin-left: ' . self::testUnit($params->get($prefix . 'marginleft', '0')) . $important . ';' : '';
		$css['margin'] = $cssmargintop . $cssmarginright . $cssmarginbottom . $cssmarginleft;
		$bgcolor1 = ($params->get($prefix . 'bgcolor1') && $params->get($prefix . 'bgopacity') !== null && $params->get($prefix . 'bgopacity') !== '') ? self::hex2RGB($params->get($prefix . 'bgcolor1'), $params->get($prefix . 'bgopacity')) : $params->get($prefix . 'bgcolor1');
		$css['background'] = ($params->get($prefix . 'bgcolor1')) ? 'background: ' . $bgcolor1 . $important . ';' : '';
		$css['background'] .= ($params->get($prefix . 'bgcolor1')) ? 'background-color: ' . $bgcolor1 . $important . ';' : '';
		$css['background'] .= ( $params->get($prefix . 'bgimage')) ? 'background-image: url("' . JURI::ROOT() . $params->get($prefix . 'bgimage') . '")' . $important . ';' : '';
		$css['background'] .= ( $params->get($prefix . 'bgimage')) ? 'background-repeat: ' . $params->get($prefix . 'bgimagerepeat') . $important . ';' : '';
		$css['background'] .= ( $params->get($prefix . 'bgimage')) ? 'background-position: ' . ($params->get($prefix . 'bgpositionx')) . ' ' . ($params->get($prefix . 'bgpositiony')) . $important . ';' : '';

		$bgcolor2 = ($params->get($prefix . 'bgcolor2') && $params->get($prefix . 'bgopacity') && $params->get($prefix . 'bgopacity') !== '') ? self::hex2RGB($params->get($prefix . 'bgcolor2'), $params->get($prefix . 'bgopacity')) : $params->get($prefix . 'bgcolor2');
		// manage gradient svg for ie9
		$svggradient = '';
		if ($use_svggradient) {
			$svggradientfile = '';
			if ($css['background'] AND $params->get($prefix . 'bgcolor2')) {
				$svggradientfile = self::createSvgGradient($menuID, $prefix . $itemid, $params->get($prefix . 'bgcolor1', ''), $params->get($prefix . 'bgcolor2', ''));
			}
			$svggradient = $svggradientfile ? "background-image: url(\"" . $svggradientfile . "\")" . $important . ";" : "";
		}
		$css['gradient'] = ($css['background'] AND $params->get($prefix . 'bgcolor2')) ?
				$svggradient
				. "background: -moz-linear-gradient(top,  " . $bgcolor1 . " 0%, " . $bgcolor2 . " 100%)" . $important . ";"
				. "background: -webkit-gradient(linear, left top, left bottom, color-stop(0%," . $bgcolor1 . "), color-stop(100%," . $bgcolor2 . "))" . $important . "; "
				. "background: -webkit-linear-gradient(top,  " . $bgcolor1 . " 0%," . $bgcolor2 . " 100%)" . $important . ";"
				. "background: -o-linear-gradient(top,  " . $bgcolor1 . " 0%," . $bgcolor2 . " 100%)" . $important . ";"
				. "background: -ms-linear-gradient(top,  " . $bgcolor1 . " 0%," . $bgcolor2 . " 100%)" . $important . ";"
				. "background: linear-gradient(top,  " . $bgcolor1 . " 0%," . $bgcolor2 . " 100%)" . $important . "; " : '';
//                . "filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='" . $params->get($prefix . 'bgcolor1', '#f0f0f0') . "', endColorstr='" . $params->get($prefix . 'bgcolor2', '#e3e3e3') . "',GradientType=0 );" : '';
		$css['borderradius'] = ($params->get($prefix . 'roundedcornerstl', '') != '' || $params->get($prefix . 'roundedcornerstr', '') != '' || $params->get($prefix . 'roundedcornersbr', '') != '' || $params->get($prefix . 'roundedcornersbl', '') != '') ?
				'-moz-border-radius: ' . self::testUnit($params->get($prefix . 'roundedcornerstl', '0')) . ' ' . self::testUnit($params->get($prefix . 'roundedcornerstr', '0')) . ' ' . self::testUnit($params->get($prefix . 'roundedcornersbr', '0')) . ' ' . self::testUnit($params->get($prefix . 'roundedcornersbl', '0')) . $important . ';'
				. '-webkit-border-radius: ' . self::testUnit($params->get($prefix . 'roundedcornerstl', '0')) . ' ' . self::testUnit($params->get($prefix . 'roundedcornerstr', '0')) . ' ' . self::testUnit($params->get($prefix . 'roundedcornersbr', '0')) . ' ' . self::testUnit($params->get($prefix . 'roundedcornersbl', '0')) . $important . ';'
				. 'border-radius: ' . self::testUnit($params->get($prefix . 'roundedcornerstl', '0')) . ' ' . self::testUnit($params->get($prefix . 'roundedcornerstr', '0')) . ' ' . self::testUnit($params->get($prefix . 'roundedcornersbr', '0')) . ' ' . self::testUnit($params->get($prefix . 'roundedcornersbl', '0')) . $important . ';' : '';
		$shadowinset = $params->get($prefix . 'shadowinset', 0) ? 'inset ' : '';
		$css['shadow'] = ($params->get($prefix . 'shadowcolor') AND $params->get($prefix . 'shadowblur') != '') ?
				'-moz-box-shadow: ' . $shadowinset . self::testUnit($params->get($prefix . 'shadowoffsetx', '0')) . ' ' . self::testUnit($params->get($prefix . 'shadowoffsety', '0')) . ' ' . self::testUnit($params->get($prefix . 'shadowblur', '')) . ' ' . self::testUnit($params->get($prefix . 'shadowspread', '0')) . ' ' . $params->get($prefix . 'shadowcolor', '') . $important . ';'
				. '-webkit-box-shadow: ' . $shadowinset . self::testUnit($params->get($prefix . 'shadowoffsetx', '0')) . ' ' . self::testUnit($params->get($prefix . 'shadowoffsety', '0')) . ' ' . self::testUnit($params->get($prefix . 'shadowblur', '')) . ' ' . self::testUnit($params->get($prefix . 'shadowspread', '0')) . ' ' . $params->get($prefix . 'shadowcolor', '') . $important . ';'
				. 'box-shadow: ' . $shadowinset . self::testUnit($params->get($prefix . 'shadowoffsetx', '0')) . ' ' . self::testUnit($params->get($prefix . 'shadowoffsety', '0')) . ' ' . self::testUnit($params->get($prefix . 'shadowblur', '')) . ' ' . self::testUnit($params->get($prefix . 'shadowspread', '0')) . ' ' . $params->get($prefix . 'shadowcolor', '') . $important . ';' :
				(($params->get($prefix . 'useshadow') && $params->get($prefix . 'shadowblur') == '0') ? '-moz-box-shadow: none' . $important . ';'
						. '-webkit-box-shadow: none' . $important . ';'
						. 'box-shadow: none' . $important . ';' : '');
		$borderstyle = $params->get($prefix . 'borderstyle', 'solid') ? $params->get($prefix . 'borderstyle', 'solid') : 'solid';
		$bordertopstyle = $params->get($prefix . 'bordertopstyle', 'solid') ? $params->get($prefix . 'bordertopstyle', 'solid') : $borderstyle;
		$borderrightstyle = $params->get($prefix . 'borderrightstyle', 'solid') ? $params->get($prefix . 'borderrightstyle', 'solid') : $borderstyle;
		$borderbottomstyle = $params->get($prefix . 'borderbottomstyle', 'solid') ? $params->get($prefix . 'borderbottomstyle', 'solid') : $borderstyle;
		$borderleftstyle = $params->get($prefix . 'borderleftstyle', 'solid') ? $params->get($prefix . 'borderleftstyle', 'solid') : $borderstyle;
		$css['border'] = (($params->get($prefix . 'bordertopwidth') == '0') ? 'border-top: none' . $important . ';' : (($params->get($prefix . 'bordertopwidth') != '' AND $params->get($prefix . 'bordertopcolor')) ? 'border-top: ' . $params->get($prefix . 'bordertopcolor', '') . ' ' . self::testUnit($params->get($prefix . 'bordertopwidth', '')) . ' ' . $bordertopstyle . ' ' . $important . ';' : '') )
				. (($params->get($prefix . 'borderrightwidth') == '0') ? 'border-right: none' . $important . ';' : (($params->get($prefix . 'borderrightwidth') != '' AND $params->get($prefix . 'borderrightcolor')) ? 'border-right: ' . $params->get($prefix . 'borderrightcolor', '') . ' ' . self::testUnit($params->get($prefix . 'borderrightwidth', '')) . ' ' . $borderrightstyle . ' ' . $important . ';' : '') )
				. (($params->get($prefix . 'borderbottomwidth') == '0') ? 'border-bottom: none' . $important . ';' : (($params->get($prefix . 'borderbottomwidth') != '' AND $params->get($prefix . 'borderbottomcolor')) ? 'border-bottom: ' . $params->get($prefix . 'borderbottomcolor', '') . ' ' . self::testUnit($params->get($prefix . 'borderbottomwidth', '')) . ' ' . $borderbottomstyle . ' ' . $important . ';' : '') )
				. (($params->get($prefix . 'borderleftwidth') == '0') ? 'border-left: none' . $important . ';' : (($params->get($prefix . 'borderleftwidth') != '' AND $params->get($prefix . 'borderleftcolor')) ? 'border-left: ' . $params->get($prefix . 'borderleftcolor', '') . ' ' . self::testUnit($params->get($prefix . 'borderleftwidth', '')) . ' ' . $borderleftstyle . ' ' . $important . ';' : '') );
		$css['fontsize'] = ($params->get($prefix . 'fontsize') != '') ?
				'font-size: ' . self::testUnit($params->get($prefix . 'fontsize')) . $important . ';' : '';
		$css['fontcolor'] = ($params->get($prefix . 'fontcolor') != '') ?
				'color: ' . $params->get($prefix . 'fontcolor') . $important . ';' : '';
		$css['fontweight'] = ($params->get($prefix . 'fontweight')  == 'bold') ?
				'font-weight: ' . $params->get($prefix . 'fontweight') . $important . ';' : '';
		/* $css['fontcolorhover'] = ($params->get($prefix . 'usefont') AND $params->get($prefix . 'fontcolorhover')) ?
		  'color: ' . $params->get($prefix . 'fontcolorhover') . ';' : ''; */
		$css['descfontsize'] = ($params->get($prefix . 'descfontsize') != '') ?
				'font-size: ' . self::testUnit($params->get($prefix . 'descfontsize')) . $important . ';' : '';
		$css['descfontcolor'] = ($params->get($prefix . 'descfontcolor') != '') ?
				'color: ' . $params->get($prefix . 'descfontcolor') . $important . ';' : '';
		$textshadowoffsetx = ($params->get($prefix . 'textshadowoffsetx', '0') == '') ? '0px' : self::testUnit($params->get($prefix . 'textshadowoffsetx', '0'));
		$textshadowoffsety = ($params->get($prefix . 'textshadowoffsety', '0') == '') ? '0px' : self::testUnit($params->get($prefix . 'textshadowoffsety', '0'));
		$css['textshadow'] = ($params->get($prefix . 'textshadowcolor') AND $params->get($prefix . 'textshadowblur')) ?
				'text-shadow: ' . $textshadowoffsetx . ' ' . $textshadowoffsety . ' ' . self::testUnit($params->get($prefix . 'textshadowblur', '')) . ' ' . $params->get($prefix . 'textshadowcolor', '') . $important . ';' :
				(($params->get($prefix . 'textshadowblur') == '0') ? 'text-shadow: none' . $important . ';' : '');
		$css['text-align'] = $params->get($prefix . 'textalign') ? 'text-align: ' . $params->get($prefix . 'textalign') . $important . ';' : ''; '';
		$css['text-transform'] = ($params->get($prefix . 'texttransform') && $params->get($prefix . 'texttransform') != 'default') ? 'text-transform: ' . $params->get($prefix . 'texttransform') . $important . ';' : ''; '';
		$css['text-indent'] = ($params->get($prefix . 'textindent') && $params->get($prefix . 'textindent') != 'default') ? 'text-indent: ' . self::testUnit($params->get($prefix . 'textindent')) . $important . ';' : ''; '';
		$css['line-height'] = ($params->get($prefix . 'lineheight') && $params->get($prefix . 'lineheight') != 'default') ? 'line-height: ' . self::testUnit($params->get($prefix . 'lineheight')) . $important . ';' : ''; '';
		$css['height'] = ($params->get($prefix . 'height') && $params->get($prefix . 'height') != '') ? 'height: ' . self::testUnit($params->get($prefix . 'height')) . $important . ';' : ''; '';
		$css['width'] = ($params->get($prefix . 'width') && $params->get($prefix . 'width') != '') ? 'width: ' . self::testUnit($params->get($prefix . 'width')) . $important . ';' : ''; '';

		self::retrocompatibility_beforev8($css, $params, $prefix);
		return $css;
	}
	
	static function retrocompatibility_beforev8(& $css, $params, $prefix) {
		if ( $params->exists($prefix . 'usemargin') && $params->get($prefix . 'usemargin') != '1' ) {
			$css['margin'] = '';
			$css['padding'] = '';
		}
		if ( $params->exists($prefix . 'usebackground') && $params->get($prefix . 'usebackground') != '1' ) {
			$css['background'] = '';
			$css['gradient'] = '';
		}
		if ( $params->exists($prefix . 'usegradient') && $params->get($prefix . 'usegradient') != '1' ) {
			$css['gradient'] = '';
		}
		if ( $params->exists($prefix . 'useroundedcorners') && $params->get($prefix . 'useroundedcorners') != '1' ) {
			$css['borderradius'] = '';
		}
		if ( $params->exists($prefix . 'useshadow') && $params->get($prefix . 'useshadow') != '1' ) {
			$css['shadow'] = '';
		}
		if ( $params->exists($prefix . 'useborders') && $params->get($prefix . 'useborders') != '1' ) {
			$css['border'] = '';
		}
		if ( $params->exists($prefix . 'usefont') && $params->get($prefix . 'usefont') != '1' ) {
			$css['fontsize'] = '';
			$css['fontcolor'] = '';
			$css['fontweight'] = '';
			$css['descfontsize'] = '';
			$css['descfontcolor'] = '';
		}
		if ( $params->exists($prefix . 'usetextshadow') && $params->get($prefix . 'usetextshadow') == '1' ) {
			$css['textshadow'] = '';
		}

	}

	/**
	 * Create the svg gradient for IE9
	 * @param string $prefix
	 *
	 * @return void
	 */
	static function createSvgGradient($menuID, $prefix, $color1, $color2) {
		// create the file svg for IE9 and Opera gradient compatibility
		$path = JPATH_ROOT . '/modules/mod_maximenuck/assets/svggradient/';
		$svgie9cssdest = $path . $menuID . $prefix . '-gradient.svg';

		$svgie9csstext = '<?xml version="1.0" ?>
            <svg xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" version="1.0" width="100%"
            height="100%"
            xmlns:xlink="http://www.w3.org/1999/xlink">

            <defs>
            <linearGradient id="' . $menuID . $prefix . '"
            x1="0%" y1="0%"
            x2="0%" y2="100%"
            spreadMethod="pad">
            <stop offset="0%"   stop-color="' . $color1 . '" stop-opacity="1"/>
            <stop offset="100%" stop-color="' . $color2 . '" stop-opacity="1"/>
            </linearGradient>
            </defs>

            <rect width="100%" height="100%"
            style="fill:url(#' . $menuID . $prefix . ');" />
            </svg>
            ';

		if (!JFile::write($svgie9cssdest, $svgie9csstext))
			return '';

		return JURI::root(true) . '/modules/mod_maximenuck/assets/svggradient/' . $menuID . $prefix . '-gradient.svg';
	}

	/**
	 * Create the css properties
	 *
	 * @return Array
	 */
	static function injectItemCss($item, $menuID, $params) {
		$start = (int) $params->get('startLevel');
		$itemlevel = ($start > 1) ? $item->level - $start + 1 : $item->level;
		$itemcss = '';
		$cssitemnormal = self::createCss($menuID, $item->params, 'itemnormalstyles', true, $item->id);
		$cssitemhover = self::createCss($menuID, $item->params, 'itemhoverstyles', true, $item->id);
		$cssitemactive = self::createCss($menuID, $item->params, 'itemactivestyles', true, $item->id);
		$csssubmenu = self::createCss($menuID, $item->params, 'submenustyles', true, $item->id);
		//$cssheading = self::createCss($menuID, $item->params, 'headingstyles');

		$separator = ($item->type == 'separator' && !$item->params->get('maximenu_insertmodule', 0)) ? '.headingck > span.separator' : '';
		$document = JFactory::getDocument();

		// for parent arrow normal state
		$itemnormalstylesparentarrowcolor = $item->params->get('itemnormalstylesparentarrowcolor', '') ? $item->params->get('itemnormalstylesparentarrowcolor', '') : $item->params->get('itemnormalstylesfontcolor', '');
		if ($item->params->get('itemnormalstylesparentarrowtype', '') == 'image') {
			$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.parent.item" . $item->id . " > a:after, div#" . $menuID . " ul.maximenuck li.maximenuck.parent.item" . $item->id . " > span.separator:after { "
					// . ( $params->get('orientation', 'horizontal') === 'vertical'  ? "border-left-color: " . $itemnormalstylesparentarrowcolor . ";" : "border-top-color: " . $itemnormalstylesparentarrowcolor . ";" )
					. "border: none;"
					. "display:block;"
					. "position:absolute;"
					. (($item->params->get('itemnormalstylesparentitemimage', '') != '') ? "background-image: url(" . JUri::root(true) . "/" . $item->params->get('itemnormalstylesparentitemimage', '') . ") !important;" : "")
					. (($item->params->get('itemnormalstylesparentitemimagepositionx', '') != '' && $item->params->get('itemnormalstylesparentitemimagepositiony', '') != '') ? "background-position: " . $item->params->get('itemnormalstylesparentitemimagepositionx', '') . " " . $item->params->get('itemnormalstylesparentitemimagepositiony', '') . " !important;" : "")
					. (($item->params->get('itemnormalstylesparentitemimagerepeat', '') != '') ? "background-repeat: " . $item->params->get('itemnormalstylesparentitemimagerepeat', '') . " !important;" : "")
					. (($item->params->get('itemnormalstylesparentarrowwidth', '') != '') ? "width: " . self::testUnit($item->params->get('itemnormalstylesparentarrowwidth', '')) . " !important;" : "")
					. (($item->params->get('itemnormalstylesparentarrowheight', '') != '') ? "height: " . self::testUnit($item->params->get('itemnormalstylesparentarrowheight', '')) . " !important;" : "")
					. (($item->params->get('itemnormalstylesparentarrowmargintop', '') != '') ? "margin-top: " . self::testUnit($item->params->get('itemnormalstylesparentarrowmargintop', '')) . " !important;" : "")
					. (($item->params->get('itemnormalstylesparentarrowmarginright', '') != '') ? "margin-right: " . self::testUnit($item->params->get('itemnormalstylesparentarrowmarginright', '')) . " !important;" : "")
					. (($item->params->get('itemnormalstylesparentarrowmarginbottom', '') != '') ? "margin-bottom: " . self::testUnit($item->params->get('itemnormalstylesparentarrowmarginbottom', '')) . " !important;" : "")
					. (($item->params->get('itemnormalstylesparentarrowmarginleft', '') != '') ? "margin-left: " . self::testUnit($item->params->get('itemnormalstylesparentarrowmarginleft', '')) . " !important;" : "")
					. (($item->params->get('itemnormalstylesparentarrowpositiontop', '') != '') ? "top: " . self::testUnit($item->params->get('itemnormalstylesparentarrowpositiontop', '')) . " !important;" : "")
					. (($item->params->get('itemnormalstylesparentarrowpositionright', '') != '') ? "right: " . self::testUnit($item->params->get('itemnormalstylesparentarrowpositionright', '')) . " !important;" : "")
					. (($item->params->get('itemnormalstylesparentarrowpositionbottom', '') != '') ? "bottom: " . self::testUnit($item->params->get('itemnormalstylesparentarrowpositionbottom', '')) . " !important;" : "")
					. (($item->params->get('itemnormalstylesparentarrowpositionleft', '') != '') ? "left: " . self::testUnit($item->params->get('itemnormalstylesparentarrowpositionleft', '')) . " !important;" : "")
					. "} ";
		} else if ($item->params->get('itemnormalstylesparentarrowtype', '') == 'triangle' || $itemnormalstylesparentarrowcolor) {
			if ($itemnormalstylesparentarrowcolor) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.parent.item" . $item->id . " > a:after, div#" . $menuID . " ul.maximenuck li.maximenuck.parent.item" . $item->id . " > span.separator:after { " 
					. ( $params->get('orientation', 'horizontal') === 'vertical'  ? "border-left-color: " . $itemnormalstylesparentarrowcolor . " !important;" : ( $itemlevel == 1 ? "border-top-color: " . $itemnormalstylesparentarrowcolor . " !important;" : "border-left-color: " . $itemnormalstylesparentarrowcolor . " !important;") )
					. "color: " . $itemnormalstylesparentarrowcolor . " !important;"
					. "display:block;"
					. "position:absolute;"
					. (($item->params->get('itemnormalstylesparentarrowmargintop', '') != '') ? "margin-top: " . self::testUnit($item->params->get('itemnormalstylesparentarrowmargintop', '')) . " !important;" : "")
					. (($item->params->get('itemnormalstylesparentarrowmarginright', '') != '') ? "margin-right: " . self::testUnit($item->params->get('itemnormalstylesparentarrowmarginright', '')) . " !important;" : "")
					. (($item->params->get('itemnormalstylesparentarrowmarginbottom', '') != '') ? "margin-bottom: " . self::testUnit($item->params->get('itemnormalstylesparentarrowmarginbottom', '')) . " !important;" : "")
					. (($item->params->get('itemnormalstylesparentarrowmarginleft', '') != '') ? "margin-left: " . self::testUnit($item->params->get('itemnormalstylesparentarrowmarginleft', '')) . " !important;" : "")
					. (($item->params->get('itemnormalstylesparentarrowpositiontop', '') != '') ? "top: " . self::testUnit($item->params->get('itemnormalstylesparentarrowpositiontop', '')) . " !important;" : "")
					. (($item->params->get('itemnormalstylesparentarrowpositionright', '') != '') ? "right: " . self::testUnit($item->params->get('itemnormalstylesparentarrowpositionright', '')) . " !important;" : "")
					. (($item->params->get('itemnormalstylesparentarrowpositionbottom', '') != '') ? "bottom: " . self::testUnit($item->params->get('itemnormalstylesparentarrowpositionbottom', '')) . " !important;" : "")
					. (($item->params->get('itemnormalstylesparentarrowpositionleft', '') != '') ? "left: " . self::testUnit($item->params->get('itemnormalstylesparentarrowpositionleft', '')) . " !important;" : "")
					
					. "} ";
			}
		}
		// for parent arrow hover state
		$itemhoverstylesparentarrowcolor = $item->params->get('itemhoverstylesparentarrowcolor', '') ? $item->params->get('itemhoverstylesparentarrowcolor', '') : $item->params->get('itemhoverstylesfontcolor', '');
		if ($item->params->get('itemhoverstylesparentarrowtype', '') == 'image') {
			$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.parent.item" . $item->id . ":hover > a:after, div#" . $menuID . " ul.maximenuck li.maximenuck.parent.item" . $item->id . ":hover > span.separator:after { "
					// . ( $params->get('orientation', 'horizontal') === 'vertical'  ? "border-left-color: " . $itemhoverstylesparentarrowcolor . ";" : "border-top-color: " . $itemhoverstylesparentarrowcolor . ";" )
					. "border: none;"
					. "display:block;"
					. "position:absolute;"
					. (($item->params->get('itemhoverstylesparentitemimage', '') != '') ? "background-image: url(" . JUri::root(true) . "/" . $item->params->get('itemhoverstylesparentitemimage', '') . ") !important;" : "")
					. (($item->params->get('itemhoverstylesparentitemimagepositionx', '') != '' && $item->params->get('itemhoverstylesparentitemimagepositiony', '') != '') ? "background-position: " . $item->params->get('itemhoverstylesparentitemimagepositionx', '') . " " . $item->params->get('itemhoverstylesparentitemimagepositiony', '') . " !important;" : "")
					. (($item->params->get('itemhoverstylesparentitemimagerepeat', '') != '') ? "background-repeat: " . $item->params->get('itemhoverstylesparentitemimagerepeat', '') . " !important;" : "")
					. (($item->params->get('itemhoverstylesparentarrowwidth', '') != '') ? "width: " . self::testUnit($item->params->get('itemhoverstylesparentarrowwidth', '')) . " !important;" : "")
					. (($item->params->get('itemhoverstylesparentarrowheight', '') != '') ? "height: " . self::testUnit($item->params->get('itemhoverstylesparentarrowheight', '')) . " !important;" : "")
					. (($item->params->get('itemhoverstylesparentarrowmargintop', '') != '') ? "margin-top: " . self::testUnit($item->params->get('itemhoverstylesparentarrowmargintop', '')) . " !important;" : "")
					. (($item->params->get('itemhoverstylesparentarrowmarginright', '') != '') ? "margin-right: " . self::testUnit($item->params->get('itemhoverstylesparentarrowmarginright', '')) . " !important;" : "")
					. (($item->params->get('itemhoverstylesparentarrowmarginbottom', '') != '') ? "margin-bottom: " . self::testUnit($item->params->get('itemhoverstylesparentarrowmarginbottom', '')) . " !important;" : "")
					. (($item->params->get('itemhoverstylesparentarrowmarginleft', '') != '') ? "margin-left: " . self::testUnit($item->params->get('itemhoverstylesparentarrowmarginleft', '')) . " !important;" : "")
					. (($item->params->get('itemhoverstylesparentarrowpositiontop', '') != '') ? "top: " . self::testUnit($item->params->get('itemhoverstylesparentarrowpositiontop', '')) . " !important;" : "")
					. (($item->params->get('itemhoverstylesparentarrowpositionright', '') != '') ? "right: " . self::testUnit($item->params->get('itemhoverstylesparentarrowpositionright', '')) . " !important;" : "")
					. (($item->params->get('itemhoverstylesparentarrowpositionbottom', '') != '') ? "bottom: " . self::testUnit($item->params->get('itemhoverstylesparentarrowpositionbottom', '')) . " !important;" : "")
					. (($item->params->get('itemhoverstylesparentarrowpositionleft', '') != '') ? "left: " . self::testUnit($item->params->get('itemhoverstylesparentarrowpositionleft', '')) . " !important;" : "")
					. "} ";
		} else if ($item->params->get('itemhoverstylesparentarrowtype', '') == 'triangle' || $itemhoverstylesparentarrowcolor) {
			if ($itemhoverstylesparentarrowcolor) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.parent.item" . $item->id . ":hover > a:after, div#" . $menuID . " ul.maximenuck li.maximenuck.parent.item" . $item->id . ":hover > span.separator:after { " 
					. ( $params->get('orientation', 'horizontal') === 'vertical'  ? "border-left-color: " . $itemhoverstylesparentarrowcolor . " !important;" : ( $itemlevel == 1 ? "border-top-color: " . $itemhoverstylesparentarrowcolor . " !important;" : "border-left-color: " . $itemhoverstylesparentarrowcolor . " !important;") )
					. "color: " . $itemhoverstylesparentarrowcolor . " !important;"
					. "display:block;"
					. "position:absolute;"
					. (($item->params->get('itemhoverstylesparentarrowmargintop', '') != '') ? "margin-top: " . self::testUnit($item->params->get('itemhoverstylesparentarrowmargintop', '')) . " !important;" : "")
					. (($item->params->get('itemhoverstylesparentarrowmarginright', '') != '') ? "margin-right: " . self::testUnit($item->params->get('itemhoverstylesparentarrowmarginright', '')) . " !important;" : "")
					. (($item->params->get('itemhoverstylesparentarrowmarginbottom', '') != '') ? "margin-bottom: " . self::testUnit($item->params->get('itemhoverstylesparentarrowmarginbottom', '')) . " !important;" : "")
					. (($item->params->get('itemhoverstylesparentarrowmarginleft', '') != '') ? "margin-left: " . self::testUnit($item->params->get('itemhoverstylesparentarrowmarginleft', '')) . " !important;" : "")
					. (($item->params->get('itemhoverstylesparentarrowpositiontop', '') != '') ? "top: " . self::testUnit($item->params->get('itemhoverstylesparentarrowpositiontop', '')) . " !important;" : "")
					. (($item->params->get('itemhoverstylesparentarrowpositionright', '') != '') ? "right: " . self::testUnit($item->params->get('itemhoverstylesparentarrowpositionright', '')) . " !important;" : "")
					. (($item->params->get('itemhoverstylesparentarrowpositionbottom', '') != '') ? "bottom: " . self::testUnit($item->params->get('itemhoverstylesparentarrowpositionbottom', '')) . " !important;" : "")
					. (($item->params->get('itemhoverstylesparentarrowpositionleft', '') != '') ? "left: " . self::testUnit($item->params->get('itemhoverstylesparentarrowpositionleft', '')) . " !important;" : "")
					
					. "} ";
			}
		}
		// for parent arrow active state
		$itemactivestylesparentarrowcolor = $item->params->get('itemactivestylesparentarrowcolor', '') ? $item->params->get('itemactivestylesparentarrowcolor', '') : $item->params->get('itemactivestylesfontcolor', '');
		if ($item->params->get('itemactivestylesparentarrowtype', '') == 'image') {
			$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.parent.item" . $item->id . ".active > a:after, div#" . $menuID . " ul.maximenuck li.maximenuck.parent.item" . $item->id . ".active > span.separator:after { "
					// . ( $params->get('orientation', 'horizontal') === 'vertical'  ? "border-left-color: " . $itemactivestylesparentarrowcolor . ";" : "border-top-color: " . $itemactivestylesparentarrowcolor . ";" )
					. "border: none;"
					. "display:block;"
					. "position:absolute;"
					. (($item->params->get('itemactivestylesparentitemimage', '') != '') ? "background-image: url(" . JUri::root(true) . "/" . $item->params->get('itemactivestylesparentitemimage', '') . ") !important;" : "")
					. (($item->params->get('itemactivestylesparentitemimagepositionx', '') != '' && $item->params->get('itemactivestylesparentitemimagepositiony', '') != '') ? "background-position: " . $item->params->get('itemactivestylesparentitemimagepositionx', '') . " " . $item->params->get('itemactivestylesparentitemimagepositiony', '') . " !important;" : "")
					. (($item->params->get('itemactivestylesparentitemimagerepeat', '') != '') ? "background-repeat: " . $item->params->get('itemactivestylesparentitemimagerepeat', '') . " !important;" : "")
					. (($item->params->get('itemactivestylesparentarrowwidth', '') != '') ? "width: " . self::testUnit($item->params->get('itemactivestylesparentarrowwidth', '')) . " !important;" : "")
					. (($item->params->get('itemactivestylesparentarrowheight', '') != '') ? "height: " . self::testUnit($item->params->get('itemactivestylesparentarrowheight', '')) . " !important;" : "")
					. (($item->params->get('itemactivestylesparentarrowmargintop', '') != '') ? "margin-top: " . self::testUnit($item->params->get('itemactivestylesparentarrowmargintop', '')) . " !important;" : "")
					. (($item->params->get('itemactivestylesparentarrowmarginright', '') != '') ? "margin-right: " . self::testUnit($item->params->get('itemactivestylesparentarrowmarginright', '')) . " !important;" : "")
					. (($item->params->get('itemactivestylesparentarrowmarginbottom', '') != '') ? "margin-bottom: " . self::testUnit($item->params->get('itemactivestylesparentarrowmarginbottom', '')) . " !important;" : "")
					. (($item->params->get('itemactivestylesparentarrowmarginleft', '') != '') ? "margin-left: " . self::testUnit($item->params->get('itemactivestylesparentarrowmarginleft', '')) . " !important;" : "")
					. (($item->params->get('itemactivestylesparentarrowpositiontop', '') != '') ? "top: " . self::testUnit($item->params->get('itemactivestylesparentarrowpositiontop', '')) . " !important;" : "")
					. (($item->params->get('itemactivestylesparentarrowpositionright', '') != '') ? "right: " . self::testUnit($item->params->get('itemactivestylesparentarrowpositionright', '')) . " !important;" : "")
					. (($item->params->get('itemactivestylesparentarrowpositionbottom', '') != '') ? "bottom: " . self::testUnit($item->params->get('itemactivestylesparentarrowpositionbottom', '')) . " !important;" : "")
					. (($item->params->get('itemactivestylesparentarrowpositionleft', '') != '') ? "left: " . self::testUnit($item->params->get('itemactivestylesparentarrowpositionleft', '')) . " !important;" : "")
					. "} ";
		} else if ($item->params->get('itemactivestylesparentarrowtype', '') == 'triangle' || $itemactivestylesparentarrowcolor) {
			if ($itemactivestylesparentarrowcolor) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.parent.item" . $item->id . ".active > a:after, div#" . $menuID . " ul.maximenuck li.maximenuck.parent.item" . $item->id . ".active > span.separator:after { " 
					. ( $params->get('orientation', 'horizontal') === 'vertical'  ? "border-left-color: " . $itemactivestylesparentarrowcolor . " !important;" : ( $itemlevel == 1 ? "border-top-color: " . $itemactivestylesparentarrowcolor . " !important;" : "border-left-color: " . $itemactivestylesparentarrowcolor . " !important;") )
					. "color: " . $itemactivestylesparentarrowcolor . " !important;"
					. "display:block;"
					. "position:absolute;"
					. (($item->params->get('itemactivestylesparentarrowmargintop', '') != '') ? "margin-top: " . self::testUnit($item->params->get('itemactivestylesparentarrowmargintop', '')) . " !important;" : "")
					. (($item->params->get('itemactivestylesparentarrowmarginright', '') != '') ? "margin-right: " . self::testUnit($item->params->get('itemactivestylesparentarrowmarginright', '')) . " !important;" : "")
					. (($item->params->get('itemactivestylesparentarrowmarginbottom', '') != '') ? "margin-bottom: " . self::testUnit($item->params->get('itemactivestylesparentarrowmarginbottom', '')) . " !important;" : "")
					. (($item->params->get('itemactivestylesparentarrowmarginleft', '') != '') ? "margin-left: " . self::testUnit($item->params->get('itemactivestylesparentarrowmarginleft', '')) . " !important;" : "")
					. (($item->params->get('itemactivestylesparentarrowpositiontop', '') != '') ? "top: " . self::testUnit($item->params->get('itemactivestylesparentarrowpositiontop', '')) . " !important;" : "")
					. (($item->params->get('itemactivestylesparentarrowpositionright', '') != '') ? "right: " . self::testUnit($item->params->get('itemactivestylesparentarrowpositionright', '')) . " !important;" : "")
					. (($item->params->get('itemactivestylesparentarrowpositionbottom', '') != '') ? "bottom: " . self::testUnit($item->params->get('itemactivestylesparentarrowpositionbottom', '')) . " !important;" : "")
					. (($item->params->get('itemactivestylesparentarrowpositionleft', '') != '') ? "left: " . self::testUnit($item->params->get('itemactivestylesparentarrowpositionleft', '')) . " !important;" : "")
					
					. "} ";
			}
		}

		// normal item styles
		if (isset($cssitemnormal)) {
			if ($cssitemnormal['margin'] || $cssitemnormal['background'] || $cssitemnormal['gradient'] || $cssitemnormal['borderradius'] || $cssitemnormal['shadow'] || $cssitemnormal['border']
			) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . $separator . ", 
div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . $separator . "{ " . $cssitemnormal['margin'] . $cssitemnormal['background'] . $cssitemnormal['gradient'] . $cssitemnormal['borderradius'] . $cssitemnormal['shadow'] . $cssitemnormal['border'] . " } ";
			}
			if ($cssitemnormal['padding']) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . " > a,
div#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . " > * { " . $cssitemnormal['padding'] . " } ";
			}
			if ($cssitemnormal['fontcolor'] || $cssitemnormal['fontsize'] || $cssitemnormal['fontweight']
			) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . " > a.maximenuck span.titreck, div#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . ".headingck > span.separator span.titreck,
div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . " > a.maximenuck span.titreck, div#" . $menuID . " li.maximenuck.item" . $item->id . ".level" . $itemlevel . ".headingck > span.separator span.titreck { " . $cssitemnormal['fontcolor'] . $cssitemnormal['fontsize'] . $cssitemnormal['fontweight'] . " } ";
			}
			if ($cssitemnormal['descfontcolor'] || $cssitemnormal['descfontsize']
			) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . " > a.maximenuck span.descck, div#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $item->level . ".headingck > span.separator span.descck,
div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . " > a.maximenuck span.descck, div#" . $menuID . " li.maximenuck.item" . $item->id . ".level" . $itemlevel . ".headingck > span.separator span.descck { " . $cssitemnormal['descfontcolor'] . $cssitemnormal['descfontsize'] . " } ";
			}
		}

		// hover item styles
		if (isset($cssitemhover)) {
			if ($cssitemhover['margin'] || $cssitemhover['background'] || $cssitemhover['gradient'] || $cssitemhover['borderradius'] || $cssitemhover['shadow'] || $cssitemhover['border']
			) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . $separator . ":hover,
div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . $separator . ":hover { " . $cssitemhover['margin'] . $cssitemhover['background'] . $cssitemhover['gradient'] . $cssitemhover['borderradius'] . $cssitemhover['shadow'] . $cssitemhover['border'] . " } ";
			}
			if ($cssitemhover['padding']) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . ":hover > a,
div#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . ":hover > span { " . $cssitemhover['padding'] . " } ";
			}
			if ($cssitemhover['fontcolor'] || $cssitemhover['fontsize'] || $cssitemhover['fontweight']
			) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . ":hover > a.maximenuck span.titreck, div#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . ":hover > span.separator span.titreck,
div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . ":hover > a.maximenuck span.titreck, div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . ":hover > span.separator span.titreck { " . $cssitemhover['fontcolor'] . $cssitemhover['fontsize'] . $cssitemhover['fontweight'] . " } ";
			}
			if ($cssitemhover['descfontcolor'] || $cssitemhover['descfontsize']
			) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . ":hover > a.maximenuck span.descck, div#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . ":hover > span.separator span.descck,
div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . ":hover > a.maximenuck span.descck, div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . ":hover > span.separator span.descck { " . $cssitemhover['descfontcolor'] . $cssitemhover['descfontsize'] . " } ";
			}
		}

		// active item styles
		if (isset($cssitemactive)) {
			if ($cssitemactive['margin'] || $cssitemactive['background'] || $cssitemactive['gradient'] || $cssitemactive['borderradius'] || $cssitemactive['shadow'] || $cssitemactive['border']
			) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . ".active" . $separator . ",
div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . ".active" . $separator . " { " . $cssitemactive['margin'] . $cssitemactive['background'] . $cssitemactive['gradient'] . $cssitemactive['borderradius'] . $cssitemactive['shadow'] . $cssitemactive['border'] . " } ";
			}
			if ($cssitemactive['padding']) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . ".active > a,
div#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . ".active > span { " . $cssitemactive['padding'] . " } ";
			}
			if ($cssitemactive['fontcolor'] || $cssitemactive['fontsize'] || $cssitemactive['fontweight']
			) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . ".active > a.maximenuck span.titreck, div#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . ".active > span.separator span.titreck,
div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . ".active > a.maximenuck span.titreck, div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . ".active > span.separator span.titreck { " . $cssitemactive['fontcolor'] . $cssitemactive['fontsize'] . $cssitemactive['fontweight'] . " } ";
			}
			if ($cssitemactive['descfontcolor'] || $cssitemactive['descfontsize']
			) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . ".active > a.maximenuck span.descck, div#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . ".active > span.separator span.descck,
div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . ".active > a.maximenuck span.descck, div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . ".active > span.separator span.descck { " . $cssitemactive['descfontcolor'] . $cssitemactive['descfontsize'] . " } ";
			}
		}

		// submenu item styles
		if (isset($csssubmenu)) {
			if ($csssubmenu['padding'] || $csssubmenu['margin'] || $csssubmenu['background'] || $csssubmenu['gradient'] || $csssubmenu['borderradius'] || $csssubmenu['shadow'] || $csssubmenu['border']) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $item->level . " > div.floatck,
div#" . $menuID . " .maxipushdownck div.floatck.submenuck" . $item->id . " { " . $csssubmenu['padding'] . $csssubmenu['margin'] . $csssubmenu['background'] . $csssubmenu['gradient'] . $csssubmenu['borderradius'] . $csssubmenu['shadow'] . $csssubmenu['border'] . " } ";
			}
		}

		return $itemcss;
	}

	/**
	 * load the css properties for the module
	 * @param JRegistry $params
	 * @param string $menuID the module ID
	 *
	 * @return void
	 */
	static function injectModuleCss($params, $menuID) {
		if ($params->get('doCompile') || $params->get('loadcompiledcss', '0') == '0') {
			$csstoinject = self::createModuleCss($params, $menuID);
			if ($csstoinject) {
				if ($params->get('loadcompiledcss', '0') == '0') {
					$document = JFactory::getDocument();
					$document->addStyleDeclaration($csstoinject);
				} else {
					self::$_modulecss .= $csstoinject;
				}
			}
		}
	}

	static function createModuleCss($params, $menuID) {
		$document = JFactory::getDocument();

		// set the prefixes for all xml fieldset
		$prefixes = array('menustyles',
			'level1itemnormalstyles',
			'level1itemhoverstyles',
			'level1itemactivestyles',
			'level2menustyles',
			'level2itemnormalstyles',
			'level2itemhoverstyles',
			'level2itemactivestyles',
			'level1itemnormalstylesicon',
			'level1itemhoverstylesicon',
			'level2itemnormalstylesicon',
			'level2itemhoverstylesicon',
			'headingstyles');

		$css = new stdClass();
		$csstoinject = '';
		$important = false;
		$fields = Array();

		// create the css rules for each prefix
		foreach ($prefixes as $prefix) {
			$param = $params->get($prefix, '[]');
			$objs = json_decode(str_replace("|qq|", "\"", $param));
			$fields[$prefix] = new CkCssParams();

			if (!$objs)
				continue;

			foreach ($objs as $obj) {
				$fieldid = str_replace($prefix . "_", "", $obj->id);
				$fields[$prefix]->$fieldid = isset($obj->value) ? $obj->value : null;
			}

			if ($prefix == 'headingstyles') {
				$important = true;
			}
			$css->$prefix = modMaximenuckHelper::createCss($menuID, $fields[$prefix], $prefix, $important, '');
		}

		$csstoinject = '';

		// get the css suffix for the module
		$menu_class = ( $params->get('orientation', 'horizontal') === 'horizontal' ) ? '.maximenuckh' : '.maximenuckv';

		switch (trim($params->get('layout', 'default'), '_:')) {
			case 'flatlist':
				$menu_begin = ' ul.maximenuck2';
				break;
			case 'nativejoomla':
				$menu_begin = ' ul';
				break;
			default:
			case 'default':
				$menu_begin = ' ul.maximenuck';
				break;
		}

		// set the specific menu ID to give more weight to the css rule
		$menuCSSID = $menuID . $menu_class . $menu_begin;

		// load the google font
		$gfont = $fields['menustyles']->get('menustylestextgfont', '');
		$isGfont = $fields['menustyles']->get('menustylestextisgfont', '1');

		if ($gfont) {
			$gfontfamily = self::get_gfontfamily($gfont);
			if ($isGfont) $document->addStylesheet('https://fonts.googleapis.com/css?family=' . $gfont);
			$csstoinject .= "div#" . $menuID . " li > a, div#" . $menuID . " li > span { font-family: '" . $gfontfamily . "';}";
		}
		$gfont = $fields['level2itemnormalstyles']->get('level2itemnormalstylestextgfont', '');
		$isGfont = $fields['level2itemnormalstyles']->get('level2itemnormalstylestextisgfont', '1');
		if ($gfont) {
			$gfontfamily = self::get_gfontfamily($gfont);
			if ($isGfont) $document->addStylesheet('https://fonts.googleapis.com/css?family=' . $gfont);
			$csstoinject .= "div#" . $menuID . " ul.maximenuck2 li > a, div#" . $menuID . " ul.maximenuck2 li > span { font-family: '" . $gfontfamily . "';}";
		}

		// set the styles for the global menu
		$submenuwidth = $fields['menustyles']->get('menustylessubmenuwidth', '');
		$submenuheight = $fields['menustyles']->get('menustylessubmenuheight', '');
		$submenu1marginleft = $fields['menustyles']->get('menustylessubmenu1marginleft', '');
		$submenu1margintop = $fields['menustyles']->get('menustylessubmenu1margintop', '');
		$submenu2marginleft = $fields['menustyles']->get('menustylessubmenu2marginleft', '');
		$submenu2margintop = $fields['menustyles']->get('menustylessubmenu2margintop', '');
		
		if ($submenuwidth)
			$csstoinject .= "\ndiv#" . $menuCSSID . " div.maxidrop-main, div#" . $menuCSSID . " li div.maxidrop-main { width: " . self::testUnit($submenuwidth) . "; } ";
		if ($submenuheight)
			$csstoinject .= "\ndiv#" . $menuCSSID . " div.maxidrop-main, div#" . $menuCSSID . " li.maximenuck div.maxidrop-main { height: " . self::testUnit($submenuheight) . "; } ";
		if ($submenu1marginleft)
			$csstoinject .= "\ndiv#" . $menuCSSID . " div.floatck, div#" . $menuCSSID . " li.maximenuck div.floatck { margin-left: " . self::testUnit($submenu1marginleft) . "; } ";
		if ($submenu1margintop)
			$csstoinject .= "\ndiv#" . $menuCSSID . " div.floatck, div#" . $menuCSSID . " li.maximenuck div.floatck { margin-top: " . self::testUnit($submenu1margintop) . "; } ";
		if ($submenu2marginleft)
			$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck div.floatck div.floatck { margin-left: " . self::testUnit($submenu2marginleft) . "; } ";
		if ($submenu2margintop)
			$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck div.floatck div.floatck { margin-top: " . self::testUnit($submenu2margintop) . "; } ";

		$level1itemnormalstylesparentarrowcolor = $fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowcolor', '') ? $fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowcolor', '') : $fields['level1itemnormalstyles']->get('level1itemnormalstylesfontcolor', '');
		if ($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowtype', '') != 'none'
				&& $fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowtype', '') != 'image'
				&& ($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowtype', '') == 'triangle' || $level1itemnormalstylesparentarrowcolor)
						){
			// for parent arrow normal state
			if ($level1itemnormalstylesparentarrowcolor) {
				$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1.parent > a:after, div#" . $menuCSSID . " li.maximenuck.level1.parent > span.separator:after { " 
					. ( $params->get('orientation', 'horizontal') === 'vertical'  ? "border-left-color: " . $level1itemnormalstylesparentarrowcolor . ";" : "border-top-color: " . $level1itemnormalstylesparentarrowcolor . ";" )
					. "color: " . $level1itemnormalstylesparentarrowcolor . ";"
						. "display:block;"
						. "position:absolute;"
					. (($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowmargintop', '') != '') ? "margin-top: " . self::testUnit($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowmargintop', '')) . ";" : "")
					. (($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowmarginright', '') != '') ? "margin-right: " . self::testUnit($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowmarginright', '')) . ";" : "")
					. (($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowmarginbottom', '') != '') ? "margin-bottom: " . self::testUnit($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowmarginbottom', '')) . ";" : "")
					. (($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowmarginleft', '') != '') ? "margin-left: " . self::testUnit($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowmarginleft', '')) . ";" : "")
						. (($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowpositiontop', '') != '') ? "top: " . self::testUnit($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowpositiontop', '')) . ";" : "")
						. (($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowpositionright', '') != '') ? "right: " . self::testUnit($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowpositionright', '')) . ";" : "")
						. (($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowpositionbottom', '') != '') ? "bottom: " . self::testUnit($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowpositionbottom', '')) . ";" : "")
						. (($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowpositionleft', '') != '') ? "left: " . self::testUnit($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowpositionleft', '')) . ";" : "")
					. "} ";
			}

			$level1itemhoverstylesparentarrowcolor = $fields['level1itemhoverstyles']->get('level1itemhoverstylesparentarrowcolor', '') ? $fields['level1itemhoverstyles']->get('level1itemhoverstylesparentarrowcolor', '') : $fields['level1itemhoverstyles']->get('level1itemhoverstylesfontcolor', '');
			// for parent arrow hover state
			if ($level1itemhoverstylesparentarrowcolor) {
				$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1.parent:hover > a:after, div#" . $menuCSSID . " li.maximenuck.level1.parent:hover > span.separator:after { " 
					. ( $params->get('orientation', 'horizontal') === 'vertical'  ? "border-left-color: " . $level1itemhoverstylesparentarrowcolor . ";" : "border-top-color: " . $level1itemhoverstylesparentarrowcolor . ";" )
					. "color: " . $level1itemhoverstylesparentarrowcolor . ";"
					. "} ";
			}
		} else if ($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowtype', '') == 'image') {
			// for parent arrow normal state
			$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1.parent > a:after, div#" . $menuCSSID . " li.maximenuck.level1.parent > span.separator:after { " 
					// . ( $params->get('orientation', 'horizontal') === 'vertical'  ? "border-left-color: " . $level1itemnormalstylesparentarrowcolor . ";" : "border-top-color: " . $level1itemnormalstylesparentarrowcolor . ";" )
					. "border: none;"
					. "display:block;"
					. "position:absolute;"
					. (($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentitemimage', '') != '') ? "background-image: url(" . JUri::root(true) . "/" . $fields['level1itemnormalstyles']->get('level1itemnormalstylesparentitemimage', '') . ");" : "")
					. (($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentitemimagepositionx', '') != '' && $fields['level1itemnormalstyles']->get('level1itemnormalstylesparentitemimagepositiony', '') != '') ? "background-position: " . $fields['level1itemnormalstyles']->get('level1itemnormalstylesparentitemimagepositionx', '') . " " . $fields['level1itemnormalstyles']->get('level1itemnormalstylesparentitemimagepositiony', '') . ";" : "")
					. (($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentitemimagerepeat', '') != '') ? "background-repeat: " . $fields['level1itemnormalstyles']->get('level1itemnormalstylesparentitemimagerepeat', '') . ";" : "")
					. (($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowwidth', '') != '') ? "width: " . self::testUnit($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowwidth', '')) . ";" : "")
					. (($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowheight', '') != '') ? "height: " . self::testUnit($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowheight', '')) . ";" : "")
					. (($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowmargintop', '') != '') ? "margin-top: " . self::testUnit($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowmargintop', '')) . ";" : "")
					. (($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowmarginright', '') != '') ? "margin-right: " . self::testUnit($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowmarginright', '')) . ";" : "")
					. (($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowmarginbottom', '') != '') ? "margin-bottom: " . self::testUnit($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowmarginbottom', '')) . ";" : "")
					. (($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowmarginleft', '') != '') ? "margin-left: " . self::testUnit($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowmarginleft', '')) . ";" : "")
					. (($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowpositiontop', '') != '') ? "top: " . self::testUnit($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowpositiontop', '')) . ";" : "")
					. (($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowpositionright', '') != '') ? "right: " . self::testUnit($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowpositionright', '')) . ";" : "")
					. (($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowpositionbottom', '') != '') ? "bottom: " . self::testUnit($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowpositionbottom', '')) . ";" : "")
					. (($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowpositionleft', '') != '') ? "left: " . self::testUnit($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowpositionleft', '')) . ";" : "")
					. "} ";
			// for parent arrow hover state
			if ($fields['level1itemhoverstyles']->get('level1itemhoverstylesparentitemimage', '')) {
				$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1.parent:hover > a:after, div#" . $menuCSSID . " li.maximenuck.level1.parent:hover > span.separator:after { " 
//					. ( $params->get('orientation', 'horizontal') === 'vertical'  ? "border-left-color: " . $level1itemhoverstylesparentarrowcolor . ";" : "border-top-color: " . $level1itemhoverstylesparentarrowcolor . ";" )
					. (($fields['level1itemhoverstyles']->get('level1itemhoverstylesparentitemimage', '') != '') ? "background-image: url(" . JUri::root(true) . "/" . $fields['level1itemhoverstyles']->get('level1itemhoverstylesparentitemimage', '') . ");" : "")
					. (($fields['level1itemhoverstyles']->get('level1itemhoverstylesparentitemimagepositionx', '') != '' && $fields['level1itemhoverstyles']->get('level1itemhoverstylesparentitemimagepositiony', '') != '') ? "background-position: " . $fields['level1itemhoverstyles']->get('level1itemhoverstylesparentitemimagepositionx', '') . " " . $fields['level1itemhoverstyles']->get('level1itemhoverstylesparentitemimagepositiony', '') . ";" : "")
					. (($fields['level1itemhoverstyles']->get('level1itemhoverstylesparentitemimagerepeat', '') != '') ? "background-repeat: " . $fields['level1itemhoverstyles']->get('level1itemhoverstylesparentitemimagerepeat', '') . ";" : "")
					. "} ";
			}
		} else if ($fields['level1itemnormalstyles']->get('level1itemnormalstylesparentarrowtype', '') == 'none') {
				$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1.parent > a:after, div#" . $menuCSSID . " li.maximenuck.level1.parent > span.separator:after { " 
					. "display: none;"
					. "}";
		}

		$level2itemnormalstylesparentarrowcolor = $fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowcolor', '') ? $fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowcolor', '') : $fields['level2itemnormalstyles']->get('level2itemnormalstylesfontcolor', '');
		if ($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowtype', '') != 'none'
				&& $fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowtype', '') != 'image'
				&& ($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowtype', '') == 'triangle' || $level2itemnormalstylesparentarrowcolor) 
				) {
			// for parent arrow normal state
			if ($level2itemnormalstylesparentarrowcolor) {
				$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck.parent > a:after, div#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck.parent > span.separator:after,
	div#" . $menuID . " .maxipushdownck li.maximenuck.parent > a:after, div#" . $menuID . " .maxipushdownck li.maximenuck.parent > span.separator:after { " 
					. "border-left-color: " . $level2itemnormalstylesparentarrowcolor . ";"
					. "color: " . $level2itemnormalstylesparentarrowcolor . ";"
					. (($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowmargintop', '') != '') ? "margin-top: " . self::testUnit($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowmargintop', '')) . ";" : "")
					. (($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowmarginright', '') != '') ? "margin-right: " . self::testUnit($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowmarginright', '')) . ";" : "")
					. (($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowmarginbottom', '') != '') ? "margin-bottom: " . self::testUnit($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowmarginbottom', '')) . ";" : "")
					. (($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowmarginleft', '') != '') ? "margin-left: " . self::testUnit($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowmarginleft', '')) . ";" : "")
					. "} ";
			}

			$level2itemhoverstylesparentarrowcolor = $fields['level2itemhoverstyles']->get('level2itemhoverstylesparentarrowcolor', '') ? $fields['level2itemhoverstyles']->get('level2itemhoverstylesparentarrowcolor', '') : $fields['level2itemhoverstyles']->get('level2itemhoverstylesfontcolor', '');
			// for parent arrow hover state
			if ($level2itemhoverstylesparentarrowcolor) {
				$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck.parent:hover > a:after, div#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck.parent:hover > span.separator:after,
	div#" . $menuID . " .maxipushdownck li.maximenuck.parent:hover > a:after, div#" . $menuID . " .maxipushdownck li.maximenuck.parent:hover > span.separator:after { " 
					. "border-color: transparent transparent transparent " . $level2itemhoverstylesparentarrowcolor . ";"
					. "color: " . $level2itemhoverstylesparentarrowcolor . ";"
					. "} ";
			}
		} else if ($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowtype', '') == 'image') {
			// for parent arrow normal state
			$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck.parent > a:after, div#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck.parent > span.separator:after,
	div#" . $menuID . " .maxipushdownck li.maximenuck.parent > a:after, div#" . $menuID . " .maxipushdownck li.maximenuck.parent > span.separator:after { " 
					// . ( $params->get('orientation', 'horizontal') === 'vertical'  ? "border-left-color: " . $level2itemnormalstylesparentarrowcolor . ";" : "border-top-color: " . $level2itemnormalstylesparentarrowcolor . ";" )
					. "border: none;"
					. "display:block;"
					. "position:absolute;"
					. (($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentitemimage', '') != '') ? "background-image: url(" . JUri::root(true) . "/" . $fields['level2itemnormalstyles']->get('level2itemnormalstylesparentitemimage', '') . ");" : "")
					. (($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentitemimagepositionx', '') != '' && $fields['level2itemnormalstyles']->get('level2itemnormalstylesparentitemimagepositiony', '') != '') ? "background-position: " . $fields['level2itemnormalstyles']->get('level2itemnormalstylesparentitemimagepositionx', '') . " " . $fields['level2itemnormalstyles']->get('level2itemnormalstylesparentitemimagepositiony', '') . ";" : "")
					. (($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentitemimagerepeat', '') != '') ? "background-repeat: " . $fields['level2itemnormalstyles']->get('level2itemnormalstylesparentitemimagerepeat', '') . ";" : "")
					. (($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowwidth', '') != '') ? "width: " . self::testUnit($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowwidth', '')) . ";" : "")
					. (($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowheight', '') != '') ? "height: " . self::testUnit($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowheight', '')) . ";" : "")
					. (($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowmargintop', '') != '') ? "margin-top: " . self::testUnit($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowmargintop', '')) . ";" : "")
					. (($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowmarginright', '') != '') ? "margin-right: " . self::testUnit($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowmarginright', '')) . ";" : "")
					. (($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowmarginbottom', '') != '') ? "margin-bottom: " . self::testUnit($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowmarginbottom', '')) . ";" : "")
					. (($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowmarginleft', '') != '') ? "margin-left: " . self::testUnit($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowmarginleft', '')) . ";" : "")
					. (($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowpositiontop', '') != '') ? "top: " . self::testUnit($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowpositiontop', '')) . ";" : "")
					. (($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowpositionright', '') != '') ? "right: " . self::testUnit($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowpositionright', '')) . ";" : "")
					. (($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowpositionbottom', '') != '') ? "bottom: " . self::testUnit($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowpositionbottom', '')) . ";" : "")
					. (($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowpositionleft', '') != '') ? "left: " . self::testUnit($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowpositionleft', '')) . ";" : "")
					. "} ";
			// for parent arrow hover state
			if ($fields['level2itemhoverstyles']->get('level2itemhoverstylesparentitemimage', '')) {
				$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck.parent:hover > a:after, div#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck.parent:hover > span.separator:after,
	div#" . $menuID . " .maxipushdownck li.maximenuck.parent:hover > a:after, div#" . $menuID . " .maxipushdownck li.maximenuck.parent:hover > span.separator:after { " 
					. (($fields['level2itemhoverstyles']->get('level2itemhoverstylesparentitemimage', '') != '') ? "background-image: url(" . JUri::root(true) . "/" . $fields['level2itemhoverstyles']->get('level2itemhoverstylesparentitemimage', '') . ");" : "")
					. (($fields['level2itemhoverstyles']->get('level2itemhoverstylesparentitemimagepositionx', '') != '' && $fields['level2itemhoverstyles']->get('level2itemhoverstylesparentitemimagepositiony', '') != '') ? "background-position: " . $fields['level2itemhoverstyles']->get('level2itemhoverstylesparentitemimagepositionx', '') . " " . $fields['level2itemhoverstyles']->get('level2itemhoverstylesparentitemimagepositiony', '') . ";" : "")
					. (($fields['level2itemhoverstyles']->get('level2itemhoverstylesparentitemimagerepeat', '') != '') ? "background-repeat: " . $fields['level2itemhoverstyles']->get('level2itemhoverstylesparentitemimagerepeat', '') . ";" : "")
					. "} ";
			}
		} else if ($fields['level2itemnormalstyles']->get('level2itemnormalstylesparentarrowtype', '') == 'none') {
				$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck.parent > a:after, div#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck.parent > span.separator:after,
	div#" . $menuID . " .maxipushdownck li.maximenuck.parent > a:after, div#" . $menuID . " .maxipushdownck li.maximenuck.parent > span.separator:after { " 
					. "display: none;"
					. "}";
		}

		// for item icon level1
		if (isset($css->level1itemnormalstylesicon)) {
			$level1itemiconwidth = isset($fields['level1itemnormalstylesicon']) && $fields['level1itemnormalstylesicon']->get('level12itemnormalstylesiconfontsize') ? "width:" . self::testUnit($fields['level1itemnormalstylesicon']->get('level1itemnormalstylesiconfontsize')) . ";" : "";
			if ($css->level1itemnormalstylesicon['margin'] || $css->level1itemnormalstylesicon['fontsize'] || $css->level1itemnormalstylesicon['line-height'] || $css->level1itemnormalstylesicon['fontcolor']) {
					$csstoinject .= "\ndiv#" . $menuCSSID . " li.level1 > *:not(div) .maximenuiconck { "
						. "float: left;"
						. $level1itemiconwidth
						. $css->level1itemnormalstylesicon['margin'] . $css->level1itemnormalstylesicon['fontsize'] . $css->level1itemnormalstylesicon['line-height'] . $css->level1itemnormalstylesicon['fontcolor']
						. "}";
			}
		}

		if (isset($css->level1itemhoverstylesicon) && $css->level1itemhoverstylesicon['fontcolor']) {
				$csstoinject .= "\ndiv#" . $menuCSSID . " li.level1:hover > *:not(div) .maximenuiconck { "
					. $css->level1itemhoverstylesicon['fontcolor']
					. "}";
		}
		
		// for item icon level2
		if (isset($css->level2itemnormalstylesicon)) {
			$level2itemiconwidth = isset($fields['level2itemnormalstylesicon']) && $fields['level2itemnormalstylesicon']->get('level2itemnormalstylesiconfontsize') ? "width:" . self::testUnit($fields['level2itemnormalstylesicon']->get('level2itemnormalstylesiconfontsize')) . ";" : "";
			if ($css->level2itemnormalstylesicon['margin'] || $css->level2itemnormalstylesicon['fontsize'] || $css->level2itemnormalstylesicon['line-height'] || $css->level2itemnormalstylesicon['fontcolor']) {
					$csstoinject .= "\ndiv#" . $menuCSSID . " li.level1 li > *:not(div) .maximenuiconck { "
						. "float: left;"
						. $level2itemiconwidth
						. $css->level2itemnormalstylesicon['margin'] . $css->level2itemnormalstylesicon['fontsize'] . $css->level2itemnormalstylesicon['line-height'] . $css->level2itemnormalstylesicon['fontcolor']
						. "}";
			}
		}
		if (isset($css->level2itemhoverstylesicon) && $css->level2itemhoverstylesicon['fontcolor']) {
				$csstoinject .= "\ndiv#" . $menuCSSID . " li.level1 li:hover > *:not(div) .maximenuiconck { "
					. $css->level2itemhoverstylesicon['fontcolor']
					. "}";
		}

		// root styles
		if (isset($css->menustyles)) {
			if ($css->menustyles['padding'] || $css->menustyles['margin'] || $css->menustyles['background'] || $css->menustyles['gradient'] || $css->menustyles['borderradius'] || $css->menustyles['shadow'] || $css->menustyles['border'] || $css->menustyles['text-align']
			) {
				$csstoinject .= "\ndiv#" . $menuCSSID . " { " . $css->menustyles['padding'] . $css->menustyles['margin'] . $css->menustyles['background'] . $css->menustyles['gradient'] . $css->menustyles['borderradius'] . $css->menustyles['shadow'] . $css->menustyles['border'] . $css->menustyles['text-align'] . " } ";
			}
			if ($css->menustyles['fontcolor'] || $css->menustyles['fontsize'] || $css->menustyles['textshadow']
			) {
				$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck > a span.titreck, div#" . $menuCSSID . " li.maximenuck > span.separator span.titreck,
div#" . $menuID . " .maxipushdownck li.maximenuck > a span.titreck, div#" . $menuID . " .maxipushdownck li.maximenuck > span.separator span.titreck { " . $css->menustyles['fontcolor'] . $css->menustyles['fontsize'] . $css->menustyles['textshadow'] . " } ";
			}
			if ($css->menustyles['descfontcolor'] || $css->menustyles['descfontsize']
			) {
				$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck > a span.descck, div#" . $menuCSSID . " li.maximenuck > span.separator span.descck,
div#" . $menuID . " .maxipushdownck li.maximenuck > a span.descck, div#" . $menuID . " .maxipushdownck li.maximenuck > span.separator span.descck { " . $css->menustyles['descfontcolor'] . $css->menustyles['descfontsize'] . " } ";
			}
		}

		// level1 normal items styles
		if (isset($css->level1itemnormalstyles)) {
			if ($css->level1itemnormalstyles['padding'] || $css->level1itemnormalstyles['margin'] || $css->level1itemnormalstyles['background'] || $css->level1itemnormalstyles['gradient'] || $css->level1itemnormalstyles['borderradius'] || $css->level1itemnormalstyles['shadow'] || $css->level1itemnormalstyles['border']
			) {
				$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1, div#" . $menuCSSID . " li.maximenuck.level1.parent { " . $css->level1itemnormalstyles['margin'] . $css->level1itemnormalstyles['background'] . $css->level1itemnormalstyles['gradient'] . $css->level1itemnormalstyles['borderradius'] . $css->level1itemnormalstyles['shadow'] . $css->level1itemnormalstyles['border'] . " } ";
				$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1 > a, div#" . $menuCSSID . " li.maximenuck.level1 > span.separator { " . $css->level1itemnormalstyles['padding'] . " } ";
			}
			if ($css->level1itemnormalstyles['fontcolor'] || $css->level1itemnormalstyles['fontsize'] || $css->level1itemnormalstyles['textshadow'] || $css->level1itemnormalstyles['text-transform']
			) {
				$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1 > a span.titreck, div#" . $menuCSSID . " li.maximenuck.level1 > span.separator span.titreck { " . $css->level1itemnormalstyles['fontcolor'] . $css->level1itemnormalstyles['fontsize'] . $css->level1itemnormalstyles['fontweight'] . $css->level1itemnormalstyles['textshadow'] . $css->level1itemnormalstyles['text-transform'] . " } ";
			}
			if ($css->level1itemnormalstyles['descfontcolor'] || $css->level1itemnormalstyles['descfontsize']
			) {
				$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1 > a span.descck, div#" . $menuCSSID . " li.maximenuck.level1 > span.separator span.descck { " . $css->level1itemnormalstyles['descfontcolor'] . $css->level1itemnormalstyles['descfontsize'] . " } ";
			}
		}

		// level1 hover items styles
		if (isset($fields['level1itemactivestyles']) && $fields['level1itemactivestyles']->get('level1itemactivestylesidemhover') == '1') {
			$level1active_li = "\ndiv#" . $menuCSSID . " li.maximenuck.level1.active, div#" . $menuCSSID . " li.maximenuck.level1.parent.active, ";
			$level1active_li_a = "\ndiv#" . $menuCSSID . " li.maximenuck.level1.active > a, div#" . $menuCSSID . " li.maximenuck.level1.active > span, ";
			$level1active_titreck = "\ndiv#" . $menuCSSID . " li.maximenuck.level1.active > a span.titreck, div#" . $menuCSSID . " li.maximenuck.level1.active > span.separator span.titreck, ";
			$level1active_descck = "\ndiv#" . $menuCSSID . " li.maximenuck.level1.active > a span.descck, div#" . $menuCSSID . " li.maximenuck.level1.active > span.separator span.descck, ";
		} else {
			$level1active_li = "";
			$level1active_li_a = "";
			$level1active_titreck = "";
			$level1active_descck = "";
		}
		if (isset($css->level1itemhoverstyles)) {
			if ($css->level1itemhoverstyles['padding'] || $css->level1itemhoverstyles['margin'] || $css->level1itemhoverstyles['background'] || $css->level1itemhoverstyles['gradient'] || $css->level1itemhoverstyles['borderradius'] || $css->level1itemhoverstyles['shadow'] || $css->level1itemhoverstyles['border']
			) {
				$csstoinject .= $level1active_li . "\ndiv#" . $menuCSSID . " li.maximenuck.level1:hover, div#" . $menuCSSID . " li.maximenuck.level1.parent:hover { " . $css->level1itemhoverstyles['margin'] . $css->level1itemhoverstyles['background'] . $css->level1itemhoverstyles['gradient'] . $css->level1itemhoverstyles['borderradius'] . $css->level1itemhoverstyles['shadow'] . $css->level1itemhoverstyles['border'] . " } ";
				$csstoinject .= $level1active_li_a . "\ndiv#" . $menuCSSID . " li.maximenuck.level1:hover > a, div#" . $menuCSSID . " li.maximenuck.level1:hover > span.separator { " . $css->level1itemhoverstyles['padding'] . " } ";
			}
			if ($css->level1itemhoverstyles['fontcolor'] || $css->level1itemhoverstyles['fontsize'] || $css->level1itemhoverstyles['textshadow']
			) {
				$csstoinject .= $level1active_titreck . "\ndiv#" . $menuCSSID . " li.maximenuck.level1:hover > a span.titreck, div#" . $menuCSSID . " li.maximenuck.level1:hover > span.separator span.titreck { " . $css->level1itemhoverstyles['fontcolor'] . $css->level1itemhoverstyles['fontsize'] . $css->level1itemhoverstyles['fontweight'] . $css->level1itemhoverstyles['textshadow'] . " } ";
			}
			if ($css->level1itemhoverstyles['descfontcolor'] || $css->level1itemhoverstyles['descfontsize']
			) {
				$csstoinject .= $level1active_descck . "\ndiv#" . $menuCSSID . " li.maximenuck.level1:hover > a span.descck, div#" . $menuCSSID . " li.maximenuck.level1:hover > span.separator span.descck { " . $css->level1itemhoverstyles['descfontcolor'] . $css->level1itemhoverstyles['descfontsize'] . " } ";
			}
		}

		if (isset($fields['level1itemactivestyles']) && $fields['level1itemactivestyles']->get('level1itemactivestylesidemhover') == '0') {
			// level1 active items styles
			if (isset($css->level1itemactivestyles)) {
				if ($css->level1itemactivestyles['padding'] || $css->level1itemactivestyles['margin'] || $css->level1itemactivestyles['background'] || $css->level1itemactivestyles['gradient'] || $css->level1itemactivestyles['borderradius'] || $css->level1itemactivestyles['shadow'] || $css->level1itemactivestyles['border']
				) {
					$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1.active { " . $css->level1itemactivestyles['margin'] . $css->level1itemactivestyles['background'] . $css->level1itemactivestyles['gradient'] . $css->level1itemactivestyles['borderradius'] . $css->level1itemactivestyles['shadow'] . $css->level1itemactivestyles['border'] . " } ";
					$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1.active > a, div#" . $menuCSSID . " li.maximenuck.level1.active > span.separator { " . $css->level1itemactivestyles['padding'] . " } ";
				}
				if ($css->level1itemactivestyles['fontcolor'] || $css->level1itemactivestyles['fontsize'] || $css->level1itemactivestyles['textshadow']
				) {
					$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1.active > a span.titreck, div#" . $menuCSSID . " li.maximenuck.level1.active > span.separator span.titreck { " . $css->level1itemactivestyles['fontcolor'] . $css->level1itemactivestyles['fontsize'] . $css->level1itemactivestyles['fontweight'] . $css->level1itemactivestyles['textshadow'] . " } ";
				}
				if ($css->level1itemactivestyles['descfontcolor'] || $css->level1itemactivestyles['descfontsize']
				) {
					$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1.active > a span.descck, div#" . $menuCSSID . " li.maximenuck.level1.active > span.separator span.descck { " . $css->level1itemactivestyles['descfontcolor'] . $css->level1itemactivestyles['descfontsize'] . " } ";
				}
			}
		}
		
		// submenu styles
		if (isset($css->level2menustyles)) {
			if ($css->level2menustyles['padding'] || $css->level2menustyles['margin'] || $css->level2menustyles['background'] || $css->level2menustyles['gradient'] || $css->level2menustyles['borderradius'] || $css->level2menustyles['shadow'] || $css->level2menustyles['border']
			) {
				$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck div.floatck, div#" . $menuCSSID . " li.maximenuck div.floatck div.floatck,
div#" . $menuID . " .maxipushdownck div.floatck { " . $css->level2menustyles['padding'] . $css->level2menustyles['margin'] . $css->level2menustyles['background'] . $css->level2menustyles['gradient'] . $css->level2menustyles['borderradius'] . $css->level2menustyles['shadow'] . $css->level2menustyles['border'] . " } ";
			}
		}

		// level2 normal items styles
		if (isset($css->level2itemnormalstyles)) {
			if ($css->level2itemnormalstyles['padding'] || $css->level2itemnormalstyles['margin'] || $css->level2itemnormalstyles['background'] || $css->level2itemnormalstyles['gradient'] || $css->level2itemnormalstyles['borderradius'] || $css->level2itemnormalstyles['shadow'] || $css->level2itemnormalstyles['border']
			) {
				$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck:not(.headingck), div#" . $menuID . " li.maximenuck.maximenuflatlistck:not(.level1):not(.headingck),
div#" . $menuID . " .maxipushdownck li.maximenuck:not(.headingck) { " . $css->level2itemnormalstyles['margin'] . $css->level2itemnormalstyles['background'] . $css->level2itemnormalstyles['gradient'] . $css->level2itemnormalstyles['borderradius'] . $css->level2itemnormalstyles['shadow'] . $css->level2itemnormalstyles['border'] . " } ";
				$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck:not(.headingck) > a, div#" . $menuID . " li.maximenuck.maximenuflatlistck:not(.level1):not(.headingck) > a,
div#" . $menuID . " .maxipushdownck li.maximenuck:not(.headingck) > a, ndiv#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck:not(.headingck) > span.separator, div#" . $menuID . " li.maximenuck.maximenuflatlistck:not(.level1):not(.headingck) > span.separator,
div#" . $menuID . " .maxipushdownck li.maximenuck:not(.headingck) > span.separator { " . $css->level2itemnormalstyles['padding'] . " } ";
			}
			if ($css->level2itemnormalstyles['fontcolor'] || $css->level2itemnormalstyles['fontsize'] || $css->level2itemnormalstyles['textshadow'] || $css->level2itemnormalstyles['text-transform']
			) {
				$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck > a span.titreck, div#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck > span.separator span.titreck, div#" . $menuID . " li.maximenuck.maximenuflatlistck:not(.level1) span.titreck,
div#" . $menuID . " .maxipushdownck li.maximenuck > a span.titreck, div#" . $menuID . " .maxipushdownck li.maximenuck > span.separator span.titreck { " . $css->level2itemnormalstyles['fontcolor'] . $css->level2itemnormalstyles['fontsize'] . $css->level2itemnormalstyles['fontweight'] . $css->level2itemnormalstyles['textshadow'] . $css->level2itemnormalstyles['text-transform'] . " } ";
			}
			if ($css->level2itemnormalstyles['descfontcolor'] || $css->level2itemnormalstyles['descfontsize']
			) {
				$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck > a span.descck, div#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck > span.separator span.descck, div#" . $menuID . " li.maximenuck.maximenuflatlistck:not(.level1) span.descck,
div#" . $menuID . " .maxipushdownck li.maximenuck > a span.descck, div#" . $menuID . " .maxipushdownck li.maximenuck > span.separator span.descck { " . $css->level2itemnormalstyles['descfontcolor'] . $css->level2itemnormalstyles['descfontsize'] . " } ";
			}
		}

		// level2 hover items styles
		if (isset($fields['level2itemactivestyles']) && $fields['level2itemactivestyles']->get('level2itemactivestylesidemhover') == '1') {
			$level2active_li = "\ndiv#" . $menuCSSID . " li.maximenuck.level2.active:not(.headingck), div#" . $menuCSSID . " li.maximenuck.level2.parent.active:not(.headingck), div#" . $menuID . " li.maximenuck.maximenuflatlistck.active:not(.level1):not(.headingck),";
			$level2active_li_a = "\ndiv#" . $menuCSSID . " li.maximenuck.level2.active:not(.headingck), div#" . $menuCSSID . " li.maximenuck.level2.parent.active:not(.headingck), div#" . $menuID . " li.maximenuck.maximenuflatlistck.active:not(.level1):not(.headingck),";
			$level2active_titreck = "\ndiv#" . $menuCSSID . " li.maximenuck.level2.active > a span.titreck, div#" . $menuCSSID . " li.maximenuck.level2.active > span.separator span.titreck, div#" . $menuID . " li.maximenuck.maximenuflatlistck.active:not(.level1) span.titreck,";
			$level2active_descck = "\ndiv#" . $menuCSSID . " li.maximenuck.level2.active > a span.descck, div#" . $menuCSSID . " li.maximenuck.level2.active > span.separator span.descck, div#" . $menuID . " li.maximenuck.maximenuflatlistck.active:not(.level1) span.descck,";
		} else {
			$level2active_li = "";
			$level2active_li_a = "";
			$level2active_titreck = "";
			$level2active_descck = "";
		}
		if (isset($css->level2itemhoverstyles)) {
			if ($css->level2itemhoverstyles['padding'] || $css->level2itemhoverstyles['margin'] || $css->level2itemhoverstyles['background'] || $css->level2itemhoverstyles['gradient'] || $css->level2itemhoverstyles['borderradius'] || $css->level2itemhoverstyles['shadow'] || $css->level2itemhoverstyles['border']
			) {
				$csstoinject .= $level2active_li . "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.level1 li.maximenuck:not(.headingck):hover, div#" . $menuID . " li.maximenuck.maximenuflatlistck:hover:not(.level1):not(.headingck):hover,
div#" . $menuID . " .maxipushdownck li.maximenuck:not(.headingck):hover { " . $css->level2itemhoverstyles['margin'] . $css->level2itemhoverstyles['background'] . $css->level2itemhoverstyles['gradient'] . $css->level2itemhoverstyles['borderradius'] . $css->level2itemhoverstyles['shadow'] . $css->level2itemhoverstyles['border'] . " } ";
				$csstoinject .= $level2active_li_a . "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.level1 li.maximenuck:not(.headingck):hover > a, div#" . $menuID . " li.maximenuck.maximenuflatlistck:hover:not(.level1):not(.headingck):hover > a,
div#" . $menuID . " .maxipushdownck li.maximenuck:not(.headingck):hover > a, div#" . $menuID . " ul.maximenuck li.maximenuck.level1 li.maximenuck:not(.headingck):hover > span.separator, div#" . $menuID . " li.maximenuck.maximenuflatlistck:hover:not(.level1):not(.headingck):hover > span.separator,
div#" . $menuID . " .maxipushdownck li.maximenuck:not(.headingck):hover > span.separator { " . $css->level2itemhoverstyles['padding'] . " } ";
			}
			if ($css->level2itemhoverstyles['fontcolor'] || $css->level2itemhoverstyles['fontsize'] || $css->level2itemhoverstyles['textshadow']
			) {
				$csstoinject .= $level2active_titreck . "\ndiv#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck:hover > a span.titreck, div#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck:hover > span.separator span.titreck, div#" . $menuID . " li.maximenuck.maximenuflatlistck:hover:not(.level1) span.titreck,
div#" . $menuID . " .maxipushdownck li.maximenuck:hover > a span.titreck, div#" . $menuID . " .maxipushdownck li.maximenuck:hover > span.separator span.titreck { " . $css->level2itemhoverstyles['fontcolor'] . $css->level2itemhoverstyles['fontsize'] . $css->level2itemhoverstyles['fontweight'] . $css->level2itemhoverstyles['textshadow'] . " } ";
			}
			if ($css->level2itemhoverstyles['descfontcolor'] || $css->level2itemhoverstyles['descfontsize']
			) {
				$csstoinject .= $level2active_descck . "\ndiv#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck:hover > a span.descck, div#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck:hover > span.separator span.descck, div#" . $menuID . " li.maximenuck.maximenuflatlistck:hover:not(.level1) span.descck,
div#" . $menuID . " .maxipushdownck li.maximenuck:hover > a span.descck, div#" . $menuID . " .maxipushdownck li.maximenuck:hover > span.separator span.descck { " . $css->level2itemhoverstyles['descfontcolor'] . $css->level2itemhoverstyles['descfontsize'] . " } ";
			}
		}

		if (isset($fields['level2itemactivestyles']) && $fields['level2itemactivestyles']->get('level2itemactivestylesidemhover') == '0') {
			// level2 active items styles
			if (isset($css->level2itemactivestyles)) {
				if ($css->level2itemactivestyles['padding'] || $css->level2itemactivestyles['margin'] || $css->level2itemactivestyles['background'] || $css->level2itemactivestyles['gradient'] || $css->level2itemactivestyles['borderradius'] || $css->level2itemactivestyles['shadow'] || $css->level2itemactivestyles['border']
				) {
					$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck.active:not(.headingck),
	div#" . $menuID . " .maxipushdownck li.maximenuck.active:not(.headingck) { " . $css->level2itemactivestyles['margin'] . $css->level2itemactivestyles['background'] . $css->level2itemactivestyles['gradient'] . $css->level2itemactivestyles['borderradius'] . $css->level2itemactivestyles['shadow'] . $css->level2itemactivestyles['border'] . " } ";
					$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck.active:not(.headingck) > a,
	div#" . $menuID . " .maxipushdownck li.maximenuck.active:not(.headingck) > a, div#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck.active:not(.headingck) > span.separator,
	div#" . $menuID . " .maxipushdownck li.maximenuck.active:not(.headingck) > span.separator { " . $css->level2itemactivestyles['padding'] . " } ";
				}
				if ($css->level2itemactivestyles['fontcolor'] || $css->level2itemactivestyles['fontsize'] || $css->level2itemactivestyles['textshadow']
				) {
					$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck.active > a span.titreck, div#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck.active > span.separator span.titreck,
	div#" . $menuID . " .maxipushdownck li.maximenuck.active > a span.titreck, div#" . $menuID . " .maxipushdownck li.maximenuck.active > span.separator span.titreck { " . $css->level2itemactivestyles['fontcolor'] . $css->level2itemactivestyles['fontsize'] . $css->level2itemactivestyles['fontweight'] . $css->level2itemactivestyles['textshadow'] . " } ";
				}
				if ($css->level2itemactivestyles['descfontcolor'] || $css->level2itemactivestyles['descfontsize']
				) {
					$csstoinject .= "\ndiv#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck.active > a span.descck, div#" . $menuCSSID . " li.maximenuck.level1 li.maximenuck.active > span.separator span.descck,
	div#" . $menuID . " .maxipushdownck li.maximenuck.active > a span.descck, div#" . $menuID . " .maxipushdownck li.maximenuck.active > span.separator span.descck { " . $css->level2itemactivestyles['descfontcolor'] . $css->level2itemactivestyles['descfontsize'] . " } ";
				}
			}
		}

		// heading items styles
		if (isset($css->headingstyles)) {
			$headingclass = '.separator';
			$padding = $css->headingstyles['padding'] ? trim($css->headingstyles['padding'], ";") . ";" : '';
			$margin = $css->headingstyles['margin'] ? trim($css->headingstyles['margin'], ";") . ";" : '';
			$background = $css->headingstyles['background'] ? trim($css->headingstyles['background'], ";") . ";" : '';
			$gradient = $css->headingstyles['gradient'] ? trim($css->headingstyles['gradient'], ";") . ";" : '';
			$borderradius = $css->headingstyles['borderradius'] ? trim($css->headingstyles['borderradius'], ";") . ";" : '';
			$shadow = $css->headingstyles['shadow'] ? trim($css->headingstyles['shadow'], ";") . ";" : '';
			$border = $css->headingstyles['border'] ? trim($css->headingstyles['border'], ";") . ";" : '';
			if ($padding || $margin || $background || $gradient || $borderradius || $shadow || $border || $css->headingstyles['text-align']) {
			$csstoinject .= "\ndiv#" . $menuCSSID . " ul.maximenuck2 li.maximenuck > " . $headingclass . ",
div#" . $menuID . " .maxipushdownck ul.maximenuck2 li.maximenuck > " . $headingclass . " { " . $padding . $margin . $background . $gradient . $borderradius . $shadow . $border . $css->headingstyles['text-align']. " } ";
			}
			if ($css->headingstyles['fontcolor'] || $css->headingstyles['fontsize'] || $css->headingstyles['fontweight'] || $css->headingstyles['textshadow']) {
			$csstoinject .= "\ndiv#" . $menuCSSID . " ul.maximenuck2 li.maximenuck > " . $headingclass . " span.titreck,
div#" . $menuID . " .maxipushdownck ul.maximenuck2 li.maximenuck > " . $headingclass . " span.titreck { " . $css->headingstyles['fontcolor'] . $css->headingstyles['fontsize'] . $css->headingstyles['fontweight'] . $css->headingstyles['textshadow'] . " } ";
			}
			if ($css->headingstyles['descfontcolor'] || $css->headingstyles['descfontsize']) {
			$csstoinject .= "\ndiv#" . $menuCSSID . " ul.maximenuck2 li.maximenuck > " . $headingclass . " span.descck,
div#" . $menuID . " .maxipushdownck ul.maximenuck2 li.maximenuck > " . $headingclass . " span.descck{ " . $css->headingstyles['descfontcolor'] . $css->headingstyles['descfontsize'] . " } ";
			}
		}

		return $csstoinject;
	}

	/**
	 * Extract the name of the google font from the url - For Ajax method only
	 * @param string $gfont the font url
	 *
	 * @return void (echo the string of the font name)
	 */ 
	static function clean_gfont_name($gfont) {
		// <link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>
		// Open+Sans+Condensed:300
		// Open Sans
		if ( preg_match( '/family=(.*?) /', $gfont . ' ', $matches) ) {
			if ( isset($matches[1]) ) {
				$gfont = $matches[1];
			}
		}

		$gfont = str_replace(' ', '+', ucwords (trim($gfont)));
		echo trim(trim($gfont, "'"));
		die;
	}

	/**
	 * Extract the css family name of the google font from the url
	 * @param string $gfont the font url
	 *
	 * @return string the font family
	 */
	static function get_gfontfamily($gfont) {
		// Open+Sans+Condensed:300
		if ( preg_match( '/(.*?):/', $gfont, $matches) ) {
			if ( isset($matches[1]) ) {
				$gfont = $matches[1];
			}
		}

		return ucwords(str_replace("+", " ", $gfont));
	}

	/**
	 * Test if there is already a unit, else add the px
	 *
	 * @param string $value
	 * @return string
	 */
	static function testUnit($value) {
		if ((stristr($value, 'px')) OR (stristr($value, 'em')) OR (stristr($value, '%')) OR (stristr($value, 'auto')) ) {
			return $value;
		}

		if ($value == '') {
			$value = 0;
		}

		return $value . 'px';
	}

	/**
	 * Convert a hexa decimal color code to its RGB equivalent
	 *
	 * @param string $hexStr (hexadecimal color value)
	 * @param boolean $returnAsString (if set true, returns the value separated by the separator character. Otherwise returns associative array)
	 * @param string $seperator (to separate RGB values. Applicable only if second parameter is true.)
	 * @return array or string (depending on second parameter. Returns False if invalid hex color value)
	 */
	static function hex2RGB($hexStr, $opacity) {
		if ($opacity > 1) $opacity = $opacity/100;
		$hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
		$rgbArray = array();
		if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
			$colorVal = hexdec($hexStr);
			$rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
			$rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
			$rgbArray['blue'] = 0xFF & $colorVal;
		} elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
			$rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
			$rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
			$rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
		} else {
			return false; //Invalid hex color code
		}
		$rgbacolor = "rgba(" . $rgbArray['red'] . "," . $rgbArray['green'] . "," . $rgbArray['blue'] . "," . $opacity . ")";

		return $rgbacolor;
	}

	/**
	 * Get base menu item.
	 *
	 * @param   JRegistry  &$params  The module options.
	 *
	 * @return   object
	 *
	 * @since	3.0.2
	 */
	public static function getBase(&$params)
	{
		// Get base menu item from parameters
		if ($params->get('base'))
		{
			$base = JFactory::getApplication()->getMenu()->getItem($params->get('base'));
		}
		else
		{
			$base = false;
		}

		// Use active menu item if no base found
		if (!$base)
		{
			$base = self::getActive($params);
		}

		return $base;
	}

	/**
	 * Get active menu item.
	 *
	 * @param   JRegistry  &$params  The module options.
	 *
	 * @return  object
	 *
	 * @since	3.0.2
	 */
	public static function getActive(&$params)
	{
		$menu = JFactory::getApplication()->getMenu();

		return $menu->getActive() ? $menu->getActive() : $menu->getDefault();
	}
	
	/**
	 * Get the css from the theme php file and write them into a css file.
	 *
	 * @param   string  $filetocompile  The path to the theme php file.
	 * @param   JRegistry  &$params  The module options.
	 *
	 * @return  true on success
	 *
	 */
	public static function getCompiledCss($params) {
		$theme = $params->get('theme', 'default');
		$phpcss = file_get_contents(dirname(__FILE__) . '/themes/' . $theme . '/css/maximenuck.php');
		$menuID = $params->get('menuid', '');
		$css = str_replace('<?php echo $id; ?>', $menuID, $phpcss);
		$pattern = '/<\?php\s[^>]*[^>]*(.*)\?>/iUs';
		$replacement = '';
		$css = preg_replace($pattern, $replacement, $css);

		// add the menu items css
		if (self::$_modulecss) {
			$css .= '
				
/*---------------------------------------------
---	 Module styles from Maximenu Params     ---
----------------------------------------------*/
';
			$css .= str_replace(array(";", "{"), array(";\n\t", "{\n\t"), self::$_modulecss); // add new line and tab for reading purpose
		}

		// add the menu items css
		if (self::$_itemcss) {
			$css .= '
				
/*---------------------------------------------
---	 Menu items	styles from Maximenu Params ---
----------------------------------------------*/
';
			$css .= str_replace(array(";", "{"), array(";\n\t", "{\n\t"), self::$_itemcss); // add new line and tab for reading purpose
		}
		$cssfile = dirname(__FILE__) . '/themes/custom/css/maximenuck_' . $menuID . '.css';
		if (! JFolder::exists(dirname(__FILE__) . '/themes/custom/css/')) {
			JFolder::create(dirname(__FILE__) . '/themes/custom/css/');
		}
		return JFile::write($cssfile, $css);
	}

}

// create a new class to manage objects
if (!class_exists('CkCssParams')) {

	class CkCssParams extends stdClass {

		function get($key) {
			return isset($this->$key) ? $this->$key : null;
		}
		
		function exists($key) {
			return isset($this->$key) ? true : false;
		}

	}

}
