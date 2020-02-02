<?php

/**
 * Favorites Model for Favorites Component
 * 
 * @package    Favorites & Wishlist
 * @subpackage com_wishlist
 * @license  GNU/GPL v2
 * @Copyright (C) 2013 2KWeb Solutions. All rights reserved.
 * This program is distributed under the terms of the GNU General Public License
 *
 */


// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.modellist' );
/**
 * Favorites Model
 *
 * @package    Joomla.Components
 * @subpackage 	Favorites
 */
class FavoritesModelFavoriteslist extends JModelList{
	/**
	 * Favoriteslist data array
	 *
	 * @var array
	 */
	private $_data;

	/**
	* Pagination object
	* @var object
	*/
	private $_pagination = null;

	/*
	 * Constructor
	 *
	 */
	function __construct(){
		parent::__construct();

		$app =& JFactory::getApplication();

        // Get pagination request variables
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
 
        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
 
        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);

	}

	/**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
	private function _buildQuery(){
		// getting the language tag for virtuemart_products table
		$siteLang = JFactory::getLanguage()->getTag();
		$lang = strtolower(strtr($siteLang,'-','_'));
		
		// use alias fav for easier JOINs writing
		$query = 'SELECT fav.fav_id, fav.product_id, prod_lng.product_name, fav.product_qty, fav.user_id, usr.name, fav.fav_date, fav.isGuest FROM `#__virtuemart_favorites` fav join `#__virtuemart_products` prod on fav.product_id = prod.virtuemart_product_id left join `#__virtuemart_products_'.$lang.'` prod_lng on fav.product_id = prod_lng.virtuemart_product_id left join `#__users` usr on fav.user_id = usr.id '. $this->_buildQueryWhere() ."HAVING isGuest = 0". $this->_buildQueryOrderBy();
		return $query;
	}

	/**
	 * Returns the 'order by' part of the query
	 * @return string the order by''  part of the query
	 */
	private function _buildQueryOrderBy() {
	    $app =& JFactory::getApplication();

		// default field for records list
		$default_order_field = 'fav_id'; 
		// Array of allowable order fields
	    $allowedOrders = explode(',', 'fav_id,product_id,product_name,product_qty,user_id,name,fav_date'); // array('id', 'ordering', 'published'); 

		// retrive ordering info
		$filter_order = $app->getUserStateFromRequest('com_wishlistfilter_order', 'filter_order', $default_order_field);
		$filter_order_Dir = strtoupper($app->getUserStateFromRequest('com_wishlistfilter_order_Dir', 'filter_order_Dir', 'ASC'));

	    // validate the order direction, must be ASC or DESC
	    if ($filter_order_Dir != 'ASC' && $filter_order_Dir != 'DESC') {
			$filter_order_Dir = 'ASC';
	    }

	    // if order column is unknown use the default
	    if ((isSet($allowedOrders)) && !in_array($filter_order, $allowedOrders)){
			$filter_order = $default_order_field;
	    }
		// comment out if use switch
		//$prefix = 'fav'; 
		
		// strip comment if you use JOIN in select statement
		switch ($filter_order){
			case 'product_name':
				$prefix = 'prod_lng';
				break;
			case 'name':
				$prefix = 'usr';
				break;
			default:
				$prefix = 'fav';
				break;
		}

	    // return the ORDER BY clause        
	    return " ORDER BY {$prefix}.`{$filter_order}` {$filter_order_Dir}";
	}	
	private function _buildQueryWhere() {
	    $app =& JFactory::getApplication();
	
		$search = $app->getUserStateFromRequest('com_wishlistsearch', 'search', '');
		
	    if (!$search) return '';
		
		$allowedSearch = explode(',','product_name,user_id,name,fav_date'); // array('id', 'ordering', 'published'); 
		$where = ' WHERE (0=1) ';
		foreach($allowedSearch as $field){
			if (!$field) return '';
			$where .= " OR (`$field` LIKE '%" . addSlashes($search) . "%') ";
		}
	    return $where;
	}
	
	
	/**
	 * Retrieves the data
	 * @return array Array of objects containing the data from the database
	 */
	public function getData(){
		// Lets load the data if it doesn't already exist
		if (empty( $this->_data ))		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_data;
	}

	/**
	 * Gets the number of published records
	 * @return int
	 */
	public function getTotal(){
		// getting the language tag for virtuemart_products table
		$siteLang = JFactory::getLanguage()->getTag();
		$lang = strtolower(strtr($siteLang,'-','_'));
		
		$db =& JFactory::getDBO();
		$db->setQuery('SELECT COUNT(*), fav.fav_id, fav.product_id, prod_lng.product_name, fav.product_qty, fav.user_id, usr.name, fav.fav_date, fav.isGuest FROM `#__virtuemart_favorites` fav join `#__virtuemart_products` prod on fav.product_id = prod.virtuemart_product_id left join `#__virtuemart_products_'.$lang.'` prod_lng on fav.product_id = prod_lng.virtuemart_product_id left join `#__users` usr on fav.user_id = usr.id '. $this->_buildQueryWhere() ."HAVING isGuest = 0");
		return $db->loadResult();
	}
	
	/**
	 * Gets the Pagination Object
	 * @return object JPagination
	 */
	public function getPagination(){
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}
	
	/**
	 * Methods to get records data for specific fields
	 * use returned recorset to populate view in specific
	 * select to manage related tables
	 * @return object list with options array
	 */
	
	public function &getGenericFieldName($fieldName){
		$db =& JFactory::getDBO();
		$db->setQuery( 'SELECT fav_id AS value `$fieldName` AS text FROM `#__virtuemart_favorites` ORDER BY `$fieldName`');
		$options = array();
		foreach( $db->loadObjectList() as $r){
			$options[] = JHTML::_('select.option',  $r->value, $r->text );
        }
		return $options;

	}
	
	

	
}