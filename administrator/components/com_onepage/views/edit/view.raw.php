<?php
/**
 * @version		$Id: view.html.php 21705 2011-06-28 21:19:50Z dextercowley $
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
class JViewEdit extends OPCView
{
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$sep = '>><<';
		$command = JRequest::getVar('command', ''); 
		
		$model = &$this->getModel();
		if ($command == 'update')
		{
	    
		
		
	    $ret = $model->updateT();
		
	    echo 'hash'.md5(JRequest::getVar('translation_var', '').'_span').$sep; 
	    if ($ret === true)
	    echo JText::_('COM_ONEPAGE_OK_UPDATED_IN_DB'); 
	    else
	    echo JText::_('COM_ONEPAGE_NOT_UPDATED'); 
	    }
		else
		if ($command == 'editcss')
		{
		   $file = JRequest::getVar('file', ''); 
		  jimport( 'joomla.filesystem.folder' );
		  jimport( 'joomla.filesystem.file' );
		  
		  if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'))
		  include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php');
		  $files = JFolder::files(JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$selected_template, 'css', 1, true, array('.svn', 'CVS')); 
		  foreach ($files as $f)
		  if (md5($f) == $file)
		   {
		     @header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		     @header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
			 // we won't set css here because of overrides in various hostings with mod_expire and similar
			 @header("Content-Type: text/html; charset=UTF-8"); 
		      echo file_get_contents($f); 
			  
		   }
		  		   
		}
	    else
	    {
		
		 ob_start(); 
		 
	      $link = $model->generatefile(); 
		 
		 $buf = ob_get_clean(); 
	     
		 echo $buf;
		
		
	    }
		$app	= JFactory::getApplication();
		 $app->close(); 
		//parent::display($tpl);
	}

}
