<?php
/*------------------------------------------------------------------------
# vData -
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');

class VDataFtpHelper{
	
	private $connection;
	private $loginStatus = false;
	private $message;
	// private $isPassive = true;
	private $formats = array('csv', 'xml', 'json');
	
	public function __construct(){
		
		if(!function_exists('ftp_connect')){
			throw new Exception(JText::_('PHP_FTP_EXTENSION_NOT_FOUND'));
		}
		
	}
	
	private function setMessage($message){
		$this->message = $message;
	}
	
	public function getMessage(){
		return $this->message;
	}
	
	public function getConnection(){
		return $this->connection;
	}
	
	public function connect($host, $port=21, $user, $pass ,$isPassive=true){
		try{
		//get connection ID
		$this->connection = ftp_connect($host, $port);
		//login
		
		$this->loginStatus = ftp_login($this->connection, $user, $pass);
		//check passive mode
		$mode = ftp_pasv($this->connection, $isPassive);
		
		if(!$this->connection || !$this->loginStatus){
			$this->setMessage(JText::_('FAILED_TO_MAKE_FTP_CONNECTION'));
			return false;
		}
		
		
		}
		catch(Exception $e){
			throw new Exception($e->getMessage());
		}
		return true;
		
	}
	
	public function getList($directory="."){
		/* if(!ftp_chdir($this->connection, $directory)){
			$this->setMessage(JText::_('FAILED_TO_CHANGE_DIRECTORY'));
			return false;
		} */
		return ftp_nlist($this->connection, $directory);
	}
	
	public function upload($serverPath, $localPath,$mode=FTP_ASCII){
		
		$filename = basename($localPath);
		$ext = strtolower(end(explode('.',$filename)));
		if(!in_array($ext, $this->formats)){
			$this->setMessage(JText::_('FAILED_TO_UPLOAD_INVALID_FILE_EXTENSION'));
			return false;
		}
		$serverPath = empty($serverPath)?$filename:$serverPath;
		if(!isset(pathinfo($serverPath)['extension'])){
			$this->setMessage(JText::_('INCORRECT_SERVER_PATH'));
			return false;
		}
		$uploadStatus = ftp_put($this->connection, $serverPath, $localPath, $mode);
		if(!$uploadStatus){
			$this->setMessage(JText::_('FAILED_TO_UPLOAD_FILE'));
			return false;
		}
		return true;
	}
	
	public function uploadContent($handle, $serverPath,$mode=FTP_ASCII){
		if(!isset(pathinfo($serverPath)['extension'])){
			$this->setMessage(JText::_('INCORRECT_SERVER_PATH'));
			return false;
		}
			
		if (!ftp_fput($this->connection, $serverPath, $handle, $mode)) {
			return false;
		}
		return true;
	}
	
	public function download($localPath, $serverPath, $formats='',$mode=FTP_ASCII){
	
		if($formats){
			$this->formats=$formats;
		}
		$filename = basename($localPath);
		$ext_arr = explode('.',$filename);
		$ext = strtolower(end($ext_arr));
		if(!in_array($ext, $this->formats)){
			$this->setMessage(JText::_('FAILED_TO_UPLOAD_INVALID_FILE_EXTENSION'));
			return false;
		}
		
		$downStatus = ftp_get($this->connection, $localPath, $serverPath, $mode);
		if(!$downStatus){
			$this->setMessage(JText::_('FAILED_TO_UPLOAD_FILE'));
			return false;
		}
		
		return true;
	}
	
	
	public function __deconstruct(){
		if ($this->connection) {
			ftp_close($this->connection);
		}
	}
	
}