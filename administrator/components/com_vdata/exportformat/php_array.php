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
    $plugin_list['php_array'] = array(
        'text'          => JText::_('PHP array'),
        'extension'     => 'php',
        'mime_type'     => 'text/plain',
        'options'       => array(
        array('type' => 'begin_group', 'name' => 'general_opts'),
            array(
                'type' => 'hidden',
                'name' => 'structure_or_data',
            ),
        array('type' => 'end_group')
        ),
        'options_text'  => JText::_('Options'),
    );
} else {


function PMA_exportComment($text)
{
    $this->PMA_exportOutputHandler('// ' . $text . "\n");
    return true;
}


function PMA_exportFooter()
{
    return true;
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

function PMA_exportHeader()
{
	 $data = JFactory::getApplication()->input->post->getArray(); 
	 $data['crlf'] ="\n"; 
    PMA_exportOutputHandler(
          '<?php' . $data['crlf']
        . '/**' . $data['crlf']
        . ' * Export to PHP Array plugin for PHPMyAdmin' . $data['crlf']
        . ' * @version 0.2b' . $data['crlf']
        . ' */' . $data['crlf'] . $data['crlf']
    );
    return true;
}


function PMA_exportDBHeader($db)
{   $data = JFactory::getApplication()->input->post->getArray();
	 $data['crlf'] ="\n"; 
    PMA_exportOutputHandler('//' . $data['crlf'] . '// Database ' . PMA_backquote($db) . $data['crlf'] . '//' . $data['crlf']);
    return true;
}


function PMA_exportDBFooter($db)
{
    return true;
}


function PMA_exportDBCreate($db)
{
    return true;
}


function PMA_exportData($db, $table, $crlf, $error_url, $sql_query)
{
    $data = JFactory::getApplication()->input->post->getArray();
	 $data['crlf'] ="\n"; 
	 $dbs = CheckDatabase();
	 $result      = $dbs->setQuery($sql_query)->loadObjectList();

   $fields_cnt  = array_keys($dbs->setQuery($sql_query)->loadAssoc());
    for ($i = 0; $i < count($fields_cnt); $i++) {
        $columns[$i] = stripslashes($fields_cnt[$i]);
    }
    unset($i);

    // fix variable names (based on http://www.php.net/manual/language.variables.basics.php)
    if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $table) == false) {
        // fix invalid chars in variable names by replacing them with underscores
        $tablefixed = preg_replace('/[^a-zA-Z0-9_\x7f-\xff]/', '_', $table);

        // variable name must not start with a number or dash...
        if (preg_match('/^[a-zA-Z_\x7f-\xff]/', $tablefixed) == false) {
            $tablefixed = '_' . $tablefixed;
        }
    } else {
        $tablefixed = $table;
    }

    $buffer = '';
    $record_cnt = 0;
	$checked_value = isset($data['checked_value'])&& $data['checked_value']!=''?explode(',', $data['checked_value']):array();
	$checked_id = isset($data['checked_id'])&& $data['checked_id']!=''?$data['checked_id']:'';
	$key_checke_id = array_search($checked_id, $fields_cnt);
    for ($j = 0; $j< count($result); $j++) {

       
      $record = $result[$j];
        // Output table name as comment if this is the first record of the table
		
		if(count($checked_value)>0)
			{
			if(!in_array($record->$checked_id, $checked_value)){
				continue;
			}
				
			}
		 $record_cnt++;
        if ($record_cnt == 1) {
            $buffer .= $crlf . '// ' . PMA_backquote($db) . '.' . PMA_backquote($table) . $crlf;
            $buffer .= '$' . $tablefixed . ' = array(' . $crlf;
            $buffer .= '  array(';
        } else {
            $buffer .= ',' . $crlf . '  array(';
        }

        for ($i = 0; $i < count($fields_cnt); $i++) {
           $buffer .= var_export($fields_cnt[$i], true) . " => " . var_export($record->$fields_cnt[$i], true) . (($i + 1 >= $fields_cnt) ? '' : ',');
        }

        $buffer .= ')';
    }

    $buffer .= $crlf . ');' . $crlf;
    if (! PMA_exportOutputHandler($buffer)) {
        return FALSE;
    }

    //PMA_DBI_free_result($result);

    return true;
}

}
