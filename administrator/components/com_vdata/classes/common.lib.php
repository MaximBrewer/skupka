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
function PMA_pow($base, $exp, $use_function = false)
{
    static $pow_function = null;

    if (null == $pow_function) {
        if (function_exists('bcpow')) {
            // BCMath Arbitrary Precision Mathematics Function
            $pow_function = 'bcpow';
        } elseif (function_exists('gmp_pow')) {
            // GMP Function
            $pow_function = 'gmp_pow';
        } else {
            // PHP function
            $pow_function = 'pow';
        }
    }

    if (! $use_function) {
        $use_function = $pow_function;
    }

    if ($exp < 0 && 'pow' != $use_function) {
        return false;
    }
    switch ($use_function) {
        case 'bcpow' :
            // bcscale() needed for testing PMA_pow() with base values < 1
            bcscale(10);
            $pow = bcpow($base, $exp);
            break;
        case 'gmp_pow' :
             $pow = gmp_strval(gmp_pow($base, $exp));
            break;
        case 'pow' :
            $base = (float) $base;
            $exp = (int) $exp;
            $pow = pow($base, $exp);
            break;
        default:
            $pow = $use_function($base, $exp);
    }

    return $pow;
}


function PMA_getIcon($icon, $alternate = '', $container = false, $force_text = false)
{
    $include_icon = false;
    $include_text = false;
    $include_box  = false;
    $alternate    = htmlspecialchars($alternate);
    $button       = '';

    if ($GLOBALS['cfg']['PropertiesIconic']) {
         $include_icon = true;
    }

    if ($force_text
     || ! (true === $GLOBALS['cfg']['PropertiesIconic'])
     || ! $include_icon) {
        // $cfg['PropertiesIconic'] is false or both
        // OR we have no $include_icon
        $include_text = true;
    }

    if ($include_text && $include_icon && $container) {
        // we have icon, text and request for container
        $include_box = true;
    }

    // Always use a span (we rely on this in js/sql.js)
    $button .= '<span class="nowrap">';

    if ($include_icon) {
        $button .= '<img src="' . $GLOBALS['pmaThemeImage'] . $icon . '"'
            . ' title="' . $alternate . '" alt="' . $alternate . '"'
            . ' class="icon" width="16" height="16" />';
    }

    if ($include_icon && $include_text) {
        $button .= ' ';
    }

    if ($include_text) {
        $button .= $alternate;
    }

    $button .= '</span>';

    return $button;
}


function PMA_displayMaximumUploadSize($max_upload_size)
{
    // I have to reduce the second parameter (sensitiveness) from 6 to 4
    // to avoid weird results like 512 kKib
    list($max_size, $max_unit) = PMA_formatByteDown($max_upload_size, 4);
    return '(' . sprintf(JText::_('Max: %s%s'), $max_size, $max_unit) . ')';
}


 function PMA_generateHiddenMaxFileSize($max_size)
 {
     return '<input type="hidden" name="MAX_FILE_SIZE" value="' .$max_size . '" />';
 }


function PMA_sqlAddslashes($a_string = '', $is_like = false, $crlf = false, $php_code = false)
{
    if ($is_like) {
        $a_string = str_replace('\\', '\\\\\\\\', $a_string);
    } else {
        $a_string = str_replace('\\', '\\\\', $a_string);
    }

    if ($crlf) {
        $a_string = str_replace("\n", '\n', $a_string);
        $a_string = str_replace("\r", '\r', $a_string);
        $a_string = str_replace("\t", '\t', $a_string);
    }

    if ($php_code) {
        $a_string = str_replace('\'', '\\\'', $a_string);
    } else {
        $a_string = str_replace('\'', '\'\'', $a_string);
    }

    return $a_string;
} // end of the 'PMA_sqlAddslashes()' function



function PMA_escape_mysql_wildcards($name)
{
    $name = str_replace('_', '\\_', $name);
    $name = str_replace('%', '\\%', $name);

    return $name;
} // end of the 'PMA_escape_mysql_wildcards()' function


function PMA_unescape_mysql_wildcards($name)
{
    $name = str_replace('\\_', '_', $name);
    $name = str_replace('\\%', '%', $name);

    return $name;
} // end of the 'PMA_unescape_mysql_wildcards()' function


function PMA_unQuote($quoted_string, $quote = null)
{
    $quotes = array();

    if (null === $quote) {
        $quotes[] = '`';
        $quotes[] = '"';
        $quotes[] = "'";
    } else {
        $quotes[] = $quote;
    }

    foreach ($quotes as $quote) {
        if (substr($quoted_string, 0, 1) === $quote
         && substr($quoted_string, -1, 1) === $quote) {
             $unquoted_string = substr($quoted_string, 1, -1);
             // replace escaped quotes
             $unquoted_string = str_replace($quote . $quote, $quote, $unquoted_string);
             return $unquoted_string;
         }
    }

    return $quoted_string;
}


function PMA_formatSql($parsed_sql, $unparsed_sql = '')
{
    global $cfg;

    // Check that we actually have a valid set of parsed data
    // well, not quite
    // first check for the SQL parser having hit an error
    if (PMA_SQP_isError()) {
        return htmlspecialchars($parsed_sql['raw']);
    }
    // then check for an array
    if (!is_array($parsed_sql)) {
        // We don't so just return the input directly
        // This is intended to be used for when the SQL Parser is turned off
        $formatted_sql = '<pre>' . "\n"
                        . (($cfg['SQP']['fmtType'] == 'none' && $unparsed_sql != '') ? $unparsed_sql : $parsed_sql) . "\n"
                        . '</pre>';
        return $formatted_sql;
    }

    $formatted_sql        = '';

    switch ($cfg['SQP']['fmtType']) {
        case 'none':
            if ($unparsed_sql != '') {
                $formatted_sql = '<span class="inner_sql"><pre>' . "\n" . PMA_SQP_formatNone(array('raw' => $unparsed_sql)) . "\n" . '</pre></span>';
            } else {
                $formatted_sql = PMA_SQP_formatNone($parsed_sql);
            }
            break;
        case 'html':
            $formatted_sql = PMA_SQP_formatHtml($parsed_sql, 'color');
            break;
        case 'text':
            $formatted_sql = PMA_SQP_formatHtml($parsed_sql, 'text');
            break;
        default:
            break;
    } // end switch

    return $formatted_sql;
} // end of the "PMA_formatSql()" function



function PMA_showMySQLDocu($chapter, $link, $big_icon = false, $anchor = '', $just_open = false)
{
    global $cfg;

    if ($cfg['MySQLManualType'] == 'none' || empty($cfg['MySQLManualBase'])) {
        return '';
    }

    // Fixup for newly used names:
    $chapter = str_replace('_', '-', strtolower($chapter));
    $link = str_replace('_', '-', strtolower($link));

    switch ($cfg['MySQLManualType']) {
        case 'chapters':
            if (empty($chapter)) {
                $chapter = 'index';
            }
            if (empty($anchor)) {
                $anchor = $link;
            }
            $url = $cfg['MySQLManualBase'] . '/' . $chapter . '.html#' . $anchor;
            break;
        case 'big':
            if (empty($anchor)) {
                $anchor = $link;
            }
            $url = $cfg['MySQLManualBase'] . '#' . $anchor;
            break;
        case 'searchable':
            if (empty($link)) {
                $link = 'index';
            }
            $url = $cfg['MySQLManualBase'] . '/' . $link . '.html';
            if (!empty($anchor)) {
                $url .= '#' . $anchor;
            }
            break;
        case 'viewable':
        default:
            if (empty($link)) {
                $link = 'index';
            }
            $mysql = '5.0';
            $lang = 'en';
            if (defined('PMA_MYSQL_INT_VERSION')) {
                if (PMA_MYSQL_INT_VERSION >= 50500) {
                    $mysql = '5.5';
                    /* l10n: Language to use for MySQL 5.5 documentation, please use only languages which do exist in official documentation.  */
                    $lang = _pgettext('MySQL 5.5 documentation language', 'en');
                } else if (PMA_MYSQL_INT_VERSION >= 50100) {
                    $mysql = '5.1';
                    /* l10n: Language to use for MySQL 5.1 documentation, please use only languages which do exist in official documentation.  */
                    $lang = _pgettext('MySQL 5.1 documentation language', 'en');
                } elseif (PMA_MYSQL_INT_VERSION >= 50000) {
                    $mysql = '5.0';
                    /* l10n: Language to use for MySQL 5.0 documentation, please use only languages which do exist in official documentation. */
                    $lang = _pgettext('MySQL 5.0 documentation language', 'en');
                }
            }
            $url = $cfg['MySQLManualBase'] . '/' . $mysql . '/' . $lang . '/' . $link . '.html';
            if (!empty($anchor)) {
                $url .= '#' . $anchor;
            }
            break;
    }

    if ($just_open) {
        return '<a href="' . PMA_linkURL($url) . '" target="mysql_doc">';
    } elseif ($big_icon) {
        return '<a href="' . PMA_linkURL($url) . '" target="mysql_doc"><img class="icon" src="' . $GLOBALS['pmaThemeImage'] . 'b_sqlhelp.png" width="16" height="16" alt="' . JText::_('Documentation') . '" title="' . JText::_('Documentation') . '" /></a>';
    } elseif ($GLOBALS['cfg']['ReplaceHelpImg']) {
        return '<a href="' . PMA_linkURL($url) . '" target="mysql_doc"><img class="icon" src="' . $GLOBALS['pmaThemeImage'] . 'b_help.png" width="11" height="11" alt="' . JText::_('Documentation') . '" title="' . JText::_('Documentation') . '" /></a>';
    } else {
        return '[<a href="' . PMA_linkURL($url) . '" target="mysql_doc">' . JText::_('Documentation') . '</a>]';
    }
} // end of the 'PMA_showMySQLDocu()' function



function PMA_showDocu($anchor) {
    if ($GLOBALS['cfg']['ReplaceHelpImg']) {
        return '<a href="Documentation.html#' . $anchor . '" target="documentation"><img class="icon" src="' . $GLOBALS['pmaThemeImage'] . 'b_help.png" width="11" height="11" alt="' . JText::_('Documentation') . '" title="' . JText::_('Documentation') . '" /></a>';
    } else {
        return '[<a href="Documentation.html#' . $anchor . '" target="documentation">' . JText::_('Documentation') . '</a>]';
    }
} // end of the 'PMA_showDocu()' function


function PMA_showPHPDocu($target) {
    $url = PMA_getPHPDocLink($target);

    if ($GLOBALS['cfg']['ReplaceHelpImg']) {
        return '<a href="' . $url . '" target="documentation"><img class="icon" src="' . $GLOBALS['pmaThemeImage'] . 'b_help.png" width="11" height="11" alt="' . JText::_('Documentation') . '" title="' . JText::_('Documentation') . '" /></a>';
    } else {
        return '[<a href="' . $url . '" target="documentation">' . JText::_('Documentation') . '</a>]';
    }
} // end of the 'PMA_showPHPDocu()' function


function PMA_showHint($message, $bbcode = false, $type = 'notice')
{
    if ($message instanceof PMA_Message) {
        $key = $message->getHash();
        $type = $message->getLevel();
    } else {
        $key = md5($message);
    }

    if (! isset($GLOBALS['footnotes'][$key])) {
        if (empty($GLOBALS['footnotes']) || ! is_array($GLOBALS['footnotes'])) {
            $GLOBALS['footnotes'] = array();
        }
        $nr = count($GLOBALS['footnotes']) + 1;
        // this is the first instance of this message
        $instance = 1;
        $GLOBALS['footnotes'][$key] = array(
            'note'      => $message,
            'type'      => $type,
            'nr'        => $nr,
            'instance'  => $instance
        );
    } else {
        $nr = $GLOBALS['footnotes'][$key]['nr'];
        // another instance of this message (to ensure ids are unique)
        $instance = ++$GLOBALS['footnotes'][$key]['instance'];
    }

    if ($bbcode) {
        return '[sup]' . $nr . '[/sup]';
    }

    // footnotemarker used in js/tooltip.js
    return '<sup class="footnotemarker">' . $nr . '</sup>' .
    '<img class="footnotemarker" id="footnote_' . $nr . '_' . $instance . '" src="' .
    $GLOBALS['pmaThemeImage'] . 'b_help.png" alt="" />';
}

function PMA_mysqlDie($error_message = '', $the_query = '',
                        $is_modify_link = true, $back_url = '', $exit = true)
{
    global $table, $db;

    
    $error_msg_output = '';

    if (!$error_message) {
        $error_message = PMA_DBI_getError();
    }
    if (!$the_query && !empty($GLOBALS['sql_query'])) {
        $the_query = $GLOBALS['sql_query'];
    }

    // --- Added to solve bug #641765
    if (!function_exists('PMA_SQP_isError') || PMA_SQP_isError()) {
        $formatted_sql = htmlspecialchars($the_query);
    } elseif (empty($the_query) || trim($the_query) == '') {
        $formatted_sql = '';
    } else {
        if (strlen($the_query) > $GLOBALS['cfg']['MaxCharactersInDisplayedSQL']) {
            $formatted_sql = htmlspecialchars(substr($the_query, 0, $GLOBALS['cfg']['MaxCharactersInDisplayedSQL'])) . '[...]';
        } else {
            $formatted_sql = PMA_formatSql(PMA_SQP_parse($the_query), $the_query);
        }
    }
    // ---
    $error_msg_output .= "\n" . '<!-- PMA-SQL-ERROR -->' . "\n";
    $error_msg_output .= '    <div class="error"><h1>' . JText::_('Error') . '</h1>' . "\n";
    // if the config password is wrong, or the MySQL server does not
    // respond, do not show the query that would reveal the
    // username/password
    if (!empty($the_query) && !strstr($the_query, 'connect')) {
        // --- Added to solve bug #641765
        if (function_exists('PMA_SQP_isError') && PMA_SQP_isError()) {
            $error_msg_output .= PMA_SQP_getErrorString() . "\n";
            $error_msg_output .= '<br />' . "\n";
        }
        // ---
        // modified to show the help on sql errors
        $error_msg_output .= '    <p><strong>' . JText::_('SQL query') . ':</strong>' . "\n";
        if (strstr(strtolower($formatted_sql), 'select')) { // please show me help to the error on select
            $error_msg_output .= PMA_showMySQLDocu('SQL-Syntax', 'SELECT');
        }
        if ($is_modify_link) {
            $_url_params = array(
                'sql_query' => $the_query,
                'show_query' => 1,
            );
            if (strlen($table)) {
                $_url_params['db'] = $db;
                $_url_params['table'] = $table;
                $doedit_goto = '<a href="tbl_sql.php' . PMA_generate_common_url($_url_params) . '">';
            } elseif (strlen($db)) {
                $_url_params['db'] = $db;
                $doedit_goto = '<a href="db_sql.php' . PMA_generate_common_url($_url_params) . '">';
            } else {
                $doedit_goto = '<a href="server_sql.php' . PMA_generate_common_url($_url_params) . '">';
            }

            $error_msg_output .= $doedit_goto
               . PMA_getIcon('b_edit.png', JText::_('Edit'))
               . '</a>';
        } // end if
        $error_msg_output .= '    </p>' . "\n"
            .'    <p>' . "\n"
            .'        ' . $formatted_sql . "\n"
            .'    </p>' . "\n";
    } // end if

    if (!empty($error_message)) {
        $error_message = preg_replace("@((\015\012)|(\015)|(\012)){3,}@", "\n\n", $error_message);
    }
    // modified to show the help on error-returns
    // (now error-messages-server)
    $error_msg_output .= '<p>' . "\n"
            . '    <strong>' . JText::_('MySQL said: ') . '</strong>'
            . PMA_showMySQLDocu('Error-messages-server', 'Error-messages-server')
            . "\n"
            . '</p>' . "\n";

    // The error message will be displayed within a CODE segment.
    // To preserve original formatting, but allow wordwrapping, we do a couple of replacements

    // Replace all non-single blanks with their HTML-counterpart
    $error_message = str_replace('  ', '&nbsp;&nbsp;', $error_message);
    // Replace TAB-characters with their HTML-counterpart
    $error_message = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $error_message);
    // Replace linebreaks
    $error_message = nl2br($error_message);

    $error_msg_output .= '<code>' . "\n"
        . $error_message . "\n"
        . '</code><br />' . "\n";
    $error_msg_output .= '</div>';

    

    if ($exit) {
     
       if($GLOBALS['is_ajax_request'] == true) {
           PMA_ajaxResponse($error_msg_output, false);
       }
        if (! empty($back_url)) {
            if (strstr($back_url, '?')) {
                $back_url .= '&amp;no_history=true';
            } else {
                $back_url .= '?no_history=true';
            }

          

            $error_msg_output .= '<fieldset class="tblFooters">';
            $error_msg_output .= '[ <a href="' . $back_url . '">' . JText::_('Back') . '</a> ]';
            $error_msg_output .= '</fieldset>' . "\n\n";
       }

       echo $error_msg_output;
      
       require './libraries/footer.inc.php';
    } else {
        echo $error_msg_output;
    }
} // end of the 'PMA_mysqlDie()' function


function PMA_getTableList($db, $tables = null, $limit_offset = 0, $limit_count = false)
{
    $sep = $GLOBALS['cfg']['LeftFrameTableSeparator'];

    if (null === $tables) {
        $tables = PMA_DBI_get_tables_full($db, false, false, null, $limit_offset, $limit_count);
        if ($GLOBALS['cfg']['NaturalOrder']) {
            uksort($tables, 'strnatcasecmp');
        }
    }

    if (count($tables) < 1) {
        return $tables;
    }

    $default = array(
        'Name'      => '',
        'Rows'      => 0,
        'Comment'   => '',
        'disp_name' => '',
    );

    $table_groups = array();

    // for blobstreaming - list of blobstreaming tables

    // load PMA configuration
    $PMA_Config = $GLOBALS['PMA_Config'];

    foreach ($tables as $table_name => $table) {
        // if BS tables exist
        if (PMA_BS_IsHiddenTable($table_name)) {
            continue;
        }

        // check for correct row count
        if (null === $table['Rows']) {
            // Do not check exact row count here,
            // if row count is invalid possibly the table is defect
            // and this would break left frame;
            // but we can check row count if this is a view or the
            // information_schema database
            // since PMA_Table::countRecords() returns a limited row count
            // in this case.

            // set this because PMA_Table::countRecords() can use it
            $tbl_is_view = PMA_Table::isView($db, $table['Name']);

            if ($tbl_is_view || 'information_schema' == $db) {
                $table['Rows'] = PMA_Table::countRecords($db, $table['Name']);
            }
        }

        // in $group we save the reference to the place in $table_groups
        // where to store the table info
        if ($GLOBALS['cfg']['LeftFrameDBTree']
            && $sep && strstr($table_name, $sep))
        {
            $parts = explode($sep, $table_name);

            $group =& $table_groups;
            $i = 0;
            $group_name_full = '';
            $parts_cnt = count($parts) - 1;
            while ($i < $parts_cnt
              && $i < $GLOBALS['cfg']['LeftFrameTableLevel']) {
                $group_name = $parts[$i] . $sep;
                $group_name_full .= $group_name;

                if (!isset($group[$group_name])) {
                    $group[$group_name] = array();
                    $group[$group_name]['is' . $sep . 'group'] = true;
                    $group[$group_name]['tab' . $sep . 'count'] = 1;
                    $group[$group_name]['tab' . $sep . 'group'] = $group_name_full;
                } elseif (!isset($group[$group_name]['is' . $sep . 'group'])) {
                    $table = $group[$group_name];
                    $group[$group_name] = array();
                    $group[$group_name][$group_name] = $table;
                    unset($table);
                    $group[$group_name]['is' . $sep . 'group'] = true;
                    $group[$group_name]['tab' . $sep . 'count'] = 1;
                    $group[$group_name]['tab' . $sep . 'group'] = $group_name_full;
                } else {
                    $group[$group_name]['tab' . $sep . 'count']++;
                }
                $group =& $group[$group_name];
                $i++;
            }
        } else {
            if (!isset($table_groups[$table_name])) {
                $table_groups[$table_name] = array();
            }
            $group =& $table_groups;
        }


        if ($GLOBALS['cfg']['ShowTooltipAliasTB']
          && $GLOBALS['cfg']['ShowTooltipAliasTB'] !== 'nested') {
            // switch tooltip and name
            $table['disp_name'] = $table['Comment'];
            $table['Comment'] = $table['Name'];
        } else {
            $table['disp_name'] = $table['Name'];
        }

        $group[$table_name] = array_merge($default, $table);
    }

    return $table_groups;
}

 function PMA_SQP_analyze($arr)
    {
        if ($arr == array() || !isset($arr['len'])) {
			
            return array();
        }
		
        $result          = array();
        $size            = $arr['len'];
        $subresult       = array(
            'querytype'      => '',
            'select_expr_clause'=> '', // the whole stuff between SELECT and FROM , except DISTINCT
            'position_of_first_select' => '', // the array index
            'from_clause'=> '',
            'group_by_clause'=> '',
            'order_by_clause'=> '',
            'having_clause'  => '',
            'limit_clause'  => '',
            'where_clause'   => '',
            'where_clause_identifiers'   => array(),
            'unsorted_query' => '',
            'queryflags'     => array(),
            'select_expr'    => array(),
            'table_ref'      => array(),
            'foreign_keys'   => array(),
            'create_table_fields' => array()
        );
        $subresult_empty = $subresult;
        $seek_queryend         = FALSE;
        $seen_end_of_table_ref = FALSE;
        $number_of_brackets_in_extract = 0;
        $number_of_brackets_in_group_concat = 0;

        $number_of_brackets = 0;
        $in_subquery = false;
        $seen_subquery = false;
        $seen_from = false;

        
        $in_extract          = FALSE;

        // for GROUP_CONCAT(...)
        $in_group_concat     = FALSE;


        $words_ending_table_ref = array(
            'FOR',
            'GROUP',
            'HAVING',
            'LIMIT',
            'LOCK',
            'ORDER',
            'PROCEDURE',
            'UNION',
            'WHERE'
        );
        $words_ending_table_ref_cnt = 9; 

        $words_ending_clauses = array(
            'FOR',
            'LIMIT',
            'LOCK',
            'PROCEDURE',
            'UNION'
        );
        $words_ending_clauses_cnt = 5;




        // must be sorted
        $supported_query_types = array(
            'SELECT'
            
        );
        $supported_query_types_cnt = count($supported_query_types);

        // loop #1 for each token: select_expr, table_ref for SELECT
 
        for ($i = 0; $i < $size; $i++) {


            
            if ($seek_queryend == TRUE) {
                if ($arr[$i]['type'] == 'punct_queryend') {
                    $seek_queryend = FALSE;
                } else {
                    continue;
                } 
            } 

            if ($arr[$i]['type'] == 'punct_queryend' && ($i + 1 != $size)) {
                $result[]  = $subresult;
                $subresult = $subresult_empty;
                continue;
            } 


            if ($arr[$i]['type'] == 'punct_bracket_open_round') {
                $number_of_brackets++;
                if ($in_extract) {
                    $number_of_brackets_in_extract++;
                }
                if ($in_group_concat) {
                    $number_of_brackets_in_group_concat++;
                }
            }
// ==============================================================
            if ($arr[$i]['type'] == 'punct_bracket_close_round') {
                $number_of_brackets--;
                if ($number_of_brackets == 0) {
                    $in_subquery = false;
                }
                if ($in_extract) {
                    $number_of_brackets_in_extract--;
                    if ($number_of_brackets_in_extract == 0) {
                       $in_extract = FALSE;
                    }
                }
                if ($in_group_concat) {
                    $number_of_brackets_in_group_concat--;
                    if ($number_of_brackets_in_group_concat == 0) {
                       $in_group_concat = FALSE;
                    }
                }
            }

            if ($in_subquery) {
                
                continue;
            }
// ==============================================================
            if ($arr[$i]['type'] == 'alpha_functionName') {
                $upper_data = strtoupper($arr[$i]['data']);
                if ($upper_data =='EXTRACT') {
                    $in_extract = TRUE;
                    $number_of_brackets_in_extract = 0;
                }
                if ($upper_data =='GROUP_CONCAT') {
                    $in_group_concat = TRUE;
                    $number_of_brackets_in_group_concat = 0;
                }
            }

// ==============================================================
            if ($arr[$i]['type'] == 'alpha_reservedWord') {
                
                if ($subresult['querytype'] == '') {
                    $subresult['querytype'] = strtoupper($arr[$i]['data']);
                } // end if (querytype was empty)

                // Check if we support this type of query
                if (!PMA_STR_binarySearchInArr($subresult['querytype'], $supported_query_types, $supported_query_types_cnt)) {
                    // Skip ahead to the next one if we don't
                    $seek_queryend = TRUE;
                    continue;
                } // end if (query not supported)

                // upper once
                $upper_data = strtoupper($arr[$i]['data']);
                

                if ($upper_data == 'SELECT') {
                    if ($number_of_brackets > 0) {
                        $in_subquery = true;
                        $seen_subquery = true;
                        // this is a subquery so do not analyze inside it
                        continue;
                    }
                    $seen_from = FALSE;
                    $previous_was_identifier = FALSE;
                    $current_select_expr = -1;
                    $seen_end_of_table_ref = FALSE;
                } // end if (data == SELECT)

                if ($upper_data =='FROM' && !$in_extract) {
                    $current_table_ref = -1;
                    $seen_from = TRUE;
                    $previous_was_identifier = FALSE;
                    $save_table_ref = TRUE;
                } 
            } 


            if ($arr[$i]['type'] == 'quote_backtick' || $arr[$i]['type'] == 'quote_double' || $arr[$i]['type'] == 'quote_single' || $arr[$i]['type'] == 'alpha_identifier' || ($arr[$i]['type'] == 'alpha_reservedWord' && $arr[$i]['forbidden'] == FALSE)) {

                switch ($arr[$i]['type']) {
                    case 'alpha_identifier':
                    case 'alpha_reservedWord':
                       
                        $identifier = $arr[$i]['data'];
                        break;

                    case 'quote_backtick':
                    case 'quote_double':
                    case 'quote_single':
                        $identifier = PMA_unQuote($arr[$i]['data']);
                        break;
                } // end switch

                if ($subresult['querytype'] == 'SELECT'
                 && ! $in_group_concat
                 && ! ($seen_subquery && $arr[$i - 1]['type'] == 'punct_bracket_close_round')) {
                    if (!$seen_from) {
                        if ($previous_was_identifier && isset($chain)) {
                            // found alias for this select_expr, save it
                            // but only if we got something in $chain
                            // (for example, SELECT COUNT(*) AS cnt
                            // puts nothing in $chain, so we avoid
                            // setting the alias)
                            $alias_for_select_expr = $identifier;
                        } else {
                            $chain[] = $identifier;
                            $previous_was_identifier = TRUE;

                        } // end if !$previous_was_identifier
                    } else {
                        // ($seen_from)
                        if ($save_table_ref && !$seen_end_of_table_ref) {
                            if ($previous_was_identifier) {
                                // found alias for table ref
                                // save it for later
                                $alias_for_table_ref = $identifier;
                            } else {
                                $chain[] = $identifier;
                                $previous_was_identifier = TRUE;

                            } // end if ($previous_was_identifier)
                        } // end if ($save_table_ref &&!$seen_end_of_table_ref)
                    } // end if (!$seen_from)
                } // end if (querytype SELECT)
            } // end if (quote_backtick or double quote or alpha_identifier)

// ===================================
            if ($arr[$i]['type'] == 'punct_qualifier') {
                // to be able to detect an identifier following another
                $previous_was_identifier = FALSE;
                continue;
            } // end if (punct_qualifier)

           

            if (isset($chain) && !$seen_end_of_table_ref
             && ((!$seen_from && $arr[$i]['type'] == 'punct_listsep')
              || ($arr[$i]['type'] == 'alpha_reservedWord' && $upper_data == 'FROM'))) {
                $size_chain = count($chain);
                $current_select_expr++;
                $subresult['select_expr'][$current_select_expr] = array(
                  'expr' => '',
                  'alias' => '',
                  'db'   => '',
                  'table_name' => '',
                  'table_true_name' => '',
                  'column' => ''
                 );

                if (isset($alias_for_select_expr) && strlen($alias_for_select_expr)) {
                    // we had found an alias for this select expression
                    $subresult['select_expr'][$current_select_expr]['alias'] = $alias_for_select_expr;
                    unset($alias_for_select_expr);
                }
                // there is at least a column
                $subresult['select_expr'][$current_select_expr]['column'] = $chain[$size_chain - 1];
                $subresult['select_expr'][$current_select_expr]['expr'] = $chain[$size_chain - 1];

                // maybe a table
                if ($size_chain > 1) {
                    $subresult['select_expr'][$current_select_expr]['table_name'] = $chain[$size_chain - 2];
                    // we assume for now that this is also the true name
                    $subresult['select_expr'][$current_select_expr]['table_true_name'] = $chain[$size_chain - 2];
                    $subresult['select_expr'][$current_select_expr]['expr']
                     = $subresult['select_expr'][$current_select_expr]['table_name']
                      . '.' . $subresult['select_expr'][$current_select_expr]['expr'];
                } // end if ($size_chain > 1)

                // maybe a db
                if ($size_chain > 2) {
                    $subresult['select_expr'][$current_select_expr]['db'] = $chain[$size_chain - 3];
                    $subresult['select_expr'][$current_select_expr]['expr']
                     = $subresult['select_expr'][$current_select_expr]['db']
                      . '.' . $subresult['select_expr'][$current_select_expr]['expr'];
                } // end if ($size_chain > 2)
                unset($chain);

               
                if (($arr[$i]['type'] == 'alpha_reservedWord')
                 && ($upper_data != 'FROM')) {
                    $previous_was_identifier = TRUE;
                }

            } // end if (save a select expr)



            if (isset($chain) && $seen_from && $save_table_ref
             && ($arr[$i]['type'] == 'punct_listsep'
               || ($arr[$i]['type'] == 'alpha_reservedWord' && $upper_data!="AS")
               || $seen_end_of_table_ref
               || $i==$size-1)) {

                $size_chain = count($chain);
                $current_table_ref++;
                $subresult['table_ref'][$current_table_ref] = array(
                  'expr'            => '',
                  'db'              => '',
                  'table_name'      => '',
                  'table_alias'     => '',
                  'table_true_name' => ''
                 );
                if (isset($alias_for_table_ref) && strlen($alias_for_table_ref)) {
                    $subresult['table_ref'][$current_table_ref]['table_alias'] = $alias_for_table_ref;
                    unset($alias_for_table_ref);
                }
                $subresult['table_ref'][$current_table_ref]['table_name'] = $chain[$size_chain - 1];
                // we assume for now that this is also the true name
                $subresult['table_ref'][$current_table_ref]['table_true_name'] = $chain[$size_chain - 1];
                $subresult['table_ref'][$current_table_ref]['expr']
                     = $subresult['table_ref'][$current_table_ref]['table_name'];
                // maybe a db
                if ($size_chain > 1) {
                    $subresult['table_ref'][$current_table_ref]['db'] = $chain[$size_chain - 2];
                    $subresult['table_ref'][$current_table_ref]['expr']
                     = $subresult['table_ref'][$current_table_ref]['db']
                      . '.' . $subresult['table_ref'][$current_table_ref]['expr'];
                } // end if ($size_chain > 1)

                // add the table alias into the whole expression
                $subresult['table_ref'][$current_table_ref]['expr']
                 .= ' ' . $subresult['table_ref'][$current_table_ref]['table_alias'];

                unset($chain);
                $previous_was_identifier = TRUE;
                //continue;

            } // end if (save a table ref)


           

            if (isset($current_table_ref) && ($seen_end_of_table_ref || $i == $size-1) && $subresult != $subresult_empty) {
                for ($tr=0; $tr <= $current_table_ref; $tr++) {
                    $alias = $subresult['table_ref'][$tr]['table_alias'];
                    $truename = $subresult['table_ref'][$tr]['table_true_name'];
                    for ($se=0; $se <= $current_select_expr; $se++) {
                        if (isset($alias) && strlen($alias) && $subresult['select_expr'][$se]['table_true_name']
                           == $alias) {
                            $subresult['select_expr'][$se]['table_true_name']
                             = $truename;
                        } // end if (found the alias)
                    } // end for (select expressions)

                } // end for (table refs)
            } // end if (set the true names)


        
            if (($arr[$i]['type'] != 'alpha_identifier')
             && ($arr[$i]['type'] != 'quote_double')
             && ($arr[$i]['type'] != 'quote_single')
             && ($arr[$i]['type'] != 'quote_backtick')) {
                $previous_was_identifier = FALSE;
            } // end if

            // however, if we are on AS, we must keep the $previous_was_identifier
            if (($arr[$i]['type'] == 'alpha_reservedWord')
             && ($upper_data == 'AS'))  {
                $previous_was_identifier = TRUE;
            }

            if (($arr[$i]['type'] == 'alpha_reservedWord')
             && ($upper_data =='ON' || $upper_data =='USING')) {
                $save_table_ref = FALSE;
            } // end if (data == ON)

            if (($arr[$i]['type'] == 'alpha_reservedWord')
             && ($upper_data =='JOIN' || $upper_data =='FROM')) {
                $save_table_ref = TRUE;
            } // end if (data == JOIN)

            
            if (!$seen_end_of_table_ref) {
                
                if (($i == $size-1)
                 || ($arr[$i]['type'] == 'alpha_reservedWord'
                 && !$in_group_concat
                 && PMA_STR_binarySearchInArr($upper_data, $words_ending_table_ref, $words_ending_table_ref_cnt))) {
                    $seen_end_of_table_ref = TRUE;
                    // to be able to save the last table ref, but do not
                    // set it true if we found a word like "ON" that has
                    // already set it to false
                    if (isset($save_table_ref) && $save_table_ref != FALSE) {
                        $save_table_ref = TRUE;
                    } //end if

                } // end if (check for end of table ref)
            } //end if (!$seen_end_of_table_ref)

            if ($seen_end_of_table_ref) {
                $save_table_ref = FALSE;
            } // end if

        } // end for $i (loop #1)

        //DEBUG
        /*
          if (isset($current_select_expr)) {
           for ($trace=0; $trace<=$current_select_expr; $trace++) {
               echo "<br />";
               reset ($subresult['select_expr'][$trace]);
               while (list ($key, $val) = each ($subresult['select_expr'][$trace]))
                   echo "sel expr $trace $key => $val<br />\n";
               }
          }

          if (isset($current_table_ref)) {
           echo "current_table_ref = " . $current_table_ref . "<br>";
           for ($trace=0; $trace<=$current_table_ref; $trace++) {

               echo "<br />";
               reset ($subresult['table_ref'][$trace]);
               while (list ($key, $val) = each ($subresult['table_ref'][$trace]))
               echo "table ref $trace $key => $val<br />\n";
               }
          }
        */
        // -------------------------------------------------------


        // loop #2: - queryflags
        //          - querytype (for queries != 'SELECT')
        //          - section_before_limit, section_after_limit
        //
        // we will also need this queryflag in loop 2
        // so set it here
        if (isset($current_table_ref) && $current_table_ref > -1) {
            $subresult['queryflags']['select_from'] = 1;
        }

        $section_before_limit = '';
        $section_after_limit = ''; // truly the section after the limit clause
        $seen_reserved_word = FALSE;
        $seen_group = FALSE;
        $seen_order = FALSE;
        $seen_order_by = FALSE;
        $in_group_by = FALSE; // true when we are inside the GROUP BY clause
        $in_order_by = FALSE; // true when we are inside the ORDER BY clause
        $in_having = FALSE; // true when we are inside the HAVING clause
        $in_select_expr = FALSE; // true when we are inside the select expr clause
        $in_where = FALSE; // true when we are inside the WHERE clause
        $seen_limit = FALSE; // true if we have seen a LIMIT clause
        $in_limit = FALSE; // true when we are inside the LIMIT clause
        $after_limit = FALSE; // true when we are after the LIMIT clause
        $in_from = FALSE; // true when we are in the FROM clause
        $in_group_concat = FALSE;
        $first_reserved_word = '';
        $current_identifier = '';
        $unsorted_query = $arr['raw']; // in case there is no ORDER BY
        $number_of_brackets = 0;
        $in_subquery = false;

        for ($i = 0; $i < $size; $i++) {
//DEBUG echo "Loop2 <strong>"  . $arr[$i]['data'] . "</strong> (" . $arr[$i]['type'] . ")<br />";

            // need_confirm
            //
            // check for reserved words that will have to generate
            // a confirmation request later in sql.php
            // the cases are:
            //   DROP TABLE
            //   DROP DATABASE
            //   ALTER TABLE... DROP
            //   DELETE FROM...
            //
            // this code is not used for confirmations coming from functions.js

            if ($arr[$i]['type'] == 'punct_bracket_open_round') {
                $number_of_brackets++;
            }

            if ($arr[$i]['type'] == 'punct_bracket_close_round') {
                $number_of_brackets--;
                if ($number_of_brackets == 0) {
                    $in_subquery = false;
                }
            }

            if ($arr[$i]['type'] == 'alpha_reservedWord') {
                $upper_data = strtoupper($arr[$i]['data']);

                if ($upper_data == 'SELECT' && $number_of_brackets > 0) {
                    $in_subquery = true;
                }

                if (!$seen_reserved_word) {
                    $first_reserved_word = $upper_data;
                    $subresult['querytype'] = $upper_data;
                    $seen_reserved_word = TRUE;

                    // if the first reserved word is DROP or DELETE,
                    // we know this is a query that needs to be confirmed
                    if ($first_reserved_word=='DROP'
                     || $first_reserved_word == 'DELETE'
                     || $first_reserved_word == 'TRUNCATE') {
                        $subresult['queryflags']['need_confirm'] = 1;
                    }

                    if ($first_reserved_word=='SELECT'){
                        $position_of_first_select = $i;
                    }

                } else {
                    if ($upper_data == 'DROP' && $first_reserved_word == 'ALTER') {
                        $subresult['queryflags']['need_confirm'] = 1;
                    }
                }

                if ($upper_data == 'LIMIT' && ! $in_subquery) {
                    $section_before_limit = substr($arr['raw'], 0, $arr[$i]['pos'] - 5);
                    $in_limit = TRUE;
                    $seen_limit = TRUE;
                    $limit_clause = '';
                    $in_order_by = FALSE; 
                }

                if ($upper_data == 'PROCEDURE') {
                    $subresult['queryflags']['procedure'] = 1;
                    $in_limit = FALSE;
                    $after_limit = TRUE;
                }
                
                if ($upper_data == 'SELECT') {
                    $in_select_expr = TRUE;
                    $select_expr_clause = '';
                }
                if ($upper_data == 'DISTINCT' && !$in_group_concat) {
                    $subresult['queryflags']['distinct'] = 1;
                }

                if ($upper_data == 'UNION') {
                    $subresult['queryflags']['union'] = 1;
                }

                if ($upper_data == 'JOIN') {
                    $subresult['queryflags']['join'] = 1;
                }

                if ($upper_data == 'OFFSET') {
                    $subresult['queryflags']['offset'] = 1;
                }

                // if this is a real SELECT...FROM
                if ($upper_data == 'FROM' && isset($subresult['queryflags']['select_from']) && $subresult['queryflags']['select_from'] == 1) {
                    $in_from = TRUE;
                    $from_clause = '';
                    $in_select_expr = FALSE;
                }


                // (we could have less resetting of variables to FALSE
                // if we trust that the query respects the standard
                // MySQL order for clauses)

                // we use $seen_group and $seen_order because we are looking
                // for the BY
                if ($upper_data == 'GROUP') {
                    $seen_group = TRUE;
                    $seen_order = FALSE;
                    $in_having = FALSE;
                    $in_order_by = FALSE;
                    $in_where = FALSE;
                    $in_select_expr = FALSE;
                    $in_from = FALSE;
                }
                if ($upper_data == 'ORDER' && !$in_group_concat) {
                    $seen_order = TRUE;
                    $seen_group = FALSE;
                    $in_having = FALSE;
                    $in_group_by = FALSE;
                    $in_where = FALSE;
                    $in_select_expr = FALSE;
                    $in_from = FALSE;
                }
                if ($upper_data == 'HAVING') {
                    $in_having = TRUE;
                    $having_clause = '';
                    $seen_group = FALSE;
                    $seen_order = FALSE;
                    $in_group_by = FALSE;
                    $in_order_by = FALSE;
                    $in_where = FALSE;
                    $in_select_expr = FALSE;
                    $in_from = FALSE;
                }

                if ($upper_data == 'WHERE') {
                    $in_where = TRUE;
                    $where_clause = '';
                    $where_clause_identifiers = array();
                    $seen_group = FALSE;
                    $seen_order = FALSE;
                    $in_group_by = FALSE;
                    $in_order_by = FALSE;
                    $in_having = FALSE;
                    $in_select_expr = FALSE;
                    $in_from = FALSE;
                }

                if ($upper_data == 'BY') {
                    if ($seen_group) {
                        $in_group_by = TRUE;
                        $group_by_clause = '';
                    }
                    if ($seen_order) {
                        $seen_order_by = TRUE;
                        // Here we assume that the ORDER BY keywords took
                        // exactly 8 characters.
                        // We use PMA_substr() to be charset-safe; otherwise
                        // if the table name contains accents, the unsorted
                        // query would be missing some characters.
                        $unsorted_query = PMA_substr($arr['raw'], 0, $arr[$i]['pos'] - 8);
                        $in_order_by = TRUE;
                        $order_by_clause = '';
                    }
                }

                // if we find one of the words that could end the clause
                if (PMA_STR_binarySearchInArr($upper_data, $words_ending_clauses, $words_ending_clauses_cnt)) {

                    $in_group_by = FALSE;
                    $in_order_by = FALSE;
                    $in_having   = FALSE;
                    $in_where    = FALSE;
                    $in_select_expr = FALSE;
                    $in_from = FALSE;
                }

            } // endif (reservedWord)



            $sep = ' ';
            if ($arr[$i]['type'] == 'alpha_functionName') {
                $sep='';
                $upper_data = strtoupper($arr[$i]['data']);
                if ($upper_data =='GROUP_CONCAT') {
                    $in_group_concat = TRUE;
                    $number_of_brackets_in_group_concat = 0;
                }
            }

            if ($arr[$i]['type'] == 'punct_bracket_open_round') {
                if ($in_group_concat) {
                    $number_of_brackets_in_group_concat++;
                }
            }
            if ($arr[$i]['type'] == 'punct_bracket_close_round') {
                if ($in_group_concat) {
                    $number_of_brackets_in_group_concat--;
                    if ($number_of_brackets_in_group_concat == 0) {
                        $in_group_concat = FALSE;
                    }
                }
            }

            // do not add a space after an identifier if followed by a dot
            if ($arr[$i]['type'] == 'alpha_identifier' && $i < $size - 1 && $arr[$i + 1]['data'] == '.') {
                $sep = '';
            }

            // do not add a space after a dot if followed by an identifier
            if ($arr[$i]['data'] == '.' && $i < $size - 1 && $arr[$i + 1]['type'] == 'alpha_identifier') {
                $sep = '';
            }

            if ($in_select_expr && $upper_data != 'SELECT' && $upper_data != 'DISTINCT') {
                $select_expr_clause .= $arr[$i]['data'] . $sep;
            }
            if ($in_from && $upper_data != 'FROM') {
                $from_clause .= $arr[$i]['data'] . $sep;
            }
            if ($in_group_by && $upper_data != 'GROUP' && $upper_data != 'BY') {
                $group_by_clause .= $arr[$i]['data'] . $sep;
            }
            if ($in_order_by && $upper_data != 'ORDER' && $upper_data != 'BY') {
                // add a space only before ASC or DESC
                // not around the dot between dbname and tablename
                if ($arr[$i]['type'] == 'alpha_reservedWord') {
                    $order_by_clause .= $sep;
                }
                $order_by_clause .= $arr[$i]['data'];
            }
            if ($in_having && $upper_data != 'HAVING') {
                $having_clause .= $arr[$i]['data'] . $sep;
            }
            if ($in_where && $upper_data != 'WHERE') {
                $where_clause .= $arr[$i]['data'] . $sep;

                if (($arr[$i]['type'] == 'quote_backtick')
                 || ($arr[$i]['type'] == 'alpha_identifier')) {
                    $where_clause_identifiers[] = $arr[$i]['data'];
                }
            }

            // to grab the rest of the query after the ORDER BY clause
            if (isset($subresult['queryflags']['select_from'])
             && $subresult['queryflags']['select_from'] == 1
             && ! $in_order_by
             && $seen_order_by
             && $upper_data != 'BY') {
                $unsorted_query .= $arr[$i]['data'];
                if ($arr[$i]['type'] != 'punct_bracket_open_round'
                 && $arr[$i]['type'] != 'punct_bracket_close_round'
                 && $arr[$i]['type'] != 'punct') {
                    $unsorted_query .= $sep;
                }
            }

            if ($in_limit) {
                if ($upper_data == 'OFFSET') {
                    $limit_clause .= $sep;
                }
                $limit_clause .= $arr[$i]['data'];
                if ($upper_data == 'LIMIT' || $upper_data == 'OFFSET') {
                    $limit_clause .= $sep;
                }
            }
            if ($after_limit && $seen_limit) {
                $section_after_limit .= $arr[$i]['data'] . $sep;
            }

            // clear $upper_data for next iteration
            $upper_data='';
        } // end for $i (loop #2)
        if (empty($section_before_limit)) {
            $section_before_limit = $arr['raw'];
        }

        // -----------------------------------------------------
        // loop #3: foreign keys and MySQL 4.1.2+ TIMESTAMP options
        // (for now, check only the first query)
        // (for now, identifiers are assumed to be backquoted)

        // If we find that we are dealing with a CREATE TABLE query,
        // we look for the next punct_bracket_open_round, which
        // introduces the fields list. Then, when we find a
        // quote_backtick, it must be a field, so we put it into
        // the create_table_fields array. Even if this field is
        // not a timestamp, it will be useful when logic has been
        // added for complete field attributes analysis.

        $seen_foreign = FALSE;
        $seen_references = FALSE;
        $seen_constraint = FALSE;
        $foreign_key_number = -1;
        $seen_create_table = FALSE;
        $seen_create = FALSE;
        $seen_alter = FALSE;
        $in_create_table_fields = FALSE;
        $brackets_level = 0;
        $in_timestamp_options = FALSE;
        $seen_default = FALSE;

        for ($i = 0; $i < $size; $i++) {
        // DEBUG echo "Loop 3 <strong>" . $arr[$i]['data'] . "</strong> " . $arr[$i]['type'] . "<br />";

            if ($arr[$i]['type'] == 'alpha_reservedWord') {
                $upper_data = strtoupper($arr[$i]['data']);

                if ($upper_data == 'NOT' && $in_timestamp_options) {
                    $create_table_fields[$current_identifier]['timestamp_not_null'] = TRUE;

                }

                if ($upper_data == 'CREATE') {
                    $seen_create = TRUE;
                }

                if ($upper_data == 'ALTER') {
                    $seen_alter = TRUE;
                }

                if ($upper_data == 'TABLE' && $seen_create) {
                    $seen_create_table = TRUE;
                    $create_table_fields = array();
                }

                if ($upper_data == 'CURRENT_TIMESTAMP') {
                    if ($in_timestamp_options) {
                        if ($seen_default) {
                            $create_table_fields[$current_identifier]['default_current_timestamp'] = TRUE;
                        }
                    }
                }

                if ($upper_data == 'CONSTRAINT') {
                    $foreign_key_number++;
                    $seen_foreign = FALSE;
                    $seen_references = FALSE;
                    $seen_constraint = TRUE;
                }
                if ($upper_data == 'FOREIGN') {
                    $seen_foreign = TRUE;
                    $seen_references = FALSE;
                    $seen_constraint = FALSE;
                }
                if ($upper_data == 'REFERENCES') {
                    $seen_foreign = FALSE;
                    $seen_references = TRUE;
                    $seen_constraint = FALSE;
                }


                // Cases covered:

                // [ON DELETE {CASCADE | SET NULL | NO ACTION | RESTRICT}]
                // [ON UPDATE {CASCADE | SET NULL | NO ACTION | RESTRICT}]

                // but we set ['on_delete'] or ['on_cascade'] to
                // CASCADE | SET_NULL | NO_ACTION | RESTRICT

                // ON UPDATE CURRENT_TIMESTAMP

                if ($upper_data == 'ON') {
                    if (isset($arr[$i+1]) && $arr[$i+1]['type'] == 'alpha_reservedWord') {
                        $second_upper_data = strtoupper($arr[$i+1]['data']);
                        if ($second_upper_data == 'DELETE') {
                            $clause = 'on_delete';
                        }
                        if ($second_upper_data == 'UPDATE') {
                            $clause = 'on_update';
                        }
                        if (isset($clause)
                        && ($arr[$i+2]['type'] == 'alpha_reservedWord'

                // ugly workaround because currently, NO is not
                // in the list of reserved words in sqlparser.data
                // (we got a bug report about not being able to use
                // 'no' as an identifier)
                           || ($arr[$i+2]['type'] == 'alpha_identifier'
                              && strtoupper($arr[$i+2]['data'])=='NO'))
                          ) {
                            $third_upper_data = strtoupper($arr[$i+2]['data']);
                            if ($third_upper_data == 'CASCADE'
                            || $third_upper_data == 'RESTRICT') {
                                $value = $third_upper_data;
                            } elseif ($third_upper_data == 'SET'
                              || $third_upper_data == 'NO') {
                                if ($arr[$i+3]['type'] == 'alpha_reservedWord') {
                                    $value = $third_upper_data . '_' . strtoupper($arr[$i+3]['data']);
                                }
                            } elseif ($third_upper_data == 'CURRENT_TIMESTAMP') {
                                if ($clause == 'on_update'
                                && $in_timestamp_options) {
                                    $create_table_fields[$current_identifier]['on_update_current_timestamp'] = TRUE;
                                    $seen_default = FALSE;
                                }

                            } else {
                                $value = '';
                            }
                            if (!empty($value)) {
                                $foreign[$foreign_key_number][$clause] = $value;
                            }
                            unset($clause);
                        } // endif (isset($clause))
                    }
                }

            } // end of reserved words analysis


            if ($arr[$i]['type'] == 'punct_bracket_open_round') {
                $brackets_level++;
                if ($seen_create_table && $brackets_level == 1) {
                    $in_create_table_fields = TRUE;
                }
            }


            if ($arr[$i]['type'] == 'punct_bracket_close_round') {
                $brackets_level--;
                if ($seen_references) {
                    $seen_references = FALSE;
                }
                if ($seen_create_table && $brackets_level == 0) {
                    $in_create_table_fields = FALSE;
                }
            }

            if (($arr[$i]['type'] == 'alpha_columnAttrib')) {
                $upper_data = strtoupper($arr[$i]['data']);
                if ($seen_create_table && $in_create_table_fields) {
                    if ($upper_data == 'DEFAULT') {
                        $seen_default = TRUE;
                        $create_table_fields[$current_identifier]['default_value'] = $arr[$i + 1]['data'];
                    }
                }
            }

            
            if (($arr[$i]['type'] == 'alpha_columnType') || ($arr[$i]['type'] == 'alpha_functionName' && $seen_create_table)) {
                $upper_data = strtoupper($arr[$i]['data']);
                if ($seen_create_table && $in_create_table_fields && isset($current_identifier)) {
                    $create_table_fields[$current_identifier]['type'] = $upper_data;
                    if ($upper_data == 'TIMESTAMP') {
                        $arr[$i]['type'] = 'alpha_columnType';
                        $in_timestamp_options = TRUE;
                    } else {
                        $in_timestamp_options = FALSE;
                        if ($upper_data == 'CHAR') {
                            $arr[$i]['type'] = 'alpha_columnType';
                        }
                    }
                }
            }


            if ($arr[$i]['type'] == 'quote_backtick' || $arr[$i]['type'] == 'alpha_identifier') {

                if ($arr[$i]['type'] == 'quote_backtick') {
                    // remove backquotes
                    $identifier = PMA_unQuote($arr[$i]['data']);
                } else {
                    $identifier = $arr[$i]['data'];
                }

                if ($seen_create_table && $in_create_table_fields) {
                    $current_identifier = $identifier;
                    // we set this one even for non TIMESTAMP type
                    $create_table_fields[$current_identifier]['timestamp_not_null'] = FALSE;
                }

                if ($seen_constraint) {
                    $foreign[$foreign_key_number]['constraint'] = $identifier;
                }

                if ($seen_foreign && $brackets_level > 0) {
                    $foreign[$foreign_key_number]['index_list'][] = $identifier;
                }

                if ($seen_references) {
                    if ($seen_alter && $brackets_level > 0) {
                        $foreign[$foreign_key_number]['ref_index_list'][] = $identifier;
                    // here, the first bracket level corresponds to the
                    // bracket of CREATE TABLE
                    // so if we are on level 2, it must be the index list
                    // of the foreign key REFERENCES
                    } elseif ($brackets_level > 1) {
                        $foreign[$foreign_key_number]['ref_index_list'][] = $identifier;
                    } elseif ($arr[$i+1]['type'] == 'punct_qualifier') {
                        // identifier is `db`.`table`
                        // the first pass will pick the db name
                        // the next pass will pick the table name
                        $foreign[$foreign_key_number]['ref_db_name'] = $identifier;
                    } else {
                        // identifier is `table`
                        $foreign[$foreign_key_number]['ref_table_name'] = $identifier;
                    }
                }
            }
        } // end for $i (loop #3)


        // Fill the $subresult array

        if (isset($create_table_fields)) {
            $subresult['create_table_fields'] = $create_table_fields;
        }

        if (isset($foreign)) {
            $subresult['foreign_keys'] = $foreign;
        }

        if (isset($select_expr_clause)) {
            $subresult['select_expr_clause'] = $select_expr_clause;
        }
        if (isset($from_clause)) {
            $subresult['from_clause'] = $from_clause;
        }
        if (isset($group_by_clause)) {
            $subresult['group_by_clause'] = $group_by_clause;
        }
        if (isset($order_by_clause)) {
            $subresult['order_by_clause'] = $order_by_clause;
        }
        if (isset($having_clause)) {
            $subresult['having_clause'] = $having_clause;
        }
        if (isset($limit_clause)) {
            $subresult['limit_clause'] = $limit_clause;
        }
        if (isset($where_clause)) {
            $subresult['where_clause'] = $where_clause;
        }
        if (isset($unsorted_query) && !empty($unsorted_query)) {
            $subresult['unsorted_query'] = $unsorted_query;
        }
        if (isset($where_clause_identifiers)) {
            $subresult['where_clause_identifiers'] = $where_clause_identifiers;
        }

        if (isset($position_of_first_select)) {
            $subresult['position_of_first_select'] = $position_of_first_select;
            $subresult['section_before_limit'] = $section_before_limit;
            $subresult['section_after_limit'] = $section_after_limit;
        }

        // They are naughty and didn't have a trailing semi-colon,
        // then still handle it properly
		
        if ($subresult['querytype'] != '') {
            $result[] = $subresult;
        }
        return $result;
    } 
	function PMA_STR_binarySearchInArr($str, $arr, $arrsize)
{
    $top    = $arrsize - 1;
    $bottom = 0;
    $found  = false;

    while ($top >= $bottom && $found == false) {
        $mid        = intval(($top + $bottom) / 2);
        $res        = strcmp($str, $arr[$mid]);
        if ($res == 0) {
            $found  = true;
        } elseif ($res < 0) {
            $top    = $mid - 1;
        } else {
            $bottom = $mid + 1;
        }
    } // end while

    return $found;
}
function PMA_backquote($a_name, $do_it = true)
{ 
    if (is_array($a_name)) {
        foreach ($a_name as &$data) {
            $data = PMA_backquote($data, $do_it);
        }
        return $a_name;
    }

    if (! $do_it) {
        global $PMA_SQPdata_forbidden_word;
        global $PMA_SQPdata_forbidden_word_cnt;

        if(! PMA_STR_binarySearchInArr(strtoupper($a_name), $PMA_SQPdata_forbidden_word, $PMA_SQPdata_forbidden_word_cnt)) {
            return $a_name;
        }
    }

  
    if (strlen($a_name) && $a_name !== '*') {
        return '`' . str_replace('`', '``', $a_name) . '`';
    } else {
        return $a_name;
    }
} // end of the 'PMA_backquote()' function
function PMA_exportOutputHandler($line)
    { 
    global $time_start, $dump_buffer, $dump_buffer_len, $save_filename;
     $data =  JFactory::getApplication()->input->post->getArray();
   
    if (isset($data['output_kanji_conversion'])) {
        $line = PMA_kanji_str_conv($line, $data['knjenc'], isset($data['xkana']) ? $data['xkana'] : '');
    }
    // If we have to buffer data, we will perform everything at once at the end
    if (isset($data['buffer_needed'])) {

        $dump_buffer .= $line;
        if (isset($data['onfly_compression'])) {

            $dump_buffer_len += strlen($line);

            if ($dump_buffer_len > $data['memory_limit']) {
                if ($data['output_charset_conversion']) {
                    $dump_buffer = PMA_convert_string($data['charset'], $data['charset_of_file'], $dump_buffer);
                }
                // as bzipped
                if ($data['compression'] == 'bzip2'  && @function_exists('bzcompress')) {
                    $dump_buffer = bzcompress($dump_buffer);
                }
                // as a gzipped file
                elseif ($data['compression'] == 'gzip' && @function_exists('gzencode')) {
                    // without the optional parameter level because it bug
                    $dump_buffer = gzencode($dump_buffer);
                }
                if ($data['save_on_server']) {
                    $write_result = @fwrite($data['file_handle'], $dump_buffer);
                    if (!$write_result || ($write_result != strlen($dump_buffer))) {
                        //$data['message'] = PMA_Message::error(JText::_('Insufficient space to save the file %s.'));
                        $data['message']->addParam($save_filename);
                        return false;
                    }
                } else {
                    echo $dump_buffer;
                }
                $dump_buffer = '';
                $dump_buffer_len = 0;
            }
        } else {
            $time_now = time();
            if ($time_start >= $time_now + 30) {
                $time_start = $time_now;
                header('X-pmaPing: Pong');
            } // end if
        }
    } else {
        if (1) {
            if (isset($data['output_charset_conversion'])) {
                $line = PMA_convert_string($data['charset'], $data['charset_of_file'], $line);
            }
            if (isset($data['save_on_server']) && strlen($line) > 0) {
                $write_result = @fwrite($data['file_handle'], $line);
                if (!$write_result || ($write_result != strlen($line))) {
                    //$data['message'] = PMA_Message::error(JText::_('Insufficient space to save the file %s.'));
                    $data['message']->addParam($save_filename);
                    return false;
                }
                $time_now = time();
                if ($time_start >= $time_now + 30) {
                    $time_start = $time_now;
                    header('X-pmaPing: Pong');
                } // end if
            } else {
                // We export as file - output normally
                echo htmlspecialchars_decode($line);
                
            }
        } else {
            // We export as html - replace special chars
            echo htmlspecialchars_decode($line);
        }
    }
    return true;
} 

function PMA_whichCrlf()
{
    $the_crlf = "\n";

    // The 'PMA_USR_OS' constant is defined in "./libraries/Config.class.php"
    // Win case
    if (PMA_USR_OS == 'Win') {
        $the_crlf = "\r\n";
    }
    // Others
    else {
        $the_crlf = "\n";
    }

    return $the_crlf;
} // end of the 'PMA_whichCrlf()' function

function PMA_reloadNavigation($jsonly=false)
{
    global $cfg;

    // Reloads the navigation frame via JavaScript if required
    if (isset($GLOBALS['reload']) && $GLOBALS['reload']) {
        // one of the reasons for a reload is when a table is dropped
        // in this case, get rid of the table limit offset, otherwise
        // we have a problem when dropping a table on the last page
        // and the offset becomes greater than the total number of tables
        
        echo "\n";
        $reload_url = './navigation.php?' . PMA_generate_common_url($GLOBALS['db'], '', '&');
	if (!$jsonly)
	  echo '<script type="text/javascript">' . PHP_EOL;
	?>
//<![CDATA[
if (typeof(window.parent) != 'undefined'
    && typeof(window.parent.frame_navigation) != 'undefined'
    && window.parent.goTo) {
    window.parent.goTo('<?php echo $reload_url; ?>');
}
//]]>
<?php
if (!$jsonly)
  echo '</script>' . PHP_EOL;

        unset($GLOBALS['reload']);
    }
}


function PMA_showMessage($message, $sql_query = null, $type = 'notice', $is_view = false)
{
    
    if( $GLOBALS['is_ajax_request'] == true && !isset($GLOBALS['buffer_message']) ) {
        ob_start();
    }
    global $cfg;

    if (null === $sql_query) {
        if (! empty($GLOBALS['display_query'])) {
            $sql_query = $GLOBALS['display_query'];
        } elseif ($cfg['SQP']['fmtType'] == 'none' && ! empty($GLOBALS['unparsed_sql'])) {
            $sql_query = $GLOBALS['unparsed_sql'];
        } elseif (! empty($GLOBALS['sql_query'])) {
            $sql_query = $GLOBALS['sql_query'];
        } else {
            $sql_query = '';
        }
    }

    if (isset($GLOBALS['using_bookmark_message'])) {
        $GLOBALS['using_bookmark_message']->display();
        unset($GLOBALS['using_bookmark_message']);
    }

    
    if (! $is_view && strlen($GLOBALS['table']) && $cfg['ShowTooltip']) {
        $tooltip = PMA_Table::sGetToolTip($GLOBALS['db'], $GLOBALS['table']);
        $uni_tbl = PMA_jsFormat($GLOBALS['db'] . '.' . $GLOBALS['table'], false);
        echo "\n";
        echo '<script type="text/javascript">' . "\n";
        echo '//<![CDATA[' . "\n";
        echo "if (window.parent.updateTableTitle) window.parent.updateTableTitle('" . $uni_tbl . "', '" . PMA_jsFormat($tooltip, false) . "');" . "\n";
        echo '//]]>' . "\n";
        echo '</script>' . "\n";
    }

   
    if (strlen($GLOBALS['table'])
     && $GLOBALS['sql_query'] == 'TRUNCATE TABLE ' . PMA_backquote($GLOBALS['table'])) {
        if (PMA_Table::sGetStatusInfo($GLOBALS['db'], $GLOBALS['table'], 'Index_length') > 1024) {
            PMA_DBI_try_query('REPAIR TABLE ' . PMA_backquote($GLOBALS['table']));
        }
    }
    unset($tbl_status);

   
    echo '<div id="result_query" align="' . ( isset($GLOBALS['cell_align_left']) ? $GLOBALS['cell_align_left'] : '' ) . '">' . "\n";

    if ($message instanceof PMA_Message) {
        if (isset($GLOBALS['special_message'])) {
            $message->addMessage($GLOBALS['special_message']);
            unset($GLOBALS['special_message']);
        }
        $message->display();
        $type = $message->getLevel();
    } else {
        echo '<div class="' . $type . '">';
        echo PMA_sanitize($message);
        if (isset($GLOBALS['special_message'])) {
            echo PMA_sanitize($GLOBALS['special_message']);
            unset($GLOBALS['special_message']);
        }
        echo '</div>';
    }

    if ($cfg['ShowSQL'] == true && ! empty($sql_query)) {
        // Html format the query to be displayed
        // If we want to show some sql code it is easiest to create it here
        /* SQL-Parser-Analyzer */

        if (! empty($GLOBALS['show_as_php'])) {
            $new_line = '\\n"<br />' . "\n"
                . '&nbsp;&nbsp;&nbsp;&nbsp;. "';
            $query_base = htmlspecialchars(addslashes($sql_query));
            $query_base = preg_replace('/((\015\012)|(\015)|(\012))/', $new_line, $query_base);
        } else {
            $query_base = $sql_query;
        }

        $query_too_big = false;

        if (strlen($query_base) > $cfg['MaxCharactersInDisplayedSQL']) {
            // when the query is large (for example an INSERT of binary
            // data), the parser chokes; so avoid parsing the query
            $query_too_big = true;
            $shortened_query_base = nl2br(htmlspecialchars(substr($sql_query, 0, $cfg['MaxCharactersInDisplayedSQL']) . '[...]'));
        } elseif (! empty($GLOBALS['parsed_sql'])
         && $query_base == $GLOBALS['parsed_sql']['raw']) {
            // (here, use "! empty" because when deleting a bookmark,
            // $GLOBALS['parsed_sql'] is set but empty
            $parsed_sql = $GLOBALS['parsed_sql'];
        } else {
            // Parse SQL if needed
            $parsed_sql = PMA_SQP_parse($query_base);
        }

        // Analyze it
        if (isset($parsed_sql) && ! PMA_SQP_isError()) {
            $analyzed_display_query = PMA_SQP_analyze($parsed_sql);
           

            if (isset($analyzed_display_query[0]['queryflags']['select_from'])
             && isset($GLOBALS['sql_limit_to_append'])) {
                $query_base = $analyzed_display_query[0]['section_before_limit']
                    . "\n" . $GLOBALS['sql_limit_to_append']
                    . $analyzed_display_query[0]['section_after_limit'];
                // Need to reparse query
                $parsed_sql = PMA_SQP_parse($query_base);
            }
        }

        if (! empty($GLOBALS['show_as_php'])) {
            $query_base = '$sql  = "' . $query_base;
        } elseif (! empty($GLOBALS['validatequery'])) {
            try {
                $query_base = PMA_validateSQL($query_base);
            } catch (Exception $e) {
                PMA_Message::error(JText::_('Failed to connect to SQL validator!'))->display();
            }
        } elseif (isset($parsed_sql)) {
            $query_base = PMA_formatSql($parsed_sql, $query_base);
        }

        // Prepares links that may be displayed to edit/explain the query
        // (don't go to default pages, we must go to the page
        // where the query box is available)

        // Basic url query part
        $url_params = array();
        if (! isset($GLOBALS['db'])) {
            $GLOBALS['db'] = '';
        }
        if (strlen($GLOBALS['db'])) {
            $url_params['db'] = $GLOBALS['db'];
            if (strlen($GLOBALS['table'])) {
                $url_params['table'] = $GLOBALS['table'];
                $edit_link = 'tbl_sql.php';
            } else {
                $edit_link = 'db_sql.php';
            }
        } else {
            $edit_link = 'server_sql.php';
        }

        // Want to have the query explained (Mike Beck 2002-05-22)
        // but only explain a SELECT (that has not been explained)
        /* SQL-Parser-Analyzer */
        $explain_link = '';
        if (! empty($cfg['SQLQuery']['Explain']) && ! $query_too_big) {
            $explain_params = $url_params;
            // Detect if we are validating as well
            // To preserve the validate uRL data
            if (! empty($GLOBALS['validatequery'])) {
                $explain_params['validatequery'] = 1;
            }
            $is_select = false;
            if (preg_match('@^SELECT[[:space:]]+@i', $sql_query)) {
                $explain_params['sql_query'] = 'EXPLAIN ' . $sql_query;
                $_message = JText::_('Explain SQL');
                $is_select = true;
            } elseif (preg_match('@^EXPLAIN[[:space:]]+SELECT[[:space:]]+@i', $sql_query)) {
                $explain_params['sql_query'] = substr($sql_query, 8);
                $_message = JText::_('Skip Explain SQL');
            }
            if (isset($explain_params['sql_query'])) {
                $explain_link = 'import.php' . PMA_generate_common_url($explain_params);
                $explain_link = ' [' . PMA_linkOrButton($explain_link, $_message) . ']';
            }
        } //show explain

        $url_params['sql_query']  = $sql_query;
        $url_params['show_query'] = 1;

        // even if the query is big and was truncated, offer the chance
        // to edit it (unless it's enormous, see PMA_linkOrButton() )
        if (! empty($cfg['SQLQuery']['Edit'])) {
            if ($cfg['EditInWindow'] == true) {
                $onclick = 'window.parent.focus_querywindow(\'' . PMA_jsFormat($sql_query, false) . '\'); return false;';
            } else {
                $onclick = '';
            }

            $edit_link .= PMA_generate_common_url($url_params) . '#querybox';
            $edit_link = ' [' . PMA_linkOrButton($edit_link, JText::_('Edit'), array('onclick' => $onclick)) . ']';
        } else {
            $edit_link = '';
        }

        $url_qpart = PMA_generate_common_url($url_params);

        // Also we would like to get the SQL formed in some nice
        // php-code (Mike Beck 2002-05-22)
        if (! empty($cfg['SQLQuery']['ShowAsPHP']) && ! $query_too_big) {
            $php_params = $url_params;

            if (! empty($GLOBALS['show_as_php'])) {
                $_message = JText::_('Without PHP Code');
            } else {
                $php_params['show_as_php'] = 1;
                $_message = JText::_('Create PHP Code');
            }

            $php_link = 'import.php' . PMA_generate_common_url($php_params);
            $php_link = ' [' . PMA_linkOrButton($php_link, $_message) . ']';

            if (isset($GLOBALS['show_as_php'])) {
                $runquery_link = 'import.php' . PMA_generate_common_url($url_params);
                $php_link .= ' [' . PMA_linkOrButton($runquery_link, JText::_('Submit Query')) . ']';
            }
        } else {
            $php_link = '';
        } //show as php

        // Refresh query
        if (! empty($cfg['SQLQuery']['Refresh'])
         && preg_match('@^(SELECT|SHOW)[[:space:]]+@i', $sql_query)) {
            $refresh_link = 'import.php' . PMA_generate_common_url($url_params);
            $refresh_link = ' [' . PMA_linkOrButton($refresh_link, JText::_('Refresh')) . ']';
        } else {
            $refresh_link = '';
        } //show as php

        if (! empty($cfg['SQLValidator']['use'])
         && ! empty($cfg['SQLQuery']['Validate'])) {
            $validate_params = $url_params;
            if (!empty($GLOBALS['validatequery'])) {
                $validate_message = JText::_('Skip Validate SQL') ;
            } else {
                $validate_params['validatequery'] = 1;
                $validate_message = JText::_('Validate SQL') ;
            }

            $validate_link = 'import.php' . PMA_generate_common_url($validate_params);
            $validate_link = ' [' . PMA_linkOrButton($validate_link, $validate_message) . ']';
        } else {
            $validate_link = '';
        } //validator

        if (!empty($GLOBALS['validatequery'])) {
            echo '<div class="sqlvalidate">';
        } else {
            echo '<code class="sql">';
        }
        if ($query_too_big) {
            echo $shortened_query_base;
        } else {
            echo $query_base;
        }

        //Clean up the end of the PHP
        if (! empty($GLOBALS['show_as_php'])) {
            echo '";';
        }
        if (!empty($GLOBALS['validatequery'])) {
            echo '</div>';
        } else {
            echo '</code>';
        }

        echo '<div class="tools">';
        // avoid displaying a Profiling checkbox that could
        // be checked, which would reexecute an INSERT, for example
        if (! empty($refresh_link)) {
            PMA_profilingCheckbox($sql_query);
        }
        // if needed, generate an invisible form that contains controls for the
        // Inline link; this way, the behavior of the Inline link does not
        // depend on the profiling support or on the refresh link
        if (empty($refresh_link) || ! PMA_profilingSupported()) {
            echo '<form action="sql.php" method="post">';
            echo PMA_generate_common_hidden_inputs($GLOBALS['db'], $GLOBALS['table']);
            echo '<input type="hidden" name="sql_query" value="' . htmlspecialchars($sql_query) . '" />';
            echo '</form>';
        }

        // in the tools div, only display the Inline link when not in ajax
        // mode because 1) it currently does not work and 2) we would
        // have two similar mechanisms on the page for the same goal
        if ($is_select || $GLOBALS['is_ajax_request'] === false) {
        // see in js/functions.js the jQuery code attached to id inline_edit
        // document.write conflicts with jQuery, hence used $().append()
            echo "<script type=\"text/javascript\">\n" .
                "//<![CDATA[\n" .
                "$('.tools form').last().after('[<a href=\"#\" title=\"" .
                PMA_escapeJsString(JText::_('Inline edit of this query')) .
                "\" class=\"inline_edit_sql\">" .
                PMA_escapeJsString(JText::_('Inline')) .
                "</a>]');\n" .
                "//]]>\n" .
                "</script>";
        }
        echo $edit_link . $explain_link . $php_link . $refresh_link . $validate_link;
        echo '</div>';
    }
    echo '</div>';
    if ($GLOBALS['is_ajax_request'] === false) {
        echo '<br class="clearfloat" />';
    }

    // If we are in an Ajax request, we have most probably been called in
    // PMA_ajaxResponse().  Hence, collect the buffer contents and return it
    // to PMA_ajaxResponse(), which will encode it for JSON.
    if( $GLOBALS['is_ajax_request'] == true && !isset($GLOBALS['buffer_message']) ) {
        $buffer_contents =  ob_get_contents();
        ob_end_clean();
        return $buffer_contents;
    }
} // end of the 'PMA_showMessage()' function

function PMA_profilingCheckbox($sql_query)
{
    if (PMA_profilingSupported()) {
        echo '<form action="sql.php" method="post">' . "\n";
        echo PMA_generate_common_hidden_inputs($GLOBALS['db'], $GLOBALS['table']);
        echo '<input type="hidden" name="sql_query" value="' . htmlspecialchars($sql_query) . '" />' . "\n";
        echo '<input type="hidden" name="profiling_form" value="1" />' . "\n";
        PMA_display_html_checkbox('profiling', JText::_('Profiling'), 0, true); 
        echo '<noscript><input type="submit" value="' . JText::_('Go') . '" /></noscript>' . "\n";
        echo '</form>' . "\n";
    }
}


function PMA_profilingResults($profiling_results, $show_chart = false)
{
    echo '<fieldset><legend>' . JText::_('Profiling') . '</legend>' . "\n";
    echo '<div style="float: left;">';
    echo '<table>' . "\n";
    echo ' <tr>' .  "\n";
    echo '  <th>' . JText::_('Status') . '</th>' . "\n";
    echo '  <th>' . JText::_('Time') . '</th>' . "\n";
    echo ' </tr>' .  "\n";

    foreach($profiling_results as $one_result) {
        echo ' <tr>' .  "\n";
        echo '<td>' . $one_result['Status'] . '</td>' .  "\n";
        echo '<td>' . $one_result['Duration'] . '</td>' .  "\n";
    }

    echo '</table>' . "\n";
    echo '</div>';

    if ($show_chart) {
        require_once './libraries/chart.lib.php';
        echo '<div style="float: left;">';
        PMA_chart_profiling($profiling_results);
        echo '</div>';
    }

    echo '</fieldset>' . "\n";
}


 function PMA_SQP_arrayAdd(&$arr, $type, $data, &$arrsize, $pos = 0)
        {
            global $timer;

            $t     = $timer;
            $arr[] = array('type' => $type, 'data' => $data, 'pos' => $pos, 'time' => $t);
            $timer = microtime();
            $arrsize++;
        }
	function PMA_STR_charIsEscaped($string, $pos, $start = 0)
{
    $pos = max(intval($pos), 0);
    $start = max(intval($start), 0);
    $len = strlen($string);
    // Base case:
    // Check for string length or invalid input or special case of input
    // (pos == $start)
    if ($pos <= $start || $len <= max($pos, $start)) {
        return false;
    }

    $pos--;
    $escaped     = false;
    while ($pos >= $start && substr($string, $pos, 1) == '\\') {
        $escaped = !$escaped;
        $pos--;
    } // end while

    return $escaped;
}	
 function PMA_SQP_parse($sql)
    {
        global $PMA_SQPdata_column_attrib, $PMA_SQPdata_reserved_word, $PMA_SQPdata_column_type, $PMA_SQPdata_function_name,
               $PMA_SQPdata_column_attrib_cnt, $PMA_SQPdata_reserved_word_cnt, $PMA_SQPdata_column_type_cnt, $PMA_SQPdata_function_name_cnt;
        global $mysql_charsets, $mysql_collations_flat, $mysql_charsets_count, $mysql_collations_count;
        global $PMA_SQPdata_forbidden_word, $PMA_SQPdata_forbidden_word_cnt;

        // Convert all line feeds to Unix style
        $sql = str_replace("\r\n", "\n", $sql);
        $sql = str_replace("\r", "\n", $sql);

        $len = strlen($sql);
        if ($len == 0) {
            return array();
        }

        $sql_array               = array();
        $sql_array['raw']        = $sql;
        $count1                  = 0;
        $count2                  = 0;
        $punct_queryend          = ';';
        $punct_qualifier         = '.';
        $punct_listsep           = ',';
        $punct_level_plus        = '(';
        $punct_level_minus       = ')';
        $punct_user              = '@';
        $digit_floatdecimal      = '.';
        $digit_hexset            = 'x';
        $bracket_list            = '()[]{}';
        $allpunct_list           =  '-,;:!?/.^~\*&%+<=>|';
        $allpunct_list_pair      = array (
            0 => '!=',
            1 => '&&',
            2 => ':=',
            3 => '<<',
            4 => '<=',
            5 => '<=>',
            6 => '<>',
            7 => '>=',
            8 => '>>',
            9 => '||',
            10 => '==',
        );
        $allpunct_list_pair_size = 11; //count($allpunct_list_pair);
        $quote_list              = '\'"`';
        $arraysize               = 0;

        $previous_was_space   = false;
        $this_was_space       = false;
        $previous_was_bracket = false;
        $this_was_bracket     = false;
        $previous_was_punct   = false;
        $this_was_punct       = false;
        $previous_was_listsep = false;
        $this_was_listsep     = false;
        $previous_was_quote   = false;
        $this_was_quote       = false;

        while ($count2 < $len) {
            $c      = substr($sql, $count2, 1);
            $count1 = $count2;

            $previous_was_space = $this_was_space;
            $this_was_space = false;
            $previous_was_bracket = $this_was_bracket;
            $this_was_bracket = false;
            $previous_was_punct = $this_was_punct;
            $this_was_punct = false;
            $previous_was_listsep = $this_was_listsep;
            $this_was_listsep = false;
            $previous_was_quote = $this_was_quote;
            $this_was_quote = false;

            if (($c == "\n")) {
                $this_was_space = true;
                $count2++;
                PMA_SQP_arrayAdd($sql_array, 'white_newline', '', $arraysize);
                continue;
            }

            // Checks for white space
			
            if (preg_match('/\s/',$c)) {
                $this_was_space = true;
                $count2++;
                continue;
            }

            // Checks for comment lines.
            // MySQL style #
            // C style /* */
            // ANSI style --
            if (($c == '#')
                || (($count2 + 1 < $len) && ($c == '/') && (substr($sql, $count2 + 1, 1) == '*'))
                || (($count2 + 2 == $len) && ($c == '-') && (substr($sql, $count2 + 1, 1) == '-'))
                || (($count2 + 2 < $len) && ($c == '-') && (substr($sql, $count2 + 1, 1) == '-') && ((substr($sql, $count2 + 2, 1) <= ' ')))) {
                $count2++;
                $pos  = 0;
                $type = 'bad';
                switch ($c) {
                    case '#':
                        $type = 'mysql';
                    case '-':
                        $type = 'ansi';
                        $pos  = strpos($sql, "\n", $count2);
                        break;
                    case '/':
                        $type = 'c';
                        $pos  = strpos($sql, '*/', $count2);
                        $pos  += 2;
                        break;
                    default:
                        break;
                } // end switch
                $count2 = ($pos < $count2) ? $len : $pos;
                $str    = substr($sql, $count1, $count2 - $count1);
                PMA_SQP_arrayAdd($sql_array, 'comment_' . $type, $str, $arraysize);
                continue;
            } // end if

            // Checks for something inside quotation marks
            if (strpos($quote_list, $c) !== false) {
                $startquotepos   = $count2;
                $quotetype       = $c;
                $count2++;
                $escaped         = FALSE;
                $escaped_escaped = FALSE;
                $pos             = $count2;
                $oldpos          = 0;
                do {
                    $oldpos = $pos;
                    $pos    = strpos(' ' . $sql, $quotetype, $oldpos + 1) - 1;
                    // ($pos === FALSE)
                    if ($pos < 0) {
                        if ($c == '`') {
                            /*
                             * Behave same as MySQL and accept end of query as end of backtick.
                             * I know this is sick, but MySQL behaves like this:
                             *
                             * SELECT * FROM `table
                             *
                             * is treated like
                             *
                             * SELECT * FROM `table`
                             */
                            $pos_quote_separator = strpos(' ' . $sql, ',', $oldpos + 1) - 1;
                            if ($pos_quote_separator < 0) {
                                $len += 1;
                                $sql .= '`';
                                $sql_array['raw'] .= '`';
                                $pos = $len;
                            } else {
                                $len += 1;
                                $sql = substr($sql, 0, $pos_quote_separator) . '`' . substr($sql, $pos_quote_separator);
                                $sql_array['raw'] = $sql;
                                $pos = $pos_quote_separator;
                            }
                            
                        }  else {
                            
                           
                            return $sql_array;
                        }
                    }

                    // If the quote is the first character, it can't be
                    // escaped, so don't do the rest of the code
                    if ($pos == 0) {
                        break;
                    }

                    // Checks for MySQL escaping using a \
                    // And checks for ANSI escaping using the $quotetype character
                    if (($pos < $len) && PMA_STR_charIsEscaped($sql, $pos) && $c != '`') {
                        $pos ++;
                        continue;
                    } elseif (($pos + 1 < $len) && (substr($sql, $pos, 1) == $quotetype) && (substr($sql, $pos + 1, 1) == $quotetype)) {
                        $pos = $pos + 2;
                        continue;
                    } else {
                        break;
                    }
                } while ($len > $pos); // end do

                $count2       = $pos;
                $count2++;
                $type         = 'quote_';
                switch ($quotetype) {
                    case '\'':
                        $type .= 'single';
                        $this_was_quote = true;
                        break;
                    case '"':
                        $type .= 'double';
                        $this_was_quote = true;
                        break;
                    case '`':
                        $type .= 'backtick';
                        $this_was_quote = true;
                        break;
                    default:
                        break;
                } // end switch
                $data = substr($sql, $count1, $count2 - $count1);
                PMA_SQP_arrayAdd($sql_array, $type, $data, $arraysize);
                continue;
            }

            // Checks for brackets
            if (strpos($bracket_list, $c) !== false) {
                // All bracket tokens are only one item long
                $this_was_bracket = true;
                $count2++;
                $type_type     = '';
                if (strpos('([{', $c) !== false) {
                    $type_type = 'open';
                } else {
                    $type_type = 'close';
                }

                $type_style     = '';
                if (strpos('()', $c) !== false) {
                    $type_style = 'round';
                } elseif (strpos('[]', $c) !== false) {
                    $type_style = 'square';
                } else {
                    $type_style = 'curly';
                }

                $type = 'punct_bracket_' . $type_type . '_' . $type_style;
                PMA_SQP_arrayAdd($sql_array, $type, $c, $arraysize);
                continue;
            }
            if (PMA_STR_isSqlIdentifier($c, false)
             || $c == '@'
             || ($c == '.'
              && is_numeric(substr($sql, $count2 + 1, 1))
              && ($previous_was_space || $previous_was_bracket || $previous_was_listsep))) {

             

                $count2++;

                $is_identifier           = $previous_was_punct;
                $is_sql_variable         = $c == '@' && ! $previous_was_quote;
                $is_user                 = $c == '@' && $previous_was_quote;
                $is_digit                = !$is_identifier && !$is_sql_variable && is_numeric($c);
                $is_hex_digit            = $is_digit && $c == '0' && $count2 < $len && substr($sql, $count2, 1) == 'x';
                $is_float_digit          = $c == '.';
                $is_float_digit_exponent = FALSE;

               
                if ($is_hex_digit) {
                    $count2++;
                    $pos = strspn($sql, '0123456789abcdefABCDEF', $count2);
                    if ($pos > $count2) {
                        $count2 = $pos;
                    }
                    unset($pos);
                } elseif ($is_digit) {
                    $pos = strspn($sql, '0123456789', $count2);
                    if ($pos > $count2) {
                        $count2 = $pos;
                    }
                    unset($pos);
                }

                while (($count2 < $len) && PMA_STR_isSqlIdentifier(substr($sql, $count2, 1), ($is_sql_variable || $is_digit))) {
                    $c2 = substr($sql, $count2, 1);
                    if ($is_sql_variable && ($c2 == '.')) {
                        $count2++;
                        continue;
                    }
                    if ($is_digit && (!$is_hex_digit) && ($c2 == '.')) {
                        $count2++;
                        if (!$is_float_digit) {
                            $is_float_digit = TRUE;
                            continue;
                        } else {
                            
                           
                            return $sql_array;
                        }
                    }
                    if ($is_digit && (!$is_hex_digit) && (($c2 == 'e') || ($c2 == 'E'))) {
                        if (!$is_float_digit_exponent) {
                            $is_float_digit_exponent = TRUE;
                            $is_float_digit          = TRUE;
                            $count2++;
                            continue;
                        } else {
                            $is_digit                = FALSE;
                            $is_float_digit          = FALSE;
                        }
                    }
                     else {
                        $is_digit     = FALSE;
                        $is_hex_digit = FALSE;
                    }

                    $count2++;
                }   

                $l    = $count2 - $count1;
                $str  = substr($sql, $count1, $l);

                $type = '';
                if ($is_digit || $is_float_digit || $is_hex_digit) {
                    $type     = 'digit';
                    if ($is_float_digit) {
                        $type .= '_float';
                    } elseif ($is_hex_digit) {
                        $type .= '_hex';
                    } else {
                        $type .= '_integer';
                    }
                } elseif ($is_user) {
                    $type = 'punct_user';
                } elseif ($is_sql_variable != FALSE) {
                    $type = 'alpha_variable';
                } else {
                    $type = 'alpha';
                } // end if... else....
                PMA_SQP_arrayAdd($sql_array, $type, $str, $arraysize, $count2);

                continue;
            }

            // Checks for punct
            if (strpos($allpunct_list, $c) !== false) {
                while (($count2 < $len) && strpos($allpunct_list, substr($sql, $count2, 1)) !== false) {
                    $count2++;
                }
                $l = $count2 - $count1;
                if ($l == 1) {
                    $punct_data = $c;
                } else {
                    $punct_data = substr($sql, $count1, $l);
                }

               

                if ($l == 1) {
                    $t_suffix         = '';
                    switch ($punct_data) {
                        case $punct_queryend:
                            $t_suffix = '_queryend';
                            break;
                        case $punct_qualifier:
                            $t_suffix = '_qualifier';
                            $this_was_punct = true;
                            break;
                        case $punct_listsep:
                            $this_was_listsep = true;
                            $t_suffix = '_listsep';
                            break;
                        default:
                            break;
                    }
                    PMA_SQP_arrayAdd($sql_array, 'punct' . $t_suffix, $punct_data, $arraysize);
                } elseif (PMA_STR_binarySearchInArr($punct_data, $allpunct_list_pair, $allpunct_list_pair_size)) {
                    // Ok, we have one of the valid combined punct expressions
                    PMA_SQP_arrayAdd($sql_array, 'punct', $punct_data, $arraysize);
                } else {
                    // Bad luck, lets split it up more
                    $first  = $punct_data[0];
                    $first2 = $punct_data[0] . $punct_data[1];
                    $last2  = $punct_data[$l - 2] . $punct_data[$l - 1];
                    $last   = $punct_data[$l - 1];
                    if (($first == ',') || ($first == ';') || ($first == '.') || ($first == '*')) {
                        $count2     = $count1 + 1;
                        $punct_data = $first;
                    } elseif (($last2 == '/*') || (($last2 == '--') && ($count2 == $len || substr($sql, $count2, 1) <= ' '))) {
                        $count2     -= 2;
                        $punct_data = substr($sql, $count1, $count2 - $count1);
                    } elseif (($last == '-') || ($last == '+') || ($last == '!')) {
                        $count2--;
                        $punct_data = substr($sql, $count1, $count2 - $count1);
                    

                    } elseif ($last != '~') {
                        
                        return $sql_array;
                    }
                    PMA_SQP_arrayAdd($sql_array, 'punct', $punct_data, $arraysize);
                    continue;
                } // end if... elseif... else
                continue;
            }

            // DEBUG
            $count2++;

            
            return $sql_array;

        } // end while ($count2 < $len)

        /*
        echo '<pre>';
        print_r($sql_array);
        echo '</pre>';
        */

        if ($arraysize > 0) {
            $t_next           = $sql_array[0]['type'];
            $t_prev           = '';
            $t_bef_prev       = '';
            $t_cur            = '';
            $d_next           = $sql_array[0]['data'];
            $d_prev           = '';
            $d_bef_prev       = '';
            $d_cur            = '';
            $d_next_upper     = $t_next == 'alpha' ? strtoupper($d_next) : $d_next;
            $d_prev_upper     = '';
            $d_bef_prev_upper = '';
            $d_cur_upper      = '';
        }

        for ($i = 0; $i < $arraysize; $i++) {
            $t_bef_prev       = $t_prev;
            $t_prev           = $t_cur;
            $t_cur            = $t_next;
            $d_bef_prev       = $d_prev;
            $d_prev           = $d_cur;
            $d_cur            = $d_next;
            $d_bef_prev_upper = $d_prev_upper;
            $d_prev_upper     = $d_cur_upper;
            $d_cur_upper      = $d_next_upper;
            if (($i + 1) < $arraysize) {
                $t_next = $sql_array[$i + 1]['type'];
                $d_next = $sql_array[$i + 1]['data'];
                $d_next_upper = $t_next == 'alpha' ? strtoupper($d_next) : $d_next;
            } else {
                $t_next       = '';
                $d_next       = '';
                $d_next_upper = '';
            }

            //DEBUG echo "[prev: <strong>".$d_prev."</strong> ".$t_prev."][cur: <strong>".$d_cur."</strong> ".$t_cur."][next: <strong>".$d_next."</strong> ".$t_next."]<br />";

            if ($t_cur == 'alpha') {
                $t_suffix     = '_identifier';
                // for example: `thebit` bit(8) NOT NULL DEFAULT b'0'
                if ($t_prev == 'alpha' && $d_prev == 'DEFAULT' && $d_cur == 'b' && $t_next == 'quote_single') {
                    $t_suffix = '_bitfield_constant_introducer';
                } elseif (($t_next == 'punct_qualifier') || ($t_prev == 'punct_qualifier')) {
                    $t_suffix = '_identifier';
                } elseif (($t_next == 'punct_bracket_open_round')
                  && PMA_STR_binarySearchInArr($d_cur_upper, $PMA_SQPdata_function_name, $PMA_SQPdata_function_name_cnt)) {
                    
                    $t_suffix = '_functionName';
                    /* There are functions which might be as well column types */
                } elseif (PMA_STR_binarySearchInArr($d_cur_upper, $PMA_SQPdata_column_type, $PMA_SQPdata_column_type_cnt)) {
                    $t_suffix = '_columnType';

                    
                    if (($d_cur_upper == 'SET' || $d_cur_upper == 'BINARY') && $t_next != 'punct_bracket_open_round') {
                        $t_suffix = '_reservedWord';
                    }
                    //END OF TEMPORARY FIX

                    // CHARACTER is a synonym for CHAR, but can also be meant as
                    // CHARACTER SET. In this case, we have a reserved word.
                    if ($d_cur_upper == 'CHARACTER' && $d_next_upper == 'SET') {
                        $t_suffix = '_reservedWord';
                    }

                    // experimental
                    // current is a column type, so previous must not be
                    // a reserved word but an identifier
                    // CREATE TABLE SG_Persons (first varchar(64))

                    //if ($sql_array[$i-1]['type'] =='alpha_reservedWord') {
                    //    $sql_array[$i-1]['type'] = 'alpha_identifier';
                    //}

                } elseif (PMA_STR_binarySearchInArr($d_cur_upper, $PMA_SQPdata_reserved_word, $PMA_SQPdata_reserved_word_cnt)) {
                    $t_suffix = '_reservedWord';
                } elseif (PMA_STR_binarySearchInArr($d_cur_upper, $PMA_SQPdata_column_attrib, $PMA_SQPdata_column_attrib_cnt)) {
                    $t_suffix = '_columnAttrib';
                    // INNODB is a MySQL table type, but in "SHOW INNODB STATUS",
                    // it should be regarded as a reserved word.
                    if ($d_cur_upper == 'INNODB' && $d_prev_upper == 'SHOW' && $d_next_upper == 'STATUS') {
                        $t_suffix = '_reservedWord';
                    }

                    if ($d_cur_upper == 'DEFAULT' && $d_next_upper == 'CHARACTER') {
                        $t_suffix = '_reservedWord';
                    }
                    // Binary as character set
                    if ($d_cur_upper == 'BINARY' && (
                      ($d_bef_prev_upper == 'CHARACTER' && $d_prev_upper == 'SET')
                      || ($d_bef_prev_upper == 'SET' && $d_prev_upper == '=')
                      || ($d_bef_prev_upper == 'CHARSET' && $d_prev_upper == '=')
                      || $d_prev_upper == 'CHARSET'
                      ) && PMA_STR_binarySearchInArr($d_cur, $mysql_charsets, count($mysql_charsets))) {
                        $t_suffix = '_charset';
                    }
                } elseif (PMA_STR_binarySearchInArr($d_cur, $mysql_charsets, $mysql_charsets_count)
                  || PMA_STR_binarySearchInArr($d_cur, $mysql_collations_flat, $mysql_collations_count)
                  || ($d_cur{0} == '_' && PMA_STR_binarySearchInArr(substr($d_cur, 1), $mysql_charsets, $mysql_charsets_count))) {
                    $t_suffix = '_charset';
                } else {
                    // Do nothing
                }
                // check if present in the list of forbidden words
                if ($t_suffix == '_reservedWord' && PMA_STR_binarySearchInArr($d_cur_upper, $PMA_SQPdata_forbidden_word, $PMA_SQPdata_forbidden_word_cnt)) {
                    $sql_array[$i]['forbidden'] = TRUE;
                } else {
                    $sql_array[$i]['forbidden'] = FALSE;
                }
                $sql_array[$i]['type'] .= $t_suffix;
            }
        } // end for

        // Stores the size of the array inside the array, as count() is a slow
        // operation.
        $sql_array['len'] = $arraysize;

        // DEBUG echo 'After parsing<pre>'; print_r($sql_array); echo '</pre>';
        // Sends the data back
        return $sql_array;
    } // end of the "PMA_SQP_parse()" function
function PMA_STR_isSqlIdentifier($c, $dot_is_valid = false)
{
    return (ctype_alnum($c)
        || ($ord_c = ord($c)) && $ord_c >= 192 && $ord_c != 215 && $ord_c != 249
        || $c == '_'
        || $c == '$'
        || ($dot_is_valid != false && $c == '.'));
}	
function PMA_formatByteDown($value, $limes = 6, $comma = 0)
{
    /* l10n: shortcuts for Byte, Kilo, Mega, Giga, Tera, Peta, Exa+ */
    $byteUnits = array(JText::_('B'), JText::_('KiB'), JText::_('MiB'), JText::_('GiB'), JText::_('TiB'), JText::_('PiB'), JText::_('EiB'));

    $dh           = PMA_pow(10, $comma);
    $li           = PMA_pow(10, $limes);
    $return_value = $value;
    $unit         = $byteUnits[0];

    for ($d = 6, $ex = 15; $d >= 1; $d--, $ex-=3) {
        if (isset($byteUnits[$d]) && $value >= $li * PMA_pow(10, $ex)) {
            // use 1024.0 to avoid integer overflow on 64-bit machines
            $value = round($value / (PMA_pow(1024, $d) / $dh)) /$dh;
            $unit = $byteUnits[$d];
            break 1;
        } // end if
    } // end for

    if ($unit != $byteUnits[0]) {
        // if the unit is not bytes (as represented in current language)
        // reformat with max length of 5
        // 4th parameter=true means do not reformat if value < 1
        $return_value = PMA_formatNumber($value, 5, $comma, true);
    } else {
        // do not reformat, just handle the locale
        $return_value = PMA_formatNumber($value, 0);
    }

    return array(trim($return_value), $unit);
} // end of the 'PMA_formatByteDown' function

function PMA_localizeNumber($value)
{
    return str_replace(
        array(',', '.'),
        array(
            /* l10n: Thousands separator */
            JText::_(','),
            /* l10n: Decimal separator */
            JText::_('.'),
            ),
        $value);
}


function PMA_formatNumber($value, $length = 3, $comma = 0, $only_down = false)
{
    //number_format is not multibyte safe, str_replace is safe
    if ($length === 0) {
        return PMA_localizeNumber(number_format($value, $comma));
    }

    // this units needs no translation, ISO
    $units = array(
        -8 => 'y',
        -7 => 'z',
        -6 => 'a',
        -5 => 'f',
        -4 => 'p',
        -3 => 'n',
        -2 => '&micro;',
        -1 => 'm',
        0 => ' ',
        1 => 'k',
        2 => 'M',
        3 => 'G',
        4 => 'T',
        5 => 'P',
        6 => 'E',
        7 => 'Z',
        8 => 'Y'
    );

    // we need at least 3 digits to be displayed
    if (3 > $length + $comma) {
        $length = 3 - $comma;
    }

    // check for negative value to retain sign
    if ($value < 0) {
        $sign = '-';
        $value = abs($value);
    } else {
        $sign = '';
    }

    $dh = PMA_pow(10, $comma);
    $li = PMA_pow(10, $length);
    $unit = $units[0];

    if ($value >= 1) {
        for ($d = 8; $d >= 0; $d--) {
            if (isset($units[$d]) && $value >= $li * PMA_pow(1000, $d-1)) {
                $value = round($value / (PMA_pow(1000, $d) / $dh)) /$dh;
                $unit = $units[$d];
                break 1;
            } // end if
        } // end for
    } elseif (!$only_down && (float) $value !== 0.0) {
        for ($d = -8; $d <= 8; $d++) {
            // force using pow() because of the negative exponent
            if (isset($units[$d]) && $value <= $li * PMA_pow(1000, $d-1, 'pow')) {
                $value = round($value / (PMA_pow(1000, $d, 'pow') / $dh)) /$dh;
                $unit = $units[$d];
                break 1;
            } // end if
        } // end for
    } // end if ($value >= 1) elseif (!$only_down && (float) $value !== 0.0)

    //number_format is not multibyte safe, str_replace is safe
    $value = PMA_localizeNumber(number_format($value, $comma));

    return $sign . $value . ' ' . $unit;
} // end of the 'PMA_formatNumber' function


function PMA_extractValueFromFormattedSize($formatted_size)
{
    $return_value = -1;

    if (preg_match('/^[0-9]+GB$/', $formatted_size)) {
        $return_value = substr($formatted_size, 0, -2) * PMA_pow(1024, 3);
    } elseif (preg_match('/^[0-9]+MB$/', $formatted_size)) {
        $return_value = substr($formatted_size, 0, -2) * PMA_pow(1024, 2);
    } elseif (preg_match('/^[0-9]+K$/', $formatted_size)) {
        $return_value = substr($formatted_size, 0, -1) * PMA_pow(1024, 1);
    }
    return $return_value;
}// end of the 'PMA_extractValueFromFormattedSize' function


function PMA_localisedDate($timestamp = -1, $format = '')
{ 
    $month = array(
/* l10n: Short month name */
        JText::_('Jan'),
/* l10n: Short month name */
         JText::_('Feb'),
/* l10n: Short month name */
         JText::_('Mar'),
/* l10n: Short month name */
         JText::_('Apr'),
/* l10n: Short month name */
        JText::_('Short month name', 'May'),
/* l10n: Short month name */
         JText::_('Jun'),
/* l10n: Short month name */
         JText::_('Jul'),
/* l10n: Short month name */
         JText::_('Aug'),
/* l10n: Short month name */
         JText::_('Sep'),
/* l10n: Short month name */
         JText::_('Oct'),
/* l10n: Short month name */
         JText::_('Nov'),
/* l10n: Short month name */
         JText::_('Dec'));
    $day_of_week = array(
/* l10n: Short week day name */
         JText::_('Sun'),
/* l10n: Short week day name */
         JText::_('Mon'),
/* l10n: Short week day name */
         JText::_('Tue'),
/* l10n: Short week day name */
         JText::_('Wed'),
/* l10n: Short week day name */
         JText::_('Thu'),
/* l10n: Short week day name */
         JText::_('Fri'),
/* l10n: Short week day name */
         JText::_('Sat'));

    if ($format == '') {
        /* l10n: See http://www.php.net/manual/en/function.strftime.php to define the format string */
        $format =  JText::_('%B %d, %Y at %I:%M %p');
    }

    if ($timestamp == -1) {
        $timestamp = time();
    }

    $date = preg_replace('@%[aA]@', $day_of_week[(int)strftime('%w', $timestamp)], $format);
    $date = preg_replace('@%[bB]@', $month[(int)strftime('%m', $timestamp)-1], $date);

    return strftime($date, $timestamp);
} // end of the 'PMA_localisedDate()' function


function PMA_generate_html_tab($tab, $url_params = array())
{
    // default values
    $defaults = array(
        'text'      => '',
        'class'     => '',
        'active'    => null,
        'link'      => '',
        'sep'       => '?',
        'attr'      => '',
        'args'      => '',
        'warning'   => '',
        'fragment'  => '',
        'id'        => '',
    );

    $tab = array_merge($defaults, $tab);

    // determine additionnal style-class
    if (empty($tab['class'])) {
        if (! empty($tab['active'])
         || PMA_isValid($GLOBALS['active_page'], 'identical', $tab['link'])) {
            $tab['class'] = 'active';
        } elseif (is_null($tab['active']) && empty($GLOBALS['active_page'])
          && basename($GLOBALS['PMA_PHP_SELF']) == $tab['link']
          && empty($tab['warning'])) {
            $tab['class'] = 'active';
        }
    }

    if (!empty($tab['warning'])) {
        $tab['class'] .= ' error';
        $tab['attr'] .= ' title="' . htmlspecialchars($tab['warning']) . '"';
    }

	// If there are any tab specific URL parameters, merge those with the general URL parameters
	if(! empty($tab['url_params']) && is_array($tab['url_params'])) {
		$url_params = array_merge($url_params, $tab['url_params']);
	}

    // build the link
    if (!empty($tab['link'])) {
        $tab['link'] = htmlentities($tab['link']);
        $tab['link'] = $tab['link'] . PMA_generate_common_url($url_params);
        if (! empty($tab['args'])) {
            foreach ($tab['args'] as $param => $value) {
                $tab['link'] .= PMA_get_arg_separator('html') . urlencode($param) . '='
                    . urlencode($value);
            }
        }
    }

    if (! empty($tab['fragment'])) {
        $tab['link'] .= $tab['fragment'];
    }

    // display icon, even if iconic is disabled but the link-text is missing
    if (($GLOBALS['cfg']['MainPageIconic'] || empty($tab['text']))
        && isset($tab['icon'])) {
        // avoid generating an alt tag, because it only illustrates
        // the text that follows and if browser does not display
        // images, the text is duplicated
        $image = '<img class="icon" src="' . htmlentities($GLOBALS['pmaThemeImage'])
            .'%1$s" width="16" height="16" alt="" />%2$s';
        $tab['text'] = sprintf($image, htmlentities($tab['icon']), $tab['text']);
    }
    // check to not display an empty link-text
    elseif (empty($tab['text'])) {
        $tab['text'] = '?';
        trigger_error('empty linktext in function ' . __FUNCTION__ . '()',
            E_USER_NOTICE);
    }

    //Set the id for the tab, if set in the params
    $id_string = ( empty($tab['id']) ? '' : ' id="'.$tab['id'].'" ' );
    $out = '<li' . ($tab['class'] == 'active' ? ' class="active"' : '') . '>';

    if (!empty($tab['link'])) {
        $out .= '<a class="tab' . htmlentities($tab['class']) . '"'
            .$id_string
            .' href="' . $tab['link'] . '" ' . $tab['attr'] . '>'
            . $tab['text'] . '</a>';
    } else {
        $out .= '<span class="tab' . htmlentities($tab['class']) . '"'.$id_string.'>'
            . $tab['text'] . '</span>';
    }

    $out .= '</li>';
    return $out;
} // end of the 'PMA_generate_html_tab()' function

function PMA_generate_html_tabs($tabs, $url_params)
{
    $tag_id = 'topmenu';
    $tab_navigation =
         '<div id="' . htmlentities($tag_id) . 'container">' . "\n"
        .'<ul id="' . htmlentities($tag_id) . '">' . "\n";

    foreach ($tabs as $tab) {
        $tab_navigation .= PMA_generate_html_tab($tab, $url_params);
    }

    $tab_navigation .=
         '</ul>' . "\n"
        .'<div class="clearfloat"></div>'
        .'</div>' . "\n";

    return $tab_navigation;
}

function PMA_linkOrButton($url, $message, $tag_params = array(),
    $new_form = true, $strip_img = false, $target = '')
{
    $url_length = strlen($url);
    // with this we should be able to catch case of image upload
    // into a (MEDIUM) BLOB; not worth generating even a form for these
    if ($url_length > $GLOBALS['cfg']['LinkLengthLimit'] * 100) {
        return '';
    }

    if (! is_array($tag_params)) {
        $tmp = $tag_params;
        $tag_params = array();
        if (!empty($tmp)) {
            $tag_params['onclick'] = 'return confirmLink(this, \'' . PMA_escapeJsString($tmp) . '\')';
        }
        unset($tmp);
    }
    if (! empty($target)) {
        $tag_params['target'] = htmlentities($target);
    }

    $tag_params_strings = array();
    foreach ($tag_params as $par_name => $par_value) {
        // htmlspecialchars() only on non javascript
        $par_value = substr($par_name, 0, 2) == 'on'
            ? $par_value
            : htmlspecialchars($par_value);
        $tag_params_strings[] = $par_name . '="' . $par_value . '"';
    }

    if ($url_length <= $GLOBALS['cfg']['LinkLengthLimit']) {
        // no whitespace within an <a> else Safari will make it part of the link
        $ret = "\n" . '<a href="' . $url . '" '
            . implode(' ', $tag_params_strings) . '>'
            . $message . '</a>' . "\n";
    } else {
        // no spaces (linebreaks) at all
        // or after the hidden fields
        // IE will display them all

        // add class=link to submit button
        if (empty($tag_params['class'])) {
            $tag_params['class'] = 'link';
        }

        // decode encoded url separators
        $separator   = PMA_get_arg_separator();
        // on most places separator is still hard coded ...
        if ($separator !== '&') {
            // ... so always replace & with $separator
            $url         = str_replace(htmlentities('&'), $separator, $url);
            $url         = str_replace('&', $separator, $url);
        }
        $url         = str_replace(htmlentities($separator), $separator, $url);
        // end decode

        $url_parts   = parse_url($url);
        $query_parts = explode($separator, $url_parts['query']);
        if ($new_form) {
            $ret = '<form action="' . $url_parts['path'] . '" class="link"'
                 . ' method="post"' . $target . ' style="display: inline;">';
            $subname_open   = '';
            $subname_close  = '';
            $submit_name    = '';
        } else {
            $query_parts[] = 'redirect=' . $url_parts['path'];
            if (empty($GLOBALS['subform_counter'])) {
                $GLOBALS['subform_counter'] = 0;
            }
            $GLOBALS['subform_counter']++;
            $ret            = '';
            $subname_open   = 'subform[' . $GLOBALS['subform_counter'] . '][';
            $subname_close  = ']';
            $submit_name    = ' name="usesubform[' . $GLOBALS['subform_counter'] . ']"';
        }
        foreach ($query_parts as $query_pair) {
            list($eachvar, $eachval) = explode('=', $query_pair);
            $ret .= '<input type="hidden" name="' . $subname_open . $eachvar
                . $subname_close . '" value="'
                . htmlspecialchars(urldecode($eachval)) . '" />';
        } // end while

        if (stristr($message, '<img')) {
            if ($strip_img) {
                $message = trim(strip_tags($message));
                $ret .= '<input type="submit"' . $submit_name . ' '
                    . implode(' ', $tag_params_strings)
                    . ' value="' . htmlspecialchars($message) . '" />';
            } else {
                $displayed_message = htmlspecialchars(
                        preg_replace('/^.*\salt="([^"]*)".*$/si', '\1',
                            $message));
                $ret .= '<input type="image"' . $submit_name . ' '
                    . implode(' ', $tag_params_strings)
                    . ' src="' . preg_replace(
                        '/^.*\ssrc="([^"]*)".*$/si', '\1', $message) . '"'
                        . ' value="' . $displayed_message . '" title="' . $displayed_message . '" />';
                // Here we cannot obey PropertiesIconic completely as a
                // generated link would have a length over LinkLengthLimit
                // but we can at least show the message.
                // If PropertiesIconic is false or 'both'
                if ($GLOBALS['cfg']['PropertiesIconic'] !== true) {
                    $ret .= ' <span class="clickprevimage">' . $displayed_message . '</span>';
                }
            }
        } else {
            $message = trim(strip_tags($message));
            $ret .= '<input type="submit"' . $submit_name . ' '
                . implode(' ', $tag_params_strings)
                . ' value="' . htmlspecialchars($message) . '" />';
        }
        if ($new_form) {
            $ret .= '</form>';
        }
    } // end if... else...

        return $ret;
} // end of the 'PMA_linkOrButton()' function


function PMA_timespanFormat($seconds)
{
    $return_string = '';
    $days = floor($seconds / 86400);
    if ($days > 0) {
        $seconds -= $days * 86400;
    }
    $hours = floor($seconds / 3600);
    if ($days > 0 || $hours > 0) {
        $seconds -= $hours * 3600;
    }
    $minutes = floor($seconds / 60);
    if ($days > 0 || $hours > 0 || $minutes > 0) {
        $seconds -= $minutes * 60;
    }
    return sprintf(JText::_('%s days, %s hours, %s minutes and %s seconds'), (string)$days, (string)$hours, (string)$minutes, (string)$seconds);
}


function PMA_flipstring($string, $Separator = "<br />\n")
{
    $format_string = '';
    $charbuff = false;

    for ($i = 0, $str_len = strlen($string); $i < $str_len; $i++) {
        $char = $string{$i};
        $append = false;

        if ($char == '&') {
            $format_string .= $charbuff;
            $charbuff = $char;
        } elseif ($char == ';' && !empty($charbuff)) {
            $format_string .= $charbuff . $char;
            $charbuff = false;
            $append = true;
        } elseif (! empty($charbuff)) {
            $charbuff .= $char;
        } else {
            $format_string .= $char;
            $append = true;
        }

        // do not add separator after the last character
        if ($append && ($i != $str_len - 1)) {
            $format_string .= $Separator;
        }
    }

    return $format_string;
}

function PMA_checkParameters($params, $die = true, $request = true)
{
    global $checked_special;

    if (!isset($checked_special)) {
        $checked_special = false;
    }

    $reported_script_name = basename($GLOBALS['PMA_PHP_SELF']);
    $found_error = false;
    $error_message = '';

    foreach ($params as $param) {
        if ($request && $param != 'db' && $param != 'table') {
            $checked_special = true;
        }

        if (!isset($GLOBALS[$param])) {
            $error_message .= $reported_script_name
                . ': Missing parameter: ' . $param
                . PMA_showDocu('faqmissingparameters')
                . '<br />';
            $found_error = true;
        }
    }
    if ($found_error) {
       
        require_once './libraries/header_meta_style.inc.php';
        echo '</head><body><p>' . $error_message . '</p></body></html>';
        if ($die) {
            exit();
        }
    }
} // end function


function PMA_getUniqueCondition($handle, $fields_cnt, $fields_meta, $row, $force_unique=false)
{
    $primary_key          = '';
    $unique_key           = '';
    $nonprimary_condition = '';
    $preferred_condition = '';

    for ($i = 0; $i < $fields_cnt; ++$i) {
        $condition   = '';
        $field_flags = PMA_DBI_field_flags($handle, $i);
        $meta        = $fields_meta[$i];

        // do not use a column alias in a condition
        if (! isset($meta->orgname) || ! strlen($meta->orgname)) {
            $meta->orgname = $meta->name;

            if (isset($GLOBALS['analyzed_sql'][0]['select_expr'])
              && is_array($GLOBALS['analyzed_sql'][0]['select_expr'])) {
                foreach ($GLOBALS['analyzed_sql'][0]['select_expr']
                  as $select_expr) {
                    // need (string) === (string)
                    // '' !== 0 but '' == 0
                    if ((string) $select_expr['alias'] === (string) $meta->name) {
                        $meta->orgname = $select_expr['column'];
                        break;
                    } // end if
                } // end foreach
            }
        }

        // Do not use a table alias in a condition.
        // Test case is:
        // select * from galerie x WHERE
        //(select count(*) from galerie y where y.datum=x.datum)>1
        //
        // But orgtable is present only with mysqli extension so the
        // fix is only for mysqli.
        // Also, do not use the original table name if we are dealing with
        // a view because this view might be updatable.
        // (The isView() verification should not be costly in most cases
        // because there is some caching in the function).
        if (isset($meta->orgtable) && $meta->table != $meta->orgtable && ! PMA_Table::isView($GLOBALS['db'], $meta->table)) {
            $meta->table = $meta->orgtable;
        }

        // to fix the bug where float fields (primary or not)
        // can't be matched because of the imprecision of
        // floating comparison, use CONCAT
        // (also, the syntax "CONCAT(field) IS NULL"
        // that we need on the next "if" will work)
        if ($meta->type == 'real') {
            $condition = ' CONCAT(' . PMA_backquote($meta->table) . '.'
                . PMA_backquote($meta->orgname) . ') ';
        } else {
            $condition = ' ' . PMA_backquote($meta->table) . '.'
                . PMA_backquote($meta->orgname) . ' ';
        } // end if... else...

        if (!isset($row[$i]) || is_null($row[$i])) {
            $condition .= 'IS NULL AND';
        } else {
            // timestamp is numeric on some MySQL 4.1
            // for real we use CONCAT above and it should compare to string
            if ($meta->numeric && $meta->type != 'timestamp' && $meta->type != 'real') {
                $condition .= '= ' . $row[$i] . ' AND';
            } elseif (($meta->type == 'blob' || $meta->type == 'string')
                // hexify only if this is a true not empty BLOB or a BINARY
                 && stristr($field_flags, 'BINARY')
                 && !empty($row[$i])) {
                    // do not waste memory building a too big condition
                    if (strlen($row[$i]) < 1000) {
                        // use a CAST if possible, to avoid problems
                        // if the field contains wildcard characters % or _
                        $condition .= '= CAST(0x' . bin2hex($row[$i])
                            . ' AS BINARY) AND';
                    } else {
                        // this blob won't be part of the final condition
                        $condition = '';
                    }
            } elseif ($meta->type == 'bit') {
                $condition .= "= b'" . PMA_printable_bit_value($row[$i], $meta->length) . "' AND";
            } else {
                $condition .= '= \''
                    . PMA_sqlAddslashes($row[$i], false, true) . '\' AND';
            }
        }
        if ($meta->primary_key > 0) {
            $primary_key .= $condition;
        } elseif ($meta->unique_key > 0) {
            $unique_key  .= $condition;
        }
        $nonprimary_condition .= $condition;
    } // end for

    // Correction University of Virginia 19991216:
    // prefer primary or unique keys for condition,
    // but use conjunction of all values if no primary key
    $clause_is_unique = true;
    if ($primary_key) {
        $preferred_condition = $primary_key;
    } elseif ($unique_key) {
        $preferred_condition = $unique_key;
    } elseif (! $force_unique) {
        $preferred_condition = $nonprimary_condition;
        $clause_is_unique = false;
    }

    $where_clause = trim(preg_replace('|\s?AND$|', '', $preferred_condition));
    return(array($where_clause, $clause_is_unique));
} // end function


function PMA_buttonOrImage($button_name, $button_class, $image_name, $text,
    $image, $value = '')
{
    if ($value == '') {
        $value = $text;
    }
    if (false === $GLOBALS['cfg']['PropertiesIconic']) {
        echo ' <input type="submit" name="' . $button_name . '"'
                .' value="' . htmlspecialchars($value) . '"'
                .' title="' . htmlspecialchars($text) . '" />' . "\n";
        return;
    }

    /* Opera has trouble with <input type="image"> */
    /* IE has trouble with <button> */
    if (PMA_USR_BROWSER_AGENT != 'IE') {
        echo '<button class="' . $button_class . '" type="submit"'
            .' name="' . $button_name . '" value="' . htmlspecialchars($value) . '"'
            .' title="' . htmlspecialchars($text) . '">' . "\n"
            . PMA_getIcon($image, $text)
            .'</button>' . "\n";
    } else {
        echo '<input type="image" name="' . $image_name . '" value="'
            . htmlspecialchars($value) . '" title="' . htmlspecialchars($text) . '" src="' . $GLOBALS['pmaThemeImage']
            . $image . '" />'
            . ($GLOBALS['cfg']['PropertiesIconic'] === 'both' ? '&nbsp;' . htmlspecialchars($text) : '') . "\n";
    }
} // end function

function PMA_pageselector($rows, $pageNow = 1, $nbTotalPage = 1,
    $showAll = 200, $sliceStart = 5, $sliceEnd = 5, $percent = 20,
    $range = 10, $prompt = '')
{
    $increment = floor($nbTotalPage / $percent);
    $pageNowMinusRange = ($pageNow - $range);
    $pageNowPlusRange = ($pageNow + $range);

    $gotopage = $prompt . ' <select id="pageselector" ';
    if ($GLOBALS['cfg']['AjaxEnable']) {
        $gotopage .= ' class="ajax"';
    }
    $gotopage .= ' name="pos" >' . "\n";
    if ($nbTotalPage < $showAll) {
        $pages = range(1, $nbTotalPage);
    } else {
        $pages = array();

        // Always show first X pages
        for ($i = 1; $i <= $sliceStart; $i++) {
            $pages[] = $i;
        }

        // Always show last X pages
        for ($i = $nbTotalPage - $sliceEnd; $i <= $nbTotalPage; $i++) {
            $pages[] = $i;
        }

        // Based on the number of results we add the specified
        // $percent percentage to each page number,
        // so that we have a representing page number every now and then to
        // immediately jump to specific pages.
        // As soon as we get near our currently chosen page ($pageNow -
        // $range), every page number will be shown.
        $i = $sliceStart;
        $x = $nbTotalPage - $sliceEnd;
        $met_boundary = false;
        while ($i <= $x) {
            if ($i >= $pageNowMinusRange && $i <= $pageNowPlusRange) {
                // If our pageselector comes near the current page, we use 1
                // counter increments
                $i++;
                $met_boundary = true;
            } else {
                // We add the percentage increment to our current page to
                // hop to the next one in range
                $i += $increment;

                // Make sure that we do not cross our boundaries.
                if ($i > $pageNowMinusRange && ! $met_boundary) {
                    $i = $pageNowMinusRange;
                }
            }

            if ($i > 0 && $i <= $x) {
                $pages[] = $i;
            }
        }

        // Since because of ellipsing of the current page some numbers may be double,
        // we unify our array:
        sort($pages);
        $pages = array_unique($pages);
    }

    foreach ($pages as $i) {
        if ($i == $pageNow) {
            $selected = 'selected="selected" style="font-weight: bold"';
        } else {
            $selected = '';
        }
        $gotopage .= '                <option ' . $selected . ' value="' . (($i - 1) * $rows) . '">' . $i . '</option>' . "\n";
    }

    $gotopage .= ' </select><noscript><input type="submit" value="' . JText::_('Go') . '" /></noscript>';

    return $gotopage;
} // end function


function PMA_listNavigator($count, $pos, $_url_params, $script, $frame, $max_count) {

    if ($max_count < $count) {
        echo 'frame_navigation' == $frame ? '<div id="navidbpageselector">' . "\n" : '';
        echo JText::_('Page number:');
        echo 'frame_navigation' == $frame ? '<br />' : ' ';

        // Move to the beginning or to the previous page
        if ($pos > 0) {
            // patch #474210 - part 1
            if ($GLOBALS['cfg']['NavigationBarIconic']) {
                $caption1 = '&lt;&lt;';
                $caption2 = ' &lt; ';
                $title1   = ' title="' . JText::_('Begin') . '"';
                $title2   = ' title="' . JText::_('Previous') . '"';
            } else {
                $caption1 = JText::_('Begin') . ' &lt;&lt;';
                $caption2 = JText::_('Previous') . ' &lt;';
                $title1   = '';
                $title2   = '';
            } // end if... else...
            $_url_params['pos'] = 0;
            echo '<a' . $title1 . ' href="' . $script
                . PMA_generate_common_url($_url_params) . '" target="' . $frame . '">'
                . $caption1 . '</a>';
            $_url_params['pos'] = $pos - $max_count;
            echo '<a' . $title2 . ' href="' . $script
                . PMA_generate_common_url($_url_params) . '" target="' . $frame . '">'
                . $caption2 . '</a>';
        }

        echo "\n", '<form action="./', basename($script), '" method="post" target="', $frame, '">', "\n";
        echo PMA_generate_common_hidden_inputs($_url_params);
        echo PMA_pageselector(
                $max_count,
                floor(($pos + 1) / $max_count) + 1,
                ceil($count / $max_count));
        echo '</form>';

        if ($pos + $max_count < $count) {
            if ($GLOBALS['cfg']['NavigationBarIconic']) {
                $caption3 = ' &gt; ';
                $caption4 = '&gt;&gt;';
                $title3   = ' title="' . JText::_('Next') . '"';
                $title4   = ' title="' . JText::_('End') . '"';
            } else {
                $caption3 = '&gt; ' . JText::_('Next');
                $caption4 = '&gt;&gt; ' . JText::_('End');
                $title3   = '';
                $title4   = '';
            } // end if... else...
            $_url_params['pos'] = $pos + $max_count;
            echo '<a' . $title3 . ' href="' . $script
                . PMA_generate_common_url($_url_params) . '" target="' . $frame . '">'
                . $caption3 . '</a>';
            $_url_params['pos'] = floor($count / $max_count) * $max_count;
            if ($_url_params['pos'] == $count) {
                $_url_params['pos'] = $count - $max_count;
            }
            echo '<a' . $title4 . ' href="' . $script
                . PMA_generate_common_url($_url_params) . '" target="' . $frame . '">'
                . $caption4 . '</a>';
        }
        echo "\n";
        if ('frame_navigation' == $frame) {
            echo '</div>' . "\n";
        }
    }
}

function PMA_userDir($dir)
{
    // add trailing slash
    if (substr($dir, -1) != '/') {
        $dir .= '/';
    }

    return str_replace('%u', $GLOBALS['cfg']['Server']['user'], $dir);
}


function PMA_getDbLink($database = null)
{
    if (!strlen($database)) {
        if (!strlen($GLOBALS['db'])) {
            return '';
        }
        $database = $GLOBALS['db'];
    } else {
        $database = PMA_unescape_mysql_wildcards($database);
    }

    return '<a href="' . $GLOBALS['cfg']['DefaultTabDatabase'] . '?' . PMA_generate_common_url($database) . '"'
        .' title="' . sprintf(JText::_('Jump to database &quot;%s&quot;.'), htmlspecialchars($database)) . '">'
        .htmlspecialchars($database) . '</a>';
}

function PMA_externalBug($functionality, $component, $minimum_version, $bugref)
{
    if ($component == 'mysql' && PMA_MYSQL_INT_VERSION < $minimum_version) {
        echo PMA_showHint(sprintf(JText::_('The %s functionality is affected by a known bug, see %s'), $functionality, PMA_linkURL('http://bugs.mysql.com/') . $bugref));
    }
}

function PMA_display_html_checkbox($html_field_name, $label, $checked, $onclick) {

    echo '<input type="checkbox" name="' . $html_field_name . '" id="' . $html_field_name . '"' . ($checked ? ' checked="checked"' : '') . ($onclick ? ' onclick="this.form.submit();"' : '') . ' /><label for="' . $html_field_name . '">' . $label . '</label>';
}


function PMA_display_html_radio($html_field_name, $choices, $checked_choice = '', $line_break = true, $escape_label = true, $class='') {
    foreach ($choices as $choice_value => $choice_label) {
        if (! empty($class)) {
            echo '<div class="' . $class . '">';
        }
        $html_field_id = $html_field_name . '_' . $choice_value;
        echo '<input type="radio" name="' . $html_field_name . '" id="' . $html_field_id . '" value="' . htmlspecialchars($choice_value) . '"';
        if ($choice_value == $checked_choice) {
            echo ' checked="checked"';
        }
        echo ' />' . "\n";
        echo '<label for="' . $html_field_id . '">' . ($escape_label ? htmlspecialchars($choice_label)  : $choice_label) . '</label>';
        if ($line_break) {
            echo '<br />';
        }
        if (! empty($class)) {
            echo '</div>';
        }
        echo "\n";
    }
}

function PMA_generate_html_dropdown($select_name, $choices, $active_choice, $id)
{
    $result = '<select name="' . htmlspecialchars($select_name) . '" id="' . htmlspecialchars($id) . '">';
    foreach ($choices as $one_choice_value => $one_choice_label) {
        $result .= '<option value="' . htmlspecialchars($one_choice_value) . '"';
        if ($one_choice_value == $active_choice) {
            $result .= ' selected="selected"';
        }
        $result .= '>' . htmlspecialchars($one_choice_label) . '</option>';
    }
    $result .= '</select>';
    return $result;
}


function PMA_generate_slider_effect($id, $message)
{
    if ($GLOBALS['cfg']['InitialSlidersState'] == 'disabled') {
        echo '<div id="' . $id . '">';
        return;
    }
   
    ?>
<div id="<?php echo $id; ?>" <?php echo $GLOBALS['cfg']['InitialSlidersState'] == 'closed' ? ' style="display: none; overflow:auto;"' : ''; ?> class="pma_auto_slider" title="<?php echo htmlspecialchars($message); ?>">
    <?php
}

function PMA_printable_bit_value($value, $length) {
    $printable = '';
    for ($i = 0, $len_ceiled = ceil($length / 8); $i < $len_ceiled; $i++) {
        $printable .= sprintf('%08d', decbin(ord(substr($value, $i, 1))));
    }
    $printable = substr($printable, -$length);
    return $printable;
}

function PMA_contains_nonprintable_ascii($value) {
    return preg_match('@[^[:print:]]@', $value);
}


function PMA_convert_bit_default_value($bit_default_value) {
    return strtr($bit_default_value, array("b" => "", "'" => ""));
}

function PMA_extractFieldSpec($fieldspec) {
    $first_bracket_pos = strpos($fieldspec, '(');
    if ($first_bracket_pos) {
        $spec_in_brackets = chop(substr($fieldspec, $first_bracket_pos + 1, (strrpos($fieldspec, ')') - $first_bracket_pos - 1)));
        // convert to lowercase just to be sure
        $type = strtolower(chop(substr($fieldspec, 0, $first_bracket_pos)));
    } else {
        $type = $fieldspec;
        $spec_in_brackets = '';
    }

    if ('enum' == $type || 'set' == $type) {
        // Define our working vars
        $enum_set_values = array();
        $working = "";
        $in_string = false;
        $index = 0;

        // While there is another character to process
        while (isset($fieldspec[$index])) {
            // Grab the char to look at
            $char = $fieldspec[$index];

            // If it is a single quote, needs to be handled specially
            if ($char == "'") {
                // If we are not currently in a string, begin one
                if (! $in_string) {
                    $in_string = true;
                    $working = "";
                // Otherwise, it may be either an end of a string, or a 'double quote' which can be handled as-is
                } else {
                // Check out the next character (if possible)
                    $has_next = isset($fieldspec[$index + 1]);
                    $next = $has_next ? $fieldspec[$index + 1] : null;

                // If we have reached the end of our 'working' string (because there are no more chars, or the next char is not another quote)
                    if (! $has_next || $next != "'") {
                        $enum_set_values[] = $working;
                        $in_string = false;

                    // Otherwise, this is a 'double quote', and can be added to the working string
                    } elseif ($next == "'") {
                        $working .= "'";
                        // Skip the next char; we already know what it is
                        $index++;
                    }
                }
            // escaping of a quote?
            } elseif ('\\' == $char && isset($fieldspec[$index + 1]) && "'" == $fieldspec[$index + 1]) {
                $working .= "'";
                $index++;
            // Otherwise, add it to our working string like normal
            } else {
                $working .= $char;
            }
            // Increment character index
            $index++;
        } // end while
    } else {
        $enum_set_values = array();
    }

    return array(
        'type' => $type,
        'spec_in_brackets' => $spec_in_brackets,
        'enum_set_values'  => $enum_set_values
    );
}


function PMA_foreignkey_supported($engine) {
    $engine = strtoupper($engine);
    if ('INNODB' == $engine || 'PBXT' == $engine) {
        return true;
    } else {
        return false;
    }
}


function PMA_replace_binary_contents($content) {
    $result = str_replace("\x00", '\0', $content);
    $result = str_replace("\x08", '\b', $result);
    $result = str_replace("\x0a", '\n', $result);
    $result = str_replace("\x0d", '\r', $result);
    $result = str_replace("\x1a", '\Z', $result);
    return $result;
}



function PMA_duplicateFirstNewline($string){
    $first_occurence = strpos($string, "\r\n");
    if ($first_occurence === 0){
        $string = "\n".$string;
    }
    return $string;
}

function PMA_getTitleForTarget($target) {

$mapping = array(
	// Values for $cfg['DefaultTabTable']
	'tbl_structure.php' =>  JText::_('Structure'),
	'tbl_sql.php' => JText::_('SQL'),
	'tbl_select.php' =>JText::_('Search'),
	'tbl_change.php' =>JText::_('Insert'),
	'sql.php' => JText::_('Browse'),

	// Values for $cfg['DefaultTabDatabase']
	'db_structure.php' => JText::_('Structure'),
	'db_sql.php' => JText::_('SQL'),
	'db_search.php' => JText::_('Search'),
	'db_operations.php' => JText::_('Operations'),
);
    return $mapping[$target];
}


function PMA_expandUserString($string, $escape = NULL, $updates = array()) {
    /* Content */
    $vars['http_host'] = PMA_getenv('HTTP_HOST') ? PMA_getenv('HTTP_HOST') : '';
    $vars['server_name'] = $GLOBALS['cfg']['Server']['host'];
    $vars['server_verbose'] = $GLOBALS['cfg']['Server']['verbose'];
    $vars['server_verbose_or_name'] = !empty($GLOBALS['cfg']['Server']['verbose']) ? $GLOBALS['cfg']['Server']['verbose'] : $GLOBALS['cfg']['Server']['host'];
    $vars['database'] = $GLOBALS['db'];
    $vars['table'] = $GLOBALS['table'];
    $vars['phpmyadmin_version'] = 'phpMyAdmin ' . PMA_VERSION;

   
    foreach($updates as $key => $val) {
        $vars[$key] = $val;
    }

    $replace = array(
        '@HTTP_HOST@' => $vars['http_host'],
        '@SERVER@' => $vars['server_name'],
        '__SERVER__' => $vars['server_name'],
        '@VERBOSE@' => $vars['server_verbose'],
        '@VSERVER@' => $vars['server_verbose_or_name'],
        '@DATABASE@' => $vars['database'],
        '__DB__' => $vars['database'],
        '@TABLE@' => $vars['table'],
        '__TABLE__' => $vars['table'],
        '@PHPMYADMIN@' => $vars['phpmyadmin_version'],
        );

    /* Optional escaping */
    if (!is_null($escape)) {
        foreach($replace as $key => $val) {
            $replace[$key] = $escape($val);
        }
    }

    /* Fetch fields list if required */
    if (strpos($string, '@FIELDS@') !== FALSE) {
        $fields_list = PMA_DBI_fetch_result(
            'SHOW COLUMNS FROM ' . PMA_backquote($GLOBALS['db'])
            . '.' . PMA_backquote($GLOBALS['table']));

        $field_names = array();
        foreach ($fields_list as $field) {
            if (!is_null($escape)) {
                $field_names[] = $escape($field['Field']);
            } else {
                $field_names[] = $field['Field'];
            }
        }

        $replace['@FIELDS@'] = implode(',', $field_names);
    }

  
    return str_replace(array_keys($replace), array_values($replace), strftime($string));
}


function PMA_ajaxResponse($message, $success = true, $extra_data = array())
{
    $response = array();
    if( $success == true ) {
        $response['success'] = true;
        if ($message instanceof PMA_Message) {
            $response['message'] = $message->getDisplay();
        }
        else {
            $response['message'] = $message;
        }
    }
    else {
        $response['success'] = false;
        if($message instanceof PMA_Message) {
            $response['error'] = $message->getDisplay();
        }
        else {
            $response['error'] = $message;
        }
    }

    // If extra_data has been provided, append it to the response array
    if( ! empty($extra_data) && count($extra_data) > 0 ) {
        $response = array_merge($response, $extra_data);
    }

    // Set the Content-Type header to JSON so that jQuery parses the
    // response correctly.
    //
    // At this point, other headers might have been sent;
    // even if $GLOBALS['is_header_sent'] is true,
    // we have to send these additional headers.
    header('Cache-Control: no-cache');
    header("Content-Type: application/json");

    echo json_encode($response);
    exit;
}


function PMA_browseUploadFile($max_upload_size) {
    $uid = uniqid("");
    echo '<label for="radio_import_file">' . JText::_("Browse your computer:") . '</label>';
    echo '<div id="upload_form_status" style="display: none;"></div>';
    echo '<div id="upload_form_status_info" style="display: none;"></div>';
    echo '<input type="file" name="import_file" id="input_import_file" />';
    echo PMA_displayMaximumUploadSize($max_upload_size) . "\n";
    // some browsers should respect this :)
    echo PMA_generateHiddenMaxFileSize($max_upload_size) . "\n";
}


function PMA_selectUploadFile($import_list, $uploaddir) {
	echo '<label for="radio_local_import_file">' . sprintf(JText::_("Select from the web server upload directory <b>%s</b>:"), htmlspecialchars(PMA_userDir($uploaddir))) . '</label>';
	$extensions = '';
    foreach ($import_list as $key => $val) {
        if (!empty($extensions)) {
            $extensions .= '|';
        }
        $extensions .= $val['extension'];
    }
    $matcher = '@\.(' . $extensions . ')(\.(' . PMA_supportedDecompressions() . '))?$@';

    $files = PMA_getFileSelectOptions(PMA_userDir($uploaddir), $matcher, (isset($timeout_passed) && $timeout_passed && isset($local_import_file)) ? $local_import_file : '');
    if ($files === FALSE) {
        PMA_Message::error(JText::_('The directory you set for upload work cannot be reached'))->display();
    } elseif (!empty($files)) {
        echo "\n";
        echo '    <select style="margin: 5px" size="1" name="local_import_file" id="select_local_import_file">' . "\n";
        echo '        <option value="">&nbsp;</option>' . "\n";
        echo $files;
        echo '    </select>' . "\n";
    } elseif (empty ($files)) {
        echo '<i>' . JText::_('There are no files to upload') . '</i>';
    }
}

function PMA_buildActionTitles() {
    $titles = array();

    $titles['Browse']     = PMA_getIcon('b_browse.png', JText::_('Browse'), true);
    $titles['NoBrowse']   = PMA_getIcon('bd_browse.png', JText::_('Browse'), true);
    $titles['Search']     = PMA_getIcon('b_select.png', JText::_('Search'), true);
    $titles['NoSearch']   = PMA_getIcon('bd_select.png', JText::_('Search'), true);
    $titles['Insert']     = PMA_getIcon('b_insrow.png', JText::_('Insert'), true);
    $titles['NoInsert']   = PMA_getIcon('bd_insrow.png', JText::_('Insert'), true);
    $titles['Structure']  = PMA_getIcon('b_props.png', JText::_('Structure'), true);
    $titles['Drop']       = PMA_getIcon('b_drop.png', JText::_('Drop'), true);
    $titles['NoDrop']     = PMA_getIcon('bd_drop.png', JText::_('Drop'), true);
    $titles['Empty']      = PMA_getIcon('b_empty.png', JText::_('Empty'), true);
    $titles['NoEmpty']    = PMA_getIcon('bd_empty.png', JText::_('Empty'), true);
    $titles['Edit']       = PMA_getIcon('b_edit.png', JText::_('Edit'), true);
    return $titles;
}
?>
