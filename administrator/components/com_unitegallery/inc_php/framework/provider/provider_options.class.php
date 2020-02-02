<?php
/**
 * @package Unite Gallery
 * @author UniteCMS.net / Valiano
 * @copyright (C) 2012 Unite CMS, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('_JEXEC') or die('Restricted access');

class ProviderOptionsUG extends UniteElementsBaseUG{
	
	private static $optionsCache = null;
	private static $tableOptions;
	
	
	/**
	 * init the class
	 */
	public function __construct(){
		
		parent::__construct();
		
		if(empty(self::$tableOptions)){
			self::$tableOptions = UniteProviderFunctionsUG::$tablePrefix."unitegallery_options";
		}
		
	}
	
	
	/**
	 * get options from cache if empty
	 */
	private function checkOptionsCache(){
		if(self::$optionsCache !== null)
			return(false);
		
		$arrOptions = $this->db->fetch(self::$tableOptions);
		
		$arrAssoc = array();
		foreach($arrOptions as $option){
			$name = $option["name"];
			$value = $option["val"];
			$arrAssoc[$name] = $value;
		}
		
		self::$optionsCache = $arrAssoc;
				
	}
	
	
	/**
	 * set option with expiration
	 */
	function setTransient( $transient, $value, $expiration = 0 ) {
		
		$transient_timeout = '_transient_timeout_' . $transient;
		$transient_option = '_transient_' . $transient;
		
		//add or remove timeout
		if($expiration == 0)
			$this->deleteOption($transient_timeout);
		else{
			$timeoutTime = time() + $expiration;
			$this->updateOption($transient_timeout, $timeoutTime);
		}
		
		//add the actual option
		$this->updateOption($transient_option, $value);
		
		return(true);
	}
	
	
	/**
	 * get transient
	 */
	function getTransient($transient){
		
		$transient_timeout = '_transient_timeout_' . $transient;
		$transient_option = '_transient_' . $transient;
		
		if($this->isOptionExists($transient_option) == false)
			return(false);
		
		$value = $this->getOption($transient_option);
		
		//----- check if allowed to return
		
		//if no expiration - return
		if($this->isOptionExists($transient_timeout) == false)
			return($value);
		
		$expirationTime = $this->getOption($transient_timeout);
		if(!is_numeric($expirationTime))
			return(false);
		
		if($expirationTime > time())
			return($value);
		
		return(false);
	}
	
	
	/**
	 * get if the option exists
	 */
	public function isOptionExists($option){
		$this->checkOptionsCache();
		
		$isExists = array_key_exists($option, self::$optionsCache);
		
		return $isExists;
	}
	
	
	/**
	 * get option
	 */
	public function getOption($option, $default = false){
		
		$this->checkOptionsCache();
		
		$value = $default;
		if(array_key_exists($option, self::$optionsCache))
			$value = self::$optionsCache[$option];
		
		$value = HelperProviderUG::maybe_unserialize($value);
		
		return($value);
	}
	
	
	/**
	 * update option
	 */
	public function updateOption($option, $value){
		$this->checkOptionsCache();
		
		$isUpdate = false;
		if(array_key_exists($option, self::$optionsCache))
			$isUpdate = true;
		
		if(is_array($value) || is_object($value))
			$value = serialize($value);

		$arrData = array();
		$arrData["name"] = $option;
		$arrData["val"] = $value;
		
		//dmp($arrData);
		//exit();
		
		if($isUpdate == true)
			$this->db->update(self::$tableOptions, $arrData, array("name"=>$option));
		else
			$this->db->insert(self::$tableOptions, $arrData);
		
		self::$optionsCache[$option] = $value;
		
		return(true);
	}
	
	
	/**
	 * add option
	 */
	public function addOption($option, $value=''){
		
		if($this->isOptionExists($option))
			return(false);
		
		$response = $this->updateOption($option, $value);
		
		return($response);
	}
	
	
	/**
	 * delete some option
	 */
	public function deleteOption($option){
		
		$this->checkOptionsCache();
		
		if($this->isOptionExists($option) == false)
			return(false);
		
		$this->db->delete(self::$tableOptions, array("name"=>$option));
		
		unset(self::$optionsCache[$option]);
		
		return(true);
	}
	
}