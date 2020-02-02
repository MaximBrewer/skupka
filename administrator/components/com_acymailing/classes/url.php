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

class urlClass extends acymailingClass{

	var $tables = array('urlclick','url');
	var $pkey = 'urlid';

	function get($urlid,$default = null){
		$column = is_numeric($urlid) ? 'urlid' : 'url';
		$query = 'SELECT * FROM '.acymailing_table('url').' WHERE '.$column.' = '.acymailing_escapeDB($urlid).' LIMIT 1';
		return acymailing_loadObject($query);
	}

	function getAdd($url){
		$currentURL = $this->get($url);
		if(empty($currentURL->urlid)){
			$currentURL = new stdClass();
			$currentURL->url = $url;
			$currentURL->name = $url;
			$currentURL->urlid = $this->save($currentURL);
		}

		return $currentURL;
	}

	function getCurrentUrl(){
		if(isset($_SERVER["REQUEST_URI"])){
			$requestUri = $_SERVER["REQUEST_URI"];
		}else{
			$requestUri = $_SERVER['PHP_SELF'];
			if (!empty($_SERVER['QUERY_STRING'])) $requestUri = rtrim($requestUri,'/').'?'.$_SERVER['QUERY_STRING'];
		}
		$currentURL = (((!empty($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS']) == "on") || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://').$_SERVER["HTTP_HOST"].$requestUri;

		return $currentURL;
	}

	function getAddCurrentUrl(){
		$currentURL = $this->getCurrentUrl();

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

		return $this->getAdd($currentURL);
	}

	function saveCurrentUrlName($urlid){
		$title = acymailing_getTitle();
		if(empty($title)) return;

		acymailing_query('UPDATE #__acymailing_url SET `name` = '.acymailing_escapeDB($title).' WHERE `urlid` = '.intval($urlid));
	}

	function getUrl($url,$mailid,$subid, $sef = true){
		static $allurls;

		$url = str_replace('&amp;','&',$url);
		if(empty($allurls[$url])){
			$currentURL = $this->getAdd($url);
			$allurls[$url] = $currentURL;
		}else{
			$currentURL = $allurls[$url];
		}

		$config = acymailing_config();
		$itemId = $config->get('itemid',0);
		$item = empty($itemId) ? '' : '&Itemid='.$itemId;

		if(empty($currentURL->urlid)) return;
		if($sef) {
			$newURL = str_replace('&amp;', '&', acymailing_frontendLink('url&subid='.$subid.'&urlid='.$currentURL->urlid.'&mailid='.$mailid.$item, true, false, true));
		}else{
			$newURL = 'index.php?option=com_acymailing&ctrl=url&subid='.$subid.'&urlid='.$currentURL->urlid.'&mailid='.$mailid.$item;
			$mainurl = acymailing_mainURL($newURL);
			$newURL = $mainurl.$newURL;
		}
		
		return $newURL;
	}

	function saveForm(){

		$object = new stdClass();
		$object->urlid = acymailing_getCID('urlid');

		$formData = acymailing_getVar('array',  'data', array(), '');

		foreach($formData['url'] as $column => $value){
			acymailing_secureField($column);
			$object->$column = strip_tags($value);
		}

		$urlid = $this->save($object);
		if(!$urlid) return false;

		$js = "document.addEventListener(\"DOMContentLoaded\", function(){
				var allLinks = window.parent.document.getElements('a[id^=urlink_".$urlid."_]');
				i=0;
				while(allLinks[i]){
					allLinks[i].innerHTML = '".str_replace(array("'",'"'),array("&#039;",'&quot;'),$object->name)."';
					i++;
				}
				acymailing.closeBox(true);
				})";

		acymailing_addScript(true, $js);

		return true;

	}

}
