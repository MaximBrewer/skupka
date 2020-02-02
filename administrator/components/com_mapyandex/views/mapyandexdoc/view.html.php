<?php
/*
 * @package Joomla 3.x
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view' );

class MapYandexViewMapyandexdoc extends JViewLegacy
{
	function display($tpl = null) {
		
		
		$doc = JFactory::getDocument();

		JToolBarHelper::title(   JText::_( 'COM_MAPYANDEX_INSTRUCTION' ), 'mapyandexdoc' );
		$doc->addStyleSheet( JURI::root(true).'/administrator/components/com_mapyandex/assets/mapyandex.css' );
		
		parent::display($tpl);
	}
}
?>