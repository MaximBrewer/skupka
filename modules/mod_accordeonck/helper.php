<?php

/**
 * @copyright	Copyright (C) 2011 Cédric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Accordeon CK
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');

class modAccordeonckHelper {

	private static $_itemcss;

	private static $_modulecss;

	static function GetItems(&$params, $module) {
		// Initialise variables.
		$list = array();
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		$menuID = $params->get('tag_id', 'accordeonck' . $module->id);

		// If no active menu, use default
		$active = ($menu->getActive()) ? $menu->getActive() : $menu->getDefault();
		$active_id = isset($active) ? $active->id : $menu->getDefault()->id;
		$base = self::getBase($params);

		$path = is_array($base->tree) ? $base->tree : array(); // pb si on utilise cela, lorsque base item est sur autre que courant, ça ne marche plus. Le path renvoit uniquement le lien base item
		$pathActive = isset($active) ? $active->tree : $path;
		$start = (int) $params->get('startLevel');
		$end = (int) $params->get('endLevel');
		$showAll = 1;
		$maxdepth = $params->get('maxdepth');
		$items = $menu->getItems('menutype', $params->get('menutype'));
		$hidden_parents = array();

		$lastitem = 0;

		if ($items) {
			// load the list of all published modules
			$modulesList = modAccordeonckHelper::CreateModulesList();

			foreach ($items as $i => $item) {

				if (($start && $start > $item->level) 
					|| ($end && $item->level > $end) 
					|| (!$showAll && $item->level > 1 
						&& (!in_array($item->parent_id, $path) && !in_array($item->parent_id, $pathActive))
					)
						//|| ($maxdepth && $item->level > $maxdepth)
					|| ($start > 1 && !in_array($item->tree[$start - 2], $path)
						&& $start > 1 && !in_array($item->tree[$start - 2], $pathActive))
				) {
					unset($items[$i]);
					continue;
				}

				// Exclude item with menu item option set to exclude from menu modules
				if (($item->params->get('menu_show', 1) == 0) || in_array($item->parent_id, $hidden_parents))
				{
					$hidden_parents[] = $item->id;
					unset($items[$i]);
					continue;
				}

				$item->deeper = false;
				$item->shallower = false;
				$item->level_diff = 0;
				$item->isactive = false;

				if (isset($items[$lastitem])) {
					$items[$lastitem]->deeper = ($item->level > $items[$lastitem]->level);
					$items[$lastitem]->shallower = ($item->level < $items[$lastitem]->level);
					$items[$lastitem]->level_diff = ($items[$lastitem]->level - $item->level);
				}

				$item->parent = (boolean) $menu->getItems('parent_id', (int) $item->id, true);

				$lastitem = $i;
				$item->active = false;
				$item->flink = $item->link;

				switch ($item->type) {
					case 'separator':
						// No further action needed.
						continue;

					case 'url':
						if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false)) {
							// If this is an internal Joomla link, ensure the Itemid is set.
							$item->flink = $item->link . '&Itemid=' . $item->id;
						}
						break;

					case 'alias':
						// If this is an alias use the item id stored in the parameters to make the link.
						$item->flink = 'index.php?Itemid=' . $item->params->get('aliasoptions');
						break;

					default:
						$router = $app::getRouter();
						if ($router->getMode() == JROUTER_MODE_SEF) {
							$item->flink = 'index.php?Itemid=' . $item->id;
						} else {
							$item->flink .= '&Itemid=' . $item->id;
						}
						break;
				}

				if (strcasecmp(substr($item->flink, 0, 4), 'http') && (strpos($item->flink, 'index.php?') !== false)) {
					$item->flink = JRoute::_($item->flink, true, $item->params->get('secure'));
				} else {
					$item->flink = JRoute::_($item->flink);
				}

				$item->ftitle = htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8', false);
				$item->anchor_css = htmlspecialchars($item->params->get('menu-anchor_css', ''), ENT_COMPAT, 'UTF-8', false);
				$item->anchor_title = htmlspecialchars($item->params->get('menu-anchor_title', ''), ENT_COMPAT, 'UTF-8', false);
				$item->menu_image = $item->params->get('menu_image', '') ? htmlspecialchars($item->params->get('menu_image', ''), ENT_COMPAT, 'UTF-8', false) : '';

				// manage plugin parameters, need the plugin maximenu_ck_params to be installed and active
				//$item->description = $item->params->get('accordeonck_desc', '');
				$item->insertmodule = $item->params->get('accordeonckparams_insertmodule', 0);
				$item->module = $item->params->get('accordeonckparams_module', '');
				$item->content = '';

				// manage description
				$titreCK = explode("||", $item->ftitle);
				if (isset($titreCK[1])) {
					$item->desc = $titreCK[1];
				} else {
					$item->desc = '';
				}
				$item->ftitle = $titreCK[0];
				$item->desc = $item->params->get('accordeonckparams_desc', '') ? $item->params->get('accordeonckparams_desc', '') : $item->desc;
				if ($item->desc) {
					$item->desc = '<span class="accordeonckdesc">' . $item->desc . '</span>';
				}

				// manage rel attribute
				$item->rel = '';
				if ($rel = $item->params->get('accordeonckparams_relattr', '')) {
					$item->rel = ' rel="' . $rel . '"';
				} elseif (preg_match('/\[rel=([a-z]+)\]/i', $item->ftitle, $resultat)) {
					$item->ftitle = preg_replace('/\[rel=[a-z]+\]/i', '', $item->ftitle);
					$item->rel = ' rel="' . $resultat[1] . '"';
				}

				// add the anchor tag
				$item->flink .= $item->params->get('accordeonckparams_anchor', '') ? '#' . $item->params->get('accordeonckparams_anchor', '') : '';

				// manage module
				if ($item->insertmodule AND $item->module) {
					$item->content = '<div class="accordeonckmod">' . modAccordeonckHelper::GenModuleById($item->module, $params, $modulesList) . '<div style="clear:both;"></div></div>';
				} else if (stristr($item->ftitle, '[modid=')) {
					preg_match('/\[modid=([0-9]+)\]/', $item->ftitle, $resultat);
					$item->ftitle = preg_replace('/\[modid=[0-9]+\]/', '', $item->ftitle);
					$item->content = '<div class="accordeonckmod">' . modAccordeonckHelper::GenModuleById($resultat[1], $params, $modulesList) . '<div style="clear:both;"></div></div>';
				}

				// manage item class
				$item->classe = 'item-' . $item->id;
				if ($item->id == $active_id) {
					$item->classe .= ' current';
				}

				// if (in_array($item->id, $path)) {
					// $item->classe .= ' active';
					// $item->isactive = true;
				// } elseif ($item->type == 'alias') {
					// $aliasToId = $item->params->get('aliasoptions');
					// if (count($path) > 0 && $aliasToId == $path[count($path) - 1]) {
						// $item->classe .= ' active';
						// $item->isactive = true;
					// } elseif (in_array($aliasToId, $path)) {
						// $item->classe .= ' alias-parent-active active';
						// $item->isactive = true;
					// }
				// }
				
				// new method for the active class
					if (	$item->type == 'alias' &&
						in_array($item->params->get('aliasoptions'),$path)
					||	in_array($item->id, $path)
					||	in_array($item->id, $pathActive)
							) {
							$item->classe .= ' active';
							$item->isactive = true;
				}

				// set the item styles if the plugin is enabled
				if (JPluginHelper::isEnabled('system', 'accordeonckparams')) {
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

				// get plugin parameters that are used directly in the layout
				$item->liclass = $item->params->get('accordeonckparams_liclass', '');
			}

			if (isset($items[$lastitem])) {
				$items[$lastitem]->deeper = (($start ? $start : 1) > $items[$lastitem]->level);
				$items[$lastitem]->shallower = (($start ? $start : 1) < $items[$lastitem]->level);
				$items[$lastitem]->level_diff = ($items[$lastitem]->level - ($start ? $start : 1));
			}
		}

		return $items;
	}

	static function GenModuleById($title, &$params, &$modulesList) {
		$attribs['style'] = 'none';

		if (!isset($modulesList[$title]))
			return "<p>No module found !</p>";
		$modtitle = $modulesList[$title]->title;
		$modname = $modulesList[$title]->module;
		//$modname = preg_replace('/mod_/', '', $modname);
		// load the module
		if (JModuleHelper::isEnabled($modname)) {
			$module = JModuleHelper::getModule($modname, $modtitle);
			if ($module) {
				return JModuleHelper::renderModule($module, $attribs);
			}
		}

		return "<p>No module found !</p>";
	}

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

	static function createCss($menuID, $params, $prefix = 'menu', $important = false, $itemid = '') {

		$css = Array();
		$important = ($important == true ) ? ' !important' : '';
		// test padding for legacy purpose
		if ( $params->exists($prefix . 'usemargin') && $params->get($prefix . 'usemargin') == '1' && $params->get('usestyles', '1') == '1' ) {
			$csspadding = ($params->get($prefix . 'padding') != '') ? 'padding: ' . self::testUnit($params->get($prefix . 'padding', '0')) . $important . ';' : '';
		} else {
			$csspadding = '';
		}
		$csspaddingtop = ($params->get($prefix . 'paddingtop') != '') ? 'padding-top: ' . self::testUnit($params->get($prefix . 'paddingtop', '0')) . $important . ';' : $csspadding;
		$csspaddingright = ($params->get($prefix . 'paddingright') != '') ? 'padding-right: ' . self::testUnit($params->get($prefix . 'paddingright', '0')) . $important . ';' : $csspadding;
		$csspaddingbottom = ($params->get($prefix . 'paddingbottom') != '') ? 'padding-bottom: ' . self::testUnit($params->get($prefix . 'paddingbottom', '0')) . $important . ';' : $csspadding;
		$csspaddingleft = ($params->get($prefix . 'paddingleft') != '') ? 'padding-left: ' . self::testUnit($params->get($prefix . 'paddingleft', '0')) . $important . ';' : $csspadding;
		$css['padding'] = $csspaddingtop . $csspaddingright . $csspaddingbottom . $csspaddingleft;
		
		// test margin for legacy purpose
		if ( $params->exists($prefix . 'usemargin') && $params->get($prefix . 'usemargin') == '1' && $params->get('usestyles', '1') == '1' ) {
			$cssmargin = ($params->get($prefix . 'margin') != '') ? 'margin: ' . self::testUnit($params->get($prefix . 'margin', '0')) . $important . ';' : '';
		} else {
			$cssmargin = '';
		}
		$cssmargintop = ($params->get($prefix . 'margintop') != '') ? 'margin-top: ' . self::testUnit($params->get($prefix . 'margintop', '0')) . $important . ';' : $cssmargin;
		$cssmarginright = ($params->get($prefix . 'marginright') != '') ? 'margin-right: ' . self::testUnit($params->get($prefix . 'marginright', '0')) . $important . ';' : $cssmargin;
		$cssmarginbottom = ($params->get($prefix . 'marginbottom') != '') ? 'margin-bottom: ' . self::testUnit($params->get($prefix . 'marginbottom', '0')) . $important . ';' : $cssmargin;
		$cssmarginleft = ($params->get($prefix . 'marginleft') != '') ? 'margin-left: ' . self::testUnit($params->get($prefix . 'marginleft', '0')) . $important . ';' : $cssmargin;
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
		/*if ($use_svggradient) {
			$svggradientfile = '';
			if ($css['background'] AND $params->get($prefix . 'bgcolor2')) {
				$svggradientfile = self::createSvgGradient($menuID, $prefix . $itemid, $params->get($prefix . 'bgcolor1', ''), $params->get($prefix . 'bgcolor2', ''));
			}
			$svggradient = $svggradientfile ? "background-image: url(\"" . $svggradientfile . "\")" . $important . ";" : "";
		}*/
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
		$bordercolor = $params->get($prefix . 'bordercolor', '') ? $params->get($prefix . 'bordercolor', '') : '';
		$bordertopcolor = $params->get($prefix . 'bordertopcolor', '') ? $params->get($prefix . 'bordertopcolor', '') : $bordercolor;
		$borderrightcolor = $params->get($prefix . 'borderrightcolor', '') ? $params->get($prefix . 'borderrightcolor', '') : $bordercolor;
		$borderbottomcolor = $params->get($prefix . 'borderbottomcolor', '') ? $params->get($prefix . 'borderbottomcolor', '') : $bordercolor;
		$borderleftcolor = $params->get($prefix . 'borderleftcolor', '') ? $params->get($prefix . 'borderleftcolor', '') : $bordercolor;

		$css['border'] = (($params->get($prefix . 'bordertopwidth') == '0') ? 'border-top: none' . $important . ';' : (($params->get($prefix . 'bordertopwidth') != '' AND $bordertopcolor) ? 'border-top: ' . $bordertopcolor . ' ' . self::testUnit($params->get($prefix . 'bordertopwidth', '')) . ' ' . $bordertopstyle . ' ' . $important . ';' : '') )
				. (($params->get($prefix . 'borderrightwidth') == '0') ? 'border-right: none' . $important . ';' : (($params->get($prefix . 'borderrightwidth') != '' AND $borderrightcolor) ? 'border-right: ' . $borderrightcolor . ' ' . self::testUnit($params->get($prefix . 'borderrightwidth', '')) . ' ' . $borderrightstyle . ' ' . $important . ';' : '') )
				. (($params->get($prefix . 'borderbottomwidth') == '0') ? 'border-bottom: none' . $important . ';' : (($params->get($prefix . 'borderbottomwidth') != '' AND $borderbottomcolor) ? 'border-bottom: ' . $borderbottomcolor . ' ' . self::testUnit($params->get($prefix . 'borderbottomwidth', '')) . ' ' . $borderbottomstyle . ' ' . $important . ';' : '') )
				. (($params->get($prefix . 'borderleftwidth') == '0') ? 'border-left: none' . $important . ';' : (($params->get($prefix . 'borderleftwidth') != '' AND $borderleftcolor) ? 'border-left: ' . $borderleftcolor . ' ' . self::testUnit($params->get($prefix . 'borderleftwidth', '')) . ' ' . $borderleftstyle . ' ' . $important . ';' : '') );
		$css['fontsize'] = ($params->get($prefix . 'fontsize') != '') ?
				'font-size: ' . self::testUnit($params->get($prefix . 'fontsize')) . $important . ';' : '';
		$css['fontcolor'] = ($params->get($prefix . 'fontcolor') != '') ?
				'color: ' . $params->get($prefix . 'fontcolor') . $important . ';' : '';
		$css['fontweight'] = ($params->get($prefix . 'fontweight')  == 'bold') ?
				'font-weight: ' . $params->get($prefix . 'fontweight') . $important . ';' : '';

		$fontfamily = $params->get($prefix . 'fontfamily');
		// load the google font
		$isGfont = $params->get($prefix . 'textisgfont', '1');
//		$css['gfont'] = '';
		if ($isGfont && $fontfamily) {
			$fontfamily = self::get_gfontfamily($fontfamily);
//			$css['gfont'] = '@import url(https://fonts.googleapis.com/css?family=' . $gfont . ');';
//			$css['gfont'] = 'https://fonts.googleapis.com/css?family=' . $fontfamily;
			$document = JFactory::getDocument();
			$document->addStylesheet('https://fonts.googleapis.com/css?family=' . $fontfamily);
		}

		$css['fontfamily'] = ($fontfamily  != '') ?
				'font-family: ' . ($fontfamily) . $important . ';' : '';
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

		self::retrocompatibility_css($css, $params, $prefix);
		return $css;
	}

	static function retrocompatibility_css(& $css, $params, $prefix) {
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

		$css['fontcolorhover'] = ($params->get($prefix . 'usefont') AND $params->get($prefix . 'fontcolorhover')) ?
				'color: ' . $params->get($prefix . 'fontcolorhover') . ';' : '';

	}

	/*
	 * Method to inject the CSS for a specific Itemid with the plugin params
	 */

	static function injectItemCss($item, $menuID, $params) {
		$itemcss = self::createCss($menuID, $item->params, 'accordeonckparams_link');
		$itemcsshover = self::createCss($menuID, $item->params, 'accordeonckparams_linkhover');
		$itemcssactive = self::createCss($menuID, $item->params, 'accordeonckparams_linkactive');
		$css = '';

		// normal state
		if ($itemcss['padding'] || $itemcss['margin'] || $itemcss['background'] || $itemcss['borderradius'] || $itemcss['shadow'] || $itemcss['border'])
			$css .= "#" . $menuID . " li#item-" . $item->id . " { " . $itemcss['padding'] . $itemcss['margin'] . $itemcss['background'] . $itemcss['gradient'] . $itemcss['borderradius'] . $itemcss['shadow'] . $itemcss['border'] . " }\n";
		if ($itemcss['fontcolor'] || $itemcss['fontsize'])
			$css .= "#" . $menuID . " li#item-" . $item->id . " > a { " . $itemcss['fontcolor'] . $itemcss['fontsize'] . " }\n";
		if ($itemcss['descfontcolor'] || $itemcss['descfontsize'])
			$css .= "#" . $menuID . " li#item-" . $item->id . " > a span.accordeonckdesc { " . $itemcss['descfontcolor'] . $itemcss['descfontsize'] . " }\n";

		// hover state
		if ($itemcsshover['padding'] || $itemcsshover['margin'] || $itemcsshover['background'] || $itemcsshover['borderradius'] || $itemcsshover['shadow'] || $itemcsshover['border'])
			$css .= "#" . $menuID . " li#item-" . $item->id . ":hover { " . $itemcsshover['padding'] . $itemcsshover['margin'] . $itemcsshover['background'] . $itemcsshover['gradient'] . $itemcsshover['borderradius'] . $itemcsshover['shadow'] . $itemcsshover['border'] . " }\n";
		if ($itemcsshover['fontcolor'] || $itemcsshover['fontsize'])
			$css .= "#" . $menuID . " li#item-" . $item->id . ":hover > a { " . $itemcsshover['fontcolor'] . $itemcsshover['fontsize'] . " }\n";
		if ($itemcsshover['descfontcolor'] || $itemcsshover['descfontsize'])
			$css .= "#" . $menuID . " li#item-" . $item->id . ":hover > a span.accordeonckdesc { " . $itemcsshover['descfontcolor'] . $itemcsshover['descfontsize'] . " }\n";

		// active state
		if ($itemcssactive['padding'] || $itemcssactive['margin'] || $itemcssactive['background'] || $itemcssactive['borderradius'] || $itemcssactive['shadow'] || $itemcssactive['border'])
			$css .= "#" . $menuID . " li#item-" . $item->id . ".active { " . $itemcssactive['padding'] . $itemcssactive['margin'] . $itemcssactive['background'] . $itemcssactive['gradient'] . $itemcssactive['borderradius'] . $itemcssactive['shadow'] . $itemcssactive['border'] . " }\n";
		if ($itemcssactive['fontcolor'] || $itemcssactive['fontsize'])
			$css .= "#" . $menuID . " li#item-" . $item->id . ".active > a { " . $itemcssactive['fontcolor'] . $itemcssactive['fontsize'] . " }\n";
		if ($itemcssactive['descfontcolor'] || $itemcssactive['descfontsize'])
			$css .= "#" . $menuID . " li#item-" . $item->id . ".active > a span.accordeonckdesc { " . $itemcssactive['descfontcolor'] . $itemcssactive['descfontsize'] . " }\n";

		// inject the css in the page
		if ($css) {
			$document = JFactory::getDocument();
			$document->addStyleDeclaration($css);
		}
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
	
	/**
	 * load the css properties for the module, legacy mode that works with the module styles (not customizer)
	 * @param JRegistry $params
	 * @param string $menuID the module ID
	 *
	 * @return void
	 */
	static function injectLegacyModuleCss($params, $menuID) {
		if ($params->get('doCompile') || $params->get('loadcompiledcss', '0') == '0') {
				$theme = $params->get('theme', 'default');
				$imageplus = $params->get('imageplus', 'modules/mod_accordeonck/assets/plus.png');
				$imageminus = $params->get('imageminus', 'modules/mod_accordeonck/assets/minus.png');
				$imageposition = $params->get('imageposition', 'right');
				$document = JFactory::getDocument();
				$menucss = modAccordeonckHelper::createCss($menuID, $params, 'menu');
				$level1linkcss = modAccordeonckHelper::createCss($menuID, $params, 'level1link');
				$level2linkcss = modAccordeonckHelper::createCss($menuID, $params, 'level2link');
				$level3linkcss = modAccordeonckHelper::createCss($menuID, $params, 'level3link');

				$css = '';
				if ($params->get('useplusminusimages', '1')) {
					$css .= "\n#" . $menuID . " li > .accordeonck_outer .toggler_icon  { outline: none;background: url(" . JURI::base(true) . '/' . $imageplus . ") " . $imageposition . " center no-repeat !important; }";
					$css .= "\n#" . $menuID . " li.open > .accordeonck_outer .toggler_icon { background: url(" . JURI::base(true) . '/' . $imageminus . ") " . $imageposition . " center no-repeat !important; }";
				}
				$css .= "\n#" . $menuID . " li ul li ul li ul { border:none !important; padding-top:0px !important; padding-bottom:0px !important; }";

				// $css .= "\n#" . $menuID . " ul[class^=\"content\"] { margin:0;padding:0; }";
				$css .= "\n#" . $menuID . " { " . implode('', $menucss) . " } ";
				$css .= "\n#" . $menuID . " li.parent > span { display: block; position: relative; }";
				$css .= "\n#" . $menuID . " li.parent > span span.toggler_icon { position: absolute; cursor: pointer; display: block; height: 100%; z-index: 10;" . $imageposition . ":0; background: none;width: 20px;}";
				// first level items
				$css .= "\n#" . $menuID . " li.level1 { " . $level1linkcss['padding'] . $level1linkcss['margin'] . $level1linkcss['background'] . $level1linkcss['gradient'] . $level1linkcss['borderradius'] . $level1linkcss['shadow'] . $level1linkcss['border'] . " } ";
				$css .= "\n#" . $menuID . " li.level1 > .accordeonck_outer a { " . $level1linkcss['fontcolor'] . $level1linkcss['fontsize'] . " } ";
				$css .= "\n#" . $menuID . " li.level1 > .accordeonck_outer a:hover { " . $level1linkcss['fontcolorhover'] . " } ";
				$css .= "\n#" . $menuID . " li.level1 > .accordeonck_outer a span.accordeonckdesc { " . $level1linkcss['descfontcolor'] . $level1linkcss['descfontsize'] . " } ";
				// second level items
				$css .= "\n#" . $menuID . " li.level2 { " . $level2linkcss['padding'] . $level2linkcss['margin'] . $level2linkcss['background'] . $level2linkcss['gradient'] . $level2linkcss['borderradius'] . $level2linkcss['shadow'] . $level2linkcss['border'] . " } ";
				$css .= "\n#" . $menuID . " li.level2 > .accordeonck_outer a { " . $level2linkcss['fontcolor'] . $level2linkcss['fontsize'] . " } ";
				$css .= "\n#" . $menuID . " li.level2 > .accordeonck_outer a:hover { " . $level2linkcss['fontcolorhover'] . " } ";
				$css .= "\n#" . $menuID . " li.level2 > .accordeonck_outer a span.accordeonckdesc { " . $level2linkcss['descfontcolor'] . $level2linkcss['descfontsize'] . " } ";
				// third and more level items
				$css .= "\n#" . $menuID . " li.level3 { " . $level3linkcss['padding'] . $level3linkcss['margin'] . $level3linkcss['background'] . $level3linkcss['gradient'] . $level3linkcss['borderradius'] . $level3linkcss['shadow'] . $level3linkcss['border'] . " } ";
				$css .= "\n#" . $menuID . " li.level3 .accordeonck_outer a { " . $level3linkcss['fontcolor'] . $level3linkcss['fontsize'] . " } ";
				$css .= "\n#" . $menuID . " li.level3 .accordeonck_outer a:hover { " . $level3linkcss['fontcolorhover'] . " } ";
				$css .= "\n#" . $menuID . " li.level3 .accordeonck_outer a span.accordeonckdesc { " . $level3linkcss['descfontcolor'] . $level3linkcss['descfontsize'] . " } ";
			if ($params->get('loadcompiledcss', '0') == '0') {
				$document->addStyleDeclaration($css);
				$document->addStylesheet(JURI::base(true) . '/modules/mod_accordeonck/themes/' . $theme . '/mod_accordeonck_css.php?cssid=' . $menuID);
			} else {
				self::$_modulecss .= $css;
			}
		}
	}

	static function createModuleCss($params, $menuID) {
		$document = JFactory::getDocument();

		// set the prefixes for all xml fieldset
		$prefixes = array(
			'menustyles',
			'level1itemgroup',
			'level1itemnormalstyles',
			'level1itemhoverstyles',
			'level1itemactivestyles',
			'level2menustyles',
			'level2itemgroup',
			'level2itemnormalstyles',
			'level2itemhoverstyles',
			'level2itemactivestyles',
			'level1itemnormalstylesicon',
			'level1itemhoverstylesicon',
			'level2itemnormalstylesicon',
			'level2itemhoverstylesicon',
			'level3menustyles',
			'level3itemgroup',
			'level3itemnormalstyles',
			'level3itemhoverstyles',
			'level3itemactivestyles',
			'level1itemnormalstylesicon',
			'level1itemhoverstylesicon',
			'level3itemnormalstylesicon',
			'level3itemhoverstylesicon',
//			'headingstyles'
			);

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
			$css->$prefix = modAccordeonckHelper::createCss($menuID, $fields[$prefix], $prefix, $important, '');
		}

		// root styles
		if (isset($css->menustyles)) {
//			if ($menupadding || $menumargin || $css->menustyles['background'] || $css->menustyles['gradient'] || $css->menustyles['borderradius'] || $css->menustyles['shadow'] || $css->menustyles['border'] || $css->menustyles['text-align']
//			) {
				$csstoinject .= "\n#" . $menuID . " { padding:0;margin:0;" . $css->menustyles['padding'] . $css->menustyles['margin'] . $css->menustyles['background'] . $css->menustyles['gradient'] . $css->menustyles['borderradius'] . $css->menustyles['shadow'] . $css->menustyles['border'] . $css->menustyles['text-align'] . " } ";
//			}
		}

		$imageposition = isset($fields['menustyles']->menustylesparentarrowposition) ? $fields['menustyles']->menustylesparentarrowposition : 'right';
		
		// level 1
		$level1imageplus = isset($fields['menustyles']->menustylesimageplus) ? $fields['menustyles']->menustylesimageplus : '';
		$level1imageminus = isset($fields['menustyles']->menustylesimageminus) ? $fields['menustyles']->menustylesimageminus : '';
		$level1imagepadding = isset($fields['menustyles']->menustylesparentarrowwidth) && $fields['menustyles']->menustylesparentarrowwidth ? 'padding-' . $imageposition . ': ' . self::testUnit($fields['menustyles']->menustylesparentarrowwidth) . ';' : '';
		$level1imagewidth = isset($fields['menustyles']->menustylesparentarrowwidth) && $fields['menustyles']->menustylesparentarrowwidth ? 'width: ' . self::testUnit($fields['menustyles']->menustylesparentarrowwidth) . ';' : '';

		// level 2
		$level2imageplus = isset($fields['level2menustyles']->level2menustylesimageplus) ? $fields['level2menustyles']->level2menustylesimageplus : '';
		$level2imageminus = isset($fields['level2menustyles']->level2menustylesimageminus)  ?$fields['level2menustyles']->level2menustylesimageminus : '';
		$level2imagepadding = isset($fields['level2menustyles']->level2menustylesparentarrowwidth) ? 'padding-' . $imageposition . ': ' . self::testUnit($fields['level2menustyles']->level2menustylesparentarrowwidth) . ';' : '';
		$level2imagewidth = isset($fields['level2menustyles']->level2menustylesparentarrowwidth) ? 'width: ' . self::testUnit($fields['level2menustyles']->level2menustylesparentarrowwidth) . ';' : '';

		// level 3
		$level3imageplus = isset($fields['level3menustyles']->level3menustylesimageplus) ? $fields['level3menustyles']->level3menustylesimageplus : '';
		$level3imageminus = isset($fields['level3menustyles']->level3menustylesimageminus) ? $fields['level3menustyles']->level3menustylesimageminus : '';
		$level3imagepadding = isset($fields['level3menustyles']->level3menustylesparentarrowwidth) ? 'padding-' . $imageposition . ': ' . self::testUnit($fields['level3menustyles']->level3menustylesparentarrowwidth) . ';' : '';
		$level3imagewidth = isset($fields['level3menustyles']->level3menustylesparentarrowwidth) ? 'width: ' . self::testUnit($fields['level3menustyles']->level3menustylesparentarrowwidth) . ';' : '';

		// base styles
		$csstoinject .= "\n#" . $menuID . " li.accordeonck { list-style: none;overflow: hidden; }";
		$csstoinject .= "\n#" . $menuID . " ul[class^=\"content\"] { margin:0;padding:0; }";
		$csstoinject .= "\n#" . $menuID . " li.accordeonck > span { position: relative; display: block; " . $css->menustyles['fontfamily'] . "}";
		$csstoinject .= "\n#" . $menuID . " li.accordeonck.parent > span { " . $level1imagepadding . "}";
		$csstoinject .= "\n#" . $menuID . " li.parent > span span.toggler_icon { position: absolute; cursor: pointer; display: block; height: 100%; z-index: 10;" . $imageposition . ":0; background: url(" . JURI::root(true) . '/' . $level1imageplus . ") center center no-repeat !important;" . $level1imagewidth . "}";
		$csstoinject .= "\n#" . $menuID . " li.parent.open > span span.toggler_icon { " . $imageposition . ":0; background: url(" . JURI::root(true) . '/' . $level1imageminus . ") center center no-repeat !important;}";
		$csstoinject .= "\n#" . $menuID . " li.accordeonck.level2 > span { " . $level2imagepadding . "}";
		$csstoinject .= "\n#" . $menuID . " li.level3 li.accordeonck > span { " . $level3imagepadding . "}";
		$csstoinject .= "\n#" . $menuID . " a.accordeonck { display: block;text-decoration: none; " . $css->level1itemnormalstyles['text-align'] . $css->level1itemnormalstyles['fontcolor'] . $css->level1itemnormalstyles['fontsize'] . $css->level1itemnormalstyles['fontweight'] . "}";
		$csstoinject .= "\n#" . $menuID . " a.accordeonck:hover { text-decoration: none; " . $css->level1itemhoverstyles['fontcolor'] . "}";
		$csstoinject .= "\n#" . $menuID . " li.parent > span a { display: block;outline: none; }";
		$csstoinject .= "\n#" . $menuID . " li.parent.open > span a {  }";
		if ($level2imageplus) $csstoinject .= "\n#" . $menuID . " li.level2.parent > span a { display: block;outline: none;background: url(" . JURI::root(true) . '/' . $level2imageplus . ") " . $imageposition . " center no-repeat !important; " . $level12magewidth . "}";
		if ($level2imageminus) $csstoinject .= "\n#" . $menuID . " li.level2.parent.open > span a { background: url(" . JURI::root(true) . '/' . $level2imageminus . ") " . $imageposition . " center no-repeat !important; }";
		if ($level3imageplus) $csstoinject .= "\n#" . $menuID . " li.level2 li.accordeonck.parent > span a { display: block;outline: none;background: url(" . JURI::root(true) . '/' . $level3imageplus . ") " . $imageposition . " center no-repeat !important; " . $level3imagewidth . "}";
		if ($level3imageminus) $csstoinject .= "\n#" . $menuID . " li.level2 li.accordeonck.open.parent > span a { background: url(" . JURI::root(true) . '/' . $level3imageminus . ") " . $imageposition . " center no-repeat !important; }";

		// level 1 styles
		if (isset($css->level1itemgroup)) {
			if ($css->level1itemgroup['padding'] || $css->level1itemgroup['margin'] || $css->level1itemgroup['background'] || $css->level1itemgroup['gradient'] || $css->level1itemgroup['borderradius'] || $css->level1itemgroup['shadow'] || $css->level1itemgroup['border']
					) {
				$csstoinject .= "\n#" . $menuID . " li.level1 { " . $css->level1itemgroup['padding'] . $css->level1itemgroup['margin'] . $css->level1itemgroup['background'] . $css->level1itemgroup['gradient'] . $css->level1itemgroup['borderradius'] . $css->level1itemgroup['shadow'] . $css->level1itemgroup['border'] . " } ";
			}
		}

		// level 1 styles
		if (isset($css->level1itemnormalstyles)) {
			if ($css->level1itemnormalstyles['padding'] || $css->level1itemnormalstyles['margin'] || $css->level1itemnormalstyles['background'] || $css->level1itemnormalstyles['gradient'] || $css->level1itemnormalstyles['borderradius'] || $css->level1itemnormalstyles['shadow'] || $css->level1itemnormalstyles['border'] || $css->level1itemnormalstyles['text-align']
				|| $css->level1itemnormalstyles['fontfamily'] || $css->level1itemnormalstyles['fontcolor'] || $css->level1itemnormalstyles['fontweight'] || $css->level1itemnormalstyles['fontsize'] || $css->level1itemnormalstyles['textshadow'] || $css->level1itemnormalstyles['text-transform']
					) {
				$csstoinject .= "\n#" . $menuID . " li.level1 > span { " . $css->level1itemnormalstyles['margin'] . $css->level1itemnormalstyles['background'] . $css->level1itemnormalstyles['gradient'] . $css->level1itemnormalstyles['borderradius'] . $css->level1itemnormalstyles['shadow'] . $css->level1itemnormalstyles['border'] . " } ";
				$csstoinject .= "\n#" . $menuID . " li.level1 > span a { " . $css->level1itemnormalstyles['padding'] . $css->level1itemnormalstyles['fontcolor'] . $css->level1itemnormalstyles['fontsize'] . $css->level1itemnormalstyles['textshadow'] . $css->level1itemnormalstyles['text-align'] . $css->level1itemnormalstyles['text-transform'] . $css->level1itemnormalstyles['fontweight'] . " } ";
			}
			if ($css->level1itemnormalstyles['descfontcolor'] || $css->level1itemnormalstyles['descfontsize']
			) {
				$csstoinject .= "\n#" . $menuID . " li.level1 > span span.accordeonckdesc { " . $css->level1itemnormalstyles['descfontcolor'] . $css->level1itemnormalstyles['descfontsize'] . " } ";
			}
		}

		// level 1 styles
		if (isset($css->level1itemhoverstyles)) {
			if ($css->level1itemhoverstyles['padding'] || $css->level1itemhoverstyles['margin'] || $css->level1itemhoverstyles['background'] || $css->level1itemhoverstyles['gradient'] || $css->level1itemhoverstyles['borderradius'] || $css->level1itemhoverstyles['shadow'] || $css->level1itemhoverstyles['border'] || $css->level1itemhoverstyles['text-align']
				|| $css->level1itemhoverstyles['fontcolor'] || $css->level1itemhoverstyles['fontsize'] || $css->level1itemhoverstyles['textshadow']
					) {
				$csstoinject .= "\n#" . $menuID . " li.level1:hover > span { " . $css->level1itemhoverstyles['margin'] . $css->level1itemhoverstyles['background'] . $css->level1itemhoverstyles['gradient'] . $css->level1itemhoverstyles['borderradius'] . $css->level1itemhoverstyles['shadow'] . $css->level1itemhoverstyles['border'] . " } ";
				$csstoinject .= "\n#" . $menuID . " li.level1:hover > span a { " . $css->level1itemhoverstyles['padding'] . $css->level1itemhoverstyles['text-align'] . $css->level1itemhoverstyles['fontfamily'] . $css->level1itemhoverstyles['fontcolor'] . $css->level1itemhoverstyles['fontsize'] . $css->level1itemhoverstyles['textshadow'] . $css->level1itemhoverstyles['text-align'] . $css->level1itemhoverstyles['text-transform'] . $css->level1itemhoverstyles['fontweight'] . " } ";
				$csstoinject .= "\n#" . $menuID . " li.level1.active > span { " . $css->level1itemhoverstyles['margin'] . $css->level1itemhoverstyles['background'] . $css->level1itemhoverstyles['gradient'] . $css->level1itemhoverstyles['borderradius'] . $css->level1itemhoverstyles['shadow'] . $css->level1itemhoverstyles['border'] . " } ";
				$csstoinject .= "\n#" . $menuID . " li.level1.active > span a { " . $css->level1itemhoverstyles['padding'] . $css->level1itemhoverstyles['fontfamily'] . $css->level1itemhoverstyles['fontcolor'] . $css->level1itemhoverstyles['fontsize'] . $css->level1itemhoverstyles['textshadow'] . $css->level1itemhoverstyles['text-align'] . $css->level1itemhoverstyles['text-transform'] . $css->level1itemhoverstyles['fontweight'] . " } ";
			}
			if ($css->level1itemhoverstyles['descfontcolor'] || $css->level1itemhoverstyles['descfontsize']
			) {
				$csstoinject .= "\n#" . $menuID . " li.level1:hover > span span.accordeonckdesc { " . $css->level1itemhoverstyles['descfontcolor'] . $css->level1itemhoverstyles['descfontsize'] . " } ";
				$csstoinject .= "\n#" . $menuID . " li.level1.active > span span.accordeonckdesc { " . $css->level1itemhoverstyles['descfontcolor'] . $css->level1itemhoverstyles['descfontsize'] . " } ";
			}
		}

		// level 2 styles
		if (isset($css->level2menustyles)) {
			if ($css->level2menustyles['padding'] || $css->level2menustyles['margin'] || $css->level2menustyles['background'] || $css->level2menustyles['gradient'] || $css->level2menustyles['borderradius'] || $css->level2menustyles['shadow'] || $css->level2menustyles['border']
					) {
				$csstoinject .= "\n#" . $menuID . " li.level1 > ul { " . $css->level2menustyles['padding'] . $css->level2menustyles['margin'] . $css->level2menustyles['background'] . $css->level2menustyles['gradient'] . $css->level2menustyles['borderradius'] . $css->level2menustyles['shadow'] . $css->level2menustyles['border'] . " } ";
			}
		}

		// level 2 styles
		if (isset($css->level2itemgroup)) {
			if ($css->level2itemgroup['padding'] || $css->level2itemgroup['margin'] || $css->level2itemgroup['background'] || $css->level2itemgroup['gradient'] || $css->level2itemgroup['borderradius'] || $css->level2itemgroup['shadow'] || $css->level2itemgroup['border']
					) {
				$csstoinject .= "\n#" . $menuID . " li.level2 { " . $css->level2itemgroup['padding'] . $css->level2itemgroup['margin'] . $css->level2itemgroup['background'] . $css->level2itemgroup['gradient'] . $css->level2itemgroup['borderradius'] . $css->level2itemgroup['shadow'] . $css->level2itemgroup['border'] . " } ";
			}
		}

		// level 2 styles
		if (isset($css->level2itemnormalstyles)) {
			if ($css->level2itemnormalstyles['padding'] || $css->level2itemnormalstyles['margin'] || $css->level2itemnormalstyles['background'] || $css->level2itemnormalstyles['gradient'] || $css->level2itemnormalstyles['borderradius'] || $css->level2itemnormalstyles['shadow'] || $css->level2itemnormalstyles['border'] || $css->level2itemnormalstyles['text-align']
				|| $css->level2itemnormalstyles['fontcolor'] || $css->level2itemnormalstyles['fontweight'] || $css->level2itemnormalstyles['fontsize'] || $css->level2itemnormalstyles['textshadow'] || $css->level2itemnormalstyles['text-transform']
					) {
				$csstoinject .= "\n#" . $menuID . " li.level2 > span { " . $css->level2itemnormalstyles['margin'] . $css->level2itemnormalstyles['background'] . $css->level2itemnormalstyles['gradient'] . $css->level2itemnormalstyles['borderradius'] . $css->level2itemnormalstyles['shadow'] . $css->level2itemnormalstyles['border'] . " } ";
				$csstoinject .= "\n#" . $menuID . " li.level2 > span a { " . $css->level2itemnormalstyles['padding'] . $css->level2itemnormalstyles['text-align'] . $css->level2itemnormalstyles['fontfamily'] . $css->level2itemnormalstyles['fontcolor'] . $css->level2itemnormalstyles['fontsize'] . $css->level2itemnormalstyles['textshadow'] . $css->level2itemnormalstyles['text-align'] . $css->level2itemnormalstyles['text-transform'] . $css->level2itemnormalstyles['fontweight'] . " } ";
			}
			if ($css->level2itemnormalstyles['descfontcolor'] || $css->level2itemnormalstyles['descfontsize']
			) {
				$csstoinject .= "\n#" . $menuID . " li.level2 > span span.accordeonckdesc { " . $css->level2itemnormalstyles['descfontcolor'] . $css->level2itemnormalstyles['descfontsize'] . " } ";
			}
		}
		
		// level 2 styles
		if (isset($css->level2itemhoverstyles)) {
			if ($css->level2itemhoverstyles['padding'] || $css->level2itemhoverstyles['margin'] || $css->level2itemhoverstyles['background'] || $css->level2itemhoverstyles['gradient'] || $css->level2itemhoverstyles['borderradius'] || $css->level2itemhoverstyles['shadow'] || $css->level2itemhoverstyles['border'] || $css->level2itemhoverstyles['text-align']
				|| $css->level2itemhoverstyles['fontcolor'] || $css->level2itemhoverstyles['fontsize'] || $css->level2itemhoverstyles['textshadow']
					) {
				$csstoinject .= "\n#" . $menuID . " li.level2:hover > span { " . $css->level2itemhoverstyles['margin'] . $css->level2itemhoverstyles['background'] . $css->level2itemhoverstyles['gradient'] . $css->level2itemhoverstyles['borderradius'] . $css->level2itemhoverstyles['shadow'] . $css->level2itemhoverstyles['border'] . " } ";
				$csstoinject .= "\n#" . $menuID . " li.level2:hover > span a { " . $css->level2itemhoverstyles['padding'] . $css->level2itemhoverstyles['text-align'] . $css->level2itemhoverstyles['fontfamily'] . $css->level2itemhoverstyles['fontcolor'] . $css->level2itemhoverstyles['fontsize'] . $css->level2itemhoverstyles['textshadow'] . $css->level2itemhoverstyles['text-align'] . $css->level2itemhoverstyles['text-transform'] . $css->level2itemhoverstyles['fontweight'] . " } ";
				$csstoinject .= "\n#" . $menuID . " li.level2.active > span { " . $css->level2itemhoverstyles['margin'] . $css->level2itemhoverstyles['background'] . $css->level2itemhoverstyles['gradient'] . $css->level2itemhoverstyles['borderradius'] . $css->level2itemhoverstyles['shadow'] . $css->level2itemhoverstyles['border'] . " } ";
				$csstoinject .= "\n#" . $menuID . " li.level2.active > span a { " . $css->level2itemhoverstyles['padding'] . $css->level2itemhoverstyles['text-align'] . $css->level2itemhoverstyles['fontfamily'] . $css->level2itemhoverstyles['fontcolor'] . $css->level2itemhoverstyles['fontsize'] . $css->level2itemhoverstyles['textshadow'] . $css->level2itemhoverstyles['text-align'] . $css->level2itemhoverstyles['text-transform'] . $css->level2itemhoverstyles['fontweight'] . " } ";
			}
			if ($css->level2itemhoverstyles['descfontcolor'] || $css->level2itemhoverstyles['descfontsize']
			) {
				$csstoinject .= "\n#" . $menuID . " li.level2:hover > span span.accordeonckdesc { " . $css->level2itemhoverstyles['descfontcolor'] . $css->level2itemhoverstyles['descfontsize'] . " } ";
				$csstoinject .= "\n#" . $menuID . " li.level2.active > span span.accordeonckdesc { " . $css->level2itemhoverstyles['descfontcolor'] . $css->level2itemhoverstyles['descfontsize'] . " } ";
			}
		}

		// level 3 styles
		if (isset($css->level3menustyles)) {
			if ($css->level3menustyles['padding'] || $css->level3menustyles['margin'] || $css->level3menustyles['background'] || $css->level3menustyles['gradient'] || $css->level3menustyles['borderradius'] || $css->level3menustyles['shadow'] || $css->level3menustyles['border']
					) {
				$csstoinject .= "\n#" . $menuID . " li.level2 ul[class^=\"content\"] { " . $css->level3menustyles['padding'] . $css->level3menustyles['margin'] . $css->level3menustyles['background'] . $css->level3menustyles['gradient'] . $css->level3menustyles['borderradius'] . $css->level3menustyles['shadow'] . $css->level3menustyles['border'] . " } ";
			}
		}

		// level 3 styles
		if (isset($css->level3itemgroup)) {
			if ($css->level3itemgroup['padding'] || $css->level3itemgroup['margin'] || $css->level3itemgroup['background'] || $css->level3itemgroup['gradient'] || $css->level3itemgroup['borderradius'] || $css->level3itemgroup['shadow'] || $css->level3itemgroup['border']
					) {
				$csstoinject .= "\n#" . $menuID . " li.level2 li.accordeonck { " . $css->level3itemgroup['padding'] . $css->level3itemgroup['margin'] . $css->level3itemgroup['background'] . $css->level3itemgroup['gradient'] . $css->level3itemgroup['borderradius'] . $css->level3itemgroup['shadow'] . $css->level3itemgroup['border'] . " } ";
			}
		}

		// level 3 styles
		if (isset($css->level3itemnormalstyles)) {
			if ($css->level3itemnormalstyles['padding'] || $css->level3itemnormalstyles['margin'] || $css->level3itemnormalstyles['background'] || $css->level3itemnormalstyles['gradient'] || $css->level3itemnormalstyles['borderradius'] || $css->level3itemnormalstyles['shadow'] || $css->level3itemnormalstyles['border'] || $css->level3itemnormalstyles['text-align']
				|| $css->level3itemnormalstyles['fontcolor'] || $css->level3itemnormalstyles['fontweight'] || $css->level3itemnormalstyles['fontsize'] || $css->level3itemnormalstyles['textshadow'] || $css->level3itemnormalstyles['text-transform']
					) {
				$csstoinject .= "\n#" . $menuID . " li.level2 li.accordeonck > span { " . $css->level3itemnormalstyles['margin'] . $css->level3itemnormalstyles['background'] . $css->level3itemnormalstyles['gradient'] . $css->level3itemnormalstyles['borderradius'] . $css->level3itemnormalstyles['shadow'] . $css->level3itemnormalstyles['border'] . " } ";
				$csstoinject .= "\n#" . $menuID . " li.level2 li.accordeonck > span a { " . $css->level3itemnormalstyles['padding'] . $css->level3itemnormalstyles['text-align'] . $css->level3itemnormalstyles['fontfamily'] . $css->level3itemnormalstyles['fontcolor'] . $css->level3itemnormalstyles['fontsize'] . $css->level3itemnormalstyles['textshadow'] . $css->level3itemnormalstyles['text-align'] . $css->level3itemnormalstyles['text-transform'] . $css->level3itemnormalstyles['fontweight'] . " } ";
			}
			if ($css->level3itemnormalstyles['descfontcolor'] || $css->level3itemnormalstyles['descfontsize']
			) {
				$csstoinject .= "\n#" . $menuID . " li.level2 li.accordeonck > span span.accordeonckdesc { " . $css->level3itemnormalstyles['descfontcolor'] . $css->level3itemnormalstyles['descfontsize'] . " } ";
			}
		}
		
		// level 3 styles
		if (isset($css->level3itemhoverstyles)) {
			if ($css->level3itemhoverstyles['padding'] || $css->level3itemhoverstyles['margin'] || $css->level3itemhoverstyles['background'] || $css->level3itemhoverstyles['gradient'] || $css->level3itemhoverstyles['borderradius'] || $css->level3itemhoverstyles['shadow'] || $css->level3itemhoverstyles['border'] || $css->level3itemhoverstyles['text-align']
				|| $css->level3itemhoverstyles['fontcolor'] || $css->level3itemhoverstyles['fontsize'] || $css->level3itemhoverstyles['textshadow']
					) {
				$csstoinject .= "\n#" . $menuID . " li.level2 li.accordeonck:hover > span { " . $css->level3itemhoverstyles['margin'] . $css->level3itemhoverstyles['background'] . $css->level3itemhoverstyles['gradient'] . $css->level3itemhoverstyles['borderradius'] . $css->level3itemhoverstyles['shadow'] . $css->level3itemhoverstyles['border'] . " } ";
				$csstoinject .= "\n#" . $menuID . " li.level2 li.accordeonck:hover > span a { " . $css->level3itemhoverstyles['padding'] . $css->level3itemhoverstyles['text-align'] . $css->level3itemhoverstyles['fontfamily'] . $css->level3itemhoverstyles['fontcolor'] . $css->level3itemhoverstyles['fontsize'] . $css->level3itemhoverstyles['textshadow'] . $css->level3itemhoverstyles['text-align'] . $css->level3itemhoverstyles['text-transform'] . $css->level3itemhoverstyles['fontweight'] . " } ";
				$csstoinject .= "\n#" . $menuID . " li.level2 li.accordeonck.active > span { " . $css->level3itemhoverstyles['margin'] . $css->level3itemhoverstyles['background'] . $css->level3itemhoverstyles['gradient'] . $css->level3itemhoverstyles['borderradius'] . $css->level3itemhoverstyles['shadow'] . $css->level3itemhoverstyles['border'] . " } ";
				$csstoinject .= "\n#" . $menuID . " li.level2 li.accordeonck.active > span a { " . $css->level3itemhoverstyles['padding'] . $css->level3itemhoverstyles['text-align'] . $css->level3itemhoverstyles['fontfamily'] . $css->level3itemhoverstyles['fontcolor'] . $css->level3itemhoverstyles['fontsize'] . $css->level3itemhoverstyles['textshadow'] . $css->level3itemhoverstyles['text-align'] . $css->level3itemhoverstyles['text-transform'] . $css->level3itemhoverstyles['fontweight'] . " } ";
			}
			if ($css->level3itemhoverstyles['descfontcolor'] || $css->level3itemhoverstyles['descfontsize']
			) {
				$csstoinject .= "\n#" . $menuID . " li.level2 li.accordeonck:hover > span span.accordeonckdesc { " . $css->level3itemhoverstyles['descfontcolor'] . $css->level3itemhoverstyles['descfontsize'] . " } ";
				$csstoinject .= "\n#" . $menuID . " li.level2 li.accordeonck.active > span span.accordeonckdesc { " . $css->level3itemhoverstyles['descfontcolor'] . $css->level3itemhoverstyles['descfontsize'] . " } ";
			}
		}

		if ($params->get('customcss', '') != '[]')
			$csstoinject .= $params->get('customcss', '');

		return $csstoinject;
	}
	
	/**
	 * Create the svg gradient for IE9
	 * @param string $prefix
	 *
	 * @return void
	 */
	static function createSvgGradient($menuID, $prefix, $color1, $color2) {
		// create the file svg for IE9 and Opera gradient compatibility
		$path = JPATH_ROOT . '/modules/mod_accordeonck/assets/svggradient/';
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

		return JURI::root() . 'modules/mod_accordeonck/assets/svggradient/' . $menuID . $prefix . '-gradient.svg';
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
	
	static public function getModuleFromId($id) {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select('m.id, m.params, mm.menuid')
			->from('#__modules AS m')
			->join('LEFT', '#__modules_menu AS mm ON mm.moduleid = m.id')
			->where('m.published = 1')
			->where('m.id = ' . (int)$id)

			->join('LEFT', '#__extensions AS e ON e.element = m.module AND e.client_id = m.client_id')
			->where('module = \'mod_accordeonck\'')
			->where('e.enabled = 1');
		// Set the query
		$db->setQuery($query);

		try
		{
			$module = $db->loadObject();
		}
		catch (RuntimeException $e)
		{
			JLog::add(JText::sprintf('JLIB_APPLICATION_ERROR_MODULE_LOAD', $e->getMessage()), JLog::WARNING, 'jerror');

			return null;
		}

		return $module;
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
	 * Get the css from the theme php file and write them into a css file.
	 *
	 * @param   string  $filetocompile  The path to the theme php file.
	 * @param   JRegistry  &$params  The module options.
	 *
	 * @return  true on success
	 *
	 */
	public static function getCompiledCss($params, $menuID) {
		$theme = $params->get('theme', 'default');
		$css = '';
		$phpcssfile = dirname(__FILE__) . '/themes/' . $theme . '/mod_accordeonck_css.php';
		if ($params->get('usestyles') == 1 && file_exists($phpcssfile)) {
			$phpcss = file_get_contents($phpcssfile);
	//		$menuID = $params->get('menuid', '');
			$css = str_replace('<?php echo $id; ?>', '#' . $menuID, $phpcss);
			$pattern = '/<\?php\s[^>]*[^>]*(.*)\?>/iUs';
			$replacement = '';
			$css = preg_replace($pattern, $replacement, $css);
		}

		// add the menu items css
		if (self::$_modulecss) {
			$css .= '
				
/*---------------------------------------------
---	 Module styles from Accordeon CK Params     ---
----------------------------------------------*/
';
			$css .= str_replace(array(";", "{"), array(";\n\t", "{\n\t"), self::$_modulecss); // add new line and tab for reading purpose
		}

		// add the menu items css
		if (self::$_itemcss) {
			$css .= '
				
/*---------------------------------------------
---	 Menu items	styles from Accordeon CK Params ---
----------------------------------------------*/
';
			$css .= str_replace(array(";", "{"), array(";\n\t", "{\n\t"), self::$_itemcss); // add new line and tab for reading purpose
		}
		$cssfile = dirname(__FILE__) . '/themes/custom/css/' . $menuID . '.css';
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