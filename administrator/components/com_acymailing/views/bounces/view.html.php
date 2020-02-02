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

class BouncesViewBounces extends acymailingView{

	function display($tpl = null){
		$function = $this->getLayout();
		if(method_exists($this, $function)) $this->$function();

		parent::display($tpl);
	}

	function form(){
		$ruleid = acymailing_getCID('ruleid');
		$rulesClass = acymailing_get('class.rules');
		if(!empty($ruleid)){
			$rule = $rulesClass->get($ruleid);
		}else{
			$rule = new stdClass();
			$rule->published = 1;
		}

		$acyToolbar = acymailing_get('helper.toolbar');
		$acyToolbar->addButtonOption('apply', acymailing_translation('ACY_APPLY'), 'apply', false);
		$acyToolbar->save();
		$acyToolbar->cancel();
		$acyToolbar->divider();
		$acyToolbar->help('bounce', 'createrule');
		$acyToolbar->setTitle(acymailing_translation('ACY_RULE'), 'bounces&task=edit&ruleid='.$ruleid);
		$acyToolbar->display();

		$lists = acymailing_get('type.lists');
		$lists->getValues();
		array_shift($lists->values);
		$this->lists = $lists;
		$this->rule = $rule;
	}

	function listing(){


		$rulesClass = acymailing_get('class.rules');
		$rows = $rulesClass->getRules();
		$config = acymailing_config();
		$listClass = acymailing_get('class.list');
		$elements = new stdClass();
		$elements->bounce = acymailing_boolean("config[bounce]", '', $config->get('bounce', 0));

		$connections = array('imap' => 'IMAP', 'pop3' => 'POP3', 'pear' => 'POP3 (without imap extension)', 'nntp' => 'NNTP');

		$connecvals = array();
		foreach($connections as $code => $string){
			$connecvals[] = acymailing_selectOption($code, $string);
		}

		$elements->bounce_connection = acymailing_select($connecvals, 'config[bounce_connection]', 'size="1"', 'value', 'text', $config->get('bounce_connection', 'imap'));

		$securedVals = array();
		$securedVals[] = acymailing_selectOption('', '- - -');
		$securedVals[] = acymailing_selectOption('ssl', 'SSL');
		$securedVals[] = acymailing_selectOption('tls', 'TLS');

		$elements->bounce_secured = acymailing_select($securedVals, "config[bounce_secured]", 'size="1"', 'value', 'text', $config->get('bounce_secured'));
		$elements->bounce_certif = acymailing_boolean("config[bounce_certif]", '', $config->get('bounce_certif', 0));

		$js = "function displayBounceFrequency(newvalue){ if(newvalue == '1') {window.document.getElementById('bouncefrequency').style.display = 'block';}else{window.document.getElementById('bouncefrequency').style.display = 'none';}} ";
		$js .= 'window.addEventListener("load", function(){ displayBounceFrequency(\''.$config->get('auto_bounce', 0).'\');});';
		acymailing_addScript(true, $js);

		$acyToolbar = acymailing_get('helper.toolbar');
		$acyToolbar->custom('test', acymailing_translation('BOUNCE_PROCESS'), 'bounce', false);
		$onClickBounce = "if (confirm('".acymailing_translation('CONFIRM_REINSTALL_RULES')." ".acymailing_translation('PROCESS_CONFIRMATION')."')){acymailing.submitbutton('reinstall');}";
		$acyToolbar->custom('installbounces', acymailing_translation('REINSTALL_RULES'), 'generate', false, $onClickBounce);
		$acyToolbar->divider();
		$acyToolbar->custom('saveconfig', acymailing_translation('ACY_SAVE'), 'save', false);
		$acyToolbar->cancel();
		$acyToolbar->divider();
		$acyToolbar->help('bounce');
		$acyToolbar->setTitle(acymailing_translation('BOUNCE_HANDLING'), 'bounces');
		$acyToolbar->display();

		$updateClass = acymailing_get('helper.update');
		if($config->get('bouncerulesversion', 0) < $updateClass->bouncerulesversion){
			acymailing_display('<a href="'.acymailing_completeLink('bounces&task=reinstall').'" title="'.acymailing_translation('REINSTALL_RULES').'" >'.acymailing_translation('WANNA_REINSTALL_RULES').'</a>', 'warning');
		}

		$lists = $listClass->getLists('listid');
		$this->rows = $rows;
		$this->lists = $lists;
		$this->elements = $elements;
		$this->config = $config;
		$toggleClass = acymailing_get('helper.toggle');
		$this->toggleClass = $toggleClass;
	}

	function chart(){
		$mailid = acymailing_getVar('int', 'mailid');
		if(empty($mailid)) return;

		acymailing_addStyle(false, ACYMAILING_CSS.'acyprint.css', 'text/css', 'print');

		$data = acymailing_loadObject('SELECT bouncedetails FROM #__acymailing_stats WHERE mailid = '.intval($mailid));

		if(empty($data->bouncedetails)){
			acymailing_display("No data recorded for that Newsletter", 'warning');
			return;
		}

		$acyToolbar = acymailing_get('helper.toolbar');
		$acyToolbar->topfixed = false;
		$acyToolbar->link(acymailing_completeLink((acymailing_isAdmin() ? '' : 'front').'bounces&task=chart&export=1&mailid='.$mailid, true), acymailing_translation('ACY_EXPORT'), 'export');
		$acyToolbar->directPrint();
		$acyToolbar->setTitle(acymailing_translation('BOUNCE_HANDLING'));
		$acyToolbar->display();

		$data->bouncedetails = unserialize($data->bouncedetails);

		arsort($data->bouncedetails);
		acymailing_addScript(false, "https://www.google.com/jsapi");

		$this->data = $data;

		if(acymailing_getVar('cmd', 'export')){
			$exportHelper = acymailing_get('helper.export');
			$exportHelper->exportOneData($data->bouncedetails, 'bounce_'.acymailing_getVar('int', 'mailid'));
		}
	}
}
