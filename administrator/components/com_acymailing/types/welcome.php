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

class welcomeType extends acymailingClass{
	function __construct(){
		parent::__construct();

		$query = 'SELECT `subject`, `mailid` FROM '.acymailing_table('mail').' WHERE `type`= \'welcome\'';
		$messages = acymailing_loadObjectList($query);

		$this->values = array();
		$this->values[] = acymailing_selectOption('0', acymailing_translation('NO_WELCOME_MESSAGE'));
		$this->values[] = acymailing_selectOption('-1', acymailing_translation('LATEST_NEWSLETTER'));
		foreach($messages as $oneMessage){
			$this->values[] = acymailing_selectOption($oneMessage->mailid, '['.acymailing_translation('ACY_ID').' '.$oneMessage->mailid.'] '.$oneMessage->subject);
		}
	}

	function display($value){
		$linkEdit = acymailing_completeLink((acymailing_isAdmin() ? '' : 'front').'email', true).'&amp;task=edit&amp;type=welcome&amp;mailid='.$value;
		$linkAdd = acymailing_completeLink((acymailing_isAdmin() ? '' : 'front').'email', true).'&amp;task=add&amp;type=welcome';
		$style = empty($value) ? 'style="display:none!important;"' : '';
		$text = acymailing_popup($linkEdit, '<img src="'.ACYMAILING_IMAGES.'icons/icon-16-edit.png" alt="'.acymailing_translation('ACY_EDIT',true).'"/>', '', 0, 500, 'welcome_edit', $style);
		$text .= acymailing_popup($linkAdd, '<img src="'.ACYMAILING_IMAGES.'icons/icon-16-add.png" alt="'.acymailing_translation('CREATE_EMAIL',true).'"/>', '', 0, 500, 'welcome_add');

		return acymailing_select(  $this->values, 'data[list][welmailid]', 'size="1" onchange="changeMessage(\'welcome\',this.value);"', 'value', 'text', (int) $value ).$text;
	}
}
