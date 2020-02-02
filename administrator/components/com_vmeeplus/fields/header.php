<?php
// Check to ensure this file is within the rest of the framework
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/


class JFormFieldHeader extends JFormField {

	var	$type = 'header';

	function getInput(){
		$document = JFactory::getDocument();
		$document->addStyleSheet(JUri::root(true).'/administrator/components/com_vmeeplus/views/com_vmeeplus.css');
			
		return '<div class="paramHeaderContainer"><div class="paramHeaderContent">'.JText::_($this->value).'</div><div class="k2clr"></div></div>';
	}

	function getLabel(){
		return '';
	}
}
