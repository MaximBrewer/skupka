<?php

// No direct access
defined('_JEXEC') or die;

class plgVm2smsBytehand extends JPlugin
{


	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	public function onSendSMS($phone,$text,$worktime,$sender)
	{
		$data = array(
			'id'=>$this->params->get('login'),
			'key'=>$this->params->get('pass'),
			'text'=>$text,
			'to'=>$phone,

		);
		if ($sender){
			$data['from']=$sender;
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
				$data['send_after'] = JHtml::_('date',$time->toUnix(),"d.m.y ".$cparam->get('work_start'));
			}
		}

		$ch = curl_init('http://bytehand.com:3800/send?'.http_build_query($data));
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		$result = curl_exec($ch);
		curl_close($ch);
		$ans = json_decode($result);
		if (isset($ans->status) && $ans->status!=0){
			JFactory::getApplication()->enqueueMessage('SMS Error:'.$ans->description,'error');
		}
		return true;
	}
}

