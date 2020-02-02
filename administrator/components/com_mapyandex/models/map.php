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
class MapYandexModelMap extends JModelAdmin
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

/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	2.5
	 */
	public function getTable($type = 'Map', $prefix = 'Table', $config = array()) 
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
		// Get the form.
		$form = $this->loadForm('com_mapyandex.map', 'map',
		                        array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_mapyandex.edit.map.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
		}

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
		$text = addslashes($text);
		return $text;
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
		$data['text_map_yandex'] = JRequest::getVar('jform', '', 'post', 'string', JREQUEST_ALLOWRAW);
		// Bind the form fields to the hello table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		$lng = json_encode(array('longitude_map_yandex' => $data['longitude_map_yandex'],'latitude_map_yandex'=>$data['latitude_map_yandex']));
		$el = json_encode($data['yandexel']);
		$date = date("Y-m-d H:i:s", time());
		// Store the web link table to the database
			$db = $this->getDBO();
			$query = "UPDATE ".$db->quoteName('#__map_yandex') 
			." set ".$db->quoteName('misil')." = '".$this->textnl($data['misil'])
			."', ".$db->quoteName('misilonclick')." = '".$this->textnl($data['misilonclick'])
			."', ".$db->quoteName('id_map_yandex')." = '".$data['id_map_yandex']
			."', ".$db->quoteName('checked_out_time')." = '".$date
			."', ".$db->quoteName('defaultmap')." = '".$data['defaultmap']
			."', ".$db->quoteName('name_map_yandex')." = '".$data['name_map_yandex']
			."', ".$db->quoteName('city_map_yandex')." = '".$data['city_map_yandex']
			."', ".$db->quoteName('street_map_yandex')." = '".$data['street_map_yandex']
			."', ".$db->quoteName('width_map_yandex')." = '".$data['width_map_yandex']
			."', ".$db->quoteName('height_map_yandex')." = '".$data['height_map_yandex']
			."', ".$db->quoteName('oblako_width_map_yandex')." = '".$data['oblako_width_map_yandex']
			."', ".$db->quoteName('yandexbutton')." = '".$data['yandexbutton']
			."', ".$db->quoteName('color_map_yandex')." = '".$data['text_map_yandex']['color_map_yandex']
			."', ".$db->quoteName('bradius')." = '".$data['bradius']
			."', ".$db->quoteName('center_map_yandex')." = '".$data['center_map_yandex']
			."', ".$db->quoteName('yandexborder')." = '".$data['yandexborder']
			."', ".$db->quoteName('text_map_yandex')." = '".$data['text_map_yandex']['text_map_yandex']
			."', ".$db->quoteName('where_text')." = '".$data['where_text']
			."', ".$db->quoteName('yandexcoord')." = '".$data['yandexcoord']
			."', ".$db->quoteName('lng')." = '".$lng
			."', ".$db->quoteName('yandexzoom')." = '".$data['yandexzoom']
			."', ".$db->quoteName('autozoom')." = '".$data['text_map_yandex']['autozoom']
			."', ".$db->quoteName('yandexel')." = '".$el
			."', ".$db->quoteName('map_baloon_or_placemark')." = '".$data['text_map_yandex']['map_baloon_or_placemark']
			."', ".$db->quoteName('map_baloon_minwidth')." = '".$data['text_map_yandex']['map_baloon_minwidth']
			."', ".$db->quoteName('map_baloon_minheight')." = '". $data['text_map_yandex']['map_baloon_minheight']
			."', ".$db->quoteName('map_baloon_autopanduration')." = '".$data['text_map_yandex']['map_baloon_autopanduration']
			."', ".$db->quoteName('map_baloon_autopan')." = '".$data['text_map_yandex']['map_baloon_autopan']
			."', ".$db->quoteName('map_centering')." = '".$data['text_map_yandex']['map_centering']
			."', ".$db->quoteName('map_settings_user_all')." = '".$data['text_map_yandex']['map_settings_user_all']
			."' WHERE ".$db->quoteName('id')." =".$data['id'];
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

		$row =& $this->getTable();

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
	function hit()
	{

		$db = JFactory::getDBO();
		$db->setQuery('UPDATE '.$db->quoteName('#__map_yandex')
		.'SET '.$db->quoteName('hits').' = '.$db->quoteName('hits').' + 1 '.'WHERE id = '.$this->_id);
		$db->query();
	}

}
