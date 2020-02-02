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
class TableFavorites extends JTable{
	/** jcb code */
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $fav_id = null;
	/**
	 *
	 * @var int
	 */
	var $product_id = 0;
	/**
	 *
	 * @var int
	 */
	var $product_qty = -1;
	/**
	 *
	 * @var int
	 */
	var $user_id = null;
	/**
	 *
	 * @var datetime
	 */
	var $fav_date = null;
	/** jcb code */

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableFavorites(& $db){
		parent::__construct('#__virtuemart_favorites', 'fav_id', $db);
	}
	
	function check(){
		// write here data validation code
		return parent::check();
	}
}