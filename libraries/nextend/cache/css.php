<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
nextendimport('nextend.cache.cache');

class NextendCacheCss extends NextendCache {

    function NextendCacheCss() {
        $this->_subfolder = 'css' . DIRECTORY_SEPARATOR;
        parent::NextendCache();
        $this->_filetype = 'css';
        $this->_gzip = getNextend('gzip', 0);
    }

    function parseFile($content, $path, $i) {
        $this->path = $path;
        return preg_replace_callback('#url\([\'"]([^"\'\)]+)[\'"]\)#', array($this, 'makeUrl'), $content);
    }
    
    function makeUrl($matches){
        if(substr($matches[1], 0, 5) == 'data:') return $matches[0];
        if(substr($matches[1], 0, 4) == 'http') return $matches[0];
        if(substr($matches[1], 0, 2) == '//') return $matches[0];
        return 'url(' . str_replace(array('http://', 'https://'), '//', NextendFilesystem::pathToAbsoluteURL(dirname($this->path))) . '/'.$matches[1].')';
    }     
    
    function parseCached($cached){
        return $cached;
    }

    function getContentHeader() {
        return 'header("Content-type: text/css", true);';
    }
}