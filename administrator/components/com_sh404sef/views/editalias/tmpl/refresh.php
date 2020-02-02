<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_config
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * This layout only insert javascript to close a modal windows
 */
$displayData = new stdClass();
$displayData->document = JFactory::getDocument();
$displayData->refreshAfter = empty($this->refreshAfter) ? 1500 : $this->refreshAfter;
ShlMvcLayout_Helper::render('com_sh404sef.general.refresh_parent', $displayData);

