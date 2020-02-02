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


class StatsurlViewStatsurl extends acymailingView{
	var $searchFields = array('b.subject', 'a.mailid', 'a.urlid', 'c.name', 'c.url', 'a.click');
	var $selectFields = array('b.subject', 'a.mailid', 'a.urlid', 'c.name', 'c.url', 'COUNT(a.click) as uniqueclick', 'SUM(a.click) as totalclick');
	var $detailSearchFields = array('b.subject', 'a.mailid', 'a.urlid', 'a.subid', 'c.name', 'c.url', 'd.name', 'd.email');
	var $detailSelectFields = array('d.*', 'a.*', 'b.subject', 'c.name as urlname', 'c.url');

	function display($tpl = null){
		$function = $this->getLayout();
		if(method_exists($this, $function)) $this->$function();

		parent::display($tpl);
	}

	function listing(){
		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$pageInfo->elements = new stdClass();

		$paramBase = ACYMAILING_COMPONENT . '.' . $this->getName() . $this->getLayout();
		$pageInfo->filter->order->value = acymailing_getUserVar($paramBase . ".filter_order", 'filter_order', '', 'cmd');
		$pageInfo->filter->order->dir = acymailing_getUserVar($paramBase . ".filter_order_Dir", 'filter_order_Dir', 'desc', 'word');
		if (strtolower($pageInfo->filter->order->dir) !== 'desc') $pageInfo->filter->order->dir = 'asc';
		$pageInfo->search = acymailing_getUserVar($paramBase . ".search", 'search', '', 'string');
		$pageInfo->search = strtolower(trim($pageInfo->search));
		$selectedMail = acymailing_getUserVar($paramBase . "filter_mail", 'filter_mail', 0, 'int');
		$selectedUrl = acymailing_getUserVar($paramBase . "filter_url", 'filter_url', 0, 'int');

		$pageInfo->limit->value = acymailing_getUserVar($paramBase . '.list_limit', 'limit', acymailing_getCMSConfig('list_limit'), 'int');
		$pageInfo->limit->start = acymailing_getUserVar($paramBase . '.limitstart', 'limitstart', 0, 'int');

		$filters = array();
		if (!empty($pageInfo->search)) {
			$searchVal = '\'%' . acymailing_getEscaped($pageInfo->search, true) . '%\'';
			$filters[] = implode(" LIKE $searchVal OR ", $this->searchFields) . " LIKE $searchVal";
		}

		if (!empty($selectedMail)) $filters[] = 'a.mailid = ' . $selectedMail;
		if (!empty($selectedUrl)) $filters[] = 'a.urlid = ' . $selectedUrl;

		$query = 'SELECT SQL_CALC_FOUND_ROWS ' . implode(' , ', $this->selectFields);
		$query .= ' FROM ' . acymailing_table('urlclick') . ' as a';
		$query .= ' JOIN ' . acymailing_table('mail') . ' as b on a.mailid = b.mailid';
		$query .= ' JOIN ' . acymailing_table('url') . ' as c on a.urlid = c.urlid';
		if (!empty($filters)) $query .= ' WHERE (' . implode(') AND (', $filters) . ')';
		$query .= ' GROUP BY a.mailid,a.urlid';
		if (!empty($pageInfo->filter->order->value)) {
			$query .= ' ORDER BY ' . $pageInfo->filter->order->value . ' ' . $pageInfo->filter->order->dir;
		}

		$rows = acymailing_loadObjectList($query, '', $pageInfo->limit->start, $pageInfo->limit->value);

		$pageInfo->elements->total = acymailing_loadResult('SELECT FOUND_ROWS()');
		$pageInfo->elements->page = count($rows);

		$pagination = new acyPagination($pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value);

		$filtersType = new stdClass();
		$mailType = acymailing_get('type.urlmail');
		$urlType = acymailing_get('type.url');
		$filtersType->mail = $mailType->display('filter_mail', $selectedMail);
		$filtersType->url = $urlType->display('filter_url', $selectedUrl);

		if(acymailing_isAdmin()){
			$acyToolbar = acymailing_get('helper.toolbar');
			if (acymailing_level(2)) $acyToolbar->link(acymailing_completeLink('statsurl&task=detaillisting&filter_mail=' . $selectedMail . '&filter_url=' . $selectedUrl), acymailing_translation('VIEW_DETAILS'), 'export');
			$acyToolbar->custom('exportglobal', acymailing_translation('ACY_EXPORT'), 'export', false, '');
			$acyToolbar->link(acymailing_completeLink('stats'), acymailing_translation('GLOBAL_STATISTICS'), 'cancel');
			$acyToolbar->divider();
			$acyToolbar->help('statsurl-listing');
			$acyToolbar->setTitle(acymailing_translation('CLICK_STATISTICS'), 'statsurl');
			$acyToolbar->display();
		}

		$config = acymailing_config();

		$this->selectedMail = $selectedMail;
		$this->selectedUrl = $selectedUrl;
		$this->config = $config;
		$this->filters = $filtersType;
		$this->rows = $rows;
		$this->pageInfo = $pageInfo;
		$this->pagination = $pagination;
	}

	function form(){
		$acyToolbar = acymailing_get('helper.toolbar');
		$acyToolbar->save();
		$acyToolbar->setTitle(acymailing_translation('URL'));
		$acyToolbar->topfixed = false;
		$acyToolbar->display();

		$urlid = acymailing_getCID('urlid');
		$urlClass = acymailing_get('class.url');
		$this->url = $urlClass->get($urlid);
	}

	function detaillisting(){
		acymailing_addStyle(false, ACYMAILING_CSS.'frontendedition.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'frontendedition.css'));
		$config = acymailing_config();

		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$pageInfo->elements = new stdClass();

		$paramBase = ACYMAILING_COMPONENT.'.'.$this->getName().$this->getLayout();
		$pageInfo->filter->order->value = acymailing_getUserVar($paramBase.".filter_order", 'filter_order', '', 'cmd');
		$pageInfo->filter->order->dir = acymailing_getUserVar($paramBase.".filter_order_Dir", 'filter_order_Dir', 'desc', 'word');
		if(strtolower($pageInfo->filter->order->dir) !== 'desc') $pageInfo->filter->order->dir = 'asc';
		$pageInfo->search = acymailing_getUserVar($paramBase.".search", 'search', '', 'string');
		$pageInfo->search = strtolower(trim($pageInfo->search));
		$selectedMail = acymailing_getUserVar($paramBase."filter_mail", 'filter_mail', 0, 'int');
		$selectedUrl = acymailing_getUserVar($paramBase."filter_url", 'filter_url', 0, 'int');

		$pageInfo->limit->value = acymailing_getUserVar($paramBase.'.list_limit', 'limit', acymailing_getCMSConfig('list_limit'), 'int');
		$pageInfo->limit->start = acymailing_getUserVar($paramBase.'.limitstart', 'limitstart', 0, 'int');

		$filters = array();
		if(!empty($pageInfo->search)){
			$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search, true).'%\'';
			$filters[] = implode(" LIKE $searchVal OR ", $this->detailSearchFields)." LIKE $searchVal";
		}

		if(!empty($selectedMail)) $filters[] = 'a.mailid = '.$selectedMail;
		if(!empty($selectedUrl)) $filters[] = 'a.urlid = '.$selectedUrl;

		$query = 'SELECT '.implode(' , ', $this->detailSelectFields);
		$query .= ' FROM '.acymailing_table('urlclick').' as a';
		$query .= ' JOIN '.acymailing_table('mail').' as b on a.mailid = b.mailid';
		$query .= ' JOIN '.acymailing_table('url').' as c on a.urlid = c.urlid';
		$query .= ' JOIN '.acymailing_table('subscriber').' as d on a.subid = d.subid';
		if(!empty($filters)) $query .= ' WHERE ('.implode(') AND (', $filters).')';
		if(!empty($pageInfo->filter->order->value)){
			$query .= ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}

		$rows = acymailing_loadObjectList($query, '', $pageInfo->limit->start, $pageInfo->limit->value);

		$countQuery = 'SELECT COUNT(a.subid) FROM #__acymailing_urlclick as a';
		if(!empty($pageInfo->search)){
			$countQuery .= ' JOIN '.acymailing_table('mail').' as b on a.mailid = b.mailid';
			$countQuery .= ' JOIN '.acymailing_table('url').' as c on a.urlid = c.urlid';
			$countQuery .= ' JOIN '.acymailing_table('subscriber').' as d on a.subid = d.subid';
		}
		if(!empty($filters)) $countQuery .= ' WHERE ('.implode(') AND (', $filters).')';

		$pageInfo->elements->total = acymailing_loadResult($countQuery);
		$pageInfo->elements->page = count($rows);

		$pagination = new acyPagination($pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value);

		$filtersType = new stdClass();
		$mailType = acymailing_get('type.urlmail');
		$urlType = acymailing_get('type.url');
		$filtersType->mail = $mailType->display('filter_mail', $selectedMail);
		$filtersType->url = $urlType->display('filter_url', $selectedUrl);

		if(acymailing_isAdmin()){
			$acyToolbar = acymailing_get('helper.toolbar');
			$acyToolbar->custom('export', acymailing_translation('ACY_EXPORT'), 'export', false, '');
			if(acymailing_isNoTemplate()){
				$acyToolbar->topfixed = false;
				$acyToolbar->custom('', acymailing_translation('ACY_CANCEL'), 'cancel', false, 'location.href=\''.acymailing_completeLink('diagram&task=mailing&mailid='.acymailing_getVar('int', 'filter_mail'), true).'\';');
			}else{
				$acyToolbar->custom('cancel', acymailing_translation('ACY_CANCEL'), 'cancel', false, '');
			}
			$acyToolbar->setTitle(acymailing_translation('CLICK_STATISTICS'));
			$acyToolbar->display();
		}

		if(acymailing_isNoTemplate()){
			$filtersType->mail = '<input type="hidden" value="'.acymailing_getVar('int', 'mailid').'" name="mailid" />';
			$filtersType->mail .= '<input type="hidden" value="'.acymailing_getVar('int', 'filter_mail').'" name="filter_mail" />';
		}
		
		$this->filters = $filtersType;
		$this->rows = $rows;
		$this->pageInfo = $pageInfo;
		$this->pagination = $pagination;
		$this->config = $config;
	}

	function globalOverview(){
		$mailid = acymailing_getVar('int', 'filter_mail');

		if(acymailing_isAdmin() && !acymailing_isNoTemplate()){
			$acyToolbar = acymailing_get('helper.toolbar');
			$acyToolbar->link(acymailing_completeLink('statsurl'), acymailing_translation('ACY_CANCEL'), 'cancel');
			$acyToolbar->help('statsurl-overview');
			$acyToolbar->setTitle(acymailing_translation('CLICK_STATISTICS'), 'statsurl&task=globalOverview&filter_mail='.$mailid);
			$acyToolbar->display();
		}

		$mailerHelper = acymailing_get('helper.mailer');
		$mailerHelper->loadedToSend = false;
		$mail = $mailerHelper->load($mailid);

		if(empty($mail)) return;

		preg_match('@href="{unsubscribe:(.*)}"@', $mail->body, $match);//we get the tag unsubscribe
		if(!empty($match)){
			$mail->body = str_replace($match[0], 'href="'.$match[1].'"', $mail->body);
		}

		$userClass = acymailing_get('class.subscriber');
		$receiver = $userClass->get(acymailing_currentUserEmail());
		$mail->sendHTML = true;
		acymailing_trigger('acymailing_replaceusertags', array(&$mail, &$receiver, false));
		if(empty($mail->html)) $mail->body = $mailerHelper->textVersion($mail->altbody, false);

		$query = 'SELECT SQL_CALC_FOUND_ROWS uc.urlid, u.name, u.url, COUNT(uc.click) AS uniqueclick, SUM(uc.click) AS totalclick';
		$query .= ' FROM '.acymailing_table('urlclick').' AS uc';
		$query .= ' JOIN '.acymailing_table('url').' AS u ON uc.urlid = u.urlid';
		$query .= ' WHERE uc.mailid = '.intval($mailid);
		$query .= ' GROUP BY uc.mailid, uc.urlid';
		$query .= ' ORDER BY totalclick DESC';
		$stats = acymailing_loadObjectList($query);

		$total = acymailing_loadObject('SELECT COUNT(click) AS uniqueclick, SUM(click) AS totalclick FROM '.acymailing_table('urlclick').' WHERE mailid = '.intval($mailid));

		if(!empty($stats)){
			preg_match_all('~<a [^>]*href="([^"]*)"~Uis', $mail->body, $allLinks);
			foreach($allLinks[1] as $oneLink){
				$mail->body = str_replace('href="'.$oneLink.'"', 'href="'.$this->getUrlTrackedForm($oneLink).'"', $mail->body);
			}

			$maxClick = $stats[0]->totalclick;
			$minClick = $stats[count($stats)-1]->totalclick;
			foreach ($stats as $oneLink) {
				$percentage = intval($oneLink->totalclick / $total->totalclick*100);
				$red = $maxClick == $minClick ? 255 : intval((($oneLink->totalclick-$minClick)/($maxClick-$minClick))*255);
				$blue = intval(255-$red);

				$content = acymailing_tooltip(acymailing_translation_sprintf('ACY_CLICKS_DETAILS', $oneLink->totalclick, $total->totalclick), $percentage.'%', '', $percentage.'<span style="font-size: 7px;">%</span>');
				$bubble = '<span class="overviewbubble" style="display:none;background-color:rgba(' . $red . ',0,' . $blue . ',0.5);">'.$content.'</span>';

				if(strpos($mail->body, '&ctrl=url&urlid='.$oneLink->urlid) === false) {
					$mail->body = preg_replace('#(<a [^>]*href="'.preg_quote($oneLink->url, '#').'"[^>]*>)#Uis', '$1'.$bubble, $mail->body);
				}else{
					$mail->body = preg_replace('#(<a [^>]*href="[^"]*&ctrl=url&urlid='.$oneLink->urlid.'[^"]*"[^>]*>)#Uis', '$1'.$bubble, $mail->body);
				}
				$mail->body = preg_replace('#(<a [^>]*(?!target=\")[^>]*)>#Uis', '$1 target="_blank">', $mail->body);
			}
		}

		$templateClass = acymailing_get('class.template');
		if(!empty($mail->tempid)) $templateClass->createTemplateFile($mail->tempid);
		$styles = array(
			'bubble.css',
			'acytooltip.css'
		);
		$scripts = array(
			'acymailing.js',
			'var tooltips = document.querySelectorAll(".acymailingtooltip");for (var i = 0; i < tooltips.length; i++) {tooltips[i].addEventListener("mouseover", function (event) {var tooltiptext = this.getElementsByClassName("acymailingtooltiptext")[0];if(this.parentElement.className == "overviewbubble") {tooltiptext.style.width = "140px";tooltiptext.style.top = "-50px";tooltiptext.style.left = "-65px";}else{var newTop = event.clientY - tooltiptext.clientHeight - 5;if(newTop < 0) newTop = 0;var newleft = event.clientX - tooltiptext.clientWidth/2;if(newleft < 0) newleft = 0;tooltiptext.style.top = newTop + "px";tooltiptext.style.left = newleft + "px";}});}'
		);
		$templateClass->displayPreview('clicks_overview', $mail->tempid, $mail->subject, $styles, $scripts);

		$this->mail = $mail;
		$this->stats = $stats;
	}

	function getUrlTrackedForm($currentURL){
		$currentURL = str_replace('&amp;', '&', $currentURL);

		$removeParams = array();
		$removeParams[''] = 'subid|key|acm|utm_source|utm_medium|utm_campaign|token|user\[.{1,10}\]|user_var[0-9]*';
		$removeParams['passw'] = 'passw|user';
		$removeParams['activation'] = 'activation|id|infos';

		foreach($removeParams as $needed => $val){
			if(!empty($needed) && strpos($currentURL,$needed) === false) continue;
			$currentURL = preg_replace('#(\?|&|\/)('.$val.')[=:-][^&\/]*#i','',$currentURL);
		}

		if(!strpos($currentURL,'?') && strpos($currentURL,'&')){
			$firstpos = strpos($currentURL,'&');
			$currentURL = substr($currentURL,0,$firstpos).'?'.substr($currentURL,$firstpos+1);
		}

		return $currentURL;
	}
}
