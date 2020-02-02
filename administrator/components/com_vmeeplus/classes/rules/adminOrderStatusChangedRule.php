<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

class emp_rules_adminOrderStatusChangedRule extends emp_rules_base{
	public function getTrigger(){
		return 'TRIGGER_ADMIN_ORDER_STATUS_CHANGED';
	}

	public function getTriggerDisplayName(){
		return JText::_('TRIGGER_ADMIN_ORDER_STATUS_CHANGED');
	}

	public function getDefaultReciepientByType($args){
		$email = '';
		$parameters = $this->getParameters();
		$email = '';
		if(isset($parameters['preconds']['disabledefaultreciepient']['values']) && $parameters['preconds']['disabledefaultreciepient']['values'] == false){
			//as in VM, the vendor from email is also the email address for the admin order status changed email
			$helper = new emp_helper();
			$email = $helper->getMailDefaultFromEmail();
		}
		return $email;
	}

	public function getOrientation(){
		return self::ORIENTATION_ORDER | self::ORIENTATION_EXISTING_CUSTOMER;
	}

	public function isSendEmails(){
		return !emp_helper::getGlobalParam('is_disable_admin_order_status');
	}
	
	Public function allowEmailFromCustomer(){
		return true;
	}
}