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




class JHTMLOPC
{
 public static function stylesheet($file, $path, $option=array())
  {
	  $loadpath = self::getFullUrl($file, $path); 
	 
	
	
	
    JHTML::stylesheet($loadpath);
  }
  
  public static function _($params, $params2='')
  {
	  if ($params === 'behavior.modal')
	  {
		  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'jhtml'.DIRECTORY_SEPARATOR.'opchtml.php'); 
		  
		  
		  $params = 'opchtml.modal'; 
	  }
	  if ($params === 'behavior.tooltip')
	  {
		  $params = 'bootstrap.tooltip'; 
		 
		  $params2 = '.hasTip'; 
	  }
	  JHTML::_($params, $params2); 
  }
  
  public static function script($file, $path, $mootools=false)
  {
	  $loadpath = self::getFullUrl($file, $path); 
   JHTML::script($loadpath, $mootools);
  }
  
  public static function getFullUrl($file, $path)
  {
     $path = str_replace('administrator/', '', $path); 
	 $url = Juri::base(); 
	 $url = str_replace('httP', 'http', $url); 
	 
	 //$url = str_replace('http:', '', $url); 
	 //$url = str_replace('https:', '', $url); 
	 
	 if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.$path.$file))
	 {
	   $url = str_replace('/administrator', '/', $url); 
	 
	 }
	 
	 if ((substr($url, -1) !== '/') && (substr($path, 0, 1) !== '/')) $url .= '/'; 
	 if ((substr($path, -1) !== '/') && (substr($file, 0, 1) !== '/')) $path .= '/'; 
	 
	 
	 	if (!defined('OPCVERSION'))
    include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'version.php'); 
	if (defined('OPCVERSION'))
	{
	 $v = str_replace('.', 'Z', OPCVERSION); 
	 
	 if (stripos($file, '?')===false)
	  {
	     $file .= '?opcver='.$v; 
	  }
	}

	 
	 return $url.$path.$file; 
  }
}