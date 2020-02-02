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

/**
 * Hello View
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class MapYandexViewMapyandexcalculator extends JViewLegacy
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



		$document->addStyleSheet( JURI::root(true).'/administrator/components/com_mapyandex/assets/themes/base/jquery.ui.all.css' );
		$document->addStyleSheet( JURI::root(true).'/administrator/components/com_mapyandex/assets/mapyandex.css' );
		$document->addScript($jsLink . '/components/com_mapyandex/assets/js/jquery-1.7.2.min.js');

		
		// prepare the cSS
			$css = '.icon-48-mapyandexcalculator {
				background: url("'.JURI::root(true).'/media/com_mapyandex/colorpicker/images/icon-48-png") 0 0 no-repeat;
			}';
			
			// add the CSS to the document

		$document->addStyleDeclaration($css);


		JToolBarHelper::title(   JText::_( 'COM_MAPYANDEX_CALC' ), 'mapyandexcalculator' );
		
		$data['layout'] = JRequest::getVar('layout');

		if($data['layout'] == 'form') { 
			JToolBarHelper::save('save', 'JTOOLBAR_SAVE');
			JToolBarHelper::cancel( 'cancel','COM_MAPYANDEX_CANCEL' );
		}
		else if($data['layout'] == 'formedit') {

			JToolBarHelper::apply('apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('save', 'JTOOLBAR_SAVE');
			JToolBarHelper::cancel( 'cancel','COM_MAPYANDEX_CANCEL' );
	
		} else {
			JToolBarHelper::title(   JText::_( 'COM_MAPYANDEX_CALC' ), 'mapyandexcalculator' );
			JToolBarHelper::preferences('com_mapyandex', '460');
			JToolBarHelper::addNew('add');
			
			JToolBarHelper::deleteList( JText::_( 'COM_MAPYANDEX_CALC_DELETE_CONFIRM' ), 'remove');
		}
		
		// interrogate the model
		$this->map = $this->get('Foobar');
		if(empty($this->map)) {
			require_once JPATH_COMPONENT.DS.'models'.DS.'mapyandexallmaps.php';
			$mapset = new MapYandexModelMapYandexAllMaps();
			$this->map = $mapset->getDefaultSettings();
		
		}

		$this->allroute = $this->get('AllRoute');

		$this->assignRef( 'tmpl', $tmpl );
		
		$this->metka = $this->get('Metka');
		
		$pageNav = $this->get('Reviews');
		$this->assignRef('pageNav', $pageNav);
		
		parent::display($tpl);
	}
}
?>