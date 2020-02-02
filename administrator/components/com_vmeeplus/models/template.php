<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/
 
jimport( 'joomla.application.component.model' );


class vmeeProModelTemplate extends JModelLegacy {
	
	function getTemplateData($id){
		$row = JTable::getInstance('VmeePlusTemplates', 'Table');
		$row->load( $id );
		return $row;
	}
	
	function applyTemplateDetails(){
		if(!$this->validate())
			return false;
		
		$result = false;
    	$db =JFactory::getDBO();
    	
    	$template_id = JFactory::getApplication()->input->get('template_id',null,'RAW');
    	$trigger = JFactory::getApplication()->input->get('rule_trigger',null,'RAW');
    	$trigger = $db->escape($trigger);
    	
    	$name = JFactory::getApplication()->input->get('templateName',null,'RAW');
    	$name = $db->escape($name);
    	
    	$emailSubject = JFactory::getApplication()->input->get('emailSubject',null,'RAW');
    	$emailSubject = $db->escape($emailSubject);
    	
    	$emailBody = JFactory::getApplication()->input->get( 'emailBody',null,'RAW');
    	$emailBody = $db->escape($emailBody);
    	
    	if($template_id > -1){
    		if(empty($trigger)){
    			$triggerQ = '';
    		}
    		else{
    			$triggerQ = ",trigger_id='" . $trigger . "'";
    		}
    		$q = "UPDATE #__vmee_plus_templates SET name='".$name."'" . $triggerQ . ", subject='".$emailSubject."', body='".$emailBody."' WHERE id=".$template_id;
    		$db->setQuery($q);
    		$result = $db->execute();
	    	
    	}else{
    		//trigger will alway be for new temaplates
    		$q = "INSERT INTO #__vmee_plus_templates (trigger_id, name, subject, body) VALUES ('" . $trigger . "','".$name."','".$emailSubject."','".$emailBody."')";
    		$db->setQuery($q);
    		$result = $db->execute();
    		if($result){
    			JRequest::setVar('template_id', array($db->insertid()));
    		}
    	}
		
    	return $result;
    }
    
    function saveTemplateDetails(){
    	return $this->applyTemplateDetails();
    }
    
    private function validate(){
    	$isValid = true;
    	$template_id = JFactory::getApplication()->input->get('template_id',null,'RAW');
    	if(empty($template_id)){
    		JError::raiseNotice('', JText::_('VALIDATE_TEMPLATE_NO_ID'));
    		return false;
    	}
    	$name = JFactory::getApplication()->input->get('templateName',null,'RAW');
   		if(empty($name)){
    		JError::raiseNotice('', JText::_('VALIDATE_TEMPLATE_NO_NAME'));
    		return false;
    	}
    	$emailSubject = JFactory::getApplication()->input->get('emailSubject',null,'RAW');
    	if(empty($emailSubject)){
    		JError::raiseNotice('', JText::_('VALIDATE_TEMPLATE_NO_SUBJECT'));
    		//$isValid = false;
    	}
    	$emailBody = JFactory::getApplication()->input->get('emailBody',null,'RAW');
    	if(empty($emailBody)){
    		JError::raiseNotice('', JText::_('VALIDATE_TEMPLATE_NO_BODY'));
    		//$isValid = false;
    	}
    	
    	return $isValid;
    }
    
	function delete($idArr)
	{
		$db		= JFactory::getDBO();
		$n		= count( $idArr );
		JArrayHelper::toInteger( $idArr );

		if ($n)
		{
			$query = 'DELETE FROM #__vmee_plus_templates WHERE id IN (' . implode(',', $idArr) . ')';
			$db->setQuery( $query );
			if (!$db->execute()) {
				JError::raiseWarning( 500, $db->getError() );
			}
		}
	}
}
