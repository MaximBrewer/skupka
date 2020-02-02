<?php
/*-------------------------------------------------------------------------
# mod_improved_ajax_login - Improved AJAX Login and Register
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2013 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
defined('_JEXEC') or die('Restricted access');

$fonts = new OfflajnFontHelper($params);
echo $fonts->parseFonts();

$GLOBALS['googlefontsloaded'] = array();
foreach($params->toArray() AS $k => $p){
  if (strpos($k, 'grad')) $p = explode('-', $p);
  elseif (strpos($k, 'comb')) $p = explode('|*|', $p);

  if ($k != 'params') $$k = $p;
}

if(!function_exists('shift_color')){
  function shift_color($hex, $s) {
  	$c = hexdec($hex);
  	$r = (($c >> 16) & 255)+$s;
  	$g = (($c >> 8) & 255)+$s;
  	$b = ($c & 255)+$s;
  	if ($r>255) $r=255; elseif ($r<0) $r=0;
  	if ($g>255) $g=255; elseif ($g<0) $g=0;
  	if ($b>255) $b=255; elseif ($b<0) $b=0;
  	printf('%02X%02X%02X', $r, $g, $b);
  }
}
?>
*{}
.gi-elem input,
.gi-elem button,
.gi-elem select,
.gi-elem textarea,
.gi-elem, .ial-close {
	box-sizing: border-box !important;
	-moz-box-sizing: border-box !important;
	-webkit-box-sizing: border-box !important;
}
.ial-close {
  margin: 0 !important;
}
.gi-elem .hidden {
  display: none;
}
.gi-elem {
  display: block;
  float: left;
  text-align: left;
  line-height: 0;
  padding-top: 2px;
}
.gi-elem.checkbox {
	padding-left: 0;
}
.regRequired .red {
  font-weight: normal;
  color: inherit;
}
.gi-elem.gi-wide {
  width: 100%;
}
.ial-login,
.ial-form {
  display: block;
  margin: 0;
  line-height: 0;
  max-width: 100%;
}

.loginWndInside:not(.ui-sortable) {
  max-width: 100%;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
}
.loginWndInside * {
	max-width: 100%;
}
.ial-recaptcha[data-sitekey] {
	height: 78px;
}

.ial-trans-gpu {
  -webkit-transition: 300ms ease-out;
	-moz-transition: 300ms ease-out;
  -ms-transition: 300ms ease-out;
  -o-transition: 300ms ease-out;
	transition: 300ms ease-out;
  -webkit-transition-property: visibility, opacity, -webkit-transform;
	-moz-transition-property: visibility, opacity, -moz-transform;
  -ms-transition-property: visibility, opacity, -ms-transform;
  -o-transition-property: visibility, opacity, -o-transform;
	transition-property: visibility, opacity, transform;
}
.ial-trans-b {
  visibility: hidden;
  opacity: 0;
  -webkit-transform: translate(0, 30px);
	-moz-transform: translate(0, 30px);
  -ms-transform: translate(0, 30px);
  -o-transform: translate(0, 30px);
	transform: translate(0, 30px);
}
.ial-trans-t {
  visibility: hidden;
  opacity: 0;
  -webkit-transform: translate(0, -30px);
	-moz-transform: translate(0, -30px);
  -ms-transform: translate(0, -30px);
  -o-transform: translate(0, -30px);
	transform: translate(0, -30px);
}
.ial-trans-r {
  visibility: hidden;
  opacity: 0;
  -webkit-transform: translate(-30px, 0);
	-moz-transform: translate(-30px, 0);
  -ms-transform: translate(-30px, 0);
  -o-transform: translate(-30px, 0);
	transform: translate(-30px, 0);
}
.ial-trans-l {
  visibility: hidden;
  opacity: 0;
  -webkit-transform: translate(30px, 0);
	-moz-transform: translate(30px, 0);
  -ms-transform: translate(30px, 0);
  -o-transform: translate(30px, 0);
	transform: translate(30px, 0);
}
.ial-trans-gpu.ial-active {
  visibility: visible;
  opacity: 1;
  -webkit-transform: none;
	-moz-transform: none;
  -ms-transform: none;
  -o-transform: none;
	transform: none;
  /* safari fix */
  -webkit-transition-property: opacity, -webkit-transform;
}

/* Effect 1: Fade in and scale up */
.ial-effect-1{
	-webkit-transform: scale(0.7);
	-moz-transform: scale(0.7);
	-ms-transform: scale(0.7);
	transform: scale(0.7);
  visibility: hidden;
	opacity: 0;
	-webkit-transition: visibility 0.3s, opacity 0.3s, -webkit-transform 0.3s;
	-moz-transition: visibility 0.3s, opacity 0.3s, -moz-transform 0.3s;
	transition: visibility 0.3s, opacity 0.3s, transform 0.3s;
}

.ial-effect-1.ial-active{
	-webkit-transform: scale(1);
	-moz-transform: scale(1);
	-ms-transform: scale(1);
	transform: scale(1);
	visibility: visible;
	opacity: 1;
}


/* Effect 2: Slide from the right */
.ial-effect-2{
	-webkit-transform: translateX(20%);
	-moz-transform: translateX(20%);
	-ms-transform: translateX(20%);
	transform: translateX(20%);
	opacity: 0;
	visibility: hidden;
	-webkit-transition: visibility 0.3s, opacity 0.3s, -webkit-transform 0.3s cubic-bezier(0.25, 0.5, 0.5, 0.9);
	-moz-transition: visibility 0.3s, opacity 0.3s, -moz-transform 0.3s cubic-bezier(0.25, 0.5, 0.5, 0.9);
	transition: visibility 0.3s, opacity 0.3s, transform 0.3s cubic-bezier(0.25, 0.5, 0.5, 0.9);
}

.ial-effect-2.ial-active {
	-webkit-transform: translateX(0);
	-moz-transform: translateX(0);
	-ms-transform: translateX(0);
	transform: translateX(0);
	opacity: 1;
	visibility: visible;
}


/* Effect 4: Newspaper */
.ial-effect-4 {
	-webkit-transform: perspective( 1300px ) scale(0) rotate(720deg);
	-moz-transform: perspective( 1300px ) scale(0) rotate(720deg);
	-ms-transform: perspective( 1300px ) scale(0) rotate(720deg);
	transform: perspective( 1300px ) scale(0) rotate(720deg);
	opacity: 0;
	visibility: hidden;
	-webkit-transition: visibility 0.5s, opacity 0.5s, -webkit-transform 0.5s;
	-moz-transition: visibility 0.5s, opacity 0.5s, -moz-transform 0.5s;
	transition: visibility 0.5s, opacity 0.5s, transform 0.5s;
}

.ial-effect-4.ial-active {
	-webkit-transform: perspective( 1300px ) scale(1) rotate(0deg);
	-moz-transform: perspective( 1300px ) scale(1) rotate(0deg);
	-ms-transform: perspective( 1300px ) scale(1) rotate(0deg);
	transform: perspective( 1300px ) scale(1) rotate(0deg);
	opacity: 1;
	visibility: visible;
}


/* Effect 5: fall */

.ial-effect-5{
	-webkit-transform-style: preserve-3d;
	-moz-transform-style: preserve-3d;
	transform-style: preserve-3d;
	-webkit-transform: perspective( 1300px ) translateZ(600px) rotateX(20deg);
	-moz-transform: perspective( 1300px ) translateZ(600px) rotateX(20deg);
	-ms-transform: perspective( 1300px ) translateZ(600px) rotateX(20deg);
	transform: perspective( 1300px ) translateZ(600px) rotateX(20deg);
	opacity: 0;
	visibility: hidden;
	-webkit-transition:opacity;
	-moz-transition:opacity;
	transition:opacity;
}

.ial-effect-5.ial-active{
	-webkit-transition: visibility 0.3s, opacity 0.3s, -webkit-transform 0.3s;
	-moz-transition: visibility 0.3s, opacity 0.3s, -moz-transform 0.3s;
	transition: visibility 0.3s, opacity 0.3s, transform 0.3s;
	-webkit-transform: perspective( 3000px ) translateZ(0px) rotateX(0deg);
	-moz-transform: perspective( 3000px ) translateZ(0px) rotateX(0deg);
	-ms-transform: perspective( 3000px ) translateZ(0px) rotateX(0deg);
	transform: perspective( 3000px ) translateZ(0px) rotateX(0deg);
	opacity: 1;
	visibility: visible;
}


/* Effect 6: side fall */

.ial-effect-6{
	-webkit-transform-style: preserve-3d;
	-moz-transform-style: preserve-3d;
	transform-style: preserve-3d;
	-webkit-transform: perspective( 1300px ) translate(30%) translateZ(600px) rotate(10deg);
	-moz-transform: perspective( 1300px ) translate(30%) translateZ(600px) rotate(10deg);
	-ms-transform: perspective( 1300px )translate(30%) translateZ(600px) rotate(10deg);
	transform: perspective( 1300px ) translate(30%) translateZ(600px) rotate(10deg);
	opacity: 0;
	visibility: hidden;
}

.ial-effect-6.ial-active{
	-webkit-transition: visibility 0.3s, opacity 0.3s, -webkit-transform 0.3s;
	-moz-transition: visibility 0.3s, opacity 0.3s, -moz-transform 0.3s;
	transition: visibility 0.3s, opacity 0.3s, transform 0.3s;
	-webkit-transform: perspective( 3000px ) translate(0%) translateZ(0) rotate(0deg);
	-moz-transform: perspective( 3000px ) translate(0%) translateZ(0) rotate(0deg);
	-ms-transform: perspective( 3000px ) translate(0%) translateZ(0) rotate(0deg);
	transform: perspective( 3000px ) translate(0%) translateZ(0) rotate(0deg);
	opacity: 1;
	visibility: visible;
}


/* Effect 8: 3D flip horizontal */

.ial-effect-8 {
	-webkit-transform-style: preserve-3d;
	-moz-transform-style: preserve-3d;
	transform-style: preserve-3d;
	-webkit-transform: perspective( 1300px ) rotateY(-70deg);
	-moz-transform: perspective( 1300px ) rotateY(-70deg);
	-ms-transform: perspective( 1300px ) rotateY(-70deg);
	transform: perspective( 1300px ) rotateY(-70deg);
	-webkit-transition: visibility 0.3s, opacity 0.3s, -webkit-transform 0.3s;
	-moz-transition: visibility 0.3s, opacity 0.3s, -moz-transform 0.3s;
	transition: visibility 0.3s, opacity 0.3s, transform 0.3s;
	opacity: 0;
	visibility: hidden;
}

.ial-effect-8.ial-active {
	-webkit-transform: perspective( 1300px ) rotateY(0deg);
	-moz-transform: perspective( 1300px ) rotateY(0deg);
	-ms-transform: perspective( 1300px ) rotateY(0deg);
	transform: perspective( 1300px ) rotateY(0deg);
	opacity: 1;
	visibility: visible;
}

/* Effect 9: 3D flip vertical */
.ial-effect-9 {
	-webkit-transform-style: preserve-3d;
	-moz-transform-style: preserve-3d;
	transform-style: preserve-3d;
	-webkit-transform: perspective( 1300px ) rotateX(-70deg);
	-moz-transform: perspective( 1300px ) rotateX(-70deg);
	-ms-transform: perspective( 1300px ) rotateX(-70deg);
	transform: perspective( 1300px ) rotateX(-70deg);
	-webkit-transition: visibility 0.3s, opacity 0.3s, -webkit-transform 0.3s;
	-moz-transition: visibility 0.3s, opacity 0.3s, -moz-transform 0.3s;
	transition: visibility 0.3s, opacity 0.3s, transform 0.3s;
	opacity: 0;
	visibility: hidden;
}

.ial-effect-9.ial-active {
	-webkit-transform: perspective( 1300px ) rotateX(0deg);
	-moz-transform: perspective( 1300px ) rotateX(0deg);
	-ms-transform: perspective( 1300px ) rotateX(0deg);
	transform: perspective( 1300px ) rotateX(0deg);
	opacity: 1;
	visibility: visible;
}

/* Effect 11: Super scaled */
.ial-effect-11 {
	-webkit-transform: perspective( 1300px ) scale(2);
	-moz-transform: perspective( 1300px ) scale(2);
	-ms-transform: perspective( 1300px ) scale(2);
	transform: perspective( 1300px ) scale(2);
	opacity: 0;
	visibility: hidden;
	-webkit-transition: visibility 0.3s, opacity 0.3s, -webkit-transform 0.3s;
	-moz-transition: visibility 0.3s, opacity 0.3s, -moz-transform 0.3s;
	transition: visibility 0.3s, opacity 0.3s, transform 0.3s;
}

.ial-effect-11.ial-active {
	-webkit-transform: perspective( 1300px ) scale(1);
	-moz-transform: perspective( 1300px ) scale(1);
	-ms-transform: perspective( 1300px ) scale(1);
	transform: perspective( 1300px ) scale(1);
	opacity: 1;
	visibility: visible;
}

/* Effect 13: 3D slit */

.ial-effect-13 {
	-webkit-transform-style: preserve-3d;
	-moz-transform-style: preserve-3d;
	transform-style: preserve-3d;
	/*-webkit-transform: perspective( 1300px ) translateZ(-3000px) rotateY(90deg);
	-moz-transform: perspective( 1300px ) translateZ(-3000px) rotateY(90deg);
	-ms-transform: perspective( 1300px ) translateZ(-3000px) rotateY(90deg);
	transform: perspective( 1300px ) translateZ(-3000px) rotateY(90deg);*/
	opacity: 0;
	visibility: hidden;
}

.ial-effect-13.ial-active {
	-webkit-animation: slit .7s forwards ease-out;
	-moz-animation: slit .7s forwards ease-out;
	animation: slit .7s forwards ease-out;
}

@-webkit-keyframes slit {
	0% { -webkit-transform: perspective( 1300px ) translateZ(-3000px) rotateY(90deg); opacity: 0; }
	50% { -webkit-transform: perspective( 1300px ) translateZ(-250px) rotateY(89deg); opacity: 0.5; -webkit-animation-timing-function: ease-out;}
	100% { -webkit-transform: perspective( 1300px ) translateZ(1px) rotateY(0deg); opacity: 1; visibility: visible;}
}

@-moz-keyframes slit {
	0% { -moz-transform: perspective( 1300px ) translateZ(-3000px) rotateY(90deg); opacity: 0; }
	50% { -moz-transform: perspective( 1300px ) translateZ(-250px) rotateY(89deg); opacity: 0.5; -moz-animation-timing-function: ease-out;}
	100% { -moz-transform: perspective( 1300px ) translateZ(0) rotateY(0deg); opacity: 1; visibility: visible;}
}

@keyframes slit {
	0% { transform: perspective( 1300px ) translateZ(-3000px) rotateY(90deg); opacity: 0; }
	50% { transform: perspective( 1300px ) translateZ(-250px) rotateY(89deg); opacity: 0.5; animation-timing-function: ease-in;}
	100% { transform: perspective( 1300px ) translateZ(0) rotateY(0deg); opacity: 1; visibility: visible;}
}


/* Effect 14:  3D Rotate from bottom */

.ial-effect-14 {
	-webkit-transform-style: preserve-3d;
	-moz-transform-style: preserve-3d;
	transform-style: preserve-3d;
	-webkit-transform: perspective( 1300px ) translateY(100%) rotateX(90deg);
	-moz-transform: perspective( 1300px ) translateY(100%) rotateX(90deg);
	-ms-transform: perspective( 1300px ) translateY(100%) rotateX(90deg);
	transform: translateY(100%) rotateX(90deg);
	-webkit-transform-origin: 0 100%;
	-moz-transform-origin: 0 100%;
	transform-origin: 0 100%;
	opacity: 0;
	visibility: hidden;
	-webkit-transition: visibility 0.3s, opacity 0.3s, -webkit-transform 0.3s;
	-moz-transition: visibility 0.3s, opacity 0.3s, -moz-transform 0.3s;
	transition: visibility 0.3s, opacity 0.3s, transform 0.3s;
}

.ial-effect-14.ial-active {
	-webkit-transform: perspective( 1300px ) translateY(0%) rotateX(0deg);
	-moz-transform: perspective( 1300px ) translateY(0%) rotateX(0deg);
	-ms-transform: perspective( 1300px ) translateY(0%) rotateX(0deg);
	transform: perspective( 1300px ) translateY(0%) rotateX(0deg);
	opacity: 1;
	visibility: visible;
}

/* Effect 15:  3D Rotate in from left */

.ial-effect-15 {
	-webkit-transform-style: preserve-3d;
	-moz-transform-style: preserve-3d;
	transform-style: preserve-3d;
	-webkit-transform: perspective( 1300px ) translateZ(100px) translateX(-30%) rotateY(90deg);
	-moz-transform: perspective( 1300px ) translateZ(100px) translateX(-30%) rotateY(90deg);
	-ms-transform: perspective( 1300px ) translateZ(100px) translateX(-30%) rotateY(90deg);
	transform: perspective( 1300px ) translateZ(100px) translateX(-30%) rotateY(90deg);
	-webkit-transform-origin: 0 100%;
	-moz-transform-origin: 0 100%;
	transform-origin: 0 100%;
	opacity: 0;
	visibility: hidden;
	-webkit-transition: visibility 0.3s, opacity 0.3s, -webkit-transform 0.3s;
	-moz-transition: visibility 0.3s, opacity 0.3s, -moz-transform 0.3s;
	transition: visibility 0.3s, opacity 0.3s, transform 0.3s;
}

.ial-effect-15.ial-active {
	-webkit-transform: perspective( 1300px ) translateZ(0px) translateX(0%) rotateY(0deg);
	-moz-transform: perspective( 1300px ) translateZ(0px) translateX(0%) rotateY(0deg);
	-ms-transform: perspective( 1300px ) translateZ(0px) translateX(0%) rotateY(0deg);
	transform: perspective( 1300px ) translateZ(0px) translateX(0%) rotateY(0deg);
	opacity: 1;
	visibility: visible;
}

/* Effect 17:  Slide in from bottom with perspective on container */

#fake-offlajn-body{
  outline: 1px solid transparent;
	-webkit-transform: perspective( 1300px ) rotateX(0);
	-moz-transform: perspective( 1300px ) rotateX(0);
	-ms-transform: perspective( 1300px ) rotateX(0);
}

#fake-offlajn-body.go-to-back-17 {
	height: 100%;
	overflow: hidden;
	-webkit-transition: -webkit-transform 0.3s;
	-moz-transition: -moz-transform 0.3s;
	transition: transform 0.3s;
	-webkit-transform: perspective( 1300px ) rotateX(-4deg);
	-moz-transform: perspective( 1300px ) rotateX(-4deg);
	-ms-transform: perspective( 1300px ) rotateX(-4deg);
	transform: perspective( 1300px ) rotateX(-4deg);
	-webkit-transform-origin: 50% 0%;
	-moz-transform-origin: 50% 0%;
	transform-origin: 50% 0%;
	-webkit-transform-style: preserve-3d;
	-moz-transform-style: preserve-3d;
	transform-style: preserve-3d;
}

.ial-effect-17 {
	opacity: 0;
	visibility: hidden;
	-webkit-transform: translateY(200%);
	-moz-transform: translateY(200%);
	-ms-transform: translateY(200%);
	transform: translateY(200%);
}

.ial-effect-17.ial-active {
	-webkit-transform: translateY(0);
	-moz-transform: translateY(0);
	-ms-transform: translateY(0);
	transform: translateY(0);
	opacity: 1;
	visibility: visible;
	-webkit-transition: visibility 0.3s 0.2s, opacity 0.3s 0.2s, -webkit-transform 0.3s 0.2s;
	-moz-transition: visibility 0.3s 0.2s, opacity 0.3s 0.2s, -moz-transform 0.3s 0.2s;
	transition: visibility 0.3s 0.2s, opacity 0.3s 0.2s, transform 0.3s 0.2s;
}


/* Effect 18:  Slide from right with perspective on container */

#fake-offlajn-body.go-to-back-18 {
	-webkit-transform-style: preserve-3d;
	-webkit-animation: rotateRightSideFirst 0.5s forwards ease-in;
	-moz-transform-style: preserve-3d;
	-moz-animation: rotateRightSideFirst 0.5s forwards ease-in;
	transform-style: preserve-3d;
	animation: rotateRightSideFirst 0.5s forwards ease-in;
}


@-webkit-keyframes rotateRightSideFirst {
	50% { -webkit-transform: perspective( 1300px ) translateZ(-50px) rotateY(5deg); -webkit-animation-timing-function: ease-out; }
	100% { -webkit-transform: perspective( 1300px ) translateZ(-200px); }
}

@-moz-keyframes rotateRightSideFirst {
	50% { -moz-transform: perspective( 1300px ) translateZ(-50px) rotateY(5deg); -moz-animation-timing-function: ease-out; }
	100% { -moz-transform: perspective( 1300px ) translateZ(-200px); }
}

@keyframes rotateRightSideFirst {
	50% { transform: perspective( 1300px ) translateZ(-50px) rotateY(5deg); animation-timing-function: ease-out; }
	100% { transform: perspective( 1300px ) translateZ(-200px); }
}

.ial-effect-18 {
	-webkit-transform: translateX(200%);
	-moz-transform: translateX(200%);
	-ms-transform: translateX(200%);
	transform: translateX(200%);
	opacity: 0;
	visibility: hidden;
}

.ial-effect-18.ial-active {
	-webkit-transform: translateX(0);
	-moz-transform: translateX(0);
	-ms-transform: translateX(0);
	transform: translateX(0);
	opacity: 1;
	visibility: visible;
	-webkit-transition: visibility 0.3s, opacity 0.3s, -webkit-transform 0.3s;
  -moz-transition: visibility 0.3s, opacity 0.3s, -moz-transform 0.3s;
  -ms-transition: visibility 0.3s, opacity 0.3s, -ms-transform 0.3s;
  transition: visibility 0.3s, opacity 0.3s, transform 0.3s;
	-webkit-transition-delay: 600ms;
	-moz-transition-delay: 600ms;
	-ms-transition-delay: 600ms;
  transition-delay: 600ms;
}


/* Effect 19: Blur */
.ial-effect-19,
.ial-effect-20{
	-webkit-transform: scale(0.7);
	-moz-transform: scale(0.7);
	-ms-transform: scale(0.7);
	transform: scale(0.7);
  visibility: hidden;
	opacity: 0;
	-webkit-transition: visibility 0.3s, opacity 0.3s, -webkit-transform 0.3s;
	-moz-transition: visibility 0.3s, opacity 0.3s, -moz-transform 0.3s;
	transition: visibility 0.3s, opacity 0.3s, transform 0.3s;
}

.ial-effect-19.ial-active,
.ial-effect-20.ial-active{
	-webkit-transform: scale(1);
	-moz-transform: scale(1);
	-ms-transform: scale(1);
	transform: scale(1);
	visibility: visible;
	opacity: 1;
}

img.ial-captcha {
  max-width: 100%;
  opacity: 0;
  transition: opacity .33s ease-in-out;
  -o-transition: opacity .33s ease-in-out;
  -ms-transition: opacity .33s ease-in-out;
  -moz-transition: opacity .33s ease-in-out;
  -webkit-transition: opacity .33s ease-in-out;
}
img.ial-captcha.fadeIn {
  opacity: 1;
}

#loginComp {
  display: inline-block;
  margin-bottom: 15px;
  overflow: hidden;
  max-width: 100%;
}
#loginComp #loginBtn {
  display: none;
}
.selectBtn {
  display: inline-block;
  *display: inline;
  z-index: 10000;
  user-select: none;
  -moz-user-select: none;
  -webkit-user-select: auto;
  -ms-user-select: none;
}
.selectBtn:hover,
.selectBtn:active,
.selectBtn:focus {
  background: none;
}
#logoutForm,
#loginForm {
  display: inline-block;
  margin: 0;
}

.ial-msg.inf,
.ial-msg.err {
  z-index: 99999;
}

.ial-window, .ial-usermenu {
	visibility: hidden;
}

.hiddenField {
  display: none;
}

.ial-frame {
	width: 80vw;
	height: 80vh;
	margin: 0;
	border: 0 none;
	padding: 15px;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}
@media (max-width: 767px) {
	.ial-frame {
		width: 100vw;
		height: 100vh;
	}
}
.ial-modal .loginWndInside {
	background: #fff
}
.ial-window.ial-modal:not(.ial-active) {
	z-index: -1;
}