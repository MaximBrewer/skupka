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

class FieldsController extends acymailingController{
	var $pkey = 'fieldid';
	var $table = 'fields';
	var $groupMap = '';
	var $groupVal = '';

	function listing(){
		if(!acymailing_level(3)){
			$acyToolbar = acymailing_get('helper.toolbar');
			$acyToolbar->setTitle(acymailing_translation('EXTRA_FIELDS'), 'fields');
			$acyToolbar->help('customfields');
			$acyToolbar->display();
			$config = acymailing_config();

			$level = $config->get('level');
			$url = ACYMAILING_HELPURL.'fields-paidversion&utm_source=acymailing-'.$level.'&utm_medium=back-end&utm_content=customfields-display&utm_campaign=upgrade';
			$iFrame = "<iframe class='paidversion' frameborder='0' src='$url' width='100%' height='100%' scrolling='auto'></iframe>";
			echo $iFrame.'<div id="iframedoc"></div>';
			return;
		}

		return parent::listing();
	}


	function store(){
		if(!$this->isAllowed('configuration', 'manage')) return;
		acymailing_checkToken();

		$class = acymailing_get('class.fields');
		$status = $class->saveForm();
		if($status){
			acymailing_enqueueMessage(acymailing_translation('JOOMEXT_SUCC_SAVED'), 'message');
		}else{
			acymailing_enqueueMessage(acymailing_translation('ERROR_SAVING'), 'error');
			if(!empty($class->errors)){
				foreach($class->errors as $oneError){
					acymailing_enqueueMessage($oneError, 'error');
				}
			}
		}
	}

	function remove(){
		if(!$this->isAllowed('configuration', 'manage')) return;
		acymailing_checkToken();

		$cids = acymailing_getVar('array', 'cid', array(), '');

		$class = acymailing_get('class.fields');
		$num = $class->delete($cids);

		if($num) acymailing_enqueueMessage(acymailing_translation_sprintf('SUCC_DELETE_ELEMENTS', $num), 'message');

		return $this->listing();
	}

	function updateTablesDB(){
		$dbName = acymailing_getVar('string', 'dbName');
		if(empty($dbName)) exit;
		$query = 'SHOW TABLES FROM `'.acymailing_secureField($dbName).'`';
		$allTables = acymailing_loadResultArray($query);
		array_unshift($allTables, '');

		$allTablesSelect = '';
		foreach($allTables as $oneTable){
			$allTablesSelect .= '<option value="'.$oneTable.'">'.$oneTable.'</option>';
		}

		echo $allTablesSelect;
		exit;
	}

	function updateFieldsDB(){
		$dbName = acymailing_getVar('string', 'dbName');
		$tableName = acymailing_getVar('string', 'tableName');
		$fieldType = acymailing_getVar('string', 'fieldType');
		$defaultValue = acymailing_getVar('string', 'defaultValue');
		
		if(empty($dbName) || empty($tableName)) exit;

		$query = 'SHOW FIELDS FROM `'.acymailing_secureField($dbName).'`.`'.acymailing_secureField($tableName).'`';
		$allFields = acymailing_loadResultArray($query);
		array_unshift($allFields, '');

		$allFieldsSelect = '<select name="fieldsoptions['.$fieldType.']" id="'.$fieldType.'" style="width:150px" class="chzn-done">';
		foreach($allFields as $oneField){
			$allFieldsSelect .= '<option '.($defaultValue == $oneField ? 'selected="selected"' : '').' value="'.$oneField.'">'.$oneField.'</option>';
		}
		$allFieldsSelect .= '</select>';

		echo $allFieldsSelect;
		exit;
	}
}
