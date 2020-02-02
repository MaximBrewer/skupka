<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

class NextendJavascriptJoomla extends NextendJavascript {
    
    function generateJs(){
        $this->generateLibraryJs();
        $document = JFactory::getDocument();
        
        if($this->_cacheenabled){
            if (count($this->_jsFiles)) {
                foreach($this->_jsFiles AS $file) {
                    if(substr($file, 0, 4) == 'http'){
                        if($this->_echo){
                            parent::serveJsFile($file);
                        }else{
                            $document->addScript($file);
                        }
                    }else{
                        $this->_cache->addFile($file);
                    }
                }
            }
            $this->_cache->addInline($this->_js);
            $filename = $this->_cache->getCache();
            if($filename){
                if($this->_echo){
                    parent::serveJsFile($filename);
                }else{
                    $document->addScript($filename);
                }
            }
        }else{
            if(count($this->_jsFiles)){
                foreach($this->_jsFiles AS $file){
                    if($this->_echo){
                        parent::serveJsFile(NextendUri::pathToUri($file));
                    }else{
                        $document->addScript(NextendUri::pathToUri($file));
                    }
                }
            }
            $this->serveJs();
        }
        $this->serveInlineJs();
    }
    
    function serveJs($clear = true){
        if($this->_js == '') return;
        if($this->_echo){
            parent::serveJs($clear);
            return;
        }
        $document = JFactory::getDocument();
        $document->addScriptDeclaration($this->_js);
        if($clear) $this->_js = '';
    }
    
    function serveInlineJs($clear = true){
        if($this->_inlinejs == '') return;
        if($this->_echo){
            parent::serveInlineJs($clear);
            return;
        }
        $document = JFactory::getDocument();
        $document->addScriptDeclaration($this->_inlinejs);
        if($clear) $this->_inlinejs = '';
    }
    
    function serveJsFile($url){
        
    }
}