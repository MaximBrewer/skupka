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

class StatsurlController extends acymailingController{

	var $aclCat = 'statistics';

	function save(){
		if(!$this->isAllowed($this->aclCat, 'manage')) return;
		acymailing_checkToken();

		$class = acymailing_get('class.url');
		$status = $class->saveForm();
		if($status){
			acymailing_display(acymailing_translation('JOOMEXT_SUCC_SAVED'), 'success');
			return true;
		}else{
			acymailing_display(acymailing_translation('ERROR_SAVING'), 'success');
		}

		return $this->edit();
	}

	function detaillisting(){
		if(!$this->isAllowed($this->aclCat, 'manage') || !$this->isAllowed('subscriber', 'view')) return;
		acymailing_setVar('layout', 'detaillisting');
		return parent::display();
	}

	function export(){
		$selectedMail = acymailing_getVar('int', 'filter_mail', 0);
		$selectedUrl = acymailing_getVar('int', 'filter_url', 0);

		$filters = array();
		if(!empty($selectedMail)) $filters[] = 'urlclick.mailid = '.$selectedMail;
		if(!empty($selectedUrl)) $filters[] = 'urlclick.urlid = '.$selectedUrl;
		$query = 'FROM `#__acymailing_urlclick` as urlclick JOIN `#__acymailing_subscriber` as s ON s.subid = urlclick.subid JOIN `#__acymailing_url` as url ON url.urlid = urlclick.urlid';
		if(!empty($filters)) $query .= ' WHERE ('.implode(') AND (', $filters).')';

		acymailing_session();
		$_SESSION['acymailing']['acyexportquery'] = $query;

		acymailing_redirect(acymailing_completeLink((acymailing_isAdmin() ? '' : 'front').'data&task=export&sessionquery=1', acymailing_isNoTemplate(), true));
	}

	function exportglobal(){

		$pageInfo = new stdClass();
		$paramBase = ACYMAILING_COMPONENT.'.statsurllisting';
		$pageInfo->search = acymailing_getUserVar($paramBase.".search", 'search', '', 'string');
		$pageInfo->search = strtolower(trim($pageInfo->search));

		$filters = array();
		if(!empty($pageInfo->search)){
			$searchFields = array('m.subject', 'uc.mailid', 'uc.urlid', 'u.name', 'u.url', 'uc.click');
			$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search, true).'%\'';
			$filters[] = implode(" LIKE $searchVal OR ", $searchFields)." LIKE $searchVal";
		}

		$selectedMail = acymailing_getUserVar($paramBase."filter_mail", 'filter_mail', 0, 'int');
		if(!empty($selectedMail)) $filters[] = 'uc.mailid = '.$selectedMail;
		$selectedUrl = acymailing_getUserVar($paramBase."filter_url", 'filter_url', 0, 'int');
		if(!empty($selectedUrl)) $filters[] = 'uc.urlid = '.$selectedUrl;

		$query = 'SELECT m.mailid, m.subject, u.url, COUNT(uc.click) AS uniqueclick, SUM(uc.click) AS totalclick';
		$query .= ' FROM '.acymailing_table('urlclick').' AS uc';
		$query .= ' JOIN '.acymailing_table('mail').' AS m on uc.mailid = m.mailid';
		$query .= ' JOIN '.acymailing_table('url').' AS u on uc.urlid = u.urlid';
		if(!empty($filters)) $query .= ' WHERE ('.implode(') AND (', $filters).')';
		$query .= ' GROUP BY uc.mailid,uc.urlid';
		$query .= ' ORDER BY u.urlid DESC';

		$mydata = acymailing_loadObjectList($query);

		$exportHelper = acymailing_get('helper.export');
		$config = acymailing_config();
		$encodingClass = acymailing_get('helper.encoding');
		$exportHelper->addHeaders('globalClickStatistics_' . date('m_d_y'));

		$eol = "\r\n";
		$before = '"';
		$separator = '"'.str_replace(array('semicolon','comma'),array(';',','), $config->get('export_separator',';')).'"';
		$exportFormat = $config->get('export_format','UTF-8');
		$after = '"';

		$titles = array(acymailing_translation('ACY_ID'), acymailing_translation('JOOMEXT_SUBJECT'), acymailing_translation('URL'), acymailing_translation('UNIQUE_HITS'), acymailing_translation('TOTAL_HITS'));

		$titleLine = $before.implode($separator, $titles).$after.$eol;
		echo $titleLine;

		foreach($mydata as $cstats){
			$line = $cstats->mailid . $separator;
			$line .= $cstats->subject . $separator;
			$line .= $cstats->url . $separator;
			$line .= $cstats->uniqueclick . $separator;
			$line .= $cstats->totalclick . $separator;

			$line = $before.$encodingClass->change($line, 'UTF-8', $exportFormat).$after.$eol;
			echo $line;
		}

		exit;
	}

	function globalOverview(){
		if(!$this->isAllowed($this->aclCat, 'manage')) return;
		acymailing_setVar('layout', 'globalOverview');
		return parent::display();
	}
}
