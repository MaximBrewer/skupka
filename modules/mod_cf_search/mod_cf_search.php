<?php
/**
 * @version		$Id: mod_cf_search.php sakis Terz $
 * @package		customfilters
 * @subpackage	mod_cf_search
 * @copyright	Copyright (C) 2014-2018 breakdesigns.net . All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
//the basic tools class
require_once JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_customfilters'.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'tools.php';
require_once JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_customfilters'.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'input.php';
require_once JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_customfilters'.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'output.php';

$cfInputs=CfInput::getInputs();
$cfOutputs=CfOutput::getOutput($cfInputs);
$menuParams=cftools::getMenuparams();
$itemId=$menuParams->get('cf_itemid','');

//calls the layout which will be used
//template overrides can be done
require(JModuleHelper::getLayoutPath('mod_cf_search',$params->get('layout', 'default')));
