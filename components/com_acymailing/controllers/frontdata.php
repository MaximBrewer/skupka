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

include(ACYMAILING_BACK.'controllers'.DS.'data.php');

class FrontdataController extends DataController{

	function __construct($config = array())
	{
		parent::__construct($config);

		$listid = acymailing_getVar('int', 'listid');
		if(empty($listid)){
			$listid = acymailing_getVar('int', 'filter_lists');
		}
		if(empty($listid)){
			$listClass = acymailing_get('class.list');
			$allAllowedLists = $listClass->getFrontendLists();
			if(!empty($allAllowedLists)){
				$firstList = reset($allAllowedLists);
				$listid = $firstList->listid;
			}
		}

		acymailing_setVar('filter_lists',$listid);
		acymailing_setVar('listid',$listid);

		if(!acymailing_accessList()){
			acymailing_enqueueMessage('You can not have access to this list','error');
			acymailing_redirect('index.php?option=com_acymailing');
			return false;
		}

	}

}
