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

class plgSystemAcymailingurltracker extends JPlugin
{
	function __construct(&$subject, $config){
		parent::__construct($subject, $config);
	}

	function onAfterInitialise(){
		if(empty($_REQUEST['acm'])) return;

		$acm = $_REQUEST['acm'];
		if(!preg_match('#^[0-9]+_[0-9]+$#',$acm)) return;

		$helperFile = rtrim(JPATH_ADMINISTRATOR,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_acymailing'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php';
		if(!file_exists($helperFile) || !include_once($helperFile)) return;

		$vals = explode('_',$acm);

		$urlClass = acymailing_get('class.url');
		$urlObject = $urlClass->getAddCurrentUrl();

		if(empty($urlObject->urlid)) return;

		$urlClickClass = acymailing_get('class.urlclick');
		$urlClickClass->addClick($urlObject->urlid,$vals[1],$vals[0]);

		if($urlObject->url == $urlObject->name){
			$urlid = $urlObject->urlid;
		}

		unset($_GET['acm']);
		unset($_GET['idU']);
		unset($_REQUEST['acm']);
		unset($_REQUEST['idU']);

		$currentURL = $urlClass->getCurrentUrl();
		if(!empty($currentURL) && strpos($currentURL,'#') === false){
			$oldUrl = $currentURL;
			$currentURL = preg_replace('#(\?|&|\/)(acm|idU)[=:-][^&\/]*#i','',$currentURL);
			if($oldUrl != $currentURL){
				if(!strpos($currentURL,'?') && strpos($currentURL,'&')){
					$firstpos = strpos($currentURL,'&');
					$currentURL = substr($currentURL,0,$firstpos).'?'.substr($currentURL,$firstpos+1);
				}

				if(!empty($urlid)) $currentURL .= (strpos($currentURL,'?') ? '&' : '?').'auid='.$urlid;
				acymailing_redirect($currentURL);
			}
		}
	}

	function onAfterRender(){
		if(empty($_GET['auid'])) return;

		if(!function_exists('acymailing_get')){
			$helperFile = rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_acymailing'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php';
			if(!file_exists($helperFile) || !include_once($helperFile)) return;
		}

		$urlClass = acymailing_get('class.url');
		$urlClass->saveCurrentUrlName($_GET['auid']);
	}
}//endclass
