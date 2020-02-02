<?php
/**
 * @version		$Id: contact.php 21555 2011-06-17 14:39:03Z chdemko $
 * @package		Joomla.Site
 * @subpackage	Contact
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class JControllerNumbering extends JControllerBase
{
     function getViewName() 
	{ 
		return 'numbering';		
	} 

   function getModelName() 
	{		
		return 'numbering';
	}
	function apply()
	{
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'numbering.php'); 
		$this->checkDBS(); 
		
		$data = JRequest::get('post'); 
		foreach ($data['nformat'] as $key=>$val)
		{
			$id = (int)$key; 
			$format = $val; 
			$depends = (int)$data['dependson'][$key]; 
			$reseton = (int)$data['reseton'][$key]; 
			$name = (string)$data['aname'][$key]; 
			$next_ai = (int)$data['nextai'][$key]; 
			
			$created = null; 
		
			$ai = 0; 
			if ($id === 0)
			$numbering = OPCNumbering::getNext($id, 1, -1, $created, $ai);
			else
			if ($id === 1)
			$numbering = OPCNumbering::getNext($id, 2, -1, $created, $ai);
			else
			if ($id > 1)
			$numbering = OPCNumbering::getNext($id, 3, -1, $created, $ai);
			
			$changed = 0; 
			
			if (($next_ai > $ai) || ($next_ai < $ai))
			{
				$ai = $next_ai; 
				
				if ($id === 0)
			$numbering = OPCNumbering::getNext($id, 1, -1, $created, $ai);
			else
			if ($id === 1)
			$numbering = OPCNumbering::getNext($id, 2, -1, $created, $ai);
			else
			if ($id > 1)
			$numbering = OPCNumbering::getNext($id, 3, -1, $created, $ai);
			
			/*
			OPCNumbering::$debug = true; 
			$ai = 0; 
			$numbering = OPCNumbering::getNext($id, 2, -1, $created, $ai);		
			*/
			
		
				
				
				$changed = time(); 
			}
			
		
		
			$this->updateconfig($id, $format, $depends, $reseton, $name, $changed); 
		}
		
		$this->setRedirect('index.php?option=com_onepage&view=numbering');
	}
	private function checkDBS() {
	   $q = 'select * from #__onepage_agendas where 1 limit 0,1'; 
	   $db = JFactory::getDBO(); 
	   $db->setQuery($q); 
	   $result = $db->loadAssoc(); 
	   
	   if ((!empty($result)) && (!isset($result['changed']))) {
		   $q = 'ALTER TABLE `#__onepage_agendas` ADD `changed` INT(11) NOT NULL DEFAULT \'0\' AFTER `name`, ADD INDEX (`changed`);'; 
		   $db->setQuery($q); 
		   $db->query(); 
	   }
	}
	private function updateConfig($id, $format, $depends, $reseton, $name, $changed=0)
	{
		$db = JFactory::getDBO(); 
		if (!empty($id))
		{
			$q = "update `#__onepage_agendas` set `format` =  '".$db->escape($format)."', `depends` = ".(int)$depends.", `reseton` = ".(int)$reseton.", `name`='".$db->escape($name)."' "; 
			if (!empty($changed)) {
			   $q .= ", `changed` = ".(int)$changed; 
			}
			$q .= " where `id` = ".(int)$id;
			
			
		}
		else
		{
			if (empty($changed))
			$changed = time(); 
			$q = 'insert into `#__onepage_agendas` (`id`, `format`, `depends`, `reseton`, `name`, `changed`'; 
			$q .= ') values ('; 
			$q .= "NULL, '".$db->escape($format)."', ".(int)$depends.", ".(int)$reseton.", '".$db->escape($name)."', ".(int)$changed; 
			$q .= "')"; 
		}
		
		
		$db->setQuery($q); 
		$db->query(); 
		$e = $db->getErrorMsg(); if (!empty($e)) {
		  JFactory::getApplication()->enqueueMessage($e); 
		  return; 
		}
	}
	
	public function save()
	{
		$this->apply(); 
	}
	public function proceed()
	{

		// Check for request forgeries.
		return;

		// Initialise variables.
		$app	= JFactory::getApplication();
		$model	= $this->getModel('default');
		$msg = $model->proceed(); 
		// Redirect back to the contact form.
		$lang_name = JRequest::getVar('selected_lang', ''); 
		$lang_code = JRequest::getVar('selected_code', ''); 
		if (empty($lang_name) || (empty($lang_code)))
		$this->setRedirect(JRoute::_('index.php?option=com_vmtranslator&view=default&', false, 'Error'));
		else
		$this->setRedirect(JRoute::_('index.php?option=com_vmtranslator&view=edit&', false));
		return false;
	}

	
}
