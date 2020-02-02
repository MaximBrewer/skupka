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

class listslanguagesType extends acymailingClass{
	var $multipleLang = false;

	function __construct(){
		parent::__construct();
		$this->languages = acymailing_getLanguages(true);
		if(count($this->languages) < 2) return;

		$this->multipleLang = true;

		$this->choice = array();
		$this->choice[] = acymailing_selectOption('all', acymailing_translation('ACY_ALL'));
		$this->choice[] = acymailing_selectOption('special', acymailing_translation('ACY_CUSTOM'));

		$js = "function updateLanguages(){
			choice = eval('document.adminForm.choice_languages');
			choiceValue = 'special';
			for (var i=0; i < choice.length; i++){
				 if (choice[i].checked){
					 choiceValue = choice[i].value;
				}
			}

			hiddenVar = document.getElementById('hidden_languages');
			if(choiceValue != 'special'){
				hiddenVar.value = choiceValue;
				document.getElementById('div_languages').style.display = 'none';
			}else{
				document.getElementById('div_languages').style.display = 'block';
				specialVar = eval('document.adminForm.special_languages');
				finalValue = '';
				for (var i=0; i < specialVar.length; i++){
					if (specialVar[i].checked){
							 finalValue += specialVar[i].value+',';
					}
				}
				hiddenVar.value = finalValue;
			}

		}";

		acymailing_addScript(true, $js);
	}

	function display($map, $values){
		$js = 'document.addEventListener("DOMContentLoaded", function(){ updateLanguages(); });';
		acymailing_addScript(true, $js);

		$choiceValue = ($values == 'all') ? $values : 'special';
		$return = acymailing_radio($this->choice, "choice_languages", 'onclick="updateLanguages();"', 'value', 'text', $choiceValue);
		$return .= '<input type="hidden" name="data[list][languages]" id="hidden_languages" value="'.$values.'"/>';
		$valuesArray = explode(',', $values);
		$listLang = '<div style="display:none" id="div_languages"><table class="acymailing_smalltable">';
		foreach($this->languages as $oneLanguage){
			$listLang .= '<tr><td style="width:20px;">';
			$listLang .= '<input type="checkbox" onclick="updateLanguages();" value="'.$oneLanguage->language.'" '.(in_array($oneLanguage->language, $valuesArray) ? 'checked' : '').' name="special_languages" id="special_languages_'.$oneLanguage->language.'"/>';
			$listLang .= '</td><td><label for="special_languages_'.$oneLanguage->language.'">'.$oneLanguage->name.'</label></td></tr>';
		}
		$listLang .= '</table></div>';
		$return .= $listLang;
		return $return;
	}
}
