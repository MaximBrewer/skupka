<?php

// No direct access
defined('_JEXEC') or die;

class plgVm2smsRobokassa extends JPlugin
{


	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	public function onSendSMS($phone,$text,$worktime,$sender)
	{
		if(preg_match('/^8(\d{10})$/', $phone,$m)){
			$phone = '7'.$m[1];
		}
		if(preg_match('/^\+7(\d{10})$/', $phone,$m)){
			$phone = '7'.$m[1];
		}
		$text = mb_substr($text, 0,128);
		$data = array(
			'login'=>$this->params->get('login'),
			'message'=>$text,
			'phone'=>$phone
		);

		$data['signature'] = md5($data['login'].':'.$data['phone'].':'.$data['message'].':'.$this->params->get('password'));
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
		$result = $http->get('https://services.robokassa.ru/SMS/',$data);
		$ans = json_decode($result->body);
		if ($ans->errorCode!='0'){
			JFactory::getApplication()->enqueueMessage('SMS Error:'.$ans->errorMessage,'error');
		}
		return true;
	}
}

