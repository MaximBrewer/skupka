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
namespace Arisoft\Cache;

defined('_JEXEC') or die;

use JFactory;

class Cache
{
	static private function getCacheProvider()
	{
		$cache = JFactory::getCache('arisoft', '');
		$cache->setCaching(true);
		
		return $cache;
	}
	
	static private function getHash($key)
	{
		return md5($key);
	}
	
	static public function get($key)
	{
		$hash = self::getHash($key);		
		$cache = self::getCacheProvider();

		return $cache->get($hash);
	}

	static public function set($key, $data, $lifetime = 0)
	{
		$hash = self::getHash($key);
		$cache = self::getCacheProvider();

		if ($lifetime)
			$cache->setLifeTime($lifetime);

		$cache->store($data, $hash);

		return $data;
	}
}