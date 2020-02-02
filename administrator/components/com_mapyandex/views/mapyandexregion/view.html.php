<?php
/*
 * @package Joomla 3.x
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );
jimport('joomla.string');
/**
 * Hello View
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class MapYandexViewMapyandexregion extends JViewLegacy
{

	protected $state;
	protected $item;
	protected $form;
	protected $tmpl;

	function untextnl($text)
	{
		$text = str_replace("<br />","\r\n",$text);
		return $text;
	}
	
	function display($tpl = null)
	{
		$app	= JFactory::getApplication();
		$this->form	= $this->get('Form');
		$this->state = $this->get('State');
		$tmpl = array();
		$this->params = JComponentHelper::getParams( 'com_mapyandex' );

		$document = JFactory::getDocument();
		$jsLink = JURI::base(true);
		if (!$app->isAdmin()) {
			$tUri = JURI::base();

			$jsLink = JURI::base(true).'/administrator';
		}

		$uri		= JFactory::getURI();



		JHTML::stylesheet( 'administrator/components/com_mapyandex/assets/themes/base/jquery.ui.all.css' );
		JHTML::stylesheet( 'administrator/components/com_mapyandex/assets/mapyandex.css' );
		$document->addScript($jsLink . '/components/com_mapyandex/assets/js/jquery-1.7.2.min.js');
		$document->addScript($jsLink . '/components/com_mapyandex/assets/js/jquery-ui-1.8.21.custom.min.js');


		JToolBarHelper::title(   JText::_( 'COM_MAPYANDEX_REGIONS' ), 'mapyandexe' );
		
		$data['layout'] = JRequest::getVar('layout');
		$this->map = $this->get('Foobar');
		if($data['layout'] == 'form' && !empty($this->map)) {
			JToolBarHelper::apply('apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('save', 'JTOOLBAR_SAVE');
			
			JToolBarHelper::cancel( 'cancel','COM_MAPYANDEX_CANCEL' );	
			$map_region_style = array();
			$map_region_style = json_decode($this->map->map_region_style);

			$this->map->color_map_region = $map_region_style[1];
			$this->textarrayoutput = MapYandexHelper::textarrayForRegions($this->map);
			$this->route = MapYandexHelper::addRouteToMap($this->map);
			$this->regions = MapYandexHelper::addRegionsToMap($this->map);
			$this->form->bind($this->map);
		} 
		
		$this->allroute = $this->get('AllRoute');
		$this->metka = $this->get('Metka');
		
		$pageNav = $this->get('Reviews');
		$this->assignRef('pageNav', $pageNav);
		
		parent::display($tpl);
	}
}