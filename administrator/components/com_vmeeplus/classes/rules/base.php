<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

abstract class emp_rules_base{
	var $id = null;
	var $name = null;
	var $trigger_id = null;
	var $template_id = null;
	var $conditions = null;
	var $toList = null;
	var $ccList = null;
	var $bccList = null;
	var $attachments = null;
	var $isEmailToAdmins = 0;
	var $isEmailToStoreAdmins = 0;
	var $parameters = null;
	var $enabled = 0;
	var $from = null;
	var $fromName = null;

	const ORIENTATION_ORDER = 1;
	const ORIENTATION_EXISTING_CUSTOMER = 2;
	const ORIENTATION_NEW_CUSTOMER = 4;
	const ORIENTATION_CART = 8;
	const ORIENTATION_WAITING_LIST = 16;
	const ORIENTATION_RECOMMEND = 32;
	
	public function __construct($ruleId = null){
		if(!empty($ruleId)){
			$this->id = $ruleId;
			$this->init();
		}
		else{
			$this->trigger_id = $this->getTrigger();
			//this will set the deault parameters.
			$this->setParameters(null);
		}
	}

	protected function init(){
		$this->getConditions();
		$row = $this->getData();

		$this->setId($row->id);
		$this->setName($row->name);
		$this->setTrigger($row->trigger_id);
		$this->setTemplateId($row->template_id);
		$this->setTo($row->toList);
		$this->setCc($row->ccList);
		$this->setBcc($row->bccList);
		$this->setAttachments($row->attachments);
		$this->setIsEmailToAdmins($row->isEmailToAdmins);
		$this->setIsEmailToStoreAdmins($row->isEmailToStoreAdmins);
		$this->setParameters($row->parameters);
		$this->setEnabled($row->enabled);
		$this->setFrom($row->from);
		$this->setFromName($row->fromName);
	}

	protected final function getData(){
		$row = JTable::getInstance('VmeePlusRules', 'Table');
		$row->load( $this->id );
		return $row;
	}

	/**
	 * @return assoc list
	 */
	public final function getConditions(){
		if($this->conditions == null){
			$this->conditions = array();
			$db =JFactory::getDBO();
			$q = "SELECT * FROM #__vmee_plus_conditions WHERE rule_id=".$this->id;
			$db->setQuery($q);
			$result = $db->loadAssocList();

			if(!empty($result)){
				foreach ($result as $condition) {
					$condObj = new emp_rules_conditions_condition($condition['id']);
					$this->conditions[] = $condObj;
				}
			}
		}
		return $this->conditions;
	}

	public final function save(){
		$row = JTable::getInstance('VmeePlusRules', 'Table');
		$data = get_object_vars($this);

		if (!$row->bind($data)) {
			JError::raiseWarning('', JText::_('RULE_NOT_SAVED'));
			return false;
		}
		if (!$row->store(true)) {
			JError::raiseWarning('', JText::_('RULE_NOT_SAVED'));
			return false;
		}
		$this->setId($row->id);
		return $row->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	public function getId(){
		return $this->id;
	}

	public function setName($name){
		$this->name = $name;
	}

	public function getName(){
		return $this->name;
	}

	public function setTrigger($trigger_id){
		$this->trigger_id = $trigger_id;
	}

	abstract public function getTrigger();

	abstract public function getTriggerDisplayName();/* {
		$trigger_row = $this->getTriggerRow($this->getTrigger());
	return JText::_($trigger_row->display_name);
	}  */

	public function setTemplateId($id){
		$this->template_id = $id;
	}

	/**
	 * @return int
	 */
	public function getTemplateId(){
		return $this->template_id;
	}

	public function getTemplate(){
		return new emp_templates_template($this->getTemplateId());
	}

	public function setConditions($conditions){
		$this->conditions = $conditions;
	}

	public function setTo($to){
		$this->toList = $to;
	}

	public function getTo(){
		return $this->toList;
	}

	public function setCc($cc){
		$this->ccList = $cc;
	}

	public function getCc(){
		return $this->ccList;
	}

	public function setBcc($bcc){
		$this->bccList = $bcc;
	}
	
	public function getBcc(){
		return $this->bccList;
	}
	
	public function setAttachments($attachments){
		$this->attachments = $attachments;
	}

	public function getAttachments(){
		return $this->attachments;
	}

	public function setIsEmailToAdmins($isEmailToAdmins){
		$this->isEmailToAdmins = $isEmailToAdmins == true ? 1 : 0;
	}

	/**
	 * @return int 1 or 0
	 */
	public function isEmailToAdmins(){
		return $this->isEmailToAdmins;
	}

	public function setIsEmailToStoreAdmins($isEmailToStoreAdmins){
		$this->isEmailToStoreAdmins = $isEmailToStoreAdmins == true ? 1 : 0;
	}

	/**
	 * @return boolean
	 */
	public function isEmailToStoreAdmins(){
		return $this->isEmailToStoreAdmins;
	}
	
	public function setIsUseCustomerFrom($useCustomerFrom){
		$params = $this->getParameters();
		$params['useCustomerFrom'] =$useCustomerFrom;
		$this->parameters = serialize($params);
	}
	
	public function isUseCustomerFrom(){
		$param = $this->getParameters();
		return isset($param['useCustomerFrom']) ? $param['useCustomerFrom'] : false;
	}

	/**
	 *
	 * @param array $parameters
	 */
	public function setParameters($parameters){
		$paramsArr = array();
		if(is_string($parameters)){
			$parameters = @unserialize($parameters);
		}
		if(isset($parameters['preconds'])){
			$paramsArr = $parameters;
		}
		else{
			$paramsArr['preconds']['disabledefaultreciepient']['values'] = isset($parameters[0]) && $parameters[0] == 'on' ? true : false;
			$paramsArr['preconds']['emaillanguage']['values'] = isset($parameters[1]) ? $parameters[1] : array('OL');
		}


		$this->parameters = serialize($paramsArr);
	}

	public function getParameters(){
		return unserialize($this->parameters);

	}

	/**
	 * @param int 1 or 0
	 */
	public function setEnabled($enabled){
		$this->enabled = $enabled == true ? 1 : 0;
	}

	public function setFrom($from){
		$this->from = $from;
	}

	public function getFrom($args = null){
		if($this->isUseCustomerFrom() && !is_null($args)){
			if(isset($args['user_id'])){
				return $this->getUserEmail($args['user_id']);
			}
			elseif(isset($args['order_id'])){
				return $this->getOrderUserEmail($args['order_id']);
			}
			else{
				return $this->getFrom();
			}
		}
		elseif(!empty($this->from)){
			return $this->from;
		}
		else{
			$helper = new emp_helper();
			return $helper->getMailDefaultFromEmail();
		}
	}

	public function setFromName($fromName){
		$this->fromName = $fromName;
	}

	public function getFromName($args = null){
		if($this->isUseCustomerFrom() && !is_null($args)){
			if(isset($args['user_id'])){
				return $this->getUserFullName($args['user_id']);
			}
			elseif(isset($args['order_id'])){
				return $this->getUserFullNameFromOrder($args['order_id']);
			}
			else{
				return $this->getFromName();
			}
		}
		elseif(!empty($this->fromName)){
			return stripslashes($this->fromName);
		}
		else{
			$helper = new emp_helper();
			return $helper->getMailDefaultFromName();
		}
	}

	/**
	 * @return int 1 or 0
	 */
	public function isEnabled(){
		return $this->enabled;
	}

	abstract public function getDefaultReciepientByType($args);
	abstract public function getOrientation();
	public function isSendEmails(){return true;}

	public function getRulePreconditions(){
		$parameters = $this->getParameters();
		if(empty($parameters)){
			return false;
		}
		$preconditionArr = array();

		$preconditionArr[0]['label'] = JText::_('DISABLE_EMAIL_TO_DEFAULT_RECIEPIENT');
		$preconditionArr[0]['type'] = 'CHECKBOX';
		$preconditionArr[0]['name'] = 'disabledefaultreciepient';
		$preconditionArr[0]['values'] = $parameters['preconds']['disabledefaultreciepient']['values'];
		$preconditionArr[0]['description'] = JText::_('DO_NOT_SEND_EMAIL_TO_THE_RULE_DEFAULT_RECIEPIENT');
		
		$preconditionArr[1]['label'] = JText::_('RULE_PARAM_EMAIL_LANG_NAME');
		$preconditionArr[1]['type'] = 'LANGUAGES_SELECT';
		$preconditionArr[1]['name'] = 'emaillanguage';
		$preconditionArr[1]['values'] = isset($parameters['preconds']['emaillanguage']['values']) ? $parameters['preconds']['emaillanguage']['values'] : array('FD');
		$preconditionArr[1]['description'] = JText::_('RULE_PARAM_EMAIL_LANG_DESC');
		
		return $this->formatPrecondition($preconditionArr);
	}

	/**
	 * @desc get a first list of users to send them emails. This function is used for 
	 * rules that are not triggered by user action. For example timer triggered rules.
	 * @param boolean $ignoreAlreadySentEmails - in test mode we want to send emails even
	 * if this user already received email from this rule. Of course, in test mode, emails
	 * are sent to the administrator and not to the user itself.
	 */
	public function getPreprocessedIdList($ignoreAlreadySentEmails = false){
		return array();
	}

	public function getRulePreCondFromRequest(){
		$parameters = array();
		$db =JFactory::getDBO();
		
		$parameters[] = $db->escape(JFactory::getApplication()->input->get('disabledefaultreciepient',null,'RAW'));
		$parameters[] = JFactory::getApplication()->input->get('emaillanguage',null,'RAW');
		
		return $parameters;
	}

	public function getUserIdFromArgs($args){
		$userId = null;
		if(isset($args['user_id']) && !empty($args['user_id'])){
			$userId = $args['user_id'];
		}
		elseif(isset($args['order_id']) && !empty($args['order_id'])){
			$db = JFactory::getDBO();
			$sql = sprintf("SELECT virtuemart_user_id from #__virtuemart_orders WHERE virtuemart_order_id=%d",$args['order_id']);
			$db->setQuery($sql);
			$userId = $db->loadResult();
		}
		return $userId;
	}

	public function getOrderIdFromArgs($args){
		$orderId = null;
		if(isset($args['order_id']) && !empty($args['order_id'])){
			$orderId = $args['order_id'];
		}
		return $orderId;
	}

	public function getExcludeConditionTypes(){
		$excludeConditions = array('CART_PRODUCT', 'CART_PRODUCT_CATEGORY', 'CART_TOTAL');
		return $excludeConditions;
	}
	
	public function getLangPreference(){
		$params = $this->getParameters();
		$lang = $params['preconds']['emaillanguage']['values'];
		return $lang;
	}
	
	Public function allowEmailFromCustomer(){
		return false;
	}

	protected function formatPrecondition($preconditionArr){
		$formattedPreconditionsArr = array();
		$imgPath = JUri::root() . 'administrator/components/com_vmeeplus/images/16x16/';
		foreach ($preconditionArr as $precondition){
			switch ($precondition['type']){
				case 'INPUT':
					$formattedPreconditionsArr[] = '<div class="rule_precond">' . $precondition['label'] . ' <input type="text" name="' . $precondition['name'] . '" id="' . $precondition['name'] . '" value="' . $precondition['values'] . '"/><img src="'.$imgPath.'help.png" title="' . $precondition['description'] . '" class="hasTip"/></div>';
					break;
				case 'CHECKBOX':
					$checked = (bool)$precondition['values'] === true ? "checked" : "";
					$field = sprintf("<input id=\"%s\" type=\"checkbox\" name=\"%s\" %s>\n", $precondition['name'], $precondition['name'], $checked);
					$field .= '<span>' . $precondition['label'] . '</span>';
					$formattedPreconditionsArr[] = '<div class="rule_precond">' . $field . '<img src="'.$imgPath.'help.png" title="' . $precondition['description'] . '" class="hasTip"/></div>';
					
					break;
				case 'MULTI_STATUS_SELECT':
					$statusArr = $this->getStatusArray();
					$field = sprintf("<select id=\"%s\" name=\"%s[]\" multiple=\"multiple\" >", $precondition['name'], $precondition['name']);
					foreach ($statusArr as $status){
						//create status options. If values contains statuses, mark them as selected
						$selected = '';
						$valuesArr = $precondition['values'];//explode(',', $precondition['values']);
						if(in_array($status['id'], $valuesArr)){
							$selected = 'selected="selected"';
						}
						$field .= sprintf("<option value=\"%s\" %s>%s</option>", $status['id'], $selected, $status['name']);
					}
					$field .= '</select>';
					$formattedPreconditionsArr[] = '<div class="rule_precond"><span>' . $precondition['label'] . '</span>' . $field . '<img src="'.$imgPath.'help.png" title="' . $precondition['description'] . '" class="hasTip"/></div>';
					break;
				case 'LANGUAGES_SELECT':
					$langArr = $this->getLanguagesArray();
					$field = sprintf("<select id=\"%s\" name=\"%s[]\" >", $precondition['name'], $precondition['name']);
					foreach ($langArr as $id=>$lang){
						$selected = '';
						$valuesArr = $precondition['values'];
						if(in_array($id, $valuesArr)){
							$selected = 'selected="selected"';
						}
						$field .= sprintf("<option value=\"%s\" %s>%s</option>", $id, $selected, $lang);
					}
					$field .= '</select>';
					$formattedPreconditionsArr[] = '<div class="rule_precond"><span>' . $precondition['label'] . '</span> ' . $field . '<img src="'.$imgPath.'help.png" title="' . $precondition['description'] . '" class="hasTip"/></div>';
				default:
			}
		}
		
		return $formattedPreconditionsArr;
	}

	protected function getOrderUserEmail($orderId){
		if(!isset($orderId) || empty($orderId)){
			return false;
		}

		emp_helper::loadVirtueMartFiles();
		$orderModel = emp_helper::getVmModels('orders');
		$order = $orderModel->getOrder($orderId);
		$order_details = $order['details']['BT'];
		return $order_details->email;
	}

	protected function getUserEmail($userId){
		if(!isset($userId) || empty($userId)){
			return false;
		}

		$db = JFactory::getDBO();
		$sql = sprintf("SELECT email from #__users WHERE id=%d",$userId);
		$db->setQuery($sql);
		$email = $db->loadResult();
		return $email;
	}
	
	protected function getUserEmailFromUserName($userName){
		if(!isset($userName) || empty($userName)){
			return false;
		}
	
		$db = JFactory::getDBO();
		$sql = sprintf("SELECT email from #__users WHERE username=%s",$db->Quote($userName));
		$db->setQuery($sql);
		$email = $db->loadResult();
		return $email;
	}

	protected function getUserFullName($userId){
		$db = JFactory::getDBO();
		$sql = sprintf("SELECT first_name, last_name from #__virtuemart_userinfos WHERE address_type='BT' AND virtuemart_user_id=%d",$userId);
		$db->setQuery($sql);
		$res = $db->loadAssoc();
		return $res['first_name'] . ' ' . $res['last_name'];
	}
	
	protected function getUserFullNameFromOrder($orderId){
		emp_helper::loadVirtueMartFiles();
		$orderModel = emp_helper::getVmModels('orders');
		$order = $orderModel->getOrder($orderId);
		$order_details = $order['details']['BT'];
		return $order_details->first_name . ' ' . $order_details->last_name;
	}
	
	protected function convertToMysql($time, $convert = false) {
	if($convert == false){
			return $time;
		}
		else{
			$date = new DateTime('@' .$time, new DateTimeZone('UTC'));
			return $date->format("Y-m-d G:i:s");
		}
	}
	
	protected function getStatusArray(){
		$db = JFactory::getDBO();
		$sql = "SELECT DISTINCT order_status_code AS id, order_status_name AS name  FROM #__virtuemart_orderstates WHERE virtuemart_vendor_id = 1";
		$db->setQuery($sql);
		$statusArr = $db->loadAssocList();
		return $statusArr;
	}
	
	protected function getLanguagesArray(){
		$langArray = array();
		$langArray['OL'] = 'Order Language';
		$langArray['UD'] = 'User Default language';
		$langArray['BD'] = 'Default back language';
		$langArray['FD'] = 'Default front language';
		
		$lang = JFactory::getLanguage();
		$siteLangs = $lang->getKnownLanguages(JPATH_SITE);
		foreach ($siteLangs as $code => $langObj){
			$langArray[$code] = $code;
		}
		
		return $langArray;
	}

}