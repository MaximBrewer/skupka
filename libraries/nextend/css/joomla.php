<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

class NextendCssJoomla extends NextendCss {
    function serveCSSFile($url){
        if($this->_echo){
            parent::serveCSSFile($url);
        }else{
          $document = JFactory::getDocument();
          $document->addStyleSheet($url);
        }
    }
    
    function serveCSS($clear = true, $group = null) {
        if(!$group && $this->_css != ''){
            if($this->_echo){
                parent::serveCSS($clear);
            }else{
                $document = JFactory::getDocument();
                $document->addStyleDeclaration($this->_css);
                if ($clear) $this->_css = '';
            }
        }
    }
}