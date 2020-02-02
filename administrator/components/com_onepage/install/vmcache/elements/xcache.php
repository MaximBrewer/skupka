<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.form.formfield');
class JFormFieldXcache extends JFormField
{
  protected $type = 'xcache';
  function getInput()
	{
	  if ((!function_exists('xcache_isset')) && (!function_exists('apc_add '))) 
	  echo 'SHM is not Available, install Xcache or APC. If you enable this option, file cache will be used. Please keep an eye on number of files in your /cache/vmcache_urls directory. Once it reaches limit of 30 000 files, the cache may not be effective. TTL only applies to SHM based caching.'; 
	  
	  $fx = JPATH_SITE.DIRECTORY_SEPARATOR.'defines.php'; 
	  $disable = false; 
	  $installed = false; 
	  if (file_exists($fx))
	   {
	      $disable = true; 
	      $data = file_get_contents($fx); 
		  if (stripos($data, 'vmcache')!==false)
		   {
		     $disable = false; 
			 $installed = true; 
		   }
	   }
	   
	   if ($disable)
	    {
		   return JText::_('PLG_VMCACHE_FIELD_DEFINES_EXISTS'); 
		}
		//params: 
		// Get plugin parameters
$db = JFactory::getDBO();
$query = $db->getQuery(true);
$query->select('`params`')
      ->from  ('`#__extensions`')
      ->where ("`type`    = 'plugin'")
      ->where ("`folder`  = 'system'")
      ->where ("`element` = 'vmcache'");
$db->setQuery($query);
$res = $db->loadResult(); 

$ttl = 120; 
if (!empty($res))
$json = json_decode($res, true);
if (!empty($json))
if (!empty($json['xcache']))
$ttl = $json['xcache'];
	  
	  $clearcache = JRequest::getVar('clearcache', false);
	  if (!empty($clearcache))
	   {
	     if (function_exists('xcache_clear_cache'))
	     xcache_clear_cache(XC_TYPE_VAR); 
		 if (function_exists('apc_clear_cache'))
		 apc_clear_cache('user'); 
	   }
	  
	  
	  $ret = '<input type="checkbox" value="1" name="routercache" '; 
	  if ($installed) $ret .= ' checked="checked" '; 
	  $ret .= ' /> <br /><input type="text" placeholder="Xcache TTL in seconds" class="hasTip" title="Xcache TTL in seconds" name="jform[params][xcache]" value="'.$ttl.'" /><br /><a href="'; 
	  $ret .= $_SERVER['REQUEST_URI'].'&clearcache=1" style="float: left; clear: both; border: 2px solid #ddd; padding: 2px;">Clear shared memory cache</a>'; 
	  return $ret; 
	}
}

