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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.modellist' );

/**
 * Favorites Model
 *
 * @package    Joomla.Components
 * @subpackage 	Favorites
 */
class FavoritesModelFavoritesshlist extends JModelList{
	
	/**
	 * Favoritesshlist data array for tmp store
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
	 * Gets the data
	 * @return mixed The data to be displayed to the user
	 */
	public function getData(){
		// Lets load the data if it doesn't already exist
		if (empty( $this->_data )){
			$recordSet =& $this->getTable('favoritessh');
			$db =& JFactory::getDBO();
			$query = 'SELECT sh.shared_id, sh.user_id, sh.share_date, sh.share_title, sh.isWishList, u.name, u.id FROM `#__virtuemart_favorites_sh` sh, `#__users` u WHERE ' . (isset($recordSet->published)?'`published`':'1') . ' = 1 AND sh.user_id = u.id AND sh.share_date > "1900-01-01" ORDER BY sh.share_date DESC ';
			$this->_data = $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_data;
	}

	/**
	 * Gets the number of published records
	 * @return int
	 */
	public function getTotal(){
		$db =& JFactory::getDBO();
		$recordSet =& $this->getTable('favoritessh');
		$db->setQuery( 'SELECT COUNT(*) FROM `#__virtuemart_favorites_sh` WHERE ' . (isset($recordSet->published)?'`published`':'1') . ' = 1 AND share_date > "1900-01-01"' );
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
	
	
}
