<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.10.7
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><?php

class SendController extends acymailingController{

	function sendready(){
		if(!$this->isAllowed('newsletters', 'send')) return;
		acymailing_setVar('layout', 'sendconfirm');
		return parent::display();
	}

	function send(){
		if(!$this->isAllowed('newsletters', 'send')) return;
		acymailing_checkToken();

		acymailing_setNoTemplate();
		$mailid = acymailing_getCID('mailid');
		if(empty($mailid)) exit;

		$time = time();
		$queueClass = acymailing_get('class.queue');
		$queueClass->onlynew = acymailing_getVar('int', 'onlynew');
		$queueClass->mindelay = acymailing_getVar('int', 'mindelay');
		$totalSub = $queueClass->queue($mailid, $time);

		if(empty($totalSub)){
			acymailing_display(acymailing_translation('NO_RECEIVER'), 'warning');
			return;
		}

		$mailObject = new stdClass();
		$mailObject->senddate = $time;
		$mailObject->published = 1;
		$mailObject->mailid = $mailid;
		$mailObject->sentby = acymailing_currentUserId();
		acymailing_updateObject(acymailing_table('mail'), $mailObject, 'mailid');

		$config = acymailing_config();
		$queueType = $config->get('queue_type');
		if($queueType == 'onlyauto'){
			$messages = array();
			$messages[] = acymailing_translation_sprintf('ADDED_QUEUE', $totalSub);
			$messages[] = acymailing_translation('AUTOSEND_CONFIRMATION');
			acymailing_display($messages, 'success');
			return;
		}else{
			acymailing_setVar('totalsend', $totalSub);
			acymailing_redirect(acymailing_completeLink('send&task=continuesend&mailid='.$mailid.'&totalsend='.$totalSub, true, true));
			exit;
		}
	}

	function continuesend(){
		$config = acymailing_config();

		if(acymailing_level(1) && $config->get('queue_type') == 'onlyauto'){
			acymailing_setNoTemplate();
			acymailing_display(acymailing_translation('ACY_ONLYAUTOPROCESS'), 'warning');
			return;
		}


		$newcrontime = time() + 120;
		if($config->get('cron_next') < $newcrontime){
			$newValue = new stdClass();
			$newValue->cron_next = $newcrontime;
			$config->save($newValue);
		}

		$mailid = acymailing_getCID('mailid');

		$totalSend = acymailing_getVar('int', 'totalsend', 0, '');
		$alreadySent = acymailing_getVar('int', 'alreadysent', 0, '');

		$helperQueue = acymailing_get('helper.queue');
		$helperQueue->mailid = $mailid;
		$helperQueue->report = true;
		$helperQueue->total = $totalSend;
		$helperQueue->start = $alreadySent;
		$helperQueue->pause = $config->get('queue_pause');
		$helperQueue->process();

		acymailing_setNoTemplate();



	}

	function scheduleready(){
		if(!$this->isAllowed('newsletters', 'schedule')) return;
		$mailid = acymailing_getCID('mailid');

		if(empty($mailid)) return false;
		$queueClass = acymailing_get('class.queue');
		$values = new stdClass();
		$values->nbqueue = $queueClass->nbQueue($mailid);

		if(!empty($values->nbqueue)){
			$messages = array();
			$messages[] = acymailing_translation_sprintf('ALREADY_QUEUED', $values->nbqueue);
			$messages[] = acymailing_translation_sprintf('DELETE_QUEUE');
			acymailing_display($messages, 'warning');
			return;
		}
		acymailing_setVar('layout', 'scheduleconfirm');
		return parent::display();
	}

	function genschedule(){
		acymailing_checkToken();

		$schedHelper = acymailing_get('helper.schedule');
		$result = $schedHelper->queueScheduled();

		acymailing_display($schedHelper->messages, $result ? 'success' : 'warning');

		return true;
	}

	function schedule(){
		if(!$this->isAllowed('newsletters', 'schedule')) return;
		acymailing_checkToken();
		$mailid = acymailing_getCID('mailid');

		acymailing_setNoTemplate();

		if(empty($mailid)) die('Missing mail ID');

		$senddate = acymailing_getVar('string', 'senddate', '');
		$sendhours = acymailing_getVar('string', 'sendhours', '');
		$sendminutes = acymailing_getVar('string', 'sendminutes', '');
		$senddateComplete = $senddate.' '.$sendhours.':'.$sendminutes;

		if(empty($senddate)){
			acymailing_display(acymailing_translation('SPECIFY_DATE'), 'warning');
			return $this->scheduleready();
		}

		$realSendDate = acymailing_getTime($senddateComplete);
		if($realSendDate < time()){
			acymailing_display(acymailing_translation('DATE_FUTURE'), 'warning');
			return $this->scheduleready();
		}


		$mailClass = acymailing_get('class.mail');
		$myNewsletter = $mailClass->get($mailid);

		$mail = new stdClass();
		$mail->mailid = $mailid;
		$mail->senddate = $realSendDate;
		$mail->sentby = acymailing_currentUserId();
		$mail->published = 2;
		$myNewsletter->params['onlynew'] = acymailing_getVar('int', 'onlynew', 0);
		$mail->params = $myNewsletter->params;

		$mailClass->save($mail);

		acymailing_display(acymailing_translation_sprintf('AUTOSEND_DATE', '<b><i>'.$myNewsletter->subject.'</i></b>', acymailing_getDate($realSendDate)), 'success');

		if(!ACYMAILING_J30){
			$js = "window.top.document.getElementById('a_schedule').innerHTML = '<a class=\"toolbar\" onclick=\"acymailing.submitbutton(\'unschedule\')\" href=\"#\"><span class=\"acyicon-schedule\" title=\"".acymailing_translation('UNSCHEDULE', true)."\"> </span>".acymailing_translation('UNSCHEDULE')."</a>';";
		}else{
			$js = "window.top.document.getElementById('toolbar-schedule').innerHTML = '<div id=\"toolbar-unschedule\"><button onclick=\"acymailing.submitbutton(\'unschedule\');\" href=\"#\"><i class=\"acyicon-schedule \"> </i>Unschedule</button></div>';";
		}
		acymailing_addScript(true, $js);
	}

	function addqueue(){
		if(!$this->isAllowed('newsletters', 'schedule')) return;
		acymailing_setVar('layout', 'addqueue');
		return parent::display();
	}

	function scheduleone(){
		if(!$this->isAllowed('newsletters', 'schedule')) return;
		acymailing_checkToken();

		$mailid = acymailing_getVar('int', 'mailid');
		$subid = acymailing_getVar('int', 'subid');
		$senddate = acymailing_getVar('string', 'senddate', '');
		$sendhours = acymailing_getVar('string', 'sendhours', '');
		$sendminutes = acymailing_getVar('string', 'sendminutes', '');
		$senddateComplete = $senddate.' '.$sendhours.':'.$sendminutes;

		if(empty($mailid) || empty($subid)) die('Missing mail ID or user ID');

		$realSendDate = acymailing_getTime($senddateComplete);
		if($realSendDate < time()){
			acymailing_display(acymailing_translation('DATE_FUTURE'), 'warning');
			if(acymailing_isAdmin()){
				return $this->addqueue();
			}else{
				$frontSubController = acymailing_get('controller.frontsubscriber');
				return $frontSubController->addqueue();
			}
		}

		$mailClass = acymailing_get('class.mail');
		$myNewsletter = $mailClass->get($mailid);

		$status = acymailing_query('INSERT IGNORE INTO `#__acymailing_queue` (`mailid`,`subid`,`senddate`,`priority`) VALUES ('.intval($myNewsletter->mailid).','.intval($subid).','.intval($realSendDate).',1)');

		if($status !== false){
			acymailing_display(acymailing_translation_sprintf('AUTOSEND_DATE', '<b><i>'.$myNewsletter->subject.'</i></b>', acymailing_getDate($realSendDate)), 'success');
		}else{
			acymailing_display(array(acymailing_translation('ERROR_SAVING'), acymailing_getDBError()), 'error');
			if(acymailing_isAdmin()){
				return $this->addqueue();
			}else{
				$frontSubController = acymailing_get('controller.frontsubscriber');
				return $frontSubController->addqueue();
			}
		}
	}


	function spamtest(){
		$mailid = acymailing_getVar('int', 'mailid');
		if(empty($mailid)) return;

		$config = acymailing_config();
		ob_start();
		$urlSite = trim(base64_encode(preg_replace('#https?://(www\.)?#i', '', ACYMAILING_LIVE)), '=/');
		$url = ACYMAILING_SPAMURL.'spamTestSystem&component=acymailing&level='.strtolower($config->get('level', 'starter')).'&urlsite='.$urlSite;
		$spamtestSystem = acymailing_fileGetContent($url, 30);

		$warnings = ob_get_clean();

		if(empty($spamtestSystem) || $spamtestSystem === false || !empty($warnings)){
			acymailing_display('Could not load your information from our server'.((!empty($warnings) && acymailing_isDebug()) ? $warnings : ''), 'error');
			return;
		}
		$decodedInformation = json_decode($spamtestSystem, true);
		if(!empty($decodedInformation['messages']) || !empty($decodedInformation['error'])){
			$msgError = (!empty($decodedInformation['messages'])) ? $decodedInformation['messages'].'<br />' : '';
			$msgError .= (!empty($decodedInformation['error'])) ? $decodedInformation['error'] : '';
			acymailing_display($msgError, 'error');
			return;
		}
		if(empty($decodedInformation['email'])){
			acymailing_display('Missing test mail address', 'error');
			return;
		}

		$receiver = new stdClass();
		$receiver->subid = 0;
		$receiver->email = $decodedInformation['email'];
		$receiver->name = $decodedInformation['name'];
		$receiver->html = 1;
		$receiver->confirmed = 1;
		$receiver->enabled = 1;

		$mailerHelper = acymailing_get('helper.mailer');
		$mailerHelper->checkConfirmField = false;
		$mailerHelper->checkEnabled = false;
		$mailerHelper->checkPublished = false;
		$mailerHelper->checkAccept = false;
		$mailerHelper->loadedToSend = true;
		$mailerHelper->report = false;

		if(!$mailerHelper->sendOne($mailid, $receiver)){
			acymailing_display($mailerHelper->reportMessage, 'error');
			return;
		}
		
		acymailing_redirect($decodedInformation['displayURL']);
		return;
	}
}
