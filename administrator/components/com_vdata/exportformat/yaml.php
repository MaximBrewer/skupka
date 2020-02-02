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
    $plugin_list['yaml'] = array(
        'text'          => 'YAML',
        'extension'     => 'yml',
        'mime_type'     => 'text/yaml',
        'force_file'    => true,
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
    $data =  JFactory::getApplication()->input->post->getArray(); 
	  $data['crlf'] = "\n";
	$this->PMA_exportOutputHandler('# ' . $text . $data['crlf']);
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

function PMA_exportFooter()
{
	 $data =  JFactory::getApplication()->input->post->getArray(); 
	  $data['crlf'] = "\n";
    PMA_exportOutputHandler('...' . $data['crlf']);
    return true;
}


function PMA_exportHeader()
{
    $data = JFactory::getApplication()->input->post->getArray(); 
	  $data['crlf'] = "\n";
	PMA_exportOutputHandler('%YAML 1.1' . $data['crlf'] . '---' . $data['crlf']);
    return true;
}


function PMA_exportDBHeader($db)
{
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
	 $dbs = CheckDatabase();
	$result      = $dbs->setQuery($sql_query)->loadObjectList();

     $columns_cnt = array_keys($dbs->setQuery($sql_query)->loadAssoc());
    for ($i = 0; $i < count($columns_cnt); $i++) {
        $columns[$i] = stripslashes($columns_cnt[$i]);
    }
    unset($i);

    $buffer = '';
    $record_cnt = 0;
	$checked_value = isset($data['checked_value'])&& $data['checked_value']!=''?explode(',', $data['checked_value']):array();
	$checked_id = isset($data['checked_id'])&& $data['checked_id']!=''?$data['checked_id']:'';
	$key_checke_id = array_search($checked_id, $columns_cnt);
	for ($j = 0; $j < count($result); $j++) {
   $record = $result[$j];
        $record_cnt++;
         if(count($checked_value)>0)
			{
			if(!in_array($record->$checked_id, $checked_value)){
				continue;
			}
				
			}
        // Output table name as comment if this is the first record of the table
        if ($record_cnt == 1) {
            $buffer = '# ' . $db . '.' . $table . $crlf;
            $buffer .= '-' . $crlf;
        } else {
            $buffer = '-' . $crlf;
        }

        for ($i = 0; $i < count($columns_cnt); $i++) {
            $c_name = $columns_cnt[$i];
			if (! isset($record->$c_name)) {
                continue;
            }

            $column = $columns[$i];

            if (is_null($record->$c_name)) {
                $buffer .= '  ' . $c_name . ': null' . $crlf;
                continue;
            }

            if (is_numeric($record->$c_name)) {
                $buffer .= '  ' . $c_name . ': '  . $record->$c_name . $crlf;
                continue;
            }

            $record->$c_name = str_replace(array('\\', '"', "\n", "\r"), array('\\\\', '\"', '\n', '\r'), $record->$c_name);
            $buffer .= '  ' . $c_name . ': "' . $record->$c_name . '"' . $crlf;
        }

        if (! PMA_exportOutputHandler($buffer)) {
            return FALSE;
        }
    }
    //PMA_DBI_free_result($result);

    return true;
}

}
?>
