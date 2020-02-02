<?php
/**
 * Customfilter table
 *
 * @package		Customfilters
 * @since		1.5
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * 
 * Table class
 * @author Sakis Terz
 *
 */
class customfiltersTableCustomfilter extends JTable{
/**
	 * Constructor
	 *
	 * @since	1.5
	 */
	function __construct(&$_db)
	{
		parent::__construct('#__cf_customfields', 'id', $_db);
	}
}