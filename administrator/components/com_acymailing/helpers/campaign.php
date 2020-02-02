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

class acycampaignHelper{

	var $campaigndelay = 0;
	var $skipedfollowups = 0;

	function start($subid, $listids){

		$listCampaignClass = acymailing_get('class.listcampaign');
		$campaignids = $listCampaignClass->getAffectedCampaigns($listids);

		if(empty($campaignids)) return true;

		$campaignSubscription = acymailing_get('class.listsub');
		$campaignSubscription->type = 'campaign';
		$subscription = $campaignSubscription->getSubscription($subid);

		$campaignAdded = array();
		$time = time();
		foreach($campaignids as $id => $campaignid){
			if(!empty($subscription[$campaignid]) AND $subscription[$campaignid]->status == 1 AND $subscription[$campaignid]->unsubdate > $time){
				continue;
			}

			$campaignAdded[] = $campaignid;
		}

		if(empty($campaignAdded)) return true;

		$config = acymailing_config();

		$query = 'SELECT a.`listid`, max(b.`senddate`) as maxsenddate FROM '.acymailing_table('listmail').' as a JOIN '.acymailing_table('mail').' as b on a.`mailid` = b.`mailid`';
		$query .= ' WHERE a.`listid` IN ('.implode(',', $campaignAdded).') AND b.`published` = 1 GROUP BY a.listid';
		$maxunsubdate = acymailing_loadObjectList($query);

		if(empty($maxunsubdate)) return true;

		$allDelays = array();
		$currentDay = date('w');
		if($currentDay == 0) $currentDay = 7;
		if(empty($this->campaigndelay)){
			$allDelays = acymailing_loadObjectList('SELECT `startrule` , `listid` FROM #__acymailing_list WHERE `startrule` != "0" AND `startrule` != "'.intval($currentDay).'" AND `listid` IN ('.implode(',', $campaignAdded).')', 'listid');
		}

		$firstDelays = array();
		if(!empty($this->skipedfollowups)){
			foreach($maxunsubdate as $onecampaign){
				$firstDelays[$onecampaign->listid] = intval(acymailing_loadResult('SELECT b.`senddate` FROM '.acymailing_table('listmail').' AS a JOIN '.acymailing_table('mail').' AS b on a.`mailid` = b.`mailid` WHERE a.`listid` = '.intval($onecampaign->listid).' AND b.`published` = 1 ORDER BY b.`senddate` ASC LIMIT '.intval($this->skipedfollowups).',1'));
			}
		}

		$queryInsert = array();
		foreach($maxunsubdate as $onecampaign){
			$allDelays[$onecampaign->listid] = empty($allDelays[$onecampaign->listid]) ? $this->campaigndelay : ((($allDelays[$onecampaign->listid]->startrule - $currentDay + 7) % 7) * 60 * 60 * 24);
			$queryInsert[] = $onecampaign->listid.','.$subid.','.($time + $allDelays[$onecampaign->listid] - intval(@$firstDelays[$onecampaign->listid])).','.($time + $onecampaign->maxsenddate + $allDelays[$onecampaign->listid] - intval(@$firstDelays[$onecampaign->listid])).',1';
		}

		$query = 'REPLACE INTO '.acymailing_table('listsub').' (listid,subid,subdate,unsubdate,status) VALUES ('.implode('),(', $queryInsert).')';
		acymailing_query($query);

		$result = true;
		foreach($maxunsubdate as $onecampaign){

			$querySelect = 'SELECT '.$subid.',a.`mailid`,'.($time + $allDelays[$onecampaign->listid] - intval(@$firstDelays[$onecampaign->listid])).' + b.`senddate`,'.(int)$config->get('priority_followup', 2);
			$querySelect .= ' FROM '.acymailing_table('listmail').' AS a JOIN '.acymailing_table('mail').' AS b on a.`mailid` = b.`mailid`';
			$querySelect .= ' WHERE a.`listid` = '.$onecampaign->listid.' AND b.`published` = 1 ORDER BY b.`senddate` ASC';
			if(!empty($this->skipedfollowups)) $querySelect .= ' LIMIT '.intval($this->skipedfollowups).',10000';
			$query = 'INSERT IGNORE INTO '.acymailing_table('queue').' (`subid`,`mailid`,`senddate`,`priority`) '.$querySelect;

			$res = acymailing_query($query);
			$result = $res !== false && $result;
		}
		return $result;
	}

	private function addQueue($campaignId, &$subids, $followupToAdd){

		if(empty($subids) || empty($followupToAdd)) return;

		$query = 'SELECT sub.subid FROM `#__acymailing_subscriber` AS sub';
		$query .= ' LEFT JOIN `#__acymailing_listsub` AS campaign ON sub.subid=campaign.subid AND campaign.listid='.intval($campaignId);
		$query .= ' WHERE campaign.subid IS NULL AND sub.subid IN ('.implode(',', $subids).')';

		$listSubidOk = acymailing_loadResultArray($query);

		if(empty($listSubidOk)) return;

		$mailToAdd = '';
		$max = 0;
		foreach($listSubidOk as $oneSubId){
			foreach($followupToAdd as $oneFollow){
				$mailToAdd .= '('.intval($oneSubId).','.$oneFollow->mailid.','.$oneFollow->senddate.','.$oneFollow->priority.'),';
				if($oneFollow->senddate > $max) $max = $oneFollow->senddate;
			}
		}

		$query = 'INSERT IGNORE INTO `#__acymailing_listsub` (listid,subid,subdate,status,unsubdate) ';
		$query .= 'SELECT '.intval($campaignId).',subid,'.time().',1,'.$max.' FROM #__acymailing_subscriber WHERE subid IN ('.implode(',', $listSubidOk).')';
		acymailing_query($query);

		$mailToAdd = rtrim($mailToAdd, ',');
		$queryInsert = 'INSERT IGNORE INTO '.acymailing_table('queue').' (`subid`,`mailid`,`senddate`,`priority`) VALUES '.$mailToAdd;
		acymailing_query($queryInsert);
	}

	function autoSubCampaign(&$subids, $campaignId){
		$config = acymailing_config();
		$time = time();

		$querySelect = 'SELECT a.`mailid`,'.$time.' + b.`senddate` AS senddate,'.(int)$config->get('priority_followup', 2).' AS priority';
		$querySelect .= ' FROM '.acymailing_table('listmail').' AS a LEFT JOIN '.acymailing_table('mail').' AS b ON a.`mailid` = b.`mailid`';
		$querySelect .= ' WHERE a.`listid`='.$campaignId.' AND b.`published` = 1';
		$followupToAdd = acymailing_loadObjectList($querySelect);

		if(empty($followupToAdd)) return true;

		$users = array();
		foreach($subids as $subid){
			$users[] = $subid;
			if(count($users) == 50){
				$this->addQueue($campaignId, $users, $followupToAdd);
				$users = array();
			}
		}

		$this->addQueue($campaignId, $users, $followupToAdd);

		return true;
	}

	function stop($subid, $listids){
		$listCampaignClass = acymailing_get('class.listcampaign');
		$campaignids = $listCampaignClass->getAffectedCampaigns($listids);

		if(empty($campaignids)) return true;

		$selectquery = 'SELECT `mailid` FROM '.acymailing_table('listmail').' WHERE `listid` IN ('.implode(',', $campaignids).')';
		$query = 'DELETE FROM '.acymailing_table('queue').' WHERE `subid` = '.$subid.' AND `mailid` IN ('.$selectquery.')';

		acymailing_query($query);

		$time = time();
		return acymailing_query('UPDATE '.acymailing_table('listsub').' SET `unsubdate` = '.$time.', `status` = -1 WHERE `subid` = '.$subid.'. AND `listid` IN ('.implode(',', $campaignids).')');
	}

	function updateUnsubdate($campaignid, $newdelay){
		$campaignid = intval($campaignid);
		$newdelay = intval($newdelay);

		$query = 'UPDATE `#__acymailing_listsub` SET `unsubdate` = `subdate` + '.$newdelay.' WHERE listid = '.$campaignid.' AND `subdate` + '.$newdelay.' > `unsubdate` AND `status` = 1 AND `subdate` > '.(time() - $newdelay);
		acymailing_query($query);
	}

	function unsubCampaign(&$subids, $campaignId){
		acymailing_arrayToInteger($subids);

		acymailing_query('DELETE q.* FROM #__acymailing_queue AS q JOIN #__acymailing_listmail AS lm ON q.mailid = lm.mailid AND lm.listid = '.intval($campaignId).' WHERE q.subid IN ('.implode(',', $subids).')');
		acymailing_query('UPDATE #__acymailing_listsub SET status = -1, unsubdate = '.time().' WHERE subid IN ('.implode(',', $subids).') AND listid = '.intval($campaignId));
	}
}//endclass
