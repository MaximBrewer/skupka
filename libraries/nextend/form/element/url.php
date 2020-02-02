<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

nextendimport('nextend.form.element.text');

class NextendElementUrl extends NextendElementText {

    function fetchElement() {
        $html = parent::fetchElement();
        if (nextendIsJoomla()) {
            JHtml::_('behavior.modal');
            $user = JFactory::getUser();
            $link = 'index.php?option=com_content&view=articles&layout=modal&tmpl=component';
            $html .= '<div class="button2-left" style="margin: 2px 0 2px 10px; float: left;">
                    <div class="article">
                        <a onclick="window.jSelectArticle = function(id, title, catid, object, link, lang){njQuery(\'#' . $this->_id . '\').val(link); NfireEvent(document.getElementById(\'' . $this->_id . '\'),\'change\');SqueezeBox.close();};return false;" rel="{handler: \'iframe\', size: {x: 900, y: 520}}" href="' . $link . '" title="Article" class="modal btn modal-button"><i class="icon-file-add"></i>'.NextendText::_('Article').'</a>
                    </div>
                  </div>';
        }
        return $html;
    }
}
