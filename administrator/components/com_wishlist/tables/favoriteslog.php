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

/**
 * Favorites Table
 *
 * @package    Joomla.Components
 * @subpackage 	Favorites
 */
class TableFavoriteslog extends JTable{
	/** jcb code */
	/**
	 *
	 * @var datetime
	 */
	var $dt_stamp = null;
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $log_id = null;
	/**
	 *
	 * @var string
	 */
	var $log_type = null;
	/**
	 *
	 * @var int
	 */
	var $user_id = null;
	/**
	 *
	 * @var int
	 */
	var $cust_id = null;
	/**
	 *
	 * @var int
	 */
	var $product_id = null;
	/**
	 *
	 * @var int
	 */
	var $product_qty = null;
	/** jcb code */

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableFavoriteslog(& $db){
		parent::__construct('#__virtuemart_favorites_log', 'log_id', $db);
	}
	
	function check(){
		// write here data validation code
		return parent::check();
	}
}