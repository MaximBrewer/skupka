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
    $plugin_list['xml'] = array(
        'text' => JText::_('XML'),
        'extension' => 'xml',
        'mime_type' => 'text/xml',
        'options' => array(
            array('type' => 'begin_group', 'name' => 'general_opts'),
            array('type' => 'hidden', 'name' => 'structure_or_data'),
            array('type' => 'end_group')
            ),
        'options_text' => JText::_('Options')
        );
    
    /* Export structure */
    $plugin_list['xml']['options'][] =
        array('type' => 'begin_group', 'name' => 'structure', 'text' => JText::_('Object creation options (all are recommended)'));
    $plugin_list['xml']['options'][] =
        array('type' => 'bool', 'name' => 'export_functions', 'text' => JText::_('Functions'));
    $plugin_list['xml']['options'][] =
        array('type' => 'bool', 'name' => 'export_procedures', 'text' => JText::_('Procedures'));
    $plugin_list['xml']['options'][] =
        array('type' => 'bool', 'name' => 'export_tables', 'text' => JText::_('Tables'));
    $plugin_list['xml']['options'][] =
        array('type' => 'bool', 'name' => 'export_triggers', 'text' => JText::_('Triggers'));
    $plugin_list['xml']['options'][] =
        array('type' => 'bool', 'name' => 'export_views', 'text' => JText::_('Views'));
    $plugin_list['xml']['options'][] = array('type' => 'end_group');
    
    /* Data */
    $plugin_list['xml']['options'][] =
        array('type' => 'begin_group', 'name' => 'data', 'text' => JText::_('Data dump options'));
    $plugin_list['xml']['options'][] =
        array('type' => 'bool', 'name' => 'export_contents', 'text' => JText::_('Export contents'));
    $plugin_list['xml']['options'][] = array('type' => 'end_group');
} else {


function PMA_exportComment($text) {
    return PMA_exportOutputHandler('<!-- ' . $text . ' -->' . "\n");
}
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

function PMA_exportFooter() {
    $foot = '</pma_xml_export>';
    
    return PMA_exportOutputHandler($foot);
}


function PMA_exportHeader() {
    global $crlf;
    global $cfg;
    global $db;
    global $table;
    global $tables;
    $data = JFactory::getApplication()->input->post->getArray(); 
	$config = DatabaseName();
	$crlf ="\n";
	$db = $config['db'];
	$dbs= CheckDatabase(); 
    $export_struct = (isset($data['xml_export_functions']) || isset($data['xml_export_procedures'])
        || isset($data['xml_export_tables']) || isset($data['xml_export_triggers'])
        || isset($data['xml_export_views']))&& $data['sql_query']=='';
    $export_data = isset($data['xml_export_contents']) ? true : false;

    if (isset($data['output_charset_conversion'])) {
        $charset = $data['charset_of_file'];
    } else {
        $charset = "utf-8";
    }

    $head  =  '<?xml version="1.0" encoding="' . $charset . '"?>' . $crlf
           .  '<!--' . $crlf
           .  '- phpMyAdmin XML Dump' . $crlf
           .  '- version ' . phpversion() . $crlf
           .  '- http://www.phpmyadmin.net' . $crlf
           .  '-' . $crlf;
          
		  
    $head .= $crlf
           .  '- ' . JText::_('Generation Time') . ': ' . PMA_localisedDate() . $crlf
          
           .  '- ' . JText::_('PHP Version') . ': ' . phpversion() . $crlf
           .  '-->' . $crlf . $crlf;
    
    $head .= '<pma_xml_export version="1.0"' . (($export_struct) ? ' xmlns:pma="http://www.phpmyadmin.net/some_doc_url/"' : '') . '>' . $crlf;
   
    if ($export_struct) {
        $result = $dbs->setQuery('SELECT `DEFAULT_CHARACTER_SET_NAME`, `DEFAULT_COLLATION_NAME` FROM `information_schema`.`SCHEMATA` WHERE `SCHEMA_NAME` = \''.$db.'\' LIMIT 1')->loadObjectList();
		 
		 $fr = $result[0];
		
        $db_collation = $fr->DEFAULT_COLLATION_NAME;
        $db_charset = $fr->DEFAULT_CHARACTER_SET_NAME;
       
        $head .= '    <!--' . $crlf;
        $head .= '    - Structure schemas' . $crlf;
        $head .= '    -->' . $crlf;
        $head .= '    <pma:structure_schemas>' . $crlf;
        $head .= '        <pma:database name="' . htmlspecialchars($db) . '" collation="' . $db_collation . '" charset="' . $db_charset . '">' . $crlf;
         
       
        if(empty($data['sql_query']) && !empty($data['table_name'])){
            // Export tables and views
			
            $result = $dbs->setQuery('SHOW CREATE TABLE ' . PMA_backquote($db) . '.' . PMA_backquote($data['table_name']), 0)->loadObjectList();
			$creat_table='Create Table';
            $tbl =  $result[0]->Table;
			$creat_table =  $result[0]->$creat_table;
            
            $is_view = false;
            
            if ($is_view) {
                $type = 'view';
            } else {
                $type = 'table';
            }
            
            if ($is_view && ! isset($data['xml_export_views'])) {
                continue;
            }
            
            if (! $is_view && ! isset($data['xml_export_tables'])) {
                continue;
            }
            
            $head .= '            <pma:' . $type . ' name="' . $data['table_name'] . '">' . $crlf;
            
            $tbl = "                " . htmlspecialchars($tbl);
            $tbl = str_replace("\n", "\n                ", $tbl);
            
            $head .= $creat_table . ';' . $crlf;
            $head .= '            </pma:' . $type . '>' . $crlf;
            
            if (isset($data['xml_export_triggers']) && $data['xml_export_triggers']) { 
                // Export triggers
                $triggers = '';//PMA_DBI_get_triggers($db, $table);
                if ($triggers) {
                    foreach ($triggers as $trigger) {
                        $code = $trigger['create'];
                        $head .= '            <pma:trigger name="' . $trigger['name'] . '">' . $crlf;
                        
                        // Do some formatting
                        $code = substr(rtrim($code), 0, -3);
                        $code = "                " . htmlspecialchars($code);
                        $code = str_replace("\n", "\n                ", $code);
                        
                        $head .= $code . $crlf;
                        $head .= '            </pma:trigger>' . $crlf;
                    }
                    
                    unset($trigger);
                    unset($triggers);
                }
            }
        }
        
        if (isset($data['xml_export_functions']) && $data['xml_export_functions']) {
            // Export functions
            $functions = '';//PMA_DBI_get_procedures_or_functions($db, 'FUNCTION');
            if ($functions) {
                foreach ($functions as $function) {
                    $head .= '            <pma:function name="' . $function . '">' . $crlf;
                    
                    // Do some formatting
                    $sql = PMA_DBI_get_definition($db, 'FUNCTION', $function);
                    $sql = rtrim($sql);
                    $sql = "                " . htmlspecialchars($sql);
                    $sql = str_replace("\n", "\n                ", $sql);
                    
                    $head .= $sql . $crlf;
                    $head .= '            </pma:function>' . $crlf;
                }
                
                unset($create_func);
                unset($function);
                unset($functions);
            }
        }
         
        if (isset($data['xml_export_procedures']) && $data['xml_export_procedures']) {
            // Export procedures
            $procedures = '';//PMA_DBI_get_procedures_or_functions($db, 'PROCEDURE');
            if ($procedures) {
                foreach ($procedures as $procedure) {
                    $head .= '            <pma:procedure name="' . $procedure . '">' . $crlf;
                    
                    // Do some formatting
                   $sql = '';//PMA_DBI_get_definition($db, 'PROCEDURE', $procedure);
                    $sql = rtrim($sql);
                    $sql = "                " . htmlspecialchars($sql);
                    $sql = str_replace("\n", "\n                ", $sql);
                    
                    $head .= $sql . $crlf;
                    $head .= '            </pma:procedure>' . $crlf;
                }
                
                unset($create_proc);
                unset($procedure);
                unset($procedures);
            }
        }
        
        unset($result);
        
        $head .= '        </pma:database>' . $crlf;
        $head .= '    </pma:structure_schemas>' . $crlf;
        
        if ($export_data) {
            $head .= $crlf;
        }
    }
   
    return PMA_exportOutputHandler($head);
}


 
function PMA_exportDBHeader($db) {
    $crlf="\n";
     $data = JFactory::getApplication()->input->post->getArray(); 
    if (isset($data['xml_export_contents']) && $data['xml_export_contents']) {
        $head = '    <!--' . $crlf
              . '    - ' . JText::_('Database') . ': ' . (isset($data['use_backquotes']) ? PMA_backquote($db) : '\'' . $db . '\''). $crlf
              . '    -->' . $crlf
              . '    <database name="' . htmlspecialchars($db) . '">' . $crlf;
        
        return PMA_exportOutputHandler($head);
    }
    else
    {
        return TRUE;
    }
}


 
function PMA_exportDBFooter($db) {
    
    $crlf="\n";
     $data = JFactory::getApplication()->input->post->getArray(); 
    if (isset($data['xml_export_contents']) && $data['xml_export_contents']) {
        return PMA_exportOutputHandler('    </database>' . $crlf);
    }
    else
    {
        return TRUE;
    }
}


 
function PMA_exportDBCreate($db) {
    return TRUE;
}



function PMA_exportData($db, $table, $crlf, $error_url, $sql_query) {
	$data = JFactory::getApplication()->input->post->getArray(); 
    $dbs = CheckDatabase();
    if (isset($data['xml_export_contents']) && $data['xml_export_contents']) {
        $result      = $dbs->setQuery($sql_query)->loadObjectList();
        
        $columns_cnt = array_keys($dbs->setQuery($sql_query)->loadAssoc());
        $columns = array();
		
        for ($i = 0; $i < count($columns_cnt); $i++) {
            $columns[$i] = stripslashes(str_replace(' ', '_', $columns_cnt[$i]));
        }
        unset($i);
        
        $buffer      = '        <!-- ' . JText::_('Table') . ' ' . $table . ' -->' . $crlf;
        if (!PMA_exportOutputHandler($buffer)) {
            return FALSE;
        }
		$checked_value = isset($data['checked_value'])&& $data['checked_value']!=''?explode(',', $data['checked_value']):array();
	    $checked_id = isset($data['checked_id'])&& $data['checked_id']!=''?$data['checked_id']:'';
	    $key_checke_id = array_search($checked_id, $columns_cnt);
         for ($j = 0; $j < count($result); $j++) {
           $results =  $result[$j];
			if(count($checked_value)>0)
			{
			if(!in_array($results->$checked_id, $checked_value)){
				continue;
			}
				
			}
            $buffer         = '        <table name="' . htmlspecialchars($table) . '">' . $crlf;
            for ($i = 0; $i < count($columns_cnt); $i++) {
                // If a cell is NULL, still export it to preserve the XML structure
				$c_name = $columns_cnt[$i];
                if (!isset($results->$c_name) || is_null($results->$c_name)) {
                    $results->$c_name = 'NULL';
                } 
				
                $buffer .= '            <column name="' . htmlspecialchars($columns[$i]) . '">' . htmlspecialchars((string)$results->$c_name)
                        .  '</column>' . $crlf;
            }
            $buffer         .= '        </table>' . $crlf;
            
            if (!PMA_exportOutputHandler($buffer)) {
                return FALSE;
            }
        }
       // PMA_DBI_free_result($result);
    }
 
    return TRUE;
} // end of the 'PMA_getTableXML()' function
}

?>
