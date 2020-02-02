<?php
 /**
 * The helper class which contains the functionality for fetching and creating the filter's options
 * @package	customfilters
 * @author 	Sakis Terz
 * @copyright	Copyright (C) 2012-2018 breakdesigns.net . All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die();
jimport('joomla.filter.filteroutput');

use Joomla\Utilities\ArrayHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Factory as JFactory;

/**
 * Class responsible for generating the options of each filter
 *
 * @author sakis
 */
class OptionsHelper
{

	/**
	 * 
	 * @var \CustomfiltersConfig
	 */
	protected $componentparams;
	
	/**
	 * The module's params
	 *
	 * @var \Joomla\Registry\Registry
	 */
	protected $moduleparams;
	
	/**
	 * 
	 * @var \Joomla\Registry\Registry
	 */
	protected $menuparams;
	
    /**
     *
     * @var String
     */
    private $defaultShopLang;

    /**
     *
     * @var array
     */
    private $product_ids;

    /**
     *
     * @var array
     */
    public $selected_flt;

    /**
     *
     * @var array
     */
    private $shopperGroups = [];

    /**
     *
     * @var array
     */
    public $input;
    
    /**
     * 
     * @var string
     */
    protected $vmVersion;

    /**
     * 
     * @var array
     */
    public $fltSuffix = [
        'q' => 'keyword_flt',
        'virtuemart_category_id' => 'category_flt',
        'virtuemart_manufacturer_id' => 'manuf_flt',
        'price' => 'price_flt',
        'custom_f' => 'custom_flt'
    ];    
    
    // the table names which are retreived by the $field var which is used as key
    protected $table_db_flds= [        
        'virtuemart_category_id' => '#__virtuemart_product_categories',
        'virtuemart_manufacturer_id' => '#__virtuemart_product_manufacturers',
        'price' => '#__virtuemart_product_prices',
        'virtuemart_custom_id' => '#__virtuemart_product_customfields'        
    ];

    /**
     *
     * @var array
     */
    public static $parent_categories = [];
    
    /**
     * 
     * @var array
     */
    protected $customFltActive = [];

    /**
     * Constructor
     *
     * @param object $params
     *            - StdClass the module params
     * @param object $module
     *            - StdClass the module object
     */
    function __construct($params, $module)
    {
        $app = JFactory::getApplication();
        $this->moduleparams = $params;
        $this->componentparams = CustomfiltersConfig::getInstance();
        $this->menuparams = cftools::getMenuparams();
        $this->customFltActive = cftools::getCustomFilters($this->moduleparams);
        $jinput = $app->input;
        $this->input = $jinput;
        $this->selected_flt = CfInput::getInputs();
        $this->shopperGroups = cftools::getUserShopperGroups();
        $this->vmVersion = VmConfig::getInstalledVersion();
        $option = $jinput->get('option', '', 'cmd');

        // in cf pages get the returned product ids
        if ($option == 'com_customfilters') {
            $this->product_ids = $app->getUserState("com_customfilters.product_ids");
        } else {
            $this->product_ids = array();
        }

        $dependency_dir = $params->get('dependency_direction', 'all');
        if (count($this->selected_flt) > 0 && $dependency_dir == 't-b') {
            $this->selected_fl_per_flt = CfInput::getInputsPerFilter($module);
        }
    }

    /**
     * Checks if the site's language is the same as the one set as default
     * If not return the default
     *
     * @return false|string
     */
    protected function getDefaultLang()
    {
        if ($this->defaultShopLang == null) {
            if (JLANGPRFX != VM_SHOP_LANG_PRFX && VmConfig::$langCount>1) {
                $this->defaultShopLang = VM_SHOP_LANG_PRFX;
            } else {
                $this->defaultShopLang = false;
            }
        }
        return $this->defaultShopLang;
    }

    /**
     * Proxy function to get the options of specific filters
     *
     * @param string $var_name
     * @param string $custom_field_type
     *            - used only for custom fields
     *
     * @return array - the options
     */
    public function getOptions($var_name, $custom_filter = null)
    {
        $options = array();
        if (strpos($var_name, 'custom_f_') !== false)
            $var_type = 'custom_f';
        else
            $var_type = $var_name;

        switch ($var_type) {
            case 'virtuemart_category_id':
                $options = $this->getCategories();
                break;
            case 'virtuemart_manufacturer_id':
                $options = $this->getManufacturers();
                break;
            case 'price':
                $options = $this->getPriceRanges();
                break;
            case 'custom_f':
                $options = $this->getCustomOptions($custom_filter);
                break;
        }
        return $options;
    }

    /**
     *
     * Proxy function to build the queries of the various filters
     *
     * @param JDatabaseQuery
     * @param string $var_name
     * @param string $custom_field_type
     *
     * @return JDatabaseQuery
     * @since 1.5.0
     */
    public function buildQuery(JDatabaseQuery $query, $var_name, $customFilter, $part = false)
    {
        $options = array();
        if (! empty($customFilter))
            $var_type = 'custom_f';
        else
            $var_type = $var_name;

        switch ($var_type) {
            case 'virtuemart_category_id':
                $query = $this->buildCategoriesQuery($query, $part);
                break;
            case 'virtuemart_manufacturer_id':
                $query = $this->buildManufQuery($query, $part);
                break;
            case 'price':
                $query = $this->buildPriceRangeQuery($query, $part);
                break;
            case 'custom_f':
                $query = $this->buildCustomFltQuery($query, $customFilter, $part);
                break;
            default:
                $query = $query;
                break;
        }
        return $query;
    }

    /**
     * Get the active options of a current filter using dependencies from the selections in other filters
     *
     * @param string $field
     * @param stdClass $customfilter
     * @param boolean $joinFieldData
     *            if there will be join with other queries, built by the buildQuery functions.
     *            Join is not necessary when the display type is "disabled" as only the active options are used
     *
     * @return mixed when there are results - true if there are no other filters selected. So all are active
     * @author Sakis Terz
     * @since 1.0
     */
    public function getActiveOptions($field, $customfilter = null, $joinFieldData = false)
    {
        $selected_flt = [];

        /*
         * ids generated in the component and should be included in the query
         * Storing them to the component we avoid duplicate work and the sql query becomes lighter
         */
        $where_productIds = $this->input->get('where_productIds', array(), 'array');

        // each range filter and search stores the found product ids in this assoc array
        $found_product_ids_per_filter = $this->input->get('found_product_ids', array(), 'array');

        // all the product ids found from searches and range filters
        $returned_products = $this->componentparams->getReturnedProductsType();
        $filtered_products = $this->componentparams->getFilteredProductsType();

        // if the dependency works from top to bottom, get the selected filters as stored in the "selected_fl_per_flt"
        if (isset($this->selected_fl_per_flt)) {
            if (isset($this->selected_fl_per_flt[$field])) {
                $selected_flt = $this->selected_fl_per_flt[$field];
            } else {
                $selected_flt = [];
            }
        }

        // if the category is not reseting the keywords, then it should get keywords in account
        elseif ($field == 'virtuemart_category_id' && $this->moduleparams->get('category_flt_onchange_reset', 'filters') == 'filters') {
            // reset that for the categories we do not want them to take other filters like price range into account
            $where_productIds = [];
            ! empty($this->selected_flt['q']) ? $selected_flt['q'] = $this->selected_flt['q'] : '';
        } else {
            $selected_flt = $this->selected_flt;
        }
            
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query = $this->buildQuery($query, $field, $customfilter, true);

        $is_customfield = strpos($field, 'custom_f_');
        $activeOptions = [];

        $innerJoins = [];
        $leftJoins = [];
        $where = [];
        $where_str = '';
        $i = 0; 

        // if the field is a cucstomfield use that as the table name
        if ($is_customfield !== false) {
            $this->table_db_flds[$field] = 'cfp';
        }
            
            // iterate through the selected variables and join the relevant tables
        foreach ($selected_flt as $key => $ar_value) {
            $wherecf = array();
            
            /*
             * the query should run only if there are options selected in the filters
             * other than the one we get as field param in that function
             */
            if (count($ar_value) == 0 || $key == $field) {
                continue;
            }          
            
            /*
             * if the key of the selected filters array is a customfield there are other rules
             * This because the custom filers are stored in various arrays. Use a generated by the module name
             * and also because they are stored as varchars in the db and we cannot use where in
             */
            if (strpos($key, 'custom_f_') !== false) {
                
                // get the filter id
                preg_match('/[0-9]+/', $key, $mathces);
                if(!isset($this->customFltActive[(int) $mathces[0]])) {
                    continue;
                }
                $custFltObj = $this->customFltActive[(int) $mathces[0]];
                
                // check if its range
                if ($custFltObj->disp_type != 5 && $custFltObj->disp_type != 6 && $custFltObj->disp_type != 8) {
                    
                    // not plugin
                    if ($custFltObj->field_type != 'E') {
                        $this->table_db_flds[$key] = '#__virtuemart_product_customfields';
                        $sel_field = 'customfield_value';
                        
                        foreach ($ar_value as $av) {
                            $wherecf[] = "{$key}.{$sel_field} =" . $db->quote($av);
                        }
                        
                        if (count($wherecf) > 0) {
                            $where[] = "((" . implode(' OR ', $wherecf) . ") AND {$key}.virtuemart_custom_id=" . (int) $mathces[0] . ")";
                        }
                        $innerJoins[] = "{$this->table_db_flds[$key]} AS $key ON {$key}.virtuemart_product_id=p.virtuemart_product_id";
                    } else {
                        
                        // if the plugin has not declared the necessary params go to the next selected var
                        if (empty($custFltObj->pluginparams)) {
                            continue;
                        }
                            // get vars from plugins
                        $curSelectionTable = $custFltObj->pluginparams->product_customvalues_table;
                        $sel_field = $custFltObj->pluginparams->filter_by_field;
                        $filter_data_type = $custFltObj->pluginparams->filter_data_type;
                        $wherecf = [];
                        
                        // if its string we need to escape and quote each value
                        if ($filter_data_type == 'string') {
                            foreach ($ar_value as $av) {
                                $wherecf[] = "{$key}.{$sel_field} =" . $db->quote($av);
                            }
                            
                            if (count($wherecf) > 0) {
                                if ($custFltObj->pluginparams->product_customvalues_table == $custFltObj->pluginparams->customvalues_table)
                                    $where[] = '((' . implode(' OR ', $wherecf) . ") AND {$key}.virtuemart_custom_id=" . (int) $mathces[0] . ")";
                                else
                                    $where[] = '(' . implode(' OR ', $wherecf) . ")";
                            }
                        } else {
                            
                            // if they are in different tables we can use where in which is faster also we should sanitize the vars
                            if ($filter_data_type == 'int' || $filter_data_type == 'boolean' || $filter_data_type == 'bool') {
                                $ar_value = ArrayHelper::toInteger($ar_value);
                            } elseif ($filter_data_type == 'float') { // sanitize the float numbers
                                foreach ($ar_value as &$av) {
                                    $av = (float) $av;
                                }
                            }  // if none of the above continue
                             else {
                                continue;
                            }
                            if (! empty($ar_value)) {
                                $where[] = "{$key}.{$sel_field} IN(" . implode(',', $ar_value) . ")";
                            }
                        }
                        
                        $innerJoins[] = "$curSelectionTable AS $key ON {$key}.virtuemart_product_id=p.virtuemart_product_id";
                    }
                }
            }            

            // keyword
            elseif ($key == 'q') {
                
                // if the $where_productIds is not empty, then this var contains also the products from searches and will be added later
                if (! empty($where_productIds))
                    continue;
                $product_ids_from_search = $found_product_ids_per_filter['search'];
                if (is_array($product_ids_from_search) && ! empty($product_ids_from_search)) {
                    $where[] = "p.virtuemart_product_id IN(" . implode(',', $product_ids_from_search) . ")";
                }                 // empty set of products
                else
                    return;
            }            

            // other filters than customfilters but not product_price or keyword (i.e. categories, manufacturers)
            elseif ($key != 'price') {
                $sel_field = $key;
                $where[] = "{$this->table_db_flds[$key]}.{$sel_field} IN (" . implode(' ,', $ar_value) . ")";
                
                /*
                 * lookup for filters into the parent or the child products
                 * This is designated by the use of the $returned_products component setting
                 * Or by the custom_flt_lookup_in module setting for the custom filters
                 */
                if ($returned_products == 'child') {
                    $innerJoins[] = "{$this->table_db_flds[$key]} ON p.product_parent_id = {$this->table_db_flds[$key]}.virtuemart_product_id";
                } elseif ($returned_products == 'parent' &&  $filtered_products == 'all') {
                    $innerJoins[] = "{$this->table_db_flds[$key]} ON {$this->table_db_flds[$key]}.virtuemart_product_id = 
                    (CASE WHEN p.product_parent_id>0 THEN p.product_parent_id ELSE p.virtuemart_product_id END)";
                }
                elseif ($returned_products == 'parent' &&  $filtered_products == 'child') {
                    $innerJoins[] = "{$this->table_db_flds[$key]} ON p.product_parent_id = {$this->table_db_flds[$key]}.virtuemart_product_id";
                }                
                else {
                    $innerJoins[] = "{$this->table_db_flds[$key]} ON p.virtuemart_product_id = {$this->table_db_flds[$key]}.virtuemart_product_id";
                }
            }
        }

        // product ids that come from the component (range filters and seaeches)
        if (! empty($where_productIds)) {

            /* If we are searching for price ranges, we should not take into account the products returned by the current price search, only by the other searches */
            if ($field == 'price' && ! empty($found_product_ids_per_filter['price'])) {
                if (! isset($found_product_ids_per_filter['search']))
                    $found_product_ids_per_filter['search'] = array();
                if (! isset($found_product_ids_per_filter['ranges']))
                    $found_product_ids_per_filter['ranges'] = array();
                $where_productIds = array_merge($found_product_ids_per_filter['search'], $found_product_ids_per_filter['ranges']);
            }
            if (! empty($where_productIds)) {
                $where[] = "p.virtuemart_product_id IN(" . implode(',', $where_productIds) . ")";
            }
        }

        if ($where) {

            // generate some db vars
            if ($is_customfield !== false) {
                preg_match('/[0-9]+/', $field, $mathcess);
                if ($customfilter->field_type != 'E') { // not plugin
                    $from_alias = 'cfp';
                    $from = '#__virtuemart_product_customfields AS ' . $from_alias;
                    $sqlfield = 'custom_value';
                    $where[] = 'cfp.virtuemart_custom_id=' . (int) $mathcess[0];
                }

                // is plugin and has params
                elseif (isset($customfilter->pluginparams)) {
                    $from_alias = 'cfp';
                    $from = $customfilter->pluginparams->customvalues_table . ' AS ' . $from_alias = 'cf';
                    $sqlfield = $customfilter->pluginparams->filter_by_field;
                    $where[] = 'cf.virtuemart_custom_id=' . (int) $mathcess[0];
                }
            } else {
                $sqlfield = $field;
                $from = $this->table_db_flds[$field];
                $from_alias = $from;
            }

            $query->where(implode(' AND ', $where));
            if (count($innerJoins) > 0) {
                $query->innerJoin(implode(' INNER JOIN ', $innerJoins));
            }
            if (count($leftJoins) > 0) {
                $query->leftJoin(implode(' LEFT JOIN ', $leftJoins));
            }
        }
        $q= (string)$query;
        $db->setQuery($query);
        $activeOpt = $db->loadObjectList();

        /*
         * If $joinFieldData is true all the data are included in the $activeOpt
         * so we have to handle them accordingly e.g. Create category levels or encode cf values
         */
        if (! empty($activeOpt)) {
            if ($joinFieldData) {
                if ($is_customfield !== false && ! empty($activeOpt)) {
                    $sort_by = 'name';
                    if (($customfilter->is_list && ! empty($customfilter->custom_value)) || $customfilter->field_type == 'E') {
                        $sort_by = 'default_values';
                    }
                    $activeOpt = $this->encodeOptions($activeOpt);
                    if ($sort_by == 'name') {
                        $this->sort_by($sort_by, $activeOpt);
                    }
                }
            } else {

                // convert to hexademical if custom filters
                if ($is_customfield !== false) {
                    $activeOptions = array();
                    if (is_array($activeOpt)) {
                        $activeOpt = $this->encodeOptions($activeOpt);
                    }
                }
            }
            if (! empty($activeOpt)) {
                $activeOptions = cftools::arrayFromValList($activeOpt);
            }
        }
        return $activeOptions;
    }

    /**
     * Get the categories
     *
     * @param
     *            Array Contains the ids of the categories we want to display
     * @param
     *            String
     *
     * @author Sakis Terz
     * @since 1.0
     */
    public function getCategories()
    {
        $caching = false;
        $cahche_id = '';
        $results = array();
        $subtree_parent_category_id = false;
        $displayCounterSetting = $this->moduleparams->get('category_flt_display_counter_results', 1);
        $on_category_reset_others = $this->moduleparams->get('category_flt_onchange_reset', 'filters');
        $selected_categories = ! empty($this->selected_flt['virtuemart_category_id']) ? $this->selected_flt['virtuemart_category_id'] : array();
        $virtuemart_shoppergroup_ids = cftools::getUserShopperGroups();
        /*
         * if we should return only the subcats
         * this should happen only when imposed by the corresponding setting in the module
         * and the categories list is integral, unaffected by other selections
         */
        if ($this->moduleparams->get('category_flt_only_subcats', false) && (($this->moduleparams->get('category_flt_onchange_reset', 'filters') == 'filters' && empty($this->selected_flt['q'])) || $this->moduleparams->get('category_flt_onchange_reset', 'filters') == 'filters_keywords')) {
            $subtree_parent_category_id = $this->getParentCategoryId($selected_categories);
        }

        // the display order of the categories. If we display only the subcategories we do not need tree ordering.
        $cat_disp_order = ! empty($subtree_parent_category_id) && $this->moduleparams->get('categories_disp_order') == 'tree' ? 'ordering' : $this->moduleparams->get('categories_disp_order');

        // if the category tree is always the same and has no countering then get a cached version
        if (($on_category_reset_others == 'filters_keywords' || ($on_category_reset_others == 'filters')) && empty($subtree_parent_category_id)) {
            $caching = true;
            $disp_vm_cat = $this->moduleparams->get('category_flt_disp_vm_cat', '');
            $excluded_vm_cat = $this->moduleparams->get('category_flt_exclude_vm_cat', '');
            $display_empty_opt = $this->moduleparams->get('category_flt_disable_empty_filters', '1');
            $q = ! empty($this->selected_flt['q']) ? $this->selected_flt['q'] : '';
            $cahche_id = serialize('dbObj' . implode(',', $virtuemart_shoppergroup_ids) . $cat_disp_order . $disp_vm_cat . $excluded_vm_cat . $display_empty_opt . $displayCounterSetting . $q);

            // 6 minutes if we have counter - 60 if we do not have
            $cacheTime = $displayCounterSetting ? 6 : 60;

            $cache = JFactory::getCache('mod_cf_filtering.categories', '');
            $cache->setCaching(true);
            $cache->setLifeTime($cacheTime);
            $results = $cache->get($cahche_id);
        }

        // runs when the cache is inactive or empty
        if (empty($results)) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query = $this->buildCategoriesQuery($query, $part = false, $subtree_parent_category_id);
            $db->setQuery($query);
            $dbresults = $db->loadObjectList();

            // we need the creation of levels only in trees
            if ($cat_disp_order == 'tree') {
                $elaborated_list = $this->createCatLevels($dbresults);
                $results = $elaborated_list;
            } else {
                // in case of subtree, set the parent at the top as a clear option, but not when checkboxes
                if (! empty($subtree_parent_category_id)) {
                    $handle = 'ontop';

                    // remove the parent category from top when checkboxes. Add the clear option
                    if ($this->moduleparams->get('category_flt_disp_type', '1') == '3')
                        $handle = 'remove';
                    $dbresults = $this->_handleParentCategory($dbresults, $subtree_parent_category_id, $handle);
                } else
                    $dbresults = cftools::arrayFromValList($dbresults);

                $results['options'] = $dbresults;
                $results['maxLevel'] = 0;
            }
            if ($caching) {
                $cache->store($results, $cahche_id);
            }
        }
        return $results;
    }

    /**
     * Build the query for the Categories
     *
     * @since 1.5.0
     * @param JDatabaseQuery $query
     * @param boolean $part
     * @param boolean $subtree if we should return only the subcats
     *
     * @return JDatabaseQuery The db query
     * @author Sakis Terz
     */
    public function buildCategoriesQuery(JDatabaseQuery $query, $part = false, $subtree = false)
    {
        $where = array();
        $where_str = '';
        $innerJoin = array();
        $innerJoin_str = '';
        $join_products = false;
        $subtree_parent_category_id = false;
        $selected_categories = ! empty($this->selected_flt['virtuemart_category_id']) ? $this->selected_flt['virtuemart_category_id'] : array();
        $returned_products=$this->componentparams->getReturnedProductsType();
        $filtered_products=$this->componentparams->getFilteredProductsType();

        /*
         * if we should return only the subcats
         */
        if ($subtree) {
            $subtree_parent_category_id = $this->getParentCategoryId($selected_categories);
        }

        // the display order of the categories. If we display only the subcategories we do not need tree ordering.
        $cat_disp_order = ! empty($subtree_parent_category_id) && $this->moduleparams->get('categories_disp_order') == 'tree' ? 'ordering' : $this->moduleparams->get('categories_disp_order');

        // included categories
        $disp_vm_cat = $this->moduleparams->get('category_flt_disp_vm_cat', '');

        // excluded categories
        $excluded_vm_cat = $this->moduleparams->get('category_flt_exclude_vm_cat', '');

        $display_empty_opt = $this->moduleparams->get('category_flt_disable_empty_filters', '1');

        // convert to array to sanitize data
        if (! empty($disp_vm_cat)) {
            $cat_ids_ar = explode(',', $disp_vm_cat);
            $cat_ids_ar = ArrayHelper::toInteger($cat_ids_ar);
        } else {
            $cat_ids_ar = array();
        }

        // convert to array to sanitize data
        if (! empty($excluded_vm_cat)) {
            $excluded_ids_ar = explode(',', $excluded_vm_cat);
            if (is_array($excluded_ids_ar)) {
                $excluded_ids_ar = ArrayHelper::toInteger($excluded_ids_ar);
            }
        } else {
            $excluded_ids_ar = array();
        }

         // counter
        $suffix = $this->fltSuffix['virtuemart_category_id'];
        $displayCounterSetting = $this->moduleparams->get($suffix . '_display_counter_results', 1);

        //language table
        $query->leftJoin("#__virtuemart_categories_" . JLANGPRFX . " AS langt ON vc.virtuemart_category_id=langt.virtuemart_category_id");

        //category->category table (parent child products)
        $innerJoin[] = "#__virtuemart_category_categories AS cx ON cx.category_child_id=vc.virtuemart_category_id ";

        /*
         * count results only when
         * the $displayCounterSetting is active and it is a part query (getActiveOptions)
         * or when all options are active (display type setting)
         * or the $displayCounterSetting is active and there is no selection
         * or when the $displayCounterSetting is active and the only selection is a category
         *
         * //we don't want in any case to run the count both in the getActiveOptions and here
         */
        if ($displayCounterSetting) {
            $table = '#__virtuemart_product_categories';

            // if return child products
            if ($returned_products == 'child') {
                $query->select("SUM(CASE WHEN p.product_parent_id>0 THEN 1 ELSE 0 END) AS counter");
            }
            // if return parent products
            else if ($returned_products == 'parent') {
                
                // return parents and generate filters from parents
                if ($filtered_products == 'all' || $filtered_products == 'parent') {
                    $query->select("SUM(CASE WHEN p.product_parent_id=0 THEN 1 ELSE 0 END) AS counter");
                }
                // return parents and generate filters from all
                else if ($filtered_products == 'child'){
                    $query->select("COUNT(DISTINCT p.product_parent_id) AS counter");
                }
            }
            // if return all products
            else {
                $query->select("COUNT(p.virtuemart_product_id) AS counter");
            }
        }

        // if not part join the category products and the products_lang in case of multi-language site
        if ($displayCounterSetting || $part) {
            $parents_sql = '';

            // get the parents if exist.Parents should be always displayed, otherwise the tree will be incomprehensive
            if ($cat_disp_order == 'tree') {
                if (! isset($db))
                    $db = JFactory::getDbo();
                $myQuery = $db->getQuery(true);
                $myQuery->select('DISTINCT cx.category_parent_id');
                $myQuery->from('#__virtuemart_category_categories AS cx');
                $myQuery->innerJoin('#__virtuemart_categories AS c ON cx.category_parent_id=c.virtuemart_category_id');
                $myQuery->where('cx.category_parent_id>0 AND c.published=1');
                $db->setQuery($myQuery);
                $parents = $db->loadColumn();
                if (! empty($parents)) {
                    $parents = implode(',', $parents);
                    $parents_sql = " OR vc.virtuemart_category_id IN($parents)";
                }
            }

            //join with the category products tables
            $query->leftJoin("#__virtuemart_product_categories ON vc.virtuemart_category_id=#__virtuemart_product_categories.virtuemart_category_id");


            // join the products table to check for unpublished
            if ($returned_products == 'child' || $filtered_products == 'child') {
                $query->leftJoin("`#__virtuemart_products` AS p ON #__virtuemart_product_categories.virtuemart_product_id = p.`product_parent_id`");
            }
            else {
                $query->leftJoin("`#__virtuemart_products` AS p ON #__virtuemart_product_categories.virtuemart_product_id = p.`virtuemart_product_id`");
            }

             // stock control
            if (! VmConfig::get('use_as_catalog', 0)) {
                if (VmConfig::get('stockhandle', 'none') == 'disableit_children') {
                    $where[] = '((p.published=1 AND (p.`product_in_stock` - p.`product_ordered` >0 OR children.`product_in_stock` - children.`product_ordered` >0))' . $parents_sql . ')';
                    $query->leftJoin('`#__virtuemart_products` AS children ON p.`virtuemart_product_id` = children.`product_parent_id`');
                } elseif (VmConfig::get('stockhandle', 'none') == 'disableit') {
                    $where[] = '((p.published=1 AND(p.`product_in_stock` - p.`product_ordered` >0))' . $parents_sql . ')';
                } else
                    $where[] = "(p.published=1 $parents_sql)";
            } else
                $where[] = "(p.published=1 $parents_sql)";

                // use of shopper groups
            if (count($this->shopperGroups) > 0 && $this->componentparams->get('products_multiple_shoppers', 0)) {

                $query->leftJoin("
                    (SELECT #__virtuemart_product_categories.virtuemart_product_id,
					CASE WHEN (s.`virtuemart_shoppergroup_id` IN(" . implode(',', $this->shopperGroups) . ") OR  (s.`virtuemart_shoppergroup_id`) IS NULL) THEN 1 ELSE 0 END AS `virtuemart_shoppergroup_id`
					FROM `#__virtuemart_product_shoppergroups` AS s
					RIGHT JOIN #__virtuemart_product_categories ON #__virtuemart_product_categories.virtuemart_product_id =s.virtuemart_product_id
					WHERE
					(s.`virtuemart_shoppergroup_id` IN(" . implode(',', $this->shopperGroups) . ") OR  (s.`virtuemart_shoppergroup_id`) IS NULL)
					GROUP BY #__virtuemart_product_categories.virtuemart_product_id
					) AS sp
					ON  #__virtuemart_product_categories.virtuemart_product_id=sp.virtuemart_product_id");

                $where[] = "(sp.virtuemart_shoppergroup_id=1 $parents_sql)";
            }
            $query->group('cx.category_child_id');
        }

        // where
        $cat_ids = implode(',', $cat_ids_ar);
        if (! empty($cat_ids)) {
            $where[] = "vc.virtuemart_category_id IN(" . $cat_ids . ")";
        }

        $excluded_cat_ids = implode(',', $excluded_ids_ar);
        if (! empty($excluded_cat_ids)) {
            $where[] = "vc.virtuemart_category_id NOT IN(" . $excluded_cat_ids . ")";
        }

        if (! empty($subtree_parent_category_id)) {
            $where[] = "(cx.category_child_id=" . (int) $subtree_parent_category_id . " OR cx.category_parent_id=" . (int) $subtree_parent_category_id . ')';
        }

        // define the categories order by and some other vars
        switch ($cat_disp_order) {
            case 'ordering':
                $orderBy = 'vc.ordering, name';
                $fields = '';
                break;
            case 'names':
                $orderBy = 'name';
                $fields = '';
                break;
            case 'tree':
                $orderBy = 'cx.category_parent_id,vc.ordering';
                $fields = ',cx.category_parent_id ,cx.category_child_id';
                break;
        }

        // published only
        $where[] = 'vc.published=1';

        if (count($innerJoin) > 0)
            $query->innerJoin(implode(" INNER JOIN ", $innerJoin));
        if (count($where) > 0)
            $query->where(implode(" AND ", $where));

         // format the final query
        if($this->getDefaultLang()){
            $query->leftJoin("#__virtuemart_categories_" . VM_SHOP_LANG_PRFX . " AS langt_def ON vc.virtuemart_category_id=langt_def.virtuemart_category_id");
            $query->select("IFNULL(langt.category_name, langt_def.category_name) AS name");
        }
        else {
            $query->select("langt.category_name AS name");
        }
        $query->select("vc.virtuemart_category_id AS id $fields");
        $query->from("#__virtuemart_categories AS vc");
        $query->order($orderBy);
        $q= (string)$query;
        return $query;
    }

    /**
     * Detects and returns the parent category of the current subtree based on the selections
     * The parent category is not necessarily the actual parent of the cuurent category but the parent of the categories that should be displayed (subtree)
     *
     * @param array $categories
     * @return boolean|mixed
     */
    public function getParentCategoryId(array $categories = [])
    {
        if (empty($categories) || ! is_array($categories))
            return false;
        $category_id = reset($categories);
        $key = md5($category_id);

        if (empty(self::$parent_categories[$key])) {
            $category = $this->_getCategory($category_id);

            $hasSubCategories = $this->_hasSubCategories($category_id);
            if (! empty($hasSubCategories))
                $parent_category_id = $category_id;
            else
                $parent_category_id = $category->category_parent_id;
            self::$parent_categories[$key] = $parent_category_id;
        }
        return self::$parent_categories[$key];
    }

    /**
     * Get a category by it's id
     *
     * @param int $id
     * @return mixed
     * @since 2.3.1
     */
    private function _getCategory($id)
    {
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('*')
            ->from('#__virtuemart_category_categories')
            ->where('category_child_id=' . (int) $id);
        $db->setQuery($q);
        $result = $db->loadObject();
        return $result;
    }

    /**
     * Detect if a category is parent / has sub categories
     *
     * @param int $id
     *
     * @return mixed
     * @since 2.3.1
     */
    private function _hasSubCategories($id)
    {
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('category_child_id')
            ->from('#__virtuemart_category_categories')
            ->where('category_parent_id=' . (int) $id)
            ->setLimit(1);
        $db->setQuery($q);
        $result = $db->loadResult();
        return $result;
    }

    /**
     * Gets an array and puts an element at the start of it
     *
     * @param array $categories
     * @param int $parent_id
     *            - the id of the parent category
     *
     * @return array
     * @since 2.3.1
     */
    private function _handleParentCategory(array $categories = [], $parent_id, $handle = 'ontop')
    {
        $newArray = array();
        foreach ($categories as $category) {
            $category->name = htmlspecialchars($category->name, ENT_COMPAT, 'UTF-8');

            if ($category->id == $parent_id) {

                // put parent it on top
                if ($handle == 'ontop') {
                    $category->name = JText::sprintf('MOD_CF_ANY_HEADER', $category->name);
                    $category->isparent = true;
                    $category->counter = 0;
                    $newArray[0] = $category; // set this at top
                }
                // ignore parent
            }

            else
                $newArray[$category->id] = $category;
        }
        return $newArray;
    }

    /**
     * Set categories to levels and order them appropriately
     *
     * @param array $categArray
     *
     * @return array categories
     * @since 1.0
     */
    public function createCatLevels($categArray)
    {
        if (empty($categArray))
            return;
        $maxLevel = 0;
        $disp_vm_cat = $this->moduleparams->get('category_flt_disp_vm_cat', '');
        $category_flt_disp_type = $this->moduleparams->get('category_flt_disp_type', '1');

        // convert to array to sanitize data
        if (! empty($disp_vm_cat)) {
            $cat_ids_ar = explode(',', trim($disp_vm_cat));
            $cat_ids_ar = ArrayHelper::toInteger($cat_ids_ar);
        }

        // excluded categories
        $excluded_vm_cat = $this->moduleparams->get('category_flt_exclude_vm_cat', '');

        // convert to array to sanitize data
        if (! empty($excluded_vm_cat)) {
            $excluded_ids_ar = explode(',', $excluded_vm_cat);
            if (is_array($excluded_ids_ar))
                $excluded_ids_ar = ArrayHelper::toInteger($excluded_ids_ar);
        } else
            $excluded_ids_ar = array();

        $cat_disp_order = $this->moduleparams->get('categories_disp_order');

        // create the tree
        if (empty($cat_ids_ar) && $cat_disp_order == 'tree') {

            $results = $this->orderVMcats($categArray, $excluded_ids_ar);
            if (empty($results))
                return;
            $levels = $this->getVmCatLevels($results);

            // add the spaces
            foreach ($results as $key => &$cat) {
                $cat->level = $levels[$key];
                $cat->name = $cat->name;
                if ($levels[$key] > $maxLevel)
                    $maxLevel = $levels[$key];
                    // add the blanks only when drop-down lists
                if ($category_flt_disp_type == 1) {
                    for ($i = 0; $i < $levels[$key]; $i ++) {
                        $cat->name = '&nbsp;&nbsp;' . $cat->name; // add the blanks
                    }
                }
            }
        }

        // when no tree
        else {
            // the returned array should be assoc with key the cat id
            foreach ($categArray as $cat) {
                $cat->name = $cat->name;
                $results[$cat->id] = $cat;
            }
        }
        $finalArray['options'] = $results;
        $finalArray['maxLevel'] = $maxLevel;
        return $finalArray;
    }

    /**
     * creates indentation according to the categories hierarhy
     *
     * @param array $categoryArr
     * @return array
     */
    public function getVmCatLevels($results)
    {
        if (! $results)
            return;
        $blank = 0;
        $blanks = array();
        $blanks[0] = 0;

        foreach ($results as $res) {
            if (! empty($blanks[$res->category_parent_id]))
                $blanks[$res->category_child_id] = $blanks[$res->category_parent_id];
            else
                $blanks[$res->category_child_id] = 0;
            $blanks[$res->category_child_id] += 1;
        }

        // set the levels - removing them by 1 (1st should be zero)
        foreach ($blanks as &$bl) {
            $bl -= 1;
        }
        return $blanks;
    }

    /**
     * Order the categories to create the tree
     *
     * @param array $categoryArr
     * @param array $excluded_categ
     *
     * @return mixed on success, boolean on failure
     */
    public function orderVMcats(&$categoryArr, $excluded_categ)
    {
        // Copy the Array into an Array with auto_incrementing Indexes
        $categCount = count($categoryArr);
        if ($categCount > 0) {
            for ($i = 0; $i < $categCount; $i ++) {
                $resultsKey[$categoryArr[$i]->category_child_id] = $categoryArr[$i];
            }
            $key = array_keys($resultsKey); // Array of category table primary keys
            $nrows = $size = sizeOf($key); // Category count

            // Order the Category Array and build a Tree of it
            $id_list = array();
            $row_list = array();
            $depth_list = array();

            $children = array();
            $parent_ids = array();
            $parent_ids_hash = array();

            // Build an array of category references
            $category_temp = array();
            for ($i = 0; $i < $size; $i ++) {
                $category_tmp[$i] = &$resultsKey[$key[$i]];
                $parent_ids[$i] = $category_tmp[$i]->category_parent_id;

                if ($category_tmp[$i]->category_parent_id == 0 || in_array($category_tmp[$i]->category_parent_id, $excluded_categ)) {
                    array_push($id_list, $category_tmp[$i]->category_child_id);
                    array_push($row_list, $i);
                    array_push($depth_list, 0);
                }

                $parent_id = $parent_ids[$i];

                if (isset($parent_ids_hash[$parent_id])) {
                    $parent_ids_hash[$parent_id][$i] = $parent_id;
                } else {
                    $parent_ids_hash[$parent_id] = array(
                        $i => $parent_id
                    );
                }
            }
            $loop_count = 0;
            $watch = array(); // Hash to store children
            while (count($id_list) < $nrows) {
                if ($loop_count > $nrows)
                    break;
                $id_temp = array();
                $row_temp = array();

                for ($i = 0; $i < count($id_list); $i ++) {
                    $id = $id_list[$i];
                    $row = $row_list[$i];
                    if (isset($parent_ids_hash[$id]) && $id > 0) {
                        $resultsKey[$id]->isparent = true;
                    }
                    array_push($id_temp, $id);
                    array_push($row_temp, $row);

                    if (isset($parent_ids_hash[$id])) {
                        $children = $parent_ids_hash[$id];
                        foreach ($children as $key => $value) {
                            if (! isset($watch[$id][$category_tmp[$key]->category_child_id])) {
                                $watch[$id][$category_tmp[$key]->category_child_id] = 1;
                                $category_tmp[$key]->isparent = false;
                                array_push($id_temp, $category_tmp[$key]->category_child_id);
                                array_push($row_temp, $key);
                            }
                        }
                    }
                }
                $id_list = $id_temp;
                $row_list = $row_temp;
                $loop_count ++;
            }
            $orderedArray = array();
            for ($i = 0; $i < count($resultsKey); $i ++) {
                if (isset($id_list[$i]) && isset($resultsKey[$id_list[$i]])) {
                    $parent_id = $resultsKey[$id_list[$i]]->category_parent_id;

                    if ($parent_id == 0) {
                        $resultsKey[$id_list[$i]]->cat_tree = $parent_id;
                    } else {
                        if (isset($resultsKey[$parent_id]->cat_tree))
                            $parent_tree = $resultsKey[$parent_id]->cat_tree;
                        else
                            $parent_tree = '0';

                        $parent_tree .= '-' . $parent_id;
                        $resultsKey[$id_list[$i]]->cat_tree = $parent_tree;
                    }
                    $orderedArray[$id_list[$i]] = $resultsKey[$id_list[$i]];
                }
            }
            return $orderedArray;
        }
        return;
    }

    /**
     * Gets the options for the manufacturers
     *
     * @return array list of objects with the available options
     * @since 1.0
     * @author Sakis Terz
     */
    public function getManufacturers()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query = $this->buildManufQuery($query);
        $db->setQuery($query);
        $manufs = cftools::arrayFromValList($db->loadObjectList());
        return $manufs;
    }

    /**
     * Build the query for the manufacturers
     *
     * @param JDatabaseQuery $query
     * @param boolean $part if this is a query part or the whole query
     *
     * @return JDatabaseQuery The db query
     * @author Sakis Terz
     * @since 1.5.0
     */
    public function buildManufQuery(JDatabaseQuery $query, $part = false)
    {
        $suffix = $this->fltSuffix['virtuemart_manufacturer_id'];
        $displayCounterSetting = $this->moduleparams->get($suffix . '_display_counter_results', 1);
        $display_type = $this->moduleparams->get($suffix . '_disp_type', 1);
        $returned_products = $this->componentparams->getReturnedProductsType();
        $filtered_products = $this->componentparams->getFilteredProductsType();

        // use the media only if image link
        if ($display_type == 7) {
            $query->leftJoin("#__virtuemart_manufacturer_medias AS manuf_med ON vm.virtuemart_manufacturer_id=manuf_med.virtuemart_manufacturer_id");
        }
        $display_empty_opt = $this->moduleparams->get('manuf_flt_disable_empty_filters', '1');

        /*
         * count results only when
         * the $displayCounterSetting is active
         */
        if ($displayCounterSetting) {
            $table = '#__virtuemart_product_manufacturers';

            // if return child products
            if ($returned_products == 'child') {
                $query->select("SUM(CASE WHEN p.product_parent_id>0 THEN 1 ELSE 0 END) AS counter");
            }
            // if return parent products
            else if ($returned_products == 'parent') {

                // return parents and generate filters from parents
                if ($filtered_products == 'all' || $filtered_products == 'parent') {
                    $query->select("SUM(CASE WHEN p.product_parent_id=0 THEN 1 ELSE 0 END) AS counter");
                }
                // return parents and generate filters from child
                else if ($filtered_products == 'child'){
                    $query->select("COUNT(DISTINCT p.product_parent_id) AS counter");
                }
            }
            // if return all products
            else {
                $query->select("COUNT(p.virtuemart_product_id) AS counter");
            }

            $query->group('vm.virtuemart_manufacturer_id');
        }

        if ($displayCounterSetting || $part) {
            $query->leftJoin("#__virtuemart_product_manufacturers ON vm.virtuemart_manufacturer_id=#__virtuemart_product_manufacturers.virtuemart_manufacturer_id");

            // join the products table to check for unpublished
            if ($returned_products == 'child' || $filtered_products == 'child') {
                $query->innerJoin("`#__virtuemart_products` AS p ON #__virtuemart_product_manufacturers.virtuemart_product_id = p.`product_parent_id`");
            }
            else {
                $query->innerJoin("`#__virtuemart_products` AS p ON #__virtuemart_product_manufacturers.virtuemart_product_id = p.`virtuemart_product_id`");
            }

            // stock control
            if (! VmConfig::get('use_as_catalog', 0)) {
                if (VmConfig::get('stockhandle', 'none') == 'disableit_children') {
                    $query->where('(p.`product_in_stock` - p.`product_ordered` >0 OR children.`product_in_stock` - children.`product_ordered` >0)');
                    $query->leftJoin('`#__virtuemart_products` AS children ON p.`virtuemart_product_id` = children.`product_parent_id`');
                } elseif (VmConfig::get('stockhandle', 'none') == 'disableit') {
                    $query->where('(p.`product_in_stock` - p.`product_ordered` >0)');
                }
            }
            // use of shopper groups
            if (count($this->shopperGroups) > 0 && $this->componentparams->get('products_multiple_shoppers', 0)) {
                $query->innerJoin("(SELECT #__virtuemart_product_manufacturers.virtuemart_product_id,s.`virtuemart_shoppergroup_id` FROM `#__virtuemart_product_shoppergroups` AS s
					RIGHT JOIN #__virtuemart_product_manufacturers ON #__virtuemart_product_manufacturers.virtuemart_product_id =s.virtuemart_product_id WHERE
					(s.`virtuemart_shoppergroup_id` IN(" . implode(',', $this->shopperGroups) . ") OR (s.`virtuemart_shoppergroup_id`) IS NULL) GROUP BY #__virtuemart_product_manufacturers.virtuemart_product_id) AS sp
					ON  #__virtuemart_product_manufacturers.virtuemart_product_id=sp.virtuemart_product_id");
            }
            $query->where(" p.published=1");
        }

        $query->leftJoin("#__virtuemart_manufacturers_" . JLANGPRFX . " AS langt ON vm.virtuemart_manufacturer_id=langt.virtuemart_manufacturer_id");

        // format the final query
        if($this->getDefaultLang()){
            $query->leftJoin("#__virtuemart_manufacturers_" . VM_SHOP_LANG_PRFX . " AS langt_def ON vm.virtuemart_manufacturer_id=langt_def.virtuemart_manufacturer_id");
            $query->select("IFNULL(langt.mf_name, langt_def.mf_name) AS name");
        }
        else {
            $query->select("langt.mf_name AS name");
        }

        if ($display_type == 7) {
            $query->select("manuf_med.virtuemart_media_id AS media_id");
        }
        $query->select("vm.virtuemart_manufacturer_id AS id");
        $query->from("#__virtuemart_manufacturers AS vm");
        $query->where("vm.published=1");
        $query->order("name ASC");
        return $query;
    }

    // ___CF___//

    /**
     * Get the price ranges getting into account all the products
     *
     * @return Array ranges
     * @since 2.2.2
     */
    public function getPriceRanges()
    {

        /* Get the vendor's currency and the site's currency */
        $vendor_currency = cftools::getVendorCurrency();
        $virtuemart_currency_id = $this->input->get('virtuemart_currency_id', $vendor_currency['vendor_currency'], 'int');
        $shop_currency_id = JFactory::getApplication()->getUserStateFromRequest("virtuemart_currency_id", 'virtuemart_currency_id', $virtuemart_currency_id);

        // try using external cache - if exists
        $store_id = 'price_range_init::' . $shop_currency_id;
        $cache = JFactory::getCache('mod_cf_filtering.ranges', 'output');
        $lt = (int) $this->componentparams->get('cache_time', 180);
        $cache->setCaching(1);
        $cache->setLifeTime($lt);
        $ranges = $cache->get($store_id);

        if (empty($ranges)) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query = $this->buildPriceRangeQuery($query);
            $db->setQuery($query);
            $ranges = $db->loadObject();
            if (! empty($ranges)) {
                $ranges = $this->caclFinalPrice($ranges);
                if (! empty($ranges->min_value))
                    $ranges->min_value = floor($ranges->min_value);
                if (! empty($ranges->max_value))
                    $ranges->max_value = round($ranges->max_value);
                $cache->store($ranges, $store_id);
            }
        }
        return $ranges;
    }

    /**
     * Get ranges when there are selections/inputs
     *
     * @return Array ranges
     * @since 2.2.0
     */
    public function getRelativePriceRanges()
    {

        /* Get the vendor's currency and the site's currency */
        $vendor_currency = cftools::getVendorCurrency();
        $virtuemart_currency_id = $this->input->get('virtuemart_currency_id', $vendor_currency['vendor_currency'], 'int');
        $shop_currency_id = JFactory::getApplication()->getUserStateFromRequest("virtuemart_currency_id", 'virtuemart_currency_id', $virtuemart_currency_id);

        // if only categories or only manufacturers store the ranges in cache
        $use_external_cache = false;
        if (empty($this->selected_flt) || (! empty($this->selected_flt['virtuemart_category_id']) && count($this->selected_flt) == 1) || (! empty($this->selected_flt['virtuemart_category_id']) && ! empty($this->selected_flt['price']) && count($this->selected_flt) == 1))
            $use_external_cache = true;
        if ((! empty($this->selected_flt['virtuemart_manufacturer_id']) && count($this->selected_flt) == 1) || (! empty($this->selected_flt['virtuemart_manufacturer_id']) && ! empty($this->selected_flt['price']) && count($this->selected_flt) == 1))
            $use_external_cache = true;

            /* Get ranges from externalcache */
        if ($use_external_cache) {
            $store_id = md5('price_range::' . $shop_currency_id . '::' . json_encode($this->selected_flt));
            $cache = JFactory::getCache('mod_cf_filtering.ranges', 'output');
            $lt = (int) $this->componentparams->get('cache_time', 180);
            $cache->setCaching(1);
            $cache->setLifeTime($lt);
            $ranges = $cache->get($store_id);
        }
        if (empty($ranges)) {
            $ranges = $this->getActiveOptions('price');
            if (is_array($ranges))
                $ranges = reset($ranges);
            $ranges = $this->caclFinalPrice($ranges);
            if (! empty($ranges->min_value))
                $ranges->min_value = floor($ranges->min_value);
            if (! empty($ranges->max_value))
                $ranges->max_value = ceil($ranges->max_value);
            if ($use_external_cache)
                $cache->store($ranges, $store_id);
        }
        return $ranges;
    }

    /**
     * Calculates the final price of the ranges
     *
     * @param object $ranges
     *
     * @return object
     * @since 2.2.0
     */
    public function caclFinalPrice($ranges)
    {
        if (empty($ranges)) {
            return $ranges;
        }

        $app = JFactory::getApplication();
        $min_value_tmp = 0;

        /* Get the vendor's currency and the site's currency */
        $vendor_currency = cftools::getVendorCurrency();
        $vendor_currency_details = cftools::getCurrencyInfo($vendor_currency['vendor_currency']);
        $virtuemart_currency_id = $this->input->get('virtuemart_currency_id', $vendor_currency['vendor_currency'], 'int');
        $shop_currency_id = $app->getUserStateFromRequest("virtuemart_currency_id", 'virtuemart_currency_id', $virtuemart_currency_id);

        /*
         * vendor's currency is different than the shop's currency.
         * The prices need convertion to the shop's currency
         */
        if ((int) $vendor_currency['vendor_currency'] != $shop_currency_id) {
            // create a currency object which will be used later
            if (! class_exists('CurrencyDisplay'))
                require_once (JPATH_VM_ADMIN . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'currencydisplay.php');
            $vmCurrencyHelper = CurrencyDisplay::getInstance();
            if (! empty($ranges->min_value))
                $ranges->min_value = $vmCurrencyHelper->convertCurrencyTo($shop_currency_id, $ranges->min_value, $shop = false);
            if (! empty($ranges->max_value))
                $ranges->max_value = $vmCurrencyHelper->convertCurrencyTo($shop_currency_id, $ranges->max_value, $shop = false);
        }

        $calc_rules = cftools::getCalcRules();
        if (empty($calc_rules)) {
            return $ranges;
        }

        /*
         * Add the calc. rules to get the final prices
         */
        if (! empty($ranges->min_value)) {
            $min_value_tmp = $ranges->min_value;
            $ranges->min_value = $this->addCalcRulesByCalcType($ranges->min_value, $calc_rules);
        }
        if (! empty($ranges->max_value)) {
            // min and max are the same. This can happen if only 1 product is returned
            if ($min_value_tmp == $ranges->max_value) {
                $ranges->max_value = $ranges->min_value;
            } else {
                $ranges->max_value = $this->addCalcRulesByCalcType($ranges->max_value, $calc_rules);
            }
        }

        return $ranges;
    }

    /**
     * Gets a group of cacl rules and subtract them from the price
     *
     * @param float $price
     * @param array $calc_group
     *
     * @since 2.2.2
     */
    public function addCalcRulesByCalcType($price, array $calc_group = [])
    {
        foreach ($calc_group as $calc) {
            $price = $this->addCalcRule($price, $calc);
        }
        return $price;
    }

    /**
     * Adds a calculation rule from the price to get the final price
     *
     * @param float $price
     *            - The price
     * @param objecr $calc
     *            - The calc rule object
     *
     * @since 2.2.2
     */
    public function addCalcRule($price, $calc)
    {
        $value = $calc->calc_value;
        $mathop = $calc->calc_value_mathop;
        $currency = $calc->calc_currency;

        if ($value != 0) {
            $coreMathOp = array(
                '+',
                '-',
                '+%',
                '-%'
            );
            if (in_array($mathop, $coreMathOp)) {
                $sign = substr($mathop, 0, 1);
            }
            if (strlen($mathop) == 2) {
                $cmd = substr($mathop, 1, 2);
                // revert
                if ($cmd == '%') {
                    $calculated = $price * ($value / 100);
                }
            } elseif (strlen($mathop) == 1) {
                $calculated = $value;

                /* Get the vendor's currency and the site's currency */
                $app = JFactory::getApplication();
                $vendor_currency = cftools::getVendorCurrency();
                $vendor_currency_details = cftools::getCurrencyInfo($vendor_currency['vendor_currency']);
                $virtuemart_currency_id = $this->input->get('virtuemart_currency_id', $vendor_currency['vendor_currency'], 'int');
                $shop_currency_id = $app->getUserStateFromRequest("virtuemart_currency_id", 'virtuemart_currency_id', $virtuemart_currency_id);

                // create a currency object which will be used later
                if (! class_exists('CurrencyDisplay')) {
                    require_once (JPATH_VM_ADMIN . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'currencydisplay.php');
                }
                $vmCurrencyHelper = CurrencyDisplay::getInstance();

                // then its a price and needs to be in the correct currency
                if ((int) $vendor_currency['vendor_currency'] != $shop_currency_id) {
                    if (! empty($ranges->min_value)) {
                        $calculated = $vmCurrencyHelper->convertCurrencyTo($calculated, $shop_currency_id, $shop = false);
                    }
                }
            }
            if ($sign == '+') {
                return $price += $calculated;
            } elseif ($sign == '-') {
                return $price -= $calculated;
            } else
                return $price;
        } else
            return $price;
    }

    /**
     * Build the query for the price ranges
     *
     * @param
     *            object The db query Object
     * @param
     *            Boolean Indicates if this is a query part or the whole query
     *
     * @return JDatabaseQuery The db query Object
     * @author Sakis Terz
     * @since 2.2.2
     */
    public function buildPriceRangeQuery(JDatabaseQuery $query, $part = false)
    {
        $query->select('MIN(pp.product_price) AS min_value, MAX(pp.product_price) AS max_value');
        $query->from('#__virtuemart_product_prices AS pp');
        $query->innerJoin('#__virtuemart_products AS p ON pp.virtuemart_product_id=p.virtuemart_product_id');
        $query->where('p.published=1');

        // get into account parent/child products
        if ($this->componentparams->getReturnedProductsType() == 'child' || $this->componentparams->getFilteredProductsType() == 'child') {
            $query->where('p.product_parent_id>0');
        } else {
            $query->where('p.product_parent_id=0');
        }
        return $query;
    }

    /**
     * Gets the options of a custom filter
     *
     * @param stdClass The custom filter object
     *
     * @return array list of objects with the available options
     * @author Sakis Terz
     * @since 1.0
     */
    public function getCustomOptions($customfilter)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query = $this->buildCustomFltQuery($query, $customfilter);

        $db->setQuery($query);
        $options = $db->loadObjectList();
        // if($db->getErrorMsg())JFactory::getApplication()->enqueueMessage($db->getErrorMsg());
        $sort_by = 'name';
        if ($customfilter->is_list && ! empty($customfilter->custom_value) || $customfilter->field_type == 'E') {
            $sort_by = 'default_values';
        }
        $opt_array = $this->encodeOptions($options);
        
        // sort after the translation
        if ($sort_by == 'name') {
            $this->sort_by($sort_by, $opt_array); // sort alphabetically
        }
        return $opt_array;
    }

    /**
     * Build the query for the custom options
     *
     * @param JDatabaseQuery $query
     *
     * @param
     *            int The virtuemart_custom_id
     * @param
     *            String The type of the custom field
     * @param
     *            Boolean Indicates if this is a query part or the whole query
     *
     * @return JDatabaseQuery The db query Object
     * @author Sakis Terz
     * @since 1.5.0
     */
    public function buildCustomFltQuery(JDatabaseQuery $query, $customfilter, $part = false)
    {
        JPluginHelper::importPlugin('vmcustom');
        $id = $customfilter->custom_id;
        $field_type = $customfilter->field_type;
        $suffix = $this->fltSuffix['custom_f'];
        $displayCounterSetting = $this->moduleparams->get($suffix . '_display_counter_results', 0);
        $display_empty_opt = $this->moduleparams->get('custom_flt_disable_empty_filters', '1');
        $returned_products=$this->componentparams->getReturnedProductsType();
        $filtered_products=$this->componentparams->getFilteredProductsType();
        $db = JFactory::getDbo();

        // is plugin
        if ($field_type == 'E') {
            if (! isset($customfilter->pluginparams)) {
                return;
            }
            $pluginparams = $customfilter->pluginparams;
            $customvalues_table = $pluginparams->customvalues_table;
            $product_customvalues_table = $pluginparams->product_customvalues_table;
            $filter_by = $pluginparams->filter_by_field;
            $customvalue_value_field = $pluginparams->customvalue_value_field;
            // if the values and the product relationships are in the different tables
            if ($product_customvalues_table != $customvalues_table) {
                $query->innerJoin($product_customvalues_table . ' AS cfp ON cf.' . $filter_by . '=cfp.' . $filter_by);
            }
        } else {
            $customvalues_table = 'cfp';
            $product_customvalues_table = '#__virtuemart_product_customfields';
            $filter_by = 'customfield_value';
        }

        /*
         * count results only when the $displayCounterSetting is active and there is no selection
         * or when the $displayCounterSetting is active and the only selection is that cf
         * in all other cases the counting will be done within the getActiveOptions function
         */
        if ($displayCounterSetting) {
            $table = 'cfp';
            $selectType = "";

            // if return child products
            if ($returned_products == 'child') {
                $query->select("SUM(CASE WHEN p.product_parent_id>0 THEN 1 ELSE 0 END) AS counter");
            }
            // if return parent products
            else if ($returned_products == 'parent') {
                if($filtered_products == 'all') {
                    $query->select("SUM(CASE WHEN p.product_parent_id=0 THEN 1 ELSE 0 END)+COUNT(DISTINCT p.product_parent_id) AS counter");
                }
                // return parents and generate filters from parents
                 elseif ($filtered_products == 'parent') {
                     $query->select("SUM(CASE WHEN p.product_parent_id=0 THEN 1 ELSE 0 END) AS counter");
                }
                // return parents and generate filters from child
                else if ($filtered_products == 'child'){
                    $query->select("COUNT(DISTINCT p.product_parent_id) AS counter");
                }
            }
            // if return all products
            else {
                $query->select("COUNT(p.virtuemart_product_id) AS counter");
            }
        } else {
            $selectType = "DISTINCT";
        }

        if ($displayCounterSetting || $part) {

            // join the products table to check for unpublished
            $query->innerJoin("`#__virtuemart_products` AS p ON cfp.virtuemart_product_id = p.`virtuemart_product_id`");
            $query->where(" p.published=1");
            $query->group('cfp.' . $filter_by);

            // stock control
            if (! VmConfig::get('use_as_catalog', 0)) {
                if (VmConfig::get('stockhandle', 'none') == 'disableit_children') {
                    $query->where('(p.`product_in_stock` - p.`product_ordered` >0 OR children.`product_in_stock` - children.`product_ordered` >0)');
                    $query->leftJoin('`#__virtuemart_products` AS children ON p.`virtuemart_product_id` = children.`product_parent_id`');
                } elseif (VmConfig::get('stockhandle', 'none') == 'disableit') {
                    $query->where('(p.`product_in_stock` - p.`product_ordered` >0)');
                }
            }

            // use of shopper groups
            if (count($this->shopperGroups) > 0 && $this->componentparams->get('products_multiple_shoppers', 0)) {
                $query->innerJoin("(SELECT cfp.virtuemart_product_id,s.`virtuemart_shoppergroup_id` FROM `#__virtuemart_product_shoppergroups` AS s
					RIGHT JOIN " . $product_customvalues_table . " AS cfp ON cfp.virtuemart_product_id =s.virtuemart_product_id WHERE
					(s.`virtuemart_shoppergroup_id` IN(" . implode(',', $this->shopperGroups) . ") OR (s.`virtuemart_shoppergroup_id`) IS NULL) GROUP BY cfp.virtuemart_product_id) AS sp
					ON  cfp.virtuemart_product_id=sp.virtuemart_product_id");
            }
        }

        // if not plugin
        if ($field_type != 'E') {
            // when boolean display Yes or No in case of 0 and 1
            if ($field_type == 'B') {
                $jyes = JText::_('JYES');
                $jno = JText::_('JNO');
                $name_string = "(CASE WHEN cfp.customfield_value='0' THEN '{$jno}' ELSE '{$jyes}' END) AS name";
            } else
                $name_string = "cfp.customfield_value AS name";

            $query->select("$selectType cfp.customfield_value AS id,$name_string");
            $query->from('#__virtuemart_product_customfields AS cfp');           
            if (! $part) {
                $query->where("cfp.virtuemart_custom_id =" . $id);
            }
            $order = '`name` ASC';

            // if its a list get the list ordering, otherwise alphabetically
            if ($customfilter->is_list && ! empty($customfilter->custom_value)) {
                $is_list = true;
                $defaultValues = explode(';', $customfilter->custom_value);
                if ($defaultValues !== false) {
                    $counter = count($defaultValues);
                    $orderfields = '';
                    // default values need to be quoted and escpaed
                    for ($i = 0; $i < $counter; $i ++) {
                        $orderfields .= $db->quote(trim($defaultValues[$i]));
                        if ($i < $counter - 1)
                            $orderfields .= ',';
                    }
                    if (! empty($orderfields))
                        $order = 'FIELD(cfp.customfield_value,' . $orderfields . ')';
                }
            }
        }

        // plugins should exec that function (plugin hook)
        else {
            $query->select("$selectType cf.$filter_by AS id,$customvalue_value_field AS name");
            $query->from($customvalues_table . ' AS cf');
            $pluginparams = $customfilter->pluginparams;
            if(!empty($pluginparams->custom_parent_id) && isset($this->selected_flt['custom_f_'.$pluginparams->custom_parent_id])) {
                $query->where("cf.parent_id IN(".implode(',', $this->selected_flt['custom_f_'.(int)$pluginparams->custom_parent_id]).")");
            }
            if (! $part) {
                $query->where("cf.virtuemart_custom_id={$id}");
            }
            $order = $pluginparams->sort_by; // change that later
        }
        $query->order("$order");
        $q = (string)$query;
        return $query;
    }

    /**
     * Encode the option's value
     * Options may contain special characters, which will break the url query
     * So we convert them to hex values
     *
     * @param
     *            Array An object array with the options
     *
     * @return Array object array with the value attribute in hex format
     * @since 1.0
     * @author Sakis Terz
     */
    public function encodeOptions($opt_array)
    {
        if (empty($opt_array)) {
            return $opt_array;
        }
        $new_opt_array = array();
        $i = 1; // it must be >0. 0 is used for the clear (1st option)
        foreach ($opt_array as $op) {
            if (!empty($op->name)) {
                // translate only if it can be translated
                preg_match('/^[0-9A-Z_-]+$/i', $op->name, $matches);
                if (! empty($matches[0])) {
                    $op->name = JText::_($op->name);
                }
                $op->id = bin2hex(trim($op->id));
                $new_opt_array[$i] = $op;
                $i ++;
            }
           
        }
        return $new_opt_array;
    }

    /**
     * Sort the options in ascending order
     * Options may translated in other languages, so they need to be translated
     *
     * @param
     *            Array An object array with the options
     * @return Array
     * @since 1.1
     * @author Sakis Terz
     */
    public function sort_by($field = 'name', &$arr, $sorting = SORT_ASC, $case_insensitive = false)
    {
        if (is_array($arr) && (count($arr) > 0)) {
            if ($case_insensitive == true)
                $strcmp_fn = "strnatcasecmp";
            else
                $strcmp_fn = "strnatcmp";

            if ($sorting == SORT_ASC) {
                $fn = create_function('$a,$b', '
                    if(is_object($a) && is_object($b)){
                        return ' . $strcmp_fn . '($a->' . $field . ', $b->' . $field . ');
                    }elseif(is_array($a) && is_array($b)){
                        return ' . $strcmp_fn . '($a["' . $field . '"], $b["' . $field . '"]);
                    }else return 0;
                ');
            } else {
                $fn = create_function('$a,$b', '
                    if(is_object($a) && is_object($b)){
                        return ' . $strcmp_fn . '($b->' . $field . ', $a->' . $field . ');
                    }elseif(is_array($a) && is_array($b)){
                        return ' . $strcmp_fn . '($b["' . $field . '"], $a["' . $field . '"]);
                    }else return 0;
                ');
            }
            usort($arr, $fn);
            // sort correctly - usort removes the array keys
            $arr = cftools::arrayFromValList($arr);
            return true;
        } else {
            return false;
        }
    }   
}
