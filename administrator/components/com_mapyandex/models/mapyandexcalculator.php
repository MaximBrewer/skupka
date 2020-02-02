<?php
/*
 * @package Joomla 1.5
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */
defined('_JEXEC') or die();

jimport('joomla.application.component.modeladmin');
mapyandeximport('mapyandex.tag.tag');
jimport( 'joomla.application.component.modellist' );

class MapYandexModelMapYandexCalculator extends JModelAdmin
{
/**
* Foobar ID
*
* @var int
*/
var $_id;
/**
* Foobar data
*
* @var object
*/
var $_foobar;
var $_allroute;
var $_metka;
/**
* Constructor, builds object and determines the foobar ID
*
*/
	function __construct()
	{

		parent::__construct();
		// get the cid array from the default request hash

		$id = JRequest::getVar('id', 3);

		
		$this->setId($id);
	}
	
	protected function getListQuery()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
 
                //CHANGE THIS QUERY AS YOU NEED...
                $query->select('id As value, name_map_yandex As name');
		$query->from('#__map_yandex AS a');
		$query->order('a.name');

 
 
		// Filter
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$query->where('(a.name LIKE '.$search.')');
		}
 
		return $query;
	}
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest('filter.search', 'filter_search');
		$this->setState('filter.search', $search);


		// List state information.
		parent::populateState('a.id', 'asc');
	}
	
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	2.5
	 */
	public function getTable($type = 'MapYandexRoute', $prefix = 'Table', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	2.5
	 */
	public function getForm($data = array(), $loadData = true) 
	{
		// Default opacity
		$def = $this->getFoobar();
		
		$def = (!empty($def->map_calculator_settings)) ? json_decode($def->map_calculator_settings) : array(100,100,1,1);	

		$form = $this->loadForm('com_mapyandex.mapyandexcalculator', 'mapyandexcalculator',
		                        array('control' => 'jform', 'load_data' => $loadData));
		$form->setFieldAttribute( 'map_price', 'default', $def[0] );
		$form->setFieldAttribute( 'map_min_price', 'default', $def[1] );
		$form->setFieldAttribute( 'map_calculator_delimetr', 'default', $def[2] );
		$form->setFieldAttribute( 'map_calculator_currency', 'default', $def[3] );
		if (empty($form)) 
		{
			return false;
		}
		return $form;
	}
	/**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getScript() 
	{
		return 'administrator/components/com_mapyandex/models/forms/map.js';
	}
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	2.5
	 */
	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_mapyandex.edit.mapyandexcalculator.data', array());
		/*if (empty($data)) 
		{
			$data = $this->getItem();
		}*/

		return $data;
	}

/**
* Обновление ID и данных
*
* @param int foobar ID
*/

	function setId($id)
	{
		$this->_id = $id;
		$this->_foobar = null;
	}
/**
* Получаем данные класса
*
* @return object
*/
	function getAllRoute()
	{
	
		
		
		// Create a new query object.
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		//вставляем насройки последней карты в новую...
		$data['task'] = JRequest::getVar('task');
		if($data['task'] !== 'add') {
		$app = JFactory::getApplication(); 
		$limit = JRequest::getVar('limit',$app->getCfg('list_limit'));
		
		$limitstart = JRequest::getVar('limitstart', 0);
			
			$query->select('a.*');
			$query->from('`#__map_yandex` AS a');

			
			$search = $this->getState('filter.search');

			if (!empty($search))
			{

				if (stripos($search, 'id:') === 0) {
					$query->where('a.id = '.(int) substr($search, 3).' AND map_type = "calculator"');
					
				}
				else
				{
		
					$search = $db->Quote('%'.$db->escape($search, true).'%');
					$query->where('( a.name_map_yandex LIKE '.$search.')'.' AND map_type = "calculator"');
				}
			} else {
					$query->where("map_type = 'calculator'");
			}

			$query = $db->setQuery( $query, $limitstart, $limit );
			$this->_allroute = $db->loadObjectList();
			
		}
		else {
		
			$query = ' SELECT * '
				. ' FROM #__map_yandex ORDER BY ID DESC';
				$query = $db->setQuery( $query);
			$this->_allroute = $db->loadObjectList();
		}

		
		
		// return the foobar data
		return $this->_allroute;
	
	}
	
	/**
* Получаем данные класса
*
* @return object
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
	function getMetka()
	{
	
			$db = $this->getDBO();
			$query = "SELECT * FROM ".$db->quoteName('#__map_yandex_metki') 
			." WHERE ".$db->quoteName('id_map')." = ".$this->_id;
			$db->setQuery($query);
			$this->_metka = $db->loadObjectList();
		
		// return the foobar data
		return $this->_metka;
	
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
	function store($post)
	{	
		
		$row = $this->getTable();
		
	
		
		$data = JRequest::get( 'post' );
		$data['misil'] = JRequest::getVar('misil', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$data['misilonclick'] = JRequest::getVar('misilonclick', '', 'post', 'string', JREQUEST_ALLOWRAW);
		
		$data['jform'] = JRequest::getVar('jform', '', 'post', 'string', JREQUEST_ALLOWRAW);
		
		// Bind the form fields to the hello table
		
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		//preg_match_all('@@',$this->textnl($data['allroutemap']),$matches);

		$lng = json_encode(array('longitude_map_yandex' => $data['longitude_map_yandex'],'latitude_map_yandex'=>$data['latitude_map_yandex']));
	
		$map_calculator_settings = json_encode(array($data['jform']['map_price'],$data['jform']['map_min_price'],$data['jform']['map_calculator_delimetr'],$data['jform']['map_calculator_currency']));
		$data['yandexel'] = json_encode($data['yandexel']);
		
		// Store the web link table to the database
			$db = $this->getDBO();
			
			if(!$data['editmarket']) {
			$query = "INSERT INTO ".$db->quoteName('#__map_yandex') 
			." (`name_map_yandex`, `city_map_yandex`, `street_map_yandex`,`full_address_map_yandex`,`map_type`,`map_calculator_settings`,`yandexzoom`,`yandexel`,`width_map_yandex`,`height_map_yandex`,`defaultmap`) VALUES 
			('".$data['name_map_yandex']."','".$data['city_map_yandex']."', '".$data['street_map_yandex']."', '".$data['city_map_yandex']."', '".$data['map_type']."', '".$map_calculator_settings."', '".$data['yandexzoom']."','".$data['yandexel']."','".$data['width_map_yandex']."','".$data['height_map_yandex']."','".$data['defaultmap']."')";
			} else {
			$query = "UPDATE ".$db->quoteName('#__map_yandex') 
			." set ".$db->quoteName('name_map_yandex')." = '".$data['name_map_yandex']
			."', ".$db->quoteName('city_map_yandex')." = '".$data['city_map_yandex']
			."', ".$db->quoteName('street_map_yandex')." = '".$data['street_map_yandex']
			."', ".$db->quoteName('full_address_map_yandex')." = '".$data['city_map_yandex']
			."', ".$db->quoteName('map_calculator_settings')." = '".$map_calculator_settings
			."', ".$db->quoteName('street_map_yandex')." = '".$data['street_map_yandex']
			."', ".$db->quoteName('yandexzoom')." = '".$data['yandexzoom']
			."', ".$db->quoteName('autozoom')." = '".$data['jform']['autozoom']
			."', ".$db->quoteName('yandexel')." = '".$data['yandexel']
			."', ".$db->quoteName('width_map_yandex')." = '".$data['width_map_yandex']
			."', ".$db->quoteName('height_map_yandex')." = '".$data['height_map_yandex']
			."', ".$db->quoteName('defaultmap')." = '".$data['defaultmap']
			."' WHERE ".$db->quoteName('id')." =".$data['id'];
			}
			
			$db->setQuery($query);
			if (!$db->query()) {
		
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
			

		return true;
	}

	/**
	 * Method to delete record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function delete(&$post)
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		$row = $this->getTable();

		if (count( $cids )) {
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
			}
		}
		return true;
	}

	function deleteitem()
	{
		$cids = JRequest::getVar( 'id', array(0), 'post', 'array' );

		$row =& $this->getTable();

		$this->setError( '123' );
		return '123';
	}
	
	
	function hit()
	{

		$db =& JFactory::getDBO();
		$db->setQuery('UPDATE '.$db->quoteName('#__map_yandex')
		.'SET '.$db->quoteName('hits').' = '.$db->quoteName('hits').' + 1 '.'WHERE id = '.$this->_id);
		$db->query();
	}
	/**
	 * 
	 * @возвращает список строк в файл view.html.php
	 */

	
	function getReviews()
	{
		global $option, $mainframe;
		$app = JFactory::getApplication(); 
		$limit = JRequest::getVar('limit',
		$app->getCfg('list_limit'));
		$limitstart = JRequest::getVar('limitstart', 0);
		$db = JFactory::getDBO();
		$query = "SELECT count(*) FROM #__map_yandex";
		$db->setQuery( $query );
		$total = $db->loadResult();
		$query = "SELECT * FROM #__map_yandex";
		$db->setQuery( $query, $limitstart, $limit );
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}

		return $this->pageNav = new JPagination($total, $limitstart, $limit);
		
	}
}
