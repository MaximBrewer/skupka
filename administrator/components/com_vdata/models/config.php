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

class VdataModelConfig extends VDModel
{
    
    var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
        $mainframe 	= JFactory::getApplication();
		$context	= 'com_vdata.config.list.'; 
	}
	
	function _buildQuery()
	{
		$query = 'select * from '.$this->_db->quoteName('#__vd_config').' where id=1';
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
		$config = JFactory::getConfig();
		$query = 'select * from '.$this->_db->quoteName('#__vd_config').' where id=1';
		$this->_db->setQuery( $query );
		$item = $this->_db->loadObject();
		if(!empty($item)){
			$dbconfig = json_decode($item->dbconfig);
			if($dbconfig->local_db==1){
				$item->local_db = 1;
				$item->driver = '';
				$item->host = '';
				$item->user = '';
				$item->password = '';
				$item->database =  '';
				$item->dbprefix = '';
			}
			else{
				$item->local_db = 0;
				$item->driver = $dbconfig->driver;
				$item->host = $dbconfig->host;
				$item->user = $dbconfig->user;
				$item->password = $dbconfig->password;
				$item->database =  $dbconfig->database;
				$item->dbprefix = $dbconfig->dbprefix;
			}
			$item->php_settings = json_decode($item->php_settings);
			$item->notification = json_decode($item->notification);
			$item->xml_parent = json_decode($item->xml_parent);
			
		}
		else{
			$item = new stdClass();
			$item->id = 1;
			$item->limit = '';
			$item->delimiter->value = '';
			$item->enclosure->value = '';
			$item->csv_child = '';
			$item->local_db = 1;
		}
		return $item;
    }


	function store()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit( JText::_('INVALID_TOKEN') );
    	$input = JFactory::getApplication()->input;
		$post = JFactory::getApplication()->input->post->getArray();
		$notification = JFactory::getApplication()->input->post->getArray(array('notification'=>'RAW'));
		$post["notification"] = $notification["notification"];
		$query = 'show fields FROM '.$this->_db->quoteName('#__vd_config');
		$this->_db->setQuery( $query );
		if($this->_db->getErrorNum())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		$fields = $this->_db->loadObjectList();
		$obj = new stdClass();
	
		if(!is_numeric($post["limit"])){
			$this->setError("ENTER_BATCH_LIMIT_NUMERIC_VALUE");
			return false;
		}
		$local_db = JFactory::getApplication()->input->getInt('local_db', 0);
		$dbconfig = new stdClass();
		if($local_db==1){
			$dbconfig->local_db = 1;
		}
		else{
			$dbconfig->local_db = 0;
			$option = array();
			$option['driver'] = $post["driver"];
			$option['host'] = $post["host"];
			$option['user'] = $post["user"];
			$option['password'] = $post["password"];
			$option['database'] = $post["database"];
			
			try{
				$remote_db = JDatabaseDriver::getInstance( $option );
				$remote_db->connect();
			}
			catch(Exception $e){
				$this->setError($e->getMessage());
				return false;
			}
			$dbconfig->driver = $post["driver"];
			$dbconfig->host = $post["host"];
			$dbconfig->user = $post["user"];
			$dbconfig->password = $post["password"];
			$dbconfig->database = $post["database"];
			$dbconfig->dbprefix = $post["dbprefix"];
		}
		
		
		foreach($fields as $field){
			if($field->Field == 'id'){
				$obj->id = 1;
			}
			elseif($field->Field == 'delimiter'){
				$delimiter['value'] =  $post[$field->Field];
				if($post[$field->Field] == 'other'){
					$delimiter['other'] = $post['dother'];
				}
				
				$obj->{$field->Field} = json_encode($delimiter);
			}
			elseif($field->Field == 'enclosure'){
				$enclosure['value'] = $post[$field->Field];
				if($post[$field->Field] == 'other'){
					$enclosure['other'] = $post['eother'];
				}
				$obj->{$field->Field} = json_encode($enclosure);
			}
			elseif($field->Field == 'csv_child'){
				$csv_child['csv_child'] = $post[$field->Field];
				if($post[$field->Field]==1){
					$csv_child['child_sep'] = $post['child_sep'];
					if($post['child_sep'] == 'other'){
						$csv_child['child_del'] = $post['child_del'];
					}
				}
				$obj->{$field->Field} = json_encode($csv_child);
			}
			elseif($field->Field == 'dbconfig'){
				$obj->{$field->Field} = json_encode($dbconfig);
			}
			elseif($field->Field=='php_settings'){
				$obj->{$field->Field} = json_encode($post["php_settings"]);
			}
			elseif($field->Field=='notification'){
				$obj->{$field->Field} = json_encode($post["notification"]);
			}
			elseif($field->Field=='xml_parent'){
				$obj->{$field->Field} = json_encode($post["xml_parent"]);
			}
			else{
				$obj->{$field->Field} = $post[$field->Field];
			}
		}
		
		if(!$this->_db->updateObject('#__vd_config', $obj, 'id'))
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
	
	//fetches all the plugins available
	function getPlugins()
	{
		$query = 'select extension_id, name, element, folder from #__extensions where type = "plugin" and folder = "vdata" and enabled = 1';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		return $items;
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
	
}

?>