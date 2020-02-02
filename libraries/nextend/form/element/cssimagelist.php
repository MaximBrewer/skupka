<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
nextendimport('nextend.form.element.imagelistfromfolder');

class NextendElementCssImageList extends NextendElementImagelistFromfolder {


    function generateOptions(&$xml) {
        $template = (string)$this->_xml;
        $cssfile = NextendFilesystem::translateToMediaPath(str_replace(DIRECTORY_SEPARATOR, '/', dirname($this->_form->_xmlfile)).'/style.');
        $css = NextendCss::getInstance();

        if(NextendFilesystem::fileexists($cssfile.'less')){
            $css->enableLess();
            $cssfile.='less';
            $css->addCssFile(array(
                $cssfile,
                $cssfile,
                array('id' => 'body')
            ));
        }else{
            $cssfile.='css';
            $css->addCssFile($cssfile);
        }

        $prefix = NextendXmlGetAttribute($this->_xml, 'prefix');
        $this->_values = array();
        $html = '';
        foreach($xml->option AS $option) {
            $v = NextendXmlGetAttribute($option, 'value');
            $this->_values[] = $v;
            if($v != -1){
                $info = pathinfo($v);
                $class = $prefix.basename($v, '.'.$info['extension']);
                $html.= '
                <div class="nextend-radio-option nextend-imagelist-option' . $this->isSelected($v) . '">
                    '.str_Replace('{image}',NextendUri::pathToUri($v),str_Replace('{class}',$class,$template)).'
                </div>';
            }else{
                $html.= '<div class="nextend-radio-option' . $this->isSelected($v) . '">' . ((string)$option) . '</div>';
            }
        }
        return $html;
    }
}
