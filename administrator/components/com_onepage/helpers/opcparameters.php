<?php
/**
 * @version		$Id: cache.php 21518 2011-06-10 21:38:12Z chdemko $
 * @package		Joomla.Administrator
 * @subpackage	com_cache
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;


if (!class_exists('FileUtilities'))
	  {
	    require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'parameterparser.php'); 
		
	  }
	  
class vmParametersOPC extends vmParameters
{

}


class OPCparameters extends vmParametersOPC
{



  function __construct($data, $element, $path, $type='opctracking')
   {
      $this->_type = $type;
	  //if (method_exists('JParameter', 'getInstance'))
	  //return JParameter::getInstance($element, $path); 
	  JParameter::__construct($element, $path);
	  $this->bind($data);
   }
  
   function getXml()
   {
     return $this->_xml; 
   }
}