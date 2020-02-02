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

class urlclickClass extends acymailingClass{

	var $tables = array('urlclick');

	function addClick($urlid,$mailid,$subid){
		$mailid = intval($mailid);
		$urlid = intval($urlid);
		$subid = intval($subid);
		if(empty($mailid) OR empty($urlid) OR empty($subid)) return true;

		$statsClass = acymailing_get('class.stats');
		$statsClass->countReturn = false;
		$statsClass->mailid = $mailid;
		$statsClass->subid = $subid;
		if(!$statsClass->saveStats()) return true;

		$date = time();
		$config = acymailing_config();
		if($config->get('anonymous_tracking', 0) == 0) {
			$ipClass = acymailing_get('helper.user');
			$ip = $ipClass->getIP();
		}else{
			$ip = '';  
			$subid = 0;
		}

		$query = 'INSERT IGNORE INTO '.acymailing_table('urlclick').' (urlid,mailid,subid,date,click,ip) VALUES ('.$urlid.','.$mailid.','.$subid.','.$date.',1,'.acymailing_escapeDB($ip).')';
		$affected = acymailing_query($query);
		if($affected === false){
			acymailing_display(acymailing_getDBError(),'error');
			exit;
		}

		if(!$affected){
			$query = 'UPDATE '.acymailing_table('urlclick').' SET click = click +1 WHERE mailid = '.$mailid.' AND urlid = '.$urlid.' AND subid = '.$subid.' LIMIT 1';
			acymailing_query($query);
		}

		$query = 'SELECT SUM(click) FROM '.acymailing_table('urlclick').' WHERE mailid = '.$mailid.' AND subid = '.$subid;
		$totalUserClick = acymailing_loadResult($query);

		$query = 'UPDATE '.acymailing_table('stats').' SET clicktotal = clicktotal + 1 ';
		if($totalUserClick <= 1){
			$query .= ' , clickunique = clickunique + 1';
		}
		$query .= ' WHERE mailid = '.$mailid.' LIMIT 1';
		acymailing_query($query);

		acymailing_query('UPDATE #__acymailing_subscriber SET lastclick_date = '.time().' WHERE subid = '.$subid);

		$filterClass = acymailing_get('class.filter');
		$filterClass->subid = $subid;
		$filterClass->trigger('clickurl');

		$classGeoloc = acymailing_get('class.geolocation');
		$classGeoloc->saveGeolocation('clic', $subid);

		acymailing_importPlugin('acymailing');
		acymailing_trigger('onAcyClickLink', array($subid,$mailid,$urlid));

		return true;
	}

}
