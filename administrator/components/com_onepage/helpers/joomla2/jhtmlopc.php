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



class JHTMLOPC extends JHtml
{
 //public static function stylesheet($file, $path, $option=array())
 public static function stylesheet($file, $attribs = array(), $relative = false, $path_only = false, $detect_browser = true, $detect_debug = true)
  {
   // Need to adjust for the change in API from 1.5 to 1.6.
		// Function stylesheet($filename, $path = 'media/system/css/', $attribs = array())
		if (is_string($attribs))
		{
			JLog::add('The used parameter set in JHtml::stylesheet() is deprecated.', JLog::WARNING, 'deprecated');
			// Assume this was the old $path variable.
			$file = $attribs . $file;
		}

		if (is_array($relative))
		{
			// Assume this was the old $attribs variable.
			$attribs = $relative;
			$relative = false;
		}

		$includes = self::includeRelativeFiles('css', $file, $relative, $detect_browser, $detect_debug);

		// If only path is required
		if ($path_only)
		{
			if (count($includes) == 0)
			{
				return null;
			}
			elseif (count($includes) == 1)
			{
				return $includes[0];
			}
			else
			{
				return $includes;
			}
		}
		// If inclusion is required
		else
		{
		if (!defined('OPCVERSION'))
    include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'version.php'); 
	if (defined('OPCVERSION'))
	{
	 $v = str_replace('.', 'Z', OPCVERSION); 
	}
			$document = JFactory::getDocument();
			foreach ($includes as $include)
			{
			    $include = $include.'?opcver='.$v; 
				$document->addStylesheet($include, 'text/css', null, $attribs);
			}
		}
  }
  //public static function script($file, $path, $mootools=false)
  public static function script($file, $framework = false, $relative = false, $path_only = false, $detect_browser = true, $detect_debug = true)
  {
  
   // Need to adjust for the change in API from 1.5 to 1.6.
		// function script($filename, $path = 'media/system/js/', $mootools = true)
		if (is_string($framework))
		{
			//JLog::add('The used parameter set in JHtml::script() is deprecated.', JLog::WARNING, 'deprecated');
			// Assume this was the old $path variable.
			$file = $framework . $file;
			$framework = $relative;
		}

		// Include MooTools framework
		if ($framework)
		{
			JHtml::_('behavior.framework');
		}

		$includes = self::includeRelativeFiles('js', $file, $relative, $detect_browser, $detect_debug);
		
		// If only path is required
		if ($path_only)
		{
			if (count($includes) == 0)
			{
				return null;
			}
			elseif (count($includes) == 1)
			{
				return $includes[0];
			}
			else
			{
				return $includes;
			}
		}
		// If inclusion is required
		else
		{
	
	if (!defined('OPCVERSION'))
    include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'version.php'); 
	if (defined('OPCVERSION'))
	{
	 $v = str_replace('.', 'Z', OPCVERSION); 
	 
	}
		
		
			$document = JFactory::getDocument();
			foreach ($includes as $include)
			{
			    $include = $include.'?opcver='.$v; 
				
				$document->addScript($include);
			}
		}
  
   
	
   
  }
  
   public static function getFullUrl($file, $path)
  {
     $path = str_replace('administrator/', '', $path); 
	 $url = Juri::base(); 
	 
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