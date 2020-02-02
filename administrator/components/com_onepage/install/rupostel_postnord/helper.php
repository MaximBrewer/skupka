<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

class postnordHelper {
  public static $lastError; 


  public static function getPobockaDetails(&$method, $id, $cache=true, $country='SE', $lang='en')
  {
	  $key = 'getPobockaDetails_'.$id.'_'.$country.'_'.$lang; 
	  if ($cache)
	  {
	   $test = self::getCache($key); 
	  }
	  if (!empty($test)) return $test; 
	   
	  require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'api.php'); 
		$request = new postnordApi($method);
		  
		   $data = $request->getPobockaDetails($id, $country, $lang);
		   
		   if ($data === false) return array(); 
		   
		  $data_json = json_decode($data, true); 
	  
	  if (!empty($data_json))
	  {
	  
	  
	  
	  self::writeCache($key, $data);
	  
	  
	  
	   $data_pobocka = (array)$data_json; 
		  
	   return $data_pobocka; 
	  
	  }
	  return array(); 
		 
  }
  
  
  
  private static function getCache($hash)
  {
	  jimport('joomla.filesystem.file');
	  $hash = JFile::makeSafe($hash); 
	  if (empty($hash)) return ''; 
	  
	   
	   jimport('joomla.filesystem.folder');
	   
	   if (!file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'postnord'))
		 {
		   if (@JFolder::create(JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'postnord')===false) 
		   {
			 return ''; 
		   }
		 }
		 
		
		$filename = JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'postnord'.DIRECTORY_SEPARATOR.$hash.'.json';
		
		if (file_exists($filename))
		{
		$data = file_get_contents($filename); 
		$datat = json_decode($data, true); 
	  
	  if (empty($datat))
	  {
		  self::clearCache($hash); 
		  return ''; 
	  
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
	  
	$filename = JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'postnord'.DIRECTORY_SEPARATOR.$hash.'.json';
	  if (file_exists($filename))
	  {
		  @JFile::delete($filename); 
	  }
	  //JFile::delete($ts_filename);
	  
	  
  }
  
  private static function writeCache($hash, $data)
  {
	  if (is_array($data)) $data = json_encode($data); 
	  if (is_object($data)) $data = json_encode($data); 
	  
	  if (empty($data)) return; 
	  
	  $time = time(); 
	  			 jimport('joomla.filesystem.file');
	   jimport('joomla.filesystem.folder');
	   
	    $hash = JFile::makeSafe($hash); 
	  if (empty($hash)) return ''; 
	   
	   if (!file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'postnord'))
		 {
		   if (@JFolder::create(JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'postnord')===false) 
		   {
			  return ''; 
		   }
		 }
		 
		
		$filename = JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'postnord'.DIRECTORY_SEPARATOR.$hash.'.json';
		
		
				if (!empty($data))
				 {
				  JFile::write($filename.'.tmp', $data);
				  JFile::move($filename.'.tmp', $filename); 
				 
				  
				 }

  }
  
  
  public static function getPobocky(&$params, $cache=true, $country='SE', $lang='sv', $zip='')
   {
	  
	 
	  
       if (isset(self::$pobocky_cache)) return self::$pobocky_cache; 
	   
	  $key = 'getPobocky_'.$country.'_'.$lang.'_'.$zip; 
	  if ($cache)
	  {
	   $test = self::getCache($key); 
	   if (!empty($test))
	   {
		   return $test; 
	   }
	  }
	   
	   		require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'api.php'); 
			$request = new postnordApi($params);

	   
       jimport('joomla.filesystem.file');
	   jimport('joomla.filesystem.folder');
		
		$retfalse = new stdClass(); 
	
		$document = JFactory::getDocument(); 
		
		if (isset($params->cache))
			if (empty($params->cache))
				$cache = false; 
		
		
		
		
		if (!$cache)
		{
		  
		  $data = $request->getPobocky($country, $lang, $zip); 
		  if (empty($data)) return false; 
		  $data_json = @json_decode($data, true); 
		  
		 
		}
		if (($cache) || (empty($data_json)))
		{
			
			$cache_test = self::getCache('pobocky'); 
		 
			if (empty($cache_test))
			{
				
				
			    
				 $data = $request->getPobocky($country, $lang, $zip); 
				 if (empty($data)) return false; 
				
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
			     
				 $data = $request->getPobocky($country, $lang, $zip); 
				 if (empty($data)) return false; 
				 self::writeCache('pobocky', $data); 
				 $data_json = json_decode($data, true); 
				 
				 
		   }
		   
		 } 
		 
		if (!isset($data_json['servicePointInformationResponse'])) return false; 
		if (empty($data_json['servicePointInformationResponse']['servicePoints'])) return false;
		
		$pobocky = $data_json['servicePointInformationResponse']['servicePoints']; 
		
		
		
		 
		 if (empty($data_json)) 
		 {
			 $xml = new stdClass(); 
		    return $xml; 
		 }
		 
		
		 
		
		 
		 $copy = new stdClass(); 
		 $copy->pobocky = array(); 
		 $copy->branch = $data_json; 
		 if (isset($request->error)) $copy->error = (string)$request->error; 
		  
		  
		 $pobocky = $data_json['servicePointInformationResponse']['servicePoints']; 
		 
		 foreach ($pobocky as $k => &$pobocka)
		 {
			 $id = $pobocka['servicePointId']; 
			 $data = self::getPobockaDetails($params, $id, $cache, $country, $lang); 
			 if ((!empty($data)) && (!empty($data['servicePointInformationResponse'])))
			 {
			  $nd = $data['servicePointInformationResponse']; 
			  $pobocka = array_merge($pobocka, $nd); 
			 }
		 }
		 
		 
		 if (isset($pobocky))
		 {
		 $pobocky = (array)$pobocky; 
		 
		 
		 foreach ($pobocky as $p)
		  {
			$p = (object)$p; 
		    self::br2p($copy->pobocky, $p); 
		  }
		  
		 
		   self::$pobocky_cache = $copy; 
		   
		   self::writeCache($key, $copy); 
		   
           	  
		  
		 }
		 return $copy; 	
		 
		 
		 
		
		  
		 
		
   }
   private static function saveObj($obj)
    {
	  return; 
	   $obj = (array)$obj; 
	   unset($obj['branch']); 
	  
	   jimport('joomla.filesystem.file');
	   jimport('joomla.filesystem.folder');
		
		
		
		if (!file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'postnord'))
		 {
		   if (@JFolder::create(JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'postnord')===false) return $retfalse; 
		 }
		 
	   $file = JPATH_CACHE.DIRECTORY_SEPARATOR.'postnord'.DIRECTORY_SEPARATOR.'postnordobj.php'; 
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
		if (isset($p->servicePointId))
		$np->id = (int)$p->servicePointId; 
	
		if (!isset($p->active)) $p->active = 1; 
		$np->aktiv = (int)$p->active; 
		
		if (empty($np->aktiv)) return; 
		
		$np->zkratka = (string)$p->name; 
		$np->nazev = (string)$p->name; 
		
		if (!empty($p->customerSupports))
		{
			foreach ($p->customerSupports as $k=>$v)
			{
				if (isset($v['customerSupportPhoneNo']))
				{
					$p->phone = $v['customerSupportPhoneNo']; 
					break; 
				}
			}
		}
		
		
		if (!isset($p->phone)) $p->phone = ''; 
		$np->telefon = (string)$p->phone; 
		
		if (!isset($p->email)) $p->email = ''; 
		$np->email = (string)$p->email; 
		$np->obec = (string)$p->deliveryAddress['city']; 
		$np->psc = (string)$p->deliveryAddress['postalCode']; 
		$np->gsm = (string)$p->phone;
		if (isset($p->link))
		{
		 $np->odkaz = ''; 
		}
		else
		{
			$np->odkaz = ''; 
		}
		
	
		
		$np->ulice = (string)$p->deliveryAddress['streetName'].' '.(string)$p->deliveryAddress['streetNumber']; 
		
		
		if (isset($p->picture))
		$np->obrazek = ''; 

		if (isset($p->map))
		$np->mapa = ''; 
	
		$np->skype = ''; 
		
		$np->gps  = ''; 
		
		
		
		$np->sk = 0; 
		
		$np->provoz = ''; 
		$np->provoz_full = ''; 
		
		$odays = array(); 
		
		if (isset($p->openingHours))
		foreach ($p->openingHours as $i2)
		 {
		    
			 
			   
			    
			    //$np->provoz = $i2->hours->open.' '.$i2->hours->close; 
				//$days = array('MON'=>'monday', 'TUE'=>'tuesday', 'WED'=>'wednesday', 'THU'=>'thursday', 'FRI'=>'friday', 'SAT'=>'saturday', 'SUN'=>'sunday'); 
				//foreach ($days as $keyd => $day)
				{
				
				 if (isset($i2['day']))
				 {
			      //$lw = strtolower($i2['day']); 
				  //$day = strtolower($day); 

				  //if ($lw === $day)
				  {
					$np->provoz_full .= '<div class="prvovoz" style="clear: both;">'.$i2['day'].': '.$i2['from1'].' - '.$i2['to1']."</div>\n";   
				  }
				  
				  
				 }
				}
			  
			
			  
		 }
		 $np->provoz = $np->provoz_full; 
		
		 $np->prices = new stdClass(); 
		 $np->prices->parcel = 0;
		 $np->prices->cashOnDelivery = 0; 
	 
		
		 

		$np->country = $p->deliveryAddress['countryCode']; 
		$np->partner = 0;
		$copy[] = $np; 
		
	/*
	
object(stdClass)#1248 (9) refcount(4){
  ["servicePointId"]=>
  string(6) "546431" refcount(4)
  ["name"]=>
  string(29) "PostNord Arsta Byängsgränd" refcount(3)
  ["visitingAddress"]=>
  array(5) refcount(3){
    ["streetName"]=>
    string(29) "Terminal Arsta Byängsgränd" refcount(1)
    ["streetNumber"]=>
    string(1) "1" refcount(1)
    ["postalCode"]=>
    string(5) "12040" refcount(1)
    ["city"]=>
    string(6) "ARSTA" refcount(1)
    ["countryCode"]=>
    string(2) "SE" refcount(1)
  }
  ["deliveryAddress"]=>
  array(5) refcount(3){
    ["streetName"]=>
    string(29) "Terminal Arsta Byängsgränd" refcount(1)
    ["streetNumber"]=>
    string(1) "1" refcount(1)
    ["postalCode"]=>
    string(5) "12040" refcount(1)
    ["city"]=>
    string(6) "ARSTA" refcount(1)
    ["countryCode"]=>
    string(2) "SE" refcount(1)
  }
  ["openingHours"]=>
  array(5) refcount(3){
    [0]=>
    array(3) refcount(1){
      ["from1"]=>
      string(4) "0830" refcount(1)
      ["to1"]=>
      string(4) "1800" refcount(1)
      ["day"]=>
      string(2) "MO" refcount(1)
    }
    [1]=>
    array(3) refcount(1){
      ["from1"]=>
      string(4) "0830" refcount(1)
      ["to1"]=>
      string(4) "1800" refcount(1)
      ["day"]=>
      string(2) "TU" refcount(1)
    }
    [2]=>
    array(3) refcount(1){
      ["from1"]=>
      string(4) "0830" refcount(1)
      ["to1"]=>
      string(4) "1800" refcount(1)
      ["day"]=>
      string(2) "WE" refcount(1)
    }
    [3]=>
    array(3) refcount(1){
      ["from1"]=>
      string(4) "0830" refcount(1)
      ["to1"]=>
      string(4) "1800" refcount(1)
      ["day"]=>
      string(2) "TH" refcount(1)
    }
    [4]=>
    array(3) refcount(1){
      ["from1"]=>
      string(4) "0830" refcount(1)
      ["to1"]=>
      string(4) "1800" refcount(1)
      ["day"]=>
      string(2) "FR" refcount(1)
    }
  }
  ["customerSupports"]=>
  array(1) refcount(3){
    [0]=>
    array(2) refcount(2){
      ["customerSupportPhoneNo"]=>
      string(12) "+46771333310" refcount(2)
      ["country"]=>
      string(2) "SE" refcount(1)
    }
  }
  ["servicePoints"]=>
  array(1) refcount(3){
    [0]=>
    array(8) refcount(1){
      ["servicePointId"]=>
      string(6) "546431" refcount(1)
      ["name"]=>
      string(29) "PostNord Arsta Byängsgränd" refcount(1)
      ["eligibleParcelOutlet"]=>
      bool(false) refcount(1)
      ["visitingAddress"]=>
      array(5) refcount(1){
        ["streetName"]=>
        string(29) "Terminal Arsta Byängsgränd" refcount(1)
        ["streetNumber"]=>
        string(1) "1" refcount(1)
        ["postalCode"]=>
        string(5) "12040" refcount(1)
        ["city"]=>
        string(6) "ARSTA" refcount(1)
        ["countryCode"]=>
        string(2) "SE" refcount(1)
      }
      ["deliveryAddress"]=>
      array(5) refcount(1){
        ["streetName"]=>
        string(29) "Terminal Arsta Byängsgränd" refcount(1)
        ["streetNumber"]=>
        string(1) "1" refcount(1)
        ["postalCode"]=>
        string(5) "12040" refcount(1)
        ["city"]=>
        string(6) "ARSTA" refcount(1)
        ["countryCode"]=>
        string(2) "SE" refcount(1)
      }
      ["notificationArea"]=>
      array(1) refcount(1){
        ["postalCodes"]=>
        array(172) refcount(1){
          [0]=>
          string(5) "10997" refcount(1)
          [1]=>
          string(5) "10998" refcount(1)
          [2]=>
          string(5) "10999" refcount(1)
          [3]=>
          string(5) "11099" refcount(1)
          [4]=>
          string(5) "10990" refcount(1)
          [5]=>
          string(5) "10991" refcount(1)
          [6]=>
          string(5) "10992" refcount(1)
          [7]=>
          string(5) "10993" refcount(1)
          [8]=>
          string(5) "10918" refcount(1)
          [9]=>
          string(5) "10994" refcount(1)
          [10]=>
          string(5) "10917" refcount(1)
          [11]=>
          string(5) "12010" refcount(1)
          [12]=>
          string(5) "10995" refcount(1)
          [13]=>
          string(5) "10996" refcount(1)
          [14]=>
          string(5) "10919" refcount(1)
          [15]=>
          string(5) "10540" refcount(1)
          [16]=>
          string(5) "12013" refcount(1)
          [17]=>
          string(5) "10924" refcount(1)
          [18]=>
          string(5) "12012" refcount(1)
          [19]=>
          string(5) "11090" refcount(1)
          [20]=>
          string(5) "10925" refcount(1)
          [21]=>
          string(5) "12015" refcount(1)
          [22]=>
          string(5) "10926" refcount(1)
          [23]=>
          string(5) "12014" refcount(1)
          [24]=>
          string(5) "10927" refcount(1)
          [25]=>
          string(5) "12090" refcount(1)
          [26]=>
          string(5) "10920" refcount(1)
          [27]=>
          string(5) "12016" refcount(1)
          [28]=>
          string(5) "10921" refcount(1)
          [29]=>
          string(5) "10922" refcount(1)
          [30]=>
          string(5) "11013" refcount(1)
          [31]=>
          string(5) "10923" refcount(1)
          [32]=>
          string(5) "10611" refcount(1)
          [33]=>
          string(5) "10610" refcount(1)
          [34]=>
          string(5) "11091" refcount(1)
          [35]=>
          string(5) "12021" refcount(1)
          [36]=>
          string(5) "10909" refcount(1)
          [37]=>
          string(5) "12022" refcount(1)
          [38]=>
          string(5) "10908" refcount(1)
          [39]=>
          string(5) "10907" refcount(1)
          [40]=>
          string(5) "10906" refcount(1)
          [41]=>
          string(5) "12026" refcount(1)
          [42]=>
          string(5) "10915" refcount(1)
          [43]=>
          string(5) "12025" refcount(1)
          [44]=>
          string(5) "10916" refcount(1)
          [45]=>
          string(5) "12024" refcount(1)
          [46]=>
          string(5) "10913" refcount(1)
          [47]=>
          string(5) "12023" refcount(1)
          [48]=>
          string(5) "10914" refcount(1)
          [49]=>
          string(5) "10911" refcount(1)
          [50]=>
          string(5) "10912" refcount(1)
          [51]=>
          string(5) "11000" refcount(1)
          [52]=>
          string(5) "10910" refcount(1)
          [53]=>
          string(5) "11084" refcount(1)
          [54]=>
          string(5) "11085" refcount(1)
          [55]=>
          string(5) "11086" refcount(1)
          [56]=>
          string(5) "11081" refcount(1)
          [57]=>
          string(5) "11082" refcount(1)
          [58]=>
          string(5) "11083" refcount(1)
          [59]=>
          string(5) "10524" refcount(1)
          [60]=>
          string(5) "10939" refcount(1)
          [61]=>
          string(5) "10942" refcount(1)
          [62]=>
          string(5) "10943" refcount(1)
          [63]=>
          string(5) "10944" refcount(1)
          [64]=>
          string(5) "10945" refcount(1)
          [65]=>
          string(5) "10946" refcount(1)
          [66]=>
          string(5) "11037" refcount(1)
          [67]=>
          string(5) "10947" refcount(1)
          [68]=>
          string(5) "10948" refcount(1)
          [69]=>
          string(5) "10949" refcount(1)
          [70]=>
          string(5) "10940" refcount(1)
          [71]=>
          string(5) "10941" refcount(1)
          [72]=>
          string(5) "10519" refcount(1)
          [73]=>
          string(5) "10682" refcount(1)
          [74]=>
          string(5) "12044" refcount(1)
          [75]=>
          string(5) "10929" refcount(1)
          [76]=>
          string(5) "10928" refcount(1)
          [77]=>
          string(5) "12040" refcount(1)
          [78]=>
          string(5) "10510" refcount(1)
          [79]=>
          string(5) "10688" refcount(1)
          [80]=>
          string(5) "11025" refcount(1)
          [81]=>
          string(5) "10933" refcount(1)
          [82]=>
          string(5) "10934" refcount(1)
          [83]=>
          string(5) "10931" refcount(1)
          [84]=>
          string(5) "10932" refcount(1)
          [85]=>
          string(5) "10937" refcount(1)
          [86]=>
          string(5) "10938" refcount(1)
          [87]=>
          string(5) "10935" refcount(1)
          [88]=>
          string(5) "10680" refcount(1)
          [89]=>
          string(5) "10936" refcount(1)
          [90]=>
          string(5) "10930" refcount(1)
          [91]=>
          string(5) "11052" refcount(1)
          [92]=>
          string(5) "11051" refcount(1)
          [93]=>
          string(5) "11054" refcount(1)
          [94]=>
          string(5) "10500" refcount(1)
          [95]=>
          string(5) "11053" refcount(1)
          [96]=>
          string(5) "10950" refcount(1)
          [97]=>
          string(5) "10952" refcount(1)
          [98]=>
          string(5) "10951" refcount(1)
          [99]=>
          string(5) "10506" refcount(1)
          [100]=>
          string(5) "10954" refcount(1)
          [101]=>
          string(5) "10507" refcount(1)
          [102]=>
          string(5) "10953" refcount(1)
          [103]=>
          string(5) "10956" refcount(1)
          [104]=>
          string(5) "10955" refcount(1)
          [105]=>
          string(5) "10958" refcount(1)
          [106]=>
          string(5) "10957" refcount(1)
          [107]=>
          string(5) "10959" refcount(1)
          [108]=>
          string(5) "10654" refcount(1)
          [109]=>
          string(5) "10585" refcount(1)
          [110]=>
          string(5) "10650" refcount(1)
          [111]=>
          string(5) "12200" refcount(1)
          [112]=>
          string(5) "11059" refcount(1)
          [113]=>
          string(5) "11055" refcount(1)
          [114]=>
          string(5) "10656" refcount(1)
          [115]=>
          string(5) "11056" refcount(1)
          [116]=>
          string(5) "10657" refcount(1)
          [117]=>
          string(5) "11057" refcount(1)
          [118]=>
          string(5) "11058" refcount(1)
          [119]=>
          string(5) "10963" refcount(1)
          [120]=>
          string(5) "10962" refcount(1)
          [121]=>
          string(5) "10961" refcount(1)
          [122]=>
          string(5) "10960" refcount(1)
          [123]=>
          string(5) "10967" refcount(1)
          [124]=>
          string(5) "10966" refcount(1)
          [125]=>
          string(5) "10965" refcount(1)
          [126]=>
          string(5) "10964" refcount(1)
          [127]=>
          string(5) "10969" refcount(1)
          [128]=>
          string(5) "10968" refcount(1)
          [129]=>
          string(5) "10665" refcount(1)
          [130]=>
          string(5) "11070" refcount(1)
          [131]=>
          string(5) "10972" refcount(1)
          [132]=>
          string(5) "10971" refcount(1)
          [133]=>
          string(5) "10974" refcount(1)
          [134]=>
          string(5) "10973" refcount(1)
          [135]=>
          string(5) "11076" refcount(1)
          [136]=>
          string(5) "10970" refcount(1)
          [137]=>
          string(5) "11075" refcount(1)
          [138]=>
          string(5) "10979" refcount(1)
          [139]=>
          string(5) "10976" refcount(1)
          [140]=>
          string(5) "10975" refcount(1)
          [141]=>
          string(5) "10978" refcount(1)
          [142]=>
          string(5) "10977" refcount(1)
          [143]=>
          string(5) "11078" refcount(1)
          [144]=>
          string(5) "11079" refcount(1)
          [145]=>
          string(5) "10901" refcount(1)
          [146]=>
          string(5) "10569" refcount(1)
          [147]=>
          string(5) "10900" refcount(1)
          [148]=>
          string(5) "10903" refcount(1)
          [149]=>
          string(5) "10902" refcount(1)
          [150]=>
          string(5) "10905" refcount(1)
          [151]=>
          string(5) "10904" refcount(1)
          [152]=>
          string(5) "12000" refcount(1)
          [153]=>
          string(5) "10985" refcount(1)
          [154]=>
          string(5) "11060" refcount(1)
          [155]=>
          string(5) "10984" refcount(1)
          [156]=>
          string(5) "10983" refcount(1)
          [157]=>
          string(5) "10982" refcount(1)
          [158]=>
          string(5) "10981" refcount(1)
          [159]=>
          string(5) "10980" refcount(1)
          [160]=>
          string(5) "12288" refcount(1)
          [161]=>
          string(5) "10989" refcount(1)
          [162]=>
          string(5) "10988" refcount(1)
          [163]=>
          string(5) "10987" refcount(1)
          [164]=>
          string(5) "10986" refcount(1)
          [165]=>
          string(5) "12088" refcount(1)
          [166]=>
          string(5) "12082" refcount(1)
          [167]=>
          string(5) "12083" refcount(1)
          [168]=>
          string(5) "10642" refcount(1)
          [169]=>
          string(5) "12005" refcount(1)
          [170]=>
          string(5) "12004" refcount(1)
          [171]=>
          string(5) "12001" refcount(1)
        }
      }
      ["coordinates"]=>
      array(1) refcount(1){
        [0]=>
        array(3) refcount(1){
          ["srId"]=>
          string(9) "EPSG:4326" refcount(1)
          ["northing"]=>
          double(59.2871817) refcount(1)
          ["easting"]=>
          double(18.0541582) refcount(1)
        }
      }
      ["openingHours"]=>
      array(5) refcount(1){
        [0]=>
        array(3) refcount(1){
          ["from1"]=>
          string(5) "08:30" refcount(1)
          ["to1"]=>
          string(5) "18:00" refcount(1)
          ["day"]=>
          string(6) "MONDAY" refcount(1)
        }
        [1]=>
        array(3) refcount(1){
          ["from1"]=>
          string(5) "08:30" refcount(1)
          ["to1"]=>
          string(5) "18:00" refcount(1)
          ["day"]=>
          string(7) "TUESDAY" refcount(1)
        }
        [2]=>
        array(3) refcount(1){
          ["from1"]=>
          string(5) "08:30" refcount(1)
          ["to1"]=>
          string(5) "18:00" refcount(1)
          ["day"]=>
          string(9) "WEDNESDAY" refcount(1)
        }
        [3]=>
        array(3) refcount(1){
          ["from1"]=>
          string(5) "08:30" refcount(1)
          ["to1"]=>
          string(5) "18:00" refcount(1)
          ["day"]=>
          string(8) "THURSDAY" refcount(1)
        }
        [4]=>
        array(3) refcount(1){
          ["from1"]=>
          string(5) "08:30" refcount(1)
          ["to1"]=>
          string(5) "18:00" refcount(1)
          ["day"]=>
          string(6) "FRIDAY" refcount(1)
        }
      }
    }
  }
  ["active"]=>
  long(1) refcount(1)
  ["phone"]=>
  string(12) "+46771333310" refcount(2)
}

  */
	
		
	 }

	 
   public static $pobocky_cache; 
   public static function getDataPobocky(&$params, $id, $cache, $country, $lang, $zip)
   {
      if ((!empty(self::$pobocky_cache)) && (!empty(self::$pobocky_cache->pobocky)))
	  {
		  foreach (self::$pobocky_cache->pobocky as $p)
		  {
			   $id = (int)$id; 
			  $p->id = (int)$p->id; 
			  if ($p->id === $id)
			  {
				  return $p; 
			  }
		  }
	  }
	  
	  $data = self::getPobockaDetails($params, $id, $cache, $country, $lang); 
	  if (empty($data))
	  {
		  $z2 = self::getPobocky($params, $cache, $country, $lang, $zip); 
		  foreach ($z2->pobocky as $p)
		  {
			  
			  $id = (int)$id; 
			  $p->id = (int)$p->id; 
			  if ($p->id === $id)
			  {
				  return $p; 
			  }
		  }
	  }
	  
	  if ((!empty($data)) && (!empty($data['servicePointInformationResponse'])))
			 {
			  $pobocka = $data['servicePointInformationResponse']; 
			  $pobocka = (object)$pobocka; 
			  $a = array(); 
			  self::br2p($a, $pobocka); 
			 }
	  
	  return $pobocka; 
	  
	  
	  
	  
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

   
   
}