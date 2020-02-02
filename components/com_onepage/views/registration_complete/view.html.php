<?php
/**
 * @version		$Id: view.html.php 21705 2011-06-28 21:19:50Z RuposTel.com $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of banners.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
 jimport('joomla.application.component.view');

if(!class_exists('VmView'))
{
if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vmview.php'))
require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vmview.php');
else
require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'vmview.php');
}
 
class virtuemartViewregistration_complete extends OPCView
{
    function display($tpl = null) {
	
	
	 $template = JFactory::getApplication('site')->getTemplate();
		$viewName = $this->getName(); 
		$path = JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.$viewName.DIRECTORY_SEPARATOR.'default.php'; 
	    $dir = JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.$viewName.DIRECTORY_SEPARATOR;
		if (file_exists($path)) 
		{
				if (method_exists($this, 'addTemplatePath')) {
					$this->addTemplatePath($dir);
				}
				else
				{
					if (method_exists($this, 'addIncludePath')) 
					{
					$this->addIncludePath( $dir );
					}
				}
			
		
		}
	
	require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
	
    $config = new JModelConfig(); 
	$config->loadVmConfig(); 
	
	$usersConfig = JComponentHelper::getParams( 'com_users' );
	$useractivation = $usersConfig->get( 'useractivation' );
	
	JFactory::getLanguage()->load('com_users'); 
	
	$useractivation = (int)$useractivation; 
	
	$msgtype = JRequest::getInt('msgtype'); 
	if ((!empty($msgtype)) && ($msgtype === 3)) $msg = JText::_('COM_USERS_REGISTRATION_SAVE_SUCCESS'); 
	else {
	switch ($useractivation)
	{
	   case 1: 
	    $msg = JText::_('COM_USERS_REGISTRATION_COMPLETE_ACTIVATE'); 
		break; 
	   case 2:
	    $msg = JText::_('COM_USERS_REGISTRATION_COMPLETE_VERIFY'); 
		break; 
	   default: 
	     $msg = JText::_('COM_USERS_REGISTRATION_SAVE_SUCCESS'); 

	 }
	}
	$this->assignRef('registration_msg', $msg); 
	
	parent::display(); 
	return;
}

}