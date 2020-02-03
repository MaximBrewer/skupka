<?php
/**
 * @name		CK Framework
 * @copyright	Copyright (C) 2019. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - https://www.template-creator.com - https://www.joomlack.fr
 */
namespace Slideshowck;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require_once 'ckuri.php';

//use Joomla\CMS\Language\Text as CKText;
// use Joomla\CMS\Uri\Uri as CKUri;
use \Slideshowck\CKUri;

/**
 * Framework Helper
 */
class CKFramework {

	private static $assetsPath = '/media/com_slideshowck/assets';

	private static $version = '1.0.0';

	private static $doload;

	public static function init() {
		global $ckframeworkloaded;
		global $ckframeworkloadedversion;

		// if the framework is already loaded with a same or better version, do nothing
		if ($ckframeworkloaded && version_compare($ckframeworkloadedversion, self::$version, '>=')) {
			self::$doload = false;
		}

		self::$doload = true;
	}

	public static function getInline() {
		if (self::$doload === false) return '';

		$assets = self::getInlineCss() . self::getInlineJs();

		return $assets;
	}

	public static function getInlineCss() {
		if (self::$doload === false) return '';

		$assets = '<link rel="stylesheet" href="' . CKUri::root(true) . self::$assetsPath . '/ckframework.css" type="text/css" />';

		return $assets;
	}

	public static function getInlineJs() {
		if (self::$doload === false) return '';

		$assets = '<script src="' . CKUri::root(true) . self::$assetsPath . '/ckframework.js" type="text/javascript"></script>';

		return $assets;
	}

	public static function loadInline() {
		echo self::getInline();
	}

	public static function load() {
		if (self::$doload === false) return;

		\JHtml::_('jquery.framework');
		$doc = \JFactory::getDocument();
		$doc->addStylesheet(CKUri::root(true) . self::$assetsPath . '/ckframework.css');
		$doc->addScript(CKUri::root(true) . self::$assetsPath . '/ckframework.js');
	}

	public static function loadCss() {
		if (self::$doload === false) return;

		$doc = \JFactory::getDocument();
		$doc->addStylesheet(CKUri::root(true) . self::$assetsPath . '/ckframework.css');
	}

	public static function loadJs() {
		if (self::$doload === false) return;

		$doc = \JFactory::getDocument();
		$doc->addScript(CKUri::root(true) . self::$assetsPath . '/ckframework.js');
	}

	public static function getFaIconsInline() {
		return '<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous" />';
	}

	public static function loadFaIconsInline() {
		echo self::getFaIconsInline();
	}

	/*
	 * Load the JS and CSS files needed to use CKBox
	 *
	 * Return void
	 */
	public static function loadCkbox() {
		$doc = \JFactory::getDocument();
		\JHtml::_('jquery.framework');
		$doc->addStyleSheet(CKUri::root(true) . self::$assetsPath . '/ckbox.css');
		$doc->addScript(CKUri::root(true) . self::$assetsPath . '/ckbox.js');
	}
}

CKFramework::init();