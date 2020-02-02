<?php
/*
 * @package Joomla 1.5
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');
$view	= JRequest::getVar( 'view', '', '', 'string', JREQUEST_ALLOWRAW );


//if ($view == 'mapyandexmetki') {
/*
	JSubMenuHelper::addEntry(JText::_('COM_MAPYANDEX_ALLMAPS'), 'index.php?option=com_mapyandex&view=mapyandexallmaps');
	JSubMenuHelper::addEntry(JText::_('COM_MAPYANDEX_CREATEMAP'), 'index.php?option=com_mapyandex&amp;controller=mapyandexallmaps&amp;task=add');
	JSubMenuHelper::addEntry(JText::_('COM_MAPYANDEX_CREATE_MARKERS'), 'index.php?option=com_mapyandex&amp;view=mapyandexmetki' );
	JSubMenuHelper::addEntry(JText::_('COM_MAPYANDEX_INSRUCTION'), 'index.php?option=com_mapyandex&view=mapyandexdoc' );
}*/


$l[]	= array('COM_MAPYANDEX_HOMECP', '');
$l[]	= array('COM_MAPYANDEX_ALLMAPS', 'mapyandexallmaps');
$l[]	= array('COM_MAPYANDEX_CREATEMAP', '3');
$l[]	= array('COM_MAPYANDEX_CREATE_MARKERS', 'mapyandexmetki');
$l[]	= array('COM_MAPYANDEX_ROUTES', 'mapyandexroute');
$l[]	= array('COM_MAPYANDEX_REGIONS', 'mapyandexregion');
$l[]	= array('COM_MAPYANDEX_CALC', 'mapyandexcalculator');
$l[]	= array('COM_MAPYANDEX_INSRUCTION', 'mapyandexdoc');

foreach ($l as $k => $v) {

	if ($v[1] == '') {
		$link = 'index.php?option=com_mapyandex';
	}
	else {
		if($v[1] == 3) {
			$link = 'index.php?option=com_mapyandex&view=';
		} else {
			$link = 'index.php?option=com_mapyandex&view=';
		}

	}

	if($v[1] == $view) {
		JHtmlSidebar::addEntry(JText::_($v[0]), $link.$v[1], true);
	}	
	else {
		if($v[1] == 3) {
			JHtmlSidebar::addEntry(JText::_($v[0]), $link.'mapyandexallmaps&layout=form');
		} else {
			JHtmlSidebar::addEntry(JText::_($v[0]), $link.$v[1]);
		}

	}

}



	
class MapYandexController extends JControllerLegacy
{


	function display($cachable = false, $urlparams = array()) 
	{
		// set default view if not set
		JRequest::setVar('view', JRequest::getCmd('view', 'MapYandex'));
 
		// call parent behavior
		parent::display($cachable);
	}
	
	/**
	 * display task
	 *
	 * @return void
	 */
	public function ajaxdelete()
	{
	

			JRequest::setVar( 'view', 'mapyandexajaxdelete');
			JRequest::setVar( 'layout', 'default');


		parent::display();
	}

	
	/**
	 * display task
	 *
	 * @return void
	 */
	public function ajaxsavekey()
	{
	

			JRequest::setVar( 'view', 'mapyandexajax');
			JRequest::setVar( 'layout', 'row');


		parent::display();
	}
	
	public function loadimg()
	{

		JRequest::setVar( 'view', 'loadimg');
		JRequest::setVar( 'layout', 'loadimg');


		parent::display();
	}
}
