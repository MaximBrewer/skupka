<?php
/**
 *
 * Customfilters products model
 *
 * @package		customfilters
 * @author		Sakis Terz
 * @link		http://breakdesigns.net
 * @copyright	Copyright (c) 2012-2018 breakdesigns.net. All rights reserved.
 * @license		see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');
jimport( 'joomla.application.module.helper' );

require_once(JPATH_VM_ADMIN . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'product.php');

use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Factory as JFactory;

/**
 * Class containing the main logic of the component
 * @author sakis
 *
 */
class CustomfiltersModelProducts extends VirtueMartModelProduct
{
    /**
     *
     * @var string
     */
	protected $context = 'com_customfilters.products';

	/**
	 *
	 * @var array
	 */
	private $published_cf;

	/**
	 *
	 * @var int
	 */
	public $total;

	/**
	 *
	 * @var \CurrencyDisplay
	 */
	public $vmCurrencyHelper;

	/**
	 *
	 * @var \CustomfiltersConfig
	 */
	protected $componentparams;

	/**
	 *
	 * @var \Joomla\Registry\Registry
	 */
	protected $menuparams;

	/**
	 *
	 * @var \Joomla\Registry\Registry
	 */
	protected $moduleparams;

	/**
	 *
	 * @var array
	 */
	protected $found_product_ids=[];

	/**
	 *
	 * @var string
	 */
	public $vmVersion;

	/**
	 *
	 * @var string
	 */
	protected $currentLangPrefix;

	/**
	 *
	 * @var string
	 */
    protected $defaultLangPrefix;

	/**
	 * The class constructor
	 * @since	1.0
	 * @author	Sakis Terz
	 */
	public function __construct($config = array())
    {
        $module = cftools::getModule();
        $this->menuparams = cftools::getMenuparams();
        $this->moduleparams = cftools::getModuleparams();
        $this->componentparams = CustomfiltersConfig::getInstance();
        $this->cfinputs = CfInput::getInputs();
        $this->vmVersion = VmConfig::getInstalledVersion();
        $this->currentLangPrefix = cftools::getCurrentLanguagePrefix();
        $this->defaultLangPrefix = cftools::getDefaultLanguagePrefix();
        parent::__construct($config);
    }

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.0
	 */
	protected function populateState($ordering = 'ordering', $direction = 'ASC')
	{
		$app = JFactory::getApplication();
		$jinput=$app->input;
		$view = $jinput->get('view','products','cmd');

		//check multi-language
		$plugin =JPluginHelper::getPlugin('system', 'languagefilter');
		$this->setState('langPlugin', $plugin);

		// List state information
		$default_limit=!empty($this->menuparams)?$this->menuparams->get('pagination_default_value','24'):VmConfig::get ('list_limit', 20);
		$limit = $app->getUserStateFromRequest('com_customfilters.products.limit', 'limit', $default_limit,'int');
		$limitstart = $jinput->get('limitstart', 0,'uint');

		//get the order by field
		$filter_order = $jinput->get('orderby',$this->filter_order,'string');

		//sanitize the order by
		$filter = JFilterInput::getInstance();
		$filter_order_fields=explode(',', $filter_order);

		foreach ($filter_order_fields as &$order_by_field){
		    $order_by_field=$filter->clean($order_by_field,'cmd');
		}
		$order_by_string=implode(',', $filter_order_fields);

		//check also against the allowed order by fields
		if(method_exists($this, 'checkFilterOrder'))$order_by_string = $this->checkFilterOrder ($order_by_string);

		//get the order by direction
		$this->filter_order_Dir= strtoupper($jinput->get('order', VmConfig::get('prd_brws_orderby_dir', 'ASC'),'cmd'));

		//sanitize Direction in case of invalid input
		if(!in_array($this->filter_order_Dir, array('ASC','DESC'))){
			$this->filter_order_Dir ='ASC';
		}

		$this->setState('list.limitstart', $limitstart);
		$this->setState('list.limit', $limit);
		$this->setState('filter_order', $order_by_string);
		$this->setState('filter_order_Dir', $this->filter_order_Dir);
	}

    /**
     * (non-PHPdoc)
     * @see VirtueMartModelProduct::getProductListing()
     */
	public function getProductListing(
	    $group = false,
	    $nbrReturnProducts = false,
	    $withCalc = true,
	    $onlyPublished = true,
	    $single = false,
	    $filterCategory = true,
	    $category_id = 0,
	    $filterManufacturer = true,
	    $manufacturer_id = 0)
	{
		$front = true;
		$user = JFactory::getUser();
		if (!($user->authorise('core.admin','com_virtuemart') or $user->authorise('core.manage','com_virtuemart'))) {
			$onlyPublished = true;
			if ($show_prices=VmConfig::get('show_prices',1) == '0'){
				$withCalc = false;
			}
		}

		//get the published custom filters
		$this->published_cf=cftools::getCustomFilters('');
		$ids = $this->sortSearchListQuery($onlyPublished,$vmcat=false,$group,$nbrReturnProducts);
		return $ids;
	}

	/**
	 *
	 * Returns the product ids after running the filtering sql queries
	 * Overriddes the function defined in the com_virtuemart/models/product.php
	 *
	 * @param 	boolen	$onlyPublished only the published products
	 * @param 	string	$group	indicates some predefined groups
	 * @param 	Int $nbrReturnProducts
	 *
	 * @since	1.0
	 *
	 * @return  array  product ids
	 *
	 * @todo	Avoid joins if only 1 filter is selected. Just get the product id from it's table
	 */
	public function sortSearchListQuery($onlyPublished=true, $virtuemart_category_id=false, $group=false, $nbrReturnProducts=false, $langFields = array())
	{
		if($this->moduleparams->get('cf_profiler',0))$profiler=JProfiler::getInstance('application');
		if($this->moduleparams->get('cf_profiler',0))$profiler->mark('start');
		$app = JFactory::getApplication() ;
		$db=JFactory::getDbo();
		$where_product_ids=array();
		
		//Creates a logger for that extension
		cftools::addLogger();		
		$resetType=$this->componentparams->get('reset_results',0);
		if($resetType==0 && empty($this->cfinputs))return;
		
		$queryBuilder = new ProductsQueryBuilder($this->componentparams->getFilteredProductsType(), $this->componentparams->getReturnedProductsType());
        
		//keyword search
		if(!empty($this->cfinputs['q'])){
			$where_product_ids[]=$this->getProductIdsFromSearch();
			if(empty($where_product_ids))return;
			if(!empty($profiler))$profiler->mark('After Keyword Search');
		}

		//generate categories filter query
		if(isset($this->cfinputs['virtuemart_category_id'])){
			$vm_categories=$this->cfinputs['virtuemart_category_id'];
			if(isset($vm_categories[0])){
				$queryBuilder->setWhere('p_c.virtuemart_category_id', $vm_categories);	
			}
		}

		//generate manufacturers filter query
		if(isset($this->cfinputs['virtuemart_manufacturer_id'])) {
			$vm_manufacturers=$this->cfinputs['virtuemart_manufacturer_id'];
    		if(isset($vm_manufacturers[0])){
    			$queryBuilder->setWhere('p_m.virtuemart_manufacturer_id', $vm_manufacturers);	
    		}
		}

		//generate price filter query
		if(isset($this->cfinputs['price'][0]))$price_from=$this->cfinputs['price'][0];
		if(isset($this->cfinputs['price'][1]))$price_to=$this->cfinputs['price'][1];

		if (! empty($price_from) || ! empty($price_to)) {
            $productIdsByPrice = $this->getProductIdsByPrice();
            if (! empty($productIdsByPrice)) {
                $where_product_ids[] = $productIdsByPrice;
            } else 
                if (is_array($productIdsByPrice)) {
                    return;
                }
            if (! empty($profiler)) {
                $profiler->mark('After Price Range Search');
            }
        }

		//generate Custom fields filter
		$customFilters=$this->published_cf;
		$cfilter_found=false;

		if(!empty($customFilters)){
			$query = $queryBuilder->getQuery();
			foreach($customFilters as $cf){
				$cf_name='custom_f_'.$cf->custom_id;
				
				//if not range
				if($cf->disp_type!=5 && $cf->disp_type!=6 && $cf->disp_type!=8){
					if(!isset($this->cfinputs[$cf_name])) {
						continue;
					}
					
					//set the selected cfs
					$selected_cf=$this->cfinputs[$cf_name];						
					$cfilter_found=true;
					$custom_search=array();

					//not plugin
					if($cf->field_type!='E'){
						$product_customvalues_table='`#__virtuemart_product_customfields`';
						foreach($selected_cf as $cf_value){
							if(isset($cf_value)) {
								$custom_search[] ="(".$cf_name.'.customfield_value =' .$db->quote( $cf_value, true ).' AND '.$cf_name.'.virtuemart_custom_id='.(int)$cf->custom_id.")";
							}
						}
						if(!empty($custom_search)) {
							$where[] = " (".implode(' OR ', $custom_search ).") ";
						}
					}

					//plugins
					else
					{
						//if the plugin has not declared the necessary params go to the next selected vars
						if(empty($cf->pluginparams))continue;

						//get vars from plugins
						$product_customvalues_table=$cf->pluginparams->product_customvalues_table;
						$sel_field=$cf->pluginparams->filter_by_field;
						$filter_data_type=$cf->pluginparams->filter_data_type;
						$cf_values=$selected_cf;

						//string escape and quote each value
						if($filter_data_type=='string'){
							foreach($cf_values as $cf_val){
								if(isset($cf_val))$custom_search[] =$cf_name.'.'.$sel_field.' = '.$db->quote( $cf_val, true );
							}

							if(count($custom_search)>0){
								if($cf->pluginparams->product_customvalues_table==$cf->pluginparams->customvalues_table)$where[]='(('.implode(' OR ',$custom_search).") AND {$cf_name}.virtuemart_custom_id=".(int)$cf->custom_id.")";
								else $where[]='('.implode(' OR ',$custom_search).")";
							}
						}

						//if not string is number and already filtered in the input
						else{
							if(!empty($cf_values))$where[] =$cf_name.'.'.$sel_field.' IN ('.implode(',',$cf_values).')';
						}
					}
					$query->where($where);
					$query->innerJoin($product_customvalues_table.' AS '.$cf_name.' ON '.$cf_name.'.virtuemart_product_id=p.virtuemart_product_id');

				}
				//range
				else {
					$productIdsByCF=$this->getProductIdsByCfRange($cf);
					if(!empty($productIdsByCF))	$where_product_ids[]=$productIdsByCF;
					else if(is_array($productIdsByCF))return;//there is range set but no product found
					if(!empty($profiler))$profiler->mark('After Range Search Custom Filter:'.$cf->custom_id);
				}
			}
		}
            
        // find the common product ids between all the varriables/intersection
        if (! empty($where_product_ids)) {
            $common_prod_ids = $this->intersectProductIds($where_product_ids);
            if (! empty($common_prod_ids)) {
                $queryBuilder->setWhere('p.virtuemart_product_id', $common_prod_ids);
            }             
            // no product found
            else {
                return;
            }
        }

		//display products in specific shoppers
		$virtuemart_shoppergroup_ids =cftools::getUserShopperGroups();
		if(is_array($virtuemart_shoppergroup_ids) && $this->componentparams->get('products_multiple_shoppers',0)){
			$queryBuilder->getQuery()->where('(s.`virtuemart_shoppergroup_id` IN (' . implode(',',$virtuemart_shoppergroup_ids). ') OR' . ' (s.`virtuemart_shoppergroup_id`) IS NULL )');
			$queryBuilder->setJoin('s');
		}
		
		//stock controls
		if(!VmConfig::get('use_as_catalog',0)) {
		    if (VmConfig::get('stockhandle','none')=='disableit_children') {
		        $queryBuilder->getQuery()->where('(p.`product_in_stock` - p.`product_ordered` >0 OR children.`product_in_stock` - children.`product_ordered` >0)');
		        $queryBuilder->setJoin('children');
		    } else if (VmConfig::get('stockhandle','none')=='disableit') {
		        $queryBuilder->getQuery()->where('p.`product_in_stock` - p.`product_ordered` >0');
		    }
		}
		
		$queryBuilder->setOrder($this->getState('filter_order'), $this->getState('filter_order_Dir'));

		// List state information
		$limit =$this->getState('list.limit',5);
		$limitstart=$this->getState('list.limitstart',0);
      
		//fetch the product ids
		try{
			$query = $queryBuilder->create();
			$q = (string)$query;
    		$db->setQuery($query, $limitstart, $limit);
    		$product_ids =$db->loadColumn();
		}
		catch (RuntimeException $e){
		    JLog::add(
                sprintf('Failed to return products: %s',$e->getMessage()),
                	JLog::ERROR,
		           'customfilters'
                );
		}

		//count the results
		try{
    		$db->setQuery('SELECT FOUND_ROWS()');
    		$this->total=$db->loadResult();
		}
		catch(RuntimeException $e){
		    JLog::add(
		        sprintf('Failed to count products: %s',$e->getMessage()),
		        JLog::ERROR,
		        'customfilters'
		    );
		}

		$app->setUserState("com_customfilters.product_ids",$product_ids);
		if(!empty($profiler))$profiler->mark('Finish Filtering/Search');
		return $product_ids;
	}
	
	
	

	/**
	 * Function that handles the keyword search
	 *
	 * @since	2.2.0
	 * @author	Sakis Terz
	 * @return  array  product ids
	 */
	protected function getProductIdsFromSearch()
	{
		if(empty($this->cfinputs['q']))return false;

		if(!isset($this->found_product_ids['search'])){
			$input=JFactory::getApplication()->input;
			$phrase=$this->cfinputs['q'];
			$language=JFactory::getLanguage();
			$lang=$language->getTag();
			$results=array();
			$searchfields=$this->componentparams->get('keyword_searchfield',array('l.product_name','l.product_s_desc','catlang.category_name','mflang.mf_name','custom'),'array');
			$matching_type=$this->componentparams->get('keyword_search_match','any','string');
			$returned_products=$this->componentparams->get('returned_products','parent');

			$store_id=':'.$phrase;
			$store_id.=':'.$lang;
			$store_id.=':'.$matching_type;

			//try using external cache - if exists
			$cache=JFactory::getCache($this->context,'output');
			$lt=(int)$this->componentparams->get('cache_time',5);
			$cache->setCaching( 1 );
			$cache->setLifeTime($lt);
			$results=$cache->get($store_id);

			if($results!==false){
				$results=unserialize($results);
				//set the product ids in the jinput so it will be used by the module for the getActiveOptions Functionalitity
				$this->found_product_ids['search']=$results;
				$input->set('found_product_ids',$this->found_product_ids);
				return $results;
			}

			$searchHelper=new CfSearchHelper;
			//tokenize the input
			$tokens=$searchHelper->tokenize($phrase, $lang,$matching_type);

			$where_or=array();
			$category_found=array();

			//joins
			$joins=array();
			$join_prodlang=false;
			$join_prodcat=false;
			$join_prodmnf=false;

			$db=JFactory::getDbo();
			$query=$db->getQuery(true);

			foreach ($tokens as $index=>$token){
				$where_or_tmp=array();
				$where_and_tmp=array();
				$query_str='';

				foreach ($searchfields as $searchfield){
					switch ($searchfield) {

						/*
						 * This should run only once
						 * The sku/gtin/mpn should not use the phrase in reverse order.
						 * Because skus are usually strings without spaces, check for every word of the phrase if it matches
						 */
					    case 'p.product_sku':
					    case 'p.product_gtin':
						case 'p.product_mpn':
							if(!empty($token->term) && ($index==0 || (!empty($token->category_id) && !isset($category_found[$token->category_id])))){
								if(!empty($token->category_id))$category_found[$token->category_id]=true;
								$searches=$matching_type=='any'?explode(' ', $token->term):$token->term;

								if(is_array($searches)){
									//the whole input
									$where_or_tmp[]=$searchfield.' LIKE '.$db->quote($token->term.'%');
									foreach ($searches as $search){
									    //we do not want searches for single characters (a,b,c,1...)
										if(strlen($search)>1)$where_or_tmp[]=$searchfield.' LIKE '.$db->quote($search.'%');
									}
									unset($search);
								}else $where_or_tmp[]=$searchfield.' LIKE '.$db->quote($searches.'%');
							}
							break;

						case 'l.product_name':
							if(!empty($token->term)){
								$search=preg_replace('/\s+/', '%', $token->term);
								$where_or_tmp[]=$searchfield.' LIKE '.$db->quote('%'.$search.'%');
								$join_prodlang=true;
							}
							break;

						default:
						case 'l.product_s_desc':
						case 'l.product_desc':
							if(!empty($token->term)){
								$search=$token->term;
								$where_or_tmp[]=$searchfield.' LIKE '.$db->quote('%'.$search.'%');
								$join_prodlang=true;
							}
							break;

							/*
							 * only if no term exist when there are no previous where
							 * That means that it is pure category without the need to search other fields
							 */
						case 'catlang.category_name':
							if(!empty($token->category) && !(empty($where_or_tmp) && !empty($token->term))){
								$where_and_tmp[]='pc.virtuemart_category_id='.(int)$token->category->id;
								$join_prodcat=true;
							}
							break;

						case 'mflang.mf_name':
							if(!empty($token->manufacturer) && !(empty($where_or_tmp) && !empty($token->term))){
								$where_and_tmp[]='p_m.virtuemart_manufacturer_id='.(int)$token->manufacturer->id;
								$join_prodmnf=true;
							}
							break;

						case 'custom':
							if(!empty($token->customvalue)&& !(empty($where_or_tmp) && !empty($token->term))){
								$custom=$token->customvalue;
								$table_alias='cf_'.$custom->custom_id;
								if(!isset($joins[$table_alias]))$joins[$table_alias]=$custom->products_table.' AS '.$table_alias.' ON '.$table_alias.'.virtuemart_product_id=p.virtuemart_product_id';
								$where_and_tmp[]=$table_alias.'.'.$custom->filter_by_field.'='.$db->quote($custom->value);

							}
							break;

					}
				}
				if(!empty($where_or_tmp))$query_str='('.implode(' OR ', $where_or_tmp).')';
				if(!empty($where_and_tmp) || !empty($where_and_tmp)){

					//the concat string that concats the 2 strings
					$concat=!empty($query_str)?' AND ':'';
					$query_str.=$concat.'('.implode(' AND ', $where_and_tmp).')';
				}
				if(!empty($query_str)){
					$query_str='('.$query_str.')';
					$where_or[]=$query_str;
				}
			}

			if(!empty($where_or)){
				$query->select('p.virtuemart_product_id');
				$query->from('#__virtuemart_products AS p');
				$query->where($where_or, 'OR');
				if($join_prodlang)$query->leftJoin('#__virtuemart_products_'.$this->currentLangPrefix.' AS l ON p.virtuemart_product_id=l.virtuemart_product_id');
				if($join_prodcat){
					//in case the
					if($returned_products=='child')$query->innerJoin('#__virtuemart_product_categories AS pc ON pc.virtuemart_product_id=p.product_parent_id');
					else $query->innerJoin('#__virtuemart_product_categories AS pc ON pc.virtuemart_product_id=p.virtuemart_product_id');
				}
				if($join_prodmnf){
					if($returned_products=='child')$query->innerJoin('#__virtuemart_product_manufacturers  AS p_m ON p_m.virtuemart_product_id=p.product_parent_id');
					else $query->innerJoin('#__virtuemart_product_manufacturers  AS p_m ON p_m.virtuemart_product_id=p.virtuemart_product_id');
				}
				if(!empty($joins)){
					foreach ($joins as $join){
						$query->innerJoin($join);
					}
				}

				//fetch the search results
                try{
    				$db->setQuery($query);
    				$results=$db->loadColumn();
                }
                catch (RuntimeException $e){
                    JLog::add(
                        sprintf('Search query error: %s',$e->getMessage()),
                        JLog::ERROR,
                        'customfilters'
                    );
                }
			}

			//store to external cache
			$cache->store(serialize($results), $store_id);

			//store to internal cache
			$this->found_product_ids['search']=$results;
			$input->set('found_product_ids',$this->found_product_ids);
		}

		return $this->found_product_ids['search'];
	}

	/**
	 *Get the product ids from all the used range filters and searches
	 *
	 *@return	array - the product ids
	 *@since	1.6.1
	 *@author	Sakis Terz
	 */
	public function getProductIdsFromSearches()
	{
		if(isset($this->cfinputs['price'][0]))$price_from=$this->cfinputs['price'][0];
		if(isset($this->cfinputs['price'][1]))$price_to=$this->cfinputs['price'][1];

		//keyword search
		if(!empty($this->cfinputs['q'])){
			$productIdsBySearch=$this->getProductIdsFromSearch();
			if(!empty($productIdsBySearch)){
				$where_product_ids[]=$productIdsBySearch;
			}

			//empty results
			else if(is_array($productIdsBySearch))return;
		}

		//price ranges
		if(!empty($price_from) || !empty($price_to)){
			$productIdsByPrice=$this->getProductIdsByPrice();
			if(!empty($productIdsByPrice)){
				$where_product_ids[]=$productIdsByPrice;
			}

			//price set but no product found
			else if(is_array($productIdsByPrice))return;
		}

		$customFilters=cftools::getCustomFilters('');
		$cfilter_found=false;

		//custom field ranges
		if(!empty($customFilters)){
			foreach($customFilters as $cf){
				$cf_name='custom_f_'.$cf->custom_id;
				if($cf->disp_type==5 || $cf->disp_type==6 || $cf->disp_type==8){//if is range
					$productIdsByCF=$this->getProductIdsByCfRange($cf);
					if(!empty($productIdsByCF))	$where_product_ids[]=$productIdsByCF;
					else if(is_array($productIdsByCF))return;//there is range set but no product found
				}
			}
		}
		if(!empty($where_product_ids))$common_prod_ids=$this->intersectProductIds($where_product_ids);
	}

	/**
	 * Intersects the product ids and returns only the common between the used filters
	 *
	 * @param 	array $where_product_ids	- contains the product ids of every used filter
	 * @return	array	the common product ids
	 */
	public function intersectProductIds($where_product_ids)
	{
		//find the common product ids between all the varriables/intersection
		if(!empty($where_product_ids)){
			$ar_counter=count($where_product_ids);
			$where_product_ids_ar=$where_product_ids[0];

			$common_prod_ids=array();
			if($ar_counter==1) $common_prod_ids=$where_product_ids[0];
			else{

				//find the smaller array
				$smaller_array_index=0;
				$smaller_array_counter=count($where_product_ids[0]);
				foreach ($where_product_ids as $key=>$array){
				    if($key==0)continue;
					$current_counter=count($array);
					if($current_counter<$smaller_array_counter){
						$smaller_array_index=$key;
						$smaller_array_counter=$current_counter;
					}
				}
				$smaller_array=$where_product_ids[$smaller_array_index];

                //remove it from the main array
				unset($where_product_ids[$smaller_array_index]);

                //now check the rest of the array against the smallest chunk
				for($m=0; $m<$ar_counter; $m++){
				    if(empty($where_product_ids[$m]))continue;
					$tmp_common_prod_ids=array();
					if(empty($common_prod_ids))$search_into=$smaller_array;
					else $search_into=$common_prod_ids;

					foreach($where_product_ids[$m] as $id){
						if(in_array($id, $search_into))$tmp_common_prod_ids[]=$id;
					}
					//found no match return
					if(empty($tmp_common_prod_ids))return array();
					$common_prod_ids=array_merge($common_prod_ids,$tmp_common_prod_ids);
				}
			}
			if(!empty($common_prod_ids)){
				$app = JFactory::getApplication() ;
				$jinput=$app->input;
				$jinput->set('where_productIds',$common_prod_ids);
				return $common_prod_ids;
			}
		}
		return $common_prod_ids;
	}

   	/**
	 * Returns the product ids based only on the custom filters range
	 * These ids can be used both by the component and the module
	 * in the component's filtering and the module's get active functionalities accordingly
	 *
	 * @author Sakis Terz
	 * @since  1.6.1
	 * @return array
	 */
	protected function getProductIdsByCfRange($cf)
	{
		$japplication=JFactory::getApplication();
		$jinput=$japplication->input;
		$var_name='custom_f_'.$cf->custom_id;
		$product_ids=array();
		$custom_from=0;
		$custom_to=0;

		if(isset($this->cfinputs[$var_name][0]) && $this->cfinputs[$var_name][0]>0)$custom_from=$this->cfinputs[$var_name][0];
		if(isset($this->cfinputs[$var_name][1]) && $this->cfinputs[$var_name][1]>0)$custom_to=$this->cfinputs[$var_name][1];
		if(empty($custom_from) && empty($custom_to))return false;

			$db=$this->_db;
			$query=$db->getQuery(true);

			//not plugin
			if($cf->field_type!='E'){
				$select_field='virtuemart_product_id';
				$from_table='#__virtuemart_product_customfields AS pc';
				$where_field='pc.customfield_value';
			}

			//plugin
			else{
				if(empty($cf->pluginparams))return;
				$select_field='virtuemart_product_id';
				$from_table=$cf->pluginparams->product_customvalues_table;
				$customvalues_table=$cf->pluginparams->customvalues_table;
				$data_type='int';


				if($customvalues_table!=$from_table){
					$from_table=$from_table.' AS pc';
					$where_field='c.'.$cf->pluginparams->customvalue_value_field;
					$filter_by_field=$cf->pluginparams->filter_by_field;
					$query->innerJoin("$customvalues_table c ON c.{$filter_by_field}=pc.{$filter_by_field}");
				}else{
					$where_field=$cf->pluginparams->customvalue_value_field;
				}
			}

			$query->select('DISTINCT '.$select_field);
			$query->from($from_table);


			if($custom_from && empty($custom_to)){
				if($cf->disp_type==8){
					$converted_date_from=cftools::getFormatedDate($custom_from);
					$query->where("STR_TO_DATE($where_field,'%Y-%m-%d')>=".$db->quote($converted_date_from));
				}
				else $query->where("$where_field>=$custom_from");
			}
			else if (empty($custom_from) && $custom_to){
				if($cf->disp_type==8){
					$converted_date_to=cftools::getFormatedDate($custom_to);
					$query->where("STR_TO_DATE($where_field,'%Y-%m-%d')<=".$db->quote($converted_date_to));
				}
				else $query->where("$where_field<=$custom_to");
			}
			else{
				if($cf->disp_type==8){
					$converted_date_from=cftools::getFormatedDate($custom_from);
					$converted_date_to=cftools::getFormatedDate($custom_to);

					$query->where("STR_TO_DATE($where_field,'%Y-%m-%d') BETWEEN ".$db->quote($converted_date_from)." AND ".$db->quote($converted_date_to));
				}
				else {
					$query->where("$where_field BETWEEN $custom_from AND $custom_to");
				}
			}
			$query->where('virtuemart_custom_id='.$cf->custom_id);

			try{
    			$db->setQuery($query);
    			$product_ids=$db->loadColumn();
			}
			catch (RuntimeException $e){
			    JLog::add(
			        sprintf('Custom filters range error: %s',$e->getMessage()),
			        JLog::ERROR,
			        'customfilters'
			    );
			}

            //if there are ranges from previous searches, intersect to find the common
            if(!empty($this->found_product_ids['ranges'])){
                 $tmp_array=array();
                 $tmp_array[]=$this->found_product_ids['ranges'];
                 $tmp_array[]=$product_ids;
                 $this->found_product_ids['ranges']=$this->intersectProductIds($tmp_array);
            }else $this->found_product_ids['ranges']=$product_ids;
			$jinput->set('found_product_ids',$this->found_product_ids);

		return $this->found_product_ids['ranges'];
	}

	/**
	 * Returns the product ids based only on the price filter
	 * These ids can be used both by the component and the module
	 * in the component's filtering and the module's get active functionalities accordingly
	 *
	 * @author Sakis Terz
	 * @since  1.4.0
	 * @return array
	 */
	protected function getProductIdsByPrice()
	{
		$japplication=JFactory::getApplication();
		$jinput=$japplication->input;

		if(isset($this->cfinputs['price'][0]))$price_from=$this->cfinputs['price'][0];
		if(isset($this->cfinputs['price'][1]))$price_to=$this->cfinputs['price'][1];

		if(empty($price_from) && empty($price_to))return false;


		if(!isset($this->found_product_ids['price'])){
			$where=array();
			$where_or=array();

			$join_product_categories=false;
			$join_product_manufacturers=false;

			//create a currency object which will be used later
			if(!class_exists('CurrencyDisplay'))require_once(JPATH_VM_ADMIN.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'currencydisplay.php');
			$this->vmCurrencyHelper=CurrencyDisplay::getInstance();

			//having them activated the query is faster. So they are activated by default
			//prices per shopper
			$prices_per_shopper=true;
			//prices per quantities
			$prices_per_quantities=true;

			/*Multiple Currencies*/
			$multiple_cur=$this->componentparams->get('products_multiple_currencies',0);
			if($multiple_cur){

				/* Check if the currencies are stored in the session from previous search
				 * Otherwise retreive them running a db query
				 */
				$session = JFactory::getSession();
				$used_currencies=$session->get('cf_product_currencies',array());
				//$used_currencies[]=144;
				if(empty($used_currencies))$used_currencies=cftools::getProductCurrencies();
			}


			/* Get the vendor's currency and the site's currency*/
			$vendor_currency=cftools::getVendorCurrency();
			$vendor_currency_details=cftools::getCurrencyInfo($vendor_currency['vendor_currency']);
			$virtuemart_currency_id=$jinput->get('virtuemart_currency_id',$vendor_currency['vendor_currency'],'int');
			$currency_id=$japplication->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',$virtuemart_currency_id);

			/* Calc. Rules */
			$calc_rules=cftools::getCalcRules();
			$ruleGroupsPerSelection=cftools::createCalcRuleGroups($calc_rules);
			/* Create the SQL queries*/

			/* If there is only 1 currency to the product prices*/
			if(empty($used_currencies) || (count($used_currencies)==1 && $used_currencies[0]==$vendor_currency['vendor_currency'])){
				//first convert it to vendor's currency and substract the tax
				if(!empty($price_from)){
					$price_from_converted=$this->vmCurrencyHelper->convertCurrencyTo($currency_id,$price_from);
					$price_from_converted = str_replace(',', '.', $price_from_converted);
					$ruleGroupsPerSelection=$this->subtractCalcRules($price_from_converted, $ruleGroupsPerSelection, $key='price_from');
				}
				if(!empty($price_to)){
					$price_to_converted=$this->vmCurrencyHelper->convertCurrencyTo($currency_id,$price_to);
					$ruleGroupsPerSelection=$this->subtractCalcRules($price_to_converted, $ruleGroupsPerSelection, $key='price_to');
					$price_to_converted = str_replace(',', '.', $price_to_converted);
				}

				//remove the global from the keys as i am using the category ids later in the query;
				$categories_of_rules=array_keys($ruleGroupsPerSelection);
				$global_index=array_search('global', $categories_of_rules);
				if($global_index!==false)unset($categories_of_rules[$global_index]);

				//print_r($ruleGroupsPerSelection);
				foreach ($ruleGroupsPerSelection as $key=>$calc_group){
					$where_cat='';
					$where_manuf='';

					if(!empty($price_from_converted)){
						$price_from_calcRules_converted=round($calc_group['price_from'],$vendor_currency_details->currency_decimal_place);
						$price_from_calcRules_converted = str_replace(',', '.', $price_from_calcRules_converted);
					}
					if(!empty($price_to_converted)){
						$price_to_calcRules_converted=round($calc_group['price_to'],$vendor_currency_details->currency_decimal_place);
						$price_to_calcRules_converted = str_replace(',', '.', $price_to_calcRules_converted);
					}

					//only when the selected categories are more than the cal rule categories. Otherwise it can be used for all the returned products
					if(!empty($key) && $key!='global'){
						if(strpos($key, ',')!==false){
							$categries_query='pc.virtuemart_category_id IN('.implode(',', $calc->categories).')';
						}else $categries_query='pc.virtuemart_category_id='.$key;

						$join_product_categories=true;
						$where_cat='AND '.$categries_query;
					}

					//global query should not have the categories which have rules
					else if(!empty($categories_of_rules)){
						$categries_query='pc.virtuemart_category_id NOT IN('.implode(',', $categories_of_rules).')';
						$join_product_categories=true;
						$where_cat='AND '.$categries_query;
					}

					if(!empty($price_from) && empty($price_to)){
						$where_or[]=" (((pp.`product_price` >=$price_from_calcRules_converted AND (ISNULL(pp.override) OR pp.override=0)) OR (pp.`product_override_price` >=$price_from_converted AND pp.override=1) OR (pp.`product_override_price` >= $price_from_calcRules_converted AND pp.override=-1)) $where_cat $where_manuf)";
					}
					else if(!empty($price_from) && !empty($price_to) && $price_from<=$price_to){
						$where_or[]=" ((
						(pp.`product_price` BETWEEN $price_from_calcRules_converted AND $price_to_calcRules_converted AND (ISNULL(pp.override) OR pp.override=0))
						OR (pp.`product_override_price` BETWEEN $price_from_converted AND $price_to_converted AND pp.override=1)
						OR (pp.`product_override_price` BETWEEN $price_from_calcRules_converted AND $price_to_calcRules_converted AND pp.override=-1)) $where_cat $where_manuf )";
					}
					else if(!empty($price_to) && empty($price_from)){
						$where_or[]=" (((pp.`product_price` <= $price_to_calcRules_converted AND (ISNULL(pp.override) OR pp.override=0)) OR (pp.`product_override_price` <= $price_to_converted AND pp.override=1) OR (pp.`product_override_price` <= $price_to_calcRules_converted AND pp.override=-1)) $where_cat $where_manuf)";
					}
				}
				//only for the vendor's currency
				$where[]="pp.product_currency=".$vendor_currency['vendor_currency'];
			}

			//multiple currencies in product prices
			else if(!empty($used_currencies)){

				if(!empty($price_from)){
					$price_from=$this->vmCurrencyHelper->convertCurrencyTo($currency_id,$price_from);
					$ruleGroupsPerSelection=$this->subtractCalcRules($price_from,$ruleGroupsPerSelection,$key='price_from');
				}
				if(!empty($price_to)){
					$price_to=$this->vmCurrencyHelper->convertCurrencyTo($currency_id,$price_to);
					$ruleGroupsPerSelection=$this->subtractCalcRules($price_to,$ruleGroupsPerSelection, $key='price_to');
				}

				//remove the global from the keys as i am using the category ids later in the query;
				$categories_of_rules=array_keys($ruleGroupsPerSelection);
				$global_index=array_search('global', $categories_of_rules);
				if($global_index!==false)unset($categories_of_rules[$global_index]);


				foreach ($used_currencies as $cur){

					foreach ($ruleGroupsPerSelection as $key=>$calc_group){
						$where_cat='';
						$where_manuf='';

						//convert the entered price in all the available currencies
						$cur_code=cftools::getCurrencyCode($cur);
						if(!empty($price_from)){
							$price_from_converted=round($this->vmCurrencyHelper->convertCurrencyTo($cur,$price_from,$shop=false),$vendor_currency_details->currency_decimal_place);
							if(!empty($calc_group['price_from']) && $calc_group['price_from']!=$price_from_converted)$price_from_calcRules_converted=round($this->vmCurrencyHelper->convertCurrencyTo($cur,$calc_group['price_from'],$shop=false),$vendor_currency_details->currency_decimal_place);
							else $price_from_calcRules_converted=$price_from_converted;
						}
						if(!empty($price_to)){
							$price_to_converted=round($this->vmCurrencyHelper->convertCurrencyTo($cur,$price_to,$shop=false),$vendor_currency_details->currency_decimal_place);
							if(!empty($calc_group['price_to']) && $calc_group['price_to']!=$price_to_converted)$price_to_calcRules_converted=round($this->vmCurrencyHelper->convertCurrencyTo($cur,$calc_group['price_to'],$shop=false),$vendor_currency_details->currency_decimal_place);
							else $price_to_calcRules_converted=$price_to_converted;
						}

						//only when the selected categories are more than the cal rule categories. Otherwise it can be used for all the returned products
						if(!empty($key) && $key!='global'){
							if(strpos($key, ',')!==false){
								$categries_query='pc.virtuemart_category_id IN('.implode(',', $calc->categories).')';
							}else $categries_query='pc.virtuemart_category_id='.$key;

							$join_product_categories=true;
							$where_cat='AND '.$categries_query;
						}

						//global query should not have the categories which have rules
						else if(!empty($categories_of_rules)){
							$categries_query='pc.virtuemart_category_id NOT IN('.implode(',', $categories_of_rules).')';
							$join_product_categories=true;
							$where_cat='AND '.$categries_query;
						}

						if(!empty($price_from) && empty($price_to)){
							$where_or[]=" ((
							(pp.`product_price` >=$price_from_calcRules_converted AND (ISNULL(pp.override) OR pp.override=0))
							OR (pp.`product_override_price` >=$price_from_converted AND pp.override=1)
							OR (pp.`product_override_price` >=$price_from_calcRules_converted AND pp.override=-1))
							AND pp.product_currency=$cur  $where_cat)";
						}
						else if(!empty($price_from) && !empty($price_to)){
							$where_or[]=" ((
							(pp.`product_price` BETWEEN $price_from_calcRules_converted AND $price_to_calcRules_converted AND (ISNULL(pp.override) OR pp.override=0))
							OR (pp.`product_override_price` BETWEEN $price_from_converted AND $price_to_converted AND pp.override=1)
							OR (pp.`product_override_price` BETWEEN $price_from_calcRules_converted AND $price_to_calcRules_converted AND pp.override=-1))
							AND pp.product_currency=$cur $where_cat)";
						}
						if(!empty($price_to) && empty($price_from)){
							$where_or[]=" ((
							(pp.`product_price` <=$price_to_calcRules_converted AND (ISNULL(pp.override) OR pp.override=0))
							OR (pp.`product_override_price` <=$price_to_converted AND pp.override=1)
							OR (pp.`product_override_price` <=$price_to_calcRules_converted AND pp.override=-1))
							AND pp.product_currency=$cur $where_cat)";
						}
					}
				}

			}

			$db=JFactory::getDbo();
			$query=$db->getQuery(true);
			$query->select('pp.virtuemart_product_id');
			$query->from('#__virtuemart_product_prices AS pp');
			if($join_product_categories)$query->leftJoin('#__virtuemart_product_categories AS pc ON pp.virtuemart_product_id=pc.virtuemart_product_id');
			if($join_product_manufacturers)$query->leftJoin('#__virtuemart_manufacturers AS pm ON pp.virtuemart_product_id=pm.virtuemart_product_id');

			//prices per shopper
			if($prices_per_shopper){
				$usermodel = VmModel::getModel ('user');
				$currentVMuser = $usermodel->getUser();
				$virtuemart_shoppergroup_ids = cftools::getUserShopperGroups();
				if($currentVMuser->virtuemart_user_id>0){
					ArrayHelper::toInteger($virtuemart_shoppergroup_ids);
					if(!empty($virtuemart_shoppergroup_ids)  && is_array($virtuemart_shoppergroup_ids))
					$whereShopper = 'pp.`virtuemart_shoppergroup_id` IN('.implode(',', $virtuemart_shoppergroup_ids).')';
					//prices for all shopppers
					$whereShopper.= ' OR (ISNULL(pp.`virtuemart_shoppergroup_id`) OR pp.`virtuemart_shoppergroup_id`=0)';
					$whereShopper='('.$whereShopper.')';
				}

				//no shopper
				else $whereShopper= '(ISNULL(pp.`virtuemart_shoppergroup_id`) OR pp.`virtuemart_shoppergroup_id`=0)';
				$where[]=$whereShopper;
			}

			//prices per quantity ranges, its should always refer to 1 quantity i.e. 0 or 1
			if($prices_per_quantities){
				$where[] .='(pp.`price_quantity_start`=0 OR pp.`price_quantity_start`=1 OR ISNULL(pp.`price_quantity_start`))';
			}
			if(!empty($where_or))$where[]='('.implode(' OR ', $where_or).')';

			if(!empty($where)){
				$where_str=implode(' AND ',$where);
				$query->where($where_str);
			}

			//fetch results
			try{
    			$db->setQuery($query);
    			$q=(string)$query;
    			$result=$db->loadColumn();
			}
			catch (RuntimeException $e){
			    JLog::add(
			        sprintf('Price range query error: %s',$e->getMessage()),
			        JLog::ERROR,
			        'customfilters'
			    );
			}

			$this->found_product_ids['price']=$result;

			//set the product ids in the jinput so it will be used by the module for the getActiveOptions Functionalitity
			$jinput->set('found_product_ids',$this->found_product_ids);
		}
		return $this->found_product_ids['price'];
	}

	/**
	 * Find the base price by substracting a set of calc rules
	 * The execution order is the following "Marge","DBTax","Tax","VatTax","DATax". in our case it should be reversed
	 *
	 * @param  float $price
	 * @param  array $calc_groups
	 * @return array
	 */
	public function subtractCalcRules($price, $calc_groups, $price_key='price' )
	{

		if(isset($calc_groups['global'])){
			$global_rule=$calc_groups['global'];
		}

		foreach($calc_groups as $key=>&$calc_gr){
			$group_price=$price;

			if(isset($calc_gr['DATax']))$group_price=$this->subtractCalcRulesByCalcType($group_price,$calc_gr['DATax']);
			if(isset($global_rule['DATax']) && $key!='global')$group_price=$this->subtractCalcRulesByCalcType($group_price,$global_rule['DATax']);

			if(isset($calc_gr['VatTax']))$group_price=$this->subtractCalcRulesByCalcType($group_price,$calc_gr['VatTax']);
			if(isset($global_rule['VatTax']) && $key!='global')$group_price=$this->subtractCalcRulesByCalcType($group_price,$global_rule['VatTax']);

			if(isset($calc_gr['Tax']))$group_price=$this->subtractCalcRulesByCalcType($group_price,$calc_gr['Tax']);
			if(isset($global_rule['Tax']) && $key!='global')$group_price=$this->subtractCalcRulesByCalcType($group_price,$global_rule['Tax']);

			if(isset($calc_gr['DBTax']))$group_price=$this->subtractCalcRulesByCalcType($group_price,$calc_gr['DBTax']);
			if(isset($global_rule['DBTax']) && $key!='global')$group_price=$this->subtractCalcRulesByCalcType($group_price,$global_rule['DBTax']);

			if(isset($calc_gr['Marge']))$group_price=$this->subtractCalcRulesByCalcType($group_price,$calc_gr['Marge']);
			if(isset($global_rule['Marge']) && $key!='global')$group_price=$this->subtractCalcRulesByCalcType($group_price,$global_rule['Marge']);

			$calc_gr[$price_key]=$group_price;
		}
		return $calc_groups;
	}

	/**
	 * Gets a group of cacl rules and subtract them from the price
	 *
	 * @param  float $price
	 * @param  object $calc_group
	 *
	 * @since  1.9.5
	 * @return float $price
	 */
	public function subtractCalcRulesByCalcType($price, $calc_group)
	{
		foreach($calc_group as $calc){
			$price=$this->subtractCalcRule($price,$calc);
		}

		return $price;
	}

	/**
	 * Substract calculation rules from the price to get the base price
	 *
	 * @param  float $price - The price from which we will subtract the calc rule
	 * @param  objecr $calc - The calc rule object
	 *
	 * @return float   $price  - The price by subtracting the global calculation rules
	 */
	public function subtractCalcRule($price, $calc )
	{
		$value=$calc->calc_value;
		$mathop=$calc->calc_value_mathop;
		$currency=$calc->calc_currency;

		if($value!=0){
			$coreMathOp = array('+','-','+%','-%');
			if(in_array($mathop,$coreMathOp)){
				$sign = substr($mathop, 0, 1);
			}
			if (strlen($mathop) == 2) {
				$cmd = substr($mathop, 1, 2);

				//revert
				if ($cmd == '%') {
					$calculated = $price /(1 +  (100.0 / $value));
				}
			} else if (strlen($mathop) == 1){

				//then its a price and needs to be in the correct currency
				$calculated = $this->vmCurrencyHelper->convertCurrencyTo($currency, $value);
			}

			if($sign=='+'){
				return $price-=$calculated;
			}else if($sign=='-'){
				return $price+=$calculated;
			}else return $price;
		}else return $price;
	}

	/**
	 * Get the Order By Select List
	 * Overrides the function originaly written by Kohl Patrick (Virtuemart parent class)
	 *
	 * @author 	Sakis Terz
	 * @access	public
	 * @param 	int	    The category id
	 *
	 * @return 	array	the orderBy HTML List and the manufacturers list
	 **/
	public function getOrderByList($virtuemart_category_id=false)
	{
	    if ($this->_pagination == null) {
	        require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'cfpagination.php';

	        $limit = $this->getState('list.limit');
	        $limitstart=$this->getState('list.limitstart',0);
	        $this->_pagination = new cfPagination($this->total , $limitstart, $limit );
	    }

	    return $this->_pagination->getOrderByList($virtuemart_category_id=false, $default_order_by=$this->filter_order, $order_by=$this->getState('filter_order'), $order_dir=$this->getState('filter_order_Dir'));
	}

	/**
	 * Loads the pagination
	 *
	 * @author 	   Sakis Terz
	 * @since	 1.0
	 * @return   cfPagination object
	 */
	public function getPagination($total=0,$limitStart=0,$limit=0)
	{
		if ($this->_pagination == null) {
			require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'cfpagination.php';

			$limit = $this->getState('list.limit');
			$limitstart=$this->getState('list.limitstart',0);
			$this->_pagination = new cfPagination($this->total , $limitstart, $limit );
		}
		return $this->_pagination;
	}
}
