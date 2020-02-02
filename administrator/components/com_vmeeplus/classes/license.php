<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

class emp_license{

	const SUCCESS = 0;
	const ERROR_NO_FILE = 1;
	const ERROR_NO_DOWNLOADID = 2;
	const ERROR_NO_SIGNATURE = 3;
	const ERROR_INVALID_SIGNATURE = 4;
	const ERROR_LICENSE_INVALID = 5;
	const ERROR_LICENSE_EXPIRED = 6;
	const ERROR_DOMAIN_NOT_MATCH = 7;
	const ERROR_CONNECTION_FAILED = 8;
	const ERROR_LICENSE_INVALID_REQUEST = 9;

	public static function checkLicense(){
		return self::SUCCESS;
	}

	public static function getLicenseFromServer(){
		return self::SUCCESS;
	}

	private static function doReq($request){
		return true;
	}

	private static function fixBaseUri(&$baseUri){
		$matches = array();
		preg_match('/(https?:\/\/.*?\/).*/',$baseUri,$matches);
		$baseUri = str_ireplace('www.', '', $matches[1]);
		$baseUri = str_ireplace('ww.', '', $baseUri);
		$baseUri = str_ireplace('https://', 'http://', $baseUri);
	}
}