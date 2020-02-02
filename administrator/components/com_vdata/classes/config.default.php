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
$cfg['PmaAbsoluteUri'] = '';


$cfg['PmaNoRelation_DisableWarning'] = false;


$cfg['SuhosinDisableWarning'] = false;


$cfg['McryptDisableWarning'] = false;


$cfg['TranslationWarningThreshold'] = 80;


$cfg['AllowThirdPartyFraming'] = false;


$cfg['blowfish_secret'] = '';



$cfg['Servers'] = array();

$i = 1;


$cfg['Servers'][$i]['host'] = 'localhost';


$cfg['Servers'][$i]['port'] = '';


$cfg['Servers'][$i]['socket'] = '';


$cfg['Servers'][$i]['ssl'] = false;


$cfg['Servers'][$i]['connect_type'] = 'tcp';


$cfg['Servers'][$i]['extension'] = 'mysqli';


$cfg['Servers'][$i]['compress'] = false;


$cfg['Servers'][$i]['controluser'] = '';


$cfg['Servers'][$i]['controlpass'] = '';


$cfg['Servers'][$i]['auth_type'] = 'cookie';


$cfg['Servers'][$i]['auth_http_realm'] = '';


$cfg['Servers'][$i]['auth_swekey_config'] = '';


$cfg['Servers'][$i]['user'] = 'root';


$cfg['Servers'][$i]['password'] = '';


$cfg['Servers'][$i]['SignonSession'] = '';


$cfg['Servers'][$i]['SignonURL'] = '';


$cfg['Servers'][$i]['LogoutURL'] = '';


$cfg['Servers'][$i]['nopassword'] = false;


$cfg['Servers'][$i]['only_db'] = '';


$cfg['Servers'][$i]['hide_db'] = '';


$cfg['Servers'][$i]['verbose'] = '';


$cfg['Servers'][$i]['pmadb'] = '';


$cfg['Servers'][$i]['bookmarktable'] = '';


$cfg['Servers'][$i]['relation'] = '';


$cfg['Servers'][$i]['table_info'] = '';


$cfg['Servers'][$i]['table_coords'] = '';


$cfg['Servers'][$i]['pdf_pages'] = '';


$cfg['Servers'][$i]['column_info'] = '';


$cfg['Servers'][$i]['history'] = '';


$cfg['Servers'][$i]['designer_coords'] = '';


$cfg['Servers'][$i]['tracking'] = '';


$cfg['Servers'][$i]['userconfig'] = '';


$cfg['Servers'][$i]['verbose_check'] = true;


$cfg['Servers'][$i]['AllowRoot'] = true;


$cfg['Servers'][$i]['AllowNoPassword'] = false;


$cfg['Servers'][$i]['AllowDeny']['order'] = '';


$cfg['Servers'][$i]['AllowDeny']['rules'] = array();


$cfg['Servers'][$i]['DisableIS'] = true;

$cfg['Servers'][$i]['ShowDatabasesCommand'] = 'SHOW DATABASES';

$cfg['Servers'][$i]['CountTables'] = false;



$cfg['Servers'][$i]['tracking_version_auto_create'] = false;



$cfg['Servers'][$i]['tracking_default_statements'] = 'CREATE TABLE,ALTER TABLE,DROP TABLE,RENAME TABLE,' .
                                          'CREATE INDEX,DROP INDEX,' .
                                          'INSERT,UPDATE,DELETE,TRUNCATE,REPLACE,' .
                                          'CREATE VIEW,ALTER VIEW,DROP VIEW,' .
                                          'CREATE DATABASE,ALTER DATABASE,DROP DATABASE';



$cfg['Servers'][$i]['tracking_add_drop_view'] = true;



$cfg['Servers'][$i]['tracking_add_drop_table'] = true;



$cfg['Servers'][$i]['tracking_add_drop_database'] = true;


$cfg['ServerDefault'] = 1;


$cfg['AjaxEnable'] = true;

$cfg['VersionCheck'] = VERSION_CHECK_DEFAULT;


$cfg['MaxDbList'] = 100;


$cfg['MaxTableList'] = 250;


$cfg['MaxCharactersInDisplayedSQL'] = 1000;


$cfg['OBGzip'] = 'auto';


$cfg['PersistentConnections'] = false;


$cfg['ForceSSL'] = false;


$cfg['ExecTimeLimit'] = 300;


$cfg['SessionSavePath'] = '';


$cfg['MemoryLimit'] = '0';


$cfg['SkipLockedTables'] = false;


$cfg['ShowSQL'] = true;


$cfg['AllowUserDropDatabase'] = false;


$cfg['Confirm'] = true;


$cfg['LoginCookieRecall'] = true;


$cfg['LoginCookieValidity'] = 1440;

/**
 * how long login cookie should be stored (in seconds)
 *
 * @global integer $cfg['LoginCookieStore']
 */
$cfg['LoginCookieStore'] = 0;


$cfg['LoginCookieDeleteAll'] = true;


$cfg['UseDbSearch'] = true;


$cfg['IgnoreMultiSubmitErrors'] = false;


$cfg['VerboseMultiSubmit'] = true;


$cfg['AllowArbitraryServer'] = false;



$cfg['Error_Handler'] = array();


$cfg['Error_Handler']['display'] = false;


$cfg['Error_Handler']['gather'] = false;



$cfg['LeftFrameLight'] = true;


$cfg['LeftFrameDBTree'] = true;


$cfg['LeftFrameDBSeparator'] = '_';


$cfg['LeftFrameTableSeparator']= '__';


$cfg['LeftFrameTableLevel'] = 1;


$cfg['ShowTooltip'] = true;


$cfg['ShowTooltipAliasDB'] = false;


$cfg['ShowTooltipAliasTB'] = false;


$cfg['LeftDisplayLogo'] = true;


$cfg['LeftLogoLink'] = 'main.php';


$cfg['LeftLogoLinkWindow'] = 'main';


$cfg['LeftDisplayTableFilterMinimum'] = 30;


$cfg['LeftDisplayServers'] = false;


$cfg['DisplayServersList'] = false;


$cfg['DisplayDatabasesList'] = 'auto';


$cfg['LeftDefaultTabTable'] = 'tbl_structure.php';

$cfg['ShowStats'] = true;


$cfg['ShowPhpInfo'] = false;

$cfg['ShowServerInfo'] = true;


$cfg['ShowChgPassword'] = true;


$cfg['ShowCreateDb'] = true;


$cfg['SuggestDBName'] = true;

$cfg['NavigationBarIconic'] = true;

$cfg['ShowAll'] = false;


$cfg['MaxRows'] = 30;


$cfg['Order'] = 'SMART';


$cfg['DisplayBinaryAsHex'] = true;



$cfg['ProtectBinary'] = 'blob';


$cfg['ShowFunctionFields'] = true;


$cfg['ShowFieldTypesInDataEditView'] = true;


$cfg['CharEditing'] = 'input';


$cfg['InsertRows'] = 2;


$cfg['ForeignKeyDropdownOrder'] = array('content-id', 'id-content');


$cfg['ForeignKeyMaxLimit'] = 100;


$cfg['ZipDump'] = true;


$cfg['GZipDump'] = true;


$cfg['BZipDump'] = true;


$cfg['CompressOnFly'] = true;



$cfg['LightTabs'] = false;


$cfg['PropertiesIconic'] = 'both';


$cfg['PropertiesNumColumns'] = 1;


$cfg['DefaultTabServer'] = 'main.php';


$cfg['DefaultTabDatabase'] = 'db_structure.php';


$cfg['DefaultTabTable'] = 'sql.php';


$cfg['Export'] = array();


$cfg['Export']['format'] = 'sql';


$cfg['Export']['method'] = 'quick';


$cfg['Export']['compression'] = 'none';


$cfg['Export']['asfile'] = true;


$cfg['Export']['charset'] = '';


$cfg['Export']['onserver'] = false;


$cfg['Export']['onserver_overwrite'] = false;


$cfg['Export']['quick_export_onserver'] = false;


$cfg['Export']['quick_export_onserver_overwrite'] = false;


$cfg['Export']['remember_file_template'] = true;


$cfg['Export']['file_template_table'] = '@TABLE@';


$cfg['Export']['file_template_database'] = '@DATABASE@';


$cfg['Export']['file_template_server'] = '@SERVER@';


$cfg['Export']['codegen_structure_or_data'] = 'data';


$cfg['Export']['codegen_format'] = 0;


$cfg['Export']['ods_columns'] = false;


$cfg['Export']['ods_null'] = 'NULL';


$cfg['Export']['odt_structure_or_data'] = 'structure_and_data';


$cfg['Export']['odt_columns'] = true;


$cfg['Export']['odt_relation'] = true;


$cfg['Export']['odt_comments'] = true;


$cfg['Export']['odt_mime'] = true;


$cfg['Export']['odt_null'] = 'NULL';


$cfg['Export']['htmlword_structure_or_data'] = 'structure_and_data';


$cfg['Export']['htmlword_columns'] = false;


$cfg['Export']['htmlword_null'] = 'NULL';


$cfg['Export']['texytext_structure_or_data'] = 'structure_and_data';


$cfg['Export']['texytext_columns'] = FALSE;


$cfg['Export']['texytext_null'] = 'NULL';


$cfg['Export']['xls_columns'] = false;


$cfg['Export']['xls_structure_or_data'] = 'data';


$cfg['Export']['xls_null'] = 'NULL';


$cfg['Export']['xlsx_columns'] = false;


$cfg['Export']['xlsx_structure_or_data'] = 'data';


$cfg['Export']['xlsx_null'] = 'NULL';


$cfg['Export']['csv_columns'] = false;


$cfg['Export']['csv_structure_or_data'] = 'data';


$cfg['Export']['csv_null'] = 'NULL';


$cfg['Export']['csv_separator'] = ',';


$cfg['Export']['csv_enclosed'] = '"';


$cfg['Export']['csv_escaped'] = '\\';


$cfg['Export']['csv_terminated'] = 'AUTO';


$cfg['Export']['csv_removeCRLF'] = false;


$cfg['Export']['excel_columns'] = false;


$cfg['Export']['excel_null'] = 'NULL';


$cfg['Export']['excel_edition'] = 'win';


$cfg['Export']['excel_removeCRLF'] = false;

$cfg['Export']['excel_structure_or_data'] = 'data';


$cfg['Export']['latex_structure_or_data'] = 'structure_and_data';


$cfg['Export']['latex_columns'] = true;


$cfg['Export']['latex_relation'] = true;


$cfg['Export']['latex_comments'] = true;


$cfg['Export']['latex_mime'] = true;


$cfg['Export']['latex_null'] = '\textit{NULL}';


$cfg['Export']['latex_caption'] = true;


$cfg['Export']['latex_structure_caption'] = 'strLatexStructure';


$cfg['Export']['latex_structure_continued_caption'] = 'strLatexStructure strLatexContinued';


$cfg['Export']['latex_data_caption'] = 'strLatexContent';


$cfg['Export']['latex_data_continued_caption'] = 'strLatexContent strLatexContinued';


$cfg['Export']['latex_data_label'] = 'tab:@TABLE@-data';


$cfg['Export']['latex_structure_label'] = 'tab:@TABLE@-structure';


$cfg['Export']['mediawiki_structure_or_data'] = 'data';


$cfg['Export']['ods_structure_or_data'] = 'data';


$cfg['Export']['pdf_structure_or_data'] = 'data';


$cfg['Export']['php_array_structure_or_data'] = 'data';


$cfg['Export']['json_structure_or_data'] = 'data';


$cfg['Export']['sql_structure_or_data'] = 'structure_and_data';


$cfg['Export']['sql_compatibility'] = 'NONE';


$cfg['Export']['sql_include_comments'] = true;


$cfg['Export']['sql_disable_fk'] = false;


$cfg['Export']['sql_use_transaction'] = false;


$cfg['Export']['sql_drop_database'] = false;


$cfg['Export']['sql_drop_table'] = false;


$cfg['Export']['sql_if_not_exists'] = true;


$cfg['Export']['sql_procedure_function'] = true;


$cfg['Export']['sql_auto_increment'] = true;


$cfg['Export']['sql_backquotes'] = true;


$cfg['Export']['sql_dates'] = false;


$cfg['Export']['sql_relation'] = false;


$cfg['Export']['sql_delayed'] = false;


$cfg['Export']['sql_ignore'] = false;


$cfg['Export']['sql_utc_time'] = true;


$cfg['Export']['sql_hex_for_blob'] = true;


$cfg['Export']['sql_type'] = 'INSERT';


$cfg['Export']['sql_max_query_size'] = 50000;


$cfg['Export']['sql_comments'] = false;


$cfg['Export']['sql_mime'] = false;


$cfg['Export']['sql_header_comment'] = '';


$cfg['Export']['sql_create_table_statements'] = true;


$cfg['Export']['sql_insert_syntax'] = 'both';


$cfg['Export']['pdf_report_title'] = '';


$cfg['Export']['xml_structure_or_data'] = 'data';


$cfg['Export']['xml_export_struc'] = true;


$cfg['Export']['xml_export_functions'] = true;


$cfg['Export']['xml_export_procedures'] = true;


$cfg['Export']['xml_export_tables'] = true;


$cfg['Export']['xml_export_triggers'] = true;


$cfg['Export']['xml_export_views'] = true;


$cfg['Export']['xml_export_contents'] = true;


$cfg['Export']['yaml_structure_or_data'] = 'data';


$cfg['Import'] = array();


$cfg['Import']['format'] = 'sql';


$cfg['Import']['charset'] = '';


$cfg['Import']['allow_interrupt'] = true;


$cfg['Import']['skip_queries'] = 0;


$cfg['Import']['sql_compatibility'] = 'NONE';


$cfg['Import']['sql_no_auto_value_on_zero'] = true;


$cfg['Import']['csv_replace'] = false;


$cfg['Import']['csv_ignore'] = false;


$cfg['Import']['csv_terminated'] = ',';


$cfg['Import']['csv_enclosed'] = '"';


$cfg['Import']['csv_escaped'] = '\\';


$cfg['Import']['csv_new_line'] = 'auto';


$cfg['Import']['csv_columns'] = '';


$cfg['Import']['csv_col_names'] = false;


$cfg['Import']['ldi_replace'] = false;


$cfg['Import']['ldi_ignore'] = false;


$cfg['Import']['ldi_terminated'] = ';';


$cfg['Import']['ldi_enclosed'] = '"';


$cfg['Import']['ldi_escaped'] = '\\';


$cfg['Import']['ldi_new_line'] = 'auto';


$cfg['Import']['ldi_columns'] = '';


$cfg['Import']['ldi_local_option'] = 'auto';


$cfg['Import']['ods_col_names'] = false;


$cfg['Import']['ods_empty_rows'] = true;


$cfg['Import']['ods_recognize_percentages'] = true;


$cfg['Import']['ods_recognize_currency'] = true;


$cfg['Import']['xls_col_names'] = false;


$cfg['Import']['xls_empty_rows'] = true;


$cfg['Import']['xlsx_col_names'] = false;


$cfg['MySQLManualBase'] = 'http://dev.mysql.com/doc/refman';


$cfg['MySQLManualType'] = 'viewable';



$cfg['PDFPageSizes'] = array('A3', 'A4', 'A5', 'letter', 'legal');


$cfg['PDFDefaultPageSize'] = 'A4';



$cfg['DefaultLang'] = 'en';


$cfg['DefaultConnectionCollation'] = 'utf8_general_ci';


$cfg['FilterLanguages'] = '';


$cfg['RecodingEngine'] = 'auto';


$cfg['IconvExtraParams'] = '//TRANSLIT';


$cfg['AvailableCharsets'] = array(
    'iso-8859-1',
    'iso-8859-2',
    'iso-8859-3',
    'iso-8859-4',
    'iso-8859-5',
    'iso-8859-6',
    'iso-8859-7',
    'iso-8859-8',
    'iso-8859-9',
    'iso-8859-10',
    'iso-8859-11',
    'iso-8859-12',
    'iso-8859-13',
    'iso-8859-14',
    'iso-8859-15',
    'windows-1250',
    'windows-1251',
    'windows-1252',
    'windows-1256',
    'windows-1257',
    'koi8-r',
    'big5',
    'gb2312',
    'utf-16',
    'utf-8',
    'utf-7',
    'x-user-defined',
    'euc-jp',
    'ks_c_5601-1987',
    'tis-620',
    'SHIFT_JIS'
);



$cfg['LeftPointerEnable'] = true;


$cfg['BrowsePointerEnable'] = true;


$cfg['BrowseMarkerEnable'] = true;


$cfg['TextareaCols'] = 40;


$cfg['TextareaRows'] = 15;


$cfg['LongtextDoubleTextarea'] = true;


$cfg['TextareaAutoSelect'] = false;


$cfg['CharTextareaCols'] = 40;


$cfg['CharTextareaRows'] = 2;


$cfg['LimitChars'] = 50;


$cfg['ModifyDeleteAtLeft'] = true;


$cfg['ModifyDeleteAtRight'] = false;


$cfg['DefaultDisplay'] = 'horizontal';


$cfg['DefaultPropDisplay'] = 3;


$cfg['HeaderFlipType'] = 'auto';


$cfg['ShowBrowseComments'] = true;


$cfg['ShowPropertyComments']= true;


$cfg['RepeatCells'] = 100;


$cfg['EditInWindow'] = true;


$cfg['QueryWindowWidth'] = 550;


$cfg['QueryWindowHeight'] = 310;


$cfg['QueryHistoryDB'] = false;


$cfg['QueryWindowDefTab'] = 'sql';


$cfg['QueryHistoryMax'] = 25;


$cfg['BrowseMIME'] = true;


$cfg['MaxExactCount'] = 20000;


$cfg['MaxExactCountViews'] = 0;


$cfg['NaturalOrder'] = true;


$cfg['InitialSlidersState'] = 'closed';


$cfg['UserprefsDisallow'] = array();


$cfg['UserprefsDeveloperTab'] = false;




$cfg['TitleTable'] = '@HTTP_HOST@ / @VSERVER@ / @DATABASE@ / @TABLE@ | @PHPMYADMIN@';


$cfg['TitleDatabase'] = '@HTTP_HOST@ / @VSERVER@ / @DATABASE@ | @PHPMYADMIN@';


$cfg['TitleServer'] = '@HTTP_HOST@ / @VSERVER@ | @PHPMYADMIN@';


$cfg['TitleDefault'] = '@HTTP_HOST@ | @PHPMYADMIN@';


$cfg['ErrorIconic'] = true;


$cfg['MainPageIconic'] = true;


$cfg['ReplaceHelpImg'] = true;



$cfg['ThemePath'] = './themes';

$cfg['ThemeManager'] = true;


$cfg['ThemeDefault'] = 'pmahomme';


$cfg['ThemePerServer'] = false;

$cfg['DefaultQueryTable'] = 'SELECT * FROM @TABLE@ WHERE 1';

$cfg['DefaultQueryDatabase'] = '';


$cfg['SQLQuery'] = array();

$cfg['SQLQuery']['Edit'] = true;

$cfg['SQLQuery']['Explain'] = true;

$cfg['SQLQuery']['ShowAsPHP'] = true;

$cfg['SQLQuery']['Validate'] = false;

$cfg['SQLQuery']['Refresh'] = true;

$cfg['UploadDir'] = '';

$cfg['SaveDir'] = '';

$cfg['TempDir'] = '';

$cfg['GD2Available'] = 'auto';

$cfg['TrustedProxies'] = array();

$cfg['CheckConfigurationPermissions'] = true;


$cfg['LinkLengthLimit'] = 1000;

$cfg['SQP'] = array();

$cfg['SQP']['fmtType'] = 'html';

$cfg['SQP']['fmtInd'] = '1';

$cfg['SQP']['fmtIndUnit'] = 'em';


$cfg['SQLValidator'] = array();

$cfg['SQLValidator']['use'] = false;

$cfg['SQLValidator']['username'] = '';

$cfg['SQLValidator']['password'] = '';

$cfg['DBG'] = array();

$cfg['DBG']['sql'] = false;



$cfg['ColumnTypes'] = array(
    // most used
    'INT',
    'VARCHAR',
    'TEXT',
    'DATE',

    // numeric
    'NUMERIC' => array(
        'TINYINT',
        'SMALLINT',
        'MEDIUMINT',
        'INT',
        'BIGINT',
        '-',
        'DECIMAL',
        'FLOAT',
        'DOUBLE',
        'REAL',
        '-',
        'BIT',
        'BOOLEAN',
        'SERIAL',
    ),


    // Date/Time
    'DATE and TIME' => array(
        'DATE',
        'DATETIME',
        'TIMESTAMP',
        'TIME',
        'YEAR',
    ),

    // Text
    'STRING' => array(
        'CHAR',
        'VARCHAR',
        '-',
        'TINYTEXT',
        'TEXT',
        'MEDIUMTEXT',
        'LONGTEXT',
        '-',
        'BINARY',
        'VARBINARY',
        '-',
        'TINYBLOB',
        'MEDIUMBLOB',
        'BLOB',
        'LONGBLOB',
        '-',
        'ENUM',
        'SET',
    ),

    'SPATIAL' => array(
        'GEOMETRY',
        'POINT',
        'LINESTRING',
        'POLYGON',
        'MULTIPOINT',
        'MULTILINESTRING',
        'MULTIPOLYGON',
        'GEOMETRYCOLLECTION',
    ),
);


$cfg['AttributeTypes'] = array(
   '',
   'BINARY',
   'UNSIGNED',
   'UNSIGNED ZEROFILL',
   'on update CURRENT_TIMESTAMP',
);


if ($cfg['ShowFunctionFields']) {
    
    $cfg['Functions'] = array(
        'ABS',
        'ACOS',
        'ASCII',
        'ASIN',
        'ATAN',
        'BIN',
        'BIT_COUNT',
        'BIT_LENGTH',
        'CEILING',
        'CHAR',
        'CHAR_LENGTH',
        'COMPRESS',
        'COS',
        'COT',
        'CRC32',
        'CURDATE',
        'CURRENT_USER',
        'CURTIME',
        'DATE',
        'DAYNAME',
        'DEGREES',
        'DES_DECRYPT',
        'DES_ENCRYPT',
        'ENCRYPT',
        'EXP',
        'FLOOR',
        'FROM_DAYS',
        'FROM_UNIXTIME',
        'HEX',
        'INET_ATON',
        'INET_NTOA',
        'LENGTH',
        'LN',
        'LOG',
        'LOG10',
        'LOG2',
        'LOWER',
        'MD5',
        'NOW',
        'OCT',
        'OLD_PASSWORD',
        'ORD',
        'PASSWORD',
        'RADIANS',
        'RAND',
        'REVERSE',
        'ROUND',
        'SEC_TO_TIME',
        'SHA1',
        'SOUNDEX',
        'SPACE',
        'SQRT',
        'STDDEV_POP',
        'STDDEV_SAMP',
        'TAN',
        'TIMESTAMP',
        'TIME_TO_SEC',
        'UNCOMPRESS',
        'UNHEX',
        'UNIX_TIMESTAMP',
        'UPPER',
        'USER',
        'UTC_DATE',
        'UTC_TIME',
        'UTC_TIMESTAMP',
        'UUID',
        'VAR_POP',
        'VAR_SAMP',
        'YEAR',
    );

    
    $cfg['RestrictColumnTypes'] = array(
        'TINYINT'   => 'FUNC_NUMBER',
        'SMALLINT'  => 'FUNC_NUMBER',
        'MEDIUMINT' => 'FUNC_NUMBER',
        'INT'       => 'FUNC_NUMBER',
        'BIGINT'    => 'FUNC_NUMBER',
        'DECIMAL'   => 'FUNC_NUMBER',
        'FLOAT'     => 'FUNC_NUMBER',
        'DOUBLE'    => 'FUNC_NUMBER',
        'REAL'      => 'FUNC_NUMBER',
        'BIT'       => 'FUNC_NUMBER',
        'BOOLEAN'   => 'FUNC_NUMBER',
        'SERIAL'    => 'FUNC_NUMBER',

        'DATE'      => 'FUNC_DATE',
        'DATETIME'  => 'FUNC_DATE',
        'TIMESTAMP' => 'FUNC_DATE',
        'TIME'      => 'FUNC_DATE',
        'YEAR'      => 'FUNC_DATE',

        'CHAR'          => 'FUNC_CHAR',
        'VARCHAR'       => 'FUNC_CHAR',
        'TINYTEXT'      => 'FUNC_CHAR',
        'TEXT'          => 'FUNC_CHAR',
        'MEDIUMTEXT'    => 'FUNC_CHAR',
        'LONGTEXT'      => 'FUNC_CHAR',
        'BINARY'        => 'FUNC_CHAR',
        'VARBINARY'     => 'FUNC_CHAR',
        'TINYBLOB'      => 'FUNC_CHAR',
        'MEDIUMBLOB'    => 'FUNC_CHAR',
        'BLOB'          => 'FUNC_CHAR',
        'LONGBLOB'      => 'FUNC_CHAR',
        'ENUM'          => '',
        'SET'           => '',

        'GEOMETRY'              => 'FUNC_SPATIAL',
        'POINT'                 => 'FUNC_SPATIAL',
        'LINESTRING'            => 'FUNC_SPATIAL',
        'POLYGON'               => 'FUNC_SPATIAL',
        'MULTIPOINT'            => 'FUNC_SPATIAL',
        'MULTILINESTRING'       => 'FUNC_SPATIAL',
        'MULTIPOLYGON'          => 'FUNC_SPATIAL',
        'GEOMETRYCOLLECTION'    => 'FUNC_SPATIAL',

    );

    
    $cfg['RestrictFunctions'] = array(
        'FUNC_CHAR' => array(
            'BIN',
            'CHAR',
            'CURRENT_USER',
            'COMPRESS',
            'DAYNAME',
            'DES_DECRYPT',
            'DES_ENCRYPT',
            'ENCRYPT',
            'HEX',
            'INET_NTOA',
            'LOWER',
            'MD5',
            'OLD_PASSWORD',
            'PASSWORD',
            'REVERSE',
            'SHA1',
            'SOUNDEX',
            'SPACE',
            'UNCOMPRESS',
            'UNHEX',
            'UPPER',
            'USER',
            'UUID',
        ),

        'FUNC_DATE' => array(
            'CURDATE',
            'CURTIME',
            'DATE',
            'FROM_DAYS',
            'FROM_UNIXTIME',
            'NOW',
            'SEC_TO_TIME',
            'TIMESTAMP',
            'UTC_DATE',
            'UTC_TIME',
            'UTC_TIMESTAMP',
            'YEAR',
        ),

        'FUNC_NUMBER' => array(
            'ABS',
            'ACOS',
            'ASCII',
            'ASIN',
            'ATAN',
            'BIT_LENGTH',
            'BIT_COUNT',
            'CEILING',
            'CHAR_LENGTH',
            'COS',
            'COT',
            'CRC32',
            'DEGREES',
            'EXP',
            'FLOOR',
            'INET_ATON',
            'LENGTH',
            'LN',
            'LOG',
            'LOG2',
            'LOG10',
            'OCT',
            'ORD',
            'RADIANS',
            'RAND',
            'ROUND',
            'SQRT',
            'STDDEV_POP',
            'STDDEV_SAMP',
            'TAN',
            'TIME_TO_SEC',
            'UNIX_TIMESTAMP',
            'VAR_POP',
            'VAR_SAMP',
        ),

        'FUNC_SPATIAL' => array(
            'GeomFromText',
            'GeomFromWKB',

            'GeomCollFromText',
            'LineFromText',
            'MLineFromText',
            'PointFromText',
            'MPointFromText',
            'PolyFromText',
            'MPolyFromText',

            'GeomCollFromWKB',
            'LineFromWKB',
            'MLineFromWKB',
            'PointFromWKB',
            'MPointFromWKB',
            'PolyFromWKB',
            'MPolyFromWKB',
        ),
    );

    
    $cfg['DefaultFunctions'] = array(
        'FUNC_CHAR' => '',
        'FUNC_DATE' => '',
        'FUNC_NUMBER' => '',
        'first_timestamp' => 'NOW',
        'pk_char36' => 'UUID',
    );


} // end if


$cfg['NumOperators'] = array(
   '=',
   '>',
   '>=',
   '<',
   '<=',
   '!=',
   'LIKE',
   'NOT LIKE',
   'IN (...)',
   'NOT IN (...)',
   'BETWEEN',
   'NOT BETWEEN',
);


$cfg['TextOperators'] = array(
   'LIKE',
   'LIKE %...%',
   'NOT LIKE',
   '=',
   '!=',
   'REGEXP',
   'REGEXP ^...$',
   'NOT REGEXP',
   "= ''",
   "!= ''",
   'IN (...)',
   'NOT IN (...)',
   'BETWEEN',
   'NOT BETWEEN',
);


$cfg['EnumOperators'] = array(
   '=',
   '!=',
);


$cfg['SetOperators'] = array(
   'IN',
   'NOT IN',
);


$cfg['NullOperators'] = array(
   'IS NULL',
   'IS NOT NULL',
);


$cfg['UnaryOperators'] = array(
   'IS NULL' => 1,
   'IS NOT NULL' => 1,
   "= ''" => 1,
   "!= ''" => 1
);

?>
