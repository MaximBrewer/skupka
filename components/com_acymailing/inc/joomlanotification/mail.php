<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Mail
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

//We play safe... it the JMail class is already defined, we don't load our file
if(class_exists('JMail', false)) return;

jimport('phpmailer.phpmailer');
$jversion = preg_replace('#[^0-9\.]#i', '', JVERSION);
if(version_compare($jversion, '1.6.0', '>=')){
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'jMail_J25.php');
}else{
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'jMail_J15.php');
}

//Just to be safe...
if(!class_exists('jMail_acy')) return;

/**
 * Email Class.  Provides a common interface to send email from the Joomla! Platform
 *
 * @package     Joomla.Platform
 * @subpackage  Mail
 * @since       11.1
 */
class JMail extends jMail_acy{
	// Link between Joomla notification and Acymailing mail
	protected $bodyAliasCorres = array();

	/**
	 * Constructor
	 */
	public function __construct(){
		$this->initMailCorrespondance();
		parent::__construct();
	}

	// Create link between joomla message and corresponding Acymailing mail (alias)
	protected function initMailCorrespondance(){
		include_once(rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_acymailing'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php');
		
		$jversion = preg_replace('#[^0-9\.]#i', '', JVERSION);
		if(version_compare($jversion, '1.6.0', '>=')){
			if(version_compare($jversion, '3.0.0', '>=')){
				$this->bodyAliasCorres['joomla-directreg-j3'] = acymailing_translation('COM_USERS_EMAIL_REGISTERED_BODY');
				$this->bodyAliasCorres['joomla-directRegNoPwd-j3'] = acymailing_translation('COM_USERS_EMAIL_REGISTERED_BODY_NOPW');
			}else{
				$this->bodyAliasCorres['joomla-directreg'] = acymailing_translation('COM_USERS_EMAIL_REGISTERED_BODY');
			}
			$this->bodyAliasCorres['joomla-ownActivReg'] = acymailing_translation('COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY');
			$this->bodyAliasCorres['joomla-ownActivRegNoPwd'] = acymailing_translation('COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY_NOPW');
			$this->bodyAliasCorres['joomla-adminActivReg'] = acymailing_translation('COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY');
			$this->bodyAliasCorres['joomla-adminActivRegNoPwd'] = acymailing_translation('COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY_NOPW');
			$this->bodyAliasCorres['joomla-confirmActiv'] = acymailing_translation('COM_USERS_EMAIL_ACTIVATED_BY_ADMIN_ACTIVATION_BODY');
			$this->bodyAliasCorres['joomla-usernameReminder'] = acymailing_translation('COM_USERS_EMAIL_USERNAME_REMINDER_BODY');
			$this->bodyAliasCorres['joomla-resetPwd'] = acymailing_translation('COM_USERS_EMAIL_PASSWORD_RESET_BODY');
			$this->bodyAliasCorres['joomla-regByAdmin'] = acymailing_translation('PLG_USER_JOOMLA_NEW_USER_EMAIL_BODY');
			$this->bodyAliasCorres['joomla-regNotifAdmin'] = acymailing_translation('COM_USERS_EMAIL_REGISTERED_NOTIFICATION_TO_ADMIN_BODY');
			$this->bodyAliasCorres['joomla-regNotifAdminActiv'] = acymailing_translation('COM_USERS_EMAIL_ACTIVATE_WITH_ADMIN_ACTIVATION_BODY');
			// Joomla front send article
			$this->bodyAliasCorres['joomla-frontsendarticle'] = acymailing_translation('COM_MAILTO_EMAIL_MSG');

			// JomSocial
			$this->bodyAliasCorres['jomsocial-directreg'] = acymailing_translation('COM_COMMUNITY_EMAIL_REGISTRATION_ACCOUNT_DETAILS');
			$this->bodyAliasCorres['jomsocial-ownActivReg'] = acymailing_translation('COM_COMMUNITY_EMAIL_REGISTRATION_COMPLETED_REQUIRES_ACTIVATION');
			$this->bodyAliasCorres['jomsocial-welcomeactiv'] = acymailing_translation('COM_COMMUNITY_EMAIL_REGISTRATION_ACCOUNT_DETAILS_REQUIRES_ACTIVATION');
			$this->bodyAliasCorres['jomsocial-regactivadmin'] = acymailing_translation('COM_COMMUNITY_EMAIL_REGISTRATION_COMPLETED_REQUIRES_ADMIN_ACTIVATION');
			$this->bodyAliasCorres['jomsocial-notifadmin'] = acymailing_translation('COM_COMMUNITY_SEND_MSG_ADMIN');
			$this->bodyAliasCorres['jomsocial-notifadminactiv'] = acymailing_translation('COM_COMMUNITY_USER_REGISTERED_NEEDS_APPROVAL');
			$this->bodyAliasCorres['jomsocial-notifactivated'] = acymailing_translation('COM_COMMUNITY_EMAIL_ACTIVATED_BY_ADMIN_ACTIVATION_BODY');
			$this->bodyAliasCorres['jomsocial-notifaccountparameters'] = acymailing_translation('COM_COMMUNITY_EMAIL_REGISTRATION');
			// Seblod
			$this->bodyAliasCorres['seblod-directregnopwd'] = acymailing_translation('COM_CCK_EMAIL_REGISTERED_BODY_NOPW');
			$this->bodyAliasCorres['seblod-directreg'] = acymailing_translation('COM_CCK_EMAIL_REGISTERED_BODY');
			$this->bodyAliasCorres['seblod-notifadmin'] = acymailing_translation('COM_CCK_EMAIL_REGISTERED_NOTIFICATION_TO_ADMIN_BODY');
			$this->bodyAliasCorres['seblod-ownactivreg'] = acymailing_translation('COM_CCK_EMAIL_REGISTERED_WITH_ACTIVATION_BODY');
			$this->bodyAliasCorres['seblod-ownactivregnopwd'] = acymailing_translation('COM_CCK_EMAIL_REGISTERED_WITH_ACTIVATION_BODY_NOPW');
			$this->bodyAliasCorres['seblod-adminactivreg'] = acymailing_translation('COM_CCK_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY');
			$this->bodyAliasCorres['seblod-adminactivregnopwd'] = acymailing_translation('COM_CCK_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY_NOPW');
		}else{
			$this->bodyAliasCorres['joomla-directreg'] = acymailing_translation('SEND_MSG');
			$this->bodyAliasCorres['joomla-ownActivReg'] = acymailing_translation('SEND_MSG_ACTIVATE');
			$this->bodyAliasCorres['joomla-usernameReminder'] = acymailing_translation('USERNAME_REMINDER_EMAIL_TEXT');
			$this->bodyAliasCorres['joomla-resetPwd'] = acymailing_translation('PASSWORD_RESET_CONFIRMATION_EMAIL_TEXT');
			$this->bodyAliasCorres['joomla-regByAdmin'] = acymailing_translation('NEW_USER_MESSAGE');
			$this->bodyAliasCorres['joomla-regNotifAdmin'] = acymailing_translation('SEND_MSG_ADMIN');
			$this->bodyAliasCorres['joomla-frontsendarticle'] = acymailing_translation('EMAIL_MSG');
		}
	}

	// Use Acymailing to send emails
	protected function sendMailThroughAcy(){
		// Check if this is a notifification that we override. If yes send with Acymailing, if no let Joomla handle it.
		foreach($this->bodyAliasCorres as $alias => $oneMsg){
			// Change default texts to regexp in order to identify mail and get values (%s, %1$s...)
			$oneMsg = preg_replace('/%([0-9].?\$)?s/', '(.*)', preg_quote($oneMsg, '/'));
			$oneMsg = str_replace('&amp;', '&', $oneMsg);

			$testMail = preg_match('/'.trim($oneMsg).'/', $this->Body, $matches);
			if($testMail !== 1) continue;
			$mailNotif = acymailing_loadObject('SELECT * FROM #__acymailing_mail WHERE `alias` = '.acymailing_escapeDB($alias).' AND `type` = \'joomlanotification\'');
			if($mailNotif->published != 1) break;

			$acymailer = acymailing_get('helper.acymailer');
			// Skip check on user enabled
			$acymailer->checkConfirmField = false;
			$acymailer->checkEnabled = false;
			$acymailer->checkAccept = false;
			$acymailer->report = false;
			// Check if the user needs to be created in Acymailing
			if(!isset($this->params)){
				$plugin = acymailing_getPlugin('system', 'acymailingclassmail');
				$paramsacyclassmail = new acyParameter($plugin->params);
			}
			$createAcyUser = $paramsacyclassmail->get('createacyuser', 1);

			if($createAcyUser == 1){
				$acymailer->trackEmail = true;
				$acymailer->autoAddUser = true;
			}else{
				$acymailer->trackEmail = false;
				$acymailer->autoAddUser = false;
				// Create receveir object to avoid creation of subscribed in acymailer helper
				$receiver = new stdClass();
				$receiver->subid = 0;
				$receiver->email = $this->to[0][0];
				// Define a default name to avoid issue with addnames option (can't replace [name] in the receiver name and can create phpmail error)
				if(!empty($this->to[0][1])){
					$receiver->name = $this->to[0][1];
				}else{
					$receiver->name = substr($receiver->email, 0, strpos($receiver->email, '@'));
				}
				$receiver->html = 1;
				$receiver->confirmed = 1;
				$receiver->enabled = 1;
			}

			for($i = 1; $i < count($matches); $i++){
				// Joomla emails does not contain links with href but links as text
				$tmp = $matches[$i];
				if($this->ContentType != 'text/html'){
					$matches[$i] = preg_replace('/(http|https):\/\/(.*)/', '<a href="$1://$2" target="_blank">$1://$2</a>', $matches[$i], -1, $count);
					if($count > 0) $acymailer->addParam('link'.$i, $tmp);
					if($count > 0) $acymailer->addParam('link', $tmp);
				}
				$acymailer->addParam('param'.$i, $matches[$i]);
			}

			// Special case for share an article from Joomla to get the subject set by the user
			if($alias == 'joomla-frontsendarticle'){
				$acymailer->addParam('senderSubject', $this->Subject);
			}

			if($createAcyUser == 1){
				$statusSend = $acymailer->sendOne($mailNotif->mailid, $this->to[0][0]);
			}else{
				$statusSend = $acymailer->sendOne($mailNotif->mailid, $receiver);
			}
			if(!$statusSend) acymailing_enqueueMessage(nl2br($acymailer->reportMessage), 'error');

			return $statusSend;
		}
		// No message sent
		return 'noSend';
	}

	/**
	 * Send the mail
	 *
	 * @return  mixed  True if successful, a JError object otherwise
	 *
	 * @since   11.1
	 */
	public function Send(){
		// Include Acymailing to override mails
		if(include_once(rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_acymailing'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php')){
			$ret = $this->sendMailThroughAcy();
		}

		if($ret === true || $ret === false){
			return $ret;
		}
		return parent::Send();
	}
}

if(version_compare($jversion, '3.8.0', '>=')){
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'jMail_J38.php');
}