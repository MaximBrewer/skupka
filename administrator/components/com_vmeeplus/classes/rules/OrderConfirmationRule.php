<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

class emp_rules_orderConfirmationRule extends emp_rules_base{
	public function getTrigger(){
		return 'TRIGGER_ORDER_CONFIRMATION';
	}

	public function getTriggerDisplayName(){
		return JText::_('TRIGGER_ORDER_CONFIRMATION');
	}

	public function getDefaultReciepientByType($args){
		$parameters = $this->getParameters();
		$email = '';
		if(isset($parameters['preconds']['disabledefaultreciepient']['values']) && $parameters['preconds']['disabledefaultreciepient']['values'] == false){
			if(isset($args['order_id']) && !empty($args['order_id'])){
				$email = $this->getOrderUserEmail($args['order_id']);
			}
		}
		return $email;
	}

	public function getOrientation(){
		return self::ORIENTATION_ORDER | self::ORIENTATION_EXISTING_CUSTOMER;
	}

	public function isSendEmails(){
		return !emp_helper::getGlobalParam('is_disable_order_confirmation');
	}
}