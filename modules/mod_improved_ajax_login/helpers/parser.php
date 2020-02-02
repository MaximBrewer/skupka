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
// no direct access
defined('_JEXEC') or die('Restricted access');

if(!class_exists('OfflajnValueParser')){
  class OfflajnValueParser {  
    function parse($s, $concat = false){
      $v = explode("|*|", $s);
      for($i = 0; $i < count($v);$i++){
        if(strpos($v[$i] ,"||") !== false){
          if($concat === false)
            $v[$i] = explode("||", $v[$i]);
          else
            $v[$i] = str_replace("||",$concat, $v[$i]);
        }
      }
      if($v[count($v)-1] == '') unset($v[count($v)-1]);
      return count($v) == 1 ? $v[0] : $v;
    }
    
    function parseUnit($v, $concat = ''){
      if(!is_array($v)) $v = self::parse($v);
    	$unit = $v[count($v)-1];
    	unset($v[count($v)-1]);
    	$r = '';
    	foreach($v AS $m){
          $r.= $m.$unit.$concat;
    	}
    	return $r;
    }
    
    function parseBorder($s){
    	$v = self::parse($s);
    	return array(self::parseUnit(array_splice($v,0,5),' '), '#'.$v[0], $v[1]);
    }
    
    function parseColorizedImage($s){
      global $ImageHelper;
      $v = self::parse($s);
      $img = '';
      if($v[3] == 1){
        $v[0] = str_replace(JURI::root(), JPATH_SITE, $v[0]);
        if(strpos($v[0], JPATH_SITE) === false) {
          $v[0] = JPATH_SITE.$v[0];
        }
          $img = $ImageHelper->colorizeImage($v[0], $v[2], '548722');
      }else{
          $img = JUri::root(true).$v[0];
      }
      return array($img,$v[1]);
    }
  
  }
}

?>