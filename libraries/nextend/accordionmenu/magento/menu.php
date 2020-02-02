<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

nextendimportaccordionmenu('nextend.accordionmenu.menu');
nextendimportaccordionmenu('nextend.accordionmenu.magento.tree');
nextendimport('nextend.data.data');
nextendimport('nextend.parse.parse');

class NextendMenuMagento extends NextendMenu {

    var $_data;
    
    var $_module;
    
    var $_magethis;

    function NextendMenuMagento($magethis, $instance, &$params, $path) {
        parent::NextendMenu($path);
        $this->_magethis = $magethis;
        $this->_data = new NextendData();
        $this->_data->loadArray($params);
        
        $cacheby = (array)explode('||', $this->_data->get('ajax_cacheby', 'storeid'));
        if(in_array('storeid', $cacheby)){
           $this->_cachehash.= Mage::app()->getWebsite()->getId().'-'; 
        }
        if(in_array('userid', $cacheby)){
            if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                $this->_cachehash.= Mage::getSingleton('customer/session')->getCustomer()->getId().'-'; 
            }
        }
        
        $module = new stdClass();
        $module->id = $instance;
        $this->_module = $module;
        $this->setThemePath();
        $this->setInstance();
    }

    function setInstance() {
        $this->_instance = $this->_module->id;
    }

    function getTreeInstance() {
        static $instance = null;
        if(!$instance){
            $instance = new NextendTreeMagento($this, $this->_module, $this->_data);
        }
        return $instance;
    }

    function setThemePath() {
        $theme = $this->_data->get('theme', 'default');
        $class = 'plgNextendMenutheme' . $theme;
        if (!class_exists($class)) {
            echo 'Error in menu theme!';
            return false;
        }
        $class = new $class();
        $this->_themePath = $class->getPath();
    }

    function getTitle() {
        return $this->_magethis->__('Categories');
    }
    
    function getAjaxUrl(){
        return Mage::getUrl('accordionmenu/accordionmenu/ajaxload');
    }

}