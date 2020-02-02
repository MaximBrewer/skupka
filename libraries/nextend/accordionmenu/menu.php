<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

class NextendMenu {

    var $id = 0;
    
    var $_tree;
    
    var $_path;
    
    var $_themePath;
    
    var $_identifier = 'nextend-accordion-menu';
    
    var $_instance;
    
    var $_classPrefix = 'nextend-nav-';
    
    var $_error;
    
    var $_cachehash = '';
    
    var $_cachemoduleid = '';
    
    function NextendMenu($path) {

        $this->_path = $path . DIRECTORY_SEPARATOR;
        $this->_error = array();
    }
    
    function getTitle() {

        return 'No title available';
    }
    
    function getId() {

        return $this->_identifier . '-' . $this->_instance;
    }
    
    function render() {
        if(count($this->_error) > 0){
            foreach($this->_error AS $error){
                echo $error."<br />";
            }
            return false;
        }

        $this->_tree = $this->getTreeInstance();
        if ($this->_tree === false) {
            return false;
        }
        
        $this->_render();
        
        $this->addCSS();
        $this->addJs();
        unset($this->_tree);
        unset($this->_data);
    }
    
    function _render(){
        $this->_tree->generateItems();
        $id = $this->getId();
        $data = & $this->_data;
        $menu = & $this->_tree;
        $menu->_classPrefix = $this->_classPrefix;
        $html5 = $this->_data->get('html5', 0);
        if($html5){
            echo "<nav>";
        }
        include ($this->_themePath . 'container.php');
        if($html5){
            echo "</nav>";
        }
    }
    
    function addJs() {

        nextendimport('nextend.javascript.javascript');
        $data = & $this->_data;
        $js = NextendJavascript::getInstance();
        $css3animation = $data->get('css3animation', 0);
        if($css3animation){
            $js->loadLibrary('modernizr');
        }
        $js->loadLibrary('dojo');
        $js->addLibraryJsLibraryFile('dojo', 'dojo/fx/easing.js');
        $js->addLibraryJsLibraryFile('dojo', 'dojo/regexp.js');
        $js->addLibraryJsLibraryFile('dojo', 'dojo/cookie.js');
        $js->addLibraryJsLibraryFile('dojo', 'dojo/uacss.js');
        $js->addLibraryJsFile('dojo', (defined('NEXTEND_ACCORDION_MENU_ASSETS') ? NEXTEND_ACCORDION_MENU_ASSETS : NEXTENDLIBRARYASSETS) . 'accordionmenu' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'accordionmenu.js');
        $animation = explode('|*|',$data->get('animation', '500|*|dojo.fx.easing.cubicInOut|*|dojo.fx.easing.cubicInOut'));
        
        $tooltip = NextendParse::parse($data->get('tooltip', '0|*|Click to open!|*|Click to close!'));
        
        $js->addLibraryJs('dojo', "
            dojo.query('.noscript').removeClass('noscript');
            new AccordionMenu({
                node: dojo.query('#" . $this->getId() . " dl.level1')[0],
                instance: '" . $this->getId() . "',
                url: '".$this->getAjaxUrl()."',
                moduleid: ".$this->id.",
                classPattern: /" . $this->_classPrefix . "[0-9]+/,
                mode: '" . $data->get('mode', 'onclick') . "', 
                level: 1,
                interval: " .intval($animation[0]) . ", 
                css3animation: ".intval($css3animation).",
                easing:  '" . $animation[1] . "',
                closeeasing:  '" . $animation[2] . "',
                accordionmode:  " . intval($data->get('accordionmode', 1)) . ",
                usecookies: ". ($data->get('opened', 0) == 3 ? 1 : 0) .",
                tooltip:  " . intval($tooltip[0]) . ",
                tooltipopen:  '" . $tooltip[1] . "',
                tooltipclose:  '" . $tooltip[2] . "'
            });
        ");
    }
    
    function getAjaxUrl(){
        return '';
    }
    
    function addCSS() {
        nextendimport('nextend.css.css');
        $css = NextendCss::getInstance();
        $css->enableLess();
        $css->addLessImportDir((defined('NEXTEND_ACCORDION_MENU_ASSETS') ? NEXTEND_ACCORDION_MENU_ASSETS : NEXTENDLIBRARYASSETS) . 'accordionmenu' . DIRECTORY_SEPARATOR . 'less' . DIRECTORY_SEPARATOR);
        $data = & $this->_data;
        $css3animation = $data->get('css3animation', 0);
        $context = array(
            'id' => '~"#'.$this->getId().'"',
            'snaptobottom' => $data->get('snaptobottom', 0),
            'css3animation' => $css3animation,
            'accordionmode' => $data->get('accordionmode', 0)
        );
        if($css3animation){
            nextendimport('nextend.animation.animation');
            $animation = explode('|*|',$data->get('animation', '500|*|dojo.fx.easing.cubicInOut|*|dojo.fx.easing.cubicInOut'));
            if($animation[0] < 1) $animation[0] = 1;
            $context['animationinterval'] = number_format(round($animation[0]/1000,2),2,'.','').'s';
            $context['animationopening'] = NextendAnimation::dojoEasingToCSSEasing($animation[1]);
            $context['animationclosing'] = NextendAnimation::dojoEasingToCSSEasing($animation[2]);
            $context['animationclosing'] = NextendAnimation::dojoEasingToCSSEasing($animation[2]);
            
            $transform = intval($data->get('transform', 0));
            $context['transform'] = $transform;
            if($transform){
                $context['opacity'] = floatval($data->get('opacity', 1));
                
                $context['transformperspective'] = $data->get('transformperspective', 0).'px';
                $context['transformorigin'] = implode(' ',NextendParse::parse($data->get('transformorigin', 'center|*|center|*|0')));
                
                $translate = NextendParse::parse($data->get('transformtranslate', '0|*|0|*|0'));
                $scale = NextendParse::parse($data->get('transformscale', '1|*|1|*|1'));
                $rotate = NextendParse::parse($data->get('transformrotate', '0|*|0|*|0'));
                
                $context['transformvalue'] = 'translate3d('.implode('px,',$translate).'px'.') scale3d('.implode(',',$scale).') rotateX('.$rotate[0].'deg) rotateY('.$rotate[1].'deg) rotateZ('.$rotate[2].'deg)';
            }
        }
        include($this->_themePath.'context.php');
        $css->addCssFile(array(
            $this->getId(),
            $this->_themePath . 'style.less',
            $context
        ));
    }
}
