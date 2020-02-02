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

class CacheLite
{
	private static $cache = array();

	static public function exists($key)
	{
		return isset(self::$cache[$key]);
	}

	static public function get($key, $defaultValue = false)
	{
		if (!isset(self::$cache[$key]))
		{
			return $defaultValue;
		}

		return self::$cache[$key];
	}

	static public function set($key, $data)
	{
		if (is_object($data))
			$data = clone($data);

		self::$cache[$key] = $data;
	}
}