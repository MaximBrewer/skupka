<?php
/**
 * @package         NoNumber Framework
 * @version         20.1.23725
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2020 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;

class NNCache
{
	static $cache = [];

	public static function has($hash)
	{
		return isset(self::$cache[$hash]);
	}

	public static function get($hash)
	{
		if ( ! isset(self::$cache[$hash]))
		{
			return false;
		}

		return is_object(self::$cache[$hash]) ? clone self::$cache[$hash] : self::$cache[$hash];
	}

	public static function set($hash, $data)
	{
		self::$cache[$hash] = $data;

		return $data;
	}

	public static function read($hash)
	{
		if (isset(self::$cache[$hash]))
		{
			return self::$cache[$hash];
		}

		$cache = JFactory::getCache('nonumber', 'output');

		return $cache->get($hash);
	}

	public static function write($hash, $data, $ttl = 0)
	{
		self::$cache[$hash] = $data;

		$cache = JFactory::getCache('nonumber', 'output');

		if ($ttl)
		{
			// convert ttl to minutes
			$cache->setLifeTime($ttl * 60);
		}

		$cache->setCaching(true);

		$cache->store($data, $hash);

		self::set($hash, $data);

		return $data;
	}
}
