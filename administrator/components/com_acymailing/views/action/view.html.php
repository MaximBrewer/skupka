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


class ActionViewAction extends acymailingView{
	
	function display($tpl = null){
		$function = $this->getLayout();
		if(method_exists($this, $function)) $this->$function();

		parent::display($tpl);
	}

	function listing(){
		$config = acymailing_config();
		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$pageInfo->elements = new stdClass();

		$paramBase = ACYMAILING_COMPONENT.'.'.$this->getName();

		$pageInfo->filter->order->value = acymailing_getUserVar($paramBase.".filter_order", 'filter_order', 'a.ordering', 'cmd');
		$pageInfo->filter->order->dir = acymailing_getUserVar($paramBase.".filter_order_Dir", 'filter_order_Dir', 'asc', 'word');
		if(strtolower($pageInfo->filter->order->dir) !== 'desc') $pageInfo->filter->order->dir = 'asc';
		$pageInfo->search = acymailing_getUserVar($paramBase.".search", 'search', '', 'string');
		$pageInfo->search = strtolower(trim($pageInfo->search));
		$selectedCreator = acymailing_getUserVar($paramBase."filter_creator", 'filter_creator', 0, 'int');

		$pageInfo->limit->value = acymailing_getUserVar($paramBase.'.list_limit', 'limit', acymailing_getCMSConfig('list_limit'), 'int');
		$pageInfo->limit->start = acymailing_getUserVar($paramBase.'.limitstart', 'limitstart', 0, 'int');

		$filters = array();
		if(!empty($pageInfo->search)){
			$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search, true).'%\'';
			$filters[] = "a.name LIKE $searchVal OR a.description LIKE $searchVal OR a.action_id LIKE $searchVal OR a.username LIKE $searchVal OR d.'.$this->cmsUserVars->name.' LIKE $searchVal";
		}
		if(!empty($selectedCreator)) $filters[] = 'a.userid = '.$selectedCreator;

		$query = 'SELECT a.*, d.'.$this->cmsUserVars->name.' AS creatorname, d.'.$this->cmsUserVars->username.' AS creatorusername, d.'.$this->cmsUserVars->email.' AS email';
		$query .= ' FROM '.acymailing_table('action').' AS a';
		$query .= ' LEFT JOIN '.acymailing_table($this->cmsUserVars->table, false).' AS d ON a.userid = d.'.$this->cmsUserVars->id;
		if(!empty($filters)) $query .= ' WHERE ('.implode(') AND (', $filters).')';
		if(!empty($pageInfo->filter->order->value)){
			$query .= ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}

		$rows = acymailing_loadObjectList($query, '', $pageInfo->limit->start, $pageInfo->limit->value);
		if(!empty($pageInfo->search)){
			$rows = acymailing_search($pageInfo->search, $rows);
		}

		$queryCount = 'SELECT COUNT(a.action_id) FROM '.acymailing_table('action').' AS a';
		if(!empty($pageInfo->search)) $queryCount .= ' LEFT JOIN '.acymailing_table($this->cmsUserVars->table, false).' AS d ON a.userid = d.'.$this->cmsUserVars->id;
		if(!empty($filters)) $queryCount .= ' WHERE ('.implode(') AND (', $filters).')';

		$pageInfo->elements->total = acymailing_loadResult($queryCount);

		$actionids = array();
		foreach($rows as $oneRow){
			$actionids[] = $oneRow->action_id;
		}

		$pageInfo->elements->page = count($rows);

		$pagination = new acyPagination($pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value);

		$acyToolbar = acymailing_get('helper.toolbar');
		if(acymailing_isAllowed($config->get('acl_distribution_manage', 'all'))) $acyToolbar->add();
		if(acymailing_isAllowed($config->get('acl_distribution_manage', 'all'))) $acyToolbar->edit();
		if(acymailing_isAllowed($config->get('acl_distribution_copy', 'all'))) $acyToolbar->copy();
		if(acymailing_isAllowed($config->get('acl_distribution_delete', 'all'))) $acyToolbar->delete();
		if(acymailing_isAllowed($config->get('acl_distribution_manage', 'all')) || acymailing_isAllowed($config->get('acl_distribution_copy', 'all')) || acymailing_isAllowed($config->get('acl_distribution_delete', 'all'))) $acyToolbar->divider();
		$acyToolbar->help('distributionlists#listing');
		$acyToolbar->setTitle(acymailing_translation('ACY_DISTRIBUTION'), 'action');
		$acyToolbar->display();

		$order = new stdClass();
		$order->ordering = false;
		$order->orderUp = 'orderup';
		$order->orderDown = 'orderdown';
		$order->reverse = false;
		if($pageInfo->filter->order->value == 'a.ordering'){
			$order->ordering = true;
			if($pageInfo->filter->order->dir == 'desc'){
				$order->orderUp = 'orderdown';
				$order->orderDown = 'orderup';
				$order->reverse = true;
			}
		}

		$filters = new stdClass();
		$creatorfilterType = acymailing_get('type.creatorfilter');
		$filters->creator = $creatorfilterType->display('filter_creator', $selectedCreator, 'action');

		$this->filters = $filters;
		$this->order = $order;
		$toggleClass = acymailing_get('helper.toggle');
		$this->toggleClass = $toggleClass;
		$this->rows = $rows;
		$this->pageInfo = $pageInfo;
		$this->pagination = $pagination;
	}

	function form(){
		$action_id = acymailing_getCID('action_id');
		$actionClass = acymailing_get('class.action');
		
		if(!empty($action_id)){
			$action = $actionClass->get($action_id);

			if(empty($action->action_id)){
				acymailing_display('Action '.$action_id.' not found', 'error');
				$action_id = 0;
			}

			$action->conditions = json_decode($action->conditions, true);
			$action->actions = json_decode($action->actions, true);
		}else{
			$action = new stdClass();
			$action->published = 1;
			$action->creatorname = acymailing_currentUserName();
			$action->userid = acymailing_currentUserId();
			$action->username = '';
		}
		if(!empty($action->username)) $action->username = acymailing_punycode($action->username, 'emailToUTF8');

		$script = '
			document.addEventListener("DOMContentLoaded", function(){
				acymailing.submitbutton = function(pressbutton) {
					if (pressbutton == \'cancel\') {
						acymailing.submitform(pressbutton, document.adminForm);
						return;
					}
					
					if(window.document.getElementById("name").value.length < 2){alert(\''.acymailing_translation('ENTER_TITLE', true).'\'); return false;}
					acymailing.submitform(pressbutton, document.adminForm);
				};
			}); ';
		$script .= 'function affectUser(idcreator,name,email){
			window.document.getElementById("creatorname").innerHTML = name;
			window.document.getElementById("actioncreator").value = idcreator;
		}';

		$script .= 'function displayAllowedOptions(value){
			if(value == "specific"){
				document.getElementById("dataconditionsspecific").style.display = "";
			}else{
				document.getElementById("dataconditionsspecific").style.display = "none";
			}

			if(value == "group"){
				document.getElementById("allowedgroupsblock").style.display = "";
			}else{
				document.getElementById("allowedgroupsblock").style.display = "none";
			}

			if(value == "list"){
				document.getElementById("allowedlistsblock").style.display = "";
			}else{
				document.getElementById("allowedlistsblock").style.display = "none";
			}
		}';

		$acyToolbar = acymailing_get('helper.toolbar');
		$acyToolbar->custom('test', acymailing_translation('ABTESTING_TEST'), 'connection', false);
		$acyToolbar->divider();
		$acyToolbar->addButtonOption('apply', acymailing_translation('ACY_APPLY'), 'apply', false);
		$acyToolbar->save();
		$acyToolbar->cancel();
		$acyToolbar->divider();
		$acyToolbar->help('distributionlists', 'edit');
		$acyToolbar->setTitle(acymailing_translation('ACY_DISTRIBUTION'), 'action&task=edit&action_id='.$action_id);
		$acyToolbar->display();

		$allowedoptions = new stdClass();
		$allowedoptions->specific = '<input type="text" name="data[conditions][specific]" id="dataconditionsspecific" class="inputbox" style="'.(@$action->conditions['sender'] == 'specific' ? '' : 'display:none;').'width:200px" value="'.$this->escape(empty($action->conditions['specific']) ? acymailing_currentUserEmail() : $action->conditions['specific']).'"/>';

		if(!ACYMAILING_J16){
			$acl = JFactory::getACL();
			$groups = $acl->get_group_children_tree(null, 'USERS', false);
		}else{
			$groups = acymailing_getGroups();
			foreach($groups as $id => $group){
				if(isset($groups[$group->parent_id])){
					$groups[$id]->level = empty($groups[$group->parent_id]->level) ? 1 : intval($groups[$group->parent_id]->level + 1);
					$groups[$id]->text = str_repeat('- - ', $groups[$id]->level).$groups[$id]->text;
				}
			}
		}

		$allgroups = new stdClass();
		$allgroups->text = acymailing_translation('ACY_ALL');
		$allgroups->value = 'all';
		array_unshift($groups, $allgroups);
		$allowedoptions->group = '<span id="allowedgroupsblock"'.(@$action->conditions['sender'] == 'group' ? '' : 'style="display:none;"').'>'.acymailing_select($groups, "data[conditions][group]", 'class="inputbox" size="1"', 'value', 'text', @$action->conditions['group']).'</span>';

		$allowedoptions->list = '<span id="allowedlistsblock"'.(@$action->conditions['sender'] == 'list' ? '' : 'style="display:none;"').'><input class="inputbox" id="dataconditionslistids" name="data[conditions][listids]" type="text" style="width:75px" value="'.@$action->conditions['listids'].'">';
		$allowedoptions->list .= acymailing_popup(acymailing_completeLink('chooselist', true).'&task=listids&values='.@$action->conditions['listids'].'&control=dataconditions', '<button class="acymailing_button_grey" onclick="return false">'.acymailing_translation('SELECT').'</button>', '', 650, 375, 'linkdataconditionslistids').'</span>';

		$possibleActions = array();
		$possibleActions[] = acymailing_selectOption('none', acymailing_translation('ACTION_SELECT'));
		$possibleActions[] = acymailing_selectOption('forwardlist', acymailing_translation('ACY_FORWARD_LIST'));
		$possibleActions[] = acymailing_selectOption('forward', acymailing_translation('FORWARD_EMAIL'));
		$possibleActions[] = acymailing_selectOption('subscribe', acymailing_translation('SUBSCRIBECAPTION'));
		$possibleActions[] = acymailing_selectOption('unsubscribe', acymailing_translation('UNSUBSCRIBECAPTION'));
		$possibleActions[] = acymailing_selectOption('newsletter', acymailing_translation('ACY_SEND_NEWSLETTER'));

		$listClass = acymailing_get('class.list');
		$lists = $listClass->getLists();

		$templates = acymailing_loadObjectList('SELECT tempid, name FROM '.acymailing_table('template').' WHERE published = 1 AND body LIKE "%{emailcontent}%" ORDER BY name ASC');
		$noTemplateFound = empty($templates) ? $noTemplateFound = ' '.acymailing_translation('ACY_EMAILCONTENT_TEMPLATE') : '';

		$defaultTemplate = new stdClass();
		$defaultTemplate->tempid = 0;
		$defaultTemplate->name = acymailing_translation('ACY_NO_TEMPLATE');
		array_unshift($templates, $defaultTemplate);

		$newsletters = acymailing_loadObjectList('SELECT mailid, subject FROM '.acymailing_table('mail').' WHERE published = 1 AND type = "news" ORDER BY mailid DESC LIMIT 100');

		$defaultNewsletter = new stdClass();
		$defaultNewsletter->mailid = 0;
		$defaultNewsletter->subject = acymailing_translation('LATEST_NEWSLETTER');
		array_unshift($newsletters, $defaultNewsletter);

		$includeIn = '<br />'.acymailing_translation('ACY_INCLUDE_MSG_IN').' ';

		$js = "var typesOptions = new Array();
		typesOptions['none'] = '';
		typesOptions['forwardlist'] = '".str_replace(array("'", "\n"), array("\\'", ''), acymailing_select($lists, "data[actions][__num__][list]", 'class="inputbox chzn-done" size="1"', 'listid', 'name').$includeIn.acymailing_select($templates, "data[actions][__num__][template]", 'class="inputbox chzn-done" size="1"', 'tempid', 'name').$noTemplateFound)."';
		typesOptions['forward'] = '<input id=\"dataactions__num__forward\" type=\"text\" placeholder=\"address1@example.com,address2@example.com\" name=\"data[actions][__num__][forward]\" /> ".str_replace(array("'", "\n"), array("\\'", ''), $includeIn.acymailing_select($templates, "data[actions][__num__][template]", 'class="inputbox chzn-done" size="1"', 'tempid', 'name').$noTemplateFound)."';
		typesOptions['subscribe'] = '".str_replace(array("'", "\n"), array("\\'", ''), acymailing_select($lists, "data[actions][__num__][list]", 'class="inputbox chzn-done" size="1"', 'listid', 'name'))."';
		typesOptions['unsubscribe'] = '".str_replace(array("'", "\n"), array("\\'", ''), acymailing_select($lists, "data[actions][__num__][list]", 'class="inputbox chzn-done" size="1"', 'listid', 'name'))."';
		typesOptions['newsletter'] = '".str_replace(array("'", "\n"), array("\\'", ''), acymailing_select($newsletters, "data[actions][__num__][newsletter]", 'class="inputbox chzn-done" size="1"', 'mailid', 'subject'))."';

		function updateAction(number){
			var selectedType = document.getElementById('dataactions'+number+'type').value;
			document.getElementById('actions'+number+'area').innerHTML = typesOptions[selectedType].replace(/__num__/g, number);
		}

		var numActions = 0;
		function addAction(){
			var newdiv = document.createElement('div');
			newdiv.id = 'actionscontainer'+numActions;

			var content = '".str_replace(array("'", "\n"), array("\\'", ''), acymailing_select($possibleActions, "data[actions][__num__][type]", 'class="inputbox chzn-done" size="1" onchange="updateAction(__num__);"', 'value', 'text'))."<div class=\"acyfilterarea\" id=\"actions'+numActions+'area\"></div>';
			newdiv.innerHTML = content.replace(/__num__/g, numActions);

			var actionsarea = document.getElementById('actionsarea');
			if(actionsarea != 'undefined' && actionsarea != null) actionsarea.appendChild(newdiv);
			numActions++;
		}";

		$ready = "addAction();";
		if(!empty($action->actions)){
			foreach($action->actions as $oneAction){
				if(empty($oneAction['type']) || $oneAction['type'] == 'none') continue;
				$ready .= "document.getElementById('dataactions'+(numActions-1)+'type').value = '".$oneAction['type']."';
				updateAction(numActions-1);";
				unset($oneAction['type']);
				foreach($oneAction as $element => $value){
					$ready .= "document.getElementById('dataactions'+(numActions-1)+'".$element."').value = '".$value."';";
				}
				$ready .= "addAction();";
			}
		}

		$js .= "document.addEventListener(\"DOMContentLoaded\", function(){ ".$ready." });";
		$script .= $js;

		acymailing_addScript(true, $script);

		$this->allowedoptions = $allowedoptions;
		$this->action = $action;
		if(!empty($action->conditions['removeSubject'])) $this->removeSub = $action->conditions['removeSubject'];
	}
}
