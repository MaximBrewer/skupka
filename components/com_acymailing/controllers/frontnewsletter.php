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
$currentUserid = acymailing_currentUserId();
if(empty($currentUserid)){
	acymailing_askLog();
	return false;
}

$config = acymailing_config();
if(!acymailing_isAllowed($config->get('acl_newsletter_manage', 'all'))) die('You are not allowed to access this page');

include(ACYMAILING_BACK.'controllers'.DS.'newsletter.php');

class FrontnewsletterController extends NewsletterController{
	function __construct($config = array()){
		parent::__construct($config);

		$listid = acymailing_getVar('int', 'listid');
		if(empty($listid)){
			$listid = acymailing_getVar('int', 'filter_lists');
		}
		if(empty($listid)){
			$listid = acymailing_getUserVar("com_acymailing.frontnewsletterfilter_list", 'frontnewsletterfilter_list');
		}
		if(empty($listid)){
			$listClass = acymailing_get('class.list');
			$allAllowedLists = $listClass->getFrontendLists();
			if(!empty($allAllowedLists)){
				$firstList = reset($allAllowedLists);
				$listid = $firstList->listid;
			}
		}
		acymailing_setVar('filter_lists', $listid);
		acymailing_setVar('listid', $listid);

		if(!acymailing_accessList()){
			acymailing_enqueueMessage('You can not have access to this list', 'error');
			acymailing_redirect('index.php');
			return false;
		}

		if(!in_array(acymailing_getVar('none', 'task'), array('remove', 'form', 'cancel', 'copy'))){
			$mailid = acymailing_getCID('mailid');

			$edit = false;
			if(!empty($mailid) && !in_array(acymailing_getVar('none', 'task'), array('stats', 'detailstats', 'statsclick', 'export'))){
				$edit = true;
			}

			if(!$this->acyCheckEditNewsletter($edit)){
				acymailing_enqueueMessage(acymailing_translation_sprintf('NO_ACCESS_NEWSLETTER', $mailid), 'error');
				acymailing_redirect('index.php?option=com_acymailing&ctrl=frontnewsletter&listid='.$listid);
				return false;
			}
		}
	}

	function acyCheckEditNewsletter($edit = true){
		$mailid = acymailing_getCID('mailid');

		$listClass = acymailing_get('class.list');
		$lists = $listClass->getFrontendLists();
		$frontListsIds = array();
		foreach($lists as $oneList){
			$frontListsIds[] = $oneList->listid;
		}

		if(empty($mailid)) return true;

		$mail = acymailing_loadObject('SELECT * FROM `#__acymailing_mail` WHERE mailid = '.intval($mailid));
		if(empty($mail->mailid)) return false;
		$config = acymailing_config();
		if($edit && !$config->get('frontend_modif', 1) && acymailing_currentUserId() != $mail->userid) return false;
		if($edit && !$config->get('frontend_modif_sent', 1) && !empty($mail->senddate)) return false;

		$result = acymailing_loadResult('SELECT `mailid` FROM `#__acymailing_listmail` WHERE `mailid` = '.intval($mailid).' AND `listid` IN ('.implode(',', $frontListsIds).')');
		if(empty($result) && acymailing_currentUserId() != $mail->userid) return false;

		return true;
	}

	function form(){
		return $this->edit();
	}

	function remove(){
		$cids = acymailing_getVar('array', 'cid', array(), '');
		acymailing_arrayToInteger($cids);
		$config = acymailing_config();

		if(empty($cids)) acymailing_redirect('index.php?option=com_acymailing&ctrl=frontnewsletter');

		$rightDeleteOther = $config->get('frontend_modif', 1);
		if($rightDeleteOther){
			$listClass = acymailing_get('class.list');
			$lists = $listClass->getFrontendLists();
			$frontListsIds = array();
			foreach($lists as $oneList){
				$frontListsIds[] = $oneList->listid;
			}
			acymailing_arrayToInteger($frontListsIds);
			$mails = acymailing_loadResultArray('SELECT mailid FROM #__acymailing_listmail WHERE mailid IN ('.implode(",", $cids).') AND listid IN ('.implode(',', $frontListsIds).') GROUP BY mailid');
			$result = array_diff($cids, $mails);
			foreach($result as $mailOtherList){
				acymailing_enqueueMessage(acymailing_translation_sprintf('NO_ACCESS_NEWSLETTER', $mailOtherList->mailid), 'error');
			}
			$cids = $mails;
		}else{
			$mails = acymailing_loadObjectList('SELECT * FROM `#__acymailing_mail` WHERE mailid IN ('.implode(',', $cids).')');
			foreach($mails as $mail){
				if(acymailing_currentUserId() != $mail->userid){
					acymailing_enqueueMessage(acymailing_translation_sprintf('NO_ACCESS_NEWSLETTER', $mail->mailid), 'error');
					array_splice($cids, array_search($mail->mailid, $cids), 1);
				}
			}
		}

		acymailing_setVar('cid', $cids);
		return parent::remove();
	}

	function scheduleconfirm(){
		acymailing_setVar('layout', 'scheduleconfirm');
		return parent::display();
	}

	function schedule(){
		if(!$this->isAllowed('newsletters', 'schedule')) return;
		acymailing_checkToken();

		$mailid = acymailing_getCID('mailid');

		$senddate = acymailing_getVar('string', 'senddate', '');
		$sendhours = acymailing_getVar('string', 'sendhours', '');
		$sendminutes = acymailing_getVar('string', 'sendminutes', '');
		$senddateComplete = $senddate.' '.$sendhours.':'.$sendminutes;

		if(empty($senddate)){
			acymailing_display(acymailing_translation('SPECIFY_DATE'), 'warning');
			return $this->scheduleconfirm();
		}

		$realSendDate = acymailing_getTime($senddateComplete);
		if($realSendDate < time()){
			acymailing_display(acymailing_translation('DATE_FUTURE'), 'warning');
			return $this->scheduleconfirm();
		}

		$mail = new stdClass();
		$mail->mailid = $mailid;
		$mail->senddate = $realSendDate;
		$mail->sentby = acymailing_currentUserId();
		$mail->published = 2;

		$mailClass = acymailing_get('class.mail');
		$mailClass->save($mail);

		$myNewsletter = $mailClass->get($mailid);

		acymailing_setVar('tmpl', 'component');

		acymailing_display(acymailing_translation_sprintf('AUTOSEND_DATE', '<b><i>'.$myNewsletter->subject.'</i></b>', acymailing_getDate($realSendDate)), 'success');

		$config = acymailing_config();
		$redirecturl = $config->get('redirect_schedule');
		if(empty($redirecturl)) $redirecturl = "index.php?option=com_acymailing&ctrl=frontnewsletter&listid=".acymailing_getVar('int', 'listid');

		$js = "setTimeout('redirect()',2000); function redirect(){window.top.location.href = '".$redirecturl."'; }";

		acymailing_addScript(true, $js);
	}

	function delete(){

		list($mailid, $attachid) = explode('_', acymailing_getVar('cmd', 'value'));
		$mailid = intval($mailid);
		if(empty($mailid)) return false;

		$attachment = acymailing_loadResult('SELECT `attach` FROM '.acymailing_table('mail').' WHERE mailid = '.$mailid.' LIMIT 1');
		if(empty($attachment)) return;
		$attach = unserialize($attachment);

		unset($attach[$attachid]);
		$attachdb = serialize($attach);

		acymailing_query('UPDATE '.acymailing_table('mail').' SET attach = '.acymailing_escapeDB($attachdb).' WHERE mailid = '.$mailid.' LIMIT 1');

		exit;
	}

	function edit(){
		acymailing_setVar('layout', 'form');
		return parent::display();
	}

	function sendconfirm(){
		acymailing_setVar('layout', 'sendconfirm');
		return parent::display();
	}

	function send(){
		if(!$this->isAllowed('newsletters', 'send')) return;
		acymailing_checkToken();

		$mailid = acymailing_getCID('mailid');
		if(empty($mailid)) exit;

		$time = time();
		$queueClass = acymailing_get('class.queue');
		$nbEmails = $queueClass->nbQueue($mailid);
		if($nbEmails > 0){
			acymailing_enqueueMessage(acymailing_translation_sprintf('ALREADY_QUEUED', $nbEmails), 'notice');
			return;
		}

		$queueClass->onlynew = acymailing_getVar('int', 'onlynew');
		$queueClass->mindelay = acymailing_getVar('int', 'mindelay');
		$totalSub = $queueClass->queue($mailid, $time);

		if(empty($totalSub)){
			acymailing_enqueueMessage(acymailing_translation('NO_RECEIVER'), 'notice');
			return;
		}

		$mailObject = new stdClass();
		$mailObject->senddate = $time;
		$mailObject->published = 1;
		$mailObject->mailid = $mailid;
		$mailObject->sentby = acymailing_currentUserId();
		acymailing_updateObject(acymailing_table('mail'), $mailObject, 'mailid');

		acymailing_display(acymailing_translation_sprintf('ADDED_QUEUE', $totalSub));
		acymailing_display(acymailing_translation_sprintf('AUTOSEND_CONFIRMATION', $totalSub));

		$config = acymailing_config();
		$redirecturl = $config->get('redirect_send');
		if(empty($redirecturl)){
			$redirecturl = acymailing_completeLink("frontnewsletter&listid=".acymailing_getVar('int', 'listid'), false, true);
			$redirecturl = str_replace(acymailing_noTemplate().'&', '', $redirecturl);
			$redirecturl = preg_replace('#(\?|&)'.preg_quote(acymailing_noTemplate(), '#').'#Uis', '', $redirecturl);
		}
		$js = "setTimeout('redirect()',2000); function redirect(){window.top.location.href = '$redirecturl'; }";
		acymailing_addScript(true, $js);
		return false;
	}

	function spamtest(){
		include_once(ACYMAILING_BACK.'controllers'.DS.'send.php');
		$sendController = new SendController();
		$sendController->spamtest();
	}
}
