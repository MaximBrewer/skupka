<?php

// No direct access
defined('_JEXEC') or die;

class plgVm2smsSmsru extends JPlugin
{


	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	public function onSendSMS($phone,$text,$worktime,$sender)
	{
		$data = array(
			'login'=>trim($this->params->get('login')),
			'text'=>$text,
			'to'=>$phone,
			'partner_id'=>12962
		);
		if ($sender){
			$data['from']=$sender;
		}
		if ($worktime) {
			$config = JFactory::getConfig();
			$cparam= &JComponentHelper::getParams( 'com_vm2sms' );
			$now = floatval(JHtml::_("date",'now',"H.i"));
			$start = floatval(str_replace(':','.',$cparam->get('work_start')));
			$end = floatval(str_replace(':','.',$cparam->get('work_end')));
			if ($now<$start||$now>$end){
				jimport( 'joomla.utilities.date' );
			// Set the correct time zone based on the user configuration.

				if ($now>$end){
					$time = new JDate('tomorrow '.$cparam->get('work_start'),new DateTimeZone($config->get('offset')));
				} else {
					$time = new JDate('today '.$cparam->get('work_start'),new DateTimeZone($config->get('offset')));
				}
				$data['time'] = $time->toUnix();
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
		$result = $http->get('http://sms.ru/auth/get_token');
		$token = $result->body;
		$data['token'] = $token;
		$data["sha512"]	=	hash("sha512",trim($this->params->get('pass')).$token);
		$result = $http->post('http://sms.ru/sms/send',$data);
		$ans = substr($result->body,0,3);
		if ($ans=='100') return true;
		$error = '';
		switch ($ans){
			case '200':$error = 'Неправильный api_id';break;
			case '201':$error = 'Не хватает средств на лицевом счету';break;
			case '202':$error = 'Неправильно указан получатель';break;
			case '203':$error = 'Нет текста сообщения';break;
			case '204':$error = 'Имя отправителя не согласовано с администрацией';break;
			case '205':$error = 'Сообщение слишком длинное (превышает 8 СМС)';break;
			case '206':$error = 'Будет превышен или уже превышен дневной лимит на отправку сообщений';break;
			case '207':$error = 'На этот номер (или один из номеров) нельзя отправлять сообщения, либо указано более 100 номеров в списке получателей';break;
			case '208':$error = 'Параметр time указан неправильно';break;
			case '209':$error = 'Вы добавили этот номер (или один из номеров) в стоп-лист';break;
			case '210':$error = 'Используется GET, где необходимо использовать POST';break;
			case '211':$error = 'Метод не найден';break;
			case '220':$error = 'Сервис временно недоступен, попробуйте чуть позже.';break;
			case '300':$error = 'Неправильный token (возможно истек срок действия, либо ваш IP изменился)';break;
			case '301':$error = 'Неправильный пароль, либо пользователь не найден';break;
			case '302':$error = 'Пользователь авторизован, но аккаунт не подтвержден (пользователь не ввел код, присланный в регистрационной смс)';break;
		}
		JFactory::getApplication()->enqueueMessage('SMS Error:'.$error,'error');
		return false;
	}
}

