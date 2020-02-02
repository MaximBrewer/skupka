<?php  defined ('_JEXEC') or die('Restricted access');

/**
 * pickup or delivery plugin
 * license - commercial
 * @author RuposTel.com
 *
 */
 


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
	  
jimport('joomla.form.formfield');
class JFormFieldZipranges extends JFormField
{
  protected $type = 'zipranges';
  function getInput()
	{
		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_delivery'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php')) {
			return 'Install com_delivery first !'; 
		}
		 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_delivery'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
		 
		 $dispatcher = JEventDispatcher::getInstance();
		 $plugin = JPluginHelper::importPlugin('vmshipment', 'pickup_or_free', true, $dispatcher); 
		 $pfClass = null; 
		 $dispatcher->trigger('pickup_or_free_get_class', array(&$pfClass));
		 
		 

	  if (empty($pfClass)) return 'Error loading class... '; 
	  $method =& $pfClass->methods[0]; 
	 
	  $routes = $pfClass->getRoutes($method); 
	  if (empty($routes)) {
		  return 'No routes found, configure at com_delivery !'; 
	  }
	  
	 
	  $html = ''; 
	  foreach ($routes as $key=>$route_name) {
		  $name = str_replace('[]', '['.$key.']', $this->name);
		  $html .= '<label>ZIP Range for '.$route_name.'</label>'; 
		  $html .= '<input name='.$name.' type="text" value="';
		  if ((!empty($this->value)) && (!empty($this->value[$key]))) $html .= htmlentities($this->value[$key]); 
		  $html .= '" placeholder="Input ZIP range such as 5600;7000-7100;8000" />';
		  
	  }
	  return $html; 
	  
		 
	}
}