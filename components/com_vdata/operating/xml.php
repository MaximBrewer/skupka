<?php
/*------------------------------------------------------------------------
# com_vdata - vData 
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2016 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
define('PSI_INTERNAL_XML', true);

require_once APP_ROOT.'/includes/autoloader.inc.php';

$output = new WebpageXML(false, null);

if (isset($output) && is_object($output)) {
    if (isset(JFactory::getApplication()->input->get('json')) || isset(JFactory::getApplication()->input->get('jsonp')) {
        if (defined('PSI_JSON_ISSUE') && (PSI_JSON_ISSUE)) {
            $json = json_encode(simplexml_load_string(str_replace(">", ">\n", $output->getXMLString()))); // solving json_encode issue
        } else {
            $json = json_encode(simplexml_load_string($output->getXMLString()));
        }
        // check for jsonp with callback name restriction
        echo isset(JFactory::getApplication()->input->get('jsonp')) ? (!preg_match('/[^A-Za-z0-9_\?]/', JFactory::getApplication()->input->get('callback'))?JFactory::getApplication()->input->get('callback'):'') . '('.$json.')' : $json;
    } else {
        $output->run();
    }
}
