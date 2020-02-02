<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Cache
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * File cache storage handler
 *
 * @since  11.1
 * @note   For performance reasons this class does not use the Filesystem package's API
 */
class JCacheStorageSql extends JCacheStorage
{
	/**
	 * Root path
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $_root;
	static $_staticcache; 
	static $_db; 
	static $_todo; 
	/**
	 * Constructor
	 *
	 * @param   array  $options  Optional parameters
	 *
	 * @since   11.1
	 */
	public function __construct($options = array())
	{
		parent::__construct($options);
		$this->_root = '';
		self::$_db = JFactory::getDBO(); 
	}
	private function setStatic($id, $group, $data) {
	   $hash = $this->_getFilePath($id, $group);
	   if (empty(self::$_staticcache)) self::$_staticcache = array(); 
	   $ret = false;
	   if (isset(self::$_staticcache[$hash])) { $ret = true; }
	   self::$_staticcache[$hash] = $data; 
	   return $ret; 
	   
	}
	private function getStatic($id, $group) {
	  $hash = $this->_getFilePath($id, $group);
	  if (isset(self::$_staticcache[$hash])) return self::$_staticcache[$hash]; 
	  return false; 
	}
	
	/**
	 * Get cached data by ID and group
	 *
	 * @param   string   $id         The cache data ID
	 * @param   string   $group      The cache data group
	 * @param   boolean  $checkTime  True to verify cache time expiration threshold
	 *
	 * @return  mixed  Boolean false on failure or a cached data object
	 *
	 * @since   11.1
	 */
	public function get($id, $group, $checkTime = true)
	{
		
		$test1 = $this->getStatic($id, $group); 
		
		if ($test1 !== false) return $test1; 
	    
		
		  $res = ''; 
		  
		  $ret = $this->_checkExpire($id, $group, $checkTime, $res); 
		  
		
		  
		  if ($ret === false) {
			  return false; 
		  }
		  else {
			   
			  
			$res = (string)$res; 
			if (empty($res)) return false; 
			
			return $res; 
		  }
		
		
	}

	/**
	 * Get all cached data
	 *
	 * @return  mixed  Boolean false on failure or a cached data object
	 *
	 * @since   11.1
	 */
	public function getAll()
	{
		
		$data    = array();
		
	
		$px =  self::$_db->getPrefix(); 		
		$q = "select sum(LENGTH(`data`)) as bytes, count(`hash`) as c from `".$px."cache` where expiry > -1"; 
		
		$res = $this->loadAssoc($q); 
		
		if (empty($res)) return array(); 
		
		$bytes = (int)$res['bytes']; 
		$count = (int)$res['c']; 
		
		if (empty($bytes)) $bytes = 0; 
		$am = $bytes / 1024; 
		
		$folder = 'sqlcache'; 
		
		$item  = new JCacheStorageHelper($folder);
		$item->size = $am; 
		$item->count = $count; 
		
		$data['sqlcache'] = $item; 
		
		

		return $data;
	}
	
	// must not change DB query status: 
	private function loadAssoc($query) {
		$db =& self::$_db; 
		$conn = $db->getConnection(); 
		$res = null; 
		
		
		
		if (method_exists($conn, 'query')) {
			
		if ($result = $conn->query($query)) {
		  $res = $result->fetch_assoc();
		  
		 
		}
		
		
		
		}
		else 
		{
			if (method_exists($conn, 'prepare')) {
				$sth = $conn->prepare($query); 
				$sth->execute();
				$res = $sth->fetch(PDO::FETCH_ASSOC);
			}
		}
		return $res; 
	}

		// must not change DB query status: 
	private function loadResult($query) {
		$db =& self::$_db; 
		$res = null; 
		$conn = $db->getConnection(); 
		if (method_exists($conn, 'query')) {
		if ($result = $conn->query($query)) {
		  $res = $result->fetch_assoc();
		  if (is_array($res)) return reset($res); 
		  
		  
		}
		}
		else {
			if (method_exists($conn, 'prepare')) {
				$sth = $conn->prepare($query); 
				$sth->execute();
				$res = $sth->fetch(PDO::FETCH_ASSOC);
				
				
				if (is_array($res)) return reset($res); 
			}
		}
	    return $res; 
	}
	private function query($query) {
	   $db =& self::$_db; 
	   $conn = $db->getConnection(); 
	   $res = null; 
	   if (method_exists($conn, 'query')) {
	   $res = $conn->real_query($query);
	   $e = $conn->error; 
	   if (!empty($e)) {
		  // die($e); 
		  //error_log($e); 
	   }
	   }
	   else {
		   if (method_exists($conn, 'exec')) {
		   $res = $conn->exec($query);
		   }
	   }
	   
	   
	   
	   return $res; 
	
	
	}

	
	/**
	 * Store the data to cache by ID and group
	 *
	 * @param   string  $id     The cache data ID
	 * @param   string  $group  The cache data group
	 * @param   string  $data   The data to store in cache
	 *
	 * @return  boolean
	 *
	 * @since   11.1
	 */
	public function store($id, $group, $data)
	{
		
		
		
		
		$wasDone = $this->setStatic($id, $group, $data); 
		
		if ($wasDone) {
			// die('storing the same thing twice !'); 
		}
		
		
		$hash    = $this->_getFilePath($id, $group);
		$db =& self::$_db; 
		$time = time(); 
		if (isset($this->_lifetime)) {
			$this->_lifetime = (int)$this->_lifetime; 
			$expiry = $time + ($this->_lifetime * 60); 
		}
		else {
		   // default 24 hour cache: 
		  $expiry = $time + 24*60*60; 
		}
		
		
		
		
		$group = hash('sha384', $group); 
		$px =  $db->getPrefix(); 	
		$q = "insert into `".$px."cache` (`hash`, `cachegroup`, `data`, `expiry`) values "; 
		
		$values = " ('".$db->escape($hash)."', '".$db->escape($group)."', '".$db->escape($data)."', ".(int)$expiry.") "; 
		$q .= $values; 
		
		$q .= " on duplicate key update `data` = '".$db->escape($data)."' and `expiry` = ".(int)$expiry." "; 
		
		if (!function_exists('fastcgi_finish_request')) {
		  $this->query($q); 
		}
		else {
			// note - you should use php-fpm to utilize the speed of after connecton storing of the cache: 
			
		  if (!defined('callAtShutDown_REGISTERED')) {
		   register_shutdown_function(array($this, 'callAtShutDown')); 
		   define('callAtShutDown_REGISTERED', 1); 
		  }
			
			
		  $this->toDo($values); 
		}
		
		
		
		return true; 
		
		
	}

	/**
	 * Remove a cached data entry by ID and group
	 *
	 * @param   string  $id     The cache data ID
	 * @param   string  $group  The cache data group
	 *
	 * @return  boolean
	 *
	 * @since   11.1
	 */
	public function remove($id, $group)
	{
		$hash = $this->_getFilePath($id, $group);
		
		
		$db =& self::$_db; 
		$px =  $db->getPrefix(); 	
		$q = "delete * from ".$px."cache where `hash` = '".$db->escape($hash)."' limit 0,1"; 
		
		$this->query($q); 

		return true;
	}

	/**
	 * Clean cache for a group given a mode.
	 *
	 * group mode    : cleans all cache in the group
	 * notgroup mode : cleans all cache not in the group
	 *
	 * @param   string  $group  The cache data group
	 * @param   string  $mode   The mode for cleaning cache [group|notgroup]
	 *
	 * @return  boolean
	 *
	 * @since   11.1
	 */
	public function clean($group, $mode = null)
	{
		$return = true;
		$folder = $group;

		if (trim($folder) == '')
		{
			$mode = 'notgroup';
		}
		$px =  self::$_db->getPrefix(); 	
		switch ($mode)
		{
			case 'notgroup' :
			 $q = 'delete from `'.$px.'cache` where `expiry` > -1'; 
			 $this->query($q); 
			 break;

			case 'group' :
			default :
				
				$group = hash('sha384', $group); 
				$q = 'delete from `'.$px.'cache` where `expiry` > -1'; 
			    $this->query($q); 
				

				break;
		}

		return (bool) $return;
	}

	/**
	 * Flush all existing items in storage.
	 *
	 * @return  boolean
	 *
	 * @since   3.6.3
	 */
	public function flush()
	{
		$this->clean(); 
		return true;
	}
	
	/**
	 * Test to see if the storage handler is available.
	 *
	 * @return  boolean
	 *
	 * @since   12.1
	 */
	public static function isSupported()
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
		

		$time = time(); 
		
		$px =  self::$_db->getPrefix(); 	
		$q = "delete from `".$px."cache` where `expiry` < ".$time; 
		$q->query($q); 
		return true;
	}

	

	/**
	 * Lock cached item
	 *
	 * @param   string   $id        The cache data ID
	 * @param   string   $group     The cache data group
	 * @param   integer  $locktime  Cached item max lock time
	 *
	 * @return  mixed  Boolean false if locking failed or an object containing properties lock and locklooped
	 *
	 * @since   11.1
	 */
	public function lock($id, $group, $locktime)
	{
		/*
		$lastData = new stdClass(); 
		$lastData->sql = self::$_db->sql; 
		$lastData->limit = self::$_db->limit; 
		$lastData->offset = self::$_db->limit; 
		self::$_staticcache = $lastData; 
		*/
		self::$_staticcache['lastid'] = $id; 
		
		$returning             = new stdClass;
		$returning->locked     = false;
		$returning->locklooped = true;
		return $returning; 
	}

	/**
	 * Unlock cached item
	 *
	 * @param   string  $id     The cache data ID
	 * @param   string  $group  The cache data group
	 *
	 * @return  boolean
	 *
	 * @since   11.1
	 */
	public function unlock($id, $group = null)
	{

		return true;
	}

	/**
	 * Check if a cache object has expired
	 *
	 * @param   string  $id     Cache ID to check
	 * @param   string  $group  The cache data group
	 *
	 * @return  boolean  True if the cache ID is valid
	 *
	 * @since   11.1
	 */
	protected function _checkExpire($id, $group, $checkTime, &$res)
	{
		$hash = $this->_getFilePath($id, $group);
		
		
		$qt = ''; 
		if ($checkTime) {
			$time = time(); 
			$qt = ' and `expiry` > '.(int)$time; 
		}
		$db =& self::$_db; 
		$px =  $db->getPrefix(); 	
		$q = "select `data` from `".$px."cache` where `hash` = '".$db->escape($hash)."' ".$qt." limit 0,1"; 
		
		try {
		 $res = $this->loadResult($q); 
		}
		catch (Exception $e) {
			return false; 
		}
		
		
		
	    if (empty($res)) return false; 
			
		
		return true; 
		
		
		
	}

	/**
	 * Get a cache ID string from an ID/group pair
	 *
	 * @param   string  $id     The cache data ID
	 * @param   string  $group  The cache data group
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	protected function _getCacheId($id, $group)
	{
		$name          = $this->_application . '-' . $id . '-' . $this->_language;
		$this->rawname = $this->_hash . '-' . $name;

		return JCache::getPlatformPrefix() . $this->_hash . '-cache-' . $group . '-' . $name;
	}
	
	/**
	 * Get a cache file path from an ID/group pair
	 *
	 * @param   string  $id     The cache data ID
	 * @param   string  $group  The cache data group
	 *
	 * @return  boolean|string  The path to the data object or boolean false if the cache directory does not exist
	 *
	 * @since   11.1
	 */
	protected function _getFilePath($id, $group)
	{
		
		$name = $this->_getCacheId($id, $group);
		if (isset(self::$_staticcache['hash_'.$name])) 
			return self::$_staticcache['hash_'.$name]; 
		
		$key = $group.'_'.$name; 
		if (strlen($key) <= 128) return $key; 
		else {
			$hash = hash('sha512', $key); 
			self::$_staticcache['hash_'.$name] = $hash; 
			return $hash; 
		}
		
	}

	/**
	 * Quickly delete a folder of files
	 *
	 * @param   string  $path  The path to the folder to delete.
	 *
	 * @return  boolean
	 *
	 * @since   11.1
	 */
	protected function _deleteFolder($path)
	{

		return true;
	}

	/**
	 * Function to strip additional / or \ in a path name
	 *
	 * @param   string  $path  The path to clean
	 * @param   string  $ds    Directory separator (optional)
	 *
	 * @return  string  The cleaned path
	 *
	 * @since   11.1
	 */
	protected function _cleanPath($path, $ds = DIRECTORY_SEPARATOR)
	{
		return $path;
	}

	/**
	 * Utility function to quickly read the files in a folder.
	 *
	 * @param   string   $path           The path of the folder to read.
	 * @param   string   $filter         A filter for file names.
	 * @param   mixed    $recurse        True to recursively search into sub-folders, or an integer to specify the maximum depth.
	 * @param   boolean  $fullpath       True to return the full path to the file.
	 * @param   array    $exclude        Array with names of files which should not be shown in the result.
	 * @param   array    $excludefilter  Array of folder names to exclude
	 *
	 * @return  array  Files in the given folder.
	 *
	 * @since   11.1
	 */
	protected function _filesInFolder($path, $filter = '.', $recurse = false, $fullpath = false,
		$exclude = array('.svn', 'CVS', '.DS_Store', '__MACOSX'), $excludefilter = array('^\..*', '.*~'))
	{
		return array(); 
	}

	/**
	 * Utility function to read the folders in a folder.
	 *
	 * @param   string   $path           The path of the folder to read.
	 * @param   string   $filter         A filter for folder names.
	 * @param   mixed    $recurse        True to recursively search into sub-folders, or an integer to specify the maximum depth.
	 * @param   boolean  $fullpath       True to return the full path to the folders.
	 * @param   array    $exclude        Array with names of folders which should not be shown in the result.
	 * @param   array    $excludefilter  Array with regular expressions matching folders which should not be shown in the result.
	 *
	 * @return  array  Folders in the given folder.
	 *
	 * @since   11.1
	 */
	protected function _folders($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('.svn', 'CVS', '.DS_Store', '__MACOSX'),
		$excludefilter = array('^\..*'))
	{
		
		return array();
	}
	
	//stan -> this is the async part to store the cache AFTER THE CONNECTION WAS CLOSED: 
	function callAtShutDown()
	{
	   $a3 = true; 
	   $session = JFactory::getSession(); 
	   $session->close(); 
	   
	   if (function_exists('fastcgi_finish_request')) {
		   fastcgi_finish_request(); 
	   }
	   $px =  self::$_db->getPrefix(); 		
	   $q = "INSERT INTO `".$px."cache` (`hash`, `cachegroup`, `data`, `expiry`) VALUES "; 
	   $bytes = $this->getMaxPacket(); 
	   $bytes = $bytes * 0.9; 
	   if (!empty(self::$_todo)) { 
	     
	      $insertV = implode(',', self::$_todo); 
	   }
	   else {
		   return; 
	   }
	   $q .= $insertV; 
	   $q .= " ON DUPLICATE KEY UPDATE "; 
	   $q .= " `data` = VALUES(data) "; 
	   $this->query($q); 
	   
	  // error_log($q); 
	   
	   
	}
	
	function getMaxPacket() {
		$q = "SHOW VARIABLES LIKE 'max_allowed_packet';"; 

$bytesR = $this->loadAssoc($q); 

if (isset($bytesR['Value'])) $bytes = $bytesR['Value']; 
if (empty($bytes)) $bytes = 1024*1024*15; 
	
	return $bytes; 
	}
	
	function toDo($q) {
		if (empty(self::$_todo)) self::$_todo = array(); 
		// do not allow duplicities: 
		self::$_todo[$q] = $q; 
	}
	
	
}
