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

class urlType extends acymailingClass{

	function __construct(){
		parent::__construct();
		$selectedMail = acymailing_getVar('int', 'filter_mail');
		if(!empty($selectedMail)){
			$query = 'SELECT DISTINCT a.name,a.urlid FROM '.acymailing_table('urlclick').' as b JOIN '.acymailing_table('url').' as a on a.urlid = b.urlid WHERE b.mailid = '.$selectedMail.' ORDER BY a.name LIMIT 300';
		}else{
			$query = 'SELECT DISTINCT a.name,a.urlid FROM '.acymailing_table('urlclick').' as b JOIN '.acymailing_table('url').' as a on a.urlid = b.urlid ORDER BY a.name LIMIT 300';
		}

		$urls = acymailing_loadObjectList($query);

		$this->values = array();
		$this->values[] = acymailing_selectOption('0', acymailing_translation('ALL_URLS'));
		foreach($urls as $onrUrl){
			if(strlen($onrUrl->name)>55) $onrUrl->name = substr($onrUrl->name,0,20).'...'.substr($onrUrl->name,-30);
			$this->values[] = acymailing_selectOption($onrUrl->urlid, $onrUrl->name );
		}
	}

	function display($map,$value){
		return acymailing_select(  $this->values, $map, ' size="1" onchange="document.adminForm.submit( );" style="max-width:200px;"', 'value', 'text', (int) $value );
	}
}
