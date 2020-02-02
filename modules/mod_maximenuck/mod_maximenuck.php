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
require_once dirname(__FILE__) . '/helper.php';

// set the default html id for the menu
if ( $params->get('menuid', '') === '' || is_numeric($params->get('menuid', ''))) {
	$params->set('menuid', 'maximenuck' . $module->id);
}
$menuID = $params->get('menuid', '');
$loadfontawesome = false;
$theme = $params->get('theme', 'default');

// check the compilation process
$doCompile = false;
// if one of the compile option is active (compile or yes)
if ($params->get('loadcompiledcss', '0') != '0') {
	if ( ($params->get('loadcompiledcss', '0') == '2' && JFile::exists(dirname(__FILE__) . '/themes/' . $theme . '/css/maximenuck.php'))
			|| ! JFile::exists(dirname(__FILE__) . '/themes/custom/css/maximenuck_' . $menuID . '.css') ) {
		$doCompile = true;
	} else if($params->get('loadcompiledcss', '0') == '2') {
		echo '<p style="color:red;font-weight:bold;">MAXIMENU ERROR : Advanced Options - Compile theme is active but file themes/' . $theme . '/css/maximenuck.php not found.</p>';
	}
}
// set the doCompile params to use in the helper for menu items css
$params->set('doCompile', $doCompile);


// retrieve menu items
$thirdparty = $params->get('thirdparty', 'none');
switch ($thirdparty) :
	case 'none':
		// Include the syndicate functions only once
		// require_once dirname(__FILE__).'/helper.php';
		$items = modMaximenuckHelper::getItems($params);
		break;
//	case 'virtuemart':
//		// Include the syndicate functions only once
//		if (JFile::exists(dirname(__FILE__) . '/helper_virtuemart.php')) {
//			require_once dirname(__FILE__) . '/helper_virtuemart.php';
//			$items = modMaximenuckvirtuemartHelper::getItems($params);
//		} else {
//			echo '<p style="color:red;font-weight:bold;">File helper_virtuemart.php not found ! Please download the patch for Maximenu - Virtuemart on <a href="http://www.joomlack.fr">http://www.joomlack.fr</a></p>';
//			return false;
//		}
//		break;
	case 'hikashop':
		// Include the syndicate functions only once
		if (JFile::exists(dirname(__FILE__) . '/helper_hikashop.php')) {
			require_once dirname(__FILE__) . '/helper_hikashop.php';
			$items = modMaximenuckhikashopHelper::getItems($params);
		} else {
			echo '<p style="color:red;font-weight:bold;">File helper_hikashop.php not found ! Please download the patch for Maximenu - Hikashop on <a href="http://www.joomlack.fr">http://www.joomlack.fr</a></p>';
			return false;
		}
		break;
	case 'articles':
		// Include the syndicate functions only once
		if (JFile::exists(dirname(__FILE__) . '/helper_articles.php')) {
			require_once dirname(__FILE__) . '/helper_articles.php';
			$items = modMaximenuckhikashopHelper::getItems($params);
		} else {
			echo '<p style="color:red;font-weight:bold;">File helper_articles.php not found ! Please download the patch for Maximenu - Joomla articles on <a href="http://www.joomlack.fr">http://www.joomlack.fr</a></p>';
			return false;
		}
		break;
	case 'k2':
		// Include the syndicate functions only once
		if (JFile::exists(dirname(__FILE__) . '/helper_k2.php')) {
			require_once dirname(__FILE__) . '/helper_k2.php';
			$items = modMaximenuckk2Helper::getItems($params);
		} else {
			echo '<p style="color:red;font-weight:bold;">File helper_k2.php not found ! Please download the patch for Maximenu - k2 on <a href="http://www.joomlack.fr">http://www.joomlack.fr</a></p>';
			return false;
		}
		break;
	case 'joomshopping':
		// Include the syndicate functions only once
		if (JFile::exists(dirname(__FILE__) . '/helper_joomshopping.php')) {
			require_once dirname(__FILE__) . '/helper_joomshopping.php';
			$items = modMaximenuckjoomshoppingHelper::getItems($params, false);
		} else {
			echo '<p style="color:red;font-weight:bold;">File helper_joomshopping.php not found ! Please download the patch for Maximenu - Joomshopping on <a href="http://www.joomlack.fr">http://www.joomlack.fr</a></p>';
			return false;
		}
		break;
	default: // for all thirdparty like virtuemart or adsmanager
	// case 'adsmanager':
		// Include the syndicate functions only once
		if (JFile::exists(JPATH_ROOT . '/plugins/system/maximenuck_'.$thirdparty.'/helper/helper_maximenuck_'.$thirdparty.'.php')) {
			require_once JPATH_ROOT . '/plugins/system/maximenuck_'.$thirdparty.'/helper/helper_maximenuck_'.$thirdparty.'.php';
			$className = 'modMaximenuck'.$thirdparty.'Helper';
			$items = $className::getItems($params, false);
		} else {
			echo '<p style="color:red;font-weight:bold;">Plugin maximenuck_'.$thirdparty.' not found ! Please download the patch for Maximenu - '.ucfirst($thirdparty).' on <a href="http://www.joomlack.fr">http://www.joomlack.fr</a></p>';
			return false;
		}
		break;
endswitch;

// if no item in the menu then exit
if (!count($items) OR !$items)
	return false;

$document = JFactory::getDocument();
$app = JFactory::getApplication();
$menu = $app->getMenu();
$active = $menu->getActive();
$active_id = isset($active) ? $active->id : $menu->getDefault()->id;
$path = isset($active) ? $active->tree : array();
$class_sfx = htmlspecialchars($params->get('class_sfx'));
jimport('joomla.plugin.helper');

// get the language direction
$langdirection = $document->getDirection();

// page title management
if ($active) {
	$pagetitle = $document->getTitle();
	$title = $pagetitle;
	if (preg_match("/||/", $active->title)) {
		$title = explode("||", $active->title);
		$title = str_replace($active->title, $title[0], $pagetitle);
	}
	if (preg_match("/\[/", $active->title)) {
		if (!$title)
			$title = $active->title;
		$title = explode("[", $title);
		$title = str_replace($active->title, $title[0], $pagetitle);
	}
	$document->setTitle($title); // returns the page title without description
}


// retrieve parameters from the module
// params for the script
$fxduration = $params->get('fxduration', 500);
$fxtransition = $params->get('fxtransition', 'linear');
$orientation = $params->get('orientation', 'horizontal');
$testoverflow = $params->get('testoverflow', '0');
$opentype = $params->get('opentype', 'open');
$fxdirection = $params->get('direction', 'normal');
$directionoffset1 = $params->get('directionoffset1', '30');
$directionoffset2 = $params->get('directionoffset2', '30');
$behavior = $params->get('behavior', 'moomenu');
$usecss = $params->get('usecss', '1'); // for old version compatibility (no more used in the xml)
$usefancy = $params->get('usefancy', '1');
$fancyduree = $params->get('fancyduration', 500);
$fancytransition = $params->get('fancytransition', 'linear');
$fancyease = $params->get('fancyease', 'easeOut');
$fxtype = $params->get('fxtype', 'open');
$dureein = $params->get('dureein', 0);
$dureeout = $params->get('dureeout', 500);
$showactivesubitems = $params->get('showactivesubitems', '0');
$menubgcolor = $params->get('menubgcolor', '') ? "background:" . $params->get('menubgcolor', '') : '';
$ismobile = '0';
$logoimage = $params->get('logoimage', '');
$logolink = $params->get('logolink', '');
$logoheight = $params->get('logoheight', '');
$logowidth = $params->get('logowidth', '');
$usejavascript = $params->get('usejavascript', '1');
$effecttype = ($params->get('layout', 'default') == '_:pushdown') ? 'pushdown' : 'dropdown';

if ( ($effecttype == 'pushdown' || $effecttype == 'megatabs') && $orientation == 'vertical') {
	echo '<p style="color:red;font-weight:bold;">MAXIMENU MESSAGE : You can not use this layout for a Vertical menu</p>';
	return false;
}
// detection for mobiles
if (isset($_SERVER['HTTP_USER_AGENT']) && (strstr($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'], 'iPad') || strstr($_SERVER['HTTP_USER_AGENT'], 'iPod') || strstr($_SERVER['HTTP_USER_AGENT'], 'Android'))) {
	$behavior = 'click';
	$ismobile = '1';
}

// get the css from the plugin params and inject them
if ( file_exists(JPATH_ROOT . '/administrator/components/com_maximenuck/maximenuck.php') ) {
	modMaximenuckHelper::injectModuleCss($params, $menuID);
}


if ( $theme != '-1' ) {
	if ($params->get('loadcompiledcss', '0')) {
		if ( $doCompile ) {
			$compilation = modMaximenuckHelper::getCompiledCss($params);
			if (! $compilation) {
				echo '<p style="color:red;font-weight:bold;">MAXIMENU ERROR : Advanced Options - Compile theme is active, error during compilation process.</p>';
			}
		}
		$document->addStyleSheet(JURI::base(true) . '/modules/mod_maximenuck/themes/custom/css/maximenuck_' . $menuID . '.css');
	} else if ( JFile::exists(dirname(__FILE__) . '/themes/' . $theme . '/css/maximenuck.php') ) {
		if ($langdirection == 'rtl' && JFile::exists(dirname(__FILE__) . '/themes/' . $theme . '/css/maximenuck_rtl.php')) {
			$document->addStyleSheet(JURI::base(true) . '/modules/mod_maximenuck/themes/' . $theme . '/css/maximenuck_rtl.php?monid=' . $menuID);
		} else {
			$document->addStyleSheet(JURI::base(true) . '/modules/mod_maximenuck/themes/' . $theme . '/css/maximenuck.php?monid=' . $menuID);
		}
	} else { // compatibility with old themes
		$retrocompatibility_css = '#'.$menuID.' div.floatck, #'.$menuID.' ul.maximenuck li:hover div.floatck div.floatck, #'.$menuID.' ul.maximenuck li:hover div.floatck:hover div.floatck div.floatck,
#'.$menuID.' ul.maximenuck li.sfhover div.floatck div.floatck, #'.$menuID.' ul.maximenuck li.sfhover div.floatck.sfhover div.floatck div.floatck {
left: auto !important;
height: auto;
width: auto;
display: none;
}

#'.$menuID.' ul.maximenuck li:hover div.floatck, #'.$menuID.' ul.maximenuck li:hover div.floatck li:hover div.floatck, #'.$menuID.' ul.maximenuck li:hover div.floatck li:hover div.floatck li:hover div.floatck,
#'.$menuID.' ul.maximenuck li.sfhover div.floatck, #'.$menuID.' ul.maximenuck li.sfhover div.floatck li.sfhover div.floatck, #'.$menuID.' ul.maximenuck li.sfhover div.floatck li.sfhover div.floatck li.sfhover div.floatck {
display: block;
left: auto !important;
height: auto;
width: auto;
}

div#'.$menuID.' ul.maximenuck li.maximenuck.nodropdown div.floatck,
div#'.$menuID.' ul.maximenuck li.maximenuck div.floatck li.maximenuck.nodropdown div.floatck,
div#'.$menuID.' .maxipushdownck div.floatck div.floatck {
display: block !important;
}';
		$document->addStyleDeclaration($retrocompatibility_css);
		// add external stylesheets
		if ($orientation == 'vertical') {
			if ($langdirection == 'rtl' && JFile::exists(dirname(__FILE__) . '/themes/' . $theme . '/css/moo_maximenuvck_rtl.css')) {
				$document->addStyleSheet(JURI::base(true) . '/modules/mod_maximenuck/themes/' . $theme . '/css/moo_maximenuvck_rtl.css');
			} else {
				$document->addStyleSheet(JURI::base(true) . '/modules/mod_maximenuck/themes/' . $theme . '/css/moo_maximenuvck.css');
			}
			if ($usecss == 1 ) {
				if ($langdirection == 'rtl' && JFile::exists(dirname(__FILE__) . '/themes/' . $theme . '/css/maximenuvck_rtl.php')) {
					$document->addStyleSheet(JURI::base(true) . '/modules/mod_maximenuck/themes/' . $theme . '/css/maximenuvck_rtl.php?monid=' . $menuID);
				} else {
					$document->addStyleSheet(JURI::base(true) . '/modules/mod_maximenuck/themes/' . $theme . '/css/maximenuvck.php?monid=' . $menuID);
				}
			}
		} else {
			if ($langdirection == 'rtl' && JFile::exists(dirname(__FILE__) . '/themes/' . $theme . '/css/moo_maximenuhck_rtl.css')) {
				$document->addStyleSheet(JURI::base(true) . '/modules/mod_maximenuck/themes/' . $theme . '/css/moo_maximenuhck_rtl.css');
			} else {
				$document->addStyleSheet(JURI::base(true) . '/modules/mod_maximenuck/themes/' . $theme . '/css/moo_maximenuhck.css');
			}
			if ($usecss == 1) {
				if ($langdirection == 'rtl' && JFile::exists(dirname(__FILE__) . '/themes/' . $theme . '/css/maximenuhck_rtl.php')) {
					$document->addStyleSheet(JURI::base(true) . '/modules/mod_maximenuck/themes/' . $theme . '/css/maximenuhck_rtl.php?monid=' . $menuID);
				} else {
					$document->addStyleSheet(JURI::base(true) . '/modules/mod_maximenuck/themes/' . $theme . '/css/maximenuhck.php?monid=' . $menuID);
				}
			}
		}
	}

	if (JFile::exists('modules/mod_maximenuck/themes/' . $theme . '/css/ie7.css')) {
		echo '
			<!--[if lte IE 7]>
			<link href="' . JURI::base(true) . '/modules/mod_maximenuck/themes/' . $theme . '/css/ie7.css" rel="stylesheet" type="text/css" />
			<![endif]-->';
	}
} else {
	$dropdown_css = '#'.$menuID.' div.floatck, #'.$menuID.' ul.maximenuck li:hover div.floatck div.floatck, #'.$menuID.' ul.maximenuck li:hover div.floatck:hover div.floatck div.floatck,
#'.$menuID.' ul.maximenuck li.sfhover div.floatck div.floatck, #'.$menuID.' ul.maximenuck li.sfhover div.floatck.sfhover div.floatck div.floatck {
display: none;
}

#'.$menuID.' ul.maximenuck li:hover div.floatck, #'.$menuID.' ul.maximenuck li:hover div.floatck li:hover div.floatck, #'.$menuID.' ul.maximenuck li:hover div.floatck li:hover div.floatck li:hover div.floatck,
#'.$menuID.' ul.maximenuck li.sfhover div.floatck, #'.$menuID.' ul.maximenuck li.sfhover div.floatck li.sfhover div.floatck, #'.$menuID.' ul.maximenuck li.sfhover div.floatck li.sfhover div.floatck li.sfhover div.floatck {
display: block;
}';
		$document->addStyleDeclaration($dropdown_css);
}

$menuposition = $params->get('menuposition', '0');
if ($menuposition) {
	$fixedcssposition = ($menuposition == 'bottomfixed') ? "bottom: 0 !important;" : "top: 0 !important;";
	$fixedcss = "div#" . $menuID . ".maximenufixed {
        position: fixed !important;
        left: 0 !important;
        " . $fixedcssposition . "
        right: 0 !important;
        z-index: 1000 !important;
		margin: 0 auto;
		width: 100%;
		" . ($params->get('fixedpositionwidth') ? "max-width: " . modMaximenuckHelper::testUnit($params->get('fixedpositionwidth')) . ";" : "" ) . "
    }";
	if ($menuposition == 'topfixed') {
		$fixedcss .= "div#" . $menuID . ".maximenufixed ul.maximenuck {
            top: 0 !important;
        }";
	} else if ($menuposition == 'bottomfixed') {
		$fxdirection = 'inverse';
	}
//$topfixedmenu = $params->get('topfixedmenu', '0');
	//if ($topfixedmenu)
	$document->addStyleDeclaration($fixedcss);
}

if ($params->get('maximenumobile_enable') === '1') {
	$mobiletogglercss = "@media screen and (max-width: 524px) {"
		. "#" . $menuID . " .maximenumobiletogglericonck {display: block !important;font-size: 33px !important;text-align: right !important;padding-top: 10px !important;}"
		. "#" . $menuID . " ul.maximenuck .maximenumobiletogglerck ~ li.maximenuck.level1 {display: none !important;}"
		. "#" . $menuID . " ul.maximenuck .maximenumobiletogglerck:checked ~ li.maximenuck.level1 {display: block !important;}"
		. "}";
	$document->addStyleDeclaration($mobiletogglercss);
}

// add the css classes to show/hide the items
if (JPluginHelper::isEnabled('system', 'maximenuckmobile')) {
	$maximenuplugin = JPluginHelper::getPlugin('system', 'maximenuckmobile');
	$pluginParams = new JRegistry($maximenuplugin->params);
	$resolution = $pluginParams->get('maximenumobile_resolution', '640');
} else {
	$resolution = "524";
}
$mobilecss = "@media screen and (max-width: " . (int)$resolution . "px) {"
	. "div#" . $menuID . " ul.maximenuck li.maximenuck.nomobileck, div#" . $menuID . " .maxipushdownck ul.maximenuck2 li.maximenuck.nomobileck { display: none !important; }"
	. "}"
	. "@media screen and (min-width: " . ((int)$resolution+1) . "px) {"
	. "div#" . $menuID . " ul.maximenuck li.maximenuck.nodesktopck, div#" . $menuID . " .maxipushdownck ul.maximenuck2 li.maximenuck.nodesktopck { display: none !important; }"
	. "}";
$document->addStyleDeclaration($mobilecss);


// add compatibility css for templates
$templatelayer = $params->get('templatelayer', 'beez3-position1');
if ($templatelayer != -1)
	$document->addStyleSheet(JURI::base(true) . '/modules/mod_maximenuck/templatelayers/' . $templatelayer . '.css');

// add responsive css
if ($orientation == 'horizontal' && $params->get('useresponsive', '1') == '1')
	$document->addStyleSheet(JURI::base(true) . '/modules/mod_maximenuck/assets/maximenuresponsiveck.css');

JHTML::_("jquery.framework", true);
JHTML::_("jquery.ui");

if ($usejavascript && $params->get('layout', 'default') != '_:flatlist' && $params->get('layout', 'default') != '_:nativejoomla' && $params->get('layout', 'default') != '_:dropselect') {
	$document->addScript(JURI::base(true) . '/modules/mod_maximenuck/assets/maximenuck.js');

	if ($fxtransition != 'linear' || $fancytransition != 'linear')
		$document->addScript(JURI::base(true) . '/modules/mod_maximenuck/assets/jquery.easing.1.3.js');
	if ($opentype == 'scale' || $opentype == 'puff' || $opentype == 'drop')
		$document->addScript(JURI::base(true) . '/modules/mod_maximenuck/assets/jquery.ui.1.8.js');

	$load = ($params->get('load', 'domready') == 'load') ? "jQuery(window).load(function(){jQuery" : "jQuery(document).ready(function(jQuery){jQuery";
	$js = $load . "('#" . $menuID . "').DropdownMaxiMenu({"
			. "fxtransition : '" . $fxtransition . "',"
			. "dureeIn : " . $dureein . ","
			. "dureeOut : " . $dureeout . ","
			. "menuID : '" . $menuID . "',"
			. "testoverflow : '" . $testoverflow . "',"
			. "orientation : '" . $orientation . "',"
			. "behavior : '" . $behavior . "',"
			. "opentype : '" . $opentype . "',"
			. "fxdirection : '" . $fxdirection . "',"
			. "directionoffset1 : '" . $directionoffset1 . "',"
			. "directionoffset2 : '" . $directionoffset2 . "',"
			. "showactivesubitems : '" . $showactivesubitems . "',"
			. "ismobile : " . $ismobile . ","
			. "menuposition : '" . $menuposition . "',"
			. "effecttype : '" . $effecttype . "',"
			. "topfixedeffect : '" . $params->get('topfixedeffect', '1') . "',"
			. "fxduration : " . $fxduration . "});"
			. "});";

	$document->addScriptDeclaration($js);



// add fancy effect
	if ($orientation == 'horizontal' && $usefancy == 1) {
		$document->addScript(JURI::base(true) . '/modules/mod_maximenuck/assets/fancymenuck.js');
		$js = "jQuery(window).load(function(){
            jQuery('#" . $menuID . "').FancyMaxiMenu({"
				. "fancyTransition : '" . $fancytransition . "',"
				. "fancyDuree : " . $fancyduree . "});"
				. "});";
		$document->addScriptDeclaration($js);
	}
}

require JModuleHelper::getLayoutPath('mod_maximenuck', $params->get('layout', 'default'));

// load font awesome if needed
global $ckfontawesomeisloaded;
if ($loadfontawesome && !$ckfontawesomeisloaded) {
	$document->addStyleSheet(JURI::base(true) . '/modules/mod_maximenuck/assets/font-awesome.min.css');
	$ckfontawesomeisloaded = true;
}
