<?php
/**
 * @package     One Page Checkout
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;


abstract class JHtmlOpchtml
{
	
	protected static $loaded = array();
	public static function modal($selector = 'modal', $params = array())
	{
	  if (!class_exists( 'VmConfig' )) require(JPATH_COMPONENT_ADMINISTRATOR .'/helpers/config.php');
		 if (VmConfig::get('usefancy', 1)) {
$doc = JFactory::getDocument(); 	  
	   
//add popup support:
if (!isset(static::$loaded[__METHOD__]['modal'])) {
if (method_exists('vmJsApi', 'addJScript')) {
 vmJsApi::addJScript( 'fancybox/jquery.fancybox-1.3.4.pack', false);
 vmJsApi::css('jquery.fancybox-1.3.4');
 if (method_exists('vmJsApi', 'writeJS'))
 {
	 vmJsApi::writeJS(); 
 }
 
}
else
{
if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'fancybox'.DIRECTORY_SEPARATOR.'jquery.fancybox-1.3.4.pack.js'))
{
	$path = 'components/com_virtuemart/assets/js/fancybox/'; 
	JHTMLOPC::stylesheet('fancybox-1.3.4.css', 'components/com_virtuemart/assets/css/', array());
	JHTMLOPC::script('jquery.fancybox-1.3.4.pack.js', $path, false);
}
}
JHtml::_('jquery.framework'); 
JHtml::_('jquery.ui');
JHTMLOPC::script('fancybinder.js', 'components/com_onepage/assets/js/', false);


static::$loaded[__METHOD__]['modal'] = true; 
}
	   
		$sig = md5(serialize(array($selector, $params)));

		if (!isset(static::$loaded[__METHOD__][$sig]))
		{
			

			// Setup options object
			/*
			$opt['backdrop'] = isset($params['backdrop']) ? (boolean) $params['backdrop'] : true;
			$opt['keyboard'] = isset($params['keyboard']) ? (boolean) $params['keyboard'] : true;
			$opt['show']     = isset($params['show']) ? (boolean) $params['show'] : true;
			$opt['remote']   = isset($params['remote']) ?  $params['remote'] : '';
			*/
			//$opt['href'] = 'this.href'; 
			$opt = array(); 
			$options = JHtml::getJSObject($opt);

			// Attach the modal to document
			JFactory::getDocument()->addScriptDeclaration(
				"
				if (typeof jQuery != 'undefined')
				 jQuery(document).ready(function(){
					if (typeof bindFancyBox != 'undefined')
					bindFancyBox(jQuery('".$selector."')); 
					});"
			);

			// Set static array
			static::$loaded[__METHOD__][$sig] = true;
		}
		 }
		 else
		 {
			 $doc = JFactory::getDocument(); 	  
	   
//add popup support:
if (!isset(static::$loaded[__METHOD__]['modal'])) {
if (method_exists('vmJsApi', 'addJScript')) {
 
 			vmJsApi::addJScript( 'facebox', false, true, true, false, '' );
			vmJsApi::css( 'facebox' );

 
 if (method_exists('vmJsApi', 'writeJS'))
 {
	 vmJsApi::writeJS(); 
 }
 
}
else
{
if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'fancybox'.DIRECTORY_SEPARATOR.'jquery.fancybox-1.3.4.pack.js'))
{
	$path = 'components/com_virtuemart/assets/js/'; 
	JHTMLOPC::stylesheet('facebox.css', 'components/com_virtuemart/assets/css/', array());
	JHTMLOPC::script('facebox.js', $path, false);
}
}
JHtml::_('jquery.framework'); 
JHtml::_('jquery.ui');
JHTMLOPC::script('fancybinder.js', 'components/com_onepage/assets/js/', false);


static::$loaded[__METHOD__]['modal'] = true; 
}
	   
		$sig = md5(serialize(array($selector, $params)));

		if (!isset(static::$loaded[__METHOD__][$sig]))
		{
			

			// Setup options object
			/*
			$opt['backdrop'] = isset($params['backdrop']) ? (boolean) $params['backdrop'] : true;
			$opt['keyboard'] = isset($params['keyboard']) ? (boolean) $params['keyboard'] : true;
			$opt['show']     = isset($params['show']) ? (boolean) $params['show'] : true;
			$opt['remote']   = isset($params['remote']) ?  $params['remote'] : '';
			*/
			//$opt['href'] = 'this.href'; 
			$opt = array(); 
			$options = JHtml::getJSObject($opt);

			// Attach the modal to document
			JFactory::getDocument()->addScriptDeclaration(
				"
				if (typeof jQuery != 'undefined')
				 jQuery(document).ready(function(){
					if (typeof faceboxBinder != 'undefined')
					faceboxBinder(jQuery('".$selector."')); 
					});"
			);

			// Set static array
			static::$loaded[__METHOD__][$sig] = true;
		}
		 }
		return;
	}
}