<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class NivoSliderTableItem extends JTable
{
	public function __construct(&$db) {
		parent::__construct(GlobalsNivoPro::TABLE_SLIDES, 'id', $db);
	}

	function bind($array, $ignore = '')
	{
		
		if (isset($array['params']) && is_array($array['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = (string)$registry;
		}
		
		if(empty($array['alias'])) {
			$array['alias'] = $array['title'];
		}
		$array['alias'] = JFilterOutput::stringURLSafe($array['alias']);
		if(trim(str_replace('-','',$array['alias'])) == '') {
			$array['alias'] = JFactory::getDate()->format("Y-m-d-H-i-s");
		}
		
		//dmp($array);exit();
		
		return parent::bind($array, $ignore);
	}
	
	//public function delete($pk=null){
		/*
		dmp($_REQUEST);
		MaxFunctionsAdmin::showTrace("delete item");
		dmp("items delete");
		exit();
		*/
		//parent::delete($pk);
	//}
	
}
