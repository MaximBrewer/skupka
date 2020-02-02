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
// no direct access
defined('_JEXEC') or die('Restricted access');

class OPCschema {
 public static function getSchema($schema) {
   jimport( 'joomla.filesystem.file' );
   $schema = JFile::makeSafe($schema); 
   $sfile = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'schemas'.DIRECTORY_SEPARATOR.$schema.'.php'; 
   if (file_exists($sfile)) {
	   include($sfile); 
	   
	   if (!empty($p)) 
	   return $p; 
   }
   return array(); 
 }
}