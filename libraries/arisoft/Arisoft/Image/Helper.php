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

use JImage;

class Helper
{
    static $cache = array(
        'imageExt' => array(),

        'exifExist' => null
    );
	
	public static function isImageExtInstalled($ext = 'gd')
	{
		if (!array_key_exists($ext, self::$cache['imageExt']))
		{
			self::$cache['imageExt'][$ext] = extension_loaded($ext);
		}
		
		return self::$cache['imageExt'][$ext];
	}

    public static function getImageDimension($path)
    {
        $dim = array('w' => 0, 'h' => 0);

        if (@is_readable($path) && function_exists('getimagesize'))
        {
            $info = @getimagesize($path);
            if (!empty($info) && count($info) > 1)
            {
                $dim['w'] = $info[0];
                $dim['h'] = $info[1];
            }
        }

        return $dim;
    }

    public static function getThumbnailDimension($path, $w, $h)
    {
        $dim = array('w' => $w, 'h' => $h);
        if (($w && $h) || (!$w && !$h)) return $dim;

        if (@is_readable($path) && function_exists('getimagesize'))
        {
            $info = @getimagesize($path);
            if (!empty($info) && count($info) > 1)
            {
                if (empty($w))
                {
                    $w = round($h * $info[0] / $info[1]);
                    $dim['w'] = $w;
                }
                else if (empty($h))
                {
                    $h = round($w * $info[1] / $info[0]);
                    $dim['h'] = $h;
                }
            }
        }

        return $dim;
    }

    public static function generateThumbnailFileName($prefix, $originalImgPath, $width, $height, $type = 'resize')
    {
        if ($type == 'resize')
            $type = '';

        $path_parts = pathinfo($originalImgPath);
        return sprintf('%s_%s_%s_%s.%s',
            $prefix,
            md5($originalImgPath . $type),
            $width,
            $height,
            $path_parts['extension']
        );
    }

    private static function isExifAvailable()
    {
        if (is_null(self::$cache['exifExist']))
            self::$cache['exifExist'] = function_exists('exif_imagetype');

        return self::$cache['exifExist'];
    }

    public static function getImageType($filePath, $defaultType = IMAGETYPE_JPEG)
    {
        if (self::isExifAvailable())
        {
            $imageType = exif_imagetype($filePath);
            if ($imageType !== false)
                return $imageType;
        }

        $properties = JImage::getImageFileProperties($filePath);

        $imageType = $defaultType;
        switch ($properties->mime)
        {
            case 'image/jpeg':
                $imageType = IMAGETYPE_JPEG;
                break;

            case 'image/png':
                $imageType = IMAGETYPE_JPEG;
                break;

            case 'image/gif':
                $imageType = IMAGETYPE_GIF;
                break;
        }

        return $imageType;
    }

    public static function generateThumbnail($originalImgPath, $thumbDir, $prefix, $thumbWidth = 0, $thumbHeight = 0)
    {
        $thumbSize = self::getThumbnailDimension($originalImgPath, $thumbWidth, $thumbHeight);
        if (!$thumbSize['w'] || !$thumbSize['h'] || !@is_readable($originalImgPath))
            return null;

        $width = $thumbSize['w'];
        $height = $thumbSize['h'];

        $thumbName = self::generateThumbnailFileName($prefix, $originalImgPath, $width, $height);
        $thumbImgPath = $thumbDir . '/' . $thumbName;
        if (@file_exists($thumbImgPath) && @filemtime($thumbImgPath) > @filemtime($originalImgPath))
            return $thumbName;

        if (!self::isImageExtInstalled())
            return ;

        $img = new JImage($originalImgPath);//Asido::image($originalImgPath, $thumbImgPath);
        $thumbImg = $img->resize($width, $height);
        if (!$thumbImg->toFile($thumbImgPath, self::getImageType($originalImgPath)))
            return null;

        return $thumbName;
    }
}