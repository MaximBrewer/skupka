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
if(!acymailing_isAllowed($config->get('acl_statistics_manage', 'all'))) die(acymailing_translation('ACY_NOTALLOWED'));

include(ACYMAILING_BACK.'controllers'.DS.'stats.php');

class FrontstatsController extends StatsController{
	function __construct($config = array()){
		parent::__construct($config);

		$task = acymailing_getVar('cmd', 'task');
		if(!in_array($task, array('', 'opendays', 'listing', 'detaillisting', 'unsubchart', 'unsubscribed', 'export', 'exportUnsubscribed', 'exportglobal', 'remove'))) die(acymailing_translation('ACY_NOTALLOWED'));

		$listid = acymailing_getVar('int', 'listid');
		if(empty($listid)){
			$listid = acymailing_getVar('int', 'filter_msg');
		}
		if(empty($listid)){
			$listid = acymailing_getUserVar("com_acymailing.frontstatsfilter_msg", 'frontstatsfilter_msg');
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
	}
}
