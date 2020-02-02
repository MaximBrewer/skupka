<?php

/**
 * @copyright	Copyright (C) 2015 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * http://www.template-creator.com
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.form');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class JFormFieldCkcheckmoduleversion extends JFormField {

	protected $type = 'ckcheckmoduleversion';

	protected $url = 'http://www.joomlack.fr/en/joomla-extensions/maximenu-ck';

	protected function getLabel() {
		// check if the plugin is installed
		if (! JFile::exists(JPATH_SITE .'/modules/mod_maximenuck/mod_maximenuck.xml')) {
			return '';
		}

		$styles = 'background:#efefef;';
		$styles .= 'border: none;';
		$styles .= 'border-radius: 3px;';
		$styles .= 'color: red;';
		// $styles .= 'font-weight: bold;';
		$styles .= 'line-height: 24px;';
		$styles .= 'padding: 5px;';
		$styles .= 'margin: 3px 0;';
		$styles .= 'text-align: left;';
		$styles .= 'text-decoration: none;';

		$html = '';

		$current_version = $this->get_current_version();
		// check if the plugin params needs to be updated
		if (version_compare($this->element['version'], $current_version) > 0) {
			$html .= '<div id="'.$this->type.'updatealert" style="' . $styles . '"><b>MODULE MAXIMENU CK - ' . JText::_('PLG_MAXIMENUCK_NEED_UPDATE') . '</b><br />'
				. JText::_('PLG_MAXIMENUCK_YOU_HAVE_VERSION'). ' : <span class="label">' . $current_version . '</span><br />'
				. JText::_('PLG_MAXIMENUCK_REQUIRED_VERSION'). ' : <span class="label">' . $this->element['version'] . '</span>'
				. '<a href="'.$this->url.'" target="_blank" class="pull-right btn btn-info" style="font-size:1em;padding:0.2em 0.4em;margin: 0 0 0 5px;"><i class="icon icon-download"></i>' . JText::_('PLG_MAXIMENUCK_DOWNLOAD') . '</a>'
				. '</div>';
		}

		return $html;
	}

	protected function getPathToElements() {
		$localpath = dirname(__FILE__);
		$rootpath = JPATH_ROOT;
		$httppath = trim(JURI::root(), "/");
		$path = str_replace("\\", "/", str_replace($rootpath, $httppath, $localpath));
		return $path;
    }

	protected function getInput() {

		return '';
	}

	/*
	 * Get a variable from the manifest file.
	 * 
	 * @return the current version
	 */
	public static function get_current_version() {		
		// get the version installed
		$installed_version = 'UNKOWN';
		$file_url = JPATH_SITE .'/modules/mod_maximenuck/mod_maximenuck.xml';
		if ($xml_installed = JFactory::getXML($file_url)) {
			$installed_version = (string)$xml_installed->version;
		}

		return $installed_version;
	}
}

