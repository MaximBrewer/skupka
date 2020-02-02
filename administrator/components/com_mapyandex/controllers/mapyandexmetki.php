<?php
/*
 * @package Joomla 1.5
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.controller' );

class MapYandexControllerMapYandexMetki extends MapYandexController
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


	

	function add()
	{

		JRequest::setVar( 'view', 'mapyandexmetki' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}
	
	function edit()
	{

		JRequest::setVar( 'view', 'mapyandexmetki' );
		JRequest::setVar( 'layout', 'formedit'  );
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}
	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save()
	{
	
		$model = $this->getModel('mapyandexmetki');
		$data = JRequest::get( 'post' );
		if ($model->store($post)) {
			if(!$data['editmarket']) {
				$msg = JText::_( 'COM_MAPYANDEX_SUCCESSMARKET' );
			} else {
				$msg = JText::_( 'COM_MAPYANDEX_SUCCESSMARKETEDIT' );
			}

		} else {
			if(!$data['editmarket']) {
				$msg = JText::_( 'COM_MAPYANDEX_ERRORADDNEWMARKET' );
			} else {
				$msg = JText::_( 'COM_MAPYANDEX_ERRORMARKETEDIT' );
			}
			
	
		}
			$link = 'index.php?option=com_mapyandex&view=mapyandexmetki';
			$this->setRedirect($link, $msg);
		// Check the table in so it can be edited.... we are done with it anyway

	}

	function apply()
	{
	
		$model = $this->getModel('mapyandexmetki');
		
		$data = JRequest::get( 'post' );
		if ($model->store($post)) {
			if(!$data['editmarket']) {
				$msg = JText::_( 'COM_MAPYANDEX_SUCCESSMARKET' );
			} else {
				$msg = JText::_( 'COM_MAPYANDEX_SUCCESSMARKETEDIT' );
			}

		} else {
			if(!$data['editmarket']) {
				$msg = JText::_( 'COM_MAPYANDEX_ERRORADDNEWMARKET' );
			} else {
				$msg = JText::_( 'COM_MAPYANDEX_ERRORMARKETEDIT' );
			}
			
	
		}
		$id = JRequest::getVar( 'id', '', 'post','string', JREQUEST_ALLOWRAW );
	
		$link = 'index.php?option=com_mapyandex&view=mapyandexmetki&layout=formedit&id='. $id;
		$this->setRedirect($link, $msg);

	}
	/**
	 * remove record(s)
	 * @return void
	 */
	function remove()
	{
		$post = JRequest::get( 'post' );
		$model = $this->getModel('mapyandexmetki');
		if ($model->delete($post)) {
			$msg = JText::_( 'COM_MAPYANDEX_SUCCESSMARKET' );
		} else {
			$msg = JText::_( 'COM_MAPYANDEX_ERRORADDNEWMARKET' );
		}

		$this->setRedirect( 'index.php?option=com_mapyandex&view=mapyandexmetki', $msg );
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		$msg = JText::_( 'COM_MAPYANDEX_CANCEL' );
		$this->setRedirect( 'index.php?option=com_mapyandex&view=mapyandexmetki', $msg );
	}
	

}
