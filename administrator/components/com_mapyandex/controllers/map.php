<?php
/*
 * @package Joomla 1.5
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controllerform');

class MapYandexControllerMap extends MapYandexController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		
	}

	/**
	 * display the edit form
	 * @return void
	 */
	function edit()
	{
		JRequest::setVar( 'view', 'map' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}
	

	function add()
	{
		JRequest::setVar( 'view', 'map' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}
	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save()
	{
	
		$model = $this->getModel('map');
		
		if ($model->store($post)) {
			$msg = JText::_( 'COM_MAPYANDEX_SUCCESS' );
		} else {
			$msg = JText::_( 'COM_MAPYANDEX_ERROR' );
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$link = 'index.php?option=com_mapyandex';
		$this->setRedirect($link, $msg);
	}

	function apply()
	{
	
		$model = $this->getModel('map');
		
		if ($model->store($post)) {
			$msg = JText::_( 'COM_MAPYANDEX_SUCCESS' );
		} else {
			$msg = JText::_( 'COM_MAPYANDEX_ERROR' );
		}
		$id = JRequest::getVar( 'id', '', 'post','string', JREQUEST_ALLOWRAW );
	
		$link = 'index.php?option=com_mapyandex&view=map&layout=form&id='. $id;
		$this->setRedirect($link, $msg);

	}
	/**
	 * remove record(s)
	 * @return void
	 */
	function remove()
	{
		$model = $this->getModel('map');
		if ($model->delete($post)) {
			$msg = JText::_( 'COM_MAPYANDEX_SUCCESS' );
		} else {
			$msg = JText::_( 'COM_MAPYANDEX_ERROR' );
		}

		$this->setRedirect( 'index.php?option=com_mapyandex', $msg );
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		$msg = JText::_( 'COM_MAPYANDEX_CANCEL' );
		$this->setRedirect( 'index.php?option=com_mapyandex', $msg );
	}
	
	function helloworld()
	{
		$msg = JText::_( 'COM_MAPYANDEX_CANCEL' );
		$this->setRedirect( 'index.php?option=com_mapyandex', $msg );
	}
}