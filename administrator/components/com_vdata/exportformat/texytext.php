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
if (isset($plugin_list)) {
    $plugin_list['texytext'] = array(
        'text' => JText::_('Texy! text'),
        'extension' => 'txt',
        'mime_type' => 'text/plain',
        'options' => array(
        /* what to dump (structure/data/both) */
        array('type' => 'begin_group', 'text' => JText::_('Dump table'), 'name' => 'general_opts'),
        array('type' => 'radio', 'name' => 'structure_or_data', 'values' => array('structure' => JText::_('structure'), 'data' => JText::_('data'), 'structure_and_data' => JText::_('structure and data'))),
        array('type' => 'end_group'),
        array('type' => 'begin_group', 'name' => 'data', 'text' => JText::_('Data dump options'), 'force' => 'structure'),
        array('type' => 'text', 'name' => 'null', 'text' => JText::_('Replace NULL by')),
        array('type' => 'bool', 'name' => 'columns', 'text' => JText::_('Put columns names in the first row')),
        array('type' => 'end_group'),
        ),
        'options_text' => JText::_('Options'),
        );
} else {


function PMA_exportComment($text) {
    return TRUE;
}


function PMA_exportFooter() {
    return true;
}


function PMA_exportHeader() {
    return true;
}


function PMA_exportDBHeader($db) {
    return PMA_exportOutputHandler('===' . JText::_('Database') . ' ' . $db . "\n\n");
}


function PMA_exportDBFooter($db) {
    return TRUE;
}


function PMA_exportDBCreate($db) {
    return TRUE;
}


function PMA_exportData($db, $table, $crlf, $error_url, $sql_query)
{
    global $what;
    $data = JFactory::getApplication()->input->post->getArray(); 
    if (! $this->PMA_exportOutputHandler('== ' . JText::_('Dumping data for table') . ' ' . $table . "\n\n")) {
        return FALSE;
    }

    // Gets the data from the database
    $result      = $dbs->setQuery($sql_query)->loadObjectList();
    $fields_cnt     = $dbs->setQuery('SHOW COLUMNS FROM '.PMA_backquote($db).'.'.PMA_backquote($table))->loadObjectList();

    // If required, get fields name at the first line
    if (isset($data[$what . '_columns'])) {
        $text_output = "|------\n";
        for ($i = 0; $i < count($fields_cnt); $i++) {
			
            $text_output .= '|' . htmlspecialchars(stripslashes($fields_cnt[$i]));
        } // end for
        $text_output .= "\n|------\n";
        if (! $this->PMA_exportOutputHandler($text_output)) {
            return FALSE;
        }
    } // end if
jexit();
    // Format the data
    while ($row = PMA_DBI_fetch_row($result)) {
        $text_output = '';
        for ($j = 0; $j < $fields_cnt; $j++) {
            if (! isset($row[$j]) || is_null($row[$j])) {
                $value = $GLOBALS[$what . '_null'];
            } elseif ($row[$j] == '0' || $row[$j] != '') {
                $value = $row[$j];
            } else {
                $value = ' ';
            }
            $text_output .= '|' . htmlspecialchars($value);
        } // end for
        $text_output .= "\n";
        if (! $this->PMA_exportOutputHandler($text_output)) {
            return FALSE;
        }
    } // end while
    PMA_DBI_free_result($result);

    return TRUE;
}

function PMA_exportStructure($db, $table, $crlf, $error_url, $do_relation = false, $do_comments = false, $do_mime = false, $dates = false, $dummy)
{
    global $cfgRelation;
	$dbs = JFactory::getDbo();
    $data = JFactory::getApplication()->input->post->getArray(); 
    if (! PMA_exportOutputHandler('== ' . JText::_('Table structure for table') . ' ' .$table . "\n\n")) {
        return FALSE;
    }

   
	 $result      = $dbs->setQuery($sql_query)->loadObjectList();
    $fields_cnt     = $dbs->setQuery('SHOW COLUMNS FROM '.PMA_backquote($db).'.'.PMA_backquote($table))->loadObjectList();
    $keys_result     = $dbs->setQuery('SHOW KEYS FROM ' . PMA_backquote($table) . ' FROM '. PMA_backquote($db))->loadObjectList();
    //$keys_result    = PMA_DBI_query($keys_query);
    $unique_keys    = array();
    for($i=0;$i<count($keys_result);$i++){
		$key = $keys_result[$i];
        if ($key->Non_unique == 0) {
            $unique_keys[] = $key->Column_name;
        }
    }
    PMA_DBI_free_result($keys_result);

    /**
     * Gets fields properties
     */
    PMA_DBI_select_db($db);
    $result = $dbs->setQuery('SHOW FIELDS FROM ' . PMA_backquote($db) . '.' . PMA_backquote($table))->loadObjectList();
    
    $fields_cnt  = PMA_DBI_num_rows($result);

    // Check if we can use Relations (Mike Beck)
    if ($do_relation && ! empty($cfgRelation['relation'])) {
        // Find which tables are related with the current one and write it in
        // an array
        $res_rel = PMA_getForeigners($db, $table);

        if ($res_rel && count($res_rel) > 0) {
            $have_rel = TRUE;
        } else {
            $have_rel = FALSE;
        }
    } else {
           $have_rel = FALSE;
    } // end if

    

    $columns_cnt = 4;
    if ($do_relation && $have_rel) {
        $columns_cnt++;
    }
    if ($do_comments && $cfgRelation['commwork']) {
        $columns_cnt++;
    }
    if ($do_mime && $cfgRelation['mimework']) {
        $columns_cnt++;
    }

    $text_output = "|------\n";
    $text_output .= '|' . htmlspecialchars(JText::_('Column'));
    $text_output .= '|' . htmlspecialchars(JText::_('Type'));
    $text_output .= '|' . htmlspecialchars(JText::_('Null'));
    $text_output .= '|' . htmlspecialchars(JText::_('Default'));
    if ($do_relation && $have_rel) {
        $text_output .= '|' . htmlspecialchars(JText::_('Links to'));
    }
    if ($do_comments) {
        $text_output .= '|' . htmlspecialchars(JText::_('Comments'));
        $comments = PMA_getComments($db, $table);
    }
    if ($do_mime && $cfgRelation['mimework']) {
        $text_output .= '|' . htmlspecialchars('MIME');
        $mime_map = PMA_getMIME($db, $table, true);
    }
    $text_output .= "\n|------\n";

    if (! $this->PMA_exportOutputHandler($text_output)) {
        return FALSE;
    }

    foreach ($result as $row) {

        $text_output = '';
        $type             = $row['Type'];
        // reformat mysql query output
        // set or enum types: slashes single quotes inside options
        if (preg_match('/^(set|enum)\((.+)\)$/i', $type, $tmp)) {
            $tmp[2]       = substr(preg_replace('/([^,])\'\'/', '\\1\\\'', ',' . $tmp[2]), 1);
            $type         = $tmp[1] . '(' . str_replace(',', ', ', $tmp[2]) . ')';
            $type_nowrap  = '';

            $binary       = 0;
            $unsigned     = 0;
            $zerofill     = 0;
        } else {
            $type_nowrap  = ' nowrap="nowrap"';
            $type         = preg_replace('/BINARY/i', '', $type);
            $type         = preg_replace('/ZEROFILL/i', '', $type);
            $type         = preg_replace('/UNSIGNED/i', '', $type);
            if (empty($type)) {
                $type     = '&nbsp;';
            }

            $binary       = preg_match('/BINARY/i', $row['Type']);
            $unsigned     = preg_match('/UNSIGNED/i', $row['Type']);
            $zerofill     = preg_match('/ZEROFILL/i', $row['Type']);
        }
        $attribute     = '&nbsp;';
        if ($binary) {
            $attribute = 'BINARY';
        }
        if ($unsigned) {
            $attribute = 'UNSIGNED';
        }
        if ($zerofill) {
            $attribute = 'UNSIGNED ZEROFILL';
        }
        if (! isset($row['Default'])) {
            if ($row['Null'] != 'NO') {
                $row['Default'] = 'NULL';
            }
        } else {
            $row['Default'] = $row['Default'];
        }

        $fmt_pre = '';
        $fmt_post = '';
        if (in_array($row['Field'], $unique_keys)) {
            $fmt_pre = '**' . $fmt_pre;
            $fmt_post = $fmt_post . '**';
        }
        if ($row['Key']=='PRI') {
            $fmt_pre = '//' . $fmt_pre;
            $fmt_post = $fmt_post . '//';
        }
        $text_output .= '|' . $fmt_pre . htmlspecialchars($row['Field']) . $fmt_post;
        $text_output .= '|' . htmlspecialchars($type);
        $text_output .= '|' . htmlspecialchars(($row['Null'] == '' || $row['Null'] == 'NO') ? JText::_('No') : JText::_('Yes'));
        $text_output .= '|' . htmlspecialchars(isset($row['Default']) ? $row['Default'] : '');

        $field_name = $row['Field'];

        if ($do_relation && $have_rel) {
            $text_output .= '|' . (isset($res_rel[$field_name]) ? htmlspecialchars($res_rel[$field_name]['foreign_table'] . ' (' . $res_rel[$field_name]['foreign_field'] . ')') : '');
        }
        if ($do_comments && $cfgRelation['commwork']) {
            $text_output .= '|' . (isset($comments[$field_name]) ? htmlspecialchars($comments[$field_name]) : '');
        }
        if ($do_mime && $cfgRelation['mimework']) {
            $text_output .= '|' . (isset($mime_map[$field_name]) ? htmlspecialchars(str_replace('_', '/', $mime_map[$field_name]['mimetype'])) : '');
        }

        $text_output .= "\n";

        if (! $this->PMA_exportOutputHandler($text_output)) {
            return FALSE;
        }
    } // end while
    PMA_DBI_free_result($result);

    return true;
}

}
?>
