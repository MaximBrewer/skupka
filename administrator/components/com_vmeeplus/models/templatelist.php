<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

 
jimport( 'joomla.application.component.model' );

define ('ALL_TEMPLATE_TYPES', 'all_templates');
//define ('ABANDONED_TEMPLATE_TYPE', 'abandoned_cart_template');

class vmeeProModelTemplatelist extends JModelLegacy {
	
	function getTemplateList($triggerId = null){
		//TODO: what is this? $this->getDBO()
		$db = $this->getDBO();
		$whereClause = '';
		if(!is_null($triggerId)){
			$whereClause = sprintf(" WHERE trigger_id='%s'",$triggerId);
		}
		$orderClause = ' ORDER BY ' . $this->getState('filter_order') . ' ' . $this->getState('filter_order_Dir');
		$q = "SELECT * FROM #__vmee_plus_templates" . $whereClause .  $orderClause;
		$db->setQuery($q);
		$result = $db->loadAssocList();
		return $result;
	}
	
	public function populateState() {
		$filter_order = JFactory::getApplication()->input->get('filter_order','id','RAW');
		$filter_order_Dir = JFactory::getApplication()->input->get('filter_order_Dir','ASC','RAW');
	
		$this->setState('filter_order', $filter_order);
		$this->setState('filter_order_Dir', $filter_order_Dir);
	}
}
