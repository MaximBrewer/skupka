<?php


defined('JPATH_PLATFORM') or die;


class JCacheStorageNocache extends JCacheStorage
{
	
	public function get($id, $group, $checkTime = true)
	{
		return false; 
	}

	
	public function getAll()
	{
		return false; 
		
	}

	
	public function store($id, $group, $data)
	{
		return true; 
	}

	
	public function remove($id, $group)
	{
		return true; 
	}

	
	public function clean($group, $mode = null)
	{
		return true; 
		
	}

	/**
	 * Garbage collect expired cache data
	 *
	 * @return  boolean
	 *
	 * @since   11.1
	 */
	public function gc()
	{
		return true; 
	}

	
	public static function isSupported()
	{
		return true; 
	}

	
	public function lock($id, $group, $locktime)
	{
		$returning             = new stdClass;
		$returning->locklooped = true;
		$returning->locked     = true;
		return $returning;

		
	}

	public function unlock($id, $group = null)
	{
		return true; 
	}
}
