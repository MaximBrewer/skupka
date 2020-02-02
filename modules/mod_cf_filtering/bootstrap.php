<?php
/**
 * @package		customfilters
 * @subpackage	mod_cf_filtering
 * @copyright	Copyright (C) 2012-2018 breakdesigns.net . All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

jimport('joomla.language.helper');

// Include the syndicate functions only once
require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_customfilters' . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'tools.php';
require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_customfilters' . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'Config.php';
require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_customfilters' . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'input.php';
require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_customfilters' . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'output.php';
// get the helper classes
require_once dirname(__FILE__) . '/helper.php';
require_once dirname(__FILE__) . '/DisplayManager.php';
require_once dirname(__FILE__) . '/optionsHelper.php';
require_once dirname(__FILE__) . '/renderHelper.php';
require_once dirname(__FILE__) . '/UrlHandler.php';

// load the Virtuemart configuration
require_once (JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'config.php');