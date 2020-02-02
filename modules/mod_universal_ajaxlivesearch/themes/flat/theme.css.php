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

$searchareawidth = $this->params->get('searchareawidth', 150);
if($searchareawidth[strlen($searchareawidth)-1] != '%'){
  $searchareawidth.='px';
}

$resultimagewidth = $this->params->get('imagew', 180);
$resultimageheight = $this->params->get('imageh', 140);

$resoponsive = $this->params->get('columns',"");

$align = "";
if($this->params->get('searchareaalign') == 'center') {
  $align = "margin-left: auto; margin-right: auto;";
} else {
  $align = "float: ".$this->params->get('searchareaalign', 'left').";";
}
?>

html, body{
  -webkit-font-smoothing: antialiased;
}

#offlajn-ajax-search<?php echo $module->id; ?>{
  width: <?php print $searchareawidth; ?>;
  <?php echo $align; ?>

  -webkit-transition: all 0.25s ease-out 0.3s;
  -moz-transition: all 0.25s ease-out 0.3s;
  -ms-transition: all 0.25s ease-out 0.3s;
  -o-transition: all 0.25s ease-out 0.3s;
  transition: all 0.25s ease-out 0.3s;
}

#offlajn-ajax-search<?php echo $module->id; ?>.active{
  -webkit-transition: all 0.25s ease-out 0s;
  -moz-transition: all 0.25s ease-out 0s;
  -ms-transition: all 0.25s ease-out 0s;
  -o-transition: all 0.25s ease-out 0s;
  transition: all 0.25s ease-out 0s;
}


#offlajn-ajax-search<?php echo $module->id; ?> .offlajn-ajax-search-container{
  padding: 0;
  margin:0;
}

#offlajn-ajax-search<?php echo $module->id; ?> .offlajn-ajax-search-container.active{
  border-radius: 0;
}

#offlajn-ajax-search<?php echo $module->id; ?> .offlajn-ajax-search-inner{
  width:100%;
}

#search-form<?php echo $module->id; ?>{
  margin:0;
  padding:0;
  position: relative;
}

#search-form<?php echo $module->id; ?> input{
  padding-top:1px;
  /*font chooser*/
  <?php $f = $searchboxfont; ?>
  color: #<?php echo $f[6]?>;
  font-family: <?php echo ($f[0] ? '"'.$f[2].'"':'').($f[1] && $f[0] ? ',':'').$f[1];?>;
  font-weight: <?php echo $f[4]? 'bold' : 'normal';?>;
  font-style: <?php echo $f[5]? 'italic' : 'normal';?>;
  font-size: <?php echo $f[3]?>;
  <?php if($f[7]): ?>
  text-shadow: #<?php echo $f[11]?> <?php echo $f[8]?> <?php echo $f[9]?> <?php echo $f[10]?>;
  <?php else: ?>
  text-shadow: none;
  <?php endif; ?>
  text-decoration: <?php echo $f[12]?>;
  text-transform: <?php echo $f[13]?>;
  line-height: <?php echo $f[14]?>;
  /*font chooser*/
  -webkit-appearance: none;
  border-radius:0px;
}

.dj_ie #search-form<?php echo $module->id; ?> input{
  padding-top:0px;
}

.dj_ie7 #search-form<?php echo $module->id; ?>{
  padding-bottom:0px;
}

#search-form<?php echo $module->id; ?> .category-chooser{
  height: 24px;
  background-color: transparent;
  position: absolute;
  left: 3px;
  z-index: 5;
  cursor: pointer;
  border:none;
  border-right: 1px solid rgba(255, 255, 255, 0.6);
  top:0;
  bottom:0;
  margin: auto;
  position: absolute;
  padding-right:2px;
}

#search-form<?php echo $module->id; ?> .category-chooser:hover{
  -webkit-transition: background 200ms ease-out;
  -moz-transition: background 200ms ease-out;
  -o-transition: background 200ms ease-out;
  transition: background 200ms ease-out;
/*  background-color: #ffffff;*/
}

#search-form<?php echo $module->id; ?> .category-chooser.opened{
  border-bottom: none;
  -moz-border-radius-bottomleft: 0px;
  border-bottom-left-radius: 0px;
}

#search-form<?php echo $module->id; ?> .category-chooser.opened .arrow{
  -moz-transform: rotateZ(90deg);
  -webkit-transform: rotateZ(90deg);
  transform: rotateZ(90deg);
  -moz-transition:  all 200ms ease-out;
  -webkit-transition:  all 200ms ease-out;
  transition:  all 200ms ease-out;
}

#search-form<?php echo $module->id; ?> .category-chooser .arrow{
  height: 100%;
  width: 24px;
  background: no-repeat center center;
  background-image: url(<?php print $themeurl.'images/arrow/settings.png';?>);
  background-size: auto 100%;
  border:none;
/*  border-right: 1px solid rgba(255, 255, 255, 0.6);*/
}

.dj_ie7 #search-form<?php echo $module->id; ?> .category-chooser .arrow,
.dj_ie8 #search-form<?php echo $module->id; ?> .category-chooser .arrow{
  background-image: url(<?php print $themeurl.'images//ie_old/settings_old.png';?>);
}


input#search-area<?php echo $module->id; ?>{
  <?php $f = $searchboxfont; ?>
  display: block;
  position: relative;
  height: <?php echo $f[14]?>;
  padding: 0 30px 0 5px;
  width: 100%;
  box-sizing: border-box !important; /* css3 rec */
  -moz-box-sizing: border-box !important; /* ff2 */
  -ms-box-sizing: border-box !important; /* ie8 */
  -webkit-box-sizing: border-box !important; /* safari3 */
  -khtml-box-sizing: border-box !important; /* konqueror */
  background-color: transparent;
  border: none;
  z-index:4;
  top:0px;
  float: left;
  margin: 0;

  /*if category chooser enabled*/

  <?php if($this->params->get('catchooser')):?>
  padding-left:34px;
  <?php endif; ?>

  box-shadow: none;
}

input#suggestion-area<?php echo $module->id; ?>{
  <?php $f = $searchboxfont; ?>
  display: block;
  position: absolute;
  height: <?php echo $f[14]?>;
  padding: 0 60px 0 5px;
  width: 100%;
  box-sizing: border-box !important; /* css3 rec */
  -moz-box-sizing: border-box !important; /* ff2 */
  -ms-box-sizing: border-box !important; /* ie8 */
  -webkit-box-sizing: border-box !important; /* safari3 */
  -khtml-box-sizing: border-box !important; /* konqueror */
  color:rgba(255, 255, 255, 0.25);
  <?php print $helper->generateBackground($this->params->get('searchboxbg'),"", "", 1);?>

  border: none;
  z-index:1;

  -webkit-box-shadow: none;
  -moz-box-shadow: none;
  box-shadow: none;

  float: left;
  margin: 0;

  /*if category chooser enabled*/

  <?php if($this->params->get('catchooser')):?>
  padding-left:34px;
  <?php endif; ?>
  top:0px;
}

.dj_ie7 input#suggestion-area<?php echo $module->id; ?>{
  float: right;
  left:1px;
  top:1px;
  border:none;
}

.search-caption-on{
  color: #aaa;
}

#search-form<?php echo $module->id; ?> #search-area-close<?php echo $module->id; ?>.search-area-loading{
  background: url(<?php print $themeurl.'images/loaders/'.$this->params->get('ajaxloaderimage');?>) no-repeat center center;
  background-size: auto 100%;
  animation: loader 1s infinite linear;
  -webkit-animation: loader 1s infinite linear;
}

#search-form<?php echo $module->id; ?> #search-area-close<?php echo $module->id; ?>{
  <?php if($this->params->get('closeimage') != -1 && file_exists(dirname(__FILE__).'/images/close/'.$this->params->get('closeimage'))): ?>
  background: url(<?php print $themeurl.'images/close/'.$this->params->get('closeimage');?>) no-repeat center center;
  <?php endif; ?>
  background-size: auto 100%;
  height: 28px;
  width: 28px;
  top:50%;
  margin-top:-14px;
  right: 44px;
  position: absolute;
  cursor: pointer;
  visibility: hidden;
  z-index:5;
}

.dj_ie7 #search-form<?php echo $module->id; ?> #search-area-close<?php echo $module->id; ?>,
.dj_ie8 #search-form<?php echo $module->id; ?> #search-area-close<?php echo $module->id; ?>{
  <?php if($this->params->get('closeimage') != -1 && file_exists(dirname(__FILE__).'/images/close/'.$this->params->get('closeimage'))): ?>
  background: url(<?php print $themeurl.'images/ie_old/close_old.png'; ?>) no-repeat center center;
  <?php endif; ?>
}

#ajax-search-button<?php echo $module->id; ?>{
  height: 100%;
  width: 30px;
  padding: 0 5px;
  background: transparent;
  float: left;
  cursor: pointer;
  position: absolute;
  bottom:0px;
  top: 0px;
  right: 0px;
  z-index:5;
  <?php print $helper->generateBackground($this->params->get('searchiconbg', '00000099'),"", "", 1);?>
  box-sizing: content-box;
}

.dj_ie7 #ajax-search-button<?php echo $module->id; ?>{
  top: 0+1; ?>px;
  right: 0-1; ?>px;
}

.dj_opera #ajax-search-button<?php echo $module->id; ?>{
  border-radius: 0;
}

#ajax-search-button<?php echo $module->id; ?> .magnifier{
  <?php if($this->params->get('searchbuttonimage') != -1 && file_exists(dirname(__FILE__).'/images/search_button/'.$this->params->get('searchbuttonimage'))): ?>
  background: url(<?php print $themeurl.'images/search_button/'.$this->params->get('searchbuttonimage');?>) no-repeat center center;
  <?php endif; ?>
  background-size: 100%;
  height: 27px;
  width: 30px;
  padding:0;
  margin: auto;
  position: absolute;
  bottom:0px;
  top: 0px;
}

.dj_ie7 #ajax-search-button<?php echo $module->id; ?> .magnifier,
.dj_ie8 #ajax-search-button<?php echo $module->id; ?> .magnifier{
  <?php if($this->params->get('closeimage') != -1 && file_exists(dirname(__FILE__).'/images/close/'.$this->params->get('closeimage'))): ?>
  background: url(<?php print $themeurl.'images/ie_old/magnifier_old.png';?>) no-repeat center center;
  <?php endif; ?>
}

#search-results<?php echo $module->id; ?>{
  position: absolute;
  top:0px;
  left:0px;
  margin-top: 2px;
  visibility: hidden;
  text-decoration: none;
  z-index:1000;
  font-size:12px;
  width: <?php print $searchresultwidth;?>px;
}

#offlajn-ajax-tile-results{
  overflow: hidden;
  position: relative;
  -webkit-touch-callout: none;
  -webkit-user-select: none;
  -khtml-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
/*  transition: opacity 0.25s ease-out 0s;
  -moz-transition: opacity 0.25s ease-out 0s;
  -webkit-transition: opacity 0.25s ease-out 0s;
  -o-transition: opacity 0.25s ease-out 0s; */
}

#offlajn-ajax-tile-results.hidde{
  opacity:0;
}

#offlajn-ajax-tile-loading{
  text-align: center;
}

#offlajn-ajax-tile-loading .offlajn-ajax-search-control-panel{
  width: 100px;
  height: 90px;
  display: inline-block;
  -webkit-transition: all 200ms ease-out;
  -moz-transition: all 200ms ease-out;
  -o-transition: all 200ms ease-out;
  transition: all 200ms ease-out;
}

#offlajn-ajax-tile-results .offlajn-ajax-search-control-panel{
  min-height: 38px;
  border: none;
  <?php print $helper->generateBackground($this->params->get('controlinnerbg'),"", "", 1);?>
  -webkit-touch-callout: none;
  -webkit-user-select: none;
  -khtml-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

#offlajn-ajax-tile-results .offlajn-ajax-search-control-panel .offlajn-close-button{
  float:right;
  height:26px;
  width:26px;
  margin:6px;
  line-height:24px;
  text-align: center;
  background-color: rgba(255,255,255,0);
  border-radius: 13px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.6) , 0 1px 2px rgba(255,255,255, 0.95) inset;
  cursor: pointer;
  -webkit-transition: all 200ms ease-out;
  -moz-transition: all 200ms ease-out;
  -o-transition: all 200ms ease-out;
  transition: all 200ms ease-out;
}

#offlajn-ajax-tile-results .offlajn-ajax-search-control-panel .offlajn-close-button:hover{
  background-color: #ffffff;
}

#offlajn-ajax-tile-results .offlajn-ajax-search-control-panel .offlajn-close-button:active{
  background-color: rgba(255,255,255,0.1);
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.5) inset, 0 1px 2px rgba(255,255,255, 0.95);
}


#offlajn-ajax-tile-results .offlajn-ajax-search-control-panel .inner-control-panel{
  height:38px;
/*  width:100%; */
}

#offlajn-ajax-tile-results .offlajn-ajax-search-control-panel .inner-control-panel .offlajn-button{
  height:38px;
  width:38px;
  line-height:38px;
  background-color: rgba(255,255,255,0);

  text-align: center;
  float: left;
  -webkit-transition: all 100ms ease-out;
  -moz-transition: all 100ms ease-out;
  -o-transition: all 100ms ease-out;
  transition: all 100ms ease-out;
  cursor: pointer;
}

#offlajn-ajax-tile-results .offlajn-ajax-search-control-panel .offlajn-close-button,
#offlajn-ajax-tile-results .offlajn-ajax-search-control-panel .inner-control-panel .offlajn-button{
  /*font chooser*/
  <?php $f = $controlpanelfont; ?>
  color: #<?php echo $f[6]?>;
  font-family: <?php echo ($f[0] ? '"'.$f[2].'"':'').($f[1] && $f[0] ? ',':'').$f[1];?>;
  font-weight: <?php echo $f[4]? 'bold' : 'normal';?>;
  font-style: <?php echo $f[5]? 'italic' : 'normal';?>;
  font-size: <?php echo $f[3]?>;
  <?php if($f[7]): ?>
  text-shadow: <?php echo $f[11]?> <?php echo $f[8]?> <?php echo $f[9]?> <?php echo $f[10]?>;
  <?php else: ?>
  text-shadow: none;
  <?php endif; ?>
  text-decoration: <?php echo $f[12]?>;
  text-transform: <?php echo $f[13]?>;
  text-align: center;
  /*font chooser*/
}

#offlajn-ajax-tile-results .offlajn-ajax-search-control-panel .inner-control-panel .offlajn-button:active,
#offlajn-ajax-tile-results .offlajn-ajax-search-control-panel .inner-control-panel .pushed{
  background-color: rgba(255,255,255,0.1);
    /*font chooser*/
  <?php $f = $controlbpushedfont; ?>
  color: #<?php echo $f[6]?>;
  font-family: <?php echo ($f[0] ? '"'.$f[2].'"':'').($f[1] && $f[0] ? ',':'').$f[1];?>;
  font-weight: <?php echo $f[4]? 'bold' : 'normal';?>;
  font-style: <?php echo $f[5]? 'italic' : 'normal';?>;
  font-size: <?php echo $f[3]?>;
  <?php if($f[7]): ?>
  text-shadow: <?php echo $f[11]?> <?php echo $f[8]?> <?php echo $f[9]?> <?php echo $f[10]?>;
  <?php else: ?>
  text-shadow: none;
  <?php endif; ?>

}


#offlajn-ajax-tile-results .offlajn-ajax-search-control-panel .inner-control-panel .offlajn-prev,
#offlajn-ajax-tile-results .offlajn-ajax-search-control-panel .inner-control-panel .offlajn-next{
  width: auto;
  padding: 0 14px;
  <?php print $helper->generateBackground("00000099","", "", 1);?>
}

#offlajn-ajax-tile-results .offlajn-ajax-search-control-panel .inner-control-panel .offlajn-next{
  float: right;
}

#offlajn-ajax-tile-results .offlajn-ajax-search-control-panel .inner-control-panel .offlajn-paginators{
  height:38px;
  overflow: hidden;
}

#offlajn-ajax-tile-results .offlajn-ajax-search-control-panel .inner-control-panel .offlajn-button:hover{
  <?php print $helper->generateBackground($this->params->get('controlbuttonhoverbg'),"", "", 1);?>
}


#offlajn-ajax-tile-results{
  -moz-perspective: 1500px;
  -webkit-perspective: 1500px;
  perspective: 1500px;
  -moz-transition: height 300ms ease-out 0s;
  -webkit-transition: height 300ms ease-out 0s;
  transition: height 300ms ease-out 0s;
}

#offlajn-ajax-search-page-out{
  position: absolute;
  z-index: 100;
  transform-style: preserve-3d;
  -moz-transform-style: preserve-3d;
  -webkit-transform-style: preserve-3d;
  transform: rotateY(0deg);
  -ms-transform:rotateY(0deg); /* IE 9 */
  -moz-transform:rotateY(0deg); /* Firefox */
  -webkit-transform:rotateY(0deg); /* Safari and Chrome */
  -o-transform:rotateY(0deg); /* Opera */
  backface-visibility: hidden;
  opacity:1;

  transition: transform 0.5s ease-out 0s, opacity 0.5s ease-out 0s;
  -moz-transition: -moz-transform 0.5s ease-out 0s, opacity 0.5s ease-out 0s; /* Firefox 4 */
  -webkit-transition: -webkit-transform 0.5s ease-out 0s, opacity 0.5s ease-out 0s; /* Safari and Chrome */
  -o-transition: -o-transform 0.5s ease-out 0s, opacity 0.5s ease-out 0s; /* Opera */

}

#offlajn-ajax-search-page-out.flipleft{
  transform-origin: left 0 0;
  -ms-transform-origin: left 0 0;
  -moz-transform-origin: left 0 0;
  -webkit-transform-origin: left 0 0;
  -o-transform-origin: left 0 0;

  -o-transform: translate3D(-400px,0,0);
  -moz-transform: translate3D(-400px,0,0);
  -webkit-transform: translate3D(-400px,0,0) rotateY(60deg);
  transform: translate3D(-400px,0,0) rotateY(60deg);

  z-index: 900;
  opacity:0;
}

#offlajn-ajax-search-page-out.flipright{
  transform-origin: right 0 0;
  -ms-transform-origin: right 0 0;
  -moz-transform-origin: right 0 0;
  -webkit-transform-origin: right 0 0;
  -o-transform-origin: right 0 0;
/*  right:0px;*/
  -o-transform: translate3D(400px,0,0);
  -moz-transform: translate3D(400px,0,0);
  -webkit-transform: translate3D(400px,0,0) rotateY(-60deg);
  transform: translate3D(400px,0,0) rotateY(-60deg);

/*  transform: rotateY(100deg);*/
  z-index: 900;
  opacity:0;
}


#offlajn-ajax-search-results-inner{
  position: absolute;
  width: 100%;
}

#offlajn-ajax-tile-results .search-result-link{
  display: inline-block;
  float: left;
}

#offlajn-ajax-tile-results .search-result-link:hover,
#offlajn-ajax-tile-results .search-result-link:active,
#offlajn-ajax-tile-results .search-result-link:focus{
  background: transparent;
}

#offlajn-ajax-tile-results .search-result-link .search-result-card-category{
  /*font chooser*/
  <?php $f = $resultcategoryfont; ?>
  color: #<?php echo $f[6]?>;
  font-family: <?php echo ($f[0] ? '"'.$f[2].'"':'').($f[1] && $f[0] ? ',':'').$f[1];?>;
  font-weight: <?php echo $f[4]? 'bold' : 'normal';?>;
  font-style: <?php echo $f[5]? 'italic' : 'normal';?>;
  font-size: <?php echo $f[3]?>;
  <?php if($f[7]): ?>
  text-shadow: <?php echo $f[11]?> <?php echo $f[8]?> <?php echo $f[9]?> <?php echo $f[10]?>;
  <?php else: ?>
  text-shadow: none;
  <?php endif; ?>
  text-decoration: <?php echo $f[12]?>;
  text-transform: <?php echo $f[13]?>;
  line-height: <?php echo $f[14]?>;
  /*font chooser*/

  display: inline-block;
  padding: 0 5px;
  <?php print $helper->generateBackground($this->params->get('resultcontentbg'),"", "", 1);?>

  -moz-box-shadow: 0 -4px 3px -4px rgba(0, 0, 0, 0.9) inset;
  -webkit-box-shadow: 0 -4px 3px -4px rgba(0, 0, 0, 0.9) inset;
  box-shadow: 0 -4px 3px -4px rgba(0, 0, 0, 0.9) inset;
  opacity:0;
  -moz-transform: perspective(1500px) translate3d(0,20px,0);
  -webkit-transform: perspective(1500px) translate3d(0,20px,0);
  transform: perspective(1500px) translate3d(0,20px,0);
  -moz-transition: all 500ms ease-out 500ms;
  -webkit-transition: all 500ms ease-out 500ms;
  transition: all 500ms ease-out 500ms;
  z-index:0;
}

#offlajn-ajax-tile-results .search-result-link .search-result-card-category.show{
  -moz-transform: perspective(1500px) translate3d(0,0,0);
  -webkit-transform: perspective(1500px) translate3d(0,0,0);
  transform: perspective(1500px) translate3d(0,0,0);
  opacity:1;
}

#offlajn-ajax-tile-results .search-result-card.minimized{
/*  -webkit-transform: none;
  -moz-transform: scale(0);
  transform: scale(0);*/

  -moz-transform: perspective(1500px) translate3d(0,0,-300px);
  -webkit-transform: perspective(1500px) translate3d(0,0,-300px) rotateZ(-5deg) rotateY(-5deg);
  transform: perspective(1500px) translate3d(0,0,-300px) rotateZ(-5deg) rotateY(-5deg);
  opacity:0;
}

#offlajn-ajax-tile-results .search-result-card{
  z-index:50;
  opacity:1;
  margin: 12px;
  border: none;
  -moz-box-shadow: 0 1px 1px -1px rgba(0, 0, 0, 0.2);
  -webkit-box-shadow: 0 1px 1px -1px rgba(0, 0, 0, 0.2);
  box-shadow: 0 1px 1px -1px rgba(0, 0, 0, 0.2);
  display: inline-block;

  -webkit-transition: all 0.3s ease-out 0s;
  -moz-transition: all 0.3s ease-out 0s;
  transition: all 0.3s ease-out 0s;
  position: relative;
  overflow: hidden;
  -webkit-transform: perspective(1500px) translate3d(0,0,0) rotateZ(0deg);
  -moz-transform: perspective(1500px) translate3d(0,0,0) rotateZ(0deg) rotateY(0deg);
  transform: perspective(1500px) translate3d(0,0,0)  rotateZ(0deg) rotateY(0deg);
}

#offlajn-ajax-tile-results .search-result-card:before{
  position: absolute;
  content: "";
  box-shadow: 0 0 1px rgba(0,0,0,0.3) inset;
  width: 100%;
  height: 100%;
}

/*TODO IE7  WIDTH-FIX  INLINE-BLOCK*/
.dj_ie7 #offlajn-ajax-tile-results .search-result-card{
  width:<?php echo $resultimagewidth?>px;
  display: inline;
}

/*
#offlajn-ajax-tile-results .search-result-card.clicked{
  transform-origin: center center;
  opacity:0;
}
*/

#offlajn-ajax-tile-results .search-result-card img{
  width:<?php echo $resultimagewidth?>px;
  height:<?php echo $resultimageheight?>px;
  display: inline-block;
}

#offlajn-ajax-tile-results .search-result-card .search-result-content{
  bottom: 0;
  position: absolute;
  width: 100%;
  <?php print $helper->generateBackground($this->params->get('resultcontentbg'),"", "", 1);?>
  padding:5px;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
  overflow: hidden;
/*  transition: all 150ms ease-out;*/
}

#offlajn-ajax-tile-results .search-result-link .search-result-card .search-result-content{
  transition: all 400ms ease-out;
  opacity:0.99;
}

#offlajn-ajax-tile-results .search-result-link .search-result-card .search-result-overlay{
  width: 100%;
  height: 100%;
  position: absolute;
  opacity:0;
  -moz-transition: all 400ms cubic-bezier(0.230, 1.000, 0.320, 1.000);
  -webkit-transition: all 400ms cubic-bezier(0.230, 1.000, 0.320, 1.000);
  transition: all 400ms cubic-bezier(0.230, 1.000, 0.320, 1.000);
  -moz-transform: translate3d(0,-10px,0);
  -webkit-transform: translate3d(0,-10px,0);
  transform: translate3d(0,-10px,0);
  z-index: 10;
}

#offlajn-ajax-tile-results .search-result-link .search-result-card .search-result-beacon{
  width:100px;
  height:100px;
  background-color: transparent;
  top:0;
  bottom: 0;
  left:0;
  right: 0;
  margin: auto;
  position: absolute;
}

#offlajn-ajax-tile-results .search-result-link .search-result-card .search-result-beacon .beaconCircle1{
  border-radius: 50%;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.4);
  <?php print $helper->generateBackground($this->params->get('openresultbg'),"", "", 1);?>
  -moz-transition: all 300ms cubic-bezier(0.230, 1.000, 0.320, 1.000);
  -webkit-transition: all 300ms cubic-bezier(0.230, 1.000, 0.320, 1.000);
  transition: all 300ms cubic-bezier(0.230, 1.000, 0.320, 1.000);
}

#offlajn-ajax-tile-results .search-result-link .search-result-card .search-result-beacon .beaconCircle2{
  top:0;
  bottom: 0;
  left:0;
  right: 0;
  margin: auto;
  position: absolute;
  border-radius: 50%;
  width: 75%;
  height: 75%;
  <?php print $helper->generateBackground("000000b3","", "", 1);?>
  -moz-transition: all 300ms ease-out;
  -webkit-transitiontransition: all 300ms ease-out;
  transition: all 300ms ease-out;
}
#offlajn-ajax-tile-results .search-result-link .search-result-card .search-result-beacon .imgbeacon{
  width: 100%;
  height: 100%;
  top:0;
  bottom: 0;
  left:0;
  right: 0;
  margin: auto;
  <?php if($this->params->get('searchbuttonimage') != -1 && file_exists(dirname(__FILE__).'/images/search_button/'.$this->params->get('searchbuttonimage'))): ?>
  background: url(<?php print $themeurl.'images/search_button/'.$this->params->get('searchbuttonimage');?>) no-repeat center center;
  <?php endif; ?>
  background-size: 30% 30%;
  position: absolute;
}

#offlajn-ajax-tile-results .search-result-link .search-result-card .search-result-beacon:hover .beaconCircle1,
#offlajn-ajax-tile-results .search-result-link .search-result-card.clicked .search-result-beacon .beaconCircle1{
  -moz-transform: matrix(1.5, 0, 0, 1.5, 0, 0);
  -webkit-transform: matrix(1.5, 0, 0, 1.5, 0, 0);
  transform: matrix(1.5, 0, 0, 1.5, 0, 0);
  opacity:0;
}

#offlajn-ajax-tile-results .search-result-link .search-result-card .search-result-beacon:hover .beaconCircle2,
#offlajn-ajax-tile-results .search-result-link .search-result-card.clicked .search-result-beacon .beaconCircle2{
  -moz-transform: matrix(0.95, 0, 0, 0.95, 0, 0);
  -webkit-transform: matrix(0.95, 0, 0, 0.95, 0, 0);
  transform: matrix(0.95, 0, 0, 0.95, 0, 0);
  opacity:0.7;
}


#offlajn-ajax-tile-results .search-result-link:hover .search-result-card .search-result-overlay,
#offlajn-ajax-tile-results .search-result-link .search-result-card.clicked .search-result-overlay{
  -moz-transform: translate3d(0,0,0);
  -webkit-transform: translate3d(0,0,0);
  transform: translate3d(0,0,0);
  opacity:1;
}

#offlajn-ajax-tile-results .search-result-link:hover .search-result-card .search-result-content,
#offlajn-ajax-tile-results .search-result-link .search-result-card.clicked .search-result-content{
  opacity:0;
}


#offlajn-ajax-tile-results .search-result-card .search-result-content .blurred{
  position: absolute;
  bottom: 0;
  margin-left: -5px;
}

.dj_ie9 #offlajn-ajax-tile-results .search-result-card .search-result-content .blurred{
  margin-left: -7px;
}


#offlajn-ajax-tile-results .search-result-card .search-result-title{
  display: block;
  width: auto;
  <?php if($this->params->get('intro', 1)): ?>
  margin-bottom:4px;
  <?php endif; ?>
}

#offlajn-ajax-tile-results .search-result-card .search-result-title > span{
  display: block;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;

  /*font chooser*/
  <?php $f = $resulttitlefont; ?>
  color: #<?php echo $f[6]?>;
  font-family: <?php echo ($f[0] ? '"'.$f[2].'"':'').($f[1] && $f[0] ? ',':'').$f[1];?>;
  font-weight: <?php echo $f[4]? 'bold' : 'normal';?>;
  font-style: <?php echo $f[5]? 'italic' : 'normal';?>;
  font-size: <?php echo $f[3]?>;
  <?php if($f[7]): ?>
  text-shadow: <?php echo $f[11]?> <?php echo $f[8]?> <?php echo $f[9]?> <?php echo $f[10]?>;
  <?php else: ?>
  text-shadow: none;
  <?php endif; ?>
  text-decoration: <?php echo $f[12]?>;
  text-transform: <?php echo $f[13]?>;
  line-height: <?php echo $f[14]?>;
  text-align: <?php echo $f[15]?>;
  /*font chooser*/
  letter-spacing: -0.3px;
  text-indent: 0.1em;
}

#offlajn-ajax-tile-results .search-result-card .search-result-divider{
  margin:8px 0px 5px 0px;
  height: 1px;
  background-color: #c9c9c9;
  border-bottom:1px solid #ffffff;
  width: 100%;
}

#offlajn-ajax-tile-results .search-result-card .search-result-inner{
  width: auto;
  /*font chooser*/
  <?php $f = $resultintrotextfont; ?>
  color: #<?php echo $f[6]?>;
  font-family: <?php echo ($f[0] ? '"'.$f[2].'"':'').($f[1] && $f[0] ? ',':'').$f[1];?>;
  font-weight: <?php echo $f[4]? 'bold' : 'normal';?>;
  font-style: <?php echo $f[5]? 'italic' : 'normal';?>;
  font-size: <?php echo $f[3]?>;
  <?php if($f[7]): ?>
  text-shadow: <?php echo $f[11]?> <?php echo $f[8]?> <?php echo $f[9]?> <?php echo $f[10]?>;
  <?php else: ?>
  text-shadow: none;
  <?php endif; ?>
  text-decoration: <?php echo $f[12]?>;
  text-transform: <?php echo $f[13]?>;
  line-height: <?php echo $f[14]?>;
  /*font chooser*/

  text-align: justify;
}

#offlajn-ajax-tile-results .search-result-card .search-result-ajax-loader{
  opacity:0;
}

#offlajn-ajax-tile-results .search-result-card.clicked .search-result-ajax-loader{
  width:85px;
  height:0px;
  position: absolute;
  bottom:50px;
  padding: <?php echo $this->params->get('resultspadding')?>px;
  left:<?php echo ($resultimagewidth)/2?>px; /*(180-85) /2 */
  -webkit-transition:opacity 0.2s ease-out 0s;
  -moz-transition:opacity 0.2s ease-out 0s;
  transition:opacity 0.2s ease-out 0s;
  opacity:1;
}

#offlajn-ajax-tile-results .search-result-card.clicked .search-result-ajax-loader-inner{
  position: relative;
}

#offlajn-ajax-tile-results .search-result-link .search-result-card.clicked .search-result-beacon .imgbeacon{
  animation: loader 1s infinite linear;
  -webkit-animation: loader 1s infinite linear;
  background-image: url(<?php print $themeurl.'images/loaders/'.$this->params->get('ajaxloaderimage');?>);
}

@keyframes loader {
  0% {
    transform : perspective(1000px) rotateZ(0deg) translateZ(0px);
    transform-origin: 50% 50% 0;
  }
  50% {
    transform : perspective(1000px) rotateZ(180deg) translateZ(100px);
    transform-origin: 50% 50% 0
  }
  100% {
    transform : perspective(1000px) rotateZ(360deg) translateZ(0px);
    transform-origin: 50% 50% 0
  }
}

@-webkit-keyframes loader {
  0% {
    -webkit-transform : perspective(1000px) rotateZ(0deg) translateZ(0px);
    -webkit-transform-origin: 50% 50% 0;
  }
  50% {
    -webkit-transform : perspective(1000px) rotateZ(180deg) translateZ(100px);
    -webkit-transform-origin: 50% 50% 0
  }
  100% {
    -webkit-transform : perspective(1000px) rotateZ(360deg) translateZ(0px);
    -webkit-transform-origin: 50% 50% 0
  }
}

#offlajn-ajax-tile-results .search-result-link .search-result-card.clicked .search-result-title,
#offlajn-ajax-tile-results .search-result-link .search-result-card.clicked .search-result-divider,
#offlajn-ajax-tile-results .search-result-link .search-result-card.clicked .search-result-inner{
  visibility: hidden;
  -webkit-transition:all 0.3s;
  -moz-transition:all 0.3s;
  transition:all 0.3s;
  opacity:0;
}

#offlajn-ajax-tile-results .search-result-card .search-result-price{
  position: absolute;
  top: <?php echo $this->params->get('pricedist', 5) ?>px;
  right: <?php echo $this->params->get('pricedist', 5) ?>px;
  padding: 0 5px;
  /*font chooser*/
  <?php $f = $pricefont; ?>
  color: #<?php echo $f[6]?>;
  font-family: <?php echo ($f[0] ? '"'.$f[2].'"':'').($f[1] && $f[0] ? ',':'').$f[1];?>;
  font-weight: <?php echo $f[4]? 'bold' : 'normal';?>;
  font-style: <?php echo $f[5]? 'italic' : 'normal';?>;
  font-size: <?php echo $f[3]?>;
  <?php if($f[7]): ?>
  text-shadow: #<?php echo $f[11]?> <?php echo $f[8]?> <?php echo $f[9]?> <?php echo $f[10]?>;
  <?php else: ?>
  text-shadow: none;
  <?php endif; ?>
  text-decoration: <?php echo $f[12]?>;
  text-transform: <?php echo $f[13]?>;
  line-height: <?php echo $f[14]?>;
  /*font chooser*/
  <?php print $helper->generateBackground($this->params->get('resultcontentbg'),"", "", 1);?>
  -webkit-transition:all 0.3s;
  -moz-transition:all 0.3s;
  transition:all 0.3s;
  -moz-transform: translate3d(0,0,0);
  -webkit-transform: translate3d(0,0,0);
  transform: translate3d(0,0,0);
}

#offlajn-ajax-tile-results .search-result-link:hover .search-result-card .search-result-price,
#offlajn-ajax-tile-results .search-result-link .search-result-card.clicked .search-result-price{
  opacity:0;
  -moz-transform: translate3d(50px,0,0);
  -webkit-transform: translate3d(50px,0,0);
  transform: translate3d(50px,0,0);
}

#offlajn-ajax-tile-results #no-result-message{
  padding: 10px;
  margin: 12px 0 0;
  border: none;
  border-radius: 0;
  display: block;
  <?php print $helper->generateBackground($this->params->get('controlinnerbg'),"", "", 1);?>
  -webkit-transition: all 0.15s ease-out 0s;
  -moz-transition: all 0.15s ease-out 0s;
  transition: all 0.15s ease-out 0s;
  position: relative;
}

#offlajn-ajax-tile-results #no-result-message span{
  display: block;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;

  /*font chooser*/
  <?php $f = $resulttitlefont; ?>
  color: #<?php echo $f[6]?>;
  font-family: <?php echo ($f[0] ? '"'.$f[2].'"':'').($f[1] && $f[0] ? ',':'').$f[1];?>;
  font-weight: <?php echo $f[4]? 'bold' : 'normal';?>;
  font-style: <?php echo $f[5]? 'italic' : 'normal';?>;
  font-size: <?php echo $f[3]?>;
  <?php if($f[7]): ?>
  text-shadow: #<?php echo $f[11]?> <?php echo $f[8]?> <?php echo $f[9]?> <?php echo $f[10]?>;
  <?php else: ?>
  text-shadow: none;
  <?php endif; ?>
  text-decoration: <?php echo $f[12]?>;
  text-transform: <?php echo $f[13]?>;
  line-height: <?php echo $f[14]?>;
  /*font chooser*/
  letter-spacing: -0.3px;
  text-indent: 0.1em;
  text-align: center;
}

#offlajn-ajax-tile-results div.no-result-suggest{
  height:26px;
  line-height:26px;
  margin:6px 12px 6px 0px;
  padding: 2px 11px 2px 26px;
  border-radius: 0;
  text-align: center;
  float: left;
  -webkit-transition: all 200ms ease-out;
  -moz-transition: all 200ms ease-out;
  -o-transition: all 200ms ease-out;
  transition: all 200ms ease-out;
  cursor: pointer;

  <?php print $helper->generateBackground($this->params->get('controlinnerbg'),"", "", 1);?>
  border: none;

  /*font chooser*/
  <?php $f = $controlpanelfont; ?>
  color: #<?php echo $f[6]?>;
  font-family: <?php echo ($f[0] ? '"'.$f[2].'"':'').($f[1] && $f[0] ? ',':'').$f[1];?>;
  font-weight: <?php echo $f[4]? 'bold' : 'normal';?>;
  font-style: <?php echo $f[5]? 'italic' : 'normal';?>;
  font-size: <?php echo $f[3]?>;
  <?php if($f[7]): ?>
  text-shadow: <?php echo $f[11]?> <?php echo $f[8]?> <?php echo $f[9]?> <?php echo $f[10]?>;
  <?php else: ?>
  text-shadow: none;
  <?php endif; ?>
  text-decoration: <?php echo $f[12]?>;
  text-transform: <?php echo $f[13]?>;
  position: relative;
}

#offlajn-ajax-tile-results div.no-result-suggest:after{
  position: absolute;
  content: "";
  width: 100%;
  height: <?php echo $f[14]?>;
  left:0;
  background: url(<?php print ($themeurl.'images/tags/link.png');?>) no-repeat left center;
  background-size: auto 100%;
}

.dj_ie7 #offlajn-ajax-tile-results div.no-result-suggest,
.dj_ie8 #offlajn-ajax-tile-results div.no-result-suggest{
  padding: 2px 11px;
}

.dj_ie7 #offlajn-ajax-tile-results div.no-result-suggest:after,
.dj_ie8 #offlajn-ajax-tile-results div.no-result-suggest:after{
  display: none;
}

#offlajn-ajax-tile-results div.no-result-suggest:hover{
  -webkit-transition: background 200ms ease-out;
  -moz-transition: background 200ms ease-out;
  -o-transition: background 200ms ease-out;
  transition: background 200ms ease-out;
  background: #000000;
}

.ajax-clear{
  clear: both;
}

#search-categories<?php echo $module->id; ?>{
  border-radius: 0px;
  <?php print $helper->generateBackground($this->params->get('catchooserbg'),"", "", 1);?>
  padding: 4px 10px;
  position: absolute;
  top:0px;
  left:0px;
  visibility: hidden;
  text-decoration: none;
  font-size:12px;
  border: 1px solid #bfbfbf;
  border: 1px solid rgba(0,0,0,0.15);
  box-shadow: 0 0 1px rgba(0,0,0,0.1);
  display: inline-block;
  z-index: 10000;
}

#search-categories<?php echo $module->id; ?> .search-categories-inner div{
  padding:6px 10px 6px 40px;
  margin: 5px 0;
  border-bottom: 1px rgba(255,255,255,0.4) solid;

  cursor: default;
  /*font chooser*/
  <?php $f = $catchooserfont; ?>
  color: #<?php echo $f[6]?>;
  font-family: <?php echo ($f[0] ? '"'.$f[2].'"':'').($f[1] && $f[0] ? ',':'').$f[1];?>;
  font-weight: <?php echo $f[4]? 'bold' : 'normal';?>;
  font-style: <?php echo $f[5]? 'italic' : 'normal';?>;
  font-size: <?php echo $f[3]?>;
  <?php if($f[7]): ?>
  text-shadow: #<?php echo $f[11]?> <?php echo $f[8]?> <?php echo $f[9]?> <?php echo $f[10]?>;
  <?php else: ?>
  text-shadow: none;
  <?php endif; ?>
  text-decoration: <?php echo $f[12]?>;
  text-transform: <?php echo $f[13]?>;
  line-height: <?php echo $f[14]?>;
  /*font chooser*/

  background: url(<?php print ($themeurl.'images/selections/unselected.png');?>) no-repeat left center;
  background-size: auto 90%;
  -webkit-touch-callout: none;
  -webkit-user-select: none;
  -khtml-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  -o-user-select: none;
  user-select: none;
  -webkit-transition: color 300ms ease-out;
  -moz-transition: color 300ms ease-out;
  -o-transition: color 300ms ease-out;
  transition: color 300ms ease-out;
}

.dj_ie7 #search-categories<?php echo $module->id; ?> .search-categories-inner div,
.dj_ie8 #search-categories<?php echo $module->id; ?> .search-categories-inner div{
  background: url(<?php print ($themeurl.'images/ie_old/unselected_old.png');?>) no-repeat left center;
}

#search-categories<?php echo $module->id; ?> .search-categories-inner div.last{
  border:none;
}

#search-categories<?php echo $module->id; ?> .search-categories-inner div.selected{
  background: url(<?php print ($themeurl.'images/selections/selected.png');?>) no-repeat left center;
  background-size: auto 90%;
}

.dj_ie7 #search-categories<?php echo $module->id; ?> .search-categories-inner div.selected,
.dj_ie8 #search-categories<?php echo $module->id; ?> .search-categories-inner div.selected{
  background: url(<?php print ($themeurl.'images/ie_old/selected_old.png');?>) no-repeat left center;
}


#search-categories<?php echo $module->id; ?> .search-categories-inner div:hover{
  color: rgba(255,255,255,0.7);
}

/*100% width*/
#offlajn-ajax-tile-results .search-result-link{
  width:100%;
  line-height:0;
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
}

#offlajn-ajax-tile-results .search-result-card{
  width: 100%;
  margin: 0;
}

#offlajn-ajax-tile-results .search-result-card img{
  width: 100%;
  height: auto;
}

#offlajn-ajax-tile-results .search-result-link.search-result-sep{
  width:1%;
  height: 100%;
  display: inline-block;
  float:left;
}

.dj_ie7 #offlajn-ajax-tile-results div.search-result-sep,
.dj_ie8 #offlajn-ajax-tile-results div.search-result-sep{
  width: 0;
}

@media only screen and (min-width: 1400px){
  #offlajn-ajax-tile-results .search-result-link{
    width: <?php print (100-($resoponsive[5]-1)*1) / $resoponsive[5] ?>%;
    line-height:0;
    padding: 10px 0 0;
  }

  #offlajn-ajax-tile-results div.search-result-sep:nth-child(<?php print $resoponsive[5]*2 ?>n){
    width: 0;
  }
}

@media only screen and (min-width: 1025px) and (max-width: 1399px) {
  #offlajn-ajax-tile-results .search-result-link{
    width: <?php print (100-($resoponsive[4]-1)*1) / $resoponsive[4] ?>%;
    line-height:0;
    padding: 10px 0 0;
  }

  #offlajn-ajax-tile-results div.search-result-sep:nth-child(<?php print $resoponsive[4]*2 ?>n){
    width: 0;
  }

}

@media only screen and (min-width: 769px) and (max-width: 1024px) {
  #offlajn-ajax-tile-results .search-result-link{
    width: <?php print (100-($resoponsive[3]-1)*1) / $resoponsive[3] ?>%;
    line-height:0;
    padding: 10px 0 0;
  }

  #offlajn-ajax-tile-results div.search-result-sep:nth-child(<?php print $resoponsive[3]*2 ?>n){
    width: 0;
  }

}

@media only screen and (min-width: 569px) and (max-width: 768px) {
  #offlajn-ajax-tile-results .search-result-link{
    width: <?php print (100-($resoponsive[2]-1)*1) / $resoponsive[2] ?>%;
    line-height:0;
    padding: 10px 0 0;
  }

  #offlajn-ajax-tile-results div.search-result-sep:nth-child(<?php print $resoponsive[2]*2 ?>n){
    width: 0;
  }

}

@media only screen and (min-width: 469px) and (max-width: 568px) {
  #offlajn-ajax-tile-results .search-result-link{
    width: <?php print (100-($resoponsive[1]-1)*1) / $resoponsive[1] ?>%;
    line-height:0;
    padding: 10px 0 0;
  }

  #offlajn-ajax-tile-results div.search-result-sep:nth-child(<?php print $resoponsive[1]*2 ?>n){
    width: 0;
  }

}

@media only screen and (max-width: 468px) {
  #offlajn-ajax-tile-results .search-result-link{
    width: <?php print (100-($resoponsive[0]-1)*1) / $resoponsive[0] ?>%;
    line-height:0;
    padding: 10px 0 0;
  }

  #offlajn-ajax-tile-results div.search-result-sep:nth-child(<?php print $resoponsive[0]*2 ?>n){
    width: 0;
  }

}
