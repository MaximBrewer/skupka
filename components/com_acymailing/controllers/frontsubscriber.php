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
if(!acymailing_isAllowed($config->get('acl_subscriber_manage', 'all'))) die(acymailing_translation('ACY_NOTALLOWED'));

include(ACYMAILING_BACK.'controllers'.DS.'subscriber.php');

class FrontsubscriberController extends SubscriberController{

	function __construct($config = array()){
		parent::__construct($config);

		$this->allowedLists = array();
		$listid = acymailing_getVar('int', 'listid');

		if(empty($listid)) $listid = acymailing_getVar('int', 'filter_lists');
		if(empty($listid)) $listid = acymailing_getUserVar("com_acymailing.frontsubscriberfilter_lists", 'frontsubscriberfilter_lists');
		if(empty($listid)){
			$listClass = acymailing_get('class.list');
			$allAllowedLists = $listClass->getFrontendLists('listid');
			if(!empty($allAllowedLists)){
				$this->allowedLists = array_keys($allAllowedLists);
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

		if(in_array(acymailing_getVar('cmd', 'task'), array('edit', 'add')) && !$this->acyCheckEditUser()){
			acymailing_enqueueMessage('This user does not belong to your list', 'error');
			acymailing_redirect('index.php?option=com_acymailing');
			return false;
		}
	}

	function acyCheckEditUser(){
		$listid = acymailing_getVar('int', 'listid');
		$subid = acymailing_getCID('subid');

		if(empty($subid)) return true;

		$query = 'SELECT status FROM #__acymailing_listsub WHERE subid = '.intval($subid);
		if(empty($this->allowedLists)){
			$query .= ' AND listid = '.intval($listid);
		}else{
			acymailing_arrayToInteger($this->allowedLists);
			$query .= ' AND listid IN ('.implode(',', $this->allowedLists).')';
		}

		$status = acymailing_loadResult($query);
		if(empty($status)) return false;

		return true;
	}

	function addqueue(){
		if(!$this->isAllowed('newsletters', 'schedule')) return;
		acymailing_setVar('layout', 'addqueue');
		return parent::display();
	}

	function scheduleone(){
		$sendController = acymailing_get('controller.send');
		$sendController->scheduleone();
	}
}
