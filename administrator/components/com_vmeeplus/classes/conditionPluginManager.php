<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

define ('CONDITIONS_PLUGIN_BASE_CLASS', 'emp_conditionPlugins_base');
define ('CONDITIONS_PLUGIN_BASE_DIR_NAME', 'conditionPlugins');

class emp_conditionPluginManager{
	
	var $conditionPluginTypes = null;
	
	function getConditionTypes(){
		if($this->conditionPluginTypes == null){
			
			$conditionPluginTypes = array();
			$implementors = array_merge(emp_helper::getImplementors(VMEE_PRO_CLASSPATH, CONDITIONS_PLUGIN_BASE_DIR_NAME, CONDITIONS_PLUGIN_BASE_CLASS));
		
			foreach ($implementors as $implementor) {
				$cls = new $implementor;
				$conditionPluginTypes[$implementor] = $cls;
			}
			sort($conditionPluginTypes);
		}
		
		return $conditionPluginTypes;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param $type
	 * @return emp_conditionPlugins_base
	 */
	function getConditionPluginByType($type){
		$conditionTypes = $this->getConditionTypes();
		foreach($conditionTypes as $conditionPlugin){
			if($conditionPlugin->getType() == $type)
				return $conditionPlugin;
		}
		return null;
	}
}