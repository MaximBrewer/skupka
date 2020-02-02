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
namespace Arisoft\Image;

defined('_JEXEC') or die;

jimport('joomla.filesystem.path');
jimport('joomla.filesystem.folder');

use JImage, JPath, JURI, JFolder;
use \Joomla\Utilities\ArrayHelper as ArrayHelper;
use \Arisoft\Parameters\Helper as ParametersHelper;
use \Arisoft\Utilities\Utilities as Utilities;
use \Arisoft\Utilities\AppUtilities as AppUtilities;
use \Arisoft\Csv\Csvparser as CSVParser;
use \Arisoft\Template\Template as Template;

class ThumbnailThumbnailSettings
{
    public $generateThumbnails;
    public $thumbWidth;
    public $thumbHeight;
    public $thumbPath;

    function __construct($params = null)
    {
        $this->populate($params);
    }

    function populate($params)
    {
        $thumbWidth = intval(ArrayHelper::getValue($params, 'thumbWidth', 0), 10);
        if ($thumbWidth < 0)
            $thumbWidth = 0;

        $thumbHeight = intval(ArrayHelper::getValue($params, 'thumbHeight', 0), 10);
        if ($thumbHeight < 0)
            $thumbHeight = 0;

        $generateThumbs = (($thumbWidth || $thumbHeight) && Helper::isImageExtInstalled())
            ? (bool)ArrayHelper::getValue($params, 'generateThumbs', false)
            : false;

        $thumbPath = trim(ArrayHelper::getValue($params, 'thumbPath', ''));
        if ($thumbPath)
            $thumbPath = preg_replace('#^[/\\\\]+|[/\\\\]+$#', '', JPath::clean($thumbPath));

        $this->thumbWidth = $thumbWidth;
        $this->thumbHeight = $thumbHeight;
        $this->generateThumbnails = $generateThumbs;
        $this->thumbPath = $thumbPath;
    }
}

class ThumbnailSettings
{
    public $imagePathList;
    public $cachePeriod = 0;
    public $sortDirection = 'asc';
    public $sortBy;
    public $fileFilter;
    public $metaFile;
    public $defaultTitle = '';

    public $thumbnails = array();

    function __construct($params = null)
    {
        $this->populate($params);
    }

    private function populate($params)
    {
        $cachePeriod = intval(ArrayHelper::getValue($params, 'cachePeriod', 0), 10);
        if ($cachePeriod < 0)
            $cachePeriod = 0;

        $scanSubFolders = (bool)ArrayHelper::getValue($params, 'subdir', false);
        $folders = $this->prepareFolders(trim(ArrayHelper::getValue($params, 'dir', '')), $scanSubFolders);

        $sortDir = strtolower(ArrayHelper::getValue($params, 'sortDir', 'asc'));
        if (!in_array($sortDir, array('asc', 'desc')))
            $sortDir = 'asc';

        $this->cachePeriod = $cachePeriod;
        $this->metaFile = trim(ArrayHelper::getValue($params, 'metaFile', ''));
        $this->defaultTitle = trim(ArrayHelper::getValue($params, 'defaultTitle', ''));
        $this->imagePathList = $folders;
        $this->fileFilter = ArrayHelper::getValue($params, 'fileFilter', '\.(jpg|gif|jpeg|png|bmp|JPG|GIF|JPEG|BMP)$');
        $this->sortDirection = $sortDir;
        $this->sortBy = strtolower(ArrayHelper::getValue($params, 'sortBy', ''));

        $treeParams = ParametersHelper::getRootParameters(ParametersHelper::flatParametersToTree($params));

        if (isset($treeParams['thumb']) && is_array($treeParams['thumb']))
        {
            foreach ($treeParams['thumb'] as $thumbKey => $thumbParams)
            {
                $thumbItemParams = new ThumbnailThumbnailSettings($thumbParams);

                $this->thumbnails[$thumbKey] = $thumbItemParams;
            }
        }
    }

    private function prepareFolders($folders, $scanSubFolders = true)
    {
        $findFolders = array();
        if (empty($folders))
            return $findFolders;

        $folders = str_replace(array("\r","\n"), array('',';'), $folders);
        $folders = explode(';', $folders);
        array_walk($folders, 'trim');
        foreach ($folders as $folder)
        {
            if (empty($folder))
                continue ;

            $folder = Utilities::resolvePath($folder);
            if (!@file_exists($folder) || !@is_dir($folder))
                continue ;

            $folder = preg_replace('#[/\\\\]+$#', '', JPath::clean($folder));
            $findFolders[] = $folder;
            if ($scanSubFolders)
            {
                $subFolders = JFolder::folders($folder, '.', true, true);
                if (!empty($subFolders) && count($subFolders) > 0) $findFolders = array_merge($findFolders, $subFolders);
            }
        }

        return array_unique($findFolders);
    }
}

class Thumbnail
{
	private $prefix;
	private $cacheDir;
    private $key;
    private $checkSum;
    private $params;

	function __construct($key, $params, $prefix = 'arithumb', $cacheDir = null)
	{
        $this->key = $key;
		$this->prefix = $prefix;
		$this->cacheDir = $cacheDir;
        $this->checkSum = md5(serialize($params));
        $this->params = new ThumbnailSettings($params);

        if (empty($key))
            $this->key = $this->checkSum;
	}
	
	public function getStoredData()
	{
        $params = $this->params;
		$cachePeriod = $params->cachePeriod;
		$data = null;

		if ($cachePeriod > 0)
		{
			$needReCache = true;
			$cacheDir = $this->cacheDir;
			$key = $this->key;
			$checkSum = $this->checkSum;

			$cacheCheckFile = $cacheDir . '/' . $key . '.txt';
			$cacheDataFile = $cacheDir . '/' . $key . '.php';
			if (@file_exists($cacheCheckFile) && @file_exists($cacheDataFile))
			{
				$oldCheckSum = trim(@file_get_contents($cacheCheckFile));
				if ($oldCheckSum == $checkSum)
				{
					$needReCache = (filemtime($cacheCheckFile) + $cachePeriod * 60 < time());
				}
			}
			
			global $_THUMB_CACHED_DATA;
			if ($needReCache)
			{
				$this->prepare();
				$data = $this->getData();
				
				$h = fopen($cacheCheckFile, 'w');
				fwrite($h, $checkSum);
				fclose($h);
				
				if (!isset($_THUMB_CACHED_DATA)) $_THUMB_CACHED_DATA = array();
				
				$cachedData = var_export($data, true);
				$h = fopen($cacheDataFile, 'w');
				fwrite($h, sprintf('<?php%1$sdefined("ARI_FRAMEWORK_LOADED") or die("Direct Access to this location is not allowed.");%1$s$_THUMB_CACHED_DATA["%2$s"] = %3$s;?>',
					"\n",
					$key,
					$cachedData));
				fclose($h);
			}
			else 
			{
				require_once $cacheDataFile;

				$data = $_THUMB_CACHED_DATA[$key];
			}
		}
		else 
		{
			$this->prepare();
			$data = $this->getData();
		}

		if ($params->sortBy == 'random')
			shuffle($data);

		return $data;
	}
	
	private function prepare()
	{
        $params = $this->params;

        foreach ($params->thumbnails as $thumbnailParams)
        {
            if ($thumbnailParams->generateThumbnails)
                $this->generateThumbnails($thumbnailParams);
        }
	}

	private function getData()
	{
        $params = $this->params;
		$data = array();
		
		$prefix = $this->prefix;
		$descrFile = $params->metaFile;
        $defaultTitle = $params->defaultTitle;
        $needProcessEmptyTitle = ($defaultTitle && strpos($defaultTitle, '{$') !== false);;
		$cacheDir = $this->cacheDir;
		$cacheUri = str_replace('\\', '/', $cacheDir);
		$rootUri = str_replace('\\', '/', JPATH_ROOT . DIRECTORY_SEPARATOR);
		if (strpos($cacheUri, $rootUri) === 0)
			$cacheUri = substr($cacheUri, strlen($rootUri));
		$cacheUri = JURI::root(true) . '/' . str_replace(DIRECTORY_SEPARATOR, '/', $cacheUri);

		$folders = $params->imagePathList;
        $sortBy = $params->sortBy;
        $sortDir = $params->sortDirection;

		foreach ($folders as $folder)
		{
			$descriptions = $this->getDescriptions($descrFile, $folder);
			$files = $this->getImageFiles($folder, $params->fileFilter);
			$inCSV = false;
			$folderData = array();
			foreach ($files as $file)
			{
				$fileUri = Utilities::absPath2Url($file);
				$relFilePath = Utilities::absPath2Relative($file);
				$filePath = $file;
				$imgSize = Helper::getImageDimension($filePath);

				$baseFileName = basename($file);
				$dataItem = array(
					'fileName' => $baseFileName,
					'relFilePath' => $relFilePath,
                    'image' => array(
                        'url' => $fileUri,
                        'w' => $imgSize['w'],
                        'h' => $imgSize['h']
                    ),
                    'thumb' => array()
				);

                foreach ($params->thumbnails as $thumbnailKey => $thumbnailParams)
                {
                    $thumbPath = $thumbnailParams->thumbPath;
                    $thumbWidth = $thumbnailParams->thumbWidth;
                    $thumbHeight = $thumbnailParams->thumbHeight;
                    //$thumbType = $thumbnailParams->thumbType;
                    //$thumbTypeParams = $thumbnailParams->thumbTypeParams;
                    //$thumbBehavior = $thumbType == 'resize' ? AriUtils::getParam($thumbTypeParams, 'behavior') : null;

                    $thumbImagePath = $thumbPath
                        ? $folder . '/' . str_replace('{$fileName}', $baseFileName, $thumbPath)
                        : null;

                    if ($thumbImagePath && file_exists($thumbImagePath) && is_readable($thumbImagePath))
                    {
                        $thumbSize = getimagesize($thumbImagePath);

                        $dataItem['thumb'][$thumbnailKey] = array(
                            'url' => Utilities::absPath2Url(
                                str_replace(DIRECTORY_SEPARATOR, '/', $folder . '/' . str_replace('{$fileName}', $baseFileName, $thumbPath))
                            ),
                            'w' => $thumbSize[0],
                            'h' => $thumbSize[1]
                        );
                    }
                    else
                    {
                        $thumbSize = Helper::getThumbnailDimension($filePath, $thumbWidth, $thumbHeight);

                        $dataItem['thumb'][$thumbnailKey] = array(
                            'url' => $fileUri,
                            'w' => $thumbSize['w'],
                            'h' => $thumbSize['h']
                        );

                        if ($thumbSize['w'] && $thumbSize['h'])
                        {
                            $thumbFile = Helper::generateThumbnailFileName($prefix, $filePath, $thumbSize['w'], $thumbSize['h']);
                            if (@file_exists($cacheDir . '/' . $thumbFile))
                                $dataItem['thumb'][$thumbnailKey]['url'] = Utilities::absPath2Url(
                                    $cacheUri . '/' . $thumbFile
                                );
                        }
                    }
                }

				if (isset($descriptions[$baseFileName]))
				{
					$dataItem = array_merge($descriptions[$baseFileName], $dataItem);
					$inCSV = true;
				}

				if ($defaultTitle && empty($dataItem['Title']))
				{
					$title = $defaultTitle;
					if ($needProcessEmptyTitle)
					{
						$pathInfo = pathinfo($baseFileName);
                        $title = Template::parse(
                            $title,
                            array(
                                'fileName' => $baseFileName,
                                'baseFileName' => basename($baseFileName, '.' . $pathInfo['extension'])
                            )
                        );
					}
					
					$dataItem['Title'] = $title;
				}

				$key = $this->getDataItemKey($filePath, $baseFileName, $sortBy, $inCSV);
				if (empty($key))
					$folderData[$baseFileName] = $dataItem;
				else
					$folderData[$key] = $dataItem;
			}
			
			if (count($folderData) == 0)
				continue ;
				
			if ($inCSV && $sortBy == 'csv')
			{
				$tempData = array();
				
				foreach ($descriptions as $fileName => $value)
				{
					if (!isset($folderData[$fileName]))
						continue ;
						
					$tempData[] = $folderData[$fileName];
					unset($folderData[$fileName]);
				}
				
				if ($sortDir == 'desc')
					$tempData = array_reverse($tempData);

				$folderData = array_values($folderData);
				$folderData = array_merge($tempData, array_values($folderData));
			}
			else if ($sortBy != 'filename' && $sortBy != 'modified')
			{
				$folderData = array_values($folderData);
			}
				
			$data = array_merge($data, $folderData);
		}
		
		if ($sortBy && $sortBy != 'random' && $sortBy != 'csv')
		{
			if ($sortDir == 'asc')
			{
				uksort($data, 'strnatcasecmp');
				//ksort($data, SORT_NATURAL);
			}
			else
			{
				uksort($data, 'strnatcasecmp');
				$data = array_reverse($data, true);
				//krsort($data, SORT_NATURAL);
			} 
		}

		return $data;
	}
	
	private function getDataItemKey($file, $baseFileName, $sortBy)
	{
		$key = null;
		switch ($sortBy)
		{
			case 'filename':
				$key = $baseFileName . md5($file);
				break;
				
			case 'modified':
				$key = filemtime($file) . md5($file);
				break;
		}
		
		return $key;
	}
	
	private function getDescriptions($fileName, $path)
	{
		$data = array();
		$filePath = AppUtilities::getLocalizedFileName($path . '/' . $fileName);

		if (empty($fileName) || @!file_exists($filePath) || !@is_readable($filePath))
			return $data;

		$csvParser = new CSVParser();
		$csvParser->auto($filePath);
		$csvData = $csvParser->data;

		if (!empty($csvData))
		{
			foreach ($csvData as $csvDataItem)
			{
				if (isset($csvDataItem['File']))
					$data[$csvDataItem['File']] = $csvDataItem;
			}
		}

		return $data;
	}

	private function generateThumbnails($thumbnailParams)
	{
        $mainParams = $this->params;

		$thumbWidth = $thumbnailParams->thumbWidth;
		$thumbHeight = $thumbnailParams->thumbHeight;
		$folders = $mainParams->imagePathList;
		$cacheDir = $this->cacheDir;
		$prefix = $this->prefix;
		$thumbPath = $thumbnailParams->thumbPath;

		foreach ($folders as $folder)
		{
			$files = $this->getImageFiles($folder, $mainParams->fileFilter);
			foreach ($files as $filePath)
			{
				if ($thumbPath)
				{
					$thumbImagePath = $folder . '/' . str_replace('{$fileName}', basename($filePath), $thumbPath);
					if (file_exists($thumbImagePath))
						continue ;
				}

				Helper::generateThumbnail(
					$filePath, 
					$cacheDir, 
					$prefix, 
					$thumbWidth, 
					$thumbHeight
                );
			}
		}
	}

	private function getImageFiles($folder, $filter = '\.(jpg|gif|jpeg|png|bmp|JPG|GIF|JPEG|BMP)$')
	{
		return JFolder::files($folder, $filter, false, true);
	}
}