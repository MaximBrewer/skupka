<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/


class helper {
	
	public static function getVersion(){
		return helper::getComponetManifestElement('version');
	}
	
	public static function getComponetManifestElement($elementName) {
		jimport('joomla.filesystem.folder');
		$folder = JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'components'.DIRECTORY_SEPARATOR.'com_vmeeplus';
		if (JFolder::exists($folder)) {
			$xmlFilesInDir = JFolder::files($folder, '.xml$');
		} else {
			$folder = JPATH_SITE .DIRECTORY_SEPARATOR. 'components'.DIRECTORY_SEPARATOR.$component;
			if (JFolder::exists($folder)) {
				$xmlFilesInDir = JFolder::files($folder, '.xml$');
			} else {
				$xmlFilesInDir = null;
			}
		}

		$xml_items = '';
		if (count($xmlFilesInDir))
		{
			foreach ($xmlFilesInDir as $xmlfile)
			{
				if ($data = JApplicationHelper::parseXMLInstallFile($folder.DIRECTORY_SEPARATOR.$xmlfile)) {
					foreach($data as $key => $value) {
						$xml_items[$key] = $value;
					}
				}
			}
		}
		
		if (isset($xml_items[$elementName]) && $xml_items[$elementName] != '' ) {
			return $xml_items[$elementName];
		} else {
			return '';
		}
	}
}
?>