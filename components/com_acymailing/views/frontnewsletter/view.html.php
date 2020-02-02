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
include(ACYMAILING_BACK.'views'.DS.'newsletter'.DS.'view.html.php');

class FrontnewsletterViewFrontnewsletter extends NewsletterViewNewsletter
{

	var $ctrl='frontnewsletter';

	function display($tpl = null)
	{
		acymailing_addStyle(false, ACYMAILING_CSS.'frontendedition.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'frontendedition.css'));

		global $Itemid;
		$this->Itemid = $Itemid;
		parent::display($tpl);
	}

	function listing(){

		if(empty($_POST) && !acymailing_getVar('int', 'start') && !acymailing_getVar('int', 'limitstart')){
			acymailing_setVar('limitstart',0);
		}

		return parent::listing();
	}

	function preview(){
		$config = acymailing_config();
		$this->config = $config;
		return parent::preview();
	}

	function form(){
		return parent::form();
	}

	function scheduleconfirm(){
		acymailing_setVar('tmpl', 'component');
		$mailid = acymailing_getCID('mailid');
		$listmailClass = acymailing_get('class.listmail');
		$mailClass = acymailing_get('class.mail');
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

		acymailing_addScript(false, "https://www.google.com/jsapi");

		$this->lists = $listmailClass->getReceivers($mailid);
		$this->mail = $mailClass->get($mailid);
		$this->hours = $hours;
		$this->min = $minutes;
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
}

