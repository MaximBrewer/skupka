<?php
/**
* @version		$Id: link.php 58 2011-02-18 12:40:41Z happy_noodle_boy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
defined( '_JEXEC' ) or die( 'RESTRICTED' );

require_once( dirname( __FILE__ ) .DIRECTORY_SEPARATOR. 'classes' .DIRECTORY_SEPARATOR. 'hshtmlexpander.php' );

$plugin = WFHsHtmlExpanderPlugin::getInstance();
$plugin->execute();
