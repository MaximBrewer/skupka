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

class JControllerEdittheme extends JControllerBase
{
     function getViewName() 
	{ 
		return 'edittheme';		
	} 

   function getModelName() 
	{		
		return 'edittheme';
	}
	function ajax()
	{
		$x = @ob_get_clean();$x = @ob_get_clean();$x = @ob_get_clean();$x = @ob_get_clean();$x = @ob_get_clean();
		ob_start(); 
		$model = $this->getModel('edittheme');
		
		$command = JRequest::getCmd('command'); 
		
		if ($command == 'editcss')
		{
			
			$model = $this->getModel('edittheme');
			$model->updateColors(); 
			
			$file = JRequest::getCmd('file'); 
			$files = $model->getCss(); 
			foreach ($files as $f)
			{
			 if (md5($f)==$file) 
			 {
			 $myfile = $f; 
			 break; 
			  }
			}
			if (!empty($myfile))
			{
			  $myfile2 = strtolower($myfile);
			  if (substr($myfile2, -4)!='.css') return; 
		      echo file_get_contents($myfile); 
			}

			
		}
		
		if ($command == 'savecss')
		{
			 
			$file = JRequest::getCmd('file'); 
			$files = $model->getCss(); 
			foreach ($files as $f)
			{
		     
			 if (md5($f)==$file) 
			 {
			 $myfile = $f; 
			 break; 
			  }
			}
			
			if (!empty($myfile))
			{
			  $myfile2 = strtolower($myfile);
			  if (substr($myfile2, -4)!='.css') return; 
			  {
				 
			     //echo file_get_contents($myfile); 
				  //$html = JRequest::getVar('html', JText::_('COM_VIRTUEMART_ORDER_PROCESSED'), 'default', 'STRING', JREQUEST_ALLOWRAW);
				 $css = JRequest::getVar('css', '', 'post', 'STRING', JREQUEST_ALLOWRAW);
				 if (!empty($css))
				 {
					 $css = str_replace("\r\r\n", "\r\n", $css); 
					 $css = str_replace("\xEF\xBB\xBF", "", $css); 
					 //echo $css; die(); 
					 JFile::write($myfile, $css); 
					 echo 'OPC_OK'; 
				 }
			  }
			}
		}
		
		if (($command == 'preview') || ($command == 'savepreview'))
		{
		
			$model = $this->getModel('edittheme');
			$model->updateColors(); 
		}
		
		if ($command == 'savepreview')
		{
			$model->createCustom(); 
			
		}
		JFactory::getApplication()->close(); 
		
	}

	
}
