<?php
/**
 * Favorites Controller for Favorites Component
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

JHTML::stylesheet("template.css", "administrator/components/com_wishlist/");

/**
 * Favorites Model
 *
 * @package    Joomla.Components
 * @subpackage 	Favorites
 */
class FavoritesControllerFavoriteslog extends FavoritesController{


	/**
	 * Parameters in config.xml.
	 *
	 * @var	object
	 * @access	protected
	 */
	private $_params = null;

	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct(){
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
		
		// Set reference to parameters
		$this->_params = &JComponentHelper::getParams( 'com_wishlist' );
		//$dummy = $this->_params->get('parm_text');

	}

	/**
	 * display the edit form
	 * @return void
	 */
	public function edit(){
		JRequest::setVar( 'view', 'favoriteslog' );
		JRequest::setVar( 'layout', 'default'  );
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	public function save(){
		$model = $this->getModel('favoriteslog'); 

		if ($model->store()) {
			$msg = JText::_( 'DATA_SAVED' );
		} else {
			$msg = JText::_( 'ERROR_SAVING_DATA' );
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$link = 'index.php?option=com_wishlist&controller=favoritesloglist';
		$this->setRedirect($link, $msg);
	}

	/**
	 * remove record(s)
	 * @return void
	 */
	public function remove(){
		$model = $this->getModel('favoriteslog'); //
		if(!$model->delete()) {
			$msg = JText::_( 'ERROR_ONE_OR_MORE_DATA_COULD_NOT_BE_DELETED' );
		} else {
			$msg = JText::_( 'DATAS_DELETED' );
		}

		$this->setRedirect( 'index.php?option=com_wishlist&controller=favoritesloglist', $msg );
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	public function cancel(){
		$msg = JText::_( 'OPERATION_CANCELLED' );
		$this->setRedirect( 'index.php?option=com_wishlist&controller=favoritesloglist', $msg );
	}
}