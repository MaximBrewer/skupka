<?php
/*------------------------------------------------------------------------
# mod_universal_ajaxlivesearch - Universal AJAX Live Search
# ------------------------------------------------------------------------
# author    Janos Biro
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
defined('_JEXEC') or die('Restricted access');

$GLOBALS['googlefontsloaded'] = array();

foreach($this->params->toArray() AS $k => $p){
  if(strpos($k, 'font')){
    $p = explode('|*', $p);
    $p[0] = str_replace('*','',$p[0]);
    if(strlen($p[11])>6) {
      preg_match('/(\\w\\w)(\\w\\w)(\\w\\w)(\\w\\w)/', $p[11], $m);
      $p[11] = 'rgba('.hexdec($m[1]).','.hexdec($m[2]).','.hexdec($m[3]).','.(hexdec($m[4])/255.0).')';
    }
    $$k = $p;
    if($p[0] == '0' || !isset($p[2])) continue;
    $t = $p[2];
    if($p[4] == 1){
      $t.=':700';
    }else{
      $t.=':400';
    }
    if($p[5] == 1){
      $t.='italic';
    }
    $subset = $p[0];
    if($subset == 'LatinExtended'){
      $subset = 'latin,latin-ext';
    }else if($subset == 'CyrillicExtended'){
      $subset = 'cyrillic,cyrillic-ext';
    }else if($subset == 'GreekExtended'){
      $subset = 'greek,greek-ext';
    }
    $t.='&subset='.$subset;
    if(isset($GLOBALS['googlefontsloaded'][$t])) continue;
    $GLOBALS['googlefontsloaded'][$t] = true;
    $t = trim(preg_replace('/\s+/', ' ', $t));
    ?>
@import url("https://fonts.googleapis.com/css?family=<?php echo $t; ?>");
    <?php
  }
}
?>

#search-form<?php echo $module->id; ?> input::-ms-clear{
  width: 0;
  height: 0;
}

#search-form<?php echo $module->id; ?> input::focus {
  outline: none;
}