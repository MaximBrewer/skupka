<?php
/**
 * @version		$Id$
 * @author		Pham Minh Tuan (admin@extstore.com)
 * @package		Joomla.Site
 * @subpakage	Skyline.AdvPoll
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die();

// include the syndicate functions only once
require_once(dirname(__FILE__) . '/helper.php');

$item		= modSL_AdvPollHelper::getItem($params);
$class_sfx	= htmlspecialchars($params->get('class_sfx'));

if ($item) {
    require(JModuleHelper::getLayoutPath('mod_sl_advpoll', $params->get('layout', 'default')));
}

