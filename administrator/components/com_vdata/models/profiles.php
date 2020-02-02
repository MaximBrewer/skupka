<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die();

jimport('joomla.application.component.modellist');

class VdataModelProfiles extends VDModel
{
    var $_data = null;
    var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
 
        $mainframe = JFactory::getApplication();
		
		$context			= 'com_vdata.profiles.list.'; 
        // Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
		
        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
 
        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);

		$array = JFactory::getApplication()->input->get('cid',  0, 'ARRAY');
		$this->setId((int)$array[0]);
	}
	
	function _buildQuery()
	{
		$app = JFactory::getApplication();
		$context = 'com_vdata.profiles.list.';
		$db = JFactory::getDbo();
		
		$keyword = $app->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$filter_type = $app->getUserStateFromRequest( $context.'filter_type', 'filter_type', -1, 'int' );
		
		$user = JFactory::getUser();
		$query = 'select i.*, e.name as plugin, e.element, concat("plg_", e.folder, "_", e.element) as extension,count(l.profileid) as logs  FROM #__vd_profiles as i left join #__extensions as e on (e.extension_id=i.pluginid and e.enabled=1) left join #__vd_logs as l on i.id=l.profileid where 1=1';
		if (!$user->authorise('core.admin'))
		{
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query .= ' and i.access IN (' . $groups . ')';
		}
		
		if(!empty($keyword)){
			$query .= ' and i.title like '.$db->quote('%'.$keyword.'%');
		}
		if($filter_type!=-1){
			$query .= ' and i.iotype='.$db->quote($filter_type);
		}
		
		return $query;
	}
	
	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}
	
	function getItem()
    {
		
		$query = 'select i.*, e.name as plugin, e.element, concat("plg_", e.folder, "_", e.element) as extension FROM #__vd_profiles as i left join #__extensions as e on (e.extension_id=i.pluginid and e.enabled=1) where i.id = '.(int)$this->_id;
		$this->_db->setQuery( $query );
		$item = $this->_db->loadObject();
		
		if(empty($item))	{
			$item = new stdClass();
			$item->id = null;
			$item->title = null;
			$item->iotype = null;
			$item->quick = null;
			$item->pluginid = 0;
			$item->plugin = null;
			$item->element = null;
			$item->params = '';
		}
		
		$item->params = json_decode($item->params);
		
		return $item;
    }
	
	function getItems()
    {
        if(empty($this->_data))	{
		
			$query = $this->_buildQuery();
			$orderby = $this->_buildItemOrderBy();
			$query .= $orderby; 
			
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		
		}
		echo $this->_db->getErrorMsg();
        return $this->_data;
    }
	
	function getTotal()
  	{
        // Load the content if it doesn't already exist
        if (empty($this->_total)) {
            $query = $this->_buildQuery();
			$orderby = $this->_buildItemOrderBy();
			$query .= $orderby; 
            $this->_total = $this->_getListCount($query);
        }
        return $this->_total;
  	}
	
	function getPagination()
  	{
        // Load the content if it doesn't already exist
        if (empty($this->_pagination)) {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
        }
        return $this->_pagination;
  	}
	
	function _buildItemOrderBy()
	{
        $mainframe = JFactory::getApplication();
		
		$context			= 'com_vdata.profiles.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
 
        $orderby = ' group by i.id order by '.$filter_order.' '.$filter_order_Dir . ' ';
 
        return $orderby;
	}

	function store()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit( JText::_('INVALID_TOKEN') );
		$params = JFactory::getApplication()->input->post->getArray(array("params"=>"RAW"));
    	$post = JFactory::getApplication()->input->post->getArray(array());
		$post['params'] = $params['params'];
		
		if( $post['task']=='save2copy' ){
			$profile_count = $this->getProfileCount($post['id']);
			$post['id'] = 0;
			$post['title'] = JText::_('COPY_OF').' '.$post['title'];
			// $post['title'] = $post['title'].'('.($profile_count+1).')';
		}
		
		$row = $this->getTable();
		
		$row->load($post['id']);
		
		if (!$row->bind( $post )){
        	$this->setError($row->getError());
			return false;
		}
		
		if (!$row->check())	{
        	$this->setError($row->getError());
			return false;
		}
		
		if (!$row->store())	{
        	$this->setError($row->getError());
			return false;
		}
		
		if(!$post['id']){
			$post['id'] = $row->id;
			JFactory::getApplication()->input->set('id', $post['id']);
		}
			
		return true;
			
	}
	
	function getProfileCount($id=0){
		
		$query = $this->_db->getQuery(true);
		$query->select('count(*)');
		$query->from('#__vd_profiles');
		$query->where('id = '.$id);
		$query->group('id');
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
		
	}
	
	/**
	 * Method to delete record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function delete()
	{
		
		// Check for request forgeries
		JSession::checkToken() or jexit( JText::_('INVALID_TOKEN') );
		
		$cids = JFactory::getApplication()->input->get( 'cid', array(), 'ARRAY' );

		if (count( $cids ) < 1) {
			$this->setError( JText::_( 'SELECT_MIN', true ) );
			return false;
		}
		
		$row =  $this->getTable();

		foreach ($cids as $id)
		{
			
			if(!$row->delete($id))	{
				$this->setError($row->getError());
				return false;
				
			}

		}
		
		return true;
		
	}
	
	//fetches all the plugins available
	function getPlugins()
	{
		
		$query = 'select extension_id, name, element, folder, manifest_cache from #__extensions where type = "plugin" and folder = "vdata" and enabled = 1';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		
		return $items;
		
	}
	
	//fetch top profiles from remote server
	function getTopRemoteProfiles(){
		//fetch top profiles id, title and description for suggestion
		// $url = 'http://www.joomlawings.com/index.php';
		$url = 'http://www.wdmtech.com/demo/index.php';
		
		$install_components = $this->getInstallComponents();
		$enable_plg_elements = $this->getPlugins();
		$enable_plg_elements = json_encode($enable_plg_elements);
		
		$postdata = array("option"=>"com_vdata", "task"=>"getTopProfiles", "token"=>JSession::getFormToken(), "scope"=>$install_components, "plg_element"=>$enable_plg_elements);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$result = curl_exec($ch);
		if($result === false){
			return false;
		}
		curl_close($ch);
		return json_decode($result);
	}
	
	//get the profile object with the associated plugin info
	function getProfile()
	{
		
		$id = JFactory::getApplication()->input->getInt('profileid', 0);
		
		$query = 'select i.*, e.element as plugin from #__vd_profiles as i join #__extensions as e on (i.pluginid=e.extension_id and e.enabled=1) where i.id = '.$id;
		$this->_db->setQuery( $query );
		$item = $this->_db->loadObject();
		
		return $item;
		
	}
	
	function getInstallComponents(){
		$db = JFactory::getdbo();
		$query = $db->getQuery(true);
		$query->select('e.element as title')
			->from('#__menu AS m');

			// Filter on the enabled states.
			$query->join('LEFT', '#__extensions AS e ON m.component_id = e.extension_id')
			->where('m.client_id = 1')
			->where('e.enabled = 1')
			->where('m.id > 1')
			->where('m.parent_id = 1');
		// Order by lft.
		$query->order('m.lft');

		$db->setQuery($query);

		// Component list
		$components = $db->loadObjectList();
		$install_components ='"com_joomla"';
		foreach ($components as &$component)
		{
			$install_components .= ',"'.$component->title.'"';
		}
		return $install_components;
	}
	
	function getAssocSchedule(){

		$id = JFactory::getApplication()->input->getInt('id', 0);
		$query = $this->_db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from($this->_db->quoteName('#__vd_schedules'));
		$query->where($this->_db->quoteName('profileid')." = ".(int)$id);
		$query->group($this->_db->quoteName('profileid'));
		$result = $this->_db->setQuery($query)->loadResult();
		return $result;
		
	}
	
	function getProfileById($id){
		
		$query = $this->_db->getQuery(true);
		$query->select('*');
		$query->from($this->_db->quoteName('#__vd_profiles'));
		$query->where($this->_db->quoteName('id').' = '.(int)$id);
		$this->_db->setQuery($query);
		return $this->_db->loadObject();
		
	}
	
	function getComponents(){
		
		/* $query = $this->_db->getQuery(true);
		$query->select($this->_db->quoteName(array('element')))
				->from($this->_db->quoteName('#__extensions'))
				->where($this->_db->quoteName('type') . ' = ' . $this->_db->quote('component'));
				->where($this->_db->quoteName('enabled') . ' = 1');
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList(); */
		
		$query = $this->_db->getQuery(true);
		$query->select('e.element')
			->from('#__menu AS m');

			// Filter on the enabled states.
			$query->join('LEFT', '#__extensions AS e ON m.component_id = e.extension_id')
			->where('m.client_id = 1')
			->where('e.enabled = 1')
			->where('m.id > 1')
			->where('m.parent_id = 1');
		// Order by lft.
		$query->order('m.lft');

		$this->_db->setQuery($query);

		// Component list
		$components = $this->_db->loadObjectList();
		return $components;
		
	}
	
	function getWizardProfiles(){
		
		$wizardProfiles = new stdClass();
		
		$app = JFactory::getApplication();
		$context = 'com_vdata.profiles.wizard.';
		
		$iotype = $app->getUserStateFromRequest( $context.'iotype', 'iotype', -1, 'int' );
		$component = $app->getUserStateFromRequest( $context.'component', 'component', '', 'string' );
		$keyword = $app->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		

		$config = JFactory::getConfig();
		$config_limit = $config->get('list_limit');
		$limit = JFactory::getApplication()->input->getVar('limit', $config_limit);
		$limitstart = JFactory::getApplication()->input->getVar('limitstart', 0);
		
		
		
		$url = 'http://www.wdmtech.com/demo/index.php';
		
		$postdata = array("keyword"=>$keyword,"iotype"=>$iotype, "option"=>"com_vdata", "task"=>"getWizardProfiles", "token"=>JSession::getFormToken(), "scope"=>$component, "limit"=>$limit,"limitstart"=>$limitstart);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$result = curl_exec($ch);
// print_r($result);
		
		curl_close($ch);
		$result = json_decode($result);
		if(empty($result) || $result->result === 'error'){
			return false;
		}
		
		if($result->result=='success'){
			$wizardProfiles->components = $result->components;
			$wizardProfiles->profiles = $result->profiles;
			$wizardProfiles->total = $result->total;
			return $wizardProfiles;
		}
		return false;
		
	}
	
	function getWizardProfile(){
		
		$url = 'http://www.wdmtech.com/demo/index.php';
		
		$postdata = array("option"=>"com_vdata", "task"=>"getWizardProfile", "token"=>JSession::getFormToken(), "id"=>$this->_id);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$result = curl_exec($ch);
		$result =  json_decode($result);
		if($result->result=='success'){
			$plugin = $this->getPlugin($result->profile->plg_element);
			if(!$plugin){
				return false;
			}
			$result->profile->plugin = $plugin->plugin;
			$result->profile->element = $plugin->element;
			$result->profile->pluginid = $plugin->pluginid;
			
			return $result->profile;
		}
		else{
			return false;
		}
		
	}
	
	function getPlugin($element){
		
		$query = 'select e.name as plugin, e.element,e.extension_id as pluginid from #__extensions as e where type = "plugin" and folder = "vdata" and enabled = 1 and element='.$this->_db->quote($element);
		$this->_db->setQuery( $query );
		$item = $this->_db->loadObject();
		return empty($item)?false:$item;
		
	}
	
	function copyList($cid){
		
		if(count($cid)){
			$query = $this->_db->getQuery(true);
			foreach($cid as $id){
				$query->clear();
				$query->select('*')
					->from('#__vd_profiles')
					->where('id='.$this->_db->quote($id));
				$this->_db->setQuery($query);
				$profile = $this->_db->loadObject();
				
				$query->clear();
				$query->select('count(*)')
					->from('#__vd_profiles')
					->where('title = '.$this->_db->quote($profile->title));
				$this->_db->setQuery($query);
				$pcount = $this->_db->loadResult();
				
				//copy profile
				unset($profile->id);
				//change title,created_by
				
				if(!$this->_db->insertObject('#__vd_profiles', $profile)){
					$this->setError($this->_db->stderr());
					return false;
				}
			}
		}
		else{
			$this->setError(JText::_('PLZ_SELECT_PROFILE'));
			return false;
		}
		return true;
	}
	
	function getConfig(){
		$query = $this->_db->getQuery(true);
		$query->select('*')
			->from('#__vd_config')
			->where('id=1');
		$this->_db->setQuery($query);
		return $this->_db->loadObject();
	}
	
}

?>