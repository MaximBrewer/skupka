<?php
/**
 *
 * Breakdesigns Ajax system plugin
 *
 * @package		customfilters
 * @author		Sakis Terz
 * @link		http://breakdesigns.net
 * @copyright	Copyright (c) 2012-2018 breakdesigns.net. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *				customfilters is free software. This version may have been modified
 *				pursuant to the GNU General Public License, and as distributed
 *				it includes or is derivative of works licensed under the GNU
 *				General Public License or other free or open source software
 *				licenses.
 * @version $Id: breakdesigns_ajax.php 2013-01-31 12:46:00Z sakis $
 */
// No direct access.
defined('_JEXEC') or die;

Class plgSystemBreakdesignsajax extends JPlugin{

	public function onAfterDispatch(){
		$app=JFactory::getApplication();
		$doc=JFactory::getDocument();
		$format = $doc->getType();
		$buffer=trim($doc->getBuffer('component'));
        if(empty($buffer))return;
		$is_json=!empty($buffer[0]) && $buffer[0]=='{';
		$xml_pos=strpos($buffer,'<?xml');
		$is_xml=$xml_pos==0 && $xml_pos!==false;

		if($format=='html' && !$is_json && !$is_xml && !$app->isAdmin()){
			$newBuffer='
			<div id="bd_results">
			<div id="cf_res_ajax_loader"></div>'
			.$buffer.
			'</div>';
			$doc->setBuffer($newBuffer,'component');
		}
	}
}