<?php

// No direct access
defined('_JEXEC') or die;

if(version_compare(JVERSION,'3.0.0','ge')) {
	class VM2SMSProxyController extends JControllerLegacy{}
} else {
	class VM2SMSProxyController extends JController{}
}

class Vm2smsController extends VM2SMSProxyController
{
	public function save(){
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$send_sms = JRequest::getVar('send_sms');
		$data = JRequest::getVar('text_sms');
		$worktime = JRequest::getVar('worktime');
		$manager_send_sms = JRequest::getVar('manager_send_sms');
		$manager_data = JRequest::getVar('manager_text_sms');
		$manager_worktime = JRequest::getVar('manager_worktime');
		$include_comment = JRequest::getVar('include_comment');
		$db = JFactory::getDBO();
		$sql = 'REPLACE INTO #__vm2sms (`status`,`text_sms`,`worktime`
			,`manager_text_sms`,`manager_worktime`) values ';
		foreach ($data as $status=>$text){
			$sql .= "(".$db->Quote($status).",".$db->Quote($text).",".$db->quote($worktime[$status]).",".$db->Quote($manager_data[$status]).",".$db->quote($manager_worktime[$status])."),";
		}
		$sql  = substr($sql,0,-1);
		$db->setQuery($sql);
		$db->query();
		$db->setQuery('UPDATE #__vm2sms set send_sms=1 where status in ("'.implode('","',$send_sms).'")');
		$db->query();
		$db->setQuery('UPDATE #__vm2sms set include_comment=1 where status in ("'.implode('","',$include_comment).'")');
		$db->query();
		$db->setQuery('UPDATE #__vm2sms set manager_send_sms=1 where status in ("'.implode('","',$manager_send_sms).'")');
		$db->query();
		JFactory::getApplication()->redirect('index.php?option=com_vm2sms');

	}

}
