<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class NivoSliderTableSliders extends JTable
{
	public function __construct(&$db) {
		parent::__construct(GlobalsNivoPro::TABLE_SLIDERS, 'id', $db);
	}

	function bind($array, $ignore = '')
	{
		
		$array["visual"] = UniteFunctionJoomla::encodeArrayToRegistry($array, "visual");
		$array["params"] = UniteFunctionJoomla::encodeArrayToRegistry($array, "params");
		
		if(empty($array['alias'])) {
			$array['alias'] = $array['title'];
		}
		
		$array['alias'] = UniteFunctionJoomla::normalizeAlias($array['alias']);
		
		return parent::bind($array, $ignore);
	}
	
	
}
