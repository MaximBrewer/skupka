<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
global $nextendqtip;
nextendimport('nextend.data.data');
nextendimport('nextend.xml.xml');
nextendimport('nextend.fonts.google');

class NextendForm extends NextendData {
    
    var $_xml;
    
    var $_xmlfile;
    
    var $_tabs;
    
    function NextendForm() {
        $this->_xml = null;
        $this->_tabs = array();
        NextendText::l('common');
        parent::NextendData();
        $this->loadQtip();
        
    }
    
    static function loadQtip(){
        global $nextendqtip;
        if($nextendqtip) return;
        $nextendqtip = true;
        $css = NextendCss::getInstance();
        $js = NextendJavascript::getInstance();
        $css->addCssLibraryFile('jquery.qtip.min.css');
        
        $js->loadLibrary('jquery');
        $js->addLibraryJsAssetsFile('jquery', 'jquery.qtip.min.js');
        $js->addLibraryJs('jquery', '$(".nextend-hastip").qtip({
                position: {
                    my: "bottom center",
                    at: "top right"
                },
                style: {
                    tip: {                        
                        width: 12,
                        height: 6
                    }
                }
            });
            $(".nextend-element-hastip").qtip({
                position: {
                    my: "bottom center",
                    at: "top center"
                },
                style: {
                    tip: {                        
                        width: 12,
                        height: 6
                    }
                }
            });
        ');
    }
    
    function initTabs() {
        if (count($this->_tabs) == 0 && $this->_xml->params && count($this->_xml->params)) {
            foreach($this->_xml->params as $tab) {
                $type = NextendXmlGetAttribute($tab, 'type');
                if($type == '') $type = 'default';
                nextendimport('nextend.form.tabs.'.$type);
                $class = 'NextendTab' . ucfirst($type);
                
                $this->_tabs[NextendXmlGetAttribute($tab, 'name') ] = new $class($this, $tab);
            }
        }
    }
    
    function render($control_name) {
        $css = NextendCss::getInstance();
        $css->addCssLibraryFile('form.css');
        
        $this->initTabs();
        $this->decorateFormStart();
        foreach($this->_tabs AS $tabname => $tab) {
            $tab->render($control_name);
        }
        $this->decorateFormEnd();
    }
    
    function decorateFormStart() {

        echo "<div class='nextend-form'>";
    }
    
    function decorateFormEnd() {

        echo "</div>";
        $fonts = NextendFontsGoogle::getInstance();
        $fonts->addFont('Open Sans');
        $fonts->addFont('Open Sans', 600);
        $fonts->addFont('Open Sans', 700);
    }
    
    function loadXMLFile($file) {
        
        $this->_xml = simplexml_load_file($file);
        $this->_xmlfile = $file;
        $this->_xmlfolder = dirname($file).'/';
        $this->_root = dirname($file).DIRECTORY_SEPARATOR;
        
        $translate = NextendXmlGetAttribute($this->_xml, 'translate');
        if($translate){
            NextendText::l('common', nextendSubLibraryPath($translate).'languages/');
        }
    }
    
    function setXML(&$xml) {

        $this->_xml = $xml;
    }
    
    function getSubform($tab, $name){
        $this->initTabs();
        if(isset($this->_tabs[$tab])){
            if(isset($this->_tabs[$tab]->_elements[$name])){
                return $this->_tabs[$tab]->_elements[$name];
            }
        }
        return null;
    }
    
    static function tokenize(){
        if(nextendIsMagento()){
            $adminobj= new Mage_Adminhtml_Block_Template(); 
            echo '<input name="form_key" type="hidden" value="'.$adminobj->getFormKey().'" />';
        }
    }
    
    static function addExtra($data = array()){
        if(nextendIsMagento()){
            $data['form_key'] = Mage::getSingleton('core/session' )->getFormKey();
        }
        return json_encode($data);
    }
}
?>