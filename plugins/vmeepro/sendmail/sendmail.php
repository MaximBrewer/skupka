<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
defined('VMEE_PRO_CLASSPATH') or define ("VMEE_PRO_CLASSPATH", JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components' . DIRECTORY_SEPARATOR . 'com_vmeeplus' . DIRECTORY_SEPARATOR . 'classes'.DIRECTORY_SEPARATOR);

// Import library dependencies
jimport('joomla.plugin.plugin');

require_once JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_vmeeplus'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'helper.php';
require_once JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_vmeeplus'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'autoloader.php';

class plgvmeeproSendMail extends JPlugin
{

	function plgvmeeproSendMail( &$subject )
	{
		parent::__construct( $subject );

		// load plugin parameters
		//$this->_plugin = JPluginHelper::getPlugin( '<GroupName>', '<PluginName>' );
		//$this->_params = new JParameter( $this->_plugin->params );
	}
	/**
	 * Plugin method with the same name as the event will be called automatically.
	 */
	function OnSendMail($trigger, $args)
	{
		emp_logger::log('Start sending service mail',emp_logger::LEVEL_DEBUG,$trigger);
		$bRes = true;
		$licenseRes = emp_license::checkLicense();
		if($licenseRes != emp_license::SUCCESS){
			emp_logger::log('License check failed, reason:',emp_logger::LEVEL_WARNING, $licenseRes);
			return false;
		}
		$ruleListModelPath = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_vmeeplus'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'rulelist.php';
		$ruleModelPath = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_vmeeplus'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'rule.php';
		if(!file_exists($ruleListModelPath)){
			emp_logger::log('vmeepro files could not be included',emp_logger::LEVEL_WARNING);
			return false;
		}
		$triggerList = $this->getTriggerList();

		if($this->getVmeeproParam('is_disable_all_emails') || !$triggerList[$trigger]){
			//configuration set to not sending this trigger or any email
			emp_logger::log('Emails of this trigger are disabled by configuration',emp_logger::LEVEL_INFO,$trigger);
			return false;
		}

		require_once($ruleListModelPath);
		require_once($ruleModelPath);

		$ruleListModel = new vmeeProModelRuleList();
		$ruleModel = new vmeeProModelRule();
		//load all rules that listen to the trigger
		//foreach rule call it's execute() function
		if(isset($triggerList[$trigger])){
			//load relevant rules
			$ruleIds = $ruleListModel->getRuleIdsByTriggerId($trigger);
			emp_logger::log('Found the following rules',emp_logger::LEVEL_DEBUG,$ruleIds);
			$helper = new emp_helper();
			if(!empty($ruleIds)){
				foreach ($ruleIds as $ruleId){
					$id = $ruleId['id'];
					$rule = $ruleModel->getRule($id);
					emp_logger::log('Start working on rule:', emp_logger::LEVEL_DEBUG, array($id,$rule->getName()));
					$mailArr = $ruleModel->execute($id, $args);
					if(!empty($mailArr)){
						$time = time();
						$sendRes = 'OK';
						try {
							$res = $helper->sendMail($mailArr['from_email'], $mailArr['from_name'], $mailArr['to'], $mailArr['subject'], $mailArr['body'], true, $mailArr['cc'], $mailArr['bcc'], $mailArr['embedded_images'], null, null, $mailArr['attachments']);
						}
						catch (Exception $e){
							$sendRes = $e->getMessage();
						}
						
						if (is_a($res, 'JError')){
							$sendRes = $res->getErrors();
						}
						$userId = isset($args['user_id']) ? $args['user_id'] : null;
						$orderId = isset($args['order_id']) ? $args['order_id'] : null;
						emp_logger::log('message parameters: ',emp_logger::LEVEL_DEBUG, $mailArr);
						emp_logger::log('message has been sent to: ',emp_logger::LEVEL_DEBUG, $mailArr['to'], $orderId,$userId);
						$resstr = $res == true ? 'true' : 'false';
						emp_logger::log('message has been sent',emp_logger::LEVEL_DEBUG, $sendRes . $resstr . '-' . $res, $orderId,$userId);
						if($res === true){
							$status = 'success';
						}
						else{
							$status = 'failed';
						}
						$data = array();
						$data['unique_id'] = $mailArr['unique_id'];
						$data['type'] = $trigger;
						$data['rule_id'] = $id;
						$data['date'] = $time;
						$data['order_id'] = $rule->getOrderIdFromArgs($args);
						$data['user_id'] = $rule->getUserIdFromArgs($args);
						$data['open'] = 'no';
						$data['click_through'] = 'no';
						$data['generated_income'] = null;
						$data['template_id'] = $rule->getTemplateId();
						$data['status'] = $status;
						$data['email'] = $mailArr['to'][0];

						$row = JTable::getInstance('VmeePlusEmailsHistory', 'Table');
						$row->bind($data);
						$row->store();
					}
					else{
						emp_logger::log('Condition avaluation failed for:', emp_logger::LEVEL_DEBUG, array($id, $rule->getName()));
					}
				}
			}
		}
		return $bRes;
	}

	private function getTriggerList(){
		$triggers = array();
		$implementors = emp_helper::getImplementors(VMEE_PRO_CLASSPATH, 'rules', 'emp_rules_base');
		foreach ($implementors as $implementor) {
			$obj = new $implementor;
			$triggers[$obj->getTrigger()]= $obj->isSendEmails();
		}

		return $triggers;
	}

	private function getVmeeproParam($paramName){
		$compParams =JComponentHelper::getParams( 'com_vmeeplus' );
 		return $compParams->get('params.'. $paramName);
	}
}
?>