<?php
/*-------------------------------------------------------------------------
# mod_improved_ajax_login - Improved AJAX Login and Register
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2017 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
defined('_JEXEC') or die('Restricted access');

class JElementOfflajnTextarea extends JOfflajnFakeElementBase{
  var	$_name = 'OfflajnTextarea';
  
  function universalfetchElement($name, $value, &$node){
    $document =& JFactory::getDocument();
    $this->loadFiles();
    $attr = $node->attributes();
    
    $html = '<div class="offlajntextareacontainer" id="offlajntextareacontainer'.$this->id.'">';
    $html.= '<textarea  cols="' . (isset($attr['cols'])? $attr['cols'] : 10) . '" rows="' . (isset($attr['rows'])? $attr['rows'] : 10) . '" class="offlajntextarea" type="text" name="'.$name.'" id="'.$this->id.'">'.$value.'</textarea>';
    $html.= '</div>';
    return $html;
  }
}
