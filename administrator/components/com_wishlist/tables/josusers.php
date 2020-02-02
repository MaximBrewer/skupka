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
class TableJosusers extends JTable{
	/** jcb code */
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;
	/**
	 *
	 * @var string
	 */
	var $name = null;
	/**
	 *
	 * @var string
	 */
	var $username = null;
	/**
	 *
	 * @var string
	 */
	var $email = null;
	/**
	 *
	 * @var string
	 */
	var $password = null;
	/**
	 *
	 * @var string
	 */
	var $usertype = null;
	/**
	 *
	 * @var int
	 */
	var $block = 0;
	/**
	 *
	 * @var int
	 */
	var $sendEmail = 0;
	/**
	 *
	 * @var int
	 */
	var $gid = 1;
	/**
	 *
	 * @var datetime
	 */
	var $registerDate = "0000-00-00 00:00:00";
	/**
	 *
	 * @var datetime
	 */
	var $lastvisitDate = "0000-00-00 00:00:00";
	/**
	 *
	 * @var string
	 */
	var $activation = null;
	/**
	 *
	 * @var string
	 */
	var $params = null;
	/** jcb code */

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableJosusers(& $db){
		parent::__construct('#__users', 'id', $db);
	}
	
	function check(){
		// write here data validation code
		return parent::check();
	}
}