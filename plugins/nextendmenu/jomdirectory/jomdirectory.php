<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
jimport( 'joomla.plugin.plugin' );

class plgNextendMenuJomDirectory extends JPlugin {
    
    var $_name = 'jomdirectory';
    
    function onNextendMenuList(&$list){
        if(NextendFilesystem::existsFolder(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_jomdirectory')){
            $list[$this->_name] = $this->getPath();
        }
    }
    
    function getPath(){
        return dirname(__FILE__).DIRECTORY_SEPARATOR.'jomdirectory'.DIRECTORY_SEPARATOR;
    }
}