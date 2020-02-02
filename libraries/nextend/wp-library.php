<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

if (!defined('NEXTENDLIBRARY')) {
    global $nextend_head, $nextend_body, $nextend_wp_head, $nextend_wp_footer;

    $nextend_head = '';
    $nextend_body = '';
    $nextend_wp_head = false;
    $nextend_wp_footer = false;
    
    require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'library.php');

    nextendimport('nextend.wordpress.settings');
    
    add_action('wp_footer', 'nextend_generate');
    add_action('admin_footer', 'nextend_generate');
    function nextend_generate() {
        global $nextend_head, $nextend_body, $nextend_wp_footer;
        $nextend_wp_footer = true;
        
        if (class_exists('NextendCss', false) || class_exists('NextendJavascript', false)) {
            ob_start();
              do_action('nextend_css');
              $css = NextendCss::getInstance();
              $css->generateCSS();
              echo '<script type="text/javascript">
(function (w, d, u) {
    if(w.njQuery === u){
        w.bindNextendQ = [];
    
        function pushToReady(x) {
            w.bindNextendQ.push([alias.handler,"ready", x]);
        }
        
        function pushToLoad(x) {
            w.bindNextendQ.push([alias.handler,"load", x]);
        }

        var alias = {
            handler: w,
            ready: pushToReady,
            load: pushToLoad
        }

        w.njQuery = function (handler) {
            alias.handler = handler;
            return alias;
        }
    }
})(window, document);
              </script>';
            $nextend_head = ob_get_clean();
            
            ob_start();
            do_action('nextend_js');
            $js = NextendJavascript::getInstance();
            $js->generateJs();
            $nextend_body = ob_get_clean();
        }
        if(getNextend('safemode', 0) == 1) echo $nextend_head.$nextend_body;
        return true;
    }
    
    function nextend_render_end($buffer){
        global $nextend_head, $nextend_body;
        if($nextend_head != ''){
            $buffer = preg_replace('/<\/head>/', $nextend_head.'</head>', $buffer, 1);
        }
        if($nextend_body != ''){
            $buffer = preg_replace('/<\/body>/', $nextend_body.'</body>', $buffer, 2);
        }
        return $buffer;
    }
    
    if(is_admin()){
        add_action('admin_init', 'nextend_wp_loaded', 3000);
    }else if(getNextend('safemode', 0) == 0){
        add_action('wp', 'nextend_wp_loaded', 30000);
    }else{
        add_action('wp_head', 'nextend_wp_loaded');
    }
    function nextend_wp_loaded() {
        global $nextend_wp_head;
        $nextend_wp_head = true;
        //setNextend('safemode', 0);
        if(getNextend('safemode', 0) != 1){
            ob_start("nextend_render_end");
            ob_start();
        }
    }
    
    if(getNextend('logproblems')){
        function nextend_error_checker(){
            global $nextend_wp_head, $nextend_wp_footer;
            if (class_exists('NextendCss', false) || class_exists('NextendJavascript', false)) {
                if((!defined('DOING_AJAX') || !DOING_AJAX) && !is_admin()){
                    if($nextend_wp_head === false){
                        nextend_add_error('missinghead');
                    }else if($nextend_wp_footer === false){
                        nextend_add_error('missingfooter');
                    }
                }
            }
        }
        
        function nextend_add_error($key){
            static $nextenderror = null;
            if(!$nextenderror){
                $nextenderror = get_option( 'nextend_error' );
                if ( $nextenderror === false ){
                    $nextenderror = array();
                }
            }
            if(!is_array($nextenderror[$key])) $nextenderror[$key] = array();
            $nextenderror[$key][] = $_SERVER["REQUEST_URI"];
            $nextenderror[$key] = array_unique($nextenderror[$key]);
            update_option( 'nextend_error' , $nextenderror);
        }
        
        register_shutdown_function('nextend_error_checker');
    }
}
