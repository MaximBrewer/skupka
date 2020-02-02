<?php
/**
 * Class that offers some static functions that can be used by the module
 * @package	customfilters
 * @author 	Sakis Terz
 * @since	1.8.0
 * @copyright	Copyright (C) 2012-2018 breakdesigns.net . All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die();

if (! class_exists('CfOutput')) {
	require (JPATH_SITE. DIRECTORY_SEPARATOR.'components' . DIRECTORY_SEPARATOR . 'com_customfilters' . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'output.php');
}

use Joomla\Utilities\ArrayHelper;
use Joomla\Registry\Registry;

class cftools
{

    protected static $menuparams;

    protected static $moduleparams = array();

    protected static $componentparams;

    protected static $_publishedCustomFilters = null;

    protected static $encodedInputs = null;

    protected static $rangeVars = array();

    protected static $active_tree = array();

    // the vendor's accepted currencies
    protected static $vendor_cur;

    // the shopper groups of a shopper
    protected static $shopper_groups = array();

    // info about the a specific currency
    protected static $currency_info = array();

    protected static $currencyConverter;

    protected static $cur_codes = array();

    // the date format as set in the component's parems
    protected static $dateFormat_php = '';

    protected static $dateFormat = '';

    protected static $calcRules = array();

    protected static $calcRulesPerSelection = array();

    protected static $module = array();

    protected static $subcategories = array();

    protected static $langPrefix;

    protected static $defaultLangPrefix;

    /**
     * List of colors with their corresponding names
     * Used to sanitize color names
     *
     * @var array
     */
    public static $standard_colors = array(
        "Black" => "#000000",
        "Navy" => "#000080",
        "DarkBlue" => "#00008B",
        "MediumBlue" => "#0000CD",
        "Blue" => "#0000FF",
        "DarkGreen" => "#006400",
        "Green" => "#008000",
        "Teal" => "#008080",
        "DarkCyan" => "#008B8B",
        "DeepSkyBlue" => "#00BFFF",
        "DarkTurquoise" => "#00CED1",
        "MediumSpringGreen" => "#00FA9A",
        "Lime" => "#00FF00",
        "SpringGreen" => "#00FF7F",
        "Aqua" => "#00FFFF",
        "Cyan" => "#00FFFF",
        "MidnightBlue" => "#191970",
        "DodgerBlue" => "#1E90FF",
        "LightSeaGreen" => "#20B2AA",
        "ForestGreen" => "#228B22",
        "SeaGreen" => "#2E8B57",
        "DarkSlateGray" => "#2F4F4F",
        "LimeGreen" => "#32CD32",
        "MediumSeaGreen" => "#3CB371",
        "Turquoise" => "#40E0D0",
        "RoyalBlue" => "#4169E1",
        "SteelBlue" => "#4682B4",
        "DarkSlateBlue" => "#483D8B",
        "MediumTurquoise" => "#48D1CC",
        "Indigo " => "#4B0082",
        "DarkOliveGreen" => "#556B2F",
        "CadetBlue" => "#5F9EA0",
        "CornflowerBlue" => "#6495ED",
        "MediumAquaMarine" => "#66CDAA",
        "DimGray" => "#696969",
        "SlateBlue" => "#6A5ACD",
        "OliveDrab" => "#6B8E23",
        "SlateGray" => "#708090",
        "LightSlateGray" => "#778899",
        "MediumSlateBlue" => "#7B68EE",
        "LawnGreen" => "#7CFC00",
        "Chartreuse" => "#7FFF00",
        "Aquamarine" => "#7FFFD4",
        "Maroon" => "#800000",
        "Purple" => "#800080",
        "Olive" => "#808000",
        "Gray" => "#808080",
        "SkyBlue" => "#87CEEB",
        "LightSkyBlue" => "#87CEFA",
        "BlueViolet" => "#8A2BE2",
        "DarkRed" => "#8B0000",
        "DarkMagenta" => "#8B008B",
        "SaddleBrown" => "#8B4513",
        "DarkSeaGreen" => "#8FBC8F",
        "LightGreen" => "#90EE90",
        "MediumPurple" => "#9370DB",
        "DarkViolet" => "#9400D3",
        "PaleGreen" => "#98FB98",
        "DarkOrchid" => "#9932CC",
        "YellowGreen" => "#9ACD32",
        "Sienna" => "#A0522D",
        "Brown" => "#A52A2A",
        "DarkGray" => "#A9A9A9",
        "LightBlue" => "#ADD8E6",
        "GreenYellow" => "#ADFF2F",
        "PaleTurquoise" => "#AFEEEE",
        "LightSteelBlue" => "#B0C4DE",
        "PowderBlue" => "#B0E0E6",
        "FireBrick" => "#B22222",
        "DarkGoldenRod" => "#B8860B",
        "MediumOrchid" => "#BA55D3",
        "RosyBrown" => "#BC8F8F",
        "DarkKhaki" => "#BDB76B",
        "Silver" => "#C0C0C0",
        "MediumVioletRed" => "#C71585",
        "IndianRed " => "#CD5C5C",
        "Peru" => "#CD853F",
        "Chocolate" => "#D2691E",
        "Tan" => "#D2B48C",
        "LightGray" => "#D3D3D3",
        "Thistle" => "#D8BFD8",
        "Orchid" => "#DA70D6",
        "GoldenRod" => "#DAA520",
        "PaleVioletRed" => "#DB7093",
        "Crimson" => "#DC143C",
        "Gainsboro" => "#DCDCDC",
        "Plum" => "#DDA0DD",
        "BurlyWood" => "#DEB887",
        "LightCyan" => "#E0FFFF",
        "Lavender" => "#E6E6FA",
        "DarkSalmon" => "#E9967A",
        "Violet" => "#EE82EE",
        "PaleGoldenRod" => "#EEE8AA",
        "LightCoral" => "#F08080",
        "Khaki" => "#F0E68C",
        "AliceBlue" => "#F0F8FF",
        "HoneyDew" => "#F0FFF0",
        "Azure" => "#F0FFFF",
        "SandyBrown" => "#F4A460",
        "Wheat" => "#F5DEB3",
        "Beige" => "#F5F5DC",
        "WhiteSmoke" => "#F5F5F5",
        "MintCream" => "#F5FFFA",
        "GhostWhite" => "#F8F8FF",
        "Salmon" => "#FA8072",
        "AntiqueWhite" => "#FAEBD7",
        "Linen" => "#FAF0E6",
        "LightGoldenRodYellow" => "#FAFAD2",
        "OldLace" => "#FDF5E6",
        "Red" => "#FF0000",
        "FerrariRed" => "#FF2800",
        "Fuchsia" => "#FF00FF",
        "Magenta" => "#FF00FF",
        "DeepPink" => "#FF1493",
        "OrangeRed" => "#FF4500",
        "Tomato" => "#FF6347",
        "HotPink" => "#FF69B4",
        "Coral" => "#FF7F50",
        "DarkOrange" => "#FF8C00",
        "LightSalmon" => "#FFA07A",
        "Orange" => "#FFA500",
        "LightPink" => "#FFB6C1",
        "Pink" => "#FFC0CB",
        "Gold" => "#FFD700",
        "PeachPuff" => "#FFDAB9",
        "NavajoWhite" => "#FFDEAD",
        "Moccasin" => "#FFE4B5",
        "Bisque" => "#FFE4C4",
        "MistyRose" => "#FFE4E1",
        "BlanchedAlmond" => "#FFEBCD",
        "PapayaWhip" => "#FFEFD5",
        "LavenderBlush" => "#FFF0F5",
        "SeaShell" => "#FFF5EE",
        "Cornsilk" => "#FFF8DC",
        "LemonChiffon" => "#FFFACD",
        "FloralWhite" => "#FFFAF0",
        "Snow" => "#FFFAFA",
        "Yellow" => "#FFFF00",
        "LightYellow" => "#FFFFE0",
        "Ivory" => "#FFFFF0",
        "White" => "#FFFFFF"
    );

    /**
     * gets the cf module based on the id or its name
     *
     * @param int $module_id
     * @return stdClass - the Module
     * @since 1.9.5
     * @author Sakis Terz
     */
    public static function getModule($module_id = 0)
    {
        if (! isset(self::$module[$module_id])) {
            if (empty($module_id)) {
                $module = JModuleHelper::getModule('mod_cf_filtering');
                // components such as Adv. Module Manager do not allow us to get the module outside certain pages
                if (empty($module->id))
                    $module = self::loadModule($module_id, 'mod_cf_filtering');
            } else {
                $module = self::loadModule($module_id);
            }
            self::$module[$module_id] = $module;
        }
        return self::$module[$module_id];
    }

    /**
     * Load the module from the db
     *
     * @param int $id
     * @param string $name
     * @since 1.9.7
     */
    protected static function loadModule($id = 0, $name = 'mod_cf_filtering')
    {
        if (empty($id))
            $id = 0;
        $key = md5($id . $name);
        if (empty(self::$module[$key])) {
            $input = JFactory::getApplication()->input;
            $Itemid = $input->getInt('Itemid');
            $app = JFactory::getApplication();
            $user = JFactory::getUser();
            $groups = implode(',', $user->getAuthorisedViewLevels());
            $lang = JFactory::getLanguage()->getTag();
            $clientId = (int) $app->getClientId();
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('m.id, m.title, m.module, m.position, m.content, m.showtitle, m.params, mm.menuid');
            $query->from('#__modules AS m');
            $query->join('LEFT', '#__modules_menu AS mm ON mm.moduleid = m.id');
            $query->where('m.published = 1');

            $query->join('LEFT', '#__extensions AS e ON e.element = m.module AND e.client_id = m.client_id');
            $query->where('e.enabled = 1');

            $date = JFactory::getDate();
            $now = $date->toSql();
            $nullDate = $db->getNullDate();

            $query->where('m.access IN (' . $groups . ')');
            $query->where('m.client_id = ' . $clientId);

            if (! empty($id)) {
                $query->where('m.id=' . (int) $id);
            } else {
                if (! empty($name)) {
                    $query->where('m.module=' . $db->quote($name));
                }
            }
            $db->setQuery($query);
            $result = $db->loadObject();
            self::$module[$key] = $result;
        }
        return self::$module[$key] = $result;
    }

    /**
     * Function to get the menu params
     *
     * @since 1.8.0
     * @author Sakis Terz
     */
    public static function getMenuparams()
    {
        if (empty(self::$menuparams)) {
            $app = JFactory::getApplication();
            $menus = $app->getMenu();
            $cfmenus = $menus->getItems('link', 'index.php?option=com_customfilters&view=products');
            $menuparams = new Registry();
            if (empty($cfmenus)) {
                $app->enqueueMessage(JText::_('COM_CUSTOMFILTERS_MENU_ITEM_MISSING'), 'Notice');
            } else {
                $menuparams->loadString($cfmenus[0]->params);
                $menuparams->set('cf_itemid', $cfmenus[0]->id);
            }
            self::$menuparams = $menuparams;
        }
        return self::$menuparams;
    }

    /**
     * Function to get the module's params
     *
     * @since 1.9.0
     * @author Sakis Terz
     */
    public static function getModuleparams($module = '')
    {
        if (! empty($module)) {
            $key = $module->id;
        } else {
            $key = 0;
        }

        if (empty(self::$moduleparams[$key])) {
            if (empty($module))
                $module = self::getModule();
                // maybe its unpublished or not installed
            if (empty($module)) {
                $module = new stdClass();
                $module->params = '';
            }
            $moduleParams = new Registry();
            $moduleParams->loadString($module->params);
            self::$moduleparams[$key] = $moduleParams;
        }
        return self::$moduleparams[$key];
    }

    /**
     * Function to get the component's params
     *
     * @since 1.9.0
     * @author Sakis Terz
     */
    public static function getComponentparams()
    {
        if (empty(self::$componentparams)) {
            self::$componentparams = JComponentHelper::getParams('com_customfilters');
        }
        return self::$componentparams;
    }

    /**
     * Return the url of a specific media
     *
     * @author Sakis Terz
     * @param int $media_id
     * @since 1.7.1
     */
    public static function getMediaFile($media_id)
    {
        if ($media_id) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select("file_title,file_url,file_url_thumb");
            $query->from("#__virtuemart_medias");
            $query->where("file_is_downloadable=0");
            $query->where("published=1");
            $query->where("virtuemart_media_id=$media_id");
            $db->setQuery($query);
            $media = $db->loadObject();

            // if thumb exists in the database
            if (! empty($media->file_url_thumb)) {
                $img_url = $media->file_url_thumb;
                $img_path = JPATH_ROOT . DIRECTORY_SEPARATOR . $img_url;
                if (! file_exists($img_path))
                    $img_url = JURI::base() . 'components/com_virtuemart/assets/images/vmgeneral/' . VmConfig::get('no_image_found', 'noimage.gif');
                else {
                    $img_url = JURI::base() . $img_url;
                }
            }  // if it does not exist as db record check the resized folder
else {
                $img_url = $media->file_url;
                $path_segments = explode('/', $media->file_url);

                // get the file name and leave the rest path
                $filename = array_pop($path_segments);
                $ext = substr($filename, strrpos($filename, '.') + 1);
                $filename_no_extension = strstr($filename, '.', true);

                // create the new filename
                $width = VmConfig::get('img_width', 90);
                $height = VmConfig::get('img_height', 90);
                $resized_filename = $filename_no_extension . '_' . $width . 'x' . $height . '.' . $ext;
                $img_url = implode('/', $path_segments) . '/resized/' . $resized_filename;
                $img_path = JPATH_ROOT . DIRECTORY_SEPARATOR . $img_url;
                if (! file_exists($img_path)) {
                    $img_url = '';
                }
            }
        }

        // no media or media but no matching image
        if (empty($img_url)) {
            $img_url = JURI::base() . 'components/com_virtuemart/assets/images/vmgeneral/' . VmConfig::get('no_image_set');
        }
        $img_prop = getimagesize($img_url);
        $img = new stdClass();

        $img->url = $img_url;
        if (! empty($img_prop) && is_array($img_prop)) {
            $img->width = $img_prop[0];
            $img->height = $img_prop[1];
        }
        return $img;
    }

    /**
     * used to convert the hex custom filter option to normal/dec string
     * It controls also the string format for security reasons
     *
     * @author Sakis Terz
     * @since 1.0
     * @return String
     */
    public static function cfHex2bin($h)
    {
        $filter = JFilterInput::getInstance();
        $h = (string) $h;
        // only hex allowed
        $hex_match = preg_match('/^[a-fA-F0-9]+$/', $h, $matches);

        if ((is_string($h) && $matches[0])) {
            $r = '';
            for ($a = 0; $a < strlen($h); $a += 2) {
                $r .= chr(hexdec($h{$a} . $h{($a + 1)}));
            }
            $r = $filter->clean($r, 'string');
            return $r;
        }
        return;
    }

    /**
     * Convert the hex array to normal stings array
     *
     * @param
     *            Array
     * @return Array
     * @since 1.0
     * @author Sakis Terz
     */
    public static function hex2binArray($array)
    {
        $myArray = array();
        foreach ($array as $h) {
            $r = self::cfHex2bin($h);
            if (! empty($r)) {
                $myArray[] = $r;
            }
        }
        return $myArray;
    }

    /**
     * Convert the hex array to normal stings array
     *
     * @param
     *            array
     *
     * @return array
     * @since 2.2.0
     * @author Sakis Terz
     */
    public static function bin2hexArray($array)
    {
        $myArray = array();
        foreach ($array as $key => $h) {
            $r = bin2hex($h);

            if (! empty($r)) {
                $myArray[$key] = $r;
            }
        }
        return $myArray;
    }

    /**
     * Create an assoc.
     * array with the filter options using as key the value id
     * Also it converts special characters of the label/name to their html equivelants
     *
     * @param
     *            array The object list with the values and the counter
     * @return array values array using as key the value id. We need the key later to check the active/inactive options
     */
    public static function arrayFromValList($valList)
    {
        if (empty($valList)) {
            return;
        }
        $valArray = array();

        foreach ($valList as $val) {
            if (! empty($val->id)) {
                if (! array_key_exists($val->id, $valArray)) {
                    if (! empty($val->name)) {
                        $val->name = CfOutput::getOutput($val->name);
                    }
                    $valArray[$val->id] = $val;
                }
            }

            // some vars e.g. ranges do not use ids and names
            else {
                if (is_object($val) || is_array($val)) {
                    $myval = new stdClass();
                    foreach ($val as $key => $value) {
                        $myval->$key = htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
                    }
                    $valArray[] = $myval;
                    unset($myval);
                } else {
                    if (is_scalar($val))
                        $valArray[] = htmlspecialchars($val, ENT_COMPAT, 'UTF-8');
                }
            }
        }
        return $valArray;
    }

    /**
     * Function to get the existing custom filters
     *
     * The same function exist in the module options_helper.php file
     *
     * @since 1.9
     * @author Sakis Terz
     */
    public static function getCustomFilters($module_params = '')
    {
        if (! empty($module_params)) {
            $store = md5(json_encode($module_params->get('selected_customfilters', array())) . '::' . $module_params->get('cf_ordering', 'cf.ordering') . '::' . $module_params->get('cf_ordering_dir', 'ASC'));
        }  // default
else {
            $store = md5('Array::cf.ordering::ASC');
        }

        if (! isset(self::$_publishedCustomFilters[$store])) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            if (! empty($module_params)) {
                $selected_customfilters = $module_params->get('selected_customfilters', '');
                if (! empty($selected_customfilters)) {
                    $selected_customfilters=ArrayHelper::toInteger($selected_customfilters);
                    $selected_customfilters = array_filter($selected_customfilters);
                }
            }

            // ordering
            $order = ! empty($module_params) ? $module_params->get('cf_ordering', 'cf.ordering') : 'cf.ordering';
            $order_dir = ! empty($module_params) ? $module_params->get('cf_ordering_dir', 'ASC') : 'ASC';

            // table cf_customfields
            $query->select('cf.id AS id');
            $query->select('cf.type_id  AS disp_type');
            $query->select('cf.params AS params');
            $query->select('cf.data_type AS data_type');
            $query->from('#__cf_customfields AS cf');

            // table vituemart_customfields
            $query->select('vmc.virtuemart_custom_id AS custom_id');
            $query->select('vmc.custom_title AS custom_title');
            $query->select('vmc.custom_element AS custom_element');
            $query->select('vmc.field_type AS field_type');
            $query->select('vmc.is_list, vmc.custom_value');

            // joins
            $query->join('INNER', '#__virtuemart_customs AS vmc ON cf.vm_custom_id=vmc.virtuemart_custom_id');
            $query->where('cf.published=1');
            if (! empty($selected_customfilters)) {
                $query->where('vmc.virtuemart_custom_id IN(' . implode(',', $selected_customfilters) . ')');
            }
            $query->order($order . ' ' . $order_dir);
            $db->setQuery($query);
            $cust_filters = $db->loadObjectList();
            $cust_filters = self::setPluginparamsAsAttributes($cust_filters);
            self::$_publishedCustomFilters = array();
            self::$_publishedCustomFilters[$store] = array();

            foreach ($cust_filters as $cf) {
                self::$_publishedCustomFilters[$store][$cf->custom_id] = $cf;
            }
        }
        return self::$_publishedCustomFilters[$store];
    }

    /**
     * If the customfield is plugin then get the plugin params and assign them to the custom filter as object attr.
     *
     * @param array $cust_filters            
     * @return array $cust_filters
     * @since 1.9.0
     */
public static function setPluginparamsAsAttributes($cust_filters)
    {
        if (! is_array($cust_filters))
            return;
        JPluginHelper::importPlugin('vmcustom');
        foreach ($cust_filters as &$customfilter) {
            if ($customfilter->field_type == 'E') {
                $name = $customfilter->custom_element;
                $virtuemart_custom_id = $customfilter->custom_id;
                $dispatcher = JDispatcher::getInstance();
                $product_customvalues_table = '';
                $customvalues_table = '';
                $filter_by_field = '';
                $customvalue_value_field = '';
                $filter_data_type = 'string';
                $sort_by = '';
                $custom_parent_id = 0;
                $value_parent_id_field='parent_id';
                
                $ret = $dispatcher->trigger('onFilteringCustomfilters', array(
                    $name,
                    $virtuemart_custom_id,
                    &$product_customvalues_table,
                    &$customvalues_table,
                    &$filter_by_field,
                    &$customvalue_value_field,
                    &$filter_data_type,
                    &$sort_by,
                    &$custom_parent_id,
                    &$value_parent_id_field
                ));
                
                // all the necessary variables should be there
                if ($ret && 
                    ! empty($product_customvalues_table) && 
                    ! empty($customvalues_table) && 
                    ! empty($filter_by_field) && 
                    ! empty($customvalue_value_field) && 
                    ! empty($filter_data_type) && 
                    ! empty($sort_by)) {
                    $pluginparams = new stdClass();
                    $pluginparams->product_customvalues_table = $product_customvalues_table;
                    $pluginparams->customvalues_table = $customvalues_table;
                    $pluginparams->filter_by_field = $filter_by_field;
                    $pluginparams->filter_data_type = strtolower($filter_data_type);
                    $pluginparams->customvalue_value_field = $customvalue_value_field;
                    $pluginparams->sort_by = $sort_by;
                    $pluginparams->custom_parent_id = $custom_parent_id;
                    $pluginparams->value_parent_id_field = $value_parent_id_field;
                    $customfilter->pluginparams = $pluginparams;
                }
            }
        }
        return $cust_filters;
    }
    /**
     * Get the vendor's accepted currency ids
     *
     * @author Sakis Terz
     * @since 1.4.0
     */
    public static function getVendorCurrency()
    {
        if (empty(self::$vendor_cur)) {
            $db = JFactory::getDbo();
            $q = 'SELECT CONCAT(`vendor_accepted_currencies`, ",",`vendor_currency`) AS all_currencies, `vendor_currency` FROM `#__virtuemart_vendors` WHERE `virtuemart_vendor_id`=1';
            $db->setQuery($q);
            self::$vendor_cur = $db->loadAssoc();
        }
        return self::$vendor_cur;
    }

    /**
     * Get info of the current used currency
     *
     * @author Sakis Terz
     * @since 1.4.0
     * @param integer $curr_id
     */
    public static function getCurrencyInfo($curr_id)
    {
        if (empty($curr_id)) {
            return;
        }
        if (empty(self::$currency_info[$curr_id])) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('currency_symbol, currency_decimal_place, currency_decimal_symbol,currency_positive_style');
            $query->from('#__virtuemart_currencies');
            $query->where('virtuemart_currency_id=' . (int) $curr_id);
            $db->setQuery($query);
            self::$currency_info[$curr_id] = $db->loadObject();
        }
        return self::$currency_info[$curr_id];
    }

    /**
     * Get all the currencies that the products use
     *
     * @author Sakis Terz
     * @since 1.4.0
     */
    public static function getProductCurrencies()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('DISTINCT product_currency');
        $query->from('#__virtuemart_product_prices');
        $query->where('NOT ISNULL(product_currency)');
        $db->setQuery($query);
        $currencies = $db->loadColumn();
        $session = JFactory::getSession();
        $session->set('cf_product_currencies', $currencies);
        return $currencies;
    }

    /**
     * Return the currency string code
     *
     * @param int $cur_id
     * @author Sakis Terz
     * @since 1.4.0
     * @return int
     */
    public function getCurrencyCode($cur_id)
    {
        if (empty(self::$cur_codes[$cur_id])) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('currency_code_3');
            $query->from('#__virtuemart_currencies');
            $query->where('virtuemart_currency_id=' . (int) $cur_id);
            $db->setQuery($query);
            $code = $db->loadResult();
            $cur_codes[$cur_id] = $code;
        }
        return $cur_codes[$cur_id];
    }

    /**
     * Formats the date of the custom fields in way that can be used in the database queries
     *
     * @param
     *            string
     * @return string
     * @since 1.7.1
     */
    public static function getFormatedDate($date)
    {
        $dateFormats = cftools::getDateFormat();
        $dateFormat = $dateFormats['dateFormat_php'];
        $date_ar = explode('-', $date);
        $date_array = array();

        if ($dateFormat == 'd-m-Y') {
            $date_array['d'] = $date_ar[0];
            $date_array['m'] = $date_ar[1];
            $date_array['Y'] = $date_ar[2];
        } else
            if ($dateFormat == 'Y-m-d') {
                $date_array['d'] = $date_ar[2];
                $date_array['m'] = $date_ar[1];
                $date_array['Y'] = $date_ar[0];
            } else
                if ($dateFormat == 'm-d-Y') {
                    $date_array['d'] = $date_ar[1];
                    $date_array['m'] = $date_ar[0];
                    $date_array['Y'] = $date_ar[2];
                }

        $converted_date = date('Y-m-d', mktime(0, 0, 0, $date_array['m'], $date_array['d'], $date_array['Y']));

        return $converted_date;
    }

    /**
     * Get the date format as set in the component params
     *
     * @return array
     */
    public static function getDateFormat()
    {
        if (empty(self::$dateFormat) && empty(self::$dateFormat_php)) {
            $component_params = self::getComponentparams();
            self::$dateFormat_php = $component_params->get('date_format', 'd-m-Y');
            $format_array = explode('-', self::$dateFormat_php);
            self::$dateFormat = '%' . $format_array[0] . '-%' . $format_array[1] . '-%' . $format_array[2];
        }
        return array(
            'dateFormat_php' => self::$dateFormat_php,
            'dateFormat' => self::$dateFormat
        );
    }

    /**
     * set the range vars
     *
     * @param array $rangeVars
     * @since 1.9.0
     */
    public static function setRangeVars($rangeVars)
    {
        self::$rangeVars = $rangeVars;
    }

    /**
     * get the range vars
     *
     * @param array $rangeVars
     * @since 1.9.0
     */
    public static function getRangeVars()
    {
        return self::$rangeVars;
    }

    /**
     * set the range vars
     *
     * @param array $rangeVars
     * @since 1.9.0
     */
    public static function setActiveTree($active_tree)
    {
        self::$active_tree = $active_tree;
    }

    /**
     * get the current active trees
     *
     * @return array
     */
    public static function getActiveTree()
    {
        return self::$active_tree;
    }

    /**
     * Get the currency converter
     *
     * @author Sakis Terz
     * @since 1.4.0
     */
    public function getCurrencyConverter()
    {
        if (empty(self::$currencyConverter)) {
            $converterFile = VmConfig::get('currency_converter_module');

            /* Get the currency plugin */
            if (file_exists(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'currency_converter' . DIRECTORY_SEPARATOR . $converterFile)) {
                $module_filename = substr($converterFile, 0, - 4);
                require_once (JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'currency_converter' . DIRECTORY_SEPARATOR . $converterFile);
                if (class_exists($module_filename)) {
                    $currencyConverter = new $module_filename();
                }
            } else {

                if (! class_exists('convertECB')) {
                    require (JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'currency_converter' . DIRECTORY_SEPARATOR . 'convertECB.php');
                }
                $currencyConverter = new convertECB();
            }
            self::$currencyConverter = $currencyConverter;
        }
        return self::$currencyConverter;
    }

    /**
     * Get and return the calc rules, ordered by the order they are applied
     *
     * @author Sakis Terz
     * @since 1.9.5
     * @return array
     */
    public static function getCalcRules()
    {
        if (empty(self::$calcRules)) {
            $cfinput = CfInput::getInputs();
            $virtuemart_shoppergroup_ids = self::getUserShopperGroups();

            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('calc.virtuemart_calc_id,calc.calc_kind,calc.ordering, calc_value, calc_value_mathop,calc_currency,calcc.virtuemart_category_id,calcm.virtuemart_manufacturer_id');
            $query->from('#__virtuemart_calcs AS calc');
            $query->leftJoin('#__virtuemart_calc_categories AS calcc ON calc.virtuemart_calc_id=calcc.virtuemart_calc_id');
            $query->leftJoin('#__virtuemart_calc_manufacturers AS calcm ON calc.virtuemart_calc_id=calcm.virtuemart_calc_id');
            $query->leftJoin('#__virtuemart_calc_shoppergroups AS calcshopper ON calc.virtuemart_calc_id=calcshopper.virtuemart_calc_id');
            $query->where('calc.published=1');

            /*
             * use only the taxes as global. Is not accurate when there are rules applied to entire categories/manufacturers but works in most cases.
             * While if we get into account all the types is inaccurate in most cases
             */
            $query->where('(calc.calc_kind="Tax" OR calc.calc_kind="VatTax")');
            // $query->where('(calc.calc_kind="Tax" OR calc.calc_kind="VatTax" OR calc.calc_kind="Marge" OR calc.calc_kind="DBTax" OR calc.calc_kind="DATax")');

            // restrictions based on shoppers
            if (! empty($virtuemart_shoppergroup_ids)) {
                $query->where('(calcshopper.`virtuemart_shoppergroup_id` IN (' . implode(',', $virtuemart_shoppergroup_ids) . ') OR (calcshopper.`virtuemart_shoppergroup_id`) IS NULL )');
            }
            else {
                $query->where('calcshopper.`virtuemart_shoppergroup_id` IS NULL');
            }
                // restrictions based on categories
            if (isset($cfinput['virtuemart_category_id'])) {
                $query->where('(calcc.virtuemart_category_id IS NULL OR calcc.virtuemart_category_id IN(' . implode(',', $cfinput['virtuemart_category_id']) . '))');
            }
                // restrictions based on manufacturers
            if (isset($cfinput['virtuemart_manufacturer_id'])) {
                $query->where('(calcm.virtuemart_manufacturer_id IS NULL OR calcm.virtuemart_manufacturer_id IN(' . implode(',', $cfinput['virtuemart_manufacturer_id']) . '))');
            }

            $query->order('FIELD(calc.calc_kind,"Marge","DBTax","Tax","VatTax","DATax"),calc.ordering');
            $db->setQuery($query);
            self::$calcRules = $db->loadObjectList();
        }
        return self::$calcRules;
    }

    /**
     * Creates culc rule groups based on some criteria (e.g.
     * categories, manufacturers)
     * Each group has all the calc rules which are applied to this group of products
     *
     * @since 1.9.5
     * @param array $rules
     * @return array
     */
    public static function createCalcRuleGroups($rules)
    {
        if (empty(self::$calcRulesPerSelection)) {
            $cfinput = CfInput::getInputs();
            $rulesGroup = array();
            $categories = array();
            $manufacturers = array();
            if (isset($cfinput['virtuemart_category_id']))
                $categories = $cfinput['virtuemart_category_id'];
            if (isset($cfinput['virtuemart_manufacturer_id']))
                $manufacturers = $cfinput['virtuemart_manufacturer_id'];
            $counter = count($rules);
            $i = 0;
            $group = array();
            $found = array();

            for ($i = 0; $i < $counter; $i ++) {
                $r = $rules[$i];
                if (! empty($r->virtuemart_category_id)) {
                    if (! isset($group[$r->virtuemart_category_id]))
                        $group[$r->virtuemart_category_id] = array();

                    if (! isset($group[$r->virtuemart_category_id][$r->virtuemart_calc_id])) {
                        $group[$r->virtuemart_category_id][$r->virtuemart_calc_id] = $r;
                        $found[] = $r->virtuemart_calc_id;
                    }
                }
            }

            // now create an array for those that don't have matches
            $global['global'] = array();
            foreach ($rules as $rl) {
                if (empty($rl->virtuemart_category_id))
                    $global['global'][$rl->virtuemart_calc_id] = $rl;
            }

            /*
             * now check if the existing groups are global.
             * The groups are global if there are groups that cover all the selected categories and have exactly the same calc rules.
             * In other words all the categories use the same rules
             */

            $diiference = false;
            $no_of_groups = count($group);
            if ($no_of_groups > 0) {
                $tmp_group = array_values($group);
                // print_r($tmp_group);

                // possibly global.Check if all contain the same calc rules
                if ($no_of_groups == count($categories)) {
                    for ($i = 0; $i < $no_of_groups; $i ++) {
                        $calc_rule_ids = array_keys($tmp_group[$i]);
                        for ($j = $no_of_groups - 1; $j > $i; $j --) {
                            $calc_rule_ids2 = array_keys($tmp_group[$j]);
                            $difference = array_diff($calc_rule_ids, $calc_rule_ids2);
                            if ($diiference)
                                break 2;
                        }
                    }

                    if ($diiference == false) {
                        // just the 1st group and the global. The 1st is the same with the others
                        $groups['global'] = array_merge($global['global'], $tmp_group[0]);
                    } else {
                        $group['global'] = $global['global'];
                        $groups = $group;
                    }
                } else {
                    $group['global'] = $global['global'];
                    $groups = $group;
                }
            } else
                $groups = $global;

                // order them by the type
            $new_groups = array();
            foreach ($groups as $key => $gr) {
                if (! isset($new_groups[$key])) {
                    $new_groups[$key] = array();
                }
                foreach ($gr as $rule) {
                    if (! isset($new_groups[$key][$rule->calc_kind])) {
                        $new_groups[$key][$rule->calc_kind] = array();
                    }
                    $new_groups[$key][$rule->calc_kind][] = $rule;
                }
            }
            self::$calcRulesPerSelection = $new_groups;
        }
        return self::$calcRulesPerSelection;
    }

    /**
     * Get shopper groups of that user
     *
     * @author Sakis Terz
     * @since 1.3
     * @return array
     */
    public static function getUserShopperGroups()
    {
        $user = JFactory::getUser();
        $user_id = $user->id;

        if (empty(self::$shopper_groups[$user_id])) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('virtuemart_shoppergroup_id');
            $query->from('#__virtuemart_vmuser_shoppergroups');
            $query->where('virtuemart_user_id=' . (int) $user_id);
            $db->setQuery($query);
            $user_shopper_groups = $db->loadColumn();

            // 1 is the default group for guests/non registered
            if (empty($user_shopper_groups)) {
                $user_shopper_groups = array(
                    1
                );
            }

            self::$shopper_groups[$user_id] = $user_shopper_groups;
        }
        return self::$shopper_groups[$user_id];
    }

    /**
     * Check if a value is color and format it to be used in css
     *
     * @param string $string
     * @return mixed on suceess, false on failure
     * @see customfieldsforall\helpers\filter.php
     */
    public static function checkNFormatColor($string)
    {
        $string = (string) $string;
        if (empty($string)) {
            return false;
        }

        // check for hexademical
        preg_match('/^[a-f0-9]{6}$/i', $string, $matches);
        $result = @$matches[0];
        if (! empty($result)) {
            return '#' . $result;
        }

        // check for standard color name
        $string = ucfirst($string);
        if (isset(self::$standard_colors[$string])) {
            $result = strtolower($string);
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * Load the necessary scripts and styles for the results
     *
     * @since 2.1.0
     */
    public static function loadScriptsNstyles()
    {
        $document = JFactory::getDocument();

        // use the vm functions for loading scripts and css
        if (! class_exists('VmConfig')) {
            require (JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'config.php');
        }
        $vmVersion = VmConfig::getInstalledVersion();
        VmConfig::loadConfig();

        vmJsApi::cssSite();
        vmJsApi::jPrice();
        echo vmJsApi::writeJS();

        if (VmConfig::get('jdynupdate', true)) {
            vmJsApi::jDynUpdate();
        }
        
        // load the styles for cf4all - usefull when ajax is used to load the results
        if (file_exists(JPATH_BASE . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'vmcustom' . DIRECTORY_SEPARATOR . 'customfieldsforall')) {
            $document->addStyleSheet(JURI::base() . 'plugins/vmcustom/customfieldsforall/assets/css/customsforall_fe.css');
        }
    }

    /**
     * Prints the profiler data to the screen
     *
     * @author Sakis Terz
     * @param
     *            Object The jprofiler instance
     * @since 1.0
     */
    public static function printProfiler($profiler)
    {
        $data = $profiler->getBuffer();
        $counter = count($data);
        $data_str = implode('<hr/>', $data);
        $data_str .= '<hr/><b>Total filters:</b>' . $counter;
        $data_str .= '&nbsp;<b>RAM usage:</b>' . $profiler->getMemory() . ' b';
        echo $data_str;
    }

    /**
     * Initialize the language variables
     */
    protected static function setLanguageVars()
    {
        if (self::$langPrefix == null && self::$defaultLangPrefix == null) {
            if (! defined('VMLANG')) {
                $languages = JLanguageHelper::getLanguages('lang_code');
                $siteLang = $jlang->getTag();
                $siteLang = strtolower(strtr($siteLang, '-', '_'));
            } else {
                $siteLang = VMLANG;
            }
            // Set the shop's default language
            $shop_default_lang = VmConfig::$defaultLang;

            self::$langPrefix = $siteLang;
            self::$defaultLangPrefix = $shop_default_lang;
        }
    }

    /**
     * get the current language's database prefix
     *
     * @return string
     */
    public static function getCurrentLanguagePrefix()
    {
        self::setLanguageVars();
        return self::$langPrefix;
    }

    /**
     * get the current language's database prefix
     *
     * @return string
     */
    public static function getDefaultLanguagePrefix()
    {
        self::setLanguageVars();
        return self::$defaultLangPrefix;
    }

    /**
     * Creates a logger that writes in specific file
     *
     * @author Sakis Terz
     * @since 2.2.9
     */
    public static function addLogger()
    {
        JLog::addLogger(array(

            // Sets file name
            'text_file' => 'customfilters.errors.php'
        ),

            // Sets messages of all log levels to be sent to the file
            JLog::ALL,
            // The log category/categories which should be recorded in this file
            array(
                'customfilters'
            ));
    }
}