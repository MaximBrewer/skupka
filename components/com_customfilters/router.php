<?php
/**
 *
 * Customfilters router
 *
 * @package		customfilters
 * @author		Sakis Terz
 * @link		http://breakdesigns.net
 * @copyright	Copyright (c) 2012-2018 breakdesigns.net. All rights reserved.
 * @license		See LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die();
jimport('joomla.application.module.helper');
require_once JPATH_SITE. DIRECTORY_SEPARATOR. 'components'.DIRECTORY_SEPARATOR. 'com_customfilters'.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'tools.php';

use Joomla\Utilities\ArrayHelper;

function customfiltersBuildRoute(&$query)
{
    $segments = array();
    $db = JFactory::getDbo();

    // first get the filters
    if (! empty($query['virtuemart_category_id']) && is_array($query['virtuemart_category_id'])) {
        $vm_categories = $query['virtuemart_category_id'];
        $vm_categories=ArrayHelper::toInteger($vm_categories);
        $vm_categories=array_filter($vm_categories);
    }
    if (! empty($query['virtuemart_manufacturer_id']) && is_array($query['virtuemart_manufacturer_id'])) {
        $vm_manufacturers = $query['virtuemart_manufacturer_id'];
        $vm_manufacturers=ArrayHelper::toInteger($vm_manufacturers);
        $vm_manufacturers=array_filter($vm_manufacturers);
    }
    // empty filters strings
    $no_category = urlencode(JText::_('CF_NO_VMCAT'));
    $no_manufacturer = urlencode(JText::_('CF_NO_VMMANUF'));
    $manuf_string = '';
    $categ_string = '';

    if (isset($query['view']) && $query['view'] == 'products') {
        unset($query['view']);
    } else {
        // do not build route for other views
        return $segments;
    }

    // get the variables related with the languages
    if (! empty($vm_categories) || ! empty($vm_manufacturers)) {
        $CfRouterHelper = CfRouterHelper::getInstance();
        $siteLang = $CfRouterHelper->getLangPrefix();
        $defaultSiteLang = $CfRouterHelper->getDefaultLangPrefix();
    }

    // categories
    if (! empty($vm_categories)) {
        // Add the category alias
        $q = $db->getQuery(true);

        // It's multi-lingual
        if ($CfRouterHelper->getDefaultLang()) {
            $q->select("IFNULL(lang.slug, lang_def.slug) AS name");
            $q->from("#__virtuemart_categories_" . $defaultSiteLang . " AS lang_def");
            $q->leftJoin("#__virtuemart_categories_" . $siteLang . " AS lang ON lang_def.virtuemart_category_id=lang.virtuemart_category_id");
            $q->where('lang_def.virtuemart_category_id IN (' . implode(',', $vm_categories) . ')');
        } else {
            $q->select('slug');
            $q->from('#__virtuemart_categories_' . $siteLang);
            $q->where('virtuemart_category_id IN (' . implode(',', $vm_categories) . ')');
        }

        $db->setQuery($q);
        $vm_cat_aliases = $db->loadColumn();
        if ($vm_cat_aliases) {
            $categ_string = implode('__or__', $vm_cat_aliases);
        } else {
            $categ_string = $no_category;
        }
        unset($query['virtuemart_category_id']);
    } else {
        if (! empty($vm_manufacturers)) {
            $categ_string = $no_category;
        }
    }

    // manufacturers
    if (! empty($vm_manufacturers)) {
        $vm_manufacturers = (array) $vm_manufacturers;

        // Add the manuf alias
        $q = $db->getQuery(true);

        // It's multi-lingual
        if ($CfRouterHelper->getDefaultLang()) {
            $q->select("IFNULL(lang.slug, lang_def.slug) AS name");
            $q->from("#__virtuemart_manufacturers_" . $defaultSiteLang . " AS lang_def");
            $q->leftJoin("#__virtuemart_manufacturers_" . $siteLang . " AS lang ON lang_def.virtuemart_manufacturer_id=lang.virtuemart_manufacturer_id");
            $q->where('lang_def.virtuemart_manufacturer_id IN (' . implode(',', $vm_manufacturers) . ')');
        } else {
            $q->select('slug');
            $q->from('#__virtuemart_manufacturers_' . $siteLang);
            $q->where('virtuemart_manufacturer_id IN (' . implode(',', $vm_manufacturers) . ')');
        }

        $db->setQuery($q);
        $vm_mnf_aliases = $db->loadColumn();
        if ($vm_mnf_aliases) {
            $manuf_string = implode('__or__', $vm_mnf_aliases);
        }
        unset($query['virtuemart_manufacturer_id']);
    }

    $segments[] = $categ_string;
    $segments[] = $manuf_string;

    return $segments;
}

/**
 *
 * @author Sakis Terz
 * @since 1.0
 * @todo Check if the segments param is sanitized
 */
function customfiltersParseRoute($segments)
{
    $CfRouterHelper = CfRouterHelper::getInstance();
    $siteLang = $CfRouterHelper->getLangPrefix();
    $defaultSiteLang = $CfRouterHelper->getDefaultLangPrefix();

    // Fix the segments
    $total = count($segments);
    for ($i = 0; $i < $total; $i ++) {
        $segments[$i] = preg_replace('/:/', '-', $segments[$i], 1);
    }

    // empty filters strings
    $no_category = urlencode(JText::_('CF_NO_VMCAT'));
    $no_manufacturer = urlencode(JText::_('CF_NO_VMMANUF'));
    $db = JFactory::getDbo();

    $categories_ar = explode('__or__', $segments[0]);
    if (count($categories_ar) == 1 && $categories_ar[0] == $no_category) {} else {
        // get the category ids
        $where_vmcat_slug = array();
        $vmcat_where_str = '';

        // It's multi-lingual
        if ($CfRouterHelper->getDefaultLang()) {
            // prepare the slugs for the query
            array_walk($categories_ar, function (&$value, $key) {
                $db = JFactory::getDbo();
                $value = '(lang_def.slug=' . $db->quote($db->escape($value)) . ' OR lang.slug=' . $db->quote($db->escape($value)) . ')';
            });

            $vmcat_where_str = implode(' OR ', $categories_ar);

            if ($vmcat_where_str) {
                // Add the manuf alias
                $q = $db->getQuery(true);
                $q->select("IFNULL(lang.virtuemart_category_id, lang_def.virtuemart_category_id) AS virtuemart_category_id");
                $q->from('#__virtuemart_categories_' . $defaultSiteLang . ' AS lang_def');
                $q->leftJoin("#__virtuemart_categories_" . $siteLang . " AS lang ON lang_def.virtuemart_category_id=lang.virtuemart_category_id");
                $q->where($vmcat_where_str);
                $db->setQuery($q);
                $vm_cat_ids = $db->loadColumn();
                $vars['virtuemart_category_id'] = $vm_cat_ids;
            }
        } else {
            // prepare the slugs for the query
            array_walk($categories_ar, function (&$value, $key) {
                $db = JFactory::getDbo();
                $value = 'slug=' . $db->quote($db->escape($value));
            });

            $vmcat_where_str = implode(' OR ', $categories_ar);

            if ($vmcat_where_str) {
                // Add the manuf alias
                $q = $db->getQuery(true);
                $q->select('virtuemart_category_id');
                $q->from('#__virtuemart_categories_' . $siteLang);
                $q->where($vmcat_where_str);
                $db->setQuery($q);
                $vm_cat_ids = $db->loadColumn();
                $vars['virtuemart_category_id'] = $vm_cat_ids;
            }
        }
    }
    if (isset($segments[1])) {
        $manuf_ar = explode('__or__', $segments[1]);
        if (count($manuf_ar) == 1 && $manuf_ar[0] == $no_manufacturer) {} else {
            // get the manuf ids
            $where_vmmnf_slug = array();
            $vmmnf_where_str = '';

            // It's multi-lingual
            if ($CfRouterHelper->getDefaultLang()) {
                // prepare the slugs for the query
                array_walk($manuf_ar, function (&$value, $key) {
                    $db = JFactory::getDbo();
                    $value = '(lang_def.slug=' . $db->quote($db->escape($value)).' OR lang.slug=' . $db->quote($db->escape($value)).')';
                });

                    $vmmnf_where_str = implode(' OR ', $manuf_ar);

                    if ($vmmnf_where_str) {
                        // Add the manuf alias
                        $q = $db->getQuery(true);
                        $q->select('IFNULL(lang_def.virtuemart_manufacturer_id, lang.virtuemart_manufacturer_id) AS virtuemart_manufacturer_id');
                        $q->from('#__virtuemart_manufacturers_' . $defaultSiteLang.' AS lang_def');
                        $q->leftJoin('#__virtuemart_manufacturers_' . $siteLang.' AS lang ON lang_def.virtuemart_manufacturer_id=lang.virtuemart_manufacturer_id');
                        $q->where($vmmnf_where_str);
                        $db->setQuery($q);
                        $vm_mnf_ids = $db->loadColumn();
                        $vars['virtuemart_manufacturer_id'] = $vm_mnf_ids;
                    }

            } else {

                // prepare the slugs for the query
                array_walk($manuf_ar, function (&$value, $key) {
                    $db = JFactory::getDbo();
                    $value = 'slug=' . $db->quote($db->escape($value));
                });

                $vmmnf_where_str = implode(' OR ', $manuf_ar);

                if ($vmmnf_where_str) {
                    // Add the manuf alias
                    $q = $db->getQuery(true);
                    $q->select('virtuemart_manufacturer_id');
                    $q->from('#__virtuemart_manufacturers_' . $siteLang);
                    $q->where($vmmnf_where_str);
                    $db->setQuery($q);
                    $vm_mnf_ids = $db->loadColumn();
                    $vars['virtuemart_manufacturer_id'] = $vm_mnf_ids;
                }
            }
        }
    }
    return $vars;
}

/**
 * Check if any custom filter exist
 *
 * @param array $query
 * @since 1.9.0
 */
function existCustomfilter($query)
{
    foreach ($query as $key => $q) {
        if (strpos($key, 'custom_f_') !== false || strpos($key, 'price') !== false) {
            return true;
        }
    }
    return false;
}

/**
 * Class offering helper functions to the router's functions
 *
 * @author sakis
 *
 */
class CfRouterHelper
{

    /**
     *
     * @var CfRouterHelper
     */
    protected static $_cfrouter;

    /**
     *
     * @var bool|string
     */
    protected $defaultShopLang;

    /**
     * Constructor function
     * since 1.9.0
     */
    public function __construct()
    {
        if (! class_exists('VmConfig')) {
            require (JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'config.php');
        }
        VmConfig::loadConfig();
    }

    /**
     * Instantiation function
     *
     * @since 1.9.0
     *
     */
    public static function getInstance()
    {
        if (empty(self::$_cfrouter)) {
            self::$_cfrouter = new CfRouterHelper();
        }
        return self::$_cfrouter;
    }

    /**
     * Return the langprefix
     *
     * @since 1.9.0
     */
    public function getDefaultLangPrefix()
    {
        return cftools::getDefaultLanguagePrefix();
    }

    /**
     * Return the langprefix
     *
     * @since 1.9.0
     */
    public function getLangPrefix()
    {
        return cftools::getCurrentLanguagePrefix();
    }

    /**
     * Checks if the site's language is the same as the current
     * If not return the default
     *
     * @return false|string
     */
    public function getDefaultLang()
    {
        if ($this->defaultShopLang == null) {
            if ($this->getLangPrefix() != $this->getDefaultLangPrefix() && VmConfig::$langCount > 1) {
                $this->defaultShopLang = $this->getLangPrefix();
            } else {
                $this->defaultShopLang = false;
            }
        }
        return $this->defaultShopLang;
    }
}