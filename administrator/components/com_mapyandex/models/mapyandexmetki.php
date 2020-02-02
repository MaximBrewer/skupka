<?php
/*
 * @package Joomla 3.0
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
jimport('joomla.html.pagination');
jimport( 'joomla.filesystem.file' );
class MapYandexModelMapYandexmetki extends JModelLegacy
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


	function getFoobar()
	{
	
		// Create a new query object.
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		
		$data['task'] = JRequest::getVar('task');
		if($data['task'] !== 'add') {
			$app = JFactory::getApplication(); 
			$limit = JRequest::getVar('limit',$app->getCfg('list_limit'));
			$limitstart = JRequest::getVar('limitstart', 0);
	
			/*$query = "SELECT #__map_yandex_metki.*,#__users.id as uid,#__users.username FROM #__map_yandex_metki LEFT JOIN #__users ON #__map_yandex_metki.whoadd=#__users.id ORDER BY #__map_yandex_metki.id DESC";*/
			
			$query->select('#__map_yandex_metki.*,#__users.id as uid,#__users.username');
			$query->from('`#__map_yandex_metki`');
			//$query->join('LEFT', 'metki ON metki.whoadd = users.id');
			$query->leftJoin('#__users ON #__map_yandex_metki.whoadd = #__users.id');
			 //$query->innerJoin('#__users ON #__map_yandex_metki.whoadd = #__users.id');
			$search = $this->getState('filter.search');
			$query->order('#__map_yandex_metki.id DESC');
			if (!empty($search))
			{
				if (stripos($search, 'id:') === 0) {
					$query->where('a.id = '.(int) substr($search, 3));
		
				}
				else
				{

					$search = $db->Quote('%'.$db->escape($search, true).'%');
					$query->where('( #__map_yandex_metki.name_marker LIKE '.$search.')');
				}
			}
			

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
		$db = JFactory::getDBO();
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

		(trim($data['width_map_yandex']) !== '') ?  $data['width_map_yandex'] = $data['width_map_yandex'] : $data['width_map_yandex'] = 500;
		(trim($data['height_map_yandex']) !== '') ?  $data['height_map_yandex'] = $data['height_map_yandex'] : $data['height_map_yandex'] = 500;

		$lng = json_encode(array('longitude_map_yandex' => $data['longitude_map_yandex'],'latitude_map_yandex'=>$data['latitude_map_yandex']));
		$data['wih'] = json_encode(array($data['width'],$data['height']));
		// Store the web link table to the database
			$db = $this->getDBO();
			$id = JRequest::getInt('id');
			$user = JFactory::getUser(); // it's important to set the "0" otherwise your admin user information will be loaded
			if($user->id > 0) {
				$user = $user->id;
			}
			$date = date("Y-m-d H:i:s", time());
			
			$userimg = json_encode(array('smallfile'=>$data['smallfile'],'startfile'=>$data['startfile']));
			if(!$data['editmarket']) {
			$query = "INSERT INTO ".$db->quoteName('#__map_yandex_metki') 
			." (`name_marker`,`misil`,`misilonclick`,`city_map_yandex`, `street_map_yandex`, `checked_out`,`checked_out_time`,`ordering`,`published`,`hits`,`catid`,`params`,`yandexcoord`,`lng`,`id_map`,`deficon`,`whoadd`,`userimg`) VALUES 
			('".$data['name_marker']."','".$data['misil']."','".$data['misilonclick']."','".$data['city_map_yandex']."', '".$data['street_map_yandex']."', 0, '$date', 4, 1, 0, 1, '', '".$data['yandexcoord']."','$lng', '".$data['id_map']."', '".$data['deficon']."', '".$user."', '".$userimg."')";
			} else {
			$query = "UPDATE ".$db->quoteName('#__map_yandex_metki') 
			." set ".$db->quoteName('name_marker')." = '".$data['name_marker']
			."', ".$db->quoteName('misil')." = '".$data['misil']
			."', ".$db->quoteName('misilonclick')." = '".$data['misilonclick']
			."', ".$db->quoteName('city_map_yandex')." = '".$data['city_map_yandex']
			."', ".$db->quoteName('street_map_yandex')." = '".$data['street_map_yandex']
			."', ".$db->quoteName('yandexcoord')." = '".$data['yandexcoord']
			."', ".$db->quoteName('lng')." = '".$lng
			."', ".$db->quoteName('id_map')." = '".$data['id_map']
			."', ".$db->quoteName('deficon')." = '".$data['deficon']
			."', ".$db->quoteName('wih')." = '".$data['wih']
			."' WHERE ".$db->quoteName('id')." =".$data['id'];
			}
			
			
			$db->setQuery($query);
			if (!$db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
			

		return true;
	}
	
	function getMarker($cid)
	{
	
				
		$db = JFactory::getDBO();
		
		$query = ' SELECT * '
			. ' FROM #__map_yandex_metki WHERE id='.$cid;
		$query = $db->setQuery( $query);
		
		$this->marker = $db->loadObjectList();

		return $this->marker;
	}
	
	function delete(&$post)
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$db		= JFactory::getDBO();
		if (count( $cids )) {
				$params = JComponentHelper::getParams( 'com_mapyandex' );
					
			foreach($cids as $cid) {

				$marker = $this->getMarker($cid);
				$userimg = json_decode($marker[0]->userimg);
				$userimgarr = (array)$userimg;
				array_push($userimgarr,str_replace('_s','',$userimgarr['smallfile']));
				if(count((array)$userimg)>1) {

					$this->success = array();
					$this->info = array();
					$i = 0;
					foreach($userimgarr as $file) {
					++$i;
						$userpath = $params->get('userpathtoimg');
			
						if(empty($userpath)) {

							$userpath = '/images/mapyandex/yandexmarkerimg/';
						}
						
						if(JFile::exists($_SERVER['DOCUMENT_ROOT'].JURI::root(true).$userpath.$file)) {
							
							$jp = JPath::getPermissions($_SERVER['DOCUMENT_ROOT'].JURI::root(true).$userpath.$file);
							$ch = JPath::canChmod($_SERVER['DOCUMENT_ROOT'].JURI::root(true).$userpath.$file);
							
							$jp = str_split($jp, 3);

							if(strpos($jp[0],'w')) {
	
								if(JFile::delete($_SERVER['DOCUMENT_ROOT'].JURI::root(true).$userpath.$file)) {
									$this->success[] = 1;
									$this->info[] = $file;
								} else {
									$this->success[] = 0;
									$this->info[] = $file;
								}
								
							} else {
				
								//2 not perms
								$this->success[] = 2;
								$this->info[] = $file;
								JError::raiseNotice( 100, 'Ошибка файл "'.JURI::root(true).$userpath.$file.'" не существует!' );
							}
						} else {
					
							$this->success[] = 0;
							$this->info[] = $file;
							JError::raiseNotice( 100, JText::sprintf('COM_MAPYANDEX_ERROR_NO_FILE', JURI::root(true).$userpath.$file));
						
						}
							if($i == 3) {
								$query = 'DELETE FROM #__map_yandex_metki'
								. ' WHERE id = ' .$cid;
								$db->setQuery( $query );
								if (!$db->query()) {
									JError::raiseWarning( 500, $db->getError() );
								}
							}
					}
					
				} else {
					$query = 'DELETE FROM #__map_yandex_metki'
					. ' WHERE id = ' .$cid;
						$db->setQuery( $query );
						if (!$db->query()) {
							JError::raiseWarning( 500, $db->getError() );
						}
				}
			

			}
		}
		return true;
	}

}


