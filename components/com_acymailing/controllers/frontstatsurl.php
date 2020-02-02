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

include(ACYMAILING_BACK.'controllers'.DS.'statsurl.php');

class FrontstatsurlController extends StatsurlController{

	function __construct($config = array()){
		parent::__construct($config);

		$task = acymailing_getVar('cmd', 'task');
		if(!in_array($task, array('', 'listing', 'detaillisting', 'exportglobal', 'export', 'edit', 'save', 'globalOverview'))) die(acymailing_translation('ACY_NOTALLOWED'));

	}
}
