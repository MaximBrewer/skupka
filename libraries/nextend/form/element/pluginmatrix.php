<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
nextendimport('nextend.form.element.hidden');

class NextendElementPluginMatrix extends NextendElementHidden {
    
    var $_list = null;
    
    function fetchElement() {
        
        $css = NextendCss::getInstance();
        $css->addCssLibraryFile('element/pluginmatrix.css');
        $html = "<div id='nextend-pluginmatrix-".$this->_id."'>";
        
        $widgetTypes = $this->getOptions();
        
        $html.="<div class='nextend-pluginmatrix-views nextend-button-grey nextend-clearfix'>";
        $value = $this->_form->get($this->_name, 'arrow');

        $test = false;
        foreach($widgetTypes AS $type => $v){
            if($value == $type){
                $test = true;
                break;
            }
        }
        if(!$test) $value = 'arrow';
        
        foreach($widgetTypes AS $type => $v){
            if($value == $type) $active = 'active ';
            else $active = '';
            $html.="<div onclick=\"njQuery('#".$this->_id."').val('".$type."')\" class='".$active."nextend-button-grey nextend-button-blue-active nextend-pluginmatrix-view nextend-pluginmatrix-view-".$type."'>
                <div class='nextend-border'>";
            $html.=$v[0];
            $html.="</div></div>";
        }
        $html.="</div>";
        
        $html.="<div class='nextend-pluginmatrix-panes nextend-clearfix'>";
        foreach($widgetTypes AS $type => $v){
            if($value == $type) $active = 'active ';
            else $active = '';
            $html.="<div class='".$active."nextend-pluginmatrix-pane nextend-pluginmatrix-pane-".$type."'>";
            
            $GLOBALS['nextendbuffer'] = '';
            $form = new NextendForm();
            $form->_data = &$this->_form->_data;

            $form->loadXMLFile($v[1].'config.xml');

            ob_start();
            $form->render($this->control_name);
            $html.= ob_get_clean();
        
            $html.= $GLOBALS['nextendbuffer'];
            
            $html.="</div>";
        }
        $html.="</div>";
        
        $html.="</div>";
        
        $js = NextendJavascript::getInstance();
        $js->addLibraryJs('jquery', '
            (function(){
                var parent = $("#nextend-pluginmatrix-'.$this->_id.'"),
                    views = parent.find("> .nextend-pluginmatrix-views > .nextend-pluginmatrix-view"),
                    panes = parent.find("> .nextend-pluginmatrix-panes > .nextend-pluginmatrix-pane");
                views.on("click", function(){
                    views.not(this).removeClass("active");
                    panes.removeClass("active");
                    var i = views.index(this);
                    views.eq(i).addClass("active");
                    panes.eq(i).addClass("active");
                    njQuery(window).trigger("resize");
                });
            })();
        ');
 
        return $html.parent::fetchElement();
    }
    
    function getOptions(){
        if($this->_list == null){
            $this->_list = array();
            NextendPlugin::callPlugin('nextendsliderwidget', 'onNextendSliderWidgetList', array(&$this->_list));
        }
        uasort($this->_list, array($this, 'sort'));
        return $this->_list;
    }

    function sort($a, $b){
        if(!isset($a[2])) $a[2] = 10000;
        if(!isset($b[2])) $b[2] = 10000;
        return $a[2] - $b[2];
    }
}