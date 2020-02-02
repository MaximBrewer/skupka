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

$align = "";
if($this->params->get('searchareaalign') == 'center') {
  $align = "margin-left: auto; margin-right: auto;";
} else {
  $align = "float: ".$this->params->get('searchareaalign', 'left').";";
}
?>
#offlajn-ajax-search<?php echo $module->id; ?>{
  width: <?php print $searchareawidth; ?>;
  <?php echo $align; ?>
}

#offlajn-ajax-search<?php echo $module->id; ?> .offlajn-ajax-search-container{
  background-color: #<?php print $this->params->get('borderboxcolor');?>;
  padding: <?php echo intval($this->params->get('borderw', 4)); ?>px;
  margin:0;
}

#offlajn-ajax-search<?php echo $module->id; ?> .offlajn-ajax-search-container.active{
  background-color: #<?php print $this->params->get('highlightboxcolor');?>;
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
  border-left: 1px #dadada solid;
/*  border-right: none;*/
  background-color: transparent;
  position: absolute;
  right: 1px;
  top:5px;
  z-index: 5;
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
  background-color: #ffffff;
  top:1px;
  box-shadow: inset 0px 1px 1px rgba(0,0,0,0.15);
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

  -webkit-box-shadow: inset 0px 1px 2px rgba(0,0,0,0.15);
  -moz-box-shadow: inset 0px 1px 2px rgba(0,0,0,0.15);
  box-shadow: inset 0px 1px 2px rgba(0,0,0,0.15);

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
  border-left: 1px #cecece solid;

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

#ajax-search-button<?php echo $module->id; ?>:hover{

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

#search-results-moovable<?php echo $module->id; ?>{
  position: relative;
  overflow: hidden;
  height: 0px;
  background-color: #<?php print $this->params->get('resultcolor');?>;
  border: 1px #<?php print $this->params->get('resultbordercolor');?> solid;

<?php if($this->params->get('boxshadow')):?>
  -webkit-box-shadow: 3px 3px 3px rgba(0, 0, 0, 0.3);
  -moz-box-shadow: 3px 3px 3px rgba(0, 0, 0, 0.3);
  box-shadow: 3px 3px 3px rgba(0, 0, 0, 0.3);
<?php endif; ?>
}


#search-results-inner<?php echo $module->id; ?>{
  position: relative;
  width: <?php print $searchresultwidth;?>px; /**/
  overflow: hidden;
/*  padding-bottom: 10px;*/
}

.dj_ie #search-results-inner<?php echo $module->id; ?>{
  padding-bottom: 0px;
}

#search-results<?php echo $module->id; ?> .plugin-title{
  line-height: 27px;
  font-size: 11px;
  text-transform: uppercase;
  /* Firefox */
  background-color: #<?php print $this->params->get('plugintitlecolor');?>;
  color: #<?php print $this->params->get('plugintitlefontcolor');?>;
  text-shadow: 1px 1px 0px rgba(255, 255, 255, 0.8);
  text-align: left;
  border-top: 1px solid #<?php print $this->params->get('resultbordercolor');?>;
  font-weight: bold;
  height: 100%;
  margin:0;
  padding:0;
}

#search-results<?php echo $module->id; ?> .plugin-title.first{
  margin-top: -1px;
}

.dj_ie #search-results<?php echo $module->id; ?> .plugin-title.first{
  margin-top: 0;
}

#search-results<?php echo $module->id; ?> .ie-fix-plugin-title{
  border-top: 1px solid #B2BCC1;
  border-bottom: 1px solid #000000;
}


#search-results<?php echo $module->id; ?> .plugin-title-inner{
/* -moz-box-shadow:0 1px 2px #B2BCC1 inset;*/
  -moz-user-select:none;
  padding-left:10px;
  padding-right:5px;
  float: <?php echo ($this->params->get('resultalign', 0)) ? 'right' : 'left' ?>;
  cursor: default;

  /*font chooser*/
  <?php $f = $plugintitlefont; ?>
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
  text-align: center;
  /*font chooser*/
}

#search-results<?php echo $module->id; ?> .pagination{
  margin: 8px;
  margin-left: 0px;
  float: <?php echo ($this->params->get('resultalign', 0)) ? 'left' : 'right' ?>;
  width: auto;
  height: auto;
}

#search-results<?php echo $module->id; ?> .pager{
  width: 10px;
  height: 11px;
  margin: 0px 0px 0px 5px;
  <?php if(file_exists(dirname(__FILE__).'/images/paginators/default/dot.png')): ?>
  background: url(<?php print $themeurl.'images/paginators/default/'.$this->params->get('paginatorstyle');?>) no-repeat center center;
  <?php endif; ?>

  float: left;
  padding:0;
}

#search-results<?php echo $module->id; ?> .pager.active{

  <?php if(file_exists(dirname(__FILE__).'/images/paginators/selected/dot.png')): ?>
  background: url(<?php print $themeurl.'images/paginators/selected/'.$this->params->get('paginatorstyle');?>) no-repeat center center;
  <?php endif; ?>

  cursor: default;
}


#search-results<?php echo $module->id; ?> .page-container{
  position: relative;
  overflow: hidden;
  height: <?php print (intval($this->params->get('imageh', 60))+5)*$productsperplugin;?>px; /* 65px num of elements */
  width: <?php print $searchresultwidth;?>px; /**/
}

#search-results<?php echo $module->id; ?> .page-band{
  position: absolute;
  left: 0;
  width: 10000px;
}

#search-results<?php echo $module->id; ?> .page-element{
  float: left;
  left: 0;
  cursor: hand;
}

#search-results<?php echo $module->id; ?> #search-results-inner<?php echo $module->id; ?> .result-element:hover,
#search-results<?php echo $module->id; ?> #search-results-inner<?php echo $module->id; ?> .selected-element{
  text-decoration: none;
/*  color: #<?php print $this->params->get('activeresultfontcolor');?>;*/
  background-color: #<?php print $this->params->get('activeresultcolor');?>;

/*  border-top: 1px solid #188dd9;*/
}


#search-results<?php echo $module->id; ?> .result-element{
  display: block;
  width: <?php print $searchresultwidth;?>px; /**/
  height: <?php echo intval($this->params->get('imageh', 60))+4; ?>px; /*height*/
  font-weight: bold;
  border-top: 1px solid #<?php print $this->params->get('resultbordercolor');?>;
  overflow: hidden;
}

#search-results<?php echo $module->id; ?> .result-element img{
  display: block;
  float: <?php echo ($this->params->get('resultalign', 0)) ? 'right' : 'left' ?>;
  padding: 2px;
  padding-right:10px;
  border: 0;
}

.ajax-clear{
  clear: both;
}

#search-results<?php echo $module->id; ?> .result-element span{
  display: block;
  float: left;
  width: <?php print $searchresultwidth-17;?>px;   /*  margin:5+12 */
  margin-left:5px;
  margin-right:12px;
  line-height: 14px;
  text-align: <?php echo ($this->params->get('resultalign', 0)) ? 'right' : 'left' ?>;
  cursor: pointer;
  margin-top: 5px;

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

}

#search-results<?php echo $module->id; ?> .result-element span.small-desc{
  margin-top : 2px;
  font-weight: normal;
  line-height: 13px;
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
}

#search-results<?php echo $module->id; ?> .result-element:hover span.small-desc,
#search-results<?php echo $module->id; ?> .selected-element span.small-desc{
}

#search-results<?php echo $module->id; ?> .result-products span{
/*  text-align: center;*/
  width: <?php print $searchresultwidth-12-intval($this->params->get('imagew', 60))-17;?>px;   /* padding and pictures: 10+2+60, margin:5+12  */
  margin-top: 5px;
}

#search-results<?php echo $module->id; ?> .no-result{
  display: block;
  width: <?php print $searchresultwidth;?>px; /**/
  height: 30px; /*height*/
  font-weight: bold;
  border-top: 1px solid #<?php print $this->params->get('resultbordercolor');?>;
  overflow: hidden;
  text-align: center;
  padding-top:10px;
}

#search-results<?php echo $module->id; ?> .no-result-suggest {
  display: block;
  font-weight: bold;
  border-top: 1px solid #<?php print $this->params->get('resultbordercolor');?>;
  overflow: hidden;
  text-align: center;
  padding-top:10px;
  padding-bottom: 6px;
  padding-left: 5px;
  padding-right: 5px;
}

#search-results<?php echo $module->id; ?> .no-result-suggest a {
  cursor: pointer;
  font-weight: bold;
  text-decoration: none;
  padding-left: 4px;
}

#search-results<?php echo $module->id; ?> .no-result-suggest,
#search-results<?php echo $module->id; ?> .no-result-suggest a{
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
}

#search-results<?php echo $module->id; ?> .no-result-suggest a:hover {
  text-decoration: underline;
}

#search-results<?php echo $module->id; ?> .no-result span{
  width: <?php print $searchresultwidth-17;?>px;   /*  margin:5+12 */
  line-height: 20px;
  text-align: left;
  cursor: default;
  -moz-user-select:none;
}

#search-categories<?php echo $module->id; ?>{
  border: 1px #BFBFBF solid;
  border-top: none;
  background-color: #f2f2f2;
  position: absolute;
  top:0px;
  left:0px;
  visibility: hidden;
  text-decoration: none;
  z-index:1001;
  font-size:12px;
}

#search-categories<?php echo $module->id; ?> .search-categories-inner div{
  padding:6px 30px 6px 15px;
  border-bottom: 1px #BFBFBF solid;
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
}

#search-categories<?php echo $module->id; ?> .search-categories-inner div.last{
  border:none;
}

#search-categories<?php echo $module->id; ?> .search-categories-inner div.selected{
  background: url(<?php print ($themeurl.'images/selections/selected.png');?>) no-repeat right center;
  background-color: #ffffff;
}






/*
#search-results-inner<?php echo $module->id; ?>.withoutseemore{
  padding-bottom: 10px;
}*/

#search-results<?php echo $module->id; ?> .seemore{

  padding-top: 5px;
  padding-bottom: 5px;
  cursor: pointer;
  border-top: 1px solid #CDCDCD;
  background-color: #ffffff; /*f2f2f2*/
  text-align: center;
  padding-right: 10px;
}
#search-results<?php echo $module->id; ?> .seemore:hover{
  background-color: #ffffff;
}

#search-results<?php echo $module->id; ?> .seemore:hover span{
  /*font chooser*/
  <?php $f = $seemorefonthover; ?>
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
  text-align: center;
  /*font chooser*/

}

#search-results<?php echo $module->id; ?> .seemore span{
  /*font chooser*/
  <?php $f = $seemorefont; ?>
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
  text-align: center;
  /*font chooser*/

}