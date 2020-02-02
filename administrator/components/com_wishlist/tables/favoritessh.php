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
class TableFavoritessh extends JTable{
	/** jcb code */
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $shared_id = null;
	/**
	 *
	 * @var string
	 */
	var $user_id = null;
	/**
	 *
	 * @var datetime
	 */
	var $share_date = null;
	/**
	 *
	 * @var string
	 */
	var $share_title = null;
	/**
	 *
	 * @var string
	 */
	var $share_desc = null;
	/**
	 *
	 * @var int
	 */
	var $isWishList = 0;
	/** jcb code */

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableFavoritessh(& $db){
		parent::__construct('#__virtuemart_favorites_sh', 'shared_id', $db);
	}
	
	function check(){
		// write here data validation code
		return parent::check();
	}
}