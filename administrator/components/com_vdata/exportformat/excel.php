<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
if (isset($plugin_list)) {
    $plugin_list['excel'] = array(
        'text' => JText::_('CSV for MS Excel'),
        'extension' => 'csv',
        'mime_type' => 'text/comma-separated-values',
        'options' => array(
            array('type' => 'begin_group', 'name' => 'general_opts'),
            array('type' => 'text', 'name' => 'null', 'text' => JText::_('Replace NULL with:')),
            array('type' => 'bool', 'name' => 'removeCRLF', 'text' => JText::_('Remove carriage return/line feed characters within columns')),
            array('type' => 'bool', 'name' => 'columns', 'text' => JText::_('Put columns names in the first row')),
            array(
                'type' => 'select', 
                'name' => 'edition', 
                'values' => array(
                    'win' => 'Windows',
                    'mac_excel2003' => 'Excel 2003 / Macintosh', 
                    'mac_excel2008' => 'Excel 2008 / Macintosh'), 
                'text' => JText::_('Excel edition:')),
            array('type' => 'hidden', 'name' => 'structure_or_data'),
            array('type' => 'end_group'),
            ),
        'options_text' => JText::_('Options'),
        );
} else {
    /* Everything rest is coded in csv plugin */
    require './libraries/export/csv.php';
}
?>
