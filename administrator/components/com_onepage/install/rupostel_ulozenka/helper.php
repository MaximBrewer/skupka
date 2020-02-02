<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

class UlozenkaHelper {
  public static $lastError; 


  public static function getPobockaDetails(&$method, $id, $cache=true)
  {
	  $key = 'getPobockaDetails_'.$id; 
	  $test = self::getCache($key); 
	  if (!empty($test)) return $test['data']; 
	   
	  require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'api.php'); 
		  $request = new ulozenkaApi($method);
		  $data = $request->getPobockaDetails($id); 
		  $data_json = json_decode($data, true); 
	  
	  if (!empty($data_json))
	  {
	  $e = $data_json['errors']; 
	  if (empty($e))
	  {
	  self::writeCache($key, $data);
	  }
	  else
	  {
		  foreach ($e as $k=>$msg)
		  {
			  if (!empty($msg['description']))
			  JFactory::getMessage()->enqueueMessage($msg['description']); 
			   
		  }
		  return array(); 
	  }
	  
	   $data_pobocka = (array)$data_json['data']; 
		  
		  return $data_pobocka; 
	  
	  }
	  return array(); 
		 
  }
  
  public static function getPobockyByServiceId($id, $method)
  {
	  $key = 'getPobockyByServiceId_'.$id; 
	  $test = self::getCache($key); 
	  
	  if (!empty($test)) return $test['data']; 
	  
	  require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'api.php'); 
	  $request = new ulozenkaApi($method);
	  $data = $request->getPobockyByServiceId($id); 
	  
	  
	  
	  $data_json = json_decode($data, true); 
	  
	  if (!empty($data_json))
	  {
	  $e = $data_json['errors']; 
	  if (empty($e))
	  {
	  self::writeCache($key, $data);
	  }
	  else
	  {
		  foreach ($e as $k=>$msg)
		  {
			  if (!empty($msg['description']))
			  JFactory::getMessage()->enqueueMessage($msg['description']); 
		  }
	  }
	  return $data_json['data']; 
	  }
	  return array(); 
	  
	  
  }
  public static function getServices(&$method)
  {
	  $key = 'getServices'; 
	  $test = self::getCache($key); 
	  
	  if (!empty($test)) return $test['data']; 
	  
	  
	  require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'api.php'); 
	  $request = new ulozenkaApi($method);
	  $data = $request->getServices(); 
	  
	  
	  
	  $data_json = json_decode($data, true); 
	  if (!empty($data_json))
	  {
	  $e = $data_json['errors']; 
	  if (empty($e))
	  {
	  self::writeCache($key, $data);
	  }
	  else
	  {
		  foreach ($e as $k=>$msg)
		  {
			  if (!empty($msg['description']))
			  JFactory::getMessage()->enqueueMessage($msg['description']); 
		  }
	  }
	  return $data_json['data']; 
	  }
	  
	  return array(); 
	  
	  
  }
  
  private static function getCache($hash)
  {
	  jimport('joomla.filesystem.file');
	  $hash = JFile::makeSafe($hash); 
	  if (empty($hash)) return ''; 
	  
	   
	   jimport('joomla.filesystem.folder');
	   
	   if (!file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'ulozenka'))
		 {
		   if (@JFolder::create(JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'ulozenka')===false) 
		   {
			
		   }
		 }
		 
		$ts_filename = JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'ulozenka'.DIRECTORY_SEPARATOR.'timestamp.txt';
		$filename = JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'ulozenka'.DIRECTORY_SEPARATOR.$hash.'.json';
		
		if (file_exists($filename))
		{
		$data = file_get_contents($filename); 
		$datat = json_decode($data, true); 
	  
	  if (!empty($datat))
	  {
	  $e = $datat['errors']; 
	  if (!empty($e))
	  {
		  self::clearCache($hash); 
		  return ''; 
	  }
	  }
		
		if (!empty($datat))
		return $datat; 
		
		}
		
		return ''; 
	
		
	   
  }
  public static function clearCache($hash)
  {
	  jimport('joomla.filesystem.file');
	   $hash = JFile::makeSafe($hash); 
	  if (empty($hash)) return ''; 
	  
	$filename = JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'ulozenka'.DIRECTORY_SEPARATOR.$hash.'.json';
	  if (file_exists($filename))
	  {
		  @JFile::delete($filename); 
	  }
	  //JFile::delete($ts_filename);
	  
	  
  }
  
  private static function writeCache($hash, $data)
  {
	  if (empty($data)) return; 
	  
	  $time = time(); 
	  			 jimport('joomla.filesystem.file');
	   jimport('joomla.filesystem.folder');
	   
	    $hash = JFile::makeSafe($hash); 
	  if (empty($hash)) return ''; 
	   
	   if (!file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'ulozenka'))
		 {
		   if (@JFolder::create(JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'ulozenka')===false) 
		   {
			
		   }
		 }
		 
		$ts_filename = JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'ulozenka'.DIRECTORY_SEPARATOR.'timestamp.txt';
		$filename = JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'ulozenka'.DIRECTORY_SEPARATOR.$hash.'.json';
		
		
				if (!empty($data))
				 {
				  JFile::write($filename.'.tmp', $data);
				  JFile::move($filename.'.tmp', $filename); 
				  JFile::write($ts_filename.'.tmp', $time);
				  JFile::move($ts_filename.'.tmp', $ts_filename); 
				  
				 }

  }
  
  
  public static function &getPobocky(&$params, $cache=true)
   {
	  
       if (isset(self::$pobocky_cache)) return self::$pobocky_cache; 
	   
       jimport('joomla.filesystem.file');
	   jimport('joomla.filesystem.folder');
		
		$retfalse = new stdClass(); 
	
		$document = JFactory::getDocument(); 
		
		if (isset($params->cache))
			if (empty($params->cache))
				$cache = false; 
		
		
		
		
		if (!$cache)
		{
		  require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'api.php'); 
		  $request = new ulozenkaApi($params);
		  $data = $request->getPobocky(); 
		  $data_json = @json_decode($data, true); 
		  
		 
		}
		if (($cache) || (empty($data_json)))
		{
			
			$cache_test = self::getCache('pobocky'); 
		 
			if (empty($cache_test))
			{
				
				
			     require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'api.php'); 
	 
			     $request = new ulozenkaApi($params);
				 $data = $request->getPobocky(); 
				 
				
				 self::writeCache('pobocky', $data); 
				 $data_json = json_decode($data, true); 
				
			}	 
			else
			{
				$data_json = $cache_test; 
			
				
			}
				 
			
		
		         
				
				 

		}

		

		
		if ($cache)
		{
		
		if (empty($data_json))
		 {
		   self::clearCache('pobocky'); 
		   
		    $cache = -1; 
		 }
		 }
		 
		 if (empty($cache) || ($cache === -1))
		 {
		  
		  
		   if (empty($data_json))
		   {
			     require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'api.php'); 
			     $request = new ulozenkaApi($params);
				 $data = $request->getPobocky(); 
				 self::writeCache('pobocky', $data); 
				 $data_json = json_decode($data, true); 
				 
				 
		   }
		   
		 }
		 
		  
		
		 
		 if (empty($data_json)) 
		 {
			 $xml = new stdClass(); 
		    return $xml; 
		 }
		 
		 if (!empty($data_json['errors']))
		 {
			 $e = $data_json['errors']; 
			 foreach ($e as $k=>$msg)
		     {
			  if (!empty($msg['description']))
			  JFactory::getMessage()->enqueueMessage($msg['description']); 
			
			  self::clearCache('pobocky'); 
			  return $retfalse; 
		      }
		 }
		 
		
		 
		 $copy = new stdClass(); 
		 $copy->pobocky = array(); 
		 $copy->branch = $data_json; 
		 if (isset($request->error)) $copy->error = (string)$request->error; 
		  
		  
		 if (isset($data_json['data']))
			 $pobocky = $data_json['data']; 
		 else $pobocky = $data_json; 
		 
		 if (isset($pobocky))
		 {
		 $pobocky = (array)$pobocky; 
		 
		 
		 foreach ($pobocky as $p)
		  {
			$p = (object)$p; 
		    self::br2p($copy->pobocky, $p); 
		  }
		  
		 
		   self::$pobocky_cache = $copy; 
		   
	
		   
		   self::$pobocky_cache = $copy; 
		   
		   
           return $copy; 		  
		  
		 }
		 
		 
		 
		
		  
		 // if (isset($xml->body)) $copy->body = (string)$xml->error; 
		 /*
		 
		 
		 if (isset($xml->pobocky)) {
			if (count($xml->pobocky)) {
			   foreach ($xml->pobocky as $pobocka)
			    {
				  $newpobocka = new stdClass(); 
				  $ac = (array)$pobocka; 
				  foreach ($ac as $key=>$val)
				   {
				     $newpobocka->$key = $val; 
				   }
				  $copy->pobocky[] = $newpobocka; 
				}
			 if (isset($xml->error)) $copy->error = (string)$xml->error; 
			 if (isset($xml->body)) $copy->body = (string)$xml->error; 
			 self::$pobocky_cache = $copy; 
			 if ($cache)
			 //self::saveObj($copy); 
			 return $copy; 
			}
			}
	   
		 self::$pobocky_cache = $xml; 
		 return $xml; 
		*/ 
		
   }
   private static function saveObj($obj)
    {
	  return; 
	   $obj = (array)$obj; 
	   unset($obj['branch']); 
	  
	   jimport('joomla.filesystem.file');
	   jimport('joomla.filesystem.folder');
		
		
		
		if (!file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'ulozenka'))
		 {
		   if (@JFolder::create(JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'ulozenka')===false) return $retfalse; 
		 }
		 
	   $file = JPATH_CACHE.DIRECTORY_SEPARATOR.'ulozenka'.DIRECTORY_SEPARATOR.'ulozenkaobj.php'; 
	   $data = '<?php 
	   if( !defined( \'_VALID_MOS\' ) && !defined( \'_JEXEC\' ) ) die( \'Direct Access to \'.basename(__FILE__).\' is not allowed.\' ); 
	   $retObj = '.var_export($obj, true).';
	   '; 
       @JFile::write($file, $data); 
	}
   private static function convertXml($xml)
    {
	
	}
	
	private static function br2p(&$copy, $p)
	 {
		 
		 
	    $np = new stdClass(); 
		if (isset($p->id))
		$np->id = (int)$p->id; 
		if (!isset($p->active)) $p->active = 1; 
		$np->aktiv = (int)$p->active; 
		
		if (empty($np->aktiv)) return; 
		
		$np->zkratka = (string)$p->shortcut; 
		$np->nazev = (string)$p->name; 
		
		if (!isset($p->phone)) $p->phone = ''; 
		$np->telefon = (string)$p->phone; 
		
		if (!isset($p->email)) $p->email = ''; 
		$np->email = (string)$p->email; 
		$np->obec = (string)$p->town; 
		$np->psc = (string)$p->zip; 
		$np->gsm = (string)$p->phone;
		if (isset($p->link))
		{
		$np->odkaz = (string)$p->link; 
		}
		else
		{
			$np->odkaz = $p->_links['website']['href']; 
		}
		
	
		if (isset($p->houseNumber))
		$np->ulice = (string)$p->street.' '.(string)$p->houseNumber; 
		else 
		$np->ulice = (string)$p->street.' '.(string)$p->house_number ; 
		if (isset($p->picture))
		$np->obrazek = (string)$p->picture; 
	    else
	    {
		 $np->obrazek = $p->_links['picture']['href'];
	    } 
		
		if (!empty($np->obrazek)) {
		  $np->obrazek = str_replace('http://', 'https://', $np->obrazek); 
		}
		
		if (isset($p->map))
		$np->mapa = (string)$p->map; 
	    else 
		$np->mapa = $np->odkaz = $p->_links['website']['href']; 
	
	    if (!empty($np->mapa)) {
		  $np->mapa = str_replace('http://', 'https://', $np->mapa); 
		}
	
		$np->skype = ''; 
		
		if (isset($p->latitude))
		$np->gps = $p->latitude.' '.$p->longtitude; 
		else
		{
			if (!isset($p->gps))
			{
				$p->gps = array(); 
				$p->gps['latitude'] = 0; 
				$p->gps['longitude'] = 0; 
			}
			$np->gps = $p->gps['latitude'].' '.$p->gps['longitude'];
		}
		
		if ($p->country == 'SVK')
		$np->sk = 1; 
		else $np->sk = 0; 
		
		$np->provoz = ''; 
		$np->provoz_full = ''; 
		
		$odays = array(); 
		
		if (isset($p->openingHours))
		foreach ($p->openingHours as $item)
		 {
		    if (isset($item->regular))
			 foreach ($item->regular as $i2)
			  {
			    
			    //$np->provoz = $i2->hours->open.' '.$i2->hours->close; 
				$days = array('MON'=>'monday', 'TUE'=>'tuesday', 'WED'=>'wednesday', 'THU'=>'thursday', 'FRI'=>'friday', 'SAT'=>'saturday', 'SUN'=>'sunday'); 
				foreach ($days as $keyd=>$day)
				{
				
				 if (isset($i2->$day))
				 if (isset($i2->$day->hours))
				 {
				  
				  $np->provoz_full .= JText::_($keyd).': '.$i2->$day->hours->open.' - '.$i2->$day->hours->close."<br />\n"; 
				 }
				}
			  }
			
			  
		 }
		 $np->provoz = $np->provoz_full; 
		 
		 if (!isset($p->prices))
		 {
			 $p->prices = new stdClass(); 
			 $p->prices->price = new stdClass(); 
			 $p->prices->price->parcel = 0; 
			 $p->prices->price->cashOnDelivery = 0; 
			 $p->prices->price->currency = 'CZK'; 
		 }
		 if (is_array($p->prices))
		 $x = (object)reset($p->prices); 
	     else
	     {
		   $x = new stdClass(); 
		   $x->cash_on_delivery = 0; 
		  
		 }
	 
		
		 
		 if (is_object($p->prices) && (isset($p->prices->price)))
		 {
		 $np->prices = new stdClass(); 
		 $np->prices->parcel = (string)$p->prices->price->parcel; 
		 $np->prices->cashOnDelivery = (string)$p->prices->price->cashOnDelivery; 
		 $np->prices->currency = (string)$p->prices->price->currency; 
		 }
		 else
		 {
			  $np->prices = $x; 
		      $np->prices->cashOnDelivery  = $x->cash_on_delivery; 
			  
		 }
		// new
		$np->country = (string)$p->country; 
		$np->partner = (int)$p->partner;
		$copy[] = $np; 
		
	/*
	"id"]=>
  string(1) "6"
  ["active"]=>
  string(1) "1"
  ["shortcut"]=>
  string(5) "brno2"
  ["name"]=>
  string(25) "Brno, Èernopolní 54/245"
  ["phone"]=>
  string(13) "+420777208204"
  ["email"]=>
  string(16) "info@ulozenka.cz"
  ["street"]=>
  string(12) "Èernopolní"
  ["houseNumber"]=>
  string(6) "54/245"
  ["town"]=>
  string(19) "Brno - Èerná Pole"
  ["zip"]=>
  string(5) "61300"
  ["district"]=>
  object(JXMLElement)#230 (3) {
    ["id"]=>
    string(2) "11"
    ["nutsNumber"]=>
    string(5) "CZ064"
    ["name"]=>
    string(18) "Jihomoravský kraj"
  }
  ["country"]=>
  string(3) "CZE"
  ["link"]=>
  string(55) "http://www.ulozenka.cz/pobocky/6/brno-cernopolni-54-245"
  ["openingHours"]=>
  object(JXMLElement)#232 (2) {
    ["regular"]=>
    object(JXMLElement)#227 (7) {
      ["monday"]=>
      object(JXMLElement)#224 (1) {
        ["hours"]=>
        object(JXMLElement)#188 (2) {
          ["open"]=>
          string(5) "11:00"
          ["close"]=>
          string(5) "19:00"
        }
      }
      ["tuesday"]=>
      object(JXMLElement)#196 (1) {
        ["hours"]=>
        object(JXMLElement)#188 (2) {
          ["open"]=>
          string(5) "11:00"
          ["close"]=>
          string(5) "19:00"
        }
      }
      ["wednesday"]=>
      object(JXMLElement)#198 (1) {
        ["hours"]=>
        object(JXMLElement)#188 (2) {
          ["open"]=>
          string(5) "11:00"
          ["close"]=>
          string(5) "19:00"
        }
      }
      ["thursday"]=>
      object(JXMLElement)#191 (1) {
        ["hours"]=>
        object(JXMLElement)#188 (2) {
          ["open"]=>
          string(5) "11:00"
          ["close"]=>
          string(5) "19:00"
        }
      }
      ["friday"]=>
      object(JXMLElement)#184 (1) {
        ["hours"]=>
        object(JXMLElement)#188 (2) {
          ["open"]=>
          string(5) "11:00"
          ["close"]=>
          string(5) "19:00"
        }
      }
      ["saturday"]=>
      object(JXMLElement)#183 (0) {
      }
      ["sunday"]=>
      object(JXMLElement)#192 (0) {
      }
    }
    ["exceptions"]=>
    object(JXMLElement)#226 (0) {
    }
  }
  ["picture"]=>
  string(41) "http://www.ulozenka.cz/cdn/branches/6.jpg"
  ["gps"]=>
  object(JXMLElement)#231 (2) {
    ["latitude"]=>
    string(9) "49.208607"
    ["longitude"]=>
    string(9) "16.614868"
  }
  ["prices"]=>
  object(JXMLElement)#225 (1) {
    ["price"]=>
    object(JXMLElement)#226 (3) {
      ["parcel"]=>
      string(2) "29"
      ["cashOnDelivery"]=>
      string(2) "12"
      ["currency"]=>
      string(3) "CZK"
    }
  }
  ["partner"]=>
  string(1) "0"
  ["preparing"]=>
  string(1) "0"
  ["navigation"]=>
  object(JXMLElement)#229 (3) {
    ["general"]=>
    string(130) "Poboèka se nachází v ulici Èernopolní 54/245 naproti Mendelovì univerzitì v obytném domì mezi lahùdkami a restaurací."
    ["car"]=>
    string(128) "Smìrem z centra ulicí Drobného, poté odboèit do ulice Erbenova a napojit se na Èernopolní. Poboèka je po levé stranì."
    ["publicTransport"]=>
    string(125) "<ul>
<li>Bus: 67 - zast. Schodová (cca 250 metrù)</li>
<li>Tram: 9, 11 – zast. Zemìdìlská (cca 300 metrù).</li>
</ul>"
  }
  ["otherInfo"]=>
  string(228) "<ul>
<li>doba úschovy zásilky na poboèce - 7 dnù</li>
<li>možnost prodloužení termínu úschovy o 3 dny resp. až 7 dnù</li>
<li>platba kartou možná  (pokud to eshop povoluje)</li>
<li>MHD, parkování autem</li>
</ul>"
  ["transport"]=>
  object(JXMLElement)#228 (3) {
    ["id"]=>
    string(1) "1"
    ["name"]=>
    string(9) "Uloženka"
    ["alias"]=>
    string(8) "ulozenka"
  }
  ["map"]=>
  string(912) "<iframe width="600" height="400" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.cz/maps?f=q&source=embed&hl=cs&geocode=&q=%C4%8Cernopoln%C3%AD+54,+Brno-%C4%8Cern%C3%A1+Pole&aq=0&oq=%C4%8Dernopoln%C3%AD+54&sll=49.930008,15.369873&sspn=5.885092,14.27124&brcurrent=5,0,0&num=10&ie=UTF8&hq=&hnear=%C4%8Cernopoln%C3%AD+245%2F54,+613+00+Brno-%C4%8Cern%C3%A1+Pole&t=m&ll=49.213785,16.621199&spn=0.022427,0.051413&z=14&output=embed"></iframe><br /><small><a href="http://maps.google.cz/maps?f=q&source=embed&hl=cs&geocode=&q=%C4%8Cernopoln%C3%AD+54,+Brno-%C4%8Cern%C3%A1+Pole&aq=0&oq=%C4%8Dernopoln%C3%AD+54&sll=49.930008,15.369873&sspn=5.885092,14.27124&brcurrent=5,0,0&num=10&ie=UTF8&hq=&hnear=%C4%8Cernopoln%C3%AD+245%2F54,+613+00+Brno-%C4%8Cern%C3%A1+Pole&t=m&ll=49.213785,16.621199&spn=0.022427,0.051413&z=14" style="color:#0000FF;text-align:left">Zvìtšit mapu</a></small>"
  ["destination"]=>
  string(1) "1"
  ["register"]=>
  string(1) "1"
  */
	
		
	 }

	 
   public static $pobocky_cache; 
   public static function getDataPobocky(&$params, $id)
   {
     $ret = new stdClass(); 
	 if (empty(self::$pobocky_cache))
	 {
     $pobocky = self::getPobocky($params); 
	 self::$pobocky_cache = $pobocky; 
	 }
	 else $pobocky = self::$pobocky_cache; 
	 
	 if (!empty($pobocky->pobocky))
	 foreach ($pobocky->pobocky as $p)
	  {
	     if ($p->id == $id) 
		 {
		 $arr = (array)$p; 
		 foreach ($arr as $key=>$val)
		  {
		    $ret->$key = $val; 
		  }
		
		 return $ret;   
		 }
	  }
	  
	  $pobocka = self::getPobockaDetails($params, $id); 
	  
	  
	  $zz = array(); 
	  if (!empty($pobocka))
	  if (!empty($pobocka[0]))
	  {
	    $pobocka[0] = (object)$pobocka[0]; 
	    self::br2p($zz, $pobocka[0]); 
		if (!empty($zz))
		{
			return reset($zz); 
		}
	  }
	  
	  
	  return $ret; 
   }
   	/**
	* download new page from the internet through https (using fsockopen)
	* @url URL of bank payments to be downloaded and returned
	* @return result of downloaded page
	*/
	public static function fetchURL( $url ) {
		$config = JFactory::getConfig();

		$url_parsed = parse_url($url);
		$host = $url_parsed["host"];
		$port = $url_parsed["port"];
		
		switch ($url_parsed['scheme']) {
			case 'https':
				$scheme = 'ssl://';
				$port = 443;
				break;
			case 'http':
			default:
				$scheme = '';
				$port = 80;    
		} 
		$path = $url_parsed["path"];
		if ($url_parsed["query"] != "")
			$path .= "?".$url_parsed["query"];

		if ($url_parsed['user']) {
			$authorization = "Authorization: Basic ".base64_encode($url_parsed['user'].':'.$url_parsed['pass'])."\r\n";
		} else {
			$authorization = '';
		}

		$out = "GET $path HTTP/1.0\r\nHost: $host\r\n$authorization\r\n";

		$fp = fsockopen($scheme . $host, $port, $errno, $errstr, 30);

		fwrite($fp, $out);
		if (method_exists($config, 'getValue'))
		$tmpfname = $config->getValue('config.tmp_path').DIRECTORY_SEPARATOR."pi_".time();
		else 
		$tmpfname = $config->get('tmp_path').DIRECTORY_SEPARATOR."pi_".time();
		
		$handle = fopen($tmpfname, "w");

		$body = false;
		$i=0;
		while (!feof($fp)) {
			$s = fgets($fp, 1024);
			if ( $body ) {
				//$in .= $s;
				fwrite($handle, $s);
			} else {
				if (eregi("^HTTP.*404", $s)) {
					fclose($handle);
					fclose($fp);
					unlink($tmpfname);
					return false;
				}
			}
			if ( $s == "\r\n" ) {
				$body = true;
			}
			$i++;
		}
		
		fclose($handle);
		fclose($fp);
		
		return $tmpfname;
	}

	public static function getVendorCurrency() {
		$cart = VirtuemartCart::getCart(); 
		
		if (!empty($cart->vendorId))
		$vendorId = $cart->vendorId; 
		else $vendorId = 1; 	
		
		if (empty($vendorId)) $vendorId = 1; 
		$db = JFactory::getDBO(); 	
		$q  = 'SELECT  `vendor_currency` FROM `#__virtuemart_vendors` WHERE `virtuemart_vendor_id` = '.(int)$vendorId.' limit 0,1';
		$db->setQuery($q);
		$virtuemart_currency_id = $db->loadResult();
		$virtuemart_currency_id = (int)$virtuemart_currency_id; 
		
$q = 'select `currency_code_3` from `#__virtuemart_currencies` where `virtuemart_currency_id` = '.$virtuemart_currency_id; 
	   $db->setQuery($q); 
	   $vendor_currency_code_3 = $db->loadResult(); 
		return $vendor_currency_code_3; 
	}
	// from cur1 to cur2 
	// kopia z opc mini.php
 public static function convertPrice($price, $cur1, $cur2) {
		
		if (($cur1 === $cur2) || (empty($cur1)) || (empty($cur2))) return $price; 
		
		if ((!is_numeric($cur1)) && (strlen($cur1)==3)) {
	   $db = JFactory::getDBO(); 
	   $q = 'select `virtuemart_currency_id` from `#__virtuemart_currencies` where `currency_code_3` = '."'".$db->escape($cur1)."'".' limit 0,1'; 
	   $db->setQuery($q); 
	   $cidI = $db->loadResult(); 
	   $cur1 = (int)$cidI; 
	   if (empty($cidI)) return $price; 
   }
   if ((!is_numeric($cur2)) && (strlen($cur2)==3)) {
	   $db = JFactory::getDBO(); 
	   $q = 'select `virtuemart_currency_id` from `#__virtuemart_currencies` where `currency_code_3` = '."'".$db->escape($cur2)."'".' limit 0,1'; 
	   $db->setQuery($q); 
	   $cidI = $db->loadResult(); 
	   $cur2 = (int)$cidI; 
	   if (empty($cidI)) return $price; 
   }
		
		
	 if (!class_exists('VmConfig'))	  
	 {
	  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	  VmConfig::loadConfig(); 
	 }
		
		static $cC; 
		static $rate; 
		
		
		if (empty($cC)) {
		$converterFile  = VmConfig::get('currency_converter_module','convertECB.php');

		if (file_exists( JPATH_ADMINISTRATOR.DS.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'plugins'.DS.'currency_converter'.DIRECTORY_SEPARATOR.$converterFile ) and !is_dir(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'currency_converter'.DIRECTORY_SEPARATOR.$converterFile)) {
			$module_filename=substr($converterFile, 0, -4);
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DS.'plugins'.DS.'currency_converter'.DS.$converterFile);
			if( class_exists( $module_filename )) {
				$cC = new $module_filename();
			}
		} else {

			if(!class_exists('convertECB')) require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'currency_converter'.DIRECTORY_SEPARATOR.'convertECB.php');
			$cC = new convertECB();

		}
		}
		
		$c1o = self::getCurInfo($cur1); 
		$c2o = self::getCurInfo($cur2); 
		
		$priceC = (float)$price; 
		if (empty($priceC)) return $price; 
		
		if (!isset($rate[$cur1.'_'.$cur2]))
		if ((method_exists($cC, 'convert'))) {
		  
		  $multi = PHP_INT_MAX;
		  try {
		   $rateZ = $cC->convert( PHP_INT_MAX, $c1o['currency_code_3'], $c2o['currency_code_3']);
		  }
		  catch (Exception $e) {
		    $rateZ = 1; 
		  }
		  $rate[$cur1.'_'.$cur2] = $rateZ / PHP_INT_MAX; 
		}
		else
		{
			$rate[$cur1.'_'.$cur2] = 1; 
		}
		
		$priceC = $price * $rate[$cur1.'_'.$cur2]; 
		return $priceC; 
		
		
 }
 public static function getCurInfo($currency)
   {
	   static $c; 
	   static $c2; // always vendor currency
	   $currency = (int)$currency; 
	   $db = JFactory::getDBO();
	   if (empty($currency)) {
		if (empty($c2)) {
	    
		if (!class_exists('VmConfig'))	  
		{
		 require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		 VmConfig::loadConfig(); 
		}
		
		
			if (!class_exists('VirtueMartCart'))
			require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'cart.php');
			$cart = VirtuemartCart::getCart(); 
		
			   if (defined('VM_VERSION') && (VM_VERSION >= 3))
			   {
				    if (method_exists($cart, 'prepareCartData')) {
						ob_start(); 
				     $cart->prepareCartData(); 
					 $zz = ob_get_clean(); 
					}
			   }
		
		// first take the cart currency: 
		if (!empty($cart->pricesCurrency)) {
			$currency  = $c2 = $cart->pricesCurrency; 
		}
		else {
			
		// secnd take the vendors currency: 
		if (!empty($cart->vendorId))
		$vendorId = $cart->vendorId; 
		else $vendorId = 1; 	
		
		if (empty($vendorId)) $vendorId = 1; 
			
		$q  = 'SELECT  `vendor_currency` FROM `#__virtuemart_vendors` WHERE `virtuemart_vendor_id`='.$vendorId;
		$db->setQuery($q);
		$vendor_currency = $db->loadResult();
		$c2 = $vendor_currency; 
		
		
		  
		}
		}
		else
		{
			$currency = $c2; 
		}
	   }
	   
	   
	   if (isset($c[$currency])) return $c[$currency]; 
	    
	   $q = 'select * from #__virtuemart_currencies where virtuemart_currency_id = '.(int)$currency.' limit 0,1'; 
	   $db->setQuery($q); 
	   $res = $db->loadAssoc(); 
	   if (empty($res)) {
	 
	
	   $res = array(); 
	   $res['currency_symbol'] = '$'; 
	   $res['currency_decimal_place'] = 2; 
	   $res['currency_decimal_symbol'] = '.'; 
	   $res['currency_thousands'] = ' '; 
	   $res['currency_positive_style'] = '{number} {symbol}';
	   $res['currency_negative_style'] = '{sign}{number} {symbol}'; 
	   
	   
	   

	   
	   
	   }
	   $res = (array)$res; 
	   
	   $c[$currency] = $res; 
	   return $res; 
   }
 
   
   
}