<?php
/* license: commercial ! */
defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;


			$root = Juri::root(); 
		if (substr($root, -1) !== '/') $root .= '/'; 
		$sor = $root.'plugins/system/producttabs/assets/helper.js'; 
		JHtml::script($sor); 
		$sor = $root.'plugins/system/producttabs/tmpl/default.css'; 
		JHtml::stylesheet($sor); 
