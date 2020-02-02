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

use JImage, JPath, JURI, JFolder;
use \Joomla\Utilities\ArrayHelper as ArrayHelper;
use \Arisoft\Parameters\Helper as ParametersHelper;
use \Arisoft\Utilities\Utilities as Utilities;
use \Arisoft\Html\Helper as HtmlHelper;

class InlineThumbnailThumbnailSettings
{
    public $generateThumbnails;
    public $thumbWidth;
    public $thumbHeight;
    public $useInlineDimensions;

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

        $generateThumbs = Helper::isImageExtInstalled()
            ? (bool)ArrayHelper::getValue($params, 'generateThumbs', true)
            : false;

        $useInlineDimensions = (bool)ArrayHelper::getValue($params, 'useInlineDimensions', true);

        $this->thumbWidth = $thumbWidth;
        $this->thumbHeight = $thumbHeight;
        $this->generateThumbnails = $generateThumbs;
        $this->useInlineDimensions = $useInlineDimensions;
    }
}

class InlineThumbnailSettings
{
    public $ignoreEmptyDimension = false;
    public $ignoreRemote = false;
    public $ignoreFolders = array();
    public $cachePeriod = 0;
    public $ignoreCssClasses = null;
    public $thumbCount = 0;

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

        $scanSubFolders = (bool)ArrayHelper::getValue($params, 'ignoreSubFolders', false);

        $this->ignoreRemote = (bool)ArrayHelper::getValue($params, 'ignoreRemote', 0);
        $this->ignoreFolders = $this->prepareFolders(ArrayHelper::getValue($params, 'ignoreFolders', ''), $scanSubFolders);
        $this->ignoreCssClasses = $this->prepareCssClasses(ArrayHelper::getValue($params, 'ignoreClasses', ''));
        $this->cachePeriod = $cachePeriod;

        $treeParams = ParametersHelper::getRootParameters(ParametersHelper::flatParametersToTree($params));

        if (isset($treeParams['thumb']) && is_array($treeParams['thumb']))
        {
            foreach ($treeParams['thumb'] as $thumbKey => $thumbParams)
            {
                $thumbItemParams = new InlineThumbnailThumbnailSettings($thumbParams);

                $this->thumbnails[$thumbKey] = $thumbItemParams;
            }
        }
    }

    private function prepareCssClasses($classes)
    {
        $classes = ParametersHelper::prepareMultipleInlineParameter($classes, 'class');

        return $classes;
    }

    private function prepareFolders($folders, $scanSubFolders = true)
    {
        $folders = ParametersHelper::prepareMultipleInlineParameter($folders, 'folder');

        $findFolders = array();
        if (empty($folders))
            return $findFolders;

        foreach ($folders as $folder)
        {
            if (!@file_exists($folder) || !@is_dir($folder))
                continue ;

            $folder = preg_replace('#[/\\\\]+$#', '', JPath::clean($folder));
            $findFolders[] = $folder;
            if ($scanSubFolders)
            {
                $subFolders = JFolder::folders($folder, '.', true, true);
                if (!empty($subFolders) && count($subFolders) > 0)
                    $findFolders = array_merge($findFolders, $subFolders);
            }
        }

        return $findFolders;
    }
}

class InlineThumbnail
{
	protected $prefix;
	protected $cacheDir;
    protected $params;

	function __construct($params, $prefix = 'arithumb', $cacheDir = null)
	{
		if (is_null($cacheDir))
		{
			$cacheDir = JPATH_ROOT . '/cache';
		}
		
		$this->prefix = $prefix;
		$this->cacheDir = $cacheDir;
        $this->params = new InlineThumbnailSettings($params);
	}
	
	public function updateContent($content, $updateCallback = null)
	{
		$images = $this->getImages($content);
		if (is_null($updateCallback))
			$updateCallback = array(&$this, 'updateCallback');
			
		return call_user_func($updateCallback, $content, $images, $this->params);
	}

	protected function updateCallback($content, $images, $params)
	{
		$originalImages = array();
		$updatedImages = array();
		
		foreach ($images as $image)
		{
			$originalImage = $image['image'];
			$thumbImage = $image['thumb'];
			
			$originalImages[] = $originalImage['original'];
			$updatedImages[] = sprintf('<a %1$s><img %2$s /></a>',
				HtmlHelper::getAttrStr($originalImage['attributes']),
				HtmlHelper::getAttrStr($thumbImage['attributes']));
		}

		return str_replace($originalImages, $updatedImages, $content);
	}
	
	protected function getImages($content)
	{
        $params = $this->params;

		$images = array();
		$matches = array();
		$clearContent = strip_tags($content, '<img>');
		preg_match_all('/<img.*?>/i', $clearContent, $matches);
 		if (!empty($matches[0]))
 		{
            $rootUri = JURI::root();
 			$prefix = $this->prefix;
 			$cacheDir = $this->cacheDir;
 			$cacheUri = $cacheDir;
			if (strpos($cacheUri, JPATH_ROOT . '/') === 0)
				$cacheUri = substr($cacheUri, strlen(JPATH_ROOT . '/'));
			$cacheUri = str_replace('\\', '/', $cacheUri) . '/';
 			
			$i = 0;
			$thumbCount = $params->thumbCount;
			$ignoreEmptyDim = $params->ignoreEmptyDimension;
			$ignoreRemote = $params->ignoreRemote;
			$ignoreFolders = $params->ignoreFolders;
            $ignoreCssClasses = $params->ignoreCssClasses;
            $hasIgnoreClass = count($ignoreCssClasses) > 0;
			if (!is_array($ignoreFolders) || count($ignoreFolders) == 0)
				$ignoreFolders = null;

 			foreach ($matches[0] as $match)
 			{
 				$attrs = HtmlHelper::extractAttrs($match);
 				$src = ArrayHelper::getValue($attrs, 'src', '');
 				if (empty($src))
 					continue ;

                $isFullLocalUrl = (strpos($src, $rootUri) === 0);
 				$isRemote = !$isFullLocalUrl && preg_match('/^(http(s)\:|ftp\:|\/\/)/i', $src);//(strpos($src, 'http') === 0 && !$isFullLocalUrl);
 				if ($ignoreRemote && $isRemote)
 					continue ;

 				if ($ignoreFolders && !$isRemote)
 				{
                    if ($isFullLocalUrl)
                        $src = str_replace($rootUri, '', $src);

 					$imgFolder = preg_replace('#^[/\\\\]+|[/\\\\]+$#', '', JPath::clean(dirname($src)));
					if (in_array($imgFolder, $ignoreFolders))
						continue ;
 				}

                if (!empty($attrs['class']) && $hasIgnoreClass)
                {
                    $imgClasses = preg_split('/\s+/', trim($attrs['class']));
                    $break = false;
                    foreach ($imgClasses as $imgClass)
                    {
                        if (in_array($imgClass, $ignoreCssClasses))
                        {
                            $break = true;
                            break;
                        }
                    }

                    if ($break)
                        continue ;
                }

                $inlineWidth = 0;
                $inlineHeight = 0;

 				if (!empty($attrs['style']) || !empty($attrs['width']) || !empty($attrs['height']))
 				{
 					$imgStyles = !empty($attrs['style']) ? HtmlHelper::extractInlineStyles($attrs['style']) : null;
                    $inlineWidth = isset($imgStyles['width']) ? intval($imgStyles['width'], 10) : 0;
                    $inlineHeight = isset($imgStyles['height']) ? intval($imgStyles['height'], 10) : 0;

 					if ($inlineWidth < 1 && !empty($attrs['width']))
                        $inlineWidth = @intval($attrs['width'], 10);

                    if ($inlineHeight < 1 && !empty($attrs['height']))
                        $inlineHeight = @intval($attrs['height'], 10);
 				}

 				$title = ArrayHelper::getValue(
                    $attrs,
                    'alt',
                    ArrayHelper::getValue(
                        $attrs,
                        'title',
                        ''
                    )
                );

 				$imgAttrs = array('title' => $title, 'href' => $src);

 				$image = array(
 					'image' => array(
                        'attributes' => $imgAttrs,
 						'original' => $match,
 						'originalAttributes' => $attrs,
 						'title' => $title,
 						'src' => $src
                    ),
 					'thumb' => array(
 					)
 				);

                foreach ($params->thumbnails as $thumbnailKey => $thumbnailParams)
                {
                    $thumbWidth = $thumbnailParams->thumbWidth;
                    $thumbHeight = $thumbnailParams->thumbHeight;

                    if ($thumbnailParams->useInlineDimensions)
                    {
                        if ($inlineWidth > 0)
                            $thumbWidth = $inlineWidth;

                        if ($inlineHeight > 0)
                            $thumbHeight = $inlineHeight;
                    }

                    $thumbAttrs = array('alt' => $title);
                    $thumbData = array(
                        'src' => $src,
                        'width' => $thumbWidth,
                        'height' => $thumbHeight,
                        'atttributes' => null,
                        'asOriginal' => false
                    );

                    if ($thumbnailParams->generateThumbnails && ($thumbCount < 1 || $i < $thumbCount))
                    {
                        $imgPath = $src;
                        $baseUrl = strtolower(JURI::base());
                        if (strpos(strtolower($imgPath), $baseUrl) === 0)
                            $imgPath = substr($imgPath, strlen($baseUrl));

                        if (!preg_match('/^(http|https|ftp):\/\//i', $imgPath))
                        {
                            $imgPath = JPATH_ROOT . '/' . $imgPath;
                            $originalSize = @getimagesize($imgPath);

                            if (
                                    (!$ignoreEmptyDim || !$thumbnailParams->useInlineDimensions || $inlineWidth > 0 || $inlineHeight > 0) &&
                                    (
                                        !is_array($originalSize) || count($originalSize) < 2 ||
                                        (
                                            ($thumbWidth > 0 && $originalSize[0] != $thumbWidth) ||
                                            ($thumbHeight > 0 && $originalSize[1] != $thumbHeight)
                                        )
                                    )
                            )
                            {
                                $thumbFile = Helper::generateThumbnail(
                                    $imgPath,
                                    $cacheDir,
                                    $prefix,
                                    $thumbWidth,
                                    $thumbHeight
                                );
                                if ($thumbFile)
                                {
                                    $size = @getimagesize($cacheDir . '/' . $thumbFile);
                                    if (is_array($size) && count($size) > 1)
                                    {
                                        $thumbData['width'] = $size[0];
                                        $thumbData['height'] = $size[1];
                                    }

                                    $thumbData['src'] = $cacheUri . $thumbFile;
                                }
                            }
                            else
                            {
                                $thumbData['asOriginal'] = true;
                            }
                        }
                    }

                    $thumbAttrs['src'] = $thumbData['src'];
                    if ($thumbData['width'] > 0)
                        $thumbAttrs['width'] = $thumbData['width'];
                    if ($thumbData['height'] > 0)
                        $thumbAttrs['height'] = $thumbData['height'];
                    if (isset($attrs['border']))
                        $thumbAttrs['border'] = $attrs['border'];

                    $thumbData['attributes'] = $thumbAttrs;
                    $image['thumb'][$thumbnailKey] = $thumbData;
                }

 				$images[] = $image;
 				++$i;
 			}
 		}

 		return $images;
	}
}