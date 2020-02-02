<?php
/**
 * @version		$Id: helper.php sakis Terz $2
 * @package		customfilters
 * @subpackage	mod_cf_filtering
 * @copyright	Copyright (C) 2012-2018 breakdesigns.net . All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die();

use \Joomla\Registry\Registry;


/**
 * The module helper class which contains the basic module's logic
 *
 * @package customfilters
 * @author Sakis Terz
 * @since 1.0
 *
 */
class ModCfFilteringHelper
{
    // the selected criteria will be stored in this assoc array
    public $selected_flt = array();
    // stores the selections that each filter uses in dependency top-bottom
    public $selected_fl_per_flt = array();
    // remove the inactive from this array
    public $selected_flt_modif = array();
    // the display types of each filter will be stored in this assoc array
    public $disp_types = array();
    // an assoc array that stores the data of all the filters
    public $filters = array();
    // stores the options that are innactive and selected
    public $inactive_select_opt = array();
    // this array contains the header string of every filter in a different list item
    public $filters_headers_array = array();
    // the option helper class
    public $optHelper;
    // the module parameters
    public $moduleparams;
    // hold the smartSearch options for each filter
    public $smartSearch;
    // hold the expanded/collapsed state of each filter
    public $expanded;
    // it holds info about the current currency
    public $currency_info;
    // styles declaration
    public $stylesDeclaration = '';
    // contains any variable which will be passed to the script
    public $scriptVars = array();
    // contains the script files which will be loaded
    public $scriptFiles = array();
    // contains the functions/operations which will be executed in a domready event
    public $scriptProcesses = array();
    // contains the suffixes of the filters
    public $fltSuffix = array(
        'q' => 'keyword_flt',
        'virtuemart_category_id' => 'category_flt',
        'virtuemart_manufacturer_id' => 'manuf_flt',
        'price' => 'price_flt',
        'custom_f' => 'custom_flt'
    );
    // reset tool active/inactive (bool)
    public $reset = false;
    // text direction
    public $direction = 'ltr';
    // the current module object
    public $module;
    // mode (on click or with btn)
    public $results_trigger;
    // reults loading mode (http or ajax)
    public $results_loading_mode;
    // the current active trees
    public $active_tree = array();
    // array that contains the ranges
    public $rangeVars = array();
    // component params
    public $component_params;
    // menu params (cf menu)
    public $menu_params;

    /**
     * 
     * @param Registry $params
     * @param stdClass $module
     */
    public function __construct(Registry $params, $module)
    {
        $this->moduleparams = $params;
        $this->module = $module;        
        $this->component_params = cftools::getComponentparams();
        $this->menu_params = cftools::getMenuparams();        
        $doc = JFactory::getDocument();
        $Itemid = $this->menu_params->get('cf_itemid', '');        
        $this->results_trigger = $params->get('results_trigger', 'sel');
        $this->results_loading_mode = $params->get('results_loading_mode', 'ajax');
        $this->direction = $doc->getDirection();
        $this->scriptVars['base_url'] = JURI::base();
        $this->scriptVars['Itemid'] = $Itemid;
        $this->scriptVars['component_base_url'] = JRoute::_('index.php?option=com_customfilters&view=products&Itemid=' . $Itemid);
        $this->scriptProcesses[] = 'customFilters.keyword_search_clear_filters_on_new_search=' . $this->component_params->get('keyword_search_clear_filters_on_new_search', true) . '; ';
        $this->scriptVars['cf_direction'] = $this->direction;
        $this->scriptVars['results_trigger'] = $params->get('results_trigger', 'sel');
        $this->scriptVars['results_wrapper'] = $params->get('results_wrapper', 'bd_results');       
        $this->optHelper = new OptionsHelper($params, $module);        
    }
    
    /**
     * The entry point for the filters generation
     *
     * @return array with the inside html code of every filter
     *
     * @author Sakis Terz
     * @since 1.0
     */
    public function getFilters()
    {       
        $japplication = JFactory::getApplication();
        $jinput = $japplication->input;

        if ($this->results_loading_mode == 'ajax' || $this->results_trigger == 'btn') {
            $loadAjaxModule = true;
        }
        else {
            $loadAjaxModule = false;
        }
        
        $this->scriptVars['loadModule'] = $loadAjaxModule;
        $dependency_dir = $this->moduleparams->get('dependency_direction', 'all');

        // profiler to get performance metrics
        $profilerParam = $this->moduleparams->get('cf_profiler', 0);
        if ($profilerParam) {
            $profiler = JProfiler::getInstance('application');
        }

         // this array contains the html of every filter in a different list item
        $filters_rendering_array = array();

        // the selected filters' options array;
        $selected_flt = CfInput::getInputs();

        // selected filters after encoding the output
        $this->selected_flt = $output = CfOutput::getOutput($selected_flt, $escape = true);

        // holds the selections which should be used for each filter,when the dependency is from-top to bottom
        if (count($this->selected_flt) > 0 && $dependency_dir == 't-b') {
            $this->selected_fl_per_flt = CfOutput::getOutput(CfInput::getInputsPerFilter($this->module), $escape = true, $perfilter = true);
        }

        // reset options
        $display_reset_all = $this->moduleparams->get('disp_reset_all', 1);

        // check if reset is active
        $this->reset = $jinput->get('reset', 0, 'int');
        
        $displayManager = new DisplayManager($this->moduleparams, $this->selected_flt);

        // define the filters order
        $filters_order = json_decode(str_replace("'", '"', $this->moduleparams->get('filterlist', '')));
        $filters_order = (array) $filters_order;
        if (empty($filters_order) || ! in_array('virtuemart_category_id', $filters_order) || count($filters_order) != count($this->fltSuffix))
            $filters_order = array(
                'q',
                'virtuemart_category_id',
                'virtuemart_manufacturer_id',
                'price',
                'custom_f'
            );         

        foreach ($filters_order as $filter_key) {

            switch ($filter_key) {

                // --keywords search--
                case 'q':
                    if ($displayManager->getDisplayControl('keyword_flt')) {
                        $display_key = $filter_key . '_' . $this->module->id; // used as key to the html code
                        $keyword_flt = array();
                        $keyword_flt['var_name'] = 'q';
                        $keyword_flt['display'] = 5; // input text
                        $keyword_flt['header'] = JText::_('MOD_CF_KEYWORD');
                        $keyword_flt['type'] = 'string'; // then we will add a validation rule according to the type
                        $keyword_flt['clearType'] = $this->component_params->get('keyword_search_clear_filters_on_new_search', true) ? 'all' : 'this';
                        $keyword_flt['options'] = array();

                        /*
                         * If no header, add a placeholder
                         */
                        if (! $this->moduleparams->get('keyword_flt_display_header', 0)) {
                            $keyword_flt['options'][0]['placeholder'] = JText::_('MOD_CF_KEYWORD_SEARCH_PLACEHOLDER');
                        }
                        else {
                            $this->filters_headers_array[$display_key] = JText::_('MOD_CF_KEYWORD');
                        }

                        $keyword_flt['options'][0]['name'] = 'q';
                        $keyword_flt['options'][0]['value'] = ! empty($this->selected_flt[$filter_key]) ? $this->selected_flt[$filter_key] : '';
                        $keyword_flt['options'][0]['size'] = 30;
                        $keyword_flt['options'][0]['maxlength'] = 40;
                                                
                        $this->expanded[$display_key] = $this->moduleparams->get('keyword_flt_expanded', '1');
                        $this->filters[$filter_key] = $keyword_flt;

                        // profiler
                        if ($profilerParam) {
                            $profiler->mark('keyword');
                        }
                    }
                    break;

                // --Categories--
                case 'virtuemart_category_id':
                    if ($displayManager->getDisplayControl('category_flt')) {

                        $key = $filter_key;
                        $display_key = $key . '_' . $this->module->id; // used as key to the html code

                        // the categories display type
                        $vm_cat_disp_type = $this->moduleparams->get('category_flt_disp_type');
                        $this->disp_types[$key] = $vm_cat_disp_type;

                        // set the header
                        if ($vm_cat_disp_type != 3) {
                            $vmcat_header = JText::_('MOD_CF_CATEGORY');
                        }
                        else {
                            $vmcat_header = JText::_('MOD_CF_CATEGORIES');
                        }
                        
                        if ($vm_cat_disp_type != 1) {
                            $this->smartSearch[$key] = $this->moduleparams->get('category_flt_smart_search', '0');
                        }
                        $this->expanded[$display_key] = $this->moduleparams->get('category_flt_expanded', '1');

                        // create the filter object
                        $this->filters[$key] = $this->setFilter($name = $key, $vmcat_header, false);
                        $this->filters[$key]['clearType'] = 'this';

                        // display headers and some styles only in displays other than select drop down
                        if (isset($this->filters[$key])) {
                            $this->filters_headers_array[$display_key] = $vmcat_header;
                            if ($vm_cat_disp_type != 1) {

                                // set some styles for the category tree
                                if (! $this->moduleparams->get('category_flt_tree_mode', 0)) {
                                    $category_flt_collapsed_icon = $this->moduleparams->get('category_flt_collapsed_icon', '');
                                    $category_flt_expanded_icon = $this->moduleparams->get('category_flt_expanded_icon', '');
                                    $category_flt_icon_position = $this->moduleparams->get('category_flt_icon_position', 'left');

                                    if ($category_flt_collapsed_icon) {

                                        // get the width of the image
                                        $img_size = getimagesize($category_flt_collapsed_icon);
                                        if (is_array($img_size)) {
                                            $img_width = $img_size[0] + 2;
                                        }
                                        else {
                                            $img_width = 16;
                                        }
                                        $style = '';
                                        if ($category_flt_icon_position == 'left') {
                                            $style .= "padding-left:" . $img_width . "px !important;";
                                        } else {
                                            if ($this->direction == 'rtl') {
                                                $style .= "padding-right:" . $img_width . "px !important;";
                                            }
                                            $parent_decl = '#cf_flt_wrapper_virtuemart_category_id_' . $this->module->id . ' .cf_parentOpt{display:block; width:90%;}';
                                        }

                                        // unexpand
                                        $style .= 'background-image:url(' . JURI::base() . $category_flt_collapsed_icon . ') !important;';
                                        $style .= 'background-position:' . $category_flt_icon_position . ' center !important;';
                                        $style .= 'background-repeat:no-repeat !important;';
                                        $this->stylesDeclaration .= '#cf_flt_wrapper_virtuemart_category_id_' . $this->module->id . ' .cf_unexpand{' . $style . '} #cf_flt_wrapper_virtuemart_category_id_' . $this->module->id . ' .cf_unexpand:hover{' . $style . '}';
                                    }
                                    if ($category_flt_expanded_icon) {

                                        // get the width of the image
                                        $img_size = getimagesize($category_flt_expanded_icon);
                                        if (is_array($img_size)) {
                                            $img_width = $img_size[0] + 2;
                                        }
                                        else {
                                            $img_width = 16;
                                        }
                                        $style = '';
                                        if ($category_flt_icon_position == 'left') {
                                            $style .= "padding-left:" . $img_width . "px !important;";
                                        } else {
                                            if ($this->direction == 'rtl') {
                                                $style .= "padding-right:" . $img_width . "px !important;";
                                            }
                                            if (empty($parent_decl)) {
                                                $parent_decl = '#cf_flt_wrapper_virtuemart_category_id_' . $this->module->id . ' .cf_parentOpt{display:block; width:90%;}';
                                            }
                                        }

                                        // expand
                                        $style .= 'background-image:url(' . JURI::base() . $category_flt_expanded_icon . ') !important;';
                                        $style .= 'background-position:' . $category_flt_icon_position . ' center !important;';
                                        $style .= 'background-repeat:no-repeat !important;';
                                        $this->stylesDeclaration .= '#cf_flt_wrapper_virtuemart_category_id_' . $this->module->id . ' .cf_expand{' . $style . '} #cf_flt_wrapper_virtuemart_category_id_' . $this->module->id . ' .cf_expand:hover{' . $style . '}';
                                    }

                                    // styling for all the states
                                    if (! empty($parent_decl)) {
                                        $this->stylesDeclaration .= $parent_decl;
                                    }
                                }
                                // store some params
                                $maxHeight = $this->moduleparams->get('category_flt_scrollbar_after', '');
                                if ($maxHeight) {
                                    $this->stylesDeclaration .= " #cf_list_$display_key { max-height:$maxHeight; overflow:auto; height:auto;}";
                                }
                            }
                        }

                        // profiler
                        if ($profilerParam) {
                            $profiler->mark('vm_categories');
                        }
                    }
                    break;

                // --Manufacturers--
                case 'virtuemart_manufacturer_id':
                    if ($displayManager->getDisplayControl('manuf_flt')) {
                        $key = $filter_key;
                        $display_key = $key . '_' . $this->module->id; // used as key to the html code

                        // -params-
                        $vm_manuf_disp_type = $this->moduleparams->get('manuf_flt_disp_type');
                        $this->disp_types[$key] = $vm_manuf_disp_type;

                        if ($vm_manuf_disp_type != 1) {
                            $this->smartSearch[$key] = $this->moduleparams->get('manuf_flt_smart_search', '1');
                            $maxHeight = $this->moduleparams->get('manuf_flt_scrollbar_after', '');
                            if ($maxHeight)
                                $this->stylesDeclaration .= " #cf_list_$display_key { max-height:$maxHeight; overflow:auto; height:auto;}";
                        }

                        // set the header
                        if ($vm_manuf_disp_type != 3) {
                            $mnf_header = JText::_('MOD_CF_MANUFACTURER');
                        }
                        else {
                            $mnf_header = JText::_('MOD_CF_MANUFACTURERS');
                        }

                        // create the filter object
                        $this->filters[$key] = $this->setFilter($name = $key, $mnf_header, false);
                        $this->filters[$key]['clearType'] = 'this';

                        // display headers only in displays other than select drop down
                        if (isset($this->filters[$key])) {
                            $this->filters_headers_array[$display_key] = $mnf_header;
                            $this->expanded[$display_key] = $this->moduleparams->get('manuf_flt_expanded', '1');
                        }

                        // profiler
                        if ($profilerParam) {
                            $profiler->mark('vm_manufs');
                        }
                    }
                    break;

                // --Price--
                case 'price':
                    if ($displayManager->getDisplayControl('price_flt')) {

                        $display_price_inputs = $this->moduleparams->get('price_flt_disp_text_inputs', '1');
                        $display_price_slider = $this->moduleparams->get('price_flt_disp_slider', '1');
                        $display_key = $filter_key . '_' . $this->module->id;

                        if ($display_price_inputs || $display_price_slider) {

                            if ($display_price_inputs) {
                                $this->scriptProcesses[] = "customFilters.addEventsRangeInputs('$filter_key', {$this->module->id});";
                            }

                            $joomla_conf = JFactory::getConfig();
                            $joomla_sef = $joomla_conf->get('sef');
                            $this->scriptVars['cfjoomla_sef'] = $joomla_sef;
                            $this->scriptVars[$display_key . '_display_price_slider'] = $display_price_slider;
                            $this->scriptVars[$display_key . '_display_price_inputs'] = $display_price_inputs;

                            $vendor_currency = cftools::getVendorCurrency();
                            $virtuemart_currency_id = $jinput->get('virtuemart_currency_id', $vendor_currency['vendor_currency'], 'int');
                            $currency_id = $japplication->getUserStateFromRequest("virtuemart_currency_id", 'virtuemart_currency_id', $virtuemart_currency_id);
                            $this->currency_info = cftools::getCurrencyInfo($currency_id);
                            if (! empty($this->currency_info)) {
                                $this->scriptVars['currency_decimal_symbol'] = $this->currency_info->currency_decimal_symbol;
                            }
                                /*
                             * we are generating the vars that generates the setFilter function.
                             * This way we can use the renderFilters function later to render the price filter
                             */

                            if ($display_price_inputs && ! $display_price_slider) {
                                $price_flt_disp_type = 5; // range input
                            }
                            else
                                if ($display_price_inputs && $display_price_slider) {
                                    $price_flt_disp_type = '5,6';
                                } else
                                    $price_flt_disp_type = 6; // slider

                            $min_range = $this->moduleparams->get('price_flt_slider_min_value', '0');
                            $max_range = $this->moduleparams->get('price_flt_slider_max_value', '300');
                            /*
                             * find the dynamic price ranges of the displayed products
                             */
                            if ($display_price_slider && $this->moduleparams->get('price_flt_dynamic_ranges', 0)) {
                                if (count($selected_flt) == 0 || (count($selected_flt) == 1 && ! empty($selected_flt['price'])));
                                else
                                    $ranges = $this->optHelper->getRelativePriceRanges();
                                if (! empty($ranges->min_value))
                                    $min_range = $ranges->min_value;
                                if (! empty($ranges->max_value))
                                    $max_range = $ranges->max_value;
                                if ($min_range == $max_range)
                                    $min_range = 0;
                            }

                            $this->disp_types[$filter_key] = $price_flt_disp_type;
                            $price_header = JText::_('MOD_CF_PRICE');
                            $cf_price_size = 6;
                            $cf_price_maxlength = 13;
                            $price_flt = array();
                            $price_flt['var_name'] = 'price';
                            $price_flt['display'] = $price_flt_disp_type; // input text
                            $price_flt['header'] = $price_header;
                            $price_flt['type'] = 'float'; // then we will add a validation rule according to the type
                            $price_flt['clearType'] = 'this';
                            $price_flt['options'] = array();

                            // from
                            $price_flt['options'][0]['name'] = 'price[0]';
                            $price_flt['options'][0]['value'] = ! empty($this->selected_flt[$filter_key][0]) ? $this->selected_flt[$filter_key][0] : '';
                            $price_flt['options'][0]['label'] = '';
                            $price_flt['options'][0]['size'] = $cf_price_size;
                            $price_flt['options'][0]['maxlength'] = $cf_price_maxlength;
                            $price_flt['options'][0]['slider_min_value'] = $min_range;
                            // to
                            $price_flt['options'][1]['name'] = 'price[1]';
                            $price_flt['options'][1]['value'] = ! empty($this->selected_flt[$filter_key][1]) ? $this->selected_flt[$filter_key][1] : '';
                            $price_flt['options'][1]['label'] = JText::_('MOD_CF_RANGE_TO');
                            $price_flt['options'][1]['size'] = $cf_price_size;
                            $price_flt['options'][1]['maxlength'] = $cf_price_maxlength;
                            $price_flt['options'][1]['slider_max_value'] = $max_range;

                            $this->filters_headers_array[$display_key] = $price_header;
                            $this->expanded[$display_key] = $this->moduleparams->get('price_flt_expanded', '1');
                            $this->filters[$filter_key] = $price_flt;
                        }

                        // profiler
                        if ($profilerParam)
                            $profiler->mark('price_flt');
                    }
                    break;

                // --Custom filters--
                case 'custom_f':
                    if ($displayManager->getDisplayControl('custom_flt')) {
                        $custom_flt = cftools::getCustomFilters($this->moduleparams);
                        $cf_range_size = 6;
                        $cf_range_maxlength = 5;
                        $vm_cust_filters_html = array();

                        // get the options
                        foreach ($custom_flt as $cf) {
                            $var_name = "custom_f_$cf->custom_id";
                            $key = $var_name;
                            $display_key = $key . '_' . $this->module->id; // used as key to the html code
                                                                     // load the params of that cf
                            $cfparams = new JRegistry();
                            $cfparams->loadString($cf->params, 'JSON');

                            // no smart search and scrollbar to drop-downs
                            if ($cf->disp_type != 1) {
                                $maxHeight = $cfparams->get('scrollbar_after', '');
                                if ($maxHeight) {
                                    $this->stylesDeclaration .= " #cf_list_$display_key { max-height:$maxHeight; overflow:auto; height:auto;}";
                                }
                                if ($cf->disp_type != 9 && $cf->disp_type != 10) {
                                    $this->smartSearch[$key] = $cfparams->get('smart_search', '0'); // no color buttons
                                }
                            }

                            $this->expanded[$display_key] = $cfparams->get('expanded', '1');
                            $this->disp_types[$var_name] = $cf->disp_type;

                            // selectable types
                            if ($cf->disp_type != 5 && $cf->disp_type != 6 && $cf->disp_type != 8) {
                                $filter = $this->setFilter($name = $var_name, $header = JText::_($cf->custom_title), $cf, $encoded_varID = true);
                                if (! empty($filter['options'])) {
                                    $filter['clearType'] = 'this';
                                    $this->filters[$key] = $filter;
                                }
                            }

                            // range
                            else {
                                // check if it should be displayed based on the filter's settings
                                if (! $displayManager->displayCustomFilter($cf)) {
                                    continue;
                                }
                                
                                if ($cf->disp_type == 6) {
                                    // the renderer creates the inputs and the sliders
                                }
                                if ($cf->disp_type == 5)
                                    $this->scriptProcesses[] = "customFilters.addEventsRangeInputs('$key', {$this->module->id});";

                                if ($cf->disp_type == 8) {
                                    // the renderer creates the inputs and the calendars
                                }

                                // general vars
                                $cf_range['var_name'] = $var_name;
                                $cf_range['display'] = $cf->disp_type;
                                $cf_range['header'] = JText::_($cf->custom_title);
                                $cf_range['type'] = 'int'; // then we will add a validation rule according to the type
                                $cf_range['clearType'] = 'this';
                                $cf_range['options'] = array();

                                // from
                                $cf_range['options'][0]['size'] = $cf_range_size;
                                $cf_range['options'][0]['name'] = $var_name . '[0]';
                                $cf_range['options'][0]['value'] = ! empty($this->selected_flt[$var_name][0]) ? $this->selected_flt[$var_name][0] : '';
                                $cf_range['options'][0]['maxlength'] = $cf_range_maxlength;
                                $cf_range['options'][0]['slider_min_value'] = $cfparams->get('slider_min_value', 0);

                                // to
                                $cf_range['options'][1]['size'] = $cf_range_size;
                                $cf_range['options'][1]['name'] = $var_name . '[1]';
                                $cf_range['options'][1]['value'] = ! empty($this->selected_flt[$var_name][1]) ? $this->selected_flt[$var_name][1] : '';
                                $cf_range['options'][1]['label'] = JText::_('MOD_CF_RANGE_TO');
                                $cf_range['options'][1]['maxlength'] = $cf_range_maxlength;
                                $cf_range['options'][1]['slider_max_value'] = $slider_max_value = $cfparams->get('slider_max_value', 300);

                                $this->filters[$key] = $cf_range;
                            }

                            // display headers only in displays other than select drop down
                            if (isset($this->filters[$var_name])) {
                                $this->filters_headers_array[$display_key] = JText::_($cf->custom_title);
                            }

                            // profiler
                            if ($profilerParam)
                                $profiler->mark($var_name);
                        }
                    }
                    break;
            } // switch
        } // foreach

        // profiler print metrics
        if ($profilerParam)
            cftools::printProfiler($profiler);

        if (count($this->filters) > 0) {
            $parentScript = '';
            $this->scriptVars['parent_link'] = $this->moduleparams->get('category_flt_parent_link', 0);

            // set ajax spinners
            if ($this->moduleparams->get('use_ajax_spinner', '')) {
                $spinnerstyle = 'background-image:url(' . JURI::base() . $this->moduleparams->get('use_ajax_spinner', '') . ') !important;';
                $spinnerstyle .= 'background-position:center center;';
                $spinnerstyle .= 'background-repeat:no-repeat !important;';
                $this->stylesDeclaration .= '.cf_ajax_loader{' . $spinnerstyle . '}';
                $ajax_module_spinner = 1;
            } else {
                $ajax_module_spinner = 0;
            }

            if ($this->moduleparams->get('use_results_ajax_spinner', '')) {
                $spinnerstyle = 'background-image:url(' . JURI::base() . $this->moduleparams->get('use_results_ajax_spinner', '') . ') !important;';
                $spinnerstyle .= 'background-repeat:no-repeat !important;';
                $this->stylesDeclaration .= '#cf_res_ajax_loader{' . $spinnerstyle . '}';
                $ajax_results_spinner = 1;
            } else {
                $ajax_results_spinner = 0;
            }

            $this->scriptVars['mod_type'] = 'filtering';
            $this->scriptVars['use_ajax_spinner'] = $ajax_module_spinner;
            $this->scriptVars['use_results_ajax_spinner'] = $ajax_results_spinner;
            $this->scriptVars['results_loading_mode'] = $this->moduleparams->get('results_loading_mode', 'http');
            $this->scriptVars['category_flt_parent_link'] = $this->moduleparams->get('category_flt_parent_link', 0);

            if ($dependency_dir == 't-b') {}
            else {
                $this->selected_flt_modif = $this->removeInactiveOpt();
            }

                // ----------render the filters------------------//
            $selected_flt = [
                'selected_flt' => $this->selected_flt,
                'selected_flt_modif' => $this->selected_flt_modif,
                'selected_fl_per_flt' => $this->selected_fl_per_flt
            ];

            $renderer = new ModCfilteringRender($this->module, $selected_flt, $this->filters);
            $filters_html = $renderer->renderFilters();
            $render_scriptAssets = $renderer->getScriptAssets();
            $this->scriptProcesses = array_merge($this->scriptProcesses, $render_scriptAssets['scriptProcesses']);
            $this->scriptFiles = array_merge($this->scriptFiles, $render_scriptAssets['scriptFiles']);
            $this->scriptVars = array_merge($this->scriptVars, $render_scriptAssets['scriptVars']);

            $filters_rendering_array['html'] = $filters_html;
            $category_flt_tree_mode = $this->moduleparams->get('category_flt_tree_mode', '0');

            /*
             * Use event delegation
             * only in non-ajax requests - otherwise these events will be assigned multiple times
             */
            if (($this->results_trigger == 'btn' || $this->results_loading_mode == 'ajax') && ($jinput->get('view', '') != 'module' || $jinput->get('option', '') != 'com_customfilters')) {

                $this->scriptProcesses[] = "customFilters.assignEvents({$this->module->id});";
                if ($category_flt_tree_mode == false) {
                    $this->scriptProcesses[] = "customFilters.addEventTree({$this->module->id});";
                }
            } else
                if (! ($this->results_trigger == 'btn' || $this->results_loading_mode == 'ajax') && $category_flt_tree_mode == false) {
                    $this->scriptProcesses[] = "customFilters.addEventTree({$this->module->id});";
                }

                // script/styles declarations
            if (! empty($this->stylesDeclaration)) {
                $filters_rendering_array['stylesDeclaration'] = $this->stylesDeclaration;
            }
            $filters_rendering_array['selected_flt'] = $this->selected_flt;

            // only in non-ajax requests - otherwise will have the files into dom multiple times
            if (! empty($this->scriptFiles) && ($jinput->get('view', '') != 'module' && $jinput->get('option', '') == 'com_customfilters') || ($jinput->get('option', '') != 'com_customfilters')) {
                $filters_rendering_array['scriptFiles'] = $this->scriptFiles;
            }
            if (! empty($this->scriptVars)) {
                $filters_rendering_array['scriptVars'] = $this->scriptVars;
            }
            if (! empty($this->scriptProcesses)) {
                $filters_rendering_array['scriptProcesses'] = $this->scriptProcesses;
            }
            // reset tool
            if ($display_reset_all && ! empty($this->selected_flt)) {
                $urlHanlder = new UrlHandler($this->module, $selected_flt);
                $filters_rendering_array['resetUri'] = $urlHanlder->getResetUri();
            }
            $filters_rendering_array['expanded_state'] = $this->expanded;
        }
        return $filters_rendering_array;
    }

    /**
     * This function creates an assoc array with all the available filters
     * The created array will have this form
     * array('fltname1'=>array('disptype'=>string,'header'=>string,'smartSearch'=>boolean, options'=>array('0'=>array('label'=>string,'id'=>int,'enabled'=>int,'1'=>array('label'=>string,'id'=>int,...'n')))
     *
     * @param
     *            string The name of the variable which will be used in the filtering form
     * @param
     *            string The header of the filter
     * @param
     *            string Used only for custom filters.Indicates the type of the custom field
     * @param
     *            boolean Indicates if a filter contains strings. In this case they should be encoded
     * @author Sakis Terz
     * @since 1.0
     */
    public function setFilter($var_name, $header, $customfilter = null, $encoded_var = false)
    {
        $activeOptions = array();
        $on_category_reset_others = false;
        $getActive = false;
        $is_customfield = strpos($var_name, 'custom_f_');
        $activeArray = array();
        $has_active_opt = false;
        $selected_array = array();
        $clear_opt = array();
        $maxLevel = 0;
        $only_sub_cats = false;

        // add the counter settings
        if ($is_customfield !== false) {
            $field_key = 'custom_f';
        }
        else {
            $field_key = $var_name;
        }

        $suffix = $this->fltSuffix[$field_key];
        $dependency_direction = $this->moduleparams->get('dependency_direction', 'all');

        $displayCounter = $this->moduleparams->get($suffix . '_display_counter_results', '1');
        $display_empty_opt = $this->moduleparams->get($suffix . '_disable_empty_filters', '1');

        $reset_type = $this->component_params->get('reset_results', 0);
        if ($dependency_direction == 't-b') {
            if (isset($this->selected_fl_per_flt[$var_name])) {
                $selected_flt = $this->selected_fl_per_flt[$var_name];
            }
            else {
                $selected_flt = array();
            }
        } else {
            $selected_flt = $this->selected_flt;
        }

        if ($var_name == 'virtuemart_category_id') {
            $category_flt_tree_mode = $this->moduleparams->get('category_flt_tree_mode', '0');
            $cat_ordering = $this->moduleparams->get('categories_disp_order', 'tree');
            $on_category_reset_others = $this->moduleparams->get('category_flt_onchange_reset', 'filters');

            // if we should return only the subcats
            $only_sub_cats = $this->moduleparams->get('category_flt_only_subcats', false);

            // Use cache - if the category tree is always the same
            if (($on_category_reset_others == 'filters_keywords' || $on_category_reset_others == 'filters') && ! $only_sub_cats) {
                $caching = true;
                $category_ids = '';
                $disp_vm_cat = $this->moduleparams->get('category_flt_disp_vm_cat', '');
                $excluded_vm_cat = $this->moduleparams->get('category_flt_exclude_vm_cat', '');
                $display_empty_opt = $this->moduleparams->get('category_flt_disable_empty_filters', '1');
                $virtuemart_shoppergroup_ids = cftools::getUserShopperGroups();
                $q = ! empty($selected_flt['q']) ? $selected_flt['q'] : '';

                if (! empty($selected_flt['virtuemart_category_id']))
                    $category_ids = implode(',', $selected_flt['virtuemart_category_id']);

                $cahche_id = serialize('fltObj::' . $var_name . implode(',', $virtuemart_shoppergroup_ids) . $disp_vm_cat . $excluded_vm_cat . $display_empty_opt . $q . $displayCounter . $category_ids);

                // 6 minutes if we have counter - 60 if we do not have
                $cacheTime = $displayCounter ? 6 : 60;

                $cache = JFactory::getCache('mod_cf_filtering.categories', '');
                $cache->setCaching(true);
                $cache->setLifeTime($cacheTime);
                $results = $cache->get($cahche_id);
                if (! empty($results)) {
                    return $results;
                }
            }
        }

        $thereIsSelection = ! empty($selected_flt);

        /*
         * Get the options of that filter from the relevant function that does not intersect with other filters
         *
         * in case there is no selection
         * or the only selection is the current filter
         * or the display type is "all as enabled"
         * or the dependency is top-to-bottom and its the 1st filter from top
         * or reset filters on category change - for the categories filter
         */
        if (! $thereIsSelection || ($thereIsSelection && isset($selected_flt[$var_name]) && count($selected_flt) == 1) || $display_empty_opt == '2' || $on_category_reset_others == 'filters_keywords' || ($on_category_reset_others == 'filters' && empty($selected_flt['q']))) {
            $results = $this->optHelper->getOptions($var_name, $customfilter);

            if ($var_name == 'virtuemart_category_id') {
                $options_ar = $results['options'];
                $maxLevel = $results['maxLevel'];
            } else
                $options_ar = $results;

                /*
             * In case of display type=(2)"all as enabled" and the displayCounter is true
             * We should run the getActiveOptions to get the counter relative to the selected filters
             * This should happen only if there are selections in other filters
             */
            if ($display_empty_opt == '2' && $options_ar && ($thereIsSelection && (empty($selected_flt[$var_name]) || count($selected_flt) > 1)) && $displayCounter == true) {
                $activeOptions = $this->optHelper->getActiveOptions($var_name, $customfilter);
                $getActive = true;
            }
        }

        // hide disabled
        elseif ($display_empty_opt == '0') {
            $options_ar = $this->optHelper->getActiveOptions($var_name, $customfilter, $joinFieldData = true);

            // when we have category tree we should get all the categories as all the parents should be active when they have sub-categories
            if ($var_name == 'virtuemart_category_id' && $cat_ordering == 'tree') {
                $results = $this->optHelper->getOptions($var_name, $customfilter);
                $maxLevel = $results['maxLevel'];
                if ($maxLevel > 0) {
                    $categories = $results['options'];
                    $options_ar = $this->createTree($categories, $options_ar, $maxLevel);
                }
            }
        }

        // display disabled as disabled
        elseif ($display_empty_opt == '1') {
            $results = $this->optHelper->getOptions($var_name, $customfilter);
            if ($var_name == 'virtuemart_category_id') {
                $options_ar = $results['options'];
                $maxLevel = $results['maxLevel'];
            } else
                $options_ar = $results;

            if ($options_ar) {
                $activeOptions = $this->optHelper->getActiveOptions($var_name, $customfilter);
                $getActive = true;
            }
        }

        // give to each option the necessary properties
        if (is_array($options_ar) && count($options_ar) > 0) {
            $disp_type = $this->disp_types[$var_name];
            $displaySelectedOnTop = false;

            // display on top only for checkboxes , when they exceed a certain nr and the filter is not category
            if ($var_name != 'virtuemart_category_id' && $disp_type == 3 && count($options_ar) > 10)
                $displaySelectedOnTop = $this->moduleparams->get('disp_selected_ontop', '1');

            $custom_flt_disp_empty = $this->moduleparams->get('custom_flt_disp_empty', '0');
            $disp_clear_tool = $this->moduleparams->get('disp_clear', '1');

            // get the active option of the filter
            // if the param is show as disabled
            // in every other case the $options_ar will contain the options that should be displayed
            // if($display_empty_opt=='1' && $thereIsSelection)$activeOptions=$this->optHelper->getActiveOptions($var_name);

            // when it returns true all are active
            if ($activeOptions === true) {
                $activeOptions = array();
            }

            $act_opt_counter = count($activeOptions);
            $filters[$var_name] = array();
            $filters[$var_name]['var_name'] = $var_name;
            $filters[$var_name]['display'] = $disp_type;
            $filters[$var_name]['header'] = $header;
            $filters[$var_name]['smartSearch'] = isset($this->smartSearch[$var_name]) ? $this->smartSearch[$var_name] : false;

            // display counter setting
            $filters[$var_name]['dispCounter'] = $displayCounter;
            $filters[$var_name]['options'] = array();

            // generate the 1st null/clear option
            if (in_array($disp_type, array(
                1,
                2,
                4,
                7
            )) || (in_array($disp_type, array(
                3,
                9,
                10,
                11,
                12
            )) && $disp_clear_tool == 1 && isset($selected_flt[$var_name]))) {
                $filters[$var_name]['options'][0] = array();
                $filters[$var_name]['options'][0]['id'] = '';
                $filters[$var_name]['options'][0]['active'] = true;

                /*
                 * If the "reset all" returns no products and is the only filter to be reset, then display the none as 1st option's label
                 * Otherwise display the Any as 1st option's label
                 */
                if ($disp_type != 3 && $disp_type != 10 && $disp_type != 12) {
                    if ($reset_type == 0 && (count($selected_flt) == 0 || (count($selected_flt) == 1 && ! empty($selected_flt[$var_name])))) {
                        $filters[$var_name]['options'][0]['label'] = JText::sprintf('MOD_CF_NONE', $header);
                    } else {
                        $filters[$var_name]['options'][0]['label'] = JText::sprintf('MOD_CF_ANY_HEADER', $header);
                    }
                } else {
                    $filters[$var_name]['options'][0]['label'] = JText::_('MOD_CF_CLEAR');
                }
                $selected = 0;
                $type = "clear";

                // if no selection set as default
                if (! isset($selected_flt[$var_name]) || count($selected_flt[$var_name]) == 0) {
                    $selected = 1;
                }
                $filters[$var_name]['options'][0]['type'] = $type;
                $filters[$var_name]['options'][0]['selected'] = $selected;
            }

            // store the inactive selected too
            $innactive_selected = array();
            $i = 1;

            // there is also the 1st null option in some cases
            foreach ($options_ar as $key => $opt) {
                $filters[$var_name]['options'][$key] = array();
                $filters[$var_name]['options'][$key]['id'] = $opt->id;
                $filters[$var_name]['options'][$key]['label'] = $opt->name;
                $filters[$var_name]['options'][$key]['selected'] = 0;
                $filters[$var_name]['options'][$key]['type'] = 'option';

                // set media/images
                if (! empty($opt->media_id))
                    $filters[$var_name]['options'][$key]['media_id'] = $opt->media_id;

                    // in case of category tree we need some more properties for the tree
                if ($var_name == 'virtuemart_category_id' && $cat_ordering == 'tree' && $disp_type != 1 && $maxLevel > 0) {
                    if (isset($opt->level))
                        $filters[$var_name]['options'][$key]['level'] = $opt->level;
                    if (isset($opt->cat_tree))
                        $filters[$var_name]['options'][$key]['cat_tree'] = $opt->cat_tree;
                    if (isset($opt->isparent))
                        $isparent = $opt->isparent;
                    else
                        $isparent = false;
                    $filters[$var_name]['options'][$key]['isparent'] = $isparent;
                    $filters[$var_name]['options'][$key]['parent_id'] = $opt->category_parent_id;
                }

                $select_opt = false;

                // check if selected
                if (isset($selected_flt[$var_name])) {
                    $opt_id = $opt->id;
                    if (in_array($opt_id, $selected_flt[$var_name])) {
                        $select_opt = true;
                    }
                }

                // when there are active options , get the counter from the getActiveOptions function
                // this happens only when the display empty type is:"display as disabled" or "display as enabled" and there is a selection in another filter
                if ($getActive) {
                    if (isset($activeOptions[$opt->id]) || ! empty($opt->isparent)) {
                        if ($filters[$var_name]['dispCounter'] && isset($activeOptions[$opt->id]->counter))
                            $filters[$var_name]['options'][$key]['counter'] = $activeOptions[$opt->id]->counter;
                        $filters[$var_name]['options'][$key]['active'] = true;
                        $has_active_opt = true;
                        $activeArray[] = $opt->id;
                    } else {
                        if ($filters[$var_name]['dispCounter'])
                            $filters[$var_name]['options'][$key]['counter'] = 0;
                        $filters[$var_name]['options'][$key]['active'] = 0;
                        if ($select_opt)
                            $innactive_selected[] = $opt->id;

                            // when all are enabled
                        if ($display_empty_opt == '2') {
                            $filters[$var_name]['options'][$key]['active'] = 1;
                            if (isset($opt->counter) && $opt->counter > 0)
                                $has_active_opt = true;
                            $activeArray[] = $opt->id;
                        }
                    }
                } else {
                    if ($filters[$var_name]['dispCounter'] && isset($opt->counter)) {
                        $filters[$var_name]['options'][$key]['counter'] = $opt->counter;
                        if ((isset($opt->counter) && $opt->counter > 0) || $display_empty_opt == '2') {
                            $filters[$var_name]['options'][$key]['active'] = 1;
                            $activeArray[] = $opt->id;
                            if (isset($opt->counter) && $opt->counter > 0)
                                $has_active_opt = true;
                        } else {
                            if (! empty($opt->isparent)) {
                                $filters[$var_name]['options'][$key]['active'] = 1;
                                unset($filters[$var_name]['options'][$key]['counter']);
                            } else
                                $filters[$var_name]['options'][$key]['active'] = 0;
                            if ($select_opt)
                                $innactive_selected[] = $opt->id;
                        }
                    }

                    // when there is no counter and there is no selection - all are active
                    else {
                        if (! empty($opt->emptyParent) && $disp_type == 1)
                            $filters[$var_name]['options'][$key]['active'] = false;
                        else {
                            $filters[$var_name]['options'][$key]['active'] = 1;
                            $activeArray[] = $opt->id;
                            $has_active_opt = true;
                        }
                    }
                }

                if ($select_opt) {
                    $filters[$var_name]['options'][$key]['selected'] = 1;
                    $opt = $filters[$var_name]['options'][$key];

                    if (isset($opt['cat_tree'])) {
                        $opt_tree = $opt['cat_tree'] . '-' . $opt['id'];
                        if (! in_array($opt_tree, $this->active_tree)) {

                            // used by the tree (categories), to indicate the selected category's tree
                            $this->active_tree[] = $opt_tree;
                        }
                    }

                    // if set selected on top unset it now and put later at the top
                    if ($displaySelectedOnTop) {
                        if (isset($filters[$var_name]['options'][0])) {
                            $selected_array[0] = $filters[$var_name]['options'][0];
                            unset($filters[$var_name]['options'][0]);
                        }
                        $selected_array[$opt['id']] = $opt;
                        unset($filters[$var_name]['options'][$key]);
                    }
                }
                $i ++;
            }

            // set the active tree for that filter
            $filters[$var_name]['active_tree'] = $this->active_tree;

            /*
             * if there are active subtrees, can be autoexpanded
             * But can happen only:
             * in categories
             * When the there are active options
             * When there is no category selected
             * When the setting for auto-expand is active
             * When the categories reset other filters. i.e. can be affected by the search
             * When there is search
             */
            if ($var_name == 'virtuemart_category_id' && ! empty($activeArray) && empty($this->active_tree) && $this->moduleparams->get('categories_disp_order', 'tree') == 'tree' && $this->moduleparams->get('category_flt_auto_expand_subtrees', '1') && $this->moduleparams->get('category_flt_onchange_reset', 'filters') == 'filters' && ! empty($selected_flt['q'])) {
                $filters[$var_name]['active_tree'] = $this->getActiveSubtrees($activeArray, $filters[$var_name]['options']);
            }

            // there is a param for custom filters-to hide them if all are inactive
            if ($is_customfield !== false && $custom_flt_disp_empty == false && (empty($activeArray) || $has_active_opt == false)) {} else {
                // put selected on top
                if (! empty($selected_array)) {
                    $options = $selected_array + $filters[$var_name]['options'];
                    $filters[$var_name]['options'] = $options;
                }

                // check for inactive selected
                if (! empty($activeArray) && ! empty($selected_flt[$var_name])) {
                    $innactive_selected = array_diff($selected_flt[$var_name], $activeArray);
                }
                if (count($innactive_selected) > 0) {
                    $filters[$var_name]['inactive_select_opt'] = $innactive_selected;
                }
            }

            if (! empty($caching) && ! empty($cahche_id))
                $cache->store($filters[$var_name], $cahche_id);
            return $filters[$var_name];
        } // if count
    }

    /**
     * Get active subtrees
     *
     * @param array $activeArray
     *            active options
     * @param array $options
     *            the options
     * @since 2.2.1
     */
    function getActiveSubtrees($activeArray, $options)
    {
        // all are active
        if (count($activeArray) == count($options) - 1)
            return false;
        foreach ($options as $opt) {

            // if is active and not parent enable that subtree
            if ($opt['active'] && empty($opt['isparent'])) {
                if (isset($opt['cat_tree'])) {
                    $opt_tree = $opt['cat_tree'] . '-' . $opt['id'];
                    if (! in_array($opt_tree, $this->active_tree)) {

                        // used by the tree (categories), to indicate the selected category's tree
                        $this->active_tree[] = $opt_tree;
                    }
                }
            }
        }
        return $this->active_tree;
    }

    /**
     * It creates a tree (e.g.
     * Categories), enabling also the parents of the active options
     * This way the user can reach the active options in the tree depth
     *
     * @author Sakis Terz
     * @param
     *            All the options
     * @param
     *            The active options
     * @param
     *            The higher level
     * @return Array
     * @since 1.6.0
     */
    function createTree($options, $activeOptions, $maxLevel)
    {
        // if all are active it will be true
        if (! is_array($activeOptions))
            $activeOptions = array();
        $parent_categories = array();
        $parent_categories2 = array();
        $activeKeys = array_keys($activeOptions);

        // find the parents of the active
        foreach ($activeOptions as $aOpt) {
            if ($aOpt->category_parent_id > 0) {
                $parent_id = $aOpt->category_parent_id;
                $parent = $options[$parent_id];
                while ($parent_id > 0) {
                    if (! in_array($aOpt->category_parent_id, $activeKeys))
                        $parent_categories[] = $parent_id; // stores the parents which are active
                    $parent_categories2[] = $parent_id; // stores the parents of the active children
                    $parent_id = $parent->category_parent_id;
                    if ($parent_id > 0)
                        $parent = $options[$parent_id];
                }
            }
        }

        foreach ($options as $key => &$opt) {

            // unset those which are inactive or non parents of the active
            if (! in_array($opt->id, $activeKeys) && ! in_array($opt->id, $parent_categories)) {
                unset($options[$key]);
            } else {
                if (isset($activeOptions[$key]) && isset($activeOptions[$key]->counter))
                    $opt->counter = $activeOptions[$key]->counter;

                    // indicates that it is displayed only because its parent and is not included in the active options
                if (in_array($opt->id, $parent_categories) && ! in_array($opt->id, $activeKeys))
                    $opt->emptyParent = true;

                    // find if a parent has any child
                if (! in_array($opt->id, $parent_categories2))
                    unset($opt->isparent);
            }
        }
        unset($opt);
        return $options;
    }

    /**
     * Remove any inactive option from the selected options
     * This array is used later by the getURI func which should not use the inactive to generate the option's URI
     *
     * @param
     *            boolean - indicates if the var that will be used is the per filter or not
     * @author Sakis Terz
     * @since 1.0
     */
    public function removeInactiveOpt()
    {
        if (empty($this->selected_flt))
            return $this->selected_flt;
        $myselection = $this->selected_flt;
        foreach ($myselection as $key => &$array) {
            if (! is_array($array))
                continue;
            foreach ($array as $key2 => $sel) {
                if (isset($this->filters[$key]['inactive_select_opt'])) {
                    if (in_array($sel, $this->filters[$key]['inactive_select_opt']))
                        unset($array[$key2]);
                }
            }
        }
        return $myselection;
    }

    /**
     * Get an array with the filter headers
     * used in the module's template
     *
     * @author Sakis Terz
     * @return array header strings
     * @since 1.0
     */
    public function getFltHeaders()
    {
        return $this->filters_headers_array;
    }   
}