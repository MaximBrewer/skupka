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

class SimplemailController extends acymailingController{
	
	public function edit(){
		if(!acymailing_level(3) || !$this->isAllowed('simple_sending', 'manage')) return;
		acymailing_setVar('layout', 'edit');
		return parent::display();
	}

	public function send(){
		if(!acymailing_level(3) || !$this->isAllowed('simple_sending', 'manage')) return;
		$templateClass = acymailing_get('class.template');
		$acypluginsHelper = acymailing_get('helper.acyplugins');

		$failed = array();
		$success = array();

		$body = acymailing_getVar('string', 'editor_body', '', '', ACY_ALLOWRAW);
		$acypluginsHelper->cleanHtml($body);
		$subject = acymailing_getVar('string', 'subject', '');
		$templateId = acymailing_getVar('int', 'tempid', 0);
		$users = explode(',', acymailing_getVar('none', 'test_emails', ''));

		acymailing_importPlugin('acymailing');
		$userClass = acymailing_get('class.subscriber');

		$mail = new stdClass();
		$mail->sendHTML = true;

		if($templateId > 0) $mail->tempid = $templateId;

		foreach($users as $to){
			$mailer = acymailing_get('helper.acymailer');
			$mailer->report = false;
			$mailer->isHTML(true);
			if($templateId > 0) $mailer->template = $templateClass->get($templateId);

			$mailer->addAddress($to);
			$user = $userClass->get($to);

			$mail->subject = $subject;
			$mail->body = $body;

			acymailing_trigger('acymailing_replacetags', array(&$mail, true));
			acymailing_trigger('acymailing_replaceusertags', array(&$mail, &$user, true));

			$mailer->Subject = $mail->subject;
			$mailer->Body = $mail->body;

			$result = $mailer->send();
			if(!$result){
				$failed[] = $to;
			}else{
				$success[] = $to;
			}
		}

		if(sizeof($failed) == 0){
			acymailing_enqueueMessage(acymailing_translation_sprintf('SIMPLE_SENDING_SUCCESS', implode(', ', $success)));
		}else{
			acymailing_enqueueMessage(acymailing_translation_sprintf('SIMPLE_SENDING_ERROR', implode(', ', $failed), $mailer->reportMessage), 'error');
		}

		acymailing_session();
		$_SESSION['acymailing']['simplesending_tempid'] = $templateId;
		$_SESSION['acymailing']['simplesending_subject'] = $subject;
		$_SESSION['acymailing']['simplesending_body'] = $body;
		
		acymailing_redirect(acymailing_completeLink('simplemail&task=edit'));
	}
}
