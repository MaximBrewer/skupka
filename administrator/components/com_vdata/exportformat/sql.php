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
	
    $hide_sql       = false;
    $hide_structure = false;
    if ($plugin_param['export_type'] == 'table' && !$plugin_param['single_table']) {
        $hide_structure = true;
        $hide_sql       = true;
    }
    if (1) {
        $plugin_list['sql'] = array(
            'text' => JText::_('SQL'),
            'extension' => 'sql',
            'mime_type' => 'text/x-sql',
            'options' => array());

        $plugin_list['sql']['options'][] = array('type' => 'begin_group', 'name' => 'general_opts');

        /* comments */
        $plugin_list['sql']['options'][] =
            array('type' => 'begin_subgroup', 'subgroup_header' => array('type' => 'bool', 'name' => 'include_comments', 'text' => JText::_('Display comments <i>(includes info such as export timestamp, PHP version, and server version)</i>')));
        $plugin_list['sql']['options'][] =
            array('type' => 'text', 'name' => 'header_comment', 'text' => JText::_('Additional custom header comment (\n splits lines):'));
        $plugin_list['sql']['options'][] =
            array('type' => 'bool', 'name' => 'dates', 'text' => JText::_('Include a timestamp of when databases were created, last updated, and last checked'));
        if (!empty($GLOBALS['cfgRelation']['relation'])) {
            $plugin_list['sql']['options'][] =
                array('type' => 'bool', 'name' => 'relation', 'text' => JText::_('Display foreign key relationships'));
        }
        if (!empty($GLOBALS['cfgRelation']['mimework'])) {
            $plugin_list['sql']['options'][] =
                array('type' => 'bool', 'name' => 'mime', 'text' => JText::_('Display MIME types'));
        }
        $plugin_list['sql']['options'][] = array('type' => 'end_subgroup');
        /* end comments */

        /* enclose in a transaction */
        $plugin_list['sql']['options'][] = array('type' => 'bool', 'name' => 'use_transaction', 'text' => JText::_('Enclose export in a transaction'), 'doc' => array('programs', 'mysqldump', 'option_mysqldump_single-transaction'));

        /* disable foreign key checks */
        $plugin_list['sql']['options'][] = array('type' => 'bool', 'name' => 'disable_fk', 'text' => JText::_('Disable foreign key checks'), 'doc' => array('manual_MySQL_Database_Administration', 'server-system-variables', 'sysvar_foreign_key_checks'));

        $plugin_list['sql']['options_text'] = JText::_('Options');

        /* compatibility maximization */
        $compats = array(); 
        if (count($compats) > 0) {
            $values = array();
            foreach($compats as $val) {
                $values[$val] = $val;
            }
            $plugin_list['sql']['options'][] =
                array('type' => 'select', 'name' => 'compatibility', 'text' => JText::_('Database system or older MySQL server to maximize output compatibility with:'), 'values' => $values, 'doc' => array('manual_MySQL_Database_Administration', 'Server_SQL_mode'));
            unset($values);
        }

        /* server export options */
        if ($plugin_param['export_type'] == 'server') {
        $plugin_list['sql']['options'][] =
                array('type' => 'bool', 'name' => 'drop_database', 'text' => sprintf(JText::_('Add %s statement'), '<code>DROP DATABASE</code>'));
         }

        /* what to dump (structure/data/both) */
        $plugin_list['sql']['options'][] =
            array('type' => 'begin_subgroup', 'subgroup_header' => array('type' => 'message_only', 'text' => JText::_('Dump table')));
        $plugin_list['sql']['options'][] =
            array('type' => 'radio', 'name' => 'structure_or_data', 'values' => array('structure' => JText::_('structure'), 'data' => JText::_('data'), 'structure_and_data' => JText::_('structure and data')));
        $plugin_list['sql']['options'][] = array('type' => 'end_subgroup');

        $plugin_list['sql']['options'][] = array('type' => 'end_group');

        /* begin Structure options */
         if (!$hide_structure) {
            $plugin_list['sql']['options'][] =
                array('type' => 'begin_group', 'name' => 'structure', 'text' => JText::_('Object creation options'), 'force' => 'data');

            /* begin SQL Statements */
            $plugin_list['sql']['options'][] =
                array('type' => 'begin_subgroup', 'subgroup_header' => array('type' => 'message_only', 'name' => 'add_statements', 'text' => JText::_('Add statements:')));
             if ($plugin_param['export_type'] == 'table') {
                if (PMA_Table::isView($GLOBALS['db'], $GLOBALS['table'])) {
                    $drop_clause = '<code>DROP VIEW</code>';
                } else {
                    $drop_clause = '<code>DROP TABLE</code>';
                }
            } else {
                $drop_clause = '<code>DROP TABLE / VIEW / PROCEDURE / FUNCTION</code>';
                if (PMA_MYSQL_INT_VERSION > 50100) {
                    $drop_clause .= '<code> / EVENT</code>';
                }
            }
            $plugin_list['sql']['options'][] =
                array('type' => 'bool', 'name' => 'drop_table', 'text' => sprintf(JText::_('Add %s statement'), $drop_clause));
            $plugin_list['sql']['options'][] =
                array('type' => 'bool', 'name' => 'procedure_function', 'text' => sprintf(JText::_('Add %s statement'), '<code>CREATE PROCEDURE / FUNCTION' . (PMA_MYSQL_INT_VERSION > 50100 ? ' / EVENT</code>' : '</code>')));

            /* begin CREATE TABLE statements*/
            $plugin_list['sql']['options'][] =
                array('type' => 'begin_subgroup', 'subgroup_header' => array('type' => 'bool', 'name' => 'create_table_statements', 'text' => JText::_('<code>CREATE TABLE</code> options:')));
            $plugin_list['sql']['options'][] =
                array('type' => 'bool', 'name' => 'if_not_exists', 'text' => '<code>IF NOT EXISTS</code>');
            $plugin_list['sql']['options'][] =
                array('type' => 'bool', 'name' => 'auto_increment', 'text' => '<code>AUTO_INCREMENT</code>');
            $plugin_list['sql']['options'][] = array('type' => 'end_subgroup');
            /* end CREATE TABLE statements */

            $plugin_list['sql']['options'][] = array('type' => 'end_subgroup');
            /* end SQL statements */

            $plugin_list['sql']['options'][] =
                array('type' => 'bool', 'name' => 'backquotes', 'text' => JText::_('Enclose table and field names with backquotes <i>(Protects field and table names formed with special characters or keywords)</i>'));

            $plugin_list['sql']['options'][] =
                array('type' => 'end_group');
        }
        /* end Structure options */

        /* begin Data options */
         $plugin_list['sql']['options'][] =
            array('type' => 'begin_group', 'name' => 'data', 'text' => JText::_('Data dump options'), 'force' => 'structure');

        /* begin SQL statements */
        $plugin_list['sql']['options'][] =
            array('type' => 'begin_subgroup', 'subgroup_header' => array('type' => 'message_only', 'text' => JText::_('Instead of <code>INSERT</code> statements, use:')));
        $plugin_list['sql']['options'][] =
            array('type' => 'bool', 'name' => 'delayed', 'text' => JText::_('<code>INSERT DELAYED</code> statements'), 'doc' => array('manual_MySQL_Database_Administration', 'insert_delayed'));
        $plugin_list['sql']['options'][] =
            array('type' => 'bool', 'name' => 'ignore', 'text' => JText::_('<code>INSERT IGNORE</code> statements'), 'doc' => array('manual_MySQL_Database_Administration', 'insert'));
        $plugin_list['sql']['options'][] =
            array('type' => 'end_subgroup');
        /* end SQL statements */

        /* Function to use when dumping data */
        $plugin_list['sql']['options'][] =
            array('type' => 'select', 'name' => 'type', 'text' => JText::_('Function to use when dumping data:'), 'values' => array('INSERT' => 'INSERT', 'UPDATE' => 'UPDATE', 'REPLACE' => 'REPLACE'));

        /* Syntax to use when inserting data */
        $plugin_list['sql']['options'][] =
            array('type' => 'begin_subgroup', 'subgroup_header' => array('type' => 'message_only', 'text' => JText::_('Syntax to use when inserting data:')));
        $plugin_list['sql']['options'][] =
            array('type' => 'radio', 'name' => 'insert_syntax', 'values' => array(
                'complete' => JText::_('include column names in every <code>INSERT</code> statement <br /> &nbsp; &nbsp; &nbsp; Example: <code>INSERT INTO tbl_name (col_A,col_B,col_C) VALUES (1,2,3)</code>'),
                'extended' => JText::_('insert multiple rows in every <code>INSERT</code> statement<br /> &nbsp; &nbsp; &nbsp; Example: <code>INSERT INTO tbl_name VALUES (1,2,3), (4,5,6), (7,8,9)</code>'),
                'both' => JText::_('both of the above<br /> &nbsp; &nbsp; &nbsp; Example: <code>INSERT INTO tbl_name (col_A,col_B) VALUES (1,2,3), (4,5,6), (7,8,9)</code>'),
                'none' => JText::_('neither of the above<br /> &nbsp; &nbsp; &nbsp; Example: <code>INSERT INTO tbl_name VALUES (1,2,3)</code>')));
          $plugin_list['sql']['options'][] =
            array('type' => 'end_subgroup');

        /* Max length of query */
        $plugin_list['sql']['options'][] =
            array('type' => 'text', 'name' => 'max_query_size', 'text' => JText::_('Maximal length of created query'));

        /* Dump binary columns in hexadecimal */
        $plugin_list['sql']['options'][] =
            array('type' => 'bool', 'name' => 'hex_for_blob', 'text' => JText::_('Dump binary columns in hexadecimal notation <i>(for example, "abc" becomes 0x616263)</i>'));

        /* Dump time in UTC */
        $plugin_list['sql']['options'][] =
            array('type' => 'bool', 'name' => 'utc_time', 'text' => JText::_('Dump TIMESTAMP columns in UTC <i>(enables TIMESTAMP columns to be dumped and reloaded between servers in different time zones)</i>'));

        $plugin_list['sql']['options'][] = array('type' => 'end_group');
         /* end Data options */
    }
	
}
 else {

/**
 * Avoids undefined variables, use NULL so isset() returns false
 */
if (! isset($sql_backquotes)) {
    $sql_backquotes = null;
	
}

/**
 * Exports routines (procedures and functions) 
 *
 * @param   string      $db 
 *
 * @return  bool        Whether it suceeded
 */
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
function DatabaseName(){
		
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
  function PMA_exportComment($text = '')
{
     $data =  JFactory::getApplication()->input->post->getArray(); 
	  $data['crlf'] = "\n";
	if (isset($data['sql_include_comments']) && $data['sql_include_comments']) {
        // see http://dev.mysql.com/doc/refman/5.0/en/ansi-diff-comments.html
        return '--' . (empty($text) ? '' : ' ') . $text. $data['crlf'];
    } else {
        return '';
    }
}
 function PMA_exportRoutines($db) {
    global $crlf;

    $text = '';
    $delimiter = '$$';
    $data =  JFactory::getApplication()->input->post->getArray(); 
    $procedure_names = PMA_DBI_get_procedures_or_functions($db, 'PROCEDURE');
    $function_names = PMA_DBI_get_procedures_or_functions($db, 'FUNCTION');

    if ($procedure_names || $function_names) {
        $text .= "\n"
          . 'DELIMITER ' . $delimiter . $crlf;
    }

    if ($procedure_names) {
        $text .=
            PMA_exportComment()
          . PMA_exportComment(JText::_('Procedures'))
          . PMA_exportComment();

        /* foreach($procedure_names as $procedure_name) {
            if (isset($data['sql_drop_table']) && ! empty($data['sql_drop_table'])) {
                $text .= 'DROP PROCEDURE IF EXISTS ' . PMA_backquote($procedure_name) . $delimiter . $crlf;
            }
            $text .= PMA_DBI_get_definition($db, 'PROCEDURE', $procedure_name) . $delimiter . $crlf . $crlf;
        } */
    }

    if ($function_names) {
        $text .=
            PMA_exportComment()
          . PMA_exportComment(JText::_('Functions'))
          . PMA_exportComment();

        foreach($function_names as $function_name) {
            if (! empty($data['sql_drop_table'])) {
                $text .= 'DROP FUNCTION IF EXISTS ' . PMA_backquote($function_name) . $delimiter . $crlf;
            }
            //$text .= PMA_DBI_get_definition($db, 'FUNCTION', $function_name) . $delimiter . $crlf . $crlf;
        }
    }

    if ($procedure_names || $function_names) {
        $text .= 'DELIMITER ;' . $crlf;
    }
   
    if (! empty($text)) {
        return PMA_exportOutputHandler($text);
    } 
}

/**
 * Possibly outputs comment
 *
 * @param   string      Text of comment
 *
 * @return  string      The formatted comment
 */


/**
 * Possibly outputs CRLF
 *
 * @return  string  $crlf or nothing
 */
 function PMA_possibleCRLF()
{ 
 $data =  JFactory::getApplication()->input->post->getArray(); 
    if (isset($data['sql_include_comments']) && $data['sql_include_comments']) {
        return '';
    } else {
        return '';
    }
}

/**
 * Outputs export footer
 *
 * @return  bool        Whether it suceeded
 *
 * @access  public
 */
 function PMA_exportFooter()
{
    global $crlf;
    global $mysql_charset_map;
     $data = JFactory::getApplication()->input->post->getArray(); 
    $foot = '';
    $crlf ="\n";
    if (isset($data['sql_disable_fk'])) {
        $foot .=  'SET FOREIGN_KEY_CHECKS=1;' . $crlf;
    }

    if (isset($data['sql_use_transaction'])) {
        $foot .=  'COMMIT;' . $crlf;
    }

    // restore connection settings
    $charset_of_file = isset($data['charset_of_file']) ? $data['charset_of_file'] : '';
    if (!empty($data['asfile'])) {
        $foot .=  $crlf
               . '/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;' . $crlf
               . '/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;' . $crlf
               . '/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;' . $crlf;
    }

    /* Restore timezone */
    /* if (isset($data['sql_utc_time'])) {
        PMA_DBI_query('SET time_zone = "' . $data['old_tz'] . '"');
    } */

    return PMA_exportOutputHandler($foot);
}

/**
 * Outputs export header
 *
 * @return  bool        Whether it suceeded
 *
 * @access  public
 */
 function PMA_exportHeader()
{
    global $crlf;
    global $cfg;
    global $mysql_charset_map;
	$crlf = "\n";
     $config = DatabaseName();
	 $data = JFactory::getApplication()->input->post->getArray(); 
	 
    if (isset($data['sql_compatibility'])) {
        $tmp_compat = $data['sql_compatibility'];
        if ($tmp_compat == 'NONE') {
            $tmp_compat = '';
        }
        /* PMA_DBI_try_query('SET SQL_MODE="' . $tmp_compat . '"');
        unset($tmp_compat); */
    }
    $head  =  PMA_exportComment('phpMyAdmin SQL Dump')
           .  PMA_exportComment('version '.phpversion())
           .  PMA_exportComment('http://www.phpmyadmin.net')
           .  PMA_exportComment();
   // $head .= empty($cfg['Server']['port']) ? PMA_exportComment(JText::_('Host') . ': ' . $config->get('memcache_server_host')) : PMA_exportComment(JText::_('Host') . ': ' .  $config->get('memcache_server_host') . ':' . $config->get('memcache_server_port'));
    $head .=  PMA_exportComment(JText::_('Generation Time')
           . ': ' .  PMA_localisedDate())
           .  PMA_exportComment(JText::_('Server version') . ': ' .$config['db'])
           .  PMA_exportComment(JText::_('PHP Version') . ': ' . phpversion())
           .  PMA_possibleCRLF();

    if (isset($data['sql_header_comment']) && !empty($data['sql_header_comment'])) {
        // '\n' is not a newline (like "\n" would be), it's the characters
        // backslash and n, as explained on the export interface
        $lines = explode('\n', $data['sql_header_comment']);
        //$head .= PMA_exportComment();
        foreach($lines as $one_line) {
            $head .= PMA_exportComment($one_line);
        }
        $head .= PMA_exportComment();
    }

    if (isset($data['sql_disable_fk'])) {
        $head .=  'SET FOREIGN_KEY_CHECKS=0;' . $crlf;
    }

   
   

    if (isset($data['sql_use_transaction']) && $data['sql_use_transaction']) {
        $head .=  'SET AUTOCOMMIT=0;' . $crlf
                . 'START TRANSACTION;' . $crlf;
    }


    /* Change timezone if we should export timestamps in UTC */
    if (isset($data['sql_utc_time'])&& $data['sql_utc_time']) {
        $head .= 'SET time_zone = "+00:00";' . $crlf;
        
    }

    $head .= PMA_possibleCRLF();

    if (1) {
        // we are saving as file, therefore we provide charset information
        // so that a utility like the mysql client can interpret
        // the file correctly
        
        $head .=  $crlf
               . '/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;' . $crlf
               . '/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;' . $crlf
               . '/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;' . $crlf
               . '/*!40101 SET NAMES  */;' . $crlf . $crlf;
    }
     
    return PMA_exportOutputHandler($head);
}

/**
 * Outputs CREATE DATABASE database
 *
 * @param   string      Database name
 *
 * @return  bool        Whether it suceeded
 *
 * @access  public
 */
 function PMA_exportDBCreate($db)
{ 
    global $crlf;
    if (isset($GLOBALS['sql_drop_database'])) {
        if (!PMA_exportOutputHandler('DROP DATABASE ' . (isset($GLOBALS['sql_backquotes']) ? PMA_backquote($db) : $db) . ';' . $crlf)) {
            return FALSE;
        }
    }
    $create_query = 'CREATE DATABASE ' . (isset($GLOBALS['sql_backquotes']) ? PMA_backquote($db) : $db);
    $collation = PMA_getDbCollation($db);
    if (strpos($collation, '_')) {
        $create_query .= ' DEFAULT CHARACTER SET ' . substr($collation, 0, strpos($collation, '_')) . ' COLLATE ' . $collation;
    } else {
        $create_query .= ' DEFAULT CHARACTER SET ' . $collation;
    }
    $create_query .= ';' . $crlf;
    if (!PMA_exportOutputHandler($create_query)) {
        return FALSE;
    }
    if (isset($GLOBALS['sql_backquotes']) && isset($GLOBALS['sql_compatibility']) && $GLOBALS['sql_compatibility'] == 'NONE') {
        $result = PMA_exportOutputHandler('USE ' . PMA_backquote($db) . ';' . $crlf);
    } else {
        $result = PMA_exportOutputHandler('USE ' . $db . ';' . $crlf);
    }

    return $result;
}

/**
 * Outputs database header
 *
 * @param   string      Database name
 *
 * @return  bool        Whether it suceeded
 *
 * @access  public
 */
 function PMA_exportDBHeader($db)
{
    $data = JFactory::getApplication()->input->post->getArray(); 
	$head = PMA_exportComment()
          . PMA_exportComment(JText::_('Database') . ': ' . (isset($data['sql_backquotes']) ? PMA_backquote($db) : '\'' . $db . '\''))
          . PMA_exportComment();
    return PMA_exportOutputHandler($head);
}
 
function PMA_aix_iconv_wrapper($in_charset, $out_charset, $str) {

    //global $gnu_iconv_to_aix_iconv_codepage_map;
$gnu_iconv_to_aix_iconv_codepage_map = array ('iso-8859-1' => 'ISO8859-1',
    'iso-8859-2' => 'ISO8859-2',
    'iso-8859-3' => 'ISO8859-3',
    'iso-8859-4' => 'ISO8859-4',
    'iso-8859-5' => 'ISO8859-5',
    'iso-8859-6' => 'ISO8859-6',
    'iso-8859-7' => 'ISO8859-7',
    'iso-8859-8' => 'ISO8859-8',
    'iso-8859-9' => 'ISO8859-9',
    'big5' => 'IBM-eucTW',
    'euc-jp' => 'IBM-eucJP',
    'koi8-r' => 'IBM-eucKR',
    'ks_c_5601-1987' => 'KSC5601.1987-0',
    'tis-620' => 'TIS-620',
    'utf-8' => 'UTF-8'
);
   
    $translit_search = strpos(strtolower($out_charset), '//translit');
    $using_translit = (!($translit_search === FALSE));

    // Extract "plain" output character set name (without any transliteration argument)
    $out_charset_plain = ($using_translit ? substr($out_charset, 0, $translit_search) : $out_charset);

    // Transform name of input character set (if found)
    if (array_key_exists(strtolower($in_charset), $gnu_iconv_to_aix_iconv_codepage_map)) {
        $in_charset = $gnu_iconv_to_aix_iconv_codepage_map[strtolower($in_charset)];
    }

    // Transform name of "plain" output character set (if found)
    if (array_key_exists(strtolower($out_charset_plain), $gnu_iconv_to_aix_iconv_codepage_map)) {
        $out_charset_plain = $gnu_iconv_to_aix_iconv_codepage_map[strtolower($out_charset_plain)];
    }

    // Add transliteration argument again (exactly as specified by user) if used
    // Build the output character set name that we will use
    $out_charset = ($using_translit ? $out_charset_plain . substr($out_charset, $translit_search) : $out_charset_plain);

    // NOTE: Transliteration not supported; we will use the "plain" output character set name
    $out_charset = $out_charset_plain;

    // Call iconv() with the possibly modified parameters
    $result = iconv($in_charset, $out_charset, $str);
    return $result;
}
function PMA_convert_string($src_charset, $dest_charset, $what) {
    if ($src_charset == $dest_charset) {
        return $what;
    }
    switch ($GLOBALS['PMA_recoding_engine']) {
        case PMA_CHARSET_RECODE:
            return recode_string($src_charset . '..'  . $dest_charset, $what);
        case PMA_CHARSET_ICONV:
            return iconv($src_charset, $dest_charset . $GLOBALS['cfg']['IconvExtraParams'], $what);
        case PMA_CHARSET_ICONV_AIX:
            return PMA_aix_iconv_wrapper($src_charset, $dest_charset . $GLOBALS['cfg']['IconvExtraParams'], $what);
        default:
            return $what;
    }
}
/**
 * Outputs database footer
 *
 * @param   string      Database name
 *
 * @return  bool        Whether it suceeded
 *
 * @access  public
 */
 function PMA_exportDBFooter($db)
{
    global $crlf;
    $data = JFactory::getApplication()->input->post->getArray(); 
    $result = TRUE;
    if (isset($data['sql_constraints'])) {
        $result = PMA_exportOutputHandler($data['sql_constraints']);
        unset($data['sql_constraints']);
    }

    if (($data['sql_structure_or_data'] == 'structure' || $data['sql_structure_or_data'] == 'structure_and_data') && isset($data['sql_procedure_function'])) {
        $text = '';
        $delimiter = '$$';

        if (PMA_MYSQL_INT_VERSION > 50100) {
            //$event_names = PMA_DBI_fetch_result('SELECT EVENT_NAME FROM information_schema.EVENTS WHERE EVENT_SCHEMA= \'' . PMA_sqlAddslashes($db,true) . '\';');
			$event_names = array();
        } else {
            $event_names = array();
        }

        if ($event_names) {
            $text .= $crlf
              . 'DELIMITER ' . $delimiter . $crlf;

            $text .=
                PMA_exportComment()
              . PMA_exportComment(JText::_('Events'))
              . PMA_exportComment();

            foreach($event_names as $event_name) {
                if (! empty($GLOBALS['sql_drop_table'])) {
            $text .= 'DROP EVENT ' . PMA_backquote($event_name) . $delimiter . $crlf;
                }
                $text .= PMA_DBI_get_definition($db, 'EVENT', $event_name) . $delimiter . $crlf . $crlf;
            }

            $text .= 'DELIMITER ;' . $crlf;
        }

        if (! empty($text)) {
            $result = PMA_exportOutputHandler($text);
        }
    }
    return $result;
}


/**
 * Returns a stand-in CREATE definition to resolve view dependencies
 *
 * @param   string   the database name
 * @param   string   the view name
 * @param   string   the end of line sequence
 *
 * @return  string   resulting definition
 *
 * @access  public
 */
 function PMA_getTableDefStandIn($db, $view, $crlf) {
	$dbs = CheckDatabase();
	$data = JFactory::getApplication()->input->post->getArray(); 
    $create_query = '';
    if (! empty($data['sql_drop_table'])) {
        $create_query .= 'DROP VIEW IF EXISTS ' . PMA_backquote($view) . ';' . $crlf;
    }

    $create_query .= 'CREATE TABLE ';

    
        $create_query .= 'IF NOT EXISTS ';
   
    $create_query .= PMA_backquote($view) . ' (' . $crlf;
    $tmp = array();
    $columns = PMA_DBI_get_columns_full($db, $view);
    foreach($columns as $column_name => $definition) {
        $tmp[] = PMA_backquote($column_name) . ' ' . $definition['Type'] . $crlf;
    }
    $create_query .= implode(',', $tmp) . ');';
    return($create_query);
}

/**
 * Returns $table's CREATE definition
 *
 * @param   string   the database name
 * @param   string   the table name
 * @param   string   the end of line sequence
 * @param   string   the url to go back in case of error
 * @param   boolean  whether to include creation/update/check dates
 * @param   boolean  whether to add semicolon and end-of-line at the end
 * @param   boolean  whether we're handling view
 *
 * @return  string   resulting schema
 *
 * @global  boolean  whether to add 'drop' statements or not
 * @global  boolean  whether to use backquotes to allow the use of special
 *                   characters in database, table and fields names or not
 *
 * @access  public
 */
 function PMA_getTableDef($db, $table, $crlf, $error_url, $show_dates = false, $add_semicolon = true, $view = false)
{ 
    global $sql_drop_table;
    global $sql_backquotes;
    global $cfgRelation;
    global $sql_constraints;
    global $sql_constraints_query; // just the text of the query
    global $sql_drop_foreign_keys;
    $dbs = CheckDatabase();
	$data = JFactory::getApplication()->input->post->getArray(); 
	$sql_drop_table=isset($data['sql_drop_table'])?$data['sql_drop_table']:'';
    $sql_backquotes=isset($data['sql_backquotes'])?$data['sql_backquotes']:'';
    $cfgRelation=isset($data['cfgRelation'])?$data['cfgRelation']:'';
    $sql_constraints=isset($data['sql_constraints'])?$data['sql_constraints']:'';
    $sql_constraints_query=isset($data['sql_constraints_query'])?$data['sql_constraints_query']:''; // just the text of the query
    $sql_drop_foreign_keys=isset($data['sql_drop_foreign_keys'])?$data['sql_drop_foreign_keys']:'';
    $schema_create = '';
    $auto_increment = '';
    $new_crlf = $crlf;
   
    // need to use PMA_DBI_QUERY_STORE with PMA_DBI_num_rows() in mysqli
	$dbs= CheckDatabase(); 
	$query = 'SHOW TABLE STATUS FROM ' . PMA_backquote($db) . ' LIKE \'' . PMA_sqlAddslashes($table) . '\'';
	$dbs->setQuery($query);
	$result = $dbs->loadObjectList();
    //$result = PMA_DBI_query('SHOW TABLE STATUS FROM ' . PMA_backquote($db) . ' LIKE \'' . PMA_sqlAddslashes($table) . '\'', null, PMA_DBI_QUERY_STORE);
  
        if (count($result) > 0) {
            $tmpres        = $result;
            // Here we optionally add the AUTO_INCREMENT next value,
            // but starting with MySQL 5.0.24, the clause is already included
            // in SHOW CREATE TABLE so we'll remove it below
            if (isset($data['sql_auto_increment']) && !empty($tmpres['Auto_increment'])) {
                $auto_increment .= ' AUTO_INCREMENT=' . $tmpres['Auto_increment'] . ' ';
            }

            if ($show_dates && isset($tmpres['Create_time']) && !empty($tmpres['Create_time'])) {
                $schema_create .= PMA_exportComment(JText::_('Creation') . ': ' . PMA_localisedDate(strtotime($tmpres['Create_time'])));
                $new_crlf = PMA_exportComment() . $crlf;
            }

            if ($show_dates && isset($tmpres['Update_time']) && !empty($tmpres['Update_time'])) {
                $schema_create .= PMA_exportComment(JText::_('Last update') . ': ' . PMA_localisedDate(strtotime($tmpres['Update_time'])));
                $new_crlf = PMA_exportComment() . $crlf;
            }

            if ($show_dates && isset($tmpres['Check_time']) && !empty($tmpres['Check_time'])) {
                $schema_create .= PMA_exportComment(JText::_('Last check') . ': ' . PMA_localisedDate(strtotime($tmpres['Check_time'])));
                $new_crlf = PMA_exportComment() . $crlf;
            }
        }
       // PMA_DBI_free_result($result);
    //}
 
    $schema_create .= $new_crlf; 

    // no need to generate a DROP VIEW here, it was done earlier
    if (! empty($sql_drop_table)) {
        $schema_create .= 'DROP TABLE IF EXISTS ' . PMA_backquote($table, $sql_backquotes) . ';' . $crlf;
    }

    // Complete table dump,
    // Whether to quote table and fields names or not
   /*  if ($sql_backquotes) {
        PMA_DBI_query('SET SQL_QUOTE_SHOW_CREATE = 1');
    } else {
        PMA_DBI_query('SET SQL_QUOTE_SHOW_CREATE = 0');
    } */

    // I don't see the reason why this unbuffered query could cause problems,
    // because SHOW CREATE TABLE returns only one row, and we free the
    // results below. Nonetheless, we got 2 user reports about this
    // (see bug 1562533) so I remove the unbuffered mode.
    //$result = PMA_DBI_query('SHOW CREATE TABLE ' . PMA_backquote($db) . '.' . PMA_backquote($table), null, PMA_DBI_QUERY_UNBUFFERED);
    //
    // Note: SHOW CREATE TABLE, at least in MySQL 5.1.23, does not
    // produce a displayable result for the default value of a BIT
    // field, nor does the mysqldump command. See MySQL bug 35796
	
	
	$query = 'SHOW CREATE TABLE ' . PMA_backquote($db) . '.' . PMA_backquote($table);
	$dbs->setQuery($query);
	$result = $dbs->loadObjectList();
	$result = get_object_vars($result[0]);
    //$result = PMA_DBI_try_query('SHOW CREATE TABLE ' . PMA_backquote($db) . '.' . PMA_backquote($table));
    // an error can happen, for example the table is crashed
    //$tmp_error = PMA_DBI_getError();
    

    if ($result != FALSE) {
		
        $create_query = $result['Create Table'];
        unset($row);

        // Convert end of line chars to one that we want (note that MySQL doesn't return query it will accept in all cases)
        if (strpos($create_query, "(\r\n ")) {
            $create_query = str_replace("\r\n", $crlf, $create_query);
        } elseif (strpos($create_query, "(\n ")) {
            $create_query = str_replace("\n", $crlf, $create_query);
        } elseif (strpos($create_query, "(\r ")) {
            $create_query = str_replace("\r", $crlf, $create_query);
        }

        /*
         * Drop database name from VIEW creation.
         *
         * This is a bit tricky, but we need to issue SHOW CREATE TABLE with
         * database name, but we don't want name to show up in CREATE VIEW
         * statement.
         */
        if ($view) {
            $create_query = preg_replace('/' . PMA_backquote($db) . '\./', '', $create_query);
        }

        // Should we use IF NOT EXISTS?
        if (isset($data['sql_if_not_exists'])) {
            $create_query     = preg_replace('/^CREATE TABLE/', 'CREATE TABLE IF NOT EXISTS', $create_query);
        }

        // are there any constraints to cut out?
        if (preg_match('@CONSTRAINT|FOREIGN[\s]+KEY@', $create_query)) {

            // Split the query into lines, so we can easily handle it. We know lines are separated by $crlf (done few lines above).
            $sql_lines = explode($crlf, $create_query);
            $sql_count = count($sql_lines);

            // lets find first line with constraints
            for ($i = 0; $i < $sql_count; $i++) {
                if (preg_match('@^[\s]*(CONSTRAINT|FOREIGN[\s]+KEY)@', $sql_lines[$i])) {
                    break;
                }
            }

            // If we really found a constraint
            if ($i != $sql_count) {

                // remove , from the end of create statement
                $sql_lines[$i - 1] = preg_replace('@,$@', '', $sql_lines[$i - 1]);

                // prepare variable for constraints
                if (!isset($sql_constraints)) {
                    if (isset($data['no_constraints_comments'])) {
                        $sql_constraints = '';
                    } else {
                        $sql_constraints = $crlf
                                         . PMA_exportComment()
                                         . PMA_exportComment(JText::_('Constraints for dumped tables'))
                                         . PMA_exportComment();
                    }
                }

                // comments for current table
                if (!isset($data['no_constraints_comments'])) {
                    $sql_constraints .= $crlf
                                     . PMA_exportComment()
                                     . PMA_exportComment(JText::_('Constraints for table') . ' ' . PMA_backquote($table))
                                     . PMA_exportComment();
                }

                // let's do the work
                $sql_constraints_query .= 'ALTER TABLE ' . PMA_backquote($table) . $crlf;
                $sql_constraints .= 'ALTER TABLE ' . PMA_backquote($table) . $crlf;
                $sql_drop_foreign_keys .= 'ALTER TABLE ' . PMA_backquote($db) . '.' . PMA_backquote($table) . $crlf;

                $first = TRUE;
                for ($j = $i; $j < $sql_count; $j++) {
                    if (preg_match('@CONSTRAINT|FOREIGN[\s]+KEY@', $sql_lines[$j])) {
                        if (!$first) {
                            $sql_constraints .= $crlf;
                        }
                        if (strpos($sql_lines[$j], 'CONSTRAINT') === FALSE) {
                            $tmp_str = preg_replace('/(FOREIGN[\s]+KEY)/', 'ADD \1', $sql_lines[$j]);
                            $sql_constraints_query .= $tmp_str;
                            $sql_constraints .= $tmp_str;
                        } else {
                            $tmp_str = preg_replace('/(CONSTRAINT)/', 'ADD \1', $sql_lines[$j]);
                            $sql_constraints_query .= $tmp_str;
                            $sql_constraints .= $tmp_str;
                            preg_match('/(CONSTRAINT)([\s])([\S]*)([\s])/', $sql_lines[$j], $matches);
                            if (! $first) {
                                $sql_drop_foreign_keys .= ', ';
                            }
                            $sql_drop_foreign_keys .= 'DROP FOREIGN KEY ' . $matches[3];
                        }
                        $first = FALSE;
                    } else {
                        break;
                    }
                }
                $sql_constraints .= ';' . $crlf;
                $sql_constraints_query .= ';';

                $create_query = implode($crlf, array_slice($sql_lines, 0, $i)) . $crlf . implode($crlf, array_slice($sql_lines, $j, $sql_count - 1));
                unset($sql_lines);
            }
        }
        $schema_create .= $create_query;
    }

    // remove a possible "AUTO_INCREMENT = value" clause
    // that could be there starting with MySQL 5.0.24
    $schema_create = preg_replace('/AUTO_INCREMENT\s*=\s*([0-9])+/', '', $schema_create);

    $schema_create .= $auto_increment;

   // PMA_DBI_free_result($result);
    return $schema_create . ($add_semicolon ? ';' . $crlf : '');
} // end of the 'PMA_getTableDef()' function


/**
 * Returns $table's comments, relations etc.
 *
 * @param   string   the database name
 * @param   string   the table name
 * @param   string   the end of line sequence
 * @param   boolean  whether to include relation comments
 * @param   boolean  whether to include mime comments
 *
 * @return  string   resulting comments
 *
 * @access  public
 */
 function PMA_getTableComments($db, $table, $crlf, $do_relation = false,  $do_mime = false)
{
    global $cfgRelation;
    global $sql_backquotes;
    global $sql_constraints;

    $schema_create = '';

    // Check if we can use Relations (Mike Beck)
    if ($do_relation && !empty($cfgRelation['relation'])) {
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

    if ($do_mime && $cfgRelation['mimework']) {
        if (!($mime_map = PMA_getMIME($db, $table, true))) {
            unset($mime_map);
        }
    }

    if (isset($mime_map) && count($mime_map) > 0) {
        $schema_create .= PMA_possibleCRLF()
                       . PMA_exportComment()
                       . PMA_exportComment(JText::_('MIME TYPES FOR TABLE'). ' ' . PMA_backquote($table, $sql_backquotes) . ':');
        @reset($mime_map);
        foreach ($mime_map AS $mime_field => $mime) {
            $schema_create .= PMA_exportComment('  ' . PMA_backquote($mime_field, $sql_backquotes))
                            . PMA_exportComment('      ' . PMA_backquote($mime['mimetype'], $sql_backquotes));
        }
        $schema_create .= PMA_exportComment();
    }

    if ($have_rel) {
        $schema_create .= PMA_possibleCRLF()
                       . PMA_exportComment()
                       . PMA_exportComment(JText::_('RELATIONS FOR TABLE'). ' ' . PMA_backquote($table, $sql_backquotes) . ':');
        foreach ($res_rel AS $rel_field => $rel) {
            $schema_create .= PMA_exportComment('  ' . PMA_backquote($rel_field, $sql_backquotes))
                            . PMA_exportComment('      ' . PMA_backquote($rel['foreign_table'], $sql_backquotes)
                            . ' -> ' . PMA_backquote($rel['foreign_field'], $sql_backquotes));
        }
        $schema_create .= PMA_exportComment();
    }

    return $schema_create;

} // end of the 'PMA_getTableComments()' function

/**
 * Outputs table's structure
 *
 * @param   string   the database name
 * @param   string   the table name
 * @param   string   the end of line sequence
 * @param   string   the url to go back in case of error
 * @param   boolean  whether to include relation comments
 * @param   boolean  whether to include the pmadb-style column comments
 *                   as comments in the structure; this is deprecated
 *                   but the parameter is left here because export.php
 *                   calls PMA_exportStructure() also for other export
 *                   types which use this parameter
 * @param   boolean  whether to include mime comments
 * @param   string   'stand_in', 'create_table', 'create_view'
 * @param   string   'server', 'database', 'table'
 *
 * @return  bool     Whether it suceeded
 *
 * @access  public
 */
 function PMA_exportStructure($db, $table, $crlf, $error_url, $relation = FALSE, $comments = FALSE, $mime = FALSE, $dates = FALSE, $export_mode, $export_type)
{
     $data =  JFactory::getApplication()->input->post->getArray(); 
	
	$formatted_table_name = (isset($data['sql_backquotes']))
                          ? PMA_backquote($table)
                          : '\'' . $table . '\'';
					  
    $dump = PMA_possibleCRLF()
          . PMA_exportComment(str_repeat('-', 56))
          . PMA_possibleCRLF()
          . PMA_exportComment();

    switch($export_mode) {
        case 'create_table':
          
		   $dump .=  PMA_exportComment(JText::_('Table structure for table') . ' ' . $formatted_table_name)
                  . PMA_exportComment();
            $dump .= PMA_getTableDef($db, $table, $crlf, $error_url, $dates); 
            $dump .= PMA_getTableComments($db, $table, $crlf, $relation, $mime);
            break;
        case 'triggers':
            $dump = '';
            $triggers = PMA_DBI_get_triggers($db, $table);
            if ($triggers) {
                $dump .=  PMA_possibleCRLF()
                      . PMA_exportComment()
                      . PMA_exportComment(JText::_('Triggers') . ' ' . $formatted_table_name)
                      . PMA_exportComment();
                $delimiter = '//';
                foreach ($triggers as $trigger) {
                    $dump .= $trigger['drop'] . ';' . $crlf;
                    $dump .= 'DELIMITER ' . $delimiter . $crlf;
                    $dump .= $trigger['create'];
                    $dump .= 'DELIMITER ;' . $crlf;
                }
            }
            break;
        case 'create_view':
            $dump .= PMA_exportComment(JText::_('Structure for view') . ' ' . $formatted_table_name)
                  .  PMA_exportComment();
            // delete the stand-in table previously created (if any)
            if ($export_type != 'table') {
                $dump .= 'DROP TABLE IF EXISTS ' . PMA_backquote($table) . ';' . $crlf;
            }
            $dump .= PMA_getTableDef($db, $table, $crlf, $error_url, $dates, true, true);
            break;
        case 'stand_in':
            $dump .=  PMA_exportComment(JText::_('Stand-in structure for view') . ' ' . $formatted_table_name)
                  .  PMA_exportComment();
            // export a stand-in definition to resolve view dependencies
            $dump .= PMA_getTableDefStandIn($db, $table, $crlf);
    } // end switch

    // this one is built by PMA_getTableDef() to use in table copy/move
    // but not in the case of export
   // unset($GLOBALS['sql_constraints_query']);
  	
    return PMA_exportOutputHandler($dump);
}

/**
 * Dispatches between the versions of 'getTableContent' to use depending
 * on the php version
 *
 * @param   string      the database name
 * @param   string      the table name
 * @param   string      the end of line sequence
 * @param   string      the url to go back in case of error
 * @param   string      SQL query for obtaining data
 *
 * @return  bool        Whether it suceeded
 *
 * @global  boolean  whether to use backquotes to allow the use of special
 *                   characters in database, table and fields names or not
 * @global  integer  the number of records
 * @global  integer  the current record position
 *
 * @access  public
 *
 * @see     PMA_getTableContentFast(), PMA_getTableContentOld()
 *
 */
 function PMA_exportData($db, $table, $crlf, $error_url, $sql_query)
{
    global $sql_backquotes;
    global $rows_cnt;
    global $current_row;
	$sql_backquotes = 'something';
    $dbs = CheckDatabase();
	$data = JFactory::getApplication()->input->post->getArray(); 
    $formatted_table_name = (isset($data['sql_backquotes']))
                          ? PMA_backquote($table)
                          : '\'' . $table . '\'';

    // Do not export data for a VIEW
    // (For a VIEW, this is called only when exporting a single VIEW)
 
   
   
    // it's not a VIEW
    $buffer = '';

    // analyze the query to get the true column names, not the aliases
    // (this fixes an undefined index, also if Complete inserts
    //  are used, we did not get the true column name in case of aliases)
	$re = PMA_SQP_parse($sql_query);
    $analyzed_sql = PMA_SQP_analyze($re);

    $result      = $dbs->setQuery($sql_query)->loadObjectList();
    // a possible error: the table has crashed
   
  
    if ($result != FALSE) {
        $fields_cnt     = $dbs->setQuery('SHOW COLUMNS FROM '.PMA_backquote($db).'.'.PMA_backquote($table))->loadObjectList();
       
        // Get field information
		
        $fields_meta    = $fields_cnt;
        $field_flags    = array();
        $field_set    = array();

        for ($j = 0; $j < count($fields_cnt); $j++) {
            if (isset($analyzed_sql[0]['select_expr'][$j]['column'])) {
                $field_set[$j] = PMA_backquote($analyzed_sql[0]['select_expr'][$j]['column'], '``');
            } else { 
                $field_set[$j] = PMA_backquote($fields_meta[$j]->Field, '``');
            }
			
        }

        if (isset($data['sql_type']) && $data['sql_type'] == 'UPDATE') {
            // update
            $schema_insert  = 'UPDATE ';
            if (isset($data['sql_ignore'])) {
                $schema_insert .= 'IGNORE ';
            }
            // avoid EOL blank
            $schema_insert .= PMA_backquote($table, $sql_backquotes) . ' SET';
        } else {
            // insert or replace
            if (isset($data['sql_type']) && $data['sql_type'] == 'REPLACE') {
                $sql_command    = 'REPLACE';
            } else {
                $sql_command    = 'INSERT';
            }

            // delayed inserts?
            if (isset($data['sql_delayed'])) {
                $insert_delayed = ' DELAYED';
            } else {
                $insert_delayed = '';
            }
 
            // insert ignore?
            if (isset($data['sql_type']) && $data['sql_type'] == 'INSERT' && isset($data['sql_ignore'])) {
                $insert_delayed .= ' IGNORE';
            }

            // scheme for inserting fields
            if ($data['sql_insert_syntax'] == 'complete' || $data['sql_insert_syntax'] == 'both') {
                $fields        = implode(', ', $field_set);
                $schema_insert = $sql_command . $insert_delayed .' INTO ' . PMA_backquote($table, $sql_backquotes)
            // avoid EOL blank
                               . ' (' . $fields . ') VALUES';
            } else {
                $schema_insert = $sql_command . $insert_delayed .' INTO ' . PMA_backquote($table, $sql_backquotes)
                               . ' VALUES';
            }
        }
       
        $search       = array("\x00", "\x0a", "\x0d", "\x1a"); //\x08\\x09, not required
        $replace      = array('\0', '\n', '\r', '\Z');
        $current_row  = 0;
        $query_size   = 0;
		
        if (($data['sql_insert_syntax'] == 'extended' || $data['sql_insert_syntax'] == 'both') && (!isset($data['sql_type']) || $data['sql_type'] != 'UPDATE')) {
            $separator    = ',';
            $schema_insert .= $crlf;
        } else {
            $separator    = ';';
        }
		$checked_value = isset($data['checked_value'])&& $data['checked_value']!=''?explode(',', $data['checked_value']):array();
		$checked_id = isset($data['checked_id'])&& $data['checked_id']!=''?$data['checked_id']:'';
		$key_checke_id = array_search($checked_id, $fields_cnt);
		
       for($i=0;$i<count($result); $i++){
		   $row = $result[$i];
		  
       // while ($row = PMA_DBI_fetch_row($result)) 
            if ($current_row == 0) {
                $head = PMA_possibleCRLF()
                      . PMA_exportComment()
                      . PMA_exportComment(JText::_('Dumping data for table') . ' ' . $formatted_table_name)
                      . PMA_exportComment()
                      . $crlf;
                if (! PMA_exportOutputHandler($head)) {
                    return FALSE;
                }
            }
			$current_row++;
			if(count($checked_value)>0)
			{
			if(!in_array($row->$checked_id, $checked_value))	
				continue;;
			}
           
            for ($j = 0; $j < count($fields_cnt); $j++) {
               $f_name = $fields_cnt[$j]->Field;
			   
			   
                if (!isset($row->$f_name) || is_null($row->$f_name)) {
                    $values[]     = 'NULL';
                // a number
                // timestamp is numeric on some MySQL 4.1, BLOBs are sometimes numeric
				$fields_meta[$j]->Type = substr($fields_meta[$j]->Type,0,strpos($fields_meta[0]->Type, '('));
                } elseif ($fields_meta[$j]->Type != 'timestamp'
                        && $fields_meta[$j]->Type !='blob') {
                   $values[] = "'".str_replace("'", "''", $row->$f_name)."'"; 
               
                } elseif ($fields_meta[$j]->Type =='blob'
                        && isset($data['sql_hex_for_blob'])) {
                    // empty blobs need to be different, but '0' is also empty :-(
                    if (empty($row->$f_name) && $row->$f_name != '0') {
                        $values[] = '\'\'';
                    } else {
                        $values[] = '0x' . bin2hex($row->$f_name);
                    }
                // detection of 'bit' works only on mysqli extension
                } elseif ($fields_meta[$j]->Type == 'bit') {
                    $values[] = "b'" . PMA_sqlAddslashes(PMA_printable_bit_value($row->$f_name, $fields_meta[$j]->length)) . "'";
                // something else -> treat as a string
                } else {
                    $values[] = '\'' . str_replace($search, $replace, PMA_sqlAddslashes($row->$f_name)) . '\'';
                } // end if
            } // end for

            // should we make update?
            if (isset($data['sql_type']) && $data['sql_type'] == 'UPDATE') {

                $insert_line = $schema_insert;
                for ($i = 0; $i < $fields_cnt; $i++) {
                    if (0 == $i) {
                        $insert_line .= ' ';
                    }
                    if ($i > 0) {
                        // avoid EOL blank
                        $insert_line .= ',';
                    }
                    $insert_line .= $field_set[$i] . ' = ' . $values[$i];
                }

                list($tmp_unique_condition, $tmp_clause_is_unique) = PMA_getUniqueCondition($result, $fields_cnt, $fields_meta, $row->$fields_cnt[$j]->field);
                $insert_line .= ' WHERE ' . $tmp_unique_condition;
                unset($tmp_unique_condition, $tmp_clause_is_unique);

            } else {

                // Extended inserts case
                if ($data['sql_insert_syntax'] == 'extended' || $data['sql_insert_syntax'] == 'both') {
                    if ($current_row == 1) {
                        $insert_line  = $schema_insert . '(' . implode(', ', $values) . ')';
                    } else {
                        $insert_line  = '(' . implode(', ', $values) . ')';
                        if (isset($data['sql_max_query_size']) && $data['sql_max_query_size'] > 0) {
                            if (!PMA_exportOutputHandler(';' . $crlf)) {
                                return FALSE;
                            }
                            $query_size = 0;
                            $current_row = 1;
                            $insert_line = $schema_insert . $insert_line;
                        }
                    }
                    $query_size += strlen($insert_line);
                }
                // Other inserts case
                else {
                    $insert_line      = $schema_insert . '(' . implode(', ', $values) . ')';
                }
            }
            unset($values);

            if (!PMA_exportOutputHandler(($current_row == 1 ? '' : $separator . $crlf) . $insert_line)) {
                return FALSE;
            }

        } // end while
        /* if ($current_row > 0) {
            if (!$this->PMA_exportOutputHandler(';' . $crlf)) {
                return FALSE;
            }
        } */
    } 
	
	// end if ($result != FALSE)
   // PMA_DBI_free_result($result);

    return TRUE;
} // end of the 'PMA_exportData()' function

}

?>
