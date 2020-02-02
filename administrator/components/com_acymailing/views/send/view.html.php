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


class SendViewSend extends acymailingView{

	function display($tpl = null){
		$function = $this->getLayout();
		if(method_exists($this, $function)) $this->$function();

		parent::display($tpl);
	}

	function sendconfirm(){

		$mailid = acymailing_getCID('mailid');
		$mailClass = acymailing_get('class.mail');
		$listmailClass = acymailing_get('class.listmail');
		$queueClass = acymailing_get('class.queue');
		$mail = $mailClass->get($mailid);

		$values = new stdClass();
		$values->nbqueue = $queueClass->nbQueue($mailid);

		if(empty($values->nbqueue)){
			$lists = $listmailClass->getReceivers($mailid);
			$this->lists = $lists;

			$values->alreadySent = acymailing_loadResult('SELECT count(subid) FROM `#__acymailing_userstats` WHERE `mailid` = '.intval($mailid));
		}

		$this->values = $values;
		$this->mail = $mail;
	}

	function scheduleconfirm(){
		$this->chosen = false;

		$mailid = acymailing_getCID('mailid');
		$listmailClass = acymailing_get('class.listmail');
		$mailClass = acymailing_get('class.mail');

		$listHours = array();
		$listMinutess = array();
		$defaultMinutes = ceil(acymailing_getDate(time(), '%M') / 5) * 5;
		$defaultHours = acymailing_getDate(time(), 'G');

		for($i = 0; $i < 24; $i++){
			$listHours[] = acymailing_selectOption($i, ($i < 10 ? '0'.$i : $i));
		}
		$hours = acymailing_select($listHours, 'sendhours', 'class="inputbox" size="1" style="width:60px;"', 'value', 'text', $defaultHours);
		for($i = 0; $i < 60; $i += 5){
			$listMinutess[] = acymailing_selectOption($i, ($i < 10 ? '0'.$i : $i));
		}
		$minutes = acymailing_select($listMinutess, 'sendminutes', 'class="inputbox" size="1" style="width:60px;"', 'value', 'text', $defaultMinutes);

		$alreadySent = acymailing_loadResult('SELECT count(subid) FROM `#__acymailing_userstats` WHERE `mailid` = '.intval($mailid));

		acymailing_addScript(false, "https://www.google.com/jsapi");

		$this->alreadySent = $alreadySent;
		$this->lists = $listmailClass->getReceivers($mailid);
		$this->mail = $mailClass->get($mailid);
		$this->hours = $hours;
		$this->minutes = $minutes;
	}

	function addqueue(){
		$subid = acymailing_getVar('int', 'subid');
		if(empty($subid)) exit;

		$subscriberClass = acymailing_get('class.subscriber');
		$subscriber = $subscriberClass->getFull($subid);

		if(acymailing_isAdmin()){
			$allEmails = acymailing_loadObjectList("SELECT `mailid`, `subject`,`alias`, `type` FROM `#__acymailing_mail` WHERE `type` NOT IN ('notification','autonews') OR `alias` = 'confirmation' AND `published` = 1 ORDER BY `type`,`subject` ASC ");
		}else{
			$listClass = acymailing_get('class.list');
			$lists = $listClass->getFrontendLists();
			$listids = array(-1);
			foreach($lists as $oneList) $listids[] = intval($oneList->listid);
			$query = "SELECT m.`mailid`, m.`subject`, m.`alias`, m.`type` "."FROM `#__acymailing_mail` AS m "."LEFT JOIN #__acymailing_listmail AS lm "."ON lm.mailid = m.mailid "."LEFT JOIN #__acymailing_list AS l "."ON l.listid = lm.listid "."WHERE (m.`type` NOT IN ('notification','autonews') OR m.`alias` = 'confirmation') "."AND m.`published` = 1 "."AND (m.userid = ".intval($subscriber->userid)." OR l.listid IN (".implode(',', $listids).")) "."GROUP BY m.mailid "."ORDER BY m.`type`,m.`subject` ASC ";
			$allEmails = acymailing_loadObjectList($query);
		}

		$emailsToDisplay = array();
		$typeNews = '';
		foreach($allEmails as $oneMail){
			if($oneMail->type != $typeNews){
				if(!empty($typeNews)) $emailsToDisplay[] = acymailing_selectOption('</OPTGROUP>');
				$typeNews = $oneMail->type;
				if($oneMail->type == 'news'){
					$label = acymailing_translation('NEWSLETTERS');
				}elseif($oneMail->type == 'followup'){
					$label = acymailing_translation('FOLLOWUP');
				}elseif($oneMail->type == 'welcome'){
					$label = acymailing_translation('MSG_WELCOME');
				}elseif($oneMail->type == 'unsub'){
					$label = acymailing_translation('MSG_UNSUB');
				}else{
					$label = $oneMail->type;
				}
				$emailsToDisplay[] = acymailing_selectOption('<OPTGROUP>', $label);
			}
			$oneMail->subject = acyEmoji::Decode($oneMail->subject);
			$emailsToDisplay[] = acymailing_selectOption($oneMail->mailid, $oneMail->subject.' ('.$oneMail->mailid.' : '.$oneMail->alias.')');
		}
		$emailsToDisplay[] = acymailing_selectOption('</OPTGROUP>');

		$emaildrop = acymailing_select($emailsToDisplay, 'mailid', 'class="inputbox" size="1" style="width:300px;"', 'value', 'text', acymailing_getVar('int', 'mailid'));

		$listHours = array();
		$listMinutess = array();
		$defaultMinutes = ceil(acymailing_getDate(time(), '%M') / 5) * 5;
		$defaultHours = acymailing_getDate(time(), '%H');

		for($i = 0; $i < 24; $i++){
			$listHours[] = acymailing_selectOption($i, ($i < 10 ? '0'.$i : $i));
		}
		$hours = acymailing_select($listHours, 'sendhours', 'class="inputbox" size="1" style="width:60px;"', 'value', 'text', $defaultHours);
		for($i = 0; $i < 60; $i += 5){
			$listMinutess[] = acymailing_selectOption($i, ($i < 10 ? '0'.$i : $i));
		}
		$minutes = acymailing_select($listMinutess, 'sendminutes', 'class="inputbox" size="1" style="width:60px;"', 'value', 'text', $defaultMinutes);

		if(acymailing_isAdmin()){
			$this->subscriber = $subscriber;
			$this->emaildrop = $emaildrop;
			$this->hours = $hours;
			$this->minutes = $minutes;
		}else{
			return array('subscriber' => $subscriber, 'emaildrop' => $emaildrop, 'hours' => $hours, 'minutes' => $minutes);
		}
	}

}
