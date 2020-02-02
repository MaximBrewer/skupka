<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 *
 */
namespace Arisoft\Utilities;

defined('_JEXEC') or die;

use JFactory, JFolder;

class AppUtilities
{
	static public function getExtraFieldsFromINI($path, $iniFileName, $recurse = false, $fullPath = false, $i18n = false)
	{
		$fields = array();

		$iniFileName = basename($iniFileName);
		if (empty($iniFileName))
			return $fields;

		if ($recurse)
		{
			$subFolders = JFolder::folders($path);
			foreach ($subFolders as $subFolder)
			{
				$subFolderFields = self::getExtraFieldsFromINI($path . '/' . $subFolder, $iniFileName, $recurse, $fullPath);
				if (count($subFolderFields) > 0)
					$fields = array_merge($fields, $subFolderFields);
			}
		}

		
		$filePath = JPATH_ROOT . '/' . $path . '/' . $iniFileName;
		if ($i18n)
			$filePath = self::getLocalizedFileName($filePath);
		
		if (!@file_exists($filePath) || !is_file($filePath) || !is_readable($filePath))
			return $fields;

		$iniFields = parse_ini_file($filePath, true);
		if (empty($iniFields))
			return $fields;
			
		foreach ($iniFields as $secName => $secItems)
		{
			$prop = strtolower($secName);
			foreach ($secItems as $itemKey => $itemValue)
			{
				$key = $itemKey;
				if ($fullPath)
					$key = $path . '/' . $key;
				if (!isset($fields[$key]))
					$fields[$key] = array();
					
				$fields[$key][$prop] = $itemValue;
			}
		}
			
		return $fields;
	}
	
	public static function getLocalizedFileName($filePath)
	{
		if (empty($filePath))
			return $filePath;
		
		$lang = JFactory::getLanguage(); 
		$langTag = $lang->get('tag');

		if (empty($langTag))
			return $filePath;

		$defLang = $lang->getDefault();
		$prefLangs = array($langTag);
		if ($defLang != $langTag)
			$prefLangs[] = $defLang;
		
		$pathInfo = pathinfo($filePath);
		$baseName = !empty($pathInfo['extension']) ? basename($filePath, '.' . $pathInfo['extension']) : $pathInfo['basename'];
		foreach ($prefLangs as $prefLang)
		{
			$langFile = $pathInfo['dirname'] . '/' . $baseName . '.' . $prefLang;
			if (!empty($pathInfo['extension']))
				$langFile .= '.' . $pathInfo['extension'];

			if (@file_exists($langFile) && is_file($langFile))
			{
				$filePath = $langFile;
				break;
			}
		}

		return $filePath;
	}
}