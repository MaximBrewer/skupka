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

class JElementOfflajnCode extends JOfflajnFakeElementBase{
  var	$_name = 'OfflajnCode';

  function universalfetchElement($name, $value, &$node){
    $document =& JFactory::getDocument();
    $this->loadFiles();
    $attr = $node->attributes();

    $html = '<div class="offlajncodecontainer" id="offlajntextareacontainer'.$this->id.'">';
    $html.= '<textarea cols="' . (isset($attr['cols'])? $attr['cols'] : 10) . '" rows="' . (isset($attr['rows'])? $attr['rows'] : 10) . '" class="offlajncode" type="text" name="'.$name.'" id="'.$this->id.'">'.$value.'</textarea>';
    $html.= '</div>';

    if (isset($node->code)) {
      $id = (int) $_REQUEST['id'];
      $jxmle = get_class($node) == 'JXMLElement';
      foreach ($node->code as $code) {
        $ca = $code->attributes();
        $data = (string)($jxmle ? $code[0] : $code->data());
        $data = str_replace('$id', $id, $data);
        $data = preg_replace('/(\/\*.*?\*\/)/s', '<font class="comment">$1</font>', $data);
        $html .= '<pre class="offlajncodesample" style="width:'.(isset($ca['width']) ? $ca['width'].'px' : 'auto').'; height:'.(isset($ca['height']) ? $ca['height'].'px' : 'auto').';">'.$data.'</pre>';
      }
    }

    DojoLoader::addScript('new OfflajnCode({ id: "'.$this->id.'" });');

    return $html;
  }
}
