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

if(!class_exists('OfflajnParser')){
  class OfflajnParser {

    function parse($s, $def = null){
			$v = explode("|*|", is_string($s) ? $s : $def);
			if($v[count($v)-1] == '') unset($v[count($v)-1]);
			if (is_string($s) && is_string($def)) {
				$d = explode("|*|", $def);
				if (!is_string($v) && !is_string($d)) {
					for ($i = count($v); $i--;) array_shift($d);
					$v = array_merge($v, $d);
				}
			}
      for($i = 0; $i < count($v);$i++){
        if(strpos($v[$i] ,"||") !== false){
          $v[$i] = explode("||", $v[$i]);
        }
      }
/*
			if ($def=='#eeeeee|*|rgba(0, 0, 0, 0.53)|*|50||px|*|0||px|*|0||px'){
				echo'<pre>';print_r(count($v) == 1 ? $v[0] : $v);exit;
			}
*/
      return count($v) == 1 ? $v[0] : $v;
    }

    function parseUnit($v, $concat = ''){
      if(!is_array($v)) $v = self::parse($v);
    	$unit = $v[count($v)-1];
    	unset($v[count($v)-1]);
    	$r = '';
    	foreach($v as $m){
        $r.= $m.$unit.$concat;
    	}
    	return $r;
    }

    function parseBorder($s){
    	$v = self::parse($s);
    	return array(self::parseUnit(array_splice($v,0,5),' '), $v[0], $v[1]);
    }

    function parseColorizedImage($s){
      global $MiniImageHelper;
      $v = self::parse($s);
      $img = '';
			$v[0] = str_replace(JURI::root(), JPATH_SITE, $v[0]);
			if (strpos($v[0], JPATH_SITE) === false) {
				$v[0] = JPATH_SITE.$v[0];
			}
			if (file_exists($v[0])) {
				if ($v[2] == '#000000') $v[2] = '#000001';
				if ($v[3] == '#000000') $v[3] = '#000001';
				$img = $MiniImageHelper->colorizeImages($v[0], $v[2], $v[3], '548722');
			}
      return array($img, $v[1]);
    }

  }
}

?>