<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

class emp_rules_adminUserRegistrationRule extends emp_rules_base{
	public function getTrigger(){
		return 'TRIGGER_ADMIN_USER_REGISTRATION';
	}

	public function getTriggerDisplayName(){
		return JText::_('TRIGGER_ADMIN_USER_REGISTRATION');
	}

	public function getDefaultReciepientByType($args){
		$parameters = $this->getParameters();
		$email = '';
		if(isset($parameters['preconds']['disabledefaultreciepient']['values']) && $parameters['preconds']['disabledefaultreciepient']['values'] == false){
			//as in VM, the vendor from email is also the email address for the admin user registration email
			$helper = new emp_helper();
			$email = $helper->getMailDefaultFromEmail();
		}
		return $email;
	}

	public function getOrientation(){
		return self::ORIENTATION_NEW_CUSTOMER;
	}

	public function getExcludeConditionTypes(){
		$excludeConditions = array('ORDER_STATUS','ORDER_TOTAL','PRODUCT_CATEGORY', 'PRODUCT', 'CART_PRODUCT', 'CART_PRODUCT_CATEGORY', 'CART_TOTAL','ORDER_VENDOR', 'ORDER_MANUFACTURER', 'ORDER_ID','ORDER_PAYMENT_METHOD');
		return $excludeConditions;
	}

	public function isSendEmails(){
		return !emp_helper::getGlobalParam('is_disable_admin_registration');
	}
	
	Public function allowEmailFromCustomer(){
		return true;
	}
}