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

class MapYandexModelMapYandexAjaxTask extends JModelLegacy
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
	 * @возвращает список строк в файл view.html.php
	 */
	function getFoobar()
	{
		// if foobar is not already loaded load it now
		if (!$this->_foobar)
		{
			$idmap = JRequest::getVar('idmap');
			$db = $this->getDBO();
			$query = "SELECT * FROM ".$db->quoteName('#__map_yandex') 
			." WHERE ".$db->quoteName('id')." = '".$idmap."'";
			$db->setQuery($query);
			$this->_foobar = $db->loadObject();
			
		}
		// return the foobar data
		return $this->_foobar;
	
	}
	function getIdregion()
	{
		// if foobar is not already loaded load it now
		if (!$this->_foobar)
		{
			$idmap = JRequest::getVar('idmapforregion');
			$db = $this->getDBO();
			$query = "SELECT * FROM ".$db->quoteName('#__map_yandex') 
			." WHERE ".$db->quoteName('id')." = '".$idmap."'";
			$db->setQuery($query);
			$this->_foobar = $db->loadObject();
			
		}
		// return the foobar data
		return $this->_foobar;
	
	}

	function textnl($text)
	{
		$text = str_replace("\r\n","<br />",$text);
		$text = str_replace("\r","<br />",$text);
		$text = str_replace("\n\n", '<p>',$text);
		$text = str_replace("\n", '<br />',$text); 
		return addslashes($text);
	}

	/**
	 * Method to store a record
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function store($post,$col)
	{	
		


		$el = json_encode($post);
		$idmap = JRequest::getVar('idmapforregion');
		// Store the web link table to the database
			$db = $this->getDBO();
			$query = "UPDATE ".$db->quoteName('#__map_yandex') 
			." set ".$db->quoteName($col)." = '".$this->textnl($el)
			."' WHERE ".$db->quoteName('id')." = '".$idmap."'";
			$db->setQuery($query);
			if (!$db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	

}


