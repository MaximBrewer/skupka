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
    $plugin_list['csv'] = array(
        'text' => JText::_('CSV'),
        'extension' => 'csv',
        'mime_type' => 'text/comma-separated-values',
        'options' => array(
            array('type' => 'begin_group', 'name' => 'general_opts'),
            array('type' => 'text', 'name' => 'separator', 'text' => JText::_('Columns separated with:')),
            array('type' => 'text', 'name' => 'enclosed', 'text' => JText::_('Columns enclosed with:')),
            array('type' => 'text', 'name' => 'escaped', 'text' => JText::_('Columns escaped with:')),
            array('type' => 'text', 'name' => 'terminated', 'text' => JText::_('Lines terminated with:')),
            array('type' => 'text', 'name' => 'null', 'text' => JText::_('Replace NULL with:')),
            array('type' => 'bool', 'name' => 'removeCRLF', 'text' => JText::_('Remove carriage return/line feed characters within columns')),
            array('type' => 'bool', 'name' => 'columns', 'text' => JText::_('Put columns names in the first row')),
            array('type' => 'hidden', 'name' => 'structure_or_data'),
            array('type' => 'end_group'),
            ),
        'options_text' => JText::_('Options'),
        );
} else {


function PMA_exportComment($text) {
    $data  = JFactory::getApplication()->input->post->getArray();
	if (isset($data['sql_include_comments']) && $data['sql_include_comments']) {
        // see http://dev.mysql.com/doc/refman/5.0/en/ansi-diff-comments.html
        return '--' . (empty($text) ? '' : ' ') . $text;
    } else {
        return '';
    }
}


function PMA_exportFooter() {
    return TRUE;
}


function PMA_exportHeader() {
    global $what;
    global $csv_terminated;
    global $csv_separator;
    global $csv_enclosed;
    global $csv_escaped;
    $data  = JFactory::getApplication()->input->post->getArray();
    // Here we just prepare some values for export
    if ($what == 'excel') {
        $csv_terminated      = "\015\012";
        switch($data['excel_edition']) {
        case 'win':
            // as tested on Windows with Excel 2002 and Excel 2007
            $csv_separator = ';';
            break;
        case 'mac_excel2003':
            $csv_separator = ';';
            break;
        case 'mac_excel2008':
            $csv_separator = ',';
            break;
        }
        $csv_enclosed           = '"';
        $csv_escaped            = '"';
        if (isset($data['excel_columns'])) {
            $data['csv_columns'] = 'yes';
        }
    } else {
        if (empty($csv_terminated) || strtolower($csv_terminated) == 'auto') {
            $csv_terminated  = "\n";
        } else {
            $csv_terminated  = str_replace('\\r', "\015", $csv_terminated);
            $csv_terminated  = str_replace('\\n', "\012", $csv_terminated);
            $csv_terminated  = str_replace('\\t', "\011", $csv_terminated);
        } // end if
        $csv_separator          = str_replace('\\t', "\011", $csv_separator);
    }
    return TRUE;
}


function PMA_exportDBHeader($db) {
     return TRUE;
}


function PMA_exportDBFooter($db) {
     return TRUE;
}


function PMA_exportDBCreate($db) {
    
    return true;
}


function PMA_exportData($db, $table, $crlf, $error_url, $sql_query) {
    global $what;
    global $csv_terminated;
    global $csv_separator;
    global $csv_enclosed;
    global $csv_escaped;
    
    // Gets the data from the database
	$dbs = CheckDatabase();
	$data = JFactory::getApplication()->input->post->getArray();
	
    $result      = $dbs->setQuery($sql_query)->loadRowList();
    $fields_cnt  = array_keys($dbs->setQuery($sql_query)->loadAssoc()); 
	$csv_terminated = isset($data['csv_terminated'])&& $data['csv_terminated']=='AUTO'?"\n":'"'.$data['csv_terminated'].'"';
    $csv_separator = $data['csv_separator'];
    $csv_enclosed = $data['csv_enclosed'];
    $csv_escaped = $data['csv_escaped'];
    $what = $data['what'];
    // If required, get fields name at the first line
    if (isset($data['csv_columns'])) {
        $schema_insert = '';
        for($i=0;$i<count($fields_cnt);$i++){
            if ($csv_enclosed == '') {
                $schema_insert .= stripslashes($fields_cnt[$i]);
            } else {
                $schema_insert .= $csv_enclosed
                               . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, stripslashes($fields_cnt[$i]))
                               . $csv_enclosed;
            }
            $schema_insert     .= $csv_separator;
        } // end for
        $schema_insert     = trim(substr($schema_insert, 0, -1));
		
        if (!PMA_exportOutputHandler($schema_insert . "\n")) {
            return FALSE;
        }
		//PMA_exportOutputHandler('');
    } // end if
	$checked_value = isset($data['checked_value'])&& $data['checked_value']!=''?explode(',', $data['checked_value']):array();
	$checked_id = isset($data['checked_id'])&& $data['checked_id']!=''?$data['checked_id']:'';
	$key_checke_id = array_search($checked_id, $fields_cnt);
    // Format the data
    foreach ($result as $row) {
        $schema_insert = '';
		$need_break = 1;
        for ($j = 0; $j < count($fields_cnt); $j++) {
			if(count($checked_value)>0)
			{
			if(!in_array($row[$key_checke_id], $checked_value))	{
				$need_break = 0;
			break;	
			}
				
			}
		  
            if (!isset($row[$j]) || is_null($row[$j])) {
                $schema_insert .= $data[$what . '_null'];
            } elseif ($row[$j] == '0' || $row[$j] != '') {
                // always enclose fields
                if ($what == 'excel') {
                    $row[$j]       = preg_replace("/\015(\012)?/", "\012", $row[$j]);
                }
                // remove CRLF characters within field
                if (isset($data[$what . '_removeCRLF']) && $data[$what . '_removeCRLF']) {
                    $row[$j] = str_replace("\n", "", str_replace("\r", "", $row[$j]));
                }
                if ($csv_enclosed == '') {
                    $schema_insert .= $row[$j];
                } else {
                    // also double the escape string if found in the data
                    if ('csv' == $what) {
                        $schema_insert .= $csv_enclosed
                                   . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, str_replace($csv_escaped, $csv_escaped . $csv_escaped, $row[$j]))
                                   . $csv_enclosed;
                    } else {
                        // for excel, avoid a problem when a field contains
                        // double quotes
                        $schema_insert .= $csv_enclosed
                                   . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $row[$j])
                                   . $csv_enclosed;
                    }
                }
            } else {
                $schema_insert .= '';
            }
            if ($j < count($fields_cnt)-1) {
                $schema_insert .= $csv_separator;
            }
        } 
	   if($need_break==1){
        if (!PMA_exportOutputHandler($schema_insert . "\n")) {
            return FALSE;
        }
	   }	
    } // end while
    //PMA_DBI_free_result($result);
    
    return TRUE;
} // end of the 'PMA_getTableCsv()' function

function CheckDatabase()
	{
	
	$driver_object=json_decode(getVdatConfig());
	
	if(isset($driver_object->local_db) && $driver_object->local_db==0){
			$option = array(); 
				try
				{
					$option['driver']   = $driver_object->driver;            
					$option['host']     = $driver_object->host;  
					$option['user']     = $driver_object->user;  
					$option['password'] = $driver_object->password; 
					$option['database'] = $driver_object->database; 
					$option['prefix']   = $driver_object->dbprefix;  
					$db = JDatabaseDriver::getInstance( $option );
					$db->connect();
					return $db;
				
				}
				catch (RuntimeException $e)
				{                   
					$mainfram = JFactory::getApplication();
					$msg = JText::_($e->getMessage());
					$mainfram->redirect('index.php?option=com_vdata&view=config',$msg);
				
				}   
		   }
		   else
		   {
			   $db = JFactory::getDbo();
			   return $db;
		   }
	}


function getVdatConfig()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('dbconfig'));
        $query->from($db->quoteName('#__vd_config'));
		$db->setQuery($query);
		$config = $db->loadObject();
		return $config->dbconfig;
	}
function databaseName(){
		
	$driver_object = json_decode(getVdatConfig());
	
	if(isset($driver_object->local_db) && $driver_object->local_db==0){
				$option = array(); 

				$option['driver']   = $driver_object->driver;            
				$option['host']     = $driver_object->host;  
				$option['user']     = $driver_object->user;  
				$option['password'] = $driver_object->password; 
				$option['db'] = $driver_object->database; 
				$option['prefix']   = $driver_object->dbprefix;  

				return $option;
		   }
		   else
		   {
			   $db = JFactory::getConfig();
			   return $db;
		   }	
	}
	
function PMA_exportStructure($db, $table, $crlf, $error_url, $do_relation = false, $do_comments = false, $do_mime = false, $dates = false, $dummy)
{
    global $cfgRelation;

    if (! PMA_exportOutputHandler('<h2>' . JText::_('Table structure for table') . ' ' .$table . '</h2>')) {
        return FALSE;
    }

    /**
     * Get the unique keys in the table
     */
	 $dbs = CheckDatabase(); 
    $keys_query     = 'SHOW KEYS FROM ' . PMA_backquote($table) . ' FROM '. PMA_backquote($db);
    $keys_result    = $dbs->setQuery($keys_query)->loadObjectList();
    $unique_keys    = array();
    foreach ($keys_result  as $key =>$value) {
        if ($key['Non_unique'] == 0) {
            $unique_keys[] = $key['Column_name'];
        }
    }
    //PMA_DBI_free_result($keys_result);

   
	
    $local_query = 'SHOW FIELDS FROM ' . PMA_backquote($db) . '.' . PMA_backquote($table);
    $result      = $dbs->setQuery($local_query);
    $fields_cnt  = $dbs->loadColumn();

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

    
    if (! PMA_exportOutputHandler('<table class="width100" cellspacing="1">')) {
        return FALSE;
    }

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

    $schema_insert = '<tr class="print-category">';
    $schema_insert .= '<th class="print">' . htmlspecialchars(JText::_('Column')) . '</th>';
    $schema_insert .= '<td class="print"><b>' . htmlspecialchars(JText::_('Type')) . '</b></td>';
    $schema_insert .= '<td class="print"><b>' . htmlspecialchars(JText::_('Null')) . '</b></td>';
    $schema_insert .= '<td class="print"><b>' . htmlspecialchars(JText::_('Default')) . '</b></td>';
    if ($do_relation && $have_rel) {
        $schema_insert .= '<td class="print"><b>' . htmlspecialchars(JText::_('Links to')) . '</b></td>';
    }
    if ($do_comments) {
        $schema_insert .= '<td class="print"><b>' . htmlspecialchars(JText::_('Comments')) . '</b></td>';
        $comments = PMA_getComments($db, $table);
    }
    if ($do_mime && $cfgRelation['mimework']) {
        $schema_insert .= '<td class="print"><b>' . htmlspecialchars('MIME') . '</b></td>';
        $mime_map = PMA_getMIME($db, $table, true);
    }
    $schema_insert .= '</tr>';

    if (! PMA_exportOutputHandler($schema_insert)) {
        return FALSE;
    }

    foreach ($result as $row) {

        $schema_insert = '<tr class="print-category">';
        $type          = $row['Type'];
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
            $fmt_pre = '<b>' . $fmt_pre;
            $fmt_post = $fmt_post . '</b>';
        }
        if ($row['Key'] == 'PRI') {
            $fmt_pre = '<i>' . $fmt_pre;
            $fmt_post = $fmt_post . '</i>';
        }
        $schema_insert .= '<td class="print">' . $fmt_pre . htmlspecialchars($row['Field']) . $fmt_post . '</td>';
        $schema_insert .= '<td class="print">' . htmlspecialchars($type) . '</td>';
        $schema_insert .= '<td class="print">' . htmlspecialchars(($row['Null'] == '' || $row['Null'] == 'NO') ? JText::_('No') : JText::_('Yes')) . '</td>';
        $schema_insert .= '<td class="print">' . htmlspecialchars(isset($row['Default']) ? $row['Default'] : '') . '</td>';

        $field_name = $row['Field'];

        if ($do_relation && $have_rel) {
            $schema_insert .= '<td class="print">' . (isset($res_rel[$field_name]) ? htmlspecialchars($res_rel[$field_name]['foreign_table'] . ' (' . $res_rel[$field_name]['foreign_field'] . ')') : '') . '</td>';
        }
        if ($do_comments && $cfgRelation['commwork']) {
            $schema_insert .= '<td class="print">' . (isset($comments[$field_name]) ? htmlspecialchars($comments[$field_name]) : '') . '</td>';
        }
        if ($do_mime && $cfgRelation['mimework']) {
            $schema_insert .= '<td class="print">' . (isset($mime_map[$field_name]) ? htmlspecialchars(str_replace('_', '/', $mime_map[$field_name]['mimetype'])) : '') . '</td>';
        }

        $schema_insert .= '</tr>';

        if (! PMA_exportOutputHandler($schema_insert)) {
            return FALSE;
        }
    } // end while
	
    //PMA_DBI_free_result($result);

    return PMA_exportOutputHandler('</table>');
}

}
?>
