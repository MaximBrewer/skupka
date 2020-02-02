<?php
/**
 *
 * Customfilters entry point
 *
 * @package		customfilters
 * @author		Sakis Terz
 * @link		http://breakdesigns.net
 * @copyright	Copyright (c) 2012-2018 breakdesigns.net. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include dependencies
jimport('joomla.application.component.controller');
if(!defined('JPATH_VM_ADMIN')) define('JPATH_VM_ADMIN',JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart');
require_once(JPATH_VM_ADMIN.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
VmConfig::loadConfig();

if(!defined('JPATH_VM_SITE')) define('JPATH_VM_SITE',JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart');
if(!defined('JPATH_CF_MODULE')) define('JPATH_CF_MODULE',JPATH_ROOT.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'mod_cf_filtering');
require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'tools.php';
require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'Config.php';
require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'input.php';
require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'output.php';
require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'search.php';
require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'Products'.DIRECTORY_SEPARATOR.'ProductsQueryBuilder.php';

$input=JFactory::getApplication()->input;
$controller = JControllerLegacy::getInstance('Customfilters');
$controller->execute($input->get('task','display','cmd'));
$controller->redirect();
