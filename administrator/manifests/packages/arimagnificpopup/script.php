<?php
/*
 * ARI Framework
 *
 * @package		ARI Framework
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class pkg_arimagnificpopupInstallerScript
{
	function preflight($type, $parent)
	{
		$type = strtolower($type);
		if ($type == 'install' || $type == 'update')
		{
            if (!$this->isARISoftLibInstalled())
                $this->installARISoftLib();

            $baseDir = dirname(__FILE__);
			if (!$this->extractFiles($baseDir . '/shared/arisoft_lib.zip', JPATH_LIBRARIES . '/arisoft') ||
                !$this->extractFiles($baseDir . '/shared/arisoft_media.zip', JPATH_ROOT . '/media/arisoft'))
                return false;
		}
	}
	
	function postflight($type, $parent)
	{
		$type = strtolower($type);
		if ($type == 'install')
		{
            $this->enablePlugins();
			$this->createFolders();
		}
	}
	
	function createFolders()
	{
		$thumbFolder = JPATH_ROOT . '/images/arimagnificpopup';

		if (!JFolder::exists($thumbFolder))
			JFolder::create($thumbFolder);
	}

    function enablePlugins()
    {
        $db = JFactory::getDBO();

        $db->setQuery(
            'UPDATE #__extensions SET `enabled` = 1 WHERE `type` = "plugin" AND `element` = "arimagnificpopup" AND `folder` IN ("editors-xtd", "system")'
        );
        $db->query();
    }

    private function installARISoftLib()
    {
        $extPath = dirname(__FILE__) . '/packages/lib_arisoft.zip';
        $installResult = JInstallerHelper::unpack($extPath);
        if (empty($installResult))
        {
            return false;
        }

        $installer = new JInstaller();
        $installer->setOverwrite(true);
        if (!$installer->install($installResult['extractdir']))
        {
            return false;
        }
    }

    private function isARISoftLibInstalled()
    {
        $db = JFactory::getDBO();

        $db->setQuery('SELECT extension_id FROM #__extensions WHERE `type` = "library" AND `element` = "arisoft" LIMIT 0,1');
        $extId = $db->loadResult();

        return !empty($extId);
    }

    private function extractFiles($archivePath, $destPath)
    {
        if (!JFolder::exists($destPath) && !JFolder::create($destPath))
        {
            JFactory::getApplication()->enqueueMessage(
                'ARI Magnific Popup: the installer can\'t create "' . $destPath . "' folder. Check file permissions or create the folder manually and install the extension again.",
                'error'
            );

            return false;
        }

        $packager = null;
        if (!($packager = JArchive::getAdapter('zip')) || !$packager->extract($archivePath, $destPath))
        {
            JFactory::getApplication()->enqueueMessage(
                'ARI Magnific Popup: could not extract files from ' . basename($archivePath) . ' archive.',
                'error'
            );

            return false;
        }

        return true;
    }
}