<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

abstract class vmeePlusHelper {

	public static function addSubmenu($submenu) {
		JSubMenuHelper::addEntry(JText::_('EMAIL_TEMPLATES'), 'index.php?option=com_vmeeplus&controller=templateList', $submenu == 'templateList');
		JSubMenuHelper::addEntry(JText::_('RULE_LIST'), 'index.php?option=com_vmeeplus&controller=ruleList', $submenu == 'ruleList');
//		JSubMenuHelper::addEntry(JText::_('MAINTENANCE'), 'index.php?option=com_vmeeplus&controller=maintenance', $submenu == 'maintenance');
	}

}