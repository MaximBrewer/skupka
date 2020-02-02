<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
nextendimport('nextend.form.element.hidden');

class NextendElementMixed extends NextendElement {
    
    var $_separator = '|*|';
    
    var $_translateable = true;
    
    function fetchElement() {

        $css = NextendCss::getInstance();
        $css->addCssLibraryFile('element/mixed.css');
        $js = NextendJavascript::getInstance();
        $js->addLibraryJsAssetsFile('dojo', 'element.js');
        $js->addLibraryJsAssetsFile('dojo', 'element/mixed.js');
        
        $this->_translateable = NextendXmlGetAttribute($this->_xml, 'translateable');
        $this->_translateable = ($this->_translateable === '0' ? false : true);
        $default = explode($this->_separator, $this->_default);
        $this->_value = $this->_form->get($this->_name, $this->_default);
        $value = explode($this->_separator, $this->_value);
        $value = $value+$default;
        
        $html = "<div class='nextend-mixed' style='".NextendXmlGetAttribute($this->_xml, 'style')."'>";
        $this->_elements = array();
        $i = 0;
        foreach($this->_xml->param AS $element) {
            $html.= "<div class='nextend-mixed-group' style='".NextendXmlGetAttribute($element, 'mixedstyle')."'>";
            $type = NextendXmlGetAttribute($element, 'type');

            $class = 'NextendElement' . $type;
            if (!class_exists($class, false)) {
                if (!nextendimportpath($this->_form->_xmlfolder . 'element/' . $type)) {
                    nextendimport('nextend.form.element.' . $type);
                }
            }

            $element->addAttribute('name', $this->_name . '_' . $i);
            $element->addAttribute('hidename', 1);
            if (isset($value[$i])) $element->addAttribute('default', $value[$i]);
            $el = new $class($this->_form, $this, $element);
            $el->parent = &$this;
            $elementHtml = $el->render($this->_name . $this->control_name, $this->_translateable);
            $html.= "<div class='nextend-mixed-label'>";
            $html.= $elementHtml[0];
            $html.= "</div>";
            $html.= "<div class='nextend-mixed-element'>";
            $html.= $elementHtml[1];
            $html.= "</div>";
            $this->_elements[$i] = $el->_id;
            $i++;
            $html.= "</div>";
        }
        $this->_form->set($this->_name, $this->_value);
        $hidden = new NextendElementHidden($this->_form, $this->_tab, $this->_xml);
        $hiddenhtml = $hidden->render($this->control_name, false);
        $html.= $hiddenhtml[1];
        $html.= "</div>";
        $js->addLibraryJs('dojo', '
            new NextendElementMixed({
              hidden: "' . $this->_id . '",
              elements: ' . json_encode($this->_elements) . ',
              separator: "' . $this->_separator . '"
            });
        ');
        return $html;
    }
}
