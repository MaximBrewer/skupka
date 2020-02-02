<?php
/*-------------------------------------------------------------------------
# mod_improved_ajax_login - Improved AJAX Login and Register
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2013 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php defined('_JEXEC') or die('Restricted access'); ?>

.strongFields {
  display: block;
  overflow: hidden;
  height: 7px;
  margin: 3px 0 -17px;
  background-color: #<?php echo $btngrad[1]?>;
	background-image: -moz-linear-gradient(left, #<?php echo $btngrad[1]?>, #<?php echo $hovergrad[2]?>);
  background-image: -o-linear-gradient(left, #<?php echo $btngrad[1]?>, #<?php echo $hovergrad[2]?>);
  background-image: -ms-linear-gradient(left, #<?php echo $btngrad[1]?>, #<?php echo $hovergrad[2]?>);
	background-image: -webkit-gradient(linear, left top, right top, from(#<?php echo $btngrad[1]?>), to(#<?php echo $hovergrad[2]?>));
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#<?php echo $btngrad[1]?>, endColorstr=#<?php echo $hovergrad[2]?>, GradientType=1);
}
.strongFields .strongField.empty {
  background-color: #<?php echo $popupcomb[0]?>;
  -webkit-transition: background-color 1.2s ease-out;
	-moz-transition: background-color 1.2s ease-out;
  -ms-transition: background-color 1.2s ease-out;
  -o-transition: background-color 1.2s ease-out;
	transition: background-color 1.2s ease-out;
}
.strongField.empty,
.strongField {
  display: block;
  background-color: transparent;
  width: 20%;
  height: 7px;
  float: left;
}
.loginWndInside {
  position: relative;
  display: inline-block;
  background-color: #<?php echo $popupcomb[0]?>;
/*  min-height: 175px; */
}

.loginH3 {
  <?php $fonts->printFont('titlefont', 'Text');?>
  padding: 10px 70px 10px 50px;
  position: relative;
  background-color: #<?php echo $btngrad[1]?>;
	background-image: -moz-linear-gradient(top, #<?php echo $btngrad[1]?>, #<?php echo $btngrad[2]?>);
  background-image: -o-linear-gradient(top, #<?php echo $btngrad[1]?>, #<?php echo $btngrad[2]?>);
  background-image: -ms-linear-gradient(top, #<?php echo $btngrad[1]?>, #<?php echo $btngrad[2]?>);
	background-image: -webkit-gradient(linear, left top, left bottom, from(#<?php echo $btngrad[1]?>), to(#<?php echo $btngrad[2]?>));
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#<?php echo $btngrad[1]?>, endColorstr=#<?php echo $btngrad[2]?>);
	-o-background-size: 100% 100%;
  margin: -20px -50px 20px -50px;
  -webkit-box-sizing: content-box;
  -moz-box-sizing: content-box;
  box-sizing: content-box;
}
.socialBody {
	background-color: #DCDCDC;
}

.loginH3 {
  margin: 0px -55px 20px -55px;  /*25 = 10 padding ezen + 15 margin az elemeken*/
}

.strongFields {
  margin: 3px 0 2px;
  margin: <?php echo ($txtcomb[2]-40)/2>0?(($txtcomb[2]-40)/2)+3:3;?>px 0 <?php echo ($txtcomb[2]-40)/2>0?2-(($txtcomb[2]-40)/2):2;?>px;
}
/*
.gi-elem{
  padding-top: 5px;
}    */

.ial-load {
  display: block;
	position: absolute;
	width: 21px;
	height: 21px;
  margin: 6px;
  background: transparent url(<?php echo $this->cacheUrl.$helper->NewColorizeImage(dirname(__FILE__)."/images/settings.png", $btnfont['Text']['color'], "407090")?>) no-repeat center;
  -webkit-animation:spin 4s linear infinite;
  -moz-animation:spin 4s linear infinite;
  animation:spin 4s linear infinite;
}
.ial-usermenu .ial-load {
	margin: 11px 0px;
  background-image: url(<?php echo $this->cacheUrl.$helper->NewColorizeImage(dirname(__FILE__)."/images/settings.png", $btngrad[2], "407090")?>);
}

.gi-elem .ial-load,
.gi-field-out{
  -moz-perspective: 200px;
  -webkit-perspective: 200px;
  perspective: 200px;
  width: <?php echo $txtcomb[2]+0;?>px;
  height: <?php echo $txtcomb[2]+0;?>px;
  margin-left: <?php echo (40- $txtcomb[2]+0)/2;?>px;
  margin-top: <?php echo (40- $txtcomb[2]+0)/2;?>px;
  position: absolute;
}

.loginBtn .ial-load {
  visibility: hidden;
	margin: 0 0 0 -28px;
  width: 28px;
  top: 0;
  height: 100%;
}


.gi-field-icon{
  width: <?php echo $txtcomb[2]+0;?>px;
  height: <?php echo $txtcomb[2]+0;?>px;
  position: absolute;

  background-color: #<?php echo $btngrad[1]?>;
	background-image: -moz-linear-gradient(left, #<?php echo $btngrad[1]?>, #<?php echo $hovergrad[2]?>);
  background-image: -o-linear-gradient(left, #<?php echo $btngrad[1]?>, #<?php echo $hovergrad[2]?>);
  background-image: -ms-linear-gradient(left, #<?php echo $btngrad[1]?>, #<?php echo $hovergrad[2]?>);
	background-image: -webkit-gradient(linear, left top, right top, from(#<?php echo $btngrad[1]?>), to(#<?php echo $hovergrad[2]?>));
/*	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#<?php echo $btngrad[1]?>, endColorstr=#<?php echo $hovergrad[2]?>, GradientType=1);*/
  -moz-transition: all 0ms ease 0s;
  -webkit-transition: all 0ms ease 0s;
  -ms-transition: all 0ms ease 0s;
  transition: all 0ms ease 0s;

  -ms-backface-visibility:hidden;
  -moz-backface-visibility:hidden;
  -webkit-backface-visibility:hidden;
  backface-visibility:hidden;
}

.gi-field-icon-hover{
  width: <?php echo $txtcomb[2]+0;?>px;
  height: <?php echo $txtcomb[2]+0;?>px;
  margin-top:0px;
  margin-left:0px;
  left:<?php echo -($txtcomb[2]+0);?>px;
  position: absolute;
  background-color: #<?php shift_color($btngrad[1],-50)?>;
  -moz-transform: rotateY(-90deg);
  -webkit-transform: rotateY(-90deg);
  -ms-transform: rotateY(-90deg);
  transform: rotateY(-90deg);
  -ms-transform-origin: 100% 0 0;
  -moz-transform-origin: 100% 0 0;
  -webkit-transform-origin: 100% 0 0;
  transform-origin: 100% 0 0;
  transform-style: preserve-3d;
  outline:1px solid transparent;

  -ms-backface-visibility:hidden;
  -moz-backface-visibility:hidden;
  -webkit-backface-visibility:hidden;
  backface-visibility:hidden;
}

.gi-field-icon {
  -moz-transform-origin: 50% <?php echo ($txtcomb[2]+0)/2;?>px -<?php echo ($txtcomb[2]+0)/2;?>px;
  -webkit-transform-origin: 50% <?php echo ($txtcomb[2]+0)/2;?>px -<?php echo ($txtcomb[2]+0)/2;?>px;
  transform-origin: 50% <?php echo ($txtcomb[2]+0)/2;?>px -<?php echo ($txtcomb[2]+0)/2;?>px;
  -moz-transform-style: preserve-3d;
  -webkit-transform-style: preserve-3d;
  transform-style: preserve-3d;
  outline:1px solid transparent;
}

.gi-elem.ial-active .gi-field-icon{
  -moz-transform: rotateY(90deg);
  -webkit-transform: rotateY(90deg);
  transform: rotateY(90deg);

  -moz-transition-duration : 500ms;
  -webkit-transition-duration : 500ms;
  -ms-transition-duration : 500ms;
  transition-duration : 500ms;
}

@media screen and (-ms-high-contrast: active), (-ms-high-contrast: none) {
.gi-elem.ial-active .gi-field-icon{
  -ms-transform: rotateY(0deg);
  transform: rotateY(0deg);
}
}


.gi-elem.ial-active .gi-field-icon-hover{
  background-color: #<?php echo $btngrad[1]?>;
}



.gi-ie-10 .gi-field-icon-hover{
  -ms-transform: rotateY(0deg);
  transform: rotateY(0deg);
}

.gi-ie-10 .gi-elem.ial-active .gi-field-icon{
  transform: translateX(<?php echo $txtcomb[2]+0;?>px);
}

.gi-ie-7 .gi-field-out{
  display: none;
}

.gi-ie-8 .gi-field-out,
.gi-ie-9 .gi-field-out,
.gi-ie-10 .gi-field-out{
  overflow: hidden;
}

.gi-ie-9 .gi-elem.ial-active .gi-field-icon-hover{
  background-color: #<?php shift_color($popupcomb[2],-50)?>;
}

.gi-ie-9 .gi-elem.ial-active .gi-field-icon{
  left:<?php echo $txtcomb[2]+0;?>px;
}


.gi-user{
  background-position: center center;
  background-image: url(<?php echo $themeurl?>images/user.png);
  background-repeat: no-repeat;
}

.gi-passw{
  background-position: center center;
  background-image: url(<?php echo $themeurl?>images/pass.png);
  background-repeat: no-repeat;
}

.gi-key{
  background-position: center center;
  background-image: url(<?php echo $themeurl?>images/key.png);
  background-repeat: no-repeat;
}

.ial-email1 .gi-field-icon,
.ial-email1 .gi-field-icon-hover,
.ial-email2 .gi-field-icon,
.ial-email2 .gi-field-icon-hover,
.gi-ial-email1,
.gi-ial-email2{
  background-position: center center;
  background-image: url(<?php echo $themeurl?>images/email.png);
  background-repeat: no-repeat;
}

.ial-phone .gi-field-icon,
.ial-phone .gi-field-icon-hover,
.gi-ial-phone{
  background-position: center center;
  background-image: url(<?php echo $themeurl?>images/phone.png);
  background-repeat: no-repeat;
}

.ial-captcha .gi-field-icon,
.ial-captcha .gi-field-icon-hover,
.gi-ial-recaptcha_response_field{
  background-position: center center;
  background-image: url(<?php echo $themeurl?>images/pen.png);
  background-repeat: no-repeat;
}

.ial-dob .gi-field-icon,
.ial-dob .gi-field-icon-hover,
.gi-ial-dob{
  background-position: center center;
  background-image: url(<?php echo $themeurl?>images/date.png);
  background-repeat: no-repeat;
}

.ial-website .gi-field-icon,
.ial-website .gi-field-icon-hover,
.gi-ial-website{
  background-position: center center;
  background-image: url(<?php echo $themeurl?>images/website.png);
  background-repeat: no-repeat;
}

.ial-textarea .gi-field-icon,
.ial-textarea .gi-field-icon-hover,
.gi-ial-textarea{
  background-position: center center;
  background-image: url(<?php echo $themeurl?>images/about.png);
  background-repeat: no-repeat;
}

.ial-address1 .gi-field-icon,
.ial-address1 .gi-field-icon-hover,
.ial-address2 .gi-field-icon,
.ial-address2 .gi-field-icon-hover,
.gi-ial-address1,
.gi-ial-address2{
  background-position: center center;
  background-image: url(<?php echo $themeurl?>images/address.png);
  background-repeat: no-repeat;
}

.ial-country .gi-field-icon,
.ial-country .gi-field-icon-hover,
.gi-ial-country{
  background-position: center center;
  background-image: url(<?php echo $themeurl?>images/country.png);
  background-repeat: no-repeat;
}

.ial-city .gi-field-icon,
.ial-city .gi-field-icon-hover,
.gi-ial-city{
  background-position: center center;
  background-image: url(<?php echo $themeurl?>images/location3.png);
  background-repeat: no-repeat;
}

.ial-favoritebook .gi-field-icon,
.ial-favoritebook .gi-field-icon-hover,
.gi-ial-favoritebook{
  background-position: center center;
  background-image: url(<?php echo $themeurl?>images/book.png);
  background-repeat: no-repeat;
}

.ial-region .gi-field-icon,
.ial-region .gi-field-icon-hover,
.gi-ial-region {
  background-position: center center;
  background-image: url(<?php echo $themeurl?>images/location4.png);
  background-repeat: no-repeat;
}

.ial-postal_code .gi-field-icon,
.ial-postal_code .gi-field-icon-hover,
.gi-ial-postal_code{
  background-position: center center;
  background-image: url(<?php echo $themeurl?>images/location3.png);
  background-repeat: no-repeat;
}

.captchaCnt {
  text-align: center;
  *width: 215px;
  border: none;
  clear: both;
  padding: 2px;
  overflow: hidden;
  position: relative;
  margin: 0 0 6px;
  background: #fff;
  border: 1px solid #<?php echo $txtcomb[3]?>;
}

.captchaCnt .ial-reload{
  position: absolute;
  top:0;
  right:0;
  background: transparent;
  padding:3px;
}

.captchaCnt .ial-reload:hover{
  background: transparent;
  -webkit-animation:spin 1s linear infinite;
  -moz-animation:spin 1s linear infinite;
  animation:spin 1s linear infinite;
}

.captchaCnt .ial-reload:hover:active{
  background: transparent;
  -webkit-transition: none;
  -moz-transition: none;
  transition: none;
}

.ial-msg .red {
  display: none;
}
#recaptchaImg {
  display: block;
  width: 100%;
  min-height: 57px;
  max-width: 300px;
  margin: 0 auto;
  opacity: 0;
  transition: opacity .33s ease-in-out;
  -o-transition: opacity .33s ease-in-out;
  -ms-transition: opacity .33s ease-in-out;
  -moz-transition: opacity .33s ease-in-out;
  -webkit-transition: opacity .33s ease-in-out;
}
#recaptchaImg.fadeIn {
  min-height: 0;
	opacity: 1;
}
a.logBtn.selectBtn:hover {
  background-color: transparent;
}
.selectBtn {
  margin: 1px;
  white-space: nowrap;
}
.selectBtn:hover,
.loginBtn:hover {
  *text-decoration: none;
}
.btnIco {
  display: block;
  float: left;
  background: transparent no-repeat 1px center;
  width: 22px;
}

.loginBtn[data-oauth]{
  text-align: left;
}

.socialIco {
  cursor: pointer;
  width: 50px;
  height: 50px;
  display: inline-block;
  *display: block;
  *float: left;
  margin: 0 6px;
  text-align: left;
  -o-perspective: 200px;
  -moz-perspective: 200px;
  -webkit-perspective: 200px;
  perspective: 200px;
}

.socialIco:first-child {
  margin-left: 0;
}
.socialIco:last-child {
  margin-right: 0;
}
.socialImg {
  width: 50px;
  height: 50px;
  position: relative;
  -o-transform-origin: 50% 25px -25px;
  -moz-transform-origin: 50% 25px -25px;
  -webkit-transform-origin: 50% 25px -25px;
  transform-origin: 50% 25px -25px;
  -o-transform-style: preserve-3d;
  -moz-transform-style: preserve-3d;
  -webkit-transform-style: preserve-3d;
  transform-style: preserve-3d;
  background: #<?php echo $popupcomb[2]?>;
  outline:1px solid transparent;
  -ms-backface-visibility:hidden;
  -moz-backface-visibility:hidden;
  -webkit-backface-visibility:hidden;
  backface-visibility:hidden;
}

.socialIco:hover .socialImg{
  -o-transform: rotateX(90deg);
  -moz-transform: rotateX(90deg);
  -webkit-transform: rotateX(90deg);
  transform: rotateX(90deg);
}

.loginBtn,
.socialIco,
.socialImg,
.ial-close{
  -webkit-transition: all .3s ease-out;
	-moz-transition: all .3s ease-out;
  -ms-transition: all .3s ease-out;
  -o-transition: all .3s ease-out;
	transition: all .3s ease-out;
}

.socialIco:hover {
/*  background-color: #<?php echo $textfont['Hover']['color'] ?>;*/
}
.facebookImg {
  background-image:  url(<?php echo $themeurl?>images/social/fb.png);
  background-position: center;
  background-size:50px;
}
.googleImg {
  background-image:  url(<?php echo $themeurl?>images/social/google.png);
  background-position: center;
  background-size:50px;
}
.twitterImg {
  background-image:  url(<?php echo $themeurl?>images/social/twitter.png);
  background-position: center;
  background-size:50px;
}
.windowsImg {
  background-image:  url(<?php echo $themeurl?>images/social/wl.png);
  background-position: center;
  background-size:50px;
}
.linkedinImg {
  background-image: url(<?php echo $themeurl?>images/social/in.png);
  background-position: center;
  background-size:50px;
}
.vkImg {
  background-image: url(<?php echo $themeurl?>images/social/vk.png);
  background-position: center;
  background-size:50px;
}

.socialImgHover{
  background-color: #<?php shift_color($popupcomb[2],-50)?>;
  -o-transform: rotateX(-90deg);
  -moz-transform: rotateX(-90deg);
  -webkit-transform: rotateX(-90deg);
  -ms-transform: rotateX(-90deg);
  transform: rotateX(-90deg);
  -o-transform-origin: 50% 0 0;
  -ms-transform-origin: 50% 0 0;
  -moz-transform-origin: 50% 0 0;
  -webkit-transform-origin: 50% 0 0;
  transform-origin: 50% 0 0;
  -o-transition: background 0.3s ease 0s;
  -moz-transition: background 0.3s ease 0s;
  -webkit-transition: background 0.3s ease 0s;
  -ms-transition: background 0.3s ease 0s;
  transition: background 0.3s ease 0s;
  position: absolute;
  top: 100%;
  transform-style: preserve-3d;
  height:50px;
  width:50px;
  outline:1px solid transparent;
  -ms-backface-visibility:hidden;
  -moz-backface-visibility:hidden;
  -webkit-backface-visibility:hidden;
  backface-visibility:hidden;
}

.socialIco:hover .socialImgHover{
  background-color: #<?php echo $popupcomb[2]?>;
}


.gi-ie-10 .socialImgHover{
  -ms-transform: rotateX(0deg);
  transform: rotateX(0deg);
}

.gi-ie-10 .socialIco:hover .socialImg{
  transform: translateY(-50px);
}

.gi-ie-8 .socialIco,
.gi-ie-9 .socialIco,
.gi-ie-10 .socialIco{
  overflow: hidden;
}

.gi-ie-9 .socialIco:hover .socialImgHover{
  background-color: #<?php shift_color($popupcomb[2],-50)?>;
}

.gi-ie-9 .socialIco:hover .socialImg{
  top:-50px;
}


.loginBrd {
  clear: both;
  *text-align: center;
  position: relative;
  margin: 13px 0;
  height: 0;
  padding: 0;
  border: 0;
}
.loginBrd {
  border-bottom: 1px #<?php echo $popupcomb[2]?> solid;
  box-shadow:
		0px 1px 0px #<?php shift_color($popupcomb[0],20)?>;
	-moz-box-shadow:
		0px 1px 0px #<?php shift_color($popupcomb[0],20)?>;
	-webkit-box-shadow:
		0px 1px 0px #<?php shift_color($popupcomb[0],20)?>;
}
.loginOr {
  display: none;
  position: absolute;
  width: 20px;
  height: 15px;
  left: 50%;
  text-align: center;
  margin: -7px 0 0 -13px;
  border: 3px solid #<?php echo $popupcomb[0]?>;
  border-top: 0;
  background: #<?php echo $popupcomb[0]?>;
}
.ial-window .loginOr {
  display: block;
}

.ial-window ::selection {
  background-color: #<?php echo $btngrad[2] ?>;
  color: #<?php echo $txtcomb[1] ?>;
}

.ial-window ::-moz-selection {
  background-color: #<?php echo $btngrad[2] ?>;
  color: #<?php echo $txtcomb[1] ?>;
}

.ial-arrow-b,
.ial-arrow-l,
.ial-arrow-r {
  display: block;
  position: absolute;
  top: <?php echo $btnfont['Text']['size']/4+$buttoncomb[0]?>px;
  width: 0;
  height: 0;
  border: 9px transparent solid;
  border-left-width: 0;
  border-right-width: 6px;
}
.ial-arrow-l {
	left: -11px;
  border-right-color:  #<?php echo $errorgrad[1]?>;
}
.ial-arrow-r {
  right: -6px;
  border-width: 9px 0 9px 6px;
  border-left-color: #<?php echo $errorgrad[1]?>;
}
.ial-arrow-b {
  left: 4px;
  top: -6px;
  border-width: 0 9px 6px;
  border-bottom-color: #<?php echo $errorgrad[1]?>;
}
.inf .ial-arrow-l {
  border-right-color: #<?php shift_color($hintgrad[1]<$hintgrad[2]? $hintgrad[1] : $hintgrad[2], -50)?>;
}
.inf .ial-arrow-r {
  border-left-color: #<?php shift_color($hintgrad[1]<$hintgrad[2]? $hintgrad[1] : $hintgrad[2], -50)?>;
}
.inf .ial-arrow-b {
  border-bottom-color: #<?php shift_color($hintgrad[1]<$hintgrad[2]? $hintgrad[1] : $hintgrad[2], -50)?>;
}
.ial-msg {
  visibility: hidden;
  z-index: 10000;
  position: absolute;
	box-shadow:
		0px 1px 1px rgba(0,0,0,0.3);
	-moz-box-shadow:
		0px 1px 1px rgba(0,0,0,0.3);
	-webkit-box-shadow:
		0px 1px 1px rgba(0,0,0,0.3);
}
.ial-msg.inf {
  border: none;
  background-color: #<?php echo $hintgrad[1]?>;
	background-image: -moz-linear-gradient(top, #<?php echo $hintgrad[1]?>, #<?php echo $hintgrad[2]?>);
  background-image: -o-linear-gradient(top, #<?php echo $hintgrad[1]?>, #<?php echo $hintgrad[2]?>);
  background-image: -ms-linear-gradient(top, #<?php echo $hintgrad[1]?>, #<?php echo $hintgrad[2]?>);
	background-image: -webkit-gradient(linear, left top, left bottom, from(#<?php echo $hintgrad[1]?>), to(#<?php echo $hintgrad[2]?>));
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#<?php echo $hintgrad[1]?>, endColorstr=#<?php echo $hintgrad[2]?>);
}
.ial-msg.err {
  border: none;
  background-color: #<?php echo $errorgrad[1]?>;
	background-image: -moz-linear-gradient(top, #<?php echo $errorgrad[1]?>, #<?php echo $errorgrad[2]?>);
  background-image: -o-linear-gradient(top, #<?php echo $errorgrad[1]?>, #<?php echo $errorgrad[2]?>);
  background-image: -ms-linear-gradient(top, #<?php echo $errorgrad[1]?>, #<?php echo $errorgrad[2]?>);
	background-image: -webkit-gradient(linear, left top, left bottom, from(#<?php echo $errorgrad[1]?>), to(#<?php echo $errorgrad[2]?>));
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#<?php echo $errorgrad[1]?>, endColorstr=#<?php echo $errorgrad[2]?>);
}
span.ial-inf,
span.ial-err {
  position: relative;
  text-align: left;
  max-width: 360px;
  cursor: default;
  margin-left: 5px;
  padding: <?php echo $buttoncomb[0]+0?>px 8px <?php echo $buttoncomb[0]+0?>px 29px;
  text-decoration: none;
  color: #<?php echo $errorcolor?>;
}
span.ial-inf {
  color: #<?php echo $hintcolor?>;
  text-shadow: 1px 1px 0 rgba(255, 255, 255, 0.5);
}
div.ial-icon-err,
div.ial-icon-inf {
  width: 24px;
  position: absolute;
  left: 0;
  background: url(<?php echo $themeurl?>images/info.png) no-repeat scroll left center transparent;
}
div.ial-icon-err {
  background: url(<?php echo $themeurl?>images/error.png) no-repeat left center;
}
.ial-inf,
.ial-err,
.loginBtn span,
.loginBtn {
  display: inline-block;
  <?php $fonts->printFont('btnfont', 'Text'); ?>
}
.ial-icon-refr {
  display: block;
  width: 28px;
  height: 28px;
  background: url(<?php echo $themeurl?>images/refresh.png) no-repeat center center;
}
.facebookIco {
  background-image: url(<?php echo $this->cacheUrl.$helper->NewColorizeImage(dirname(__FILE__)."/images/f.png", $btnfont['Text']['color'], "2e3192")?>);
}
.googleIco {
  background-image: url(<?php echo $this->cacheUrl.$helper->NewColorizeImage(dirname(__FILE__)."/images/g.png", $btnfont['Text']['color'], "2e3192")?>);
}
.twitterIco {
  background-image: url(<?php echo $this->cacheUrl.$helper->NewColorizeImage(dirname(__FILE__)."/images/t.png", $btnfont['Text']['color'], "2e3192")?>);
}
.windowsIco {
  background-image: url(<?php echo $this->cacheUrl.$helper->NewColorizeImage(dirname(__FILE__)."/images/w.png", $btnfont['Text']['color'], "2e3192")?>);
}
.linkedinIco {
  background-image: url(<?php echo $this->cacheUrl.$helper->NewColorizeImage(dirname(__FILE__)."/images/i.png", $btnfont['Text']['color'], "2e3192")?>);
}
.vkIco {
  background-image: url(<?php echo $this->cacheUrl.$helper->NewColorizeImage(dirname(__FILE__)."/images/v.png", $btnfont['Text']['color'], "2e3192")?>);
}
.loginBtn::-moz-focus-inner {
  border:0;
  padding:0;
}
.loginBtn[type=button] {
  margin-bottom: 20px;
}
.loginBtn {
  position: relative;
  cursor: pointer;
  text-align: center;
	margin: 0;
	padding: <?php echo $buttoncomb[0]+0?>px <?php echo ($buttoncomb[0]*2)+2?>px;
	border: none;
}
.ial-modal:after {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 50px;
  height: 50px;
  box-sizing: border-box;
}
/*.socialIco:hover,*/
.ial-modal:after,
.ial-select:before,
.loginBtn,
.loginBtn:hover:active,
.selectBtn:hover .leftBtn {
  background-color: #<?php echo $btngrad[1]?>;
	background-image: -moz-linear-gradient(top, #<?php echo $btngrad[1]?>, #<?php echo $btngrad[2]?>);
  background-image: -o-linear-gradient(top, #<?php echo $btngrad[1]?>, #<?php echo $btngrad[2]?>);
  background-image: -ms-linear-gradient(top, #<?php echo $btngrad[1]?>, #<?php echo $btngrad[2]?>);
	background-image: -webkit-gradient(linear, left top, left bottom, from(#<?php echo $btngrad[1]?>), to(#<?php echo $btngrad[2]?>));
/*	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#<?php echo $btngrad[1]?>, endColorstr=#<?php echo $btngrad[2]?>);*/
	-o-background-size: 100% 100%;
}
.leftBtn {
  padding-left: <?php echo $buttoncomb[0]+2?>px;
  padding-right: <?php echo $buttoncomb[0]+2?>px;
}
.rightBtn {
  padding-left: 0px;
  padding-right: 0px;
	border-left-width: 0;
	letter-spacing: -2;
	background: url(<?php echo $themeurl?>images/arrow.png) no-repeat center center;
	background-color: #<?php echo $btngrad[1]?>;
	width: 28px;
}

.rightBtn img{
  vertical-align: middle;
}

.ial-select:hover:before,
.loginBtn:hover,
.selectBtn:hover .rightBtn,
.selectBtn.ial-active .rightBtn {
  background-color: #<?php echo $hovergrad[1]?>;
	background-image: -moz-linear-gradient(top, #<?php echo $hovergrad[1]?>, #<?php echo $hovergrad[2]?>);
  background-image: -o-linear-gradient(top, #<?php echo $hovergrad[1]?>, #<?php echo $hovergrad[2]?>);
  background-image: -ms-linear-gradient(top, #<?php echo $hovergrad[1]?>, #<?php echo $hovergrad[2]?>);
	background-image: -webkit-gradient(linear, left top, left bottom, from(#<?php echo $hovergrad[1]?>), to(#<?php echo $hovergrad[2]?>));
/*	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#<?php echo $hovergrad[1]?>, endColorstr=#<?php echo $hovergrad[2]?>);*/
}

.rightBtn:hover,
.selectBtn:hover .rightBtn,
.selectBtn.ial-active .rightBtn {
	background: url(<?php echo $themeurl?>images/arrow.png) no-repeat center center;
  background-color: #<?php echo $hovergrad[1]?>;
}

.ial-window,
.ial-usermenu {
  top: -10000px;
  margin: 0;
  position: absolute;
  z-index: 10000;
  padding: 0 0 <?php echo $popupcomb[1]+0?>px;
  background-color: #<?php echo $btngrad[1]?>;
	background-image: -moz-linear-gradient(top, #<?php echo $btngrad[1]?>, #<?php echo $btngrad[2]?>);
  background-image: -o-linear-gradient(top, #<?php echo $btngrad[1]?>, #<?php echo $btngrad[2]?>);
  background-image: -ms-linear-gradient(top, #<?php echo $btngrad[1]?>, #<?php echo $btngrad[2]?>);
	background-image: -webkit-gradient(linear, left top, left bottom, from(#<?php echo $btngrad[1]?>), to(#<?php echo $btngrad[2]?>));
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#<?php echo $btngrad[1]?>, endColorstr=#<?php echo $btngrad[2]?>);
	-o-background-size: 100% 100%;
	box-shadow:
		1px 1px 5px rgba(0, 0, 0, 0.4);
	-moz-box-shadow:
		1px 1px 5px rgba(0, 0, 0, 0.4);
	-webkit-box-shadow:
		1px 1px 5px rgba(0, 0, 0, 0.4);
	overflow: hidden;
}
.ial-usermenu .loginWndInside {
  padding: 5px 10px;
}
.ial-arrow-up {
  position: absolute;
  top: -14px;
}

.loginWndInside .ial-close {
  position: absolute;
  right: 0;
  top: 0;
  line-height: 0;
  margin: 0;
  cursor: pointer;
	z-index:10;
	border:none;
	padding: 20px;
	width: 50px;
	height: 50px;
  -ms-transform: translateX(88px) rotateZ(360deg);
  -moz-transform: translateX(88px) rotateZ(360deg);
  -webkit-transform: translateX(88px) rotateZ(360deg);
  transform: translateX(88px) rotateZ(360deg);
}
.loginWndInside .ial-close.loginBtn,
.loginWndInside .ial-close.loginBtn:hover:active {
  background-image: url(<?php echo $themeurl?>images/x.png);
  background-repeat: no-repeat;
  background-position: center center;
  background-color: transparent;
}

.ial-active .loginWndInside .ial-close {
  -ms-transform: translateX(0px) rotateZ(0deg);
  -moz-transform: translateX(0px) rotateZ(0deg);
  -webkit-transform: translateX(0px) rotateZ(0deg);
  transform: translateX(0px) rotateZ(0deg);
  -ms-transition: all 600ms ease 0ms;
  -moz-transition: all 600ms ease 0ms;
  -webkit-transition: all 600ms ease 0ms;
  transition: all 600ms ease 0ms;
}


.ial-active .loginWndInside .ial-close:hover{
	-ms-transform: rotateZ(90deg);
	-moz-transform: rotateZ(90deg);
	-webkit-transform: rotateZ(90deg);
	transform: rotateZ(90deg);
	-ms-transition-duration: 200ms;
	-moz-transition-duration: 200ms;
	-webkit-transition-duration: 200ms;
	transition-duration: 200ms;
}

i.ial-correct {
  width: 0px;
  height: 0px;
}


.loginOr,
.smallTxt,
.forgetLnk,
.loginLst a:link,
.loginLst a:visited,
select.loginTxt,
textarea.loginTxt,
input[type=text].loginTxt,
input[type=password].loginTxt {
  <?php $fonts->printFont('textfont', 'Text');?>
  border-radius:0;
  -moz-box-shadow:none;
  -webkit-box-shadow:none;
  box-shadow:none;
}

.ial-password1 .regTxt.loginTxt {
  margin-bottom: 0;
}
.passStrongness {
  *display: none;
  float: right;
}

select.loginTxt,
textarea.loginTxt,
input[type=password].loginTxt,
input[type=text].loginTxt {
  display: block;
  width: 100%;
  *width: auto;
  height: auto !important;
  margin: 0 0 14px;
  padding: <?php echo $buttoncomb[0]+1?>px;
  padding-left: <?php echo 27+($txtcomb[2]+0)/2;?>px;
  background: #<?php echo $txtcomb[0]?> no-repeat;
  *border: 1px #<?php echo $popupcomb[2]?> solid;
  border: 1px solid #<?php echo $txtcomb[3]?>;
  <?php if($txtcomb[4]=="1"): ?>
  border:none;
  <?php endif; ?>
  border-bottom: 1px solid #<?php echo $txtcomb[3]?>;
}

select.loginTxt {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  text-indent: 0.01px;
  text-overflow: '';
  padding: <?php echo $buttoncomb[0]+1?>px 0 <?php echo $buttoncomb[0]+1?>px 25px;
  cursor: pointer;
}
select.loginTxt::-ms-expand {
  display: none;
}
select.loginTxt option {
  padding-left: 5px;
}
.ial-select {
  margin: 0;
  padding: 0;
  border: 0;
  position: relative;
  display: block;
}
.ial-select:before,
.ial-select:after {
  content: "";
  position: absolute;
  top: 0;
  right: 0;
  width: 29px;
  height: 100%;
  pointer-events: none;
  -moz-box-sizing:border-box;
  -webkit-box-sizing:border-box;
  box-sizing:border-box;
  border: 1px solid #<?php echo $txtcomb[3]?>;
  <?php if($txtcomb[4]=="1"): ?>
  border:none;
  <?php endif; ?>
  border-bottom: 1px solid #<?php echo $txtcomb[3]?>;
  border-left:none;
}
.ial-select:after {
  background: transparent url(<?php echo $themeurl?>images/arrow.png) no-repeat center center;
}

textarea.loginTxt{
  border: 1px solid #<?php echo $txtcomb[3]?>;
  resize: vertical;
  height: 118px;
}

.strongFields .strongField,
.strongFields .strongField.empty,
select.loginTxt,
textarea.loginTxt,
input[type=password].loginTxt,
input[type=text].loginTxt {
  -webkit-transition: background-color 0.3s ease-out;
	-moz-transition: background-color 0.3s ease-out;
  -ms-transition: background-color 0.3s ease-out;
  -o-transition: background-color 0.3s ease-out;
	transition: background-color 0.3s ease-out;
}
.gi-elem:not(.gi-wide) .ial-recaptcha {
  overflow: hidden;
  box-shadow: 1px 0 1px -1px rgba(0,0,0,0.5);
  border-radius: 0 3px 3px 0;
}
.gi-elem .ial-recaptcha,
select.loginTxt,
textarea.regTxt,
input[type=password].regTxt,
input[type=text].regTxt {
  margin-bottom: 12px;
}
select.loginTxt option,
select.loginTxt {
  padding-left: <?php echo $buttoncomb[0]+1?>px;
}
button.ial-submit {
  margin: 0 0 7px;
  *clear: both;
}

#regLyr span.ial-submit:nth-child(2n<?php if ($socialpos == 'bottom') echo '+1' ?>) {
  float: left;
  clear: both;
}

.loginTxt::-webkit-input-placeholder {opacity: 1;}
.loginTxt:-moz-placeholder {opacity: 1;}
.loginTxt::-moz-placeholder {opacity: 1;}
.loginTxt:-ms-input-placeholder {opacity: 1;}
.loginTxt:focus::-webkit-input-placeholder {opacity: 0.5;}
.loginTxt:focus:-moz-placeholder {opacity: 0.5;}
.loginTxt:focus::-moz-placeholder {opacity: 0.5;}
.loginTxt:focus:-ms-input-placeholder {opacity: 0.5;}

textarea.loginTxt:hover,
textarea.loginTxt:focus,
input[type=password].loginTxt:hover,
input[type=text].loginTxt:hover,
input[type=password].loginTxt:focus,
input[type=text].loginTxt:focus {
  background-color: #<?php echo $txtcomb[1]?>;
}

.ial-submit {
  display: block;
  *display: inline;
  width: 100%;
  *width:auto;
  margin-bottom: 10px;
}

/*
.ial-submit:after{
  background-image: url(<?php echo $themeurl?>images/btn-shadow.png);
  width:100%;
  height:7px;
  box-sizing:border-box;
  -moz-box-sizing:border-box;
  -webkit-box-sizing:border-box;
  content: "";
  display: block;
  margin-top: 4px;
  position: absolute;
} */

.ial-check-lbl,
.forgetLnk:link,
.forgetLnk:visited {
  cursor: pointer;
  font-size: <?php echo $smalltext+0 ?>px;
  font-weight: normal;
	margin:0;
}
.smallTxt {
  display: inline-block;
  margin-bottom: <?php echo ($txtcomb[2]-40)/2>0? round(($txtcomb[2]-40)/2)+1 : 1 ?>px;
  font-size: <?php echo $smalltext+0 ?>px;
  font-weight: normal;
}
a.forgetLnk:link {
  padding: 0;
  margin-left: 10px;
  background: none;
}
.forgetDiv {
  line-height:0;
}
.forgetDiv .forgetLnk:link {
  margin: 0;
}
a.forgetLnk:hover {
  background-color: transparent;
  text-decoration: underline;
}
.ial-checkbox {
  display: block;
  margin: 1px 4px 0 0;
  width: 18px;
  height: 18px;
  border: 1px solid #<?php echo $checkboxcolor ?>;
  float: left;
  background: transparent none no-repeat center center;
}


.ial-checkbox.ial-active {
  background-image: url(<?php echo $themeurl?>images/check.png);
}
.ial-check-lbl {
  -webkit-touch-callout: none;
  -webkit-user-select: none;
  -khtml-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}
.loginLst {
  padding: 0;
  margin: 0;
  list-style: circle inside;
}
.loginLst a:link,
.loginLst a:visited {
  display: block;
  padding: 0 10px 0 30px;
  line-height: 40px;
  text-align: left;
  box-shadow:
		0px 1px 0px rgba(0,0,0,0.09);
	-moz-box-shadow:
		0px 1px 0px rgba(0,0,0,0.09);
	-webkit-box-shadow:
		0px 1px 0px rgba(0,0,0,0.09);
  -webkit-transition: padding 0.25s ease-out;
	-moz-transition: padding 0.25s ease-out;
  -ms-transition: padding 0.25s ease-out;
  -o-transition: padding 0.25s ease-out;
	transition: padding 0.25s ease-out;
}
.forgetLnk:link,
.forgetLnk:visited,
.forgetLnk:hover,
.loginLst a.active,
.loginLst a:hover {
  padding: 0 5px 0 35px;
	<?php $fonts->printFont('textfont', 'Hover') ?>
}
.passStrongness,
.regRequired,
.smallTxt.req:after {
  color: #<?php echo $textfont['Hover']['color'] ?>;
  content: " *";
}
.regRequired {
  display: block;
  margin: 0px;
  color:#fff;
}
<?php $circle = $this->cacheUrl.$helper->NewColorizeImage(dirname(__FILE__)."/images/circle.png", "727272", "407090")?>
<?php $hcircle= $this->cacheUrl.$helper->NewColorizeImage(dirname(__FILE__)."/images/circle.png", $textfont['Hover']['color'], "407090")?>
.loginLst a{
  background-color: transparent;
  background-repeat: no-repeat;
  background-image: url(<?php echo $circle ?>), url(<?php echo $hcircle ?>);
  background-position: 0 center, -100% 0;
	background-image: url(<?php echo $circle ?>)\9;
  background-position: 0 center\9;
}
.loginLst a.active,
.loginLst a:hover {
  background-position: -100% 0, 0 center;
  background-image: url(<?php echo $hcircle ?>)\9;
}

.loginLst a.mitem.active.customicon,
.loginLst a.mitem.customicon:hover {
 background-position: 0 center !important;
}


<?php $settings = $this->cacheUrl.$helper->NewColorizeImage(dirname(__FILE__)."/images/user-color.png", "727272", "407090")?>
<?php $hsettings= $this->cacheUrl.$helper->NewColorizeImage(dirname(__FILE__)."/images/user-color.png", $textfont['Hover']['color'], "407090")?>
.loginLst .settings {
	background-image: url(<?php echo $settings ?>), url(<?php echo $hsettings ?>);
  background-image: url(<?php echo $settings ?>)\9;
}
.loginLst .settings:hover {
  background-image: url(<?php echo $hsettings ?>)\9;
}
<?php $cart = $this->cacheUrl.$helper->NewColorizeImage(dirname(__FILE__)."/images/cart.png", "727272", "407090")?>
<?php $hcart= $this->cacheUrl.$helper->NewColorizeImage(dirname(__FILE__)."/images/cart.png", $textfont['Hover']['color'], "407090")?>
.loginLst .cart {
	background-image: url(<?php echo $cart ?>), url(<?php echo $hcart ?>);
  background-image: url(<?php echo $cart ?>)\9;
}
.loginLst .cart:hover {
  background-image: url(<?php echo $hcart ?>)\9;
}

<?php $off = $this->cacheUrl.$helper->NewColorizeImage(dirname(__FILE__)."/images/off.png", "727272", "407090")?>
<?php $hoff= $this->cacheUrl.$helper->NewColorizeImage(dirname(__FILE__)."/images/off.png", $textfont['Hover']['color'], "407090")?>
.loginLst .logout {
	background-image: url(<?php echo $off ?>), url(<?php echo $hoff ?>);
  background-image: url(<?php echo $off ?>)\9;
}
.loginLst .logout:hover {
  background-image: url(<?php echo $hoff ?>)\9;
}
.loginLst a.active,
.loginLst a.active:hover{
  background-image: none;
}
.loginLst a:last-child {
  border: 0;
  box-shadow:none;
	-moz-box-shadow:none;
	-webkit-box-shadow:none;
}
.ial-bg {
	visibility: hidden;
	position:absolute;
	background:#000 <?php if (!isset($blackoutcomb[2])) echo "url({$themeurl}images/patterns/".basename($blackoutcomb[1]).')' ?>;
	top:0;left:0;
	width:100%;
	height:100%;
	z-index:9999;
  opacity: 0;
}
.ial-bg.ial-active {
  visibility: visible;
  opacity: <?php echo $blackoutcomb[0]/100 ?>;
}

@-moz-keyframes spin { 100% { -moz-transform: rotate(360deg); } }
@-webkit-keyframes spin { 100% { -webkit-transform: rotate(360deg); } }
@keyframes spin { 100% { -webkit-transform: rotate(360deg); transform:rotate(360deg); } }

.loginBtn span {
  display: inline-block;
  cursor: default;
}
.fullWidth.selectBtn,
.fullWidth.selectBtn span {
  display: block;
  text-decoration: none;
  z-index: 0;
}
form.fullWidth {
  width: 100%;
  margin: auto;
}
.ial-socials {
  *display:inline;
  margin: 10px 0;
  text-align:center;
}

:focus {
  outline: none !important;
}
::-moz-focus-inner {
  border: none !important;
}