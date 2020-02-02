<?php
/**
 * Favorites View for Favorites Component
 * 
 * @package    Favorites & Wishlist
 * @subpackage com_wishlist
 * @license  GNU/GPL v2
 * @Copyright (C) 2013 2KWeb Solutions. All rights reserved.
 * This program is distributed under the terms of the GNU General Public Licenser
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

/**
 * Favorites view
 *
 * @package    Joomla.Components
 * @subpackage 	Favorites
 */
class FavoritesViewFavorites extends JViewLegacy
{
	
	/**
	 * display method of Favorites view
	 * @return void
	 **/
	function display($tpl = null)
	{
		//get the data
		$data =& $this->get('Data');
		$isNew = ($data->fav_id == null);

		$text = $isNew ? JText::_( 'NEW' ) : JText::_( 'EDIT' );
		JToolBarHelper::title(   JText::_( 'FAVORITES' ).': <small>[ ' . $text.' ]</small>', 'wishlist_icon_main.png' );
		JToolBarHelper::save();
		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}

		$this->assignRef('data', $data);
		
		// create options for 'select' used in template
		$dataOptions = array();
		foreach(explode(',', '') as $field){
			if (!$field) continue;
			//options array are generated in the model...
			$dataOptions[$field] =& $this->get( ucfirst($field) );
		}
		
		/*
		// related table example 
		// thisTableFieldKey : foreign key (es #__content.catid -> 'catid')
		// relatedTableModelList : name used for table holding data (es #__content -> 'contentlist')
		// getRelatedTableFieldData : method for getting related table values for key (es #__categories.title -> 'getTitleFieldData()')
		
		$rmodel =& $this->getModel('relatedTableModelList'); 
		$dataOptions['thisTableFieldKey'] =& $rmodel->relatedTableGetField();
		*/

		
		$this->assignRef('dataOptions', $dataOptions);

		parent::display($tpl);
	}
	
	
	/**
	* Get list of available users
	* @return array
	**/
	public function getUserList()
    {
        // get a reference to the database
        $db = &JFactory::getDBO();
 
        // get a list of acrive users ordered by name 
        $query = 'SELECT usr.id, usr.name from `#__users` usr WHERE usr.block = 0 ORDER BY usr.name ASC';
 
        $db->setQuery($query);
        $userList = ($items = $db->loadObjectList())?$items:array();
 
        return $userList;
    } //end getUserList
	
	
	/**
	* Get list of available products
	* @return array
	**/
	public function getProductList()
    {
        // getting the language tag for virtuemart_products table
		$siteLang = JFactory::getLanguage()->getTag();
		$lang = strtolower(strtr($siteLang,'-','_'));
		
		// get a reference to the database
        $db = &JFactory::getDBO();
 
        // get a list of published products ordered by product name 
        $query = 'SELECT prod.virtuemart_product_id, prod_lng.product_name from `#__virtuemart_products` prod, `#__virtuemart_products_'.$lang.'` prod_lng WHERE prod.published = "1" AND prod.virtuemart_product_id = prod_lng.virtuemart_product_id ORDER BY prod_lng.product_name ASC';
 
        $db->setQuery($query);
        $ProductList = ($items = $db->loadObjectList())?$items:array();
 
        return $ProductList;
    } //end getProductList
}