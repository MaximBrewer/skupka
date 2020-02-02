<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

class NextendJavascriptjQuery {
    
    var $_js;
    
    var $_jsFiles;
    
    function NextendJavascriptjQuery() {
        $this->_js = '';

        $this->_jsFiles = array();
    }
    
    static function getInstance() {
        global $nextendexport;

        static $instance;
        if (!is_object($instance)) {
            $instance = new NextendJavascriptjQuery();
            $instance->addJsLibraryFile('njQuery.js');
            $jqueryLoaded = false;
            if(nextendIsJoomla()){
                JLoader::import( 'joomla.version' );
                $version = new JVersion();
                if (version_compare( $version->RELEASE, '3.0', '>=')) {
                    JHtml::_('jquery.framework');
                    $jqueryLoaded = true;
                }
            }
            
            if($nextendexport) $jqueryLoaded = false;
            
            if(!$jqueryLoaded){
                $instance->addJsLibraryFile('jQuery.js');
            }
            $instance->addJsLibraryFile('uacss.js');
            $instance->addJsLibraryFile('jquery.unique-element-id.js');
        }
        return $instance;
    }
    
    /*
     * Inline script
     */
    function addJs($js, $first = false){
        if($first){
            $this->_js= $js.PHP_EOL.$this->_js;
        }else{
            $this->_js.= $js.PHP_EOL;
        }
    }
    
    /*
     * Relative path to root
     */
    function addJsFile($file) {

        if (!in_array($file, $this->_jsFiles)) {
            $this->_jsFiles[] = $file;
        }
    }
    
    
    function removeJsFile($file) {

        if(($key = array_search($file, $this->_jsFiles)) !== false) {
            unset($this->_jsFiles[$key]);
        }
    }

    /*
    * jQuery folder
    */
    
    function addJsLibraryFile($file) {
    /*
        if(nextendIsWordPress()){
            global $wp_version;
            if (version_compare($wp_version, '3.6', 'ge')) {
                switch(basename($file)){
                    case 'jquery.ui.widget.min.js': 
                    wp_enqueue_script('jquery-ui-widget');
                    return;
                    break;
                    case 'jquery.ui.sortable.min.js': 
                    wp_enqueue_script('jquery-ui-sortable');
                    return;
                    break;
                    case 'jquery.ui.resizable.min.js': 
                    wp_enqueue_script('jquery-ui-resizable');
                    return;
                    break;
                    case 'jquery.ui.mouse.min.js': 
                    wp_enqueue_script('jquery-ui-mouse');
                    return;
                    break;
                    case 'jquery.ui.droppable.min.js': 
                    wp_enqueue_script('jquery-ui-droppable');
                    return;
                    break;
                    case 'jquery.ui.draggable.min.js': 
                    wp_enqueue_script('jquery-ui-draggable');
                    return;
                    break;
                    case 'jquery.ui.core.min.js': 
                    wp_enqueue_script('jquery-ui-core');
                    return;
                    break;
                    default:
                    break;
                }
            }
        }*/
        $file = NextendFilesystem::getBasePath().NextendFilesystem::getLibraryPath() . 'javascript/jquery/1.9.1/' . $file;
        $this->addJsFile($file);
        
    }
    
    function removeJsLibraryFile($file) {

        $file = NextendFilesystem::getBasePath().NextendFilesystem::getLibraryPath() . 'javascript/jquery/1.9.1/' . $file;
        $this->removeJsFile($file);
    }

    /*
    * Assets folder
    */
    
    function addJsAssetsFile($file) {

        $this->addJsFile(NEXTENDLIBRARYASSETS . 'js' . DIRECTORY_SEPARATOR . $file);
    }
    
    function generateJs() {
        $js = NextendJavascript::getInstance();
        if (count($this->_jsFiles)) {
            foreach($this->_jsFiles AS $file) {
                $js->addJsFile($file);
            }
        }
        $this->serveJs();
    }
    
    function generateJsList(){
        if (count($this->_jsFiles)) {
            return $this->_jsFiles;
        }
    }
    
    function serveJs($clear = true){
        $js = NextendJavascript::getInstance();
        $inline = '(function($){ ';
        $inline.= '$(document).ready(function() {';
        $inline.= $this->_js;
        $inline.= '});';
        $inline.= ' })(njQuery);';
        $js->addJs($inline);
    }
}
