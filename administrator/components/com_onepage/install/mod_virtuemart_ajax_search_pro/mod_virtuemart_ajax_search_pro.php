<?php
/**
* @package mod_vm_ajax_search
*
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
* VM Live Product Search is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* Modified by rupostel.com team. 
* 
*/


// no direct access
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication(); 
$router = $app->getRouter();
$sef_mode = $router->getMode(); 
$router->setMode(0);

// prepare CSS includes: 
if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_rupsearch'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php'))
{


  //includes virtuemart
  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_rupsearch'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php'); 
  RupHelper::getIncludes(); 
  
  
  RupHelper::renderHidden(); 
  
  
 



/* load language */ 



if (empty($module->id)) {
	$q = 'select `id` from #__modules where `module` = \'mod_virtuemart_ajax_search_pro\' order by `published` desc limit 1'; 
	$db = JFactory::getDBO(); 
	$db->setQuery($q); 
	$id = (int)$db->loadResult(); 
	$module->id = $id; 
}

$clang = RupHelper::loadLangFiles(); 
$params = RupHelper::getParams($module->id); 


if ((!empty($module)) && (!empty($params))) {
	
	
	$module->params = $params; 
	
	foreach ($module->params as $key=>$v) {
		$params->set($key, $v); 
	}
	
}	


$prods = $params->get('number_of_products'); 
$myid = $module->id;
// we start with zero
if (empty($prods)) $prods = 4; 
$url = JURI::base().'index.php'; 
$child_handling = $params->get('child_products', 0); 
$internal_caching = $params->get('internal_caching', 0); 
$my_itemid = RupHelper::getModuleItemid($params->get('my_itemid',0)); 
$action_url = 'index.php?option=com_rupsearch&view=search&layout=default&nosef=1'; 
$my_itemid = (int)$my_itemid; 
if (!empty($my_itemid))
{
	$action_url .= '&Itemid='.$my_itemid; 
}
if (!empty($clang))
$action_url .= '&lang='.$clang; 
$action_url = JRoute::_($action_url); 
$vm_cat_id = $params->get('virtuemart_category_id', JRequest::getVar('virtuemart_category_id', JRequest::getVar('vm_cat_id', 0, 'get', 'INT') , 'get', 'INT')); 

$vm_cat_id = (int)$vm_cat_id; 
$cat_s = $params->get('cat_search', false); 

$search = JText::_('COM_VIRTUEMART_SEARCH');

if ((!empty($cat_s))) {
	
	$only_current = $params->get('only_current', false); 
	if (empty($only_current)) {
	$top_cats = array(); 
	$cat_ids_o = $params->get('category_list'); 
	if (!empty($cat_ids_o)) {
	  $cat_ids_oA = explode(',', $cat_ids_o); 
	  if (!empty($cat_ids_oA)) $top_cats = $cat_ids_oA;
	}
	
	$category_name = ''; 
	RupHelper::getToCats($top_cats, $category_name); 
 
	 if (!empty($category_name)) { 
	    $text = JText::_('MOD_VIRTUEMART_AJAX_SEARCH_PRO_SEARCH_IN'); 
		
		$search = str_replace('{category_name}', $category_name, $text); 
		//MOD_VIRTUEMART_AJAX_SEARCH_PRO_SEARCH_IN_CURRENT
		$text = JText::_('MOD_VIRTUEMART_AJAX_SEARCH_PRO_SEARCH_IN_CURRENT'); 
		$search_dropdown = str_replace('{category_name}', $category_name, $text); 
	 }
	}
	else {
		
		$top_cats = array($vm_cat_id); 
		$category_name = ''; 
		RupHelper::setVMLANG(); 
		$db = JFactory::getDBO(); 
		$q = 'select l.`category_name` from `#__virtuemart_categories_'.VMLANG.'` as l where l.`virtuemart_category_id` = '.(int)$vm_cat_id; 
		$db->setQuery($q); 
		$category_name = $db->loadResult(); 
 
	 if (!empty($category_name)) { 
	    $text = JText::_('MOD_VIRTUEMART_AJAX_SEARCH_PRO_SEARCH_IN'); 
		
		$search = str_replace('{category_name}', $category_name, $text); 
		//MOD_VIRTUEMART_AJAX_SEARCH_PRO_SEARCH_IN_CURRENT
		$text = JText::_('MOD_VIRTUEMART_AJAX_SEARCH_PRO_SEARCH_IN_CURRENT'); 
		$search_dropdown = str_replace('{category_name}', $category_name, $text); 
	 }
	}
}
else
{
	$vm_cat_id = 0; 
}

$myid = $myid.$params->get('id_suffix', ''); 

$default_layout = $params->get('custom_layout', 'default'); 
$path = JModuleHelper::getLayoutPath('mod_virtuemart_ajax_search_pro', $default_layout.'_js'); 
require($path);

$path = JModuleHelper::getLayoutPath('mod_virtuemart_ajax_search_pro', $default_layout); 
require($path);


$app = JFactory::getApplication(); 
$router = $app->getRouter();
$router->setMode($sef_mode);
}
else
{
	JFactory::getApplication()->enqueueMessage('Please install com_rupsearch!'); 
}