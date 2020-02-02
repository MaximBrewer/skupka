<?php
/*
 * @package Joomla 3.x
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
// import the JView class
jimport('joomla.application.component.view');

/**
* Foobar View
*/
class MapYandexViewMapyandexallmaps extends JViewLegacy
{
	/**
	 * Hellos view display method
	 * @return void
	 **/
	function display($tpl = null)
	{

			
			// add the CSS to the document
		$doc =JFactory::getDocument();
		$doc->addStyleSheet( JURI::root(true).'/administrator/components/com_mapyandex/assets/mapyandex.css' );
		$this->form	= $this->get('Form');
		$this->state = $this->get('State');

		$data['layout'] = JRequest::getVar('layout');
		$data['task'] = JRequest::getVar('task');
		

		if($data['layout'] == 'form') {
		JToolBarHelper::title(   JText::_( 'COM_MAPYANDEX_YANDEXNEWMAP' ), 'mapyandexallmaps' );
			JToolBarHelper::save('save', 'COM_MAPYANDEX_SAVE_NEW_MAP');
			JToolBarHelper::cancel('cancel');
		}
		else {
			JToolBarHelper::title(   JText::_( 'COM_MAPYANDEX_NAMECOMPONENT' ), 'allmaps' );
			JToolBarHelper::preferences('com_mapyandex', '460');
			JToolBarHelper::addNew('add');
			
			JToolBarHelper::deleteList(JText::_( 'COM_MAPYANDEX_DELETE_CONFIRM' ),'remove');
		}
		// interrogate the model
		$this->map = $this->get('Foobar');
		
		if($data['layout'] == 'form' && $data['task'] != 'add' && !empty($this->map)) $this->map = $this->map[0];
		
		if(empty($this->map) && $data['layout'] == 'form') {
			
			$this->map = $this->get('DefaultSettings');
			
		}
	
		
		$pageNav = $this->get('Reviews');
		$this->assignRef('pageNav', $pageNav);
		
		$params = JComponentHelper::getParams( 'com_mapyandex' );
				
		$tmpl['apikey'] = $params->get( 'key', '' );


		$this->assignRef( 'tmpl', $tmpl );
		
	
		
		parent::display($tpl);
	}
}

?>