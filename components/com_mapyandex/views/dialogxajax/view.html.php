<?php
/*
 * @package Joomla 1.5
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view' );

class mapyandexViewdialogxajax extends JViewLegacy
{
	function display($tpl = null) {
		
		
		$params = JComponentHelper::getParams( 'com_mapyandex' );
				
		

		$this->assignRef( 'tmpl', $tmpl );
		
		// interrogate the model
		$foobar = $this->get('Foobar');
	
		$this->assignRef('foobar', $foobar);
		
		parent::display($tpl);
	}
}
?>
