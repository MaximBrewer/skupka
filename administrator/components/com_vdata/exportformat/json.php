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
    $plugin_list['json'] = array(
        'text'          => 'JSON',
        'extension'     => 'json',
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

/**
 * Set of functions used to build exports of tables
 */


function PMA_exportComment($text)
{
    $this->PMA_exportOutputHandler('/* ' . $text . ' */' . $GLOBALS['crlf']);
    return true;
}


function PMA_exportFooter()
{
    return true;
}


function PMA_exportHeader()
{
$data = JFactory::getApplication()->input->post->getArray();
$data['crlf'] ="\n";  
   PMA_exportOutputHandler(
        '/**' . $data['crlf']
        . ' Export to JSON plugin for PHPMyAdmin' . $data['crlf']
        . ' @version 0.1' . $data['crlf']
        . ' */' . $data['crlf'] . $data['crlf']
    );
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
    PMA_exportOutputHandler('/* Database \'' . $db . '\' */ '."\n");
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
    $dbs = CheckDatabase();
	$data = JFactory::getApplication()->input->post->getArray();
	$result      = isset($sql_query)&& $sql_query!=''?$dbs->setQuery($sql_query)->loadObjectList():FALSE;
     
		if ($result == FALSE){
		$columns_cnt     = $dbs->setQuery('SHOW COLUMNS FROM '.PMA_backquote($db).'.'.PMA_backquote($table))->loadObjectList();
		
		}
		else
		{
		$object_array = get_object_vars($result[0]);
		$columns_cnt = count($object_array)>0?array_keys($object_array):array();
		}
	
    for ($i = 0; $i < count($columns_cnt); $i++) {
		
        $columns[$i] = empty($sql_query)?stripslashes($columns_cnt[$i]->Field):stripslashes($columns_cnt[$i]);
    }
    unset($i);

    $buffer = '';
    $record_cnt = 0;
	$checked_value = isset($data['checked_value'])&& $data['checked_value']!=''?explode(',', $data['checked_value']):array();
	$checked_id = isset($data['checked_id'])&& $data['checked_id']!=''?$data['checked_id']:'';
	$key_checke_id = array_search($checked_id, $columns_cnt);
    for($j = 0; $j < count($result); $j++) {
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
            $buffer .= '/* ' . $db . '.' . $table . ' */' . $crlf . $crlf;
            $buffer .= '[{';
        } else {
            $buffer .= ', {';
        }
      
        for ($i = 0; $i < count($columns_cnt); $i++) {

            $isLastLine = ($i + 1 >= count($columns_cnt));
            
            $column = empty($sql_query)?$columns_cnt[$i]->Field:$columns_cnt[$i];
          
            if (is_null($record->$column)) {
                $buffer .= '"' . $column . '": null' . (! $isLastLine ? ',' : '');
            } elseif (is_numeric($record->$column)) {
                $buffer .= '"' . $column . '": ' . $record->$column . (! $isLastLine ? ',' : '');
            } else {
                $buffer .= '"' . $column . '": "' . addslashes($record->$column) . '"' . (! $isLastLine ? ',' : '');
            }
        }

        $buffer .= '}';
    }

    $buffer .=  ']';
    if (! PMA_exportOutputHandler($buffer)) {
        return FALSE;
    }

    //PMA_DBI_free_result($result);

    return true;
}

}
