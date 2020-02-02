<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class JControllerConfig extends JControllerBase
{	
   function getViewName() 
	{ 
		return 'config';		
	} 

   function getModelName() 
	{		
		return 'config';
	}
	
	function installopcext()
	{
	  $link = 'index.php?option=com_onepage'; 
	  $msg = 'Not implemented'; 
	  $this->setRedirect($link, $msg);
	
	 
	}
	function ignoreMsg() {
		$hash = JRequest::getVar('ignhash', ''); 
		if (!empty($hash)) {
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
			$val = true; 
			OPCconfig::store('opc_ignored_messages', $hash, 0, $val); 
			
			
			
		}
		$link = 'index.php?option=com_onepage'; 
		$msg = JText::_('COM_ONEPAGE_OK'); 
	    $this->setRedirect($link, $msg);
		
	}
	function fix_db_charset() {
		$db = JFactory::getDBO(); 
		$config = JFactory::getConfig();
		$dbname = $config->get('db');
		$q = 'ALTER DATABASE `'.$db->escape($dbname).'` CHARACTER SET utf8 COLLATE utf8_unicode_ci'; 
		$db->setQuery($q); 
		$db->query(); 
		
	   $link = 'index.php?option=com_onepage'; 
	   
	   
	   
		 $msg = JText::_('COM_ONEPAGE_OK'); 
	   

	   $this->setRedirect($link, $msg);
		
	}
	
   function fix_shopfunctions() {
       $model = $this->getModel('config');
	   $model->loadVmConfig(); 
	   if ($model->isLess(3014)) {
	     $model->installShopfunctions($msg); 
	   }
	   
	   $link = 'index.php?option=com_onepage'; 
	   $data = JRequest::get('post');
	   $msg .= $model->store($data);
	   if (($msg === true) || ($msg === 1) || ($msg === '1'))
	   {
		   $msg = JText::_('COM_ONEPAGE_OK'); 
	   }

	   $this->setRedirect($link, $msg);
   }
   
   function clearfieldpaths() {
	    $model = $this->getModel('config');
	   $model->loadVmConfig(); 
	   
	   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');	  
       OPCconfig::clearConfig('vm_userfields'); 
	   
	   $link = 'index.php?option=com_onepage'; 
	   $data = JRequest::get('post');
	   $msg = ''; 
	   $msg .= $model->store($data);
	   if (($msg === true) || ($msg === 1) || ($msg === '1'))
	   {
		   $msg = JText::_('COM_ONEPAGE_OK'); 
	   }
	   
	   
	   $this->setRedirect($link, $msg);
   }
   
   
   function fix_cartfields()
   {
	    $model = $this->getModel('config');
		$model->loadVmConfig(); 
	   $msg = ''; 
			$db = JFactory::getDBO(); 
			$q = 'select * from #__virtuemart_userfields where cart = 1 and published = 1 ';   
			$db->setQuery($q); 
			$list = $db->loadAssocList(); 
			$ignore = array('tos', 'customer_note'); 
			$fields = array(); 
			$fn = array(); 
			foreach ($list as $k=>$v)
			{
				if (in_array($v['name'], $ignore))
				{
					unset($list[$k]); 
					continue; 
				}
				$fields[] = '<b>'.JText::_($v['title']).' ('.$v['name'].')</b>'; 
				$fn[] = " `name` = '".$db->escape($v['name'])."' "; 
				$fnn[] = $v['name']; 
			}
			if (!empty($fields))
			{
			$btn = '<button type="button" class="button" onclick="return fixCartFields(this);" />'.JText::_('COM_ONEPAGE_FIELD_MISCONFIGURATION_FIX').'</button>';
			
			}
			
		  
		  if (!empty($fn))
		  {
		   $q = 'update `#__virtuemart_userfields` set `cart` = 0 where `cart` = 1 and `published` = 1 and '.implode(' or ', $fn);   
		   $db->setQuery($q); 
		   $db->query(); 
		   $msg = 'OK, Updated: '.implode(', ', $fields); 
		  }
	
	  $link = 'index.php?option=com_onepage'; 
	 
	  $data = JRequest::get('post');
	  
	  if (!empty($data['per_order_rendering']))
	  {
	    foreach ($data['per_order_rendering'] as $k=>$line)
		 {
			 if (in_array($line, $fnn))
			 {
				 unset($data['per_order_rendering'][$k]); 
			 }
		   
		 }
	  }

	  
	  $msg .= $model->store($data);
	  
	  if (($msg === true) || ($msg === 1) || ($msg === '1'))
	   {
		   $msg = JText::_('COM_ONEPAGE_OK'); 
	   }

	  
	  $this->setRedirect($link, $msg);
   }
	
	function installext()
	{
	  $id = JRequest::getVar('ext_id', '-1');
	  $id = (int)$id; 
      if ($id >= 0)
       {
	       $model = $this->getModel('config');
		   $msg = ''; 
	       $ret = $model->installext($id, '', $msg);  
		   $msg .= $ret; 
	   }	   
	   
	  $link = 'index.php?option=com_onepage'; 
	 
	  $this->setRedirect($link, $msg);
	}
	
    function changelang()
	{
  
    JRequest::setVar( 'view', '[ config ]' );
    JRequest::setVar( 'layout', 'default'  );  
    
    $model = $this->getModel('config');
    $reply = $model->store();
    if ($reply===true) {
    $msg = JText::_('COM_ONEPAGE_CONFIGURATION_SAVED');
    } else { $msg = JText::_('COM_ONEPAGE_ERROR_SAVING_CONFIGURATION'); 
    }
    $link = 'index.php?option=com_onepage';
	
	$opc_lang = JRequest::getVar('opclang', ''); 
	$link .= '&opclang='.$opc_lang; 
    $this->setRedirect($link, $msg); 	      
	
	}
	
	function perlangedit()
	{
	  $model = $this->getModel('config');
	  $reply = $model->store();
	  $l = JRequest::getVar('payment_per_lang', ''); 
	  $url = 'index.php?option=com_onepage&view=payment&langcode='.$l; 
	  $this->setRedirect($url, $reply);
	}
	
		
	function removepatchusps()
	{
	  $model = $this->getModel('config');
	  $reply = $model->store();
	  
	  $reply = $model->removepatchusps();
	  
	  $url = 'index.php?option=com_onepage'; 
	  $this->setRedirect($url, $reply);
	}
	function patchusps()
	{
	  $model = $this->getModel('config');
	  $reply = $model->store();
	  $reply = $model->patchusps();
	  $url = 'index.php?option=com_onepage'; 
	  $this->setRedirect($url, $reply);
	}
	
	function langcopy()
	{
	   $model = $this->getModel('config');
	   $link = 'index.php?option=com_onepage';
	   $model->copylang(); 
	   $msg = 'OK, tables copied.';
       $this->setRedirect($link, $msg);
	  
	}
		
	function langedit()
	{
	  $from = JRequest::getVar('tr_fromlang', 'en-GB'); 
	  $to = JRequest::getVar('tr_tolang', 'en-GB'); 
	   $tr_type = JRequest::getVar('tr_type', 'site'); 
	  $xt = JRequest::getVar('tr_ext_'.$tr_type, 'com_onepage.ini'); 
	  $tr_type = JRequest::getVar('tr_type', 'site'); 
	  
	  $url = 'index.php?option=com_onepage&view=edit&tr_fromlang='.$from.'&tr_tolang='.$to.'&tr_ext='.$xt.'&tr_type='.$tr_type; 
	  
	  $this->setRedirect($url);
	  
	}
	
    
    
   

   function checkPerm() {
	   
      $isroot = $user->authorise('core.admin');	
	  if (!$isroot) {
	    $msg = JText::_('COM_ONEPAGE_PERMISSION_DENIED'); 
		JFactory::getApplication()->enqueueMessage($msg); 
		return false; 
	  }
	  return true; 
   }


   
    
   

   
	function apply()
	{
		return $this->save(); 
	}
	
	function rename_theme()
	{
	  JRequest::setVar('orig_selected_template', JRequest::getVar('selected_template')); 
	  JRequest::setVar('selected_template', JRequest::getVar('selected_template').'_custom'); 
	  JRequest::setVar('rename_to_custom', true); 
	  return $this->save(); 
	}
   function save()  // <-- edit, add, delete 
  {
    
    JRequest::setVar( 'view', '[ config ]' );
    JRequest::setVar( 'layout', 'default'  );  
    
    $model = $this->getModel('config');
    $reply = $model->store();
    if ($reply===true) {
    $msg = JText::_('COM_ONEPAGE_CONFIGURATION_SAVED');
    } else { $msg = JText::_('COM_ONEPAGE_ERROR_SAVING_CONFIGURATION'); 
    }
    $link = 'index.php?option=com_onepage';
	
	$opc_lang = JRequest::getVar('opclang', ''); 
	$link .= '&opclang='.$opc_lang; 
	
	/*
	$y = JFactory::getApplication()->get('_messageQueue', array());
	
	

	
	  $x = JFactory::getApplication()->set('messageQueue', array()); 
			$x = JFactory::getApplication()->set('_messageQueue', array()); 
			$session = JFactory::getSession();
            $sessionQueue = $session->set('application.queue', array());
	*/
	
    $this->setRedirect($link, $msg); 
  }

}
