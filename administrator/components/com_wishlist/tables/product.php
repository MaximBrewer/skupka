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
class TableProduct extends JTable{
	/** jcb code */
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $product_id = null;
	/**
	 *
	 * @var int
	 */
	var $vendor_id = 0;
	/**
	 *
	 * @var int
	 */
	var $product_parent_id = 0;
	/**
	 *
	 * @var string
	 */
	var $product_sku = null;
	/**
	 *
	 * @var string
	 */
	var $product_s_desc = null;
	/**
	 *
	 * @var string
	 */
	var $product_desc = null;
	/**
	 *
	 * @var string
	 */
	var $product_thumb_image = null;
	/**
	 *
	 * @var string
	 */
	var $product_full_image = null;
	/**
	 *
	 * @var string
	 */
	var $product_publish = null;
	/**
	 *
	 * @var float
	 */
	var $product_weight = null;
	/**
	 *
	 * @var string
	 */
	var $product_weight_uom = "pounds.";
	/**
	 *
	 * @var float
	 */
	var $product_length = null;
	/**
	 *
	 * @var float
	 */
	var $product_width = null;
	/**
	 *
	 * @var float
	 */
	var $product_height = null;
	/**
	 *
	 * @var string
	 */
	var $product_lwh_uom = "inches";
	/**
	 *
	 * @var string
	 */
	var $product_url = null;
	/**
	 *
	 * @var int
	 */
	var $product_in_stock = 0;
	/**
	 *
	 * @var int
	 */
	var $product_available_date = null;
	/**
	 *
	 * @var string
	 */
	var $product_availability = null;
	/**
	 *
	 * @var string
	 */
	var $product_special = null;
	/**
	 *
	 * @var int
	 */
	var $product_discount_id = null;
	/**
	 *
	 * @var int
	 */
	var $ship_code_id = null;
	/**
	 *
	 * @var int
	 */
	var $cdate = null;
	/**
	 *
	 * @var int
	 */
	var $mdate = null;
	/**
	 *
	 * @var string
	 */
	var $product_name = null;
	/**
	 *
	 * @var int
	 */
	var $product_sales = 0;
	/**
	 *
	 * @var string
	 */
	var $attribute = null;
	/**
	 *
	 * @var string
	 */
	var $custom_attribute = null;
	/**
	 *
	 * @var int
	 */
	var $product_tax_id = 0;
	/**
	 *
	 * @var string
	 */
	var $product_unit = null;
	/**
	 *
	 * @var int
	 */
	var $product_packaging = null;
	/**
	 *
	 * @var string
	 */
	var $child_options = null;
	/**
	 *
	 * @var string
	 */
	var $quantity_options = null;
	/**
	 *
	 * @var string
	 */
	var $child_option_ids = null;
	/**
	 *
	 * @var string
	 */
	var $product_order_levels = null;
	/** jcb code */

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableProduct(& $db){
		parent::__construct('#__virtuemart_products', 'product_id', $db);
	}
	
	function check(){
		// write here data validation code
		return parent::check();
	}
}