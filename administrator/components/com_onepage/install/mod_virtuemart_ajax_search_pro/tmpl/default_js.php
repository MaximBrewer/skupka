<?php
/**
* @package mod_vm_ajax_search_pro
*
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
* VM Live Product Search is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();


$results_width = $params->get('results_width');

if (empty($min_height)) $min_height = '40'; 
if (empty($results_width)) $results_width = '200px';
else $results_width .= 'px'; 


$root = Juri::base(); 
if (substr($root, -1) !== '/') $root .= '/'; 
$loading_img = $root.'modules/mod_virtuemart_ajax_search_pro/css/loadingcircle.gif'; 


if (!defined('search_timer2'))
{
 // init only once per all modules
 
 $document->addScript(JURI::Base().'modules/mod_virtuemart_ajax_search_pro/js/vmajaxsearch.js', 'text/javascript', true, true); 
 $document->addScript(JURI::Base().'modules/mod_virtuemart_ajax_search_pro/js/keepalivedefaults.js', 'text/javascript', true, true); 
 $component_area_id = $params->get('component_area_id', ''); 
 $js1 = ' 
          var search_timer = new Array(); 
		  var search_has_focus = new Array(); 
		  var op_active_el = null;
		  var op_active_row = null;
          var op_active_row_n = parseInt("0");
		  var ok=true; 
		  var op_last_request = ""; 
          var op_process_cmd = "href"; 
		  var op_controller = ""; 
		  var op_lastquery = "";
		  var op_maxrows = '.$prods.'; 
		  var op_lastinputid = "vm_ajax_search_search_str2'.$myid.'";
		  var op_currentlang = "'.$clang.'";
		  var op_lastmyid = "'.$myid.'"; 
		  var op_ajaxurl = "'.$url.'";
		  var op_leftID = "'.$params->get('left_id').'"; 
		  var op_order_by = "'.$params->get('order_by').'";
		  var op_resize_component = "'.$params->get('resize_component').'"; 
		  var op_ajax_debug = "'.$params->get('debug', false).'"; 
		  var op_componentID = "'.$component_area_id.'"; 
		  var op_rightID = "'.$params->get('right_id').'"; 
		  var op_childhandling = "'.$child_handling.'";
		  var op_min_chars = "'.$params->get('min_chars', 0).'";
		  var op_savedtext = new Array(); 
		  var op_results_width = "'.$results_width.'"; 
		  var op_cat_search = "'.$params->get('cat_search', 0).'"; 
		  var op_loaderimg = "'.$loading_img.'"; '; 
		  
		  
		  
		  
		  if (!empty($component_area_id))
		  {
		  $js1 .= '	
		  var ajax_options = \'&option=com_rupsearch&view=search&format=opchtml&nosef=1&tmpl=component&op_childhandling='.$child_handling.'&internal_caching='.$internal_caching.'\'; '; 
		  }
		  else
		  {
		  
		  $js1 .= '	
		  var ajax_options = \'&option=com_rupsearch&view=search&format=opchtml&nosef=1&tmpl=component&op_childhandling='.$child_handling.'&internal_caching='.$internal_caching.'&layout=dropdown\'; '; 
		  
		  }
		  
		  $js1 .= '
		  
 
 '; 
 
 if (!empty($vm_cat_id)) { 
   
 }
 
}
else $js1 = ''; 

$js = $js1.'
 
  // global variable for js
  
   
   search_timer['.$myid.'] = null; 
   search_has_focus['.$myid.'] = false; 
  
  
  
   
   
  '; 
  
  $inc = true; 
  
  $cfd = JPATH_CACHE.DIRECTORY_SEPARATOR.'mod_virtuemart_ajax_search_pro'; 
  if (!empty($js1) && (is_writable($cfd)))
  {
  
  jimport( 'joomla.filesystem.file' );
  
  if (!file_exists($cfd))
  {
	  jimport( 'joomla.filesystem.folder' );
	  JFolder::create($cfd); 
  }
  
  $scriptname = md5($js).'ajax_search_'.$myid.'.js'; 
  $scriptname = JFile::makeSafe($scriptname); 
  
  $file = $cfd.DIRECTORY_SEPARATOR.$scriptname; 
  
  
  
  try {
	  if (JFile::write($file, $js)!==false) $inc = false; 
  }
  catch (Exception $e)
  {
	  $inc = true; 
  
  }
  
  
  if (!$inc)
  if (file_exists($file))
  {
	 $root = Juri::base(); 
	 if (substr($root, -1) != '/') $root .= '/'; 
	 JHtml::script($root.'cache/mod_virtuemart_ajax_search_pro/'.$scriptname); 
  }
  
  }
  
  if ($inc)
  {
	
$js = '
/* <![CDATA[ */
  // global variable for js
  '.$js.'
   
   
  
/* ]]> */
   
   
  ';  
	  
  $document->addScriptDeclaration($js); 
  }





