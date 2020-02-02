<?php
/**
 * @name		Maximenu CK params
 * @package		com_maximenuck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */

defined('_JEXEC') or die;
$input = new JInput();
$document = JFactory::getDocument();
// get the language direction
$langdirection = $document->getDirection();
$menubgcolor = '';
$logoimage = false;
// generate the menu items
$items = array(
	(object) array(
		'ftitle' => 'Lorem'
		, 'id' => 1
		, 'level' => 1
		, 'anchor_title' => ''
		, 'desc' => ''
		, 'params' => new JRegistry()
		, 'menu_image' => ''
		, 'classe' => ' item1 parent first'
		, 'liclass' => ''
		, 'type' => ''
		, 'anchor_css' => ''
		, 'flink' => 'javascript:void(0)'
		, 'rel' => ''
		, 'deeper' => true
		, 'shallower' => false
		, 'level_diff' => -1
		, 'is_end' => false
		, 'leftmargin' => ''
		, 'topmargin' => ''
		, 'colbgcolor' => ''
		, 'submenucontainerheight' => ''
	),
	(object) array(
		'ftitle' => 'Curabitur'
		, 'id' => 2
		, 'level' => 2
		, 'anchor_title' => ''
		, 'desc' => ''
		, 'params' => new JRegistry()
		, 'menu_image' => ''
		, 'classe' => ' item2 parent'
		, 'liclass' => ''
		, 'type' => ''
		, 'anchor_css' => ''
		, 'flink' => 'javascript:void(0)'
		, 'rel' => ''
		, 'deeper' => true
		, 'shallower' => false
		, 'level_diff' => -1
		, 'is_end' => false
		, 'leftmargin' => ''
		, 'topmargin' => ''
		, 'colbgcolor' => ''
		, 'submenucontainerheight' => ''
	),
	(object) array(
		'ftitle' => 'Elementum'
		, 'id' => 3
		, 'level' => 3
		, 'anchor_title' => ''
		, 'desc' => ''
		, 'params' => new JRegistry()
		, 'menu_image' => ''
		, 'classe' => ' item2'
		, 'liclass' => ''
		, 'type' => ''
		, 'anchor_css' => ''
		, 'flink' => 'javascript:void(0)'
		, 'rel' => ''
		, 'deeper' => false
		, 'shallower' => false
		, 'level_diff' => 0
		, 'is_end' => false
		, 'leftmargin' => ''
		, 'topmargin' => ''
		, 'colbgcolor' => ''
		, 'submenucontainerheight' => ''
	),
	(object) array(
		'ftitle' => 'Lobortis nec'
		, 'id' => 4
		, 'level' => 3
		, 'anchor_title' => ''
		, 'desc' => ''
		, 'params' => new JRegistry()
		, 'menu_image' => ''
		, 'classe' => ' item2'
		, 'liclass' => ''
		, 'type' => ''
		, 'anchor_css' => ''
		, 'flink' => 'javascript:void(0)'
		, 'rel' => ''
		, 'deeper' => false
		, 'shallower' => true
		, 'level_diff' => 1
		, 'is_end' => false
		, 'leftmargin' => ''
		, 'topmargin' => ''
		, 'colbgcolor' => ''
		, 'submenucontainerheight' => ''
	),
	(object) array(
		'ftitle' => 'Dictum nisi'
		, 'id' => 5
		, 'level' => 2
		, 'anchor_title' => ''
		, 'desc' => ''
		, 'params' => new JRegistry()
		, 'menu_image' => ''
		, 'classe' => ' item3'
		, 'liclass' => ''
		, 'type' => ''
		, 'anchor_css' => ''
		, 'flink' => 'javascript:void(0)'
		, 'rel' => ''
		, 'deeper' => false
		, 'shallower' => false
		, 'level_diff' => 0
		, 'is_end' => false
		, 'leftmargin' => ''
		, 'topmargin' => ''
		, 'colbgcolor' => ''
		, 'submenucontainerheight' => ''
	),
	(object) array(
		'ftitle' => 'Semper orci'
		, 'id' => 6
		, 'level' => 2
		, 'anchor_title' => ''
		, 'desc' => ''
		, 'params' => new JRegistry()
		, 'menu_image' => ''
		, 'classe' => ' item4'
		, 'liclass' => ''
		, 'type' => ''
		, 'anchor_css' => ''
		, 'flink' => 'javascript:void(0)'
		, 'rel' => ''
		, 'deeper' => false
		, 'shallower' => true
		, 'level_diff' => 1
		, 'is_end' => false
		, 'leftmargin' => ''
		, 'topmargin' => ''
		, 'colbgcolor' => ''
		, 'submenucontainerheight' => ''
	),
	(object) array(
		'ftitle' => 'Ipsum'
		, 'id' => 7
		, 'level' => 1
		, 'anchor_title' => ''
		, 'desc' => ''
		, 'params' => new JRegistry()
		, 'menu_image' => ''
		, 'classe' => ' item5'
		, 'liclass' => ''
		, 'type' => ''
		, 'anchor_css' => ''
		, 'flink' => 'javascript:void(0)'
		, 'rel' => ''
		, 'deeper' => false
		, 'shallower' => false
		, 'level_diff' => 0
		, 'is_end' => false
		, 'leftmargin' => ''
		, 'topmargin' => ''
		, 'colbgcolor' => ''
		, 'submenucontainerheight' => ''
	),
	(object) array(
		'ftitle' => 'Dolor sit'
		, 'id' => 8
		, 'level' => 1
		, 'anchor_title' => ''
		, 'desc' => ''
		, 'params' => new JRegistry('{"maximenu_icon":"fa fa-plane"}')
		, 'menu_image' => ''
		, 'classe' => ' item6 parent'
		, 'liclass' => ''
		, 'type' => ''
		, 'anchor_css' => ''
		, 'flink' => 'javascript:void(0)'
		, 'rel' => ''
		, 'deeper' => true
		, 'shallower' => false
		, 'level_diff' => -1
		, 'is_end' => false
		, 'leftmargin' => ''
		, 'topmargin' => ''
		, 'colbgcolor' => ''
		, 'submenucontainerheight' => ''
		, 'submenuswidth' => 400
		, 'nextcolumnwidth' => '50%'
	),
	(object) array(
		'ftitle' => 'Column 1'
		, 'id' => 9
		, 'level' => 2
		, 'anchor_title' => ''
		, 'desc' => ''
		, 'params' => new JRegistry()
		, 'menu_image' => ''
		, 'classe' => ' item7 headingck'
		, 'liclass' => ''
		, 'type' => 'separator'
		, 'anchor_css' => ''
		, 'flink' => 'javascript:void(0)'
		, 'rel' => ''
		, 'deeper' => false
		, 'shallower' => false
		, 'level_diff' => 0
		, 'is_end' => false
		, 'leftmargin' => ''
		, 'topmargin' => ''
		, 'colbgcolor' => ''
		, 'submenucontainerheight' => ''
		, 'columnwidth' => '50%'
		, 'colonne' => true
	),
	(object) array(
		'ftitle' => 'Cras massa'
		, 'id' => 10
		, 'level' => 2
		, 'anchor_title' => ''
		, 'desc' => ''
		, 'params' => new JRegistry('{"maximenu_icon":"fa fa-ticket"}')
		, 'menu_image' => ''
		, 'classe' => ' item8'
		, 'liclass' => ''
		, 'type' => ''
		, 'anchor_css' => ''
		, 'flink' => 'javascript:void(0)'
		, 'rel' => ''
		, 'deeper' => false
		, 'shallower' => false
		, 'level_diff' => 0
		, 'is_end' => false
		, 'leftmargin' => ''
		, 'topmargin' => ''
		, 'colbgcolor' => ''
		, 'submenucontainerheight' => ''
	),
	(object) array(
		'ftitle' => 'Faucibus'
		, 'id' => 11
		, 'level' => 2
		, 'anchor_title' => ''
		, 'desc' => ''
		, 'params' => new JRegistry('{"maximenu_icon":"fa fa-calendar"}')
		, 'menu_image' => ''
		, 'classe' => ' item9'
		, 'liclass' => ''
		, 'type' => ''
		, 'anchor_css' => ''
		, 'flink' => 'javascript:void(0)'
		, 'rel' => ''
		, 'deeper' => false
		, 'shallower' => false
		, 'level_diff' => 0
		, 'is_end' => false
		, 'leftmargin' => ''
		, 'topmargin' => ''
		, 'colbgcolor' => ''
		, 'submenucontainerheight' => ''
	),
	(object) array(
		'ftitle' => 'Dapibus ligula'
		, 'id' => 12
		, 'level' => 2
		, 'anchor_title' => ''
		, 'desc' => ''
		, 'params' => new JRegistry('{"maximenu_icon":"fa fa-cutlery"}')
		, 'menu_image' => ''
		, 'classe' => ' item10'
		, 'liclass' => ''
		, 'type' => ''
		, 'anchor_css' => ''
		, 'flink' => 'javascript:void(0)'
		, 'rel' => ''
		, 'deeper' => false
		, 'shallower' => false
		, 'level_diff' => 0
		, 'is_end' => false
		, 'leftmargin' => ''
		, 'topmargin' => ''
		, 'colbgcolor' => ''
		, 'submenucontainerheight' => ''
	),
	(object) array(
		'ftitle' => 'Column 2'
		, 'id' => 13
		, 'level' => 2
		, 'anchor_title' => ''
		, 'desc' => ''
		, 'params' => new JRegistry()
		, 'menu_image' => ''
		, 'classe' => ' item11 headingck'
		, 'liclass' => ''
		, 'type' => 'separator'
		, 'anchor_css' => ''
		, 'flink' => 'javascript:void(0)'
		, 'rel' => ''
		, 'deeper' => false
		, 'shallower' => false
		, 'level_diff' => 0
		, 'is_end' => false
		, 'leftmargin' => ''
		, 'topmargin' => ''
		, 'colbgcolor' => ''
		, 'submenucontainerheight' => ''
		, 'columnwidth' => '50%'
		, 'colonne' => true
	),
	(object) array(
		'ftitle' => 'Eu placerat'
		, 'id' => 14
		, 'level' => 2
		, 'anchor_title' => ''
		, 'desc' => ''
		, 'params' => new JRegistry()
		, 'menu_image' => ''
		, 'classe' => ' item12'
		, 'liclass' => ''
		, 'type' => ''
		, 'anchor_css' => ''
		, 'flink' => 'javascript:void(0)'
		, 'rel' => ''
		, 'deeper' => false
		, 'shallower' => false
		, 'level_diff' => 0
		, 'is_end' => false
		, 'leftmargin' => ''
		, 'topmargin' => ''
		, 'colbgcolor' => ''
		, 'submenucontainerheight' => ''
	),
	(object) array(
		'ftitle' => 'Felis posuere'
		, 'id' => 15
		, 'level' => 2
		, 'anchor_title' => ''
		, 'desc' => ''
		, 'params' => new JRegistry()
		, 'menu_image' => ''
		, 'classe' => ' item13'
		, 'liclass' => ''
		, 'type' => ''
		, 'anchor_css' => ''
		, 'flink' => 'javascript:void(0)'
		, 'rel' => ''
		, 'deeper' => false
		, 'shallower' => false
		, 'level_diff' => 0
		, 'is_end' => false
		, 'leftmargin' => ''
		, 'topmargin' => ''
		, 'colbgcolor' => ''
		, 'submenucontainerheight' => ''
	),
	(object) array(
		'ftitle' => 'Adipiscing'
		, 'id' => 16
		, 'level' => 2
		, 'anchor_title' => ''
		, 'desc' => ''
		, 'params' => new JRegistry()
		, 'menu_image' => ''
		, 'classe' => ' item14'
		, 'liclass' => ''
		, 'type' => ''
		, 'anchor_css' => ''
		, 'flink' => 'javascript:void(0)'
		, 'rel' => ''
		, 'deeper' => false
		, 'shallower' => true
		, 'level_diff' => 1
		, 'is_end' => false
		, 'leftmargin' => ''
		, 'topmargin' => ''
		, 'colbgcolor' => ''
		, 'submenucontainerheight' => ''
	),
	(object) array(
		'ftitle' => 'Consectetur'
		, 'id' => 17
		, 'level' => 1
		, 'anchor_title' => ''
		, 'desc' => ''
		, 'params' => new JRegistry('{"maximenu_icon":"fa fa-car"}')
		, 'menu_image' => ''
		, 'classe' => ' item15 active current'
		, 'liclass' => ''
		, 'type' => ''
		, 'anchor_css' => ''
		, 'flink' => 'javascript:void(0)'
		, 'rel' => ''
		, 'deeper' => false
		, 'shallower' => false
		, 'level_diff' => 0
		, 'is_end' => true
		, 'leftmargin' => ''
		, 'topmargin' => ''
		, 'colbgcolor' => ''
		, 'submenucontainerheight' => ''
	)
);

// get the params from the module
$modulelayout = trim( $input->get('modulelayout', $this->params->get('layout', 'default'), 'string'), '_:');
$orientation = $input->get('orientation',  $this->params->get('orientation','horizontal'), 'string');

// set the params of the demo module
$params = new JRegistry();
$params->set('startLevel', '1');
$params->set('orientation', $orientation);
$params->set('menuid', 'maximenuck_previewmodule');

if ( ($modulelayout == 'pushdown' || $modulelayout == 'megatabs') && $orientation == 'vertical') {
	echo '<p style="color:red;font-weight:bold;">MAXIMENU MESSAGE : You can not use this layout for a Vertical menu</p>';
	die;
}

// load the module helper
if (file_exists(JPATH_ROOT.'/modules/mod_maximenuck/helper.php')) {
	require_once JPATH_ROOT.'/modules/mod_maximenuck/helper.php';
} else {
	echo JText::_('CK_MODULE_MAXIMENUCK_NOT_INSTALLED');
	die;
}

// load the layout
if (file_exists(JPATH_ROOT.'/modules/mod_maximenuck/tmpl/'.$modulelayout.'.php')) {
	require_once JPATH_ROOT.'/modules/mod_maximenuck/tmpl/'.$modulelayout.'.php';
} else {
	echo JText::_('CK_MODULE_MAXIMENUCK_LAYOUT_NOT_FOUND') . ' : ' . $modulelayout;
}

// add a message for pushdown
if ( $modulelayout === 'pushdown' ) {
	echo JText::_('CK_PUSHDOWN_PREVIEW_NOT_AVAILABLE');
}