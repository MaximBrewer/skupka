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

class aclType extends acymailingClass{
	var $nonLoggedIn = false;

	function __construct(){
		parent::__construct();
		if(!ACYMAILING_J16){
			$acl = JFactory::getACL();
			$this->groups = $acl->get_group_children_tree(null, 'USERS', false);
		}else{
			$this->groups = acymailing_getGroups();
			foreach($this->groups as $id => $group){
				if(isset($this->groups[$group->parent_id])){
					$this->groups[$id]->level = intval(@$this->groups[$group->parent_id]->level) + 1;
					$this->groups[$id]->text = str_repeat('- - ', $this->groups[$id]->level).$this->groups[$id]->text;
				}
			}
			if(class_exists('JComponentHelper')){
				$guestUsergroup = JComponentHelper::getParams('com_users')->get('guest_usergroup', 1);
				if(!empty($guestUsergroup) && !isset($this->groups[$guestUsergroup])){
					$guestGoup = new stdClass();
					$guestGoup->level = 1;
					$guestGoup->value = $guestUsergroup;
					$guestGoup->text = str_repeat('- - ', $guestGoup->level).$guestUsergroup;
					$this->groups[$guestUsergroup] = $guestGoup;
				}
			}
		}
		$this->choice = array();
		$this->choice[] = acymailing_selectOption('none', acymailing_translation('ACY_NONE'));
		$this->choice[] = acymailing_selectOption('all', acymailing_translation('ACY_ALL'));
		$this->choice[] = acymailing_selectOption('special', acymailing_translation('ACY_CUSTOM'));

		$js = "function updateACL(map){
			choice = eval('document.adminForm.choice_'+map);
			choiceValue = 'special';
			for (var i=0; i < choice.length; i++){
				 if (choice[i].checked){
					 choiceValue = choice[i].value;
				}
			}

			hiddenVar = document.getElementById('hidden_'+map);
			if(choiceValue != 'special'){
				hiddenVar.value = choiceValue;
				document.getElementById('div_'+map).style.display = 'none';
			}else{
				document.getElementById('div_'+map).style.display = 'block';
				specialVar = eval('document.adminForm.special_'+map);
				finalValue = ',';
				for (var i=0; i < specialVar.length; i++){
					if (specialVar[i].checked){
							 finalValue += specialVar[i].value+',';
					}
				}
				hiddenVar.value = finalValue;
			}

		}

		function checkAll(map){
			var specialVar = eval('document.adminForm.special_'+map);
			var allchecked = true;

			for (var i=0; i < specialVar.length; i++){
				if(!specialVar[i].checked){
					allchecked = false;
					break;
				}
			}

			for (var i=0; i < specialVar.length; i++){
				if(allchecked) specialVar[i].checked = '';
				else specialVar[i].checked = 'checked';
			}
			updateACL(map);
		}";

		acymailing_addScript(true, $js);
	}

	function display($map, $values){
		$simplifiedmap = str_replace(array('[', ']'), '_', $map);
		$js = 'document.addEventListener("DOMContentLoaded", function(){ updateACL(\''.$simplifiedmap.'\'); });';
		acymailing_addScript(true, $js);


		$choiceValue = ($values == 'none' || $values == 'all') ? $values : 'special';
		$return = acymailing_radio($this->choice, "choice_".$simplifiedmap, 'onclick="updateACL(\''.$simplifiedmap.'\');"', 'value', 'text', $choiceValue);
		$return .= '<input type="hidden" name="'.$map.'" id="hidden_'.$simplifiedmap.'" value="'.$values.'"/>';
		$valuesArray = explode(',', $values);
		$listAccess = '<div style="display:none" id="div_'.$simplifiedmap.'"><table class="acymailing_smalltable"><tr><td colspan="2"><a style="text-decoration:none;cursor:pointer;" onclick="checkAll(\''.$simplifiedmap.'\');">'.acymailing_translation('ACY_ALL').'</a></td></tr>';
		if($this->nonLoggedIn) $listAccess .= '<tr><td style="width:20px;"><input type="checkbox" onclick="updateACL(\''.$simplifiedmap.'\');" value="nonloggedin" '.(in_array('nonloggedin', $valuesArray) ? 'checked' : '').' name="special_'.$simplifiedmap.'" id="special_'.$simplifiedmap.'_nonloggedin"/></td><td><label for="special_'.$simplifiedmap.'_nonloggedin">'.acymailing_translation('ACY_NON_LOGGED_IN').'</label></td></tr>';
		foreach($this->groups as $oneGroup){
			$listAccess .= '<tr><td style="width:20px;">';
			if(ACYMAILING_J16 || !in_array($oneGroup->value, array(29, 30))) $listAccess .= '<input type="checkbox" onclick="updateACL(\''.$simplifiedmap.'\');" value="'.$oneGroup->value.'" '.(in_array($oneGroup->value, $valuesArray) ? 'checked' : '').' name="special_'.$simplifiedmap.'" id="special_'.$simplifiedmap.'_'.$oneGroup->value.'"/>';
			$listAccess .= '</td><td><label for="special_'.$simplifiedmap.'_'.$oneGroup->value.'">'.$oneGroup->text.'</label></td></tr>';
		}
		$listAccess .= '</table></div>';
		$return .= $listAccess;
		return $return;
	}
}
