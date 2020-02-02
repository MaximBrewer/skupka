<?php
/*
 * @package Joomla 3.0
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view' );

class MapYandexViewMapyandexajax extends JViewLegacy
{
	function display($tpl = null) {
		global $mainframe;
		$tmpl		= array();
		$css = '.icon-48-mapyandexdoc {
				background: url(\'../media/com_mapyandex/colorpicker/images/icon-48-mapyandexdoc.png\') 0 0 no-repeat;
		}';
		$doc = JFactory::getDocument();
		$doc->addStyleDeclaration($css);
		JToolBarHelper::title(   JText::_( 'INSTRUCTION' ), 'mapyandexdoc' );
		
		$params = JComponentHelper::getParams( 'com_mapyandex' );
		$key = JRequest::getVar('key');
		
		// Load the current component params.
		$params = JComponentHelper::getParams('com_mapyandex');
		// Set new value of param(s)
		$params->set('key', $key);

		// Save the parameters
		$componentid = JComponentHelper::getComponent('com_mapyandex')->id;
		$table = JTable::getInstance('extension');
		$table->load($componentid);
		$table->bind(array('params' => $params->toString()));

		// check for error
		if (!$table->check()) {
			//echo $table->getError();
			echo json_encode(array('error'=>1,'text'=>$table->getError()));
			return false;
		}
		// Save to database
		if (!$table->store()) {
			
			//echo $table->getError();
			echo json_encode(array('error'=>1,'text'=>$table->getError()));
			return false;
		} else {
			echo json_encode(array('ok'=>1,'text'=>JText::_( 'COM_MAPYANDEX_SAVE_DWS' )));
		}
	
		$this->setLayout('row');

		

		parent::display($tpl);
	}
}
?>
