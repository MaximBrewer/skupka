<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

define ('RULE_BASE_CLASS', 'emp_rules_base');
define ('RULE_BASE_DIR_NAME', 'rules');

class emp_rule{
	private static $_instance = null;
	private $rulesObjArr = array();

	protected function __construct(){

	}

	protected function __clone(){
	}

	public static function getManager(){
		if(!isset(self::$_instance)){
			self::$_instance = new emp_rule();
		}
		return self::$_instance;
	}

	/**
	 * @desc get existing rule
	 * @param int $ruleId
	 * @return false | emp_rules_base
	 */
	public function &getRule($ruleId){
		$res = false;
		if(empty($ruleId)){
			return $res;
		}
		elseif(!isset($this->rulesObjArr[$ruleId])){
			//there is no rule in array, check if rule exist on DB
			$db = JFactory::getDBO();
			$sql = sprintf("SELECT trigger_id from #__vmee_plus_rules WHERE id=%d", $ruleId);
			$db->setQuery($sql);
			$triggerId = $db->loadResult();
			if(empty($triggerId)){
				//there is no such rule also in database
				return $res;
			}
			else{
				//create new rule object and load the rule's data from the database
				$this->newRule($triggerId, $ruleId);
			}
		}

		return $this->rulesObjArr[$ruleId];
	}

	public function &newRule($triggerId, $ruleId = null){
		if(empty($triggerId)){
			return false;
		}
		$triggerRuleMap = $this->getTriggerRuleMap();
		$rule = null;
		if(isset($triggerRuleMap[$triggerId])){
			$rule = new $triggerRuleMap[$triggerId]($ruleId);
		}
		else{
			return $rule;
		}

		$id = $rule->save();
		$this->rulesObjArr[$id] = $rule;
		return $this->rulesObjArr[$id];
	}

	public function deleteRule($ruleId){
		if(empty($ruleId)){
			return false;
		}
		$this->refreshRule($ruleId);
		$row = JTable::getInstance('VmeePlusRules', 'Table');
		return $row->delete($ruleId);
	}

	public function refreshRule($ruleId){
		if(empty($ruleId)){
			return false;
		}
		if(isset($this->rulesObjArr[$ruleId])){
			unset ($this->rulesObjArr[$ruleId]);
		}

		return true;
	}

	protected function getTriggerRuleMap(){
		$ruleTriggerMap = array();
		$implementors = emp_helper::getImplementors(VMEE_PRO_CLASSPATH, RULE_BASE_DIR_NAME, RULE_BASE_CLASS);

		foreach ($implementors as $implementor) {
			$obj = new $implementor;
			$ruleTriggerMap[$obj->getTrigger()] = $implementor;
		}

		return $ruleTriggerMap;
	}
}