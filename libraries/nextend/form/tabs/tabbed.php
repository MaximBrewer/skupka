<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
nextendimport('nextend.form.tab');

class NextendTabTabbed extends NextendTab {

    var $_tabs;
    
    function NextendTabTabbed(&$form, &$xml) {
        $css = NextendCss::getInstance();
        $css->addCssLibraryFile('tabs/tabbed.css');
        parent::NextendTab($form, $xml);
    }
    
    function initTabs() {
        if (count($this->_tabs) == 0) {
            foreach($this->_xml->params as $tab) {
                $type = NextendXmlGetAttribute($tab, 'type');
                if($type == '') $type = 'default';
                nextendimport('nextend.form.tabs.'.$type);
                $class = 'NextendTab' . ucfirst($type);
                
                $this->_tabs[NextendXmlGetAttribute($tab, 'name') ] = new $class($this->_form, $tab);
            }
        }
    }
    
    function render($control_name) {
        $this->initTabs();
        $js = NextendJavascript::getInstance();
        $js->addLibraryJsAssetsFile('jquery', 'tab/tabbed.js');

        $count = count($this->_tabs);
        $id = 'nextend-tabbed-'.$this->_name;
        $active = intval(NextendXmlGetAttribute($this->_xml, 'active'));
        $active = $active > 0 ? $active-1 : 0;
        
        $js->addLibraryJs('jquery', "new nextendTabTabbed('".$id."-container', ".$active.")");
        
        echo "<div id='".$id."-container' class='nextend-tab nextend-tab-tabbed nextend-clearfix'>";
        
        echo '<div class="smartslider-greybar smartslider-button-grey">';
        $i = 0;
        foreach($this->_tabs AS $tabname => $tab) {
            echo '<div class="smartslider-toolbar-options smartslider-button-grey'.($i == $active ? ' active' : '').($i == 0 ? ' first' : '').($i == $count-1 ? ' last' : '').'"><div>'.NextendText::_(NextendXmlGetAttribute($tab->_xml, 'label')).'</div></div>';
            $i++;
        }
        echo '</div>';
        
            echo "<div id='".$id."' class='nextend-tab-tabbed-panes nextend-clearfix' style='width: ".($count*100)."%; margin-left: ".(-$active*100)."%;'>";
            $i = 0;
            foreach($this->_tabs AS $tabname => $tab) {
                echo "<div class='nextend-tab-tabbed-pane' style='width: ".(100/$count)."%; visibility: ".($i == $active ? 'visible' : 'hidden').";'>";
                $tab->render($control_name);
                echo "</div>";
                $i++;
            }
            echo "</div>";
        echo "</div>";
    }
}