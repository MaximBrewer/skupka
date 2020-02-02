<?php

defined('_JEXEC') or die;
class pkg_vm2smsInstallerScript{
	public function postflight( $type, $parent,$result ) {
		if ($type=='install'){
			$db = JFactory::getDBO();
			$db->setQuery('UPDATE #__extensions set enabled=1 where `type`="plugin" and (
				(element="vm2sms" and folder="vmpayment"))');
			$db->query();
			$db->setQuery("SHOW COLUMNS FROM #__vm2sms LIKE 'manager_send_sms'");
			if (!$db->loadObject()){
				$db->setQuery("ALTER TABLE `#__vm2sms` ADD `manager_send_sms` INT( 1 ) NOT NULL DEFAULT '0',
	ADD `manager_text_sms` TEXT NOT NULL ,
	ADD `manager_worktime` INT( 1 ) NOT NULL DEFAULT '0'");
				$db->query();
			}
		}
		return true;
	}

}
