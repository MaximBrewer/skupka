<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/


jimport( 'joomla.application.component.model' );

class vmeeProModelRuleList extends JModelLegacy {

	/**
	 * @return array of vmeeRule objects or false
	 */
	function getRuleList(){
		$db =JFactory::getDBO();
		$ruleManager = emp_rule::getManager();
		$orderClause = 'ORDER BY ' . $this->getState('filter_order') . ' ' . $this->getState('filter_order_Dir');
		$q = "SELECT * FROM #__vmee_plus_rules " . $orderClause;
		$db->setQuery($q);
		$result = $db->loadAssocList();
		if(!empty($result)){
			$ruleList = array();
			foreach ($result as $rule) {
				//$ruleList[] = new emp_rules_rule($rule['id']);
				$ruleList[] = $ruleManager->getRule($rule['id']);
			}
			return $ruleList;
		}
		return false;
	}

	function getRuleIdsByTriggerId($triggerId){
		$db =JFactory::getDBO();
		$q = "SELECT id FROM #__vmee_plus_rules WHERE trigger_id='" . $triggerId . "' AND enabled=1";
		$db->setQuery($q);
		return $db->loadAssocList();
	}

	public function setEnabled($rule_id, $isEnabled){
		//$rule = new emp_rules_rule($rule_id);
		$ruleManager = emp_rule::getManager();
		$rule = $ruleManager->getRule($rule_id);
		$rule->setEnabled($isEnabled);
		$result = $rule->save();
	}
	
	public function populateState() {
		$filter_order = JFactory::getApplication()->input->get('filter_order','id','RAW');
		$filter_order_Dir = JFactory::getApplication()->input->get('filter_order_Dir','ASC','RAW');
	
		$this->setState('filter_order', $filter_order);
		$this->setState('filter_order_Dir', $filter_order_Dir);
	}
}
