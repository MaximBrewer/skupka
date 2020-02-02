<?php

// No direct access
defined('_JEXEC') or die;

class plgVm2smsSmsc extends JPlugin
{


	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	public function onSendSMS($phone,$text,$worktime,$sender)
	{
		$data = array(
			'login'=>$this->params->get('login'),
			'psw'=>md5(trim($this->params->get('pass'))),
			'mes'=>$text,
			'phones'=>$phone,
			'charset'=>'utf-8',
			'fmt'=>'3',
			'pp'=>311551
		);
		if ($sender){
			$data['sender']=$sender;
		}
		if ($worktime) {
			$cparam= &JComponentHelper::getParams( 'com_vm2sms' );
			$now = floatval(JHtml::_("date",'now',"H.i"));
			$start = floatval(str_replace(':','.',$cparam->get('work_start')));
			$end = floatval(str_replace(':','.',$cparam->get('work_end')));
			if ($now<$start||$now>$end){
				jimport( 'joomla.utilities.date' );
				if ($now>$end){
					$time = new JDate('tomorrow');
				} else {
					$time = new JDate('now');
				}
				$data['time'] = JHtml::_('date',$time->toUnix(),"d.m.y ".$cparam->get('work_start'));
			}
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
		$result = $http->post('http://smsc.ru/sys/send.php',$data);
		$ans = json_decode($result->body);
		if (isset($ans->error)){
			JFactory::getApplication()->enqueueMessage('SMS Error:'.$ans->error,'error');
		}
		return true;
	}
}

