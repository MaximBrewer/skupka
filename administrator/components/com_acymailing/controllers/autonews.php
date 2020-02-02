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

acymailing_get('controller.newsletter');
class AutonewsController extends NewsletterController{
	var $aclCat = 'autonewsletters';
	function generate(){
		if(!$this->isAllowed('autonewsletters','manage')) return;
		acymailing_checkToken();

		$cids = acymailing_getVar('array', 'cid', array(), '');
		$autoNewsHelper = acymailing_get('helper.autonews');
		if(!$autoNewsHelper->generate($cids)){
			acymailing_enqueueMessage(acymailing_translation('NO_AUTONEWSLETTERS'),'notice');
			$query = "SELECT * FROM ".acymailing_table('mail')." WHERE `type` = 'autonews'";
			if(!empty($cids)){
				acymailing_arrayToInteger($cids);
				$query .= ' AND mailid IN ('.implode(',', $cids).')';
			}
			$allAutonews = acymailing_loadObjectList($query);

			if(!empty($allAutonews)){
				$time = time();
				foreach($allAutonews as $oneAutonews){
					if(($oneAutonews->published != 1)){
						acymailing_enqueueMessage(acymailing_translation_sprintf('AUTONEWS_NOT_PUBLISHED','<b><i>'.$oneAutonews->subject.'</i></b>'),'notice');
					}elseif($oneAutonews->senddate >= $time){
						acymailing_enqueueMessage(acymailing_translation_sprintf('AUTONEWS_NOT_READY','<b><i>'.$oneAutonews->subject.'</i></b>'),'notice');
					}
				}
			}
		}else{
			foreach($autoNewsHelper->messages as $oneMessage){
				acymailing_enqueueMessage($oneMessage);
			}
		}

		return $this->listing();
	}
}
