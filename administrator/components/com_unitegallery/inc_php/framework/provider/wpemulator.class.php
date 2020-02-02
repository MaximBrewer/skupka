<?php
/**
 * @package Unite Gallery
 * @author UniteCMS.net / Valiano
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('_JEXEC') or die('Restricted access');

	
	/**
	 * 
	 * _e() emulator
	 */
	if(function_exists("_e") == false){
	
		function _e($string, $textdomain = null){
			if(function_exists("translate")){
				echo translate( $text, $textdomain );
			}else
				echo $string;
		}
	}
	
	
	/**
	 * 
	 * __() emulator
	 */	
	if(function_exists("__") == false){
	 
		function __($string, $textdomain = null){
			
			if(function_exists("translate")){
				return translate( $text, $textdomain );
			}else
				return $string;
		
		}
	}
	
	
	
	
?>