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

class urlmailType extends acymailingClass{

	function __construct(){
		parent::__construct();
		$query = 'SELECT b.subject,b.mailid,count(distinct a.urlid) as totalmail FROM '.acymailing_table('urlclick').' as a';
		$query .= ' JOIN '.acymailing_table('mail').' as b ON a.mailid = b.mailid';
		$query .= ' GROUP BY a.mailid ORDER BY a.mailid DESC';
		$mails = acymailing_loadObjectList($query);

		$this->values = array();
		$this->values[] = acymailing_selectOption('0', acymailing_translation('ALL_EMAILS'));
		foreach($mails as $oneMail){
			if(!empty($oneMail->subject)) $oneMail->subject = acyEmoji::Decode($oneMail->subject);
			$this->values[] = acymailing_selectOption($oneMail->mailid, $oneMail->subject.' ( '.$oneMail->totalmail.' )' );
		}
	}

	function display($map,$value){
		return acymailing_select(  $this->values, $map, ' size="1" onchange="document.adminForm.submit( );" style="max-width:200px;"', 'value', 'text', (int) $value );
	}
}
