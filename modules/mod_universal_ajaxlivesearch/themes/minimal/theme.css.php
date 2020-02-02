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

$controlpanelradius = $this->params->get('controlpanelradius', 5);
$controlpinnerradius = $this->params->get('controlpinnerradius', 3);
$controlpbuttonradius = $this->params->get('controlpbuttonradius', 4);

$controlbpusheddropshadow = $this->params->get('controlbpusheddropshadow', '0 1px 2px rgba(255,255,255, 0.95)');
$controlbpushedinnershadow = $this->params->get('controlbpushedinnershadow', '0 1px 2px rgba(0, 0, 0, 0.5)');

$resultimagewidth = $this->params->get('imagew', 180);
$resultimageheight = $this->params->get('imageh', 140);

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
  padding: <?php echo intval($this->params->get('borderw', 4)); ?>px;
  margin:0;
}

#offlajn-ajax-search<?php echo $module->id; ?> .offlajn-ajax-search-container.active{
/*  background-color: #<?php print $this->params->get('highlightboxcolor');?>;*/
  border-radius: 20px;
}

#search-form<?php echo $module->id; ?> div{
  margin:0;
  padding:0;
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
  border-radius:15px;
}

.dj_ie #search-form<?php echo $module->id; ?> input{
  padding-top:0px;
}

#search-form<?php echo $module->id; ?> input:focus{
/*  background-color: #FFFFFF;*/
}

.dj_ie7 #search-form<?php echo $module->id; ?>{
  padding-bottom:0px;
}

#search-form<?php echo $module->id; ?> .category-chooser{
  height: 17px;
  width: 23px;
/*  border-left: 1px #dadada solid;*/
/*  border-right: none;*/
  background-color: transparent;
  position: absolute;
  right: 3px;
  top:5px;
  z-index: 5;
  cursor: pointer;
}

#search-form<?php echo $module->id; ?> .category-chooser:hover{
  -webkit-transition: background 200ms ease-out;
  -moz-transition: background 200ms ease-out;
  -o-transition: background 200ms ease-out;
  transition: background 200ms ease-out;
/*  background-color: #ffffff;*/
}

#search-form<?php echo $module->id; ?> .category-chooser.opened{
  height:26px;
  border-bottom: none;
  -moz-border-radius-bottomleft: 0px;
  border-bottom-left-radius: 0px;
  top:1px;
}

#search-form<?php echo $module->id; ?> .category-chooser.opened .arrow{
  height: 26px;
}

#search-form<?php echo $module->id; ?> .category-chooser .arrow{
  height: 17px;
  width: 23px;
  background: no-repeat center center;
  background-image: url('<?php echo $this->cacheUrl.$helper->NewColorizeImage(dirname(__FILE__).'/images/arrow/arrow.png', $this->params->get('categoryopenercolor'), '548722'); ?>');
}

input#search-area<?php echo $module->id; ?>{
  display: block;
  position: relative;
  height: 27px;
  padding: 0 30px 0 30px;
  width: 100%;
  box-sizing: border-box !important; /* css3 rec */
  -moz-box-sizing: border-box !important; /* ff2 */
  -ms-box-sizing: border-box !important; /* ie8 */
  -webkit-box-sizing: border-box !important; /* safari3 */
  -khtml-box-sizing: border-box !important; /* konqueror */
  background-color: transparent;

  border: 1px #bfbfbf solid;
  border-color: #<?php print $this->params->get('borderboxcolor');?>;
/*  border:none;*/
  line-height: 27px;
  z-index:4;
  top:0px;
  float: left;
  margin: 0;

  /*if category chooser enabled*/

  <?php if($this->params->get('catchooser')):?>
  padding-left:28px;
  <?php endif; ?>

  -moz-box-shadow: 0 1px 2px rgba(0,0,0,0.15) inset, 0 1px 1px rgba(255,255,255,0.8);
  -webkit-box-shadow: 0 1px 2px rgba(0,0,0,0.15) inset, 0 1px 1px rgba(255,255,255,0.8);
  box-shadow: 0 1px 2px rgba(0,0,0,0.15) inset, 0 1px 1px rgba(255,255,255,0.8);
}

.dj_ie #search-area<?php echo $module->id; ?>{
  line-height: 25px;
  border:none;
  top:1px;
}

.dj_ie7 #search-area<?php echo $module->id; ?>{
  height: 25px;
  line-height: 25px;
}

input#suggestion-area<?php echo $module->id; ?>{
  display: block;
  position: absolute;
  height: 27px;
  padding: 0 60px 0 30px;
  width: 100%;
  box-sizing: border-box !important; /* css3 rec */
  -moz-box-sizing: border-box !important; /* ff2 */
  -ms-box-sizing: border-box !important; /* ie8 */
  -webkit-box-sizing: border-box !important; /* safari3 */
  -khtml-box-sizing: border-box !important; /* konqueror */
  color:rgba(0, 0, 0, 0.25);

  border: 1px #bfbfbf solid;
  line-height: 27px;
  z-index:1;

  -webkit-box-shadow: none;
  -moz-box-shadow: none;
  box-shadow: none;

  float: left;
  margin: 0;

  /*if category chooser enabled*/

  <?php if($this->params->get('catchooser')):?>
  padding-left:28px;
  <?php endif; ?>
  top:0px;
}

.dj_ie8 input#suggestion-area<?php echo $module->id; ?>{
  line-height: 25px;
}

.dj_ie7 input#suggestion-area<?php echo $module->id; ?>{
  height: 26px;
  line-height: 25px;
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
}

#search-form<?php echo $module->id; ?> #search-area-close<?php echo $module->id; ?>{
  <?php if($this->params->get('closeimage') != -1 && file_exists(dirname(__FILE__).'/images/close/'.$this->params->get('closeimage'))): ?>
  background: url(<?php print $themeurl.'images/close/'.$this->params->get('closeimage');?>) no-repeat center center;
  background-image: url('<?php echo $this->cacheUrl.$helper->NewColorizeImage(dirname(__FILE__).'/images/close/'.$this->params->get('closeimage'), $this->params->get('closeimagecolor') , '548722'); ?>');
  <?php endif; ?>
  height: 16px;
  width: 22px;
  top:50%;
  margin-top:-8px;
  right: 5px;
  <?php if($this->params->get('catchooser', 0)){ ?>
  right: 28px;
  <?php } ?>
  position: absolute;
  cursor: pointer;
  visibility: hidden;
  z-index:5;
}

#ajax-search-button<?php echo $module->id; ?>{
<?php
  $gradient = explode('-', $this->params->get('searchbuttongradient'));
  ob_start();
  include(dirname(__FILE__).'/images/bgbutton.svg.php');
  $operagradient = ob_get_contents();
  ob_end_clean();
?>
  height: 27px;
  width: 30px;

  background: transparent;
  float: left;
  cursor: pointer;
  position: absolute;
  top: 0px;
  left: 0px;
  z-index:5;
}

.dj_ie7 #ajax-search-button<?php echo $module->id; ?>{
  top: 1px;
  right: -1px;
}

.dj_opera #ajax-search-button<?php echo $module->id; ?>{
  border-radius: 0;
}

#ajax-search-button<?php echo $module->id; ?> .magnifier{
  <?php if($this->params->get('searchbuttonimage') != -1 && file_exists(dirname(__FILE__).'/images/search_button/'.$this->params->get('searchbuttonimage'))): ?>
  background: url(<?php print $themeurl.'images/search_button/'.$this->params->get('searchbuttonimage');?>) no-repeat center center;
  <?php endif; ?>
  height: 27px;
  width: 30px;
  padding:0;
  margin:0;
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
  transition: opacity 0.25s ease-out 0s;
  -moz-transition: opacity 0.25s ease-out 0s; /* Firefox 4 */
  -webkit-transition: opacity 0.25s ease-out 0s; /* Safari and Chrome */
  -o-transition: opacity 0.25s ease-out 0s; /* Opera */
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
<?php
  $gradient = explode('-', $this->params->get('controlbggradient'));
  $gradientimg = $helper->generateGradient(1, 40, $gradient[1], $gradient[2], 'vertical');
?>

  min-height: 38px;

  padding: <?php echo $this->params->get('controlpanelpadding')?>px;
  margin: 12px;
  border: 1px solid #bfbfbf;
  border: 1px solid rgba(0,0,0,0.25);

  -webkit-border-radius: <?php echo $controlpanelradius?>px;
  border-radius: <?php echo $controlpanelradius?>px;

  box-shadow: 0 0 1px rgba(0,0,0,0.2);
  background: -moz-linear-gradient(center top , #FFFFFF, #F7F7F7);

  <?php if($gradient[0] == 1): ?>
  background: #<?php echo $gradient[2]; ?> url('<?php echo $this->cacheUrl.$gradientimg; ?>') repeat-x ;
  background-size: auto 100%;
  background: -moz-linear-gradient(#<?php echo $gradient[1];?>, #<?php echo $gradient[2]; ?>); /* FF 3.6+ */
  background: -ms-linear-gradient(#<?php echo $gradient[1];?>, #<?php echo $gradient[2]; ?>); /* IE10 */
  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #<?php echo $gradient[1];?>), color-stop(100%, #<?php echo $gradient[2]; ?>)); /* Safari 4+, Chrome 2+ */
  background: -webkit-linear-gradient(#<?php echo $gradient[1];?>, #<?php echo $gradient[2]; ?>); /* Safari 5.1+, Chrome 10+ */
  background: -o-linear-gradient(#<?php echo $gradient[1];?>, #<?php echo $gradient[2]; ?>); /* Opera 11.10 */
  background: linear-gradient( top, #<?php echo $gradient[1];?>, #<?php echo $gradient[2]; ?> );
  <?php else: ?>
  background: none;
  <?php endif; ?>


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
  background-color: #f5f5f5;
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
  background-color: #DDDDDD;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.5) inset, 0 1px 2px rgba(255,255,255, 0.95);
}


#offlajn-ajax-tile-results .offlajn-ajax-search-control-panel .inner-control-panel{
  background-color: #<?php print $this->params->get('controlinnerbg');?>;

  -webkit-border-radius: <?php echo $controlpinnerradius?>px;
  border-radius: <?php echo $controlpinnerradius?>px;

  height:38px;
  margin-right: 48px;
/*  width:100%; */
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.5) inset, 0 1px 2px rgba(255,255,255, 0.95);
}

#offlajn-ajax-tile-results .offlajn-ajax-search-control-panel .inner-control-panel .offlajn-button{
  height:26px;
  width:26px;
  line-height:26px;
  margin:6px 0 6px 6px;
  background-color: #<?php print $this->params->get('controlbuttonbg');?>;

  -webkit-border-radius: <?php echo $controlpbuttonradius?>px;
  border-radius: <?php echo $controlpbuttonradius?>px;

  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.5), 0 1px 1px rgba(255, 255, 255, 0.65) inset;

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
<?php
  $gradient = explode('-', $this->params->get('controlbuttonpushedbg'));
  $gradientimg = $helper->generateGradient(1, 40, $gradient[1], $gradient[2], 'vertical');
?>

  background: -moz-linear-gradient(center top , #e3e3e3, #cfcfcf);
  background-image: -webkit-linear-gradient(top, #e3e3e3 0%, #cfcfcf 100%);

  <?php if($gradient[0] == 1): ?>
  background: #<?php echo $gradient[2]; ?> url('<?php echo $this->cacheUrl.$gradientimg; ?>') repeat-x ;
  background-size: auto 100%;
  background: -moz-linear-gradient(#<?php echo $gradient[1];?>, #<?php echo $gradient[2]; ?>); /* FF 3.6+ */
  background: -ms-linear-gradient(#<?php echo $gradient[1];?>, #<?php echo $gradient[2]; ?>); /* IE10 */
  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #<?php echo $gradient[1];?>), color-stop(100%, #<?php echo $gradient[2]; ?>)); /* Safari 4+, Chrome 2+ */
  background: -webkit-linear-gradient(#<?php echo $gradient[1];?>, #<?php echo $gradient[2]; ?>); /* Safari 5.1+, Chrome 10+ */
  background: -o-linear-gradient(#<?php echo $gradient[1];?>, #<?php echo $gradient[2]; ?>); /* Opera 11.10 */
  background: linear-gradient( top, #<?php echo $gradient[1];?>, #<?php echo $gradient[2]; ?> );
  <?php else: ?>
  background: none;
  <?php endif; ?>

  box-shadow: <?php echo $controlbpushedinnershadow?> inset, <?php echo $controlbpusheddropshadow?>;

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
  margin:6px;
  width: auto;
  padding: 0 14px;
}

#offlajn-ajax-tile-results .offlajn-ajax-search-control-panel .inner-control-panel .offlajn-next{
  float: right;
}

#offlajn-ajax-tile-results .offlajn-ajax-search-control-panel .inner-control-panel .offlajn-paginators{
  height:38px;
  overflow: hidden;
}

#offlajn-ajax-tile-results .offlajn-ajax-search-control-panel .inner-control-panel .offlajn-button:hover{
  background-color: #<?php print $this->params->get('controlbuttonhoverbg');?>;
}


#offlajn-ajax-tile-results{
  -moz-perspective: 1500px;
  -webkit-perspective: 1500px;
  perspective: 1500px;
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

  transform: rotateY(-100deg);
  -ms-transform:rotateY(-100deg); /* IE 9 */
  -moz-transform:rotateY(-100deg); /* Firefox */
  -webkit-transform:rotateY(-100deg); /* Safari and Chrome */
  -o-transform:rotateY(-100deg); /* Opera */
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

  transform: rotateY(100deg);
  -ms-transform:rotateY(100deg); /* IE 9 */
  -moz-transform:rotateY(100deg); /* Firefox */
  -webkit-transform:rotateY(100deg); /* Safari and Chrome */
  -o-transform:rotateY(100deg); /* Opera */
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

#offlajn-ajax-tile-results .search-result-card.minimized{
  -webkit-transform: none;
  -moz-transform: scale(0);
  transform: scale(0);
  opacity:0;
}

#offlajn-ajax-tile-results .search-result-card{
<?php
  $gradient = explode('-', $this->params->get('cardbggradient'));
  $gradientimg = $helper->generateGradient(1, 300, $gradient[1], $gradient[2], 'vertical');
?>

  opacity:1;

  padding: <?php echo $this->params->get('resultspadding')?>px;
  margin: 12px;
  border: 1px solid #bfbfbf;
  border: 1px solid rgba(0,0,0,0.25);
  border-radius: 3px;
  -moz-box-shadow: 0 1px 1px -1px rgba(0, 0, 0, 0.2);
  -webkit-box-shadow: 0 1px 1px -1px rgba(0, 0, 0, 0.2);
  box-shadow: 0 1px 1px -1px rgba(0, 0, 0, 0.2);
  display: inline-block;

  <?php if($gradient[0] == 1): ?>
  background: #<?php echo $gradient[2]; ?> url('<?php echo $this->cacheUrl.$gradientimg; ?>') repeat-x ;
  background-size: auto 100%;
  background: -moz-linear-gradient(#<?php echo $gradient[1];?>, #<?php echo $gradient[2]; ?>); /* FF 3.6+ */
  background: -ms-linear-gradient(#<?php echo $gradient[1];?>, #<?php echo $gradient[2]; ?>); /* IE10 */
  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #<?php echo $gradient[1];?>), color-stop(100%, #<?php echo $gradient[2]; ?>)); /* Safari 4+, Chrome 2+ */
  background: -webkit-linear-gradient(#<?php echo $gradient[1];?>, #<?php echo $gradient[2]; ?>); /* Safari 5.1+, Chrome 10+ */
  background: -o-linear-gradient(#<?php echo $gradient[1];?>, #<?php echo $gradient[2]; ?>); /* Opera 11.10 */
  background: linear-gradient( top, #<?php echo $gradient[1];?>, #<?php echo $gradient[2]; ?> );
  <?php else: ?>
  background: none;
  <?php endif; ?>

  -webkit-transition: all 0.15s ease-out 0s;
  -moz-transition: all 0.15s ease-out 0s;
  transition: all 0.15s ease-out 0s;
  position: relative;
/*  background: url("/modules/mod_universal_ajaxlivesearch/themes/minimal/images/patterns/ricepaper2.png") no-repeat center center transparent;*/

  -webkit-transform: translate3d(0,0,0);
  -moz-transform: translate3d(0,0,0);
  transform: translate3d(0,0,0);
}

/*TODO IE7  WIDTH-FIX  INLINE-BLOCK*/
.dj_ie7 #offlajn-ajax-tile-results .search-result-card{
  width:<?php echo $resultimagewidth?>px;
  display: inline;
}

#offlajn-ajax-tile-results .search-result-link:hover .search-result-card{
  -moz-box-shadow: 0 8px 4px -5px rgba(0, 0, 0, 0.25);
  -webkit-box-shadow: 0 8px 4px -5px rgba(0, 0, 0, 0.25);
  box-shadow: 0 8px 4px -5px rgba(0, 0, 0, 0.25);
  -webkit-transform: translate3d(0,-5px,0);
  -moz-transform: translate3d(0,-5px,0);
  transform: translate3d(0,-5px,0);
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

#offlajn-ajax-tile-results .search-result-card .search-result-image-shadow{
  width:<?php echo $resultimagewidth?>px;
  height:<?php echo $resultimageheight?>px;
  position: absolute;
  top:5px
  right:5px;
  background-color: transparent;
  border-radius: 2px;
  -moz-box-shadow: 0 0 <?php echo $this->params->get('resultsimageshadow',5)?>px rgba(0,0,0,0.8) inset, 0 0 2px rgba(255,255,255,0.8);
  -webkit-box-shadow: 0 0 <?php echo $this->params->get('resultsimageshadow',5)?>px rgba(0,0,0,0.8) inset, 0 0 2px rgba(255,255,255,0.8);
  box-shadow: 0 0 <?php echo $this->params->get('resultsimageshadow',5)?>px rgba(0,0,0,0.8) inset, 0 0 2px rgba(255,255,255,0.8);
  z-index:10;
}


#offlajn-ajax-tile-results .search-result-card img{
  border-radius: 2px;
}

#offlajn-ajax-tile-results .search-result-card .search-result-title{
  display: block;
  width: <?php echo $resultimagewidth?>px;
}

#offlajn-ajax-tile-results .search-result-card .search-result-title > span{
  display: block;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  margin-top:7px;

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
  letter-spacing: -0.1em;
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
  height: <?php echo $this->params->get('resultintrominheight',90)?>px;
  width: <?php echo $resultimagewidth?>px;
  /*font chooser*/
  <?php $f = $resultintrotextfont; ?>
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

#offlajn-ajax-tile-results .search-result-card.clicked .search-result-ajax-loader-inner > div {
  width: 6px;
  height: 20px;
  position: absolute;
  left: -10px;
  bottom: 15px;
  border-radius: 5px;
  transform-origin: 10px 35px;
  transform: rotate(0deg);
  animation: loader 0.8s infinite;
  -webkit-transform-origin: 10px 35px;
  -webkit-transform: rotate(0deg);
  -webkit-animation: loader 0.8s infinite;

}
#offlajn-ajax-tile-results .search-result-card.clicked .search-result-ajax-loader-inner > div:nth-child(2) {
  transform: rotate(45deg);
  animation-delay: 0.1s;
  -webkit-transform: rotate(45deg);
  -webkit-animation-delay: 0.1s;
}
#offlajn-ajax-tile-results .search-result-card.clicked .search-result-ajax-loader-inner > div:nth-child(3) {
  transform: rotate(90deg);
  animation-delay: 0.2s;
  -webkit-transform: rotate(90deg);
  -webkit-animation-delay: 0.2s;
}
#offlajn-ajax-tile-results .search-result-card.clicked .search-result-ajax-loader-inner > div:nth-child(4) {
  transform: rotate(135deg);
  animation-delay: 0.3s;
  -webkit-transform: rotate(135deg);
  -webkit-animation-delay: 0.3s;
}
#offlajn-ajax-tile-results .search-result-card.clicked .search-result-ajax-loader-inner > div:nth-child(5) {
  transform: rotate(180deg);
  animation-delay: 0.4s;
  -webkit-transform: rotate(180deg);
  -webkit-animation-delay: 0.4s;
}
#offlajn-ajax-tile-results .search-result-card.clicked .search-result-ajax-loader-inner > div:nth-child(6) {
  transform: rotate(225deg);
  animation-delay: 0.5s;
  -webkit-transform: rotate(225deg);
  -webkit-animation-delay: 0.5s;
}
#offlajn-ajax-tile-results .search-result-card.clicked .search-result-ajax-loader-inner > div:nth-child(7) {
  transform: rotate(270deg);
  animation-delay: 0.6s;
  -webkit-transform: rotate(270deg);
  -webkit-animation-delay: 0.6s;
}
#offlajn-ajax-tile-results .search-result-card.clicked .search-result-ajax-loader-inner > div:nth-child(8) {
  transform: rotate(315deg);
  animation-delay: 0.7s;
  -webkit-transform: rotate(315deg);
  -webkit-animation-delay: 0.7s;
}

@keyframes loader {
  0% {
    background: transparent;
    left: -10px;
    transform-origin: 10px 35px;
  }
  30% {
    background: #444;
  }
  100% {
    background: transparent;
    left: 10px;
    transform-origin: -10px 35px;
  }
}

@-webkit-keyframes loader {
  0% {
    background: transparent;
    left: -10px;
    -webkit-transform-origin: 10px 35px;
  }
  30% {
    background: #444;
  }
  100% {
    background: transparent;
    left: 10px;
    -webkit-transform-origin: -10px 35px;
  }
}


/*FLIP*/
 <?php if($this->params->get('flip')): ?>
#offlajn-ajax-tile-results .search-result-link .search-result-card.clicked{
  -webkit-transform-style:preserve-3d;
  -moz-transform-style:preserve-3d;
  -webkit-transition:all 0.5s;
  -moz-transition:all 0.5s;
  -webkit-transform:rotateY(180deg);
  -moz-transform:rotateY(180deg)
}
 <?php endif; ?>

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
  top: <?php echo $this->params->get('pricedisttop', 17) ?>px;
  right: <?php echo $this->params->get('pricedistright', -5) ?>px;
  padding: 0 5px;
  border-radius: 3px;
  z-index: 100;
  
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
  background-color: #<?php print $this->params->get('pricebg');?>;
}

/*
#offlajn-ajax-tile-results .search-result-link:hover .search-result-card .search-result-price,
#offlajn-ajax-tile-results .search-result-link .search-result-card.clicked .search-result-price{
  opacity:0;
  -moz-transform: translate3d(50px,0,0);
  -webkit-transform: translate3d(50px,0,0);
  transform: translate3d(50px,0,0);
}
*/

#offlajn-ajax-tile-results #no-result-message{
  padding: 10px;
  margin: 12px;
  border: 1px solid #bfbfbf;
  border: 1px solid rgba(0,0,0,0.25);
  border-radius: 3px;
  box-shadow: 0 0 1px rgba(0,0,0,0.2);
  display: block;
  background: -moz-linear-gradient(center top , #FFFFFF, #F7F7F7);
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
  letter-spacing: -0.1em;
  text-indent: 0.1em;
  text-align: center;
}

#offlajn-ajax-tile-results div.no-result-suggest{
  height:26px;
  line-height:26px;
  margin:6px 0 6px 12px;
  padding:0 6px;
  background-color: #f5f5f5;
  border-radius: 4px;
  text-align: center;
  float: left;
  -webkit-transition: all 200ms ease-out;
  -moz-transition: all 200ms ease-out;
  -o-transition: all 200ms ease-out;
  transition: all 200ms ease-out;
  cursor: pointer;

  background: -moz-linear-gradient(center top , #FFFFFF, #F7F7F7);
  box-shadow: 0 0 1px rgba(0,0,0,0.1);
  border: 1px solid #bfbfbf;
  border: 1px solid rgba(0,0,0,0.2);
}

#offlajn-ajax-tile-results div.no-result-suggest:hover{
  -webkit-transition: background 200ms ease-out;
  -moz-transition: background 200ms ease-out;
  -o-transition: background 200ms ease-out;
  transition: background 200ms ease-out;
  background-color: #ffffff;
  background:#ffffff;
}

#offlajn-ajax-tile-results div.no-result-suggest:active{
  background: -moz-linear-gradient(center top , #e3e3e3, #cfcfcf);
  background-image: -webkit-linear-gradient(top, #e3e3e3 0%, #cfcfcf 100%);
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.5) inset, 0 1px 2px rgba(255,255,255, 0.95);
}

.ajax-clear{
  clear: both;
}

#search-categories<?php echo $module->id; ?>{
  border: 1px #BFBFBF solid;
  border-radius: 20px;
  background-color: #fcfcfc;
  padding: 4px 10px;
  position: absolute;
  top:0px;
  left:0px;
  visibility: hidden;
  text-decoration: none;
  z-index:1001;
  font-size:12px;

  border: 1px solid #bfbfbf;
  border: 1px solid rgba(0,0,0,0.15);
  box-shadow: 0 0 1px rgba(0,0,0,0.1);
  display: inline-block;
  background: -moz-linear-gradient(center top , #FFFFFF, #F7F7F7);
}

#search-categories<?php echo $module->id; ?> .search-categories-inner div{
  padding:6px 30px 6px 15px;
  border-bottom: 1px #DBDBDB solid;
  box-shadow: 0 1px 1px #FFFFFF;

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

  background: url(<?php print ($themeurl.'images/selections/unselected.png');?>) no-repeat right center;
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

#search-categories<?php echo $module->id; ?> .search-categories-inner div.last{
  border:none;
}

#search-categories<?php echo $module->id; ?> .search-categories-inner div.selected{
  background: url(<?php print ($themeurl.'images/selections/selected.png');?>) no-repeat right center;
}

#search-categories<?php echo $module->id; ?> .search-categories-inner div:hover{
  color: #90A3B2;
}