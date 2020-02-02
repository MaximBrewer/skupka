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
JHTML::_('behavior.tooltip');
/**
* Foobar View
*/
class MapYandexViewMapyandexmetki extends JViewLegacy
{
	/**
	 * Hellos view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
		// prepare the cSS
			$css = '.icon-48-mapyandexmarker {
				background: url("'.JURI::root(true).'/media/com_mapyandex/colorpicker/images/icon-48-mapyandexmarker.png") 0 0 no-repeat;
			}';
			
			// add the CSS to the document
		$doc = JFactory::getDocument();
		$doc->addStyleDeclaration($css);
		$this->form	= $this->get('Form');
		$this->state = $this->get('State');
		JHtml::stylesheet( 'administrator/components/com_mapyandex/assets/mapyandex.css' );

		$data['layout'] = JRequest::getVar('layout');
		
		if($data['layout'] == 'form') {
		JToolBarHelper::title(   JText::_( 'COM_MAPYANDEX_NEWNMARKER' ), 'mapyandexmarker' );
			JToolBarHelper::save('save');
			JToolBarHelper::cancel('cancel');
	
		}
		else if($data['layout'] == 'formedit') {
			JToolBarHelper::title(   JText::_( 'COM_MAPYANDEX_YANDEXMARKEREDIT' ), 'mapyandexmarker' );
		
			JToolBarHelper::save('save');
			JToolBarHelper::apply('apply');
			JToolBarHelper::cancel('cancel','JTOOLBAR_CLOSE');
					

		}	else {
			JToolBarHelper::title(   JText::_( 'COM_MAPYANDEX_YANDEXNEWMARKER' ), 'mapyandexmarker' );
			JToolBarHelper::preferences('com_mapyandex', '460');
			JToolBarHelper::addNew('add');
			
			JToolBarHelper::deleteList( JText::_( 'COM_MAPYANDEX_YANDEXNEWMARKER_DELETE_CONFIRM' ), 'remove');
		}
		// interrogate the model

		$foobar = $this->get('Foobar');
		$this->assignRef('foobar', $foobar);
		
		$editmarker = $this->get('Editmarker');
		$this->assignRef('editmarker', $editmarker);
		
		$pageNav = $this->get('Reviews');
		$this->assignRef('pageNav', $pageNav);
		
		$this->params = JComponentHelper::getParams( 'com_mapyandex' );
				

		if(count($this->editmarker) > 0) {
			if($this->editmarker['0']) {
				$cur_id_map = $this->editmarker[0];

				$id_map = $cur_id_map->id_map;
				$allmaps = $this->fetchElement($id_map);
				$this->assignRef( 'allmaps', $allmaps );
			}
		} else {
				$this->allmaps = $this->fetchElement();
		}
		
	

		require_once JPATH_COMPONENT. DS .'helpers'. DS .'mapyandex.php';
 
		$this->canwrite = MapYandexHelper::canWrite();
		parent::display($tpl);
		
		// Set the document
        //$this->setDocument();
	}
	
		protected function setDocument() 
        {
         
                $document = JFactory::getDocument();

     
                $document->addScript(JURI::root() . "/administrator/components/com_mapyandex"
                                                  . "/views/mapyandexmetki/submitbutton.js");
            
        }
		function fetchElement($id_map = null)
		{
	
			$db = JFactory::getDBO();

			$query = "SELECT * FROM ".$db->quoteName('#__map_yandex'). " WHERE map_type='map'";
			$db->setQuery( $query );
			$options = $db->loadObjectList();



		return JHTML::_('select.genericlist',  $options, 'id_map', 'class="inputbox"', 'id', 'name_map_yandex', $id_map, 'name_map_yandex' );
		}
}

?>