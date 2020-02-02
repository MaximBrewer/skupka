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
defined('_JEXEC') or die('Restricted access');


class TableWidget extends JTable
{
    /**
     * Primary Key
     *
     * @var int
     */
    
 
    /**
     * @var string
     */
    var $id = null;
    var $name = null;
	var $chart_type = null;
	var $data = null;
    var $datatype_option = 'predefined';
	var $detail = null;
    var $create_time = null;
    var $userid = null;
	var $ordering = null;
    
    function TableWidget( &$db ) {
        parent::__construct('#__vd_widget', 'id', $db);
    }
	
	function bind($array, $ignore = '')
	{
		
		return parent::bind($array, $ignore);
		
	}
	
	function store($updateNulls = false)
	{
		
		if(!parent::store($updateNulls))	{
			return false;
		}
		$data = JFactory::getApplication()->input->post->getArray();
		if(isset($data['ordering']) && $data['ordering']==0){
		
	    $query='SELECT MAX(ordering) FROM #__vd_widget';
	    $this->_db->setQuery($query);
		$exid = $this->_db->loadResult();
        $query='UPDATE #__vd_widget SET ordering='.($exid+1).' where id='.$this->id;
		$this->_db->setQuery($query);
		$this->_db->query();
		}
		return true;
	
	}
	
	function delete($oid=null)
	{
		
		$this->id = $oid;
		
		$profile = $this->getProfile();
		
		if(empty($profile))	{
			$this->setError(JText::_('PROFILE_NOT_FOUND'));
			return false;
		}
		
		if(!parent::delete($oid))	{
			return false;
		}
		
		JPluginHelper::importPlugin('vdata', $profile->plugin);
		$dispatcher = JDispatcher::getInstance();
		
		try{
			$dispatcher->trigger('onDeleteProfile', array($oid));
			return true;
		}catch(Exception $e){
			$this->setError($e->getMessage());
			return false;
		}
		
	}
	
	//get the profile object with the associated plugin info
	function getProfile()
	{
				
		$query = 'select i.*, e.element as plugin from #__vd_profiles as i join #__extensions as e on (i.pluginid=e.extension_id and e.enabled=1) where i.id = '.(int)$this->id;
		$this->_db->setQuery( $query );
		$item = $this->_db->loadObject();
		
		return $item;
		
	}
	
}
