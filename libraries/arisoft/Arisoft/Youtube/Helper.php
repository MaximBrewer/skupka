<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 *
 */
namespace Arisoft\Youtube;

defined('_JEXEC') or die;

class Helper
{
	private static $imageName = array(
		'DEFAULT' => 'default',
	
		'STANDARD_QUALITY' => 'sddefault',
	
		'MEDIUM_QUALITY' => 'mqdefault',
	
		'HIGH_QUALITY' => 'hqdefault'
	);
	
	private static $imageSize = array(
		0 => 'DEFAULT',
		
		1 => 'MEDIUM_QUALITY',
		
		2 => 'HIGH_QUALITY',
				
		'' => 'DEFAULT',
		
		'default' => 'DEFAULT',
		
		'm' => 'MEDIUM_QUALITY',
		
		'h' => 'HIGH_QUALITY'
	);
	
	public static function getVideoImage($videoId, $imageSize = 0)
	{
		$imageSize = self::resolveImageSize($imageSize);
		$imageName = self::$imageName[$imageSize];
		
		$imageUrl = '//img.youtube.com/vi/' . $videoId . '/' . $imageName . '.jpg';
		
		return $imageUrl;
	}
	
	public static function resolveImageSize($imageSize)
	{
		if (preg_match('/^\d+$/', $imageSize))
		{
			$imageSize = intval($imageSize, 10);
			if ($imageSize < 0 || !array_key_exists($imageSize, self::$imageSize))
				$imageSize = 0;
		}
		else
		{
			$imageSize = strtolower($imageSize);
			if (!array_key_exists($imageSize, self::$imageSize))
				$imageSize = '';			
		}
		
		return self::$imageSize[$imageSize];
	}

    public static function getVideoLink($videoId)
    {
        return 'http://www.youtube.com/watch?v=' . $videoId;
    }
}