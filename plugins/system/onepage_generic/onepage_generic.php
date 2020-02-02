<?php
/**
** Parts of this code is written by joomlaprofessionals.com Copyright (c) 2012, 2015 All Right Reserved.
** Many part of this code is from VirtueMart Team Copyright (c) 2004 - 2015. All rights reserved.
** Some parts might even be Joomla and is Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved. 
** http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
** This source is free software. This version may have been modified pursuant
** to the GNU General Public License, and as distributed it includes or
** is derivative of works licensed under the GNU General Public License or
** other free or open source software licenses.
**
** THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY 
** KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
** IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A
** PARTICULAR PURPOSE.

** <author>Joomlaproffs / Virtuemart team</author>
** <email>info@joomlaprofessionals.com</email>
** <date>2015</date>
*/

defined('_JEXEC') or die('Restricted access');


jimport('joomla.plugin.plugin');

class plgSystemOnepage_generic extends JPlugin {
	function __construct($config,$params) {
		parent::__construct($config,$params);
		
	}

	function onBeforeCompileHead()
	{
	
	    $style = '.form-horizontal .control-label{width:250px; !important; }';
		$input = JFactory::getApplication()->input;
		$document = JFactory::getDocument();
		$document->addStyleDeclaration($style);
		
		$_option = $input->getString('option'); 
		$_view =   $input->getString('view'); 
		$_format = $input->getString('format');
		$_task =   $input->getString('task'); 	
		$_tmpl =   $input->getString('tmpl');  
	    if ($_option == 'com_virtuemart' && $_view == 'cart' && $_format != 'json') 
		{
			$document = JFactory::getDocument();
		 	$rootPath = JURI::root(true);
			$arrHead = $document->getHeadData();   
			foreach($arrHead['scripts'] as $key => $script)
			{
			  if(strpos($key, "js/vmprices.js") > 1)
			  {
			      unset($arrHead['scripts'][$key]);
			  }
	  	    }	
			$document->setHeadData($arrHead);
	    }
	}
	
	function onAfterRoute() {
		
		if(JFactory::getApplication()->isAdmin()) {
			return;
		}
	    $input = JFactory::getApplication()->input;
		$document = JFactory::getDocument();
		$app = JFactory::getApplication();
		$template = $app->getTemplate(true);
		if (!class_exists ('VmConfig')) 
		{
			require(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'config.php');
		}
		VmConfig::loadConfig();
		$uri = JFactory::getURI();
		$input = JFactory::getApplication()->input;
		$post = $input->post->getArray();
		$_option = $input->getString('option'); 
		$_view =   $input->getString('view'); 
		$_format = $input->getString('format');
		$_task =   $input->getString('task'); 	
		$_tmpl =   $input->getString('tmpl');  
	    if ($_option == 'com_virtuemart' && $_view == 'cart' && $_format != 'json') 
		{
			require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cart' . DIRECTORY_SEPARATOR . 'view.html.php');
   	    }
		else if($_option == 'com_virtuemart' && $_view == 'vmplg' && $_task == "pluginUserPaymentCancel")
		{
			  require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cart' . DIRECTORY_SEPARATOR . 'view.html.php');
		}
		else if($_option == 'com_virtuemart' && $_view == 'pluginresponse' && $_task == "pluginUserPaymentCancel")
		{
			  require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cart' . DIRECTORY_SEPARATOR . 'view.html.php');
		}
	}
}
?>
