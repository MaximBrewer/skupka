<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

nextendimport('nextend.form.element.subform');

class NextendElementSubpluginmatrix extends NextendElementSubform {

    var $_list = null;

    function decorateBefore() {

        $js = NextendJavascript::getInstance();
        $html = '';
        $js->addLibraryJs('dojo', '
            new NextendElementSubform({
              hidden: "' . $this->_id . '",
              origvalue: "' . $this->_value . '",
              value: "' . $this->_value . '",
              tab: "' . $this->_tab->_name . '",
              xml: "' . NextendFilesystem::toLinux(NextendFilesystem::pathToRelativePath($this->_form->_xmlfile)) . '"
            });
        ');
        $GLOBALS['nextendbuffer'] = '<div id="nextend-' . $this->_name . '-panel">' . $this->renderForm() . "</div>";

        return $html;
    }

    function decorateAfter() {
        $html = '';
        return $html;
    }

    function getOptions() {
        if ($this->_list == null) {
            $this->loadList();
        }
        $list = array_keys($this->_list);
        sort($list);
        return $list;
    }

    function getSubFormfolder($value) {
        if ($this->_list == null) {
            $this->loadList();
        }
        if (!isset($this->_list[$value]))
            list($value) = array_keys($this->_list);
        return $this->_list[$value];
    }
    
    function loadList(){
        $this->_list = array();
        NextendPlugin::callPlugin(NextendXmlGetAttribute($this->_xml, 'group'), 'onNextend'.NextendXmlGetAttribute($this->_xml, 'method').'List', array(&$this->_list));
    }

}