<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

class OPCNumbering {
	
	public static function getNext($agenda_id, $type_id, $order_id, &$created=null, &$ai)
	{
		
		
		
		$createAi = false;
		
		if (!empty($ai)) { 
		$createAi = true; 
		}
		
		if ((!is_null($order_id)) && ($order_id > 0))
		{
		 $find = self::getLine($agenda_id, $type_id, $order_id); 
		}
		else
		{
		  $find = false;
		}
		
		
		if (!$createAi)
		{
		 $ai = 0; 		
		}
		
		
		
		if ($find !== false) 
		if (!empty($find))
		{			
			$type_idf = (int)$find['type_id']; 
			$type_id = (int)$type_id; 
			$ai = $find['ai'];
			
			$numbering = $find['result']; 
			
			
			
			if ($type_idf  === $type_id)
			if (!empty($find['result']))
			{
			 
			   if (!empty(self::$debug)) echo "<br />\n".'ai found in db.... '."<br />\n"; 
			  return $find['result']; 
			}
		}
		
		$config = JFactory::getConfig();
      
		
		
		$config = self::getAgendaConfig($agenda_id); 
		if ($config === false) return false; 
		// add offest here: 
		
		
		
		$c = JFactory::getConfig(); 
		if (method_exists($c, 'get'))
		$t = $c->get('offset'); 
	    else $t = $c->getValue('config.offset'); 
	    
		$stored_timezone = date_default_timezone_get(); 
		@date_default_timezone_set($t);

		
		if (empty($created)) $created = time(); 
		$from_time = 0; 
		$to_time = 0; 
		
		if (empty($find))
		{
		
		

		
		
		if (!empty($config['reseton']))
		{
			
			
			/* reset constants: 
			COM_ONEPAGE_NUMBERING_RESETON_0="(do not reset)"
		    COM_ONEPAGE_NUMBERING_RESETON_1="New year"
			COM_ONEPAGE_NUMBERING_RESETON_2="New month"
			COM_ONEPAGE_NUMBERING_RESETON_3="New day"
			*/
			switch ($config['reseton'])
			{
				case 3: 
				

				// new day
				  //int mktime ([ int $hour = date("H") [, int $minute = date("i") [, int $second = date("s") [, int $month = date("n") [, int $day = date("j") [, int $year = date("Y") [, int $is_dst = -1 ]]]]]]] )
				  
				  // daylight saving disabled... 
				 $from_time = mktime(0, 0, 0, date('n', $created), date('j', $created), date('Y', $created)); 
				 $to_time = mktime(23, 59, 59, date('n', $created), date('j', $created), date('Y', $created));	
 
		if (!empty(self::$debug))
		{
			echo 'new day:'; 
			echo "<br />".'from time: '.date(DATE_ATOM, $from_time)."<br />";
			echo "<br />".'to time: '.date(DATE_ATOM, $to_time)."<br />";
			
		}				

				break; 
				
				case 2: 
		

				// new month
				  //int mktime ([ int $hour = date("H") [, int $minute = date("i") [, int $second = date("s") [, int $month = date("n") [, int $day = date("j") [, int $year = date("Y") [, int $is_dst = -1 ]]]]]]] )
				  
				  // daylight saving disabled... 
				  //$from_time = mktime(0, 0, 0, 0, date('j', $created), date('Y', $created), 0); 
				  //
				  $from_time = $startTime = mktime(0, 0, 0, date('m', $created), 1, date('Y', $created)); 
				  $to_time = mktime(23,59,59,date('n', $created),date("t", $created),date('Y', $created)); 			  
				  
				  if (!empty(self::$debug))
		{
			echo 'new month:'; 
			echo "<br />".'from time: '.date(DATE_ATOM, $from_time)."<br />";
			echo "<br />".'to time: '.date(DATE_ATOM, $to_time)."<br />";
			
		}
				  
				  break; 
				case 4: 
				
				$from_time = 0; $endTime = $to_time = 0; 
				
				
				$next_order_id = self::getNextOrderId(); 
				$ai = $next_order_id; 
				
				
				break;   
				default: 
				// new year
				  // daylight saving disabled... 
				  $from_time = mktime(0, 0, 0, 1, 1, date('Y', $created)); 
				  //$to_time = mktime(0, 0, 0, 0, 0, date('Y', $created)+1, 0) - 1; 
				  $endTime = $to_time = mktime(23, 59, 59, 12, 31, date('Y', $created));    
				  
				    //int mktime ([ int $hour = date("H") [, int $minute = date("i") [, int $second = date("s") [, int $month = date("n") [, int $day = date("j") [, int $year = date("Y") [, int $is_dst = -1 ]]]]]]] )
				  if (!empty(self::$debug))
		{
			echo 'new year:'; 
			echo "<br />".'from time: '.date(DATE_ATOM, $from_time)."<br />";
			echo "<br />".'to time: '.date(DATE_ATOM, $to_time)."<br />";
			
		}
				
				  
				  break;   
				  
				  
			}
			
			
		}
		
		
		
		if ($createAi)
		{
			$ai = (int)$ai; 
			$ai = $ai - 1; 
			
			$ai_res = array(); $ai_res['ai'] = $ai; 
			
			
			
			
		}
		else
		{
		 $ai_res = self::getNextAI($agenda_id, $from_time, $to_time); 
		 
		}
		if (empty($ai))
		{
		
		$ai = $ai_res['ai']; 
		}
		
		}
		else
		{
			
		}
		
		
		
		
		
		
		
		
		$format = $config['format']; 
		if (!empty(self::$testFormat)) $format = self::$testFormat; 
		
		if (!empty(self::$debug)) echo "<br />\n".'format: '.$format."<br />\n"; 
		
		
		
		if (empty($numbering))
		$numbering = self::getIntoFormat($ai, $format, $created); 
		else
		{
			
		}
		
		
		
		if (self::$debug)
		{
			echo 'ai: '.$ai."<br />"; 
		}
		
		
		
		
		if ($createAi)
		{
			self::storeNumbering($agenda_id, $order_id, $type_id, $numbering, $created, $ai); 

		}
		
		@date_default_timezone_set($stored_timezone);
		
		return $numbering; 
	}
	
	
	/* agenda_id is the numbering system which shares the same auto increment (ai) number
	// type_id is a local constant: TYPE_ORDER:1 TYPE_INVOICE:2 OR TYPE_OPCPDF:3  
	// type: is a specific order_id or other dependent field (agenda_id can share more type_id's or type's (refund may share the same type's ID (order_id) with multiple type_id's within the same agenda
	// created is a unix timestamp per which the time related input is calculated
	*/
	
	public static function requestNew($agenda_id, $type_id, $order_id, $created=null )
	{
		
		$ai = 0; 
	    $numbering = self::getNext($agenda_id, $type_id, $order_id, $created, $ai); 
		if (!empty($ai) && (!empty($numbering)))
		{
		 self::storeNumbering($agenda_id, $order_id, $type_id, $numbering, $created, $ai); 
		}
	    else 
		{
			return false; 
		}
		return $numbering; 

	}
	
	
	public static $testFormat; 
	public static $debug; 
	public static function updateTypeid($agenda_id, $type_id, $order_id, $numbering)
	{	
		//note, this updates ALL agends (order + invoice + etc) since we don't use the type_id here
		
		$db = JFactory::getDBO(); 
		$q = "update #__onepage_numbering set ref_type = '".$db->escape($order_id)."' where ref_idagenda = ".(int)$agenda_id." and result = '".$db->escape($numbering)."' and ref_type = -1"; 
		
		$db->setQuery($q); 
		$db->query(); 
		
		$e = $db->getErrorMsg(); 
		
	}
	
	public static function updateTypeidByNumber($agenda_id, $type_id, $order_id, $numbering)
	{
		if (empty($order_id))
		{
		  $order_id = -1; 
		}
	
		$db = JFactory::getDBO(); 
		$q = "update #__onepage_numbering set ref_type = '".$db->escape($order_id)."' where ref_idagenda = ".(int)$agenda_id." and result = '".$db->escape($numbering)."' order by `ai` desc limit 1"; 
		$db->setQuery($q); 
		$db->query(); 
	}
	public static function storeNumbering($agenda_id, $order_id, $type_id, $numbering, $created, $ai)
	{
		
		
		if (is_null($order_id)) $order_id = -1; 
		
		$db = JFactory::getDBO(); 
		$created_on = time(); 
		$q = 'insert into `#__onepage_numbering` (`id`, `ref_idagenda`, `ref_idtype`, `ref_type`, `ai`, `created`, `created_on`, `result`) '; 
		$q .= ' values (NULL, '.(int)$agenda_id.', '.(int)$type_id.", ".(int)$order_id.", ".(int)$ai.", ".(int)$created.", ".(int)$created_on.", '".$db->escape($numbering)."')"; 
		
		$db->setQuery($q); 
		$db->query(); 
		$e = $db->getErrorMsg(); if (!empty($e)) {
		  JFactory::getApplication()->enqueueMessage($e); 
		  return; 
		}
		
		if (!empty(self::$debug))
		{
			echo "<br />\n".'store numbering: '.$q."<br />\n"; 
		}
		
	}
	
	public static function getIntoFormat($ai, $format, $created_on)
	{
	   	$ai = (int)$ai; 
		$ai = (string)$ai; 
		$format = (string)$format; 
		$zero = (string)'0'; 
		// to get the common types
		$zero = $zero[0]; 
		
		$ail = strlen($ai)-1; 
		
		$YYYY = (string)date('Y', $created_on); 
		
		
		
		$yl = 3; 
		
		$mm = date('m', $created_on); 
		$mm = (string)$mm; 
		
		$dd = date('d', $created_on); 
		$dd = (string)$dd; 
		
		// lengths of other stuff minus one:  
		$all = array(); 
		$all_data = array(); 
		
		$all['m'] = 1; 
		$all['d'] = 1; 
		
		$txt = '-'; 
		$txt = $txt[0]; 
		
		$delay = false; 
		
		if (!empty(self::$debug))
		{
			echo "<br />\nAI:".$ai."<br />\n"; 
			
		}
		
		
		for ($i=(strlen($format) -1);  $i>=0; $i--)
		{
			
			
			
			
			$c = (string)$format[$i]; 
			
			if (($delay) && ($c !== '{')) continue; 
			if (($delay) && ($c === '{')) 
				{
					$delay = false; 
					continue; 
				} 
			
			if ($c === 'n')
			{
				
				
			   if ($ail >= 0)
				$format[$i] = $ai[$ail];
			  else
			  {
				  $format[$i] = $zero;
			  }
			  $ail--; 
			  
			  
			}
			else
			if (($c === 'Y') || ($c==='y'))
			{
				if ($yl >= 0)
				{
				$format[$i] = $YYYY[$yl]; 
				}
				else
				{
					$format[$i] = $zero; 
				}
				$yl--; 
			}
			else
			if (($c === 'm') || ($c==='M'))
			{
				if ($all['m'] >= 0)
			    $format[$i] = $mm[$all['m']];
				else			
					$format[$i] = $zero;
				$all['m']--; 
			
			}
			else
			if (($c === 'd') || ($c === 'D'))
			{
				if ($all['d'] >= 0)
			    $format[$i] = $dd[$all['d']];
				else			
					$format[$i] = $zero;
				$all['d']--; 
			
			}
			else
			if ($c === '}')
			{
				$delay = true; 
			}
			else
			if ($c === '{')
			{
				$delay = false; 
			}
			else if ($c === 'Q') {
				$format[$i] = chr(rand(65,90));
			}
			else if ($c === 'R') {
				$format[$i] = rand(0,9);
			}
			else if ($c === 'q') {
				$format[$i] = chr(rand(97,122));
			}
			else
			{
				
				
				
				if (!isset($all[$c]))
				{
					$all_data[$c] = @date($c, $created_on); 
					
					if (empty($all_data[$c])) continue; 
					
					$all[$c] = strlen($all_data[$c]) -1; 
					
				}
				
					if ($all[$c] >= 0)
					$format[$i] = $all_data[$c][$all[$c]]; 
				    else
				    {
					 
					 $format[$i] = $txt; 
				    }
					$all[$c]--; 

			}
			
			
			
			
		}
		
		
		
		$s = array('{', '}'); 
		$r = array('', ''); 
		$format = str_replace($s, $r, $format); 
		
		
		return $format; 
	}
	

	
	public static function getNextAI($agenda_id, $from_time=0, $to_time=0)
	{
		if (!empty(self::$debug))
		{
			echo "<br />".'from time: '.date(DATE_ATOM, $from_time)."<br />";
			echo "<br />".'to time: '.date(DATE_ATOM, $to_time)."<br />";
			
		}
			
		// if no to time, than add 24 hours just for the timezones...
		if (empty($to_time)) $to_time = time()+60*60*24; 
		$db = JFactory::getDBO(); 
		
		/*
		//$q = 'select `ai`, `result` from #__onepage_numbering where `ref_idagenda` = '.(int)$agenda_id.' and `ref_idtype` = -1 and `created` >= '.$from_time.' and `created` <= '.$to_time.' order by ai desc limit 0,1';
		
		$q = 'select `ai`, `result` from #__onepage_numbering where `ref_idagenda` = '.(int)$agenda_id.' and `created` >= '.$from_time.' and `created` <= '.$to_time.' order by ai desc limit 0,1';
		
		if (!empty(self::$debug)) echo $q; 
		
		$db->setQuery($q); 
		$res = $db->loadAssoc(); 
		if (!empty($res))
		{
			
			return $res; 
		}
		*/
		
		 
		 $agenda = self::getAgendaConfig($agenda_id); 
		 if ($agenda === false) return; 
		 
		 if ($agenda_id == -1) {
		 
				$next_order_id = self::getNextOrderId(); 
				$ai = $next_order_id; 
				
				$ret = array(); 
				$ret['ai'] =  $next_order_id; 
				$ret['result'] = ''; 
			
			
				return $ret; 
				
				 
		 }
		 
		 // new column that was added to support lower AI: 
		 
		 
		
		$q = 'select `ai`, `result` from #__onepage_numbering where `ref_idagenda` = '.(int)$agenda_id.' and `created` >= '.$from_time.' and `created` <= '.$to_time.' and `created` >= '.(int)$agenda['changed'].' order by `ai` desc limit 0,1';
		$db->setQuery($q); 
		$res = $db->loadAssoc(); 
		
		
		if (!empty(self::$debug)) echo $q; 
		if (empty($res))
		{
			$ret = array(); 
			$ret['ai'] = 1; 
			$ret['result'] = ''; 
			
			
			return $ret; 
		}
		else
		if (!empty($res)) 
		{
			$ret = array(); 
			$ret['ai'] = (int)$res['ai']; 
			$ret['ai']++; 
			$ret['result'] = $res['result']; 
			return $ret; 
		}
		return; 
	}
	
	public static function getNextOrderId() {
				$db = JFactory::getDBO(); 
				$q = "show table status WHERE name = '".$db->getPrefix()."virtuemart_orders' ";
				$db->setQuery($q);
				$tableStatus = $db->loadAssoc(); 
				if (!empty($tableStatus)) {
				$next_order_id = (int)$tableStatus['Auto_increment']; 
				return $next_order_id; 
				}
				return 0; 
	}
	
	// returns the known ordering that does not depend on type_id (i.e. can be shared among various exports...)
	public static function getRelevantLine($agenda_id, $type)
	{
		$q = 'select `result`, `ref_idtype`, `ai` from #__onepage_numbering where ref_idagenda = '.(int)$agenda_id.' and ref_type = '.(int)$type.' limit 0,1'; 
		$db = JFactory::getDBO(); 
		$db->setQuery($q); 
		$result = $db->loadAssoc(); 
		if (!empty($result)) 
		{
			$ret = array(); 
			$ret['type_id'] = (int)$result['ref_idtype']; 
			$ret['result'] = (string)$result['result']; 
			$ret['ai'] = (int)$result['ai']; 
			return $ret; 
		}
		return false; 
	}
	
	public static function getLine($agenda_id, $type_id, $type)
	{
		//$q = 'select `result`, `ref_idtype`, `ai` from #__onepage_numbering where ref_idagenda = '.(int)$agenda_id.' and ref_idtype='.(int)$type_id.' and ref_type = '.(int)$type.' limit 0,1'; 
		$q = 'select `result`, `ref_idtype`, `ai` from #__onepage_numbering where ref_idagenda = '.(int)$agenda_id.' and ref_type = '.(int)$type.' order by `created` desc limit 0,1'; 
		$db = JFactory::getDBO(); 
		$db->setQuery($q); 
		$result = $db->loadAssoc(); 
		
		
		
		
		
		$e = $db->getErrorMsg(); if (!empty($e)) return array(); 
		
		
		if (!empty($result)) 
		{
			$ret = array(); 
			$ret['type_id'] = (int)$result['ref_idtype']; 
			$ret['result'] = (string)$result['result']; 
			$ret['ai'] = (int)$result['ai']; 
			
			
			
			return $ret; 
		}
		
		$result2 = self::getRelevantLine($agenda_id, $type); 
		if (!empty($result2)) return $result2; 
		
		return false; 
	}
	
	
	
	public static function getAgendaConfig($agenda_id)
	{
		
		
		static $agenda; 
		if (isset($agenda[$agenda_id])) return $agenda[$agenda_id]; 
		
		if ($agenda_id == -1) {
			 $agenda[$agenda_id] = array(); 
			 $agenda[$agenda_id]['id'] = -1; 
			 $agenda[$agenda_id]['changed'] = time(); 
			 $agenda[$agenda_id]['reseton'] = 4; 
			 $agenda[$agenda_id]['format'] = 'nnnnn'; 
			 return $agenda[$agenda_id]; 
		}
		
		$q = 'select * from #__onepage_agendas where id = '.(int)$agenda_id.' limit 0,1'; 
		$db = JFactory::getDBO(); 
		$db->setQuery($q); 
		$result = $db->loadAssoc(); 
		if (!empty($result)) {
			if (!isset($result['changed'])) $result['changed'] = 0; 
			$agenda[$agenda_id] = (array)$result; 
			return $agenda[$agenda_id]; 
		}
		
		return false; 
	}
	
	
	
 
}