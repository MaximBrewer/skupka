<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
nextendimport('nextend.form.element.hidden');
nextendimport('nextend.fonts.fontmanager');

class NextendElementFontmanager extends NextendElement {
    
    var $_includeJS = true;
    
    var $base64 = 0;
    
    function fetchElement() {

        $css = NextendCss::getInstance();
        $css->addCssLibraryFile('element/fontmanager.css');
        $js = NextendJavascript::getInstance();
        $js->addLibraryJsAssetsFile('dojo', 'element.js');
        $js->addLibraryJsAssetsFile('dojo', 'element/fontmanager.js');
        
        $this->_value = $this->_form->get($this->_name, $this->_default);
        
        if(intval(NextendXmlGetAttribute($this->_xml, 'base64'))){
            $this->base64 = 1;
            
            $js->addLibraryJsFile('jquery', NEXTENDLIBRARYASSETS . 'js' . DIRECTORY_SEPARATOR . 'base64.js');
            $json = json_decode($this->_value);
            if($json != null && $json != $this->_value){
                $this->_value = base64_encode($this->_value);
                $this->_form->set($this->_name, $this->_value);
            }
        }
        
        $hidden = new NextendElementHidden($this->_form, $this->_tab, $this->_xml);
        
        $html = '';
        
        $fontmanager = NextendFontmanager::getInstance();
        
        $fontmanager->_currentform = $this->_form;
        
        $html.= $fontmanager->render();
        
        $html.= '<a id="nextend-'.$this->_name.'-button" class="nextend-font-button" href="#">'.NextendText::_('Font').'</a>';
        $html.= '<a id="nextend-'.$this->_name.'-button-export" class="nextend-button-css nextend-font-export nextend-element-hastip" title="'.NextendText::_('FONTMANAGER_Export').'" href="#"></a>';
        $html.= '<a id="nextend-'.$this->_name.'-button-import" class="nextend-button-css nextend-font-import nextend-element-hastip" title="'.NextendText::_('FONTMANAGER_Import').'" href="#"></a>';
        $html.= '<div id="nextend-'.$this->_name.'-message" class="nextend-message"></div>';
        
        $html.= "<div class='nextend-fontmanager nextend-clearfix'>";
        $hiddenhtml = $hidden->render($this->control_name, false);
        $html.= $hiddenhtml[1];
        $html.= "</div>";
        
        $tabs = explode('|', NextendXmlGetAttribute($this->_xml, 'tabs'));
        $translatedTabs = array();
        for($i = 0; $i < count($tabs); $i++ ){
            $translatedTabs[$i] = NextendText::_($tabs[$i]);
        }
        
        $this->printjs = '
            new NextendElementFontmanager({
                hidden: "'.$this->_id.'",
                button: "nextend-'.$this->_name.'-button",
                importbtn: "nextend-'.$this->_name.'-button-import",
                exportbtn: "nextend-'.$this->_name.'-button-export",
                message: "nextend-'.$this->_name.'-message",
                tabs: '.json_encode($tabs).',
                translatedTabs: '.json_encode($translatedTabs).',
                firsttab: "'.$tabs[0].'",
                txt: {
                    importingdone: "'.NextendText::_('FONTMANAGER_Importing_done').'",
                    youcanimport: "'.NextendText::_('FONTMANAGER_Now_you_can_import_the_settings_of_this_font').'"
                },
                base64: '.$this->base64.'
            });
        ';
        if($this->_includeJS){
            $js->addLibraryJs('dojo', $this->printjs);
        }
        return $html;
    }
}
