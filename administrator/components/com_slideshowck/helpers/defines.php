<?php
/**
 * @copyright	Copyright (C) 2019. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - https://www.template-creator.com - https://www.joomlack.fr
 */

// No direct access
defined('_JEXEC') or die;

// set variables
define('SLIDESHOWCK_PLATFORM', 'joomla');
define('SLIDESHOWCK_PATH', JPATH_SITE . '/administrator/components/com_slideshowck');
define('SLIDESHOWCK_ADMIN_PATH', SLIDESHOWCK_PATH);
define('SLIDESHOWCK_FRONT_PATH', JPATH_SITE . '/components/com_slideshowck');
define('SLIDESHOWCK_PROJECTS_PATH', JPATH_SITE . '/administrator/components/com_slideshowck/projects');
define('SLIDESHOWCK_ADMIN_URL', JUri::root(true) . '/administrator/index.php?option=com_slideshowck');
define('SLIDESHOWCK_URL', JUri::base(true) . '/index.php?option=com_slideshowck');
define('SLIDESHOWCK_ADMIN_GENERAL_URL', JUri::root(true) . '/administrator/index.php?option=com_slideshowck&view=templates');
define('SLIDESHOWCK_MEDIA_URI', JUri::root(true) . '/media/com_slideshowck');
define('SLIDESHOWCK_MEDIA_URL', SLIDESHOWCK_MEDIA_URI);
define('SLIDESHOWCK_MEDIA_PATH', JPATH_ROOT . '/media/com_slideshowck');
define('SLIDESHOWCK_PLUGIN_URL', SLIDESHOWCK_MEDIA_URI);
define('SLIDESHOWCK_TEMPLATES_PATH', JPATH_SITE . '/templates');
define('SLIDESHOWCK_SITE_ROOT', JPATH_ROOT);
define('SLIDESHOWCK_URI', JUri::root(true) . '/administrator/components/com_slideshowck');
define('SLIDESHOWCK_URI_ROOT', JUri::root(true));
define('SLIDESHOWCK_URI_BASE', JUri::base(true));
define('SLIDESHOWCK_PLUGINS_PATH', JPATH_SITE . '/plugins/slideshowck');

// include the classes
require_once SLIDESHOWCK_PATH . '/helpers/ckinput.php';
require_once SLIDESHOWCK_PATH . '/helpers/cktext.php';
require_once SLIDESHOWCK_PATH . '/helpers/ckfile.php';
require_once SLIDESHOWCK_PATH . '/helpers/ckfolder.php';
require_once SLIDESHOWCK_PATH . '/helpers/ckfof.php';
//require_once SLIDESHOWCK_PATH . '/helpers/helper.php';
//require_once SLIDESHOWCK_PATH . '/helpers/ckcontroller.php';
//require_once SLIDESHOWCK_PATH . '/helpers/ckmodel.php';
//require_once SLIDESHOWCK_PATH . '/helpers/ckview.php';