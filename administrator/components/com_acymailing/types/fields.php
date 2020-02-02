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

class fieldsType extends acymailingClass{
	var $allValues;

	var $allTypes;
	function __construct(){
		parent::__construct();
		$this->allValues = array();
		$this->allValues["text"] = acymailing_translation('FIELD_TEXT');
		$this->allValues["textarea"] = acymailing_translation('FIELD_TEXTAREA');
		$this->allValues["radio"] = acymailing_translation('FIELD_RADIO');
		$this->allValues["checkbox"] = acymailing_translation('FIELD_CHECKBOX');
		$this->allValues["singledropdown"] = acymailing_translation('FIELD_SINGLEDROPDOWN');
		$this->allValues["multipledropdown"] = acymailing_translation('FIELD_MULTIPLEDROPDOWN');
		$this->allValues["date"] = acymailing_translation('FIELD_DATE');
		$this->allValues["birthday"] = acymailing_translation('FIELD_BIRTHDAY');
		$this->allValues["file"] = acymailing_translation('FIELD_FILE');
		$this->allValues["phone"] = acymailing_translation('FIELD_PHONE');
		$this->allValues["customtext"] = acymailing_translation('CUSTOM_TEXT');
		$this->allValues["gravatar"] = 'Gravatar';
		$this->allValues["category"] = acymailing_translation('ACY_CATEGORY');

		$this->allTypes = array();
		$this->allTypes['text'] = array('size','required','default','columnname','checkcontent','editablecreate','editablemodify','fieldcat','displaylimited');
		$this->allTypes['textarea'] = array('cols','rows','required','default','columnname','editablecreate','editablemodify','fieldcat','displaylimited');
		$this->allTypes['radio'] = array('multivalues','required','default','columnname','dbValues','editablecreate','editablemodify','fieldcat','displaylimited');
		$this->allTypes['checkbox'] = array('multivalues','required','default','columnname','dbValues','editablecreate','editablemodify','fieldcat','displaylimited');
		$this->allTypes['singledropdown'] = array('multivalues','required','default','columnname','size','dbValues','editablecreate','editablemodify','fieldcat','displaylimited');
		$this->allTypes['multipledropdown'] = array('multivalues','required','size','default','columnname','dbValues','editablecreate','editablemodify','fieldcat','displaylimited');
		$this->allTypes['date'] = array('required','format','size','default','columnname','editablecreate','editablemodify','fieldcat','displaylimited');
		$this->allTypes['birthday'] = array('required','format','default','columnname','editablecreate','editablemodify','fieldcat','displaylimited');
		$this->allTypes['file'] = array('columnname','required','size','editablecreate','editablemodify','fieldcat','displaylimited');
		$this->allTypes['phone'] = array('columnname','required','size','default','editablecreate','editablemodify','fieldcat','displaylimited');
		$this->allTypes['customtext'] = array('customtext','editablecreate','editablemodify','fieldcat','displaylimited');
		$this->allTypes['gravatar'] = array('columnname','required','size','editablecreate','editablemodify','fieldcat','displaylimited');
		$this->allTypes['category'] = array('fieldcat','fieldcattag','fieldcatclass','displaylimited');

		acymailing_importPlugin('acymailing');
		acymailing_trigger('onAcyCreateField', array(&$this->allValues, &$this->allTypes));
	}

	function display($map,$value){
		$allowedTypes = array('singledropdown','multipledropdown','checkbox','radio','birthday','date');
		$js = "function updateFieldType(){
				newType = document.getElementById('fieldtype').value;";
			if(acymailing_level(3)){
				$js .= "if(newType == '".implode("' || newType == '", $allowedTypes)."'){
					document.getElementById('listingfilter_option').style.display = '';
					document.getElementById('frontlistingfilter_option').style.display = '';
				}else{
					document.getElementById('listingfilter_option').style.display = 'none';
					document.getElementById('frontlistingfilter_option').style.display = 'none';
				}";
			}
			$js .= "hiddenAll = new Array('multivalues','cols','rows','size','required','format','default','customtext','columnname','checkcontent','dbValues','editablecreate','editablemodify','fieldcat','fieldcattag','fieldcatclass','displaylimited');
				allTypes = new Array();
				";

			foreach($this->allTypes as $type => $detail){
				$js .= "allTypes['" . $type ."'] = new Array('" . implode("','", $detail) . "');
				";
			}

			$js .= "for (var i=0; i < hiddenAll.length; i++){
				var elems = document.querySelectorAll('tr[class='+hiddenAll[i]+']');
				for(var j=0 ; j< elems.length ; j++){
					elems[j].style.display = 'none';
				}
			}

			for (var i=0; i < allTypes[newType].length; i++){
				var elems = document.querySelectorAll('tr[class='+allTypes[newType][i]+']');
				for(var j=0 ; j< elems.length ; j++){
					elems[j].style.display = '';
				}
			}
		}
		document.addEventListener(\"DOMContentLoaded\", function(){ updateFieldType(); });";

		acymailing_addScript(true, $js);

		$this->values = array();
		foreach($this->allValues as $oneType => $oneVal){
			$this->values[] = acymailing_selectOption($oneType, $oneVal);
		}


		return acymailing_select($this->values, $map , 'size="1" onchange="updateFieldType();"', 'value', 'text', (string) $value,'fieldtype');
	}
}
