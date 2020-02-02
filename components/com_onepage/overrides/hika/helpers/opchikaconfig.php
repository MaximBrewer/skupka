<?php
defined('_JEXEC') or die('Restricted access');

class OPChikaconfig {
	public static function get($var, $default) {
		$ref = OPChikaRef::getInstance(); 
		switch ($var) {
			case 'useSSL': 
			  return $ref->config->get('force_ssl', 0);
			  break; 
			 
			
		}
		return $default; 
	}
}
