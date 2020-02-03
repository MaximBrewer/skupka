<?php
/**
 * @copyright	Copyright (C) 2017 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * @license		GNU/GPL
 * */

defined('JPATH_PLATFORM') or die;

require_once 'ckformfield.php';

class JFormFieldCkproonly extends CKFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 */
	protected $type = 'ckproonly';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 */
	protected function getLabel()
	{
		return '';
	}

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 */
	protected function getInput()
	{
		$text = $this->element['label'] ? JText::_($this->element['label']) : JText::_('SLIDESHOWCK_ONLY_PRO');
		$html = '<div class="ckinfo"><i class="fas fa-info"></i><a href="https://www.joomlack.fr/en/joomla-extensions/slideshow-ck" target="_blank">' . $text . '</a></div>';

		return $html;
	}

	/*
	 * Get a variable from the manifest file
	 * 
	 * @return the current version
	 */
	public static function getCurrentVersion($file_url) {
		// get the version installed
		$installed_version = 'UNKOWN';
		if ($xml_installed = simplexml_load_file($file_url)) {
			$installed_version = (string)$xml_installed->version;
		}

		return $installed_version;
	}
}
