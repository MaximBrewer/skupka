<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/


jimport( 'joomla.application.component.model' );


class vmeeProModelRule extends JModelLegacy {

	/**
	 * @param int $id
	 * @return emp_rules_base
	 */
	function getRule($id){
		//return new emp_rules_rule($id);
		$ruleManager = emp_rule::getManager();
		return $ruleManager->getRule($id);

	}

	function test($rule){
		//initialize the dispatcher and import plugin
		JPluginHelper::importPlugin('vmeepro');
		$dispatcher = JDispatcher::getInstance();

		$helper = new emp_helper();
		$serviceTriggers = emp_helper::getServiceTriggers();

		//get test parameters
		$emails = emp_helper::getGlobalParam('test_emails');
		if(strpos($emails, 'Change this') !== false){
			JError::raiseWarning('', JText::_('Please replace the test email address in the configuration to a valid email adress'));
			return false;
		}
		$testEmailsArr = explode(',', $emails);

		$trigger = $rule->getTrigger();

		if(!in_array($trigger, $serviceTriggers)){
			//this is not one of the basic service triggers
			$argsArr = $rule->getPreprocessedIdList(true);
			if(empty($argsArr)){
				JError::raiseNotice('', JText::_('No emails currently for this rule'));
				return true;
			}
			//we only want to send the first 3 emails
			for($i=0; $i<3; $i++){
				$args = array_shift($argsArr);
				if($args == null){
					break;
				}
				$mailArr = $this->execute($rule->getId(),$args);
				if(!empty($mailArr)){
					$mailArr['to'] = $testEmailsArr;
					$mailArr['cc'] = null;
					$mailArr['bcc'] = null;
					$res = $helper->sendMail($mailArr['from_email'], $mailArr['from_name'], $mailArr['to'], $mailArr['subject'], $mailArr['body'], true, $mailArr['cc'], $mailArr['bcc'], $mailArr['embedded_images'], null, null, $mailArr['attachments']);
					$sendRes = 'OK';
					if (is_a($res, 'JError')){
						$sendRes = $res->getErrors();
					}

					JError::raiseNotice('', $i+1 . '. ' . JText::_('SEND_TEST_MESSAGE') . $sendRes);
				}
				else{
					JError::raiseNotice('',  $i+1 . '. ' . JText::_('FOUND_RELEVANT CANDIDATE, BUT RULE CONDITIONS AVALUATION FAILED EMAIL WAS NOT SENT'));
					continue;
				}
			}
		}
		else{
			//this is one of the service emails - use the test user Id and order Id parameters
			$orderNum = emp_helper::getGlobalParam('test_order_id');
			$userId = (int)emp_helper::getGlobalParam('test_user_id');
			$productId = (int)emp_helper::getGlobalParam('test_product_id');
			if(empty($orderNum) || empty($userId) || empty($productId)){
				JError::raiseWarning('', JText::_('TEST_VARIABLES_ERROR'));
				return false;
			}
			$db =JFactory::getDBO();
			$q = 'SELECT virtuemart_order_id FROM #__virtuemart_orders WHERE order_number = ' . $db->Quote($orderNum);
			$db->setQuery($q);
			$orderId = $db->loadResult();
			$q = 'SELECT username FROM #__users WHERE id = ' . $db->Quote($userId);
			$db->setQuery($q);
			$userName = $db->loadResult();
			if(empty($orderId) || empty($userName)){
				JError::raiseWarning('', JText::_('Please make sure that the test user id and order id in the configuration represent real values in your store'));
				return false;
			}
			
			//user registration is a special case
			if($trigger == 'TRIGGER_USER_REGISTRATION'){
				$args = array('user_name'=>$userName, 'password'=>'testPassword');
			}
			else{
				$args = array('user_id'=>$userId, 'order_id'=>$orderId, 'product_id'=>$productId);
			}
			$mailArr = $this->execute($rule->getId(),$args);
			if(!empty($mailArr)){
				$mailArr['to'] = $testEmailsArr;
				$res = $helper->sendMail($mailArr['from_email'], $mailArr['from_name'], $mailArr['to'], $mailArr['subject'], $mailArr['body'], true, $mailArr['cc'], $mailArr['bcc'], $mailArr['embedded_images'], null, null, $mailArr['attachments']);
				$sendRes = 'OK';
				if (is_a($res, 'JError')){
					$sendRes = $res->getErrors();
				}

				JError::raiseNotice('', JText::_('SEND_TEST_MESSAGE') . $sendRes);
			}
			else{
				JError::raiseNotice('', JText::_('RULE_CONDITIONS_AVALUATION_FAILED_FOR_TEST_PARAMATERS_EMAIL_WAS_NOT_SENT'));
				return true;
			}
		}
		return true;
	}
	/**
	 * @param vmeeRule $rule
	 */
	function apply($rule){
		return $rule->save();
	}

	/**
	 * @param vmeeRule $rule
	 */
	function save($rule){
		return $rule->save();
	}

	function deleteCondition($id){
		$condition = new emp_rules_conditions_condition($id);
		return $condition->delete();
	}

	function saveCondition(){

		$rule_id = JFactory::getApplication()->input->get('rule_id', false,'RAW');
		$name = JFactory::getApplication()->input->get('conditionName', false,'RAW');
		$operator = JFactory::getApplication()->input->get('conditionOperator', false,'RAW');
		$value = JFactory::getApplication()->input->get('conditionValue', false,'RAW');
			
		$condition = new emp_rules_conditions_condition();
		$condition->setRuleId($rule_id);
		$condition->setName($name);
		$condition->setOperator($operator);
		$condition->setValue($value);
		return $condition->save();
	}

	function addNewCondition(){
			
		$rule_id = JFactory::getApplication()->input->get('rule_id', false,'RAW');
		$cond_type_id = JFactory::getApplication()->input->get('newConditionType', false,'RAW');
		$operator = JFactory::getApplication()->input->get('newConditionOperator', false, 'default','none', 2);
		$value = JFactory::getApplication()->input->get('values', false,'RAW');
		$textValues = JFactory::getApplication()->input->get('textvalues', false,'RAW');
			
		$condition = new emp_rules_conditions_condition();
		$condition->setRuleId($rule_id);
		$condition->setCondType($cond_type_id);
		$condition->setOperator($operator);
		$condition->setValue($value);
		$condition->setTextValue($textValues);
		return $condition->save();
	}

	function getConditions($ruleId){
		$res = array();
		if($ruleId){
			$rule = $this->getRule($ruleId);
			$conditions = $rule->getConditions();
			$i = 1;
			foreach ($conditions as $condition) {
				$res[] = array(
						'conditionId'		=>intval($condition->getId()),
						'counter'	=> $i++,
						'conditionName'	=> JText::_($condition->getName()),
						//				'conditionClass'	=> $condition->getClassName(),
						'conditionOperator'	=> $condition->getOperator(),
						'conditionValue'=> $condition->getValue(),
						'conditionTextValue'=> $condition->getTextValue());
			}
		}
		return $res;
	}

	function createNewRule(){
		$db =JFactory::getDBO();
			
		$trigger = JFactory::getApplication()->input->get('rule_trigger',null,'RAW');
		if(isset($trigger)){
			$trigger = $db->escape($trigger);
		}
			
		//$rule = new emp_rules_rule();
		$ruleManager = emp_rule::getManager();
		$rule = $ruleManager->newRule($trigger);
			
		$name = JFactory::getApplication()->input->get('rule_name',null,'RAW');
		$name = $db->escape($name);
		$rule->setName($name);
			
		$templateID = JFactory::getApplication()->input->get('templateID',null,'RAW');
		$templateID = $db->escape($templateID);
		$rule->setTemplateId($templateID);
			
		$rule_id = $rule->save();
		return $rule_id;
	}

	function createRuleFromRequest(){
		$db =JFactory::getDBO();

		$id = JFactory::getApplication()->input->get('rule_id',null,'RAW');
		$id = $db->escape($id);
			
		//$rule = new emp_rules_rule($id);
		$ruleManager = emp_rule::getManager();
		$rule = $ruleManager->getRule($id);
			
		$name = JFactory::getApplication()->input->get('name',null,'RAW');
		$name = $db->escape($name);
		$rule->setName($name);
			
		$trigger = JFactory::getApplication()->input->get('trigger',null,'RAW');
		if(isset($trigger)){
			$trigger = $db->escape($triggere);
			$rule->setTrigger($trigger);
		}
			
		$templateID = JFactory::getApplication()->input->get('templateID',null,'RAW');
		$templateID = $db->escape($templateID);
		$rule->setTemplateId($templateID);

		$to = JFactory::getApplication()->input->get('to','','RAW');
		$to = $db->escape($to);
		$rule->setTo($to);
			
		$cc = JFactory::getApplication()->input->get('cc','','RAW');
		$cc = $db->escape($cc);
		$rule->setCc($cc);
			
		$bcc = JFactory::getApplication()->input->get('bcc','','RAW');
		$bcc = $db->escape($bcc);
		$rule->setBcc($bcc);
		
		$attachments = JFactory::getApplication()->input->get('attachments','','RAW');
		$attachments = $db->escape($attachments);
		$rule->setAttachments($attachments);
			
		$from = JFactory::getApplication()->input->get('from','','RAW');
		$from = $db->escape($from);
		$rule->setFrom($from);
			
		$fromName = JFactory::getApplication()->input->get('fromName','','RAW');
		$fromName = $db->escape($fromName);
		$rule->setFromName($fromName);
			
		$isEmailToAdmins = JFactory::getApplication()->input->get('isEmailToAdmins',null,'RAW');
		$isEmailToAdmins = $db->escape($isEmailToAdmins);
		$rule->setIsEmailToAdmins($isEmailToAdmins);
			
		$isEmailToStoreAdmins = JFactory::getApplication()->input->get('isEmailToStoreAdmins',null,'RAW');
		$isEmailToStoreAdmins = $db->escape($isEmailToStoreAdmins);
		$rule->setIsEmailToStoreAdmins($isEmailToStoreAdmins);
			
		$isEnabled = JFactory::getApplication()->input->get('isEnabled',null,'RAW');
		$isEnabled = $db->escape($isEnabled);
		$rule->setEnabled($isEnabled);
			
		$parameters = $rule->getRulePreCondFromRequest();
		$rule->setParameters($parameters);
		
		$isUseCustomerFrom = JFactory::getApplication()->input->get('isUseCustomerFrom',0,'RAW');
		$isUseCustomerFrom = $db->escape($isUseCustomerFrom);
		$rule->setIsUseCustomerFrom($isUseCustomerFrom);
			
		return $rule;
	}

	function deleteRule($id){
		//$row = JTable::getInstance('VmeePlusRules', 'Table');
		//return $row->delete($id);
		$ruleManager = emp_rule::getManager();
		return $ruleManager->deleteRule($id);
	}

	private function validate(){
		$isValid = true;
		/*$template_id = JFactory::getApplication()->input->get('template_id',null,'RAW');
		 if(empty($template_id)){
		JError::raiseNotice('', JText::_('VALIDATE_TEMPLATE_NO_ID'));
		$isValid = false;
		}
		$name = JFactory::getApplication()->input->get('templateName',null,'RAW');
		if(empty($name)){
		JError::raiseNotice('', JText::_('VALIDATE_TEMPLATE_NO_NAME'));
		$isValid = false;
		}
		$emailSubject = JFactory::getApplication()->input->get('emailSubject',null,'RAW');
		if(empty($emailSubject)){
		JError::raiseNotice('', JText::_('VALIDATE_TEMPLATE_NO_SUBJECT'));
		$isValid = false;
		}
		$emailBody = JFactory::getApplication()->input->get('emailBody',null,'RAW');
		if(empty($emailBody)){
		JError::raiseNotice('', JText::_('VALIDATE_TEMPLATE_NO_BODY'));
		$isValid = false;
		}*/
			
		return $isValid;
	}

	/**
	 *
	 * Executes the rule logic via rule model.
	 * @param int	$rule_id	Rule ID
	 * @param Array 	$args	List of args
	 */
	function execute($rule_id, $args = null, & $errors = null){
		$rule = $this->getRule($rule_id);
		$mailArr = array();
		if($rule_id && $this->validateConditions($rule_id, $args, $errors)){
			$unique32Chars = substr(uniqid(null,true) . uniqid(null,true),0,32);
			$vmeeTemplate = $rule->getTemplate();
			$lang = $rule->getLangPreference();
			$args['lang'] = $lang;
			$body = $this->replaceTags($vmeeTemplate->getBody(), $args);
			$subject = $this->replaceTags($vmeeTemplate->getSubject(), $args);
			$body = emp_helper::fixImagePath($body);
			$subject = emp_helper::fixImagePath($subject);
			$body = emp_helper::fixLinksPath($body);
			$subject = emp_helper::fixLinksPath($subject);
			//Replace links in body - add $unique32Chars as parameter
			$body = preg_replace_callback('/(?<=href)\s*?=\s*?"\s*?.*?(?=")/',create_function(
					'$matches',
            		'return emp_helper::addURLParameter($matches[0],"umk","'.urlencode($unique32Chars).'");'
			)		,$body);
			
			$helper = new emp_helper();
			$reciepient = array();
			$defaultRecipient = $rule->getDefaultReciepientByType($args);
			if(!empty($defaultRecipient)){
				$reciepient[] = $defaultRecipient;
			}
			$toMails = explode(';', $rule->getTo());
			foreach ($toMails as $to){
				if(!empty($to)){
					$reciepient[] = $to;
				}
			}
			$ccMails = $rule->getCc();
			$reciepientCc = null;
			if(!empty($ccMails)){
				$reciepientCc = array();
				$ccMails = explode(';', $ccMails);
				foreach ($ccMails as $cc){
					if(!empty($cc)){
						$reciepientCc[] = $cc;
					}
				}
			}
			
			$bccMails = $rule->getBcc();
			$reciepientBcc = array();
			if(!empty($bccMails)){
				$bccMails = explode(';', $bccMails);
				foreach ($bccMails as $bcc){
					if(!empty($bcc)){
						$reciepientBcc[] = $bcc;
					}
				}
			}
			
			if($rule->isEmailToStoreAdmins() == 1){
				//add users that are storeadmin and admin to the bcc list
				$adminEmails = $this->getVMStoreAdministratorsEmails();
				if(!empty($adminEmails)){
					$reciepientBcc = array_merge($reciepientBcc, $adminEmails);
				}
			}
			
			if($rule->isEmailToAdmins() == 1){
				
				//add users that are joomla administrators and super administrator to the bcc list
				$adminEmails = $this->getJoomlaAdministratorsEmails();
				if(!empty($adminEmails)){
					$reciepientBcc = array_merge($reciepientBcc, $adminEmails);
				} 
			}
			
			$attachmentsMails = $rule->getAttachments();
			$attachmentsArray = array();
			if(!empty($attachmentsMails)){
				$attachmentsMails = explode(';', $attachmentsMails);
				foreach ($attachmentsMails as $attachment){
					if(!empty($attachment)){
						$attachmentsArray[] = $attachment;
					}
				}
			}

			// remove duplicates from the bcc list
			$reciepientBcc = empty($reciepientBcc) ? null : $reciepientBcc;
			$embeddedImages = emp_helper::getEmbeddedImages($vmeeTemplate->getBody());
			$mailArr['to'] = $reciepient;
			$mailArr['cc'] = $reciepientCc;
			$mailArr['bcc'] = $reciepientBcc;
			$mailArr['attachments'] = $attachmentsArray;
			$mailArr['subject'] = $subject;
			$mailArr['body'] = $body;
			$mailArr['from_name'] = $rule->getFromName($args);
			$mailArr['from_email'] = $rule->getFrom($args);
			$mailArr['embedded_images'] = $embeddedImages;
			$mailArr['unique_id'] = $unique32Chars;
		}
		return $mailArr;
	}

	/**
	 * Validates all conditions associate with this rule
	 *
	 * @param int	$rule_id	Rule ID
	 * @return boolean true if all conditions passed
	 **/
	function validateConditions($rule_id, $args = null, & $errors = null){
		$condPluginManager = new emp_conditionPluginManager();
		$rule = $this->getRule($rule_id);
		$conditions = $rule->getConditions();
		$is_valid = true;
		$errors = array();
		foreach ($conditions as $condition) {
			$is_valid = $condition->evaluateCondition($args, $errors);
			if(!$is_valid)
				return false;
		}
			
		return $is_valid;
	}

	/**
	 *
	 * Call all plugins that handle the context
	 * @param String $str
	 */
	function replaceTags($str, $resources = null){
			
		$errors = array();
			
		JPluginHelper::importPlugin('vmee');
		$dispatcher = JDispatcher::getInstance();
		if($resources != null){
			$resources['creator']="VM Emails Manager";
		}
		
		$dispatcher->trigger('doPriority1',  array(&$str, &$errors, &$resources));
		$dispatcher->trigger('doPriority2',  array(&$str, &$errors, &$resources));
		$dispatcher->trigger('doPriority3',  array(&$str, &$errors, &$resources));
		$dispatcher->trigger('replaceTags',  array(&$str, &$errors, &$resources));
		return $str;
	}

	function getTriggerList(){
		$ruleTriggerMap = array();
		$implementors = emp_helper::getImplementors(VMEE_PRO_CLASSPATH, 'rules', 'emp_rules_base');
		$i = 0;
		foreach ($implementors as $implementor) {
			$obj = new $implementor;
			$ruleTriggerMap[$obj->getTrigger()]['id'] = $obj->getTrigger();
			$ruleTriggerMap[$obj->getTrigger()]['display_name'] = $obj->getTriggerDisplayName();
			$ruleTriggerMap[$obj->getTrigger()]['orientation'] = $obj->getOrientation();
			$i++;
		}

		return $ruleTriggerMap;
	}
	
	public function getRuleName($ruleId){
		$db =JFactory::getDBO();
		$sql = "SELECT name from #__vmee_plus_rules WHERE id=" . $ruleId;
		$db->setQuery($sql);
		return $db->loadResult();
	}
	
	private function getJoomlaAdministratorsEmails(){
		$jGroups = emp_helper::getGlobalParam('jgroups');
		if(!is_array($jGroups)){
			$jGroups = array($jGroups);
		}
		
		$db =JFactory::getDBO();
		foreach ($jGroups as &$group){
			$group = $db->Quote($group);
		}
		$groupList = implode(',', $jGroups);
		$sql = "SELECT u.email from #__users u, #__user_usergroup_map g WHERE u.id=g.user_id AND g.group_id IN(" . $groupList . ")";
		$db->setQuery($sql);
		$emails = $db->loadColumn();
		return $emails;
	}
	
	private function getVMStoreAdministratorsEmails(){
		$vmGroups = emp_helper::getGlobalParam('vmgroups');
		if(!is_array($vmGroups)){
			$vmGroups = array($vmGroups);
		}
		
		
		$db =JFactory::getDBO();
		foreach ($vmGroups as &$group){
			$group = $db->Quote($group);
		}
		$groupList = implode(',', $vmGroups);
		$sql = "SELECT u.email from #__users u, #__virtuemart_vmusers vu WHERE vu.virtuemart_user_id=u.id AND vu.perms IN(" . $groupList . ")";
		$db->setQuery($sql);
		$emails = $db->loadColumn();
		return $emails;
		
	}


}
