<?php
/**
 * @copyright	Copyright (C) 2012 vampirus.ru. All rights reserved.
 */
// No direct access
defined('_JEXEC') or die;

class plgVm2smsLittlesms extends JPlugin
{


	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	public function onSendSMS($phone,$text,$worktime,$sender)
	{
		$data = array(
			'user'=>$this->params->get('user'),
			'apikey'=>$this->params->get('apikey'),
			'message'=>$text,
			'recipients'=>$phone
		);
		if ($sender){
			$data['sender']=$sender;
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
		if($this->params->get('https')){
			$result = $http->post('https://littlesms.ru/api/message/send',$data);
		} else {
			$result = $http->post('http://littlesms.ru/api/message/send',$data);
		}
		$ans = json_decode($result->body);
		if ($ans->status=='error'){
			JFactory::getApplication()->enqueueMessage('SMS Error:'.$ans->message,'error');
		}
		return true;
	}
}

