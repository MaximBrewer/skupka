<?php
/**
 * @package		customfilters
 * @subpackage	mod_cf_filtering
 * @copyright	Copyright (C) 2012-2018 breakdesigns.net . All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * The class performs checks about whether the filters should be displayed or not
 * 
 * @author sakis
 *
 */
class DisplayManager
{
    /**
     * 
     * @var Registry
     */
    protected $params;
    
    /**
     * 
     * @var array
     */
    protected $selected_flt;
    
    /**
     * 
     * @param Registry $params
     * @param array $selected_flt
     */
    public function __construct(Registry $params, $selected_flt = [])
    {
        $this->params = $params;
        $this->selected_flt = $selected_flt;
    }
    
    /**
     * Control the display rules params for the specified flt to check if a filter should be displayed in the current page
     *
     * @param
     *            string The filter name abbreviation
     * @param
     *            object The params obj
     *
     * @return boolean if allow display else false
     * @author Sakis Terz
     * @since 1.0
     */
    public function getDisplayControl($flt_sfx)
    {
        $disp = false;
        $app = JFactory::getApplication();
        $jinput = $app->input;
    
        // those vars sgould be taken from the jinput becuase they may come from other Component than CF (e.g. VM)
        $option = $jinput->get('option', 'cmd');
        $view = $jinput->get('view', '');
        $vm_cat_id = $jinput->get('virtuemart_category_id', 0, 'array');
        $vm_mnf_id = $jinput->get('virtuemart_manufacturer_id', 0, 'array');
        $vm_prd_id = $jinput->get('virtuemart_product_id', 0, 'array');
        $is_published_param = $flt_sfx . '_published';
        $is_published = $this->params->get($is_published_param);
    
        // always visible in the cf pages
        if ($is_published) {
            if ($option == 'com_customfilters') {
                $disp = true;
            } elseif ($option == 'com_virtuemart') {
                if ($view == 'category' && ! empty($vm_cat_id)) {
                    $param_name = $flt_sfx . '_vm_category_pages';
                } // manufacturer page or the page that comes after selecting a manufacturer (category page)
                elseif (($view == 'manufacturer') || ($view == 'category' && ! empty($vm_mnf_id))) {
                    $param_name = $flt_sfx . '_vm_manuf_pages';
                } elseif ($view == 'productdetails' && $vm_prd_id) {
                    $param_name = $flt_sfx . '_vm_productdetails_pages';
                } // other views
                elseif (($view != 'manufacturer' && $view != 'category' && $view != 'productdetails') || ($view == 'category' && empty($vm_cat_id) && empty($vm_mnf_id))) { // other
                    $param_name = $flt_sfx . '_vm_other_pages';
                }
                if (isset($param_name))
                    $disp = $this->params->get($param_name);
            } else { // non virtuemart pages
                $param_name = $flt_sfx . '_non_vm_pages';
                $disp = $this->params->get($param_name);
            }
    
            /*
             * for the custom filters there is an extra condition
             * display only if other filters are selected
             */
    
            if ($disp) {
                if ($flt_sfx == 'custom_flt')
                    $disp_with_fltrs = $this->params->get('custom_flt_disp_after', '1');
                elseif ($flt_sfx == 'manuf_flt')
                $disp_with_fltrs = $this->params->get('manuf_flt_disp_after', '1');
                else
                    $disp_with_fltrs = 1;
    
                // the keys of the array that contains the selected options
                $selected_filters_keys = array_keys($this->selected_flt);
                $selected_filters_keys_str = implode('|', $selected_filters_keys);
    
                // display always
                if ($disp_with_fltrs == 1)
                    return true;
    
                // display only if category is selected
                elseif ($disp_with_fltrs == 'keyword') {
                    if (isset($this->selected_flt['q']))
                        $disp = true;
                    else
                        $disp = false;
                }
    
                // display only if category is selected
                elseif ($disp_with_fltrs == 'vm_cat') {
                    if (isset($this->selected_flt['virtuemart_category_id']))
                        $disp = true;
                    else
                        $disp = false;
                }
    
                // display only if manuf is selected
                elseif ($disp_with_fltrs == 'vm_manuf') {
                    if (isset($this->selected_flt['virtuemart_manufacturer_id']))
                        $disp = true;
                    else
                        $disp = false;
                }
    
                // display only if a price is selected
                elseif ($disp_with_fltrs == 'price') {
                    if (isset($this->selected_flt['price']))
                        $disp = true;
                    else
                        $disp = false;
                }
    
                // display if category or manuf is selected
                elseif ($disp_with_fltrs == 'keyword_or_vm_cat_or_customfilter') {
                    if (isset($this->selected_flt['q']) || isset($this->selected_flt['virtuemart_category_id']) || isset($this->selected_flt['virtuemart_manufacturer_id']))
                        $disp = true;
                    else
                        $disp = false;
                }
    
                // display if keyword ,category , a manuf or a price is selected
                elseif ($disp_with_fltrs == 'keyword_or_vm_cat_or_vm_manuf') {
                    if (isset($this->selected_flt['q']) || isset($this->selected_flt['virtuemart_category_id']) || isset($this->selected_flt['virtuemart_manufacturer_id']))
                        $disp = true;
                    else
                        $disp = false;
                }
    
                // display if keyword ,category , a manuf or a price is selected
                elseif ($disp_with_fltrs == 'keyword_or_vm_cat_or_vm_manuf_or_price') {
                    if (isset($this->selected_flt['q']) || isset($this->selected_flt['virtuemart_category_id']) || isset($this->selected_flt['virtuemart_manufacturer_id']) || isset($this->selected_flt['price']))
                        $disp = true;
                    else
                        $disp = false;
                }
    
                // display if keyword or category and manuf is selected
                elseif ($disp_with_fltrs == 'keyword_or_vm_cat_and_vm_manuf') {
                    if ((isset($this->selected_flt['q']) || isset($this->selected_flt['virtuemart_category_id'])) && isset($this->selected_flt['virtuemart_manufacturer_id']))
                        $disp = true;
                    else
                        $disp = false;
                } elseif ($disp_with_fltrs == 'keyword_or_vm_cat_or_customfilter') {
                    if (isset($this->selected_flt['q']) || isset($this->selected_flt['virtuemart_category_id']) || strpos($selected_filters_keys_str, 'custom_f_') !== false)
                        $disp = true;
                    else
                        $disp = false;
                }
            }
            return $disp;
        }
        return false;
    }
    
    /**
     * Check if a custom filter should be displayed based on the advanced settings of this filter
     *
     * @param object $cf
     *
     * @since 1.9.0
     */
    public function displayCustomFilter($cf)
    {
        $cfparams = new Registry();
        $cfparams->loadString($cf->params, 'JSON');
        $flt_to_categories = $cfparams->get('filter_category_ids', array());
    
        // there are categories assigned
        if (! empty($flt_to_categories)) {
            if (isset($this->selected_flt['virtuemart_category_id'])) {
                $selected_cat = $this->selected_flt['virtuemart_category_id'];
                foreach ($selected_cat as $cat) {
                    if (in_array($cat, $flt_to_categories)) {
                        return true;
                    }
                }
            }
            return false;
        }
        return true;
    }
}