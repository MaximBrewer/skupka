<?php

/**
 * @copyright	Copyright (C) 2011 Cédric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Accordeon CK
 * @license		GNU/GPL
 * Adapted from the original mod_menu on Joomla.site - Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');
require_once (dirname(__FILE__) . '/helper.php');

// load the items
$source = $params->get('source', 'menu');
if ($source != 'menu') {
	$sourceFile = JPATH_ROOT . '/plugins/accordeonmenuck/' . strtolower($source) . '/helper/helper_' . strtolower($source) . '.php';
	if (! file_exists($sourceFile)) {
		echo '<p syle="color:red;">Error : File plugins/accordeonmenuck/' . strtolower($source) . '/helper/helper_' . strtolower($source) . '.php not found !</p>';
		return;
	}
	include_once $sourceFile;
	$loaderClass = 'AccordeonmenuckHelpersource' . ucfirst($source);
	$list = $items = $loaderClass::getItems($params);
} else {
	// retrieve menu items
	$thirdparty = $params->get('thirdparty', 'none');
	switch ($thirdparty) :
		default:
		case 'none':
			// Include the syndicate functions only once
			// require_once dirname(__FILE__).'/helper.php';
			$list = modAccordeonckHelper::getItems($params, $module);
			break;
		case 'virtuemart':
			// Include the syndicate functions only once
			if (JFile::exists(dirname(__FILE__) . '/helper_virtuemart.php')) {
				require_once dirname(__FILE__) . '/helper_virtuemart.php';
				$list = modAccordeonckvirtuemartHelper::getItems($params, $params->get('vmcategoryroot', '0'), '1');
			} else {
				echo '<p style="color:red;font-weight:bold;">File helper_virtuemart.php not found ! Please download the patch for Accordeonmenu - Virtuemart on <a href="http://www.joomlack.fr">http://www.joomlack.fr</a></p>';
				return false;
			}
			break;
		case 'hikashop':
			// Include the syndicate functions only once
			if (JFile::exists(dirname(__FILE__) . '/helper_hikashop.php')) {
				require_once dirname(__FILE__) . '/helper_hikashop.php';
				$list = modAccordeonckhikashopHelper::getItems($params, false);
			} else {
				echo '<p style="color:red;font-weight:bold;">File helper_hikashop.php not found ! Please download the patch for Accordeonmenu - Hikashop on <a href="http://www.joomlack.fr">http://www.joomlack.fr</a></p>';
				return false;
			}
			break;
		case 'articles':
			// Include the syndicate functions only once
			if (JFile::exists(dirname(__FILE__) . '/helper_articles.php')) {
				require_once dirname(__FILE__) . '/helper_articles.php';
				$list = modAccordeonckarticlesHelper::getItems($params);
			} else {
				echo '<p style="color:red;font-weight:bold;">File helper_articles.php not found ! Please download the patch for Accordeonmenu - Articles on <a href="http://www.joomlack.fr">http://www.joomlack.fr</a></p>';
				return false;
			}
			break;
		case 'k2':
			// Include the syndicate functions only once
			if (JFile::exists(dirname(__FILE__) . '/helper_k2.php')) {
				require_once dirname(__FILE__) . '/helper_k2.php';
				$list = modAccordeonckk2Helper::getItems($params);
			} else {
				echo '<p style="color:red;font-weight:bold;">File helper_k2.php not found ! Please download the patch for Accordeonmenu - K2 on <a href="http://www.joomlack.fr">http://www.joomlack.fr</a></p>';
				return false;
			}
			break;
	endswitch;
}

// $list = modAccordeonckHelper::getMenu($params);
if (!$list)
	return false;

// retrieve parameters from the module
$startlevel = $params->get('startLevel', '0');
$endlevel = $params->get('endLevel', '10');
$menuID = $params->get('tag_id', 'accordeonck' . $module->id);
$mooduration = $params->get('mooduration', 500);
$mootransition = $params->get('mootransition', 'linear');
$imageplus = $params->get('imageplus', 'modules/mod_accordeonck/assets/plus.png');
$imageminus = $params->get('imageminus', 'modules/mod_accordeonck/assets/minus.png');
$imageposition = $params->get('imageposition', 'right');
$eventtype = $params->get('eventtype', 'click');
$fadetransition = $params->get('fadetransition', 'false');
$theme = $params->get('theme', 'default');

// laod the css and js in the page	
$document = JFactory::getDocument();
//JHTML::_("behavior.framework", true);
JHTML::_("jquery.framework", true);
JHTML::_("jquery.ui");
//$document->addScript(JURI::base(true) . '/modules/mod_accordeonck/assets/jquery.ui.1.8.js');
$document = JFactory::getDocument();
$document->addScript(JURI::base(true) . '/modules/mod_accordeonck/assets/mod_accordeonck.js');

// check the compilation process
$doCompile = false;
// if one of the compile option is active (compile or yes)
if ($params->get('loadcompiledcss', '0') != '0') {
	if ( $params->get('loadcompiledcss', '0') == '2'
			|| ! JFile::exists(dirname(__FILE__) . '/themes/custom/css/' . $menuID . '.css') ) {
		$doCompile = true;
	} else if($params->get('loadcompiledcss', '0') == '2') {
		echo '<p style="color:red;font-weight:bold;">MENU ACCORDEON CK ERROR : Advanced Options - Compile theme is active but file themes/' . $theme . '/css/mod_accordeonck_css.php not found.</p>';
	}
}
// set the doCompile params to use in the helper for menu items css
$params->set('doCompile', $doCompile);

// get the css from the plugin params and inject them
//if ( file_exists(JPATH_ROOT . '/administrator/components/com_accordeonck/accordeonck.php') ) {
//	modAccordeonckHelper::injectModuleCss($params, $menuID);
//}

if ($params->get('usestyles') == 1) {
	modAccordeonckHelper::injectLegacyModuleCss($params, $menuID);
}
else if ($params->get('usestyles') == 2) {
	// get the css from the plugin params and inject them
	if ( file_exists(JPATH_ROOT . '/administrator/components/com_accordeonck/accordeonck.php') ) {
		modAccordeonckHelper::injectModuleCss($params, $menuID);
//		$document->addStyleSheet(JURI::root(true) . '/modules/mod_accordeonck/themes/custom/custom_' . $module->id . '.css');
	} else {
		echo '<p style="color:red;font-weight:bold;">Accordeon CK Params not found ! Please download the Params for Accordeonmenu CK on <a href="http://www.joomlack.fr">http://www.joomlack.fr</a></p>';
	}
}
else if ($params->get('usestyles') == 3) {
//	$document->addStyleSheet(JURI::root(true) . '/modules/mod_accordeonck/themes/custom/' . $params->get('existingstyles') . '.css');
//	$styleId = str_replace('custom_', '', $params->get('existingstyles'));
	$styleId = $params->get('existingstyles');
	$existingModule = modAccordeonckHelper::getModuleFromId((int) $styleId);
	$existingModuleParams = new JRegistry($existingModule->params);
	$existingModuleParams->set('loadcompiledcss', $params->get('loadcompiledcss', '0'));
	$existingModuleParams->set('doCompile', $params->get('doCompile', '0'));
	modAccordeonckHelper::injectModuleCss($existingModuleParams, $menuID);
}

if ($params->get('loadcompiledcss', '0') != '0') {
	if ( $doCompile ) {
		$compilation = modAccordeonckHelper::getCompiledCss($params, $menuID);
		if (! $compilation) {
			echo '<p style="color:red;font-weight:bold;">ACCORDEONCK MENU ERROR : Advanced Options - Compile theme is active, error during compilation process.</p>';
		}
	}
	$document->addStyleSheet(JURI::base(true) . '/modules/mod_accordeonck/themes/custom/css/' . $menuID . '.css');
}

$document->addScript(JURI::base(true) . '/modules/mod_accordeonck/assets/jquery.easing.1.3.js');
$js = "
       jQuery(document).ready(function(jQuery){
        jQuery('#" . $menuID . "').accordeonmenuck({"
		. "fadetransition : " . $fadetransition . ","
		. "eventtype : '" . $eventtype . "',"
		. "transition : '" . $mootransition . "',"
		. "menuID : '" . $menuID . "',"
		. "imageplus : '" . JURI::base(true) . '/' . $imageplus . "',"
		. "imageminus : '" . JURI::base(true) . '/' . $imageminus . "',"
		. "defaultopenedid : '" . $params->get('defaultopenedid') . "',"
		. "activeeffect : '" . (bool) $params->get('activeeffect') . "',"
		. "showcounter : '" . (bool) $params->get('showcounter', false) . "',"
		. "duree : " . $mooduration
		. "});
}); ";

$document->addScriptDeclaration($js);

// $list = modAccordeonckHelper::getMenu($params);
$app = JFactory::getApplication();
$menu = $app->getMenu();
$active = $menu->getActive();
$active_id = isset($active) ? $active->id : $menu->getDefault()->id;
$path = isset($active) ? $active->tree : array();
$showAll = 1;
$class_sfx = htmlspecialchars($params->get('class_sfx'));

if (count($list)) {
	require JModuleHelper::getLayoutPath('mod_accordeonck', $params->get('layout', 'default'));
}