<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

// Include CSS and JS
JHtml::_('jquery.framework');
JHtml::_('script', 'com_sl_advpoll/script.js', array(), true);
JHtml::_('stylesheet', 'com_sl_advpoll/style.css', array(), true);
JHtml::_('script', 'com_sl_advpoll/jquery.fancybox.js', array(), true);
JHtml::_('stylesheet', 'com_sl_advpoll/jquery.fancybox.css', array(), true);

$controller	= JControllerLegacy::getInstance('SL_AdvPoll');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
