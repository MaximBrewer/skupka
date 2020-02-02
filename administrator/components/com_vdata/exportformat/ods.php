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
    $plugin_list['ods'] = array(
        'text' => JText::_('Open Document Spreadsheet'),
        'extension' => 'ods',
        'mime_type' => 'application/vnd.oasis.opendocument.spreadsheet',
        'force_file' => true,
        'options' => array(
            array('type' => 'begin_group', 'name' => 'general_opts'),
            array('type' => 'text', 'name' => 'null', 'text' => JText::_('Replace NULL with:')),
            array('type' => 'bool', 'name' => 'columns', 'text' => JText::_('Put columns names in the first row')),
            array('type' => 'hidden', 'name' => 'structure_or_data'),
            array('type' => 'end_group'),
            ),
        'options_text' => JText::_('Options'),
        );
} else {

$GLOBALS['ods_buffer'] = '';
require_once './libraries/opendocument.lib.php';


function PMA_exportComment($text) {
    return TRUE;
}


function PMA_exportFooter() {
    $GLOBALS['ods_buffer'] .= '</office:spreadsheet>'
        . '</office:body>'
        . '</office:document-content>';
    if (!$this->PMA_exportOutputHandler(PMA_createOpenDocument('application/vnd.oasis.opendocument.spreadsheet', $GLOBALS['ods_buffer']))) {
        return FALSE;
    }
    return TRUE;
}


function PMA_exportHeader() {
    $GLOBALS['ods_buffer'] .= '<?xml version="1.0" encoding="' . $GLOBALS['charset'] . '"?' . '>'
        . '<office:document-content '. $GLOBALS['OpenDocumentNS'] . 'office:version="1.0">'
        . '<office:automatic-styles>'
            . '<number:date-style style:name="N37" number:automatic-order="true">'
            . '<number:month number:style="long"/>'
            . '<number:text>/</number:text>'
            . '<number:day number:style="long"/>'
            . '<number:text>/</number:text>'
            . '<number:year/>'
          . '</number:date-style>'
          . '<number:time-style style:name="N43">'
            . '<number:hours number:style="long"/>'
            . '<number:text>:</number:text>'
            . '<number:minutes number:style="long"/>'
            . '<number:text>:</number:text>'
            . '<number:seconds number:style="long"/>'
            . '<number:text> </number:text>'
            . '<number:am-pm/>'
          . '</number:time-style>'
          . '<number:date-style style:name="N50" number:automatic-order="true" number:format-source="language">'
            . '<number:month/>'
            . '<number:text>/</number:text>'
            . '<number:day/>'
            . '<number:text>/</number:text>'
            . '<number:year/>'
            . '<number:text> </number:text>'
            . '<number:hours number:style="long"/>'
            . '<number:text>:</number:text>'
            . '<number:minutes number:style="long"/>'
            . '<number:text> </number:text>'
            . '<number:am-pm/>'
          . '</number:date-style>'
          . '<style:style style:name="DateCell" style:family="table-cell" style:parent-style-name="Default" style:data-style-name="N37"/>'
          . '<style:style style:name="TimeCell" style:family="table-cell" style:parent-style-name="Default" style:data-style-name="N43"/>'
          . '<style:style style:name="DateTimeCell" style:family="table-cell" style:parent-style-name="Default" style:data-style-name="N50"/>'
        . '</office:automatic-styles>'
        . '<office:body>'
        . '<office:spreadsheet>';
    return TRUE;
}


function PMA_exportDBHeader($db) {
    return TRUE;
}


function PMA_exportDBFooter($db) {
    return TRUE;
}


function PMA_exportDBCreate($db) {
    return TRUE;
}


function PMA_exportData($db, $table, $crlf, $error_url, $sql_query) {
    global $what;

    // Gets the data from the database
    $result      = PMA_DBI_query($sql_query, null, PMA_DBI_QUERY_UNBUFFERED);
    $fields_cnt  = PMA_DBI_num_fields($result);
    $fields_meta = PMA_DBI_get_fields_meta($result);
    $field_flags = array();
    for ($j = 0; $j < $fields_cnt; $j++) {
        $field_flags[$j] = PMA_DBI_field_flags($result, $j);
    }

    $GLOBALS['ods_buffer'] .= '<table:table table:name="' . htmlspecialchars($table) . '">';

    // If required, get fields name at the first line
    if (isset($GLOBALS[$what . '_columns'])) {
        $GLOBALS['ods_buffer'] .= '<table:table-row>';
        for ($i = 0; $i < $fields_cnt; $i++) {
            $GLOBALS['ods_buffer'] .= '<table:table-cell office:value-type="string">'
                . '<text:p>' . htmlspecialchars(stripslashes(PMA_DBI_field_name($result, $i))) . '</text:p>'
                . '</table:table-cell>';
        } // end for
        $GLOBALS['ods_buffer'] .= '</table:table-row>';
    } // end if

    // Format the data
    while ($row = PMA_DBI_fetch_row($result)) {
        $GLOBALS['ods_buffer'] .= '<table:table-row>';
        for ($j = 0; $j < $fields_cnt; $j++) {
            if (!isset($row[$j]) || is_null($row[$j])) {
                $GLOBALS['ods_buffer'] .= '<table:table-cell office:value-type="string">'
                    . '<text:p>' . htmlspecialchars($GLOBALS[$what . '_null']) . '</text:p>'
                    . '</table:table-cell>';
            // ignore BLOB
            } elseif (stristr($field_flags[$j], 'BINARY')
                    && $fields_meta[$j]->blob) {
                $GLOBALS['ods_buffer'] .= '<table:table-cell office:value-type="string">'
                    . '<text:p></text:p>'
                    . '</table:table-cell>';
            } elseif ($fields_meta[$j]->type == "date") {
                $GLOBALS['ods_buffer'] .= '<table:table-cell office:value-type="date" office:date-value="' . date("Y-m-d", strtotime($row[$j])) . '" table:style-name="DateCell">'
                    . '<text:p>' . htmlspecialchars($row[$j]) . '</text:p>'
                    . '</table:table-cell>';
            } elseif ($fields_meta[$j]->type == "time") {
                $GLOBALS['ods_buffer'] .= '<table:table-cell office:value-type="time" office:time-value="' . date("\P\TH\Hi\Ms\S", strtotime($row[$j])) . '" table:style-name="TimeCell">'
                    . '<text:p>' . htmlspecialchars($row[$j]) . '</text:p>'
                    . '</table:table-cell>';
            } elseif ($fields_meta[$j]->type == "datetime") {
                $GLOBALS['ods_buffer'] .= '<table:table-cell office:value-type="date" office:date-value="' . date("Y-m-d\TH:i:s", strtotime($row[$j])) . '" table:style-name="DateTimeCell">'
                    . '<text:p>' . htmlspecialchars($row[$j]) . '</text:p>'
                    . '</table:table-cell>';
            } elseif ($fields_meta[$j]->numeric && $fields_meta[$j]->type != 'timestamp' && ! $fields_meta[$j]->blob) {
                $GLOBALS['ods_buffer'] .= '<table:table-cell office:value-type="float" office:value="' . $row[$j] . '" >'
                    . '<text:p>' . htmlspecialchars($row[$j]) . '</text:p>'
                    . '</table:table-cell>';
            } else {
                $GLOBALS['ods_buffer'] .= '<table:table-cell office:value-type="string">'
                    . '<text:p>' . htmlspecialchars($row[$j]) . '</text:p>'
                    . '</table:table-cell>';
            }
        } // end for
        $GLOBALS['ods_buffer'] .= '</table:table-row>';
    } // end while
    PMA_DBI_free_result($result);

    $GLOBALS['ods_buffer'] .= '</table:table>';

    return TRUE;
}

}
?>
