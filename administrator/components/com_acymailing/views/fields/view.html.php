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


class FieldsViewFields extends acymailingView{
	var $chosen = false;

	function display($tpl = null){
		$function = $this->getLayout();
		if(method_exists($this, $function)) $this->$function();

		parent::display($tpl);
	}

	function form(){
		$fieldid = acymailing_getCID('fieldid');
		$fieldsClass = acymailing_get('class.fields');
		if(!empty($fieldid)){
			$field = $fieldsClass->get($fieldid);
		}else{
			$field = new stdClass();
			$field->published = 1;
			$field->type = 'text';
			$field->backend = 1;
			$field->namekey = '';
		}

		$fieldTitle = (!empty($field->fieldid)) ? ' : '.$field->namekey : '';

		$start = empty($field->value) ? 0 : count($field->value);
		$script = ' var currentid = '.($start + 1).';
			function addLine(){
			var myTable=window.document.getElementById("tablevalues");
			var newline = document.createElement(\'tr\');
			var column = document.createElement(\'td\');
			var column2 = document.createElement(\'td\');
			var column3 = document.createElement(\'td\');
			var column4 = document.createElement(\'td\');
			column4.innerHTML = \'<a onclick="acymove(\'+currentid+\',1);return false;" href="#"><img src="'.ACYMAILING_IMAGES.'movedown.png" alt=" ˇ "/></a><a onclick="acymove(\'+currentid+\',-1);return false;" href="#"><img src="'.ACYMAILING_IMAGES.'moveup.png" alt=" ˆ "/></a>\';
			var input = document.createElement(\'input\');
			input.id = "option"+currentid+"title";
			var input2 = document.createElement(\'input\');
			input2.id = "option"+currentid+"value";
			var input3 = document.createElement(\'select\');
			input3.id = "option"+currentid+"disabled";
			var option1 = document.createElement(\'option\');
			var option2 = document.createElement(\'option\');
			input.type = \'text\';
			input2.type = \'text\';
			input.name = \'fieldvalues[value][]\';
			input2.name = \'fieldvalues[title][]\';
			input3.name = \'fieldvalues[disabled][]\';
			input.style.width = \'150px\';
			input2.style.width = \'180px\';
			input3.style.width = \'80px\';
			option1.value= \'0\';
			option2.value= \'1\';
			option1.text= \''.acymailing_translation('JOOMEXT_NO', true).'\';
			option2.text= \''.acymailing_translation('JOOMEXT_YES', true).'\';
			try { input3.add(option1, null); } catch(ex) { input3.add(option1); }
			try { input3.add(option2, null); } catch(ex) { input3.add(option2); }
			column.appendChild(input);
			column2.appendChild(input2);
			column3.appendChild(input3);
			newline.appendChild(column);
			newline.appendChild(column2);
			newline.appendChild(column3);
			newline.appendChild(column4);
			myTable.appendChild(newline);
			currentid = currentid+1;
		}
		function acymove(myid,diff){
			var previousId = myid + diff;
			if(!document.getElementById(\'option\'+previousId+\'title\')) return;
			var prevtitle = document.getElementById(\'option\'+previousId+\'title\').value;
			var prevvalue = document.getElementById(\'option\'+previousId+\'value\').value;
			var prevdisabled = document.getElementById(\'option\'+previousId+\'disabled\').value;
			document.getElementById(\'option\'+previousId+\'title\').value = document.getElementById(\'option\'+myid+\'title\').value;
			document.getElementById(\'option\'+previousId+\'value\').value = document.getElementById(\'option\'+myid+\'value\').value;
			document.getElementById(\'option\'+previousId+\'disabled\').value = document.getElementById(\'option\'+myid+\'disabled\').value;
			document.getElementById(\'option\'+myid+\'title\').value = prevtitle;
			document.getElementById(\'option\'+myid+\'value\').value = prevvalue;
			document.getElementById(\'option\'+myid+\'disabled\').value = prevdisabled;
		}';

		acymailing_addScript(true, $script);

		$fieldsClass->addJSFunctions();
		$dbInfos = new stdClass();

		try{
			$allDatabases = acymailing_loadResultArray('SHOW DATABASES');
			array_unshift($allDatabases, '');
		}catch(Exception $e){
			$allDatabases = '';
		}

		$actualDb = acymailing_loadResult('SELECT DATABASE()');
		if(empty($field->options['dbName']) || $field->options['dbName'] == 'current'){
			$dbName = $actualDb;
		}else{
			$dbName = acymailing_secureField($field->options['dbName']);
		}
		if(empty($allDatabases)) $allDatabases = array($dbName);

		if(!empty($dbName)){
			$query = 'SHOW TABLES FROM `'.$dbName.'`';
			try{
				$allTables = acymailing_loadResultArray($query);
				array_unshift($allTables, '');
			}catch(Exception $e){
				$allTables = array('');
				acymailing_display($e->getMessage(), 'error');
			}
		}else{
			$allTables = array('');
		}

		$allFields = array();
		$tableName = '';
		if(!empty($field->options['tableName'])){
			$tableName = acymailing_secureField($field->options['tableName']);
			try{
				$allFields = acymailing_loadResultArray('SHOW FIELDS FROM `'.$dbName.'`.`'.$tableName.'`');
				array_unshift($allFields, '');
			}catch(Exception $e){
				$allFields = array('');
				acymailing_display($e->getMessage(), 'error');
			}
		}
		$eltsToClean = array('acyField_dbValues');
		acymailing_removeChzn($eltsToClean);

		$operators = acymailing_get('type.operators');

		$dbInfos->allDB = $allDatabases;
		$dbInfos->actualDB = $actualDb;
		$dbInfos->selectedDB = $dbName;
		$dbInfos->allTables = $allTables;
		$dbInfos->actualTable = $tableName;
		$dbInfos->allFields = $allFields;

		$defaultCC = '';
		if(!empty($field->options['checkcontent'])) $defaultCC = $field->options['checkcontent'];
		$valRegexp = (!empty($field->options['regexp']) ? 'value="'.$field->options['regexp'].'"' : '');
		$fieldCheckContent = '<input type="radio" name="fieldsoptions[checkcontent]" id="fieldsoptions[checkcontent]0" value="0" '.(empty($defaultCC) ? 'checked="checked"' : '').'>';
		$fieldCheckContent .= ' <label for="fieldsoptions[checkcontent]0" id="fieldsoptions[checkcontent]0-lbl class="radiobtn">'.acymailing_translation('ACY_ALL').'</label><br />';
		$fieldCheckContent .= '<input type="radio" name="fieldsoptions[checkcontent]" id="fieldsoptions[checkcontent]1" value="number" '.($defaultCC == 'number' ? 'checked="checked"' : '').'>';
		$fieldCheckContent .= ' <label for="fieldsoptions[checkcontent]1" id="fieldsoptions[checkcontent]1-lbl class="radiobtn">'.acymailing_translation('ONLY_NUMBER').'</label><br />';
		$fieldCheckContent .= '<input type="radio" name="fieldsoptions[checkcontent]" id="fieldsoptions[checkcontent]2" value="letter" '.($defaultCC == 'letter' ? 'checked="checked"' : '').'>';
		$fieldCheckContent .= ' <label for="fieldsoptions[checkcontent]2" id="fieldsoptions[checkcontent]2-lbl class="radiobtn">'.acymailing_translation('ONLY_LETTER').'</label><br />';
		$fieldCheckContent .= '<input type="radio" name="fieldsoptions[checkcontent]" id="fieldsoptions[checkcontent]3" value="letnum" '.($defaultCC == 'letnum' ? 'checked="checked"' : '').'>';
		$fieldCheckContent .= ' <label for="fieldsoptions[checkcontent]3" id="fieldsoptions[checkcontent]3-lbl class="radiobtn">'.acymailing_translation('ONLY_NUMBER_LETTER').'</label><br />';
		$fieldCheckContent .= '<input type="radio" name="fieldsoptions[checkcontent]" id="fieldsoptions[checkcontent]4" value="regexp" '.($defaultCC == 'regexp' ? 'checked="checked"' : '').'>';
		$fieldCheckContent .= ' <label for="fieldsoptions[checkcontent]4" id="fieldsoptions[checkcontent]4-lbl class="radiobtn">'.acymailing_translation('MY_REGEXP').'</label> ';
		$fieldCheckContent .= ' <input type="text" name="fieldsoptions[regexp]" id="fieldsoptions[regexp]" style="width:200px" '.$valRegexp.'/>';

		$fake = null;
		$categories = $fieldsClass->getFields('fieldcat', $fake);
		$catListing = array();
		$catListing[] = acymailing_selectOption(0, '---');
		foreach($categories as $category){
			if(!empty($field->fieldid) && $category->fieldid == $field->fieldid) continue;
			$catListing[] = acymailing_selectOption($category->fieldid, $category->fieldname);
		}
		if(count($catListing) > 1){
			$catDropdown = acymailing_select($catListing, 'data[fields][fieldcat]', 'size="1"', 'value', 'text', (!empty($field->fieldcat) ? $field->fieldcat : '0'), 'fieldcat');
		}else{
			$catDropdown = '';
		}

		$catTagValues = array();
		$catTagValues[] = acymailing_selectOption('div', 'Div');
		$catTagValues[] = acymailing_selectOption('fldset', 'Fieldset');
		$catTag = acymailing_radio($catTagValues, "fieldsoptions[fieldcattag]", '', 'value', 'text', (!empty($field->options['fieldcattag']) ? $field->options['fieldcattag'] : 'div'));

		$queryListField = "SELECT * FROM #__acymailing_fields WHERE type IN ('text', 'textarea', 'radio', 'singledropdown', 'phone', 'birthday')";
		if(!empty($field->fieldid)) $queryListField .= "  AND fieldid != ".$field->fieldid;
		$customfields = acymailing_loadObjectList($queryListField);
		$listFields = array();
		$listFields[] = acymailing_selectOption('0', '...');
		foreach($customfields as $oneField){
			$listFields[] = acymailing_selectOption($oneField->namekey, acymailing_translation($oneField->fieldname));
		}
		$listOper = array();
		$listOper[] = acymailing_selectOption('==', '=');
		$listOper[] = acymailing_selectOption('!=', '!=');
		$listOper[] = acymailing_selectOption('>', '>');
		$listOper[] = acymailing_selectOption('<', '<');
		$listOper[] = acymailing_selectOption('>=', '>=');
		$listOper[] = acymailing_selectOption('<=', '<=');
		$andOrBtnVal = array();
		$andOrBtnVal[] = acymailing_selectOption('', '');
		$andOrBtnVal[] = acymailing_selectOption('&&', 'And');
		$andOrBtnVal[] = acymailing_selectOption('||', 'Or');

		$jsAddDisplayRule = "function addDisplayRule(id){
			id= 'fieldsoptionsdisplim_'
			i = 1;
			while(document.getElementById('displaylimited_cond'+i)){
				i++;
			}
			if(document.getElementById(id+'rel'+(i-1)).value == '') return;

			var newElement = document.createElement('span');
			var toCopy = document.getElementById('displaylimited_cond0');
			newElement.id = toCopy.id.replace('_cond0','_cond'+i);
			newElement.innerHTML = toCopy.innerHTML;
			newElement.innerHTML = newElement.innerHTML.replace(new RegExp('field0','g'),'field'+i);
			newElement.innerHTML = newElement.innerHTML.replace(new RegExp('ope0','g'),'ope'+i);
			newElement.innerHTML = newElement.innerHTML.replace(new RegExp('value0','g'),'value'+i);
			newElement.innerHTML = newElement.innerHTML.replace(new RegExp('rel0','g'),'rel'+i);

			document.getElementById('displayLimitedBloc').appendChild(newElement);
			document.getElementById(id+'rel'+i).value = '';

		}";
		acymailing_addScript(true, $jsAddDisplayRule);

		$i = 0;
		$dispLimited = '';
		do{
			$dispLimited .= '<span id="displaylimited_cond'.$i.'">';
			$dispLimited .= acymailing_select($listFields, 'fieldsoptions[displim_field'.$i.']', '', 'value', 'text', (!empty($field->options['displim_field'.$i]) ? $field->options['displim_field'.$i] : ''));
			$dispLimited .= acymailing_select($listOper, 'fieldsoptions[displim_ope'.$i.']', 'style="width:50px !important"', 'value', 'text', (!empty($field->options['displim_ope'.$i]) ? $field->options['displim_ope'.$i] : ''));
			$dispLimited .= '<input type="text" id="displaylimitedvalue'.$i.'" name="fieldsoptions[displim_value'.$i.']" class="inputbox" value="'.(!empty($field->options['displim_value'.$i]) ? $field->options['displim_value'.$i] : '').'" '.(ACYMAILING_J30 ? 'style="margin-bottom:10px"' : '').'>';
			$dispLimited .= '<br />';
			$dispLimited .= acymailing_select($andOrBtnVal, 'fieldsoptions[displim_rel'.$i.']', 'onchange="addDisplayRule(this.id)" style="width:60px !important"', 'value', 'text', (!empty($field->options['displim_rel'.$i]) ? $field->options['displim_rel'.$i] : ''));
			$dispLimited .= '<br /></span>';
			$i++;
		}while(!empty($field->options['displim_rel'.($i - 1)]));

		if(!empty($field->fieldid)){
			$queryFields = "SELECT * FROM #__acymailing_fields WHERE fieldid != ".$field->fieldid." AND options like '%\"".$field->namekey."\"%'";
			$otherfields = acymailing_loadObjectList($queryFields);

			$used = false;
			$usedInFields = array();
			foreach($otherfields as $oneField){
				$options = unserialize($oneField->options);
				$keys = array_keys($options, $field->namekey);
				if(empty($keys)) continue;
				foreach($keys as $oneKey){
					if(substr($oneKey, 0, 13) == 'displim_field'){
						$used = true;
						$usedInFields[$oneField->fieldid] = '<a href="'.acymailing_completeLink('fields&task=edit&fieldid='.$oneField->fieldid).'" target="_blank">'.$oneField->namekey.'</a>';
					}
				}
			}
			if($used){
				$this->usedInFields = $usedInFields;
			}
		}

		$acyToolbar = acymailing_get('helper.toolbar');
		$acyToolbar->addButtonOption('apply', acymailing_translation('ACY_APPLY'), 'apply', false);
		$acyToolbar->addButtonOption('save2new', acymailing_translation('ACY_SAVEANDNEW'), 'new', false);
		$acyToolbar->save();
		$acyToolbar->cancel();
		$acyToolbar->divider();
		$acyToolbar->help('customfields');
		$acyToolbar->setTitle(acymailing_translation('FIELD').$fieldTitle, 'fields&task=edit&fieldid='.$fieldid);
		$acyToolbar->display();

		$fieldtype = acymailing_get('type.fields');
		$acltype = acymailing_get('type.acl');

		$this->fieldtype = $fieldtype;
		$this->field = $field;
		$this->fieldsClass = $fieldsClass;
		$this->fieldCheckContent = $fieldCheckContent;
		$this->dbInfos = $dbInfos;
		$this->operators = $operators;
		$this->acltype = $acltype;
		$this->categories = $catDropdown;
		$this->catTag = $catTag;
		$this->dispLimited = $dispLimited;
	}

	function listing(){
		$rows = acymailing_loadObjectList('SELECT * FROM `#__acymailing_fields` ORDER BY `ordering` ASC');

		$acyToolbar = acymailing_get('helper.toolbar');
		$acyToolbar->add();
		$acyToolbar->edit();
		$acyToolbar->delete();
		$acyToolbar->divider();
		$acyToolbar->help('customfields');
		$acyToolbar->setTitle(acymailing_translation('EXTRA_FIELDS'), 'fields');
		$acyToolbar->display();

		$this->rows = $rows;
		$toggleClass = acymailing_get('helper.toggle');
		$this->toggleClass = $toggleClass;
		$fieldtype = acymailing_get('type.fields');
		$this->fieldtype = $fieldtype;
		$fieldsClass = acymailing_get('class.fields');
		$this->fieldsClass = $fieldsClass;
	}
}
