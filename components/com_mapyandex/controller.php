<?php
/*
 * @package Joomla 3.0
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

/**
* MyExtension Controller
*
*/
class MapyandexController extends JControllerLegacy
{

	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function display($cachable = false, $urlparams = array())
	{
		$user = JFactory::getUser();

		parent::display();
	}

	public function ajaxit()
	{
		$model = $this->getModel('mapajax');
		$data = JRequest::get( 'post' );


		
			
		if ($model->store($data)) {
		
			$view	= JRequest::getVar( 'task', '', '', 'string', JREQUEST_ALLOWRAW );
			JRequest::setVar( 'view', 'mapajax');
			JRequest::setVar( 'layout', 'ajaxit');

		}

		parent::display();
	}


	public function loadimg()
	{

		JRequest::setVar( 'view', 'loadimg');
		JRequest::setVar( 'layout', 'loadimg');


		parent::display();
	}
	
	
	
	
}
