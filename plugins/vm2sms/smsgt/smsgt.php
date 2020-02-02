<?php
/**
 * @copyright	Copyright (C) 2012 vampirus.ru. All rights reserved.
 */
// No direct access
defined('_JEXEC') or die;

class plgVm2smsSmsgt extends JPlugin
{


	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	public function onSendSMS($phone,$text,$worktime,$sender)
	{
		$phone = str_replace("+", "", $phone);
		$data = array(
			'login'=>$this->params->get('login'),
			'msg'=>$text,
			'to'=>$phone,
			'code'=>$this->params->get('secret'),
		);
		if ($sender){
			$data['from']=$sender;
		}
		jimport( 'joomla.client.http' );
		$opt = new JRegistry;
		if (function_exists('curl_version') && curl_version()){
			$trans = new JHttpTransportCurl($opt);
		} elseif (function_exists('fopen') && is_callable('fopen') && ini_get('allow_url_fopen')){
			$trans = new JHttpTransportStream($opt);
		} elseif(function_exists('fsockopen') && is_callable('fsockopen')){
			$trans = new JHttpTransportSocket($opt);
		} else {
			JError::raiseError(500, "Can't initialise http transport ");
		}
		$http = new JHttp($opt,$trans);
		$query = "";
		foreach($data as $key=>$value){
			$query .= "$key=$value&";
		}
		$url = 'http://78.46.32.24:1200/?'.$query;
		$result = $http->get($url);
		$ans = json_decode($result->body);
		if ($ans->status=="error"){
			JFactory::getApplication()->enqueueMessage('SMS Error:'.$ans->meta,'error');
		}
		return true;
	}
}

