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

include(ACYMAILING_BACK.'controllers'.DS.'filter.php');

class FrontfilterController extends FilterController{

	function listing(){
		die('Access not allowed');
	}
	function process(){
		die('Access not allowed');
	}
	function store(){
		die('Access not allowed');
	}
}
?>
