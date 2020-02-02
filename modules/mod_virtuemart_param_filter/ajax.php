<?php
define('_JEXEC', 1);
/**
* Param Filter: Virtuemart 2 search module
* Version: 3.0.6 (2015.11.23)
* Author: Dmitriy Usov
* Copyright: Copyright (C) 2012-2015 usovdm
* License GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* http://myext.eu
**/
define('DS', DIRECTORY_SEPARATOR);
$path = str_replace(DS.'modules'.DS.'mod_virtuemart_param_filter','',dirname(__FILE__));
$path = empty($path) ? DS : $path;
define('JPATH_BASE', $path);
header('Content-Type: text/html; charset=utf-8');
require_once(JPATH_BASE.DS.'includes'.DS.'defines.php');
require_once(JPATH_BASE.DS.'includes'.DS.'framework.php');

// Делать проверку на версию джумлы
// JFactory::getApplication('site')->initialise();

//	{ +	initialise imitation
	$app = JFactory::getApplication('site');
	$user = JFactory::getUser();
	$config = JFactory::getConfig();
	$lang = isset($_REQUEST['lang']) ? JFilterOutput::stringURLSafe($_REQUEST['lang']) : '';
	$lang = ($lang && JLanguage::exists($lang)) ? $lang : $app->input->getString('language', null);
	$lang = ($lang && JLanguage::exists($lang)) ? $lang : $app->input->cookie->get(md5($app->get('secret') . 'language'), null, 'string');
	$lang = ($lang && JLanguage::exists($lang)) ? $lang : $user->getParam('language');
	$lang = ($lang && JLanguage::exists($lang)) ? $lang : JLanguageHelper::detectLanguage();
	$params = JComponentHelper::getParams('com_languages');
	$lang = ($lang && JLanguage::exists($lang)) ? $lang : $params->get('site', $app->get('language', 'en-GB'));
	$lang = ($lang && JLanguage::exists($lang)) ? $lang : $config->get('language', 'en-GB');
	$app->set('language', $lang);
	$lang = JLanguage::getInstance($lang);
	$app->loadLanguage($lang);
	JFactory::$language = $app->getLanguage();
	// JPluginHelper::importPlugin('system');
	// $app->triggerEvent('onAfterInitialise');
//	} -	initialise imitation
	
$profiler = new JProfiler;
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
VmConfig::loadConfig();
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'vmtable.php');
require(JPATH_VM_PLUGINS.DS.'vmcustomplugin.php');
require(JPATH_BASE.DS.'plugins'.DS.'vmcustom'.DS.'param'.DS.'param.php');
$where = $PluginJoinTables = array();
JPluginHelper::importPlugin('vmcustom');
$dispatcher = JDispatcher::getInstance ();
$dispatcher->trigger ('plgVmAddToSearch', array(&$where, &$PluginJoinTables, JRequest::getInt ('custom_parent_id', 0)));
$doc = JFactory::getDocument();
$module = getModuleById(JRequest::getInt('mcf_id'));
if(!empty($module)){
	echo '<div>'.JModuleHelper::renderModule($module).'</div>';
	echo "\n".$profiler->mark( ' MCF ajax' ).'<br/>';
}
die();

function getModuleById($id) {
	$user = JFactory::getUser();
	$db = JFactory::getDBO();
	if($user->get('guest',0)){
		$aid = 1;
	}else{
		$authLevels = JAccess::getAuthorisedViewLevels($user->get('id'));
		$aid = max($authLevels);
	}
	$q = 'SELECT id, title, module, position, content, showtitle, params FROM #__modules AS m WHERE `id` = "'.$id.'" AND m.published = "1" AND `access` <= '.$aid;
	$db->setQuery($q);
	$module = $db->loadObject();
	if(empty($module)){
		return false;
	}
	$file = $module->module;
	$custom = substr( $file, 0, 4 ) == 'mod_' ?  0 : 1;
	$module->user = $custom;
	$module->name = $custom ? $module->title : substr( $file, 4 );
	$module->style = null;
	$module->position = strtolower($module->position);
	return $module;
}