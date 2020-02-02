<?php
/*
 * @package Joomla 1.5
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
jimport('joomla.html.pagination');

class MapYandexModelmapyandexajaxdelete extends JModelLegacy
{
	
	var $_foobar;
	var $_editmarker;
	var $_mid;
	
function __construct()
{

	parent::__construct();
	// get the cid array from the default request hash
	$mid = JRequest::getVar('mid',1);
	


	$this->setId($mid);
}


/**
* Обновление ID и данных
*
* @param int foobar ID
*/

	function setId($mid)
	{
		$this->_mid = $mid;
		$this->_foobar = null;
	}

	

	
	
	
	function textnl($text)
	{
		$text = str_replace("\r\n","<br />",$text);
		$text = str_replace("\r","<br />",$text);
		$text = str_replace("\n\n", '<p>',$text);
		$text = str_replace("\n", '<br />',$text); 
		return addslashes($text);
	}

		

	function getMarker()
	{
		$mid = JRequest::getVar( 'mid', array(0), 'post', 'array' );
				
		$db = JFactory::getDBO();
		
		$query = ' SELECT * '
			. ' FROM #__map_yandex_metki WHERE id='.$this->_mid;
		$query = $db->setQuery( $query);
		
		$this->marker = $db->loadObjectList();

		return $this->marker;
	}

	function getDelete()
	{
		$mid = JRequest::getVar('mid',0);
		$db = $this->getDBO();
		
			$query = "UPDATE ".$db->quoteName('#__map_yandex_metki') 
			." set ".$db->quoteName('userimg')." = '' WHERE id = '$mid'";
			
			$db->setQuery($query);
			if (!$db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			return true;
	}

}


