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

acymailing_get('controller.list');

class CampaignController extends ListController{

	var $aclCat = 'campaign';

	function store(){
		if(!$this->isAllowed('campaign','manage')) return;
		acymailing_checkToken();

		$listClass = acymailing_get('class.list');
		$status = $listClass->saveForm();
		if($status){
			acymailing_enqueueMessage(acymailing_translation( 'JOOMEXT_SUCC_SAVED' ), 'message');
		}else{
			acymailing_enqueueMessage(acymailing_translation( 'ERROR_SAVING' ), 'error');
			if(!empty($listClass->errors)){
				foreach($listClass->errors as $oneError){
					acymailing_enqueueMessage($oneError, 'error');
				}
			}
		}
	}

	function copy(){
		if(!$this->isAllowed($this->aclCat,'manage')) return;
		acymailing_checkToken();

		$cids = acymailing_getVar('array',  'cid', array(), '');
		$time = time();

		$creatorId = intval(acymailing_currentUserId());
		foreach($cids as $oneListid){
			$query = 'INSERT INTO `#__acymailing_list` (`name`, `description` , `published` , `userid` , `alias` , `type` , `visible`,`startrule`) ';
			$query .= "SELECT CONCAT('copy_',`name`), `description` , 0 , ".$creatorId." , `alias` , `type` , `visible`,`startrule` FROM `#__acymailing_list` WHERE listid = ".intval($oneListid);
			acymailing_query($query);
			$newCampaignid = acymailing_insertID();

			if(empty($newCampaignid)) continue;

			acymailing_query('INSERT IGNORE INTO `#__acymailing_listcampaign` (`campaignid`,`listid`) SELECT '.intval($newCampaignid).', `listid` FROM `#__acymailing_listcampaign` WHERE `campaignid` = '.(int) $oneListid);

			$oldMailids = acymailing_loadObjectList('SELECT mailid FROM #__acymailing_listmail WHERE listid = '.intval($oneListid));
			$newMailids = array();
			foreach($oldMailids as $oneMailid){
				$query = 'INSERT INTO `#__acymailing_mail` (`subject`, `body`, `altbody`, `published`, `senddate`, `created`, `fromname`, `fromemail`, `replyname`, `replyemail`, `type`, `visible`, `userid`, `alias`, `attach`, `html`, `tempid`, `key`, `frequency`, `params`,`filter`,`metakey`,`metadesc`)';
				$query .= " SELECT `subject`, `body`, `altbody`, `published`, `senddate`, '.$time.', `fromname`, `fromemail`, `replyname`, `replyemail`, `type`, `visible`, '.$creatorId.', `alias`, `attach`, `html`, `tempid`, ".acymailing_escapeDB(md5(rand(1000,999999))).', `frequency`, `params`,`filter`,`metakey`,`metadesc` FROM `#__acymailing_mail` WHERE `mailid` = '.(int) $oneMailid->mailid;
				acymailing_query($query);
				$newMailids[] = acymailing_insertID();
			}

			if(!empty($newMailids)){
				acymailing_query('INSERT IGNORE INTO `#__acymailing_listmail` (`listid`,`mailid`) SELECT '.intval($newCampaignid).',`mailid` FROM `#__acymailing_mail` WHERE `mailid` IN ('.implode(',',$newMailids).')');
			}

		}

		return $this->listing();
	}

}
