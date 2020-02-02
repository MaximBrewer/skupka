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

class ArchiveController extends acymailingController{

	function view(){

		$statsClass = acymailing_get('class.stats');
		$statsClass->countReturn = false;
		$statsClass->saveStats();

		$printEnabled = acymailing_getVar('none', 'print', 0);
		if($printEnabled){
			$js = "setTimeout(function(){
					if(document.getElementById('iframepreview')){
						document.getElementById('iframepreview').contentWindow.focus();
						document.getElementById('iframepreview').contentWindow.print();
					}else{
						window.print();
					}
				},2000);";
			acymailing_addScript(true, $js);
		}

		acymailing_setVar('layout', 'view');
		return parent::display();
	}


	function sendautonews(){
		$mailid = acymailing_getVar('int', 'mailid');
		$security = acymailing_getVar('cmd', 'key');

		list($key, $senddate) = explode('-', $security);

		if(empty($mailid)){
			acymailing_display('mailid not found', 'error');
			return;
		}

		if(empty($key)){
			acymailing_display('key not found', 'error');
			return;
		}

		if(empty($senddate)){
			acymailing_display('send date not found', 'error');
			return;
		}

		$mailClass = acymailing_get('class.mail');
		$newsletter = $mailClass->get($mailid);

		if(empty($newsletter->mailid)){
			acymailing_display('newsletter not found', 'error');
			return;
		}

		if($newsletter->senddate != $senddate){
			acymailing_display('Wrong send date', 'error');
			return;
		}

		if($newsletter->key != $key){
			acymailing_display('Wrong security key', 'error');
			return;
		}

		if($newsletter->published != 0){
			acymailing_display(acymailing_translation('AUTONEWS_GENERATE_DONE'), 'warning');
			return;
		}

		acymailing_query('UPDATE '.acymailing_table('mail').' SET published = 1 WHERE mailid = '.intval($newsletter->mailid));

		$queueClass = acymailing_get('class.queue');
		$totalSub = $queueClass->queue($mailid, time());

		acymailing_display(array(acymailing_translation_sprintf('ADDED_QUEUE', $totalSub), acymailing_translation('AUTOSEND_CONFIRMATION')), 'success');
	}

	function forward(){
		$config = acymailing_config();
		if(!$config->get('forward', true)) return $this->view();

		$key = acymailing_getVar('cmd', 'key');
		$mailid = acymailing_getVar('int', 'mailid');

		$mailerHelper = acymailing_get('helper.mailer');
		$mailerHelper->loadedToSend = false;
		$mailtosend = $mailerHelper->load($mailid);

		if(empty($key) OR $mailtosend->key !== $key){
			return $this->view();
		}

		acymailing_setVar('layout', 'forward');
		return parent::display();
	}

	function doforward(){
		$config = acymailing_config();
		if(!$config->get('forward', true)) return $this->view();
		acymailing_checkToken();
		acymailing_checkRobots();

		$history = acymailing_get('class.acyhistory');
		$forwardusers = acymailing_getVar('array', 'forwardusers', array());

		$forwardusers = array_slice($forwardusers, 0, 5, true);

		$sendername = acymailing_getVar('string', 'sendername');
		$senderemail = strip_tags(acymailing_getVar('string', 'senderemail'));
		$forwardmsg = nl2br(strip_tags(acymailing_getVar('string', 'forwardmsg')));
		$forwardmsg = preg_replace('#(http(s)?://|www.)[^ <]*(?= |<|$)#Uis', '', $forwardmsg);
		
		if(empty($sendername) || empty($senderemail)){
			echo "<script>alert('".acymailing_translation('FILL_ALL', true)."'); window.history.go(-1);</script>";
			exit;
		}

		$userClass = acymailing_get('helper.user');
		foreach($forwardusers as $oneUser => $infos){
			if(empty($infos['email'])) continue;

			if(empty($infos['name'])){
				echo "<script>alert('".acymailing_translation('FILL_ALL', true)."'); window.history.go(-1);</script>";
				exit;
			}
			if(!$userClass->validEmail($infos['email'], true)){
				echo "<script>alert('".acymailing_translation('VALID_EMAIL', true)."'); window.history.go(-1);</script>";
				exit;
			}
		}

		$config = acymailing_config();
		if($config->get('forward', 0) == 2){
			$captchaClass = acymailing_get('class.acycaptcha');
			if($config->get('captcha_enabled', 0) == 0) $captchaClass->pluginName = 'acycaptcha';
			$captchaClass->state = 'acycaptchacomponent';
			if(!$captchaClass->check(acymailing_getVar('string', 'acycaptcha'))){
				$captchaClass->returnError();
			}
		}

		$mailid = acymailing_getVar('int', 'mailid');
		if(empty($mailid)) return $this->view();

		$mailerHelper = acymailing_get('helper.mailer');
		$mailerHelper->checkConfirmField = false;
		$mailerHelper->checkEnabled = false;
		$mailerHelper->checkAccept = false;
		$mailerHelper->loadedToSend = true;

		$mailToForward = $mailerHelper->load($mailid);

		$key = acymailing_getVar('cmd', 'key');

		if(empty($key) OR $mailToForward->key !== $key){
			return $this->view();
		}

		$subidAndKey = acymailing_getVar('cmd', 'subid');
		$subid = intval($subidAndKey);
		$subkey = substr($subidAndKey, strpos($subidAndKey, '-')+1);

		if(empty($subid) || empty($subkey) || strpos($subidAndKey, '-') === false){
			echo "<script>alert('Couldn\'t find the user'); window.history.go(-1);</script>";
			exit;
		}

		$dbSubKey = acymailing_loadResult('SELECT `key` FROM '.acymailing_table('subscriber').' WHERE subid = '.intval($subid));

		if(empty($dbSubKey) || $subkey != $dbSubKey){
			echo "<script>alert('Couldn\'t identify the user'); window.history.go(-1);</script>";
			exit;
		}

		$nbForwarded = acymailing_loadResult('SELECT nbforwarded FROM '.acymailing_table('forward').' WHERE subid = '.intval($subid).' AND mailid = '.intval($mailid));
		if(empty($nbForwarded)) $nbForwarded = 0;                                     
		$config = acymailing_config();
		if($config->get('anonymous_tracking', 0) == 0) {
			$userHelper = acymailing_get('helper.user');
			$ip = $userHelper->getIP();
		}else{
			$ip = '';
		}
		
		$time = time();

		foreach($forwardusers as $oneUser => $infos){
			if(empty($infos['email'])) continue;
			if($nbForwarded > 4){
				acymailing_query('INSERT INTO '.acymailing_table('forward').' (`subid`,`mailid`,`date`,'.(empty($ip) ? '' : '`ip`,').'`nbforwarded`) VALUES ('.$subid.','.$mailid.','.$time.','.(empty($ip) ? '' : acymailing_escapeDB($ip).',').$nbForwarded.') ON DUPLICATE KEY UPDATE `date` = VALUES(`date`),`ip` = VALUES(`ip`),`nbforwarded` = VALUES(`nbforwarded`)');
				echo "<script>alert('You cannot forward an email to more than five addresses.'); window.history.go(-1);</script>";
				exit;
			}
			$receiver = new stdClass();
			$receiver->email = $infos['email'];
			$receiver->subid = 0;
			$receiver->html = 1;
			$receiver->name = $infos['name'];

			$introtext = '<div align="center" style="max-width:600px;margin:auto;margin-top:10px;margin-bottom:10px;border:1px solid #cccccc;background-color:#f6f6f6;color:#333333;">'.acymailing_translation('MESSAGE_TO_FORWARD').'</div> ';
			$values = array('{user:name}' => $sendername, '{user:email}' => $senderemail, '{forwardmsg}' => $forwardmsg);

			$mailerHelper->introtext = str_replace(array_keys($values), $values, $introtext);

			if($mailerHelper->sendOne($mailid, $receiver)){
				$nbForwarded++;
				acymailing_query('UPDATE '.acymailing_table('stats').' SET `forward` = `forward` +1 WHERE `mailid` = '.(int)$mailid);

				$data = array();
				$data['email'] = 'EMAIL::'.$receiver->email;
				$data['name'] = 'NAME::'.$receiver->name;
				$history->insert($subid, 'forward', $data, $mailid);
			}
		}

		acymailing_query('INSERT INTO '.acymailing_table('forward').' (`subid`,`mailid`,`date`,'.(empty($ip) ? '' : '`ip`,').'`nbforwarded`) VALUES ('.$subid.','.$mailid.','.$time.','.(empty($ip) ? '' : acymailing_escapeDB($ip).',').$nbForwarded.') ON DUPLICATE KEY UPDATE `date` = VALUES(`date`),`ip` = VALUES(`ip`),`nbforwarded` = VALUES(`nbforwarded`)');

		$mailkey = '&key='.$key;
		if(!empty($subidAndKey)) $userkey = '&subid='.$subidAndKey;

		if(acymailing_getVar('cmd', 'tmpl', '') == 'component'){
			$tmpl = '&tmpl=component';
		}else{
			$tmpl = '';
		}

		$url = 'archive&task=view&mailid='.$mailid.$mailkey.$userkey.$tmpl;
		acymailing_redirect(acymailing_completeLink($url, false, true));
	}

	function sendarchive(){

		$email = trim(acymailing_getVar('string', 'email'));
		$receiveEmails = acymailing_getVar('array', 'receivemail', array(), '');

		$config = acymailing_config();
		if(!$config->get('show_receiveemail', 0) || empty($receiveEmails) || empty($email)){
			return $this->listing();
		}
		acymailing_checkToken();
		acymailing_checkRobots();

		$userClass = acymailing_get('helper.user');
		if(!$userClass->validEmail($email, true)){
			echo "<script>alert('".acymailing_translation('VALID_EMAIL', true)."'); window.history.go(-1);</script>";
			exit;
		}

		if($config->get('captcha_plugin') != 'no') {
			$captchaClass = acymailing_get('class.acycaptcha');
			$captchaClass->state = 'acycaptchacomponent';
			if (!$captchaClass->check(acymailing_getVar('string', 'acycaptcha'))) {
				$captchaClass->returnError();
			}
		}

		acymailing_arrayToInteger($receiveEmails);

		$mailids = acymailing_loadResultArray("SELECT mailid FROM #__acymailing_mail WHERE mailid IN ('".implode("','", $receiveEmails)."') AND published = 1 AND visible = 1");

		$receiver = new stdClass();
		$receiver->email = $email;
		$receiver->subid = 0;
		$receiver->html = 1;
		$receiver->name = trim(strip_tags(acymailing_getVar('string', 'name', '')));

		$mailerHelper = acymailing_get('helper.mailer');
		$mailerHelper->checkConfirmField = false;
		$mailerHelper->checkEnabled = false;
		$mailerHelper->checkAccept = false;
		$mailerHelper->loadedToSend = true;

		foreach($mailids as $oneMailid){
			$mailerHelper->sendOne($oneMailid, $receiver);
		}

		return $this->listing();
	}

}
