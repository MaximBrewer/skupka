<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
$doc = JFactory::getDocument();
$doc->addStyleDeclaration(
'.nextend-accordion-menu-inner dl.level1 dd{
   display: none;
}

.nextend-accordion-menu-inner dl.level1 dd.opened{
   display: block;
}');
jimport('nextend.library');
nextendimport('nextend.accordionmenu.joomla.menu');

$menu = new NextendMenuJoomla($module, $params, dirname(__FILE__));
$menu->render();


$config = JFactory::getConfig();
$caching = $config->get( 'config.caching' );
if($caching === NULL) $caching = $config->get( 'caching' );

$app = JFactory::getApplication();
if($app->isSite() && ($caching == 2 || $caching == 1)){
    if(class_exists('NextendCss')){
        $css = NextendCss::getInstance();
        $css->generateCSS();
    }
    if(class_exists('NextendJavascript')){
        $js = NextendJavascript::getInstance();
        $js->generateJs();
    }
}