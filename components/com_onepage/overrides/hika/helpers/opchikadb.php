<?php
defined('_JEXEC') or die('Restricted access');

class OPChikaDb {
	public static function transaction() {
		static $i; 
		if (empty($i)) {
		 $db = JFactory::getDBO(); 
		 $q = 'START TRANSACTION'; 
		 $db->setQuery($q); 
		 $db->query(); 
		 $i = 1; 
		}
		else {
		 $db = JFactory::getDBO(); 
		 $q = 'COMMIT'; 
		 $db->setQuery($q); 
		 $db->query(); 
		 $i = 0; 	
		}
		
		
	}
	
	public static function insertUpdateArray($table, $fields, $where=array()) {
		
		$db = JFactory::getDBO(); 
		if (!empty($where)) {
		$where_sql = array(); 
		foreach($where as $col=>$val) {
			$where_sql[$col] = ' `'.$db->escape($col)."` = '".$db->escape($val)."' "; 
		}
		$q = 'select * from `'.$db->escape($table).'` where '.implode(' AND ', $where_sql).' limit 1'; 
		$db->setQuery($q); 
		$row = $db->loadAssoc(); 
		if (!empty($row)) {
			
		   $update_sql  = array(); 
		   foreach ($fields as $col=>$val) {
			   if (is_null($val)) {
			     $update_sql[$col] = ' `'.$db->escape($col)."` = NULL "; 
			   }
			   else {
				 $update_sql[$col] = ' `'.$db->escape($col)."` = '".$db->escape($val)."' ";   
			   }
		   }
		   
		   $q = 'update `'.$db->escape($table).'` set '.implode(', ', $update_sql).' where '.implode(' AND ', $where_sql).' limit 1'; 
		   $db->setQuery($q); 
		   $db->query(); 
		   
		   $first_id = reset($where); 
		   return $first_id; 
		}
		}
		 
		 $ins_cols = array(); 
		 $ins_vals = array(); 
		 foreach ($fields as $col=>$val) {
			 $ins_cols[$col] = '`'.$db->escape($col).'`'; 
			 if (is_null($val)) {
				 $ins_vals[$col] = "NULL"; 
			 }
			 else {
			  $ins_vals[$col] = "'".$db->escape($val)."'"; 
			 }
			 
		 }
		 
		 $q = 'insert into `'.$db->escape($table).'` ('.implode(',', $ins_cols).') values ('.implode(',', $ins_vals).')'; 
		 $db->setQuery($q); 
		 $db->query(); 
		 $last_id = $db->insertid(); 
		 return $last_id; 
		
	}
	
}