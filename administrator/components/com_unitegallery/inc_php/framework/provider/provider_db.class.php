<?php
/**
 * @package Unite Gallery
 * @author UniteCMS.net / Valiano
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('_JEXEC') or die('Restricted access');

class UniteProviderDBUG{
	
	private $jdb;
	
	/**
	 *
	 * constructor - set database object
	 */
	public function __construct(){
		$this->jdb = JFactory::getDBO();
	}
	
	/**
	 * get error number
	 */
	public function getErrorNum(){
		return $this->jdb->getErrorNum();
	}
	
	
	/**
	 * get error message
	 */
	public function getErrorMsg(){
		return $this->jdb->getErrorMsg();
	}
	
	/**
	 * get last row insert id
	 */
	public function insertid(){
		return $this->jdb->insertid();
	}
	
	/**
	 * do sql query, return success
	 */
	public function query($query){
		$this->jdb->setQuery($query);
		$success = $this->jdb->query();
		return($success);
	}
	
	
	/**
	 * get affected rows after operation
	 */
	public function getAffectedRows(){
		return $this->jdb->getAffectedRows();
	}
	
	/**
	 * fetch objects from some sql
	 */
	public function fetchSql($query){
		$this->jdb->setQuery($query);
		$rows = $this->jdb->loadObjectList();
		
		return($rows);
	}
	
	/**
	 * escape some string
	 */
	public function escape($string){
		return $this->jdb->escape($string);
	}
	
}



?>