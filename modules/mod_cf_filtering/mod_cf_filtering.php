<?php
/**
 * @package		customfilters
 * @subpackage	mod_cf_filtering
 * @copyright	Copyright (C) 2012-2018 breakdesigns.net . All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die();

//load dependencies
require_once dirname(__FILE__) . '/bootstrap.php';

VmConfig::loadConfig();
JText::script('MOD_CF_FILTERING_INVALID_CHARACTER');
JText::script('MOD_CF_FILTERING_PRICE_MIN_PRICE_CANNOT_EXCEED_MAX_PRICE');
JText::script('MOD_CF_FILTERING_MIN_CHARACTERS_LIMIT');

$jlang = JFactory::getLanguage();
$jlang->load('com_customfilters');
$jlang->load('com_virtuemart');

// Set the current language code
if (! defined('VMLANG')) {
    $languages = JLanguageHelper::getLanguages('lang_code');
    $siteLang = $jlang->getTag();
    $siteLang = strtolower(strtr($siteLang, '-', '_'));
} else {
    $siteLang = VMLANG;
}

if (! defined('JLANGPRFX')) {
    define('JLANGPRFX', $siteLang);
}

// Set the shop's default language
$shop_default_lang = VmConfig::$defaultLang;
if (! defined('VM_SHOP_LANG_PRFX')) {
    define('VM_SHOP_LANG_PRFX', $shop_default_lang);
}

$modObj = new ModCfFilteringHelper($params, $module);
$filters_render_array = $modObj->getFilters();
$filter_headers_array = $modObj->getFltHeaders();
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
    
 /*
 * calls the layout which will be used
 * template overrides can be used
 */
require (JModuleHelper::getLayoutPath('mod_cf_filtering', $params->get('layout', 'default')));
