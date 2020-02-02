<?php
/*
 * @package Joomla 1.5
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.modelitem');

class MapYandexModelMapYandex extends JModelItem
{




	function __construct() {
		parent::__construct();
		$id = JRequest::getInt('id');
		$this->setId((int)$id);
		
	}
	
	function setId($id){
		$this->_id = $id;
	
	}
	/**
	 * Myextensions data array
	 *
	 * @var array
	 */
	var $_foobar;


	/**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
	function _buildQuery()
	{

		$query = ' SELECT * '
			. ' FROM #__map_yandex WHERE id ="'.(int)$this->_id.'"';

		return $query;
	}


	function hit()
	{

		$db = JFactory::getDBO();
		$db->setQuery("UPDATE #__map_yandex SET hits = hits + 1 WHERE id = ".$this->_id);
		$db->query();
	}
	
	protected function populateState() 
	{
			$app = JFactory::getApplication();
			// Get the message id
			$id = JRequest::getInt('id');
			$this->setState('message.id', $id);
	 
			// Load the parameters.
			$params = $app->getParams();
			$this->setState('params', $params);
			parent::populateState();
	}
 
	/**
	 * Возвращаем данные
	 * @return array Возврату подлежит массив объектов
	 */
	function getFoobar()
	{
		// проверяем существует или нет 
		if (empty( $this->_foobar ))
		{
			$this->hit();
			$query = $this->_buildQuery();
			$this->_db->setQuery($query);
			
			if (!$this->_foobar = $this->_db->loadObject()) 
			{
				$this->setError($this->_db->getError());
			}
			else
			{
				// Load the JSON string
				$params = new JRegistry;
                // loadJSON is @deprecated    12.1  Use loadString passing JSON as the format instead.
				$params->loadString($this->_foobar->params, 'JSON');
				//$params->loadJSON($this->_foobar->params);
				$this->_foobar->params = $params;
 
				// Merge global params with item params
				$params = clone $this->getState('params');
				$params->merge($this->_foobar->params);
				$this->_foobar->params = $params;
			}			
		}

		return $this->_foobar;
	}
	
	/**
	 * Возвращаем метки
	 * @return array Возврату подлежит массив объектов
	 */
	function getMetka()
	{
	
			$db = $this->getDBO();
			$query = "SELECT * FROM #__map_yandex_metki WHERE id_map  = ".$this->_id;
			$db->setQuery($query);
			$this->_metka = $db->loadObjectList();

		// return the foobar data
		return $this->_metka;
	
	}

}
