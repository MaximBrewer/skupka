<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* GeoLocator is free software released under GNU/GPL  This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* This php file was created by www.rupostel.com team
*/

	defined( '_JEXEC' ) or die( 'Restricted access' );
	jimport('joomla.application.component.view');
	class defaultViewDefault extends JViewLegacy
	{
		function display($tpl = null)
		{	
			@header('Content-Type: text/html; charset=utf-8');
			@header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			@header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

		    $from = JRequest::getInt('from', 0); 
		    $to = JRequest::getInt('to'); 
		    $url = JRequest::getVar('durl', ''); 
			$step = JRequest::getInt('step', 0); 
		    //echo $from.$to.$url;		 
			//$model = &$this->getModel();
			$model = $this->getModel('default');
			//echo $from.' '.$to; 
			ob_start(); 
			//if ($from > 0) die();
			if ($step == 0)
			if (!$model->download($url))
			 {
			   echo 'Cannot download and save file from specified URL to tmp directory of Joomla!'; 
			 }
			else echo '<div id="result_ok"></div>'; 
			if ($step == 1)
			if (!$model->extract($url))
			 {
			   echo 'Cannot extract CSV file!'; 
			 }
			else echo '<div id="result_ok"></div>'; 
			if ($step == 2)
			 {
			   $x = $model->insert($from, $to); 
			   if ($x === true)
			   echo '<div id="result_ok"></div>'; 
			   else if ($x === false)
			   echo '<div class="error_here">Cannnot find CSV file</div>';
			   else if ($x === -3)
			   echo '<div class="finished_rows">finished</div>';
			 }
			else
			if ($step == 3)
			 {
			   $model->clean($url);
			   echo '<div class="finished_here"></div>';
			 }
			 $x = ob_get_clean(); 
			 echo $x; 
			$mainframe = JFactory::getApplication();
			$mainframe->close(); 
		}
	}
