<?php
/*
 * @package Joomla 3.x
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
jimport('joomla.html.pagination');

class MapYandexModelMapYandexAjax extends JModelLegacy
{

function __construct()
{

	parent::__construct();
	// get the cid array from the default request hash
	$cid = JRequest::getVar('cid', false, 'DEFAULT', 'array');

	if($cid)
	{
		$id = $cid[0];
	}
	else
	{
		$id = JRequest::getVar('id', 3);
	}

	$this->setId($id);
}


/**
* Обновление ID и данных
*
* @param int foobar ID
*/

	function setId($id)
	{
		$this->_id = $id;
		
	}
	var $_foobar;


	/**
	 * Вывод мудрых мыслей с пагинацией
	 * @возвращает список строк в файл view.html.php
	 */
	function getFoobar()
	{
		// if foobar is not already loaded load it now
		if (!$this->_foobar)
		{
			$db = $this->getDBO();
			$query = "SELECT * FROM ".$db->quoteName('#__map_yandex') 
			." WHERE ".$db->quoteName('id')." = ".$this->_id;
			$db->setQuery($query);
			$this->_foobar = $db->loadObject();
			
		}
		// return the foobar data
		return $this->_foobar;
	
	}

	

}
?>