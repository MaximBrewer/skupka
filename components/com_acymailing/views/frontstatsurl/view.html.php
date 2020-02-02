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
include(ACYMAILING_BACK.'views'.DS.'statsurl'.DS.'view.html.php');

class FrontstatsurlViewFrontstatsurl extends StatsurlViewStatsurl{

	var $ctrl='frontstatsurl';

	function display($tpl = null){
		global $Itemid;
		$this->Itemid = $Itemid;
		parent::display($tpl);
	}
}
