<?php
/**
 * @package         NoNumber Framework
 * @version         18.12.3953
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2018 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\Installer\Installer as JInstaller;

class NNFrameworkNeeded
{
	public static function getOldInstalledExtensions()
	{
		$extensions = [
			'addtomenu'          => [5, 'Add to Menu'],
			'adminbardocker'     => [0, 'AdminBar Docker (discontinued)'],
			'advancedmodules'    => [6, 'Advanced Module Manager'],
			'advancedtemplates'  => [2, 'Advanced Template Manager'],
			'articlesanywhere'   => [5, 'Articles Anywhere'],
			'betterpreview'      => [5, 'Better Preview'],
			'cachecleaner'       => [5, 'Cache Cleaner'],
			'cdnforjoomla'       => [5, 'CDN for Joomla!'],
			'componentsanywhere' => [3, 'Components Anywhere'],
			'contenttemplater'   => [6, 'Content Templater'],
			'dbreplacer'         => [5, 'DB Replacer'],
			'dummycontent'       => [3, 'Dummy Content'],
			'emailprotector'     => [3, 'Email Protector'],
			'geoip'              => [1, 'GeoIP'],
			'iplogin'            => [3, 'IP Login'],
			'modalizer'          => [7, 'Modalizer (now Modals)'],
			'modals'             => [7, 'Modals'],
			'modulesanywhere'    => [5, 'Modules Anywhere'],
			'nonumbermanager'    => [6, 'NoNumber Extension Manager (now Regular Labs Extension Manager)'],
			'rereplacer'         => [7, 'ReReplacer'],
			'slider'             => [6, 'Slider (now Sliders)'],
			'sliders'            => [6, 'Sliders'],
			'snippets'           => [5, 'Snippets'],
			'sourcerer'          => [6, 'Sourcerer'],
			'tabber'             => [6, 'Tabber (now Tabs)'],
			'tabs'               => [6, 'Tabs'],
			'timedstyles'        => [0, 'Timed Styles (discontinued)'],
			'tooltips'           => [5, 'Tooltips'],
			'whatnothing'        => [11, 'What? Nothing!'],
		];

		$still_installed = [];

		foreach ($extensions as $extension => $data)
		{
			if ( ! $current_version = self::getCurrentVersion($extension))
			{
				// Extension not found
				continue;
			}

			$version = $data[0];

			if ( ! $version || $current_version < $version)
			{
				// An extension (version) is installed that still needs the NoNumber framework
				$still_installed[] = $data[1];
			}
		}

		// No extensions found that still needs the NoNumber framework
		return $still_installed;
	}

	private static function getCurrentVersion($extension)
	{
		if ( ! $xml = self::getXmlFile($extension))
		{
			return;
		}

		$xml = JInstaller::parseXMLInstallFile($xml);

		if ( ! isset($xml['version']))
		{
			return;
		}

		return $xml['version'];
	}

	private static function getXmlFile($extension)
	{
		jimport('joomla.filesystem.file');

		$paths = [
			JPATH_ADMINISTRATOR . '/components/com_' . $extension . '/' . $extension . '.xml',
			JPATH_ADMINISTRATOR . '/modules/mod_' . $extension . '/mod_' . $extension . '.xml',
			JPATH_SITE . '/plugins/system/' . $extension . '/' . $extension . '.xml',
			JPATH_SITE . '/plugins/editors-xtd/' . $extension . '/' . $extension . '.xml',
		];

		foreach ($paths as $path)
		{
			if ( ! JFile::exists($path))
			{
				continue;
			}

			return $path;
		}

		return false;
	}
}
