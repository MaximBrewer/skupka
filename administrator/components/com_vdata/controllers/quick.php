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
define('PMA_CHARSET_NONE', 0);
define('PMA_CHARSET_ICONV', 1);
define('PMA_CHARSET_RECODE', 2);
define('PMA_CHARSET_ICONV_AIX', 3);
class VdataControllerQuick extends VdataController
{
	
	 var $default_source = '';

    /**
     * @var array   default configuration settings
     */
    var $default = array();

    /**
     * @var array   configuration settings
     */
    var $settings = array();

    /**
     * @var string  config source
     */
    var $source = '';

    /**
     * @var int     source modification time
     */
    var $source_mtime = 0;
    var $default_source_mtime = 0;
    var $set_mtime = 0;

    /**
     * @var boolean
     */
    var $error_config_file = false;

    /**
     * @var boolean
     */
    var $error_config_default_file = false;

    /**
     * @var boolean
     */
    var $error_pma_uri = false;

    /**
     * @var array
     */
    var $default_server = array();

    /**
     * @var boolean whether init is done or not
     * set this to false to force some initial checks
     * like checking for required functions
     */
    var $done = false;
	
	
	
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
		
		JFactory::getApplication()->input->set( 'view', 'quick' );
		$this->registerTask( 'add'  , 	'edit' );
	
	}
	
	function display($cachable = false, $urlparams = false){
		
		$user = JFactory::getUser();
		$canViewQuick = $user->authorise('core.access.quick', 'com_vdata');
		
		if(!$canViewQuick){
			$msg = JText::_( 'ALERT_AUTHORIZATION_ERROR' );
			$this->setRedirect( 'index.php?option=com_vdata', $msg , 'error');
		}
			
		parent::display();
	}
	
	function add_cron(){
		
		JSession::checkToken() or jexit( JText::_('INVALID_TOKEN') );
		$post = JFactory::getApplication()->input->post->getArray();
		$url = 'index.php?option=com_vdata&view=schedules&iotype=1&task=add&qry='.urlencode($post["sql_query"]);
		$this->setRedirect($url);
		
	}
	
	function edit()
	{
		
		
		JFactory::getApplication()->input->set( 'view', 'quick' );
		JFactory::getApplication()->input->set( 'layout', 'update'  );
		JFactory::getApplication()->input->set('hidemainmenu', 1);

		parent::display();
	}
	function importready()	{
	
		JSession::checkToken() or jexit( JText::_('INVALID_TOKEN') );
		
		$profileid = JFactory::getApplication()->input->getInt('profileid', 0);
		
		$model = $this->getModel('quick');
		
		if($model->get_csv_fields())	{
			$this->setRedirect('index.php?option=com_vdata&view=quick&layout=import&profileid='.$profileid);
		}
		else	{
			JFactory::getApplication()->enqueueMessage($model->getError());
			$this->setRedirect('index.php?option=com_vdata&view=phpmyadmin');
		}
	
	}
	function chechDatabase()
	{
	$model = $this->getModel('quick');	
	$driver_object=json_decode($model->getVdatConfig());
	
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
	function save()
	{
		$post	= JFactory::getApplication()->input->post->getArray();;
		$model = $this->getModel('quick');
		$dataid=$model->store($post);
		if ($dataid>0) {
			$msg = JText::_( 'Information Saved' );
		} else {
			$msg = JText::_( 'Information not Saved' );
		}

		$link = 'index.php?option=com_vdata&view=quick'; 
		// Check the table in so it can be edited.... we are done with it anyway
		$this->setRedirect($link, $msg);
	}
	function databaseName(){
	$model = $this->getModel('quick');	
	$driver_object=json_decode($model->getVdatConfig());
	
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
	function export_data(){
			$this->PMA_checkParameters(array('what', 'export_type'));
			$export_type = $what = JFactory::getApplication()->input->get('what','');
			 $da_base = $this->chechDatabase();
			$data = JFactory::getApplication()->input->post->getArray();;
			
			
				if($data['sql_query']!='')
				$table='';
                else
                 $table=$data['table_name'];			
				
				$table=str_replace('@', $da_base->getPrefix(),$data['table_name']);
				$export_type = 'table';
			
			define('PMA_MYSQL_INT_VERSION', 50520 );
			$export_list = $this->getPlugins(JPATH_ADMINISTRATOR.'/components/com_vdata/exportformat/', array('export_type' => $export_type, 'single_table' => isset($single_table)));
            $data = JFactory::getApplication()->input->post->getArray();;
		    $type = $what;
            $da = $this->databaseName();//
		// Check export type
		
		if (!isset($export_list[$type])) {
			die('Bad type!');
		}
		
		$compression_methods = array(
				'zip',
				'gzip',
				'bzip2',
			);
			$compression = false;
			$onserver = false;
			$save_on_server = false;
			$buffer_needed = false;
		if(JFactory::getApplication()->input->get('quick_or_custom') == 'quick') {
				$quick_export = true;
			} else {
				$quick_export = false;
			}
			if (JFactory::getApplication()->input->get('output_format') == 'astext') {
			$asfile = false;
			} 
			else 
			{
			    $asfile = true;
				if (in_array(JFactory::getApplication()->input->get('compression'), $compression_methods))
				{
				$compression = JFactory::getApplication()->input->get('compression');
				$buffer_needed = true;
				}
				$quick_export_onserver = JFactory::getApplication()->input->get('quick_export_onserver');
				$onserver = JFactory::getApplication()->input->get('onserver');
				if (($quick_export && !empty($quick_export_onserver)) || (!$quick_export && !empty($onserver))) {
				if($quick_export) {
				$onserver = $quick_export_onserver;
				} else {
				$onserver = $onserver;
				}
				// Will we save dump on server?
				$save_on_server = ! empty($cfg['SaveDir']) && $onserver;
				}
			}
			
			if (isset($export_list[$type]['force_file']) && ! $asfile) {
				//$message = PMA_Message::error(__('Selected export type has to be saved in file!'));
				require_once './libraries/header.inc.php';
				if ($export_type == 'server') {
					$active_page = 'server_export.php';
					require './server_export.php';
				} elseif ($export_type == 'database') {
					$active_page = 'db_export.php';
					require './db_export.php';
				} else {
					$active_page = 'tbl_export.php';
					require './tbl_export.php';
				}
				exit();
			}
		$err_url = '';
		  
			//require JPATH_ADMINISTRATOR.'/components/com_vdata/classes/database_interface.lib.php';
			require JPATH_ADMINISTRATOR.'/components/com_vdata/classes/Config.class.php';
			
			//require JPATH_ADMINISTRATOR.'/components/com_vdata/classes/Table.class.php';
			require JPATH_ADMINISTRATOR.'/components/com_vdata/classes/common.lib.php';
			require JPATH_ADMINISTRATOR.'/components/com_vdata/exportformat/' . $this->PMA_securePath($type) . '.php';
			 //$tables = PMA_DBI_get_tables('joomla');
			// Start with empty buffer
			$dump_buffer = '';
			$dump_buffer_len = 0;

			// We send fake headers to avoid browser timeout when buffering
			$time_start = time();
			if ($what == 'sql') {
			$crlf = "\n";
			} else {
			$crlf = $this->PMA_whichCrlf();
			}
			
		$output_kanji_conversion = function_exists('PMA_kanji_str_conv') && $type != 'xls';
        $output_charset_conversion = $asfile && isset($charset_of_file) && $charset_of_file != $charset && $type != 'xls';		
	
	if ($asfile) {
		

    $pma_uri_parts = parse_url(JPATH_BASE);
    if ($export_type == 'database') {
        if (isset($remember_template)) {
            $GLOBALS['PMA_Config']->setUserValue('pma_db_filename_template',
                'Export/file_template_database', $filename_template);
        }
    } else {
        if (isset($remember_template)) {
            $GLOBALS['PMA_Config']->setUserValue('pma_table_filename_template',
                'Export/file_template_table', $filename_template);
        }
    }
	$table_name_test = isset($data['filename_template'])&& $data['filename_template']!=''?$data['filename_template']:'export';
    $filename = $this->PMA_expandUserString($table_name_test);
    $filename = $this->PMA_sanitize_filename($filename);

    // Grab basic dump extension and mime type
    // Check if the user already added extension; get the substring where the extension would be if it was included
    $extension_start_pos = strlen($filename) - strlen($export_list[$type]['extension']) - 1;
    $user_extension = substr($filename, $extension_start_pos, strlen($filename));
    $required_extension = "." . $export_list[$type]['extension'];
    if(strtolower($user_extension) != $required_extension) {
        $filename  .= $required_extension;
    }
    $mime_type  = $export_list[$type]['mime_type'];

    // If dump is going to be compressed, set correct mime_type and add
    // compression to extension
    if ($compression == 'bzip2') {
        $filename  .= '.bz2';
        $mime_type = 'application/x-bzip2';
    } elseif ($compression == 'gzip') {
        $filename  .= '.gz';
        $mime_type = 'application/x-gzip';
    } elseif ($compression == 'zip') {
        $filename  .= '.zip';
        $mime_type = 'application/zip';
    }
   }
 
 
  if (!$save_on_server) {
    if ($asfile) {
        // Download
        // (avoid rewriting data containing HTML with anchors and forms;
        // this was reported to happen under Plesk)
        @ini_set('url_rewriter.tags','');
        $filename = $this->PMA_sanitize_filename($filename);

        header('Content-Type: ' . $mime_type);
        header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        if (0) {
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
        } else {
            header('Pragma: no-cache');
            // test case: exporting a database into a .gz file with Safari
            // would produce files not having the current time
            // (added this header for Safari but should not harm other browsers)
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        }
    } else {
        // HTML
        if ($export_type == 'database') {
            $num_tables = count($tables);
            
        }
        
        echo "\n" . '<div align="center">' . "\n";
        //echo '    <pre>' . "\n";

        /**
         * Displays a back button with all the $_REQUEST data in the URL (store in a variable to also display after the textarea)
         */
        
      } // end download
   }
  
  do {

// Add possibly some comments to export
if (!PMA_exportHeader()) {
    break;
} 

// Will we need relation & co. setup?
$do_relation = isset($data[$what . '_relation']);
$do_comments = isset($data[$what . '_include_comments']);
$do_mime     = isset($data[$what . '_mime']);
/* if ($do_relation || $do_comments || $do_mime) {
    $cfgRelation = PMA_getRelationsParam();
} */


// Include dates in export?
$do_dates   = isset($data[$what . '_dates']);

/**
 * Builds the dump
 */
// Gets the number of tables if a dump of a database has been required

if ($export_type == 'database') {

    if (!PMA_exportDBHeader($da['db'])) {
        break;
    }

    if (function_exists('PMA_exportRoutines') && strpos($data['sql_structure_or_data'], 'structure') !== false && isset($data['sql_procedure_function'])) {	
		
            PMA_exportRoutines($da['db']);
    }
   
    $i = 0;
    $views = array(); 
	 $dbs = $this->chechDatabase();
	
	 $tables = $dbs->setQuery("show tables from ".$da['db'])->loadObjectList();
    // $tables contains the choices from the user (via $table_select)
	
    foreach ($tables as $table) {
		
        // if this is a view, collect it for later; views must be exported after
        // the tables
		$table = $table->Tables_in_hexdata;
        $is_view = PMA_Table::isView($da['db'], $table);
        if ($is_view) {
            $views[] = $table;
        }
      
		if (1) {
            // for a view, export a stand-in definition of the table
            // to resolve view dependencies
            if (!PMA_exportStructure($da['db'], $table, $crlf, $err_url, $do_relation, $do_comments, $do_mime, $do_dates, $is_view ? 'stand_in' : 'create_table', $export_type)) {
                break 2;
            }
        }
        // if this is a view or a merge table, don't export data
        if (!($is_view || PMA_Table::isMerge($da['db'], $table))) {
            $local_query  = 'SELECT * FROM ' . PMA_backquote($da['db']) . '.' . PMA_backquote($table);
            if (!PMA_exportData($da['db'], $table, $crlf, $err_url, $local_query)) {
                break 2;
            }
        }
	
        // now export the triggers (needs to be done after the data because
        // triggers can modify already imported tables)
        if (1) {
            if (!PMA_exportStructure($da['db'], $table, $crlf, $err_url, $do_relation, $do_comments, $do_mime, $do_dates, 'triggers', $export_type)) {
                break 2;
            }
        }
    }
      
	foreach ($views as $view) {
        // no data export for a view
        if (1) {
            if (!PMA_exportStructure($da['db'], $view, $crlf, $err_url, $do_relation, $do_comments, $do_mime, $do_dates, 'create_view', $export_type)) {
                break 2;
            }
        }
    }

    if (!PMA_exportDBFooter($da['db'])) {
        break;
    }
} 
else {
	$da = $this->databaseName();
    if (!PMA_exportDBHeader($da['db'])) {
        break;
    }
  
	// We export just one table
    // $allrows comes from the form when "Dump all rows" has been selected
	
    if ( $data['sql_query']=='' && $data['allrows'] == '0' && $data['limit_to'] > 0 && $data['limit_from'] >= 0) {
        $add_query  = ' LIMIT '
                    . (($data['limit_from'] > 0) ? $data['limit_from'] . ', ' : '')
                    . $data['limit_to'];
    } else {
        $add_query  = '';
    }
 
    $is_view = false; 
	
    if ($data[$what . '_structure_or_data'] == 'structure' || $data[$what . '_structure_or_data'] == 'structure_and_data') {
        if (!PMA_exportStructure($da['db'], $table, $crlf, Juri::root(), $do_relation, $do_comments, $do_mime, $do_dates, $is_view ? 'create_view' : 'create_table', $export_type)) {
            break;
        }
    }
	
	
    // If this is an export of a single view, we have to export data;
    // for example, a PDF report
    // if it is a merge table, no data is exported
	
    if (($data[$what . '_structure_or_data'] == 'data' || $data[$what . '_structure_or_data'] == 'structure_and_data'))
		{
        
		if (!empty($data['sql_query'])) {
            // only preg_replace if needed
            if (!empty($add_query)) {
                // remove trailing semicolon before adding a LIMIT
                $sql_query = preg_replace('%;\s*$%', '', $sql_query);
            }
            $local_query = $data['sql_query'] . $add_query;
           // PMA_DBI_select_db($da['db']);
        } else {
            $local_query  = 'SELECT * FROM ' . PMA_backquote($da['db']) . '.' . PMA_backquote($table) . $add_query;
        }
        if (!PMA_exportData($da['db'], $table, $crlf, $err_url, $local_query)) {
            break;
        }
    }
    // now export the triggers (needs to be done after the data because
    // triggers can modify already imported tables)
   
    if (!PMA_exportDBFooter($da['db'])) {
        break;
    }
}

if (!PMA_exportFooter()) {
    break;
}

} while (false);
// End of fake loop
jexit();
if ($save_on_server && isset($message)) {
    require_once './libraries/header.inc.php';
    if ($export_type == 'server') {
        $active_page = 'server_export.php';
        require './server_export.php';
    } elseif ($export_type == 'database') {
        $active_page = 'db_export.php';
        require './db_export.php';
    } else {
        $active_page = 'tbl_export.php';
       // require './tbl_export.php';
    }
    exit();
}

/**
 * Send the dump as a file...
 */
if (!empty($asfile)) {
    // Convert the charset if required.
    if ($output_charset_conversion) {
        $dump_buffer = $this->PMA_convert_string($GLOBALS['charset'], $GLOBALS['charset_of_file'], $dump_buffer);
    }

    // Do the compression
    // 1. as a zipped file
    if ($compression == 'zip') {
        if (@function_exists('gzcompress')) {
            $zipfile = new zipfile();
            $zipfile -> addFile($dump_buffer, substr($filename, 0, -4));
            $dump_buffer = $zipfile -> file();
        }
    }
    // 2. as a bzipped file
    elseif ($compression == 'bzip2') {
        if (@function_exists('bzcompress')) {
            $dump_buffer = bzcompress($dump_buffer);
        }
    }
    // 3. as a gzipped file
    elseif ($compression == 'gzip') {
        if (@function_exists('gzencode') && !@ini_get('zlib.output_compression')) {
            // without the optional parameter level because it bug
            $dump_buffer = gzencode($dump_buffer);
        }
    }

    /* If ve saved on server, we have to close file now */
    if ($save_on_server) {
        $write_result = @fwrite($file_handle, $dump_buffer);
        fclose($file_handle);
        if (strlen($dump_buffer) !=0 && (!$write_result || ($write_result != strlen($dump_buffer)))) {
            //$message = JText::_('Insufficient space to save the file %s.'), PMA_Message::ERROR, $save_filename);
        } else {
            //$message = new PMA_Message(__('Dump has been saved to file %s.'), PMA_Message::SUCCESS, $save_filename);
        }

        
    } else {
        echo $dump_buffer;
    }
}
/**
 * Displays the dump...
 */
else {
    /**
     * Close the html tags and add the footers in dump is displayed on screen
     */
    echo '</textarea>' . "\n"
       . '    </form>' . "\n";
    echo $back_button;

    echo "\n";
    echo '</div>' . "\n";
    echo "\n";

    
} // end if

}
	function PMA_sanitize_filename($filename) {
    $filename = preg_replace('/[^A-Za-z0-9_.-]/', '_', $filename);
    return $filename;
}
function optimization() {
	       $model = $this->getModel('quick');
	       $obj = $model->optimization();
	       jexit(json_encode($obj));
	
}
function repaired() {
	       $model = $this->getModel('quick');
	       $obj = $model->repaired();
	       jexit(json_encode($obj));
	
}
function PMA_getenv($var_name) {
    if (isset($_SERVER[$var_name])) {
        return $_SERVER[$var_name];
    } elseif (isset($_ENV[$var_name])) {
        return $_ENV[$var_name];
    } elseif (getenv($var_name)) {
        return getenv($var_name);
    } elseif (function_exists('apache_getenv')
     && apache_getenv($var_name, true)) {
        return apache_getenv($var_name, true);
    }

    return '';
}
function PMA_expandUserString($string, $escape = NULL, $updates = array()) {
    /* Content */
	$vars = array();
	$da = $this->databaseName();
	
    $vars['http_host'] = $this->PMA_getenv('HTTP_HOST') ? $this->PMA_getenv('HTTP_HOST') : '';
    $vars['server_name'] = $GLOBALS['_SERVER']['SERVER_NAME'];
    $vars['server_verbose'] = '@VERBOSE@';
    $vars['server_verbose_or_name'] = $GLOBALS['_SERVER']['SERVER_NAME'];
    $vars['database'] = $da['db'];
    $vars['table'] = '@TABLE@';
    $vars['phpmyadmin_version'] = 'phpMyAdmin ' . $GLOBALS['_SERVER']['SERVER_SOFTWARE'];

    /* Update forced variables */
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

    /* Do the replacement */
    return str_replace(array_keys($replace), array_values($replace), strftime($string));
}	
	
	function PMA_whichCrlf()
{
    $the_crlf = "\n";

    // The 'PMA_USR_OS' constant is defined in "./libraries/Config.class.php"
    // Win case
    if (1) {
        $the_crlf = "\r\n";
    }
    // Others
    else {
        $the_crlf = "\n";
    }

    return $the_crlf;
}
	function PMA_generate_common_url()
{
    $args = func_get_args();

    if (isset($args[0]) && is_array($args[0])) {
        // new style
        $params = $args[0];

        if (isset($args[1])) {
            $encode = $args[1];
        } else {
            $encode = 'html';
        }

        if (isset($args[2])) {
            $questionmark = $args[2];
        } else {
            $questionmark = '?';
        }
    } else {
        // old style

        if ($this->PMA_isValid($args[0])) {
            $params['db'] = $args[0];
        }

        if ($this->PMA_isValid($args[1])) {
            $params['table'] = $args[1];
        }

        if (isset($args[2]) && $args[2] !== '&amp;') {
            $encode = 'text';
        } else {
            $encode = 'html';
        }

        $questionmark = '';
    }

    $separator = $this->PMA_get_arg_separator();

    if (isset($GLOBALS['server'])
        && $GLOBALS['server'] != $GLOBALS['cfg']['ServerDefault']
            // avoid overwriting when creating navi panel links to servers
        && ! isset($params['server'])) {
        $params['server'] = $GLOBALS['server'];
    }

    

    if (empty($params)) {
        return '';
    }

    $query = $questionmark . http_build_query($params, null, $separator);

    if ($encode === 'html') {
        $query = htmlspecialchars($query);
    }

    return $query;
}
function PMA_isValid(&$var, $type = 'length', $compare = null)
{
    if (! isset($var)) {
        // var is not even set
        return false;
    }

    if ($type === false) {
        // no vartype requested
        return true;
    }

    if (is_array($type)) {
        return in_array($var, $type);
    }

    // allow some aliaes of var types
    $type = strtolower($type);
    switch ($type) {
        case 'identic' :
            $type = 'identical';
            break;
        case 'len' :
            $type = 'length';
            break;
        case 'bool' :
            $type = 'boolean';
            break;
        case 'float' :
            $type = 'double';
            break;
        case 'int' :
            $type = 'integer';
            break;
        case 'null' :
            $type = 'NULL';
            break;
    }

    if ($type === 'identical') {
        return $var === $compare;
    }

    // whether we should check against given $compare
    if ($type === 'similar') {
        switch (gettype($compare)) {
            case 'string':
            case 'boolean':
                $type = 'scalar';
                break;
            case 'integer':
            case 'double':
                $type = 'numeric';
                break;
            default:
                $type = gettype($compare);
        }
    } elseif ($type === 'equal') {
        $type = gettype($compare);
    }

    // do the check
    if ($type === 'length' || $type === 'scalar') {
        $is_scalar = is_scalar($var);
        if ($is_scalar && $type === 'length') {
            return (bool) strlen($var);
        }
        return $is_scalar;
    }

    if ($type === 'numeric') {
        return is_numeric($var);
    }

    if (gettype($var) === $type) {
        return true;
    }

    return false;
}
function PMA_get_arg_separator($encode = 'none')
{
    static $separator = null;

    if (null === $separator) {
        // use seperators defined by php, but prefer ';'
        // as recommended by W3C
        $php_arg_separator_input = ini_get('arg_separator.input');
        if (strpos($php_arg_separator_input, ';') !== false) {
            $separator = ';';
        } elseif (strlen($php_arg_separator_input) > 0) {
            $separator = $php_arg_separator_input{0};
        } else {
            $separator = '&';
        }
    }

    switch ($encode) {
        case 'html':
            return htmlentities($separator);
            break;
        case 'text' :
        case 'none' :
        default :
            return $separator;
    }
}
function PMA_kanji_str_conv($str, $enc, $kana) {
    global $enc_list;

    if ($enc == '' && $kana == '') {
        return $str;
    }
    $nw       = mb_detect_encoding($str, $enc_list);

    if ($kana == 'kana') {
        $dist = mb_convert_kana($str, 'KV', $nw);
        $str  = $dist;
    }
    if ($nw != $enc && $enc != '') {
        $dist = mb_convert_encoding($str, $enc, $nw);
    } else {
        $dist = $str;
    }
    return $dist;
}	
	function PMA_securePath($path)
{
    // change .. to .
    $path = preg_replace('@\.\.*@', '.', $path);

    return $path;
}	
	public static function PMA_backquote($a_name, $do_it = true)
   {
    if (is_array($a_name)) {
        foreach ($a_name as &$data) {
            $data = $this->PMA_backquote($data, $do_it);
        }
        return $a_name;
    }	
	}
  public static function PMA_exportOutputHandler($line)
    { 
    global $time_start, $dump_buffer, $dump_buffer_len, $save_filename;

    // Kanji encoding convert feature
    if ($GLOBALS['output_kanji_conversion']) {
        $line = $this->PMA_kanji_str_conv($line, $GLOBALS['knjenc'], isset($GLOBALS['xkana']) ? $GLOBALS['xkana'] : '');
    }
    // If we have to buffer data, we will perform everything at once at the end
    if ($GLOBALS['buffer_needed']) {

        $dump_buffer .= $line;
        if ($GLOBALS['onfly_compression']) {

            $dump_buffer_len += strlen($line);

            if ($dump_buffer_len > $GLOBALS['memory_limit']) {
                if ($GLOBALS['output_charset_conversion']) {
                    $dump_buffer = $this->PMA_convert_string($GLOBALS['charset'], $GLOBALS['charset_of_file'], $dump_buffer);
                }
                // as bzipped
                if ($GLOBALS['compression'] == 'bzip2'  && @function_exists('bzcompress')) {
                    $dump_buffer = bzcompress($dump_buffer);
                }
                // as a gzipped file
                elseif ($GLOBALS['compression'] == 'gzip' && @function_exists('gzencode')) {
                    // without the optional parameter level because it bug
                    $dump_buffer = gzencode($dump_buffer);
                }
                if ($GLOBALS['save_on_server']) {
                    $write_result = @fwrite($GLOBALS['file_handle'], $dump_buffer);
                    if (!$write_result || ($write_result != strlen($dump_buffer))) {
                        //$GLOBALS['message'] = PMA_Message::error(__('Insufficient space to save the file %s.'));
                        $GLOBALS['message']->addParam($save_filename);
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
        if ($GLOBALS['asfile']) {
            if ($GLOBALS['output_charset_conversion']) {
                $line = $this->PMA_convert_string($GLOBALS['charset'], $GLOBALS['charset_of_file'], $line);
            }
            if ($GLOBALS['save_on_server'] && strlen($line) > 0) {
                $write_result = @fwrite($GLOBALS['file_handle'], $line);
                if (!$write_result || ($write_result != strlen($line))) {
                    //$GLOBALS['message'] = PMA_Message::error(__('Insufficient space to save the file %s.'));
                    $GLOBALS['message']->addParam($save_filename);
                    return false;
                }
                $time_now = time();
                if ($time_start >= $time_now + 30) {
                    $time_start = $time_now;
                    header('X-pmaPing: Pong');
                } // end if
            } else {
                // We export as file - output normally
                echo $line;
            }
        } else {
            // We export as html - replace special chars
            echo htmlspecialchars($line);
        }
    }
    return true;
} 
	function PMA_DBI_get_procedures_or_functions($db, $which, $link = null)
{  
    $shows = $this->PMA_DBI_fetch_result('SHOW ' . $which . ' STATUS;', null, null, $link);
    $result = array();
    foreach ($shows as $one_show) {
        if ($one_show['Db'] == $db && $one_show['Type'] == $which) {
            $result[] = $one_show['Name'];
        }
    }
    return($result);
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
            return $this->PMA_aix_iconv_wrapper($src_charset, $dest_charset . $GLOBALS['cfg']['IconvExtraParams'], $what);
        default:
            return $what;
    }
}	
		function PMA_checkParameters($params, $die = true, $request = true)
	{
		global $checked_special;

		if (!isset($checked_special)) {
			$checked_special = false;
		}

		//$reported_script_name = basename($GLOBALS['PMA_PHP_SELF']);
		$found_error = false;
		$error_message = '';

		foreach ($params as $param) {
			if ($request && $param != 'db' && $param != 'table') {
				$checked_special = true;
			}

			/* if (!isset($GLOBALS[$param])) {
				$error_message .= $reported_script_name
					. ': Missing parameter: ' . $param
					. $this->PMA_showDocu('faqmissingparameters')
					. '<br />';
				$found_error = true;
			} */
		}
		if ($found_error) {
			/**
			 * display html meta tags
			 */
			require_once './libraries/header_meta_style.inc.php';
			echo '</head><body><p>' . $error_message . '</p></body></html>';
			if ($die) {
				exit();
			}
		}
	}
	
    function PMA_showDocu($anchor) {
    /* if ($GLOBALS['cfg']['ReplaceHelpImg']) {
        return '<a href="Documentation.html#' . $anchor . '" target="documentation"><img class="icon" src="' . $GLOBALS['pmaThemeImage'] . 'b_help.png" width="11" height="11" alt="' . __('Documentation') . '" title="' . JText::_('Documentation') . '" /></a>';
    } else { */
        return '[<a href="Documentation.html#' . $anchor . '" target="documentation">' . JText::_('Documentation') . '</a>]';
    }
function PMA_DBI_getCompatibilities()
{ 
    $compats = array('NONE');
    $compats[] = 'ANSI';
    $compats[] = 'DB2';
    $compats[] = 'MAXDB';
    $compats[] = 'MYSQL323';
    $compats[] = 'MYSQL40';
    $compats[] = 'MSSQL';
    $compats[] = 'ORACLE';
    // removed; in MySQL 5.0.33, this produces exports that
    // can't be read by POSTGRESQL (see our bug #1596328)
    //$compats[] = 'POSTGRESQL';
    $compats[] = 'TRADITIONAL';

    return $compats;
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
function getPlugins($plugins_dir, $plugin_param)
{
    /* Scan for plugins */
	
      $plugin_list = array();
      
	  if ($handle = opendir($plugins_dir)) {
        $is_first = 0;
        while ($file = readdir($handle)) { 
           
            if (is_file($plugins_dir . $file) && preg_match('@^[^\.](.)*\.php$@i', $file)) {
              
				include $plugins_dir . $file;
            }
        }
   }
  
	ksort($plugin_list);
    return $plugin_list;
}
	function apply()
	{
		$post	= JFactory::getApplication()->input->post->getArray();;
		$model = $this->getModel('quick');
		$dataid=$model->store($post);
		if ($dataid>0) {
			$msg = JText::_( 'Information Saved' );
		} else {
			$msg = JText::_( 'Information not Saved' );
		}

		$link = 'index.php?option=com_vdata&view=quick&task=edit&main_table_name='.$post['table_name'].'&column_name='.$post['column_name'].'&cid[]='.(int)$dataid;
		// Check the table in so it can be edited.... we are done with it anyway
		$this->setRedirect($link, $msg);
	}
	function plugintaskajax()
	{
		
		$plugin = explode('.', JFactory::getApplication()->input->get('plugin', ''));
		
		if(count($plugin)==2)	{
		
			JPluginHelper::importPlugin('vdata', $plugin[0]);
			$dispatcher = JDispatcher::getInstance();
			
			try{
				$dispatcher->trigger($plugin[1]);
			}catch(Exception $e){
				jexit('{"result":"error", "error":"'.$e->getMessage().'"}');
			}
		
		}
		
	}
		
	function import_now()	{
	
		JSession::checkToken() or jexit( JText::_('INVALID_TOKEN') );
		
		$model = $this->getModel('quick');		
		$profile = $model->getProfile();
		
		JPluginHelper::importPlugin('vdata', $profile->plugin);
		$dispatcher = JDispatcher::getInstance();
		
		try{
			$return = $dispatcher->trigger('startImport');
			$msg = JText::sprintf('IMPORT_SUCCESS', $return[0]);
			$this->setRedirect('index.php?option=com_vdata&view=quick', $msg);
		}catch(Exception $e){
			JFactory::getApplication()->enqueueMessage($e->getMessage());
			$this->setRedirect('index.php?option=com_vdata&view=quick&layout=import&profileid='.$profile->id);
		}
	
	}
	
	/**
	 * cancel an action
	 * @return void
	 */
	function cancel()
	{
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( 'index.php?option=com_vdata&view=quick', $msg );
	}
	
	/**
	 * cancel an action
	 * @return void
	 */
	function close()
	{
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( 'index.php?option=com_vdata&view=quick', $msg );
	}
   function remove()
	{
		$model = $this->getModel('quick');
		
		if(!$model->delete()) {
			JFactory::getApplication()->enqueueMessage( $model->getError());
			$this->setRedirect( 'index.php?option=com_vdata&view=quick');
		} else {
			$msg = JText::_( 'Record(s) Deleted' );
			$this->setRedirect( 'index.php?option=com_vdata&view=quick', $msg );
		}
		
	}
	function delete_value()
	{
		$model = $this->getModel('quick');
		
		 $data = new stdClass();
		if(!$model->delete()) {
			
		  $data->result = 'error';
			
		} else {
		
		$data->result = 'success';
		jexit(json_encode($data));
		
		}
		
	}
}