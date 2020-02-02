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

class plgAcymailingGeolocation extends JPlugin{
	function __construct(&$subject, $config){
		parent::__construct($subject, $config);
		if(!isset($this->params)){
			$plugin = JPluginHelper::getPlugin('acymailing', 'geolocation');
			$this->params = new acyParameter($plugin->params);
		}
	}

	function acymailing_getPluginType(){
		if($this->params->get('frontendaccess') == 'none' && !acymailing_isAdmin()) return;
		$onePlugin = new stdClass();
		$onePlugin->name = acymailing_translation('GEOLOCATION');
		$onePlugin->function = 'acymailinggeolocation_show';
		$onePlugin->help = 'plugin-geolocation';

		return $onePlugin;
	}

	function acymailinggeolocation_show(){
		?>
		<script language="javascript" type="text/javascript">
			function applyTag(tagname){
				var string = '{geoloc:' + tagname;
				if(document.adminForm.geolocType && document.adminForm.geolocType.value != '0'){
					string += '|info:' + document.adminForm.geolocType.value;
				}
				string += '}';
				setTag(string);
				insertTag();
			}
		</script>
		<?php
		$descriptions['geolocation_type'] = acymailing_translation('ACY_ACTION');
		$descriptions['geolocation_ip'] = acymailing_translation('SUBSCRIBER_IP');
		$descriptions['geolocation_created'] = acymailing_translation('CREATED_DATE');
		$descriptions['geolocation_country'] = acymailing_translation('COUNTRYCAPTION');
		$descriptions['geolocation_country_code'] = acymailing_translation('GEOLOC_COUNTRYCODE');
		$descriptions['geolocation_state'] = acymailing_translation('STATECAPTION');
		$descriptions['geolocation_state_code'] = acymailing_translation('GEOLOC_STATECODE');
		$descriptions['geolocation_city'] = acymailing_translation('CITYCAPTION');
		$descriptions['geolocation_postal_code'] = acymailing_translation('GEOLOC_POSTALCODE');
		$descriptions['geolocation_latitude'] = acymailing_translation('GEOLOC_LATITUDE');
		$descriptions['geolocation_longitude'] = acymailing_translation('GEOLOC_LONGITUDE');
		$descriptions['geolocation_continent'] = acymailing_translation('GEOLOC_CONTINENT');
		$descriptions['geolocation_timezone'] = acymailing_translation('GEOLOC_TIMEZONE');

		$config = acymailing_config();
		$geoloc = $config->get('geolocation');
		$geoloc = explode(',', $geoloc);
		if(array_search('1', $geoloc) !== false) array_splice($geoloc, array_search('1', $geoloc), 1);
		$listOptions = array();
		$listOptions[] = acymailing_selectOption('0', acymailing_translation('GEOLOC_RECENT'));
		foreach($geoloc as $oneType){
			$listOptions[] = acymailing_selectOption($oneType, $oneType);
		}
		$text = '';
		if(acymailing_getVar('none', 'type') != 'notification'){
			$text .= acymailing_translation('ACY_ACTION').': '.acymailing_select($listOptions, 'geolocType', 'size="1"');
		}

		$text .= '<div class="onelineblockoptions">
					<table class="acymailing_table" cellpadding="1">';
		$fields = acymailing_getColumns('#__acymailing_geolocation');

		$k = 0;
		foreach($fields as $fieldname => $oneField){
			if(!isset($descriptions[$fieldname]) AND $oneField == 'tinyint') continue;
			if(in_array($fieldname, array('geolocation_id', 'geolocation_subid'))) continue;

			$type = '';
			if($fieldname == 'geolocation_created') $type = '|type:time';

			$tagName = $fieldname;
			if(acymailing_getVar('none', 'type') == 'notification') $tagName = 'notif_'.$fieldname;

			$text .= '<tr style="cursor:pointer" class="row'.$k.'" onclick="applyTag(\''.$tagName.$type.'\');" ><td class="acytdcheckbox"></td><td>'.$fieldname.'</td><td>'.@$descriptions[$fieldname].'</td></tr>';
			$k = 1 - $k;
		}

		$text .= '</table></div>';

		echo $text;
	}

	function acymailing_replaceusertags(&$email, &$user, $send = true){
		$variables = array('subject', 'body', 'altbody');
		$acypluginsHelper = acymailing_get('helper.acyplugins');
		$result = $acypluginsHelper->extractTags($email, 'geoloc');
		$tags = array();

		foreach($result as $key => $oneTag){
			if(!empty($user->subid)){
				$query = 'SELECT * FROM #__acymailing_geolocation WHERE geolocation_subid='.$user->subid;
				if(!empty($oneTag->info->value)){
					$query .= ' AND geolocation_type='.acymailing_escapeDB($oneTag->info->value);
				}
				$query .= ' ORDER BY geolocation_created DESC LIMIT 1';
				$sqlRes = acymailing_loadObject($query);
			}
			$field = $oneTag->id;
			if(!empty($sqlRes) && !empty($sqlRes->$field)){
				$text = $sqlRes->$field;
				$acypluginsHelper->formatString($text, $oneTag);
				$tags[$key] = $text;
			}else{
				$tags[$key] = $oneTag->default;
			}
		}
		foreach($variables as $var){
			if(empty($email->$var)) continue;
			$email->$var = str_replace(array_keys($tags), $tags, $email->$var);
		}
	}

	function onAcyDisplayFilters(&$type, $context = "massactions"){

		if($this->params->get('displayfilter_'.$context, true) == false) return;

		$fields = acymailing_getColumns('#__acymailing_geolocation');
		if(empty($fields)) return;

		$field = array();
		$field[] = acymailing_selectOption('', '- - -');
		foreach($fields as $oneField => $fieldType){
			if($oneField == 'geolocation_id' || $oneField == 'geolocation_subid') continue;
			$field[] = acymailing_selectOption($oneField, $oneField);
		}
		$type['geolocfield'] = acymailing_translation('GEOLOCATION');

		$jsOnChange = "var searchMap = document.getElementById('filter__num__geolocfieldmap').value; ";
		$jsOnChange .= "var displayVal = 'ok'; ";
		$jsOnChange .= "if(document.getElementById('filter__num__geolocfieldoperator').value != '='){ displayVal = 'no'; } ";
		$jsOnChange .= "displayCondFilter('displayValues', 'toChange__num__',__num__,'geolocMap='+searchMap+'&displayCond='+displayVal+'&value='+document.getElementById('filter__num__geolocfieldvalue').value); ";

		$operators = acymailing_get('type.operators');
		$operators->extra = 'onchange="'.$jsOnChange.'"';

		$return = '<div id="filter__num__geolocfield">'.acymailing_select($field, "filter[__num__][geolocfield][map]", 'onchange="'.$jsOnChange.'" class="inputbox" size="1"', 'value', 'text');
		$return .= ' '.$operators->display("filter[__num__][geolocfield][operator]").' <span id="toChange__num__"><input onchange="countresults(__num__)" class="inputbox" type="text" name="filter[__num__][geolocfield][value]" id="filter__num__geolocfieldvalue" style="width:200px" value=""></span></div>';
		return $return;
	}

	function onAcyTriggerFct_displayValues(){
		$num = acymailing_getVar('int', 'num');
		$geolocMap = acymailing_getVar('string', 'geolocMap');
		$displayCond = acymailing_getVar('string', 'displayCond');
		$value = acymailing_getVar('string', 'value');

		$emptyInputReturn = '<input onchange="countresults('.$num.')" class="inputbox" type="text" name="filter['.$num.'][geolocfield][value]" id="filter'.$num.'geolocfieldvalue" style="width:200px" value="'.$value.'">';

		$listType = array('geolocation_type', 'geolocation_postal_code', 'geolocation_country', 'geolocation_country_code', 'geolocation_state', 'geolocation_state_code', 'geolocation_city');
		if(!empty($geolocMap) && $displayCond == 'ok' && in_array($geolocMap, $listType)){
			$query = 'SELECT DISTINCT '.acymailing_secureField($geolocMap).' FROM #__acymailing_geolocation LIMIT 100';
			$geolocPropositions = acymailing_loadResultArray($query);

			if(empty($geolocPropositions) || count($geolocPropositions) >= 100 || (count($geolocPropositions) == 1 && (empty($geolocPropositions[0]) || $geolocPropositions[0] == '-'))) return $emptyInputReturn;

			$valueProp = array();
			foreach($geolocPropositions as $proposition){
				$element = new stdClass();
				$element->valueProp = $proposition;
				array_push($valueProp, $element);
			}
			return acymailing_select($valueProp, "filter[$num][geolocfield][value]", 'onchange="countresults('.$num.')" class="inputbox" size="1" style="width:200px"', 'valueProp', 'valueProp', $value, 'filter'.$num.'geolocfieldvalue');
		}else{ // free input
			return $emptyInputReturn;
		}
	}

	function onAcyDisplayFilter_geolocfield($filter){
		return acymailing_translation('GEOLOCATION').' : '.$filter['map'].' '.$filter['operator'].' '.$filter['value'];
	}

	function onAcyProcessFilter_geolocfield(&$query, $filter, $num){
		if(empty($filter['map'])){
			$query->where[] = '1=0';
			return;
		}
		$query->leftjoin['geolocfield'] = '#__acymailing_geolocation AS geol ON geol.geolocation_subid=sub.subid';

		$value = acymailing_replaceDate($filter['value']);
		if(!is_numeric($value) && ($filter['map'] == 'geolocation_created')) $value = strtotime($value);

		$query->where[] = $query->convertQuery('geol', $filter['map'], $filter['operator'], $value);
	}

	function onAcyProcessFilterCount_geolocfield(&$query, $filter, $num){
		$this->onAcyProcessFilter_geolocfield($query, $filter, $num);
		return acymailing_translation_sprintf('SELECTED_USERS', $query->count());
	}
}
