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

class plgAcymailingUrltracker extends JPlugin
{

    function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        if (!isset($this->params)) {
            $plugin = JPluginHelper::getPlugin('acymailing', 'urltracker');
            $this->params = new acyParameter($plugin->params);
        }
    }

    function acymailing_replaceusertags(&$email, &$user, $send = true)
    {
        $this->currentUser = $user;
        $this->acymailing_replacetags($email, $send);
        $this->currentUser = null;
    }

    function acymailing_replacetags(&$email, $send = true)
    {

        if (empty($email->type) || !in_array($email->type, array('news', 'autonews', 'followup', 'welcome', 'unsub', 'joomlanotification', 'action')) || !acymailing_level(1)) {
            return;
        }

        $urlClass = acymailing_get('class.url');
        if ($urlClass === null) {
            return;
        }

        $urls = array();
        $altUrls = array();

        $config = acymailing_config();
        $trackingSystemExternalWebsite = $config->get('trackingsystemexternalwebsite', 1);
        preg_match_all('#href[ ]*=[ ]*"(?!mailto:|\#|ymsgr:|callto:|file:|ftp:|webcal:|skype:|tel:)([^"]+)"#Ui', $email->body, $results);

        if (empty($results)) {
            return;
        }

        $countLinks = array_count_values($results[1]);
        if (array_product($countLinks) != 1) {
            foreach ($results[1] as $key => $url) {
                if ($countLinks[$url] == 1) {
                    continue;
                }
                $countLinks[$url]--;

                $toAddUrl = (strpos($url, '?') === false ? '?' : '&').'idU='.$countLinks[$url];

                $posHash = strpos($url, '#');
                if ($posHash !== false) {
                    $newURL = substr($url, 0, $posHash).$toAddUrl.substr($url, $posHash);
                } elseif (strpos($url, '{/acyfrontsef}') !== false) {
                    $newURL = str_replace('{/acyfrontsef}', $toAddUrl.'{/acyfrontsef}', $url);
                } else {
                    $newURL = $url.$toAddUrl;
                }

                $email->body = preg_replace('#href="('.preg_quote($url, '#').')"#Uis', 'href="'.$newURL.'"', $email->body, 1);
                $email->altbody = preg_replace('#\( ('.preg_quote($url, '#').') \)#Uis', '( '.$newURL.' )', $email->altbody, 1);

                $results[0][$key] = 'href="'.$newURL.'"';
                $results[1][$key] = $newURL;
            }
        }

        foreach ($results[1] as $i => $url) {
            if (strpos($url, 'acm=') !== false || strpos($url, 'ctrl=url') !== false || strpos($url, '/urlid-') !== false) {
                continue;
            }
            if (isset($urls[$results[0][$i]]) || strpos($url, 'task=out')) {
                continue;
            }

            $simplifiedUrl = str_replace(array('https://', 'http://', 'www.'), '', $url);
            $simplifiedWebsite = str_replace(array('https://', 'http://', 'www.'), '', ACYMAILING_LIVE);
            $internalUrl = strpos($simplifiedUrl, rtrim($simplifiedWebsite, '/')) === 0;

            $isFile = false;
            if ($internalUrl) {
                $path = str_replace('/', DS, str_replace($simplifiedWebsite, '', $simplifiedUrl));
                if (!empty($path) && $path != 'index.php' && $path != 'index2.php' && @file_exists(ACYMAILING_ROOT.DS.$path)) {
                    $isFile = true;
                }
            }

            $subfolder = false;
            if ($internalUrl) {
                $urlWithoutBase = str_replace($simplifiedWebsite, '', $simplifiedUrl);
                if (strpos($urlWithoutBase, '/') || strpos($urlWithoutBase, '?')) {
                    $folderName = substr($urlWithoutBase, 0, strpos($urlWithoutBase, '/') == false ? strpos($urlWithoutBase, '?') : strpos($urlWithoutBase, '/'));
                    if (strpos($folderName, '.') === false) {
                        $subfolder = @is_dir(ACYMAILING_ROOT.$folderName);
                    }
                }
            }

            $trackingSystem = $config->get('trackingsystem', 'acymailing');

            if (strpos($url, 'utm_source') === false && !$isFile && strpos($trackingSystem, 'google') !== false) {
                if ((!$internalUrl || $subfolder) && $trackingSystemExternalWebsite != 1) {
                    continue;
                }
                $args = array();
                $args[] = 'utm_source=newsletter_'.@$email->mailid;
                $args[] = 'utm_medium=email';
                $args[] = 'utm_campaign='.@$email->alias;
                $anchor = '';
                if (strpos($url, '#') !== false) {
                    $anchor = substr($url, strpos($url, '#'));
                    $url = substr($url, 0, strpos($url, '#'));
                }

                if (strpos($url, '?')) {
                    $utmargs = '&'.implode('&', $args);
                } else {
                    $utmargs = '?'.implode('&', $args);
                }

                $sefpos = strpos($url, '{/acyfrontsef}');
                if($sefpos === false){
                    $mytracker = $url.$utmargs;
                }else{
                    $mytracker = substr($url, 0, $sefpos).$utmargs.substr($url, $sefpos);
                }

                $mytracker .= $anchor;
                $urls[$results[0][$i]] = str_replace($results[1][$i], $mytracker, $results[0][$i]);
                $altUrls['( '.str_replace('&amp;', '&', $results[1][$i]).' )'] = '( '.$mytracker.' )';

                $url = $mytracker;
            }

            if (strpos($trackingSystem, 'acymailing') !== false) {
                if (strpos($url, 'acyfrontsef') === false && (!$internalUrl || $isFile || strpos($url, '#') !== false || $subfolder)) {
                    if ($trackingSystemExternalWebsite != 1) {
                        continue;
                    }
                    if (preg_match('#subid|passw|modify|\{|%7B#i', $url)) {
                        continue;
                    }
                    if (empty($this->currentUser)) {
                        $mytracker = $urlClass->getUrl($url, $email->mailid, '{subtag:subid}');
                    } else {
                        $mytracker = $urlClass->getUrl($url, $email->mailid, $this->currentUser->subid, false);
                    }
                } else {
                    if (preg_match('#=out&|/out/#i', $url)) {
                        continue;
                    }
                    if (empty($this->currentUser)) {
                        $extraParam = 'acm={subtag:subid}_'.$email->mailid;
                    } else {
                        $extraParam = 'acm='.$this->currentUser->subid.'_'.$email->mailid;
                    }
                    if (strpos($url, '#')) {
                        $before = substr($url, 0, strpos($url, '#'));
                        $after = substr($url, strpos($url, '#'));
                    } elseif (strpos($url, '{/acyfrontsef}') !== false) {
                        $before = substr($url, 0, strpos($url, '{/acyfrontsef}'));
                        $after = substr($url, strpos($url, '{/acyfrontsef}'));
                    } else {
                        $before = $url;
                        $after = '';
                    }
                    $mytracker = $before.(strpos($before, '?') ? '&' : '?').$extraParam.$after;
                }

                if (empty($mytracker)) {
                    continue;
                }
                $urls[$results[0][$i]] = str_replace($results[1][$i], $mytracker, $results[0][$i]);
                $altUrls['( '.str_replace('&amp;', '&', $results[1][$i]).' )'] = '( '.$mytracker.' )';
            }
        }

        $email->body = str_replace(array_keys($urls), $urls, $email->body);
        $email->altbody = str_replace(array_keys($altUrls), $altUrls, $email->altbody);
    }//endfct

    function onAcyDisplayTriggers(&$triggers)
    {
        $triggers['clickurl'] = acymailing_translation('ON_USER_CLICK');
    }

    function onAcyDisplayFilters(&$type, $context = "massactions")
    {

        if ($this->params->get('displayfilter_'.$context, true) == false) {
            return;
        }

        $allemails = acymailing_loadObjectList("SELECT `mailid`,CONCAT(`subject`,' ( ',`mailid`,' )') as 'value' FROM `#__acymailing_mail` WHERE `type` IN('news', 'autonews', 'followup', 'welcome', 'unsub', 'joomlanotification', 'action') ORDER BY `senddate` DESC LIMIT 5000");
        if (empty($allemails)) {
            return;
        }
        $element = new stdClass();
        $element->mailid = 0;
        $element->value = acymailing_translation('EMAIL_NAME');
        array_unshift($allemails, $element);

        $type['clickstats'] = acymailing_translation('CLICK_STATISTICS');

        $jsOnChange = 'if(document.getElementById(\'filter__num__clickstats_urlid\')){ document.getElementById(\'filter__num__clickstats_urlid\').value=\'all\';}';
        $jsOnChange .= 'displayCondFilter(\'changeList\', \'toChange__num__\',__num__,\'mailid=\'+document.getElementById(\'filter__num__clickstats_mailid\').value);';

        $return = '<div id="filter__num__clickstats">'.acymailing_select($allemails, "filter[__num__][clickstats][mailid]", 'onchange="'.$jsOnChange.'" class="inputbox" size="1" style="max-width:200px"', 'mailid', 'value', null, 'filter__num__clickstats_mailid');

        $clicked = array();
        $clicked[] = acymailing_selectOption(0, acymailing_translation('CLICKED_LINK'));
        $clicked[] = acymailing_selectOption(1, acymailing_translation('ACY_NOT_CLICK'));

        $return .= acymailing_select($clicked, "filter[__num__][clickstats][clicked]", 'onchange="countresults(__num__);" class="inputbox" size="1" style="width:110px"', 'value', 'text', 0);
        $return .= ' <span id="toChange__num__"><input type="text" name="filter[__num__][clickstats][urlid]" value="0" readonly="readonly"/></span></div>';

        return $return;
    }

    function onAcyTriggerFct_changeList()
    {
        $mailid = acymailing_getVar('none', 'mailid');
        $num = acymailing_getVar('int', 'num');
        if ($mailid == 0) {
            $queryUrl = "SELECT urlid, CONCAT(name, ' ( ',urlid,' )') AS 'name' FROM #__acymailing_url WHERE SUBSTRING(`name`,1,230) != SUBSTRING(`url`,1,230) ORDER BY name ASC";
        } else {
            $queryUrl = "SELECT u.urlid, CONCAT(u.name, ' ( ',u.urlid,' )') AS 'name' FROM #__acymailing_url AS u LEFT JOIN #__acymailing_urlclick AS uc ON u.urlid=uc.urlid WHERE uc.mailid=".intval($mailid)." GROUP BY u.urlid ORDER BY u.name ASC";
        }
        $allurls = acymailing_loadObjectList($queryUrl);

        $element = new stdClass();
        $element->urlid = 'all';
        $element->name = acymailing_translation('ALL_URLS');
        array_unshift($allurls, $element);

        return acymailing_select($allurls, "filter[".$num."][clickstats][urlid]", 'onchange="countresults('.$num.')" class="inputbox" size="1" style="width:150px;"', 'urlid', 'name', null, 'filter'.$num.'clickstats_urlid');
    }

    function onAcyProcessFilterCount_clickstats(&$query, $filter, $num)
    {
        $this->onAcyProcessFilter_clickstats($query, $filter, $num);

        return acymailing_translation_sprintf('SELECTED_USERS', $query->count());
    }

    function onAcyDisplayFilter_clickstats($filter)
    {
        if (empty($filter['mailid'])) {
            $mailName = acymailing_translation('EMAIL_NAME');
        } else {
            $mailClass = acymailing_get('class.mail');
            $mail = $mailClass->get($filter['mailid']);
            $mailName = $mail->subject;
        }

        $clickTxt = array(0 => acymailing_translation('CLICKED_LINK'), 1 => acymailing_translation('ACY_NOT_CLICK'));
        if ($filter['urlid'] == 'all') {
            $urlName = acymailing_translation('ALL_URLS');
        } else {
            $urlClass = acymailing_get('class.url');
            $url = $urlClass->get($filter['urlid']);
            $urlName = $url->name.' ( '.$url->urlid.' )';
        }

        return acymailing_translation('CLICK_STATISTICS').': '.$mailName.' '.$clickTxt[$filter['clicked']].' '.$urlName;
    }

    function onAcyProcessFilter_clickstats(&$query, $filter, $num)
    {
        $alias = 'url'.$num;
        $join = '#__acymailing_urlclick AS '.$alias.' ON sub.subid = '.$alias.'.subid';
        if (intval($filter['mailid']) != 0) {
            $join .= ' AND '.$alias.'.mailid = '.intval($filter['mailid']);
        }

        if ($filter['urlid'] != 'all' && intval($filter['urlid']) != 0) {
            $join .= ' AND '.$alias.'.urlid = '.intval($filter['urlid']);
        }

        if (empty($filter['clicked'])) {
            $query->join[$alias] = $join;
        } else { // if == 1 => select the users that didn't click
            $query->leftjoin[$alias] = $join;
            $query->where[$alias] = $alias.'.subid IS NULL';
        }
    }
}//endclass
