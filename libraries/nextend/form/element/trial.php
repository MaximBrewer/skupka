<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

class NextendElementTrial extends NextendElement {
    
    function fetchElement() {
        
        $image = NextendXmlGetAttribute($this->_xml, 'src');
        
        if(nextendIsWordpress()){
            $imagewp = NextendXmlGetAttribute($this->_xml, 'wpsrc');
            if($imagewp) $image = $imagewp;
        }

        return "<a href='http://www.nextendweb.com/smart-slider#pricing' target='_blank'><img src='".$image."' /></a>";
    }
}
