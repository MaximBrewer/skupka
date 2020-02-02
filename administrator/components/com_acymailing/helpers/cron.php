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

class acycronHelper{

	var $report = false;
	var $messages = array();
	var $detailMessages = array();
	var $processed = false;
	var $executed = false;
	var $mainmessage = '';
	var $errorDetected = false;
	var $skip = array();
	var $emailtypes = array();

	function cron(){
		$time = time();
		$config = acymailing_config();

		$firstMessage = acymailing_translation_sprintf('ACY_CRON_TRIGGERED', acymailing_getDate(time()));
		$this->messages[] = $firstMessage;
		if($this->report){
			acymailing_display($firstMessage, 'info');
		}

		if($config->get('cron_next') > $time){

			if($config->get('cron_next') > ($time + $config->get('cron_frequency'))){
				$newConfig = new stdClass();
				$newConfig->cron_next = $time + $config->get('cron_frequency');
				$config->save($newConfig);
			}

			$nottime = acymailing_translation_sprintf('CRON_NEXT', acymailing_getDate($config->get('cron_next')));
			$this->messages[] = $nottime;
			if($this->report){
				acymailing_display($nottime, 'info');
			}
			return false;
		}

		acymailing_importPlugin('acymailing');

		$queueHelper = acymailing_get('helper.queue');

		$this->executed = true;

		$newConfig = new stdClass();
		$newConfig->cron_next = $config->get('cron_next') + $config->get('cron_frequency');
		if($newConfig->cron_next <= $time OR $newConfig->cron_next > $time + $config->get('cron_frequency')) $newConfig->cron_next = $time + $config->get('cron_frequency');

		$newConfig->cron_last = $time;
		$userHelper = acymailing_get('helper.user');
		$newConfig->cron_fromip = $userHelper->getIP();

		$config->save($newConfig);

		if($config->get('queue_type') != 'manual' && !in_array('send', $this->skip)){
			$queueHelper->send_limit = (int)$config->get('queue_nbmail_auto');
			$queueHelper->report = false;
			$queueHelper->emailtypes = $this->emailtypes;
			$queueHelper->process();
			if(!empty($queueHelper->messages)){
				$this->detailMessages = array_merge($this->detailMessages, $queueHelper->messages);
			}
			if(!empty($queueHelper->nbprocess)) $this->processed = true;
			$this->mainmessage = acymailing_translation_sprintf('ACY_CRON_PROCESS', $queueHelper->nbprocess, $queueHelper->successSend, $queueHelper->errorSend);
			$this->messages[] = $this->mainmessage;

			if(!empty($queueHelper->errorSend)) $this->errorDetected = true;
			if(!empty($queueHelper->stoptime) AND time() > $queueHelper->stoptime) return true;
		}

		if(acymailing_level(2) && !in_array('autonews', $this->skip)){
			$autonewsHelper = acymailing_get('helper.autonews');
			$resultAutonews = $autonewsHelper->generate();
			if(!empty($autonewsHelper->messages)){
				$this->messages = array_merge($this->messages, $autonewsHelper->messages);
				$this->processed = true;
			}

			if(!empty($queueHelper->stoptime) AND time() > $queueHelper->stoptime) return true;
		}

		if(!in_array('schedule', $this->skip)){
			$schedHelper = acymailing_get('helper.schedule');
			$resultSchedule = $schedHelper->queueScheduled();
			if($resultSchedule){
				if(!empty($schedHelper->nbNewsletterScheduled)) $this->messages[] = acymailing_translation_sprintf('NB_SCHED_NEWS', $schedHelper->nbNewsletterScheduled);
				$this->detailMessages = array_merge($this->detailMessages, $schedHelper->messages);
				$this->processed = true;
			}
		}

		if(!empty($queueHelper->stoptime) AND time() > $queueHelper->stoptime) return true;

		$startQueue = acymailing_getVar('int', 'startqueue', 0); // Add security to avoid multiple trigger of the final send
		if(!in_array('abtesting', $this->skip) && acymailing_level(3) && $startQueue == 0){
			$currentAbTests = $config->get('currentABTests', '');
			$currentData = unserialize($currentAbTests);
			if(!empty($currentData)){
				$valueToDelete = '';
				foreach($currentData as $key => $oneTest){
					if($oneTest->sendDate < time()){
						$mailClass = acymailing_get('class.mail');
						$newMailid = $mailClass->updateAbTest_auto($oneTest->ids);
						if(!empty($newMailid)){
							$valueToDelete = $key;
							break;
						}
					}
				}
				if(is_numeric($valueToDelete) && !empty($currentData[$valueToDelete])){
					unset($currentData[$valueToDelete]);
					$newconfig = new stdClass();
					$newconfig->currentABTests = serialize($currentData);
					$config->save($newconfig);
					$this->messages[] = acymailing_translation('ABTESTING').': '.acymailing_translation_sprintf('ABTESTING_FINALSEND', $newMailid);
					$this->processed = true;
				}
			}
		}

		if(!in_array('plugins', $this->skip) && $config->get('cron_plugins_next') < $time){
			$newConfig = new stdClass();
			$newConfig->cron_plugins_next = $config->get('cron_plugins_next', 0) + 86400;
			if($newConfig->cron_plugins_next <= $time) $newConfig->cron_plugins_next = $time + 86400;
			$config->save($newConfig);

			$resultsTrigger = acymailing_trigger('onAcyCronTrigger');
			if(!empty($resultsTrigger)){
				$this->processed = true;
				$this->messages = array_merge($this->messages, $resultsTrigger);
			}

			if(date('w') == 0){
				$this->cleanStats();
			}

			if(!empty($queueHelper->stoptime) AND time() > $queueHelper->stoptime) return true;
		}

		if(!in_array('bounce', $this->skip) && acymailing_level(3) && $config->get('auto_bounce', 0) && $time > (int)$config->get('auto_bounce_next', 0) && (empty($queueHelper->stoptime) || time() < $queueHelper->stoptime - 5)){
			$newConfig = new stdClass();
			$newConfig->auto_bounce_next = $time + (int)$config->get('auto_bounce_frequency', 0);
			$newConfig->auto_bounce_last = $time;
			$config->save($newConfig);
			$bounceClass = acymailing_get('helper.bounce');
			$bounceClass->report = false;
			$bounceClass->stoptime = $queueHelper->stoptime;
			$newConfig = new stdClass();
			if($bounceClass->init() && $bounceClass->connect()){
				$nbMessages = $bounceClass->getNBMessages();
				$this->messages[] = acymailing_translation_sprintf('NB_MAIL_MAILBOX', $nbMessages);
				$newConfig->auto_bounce_report = acymailing_translation_sprintf('NB_MAIL_MAILBOX', $nbMessages);
				$this->detailMessages[] = acymailing_translation_sprintf('NB_MAIL_MAILBOX', $nbMessages);
				if(!empty($nbMessages)){
					$bounceClass->handleMessages();
					$bounceClass->close();
					$this->processed = true;
				}
				$this->detailMessages = array_merge($this->detailMessages, $bounceClass->messages);
			}else{
				$bounceErrors = $bounceClass->getErrors();
				$newConfig->auto_bounce_report = implode('<br />', $bounceErrors);
				if(!empty($bounceErrors[0])) $bounceErrors[0] = acymailing_translation('BOUNCE_HANDLING').' : '.$bounceErrors[0];
				$this->messages = array_merge($this->messages, $bounceErrors);
				$this->processed = true;
				$this->errorDetected = true;
			}
			$config->save($newConfig);
			if(!empty($queueHelper->stoptime) AND time() > $queueHelper->stoptime) return true;
		}

		if(!in_array('filters', $this->skip) && acymailing_level(3)){
			$filterClass = acymailing_get('class.filter');
			$filterClass->trigger('daycron');
			if(!empty($filterClass->report)){
				if($filterClass->didAnAction) $this->processed = true;
				$this->messages = array_merge($this->messages, $filterClass->report);
				$filterClass->report = array();
			}

			$resultsTrigger = acymailing_trigger('onAcyEveryCronTrigger');
			if(!empty($resultsTrigger)){
				$this->processed = true;
				$this->messages = array_merge($this->messages, $resultsTrigger);
			}

			$filterClass->trigger('allcron');
			if(!empty($filterClass->report)){
				if($filterClass->didAnAction) $this->processed = true;
				$this->messages = array_merge($this->messages, $filterClass->report);
			}
		}

		if(!in_array('actions', $this->skip) && acymailing_level(3) && (empty($queueHelper->stoptime) || time() < $queueHelper->stoptime - 5)){
			$actions = acymailing_loadResultArray('SELECT action_id FROM #__acymailing_action WHERE published = 1 AND actions != '.acymailing_escapeDB('[{"type":"none"}]').' AND nextdate < '.time().' ORDER BY ordering ASC');
			if(!empty($actions)){
				$actionClass = acymailing_get('class.action');
				$bounceHelper = acymailing_get('helper.bounce');
				$bounceHelper->report = false;
				$bounceHelper->stoptime = $queueHelper->stoptime;

				foreach($actions as $oneActionId){
					$bounceHelper->messages = array();
					$currentAction = $actionClass->get($oneActionId);
					$bounceHelper->action = $currentAction;
					if($bounceHelper->init() && $bounceHelper->connect()){
						$nbMessages = $bounceHelper->getNBMessages();
						$this->messages[] = acymailing_translation_sprintf('NB_MAIL_MAILBOX', $nbMessages);
						$newConfig->auto_bounce_report = acymailing_translation_sprintf('NB_MAIL_MAILBOX', $nbMessages);
						$this->detailMessages[] = acymailing_translation_sprintf('NB_MAIL_MAILBOX', $nbMessages);
						if(!empty($nbMessages)){
							$bounceHelper->handleAction();
							$bounceHelper->close();
							$this->processed = true;
						}
						$this->detailMessages = array_merge($this->detailMessages, $bounceHelper->messages);
						$currentReport = implode("\n", $bounceHelper->messages);
					}else{
						$connectErrors = $bounceHelper->getErrors();
						$currentReport = implode("\n", $connectErrors);

						if(!empty($connectErrors[0])) $connectErrors[0] = acymailing_translation('ACY_DISTRIBUTION').' : '.$connectErrors[0];
						$this->messages = array_merge($this->messages, $connectErrors);
						$this->processed = true;
						$this->errorDetected = true;
					}

					$newAction = new stdClass();
					$newAction->action_id = $oneActionId;
					$newAction->report = $currentReport;
					$newAction->nextdate = time() + intval($currentAction->frequency);
					$actionClass->save($newAction);
				}
			}

			if(!empty($queueHelper->stoptime) AND time() > $queueHelper->stoptime) return true;
		}

		return true;
	}

	function report(){

		$config = acymailing_config();

		$sendreport = $config->get('cron_sendreport');
		$mailer = acymailing_get('helper.mailer');

		if(($sendreport == 2 && $this->processed) || $sendreport == 1 || ($sendreport == 3 && $this->errorDetected)){
			$mailer->report = false;
			$mailer->autoAddUser = true;
			$mailer->checkConfirmField = false;
			$mailer->addParam('report', implode('<br />', $this->messages));
			$mailer->addParam('mainreport', $this->mainmessage);
			$mailer->addParam('detailreport', implode('<br />', $this->detailMessages));
			$receiverString = $config->get('cron_sendto');
			$receivers = array();
			if(substr_count($receiverString, '@') > 1){
				$receivers = explode(' ', trim(preg_replace('# +#', ' ', str_replace(array(';', ','), ' ', $receiverString))));
			}else{
				$receivers[] = trim($receiverString);
			}
			if(!empty($receivers)){
				foreach($receivers as $oneReceiver){
					$mailer->sendOne('report', $oneReceiver);
				}
			}
		}

		if(!$this->executed) return;

		if($this->processed) $this->saveReport();

		$newConfig = new stdClass();
		$newConfig->cron_report = implode("\n", $this->messages);
		if(strlen($newConfig->cron_report) > 800) $newConfig->cron_report = substr($newConfig->cron_report, 0, 795).'...';
		$config->save($newConfig);
	}

	function saveReport(){
		$config = acymailing_config();
		$saveReport = $config->get('cron_savereport');
		if(empty($saveReport)) return;

		$reportPath = $config->get('cron_savepath');
		if(empty($reportPath)) return;

		$reportPath = str_replace(array('{year}', '{month}'), array(date('Y'), date('m')), $reportPath);

		$reportPath = acymailing_cleanPath(ACYMAILING_ROOT.trim(html_entity_decode($reportPath)));

		acymailing_createDir(dirname($reportPath), true, true);

		ob_start();
		file_put_contents($reportPath, "\r\n"."\r\n".str_repeat('*', 150)."\r\n".str_repeat('*', 20).str_repeat(' ', 5).acymailing_getDate(time()).str_repeat(' ', 5).str_repeat('*', 20)."\r\n".implode("\r\n", $this->messages), FILE_APPEND);
		if($saveReport == 2 AND !empty($this->detailMessages)){
			@file_put_contents($reportPath, "\r\n"."---- Details ----"."\r\n".implode("\r\n", $this->detailMessages), FILE_APPEND);
		}
		$potentialWarnings = ob_get_clean();

		if(!empty($potentialWarnings)) $this->messages[] = $potentialWarnings;
	}

	function cleanStats(){
		$config = acymailing_config();

		if($config->get('lastclean') > (time() - 518400)) return;

		$newConfig = new stdClass();
		$newConfig->lastclean = time();
		$config->save($newConfig);

		$detailedStatsFrequ = intval($config->get('delete_stats'));
		$historyFrequ = intval($config->get('delete_history'));
		$chartsFrequ = intval($config->get('delete_charts'));
		if(!empty($detailedStatsFrequ)){
			acymailing_query('DELETE FROM `#__acymailing_userstats` WHERE `senddate` <= '.(time() - $detailedStatsFrequ));

			acymailing_query('DELETE FROM `#__acymailing_urlclick` WHERE `date` <= '.(time() - $detailedStatsFrequ));
		}

		if(!empty($historyFrequ)){
			acymailing_query('DELETE FROM `#__acymailing_history` WHERE `action` != "confirmed" AND `action` != "subscribed" AND `action` != "unsubscribed" AND `action` != "removedsubscription" AND `date` <= '.(time() - $historyFrequ));
		}

		if(!empty($chartsFrequ)){
			$files = glob( ACYMAILING_MEDIA.'statistic_charts'.DS.'*.png' );
			if(!empty($files)) {
				foreach ($files as $oneFile) {
					if (filemtime($oneFile) < (time() - $chartsFrequ)) unlink($oneFile);
				}
			}
		}
		


		if((int)date('j') > 23){
			acymailing_query('OPTIMIZE TABLE `#__acymailing_history`');
		}elseif((int)date('j') > 16){
			acymailing_query('OPTIMIZE TABLE `#__acymailing_userstats`');
		}elseif((int)date('j') > 9){
			acymailing_query('OPTIMIZE TABLE `#__acymailing_urlclick`');
		}elseif((int)date('j') < 8){
			acymailing_query('OPTIMIZE TABLE `#__acymailing_queue`');
		}

		$message = array(acymailing_translation('ACY_CRON_CLEAN'));
		$this->detailMessages = array_merge($this->detailMessages, $message);
	}

}//endclass
