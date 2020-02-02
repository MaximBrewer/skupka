<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

class NextendUriAbstract{
    var $_baseuri;
    
    var $_currentbase = '';
    
    var $_relative = '';
    
    static function getInstance() {

        static $instance;
        if (!is_object($instance)) {
            $instance = new NextendUri();
        } // if

        return $instance;
    }
    
    static function setBaseUri($uri){
        $i = NextendUri::getInstance();
        $i->_baseuri = $uri;
    }
    
    static function getBaseUri(){
        $i = NextendUri::getInstance();
        return $i->_baseuri;
    }
    
    static function setRelative($relative){
        NextendUri::getInstance()->_relative = $relative;
    }
    
    static function getRelative(){
        return NextendUri::getInstance()->_relative;
    }
    
    static function pathToUri($path){
        $i = NextendUri::getInstance();
        return $i->_baseuri.str_replace(array(NextendFilesystem::getBasePath(),DIRECTORY_SEPARATOR),array('','/'), str_replace('/',DIRECTORY_SEPARATOR,$path));
    }
    
    static function ajaxUri($query = '', $magento = 'nextendlibrary'){
        $i = NextendUri::getInstance();
        return $i->_baseuri;
    }
    
    static function fixrelative($uri){
        if(substr($uri, 0, 1) == '/' || strpos($uri, '://') !== false) return $uri;
        return self::getInstance()->_baseuri.$uri;
    }
    
    static function relativetoabsolute($uri){
        if(substr($uri, 0, 1) == '/' || strpos($uri, '://') !== false) return $uri;
        return self::getInstance()->_currentbase.$uri;
    }
    
    function find_relative_path ( $frompath, $topath ) {
        $from = explode( '/', $frompath ); // Folders/File
        $to = explode( '/', $topath ); // Folders/File
        $relpath = '';
         
        $i = 0;
        // Find how far the path is the same
        while ( isset($from[$i]) && isset($to[$i]) ) {
        if ( $from[$i] != $to[$i] ) break;
        $i++;
        }
        $j = count( $from ) - 1;
        // Add '..' until the path is the same
        while ( $i <= $j ) {
        if ( !empty($from[$j]) ) $relpath .= '../';
        $j--;
        }
        // Go to folder from where it starts differing
        while ( isset($to[$i]) ) {
        if ( !empty($to[$i]) ) $relpath .= $to[$i].'/';
        $i++;
        }
        // Strip last separator
        return $relpath;
    }
}

if (nextendIsJoomla()) {
    nextendimport('nextend.uri.joomla');
} elseif (nextendIsWordPress()) {
    nextendimport('nextend.uri.wordpress');
} elseif (nextendIsMagento()) {
    nextendimport('nextend.uri.magento');
}