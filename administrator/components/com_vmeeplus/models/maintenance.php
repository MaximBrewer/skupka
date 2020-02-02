<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/


jimport( 'joomla.application.component.model' );

class vmeeProModelMaintenance extends JModelLegacy {
	public function importVMEmailsTemplates($templatesIdsArr){
		$bRes = false;
		if(!empty($templatesIdsArr)){
			$idsList = implode(',', $templatesIdsArr);
			$db =JFactory::getDBO();
			$sql = "SELECT id,email_subject,email_body from #__interamind_vm_emails WHERE id IN(" . $idsList . ")";
			$db->setQuery($sql);
			$templatesArr = $db->loadAssocList();
			$valuesClause = '';
			foreach ($templatesArr as $template){
				switch ($template['id']){
					case 1:
						$trigger = 'TRIGGER_USER_REGISTRATION';
						$name = JText::_('User registration');
						break;
					case 2:
						$trigger = 'TRIGGER_ORDER_CONFIRMATION';
						$name = JText::_('Order confirmation');
						break;
					case 3:
						$trigger = 'TRIGGER_ORDER_STATUS_CHANGED';
						$name = JText::_('Order status changed');
						break;
					case 4:
						//$trigger = 'TRIGGER_DOWNLOAD_ID';
						//$name = JText::_('Download ID');
						break;
					case 5:
						$trigger = 'TRIGGER_ADMIN_ORDER_CONFIRMATION';
						$name = JText::_('Admin order confirmation');
						break;
					default:
						$trigger = 'TRIGGER_ORDER_CONFIRMATION';
				}
				$valuesClause .= "('" . $db->escape($trigger) . "','vmemails-" . $db->escape($name) . "','" . $db->escape($template['email_subject']) . "','" . $db->escape($template['email_body']) . "','0'),";
			}
			$valuesClause = trim($valuesClause,',');
			$insert = "INSERT INTO #__vmee_plus_templates (trigger_id,name,subject,body,isDefault) VALUES " . $valuesClause;
			$db->setQuery($insert);
			$db->execute();
			$bRes = true;
		}
		return $bRes;
	}

	public function isVMEmailsInstalled(){
		$bRes = true;
		$db =JFactory::getDBO();
		$sql = "SHOW TABLES LIKE '#__interamind_vm_emails'";
		$db->setQuery($sql);
		$db->execute();
		$num_rows = $db->getNumRows();
		if(is_null($num_rows) || $num_rows == 0){
			$bRes = false;
		}
		return $bRes;
	}
}
