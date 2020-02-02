<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

class NextendMessage {
    static $error = array();
    static $notice = array();
    static $message = array();
    static $success = array();
    
    static function error($title = '', $message = ''){
        self::$error[] = array($title, $message);
    }
    
    static function notice($title = '', $message = ''){
        self::$notice[] = array($title, $message);
    }
    
    static function message($title = '', $message = ''){
        self::$message[] = array($title, $message);
    }
    
    static function success($title = '', $message = ''){
        self::$success[] = array($title, $message);
    }
    
    static function show(){
        if(count(self::$error)){
            foreach(self::$error AS $error){
                echo '<div style="max-width: 700px;" class="box y"><h3>'.$error[0].'</h3><p>'.$error[1].'</p></div>';
            }
            self::$error = array();
        }
        
        if(count(self::$success)){
            foreach(self::$success AS $success){
                echo '<div style="max-width: 700px;" class="box g"><h3>'.$success[0].'</h3><p>'.$success[1].'</p></div>';
            }
            self::$success = array();
        }
        
        if(count(self::$notice)){
            foreach(self::$notice AS $notice){
                echo '<div style="max-width: 700px;" class="box r"><h3>'.$notice[0].'</h3><p>'.$notice[1].'</p></div>';
            }
            self::$notice = array();
        }
        
        if(count(self::$message)){
            foreach(self::$message AS $message){
                echo '<div style="max-width: 700px;" class="box"><h3>'.$message[0].'</h3><p>'.$message[1].'</p></div>';
            }
            self::$message = array();
        }
    }
}