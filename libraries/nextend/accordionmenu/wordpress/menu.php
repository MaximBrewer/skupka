<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'menu.php' );

nextendimport('nextend.data.data');
nextendimport('nextend.parse.parse');

class NextendMenuWordpress extends NextendMenu {

    var $_data;
    var $_widget;

    function NextendMenuWordpress(&$widget, &$instance, $path) {
        parent::NextendMenu($path);
        $this->_data = new NextendData();
        
        if(!isset($instance['accordionmenu'])){
            $this->_error[] = 'Missing Accordion Menu for this widget.';
            return;
        }
        
        $params = get_post_meta($instance['accordionmenu'], 'nextend_configuration', true);
        if(!$params){
            $this->_error[] = 'Missing Accordion Menu configuration.';
            return;
        }
        
        $this->_data->loadArray($params);
        $widget['instance'] = $instance;
        $this->_widget = $widget;
        $this->setThemePath();
        $this->setInstance();
    }

    function setInstance() {
        $this->_instance = $this->_widget['widget_id'];
    }

    function getTreeInstance() {
        $type = $this->_data->get('type', 'joomla');
        require_once(NEXTEND_ACCORDION_MENU . 'types' . DIRECTORY_SEPARATOR . 'loadplugin.php');
        $class = 'plgNextendMenu' . $type;
        if (!class_exists($class)) {
            echo 'Error in menu type!';
            return false;
        }
        $class = new $class();
        $this->_typepath = $class->getPath();
        require_once($this->_typepath . 'menu.php');
        $class = 'NextendTree' . $type;
        return new $class($this, $this->_widget, $this->_data);
    }

    function setThemePath() {
        $theme = $this->_data->get('theme', 'default');
        require_once(NEXTEND_ACCORDION_MENU . 'themes' . DIRECTORY_SEPARATOR . 'loadplugin.php');
        $class = 'plgNextendMenutheme' . $theme;
        if (!class_exists($class)) {
            echo 'Error in menu theme!';
            return false;
        }
        $class = new $class();
        $this->_themePath = $class->getPath();
    }
    
    function addCSS(){
        parent::addCSS();
        $css = NextendCss::getInstance();
        end($css->_cssFiles);
        $last = key($css->_cssFiles);
        $override = str_replace(WP_PLUGIN_DIR, get_template_directory(), $css->_cssFiles[$last][1]);
        if(NextendFilesystem::fileexists($override)){
            $css->_cssFiles[$last][1] = $override;
        }
    }

    function getTitle() {
        return get_the_title($this->_widget['instance']['accordionmenu']);
    }

}