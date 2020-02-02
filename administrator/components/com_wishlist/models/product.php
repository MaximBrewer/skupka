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
class FavoritesModelProduct extends JModelList{
	/**
	 * Constructor that retrieves the ID from the request
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct(){
		parent::__construct();

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}

	/**
	 * Method to set the identifier for the record
	 *
	 * @access	public
	 * @param	int primary key identifier
	 * @return	void
	 */
	public function setId($id){
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}

	/**
	 * Method to get a record
	 * @return object with data
	 */
	public function &getData(){
		// Load the data
		if (empty( $this->_data )) {
			$query = 'SELECT * FROM `#__virtuemart_products` WHERE `product_id` = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			$this->_data =& $this->getTable();
		}
		return $this->_data;
	}

	/**
	 * Method to store a record
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	public function store(){	
		$row =& $this->getTable();

		$data = JRequest::get( 'post' );
		// HTML content must be required!
		//$data['my_html_field'] = JRequest::getVar( 'my_html_field', '', 'post', 'string', JREQUEST_ALLOWHTML );
		
// mcm code 
		$data['product_id'] = JRequest::getVar('id', '', 'post', 'int');
		$data['vendor_id'] = JRequest::getVar('vendor_id', '', 'post', 'int');
		$data['product_parent_id'] = JRequest::getVar('product_parent_id', '', 'post', 'int');
		$data['product_desc'] = JRequest::getVar('product_desc', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$data['product_weight'] = JRequest::getVar('product_weight', '', 'post', 'double');
		$data['product_length'] = JRequest::getVar('product_length', '', 'post', 'double');
		$data['product_width'] = JRequest::getVar('product_width', '', 'post', 'double');
		$data['product_height'] = JRequest::getVar('product_height', '', 'post', 'double');
		$data['product_in_stock'] = JRequest::getVar('product_in_stock', '', 'post', 'int');
		$data['product_available_date'] = JRequest::getVar('product_available_date', '', 'post', 'int');
		$data['product_discount_id'] = JRequest::getVar('product_discount_id', '', 'post', 'int');
		$data['ship_code_id'] = JRequest::getVar('ship_code_id', '', 'post', 'int');
		$data['cdate'] = JRequest::getVar('cdate', '', 'post', 'int');
		$data['mdate'] = JRequest::getVar('mdate', '', 'post', 'int');
		$data['product_sales'] = JRequest::getVar('product_sales', '', 'post', 'int');
		$data['attribute'] = JRequest::getVar('attribute', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$data['custom_attribute'] = JRequest::getVar('custom_attribute', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$data['product_tax_id'] = JRequest::getVar('product_tax_id', '', 'post', 'int');
		$data['product_packaging'] = JRequest::getVar('product_packaging', '', 'post', 'int');
// mcm code 


		// Bind the form fields to the table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Make sure the record is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError( $row->_db->getErrorMsg() );
			return false;
		}

		return true;
	}

	/**
	 * Method to delete record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	public function delete(){
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		$row =& $this->getTable();

		if (count( $cids )) {
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
			}
		}
		return true;
	}
	/**
	 * Method to move record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */			
	public function move($direction){
		$row =& $this->getTable();
		if (!$row->load($this->_id)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$row->move( $direction )) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
				
	/**
	 * Method to save the new order
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	public function saveorder($cid = array(), $order){
		$row =& $this->getTable();

		// update ordering values
		$n = count($cid);
		for( $i=0; $i < $n; $i++ )
		{
			$row->load( (int) $cid[$i] );

			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}

		return true;
	}			

	/**
	 * Methods to get options arrays for specific fields
	 * @return object with data
	 */
	
	public function &getGenericFieldName(){
		$options = array(
            JHTML::_('select.option',  'val1', 'text 1' ),
            JHTML::_('select.option',  'val2', 'text 2' )
        );    
		return $options;
	}
	
	

}