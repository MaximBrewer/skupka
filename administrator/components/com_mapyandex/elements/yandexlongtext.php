<?php
/*
 * @package Joomla 1.5
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */
defined('JPATH_BASE') or die();

class JElementYandexLongText extends JElement
{
	var	$_name 			= 'YandexLongText';
	var $_yandexParams 	= null;

	function fetchElement($name, $value, &$node, $control_name)
	{
		$document	= &JFactory::getDocument();
		$option 	= JRequest::getCmd('option');
		
		$globalValue = &$this->_getYandexParameter( $name );	

		$size = ( $node->attributes('size') ? 'size="'.$node->attributes('size').'"' : '' );
		$class = ( $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="text_area"' );
        /*
         * Required to avoid a cycle of encoding &
         * html_entity_decode was used in place of htmlspecialchars_decode because
         * htmlspecialchars_decode is not compatible with PHP 4
         */
        $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);

		// MENU - Set default value to "" because of saving "" value into the menu link ( use global = "")
		if ($option == "com_menus") {
			$defaultValue	= $node->attributes('default');
			if ($value == $defaultValue) {
				$value = '';
			}
		}
		if ($option == "com_menus") {
		$html = JText::_( 'FTEXT' ).'<br />';
		}
		$html .= '<input type="text" name="'.$control_name.'['.$name.']" id="'.$control_name.$name.'" value="'.$value.'" '.$class.' '.$size.' />';		
		
		// MENU - Display the global value
		if ($option == "com_menus") {
			$html .='<br />'.JText::_( 'LTEXT' ).'<br /><span>[ </span><input type="text"  value="'. $globalValue .'" style="width:15em;border:1px solid #fff;background:#fff;margin-top:3px;" /><span> ]</span>'; 
		}
	return $html;
	}
	
	function _setYandexParams(){
	
		$component 		= 'com_yandexmap';
		$table 			=& JTable::getInstance('component');
		$table->loadByOption( $component );
		$yandexParams 		= new JParameter( $table->params );
		$this->_yandexParams	= $yandexParams;
		
	}

	function _getYandexParameter( $name ){
	
		// Don't call sql query by every param item (it will be loaded only one time)
		if (!$this->_yandexParams) {
			$params = &$this->_setYandexParams();
		}
		$globalValue 	= &$this->_yandexParams->get( $name, '' );	
		return $globalValue;
	}
}
?>