<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 *
 */
namespace Arisoft\Vimeo;

defined('_JEXEC') or die;

use JHttpFactory;
use Arisoft\Cache\Cache as Cache;

class Helper
{
	const CACHE_LIFETIME = 432000; // 5 days
	
	public static function getVideoMetadata($videoId)
	{
		$cacheKey = 'vimeo.video.' . $videoId;
		
		$cachedData = Cache::get($cacheKey);
		if ($cachedData !== false)
			return $cachedData;

		$videoUrl = 'https://vimeo.com/' . $videoId;
		$http = JHttpFactory::getHttp();
		$response = $http->get('https://vimeo.com/api/oembed.json?url=' . urlencode($videoUrl));
		
		$data = null;
		if ($response->body)
		{
			$data = json_decode($response->body);

			if (!is_null($data))
				Cache::set($cacheKey, $data, self::CACHE_LIFETIME);
		}

		return $data;
	}

    public static function getVideoLink($videoId)
    {
        return 'https://www.vimeo.com/' . $videoId;
    }
}