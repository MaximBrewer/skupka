<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
nextendimport('nextend.cache.cache');
nextendimport('nextend.css.less');
nextendimport('nextend.image.image');

class NextendCacheLess extends NextendCache{
    
    var $_context;
    
    var $_less;
    
    function NextendCacheLess(){
        $this->_subfolder = 'less'.DIRECTORY_SEPARATOR;
        parent::NextendCache();
        $this->_filetype = 'css';
        $this->_less = new nlessc();
        $this->_less->addImportDir(NEXTENDLIBRARYASSETS . DIRECTORY_SEPARATOR . 'less' . DIRECTORY_SEPARATOR);
        
        $this->_image = new NextendImage();
        $this->_image->loadLess($this);
        $this->_image_cacheTime = $this->_cacheTime;
        
        $this->_context = array();
    }
    
    function addContext($path, &$context){
        $i = count($this->_files);
        $this->_files[$i] = $path;
        $this->_context[$i] = $context;
    }
    
    function parseFile($content, $path, $i){
        $this->_less->setVariables($this->_context[$i]);
        $this->path = $path;
        return 
            preg_replace('/;;/', ';',
              preg_replace('/d: ;/', '',
                  preg_replace_callback('#url\([\'"]([^"\'\)]+)[\'"]\)#', array($this, 'makeUrl'), 
                    $this->_less->compile($content)
                  )
              )
            );
    }
    
    function makeUrl($matches){
        if(substr($matches[1], 0, 5) == 'data:') return $matches[0];
        if(substr($matches[1], 0, 4) == 'http') return $matches[0];
        if(substr($matches[1], 0, 2) == '//') return $matches[0];
        return 'url('.NextendFilesystem::pathToAbsoluteURL(dirname($this->path)).'/'.$matches[1].')';
    }        
    
    function parseHash($hash){
        return $hash.json_encode($this->_context);
    }
}