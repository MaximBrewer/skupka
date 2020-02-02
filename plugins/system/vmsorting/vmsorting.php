<?php
/**
 /*------------------------------------------------------------------------
 # VmSorting 1.0
 # ------------------------------------------------------------------------
 # (C) 2016 Все права защищены.
 # Лицензия http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 # Автор: Vladimir Pronin
 # Сайт: http://virtuemart.su
 -------------------------------------------------------------------------*/

// No direct access

defined('_JEXEC') or die('Restricted access..');


jimport('joomla.plugin.plugin');

class plgSystemVmsorting extends JPlugin {
	public function onBeforeRender() {
        if (JFactory::getApplication()->isAdmin()) {
            return;
        }
        $doc = JFactory::getDocument();
        $view = JRequest::getVar('view');
        $option = JRequest::getVar('option');
        
        // get parameters
        $view_sorting = $this->params->get('view_sorting', '1');
        $bootstrap = $this->params->get('bootstrap', '0');
        
        if(($option == 'com_virtuemart' && $view == 'category') || ($option == 'com_customfilters' && $view == 'products')){
            if($view_sorting == 1){
                $js = 'jQuery(document).ready(function($) {$(".orderlistcontainer").vmsorting();});';
            } else {
                $js = 'jQuery(document).ready(function($) {$(".orderlistcontainer").vmsorting("block");});';
            }

            // Script
            $doc->addScript("/plugins/system/vmsorting/media/jquery.vmsorting.js");
            $doc->addScriptDeclaration($js);

            //Style
            if($bootstrap){
                $doc->addStyleSheet('/plugins/system/vmsorting/media/vmsorting_bs.css');
            } else {
                $doc->addStyleSheet('/plugins/system/vmsorting/media/vmsorting.css'); 
            }  
        } 
	}	
}
?>