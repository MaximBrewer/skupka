<?php
/**
 * @version		$Id: benchmark for Joomla 1.5
 * @copyright	Copyright (C) 2006 - 2009 Ryan Demmmer. All rights reserved.
 * @license		GNU/GPL
 * Joomla Benchmark is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
defined('_JEXEC') or die ('Restricted access');
/**
 * Installer function
 * @return
 */
function com_install()
{
  
    $db = & JFactory::getDBO();

    jimport('joomla.filesystem.folder');
    jimport('joomla.filesystem.file');
	jimport('joomla.filesystem.archive');
   

    jimport('joomla.installer.installer');
    $installer = & JInstaller::getInstance();

    $source 	= $installer->getPath('source');

	$disp = JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'event'.DS.'dispatcher.php';
	$disp_bck = JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'event'.DS.'dispatcher.bench_bck.php';
	$disp_src = $source.DS.'benchmark'.DS.'dispatcher.php'; 
	  $x = file_get_contents($disp); 
	  if (strpos($x, 'bench_arr')===false)
	   {
	    // not installed
	    jimport('joomla.filesystem.file');
		if (@JFile::copy($disp, $disp_bck)!==false)
		 {
		   
		if (@JFile::copy($disp_src, $disp)===false)
		 {
		   echo 'Cannot overwrite '.$disp.'<br />'; 
		   $bench['name'] = 'Cannot overwrite: '.$disp; 
		   $bench['duration'] = 0; 
		   global $bench_arr;
		   if (!empty($bench_arr)) $bench_arr[] = $bench; 

		 }
		 }
		 else
		  echo 'Cannot create backup to '.$disp_bck.'<br />'; 
	   }

	  $disp = 	  JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'application'.DS.'component'.DS.'helper.php';
	  $disp_bck = JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'application'.DS.'component'.DS.'helper.bench_bck.php';
	  $disp_src = $source.DS.'benchmark'.DS.'helper.php'; 
	  
	  $x = file_get_contents($disp); 
	  if (strpos($x, 'bench_arr')===false)
	   {
	    // not installed
	    jimport('joomla.filesystem.file');
		if (@JFile::copy($disp, $disp_bck)!==false)
		 {
		   
		if (@JFile::copy($disp_src, $disp)===false)
		 {
		   $bench['name'] = 'Cannot overwrite: '.$disp; 
		   $bench['duration'] = 0; 
		   global $bench_arr;
		   if (!empty($bench_arr)) $bench_arr[] = $bench; 
		 }
		 }
		 else
		  echo 'Cannot create backup to '.$disp_bck.'<br />'; 

	   }	

       
}


/**
 * Uninstall function
 * @return
 */
function com_uninstall()
{
  
    $db = & JFactory::getDBO();

    jimport('joomla.filesystem.folder');
    jimport('joomla.filesystem.file');
	jimport('joomla.filesystem.archive');
   

    jimport('joomla.installer.installer');
   

	$disp = JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'event'.DS.'dispatcher.php';
	$disp_bck = JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'event'.DS.'dispatcher.bench_bck.php';

	  $x = file_get_contents($disp); 
	  if (strpos($x, 'bench_arr')!==false)
	   {
	    
	    jimport('joomla.filesystem.file');
		if (@JFile::copy($disp_bck, $disp)!==false)
		 {
			 @JFile::delete($disp_bck); 
		 }
		 else
		  echo 'Cannot restore backup from '.$disp_bck.'<br />'; 
	   }

	  $disp = 	  JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'application'.DS.'component'.DS.'helper.php';
	  $disp_bck = JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'application'.DS.'component'.DS.'helper.bench_bck.php';
	
	  
	  $x = file_get_contents($disp); 
	  if (strpos($x, 'bench_arr')!==false)
	   {
	    // not installed
	    jimport('joomla.filesystem.file');
		if (@JFile::copy($disp_bck, $disp)!==false)
		 {
		    @JFile::delete($disp_bck); 
		 }
		 else
		  echo 'Cannot restore backup from '.$disp_bck.'<br />'; 

	   }

 
    
    
}
?> 