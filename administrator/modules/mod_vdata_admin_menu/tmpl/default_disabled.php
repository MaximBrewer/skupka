<?php
/*------------------------------------------------------------------------
# com_vdata - HexData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined ('_JEXEC') or die ('resticted aceess');

if ($user->authorise('core.manage', 'com_vdata'))
{
	$menu->addChild(new JMenuNode(JText::_('MOD_MENU_COM_VDATA'), null, 'disabled'));
}