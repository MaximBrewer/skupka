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


class CampaignViewCampaign extends acymailingView{
	
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
		$config = acymailing_config();
		
		$paramBase = ACYMAILING_COMPONENT.'.'.$this->getName();
		$pageInfo->filter->order->value = acymailing_getUserVar($paramBase.".filter_order", 'filter_order', 'a.listid', 'cmd');
		$pageInfo->filter->order->dir = acymailing_getUserVar($paramBase.".filter_order_Dir", 'filter_order_Dir', 'desc', 'word');
		if(strtolower($pageInfo->filter->order->dir) !== 'desc') $pageInfo->filter->order->dir = 'asc';
		$pageInfo->search = acymailing_getUserVar($paramBase.".search", 'search', '', 'string');
		$pageInfo->search = strtolower(trim($pageInfo->search));
		$selectedCreator = acymailing_getUserVar($paramBase."filter_creator", 'filter_creator', 0, 'int');

		$pageInfo->limit->value = acymailing_getUserVar($paramBase.'.list_limit', 'limit', acymailing_getCMSConfig('list_limit'), 'int');
		$pageInfo->limit->start = acymailing_getUserVar($paramBase.'.limitstart', 'limitstart', 0, 'int');

		$filters = array();
		if(!empty($pageInfo->search)){
			$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search).'%\'';
			$filters[] = "a.name LIKE $searchVal OR a.description LIKE $searchVal OR a.listid LIKE $searchVal";
		}
		$filters[] = 'a.type = \'campaign\'';
		if(!empty($selectedCreator)) $filters[] = 'a.userid = '.$selectedCreator;

		$query = 'SELECT a.*, d.'.$this->cmsUserVars->name.' as creatorname, d.'.$this->cmsUserVars->username.' AS username, d.'.$this->cmsUserVars->email.' AS email';
		$query .= ' FROM '.acymailing_table('list').' as a';
		$query .= ' LEFT JOIN '.acymailing_table($this->cmsUserVars->table, false).' as d on a.userid = d.'.$this->cmsUserVars->id;
		$query .= ' WHERE ('.implode(') AND (', $filters).') ';
		if(!empty($pageInfo->filter->order->value)){
			$query .= ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}

		$rows = acymailing_loadObjectList($query, '', $pageInfo->limit->start, $pageInfo->limit->value);

		$queryCount = 'SELECT COUNT(a.listid) FROM '.acymailing_table('list').' as a';
		if(count($filters) > 1) $queryCount .= ' LEFT JOIN '.acymailing_table($this->cmsUserVars->table, false).' as d on a.userid = d.'.$this->cmsUserVars->id;
		$queryCount .= ' WHERE ('.implode(') AND (', $filters).') ';

		$pageInfo->elements->total = acymailing_loadResult($queryCount);

		$pageInfo->elements->page = count($rows);

		$followupClass = acymailing_get('class.listmail');
		if(!empty($rows)){
			foreach($rows as $id => $onerow){
				$rows[$id]->followup = $followupClass->getFollowup($onerow->listid);
			}
		}

		$pagination = new acyPagination($pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value);

		$acyToolbar = acymailing_get('helper.toolbar');
		$acyToolbar->add();
		$acyToolbar->edit();
		if(acymailing_isAllowed($config->get('acl_campaign_copy', 'all'))) $acyToolbar->copy();
		if(acymailing_isAllowed($config->get('acl_campaign_delete', 'all'))) $acyToolbar->delete();
		$acyToolbar->divider();
		$acyToolbar->help('campaign');
		$acyToolbar->setTitle(acymailing_translation('CAMPAIGN'), 'campaign');
		$acyToolbar->display();


		$toggleClass = acymailing_get('helper.toggle');
		$this->rows = $rows;
		$this->pageInfo = $pageInfo;
		$this->pagination = $pagination;
		$this->toggleClass = $toggleClass;
		$delay = acymailing_get('type.delaydisp');
		$this->delay = $delay;
		$this->config = $config;

		$toggleClass->toggleText();
	}

	function form(){
		$listid = acymailing_getCID('listid');

		$listClass = acymailing_get('class.list');
		if(!empty($listid)){
			$list = $listClass->get($listid);
			$followupClass = acymailing_get('class.listmail');
			$followup = $followupClass->getFollowup($listid);
		}else{
			$list = new stdClass();
			$list->published = 1;
			$list->visible = 0;
			$list->description = '';
			$list->creatorname = acymailing_currentUserName();
			$list->listid = 0;
			$list->startrule = 0;
			$followup = array();
		}

		$editor = acymailing_get('helper.editor');
		$editor->name = 'editor_description';
		$editor->content = $list->description;
		$editor->setDescription();

		$listCampaign = acymailing_get('class.listcampaign');
		$lists = $listCampaign->getLists($listid);

		$script = '
			document.addEventListener("DOMContentLoaded", function(){
				acymailing.submitbutton = function(pressbutton) {
					if (pressbutton == \'cancel\') {
						acymailing.submitform(pressbutton,document.adminForm);
						return;
					}
					
					if(window.document.getElementById("name").value.length < 1){alert(\''.acymailing_translation('ENTER_TITLE', true).'\'); return false;}';
		$script .= $editor->jsCode();
		$script .= 'acymailing.submitform(pressbutton,document.adminForm);
				};
			}); ';

		acymailing_addScript(true, $script);


		$acyToolbar = acymailing_get('helper.toolbar');
		$acyToolbar->addButtonOption('apply', acymailing_translation('ACY_APPLY'), 'apply', false);
		$acyToolbar->save();
		$acyToolbar->cancel();
		$acyToolbar->divider();
		$acyToolbar->help('campaign');
		$acyToolbar->setTitle(acymailing_translation('CAMPAIGN'), 'campaign&task=edit&listid='.$listid);
		$acyToolbar->display();

		$startoptions = array();
		$startoptions[] = acymailing_selectOption("0", acymailing_translation('START_ON_SUBSCRIBE'));
		$days = array(acymailing_translation('MONDAY'), acymailing_translation('TUESDAY'), acymailing_translation('WEDNESDAY'), acymailing_translation('THURSDAY'), acymailing_translation('FRIDAY'), acymailing_translation('SATURDAY'), acymailing_translation('SUNDAY'));
		foreach($days as $i => $oneDay){
			$startoptions[] = acymailing_selectOption($i + 1, acymailing_translation_sprintf('START_ON_DAY', $oneDay));
		}

		$toggleClass = acymailing_get('helper.toggle');
		$this->startoptions = $startoptions;
		$this->toggleClass = $toggleClass;
		$this->followup = $followup;
		$this->lists = $lists;
		$this->list = $list;
		$this->editor = $editor;
	}
}
