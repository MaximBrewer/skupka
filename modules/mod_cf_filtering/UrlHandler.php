<?php
require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_customfilters' . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'tools.php';

class UrlHandler
{
    /**
     *
     * @var stdClass
     */
    protected $module;

    /**
     *
     * @var array
     */
    protected $selected_flt;

    /**
     *
     * @var unknown
     */
    protected $selected_flt_modif;

    /**
     *
     * @var array
     */
    protected $selected_fl_per_flt;

    /**
     *
     * @var Joomla\Registry\Registry
     */
    protected $moduleParams;

    /**
     *
     * @var Joomla\Registry\Registry
     */
    protected $menuParams;

    /**
     *
     * @var bool|array
     */
    protected $hiddenCategory;

    /**
     *
     * @var int
     */
    protected $parentCategoryId;

    /**
     *
     * @param unknown $module
     * @param array $selected_filters
     */
    public function __construct($module, $selected_filters = [])
    {
        $this->module = $module;
        $this->selected_flt = $selected_filters['selected_flt'];
        $this->selected_flt_modif = $selected_filters['selected_flt_modif'];
        $this->selected_fl_per_flt = $selected_filters['selected_fl_per_flt'];
        $this->moduleParams = cftools::getModuleparams($module);
        $this->menuParams = cftools::getMenuparams();
    }

    /**
     * Creates the href/URI for each filter's option
     *
     * @param array $filter
     * @param string $var_value
     * @param string $type
     *            the type of url (option|clear)
     *
     * @author Sakis Terz
     * @return String URI
     * @since 1.0
     */
    public function getURL($filter, $var_value = NULL, $type = 'option')
    {
        $var_name = $filter['var_name'];
        $display_type = $filter['display'];
        $on_category_reset_others = false;
        $selected_filters = $this->selected_flt_modif;
        $results_trigger=$this->moduleParams->get('results_trigger','sel');

        if ($var_name == 'virtuemart_category_id') {
            $on_category_reset_others = $this->moduleParams->get('category_flt_onchange_reset', 'filters');
            if ($on_category_reset_others) {
                if (! empty($selected_filters['virtuemart_category_id']))
                    $categ_array = $selected_filters['virtuemart_category_id'];
                else
                    $categ_array = array();
            }
        } else {
            //set category to the rest of the filters when no category is selected, in case of only sub-categories display
            if ($this->moduleParams->get('category_flt_only_subcats', false) && $this->getHiddenCategory()) {
                $selected_filters['virtuemart_category_id'] = $this->getHiddenCategory();
            }
        }

        // in case of dependency top-bottom get the selected that this filter should use
        if ($this->moduleParams->get('dependency_direction', 't-b') == 't-b') {
            if (isset($this->selected_fl_per_flt[$var_name])) {
                $q_array = $this->selected_fl_per_flt[$var_name];
            }
            else {
                $q_array = array();
            }
        }

        // on category selection clear others
        else
            if ($on_category_reset_others) {
                $q_array['virtuemart_category_id'] = $categ_array;
                if ($on_category_reset_others == 'filters') {
                    ! empty($this->selected_flt['q']) ? $q_array['q'] = $this->selected_flt['q'] : '';
                }
            } else {
                $q_array = $selected_filters;
            }

        // in case of category tree, the parent options are always links, no matter what is the display type of the filter
        if (! empty($filter['options'][$var_value]['isparent'])) {
            $display_type = 4;
        }

        // do not include also the parents in the urls of the child
        if (! empty($filter['options'][$var_value]['cat_tree'])) {
            $parent_cat = explode('-', $filter['options'][$var_value]['cat_tree']);
            foreach ($parent_cat as $pcat) {
                if (isset($q_array[$var_name])) {
                    $index = array_search($pcat, $q_array[$var_name]);
                    if ($index !== false) {
                        unset($q_array[$var_name][$index]);
                    }
                }
            }
        }

        /*
         * in case of select , radio or links (single select) or is clear remove previous selected criteria from the same filter
         * only 1 option from that filter should be selected
         */
        if (($display_type != 3 && $display_type != 10 && $display_type != 12) || $type == 'clear') {
            $q_array=$this->getClearQuery($q_array, $filter, $type);
        }

        /*
         * in case an option is already selected
         * The destination link of that option should omit it's value in case of checkboxes or multi-button
         * to create the uncheck effect
         */
        if (($display_type == 3 || $display_type == 10 || $display_type == 12) && (isset($q_array[$var_name]) && in_array($var_value, $q_array[$var_name]))) {
            if (is_array($q_array[$var_name])) {
                $key = array_search($var_value, $q_array[$var_name]);
                unset($q_array[$var_name][$key]);
                $q_array[$var_name] = array_values($q_array[$var_name]); // reorder to fill null indexes
                if (count($q_array[$var_name]) == 0)
                    unset($q_array[$var_name]); // if no any value unset it
            }
        }

        /* if not exist add it */
        else {
            if ($var_value) {
                if (isset($q_array[$var_name]) && is_array($q_array[$var_name])) {

                    // remove the null option which used only for sef reasons
                    if (isset($q_array[$var_name][0])) {
                        if ($q_array[$var_name][0] == '0' || $q_array[$var_name][0] == ' ') {
                            $q_array[$var_name][0] = $var_value;
                        }
                    }

                    $q_array[$var_name][] = $var_value;
                } else
                    $q_array[$var_name] = array(
                        $var_value
                    );
            }
        }

        /*
         * If the custom filters won't be displayed in the page in case a vm_cat and/or a vm_manuf is not selected
         * remove the custom filters from the query too
         */
        if ($var_name == 'virtuemart_category_id' || $var_name == 'virtuemart_manufacturer_id') {
            $cust_flt_disp_if = $this->moduleParams->get('custom_flt_disp_after');

            if (($cust_flt_disp_if == 'vm_cat' && $var_name == 'virtuemart_category_id') || ($cust_flt_disp_if == 'vm_manuf' && $var_name == 'virtuemart_manufacturer_id')) {
                // if no category or manuf in the query
                // remove all the custom filters from the query as the custom filters won't displayed
                if (! isset($q_array[$var_name]) || count($q_array[$var_name]) == 0) {
                    $this->unsetCustomFilters($q_array);
                }
            } else
                if ($cust_flt_disp_if == 'vm_cat_or_vm_manuf' && ($var_name == 'virtuemart_category_id' || $var_name == 'virtuemart_manufacturer_id')) {
                    if (! isset($q_array['virtuemart_category_id']) && ! isset($q_array['virtuemart_manufacturer_id'])) {
                        $this->unsetCustomFilters($q_array);
                    }
                } else
                    if ($cust_flt_disp_if == 'vm_cat_and_vm_manuf' && ($var_name == 'virtuemart_category_id' || $var_name == 'virtuemart_manufacturer_id')) {
                        if (! isset($q_array['virtuemart_category_id']) || ! isset($q_array['virtuemart_manufacturer_id'])) {
                            $this->unsetCustomFilters($q_array);
                        }
                    }
        }

        $itemId = $this->menuParams->get('cf_itemid', '');
        if ($itemId) {
            $q_array['Itemid'] = $itemId;
        }
        $q_array['option'] = 'com_customfilters';
        $q_array['view'] = 'products';

        // if trigger is on select load results
        // else load the module
        if ($results_trigger == 'btn') {
            unset($q_array['Itemid']);
            $q_array['module_id'] = $this->module->id;
        }

        $u = JFactory::getURI();
        $query = $u->buildQuery($q_array);
        $uri = 'index.php?' . $query;
        return $uri;
    }

    /**
     * Used in case a category is not displayed (e.g.
     * only child are displayed)
     *
     * @return boolean|Integer
     */
    protected function getHiddenCategory()
    {
        if (! isset($this->hiddenCategory)) {
            $this->hiddenCategory = false;
            if (isset($this->selected_flt['virtuemart_category_id']) &&
                count($this->selected_flt['virtuemart_category_id']) == 1 &&
                empty($this->selected_flt_modif['virtuemart_category_id'])) {
                $this->hiddenCategory = $this->selected_flt['virtuemart_category_id'];
            }
        }
        return $this->hiddenCategory;
    }

    /**
     *
     * @param int $category_id
     */
    protected function getParentCategoryId($category_id)
    {
        if (! $this->parentCategoryId) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('category_parent_id')
                ->from('#__virtuemart_category_categories')
                ->where('category_child_id=' . (int) $category_id);
            $db->setQuery($query);
            $this->parentCategoryId = $db->loadResult();
        }
        return $this->parentCategoryId;
    }

    /**
     *
     * @param unknown $q_array
     * @param string $type
     */
    protected function getClearQuery($q_array, $filter, $type='clear')
    {
        $var_name = $filter['var_name'];

        // clear all the selections in all filters. e.g. search
        if ($type == 'clear' && $filter['clearType'] == 'all') {
            $q_array=[];
        }

        // clear only the selections in that filter
        else {
            $selection = isset($q_array[$var_name]) && is_array($q_array[$var_name])?reset($q_array[$var_name]):false;
            unset($q_array[$var_name]);
        }
        return $q_array;
    }

    /**
     * Unset any custom filter found from the assoc array
     *
     * @param 	Array	An array tha conains the vars of the query
     * @author	Sakis Terz
     * @return
     * @since 	1.0
     */
    protected function unsetCustomFilters(&$query)
    {
        $published_cf=cftools::getCustomFilters();
        if(isset($published_cf)){
            foreach($published_cf as $cf) {
                $cf_var_name='custom_f_'.$cf->custom_id;
                if(isset($query[$cf_var_name]))unset($query[$cf_var_name]);
            }
        }
    }
    
    /**
     * creates the reset uri
     *
     * @author Sakis Terz
     * @since 1.5.0
     * @return string
     */
    public function getResetUri()
    {
        $resetfields = $this->moduleParams->get('reset_all_reset_flt', array(
            'virtuemart_manufacturer_id',
            'price',
            'custom_f'
        ), 'array');
        $itemId = $this->menuParams->get('cf_itemid', '');
        $q_array = array();
        $q_array['option'] = 'com_customfilters';
        $q_array['view'] = 'products';
        if (! empty($itemId))
            $q_array['Itemid'] = $itemId;
    
        foreach ($this->selected_flt as $key => $selected) {
            $new_key = strpos($key, 'custom_f_') !== false ? 'custom_f' : $key;
            if (! in_array($new_key, $resetfields))
                $q_array[$key] = $selected;
        }
        $virtuemart_category_id = '';
        /*
         * if no category filter and category var. Or (category filter and category var and option=virtuemart)
         * It means that we are in a category page and the category id should be kept
         */
        if (isset($this->selected_flt['virtuemart_category_id']) && $this->moduleParams->get('category_flt_published', 0) == false) {
            $q_array['virtuemart_category_id'] = $this->selected_flt['virtuemart_category_id'][0];
        }
        $u = JFactory::getURI();
        $query = $u->buildQuery($q_array);
        $uri = 'index.php?' . $query;
        return $uri;
    }
}