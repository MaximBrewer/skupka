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

class MapYandexModelmapajax extends JModelItem
{
	
	var $_foobar;
	var $_editmarker;
	
function __construct()
{

	parent::__construct();
	// get the cid array from the default request hash
	$id = JRequest::getVar('id',1);
	


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
		$this->_foobar = null;
	}

	

	
	function getEditmarker()
	{
	
	global $option, $mainframe;
	$db = JFactory::getDBO();
		$cid = JRequest::getVar('cid');

		$query = ' SELECT * '
			. ' FROM #__map_yandex_metki WHERE id='.$this->_id;
			$query = $db->setQuery( $query);
		$this->_editmarker = $db->loadObjectList();

		return $this->_editmarker;
	}

	/**
	 * Вывод мудрых мыслей с пагинацией
	 * @возвращает список строк в файл view.html.php
	 */
	function getFoobar()
	{
	
	global $option, $mainframe;
	$app = JFactory::getApplication(); 
	
	$db = JFactory::getDBO();
	//вставляем насройки последней карты в новую...
	$data['task'] = JRequest::getVar('task');
	if($data['task'] !== 'add') {

	$limit = JRequest::getVar('limit',
		$app->getCfg('list_limit'));
		$limitstart = JRequest::getVar('limitstart', 0);
		$query = "SELECT * FROM #__map_yandex_metki";
		$query = $db->setQuery( $query, $limitstart, $limit );
		$this->_foobar = $db->loadObjectList();
		
	}
	else {
	
		$query = ' SELECT * '
			. ' FROM #__map_yandex_metki ORDER BY ID DESC';
			$query = $db->setQuery( $query);
		$this->_foobar = $db->loadObjectList();
		}
		return $this->_foobar;
	}

	/**
	 * Вывод мудрых мыслей с пагинацией
	 * @возвращает список строк в файл view.html.php
	 */

	
	function getReviews()
	{
		global $option, $mainframe;
		$app = JFactory::getApplication(); 
		$limit = JRequest::getVar('limit',
		$app->getCfg('list_limit'));
		
		$limitstart = JRequest::getVar('limitstart', 0);
		$db =& JFactory::getDBO();
		$query = "SELECT count(*) FROM #__map_yandex_metki";
		$db->setQuery( $query );
		$total = $db->loadResult();
		$query = "SELECT * FROM #__map_yandex_metki";
		$db->setQuery( $query, $limitstart, $limit );
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}

		return $this->pageNav = new JPagination($total, $limitstart, $limit);
		
	}
	
	
	function textnl($text)
	{
		$text = str_replace("\r\n","<br />",$text);
		$text = str_replace("\r","<br />",$text);
		$text = str_replace("\n\n", '<p>',$text);
		$text = str_replace("\n", '<br />',$text); 
		return addslashes($text);
	}

		
		
	function store($post)
	{	

		
		$data = JRequest::get( 'post' );

		$data['misil'] = JRequest::getVar('misil', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$data['misilonclick'] = JRequest::getVar('misilonclick', '', 'post', 'string', JREQUEST_ALLOWRAW);

		// Bind the form fields to the hello table

		$data['misil'] = $this->textnl($data['misil']);
		$data['misilonclick'] = $this->textnl($data['misilonclick']);
		
		
		// Make sure the hello record is valid

		(!empty($data['width_map_yandex']) && trim($data['width_map_yandex']) !== '') ?  $data['width_map_yandex'] = $data['width_map_yandex'] : $data['width_map_yandex'] = 500;
		(!empty($data['height_map_yandex']) && trim($data['height_map_yandex']) !== '') ?  $data['height_map_yandex'] = $data['height_map_yandex'] : $data['height_map_yandex'] = 500;

		$lng = json_encode(array('longitude_map_yandex' => $data['longitude_map_yandex'],'latitude_map_yandex'=>$data['latitude_map_yandex']));
		$gmt = gmstrftime("%Y-%m-%d %H:%M:%S");
		// Store the web link table to the database
			$db = $this->getDBO();
			$id = JRequest::getInt('id');
			$user = JFactory::getUser(); // it's important to set the "0" otherwise your admin user information will be loaded
			if($user->id > 0) {
				$userid = $user->id;
			} else {
				$userid = 0;
			}
			
			$userimg = json_encode(array('smallfile'=>$data['smallfile'],'startfile'=>$data['startfile']));
			
			if(empty($data['editmarket'])) {
			$query = "INSERT INTO #__map_yandex_metki (`name_marker`,`misil`,`misilonclick`,`city_map_yandex`, `street_map_yandex`, `checked_out`,`checked_out_time`,`ordering`,`published`,`hits`,`catid`,`params`,`yandexcoord`,`lng`,`id_map`,`deficon`,`whoadd`,`userimg`) VALUES 
			('".$data['name_marker']."','".$data['misil']."','".$data['misilonclick']."','".$data['city_map_yandex']."', '".$data['street_map_yandex']."', 0, '$gmt', 4, 1, 0, 1, '', '".$data['yandexcoord']."','$lng', '".$id."', '".$data['deficon']."', '".$userid."', '".$userimg."')";
			} else {
			$query = "UPDATE #__map_yandex_metki set ".$db->Quote('name_marker')." = '".$data['name_marker']
			."', ".$db->Quote('misil')." = '".$data['misil']
			."', ".$db->Quote('misilonclick')." = '".$data['misilonclick']
			."', ".$db->Quote('city_map_yandex')." = '".$data['city_map_yandex']
			."', ".$db->Quote('street_map_yandex')." = '".$data['street_map_yandex']
			."', ".$db->Quote('yandexcoord')." = '".$data['yandexcoord']
			."', ".$db->Quote('lng')." = '".$lng
			."', ".$db->Quote('id_map')." = '".$data['id_map']
			."', ".$db->Quote('deficon')." = '".$data['deficon']
			."', ".$db->Quote('whoadd')." = '".$userid
			."' WHERE ".$db->Quote('id')." =".$data['id'];
			}
			
			
			$db->setQuery($query);
			if (!$db->query()) {

			$this->setError($this->_db->getErrorMsg());
			return false;
		}
			

		return true;
	}

	function delete()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$db		= JFactory::getDBO();
		if (count( $cids )) {
			foreach($cids as $cid) {
			$query = 'DELETE FROM #__map_yandex_metki'
			. ' WHERE id = ' .$cid;
				$db->setQuery( $query );
				if (!$db->query()) {
					JError::raiseWarning( 500, $db->getError() );
				}
			}
		}
		return true;
	}

}


